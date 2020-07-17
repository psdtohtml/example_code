<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Результаты поисковой выдачи.
 *
 * @property int $id
 * @property int $date Дата
 * @property int $state Состояние
 * @property int $building_percentage Готовность, %
 * @property int $project_id Проект
 * @property int $created_at Создана
 * @property int $updated_at Обновлена
 *
 * @property Project $project
 * @property SearchResultItem $searchResultItems
 */
class SearchResult extends ActiveRecord
{
    /**
     * Новый
     */
    public const STATE_NEW = 10;
    /**
     * В обработке
     */
    public const STATE_PROCESSING = 20;
    /**
     * Загрузка данных
     */
    public const STATE_LOADING_DATA = 30;
    /**
     * Готовый
     */
    public const STATE_DONE = 40;
    /**
     * Обработка завершилась ошибкой
     */
    public const STATE_ERROR = 50;

    /**
     * Возвращает набор доступных состояний.
     *
     * @return array
     */
    public static function getStates(): array
    {
        return [
            static::STATE_NEW,
            static::STATE_PROCESSING,
            static::STATE_LOADING_DATA,
            static::STATE_DONE,
            static::STATE_ERROR,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%search_results}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date', 'state', 'project_id'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['date', 'state', 'project_id', 'created_at', 'updated_at'], 'integer'],
            ['building_percentage', 'integer', 'min' => 0, 'max' => 100],
            [
                'project_id',
                'exist',
                'skipOnError' => true,
                'targetClass' => Project::class,
                'targetAttribute' => ['project_id' => 'id']
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date' => 'Дата',
            'state' => 'Состояние',
            'building_percentage' => 'Готовность, %',
            'project_id' => 'Проект',
            'created_at' => 'Создана',
            'updated_at' => 'Обновлена',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject(): ActiveQuery
    {
        return $this->hasOne(Project::class, ['id' => 'project_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSearchResultItems(): ActiveQuery
    {
        return $this->hasMany(SearchResultItem::class, ['search_result_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\SearchResultQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\SearchResultQuery(get_called_class());
    }


    /**
     * Последний успешный запрос
     *
     * @param Project $project
     * @return array|null|ActiveRecord
     */
    public function getLastSearchResult(SearchResult $searchResult)
    {
        return self::find()
            ->where(['state' => self::STATE_DONE, 'project_id' => $searchResult->project_id])
            ->orderBy(['updated_at' => SORT_DESC])
            ->limit(1)
            ->one();
    }
}
