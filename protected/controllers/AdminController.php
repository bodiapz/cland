<?php

class AdminController extends Controller
{
	//protected $sku = 'df3456gtfdg4545fd';
	//$this->sku;
	
	/*public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}
	
	public function accessRules() {
        return array(
            array('allow',
                'actions' => array('CreateAll','ResetPassword', 'UpdateMailBox', 'GetMailBoxUsage', 'DeleteMailBox'),
                'ips' => array('5.135.193.138'),
				'users'=>array('?'),
            ),
            array('deny',
                'actions' => array('CreateAll','ResetPassword', 'UpdateMailBox', 'GetMailBoxUsage', 'DeleteMailBox'),
                'ips' => array('*'),
				'users'=>array('?'),
            ),
        );
    }*/

    protected function beforeAction()
    {
        if(Yii::app()->request->isPostRequest && isset($_REQUEST['sku']) && $_REQUEST['sku'] ='df3456gtfdg4545fd') {
            return true;
        }
        return false;
    }
	
	public function actionCreateAll() {
		$backend = new Backend;
		$backend -> createMailBox();
	}
	
	public function actionResetPassword() {
		$backend = new Backend;
		$backend -> updateMailBox();
	}
	
	public function actionUpdateMailBox() {
		$backend = new Backend;
		$backend -> updateMailBox();
	}
	
	public function actionGetMailBoxUsage() {
		if(isset($_POST['id'])){
			$id = (int)$_POST['id'];
				
			$user = Users::model()->findByPk($id);
			if($user){
				$backend = new Backend;
				$output = $backend -> getMailBoxUsage($user->email);
				echo json_encode($output);
            }
		}
	}
	
	public function actionDeleteMailBox() {
		if(isset($_POST['id'])){
			$id = (int)$_POST['id'];
				
			$user = Users::model()->findByPk($id);
			if($user){
				$backend = new Backend;
				$backend -> deleteAllMail($user->email);
					
				$user->delete();
				Mailbox::model()->deleteAll('user_id=:user_id',array(':user_id'=>$id));
				TempMailbox::model()->deleteAll('email=:email',array(':email'=>$user->email));
			}
		}
	}

    public function actionMail() {
        if(isset($_POST['email'])){
            $utility = new UtilityFunction();
            echo $utility -> mail_send($_POST['email'], $_POST['message'], $_POST['subject']);
        }
    }
}
