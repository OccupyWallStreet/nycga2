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

class Buddyvents_Core
{
	/**
	 * Initialize the core component
	 *
	 * @package	 Core
	 * @since 	 2.0
	 */
	 public static function init()
	{
		add_action( 'init', 				array( __CLASS__, 'includes' 			 ),	 0 );
		add_action( 'bp_setup_globals', 	array( __CLASS__, 'setup_globals' 		 ),	10 );
		add_action( 'bp_setup_title', 		array( __CLASS__, 'setup_title'			 ),	10 );
		add_action( 'bp_setup_nav', 		array( __CLASS__, 'setup_nav'			 ),	10 );
		add_action( 'bp_adminbar_menus', 	array( __CLASS__, 'setup_adminbar_menu'  ), 20 );
		
		add_filter( 'bp_directory_pages', 	array( __CLASS__, 'add_loaded_component' ), 10 );
	}
	
	/**
	 * Include relevant files
	 *
	 * @package	 Core
	 * @since 	 2.0
	 */
	public static function includes()
	{
		$files = array(
			'core/bpe-helpers',					'core/models/bpe-events',
			'core/models/bpe-members',			'core/models/bpe-categories',
			'core/models/bpe-notifications',	'core/models/bpe-meta',
			'core/templatetags/bpe-events',		'core/bpe-cleanup',
			'core/bpe-options',					'core/bpe-process',
			'core/bpe-create-edit',				'core/bpe-conditionals',
			'core/bpe-filters',					'core/bpe-search',
			'core/bpe-upload',					'core/bpe-template',
			'core/bpe-oembed',					'core/bpe-js-css',
			'core/bpe-db',						'core/bpe-widgets',
			'core/bpe-shortcodes',				'core/bpe-calendar',
			'core/bpe-feeds',					'core/bpe-notifications',
			'core/bpe-recurrence',				'core/bpe-ajax',
			'core/bpe-map',						'core/bpe-screen',
			'core/bpe-menu',					'core/bpe-deprecated',
			'core/bpe-debug'
		);
		
		// check for activity component
		if( bp_is_active( 'activity' ) )
			$files[] = 'core/bpe-activity';
		
		// check for Mapology
		if( ! defined( 'MAPO_VERSION' ) )
		{
			$files[] = 'core/models/bpe-coords';
			$files[] = 'core/bpe-usercoords';
		}
		
		foreach( $files as $file )
			require( EVENT_ABSPATH .'components/'. $file .'.php' );
	}

	/**
	 * Setup BP globals
	 *
	 * @package	 Core
	 * @since 	 1.0
	 */
	public static function setup_globals()
	{
		global $bp, $bpe;
	
		$bp->buddyvents->id 						= 'buddyvents';
		$bp->buddyvents->slug 						= BUDDYVENTS_SLUG;
		$bp->buddyvents->name 						= __( 'Events', 'events');
		$bp->buddyvents->has_directory 				= true;
		$bp->buddyvents->root_slug 					= $bp->pages->{bpe_get_base( 'slug' )}->slug;
		$bp->buddyvents->notification_callback 		= apply_filters( 'bpe_notification_callback', 'bpe_format_notifications' );
	
		$bpe->user_groups = apply_filters( 'bpe_filter_user_groups', bpe_get_groups_for_user() );
	
		// define it here, functions are too early for main class
		$bpe->config->timezones = apply_filters( 'bpe_filter_timezones', bpe_get_timezones() );
		$bpe->config->venues 	= apply_filters( 'bpe_filter_venues', bpe_get_venues() );
		
		if( ! defined( 'MAPO_VERSION' ) )
			$bp->loggedin_user->has_location = bpe_has_user_location();
	
		if( bpe_is_single_event() )
		{
			$result = bpe_get_events( array( 'slug' => bpe_get_ev_slug(), 'future' => false, 'past' => false ) );
			
			if( isset( $result['total'] ) && $result['total'] > 0 )
			{ 
				$bpe->displayed_event = $result['events'][0];
		
				$bp->is_single_item = true;
				$bp->is_item_admin = ( bpe_get_displayed_event( 'user_id' ) == bp_loggedin_user_id() ) ? true : false;
			}
		}
	
		do_action( 'bpe_setup_globals' );
	}

	/**
	 * Add Buddyvents to the loaded components array
	 * 
	 * @package	 Core
	 * @since 	 2.0
	 *
	 * @return array
	 */
	public static function add_loaded_component( $components )
	{
		$components[bpe_get_base( 'slug' )] = bpe_get_base( 'name' );
		
		return $components;
	}

