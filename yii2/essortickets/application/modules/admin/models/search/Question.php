<?php

namespace app\modules\admin\models\search;

use \Yii;
use \yii\base\Model;
use \yii\data\Sort;
use \yii\data\Pagination;
use \yii\db\Query;

use \app\base\ArrayDataProvider;
use \app\models\Question as QuestionModel;
use yii\helpers\ArrayHelper;

/**
 * Question represents the model behind the search form about `\app\models\Question`.
 */
class Question extends QuestionModel
{
    use \app\models\traits\ColumnFilter {
        getSuggestions as traitGetSuggestions;
    }

    /** @var string $answer */
    public $answer;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'ticket_id', 'input_type', 'optional', 'aggregate'], 'integer'],
            [['question', 'answer', 'option'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'answer'  => Yii::t('app', 'Answer'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * @param integer $id order_id
     * @param array $params for search.
     * @return ArrayDataProvider
     */
    public function search(array $params, $id = null)
    {

        $this->sort = $this->getSortAttributes($params);
        $this->setQuery($id);
        if ($this->load($params) && $this->validate()) {
            $this->applyFilters();

        }

        $countQuery = clone $this->query;

        $pagination = new Pagination(['totalCount' => $countQuery->count(), 'defaultPageSize' => 10]);
        $this->query->limit($pagination->limit)->offset($pagination->offset);

        $this->query->each();
        $command = $this->query->createCommand();

        return new ArrayDataProvider([
            'allModels'  => $command->queryAll(),
            'key'        => 'id',
            'pagination' => $pagination,
            'sort'       => $this->sort,
        ]);
    }

    /**
     * Set Query.
     * @param integer|null $id  order_id
     */
    protected function setQuery($id)
    {
        $this->query = new Query();
        $this->query->select([
            'id'           => 'q.id',
            'question'     => 'q.question',
            'answer'       => 'a.answer',
        ])
            ->from(['q' => static::tableName()])
            ->leftJoin('ticket_booked_buy tbb', 'tbb.ticket_id = q.ticket_id')
            ->leftJoin('orders o', 'o.id = tbb.order_id')
            ->leftJoin('answer a', 'a.question_id = q.id')
            ->where(['o.id' => $id, 'a.order_id' => $id])
            ->groupBy(['q.question']);

        if ($this->sort instanceof Sort) {
            $safe = array_flip($this->safeAttributes());
            foreach ($this->sort->getOrders() as $attribute => $order) {
                $temp = explode('.', $attribute);

                if (isset($temp[1])) {
                    $attribute = $temp[1];
                }

                if (isset($safe[$attribute]) || $attribute == 'title') {
                    $orderer = true;
                    $orderBy = 'ASC';
                    if ($order == 3) {
                        $orderBy = 'DESC';
                    }

                    $this->query->addOrderBy("{$temp[0]}.{$attribute} {$orderBy}");
                }
            }
            /** Default orders */
            if (empty($orderer)) {
                $this->query->addOrderBy("`q`.id ASC");
            }
        }
    }

    /**
     * Apply filters.
     */
    public function applyFilters()
    {
        $this->query
            ->andFilterWhere([
                'q.id'       => $this->id,
                'q.question' => $this->question,
            ])
            ->andFilterWhere(['like', 'a.answer', $this->answer]);
    }

    /**
     * @param array $params for search.
     * @return Sort
     */
    public function getSortAttributes(array $params)
    {
        $sort = new Sort();
        $sort->attributes = [
            'id'          => [
                'asc'  => ['q.id' => SORT_ASC],
                'desc' => ['q.id' => SORT_DESC],
            ],
            'question'        => [
                'asc'  => ['q.question' => SORT_ASC],
                'desc' => ['q.question' => SORT_DESC],
            ],
            'answer'        => [
                'asc'  => ['a.answer' => SORT_ASC],
                'desc' => ['a.answer' => SORT_DESC],
            ],
        ];
        $sort->params = $params;
        $sort->sortParam = 'id-sort';
        return $sort;
    }


    /**
     * Overriding ColumnFilter method to create custom suggestions query
     * @param string $field field name
     * @param string $value field value
     * @param int $limit records count
     * @param int $userId Id of a user
     * @return mixed list of suggestions
     */
    public function getSuggestions($field, $value, $limit, $userId)
    {
       //TODO
    }
}
