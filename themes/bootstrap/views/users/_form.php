<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'users-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($model); ?>

	<?php echo $form->textFieldRow($model,'first_name',array('class'=>'span5','maxlength'=>100)); ?>

	<?php echo $form->textFieldRow($model,'last_name',array('class'=>'span5','maxlength'=>100)); ?>

	<?php echo $form->textFieldRow($model,'email',array('class'=>'span5','maxlength'=>200, 'readonly' => 'readonly')); ?>

	<br />

	<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'success',
			'label'=> 'Submit',
		)); ?>
	</div>

<?php $this->endWidget(); ?>

<?php if(is_null($model -> premium)): ?>
<!--div>
	<h6 class="bg3 padding-2px text-left">Upgrade Account - </h6> 
		<div class='text-left  width-70pr margin-a '> 	 	
	 		<a class=" btn btn-warning  text-upper  radius-0px text-left" href="<?php echo Yii::app()->urlManager->createUrl('site/packages/3');?>"><i class="icon-circle-arrow-up icon-white"></i> Upgrade to premium</a> 
	 	</div>
</div-->
<?php endif;?>

