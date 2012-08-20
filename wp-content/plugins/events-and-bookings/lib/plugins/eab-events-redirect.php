<?php
/*
Plugin Name: Event Controlled Redirect
Description: Redirects visitor from a selected page, post or event of the website (given with its ID) to any external or internal url, if event is progressing at the moment.
Plugin URI: http://premium.wpmudev.org/project/events-and-booking
Version: 0.25
Author: Hakan Evin
*/

/*
Detail: This Addon will add two fields to each Event: <b>ID of source page</b> and <b>URL of the target</b>. If the Event is progressing, any visitor visiting page with set page/post/event ID will be redirected to the set URL. Source page does not need to be the same as the event page. The Addon also provides two Global fields on this page which will be used if the related field(s) on Event page is left empty.

*/

class Eab_Events_EventControlledRedirect {

	private $_data;

	/**
	 * Constructor
	 */	
	private function __construct () {
		$this->_data = Eab_Options::get_instance();
	}

	/**
	 * Run the Addon
	 *
	 */	
	public static function serve () {
		$me = new Eab_Events_EventControlledRedirect;
		$me->_add_hooks();
	}

	/**
	 * Hooks to the main plugin Events+
	 *
	 */	
	private function _add_hooks () {
		add_action('eab-settings-after_payment_settings', array(&$this, 'show_settings'));
		add_action('template_redirect', array(&$this, 'redirect'));
		add_action('eab-event_meta-save_meta', array(&$this, 'save_redirect_meta'));
		add_filter('eab-event_meta-event_meta_box-after', array( &$this, 'event_meta_box'));
		add_filter('eab-settings-before_save', array(&$this,'save_settings'));
		add_action('admin_notices', array(&$this, 'warn_admin'));
	}
	
	function redirect() {
		global $post, $wpdb;
		
		/* Find if this page is selected to be redirected.
		   For optimization reasons, at this moment we assume that target url is defined
		   We will check it later
		*/
		$results = $wpdb->get_results("SELECT post_id FROM ". $wpdb->postmeta." WHERE meta_key='eab_events_redirect_source' AND meta_value='".$post->ID ."' "); 
		
		if ( $results ) {
			$query = '';
			foreach ( $results as $result )
				$query .= $this->generate_query( $result->post_id ). " UNION "; // UNION is faster than OR

			$this->finalize_redirect( rtrim( $query, "UNION ") ); // get rid of the last UNION
		}
		// Let's check if a global source ID is set, but source is not set for this event
		else if ( $global_post_id = $this->_data->get_option('global_redirect_source') && $global_post_id == $post->ID ) 
			$this->finalize_redirect( $this->generate_query( ) ); // Check all active events now

		return; // No match, no redirect.
	}

	/**
	 * Helper function to generate the query
	 */	
	function generate_query( $post_id=0 ) {
		global $wpdb;
		if ( $post_id )
			$add_query = "wposts.ID=".$post_id." ";
		else
			$add_query = "esource.meta_key='eab_events_redirect_source' AND esource.meta_value <> ''";
			
		$local_now = "DATE_ADD(UTC_TIMESTAMP(),INTERVAL ". ( current_time('timestamp') - time() ). " SECOND)";
		
		//$this->log( $local_now );
			
		return "SELECT wposts.* 
				FROM $wpdb->posts wposts, $wpdb->postmeta estart, $wpdb->postmeta eend, $wpdb->postmeta estatus, $wpdb->postmeta esource 
				WHERE ". $add_query . "
				AND wposts.ID=estart.post_id AND wposts.ID=eend.post_id AND wposts.ID=estatus.post_id 
				AND estart.meta_key='incsub_event_start' AND estart.meta_value < $local_now
				AND eend.meta_key='incsub_event_end' AND eend.meta_value > $local_now
				AND estatus.meta_key='incsub_event_status' AND estatus.meta_value <> 'closed'
				AND wposts.post_type='incsub_event' AND wposts.post_status='publish'";
	}

	/**
	 * Save a message in the log file
	 */	
	function log( $message='' ) {
		// Don't give warning if folder is not writable
		@file_put_contents( WP_PLUGIN_DIR. "/events-and-bookings/log.txt", $message . chr(10). chr(13), FILE_APPEND ); 
	}

	/**
	 * Helper function to redirect, if conditions are met
	 */	
	function finalize_redirect( $query ) {
		global $wpdb;
		$rows = $wpdb->get_results( $query );
		if ( $rows ) {
			$global_target = $this->_data->get_option('global_redirect_target');
			foreach ( $rows as $event ) {
				if ( $url = get_post_meta( $event->ID, 'eab_events_redirect_target', true ) )
					wp_redirect( $url ); // Normally target url should have been defined and we would redirect the user on first match.
				else if ( $global_target )
					wp_redirect( $global_target );
				exit;
			}
		}
		return;
	}


