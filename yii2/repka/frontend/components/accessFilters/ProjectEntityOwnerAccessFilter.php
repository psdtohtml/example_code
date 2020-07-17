<?php

namespace frontend\components\accessFilters;

use common\models\User;
use frontend\services\ProjectAccessService;
use Yii;
use yii\base\ActionFilter;
use yii\db\ActiveRecord;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * Фильтрация доступа пользователя к объектам проекта.
 * Разрешает доступ если пользователь является владельцем проекта к которому относится запращиваемый объект.
 *
 * Для подключения укажите класс фильтра в методе behaviors контроллера.
 *
 * 'projectAccess' => [
 *      'class' => ProjectEntityOwnerAccessFilter::class,
 *      'entityClass' => ProjectRequest:class,
 *      'only' => ['create', 'view', 'update', 'delete'],
 *      'queryParamId' => 'id',
 * ],
 */
class ProjectEntityOwnerAccessFilter extends ActionFilter
{
    /**
     * @var ProjectAccessService
     */
    private $projectAccessService;

    /**
     * ActiveRecord::class который реализует интерфейс IProjectAccessible
     * @var string
     */
    public $entityClass;

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
        /** @var ActiveRecord $object */
        $object = Yii::createObject($this->entityClass);

        $entityId = Yii::$app->getRequest()->getBodyParam(
            'id',
            Yii::$app->getRequest()->getQueryParam($this->queryParamId)
        );

        $entity = $object::findOne($entityId);

        if ($entity === null) {
            throw new NotFoundHttpException('Запрошенный объект не найден.');
        }

        $this->validateEntity($entity);

        return parent::beforeAction($action);
    }

    /**
     * Функция проверки доступа к объекту
     * @param IProjectAccessible $entity
     * @throws HttpException
     * @throws \yii\web\NotFoundHttpException
     */
    protected function validateEntity(IProjectAccessible $entity)
    {
        $user = User::findOne(Yii::$app->user->id);
        if (!$this->projectAccessService->hasAccessToEntity($user, $entity)) {
            throw new HttpException(403, 'Доступ запрещен.');
        }
    }
}