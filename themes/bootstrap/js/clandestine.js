$(document).ready(function() {
	$('#proceed-form').hide();
});


$('#proceed').live('click', function() {
	$('#proceed-form').show();
	$(this).hide();
});