<?php

namespace frontend\models\rebate;

use Yii;
use common\models\User;
use backend\models\Pay;

/**
 * This is the model class for table "user_pay".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $pay_id
 * @property string $value
 *
 * @property User $user
 * @property Pay $pay
 */
class UserPay extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_pay';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'pay_id', 'value'], 'required'],
            [['user_id', 'pay_id'], 'integer'],
            [['value'], 'string', 'max' => 250],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['pay_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pay::className(), 'targetAttribute' => ['pay_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'pay_id' => 'Pay ID',
            'value' => 'Value',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPay()
    {
        return $this->hasOne(Pay::className(), ['id' => 'pay_id']);
    }
}
