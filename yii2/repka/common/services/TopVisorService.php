<?php


namespace common\services;

use common\models\Project;
use common\models\SearchResult;
use common\models\SystemSettings;
use RuntimeException;
use Topvisor\TopvisorSDK\V2\Pen;
use Topvisor\TopvisorSDK\V2\Session;
use yii\helpers\ArrayHelper;

/**
 * Сервис взаимодействия с онлайн-сервисом TopVisor, предоставляющим все данные поисковой выдачи
 * и данные для конфигурирования запросов поисковой выдачи.
 */
class TopVisorService implements ISeoService
{
    /**
     * @var \Topvisor\TopvisorSDK\V2\Session
     */
    private $session;

    /**
     * @var null|\stdClass данные текущего проекта в сервисе
     */
    private $currentProjectData;


    /**
     * Конструктор.
     *
     * @param \common\services\SystemSettingsService $settingsService
     */
    public function __construct(SystemSettingsService $settingsService)
    {
        $authParams = [
            'userId'      => $settingsService->getValue(SystemSettings::KEY_TOPVISOR_USER_ID),
            'accessToken' => $settingsService->getValue(SystemSettings::KEY_TOPVISOR_ACCESS_TOKEN),
        ];

        $this->session = new Session($authParams);
    }


    /**
     * Возвращает доступный набор поисковых систем.
     *
     * @return array [['code' => string, 'name' => string], ...]
     */
    public function getSearchEngines(): array
    {
        return [
            static::SEARCH_ENGINE_GOOGLE => [
                'id'   => static::SEARCH_ENGINE_GOOGLE,
                'code' => '1',
                'name' => 'Google',
            ],
            static::SEARCH_ENGINE_YANDEX => [
                'id'   => static::SEARCH_ENGINE_YANDEX,
                'code' => '0',
                'name' => 'Yandex',
            ],
        ];
    }


    /**
     * @inheritDoc
     */
    public function convertSearchEngineToExternal(int $engineId): string
    {
        return $this->getSearchEngines()[$engineId]['code'] ?? '';
    }


    /**
     * @inheritDoc
     */
    public function convertSearchEngineFromExternal(string $engineCode): int
    {
        switch ($engineCode) {
            case '1':
                return static::SEARCH_ENGINE_GOOGLE;
            case '0':
                return static::SEARCH_ENGINE_YANDEX;
            default:
                throw new \RuntimeException('Неизвестный код поисковой системы.');
        }
    }


    /**
     * Поиск регионов по вхождению наименования.
     *
     * @param string $term имя или часть имени региона
     * @param int $limit [10] максимальное количество записей в ответе (от 1 до 100, если указанное значение
     * не входит в этот интервал, будет назначено значение 10)
     * @return array [['id', 'countryCode', 'type', 'areaName', 'name', 'name_ru', 'areaName_ru',
     * 'name_en', 'areaName_en']]
     * @throws \Exception
     */
    public function findRegions(string $term, int $limit = 10): array
    {
        $term = trim($term);

        if ('' === $term) {
            return [];
        }

        if ($limit < 1 || $limit > 100) {
            $limit = 10;
        }

        $regionsPen = new Pen($this->session, 'get', 'mod_common', 'regions');
        $regionsPen->setFields(['name']);
        $regionsPen->setData(['term' => $term, 'searcher' => 0]);
        $regionsPen->setLimit($limit);

        $response = $regionsPen->exec();

        $list = $response->getResult();
        if ($list === null) {
            throw new RuntimeException($response->getErrorsString(), 110);
        }

        $list = ArrayHelper::toArray($list, [
            \stdClass::class => [
                'id', 'countryCode', 'type',
                'areaName', 'name',
                'name_ru', 'areaName_ru',
                'name_en', 'areaName_en',
            ]
        ]);

        return $list;
    }


