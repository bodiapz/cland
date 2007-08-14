
<?php //$this -> renderPartial('_menu', array('model' => $model)); ?>

<?php
	$this->widget('bootstrap.widgets.TbAlert', array(
        'block'=>true, // display a larger alert block?
        'fade'=>false, // use transitions?
        'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
        'alerts'=>array( // configurations per alert type            
            'warning'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
            'error'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
            'success'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
        ),
    )); 
?>

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'delete-mail-form',
    'type'=>'vertical',
)); ?>

	<?php echo CHtml::label('Please Insert Security Pin to Verify', 'security-pin'); ?>
 	<?php echo CHtml::textField('security_pin','', array('class' => 'span4', 'required' => 'required'));?> 
 	
 	<br />

	<?php $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType'=>'submit',
        'type'=>'danger',
        'size' => 'large',
        'label'=>'Delete',
    )); ?>	

<?php $this->endWidget(); ?>