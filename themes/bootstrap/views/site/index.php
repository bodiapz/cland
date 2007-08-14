<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
?>

<div class='height-300' id="static-content">
	<?php echo isset($model -> content) ? $model -> content : ""; ?>
</div>

<p><img src="<?php echo Yii::app()->theme->baseUrl; ?>/images/logos.jpg"> </p>