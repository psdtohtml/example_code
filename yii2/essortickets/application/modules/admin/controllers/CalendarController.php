<?php

namespace app\modules\admin\controllers;

use app\enums\YesNo;
use app\modules\admin\models\search\TicketBookedBuy;
use app\widgets\fullcalendar\models\Event;
use Yii;
use app\models\Events;
use yii\data\ActiveDataProvider;
use \app\modules\admin\base\Controller;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CalendarController implements the CRUD actions for Events model.
 */
class CalendarController extends Controller
{

    /**
     * This method send mails to buyers
     * @param $id event id
     * @return bool
     */
    public function actionBroadcastMessage($id)
    {
    /** @var \app\services\Events $customers */
     $customers = Yii::$app->events->getNameEmailsByEventId($id);

     foreach ($customers as $customer){
         Yii::$app->user_notifier->send(
             $customer['email'],
             'Something happened with the tour',
             'customer_broadcast_message_notify',
             [
                 'username'  => $customer['fullName'],
                 'tourName'  => $customer['product'],
             ]);
     }
        return $this->redirect(['view','id' => $id]);
    }

    /**
     * Lists all Events models.
     * @return mixed
     */
    public function actionManageCalendar()
    {
        /** @var array $post */
        $post = Yii::$app->request->post();

        if($post && $post !== null){
            /** @var \app\models\Events $models */
            $models = Events::find()->where(['title' => $post['id']])->all();
        }else{
            /** @var \app\models\Events $models */
            $models = Events::find()->all();
        }

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
            $event->url = Url::to(['/admin/calendar/view', 'id' => $model->id]);
            if(!Yii::$app->user->isGuest){
                    $event->url = Url::to(['/admin/calendar/view', 'id' => $model->id]);
                }else{
                    $event->url = Url::to(['/admin/calendar/view-guest', 'id' => $model->id]);
                }

            if($model->dayoff == YesNo::YES){
                $event->className = 'clickable';
                $event->backgroundColor = 'red';
            }

            $events[] = $event;
        }
        return $this->render('manageCalendar', [
            'events' => $events,
            'tours'  => Yii::$app->tour->getTourNames(),
        ]);
    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     */
    public function actionView($id)
    {
        /** @var \app\models\Events $model */
        $model = $this->findModel($id);
        /** @var \app\modules\admin\models\search\TicketBookedBuy $modelTicketBookedBuy */
        $modelTicketBookedBuy = new TicketBookedBuy();

        /** @var array $post */
        $post = Yii::$app->request->post();

        if ($model->load($post) && $model->save()) {
            return $this->redirect(['view','id' => $id]);
        }elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('manageEvent', [
                'model' => $model,
                'products' => Yii::$app->tour->getTourNames(),
                'users'    => Yii::$app->user_component->getUsers(),
                'modelTicketBookedBuy' => $modelTicketBookedBuy,
                'dataProviderTicketBookedBuy' => $modelTicketBookedBuy->search(Yii::$app->request->get(), '', $model->id),
            ]);
        } else {
            return $this->render('manageEvent', [
                'model' => $model,
                'products' => Yii::$app->tour->getTourNames(),
                'users'    => Yii::$app->user_component->getUsers(),
                'modelTicketBookedBuy' => $modelTicketBookedBuy,
                'dataProviderTicketBookedBuy' => $modelTicketBookedBuy->search(Yii::$app->request->get(), '', $model->id)
            ]);
        }
    }

    public function actionViewGuest($id)
    {
        /** @var \app\models\Events $model */
        $model = $this->findModel($id);
        /** @var \app\modules\admin\models\search\TicketBookedBuy $modelTicketBookedBuy */
        $modelTicketBookedBuy = new TicketBookedBuy();

        /** @var array $post */
        $post = Yii::$app->request->post();

        if ($model->load($post) && $model->save()) {
            return $this->redirect(['manage-calendar']);
        }elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('manageEventGuest', [
                'model' => $model,
                'products' => Yii::$app->tour->getTourNames(),
                'users'    => Yii::$app->user_component->getUsers(),
                'modelTicketBookedBuy' => $modelTicketBookedBuy,
                'dataProviderTicketBookedBuy' => $modelTicketBookedBuy->search(Yii::$app->request->get(), '', $model->id),
            ]);
        } else {
            return $this->render('manageEventGuest', [
                'model' => $model,
                'products' => Yii::$app->tour->getTourNames(),
                'users'    => Yii::$app->user_component->getUsers(),
                'modelTicketBookedBuy' => $modelTicketBookedBuy,
                'dataProviderTicketBookedBuy' => $modelTicketBookedBuy->search(Yii::$app->request->get(), '', $model->id)
            ]);
        }
    }

    /**
     * Creates a new Events model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param string $date example "2016-11-11"
     * @return mixed
     */
    public function actionCreate($date)
    {
        /** @var \app\models\Events $model */
        $model = new Events();
        $model->start_date = base64_decode($date);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['manage-calendar']);
        }elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('partials/_form', [
                'model'    => $model,
                'products' => Yii::$app->tour->getTourNames(),
                'users'    => Yii::$app->user_component->getUsers(),
            ]);
        } else {
            return $this->render('partials/_form', [
                'model' => $model,
                'products' => Yii::$app->tour->getTourNames(),
                'users'    => Yii::$app->user_component->getUsers(),
            ]);
        }
    }

    /**
     * Updates an existing Events model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        /** @var \app\models\Events $model */
        $model = $this->findModel($id);

        /** @var array $post */
        $post = Yii::$app->request->post();

            if ($model->load($post) && $model->save()) {
                return $this->redirect(['view','id' => $id]);
            }elseif (Yii::$app->request->isAjax) {
                return $this->renderAjax('partials/_form', [
                    'model' => $model,
                    'products' => Yii::$app->tour->getTourNames(),
                    'users'    => Yii::$app->user_component->getUsers(),
                ]);
            } else {
                return $this->render('partials/_form', [
                    'model' => $model,
                    'products' => Yii::$app->tour->getTourNames(),
                    'users'    => Yii::$app->user_component->getUsers(),
                ]);
            }
    }

    /**
     * Deletes an existing Events model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['manage-calendar']);
    }

    /**
     * Finds the Events model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Events the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Events::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
