<?php

namespace common\models\form;

use common\services\ISeoService;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Модель формы поисковых систем проекта.
 *
 * @property integer $projectId
 * @property array $selected
 */
class ProjectSearchEngineForm extends Model
{
    public $projectId;
    /**
     * @var array|null
     */
    public $selected;

    /**
     * @var ISeoService
     */
    private $seoService;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['selected', 'required']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->seoService = Yii::$container->get(ISeoService::class);
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'selected' => 'Поисковые системы'
        ];
    }

    /**
     * Список поисковых систем.
     *
     * @return array
     */
    public function getSearchEngineList(): array
    {
        return ArrayHelper::map($this->seoService->getSearchEngines(), 'id', 'name');
    }
}
