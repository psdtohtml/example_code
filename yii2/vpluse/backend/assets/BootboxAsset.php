<?php
namespace backend\assets;

use Yii;
use yii\web\AssetBundle;

class BootboxAsset extends AssetBundle
{
    public $sourcePath = '@webroot';
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    //public $sourcePath = '@vendor/bower/bootbox';
    public $js = [
        'js/bootbox.js',
    ];

    public static function overrideSystemConfirm()
    {
        Yii::$app->view->registerJs('
            yii.confirm = function(message, ok, cancel) {
                bootbox.confirm(message, function(result) {
                    if (result) { !ok || ok(); } else { !cancel || cancel(); }
                });
            }
        ');
    }
}