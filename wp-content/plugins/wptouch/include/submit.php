<?php

if ( isset( $_POST['submit'] ) ) {
	// let's rock and roll
	$nonce = $_POST['wptouch-nonce'];
	if ( !wp_verify_nonce( $nonce, 'wptouch-nonce' ) ) {
		_e( "Nonce Failure", "wptouch" );
		die;
	}

	if ( !current_user_can( 'manage_options' ) ) {
		_e( "Security failure.  Please log in again.", "wptouch" ); 
		die;
	}
	
	unset( $_POST['submit'] );
	$a = array();

	if ( isset( $_POST['enable-post-excerpts'] ) ) {
		$a['enable-post-excerpts'] = 1;
	} else {
		$a['enable-post-excerpts'] = 0;
	}
	
	if ( isset( $_POST['enable-twenty-eleven-footer'] ) ) {
		$a['enable-twenty-eleven-footer'] = 1;
	} else {
		$a['enable-twenty-eleven-footer'] = 0;
	}

	if ( isset( $_POST['enable-page-coms'] ) ) {
		$a['enable-page-coms'] = 1;
	} else {
		$a['enable-page-coms'] = 0;
	}

	if ( isset( $_POST['enable-zoom'] ) ) {
		$a['enable-zoom'] = 1;
	} else {
		$a['enable-zoom'] = 0;
	}

	if ( isset( $_POST['enable-cats-button'] ) ) {
		$a['enable-cats-button'] = 1;
	} else {
		$a['enable-cats-button'] = 0;
	}

	if ( isset( $_POST['enable-tags-button'] ) ) {
		$a['enable-tags-button'] = 1;
	} else {
		$a['enable-tags-button'] = 0;
	}

	if ( isset( $_POST['enable-search-button'] ) ) {
		$a['enable-search-button'] = 1;
	} else {
		$a['enable-search-button'] = 0;
	}

	if ( isset( $_POST['enable-login-button'] ) ) {
		$a['enable-login-button'] = 1;
	} else {
		$a['enable-login-button'] = 0;
	}

	if ( isset( $_POST['enable-gigpress-button'] ) ) {
		$a['enable-gigpress-button'] = 1;
	} else {
		$a['enable-gigpress-button'] = 0;
	}

	if ( isset( $_POST['enable-flat-icon'] ) ) {
		$a['enable-flat-icon'] = 1;
	} else {
		$a['enable-flat-icon'] = 0;
	}
	
	if ( isset( $_POST['enable-gravatars'] ) ) {
		$a['enable-gravatars'] = 1;
	} else {
		$a['enable-gravatars'] = 0;
	}
	
	if ( isset( $_POST['enable-main-home'] ) ) {
		$a['enable-main-home'] = 1;
	} else {
		$a['enable-main-home'] = 0;
	}
	
	if ( isset( $_POST['enable-main-rss'] ) ) {
		$a['enable-main-rss'] = 1;
	} else {
		$a['enable-main-rss'] = 0;
	}
	
	if ( isset( $_POST['enable-main-email'] ) ) {
		$a['enable-main-email'] = 1;
	} else {
		$a['enable-main-email'] = 0;
	}
	
	if ( isset( $_POST['enable-truncated-titles'] ) ) {
		$a['enable-truncated-titles'] = 1;
	} else {
		$a['enable-truncated-titles'] = 0;
	}
	
	if ( isset( $_POST['enable-main-name'] ) ) {
		$a['enable-main-name'] = 1;
	} else {
		$a['enable-main-name'] = 0;
	}
	
	if ( isset( $_POST['enable-main-tags'] ) ) {
		$a['enable-main-tags'] = 1;
	} else {
		$a['enable-main-tags'] = 0;
	}

	if ( isset( $_POST['enable-main-categories'] ) ) {
		$a['enable-main-categories'] = 1;
	} else {
		$a['enable-main-categories'] = 0;
	}

//Prowl
	if ( isset($_POST['prowl-api']) ) {
		$a['prowl-api'] = $_POST['prowl-api'];
	}
	
	if ( isset( $_POST['enable-prowl-comments-button'] ) ) {
		$a['enable-prowl-comments-button'] = 1;
	} else {
		$a['enable-prowl-comments-button'] = 0;
	}
	
	if ( isset( $_POST['enable-prowl-users-button'] ) ) {
		$a['enable-prowl-users-button'] = 1;
	} else {
		$a['enable-prowl-users-button'] = 0;
	}

	if ( isset( $_POST['enable-prowl-message-button'] ) ) {
		$a['enable-prowl-message-button'] = 1;
	} else {
		$a['enable-prowl-message-button'] = 0;
	}
//

	if ( isset( $_POST['home-page'] ) ) {
		$a['home-page'] = $_POST['home-page'];
		if (strlen($a['home-page']) == 0) {
			$a['home-page'] = 'Default';
		}
	} else {
		$a['home-page'] = 'Default';
	}

	if ( isset($_POST['statistics']) ) {
		$a['statistics'] = $_POST['statistics'];
	}

	if ( isset($_POST['sort-order']) ) {
		$a['sort-order'] = $_POST['sort-order'];
	}

	if ( isset($_POST['enable-regular-default']) ) {
		$a['enable-regular-default'] = 1;
	} else {
		$a['enable-regular-default'] = 0;
	}
	
	if ( isset($_POST['enable-show-comments']) ) {
		$a['enable-show-comments'] = 1;
	} else {
		$a['enable-show-comments'] = 0;
	}		
	
	if ( isset($_POST['enable-show-tweets']) ) {
		$a['enable-show-tweets'] = 1;
	} else {
		$a['enable-show-tweets'] = 0;
	}		

	if ( isset($_POST['enable-fixed-header']) ) {
		$a['enable-fixed-header'] = 1;
	} else {
		$a['enable-fixed-header'] = 0;
	}		
	
	if ( isset( $_POST['custom-user-agents'] ) ) {
		$new_array = array();
		if ( !strlen( trim( $_POST['custom-user-agents'] ) ) ) {
			$a['custom-user-agents'] = array();
		} else {				
			$a['custom-user-agents'] = explode( ",", $_POST['custom-user-agents'] );
			foreach( $a['custom-user-agents'] as $agent ) {
				$new_array[] = trim( $agent );	
			}
			$a['custom-user-agents'] = $new_array;
		}
	} else {
		$a['custom-user-agents'] = array();	
	}

	if ( isset($_POST['excluded-cat-ids']) ) {
		$a['excluded-cat-ids'] = $_POST['excluded-cat-ids'];
	}

	if ( isset($_POST['excluded-tag-ids']) ) {
		$a['excluded-tag-ids'] = $_POST['excluded-tag-ids'];
	}
		
	if ( isset($_POST['adsense-id']) ) {
		$a['adsense-id'] = trim( $_POST['adsense-id'] );
	}

	if ( isset($_POST['adsense-channel']) ) {
		$a['adsense-channel'] = $_POST['adsense-channel'];
	}		

	if ( isset($_POST['post-cal-thumb']) ) {
		$a['post-cal-thumb'] = $_POST['post-cal-thumb'];
	}

	if ( isset($_POST['h2-font']) ) {
		$a['h2-font'] = $_POST['h2-font'];
	}

	if ( isset($_POST['style-text-justify']) ) {
		$a['style-text-justify'] = $_POST['style-text-justify'];
	}

	if ( isset($_POST['style-background']) ) {
		$a['style-background'] = $_POST['style-background'];
	}

	if ( isset($_POST['style-icon']) ) {
		$a['style-icon'] = $_POST['style-icon'];
	}
	
	if ( isset( $_POST['enable-exclusive'] ) ) {
		$a['enable-exclusive'] = 1;	
	} else {
		$a['enable-exclusive'] = 0;
	}
	
	if ( isset( $_POST['wptouch-language'] ) ) {
		$a['wptouch-language'] = $_POST['wptouch-language'];
	} else {
		$a['wptouch-language'] = "auto";	
	}
	
	if ( isset( $_POST['ad_service'] ) ) {
		$a['ad_service'] = $_POST['ad_service'];
	} else {
		$a['ad_service'] = 'none';	
	}	

	foreach ($_POST as $k => $v) {
		if ($k == 'enable_main_title') {
			$a['main_title'] = $v;
		} else {
			if (preg_match('#enable_(.*)#', $k, $matches)) {
				$id = $matches[1];
				if (!isset($a[$id]))
				$a[$id] = $_POST['icon_' . $id];
			}
		}
	}
	
	$a['header-title'] = $_POST['header-title'];
	if (!isset($a['header-title']) || (isset($a['header-title']) && strlen($a['header-title']) == 0)) {
		$a['header-title'] = get_bloginfo('title');
	}
	
	$a['custom-footer-msg'] = $_POST['custom-footer-msg'];
	if (!isset($a['custom-footer-msg']) || (isset($a['custom-footer-msg']) && strlen($a['custom-footer-msg']) == 0)) {
		$a['custom-footer-msg'] = 'All content Copyright '. get_bloginfo('name') . '';
	}
	
	
	$a['header-background-color'] = $_POST['header-background-color'];
	$a['header-border-color'] = $_POST['header-border-color'];
	$a['header-text-color'] = $_POST['header-text-color'];
	$a['link-color'] = $_POST['link-color'];
	//Prowl
	$a['prowl-api'] = $_POST['prowl-api'];
	
	$values = serialize($a);
	update_option('bnc_iphone_pages', $values);
} elseif ( isset( $_POST['reset'] ) ) {
	update_option( 'bnc_iphone_pages', false );
}
 	
do_action( 'wptouch_load_locale' );

global $wptouch_settings;
$wptouch_settings = bnc_wptouch_get_settings();