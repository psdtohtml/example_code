<?php

namespace common\models\query;

use common\models\Project;
use yii\db\ActiveQuery;

/**
 * ActiveQuery для [[\common\models\SearchResult]].
 *
 * @see \common\models\SearchResult
 */
class SearchResultQuery extends ActiveQuery
{
    /**
     * Фильтрация по запрошенной дате.
     *
     * @param int $date
     * @return \common\models\query\SearchResultQuery
     */
    public function withDate(int $date): SearchResultQuery
    {
        return $this->andWhere(['date' => $date]);
    }

    /**
     * Фильтрация по периоду между запрошенными датами.
     *
     * @param int $dateFrom дата начала периода
     * @param int $dateTo дата окончания периода
     * @return \common\models\query\SearchResultQuery
     */
    public function betweenDates(int $dateFrom, int $dateTo): SearchResultQuery
    {
        return $this->andWhere(['>=', 'date', $dateFrom])
            ->andWhere(['<=', 'date', $dateTo]);
    }

    /**
     * Фильтрация данных по запрошенному проекту.
     *
     * @param \common\models\Project $project проект
     * @return \common\models\query\SearchResultQuery
     */
    public function withProject(Project $project): SearchResultQuery
    {
        return $this->andWhere(['project_id' => $project->id]);
    }

    /**
     * Фильтрация данных по запрошенному состоянию.
     *
     * @param int $state состояние (констаны класса SearchResult::STATE_*)
     * @return \common\models\query\SearchResultQuery
     */
    public function withState(int $state): SearchResultQuery
    {
        return $this->andWhere(['state' => $state]);
    }
}
