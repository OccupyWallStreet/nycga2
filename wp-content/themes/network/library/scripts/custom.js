// AJAX Functions
var jq = jQuery;

jq(document).ready( function() {

	/**** Additional Functions (David Bisset) *************************************/
		
	/*jq('#articleBox').pajinate({
		start_page : 0,
		items_per_page : 12,
		item_container_id : '.articles',
		nav_label_first : '<< first',
		nav_label_last : 'last >>',
		nav_label_prev : '< prev',
		nav_label_next : 'next >'	
	});*/
	
    jq('ul.sf-menu').superfish({ 
        delay:       200,                            	// one second delay on mouseout 
        animation:   {opacity:'show',height:'show'},  	// fade-in and slide-down animation 
        speed:       'fast',                          	// faster animation speed 
        autoArrows:  false                           	// disable generation of arrow mark-up 
    });
	

});