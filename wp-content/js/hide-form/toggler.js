jQuery(document).ready(function() {
	//toggle group description at the top of the group page
	jQuery("#top-group-desc-excerpt").click(function(){
		jQuery("#top-group-desc").show('slow');
		jQuery("#top-group-desc-excerpt").hide('fast');
	});
	jQuery("#top-group-desc").click(function(){
		jQuery("#top-group-desc").hide('slow');
		jQuery("#top-group-desc-excerpt").show('fast');
	});
	
	jQuery("#principles-solidarity-title").click(function(){
		jQuery("#principles-solidarity-text").slideToggle('slow');
		return false;
	});

	
	//Hide (Collapse) the toggle containers on load -for group tags
	jQuery(".toggle_container").hide(); 
	
	//Switch the "Open" and "Close" state per click then slide up/down (depending on open/close state)
	jQuery("h2.trigger").click(function(){
		jQuery(this).toggleClass("active").next().slideToggle("slow");
		return false; //Prevent the browser jump to the link anchor
	});
	
    jQuery("button").click(function () {
      jQuery(".event-map").slideToggle("fast");
    });
    
});