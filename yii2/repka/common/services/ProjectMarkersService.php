<?php

namespace common\services;

use common\models\form\ProjectMarkerDomainsForm;
use common\models\Project;
use common\models\ProjectColorReference;
use common\models\ProjectMarker;
use common\models\ProjectMarkerDomain;
use RuntimeException;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Сервис маркиров площадок проекта.
 */
class ProjectMarkersService
{
    /**
     * Создает значения по умолчанию для маркировки площадок для проекта.
     *
     * @param Project $project
     * @return void
     */
    public function createDefaultValues(Project $project): void
    {
        foreach (ProjectColorReference::getDefaults() as $color => $name) {
            $model = new ProjectMarker([
                'name' => $name,
                'color' => $color,
                'project_id' => $project->id
            ]);
            $model->save();
        }
    }

    /**
     * Возвращает список марекров площадок проекта.
     *
     * @param Project $project
     * @return \common\models\ProjectMarker[]
     */
    public function getList(Project $project)
    {
        return $project->projectMarkers;
    }

    /**
     * Обновляет информацию о маркировках площадок проекта.
     *
     * @param Project $project
     * @param array $markers
     * @return void
     */
    public function updateMarkers(Project $project, $markers): void
    {
        $currentProjectMarkersIds = ArrayHelper::getColumn($project->projectMarkers, 'id');
        $needProjectMarkersIds = [];
        foreach ($markers as $marker) {
            if (isset($marker->id)) {
                $needProjectMarkersIds[] = $marker->id;
            }
        }
        $removeItems = array_diff($currentProjectMarkersIds, $needProjectMarkersIds);
        if (count($removeItems)) {
            ProjectMarker::deleteAll(['id' => $removeItems]);
        }

        foreach ($markers as $marker) {
            $marker->save();
        }
    }

    /**
     * Создает маркировку площадки.
     * Если существует запись со значениям переданного проекта и доменом, то обновляем идентификатор маркера.
     *
     * @param Project $project
     * @param string $domain
     * @param ProjectMarker $projectMarker
     * @return ProjectMarkerDomain
     */
    public function createMarker(Project $project, string $domain, ProjectMarker $projectMarker): ProjectMarkerDomain
    {
        $domain = $this->prepareDomain($domain);
        $model = $this->findMarker($project, $domain);
        if ($model === null) {
            $model = new ProjectMarkerDomain([
                'project_id' => $project->id,
                'domain' => $domain,
            ]);
        }
        $model->project_marker_id = $projectMarker->id;

        if (!$model->save()) {
            throw new RuntimeException('Произошла ошибка при создании маркировки площадки.');
        }
        return $model;
    }

    /**
     * Возвращает маркировку площадки.
     *
     * @param Project $project
     * @param string $domain
     * @return ProjectMarkerDomain|null
     */
    public function getMarker(Project $project, string $domain): ProjectMarkerDomain
    {
        $model = $this->findMarker($project, $domain);
        if ($model === null) {
            throw new RuntimeException('Маркировка площадки не найдена.');
        }
        return $model;
    }

    /**
     * Удаление маркировки площадки.
     *
     * @param ProjectMarkerDomain $marker
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function removeMarker(ProjectMarkerDomain $marker): void
    {
        if (!$marker->delete()) {
            throw new RuntimeException('Произошла ошибка при удалении маркировки площадки.');
        }
    }

    /**
     * Поиск маркировки.
     *
     * @param Project $project
     * @param string $url
     * @return ProjectMarkerDomain|null
     */
    public function findMarker(Project $project, string $url): ?ProjectMarkerDomain
    {
        return ProjectMarkerDomain::findOne([
            'project_id' => $project->id,
            'domain' => $this->prepareDomain($url)
        ]);
    }

    /**
     * Возвращает маркировку площадки по идентификатору.
     *
     * @param $id
     * @return ProjectMarker
     */
    public function getMarkerById($id): ProjectMarker
    {
        $model = ProjectMarker::findOne($id);

        if ($model === null) {
            throw new RuntimeException('Маркировка площадки не найдена.');
        }

        return $model;
    }

    /**
     * Возвращает имя домена переданного url.
     *
     * @param string $url
     * @return string
     */
    public function prepareDomain(string $url): string
    {
        $result = preg_replace('/(https?:\/\/www\.)|(https?:\/\/)|(www\.?)/', '', $url);
        $result = trim($result);

        if (strpos($result, '/') !== false) {
            $result = explode('/', $result)[0];
        }
        if (strpos($result, '?') !== false) {
            $result = explode('?', $result)[0];
        }

        if (strlen($result) < 3) {
            throw new RuntimeException('Переданный адрес не является валидным.');
        }
        return $result;
    }

    /**
     * Привязка площадок к маркеру.
     * Существующие площадки не загруженные в форму удаляются.
     *
     * @param ProjectMarker $marker
     * @param ProjectMarkerDomainsForm $form
     * @throws RuntimeException
     */
    public function bindDomainsToMarker(ProjectMarker $marker, ProjectMarkerDomainsForm $form): void
    {
        $transaction = Yii::$app->getDb()->beginTransaction();
        $domains = $form->getDomainsArray();
        $existedDomains = $marker->getDomains()
            ->indexBy('domain')
            ->all();

        try {
            // добавление новых
            foreach ($domains as $domain) {
                if (!array_key_exists($domain, $existedDomains)) {
                    $existedDomain = $this->createMarker($marker->project, $domain, $marker);
                    $existedDomains[$existedDomain->domain] = $existedDomain;
                }
            }

            // удаление
            /** @var ProjectMarkerDomain[]|array $domainsToRemove */
            $domainsToRemove = array_diff_key($existedDomains, array_combine($domains, $domains));

            foreach ($domainsToRemove as $markerDomain) {
                $markerDomain->delete();
            }

            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::error("Unable to bind domains for marker #{$marker->id} due error: {$e->getMessage()}");
            throw new RuntimeException("Ошибка привязки площадок к маркеру #{$marker->id}.");
        }
    }
}
