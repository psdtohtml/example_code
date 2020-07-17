<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * Модель для работы с таблицей суточной статистики количества пользователей и проектов
 *
 * @property string $date
 * @property int $projects_count
 * @property int $users_count
 * @property int $updated_at
 */
class Statistic extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%statistics}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date'], 'required'],
            [['date'], 'safe'],
            [['projects_count', 'users_count'], 'integer'],
            [['date'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'date' => 'Дата',
            'projects_count' => 'Количество проектов',
            'users_count' => 'Количество пользователей',
            'updated_at' => 'Обновлено',
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => false,
            ],
        ];
    }
}
