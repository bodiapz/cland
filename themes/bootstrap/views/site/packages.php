<?php
	$this->widget('bootstrap.widgets.TbAlert', array(
        'block'=>true, // display a larger alert block?
        'fade'=>false, // use transitions?
        'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
        'alerts'=>array( // configurations per alert type
            'danger'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
        ),
    )); 
?>

<?php $form = $this -> beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id' => 'register-package-form',
	'type' => 'horizontal',
	'enableAjaxValidation' => false,
)); ?>

<table>
	<?php foreach($model as $package): ?>
	<tr>
		<td>
	      	<label class="radio alert-success  text-upper radius-0 text-left padding-10">
	      		<?php echo CHtml::hiddenField('account', $account); ?>
				<?php echo CHtml::radioButton('package', $package -> id, array('class' => 'radio-package', 'value' => $package -> id)); ?>
				<span class='badge badge-info'><?php echo $package->name; ?></span><br/>
				<span>$<?php echo number_format($package->cost, 3, '.', '');?></span> 
				<span>[<?php echo $package -> cost / $rate['last']; ?> BTC] for  <?php echo $package -> term; ?> months</span>
			</label>   
		</td>
	</tr>
	<?php endforeach; ?>	          
</table>

<div class="text-left">
<?php $this->widget('bootstrap.widgets.TbButton', array(
					'buttonType' => 'submit',
					'type' =>'success',
					'size' => 'large',
					'label'=> 'Continue',
	)); 
?>
</div>

<?php $this->endWidget(); ?>