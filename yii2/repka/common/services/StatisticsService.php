<?php

namespace common\services;

use common\models\Project;
use common\models\SearchResult;
use common\models\Statistic;
use common\models\User;

/**
 * Сервис статистики.
 */
class StatisticsService
{
    /**
     * Возвращает полную статистику по проекту.
     *
     * @return StatisticsData
     */
    public function getFullStatistics(): StatisticsData
    {
        $data = new StatisticsData();
        $data->totalProjectsCount = $this->getTotalProjectsCount();
        $data->totalUsersCount = $this->getTotalUsersCount();
        $data->lastSearchCheckCount = $this->getLastSearchCheckCount();
        $data->lastUsersCount = $this->getLastUsersCount();

        return $data;
    }

    /**
     * Возвращает кол-во проектов в системе.
     *
     * @return int
     */
    public function getTotalProjectsCount(): int
    {
        return Project::find()->count();
    }

    /**
     * Возвращает кол-во проверок выдачи за последние N дней.
     *
     * @param int $days
     * @return int
     */
    public function getLastSearchCheckCount(int $days = 7): int
    {
        return SearchResult::find()->andWhere([
            '>',
            'created_at',
            strtotime("- $days days")
        ])->count();
    }

    /**
     * Возвращает количество пользователей в системе.
     *
     * @return int
     */
    public function getTotalUsersCount(): int
    {
        return User::find()->active()->count();
    }

    /**
     * Возвращает количество новых пользователей за N дней.
     *
     * @param int $days
     * @return int
     */
    public function getLastUsersCount(int $days = 7): int
    {
        return User::find()->active()->andWhere([
            '>',
            'created_at',
            strtotime("- $days days")
        ])->count();
    }

    /**
     * Получение статистики за N дней (для графика)
     * @param int $days
     * @return array
     */
    public function getStatisticsDataChart($days = 7)
    {
        return $result = Statistic::find()->andWhere([
            '>',
            'date',
            date("Y-m-d", strtotime("- $days days"))
        ])->asArray()->all();
    }

    /**
     * Обновление статистики по проектам
     */
    public function updateProjectsStatistic()
    {
        $statistic = $this->getCurrentDailyStatistics();
        $statistic->projects_count = $this->getTotalProjectsCount();
        $statistic->save();
    }

    /**
     * Обнолвение статистики по пользователям
     */
    public function updateUsersStatistic()
    {
        $statistic = $this->getCurrentDailyStatistics();
        $statistic->users_count = $this->getTotalUsersCount();
        $statistic->save();
    }

    /**
     * Получаем либо текущую модель для статистики, либо создаем новую
     *
     * @return Statistic
     */
    private function getCurrentDailyStatistics()
    {
        $date = date("Y-m-d");
        if (!$statistic = Statistic::find()->andWhere(['date' => $date])->one()) {
            $statistic = new Statistic();
            $statistic->date = $date;
        }

        return $statistic;
    }
}
