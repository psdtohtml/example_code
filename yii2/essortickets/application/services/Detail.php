<?php

namespace app\services;

use app\helpers\ModelNamesProvider;
use yii\base\Component;
use app\models\Detail as DetailModel;
use app\models\TimeDetail as TimeDetailModel;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * Class for implements logic with Detail
 * @package app\services
 * @version 1.0
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class Detail extends Component
{

    /**
     * Finds the Detail model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $availabilityId
     * @param string $time
     * @return Detail the loaded models
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function findModels($time, $availabilityId)
    {
        if (($models = DetailModel::findAll(['time' => $time, 'availability_id' => $availabilityId])) !== null) {
            return $models;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Finds the Detail model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $time
     * @param integer $day
     * @param integer $availabilityId
     * @return Detail the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function findModel($time, $day, $availabilityId)
    {
        if (($model = DetailModel::findOne(['time' => $time, 'day' => $day, 'availability_id' => $availabilityId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Finds the Detail model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $time
     * @param string $name
     * @param integer $availabilityId
     * @return Detail the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function findModelVariation($time, $name, $availabilityId)
    {
        if (($model = DetailModel::findOne(['time' => $time, 'name' => $name, 'availability_id' => $availabilityId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
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
