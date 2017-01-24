<?php

namespace app\modules\admin\controllers;

use app\controllers\actions\Suggestions;
use app\enums\Models;
use app\models\Extras;
use app\models\Question;
use app\models\Variant;
use Yii;
use app\models\Ticket;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;
use app\modules\admin\base\Controller;
use app\modules\admin\models\search\Ticket as TicketSearch;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * TicketController.
 *
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
class TicketController extends Controller
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
     * Lists Ticket models for tour or variant.
     * @param integer $id tour_id
     * @return string
     */
    public function actionManageTicket($id)
    {
        /** @var \app\modules\admin\models\search\Ticket $model */
        $model = new TicketSearch();

        /** @var \app\models\Extras $modelExtras */
        $modelExtras = new Extras();

        /** @var  $dataProvider */
        $dataProvider = new ActiveDataProvider([
            'query' => TicketSearch::find()->where(['tour_id'=>$id])
        ]);
        /** @var \app\services\Extras $modelExtras */
        $dataModelExtras = Yii::$app->extras->getExtrasByTourId($id);

        /** @var \app\services\Variant $dataProviderVariant */
        $modelVariant = Yii::$app->variant->getVariantByTourId($id);

        /** @var \app\services\Question $modelQuestion */
        $modelQuestion = Yii::$app->question->getQuestionByTourId($id);
        return $this->render('manageTicket', [
            'model' => $model,
            'modelExtras' => $modelExtras,
            'dataProvider' => $dataProvider,
            'dataProviderExtras' => $dataModelExtras,
            'dataProviderVariant' => $modelVariant,
            'dataProviderQuestion' => $modelQuestion,
            'tourId' => $id,
        ]);
    }

    /**
     * Lists all Ticket models.
     * @return mixed
     */
    public function actionManage()
    {
        /** @var \app\modules\admin\models\search\Ticket $model */
        $model = new TicketSearch();

        return $this->render('manage', [
            'model'             => $model,
            'dataProvider'      => $model->search(Yii::$app->request->get()),
            'autoCompleteLimit' => Yii::$app->params['common.autocomplete.limit'],
            'modelCode'         => \app\helpers\ModelNamesProvider::ADMIN_TICKET_SEARCH,
        ]);
    }

    /**
     * Creates a new Ticket model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id)
    {
        /** @var \app\models\Ticket $model */
        $model = new Ticket();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['manage-ticket', 'id' => $model->tour_id]);
        }elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('partials/_form_tiers', [
                'model' => $model,
                'tourId' => $id
            ]);
        } else {
            return $this->render('partials/_form_tiers', [
                'model' => $model,
                'tourId' => $id
            ]);
        }

    }

    /**
     * Creates a new Extras model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionCreateExtras($id)
    {
        /** @var \app\models\Extras $model */
        $model = new Extras();

        /** @var array $ticketIds */
        $ticketIds= Yii::$app->ticket->getTicketIdsNames($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['manage-ticket', 'id' => $id]);
        }elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('partials/_form_extras', [
                'model' => $model,
                'ticketIds' => $ticketIds,
            ]);
        } else {
            return $this->render('partials/_form_extras', [
                'model' => $model,
                'ticketIds' => $ticketIds,
            ]);
        }

    }

    /**
     * Creates a new Variant model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateVariant($id)
    {
        /** @var \app\models\Variant $model */
        $model = new Variant();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['manage-ticket', 'id' => $model->tour_id]);
        }elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('partials/_form_variant', [
                'model' => $model,
                'tourId' => $id,
            ]);
        } else {
            return $this->render('partials/_form_variant', [
                'model' => $model,
                'tourId' => $id,
            ]);
        }

    }

    /**
     * Creates a new Question model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateQuestion($id)
    {
        /** @var \app\models\Question $model */
        $model = new Question();
        /** @var array $ticketIds */
        $ticketIds= Yii::$app->ticket->getTicketIdsNames($id);
        /** @var array $inputTypes */
        $inputTypes= \app\enums\Question::listData();
        /** @var string $tourId */
        $tourId= Yii::$app->question->getTourIdById($id)['tour_id'];

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['manage-ticket', 'id' => $tourId]);
        }elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('partials/_form_question', [
                'model' => $model,
                'ticketIds' => $ticketIds,
                'inputTypes' => $inputTypes,
            ]);
        } else {
            return $this->render('partials/_form_question', [
                'model' => $model,
                'ticketIds' => $ticketIds,
                'inputTypes' => $inputTypes,
            ]);
        }

    }

    /**
     * Updates an existing Ticket model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @param integer $modelsType
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionUpdate($id, $modelsType)
    {

        /** @var \app\models\Ticket $model */
        $model = $this->findModel($id, $modelsType);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['manage-ticket', 'id' => $model->tour_id]);
        }elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('partials/_form_tiers', [
                'model' => $model,
                'tourId' => $model->tour_id
            ]);
        } else {
            return $this->render('partials/_form_tiers', [
                'model' => $model,
                'tourId' => $model->tour_id
            ]);
        }

    }

    /**
     * Updates an existing Extras model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @param integer $modelsType
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionUpdateExtras($id, $modelsType)
    {
        /** @var \app\models\Extras $model */
        $model = $this->findModel($id, $modelsType);
        /** @var string $tourId */
        $tourId= Yii::$app->extras->getTourIdById($id)['tour_id'];
        /** @var array $ticketIds */
        $ticketIds= Yii::$app->ticket->getTicketIdsNames($tourId);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['manage-ticket', 'id' => $tourId]);
        }elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('partials/_form_extras', [
                'model' => $model,
                'ticketIds' => $ticketIds,
            ]);
        } else {
            return $this->render('partials/_form_extras', [
                'model' => $model,
                'ticketIds' => $ticketIds,
            ]);
        }

    }

    /**
     * Updates an existing Variant model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @param integer $modelsType
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionUpdateVariant($id, $modelsType)
    {
        /** @var \app\models\Variant $model */
        $model = $this->findModel($id, $modelsType);
        /** @var string $tourId */
        $tourId= $model->tour_id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['manage-ticket', 'id' => $tourId]);
        }elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('partials/_form_variant', [
                'model' => $model,
                'tourId' => $tourId,
            ]);
        } else {
            return $this->render('partials/_form_variant', [
                'model' => $model,
                'tourId' => $tourId,
            ]);
        }

    }

    /**
     * Updates an existing Question model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @param integer $modelsType
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionUpdateQuestion($id, $modelsType)
    {
        /** @var \app\models\Question $model */
        $model = $this->findModel($id, $modelsType);
        /** @var array $ticketIds */
        $ticketIds= Yii::$app->ticket->getTicketIdsNames($model->ticket_id);
        /** @var array $inputTypes */
        $inputTypes= \app\enums\Question::listData();
        /** @var string $tourId */
        $tourId= Yii::$app->question->getTourIdById($id)['tour_id'];

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['manage-ticket', 'id' => $tourId]);
        }elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('partials/_form_question', [
                'model' => $model,
                'ticketIds' => $ticketIds,
                'inputTypes' => $inputTypes,
            ]);
        } else {
            return $this->render('partials/_form_question', [
                'model' => $model,
                'ticketIds' => $ticketIds,
                'inputTypes' => $inputTypes,
            ]);
        }

    }

    /**
     * Deletes an existing universal model.
     * @param integer $id
     * @param integer $modelsType
     * @return \yii\web\Response
     * @throws ErrorException
     */
    public function actionDelete($id, $modelsType)
    {
        /** @var \app\enums\Models $currentModel */
        $currentModel = Models::getModelsByType($modelsType);
        /** @var $model */
        $model = $currentModel::findOne($id);

        if(!isset($model->tour_id)){
            if($modelsType === Models::EXTRAS){
                $tourId = Yii::$app->extras->getTourIdById($id)['tour_id'];
            }elseif ($modelsType === Models::QUESTION){
                $tourId = Yii::$app->question->getTourIdById($id)['tour_id'];
            }else{
                throw new ErrorException('tour_id Not Found!');
            }
        }else{
            $tourId = $model->tour_id;
        }
        $model->delete();

        return $this->redirect(['manage-ticket', 'id' => $tourId]);
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
