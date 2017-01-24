<?php

namespace app\widgets\countdown;

use yii\web\AssetBundle;

class CountdownAsset extends AssetBundle
{
    public $sourcePath = '@widgets/countdown/assets';
    public $css = [];
    public $js = ['js/jquery.countdown.min.js', 'js/jquery.countdown.js'];
}
