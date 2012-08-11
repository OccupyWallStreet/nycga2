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
 
class Buddyvents_Admin
{
	private $update_available;
	private $current_version;
	private $db_upgrade;
	
	/**
	 * Constructor
	 * 
	 * @package Admin
	 * @since 	1.0
	 */
	public function __construct()
	{
		$this->update_available = ( get_blog_option( Buddyvents::$root_blog, 'buddyvents_update_exists' ) == 'yes' ) ? true : false;
		$this->db_upgrade 		= get_blog_option( Buddyvents::$root_blog, 'bpe_dbversion' );
		$this->current_version 	= get_blog_option( Buddyvents::$root_blog, 'buddyvents_current_version' );

		add_action( 'admin_init', 						array( &$this, 'check_upgrade' 		),  0 );
		add_action( 'admin_menu', 			 			array( &$this, 'add_menu' 	  		), 20 );
		add_action( 'admin_enqueue_scripts', 			array( &$this, 'load_scripts' 		), 10 );
		add_action( 'admin_enqueue_scripts', 			array( &$this, 'load_styles'  		), 10 );
		add_action( 'after_plugin_row_'. EVENT_PLUGIN,  array( &$this, 'add_row' 	  		), 10 );
		add_action( 'admin_init', 						array( &$this, 'remove_upgrade_nag' ),  2 );
		
		add_action( 'admin_init', 'bpe_settings_processor',	0 );
		add_action( 'admin_init', 'bpe_events_processor', 	0 );
		add_action( 'admin_init', 'bpe_sales_processor', 	0 );
		add_action( 'admin_init', 'bpe_invoice_processor', 	0 );
		add_action( 'admin_init', 'bpe_approve_processor', 	0 );
		add_action( 'admin_init', 'bpe_webhook_processor', 	0 );
		add_action( 'admin_init', 'bpe_api_processor', 		0 );

		add_filter( 'plugin_row_meta', array( &$this, 'add_links' ), 10, 2 );
		add_filter( 'contextual_help', array( &$this, 'show_help' ), 10, 2 );
	}

	/**
	 * Check for a db table upgrade
	 * 
	 * @package Admin
	 * @since 	1.4
	 */
	public function check_upgrade()
	{
		$page = ( isset( $_GET['page'] ) ) ? $_GET['page'] : false;
		
		// no need to show on Buddyvents pages
		if( strpos( EVENT_FOLDER, $page ) === true )
			return false;

		if( EVENT_DBVERSION != $this->db_upgrade )
			add_action( 'admin_notices', create_function( '', 'printf(\'<div id="message" class="error"><p><strong>\' . __( \'The Buddyvents database tables need to get upgraded to DB v%s. Please follow <a href="%s">this link</a>.\', "events" ) . \'</strong></p></div>\', EVENT_DBVERSION, admin_url( "admin.php?page=". EVENT_FOLDER ) );' ), 20 );
	}
	
