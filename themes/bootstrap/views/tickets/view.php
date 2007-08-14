<div class="security-info">
	<h1>My Account</h1>
</div>

<?php $this -> renderPartial('_menu', array('model' => $model)); ?>

<div class="ticket-view-header">
	<h1>Ticket Detail <a href="/tickets" class='pull-right link1 font-size-80'><i class="icon-chevron-left icon-white"></i> Back</a></h1>
</div>

<?php
		$this->widget('bootstrap.widgets.TbAlert', array(
	        'block'=>true, // display a larger alert block?
	        'fade'=>false, // use transitions?
	        'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
	        'alerts'=>array( // configurations per alert type
	            'success'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
	        ),
	    )); 
	?>

<div class="padding-5 text-left alert alert-info radius-0">
	<div class='row margin-0'>
		<div class='pull-left'>
				Ticket Id   <span class='bold'>#<?php echo $ticketModel -> id; ?></span>
				<span class='label label-success radius-0'><?php echo $ticketModel -> status; ?></span>  
		</div>
		<div class='pull-right'> Posted at : <span class="font-size-80 label label-info radius-0"><?php echo $ticketModel -> created_at; ?></span> </div>
	</div>
	<div class='row margin-0 padding-5 color-b'>
		<div><strong>Subject</strong></div>
		<div class=' margin-l-10'><?php echo $ticketModel -> subject; ?> </div>
	</div>

	<div class='row margin-0 padding-5 color-b'>
		<div><strong>Detail</strong></div>
		<div class=' margin-l-10'><?php echo nl2br($ticketModel -> detail); ?></div>

	</div>
	<div class='row margin-0 padding-5'>
		Created by: <span class='label label-info  radius-0'><?php echo Users::model()->findByPk(Yii::app()->user->getState('userid')) -> first_name . ' ' . Users::model()->findByPk(Yii::app()->user->getState('userid')) -> last_name; ?></span> 
	</div>
</div> 

<div class="ticket-view-comment">
	<h6 class="bg3 padding-2">Comments - </h6> 

	<?php foreach($comments as $comment) : ?>
	<div class="padding-5 border-b-1">
		<div  class="bg2 padding-2 ">
		 	 <i class="icon-user icon-white"></i> <?php echo ($comment -> user_id == 0) ? 'Administrator' : Users::model()->findByPk($comment -> user_id) -> first_name . ' ' . Users::model()->findByPk($comment -> user_id) -> last_name; ?>   		 	
		 	<div class="font-size-80 label label-info pull-right radius-0"><?php echo $comment -> created_at; ?></div>
		 </div>
	 	 <div class="margin-l-20 padding-5">
	 		<i class="icon-comment icon-white"></i> <?php echo nl2br($comment -> comment); ?>
 		</div> 
		</div>
	<?php endforeach; ?>
</div>

<div class="ticket-view-response">
	<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
		'id'=>'comments-form',
		'enableAjaxValidation'=>false,
	)); ?>

	<?php echo $form->textAreaRow($commentModel,'comment',array('rows'=>6, 'cols'=>50, 'class'=>'span8')); ?>

	<br />
	<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'success',
			'label'=>'Submit',
		)); ?>

	<?php $this->endWidget(); ?>

</div>

