<?php

namespace common\models\rebate;

use Yii;
use common\models\User;
use backend\models\Pay;
use common\models\Admin;

/**
 * This is the model class for table "history".
 *
 * @property integer $id
 * @property integer $id_admin
 * @property integer $id_user
 * @property string $from_where
 * @property string $pay_system
 * @property string $operation_date
 * @property double $credit
 * @property string $orientation
 * @property string $note
 * @property string $sum
 *
 * @property User $idUser
 * @property Company $idCompany
 */
class History extends \yii\db\ActiveRecord
{
    public $sum;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_user', 'operation_date', 'credit', 'orientation', 'note'], 'required'],
            [['id_admin', 'id_user'], 'integer'],
            [['operation_date'], 'safe'],
            [['credit', 'orientation'], 'number'],
            [['note', 'from_where', 'pay_system'], 'string', 'max' => 255],
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
            'from_where' => 'Компания',
            'operation_date' => 'Дата операции',
            'credit' => 'Сумма($)',
            'orientation' => 'Статус',
            'note' => 'Примечание',
            'adminLogin' => 'Кто внес',

        ];
    }

    /**
     * @return string;
     */
    public function getOrientationName()
    {
        $names = $this->orientationArrayNames();


        return isset($names[$this->orientation]) ? $names[$this->orientation] : 'zzz';
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'id_user']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdmin()
    {
        return $this->hasOne(Admin::className(), ['id' => 'id_admin']);
    }

    /* Геттер для полного имени человека */
    public function getFullName()
    {
        $full_name = '';
        if($this->user) {
            $full_name = $this->user->getFullName();
        }

        return $full_name;

    }

    /* Геттер для баланса*/
    public function getBalance()
    {
        $balance = 0;
        if($this->user) {
            $balance = $this->user->balance;
        }

        return $balance;
    }

    /* Геттер для логина*/
    public function getLogin()
    {
        $username = '';
        if($this->user) {
            $username = $this->user->username;
        }

        return $username;
    }

    /* Геттер для логина админа*/
    public function getAdminLogin()
    {
        $admin_login = '';
        if($this->admin) {
            $admin_login = $this->admin->username;
        }

        return $admin_login;
    }

    /**
     * @return array
     */
    public function orientationArrayNames()
    {
        $status = ['Начислено', 'Вывод', 'Партнерские'];

        return $status;
    }
}
