<?php
	$i = 1;
?>

<title>PRINT THIS - Your two-factor authentication codes</title>

<div class="twofactor-grid">
    <p>
        <span class="key-title">Key</span>
        <span class="key"><?php echo CHtml::encode($key); ?></span>
        <span class="print-this">Print this on paper</span>
    </p>
 
    <?php foreach($hotp as $value): ?>
    	 <ul>		
    	 	<?php foreach($value as $code): ?>
			    <li>
			    	<span class="counter"><?php echo $i++; ?></span>
			     	<span class="code"><?php echo $code; ?></span>
			    </li>
			<?php endforeach; ?>
		</ul>
	<?php endforeach; ?>
</div>

<style>
    .required {
        font-weight: bold;
    }
    .twofactor-grid {
        width: 600px;
    }
    .twofactor-grid:after {
        content: "";
        display: table;
        clear: both;
    }
    .twofactor-grid > p {
        text-align: left;
    }
    .twofactor-grid .key-title {
        font-weight: bold;
        padding-right: 0.5em;
    }
    .twofactor-grid .key-title:after {
        content: ":";
    }
    .twofactor-grid ul {
        float: left;
        padding-left: 0;
        margin: 0 1.5em 1em 0;
    }
    .twofactor-grid ul li {
        list-style-type: none;
    }
    .twofactor-grid .counter {
        float: left;
        width: 2em;
        padding-right: 1em;
        text-align: right;
        font-weight: bold;
    }

    .print-this {
        margin-left: 80px;
        font-weight: bold;
        text-transform: uppercase;
        color: red;
    }
</style>