<?php

namespace app\models;

use Yii;
use app\models\data\Coupon as BaseCoupon;

/**
 * This is the model class for table "coupon".
 *
 * @property integer $id
 * @property integer $ticket_id
 * @property string $name
 * @property string $code
 * @property integer $limit
 * @property string $starts_on
 * @property string $ends_on
 * @property string $valid_from
 * @property string $expires_on
 * @property integer $discount_type
 * @property integer $discount
 * @property integer $used
 * @property integer $status
 *
 * @property Ticket $ticket
 *
 * @package app\models
 * @version 1.0
 *
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class Coupon extends BaseCoupon
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