	/**
	 * Add the options page
	 * 
	 * @package Admin
	 * @since 	1.0
	 */
	public function add_menu()
	{
		global $wpdb, $bpe;

		$update = ( $this->update_available == true ) ? '<span title="'. esc_attr(__( 'Update Available', 'events' ) ) .'" class="update-plugins count-1"><span class="update-count">1</span></span>' : '';
		
		add_menu_page( __( 'Events', 'events' ), __( 'Events', 'events' ) . $update, 'read', EVENT_FOLDER, array( &$this, 'show_menu' ), EVENT_URLPATH .'admin/images/logo-small.png', 8 );

		if( $this->db_upgrade != EVENT_DBVERSION )
			add_submenu_page( EVENT_FOLDER, __( 'Update Database', 'events' ), __( 'Update Database', 'events' ), 'manage_options', EVENT_FOLDER, array( &$this, 'show_menu' ) );

		else
		{
			add_submenu_page( EVENT_FOLDER, __( 'Events', 'events' ), __( 'Events', 'events' ), 'bpe_manage_events', EVENT_FOLDER, array( &$this, 'show_menu' ) );
			
			// Events created via the API are by default unapproved, so if the API
			// is enabled we need to show the approval panel
			if( bpe_get_option( 'approve_events' ) == true || bpe_get_option( 'enable_api' ) === true ) :
				$ap_number = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(e.id) FROM {$bpe->tables->events} e WHERE e.approved = 0 AND e.group_approved = 1" ) );
				$approvable = ( $ap_number ) ? sprintf( '<span title="%1$s" class="update-plugins count-%2$d"><span class="update-count">%2$d</span></span>', esc_attr(__( 'Events to approve', 'events' ) ), $ap_number ) : '';
				
				add_submenu_page( EVENT_FOLDER, __( 'Approve Events', 'events' ), __( 'Approve', 'events' ) . $approvable, 'bpe_manage_event_approvals', EVENT_FOLDER .'-approve', array( &$this, 'show_menu' ) );
			endif;
			
			add_submenu_page( EVENT_FOLDER, __( 'Categories', 'events' ), __( 'Categories', 'events' ), 'bpe_manage_event_categories', EVENT_FOLDER .'-categories', array( &$this, 'show_menu' ) );

			if( bpe_get_option( 'enable_tickets' ) == true )
				add_submenu_page( EVENT_FOLDER, __( 'Sales', 'events' ), __( 'Sales', 'events' ), 'bpe_manage_event_sales', EVENT_FOLDER .'-sales', array( &$this, 'show_menu' ) );

			if( bpe_get_option( 'enable_invoices' ) == true && bpe_get_option( 'enable_tickets' ) == true )
				add_submenu_page( EVENT_FOLDER, __( 'Invoices', 'events' ), __( 'Invoices', 'events' ), 'bpe_manage_event_invoices', EVENT_FOLDER .'-invoices', array( &$this, 'show_menu' ) );
	
			if( bpe_get_option( 'enable_api' ) === true )
				add_submenu_page( EVENT_FOLDER, __( 'API Keys', 'events' ), __( 'API Keys', 'events' ), 'bpe_manage_event_apikeys', EVENT_FOLDER .'-apikeys', array( &$this, 'show_menu' ) );

			if( bpe_get_option( 'enable_webhooks' ) === true && bpe_get_option( 'enable_api' ) === true )
				add_submenu_page( EVENT_FOLDER, __( 'Webhooks', 'events' ), __( 'Webhooks', 'events' ), 'bpe_manage_event_webhooks', EVENT_FOLDER .'-webhooks', array( &$this, 'show_menu' ) );
	
			add_submenu_page( EVENT_FOLDER, __( 'Settings',  'events' ), __( 'Settings',  'events' ), 'bpe_manage_events', EVENT_FOLDER .'-settings', 	array( &$this, 'show_menu' ) );
			add_submenu_page( EVENT_FOLDER, __( 'Services',  'events' ), __( 'Services',  'events' ), 'read', 			   EVENT_FOLDER .'-services', 	array( &$this, 'show_menu' ) );
			add_submenu_page( EVENT_FOLDER, __( 'Manual',    'events' ), __( 'Manual',    'events' ), 'bpe_manage_events', EVENT_FOLDER .'-manual', 	array( &$this, 'show_menu' ) );
			add_submenu_page( EVENT_FOLDER, __( 'Readme', 	 'events' ), __( 'Readme',    'events' ), 'read', 			   EVENT_FOLDER .'-readme', 	array( &$this, 'show_menu' ) );
			add_submenu_page( EVENT_FOLDER, __( 'Changelog', 'events' ), __( 'Changelog', 'events' ), 'read', 			   EVENT_FOLDER .'-changelog', 	array( &$this, 'show_menu' ) );
		}
	}

