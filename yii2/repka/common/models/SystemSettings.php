<?php

namespace common\models;

use common\models\query\SystemSettingsQuery;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Модель системных настроек.
 *
 * @property int $id
 * @property string $key Ключ
 * @property string $name Наименование
 * @property string $value Значение
 * @property int $created_at Создана
 * @property int $updated_at Обновлена
 */
class SystemSettings extends ActiveRecord
{
    /**
     * Идентификатор пользователя сервиса TopVisor
     */
    public const KEY_TOPVISOR_USER_ID = 'tv_user_id';

    /**
     * Токен авторизации пользователя сервиса TopVisor
     */
    public const KEY_TOPVISOR_ACCESS_TOKEN = 'tv_access_token';

    /**
     * Текст, который выводится в подвале сайта и на странице гостевых ссылок
     */
    public const KEY_FOOTER_TEXT = 'footer_text';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%system_settings}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['key', 'name'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['key', 'name'], 'string', 'max' => 128],
            [['value'], 'string', 'max' => 512],
            [['key'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Ключ',
            'name' => 'Наименование',
            'value' => 'Значение',
            'created_at' => 'Создана',
            'updated_at' => 'Обновлена',
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
     * @return \common\models\query\SystemSettingsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SystemSettingsQuery(get_called_class());
    }
}
