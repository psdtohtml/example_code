<?php

namespace app\widgets\booking;

use yii\web\AssetBundle;

/**
 * Class BookingAsset
 * @package app\widgets\booking
 *
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class BookingAsset extends AssetBundle
{
    public $sourcePath = '@widgets/booking/assets';
    public $css = [];
    public $js = ['js/jquery.booking.min.js', 'js/jquery.booking.js'];
}