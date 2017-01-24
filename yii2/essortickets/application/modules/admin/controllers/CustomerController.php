<?php

namespace app\modules\admin\controllers;

use app\controllers\actions\Suggestions;
use app\services\Message;
use SameerShelavale\PhpCountriesArray\CountriesArray;
use Yii;
use app\models\Customer;
use app\models\data\Message as MessageModel;
use app\modules\admin\base\Controller;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\admin\models\search\Customer as CustomerSearch;
use app\modules\admin\models\search\Booking as BookingSearch;
use yii\web\UploadedFile;

/**
 * CustomerController implements the CRUD actions for Customer model.
 */
class CustomerController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'suggestion'           => ['get'],
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
            'suggestion' => [
                'class' => Suggestions::className(),
            ]
        ];
    }

    /**
     * Lists all Customer models.
     * @return mixed
     */
    public function actionManage()
    {
        /** @var \app\modules\admin\models\search\Customer $model */
        $model = new CustomerSearch();

        return $this->render('manage', [
            'model'             => $model,
            'dataProvider'      => $model->search(Yii::$app->request->get()),
            'autoCompleteLimit' => Yii::$app->params['common.autocomplete.limit'],
            'modelCode'         => \app\helpers\ModelNamesProvider::ADMIN_CUSTOMER_SEARCH,
        ]);
    }

    /**
     * Customer model
     * @param $id
     * @return string
     */
    public function actionManageCustomer($id)
    {
        /** @var \app\models\Customer $modelCustomer */
        $modelCustomer = $this->findModel($id);
        /** @var \app\modules\admin\models\search\Booking $model */
        $modelBooking = new BookingSearch();
        /** @var \app\models\Message $modelMessages */
        $modelMessages = new MessageModel();
        $dataProviderMessages = new ActiveDataProvider([
            'query' => $modelMessages::find()->where(['customer_id'=>$id]),
        ]);
        /** @var \app\services\Customer $totalSpentMoney */
        $totalSpentMoney = Yii::$app->customer->getTotalMoneySpentByCustomerId($id);
        return $this->render('manageCustomer', [
            'modelCustomer'        => $modelCustomer,
            'dataProvider'         => $modelBooking->search(Yii::$app->request->get(), '', $modelCustomer->id),
            'modelBooking'         => $modelBooking,
            'modelMessages'        => $modelMessages,
            'dataProviderMessages' => $dataProviderMessages,
            'totalSpentMoney'      => $totalSpentMoney,
        ]);

    }

    /**
     * Creates a new Customer model
     * @param integer $id order_id
     * @return string|\yii\web\Response
     */
    public function actionCreate($id)
    {
        /** @var \app\modules\admin\models\search\Customer $model */
        $model = new CustomerSearch();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/admin/orders/manage-order-detail', 'id' => $id]);
        }elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('partials/_form', [
                'model' => $model,
                'countries' => CountriesArray::get( 'name', 'name' ), // return numeric-codes->name array
            ]);
        } else {
            return $this->render('partials/_form', [
                'model' => $model,
                'countries' => CountriesArray::get( 'name', 'name' ), // return numeric-codes->name array
            ]);
        }
    }

    /**
     * Updates an existing Customer model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return string|\yii\web\Response
     */
    public function actionUpdateCustomer($id)
    {
        /** @var \app\models\data\Customer $model */
        $model = $this->findModel($id);

        /** @var array $post */
        $post = Yii::$app->request->post();

        if ($model->load($post) && $model->save()) {
            return $this->redirect(['manage-customer', 'id' => $id]);
        }elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('partials/_form', [
                'model' => $model,
                'countries' => CountriesArray::get( 'name', 'name' ), // return numeric-codes->name array
            ]);
        } else {
            return $this->render('partials/_form', [
                'model' => $model,
                'countries' => CountriesArray::get( 'name', 'name' ), // return numeric-codes->name array
            ]);
        }
    }

    /**
     * This method save custom message to db. Send logic to be \app\commands\DeliveryController.php
     * @param integer $id customer id
     * @return string|\yii\web\Response
     */
    public function actionSendCustomMessage($id)
    {
        /** @var \app\models\data\Message $modelMessages */
        $modelMessages = new MessageModel();
        if(Yii::$app->request->post()) {
            $post = ArrayHelper::merge(Yii::$app->request->post(), ['Message' => [
                'customer_id' => $id,
            ]]);
            if ($modelMessages->load($post)) {
                // get the uploaded file instance. for multiple file uploads
                // the following data will return an array
                $file = UploadedFile::getInstance($modelMessages, 'file');
                if ($file !== null) {
                    // store the source file name
                    $modelMessages->filename = $file->name;
                    $ext = end(explode(".", $file->name));

                    // generate a unique file name
                    $modelMessages->attachment = Yii::$app->security->generateRandomString() . ".{$ext}";

                    // the path to save file, you can set an uploadPath
                    // in Yii::$app->params (as used in example below)
                    $path = Yii::$app->params['uploadPath'] . $modelMessages->attachment;
                }
                if ($modelMessages->save()) {
                    if ($file !== null) {
                        $file->saveAs($path);
                    }
                    return $this->redirect(['manage-customer', 'id' => $id]);
                }
            }
        }elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('partials/_form_send_messages', [
                'model' => $modelMessages,
            ]);
        } else {
            return $this->render('partials/_form_send_messages', [
                'model' => $modelMessages,
            ]);
        }
    }

    /**
     * Finds the Customer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Customer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Customer::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
