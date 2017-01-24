<?php

namespace common\models\rebate;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\rebate\History;

/**
 * HistorySearsh represents the model behind the search form about `common\models\rebate\History`.
 */
class HistorySearch extends History
{
    public $fullName;
    public $login;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['operation_date'], 'date', 'format' => 'php: d.m.Y'],
            [['fullName', 'login', 'note', 'from_where'], 'string'],
            [['orientation', 'credit'], 'number'],
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
    public function search($params)
    {
        $query = History::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
            'defaultOrder' => ['operation_date'=>SORT_DESC],
            'attributes' => [
                'fullName' => [
                    'asc' => ['user.first_name' => SORT_ASC, 'user.last_name' => SORT_ASC],
                    'desc' => ['user.first_name' => SORT_DESC, 'user.last_name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'login' => [
                    'asc' => ['user.username' => SORT_ASC],
                    'desc' => ['user.username' => SORT_DESC],
                ],
                'operation_date',
                'credit',
                'from_where'
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
            $query->joinWith(['user']);
            return $dataProvider;
        }

        // grid filtering conditions

        if ($this->operation_date) {
            $mysql_date_start = date("Y-m-d H:i:s", strtotime($this->operation_date));
            $mysql_date_end = date("Y-m-d", strtotime($this->operation_date) + (60*60*24));
            $query->andFilterWhere(['>=', 'operation_date', $mysql_date_start]);
            $query->andFilterWhere(['<', 'operation_date', $mysql_date_end]);
        }

        // Фильтр по пользователю
        $query->joinWith(['user' => function ($q) {
            $q->where('user.first_name LIKE "%' . $this->fullName . '%" ' .
                'OR user.last_name LIKE "%' . $this->fullName . '%"');
            $q->andFilterWhere(['like', 'username', $this->login]);
        }]);

        $query->andFilterWhere(['orientation' => $this->orientation])
            ->andFilterWhere(['credit' => $this->credit])
            ->andFilterWhere(['like', 'note', $this->note])
            ->andFilterWhere(['like', 'from_where', $this->from_where]);

        return $dataProvider;
    }
}
