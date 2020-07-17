<?php

namespace common\models\filters;

use common\models\Project;
use common\models\ProjectRegion;
use common\models\ProjectSearchEngine;
use yii\base\InvalidArgumentException;

/**
 * Объект фильтра для конфигурации запроса на поисковые выдачи.
 */
class SearchResultFilter
{
    /**
     * @var \common\models\Project проект
     */
    private $project;

    /**
     * @var array период ['from' => int, 'to' => int]
     */
    private $period;

    /**
     * @var \common\models\ProjectRegion регион
     */
    private $region;

    /**
     * @var \common\models\ProjectSearchEngine поисковая система
     */
    private $searchEngine;

    /**
     * Конструктор.
     *
     * @param \common\models\Project $project проект
     * @param array $period период (['from' => int, 'to' => int])
     * @param \common\models\ProjectRegion $region
     */
    public function __construct(
        Project $project,
        array $period,
        ProjectRegion $region,
        ProjectSearchEngine $searchEngine
    ) {
        if (!isset($period['from']) && !isset($period['to'])) {
            throw new InvalidArgumentException('Параметр "Период" фильтра должен содержать поля from и to.');
        }

        if (empty($period['from'])) {
            throw new InvalidArgumentException('Параметр "Период" фильтра не содержит значение для поля from.');
        }

        if (empty($period['to'])) {
            throw new InvalidArgumentException('Параметр "Период" фильтра не содержит значение для поля to.');
        }

        if ($period['from'] > $period['to']) {
            $to = $period['from'];
            $from = $period['to'];

            $period = [
                'from' => $from,
                'to' => $to,
            ];
        }

        $this->project = $project;
        $this->period = $period;
        $this->region = $region;
        $this->searchEngine = $searchEngine;
    }

    /**
     * Возвращает проект.
     *
     * @return \common\models\Project
     */
    public function getProject(): Project
    {
        return $this->project;
    }

    /**
     * Возвращает период.
     *
     * @return array
     */
    public function getPeriod(): array
    {
        return $this->period;
    }

    /**
     * Возвращает регион.
     *
     * @return ProjectRegion
     */
    public function getRegion(): ProjectRegion
    {
        return $this->region;
    }

    /**
     * Возвращает поисковую систему.
     *
     * @return \common\models\ProjectSearchEngine
     */
    public function getSearchEngine()
    {
        return $this->searchEngine;
    }

    /**
     * @param ProjectSearchEngine $searchEngine
     */
    public function setSearchEngine(ProjectSearchEngine $searchEngine): void
    {
        $this->searchEngine = $searchEngine;
    }


}
