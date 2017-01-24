<?php

namespace app\modules\admin\models\search;

use \Yii;
use \yii\base\Model;
use \yii\data\Sort;
use \yii\data\Pagination;
use \yii\db\Query;

use \app\base\ArrayDataProvider;
use \app\models\Ticket as TicketModel;
use yii\web\NotFoundHttpException;

/**
 * Ticket represents the model behind the search form about `\app\models\Ticket`.
 */
class Ticket extends TicketModel
{
    use \app\models\traits\ColumnFilter {
        getSuggestions as traitGetSuggestions;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'tour_id', 'price'], 'integer'],
            [['name', 'description'], 'safe'],
        ];
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
     * @param array $params for search.
     * @return ArrayDataProvider
     */
    public function search(array $params)
    {

        $this->sort = $this->getSortAttributes($params);
        $this->setQuery();
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
     */
    protected function setQuery()
    {
        $this->query = new Query();
        $this->query->select([
            'id'               => 'ticket.id',
            'tour_id'          => 'ticket.tour_id',
            'name'             => 'ticket.name',
            'description'      => 'ticket.description',
            'price'            => 'ticket.price',
        ])
            ->from(['ticket' => static::tableName()]);


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
                $this->query->addOrderBy("`ticket`.id ASC");
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
                'ticket.id'       => $this->id,
                'ticket.tour_id' => $this->tour_id,
                'ticket.price' => $this->price,
            ])
            ->andFilterWhere(['like', 'ticket.name', $this->name])
            ->andFilterWhere(['like', 'ticket.description', $this->description]);
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
                'asc'  => ['ticket.id' => SORT_ASC],
                'desc' => ['ticket.id' => SORT_DESC],
            ],
            'name'          => [
                'asc'  => ['ticket.name' => SORT_ASC],
                'desc' => ['ticket.name' => SORT_DESC],
            ],
            'tour_id'        => [
                'asc'  => ['ticket.tour_id' => SORT_ASC],
                'desc' => ['ticket.tour_id' => SORT_DESC],
            ],
            'price'        => [
                'asc'  => ['ticket.price' => SORT_ASC],
                'desc' => ['ticket.price' => SORT_DESC],
            ],
            'description'    => [
                'asc'  => ['ticket.description' => SORT_ASC],
                'desc' => ['ticket.description' => SORT_DESC],
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
        $callback = null;
        $resultFieldKey = Yii::$app->params['common.autoсomplete.display.key'];
        $auxConditions = null;
        if ($field == 'name') {
            $callback = function ($query, $field, $value, $limit) use ($resultFieldKey) {
                /** @var \yii\db\Query $query */
                $query->select([
                    $resultFieldKey => "DISTINCT (ticket.name)",
                ])
                    ->from(['ticket' => 'ticket'])
                    ->where("ticket.name LIKE :value", [':value' => "%{$value}%"])
                    ->limit($limit);

                return $query;
            };
        }

        return $this->traitGetSuggestions($field, $value, $limit, $userId, $callback, $auxConditions);

    }

    /**
     * Finds the Ticket model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Ticket the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Ticket::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
