/*
 * WPtouch 1.9.x -The WPtouch Admin Javascript File
 * Last Updated: August 7th, 2010
 */ 
var wptouchSpinnerCount = 1;
var wpQuery = jQuery;

function wptouchSpinnerDone() {
	wptouchSpinnerCount = wptouchSpinnerCount - 1;
	if ( wptouchSpinnerCount == 0 ) {
		jQuery('img.ajax-load').fadeOut( 1000 );
	}	
}

jQuery( document ).ready( function() {
	setTimeout(function() { jQuery('#wptouchupdated').fadeIn(250); }, 750);
	setTimeout(function() { jQuery( '#wptouchupdated' ).fadeOut(200); }, 2750);

	jQuery('#header-text-color, #header-background-color, #header-border-color, #link-color').ColorPicker({
		onSubmit: function(hsb, hex, rgb, el) { jQuery(el).val(hex); jQuery(el).ColorPickerHide(); },
		onBeforeShow: function () { jQuery(this).ColorPickerSetColor( jQuery(this).attr('value') ); }
	});

	jQuery("a.fancylink").fancybox({
		'padding':	10, 'zoomSpeedIn': 250, 'zoomSpeedOut': 250, 'zoomOpacity': true, 'overlayShow': false, 'frameHeight': 320, 'frameWidth': 450, 'hideOnContentClick': true
	});
	
	wptouchAjaxTimeout = 5000;
	
	// uncomment below to simulate a failure
	// wptouchBlogUrl = 'http://somefakeurlasdf.com';
	jQuery.ajax( {
		'url': wptouchBlogUrl + '?wptouch-ajax=news',
		'success': function(data) { 
			jQuery( '#wptouch-news-content' ).hide().html( data ).fadeIn(); 
			wptouchSpinnerDone();
		},
		'timeout': wptouchAjaxTimeout,
		'error': function() {
			jQuery( '#wptouch-news-content' ).hide().html( '<ul><li class="ajax-error">Unable to load the news feed</li></ul>' ).fadeIn();
			wptouchSpinnerDone();
		},
		'dataType': 'html'
	});
	
	jQuery(function(){
		var tabindex = 1;
		jQuery('input,select').each(function() {
			if (this.type != "hidden") {
				var $input = jQuery(this);
				$input.attr("tabindex", tabindex);
				tabindex++;
			}
		});
	});
	
	wpQuery( document ).ready( function() {
		if ( wpQuery( '#advertising-options' ).length ) {
			wptouchHandleAdvertising();
			
			wpQuery( '#ad_service' ).change( function() { wptouchHandleAdvertising() } );
		}
	});
});

function wptouchHandleAdvertising() {
	var selectValue = wpQuery( '#ad_service' ).attr( 'value' );
	if ( selectValue == 'none' ) {
		wpQuery( '#google-adsense' ).hide();
	} else if ( selectValue == 'adsense' ) {
		wpQuery( '#google-adsense' ).fadeIn( 250 );
	} 
}
