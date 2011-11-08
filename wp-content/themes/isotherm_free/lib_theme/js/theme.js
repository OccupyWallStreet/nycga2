jQuery.noConflict();
jQuery(document).ready(function() {

    // Initiate jQuery Dropdown navigation
    jQuery('ul.sf-menu').superfish(); 
	jQuery('div.top-featured').fadeIn();
	jQuery('li.floading').hide();
	
	// Tabs	
	var tag_cloud_class = '#tagcloud'; 
	var tag_cloud_height = jQuery('#tagcloud').height();
	jQuery('.tabs').each(function(){
		jQuery(this).children('li').children('li a').removeClass('selected'); // Add .selected class to first tab on load
		jQuery(this).children('li').children('li:nth-child(1) a').addClass('selected'); // Add .selected class to first tab on load
	});
	jQuery('.inside > *').hide();
	jQuery('.inside > *:nth-child(1)').show();
	jQuery('.tabs li a').click(function(evt){ // Init Click funtion on Tabs
	    var clicked_tab_ref = jQuery(this).attr('href'); // Strore Href value
		jQuery(this).parent().parent().children('li').children('a').removeClass('selected'); //Remove selected from all tabs
		jQuery(this).addClass('selected');
		jQuery(this).parent().parent().parent().children('.inside').children('*').hide();
		jQuery('.inside ' + clicked_tab_ref).fadeIn(500);
		 evt.preventDefault();
	});

});