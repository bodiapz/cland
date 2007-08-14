<?php

class TicketsController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$ticket = Tickets::model()->findAllByAttributes(array('id' => $id, 'user_id' => Yii::app()->user->getState('userid')));
		if(empty($ticket)){
			$this->redirect('/');
		}
		
		$model = Users::model()->findByPk(Yii::app()->user->getState('userid'));
		$model -> scenario = 'tickets';
		
		$commentModel = new Comments;
		$commentPost = new Comments;

		if(isset($_POST['Comments'])) {
			$commentPost -> attributes = $_POST['Comments'];
			$commentPost -> ticket_id = $id;
			$commentPost -> user_id = Yii::app()->user->getState('userid');

			if($commentPost -> save()) {
				Yii::app()->user->setFlash('success', 'Comment Posted Successfully');
			}
		}
		
		Comments::model()->updateAll(array('status' => 1), 'ticket_id=:ticket_id AND user_id!=:user_id', array('ticket_id' => $id, 'user_id' => Yii::app()->user->getState('userid')));

		$comments = Comments::model()->findAllByAttributes(array('ticket_id' => $id));

		$this->render('view',array(
			'model' => $model,
			'ticketModel'=>$this->loadModel($id),
			'comments' => $comments,
			'commentModel' => $commentModel
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{	
		//if(!isset($_GET['test'])) die();
		$model = Users::model()->findByPk(Yii::app()->user->getState('userid'));
		
		$ticketModel=new Tickets;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($ticketModel);

		if(isset($_POST['Tickets']))
		{
			$ticketModel->attributes=$_POST['Tickets'];
			$ticketModel -> status = 'open';
			$ticketModel -> user_id = Yii::app()->user->getState('userid');

			//print_r($ticketModel -> attributes);
			if($ticketModel->save()){
                $priority = (!empty($model->ticket_priority)) ? $model->ticket_priority : $ticketModel->priority;
                $ticket_settings = TicketSettings::model()->findByAttributes(array('priority' => $priority, 'disabled' => 0));
                if(!empty($ticket_settings)){
                    $utility = new UtilityFunction();
                    $subject = 'Ticket #' . $ticketModel->id . ': ' . $ticketModel->subject . ' From: ' . $model->first_name . ' ' . $model->last_name;
                    $message = '<p> Subject: ' . $ticketModel->subject . '<p> Detail:' . $ticketModel->detail . '<p>' . 'Priority: ' .$priority . '<p>' . 'Date: ' . $ticketModel->created_at;
                    $utility->mail_send($ticket_settings->emails, $message, $subject);
                }
				$this->redirect(array('index'));
            }
		}
	//print_r($model);die();
		$this->render('create',array(
			'model' => $model,
			'ticketModel'=>$ticketModel,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model = Users::model()->findByPk(Yii::app()->user->getState('userid'));
		$model -> scenario = 'tickets';
		
		$ticketModel=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($ticketModel);

		if(isset($_POST['Tickets']))
		{
			$ticketModel -> attributes=$_POST['Tickets'];
			if($ticketModel->save())
				$this->redirect(array('index'));
		}

		$this->render('update',array(
			'model' => $model,
			'ticketModel'=>$ticketModel,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$id = Yii::app()->user->getState('userid');
		
		$model = Users::model()->findByPk($id);
		$model -> scenario = 'tickets';
		
		/*$dataProvider=new CActiveDataProvider('Tickets');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));*/
		
		$ticketModel = new Tickets('search');
		$ticketModel->setAttributes(array('user_id' => $id));
		//$ticketModel = Tickets::model()->findByAttributes(array('user_id' => $id));
		$this -> render('index', array(
			'model' => $model,
			'ticketModel' => $ticketModel
		));

	}

	/**
	 * Manages all models.
	 */
	/*public function actionAdmin()
	{
		$ticketModel=new Tickets('search');
		$ticketModel->unsetAttributes();  // clear any default values
		if(isset($_GET['Tickets']))
			$ticketModel->attributes=$_GET['Tickets'];

		$this->render('admin',array(
			'model' => $model=Users::model()->findByPk(Yii::app()->user->getState('userid')),
			'ticketModel'=>$ticketModel,
		));
	}*/

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$ticketModel=Tickets::model()->findByPk($id);
		if($ticketModel===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $ticketModel;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($ticketModel)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='tickets-form')
		{
			echo CActiveForm::validate($ticketModel);
			Yii::app()->end();
		}
	}
}
