<?php

namespace common\services;

/**
 * Данные статистики.
 */
class StatisticsData
{
    /**
     * @var int - кол-во проектов в системе.
     */
    public $totalProjectsCount;

    /**
     * @var int - кол-во проверок выдачи за последние N дней.
     */
    public $lastSearchCheckCount;

    /**
     * @var int - количество пользователей в системе.
     */
    public $totalUsersCount;

    /**
     * @var int - количество новых пользователей за N дней.
     */
    public $lastUsersCount;
}
