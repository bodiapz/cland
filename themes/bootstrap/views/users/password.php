<div class="security-info">
	<h1>My Account</h1>
</div>

<!--div class='account'> 
	<ul class="nav nav-pills">
	  <li><?php echo CHtml::link('Account Info', Yii::app()->urlManager->createUrl('/users/view', array('id' => $model -> id))); ?></li>	
	  <li><?php echo CHtml::link('Edit/Upgrade Account', Yii::app()->urlManager->createUrl('/users/update', array('id' => $model -> id)), array('class' => '')); ?></li>
	  <li><?php echo CHtml::link('Change Password', Yii::app()->urlManager->createUrl('/users/password', array('id' => $model -> id)));?></li>
	  <li class="active"><?php echo CHtml::link('Change Security Info', Yii::app()->urlManager->createUrl('/users/security', array('id' => $model -> id))); ?></li>
	</ul>
</div-->

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

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'password-form',
    'type'=>'vertical',
)); ?>

	<?php echo $form->errorSummary($model); ?>

	<?php echo $form->passwordFieldRow($model,'current_password',array('class' => 'span5')); ?>

	<?php echo $form->passwordFieldRow($model,'new_password',array('class' => 'span5')); ?>

	<?php echo $form->passwordFieldRow($model,'confirm_new_password',array('class' => 'span5')); ?>
	
	<?php if($model -> premium == 1 && $authEnabled): ?>
		<br /><br />
		<p><?php echo CHtml::label('Please provide six-digit code number ' . ++$nextIndex . ' from your printed two-factor card', 'confirm-message'); ?></p>

		<?php echo $form->textFieldRow($multiModel,'user_key', array('class' => 'span5' , 'maxlength' => 11)); ?>

		<?php echo $form->textFieldRow($multiModel,'login_code',array('class' => 'span5', 'maxlength' => 6)); ?>
	<?php endif; ?>

	<br />
	<?php $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType'=>'submit',
        'type'=>'success',
        'label'=>'Submit',
    )); ?>	

<?php $this->endWidget(); ?>