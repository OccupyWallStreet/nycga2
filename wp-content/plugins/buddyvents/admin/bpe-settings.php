<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */

// No direct access is allowed
if( ! defined( 'ABSPATH' ) ) exit;

class Buddyvents_Admin_Settings extends Buddyvents_Admin_Core
{
	private $filepath;
	
	/**
	 * Constructor
	 * 
	 * @package Admin
	 * @since 	1.4
	 */
    public function __construct()
	{
		$this->filepath = admin_url( add_query_arg( array( 'page' => EVENT_FOLDER .'-settings' ), 'admin.php' ) );
		
		parent::__construct();
    }

	/**
	 * Return all the options
	 * 
	 * @package Admin
	 * @since 	1.4
	 */
	private function page_options()
	{
		return apply_filters( 'bpe_admin_settings_page_options', array(
			'week_start', 'enable_address',	'map_location',	'map_zoom_level', 'main_event_forum',
			'map_type',	'restrict_creation', 'clock_type', 'slugs', 'img', 'enable_forums',
			'system', 'field_id', 'timestamp', 'enable_achievements', 'enable_facebook_pages',
			'tab_order', 'deactivated_tabs', 'approve_events', 'enable_directions', 'invoice_tax',
			'map_lang', 'localize_months', 'default_view', 'enable_api', 'enable_cubepoints',
			'restrict_api_hits', 'restrict_api_timespan', 'enable_twitter', 'twitter_consumer_key',
			'twitter_consumer_secret', 'bitly_login', 'bitly_key', 'enable_facebook', 'logo',
			'facebook_appid', 'facebook_secret', 'use_event_images', 'enable_eventbrite',
			'eventbrite_appkey', 'enable_webhooks', 'enable_schedules', 'enable_documents',
			'enable_attendees', 'geonames_username', 'enable_logo', 'enable_invites', 'enable_ical',
			'enable_tickets', 'commission_percent', 'allowed_currencies', 'enable_sandbox',
			'enable_invoices', 'invoice_footer1', 'invoice_footer2', 'invoice_footer3', 'enable_groups',
			'invoice_footer4', 'invoice_message', 'invoice_settle_date', 'use_fullcalendar',
			'paypal_email', 'enable_manual_attendees', 'group_contact_required', 'disable_warnings',
			'enable_newsletter', 'enable_mailchimp', 'enable_cmonitor', 'enable_aweber',
			'enable_bp_gallery'
		) );
	}

