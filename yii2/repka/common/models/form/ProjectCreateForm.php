<?php

namespace common\models\form;

use common\models\Project;
use yii\helpers\ArrayHelper;

/**
 * Модель формы создания проекта.
 *
 * @property integer $id
 * @property string $name
 */
class ProjectCreateForm extends Project
{
    /**
     * @var array|null
     */
    public $selectedSearchEngines;
    /**
     * @var array
     */
    public $searchEnginesList;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [['selectedSearchEngines', 'required']]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function formName()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            ['selectedSearchEngines' => 'Поисковые системы']
        );
    }

    /**
     * @param Project|null $project
     */
    public function setProject(?Project $project): void
    {
        if ($project) {
            $this->setAttributes($project->getAttributes(), false);
            $this->selectedSearchEngines = ArrayHelper::getColumn($project->projectSearchEngines, 'code');
        }
    }
}
