<?php

namespace common\models\rebate;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\rebate\Company;

/**
 * SearchCompany represents the model behind the search form about `common\models\rebate\Company`.
 */
class SearchCompany extends Company
{
    public $sectionTitle;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'id_section'], 'integer'],
            [['name', 'sectionTitle'], 'safe'],
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
        $query = Company::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'name' => [
                    'asc' => ['name' => SORT_ASC],
                    'desc' => ['name' => SORT_DESC],
                    'label' => 'Компния',
                    'default' => SORT_ASC
                ],
                'sectionTitle' => [
                    'asc' => ['section.title' => SORT_ASC],
                    'desc' => ['section.title' => SORT_DESC],
                    'label' => 'Раздел'
                ],
                'id_section' => [
                    'asc' => ['section.title' => SORT_ASC],
                    'desc' => ['section.title' => SORT_DESC],
                    'label' => 'Раздел'
                ]
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
            $query->joinWith(['section']);
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        // Фильтр по разделу
        $query->joinWith(['section' => function ($q) {
            $q->where('section.title LIKE "%' . $this->sectionTitle . '%"');
        }]);

        // Фильтр по id раздела
        if($this->id_section ) {
            $query->joinWith(['section' => function ($q) {
                $q->where('section.id ="' . $this->id_section . '"');
            }]);
        }


        return $dataProvider;
    }
}
