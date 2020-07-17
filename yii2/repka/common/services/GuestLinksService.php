<?php

namespace common\services;

use common\models\filters\SearchResultFilter;
use common\models\GuestLink;
use common\models\GuestLinkMarker;
use common\models\GuestLinkMarkerDomain;
use \RuntimeException;
use Throwable;
use Yii;
use yii\base\Exception;
use yii\db\StaleObjectException;

/**
 * Сервис гостевых ссылок.
 */
class GuestLinksService
{
    /**
     * Создаёт новый объект.
     *
     * @param SearchResultFilter $filter
     * @param bool $asReport
     * @return GuestLink
     * @throws \Exception
     * @throws Throwable
     */
    public function createLink(SearchResultFilter $filter, bool $asReport = false): GuestLink
    {
        $link = $this->findLink($filter);

        if ($link && $link->report_at === date('Y-m-d')) {
            $link->delete();
        }

        $project =  $filter->getProject();
        $link = new GuestLink([
            'project_id' => $project->id,
            'project_search_engine_id' => $filter->getSearchEngine()->id,
            'project_region_id' => $filter->getRegion()->id,
            'period_from' => $filter->getPeriod()['from'],
            'period_to' => $filter->getPeriod()['to'],
            'token' => $this->getNewToken(),
        ]);

        try {
            if (!$link->save()) {
                throw new RuntimeException('Невозможно сохранить пользователюскую ссылку.');
            }
            foreach ($project->projectMarkers as $item) {
                $marker = new GuestLinkMarker();
                $marker->guest_link_id = $link->id;
                $marker->name = $item->name;
                $marker->color = $item->color;
                $marker->save();
                foreach ($item->domains as $domain) {
                    $markerDomain = new GuestLinkMarkerDomain();
                    $markerDomain->guest_link_id = $link->id;
                    $markerDomain->domain = $domain->domain;
                    $markerDomain->guest_link_marker_id = $marker->id;
                    $markerDomain->save();
                }
            }
            if (!$asReport) {
                GuestLink::deleteAll(['and', ['project_id' => $link->project_id], ['!=', 'id', $link->id], ['report_at' => $link->report_at]]);
                GuestLink::updateAll(['expired' => GuestLink::EXPIRED_YES],
                    ['and', ['project_id' => $link->project_id], ['!=', 'id', $link->id]]);
            }

        } catch (\Exception $e) {
            Yii::error([$e->getMessage(), $link->getErrors()]);
            throw $e;
        }

        return $link;
    }

    /**
     * Возвращает объект по условиям построения.
     *
     * @param SearchResultFilter $filter
     * @return GuestLink|null
     */
    public function findLink(SearchResultFilter $filter): ?GuestLink
    {
        return GuestLink::find()
            ->andWhere(['project_id' => $filter->getProject()->id])
            ->andWhere(['project_search_engine_id' => $filter->getSearchEngine()->id])
            ->andWhere(['project_region_id' => $filter->getProject()->id])
            ->andWhere(['period_from' => $filter->getPeriod()['from']])
            ->andWhere(['period_to' => $filter->getPeriod()['to']])
            ->orderBy(['id' => SORT_DESC])
            ->one();
    }

    /**
     * Возвращает объект по запрошенному токену.
     *
     * @param string $token токен
     * @return GuestLink|null
     */
    public function getLink(string $token): ?GuestLink
    {
        $token = trim($token);

        return GuestLink::find()->withToken($token)->one();
    }

    /**
     * Возвращает объект по запрошенному projectId и id самого объекта.
     * @param int $projectId
     * @param int $id
     * @return GuestLink|null
     */
    public function getLinkByProjectIdById(int $projectId, int $id): ?GuestLink
    {
        return GuestLink::find()->byProjectId($projectId)->byId($id)->one();
    }

    /**
     * Удаляет ссылку.
     *
     * @param GuestLink $link
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function remove(GuestLink $link)
    {
        $link->delete();
    }

    /**
     * Возвращает поисковый фильтр на основе данных гостевой ссылки.
     *
     * @param GuestLink $link
     * @return SearchResultFilter
     */
    public function linkToSearchFilter(GuestLink $link): SearchResultFilter
    {
        return new SearchResultFilter(
            $link->project,
            ['from' => $link->period_from, 'to' => $link->period_to],
            $link->projectRegion,
            $link->projectSearchEngine
        );
    }

    /**
     * Возвращает новый токен.
     *
     * @return string
     * @throws Exception
     */
    private function getNewToken(): string
    {
        return Yii::$app->security->generateRandomString();
    }
}
