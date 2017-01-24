<?php

namespace app\services;

use app\enums\FeeType;
use app\enums\OrderType;
use app\enums\Status;
use app\helpers\ModelNamesProvider;
use app\models\Booking as BookingModel;
use app\models\Coupon as CouponModel;
use app\models\Ticket as TicketModel;
use app\models\TicketBookedBuy as TicketBookedBuyModel;
use Yii;
use yii\base\Component;
use app\models\Orders as OrdersModel;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * Class for implements logic with Orders
 * @package app\services
 * @version 1.0
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class Orders extends Component
{

    /**
     * This method set total price to order by order id
     * @param $id order id
     * @throws \ErrorException
     */
    public function setTotalPriceByOrderId($id)
    {
    /** @var \app\models\Orders $modelOrder */
    $modelOrder = new OrdersModel();
    $modelOrder = $modelOrder::findOne(['id' => $id]);
    /** @var \app\models\Booking $modelBooking */
    $modelBooking = new BookingModel();
    $modelBooking = $modelBooking::findOne(['order_id' => $modelOrder->id]);
    /** @var \app\models\TicketBookedBuy $modelTicketBookedBuys */
    $modelTicketBookedBuys = new TicketBookedBuyModel();
    $modelTicketBookedBuys = $modelTicketBookedBuys::findAll(['order_id' => $modelOrder->id]);
    /** @var \app\models\Coupon $modelCoupon */
    $modelCoupon = new CouponModel();
    $modelCoupon = $modelCoupon::findOne(['code' => $modelOrder->coupon_code]);
    /** @var \app\services\Tax $tax */
    $tax = Yii::$app->tax->getTaxByOrderId($modelOrder->id);

    /** @var float $totalPrice */
    $totalPrice = 0;

    foreach ($modelTicketBookedBuys as $modelTicketBookedBuy){
        /** @var \app\models\Extras $modelTicketBookedBuy */
        $modelExtras = Yii::$app->extras->getExtrasById($modelTicketBookedBuy->extra_id);
        /** @var \app\models\Ticket $modelTicket */
        $modelTicket = new TicketModel();
        $modelTicket = $modelTicket::findOne(['id' => $modelTicketBookedBuy->ticket_id]);

        $totalPrice = $totalPrice + $modelTicket->price;
        if($modelExtras !== null){
            $totalPrice = $totalPrice + $modelExtras->price;
        }
        if($modelCoupon !== null){
            if($modelCoupon->discount_type == FeeType::FEE_TYPE_FIXED){
                $totalPrice = $totalPrice + $modelCoupon->discount;
            }else{
                $totalPrice = $totalPrice + ($modelTicket->price / 100) * $modelCoupon->discount;
            }
        }
    }
        $tax = ($totalPrice / 100) * $tax;
        //plus tax
        $totalPrice = $totalPrice + $tax;
        if($modelBooking !== null){
            $totalPrice = $totalPrice + $modelBooking->booking_price;
        }
        $modelOrder->total_price = $totalPrice;
        if(!$modelOrder->save(false)){
            throw new \ErrorException('Order total price not set!');
        }
    }

    /**
     * @param integer $id ticket_id
     * @return mixed
     */
    public function getCurrencyById($id)
    {
        $query = new Query();
        $query->select([
            'currency'    => 'tour.currency',
        ])
            ->from(['t' => 'ticket'])
            ->leftJoin('tour tour', 'tour.id = t.tour_id')
            ->where(['t.id' => $id]);
        $query = $query->one();
        $result = $query['currency'];
        return $result;
    }

    /**
     * @param integer $id ticket_id
     * @param int $type
     * @return string
     */
    public function getTotalPriceWithCurrency($id, $type = OrderType::ORDER)
    {
        $query = new Query();
        if($type == OrderType::ORDER) {
            $query->select([
                'price'    => 't.price',
            ])
                ->from(['t' => 'ticket'])
                ->leftJoin('orders o', 'o.ticket_id = t.id')
                ->where(['t.id' => $id, 'o.type' => $type]);
            $query = $query->one();
            $result = $query['price'];
            return $result;
        }else {
            $query->select([
                'price' => 't.price',
                'booking_fee_type' => 't.booking_fee_type',
                'booking_fee' => 't.booking_fee',
            ])
                ->from(['t' => 'ticket'])
                ->leftJoin('orders o', 'o.ticket_id = t.id')
                ->where(['t.id' => $id, 'o.type' => $type])
                ->groupBy(['t.price']);;
            $query = $query->one();
            if($query['booking_fee_type'] == 1){
                $result = $query['booking_fee'];
            }else{
                $result= number_format($query['price'] * $query['booking_fee'] / 100, 2);
            }
            return $result;
        }
    }

    /**
     * @param string $addTime example '+1 day'
     * @param string $old
     * @return false|string
     */
    public function getNewDateTime($old, $addTime = '+1 day')
    {
        $startDate = strtotime($old);

        return date('Y-m-d H:i', strtotime($addTime, $startDate));
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

    /**
     * Finds the order model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function findModel($id)
    {
        if (($model = OrdersModel::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}