<?php

namespace common\models\rebate;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\rebate\Section;

/**
 * SearchSection represents the model behind the search form about `common\models\rebate\Section`.
 */
class SearchSection extends Section
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'id_base'], 'integer'],
            [['title', 'attention_block', 'refback_title', 'refback_html'], 'safe'],
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
        $query = Section::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'title' => [
                    'asc' => ['title' => SORT_ASC],
                    'desc' => ['title' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'id_base' => [
                    'asc' => ['base_section.name' => SORT_ASC],
                    'desc' => ['base_section.name' => SORT_DESC],
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
             * Жадная загрузка данных модели Базовых разделов
             * для работы сортировки.
             */
            $query->joinWith(['baseSection']);
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'attention_block', $this->attention_block])
            ->andFilterWhere(['like', 'refback_title', $this->refback_title])
            ->andFilterWhere(['like', 'refback_html', $this->refback_html]);

        // Фильтр по id базового раздела
        if($this->id_base ) {
            $query->joinWith(['baseSection' => function ($q) {
                $q->where('base_section.id ="' . $this->id_base . '"');
            }]);
        }

        return $dataProvider;
    }
}
