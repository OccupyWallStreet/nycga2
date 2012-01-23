jQuery(document).ready( function($) {
	
	//prefill form fields for user
	$('form.bp-group-documents-admin-upload input[name=name]').val('Display Name...').css('color','grey');
	$('form.bp-group-documents-admin-upload input[name=name]').focus(function(){
		$(this).val('').css('color','black');
	});
	$('form.bp-group-documents-admin-upload textarea[name=description]').val('Description...').css('color','grey');
	$('form.bp-group-documents-admin-upload textarea[name=description]').focus(function(){
		$(this).val('').css('color','black');
	});

	//on submit, hijack the form and process with ajax
	$('form.bp-group-documents-admin-upload').submit(function(){

		//check and warn user if required field "group" is not selected
		if( $(this).find('select[name=group]').val() == '0' ) {
			alert("You must select a group");
			return false;
		}

		//remove the prefill text before submission if user didn't change it
		if( $(this).find('input[name=name]').val() == 'Display Name...') {
			$(this).find('input[name=name]').val('');
		}
		if( $(this).find('textarea[name=description]').val() == 'Description...') {
			$(this).find('textarea[name=description]').val('');
		}

		//get the immediate parent row to prepare for the fade-out
		thisRow = $(this).parents().filter('.doc-single');

		//run the server-side document move
		$.post(ajaxurl, {
			action:'bp_group_documents_admin_upload_submit',
			file:$(this).find('input[name=file]').val(),
			group:$(this).find('select[name=group]').val(),
			name:$(this).find('input[name=name]').val(),
			description:$(this).find('textarea[name=description]').val()
			}, function(response){

					//The response tends to have 1's and 0's appeneded.
					//find the end of the sentence "." and leave off the rest
					sentence_end = response.lastIndexOf('.');
					message = response.substring(0,sentence_end+1);
					
					//format the message and add response text
					$('#bp-group-documents-bulk-message').html('<div id="message" class="updated"><p></p></div>');
					$('#bp-group-documents-bulk-message #message p').html(message);
					$('#bp-group-documents-bulk-message').fadeIn('fast');
					thisRow.slideUp('slow');

				}
			);

		return false; //this keeps the form from doing a standard refresh submit
	});

});
