<?php

namespace frontend\services;

use common\models\Project;
use common\models\ProjectRequest;
use common\services\ISeoService;
use RuntimeException;
use Yii;
use yii\web\UnprocessableEntityHttpException;

/**
 * Сервис запросов проекта.
 */
class ProjectRequestService
{
    /**
     * @var ISeoService
     */
    private $seoService;

    /**
     * {@inheritDoc}
     */
    public function __construct(ISeoService $seoService)
    {
        $this->seoService = $seoService;
    }

    /**
     * Устанавливает набор поисковых запросов.
     *
     * @param Project $project
     * @param ProjectRequest[] $requests
     * @return void
     * @throws \Exception
     */
    public function setProjectRequests(Project $project, array $requests): void
    {
        try {
            $transaction = Yii::$app->db->beginTransaction();

            foreach ($requests as $request) {
                if (!($request instanceof ProjectRequest)) {
                    throw new RuntimeException('Переданные данные не являются корректными.');
                }
                $request->request = mb_strtolower(trim($request->request));
                $request->project_id = $project->id;
                $request->save();
            }

            $transaction->commit();

        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        if ($project->service_id) {
            $this->seoService->setProjectKeyWords($project);
        }
    }


    /**
     * Возвращает запрос по идентификатору запроса и проекта.
     *
     * @param int $requestId
     * @param Project $project
     * @return ProjectRequest|null
     */
    public function getRequest(int $requestId, Project $project): ?ProjectRequest
    {
        return ProjectRequest::findOne(['id' => $requestId, 'project_id' => $project->id]);
    }

    /**
     * Возвращает все запросы по проекту.
     *
     * @param Project $project
     * @return ProjectRequest[]
     */
    public function getRequests(Project $project): array
    {
        return $project->projectRequests;
    }

    /**
     * Удаление запроса проекта.
     *
     * @param ProjectRequest $request
     * @return false|int
     * @throws UnprocessableEntityHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function deleteRequest(ProjectRequest $request)
    {
        if (ProjectRequest::find()->where(['project_id' => $request->project_id])->count() <= 1) {
            throw new UnprocessableEntityHttpException('Произошла ошибка. ' .
                'Проект должен иметь минимум 1 поисковой запрос.');
        }
        return $request->delete();
    }
}
