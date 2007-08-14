
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

<div class="lang-content">
	<div class="lang-title">Choose a language</div>
	<form action="" method="post">
		<div class="lang-block">
            <?php foreach($languages as $lang) : ?>
                <label><input type="radio" name="lang" value="<?php echo $lang['code']; ?>" <?php if(Yii::app()->user->getState('language') == $lang['code']) echo 'checked'; ?>><div class="flag flag-<?php echo (strlen($lang['code']) != 3) ? strtolower(substr($lang['code'], -2)) : ''; ?>"></div><?php echo $lang['name_en']; ?></label>
            <?php endforeach; ?>
		</div>
		<noscript>
			<button class="btn lang-button" type="submit">Submit</button>
		</noscript>
	</form>
</div>

<script type='text/javascript'>

 $(document).ready(function() { 
   $('input[name=lang]').change(function(){
        $('form').submit();
   });
  });

</script>