<?php

class UserController extends Controller
{
    public $layout='//layouts/login';


    /**
     * Логин пользователя
     */
    public function actionLogin()
    {
        $this->pageTitle = Yii::t('app', 'Login');

        $serviceName = Yii::app()->request->getQuery('service');

        if (isset($serviceName))
        {
            $eauth = Yii::app()->eauth->getIdentity($serviceName);
            $eauth->redirectUrl = Yii::app()->user->returnUrl;
            $eauth->cancelUrl = $this->createAbsoluteUrl('user/login');

            try
            {
                if ($eauth->authenticate())
                {
                    $identity = new ServiceUserIdentity($eauth);

                    // успешная аутентификация
                    if ($identity->authenticate())
                    {
                        Yii::app()->session->add("servicename", $eauth->serviceName);
                        Yii::app()->session->add("user_identity", $eauth->id);
                        Yii::app()->session->add("user_photo", $eauth->photo);
                        Yii::app()->session->add("username", $eauth->name);


                        $user_id = $eauth->id;
                        $record  = Service::model()->with('user')->find('identity = :id AND service_name = :sn', array(':id' => $eauth->id, ':sn' => $eauth->serviceName));

                        if (!$record)
                        {
                            // Профиль не связан с какой-либо учетной записью на сайте
                            $eauth->redirectUrl = $this->createAbsoluteUrl('/user/registration');
                            $eauth->redirect();
                        }
                        else
                        {
                            Yii::app()->user->login($identity);
                            $eauth->redirectUrl = $this->createAbsoluteUrl('/user/profile');
                            $eauth->redirect();
                        }


                        if(Yii::app()->user->isGuest)
                        {
                            Yii::app()->user->login($identity);
                            $eauth->redirect();
                        }
                        else
                        {
                            $eauth->redirectUrl = $this->createAbsoluteUrl('/user/profile');
                            $eauth->cancelUrl = $this->createAbsoluteUrl('/user/profile');

                            $service = new Service();
                            $service->identity = $eauth->id;
                            $service->service_name = $eauth->serviceName;
                            $service->user_id = Yii::app()->user->id;

                            if ($service->save()) {
                                $eauth->redirect();
                            }
                        }
                    }
                    else
                    {
                        // закрытие popup-окна
                        $eauth->cancel();
                    }
                }
                $this->redirect(array('user/login'));
            }
            catch (EAuthException $e)
            {
                Yii::app()->user->setFlash('error',
                    'EAuthException: '.$e->getMessage());
                $eauth->redirect($eauth->getCancelUrl());
            }
        }
        elseif (Yii::app()->user->isGuest)
        {
            $model = new LoginForm;

            // пользователь отправил форму логина
            if (isset($_POST['LoginForm']))
            {
                $model->attributes = $_POST['LoginForm'];
                // валидация полей и вход
                if ($model->validate() && $model->login()) {
                    if (Yii::app()->user->checkAccess('admin')){
                        $this->redirect('/admin');
                    } else
                        $this->redirect('/template');
                }
            }
            // отображение логин формы
            $this->render('login', array('model' => $model));

        } else
            $this->redirect(Yii::app()->controller->module->returnUrl);


    }


    public function actionForgot()
    {
        $model = new ForgotForm;
        $this->pageTitle = Yii::t('app', 'Forgot password');

        if (isset($_POST['ForgotForm']))
        {
            $model->attributes = $_POST['ForgotForm'];
            if ($model->validate())
            {
                $user = User::model()->notsafe()->findbyPk($model->user_id);

                $activation_url = 'http://' . $_SERVER['HTTP_HOST'] . $this->createUrl(implode(array('/user/recovery/')), array("activkey" => $user->activkey, "email" => $user->email));

                // MAIL
                Mail::sendMail($user->email, Yii::t('app', 'Forgot password'), Yii::t('app', 'Forgot password to link {link}', array('{link}' => $activation_url)));

                Yii::app()->user->setFlash('recoveryMessage', Yii::t('app', 'Check your mail. We send instructions for forgot password'));
                $this->refresh();
            }
        }

        $this->render('forgot', array('model' => $model));
    }


    public function actionRecovery()
    {
        $this->pageTitle = Yii::t('app', 'Change password');

        $email = ((isset($_GET['email'])) ? $_GET['email'] : '');
        $activkey = ((isset($_GET['activkey'])) ? $_GET['activkey'] : '');

        if ($email && $activkey)
        {
            $model = new ChangePasswordForm;
            $find = User::model()->notsafe()->findByAttributes(array('email' => $email));

            if (isset($find) && $find->activkey == $activkey)
            {
                if (isset($_POST['ChangePasswordForm']))
                {
                    $model->attributes = $_POST['ChangePasswordForm'];
                    if ($model->validate()) {
                        $find->password = CPasswordHelper::hashPassword($model->password);
                        $find->activkey = md5(microtime() . $model->password);
                        if ($find->status == 0) {
                            $find->status = 1;
                        }
                        $find->save();
                        Yii::app()->user->setFlash('recoveryMessage',  Yii::t('app', 'Password changed'));
                        $this->redirect('/forgot');
                    }
                }
                $this->render('changepassword', array('model' => $model));
            }
            else
            {
                Yii::app()->user->setFlash('recoveryMessage', Yii::t('app', 'Link error'));
                $this->redirect('/forgot');
            }
        }
    }


    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect('/login');
    }
}