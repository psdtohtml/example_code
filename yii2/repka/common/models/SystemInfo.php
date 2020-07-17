<?php

namespace common\models;

/**
 * Сведения о системе.
 *
 * @property int $id
 * @property string $name Наименование
 * @property string $key Ключ
 * @property string $value Значение
 * @property int $type Тип значения
 */
class SystemInfo extends \yii\db\ActiveRecord
{
    public const KEY_SEO_BALANCE = 'seo_balance';
    public const KEY_SEO_BALANCE_ACTUALIZED_AT = 'seo_balance_actualized_at';

    public const TYPE_FLOAT = 1;
    public const TYPE_DATETIME = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%system_info}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'key'], 'required'],
            [['type'], 'integer'],
            [['name', 'value'], 'string', 'max' => 256],
            [['key'], 'string', 'max' => 128],
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
            'name' => 'Наименование',
            'key' => 'Ключ',
            'value' => 'Значение',
            'type' => 'Тип значения',
        ];
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\SystemInfoQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\SystemInfoQuery(get_called_class());
    }

    /**
     * Возвращает значение по запрошенному ключу.
     * В методе производится типизация значения в зависимости от указанного у него типа.
     *
     * @param string $key ключ
     * @return \DateTime|float|null
     * @throws \Exception
     */
    public static function getValue(string $key)
    {
        $item = static::find()->withKey($key)->one();

        if (!$item) {
            return null;
        }

        $value = null;

        switch ($item->type) {
            case static::TYPE_FLOAT:
                $value = (float) $item->value;
                break;
            case static::TYPE_DATETIME:
                $value = new \DateTime($item->value);
                break;
        }

        return $value;
    }
}
