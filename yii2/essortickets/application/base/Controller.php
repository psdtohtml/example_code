<?php

namespace app\base;

use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;
use yii\web\Response;
use yii\helpers\Json;
use yii\widgets\ActiveForm;
use yii\web\NotFoundHttpException;
use app\enums\UserType;

/**
 * Application base controller
 * @package app\base\Controller
 * @version 1.0
 *
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class Controller extends \yii\web\Controller
{

    /**
     * Perform ajax validation
     * @param \app\base\ActiveRecord $model model for validate
     * @return array the error message array indexed by the attribute IDs.
     *
     * @author Alex Zalharov <alexzakharovwork@gmail.com>
     */
    protected function performAjaxValidation(&$model)
    {
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            echo Json::encode(ActiveForm::validate($model));
            Yii::$app->end();
        }
    }

    /**
     * @return string cabinet actions
     * @throws NotFoundHttpException
     *
     * @author Alex Zakharov <alexzakharovwork@gmail.com>
     */
    public function cabinetAction()
    {
        switch (true) {
            case Yii::$app->user->isGuest :
                $result = '/user/login';
                break;
            case (Yii::$app->user->identity->type == UserType::ADMIN) :
                $result = Yii::$app->params['url.cabinet.admin'];
                break;
            case (Yii::$app->user->identity->type == UserType::MANAGER) :
                $result = Yii::$app->params['url.cabinet.manager'];
                break;
            default:
                throw new NotFoundHttpException();
        }

        return $result;
    }

}
