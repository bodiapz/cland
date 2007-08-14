<?php

?>

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'authentication-form',
    'type'=>'vertical',
)); ?>

<?php
	$this->widget('bootstrap.widgets.TbAlert', array(
        'block'=>true, // display a larger alert block?
        'fade'=>false, // use transitions?
        'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
        'alerts'=>array( // configurations per alert type
            'error'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
            'success'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
        ),
    )); 
?>

	<?php //echo $form->errorSummary($model); ?>

	<p><?php echo CHtml::label('Please provide six-digit code number ' . ++$nextIndex . ' from your printed two-factor card', 'confirm-message'); ?></p>

	<?php echo $form->textFieldRow($model,'user_key', array('class' => 'span5' , 'maxlength' => 11)); ?>

	<?php echo $form->textFieldRow($model,'login_code',array('class' => 'span5', 'maxlength' => 6)); ?>

	<br />
	<?php $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType'=>'submit',
        'type'=>'success',
        'label'=>'Authenticate',
    )); ?>
	
<?php $this->endWidget(); ?>
