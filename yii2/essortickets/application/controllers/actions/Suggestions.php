<?php

namespace app\controllers\actions;

use \Yii;
use \yii\base\Action;
use \yii\web\Response;

/**
 * Universal Action to get suggestions for typeahead widgets
 * @inheritdoc
 * @package app\helpers
 * @version 1.0
 *
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class Suggestions extends Action
{
    /**
     * @inheritdoc
     */
    public function run($code ,$field, $value)
    {

        switch ($code) {
            case 0:
                $component = 'tour';
                break;
            case 1:
                $component = 'variant';
                break;
            case 2:
                $component = 'ticket';
                break;
            case 3:
                $component = 'coupon';
                break;
            case 4:
                $component = 'detail';
                break;
            case 5:
                $component = 'orders';
                break;
            case 6:
                $component = 'booking';
                break;
            case 7:
                $component = 'customer';
                break;
            case 8:
                $component = 'question';
                break;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return Yii::$app->{$component}->getAutoCompleteSuggestions($code, $field, $value, Yii::$app->params['common.autocomplete.limit'], Yii::$app->user->getId());
    }
}