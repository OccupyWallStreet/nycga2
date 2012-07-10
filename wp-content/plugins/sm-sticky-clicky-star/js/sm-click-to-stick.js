jQuery(document).ready(function() {
	// move sticky check box out of sticky span and hide it (must be present on page for sticky to stick when post saved)
	jQuery('#sticky').appendTo('#post-visibility-select').css('display','none');	
	// remove sticky span
	jQuery('#sticky-span').remove();
	jQuery('.smClickToStick').click(function(e) {
		sm_sticky_toggle(jQuery(this).attr('href'), jQuery(this));
		e.preventDefault();
	});
});

function sm_sticky_toggle(args, obj) {
	jQuery.ajax({
		//url:"/wp-admin/admin-ajax.php",  
		//TEMP FIX UNTIL FULL DIR VAR IS AVAILABLE
		url:"admin-ajax.php",
		type:"POST",
		data:"action=sm_sticky&"+args,
		success:function(results) {
			if(results != '') {
				//alert('Success: '+results);
				if(results == 'added') {
					// check sticky box
					jQuery('#sticky').attr('checked','checked');
					obj.addClass('isSticky');
					obj.attr('title', 'Remove Sticky');
				}
				if(results == 'removed') {
					// uncheck sticky box
					jQuery('#sticky').removeAttr('checked');
					obj.removeClass('isSticky');
					obj.attr('title', 'Make Sticky');
					id = obj.attr('id');
					id= id.replace('smClickToStick', '');
					 jQuery.each(jQuery('#post-'+id+' .post-state'), function(index, value) { if(jQuery(value).html().search('Sticky') > -1) {jQuery(value).html(''); } });
				}
			}
			else {
				alert('There was a problem with your request, please logout, log back in and try again. If the problem persist contact website administrator. ['+results+']');
			}
		}
	});	
}