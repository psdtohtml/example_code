<?php

namespace app\modules\admin\controllers;

use app\enums\Status;
use app\enums\YesNo;
use app\models\Coupon;
use app\models\Customer;
use app\models\Events;
use app\models\Orders;
use app\modules\admin\base\Controller;
use app\modules\admin\models\search\TicketBookedBuy;
use app\widgets\fullcalendar\models\Event;
use Yii;
use yii\base\DynamicModel;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Response;

/**
 * WidgetController.
 *
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class WidgetController extends Controller
{

    /**
     * This method render button or redirect to actionForm
     * @return string
     */
    public function actionButton(){
        if(Yii::$app->request->get()){
            Yii::$app->session->set('options', Yii::$app->request->get());
            if(Yii::$app->request->get('layout') == 0){
                return $this->renderAjax('partials/_button',[
                    'id' => Yii::$app->request->get('id', ''),
                    'color' =>  Yii::$app->request->get('color', ''),
                    'label' => Yii::$app->request->get('label', ''),
                ]);
            }else{
                $this->redirect(['tiers-select', 'id' => Yii::$app->request->get('id')]);
            }
        }

    }

    /**
     * This method render tiers-select step tiers-select
     * @param integer $id tour_id
     * @param integer|null $limit
     * @param integer|null $empty
     *
     * @return string|\yii\web\Response
     */
    public function actionTiersSelect($id, $limit = null, $empty = null)
    {
        /** @var \app\models\TicketBookedBuy $modelTicketBookedBuy */
        $modelTicketBookedBuy = new TicketBookedBuy();
        /** @var \app\models\Orders $modelOrders */
        $modelOrders = new Orders();
        /** @var \app\models\Customer $modelCustomer */
        $modelCustomer = new Customer();
        $variants = Yii::$app->variant->getVariantIdsNames(base64_decode($id));
        $tiers = Yii::$app->ticket->getTiersIdsNames(base64_decode($id));


        $fieldsForDynamic =[];
        foreach ($tiers as $key => $value){
            array_push($fieldsForDynamic, base64_encode($value));
        }

        $modelDynamic = new DynamicModel($fieldsForDynamic);
        $modelDynamic->addRule($fieldsForDynamic, 'integer', ['message' =>'field must be an integer.'])
            ->validate();
        if(Yii::$app->request->post()){
            $check = 0;
            foreach (Yii::$app->request->post()['DynamicModel'] as $key => $value){
                $check = $check + $value;
            }
            if($check == 0){
                return $this->redirect(['tiers-select', 'id' => $id, '', 'empty' => true]);
            }
            Yii::$app->session->set('tiers-select',Yii::$app->request->post());
            return $this->redirect(['extras-select', 'id' => $id]);
        }
        return $this->renderAjax('partials/_form_tiers_select', [
            'modelTicketBookedBuy' => $modelTicketBookedBuy,
            'modelOrders'          => $modelOrders,
            'modelCustomer'        => $modelCustomer,
            'variants'             => $variants,
            'tiers'                => $tiers,
            'modelDynamic'         => $modelDynamic,
            'limit'                => $limit,
            'empty'                => $empty,
        ]);

    }

    /**
     * This method render second step
     * @param integer $id tour_id
     *
     * @return string
     */
    public function actionExtrasSelect($id)
    {
        /**
         * if tiers-select null return to tiers-select (actionTiersSelect)
         */
        if(($firstStepPost=Yii::$app->session->get('tiers-select')) == null){
            $this->redirect(['tiers-select', 'id' => $id]);
        }
        /**
         * get ticket ids
         * @var $result
         */
        $result=[];
        foreach ($firstStepPost['DynamicModel'] as $key => $value){
            if($value !== "0"){
                array_push($result, Yii::$app->ticket->getTicketIdByTiersNameAndTourId(base64_decode($key), base64_decode($id))) ;
            }
        }
        /**
         * get extra id => name
         * @var $extras
         */
        $extras=[];
        foreach ($result as $value){
            $extras= ArrayHelper::merge($extras, Yii::$app->extras->getExtrasIdsNames($value));
        }
        if(empty($extras)){
            $this->redirect(['question-answer', 'id' => $id]);
        }
        $fieldsForDynamic =[];
        foreach ($extras as $key => $value){
            array_push($fieldsForDynamic, base64_encode($value));
        }
        $modelDynamic = new DynamicModel($fieldsForDynamic);
        $modelDynamic->addRule($fieldsForDynamic, 'integer', ['message' =>'field must be an integer.'])
            ->validate();
        return $this->renderAjax('partials/_form_extras_select', [
            'model'   => $modelDynamic,
            'extras'  => $extras,
            'id' => $id,
        ]);

    }

    public function actionQuestionAnswer($id){
        /**
         * get question models
         */
        $modelsQuestion = [];
        foreach (Yii::$app->session->get('tiers-select')['DynamicModel'] as $key => $value){
            $modelsQuestion = ArrayHelper::merge(
                $modelsQuestion,
                Yii::$app->question->getQuestionByTicketIds(Yii::$app->ticket->getTicketIdByTiersNameAndTourId(base64_decode($key), base64_decode($id)))
            );
        }
        /**
         * data processing
         */
        $questions = [];
        foreach ($modelsQuestion as $item){
            $questions = ArrayHelper::merge(
                $questions,
                [$item['id'] =>['ticket_id' => $item['ticket_id'],'question' => $item['question']]]
            );
        }
        var_dump($modelsQuestion, $questions);die;
    }

    /**
     * This method render three step
     * @param integer $id tour_id
     *
     * @return string
     */
    public function actionThreeStep($id){

        /** @var \app\models\Events $models */
        $models = Events::find()->where(['title' => Yii::$app->tour->getProductNameByOrderId(base64_decode($id))])->all();
        /** @var array $events */
        $events =[];
        foreach ($models as $model)
        {
            /** @var  \app\widgets\fullcalendar\models\Event $event */
            $event = new Event();
            /** @var  \app\models\Events $model */
            $event->id = $model->id;
            $event->title = $model->title;
            $event->description = $model->description;
            $event->start = $model->start_date . ' ' . $model->start_time;
            $event->end = $model->end_date . ' ' . $model->end_time;
            $event->ticket =  $model->size_pending + $model->size_use . '/' . $model->size_all;
            $event->url = Url::to(['/admin/widget/four-step', 'id' =>$id, 'event_id' => $model->id]);

            if($model->dayoff == YesNo::YES){
                $event->className = 'clickable';
                $event->backgroundColor = 'red';
                $event->url = false;
            }

            $events[] = $event;
        }
        if(isset(Yii::$app->request->post()['DynamicModel'])){
            Yii::$app->session->set('ticket_booked_buy_extra',Yii::$app->request->post()['DynamicModel']);
            return $this->renderAjax('partials/_form_three_step', [
                'events' => $events,
            ]);
        }else{
            Yii::$app->session->set('ticket_booked_buy_extra',[0=>0]);
            return $this->renderAjax('partials/_form_three_step', [
                'events' => $events,
            ]);
        }

    }

    /**
     * This method render four step or redirect to step three
     * @param integer $id tour_id
     *
     * @return string|\yii\web\Response
     */
    public function actionFourStep($id){

        if(Yii::$app->request->get('event_id') !== null){
            /** @var \app\models\Events $modelEvent */
            $modelEvent = Yii::$app->events->getEventById(Yii::$app->request->get('event_id'));
            /**
             * Save selected event to session
             */
            Yii::$app->session->set('event',$modelEvent);
            /**
             * check free tickets
             */
            $limit = $modelEvent->size_use + $modelEvent->size_pending + array_sum(Yii::$app->session->get('tiers-select')['DynamicModel']);

            if($modelEvent->size_all < $limit){
                $availableLimit = $modelEvent->size_all - ($modelEvent->size_use + $modelEvent->size_pending);
                return $this->redirect(['tiers-select', 'id' => $id, 'limit' => $availableLimit]);
            }

            /** @var string $startDayOfWeek day of week*/
            $startDayOfWeek = date('l', strtotime($modelEvent->start_date));
            /** @var string $startDate date yyyy-mm-dd */
            $startDate = $modelEvent->start_date;
            /** @var string $startTime time hh:mm:ss */
            $startTime = $modelEvent->start_time;

            $checkEmptyExtra = false;
            foreach (Yii::$app->session->get('ticket_booked_buy_extra') as $key => $value){
                if($value != 0){
                    $checkEmptyExtra = true;
                }
            }

            return $this->renderAjax('partials/_form_four_step', [
                'startDayOfWeek'  => $startDayOfWeek,
                'startDate'       => $startDate,
                'startTime'       => $startTime,
                'tickets'         => Yii::$app->session->get('tiers-select')['DynamicModel'],
                'extras'          => Yii::$app->session->get('ticket_booked_buy_extra'),
                'id'              => base64_decode($id),
                'checkEmptyExtra' => $checkEmptyExtra,
                'tax'             => Yii::$app->tax->getTaxByTourId(base64_decode($id)),
                'cumulative'      => Yii::$app->tax->getCumulativeByTourId(base64_decode($id)),
                'included'        => Yii::$app->tax->getIncludedByTourId(base64_decode($id)),
            ]);
        }else{
            $this->redirect(['three-step', 'id' => $id]);
        }
    }

    /**
     * This method get customers data
     * @return string|\yii\web\Response
     */
    public function actionFiveStep($price){
        /**
         * Save total price to session
         */
        Yii::$app->session->set('price', $price);
        /** @var \app\models\Customer $modelCustomer */
        $modelCustomer = new Customer();
        /** @var \jisoft\sypexgeo\Sypexgeo $geo */
        $geo = new \jisoft\sypexgeo\Sypexgeo();
        $geo->get();

        if ($modelCustomer->load(Yii::$app->request->post()) && $modelCustomer->validate()) {
            /**
             * Save to session customer info
             */
            Yii::$app->session->set('customer', Yii::$app->request->post());
            return $this->redirect(['six-step']);
        }else{
            return $this->renderAjax('partials/_form_five_step', [
                'model'                 => $modelCustomer,
                'addressVerification'   => base64_decode(Yii::$app->session->get('options')['address']),
                'subscribed'            => base64_decode(Yii::$app->session->get('options')['subscribed']),
                'ip'                    => $geo->ip,
            ]);
        }
    }

    /**
     * @return string
     */
    public function actionSixStep(){
        if(Yii::$app->request->post()){
            /**
             * @param $amount
             * @param $currency
             * @param $source
             * @param $description
             * @param $idempotency_key
             */
            $charge = Yii::$app->stripe_component->checkout(
                Yii::$app->stripe_component->getAmountForStripe(Yii::$app->session->get('price')),
                Yii::$app->tour->getCurrencyForWidgetByTourId(base64_decode(Yii::$app->session->get('options')['id'])),
                Yii::$app->request->post()['stripeToken'],
                "Charge for charlotte.taylor@example.com",
                "OR-" . rand(1,99999)
            );
            if(isset($charge->id)){
                Yii::$app->session->set('ch', $charge->id);
                $this->redirect('save');
            }else{
                $this->redirect(['tiers-select', 'id' => Yii::$app->session->get('options')['id']]);
            }
        }else{
            return $this->renderAjax('partials/_form_six_step', [
                'amount'   => Yii::$app->stripe_component->getAmountForStripe(Yii::$app->session->get('price')),
                'currency' => Yii::$app->tour->getCurrencyForWidgetByTourId(base64_decode(Yii::$app->session->get('options')['id'])),
                'logo'     => Yii::$app->tour->getLogoUrlByTourId(base64_decode(Yii::$app->session->get('options')['id'])),
                'email'    => Yii::$app->session->get('customer')['Customer']['email'],
                'product'  => Yii::$app->tour->getProductNameProductId(base64_decode(Yii::$app->session->get('options')['id'])),
            ]);
        }
    }

    public function actionSave(){
        /**
         * Check all data not null
         */
        if(
            Yii::$app->session->get('tiers-select') != null
            && Yii::$app->session->get('ticket_booked_buy_extra') != null
            && Yii::$app->session->get('event') != null
            && Yii::$app->session->get('customer') != null
            && Yii::$app->session->get('ch') != null
        ){
            /** @var \app\models\Customer $customerModel */
            $customerModel = new Customer();
            /** @var \app\models\Coupon $couponModel */
            $couponModel = new Coupon();

            /** @var  $connection */
            $connection = Yii::$app->db;
            /** @var  $transaction */
            $transaction = $connection->beginTransaction();
            /**
             * save customer model
             */
            if($customerModel->load(Yii::$app->session->get('customer')) && $customerModel->save()){
                /** @var \app\models\Orders $ordersModel */
                $ordersModel = new Orders();
                $ordersModel->customer_id = $customerModel->id;
                $ordersModel->tax_id = Yii::$app->tax->getTaxIdByTourId(base64_decode(Yii::$app->session->get('options')['id']));
                $ordersModel->status = Status::CONFIRMED;
                $ordersModel->valid_from = date('Y-m-d H:i:s');
                $ordersModel->valid_to = date('Y-m-d H:i:s');
                $ordersModel->size = array_sum(Yii::$app->session->get('tiers-select')['DynamicModel']);
                $ordersModel->total_price = Yii::$app->session->get('price');
                $ordersModel->coupon_code = Yii::$app->session->get('tiers-select')['Orders']['coupon_code'];
                $ordersModel->created_at = time();
                $ordersModel->confirm = YesNo::YES;
                $ordersModel->ch = Yii::$app->session->get('ch');
                $ordersModel->ch_status = YesNo::YES;

                if($ordersModel->save(false)){
                    /**
                     * update coupon model if used coupon
                     */
                    if(Yii::$app->session->get('tiers-select')['Orders']['coupon_code'] != ''){
                        $couponModel = $couponModel::findOne(['code' => Yii::$app->session->get('tiers-select')['Orders']['coupon_code']]);
                        $couponModel->updateField('used', ($couponModel->used + 1));
                    }
                    $arr = Yii::$app->session->get('tiers-select')['DynamicModel'];
                    foreach ($arr as $key => $value){
                        if($value != 0){
                            /** @var \app\models\TicketBookedBuy $tbbModel */
                            $tbbModel = new \app\models\TicketBookedBuy();
                            $tbbModel->order_id = $ordersModel->id;
                            $tbbModel->ticket_id = Yii::$app->ticket->getTicketIdByTiersNameAndTourId(base64_decode($key), base64_decode(Yii::$app->session->get('options')['id']));
                            $tbbModel->variant_id = Yii::$app->session->get('tiers-select')['TicketBookedBuy']['variant_id'];
                            $tbbModel->event_id = Yii::$app->session->get('event')->id;
                            $tbbModel->start_date = date('Y-m-d');
                            $tbbModel->size_amount = $value;
                            if(!$tbbModel->save(false)){
                                $transaction->rollBack();
                                return Yii::error('Event not created!');
                            }
                        }
                    }
                }
            }
            $transaction->commit();
        }else{
            $this->redirect(['tiers-select', 'id' => base64_decode(Yii::$app->session->get('options')['id'])]);
        }
    }
}