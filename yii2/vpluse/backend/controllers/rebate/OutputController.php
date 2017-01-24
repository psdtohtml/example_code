<?php

namespace backend\controllers\rebate;

use Yii;
use common\models\rebate\Output;
use common\models\rebate\SearchOutput;
use yii\web\NotFoundHttpException;
use common\models\User;
use backend\components\ChangeBalance;

/**
 * OutputController implements the CRUD actions for Output model.
 */
class OutputController extends \backend\controllers\SiteController
{

    public function behaviors()
    {
        return [
            ChangeBalance::className(),
        ] + parent::behaviors();
    }

    /**
     * Lists all Output models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchOutput();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 1);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Update status.
     * @return mixed
     */

    public function actionUpdateStatus($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($this->changeBalance($model->idUser, $model->amount, $model->payment_detail, '', true)) {
                $model->status = 1;
                if ($model->save()) {
                    $model->idUser->sendMail(1, ['id' => $model->id, 'amount'=> $model->amount, 'userName' => $model->idUser->fullName]);
                    Yii::$app->session->setFlash('success', 'Заявка успешно выполнена');
                } else {
                    Yii::$app->session->setFlash('error', 'Не удалось изменить статус');
                }
            }

            return $this->redirect(['index']);
        } else {

            return $this->renderAjax('update-status', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Output model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if($model->status == 0) {
            $model->idUser->sendMail(2, ['id' => $model->id, 'amount'=> $model->amount, 'userName' => $model->idUser->fullName]);
        }
        $model->delete();
        Yii::$app->session->setFlash('success', 'Заявка успешно удалена');

        return $this->redirect(['index']);
    }

    /**
     * Finds the Output model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Output the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Output::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
