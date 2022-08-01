<?php

namespace common\services;

use common\exceptions\SearchResult\ProcessAlreadyRunningException;
use common\helpers\LogHelper;
use common\helpers\MailHelper;
use common\helpers\UserHelper;
use common\jobs\SearchResult\UpdateJob;
use common\models\filters\SearchResultFilter;
use common\models\GuestLinkMarkerDomain;
use common\models\Project;
use common\models\ProjectNotification;
use common\models\query\SearchResultQuery;
use common\models\SearchResult;
use common\models\SearchResultItem;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\queue\redis\Queue;

/**
 * Сервис поисковой выдачи.
 */
class SearchResultsService
{
    /**
     * @var \common\services\ISeoService
     */
    private $seoService;
    /**
     * @var Queue
     */
    private $queue;

    public $cron = false; // флаг для определения откуда пришел запрос

    public $projectNotificationId = null; // ID нотификации проекта


    /**
     * Конструктор.
     *
     * @param \common\services\ISeoService $seoService
     * @param Queue                        $queue
     */
    public function __construct(ISeoService $seoService, Queue $queue)
    {
        $this->seoService = $seoService;
        $this->queue      = $queue;
    }


    /**
     * Создание новой поисковой выдачи.
     *
     * @param \common\models\Project $project проект
     *
     * @return SearchResult
     * @throws \Exception
     */
    public function create(Project $project): SearchResult
    {
        if ($project->status !== Project::STATUS_ACTIVE) {
            throw new \RuntimeException(
                'Запрошено создание поисковой выдачи для неактивного проекта (' . $project->id . ').'
            );
        }

        if ($project->hasProcessedSearchResults()) {
            throw new ProcessAlreadyRunningException(
                'Обновление поисковой выдачи уже запущена для этого проекта.'
            );
        }

        $currentDate = strtotime('today midnight');

        $searchResult = SearchResult::find()
            ->withDate($currentDate)
            ->withProject($project)
            ->one();

        if ($searchResult === null) {
            $searchResult = new SearchResult([
                'project_id' => $project->id,
                'date'       => $currentDate,
            ]);
        }

        $searchResult->state               = SearchResult::STATE_NEW;
        $searchResult->building_percentage = 0;

        if ($searchResult->save()) {
            $this->queue->push(
                new UpdateJob(['resultId' => $searchResult->id, 'cron' => $this->cron, 'projectNotificationId' => $this->projectNotificationId])
            );
        }

        return $searchResult;
    }


    /**
     * Актуализация состояния процесса выполнения формирования данных поисковой выдачи.
     *
     * @param SearchResult $searchResult
     *
     * @throws \Exception
     */
    public function actualizeState(SearchResult $searchResult): void
    {
        if ((int)$searchResult->state !== SearchResult::STATE_PROCESSING) {
            return;
        }

        $results = $this->seoService->getPositionsBuildingState($searchResult->project);
        $this->setBuildingPercentage($searchResult, $results['ready_percentage']);

        if (!$results['in_process']) {
            $this->setState($searchResult, SearchResult::STATE_LOADING_DATA);
        }
    }


    /**
     * Установка состояния поисковой выдачи.
     *
     * @param SearchResult $result
     * @param int          $state состояние, констаны класса SearchResult::STATE_*
     *
     * @return bool
     * @throws \RuntimeException
     */
    private function setState(SearchResult $result, int $state): bool
    {
        $availableStates = SearchResult::getStates();
        if (!\in_array($state, $availableStates, true)) {
            throw new \RuntimeException(
                'Попытка установки для поисковой выдачи ' . $result->id . ' несуществующего состояния.'
            );
        }

        $result->state = $state;
        return $result->save();
    }


    /**
     * Установка процента готовности формирования поисковый выгрузки.
     *
     * @param SearchResult $result
     * @param int          $percentage процент готовности (значение: 0 - 100)
     *
     * @return bool
     */
    private function setBuildingPercentage(SearchResult $result, int $percentage): bool
    {
        if ((int)$result->state !== SearchResult::STATE_PROCESSING) {
            throw new \RuntimeException(
                'Отклонена попытка установки процента готовности поисковой выдачи ' . $result->id
                . ', состояние выдачи отличается от "В обработке".'
            );
        }

        if ($percentage < 0 || $percentage > 100) {
            throw new \RuntimeException(
                'Отклонена попытка установки процента готовности поисковой выдачи ' . $result->id
                . ', значение процента должен быть в пределах от 0 до 100.'
            );
        }

        $result->building_percentage = $percentage;

        return $result->save();
    }


