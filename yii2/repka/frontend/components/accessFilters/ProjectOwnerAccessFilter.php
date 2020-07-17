<?php

namespace frontend\components\accessFilters;

use common\models\Project;
use common\models\User;
use frontend\services\ProjectAccessService;
use Yii;
use yii\base\ActionFilter;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;

/**
 * Фильтрация доступа пользователя к проекту.
 * Разрешает доступ если пользователь является владельцем.
 *
 * Для подключения укажите класс фильтра в методе behaviors контроллера.
 *
 * 'projectAccess' => [
 *      'class' => ProjectOwnerAccessFilter::class,
 *      'only' => ['view', 'update', 'delete'],
 *      'queryParamId' => 'id',
 * ],
 */
class ProjectOwnerAccessFilter extends ActionFilter
{
    /**
     * @var ProjectAccessService
     */
    private $projectAccessService;

    /**
     * @var string Название параметра запроса отвечающее за получение идентификатора.
     */
    public $queryParamId = 'id';

    /**
     * @inheritdoc
     */
    public function __construct(ProjectAccessService $projectAccessService, $config = [])
    {
        $this->projectAccessService = $projectAccessService;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $this->validateEntity($this->getProject());

        return parent::beforeAction($action);
    }

    /**
     * Возвращаем проверяемый проект.
     * @return Project
     * @throws HttpException
     */
    public function getProject(): Project
    {
        $projectId = Yii::$app->getRequest()->getBodyParam(
            'id',
            Yii::$app->getRequest()->getQueryParam($this->queryParamId)
        );

        if (empty($entity = Project::findOne(['id' => $projectId]))) {
            throw new ForbiddenHttpException('Запрошенный объект не найден.');
        }

        return $entity;
    }

    /**
     * Функция проверки доступа к объекту
     * @param Project $project
     * @throws HttpException
     */
    protected function validateEntity(Project $project)
    {
        $user = User::findOne(Yii::$app->user->id);
        if (!$this->projectAccessService->hasOwnerAccess($user, $project)) {
            throw new HttpException(403, 'Доступ запрещен.');
        }
    }
}