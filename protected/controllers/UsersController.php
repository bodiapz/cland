<?php

class UsersController extends Controller
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

    protected function beforeAction()
    {
        $model = Users::model()->findByPk(Yii::app()->user->getState('userid'));
        if(Yii::app()->controller->action->id != 'payment' && Yii::app()->controller->action->id != 'security' && (empty($model->security_question) || empty($model->security_pin))){
            $this -> redirect(Yii::app()->urlManager->createUrl('users/security/', array('id' => $model -> id)));
        }
        if($model->language != null){
            Yii::app()->user->setState('language', $model -> language);
        }elseif(Yii::app()->user->getState('language')){
            $model -> language = Yii::app()->user->getState('language');
            $model -> save();
        }
        return true;
    }

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',
				'actions' => array('deleteAllMail', 'multifactor', 'disableMultiFactor', 'papercode'),
				'expression' => 'Yii::app()->user->getState("premium") == 1'
			),
			array('allow', // allow authenticated user to perform actions
				'actions' => array('view', 'update', 'dashboard', 'password', 'security', 'billing', 'welcomeNews', 
									'PasswordReminder', 'DisablePassReminder', 'Smtp', 'forward', 'DeleteForward',
									'addFunds', 'payment', 'invite'),
				'users'=>array('@'),
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
		if(Yii::app()->user->getState('userid') != $id){
			$this -> redirect('/');
		}
		$model 	 = $this->loadModel($id);
		$payment = Payments::model()->findLastPayment($id);
		$backend = new Backend;

		$mailbox = array();
		$mailbox = $backend -> getMailBoxUsage($model -> email);
		
		$utility = new UtilityFunction;
		$new_message = $utility->getUserCountMessages();

		
		$newsModel = News::model()->findAllByAttributes(array('status' => 1));
        $content_text = '';
		if(!empty($newsModel)){
            $i =0;
            foreach($newsModel as $new){
                $content_text = str_replace('%first_name%', $model->first_name, $new->description);
                $content_text = str_replace('%last_name%', $model->last_name, $content_text);
                $content_text = str_replace('%email%', $model->email, $content_text);
                $news[$i]['description'] = str_replace('%title%', $new->title, $content_text);
                $i++;
            }
		}
		$this -> render('view',array('model' => $model,'payment' => $payment, 'mailbox' => $mailbox, 'news' => $news, 'new_message' => $new_message));
	}
	
	public function actionBilling($id)
	{
		if(Yii::app()->user->getState('userid') != $id){
			$this -> redirect('/');
		}
		$model = $this->loadModel($id);
		
		if(isset($_REQUEST['status'])){
			$model -> auto_payment = $_REQUEST['status'];
			$model -> save();
			//print_r($model->getErrors());exit;
		}
		
		$utility = new UtilityFunction;
		
		$wallet_amount = $utility -> convert($model->wallet_amount);
		
		$this -> render('billing',array('model' => $model, 'wallet_amount' => $wallet_amount));
	}
	
	public function actionAddFunds($id)
	{
		if(Yii::app()->user->getState('userid') != $id){
			$this -> redirect('/');
		}
		
		//Yii::app()->user->setFlash('success', 'Add funds');
		$model 	 = $this->loadModel($id);
		
		$rCoin = new BitcoinrBit; // Bitcoin API Object
		$utility = new UtilityFunction;
		
		$rate = $utility -> getCurrentBitRate();
		
		//$transaction = Transactions::model()->findLastTransByUser($id);
		$address = BcAddresses::model()->findLastBcAddressByUser($id);
		
		if(empty($address) || $address->btc_amount > 0){
			$bitcoinAddress = $rCoin->getnewaddress($id); // New Bitcoin Address
		
			$bitcoin = new BcAddresses;
			$bitcoin -> user_id = $id;//Yii::app()->user->getState('userid');
			$bitcoin -> address = $bitcoinAddress;
			$bitcoin -> save();
		}else{
			$bitcoinAddress = $address->address;
		}
		
		$this -> render('add_funds',array('model' => $model, 'bitcoinAddress' => $bitcoinAddress, 'rate' => $rate,));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Users;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Users']))
		{
			$model->attributes=$_POST['Users'];
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
		if(Yii::app()->user->getState('userid') != $id){
			$this -> redirect('/');
		}
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Users']))
		{
			$model->attributes=$_POST['Users'];
			if($model->save()) {
				Yii::app()->user->setFlash('success', 'Account Updated Successfully');
				$this->redirect(array('users/update','id'=>$model->id));
			}
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
		if(Yii::app()->user->getState('userid') != $id){
			$this -> redirect('/');
		}
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
		if(Yii::app()->user->getState('userid') != $id){
			$this -> redirect('/');
		}
		$dataProvider=new CActiveDataProvider('Users');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Users('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Users']))
			$model->attributes=$_GET['Users'];

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
		$model=Users::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='users-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	/**
	 *
	 */
	public function actionDashboard() {
		$this -> render('dashboard');
	}


	/**
	 * Change Password
	 */
	public function actionPassword($id) {	
		if(Yii::app()->user->getState('userid') != $id){
			$this -> redirect('/');
		}
		$model = $this -> loadModel($id);

		$model -> scenario = 'changepassword';
		
		$authEnabled = Multifactor::model()->checkMultifactorEnabled($id);
		
		$multiModel = new Multifactor;
		$authentication = new MultiFactorAuthentication;
		
		/* Find the next code index to ask for validation */
		$nextIndex = Multifactor::model()->findNextCodeIndexByUser($id);

		if(isset($_POST['Users'])) {
			$model -> attributes = $_POST['Users'];

//            $utility = new UtilityFunction;
//            $model -> current_password = $utility->password($model -> current_password);
//            $model -> new_password = $utility->password($model -> new_password);
//            $model -> confirm_new_password = $utility->password($model -> confirm_new_password);

			$validate = false;
			if($model -> validate()) {
				if(hash('whirlpool', $model -> current_password) !== $model -> password) {
					Yii::app()->user->setFlash('error', 'Current Password is not correct!');
				} elseif($model->premium && $authEnabled && isset($_POST['Multifactor'])) {
					$multiModel -> scenario = 'login';
					$multiModel -> attributes = $_POST['Multifactor'];
					$multiModel -> user_id = $id;

					/* Using Yii Default validation */
					if($multiModel -> validate()) {
						$multifactor = Multifactor::model()->findByAttributes(array('user_id' => $id));
						
						/* validate for correct values */
						if($multiModel -> user_key != $multifactor -> user_key) {
							$multiModel -> user_key = ''; //empty the model if incorrect
						} else if ($multiModel -> login_code != $authentication -> getHotpByKeyIndex($multiModel -> user_key, $nextIndex)) {
							$multiModel -> login_code = ''; //empty the model if incorrect
						} 
						
						/* this validation would mean everything is correct */
						if($multiModel->validate()) {
							$validate = true;
							/* update the multifactor next index */
							$multifactor -> next_index = $nextIndex + 1;
							$multifactor -> save(); 
						}
					}
				}else{
					$validate = true;
				}
				
				if($validate){
					$mailbox = Mailbox::model()->findMailboxByUser($id);

					$mailbox -> password = hash('whirlpool', $model -> new_password);
					$model -> password = hash('whirlpool', $model -> new_password);

                    $model -> last_password_change = date('Y-m-d H:i:s');
					
					if($model -> save() && $mailbox -> save()) {
					
						/* Create temp mailbox record for script */
						$tempbox = new TempMailbox;
                        $tempbox -> user_id = $model -> id;
						$tempbox -> email = $mailbox -> email;
						$tempbox -> password = $model -> new_password;
						$tempbox -> size = $mailbox -> size;
						$tempbox -> updated = 1;
						$tempbox -> save();
						
						/* execute the backend script */
						$backend = new Backend;
						$backend -> updateMailBox();
						
						$utility = new UtilityFunction;
						$utility->fileToken($model->email, $model -> new_password, false);
						
						$utility->curl('https://clandestine.se/webmail/?_task=logout&_action=changepassword');

						Yii::app()->user->setFlash('success', 'Password Changed Successfully');
						$this -> redirect(Yii::app()->urlManager->createUrl('/users/password', array('id' => $id)));
					} 
				}
			}
		}

		$this -> render('password', array('model' => $model, 'multiModel' => $multiModel, 'nextIndex' => $nextIndex, 'authEnabled' => $authEnabled));
	}

	/**
	 * Change Security Info
	 */
	public function actionSecurity($id) {
		if(Yii::app()->user->getState('userid') != $id){
			$this -> redirect('/');
		}

		$model = $this -> loadModel($id);

		$model -> scenario = 'security';

		if(isset($_POST['Users'])) {
			$model -> attributes = $_POST['Users'];
            unset($model -> security_pin);
			if($model -> validate()) {
				if(!empty($model->security_question) && $_POST['Users']['current_security_answer'] != $model -> security_answer) {
					Yii::app()->user->setFlash('error', 'Current Security Answer is not correct!');
				} else {
                    if(!empty($model->new_security_question) && !empty($model->new_security_answer)){
                        $model -> security_question = $model->new_security_question;
                        $model -> security_answer = $model -> new_security_answer;
                    }
                    if(!empty($model -> new_security_pin)) {
                        $model->security_pin = $model->new_security_pin;
                    }

					if($model -> save()) {
						Yii::app()->user->setFlash('success', 'Security Info Changed Successfully');
						$this -> redirect(Yii::app()->urlManager->createUrl('/users/security', array('id' => $id)));
					}
				}
			}
		}

        if(empty($model->security_question)){
            Yii::app()->user->setFlash('error', 'For security reason, please fill those fields');
        }

		$this -> render('security', array('model' => $model));
	}

	/**
	 * Delete All Mails
	 */
	public function actionDeleteAllMail($id) {
		if(Yii::app()->user->getState('userid') != $id){
			$this -> redirect('/');
		}
		$model = $this -> loadModel($id);

		if(Yii::app()->request->isPostRequest) {
			$securityPin = $_POST['security_pin'];
			if($model -> security_pin == $securityPin) {
				$email = $model -> email;

				//Mailbox::model()->deleteMailbox($email);
				
				$backend = new Backend;
				$backend -> emptyAllMail($email);

				Yii::app()->user->setFlash('success', 'All of you Emails are Deleted');
			} else {
				Yii::app()->user->setFlash('error', 'Invalid Security Pin');
			}
		}

		Yii::app()->user->setFlash('warning', 'Delete All Mail');
		$this -> render('deletemail', array('model' => $model));
	}

	/**
	 * Multifactor Authentication
	 * 
	 */
	public function actionMultifactor($id) {
		if(Yii::app()->user->getState('userid') != $id){
			$this -> redirect('/');
		}
		Yii::app()->user->setFlash('success', 'Enable two factor Authentication');
		$authentication = new MultiFactorAuthentication;

		$model = new Multifactor;
		if(isset($_POST['Multifactor'])) {
			/* for empty validation using Yii model */
			$model -> scenario = 'enable';
			$model -> attributes = $_POST['Multifactor'];
			$model -> user_id = Yii::app()->user->getState('userid');
			
			if($model -> validate()) {
				$multifactor = Multifactor::model()->findByAttributes(array('user_id' => Yii::app()->user->getState('userid')));

				/* validate for correct values */
				if($model -> user_key != $multifactor -> user_key) {
					$model -> user_key = ''; //empty the model if incorrect
				} else if ($model -> code != $authentication -> getHotpByKeyIndex($model -> user_key, 0)) {
					$model -> code = ''; //empty the model if incorrect
				} 

				/* this validation would mean everything is correct */
				if($model -> validate()) {
					/* if so, enable authentication */
					$multifactor -> next_index = 1;
					$multifactor -> enabled = 1;
					if($multifactor -> save()) {
						Yii::app()->user->setFlash('success', 'Paper code two-factor authentication enabled succesfully. Next unused code is number two. ');
						$this -> redirect(Yii::app()->urlManager->createUrl('/users/security', array('id' => $id)));
					}	
				}
			} 
		} else {	
			/* See if the multi factory key is already created for the user */
			$userKey = Multifactor::model()->findByAttributes(array('user_id' => Yii::app()->user->getState('userid')));		

			if(empty($userKey)) { // if not								
				$keyExist = false;
				
				/* Loop through until the generated key is unique */
				do {
					$key = $authentication -> getMultiFactorKey(); //create the key for user

					$multifactor = Multifactor::model() ->findByAttributes(array('user_key' => $key));
					if(!empty($multifactor)) {
						$keyExist = true;
					} else {
						$keyExist = false;
					}

					/** 
					 * @todo need to add termination condition in case it goes for infinity
					 */
				} while($keyExist);

				/* create the record for multi-factor authentication */
				$multifactor = new Multifactor;
				$multifactor -> user_id = Yii::app()->user->getState('userid');
				$multifactor -> user_key = $key;
				$multifactor -> save();
			}
		}

		$this -> render('multifactor', array('model' => $model, 'id' => $id));
	}	

	/** 
	 *
	 */
	public function actionDisableMultiFactor($id) {
		if(Yii::app()->user->getState('userid') != $id){
			$this -> redirect('/');
		}
		$model = new Multifactor;
		$authentication = new MultiFactorAuthentication;

		Yii::app()->user->setFlash('danger', 'Disable two factor Authentication');
		$nextIndex = Multifactor::model()->findNextCodeIndexByUser($id);

		if(isset($_POST['Multifactor'])) {
			$model -> attributes = $_POST['Multifactor'];
			$model -> scenario = 'disable';
			$model -> user_id = Yii::app()->user->getState('userid');

			if($model -> validate()) {				
				$multifactor = Multifactor::model()->findByAttributes(array('user_id' => Yii::app()->user->userid));

				/* validate for correct values */
				if($model -> user_key != $multifactor -> user_key) {
					$model -> user_key = ''; //empty the model if incorrect
				} else if ($model -> next_code != $authentication -> getHotpByKeyIndex($model -> user_key, $nextIndex)) {
					$model -> next_code = ''; //empty the model if incorrect
				} 

				/* this validation would mean everything is correct */
				if($model -> validate()) {
					if($multifactor -> delete()) {
						Yii::app()->user->setFlash('success', 'Paper code two-factor authentication disabled succesfully.');
						$this -> redirect(Yii::app()->urlManager->createUrl('/users/security', array('id' => $id)));
					}
				}
			} 
		}

		$this -> render('disable', array('model' => $model, 'nextIndex' => $nextIndex, 'id' => $id));
	}

	/**
	 *
	 */
	public function actionPaperCode($id) {
		if(Yii::app()->user->getState('userid') != $id){
			$this -> redirect('/');
		}
		$this -> layout = false;
		$utility = new UtilityFunction;

		$key = Multifactor::model()->findByAttributes(array('user_id' => $id)) -> user_key;

		if(!empty($key)) {
			$authentication = new MultiFactorAuthentication;
			$hotpValue = $authentication -> getHotpByKey($key);
		}

		$hotpValue = $utility -> splitInTen($hotpValue);

		$this -> render('papercode', array('hotp' => $hotpValue, 'key' => $key));
	}
	
	public function actionPasswordReminder($id) {	
		if(Yii::app()->user->getState('userid') != $id){
			$this -> redirect('/');
		}
		$model = $this -> loadModel($id);

		//$model -> scenario = 'changepassword';
		
		Yii::app()->user->setFlash('success', 'Password Reminder');
		
		$authEnabled = Multifactor::model()->checkMultifactorEnabled($id);
		$nextIndex = Multifactor::model()->findNextCodeIndexByUser($id);

		$multiModel = new Multifactor;
		$authentication = new MultiFactorAuthentication;

		if(Yii::app()->request->isPostRequest) {
			$validate = true;
			$securityPin = $_POST['security_pin'];
			if($model -> security_pin == $securityPin) {
				if($model->premium && $authEnabled && isset($_POST['Multifactor'])) {
					$multiModel -> scenario = 'login';
					$multiModel -> attributes = $_POST['Multifactor'];
					$multiModel -> user_id = $id;

					/* Using Yii Default validation */
					if($multiModel -> validate()) {
						$multifactor = Multifactor::model()->findByAttributes(array('user_id' => $id));
						
						/* validate for correct values */
						if($multiModel -> user_key != $multifactor -> user_key) {
							$multiModel -> user_key = ''; //empty the model if incorrect
						} else if ($multiModel -> login_code != $authentication -> getHotpByKeyIndex($multiModel -> user_key, $nextIndex)) {
							$multiModel -> login_code = ''; //empty the model if incorrect
						} 
						
						/* this validation would mean everything is correct */
						if($multiModel->validate()) {
							/* update the multifactor next index */
							$multifactor -> next_index = $nextIndex + 1;
							$multifactor -> save(); 
						}else{
							$validate = false;
						}
					}
				}
				
				if($validate){
					$model->password_reminder = (int)$_POST['password_reminder'];
					$model->last_password_change = date('Y-m-d', time());
					$model->save();
					
					Yii::app()->user->setFlash('success', 'Password Reminder Enabled');
					$this -> redirect(Yii::app()->urlManager->createUrl('/users/update', array('id' => $id)));
				}
				
			} else {
				Yii::app()->user->setFlash('error', 'Invalid Security Pin');
			}
		}

		$this -> render('password_reminder', array('model' => $model, 'multiModel' => $multiModel, 'nextIndex' => $nextIndex, 'authEnabled' => $authEnabled));
	}
	
	public function actionDisablePassReminder($id) {	
		if(Yii::app()->user->getState('userid') != $id){
			$this -> redirect('/');
		}
		$model = $this -> loadModel($id);

		$model->password_reminder = 0;
		$model->save();
					
		Yii::app()->user->setFlash('success', 'Password Reminder Disabled');
		$this -> redirect(Yii::app()->urlManager->createUrl('/users/update', array('id' => $id)));
	}
	
	public function actionSmtp($id)
	{
		if(Yii::app()->user->getState('userid') != $id){
			$this -> redirect('/');
		}
		$model 	 = $this->loadModel($id);

		$content = Content::model()->find("alias = 'smtp-settings'");
		
		$this -> render('smtp',array('model' => $model, 'content' => $content->content));
	}
	
	public function actionForward($id)
	{
		if(Yii::app()->user->getState('userid') != $id){
			$this -> redirect('/');
		}
		$model 	 = $this->loadModel($id);
		//$model -> scenario = 'forward';
		
		//if(Yii::app()->request->isPostRequest)
		if(isset($_POST['email_forward'])){
			$email_forward = $_POST['email_forward'];
			
			$forwards = MailForward::model()->findMailForwardByUser($id, $email_forward);
			if(empty($forwards)){
				$backend = new Backend;
				$backend->addForwardMail($model -> email, $email_forward);
			
				$mailForward = new MailForward;
				$mailForward -> user_id = $id;
				$mailForward -> email = $model -> email;
				$mailForward -> email_forward = $email_forward;
				$mailForward -> save();
			}
		}

        if(!isset($page)) $page = 0;

		$forwards = MailForward::model()->findMailForwardByUser($id, null, $page);
		
		$this -> render('forward',array('model' => $model, 'forwards' => $forwards));
	}
	
	public function actionDeleteForward($id)
	{
		//if(Yii::app()->request->isPostRequest){
			$forward = MailForward::model()->findByPk($id);
			if(Yii::app()->user->getState('userid') == $forward->user_id){
			
				$model 	 = $this->loadModel(Yii::app()->user->getState('userid'));
				
				$backend = new Backend;
				$backend->deleteForwardMail($model->email, $forward -> email_forward);
				
				$forward -> delete();
			}
			
			$this -> redirect(Yii::app()->urlManager->createUrl('/users/forward', array('id' => $model -> id)));
		//}else
		//	throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}
	
	public function actionPayment($id) {
        if(Yii::app()->user->getState('userid') != $id){
			$this -> redirect('/');
		}
        //Yii::app()->user->setFlash('success', 'Upgrade Account');

		$model = $this->loadModel($id);
		
		$utility = new UtilityFunction;
		$packages = Packages::model()->findAll('disabled is null AND id>0');
		$rate = $utility->getCurrentBitRate();
		
		//if(Yii::app()->request->isPostRequest){
			$package_id = isset($_REQUEST['package']) ? $_REQUEST['package'] : "";
			
			// Check if the package sent through get request is valid
			if(!empty($package_id)) {
				$package = Packages::model()->findByPk($package_id);
				if($package) {
                    $btc_total = $package -> cost / $rate['last'];

                    $payment = new Payments;
                    $payment -> user_id = $model -> id;
                    $payment -> package_id = $package_id;
                    $payment -> amount = $package -> cost;
                    $payment -> btc_amount = $btc_total;
                    $payment -> save();

                    $free_account = !Users::model()->checkUserLoginDate($id);

                    $rCoin = new BitcoinrBit; // Bitcoin API Object

                    $address = BcAddresses::model()->findLastBcAddressByUser($id);

                    if(empty($address) || $address->btc_amount > 0){
                        $bitcoinAddress = $rCoin->getnewaddress($id); // New Bitcoin Address

                        $bitcoin = new BcAddresses;
                        $bitcoin -> user_id = $id;//Yii::app()->user->getState('userid');
                        $bitcoin -> address = $bitcoinAddress;
                        $bitcoin -> save();
                    }else{
                        $bitcoinAddress = $address->address;
                    }

                    $this -> render('add_funds',array('model' => $model, 'bitcoinAddress' => $bitcoinAddress, 'rate' => $rate, 'btc_total' => $btc_total, 'packageSelected' => $package_id, 'free_account' => $free_account));
                    Yii::app()->end();
                    /*
					if($model->wallet_amount >= $amount){
						$model -> wallet_amount = $model->wallet_amount - $amount;
						$model -> premium = 1; //set the user as premium
						$model -> downgrade_date = null;

						// Create Mailbox Record in db
						$mailbox = Mailbox::model()->findByAttributes(array('user_id' => $id));
						$mailbox -> size = '250M';
						$mailbox -> smtp = 1;
						//$mailbox -> disabled = $model -> premium == '1' ? 1 : 0;

						$payment = new Payments;
						$payment -> user_id = $model -> id;
						$payment -> package_id = $package_id;
						$payment -> amount = $package -> cost;
						$payment -> btc_amount = $amount;
						$payment -> payment_date = date('Y-m-d H:i:s', time());
						$payment -> paid = 1;
						$payment -> disabled = null;
						$payment_last = Payments::model()->findLastPayment($id);
						if($payment_last && $payment_last -> next_due_date){
							$payment -> next_due_date = date('Y-m-d  H:i:s', strtotime("+".$package->term." month", strtotime($payment_last->next_due_date)));
						}else{
							$payment -> next_due_date = date('Y-m-d  H:i:s', strtotime("+".$package->term." month", time()));
						}

						$tempbox = new TempMailbox;
						$tempbox -> email = $model -> email;
						//$tempbox -> password = $model -> password;
						$tempbox -> size = '250M';
						$tempbox -> smtp = 1;
						$tempbox -> updated = 1;

						if($payment->save() && $mailbox -> save() && $model->save() && $tempbox -> save()) {
							$this -> redirect(Yii::app()->urlManager->createUrl('/users/view', array('id' => $model -> id)));
						}
					}else{
						Yii::app()->user->setFlash('error', "Not enough money. Please top up wallet!");
						$this -> redirect(Yii::app()->urlManager->createUrl('/users/billing', array('id' => $model -> id)));
					}
                    */


				}
			}
		//	Yii::app()->user->setFlash('error', "Something went wrong. Please try again!");
		//}
		
		$this -> render('payment', array('model' => $model, 'packages' => $packages, 'rate' => $rate));
	}

    public function actionInvite()
    {
        $id = Yii::app()->user->getState('userid');

        $model 	 = $this->loadModel($id);

        if(isset($_POST['email_invite'])){
            $email_invite = $_POST['email_invite'];

            $invite_user = Invites::model()->findByAttributes(array('inviter_id' => $id, 'email_invite' => $email_invite, 'status' => 1));

            if(empty($invite_user)){
                $message = MessageTemplate::model()->findByAttributes(array('title' => 'Mail invite'));
                if(!empty($message)){
                    $utility = new UtilityFunction;
                    if(empty($model->invite_token)){
                        $model->invite_token = $utility->generateInviteToken();
                        $model->save();
                    }

                    $invite = new Invites;
                    $invite -> inviter_id = $id;
                    $invite -> email = $model -> email;
                    $invite -> email_invite = $email_invite;
                    if($invite -> save()){
                        $send_link = '<a href="https://clandestine.se/site/signup/2?invite='.$invite->id.'&token='.$model->invite_token.'">this link</a>';
                        $send_text = str_replace('%invite_link%', $send_link, $message['message']);
                        if($utility->mail_send($email_invite, $send_text, $subject = 'Invite to clandestine.se')){
                            Yii::app()->user->setFlash('success', "invitation message sent to ".$email_invite."!");
                        }
                    }else{
                        print_r($invite->getErrors());
                        die('end');
                    }
                }
            }else{
                Yii::app()->user->setFlash('error', "User is already invited!");
            }
        }

        if(!isset($page)) $page = 0;

        $invites = Invites::model()->findInvitesByUser($id, $page);

        $this -> render('invite',array('model' => $model, 'invites' => $invites));
    }
}
