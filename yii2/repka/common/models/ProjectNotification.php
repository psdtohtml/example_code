<?php

namespace common\models;

use common\models\query\ProjectNotificationQuery;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "project_notification".
 *
 * @property int $id
 * @property int $user_id Пользователь
 * @property int $frequency_id Частота оповещений
 * @property int $category_id Категория оповещений
 * @property int $top_id ТОП какого числа
 * @property int $created_at Создан
 * @property int $updated_at Обновлён
 * @property int $project_id Проект
 *
 * @property User $user
 * @property Project $project
 */
class ProjectNotification extends \yii\db\ActiveRecord
{
    public const FREQUENCY_NONE  = 0;
    public const FREQUENCY_DAY   = 1;
    public const FREQUENCY_WEEK  = 2;
    public const FREQUENCY_MONTH = 3;

    public const CATEGORY_NEW = 1;

    public const TOP_5  = 5;
    public const TOP_10 = 10;
    public const TOP_15 = 15;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project_notification';
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'frequency_id', 'category_id', 'top_id', 'created_at', 'updated_at', 'project_id'], 'integer'],
            [['frequency_id', 'category_id', 'top_id', 'project_id'], 'required'],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['project_id' => 'id']],
            [
                ['user_id'],
                'exist',
                'skipOnError'     => true,
                'targetClass'     => User::class,
                'targetAttribute' => ['user_id' => 'id']
            ],
            ['user_id', 'default', 'value' => Yii::$app->user->id],
            ['frequency_id', 'in', 'range' => array_keys(self::getFrequencyList())],
            ['category_id', 'in', 'range' => array_keys(self::getCategoryList())],
            ['top_id', 'in', 'range' => array_keys(self::getTopList())],
        ];
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
    public function attributeLabels()
    {
        return [
            'id'           => 'ID',
            'user_id'      => 'User ID',
            'frequency_id' => 'Частота проверки',
            'category_id'  => 'Тип оповещения',
            'top_id'       => 'Топ',
            'created_at'   => 'Created At',
            'updated_at'   => 'Updated At',
            'project_id'   => 'Project ID',
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
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }


    /**
     * {@inheritdoc}
     * @return ProjectNotificationQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ProjectNotificationQuery(static::class);
    }


    public static function getFrequencyList(): array
    {
        return [
            self::FREQUENCY_NONE  => 'Не оповещать',
            self::FREQUENCY_DAY   => 'Раз в день',
            self::FREQUENCY_WEEK  => 'Раз в неделю (каждый понедельник)',
            self::FREQUENCY_MONTH => 'Раз в месяц (каждое 28 число)',
        ];
    }


    public static function getCategoryList(): array
    {
        return [
            self::CATEGORY_NEW => 'Появление новой страницы в выдаче'
        ];
    }


    public static function getTopList(): array
    {
        return [
            self::TOP_5  => 'ТОП 5',
            self::TOP_10 => 'ТОП 10',
            self::TOP_15 => 'ТОП 15',
        ];
    }


    public function getFrequencyName(): string
    {
        $items = self::getFrequencyList();
        return $items[$this->frequency_id];
    }


    public function getCategoryName(): string
    {
        $items = self::getCategoryList();
        return $items[$this->category_id];
    }


    public function getTopName(): string
    {
        $items = self::getTopList();
        return $items[$this->top_id];
    }
}
