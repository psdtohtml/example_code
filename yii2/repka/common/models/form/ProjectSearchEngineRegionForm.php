<?php

namespace common\models\form;

use common\models\Project;
use common\models\ProjectRegion;
use common\models\ProjectSearchEngine;
use common\services\ProjectService;
use Yii;
use yii\base\Model;

/**
 * Модель формы поисковых систем и регионов.
 *
 * @property integer $projectId
 * @property ProjectRegionForm $regions
 * @property ProjectSearchEngineForm $searchEngines
 */
class ProjectSearchEngineRegionForm extends Model
{
    /**
     * @var Project
     */
    public $project;
    /**
     * @var ProjectRegionForm
     */
    public $regions;
    /**
     * @var ProjectSearchEngineForm
     */
    public $searchEngines;
    /**
     * @var ProjectService
     */
    private $projectService;

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
        $this->projectService = Yii::$container->get(ProjectService::class);

        $this->regions = new ProjectRegionForm();
        $this->searchEngines = new ProjectSearchEngineForm();

        $this->regions->projectId = $this->searchEngines->projectId = $this->project->id;
        $this->regions->regions = $this->projectService->getCodeRegions($this->project);
        $this->searchEngines->selected = $this->projectService->getSelectedSearchEngines($this->project);

        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function load($data, $formName = null)
    {
        if (!isset($data['projectRegion']) ||
            !isset($data['ProjectSearchEngineForm']) ||
            !isset($data['ProjectSearchEngineForm']['selected'])
        ) {
            return false;
        }

        $this->regions->regions = [];

        foreach ($data['projectRegion'] as $region) {
            $projectRegion = new ProjectRegion($region);
            $projectRegion->project_id = $this->project->id;
            $this->regions->regions[] = $projectRegion;
        }

        $this->searchEngines->selected = [];

        foreach ($data['ProjectSearchEngineForm']['selected'] as $index => $searchEngineId) {
            $this->searchEngines->selected[] = new ProjectSearchEngine([
                'project_id' => $this->project->id,
                'name' => $this->projectService->getSearchEngineNameById($searchEngineId),
                'code' => $searchEngineId,
            ]);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($attributeNames = null, $clearErrors = true)
    {
        return ProjectRegion::validateMultiple($this->regions->regions) &&
            ProjectSearchEngine::validateMultiple($this->searchEngines->selected);
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // требуется сначала обновить поисковые системы, т.к. регионы устанавливаются для каждой из них
            $this->projectService->updateSearchEngines($this->project, $this->searchEngines->selected);
            $this->projectService->updateRegions($this->project, $this->regions->regions);
            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'regions' => 'Регионы',
        ];
    }
}
