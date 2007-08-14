<?php
/** 
 *
 * Class for utility functions
 *
 */
class UtilityFunction {

	/**
	 *
	 * Get the Current Bitcoin Rate
	 * @return float $price
	 * 
	 */
	public function getCurrentBitRate(){
		ini_set('max_execution_time', 0);

		$content = @file_get_contents('https://www.bitstamp.net/api/ticker/'); 
	    
	    if($content) {
	        $price =  (array)json_decode($content);
	    } else {
	        $price = array('last' => 180);
	    }
	        
	    return $price;
	}
	
	function convert($conv_amount, $from = 'btc'){
		$currentRate = $this->getCurrentBitRate();
		$one_dollar_bit = 1;
		$doller_amount = $currentRate['last'];
		$amount = 0;
		if($from == 'btc'){
			//bitcoin to dollar conversion
			$amount = round($doller_amount * $conv_amount, 2);
		}else{
			//dollar to bitcoin conversion
			$amount = round($one_dollar_bit/$doller_amount * $conv_amount, 8);
		}
		
    return $amount;
}

	/**
	 * Split for display purposes
	 * @param Array $hotp
	 * @return Array $split
	 */
	public function splitInTen($hotp) {
		$j = 0;
		$buffer = array();
		$split = array();

		foreach($hotp as $value) {
			array_push($buffer, $value);

			if($j++ == 8) {
				$j = 0;
				
				array_push($split, $buffer);
				
				unset($buffer);
				$buffer = array();
			} 
		}

		return $split;
	}
	
	public function curl($url, $ssl = true){
		ob_start();
		$ch = curl_init($url);
		if($ssl){
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		}
		curl_exec($ch);
		curl_close($ch);
		ob_end_clean();
	}
	
	public function touch($email){
		$token = Yii::app()->user->getState('token');
		touch('/var/www/clandestine.se/assets/token/'.$token, time());
		//touch('/var/www/clandestine.se/assets/online/'.strtolower($email), time());
	}
	
	public function fileToken($email, $password, $generateToken = true){
		if($generateToken){
			$token = $this->generateToken();
			Yii::app()->user->setState('token', $token);
			Yii::app()->user->setState('email',$email);

            $model = Users::model()->findByAttributes(array('email' => $email));
            if($model->language != null){
                Yii::app()->user->setState('language', $model -> language);
            }elseif(Yii::app()->user->getState('language')){
                $model -> language = Yii::app()->user->getState('language');
                $model -> save();
            }

		}else{
			$token = Yii::app()->user->getState('token');
		}

        Yii::app()->user->setState('pas_token',$this->encrypt($password));

		$file_name = '/var/www/clandestine.se/assets/token/'.$token;
		$file = fopen($file_name, "w");
		//file_put_contents($file_name, $model -> encrypt($user->email));
		file_put_contents($file_name, $this->encrypt($email) . PHP_EOL . $this->encrypt($password));
		fclose($file);
	}
	
	public function generateToken(){
		return $this->generateRandomString();
	}
    public function generateInviteToken(){
		return $this->generateRandomString(6);
	}
	
