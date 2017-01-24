<?php

namespace common\models\rebate;

use yii\behaviors\TimestampBehavior;
use \yii\db\ActiveRecord;
use common\models\User;

/**
 * This is the model class for table "{{%output}}".
 *
 * @property integer $id
 * @property integer $id_user
 * @property integer $payment_detail
 * @property double $amount
 * @property integer $created_at
 * @property string $status
 *
 * @property \common\models\User $idUser
 */
class Output extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%output}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
                'value' => strtotime(date('Y-m-d')),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_user', 'amount','payment_detail'], 'required'],
            [['id_user', 'status'], 'integer'],
            [['amount'], 'number'],
            [['payment_detail'], 'string'],
            [['id_user'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['id_user' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'fullName' => 'Имя Фамилия',
            'login' => 'Логин',
            'amount' => 'Сумма($)',
            'balance' => 'Баланс',
            'balancePartner' => 'Партнерский баланс',
            'created_at' => 'Дата записи',
            'status' => 'Состояние',
            'payment_detail' => 'Кошелек',
            'id' => 'Заявка',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdUser()
    {
        return $this->hasOne(User::className(), ['id' => 'id_user']);
    }

    /* Геттер для полного имени человека */
    public function getFullName()
    {
        $full_name = '';
        if($this->idUser) {
            $full_name = $this->idUser->getFullName();
        }

        return $full_name;

    }

    /* Геттер для баланса*/
    public function getBalance()
    {
        $balance = 0;
        if($this->idUser) {
            $balance = $this->idUser->balance;
        }

        return $balance;
    }

    /* Геттер для баланса партнера*/
    public function getBalancePartner()
    {
        $balance = 0;
        if($this->idUser) {
            $balance = $this->idUser->balance_partner;
        }

        return $balance;
    }

    /* Геттер для логина*/
    public function getLogin()
    {
        $username = '';
        if($this->idUser) {
            $username = $this->idUser->username;
        }

        return $username;
    }

    /**
     * @return string
     */
    public function getStatusName()
    {
        $status = $this->getStatusArrayNames();

        return isset($status[$this->status]) ? $status[$this->status] : 'zzz';
    }

    /**
     * @return array
     */
    public function getStatusArrayNames()
    {
        $status = ['Ожидание', 'Исполнено'];

        return $status;
    }


}
