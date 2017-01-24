<?php

namespace app\modules\admin\controllers;

use app\models\Tax;
use \Yii;
use \app\modules\admin\base\Controller;
use yii\data\ActiveDataProvider;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * OptionsController implements the CRUD actions for Options model.
 * @package app\modules\admin\controllers
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class OptionsController extends Controller
{
    public $layout = 'middle.php';

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'update' => '\kfosoft\yii2\system\actions\UpdateOptionAction'
        ];
    }

    /**
     * Action manage.
     * @return string
     * @throws HttpException
     */
    public function actionManage()
    {
        /** @var \app\models\Tax $model */
        $model = new Tax();
        /** @var array $dataProvider */
        $dataProvider = new ActiveDataProvider([
            'query' => Tax::find(),
        ]);
        return Yii::$app->request->isAjax ? $this->renderAjax('manage', ['model' => $model, 'dataProvider' => $dataProvider]) : $this->render('manage', ['model' => $model, 'dataProvider' => $dataProvider]);
    }

    /**
     * Creates a new Tax
     * @return string|\yii\web\Response
     * @throws \ErrorException
     */
    public function actionCreateTax()
    {
        /** @var \app\models\Tax $model */
        $model = new Tax();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['manage']);

        }elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('partials/_form', [
                'model' => $model,
            ]);
        } else {
            return $this->render('partials/_form', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Tax model.
     * @param integer $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdateTax($id)
    {
        /** @var \app\models\Tax $model */
        $model = new Tax();
        $model = $model::findOne(['id' => $id]);

        /** @var array $post */
        $post = Yii::$app->request->post();

        if ($model->load($post) && $model->save()) {
            return $this->redirect(['manage']);
        }elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('partials/_form', [
                'model' => $model,
            ]);
        } else {
            return $this->render('partials/_form', [
                'model' => $model,
            ]);
        }

    }

    /**
     * Deletes an existing Tax model.
     * If deletion is successful, the browser will be redirected to the 'manage' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        /** @var \app\models\Tax $model */
        $model = new Tax();
        $model::findOne(['id' => $id])->delete();

        return $this->redirect(['manage']);
    }


    /**
     * Finds the Tax model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Tax the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Tax::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
