<?php /* @var $this Controller */ ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />   

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
    <link href="<?php echo Yii::app()->theme->baseUrl; ?>/images/favicon.ico" rel="shortcut icon" type="image/x-icon" />
	<?php Yii::app()->bootstrap->register(); ?>

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/styles.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/flags.css" />
	<script src="<?php echo Yii::app()->theme->baseUrl; ?>/js/clandestine.js" type="text/javascript"></script>
	<script src="<?php echo Yii::app()->theme->baseUrl;?>/js/jQueryKnob/jquery.knob.min.js" type="text/javascript"></script>
</head>

<body>

<div id="wrapper">
	<div class="header padding-10">
		<div class="container">
            <?php if(!Yii::app()->user->isGuest): ?>
			    <a class="header-left logo" href="/users/<?php echo Yii::app()->user->getState('userid');?>">clandestine<span>INC.</span></a>
			<?php else : ?>
                <a class="header-left logo" href="/">clandestine<span>INC.</span></a>
            <?php endif;?>
            <style>
                .logo{
                    color: white;
                    font-size: 20px;
                    height: 50px;
                    line-height: 50px;
                    padding-left: 100px;
                    background-image: url("/themes/bootstrap/images/logo.png");
                    background-size: 100px;
                    background-repeat: no-repeat;
                    text-decoration:none;
                }
                .logo span{
                    color: #56A5A9;
                    font-size: 17px;
                    font-weight: 900;
                }
                .logo:hover{
                    color: white;
                    text-decoration:none;
                }
            </style>
			<div class="header-right">
				<ul>
					<?php if(!Yii::app()->user->isGuest): ?>
							<?php 
								$filename = '/var/www/clandestine.se/assets/token/'.Yii::app()->user->getState('token');
								if (file_exists($filename) && filectime($filename)+120 > time()) {
									touch('/var/www/clandestine.se/assets/token/'.Yii::app()->user->getState('token'), time()); 
								}else{
									if(file_exists($filename)) unlink($filename);
									Yii::app()->user->logout();
									$this->redirect(Yii::app()->homeUrl);
								}
							?>

                            <li><?php echo CHtml::encode('Welcome ');?><?php echo Yii::app()->user->getState('name'); ?></li>
                            <?php if(Yii::app()->user->hasState('logout')) :
                                Yii::app()->user->setState('logout', null);
                                ?>
                                <li>
                                    You have <span id="timer_logout_time"></span> till <?php echo CHtml::link('Logout', Yii::app()->urlManager->createUrl('/site/logout'), array('class' => 'underline'));?> | <?php echo CHtml::link('Cancel', Yii::app()->urlManager->createUrl('/site/logoutTimer/0'), array('class' => 'underline'));?>
                                </li>
                            <?php else : ?>
                                <li><?php echo CHtml::link('Logout', Yii::app()->urlManager->createUrl('/site/logout'));?></li>
                            <?php endif; ?>
							<li><?php echo CHtml::link('Webmail','/webmail/?token='.Yii::app()->user->getState('token').'&js='.Yii::app()->user->getState('js')); ?></li>

					<?php else: ?>
						<li>
							<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
								'id'=>'layout-login-form',
								'enableAjaxValidation'=>false,
								'action' => Yii::app()->request->getBaseUrl(true) . Yii::app()->urlManager->createUrl('/site/LayoutLogin') //'https://clandestine.se/webmail/index.php' //
							)); ?>

							<?php echo CHtml::textField('email', '', array('placeholder' => 'Email Address', 'class' => 'input-1 input-large placeholder-color')); ?>
							<?php echo CHtml::passwordField('password', '', array('placeholder' => 'Password', 'class' => 'input-1 input-large placeholder-color')); ?>
							<noscript>
								<?php echo CHtml::hiddenField('jsoff', true); ?>
							</noscript>
							
							<?php echo CHtml::submitButton('', array('class' => 'btn-go')); ?>

							<?php $this ->endWidget() ;?>
								<div class="forgot-password"><?php echo CHtml::link('Forgot Password', Yii::app()->urlManager->createUrl('/site/forgotPassword'));?></div>
						</li>

					<?php endif; ?>
				</ul>
			</div>

			<div class="clear"></div>
		</div>
	</div>

	<div class="content">
		<div class="container" id="page">
			<div class="nav-left padding-30">
				<?php $this->widget('bootstrap.widgets.TbMenu',array(
							'type' => 'pills',
							'stacked' => 'true',
				            'items'=>array(
								array('label'=>'My Account','icon'=>'chevron-right','url'=>array('/users/view','id' => Yii::app()->user->getState('userid')), 'visible' => !Yii::app()->user->isGuest, 'active'=>($this->getUniqueId() == 'users' || $this->getUniqueId() == 'tickets'), 'itemOptions' => array('class'=>'my_acount_tab')),
				                array('label'=>'What we are', 'icon'=>'chevron-right', 'url'=>array('/site/index')),
				                array('label'=>'Register Now', 'icon'=>'chevron-right','url'=>array('/site/register'), 'visible' => Yii::app()->user->isGuest),
				               /* array('label'=>'My Account', 'icon'=>'chevron-right','url'=>array('/site/login'), 'visible' => Yii::app()->user->isGuest),*/
				                /*array('label'=>'Tickets', 'icon'=>'chevron-right','url'=>array('/tickets'), 'visible' => !Yii::app()->user->isGuest),*/
				                array('label'=>'Why Us?', 'icon'=>'chevron-right','url'=>array('/site/whyus')),
				                array('label'=>'TOS', 'icon'=>'chevron-right', 'url'=>array('/site/tos')),
				                array('label'=>'Privacy Policy', 'icon'=>'chevron-right', 'url'=>array('/site/privacy')),
				                array('label'=>'Contact us', 'icon'=>'chevron-right', 'url'=>array('/site/contact')),
				            )
			          )); ?>
                <a class="nav-lang <?php if(Yii::app()->controller->action->id == 'language') echo 'active'; ?>" href="<?php echo Yii::app()->urlManager->createUrl('/site/language'); ?>"><div class="flag flag-fi"></div> Language Selection</a>

			    <?php echo CHtml::image(Yii::app()->theme->baseUrl.'/images/bitcoin.jpg', 'bitcoin'); ?>
			</div>

			<div class="content-wrapper">
				<div class="content-right">
					<?php echo $content; ?>
				</div>
			</div>

			<div class="clear"></div>

		</div><!-- page -->
	</div><!--content-->

	<div class="footer-wrapper">
		<div id="footer"></div><!-- footer -->
	</div>
