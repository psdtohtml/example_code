<?php

namespace backend\controllers\rebate;

use Yii;
use common\models\rebate\News;
use common\models\User;
use common\models\rebate\NewsSearch;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use common\models\SearchUser;
use backend\models\rebate\GroupSearch;
use backend\models\rebate\Group;
use backend\models\EmailTemplates;

/**
 * NewsController implements the CRUD actions for News model.
 */
class NewsController extends \backend\controllers\SiteController
{
    /**
     * Lists all News models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new NewsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new News model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new News();

        if ($model->load(Yii::$app->request->post()))
        {
            $file = UploadedFile::getInstance($model, 'img');
            if (isset($file)) {

                $filename = uniqid() . '.' . $file->extension;
                $path = 'uploads/news/' . $filename;
                if ($file->saveAs($path)) {
                    $model->img = $filename;
                }
            }

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Новость успешно добавлена.');

                return $this->redirect('index');
            }

            Yii::$app->session->setFlash('error', 'Возникли проблемы при добавлении новости');
        }

        return $this->renderAjax('create', [
            'model' => $model,
        ]);

    }

    /**
     * Updates an existing News model.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $old_file = $model->img;

        if ($model->load(Yii::$app->request->post())) {
            $file = UploadedFile::getInstance($model, 'img');
            $model->img = $old_file;
            if (isset($file)) {

                $filename = uniqid() . '.' . $file->extension;
                $path = 'uploads/news/' . $filename;
                if ($file->saveAs($path)) {
                    $model->img = $filename;
                }
            }
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Новость успешно обновлена.');

                return $this->redirect('index');
            }
            Yii::$app->session->setFlash('error', 'Возникли проблемы при обновлении новости');
        }

        return $this->renderAjax('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing News model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'Новость успешно удалена');

        return $this->redirect(['index']);
    }

    /**
     * Send news to users.
     * @return mixed
     */
    public function actionSend($id)
    {
        $searchModel = new SearchUser();
        $query = $searchModel->subscribers();
        $searchUser = [];
        if(isset(Yii::$app->request->queryParams['SearchUser'])) {
            $searchUser = ['SearchUser' => Yii::$app->request->queryParams['SearchUser']];
        }
        $dataProvider = $searchModel->search($searchUser, $query);

        $searchModelGroup = new GroupSearch();
        $groupSearch = [];
        if(isset(Yii::$app->request->queryParams['GroupSearch'])) {
            $groupSearch = ['GroupSearch' => Yii::$app->request->queryParams['GroupSearch']];
        }
        $dataProviderGroup = $searchModelGroup->search($groupSearch);


        $post = Yii::$app->request->post();
        if(isset($post['users'])) {
            if ($this->sendNews($id, $post['users'])) {
                Yii::$app->session->setFlash('success', 'Новость успешно отправлена выбранным пользователям.');
            } else {
                Yii::$app->session->setFlash('error', 'Возникли проблемы при отправке новости');
            }

        } elseif (isset($post['groups'])) {
            foreach ($post['groups'] as $group_id) {
                $users_id_ar_obj = Group::findOne($group_id)
                    ->inGroupUsers()
                    ->select('id')
                    ->all();
                if($users_id_ar_obj) {
                    $users_id = [];
                    foreach ($users_id_ar_obj as $obj) {
                        $users_id[] = $obj->id;
                    }
                    if ($this->sendNews($id, $users_id)) {
                        Yii::$app->session->setFlash('success', 'Новость успешно отправлена выбранным группам пользователей.');
                    } else {
                        Yii::$app->session->setFlash('error', 'Возникли проблемы при отправке новости');
                    }
                }
            }
        }

        return $this->render('send', [
            'model' => $this->findModel($id),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'searchModelGroup' => $searchModelGroup,
            'dataProviderGroup' => $dataProviderGroup,
        ]);
    }


    /**
     * @param integer $id_news
     * @param array $id_users
     * @return bool
     */
    private function sendNews($id_news, $id_users)
    {

        $news = $this->findModel($id_news);
        $params = [
            'news_url' => Yii::$app->urlManagerFrontend->createAbsoluteUrl(['site/news', 'id' => $news->id]),
            'profile_url' => Yii::$app->urlManagerFrontend->createAbsoluteUrl(['rebate/profile']),
            'title' => $news->title,
            'text'=> $news->content
        ];
        $users = User::find()->where(['IN', 'id', $id_users])->all();
        $template = EmailTemplates::findOne(14);
        if ($params) {
            foreach ($params as $key => $param) {
                $template->body_html = str_replace('#'.$key.'#', $param, $template->body_html);
                $template->body_text = str_replace('#'.$key.'#', $param, $template->body_text);
            }
        }
        $messages = [];
        foreach ($users as $user) {
            $messages[] = Yii::$app->mailer->compose()
                ->setTo([$user->email => $user->fullName])
                ->setSubject($template->subject)
                ->setHtmlBody($template->body_html)
                ->setTextBody($template->body_text);
        }

        return Yii::$app->mailer->sendMultiple($messages);

    }

    /**
     * Finds the News model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return News the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = News::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
