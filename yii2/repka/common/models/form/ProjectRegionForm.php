<?php

namespace common\models\form;

use common\components\validators\ArrayValidator;
use common\models\ProjectRegion;
use Yii;
use yii\base\Model;

/**
 * Модель формы регионов проекта.
 *
 * @property $regions
 * @property $projectId
 */
class ProjectRegionForm extends Model
{
    public $regions;

    public $projectId;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['regions', ArrayValidator::class, 'min' => 1]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function load($data, $formName = null)
    {
        if (!isset($data['projectRegion'])) {
            return false;
        }
        foreach ($data['projectRegion'] as $region) {
            $this->regions[] = new ProjectRegion($region);
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($attributeNames = null, $clearErrors = true)
    {
        ProjectRegion::validateMultiple($this->regions);
        return parent::validate($attributeNames, $clearErrors);
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($this->regions as $region) {
                $region->project_id = $this->projectId;
                $region->save();
            }
            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'regions' => 'Регионы'
        ];
    }
}
