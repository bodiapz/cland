<?php
	if($mailbox['used'] > 0 && $mailbox['quota'] > 0) {
		$usage = 100 - ($mailbox['used'] / $mailbox['quota']) * 100;
	} else {
		$usage = 0;
		$mailbox['used'] = 0;
	}
?>

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
</br>
<table class="table-account">
	<tr>
		<th class="text-right">Account Type</th>
		<td class="text-left"><?php echo ($model->premium == 1) ? 'Premium' : 'Free'; ?></td>
		<td rowspan="5">
			<div class="text-left margin-l-10 account-usage-text"><?php echo Yii::t('usage', 'Account Usage'); ?></div>
			<div class="text-right account-diagram"><input type="text" value="<?php echo $mailbox['used']; ?>" class="dial" /></div>
		</td>
	</tr>
	<tr>
		<th class="text-right">Billing</th>
		<td class="test-left">
			<?php if($model->premium == 1): ?>
				<?php if(isset($payment->paid) && $payment->paid): ?> 
					<span class="alert alert-info padding-2 radius-0">Payment status: Valid until: <span class="label label-info margin-l-10 radius-0"><?php echo date('d/m/y', strtotime($payment->next_due_date)); ?></span></span>
				<?php else : ?>
					<span class='alert alert-danger padding-2 radius-0'>Payment Status : <span class="label label-important radius-0">Pending</span></span>
				<?php endif; ?>
			<?php else: ?>
				 	<?php //echo Yii::t('free','Free'); ?>   
					<?php //echo CHtml::link(CHtml::tag('i', array('class'=>'icon-circle-arrow-up icon-white')) . CHtml::closeTag('i') . Yii::t('upgrade', 'Upgrade to Premium'), 
											//Yii::app()->urlManager->createUrl('site/packages/3'), 
											//array('class' => 'btn btn-warning btn-mini text-upper radius-0 text-left')); ?>
			<?php endif; ?>
		</td>
	</tr>
	<tr>
		<th class="text-right"><?php echo Yii::t('email','Email'); ?></th>
		<td class="text-left"><?php echo CHtml::encode($model -> email); ?></td>
	</tr>
	<tr>
		<th class="text-right">Two Factor Auth</th>
		<td class="text-left"><strong class="alert alert-info padding-2 radius-0 <?php echo (Multifactor::model()->checkMultifactorEnabled($model ->id)) ? 'text-green' : 'text-red'; ?>">ENABLED</strong></td>
	</tr>
	<tr>
		<th class="text-right">New messages</th>
		<td class="text-left"><span class="alert alert-info padding-2 radius-0 "><span class="label label-info radius-0"><?php echo 0;?></span></span></td>
	</tr>
</table> 

<div class="account-line"></div>

<div class="account-news-content">
	<?php echo (isset($content)) ? $content : ""; ?>
</div>
<script>
$(function() {
    $(".dial").knob({readOnly: true, fgColor: '#ff0000', bgColor: '#3CB7BD', width:'150', height:'150'});
});
</script>