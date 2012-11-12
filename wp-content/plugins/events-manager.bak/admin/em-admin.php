<?php
//Admin functions

/**
 * Generate warnings and notices in the admin area
 */
function em_admin_warnings() {
	//If we're editing the events page show hello to new user
	$events_page_id = get_option ( 'dbem_events_page' );
	$dismiss_link_joiner = ( count($_GET) > 0 ) ? '&amp;':'?';
	
	if( current_user_can('activate_plugins') ){
		//New User Intro
		if (isset ( $_GET ['disable_hello_to_user'] ) && $_GET ['disable_hello_to_user'] == 'true'){
			// Disable Hello to new user if requested
			update_option('dbem_hello_to_user',0);
		}elseif ( get_option ( 'dbem_hello_to_user' ) ) {
			//FIXME update welcome msg with good links
			$advice = sprintf( __("<p>Events Manager is ready to go! It is highly recommended you read the <a href='%s'>Getting Started</a> guide on our site, as well as checking out the <a href='%s'>Settings Page</a>. <a href='%s' title='Don't show this advice again'>Dismiss</a></p>", 'dbem'), 'http://wp-events-plugin.com/documentation/getting-started/?utm_source=em&utm_medium=plugin&utm_content=installationlink&utm_campaign=plugin_links', get_bloginfo('url').'/wp-admin/admin.php?page=events-manager-options',  $_SERVER['REQUEST_URI'].$dismiss_link_joiner.'disable_hello_to_user=true');
			?>
			<div id="message" class="updated">
				<?php echo $advice; ?>
			</div>
			<?php
		}	
		
		//Image upload folders
		if( is_admin() && EM_IMAGE_DS == '/' ){
			$errs = array();
			if( is_writable(EM_IMAGE_UPLOAD_DIR) || @mkdir(EM_IMAGE_UPLOAD_DIR, 0777)){
				if( !is_writable(EM_IMAGE_UPLOAD_DIR.'/events/') && !@mkdir(EM_IMAGE_UPLOAD_DIR."events/", 0777) ){ $errs[] = 'events'; }
				if( !is_writable(EM_IMAGE_UPLOAD_DIR.'/locations/') && !@mkdir(EM_IMAGE_UPLOAD_DIR."locations/", 0777) ){ $errs[] = 'locations'; }
				if( !is_writable(EM_IMAGE_UPLOAD_DIR.'/categories/') && !@mkdir(EM_IMAGE_UPLOAD_DIR."categories/", 0777) ){ $errs[] = 'categories'; }
			}elseif( !is_writable(EM_IMAGE_UPLOAD_DIR) ){
				$errs = array('events','categories','locations');
			}
			if( count($errs) > 0 ){
			  	?>
				<div class="updated">
					<p><?php echo sprintf(__('The upload directory '.EM_IMAGE_UPLOAD_DIR.' is must be present with these writeable folders: %s. Please create these folders with the same write permissions you use for your normal wordpress image upload folders.','dbem'),implode(', ',$errs)); ?></p>
				</div>
				<?php
			}
		}
	
		//If events page couldn't be created
		if( !empty($_GET['em_dismiss_admin_notice']) ){
			delete_option('dbem_admin_notice_'.$_GET['em_dismiss_admin_notice']);
		}else{
			if ( get_option('dbem_admin_notice_3.0.91') ){
				?>
				<div class="updated">
					<p><?php echo sprintf ( __( '<strong>Events Manager has some new features!</strong><ul><li>Bookings can now be approved before they count towards your event\'s space allocations.</li><li>Events now have owners, and you can restrict users so they can only manage events/locations/categories they create.<br/><br/>These new permissions are enabled by default, but since you upgraded it has been disabled to maintain the previous plugin behaviour. You can re-enable it from the <a href="%s">settings page</a>. <a href="%s">Dismiss</a>', 'dbem'), get_bloginfo ( 'url' ) . '/wp-admin/admin.php?page=events-manager-options', $_SERVER['REQUEST_URI'].$dismiss_link_joiner.'em_dismiss_admin_notice=3.0.91' ); ?></p>
				</div>
				<?php		
			}
		}
	
		//If events page couldn't be created
		if( !empty($_GET['em_dismiss_events_page']) ){
			update_option('dbem_dismiss_events_page',1);
		}else{
			if ( !get_page($events_page_id) && !get_option('dbem_dismiss_events_page') ){
				?>
				<div id="em_page_error" class="updated">
					<p><?php echo sprintf ( __( 'Uh Oh! For some reason wordpress could not create an events page for you (or you just deleted it). Not to worry though, all you have to do is create an empty page, name it whatever you want, and select it as your events page in your <a href="%s">settings page</a>. Sorry for the extra step! If you know what you are doing, you may have done this on purpose, if so <a href="%s">ignore this message</a>', 'dbem'), get_bloginfo ( 'url' ) . '/wp-admin/admin.php?page=events-manager-options', $_SERVER['REQUEST_URI'].$dismiss_link_joiner.'em_dismiss_events_page=1' ); ?></p>
				</div>
				<?php		
			}
		}
		//If events page couldn't be created
		if( !empty($_GET['em_dismiss_notice_migrate_v3']) ){
			delete_option('em_notice_migrate_v3');
		}else{
			if( get_option('em_notice_migrate_v3') ){
				?>
				<div id="em_page_error" class="updated">
					<p><?php echo sprintf ( __( 'A <strong>LOT</strong> has changed since Events Manager 3. We recommend you take a look at the <a href="%s">settings page</a> for new features and upgrade instructions, and you may particualarly be interested in modifying permissions. <a href="%s">Dismiss</a>' ), 'admin.php?page=events-manager-options',  em_add_get_params($_SERVER['REQUEST_URI'], array('em_dismiss_notice_migrate_v3'=>1))); ?></p>
				</div>
				<?php		
			}
		}
		
		if( !empty($_REQUEST['action']) && $_REQUEST['action'] == 'em_rc_reimport' && wp_verify_nonce($_REQUEST['_wpnonce'], 'em_rc_reimport') ){
			require_once( dirname(__FILE__).'/../em-install.php');
			em_migrate_v3();
			?>
			<div id="em_page_error" class="updated">
				<p>Reimporting old settings was successful. Click the dismiss button on the other notification if after checking things are now working.</p>
			</div>
			<?php
		}
		
		if( defined('EMP_VERSION') && EMP_VERSION < EM_PRO_MIN_VERSION ){?>
			<div id="em_page_error" class="updated">
				<p>There is a newer version of Events Manager Pro which is required for this current version of Events Manager. Please go to the plugin website and download the latest update.</p>
			</div>
			<?php
		}
	}
	//Warn about EM page edit
	if ( preg_match( '/(post|page).php/', $_SERVER ['SCRIPT_NAME']) && isset ( $_GET ['action'] ) && $_GET ['action'] == 'edit' && isset ( $_GET ['post'] ) && $_GET ['post'] == "$events_page_id") {
		$message = sprintf ( __ ( "This page corresponds to <strong>Events Manager</strong> events page. Its content will be overriden by Events Manager, although if you include the word CONTENTS (exactly in capitals) and surround it with other text, only CONTENTS will be overwritten. If you want to change the way your events look, go to the <a href='%s'>settings</a> page. ", 'dbem' ), 'admin.php?page=events-manager-options' );
		$notice = "<div class='error'><p>$message</p></div>";
		echo $notice;
	}
	
}
add_action ( 'admin_notices', 'em_admin_warnings' );

