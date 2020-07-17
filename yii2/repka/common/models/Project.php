<?php

namespace common\models;

use common\models\query\ProjectQuery;
use common\models\query\SearchResultQuery;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Модель проекта.
 *
 * @property int $id
 * @property string $name Название
 * @property int $last_checked_at
 * @property int $user_id Пользователь
 * @property int $status Статус
 * @property int $service_id [int(11) unsigned]  Идентификатор в сервисе
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User $owner
 * @property ProjectRegion[] $projectRegions
 * @property ProjectRequest[] $projectRequests
 * @property ProjectMarker[] $projectMarkers
 * @property ProjectMarkerDomain[] $projectMarkerDomains
 * @property ProjectSearchEngine[] $projectSearchEngines
 * @property SearchResult[] $searchResults
 * @property SharedProject2User[] $sharedProject2Users
 * @property GuestLink[] $guestLinks
 * @property ProjectHosts[] $projectHosts
 */
class Project extends ActiveRecord
{
    public const STATUS_DRAFT = 1;
    public const STATUS_ACTIVE = 2;

    private $hostsProjectList;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%projects}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['name', 'required'],
            [['last_checked_at', 'user_id'], 'integer'],
            [['name'], 'string', 'max' => 15],
            ['user_id', 'default', 'value' => Yii::$app->user->id],
            ['status', 'in', 'range' => array_keys(self::getStatusLabels())],
            ['status', 'default', 'value' => self::STATUS_DRAFT],
            [['created_at', 'updated_at', 'service_id'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'status' => 'Статус',
            'last_checked_at' => 'Последний раз проверяли',
            'user_id' => 'Пользователь',
            'service_id' => 'Идентификатор в сервисе',
            'created_at' => 'Создан',
            'updated_at' => 'Обновлен',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getOwner()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProjectRegions(): ActiveQuery
    {
        return $this->hasMany(ProjectRegion::class, ['project_id' => 'id'])->orderBy(['region' => SORT_ASC]);
    }

    /**
     * @return ActiveQuery
     */
    public function getProjectRequests(): ActiveQuery
    {
        return $this->hasMany(ProjectRequest::class, ['project_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProjectMarkers(): ActiveQuery
    {
        return $this->hasMany(ProjectMarker::class, ['project_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProjectMarkerDomains(): ActiveQuery
    {
        return $this->hasMany(ProjectMarkerDomain::class, ['project_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProjectSearchEngines(): ActiveQuery
    {
        return $this->hasMany(ProjectSearchEngine::class, ['project_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getSharedProject2Users(): ActiveQuery
    {
        return $this->hasMany(SharedProject2User::class, ['project_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getSearchResults(): SearchResultQuery
    {
        return $this->hasMany(SearchResult::class, ['project_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getGuestLinks()
    {
        return $this->hasMany(GuestLink::class, ['project_id' => 'id'])->orderBy(['id' => SORT_DESC]);
    }

    /**
     * @return ActiveQuery
     */
    public function getProjectHosts(): ActiveQuery
    {
        return $this->hasMany(ProjectHosts::class, ['project_id' => 'id']);
    }

    /**
     * Названия статусов.
     *
     * @return array
     */
    public static function getStatusLabels(): array
    {
        return [
            static::STATUS_ACTIVE => 'Активен',
            static::STATUS_DRAFT => 'Черновик',
        ];
    }

    /**
     * @return ProjectQuery
     */
    public static function find()
    {
        return new ProjectQuery(get_called_class());
    }

    /**
     * Наличие обрабатываемой записи актуализации данных поисковой выдачи.
     *
     * @return bool
     */
    public function hasProcessedSearchResults(): bool
    {
        return $this->getSearchResults()->withState(SearchResult::STATE_PROCESSING)->exists();
    }

    public function inHostList($domain): bool
    {
        $hostList = $this->hostsProjectList;
        if ($hostList === null) {
            $this->hostsProjectList = ArrayHelper::getColumn($this->projectHosts, 'domain');
            $hostList = $this->hostsProjectList;
        }
        return in_array($domain, $hostList, true);
    }
}
