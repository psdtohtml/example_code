<?php

namespace app\modules\admin\models\search;

use \Yii;
use \yii\base\Model;
use \yii\data\Sort;
use \yii\data\Pagination;
use \yii\db\Query;

use \app\base\ArrayDataProvider;
use \app\models\Customer as CustomerModel;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * Customer represents the model behind the search form about `\app\models\Customer`.
 */
class Customer extends CustomerModel
{
    use \app\models\traits\ColumnFilter {
        getSuggestions as traitGetSuggestions;
    }

    /** @var string $fullName */
    public $fullName;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'last_name', 'email'], 'required'],
            [['id', 'subscribed'], 'integer'],
            [['fullName', 'name', 'last_name', 'email', 'country', 'phone', 'ip', 'title', 'company', 'street_address', 'city', 'zip'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'fullName' => Yii::t('app', 'Full Name'),
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
            'id'              => 'c.id',
            'title'           => 'c.title',
            'name'            => 'c.name',
            'last_name'       => 'c.last_name',
            'company'         => 'c.company',
            'fullName'        => 'CONCAT(\'#\', c.id, \' \',c.name,\' \', c.last_name)',
            'email'           => 'c.email',
            'country'         => 'c.country',
            'city'            => 'c.city',
            'street_address'  => 'c.street_address',
            'zip'             => 'c.zip',
            'phone'           => 'c.phone',
            'ip'              => 'c.ip',
            'subscribed'      => 'c.subscribed',
        ])
            ->from(['c' => static::tableName()]);


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
                $this->query->addOrderBy("`c`.id ASC");
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
                'c.id'              => $this->id,
                'c.title'           => $this->title,
                'c.name'            => $this->name,
                'c.company'         => $this->company,
                'c.email'           => $this->email,
                'c.country'         => $this->country,
                'c.city'            => $this->city,
                'c.street_address'  => $this->street_address,
                'c.zip'             => $this->zip,
                'c.phone'           => $this->phone,
                'c.subscribed'      => $this->subscribed,
            ])
            ->andFilterWhere(['like', 'c.ip', $this->id]);
        if (!empty($this->fullName)) {
            $this->query->andFilterWhere([
                'like',
                'CONCAT(\'#\', c.id, \' \',c.name,\' \', c.last_name)',
                $this->fullName
            ]);
        }
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
                'asc'  => ['c.id' => SORT_ASC],
                'desc' => ['c.id' => SORT_DESC],
            ],
            'fullName'          => [
                'asc'  => ['c.id' => SORT_ASC],
                'desc' => ['c.id' => SORT_DESC],
            ],
            'title'          => [
                'asc'  => ['c.title' => SORT_ASC],
                'desc' => ['c.title' => SORT_DESC],
            ],
            'company'          => [
                'asc'  => ['c.company' => SORT_ASC],
                'desc' => ['c.company' => SORT_DESC],
            ],
            'street_address'          => [
                'asc'  => ['c.street_address' => SORT_ASC],
                'desc' => ['c.street_address' => SORT_DESC],
            ],
            'city'          => [
                'asc'  => ['c.city' => SORT_ASC],
                'desc' => ['c.city' => SORT_DESC],
            ],
            'zip'          => [
                'asc'  => ['c.zip' => SORT_ASC],
                'desc' => ['c.zip' => SORT_DESC],
            ],
            'email'        => [
                'asc'  => ['c.email' => SORT_ASC],
                'desc' => ['c.email' => SORT_DESC],
            ],
            'country'        => [
                'asc'  => ['c.country' => SORT_ASC],
                'desc' => ['c.country' => SORT_DESC],
            ],
            'phone'        => [
                'asc'  => ['c.phone' => SORT_ASC],
                'desc' => ['c.phone' => SORT_DESC],
            ],
            'ip'        => [
                'asc'  => ['c.ip' => SORT_ASC],
                'desc' => ['c.ip' => SORT_DESC],
            ],
            'subscribed'        => [
                'asc'  => ['c.subscribed' => SORT_ASC],
                'desc' => ['c.subscribed' => SORT_DESC],
            ],
        ];
        $sort->params = $params;
        $sort->sortParam = 'id-sort';
        return $sort;
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
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
        if ($field == 'fullName') {
            $callback = function ($query, $field, $value, $limit) use ($resultFieldKey) {

                /** @var \yii\db\Query $query */
                $query->select([
                    $resultFieldKey => "DISTINCT ('CONCAT('#', c.id, ' ',c.name,' ', c.last_name)')",
                ])
                    ->from(['customer' => 'customer'])
                    ->having("value LIKE :value", [':value' => "%{$value}%"])
                    ->limit($limit);

                return $query;
            };
        }

        return $this->traitGetSuggestions($field, $value, $limit, $userId, $callback, $auxConditions);

    }

    /**
     * Finds the Variant model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Customer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CustomerModel::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