	/**
	 * Display the options page
	 * 
	 * @package Admin
	 * @since 	1.0
	 */
	public function show_menu()
	{
		if( $this->db_upgrade != EVENT_DBVERSION )
		{
			include_once ( EVENT_ABSPATH .'admin/bpe-upgrade.php' );
			bpe_upgrade();
			return;			
		}

		// output a warning if the PDF cache directory has the wrong permissions
		$path = EVENT_ABSPATH .'components/tickets/pdf-cache/';
		if( bpe_get_option( 'enable_tickets' ) == true && is_dir( $path ) ) :
			if( substr( decoct( fileperms( $path ) ), 2 ) != '777' )
    			bpe_show_admin_notices( array( 'content' => sprintf( __( 'Please give %s the right permissions (777) via your favorite FTP programme.', 'events' ), $path ), 'type' =>'error' ) );
		endif;

        if( current_user_can( 'activate_plugins' ) )
		{
    		if( $this->check_new_version() )
    			bpe_show_admin_notices( array( 'content' => sprintf( __( 'A new version of Buddyvents is available! Please download the latest version <a href="http://shabushabu.eu/downloads/?category=16">here</a>. <a style="float:right" href="%s">Remove</a>', 'events' ), admin_url( '?remove_nag=right-this-second-young-lady' ) ) , 'type' => 'error' ) );
    	}
		
		$page = ( isset( $_GET['page'] ) ) ? $_GET['page'] : '';
		
		switch( $page )
		{
			case EVENT_FOLDER:
				include_once( EVENT_ABSPATH .'admin/bpe-events.php' );
				new Buddyvents_Admin_Events();
				break;
				
			case EVENT_FOLDER .'-settings':
				include_once( EVENT_ABSPATH .'admin/bpe-settings.php' );
				new Buddyvents_Admin_Settings();
				break;

			case EVENT_FOLDER .'-sales':
				include_once( EVENT_ABSPATH .'admin/bpe-sales.php' );
				new Buddyvents_Admin_Sales();
				break;

			case EVENT_FOLDER .'-invoices':
				include_once( EVENT_ABSPATH .'admin/bpe-invoices.php' );
				new Buddyvents_Admin_Invoices();
				break;
				
			case EVENT_FOLDER .'-approve':
				include_once( EVENT_ABSPATH .'admin/bpe-approve.php' );
				new Buddyvents_Admin_Approve();
				break;
				
			case EVENT_FOLDER .'-categories':
				include_once( EVENT_ABSPATH .'admin/bpe-categories.php' );
				new Buddyvents_Admin_Categories();
				break;

			case EVENT_FOLDER .'-apikeys':
				include_once( EVENT_ABSPATH .'admin/bpe-apikeys.php' );
				new Buddyvents_Admin_Apikeys();
				break;

			case EVENT_FOLDER .'-webhooks':
				include_once( EVENT_ABSPATH .'admin/bpe-webhooks.php' );
				new Buddyvents_Admin_Webhooks();
				break;

			case EVENT_FOLDER .'-services':
				include_once( EVENT_ABSPATH .'admin/bpe-services.php' );
				new Buddyvents_Admin_Services();
				break;

			case EVENT_FOLDER .'-manual':
				include_once( EVENT_ABSPATH .'admin/bpe-manual.php' );
				new Buddyvents_Admin_Manual();
				break;
				
			case EVENT_FOLDER .'-readme':
				include_once( EVENT_ABSPATH .'admin/bpe-readme.php' );
				new Buddyvents_Admin_Readme();
				break;

			case EVENT_FOLDER .'-changelog':
				include_once( EVENT_ABSPATH .'admin/bpe-changelog.php' );
				new Buddyvents_Admin_Changelog();
				break;
		}
	}

