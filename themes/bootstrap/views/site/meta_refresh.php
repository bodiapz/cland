<head>
<meta http-equiv="refresh" content="20">
</head> 

<?php
	$filename = '/var/www/clandestine.se/assets/token/'.Yii::app()->user->getState('token');
	if (file_exists($filename) && filectime($filename)+120 > time()) {
		touch('/var/www/clandestine.se/assets/token/'.Yii::app()->user->getState('token'), time()); 
	}else{
		unlink($filename);
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
?>