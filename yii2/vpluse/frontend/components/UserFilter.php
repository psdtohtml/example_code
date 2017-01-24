<?php

namespace frontend\components;

use Yii;
use yii\base\ActionFilter;

class UserFilter extends ActionFilter
{
    public function beforeAction($action)
    {
        echo '<pre>'; var_dump($action); echo '</pre>'; exit;
        //return false;
    }

}