	/**
	 * Load necessary scripts
	 * 
	 * @package Admin
	 * @since 	1.0
	 */
	public function load_scripts()
	{
		global $pagenow;
		
		// load flot for the admin area
		if( $pagenow == 'index.php' )
		{
			wp_enqueue_script( 'bv-sales-chart', EVENT_URLPATH .'components/tickets/js/jquery.flot.min.js', array( 'jquery' ), '1.0' );
		}
		
		// no need to go on if it's not a plugin page
		if( ! isset( $_GET['page'] ) )
			return;

		switch( $_GET['page'] )
		{
			case EVENT_FOLDER:
				if( ! wp_script_is( 'jquery-ui-datepicker', 'registered' ) )
					wp_register_script( 'jquery-ui-datepicker', EVENT_URLPATH .'js/deprecated/datepicker.js', array( 'jquery' ), '1.0', true );
		
				if( ! wp_script_is( 'jquery-ui-slider', 'registered' ) )
					wp_register_script( 'jquery-ui-slider', EVENT_URLPATH .'js/deprecated/slider.js', array( 'jquery' ), '1.0', true );

				$step = ( isset( $_GET['step'] ) ) ? $_GET['step'] : '';
				
				if( isset( $_GET['action'] ) && $_GET['action'] == 'create' || isset( $_GET['event'] ) )
				{
					if( $step == bpe_get_option( 'general_slug' ) )
					{
						wp_enqueue_script( 'bpe-maps-js', 'http://maps.google.com/maps/api/js?sensor=false' );
						
						if( isset( $_GET['event'] ) ) :
							wp_enqueue_script( 'bpe-edit', EVENT_URLPATH .'js/edit.js', array( 'bpe-timepicker', 'bpe-maps-js' ), '1.0', true );
						endif;
					}
					
					if( $step == bpe_get_option( 'general_slug' ) || $step == bpe_get_option( 'schedule_slug' ) ) :
						wp_enqueue_script( 'jquery-ui-datepicker' );
						wp_enqueue_script( 'bpe-timepicker', EVENT_URLPATH .'js/timepicker.js', array( 'jquery-ui-datepicker', 'jquery-ui-widget', 'jquery-ui-mouse', 'jquery-ui-slider' ), '0.9.3' );
					endif;
						
					if( $step == bpe_get_option( 'documents_slug' ) )
						wp_enqueue_script( 'bpe-multifile-js', EVENT_URLPATH .'js/jquery.MultiFile.min.js', array( 'jquery' ), '1.47', true );

					if( $step == bpe_get_option( 'invite_slug' ) || ( $step == bpe_get_option( 'manage_slug' ) && bpe_get_option( 'enable_manual_attendees' ) == true ) || bpe_are_tickets_enabled() )
						wp_enqueue_script( 'bpe-jquery-autocomplete', EVENT_URLPATH . 'js/jquery.autocomplete.min.js', array( 'jquery' ) );

					if( $step == bpe_get_option( 'tickets_slug' ) ) :
						wp_enqueue_script( 'jquery-ui-datepicker' );
						wp_enqueue_script( 'bpe-tickets-create', EVENT_URLPATH .'components/tickets/js/create.js', array( 'jquery' ), '1.0', true );
					endif;
					
					wp_enqueue_script( 'bpe-general', EVENT_URLPATH .'js/general.js', array( 'jquery' ), '1.0', true );
				}
				else
					wp_enqueue_script( 'bv-events', EVENT_URLPATH .'admin/js/events.js', array( 'jquery' ), '1.0' );
				break;
				
			case EVENT_FOLDER .'-apikeys':
			case EVENT_FOLDER .'-webhooks':
			case EVENT_FOLDER .'-invoices':
				wp_enqueue_script( 'bv-events', EVENT_URLPATH .'admin/js/events.js', array( 'jquery' ), '1.0' );
				break;

			case EVENT_FOLDER .'-sales':
				wp_enqueue_script( 'bv-sales-chart', EVENT_URLPATH .'components/tickets/js/jquery.flot.min.js', array( 'jquery' ), '1.0' );
				break;
				
			case EVENT_FOLDER .'-settings':
				wp_enqueue_script( 'jquery-ui-draggable' );
				wp_enqueue_script( 'jquery-ui-droppable' );
				wp_enqueue_script( 'jquery-ui-sortable' );
				wp_enqueue_script( 'bpe-maps-js', 'http://maps.google.com/maps/api/js?sensor=false' );
				wp_enqueue_script( 'bv-settings', EVENT_URLPATH .'admin/js/settings.js', array( 'jquery' ), '1.0' );
				break;
				
			case EVENT_FOLDER .'-approve':
				wp_enqueue_script( 'bv-events', EVENT_URLPATH .'admin/js/events.js', array( 'jquery' ), '1.0' );
				wp_enqueue_script( 'bv-approve', EVENT_URLPATH .'admin/js/approve.js', array( 'jquery' ), '1.0' );
				break;

			case EVENT_FOLDER .'-services':
				wp_enqueue_script( 'bv-services', EVENT_URLPATH .'admin/js/services.js', array( 'jquery' ), '1.0' );
				break;
				
			case EVENT_FOLDER .'-categories':
				wp_enqueue_script( 'bv-cats', EVENT_URLPATH .'admin/js/categories.js', array( 'jquery' ), '1.0' );
				break;

			case EVENT_FOLDER .'-manual':
				wp_enqueue_script( 'bv-sticky', EVENT_URLPATH .'admin/js/stickyfloat.js', array( 'jquery' ), '1.4.2' );
				wp_enqueue_script( 'bv-scrollto', EVENT_URLPATH .'admin/js/jquery.scrollTo.js', array( 'jquery' ), '1.4.2' );
				wp_enqueue_script( 'bv-shcore', EVENT_URLPATH .'admin/js/shCore.js', array( 'jquery' ), '1.0' );
				wp_enqueue_script( 'bv-shcss', EVENT_URLPATH .'admin/js/shBrushCss.js', array( 'jquery', 'bv-shcore' ), '1.0' );
				wp_enqueue_script( 'bv-shjs', EVENT_URLPATH .'admin/js/shBrushJScript.js', array( 'jquery', 'bv-shcore' ), '1.0' );
				wp_enqueue_script( 'bv-shphp', EVENT_URLPATH .'admin/js/shBrushPhp.js', array( 'jquery', 'bv-shcore' ), '1.0' );
				wp_enqueue_script( 'bv-shplain', EVENT_URLPATH .'admin/js/shBrushPlain.js', array( 'jquery', 'bv-shcore' ), '1.0' );
				break;
		}
	}		
	