    /**
     * Возвращает набор имеющихся дат поисковых выгрузок по запрошенному проекту.
     *
     * @param \common\models\Project $project
     *
     * @return array
     */
    public function getAvailableDates(Project $project): array
    {
        $query = SearchResult::find()
            ->select(['date'])
            ->withProject($project)
            ->withState(SearchResult::STATE_DONE)
            ->orderBy(['date' => SORT_ASC])
            ->asArray();

        return ArrayHelper::getColumn($query->all(), 'date');
    }


    /**
     * Возвращает набор регионов проекта.
     *
     * @param \common\models\Project $project
     *
     * @return \common\models\ProjectRegion[]
     */
    public function getProjectsRegions(Project $project): array
    {
        return $project->projectRegions;
    }


    /**
     * Поиск результатов выдачи с фильтрацией.
     *
     * @param \common\models\filters\SearchResultFilter $filter
     * @param int|null                                  $guestLinkId
     *
     * @return array [int => [
     *          'request' => ProjectRequest,
     *          'items' => ['item' => SearchResultItem, 'domainMarker' => ?ProjectMarkerDomain]
     *        ]]
     */
    public function findSearchResults(SearchResultFilter $filter, ?int $guestLinkId = null): array
    {
        $results        = [];
        $projectMarkers = [];

        foreach ($filter->getProject()->projectRequests as $request) {
            $results[$request->id] = [
                'request' => $request,
                'items'   => []
            ];
        }

        // страница /snapshots
        if ($guestLinkId === null) {
            foreach ($filter->getProject()->projectMarkerDomains as $markerDomain) {
                // поскольку в таблице `project_marker_domains` в поле url в значениях попадаются символы `&amp;` (в отличие от значений в таблице `search_result_items`, где есть символы `&`), то декодируем его
                // почему такая разница - нз, видимо прошлый разрабочик завтыкал
                $domainHash                  = md5(Html::decode($markerDomain->url));
                $projectMarkers[$domainHash] = $markerDomain;
            }
            // страница отчета
        } else {
            $guestLinkMarkers = GuestLinkMarkerDomain::find()->andWhere(['guest_link_id' => $guestLinkId])->all();
            foreach ($guestLinkMarkers as $markerDomain) {
                // для новых берем URL, для старых берем domain
                if (isset($markerDomain->url) && !empty($markerDomain->url)) {
                    // поскольку в таблице `project_marker_domains` в поле url в значениях попадаются символы `&amp;` (в отличие от значений в таблице `search_result_items`, где есть символы `&`), то декодируем его
                    // почему такая разница - нз, видимо прошлый разрабочик завтыкал
                    $domainHash = Html::decode($markerDomain->url);
                } else {
                    $domainHash = md5($markerDomain->domain); // поле domain в таблице везде Null (недоделали видимо, нз)
                }

                $projectMarkers[$domainHash] = $markerDomain;
            }
        }

        $period = $filter->getPeriod();
        /** @var SearchResult $lastSearchResult */
        $lastSearchResult = SearchResult::find()
            ->betweenDates($period['from'], $period['to'])
            ->withProject($filter->getProject())
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(1)
            ->one();

        if ($lastSearchResult) {
            $query = SearchResultItem::find()
                ->withRegion($filter->getRegion())
                ->withSearchEngine($filter->getSearchEngine())
                ->withSearchResult($lastSearchResult)
                ->positionOrder();

            foreach ($query->all() as $item) {
                $itemDomainMarker = null;

                // страница /snapshots
                if ($guestLinkId === null) {
                    $urlHash = md5($item->url);
                    if (isset($projectMarkers[$urlHash])) {
                        $itemDomainMarker = $projectMarkers[$urlHash];
                    }
                    // страница отчета
                } else {
                    if (isset($projectMarkers[$item->domain_hash])) {
                        $itemDomainMarker = $projectMarkers[$item->domain_hash];
                    }
                    if (isset($projectMarkers[$item->url])) {
                        $itemDomainMarker = $projectMarkers[$item->url];
                    }

                    // для отчетов если есть измененная индивидуальная ссылка
                    if (isset($item->list_changed_urls) && !empty($item->list_changed_urls)) {

                        $decodeListChangedUrls = Json::decode($item->list_changed_urls);
                        foreach ($decodeListChangedUrls as $key => $listChangedUrl) {
                            if (isset($projectMarkers[$listChangedUrl['newUrl']])) {
                                $itemDomainMarker = $projectMarkers[$listChangedUrl['newUrl']];
                            }
                        }

                        //$hashChangeUrl = md5($item->change_url);
//                        if (isset($projectMarkers[$item->change_url])) {
//                            $itemDomainMarker = $projectMarkers[$item->change_url];
//                        }
                    }
                }

                $results[$item->project_request_id]['items'][] = [
                    'item'         => $item,
                    'domainMarker' => $itemDomainMarker,
                ];
            }
        }

        return $results;
    }


