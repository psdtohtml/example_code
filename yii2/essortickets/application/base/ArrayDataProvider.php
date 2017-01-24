<?php

namespace app\base;

/**
 * Fix pagination.
 * @package app\base
 * @version 1.0
 *
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class ArrayDataProvider extends \yii\data\ArrayDataProvider
{
    /**
     * @inheritdoc
     */
    protected function prepareModels()
    {
        if (($models = $this->allModels) === null) {
            return [];
        }

        if (($sort = $this->getSort()) !== false) {
            $models = $this->sortModels($models, $sort);
        }

        if (($pagination = $this->getPagination()) !== false) {
            $pagination->totalCount = $this->getTotalCount();
        }

        return $models;
    }

    /**
     * @inheritdoc
     */
    public function prepareTotalCount()
    {
        return $this->getPagination()->totalCount === 0 ? $this->getCount() : $this->getPagination()->totalCount;
    }

    /**
     * Override to remove embedded sorting
     * @inheritdoc
     */
    protected function sortModels($models, $sort)
    {
        return $models;
    }
}
