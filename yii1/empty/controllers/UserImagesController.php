<?php

class UserImagesController extends RController
{
    public $defaultAction = 'index';
    public $layout = 'backend';


    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'rights',
        );
    }


    /**
     * Lists all models.
     */
    public function actionIndex()
    {
        // client script manager
        $clientScript = Yii::app()->clientScript;

        //$basePath = Yii::getPathOfAlias('application.modules.clickbuilder');
        //$baseUrl = Yii::app()->getAssetManager()->publish($basePath);

        $theme = Yii::app()->theme->baseUrl;

        // подключить css
        //$clientScript->registerCssFile($theme . '/css/bootstrap.min.css');

        // подключить javascript
        $clientScript->registerScriptFile($theme . '/js/jquery.min.js');
        $clientScript->registerScriptFile($theme . '/js/bootstrap.min.js');
        $clientScript->registerScriptFile($theme . '/js/userimages.js');


        if (Yii::app()->user->isSuperuser)
        {
            //$dataProvider = new CActiveDataProvider('UserImages');
            $model = UserImages::model()->findAll();
        } else {
            /*
            $dataProvider = new CActiveDataProvider('UserImages', array(
                'criteria' => array(
                    'condition' => 'user_id = :user_id',
                    'params' => array(':user_id' => Yii::app()->user->id),
                )
            ));
            */

            $model = UserImages::model()->findAll('user_id = :user_id', array(':user_id' => Yii::app()->user->id));
        }

        $this->render('index', array(
            'images' => $model,
            //'user_id' => $model->user_id,
            //'name' => $model->name,
            //'hash_file' => $model->hash_file,
            //'thumbnail_url' => Yii::app()->params['upload_folder'] . '/' . $model->user_id . '/mini/' . $model->name,
            //'original_url' => Yii::app()->params['upload_folder'] . '/' . $model->user_id . '/oroginal/' . $model->name,
        ));


        /*
        $this->render('index',array(
            'dataProvider' => $dataProvider,
        ));
        */
    }


	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}


	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new UserImages;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['UserImages']))
		{
			$model->attributes=$_POST['UserImages'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}


	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['UserImages']))
		{
			$model->attributes=$_POST['UserImages'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}


	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$model = $this->loadModel($id);

        $this->DeleteFile(Yii::app()->params['upload_folder'] . '/' . Yii::app()->user->id . '/mini/' . $model->name);
        $this->DeleteFile(Yii::app()->params['upload_folder'] . '/' . Yii::app()->user->id . '/original/' . $model->name);

        $model->delete();

        $this->redirect(array('index'));

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		//if (!isset($_GET['ajax']))
			//$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}


	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new UserImages('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['UserImages']))
			$model->attributes=$_GET['UserImages'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}


	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return UserImages the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=UserImages::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}


	/**
	 * Performs the AJAX validation.
	 * @param UserImages $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='user-images-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}


    public function DeleteFile($file)
    {
        if ($file)
        {
            if (file_exists($file))
                unlink($file);
        }
    }
}