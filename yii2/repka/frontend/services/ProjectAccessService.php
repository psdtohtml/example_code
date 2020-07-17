<?php

namespace frontend\services;

use common\models\Project;
use common\models\SharedProject2User;
use common\models\User;
use frontend\components\accessFilters\IProjectAccessible;

/**
 * Сервис проверки доступа к проекту.
 */
class ProjectAccessService
{
    /**
     * Проверяет имеет ли пользователь права изменять проект.
     *
     * @param User $user
     * @param Project $project
     * @return bool
     */
    public function hasOwnerAccess(User $user, Project $project): bool
    {
        return Project::find()->andWhere(['id' => $project->id, 'user_id' => $user->id])->exists() ||
            SharedProject2User::find()->andWhere(['project_id' => $project->id, 'user_id' => $user->id])->exists();
    }

    /**
     * Проверяет имеет ли пользователь права на изменение объекта.
     *
     * @param User $user
     * @param IProjectAccessible $object
     * @return bool
     */
    public function hasAccessToEntity(User $user, IProjectAccessible $object): bool
    {
        $project = $object->getOwnerProject();

        return Project::find()->andWhere(['id' => $project->id, 'user_id' => $user->id])->exists();
    }
}
