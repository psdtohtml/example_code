<?php

namespace backend\models;

use Yii;
use frontend\models\rebate\UserPay;

/**
 * This is the model class for table "pay".
 *
 * @property integer $id
 * @property string $name
 * @property string $tip
 * @property integer $status
 * @property string $code
 *
 * @property UserPay[] $userPays
 */
class Pay extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pay';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['status'], 'integer'],
            [['name', 'tip', 'code'], 'string', 'max' => 250],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Платежная система',
            'tip' => 'Пример',
            'code' => 'Код',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserPays()
    {
        return $this->hasMany(UserPay::className(), ['pay_id' => 'id']);
    }
}
