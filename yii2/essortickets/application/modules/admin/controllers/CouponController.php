<?php

namespace app\modules\admin\controllers;

use app\controllers\actions\Suggestions;
use Yii;
use app\models\Coupon;
use \app\modules\admin\models\search\Coupon as CouponSearch;
use app\modules\admin\base\Controller;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * CouponController implements the CRUD actions for Coupon model.
 */
class CouponController extends Controller
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
     * Lists all Coupon models.
     * @return mixed
     */
    public function actionManage()
    {
        /** @var \app\modules\admin\models\search\Coupon $model */
        $model = new CouponSearch();

        return $this->render('manage', [
            'model'             => $model,
            'dataProvider'      => $model->search(Yii::$app->request->get()),
            'autoCompleteLimit' => Yii::$app->params['common.autocomplete.limit'],
            'modelCode'         => \app\helpers\ModelNamesProvider::ADMIN_COUPON_SEARCH,
        ]);
    }

    /**
     * Creates a new Coupon model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        /** @var \app\models\Coupon $model */
        $model = new Coupon();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['manage']);
        }elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('partials/_form', [
                'model' => $model,
                'ticketIds' => Yii::$app->ticket->getTicketIdsNames(),
            ]);
        } else {
            return $this->render('partials/_form', [
                'model' => $model,
                'ticketIds' => Yii::$app->ticket->getTicketIdsNames(),
            ]);
        }

    }

    /**
     * Updates an existing Coupon model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        /** @var \app\models\Coupon $model */
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['manage']);
        }elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('partials/_form', [
                'model' => $model,
                'ticketIds' => Yii::$app->ticket->getTicketIdsNames(),
            ]);
        } else {
            return $this->render('partials/_form', [
                'model' => $model,
                'ticketIds' => Yii::$app->ticket->getTicketIdsNames(),
            ]);
        }

    }

    /**
     * Deletes an existing Coupon model.
     * If deletion is successful, the browser will be redirected to the 'manage' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['manage']);
    }

    /**
     * Finds the Coupon model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Coupon the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Coupon::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