	/**
	 * Load necessary styles
	 * 
	 * @package Admin
	 * @since 	1.0
	 */
	public function load_styles()
	{
		// no need to go on if it's not a plugin page
		if( ! isset( $_GET['page'] ) )
			return;

		switch( $_GET['page'] )
		{
			case EVENT_FOLDER .'-settings':
			case EVENT_FOLDER .'-sales':
			case EVENT_FOLDER .'-invoices':
			case EVENT_FOLDER .'-approve':
			case EVENT_FOLDER .'-categories':
			case EVENT_FOLDER .'-apikeys':
			case EVENT_FOLDER .'-webhooks':
			case EVENT_FOLDER .'-services':
			case EVENT_FOLDER .'-readme':
			case EVENT_FOLDER .'-changelog':
				wp_enqueue_style( 'bpeadmin', EVENT_URLPATH .'admin/css/bpe-admin.css', false, '1.0', 'screen' );
				break;

			case EVENT_FOLDER:
				wp_enqueue_style( 'bpeadmin', EVENT_URLPATH .'admin/css/bpe-admin.css', false, '1.0', 'screen' );
				wp_enqueue_style( 'bpe-datepicker-css', EVENT_URLPATH .'css/datepicker.css' );
				
				$step = ( isset( $_GET['step'] ) ) ? $_GET['step'] : '';
				
				if( $step == bpe_get_option( 'invite_slug' ) || ( $step == bpe_get_option( 'manage_slug' ) && bpe_get_option( 'enable_manual_attendees' ) == true ) || bpe_are_tickets_enabled() )
					wp_enqueue_style( 'bpe-messages-autocomplete', EVENT_URLPATH . 'css/jquery.autocompletefb.css' );
				break;

			case EVENT_FOLDER .'-manual':
				wp_enqueue_style( 'bpeadmin', EVENT_URLPATH .'admin/css/bpe-admin.css', false, '1.0', 'screen' );
				wp_enqueue_style( 'bpe-docs', EVENT_URLPATH .'admin/css/documenter_style.css', false, '1.0', 'screen' );
				wp_enqueue_style( 'bpe-sh-docs', EVENT_URLPATH .'admin/css/shDocumenter.css', false, '1.0', 'screen' );
				break;
		}
	}
	
