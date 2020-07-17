<?php

namespace frontend\controllers;

use common\exceptions\SearchResult\ProcessAlreadyRunningException;
use common\models\form\SnapshotForm;
use common\models\Project;
use common\models\ProjectNotification;
use common\models\SearchResultItem;
use common\services\ProjectService;
use common\services\SearchResultsService;
use frontend\components\accessFilters\ProjectOwnerAccessFilter;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UnprocessableEntityHttpException;

/**
 * Контроллер поисковой выдачи.
 */
class SnapshotsController extends Controller
{
    /**
     * @var ProjectService
     */
    private $projectService;

    /**
     * @var SearchResultsService
     */
    private $searchResultsService;


    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access'        => [
                'class' => AccessControl::class,
                'only'  => [
                    'index',
                    'refresh',
                    'check-status',
                ],
                'rules' => [
                    [
                        'actions' => ['index', 'refresh', 'check-status'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
            'projectAccess' => [
                'class'        => ProjectOwnerAccessFilter::class,
                'only'         => ['index', 'refresh', 'check-status'],
                'queryParamId' => 'project_id',
            ],
            'verbs'         => [
                'class'   => VerbFilter::class,
                'actions' => [
                    'index' => ['GET', 'POST'],
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
        ProjectService $projectService,
        SearchResultsService $searchResultsService,
        $config = []
    )
    {
        parent::__construct($id, $module, $config);

        $this->searchResultsService = $searchResultsService;
        $this->projectService       = $projectService;
    }


    /**
     * Отображение поисковой выдачи по выбранному проекту.
     *
     * @param $project_id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionIndex($project_id)
    {
        $project = $this->findProject($project_id);

        $form = new SnapshotForm($project);
        $form->loadDefaults();

        $form->load(Yii::$app->request->get());
        $form->search();

        // дата последней проверки
        $lastUpdate = $project
            ->getSearchResults()
            ->orderBy(['updated_at' => SORT_DESC])
            ->select('date')
            ->scalar();

        $notification = ProjectNotification::find()->byProjectId($project->id)->one();
        if ($notification === null) {
            $notification             = new ProjectNotification();
            $notification->project_id = $project->id;
        }
        if ($notification->load(Yii::$app->request->post())) {
            $notification->save();
        }

        return $this->render('index', [
            'model'        => $form,
            'canRefresh'   => !$project->hasProcessedSearchResults(),
            'lastUpdate'   => $lastUpdate,
            'notification' => $notification
        ]);
    }


    /**
     * Запуск загрузки данных выдачи.
     *
     * @param int $project_id
     * @return Response
     * @throws NotFoundHttpException
     * @throws UnprocessableEntityHttpException
     */
    public function actionRefresh(int $project_id): Response
    {
        $project    = $this->findProject($project_id);
        $statusCode = 201;

        try {
            $this->searchResultsService->create($project);
        } catch (\RuntimeException $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        } catch (ProcessAlreadyRunningException $e) {
            // когда загрузка данных уже была запущена - никаких действий не требуется
        }

        return $this->asJson(null)->setStatusCode($statusCode);
    }


    /**
     * Получение статуса загрузки выдачи.
     *
     * @param int $project_id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionCheckStatus(int $project_id): Response
    {
        $project      = $this->findProject($project_id);
        $searchResult = $this->searchResultsService->getLastCreatedResult($project);

        return $this->asJson($searchResult);
    }


    /**
     * @param $id
     * @return Project
     * @throws NotFoundHttpException
     */
    private function findProject($id): Project
    {
        $project = $this->projectService->findProjectById($id);

        if ($project === null) {
            throw new NotFoundHttpException('Запрошенный проект не найден.');
        }

        return $project;
    }
}
