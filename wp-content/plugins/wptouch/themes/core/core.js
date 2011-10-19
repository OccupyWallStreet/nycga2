/*
 * WPtouch 1.9.x -The WPtouch Core JS File
 */

/////-- Let's setup our namspace in jQuery -- /////
var $wpt = jQuery.noConflict();

if ( (navigator.platform == 'iPhone' || navigator.platform == 'iPod') && typeof orientation != 'undefined' ) { 
	var touchStartOrClick = 'touchstart'; 
} else {
	var touchStartOrClick = 'click'; 
};

/////-- Get out of frames! -- /////
if (top.location!= self.location) {top.location = self.location.href}

/////// -- New function fadeToggle() -- /////
$wpt.fn.wptouchFadeToggle = function(speed, easing, callback) { 
	return this.animate({opacity: 'toggle'}, speed, easing, callback); 
};

/////-- Switch Magic -- /////
function wptouch_switch_confirmation( e ) {
	if ( document.cookie && document.cookie.indexOf( 'wptouch_switch_toggle' ) > -1) {
	// just switch
		$wpt("a#switch-link").toggleClass("offimg");
		setTimeout('switch_delayer()', 1250); 
	} else {
	// ask first
	    if ( confirm( "Switch to regular view? \n \n You can switch back again in the footer." ) ) {
			$wpt("a#switch-link").toggleClass("offimg");
			setTimeout('switch_delayer()', 1350); 
		} else {
	        e.preventDefault();
	        e.stopImmediatePropagation();
		}
	}
}

/////-- Prowl Results -- /////
if ( $wpt('#prowl-success').length ) {
	setTimeout(function() { $wpt('#prowl-success').fadeOut(350); }, 5250);
}
if ( $wpt('#prowl-fail').length ) {
	setTimeout(function() { $wpt('#prowl-fail').fadeOut(350); }, 5250);
}

/////// -- jQuery Tabs -- ///////
$wpt(function() {
    var tabContainers = $wpt('#menu-head > ul');   
    $wpt('#tabnav a').bind(touchStartOrClick, function () {
        tabContainers.hide().filter(this.hash).show();
    $wpt('#tabnav a').removeClass('selected');
    $wpt(this).addClass('selected');
        return false;
    }).filter(':first').trigger(touchStartOrClick);
});

/////-- Ajax comments -- /////
function bnc_showhide_coms_toggle() {
	$wpt('#commentlist').wptouchFadeToggle(350);
	$wpt("img#com-arrow").toggleClass("com-arrow-down");
	$wpt("h3#com-head").toggleClass("comhead-open");
}
	
function doWPtouchReady() {

/////-- Tweak jQuery Timer -- /////
	$wpt.timerId = setInterval(function(){
		var timers = $wpt.timers;
		for (var i = 0; i < timers.length; i++) {
			if (!timers[i]()) {
				timers.splice(i--, 1);
			}
		}
		if (!timers.length) {
			clearInterval($wpt.timerId);
			$wpt.timerId = null;
		} 
	}, 83);
	
/////-- Menu Toggle -- /////
	$wpt('#headerbar-menu a').bind( touchStartOrClick, function( e ){
		$wpt('#wptouch-menu').wptouchFadeToggle(350);
		$wpt("#headerbar-menu a").toggleClass("open");
	});

/////-- Search Toggle -- /////
	$wpt('a#searchopen, #wptouch-search-inner a').bind( touchStartOrClick, function( e ){	
		$wpt('#wptouch-search').wptouchFadeToggle(350);
	});
	
/////-- Prowl Toggle -- /////
	$wpt('a#prowlopen').bind( touchStartOrClick, function( e ){	
		$wpt('#prowl-message').wptouchFadeToggle(350);
	});
	
/////-- WordTwit Toggle -- /////
	$wpt('a#wordtwitopen').bind( touchStartOrClick, function( e ){	
		$wpt('#wptouch-wordtwit').wptouchFadeToggle(350);
	});

/////-- Gigpress Toggle -- /////
	$wpt('a#gigpressopen').bind( touchStartOrClick, function( e ){	
		$wpt('#wptouch-gigpress').wptouchFadeToggle(350);
	});

/////-- Login Toggle -- /////
	$wpt('a#loginopen, #wptouch-login-inner a').bind( touchStartOrClick, function( e ){	
		$wpt('#wptouch-login').wptouchFadeToggle(350);
	});
	
/////// -- Single Post Page Bookmark Toggle -- /////
$wpt( 'a#obook' ).bind( touchStartOrClick, function() {
	$wpt('#bookmark-box').wptouchFadeToggle(350);
});

/////-- Try to make imgs and captions nicer in posts -- /////
		$wpt( '.singlentry img, .singlentry .wp-caption' ).each( function() {
			if ( $wpt( this ).width() <= 250 ) {
				$wpt( this ).addClass( 'aligncenter' );
			}
		});
	
/////-- Filter FollowMe Plugin -- /////
	if ( $wpt( '#FollowMeTabLeftSm' ).length ) {
		$wpt( '#FollowMeTabLeftSm' ).remove();
	}
	
//	$wpt( '.content iframe' ).each( function() {
//		var iframeSrc = $wpt( this ).attr( 'src' );
//		$wpt( this ).replaceWith( '<video src='+iframeSrc+' controls=controls></video>' )
//		
//	});
	

	
} // End document ready

$wpt( document ).ready( function() { doWPtouchReady(); } );