	/**
	 * Content of the General Options tab
	 * 
	 * @package Admin
	 * @since 	1.4
	 */
    protected function content()
	{
        global $bpe, $bp, $wpdb;

		$lat = ( ! bpe_get_option( 'map_location', 'lat' ) ) ? 5 : bpe_get_option( 'map_location', 'lat' );
		$lng = ( ! bpe_get_option( 'map_location', 'lng' ) ) ? 30 : bpe_get_option( 'map_location', 'lng' );
		$zoom = ( ! bpe_get_option( 'map_zoom_level' ) ) ? 2 : bpe_get_option( 'map_zoom_level' );
		$type = ( ! bpe_get_option( 'map_type' ) ) ? 'HYBRID' : bpe_get_option( 'map_type' );

		$field_ids = $wpdb->get_results( $wpdb->prepare( "SELECT id, name FROM {$bp->profile->table_name_fields} WHERE parent_id = 0" ) );
    	?>

        <form name="general" id="bpe-form" method="post" enctype="multipart/form-data" action="<?php echo $this->filepath ?>" >
        
            <?php wp_nonce_field( 'bpe_settings' ) ?>
            <?php wp_nonce_field( 'bpe_reorder_tabs', '_wpnonce_reorder_tabs', false ); ?>
            
          	<input type="hidden" name="page_options" value="<?php echo esc_attr( implode( ',', $this->page_options() ) ) ?>" />

            <a class="button" id="toggle-slugs" href="#"><?php _e( 'Toggle Slugs', 'events' ); ?></a>
            
            <table id="bpe-slugs" class="form-table">
            <tr>
            	<th colspan="6"><span class="description"><?php _e( 'Only lowercase, a-z,0-9, plus some special characters like - (all slugs get sanitized automatically). Do not use the same slug more than once!', 'events' ); ?></span></th>
            </tr>
            <tr>
                <th><label for="main-slug"><?php _e( 'Main', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="main-slug" name="slugs[slug]" value="<?php echo esc_attr( bpe_get_option( 'slug' ) ) ?>" />
                </td>
                <th class="divide"><label for="active_slug"><?php _e( 'Active', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="active_slug" name="slugs[active_slug]" value="<?php echo esc_attr( bpe_get_option( 'active_slug' ) ) ?>" />
                </td>
                <th class="divide"><label for="archive_slug"><?php _e( 'Archive', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="archive_slug" name="slugs[archive_slug]" value="<?php echo esc_attr( bpe_get_option( 'archive_slug' ) ) ?>" />
                </td>
                <th class="divide"><label for="attending_slug"><?php _e( 'Attending', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="attending_slug" name="slugs[attending_slug]" value="<?php echo esc_attr( bpe_get_option( 'attending_slug' ) ) ?>" />
                </td>
            </tr>
            <tr>
                <th><label for="calendar_slug"><?php _e( 'Calendar', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="calendar_slug" name="slugs[calendar_slug]" value="<?php echo esc_attr( bpe_get_option( 'calendar_slug' ) ) ?>" />
                </td>
                <th class="divide"><label for="category_slug"><?php _e( 'Category', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="category_slug" name="slugs[category_slug]" value="<?php echo esc_attr( bpe_get_option( 'category_slug' ) ) ?>" />
                </td>
                <th class="divide"><label for="map_slug"><?php _e( 'Map', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="map_slug" name="slugs[map_slug]" value="<?php echo esc_attr( bpe_get_option( 'map_slug' ) ) ?>" />
                </td>
                <th class="divide"><label for="create_slug"><?php _e( 'Create', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="create_slug" name="slugs[create_slug]" value="<?php echo esc_attr( bpe_get_option( 'create_slug' ) ) ?>" />
                </td>
            </tr>
            <tr>
                <th><label for="invite_slug"><?php _e( 'Invite', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="invite_slug" name="slugs[invite_slug]" value="<?php echo esc_attr( bpe_get_option( 'invite_slug' ) ) ?>" />
                </td>
                <th class="divide"><label for="edit_slug"><?php _e( 'Edit', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="edit_slug" name="slugs[edit_slug]" value="<?php echo esc_attr( bpe_get_option( 'edit_slug' ) ) ?>" />
                </td>
                <th class="divide"><label for="attendee_slug"><?php _e( 'Attendee', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="attendee_slug" name="slugs[attendee_slug]" value="<?php echo esc_attr( bpe_get_option( 'attendee_slug' ) ) ?>" />
                </td>
                <th class="divide"><label for="directions_slug"><?php _e( 'Directions', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="directions_slug" name="slugs[directions_slug]" value="<?php echo esc_attr( bpe_get_option( 'directions_slug' ) ) ?>" />
                </td>
            </tr>
            <tr>
                <th><label for="month_slug"><?php _e( 'Month', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="month_slug" name="slugs[month_slug]" value="<?php echo esc_attr( bpe_get_option( 'month_slug' ) ) ?>" />
                </td>
                <th class="divide"><label for="day_slug"><?php _e( 'Day', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="day_slug" name="slugs[day_slug]" value="<?php echo esc_attr( bpe_get_option( 'day_slug' ) ) ?>" />
                </td>
                <th class="divide"><label for="schedule_slug"><?php _e( 'Schedule', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="schedule_slug" name="slugs[schedule_slug]" value="<?php echo esc_attr( bpe_get_option( 'schedule_slug' ) ) ?>" />
                </td>
                <th class="divide"><label for="view_slug"><?php _e( 'View', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="view_slug" name="slugs[view_slug]" value="<?php echo esc_attr( bpe_get_option( 'view_slug' ) ) ?>" />
                </td>
            </tr>
            <tr>
                <th><label for="list_slug"><?php _e( 'List', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="list_slug" name="slugs[list_slug]" value="<?php echo esc_attr( bpe_get_option( 'list_slug' ) ) ?>" />
                </td>
                <th class="divide"><label for="grid_slug"><?php _e( 'Grid', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="grid_slug" name="slugs[grid_slug]" value="<?php echo esc_attr( bpe_get_option( 'grid_slug' ) ) ?>" />
                </td>
                <th class="divide"><label for="feed_slug"><?php _e( 'Feed', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="feed_slug" name="slugs[feed_slug]" value="<?php echo esc_attr( bpe_get_option( 'feed_slug' ) ) ?>" />
                </td>
                <th class="divide"><label for="search_slug"><?php _e( 'Search', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="search_slug" name="slugs[search_slug]" value="<?php echo esc_attr( bpe_get_option( 'search_slug' ) ) ?>" />
                </td>
            </tr>
            <tr>
                <th><label for="results_slug"><?php _e( 'Results', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="results_slug" name="slugs[results_slug]" value="<?php echo esc_attr( bpe_get_option( 'results_slug' ) ) ?>" />
                </td>
                <th class="divide"><label for="api_slug"><?php _e( 'API', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="api_slug" name="slugs[api_slug]" value="<?php echo esc_attr( bpe_get_option( 'api_slug' ) ) ?>" />
                </td>
                <th class="divide"><label for="api_key_slug"><?php _e( 'API Key', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="api_key_slug" name="slugs[api_key_slug]" value="<?php echo esc_attr( bpe_get_option( 'api_key_slug' ) ) ?>" />
                </td>
                <th class="divide"><label for="documents_slug"><?php _e( 'Documents', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="documents_slug" name="slugs[documents_slug]" value="<?php echo esc_attr( bpe_get_option( 'documents_slug' ) ) ?>" />
                </td>
            </tr>
            <tr>
                <th><label for="step_slug"><?php _e( 'Step', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="step_slug" name="slugs[step_slug]" value="<?php echo esc_attr( bpe_get_option( 'step_slug' ) ) ?>" />
                </td>
                <th class="divide"><label for="general_slug"><?php _e( 'General', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="general_slug" name="slugs[general_slug]" value="<?php echo esc_attr( bpe_get_option( 'general_slug' ) ) ?>" />
                </td>
                <th class="divide"><label for="logo_slug"><?php _e( 'Logo', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="logo_slug" name="slugs[logo_slug]" value="<?php echo esc_attr( bpe_get_option( 'logo_slug' ) ) ?>" />
                </td>
                <th class="divide"><label for="manage_slug"><?php _e( 'Manage', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="manage_slug" name="slugs[manage_slug]" value="<?php echo esc_attr( bpe_get_option( 'manage_slug' ) ) ?>" />
                </td>
            </tr>
            <tr>
                <th><label for="timezone_slug"><?php _e( 'Timezone', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="timezone_slug" name="slugs[timezone_slug]" value="<?php echo esc_attr( bpe_get_option( 'timezone_slug' ) ) ?>" />
                </td>
                <th class="divide"><label for="venue_slug"><?php _e( 'Venue', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="venue_slug" name="slugs[venue_slug]" value="<?php echo esc_attr( bpe_get_option( 'venue_slug' ) ) ?>" />
                </td>
                <th class="divide"><label for="tickets_slug"><?php _e( 'Tickets', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="tickets_slug" name="slugs[tickets_slug]" value="<?php echo esc_attr( bpe_get_option( 'tickets_slug' ) ) ?>" />
                </td>
                <th class="divide"><label for="invoice_slug"><?php _e( 'Invoices', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="invoice_slug" name="slugs[invoice_slug]" value="<?php echo esc_attr( bpe_get_option( 'invoice_slug' ) ) ?>" />
                </td>
            </tr>
            <tr>
                <th><label for="approve_slug"><?php _e( 'Approve', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="approve_slug" name="slugs[approve_slug]" value="<?php echo esc_attr( bpe_get_option( 'approve_slug' ) ) ?>" />
                </td>
                <th class="divide"><label for="checkout_slug"><?php _e( 'Checkout', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="checkout_slug" name="slugs[checkout_slug]" value="<?php echo esc_attr( bpe_get_option( 'checkout_slug' ) ) ?>" />
                </td>
                <th class="divide"><label for="cancel_slug"><?php _e( 'Cancel', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="cancel_slug" name="slugs[cancel_slug]" value="<?php echo esc_attr( bpe_get_option( 'cancel_slug' ) ) ?>" />
                </td>
                <th class="divide"><label for="success_slug"><?php _e( 'Success', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="success_slug" name="slugs[success_slug]" value="<?php echo esc_attr( bpe_get_option( 'success_slug' ) ) ?>" />
                </td>
            </tr>
            <tr>
                <th><label for="sales_slug"><?php _e( 'Sales', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="sales_slug" name="slugs[sales_slug]" value="<?php echo esc_attr( bpe_get_option( 'sales_slug' ) ) ?>" />
                </td>
                <th class="divide"><label for="signup_slug"><?php _e( 'Signup', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="signup_slug" name="slugs[signup_slug]" value="<?php echo esc_attr( bpe_get_option( 'signup_slug' ) ) ?>" />
                </td>
                <th class="divide"><label for="forum_slug"><?php _e( 'Forum', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="forum_slug" name="slugs[forum_slug]" value="<?php echo esc_attr( bpe_get_option( 'forum_slug' ) ) ?>" />
                </td>
                <th class="divide"><label for="topic_slug"><?php _e( 'Topic', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="topic_slug" name="slugs[topic_slug]" value="<?php echo esc_attr( bpe_get_option( 'topic_slug' ) ) ?>" />
                </td>
            </tr>
            <tr>
                <th><label for="reply_slug"><?php _e( 'Reply', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="reply_slug" name="slugs[reply_slug]" value="<?php echo esc_attr( bpe_get_option( 'reply_slug' ) ) ?>" />
                </td>
                <th class="divide"><label for="forum_tag_slug"><?php _e( 'Forum Tag', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="forum_tag_slug" name="slugs[forum_tag_slug]" value="<?php echo esc_attr( bpe_get_option( 'forum_tag_slug' ) ) ?>" />
                </td>
                <th class="divide"><label for="newsletter_slug"><?php _e( 'Newsletter', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="newsletter_slug" name="slugs[newsletter_slug]" value="<?php echo esc_attr( bpe_get_option( 'newsletter_slug' ) ) ?>" />
                </td>
                <!--
                <th class="divide"><label for="gallery_slug"><?php _e( 'Gallery', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="gallery_slug" name="slugs[gallery_slug]" value="<?php echo esc_attr( bpe_get_option( 'gallery_slug' ) ) ?>" />
                </td>
                -->
                <th class="divide">&nbsp;</th><td>&nbsp;</td>
            </tr>
        </table>
        
        <hr />

        <table id="bpe-settings" class="form-table">
            <tr>
                <th><label for="disable_warnings"><?php _e( 'Disable Warnings', 'events' ); ?></label></th>
                <td>
 					<input type="checkbox" id="disable_warnings" name="disable_warnings"<?php if( bpe_get_option( 'disable_warnings' ) === true ) echo ' checked="checked"'; ?> value="true" /> <?php _e( 'Check to disable plugin warnings.', 'events' ); ?>
                </td>
            </tr>
            <tr>
                <th><label for="enable_logo"><?php _e( 'Enable Logo', 'events' ); ?></label></th>
                <td>
 					<input type="checkbox" id="enable_logo" class="enable_ext_api" name="enable_logo"<?php if( bpe_get_option( 'enable_logo' ) === true ) echo ' checked="checked"'; ?> value="true" /> <?php _e( 'Check to enable event logos.', 'events' ); ?>
                </td>
            </tr>
            <tr class="logo_hide hide_indent">
                <th><label for="img"><?php _e( 'Default Logo', 'events' ); ?></label></th>
                <td>
					<img id="default-avatar" src="<?php echo ( bpe_get_option( 'default_avatar', 'mid' ) ) ? esc_url( bp_get_root_domain() . bpe_get_option( 'default_avatar', 'mid' ) ) : esc_url( bpe_get_config( 'default_logo' ) ) ; ?>" alt="" width="150" height="150" />
   					<input type="file" id="img" name="img" /> <?php if( bpe_get_option( 'default_avatar', 'mid' ) ) : ?><input type="submit" name="del_default_avatar" class="button-secondary"  value="<?php _e( 'Delete Logo','events' ) ;?> &raquo;"/><?php endif; ?><br />
    				<span class="description"><?php _e( 'You can upload one image (png,gif or jpg).<br />The image will be cropped to a square.<br />Image needs to be larger than 150x150 px.', 'events' ) ?></span>
                </td>
            </tr>
            <tr class="logo_hide hide_indent">
                <th><label for="use_event_images"><?php _e( 'Map Icons', 'events' ); ?></label></th>
                <td>
 					<input type="checkbox" id="use_event_images" name="use_event_images"<?php if( bpe_get_option( 'use_event_images' ) === true ) echo ' checked="checked"'; ?> value="true" /> <?php _e( 'Check to use event logos as map icons', 'events' ); ?>
                </td>
            </tr>
            <tr>
                <th><label for="field_id"><?php _e( 'Location Field ID', 'events' ); ?></label></th>
                <td>
                    <select id="field_id" name="field_id">
                    	<option value="">----</option>
                    	<?php foreach( $field_ids as $key => $val ) { ?>
                        <option value="<?php echo $val->id ?>" <?php selected( $val->id, bpe_get_option( 'field_id' ) ) ?>><?php echo esc_attr( $val->name ) ?></option>
                        <?php } ?>
                    </select>
    				<span class="description"><?php _e( 'Will be ignored, if <a href="http://shop.shabushabu.eu/plugins/mapology.html">Mapology</a> is activated.', 'events' ) ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="system"><?php _e( 'System', 'events' ); ?></label></th>
                <td>
                    <select id="system" name="system">
                    	<option value="">----</option>
                        <option value="km" <?php selected( 'km', bpe_get_option( 'system' ) ) ?>><?php _e( 'Metric', 'events' ); ?></option>
                        <option value="m" <?php selected( 'm', bpe_get_option( 'system' ) ) ?>><?php _e( 'Imperial', 'events' ); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="default_view"><?php _e( 'Default View', 'events' ); ?></label></th>
                <td>
                    <select id="default_view" name="default_view">
                        <?php foreach( (array) bpe_get_config( 'view_styles' ) as $view ) : ?>
                        <option value="<?php echo $view ?>" <?php selected( $view, bpe_get_option( 'default_view' ) ) ?>><?php echo ucwords( $view ) ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <?php if( bp_is_active( 'settings' ) ) : ?>
            <tr>
                <th><label for="timestamp"><?php _e( 'Reminder Emails', 'events' ); ?></label></th>
                <td>
                    <select id="timestamp" name="timestamp">
                    	<option value="">----</option>
                        <option value="-1 day" <?php selected( '-1 day', bpe_get_option( 'timestamp' ) ) ?>><?php _e( '1 day before', 'events' ); ?></option>
                        <option value="-12 hours" <?php selected( '-12 hours', bpe_get_option( 'timestamp' ) ) ?>><?php _e( '12 hours before', 'events' ); ?></option>
                        <option value="-11 hours" <?php selected( '-11 hours', bpe_get_option( 'timestamp' ) ) ?>><?php _e( '11 hours before', 'events' ); ?></option>
                        <option value="-10 hours" <?php selected( '-10 hours', bpe_get_option( 'timestamp' ) ) ?>><?php _e( '10 hours before', 'events' ); ?></option>
                        <option value="-9 hours" <?php selected( '-9 hours', bpe_get_option( 'timestamp' ) ) ?>><?php _e( '9 hours before', 'events' ); ?></option>
                        <option value="-8 hours" <?php selected( '-8 hours', bpe_get_option( 'timestamp' ) ) ?>><?php _e( '8 hours before', 'events' ); ?></option>
                        <option value="-7 hours" <?php selected( '-7 hours', bpe_get_option( 'timestamp' ) ) ?>><?php _e( '7 hours before', 'events' ); ?></option>
                        <option value="-6 hours" <?php selected( '-6 hours', bpe_get_option( 'timestamp' ) ) ?>><?php _e( '6 hours before', 'events' ); ?></option>
                        <option value="-5 hours" <?php selected( '-5 hours', bpe_get_option( 'timestamp' ) ) ?>><?php _e( '5 hours before', 'events' ); ?></option>
                        <option value="-4 hours" <?php selected( '-4 hours', bpe_get_option( 'timestamp' ) ) ?>><?php _e( '4 hours before', 'events' ); ?></option>
                        <option value="-3 hours" <?php selected( '-3 hours', bpe_get_option( 'timestamp' ) ) ?>><?php _e( '3 hours before', 'events' ); ?></option>
                        <option value="-2 hours" <?php selected( '-2 hours', bpe_get_option( 'timestamp' ) ) ?>><?php _e( '2 hours before', 'events' ); ?></option>
                        <option value="-1 hour" <?php selected( '-1 hour', bpe_get_option( 'timestamp' ) ) ?>><?php _e( '1 hour before', 'events' ); ?></option>
                        <?php do_action( 'bpe_add_timestamp_reminder' ) ?>
                    </select>
                    <span class="description"><?php _e( 'Select when reminder emails should be sent out.', 'events' ) ?></span>
                </td>
            </tr>
            <?php endif; ?>
            <tr>
                <th><label for="enable_manual_attendees"><?php _e( 'Adding Attendees manually', 'events' ); ?></label></th>
                <td>
 					<input type="checkbox" id="enable_manual_attendees" name="enable_manual_attendees"<?php if( bpe_get_option( 'enable_manual_attendees' ) === true ) echo ' checked="checked"'; ?> value="true" /> <?php _e( 'Let event admins manually add users. Will be active automatically if tickets are enabled.', 'events' ); ?>
                </td>
            </tr>
            <tr>
                <th><label for="enable_attendees"><?php _e( 'Enable Attendees', 'events' ); ?></label></th>
                <td>
 					<input type="checkbox" id="enable_attendees" name="enable_attendees"<?php if( bpe_get_option( 'enable_attendees' ) === true ) echo ' checked="checked"'; ?> value="true" /> <?php _e( 'Check to enable event attendees.', 'events' ); ?>
                </td>
            </tr>
            <?php
            if( function_exists( 'bbp_get_version' ) ) :
				if( version_compare( bbp_get_version(), Buddyvents::BBPRESS_VERSION, '>=' ) == true ) :
				?>
	            <tr>
	                <th><label for="enable_forums"><?php _e( 'Enable Forums', 'events' ); ?></label></th>
	                <td>
	 					<input type="checkbox" id="enable_forums" class="enable_ext_api" name="enable_forums"<?php if( bpe_get_option( 'enable_forums' ) === true ) echo ' checked="checked"'; ?> value="true" /> <?php _e( 'Check to enable event forums. BBPress (plugin version) needs to be active.', 'events' ); ?>
	                </td>
	            </tr>
	            <tr class="forums_hide hide_indent">
	                <th><label for="main_event_forum"><?php _e( 'Root Events Forum', 'events' ); ?></label></th>
	                <td>
					    <?php
						bbp_dropdown( array(
							'selected'           => bpe_get_option( 'main_event_forum' ),
							'show_none'          => __( '(Event Forum Root)', 'events' ),
							'select_id'          => 'main_event_forum',
							'disable_categories' => true
						) );
						?>
	                    <span class="description"><?php _e( 'Pick the parent forum for all event forums.', 'events' ); ?></span>
	                </td>
	            </tr>
	            <?php
				endif;
            endif;
            ?>
            <?php if( bp_is_active( 'settings' ) ) : ?>
            <tr>
                <th><label for="enable_tickets"><?php _e( 'Enable Tickets', 'events' ); ?></label></th>
                <td>
 					<input type="checkbox" id="enable_tickets" class="enable_ext_api" name="enable_tickets"<?php if( bpe_get_option( 'enable_tickets' ) === true ) echo ' checked="checked"'; ?> value="true" /> <?php _e( 'Check to enable event tickets.', 'events' ); ?>
                </td>
            </tr>
            <tr class="tickets_hide hide_indent">
                <th><label for="commission_percent"><?php _e( 'Commission', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="commission_percent" name="commission_percent" value="<?php echo esc_attr( bpe_get_option( 'commission_percent' ) ) ?>" />%
                    <span class="description"><?php _e( 'Your comisssion (in percent) on any ticket sales. Leave empty or set to 0 to disable.', 'events' ); ?></span>
                </td>
            </tr>
            <tr class="tickets_hide hide_indent">
                <th><label for="enable_sandbox"><?php _e( 'Enable PayPal Sandbox', 'events' ); ?></label></th>
                <td>
 					<input type="checkbox" id="enable_sandbox" name="enable_sandbox"<?php if( bpe_get_option( 'enable_sandbox' ) === true ) echo ' checked="checked"'; ?> value="true" />
 					<?php printf(__( 'Check to enable PayPal Sandbox testing. You will also have to setup the IPNs in your PayPal account. Use %s as the IPN URL and %s as the return URL.', 'events' ), bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/ipn/', bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/return/' ); ?>
                </td>
            </tr>
            <tr class="tickets_hide hide_indent">
                <th><label for="paypal_email"><?php _e( 'PayPal Email', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="paypal_email" class="large-text" name="paypal_email" value="<?php echo esc_attr( bpe_get_option( 'paypal_email' ) ) ?>" /> <span class="description"><?php _e( 'Enter your primary PayPal email address.', 'events' ); ?></span>
                </td>
            </tr>
            <tr class="tickets_hide hide_indent">
                <th><label for="allowed_currencies"><?php _e( 'Allowed Currencies', 'events' ); ?></label></th>
                <td class="allcur">
                	<?php foreach( bpe_ticket_currencies() as $code => $name ) : ?>
						<label for="allowed_currencies-<?php echo strtolower( $code ) ?>">
                        	<input<?php if( in_array( $code, (array)bpe_get_option( 'allowed_currencies' ) ) ) echo ' checked="checked"'; ?> type="checkbox" name="allowed_currencies[]" id="allowed_currencies-<?php echo strtolower( esc_attr( $code ) ) ?>" value="<?php echo esc_attr( $code ) ?>" />
							<?php echo $name ?>
                        </label>
                    <?php endforeach; ?>
                    <div class="clear"></div>
                    <span class="description"><?php _e( 'You can pick and choose which currencies you allow event admins to use.', 'events' ); ?></span>
                </td>
            </tr>
            <tr class="tickets_hide hide_indent">
                <th><label for="enable_invoices"><?php _e( 'Enable Invoices', 'events' ); ?></label></th>
                <td>
 					<input type="checkbox" id="enable_invoices" class="enable_ext_api" name="enable_invoices"<?php if( bpe_get_option( 'enable_invoices' ) === true ) echo ' checked="checked"'; ?> value="true" /> <?php _e( 'Check to enable invoices.', 'events' ); ?>
                </td>
            </tr>
            <tr class="tickets_hide invoices_hide hide_indent hide_indent_twice">
                <th><label for="logo"><?php _e( 'Invoice Logo', 'events' ); ?></label></th>
                <td>
					<input type="file" id="logo" name="logo" /> <?php if( bpe_get_option( 'invoice_logo', 'url' ) ) : ?><input type="submit" name="del_invoice_logo" class="button-secondary"  value="<?php _e( 'Delete Invoice Logo','events' ) ;?> &raquo;"/><?php endif; ?><br />
    				<span class="description"><?php _e( 'You can upload one image to use as an Invoice logo (png,gif or jpg).<br />The image will not be cropped.', 'events' ) ?></span>
                    <?php if( bpe_get_option( 'invoice_logo', 'url' ) ) : ?>
                    <div class="clear"></div>
					<img src="<?php echo esc_url( bp_get_root_domain() . bpe_get_option( 'invoice_logo', 'url' ) ); ?>" alt="" width="<?php echo esc_attr( bpe_get_option( 'invoice_logo', 'width' ) ) ?>" height="<?php echo esc_attr( bpe_get_option( 'invoice_logo', 'height' ) ) ?>" />
					<?php endif; ?>
                </td>
            </tr>
            <tr class="tickets_hide invoices_hide hide_indent hide_indent_twice">
                <th><label for="invoice_tax"><?php _e( 'VAT', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="invoice_tax" name="invoice_tax" value="<?php echo esc_attr( bpe_get_option( 'invoice_tax' ) ) ?>" />%
                </td>
            </tr>
            <tr class="tickets_hide invoices_hide hide_indent hide_indent_twice">
                <th><label for="invoice_settle_date"><?php _e( 'Days to settle', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="invoice_settle_date" name="invoice_settle_date" value="<?php echo esc_attr( bpe_get_option( 'invoice_settle_date' ) ) ?>" />
    				<span class="description"><?php _e( 'After how many days you want an invoice to get settled.', 'events' ) ?></span>
                </td>
            </tr>
            <tr class="tickets_hide invoices_hide hide_indent hide_indent_twice">
                <th><label for="invoice_message"><?php _e( 'Invoice Message', 'events' ); ?></label></th>
                <td>
 					<textarea class="large-text" rows="5" id="invoice_message" name="invoice_message"><?php echo esc_textarea( bpe_get_option( 'invoice_message' ) ) ?></textarea>
    				<span class="description"><?php _e( 'Allowed placeholder: {SETTLE_DATE}. The above option will be determined to get the correct date.', 'events' ) ?></span>
                </td>
            </tr>
            <tr class="tickets_hide invoices_hide hide_indent hide_indent_twice">
                <th><label for="invoice_footer1"><?php _e( 'Invoice Footer 1', 'events' ); ?></label></th>
                <td>
 					<textarea class="large-text" rows="5" id="invoice_footer1" name="invoice_footer1"><?php echo esc_textarea( bpe_get_option( 'invoice_footer1' ) ) ?></textarea>
                </td>
            </tr>
            <tr class="tickets_hide invoices_hide hide_indent hide_indent_twice">
                <th><label for="invoice_footer2"><?php _e( 'Invoice Footer 2', 'events' ); ?></label></th>
                <td>
 					<textarea class="large-text" rows="5" id="invoice_footer2" name="invoice_footer2"><?php echo esc_textarea( bpe_get_option( 'invoice_footer2' ) ) ?></textarea>
                </td>
            </tr>
            <tr class="tickets_hide invoices_hide hide_indent hide_indent_twice">
                <th><label for="invoice_footer3"><?php _e( 'Invoice Footer 3', 'events' ); ?></label></th>
                <td>
 					<textarea class="large-text" rows="5" id="invoice_footer3" name="invoice_footer3"><?php echo esc_textarea( bpe_get_option( 'invoice_footer3' ) ) ?></textarea>
                </td>
            </tr>
            <tr class="tickets_hide invoices_hide hide_indent hide_indent_twice">
                <th><label for="invoice_footer4"><?php _e( 'Invoice Footer 4', 'events' ); ?></label></th>
                <td>
 					<textarea class="large-text" rows="5" id="invoice_footer4" name="invoice_footer4"><?php echo esc_textarea( bpe_get_option( 'invoice_footer4' ) ) ?></textarea>
                </td>
            </tr>
            <tr class="tickets_hide invoices_hide hide_indent hide_indent_twice">
                <th><label for="test_invoice"><?php _e( 'Test Invoice', 'events' ); ?></label></th>
                <td>
                	<input type="submit" name="create_test_invoice" class="button-secondary"  value="<?php _e( 'Preview PDF','events' ) ;?> &raquo;"/>
                    <span class="description"><?php printf( __( 'After all the information has been filled in, click here to create a test invoice. You might also want to fill in your <a href="%s">billing address</a>.', 'events' ), bp_core_get_user_domain( bp_loggedin_user_id() ) . bp_get_settings_slug() .'/'. bpe_get_base( 'slug' ) .'/' ) ?></span>
                </td>
            </tr>
            <?php endif; ?>
            <tr>
                <th><label for="enable_invites"><?php _e( 'Enable Invites', 'events' ); ?></label></th>
                <td>
 					<input type="checkbox" id="enable_invites" name="enable_invites"<?php if( bpe_get_option( 'enable_invites' ) === true ) echo ' checked="checked"'; ?> value="true" /> <?php _e( 'Check to enable event invites.', 'events' ); ?>
                </td>
            </tr>
            <tr>
                <th><label for="enable_ical"><?php _e( 'Enable iCal Downloads', 'events' ); ?></label></th>
                <td>
  					<input type="radio" id="enable_ical_1" name="enable_ical"<?php if( bpe_get_option( 'enable_ical' ) == 1 ) echo ' checked="checked"'; ?> value="1" /> <?php _e( 'Always allow iCal Downloads.', 'events' ) ?><br />
 					<input type="radio" id="enable_ical_2" name="enable_ical"<?php if( bpe_get_option( 'enable_ical' ) == 2 ) echo ' checked="checked"'; ?> value="2" /> <?php _e( 'Allow iCal downloads for logged in users only.', 'events' ) ?><br />
 					<input type="radio" id="enable_ical_3" name="enable_ical"<?php if( bpe_get_option( 'enable_ical' ) == 3 ) echo ' checked="checked"'; ?> value="3" /> <?php _e( 'Only allow event members to download iCal files.', 'events' ) ?><br />
   					<input type="radio" id="enable_ical_4" name="enable_ical"<?php if( bpe_get_option( 'enable_ical' ) == 4 ) echo ' checked="checked"'; ?> value="4" /> <?php _e( 'Disable iCal downloads completely', 'events' ) ?>
                </td>
            </tr>
            <tr>
                <th><label for="enable_documents"><?php _e( 'Enable Documents', 'events' ); ?></label></th>
                <td>
  					<input type="radio" id="enable_documents_1" name="enable_documents"<?php if( bpe_get_option( 'enable_documents' ) == 1 ) echo ' checked="checked"'; ?> value="1" /> <?php _e( 'Always show documents.', 'events' ) ?><br />
 					<input type="radio" id="enable_documents_2" name="enable_documents"<?php if( bpe_get_option( 'enable_documents' ) == 2 ) echo ' checked="checked"'; ?> value="2" /> <?php _e( 'Show documents to logged in users only.', 'events' ) ?><br />
 					<input type="radio" id="enable_documents_3" name="enable_documents"<?php if( bpe_get_option( 'enable_documents' ) == 3 ) echo ' checked="checked"'; ?> value="3" /> <?php _e( 'Show documents only to event members.', 'events' ) ?><br />
   					<input type="radio" id="enable_documents_4" name="enable_documents"<?php if( bpe_get_option( 'enable_documents' ) == 4 ) echo ' checked="checked"'; ?> value="4" /> <?php _e( 'Disable documents completely', 'events' ) ?>
                </td>
            </tr>
            <tr>
                <th><label for="enable_schedules"><?php _e( 'Enable Schedules', 'events' ); ?></label></th>
                <td>
  					<input type="radio" id="enable_schedules_1" name="enable_schedules"<?php if( bpe_get_option( 'enable_schedules' ) == 1 ) echo ' checked="checked"'; ?> value="1" /> <?php _e( 'Always show schedules.', 'events' ) ?><br />
 					<input type="radio" id="enable_schedules_2" name="enable_schedules"<?php if( bpe_get_option( 'enable_schedules' ) == 2 ) echo ' checked="checked"'; ?> value="2" /> <?php _e( 'Show directions to logged in users only.', 'events' ) ?><br />
 					<input type="radio" id="enable_schedules_3" name="enable_schedules"<?php if( bpe_get_option( 'enable_schedules' ) == 3 ) echo ' checked="checked"'; ?> value="3" /> <?php _e( 'Show schedules only to event members.', 'events' ) ?><br />
   					<input type="radio" id="enable_schedules_4" name="enable_schedules"<?php if( bpe_get_option( 'enable_schedules' ) == 4 ) echo ' checked="checked"'; ?> value="4" /> <?php _e( 'Disable schedules completely', 'events' ) ?>
               </td>
            </tr>
            <tr>
                <th><label for="enable_directions"><?php _e( 'Event Directions', 'events' ); ?></label></th>
                <td>
 					<input type="radio" id="enable_directions_1" name="enable_directions"<?php if( bpe_get_option( 'enable_directions' ) == 1 ) echo ' checked="checked"'; ?> value="1" /> <?php _e( 'Always show directions.', 'events' ) ?><br />
 					<input type="radio" id="enable_directions_2" name="enable_directions"<?php if( bpe_get_option( 'enable_directions' ) == 2 ) echo ' checked="checked"'; ?> value="2" /> <?php _e( 'Show directions to logged in users only.', 'events' ) ?><br />
 					<input type="radio" id="enable_directions_3" name="enable_directions"<?php if( bpe_get_option( 'enable_directions' ) == 3 ) echo ' checked="checked"'; ?> value="3" /> <?php _e( 'Show directions only to event members.', 'events' ) ?><br />
 					<input type="radio" id="enable_directions_4" name="enable_directions"<?php if( bpe_get_option( 'enable_directions' ) == 4 ) echo ' checked="checked"'; ?> value="4" /> <?php _e( 'Disable directions completely.', 'events' ) ?>
                </td>
            </tr>
            <?php if( bp_is_active( 'settings' ) ) : ?>
            <tr>
                <th><label for="enable_eventbrite"><?php _e( 'Enable Eventbrite', 'events' ); ?></label></th>
                <td>
 					<input type="checkbox" id="enable_eventbrite" class="enable_ext_api" name="enable_eventbrite"<?php if( bpe_get_option( 'enable_eventbrite' ) === true ) echo ' checked="checked"'; ?> value="true" /> <?php _e( 'Check to enable publishing new events to Eventbrite.', 'events' ); ?>
                </td>
            </tr>
            <tr class="eventbrite_hide hide_indent">
                <th><label for="eventbrite_appkey"><?php _e( 'Eventbrite App Key', 'events' ); ?></label></th>
                <td>
 					<input type="text" class="large-text" id="eventbrite_appkey" name="eventbrite_appkey" value="<?php echo esc_attr( bpe_get_option( 'eventbrite_appkey' ) ) ?>" /><br />
                    <span class="description"><?php _e( 'Enter your app key (get it <a href="http://www.eventbrite.com/api/key" target="_blank">here</a>).', 'events' ); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="enable_newsletter"><?php _e( 'Enable Newsletter Services', 'events' ); ?></label></th>
                <td>
 					<input type="checkbox" id="enable_newsletter" class="enable_ext_api" name="enable_newsletter"<?php if( bpe_get_option( 'enable_newsletter' ) === true ) echo ' checked="checked"'; ?> value="true" /> <?php _e( 'Check to enable newsletter subscription for event members.', 'events' ); ?>
                    <span class="description"><?php _e( 'The more services you enable, the more likely it is that there is a service that an event admin can utilze.', 'events' ); ?></span>
                </td>
            </tr>
            <tr class="newsletter_hide hide_indent">
                <th><label for="enable_mailchimp"><?php _e( 'Enable Mailchimp', 'events' ); ?></label></th>
                <td>
 					<input type="checkbox" id="enable_mailchimp" name="enable_mailchimp"<?php if( bpe_get_option( 'enable_mailchimp' ) === true ) echo ' checked="checked"'; ?> value="true" /> <?php _e( 'Check to enable Mailchimp lists.', 'events' ); ?>
                </td>
            </tr>
            <tr class="newsletter_hide hide_indent">
                <th><label for="enable_cmonitor"><?php _e( 'Enable Campaign Monitor', 'events' ); ?></label></th>
                <td>
 					<input type="checkbox" id="enable_cmonitor" name="enable_cmonitor"<?php if( bpe_get_option( 'enable_cmonitor' ) === true ) echo ' checked="checked"'; ?> value="true" /> <?php _e( 'Check to enable Campaign Monitor lists.', 'events' ); ?>
            	</td>
            </tr>
            <tr class="newsletter_hide hide_indent">
                <th><label for="enable_aweber"><?php _e( 'Enable AWeber', 'events' ); ?></label></th>
                <td>
 					<input type="checkbox" id="enable_aweber" name="enable_aweber"<?php if( bpe_get_option( 'enable_aweber' ) === true ) echo ' checked="checked"'; ?> value="true" /> <?php _e( 'Check to enable AWeber lists.', 'events' ); ?>
            	</td>
            </tr>
            <tr>
                <th><label for="enable_twitter"><?php _e( 'Enable Twitter', 'events' ); ?></label></th>
                <td>
 					<input type="checkbox" id="enable_twitter" class="enable_ext_api" name="enable_twitter"<?php if( bpe_get_option( 'enable_twitter' ) === true ) echo ' checked="checked"'; ?> value="true" /> <?php _e( 'Check to enable publishing new events to Twitter.', 'events' ); ?>
                </td>
            </tr>
            <tr class="twitter_hide bpe-twitter hide_indent">
                <th><label for="twitter_consumer_key"><?php _e( 'Twitter Consumer Key', 'events' ); ?></label></th>
                <td>
 					<input type="text" class="large-text" id="twitter_consumer_key" name="twitter_consumer_key" value="<?php echo esc_attr( bpe_get_option( 'twitter_consumer_key' ) ) ?>" />
                </td>
            </tr>
            <tr class="twitter_hide hide_indent">
                <th><label for="twitter_consumer_secret"><?php _e( 'Twitter Consumer Secret', 'events' ); ?></label></th>
                <td>
 					<input type="text" class="large-text" id="twitter_consumer_secret" name="twitter_consumer_secret" value="<?php echo esc_attr( bpe_get_option( 'twitter_consumer_secret' ) ) ?>" /><br />
                    <span class="description">
						<?php _e( 'Enter your consumer key and the corresponding consumer secret (get them <a href="http://dev.twitter.com/apps" target="_blank">here</a>).', 'events' ); ?><br />
						<?php _e( 'Setting up your app is easy. Enter an application name and a description.', 'events' ); ?><br />
                        <?php printf( __( 'Application Type has to be <i>Browser</i>. Use <i>%s</i> for both <i>Application Website</i> and <i>Callback URL</i>', 'events' ), esc_url( bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/' ) ); ?><br />
                        <?php _e( 'Check <i>Read & Write</i> for the Default Access type option, enter the captcha, then fill in the consumer key and secret you receive below!', 'events' ) ?>
					</span>
                </td>
            </tr>
            <tr>
                <th><label for="enable_facebook"><?php _e( 'Enable Facebook', 'events' ); ?></label></th>
                <td>
 					<input type="checkbox" id="enable_facebook" class="enable_ext_api" name="enable_facebook"<?php if( bpe_get_option( 'enable_facebook' ) === true ) echo ' checked="checked"'; ?> value="true" /> <?php _e( 'Check to enable publishing new events to Facebook.', 'events' ); ?>
                </td>
            </tr>
            <tr class="facebook_hide bpe-twitter hide_indent">
                <th><label for="enable_facebook_pages"><?php _e( 'Enable Facebook Pages', 'events' ); ?></label></th>
                <td>
 					<input type="checkbox" id="enable_facebook_pages" name="enable_facebook_pages"<?php if( bpe_get_option( 'enable_facebook_pages' ) === true ) echo ' checked="checked"'; ?> value="true" /> <?php _e( 'Check to let users post to their Facebook pages. Please note that Facebook restricts API calls for most applications to 10-25 per day per user.', 'events' ); ?>
                </td>
            </tr>
            <tr class="facebook_hide hide_indent">
                <th><label for="facebook_appid"><?php _e( 'Facebook App ID', 'events' ); ?></label></th>
                <td>
 					<input type="text" class="large-text" id="facebook_appid" name="facebook_appid" value="<?php echo esc_attr( bpe_get_option( 'facebook_appid' ) ) ?>" />
                </td>
            </tr>
            <tr class="facebook_hide hide_indent">
                <th><label for="facebook_secret"><?php _e( 'Facebook Secret', 'events' ); ?></label></th>
                <td>
 					<input type="text" class="large-text" id="facebook_secret" name="facebook_secret" value="<?php echo esc_attr( bpe_get_option( 'facebook_secret' ) ) ?>" /><br />
                    <span class="description">
						<?php _e( 'Enter your app id and the corresponding secret (get them <a href="http://developers.facebook.com/setup/" target="_blank">here</a>).', 'events' ); ?><br />
						<?php printf( __( 'Setting up your app is easy. Enter an application name and an url(use <i>%s</i>).', 'events' ), esc_url( bp_get_root_domain() .'/' ) ); ?><br />
						<?php _e( 'Fill in the captcha on the next page, then fill the App ID and the Secret in above.', 'events' ); ?><br />
					</span>
                </td>
            </tr>
            <tr class="bpe-twitter">
                <th><label for="bitly_login"><?php _e( 'Bit.ly Login', 'events' ); ?></label></th>
                <td>
 					<input type="text" class="large-text" id="bitly_login" name="bitly_login" value="<?php echo esc_attr( bpe_get_option( 'bitly_login' ) ) ?>" />
                </td>
            </tr>
            <tr>
                <th><label for="bitly_key"><?php _e( 'Bit.ly API Key', 'events' ); ?></label></th>
                <td>
 					<input type="text" class="large-text" id="bitly_key" name="bitly_key" value="<?php echo esc_attr( bpe_get_option( 'bitly_key' ) ) ?>" />
                    <span class="description"><?php _e( 'Enter your Bit.ly credentials to enable short urls when posting to Twitter or Facebook.', 'events' ) ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="enable_api"><?php _e( 'Enable API', 'events' ); ?></label></th>
                <td>
 					<input type="checkbox" id="enable_api" class="enable_ext_api" name="enable_api"<?php if( bpe_get_option( 'enable_api' ) === true ) echo ' checked="checked"'; ?> value="true" /> <?php _e( 'Check to enable the Buddyvvents API (Let\'s other sites show events by querying your database in a safe way).', 'events' ); ?>
                </td>
            </tr>
            <tr class="api_hide hide_indent">
                <th><label for="restrict_api_hits"><?php _e( 'API Hit Restriction', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="restrict_api_hits" name="restrict_api_hits" value="<?php echo esc_attr( bpe_get_option( 'restrict_api_hits' ) ) ?>" />
                    <span class="description"><?php _e( 'Amount of allowed hits per the setting below (API Time Restriction).', 'events' ) ?></span>
                </td>
            </tr>
            <tr class="api_hide hide_indent">
                <th><label for="restrict_api_timespan"><?php _e( 'API Time Restriction', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="restrict_api_timespan" name="restrict_api_timespan" value="<?php echo esc_attr( bpe_get_option( 'restrict_api_timespan' ) ) ?>" />
                    <span class="description"><?php _e( 'Amount of seconds for which hits are allowed per the setting above (API Hit Restriction).', 'events' ) ?></span>
                </td>
            </tr>
            <tr class="api_hide hide_indent">
                <th><label for="enable_webhooks"><?php _e( 'Enable Webhooks', 'events' ); ?></label></th>
                <td>
 					<input type="checkbox" id="enable_webhooks" name="enable_webhooks"<?php if( bpe_get_option( 'enable_webhooks' ) === true ) echo ' checked="checked"'; ?> value="true" /> <?php _e( 'Check to enable webhooks.', 'events' ); ?>
                </td>
            </tr>
            <?php endif; ?>
            <tr>
                <th><label for="geonames_username"><?php _e( 'Geonames Username', 'events' ); ?></label></th>
                <td>
 					<input type="text" class="large-text" id="geonames_username" name="geonames_username" value="<?php echo esc_attr( bpe_get_option( 'geonames_username' ) ) ?>" />
                    <span class="description"><?php _e( 'Enter your Geonames username to enable timezone support and location based archives (get it <a href="http://www.geonames.org/login" target="_blank">here</a>).<br />You will also have to enable access to the Geonames webservice in your Geonames account.', 'events' ); ?></span>
                </td>
            </tr>
            <?php if( bpe_get_option( 'geonames_username' ) ) : ?>
            <tr>
                <th><label for="get_timezone"><?php _e( 'Timezones', 'events' ); ?></label></th>
                <td>
 					<input type="submit" id="get_timezone" class="button-secondary" name="get_timezone" value="<?php _e( 'Save timezones', 'events' ) ?>" />
                    <span class="description"><?php _e( 'Use this option to get timezones for your existing events.', 'events' ); ?></span>
                </td>
            </tr>
            <?php endif; ?>
            <tr>
                <th><label for="use_fullcalendar"><?php _e( 'Use Fullcalendar', 'events' ); ?></label></th>
                <td>
 					<input type="checkbox" id="use_fullcalendar" name="use_fullcalendar"<?php if( bpe_get_option( 'use_fullcalendar' ) === true ) echo ' checked="checked"'; ?> value="true" /> <?php _e( 'Check to enable the jQuery <a href="http://arshaw.com/fullcalendar/">fullcalendar.js</a> script. Enabling this option will remove the old calendar. Use it or tell me why you wouldn\'t, it\'s got more features!', 'events' ); ?>
                </td>
            </tr>
            <tr>
                <th><label for="week_start"><?php _e( 'Calendar Week Start', 'events' ); ?></label></th>
                <td>
                    <select id="week_start" name="week_start">
                        <option value="1" <?php selected( '1', bpe_get_option( 'week_start' ) ) ?>><?php _e( 'Monday', 'events' ); ?></option>
                        <option value="7" <?php selected( '7', bpe_get_option( 'week_start' ) ) ?>><?php _e( 'Sunday', 'events' ); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="clock_type"><?php _e( 'Clock Type', 'events' ); ?></label></th>
                <td>
                    <select id="clock_type" name="clock_type">
                        <option value="24" <?php selected( '24', bpe_get_option( 'clock_type' ) ) ?>><?php _e( '24h', 'events' ); ?></option>
                        <option value="12" <?php selected( '12', bpe_get_option( 'clock_type' ) ) ?>><?php _e( '12h', 'events' ); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="enable_groups"><?php _e( 'Enable Groups Events', 'events' ); ?></label></th>
                <td>
 					<input type="checkbox" class="enable_ext_api" id="enable_groups" name="enable_groups"<?php if( bpe_get_option( 'enable_groups' ) === true ) echo ' checked="checked"'; ?> value="true" /> <?php _e( 'Check to enable group events.', 'events' ); ?>
                </td>
            </tr>
            <tr class="groups_hide hide_indent">
                <th><label for="enable_address"><?php _e( 'Group Contact Details', 'events' ); ?></label></th>
                <td>
 					<input type="checkbox" id="enable_address" name="enable_address"<?php if( bpe_get_option( 'enable_address' ) === true ) echo ' checked="checked"'; ?> value="true" /> <?php _e( 'Check to enable group contact details.', 'events' ); ?>
                </td>
            </tr>
            <tr class="groups_hide hide_indent">
                <th><label for="group_contact_required"><?php _e( 'Require Group Contact Details', 'events' ); ?></label></th>
                <td>
 					<input type="checkbox" id="group_contact_required" name="group_contact_required"<?php if( bpe_get_option( 'group_contact_required' ) === true ) echo ' checked="checked"'; ?> value="true" /> <?php _e( 'Check to make group contact details required.', 'events' ); ?>
                </td>
            </tr>            <tr>
                <th><label for="localize_months"><?php _e( 'Localize Month Names', 'events' ); ?></label></th>
                <td>
 					<input type="checkbox" id="localize_months" name="localize_months"<?php if( bpe_get_option( 'localize_months' ) === true ) echo ' checked="checked"'; ?> value="true" /> <?php _e( 'Check to localize month names.<br /><strong>IMPORTANT:</strong> This can cause problems if your site is hosted on a Windows server.', 'events' ); ?>
                </td>
            </tr>
            <tr>
                <th><label for="restrict_creation"><?php _e( 'Restrict Creation', 'events' ); ?></label></th>
                <td>
 					<input type="checkbox" id="restrict_creation" name="restrict_creation"<?php if( bpe_get_option( 'restrict_creation' ) === true ) echo ' checked="checked"'; ?> value="true" /> <?php _e( 'Check to restrict event creation to group admins.', 'events' ); ?>
                </td>
            </tr>
            <tr>
                <th><label for="approve_events"><?php _e( 'Approve Events', 'events' ); ?></label></th>
                <td>
 					<input type="checkbox" id="approve_events" name="approve_events"<?php if( bpe_get_option( 'approve_events' ) === true ) echo ' checked="checked"'; ?> value="true" /> <?php _e( 'Check to approve all events manually.', 'events' ); ?>
                </td>
            </tr>
            <tr>
                <th><label for="map_lang"><?php _e( 'Map Language', 'events' ); ?></label></th>
                <td>
                    <select id="map_lang" name="map_lang">
                    	<option value="">----</option>
                        <option value="&language=ar" <?php selected( '&language=ar', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Arabic', 'events' ); ?></option>	
                        <option value="&language=eu" <?php selected( '&language=eu', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Basque', 'events' ); ?></option>
                        <option value="&language=bg" <?php selected( '&language=bg', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Bulgarian', 'events' ); ?></option>
                        <option value="&language=bn" <?php selected( '&language=bn', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Bengali', 'events' ); ?></option>
                        <option value="&language=ca" <?php selected( '&language=ca', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Catalan', 'events' ); ?></option>
                        <option value="&language=cs" <?php selected( '&language=cs', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Czech', 'events' ); ?></option>
                        <option value="&language=da" <?php selected( '&language=da', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Danish', 'events' ); ?></option>
                        <option value="&language=de" <?php selected( '&language=de', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'German', 'events' ); ?></option>
                        <option value="&language=el" <?php selected( '&language=el', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Greek', 'events' ); ?></option>
                        <option value="&language=en" <?php selected( '&language=en', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'English', 'events' ); ?></option>
                        <option value="&language=en-AU" <?php selected( '&language=en-AU', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'English (Australian)', 'events' ); ?></option>
                        <option value="&language=en-GB" <?php selected( '&language=en-GB', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'English (UK)', 'events' ); ?></option>
                        <option value="&language=es" <?php selected( '&language=es', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Spanish', 'events' ); ?></option>
                        <option value="&language=fi" <?php selected( '&language=fi', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Finnish', 'events' ); ?></option>
                        <option value="&language=fil" <?php selected( '&language=fil', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Fillipino', 'events' ); ?></option>
                        <option value="&language=fr" <?php selected( '&language=fr', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'French', 'events' ); ?></option>
                        <option value="&language=gl" <?php selected( '&language=gl', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Galician', 'events' ); ?></option>
                        <option value="&language=gu" <?php selected( '&language=gu', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Gujarati', 'events' ); ?></option>
                        <option value="&language=hi" <?php selected( '&language=hi', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Hindi', 'events' ); ?></option>
                        <option value="&language=hr" <?php selected( '&language=hr', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Croatian', 'events' ); ?></option>
                        <option value="&language=hu" <?php selected( '&language=hu', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Hungarian', 'events' ); ?></option>
                        <option value="&language=id" <?php selected( '&language=id', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Indonesian', 'events' ); ?></option>
                        <option value="&language=it" <?php selected( '&language=it', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Italian', 'events' ); ?></option>
                        <option value="&language=iw" <?php selected( '&language=iw', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Hebrew', 'events' ); ?></option>
                        <option value="&language=ja" <?php selected( '&language=ja', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Japanese', 'events' ); ?></option>
                        <option value="&language=kn" <?php selected( '&language=kn', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Kannada', 'events' ); ?></option>
                        <option value="&language=ko" <?php selected( '&language=ko', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Korean', 'events' ); ?></option>
                        <option value="&language=lt" <?php selected( '&language=lt', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Lithuanian', 'events' ); ?></option>
                        <option value="&language=lv" <?php selected( '&language=lv', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Latvian', 'events' ); ?></option>
                        <option value="&language=ml" <?php selected( '&language=ml', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Malayalam', 'events' ); ?></option>
                        <option value="&language=mr" <?php selected( '&language=mr', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Marathi', 'events' ); ?></option>
                        <option value="&language=nl" <?php selected( '&language=nl', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Dutch', 'events' ); ?></option>
                        <option value="&language=no" <?php selected( '&language=no', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Norwegian', 'events' ); ?></option>
                        <option value="&language=pl" <?php selected( '&language=pl', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Polish', 'events' ); ?></option>
                        <option value="&language=pt" <?php selected( '&language=pt', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Portuguese', 'events' ); ?></option>
                        <option value="&language=pt-BR" <?php selected( '&language=pt-BR', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Portuguese (Brazil)', 'events' ); ?></option>
                        <option value="&language=pt-PT" <?php selected( '&language=pt-PT', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Portuguese (Portugal)', 'events' ); ?></option>
                        <option value="&language=ro" <?php selected( '&language=ro', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Romanian', 'events' ); ?></option>
                        <option value="&language=ru" <?php selected( '&language=ru', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Russian', 'events' ); ?></option>
                        <option value="&language=sk" <?php selected( '&language=sk', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Slovak', 'events' ); ?></option>
                        <option value="&language=sl" <?php selected( '&language=sl', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Slovenian', 'events' ); ?></option>
                        <option value="&language=sr" <?php selected( '&language=sr', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Serbian', 'events' ); ?></option>
                        <option value="&language=sv" <?php selected( '&language=sv', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Swedish', 'events' ); ?></option>
                        <option value="&language=tl" <?php selected( '&language=tl', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Tagalog', 'events' ); ?></option>
                        <option value="&language=ta" <?php selected( '&language=ta', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Tamil', 'events' ); ?></option>
                        <option value="&language=te" <?php selected( '&language=te', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Telugu', 'events' ); ?></option>
                        <option value="&language=th" <?php selected( '&language=th', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Thai', 'events' ); ?></option>
                        <option value="&language=tr" <?php selected( '&language=tr', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Turkish', 'events' ); ?></option>
                        <option value="&language=uk" <?php selected( '&language=uk', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Ukrainian', 'events' ); ?></option>
                        <option value="&language=vi" <?php selected( '&language=vi', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Vietnamese', 'events' ); ?></option>
                        <option value="&language=zh-CN" <?php selected( '&language=zh-CN', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Chinese (Simplified)', 'events' ); ?></option>
                        <option value="&language=zh-TW" <?php selected( '&language=zh-TW', bpe_get_option( 'map_lang' ) ) ?>><?php _e( 'Chinese (Traditional)', 'events' ); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="map_zoom_level"><?php _e( 'Map Zoom Level', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="map_zoom_level" name="map_zoom_level" value="<?php echo esc_attr( bpe_get_option( 'map_zoom_level' ) ) ?>" />
                </td>
            </tr>
            <tr>
                <th><label for="map_type"><?php _e( 'Map Type', 'events' ); ?></label></th>
                <td>
                    <select id="map_type" name="map_type">
                    	<option value="">----</option>
                        <option value="ROADMAP" <?php selected( 'ROADMAP', bpe_get_option( 'map_type' ) ) ?>>ROADMAP</option>
                        <option value="SATELLITE" <?php selected( 'SATELLITE', bpe_get_option( 'map_type' ) ) ?>>SATELLITE</option>
                        <option value="HYBRID" <?php selected( 'HYBRID', bpe_get_option( 'map_type' ) ) ?>>HYBRID</option>
                        <option value="TERRAIN" <?php selected( 'TERRAIN', bpe_get_option( 'map_type' ) ) ?>>TERRAIN</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="map_location"><?php _e( 'Default Map Location', 'events' ); ?></label></th>
                <td>
 					<input type="hidden" id="map_location_lat" name="map_location[lat]" value="<?php echo esc_attr( $lat ) ?>" />
                    <input type="hidden" id="map_location_lng" name="map_location[lng]" value="<?php echo esc_attr( $lng ) ?>" />
                    <div id="default-loc-map" style="width:600px;height:400px;"></div>
                </td>
            </tr>
            <tr>
                <th><label for="deactivated_tabs"><?php _e( 'Deactivate Tabs', 'events' ); ?></label></th>
                <td>
                	<label for="active-tab"><input class="deactivate-tab" type="checkbox"<?php if( bpe_get_option( 'deactivated_tabs', 'active' ) == 'active' ) echo ' checked="checked"'; ?><?php if( bpe_get_option( 'default_tab' ) == 'active' ) echo ' disabled="disabled"'; ?> id="active-tab" name="deactivated_tabs[active]" value="active" /> <?php _e( 'Active', 'events' ); ?></label>
                	<label for="archive-tab"><input class="deactivate-tab" type="checkbox"<?php if( bpe_get_option( 'deactivated_tabs', 'archive' ) == 'archive' ) echo ' checked="checked"'; ?><?php if( bpe_get_option( 'default_tab' ) == 'archive' ) echo ' disabled="disabled"'; ?> id="archive-tab" name="deactivated_tabs[archive]" value="archive" /> <?php _e( 'Archive', 'events' ); ?></label>
                	<label for="attending-tab"><input class="deactivate-tab" type="checkbox"<?php if( bpe_get_option( 'deactivated_tabs', 'attending' ) == 'attending' ) echo ' checked="checked"'; ?><?php if( bpe_get_option( 'default_tab' ) == 'attending' ) echo ' disabled="disabled"'; ?> id="attending-tab" name="deactivated_tabs[attending]" value="attending" /> <?php _e( 'Attending', 'events' ); ?></label>
                	<label for="calendar-tab"><input class="deactivate-tab" type="checkbox"<?php if( bpe_get_option( 'deactivated_tabs', 'calendar' ) == 'calendar' ) echo ' checked="checked"'; ?><?php if( bpe_get_option( 'default_tab' ) == 'calendar' ) echo ' disabled="disabled"'; ?> id="calendar-tab" name="deactivated_tabs[calendar]" value="calendar" /> <?php _e( 'Calendar', 'events' ); ?></label>
                	<label for="map-tab"><input class="deactivate-tab" type="checkbox"<?php if( bpe_get_option( 'deactivated_tabs', 'map' ) == 'map' ) echo ' checked="checked"'; ?><?php if( bpe_get_option( 'default_tab' ) == 'map' ) echo ' disabled="disabled"'; ?> id="map-tab" name="deactivated_tabs[map]" value="map" /> <?php _e( 'Map', 'events' ); ?></label>
                	<label for="search-tab"><input class="deactivate-tab" type="checkbox"<?php if( bpe_get_option( 'deactivated_tabs', 'search' ) == 'search' ) echo ' checked="checked"'; ?><?php if( bpe_get_option( 'default_tab' ) == 'search' ) echo ' disabled="disabled"'; ?> id="search-tab" name="deactivated_tabs[search]" value="search" /> <?php _e( 'Search', 'events' ); ?></label>
                	<label for="invoices-tab"><input class="deactivate-tab" type="checkbox"<?php if( bpe_get_option( 'deactivated_tabs', 'invoices' ) == 'invoices' ) echo ' checked="checked"'; ?><?php if( bpe_get_option( 'default_tab' ) == 'invoices' ) echo ' disabled="disabled"'; ?> id="search-tab" name="deactivated_tabs[invoices]" value="invoices" /> <?php _e( 'Invoices', 'events' ); ?></label>
                	<label for="create-tab"><input class="deactivate-tab" type="checkbox"<?php if( bpe_get_option( 'deactivated_tabs', 'create' ) == 'create' ) echo ' checked="checked"'; ?><?php if( bpe_get_option( 'default_tab' ) == 'create' ) echo ' disabled="disabled"'; ?> id="create-tab" name="deactivated_tabs[create]" value="create" /> <?php _e( 'Create', 'events' ); ?></label>
                    <br /><span class="description"><?php _e( 'The default tab cannot be deactivated.', 'events' ); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="tab_order"><?php _e( 'Menu Tab Order', 'events' ); ?></label></th>
                <td>
					<ul id="menu-tabs">
                    	<?php foreach( (array)bpe_get_option( 'backend_order' ) as $tab => $order ) :
							$tabs[] = $tab;	?>
							<li id="<?php echo $tab ?>">
								<?php
                                echo __( ucfirst( $tab ), 'events' );
								if( $tab == 'attending' )
									echo __( ' (does only appear on profile pages - cannot be default tab)', 'events' );
								if( $tab == 'invoices' )
									echo __( ' (only available if tickets are enabled - cannot be default tab)', 'events' );
								if( $tab == 'create' )
									echo __( ' (only visible on member and group pages - cannot be default tab)', 'events' );
								?>
							</li>
                    	<?php endforeach; ?>
                    </ul>
                    <span class="description"><?php _e( 'Drag and drop the menu tabs up or down to reorder them. The template files will reflect these changes.', 'events' ); ?></span>
                    <input type="hidden" name="tab_order" id="tab_order" value="<?php echo esc_attr( implode( ',', (array)$tabs ) ) ?>" />
                </td>
            </tr>
            <?php if( defined( 'ACHIEVEMENTS_IS_INSTALLED' ) ) : ?>
            <tr>
                <th><label for="enable_achievements"><?php _e( 'Enable Achievements support', 'events' ); ?></label></th>
                <td>
 					<input type="checkbox" id="enable_achievements" name="enable_achievements"<?php if( bpe_get_option( 'enable_achievements' ) === true ) echo ' checked="checked"'; ?> value="true" /> <?php _e( 'Check to enable support for the achievements plugin.', 'events' ); ?>
                </td>
            </tr>
            <?php endif; ?>
            <!--
            <?php if( defined( 'BP_GALLERY_IS_INSTALLED' ) ) : ?>
            <tr>
                <th><label for="enable_bp_gallery"><?php _e( 'Enable BP Gallery Integration', 'events' ); ?></label></th>
                <td>
 					<input type="checkbox" id="enable_bp_gallery" name="enable_bp_gallery"<?php if( bpe_get_option( 'enable_bp_gallery' ) === true ) echo ' checked="checked"'; ?> value="true" /> <?php _e( 'Check to enable BP Gallery integration.', 'events' ); ?>
                </td>
            </tr>
            <?php endif; ?>
            -->
            <?php if( defined( 'CP_VER' ) && defined( 'BP_CUBEPOINT_VERSION' ) ) : ?>
            <tr>
                <th><label for="enable_cubepoints"><?php _e( 'Enable Cubepoints Integration', 'events' ); ?></label></th>
                <td>
 					<input type="checkbox" id="enable_cubepoints" class="enable_ext_api" name="enable_cubepoints"<?php if( bpe_get_option( 'enable_cubepoints' ) === true ) echo ' checked="checked"'; ?> value="true" /> <?php _e( 'Check to enable Cubepoints integration.', 'events' ); ?>
                </td>
            </tr>
            <tr class="cubepoints_hide hide_indent">
                <th><label for="cp_create_event"><?php _e( 'Event Creation Points', 'events' ); ?></label></th>
                <td>
 					<input type="text hide_indent" id="cp_create_event" name="cp_create_event" value="<?php echo esc_attr( bpe_get_option( 'cp_create_event' ) ) ?>" />
                    <span class="description"><?php _e( 'Points given for creating an event.', 'events' ) ?></span>
                </td>
            </tr>
            <tr class="cubepoints_hide hide_indent">
                <th><label for="cp_delete_event"><?php _e( 'Event Deletion Points', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="cp_delete_event" name="cp_delete_event" value="<?php echo esc_attr( bpe_get_option( 'cp_delete_event' ) ) ?>" />
                    <span class="description"><?php _e( 'Points deducted for deleting an event.', 'events' ) ?></span>
                </td>
            </tr>
            <tr class="cubepoints_hide hide_indent">
                <th><label for="cp_attend_event"><?php _e( 'Attending Event Points', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="cp_attend_event" name="cp_attend_event" value="<?php echo esc_attr( bpe_get_option( 'cp_attend_event' ) ) ?>" />
                    <span class="description"><?php _e( 'Points given for attending an event.', 'events' ) ?></span>
                </td>
            </tr>
            <tr class="cubepoints_hide hide_indent">
                <th><label for="cp_remove_event"><?php _e( 'Not Attending Event Points', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="cp_remove_event" name="cp_remove_event" value="<?php echo esc_attr( bpe_get_option( 'cp_remove_event' ) ) ?>" />
                    <span class="description"><?php _e( 'Points deducted for not attending an event.', 'events' ) ?></span>
                </td>
            </tr>
            <tr class="cubepoints_hide hide_indent">
                <th><label for="cp_maybe_attend_event"><?php _e( 'Maybe Attending Event Points', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="cp_maybe_attend_event" name="cp_maybe_attend_event" value="<?php echo esc_attr( bpe_get_option( 'cp_maybe_attend_event' ) ) ?>" />
                    <span class="description"><?php _e( 'Points given for maybe attending an event.', 'events' ) ?></span>
                </td>
            </tr>
            <tr class="cubepoints_hide hide_indent">
                <th><label for="cp_maybe_remove_event"><?php _e( 'Not Attending Event Points (maybe)', 'events' ); ?></label></th>
                <td>
 					<input type="text" id="cp_maybe_remove_event" name="cp_maybe_remove_event" value="<?php echo esc_attr( bpe_get_option( 'cp_maybe_remove_event' ) ) ?>" />
                    <span class="description"><?php _e( 'Points deducted for not attending an event (maybe).', 'events' ) ?></span>
                </td>
            </tr>
            <?php endif; ?>
            <tr>
                <th><label for="import"><?php _e( 'Import Settings', 'events' ); ?></label></th>
                <td>
                	<input type="file" id="import" name="import" />
                	<input type="submit" id="import_settings" name="import_settings" class="button-secondary"  value="<?php _e( 'Import','events' ) ;?> &raquo;"/>
                    <span class="description"><?php _e( 'Only valid JSON settings files can be imported here.', 'events' ) ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="export"><?php _e( 'Export Settings', 'events' ); ?></label></th>
                <td>
                	<a id="export" href="<?php echo admin_url( '/admin.php?page=buddyvents-settings&export_settings=1' ) ?>" class="button"><?php _e( 'Export','events' ) ;?> &raquo;</a>
                    <span class="description"><?php _e( 'Your settings will get exported as a JSON file.', 'events' ) ?></span>
                </td>
            </tr>
            <?php do_action( 'bpe_admin_add_settings' ) ?>
			</table>
            <div class="submit">
            	<input type="submit" class="button-primary" name="update_bpe_options" value="<?php _e( 'Update' ) ;?> &raquo;"/>
                <input type="submit" id="reset-options" name="reset_options" onclick="javascript:check=confirm('<?php echo esc_js( __( "Do you really want to reset the options?\n\nChoose [Cancel] to Stop, [OK] to proceed.\n", 'events' ) ); ?>');if(check==false) return false;" value="<?php _e( 'Reset Options','events' ) ;?> &raquo;"/>
            </div>
        </form>
		<script type="text/javascript">
		var mapType = '<?php echo esc_js( $type ) ?>';
		var mapZoom = <?php echo esc_js( $zoom ) ?>;
		var mapLat = <?php echo esc_js( $lat ) ?>;
        var mapLng = <?php echo esc_js( $lng ) ?>;
        </script>
        <?php	
	}
}
?>