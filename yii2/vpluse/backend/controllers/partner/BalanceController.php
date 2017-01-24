<?php
namespace backend\controllers\partner;

use Yii;
use common\models\SearchUser;
use common\models\User;
use yii\web\NotFoundHttpException;
use backend\models\rebate\BalanceForm;
use backend\components\ChangeBalance;

class BalanceController extends \backend\controllers\SiteController
{
    public function behaviors()
    {
        return [
            ChangeBalance::className(),
        ] + parent::behaviors();
    }

    /**
     * Change balance.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionChangeBalance($id)
    {
        $user = $this->findModel($id);
        $model = new BalanceForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if($this->changePartnerBalance($user, abs($model->balance), $model->note, $model->company_id)) {
                Yii::$app->session->setFlash('success', 'Баланс партнера успешно изменен');

                return $this->redirect(['index']);
            }
        } else {

            return $this->renderAjax('change-balance', [
                'model' => $model,
                'user' => $user,
            ]);
        }
    }

    /**
     * Lists all Request models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchUser();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Finds the Company model based on its primary key value.
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
