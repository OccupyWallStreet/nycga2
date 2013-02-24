jQuery(document).ready(function(){

	/*
	 * SECTION: MANAGE FIELDS
	 */
	// sorting
	jQuery("#fields-sortable").sortable({
		placeholder: "highlight",
		update: function(event, ui){
			jQuery.post( ajaxurl, {
				action: 'bpge',
				method: 'reorder_fields',
				field_order: jQuery(this).sortable('serialize')
			},
			function(response){}); 
		}
	});
	jQuery( "#fields-sortable" ).disableSelection();
	
	// delete field
	jQuery("#fields-sortable li span a.delete_field").click(function(e){
		e.preventDefault();
		var li = jQuery(this).parent().parent().attr('id').split('_');
		var field = li[1]
		jQuery.post( ajaxurl, {
			action: 'bpge',
			method: 'delete_field',
			field: field
		},
		function(response){
			if (response == 'deleted' )
				jQuery('#fields-sortable li#position_'+field).fadeOut('fast');
		}); 
	});
	/*
	 * SECTION: ADD / EDIT FIELDS
	 */
	var options_count = 2;
	function new_option(type, id){
		return '<span class="'+type+'_'+id+'">' + bpge.option_text + ': &rarr; <input type="text" tabindex="'+id+'" name="options['+id+']" value="" /> <a href="#" rel="remove_'+type+'_'+id+'" class="remove_it">'+bpge.remove_it+'</a><br /></span>';
	}

	jQuery('select#extra-field-type').change(function(){
		var type = jQuery(this).val();
		var html = '';
		if ( type == 'checkbox' ||  type == 'radio' || type == 'select' ){
			html += '<label>' + bpge.enter_options + '</label>';
			html += new_option(type, 1);
			html += new_option(type, 2);
			jQuery('#extra-field-vars .content').html(html);
			jQuery('#extra-field-vars').css('display', 'block');
		}else{
			jQuery('#extra-field-vars .content').html('');
		}
	});

	jQuery('#extra-field-vars a.remove_it').live('click', function(e){
		e.preventDefault();
		var extra = jQuery(this).attr('rel').split('_');
		var action = extra[0];
		var type = extra[1];
		var id = extra[2];
		jQuery('#extra-field-vars span.'+type+'_'+id).remove();
		console.log(action + id);
	});
	
	jQuery('#extra-field-vars a#add_new').live('click', function(e){
		e.preventDefault();
		options_count += 1;
		var type = jQuery('select#extra-field-type').val();
		var option = new_option(type, options_count);
		jQuery('#extra-field-vars .content').append(option);
	});
	
});