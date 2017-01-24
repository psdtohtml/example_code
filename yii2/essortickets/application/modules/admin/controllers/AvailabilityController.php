<?php

namespace app\modules\admin\controllers;

use app\controllers\actions\Suggestions;
use app\enums\Models;
use app\models\Detail;
use app\modules\admin\models\search\Detail as DetailSearch;
use Yii;
use app\models\Availability;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;
use app\modules\admin\base\Controller;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * AvailabilityController.
 *
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class AvailabilityController extends Controller
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
     * Lists all Availability models.
     * @return mixed
     */
    public function actionManage()
    {
        $dataProviderSeasons = new ActiveDataProvider([
            'query' => Availability::find()->where(['type'=>\app\enums\Availability::SEASON_TYPE]),
        ]);
        $dataProviderVariation = new ActiveDataProvider([
            'query' => Availability::find()->where(['type'=>\app\enums\Availability::VARIATION_TYPE]),
        ]);
        return $this->render('manage', [
            'dataProviderSeasons' => $dataProviderSeasons,
            'dataProviderVariation' => $dataProviderVariation,
        ]);
    }

    /**
     * Creates a new Availability model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param integer $type Season or Variation
     *
     * @return mixed
     */
    public function actionCreate($type)
    {
        $model = new Availability();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['manage']);
        }elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('partials/_form', [
                'model' => $model,
                'type'  => $type,
                'tourIds' => Yii::$app->tour->getTourIdsNames(),
            ]);
        } else {
            return $this->render('partials/_form', [
                'model' => $model,
                'type'  => $type,
                'tourIds' => Yii::$app->tour->getTourIdsNames(),
            ]);
        }
    }

    /**
     * Updates an existing Availability model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @param integer $type
     * @param integer $modelsType
     * @return mixed
     */
    public function actionUpdate($id, $type, $modelsType)
    {
        $model = $this->findModel($id, $modelsType);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['manage']);
        }elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('partials/_form', [
                'model' => $model,
                'type'  => $type,
                'tourIds' => Yii::$app->tour->getTourIdsNames(),
            ]);
        } else {
            return $this->render('partials/_form', [
                'model' => $model,
                'type'  => $type,
                'tourIds' => Yii::$app->tour->getTourIdsNames(),
            ]);
        }
    }

    /**
     * This method render option
     * @param integer $id availability_id
     * @return string|Response
     */
    public function actionManageDetail($id)
    {
        /** @var \app\modules\admin\models\search\Detail $model */
        $model = new DetailSearch();

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('partials/_detail', [
                'dataProvider'      => $model->search(Yii::$app->request->get()),
                'model'             => $model,
                'availabilityId'    => $id,

            ]);
        } else {
            return $this->render('partials/_detail', [
                'dataProvider'      => $model->search(Yii::$app->request->get()),
                'model'             => $model,
                'availabilityId'    => $id,
            ]);
        }
    }

    /**
     * This method render variation option
     * @param integer $id availability_id
     * @return string|Response
     */
    public function actionManageVariationDetail($id)
    {
        /** @var \app\modules\admin\models\search\Detail $model */
        $model = new DetailSearch();

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('partials/_detail_variant', [
                'dataProvider'      => $model->search(Yii::$app->request->get()),
                'model'             => $model,
                'availabilityId'    => $id,

            ]);
        } else {
            return $this->render('partials/_detail_variant', [
                'dataProvider'      => $model->search(Yii::$app->request->get()),
                'model'             => $model,
                'availabilityId'    => $id,
            ]);
        }
    }

    /**
     * @param integer $id availability_id
     * @return string|Response
     * @throws ErrorException
     */
    public function actionCreateVariationDetail($id)
    {
        /** @var \app\models\Detail $model */
        $model = new Detail();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['manage']);
        }elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('partials/__form_add_time', [
                'model' => $model,
                'availabilityId' => $id,
                'usersIds' => Yii::$app->user_component->getUsers(),
            ]);
        } else {
            return $this->render('partials/__form_add_time', [
                'model' => $model,
                'availabilityId' => $id,
                'usersIds' => Yii::$app->user_component->getUsers(),
            ]);
        }
    }

    /**
     * This method update variation model Detail
     * @param string $time
     * @param string $name
     * @param integer $availabilityId
     * @return string|Response
     */
    public function actionUpdateVariationDetail($time, $name, $availabilityId)
    {

        $model=Yii::$app->detail->findModelVariation($time, $name, $availabilityId);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['manage']);
        }elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('partials/__form_update_day', [
                'model' => $model,
                'usersIds' => Yii::$app->user_component->getUsers(),
            ]);
        } else {
            return $this->render('partials/__form_update_day', [
                'model' => $model,
                'usersIds' => Yii::$app->user_component->getUsers(),
            ]);
        }
    }

    /**
     * @param integer $id availability_id
     * @return string|Response
     * @throws ErrorException
     */
    public function actionCreateTime($id)
    {
        /** @var \app\models\Detail $model */
        $model = new Detail();

        $post = Yii::$app->request->post();

        if(Yii::$app->request->post()) {
            for ($i = 1; $i < 8; $i++) {
                /** @var \app\models\Detail $model */
                $model = new Detail();
                $newPost = ArrayHelper::merge($post, ['Detail' => ['day' => $i]]);
                $model->load($newPost) && $model->save();
            }
            return $this->redirect(['manage']);
        }elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('partials/__form_add_time', [
                'model' => $model,
                'availabilityId' => $id,
                'usersIds' => Yii::$app->user_component->getUsers(),
            ]);
        } else {
            return $this->render('partials/__form_add_time', [
                'model' => $model,
                'availabilityId' => $id,
                'usersIds' => Yii::$app->user_component->getUsers(),
            ]);
        }
    }

    /**
     * This method update all detail model by time_detail_id
     * @param integer $availabilityId
     * @param string $time
     * @return string|Response
     */
    public function actionUpdateTime($time, $availabilityId)
    {
        /** @var \app\services\Detail $models */
        $models = Yii::$app->detail->findModels($time, $availabilityId);

        if(Yii::$app->request->post()){
            foreach ($models as $model){
                $model->load(Yii::$app->request->post()) && $model->save();
            }
            return $this->redirect(['manage']);
        }elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('partials/__form_update_detail', [
                'model' => $models['0'],
                'usersIds' => Yii::$app->user_component->getUsers(),
            ]);
        } else {
            return $this->render('partials/__form_update_detail', [
                'model' => $models['0'],
                'usersIds' => Yii::$app->user_component->getUsers(),
            ]);
        }
    }

    /**
     * This method update model Detail
     * @param string $time
     * @param integer $day
     * @param integer $availabilityId
     * @return string|Response
     */
    public function actionUpdateDay($time, $day, $availabilityId)
    {

        $model=Yii::$app->detail->findModel($time, $day, $availabilityId);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['manage']);
        }elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('partials/__form_update_day', [
                'model' => $model,
                'usersIds' => Yii::$app->user_component->getUsers(),
            ]);
        } else {
            return $this->render('partials/__form_update_day', [
                'model' => $model,
                'usersIds' => Yii::$app->user_component->getUsers(),
            ]);
        }
    }

    /**
     * Delete detail models
     * @param $time
     * @param $availabilityId
     * @return Response
     */
    public function actionDeleteTime($time, $availabilityId)
    {
        /** @var \app\services\Detail $models */
        $models = Yii::$app->detail->findModels($time, $availabilityId);

        foreach ($models as $model){
            $model->delete();
        }

        return $this->redirect(['manage']);
    }

    /**
     * This method create duplicate
     * @param integer $id
     * @param integer $modelsType
     * @return Response
     * @throws ErrorException
     */
    public function actionDuplicate($id, $modelsType)
    {
        /** @var \app\models\Availability $model */
        $model = $this->findModel($id, $modelsType);

        /** Unset original id */
        unset($model['id']);
        $model->isNewRecord=true;

        if($model->save()){
            return $this->redirect(['manage']);
        }else{
            throw new ErrorException('Cloning Failed!');
        }
    }

    /**
     * Deletes an existing Availability model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @param integer $modelsType
     * @return mixed
     */
    public function actionDelete($id, $modelsType)
    {
        /** @var \app\enums\Models $currentModel */
        $currentModel = Models::getModelsByType($modelsType);
        /** @var $model */
        $model = $currentModel::findOne($id);

        $model->delete();

        return $this->redirect(['manage']);
    }

    /**
     * Finds the universal model based on its primary key value.
     * @param integer $id
     * @param integer $modelsType
     * @return mixed
     * @throws NotFoundHttpException
     */
    protected function findModel($id, $modelsType)
    {
        /** @var \app\enums\Models $currentModel */
        $currentModel = Models::getModelsByType($modelsType);

        if (($model = $currentModel::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