    /**
     * Возвращает данные запрошенного проекта.
     *
     * @param \common\models\Project $project
     * @param bool $noCache [false] не использовать кешированное значение
     * @return \stdClass|null
     * @throws \Exception
     */
    private function getProject(Project $project, bool $noCache = false): ?\stdClass
    {
        if ($this->currentProjectData === null || $noCache) {
            $this->checkProject($project);

            $projectPen = new Pen($this->session, 'get', 'projects_2', 'projects');
            $projectPen->setData([
                'show_searchers_and_regions' => 1,
            ]);
            $projectPen->setFields(['id', 'name', 'status_positions']);
            $projectPen->setFilters([
                ['name' => 'id', 'operator' => 'EQUALS', 'values' => [$project->service_id]],
            ]);

            $response = $projectPen->exec();

            $results = $response->getResult();

            $this->currentProjectData = $results[0];
        }

        return $this->currentProjectData;
    }


    /**
     * Создание нового проекта.
     * Пробует создать проект в сервисе и присваевает запрошенному проекту идентификатор, назначенный сервисом.
     *
     * @param \common\models\Project $project проект
     * @throws \Exception
     */
    public function createProject(Project $project): void
    {
        $projectPen = new Pen($this->session, 'add', 'projects_2', 'projects');

        $projectPen->setData(['url' => 'http://site.ru', 'name' => $project->name]);

        $response = $projectPen->exec();

        $serviceId = $response->getResult();

        if ($serviceId === null) {
            throw new RuntimeException($response->getErrorsString(), 110);
        }

        $project->service_id = $serviceId;
        $project->save(false);

        $this->createProjectKewordsGroup($project);
        $this->setProjectSearchEngines($project);
        $this->setProjectRegions($project);
        $this->setProjectKeyWords($project);
    }


    /**
     * Переименование проекта.
     *
     * @param \common\models\Project $project
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function renameProject(Project $project): void
    {
        $this->checkProject($project);

        $projectData = $this->getProject($project);

        if ($projectData === null) {
            throw new RuntimeException('Запрошенный проект отсутствует в сервисе.', 150);
        }

        if ($projectData->name !== $project->name) {// обновление наименования проекта
            $projectPen = new Pen($this->session, 'edit', 'projects_2', 'projects/name');
            $projectPen->setData([
                'id'   => $project->service_id,
                'name' => $project->name,
            ]);
            $response = $projectPen->exec();

            $result = $response->getResult();

            if (!$result) {
                throw new RuntimeException($response->getErrorsString(), 110);
            }

            $this->renameProjectKewordsGroup($project);
        }
    }


    /**
     * Удаление запрошенного проекта.
     *
     * @param \common\models\Project $project
     * @throws \Exception
     */
    public function removeProject(Project $project): void
    {
        if (!$project->service_id) {
            return;
        }

        $projectPen = new Pen($this->session, 'del', 'projects_2', 'projects');

        $projectPen->setFilters([
            ['name' => 'id', 'operator' => 'EQUALS', 'values' => [$project->service_id]],
        ]);

        $response = $projectPen->exec();

        $result = $response->getResult();
        if (!$result) {
            throw new RuntimeException($response->getErrorsString(), 110);
        }
    }