    /**
     * Мониторинг выдачи.
     * Метод актуализирует данные поисковой выгрузки.
     * Обновление происходит в транзакции.
     *
     * @param SearchResult $searchResult
     *
     * @throws \Exception
     */
    public function actualizeData(SearchResult $searchResult, $cron, $projectNotificationId): void
    {
        if (!empty($searchResult)) {

            $transaction = Yii::$app->db->beginTransaction();
            try {
                $list     = $this->seoService->getSearchResults($searchResult);
                $project  = $searchResult->project;
                $regions  = ArrayHelper::map($project->projectRegions, 'code', 'id');
                $requests = [];
                foreach (ArrayHelper::map($project->projectRequests, 'request', 'id') as $requestName => $requestId) {
                    $requests[mb_strtolower(trim($requestName))] = $requestId;
                }
                $searchEngines = ArrayHelper::map($project->projectSearchEngines, 'code', 'id');


                /*********************************/
                // если пришла задача из крона
                //if ($cron) {
                // Оповещаем пользователя о появлении новой страницы в выдаче
                $this->checkNewDataAndSendNotifications($searchResult, $list, $requests, $regions, $searchEngines, $projectNotificationId);
                //}
                /*******************************/

                // очищаем старые выдачи
                SearchResultItem::deleteAll(['search_result_id' => $searchResult->id]);


                // сохраняем полученные выдачи
                foreach ($list as $i => $item) {
                    $itemKeyWord           = mb_strtolower(trim($item['keyWord']));
                    $projectRegionId       = $regions[$item['regionCode']] ?? null;
                    $projectRequestId      = $requests[$itemKeyWord] ?? null;
                    $projectSearchEngineId = $searchEngines[$item['searcherCode']] ?? null;
                    $preparedDomain        = $this->customParseDomain($item['domain']);

                    if ($projectRegionId === null
                        || $projectRequestId === null
                        || $projectSearchEngineId === null
                    ) {
                        continue;
                    }

                    $searchResultItem = new SearchResultItem([
                        'search_result_id'         => $searchResult->id,
                        'project_region_id'        => $projectRegionId,
                        'project_request_id'       => $projectRequestId,
                        'project_search_engine_id' => (string)$projectSearchEngineId,
                        'url'                      => $item['url'],
                        'short_url'                => $item['domain'],
                        'domain_hash'              => md5($preparedDomain),
                        'position'                 => $item['position'],

                    ]);

                    $searchResultItem->save();
                }

                $this->setState($searchResult, SearchResult::STATE_DONE);

                $project->last_checked_at = time();
                $project->save(false);

                $transaction->commit();
            } catch (\Exception $e) {

                \Yii::error("Ошибка в actualizeData: {$e->getMessage()}");

                $transaction->rollBack();
                $this->setState($searchResult, SearchResult::STATE_ERROR);
                throw $e;
            }
        } else {
            \Yii::error('пустой $searchResult в методе actualizeData');
        }
    }


