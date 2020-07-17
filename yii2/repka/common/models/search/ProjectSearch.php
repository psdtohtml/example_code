<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Project;
use yii\db\ActiveQuery;
use yii\web\IdentityInterface;

/**
 * Поисковая модель проекта.
 */
class ProjectSearch extends Project
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'last_checked_at', 'user_id', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
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
        $query = Project::find()
            ->with('owner');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'last_checked_at' => $this->last_checked_at,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }

    /**
     * Возвращает провайдер данных для пользователя.
     *
     * @param IdentityInterface $user
     * @param $params
     * @return ActiveDataProvider
     */
    public function searchForUser(IdentityInterface $user, $params)
    {
        $ownerUserQuery = Project::find();
        $ownerUserQuery->active()->withUser($user);

        $coOwnerUserQuery = Project::find()->active();
        $coOwnerUserQuery->innerJoinWith(['sharedProject2Users sp2u' => function (ActiveQuery $query) use ($user) {
            $query->andWhere(['sp2u.user_id' => $user->getId()]);
        }]);
        $ownerUserQuery->union($coOwnerUserQuery);

        $dataProvider = new ActiveDataProvider([
            'query' => $ownerUserQuery
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $ownerUserQuery->andFilterWhere([
            'id' => $this->id,
            'last_checked_at' => $this->last_checked_at,
        ]);

        $ownerUserQuery->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