	/**
	 * Setup the page title
	 *
 	 * @package	 Core
	 * @since 	 2.0
	 */
	public static function setup_title()
	{
		global $bp;
		
		if( bp_is_current_component( bpe_get_base( 'slug' ) ) )
			$bp->bp_options_title  = __( 'Events', 'events' );
	}

	/**
	 * Setup the user navigation
	 *
 	 * @package	 Core
 	 * @since 	 1.1
	 */
	public static function setup_nav()
	{
		global $bp;

		$user_id 		 = ( bp_displayed_user_id() ) ? bp_displayed_user_id() : bp_loggedin_user_id();
		$active_count 	 = bpe_get_event_count( 'active', $user_id, 'user' );
		$archive_count 	 = bpe_get_event_count( 'archive', $user_id, 'user' );
		$attending_count = 0;
		
		$total_count = $active_count + $archive_count;

		if( bp_is_my_profile() ) :
			$attending_count = bpe_get_user_events( $user_id, true );
			$total_count += $attending_count;
		endif;

		$default = ( bpe_get_option( 'default_tab_attending' ) === false ) ? bpe_get_option( 'default_tab' ) : bpe_get_option( 'attending_slug' );
	
		bp_core_new_nav_item( array( 
			'name' 						=> sprintf( __( 'Events <span>%d</span>', 'events' ), apply_filters( 'bpe_total_events_count', $total_count, $active_count, $archive_count, $attending_count, $user_id ) ),
			'slug' 						=> bpe_get_base( 'slug' ),
			'position' 					=> apply_filters( 'bpe_main_nav_position', 70 ),
			'screen_function' 			=> 'bpe_screen_events_'. $default,
			'default_subnav_slug' 		=> $default,
			'show_for_displayed_user' 	=> true,
			'item_css_id' 				=> bpe_get_base( 'id' )
			)
		);
	
		$events_link = ( bp_displayed_user_domain() ) ? bp_displayed_user_domain() : bp_loggedin_user_domain();
		$events_link = $events_link . bpe_get_base( 'slug' ). '/';
		
		$deactivated_tabs = bpe_get_option( 'deactivated_tabs' );
		
		if( ! isset( $deactivated_tabs['active'] ) ) :
			bp_core_new_subnav_item( array(
				'name' 				=> sprintf( __( 'Active <span>%d</span>', 'events' ), apply_filters( 'bpe_active_events_count', $active_count, $user_id ) ),
				'slug' 				=> bpe_get_option( 'active_slug' ),
				'parent_url' 		=> $events_link,
				'parent_slug' 		=> bpe_get_base( 'slug' ),
				'screen_function' 	=> 'bpe_screen_events_active',
				'position' 			=> (int) bpe_get_option( 'tab_order', 'active' ),
				'item_css_id' 		=> 'active-events',
				'user_has_access' 	=> true
				)
			);
		endif;
	
		if( ! isset( $deactivated_tabs['archive'] ) ) :
			bp_core_new_subnav_item( array(
				'name' 				=> sprintf( __( 'Archive <span>%d</span>', 'events' ), apply_filters( 'bpe_active_events_count', $archive_count, $user_id ) ),
				'slug' 				=> bpe_get_option( 'archive_slug' ),
				'parent_url' 		=> $events_link,
				'parent_slug' 		=> bpe_get_base( 'slug' ),
				'screen_function' 	=> 'bpe_screen_events_archive',
				'position' 			=> (int) bpe_get_option( 'tab_order', 'archive' ),
				'item_css_id' 		=> 'events-archive',
				'user_has_access' 	=> true
				)
			);
		endif;
		
		if( ! isset( $deactivated_tabs['attending'] ) ) :
			bp_core_new_subnav_item( array(
				'name' 				=> sprintf( __( 'Attending <span>%d</span>', 'events' ), apply_filters( 'bpe_attending_events_count', $attending_count, $user_id ) ),
				'slug' 				=> bpe_get_option( 'attending_slug' ),
				'parent_url' 		=> $events_link,
				'parent_slug' 		=> bpe_get_base( 'slug' ),
				'screen_function' 	=> 'bpe_screen_events_attending',
				'position' 			=> (int) bpe_get_option( 'tab_order', 'attending' ),
				'item_css_id' 		=> 'attending-events',
				'user_has_access' 	=> bp_is_my_profile()
				)
			);
		endif;

		$email = bp_get_user_meta( $user_id, 'bpe_paypal_email', true );
		if( ! isset( $deactivated_tabs['invoices'] ) && bpe_get_option( 'enable_tickets' ) === true && bpe_get_option( 'enable_invoices' ) === true && $email ) :
			bp_core_new_subnav_item( array(
				'name' 				=> __( 'Invoices', 'events' ),
				'slug' 				=> bpe_get_option( 'invoice_slug' ),
				'parent_url' 		=> $events_link,
				'parent_slug' 		=> bpe_get_base( 'slug' ),
				'screen_function' 	=> 'bpe_screen_events_invoices',
				'position' 			=> (int) bpe_get_option( 'tab_order', 'invoices' ),
				'item_css_id' 		=> 'events-invoices',
				'user_has_access' 	=> bp_is_my_profile()
				)
			);
		endif;

		if( ! isset( $deactivated_tabs['calendar'] ) ) :
			bp_core_new_subnav_item( array(
				'name' 				=> __( 'Calendar', 'events' ),
				'slug' 				=> bpe_get_option( 'calendar_slug' ),
				'parent_url' 		=> $events_link,
				'parent_slug' 		=> bpe_get_base( 'slug' ),
				'screen_function' 	=> 'bpe_screen_events_calendar',
				'position' 			=> (int) bpe_get_option( 'tab_order', 'calendar' ),
				'item_css_id' 		=> 'events-calendar',
				'user_has_access' 	=> true
				)
			);
		endif;
		
		if( ! isset( $deactivated_tabs['map'] ) ) :
			bp_core_new_subnav_item( array(
				'name' 				=> __( 'Map', 'events' ),
				'slug' 				=> bpe_get_option( 'map_slug' ),
				'parent_url' 		=> $events_link,
				'parent_slug' 		=> bpe_get_base( 'slug' ),
				'screen_function' 	=> 'bpe_screen_events_map',
				'position' 			=> (int) bpe_get_option( 'tab_order', 'map' ),
				'item_css_id' 		=> 'events-map',
				'user_has_access' 	=> true
				)
			);
		endif;
		
		if( ! bpe_is_restricted() ) :
			if( ! isset( $deactivated_tabs['create'] ) ) :
				// can't use bp_core_new_subnav_item here, so we add it directly to the array
				$bp->bp_options_nav[bpe_get_base( 'slug' )]['create'] = array(
					'name' 				=> __( 'Create', 'events' ),
					'link' 				=> bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/'. bpe_get_option( 'create_slug' ) .'/',
					'slug' 				=> bpe_get_option( 'create_slug' ),
					'css_id' 			=> 'create-event',
					'position' 			=> (int) bpe_get_option( 'tab_order', 'create' ),
					'user_has_access' 	=> bp_is_my_profile()
				);
			endif;
		endif;
	
		if( bp_is_active( 'settings' ) ) :
			bp_core_new_subnav_item( array(
				'name' 				=> __( 'Events', 'events' ),
				'slug' 				=> bpe_get_base( 'slug' ),
				'parent_url' 		=> bp_loggedin_user_domain() . bp_get_settings_slug() . '/',
				'parent_slug' 		=> bp_get_settings_slug(),
				'screen_function' 	=> 'bpe_events_settings',
				'position' 			=> 40,
				'item_css_id' 		=> 'settings-events',
				'user_has_access' 	=> bp_is_my_profile()
				)
			);
		endif;
	}

	/**
	 * Admin options
	 *
 	 * @package	 Core
 	 * @since 	 1.2
	 */
	public static function setup_adminbar_menu()
	{
		if( ! bpe_is_single_event() )
			return false;
	
		if( ! is_super_admin() )
			return false;
	
		?>
		<li id="bp-adminbar-adminoptions-menu">
			<a href=""><?php _e( 'Admin Options', 'events' ) ?></a>
	
			<ul>
				<li><a href="<?php echo bpe_get_event_link(bpe_get_displayed_event()) . bpe_get_option('edit_slug') . '/';?>"><?php _e( 'Edit Event', 'events' ) ?></a></li>
				<li><a class="confirm" href="<?php echo wp_nonce_url( bpe_get_event_link( bpe_get_displayed_event() ) . bpe_get_option( 'edit_slug' ) .'/delete/', 'bpe_delete_event_now' ) ?>"><?php _e( 'Delete Event', 'events' ) ?></a></li>
				<?php do_action( 'bpe_adminbar_menu_items' ) ?>
			</ul>
		</li>
		<?php
	}
}
Buddyvents_Core::init();
?>