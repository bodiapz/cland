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
			<div class="text-right account-diagram"><?php echo $mailbox['used']; ?> MB of <?php echo $mailbox['quota']; ?> MB (<?php echo $usage; ?> %)</div>
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
				 	<?php echo Yii::t('free','Free'); ?>   
					<?php echo CHtml::link(CHtml::tag('i', array('class'=>'icon-circle-arrow-up icon-white')) . CHtml::closeTag('i') . Yii::t('upgrade', 'Upgrade to Premium'), 
											Yii::app()->urlManager->createUrl('users/payment/', array('id' => $model -> id)),
											array('class' => 'btn btn-warning btn-mini text-upper radius-0 text-left')); ?>
			<?php endif; ?>
		</td>
	</tr>
	<tr>
		<th class="text-right"><?php echo Yii::t('email','Email'); ?></th>
		<td class="text-left"><?php echo CHtml::encode($model -> email); ?></td>
	</tr>
	<tr>
		<th class="text-right">Two Factor Auth</th>
		<?php $mult_auth = Multifactor::model()->checkMultifactorEnabled($model ->id);?>
		<td class="text-left"><strong class="alert alert-info padding-2 radius-0 <?php echo ($mult_auth) ? 'text-green' : 'text-red'; ?>"><?php echo ($mult_auth) ? 'ENABLED' : 'DISABLED'; ?></strong></td>
	</tr>
	<tr>
		<th class="text-right">&nbsp New messages</th>
		<td class="text-left">&nbsp <span class="alert alert-info padding-2 radius-0 "><span class="label label-info radius-0"><?php echo $new_message;?></span></span></td>
	</tr>
</table> 

<div class="account-line"></div>

<div class="account-news-content">
	<?php
    if(!empty($news)){
        foreach($news as $new){
            echo '<div class="account-news-block">'.$new['description'].'</div>';
        }
    }
    ?>
</div>
<noscript>
	<style>
		.account-usage-text{margin-top: -70px;}
		.table-account{height:160px;}
	</style>
</noscript>
<style>
    .account-news-block{
        background: #516f92; /* Old browsers */
        background: -moz-linear-gradient(top,  #516f92 0%, #476280 100%); /* FF3.6+ */
        background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#516f92), color-stop(100%,#476280)); /* Chrome,Safari4+ */
        background: -webkit-linear-gradient(top,  #516f92 0%,#476280 100%); /* Chrome10+,Safari5.1+ */
        background: -o-linear-gradient(top,  #516f92 0%,#476280 100%); /* Opera 11.10+ */
        background: -ms-linear-gradient(top,  #516f92 0%,#476280 100%); /* IE10+ */
        background: linear-gradient(to bottom,  #516f92 0%,#476280 100%); /* W3C */
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#516f92', endColorstr='#476280',GradientType=0 ); /* IE6-9 */
        padding: 5px 10px 5px 10px;
        margin-bottom: 10px;
    }
    .account-news-block p{margin:5px;}
</style>
<script>
$(function() {
	$('.account-diagram').html('<input type="text" value="<?php echo $mailbox['used']; ?>" class="dial" />');
    $(".dial").knob({readOnly: true, fgColor: '#ff0000', bgColor: '#3CB7BD', width:'150', height:'150'});
});
</script>