    /**
     * Сравниваем новые данные со старыми и оповещаем пользователя о появлении новой страницы в выдаче
     *
     * @param $searchResult
     * @param $searchResultList      - новые данные от сервиса
     * @param $requests              - список запросов
     * @param $regions               - список регионов
     * @param $searchEngines         - список поисковых движков
     * @param $projectNotificationId - ID нотификации проекта
     */
    public function checkNewDataAndSendNotifications($searchResult, $searchResultList, $requests, $regions, $searchEngines, $projectNotificationId = null)
    {
        if (!empty($projectNotificationId)) {
            $projectNotification = ProjectNotification::find()->where(['id' => $projectNotificationId])->one();
        } else {
            $projectNotification = ProjectNotification::find()->where(['project_id' => $searchResult->project_id])->one();
        }

        // если делать нотификации по почте или на телегу
        if ((isset($projectNotification->is_notify_email) && $projectNotification->is_notify_email )
            || (isset($projectNotification->is_notify_telegram) && $projectNotification->is_notify_telegram)
        )
        {
            $top    = (int)$projectNotification->top_id; // ТОП какого числа
            $userId = $projectNotification->user_id;

            try {
                // старые данные c преобразованием массива
                $lastSearchResult = $searchResult->getLastSearchResult($searchResult);
                if ($lastSearchResult) {
                    $lastSearchResultItems = SearchResultItem::getLastSearchResultItems($lastSearchResult);
                    if ($lastSearchResultItems) {
                        $reformatLastSearchResultItems = $this->reformatData($lastSearchResultItems, $requests, $regions, $searchEngines, $top);
                    }
                }
            } catch (\Exception $e) {
                \Yii::error("Ошибка получения старых данных из БД.: {$e->getMessage()}");
            }

            // Преобразуем новые данные
            $reformatNewSearchResultItems = $this->reformatData($searchResultList, $requests, $regions, $searchEngines, $top);

            if (!$reformatLastSearchResultItems) {
                \Yii::error("Ошибка. Массив $reformatLastSearchResultItems с преобразованием старых данных пустой");
            }
            if (!$reformatNewSearchResultItems) {
                \Yii::error("Ошибка. Массив $reformatNewSearchResultItems с преобразованием новых данных пустой");
            }

            if (!empty($reformatLastSearchResultItems) && !empty($reformatNewSearchResultItems)) {
                // различия массивов - формирование итоговых отличий для отправки на почту
                $diffArrays = $this->diffArrays($reformatLastSearchResultItems, $reformatNewSearchResultItems);
            }

            if (!empty($diffArrays)) {

                /******************************************/
                /* Отправляем уведомления на Telegram  */
                /******************************************/
                if ($projectNotification->is_notify_telegram) {
                    //$telegramMessage = '<b>Автоматические оповещение по проекту ' . $searchResult->project->name. '.</b>\n\n';
                    $telegramMessage = $this->createTelegramMessage($searchResult, $top, $diffArrays);

                    UserHelper::sendTelegramMessage($telegramMessage, $projectNotification->user_id);
                }
                /**********************/


                // SEND EMAIL
                if (isset($projectNotification->is_notify_email) && $projectNotification->is_notify_email) {

                    MailHelper::sendToUserById($userId,
                        'Автоматические оповещение по проекту ' . $searchResult->project->name,
                        'autoCheckSnapshots',
                        null,
                        [
                            'searchResult' => $searchResult,
                            'data'         => $diffArrays,
                            'top'          => $top
                        ]
                    );
                    /*******************/

                    /******************************************/
                    /* Отправляем уведомления на доп. адреса  */
                    /******************************************/
                    if (!empty($projectNotification->emails)) {
                        $emails = explode(',', $projectNotification->emails);
                        foreach ($emails as $email) {
                            MailHelper::sendToByEmail($email,
                                'Автоматические оповещение по проекту ' . $searchResult->project->name,
                                'autoCheckSnapshots',
                                null,
                                [
                                    'searchResult' => $searchResult,
                                    'data'         => $diffArrays,
                                    'top'          => $top
                                ]
                            );
                        }
                    }
                    /**********************/
                }
            }
        }

        return true;
    }


    /**
     * Принудительный парсинг данных домена, если нет протокола - добавляем свой протокол (для ссылок ввида vk.com/..., поставщик считает весь адрес домен)
     *
     * @param string $url
     *
     * @return string
     */
    public function customParseDomain(string $url): string
    {
        if (strpos($url, '://') === false && $url[0] !== '/') {
            $url = 'http://' . $url;
        }
        return parse_url($url, PHP_URL_HOST);
    }


    /**
     * Получение последнего созданной выдачи.
     *
     * @param Project $project
     *
     * @return SearchResult|ActiveRecord|null
     */
    public function getLastCreatedResult(Project $project)
    {
        return $project->getSearchResults()->orderBy(['created_at' => SORT_DESC])->limit(1)->one();
    }


