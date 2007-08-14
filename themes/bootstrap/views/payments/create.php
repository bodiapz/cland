<?php

?>

<div class="payment">
	<h1>Payment</h1>
</div>

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'payments-form',
	'enableAjaxValidation'=>false,
	'action' => Yii::app()->urlManager->createUrl('/payments/process')
));?>

<?php if(!empty($package)): ?>
<div id="package">
	<table>
		<tbody>
			<tr> 
	          <td> 
	          	<label class="radio alert-success  text-upper radius-0 text-left padding-10">
					<?php echo CHtml::radioButton('package', $package -> id, array('class' => 'radio-package', 'value' => $package -> id)); ?>
					<span class='badge badge-info'> <?php echo $package->name; ?></span><br/>
					$<span>
						<?php echo number_format($package->cost, 3, '.', '');?> 
					</span> 
					[<?php echo $package -> cost / $rate['last']; ?> BTC] for  <?php echo $package->term;?> months.				 	
				</label>   
	          </td>
	        </tr> 
	    </tbody>        
	</table> 
</div>
<?php endif; ?>

<div class='text-left margin-l-20'>
<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType' => 'submit',
			'type' => 'danger',
			'size' => 'large',
			'label'=> 'Pay with Bitcoin',
		)); ?>
</div>

<?php $this->endWidget(); ?>