<?php

namespace frontend\controllers;

use common\models\form\ProjectCreateForm;
use common\models\form\ProjectRegionForm;
use common\models\form\ProjectRequestForm;
use common\services\ISeoService;
use common\services\ProjectService;
use Exception;
use frontend\components\accessFilters\ProjectOwnerAccessFilter;
use Yii;
use common\models\Project;
use common\models\search\ProjectSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\ServerErrorHttpException;

/**
 * Контроллер проектов.
 */
class ProjectsController extends Controller
{
    /**
     * @var ISeoService
     */
    private $seoService;
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
                    'index', 'view', 'update', 'create', 'delete'
                ],
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'update', 'create', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'projectAccess' => [
                'class' => ProjectOwnerAccessFilter::class,
                'only' => ['view', 'update', 'delete'],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'index' => ['GET', 'POST'],
                    'view' => ['GET', 'POST'],
                    'create' => ['GET', 'POST'],
                    'update' => ['GET', 'POST'],
                    'delete' => ['GET', 'POST'],
                ],
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function __construct(
        $id,
        $module,
        $config = [],
        ISeoService $seoService,
        ProjectService $projectService
    ) {
        $this->seoService = $seoService;
        parent::__construct($id, $module, $config);
        $this->projectService = $projectService;
    }

    /**
     * Выводит список проектов.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProjectSearch();
        $dataProvider = $searchModel->searchForUser(Yii::$app->user->identity, Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Отображение проекта.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $this->layout = 'projects';
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Создание проекта.
     * @param string $step - стадия создания проекта.
     * @param null $project_id - идентификатор проекта.
     * @return string|\yii\web\Response
     * @throws \Exception
     */
    public function actionCreate($step = 'project', $project_id = null)
    {
        $form = null;
        switch ($step) {
            // Создание проекта.
            case 'project':
                $form = new ProjectCreateForm();

                if ($project_id) {
                    // заполнение формы сохраненными данными при возврате на предыдущий шаг
                    $form->setProject(
                        Project::find()
                            ->with('projectSearchEngines')
                            ->where(['id' => $project_id])
                            ->one()
                    );
                }

                $form->searchEnginesList = $this->seoService->getSearchEngines();

                if ($form->load(Yii::$app->request->post()) && $form->validate()) {
                    $projectId = $form->id
                        ? $this->projectService->renewalProject($form)->id
                        : $this->projectService->createProject($form)->id;

                    return $this->redirect([
                        'create',
                        'project_id' => $projectId,
                        'step' => 'requests',
                    ]);
                }
                break;
            // Добавление поисковых запросов к проекту.
            case 'requests':
                $form = new ProjectRequestForm();
                if ($form->load(Yii::$app->request->post()) && $form->validate()) {
                    $form->project = $this->findModel($project_id);
                    $form->save(false);

                    return $this->redirect([
                        'create',
                        'project_id' => $project_id,
                        'step' => 'regions'
                    ]);
                }
                break;
            // Указание регионов проекта и публикация проекта.
            case 'regions':
                $form = new ProjectRegionForm();
                if ($form->load(Yii::$app->request->post()) && $form->validate()) {
                    $form->projectId = $project_id;
                    $form->save(false);

                    return $this->redirect([
                        'create',
                        'project_id' => $project_id,
                        'step' => 'ready'
                    ]);
                }
                break;
            case 'ready':
                $project = $this->findModel($project_id);
                try {
                    $this->seoService->createProject($project);
                    $project->status = Project::STATUS_ACTIVE;
                    $project->save();
                } catch (Exception $e) {
                    Yii::error(['Project: ' . $project_id, 'error' => $e->getMessage()]);
                    throw new ServerErrorHttpException($e->getMessage());
                }
                break;
        }

        return $this->render('create/' . $step, [
            'model' => $form,
            'project_id' => $project_id
        ]);
    }

    /**
     * Редактирование.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     */
    public function actionUpdate($id)
    {
        $this->layout = 'projects';
        $model = $this->findModel($id);

        if (!$this->projectService->isOwner($model, Yii::$app->getUser()->getIdentity())) {
            return $this->render('view', [
                'model' => $model,
            ]);
        }

        if ($model->load(Yii::$app->request->post())) {
            $this->projectService->saveEditedProject($model);
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model
        ]);
    }

    /**
     * Удаление.
     * @param integer $id
     * @return mixed
     * @throws HttpException
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     */
    public function actionDelete($id)
    {
        $project = $this->findModel($id);
        $isOwner = $this->projectService->isOwner($project, Yii::$app->getUser()->getIdentity());
        if (!$isOwner) {
            throw new HttpException(403, 'Доступ запрещен.');
        }
        $this->projectService->delete($this->findModel($id));

        return $this->redirect(['index']);
    }

    /**
     * Находит проект по идентификатору.
     * @param integer $id
     * @return Project the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Project::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Запрошенный проект не найден..');
    }
}
