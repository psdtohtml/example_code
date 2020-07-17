<?php

namespace frontend\controllers;

use common\forms\project\ProjectHostsForm;
use common\models\form\ProjectMarkerDomainsForm;
use common\models\form\ProjectMarkerForm;
use common\models\form\ProjectRequestForm;
use common\models\form\ProjectSearchEngineRegionForm;
use common\models\Project;
use common\models\ProjectMarker;
use common\models\SharedProject2User;
use common\services\ProjectService;
use frontend\components\accessFilters\ProjectOwnerAccessFilter;
use frontend\services\ProjectRequestService;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * Контроллер настроек проекта.
 */
class ProjectSettingsController extends Controller
{
    public $layout = 'projects';
    /**
     * @var ProjectRequestService
     */
    private $projectRequestService;
    /**
     * @var ProjectService
     */
    private $projectService;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => [
                    'requests', 'search-engines-regions', 'share-project'
                ],
                'rules' => [
                    [
                        'actions' => ['requests', 'search-engines-regions', 'share-project'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'projectAccess' => [
                'class' => ProjectOwnerAccessFilter::class,
                'only' => ['requests', 'search-engines-regions', 'share-project'],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'requests' => ['GET', 'POST'],
                    'search-engines-regions' => ['GET', 'POST'],
                    'share-project' => ['GET', 'POST'],
                ],
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function __construct($id, $module, ProjectRequestService $projectRequestService, ProjectService $projectService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->projectRequestService = $projectRequestService;
        $this->projectService = $projectService;
    }

    /**
     * Список всех запросов проекта.
     *
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
    public function actionRequests($id)
    {
        $project = $this->findProject($id);
        if (!$this->projectService->isOwner($project, Yii::$app->getUser()->getIdentity())) {
            return $this->render('view-requests', [
                'model' => $project,
            ]);
        }

        $form = new ProjectRequestForm();
        $form->project = $project;
        $form->requests = $this->projectRequestService->getRequests($form->project);

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $form->save();
            return $this->redirect(['projects/index']);
        }

        return $this->render('requests', [
            'model' => $form,
            'projectModel' => $this->findProject($id)
        ]);
    }

    /**
     * Настройка поисковых систем и регионов проекта.
     *
     * @param $id
     * @return string|\yii\web\Response
     * @throws \Throwable
     */
    public function actionSearchEnginesRegions($id)
    {
        $project = $this->findProject($id);

        if (!$this->projectService->isOwner($project, Yii::$app->getUser()->getIdentity())) {
            return $this->render('view-search-engines-regions', [
                'model' => $project,
            ]);
        }

        $form = new ProjectSearchEngineRegionForm($project);

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $form->save();
            return $this->redirect(['projects/index']);
        }

        return $this->render('search-engines-regions', [
            'model' => $form,
            'projectModel' => $project
        ]);
    }

    /**
     * Настройка маркировок площадок проекта.
     *
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
    public function actionMarkers($id)
    {
        $project = $this->findProject($id);

        if (!$this->projectService->isOwner($project, Yii::$app->getUser()->getIdentity())) {
            return $this->render('view-markers', [
                'model' => $project,
            ]);
        }

        $form = new ProjectMarkerForm($project);
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $form->save();
            return $this->redirect(['projects/index']);
        }

        return $this->render('markers', [
            'model' => $form,
            'projectModel' => $project
        ]);
    }

    /**
     * Совладельцы проекта.
     *
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
    public function actionShareProject($id)
    {
        $project = $this->findProject($id);
        if (!$this->projectService->isOwner($project, Yii::$app->getUser()->getIdentity())) {
            return $this->render('view-share-project', [
                'model' => $project,
            ]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => SharedProject2User::find()->with('user')->andWhere(['project_id' => $id]),
            'pagination' => false
        ]);

        return $this->render('share-project', [
            'dataProvider' => $dataProvider,
            'projectModel' => $project
        ]);
    }

    /**
     * Список площадок.
     *
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionMarkersDomains($id)
    {
        $project = $this->findProject($id);
        $models = [];

        foreach ($project->getProjectMarkers()->indexBy('id')->each() as $marker) {
            $models[$marker->id] = $this->createMarkerDomainsForm($marker);
        }

        $projectHostsForm = new ProjectHostsForm([], $project->id);
        return $this->render('@common/templates/project-settings/markers-domains', [
            'projectModel' => $project,
            'models' => $models,
            'projectHostsForm' => $projectHostsForm
        ]);
    }
    
    /**
     * Находит проект по идентификатору.
     *
     * @param $id
     * @return Project
     * @throws NotFoundHttpException
     */
    private function findProject($id): Project
    {
        if (($model = Project::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Пользователь не найден.');
    }

    /**
     * Создание модели формы списка площадок для маркера.
     *
     * @param ProjectMarker $marker
     * @return ProjectMarkerDomainsForm
     */
    private function createMarkerDomainsForm(ProjectMarker $marker): ProjectMarkerDomainsForm
    {
        return ProjectMarkerDomainsForm::createByMarker(
            $marker,
            ArrayHelper::getValue(Yii::$app->params, 'maxProjectMarkerDomains', 1)
        );
    }
}
