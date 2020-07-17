<?php

namespace common\models;

use frontend\components\accessFilters\IProjectAccessible;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Модель запроса.
 *
 * @property int $id
 * @property string $request Запрос
 * @property int $project_id Проект
 *
 * @property Project $project
 */
class ProjectRequest extends ActiveRecord implements IProjectAccessible
{
    public const SCENARIO_ADDITIONAL = 'additional';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%project_requests}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['request'], 'required', 'on' => self::SCENARIO_DEFAULT],
            [['project_id'], 'integer'],
            [['request'], 'string', 'max' => 50],
            ['request', 'filter', 'filter' => 'strtolower'],
            [
                ['project_id'],
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
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_ADDITIONAL] = ['project_id', 'request'];
        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'request' => 'Запрос',
            'project_id' => 'Проект',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getProject()
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
