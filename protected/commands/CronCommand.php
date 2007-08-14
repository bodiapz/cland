<?php
//    */5 * * * * /usr/bin/php /var/www/clandestine.se/protected/yiic.php cron bitcoin
class CronCommand extends CConsoleCommand
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
    }

    public function actionSendTickets(){
        //$fp = @fsockopen('clandestine.se', 80, $errno, $errstr, 2);
        //if(!$fp){
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
        //}
    }

	public function actionBitcoin(){
		//Yii::log("Cron Job has started");  
		$utility = new UtilityFunction;

		//check balance
		$bitCoin = new BitcoinrBit();
		//$baddress = $bitCoin->getnewaddress();//'general'
		//$bitCoin->setaccount($baddress, 'general');
		$accounts = $bitCoin->listaccounts();
		//$accounts = array("283" => '0.02668588');
		foreach($accounts as $account => $amount){
			if($account != 'general' && strpos($account, '"') === false){
				$id = (int)str_replace('"', '', $account);
				if($amount > 0){
						
					$response = $bitCoin->move($id, 'general', $amount);
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

	public function actionAutoPayment(){
		$users = Users::model()->findAll('premium = 1 AND auto_payment =1');
		foreach($users as $us){
			$premium_due_date = Payments::model()->findDueDateByUser($us['id']);
			if($premium_due_date && strtotime($premium_due_date) >= strtotime('now') && strtotime($premium_due_date) <= strtotime('+1 day')) {
				$utility = new UtilityFunction;
				$utility -> updateToPremiumPayment($us['id'], 'last');
			}
		}
	}
	
	public function actionDeleteInactiveAccount(){
		$users = Users::model()->findAll('premium is null');
		foreach($users as $us){
			Users::model()->updateLastLoginFromHistory($us['id']);
		}
		$users = Users::model()->findUsersAfterLoginDate();
		foreach($users as $user){
			$backend = new Backend;
			$backend -> deleteAllMail($user->email);
			
			$mailbox = Mailbox::model()->findByAttributes(array('email' => $user -> email));
			$delUsers = new DelUsers;
			$data = $user->attributes;
			$data['user_id'] = $data['id'];
			$data['box_size'] = $mailbox->size;
			$data['delete_date'] = date('Y-m-d H:i:s', time());
			unset($data['id']);
			$delUsers->attributes = $data;
			
			if($delUsers->save()){
				Users::model()->deleteAll('id=:id',array(':id'=>$user->id));
				Mailbox::model()->deleteAll('user_id=:user_id',array(':user_id'=>$user->id));
				//Users::model()->updateAll(array('disabled' => 1), 'id=:id', array('id'=>$user->id));
				//Mailbox::model()->updateAll(array('disabled' => 1), 'user_id=:user_id', array('user_id'=>$user->id));
				TempMailbox::model()->deleteAll('email=:email',array(':email'=>$user->email));
			}
		}
	}
	
	public function actionPremiumToFreeAccount(){
		$users = Users::model()->findAll('premium = 1');
		foreach($users as $us){
			$premium_due_date = Payments::model()->findDueDateByUser($us['id']);
			if($premium_due_date &&  $premium_due_date >= date('Y-m-d', time())){
				continue;
			}
			$user = Users::model()->findByPk($us['id']);
			if(!empty($user)) {

				$id = $user->id;
				$user -> premium = null;
				$user -> password_reminder = 0;
				$user -> downgrade_date = date('Y-m-d H:i:s', time());
				if($user -> save()) {
                    $backend = new Backend;
					$mailbox = Mailbox::model()->findByAttributes(array('email' => $user -> email));
					if(!empty($mailbox)) {
                        $backend = new Backend;
                        $usage = $backend -> getMailBoxUsage($user -> email);
                        if(!empty($usage['used'])){
                            $mailbox -> size = ($usage['used'] < 10 ) ? '10M' : ceil($usage['used']).'M';
                        }
						$mailbox -> smtp = null;
						if($mailbox -> save()) {
							if(Multifactor::model()->isEnabled($id)) {
								Multifactor::model()->disableAuthentication($id);
							}
							$tempbox = new TempMailbox;
							$tempbox -> email = $user -> email;			
							$tempbox -> size = $mailbox -> size;//'10M';
							$tempbox -> smtp = null;
							$tempbox -> updated = 1;
							$tempbox -> save();
							
							$payment = Payments::model()->updateAll(array('disabled' => 1), 'user_id=:user_id', array('user_id'=>$id));

							$backend = new Backend;
							$backend -> updateMailBox();
						}
					}
				}
			}
		}
	}
}