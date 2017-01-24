<?php

namespace app\services;

use app\enums\FeeType;
use app\enums\OrderType;
use app\helpers\ModelNamesProvider;
use app\models\Ticket as TicketModel;
use app\models\TicketBookedBuy;
use Yii;
use yii\base\Component;
use app\modules\admin\models\search\Booking as BookingModel;
use yii\db\ActiveRecord;
use yii\db\Query;

/**
 * Class for implements logic with Booking
 * @package app\services
 * @version 1.0
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class Booking extends Component
{

    /**
     * Set booking_price
     * @param integer $id booking id
     * @throws \ErrorException
     */
    public function setBookingPriceById($id)
    {
        /** @var \app\modules\admin\models\search\Booking $modelBooking */
        $modelBooking = new BookingModel();
        $modelBooking = $modelBooking::findOne(['id' => $id]);

        /** @var \app\models\TicketBookedBuy $modelTicketBookedDuys */
        $modelTicketBookedBuys = new TicketBookedBuy();
        $modelTicketBookedBuys = $modelTicketBookedBuys::findAll(['order_id' => $modelBooking->order_id]);

        /** @var \app\services\Tax $tax */
        $tax = Yii::$app->tax->getTaxByOrderId($modelBooking->order_id);

        /** @var float $bookingPrice */
        $bookingPrice = 0;

        foreach ($modelTicketBookedBuys as $modelTicketBookedBuy){
            /** @var \app\models\Ticket $modelTicket */
            $modelTicket = new TicketModel();
            $modelTicket = $modelTicket::findOne(['id' => $modelTicketBookedBuy->ticket_id]);

            if($modelTicket->booking_fee_type === FeeType::FEE_TYPE_FIXED){
                $bookingPrice = $bookingPrice + $modelTicket->booking_fee;
            }else{
                $bookingPrice = $bookingPrice + ($modelTicket->price / 100) * $modelTicket->booking_fee;
            }
        }
        $tax = ($bookingPrice / 100) * $tax;
        //plus tax
        $bookingPrice = $bookingPrice + $tax;
        $modelBooking->booking_price = $bookingPrice;
        if(!$modelBooking->save(false)){
            throw new \ErrorException('Booking price not set!');
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
     * @param string $old
     * @return false|string
     */
    public function getNewDateTime($old)
    {
        $startDate = strtotime($old);

        return date('Y-m-d H:i', strtotime('+1 day', $startDate));
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