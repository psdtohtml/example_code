<?php

namespace app\models;

use Yii;
use app\models\data\Events as BaseEvents;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "events".
 *
 * @property integer $id
 * @property integer $guide_id
 * @property string $title
 * @property string $description
 * @property string $start_date
 * @property string $end_date
 * @property string $start_time
 * @property string $end_time
 * @property integer $size_pending
 * @property integer $size_use
 * @property integer $size_all
 * @property integer $dayoff
 *
 * @package app\models
 * @version 1.0
 *
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class Events extends BaseEvents
{

    /**
     * Universal method for updating model field
     * @param $field string field name
     * @param $value mixed field value
     * @return bool Save result
     */
    public function updateField($field, $value)
    {
        if (isset($this->{$field})) {
            $this->{$field} = $value;

            return $this->save(false);
        }

        return false;
    }

}
