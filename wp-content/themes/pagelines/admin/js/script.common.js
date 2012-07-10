/**
 * Framework Javascript Functions
 * Copyright (c) PageLines 2008 - 2011
 *
 * Written By PageLines
 */



/**
 * On Ready Stuff
 *
 */
jQuery(document).ready(function(){
	
	jQuery('.sc_save_check').click( function(){		
		var sectionBar = jQuery(this).parents('li').find('div.section-bar').each( function() {
			if( jQuery(this).hasClass('hidden-section') ) {
				jQuery(this).removeClass('hidden-section');
			} else {
				jQuery(this).addClass('hidden-section');
			}
		});
		
		formData = jQuery("#pagelines-settings-form");
		serializedData = jQuery(formData).serialize();
		
		jQuery.ajax({
			type: 'POST',
			url: 'options.php',
			data: serializedData,
			beforeSend: function(){ 
				TemplateSetupStartSave();
			},
			success: function(response) {
				TemplateSetupDoneSaving( 'Section Options Saved!' );
			}
		});
		
		return true;
	});	
	
	/**
	 * Hide Error messages after 5 seconds
	 */
	jQuery('#message.slideup_message').delay(5000).slideUp('fast');
	
	
	
	
	jQuery('.graphic_selector .graphic_select_border').click(function(){
		GraphicSelect(this);
	});
	
	
	/**
	 * AJAX Image Uploading
	 */
	jQuery('.image_upload_button').each(function(){

		var clickedObject = jQuery(this);
		var clickedID = jQuery(this).attr('id');
		var settingID = jQuery(this).attr('title');
		var actionURL = jQuery(this).parent().find('.ajax_action_url').val();

		new AjaxUpload(clickedID, {
			  action: actionURL,
			  name: clickedID, // File upload name
			  data: { // Additional data to send
					action: 'pagelines_ajax_post_action',
					type: 'upload',
					oid: clickedID, 
					setting: settingID
				},
			  autoSubmit: true, // Submit file after selection
			  responseType: false,
			  onChange: function(file, extension){},
			  onSubmit: function(file, extension){
					clickedObject.text('Uploading'); // change button text, when user selects file	
					this.disable(); // If you want to allow uploading only 1 file at time, you can disable upload button
					interval = window.setInterval(function(){
						var text = clickedObject.text();
						if (text.length < 13){	clickedObject.text(text + '.'); }
						else { clickedObject.text('Uploading'); } 
					}, 200);
			  },
			  onComplete: function(file, response) {
				//alert(response); // Debugging
				window.clearInterval(interval);
				clickedObject.text('Upload Image');	
				this.enable(); // enable upload button
				
				// If there was an error
				if(response.search('Upload Error') > -1){
					var buildReturn = '<span class="upload-error">' + response + '</span>';
					jQuery(".upload-error").remove();
					clickedObject.parent().after(buildReturn);

				}
				else{

					var previewSize = clickedObject.parent().find('.image_preview_size').attr('value');

					var buildReturn = '<img style="max-width:'+previewSize+'px;" class="pagelines_image_preview" id="image_'+clickedID+'" src="'+response+'" alt="" />';

					clickedObject.parent().find('.uploaded_url').val(response);

					jQuery(".upload-error").remove();
					jQuery("#image_" + clickedID).remove();	
					clickedObject.parent().after( buildReturn );
					jQuery('img#image_' + clickedID).fadeIn();
					clickedObject.next('span').fadeIn();
					
				}
			  }
			});

		});

		/**
		 * AJAX Remove Option Value
		 */
		jQuery('.image_reset_button').click(function(){

			var clickedObject = jQuery(this);
			var theID = jQuery(this).attr('title');	
			var settingID = jQuery(this).attr('id');
			
			var actionURL = jQuery(this).parent().find('.ajax_action_url').val();

			var ajax_url = actionURL;

			var data = {
				action: 'pagelines_ajax_post_action',
				type: 'image_reset',
				oid: theID, 
				setting: settingID
			};

			jQuery.post(ajax_url, data, function(response) {
				var image_to_remove = jQuery('#image_' + theID);
				var button_to_hide = jQuery('.reset_' + theID);
				image_to_remove.fadeOut(500,function(){ jQuery(this).remove(); });
				button_to_hide.fadeOut();
				clickedObject.parent().find('.uploaded_url').val('');				
			});

			return false; 

		});


});
// End AJAX Uploading

