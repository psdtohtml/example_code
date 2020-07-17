<?php

namespace common\models\form;

use common\models\filters\SearchResultFilter;
use common\models\Project;
use common\models\ProjectRegion;
use common\models\ProjectSearchEngine;
use common\services\SearchResultsService;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Модель формы поисковой выдачи.
 *
 * @property Project $project
 * @property ProjectRegion[] $projectRegions
 * @property ProjectSearchEngine[] $projectSearchEngines
 * @property int $region
 * @property int $searchEngine
 * @property int $dateFrom
 * @property int $dateTo
 */
class SnapshotForm extends Model
{
    /**
     * @var Project
     */
    public $project;

    /**
     * @var array регионы проекта.
     */
    public $projectRegions;

    /**
     * @var array поисковые системы проекта.
     */
    public $projectSearchEngines;

    /**
     * @var int регион.
     */
    public $region;

    /**
     * @var int поисковая система.
     */
    public $searchEngine;

    /**
     * @var int - дата начала.
     */
    public $dateFrom;

    /**
     * @var int - дата окончания.
     */
    public $dateTo;

    /**
     * @var string
     */
    public $dateRange;

    /**
     * @var array [int => ['request' => ProjectRequest, 'items' => SearchResultItem[]]]
     */
    public $results;

    /**
     * @var SearchResultsService
     */
    private $searchResultsService;

    /**
     * @var SearchResultFilter
     */
    private $resultsFilter;

    /** @var null|array */
    private $hostsProjectList = null;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dateFrom', 'dateTo'], 'date', 'format' => 'dd-MM-yyyy'],
            [['region', 'searchEngine'], 'integer'],
            ['dateRange', 'safe'],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function __construct(Project $project, $config = [])
    {
        $this->project = $project;
        parent::__construct($config);
    }

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        $this->projectSearchEngines = array_reverse($this->project->projectSearchEngines);
        $this->projectRegions = ArrayHelper::map($this->project->projectRegions, 'id', 'region');
        $this->searchResultsService = Yii::$container->get(SearchResultsService::class);

        parent::init();
    }

    /**
     * {@inheritDoc}
     */
    public function attributeLabels()
    {
        return [
            'region' => 'Регион',
            'dateFrom' => 'От',
            'dateTo' => 'До',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function load($data, $formName = null)
    {
        $loadSuccess = parent::load($data, $formName);

        if ($loadSuccess) {
            if (empty($this->searchEngine)) {
                $this->searchEngine = $this->projectSearchEngines[0]->id;
            }
            $period = [
                'from' => strtotime($this->dateFrom),
                'to' => strtotime($this->dateTo)
            ];
            $region = ProjectRegion::findOne($this->region);
            $searchEngine = ProjectSearchEngine::findOne($this->searchEngine);

            try {
                $this->resultsFilter = new SearchResultFilter($this->project, $period, $region, $searchEngine);
            } catch (InvalidArgumentException $e) {
                $loadSuccess = false;
            }
        }

        return $loadSuccess;
    }

    /**
     * Ищет данные на основе запроса.
     */
    public function search()
    {
        $this->results = $this->searchResultsService->findSearchResults($this->resultsFilter);
    }

    /**
     * Установка значений по-умолчанию.
     */
    public function loadDefaults(): void
    {
        $lastSearchResult = $this->searchResultsService->getLastCreatedResult($this->project);

        $today = new \DateTime();
        $dateFrom = ($lastSearchResult) ? (new \DateTime())->setTimestamp($lastSearchResult->created_at) : clone $today;
        $this->dateFrom = $dateFrom->format('d.m.Y');
        $this->dateTo = $today->format('d.m.Y');

        $this->dateRange = $dateFrom->format('m.d.y') . ' - ' . $today->format('m.d.y');

        if (count($this->projectSearchEngines)) {
            $this->searchEngine = $this->projectSearchEngines[0]->id;
        }

        if (count($this->projectRegions)) {
            $regionsIds = array_keys($this->projectRegions);
            $this->region = array_shift($regionsIds);
        }

        $period = [
            'from' => strtotime($this->dateFrom),
            'to' => strtotime($this->dateTo),
        ];

        $region = ProjectRegion::findOne($this->region);
        $searchEngine = ProjectSearchEngine::findOne($this->searchEngine);

        $this->resultsFilter = new SearchResultFilter($this->project, $period, $region, $searchEngine);
    }


    public function inHostList($domain): bool
    {
        return $this->project->inHostList($domain);
    }
}