    /**
     * Сохраняет набор поисковых систем для проекта.
     * Метод производит сравнение набора поисковых систем, полученнх от сервиса с набором
     * локально сохранённых поисковых систем проекта. Лишние удаляются, отсутствующие добавляются.
     * ПС и регионы в проект можно добавить с помощью метода: https://topvisor.com/ru/api/v1/projects/search-engines/
     *
     * @todo Служба поддержки сообщила что метод устаревший и будет откюлчен, но нового аналога пока нет.
     *
     * @param \common\models\Project $project
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function setProjectSearchEngines(Project $project): void
    {
        $this->checkProject($project);

        $projectData = $this->getProject($project);

        $currentSearchEngines = [];
        if ($projectData && !empty($projectData->searchers)) {
            foreach ($projectData->searchers as $item) {
                $currentSearchEngines[] = $item->searcher;
            }
        }

        $needSearchEngines = [];
        foreach ($project->projectSearchEngines as $item) {
            $needSearchEngines[] = $this->convertSearchEngineToExternal((int)$item->code);
        }

        $insertItems = array_diff($needSearchEngines, $currentSearchEngines);
        $removeItems = array_diff($currentSearchEngines, $needSearchEngines);

        if (!$insertItems && !$removeItems) { // нет изменений в наборе
            return;
        }

        if ($insertItems) {
            $pen = new Pen($this->session, 'add', 'mod_projects', 'searcher');

            foreach ($insertItems as $item) {
                $pen->setData([
                    'project_id' => $project->service_id,
                    'searcher'   => $item
                ]);
                $response = $pen->exec();
                $result   = $response->getResult();

                if (!$result) {
                    throw new RuntimeException($response->getErrorsString(), 110);
                }
            }
        }

        if ($removeItems) {
            $pen = new Pen($this->session, 'del', 'mod_projects', 'searcher');

            foreach ($removeItems as $item) {
                $pen->setData([
                    'project_id' => $project->service_id,
                    'searcher'   => $item
                ]);
                $response = $pen->exec();
                $result   = $response->getResult();

                if (!$result) {
                    throw new RuntimeException($response->getErrorsString(), 110);
                }
            }
        }
    }


    /**
     * Сохраняет набор регионов для проекта.
     * Метод производит сравнение набора регионов всех происковых систем проекта, полученнх от сервиса с набором
     * локально сохранённых регионов проекта. Лишние удаляются, отсутствующие добавляются.
     * ПС и регионы в проект можно добавить с помощью метода: https://to    pvisor.com/ru/api/v1/projects/search-engines/
     *
     * @todo Служба поддержки сообщила что метод устаревший и будет откюлчен, но нового аналога пока нет.
     *
     * @param \common\models\Project $project
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function setProjectRegions(Project $project): void
    {
        $this->checkProject($project);

        $projectData = $this->getProject($project, true);

        $needRegions = [];
        foreach ($project->projectRegions as $item) {
            $needRegions[] = $item->code;
        }

        if ($projectData && !empty($projectData->searchers)) {
            foreach ($projectData->searchers as $searcher) {
                $currentRegions  = [];
                $regionsRegister = [];

                if (!empty($searcher->regions)) {
                    foreach ($searcher->regions as $region) {
                        $currentRegions[$region->key]                         = $region->key;
                        $regionsRegister[$region->key][$region->searcher_key] = $region->id;
                    }
                }

                $insertItems = array_diff($needRegions, $currentRegions);
                $removeItems = array_diff($currentRegions, $needRegions);

                if (!$insertItems && !$removeItems) { // нет изменений в наборе
                    continue;
                }

                if ($insertItems) {
                    $pen = new Pen($this->session, 'add', 'mod_projects', 'searcher_region');

                    foreach ($insertItems as $insertItem) {
                        $pen->setData([
                            'searcher_id' => $searcher->id,
                            'region'      => $insertItem,
                        ]);
                        $response = $pen->exec();
                        $result   = $response->getResult();

                        if (!$result) {
                            throw new RuntimeException($response->getErrorsString(), 110);
                        }
                    }
                }

                if ($removeItems) {
                    $pen = new Pen($this->session, 'del', 'mod_projects', 'searcher_region');

                    foreach ($removeItems as $item) {
                        $regionIds = $regionsRegister[$item];

                        foreach ($regionIds as $id) {
                            $pen->setData([
                                'project_id' => $project->service_id,
                                'id'         => $id
                            ]);
                            $response = $pen->exec();
                            $result   = $response->getResult();

                            if (!$result) {
                                throw new RuntimeException($response->getErrorsString(), 110);
                            }
                        }
                    }
                }
            }
        }
    }


    /**
     * Сохраняет набор запросов (ключевых слов) для проекта.
     * Метод производит сравнение набора регионов всех происковых систем проекта, полученнх от сервиса с набором
     * локально сохранённых регионов проекта. Лишние удаляются, отсутствующие добавляются.
     * ПС и регионы в проект можно добавить с помощью метода: https://topvisor.com/ru/api/v1/projects/search-engines/
     *
     * @param \common\models\Project $project
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function setProjectKeyWords(Project $project): void
    {
        $this->checkProject($project);

        // текущие ключевые слова на сервисе TopVisor
        $currentValues = [];
        foreach ($this->getProjectKeyWords($project) as $id => $item) {
            $currentValues[] = $item;
        }

        // берем новый проект потому как ранее были динамически добавлены новые запросы
        $project = Project::findOne($project->id);

        $needKeyWords = [];
        foreach ($project->projectRequests as $item) {
            $needKeyWords[] = $item->request;
        }

        $insertItems = array_diff($needKeyWords, $currentValues);
        $removeItems = array_diff($currentValues, $needKeyWords);

        if (!$insertItems && !$removeItems) { // нет изменений в наборе
            return;
        }

        if ($insertItems) {
            $currentGroup = $this->getProjectKewordsGroup($project);
            if ($currentGroup === null) {
                throw new RuntimeException(
                    "Для проекта {$project->name} ({$project->id}) групп для запросов " .
                    '(ключевых слов) в сервисе не обнаружена.'
                );
            }

            $keywords = ArrayHelper::merge(['name'], $insertItems);

            $pen = new Pen($this->session, 'add', 'keywords_2', 'keywords/import');

            $pen->setData([
                'project_id' => $project->service_id,
                'group_id'   => $currentGroup->id,
                'keywords'   => implode("\n", $keywords),
            ]);
            $response = $pen->exec();
            $result   = $response->getResult();
            if (!$result) {
                throw new RuntimeException($response->getErrorsString(), 110);
            }
        }

        if ($removeItems) {
            $pen = new Pen($this->session, 'del', 'keywords_2', 'keywords');

            $pen->setData(['project_id' => $project->service_id]);
            $pen->setFilters([
                ['name' => 'name', 'operator' => 'IN', 'values' => $removeItems]
            ]);

            $response = $pen->exec();

            if ($response->getErrors()) {
                throw new RuntimeException($response->getErrorsString(), 110);
            }
        }
    }


    /**
     * Создаёт группу для запросов (ключевых слов).
     *
     * @param \common\models\Project $project
     * @throws \RuntimeException
     * @throws \Exception
     */
    private function createProjectKewordsGroup(Project $project)
    {
        $this->checkProject($project);

        $pen = new Pen($this->session, 'add', 'keywords_2', 'groups');

        $pen->setData([
            'project_id' => $project->service_id,
            'name'       => [$this->getProjectKewordsGroupName($project)]
        ]);

        $response = $pen->exec();

        if ($response->getErrors()) {
            throw new RuntimeException($response->getErrorsString(), 110);
        }
    }


