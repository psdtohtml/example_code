<?php

namespace frontend\components\accessFilters;

use common\models\Project;

/**
 * Интерфейс представляет собой структуру необходимую для работы с фильтром доступа к проекту и его сущностей.
 */
interface IProjectAccessible
{
    /**
     * Возвращает проект - владелец записи.
     * @return Project
     */
    public function getOwnerProject(): Project;
}
