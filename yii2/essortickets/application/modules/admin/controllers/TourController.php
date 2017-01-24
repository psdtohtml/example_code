<?php

namespace app\modules\admin\controllers;

use app\controllers\actions\Suggestions;
use app\enums\EventRecurring;
use app\models\Events;
use Yii;
use app\models\Tour;
use app\models\data\Tour as BaseTour;
use app\modules\admin\base\Controller;
use app\modules\admin\models\search\Tour as TourSearch;
use yii\base\ErrorException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * TourController.
 *
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class TourController extends Controller
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
     * Lists all Tour models.
     * @return mixed
     */
    public function actionManage()
    {
        /** @var \app\modules\admin\models\search\Tour $model */
        $model = new TourSearch();

        return $this->render('manage', [
            'model'             => $model,
            'dataProvider'      => $model->search(Yii::$app->request->get()),
            'autoCompleteLimit' => Yii::$app->params['common.autocomplete.limit'],
            'modelCode'         => \app\helpers\ModelNamesProvider::ADMIN_TOUR_SEARCH,
        ]);
    }

    /**
     * Displays a single Tour model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        /** @var \app\modules\admin\models\search\Tour $model */
        $model = new TourSearch();
        $model = $model::findOne(['id'=>$id]);

        /** @var \app\services\User $manager */
        $manager = Yii::$app->user_component->getUserNameById($model->manager_id);

        $tax = Yii::$app->tax->getTaxNameAndAmountPercentageById($model->tax_id);

        return $this->render('view', [
            'model'      => $model,
            'manager'    => $manager,
            'tax'        => $tax,

        ]);
    }

    /**
     * Creates a new Tour model and Events
     * @return string|\yii\web\Response
     * @throws \ErrorException
     */
    public function actionCreate()
    {
        /** @var \app\models\Tour $model */
        $model = new Tour();

        if ($model->load(Yii::$app->request->post())) {
            $model->link_info = base64_encode($model->link_info);
            // get the uploaded file instance. for multiple file uploads
            // the following data will return an array
            $image = UploadedFile::getInstance($model, 'image');

            if($image !== null){
                // store the source file name
                $model->filename = $image->name;
                $ext = end(explode(".", $image->name));

                // generate a unique file name
                $model->logo = Yii::$app->security->generateRandomString().".{$ext}";

                // the path to save file, you can set an uploadPath
                // in Yii::$app->params (as used in example below)
                $path = Yii::$app->params['uploadPath'] . $model->logo;

            }

            if($model->save()){
                if($image !== null){
                    $image->saveAs($path);
                }
                Yii::$app->events->getEventCreator($model);
                return $this->redirect(['manage']);
            }
        }elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('partials/_form', [
                'model' => $model,
                'data'  => Yii::$app->user_component->getManagers(),
                'recurring' => EventRecurring::listData(),
                'taxs' => Yii::$app->tax->getTaxIdsNames(),
            ]);
        } else {
            return $this->render('partials/_form', [
                'model' => $model,
                'data'  => Yii::$app->user_component->getManagers(),
                'recurring' => EventRecurring::listData(),
                'taxs' => Yii::$app->tax->getTaxIdsNames(),
            ]);
        }
    }

    /**
     * Updates an existing Tour model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return string|\yii\web\Response
     * @throws ErrorException
     */
    public function actionUpdate($id)
    {
        /** @var \app\models\data\Tour $model */
        $model = $this->findModel($id);
        $oldProduct = $model->name;
        $oldTime = $model->start_tour_time;

        /** @var array $post */
        $post = Yii::$app->request->post();
        if ($model->load($post)) {
            $model->link_info = base64_encode($model->link_info);
            // get the uploaded file instance. for multiple file uploads
            // the following data will return an array
            $image = UploadedFile::getInstance($model, 'image');

            if($image !== null) {
                // store the source file name
                $model->filename = $image->name;
                $ext = end(explode(".", $image->name));

                // generate a unique file name
                $model->logo = Yii::$app->security->generateRandomString() . ".{$ext}";

                // the path to save file, you can set an uploadPath
                // in Yii::$app->params (as used in example below)
                $path = Yii::$app->params['uploadPath'] . $model->logo;
            }
            if($model->save()){
                if($image !== null) {
                    $image->saveAs($path);
                }
                /** @var \app\models\Events $modelsEvents */
                $modelsEvents = Yii::$app->events->findModelsByTitleAndDate($oldProduct, $oldTime);

                foreach ($modelsEvents as $modelEvent){
                    /** @var  \app\models\Events $modelEvent */
                    $modelEvent->guide_id = $model->manager_id;
                    $modelEvent->title = $model->name;
                    $modelEvent->start_time = $model->start_tour_time;
                    $modelEvent->end_time = $model->end_tour_time;
                    $modelEvent->size_all = $model->ticket_available;
                    if (!$modelEvent->save(false)){
                        throw new ErrorException("Event #{$modelEvent->id} not updated!");
                    }
                }
                return $this->redirect(['view', 'id' => $model->id]);
            }

        }elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('partials/_form', [
                'model' => $model,
                'data'  => Yii::$app->user_component->getManagers(),
                'recurring' => EventRecurring::listData(),
                'taxs' => Yii::$app->tax->getTaxIdsNames(),
            ]);
        } else {
            return $this->render('partials/_form', [
                'model' => $model,
                'data'  => Yii::$app->user_component->getManagers(),
                'recurring' => EventRecurring::listData(),
                'taxs' => Yii::$app->tax->getTaxIdsNames(),
            ]);
        }

    }

    public function actionConfirmationMail($id)
    {
        /** @var \app\models\data\Tour $model */
        $model = $this->findModel($id);
        /** @var array $post */
        $post = Yii::$app->request->post();

        if($model->load($post) && $model->save()){
            return $this->redirect(['view', 'id' => $model->id]);
        }else{
            throw new ErrorException('confirmation_mail not saved!');
        }
    }

    /**
     * Deletes an existing Tour model.
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
     * Finds the Tour model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Tour the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BaseTour::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
