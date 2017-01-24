<?php

namespace backend\controllers\partner;

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
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

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
        if ($this->changePartnerBalance($model->idUser, $model->amount, $model->payment_detail, '', true)) {
            $model->status = 1;
            if ($model->save()) {
                $model->idUser->sendMail('output', 'Выполнена заявка на вывод средств', ['id' => $model->id, 'amount'=> $model->amount]);
                Yii::$app->session->setFlash('success', 'Заявка успешно выполнена');
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось изменить статус');
            }
        } else {
            Yii::$app->session->setFlash('error', 'Не удалось выполнить заявку');
        }


        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing Output model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
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
