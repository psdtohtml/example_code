<?php

namespace app\modules\admin\models\search;

use \Yii;
use \yii\base\Model;
use \yii\data\Sort;
use \yii\data\Pagination;
use \yii\db\Query;

use \app\base\ArrayDataProvider;
use \app\models\Variant as VariantModel;
use yii\web\NotFoundHttpException;

/**
 * Variant represents the model behind the search form about `\app\models\Variant`.
 */
class Variant extends VariantModel
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
            [['id', 'tour_id'], 'integer'],
            [['description', 'name'], 'safe'],
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
            'id'               => 'variant.id',
            'tour_id'          => 'variant.tour_id',
            'name'             => 'variant.name',
            'description'      => 'variant.description',
        ])
            ->from(['variant' => static::tableName()]);


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
                $this->query->addOrderBy("`variant`.id ASC");
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
                'variant.id'       => $this->id,
                'variant.tour_id' => $this->tour_id,
            ])
            ->andFilterWhere(['like', 'variant.name', $this->name])
            ->andFilterWhere(['like', 'variant.description', $this->description]);

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
                'asc'  => ['variant.id' => SORT_ASC],
                'desc' => ['variant.id' => SORT_DESC],
            ],
            'name'          => [
                'asc'  => ['variant.name' => SORT_ASC],
                'desc' => ['variant.name' => SORT_DESC],
            ],
            'tour_id'        => [
                'asc'  => ['variant.tour_id' => SORT_ASC],
                'desc' => ['variant.tour_id' => SORT_DESC],
            ],
            'description'        => [
                'asc'  => ['variant.description' => SORT_ASC],
                'desc' => ['variant.description' => SORT_DESC],
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
                    $resultFieldKey => "DISTINCT (variant.name)",
                ])
                    ->from(['variant' => 'variant'])
                    ->where("variant.name LIKE :value", [':value' => "%{$value}%"])
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
     * @return Variant the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Variant::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