    /**
     * Возвращает группу для запросов (ключевых слов) запрошенного проекта.
     *
     * @param \common\models\Project $project
     * @return \stdClass|null
     * @throws \Exception
     */
    private function getProjectKewordsGroup(Project $project): ?\stdClass
    {
        $this->checkProject($project);

        $pen = new Pen($this->session, 'get', 'keywords_2', 'groups');
        $pen->setData(['project_id' => $project->service_id]);
        $response = $pen->exec();

        if ($response->getErrors()) {
            throw new RuntimeException($response->getErrorsString(), 110);
        }

        $result = $response->getResult();

        if (isset($result[0])) {
            return $result[0];
        }

        return null;
    }


    /**
     * Переименование группы для запросов (ключевых слов), вызывается при переименовании проекта.
     *
     * @param \common\models\Project $project
     * @throws \Exception
     */
    private function renameProjectKewordsGroup(Project $project)
    {
        $this->checkProject($project);

        $currentGroup = $this->getProjectKewordsGroup($project);

        if ($currentGroup === null) {
            throw new RuntimeException(
                "Для проекта {$project->name} ({$project->id}) групп для запросов " .
                '(ключевых слов) в сервисе не обнаружена.'
            );
        }

        $newName = $this->getProjectKewordsGroupName($project);
        if ($currentGroup->name === $newName) {
            return;
        }

        $pen = new Pen($this->session, 'edit', 'keywords_2', 'groups/rename');
        $pen->setData([
            'project_id' => $project->service_id,
            'name'       => $newName
        ]);
        $pen->setFilters([
            ['name' => 'id', 'EQUALS', 'value' => [$currentGroup->id]],
        ]);

        $response = $pen->exec();

        if ($response->getErrors()) {
            throw new RuntimeException($response->getErrorsString(), 110);
        }
    }


