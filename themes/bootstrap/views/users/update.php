<div class="security-info">
	<h1>My Account</h1>
</div>

<?php $this -> renderPartial('_menu', array('model' => $model)); ?>

<?php if($model -> premium): ?>
	<div class="destructBtn">
	<?php if($model -> password_reminder): ?>
		<div>Password Reminder Interval: <?php echo $model -> password_reminder; ?> days</div>
			<?php $this->widget('bootstrap.widgets.TbButton', array(
							'buttonType'=>'link',
							'url' => Yii::app()->urlManager->createUrl('/users/disablePassReminder', array('id' => $model -> id)),
							'type'=>'danger',
							'size' => 'normal',
							'label'=> 'Disable Password Reminder',
		)); ?>
	<?php else: ?>
		<?php $this->widget('bootstrap.widgets.TbButton', array(
							'buttonType'=>'link',
							'url' => Yii::app()->urlManager->createUrl('/users/passwordReminder', array('id' => $model -> id)),
							'type'=>'info',
							'size' => 'normal',
							'label'=> 'Enable Password Reminder',
		)); ?>
	<?php endif; ?>
	</div>
	</br>
<?php endif; ?>

<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>