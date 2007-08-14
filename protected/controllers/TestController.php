<?php

class TestController extends Controller
{
    public function actionSendNotification(){
        //$fp = @fsockopen('clandestine.se', 80, $errno, $errstr, 2);
        //if(!$fp){
        $notification = NotificationMessages::model()->findByPk(1);
        if(!empty($notification->disabled)){
            $users = Users::model()->findAllByAttributes(array('premium' => 1));
            if(!empty($users)){
                foreach($users as $user){
                    $premium_due_date = Payments::model()->findDueDateByUser($user['id']);
                    if(!empty($premium_due_date) && $premium_due_date <= date('Y-m-d', strtotime('+'.$notification->days.'days'))){
                        $utility = new UtilityFunction();
                        $utility->mail_send($user->email, $notification->message, $notification->subject);
                    }
                }
            }
        }
        //}
        die('end');
    }
    /*
    public function actionTest(){
        $fp = @fsockopen('clandestine.se', 80, $errno, $errstr, 2);
        die($fp);
    }

    public function actionSendTicket(){
        $tickets = Tickets::model()->findAll('status = "open"');
        if(!empty($tickets)){
            foreach($tickets as $ticket){
                $user = Users::model()->findByPk($ticket->user_id);
                $priority = (!empty($user->ticket_priority)) ? $user->ticket_priority : $ticket->priority;
                $tsettings = TicketSettings::model()->findByAttributes(array('priority' => $priority, 'disabled' => 0));
                if(!empty($tsettings) && $tsettings->quantity > 1){
                    $send_time = strtotime($ticket->created_at . '+' . $tsettings->frequency * $tsettings->quantity . $tsettings->frequency_type);
                    if($send_time > strtotime('-15minutes') && $send_time < time()) {
                        $utility = new UtilityFunction();
                        $subject = 'Ticket #' . $ticket->id . ': ' . $ticket->subject . ' From: ' . $user->first_name . ' ' . $user->last_name;
                        $message = '<p> Subject: ' . $ticket->subject . '<p> Detail:' . $ticket->detail . '<p>' . 'Priority: ' .$priority . '<p>' . 'Date: ' . $ticket->created_at;
                        $utility->mail_send($tsettings->emails, $message, $subject);
                    }
                }
            }
        }
        die('end');
    }

    /*
    public function actionTest(){
        $model = Users::model()->findByPk(Yii::app()->user->getState('userid'));
        $ticketModel = Tickets::model()->findByPk(7);

        $priority = (!empty($model->ticket_priority)) ? $model->ticket_priority : $ticketModel->priority;
        $ticket_settings = TicketSettings::model()->findByAttributes(array('priority' => $priority, 'disabled' => 0));
        if(!empty($ticket_settings)){
            $utility = new UtilityFunction();
            $subject = 'Ticket: ' . $ticketModel->subject;
            $message = "<p>" .$subject . '<p> Detail:' . $ticketModel->detail . "<p>" . 'Priority: ' .$priority . "<p>" . 'Date: ' . $ticketModel->created_at;
            $utility->mail_send($ticket_settings->emails, $message, $subject);
        }
        die('end');
    }

    public function actionMail(){
        $utility = new UtilityFunction();
        echo  $utility->mail_send('test@clandestine.se', 'test', 'test');
        die('end');
    }

    2014-12-15 12:37:44
    2014-12-15 13:37:44
    2014-12-15 13:47:44


    public function actionUpdate(){ //last
        $id = 283;
        $package_id = null;
        $user = Users::model()->findByPk($id);
        //$amount = $user->wallet_amount;
        //$amount = $bitCoin->getbalance($id);

        if($user->wallet_amount > 0){
            if($package_id){
                if($package_id == 'last'){
                    $payment = Payments::model()->findLastPayment($id, true);
                    $package_id = $payment ? $payment->package_id : 1;

                }
                $package = Packages::model()->findByPk($package_id);
                $payment = new Payments;
                $payment -> user_id = $user -> id;
                $payment -> package_id = $package_id;
                $payment -> amount = $package -> cost;
                $payment -> btc_amount = $this->convert($package -> cost, '$');
            }else{
                $payment = Payments::model()->findLastPayment($id, false);
            }
            if($payment){
                $amount = $payment->btc_amount;
                $package_id = $payment -> package_id;
                $package = Packages::model()->findByPk($package_id);

                if($payment && $user->wallet_amount >= $amount){
                    //$response = $bitCoin->move($id, 'general', $amount);

                    //$next = $payment -> next_due_date;
                    $payment -> paid = 1;
                    $payment -> disabled = null;
                    $payment -> payment_date = date('Y-m-d H:i:s', time());
                    $payment_last = Payments::model()->findLastPayment($id);
                    if($payment_last && $payment_last -> next_due_date){
                        $payment -> next_due_date = date('Y-m-d  H:i:s', strtotime("+".$package->term." month", strtotime($payment_last->next_due_date)));
                    }else{
                        $payment -> next_due_date = date('Y-m-d  H:i:s', strtotime("+".$package->term." month", time()));
                    }

                    $mailbox = Mailbox::model()->findByAttributes(array('user_id' => $id));
                    $mailbox -> size = '250M';
                    $mailbox -> smtp = 1;
                    //$mailbox -> disabled = null;

                    //$user = Users::model()->findByPk($id);

                    $user->wallet_amount = $user->wallet_amount - $amount;
                    $user->premium = 1;
                    $user -> downgrade_date = null;
                    //$user->disabled = 0;

                    //Create temp mailbox record for script
                    $tempbox = new TempMailbox;
                    $tempbox -> user_id = $user -> id;
                    $tempbox -> email = $user -> email;
                    //$tempbox -> password = $user -> password;
                    $tempbox -> size = '250M';
                    $tempbox -> smtp = 1;
                    //if($next){
                    //	$tempbox -> updated = 1;
                    //}

                    if($payment->save() && $mailbox -> save() && $user->save() && $tempbox -> save()) {
                        $backend = new Backend;
                        $backend -> updateMailBox();
                        //$backend -> deleteAllMail($mailbox->email);
                        //if($next){
                        //	$backend -> updateMailBox();
                        //}else{
                        //	$backend -> createMailBox();
                        //}
                    }
                }
            }
        }
    }

	public function actionCreate(){
		$backend = new Backend;
		//$backend -> deleteAllMail($mailbox->email);
		$backend -> createMailBox();
		die('end');
	}

    public function actionBitcoin(){
        //Yii::log("Cron Job has started");
        $utility = new UtilityFunction;

        //check balance
        $bitCoin = new BitcoinrBit();
        //$baddress = $bitCoin->getnewaddress();//'general'
        //$bitCoin->setaccount($baddress, 'general');
        //$accounts = $bitCoin->listaccounts();
        $accounts = array("283" => '0.02732539');
        foreach($accounts as $account => $amount){
            if($account != 'general' && strpos($account, '"') === false){
                $id = (int)str_replace('"', '', $account);
                if($amount > 0){

                    $response = true;//$bitCoin->move($id, 'general', $amount);
                    if($response){
                        //save new wallet amount
                        $user = Users::model()->findByPk($id);
                        $bc_wallet_amount = $user->wallet_amount;

                        $address = BcAddresses::model()->findByAttributes(array('user_id' => $id), array('order' => 'created_at desc', 'limit' => 1));
                        if($address){
                            if($address->btc_amount != 0){
                                $bc_address = $address->address;
                                $bc_user_id = $address->user_id;
                                $address = new BcAddresses;
                                $address->address = $bc_address;
                                $address->user_id = $bc_user_id;
                            }
                            $address->last_balance = $utility->convert($user->wallet_amount);
                            $address->btc_last_balance = $user->wallet_amount;
                            $address->amount = $utility->convert($amount);//$address->amount + $amount
                            $address->btc_amount = $address->btc_amount + $amount;
                            $address->save();
                        }

                        $user->wallet_amount = $user->wallet_amount + $amount;
                        $user->save();


                        //save transactions
                        $last_trans = Transactions::model()->findLastTransByUser($id);
                        $transactions = $bitCoin->listtransactions($id, 0, 9999999);
                        if(!empty($transactions)){
                            if(!empty($last_trans)){
                                foreach($transactions as $key=>$tran){
                                    if($tran['txid'] == $last_trans['txid']){
                                        unset($transactions[$key]);
                                        //$transactions = array_slice($transactions, key($transactions)+1);
                                        break;
                                    }
                                    unset($transactions[$key]);
                                }
                            }
                            if(!empty($transactions)){
                                //print_r($transactions);exit;
                                foreach($transactions as $transaction){
                                    if($transaction['category'] != 'move'){
                                        $transactions = new Transactions;
                                        $transactions -> address = $transaction['address'];
                                        $transactions -> txid = $transaction['txid'];
                                        //$transactions -> term = $payment->package_id;
                                        $transactions -> user_id = $id;
                                        $transactions -> status = $transaction['category'];
                                        $transactions -> btc_amount = $transaction['amount'];
                                        $transactions -> received_amount = $transaction['amount'];
                                        $transactions -> amount = $utility->convert($transaction['amount']);
                                        $transactions -> last_balance = $utility->convert($bc_wallet_amount);
                                        $transactions -> btc_last_balance = $bc_wallet_amount;
                                        $transactions -> save();
                                    }
                                }
                            }
                        }

                        //pay payment after registration
                        $utility -> updateToPremiumPayment($id);
                        //die('end');
                    }
                }
            }
        }
        //die('end');
        //Yii::log("Cron Job has ended");
    }
	
	/*public function actionSession(){
		session_start();
		print_r($_SESSION);
		exit;
	}
	
	public function actionName(){
		echo Yii::app()->controller->id;
		//echo $this->getUniqueId();
		exit;
	}*/

