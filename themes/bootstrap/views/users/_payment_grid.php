<?php 

$payments = Payments::model()->findPaymentsByUser($model -> id, $page);

$this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'user-payments-grid',
	'dataProvider'=>$payments,
	//'filter'=>$model,
	'enableSorting' => false,
	'columns'=>array(
		array(
			'name' => 'package_id',
			'value' => '$data -> package -> name'
		),
		array(
			'name' => 'amount',
			'value' => '$data -> amount'
		),
		'payment_date',
		'next_due_date',
		array(
			'header' => 'Status',
			'value' => function($data, $row) {
				if(!is_null($data -> paid)) {
					$status = 'Paid';
				} else {
					$status = 'Pending';
				}

				return $status;
			}
		)
	),
)); ?>