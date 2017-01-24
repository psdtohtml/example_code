<?php

namespace app\module\rebate\controllers;

use Yii;
use common\models\rebate\Section;
use common\models\rebate\Company;
use common\models\rebate\UserRequest;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use common\models\User;


class SectionController extends Controller
{
    public $layout = 'vpluse';


    /**
     * Show sectoon.
     * @return mixed
     */
    public function actionIndex($id)
    {
        if(Yii::$app->request->post()) {
            $model = new UserRequest();
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                //Уведомление для админа
                $params = [
                    'company' => $model->getCompanyName(),
                    'section' => $model->idSection->title,
                    'account' => $model->account,
                    'status' => 'Ожидание',
                ];
                User::sendMailToAdmin(12, $params);
                //Уведомление для пользователя
                $model->idUser->sendMail(3, $params);
                Yii::$app->session->setFlash('success', 'Заявка успешно добавлена.');
            } else {
                Yii::$app->session->setFlash('error', 'Возникли проблемы при добавлении заявки');
            }
        }

        return $this->render('index', [
            'model' => $this->findModel($id),
            'companies' => $this->getCompanies($id),
            'user_requests' => $this->userRequests($id),
        ]);
    }

    /**
     * Finds the Section model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Section the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Section::findOne($id)) !== null) {

            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Finds the Company based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return  array Company the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function getCompanies($id)
    {
        if (($model = Company::find()->where(['id_section' => $id])->all()) !== null) {

            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Finds the Requests based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return  array Requests the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function userRequests($id)
    {
        if (($model = UserRequest::find()->where(['id_section' => $id, 'id_user' => Yii::$app->user->id])->all()) !== null) {

            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