	public function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
	}
	
	public function encrypt($clear, $key = 'des_key', $base64 = true)
    {
		$crypto_key = 'zb3aSMIlJ3UWPPHxtvrIa4X0';
        if (!$clear) {
            return '';
        }
        $clear = pack("a*H2", $clear, "80");

        if (function_exists('mcrypt_module_open') &&
            ($td = mcrypt_module_open(MCRYPT_TripleDES, "", MCRYPT_MODE_CBC, ""))
        ) {
            $iv = $this->create_iv(mcrypt_enc_get_iv_size($td));
            mcrypt_generic_init($td, $crypto_key, $iv);
            $cipher = $iv . mcrypt_generic($td, $clear);
			mcrypt_generic_deinit($td);
			mcrypt_module_close($td);
        }
        else {
            if (function_exists('des')) {
                $des_iv_size = 8;
                $iv = $this->create_iv($des_iv_size);
                $cipher = $iv . des($crypto_key, $clear, 1, 1, $iv);
            }
        }
        return $base64 ? base64_encode($cipher) : $cipher;
    }
	
	public function decrypt($cipher, $key = 'des_key', $base64 = true)
    {
		$crypto_key = 'zb3aSMIlJ3UWPPHxtvrIa4X0';
        if (!$cipher) {
            return '';
        }

        $cipher = $base64 ? base64_decode($cipher) : $cipher;

        if (function_exists('mcrypt_module_open') &&
            ($td = mcrypt_module_open(MCRYPT_TripleDES, "", MCRYPT_MODE_CBC, ""))
        ) {
            $iv_size = mcrypt_enc_get_iv_size($td);
            $iv = substr($cipher, 0, $iv_size);

            // session corruption? (#1485970)
            if (strlen($iv) < $iv_size) {
                return '';
            }

            $cipher = substr($cipher, $iv_size);
            mcrypt_generic_init($td, $crypto_key, $iv);
            $clear = mdecrypt_generic($td, $cipher);
            mcrypt_generic_deinit($td);
            mcrypt_module_close($td);
        }
        else {
            // @include_once 'des.inc'; (not shipped with this distribution)

            if (function_exists('des')) {
                $des_iv_size = 8;
                $iv = substr($cipher, 0, $des_iv_size);
                $cipher = substr($cipher, $des_iv_size);
                $clear = des($crypto_key, $cipher, 0, 1, $iv);
            }
        }
		
        $clear = substr(rtrim($clear, "\0"), 0, -1);

        return $clear;
    }
	
	private function create_iv($size)
    {
        // mcrypt_create_iv() can be slow when system lacks entrophy
        // we'll generate IV vector manually
        $iv = '';
        for ($i = 0; $i < $size; $i++) {
            $iv .= chr(mt_rand(0, 255));
        }
        return $iv;
    }
	
	public function getUserCountMessages(){
		$return = '';
		if(extension_loaded("imap") && extension_loaded("openssl")){
			$hostname = '{192.168.1.2:143/novalidate-cert}INBOX';//{192.168.1.2:143/imap/ssl}INBOX
			$username = Yii::app()->user->getState('email');
			$password = $this->decrypt(Yii::app()->user->getState('pas_token'));

			//$inbox = @imap_open($hostname,$username,$password,OP_READONLY);
			
			if($inbox = @imap_open($hostname,$username,$password,OP_READONLY)){// or die('Cannot connect to your email account: ' . imap_last_error());
				//$emails = imap_search($inbox,'ALL');
				$get_unseen = imap_search($inbox,'UNSEEN');
				//echo print_r($emails);
				$return = is_array($get_unseen) ? count($get_unseen) : 0;
				
				imap_close($inbox);
			}
		}
		return $return;
	}
	
	public function updateToPremiumPayment($id, $package_id = null){ //last
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

                        if(!empty($user->inviter_id)){
                            $invite = Invites::model()->findByAttributes(array('user_id' => $user->id));
                            if(!empty($invite)){
                                $invite->status = 2;
                                $invite->save();

                                $inviter_id = $user->inviter_id;
                                $user = Users::model()->findByPk($inviter_id);
                                if(!empty($user)) {
                                    $user->premium = 1;
                                    $user->downgrade_date = null;
                                    if ($user->save()) {
                                        $mailbox = Mailbox::model()->findByAttributes(array('user_id' => $inviter_id));
                                        if (!empty($mailbox)) {
                                            if($mailbox->size != '250M'){
                                                $mailbox->size = '250M';
                                            }
                                            $mailbox->smtp = 1;
                                            if ($mailbox->save()) {
                                                $tempbox = new TempMailbox;
                                                $tempbox->user_id = $user->id;
                                                $tempbox->email = $user->email;
                                                $tempbox->size = $mailbox->size;
                                                $tempbox->smtp = 1;
                                                $tempbox->updated = 1;
                                                if($tempbox->save()) {
                                                    //payment table save
                                                    $payment = new Payments;
                                                    $payment -> user_id = $inviter_id;
                                                    $payment -> package_id = 0;
                                                    $payment -> paid = 1;
                                                    $payment -> disabled = null;
                                                    $payment -> payment_date = date('Y-m-d H:i:s', time());
                                                    $payment_last = Payments::model()->findLastPayment($inviter_id);
                                                    if($payment_last && $payment_last -> next_due_date){
                                                        $payment -> next_due_date = date('Y-m-d  H:i:s', strtotime("+ 1 month", strtotime($payment_last->next_due_date)));
                                                    }else{
                                                        $payment -> next_due_date = date('Y-m-d  H:i:s', strtotime("+ 1 month", time()));
                                                    }
                                                    if ($payment->save()) {
                                                        $backend = new Backend;
                                                        $backend->updateMailBox();
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
					}
				}
			}
		}
	}

    public function password($password){
        return str_replace("'", "", $password);
    }

    public  function mail_send($email, $message = '', $subject = ''){
        $info = SmtpSettings::model()->findByPk(1);

        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPAuth = "PLAIN";
        $mail->SMTPDebug = 0;
        $mail->Debugoutput = 'html';
        $mail->Host = $info['host'];
        $mail->Port = $info['port'];
        if($info['security']){
            $mail->SMTPSecure = $info['security'];
        }
        $mail->SMTPAuth = true;
        $mail->Username = $info['username'];
        $mail->Password = $info['password'];
        $mail->setFrom($info['username'], 'test');
        //$mail->From = $info['username'];
        //$mail->FromName = 'Pre-login';
        $mail->addAddress($email);
        $mail->Subject = $subject;
        $mail->msgHTML($message);//'This is test email'

       /*if (!$mail->send()) {
            return "Mailer Error: " . $mail->ErrorInfo;
        } else {
            return "Message sent!";
        }*/
        return $mail->send();
        //die('end');
    }
}

?>