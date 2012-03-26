jQuery(document).ready(function(){

jQuery('.postinn').css('opacity','0');

jQuery('#latestpost li').hoverIntent(function() {
	jQuery(this).find('.postinn')
		.animate({
			opacity: '1', 	
		}, 600); 

	} , function() {
	jQuery(this).find('.postinn')
		.animate({
			opacity: '0', 	
		}, 1000); 

});


jQuery('img').hover(function() {
jQuery(this).animate({opacity: 0.6}, "slow");
}, function() {
jQuery(this).animate({opacity: 1}, "slow");
}); 

});