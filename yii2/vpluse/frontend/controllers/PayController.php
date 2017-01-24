<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use backend\models\Pay;
use frontend\models\rebate\PaymentChangeForm;
use frontend\models\rebate\OutputForm;
use common\models\rebate\Output;
use yii\web\NotFoundHttpException;
use common\models\User;
use frontend\models\rebate\UserPay;

/**
 * Site controller
 */
class PayController extends Controller
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
        ];
    }


    public function init()
    {
        parent::init();
        $this->layout = 'vpluse';
    }

    /**
     * Lists all User Payments.
     * @return mixed
     */
    public function actionPaymentDetails()
    {
        $payments = Pay::find()->where('status=1')->joinWith([
            'userPays' => function ($query) {
                $query->onCondition(['user_pay.user_id' => Yii::$app->user->id]);
            },
        ])->all();

        $payments_details = [];
        foreach ($payments as $key => $payment) {
            $payments_details[$key]['id'] = $payment->id;
            $payments_details[$key]['name'] = $payment->name;
            $payments_details[$key]['value'] = $payment->userPays ? $payment->userPays[0]->value : '-';
        }

        return $this->render('payment_details', [
            'payments_details' => $payments_details,
        ]);
    }

    /**
     * Change User Payments.
     * @param integer $pay_id
     * @return mixed
     */
    //change-payment-detail
    public function actionChangePaymentDetail($pay_id)
    {
        $user = $this->findModel(Yii::$app->user->id);
        $pay = Pay::find()->where('pay.id='. $pay_id)->joinWith([
            'userPays' => function ($query) {
                $query->onCondition(['user_pay.user_id' => Yii::$app->user->id]);
            },
        ])->one();

        $model = new PaymentChangeForm($user, $pay);

        if ($model->load(Yii::$app->request->post()) && $model->changePayment()) {
            Yii::$app->session->setFlash('success', 'Реквизиты успешно сохранены.');

            return $this->redirect(['payment-details']);
        } else {
            return $this->render('payment_change', [
                'model' => $model,
            ]);
        }

    }

    /**
     * Output money
     * @return mixed
     */
    public function actionOutput()
    {
        $service = isset($_SESSION['service']) ? $_SESSION['service'] : null;
        switch ($service) {
            case 'rebate' :
                echo $service_id = 1;
                break;
            case 'vps' :
                echo $service_id = 2;
                break;
            case 'bot' :
                echo $service_id = 3;
                break;
            default : $service_id = 0;
        }

        $model = new OutputForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $output_model = new Output();
            $output_model->id_user = Yii::$app->user->id;
            $output_model->amount = $model->credit;
            $output_model->payment_detail = $model->formatPaymentDetail();
            $output_model->service_id = $service_id;

            if ($output_model->save()) {
                $params =[
                    'id' => $output_model->id,
                    'amount' => $output_model->amount,
                    'payment_detail' => $output_model->payment_detail,
                ];
                //Уведомление для админа
                User::sendMailToAdmin(10, $params);
                //Уведомление для пользователя idUser
                $output_model->idUser->sendMail(11, $params);
                Yii::$app->session->setFlash('success', 'Заявка на вывод средств успешно отправлена');
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось создать заявку');
            }
        }

        return $this->render('output', [
            'model' => $model,
        ]);

    }

    /**
     * Check user payment detail
     */
    public function actionGetPayment()
    {
        if(!Yii::$app->request->isAjax) {
            exit('go home!');
        }
        $user_payment_detail = '';
        if($post = Yii::$app->request->post()) {
            if(isset($post['pay_id'])) {
                $user_payment_detail = UserPay::find()
                    ->where(['user_id'=> Yii::$app->user->id, 'pay_id' => $post['pay_id']])
                    ->one();
            }
            if($user_payment_detail) {
                exit($user_payment_detail->value);
            }
        }
        exit($user_payment_detail);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
