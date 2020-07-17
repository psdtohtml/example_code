<?php

namespace common\models\query;

use common\models\ProjectRegion;
use common\models\ProjectSearchEngine;
use common\models\SearchResult;
use yii\db\ActiveQuery;

/**
 * ActiveQuery для [[\common\models\SearchResultItem]].
 *
 * @see \common\models\SearchResultItem
 */
class SearchResultItemQuery extends ActiveQuery
{
    /**
     * Сортировка по позиции в выдаче.
     *
     * @return \common\models\query\SearchResultItemQuery
     */
    public function positionOrder(): SearchResultItemQuery
    {
        return $this->orderBy(['position' => SORT_ASC]);
    }

    /**
     * Фильтрация по регионам.
     *
     * @param int[] $regions идентификаторы регионов
     * @return \common\models\query\SearchResultItemQuery
     */
    public function withRegionsIds(array $regions): SearchResultItemQuery
    {
        return $this->andWhere(['project_region_id' => $regions]);
    }

    /**
     * Фильтрация по региону.
     *
     * @param \common\models\ProjectRegion $region регион
     * @return \common\models\query\SearchResultItemQuery
     */
    public function withRegion(ProjectRegion $region): SearchResultItemQuery
    {
        return $this->andWhere(['project_region_id' => $region->id]);
    }

    /**
     * Фильтрация данных по поисковой системе.
     *
     * @param \common\models\ProjectSearchEngine $searchEngine поисковая система
     * @return \common\models\query\SearchResultItemQuery
     */
    public function withSearchEngine(ProjectSearchEngine $searchEngine): SearchResultItemQuery
    {
        return $this->andWhere(['project_search_engine_id' => $searchEngine->id]);
    }

    /**
     * Фильтрация данных по запрошенной выгрузке.
     *
     * @param \common\models\SearchResult $searchResult
     * @return \common\models\query\SearchResultItemQuery
     */
    public function withSearchResult(SearchResult $searchResult): SearchResultItemQuery
    {
        return $this->andWhere(['search_result_id' => $searchResult->id]);
    }
}
