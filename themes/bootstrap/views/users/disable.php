<?php

?>

<?php $url = Yii::app()->urlManager->createUrl('/users/papercode', array('id' => $id)); ?>

<?php
	$this->widget('bootstrap.widgets.TbAlert', array(
        'block'=>true, // display a larger alert block?
        'fade'=>false, // use transitions?
        'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
        'alerts'=>array( // configurations per alert type
            'danger'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
            'error'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
        ),
    )); 
?>

<?php if(!is_null($nextIndex)): ?>
	<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
		'id'=>'multifactor-form',
	)); ?>

	<a href="#" onClick="window.open('<?php echo $url; ?>','papercode','height=500,width=650'); return false;" class="print-link">Print Next set of Codes</a>
	<?php echo CHtml::label('Please confirm disabling two-factor authentication by providing the six-digit code number ' . ($nextIndex + 1) . ' from your printed two-factor card', 'confirm-message'); ?>

	<?php echo $form -> textFieldRow($model, 'user_key', array('maxlength' => 11)); ?>
	<?php echo $form -> textFieldRow($model, 'next_code', array('maxlength' => 6)); ?>

		<div class="controls">
			<?php $this->widget('bootstrap.widgets.TbButton', array(
								'buttonType'=>'submit',
								'type' => 'danger',							
								'size' => 'normal',
								'label'=> 'Disable two factor Authentication',
			)); ?>
		</div>
	</div>

	<?php $this -> endWidget(); ?>
<?php endif; ?>