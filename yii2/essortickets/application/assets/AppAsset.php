<?php

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\View;

/**
 * @package app\assets
 * @version 1.0
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class AppAsset extends AssetBundle
{
    public $jsOptions = [
        'position' => View::POS_HEAD
    ];
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
    ];
    public $js = [
        'js/common.js',
        'js/ajax-modal-popup.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
