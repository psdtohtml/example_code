<?php

namespace app\modules\admin\models\search;

use \Yii;
use \yii\base\Model;
use \yii\data\Sort;
use \yii\data\Pagination;
use \yii\db\Query;

use \app\base\ArrayDataProvider;
use \app\models\Tour as TourModel;
use yii\helpers\ArrayHelper;

/**
 * Tour represents the model behind the search form about `\app\models\Tour`.
 */
class Tour extends TourModel
{
    use \app\models\traits\ColumnFilter {
        getSuggestions as traitGetSuggestions;
    }

    /** @var string $manager full name manager*/
    public $manager;
    /** @var string $tax tax name*/
    public $tax;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['confirmation_mail'], 'safe'],
            [['start_tour_date', 'end_tour_date', 'start_tour_time', 'end_tour_time', 'name', 'ticket_available'], 'required'],
            [['id', 'ticket_available', 'manager_id', 'tax_id'], 'integer'],
            [['tax', 'manager', 'start_tour_date', 'start_tour_time', 'end_tour_date', 'end_tour_time', 'name', 'recurring', 'currency'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'manager'  => Yii::t('app', 'Manager'),
            'tax'      => Yii::t('app', 'Tax'),
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
            'id'               => 'tour.id',
            'name'             => 'tour.name',
            'manager_id'       => 'tour.manager_id',
            'recurring'        => 'tour.recurring',
            'currency'         => 'tour.currency',
            'ticket_available' => 'tour.ticket_available',
            'start_tour_date'  => 'tour.start_tour_date',
            'start_tour_time'  => 'tour.start_tour_time',
            'end_tour_date'    => 'tour.end_tour_date',
            'end_tour_time'    => 'tour.end_tour_time',
            'confirmation_mail'=> 'tour.confirmation_mail',

            'manager'          => 'u.username',
            'tax'              => 't.name',
        ])
            ->from(['tour' => static::tableName()])
            ->leftJoin('user u', 'u.id = tour.manager_id')
            ->leftJoin('tax t', 't.id = tour.tax_id');


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
                $this->query->addOrderBy("`tour`.id ASC");
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
                'tour.id'        => $this->id,
                'tour.currency'  => $this->currency,
                'tour.recurring' => $this->recurring,
                'tour.logo'      => $this->logo,
                'tour.confirmation_mail' => $this->confirmation_mail,
            ])
            ->andFilterWhere(['like', 'tour.name', $this->name])
            ->andFilterWhere(['like', 'tour.manager_id', $this->manager_id])
            ->andFilterWhere(['like', 'tour.recurring', $this->recurring])
            ->andFilterWhere(['like', 'tour.ticket_available', $this->ticket_available])
            ->andFilterWhere(['like', 'u.username', $this->manager])
            ->andFilterWhere(['like', 't.name', $this->tax]);
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
                'asc'  => ['tour.id' => SORT_ASC],
                'desc' => ['tour.id' => SORT_DESC],
            ],
            'name'        => [
                'asc'  => ['tour.name' => SORT_ASC],
                'desc' => ['tour.name' => SORT_DESC],
            ],
            'manager_id'        => [
                'asc'  => ['tour.manager_id' => SORT_ASC],
                'desc' => ['tour.manager_id' => SORT_DESC],
            ],
            'recurring'    => [
                'asc'  => ['tour.recurring' => SORT_ASC],
                'desc' => ['tour.recurring' => SORT_DESC],
            ],
            'currency'   => [
                'asc'  => ['tour.currency' => SORT_ASC],
                'desc' => ['tour.currency' => SORT_DESC],
            ],
            'ticket_available'    => [
                'asc'  => ['tour.ticket_available' => SORT_ASC],
                'desc' => ['tour.ticket_available' => SORT_DESC],
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
                    $resultFieldKey => "DISTINCT (tour.name)",
                ])
                    ->from(['tour' => 'tour'])
                    ->where("tour.name LIKE :value", [':value' => "%{$value}%"])
                    ->limit($limit);

                return $query;
            };
        }  elseif ($field == 'recurring') {
            $callback = function ($query, $field, $value, $limit) use ($resultFieldKey) {
                /** @var \yii\db\Query $query */
                $query->select([
                    $resultFieldKey => "DISTINCT (tour.recurring)",
                ])
                    ->from(['tour' => 'tour'])
                    ->where("tour.recurring LIKE :value", [':value' => "%{$value}%"])
                    ->limit($limit);

                return $query;
            };
        }

        return $this->traitGetSuggestions($field, $value, $limit, $userId, $callback, $auxConditions);

    }
}
