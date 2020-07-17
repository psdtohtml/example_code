<?php

namespace common\models\query;

use yii\db\ActiveQuery;

/**
 * ActiveQuery класс для [[\common\models\SystemSettings]].
 *
 * @see \common\models\SystemSettings
 */
class SystemSettingsQuery extends ActiveQuery
{
    /**
     * Фильтрация по имени ключа настройки.
     *
     * @param string $key ключ настройки
     * @return \common\models\query\SystemSettingsQuery
     */
    public function withKey(string $key): SystemSettingsQuery
    {
        return $this->andWhere(['key' => $key]);
    }
}
