<?php

namespace app\modules\admin\controllers;

use app\controllers\actions\Suggestions;
use app\enums\Status;
use app\models\Answer;
use app\models\Coupon;
use app\models\Events;
use app\models\Extras;
use app\models\Orders;
use app\models\Tour;
use app\modules\admin\models\search\TicketBookedBuy;
use app\models\data\Message as MessageModel;
use kartik\mpdf\Pdf;
use Yii;
use app\models\Booking;
use app\modules\admin\base\Controller;
use yii\filters\VerbFilter;
use app\modules\admin\models\search\Booking as BookingSearch;
use app\modules\admin\models\search\Orders as OrdersSearch;
use app\modules\admin\models\search\Question as QuestionSearch;
use yii\helpers\ArrayHelper;
use yii\httpclient\Response;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * BookingController implements the CRUD actions for Booking model.
 */
class BookingController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'suggestion'           => ['get'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'suggestion' => [
                'class' => Suggestions::className(),
            ]
        ];
    }

    /**
     * This method create and download ticket(s)
     * @param integer $id booking id
     */
    public function actionReport($id) {
        /** \app\service\ */
        Yii::$app->pdf_component->DownloadPdfTickets($id);
    }

    /**
     * Lists all Booking models.
     * @return mixed
     */
    public function actionManage()
    {
        /** @var \app\modules\admin\models\search\Booking $model */
        $model = new BookingSearch();

        return $this->render('manage', [
            'model'             => $model,
            'dataProvider'      => $model->search(Yii::$app->request->get()),
            'autoCompleteLimit' => Yii::$app->params['common.autocomplete.limit'],
            'modelCode'         => \app\helpers\ModelNamesProvider::ADMIN_BOOKING_SEARCH,
        ]);
    }

    /**
     * @param integer $id order_id
     * @return string|\yii\web\Response
     */
    public function actionCreateBooking($id)
    {
        // TODO refactoring this method use DepDrop Widget - © Kartik
        /** @var \app\modules\admin\models\search\Booking $model */
        $model = new BookingSearch();
        /** @var \app\modules\admin\models\search\Orders $modelOrders */
        $modelOrders = new OrdersSearch();
        $modelOrders= $modelOrders::findOne(['id' => $id]);
        /** @var \app\models\Answer $modelAnswer */
        $modelAnswer = new Answer();

        $coupons= [];
        $variants = [];
        $extras =[];
        $questions =[];
        $dates=[];
        $tier=0;
        $ticket_available = 0;

        $tiers = Yii::$app->ticket->getTicketIdsNames(Yii::$app->request->get('product',''));
        $tour_id = Yii::$app->request->get('product','');
        if(Yii::$app->request->get('tier', false)){
            $tier = Yii::$app->request->get('tier', false);
            $dates = Yii::$app->events->getStartDatesByTourName(Yii::$app->tour->getProductNameProductId(Yii::$app->request->get('product','')));

            if(Yii::$app->request->get('start', false)){
                $ticket_available = Yii::$app->tour->getTicketAvailableByTourId(Yii::$app->request->get('product',''), base64_decode(Yii::$app->request->get('start', false)));
                $coupons = Yii::$app->coupon->getCouponIdsNames(Yii::$app->request->get('tier', false));
                $variants = Yii::$app->variant->getVariantIdsNames(Yii::$app->request->get('tier', false));
                $extras = Yii::$app->extras->getExtrasIdsNames(Yii::$app->request->get('tier', false));
                $questions = Yii::$app->question->getQuestionIdsNames(Yii::$app->request->get('tier', []));
            }
        }
        if(Yii::$app->request->post()){
            $post = ArrayHelper::merge(Yii::$app->request->post(), [
                'Orders'=>[
                    'status'      => Status::EDITING,
                    'coupon_code' => Yii::$app->coupon->getCouponCodeById(Yii::$app->request->post()['Orders']['coupon_code']),
                ],
                'Booking' =>[
                    'order_id'   => $id,
                    'status'     => Status::EDITING,
                    'valid_from' => $modelOrders->valid_from,
                    'valid_to'   => $modelOrders->valid_to,
                ],
                'TicketBookedBuy' =>[
                    'order_id'   => $id,
                ],
                'Answer' =>[
                    'order_id'   => $id,
                    'question_id' => Yii::$app->request->post()['TicketBookedBuy']['question_id']
                ]
            ]);
            //Increment used coupons.
            if(Yii::$app->request->post()['Orders']['coupon_code'] !== ''){
                /** @var \app\models\Coupon $modelCoupon */
                $modelCoupon = new Coupon();
                $modelCoupon = $modelCoupon::findOne(['id' => Yii::$app->request->post()['Orders']['coupon_code']]);
                $modelCoupon->used = $modelCoupon->used + 1;
                $modelCoupon->save(false);
            }

            if ($modelOrders->load($post) && $modelOrders->save(false)) {
                if ($model->load($post) && $model->save()) {
                    for($i=0; $i < $modelOrders->size; $i++){
                        /** @var \app\modules\admin\models\search\TicketBookedBuy $modelTickedBookedBuy */
                        $modelTickedBookedBuy = new TicketBookedBuy();
                        $modelTickedBookedBuy->load($post);
                        $modelTickedBookedBuy->setIsNewRecord(true);
                        $modelTickedBookedBuy->save();
                    }
                    //Set booking_price
                    Yii::$app->booking->setBookingPriceById($model->id);
                    //Set order total_price
                    Yii::$app->order->setTotalPriceByOrderId($model->order_id);
                        if ($modelAnswer->load($post) && $modelAnswer->save()) {
                            return $this->redirect(['/admin/orders/manage-order-detail', 'id' => $id]);
                        }
                }
            }
        }elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('partials/_form', [
                'model' => $model,
                'modelTickedBookedBuy' => new TicketBookedBuy(),
                'modelOrders' => $modelOrders,
                'modelAnswer' => $modelAnswer,
                'data'  => Yii::$app->user_component->getManagers(),
                'products' => Yii::$app->tour->getTourIdsNames(),
                'order_id' => $id,
                'tiers' => $tiers,
                'tier' => $tier,
                'tour_id'=> $tour_id,
                'coupons' => $coupons,
                'variants' => $variants,
                'extras' => $extras,
                'questions' => $questions,
                'dates' => $dates,
                'ticketAvailable'=> $ticket_available
            ]);
        }else{
            return $this->redirect(['/admin/orders/manage-order-detail', 'id' => $id]);
        }
    }

    /**
     * This method render manageBookingDetail
     * @param string $id
     * @return string
     */
    public function actionManageBookingDetail($id)
    {
        /** @var \app\models\Booking $model */
        $model = $this->findModel($id);
        /** @var \app\modules\admin\models\search\TicketBookedBuy $modelTicket */
        $modelTicket = new TicketBookedBuy();
        /** @var \app\modules\admin\models\search\Booking $modelBooking */
        $modelBooking = new BookingSearch();
        /** @var \app\modules\admin\models\search\Question $modelQuestion */
        $modelQuestion = new QuestionSearch();
        /** @var \app\services\Customer $customer */
        $customer = Yii::$app->customer->getCustomerByOrderId($model->order_id);
        /** @var \app\services\Message $modelMessage */
        $modelMessage = Yii::$app->message->findLastModelByCustomerIdAndSend($customer->id);

        return $this->render('manageBookingDetail', [
            'model'                => $model,
            'modelBooking'         => $modelBooking,
            'dataProvider'         => $modelBooking->search(Yii::$app->request->get(), $id),
            'modelTicket'          => $modelTicket,
            'dataProviderTicket'   => $modelTicket->search(Yii::$app->request->get(), $id),
            'modelQuestion'        => $modelQuestion,
            'dataProviderQuestion' => $modelQuestion->search(Yii::$app->request->get(), $model->order_id),
            'peopleSize'           => Yii::$app->ticket_booked_buy->getSumSize($model->order_id),
            'product'              => Yii::$app->tour->getProductNameByOrderId($model->order_id),
            'customer'             => $customer,
            'customerId'           => $customer->id,
            'modelMessage'         => $modelMessage,
        ]);
    }

    /**
     * This method save custom message to db. Send logic to be \app\commands\DeliveryController.php
     * @param integer $id customer id
     * @param integer $bookingId booking id
     * @return string|\yii\web\Response
     */
    public function actionSendCustomMessage($id, $bookingId)
    {
        /** @var \app\models\data\Message $modelMessages */
        $modelMessages = new MessageModel();
        if(Yii::$app->request->post()) {
            $post = ArrayHelper::merge(Yii::$app->request->post(), ['Message' => [
                'customer_id' => $id,
            ]]);
            if ($modelMessages->load($post)) {
                // get the uploaded file instance. for multiple file uploads
                // the following data will return an array
                $file = UploadedFile::getInstance($modelMessages, 'file');
                if ($file !== null) {
                    // store the source file name
                    $modelMessages->filename = $file->name;
                    $ext = end(explode(".", $file->name));

                    // generate a unique file name
                    $modelMessages->attachment = Yii::$app->security->generateRandomString() . ".{$ext}";

                    // the path to save file, you can set an uploadPath
                    // in Yii::$app->params (as used in example below)
                    $path = Yii::$app->params['uploadPath'] . $modelMessages->attachment;
                }
                if ($modelMessages->save()) {
                    if ($file !== null) {
                        $file->saveAs($path);
                    }
                    $this->redirect(['manage-booking-detail', 'id' => $bookingId]);
                }
            }
        }elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('partials/_form_send_messages', [
                'model' => $modelMessages,
            ]);
        } else {
            return $this->render('partials/_form_send_messages', [
                'model' => $modelMessages,
            ]);
        }
    }

    /**
     * @param integer $id name in table Booking
     * @return bool
     */
    public function actionCancelBooking($id)
    {
        /** @var \app\modules\admin\models\search\Booking $modelBooking */
        $model = $this->findModel($id);
        /** @var \app\modules\admin\models\search\Orders $modelOrders */
        $modelOrders = new OrdersSearch();
        $modelOrders= $modelOrders::findOne(['id' => $model->order_id]);

        if($model->status == Status::PENDING){
            $startDate = Yii::$app->ticket_booked_buy->getDateByOrderId($model->order_id);
            $product = Yii::$app->ticket_booked_buy->getProductNameByOrderId($model->order_id);
            /** @var \app\models\Events $modelEvents */
            $modelEvents = Yii::$app->events->findModelByTitleAndDate($product, $startDate);
            $modelEvents->size_pending = $modelEvents->size_pending - $modelOrders->size;
            if($modelEvents->save(false)){
                if ($model->updateField('status', Status::CANCELED)) {
                    if ($modelOrders->updateField('status', Status::CANCELED)) {
                        $this->redirect(['manage-booking-detail', 'id' => $id]);
                    }
                }
            }else{
                return false;
            }
        }elseif ($model->status == Status::CONFIRMED){
            $startDate = Yii::$app->ticket_booked_buy->getDateByOrderId($model->order_id);
            $product = Yii::$app->ticket_booked_buy->getProductNameByOrderId($model->order_id);
            /** @var \app\models\Events $modelEvents */
            $modelEvents = Yii::$app->events->findModelByTitleAndDate($product, $startDate);
            $modelEvents->size_use = $modelEvents->size_use - $modelOrders->size;
            if($modelEvents->save(false)){
                if ($model->updateField('status', Status::CANCELED)) {
                    if ($modelOrders->updateField('status', Status::CANCELED)) {
                        $this->redirect(['manage-booking-detail', 'id' => $id]);
                    }
                }
            }else{
                return false;
            }
        }else{
            if ($model->updateField('status', Status::CANCELED)) {
                if ($modelOrders->updateField('status', Status::CANCELED)) {
                    $this->redirect(['manage-booking-detail', 'id' => $id]);
                }
            }else{
                return false;
            }
        }
    }

    /**
     * Send Confirmation massage to customer
     * @param integer $id booking id
     */
    public function actionSendConfirmation($id){
        /** @var \app\modules\admin\models\search\Booking $model */
        $model= $this->findModel($id);
        /** @var \app\models\Orders $modelOrder */
        $modelOrder = new Orders();
        $modelOrder = $modelOrder::findOne(['id' => $model->order_id]);

        //Send email.
        Yii::$app->customer_notifier->send(
            Yii::$app->customer->getCustomerEmailById($modelOrder->customer_id),
            Yii::t('app', 'Your ticket(s)'),
            'customer_confirmation_message_booking',
            [
                'username'  => Yii::$app->customer->getCustomerFullNameById($modelOrder->customer_id),
                'booking_id'  => $model->id,
            ],
            //Path to PDF file
            (string)(Yii::$app->params['tempPath'].'ticket(s)-order#' . $model->order_id) . '.pdf');
        $this->redirect(['manage-booking-detail', 'id' => $id]);
    }

    /**
     * Deletes an existing Booking model.
     * If deletion is successful, the browser will be redirected to the 'manage' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['manage']);
    }

    /**
     * Finds the Booking model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Booking the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BookingSearch::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

    }
}