<?php

namespace common\models;

use common\services\ISeoService;
use frontend\components\accessFilters\IProjectAccessible;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Модель поисковой системы.
 *
 * @property int $id
 * @property string $name Название
 * @property string $code [varchar(2)] Идентификатор поисковой системы
 * @property int $project_id Проект
 *
 * @property Project $project
 */
class ProjectSearchEngine extends ActiveRecord implements IProjectAccessible
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%project_search_engines}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['project_id', 'name', 'code'], 'required'],
            ['project_id', 'integer'],
            ['name', 'string', 'max' => 255],
            ['code', 'string', 'max' => 2],
            ['code', 'in', 'range' => [ISeoService::SEARCH_ENGINE_GOOGLE, ISeoService::SEARCH_ENGINE_YANDEX]],
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
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project_id' => 'Проект',
            'name' => 'Название',
            'code' => 'Код',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getProject(): ActiveQuery
    {
        return $this->hasOne(Project::class, ['id' => 'project_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function getOwnerProject(): Project
    {
        return $this->project;
    }
}