    /**
     * Возвращает наименование группы для запросов (ключевых слов) проекта.
     *
     * @param \common\models\Project $project
     * @return string
     */
    private function getProjectKewordsGroupName(Project $project): string
    {
        return 'Проект ' . $project->name . ' (' . $project->id . ')';
    }


    /**
     * Возвращает набор ключевых слов по запрошенному проекту.
     *
     * @param \common\models\Project $project
     * @return array
     * @throws \RuntimeException
     * @throws \Exception
     */
    private function getProjectKeyWords(Project $project)
    {
        $this->checkProject($project);

        $pen = new Pen($this->session, 'get', 'keywords_2', 'keywords');

        $pen->setData(['project_id' => $project->service_id]);
        $pen->setFields(['id', 'name']);

        $response = $pen->exec();

        if ($response->getErrors()) {
            throw new RuntimeException($response->getErrorsString(), 110);
        }

        $list   = [];
        $result = $response->getResult();
        if (\is_array($result)) {
            foreach ($result as $item) {
                $list[$item->id] = $item->name;
            }
        }

        return $list;
    }


    /**
     * Проверяет возможность использовать переданный проект для работы с сервисом.
     *
     * @param \common\models\Project $project
     * @throws \RuntimeException
     */
    private function checkProject(Project $project)
    {
        if (!$project->service_id) {
            throw new RuntimeException('У запрошенного проекта нет идентификатора из сервиса.', 100);
        }
    }


