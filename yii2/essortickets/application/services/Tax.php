<?php
namespace app\services;

use Yii;
use yii\base\Component;
use app\models\Tax as TaxModel;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class Tax
 * @package app\services
 * @version 1.0
 *
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class Tax extends Component
{
    /**
     * Return tax id
     * @param $id tour id
     * @return bool
     */
    public function getTaxIdByTourId($id)
    {
        $query = new Query();
        $query->select([
            'id'    => 'tax.id',
        ])
            ->from(['tax' => 'tax'])
            ->leftJoin('tour tour', 'tour.tax_id = tax.id')
            ->where(['tour.id' => $id]);
        $query = $query->one();
        $result = $query['id'];
        if($result == 1){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Return tax amount
     * @param $id tour id
     * @return bool
     */
    public function getIncludedByTourId($id)
    {
        $query = new Query();
        $query->select([
            'included'    => 'tax.included',
        ])
            ->from(['tax' => 'tax'])
            ->leftJoin('tour tour', 'tour.tax_id = tax.id')
            ->where(['tour.id' => $id]);
        $query = $query->one();
        $result = $query['included'];
        if($result == 1){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Return tax amount
     * @param $id tour id
     * @return bool
     */
    public function getCumulativeByTourId($id)
    {
        $query = new Query();
        $query->select([
            'cumulative'    => 'tax.cumulative',
        ])
            ->from(['tax' => 'tax'])
            ->leftJoin('tour tour', 'tour.tax_id = tax.id')
            ->where(['tour.id' => $id]);
        $query = $query->one();
        $result = $query['cumulative'];
        if($result == 1){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Return tax amount
     * @param $id tour id
     * @return mixed
     */
    public function getTaxByTourId($id)
    {
        $query = new Query();
        $query->select([
            'amount'    => 'tax.amount',
        ])
            ->from(['tax' => 'tax'])
            ->leftJoin('tour tour', 'tour.tax_id = tax.id')
            ->where(['tour.id' => $id]);
        $query = $query->one();
        $result = $query['amount'];
        return $result;
    }

    /**
     * Return tax amount
     * @param $id order id
     * @return mixed
     */
    public function getTaxByOrderId($id)
    {
        $query = new Query();
        $query->select([
            'amount'    => 'tax.amount',
        ])
            ->from(['tax' => 'tax'])
            ->leftJoin('tour tour', 'tour.tax_id = tax.id')
            ->leftJoin('ticket tk', 'tk.tour_id = tour.id')
            ->leftJoin('ticket_booked_buy tbb', 'tbb.ticket_id = tk.id')
            ->leftJoin('orders o', 'o.id = tbb.order_id')
            ->where(['o.id' => $id]);
        $query = $query->one();
        $result = $query['amount'];
        return $result;
    }

    /**
     * @param $id tax id
     * @return string
     */
    public function getTaxNameAndAmountPercentageById($id)
    {
        /** @var \app\models\Tax $result */
        $model = TaxModel::findOne(['id' => $id]);
        if(is_null($model)){
            $result = '-';
        }else{
            $result = $model->name . ' / ' . $model->amount . '%';
        }
        return $result;
    }

    /**
     * Return array with tour id and name
     * @return array
     */
    public function getTaxIdsNames()
    {
        /** @var array $requirements */
        $requirements = TaxModel::find()->all();
        /** @var array $options */
        $options = ArrayHelper::map($requirements, 'id', 'name');

        return $options;
    }
}