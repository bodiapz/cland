<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'tickets-form',
	'enableAjaxValidation'=>false,
)); //print_r($form);die();?>

	<?php echo $form->errorSummary($ticketModel); ?>
	
	<?php echo $form->textFieldRow($ticketModel,'subject',array('class'=>'span7','maxlength'=>255)); ?>

	<?php echo $form->textAreaRow($ticketModel,'detail',array('rows'=>6, 'cols'=>50, 'class'=>'span7')); ?>

	<?php echo $form->radioButtonList($ticketModel,'priority',array('low'=>'Low','medium'=>'Medium','high'=>'High')); ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'success',
			'label'=>$ticketModel->isNewRecord ? 'Create' : 'Save',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
