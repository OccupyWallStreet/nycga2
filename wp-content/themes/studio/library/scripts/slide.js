
    jQuery(document).ready(function() {
  jQuery.noConflict();

jQuery(document).ready(function() {
	
	// Expand Panel
	jQuery("#open").click(function(){
		jQuery("div#panel").slideDown("slow");
	
	});	
	
	// Collapse Panel
	jQuery("#close").click(function(){
		jQuery("div#panel").slideUp("slow");	
	});		
	
	// Switch buttons from "Log In | Register" to "Close Panel" on click
	jQuery("#toggle a").click(function () {
		jQuery("#toggle a").toggle();
	});		
		
});
		    });