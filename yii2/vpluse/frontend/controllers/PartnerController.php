<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use common\models\User;
use common\models\rebate\History;

/**
 * Site controller
 */
class PartnerController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],

                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }


    public function init()
    {
        parent::init();
        $this->layout = 'vpluse';
        $session = Yii::$app->session;
        $session->set('service', 'partner');
    }

    /**
     * Displays partner account.
     *
     * @return mixed
     */
    public function actionIndex()
    {

        return $this->render('index');
    }

    /**
     * Displays partner account.
     *
     * @return mixed
     */
    public function actionCustomers()
    {
        $customers = User::find()->select(['id', 'username', 'created_at', 'balance'])->where(['ref_id' => Yii::$app->user->identity->id])->all();

        return $this->render('customers', ['customers' => $customers]);
    }

    /**
     * Displays partner account.
     *
     * @return mixed
     */
    public function actionHistory()
    {
        $history = History::find()->where(['id_user' => Yii::$app->user->id, 'orientation' => 2])
            ->orderBy('operation_date DESC')->all();

        return $this->render('history', ['history' => $history]);
    }
}