/**
 * Creates a wp-admin style navigation. All this does is wrap some html around the em_paginate function result to make it style correctly in the admin area.
 * @param string $link
 * @param int $total
 * @param int $limit
 * @param int $page
 * @param int $pagesToShow
 * @return string
 * @uses em_paginate()
 */
function em_admin_paginate($total, $limit, $page=1, $vars=false){				
	$return = '<div class="tablenav-pages">';
	$events_nav = paginate_links( array(
		'base' => add_query_arg( 'pno', '%#%' ),
		'format' => '',
		'total' => ceil($total / $limit),
		'current' => $page,
		'add_args' => $vars
	));
	$return .= sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s', 'dbem') . ' </span>%s',
						number_format_i18n( ( $page - 1 ) * $limit + 1 ),
						number_format_i18n( min( $page * $limit, $total ) ),
						number_format_i18n( $total ),
						$events_nav
						);
	$return .= '</div>';
	return apply_filters('em_admin_paginate',$return,$total,$limit,$page,$vars);
}

/**
 * Called by admin_print_scripts-(hook|page) action, created when adding menu items in events-manager.php
 */
function em_admin_load_scripts(){
	//Load the UI items, currently date picker and autocomplete plus dependencies
	//wp_enqueue_script('em-ui-js', WP_PLUGIN_URL.'/events-manager/includes/js/jquery-ui-1.8.5.custom.min.js', array('jquery', 'jquery-ui-core'));
	wp_enqueue_script('events-manager', WP_PLUGIN_URL.'/events-manager/includes/js/events-manager.js', array('jquery', 'jquery-ui-core','jquery-ui-widget','jquery-ui-position'));
	
	//Time Entry
	wp_enqueue_script('em-timeentry', WP_PLUGIN_URL.'/events-manager/includes/js/timeentry/jquery.timeentry.js', array('jquery'));
	
	if( is_admin() ){
		//TinyMCE Editor
		remove_filter('the_editor',	'qtrans_modifyRichEditor'); //qtranslate filter
		if( function_exists('wp_tiny_mce')) add_action( 'admin_print_footer_scripts', 'wp_tiny_mce', 25 );
		if( function_exists('wp_tiny_mce_preload_dialogs')) add_action( 'admin_print_footer_scripts', 'wp_tiny_mce_preload_dialogs', 30 );
		wp_enqueue_script('post');
		if ( user_can_richedit() )
			wp_enqueue_script('editor');
		
		add_thickbox();
		wp_enqueue_script('media-upload');
		wp_enqueue_script('word-count');
		wp_enqueue_script('quicktags');
	}	
	em_js_localize_vars();
}

/**
 * Called by admin_print_styles-(hook|page) action, created when adding menu items in events-manager.php  
 */
function em_admin_load_styles() {
	add_thickbox();
	wp_enqueue_style('em-ui-css', WP_PLUGIN_URL.'/events-manager/includes/css/jquery-ui-1.8.13.custom.css');
	wp_enqueue_style('events-manager-admin', WP_PLUGIN_URL.'/events-manager/includes/css/events_manager_admin.css');
}

/**
 * Loads script inline due to insertion of php values 
 */
function em_admin_general_script() {
	//TODO clean script up, remove dependency of php so it can be moved to js file.	
	// Check if the locale is there and loads it
	$locale_code = substr ( get_locale (), 0, 2 );	
	$show24Hours = 'true';
	// Setting 12 hours format for those countries using it
	if (preg_match ( "/en|sk|zh|us|uk/", $locale_code ))
		$show24Hours = 'false';
	?>
	<script type="text/javascript">
	 	//<![CDATA[        
	   // TODO: make more general, to support also latitude and longitude (when added)	
		jQuery(document).ready( function($) {
		 	$("#start-time").timeEntry({spinnerImage: '', show24Hours: <?php echo $show24Hours; ?> });
			$("#end-time").timeEntry({spinnerImage: '', show24Hours: <?php echo $show24Hours; ?>});
		});
		//]]>
	</script>
	<?php
}
?>