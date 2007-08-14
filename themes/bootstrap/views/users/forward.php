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
<div class="block-bottom-white">
    <?php
        $this->widget('bootstrap.widgets.TbGridView',array(
            'id'=>'user-payments-grid',
            'dataProvider'=>$forwards,
            //'filter'=>$model,
            'enableSorting' => false,
            'columns'=>array(
                'email_forward',
                array(
                    'class'=>'bootstrap.widgets.TbButtonColumn',
                    'template' => '{remove}',
                    'buttons'=>array(
                        'remove' => array
                        (
                            'label'=>'Delete',
                            'icon'=>'trash',
                            'url'=>'Yii::app()->createUrl("users/deleteForward", array("id"=>$data->id))',
                        ),
                    ),
                ),
            ),
        ));
    ?>
</div>

<!--a class="font-size-80 label label-info pull-right radius-0" href="<?php //Yii::app()->createUrl("users/deleteForward", array("id"=>$forward['id']));?>"><i class="icon-trash icon-white"></i></a-->



<div class="ticket-view-response">
	<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'password-form',
	'action'=>Yii::app()->urlManager->createUrl('/users/forward', array('id' => $model -> id)),
    'type'=>'vertical',
)); ?>

	<?php echo CHtml::label('Please enter email address for forwarding', 'confirm-message margin-tb-10'); ?>

    <?php echo CHtml::textField('email_forward', '', array('class' => 'span5' , 'maxlength' => 100, 'required' => true)); ?>

    <small>Forwarding will forward all existing mail to another inbox, you will still receive mail to your clandestine.se mailbox, and it will not be removed or deleted as a result of forwarding</small>

    <br />
	<?php $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType'=>'submit',
        'type'=>'success',
        'label'=>'Submit',
    )); ?>

	<?php $this->endWidget(); ?>
</div>