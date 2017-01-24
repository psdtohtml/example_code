<?php

namespace app\models\data;

use Yii;

/**
 * This is the model class for table "ticket".
 *
 * @property integer $id
 * @property integer $tour_id
 * @property string $name
 * @property string $description
 * @property integer $price
 * @property integer $booking_fee_type
 * @property integer $booking_fee
 * @property integer $availability_id
 *
 * @property Coupon[] $coupons
 * @property Extras[] $extras
 * @property Question[] $questions
 *
 * @package app\models\data
 * @version 1.0
 *
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class Ticket extends \app\base\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ticket';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tour_id', 'price', 'booking_fee_type', 'booking_fee', 'availability_id'], 'integer'],
            [['description'], 'string'],
            [['tour_id', 'name', 'price'], 'required'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tour_id' => 'Tour ID',
            'name' => 'Name',
            'description' => 'Description',
            'price' => 'Price',
            'booking_fee_type' => 'Booking Fee Type',
            'booking_fee' => 'Booking Fee',
            'availability' => 'Availability ID',
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            foreach ($this->coupons as $coupon) {
                $coupon->delete();
            }
            foreach ($this->extras as $extra) {
                $extra->delete();
            }
            foreach ($this->questions as $question) {
                $question->delete();
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestions()
    {
        return $this->hasMany(Question::className(), ['ticket_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExtras()
    {
        return $this->hasMany(Extras::className(), ['ticket_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoupons()
    {
        return $this->hasMany(Coupon::className(), ['ticket_id' => 'id']);
    }
}