    /**
     * Преобразование массива в удобочитаемый вид с поделементами
     *
     * @param $$dataList - массив, который нужно преобразовать
     * @param $requests      - список запросов по проекту
     * @param $regions       - список регонов по проекту
     * @param $searchEngines - список поисковых движков по проекту
     * @param $topId         - ТОП-N
     *
     * @return array - новый переобработанный массив
     */
    public function reformatData($dataList, $requests, $regions, $searchEngines, $topId)
    {
        $newData = [];

        try {
            foreach ($requests as $request => $requestId) {
                foreach ($regions as $region => $regionId) {
                    foreach ($searchEngines as $engine => $engineId) {
                        $x = 0;
                        foreach ($dataList as $value) {
                            if (mb_strtolower(trim($value['keyWord'])) == $request) {
                                if ($value['regionCode'] == $region) {
                                    if ($value['searcherCode'] == $engine) {
                                        $x++;
                                        $newData[$requestId]['name']                                                            = $request;
                                        $newData[$requestId]['RegionsList'][$regionId]['code']                                  = $region;
                                        $newData[$requestId]['RegionsList'][$regionId]['name']                                  = $value['regionName'];
                                        $newData[$requestId]['RegionsList'][$regionId]['EnginesList'][$engineId]['code']        = $engine;
                                        $newData[$requestId]['RegionsList'][$regionId]['EnginesList'][$engineId]['name']        = isset($value['searcherName']) ? $value['searcherName'] : '';
                                        $newData[$requestId]['RegionsList'][$regionId]['EnginesList'][$engineId]['UrlList'][$x] = $value['url'];
                                    }
                                }
                            }
                        }
                    }
                }
            }

            // оставляем только первых N елементов
            foreach ($newData as $_keyRequest => $_requests) {
                foreach ($_requests['RegionsList'] as $_keyRegion => $_regions) {
                    foreach ($_regions['EnginesList'] as $_keyEngine => $_engines) {
                        // берем первые N
                        $firstChank = array_chunk($_engines['UrlList'], $topId, true);

                        $newData[$_keyRequest]['RegionsList'][$_keyRegion]['EnginesList'][$_keyEngine]['UrlList'] = $firstChank[0];
                    }
                }
            }
        } catch (\Exception $e) {
            Yii::error("Ошибка преобразования массивов: {$e->getMessage()}");
        }

        return $newData;
    }


    /**
     * Сравниваем 2 массива и формируем новый с различиями
     *
     * @return array
     */
    public function diffArrays($oldData, $newData)
    {
        $diff = [];

        try {
            foreach ($newData as $reguestId => $reguest) {
                foreach ($reguest['RegionsList'] as $regionId => $region) {
                    foreach ($region['EnginesList'] as $engineId => $engine) {
                        foreach ($engine['UrlList'] as $position => $url) {

                            if (!in_array($url, $oldData[$reguestId]['RegionsList'][$regionId]['EnginesList'][$engineId]['UrlList'])) {

                                $engineName = $oldData[$reguestId]['RegionsList'][$regionId]['EnginesList'][$engineId]['name'];

                                $diff[$reguestId]['name']                                                                   = $reguest['name'];
                                $diff[$reguestId]['RegionsList'][$regionId]['code']                                         = $region['code'];
                                $diff[$reguestId]['RegionsList'][$regionId]['name']                                         = $region['name'];
                                $diff[$reguestId]['RegionsList'][$regionId]['EnginesList'][$engineId]['code']               = $engine['code'];
                                $diff[$reguestId]['RegionsList'][$regionId]['EnginesList'][$engineId]['name']               = $engineName;
                                $diff[$reguestId]['RegionsList'][$regionId]['EnginesList'][$engineId]['UrlList'][$position] = $url;
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Yii::error("Ошибка сравнения массивов: {$e->getMessage()}");
        }


        return $diff;
    }


    /**
     *
     * Сформировать сообщение для телеги
     *
     * @param $searchResult
     * @param $top
     * @param $data
     *
     * @return string
     */
    public function createTelegramMessage($searchResult, $top, $data)
    {
        $text = "";

        $text .= "*Автоматические оповещение по проекту " . $searchResult->project->name . "!*" . "\n";
        $text .= "Внимание, появились новые странице в поиске ТОП-" . $top . " по запросам:" . "\n\n";

        if ($data) {
            foreach ($data as $requestId => $request) {
                $text .= "По запросу " . $request['name'] . ":" . "\n";
                foreach ($request['RegionsList'] as $regionId => $region) {
                    $text .= "По региону " . $region['name'] . ":" . "\n";
                    foreach ($region['EnginesList'] as $engineId => $engine) {
                        $text .= "По поисковому движку " . $engine['name'] . ":" . "\n";
                        $text .= "\n";
                        foreach ($engine['UrlList'] as $position => $url) {
                            $text .= $url . " - позиция " . $position . "\n";
                        }
                    }
                }
                $text .= "\n";
            }
        }

        return $text;
    }
}