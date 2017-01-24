<?php

namespace app\modules\admin\controllers;

use app\controllers\actions\Suggestions;
use app\enums\OrderType;
use app\enums\Status;
use app\enums\YesNo;
use app\models\Booking;
use app\models\Message;
use app\models\TicketBookedBuy;
use Composer\IO\NullIO;
use Exception;
use Yii;
use app\models\Orders;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;
use app\modules\admin\base\Controller;
use yii\filters\VerbFilter;
use app\modules\admin\models\search\Orders as OrdersSearch;
use app\modules\admin\models\search\Booking as BookingSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * OrdersController implements the CRUD actions for Order model.
 */
class OrdersController extends Controller
{

    /**
     * @param $id
     * @return string
     */
    public function actionEditOrder($id)
    {
        /** @var \app\modules\admin\models\search\Orders $model */
        $modelOrders = $this->findModel($id);
        if(Yii::$app->request->post()){
            $post = ArrayHelper::merge(Yii::$app->request->post(), ['Orders'=>[
                'valid_from' => date('Y-m-d H:i:s', time()),
                'valid_to'   => date('Y-m-d H:i:s', time() + Status::getCountdownById(Status::EDITING)),
                'status'     => Status::EDITING,
                'created_at' => (int)time(),
                'confirm'    => YesNo::NO,
            ]]);
            if ($modelOrders->load($post) && $modelOrders->save(false)) {
                $modelBooking = new BookingSearch();
                $modelBooking = $modelBooking::findOne(['order_id' => $modelOrders->id]);
                if ($modelBooking !== NULL) {
                    $modelBooking->updateField('status', Status::EDITING);

                    $this->redirect(['manage-order-detail', 'id' => $modelOrders->id]);
                }else{
                    $this->redirect(['manage-order-detail', 'id' => $modelOrders->id]);
                }
            }
        }
        elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('partials/_form', [
                'model' => $modelOrders,
                'managers'  => Yii::$app->user_component->getUsers(),
            ]);
        } else {
            return $this->render('partials/_form', [
                'model' => $modelOrders,
                'managers'  => Yii::$app->user_component->getUsers(),
            ]);
        }
    }

    /**
     * This method render manageOrderDetail
     * @param string $id
     * @return string
     */
    public function actionManageOrderDetail($id)
    {
        /** @var \app\models\Orders $model */
        $model = $this->findModel($id);
        /** @var \app\modules\admin\models\search\Orders $modelOrders */
        $modelOrders = new OrdersSearch();
        /** @var \app\modules\admin\models\search\Booking $modelBooking */
        $modelBooking = new BookingSearch();
        $modelBookingF = $modelBooking::findOne(['order_id' => $model->id]);

        $customer = Yii::$app->customer->getCustomerFullNameById($model->customer_id);
        return $this->render('manageOrderDetail', [
            'model'                => $model,
            'modelOrders'          => $modelOrders,
            'modelBooking'         => $modelBookingF,
            'dataProviderBooking'  => $modelBooking->search(Yii::$app->request->get(), $id),
            'dataProvider'         => $modelOrders->search(Yii::$app->request->get(), $id),
            'peopleSize'           => Yii::$app->ticket_booked_buy->getSumSize($model->id),
            'product'              => Yii::$app->tour->getProductNameByOrderId($model->id),
            'username'             => Yii::$app->user_component->getUserNameById($model->user_id),
            'status'               => $model->status,
            'customers'            => Yii::$app->customer->getCustomersIdsFullNames(),
            'customer'             => $customer,
        ]);
    }

    /**
     * This method created new Order
     * @return string|\yii\web\Response
     */
    public function actionCreateNewOrder()
    {
        /** @var \app\modules\admin\models\search\Orders $model */
        $modelOrders = new OrdersSearch();

        if(Yii::$app->request->post()){
            $post = ArrayHelper::merge(Yii::$app->request->post(), ['Orders'=>[
                'valid_from' => date('Y-m-d H:i:s', time()),
                'valid_to'   => date('Y-m-d H:i:s', time() + Status::getCountdownById(Status::EDITING)),
                'status'     => Status::EDITING,
                'created_at' => (int)time(),
            ]]);
            if ($modelOrders->load($post) && $modelOrders->save()) {
                return $this->redirect(['manage-order-detail', 'id' => $modelOrders->id]);
            }
        }
        elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('partials/_form', [
                'model' => $modelOrders,
                'managers'  => Yii::$app->user_component->getUsers(),
            ]);
        } else {
            return $this->render('partials/_form', [
                'model' => $model,
                'managers'  => Yii::$app->user_component->getUsers(),
            ]);
        }
    }

    /**
     * @param integer $id order_id
     * @return \yii\web\Response
     * @throws \ErrorException
     */
    public function actionUpdateCustomer($id)
    {
        /** @var \app\modules\admin\models\search\Orders $model */
        $model = $this->findModel($id);

        /** @var array $post */
        $post = Yii::$app->request->post();
        if ($model->load($post) && $model->save(false)) {
            return $this->redirect(['manage-order-detail', 'id' => $id]);
        }else{
            throw new \ErrorException('Error. Customer not selected');
        }
    }

    /**
     * This method changes the status (any) to expire
     * @param integer $id
     * @return bool
     */
    public function actionCountdown($id)
    {
        /** @var \app\modules\admin\models\search\Orders $model */
        $model = $this->findModel($id);
        /** @var \app\models\Booking $modelBooking */
        $modelBooking = new BookingSearch();
        $modelBooking = $modelBooking::findOne(['order_id' => $model->id]);

        if($model->status == Status::PENDING){

            /** @var \app\services\TicketBookedBuy $startDate */
            $startDate = Yii::$app->ticket_booked_buy->getDateByOrderId($model->id);
            /** @var \app\services\TicketBookedBuy $product */
            $product = Yii::$app->ticket_booked_buy->getProductNameByOrderId($model->id);
            /** @var \app\models\Events $modelEvents */
            $modelEvents = Yii::$app->events->findModelByTitleAndDate($product, $startDate);
            $modelEvents->size_pending = $modelEvents->size_pending - $model->size;

            if($modelEvents->save(false)){
                if ($model->updateField('status', Status::EXPIRED)) {
                    if ($modelBooking !== NULL) {
                        $modelBooking->updateField('status', Status::EXPIRED);
                        $this->redirect(['manage-order-detail', 'id' => $id]);
                    }else{
                        $this->redirect(['manage-order-detail', 'id' => $id]);
                    }
                }
            }else{
                return false;
            }
        }else{
            if ($model->updateField('status', Status::EXPIRED)) {
                if ($modelBooking !== NULL) {
                    $modelBooking->updateField('status', Status::EXPIRED);
                    $this->redirect(['manage-order-detail', 'id' => $id]);
                }else{
                    $this->redirect(['manage-order-detail', 'id' => $id]);
                }
            }else{
                return false;
            }
        }
    }

    /**
     * This method changes the status editing to pending
     * @param integer $id order_id
     * @return \yii\web\Response
     * @throws \ErrorException
     */
    public function actionConfirm($id)
    {
        /** @var \app\modules\admin\models\search\Orders $model */
        $model = $this->findModel($id);
        /** @var \app\models\Booking $modelBooking */
        $modelBooking = new BookingSearch();
        $modelBooking = $modelBooking::findOne(['order_id' => $id]);

        /** @var array $post */
        $post = Yii::$app->request->post();
        if ($model->load($post) && $model->save(false)) {
            if ($model->updateField('status', Status::PENDING)) {
                if ($modelBooking !== NULL) {
                    if($modelBooking->updateField('status', Status::PENDING)){

                        /** @var \app\services\TicketBookedBuy $product */
                        $product =Yii::$app->ticket_booked_buy->getProductNameByOrderId($model->id);
                        /** @var \app\services\TicketBookedBuy $startDate */
                        $startDate =Yii::$app->ticket_booked_buy->getDateByOrderId($model->id);
                        /** @var \app\models\Events $modelEvents */
                        $modelEvents = Yii::$app->events->findModelByTitleAndDate($product, $startDate);
                        $modelEvents->size_pending = $modelEvents->size_pending + $model->size;

                        if($modelEvents->save(false)){
                            $this->redirect(['manage-order-detail', 'id' => $id]);
                        }else{
                            throw new \ErrorException('Error. Event Not update!');
                        }
                    }else{
                        throw new \ErrorException('Error. Booking Not confirmed!');
                    }
                }
                $this->redirect(['manage-order-detail', 'id' => $id]);
            }
            return $this->redirect(['manage-order-detail', 'id' => $id]);
        }else{
            throw new \ErrorException('Error. Not confirmed!');
        }
    }

    /**
     * This method changes the status (any) to canceled
     * @param integer $id Order
     * @return bool
     */
    public function actionCancelOrder($id)
    {

        /** @var \app\modules\admin\models\search\Orders $model */
        $model = $this->findModel($id);
        /** @var \app\models\Booking $modelBooking */
        $modelBooking = new BookingSearch();
        $modelBooking = $modelBooking::findOne(['order_id' => $model->id]);

        if($model->status == Status::PENDING){

            /** @var \app\services\TicketBookedBuy $startDate */
            $startDate = Yii::$app->ticket_booked_buy->getDateByOrderId($model->id);
            /** @var \app\services\TicketBookedBuy $product */
            $product = Yii::$app->ticket_booked_buy->getProductNameByOrderId($model->id);
            /** @var \app\models\Events $modelEvents */
            $modelEvents = Yii::$app->events->findModelByTitleAndDate($product, $startDate);
            $modelEvents->size_pending = $modelEvents->size_pending - $model->size;

            if($modelEvents->save(false)){
                if ($model->updateField('status', Status::CANCELED)) {
                    if ($modelBooking !== NULL) {
                        $modelBooking->updateField('status', Status::CANCELED);
                        $this->redirect(['manage-order-detail', 'id' => $id]);
                    }else{
                        $this->redirect(['manage-order-detail', 'id' => $id]);
                    }
                }
            }else{
                return false;
            }
        }elseif ($model->status == Status::CONFIRMED){

            /** @var \app\services\TicketBookedBuy $startDate */
            $startDate = Yii::$app->ticket_booked_buy->getDateByOrderId($model->id);
            /** @var \app\services\TicketBookedBuy $product */
            $product = Yii::$app->ticket_booked_buy->getProductNameByOrderId($model->id);
            /** @var \app\models\Events $modelEvents */
            $modelEvents = Yii::$app->events->findModelByTitleAndDate($product, $startDate);
            $modelEvents->size_use = $modelEvents->size_use - $model->size;

            if($modelEvents->save(false)){
                if ($model->updateField('status', Status::CANCELED)) {
                    if ($modelBooking !== NULL) {
                        $modelBooking->updateField('status', Status::CANCELED);
                        $this->redirect(['manage-order-detail', 'id' => $id]);
                    }else{
                        $this->redirect(['manage-order-detail', 'id' => $id]);
                    }
                }
            }else{
                return false;
            }
        }else{
            if ($model->updateField('status', Status::CANCELED)) {
                if ($modelBooking !== NULL) {
                    $modelBooking->updateField('status', Status::CANCELED);
                    $this->redirect(['manage-order-detail', 'id' => $id]);
                }else{
                    $this->redirect(['manage-order-detail', 'id' => $id]);
                }
            }else{
                return false;
            }
        }
    }

    /**
     * This method changes the status (any) to confirm
     * @param integer $id
     * @return bool
     * @throws ErrorException
     * @throws \Error
     */
    public function actionConfirmOrder($id)
    {
        /** @var \app\modules\admin\models\search\Orders $model */
        $model = $this->findModel($id);
        /** @var \app\models\Booking $modelBooking */
        $modelBooking = new BookingSearch();
        $modelBooking = $modelBooking::findOne(['order_id' => $model->id]);

        if($model->status == Status::PENDING){

            /** @var \app\services\TicketBookedBuy $startDate */
            $startDate = Yii::$app->ticket_booked_buy->getDateByOrderId($model->id);
            /** @var \app\services\TicketBookedBuy $product */
            $product = Yii::$app->ticket_booked_buy->getProductNameByOrderId($model->id);
            /** @var \app\models\Events $modelEvents */
            $modelEvents = Yii::$app->events->findModelByTitleAndDate($product, $startDate);
            $modelEvents->size_pending = $modelEvents->size_pending - $model->size;
            $modelEvents->size_use = $modelEvents->size_use + $model->size;

            if($modelEvents->save(false)){

                /** @var \app\models\TicketBookedBuy $modelTicketBookedBuy */
                $modelTicketBookedBuy = new TicketBookedBuy();
                $modelTicketBookedBuy = $modelTicketBookedBuy::findOne(['order_id' => $model->id]);
                $modelTicketBookedBuy->event_id = $modelEvents->id;
                if(!$modelTicketBookedBuy->save(false)){
                    throw new ErrorException('Event id(s) not preserved!');
                }

                if($modelEvents->size_all == $modelEvents->size_use){
                    if($model->customer_id){
                        $product = Yii::$app->tour->getProductNameByOrderId($model->id);
                        $username = Yii::$app->customer->getCustomerFullNameById($model->customer_id);
                        Yii::$app->user_notifier->send(
                            Yii::$app->customer->getCustomerEmailById($model->customer_id),
                            'All tickets sold',
                            'user_guide_event_notify_full',
                            [
                                'username'  => $username,
                                'tourName'  => $product,
                                'startDate' => $modelEvents->start_date,
                            ]);
                        Yii::$app->admin_notifier->message('All tickets sold',
                            "All tickets sold for tour #{$product}, date start {$modelEvents->start_date},
                         user #{$username}");
                    }

                }
                if ($model->updateField('status', Status::CONFIRMED)) {
                    if ($modelBooking !== NULL) {
                        $modelBooking->updateField('status', Status::CONFIRMED);
                        // TODO create PDF TICKET and save to message and then send to customer.
                        //Create PDF and save to temp folder.
                        Yii::$app->pdf_component->CreateAndSavePdfTickets($model->id);
                        /** @var \app\models\Message $modelMessage */
                        $modelMessage = new Message();
                        $modelMessage->customer_id = $model->customer_id;
                        $modelMessage->head = Yii::t('app', 'Your ticket(s)');
                        $modelMessage->body = Yii::$app->tour->getConfirmationMailByOrderId($model->id);
                        $modelMessage->attachment = 'ticket(s)-order#' . $model->id . '.pdf';
                        $modelMessage->isNewRecord;
                        if(!$modelMessage->save()){
                            throw new \Error('Not save confirmation mail!');
                        }
                        $this->redirect(['manage-order-detail', 'id' => $id]);
                    }else{
                        $this->redirect(['manage-order-detail', 'id' => $id]);
                    }
                }
            }else{
                return false;
            }
        }else{
            if ($model->updateField('status', Status::CONFIRMED)) {
                if ($modelBooking !== NULL) {
                    $modelBooking->updateField('status', Status::CONFIRMED);
                    $this->redirect(['manage-order-detail', 'id' => $id]);
                }else{
                    $this->redirect(['manage-order-detail', 'id' => $id]);
                }
            }else{
                return false;
            }
        }
    }

    /**
     * Universal change order status method
     * @param integer $id order_id
     * @param integer $status
     * @throws \ErrorException
     */
    public function actionChangeOrderStatus($id, $status)
    {
        /** @var \app\modules\admin\models\search\Orders $model */
        $model = $this->findModel($id);

        if ($model->updateField('status', Status::getStatusById($status))) {

        }else{
            throw new \ErrorException('Error. Status has not changed!');
        }
    }

    /**
     * Finds the Orders model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Orders the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OrdersSearch::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

    }
}
