<?php

namespace app\base;

use Yii;
use yii\helpers\Url;

/**
 * Class Module
 * @package app\base
 * @version 1.0
 *
 * @author Alex Zalharov <alexzakharovwork@gmail.com>
 */
class Module extends \yii\base\Module
{

    /**
     * @inheritdoc
     */
    public static function getNavBarConfig()
    {
        return [
            'brandLabel' => 'Essor Tickets',
            'brandUrl'   => Url::to([Yii::$app->controller->cabinetAction()]),
            'option'     => [
                // TODO 'class'=>'',
            ],
        ];
    }

}
