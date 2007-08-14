<?php
?>
<div class="payment">
	<h1>Payment</h1>
</div>

<div id='introText'>
    Please make your payment within 15 minutes or the page will refresh and provide a new bitcoin address, to learn more about how to pay with bitcoins, click here.
	When you paid, please wait few minutes, until  bitcoin transaction will confirmed.
</div>

<div class='pull-left'>
    <div class='margin-l-10 padding-5 text-left'>
        <div class='text-left margin-l-10'><strong>Current Rate</strong></div>
        <div class='text-left margin-l-20'><span class=' '>1 BTC = $<span class='btn-rate'><?php echo $rate['last']; ?></span></span></div>
        <br/>
        <div class='text-left margin-l-10'><strong>Order Total</strong></div>

        <div class='text-left margin-l-20'><span class='order-total'><?php echo $btc_total; ?></span> BTC</div></div>
    <br/>
    <div class='text-left margin-l-20'>
		<div id="payment-dialog">
			<a id='payButton' href="#" target='_blank' class='btn btn-success open-bitcoin pay-btn'><i class='bitcoin-icon'></i>Open bitcoin client</a>
        </div>
		<div class='font-size-80 btc-address'><?php echo $bitcoinAddress; ?></div>
    </div>
</div> 
<div class='pull-right padding-5'>
    <a href="#" target='_blank' class='pay-btn open-bitcoin'><img src="" title="clandestine inc"  alt='clandestine inc' id="qr" border='0'/></a>

</div>

<div id="timer_">
    <div id="timer_line"></div>
    <div style="clear:both;"></div>
    <div id="timer_time"></div>
</div>

<div class='pull-left text-left margin-l-20'>
	<?php if($free_account) : ?>
		<a href="<?php echo Yii::app()->urlManager->createUrl('/payments/createFree')?>" class='btn btn-success'>
			Unable to make payment? Click here to create a free 10MB account instead
		</a>
	<?php endif; ?>
</div>
<script type="text/javascript">

	eval('var packageSelected = "<?php echo $packageSelected; ?>"');

    if (!packageSelected) {
        packageSelected = getCookie("packageSelected");
        deleteCookie("packageSelected");
    }
	
	var confirmation = false;
    var time = 900;
    var timer_line_width = jQuery('#timer_line').width();
    var diff = timer_line_width / 900;

    var mins = 15;  //Set the number of minutes you need
    var secs = mins * 60;
    var currentSeconds = 0;
    var currentMinutes = 0;

    function Decrement() {
        currentMinutes = Math.floor(secs / 60);
        currentSeconds = secs % 60;
        if (currentSeconds <= 9)
            currentSeconds = "0" + currentSeconds;
        secs--;
        jQuery("#timer_time").html(currentMinutes + ":" + currentSeconds);
        var newWidth = jQuery('#timer_line').width() - diff;
        jQuery('#timer_line').width(newWidth);

        if (secs !== -1) {
            setTimeout('Decrement()', 1000);
        } else {
            setCookie('packageSelected', packageSelected);
            document.location = document.location.href;
        }
    }


    function getCookie(name) {
        var matches = document.cookie.match(new RegExp(
                "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
                ));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    }


    function deleteCookie(name) {
        setCookie(name, "", {expires: -1})
    }


    function setCookie(name, value, options) {
        options = options || {};

        var expires = options.expires;

        if (typeof expires == "number" && expires) {
            var d = new Date();
            d.setTime(d.getTime() + expires * 1000);
            expires = options.expires = d;
        }
        if (expires && expires.toUTCString) {
            options.expires = expires.toUTCString();
        }

        value = encodeURIComponent(value);

        var updatedCookie = name + "=" + value;

        for (var propName in options) {
            updatedCookie += "; " + propName;
            var propValue = options[propName];
            if (propValue !== true) {
                updatedCookie += "=" + propValue;
            }
        }

        document.cookie = updatedCookie;
    }


    jQuery(document).ready(function() {
		if(!confirmation){
			setTimeout('Decrement()', 1000);

			jQuery('#payButton').hover(function() {
				jQuery(this).parent().append('<div class="buttonTip">This action will open your current installed bitcoin client from your computer, if you have one installed</div>')
			}, function() {
				jQuery('.buttonTip').remove();
			});

			jQuery('.selected-package').bind('click', function() {
				//console.log(jQuery(this).parent().find('.pamount').html());
				order(jQuery(this).parent().find('.pamount').html());
				jQuery('.selected-package').parent().removeClass('alert-danger');
				jQuery(this).parent().addClass('alert-danger');
			});

			function order(term) {
				address = jQuery('.btc-address').html();
				rate = jQuery('.btn-rate').html();
				//amount = term / rate;
				//jQuery('.order-total').html(amount);
				amount = jQuery('.order-total').html();
				address = 'bitcoin: ' + address + '?amount=' + amount + '&r=clandestine inc&label=clandestine inc';
				jQuery('.pay-btn').attr('href', address);
				jQuery('#qr').attr('src', 'https://chart.googleapis.com/chart?chs=177x177&cht=qr&chl=' + address);
			}
			order(10);

			jQuery('.open-bitcoin').bind('click', function() {

				jQuery.ajax({
					async: true,
					url: '/open-bitcoin.html',
					data: {
						'address': jQuery('.btc-address').html(),
						'term': jQuery('.selected-package').val()
					},
					type: 'POST',
					beforeSend: function() {
						jQuery('.transaction-list').html('<div class="ajax-loader"></div>');
					},
					success: function(data) {
						//jQuery('.transaction-list').html(data);
						//jQuery('.load-transactions').attr('data-collection', 1) ;
					}
				});
				//order(jQuery(this).val());
			});
			jQuery('input:radio[value=' + packageSelected + ']').click();
			// setInterval('document.location = (document.location.href)', 100000);
		}

		setInterval(function() {
			console.log('bitcoin');
			$.ajax({
				url: <?php Yii::app()->request->getBaseUrl(); ?>'/payments/bitcoin',
				data: {
					'address':	$('.btc-address').html()
				},
				type: 'POST',
				dataType:'json',
				success: function(data) {
					if(data){
						if(data['response'] == 'confirmation'){
							confirmation = true;
							$('#payment-dialog').html('Your transaction sent but not confirmed. Please wait');
						}else if(data['response'] == 'paid'){
							window.location.href = data['url'];
						}
					}
					//jQuery('.transaction-list').html(data);
					//jQuery('.load-transactions').attr('data-collection', 1) ;
				}
			});
		}, 30000);
    })
</script>