<div class="security-info">
	<h1>My Account</h1>
</div>

<?php $this -> renderPartial('_menu', array('model' => $model)); ?>

 <div class='alert alert-info radius-0px text-left'> 
    <a href="<?php echo Yii::app()->urlManager->createUrl('tickets/create'); ?>" class="btn btn-success radius-0px padding-2-10px " ><i class="icon-envelope  icon-white"></i> Create New</a>    
  </div>

<?php 

$this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'tickets-grid',
	'dataProvider'=>$ticketModel->search(),
	'summaryText' => '',
	'columns'=>array(
		array(
			'name' => 'id',
			'value' => function($data, $row) {
				if($data->priority == 'low') {
			        $class='success';
			    }
			    if($data->priority == 'medium') {
			        $class='warning';
			    }
			    if($data->priority == 'high') {
			        $class='important';
			    }

			    return '<span class="badge badge-'.$class.'">' . $data -> id. '</span>';
			}, 
			'type' => 'raw'
		),
		//'tid',
		array(
			'name' => 'subject',
			'value' => function($data, $row) {
				return CHtml::link($data -> subject, Yii::app()->urlManager->createUrl('/tickets/view', array('id' => $data -> id)));
			},
			'type' => 'raw'
		),
		array(
			'name' => 'status',		
			'value' => function($data, $row) {

				return '<span class="label label-success' . $data -> status . '">' . $data -> status. '</span>';

			},
			'type' => 'raw'
		),
		array(
			'name' => 'user_id',
			'value' => function($data, $row) {
					$users = Users::model()->findByPk($data->user_id);
					if($users) {
						return $users -> first_name . " " . $users -> last_name;
					} 

					return null;					
				}
		),
		'created_at',
		/*
		'updated_at',
		'detail',
		'priority',
		*/
		/*array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
		),*/
	),
)); ?>