</div><!--wrapper-->

<noscript style="position:fixed">
	<iframe src="<?php echo Yii::app()->urlManager->createUrl('/site/metaRefresh');?>" width="0" height="0"><iframe>
</noscript>

<script type="text/javascript">
    var mmins = 1;
    var msecs = mmins * 60;
    var mcurrentSeconds = 0;
    var mcurrentMinutes = 0;

    function mDecrement() {
        mcurrentMinutes = Math.floor(msecs / 60);
        mcurrentSeconds = msecs % 60;
        if (mcurrentSeconds <= 9)
            mcurrentSeconds = "0" + mcurrentSeconds;
        msecs--;
        jQuery('#timer_logout_time').html(mcurrentMinutes + ':' + mcurrentSeconds);

        if (msecs !== -1) {
            setTimeout('mDecrement()', 1000);
        } else {
            window.location.href = "<?php echo Yii::app()->urlManager->createUrl('/site/logout'); ?>";
        }
    }

    $(document).ready(function() {
		if($('#timer_logout_time').length){
			setTimeout('mDecrement()', 1000);
		}
    })
</script>
<script type="text/javascript">
$(document).ready(function(){
	setInterval(function() {
		$.ajax({  
			url: "<?php echo Yii::app()->homeUrl; ?>",
			type: "POST",
			async: true,
			success:function(){}
		});
	}, 20000);
})
</script>
</body>
</html>
