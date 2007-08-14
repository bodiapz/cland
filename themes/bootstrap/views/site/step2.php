
<div id="step2" class='step2'>
	<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
		'id'=>'register-form',
		'type' => 'horizontal',
		'enableAjaxValidation'=>true,
        'enableClientValidation' => true,
        'clientOptions' => array(
            'validateOnChange' => true,
            'validateOnSubmit' => true,
        ),
	)); ?>

		<?php echo $form -> hiddenField($model, 'premium'); ?>

<!--		--><?php //echo $form->textFieldRow($model,'first_name',array('class'=>'span3','maxlength'=>100)); ?>
<!---->
<!--		--><?php //echo $form->textFieldRow($model,'last_name',array('class'=>'span3','maxlength'=>100)); ?>

		<div class="control-group ">
	   		<label class="control-label" for="Users_email"><?php echo CHtml::encode('Email Address');?></label>
	   		<div class="controls">
		        <?php echo $form->textField($model,'email',array('class'=>'span3','maxlength'=>200)); ?>
		        <?php echo CHtml::encode($model -> emailsuffix, 'emailsuffix'); ?>	
		        <?php echo $form -> error($model,'email'); ?>
		    </div>
		</div>
		
<!--		--><?php //echo $form->passwordFieldRow($model,'security_pin',array('class'=>'span3')); ?>
<!---->
<!--		--><?php //echo $form->textFieldRow($model,'security_question',array('class'=>'span3','maxlength'=>200)); ?>
<!---->
<!--		--><?php //echo $form->textFieldRow($model,'security_answer',array('class'=>'span3','maxlength'=>100)); ?>

		<?php echo $form->passwordFieldRow($model,'password',array('class'=>'span3','maxlength'=>50)); ?>
		
		<?php $model->confirm_password=''; ?>
		<?php echo $form->passwordFieldRow($model,'confirm_password',array('class'=>'span3','maxlength'=>50)); ?>

		<div class="control-group">
			<label class="control-label"><?php echo CHtml::encode('Word Verification'); ?></label>
			<div class="controls">
				<input class="span1" name="Users[verifyCode]" id="Users_verifyCode" type="text">
				<?php $this->createAction('captcha')->getVerifyCode(true); ?>
				<?php $this->widget("CCaptcha", array('buttonLabel'=>'', 'buttonOptions'=>array('class' => 'refreshBtn'), 'buttonType' => 'button', 'imageOptions' => array('class' => 'refreshImage')));?>
				 <?php echo $form -> error($model,'verifyCode'); ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="Terms_and_Conditions"></label>
			<div class="controls">
				<?php //echo $form -> checkbox($model, 'terms_and_conditions'); ?>
				<?php echo CHtml::encode("By clicking 'Submit', you agree to the Terms and Privacy Policy."); ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="Submit_Button"></label>
			<div class="controls">
				<?php /*$this->widget('bootstrap.widgets.TbButton', array(
					'buttonType'=>'link',
					'type'=>'normal',
					'label'=> 'Back',
					'size' => 'large',
					'url' => Yii::app()->urlManager->createUrl('/site/register'),
					'htmlOptions' => array(
						'id' => 'backBtn',					
					)
				));*/ ?>
				<?php $this->widget('bootstrap.widgets.TbButton', array(
					'buttonType'=>'submit',
					'type'=>'success',
					'size' => 'large',
					'label'=> 'Submit',
				)); ?>
			</div>
		</div>

	<?php $this->endWidget(); ?>
</div>
<br/>
<br/>
