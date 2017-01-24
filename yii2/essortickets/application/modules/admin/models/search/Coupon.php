<?php

namespace app\modules\admin\models\search;

use \Yii;
use \yii\base\Model;
use \yii\data\Sort;
use \yii\data\Pagination;
use \yii\db\Query;

use \app\base\ArrayDataProvider;
use \app\models\Coupon as CouponModel;
use yii\web\NotFoundHttpException;

/**
 * Coupon represents the model behind the search form about `\app\models\Coupon`.
 */
class Coupon extends CouponModel
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
            [['id', 'ticket_id', 'limit', 'used', 'status'], 'integer'],
            [['name', 'code'], 'safe'],
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
            'id'               => 'coupon.id',
            'name'             => 'coupon.name',
            'ticket_id'        => 'coupon.ticket_id',
            'code'             => 'coupon.code',
            'limit'            => 'coupon.limit',
            'used'             => 'coupon.used',
            'status'           => 'coupon.status',
        ])
            ->from(['coupon' => static::tableName()]);


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
                $this->query->addOrderBy("`coupon`.id ASC");
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
                'coupon.id'        => $this->id,
                'coupon.ticket_id' => $this->ticket_id,
                'coupon.limit'     => $this->limit,
                'coupon.used'      => $this->used,
                'coupon.status'    => $this->status,
            ])
            ->andFilterWhere(['like', 'coupon.name', $this->name])
            ->andFilterWhere(['like', 'coupon.code', $this->code]);
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
                'asc'  => ['coupon.id' => SORT_ASC],
                'desc' => ['coupon.id' => SORT_DESC],
            ],
            'ticket_id'          => [
                'asc'  => ['coupon.ticket_id' => SORT_ASC],
                'desc' => ['coupon.ticket_id' => SORT_DESC],
            ],
            'limit'        => [
                'asc'  => ['coupon.limit' => SORT_ASC],
                'desc' => ['coupon.limit' => SORT_DESC],
            ],
            'used'        => [
                'asc'  => ['coupon.used' => SORT_ASC],
                'desc' => ['coupon.used' => SORT_DESC],
            ],
            'name'        => [
                'asc'  => ['coupon.name' => SORT_ASC],
                'desc' => ['coupon.name' => SORT_DESC],
            ],
            'code'    => [
                'asc'  => ['coupon.code' => SORT_ASC],
                'desc' => ['coupon.code' => SORT_DESC],
            ],
            'status'    => [
                'asc'  => ['coupon.status' => SORT_ASC],
                'desc' => ['coupon.status' => SORT_DESC],
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
                    $resultFieldKey => "DISTINCT (coupon.name)",
                ])
                    ->from(['coupon' => 'coupon'])
                    ->where("coupon.name LIKE :value", [':value' => "%{$value}%"])
                    ->limit($limit);

                return $query;
            };
        }

        return $this->traitGetSuggestions($field, $value, $limit, $userId, $callback, $auxConditions);

    }

    /**
     * Finds the Coupon model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Coupon the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Coupon::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
