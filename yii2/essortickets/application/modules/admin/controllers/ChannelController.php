<?php

namespace app\modules\admin\controllers;

use app\enums\LayoutChannel;
use app\modules\admin\base\Controller;
use kfosoft\yii2\system\Option;
use Yii;
use app\models\Channel;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ChannelController implements the CRUD actions for Channel model.
 */
class ChannelController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Channel models.
     * @return mixed
     */
    public function actionManage()
    {
        $model = new Channel();
        $dataProvider = new ActiveDataProvider([
            'query' => Channel::find(),
        ]);

        return $this->render('manage', [
            'dataProvider' => $dataProvider,
            'model'        => $model,
        ]);
    }

    /**
     * Displays a single Channel model.
     * @param integer $id
     * @return mixed
     */
    public function actionManageChannel($id)
    {
        /** @var \app\models\Channel $model */
        $model = $this->findModel($id);
        if(Yii::$app->request->post()){
            $script = '<div id="booking-widget-'. $model->id .'"><iframe src="' . Yii::$app->option_component->getOptionByKey('url.widget') . '?id=' . base64_encode($model->product_id) . '&layout='. Yii::$app->request->post()['Channel']['layout'] . '&color=' . base64_encode(Yii::$app->request->post()['Channel']['color']) . '&label=' . base64_encode(Yii::$app->request->post()['Channel']['button_label']) . '&subscribed=' . base64_encode(Yii::$app->request->post()['Channel']['subscribed']) . '&address=' . base64_encode(Yii::$app->request->post()['Channel']['address']) . '" frameBorder=0 height=' . Yii::$app->request->post()['Channel']['height'] . ' width=' . Yii::$app->request->post()['Channel']['width'] . '></iframe></div>';
            $post = ArrayHelper::merge(Yii::$app->request->post(), ['Channel'=> ['widget_code'=> $script]]);
            if ($model->load($post) && $model->save()) {
                return $this->redirect(['manage-channel', 'id' => $id]);
            }
        }
        return $this->render('manageChannel', [
            'model'  => $this->findModel($id),
            'layout' => LayoutChannel::listData(),
        ]);
    }

    /**
     * Creates a new Channel model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Channel();

        if(Yii::$app->request->post()) {
            $post = ArrayHelper::merge(Yii::$app->request->post(), ['Channel'=> ['widget_code'=> 'Update options!', 'button_label' => 'Book Now']]);
            if ($model->load($post) && $model->save()) {
                return $this->redirect(['manage']);
            }
        }elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('partials/_form', [
                'model' => $model,
                'product' => Yii::$app->tour->getTourIdsNames(),
            ]);
        } else {
            return $this->render('partials/_form', [
                'model' => $model,
                'product' => Yii::$app->tour->getTourIdsNames(),
            ]);
        }
    }

    /**
     * Updates an existing Channel model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        /** @var \app\models\Channel $model */
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['manage']);
        }elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('partials/_form', [
                'model' => $model,
                'product' => Yii::$app->tour->getTourIdsNames(),
            ]);
        } else {
            return $this->render('partials/_form', [
                'model' => $model,
                'product' => Yii::$app->tour->getTourIdsNames(),
            ]);
        }
    }

    /**
     * Deletes an existing Channel model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['manage']);
    }

    /**
     * Finds the Channel model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Channel the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Channel::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
