<?php

namespace backend\controllers\rebate;

use Yii;
use backend\models\rebate\Group;
use backend\models\rebate\GroupSearch;
use yii\web\NotFoundHttpException;
use backend\models\rebate\UserGroupSearch;
use common\models\SearchUser;
use backend\models\rebate\UserGroup;
use yii\data\ActiveDataProvider;

/**
 * GroupController implements the CRUD actions for Group model.
 */
class GroupController extends \backend\controllers\SiteController
{
    /**
     * Lists all Group models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GroupSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Creates a new Group model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Group();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Группа успешно создана');

            return $this->redirect(['index']);
        } else {
            return $this->renderAjax('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Group model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Группа успешно обновлена');

            return $this->redirect(['index']);
        } else {
            return $this->renderAjax('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Send news to users.
     * @return mixed
     */
    public function actionUsers($group_id)
    {
        $model = $this->findModel($group_id);
        $searchModel = new UserGroupSearch();
        $query = $model->inGroupUsers();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $query);
        $query = $model->outGroupUsers();
        $dataProviderOthers = $searchModel->search(Yii::$app->request->queryParams, $query);
        $post = Yii::$app->request->post();
        if(isset($post['del_users'])) {
            UserGroup::deleteAll(['in', 'user_id', $post['del_users']]);
            Yii::$app->session->setFlash('success', 'Пользователи удалены из группы');
        }
        if(isset($post['add_users'])) {
            foreach ($post['add_users'] as $user_id) {
                $userGroup = new UserGroup();
                $userGroup->group_id = $group_id;
                $userGroup->user_id = $user_id;
                $userGroup->save();
            }
            Yii::$app->session->setFlash('success', 'Пользователи доьавлены в группу');
        }

        return $this->render('users', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'dataProviderOthers' => $dataProviderOthers,
            'model' => $model,

        ]);

    }

    /**
     * Deletes an existing Group model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'Группа успешно удалена');

        return $this->redirect(['index']);
    }

    /**
     * Finds the Group model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Group the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Group::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
