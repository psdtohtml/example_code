<?php

namespace app\models\data;

use Yii;

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
 * @package app\models\data
 * @version 1.0
 *
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class Coupon extends \app\base\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'coupon';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ticket_id'], 'required'],
            [['status', 'used', 'ticket_id', 'limit', 'discount_type', 'discount'], 'integer'],
            [['starts_on', 'ends_on', 'valid_from', 'expires_on'], 'safe'],
            [['name', 'code'], 'string', 'max' => 255],
            [['ticket_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ticket::className(), 'targetAttribute' => ['ticket_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'ticket_id'     => 'Ticket ID',
            'name'          => 'Name',
            'code'          => 'Code',
            'limit'         => 'Limit',
            'starts_on'     => 'Starts On',
            'ends_on'       => 'Ends On',
            'valid_from'    => 'Valid From',
            'expires_on'    => 'Expires On',
            'discount_type' => 'Discount Type',
            'discount'      => 'Discount',
            'used'          => 'Used',
            'status'        => 'Active',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTicket()
    {
        return $this->hasOne(Ticket::className(), ['id' => 'ticket_id']);
    }
}