/**
 * Template Setup Function - Start Save
 */
function TemplateSetupStartSave(){
	jQuery('.selected_builder .confirm_save').addClass('ajax-saving');
	jQuery('.selected_builder .confirm_save_pad').html('&nbsp;');
}


/**
 *
 * @TODO document
 *
 */
function TemplateSetupDoneSaving( text ){
	jQuery('.selected_builder .ttitle').effect("highlight", {color: "#ddd"}, 2000); 
	jQuery('.selected_builder .confirm_save').removeClass('ajax-saving');
	jQuery('.selected_builder .confirm_save_pad').text( text ).show().delay(1500).fadeOut(700);
}


/**
 * Typography
 * Creates a preview of a font in admin
 */
function PageLinesStyleFont(element, property){
	
	var currentSelect = jQuery(element).attr("id");
	
	var selectedOption = '#'+currentSelect +' option:selected';

	if(jQuery(element).hasClass("fontselector")) {
		
		var previewProp = jQuery(selectedOption).attr("id");
		
		var gFontKey = jQuery('#'+currentSelect +' option:selected').attr("title");

		var gFontBase = 'http://fonts.googleapis.com/css?family=';
		
		var stylesheetId = '#' + currentSelect + '_style';
		
		jQuery(stylesheetId).attr("href", gFontBase + gFontKey);
	} else {
		
		var previewProp = jQuery(selectedOption).val();
		
	}
	
	jQuery(element).parent().parent().parent().find('.font_preview_pad').css(property, previewProp);
	
	
}

/**
 * Graphic Selector Option
 * Changes input val based on image click....
 */
function GraphicSelect ( ClickedLayout ){
	
	if( !jQuery(ClickedLayout).hasClass('disabled_option') ){
		
		jQuery(ClickedLayout).parent().parent().find('.graphic_select_border').removeClass('selectedgraphic');
		jQuery(ClickedLayout).addClass('selectedgraphic');
		jQuery(ClickedLayout).parent().find('.graphic_select_input').attr("checked", "checked");
	
	}
}



/*
 * ###########################
 *   Color Picker
 * ###########################
 */
function setColorPicker(optionid, color){
	
	jQuery('#'+optionid+'_picker').children('div').css('backgroundColor', color);    
	
	jQuery('#'+optionid+'_picker').plColorPicker({
		color: color,
		onShow: function (colpkr) {
			jQuery(colpkr).fadeIn(300);
			return false;
		},
		onHide: function (colpkr) {
			jQuery(colpkr).fadeOut(300);
			return false;
		},
		onChange: function (hsb, hex, rgb) {
			jQuery('#'+optionid+'_picker').children('div').css('backgroundColor', '#'+hex);
			jQuery('#'+optionid+'_picker').next('input').attr('value', '#'+hex);
		},
		
	});
	
	jQuery('#'+optionid).plColorPicker({
		color: color,
		onSubmit: function(hsb, hex, rgb, el) {
			jQuery(el).val(hex);
			jQuery(el).ColorPickerHide();
			jQuery(this).attr('value', hex);
		},
		onBeforeShow: function () {
			jQuery(this).ColorPickerSetColor(this.value);
		},
		onChange: function (hsb, hex, rgb) {
			jQuery( '#'+optionid ).attr('value', '#'+hex);
			jQuery(this).parent().find('#'+optionid+'_picker').children('div').css('backgroundColor', '#'+hex);
			
		},
	})
	.bind('keyup', function(){
		var str = this.value;
		if(str && str.indexOf( '#' ) == -1){
			jQuery( '#'+optionid ).attr('value', '#' + str);
		}
		jQuery(this).ColorPickerSetColor( str );
	});
	
}


