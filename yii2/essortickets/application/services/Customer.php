<?php

namespace app\services;

use app\enums\Status;
use app\models\Orders;
use yii\base\Component;
use app\models\Customer as CustomerModel;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use \app\helpers\ModelNamesProvider;
use \Yii;
use \yii\db\ActiveRecord;

/**
 * Class for implements logic with Customer
 * @package app\services
 * @version 1.0
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class Customer extends Component
{
    /**
     * This method return total spent money by customer
     * @param $id customer_id
     * @return float
     */
    public function getTotalMoneySpentByCustomerId($id)
    {
        $query = new Query();
        $query->select([
        'booking_price'    => 'b.booking_price',
        'order_price'      => 'o.total_price',
        'order_status'     => 'o.status'
        ])
        ->from(['b' => 'booking'])
        ->leftJoin('orders o', 'o.id = b.order_id')
        ->where(['o.customer_id' => $id])
        ->andWhere(['b.status'=> Status::CONFIRMED]);
        $results = $query->all();
        $money = 0;
        foreach ($results as $result){
            if($result['order_status'] != Status::CONFIRMED){
                $money = $money + $result['booking_price'];
            }else{
                $money = $money + $result['order_price'];
            }
        }
        return $money;
    }

    /**
     * Return customer model
     * @param integer $id order id
     * @return null|string
     */
    public function getCustomerByOrderId($id)
    {
        /** @var \app\models\Orders $modelOrder */
        $modelOrder = Orders::findOne(['id' =>$id]);

        /** @var \app\models\Customer $model */
        if (($model = CustomerModel::findOne(['id'=>$modelOrder->customer_id])) !== null) {
            return $model;
        } else {
            return null;
        }
    }

    /**
     * Return customer full name
     * @param $id customer_id
     * @return string
     */
    public function getCustomerFullNameById($id){
        /** @var \app\models\Customer $model */
        if (($model = CustomerModel::findOne(['id'=>$id])) !== null) {
            return $model->name . " " . $model->last_name;
        } else {
            return null;
        }
    }

    /**
     * Return customer email
     * @param $id customer_id
     * @return string
     */
    public function getCustomerEmailById($id){
        /** @var \app\models\Customer $model */
        $model = CustomerModel::find($id)->one();

        return $model->email;
    }

    /**
     * Return array with customers id and full name
     * @return array
     */
    public function getCustomersIdsFullNames()
    {
        /** @var array $requirements */
        $requirements = CustomerModel::find()->all();
        /** @var array $options */
        $options = ArrayHelper::map($requirements, 'id', 'fullName');

        return $options;
    }

    /**
     * Returns list of suggestions for a field of specific model
     * @param int $modelCode Enum value for model name
     * @param string $field Name of the field to search
     * @param string $value Value to search
     * @param int $limit Output limit
     * @param $user_id
     * @return array
     */
    public function getAutoCompleteSuggestions($modelCode, $field, $value, $limit = 5, $user_id)
    {
        $model = $this->createModel($modelCode);
        return (isset($model) && $model->hasMethod('getSuggestions', true)) ?
            $model->getSuggestions($field, $value, $limit, $user_id) : array();
    }


    /**
     * Models factory
     * @param int $modelCode Enum value for model name
     * @return ActiveRecord $model
     */
    private function createModel($modelCode)
    {
        $className = ModelNamesProvider::getModelNameByCode($modelCode);
        if (empty($className)) {
            return null;
        }

        $result = null;
        if(is_a($className, ActiveRecord::className(), true)) {
            $result = new $className();
        }
        return $result;
    }

}
