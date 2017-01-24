<?php

namespace app\modules\admin\models\search;

use \Yii;
use \yii\base\Model;
use \yii\data\Sort;
use \yii\data\Pagination;
use \yii\db\Query;

use \app\base\ArrayDataProvider;
use \app\models\Orders as OrdersModel;
use yii\helpers\ArrayHelper;

/**
 * Orders represents the model behind the search form about `\app\models\Orders`.
 */
class Orders extends OrdersModel
{
    use \app\models\traits\ColumnFilter {
        getSuggestions as traitGetSuggestions;
    }

    /** @var  integer $ticketId id ticket */
    public $ticketId;
    /** @var  string $product */
    public $product;
    /** @var  string $startDate */
    public $startDate;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['valid_from'], 'required'],
            [[
                'id',
                'customer_id',
                'user_id',
                'tax_id',
                'status',
                'total_price',
                'size',
                'ticketId',
                'created_at',
                'confirm',
            ], 'integer'],
            [[
                'valid_from',
                'valid_to',
                'coupon_code',
                'product',
                'startDate',
                'datetime_booking',
            ], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'ticketId'  => Yii::t('app', 'Ticket ID'),
            'product'   => Yii::t('app', 'Product'),
            'startDate' => Yii::t('app', 'Start Date'),
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
     * @param integer $id customer_id
     * @param array $params for search.
     * @return ArrayDataProvider
     */
    public function search(array $params , $id = null)
    {

        $this->sort = $this->getSortAttributes($params);

        if($id === null){
            $this->setQuery();
        }else{
            $this->setQueryById($id);
        }

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
    protected function setQueryById($id)
    {
        $this->query = new Query();
        $this->query->select([
            'id'                => 'o.id',
            'customer_id'       => 'o.customer_id',
            'user_id'           => 'o.user_id',
            'tax_id'            => 'o.tax_id',
            'status'            => 'o.status',
            'valid_from'        => 'o.valid_from',
            'valid_to'          => 'o.valid_to',
            'size'              => 'o.size',
            'total_price'       => 'o.total_price',
            'coupon_code'       => 'o.coupon_code',
            'created_at'        => 'o.created_at',
            'confirm'           => 'o.confirm',
            'datetime_booking'  => 'o.datetime_booking',

            'product'           => 'tour.name',
            'ticketId'          => 'tbb.ticket_id',
            'startDate'         => 'tbb.start_date'
        ])
            ->from(['o' => static::tableName()])
            ->leftJoin('ticket_booked_buy tbb', 'tbb.order_id = o.id')
            ->leftJoin('ticket t', 't.id = tbb.ticket_id')
            ->leftJoin('tour tour', 'tour.id = t.tour_id')
            ->where(['o.id' => $id]);
    }

    /**
     * Set Query.
     */
    protected function setQuery()
    {
        $this->query = new Query();
        $this->query->select([
            'id'                => 'o.id',
            'customer_id'       => 'o.customer_id',
            'user_id'           => 'o.user_id',
            'tax_id'            => 'o.tax_id',
            'status'            => 'o.status',
            'valid_from'        => 'o.valid_from',
            'valid_to'          => 'o.valid_to',
            'size'              => 'o.size',
            'total_price'       => 'o.total_price',
            'coupon_code'       => 'o.coupon_code',
            'created_at'        => 'o.created_at',
            'confirm'           => 'o.confirm',
            'datetime_booking'  => 'o.datetime_booking',

            'product'           => 'tour.name',
            'ticketId'          => 'tbb.ticket_id',
            'startDate'         => 'tbb.start_date'
        ])
            ->from(['o' => static::tableName()])
            ->leftJoin('ticket_booked_buy tbb', 'tbb.order_id = o.id')
            ->leftJoin('ticket t', 't.id = tbb.ticket_id')
            ->leftJoin('tour tour', 'tour.id = t.tour_id');

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
                $this->query->addOrderBy("`o`.id ASC");
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
                'o.id'                  => $this->id,
                'o.customer_id'         => $this->customer_id,
                'o.tax_id'              => $this->tax_id,
                'o.user_id'             => $this->user_id,
                'o.valid_from'          => $this->valid_from,
                'o.valid_to'            => $this->valid_to,
                'o.status'              => $this->status,
                'o.size'                => $this->size,
                'o.total_price'         => $this->total_price,
                'o.coupon_code'         => $this->coupon_code,
                'o.datetime_booking'    => $this->datetime_booking,
            ])
            ->andFilterWhere(['like', 'tbb.ticket_id', $this->ticketId])
            ->andFilterWhere(['like', 'tour.name', $this->product])
            ->andFilterWhere(['like', 'tbb.start_date', $this->startDate]);
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
                'asc'  => ['b.id' => SORT_ASC],
                'desc' => ['b.id' => SORT_DESC],
            ],
            'valid_from'        => [
                'asc'  => ['b.valid_from' => SORT_ASC],
                'desc' => ['b.valid_from' => SORT_DESC],
            ],
            'valid_to'        => [
                'asc'  => ['b.valid_from' => SORT_ASC],
                'desc' => ['b.valid_from' => SORT_DESC],
            ],
            'status'    => [
                'asc'  => ['o.status' => SORT_ASC],
                'desc' => ['o.status' => SORT_DESC],
            ],
            'size'    => [
                'asc'  => ['o.size' => SORT_ASC],
                'desc' => ['o.size' => SORT_DESC],
            ],
            'total_price' =>[
                'asc'  => ['o.total_price' => SORT_ASC],
                'desc' => ['o.total_price' => SORT_DESC],
            ],
            'datetime_booking' =>[
                'asc'  => ['o.datetime_booking' => SORT_ASC],
                'desc' => ['o.datetime_booking' => SORT_DESC],
            ],
            'ticketId' => [
                'asc'  => ['tbb.ticket_id' => SORT_ASC],
                'desc' => ['tbb.ticket_id' => SORT_DESC],
            ],
            'product' => [
                'asc'  => ['tour.name' => SORT_ASC],
                'desc' => ['tour.name' => SORT_DESC],
            ],
            'startDate' => [
                'asc'  => ['tbb.start_date' => SORT_ASC],
                'desc' => ['tbb.start_date' => SORT_DESC],
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
