<!--div class="security-info">
	<h1>My Account</h1>
</div-->

<?php //$this -> renderPartial('_menu', array('model' => $model)); ?>

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

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'password-form',
    'type'=>'vertical',
)); ?>

	<?php echo CHtml::label('Please Insert Security Pin to Verify', 'security-pin'); ?>
	
 	<?php echo CHtml::textField('security_pin','', array('class' => 'span4', 'required' => 'required'));?> 
	
	<?php if($model -> premium == 1 && $authEnabled): ?>
		<br /><br />
		<p><?php echo CHtml::label('Please provide six-digit code number ' . ++$nextIndex . ' from your printed two-factor card', 'confirm-message'); ?></p>

		<?php echo $form->textFieldRow($multiModel,'user_key', array('class' => 'span5' , 'maxlength' => 11)); ?>

		<?php echo $form->textFieldRow($multiModel,'login_code',array('class' => 'span5', 'maxlength' => 6)); ?>
	<?php endif; ?>
	
	<?php echo CHtml::label('Please Insert Interval of days', 'password_reminder'); ?>
	
 	<?php echo CHtml::textField('password_reminder','', array('class' => 'span4', 'type' => 'numeric', 'required' => 'required'));?> 

	<br />
	<?php $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType'=>'submit',
        'type'=>'success',
        'label'=>'Submit',
    )); ?>	

<?php $this->endWidget(); ?>
