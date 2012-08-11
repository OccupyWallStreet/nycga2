jQuery.noConflict();
jQuery(document).ready(function() {
	
	jQuery('#master_switch').children('.neg').hide();
	jQuery('.maintable .subheading a').children('.neg').hide();
	jQuery('.feature-box .subheading a').parents('.subheading').siblings('.options-box').hide();
	
	jQuery('#master_switch').click(function() {
		jQuery(this).toggleClass('active');
		jQuery(this).children('.pos').toggle();
		jQuery(this).children('.neg').toggle();
		jQuery('.subheading a').toggleClass('active');
		jQuery('.subheading a').children('.pos').toggle();
		jQuery('.subheading a').children('.neg').toggle();
		jQuery('.options-box').toggle();
		return false;
	});
	
	jQuery('.feature-box .subheading a').click(function() {
		jQuery(this).toggleClass('active');
		jQuery(this).children('.pos').toggle();
		jQuery(this).children('.neg').toggle();
		jQuery(this).parents('.subheading').siblings('.options-box:first').toggle();
		return false;
	});
	
	// Sortable behaviors
	jQuery(function() {
		jQuery("div[id*=sortme]").sortable({});
	});	
	
	// JWYSIWYG editor identifiers
	jQuery(function(){
	    jQuery('.wysiwyg').wysiwyg();
	});
			
	// Description Tooltip Toggle
	jQuery('span.trigger').click(function() {
		jQuery(this).parent('.bubbleInfo').children('.popup').toggle();
		return false;
	});
			
	// Hide Blocks if clicked outside
	jQuery(document).click(function() {
		jQuery('.popup').hide();
	});	

    // Clone image with description
	jQuery('.addremove span.add').live('click', function(e) {	  
	    jQuery.countclone = 1
		jQuery.countclone++
	    // prevent link default action
	    e.preventDefault();		
		// clone
		var parentdiv = jQuery(this).parent('.addremove').parent('.upc-wrap').parent('.text').parent('.table-row');
		parentdiv.after(parentdiv.clone());
		// set id
		var newdiv = parentdiv.next('div');
		var id = jQuery(parentdiv).attr('id');
        var newid = id.substring(0, id.length-1)+jQuery.countclone;
		jQuery(newdiv).attr('id', newid);		
		jQuery('input',newdiv).each(function() {
            var id = jQuery(this).attr('id');
		    var newid = id.substring(0, id.length-1)+jQuery.countclone;		
		    jQuery(this).attr('id', newid); // add new id
			jQuery(this).val(''); // clear value
            var name = jQuery(this).attr('name');
		    var newname = name.substring(0, name.length-1)+jQuery.countclone;				  
		    jQuery(this).attr('name', newname);
		});
		jQuery('.upload_button',newdiv).each(function() {
		    jQuery.countclone++
            var id = jQuery(this).attr('id');
		    var newid = id.substring(0, id.length-1)+jQuery.countclone;		
		    jQuery(this).attr('id', newid);
            var name = jQuery(this).attr('name');
		    var newname = name.substring(0, name.length-1)+jQuery.countclone;				  
		    jQuery(this).attr('name', newname);
		});

	});
	  
	jQuery('.addremove span.remove').live('click', function(e) {	  
	    // prevent link default action
	    e.preventDefault();		
		// remove		
		var parentdiv = jQuery(this).parent('.addremove').parent('.upc-wrap').parent('.text').parent('.table-row');
		parentdiv.remove();		
	});
	

});