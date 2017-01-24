<?php

namespace app\services;

use yii\base\Component;
use app\models\Question as QuestionModel;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class for implements logic with Question
 * @package app\services
 * @version 1.0
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class Question extends Component
{

    /**
     * Return model question
     * @param int $id ticket_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getQuestionByTicketIds($id){
        return QuestionModel::find()->where(['ticket_id' => $id])->all();
    }

    /**
     * Return array with question id and name
     * @param null|integer $id ticket_id
     * @return array
     */
    public function getQuestionIdsNames($id = null){
        if(!is_null($id)){
            $requirements = QuestionModel::find()->where(['ticket_id' => $id])->all();
        }else{
            /** @var array $requirements */
            $requirements = QuestionModel::find()->all();
        }
        /** @var array $options */
        $options = ArrayHelper::map($requirements, 'id', 'question');

        return $options;
    }

    /**
     * @param $id
     *
     * @return ActiveDataProvider
     */
    public function getQuestionByTourId($id)
    {
        return new ActiveDataProvider([
            'query' => QuestionModel::find()
                ->leftJoin('ticket t', 't.id = question.ticket_id')
                ->where(['t.tour_id' => $id])
        ]);
    }

    /**
     * @param $id
     *
     * @return array|bool
     */
    public function getTourIdById($id)
    {

        $query = new Query();
        $query->select(['t.tour_id'])
            ->from(['t' => 'ticket'])
            ->leftJoin(['q' => 'question'], 'q.ticket_id = t.id')
            ->where(['q.id'=>$id]);

        return $query->one();
    }
}