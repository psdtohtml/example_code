<?php

namespace app\module\rebate;

use Yii;

/**
 * rebate module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\module\rebate\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->setLayoutPath('@frontend/views/layouts');
        $session = Yii::$app->session;
        $session->set('service', 'rebate');
        // custom initialization code goes here
    }
}
