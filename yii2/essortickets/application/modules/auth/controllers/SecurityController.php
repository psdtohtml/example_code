<?php

namespace app\modules\auth\controllers;

use app\enums\UserType;
use app\models\AuthAssignment;
use app\modules\auth\models\forms\LoginForm;
use dektrium\user\controllers\SecurityController as BaseSecurityController;
use Yii;
use yii\db\Query;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class SecurityController. Implements default auth functionality.
 * @package app\modules\auth\controllers
 * @version 1.0
 *
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class SecurityController extends BaseSecurityController
{
    /**
     * Displays the login page.
     *
     * @return string|Response
     */
    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            $this->goHome();
        }

        /** @var LoginForm $model */
        $model = \Yii::createObject(LoginForm::className());
        $event = $this->getFormEvent($model);

        $this->performAjaxValidation($model);
        $this->trigger(self::EVENT_BEFORE_LOGIN, $event);

        if ($model->load(\Yii::$app->request->post()) && $model->login()) {
            return $this->redirect($this->cabinetAction(Yii::$app->request->get('referrer')));
        } else {
            return $this->render('login', [
                'model'  => $model,
                'module' => $this->module,
            ]);
        }


    }

    /**
     * Logs the user out and then redirects to the homepage.
     *
     * @return Response
     */
    public function actionLogout()
    {
        $event = $this->getUserEvent(\Yii::$app->user->identity);

        $this->trigger(self::EVENT_BEFORE_LOGOUT, $event);

        \Yii::$app->getUser()->logout();

        $this->trigger(self::EVENT_AFTER_LOGOUT, $event);

        return $this->goHome();
    }

    /**
     * @param string|null $referrer url referrer.
     * @return string cabinet actions.
     * @throws NotFoundHttpException
     * @author Cyril Turkevich
     */
    public function cabinetAction($referrer = null)
    {
        /** @var string $role */
        $role = AuthAssignment::getUserRoleById(Yii::$app->user->id);

        switch (true) {
            case Yii::$app->user->isGuest :
                $result = '/';
                break;
            case $referrer !== null :
                $result = $referrer;
                break;
            case ($role == UserType::ADMIN) :
                $result = Yii::$app->params['url.cabinet.admin'];
                break;
            case ($role == UserType::MANAGER) :
                $result = Yii::$app->params['url.cabinet.manager'];
                break;
            default:
                throw new NotFoundHttpException();
        }

        return $result;
    }
}
