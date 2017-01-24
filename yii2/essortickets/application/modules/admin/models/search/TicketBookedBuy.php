<?php

namespace app\modules\admin\models\search;

use \Yii;
use \yii\base\Model;
use \yii\data\Sort;
use \yii\data\Pagination;
use \yii\db\Query;

use \app\base\ArrayDataProvider;
use \app\models\TicketBookedBuy as TicketBookedBuyModel;
use yii\helpers\ArrayHelper;

/**
 * TicketBookedBuy represents the model behind the search form about `\app\models\TicketBookedBuy`.
 */
class TicketBookedBuy extends TicketBookedBuyModel
{
    use \app\models\traits\ColumnFilter {
        getSuggestions as traitGetSuggestions;
    }

    /** @var string $tier ticket name*/
    public $tier;
    /** @var integer $price ticket price*/
    public $price;
    /** @var integer $size */
    public $size;
    /** @var string $extraName */
    public $extraName;
    /** @var integer $extraPrice */
    public $extraPrice;
    /** @var string $product */
    public $product;
    /** @var int $bookingId */
    public $bookingId;
    /** @var  string $customer */
    public $customer;
    /** @var  integer $orderStatus */
    public $orderStatus;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ticket_id', 'start_date'], 'required'],
            [[
                'id',
                'order_id',
                'ticket_id',
                'variant_id',
                'price',
                'size',
                'extraPrice',
                'bookingId',
                'event_id',
                'orderStatus',
            ], 'integer'],
            [[
                'tier',
                'extraName',
                'start_date',
                'product',
                'customer',
            ], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'tier'        => Yii::t('app', 'Tier'),
            'price'       => Yii::t('app', 'Price'),
            'size'        => Yii::t('app', 'Size'),
            'extraName'   => Yii::t('app', 'Name'),
            'extraPrice'  => Yii::t('app', 'Total'),
            'product'     => Yii::t('app', 'Product'),
            'bookingId'   => Yii::t('app', 'Booking'),
            'customer'    => Yii::t('app', 'Customer'),
            'orderStatus' => Yii::t('app', 'Order Status'),
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
    public function search(array $params, $id = null, $event_id = null)
    {

        $this->sort = $this->getSortAttributes($params);

        if($event_id !== null){
            $this->setQueryWithEvent($event_id);
        }else{
            $this->setQuery($id);
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

    protected  function  setQueryWithEvent($event_id)
    {
        $this->query = new Query();
        $this->query->select([
            'id'          => 'tbb.id',
            'order_id'     => 'tbb.order_id',
            'ticket_id'    => 'tbb.ticket_id',
            'extra_id'     => 'tbb.extra_id',
            'variant_id'   => 'tbb.variant_id',
            'start_date'   => 'tbb.start_date',
            'event_id'     => 'tbb.event_id',

            'tier'         => 't.name',
            'price'        => 't.price',
            'size'         => 'o.size',
            'orderStatus'  => 'o.status',
            'extraName'    => 'e.name',
            'extraPrice'   => 'e.price',
            'product'      => 'tour.name',
            'customer'     => 'CONCAT(\'#\', c.id, \' \',c.name,\' \', c.last_name)',
            'bookingId'    => 'b.id',

        ])
            ->from(['tbb' => static::tableName()])
            ->leftJoin('orders o', 'o.id = tbb.order_id')
            ->leftJoin('customer c', 'c.id = o.customer_id')
            ->leftJoin('booking b', 'b.order_id = o.id')
            ->leftJoin('ticket t', 't.id = tbb.ticket_id')
            ->leftJoin('extras e', 'e.ticket_id = t.id')
            ->leftJoin('tour tour', 'tour.id = t.tour_id')
            ->where(['tbb.event_id' =>$event_id]);


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
                $this->query->addOrderBy("`tbb`.id ASC");
            }
        }
    }

    /**
     * Set Query.
     */
    protected function setQuery($id)
    {
        $this->query = new Query();
        $this->query->select([
            'id'           => 'tbb.id',
            'order_id'     => 'tbb.order_id',
            'ticket_id'    => 'tbb.ticket_id',
            'extra_id'     => 'tbb.extra_id',
            'variant_id'   => 'tbb.variant_id',
            'start_date'   => 'tbb.start_date',
            'event_id'     => 'tbb.event_id',

            'tier'         => 't.name',
            'price'        => 't.price',
            'size'         => 'o.size',
            'orderStatus'  => 'o.status',
            'extraName'    => 'e.name',
            'extraPrice'   => 'e.price',
            'product'      => 'tour.name',
            'customer'     => 'CONCAT(\'#\', c.id, \' \',c.name,\' \', c.last_name)',
            'bookingId'    => 'b.id',

        ])
            ->from(['tbb' => static::tableName()])
            ->leftJoin('orders o', 'o.id = tbb.order_id')
            ->leftJoin('customer c', 'c.id = o.customer_id')
            ->leftJoin('booking b', 'b.order_id = o.id')
            ->leftJoin('ticket t', 't.id = tbb.ticket_id')
            ->leftJoin('extras e', 'e.ticket_id = t.id')
            ->leftJoin('tour tour', 'tour.id = t.tour_id')
            ->where(['b.id' => $id]);


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
                $this->query->addOrderBy("`tbb`.id ASC");
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
                'tbb.id'           => $this->id,
                'tbb.order_id'     => $this->order_id,
                'tbb.ticket_id'    => $this->ticket_id,
                'tbb.variant_id'   => $this->variant_id,
                'tbb.start_date'   => $this->start_date,
                'tbb.event_id'     => $this->event_id,
            ])
            ->andFilterWhere(['like', 't.name', $this->tier])
            ->andFilterWhere(['like', 't.price', $this->price])
            ->andFilterWhere(['like', 'o.size', $this->size])
            ->andFilterWhere(['like', 'o.status', $this->orderStatus])
            ->andFilterWhere(['like', 'e.name', $this->extraName])
            ->andFilterWhere(['like', 'e.price', $this->extraPrice])
            ->andFilterWhere(['like', 'tour.name', $this->product])
            ->andFilterWhere(['like', 'c.name', $this->customer])
            ->andFilterWhere(['like', 'b.id', $this->bookingId]);
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
                'asc'  => ['tbb.id' => SORT_ASC],
                'desc' => ['tbb.id' => SORT_DESC],
            ],
            'order_id'          => [
                'asc'  => ['tbb.order_id' => SORT_ASC],
                'desc' => ['tbb.order_id' => SORT_DESC],
            ],
            'ticket_id'          => [
                'asc'  => ['tbb.ticket_id' => SORT_ASC],
                'desc' => ['tbb.ticket_id' => SORT_DESC],
            ],
            'variant_id'          => [
                'asc'  => ['tbb.variant_id' => SORT_ASC],
                'desc' => ['tbb.variant_id' => SORT_DESC],
            ],
            'start_date'          => [
                'asc'  => ['tbb.start_date' => SORT_ASC],
                'desc' => ['tbb.start_date' => SORT_DESC],
            ],
            'event_id'          => [
                'asc'  => ['tbb.event_id' => SORT_ASC],
                'desc' => ['tbb.event_id' => SORT_DESC],
            ],
            'tier'          => [
                'asc'  => ['t.name' => SORT_ASC],
                'desc' => ['t.name' => SORT_DESC],
            ],
            'price'          => [
                'asc'  => ['t.price' => SORT_ASC],
                'desc' => ['t.price' => SORT_DESC],
            ],
            'size'          => [
                'asc'  => ['o.size' => SORT_ASC],
                'desc' => ['o.size' => SORT_DESC],
            ],
            'orderStatus'          => [
                'asc'  => ['o.status' => SORT_ASC],
                'desc' => ['o.status' => SORT_DESC],
            ],
            'extraName'          => [
                'asc'  => ['e.name' => SORT_ASC],
                'desc' => ['e.name' => SORT_DESC],
            ],
            'extraPrice'          => [
                'asc'  => ['e.price' => SORT_ASC],
                'desc' => ['e.price' => SORT_DESC],
            ],
            'product'          => [
                'asc'  => ['tour.product' => SORT_ASC],
                'desc' => ['tour.product' => SORT_DESC],
            ],
            'customer'          => [
                'asc'  => ['c.id' => SORT_ASC],
                'desc' => ['c.id' => SORT_DESC],
            ],
            'bookingId'          => [
                'asc'  => ['b.id' => SORT_ASC],
                'desc' => ['b.id' => SORT_DESC],
            ],
        ];
        $sort->params = $params;
        $sort->sortParam = 'id-sort';
        return $sort;
    }


    /**
     * Overriding ColumnFilter method to create custom suggestions query
     * @param string $field
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
