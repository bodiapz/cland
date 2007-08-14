
<form action="" method="POST"><input name="email" type="email" placeholder="E-mail"><input type="submit"  value="Send" ></form>

<?php
ini_set('display_errors', 1);

if($_SERVER['REQUEST_METHOD'] === 'POST') {

	

	require_once('class.phpmailer.php');
        require_once('class.smtp.php');
        $mail = new PHPMailer();
        $mail->isSMTP();
		$mail->SMTPAuth = "PLAIN";
        $mail->SMTPDebug = 2;
        $mail->Debugoutput = 'html';
        $mail->Host = 'mail.clandestine.se';
        $mail->Port = 465;
        $mail->SMTPSecure = 'ssl';
        $mail->SMTPAuth = true;
        $mail->Username = "test@clandestine.se";
        $mail->Password = 'testtest';
        
		
		
		/*
		$mail->startTLS(); 
		$mail->hello('smtp.gmail.com'); 
		if($mail->authenticate("oleg.fedoliak", "natalieka2003", 'PLAIN')) echo "yes"; else echo "no";*/
	
        $mail->setFrom('test@clandestine.se', 'test');
//        $mail->addAddress("natalieka2003@rambler.ru");
        $mail->addAddress("test3@clandestine.se");
        $mail->Subject = "TEST";
        $mail->msgHTML("ssss");

        if (!$mail->send()) {
            echo "Mailer Error: " . $mail->ErrorInfo;
        } else {
            echo "Message sent!";
        }
		/*
        
		ini_set('display_errors',1);
		error_reporting(E_ALL);*/
}
