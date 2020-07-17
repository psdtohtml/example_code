<?php

namespace common\services;

use common\models\form\ProjectCreateForm;
use common\models\Project;
use common\models\SharedProject2User;
use common\models\ProjectRegion;
use common\models\ProjectSearchEngine;
use common\models\User;
use RuntimeException;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\UnprocessableEntityHttpException;


/**
 * Сервис проекта.
 */
class ProjectService
{
    /**
     * @var TopVisorService
     */
    private $seoService;
    /**
     * @var ProjectMarkersService
     */
    private $markersService;

    /**
     * {@inheritDoc}
     */
    public function __construct(ISeoService $seoService, ProjectMarkersService $markersService)
    {
        $this->seoService = $seoService;
        $this->markersService = $markersService;
    }

    /**
     * Находит проект по идентификатору.
     *
     * @param $id
     * @return Project|null
     */
    public function findProjectById($id): ?Project
    {
        return Project::findOne($id);
    }

    /**
     * Создает проект.
     *
     * @param ProjectCreateForm $form
     * @return Project
     * @throws \Exception
     */
    public function createProject(ProjectCreateForm $form): Project
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $project = new Project([
                'name' => $form->name,
            ]);
            $project->save();

            foreach ($form->selectedSearchEngines as $searchEngine) {
                $searchEngineModel = new ProjectSearchEngine([
                    'project_id' => $project->id,
                    'code' => $searchEngine,
                    'name' => $form->searchEnginesList[$searchEngine]['name'],
                ]);
                $searchEngineModel->save();
            }

            $this->markersService->createDefaultValues($project);
            $transaction->commit();
            return $project;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * Изменение проекта на этапе создания.
     *
     * @param ProjectCreateForm $form
     * @return Project
     * @throws \Exception
     */
    public function renewalProject(ProjectCreateForm $form): Project
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $project = Project::find()->with('projectSearchEngines')->where(['id' => $form->id])->one();
            $project->name = $form->name;
            $project->save();

            $deletedEngines = array_diff(
                ArrayHelper::getColumn($project->projectSearchEngines, 'code'),
                $form->selectedSearchEngines
            );

            ProjectSearchEngine::deleteAll(['project_id' => $project->id, 'code' => $deletedEngines]);

            $addedEngines = array_diff(
                $form->selectedSearchEngines,
                ArrayHelper::getColumn($project->projectSearchEngines, 'code')
            );

            foreach ($addedEngines as $searchEngine) {
                $searchEngineModel = new ProjectSearchEngine([
                    'project_id' => $project->id,
                    'code' => $searchEngine,
                    'name' => $form->searchEnginesList[$searchEngine]['name'],
                ]);
                $searchEngineModel->save();
            }

            $transaction->commit();
            return $project;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * Сохраненяет проект после его редактирования.
     *
     * @param Project $project
     * @return bool
     * @throws \Exception
     */
    public function saveEditedProject(Project $project): bool
    {
        if (!$project->save()) {
            throw new RuntimeException('Произошла ошибка при сохранении проекта.');
        }
        $this->seoService->renameProject($project);

        return true;
    }


    /**
     * Возвращает поисковую систему проекта, запрошенную по идентификатору.
     *
     * @param \common\models\Project $project
     * @param int $projectSearchEngineId
     * @return \common\models\ProjectSearchEngine|null
     */
    public function getSearchEngine(Project $project, int $projectSearchEngineId)
    {
        return $project->getProjectSearchEngines()
            ->andWhere(['{{%project_search_engines}}.id' => $projectSearchEngineId])
            ->one();
    }

    /**
     * Возвращает регион проекта, запрошенную по идентификатору.
     *
     * @param \common\models\Project $project
     * @param int $projectRegionId
     * @return \common\models\ProjectRegion|null
     */
    public function getRegion(Project $project, int $projectRegionId)
    {
        return $project->getProjectRegions()
            ->andWhere(['{{%project_regions}}.id' => $projectRegionId])
            ->one();

    }

    /**
     * Удаляет проект.
     *
     * @param Project $project
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function delete(Project $project): void
    {
        if (!$project->delete()) {
            throw new RuntimeException('Произошла ошибка при удалении проекта.');
        }
        $this->seoService->removeProject($project);
    }

    /**
     * Возвращает выбранные поисковые системы для проекта.
     *
     * @param Project $project
     * @return ProjectSearchEngine[]
     */
    public function getSelectedSearchEngines(Project $project): array
    {
        $data = [];
        foreach (ProjectSearchEngine::findAll(['project_id' => $project->id]) as $searchEngine) {
            $data[] = (int)$searchEngine->code;
        }

        return $data;
    }

