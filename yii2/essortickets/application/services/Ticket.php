<?php

namespace app\services;

use app\base\ActiveRecord;
use app\helpers\ModelNamesProvider;
use Yii;
use yii\base\Component;
use app\models\Ticket as TicketModel;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * Class for implements logic with Ticket
 * @package app\services
 * @version 1.0
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class Ticket extends Component
{
    /**
     * @param $name
     * @param $id
     * @return int|null
     */
    public function getTicketPriceByTiersNameAndTourId($name, $id)
    {
        if (($model = TicketModel::findOne(['name' => $name, 'tour_id' =>$id])) !== null) {
            return $model->price;
        } else {
            return null;
        }
    }
    /**
     * @param $name
     * @param $id
     * @return int|null
     */
    public function getTicketIdByTiersNameAndTourId($name, $id)
    {
        if (($model = TicketModel::findOne(['name' => $name, 'tour_id' =>$id])) !== null) {
            return $model->id;
        } else {
            return null;
        }
    }
    /**
     * Return array with ticket(tiers) id and name
     * @param null|integer $id tour_id
     * @return array
     */
    public function getTiersIdsNames($id){
        $requirements = TicketModel::find()->where(['tour_id' => $id])->all();
        /** @var array $options */
        $tiers = ArrayHelper::map($requirements, 'id', 'name');

        return $tiers;
    }

    /**
     * Finds the Ticket models based on id.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Events the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function findModelById($id)
    {
        if (($model = TicketModel::findOne(['id' => $id])) !== null) {
            return $model;
        } else {
            return null;
        }
    }

    /**
     * Return array with ticket(tier) id and name
     * @param null|integer $id tour_id
     * @return array
     */
    public function getTicketIdsNames($id = null){
        if(!is_null($id)){
            $requirements = TicketModel::find()->where(['tour_id' => $id])->all();
        }else{
            /** @var array $requirements */
            $requirements = TicketModel::find()->all();
        }
        /** @var array $options */
        $options = ArrayHelper::map($requirements, 'id', 'name');

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

    /**
     * Finds the ticket model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function findModel($id)
    {
        if (($model = TicketModel::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}