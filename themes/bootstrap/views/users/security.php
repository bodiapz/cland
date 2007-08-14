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

<?php if($model -> premium == 1): ?>
	<div class="destructBtn">
	<?php if(Multifactor::model()->checkMultifactorEnabled($model ->id)): ?>
			<?php $this->widget('bootstrap.widgets.TbButton', array(
							'buttonType'=>'link',
							'url' => Yii::app()->urlManager->createUrl('/users/disableMultiFactor', array('id' => $model -> id)),
							'type'=>'danger',
							'size' => 'normal',
							'label'=> 'Disable two factor Authentication',
		)); ?>		
	<?php else: ?>
		<?php $this->widget('bootstrap.widgets.TbButton', array(
							'buttonType'=>'link',
							'url' => Yii::app()->urlManager->createUrl('/users/multifactor', array('id' => $model -> id)),
							'type'=>'info',
							'size' => 'normal',
							'label'=> 'Enable two factor Authentication',
		)); ?>
	<?php endif; ?>
	</div>
<?php endif; ?>
</br>

<?php if(false): ?>
<?php if($model -> premium == 1): ?>
	<div class="destructBtn">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
							'buttonType'=>'link',
							'url' => Yii::app()->urlManager->createUrl('/users/deleteAllMail', array('id' => $model -> id)),
							'type'=>'danger',
							'size' => 'normal',
							'label'=> 'Delete All Mail',
		)); ?>
	</div>
<?php endif; ?>
<?php endif; ?>

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'security-form',
    'type'=>'vertical',
    'htmlOptions'=>array(
        'autocomplete' => 'off',
    )
)); ?>
    <input style="display:none">
    <input type="password" style="display:none">

	<?php echo $form->errorSummary($model); ?>

    <?php if(!empty($model->security_question)) : ?>
        <div  class='alert  alert-danger'>
            <div class="padding-5">
                    <label>Please answer your current security question: </label>
                    <label class='alert bg1 color-b'><?php echo $model -> security_question; ?></label>
            </div>
            <div class="padding-5">
               <?php echo $form -> textFieldRow($model, 'current_security_answer', array('class' => 'span5'));?>
            </div>
        </div>
    <?php endif; ?>

	<div  class='alert  alert-success'>
		 <?php echo $form -> textFieldRow($model, 'new_security_question', array('class' => 'span5'));?> 

		 <?php echo $form -> textFieldRow($model, 'new_security_answer', array('class' => 'span5'));?> 
	 </div>

    <div class="alert alert-success">
        <?php echo $form->passwordFieldRow($model,'new_security_pin',array('class'=>'span5')); ?>
    </div>

	<br />
	<?php $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType'=>'submit',
        'type'=>'success',
        'label'=>'Submit',
    )); ?>
    <br />
    <br />

<?php $this->endWidget(); ?>

<!--br /><br />
<p>You can use many programs to access your Clandestine INC. account. Here's some examples: </p>

<p><img src="<?php echo Yii::app()->theme->baseUrl; ?>/images/logos.jpg"> </p-->