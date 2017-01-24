<?php

namespace common\models\rebate;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SearchCompany represents the model behind the search form about `common\models\rebate\Company`.
 */
class SearchUserRequest extends UserRequest
{
    public $companyName;
    public $fullName;
    public $login;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'id_section'], 'integer'],
            [['created_at'], 'date', 'format' => 'php: d.m.Y'],
            [['fullName', 'companyName', 'status', 'login', 'account'], 'string'],
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
        $query = UserRequest::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
            'defaultOrder' => ['created_at'=>SORT_DESC],
            'attributes' => [
                'id',
                'fullName' => [
                    'asc' => ['user.first_name' => SORT_ASC, 'user.last_name' => SORT_ASC],
                    'desc' => ['user.first_name' => SORT_DESC, 'user.last_name' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'companyName' => [
                    'asc' => ['company.name' => SORT_ASC],
                    'desc' => ['company.name' => SORT_DESC],
                ],
                'login' => [
                    'asc' => ['user.username' => SORT_ASC],
                    'desc' => ['user.username' => SORT_DESC],
                ],
                'created_at',
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
            $query->joinWith(['idUser'])->joinWith(['idCompany'])->joinWith(['idSection']);
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'account', $this->account]);

        $query->andFilterWhere([
            'user_request.status' => $this->status,
        ]);

        if($this->created_at) {
            $query->andFilterWhere([
                '>=', 'user_request.created_at', strtotime($this->created_at)
            ]);
            $query->andFilterWhere([
                '<', 'user_request.created_at', strtotime($this->created_at) + (60*60*24)
            ]);
        }

        $query->joinWith(['idUser' => function ($q) {
            $q->where('user.first_name LIKE "%' . $this->fullName . '%" ' .
                'OR user.last_name LIKE "%' . $this->fullName . '%"');
            $q->andFilterWhere(['like', 'username', $this->login]);
        }]);

        $query->joinWith(['idCompany' => function ($q) {
            $q->where('company.name LIKE "%' . $this->companyName . '%"');
        }]);

        // Фильтр по id раздела
        if($this->id_section ) {
            $query->joinWith(['idSection' => function ($q) {
                $q->where('section.id ="' . $this->id_section . '"');
            }]);
        }

        return $dataProvider;
    }
}
