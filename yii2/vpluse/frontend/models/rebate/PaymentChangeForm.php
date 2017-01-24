<?php
namespace frontend\models\rebate;

use yii\base\Model;
use common\models\User;
use backend\models\Pay;
use frontend\models\rebate\UserPay;

/**
 * Payment change form
 *
 */
class PaymentChangeForm extends Model
{
    public $currentPassword;
    public $payment_detail;

    /**
     * @var User
     */
    private $_user;

    /**
    * @var Pay
    */
    private $_pay;

    /**
     * @param User $user
     * @param array $config
     */
    public function __construct(User $user, Pay $pay, $config = [])
    {
        $this->_user = $user;
        $this->_pay = $pay;
        if($this->_pay->userPays) {
            $this->payment_detail = $this->_pay->userPays[0]->value;
        }
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['currentPassword', 'payment_detail'], 'required'],
            ['currentPassword', 'currentPassword'],
            [['payment_detail'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'currentPassword' => 'Пароль',
            'payment_detail' => 'Реквизиты',
        ];
    }

    public function getPaymentName()
    {
        return $this->_pay->name;
    }

    public function getPaymentTip()
    {
        return $this->_pay->tip;
    }

    /**
     * @param string $attribute
     * @param array $params
     */
    public function currentPassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (!$this->_user->validatePassword($this->$attribute)) {
                $this->addError($attribute, 'Неверный пароль');
            }
        }
    }

    /**
     * @return boolean
     */
    public function changePayment()
    {
        if ($this->validate()) {

            if($this->_pay->userPays) {
                $user_pay = UserPay::findOne($this->_pay->userPays[0]->id);
            } else {
                $user_pay = new UserPay();
                $user_pay->user_id = $this->_user->id;
                $user_pay->pay_id = $this->_pay->id;

            }
            $user_pay->value = $this->payment_detail;
            $user_pay->save();

            return true;

        } else {

            return false;
        }
    }
}