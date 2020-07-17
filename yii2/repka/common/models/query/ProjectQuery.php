<?php

namespace common\models\query;

use common\models\Project;
use yii\db\ActiveQuery;
use yii\web\IdentityInterface;

/**
 * ActiveQuery класс для [[\common\models\Project]].
 *
 * @see \common\models\Project
 */
class ProjectQuery extends ActiveQuery
{
    /**
     * Возвращает только активные проекты.
     *
     * @return \common\models\query\ProjectQuery
     */
    public function active(): ProjectQuery
    {
        return $this->andWhere(['status' => Project::STATUS_ACTIVE]);
    }

    /**
     * Возвращает проекты пользователя.
     *
     * @param IdentityInterface $user
     * @return ProjectQuery
     */
    public function withUser(IdentityInterface $user): ProjectQuery
    {
        return $this->andWhere(['user_id' => $user->getId()]);
    }
}
