<?php

namespace app\models;

use Yii;
use app\models\data\Orders as BaseOrders;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "orders".
 *
 * @property integer $id
 * @property integer $customer_id
 * @property integer $user_id
 * @property integer $tax_id
 * @property integer $status
 * @property string $valid_from
 * @property string $valid_to
 * @property integer $size
 * @property integer $total_price
 * @property string $coupon_code
 * @property integer $created_at
 * @property integer $confirm
 * @property integer $discount_id
 * @property string $datetime_booking
 * @property string $ch
 * @property integer $ch_status
 *
 * @package app\models
 * @version 1.0
 *
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class Orders extends BaseOrders
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

