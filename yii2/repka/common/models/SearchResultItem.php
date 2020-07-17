<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * Данные поисковой выдачи.
 *
 * @property string $id
 * @property string $search_result_id Результат поисковой выдачи
 * @property int $project_request_id [int(11) unsigned] Поисковая фраза
 * @property int $project_region_id Регион
 * @property int $project_search_engine_id Поисковая система
 * @property string $url Адрес страницы
 * @property string $short_url Сокращённый адрес
 * @property string $domain_hash Хэш домена
 * @property int $position Позиция
 *
 * @property SearchResult $searchResult
 * @property ProjectRequest $projectRequest
 * @property ProjectSearchEngine $projectSearchEngine
 */
class SearchResultItem extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%search_result_items}}';
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'search_result_id', 'project_region_id', 'project_search_engine_id',
                    'url', 'short_url', 'domain_hash', 'position'
                ],
                'required'
            ],
            [
                ['search_result_id', 'project_request_id', 'project_region_id', 'position', 'project_search_engine_id'],
                'integer'
            ],
            ['url', 'string', 'max' => 2000],
            ['short_url', 'string', 'max' => 150],
            ['domain_hash', 'string', 'max' => 32],
            [
                'search_result_id',
                'exist',
                'skipOnError'     => true,
                'targetClass'     => SearchResult::class,
                'targetAttribute' => ['search_result_id' => 'id']
            ],
            [
                'project_request_id',
                'exist',
                'skipOnError'     => true,
                'targetClass'     => ProjectRequest::class,
                'targetAttribute' => ['project_request_id' => 'id']
            ],
            [
                'project_search_engine_id',
                'exist',
                'skipOnError'     => true,
                'targetClass'     => ProjectSearchEngine::class,
                'targetAttribute' => ['project_search_engine_id' => 'id']
            ],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'                       => 'ID',
            'search_result_id'         => 'Результат поисковой выдачи',
            'project_request_id'       => 'Поисковая фраза',
            'project_region_id'        => 'Регион',
            'project_search_engine_id' => 'Поисковая система',
            'url'                      => 'Адрес страницы',
            'short_url'                => 'Сокращённый адрес',
            'domain_hash'              => 'Хэш домена',
            'position'                 => 'Позиция',
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSearchResult(): \yii\db\ActiveQuery
    {
        return $this->hasOne(SearchResult::class, ['id' => 'search_result_id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectRequest(): \yii\db\ActiveQuery
    {
        return $this->hasOne(ProjectRequest::class, ['id' => 'project_request_id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectSearchEngine(): \yii\db\ActiveQuery
    {
        return $this->hasOne(ProjectSearchEngine::class, ['id' => 'project_search_engine_id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectRegion(): \yii\db\ActiveQuery
    {
        return $this->hasOne(ProjectRegion::class, ['id' => 'project_region_id']);
    }


    /**
     * {@inheritdoc}
     * @return \common\models\query\SearchResultItemQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\SearchResultItemQuery(get_called_class());
    }


    /**
     * Получение старых данных
     *
     * @param $$lastSearchResult
     */
    public static function getLastSearchResultItems($lastSearchResult)
    {
        try {
            // старые данные
            $oldDataSearchResultItem = SearchResultItem::find()
                ->alias('sri')
                ->joinWith(['projectRequest AS prt', 'projectSearchEngine AS pse', 'projectRegion AS prn'])
                ->where(['sri.search_result_id' => $lastSearchResult->id])
                ->asArray()
                ->all();

            if ($oldDataSearchResultItem) {
                $oldDataSearchResultItemNew = [];
                $x                          = 0;
                foreach ($oldDataSearchResultItem as $item) {
                    $oldDataSearchResultItemNew[$x]['keyWord']      = mb_strtolower(trim($item['projectRequest']['request']));
                    $oldDataSearchResultItemNew[$x]['searcherCode'] = $item['projectSearchEngine']['code'];
                    $oldDataSearchResultItemNew[$x]['searcherName'] = $item['projectSearchEngine']['name'];
                    $oldDataSearchResultItemNew[$x]['regionName']   = $item['projectRegion']['region'];
                    $oldDataSearchResultItemNew[$x]['regionCode']   = $item['projectRegion']['code'];
                    $oldDataSearchResultItemNew[$x]['position']     = $item['position'];
                    $oldDataSearchResultItemNew[$x]['url']          = $item['url'];
                    $oldDataSearchResultItemNew[$x]['domain']       = $item['short_url'];
                    $x++;
                }

                return $oldDataSearchResultItemNew;
            }
        } catch (\Exception $e) {
            \Yii::error("Ошибка в получении старых данных: {$e->getMessage()}");
        }

        return false;
    }
}