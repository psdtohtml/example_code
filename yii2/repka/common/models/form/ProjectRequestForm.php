<?php

namespace common\models\form;

use common\components\validators\ArrayValidator;
use common\models\Project;
use common\models\ProjectRequest;
use frontend\services\ProjectRequestService;
use Yii;
use yii\base\Model;

/**
 * Модель формы запросов проекта.
 *
 * @property array $requests
 * @property array $requestsForDelete
 * @property Project $project
 */
class ProjectRequestForm extends Model
{
    public $requests;
    public $project;
    public $requestsForDelete;

    /**
     * @var ProjectRequestService
     */
    private $projectRequestService;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['requests', ArrayValidator::class, 'max' => 10]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->projectRequestService = Yii::$container->get(ProjectRequestService::class);
        $this->requests[] = new ProjectRequest();
        $count = 3;
        for ($i = 1; $i < $count; $i++) {
            $this->requests[] = new ProjectRequest(['scenario' => ProjectRequest::SCENARIO_ADDITIONAL]);
        }
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'requests' => 'Запрос',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function load($data, $formName = null)
    {
        if (!isset($data['ProjectRequest'])) {
            return false;
        }

        $this->requests = [];

        foreach ($data['ProjectRequest'] as $key => $item) {
            if (empty($item['request'])) {
                continue;
            }

            if (!empty($item['id'])) {
                $requestModel = $this->projectRequestService->getRequest($item['id'], $this->project);
                $requestModel->request = $item['request'];
                $this->requests[] = $requestModel;
            } else {
                $this->requests[] = new ProjectRequest([
                    'request' => $item['request']
                ]);
            }
        }

        return !empty($this->requests);
    }

    /**
     * {@inheritdoc}
     */
    public function validate($attributeNames = null, $clearErrors = true)
    {
        return parent::validate($attributeNames, $clearErrors) && ProjectRequest::validateMultiple($this->requests);
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
        $this->projectRequestService->setProjectRequests($this->project, $this->requests);
    }
}