    /**
     * Возвращает остатки средств на балансе сервиса.
     *
     * @return float
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function getAccountBalance(): float
    {
        $pen = new Pen($this->session, 'get', 'bank_2', 'info');

        $pen->setFields(['balance_all']);
        $response = $pen->exec();

        if ($response->getErrors()) {
            throw new RuntimeException($response->getErrorsString(), 110);
        }

        $result = $response->getResult();

        return (float)$result->balance_all;
    }


    /**
     * Инициализация формирования данных поисковой выдачи.
     *
     * @param \common\models\SearchResult $searchResult
     * @return void
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function checkPositions(SearchResult $searchResult): void
    {
        $project = $searchResult->project;
        $pen     = new Pen($this->session, 'edit', 'positions_2', 'checker/go');

        // do_snapshots - Глубина проверки снимка. Возможные значения:
        //    0 - не проверять снимки, 2 - снимки выдачи ТОП20, 3 - снимки выдачи ТОП30,
        //    5 - снимки выдачи ТОП50, 9 - снимки выдачи ТОП100
        $pen->setData([
            'do_snapshots' => 2,
        ]);
        $pen->setFilters([
            ['name' => 'id', 'operator' => 'EQUALS', 'values' => [$project->service_id]],
        ]);

        $response = $pen->exec();

        if ($response->getErrors()) {
            throw new RuntimeException($response->getErrorsString(), 110);
        }

        $result = (array)$response->getResult();

        if (empty($result) || !isset($result['projectIds']) || !\in_array($project->service_id, $result['projectIds'], false)) {
            throw new RuntimeException(
                'Сервис не добавил проект ' . $project->id . ' для построения поисковой выдачи.'
            );
        }
    }


    /**
     * Возвращает данные о готовности формирования данных поисковой выдачи.
     *
     * @param \common\models\Project $project проект
     * @return array ['in_process' => bool, 'ready_percentage' => int]
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function getPositionsBuildingState(Project $project): array
    {
        $this->checkProject($project);

        $projectPen = new Pen($this->session, 'get', 'projects_2', 'projects');
        $projectPen->setFields(['id', 'name', 'status_positions']);
        $projectPen->setFilters([
            ['name' => 'id', 'operator' => 'EQUALS', 'values' => [$project->service_id]],
        ]);

        $response = $projectPen->exec();
        if ($response->getErrors()) {
            throw new RuntimeException($response->getErrorsString(), 110);
        }
        $results = $response->getResult();

        $item = $results[0];

        $statusResult = ['in_process' => false, 'ready_percentage' => 100];
        if ((int)$item->status_positions !== 0) {
            $statusResult['in_process']       = true;
            $statusResult['ready_percentage'] = $item->percent_of_parse;
        }

        return $statusResult;
    }


    /**
     * Возвращает данные поисковой выдачи.
     *
     * @param \common\models\SearchResult $searchResult
     * @return array [['keyWord' => string, 'searcherCode' => int, 'regionName' => string, 'regionCode' => int,
     *      'date' => "Y-m-d", 'position' => int, 'url' => string, 'domain' => string], ...]
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function getSearchResults(SearchResult $searchResult): array
    {
        $project     = $searchResult->project;
        $projectData = $this->getProject($project);
        $regions     = [];

        if ($projectData && !empty($projectData->searchers)) {
            foreach ($projectData->searchers as $item) {
                if (!empty($item->regions)) {
                    foreach ($item->regions as $region) {
                        $regions[] = $region;
                    }
                }
            }
        }

        $results = [];
        foreach ($regions as $region) {
            // слияние полученных данных выдачи с добавлением новых данных в конец существующего массива
            $results = array_merge($results, $this->getSnapshotData($searchResult, $region));
        }

        return $results;
    }


    /**
     * Подготавливает данные снапшота.
     *
     * @param \common\models\SearchResult $searchResult
     * @param array $region регион
     * @return array [
     *      'keyWord' => string, 'searcherCode' => int, 'regionName' => string, 'regionCode' => int,
     *      'date' => "Y-m-d", 'position' => int, 'url' => string, 'domain' => string]
     * @throws \RuntimeException
     * @throws \Exception
     */
    private function getSnapshotData(SearchResult $searchResult, $region)
    {
        $pen = new Pen($this->session, 'get', 'snapshots_2', 'history');

        $date = date('Y-m-d', $searchResult->date);

        $pen->setData([
            'project_id'   => $searchResult->project->service_id,
            'region_index' => $region->index,
            'dates'        => [$date],
        ]);

        $pen->setLimit(100);

        $response = $pen->exec();

        if ($response->getErrors()) {
            throw new RuntimeException($response->getErrorsString(), 110);
        }

        $result            = $response->getResult();
        $searchResultsList = [];

        foreach ($result->keywords as $keyword) {
            $keywordName = $keyword->name;
            foreach ($keyword->snapshotsData as $key => $datum) {
                $keyParts            = explode(':', $key);
                $searchResultsList[] = [
                    'keyWord'      => $keywordName,
                    'searcherCode' => $this->convertSearchEngineFromExternal((string)$region->searcher_key),
                    'regionName'   => $region->name,
                    'regionCode'   => $region->key,
                    'date'         => $keyParts[0],
                    'position'     => $keyParts[1],
                    'url'          => $datum->url,
                    'domain'       => $datum->domain,
                ];
            }
        }

        return $searchResultsList;
    }
}
