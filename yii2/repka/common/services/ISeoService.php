<?php

namespace common\services;

use common\models\Project;
use common\models\SearchResult;

interface ISeoService
{
    /** @var int Идентификатор Google */
    public const SEARCH_ENGINE_GOOGLE = 1;
    /** @var int Идентификатор Яндекс */
    public const SEARCH_ENGINE_YANDEX = 2;

    /**
     * Конструктор.
     * @param \common\services\SystemSettingsService $settingsService
     */
    public function __construct(SystemSettingsService $settingsService);

    /**
     * Возвращает доступный набор поисковых систем.
     *
     * @return array [['id' => int, 'code' => string, 'name' => string], ...]
     */
    public function getSearchEngines(): array;

    /**
     * Преобразование внутреннего идентификатора поисковой системы в формат внешнего сервиса.
     *
     * @param int $engineId
     * @return string
     */
    public function convertSearchEngineToExternal(int $engineId): string;

    /**
     * Получение идентификатора поисковой системы по коду внешнего сервиса.
     *
     * @param string $engineCode
     * @return int
     */
    public function convertSearchEngineFromExternal(string $engineCode): int;

    /**
     * Поиск регионов по вхождению наименования.
     *
     * @param string $term имя или часть имени региона
     * @param int $limit [10] максимальное количество записей в ответе (от 1 до 100, если указанное значение
     * не входит в этот интервал, будет назначено значение 10)
     * @return array [['id', 'countryCode', 'type', 'areaName', 'name', 'name_ru', 'areaName_ru',
     * 'name_en', 'areaName_en']]
     * @throws \Exception
     */
    public function findRegions(string $term, int $limit = 10): array;

    /**
     * Создание нового проекта.
     * Пробует создать проект в сервисе и присваевает запрошенному проекту идентификатор, назначенный сервисом.
     *
     * @param \common\models\Project $project проект
     * @throws \Exception
     */
    public function createProject(Project $project): void;

    /**
     * Переименование проекта.
     *
     * @param \common\models\Project $project
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function renameProject(Project $project): void;

    /**
     * Удаление запрошенного проекта.
     *
     * @param \common\models\Project $project
     * @throws \Exception
     */
    public function removeProject(Project $project): void;

    /**
     * Сохраняет набор поисковых систем для проекта.
     * Метод производит сравнение набора поисковых систем, полученнх от сервиса с набором
     * локально сохранённых поисковых систем проекта. Лишние удаляются, отсутствующие добавляются.
     *
     * @param \common\models\Project $project
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function setProjectSearchEngines(Project $project): void;

    /**
     * Сохраняет набор регионов для проекта.
     * Метод производит сравнение набора регионов всех происковых систем проекта, полученнх от сервиса с набором
     * локально сохранённых регионов проекта. Лишние удаляются, отсутствующие добавляются.
     *
     * @param \common\models\Project $project
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function setProjectRegions(Project $project): void;

    /**
     * Сохраняет набор запросов (ключевых слов) для проекта.
     * Метод производит сравнение набора регионов всех происковых систем проекта, полученнх от сервиса с набором
     * локально сохранённых регионов проекта. Лишние удаляются, отсутствующие добавляются.
     *
     * @param \common\models\Project $project
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function setProjectKeyWords(Project $project): void;

    /**
     * Возвращает остатки средств на балансе сервиса.
     *
     * @return float
     */
    public function getAccountBalance(): float;

    /**
     * Инициализация формирования данных поисковой выдачи.
     *
     * @param \common\models\SearchResult $searchResult
     * @return void
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function checkPositions(SearchResult $searchResult): void;

    /**
     * Возвращает данные о готовности формирования данных поисковой выдачи.
     *
     * @param \common\models\Project $project проект
     * @return array ['in_process' => bool, 'ready_percentage' => int]
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function getPositionsBuildingState(Project $project): array;

    /**
     * Возвращает данные поисковой выдачи.
     *
     * @param \common\models\SearchResult $searchResult
     * @return array [['keyWord' => string, 'searcherCode' => int, 'regionName' => string, 'regionCode' => int,
     *      'date' => "Y-m-d", 'position' => int, 'url' => string, 'domain' => string], ...]
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function getSearchResults(SearchResult $searchResult): array;
}
