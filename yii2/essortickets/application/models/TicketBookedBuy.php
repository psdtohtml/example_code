<?php

namespace app\models;

use Yii;
use app\models\data\TicketBookedBuy as BaseTicketBookedBuy;
/**
 * This is the model class for table "ticket_booked_buy".
 *
 * @property integer $id
 * @property integer $order_id
 * @property integer $ticket_id
 * @property integer $variant_id
 * @property string  $start_date
 * @property integer $event_id
 * @property integer $size_amount
 *
 * @property Orders $order
 *
 * @package app\models
 * @version 1.0
 *
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class TicketBookedBuy extends BaseTicketBookedBuy
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
