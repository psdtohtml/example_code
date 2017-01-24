<?php

namespace app\models;

use Yii;
use app\models\data\Channel as BaseChannel;

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
 * @package app\models
 * @version 1.0
 *
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class Channel extends BaseChannel
{

}
