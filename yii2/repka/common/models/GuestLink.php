<?php

namespace common\models;

use common\models\query\GuestLinkQuery;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * Модель Гостевые ссылки.
 *
 * @property int $id
 * @property string $token Токен
 * @property int $project_id Проект
 * @property int $project_search_engine_id Поисковая система
 * @property int $project_region_id Регион
 * @property int $period_from Дата начала
 * @property int $period_to Дата окончания
 * @property int $expired Истек ли отчет
 * @property int $report_at Дата создания отчета
 *
 * @property Project $project
 * @property ProjectRegion $projectRegion
 * @property ProjectSearchEngine $projectSearchEngine
 * @property GuestLinkMarker[] $markers
 */
class GuestLink extends ActiveRecord
{

    public const EXPIRED_NO = 0;
    public const EXPIRED_YES = 1;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%guest_links}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                ['token', 'project_id', 'project_search_engine_id', 'project_region_id', 'period_from', 'period_to'],
                'required'
            ],
            [['project_id', 'project_search_engine_id', 'project_region_id', 'period_from', 'period_to', 'expired'], 'integer'],
            ['expired', 'default', 'value' => self::EXPIRED_NO],
            [['report_at'], 'safe'],
            [['token'], 'string', 'max' => 128],
            [['token'], 'unique'],
            [
                ['project_id'],
                'exist',
                'skipOnError'     => true,
                'targetClass'     => Project::class,
                'targetAttribute' => ['project_id' => 'id']
            ],
            [
                ['project_region_id'],
                'exist',
                'skipOnError'     => true,
                'targetClass'     => ProjectRegion::class,
                'targetAttribute' => ['project_region_id' => 'id']
            ],
            [
                ['project_search_engine_id'],
                'exist',
                'skipOnError'     => true,
                'targetClass'     => ProjectSearchEngine::class,
                'targetAttribute' => ['project_search_engine_id' => 'id']
            ],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => 'report_at',
                ],
                'value' => new Expression('CURDATE()')
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'                       => 'ID',
            'token'                    => 'Токен',
            'project_id'               => 'Проект',
            'project_search_engine_id' => 'Поисковая система',
            'project_region_id'        => 'Регион',
            'period_from'              => 'Дата начала',
            'period_to'                => 'Дата окончания',
            'expired'                  => 'Истек ли',
            'report_at'                => 'Дата генерации отчета',
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
     * @return ActiveQuery
     */
    public function getProjectRegion()
    {
        return $this->hasOne(ProjectRegion::class, ['id' => 'project_region_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProjectSearchEngine()
    {
        return $this->hasOne(ProjectSearchEngine::class, ['id' => 'project_search_engine_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getMarkers(): ActiveQuery
    {
        return $this->hasMany(GuestLinkMarker::class, ['guest_link_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return GuestLinkQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new GuestLinkQuery(get_called_class());
    }

    public function getFormattedReportAt() {
        if ($this->report_at === null) {
            return date('d.m.Y');
        }
        return date('d.m.Y', strtotime($this->report_at));
    }
}
