<?php
namespace app\commands;

use app\base\Command;
use app\enums\Status;
use app\enums\YesNo;
use app\models\Coupon;
use app\modules\admin\models\search\Booking as BookingSearch;
use app\modules\admin\models\search\Orders as OrdersSearch;
use DateTime;
use DateTimeZone;
use Yii;

/**
 * Status Controller.
 * @version 1.0
 *
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class StatusController extends Command
{

    /**
     * This method check the status coupons
     * @throws \ErrorException
     */
    public function actionCouponCheckAlive()
    {
        /** @var string $now date time */
        $now =(new DateTime('now', new DateTimeZone('Europe/Kiev')))->format('Y-m-d H:i:s');
        /** @var \app\models\Coupon $models */
        $models = Coupon::findAll(['status' => YesNo::YES]);

        if($models !== null){
            foreach ($models as $model){
                /** @var \app\models\Coupon $model */
                if($model->ends_on < $now || $model->expires_on < $now ){
                    $model->status = YesNo::NO;
                    if(!$model->save()){
                        throw new \ErrorException("Status coupon #{$model->id} {$model->name}, not updated!");
                    }
                }elseif ($model->used >= $model->limit){
                    $model->status = YesNo::NO;
                    if(!$model->save()){
                        throw new \ErrorException("Status coupon #{$model->id} {$model->name}, not updated!");
                    }
                }
            }
        }
    }

    /**
     * This method changes the status (any) to expire
     * @return bool
     */
    public function actionCountdown()
    {

        $now =(new DateTime('now', new DateTimeZone('Europe/Kiev')))->format('Y-m-d H:i:s');
        /** @var \app\modules\admin\models\search\Orders $models */
        $models = OrdersSearch::find()
            ->where("status = " . Status::PENDING . " OR status = " . Status::EDITING)
            ->all();
        if ($models !== null) {
            foreach ($models as $model) {

                /** @var \app\modules\admin\models\search\Orders $model */
                if($model->valid_to < $now) {
                    /** @var \app\modules\admin\models\search\Booking $modelBooking */
                    $modelBooking = new BookingSearch();
                    $modelBooking = $modelBooking::findOne(['order_id' => $model->id]);

                    if ($model->status == Status::PENDING) {
                        /** @var \app\services\TicketBookedBuy $startDate */
                        $startDate = Yii::$app->ticket_booked_buy->getDateByOrderId($model->id);
                        /** @var \app\services\TicketBookedBuy $product */
                        $product = Yii::$app->ticket_booked_buy->getProductNameByOrderId($model->id);
                        /** @var \app\models\Events $modelEvents */
                        $modelEvents = Yii::$app->events->findModelByTitleAndDate($product, $startDate);
                        if ($modelEvents !== null) {
                            $modelEvents->size_pending = $modelEvents->size_pending - $model->size;

                            if ($modelEvents->save(false)) {
                                if ($model->updateField('status', Status::EXPIRED)) {
                                    if ($modelBooking !== NULL) {
                                        $modelBooking->updateField('status', Status::EXPIRED);
                                    }
                                }
                            } else {
                                return false;
                            }
                        }else{
                            if ($model->updateField('status', Status::EXPIRED)) {
                                if ($modelBooking !== NULL) {
                                    $modelBooking->updateField('status', Status::EXPIRED);
                                }
                            } else {
                                return false;
                            }
                        }
                    } elseif ($model->status == Status::EDITING) {
                        if ($model->updateField('status', Status::EXPIRED)) {
                            if ($modelBooking !== NULL) {
                                $modelBooking->updateField('status', Status::EXPIRED);
                            }
                        } else {
                            return false;
                        }
                    }
                }else {
                    return false;
                }
            }
        }
    }

}