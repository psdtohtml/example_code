<?php

namespace common\models;

use frontend\components\accessFilters\IProjectAccessible;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Модель региона.
 *
 * @property int $id
 * @property string $region Регион
 * @property int $code [int(11) unsigned]  Код поисковой системы
 * @property int $project_id Проект
 *
 * @property Project $project
 */
class ProjectRegion extends ActiveRecord implements IProjectAccessible
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%project_regions}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['region', 'code'], 'required'],
            [['project_id', 'code'], 'integer'],
            [['region'], 'string', 'max' => 255],
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
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'region' => 'Регион',
            'project_id' => 'Проект',
            'code' => 'Код поисковой системы',
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
