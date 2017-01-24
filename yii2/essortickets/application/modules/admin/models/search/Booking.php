<?php

namespace app\modules\admin\models\search;

use \Yii;
use \yii\base\Model;
use \yii\data\Sort;
use \yii\data\Pagination;
use \yii\db\Query;

use \app\base\ArrayDataProvider;
use \app\models\Booking as BookingModel;
use yii\helpers\ArrayHelper;

/**
 * Booking represents the model behind the search form about `\app\models\Booking`.
 */
class Booking extends BookingModel
{
    use \app\models\traits\ColumnFilter {
        getSuggestions as traitGetSuggestions;
    }

    /** @var string $product tour name*/
    public $product;
    /** @var string $tourId tour id*/
    public $tourId;
    /** @var string $customer full name customer*/
    public $customer;
    /** @var  integer $customerId */
    public $customerId;
    /** @var integer $currency*/
    public $currency;
    /** @var  integer $priceTicket*/
    public $priceTicket;
    /** @var  integer $priceBookingFeeType percentage or fixed*/
    public $priceBookingFeeType;
    /** @var  integer $priceBookingFee*/
    public $priceBookingFee;
    /** @var  integer $size*/
    public $size;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product'], 'required'],
            [[
                'id',
                'order_id',
                'status',
                'size',
                'tourId',
                'priceTicket',
                'priceBookingFeeType',
                'priceBookingFee',
                'customerId',
            ], 'integer'],
            [[
                'customer',
                'product',
                'currency',
                'valid_from',
                'valid_to',
                'booking_price',
            ], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'product'  => Yii::t('app', 'Product'),
            'tourId'     => Yii::t('app', 'Tour Id'),
            'customer' => Yii::t('app', 'Customer'),
            'currency' => Yii::t('app', 'Currency'),
            'priceTicket' => Yii::t('app', 'Price Ticket'),
            'priceBookingFeeType' => Yii::t('app', 'Price Booking Fee Type'),
            'priceBookingFee' => Yii::t('app', 'Price Booking Fee'),
            'size' => Yii::t('app', 'Size'),
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
    public function search(array $params , $id = null , $type = null)
    {

        $this->sort = $this->getSortAttributes($params);

        if($id === null){
            $this->setQuery();
        }else{
            if($type === null){
                $this->setQueryByOrderId($id);
            }else{
                $this->setQueryById($type);
            }
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

    public function setQueryByOrderId($id)
    {
        $this->query = new Query();
        $this->query->select([
            'id'                  => 'b.id',
            'order_id'            => 'b.order_id',
            'valid_from'          => 'b.valid_from',
            'valid_to'            => 'b.valid_to',
            'status'              => 'b.status',
            'booking_price'       => 'b.booking_price',

            'product'             => 'tour.name',
            'tourId'              => 'tour.id',
            'currency'            => 'tour.currency',
            'priceTicket'         => 't.price',
            'priceBookingFeeType' => 't.booking_fee_type',
            'priceBookingFee'     => 't.booking_fee',
            'customer'            => 'CONCAT(\'#\', c.id, \' \',c.name,\' \', c.last_name)',
            'customerId'          => 'c.id',
            'size'                => 'o.size',

        ])
            ->from(['b' => static::tableName()])
            ->leftJoin('orders o', 'o.id = b.order_id')
            ->leftJoin('customer c', 'c.id = o.customer_id')
            ->leftJoin('ticket_booked_buy tbb', 'tbb.order_id = o.id')
            ->leftJoin('ticket t', 'tbb.ticket_id = t.id')
            ->leftJoin('tour tour', 'tour.id = t.tour_id')
            ->where(['o.id' => $id])
            ->groupBy('b.id');
    }

    /**
     * @param integer $id customer id
     */
    protected function setQueryById($id)
    {
        $this->query = new Query();
        $this->query->select([
            'id'                  => 'b.id',
            'order_id'            => 'b.order_id',
            'valid_from'          => 'b.valid_from',
            'valid_to'            => 'b.valid_to',
            'status'              => 'b.status',
            'booking_price'       => 'b.booking_price',

            'product'             => 'tour.name',
            'tourId'              => 'tour.id',
            'currency'            => 'tour.currency',
            'priceTicket'         => 't.price',
            'priceBookingFeeType' => 't.booking_fee_type',
            'priceBookingFee'     => 't.booking_fee',
            'customer'            => 'CONCAT(\'#\', c.id, \' \',c.name,\' \', c.last_name)',
            'customerId'          => 'c.id',
            'size'                => 'o.size',

        ])
            ->from(['b' => static::tableName()])
            ->leftJoin('orders o', 'o.id = b.order_id')
            ->leftJoin('customer c', 'c.id = o.customer_id')
            ->leftJoin('ticket_booked_buy tbb', 'tbb.order_id = o.id')
            ->leftJoin('ticket t', 'tbb.ticket_id = t.id')
            ->leftJoin('tour tour', 'tour.id = t.tour_id')
            ->where(['c.id' => $id])
            ->groupBy('b.id');
    }

    /**
     * Set Query.
     */
    protected function setQuery()
    {
        $this->query = new Query();
        $this->query->select([
            'id'                  => 'b.id',
            'order_id'            => 'b.order_id',
            'valid_from'          => 'b.valid_from',
            'valid_to'            => 'b.valid_to',
            'status'              => 'b.status',
            'booking_price'       => 'b.booking_price',

            'product'             => 'tour.name',
            'tourId'              => 'tour.id',
            'currency'            => 'tour.currency',
            'priceTicket'         => 't.price',
            'priceBookingFeeType' => 't.booking_fee_type',
            'priceBookingFee'     => 't.booking_fee',
            'customer'            => 'CONCAT(\'#\', c.id, \' \',c.name,\' \', c.last_name)',
            'customerId'          => 'c.id',
            'size'                => 'o.size',
        ])
            ->from(['b' => static::tableName()])
            ->leftJoin('orders o', 'o.id = b.order_id')
            ->leftJoin('customer c', 'c.id = o.customer_id')
            ->leftJoin('ticket_booked_buy tbb', 'tbb.order_id = o.id')
            ->leftJoin('ticket t', 'tbb.ticket_id = t.id')
            ->leftJoin('tour tour', 'tour.id = t.tour_id')
            ->groupBy('b.id');


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
                $this->query->addOrderBy("`b`.id ASC");
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
                'b.id'             => $this->id,
                'b.order_id'       => $this->order_id,
                'b.valid_from'     => $this->valid_from,
                'b.valid_to'       => $this->valid_to,
                'b.status'         => $this->status,
                'b.booking_price'  => $this->booking_price,
            ])
            ->andFilterWhere(['like', 'o.size', $this->size])
            ->andFilterWhere(['like', 'tour.name', $this->product])
            ->andFilterWhere(['like', 'tour.id', $this->tourId])
            ->andFilterWhere(['like', 'tour.currency', $this->currency])
            ->andFilterWhere(['like', 't.price', $this->priceTicket])
            ->andFilterWhere(['like', 't.booking_fee_type', $this->priceBookingFeeType])
            ->andFilterWhere(['like', 't.booking_fee', $this->priceBookingFee])
            ->andFilterWhere(['like', 'CONCAT(\'#\', c.id, \' \',c.name,\' \', c.last_name)', $this->customer]);
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
            'product'    => [
                'asc'  => ['tour.name' => SORT_ASC],
                'desc' => ['tour.name' => SORT_DESC],
            ],
            'valid_from'        => [
                'asc'  => ['b.valid_from' => SORT_ASC],
                'desc' => ['b.valid_from' => SORT_DESC],
            ],
            'valid_to'        => [
                'asc'  => ['b.valid_from' => SORT_ASC],
                'desc' => ['b.valid_from' => SORT_DESC],
            ],
            'booking_price'        => [
                'asc'  => ['b.booking_price' => SORT_ASC],
                'desc' => ['b.booking_price' => SORT_DESC],
            ],
            'customer'   => [
                'asc'  => ['c.id' => SORT_ASC],
                'desc' => ['c.id' => SORT_DESC],
            ],
            'currency'    => [
                'asc'  => ['t.currency' => SORT_ASC],
                'desc' => ['t.currency' => SORT_DESC],
            ],
            'status'    => [
                'asc'  => ['o.status' => SORT_ASC],
                'desc' => ['o.status' => SORT_DESC],
            ],
            'size'    => [
                'asc'  => ['o.size' => SORT_ASC],
                'desc' => ['o.size' => SORT_DESC],
            ],
        ];
        $sort->params = $params;
        $sort->sortParam = 'id-sort';
        return $sort;
    }


    /**
     * Overriding ColumnFilter method to create custom suggestions query
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
        if ($field == 'product') {
            $callback = function ($query, $field, $value, $limit) use ($resultFieldKey) {
                /** @var \yii\db\Query $query */
                $query->select([
                    $resultFieldKey => "DISTINCT (tour.name)",
                ])
                    ->from(['booking' => 'b'])
                    ->innerJoin('orders o', 'o.id = b.order_id')
                    ->innerJoin('ticket_booked_buy tbb', 'tbb.order_id = o.id')
                    ->innerJoin('ticket t', 'tbb.ticket_id = t.id')
                    ->innerJoin('tour tour', 'tour.id = t.tour_id')
                    ->where("tour.name LIKE :value", [':value' => "%{$value}%"])
                    ->limit($limit);

                return $query;
            };
        }

        return $this->traitGetSuggestions($field, $value, $limit, $userId, $callback, $auxConditions);

    }
}
