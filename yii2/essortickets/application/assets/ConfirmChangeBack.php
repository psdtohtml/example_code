<?php
namespace app\assets;

use \Yii;
use \yii\web\AssetBundle;

/**
 * @package app\assets
 * @version 1.0
 * @copyright (c) 2014-2015 KFOSoftware Team <kfosoftware@gmail.com>
 */
class ConfirmChangeBack extends AssetBundle
{
    public $sourcePath = '@app/assets/assets';
    public $js = [
        'js/confirm-change-back.js'
    ];
    public $depends = [
        '\xj\bootbox\BootboxAsset',
    ];
}
