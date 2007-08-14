<div class="security-info">
	<h1>My Account</h1>
</div>

<?php $this -> renderPartial('_menu', array('model' => $model)); ?>

<?php echo $this->renderPartial('_form', array('ticketModel' => $ticketModel)); ?>