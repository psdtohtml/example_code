<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class VpluseAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'css/jquery.mCustomScrollbar.min.css',
        'css/select2.css',
        'css/main.css',
        'css/dev.css',

    ];

    public $js = [
        'js/jquery.mCustomScrollbar.js',
        'js/select2.min.js',
        'js/main.js',
    ];

    public $depends = [
        'yii\web\YiiAsset', // yii.js, jquery.js
        'yii\bootstrap\BootstrapAsset'
    ];
}