	/**
	 * Add some helpful links
	 * 
	 * @package Admin
	 * @since 	1.0
	 */
	public function show_help( $help, $screen_id )
	{
		if( strpos( $screen_id, EVENT_FOLDER ) === false )
			return $help;
		
		$help  = '<div class="metabox-prefs">';

		switch( $screen_id )
		{
			case 'toplevel_page_'. EVENT_FOLDER :
				$help .= '<p>'. __( 'Here you can find an overview of all created events. You also have the option to edit existing events by clicking on an event title or create new ones by following the button next to the heading.', 'events' ) .'</p>';
				$help .= '<p>'. __( "By using the 'Filter events' button you can filter all available events by category, user and group in any given combination.", 'events' ) .'</p>';
				$help .= '<p>'. __( 'Bulk actions let you set events to spam, delete them and un-spam them.', 'events' ) .'</p>';
				break;

			case 'events_page_'. EVENT_FOLDER .'-approve' :
				$help .= '<p>'. __( 'Here you will only ever see events that require an action from you. You can either approve an event, or decline it.', 'events' ) .'</p>';
				$help .= '<p>'. __( 'Only approved events will show up on the frontend. Declined events will be removed from the database.', 'events' ) .'</p>';
				$help .= '<p>'. __( 'Clicking the title will show the event description.', 'events' ) .'</p>';
			break;

			case 'events_page_'. EVENT_FOLDER .'-categories' :
				$help .= '<p>'. __( 'Only the category name is required when creating a new category. The slug will be automatically created from the name. You can specify a custom slug, though.', 'events' ) .'</p>';
				$help .= '<p>'. __( 'Clicking an event name will let you edit that category. Categories can be deleted by clicking the red cross next to the name.', 'events' ) .'</p>';
				$help .= '<p>'. __( 'When deleting a category, all events in that category will be associated with the default category.', 'events' ) .'</p>';
				break;

			case 'events_page_'. EVENT_FOLDER .'-sales' :
				$help .= '<p>'. __( 'This is an overview page of all ticket sales happening on your site. You can filter the sales by user, month, year and status.', 'events' ) .'</p>';
				$help .= '<p>'. __( 'You can also create invoices from this screen. Invoices can only be sent by month. Invoices will be created of all sales currently visible on the screen.', 'events' ) .'</p>';
				$help .= '<p>'. __( 'Invoices can only be created from past months. The button to create invoices will only be visible 5 days after a month has been concluded and only if sales have been filtered by both month and year.', 'events' ) .'</p>';
				$help .= '<p>'. __( 'The graph and the commission overview will reflect any filtered variables.', 'events' ) .'</p>';
				break;

			case 'events_page_'. EVENT_FOLDER .'-invoices' :
				$help .= '<p>'. __( 'Here you can see all created invoices. You can preview, delete and send invoices, either single or in bulk.', 'events' ) .'</p>';
				$help .= '<p>'. __( 'Invoices can be filtered by user, month, status and whether an invoice has been settled yet or not.', 'events' ) .'</p>';
				break;

			case 'events_page_'. EVENT_FOLDER .'-apikeys' :
				$help .= '<p>'. __( 'This page gives you an overview over all registered API keys, which user they belong to and what status they have.', 'events' ) .'</p>';
				$help .= '<p>'. __( 'Access for API keys can be either revoked here or granted again, either single or in bulk.', 'events' ) .'</p>';
				break;

			case 'events_page_'. EVENT_FOLDER .'-webhooks' :
				$help .= '<p>'. __( 'Webhooks allow users to receive event data in near real-time. Push notifications are sent to each registered URL if event data changes or gets added. This is a powerful feature, but can take up lots of resources if many URLs need to get pinged.', 'events' ) .'</p>';
				$help .= '<p>'. __( 'This page gives you an overview over all registered webhooks, which user they belong to and what status they have.', 'events' ) .'</p>';
				break;

			case 'events_page_'. EVENT_FOLDER .'-settings' :
				$help .= '<p>'. __( 'All available settings for Buddyvents installation. In the bottom right corner you find a button that resets all options to their default state. Be very careful with this button, as it will not be reversible.', 'events' ) .'</p>';
				break;

			case 'events_page_'. EVENT_FOLDER .'-changelog' :
				$help .= '<p>'. __( 'The current changelog.', 'events' ) .'</p>';
				break;

			case 'events_page_'. EVENT_FOLDER .'-readme' :
				$help .= '<p>'. __( 'The current readme file.', 'events' ) .'</p>';
				break;

			case 'events_page_'. EVENT_FOLDER .'-services' :
				$help .= '<p>'. __( 'The current additional services offered by ShabuShabu Glumpler GbR.', 'events' ) .'</p>';
				break;
		}

		$help .= '<p><a href="'. Buddyvents::HOME_URL .'forums/forum/buddyvents-v2/">' . __( 'Support Forum', 'events' ) . '</a> | <a href="'. Buddyvents::HOME_URL .'wiki-overview/">' . __( 'Documentation', 'events' ) . '</a></p>';
		$help .= '</div>';

		return $help;
	}

