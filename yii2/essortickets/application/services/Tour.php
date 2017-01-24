<?php

namespace app\services;

use app\enums\Currency;
use yii\base\Component;
use app\models\Tour as TourModel;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use \app\helpers\ModelNamesProvider;
use \Yii;
use \yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;

/**
 * Class for implements logic with Tour
 * @package app\services
 * @version 1.0
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class Tour extends Component
{
    /**
     * @param $id
     * @return string
     */
    public function getLogoUrlByTourId($id){
        if (($model = TourModel::findOne($id)) !== null) {
            if(isset($model->logo)){
                return Yii::$app->request->baseUrl . '/uploads/' . $model->logo;
            }else{
                return 'https://stripe.com/img/documentation/checkout/marketplace.png';
            }
        } else {
            return '';
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getConfirmationMailByOrderId($id)
    {
        $query = new Query();
        $query->select('t.confirmation_mail')
            ->from(['t' => 'tour'])
            ->leftJoin('ticket tk', 'tk.tour_id = t.id')
            ->leftJoin('ticket_booked_buy tbb', 'tbb.ticket_id =tk.id')
            ->leftJoin('orders o', 'o.id = tbb.order_id')
            ->where(['o.id' => $id]);
        return $query->one()["confirmation_mail"];
    }

    /**
     * @param $id
     * @return string
     */
    public function getCurrencyForWidgetByTourId($id)
    {
        $query = new Query();
        $query->select('t.currency')
            ->from(['t' => 'tour'])
            ->where(['t.id' => $id]);
        return $currency = strtolower(Currency::getTitleByType($query->one()["currency"]));
    }

    /**
     * @param $id
     * @return string
     */
    public function getCurrencyForWidgetByOrderId($id)
    {
        $query = new Query();
        $query->select('t.currency')
            ->from(['t' => 'tour'])
            ->leftJoin('ticket tk', 'tk.tour_id = t.id')
            ->leftJoin('ticket_booked_buy tbb', 'tbb.ticket_id =tk.id')
            ->leftJoin('orders o', 'o.id = tbb.order_id')
            ->where(['o.id' => $id]);
        return $currency = strtolower(Currency::getTitleByType($query->one()["currency"]));
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getCurrencyByOrderId($id)
    {
        $query = new Query();
        $query->select('t.currency')
            ->from(['t' => 'tour'])
            ->leftJoin('ticket tk', 'tk.tour_id = t.id')
            ->leftJoin('ticket_booked_buy tbb', 'tbb.ticket_id =tk.id')
            ->leftJoin('orders o', 'o.id = tbb.order_id')
            ->where(['o.id' => $id]);
        return $query->one()["currency"];
    }

    /**
     * This method returns the number of tickets available based on events
     * @param integer $id tour_id
     * @param string $startDate Events
     * @return int
     * @throws NotFoundHttpException
     */
    public function getTicketAvailableByTourId($id, $startDate)
    {
        if (($model = TourModel::findOne($id)) !== null) {
            /** @var \app\models\Events $modelEvents */
            $modelEvents = Yii::$app->events->findModelByTitleAndDate($model->name, $startDate);
            /** @var integer $available */
            $available = $modelEvents->size_all - ($modelEvents->size_pending + $modelEvents->size_use);
            if($available >0){
                if($available > $model->customer_ticket_limit){
                    return $model->customer_ticket_limit;
                }elseif ($available < $model->customer_ticket_limit){
                    return $available;
                }else{
                    return 0;
                }
            }else {
                return 0;
            }
        } else {
            throw new NotFoundHttpException('Tour not exist!');
        }

    }

    /**
     * @param integer $id
     * @return array|bool
     */
    public function getProductNameProductId($id)
    {
        $query = new Query();
        $query->select('t.name')
            ->from(['t' => 'tour'])
            ->where(['t.id' => $id]);
        return $query->one()['name'];
    }

    /**
     * @param $id
     * @return array|bool
     */
    public function getProductNameByOrderId($id)
    {
        $query = new Query();
        $query->select('t.name')
            ->from(['t' => 'tour'])
            ->leftJoin('ticket tk', 'tk.tour_id = t.id')
            ->leftJoin('ticket_booked_buy tbb', 'tbb.ticket_id =tk.id')
            ->leftJoin('orders o', 'o.id = tbb.order_id')
            ->where(['o.id' => $id]);
        return $query->one()["name"];
    }

    /**
     * Return array with tour id and name
     * @return array
     */
    public function getTourIdsNames()
    {
        /** @var array $requirements */
        $requirements = TourModel::find()->all();
        /** @var array $options */
        $options = ArrayHelper::map($requirements, 'id', 'name');

        return $options;
    }

    /**
     * Return array with tour id name => name
     * @return array
     */
    public function getTourNames()
    {
        /** @var array $requirements */
        $requirements = TourModel::find()->all();
        /** @var array $options */
        $options = ArrayHelper::map($requirements, 'name', 'name');

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
