<?php
/* @var $this SiteController */
/* @var $model ContactForm */
/* @var $form TbActiveForm */

$this->pageTitle=Yii::app()->name . ' - Contact Us';
?>

<?php if(Yii::app()->user->hasFlash('contact')): ?>
    <?php $this->widget('bootstrap.widgets.TbAlert', array(
        'alerts'=>array('contact'),
    )); ?>
<?php endif; ?>
</br>
<div class="form">

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'contact-form',
    'type'=>'vertical',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
)); ?>

	<?php echo $form->errorSummary($model); ?>

    <?php echo $form->textFieldRow($model,'name', array('class' => 'span5')); ?>

    <?php echo $form->textFieldRow($model,'email', array('class' => 'span5')); ?>

    <?php echo $form->textFieldRow($model,'subject',array('size'=>60,'maxlength'=>128,'class' => 'span5')); ?>

    <?php echo $form->textAreaRow($model,'body',array('rows'=>6, 'class'=>'span5')); ?>

    <?php echo CHtml::label('Urgency', 'urgency'); ?>
    <div class="radio-buttons">
	   	<label class="checkbox inline alert alert-success">
	   		<?php echo CHtml::radioButton('urgency', '', array('value' => 'low')); ?>
	   		<?php echo CHtml::encode('Low'); ?>
	   	</label>

	   	<label class="checkbox inline alert alert-warning">
	   		<?php echo CHtml::radioButton('urgency', '', array('value' => 'medium')); ?>
	   		<?php echo CHtml::encode('Medium'); ?>
	   	</label>

	   	<label class="checkbox inline alert alert-danger">
	   		<?php echo CHtml::radioButton('urgency','', array('value' => 'low')); ?>
	   		<?php echo CHtml::encode('High'); ?>
	   	</label>
	</div>

    <?php echo $form->checkBoxRow($model, 'term_of_services'); ?>
			    	
	<?php /*if(CCaptcha::checkRequirements()): ?>
		<?php echo $form->captchaRow($model,'verifyCode',array(
            'hint'=>'Please enter the letters as they are shown in the image above.<br/>Letters are not case-sensitive.',
        )); ?>
	<?php endif;*/ ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton',array(
            'buttonType'=>'submit',
            'type'=>'success',
            'label'=>'Submit',
        )); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

