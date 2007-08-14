<?php
/**
 * @author _uJJwAL_
 * @copyright 2014
 * 
 * Send mail for Clandestine system
 */

class ClandestineMail {

	private $name;
	private $from;
	private $to;
	private $subject;
	private $body;

	/**
	 * Send Mail using php mail() function 
	 */
	private function sendMail() {
		$headers="From: $this -> name <{$this -> from}>\r\n".
			"Reply-To: {$this -> to}\r\n".
			"MIME-Version: 1.0\r\n".
			"Content-Type: text/plain; charset=UTF-8";

		@mail($this -> to,$this -> subject,$this -> body, $headers);
	}

	public function contactMail($model, $urgency) {
		$this -> name = '=?UTF-8?B?'.base64_encode($model->name).'?=';
		$this -> subject ='=?UTF-8?B?'.base64_encode($model->subject).' - '. $urgency.'?=';
		$this -> from = $model -> email;
		$this -> to = Yii::app()->params['adminEmail'];
		$this -> body = $model -> body;

		$this -> sendMail();
	}

	public function premiumUserMailOnSignup() {

	}

	public function freeUserMailOnSignup(){

	}
}

?>