<?php

namespace app\services;

use Yii;
use yii\base\Component;
use app\models\Events as EventsModel;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * Class for implements logic with Events
 * @package app\services
 * @version 1.0
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class Events extends Component
{

    /**
     * Finds the Extras model based on id.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Extras the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function getEventById($id)
    {
        if (($models = \app\models\Events::findOne(['id' => $id])) !== null) {
            return $models;
        } else {
            return null;
        }
    }

    /**
     * This method return array customers (fullName => email)
     * @param $id event id
     * @return array
     */
    public function getNameEmailsByEventId($id)
    {
        $query = new Query();
        $query->select([
            'fullName' => 'CONCAT(c.name,\' \', c.last_name)',
            'email'    => 'c.email',
            'product'  => 'tour.name',
        ])
            ->from(['c' => 'customer'])
            ->leftJoin('orders o', 'o.customer_id = c.id')
            ->leftJoin('ticket_booked_buy tbb', 'tbb.order_id = o.id')
            ->leftJoin('ticket t', 'tbb.ticket_id = t.id')
            ->leftJoin('tour tour', 'tour.id = t.tour_id')
            ->where(['tbb.event_id' => $id]);

        return $query->all();


    }

    /**
     * Finds the Events models based on title and start_date.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $title
     * @param string $startTime
     * @return Events the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function findModelsByTitleAndDate($title, $startTime)
    {
        if (($models = EventsModel::findAll(['title' => $title, 'start_time' => $startTime])) !== null) {
            return $models;
        } else {
            return null;
        }
    }

    /**
     * Finds the Events model based on title and start_date.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $title
     * @param string $startDate
     * @return Events the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function findModelByTitleAndDate($title, $startDate)
    {
        if (($model = EventsModel::findOne(['title' => $title, 'start_date' => $startDate])) !== null) {
            return $model;
        } else {
            return null;
        }
    }

    /**
     * Return array with tour id and date
     * @param string $tourName
     * @return array
     */
    public function getStartDatesByTourName($tourName)
    {
        /** @var array $requirements */
        $requirements = EventsModel::find()
            ->where(['title' => $tourName])
            ->andWhere('start_date >= CURDATE()')
            ->andWhere('size_pending + size_use < size_all')
            ->all();
        /** @var array $options */
        $options = ArrayHelper::map($requirements, 'start_date', 'start_date');

        return $options;
    }

    /**
     * @param $tourModel
     * @return bool
     */
    public function getEventCreator($tourModel)
    {
        /** @var integer $recurring */
        $recurring = $tourModel['recurring'];

        if($recurring) {
            switch ($recurring) {
                case 1:
                    $method = $this->createEventOnce($tourModel);
                    break;
                case 2:
                    $method = $this->createEventsDaily($tourModel);
                    break;
                case 3:
                    $method = $this->createEventsWeekday($tourModel);
                    break;
                case 4:
                    $method = $this->createEventsWeekend($tourModel);
                    break;
                default:
                    $method = false;
            }
            return $method;
        }
        return false;
    }

    /**
     * @param \app\models\Tour $tourModel
     * @return bool
     */
    public function createEventOnce($tourModel)
    {
        /** @var  $connection */
        $connection = Yii::$app->db;
        /** @var  $transaction */
        $transaction = $connection->beginTransaction();

        /** @var \app\models\Events $modelEvent */
        $modelEvent = new EventsModel();
        $modelEvent->title      = $tourModel->name;
        $modelEvent->start_date = $tourModel->start_tour_date;
        $modelEvent->start_time = $tourModel->start_tour_time;
        $modelEvent->end_date   = $tourModel->end_tour_date;
        $modelEvent->end_time   = $tourModel->end_tour_time;
        $modelEvent->guide_id   = $tourModel->manager_id;
        $modelEvent->size_all   = $tourModel->ticket_available;

        if(!$modelEvent->save()){
            $transaction->rollBack();
            return Yii::error('Event not created!');
        }
        $transaction->commit();
        return true;
    }

    /**
     * @param \app\models\Tour $tourModel
     * @return bool
     */
    public function createEventsDaily($tourModel)
    {
        /** @var  $connection */
        $connection = Yii::$app->db;
        /** @var  $transaction */
        $transaction = $connection->beginTransaction();

        $dateStart = $tourModel->start_tour_date;
        while($dateStart <= $tourModel->end_tour_date){
            /** @var \app\models\Events $modelEvent */
            $modelEvent = new EventsModel();
            $modelEvent->title      = $tourModel->name;
            $modelEvent->start_date = $dateStart;
            $modelEvent->start_time = $tourModel->start_tour_time;
            $modelEvent->end_time   = $tourModel->end_tour_time;
            $modelEvent->guide_id   = $tourModel->manager_id;
            $modelEvent->size_all   = $tourModel->ticket_available;

            if(!$modelEvent->save()){
                $transaction->rollBack();
                return Yii::error('Event not created!');
            }

            $date = date_create($dateStart);
            date_add($date,date_interval_create_from_date_string("1 days"));
            $dateStart = date_format($date,"Y-m-d");
        }

        $transaction->commit();
        return true;

    }

    /**
     * @param \app\models\Tour $tourModel
     * @return bool
     */
    public function createEventsWeekday($tourModel)
    {
        /** @var  $connection */
        $connection = Yii::$app->db;
        /** @var  $transaction */
        $transaction = $connection->beginTransaction();

        $dateStart = $tourModel->start_tour_date;
        while($dateStart <= $tourModel->end_tour_date){

            $dateStart = date('Y-m-d', strtotime('Monday', strtotime($dateStart)));
            $dateEnd = date('Y-m-d', strtotime('Friday', strtotime($dateStart)));

            while ($dateStart <= $dateEnd){
                /** @var \app\models\Events $modelEvent */
                $modelEvent = new EventsModel();
                $modelEvent->title      = $tourModel->name;
                $modelEvent->start_date = $dateStart;
                $modelEvent->start_time = $tourModel->start_tour_time;
                $modelEvent->end_time   = $tourModel->end_tour_time;
                $modelEvent->guide_id   = $tourModel->manager_id;
                $modelEvent->size_all   = $tourModel->ticket_available;

                if(!$modelEvent->save()){
                    $transaction->rollBack();
                    return Yii::error('Event not created!');
                }
                $dateStart = date('Y-m-d', strtotime('+1 day', strtotime($dateStart)));
            }
            $dateStart = date('Y-m-d', strtotime('next Monday', strtotime($dateStart)));
        }
        $transaction->commit();
        return true;

    }

    /**
     * @param \app\models\Tour $tourModel
     * @return bool
     */
    public function createEventsWeekend($tourModel)
    {
        /** @var  $connection */
        $connection = Yii::$app->db;
        /** @var  $transaction */
        $transaction = $connection->beginTransaction();

        $dateStart = $tourModel->start_tour_date;
        while($dateStart <= $tourModel->end_tour_date){

            $dateStart = date('Y-m-d', strtotime('Saturday', strtotime($dateStart)));
            $dateEnd = date('Y-m-d', strtotime('Sunday', strtotime($dateStart)));

            while ($dateStart <= $dateEnd){
                /** @var \app\models\Events $modelEvent */
                $modelEvent = new EventsModel();
                $modelEvent->title      = $tourModel->name;
                $modelEvent->start_date = $dateStart;
                $modelEvent->start_time = $tourModel->start_tour_time;
                $modelEvent->end_time   = $tourModel->end_tour_time;
                $modelEvent->guide_id   = $tourModel->manager_id;
                $modelEvent->size_all   = $tourModel->ticket_available;

                if(!$modelEvent->save()){
                    $transaction->rollBack();
                    return Yii::error('Event not created!');
                }
                $dateStart = date('Y-m-d', strtotime('+1 day', strtotime($dateStart)));
            }
            $dateStart = date('Y-m-d', strtotime('next Saturday', strtotime($dateStart)));
        }
        $transaction->commit();
        return true;

    }

}