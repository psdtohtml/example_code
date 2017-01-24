<?php

namespace app\models\data;

use Yii;

/**
 * This is the model class for table "channel".
 *
 * @property integer $id
 * @property integer $product_id
 * @property string $name
 * @property integer $layout
 * @property integer $event_view_days
 * @property string $color
 * @property string $button_label
 * @property string $widget_code
 * @property string $height
 * @property string $width
 * @property integer $subscribed
 * @property integer $address
 *
 * @package app\models\data
 * @version 1.0
 *
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class Channel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'channel';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'product_id'], 'required'],
            [['product_id', 'layout', 'event_view_days', 'subscribed', 'address'], 'integer'],
            [['name', 'color', 'button_label', 'height', 'width'], 'string', 'max' => 255],
            [['widget_code'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'              => 'ID',
            'product_id'      => 'Product',
            'name'            => 'Name',
            'layout'          => 'Layout',
            'event_view_days' => 'Event View Days',
            'color'           => 'Color',
            'button_label'    => 'Button Label',
            'widget_code'     => 'Widget Code',
            'height'          => 'Height',
            'width'           => 'Width',
            'subscribed'      => 'Subscribe for Marketing',
            'address'         => 'Address verification',
        ];
    }
}
