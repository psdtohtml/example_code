<?php

namespace app\models\data;

use Yii;

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
 * @package app\models\data
 * @version 1.0
 *
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class Orders extends \app\base\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'orders';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['valid_from'], 'required'],
            [['tax_id', 'created_at', 'confirm', 'discount_id', 'customer_id', 'user_id', 'size', 'status', 'total_price', 'ch_status'], 'integer'],
            [['valid_from', 'valid_to', 'datetime_booking', 'coupon_code', 'ch', 'ch_status'], 'safe'],
            [['coupon_code'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'               => 'ID',
            'customer_id'      => 'Customer ID',
            'user_id'          => 'User ID',
            'tax_id'           => 'Tax ID',
            'discount_id'      => 'Discount ID',
            'status'           => 'Status',
            'valid_from'       => 'Valid From',
            'valid_to'         => 'Valid To',
            'size'             => 'Size',
            'total_price'      => 'Total Price',
            'coupon_code'      => 'Coupon code',
            'created_at'       => 'Created at',
            'confirm'          => 'Confirm',
            'datetime_booking' => 'Date booking',
        ];
    }
}
