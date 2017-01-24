<?php

namespace app\models\data;

use Yii;

/**
 * This is the model class for table "booking".
 *
 * @property integer $id
 * @property integer $order_id
 * @property integer $status
 * @property string $valid_from
 * @property string $valid_to
 * @property integer $booking_price
 * @property string $ch
 * @property integer $ch_status
 *
 * @package app\models\data
 * @version 1.0
 *
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class Booking extends \app\base\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'booking';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'status', 'total_price', 'booking_price'], 'integer'],
            [['valid_from', 'valid_to', 'ch', 'ch_status'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'order_id'      => 'Order ID',
            'status'        => 'Status',
            'valid_from'    => 'Valid From',
            'valid_to'      => 'Valid To',
            'booking_price' => 'Booking Price',
        ];
    }
}