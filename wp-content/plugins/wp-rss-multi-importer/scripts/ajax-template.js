jQuery(document).ready( function($) {
	$("#template-form").submit( function() {
		var str = jQuery(this).serialize();	
		var filename= jQuery('[name=filename]').val();
		var filetemplate= jQuery('[name=filetemplate]').val();
		var data = {
			action: 'wprssmi_response',
                        post_var: filetemplate,
						post_name: filename
		};
		// the_ajax_script.ajaxurl is a variable that will contain the url to the ajax processing file
	 	$.post(the_ajax_script.ajaxurl, data, function(response) {
		//	alert(response);
			jQuery("#note").html(response);
		//	if(response.indexOf("Houston")=0){
			jQuery("#save_template").hide();
		//	}
	 	});
	 	return false;
	});
	

	$("#template-restore").click( function() {
		var data = {
			action: 'wprssmi_response',
                 restore_var: 1			
		};
		$.post(the_ajax_script.ajaxurl, data, function(response) {
			jQuery("#note").html(response);
		//	Query("#save_template").hide();
			//alert(response);
				});
			
			  
		 	return false;
			});
	
		$("#template-save").click( function() {
				var filetemplate= jQuery('[name=filetemplate]').val();
				var data = {
					action: 'wprssmi_response',
					 	post_var: filetemplate,
		                 save_var: 2			
				};
				$.post(the_ajax_script.ajaxurl, data, function(response) {
					location.reload();
					jQuery("#note").html(response);
					jQuery("#save_template").show();
					jQuery("#show_action_options").hide();
						});
				 	return false;
					});
					
					
					$("#css-save").click( function() {
							var data = {
								action: 'wprssmi_response',
					                 save_var: 3			
							};
							$.post(the_ajax_script.ajaxurl, data, function(response) {
							//	location.reload();
								jQuery("#note").html(response);
								jQuery("#save_template").hide();
								jQuery("#show_action_options").hide();
									});
							 	return false;
								});
	
	
});