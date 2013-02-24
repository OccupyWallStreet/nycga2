function slShowCustomUrl( thisId, thisForm ) {
	
	if ( thisId == 'sl-redirect-logout' ) var displayCustom = jQuery( '#sl-redirect-logout-url', thisForm );
	else var displayCustom = jQuery( '#sl-redirect-login-url', thisForm );
	
	if ( jQuery( '#' + thisId, thisForm ).val() == 'custom' ) displayCustom.show();
	else displayCustom.hide();
	
}

function slShowCaptchaOptions( thisForm ) {

	var captchaOptions = jQuery( '.captcha-options', thisForm );
	if( jQuery( '#enable-captcha' ).is( ':checked' ) ) captchaOptions.show();
	else captchaOptions.hide();
	
}