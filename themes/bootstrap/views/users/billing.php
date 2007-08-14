<div class="security-info">
	<h1>My Account</h1>
</div>

<?php $this -> renderPartial('_menu', array('model' => $model)); ?>

<?php
	$this->widget('bootstrap.widgets.TbAlert', array(
        'block'=>true, // display a larger alert block?
        'fade'=>false, // use transitions?
        'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
        'alerts'=>array( // configurations per alert type
            'success'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
            'error'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
        ),
    )); 
?>

<?php 
if(!isset($page)) {
	$page = 0;
}
?>

<div id="payment-grid">
	<?php $this -> renderPartial('_payment_grid', array('model' => $model, 'page' => $page)); ?>
</div>

<?php if(Yii::app()->user->getState('userid') == 283) : ?>
<div class="account-balance">
	<?php if($model->premium) : ?>
		<div class="pull-left">
			<div>Account Balance: <strong class="text-green"><?php echo $wallet_amount; ?>$</strong></div>
			<div>Automatically apply funds to maintain my premium account status: <strong><a class="underline text-<?php echo ($model->auto_payment) ? 'green' : 'red';?>" href="<?php echo Yii::app()->urlManager->createUrl('/users/billing', array('id' => $model -> id, 'status' => ($model->auto_payment+1)%2)); ?>"><?php echo ($model->auto_payment) ? 'ENABLED' : 'DISABLED';?></a></strong></div>
		</div>
		<a class="btn-oblong-blue pull-right" href="<?php echo Yii::app()->urlManager->createUrl('/users/addFunds', array('id' => $model -> id)); ?>"> Add Funds</a>
		<?php /*$this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'link',
			'url' => Yii::app()->urlManager->createUrl('/users/addFunds', array('id' => $model -> id)),
			'type'=>'link',
			'size' => 'normal',
			'label'=> 'Add Funds',
			'htmlOptions' => array(
				'class' => 'btn-oblong-blue'
			),
		)); */?>
	<?php  else : ?>
        <div class="pull-right">
		    <?php echo CHtml::link(CHtml::tag('i', array('class'=>'icon-circle-arrow-up icon-white')) . CHtml::closeTag('i') . Yii::t('upgrade', 'Upgrade to Premium'),
											Yii::app()->urlManager->createUrl('users/payment/', array('id' => $model -> id)),
											array('class' => 'btn btn-warning btn-mini text-upper radius-0 text-left')); ?>
	    </div>
<?php endif; ?>
</div>
<?php endif; ?>