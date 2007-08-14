<?php 
	$tickets_count = Comments::model()->findCommentsCount($model -> id); 
	$count_tickets = ($tickets_count) ? ' ('.$tickets_count.')' : '';
?>
<div class="account"> 
	<ul class="nav nav-pills">
	  <li class="<?php echo Yii::app()->controller->action->id == 'view' ? 'active' : '' ?>"><?php echo CHtml::link('Dashboard', Yii::app()->urlManager->createUrl('/users/view', array('id' => $model -> id))); ?></li>	
	  <li class="<?php echo Yii::app()->controller->action->id == 'billing' ? 'active' : '' ?>"><?php echo CHtml::link('Billing', Yii::app()->urlManager->createUrl('/users/billing', array('id' => $model -> id)), array('class' => '')); ?></li>
	  <li class="<?php echo Yii::app()->controller->action->id == 'security' ? 'active' : '' ?>"><?php echo CHtml::link('Security Settings', Yii::app()->urlManager->createUrl('/users/security', array('id' => $model -> id))); ?></li>
	  <li class="<?php echo Yii::app()->controller->action->id == 'update' ? 'active' : '' ?>"><?php echo CHtml::link('Account settings', Yii::app()->urlManager->createUrl('/users/update', array('id' => $model -> id)), array('class' => '')); ?></li>
	  <li class="<?php echo Yii::app()->controller->action->id == 'smtp' ? 'active' : '' ?>"><?php echo CHtml::link('SMTP settings', Yii::app()->urlManager->createUrl('/users/smtp', array('id' => $model -> id)), array('class' => '')); ?></li>
	  <li class="<?php echo Yii::app()->controller->action->id == 'forward' ? 'active' : '' ?>"><?php echo CHtml::link('Mail forwarding', Yii::app()->urlManager->createUrl('/users/forward', array('id' => $model -> id)), array('class' => '')); ?></li>
	  <li class="<?php echo Yii::app()->controller->action->id == 'password' ? 'active' : '' ?>"><?php echo CHtml::link('Change Password', Yii::app()->urlManager->createUrl('/users/password', array('id' => $model -> id)));?></li>
	  <li class="<?php echo Yii::app()->controller->id == 'tickets' ? 'active' : '' ?>"><?php echo CHtml::link('Tickets'.$count_tickets, Yii::app()->urlManager->createUrl('/tickets')); ?></li>
	</ul>
</div>