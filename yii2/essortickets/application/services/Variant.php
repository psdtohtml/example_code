<?php

namespace app\services;

use yii\base\Component;
use app\models\Variant as VariantModel;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use \app\helpers\ModelNamesProvider;
use \Yii;
use \yii\db\ActiveRecord;


/**
 * Class for implements logic with Variant
 * @package app\services
 * @version 1.0
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class Variant extends Component
{

    /**
     * Return array with variant id and name
     * @param null|integer $id tour_id
     * @return array
     */
    public function getVariantIdsNames($id = null){
        if(!is_null($id)){
            $requirements = VariantModel::find()->where(['tour_id' => $id])->all();
        }else{
            /** @var array $requirements */
            $requirements = VariantModel::find()->all();
        }
        /** @var array $options */
        $options = ArrayHelper::map($requirements, 'id', 'name');

        return $options;
    }

    /**
     * @param $id
     *
     * @return ActiveDataProvider
     */
    public function getVariantByTourId($id)
    {
        return new ActiveDataProvider([
            'query' => VariantModel::find()
                ->leftJoin('ticket t', 't.tour_id = variant.tour_id')
                ->where(['t.tour_id' => $id])
        ]);
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