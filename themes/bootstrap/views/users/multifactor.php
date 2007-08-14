
<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'multifactor-form',
)); ?>

<?php $url = Yii::app()->urlManager->createUrl('/users/papercode', array('id' => $id)); ?>

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

<h4><?php echo CHtml::encode('Paper codes based two-factor Authentication'); ?></h4>

<ol>
	<li><a href="#" onClick="window.open('<?php echo $url; ?>','papercode','height=500,width=650'); return false;">Click here to print</a> your two-factor paper codes.</li>
	<li>Proceed to activation (above), input the paper key from the top of the paper and the first code.</li>
	<li>After all 90 paper codes have been consumed, you'll need to re-enable paper code-based two-factor authentication.</li>
	<li>Mark used codes on the paper.</li>
</ol>

<?php $this->widget('bootstrap.widgets.TbButton', array(
							'buttonType'=>'button',							
							'size' => 'normal',
							'label'=> 'Proceed to Activation',
							'htmlOptions' => array(
								'id' => 'proceed'
							)
		)); ?>

<div id="proceed-form">
	<?php echo CHtml::label('Please confirm enabling two-factor authentication by providing the six-digit code number 1 from your printed two-factor card', 'confirm-message'); ?>
	<?php echo $form -> textFieldRow($model, 'user_key', array('maxlength' => 11)); ?>
	<?php echo $form -> textFieldRow($model, 'code', array('maxlength' => 6)); ?>

	<div class='controls'>
		<?php echo CHtml::checkbox('print'); ?>
		<?php echo CHtml::encode('I have printed my two-factor authentication codes on paper '); ?>
	</div>

	<div class="controls">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
							'buttonType'=>'submit',
							'type' => 'success',							
							'size' => 'normal',
							'label'=> 'Enable two factor Authentication',
		)); ?>
	</div>
</div>

<?php $this -> endWidget(); ?>