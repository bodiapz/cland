<div class="security-info">
	<h1>Forgot password</h1>
</div>

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

	<?php echo $form->errorSummary($model); ?>
	
	<?php if($step == 1) : ?>
		<div  class='alert alert-success'> 
			<div class="padding-5"> 
				<label>Please enter your Email Address: </label>
			</div>
			<div class="padding-5">
			   <?php echo $form -> textField($model, 'email', array('class' => 'span5', 'type'=>'email', 'required' => 'required'));?> 
			</div>  
		</div> 
	<?php elseif($step == 2) : ?>
		
		
		<div  class='alert  alert-danger'> 
			<div class="padding-5"> 
				<?php echo CHtml::label('Please Insert Security Pin to Verify', 'security-pin'); ?>
			</div>
			<div class="padding-5">  
			   <?php echo $form -> textFieldRow($model, 'current_security_pin', array('class' => 'span5', 'required' => 'required'));?> 
			</div>  
		</div>
	
		<div  class='alert  alert-danger'> 
			<div class="padding-5"> 
				<label>Please answer your current security question: </label>
				<label class='alert bg1 color-b'><?php echo $model -> security_question; ?></label>
			</div>
			<div class="padding-5">  
			   <?php echo $form -> textFieldRow($model, 'current_security_answer', array('class' => 'span5', 'required' => 'required'));?> 
			</div>  
		</div> 
		
		<?php if($model -> premium == 1 && $authEnabled): ?>
			<div  class='alert  alert-danger'> 
				<div class="padding-5"> 
					<?php echo CHtml::label('Please provide six-digit code number ' . ++$nextIndex . ' from your printed two-factor card', 'confirm-message'); ?></p>
					<?php echo $form->textFieldRow($multiModel,'user_key', array('class' => 'span5' , 'maxlength' => 11, 'required' => 'required')); ?>
				</div>
				<div class="padding-5">  
				  <?php echo $form->textFieldRow($multiModel,'login_code',array('class' => 'span5', 'maxlength' => 6, 'required' => 'required')); ?>
				</div>  
			</div>
		<?php endif; ?>
		
		<?php //echo $form->passwordFieldRow($model,'current_password',array('class' => 'span5')); ?>
		
		<div  class='alert  alert-success'> 
			<div class="padding-5"> 
				<?php echo $form->passwordFieldRow($model,'new_password',array('class' => 'span5', 'required' => 'required')); ?>
			</div>
			<div class="padding-5">  
				<?php echo $form->passwordFieldRow($model,'confirm_new_password',array('class' => 'span5', 'required' => 'required')); ?>
			</div>  
		</div> 
		
		
	<?php endif; ?>
	
	<br />
	<?php $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType'=>'submit',
        'type'=>'success',
        'label'=>'Submit',
    )); ?>	

<?php $this->endWidget(); ?>

<br /><br />
<p>You can use many programs to access your Clandestine INC. account. Here's some examples: </p>

<p><img src="<?php echo Yii::app()->theme->baseUrl; ?>/images/logos.jpg"> </p>