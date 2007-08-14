<?php

class PaymentsController extends Controller
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
				'actions'=>array('create','process', 'bitcoin', 'createFree', 'index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update', 'process'),
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
		$model=new Payments;
		$utility = new UtilityFunction;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		
		//$id = Yii::app()->session['clandestine_id'];
		$id = Yii::app()->user->getState('userid');
		
		$package = Packages::model()->findPackageByUser($id);//Yii::app()->user->getState('userid')
		$rate = $utility->getCurrentBitRate();

		if(isset($_POST['Payments']))
		{
			$model->attributes=$_POST['Payments'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
			'package' => $package, 
			'rate' => $rate,
			'id' => $id
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

		if(isset($_POST['Payments']))
		{
			$model->attributes=$_POST['Payments'];
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
		$dataProvider=new CActiveDataProvider('Payments');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Payments('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Payments']))
			$model->attributes=$_GET['Payments'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Payments::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='payments-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	/**
	 *
	 */
	public function actionProcess() {
		
		if(isset($_POST['package'])) {
			$package = $_POST['package'];
		}

		if(isset($_COOKIE['packageSelected'])) {
			$package = $_COOKIE['packageSelected'];
		}
        $package = 1;
		//$id = Yii::app()->session['clandestine_id'];
		$id = Yii::app()->user->getState('userid');
		
		$rCoin = new BitcoinrBit; // Bitcoin API Object
		$utility = new UtilityFunction;
		
		$rate = $utility -> getCurrentBitRate();
		$payment = Payments::model()->findLastPayment($id, false); 
		$payment->btc_amount = round($payment->amount / $rate['last'], 8);
		$payment->save();
		
		$bitcoinAddress = $rCoin->getnewaddress($id); // New Bitcoin Address
				
		$bitcoin = new BcAddresses;
		$bitcoin -> user_id = $id;//Yii::app()->user->getState('userid');
		$bitcoin -> address = $bitcoinAddress;
		$bitcoin -> save();
		
		$free_account = !Users::model()->checkUserLoginDate($id);
		//$user = Users::model()->findByPk($id);
		//if($utility->dateDiff($user->last_login)){
		//	$free_account = false;
		//}
		
		//if($bitcoin -> save()) {
			//$rCoin->setaccount($bitcoinAddress, '"'.$bitcoin -> id.'"');
		//}

		$this -> render('process', array('bitcoinAddress' => $bitcoinAddress, 'rate' => $rate, 'btc_total' => $payment->btc_amount, 'packageSelected' => $package, 'free_account' => $free_account));
	}
	
	public function actionCreateFree() {
		//$id = Yii::app()->session['clandestine_id'];
		$id = Yii::app()->user->getState('userid');
		
//		$mailbox = Mailbox::model()->findByAttributes(array('user_id' => $id));
//		$mailbox -> size = '10M';
//		$mailbox -> smtp = null;
		
		$user = Users::model()->findByPk($id);
//		$user->premium = null;
		//$user->password_reminder = 0;
		//$user -> downgrade_date = date('Y-m-d H:i:s', time());
		//$user->disabled = 0;
		
		$password = Yii::app()->user->getState('password');
		
		// Create temp mailbox record for script
//		$tempbox = new TempMailbox;
//		$tempbox -> user_id = $user -> id;
//		$tempbox -> email = $user -> email;
//		$tempbox -> password = $password;
//		$tempbox -> size = '10M';
//		$tempbox -> smtp = null;
		//$tempbox -> updated = 1;
		
//		if($mailbox -> save() && $user->save() && $tempbox -> save()) {
			//Payments::model()->findLastPayment($id, false)->delete();
			//$payment = Payments::model()->deleteAll('user_id=:user_id',array(':user_id'=>$id));

//			$backend = new Backend;
			//$backend -> deleteAllMail($mailbox->email);
//			$backend -> createMailBox();

		
			/* Automatic Login of User after Successful Signup */
			$login = new LoginForm;
			$login -> username = $user -> email;
			$login -> password = $password;

			// If Login
			if($login -> validate() && $login -> login()) {
				Users::model()->updateLastLogin($login -> username); //Update last login
								
				$utility = new UtilityFunction;
				$utility->fileToken($user -> email, $password);
					
				$utility->touch($user->email);
				
				$this->redirect(Yii::app()->urlManager->createUrl('/site/welcome'));
			}else{
				Yii::app()->user->setFlash('success', "Registration is successful! Please use the form below to login");
				//$this -> redirect(Yii::app()->urlManager->createUrl('/site/login'));
				$this->redirect(Yii::app()->urlManager->createUrl('/site/welcome'));
			}
//		}else{
//			Yii::app()->user->setFlash('error', "Something went wrong. Please try again!");
//			$this->redirect(Yii::app()->urlManager->createUrl('/site/register'));
//		}
	}
	
	public function actionBitcoin() {
		if(Yii::app()->request->isAjaxRequest){
			//$id = Yii::app()->session['clandestine_id'];
			$id = Yii::app()->user->getState('userid');
			$bitCoin = new BitcoinrBit(); 
			$transactions = $bitCoin->listtransactions($id);
			if(!empty($transactions)){
				$transaction = $transactions[count($transactions)-1];
				
				if($transaction['address'] == $_REQUEST['address']){
					if($transaction['confirmations'] == 0){
						echo json_encode(array('response' => 'confirmation'));
						Yii::app()->end();
					}else{
						$user = Users::model()->findByPk($id);
                        if($user->premium == 1){
						    $password = Yii::app()->user->getState('password');
								
						    // Automatic Login of User after Successful Signup
						    $login = new LoginForm;
						    $login -> username = $user -> email;
						    $login -> password = $password;

						    // If Login
						    if($login -> validate() && $login -> login()) {
                                Users::model()->updateLastLogin($login -> username); //Update last login

                                $utility = new UtilityFunction;
                                $utility->fileToken($user -> email, $password);

                                $utility->touch($user->email);

                                $url = Yii::app()->urlManager->createUrl('/site/welcome');
						    }else{
							    //Yii::app()->user->setFlash('success', "Registration is successful! Please use the form below to login");
							    //$this->redirect(Yii::app()->urlManager->createUrl('/site/welcome'));
							    $url = Yii::app()->urlManager->createUrl('/site/welcome');
						    }
						    echo json_encode(array('response' => 'paid', 'url' => $url));
							Yii::app()->end();
						}else{
                            $payment = Payments::model()->findLastPayment($id, false);
                            if($user->wallet_amount < $payment->btc_amount){
							    echo json_encode(array('response' => 'not_money'));
							    Yii::app()->end();
                            }
						}
					}
				}
			}
		}
	}
}
