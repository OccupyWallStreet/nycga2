var slCurrentTab	= '';

function slRecaptchaCreate( tab ) {

	jQuery( '.sexy-div-captcha' ).html( '' );
	jQuery( '#sexy-' + tab + '-recaptcha' ).html( 
		'<div id="sexy-recaptcha-container">' + 
			'<div id="recaptcha_image"></div>' +
			'<div>' +
				'<a href="javascript:Recaptcha.reload()">' + sexy_loginl_data.captcha_get + '</a>' +
			'</div>' +
			'<!--[if IE ]><label for="recaptcha_response_field">' + sexy_loginl_data.captcha_enter + ':</label><![endif]-->' +
			'<input type="text" id="recaptcha_response_field" tabindex="3" name="recaptcha_response_field" placeholder="' + sexy_loginl_data.captcha_enter + '..." />' + 
		'</div>'
	);
	
	Recaptcha.create(
		sexy_loginl_data.public_key,
		'sexy-recaptcha-container',
		{
			theme: "custom"
		}
	);
	
}

function slTabChange( tab ) {
	
	if ( slCurrentTab == tab )
		return false;
	else
		slCurrentTab	= tab;
		
	jQuery( '.sexy-login-form-wrap' ).hide();
	jQuery( '#sexy-login-' + tab + '-form' ).show();
	jQuery( '.sexy-login-tab' ).removeClass( 'selected' );
	jQuery( 'a[name=sl-tab-' + tab + ']' ).addClass( 'selected' );
	
	Recaptcha.destroy();
	
	if ( tab != 'lostpwd' &&  jQuery( '#sexy-' + tab + '-recaptcha' ).css( 'display' ) == 'block' )
		slRecaptchaCreate( tab );
	
}

function slBlockTab( errorDiv, wrapDiv ) {
	
	errorDiv.slideUp('fast');
	wrapDiv.block({ 
		message: '<img src="' + sexy_loginl_data.loadingurl + '" />', 
		overlayCSS: { 
			backgroundColor: '#fff', 
			opacity:         0.6 
		},
		css: {
			padding:    0,
			margin:     0,
			width:      '30%',
			left:       '35%',
			textAlign:  'center',
			color:      '#000',
			border:     'none',
			backgroundColor:'none',
			cursor:     'wait'
		}
	});	

}

jQuery(	function() {

	var wrapDiv			= jQuery( '#sexy-login-wrap' );
	var errorDiv		= jQuery( '#sexy-login-show-error' );
 
	if ( jQuery( '#sexy-login-recaptcha' ).css( 'display' ) == 'block' )
		slTabChange( 'login' );
	
	jQuery('a[name=sl-tab-login]').click( function(e) {
		
		e.preventDefault();
		slTabChange( 'login' );
		
	});
	
	jQuery('a[name=sl-tab-lostpwd]').click( function(e) {
		
		e.preventDefault();
		slTabChange( 'lostpwd' );
		
	});
	
	jQuery('a[name=sl-tab-register]').click( function(e) {
		
		e.preventDefault();
		slTabChange( 'register' );
		
	});

	jQuery('.sexy_login_widget #sl-login-form').submit( function( e ) {
				
		e.preventDefault();
		
		var captchaDiv	= jQuery( '#sexy-login-recaptcha' );
		
		jQuery.ajax({
		
			url:		sexy_loginl_data.ajaxurl,
			data:		jQuery( this ).serialize() + '&action=sexy_login_hook',
			type:		'POST',
			dataType:	'json',
			
			beforeSend: function() {
			
				slBlockTab( errorDiv, wrapDiv );
		
			},
			
			success: function( result ) {
				
				if ( result.success == 1 ) {
				
					window.location = result.redirect;
					
				} else {
				
					wrapDiv.unblock();
					errorDiv.html(result.error);

					if ( result.captcha ) {
						
						if ( jQuery( '#sexy-login-recaptcha' ).css( 'display' ) == 'none' ) {
							
							jQuery( '#sexy-login-recaptcha' ).show();
							slRecaptchaCreate( 'login' );							
							
						} else {
							Recaptcha.reload();
						}
						
					}
					
					errorDiv.slideDown('fast');
						
				}
				
			},
			
			error: function( xhr, textStatus, errorThrown ) {
				
				wrapDiv.html( 'ERROR' );
				
			}
			
		});
	
	}); // END sl-login-form submit.
	
	jQuery('.sexy_login_widget #sl-register-form').submit( function( e ) {
				
		e.preventDefault();
		
		jQuery.ajax({
		
			url:		sexy_loginl_data.ajaxurl,
			data:		jQuery( this ).serialize() + '&action=sexy_register_hook',
			type:		'POST',
			dataType:	'json',
			
			beforeSend: function() {
				
				slBlockTab( errorDiv, wrapDiv );
				
			},
			
			success: function( result ) {
				
				if ( result.success == 1 ) {
				
					wrapDiv.unblock();
					errorDiv.html(result.message);
					errorDiv.slideDown('fast');
					slTabChange( 'login' );
					
				} else {
					
					wrapDiv.unblock();
					
					errorDiv.html(result.error);
					
					if ( jQuery( '#sexy-register-recaptcha' ).css( 'display' ) == 'block' )	
						Recaptcha.reload();
						
					errorDiv.slideDown('fast');	
						
				}
				
			},
			
			error: function( xhr, textStatus, errorThrown ) {
				
				wrapDiv.html( 'ERROR' );
				
			}
			
		});
	
	}); // END sl-register-form
	
	jQuery('.sexy_login_widget #sl-lostpwd-form').submit( function( e ) {
				
		e.preventDefault();
		
		jQuery.ajax({
		
			url:		sexy_loginl_data.ajaxurl,
			data:		jQuery( this ).serialize() + '&action=sexy_lostpwd_hook',
			type:		'POST',
			dataType:	'json',
			
			beforeSend: function() {
			
				slBlockTab( errorDiv, wrapDiv );
		
			},
			
			success: function( result ) {
				
				if ( result.success == 1 ) {
				
					wrapDiv.unblock();
					errorDiv.html(result.message);
					errorDiv.slideDown('fast');
					slTabChange( 'login' );
					
				} else {
					
					wrapDiv.unblock();
					errorDiv.html(result.error);
					errorDiv.slideDown('fast');
						
				}
				
			},
			
			error: function( xhr, textStatus, errorThrown ) {
				
				wrapDiv.html( 'ERROR' );
				
			}
			
		});
	
	}); // END sl-lostpwd-form submit
	
}); // END on document ready.
