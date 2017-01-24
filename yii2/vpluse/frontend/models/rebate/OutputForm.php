<?php
namespace frontend\models\rebate;

use yii\base\Model;
use Yii;
use backend\models\Pay;

/**
 * Output money form
 *
 */
class OutputForm extends Model
{
    public $pay_system;
    public $payment_detail;
    public $credit;


    public function rules()
    {
        $service = isset($_SESSION['service']) ? $_SESSION['service'] : null;
        $max = ($service != 'partner') ? Yii::$app->user->identity->balance : Yii::$app->user->identity->balance_partner;
        return [
            [['credit', 'payment_detail', 'pay_system'], 'required'],
            [['credit'], 'number', 'min' => 0, 'max' => $max, 'tooBig' => 'Сумма не может превышать ваш баланс ({max}$)'],
            [['payment_detail'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'credit' => 'Сумма (в долларах США)',
            'payment_detail' => 'Выберете счет',
            'pay_system' => 'Выберете платежную систему',
        ];
    }

    public function getPaySystems()
    {
        $pay_systems[''] = '';
        $paySystems = Pay::find()->where('status=1')->all();
        if($paySystems) {
            foreach ($paySystems as $pay) {
                $pay_systems[$pay->id] = $pay->name;
            }
        }

        return $pay_systems;
    }

    public function formatPaymentDetail()
    {

        $paySystem = Pay::findOne($this->pay_system);
        $pay_systems_code = $paySystem ? $paySystem->code . '/' : '';

        return $pay_systems_code. $this->payment_detail;
    }



}