/**
 *
 * @TODO document
 *
 */
function PageLinesSimpleToggle(showElement, hideElement){
	
	jQuery(hideElement).hide();
	jQuery(hideElement+'_button').removeClass('active_button');
	
	if( jQuery(showElement).is(':visible')) {
		jQuery(showElement).fadeOut();
		jQuery(showElement+'_button').removeClass('active_button');
	} else {
		jQuery(showElement+'_button').addClass('active_button');
		jQuery(showElement).fadeIn();
		
	}
	
}


/**
 *
 * @TODO document
 *
 */
function animate_pl_button(){
	jQuery('.superlink-pagelines .slpl').fadeIn().delay(4000).fadeOut();
}



/**
 *
 * @TODO document
 *
 */
function PageLinesSlideToggle(toggle_element, toggle_input, text_element, show_text, hide_text, option){
	var opt_value; 
	var input_flag;
	
	if(jQuery(toggle_input).val() == 'show'){
		input_flag = 'hide';
		jQuery(toggle_input).val(input_flag);
		jQuery(toggle_element).fadeOut();
		
		opt_value = input_flag;
		
		jQuery(text_element).html(hide_text);
		
		jQuery(toggle_element).css('display', 'none');
	} else {
		input_flag = 'show';
		
		jQuery(toggle_input).val(input_flag);
		jQuery(toggle_element).fadeIn();
		
		opt_value = input_flag;
		jQuery(text_element).html(show_text);
		
		jQuery(toggle_element).css('display', 'block');
	}
	
	var data = {
		action: 'pagelines_ajax_save_option',
		option_name: option,
		option_value: opt_value
	};

	// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
	jQuery.post(ajaxurl, data, function(response) { });
	
}

/*
 * ###########################
 *   Email Capture
 * ###########################
 */


/**
 *
 * @TODO document
 *
 */
function sendEmailToMothership( email, input_id ){
	// validate that shit
	
	jQuery('.the_email_response').html('');
	jQuery(".the_email_response").hide();
	var hasError = false;
	var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;

	if( email == '') {
	    jQuery(".the_email_response").html('<span class="email_error">You\'re silly... The email field is blank!</span>').show().delay(2000).slideUp();
	    hasError = true;
	}
	
	else if(!emailReg.test(email)) {
	    jQuery(".the_email_response").html('<span class="email_error">Hmm... doesn\'t seem like a valid email!</span>').show().delay(2000).slideUp();
	    hasError = true;
	}
	
	if(hasError == true) { return false; }
	
	var data = {
		email: email
	};
	
	
	var option_name = 'pagelines_email_sent';
	
	jQuery.ajax({
		type: 'GET',
		url: "http://api.pagelines.com/subscribe/index.php?",
		dataType: "json",
		data: data,
		success: function(response) {
			if(response == 1){
				jQuery(".the_email_response").html('Email Sent!').show().delay(2000).slideUp();
				
				var data = {
						action: 'pagelines_ajax_save_option',
						option_name: option_name,
						option_value: email
					};

				// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: data,
					success: function(response) {
					}
				});
				
			} else if(response == 0){
				jQuery(".the_email_response").html('Email Already Submitted!').show().delay(2000).slideUp();
			}else if(response == -1){
				jQuery(".the_email_response").html('There was an error on our side. Sorry about that...').show().delay(2000).slideUp();
			}			
			
		
		}
	});
	

}

/*
 * ###########################
 *   jQuery Extension
 * ###########################
 */

jQuery.fn.center = function ( relative_element ) {
	
    this.css("position","absolute");
    this.css("top", ( jQuery(window).height() - this.height() ) / 4+jQuery(window).scrollTop() + "px");
    this.css("left", ( jQuery(relative_element).width() - this.width() ) / 2+jQuery(relative_element).scrollLeft() + "px");
    return this;
}

jQuery.fn.exists = function(){return jQuery(this).length>0;}
