jQuery(function($) {
	
});
function pulse_press_disable_submit(){
	// we need sometime out to have a better reading of what is really there. 
	setTimeout(function() {
		
		var remainder = 140 - jQuery("#posttext").val().length
		if(remainder < 0) {
			 jQuery('#submit').attr('disabled','disabled').addClass('disabled');
			
		} else{
			  jQuery('#submit').removeAttr('disabled').removeClass('disabled');
		}
		// update the counter
		jQuery('#post-count').html(remainder);
	},50);
}