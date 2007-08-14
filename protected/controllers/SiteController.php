<?php

class SiteController extends Controller
{
    protected function beforeAction()
    {
        if(!Yii::app()->user->getState('language'))
        {
            $lang_browser = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            $lang = Languages::model()->find(
                array(
                    'condition' => 'code LIKE :lang AND disabled = 0',
                    'params' => array(':lang' => '%'.$lang_browser.'%'),
                ));

            if($lang){
                Yii::app()->user->setState('language', $lang['code']);
            }else{
                Yii::app()->user->setState('language', 'en_US');
            }

            //return false;
        }
        return true;
    }
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		$content = Content::model()->find("alias = 'what-we-are'");
		$this->render('index', array('model' => $content));
	}

	/**
	 *
	 */
	public function actionWhyus() {
		$content = Content::model()->find("alias = 'why-us'");
		$this -> render('whyus', array('model' => $content));
	}

	/**
	 *
	 */
	public function actionTOS() {
		$content = Content::model()->find("alias = 'tos'");
		$this -> render('tos', array('model' => $content));
	}

	/**
	 *
	 */
	public function actionPrivacy() {
		$content = Content::model()->find("alias = 'privacy'");
		$this -> render('privacy', array('model' => $content));
	}
	
	public function actionWelcome() {
		$content = Content::model()->find("alias = 'welcome-register'");
		$user = Users::model()->findByPk(Yii::app()->user->getState('userid'));
		if($content){
			$content_text = str_replace('%first_name%', $user->first_name, $content->content);
			$content_text = str_replace('%last_name%', $user->last_name, $content_text);
			$content_text = str_replace('%email%', $user->email, $content_text);
		}else{
			$content_text = 'Welcome '.$user->first_name.' '.$user->last_name.', thanks for opening an inbox with clandestine.se';
		}
		$this -> render('welcome', array('content' => $content_text));
	}

	/**
	 * Registration Choice
	 * Free/Premium
	 *
	 */
    public function actionRegister() {

        if(isset($_POST['account_type'])) {
            $this -> redirect(Yii::app()->urlManager->createUrl('site/signup/', array('id' => $_POST['account_type'])));
        }

        $this -> render('register');
    }

	/**
	 * Signup User
	 * 
	 */
    public function actionSignup($id) {
        $model = new Users;

        $this->performAjaxValidation($model);

         //* @param $id
         //* 1 - Free
         //* 2 - Premium

        $premium = null;
        if($id == 2) {
            $premium = 1; //set the user as premium
        }
        $model -> scenario = 'register'; //use the scenario for validation

        if(isset($_POST['Users'])) {
            $model -> attributes = $_POST['Users'];

            // store email and password entered
            $password = $model -> password;
            $emailprefix = $model -> email;

            // Validate User
            if($model -> validate()) {
                $model -> password = hash('whirlpool',$password); //whirlpool hashing password
                $model -> confirm_password = hash('whirlpool', $password); //whirlpool hashing password
                $model -> email = $model -> email . $model -> emailsuffix; //Append the email suffix
                $utility = new UtilityFunction;
                $model->invite_token = $utility->generateInviteToken();

                if(isset($_REQUEST['token'])){
                    $inviter = Users::model()->findByAttributes(array('invite_token' => $_REQUEST['token']));
                    if(!empty($inviter)){
                        $model->inviter_id = $inviter['id'];
                    }
                }

                // Save User
                if($model -> save()) {
                    //Yii::app()->session['user_id'] = $model->id;
                    Yii::app()->user->setState('userid', $model->id);
                    // Create Mailbox Record in db
                    $mailbox = new Mailbox;
                    $mailbox -> email = $model -> email;
                    $mailbox -> password = hash('whirlpool', $password);
                    $mailbox -> size = '10M';
                    $mailbox -> user_id = $model -> id;
                    if($mailbox -> save()){
                        // Create temp mailbox record for script
                        $tempbox = new TempMailbox;
                        $tempbox -> user_id = $model -> id;
                        $tempbox -> email = $model -> email;
                        $tempbox -> password = $password;
                        $tempbox -> size = '10M';

                        // Create Mailbox using dbmail command
                        if($tempbox -> save()) {
                            $backend = new Backend;
                            $backend -> createMailBox();

                            if(isset($_REQUEST['invite'])){
                                $invite = Invites::model()->findByPk($_REQUEST['invite']);
                                if(!empty($invite)){
                                    $invite->user_id = $model->id;
                                    $invite->status = 1;
                                    $invite->save();
                                }
                            }
                        }

                        // Automatic Login of User after Successful Signup
                        $login = new LoginForm;
                        $login -> username = $model -> email;
                        $login -> password = $password;

                        // If Login
                        if($login -> validate() && $login -> login()) {
                            Users::model()->updateLastLogin($login -> username); //Update last login

                            $utility = new UtilityFunction;
                            $utility->fileToken($model -> email, $password);

                            $utility->touch($model->email);

                            if($premium == 1){
                                $this -> redirect(Yii::app()->urlManager->createUrl('users/payment/', array('id' => $model -> id)));
                            }else{
                                $this -> redirect(Yii::app()->urlManager->createUrl('/site/welcome'));
                            }

                        }

                    }
                } else {
                    Yii::app()->user->setFlash('error', "Something went wrong. Please try again!");
                }
            }

            // Populate the form with correct value in case of validation failure
            $model -> email = $emailprefix;
            $model -> password = $password;
        }

        $this -> render('signup', array('model' => $model));
    }

	/**
	 * Signup User
	 * 
	 */
	public function actionUpgrade($id) {
		$model = Users::model()->findByPk($id);
		$package = isset($_GET['package']) ? $_GET['package'] : "";

		/* Check if the package sent through get request is valid */
		if(!empty($package)) {
			$validPackage = Packages::model()->findByPk($package);

			if(!$validPackage) {
				$this -> redirect(Yii::app()->urlManager->createUrl('site/packages/3', array('id' => $id)));
			}
		}
		if($model) {
			$model -> premium = 1; //set the user as premium
			$model -> downgrade_date = null;
			
			/* Save User */
			if($model -> save()) {
				/* Create Mailbox Record in db */
				$mailbox = Mailbox::model()->findByAttributes(array('user_id' => $id));
				$mailbox -> size = '250M';
				//$mailbox -> disabled = $model -> premium == '1' ? 1 : 0;
				if($mailbox -> save()){					
					/* Premium Account - Redirect User to Payment */
					$packageModel = Packages::model()->findByPk($package);

					$payment = new Payments;
					$payment -> user_id = $model -> id;
					$payment -> package_id = $package;
					$payment -> amount = $packageModel -> cost;
					$payment -> disabled = null;

					if($payment -> save()) {
						$this -> redirect(Yii::app()->urlManager->createUrl('/payments/create'));
					} else {									
						Yii::app()->user->setFlash('error', "Something went wrong. Please try again!");
						$this -> redirect(Yii::app()->urlManager->createUrl('site/packages/3'));
					}
				}
			} else {
				Yii::app()->user->setFlash('error', "Something went wrong. Please try again!");
				$this -> redirect(Yii::app()->urlManager->createUrl('site/packages/3'));
			}
		}else {
			Yii::app()->user->setFlash('error', "Something went wrong. Please try again!");
			$this -> redirect(Yii::app()->urlManager->createUrl('site/packages/3'));
		}

		$this -> render('upgrade', array('model' => $model, 'package' => $package));
	}
	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	/**
	 * Contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$urgency = $_POST['urgency'];
			$model->attributes = $_POST['ContactForm'];
			
			if($model->validate())
			{
				$clandestineMail = new ClandestineMail();
				$clandestineMail -> contactMail($model, $urgency);

				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	/**
	 * Login 
	 */
	public function actionLogin()
	{
		if(!Yii::app()->user->hasFlash('error') && !Yii::app()->user->hasFlash('success')) {
			Yii::app()->user->setFlash('success', 'Welcome, please login with top login form');
		}
		$this->render('welcome_login');
		/*
		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model -> attributes = $_POST['LoginForm'];

			// Use Yii Default Validation
			if($model -> validate()) {
				// Check if user exists
				$user = Users::model()->findUserByEmail($model -> username);

				if($user === null) { //if invalid user         
					$this -> errorCode = self::ERROR_USERNAME_INVALID;
		        }else if(!$user -> validatePassword($model -> password)) { // if incorrect password
					$this -> errorCode = self::ERROR_PASSWORD_INVALID;
				//}elseif($user->disabled){ 
					//Yii::app()->session['user_id'] = $user->id;
					//$this -> redirect(Yii::app()->urlManager->createUrl('/payments/create'));
				}else {//proceed to see if the user is premium user
					// If premium user
					if(!is_null($user -> premium)) {	
						// If two factor authentication is enabled			
						if(Multifactor::model()->checkMultiFactorEnabled($user ->id)) {
							// store some user setting
							Yii::app()->user->setState('uid', $user -> id);
							Yii::app()->user->setState('username', $model -> username);
							Yii::app()->user->setState('password', $model -> password);
							
							// take user to two factor authentication page 
							$this -> redirect(Yii::app()->urlManager->createUrl('/site/authentication'));
						} 
					}
				}

				// validate user input and redirect to the previous page if valid			
				if($model->validate() && $model->login()) {
					Users::model()->updateLastLogin($model -> username); //update the last login time
					$this->redirect(Yii::app()->urlManager->createUrl('/users/dashboard')); //take user to dashboard
				}		
			}			
		}

		// display the login form
		$this->render('login',array('model'=>$model));
		*/
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		$filename = '/var/www/clandestine.se/assets/token/'.Yii::app()->user->getState('token');
		if(file_exists($filename)) unlink($filename);
		Yii::app()->user->logout();
		//unset(Yii::app()->session['user_id']);
		$this->redirect(Yii::app()->homeUrl);
	}

	/**
	 * Layout Login
	 * 
	 */
	public function actionLayoutLogin() {
		if(isset($_POST) && !empty($_POST)) {
            $email_suffix = '@clandestine.se';
			$email = $_POST['email'];
            if(stripos($email, $email_suffix) === false){
                $email .= $email_suffix;
            }
			$password = $_POST['password'];
			$js = isset($_POST['jsoff']) ? 0 : 1;

			$model = new LoginForm;
			$model -> username = $email;
			$model -> password = $password;
			
			/* Use Yii Default Validation */
			if($model -> validate()) {
				/* Check if user exists */
				$user = Users::model()->findUserByEmail($model -> username);

				if($user === null) { //if invalid user         
					$this -> errorCode = self::ERROR_USERNAME_INVALID;
		        } else if(!$user -> validatePassword($model -> password)) { // if incorrect password
					$this -> errorCode = self::ERROR_PASSWORD_INVALID;
				} else {
					Yii::app()->user->setState('js', $js);
					Users::model()->updateLastLoginFromHistory($user->id);
					//if(!$user -> premium){
						//if(Users::model()->checkUserLoginDate($user ->id)){
						//	Yii::app()->user->setFlash('danger', 'Your account has been inactive for more then 2  weeks, in order to access this inbox you must upgrade to a premium account. We are sorry for the inconvenience. Please note if your account remains inactive it will be terminated.');
						//	$this -> redirect(Yii::app()->urlManager->createUrl('site/packages/3'));
						//}
					//}

					//proceed to see if the user is premium user
					/* If premium user */
					if($user -> premium) {
						if($user->password_reminder){
							$pas_date = $user->last_password_change;
							if(strtotime('+'.$user->password_reminder.' days', strtotime($pas_date)) < strtotime('now')){
								Yii::app()->user->setState('userid', $user->id);
								$this -> redirect(Yii::app()->urlManager->createUrl('site/changePassword'));
							}
						}
						/* If two factor authentication is enabled */				
						if(Multifactor::model()->checkMultiFactorEnabled($user ->id)) {
							/* store some user setting */
							Yii::app()->user->setState('uid', $user -> id);
							Yii::app()->user->setState('username', $model -> username);
							Yii::app()->user->setState('password', $model -> password);
							Yii::app()->user->setState('userid', $user->id);
							
							/* take user to two factor authentication page */
							$this -> redirect(Yii::app()->urlManager->createUrl('/site/authentication'));
						} 
					}
				}

				// validate user input and redirect to the previous page if valid			
				if($model->validate() && $model->login()) {
					
					$utility = new UtilityFunction;
					$utility->fileToken($user->email, $model -> password);
					
					$utility->touch($user->email);
					
					Users::model()->updateLastLogin($model -> username); //update the last login time
					
					$this->redirect(Yii::app()->urlManager->createUrl('/users/'.$user -> id)); //take user to dashboard
				}	
			}
		}

		Yii::app()->user->setFlash('error', 'Invalid Credentials! Please try again.');//using the form below
		$this -> redirect(Yii::app()->urlManager->createUrl('/site/login'));
	}

	
	public function actionLogoutTimer($id = 0){
		if($id == 0){
			Yii::app()->user->setState('logout', null);
		}elseif($id == 1){
			Yii::app()->user->setState('logout', true);
		}elseif($id ==2){
			//$this -> redirect('https://clandestine.se/webmail?token='.Yii::app()->user->getState('token'));
			Yii::app()->user->setFlash('error', 'For some reasons server not respond! Please relogin or try again later.');//
		}

		$this -> redirect(Yii::app()->urlManager->createUrl('/users/'.Yii::app()->user->getState('userid')));
	}
	/**
	 * Package Selection on Signup
	 */
	public function actionPackages($id) {
		$utility = new UtilityFunction;
		$model = Packages::model()->findAll('disabled is null AND id>0');
		$rate = $utility->getCurrentBitRate();

		if(isset($_POST['package'])) {
			if($id == 3){
				$package = $_POST['package'];
				
				$account = Yii::app()->user->getState('userid');
				$this -> redirect(Yii::app()->urlManager->createUrl('/site/upgrade', array('id' => $account, 'package' => $package)));
				
			}else{
				$package = $_POST['package'];
				$account = $_POST['account'];

				$this -> redirect(Yii::app()->urlManager->createUrl('/site/signup', array('id' => $account, 'package' => $package)));
			}
		}
		if(!Yii::app()->user->hasFlash('danger')){
			Yii::app()->user->setFlash('danger', 'Choose Packages');
		}
		$this -> render('packages', array('model' => $model, 'account' => $id, 'rate' => $rate));
	}

	/**
	 * Two factor Authentication
	 * 
	 */
	public function actionAuthentication() {
		$model = new Multifactor;
		$authentication = new MultiFactorAuthentication;

		/* If user came validated from previous screen */
		if(!isset(Yii::app()->user->uid)) {
			$this->redirect(Yii::app()->urlManager->createUrl('/site/login'));
		}

		/* Page Header */
		Yii::app()->user->setFlash('success', 'Two Factor Authentication');
		
		/* Find the next code index to ask for validation */
		$nextIndex = Multifactor::model()->findNextCodeIndexByUser(Yii::app()->user->uid);

		/* If the form is submitted */
		if(isset($_POST['Multifactor'])) {
			$model -> scenario = 'login';
			$model -> attributes = $_POST['Multifactor'];
			$model -> user_id = Yii::app()->user->uid;

			/* Using Yii Default validation */
			if($model -> validate()) {
				$multifactor = Multifactor::model()->findByAttributes(array('user_id' => Yii::app()->user->uid));

				/* validate for correct values */
				if($model -> user_key != $multifactor -> user_key) {
					$model -> user_key = ''; //empty the model if incorrect
				} else if ($model -> login_code != $authentication -> getHotpByKeyIndex($model -> user_key, $nextIndex)) {
					$model -> login_code = ''; //empty the model if incorrect
				} 

				/* this validation would mean everything is correct */
				if($model->validate()) {
					/* Try to login and set everything required by default yii login */
					$login = new LoginForm;
					$login -> username = Yii::app()->user->username;
					$login -> password = Yii::app()->user->password;

					/* Try Login */
					if($login -> login()) {
						/* update the multifactor next index */
						$multifactor -> next_index = $nextIndex + 1;
						$multifactor -> save(); 
						
						$utility = new UtilityFunction;
						$userModel = Users::model()->findByPk(Yii::app()->user->uid);
						$utility->fileToken($userModel->email, Yii::app()->user->password);
					
						$utility->touch($userModel->email);

						Users::model()->updateLastLogin(Yii::app()->user->username); // Update the login timestamp
						
					
						$this->redirect(Yii::app()->urlManager->createUrl('/users/'.Yii::app()->user->uid)); // take user to dashboard
					} else {
						Yii::app()->user->setFlash('error', 'Something went wrong!');
					}
				}
			}
		}
		
		$this -> render('authentication', array('model' => $model, 'nextIndex' => $nextIndex));
	}
	
	public function actionForgotPassword() 
	{
		$authEnabled = false;
		$nextIndex = 0;
		$step = 1;
		$model = new Users;
		$model -> scenario = 'changepassword';
		
		$multiModel = new Multifactor;

		if(isset($_POST['Users'])) {
			if(isset($_POST['Users']['email'])){
				$step = 2;
				$model = Users::model()->findUserByEmail($_POST['Users']['email']);
				if($model){
					Yii::app()->user->setState('userid', $model->id);
					$authEnabled = Multifactor::model()->checkMultifactorEnabled($model->id);
					$authentication = new MultiFactorAuthentication;
		
					// Find the next code index to ask for validation
					$nextIndex = Multifactor::model()->findNextCodeIndexByUser($model->id);
				}else{
					Yii::app()->user->setFlash('error', 'There are not this email');
					$this -> redirect(Yii::app()->urlManager->createUrl('/site/forgotPassword'));
				}
			}else{
				$step = 2;
				$model = Users::model()->findByPk(Yii::app()->user->getState('userid'));
				$authEnabled = Multifactor::model()->checkMultifactorEnabled($model->id);
				$authentication = new MultiFactorAuthentication;
		
				// Find the next code index to ask for validation
				$nextIndex = Multifactor::model()->findNextCodeIndexByUser($model->id);

				$model -> attributes = $_POST['Users'];
				$validate = false;
				if($model -> validate()){
					if($_POST['Users']['confirm_new_password'] != $_POST['Users']['new_password']) {
						Yii::app()->user->setFlash('error', 'Passwords are not the same!');
					}elseif($_POST['Users']['current_security_pin'] != $model -> security_pin ) {
						Yii::app()->user->setFlash('error', 'Current Security Pin is not correct!');
					}elseif($_POST['Users']['current_security_answer'] != $model -> security_answer ) {
						Yii::app()->user->setFlash('error', 'Current Security Answer is not correct!');
					}elseif($model->premium && $authEnabled && isset($_POST['Multifactor'])) {
						$multiModel -> scenario = 'login';
						$multiModel -> attributes = $_POST['Multifactor'];
						$multiModel -> user_id = $model->id;

						// Using Yii Default validation
						if($multiModel -> validate()) {
							$multifactor = Multifactor::model()->findByAttributes(array('user_id' => $model->id));
							
							// validate for correct values 
							if($multiModel -> user_key != $multifactor -> user_key) {
								$multiModel -> user_key = ''; //empty the model if incorrect
								Yii::app()->user->setFlash('error', 'Key is not correct!');
							} else if ($multiModel -> login_code != $authentication -> getHotpByKeyIndex($multiModel -> user_key, $nextIndex)) {
								$multiModel -> login_code = ''; //empty the model if incorrect
								Yii::app()->user->setFlash('error', 'Code is not correct!');
							} 
							
							// this validation would mean everything is correct
							if($multiModel->validate()) {
								$validate = true;
								// update the multifactor next index 
								$multifactor -> next_index = $nextIndex + 1;
								$multifactor -> save(); 
							}
						}
					}else{
						$validate = true;
					}
				
					if($validate){
						$mailbox = Mailbox::model()->findMailboxByUser($model->id);

						$mailbox -> password = hash('whirlpool', $model -> new_password);
						$model -> password = hash('whirlpool', $model -> new_password);
                        $model -> last_password_change = date('Y-m-d H:i:s');

						if($model -> save() && $mailbox -> save()) {
							// Create temp mailbox record for script 
							$tempbox = new TempMailbox;
                            $tempbox -> user_id = $model -> id;
							$tempbox -> email = $mailbox -> email;
							$tempbox -> password = $model -> new_password;
							$tempbox -> size = $mailbox -> size;
							$tempbox -> updated = 1;
							$tempbox -> save();

							//execute the backend script 
							$backend = new Backend;
							$backend -> updateMailBox();
								
							//$utility = new UtilityFunction;
							//$utility->fileToken($model->email, $model -> new_password);

							Yii::app()->user->setFlash('success', 'Password Changed Successfully');
							$this -> redirect(Yii::app()->urlManager->createUrl('/site/login'));
						} 
					}
				}
			}
		}
		$this -> render('forgot_password', array('model' => $model, 'multiModel' => $multiModel, 'authEnabled'=>$authEnabled, 'step' => $step, 'nextIndex'=>$nextIndex));
	}
	
	public function actionChangePassword() {	
		$id = Yii::app()->user->getState('userid');
		$model = Users::model()->findByPk($id);

		$model -> scenario = 'changepassword';
		
		$authEnabled = Multifactor::model()->checkMultifactorEnabled($id);
		
		$multiModel = new Multifactor;
		$authentication = new MultiFactorAuthentication;
		
		/* Find the next code index to ask for validation */
		$nextIndex = Multifactor::model()->findNextCodeIndexByUser($id);

		if(isset($_POST['Users'])) {
			$model -> attributes = $_POST['Users'];
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
					$model -> last_password_change = date('Y-m-d H:i:s', time());
					
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

						Yii::app()->user->setFlash('success', 'Password Changed Successfully');
						$this -> redirect(Yii::app()->urlManager->createUrl('/site/login'));
					} 
				}
			}
		}

		$this -> render('change_password', array('model' => $model, 'multiModel' => $multiModel, 'nextIndex' => $nextIndex, 'authEnabled' => $authEnabled));
	}
	
	public function actionMetaRefresh(){
		$this->layout = false;
		$this -> render('meta_refresh');
	}

    public function actionLanguage(){
        $save = false;
        $id = Yii::app()->user->getState('userid');
        $languages = Languages::model()->findAll('disabled = 0');
        //print_r($languages);die('end');
        if($id) $model = Users::model()->findByPk($id);

        if(Yii::app()->request->isPostRequest){
            Yii::app()->user->setState('language', $_POST['lang']);
            if($id){
                $model->language = $_POST['lang'];
                $save = $model->save();
            }else{
                $save = true;
            }

            if($save){
                Yii::app()->user->setFlash('success', 'Language changed');
            }else{
                Yii::app()->user->setFlash('error', "Something went wrong. Please try again!");
            }
        }

        $this -> render('language', array('languages' => $languages));
    }

    protected function performAjaxValidation($model)
    {
        if(isset($_POST['ajax']) && $_POST['ajax']==='register-form')
        {
            $model -> scenario = 'register';
            $model -> attributes = $_POST['Users'];
            $model -> email = $model -> email . $model -> emailsuffix;
            if(Users::model()->findUserByEmail($model -> email)){
                echo '{"Users_email":["This email already exists."]}';
            }else{
                echo CActiveForm::validate($model);
            }
            Yii::app()->end();
        }
    }
	
}