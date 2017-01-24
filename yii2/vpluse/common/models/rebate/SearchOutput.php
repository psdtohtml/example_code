<?php

namespace common\models\rebate;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SearchOutput represents the model behind the search form about `common\models\rebate\Output`.
 */
class SearchOutput extends Output
{
    public $fullName;
    public $login;
    public $balance;
    public $balancePartner;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['created_at'], 'date', 'format' => 'php: d.m.Y'],
            [['amount', 'balance', 'balancePartner'], 'number'],
            [['fullName', 'login', 'payment_detail'], 'string'],
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
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, $service_id = 0)
    {
        $query = Output::find()
            ->where(['service_id' => $service_id]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
            'defaultOrder' => ['id'=>SORT_DESC],
            'attributes' => [
                'id',
                'created_at',
                'fullName' => [
                    'asc' => ['user.first_name' => SORT_ASC, 'user.last_name' => SORT_ASC],
                    'desc' => ['user.first_name' => SORT_DESC, 'user.last_name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'login' => [
                    'asc' => ['user.username' => SORT_ASC],
                    'desc' => ['user.username' => SORT_DESC],
                ],
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if (!($this->load($params) && $this->validate())) {
            /**
             * Жадная загрузка данных модели Разделов
             * для работы сортировки.
             */
            $query->joinWith(['idUser']);

            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'output.id' => $this->id,
        ]);
        $query->andFilterWhere([
            'output.amount' => $this->amount,
        ]);
        $query->andFilterWhere(['like', 'payment_detail', $this->payment_detail]);

        if($this->created_at) {
            $query->andFilterWhere([
                '>=', 'output.created_at', strtotime($this->created_at)
            ]);
            $query->andFilterWhere([
                '<', 'output.created_at', strtotime($this->created_at) + (60*60*24)
            ]);
        }

        $query->andFilterWhere([
            'output.status' => $this->status,
        ]);

        // Фильтр по пользователю
        $query->joinWith(['idUser' => function ($q) {
            $q->where('user.first_name LIKE "%' . $this->fullName . '%" ' .
                'OR user.last_name LIKE "%' . $this->fullName . '%"');
            $q->andFilterWhere(['like', 'username', $this->login]);

            $q->andFilterWhere(['balance' => $this->balance]);

            $q->andFilterWhere(['balancePartner' => $this->balancePartner]);

        }]);



        return $dataProvider;
    }
}