	/**
	 * Maybe show the upgrade message
	 * 
	 * @package Admin
	 * @since 	1.2.1
	 */
	public function add_row()
	{
		if( $this->update_available == true )
			echo '</tr><tr class="plugin-update-tr"><td colspan="5" class="plugin-update"><div class="update-message">'. __( 'A new version of Buddyvents is available! Please download the latest version <a href="http://shabushabu.eu/downloads/?category=14">here</a>.', 'events' ) .'</div></td>';
	}

	/**
	 * Check for an update
	 * 
	 * @package Admin
	 * @since 	1.1
	 */
	private function check_new_version()
	{
		if( version_compare( Buddyvents::VERSION, $this->current_version, '=' ) )
			delete_blog_option( Buddyvents::$root_blog, 'buddyvents_update_exists' );
		
		if( $this->update_available == true )
			return true;

		$interval = get_blog_option( Buddyvents::$root_blog, 'buddyvents_next_update' );

		if( $interval < time() || empty( $interval ) )
		{
			// check twice a day
			$interval = time() + 43200;
			
			update_blog_option( Buddyvents::$root_blog, 'buddyvents_next_update', $interval );
			
			$options = array();
			$options['headers'] = array(
                'User-Agent' => 'Buddyvents v'. Buddyvents::VERSION,
                'Referer' 	 => get_bloginfo( 'url' )
			);
			
			$response = wp_remote_request( Buddyvents::HOME_URL .'versions.php', $options );

			if( is_wp_error( $response ) )
				return false;
		
			if( 200 != wp_remote_retrieve_response_code( $response ) )
				return false;
				
			$version = maybe_unserialize( wp_remote_retrieve_body( $response ) );
			
			update_blog_option( Buddyvents::$root_blog, 'buddyvents_current_version', $version['buddyvents'] );

			if( is_array( $version ) )
			{
				if( version_compare( $version['buddyvents'], Buddyvents::VERSION, '>' ) )
				{
					update_blog_option( Buddyvents::$root_blog, 'buddyvents_update_exists', 'yes' );
					return true;
				}
			} 
				
			delete_blog_option( Buddyvents::$root_blog, 'buddyvents_update_exists' );
			return false;
		}
	}

	/**
	 * Remove the upgrade nag
	 * 
	 * @package Admin
	 * @since 	1.7
	 */	
	public function remove_upgrade_nag()
	{
		if( isset( $_GET['remove_nag'] ) && $_GET['remove_nag'] = 'right-this-second-young-lady' )
		{
			delete_blog_option( Buddyvents::$root_blog, 'buddyvents_update_exists' );
			wp_redirect( admin_url() );
			exit();
		}
	}

	/**
	 * Add some links to plugin setup page
	 * 
	 * @package Admin
	 * @since 	1.0
	 */
	public function add_links( $links, $file )
	{
		if( $file == Buddyvents::$plugin_name )
		{
			$links[] = '<a href="'. Buddyvents::HOME_URL .'forums/forum/buddyvents-v2/">' . __( 'Support Forum', 'events' ) . '</a>';
			$links[] = '<a href="'. Buddyvents::HOME_URL .'donation/">' . __( 'Donate', 'events' ) . '</a>';
		}
		
		return $links;
	}
}
?>