    /**
     * Возвращает ассоциативный массив регионов, где ключ - код региона, значение - название региона и города.
     *
     * @param Project $project
     * @return array
     */
    public function getCodeRegions(Project $project): array
    {
        return ArrayHelper::map($project->projectRegions, 'code', 'region');
    }

    /**
     * Возвращает название поисковой системы.
     *
     * @param int $id
     * @return string|bool
     */
    public function getSearchEngineNameById(int $id): string
    {
        $searchEngines = ArrayHelper::map($this->seoService->getSearchEngines(), 'id', 'name');

        return isset($searchEngines[$id]) ? $searchEngines[$id] : false;
    }

    /**
     * Обновляет информацию о регионах.
     *
     * @param Project $project
     * @param $regions
     * @return void
     * @throws \Exception
     */
    public function updateRegions(Project $project, $regions): void
    {
        $currentProjectRegions = ArrayHelper::index($project->projectRegions, 'code');
        $needProjectRegions = ArrayHelper::index($regions, 'code');

        $removeItems = array_diff(array_keys($currentProjectRegions), array_keys($needProjectRegions));
        $insertItems = array_diff(array_keys($needProjectRegions), array_keys($currentProjectRegions));

        ProjectRegion::deleteAll(['code' => array_values($removeItems), 'project_id' => $project->id]);

        foreach ($insertItems as $item) {
            $needProjectRegions[$item]->save();
        }

        if ($project->service_id) {
            // обновление данных проекта для корректного получения измененного набора регионов
            $project->refresh();
            $this->seoService->setProjectRegions($project);
        }
    }

    /**
     * Обновляет информацию о поисковых системах проекта.
     *
     * @param Project $project
     * @param $searchEngines
     * @return void
     * @throws \Exception
     */
    public function updateSearchEngines(Project $project, $searchEngines): void
    {
        $currentSearchEngines = ArrayHelper::index($project->projectSearchEngines, 'code');
        $needSearchEngines = ArrayHelper::index($searchEngines, 'code');

        $removeItems = array_diff(array_keys($currentSearchEngines), array_keys($needSearchEngines));
        $insertItems = array_diff(array_keys($needSearchEngines), array_keys($currentSearchEngines));

        ProjectSearchEngine::deleteAll(['code' => array_values($removeItems), 'project_id' => $project->id]);

        foreach ($insertItems as $item) {
            $needSearchEngines[$item]->save();
        }

        if ($project->service_id) {
            // обновление данных проекта для корректного получения измененного набора поисковых систем
            $project->refresh();
            $this->seoService->setProjectSearchEngines($project);
        }
    }

    /**
     * Создает совладельца у проекта.
     *
     * @param Project $project
     * @param User $user
     * @return bool|SharedProject2User
     * @throws UnprocessableEntityHttpException
     */
    public function createCoOwner(Project $project, User $user)
    {
        if ($this->isOwner($project, $user)) {
            throw new UnprocessableEntityHttpException('Произошла ошибка, пользователь является владельцем проекта');
        }

        $coOwner = SharedProject2User::findOne(['project_id' => $project->id, 'user_id' => $user->id]);

        if ($coOwner === null) {
            $coOwner = new SharedProject2User([
                'project_id' => $project->id,
                'user_id' => $user->id
            ]);

            if (!$coOwner->save()) {
                return false;
            }
        }

        return $coOwner;
    }

    /**
     * Удаляет совладельца у проекта.
     *
     * @param Project $project
     * @param User $user
     * @return bool
     */
    public function deleteCoOwner(Project $project, User $user): bool
    {
        return SharedProject2User::deleteAll(['project_id' => $project->id, 'user_id' => $user->id]);
    }

    /**
     * Проверяет является ли запрошенный пользователь владельцем проекта.
     *
     * @param Project $project
     * @param User $user
     * @return bool
     */
    public function isOwner(Project $project, User $user): bool
    {
        return $project->user_id === $user->id;
    }

    /**
     * Возвращает список проектов пользователя.
     *
     * @param \common\models\User $user пользователь
     * @return array|\common\models\Project[]
     */
    public function getList(User $user)
    {
        return Project::find()
            ->withUser($user)
            ->active()
            ->orderBy(['created_at' => SORT_DESC])
            ->all();
    }
}