	/*public function actionCount(){
		$count_tickets = Comments::model()->findCommentsCount(283);
		print_r($count_tickets);exit;
	}
	
	public function actionForward(){
		$email = 'test@clandestine.se';
		$email_forward = 'test2@clandestine.se';
		
		$backend = new Backend;
		echo $backend->deleteForwardMail($email, $email_forward);
		die('end');
	}
	public function actionTest(){	
		$tempbox = new TempMailbox;
		$tempbox -> email = 'NeoXtreme@clandestine.se';
		$tempbox -> password = '12345678';
		$tempbox -> size = '10M';
		$tempbox -> smtp = null;
		//$tempbox -> updated = 1;
		
		if($tempbox -> save()){
			$backend = new Backend;
			//$backend -> deleteAllMail($mailbox->email);
			$backend -> createMailBox();
		}
		die('end');
	}
	
	public function actionCount(){
		//$utility = new UtilityFunction;
		echo $this-> getUserCountMessages(283);
		echo '<br/>';
		die('end');
		
	}
	
	public function actionGetMail(){
		$utility = new UtilityFunction;
		if(extension_loaded("imap") && extension_loaded("openssl")){
			$hostname = '{192.168.1.2:143/novalidate-cert}INBOX';//{192.168.1.2:143/imap/ssl}INBOX
			$username = Yii::app()->user->getState('email');
			$password = $utility->decrypt(Yii::app()->user->getState('pas_token'));
			
			if($mbox = @imap_open($hostname,$username,$password,OP_READONLY)){ //or die('Cannot connect to your email account: ' . imap_last_error());
				//$emails = imap_search($inbox,'ALL');
				$emails = imap_search($mbox,'ALL');
				$headers = imap_headers($mbox);
				//$headers = array_reverse($headers);
				foreach($headers as $header){
					//preg_match('_([A-Z]+) (\d+)\)(\d+-[A-Za-z]+-\d{4}) (\S+@\S+\.\S+) (.+) \((\d+) chars\)_', $header, $matches);
					//var_dump($matches);
					//$mail = explode(' ', $head);
					echo $header;
					exit;
				}
				//$return = is_array($get_unseen) ? count($get_unseen) : 0;
				imap_close($mbox);
				print_r($headers);
				exit;
				//print_r($emails);exit;
			}else{
				$return = '';
			}
		}else{
			$return = '';
		}
		return $return;
	}
	
	// [3] => U 4)13-Nov-2014 test2@clandestine.se dfsfsd (560 chars) ) 
	*/
}