	/**
	 * Warn admin if source ID is not valid or source=target
	 */
	function warn_admin() {
		global $post;
		if ( !is_object( $post ) )
			return;
		if( !$permalink = get_permalink( get_post_meta($post->ID, 'eab_events_redirect_source', true ) ) ) {
			echo '<div class="error"><p>' .
				__("This is not a valid source ID.", Eab_EventsHub::TEXT_DOMAIN) .
			'</p></div>';
		}
		else if ( $permalink == get_post_meta($post->ID, 'eab_events_redirect_target', true ) ) {
			echo '<div class="error"><p>' .
				__("Target and source point the same page. This will result in an endless redirection loop.", Eab_EventsHub::TEXT_DOMAIN) .
			'</p></div>';
		}
	}

	/**
	 * Save post meta
	 *
	 */	
	function _save_meta ($post_id, $REQUEST) {
		if (isset($REQUEST['incsub_event_redirect_source']) ) {
			if ( trim( $REQUEST['incsub_event_redirect_source'] ) != '' )
				update_post_meta($post_id, 'eab_events_redirect_source', trim($REQUEST['incsub_event_redirect_source']));
			else
				delete_post_meta($post_id, 'eab_events_redirect_source');
		}
		if (isset($REQUEST['incsub_event_redirect_target']) ) {
			if ( trim( $REQUEST['incsub_event_redirect_target'] ) != '' )
				update_post_meta($post_id, 'eab_events_redirect_target', trim($REQUEST['incsub_event_redirect_target']));
			else
				delete_post_meta($post_id, 'eab_events_redirect_target');
		}
	}
	function save_redirect_meta ($post_id) {
		$this->_save_meta($post_id, $_POST);	
	}
	
	/**
	 * Add HTML codes to the event meta box
	 *
	 */	
	function event_meta_box( $content ) {
		global $post;
		
		$source = get_post_meta( $post->ID, 'eab_events_redirect_source', true );
		$target = get_post_meta( $post->ID, 'eab_events_redirect_target', true );
	
		$content .= '<div class="eab_meta_box">';
		$content .= '<input type="hidden" name="incsub_event_redirect_meta" value="1" />';
		$content .= '<div class="misc-eab-section">';
		$content .= '<div class="eab_meta_column_box">'.__('Event Controlled Redirect', Eab_EventsHub::TEXT_DOMAIN).'</div>';
		$content .= '<label for="incsub_event_redirect_source" id="incsub_event_redirect_source_label">'.__('ID of source page ', Eab_EventsHub::TEXT_DOMAIN).':</label>&nbsp;';
		$content .= '<input type="text" name="incsub_event_redirect_source" id="incsub_event_redirect_source" class="incsub_event" value="'.$source.'" size="5" /> ';
		$content .= '<div class="clear"></div>';
		$content .= '<label for="incsub_event_redirect_target" id="incsub_event_redirect_targer_label">'.__('URL of the target ', Eab_EventsHub::TEXT_DOMAIN).':</label>&nbsp;';
		$content .= '<input type="text" name="incsub_event_redirect_target" id="incsub_event_redirect_target" class="incsub_event" value="'.$target.'" /> ';
		$content .= '<div class="clear"></div>';
		$content .= '</div>';
		$content .= '</div>';
	
		return $content;
	
	}

	 
	/**
	 * Add Addon settings to the other admin options to be saved
	 */	
	function save_settings( $options ) {
		$options['global_redirect_source']		= stripslashes($_POST['event_default']['global_redirect_source']);
		$options['global_redirect_target']		= stripslashes($_POST['event_default']['global_redirect_target']);
		
		return $options;
	}
	
	/**
	 * Admin settings
	 *
	 */	
	function show_settings() {
		if (!class_exists('WpmuDev_HelpTooltips')) 
			require_once dirname(__FILE__) . '/lib/class_wd_help_tooltips.php';
		$tips = new WpmuDev_HelpTooltips();
		$tips->set_icon_url(plugins_url('events-and-bookings/img/information.png'));
		?>
		<div id="eab-settings-paypal" class="eab-metabox postbox">
				<h3 class="eab-hndle"><?php _e('Event Controlled Redirect settings :', Eab_EventsHub::TEXT_DOMAIN); ?></h3>
				<div class="eab-inside">
					<div class="eab-settings-settings_item">
					    <label for="incsub_event-global_redirect_source" ><?php _e('Global source page ID', Eab_EventsHub::TEXT_DOMAIN); ?></label>
						<input type="text" size="10" name="event_default[global_redirect_source]" value="<?php print $this->_data->get_option('global_redirect_source'); ?>" />
						<span><?php echo $tips->add_tip(__('If you enter an ID here all events which do NOT have a source page ID setting will use this setting.', Eab_EventsHub::TEXT_DOMAIN)); ?></span>
					</div>
					    
					<div class="eab-settings-settings_item">
					    <label for="incsub_event-global_redirect_target" ><?php _e('Global target url', Eab_EventsHub::TEXT_DOMAIN); ?></label>
						<input type="text" size="40" name="event_default[global_redirect_target]" value="<?php print $this->_data->get_option('global_redirect_target'); ?>" />
						<span><?php echo $tips->add_tip(__('If you enter an url here all events which do NOT have a target url setting will use this setting.', Eab_EventsHub::TEXT_DOMAIN)); ?></span>
					</div>
					
					    
				</div>
		    </div>
		<?php
	}
}

Eab_Events_EventControlledRedirect::serve();