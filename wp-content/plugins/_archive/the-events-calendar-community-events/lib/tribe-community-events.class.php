<?php

// Don't load directly
if ( !defined( 'ABSPATH' ) ) {
	die('-1');
}


if ( !class_exists( 'TribeCommunityEvents' ) ) {
	/**
	 * Tribe Community Events main class
	 *
	 * @package TribeCommunityEvents
	 * @since  1.0
	 * @author Modern Tribe Inc.
	 */
	class TribeCommunityEvents {
		
		/**
		 * The current version of Community Events
		 */
		const VERSION = '1.0.2';
		
		/**
		 * required The Events Calendar Version
		 */
		const REQUIRED_TEC_VERSION = '2.0.8';
		
		/**
		 * Singleton instance variable
		 * @var object
		 */
		private static $instance;

		/**
		 * TabIndex used for forms
		 * @var int
		 */
		private $tabIndex = 1998;

		/**
		 * Loadscripts or not
		 * @var bool
		 */
		private $loadScripts = false;

		/**
		 * plugin options
		 * @var array
		 */
		protected static $options;

		/**
		 * this plugin's directory
		 * @var string
		 */
		public $pluginDir;

		/**
		 * this plugin's path
		 * @var string
		 */
		public $pluginPath;

		/**
		 * this plugin's url
		 * @var string
		 */
		public $pluginUrl;

		/**
		 * this plugin's slug
		 * @var string
		 */
		public $pluginSlug;

		/**
		 * this plugin's slug for PUE
		 * @var string
		 */
		public $slug; //PUE uses slug vs. pluginSlug

		/**
		 * tribe url (used for calling the mothership)
		 * @var string
		 */
		public static $tribeUrl = 'http://tri.be/';

		/**
		 * update url (used for calling the mothership)
		 * @var string
		 */
		public static $updateUrl = 'http://tri.be/';

		/**
		 * install key (used for PUE)
		 * @var string
		 */
		public $install_key;

		/**
		 * default event status
		 * @var string
		 */
		public $defaultStatus;

		/**
		 * setting to allow anonymous submissions
		 * @var bool
		 */
		public $allowAnonymousSubmissions;

		/**
		 * setting to allow editing of submisisons
		 * @var bool
		 */
		public $allowUsersToEditSubmissions;

		/**
		 * setting to allow deletion of submisisons
		 * @var bool
		 */
		public $allowUsersToDeleteSubmissions;

		/**
		 * setting to trash items instead of permanent delete
		 * @var bool
		 */
		public $trashItemsVsDelete;

		/**
		 * setting to use visual editor
		 * @var bool
		 */
		public $useVisualEditor;

		/**
		 * setting to control # of events per page
		 * @var int
		 */
		public $eventsPerPage;

		/**
		 * setting to control format for dates
		 * @var string
		 */
		public $eventListDateFormat;

		/**
		 * setting for pagination range
		 * @var string
		 */
		public $paginationRange;

		/**
		 * setting for default organizer (requires ECP)
		 * @var int
		 */
		public $defaultCommunityOrganizerID;

		/**
		 * setting for default venue (requires ECP)
		 * @var int
		 */
		public $defaultCommunityVenueID;

		/**
		 * message to be displayed to the user
		 * @var string
		 */
		public $message;

		/**
		 * the type of the message (error, notice, etc.)
		 * @var string
		 */
		public $messageType;

		/**
		 * the rewrite slug to use
		 * @var string
		 */
		public $communityRewriteSlug;

		/**
		 * the main rewrite slug to use
		 * @var string
		 */
		public $rewriteSlugs;

		/**
		 * rewrite slugs for different components
		 * @var array
		 */
		public $context;
		
		/**
		 * is the current page the my events list?
		 * @var bool
		 */
		public $isMyEvents;
		
		/**
		 * is the current page the event edit page?
		 * @var bool
		 */
		public $isEditPage;
		
		/**
		 * should the permalinks be flushed upon plugin load?
		 * @var bool
		 */
		 public $maybeFlushRewrite;

		/**
		 * option name to save all plugin options under
		 * as a serialized array
		 */
		const OPTIONNAME = 'tribe_community_events_options';

		/**
		 * class constructor
		 * sets all the class vars up and such
		 *
		 * @since 1.0
		 * @author nciske
		 * @return void
		 */
		private function __construct() {
			$tec = TribeEvents::instance();

			$this->getOptions();

			// get options
			$this->defaultStatus                 = $this->getOption( 'defaultStatus' );
			$this->allowAnonymousSubmissions     = $this->getOption( 'allowAnonymousSubmissions' );
			$this->allowUsersToEditSubmissions   = $this->getOption( 'allowUsersToEditSubmissions' );
			$this->allowUsersToDeleteSubmissions = $this->getOption( 'allowUsersToDeleteSubmissions' );
			$this->trashItemsVsDelete            = $this->getOption( 'trashItemsVsDelete' );
			$this->useVisualEditor               = $this->getOption( 'useVisualEditor' );
			$this->eventsPerPage                 = $this->getOption( 'eventsPerPage' );
			$this->eventListDateFormat           = $this->getOption( 'eventListDateFormat' );
			$this->paginationRange               = 3;
			$this->defaultStatus                 = $this->getOption( 'defaultStatus' );

			$this->emailAlertsEnabled            = $this->getOption( 'emailAlertsEnabled' );
			$emailAlertsList                     = $this->getOption( 'emailAlertsList' );

			$this->emailAlertsList = explode( "\n" , $emailAlertsList );

			$this->blockRolesFromAdmin = $this->getOption( 'blockRolesFromAdmin' );
			$this->blockRolesList      = $this->getOption( 'blockRolesList' );
			$this->blockRolesRedirect  = $this->getOption( 'blockRolesRedirect' );
			
			$this->maybeFlushRewrite   = $this->getOption( 'maybeFlushRewrite' );


			if ( $this->blockRolesFromAdmin )
				add_action( 'init', array( $this, 'blockRolesFromAdmin' ) );

			$this->pluginDir  = trailingslashit( basename( dirname( dirname( __FILE__ ) ) ) );
			$this->pluginPath = trailingslashit( dirname( dirname( __FILE__ ) ) );
			$this->pluginUrl  = plugins_url( '', dirname( __FILE__ ) );
			$this->pluginSlug = $this->slug = 'events-community';
			
			$this->isMyEvents = false;
			$this->isEditPage = false;

			$this->install_key = get_option( 'pue_install_key_events_community' );

			// find page associated with the shortcode
			$this->tcePageId      = $this->findPageByShortcode( '[tribe_community_events]' );

			$this->communityRewriteSlug = $this->getOption( 'communityRewriteSlug', 'community' );
			$this->eventsRewriteSlug    = TribeEvents::getOption( 'eventsSlug', 'events' );

			$this->rewriteSlugs['edit']   = sanitize_title( __( 'edit','tribe-events-community' ) );
			$this->rewriteSlugs['add']    = sanitize_title( __( 'add','tribe-events-community' ) );
			$this->rewriteSlugs['delete'] = sanitize_title( __( 'delete','tribe-events-community' ) );
			$this->rewriteSlugs['list']   = sanitize_title( __( 'list','tribe-events-community' ) );

			$this->rewriteSlugs['venue']     = sanitize_title( __( 'venue', 'tribe-events-community' ) );
			$this->rewriteSlugs['organizer'] = sanitize_title( __( 'organizer', 'tribe-events-community' ) );
			$this->rewriteSlugs['event']     = sanitize_title( __( 'event', 'tribe-events-community' ) );

			if ( '' == get_option( 'permalink_structure' ) ) {
				add_shortcode( 'tribe_community_events', array( $this, 'doShortCode' ) );

				add_shortcode( 'tribe_community_events_title', array( $this, 'doShortCodeTitle' ) );

				//allow shortcodes for dynamic titles
				add_filter( 'the_title', 'do_shortcode' );
				add_filter( 'wp_title', 'do_shortcode' );
				

				add_action( 'template_redirect', array( $this, 'maybeRedirectMyEvents' ) );
			} else {
				add_action( 'template_redirect', array( $this, 'redirectUglyUrls' ) );
			}

			add_action( 'init', array( $this, 'maybeLoadAssets' ) );

			add_action( 'init', array( $this, 'init' ) );
			
			add_action( 'wp_before_admin_bar_render', array( $this, 'addCommunityToolbarItems' ), 20 );

			// Tribe common resources
			TribeCommonLibraries::register( 'pue-client', '1.2', $this->pluginPath . 'vendor/pue-client/pue-client.php' );
			TribeCommonLibraries::register( 'wp-router', '0.3', $this->pluginPath . 'vendor/wp-router/wp-router.php' );

			add_action( 'tribe_helper_activation_complete', array( $this, 'helpersLoaded' ) );
			
			add_action( 'tribe_settings_save_field_allowAnonymousSubmissions', array( $this, 'flushRewriteOnAnonymous' ), 10, 2 );

			add_filter( 'query_vars', array( $this, 'communityEventQueryVars' ) );

			add_filter( 'body_class', array( $this, 'setBodyClasses' ) );

			// options page hook
			add_action( 'tribe_settings_do_tabs', array( $this, 'doSettingsTab' ), 10, 1 );

			add_action( 'wp_router_generate_routes', array( $this, 'addRoutes' ) );
			
			add_action( 'plugin_action_links_' . trailingslashit( $this->pluginDir ) . 'tribe-community-events.php', array( $this, 'addLinksToPluginActions' ) );
		
			// PressTrends WordPress Action
			if ( tribe_get_option( 'sendPressTrendsData', false ) ) {
				add_action('admin_init', 'presstrends_plugin_tribe_events_community');
			}
		}

		/**
		 * determines what assets to load
		 *
		 * @since 1.0
		 * @author nciske
		 * @return void
		 */
		public function maybeLoadAssets() {

			$tec = TribeEvents::instance();

			// load TEC/ECP meta box assets
			if ( !is_admin() ) {
				global $current_screen;
				$current_screen->post_type = TribeEvents::POSTTYPE;
			}
			
			add_action( 'wp_enqueue_scripts', array( $tec, 'addAdminScriptsAndStyles' ) );

			add_action( 'wp_footer', array( $tec, 'printLocalizedAdmin' ) );

			// load EC resources
			add_action( 'wp_enqueue_scripts', array( $this, 'addScriptsAndStyles' ) );

		}

		/**
		 * add wprouter and callbacks
		 *
		 * @since 1.0
		 * @author nciske
		 * @param object $router the router object
		 * @return void
		 */
		public function addRoutes( $router ) {

			// edit venue
			$router->add_route( 'ce-edit-venue-route', array(
				 'path' => '^' . $this->eventsRewriteSlug . '/' . $this->communityRewriteSlug . '/' . $this->rewriteSlugs['edit'] . '/' . $this->rewriteSlugs['venue'].'/(.*?)$',
				'query_vars' => array(
					 'tribe_event_id' => 1
				),
				'page_callback' => array(
					 get_class(),
					'editCallback'
				),
				'page_arguments' => array(
					 'tribe_event_id'
				),
				'access_callback' => true,
				'title' => __( 'Edit a Venue', 'tribe-events-community' ),
				'template' => array(
					 'page.php',
					dirname( __FILE__ ) . '/page.php'
				)
			) );


			// edit organizer
			$router->add_route( 'ce-edit-organizer-route', array(
				 'path' => '^' . $this->eventsRewriteSlug . '/' . $this->communityRewriteSlug . '/' . $this->rewriteSlugs['edit'] . '/' . $this->rewriteSlugs['organizer'] . '/(.*?)$',
				'query_vars' => array(
					 'tribe_event_id' => 1
				),
				'page_callback' => array(
					 get_class(),
					'editCallback'
				),
				'page_arguments' => array(
					 'tribe_event_id'
				),
				'access_callback' => true,
				'title' => __( 'Edit an Organizer', 'tribe-events-community' ),
				'template' => array(
					 'page.php',
					dirname( __FILE__ ) . '/page.php'
				)
			) );

			// edit event
			$router->add_route( 'ce-edit-route', array(
				 'path' => '^' . $this->eventsRewriteSlug . '/' . $this->communityRewriteSlug . '/' . $this->rewriteSlugs['edit'] . '/' . $this->rewriteSlugs['event'] . '/(.*?)$',
				'query_vars' => array(
					 'tribe_event_id' => 1
				),
				'page_callback' => array(
					 get_class(),
					'editCallback'
				),
				'page_arguments' => array(
					 'tribe_event_id'
				),
				'access_callback' => true,
				'title' => __( 'Edit an Event', 'tribe-events-community' ),
				'template' => array(
					 'page.php',
					dirname( __FILE__ ) . '/page.php'
				)
			) );


			// edit redirect
			$router->add_route( 'ce-edit-redirect-route', array(
				 'path' => '^' . $this->eventsRewriteSlug . '/' . $this->communityRewriteSlug . '/' . $this->rewriteSlugs['edit'] . '/(.*?)$',
				'query_vars' => array(
					 'tribe_id' => 1
				),
				'page_callback' => array(
					 get_class(),
					'redirectCallback'
				),
				'page_arguments' => array(
					 'tribe_id'
				),
				'access_callback' => true,
				'title' => __( 'Redirect', 'tribe-events-community' ),
				'template' => array(
					 'page.php',
					dirname( __FILE__ ) . '/page.php'
				)
			) );



			// add event
			$router->add_route( 'ce-add-route', array(
				 'path' => '^' . $this->eventsRewriteSlug . '/' . $this->communityRewriteSlug . '/' . $this->rewriteSlugs['add'] . '$',
				'query_vars' => array(
				),
				'page_callback' => array(
					 get_class(),
					'addCallback'
				),
				'page_arguments' => array(
				),
				'access_callback' => true,
				'title' => __( 'Submit an Event', 'tribe-events-community' ),
				'template' => array(
					 'page.php',
					dirname( __FILE__ ) . '/page.php'
				)
			) );

			// delete event
			$router->add_route( 'ce-delete-route', array(
				 'path' => '^' . $this->eventsRewriteSlug . '/' . $this->communityRewriteSlug . '/' . $this->rewriteSlugs['delete'] . '/(.*?)$',
				'query_vars' => array(
					 '$tribe_event_id' => 1
				),
				'page_callback' => array(
					 get_class(),
					'deleteCallback'
				),
				'page_arguments' => array(
					 '$tribe_event_id'
				),
				'access_callback' => true,
				'title' => __( 'Remove an Event', 'tribe-events-community' ),
				'template' => array(
					 'page.php',
					dirname( __FILE__ ) . '/page.php'
				)
			) );

			// list events
			$router->add_route( 'ce-list-route', array(
				 'path' => '^' . $this->eventsRewriteSlug . '/' . $this->communityRewriteSlug . '/' . $this->rewriteSlugs['list'] . '$',
				'query_vars' => array(
					 'sample_argument' => 1
				),
				'page_callback' => array(
					 get_class(),
					'listCallback'
				),
				'page_arguments' => array(
					 'sample_argument'
				),
				'access_callback' => true,
				'title' => __( 'My Events', 'tribe-events-community' ),
				'template' => array(
					 'page.php',
					dirname( __FILE__ ) . '/page.php'
				)
			) );


			// list user's events
			$router->add_route( 'ce-list-route-args', array(
				 'path' => '^' . $this->eventsRewriteSlug . '/' . $this->communityRewriteSlug . '/' . $this->rewriteSlugs['list'] . '/page/(.*?)$',
				'query_vars' => array(
					 'page' => 1
				),
				'page_callback' => array(
					 get_class(),
					'listCallback'
				),
				'page_arguments' => array(
					 'page'
				),
				'access_callback' => true,
				'title' => __( 'My Events', 'tribe-events-community' ),
				'template' => array(
					 'page.php',
					dirname( __FILE__ ) . '/page.php'
				)
			) );

		}

		/**
		 * redirect user to the right place
		 *
		 * @since 1.0
		 * @author nciske
		 * @param string $tribe_id the page being viewed
		 * @return void
		 */
		public function redirectCallback( $tribe_id ) {

			$tce = self::instance();

			if ( $tribe_id != $tce->rewriteSlugs['event'] && $tribe_id != $tce->rewriteSlugs['venue'] && $tribe_id != $tce->rewriteSlugs['organizer'] ) {
				// valid route
				$context = $tce->getContext( 'edit', $tribe_id );
				$url = $tce->getUrl( 'edit', $tribe_id, null, $context['post_type'] );
				wp_redirect( $url ); exit;
			} else {
				// invalid route, redirect to My Events
				wp_redirect( $tce->getUrl( 'list' ) ); exit;
			}

		}

		/**
		 * display event editing
		 *
		 * @since 1.0
		 * @author nciske
		 * @param $tribe_id the event being viewed
		 * @return string the form to display
		 */
		public function editCallback( $tribe_id ) {

			$tce = self::instance();
			
			$tce->isEditPage = true;
			
			$tce->removeFilters();

			$context = $tce->getContext( 'edit', $tribe_id );

			if ( $context['post_type'] == TribeEvents::VENUE_POST_TYPE )
				return $tce->doVenueForm( $tribe_id );

			if ( $context['post_type'] == TribeEvents::ORGANIZER_POST_TYPE )
				return $tce->doOrganizerForm( $tribe_id );

			if ( $context['post_type'] == TribeEvents::POSTTYPE )
				return $tce->doEventForm( $tribe_id );
			
			if ( !isset( $context['post_type']) )
				return __( 'Not found.', 'tribe-events-community' );

		}

		/**
		 * display event deletion
		 *
		 * @since 1.0
		 * @author nciske
		 * @param int $tribe_event_id the event id
		 * @return void
		 */
		public function deleteCallback( $tribe_event_id ) {

			$tce = self::instance();
			$tce->removeFilters();
			echo $tce->doDelete( $tribe_event_id );

		}


		/**
		 * display event adding
		 *
		 * @since 1.0
		 * @author nciske
		 * @return void
		 */
		public function addCallback() {

			$tce = self::instance();
			
			$tce->isEditPage = true;
			
			$tce->removeFilters();
			echo $tce->doEventForm();

		}

		/**
		 * display event listings
		 *
		 * @since 1.0
		 * @author nciske
		 * @return void
		 */
		public function listCallback( $page ) {

			$tce = self::instance();
			
			$tce->isMyEvents = true;
			
			// redirect to page containing today's events
			if ( 0 == $page && is_user_logged_in() ) {
				$page = $tce->findTodaysPage();
				wp_redirect( $tce->getUrl( 'list', null, $page ) );
			}

			$tce->removeFilters();
			echo $tce->doMyEvents( $page );

		}


		/**
		 * determine whether to redirect a user back to his events
		 *
		 * @since 1.0
		 * @author nciske
		 * @return void
		 */
		public function maybeRedirectMyEvents() {

			if ( !is_admin() ) {
				//redirect my events with no args to todays page
				global $paged;
				if ( empty( $paged ) && isset( $_GET['tribe_action'] ) && $_GET['tribe_action'] == 'list' ) {
					$paged = $this->findTodaysPage();
					wp_redirect( $this->getUrl( 'list', null, $paged ) ); exit;
				}
			}
		}

		/**
		 * take care of ugly URLs
		 *
		 * @since 1.0
		 * @author nciske
		 * @return void
		 */
		public function redirectUglyUrls() {

			if ( !is_admin() ) {
				// disable title shortcode
				add_shortcode( 'tribe_community_events', '__return_null' );
				add_shortcode( 'tribe_community_events_title', create_function( '', 'return __( "Submit an Event", "tribe-events-community" );' ) );
				add_filter( 'the_title', 'do_shortcode' );

				if ( isset( $this->tcePageId ) && get_the_ID() == $this->tcePageId ) {
					$url = $this->getUrl( 'add' );
				}

				// redirect ugly link URLs to pretty permalinks
				if ( isset( $_GET['tribe_action'] ) ) {
					if ( isset( $_GET['paged'] ) ) {
						$url = $this->getUrl( $_GET['tribe_action'], null, $_GET['paged'] );
					} elseif ( isset( $_GET['tribe_id'] ) ) {
						$url = $this->getUrl( $_GET['tribe_action'], $_GET['tribe_id'] );
					} else {
						$url = $this->getUrl( $_GET['tribe_action'] );
					}
				}

				if ( isset( $url ) ) {
					wp_redirect( $url ); exit;
				}
			}

		}

		/**
		 * get the URL for a specific action
		 *
		 * @since 1.0
		 * @author nciske
		 * @param string $action the action being performed
		 * @param int $id the id of whatever is being done, if applicable
		 * @param string $page the page being used
		 * @param string $post_type the post type being used
		 * @return sring the url
		 */
		public function getUrl( $action, $id = null, $page = null, $post_type = null ) {

			if ( '' == get_option( 'permalink_structure' ) ) {
				// pretty permalinks off
				if ( !$this->tcePageId ) {
					echo __( 'Community Events requires non-default (pretty) permalinks to be enabled or the [tribe_community_events] shortcode to exist on a page.', 'tribe-events-community' );
					return;
				}

				$args = array( 'tribe_action' => $action );
				if ( $id )
					$args['tribe_id'] = $id;
				if ( $page )
					$args['paged'] = $page;

				return add_query_arg( $args, get_permalink( $this->tcePageId ) );
			} else {
				if ( $id ) {
					if ( $post_type ) {
						if ( $post_type == TribeEvents::POSTTYPE )
							return home_url() . '/' . $this->eventsRewriteSlug . '/' . $this->communityRewriteSlug . '/' . $this->rewriteSlugs[$action] . '/' . $this->rewriteSlugs['event'] . '/' . $id . '/';

						if ( $post_type == TribeEvents::ORGANIZER_POST_TYPE )
							return home_url() . '/' . $this->eventsRewriteSlug . '/' . $this->communityRewriteSlug . '/' . $this->rewriteSlugs[$action] . '/' . $this->rewriteSlugs['organizer'] . '/' . $id . '/';

						if ( $post_type == TribeEvents::VENUE_POST_TYPE )
							return home_url() . '/' . $this->eventsRewriteSlug . '/' . $this->communityRewriteSlug . '/' . $this->rewriteSlugs[$action] . '/' . $this->rewriteSlugs['venue'] . '/' . $id . '/';
					} else {
						return home_url() . '/' . $this->eventsRewriteSlug . '/' . $this->communityRewriteSlug . '/' . $this->rewriteSlugs[$action] . '/' . $id . '/';
					}
				} else {
					if ( $page ) {
						return home_url() . '/' . $this->eventsRewriteSlug . '/' . $this->communityRewriteSlug . '/' . $this->rewriteSlugs[$action] . '/page/' . $page . '/';
					} else {
						return home_url() . '/' . $this->eventsRewriteSlug . '/' . $this->communityRewriteSlug . '/' . $this->rewriteSlugs[$action];
					}
				}
			}
		}

		/**
		 * get delete button for an event
		 *
		 * @since 1.0
		 * @author nciske
		 * @param object $event the event to get the button for
		 * @return string $output the button's output
		 */
		public function getDeleteButton( $event ) {
			
			if ( !$this->allowUsersToDeleteSubmissions ) {
				$output = '';
				return $output;
			}
			if ( class_exists( 'TribeEventsPro' ) && tribe_is_recurring_event( $event->ID ) ) {
				$output  = ' <span class="delete wp-admin events-cal">| <a rel="nofollow" class="submitdelete" href="';
				$output .= add_query_arg( 'eventDate', date( 'Y-m-d', strtotime( $event->EventStartDate ) ), wp_nonce_url( $this->getUrl( 'delete', $event->ID ) , 'tribe_community_events_delete' ) );
				$output .= '">' . __( 'Delete', 'tribe-events-community' ) . '</a></span>';
				return $output;
			} else {
				$output  = ' <span class="delete wp-admin events-cal">| <a  rel="nofollow" class="submitdelete" href="';
				$output .= wp_nonce_url( $this->getUrl( 'delete', $event->ID ), 'tribe_community_events_delete' );
				$output .= '" onclick="return confirm(\'' . __( 'Are you sure?', 'tribe-events-community' ) . '\')">' . __( 'Delete', 'tribe-events-community' ) . '</a></span>';
				return $output;
			}
		}

		/**
		 * get edit button for an event
		 *
		 * @since 1.0
		 * @author nciske
		 * @param object $event the event object
		 * @param string $label the label for the button
		 * @param string $before what comes before the button
		 * @param string $after what comes after the button
		 * @return string $output the button's output
		 */
		public function getEditButton( $event, $label = 'Edit', $before = '', $after = '' ) {

			if ( !isset( $event->EventStartDate ) )
				$event->EventStartDate = tribe_get_event_meta( $event->ID , '_EventStartDate', true );

			$output  = $before . '<a  rel="nofollow" href="';
			$output .= $this->getUrl( 'edit', $event->ID, null, TribeEvents::POSTTYPE );
			$output .= '"> ' . $label . '</a>' . $after;
			return $output;

		}
		
		/**
		 * Get the featured image delete button
		 *
		 * @since 1.0
		 * @author Paul Hughes
		 * @param $event_id
		 * @return string $output the button's output
		 */
		public function getDeleteFeaturedImageButton( $event ) {
			if ( !isset( $event ) )
				return;
				
			$url = add_query_arg( 'action', 'deleteFeaturedImage', wp_nonce_url( $this->getUrl( 'edit', $event->ID, null, TribeEvents::POSTTYPE ), 'tribe_community_events_featured_image_delete' ) );

			if ( class_exists( 'TribeEventsPro' ) && tribe_is_recurring_event( $event->ID ) ) {
				$url = add_query_arg( 'eventDate', date ('Y-m-d', strtotime( $event->EventStartDate ) ), $url );
			} 
			
			$output = '<a rel="nofollow" class="submitdelete" href="' . $url . '">Delete Image</a>';
			return $output;
		}



		/**
		 * get title for a page
		 *
		 * @since 1.0
		 * @author nciske
		 * @param string $action the action being performed
		 * @param string $post_type the post type being viewed
		 * @return string the title
		 */
		public function getTitle( $action, $post_type ) {

		if ( $action == 'delete' ) {
				switch ( $post_type ) :
					case TribeEvents::POSTTYPE:
						 return __( 'Remove an Event', 'tribe-events-community' );
					break;
					case TribeEvents::VENUE_POST_TYPE:
						return __( 'Remove a Venue', 'tribe-events-community' );
					break;
					case TribeEvents::ORGANIZER_POST_TYPE:
						return __( 'Remove an Organizer', 'tribe-events-community' );
					break;
					default:
						return __( 'Unknown Post Type', 'tribe-events-community' );
					break;
				endswitch;
			} else {
				switch ( $post_type ) :
					case TribeEvents::POSTTYPE:
						 return __( 'Edit an Event', 'tribe-events-community' );
					break;
					case TribeEvents::VENUE_POST_TYPE:
						return __( 'Edit a Venue', 'tribe-events-community' );
					break;
					case TribeEvents::ORGANIZER_POST_TYPE:
						return __( 'Edit an Organizer', 'tribe-events-community' );
					break;
					default:
						return __( 'Unknown Post Type', 'tribe-events-community' );
					break;
				endswitch;
			}

		}

		/**
		 * set context for where we are
		 *
		 * @since 1.0
		 * @author nciske
		 * @param string $action the current action
		 * @param string $post_type the current post type
		 * @param int $id the current id
		 * @return void
		 */
		private function setContext( $action, $post_type, $id ) {

			$this->context = array(
				'title' => $this->getTitle( $action, $post_type ),
				'post_type' => $post_type,
				'action' => $action,
				'id' => $id,
			);

		}

		/**
		 * get context for where we are
		 *
		 * @since 1.0
		 * @author nciske
		 * @param string $action the current action
		 * @param string $tribe_id the current post id
		 * @return string $context the current context
		 */
		public function getContext( $action = null, $tribe_id = null ) {

			// get context from query string
			if ( isset( $_GET['tribe_action'] ) )
			 $action = $_GET['tribe_action'];

			if ( isset( $_GET['tribe_id'] ) )
			 $tribe_id = intval( $_GET['tribe_id'] );

			$tribe_id = intval( $tribe_id );

			if ( isset( $this->context ) )
				return $this->context;

			switch ( $action ) {
				case 'edit':
					if ( $tribe_id )
						$post = get_post( $tribe_id );

					$context = array(
						'title' => $this->getTitle( $action, $post->post_type ),
						'action' => $action,
						'post_type' => $post->post_type,
						'id' => $tribe_id,
					);

				break;

				case 'list':
					$context = array(
						'title' => __( 'My Events', 'tribe-events-community' ),
						'action' => $action,
						'id' => null,
					);
				break;

				case 'delete':

					if ( $tribe_id )
						$post = get_post( $tribe_id );

					$context = array(
						'title' => $this->getTitle( $action, $post->post_type ),
						'post_type' => $post->post_type,
						'action' => $action,
						'id' => $tribe_id,
					);

				break;

				default:
					$context = array(
						'title' => __( 'Submit an Event', 'tribe-events-community' ),
						'action' => 'add',
						'id' => null,
					);
			}

			$this->context = $context;
			return $context;

		}

		/**
		 * set the title for the shortcode
		 *
		 * @since 1.0
		 * @author nciske
		 * @return string the title
		 */
		public function doShortCodeTitle() {

			$action = '';
			$tribe_id = '';

			$context = $this->getContext( $action, $tribe_id );

			return $context['title'];
		}

		/**
		 * output the shortcode's content based on the contet
		 *
		 * @since 1.0
		 * @author nciske
		 * @return string the shortcode's content
		 */
		public function doShortCode() {

			if ( !is_page() || !in_the_loop() || tribe_is_event() )
				return '<p>' . __( 'This shortcode can only be used in pages.', 'tribe-events-community' ) . '</p>';

			$action = '';
			$tribe_id = '';

			$context = $this->getContext( $action, $tribe_id );

			switch ( $context['action'] ) :

				case 'edit':

					if ( $context['post_type'] == TribeEvents::VENUE_POST_TYPE )
						return $this->doVenueForm( $context['id'] );

					if ( $context['post_type'] == TribeEvents::ORGANIZER_POST_TYPE )
						return $this->doOrganizerForm( $context['id'] );

					if ( $context['post_type'] == TribeEvents::POSTTYPE )
						return $this->doEventForm( $context['id'] );

				break;

				case 'list':

					return $this->doMyEvents();

				break;

				case 'delete':

					return $this->doDelete( $context['id'] );

				break;

				case 'add':
				default:

					return $this->doEventForm();

			endswitch;

		}

		/**
		 * unhook content filters from the content
		 *
		 * @since 1.0
		 * @author nciske
		 * @return void
		 */
		public function removeFilters() {

			remove_filter( 'the_content', 'wpautop' );
			remove_filter( 'the_content', 'wptexturize' );

		}

		/**
		 * Set the body classes
		 *
		 * @since 1.0.1
		 * @author PaulHughes01
		 * @return $classes the body classes to add
		 */
		public function setBodyClasses( $classes ) {
			if ( tribe_is_community_my_events_page() )
				$classes[] = 'tribe_community_list';
			
			if ( tribe_is_community_edit_event_page() )
				$classes[] = 'tribe_community_edit';
				
			return $classes;
		}

		/**
		 * find the page id that has the specified shortcode in it
		 *
		 * @since 1.0
		 * @author nciske
		 * @param string $shortcode the shortcode to search for
		 * @return int $id the page id;
		 */
		public function findPageByShortcode( $shortcode ) {

			global $wpdb;
			$id = $wpdb->get_var( $wpdb->prepare( "SELECT id from $wpdb->posts WHERE post_content LIKE '%%%s%%' AND post_type in ('page')", $shortcode ) );
			return $id;

		}

		/**
		 * enqueue scripts & styles
		 *
		 * @since 1.0
		 * @author nciske
		 * @return void
		 */
		public function addScriptsAndStyles() {

			wp_enqueue_style( TribeEvents::POSTTYPE . '-ce-default', $this->pluginUrl . '/resources/tribe-events-community.css' );

			//parent theme style override
			$theme_css_path = '/events/community/tribe-events-community.css';
			if ( file_exists( get_template_directory() . $theme_css_path ) ) {
				wp_enqueue_style( TribeEvents::POSTTYPE . '-ce-custom', get_template_directory_uri() . $theme_css_path );
			}
			//child theme style override
			if ( file_exists( get_stylesheet_directory() . $theme_css_path ) ) {
				wp_enqueue_style( TribeEvents::POSTTYPE . '-ce-custom', get_stylesheet_directory_uri() . $theme_css_path );
			}

		}

		/**
		 * Load the Plugin Update Engine
		 *
		 * @since 1.0
		 * @author nciske
		 * @return void
		 */
		public function helpersLoaded() {

			new PluginUpdateEngineChecker( self::$updateUrl, $this->pluginSlug, array(), plugin_basename( $this->pluginPath . '/tribe-community-events.php' ) );

		}

		/**
		 * Adds the event specific query vars to Wordpress
		 *
		 * @link http://codex.wordpress.org/Custom_Queries#Permalinks_for_Custom_Archives
		 * @since 1.0
		 * @author nciske
		 * @param $qvars array of query variables
		 * @return $qvars filtered array of query variables
		 */
		public function communityEventQueryVars( $qvars ) {
			$qvars[] = 'tribe_event_id';
			$qvars[] = 'tribe_venue_id';
			$qvars[] = 'tribe_organizer_id';
			//$qvars[] = 'tribe_ogranizer_id';
			return $qvars;
		}

		/**
		 * Convert the POST array into an object
		 *
		 * @since 1.0
		 * @author nciske
		 * @return object the POST object
		 */
		public function getInfoFromPost() {
			return (object) $_POST;
		}

		/**
		 * return event start/end hours
		 *
		 * @since 1.0
		 * @author nciske
		 * @param string $hours the event hours
		 * @param string $date the date
		 * @param bool $isStart is it the project start
		 * @return string the event's hours
		 */
		public function getHours( $hours, $date, $isStart ) {

			if ( $isStart ) {
				if ( isset( $_REQUEST[ 'EventStartHour' ] ) )
					$hour = intval( $_REQUEST[ 'EventStartHour' ] );
			} else {
				if ( isset( $_REQUEST[ 'EventEndHour' ] ) )
					$hour = intval( $_REQUEST[ 'EventEndHour' ] );
			}

			if ( isset( $hour ) ) {
				return $hour;
			} else {
				return $hours;
			}

		}

		/**
		 * return event start/end minues
		 *
		 * @since 1.0
		 * @author nciske
		 * @param string $minutes the event minutes
		 * @param string $date the date
		 * @param bool $isStart is it the project start
		 * @return string the event's minutes
		 */
		public function getMinutes( $minutes, $date, $isStart ) {

			if ( $isStart ) {
				if ( isset( $_REQUEST[ 'EventStartMinute' ] ) )
					$minute = intval( $_REQUEST[ 'EventStartMinute' ] );
			} else {
				if ( isset( $_REQUEST[ 'EventEndMinute' ] ) )
					$minute = intval( $_REQUEST[ 'EventEndMinute' ] );
			}

			if ( isset( $minute ) ) {
				return $minute;
			} else {
				return $minutes;
			}

		}

		/**
		 * return event start/end meridian
		 *
		 * @since 1.0
		 * @author nciske
		 * @param string $meridians the event meridians
		 * @param string $date the date
		 * @param bool $isStart is it the project start
		 * @return string the event's meridian
		 */
		public function getMeridians( $meridians, $date, $isStart ) {

			if ( $isStart ) {
				if ( isset( $_REQUEST[ 'EventStartMeridian' ] ) )
					$meridian = $_REQUEST[ 'EventStartMeridian' ];
			} else {
				if ( isset( $_REQUEST[ 'EventEndMeridian' ] ) )
					$meridian = $_REQUEST[ 'EventEndMeridian' ];
			}

			if ( isset( $meridian ) ) {
				return $meridian;
			} else {
				return $meridians;
			}
		}

		/**
		 * send email alert to email list when an event is submitted
		 *
		 * @since 1.0
		 * @author nciske
		 * @param int $tribe_event_id the event ID
		 * @return void
		 */
		public function sendEmailAlerts( $tribe_event_id ) {

			$post = get_post( $tribe_event_id );

			$subject = sprintf( __( '[%s] Community Events Submission:', 'tribe-events-community' ), wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) ) . ' "' . $post->post_title . '"';

			$message = '<html><body>';
			$message .= '<h2>' . $post->post_title . '</h2>';
			$message .= '<h4>' . tribe_get_start_date( $tribe_event_id ) . ' - ' . tribe_get_end_date( $tribe_event_id ) .'</h4>';
			$message .= '<hr />';
			$message .= '<h3>' . __( 'Event Organizer', 'tribe-events-community' ) . '</h3><p>' . tribe_get_organizer( tribe_get_event_meta( $post->ID, '_EventOrganizerID', true ) ) . '</p>';
			$message .= '<h3>' . __( 'Event Venue', 'tribe-events-community' ) . '</h3><p>' . tribe_get_venue( tribe_get_event_meta( $post->ID, '_EventVenueID', true ) ) . '</p>';
			$message .= __( '<h3>Description</h3>','tribe-events-community' ) . "\r\n" . $post->post_content;
			$message .= '<hr /><h4>' . $this->getEditButton( $post, __( 'Review Event', 'tribe-events-community' ) );

			if ( $post->post_status == 'publish' )
				$message .= ' | <a href="' . get_permalink( $tribe_event_id ) . '">View Event</a>';

			$message .= '</h4></body></html>';

			$headers = array( 'Content-Type: text/html' );
			$h = implode( "\r\n", $headers ) . "\r\n";

			if ( is_array( $this->emailAlertsList ) ) {
				foreach ( $this->emailAlertsList as $email ) {
					wp_mail( trim( $email ), $subject, $message, $h );
				}
			}

		}

		/**
		 * searches current user's events for the event closest to
		 * today but not in the past, and returns the 'page' that event is on
		 *
		 * @since 1.0
		 * @author nciske
		 * @return object $todaysPage the page object
		 */
		public function findTodaysPage() {

			if ( WP_DEBUG ) delete_transient( 'tribe_community_events_today_page' );
			$todaysPage = get_transient( 'tribe_community_events_today_page' );

			$todaysPage = null;

			if ( !$todaysPage ) {
				$current_user = wp_get_current_user();
				if ( is_object( $current_user ) && !empty( $current_user->ID ) ) {
					$args = array(
						'posts_per_page' => -1,
						'paged' => 0,
						'nopaging' => true,
						'author' => $current_user->ID,
						'post_type' => TribeEvents::POSTTYPE,
						'post_status' => 'any',
						'order' => 'ASC',
						'orderby' => 'meta_value',
						'meta_key' => '_EventStartDate',
						'meta_query' => array(
							'key' => '_EventStartDate',
							'value' => date( 'Y-m-d 00:00:00' ),
							'compare' => '<=',
						),
					);

					$tp = new WP_Query( $args );

					$pc = $tp->post_count;

					unset( $tp );

					$todaysPage = floor( $pc / $this->eventsPerPage );

					//handle bounds
					if ( $todaysPage <= 0 )
						$todaysPage = 1;

					set_transient( 'tribe-community-events_today_page', $todaysPage, 60 * 60 * 1 ); //cache for an hour
				}
			}

			return $todaysPage;

		}


		/**
		 * delete view for an event
		 *
		 * @since 1.0
		 * @author nciske
		 * @param int $tribe_event_id the event's ID
		 * @return string $output the deletion view
		 */
		public function doDelete( $tribe_event_id ) {

			if ( isset( $_GET['eventDate'] ) )
				$eventDate = date( 'Y-m-d', strtotime( $_GET['eventDate'] ) );

			if ( isset( $_GET['deleteAll'] ) )
				$deleteAll = true;

			$current_user = wp_get_current_user();

			if ( $this->userCanEdit( $tribe_event_id, TribeEvents::POSTTYPE ) && wp_verify_nonce( $_GET['_wpnonce'] , 'tribe_community_events_delete' ) && $this->allowUsersToDeleteSubmissions ) {
				//does this event even exist?
				$event = get_post( $tribe_event_id );

				if ( isset( $event->ID ) ) {
					if ( ( isset( $deleteAll ) && $deleteAll ) || !isset( $eventDate ) ) {
						if ( $this->trashItemsVsDelete ) {
							wp_trash_post( $tribe_event_id );
							$this->message = __( 'Trashed Event #', 'tribe-events-community' ) . $tribe_event_id;
						} else {
							wp_delete_post( $tribe_event_id, true );
							$this->message = __( 'Deleted Event #', 'tribe-events-community' ) . $tribe_event_id;
						}
					} else {
						$date = $eventDate;

						$startDate = TribeEvents::getRealStartDate( $tribe_event_id );
						$date = TribeDateUtils::addTimeToDate( $date, TribeDateUtils::timeOnly( $startDate ) );
						delete_post_meta( $tribe_event_id, '_EventStartDate', $date );

						$this->message = sprintf( __( 'Removed occurence %s from Event #', 'tribe-events-community' ) . $tribe_event_id , $eventDate );
					}
				} else {
					$this->message = sprintf( __( 'This event (#%s) does not appear to exist.', 'tribe_community_events_delete' ), $tribe_event_id );
				}
			} else {
				$this->message = __( 'You do not have permission to delete this event.', 'tribe-events-community' );
			}

			$output = '<div id="tribe-community-events" class="delete">';

			ob_start();
			include $this->getTemplatePath( 'views', 'delete.php' );
			$output .= ob_get_clean();

			$output .= '<a href="javascript:history.go(-1);">&laquo; ' . _x( 'Back', 'As in "go back to previous page"', 'tribe-events-community' ) . '</a>';

			$output .= '</div>';

			return $output;

		}

		/**
		 * event editing form
		 *
		 * @since 1.0
		 * @author nciske
		 * @param int $id the event's ID
		 * @return string $output the deletion view
		 */
		public function doEventForm( $id = null ) {

			// venue and organizer defaults- override ECP defaults
			add_filter( 'tribe_display_event_venue_dropdown_id', array( $this, 'tribe_display_event_venue_dropdown_id' ) );
			add_filter( 'tribe_display_event_organizer_dropdown_id', array( $this, 'tribe_display_event_organizer_dropdown_id' ) );

			add_filter( 'tribe-post-origin', array( $this, 'filterPostOrigin' ) );

			$output    = '';
			$show_form = true;
			$event     = null;

			if ( $id ) {
				$edit = true;
				$tribe_event_id = $id;
			} else {
				$edit = false;
				$tribe_event_id = null;
			}

			if ( class_exists( 'TribeEventsPro' ) && tribe_is_recurring_event( $id ) ) {
				$this->message = sprintf( __('%sWarning:%s You are editing a recurring event. All changes will be applied to the entire series.', 'tribe-events-community' ), '<b>', '</b>' );
				$this->messageType = 'error';
			}

			// Delete the featured image, if there was a request to do so.
			if ( isset( $_GET['action'] ) && $_GET['action'] == 'deleteFeaturedImage' && wp_verify_nonce( $_GET['_wpnonce'] , 'tribe_community_events_featured_image_delete' ) ) {
				$featured_image_id = get_post_thumbnail_id( $tribe_event_id );
				delete_post_meta( $tribe_event_id, '_thumbnail_id' );
				wp_delete_attachment( $featured_image_id, true );
			}

			if ( $edit && $tribe_event_id ) {
				$event = get_post( intval( $tribe_event_id ) );

				global $post;

				$old_post = $post;
				$post = $event;
			}

			if ( $edit && ( !$tribe_event_id || !isset( $event->ID ) ) ) {
				$this->message     = __( 'Event not found.', 'tribe-events-community' );
				$this->messageType = 'error';
				$output = $this->outputMessage( $this->messageType, false );
				$show_form         = false;
			}

			// login check
			if ( ( !$this->allowAnonymousSubmissions && !is_user_logged_in() ) || ( $edit && $tribe_event_id && !is_user_logged_in() ) ) {
				$output .= '<p>' . __( 'Please log in first.', 'tribe-events-community' ) . '</p>';
				$output .= wp_login_form( array( 'echo' => false ) );
				$output .= '<div class="register">';
				$output .= wp_register( '', '', false );
				$output .= '</div>';
				return $output;
			}

			// security check
			if ( $edit && $tribe_event_id && !$this->userCanEdit( $tribe_event_id, TribeEvents::POSTTYPE ) ) {
				$output .= '<p>' . __( 'You do not have permission to edit this event.', 'tribe-events-community' ) . '</p>';
				return $output;
			}

			$this->loadScripts = true;
			$output .= '<div id="tribe-community-events" class="form">';

			if ( $this->allowAnonymousSubmissions || is_user_logged_in() ) {
				$current_user = wp_get_current_user();

				if ( class_exists( 'TribeEventsPro' ) )
					$tribe_ecp = TribeEventsPro::instance();

				if ( ( isset( $_POST[ 'community-event' ] ) && $_POST[ 'community-event' ] ) && check_admin_referer( 'ecp_event_submission' ) ) {
					$_POST[ 'ID' ]           = $tribe_event_id;
					$_POST[ 'post_content' ] = $_POST[ 'tcepostcontent' ]; //wp_editor doesn't support underscores in the id

					if ( $tribe_event_id && !empty( $_POST[ 'post_content' ] ) && !empty( $_POST[ 'post_title' ] ) ) {
						if ( $this->saveEvent( $tribe_event_id ) ) {
							$this->message = __( 'Event updated.', 'tribe-events-community' );
							$this->message .= ' ' . $this->getEditButton( get_post( $tribe_event_id ), __( 'Edit event', 'tribe-events-community' ) );
							$this->message .= ' | <a href="' . $this->getUrl( 'add' ) . '">' . __( 'Submit another event', 'tribe-events-community' ) . '</a>';
							$this->messageType = '';
							$show_form         = false;

							delete_transient( 'tribe_community_events_today_page' ); //clear cache
						} else {
							$this->message     = __( 'There was a problem saving your event, please try again.', 'tribe-events-community' );
							$this->messageType = 'error';
						}
					} else {
						if ( is_user_logged_in() || ( isset( $_POST[ 'naes' ] ) && $_POST[ 'naes' ] == 1 && empty( $_POST[ 'aes' ] ) ) ) {
							$_POST[ 'post_status' ] = $this->defaultStatus;

							// fix inconsistent field naming
							if ( isset( $_POST['organizer'] ) && !isset( $_POST['Organizer'] ) )
								$_POST['Organizer'] = $_POST['organizer'];
							if ( isset( $_POST['venue'] ) && !isset( $_POST['Venue'] ) )
								$_POST['Venue'] = $_POST['venue'];
							if ( !empty( $_POST[ 'post_content' ] ) && !empty( $_POST[ 'post_title' ] ) )
								$tribe_event_id = $this->createEvent();

							if ( $tribe_event_id ) {
								$this->message = __( 'Event submitted.', 'tribe-events-community' );

								if ( $this->allowUsersToEditSubmissions && is_user_logged_in() )
									$this->message .= ' ' . $this->getEditButton( get_post( $tribe_event_id ), __( 'Edit event', 'tribe-events-community' ) );

								$this->message .= ' | <a href="' . $this->getUrl( 'add' ) . '">' . __( 'Submit another event', 'tribe-events-community' ) . '</a>';

								// email alerts
								if ( $this->emailAlertsEnabled )
									$this->sendEmailAlerts( $tribe_event_id );

								// $output .= $this->outputMessage('',false);

								$show_form = false;
							} else {
								if ( empty( $_POST[ 'post_content' ] ) && empty( $_POST[ 'post_title' ] ) ) {
									$this->message     = __( 'Event title and description are required.', 'tribe-events-community' );
									$this->messageType = 'error';
								} elseif ( empty( $_POST[ 'post_content' ] ) ) {
									$this->message     = __( 'Event description is required.', 'tribe-events-community' );
									$this->messageType = 'error';
								} elseif ( empty( $_POST[ 'post_title' ] ) ) {
									$this->message     = __( 'Event title is required.', 'tribe-events-community' );
									$this->messageType = 'error';
								} else {
									$this->message     = __( 'There was a problem submitting your event, please try again.', 'tribe-events-community' );
									$this->messageType = 'error';
								}

								//get event info from POST
								$event = $this->getInfoFromPost();
							}
						} else {
							if ( ( isset( $_POST[ 'naes' ] ) && $_POST[ 'naes' ] != 1 ) || !isset( $_POST[ 'naes' ] ) ) {
								$this->message     = __( 'Please check the box below to prove you are not an evil spammer.', 'tribe-events-community' );
								$this->messageType = 'error';
							} else {
								$this->message     = __( 'There was a problem submitting your event, please try again.', 'tribe-events-community' );
								$this->messageType = 'error';
							}

							// get event info from POST
							$event = $this->getInfoFromPost();
						}
					}
				}

				// are we editing an event?

				if ( isset( $tribe_event_id ) && $edit ) {
					// global $post;
					// $event = get_post(intval($tribe_event_id));
				} else {
					// $event = new Object;
					if ( isset( $_POST[ 'post_title' ] ) )
						$event->post_title = $_POST[ 'post_title' ];
					if ( isset( $_POST[ 'post_content' ] ) )
						$event->post_content = $_POST[ 'post_content' ];
				}

				$current_user = wp_get_current_user();
				if ( is_user_logged_in() && $this->allowUsersToEditSubmissions )
					$output .= '<div id="my-events"><a href="' . $this->getUrl( 'list' ) . '" class="button">' . __( 'My Events', 'tribe-events-community' ) . '</a></div>';

				if ( is_user_logged_in() )
					$output .= '<div id="not-user">' . __( 'Not', 'tribe-events-community' ) . ' <i>' . $current_user->display_name . '</i>? <a href="' . wp_logout_url( get_permalink() ) . '">' . __( 'Log Out', 'tribe-events-community' ) . '</a></div>';

				if ( $this->allowUsersToEditSubmissions || is_user_logged_in() )
					$output .= '<div style="clear:both"></div>';
				$output .= $this->outputMessage( $this->messageType, false );

				$show_form = apply_filters( 'tribe_community_events_show_form', $show_form );

				if ( $show_form ) {
					//get data from $_POST and override core function
					add_filter( 'tribe_get_hour_options', array( $this, 'getHours' ), 10, 3 );
					add_filter( 'tribe_get_minute_options', array( $this, 'getMinutes' ), 10, 3 );
					add_filter( 'tribe_get_meridian_options', array( $this, 'getMeridians' ), 10, 3 );


					//turn off upsell -- this is public after all
					remove_action( 'tribe_events_cost_table', array( TribeEvents::instance(), 'maybeShowMetaUpsell' ) );

					if ( class_exists( 'Event_Tickets_PRO' ) ) {
						// Remove the eventbrite method hooked into the event form, if it exists.
						remove_action( 'tribe_events_cost_table', array( Event_Tickets_PRO::instance(), 'eventBriteMetaBox' ), 1 );
					}

					//filter include paths to redirect to local view files
					add_filter( 'tribe_events_meta_box_template', array( $this, 'tribe_community_events_meta_box_template' ) );
					add_filter( 'tribe_events_venue_meta_box_template', array( $this, 'tribe_community_events_venue_meta_box_template' ) );
					add_filter( 'tribe_events_organizer_meta_box_template', array( $this, 'tribe_community_events_organizer_meta_box_template' ) );
					add_filter( 'tribe_events_event_meta_template', array( $this, 'tribe_community_events_event_meta_template' ) );

					if ( TribeEvents::ecpActive() ) {
						remove_action( 'tribe_events_date_display', array( 'TribeEventsRecurrenceMeta', 'loadRecurrenceData' ) );
						add_action( 'tribe_events_date_display', array( $this, 'loadRecurrenceData' ) );
					}

					ob_start();
					include $this->getTemplatePath( 'views', 'event-form.php' );

					// pops up dialog for recurrring events when editing/deleting
					include $this->getTemplatePath( 'views', 'recurrence-dialog.php' );

					$output .= ob_get_clean();
				}
				$output .= '</div>';
			}

			wp_reset_query();
			remove_filter( 'tribe-post-origin', array( $this, 'filterPostOrigin' ) );

			return $output;

		}

		/**
		 * main form for events
		 *
		 * @since 1.0
		 * @author nciske
		 * @param int $tribe_event_id the event's ID
		 * @return string $output the form
		 */
		public function doVenueForm( $tribe_venue_id ) {

			$output = '';

			add_filter( 'tribe-post-origin', array( $this, 'filterPostOrigin' ) );


			if ( !$this->allowUsersToEditSubmissions || !TribeEvents::ecpActive() )
				return __( 'This feature is not currently enabled.', 'tribe-events-community' );

			//$tribe_venue_id = $this->getVenueId();

			if ( $tribe_venue_id && !$this->userCanEdit( $tribe_venue_id, TribeEvents::VENUE_POST_TYPE ) ) {
				$output .= '<p>' . __( 'You do not have permission to edit this venue.', 'tribe-events-community' ) . '</p>';
				return;
			}

			$this->loadScripts = true;
			$output .= '<div id="tribe-community-events" class="form venue">';

			if ( is_user_logged_in() ) {
				$current_user = wp_get_current_user();

				if ( class_exists( 'TribeEventsPro' ) )
					$tribe_ecp = TribeEventsPro::instance();

				if ( $tribe_venue_id ) {
					if ( ( isset( $_POST[ 'community-event' ] ) && $_POST[ 'community-event' ] ) && check_admin_referer( 'ecp_venue_submission' ) ) {
						if ( isset( $_POST[ 'post_title' ] ) && $_POST[ 'post_title' ] ) {
							$_POST[ 'Venue' ] = $_POST[ 'post_title' ];

							wp_update_post( array(
								'post_title' => $_POST[ 'Venue' ],
								'ID' => $tribe_venue_id,
								'post_content' => $_POST[ 'tcepostcontent' ],
							) );

							TribeEventsAPI::updateVenue( $tribe_venue_id, $_POST );

							$this->message = __( 'Venue updated.', 'tribe-events-community' );
							/*
							// how it should work, but updateVenue does not return a boolean
							if ( TribeEventsAPI::updateVenue($tribe_venue_id, $_POST) ) {
							$this->message = __("Venue updated.",'tribe-events-community');
							}else{
							$this->message = __("There was a problem saving your venue, please try again.",'tribe-events-community');
							$this->messageType = 'error';
							}
							*/
						} else {
							$this->message     = __( 'Venue name cannot be blank.', 'tribe-events-community' );
							$this->messageType = 'error';
						}
					} else {
						if ( isset( $_POST[ 'community-event' ] ) ) {
							$this->message     = __( 'There was a problem updating your venue, please try again.', 'tribe-events-community' );
							$this->messageType = 'error';
						}
					}
				} else {
					return '<p>' . __( 'Venue not found.', 'tribe-events-community' ) . '</p>';
				}

				// are we editing a venue?

				if ( isset( $tribe_venue_id ) ) {
					global $post;
					$venue = get_post( intval( $tribe_venue_id ) );
				} else {
					// $event = new Object;
					if ( isset( $_POST[ 'post_title' ] ) )
						$venue->post_title = $_POST[ 'post_title' ];
					if ( isset( $_POST[ 'tcepostcontent' ] ) )
						$venue->post_content = $_POST[ 'tcepostcontent' ];
				}

				if ( is_user_logged_in() && $this->allowUsersToEditSubmissions )
					$output .= '<div id="my-events"><a href="' . $this->getUrl( 'list' ) . '" class="button">' . __( 'My Events', 'tribe-events-community' ) . '</a></div>';

				if ( is_user_logged_in() )
					$output .= '<div id="not-user">' . __( 'Not', 'tribe-events-community' ) . ' <i>' . $current_user->display_name . '</i>? <a href="' . wp_logout_url( get_permalink() ) . '">' . __( 'Log Out', 'tribe-events-community' ) . '</a></div>';

				if ( $this->allowUsersToEditSubmissions || is_user_logged_in() )
					$output .= '<div style="clear:both"></div>';

				$output .= $this->outputMessage( $this->messageType, false );

				ob_start();
				include $this->getTemplatePath( 'views', 'venue-form.php' );

				// pops up dialog for recurrring events when editing/deleting
				include $this->getTemplatePath( 'views', 'recurrence-dialog.php' );

				$output .= ob_get_clean();

				wp_reset_query();
			} else {
				$output .= '<p>' . __( 'Please log in to edit this venue', 'tribe-events-community' ) . '</p>';
				wp_login_form();
				$output .= '<div class="register">';
				wp_register( '', '', true );
				$output .= '</div>';
			}

			$output .= '</div>';

			remove_filter( 'tribe-post-origin', array( $this, 'filterPostOrigin' ) );
			return $output;

		}


		/**
		 * organizer form for events
		 *
		 * @since 1.0
		 * @author nciske
		 * @param int $tribe_organizer_id the organizer's ID
		 * @return string $output the form
		 */
		public function doOrganizerForm( $tribe_organizer_id ) {

			add_filter( 'tribe-post-origin', array( $this, 'filterPostOrigin' ) );

			$output = '';

			if ( !$this->allowUsersToEditSubmissions || !TribeEvents::ecpActive() )
				return __( 'This feature is not currently enabled.', 'tribe-events-community' );

			if ( $tribe_organizer_id && !$this->userCanEdit( $tribe_organizer_id, TribeEvents::ORGANIZER_POST_TYPE ) ) {
				$output .= '<p>' . __( 'You do not have permission to edit this organizer.', 'tribe-events-community' ) . '</p>';
				return;
			}

			$this->loadScripts = true;
			$output .= '<div id="tribe-community-events" class="form organizer">';

			if ( is_user_logged_in() ) {
				$current_user = wp_get_current_user();

				if ( class_exists( 'TribeEventsPro' ) )
					$tribe_ecp = TribeEventsPro::instance();

				if ( $tribe_organizer_id ) {
					if ( ( isset( $_POST[ 'community-event' ] ) && $_POST[ 'community-event' ] ) && check_admin_referer( 'ecp_organizer_submission' ) ) {
						if ( isset( $_POST[ 'organizer' ][ 'Organizer' ] ) && $_POST[ 'organizer' ][ 'Organizer' ] ) {
							//$_POST['ID'] = $tribe_organizer_id;
							$_POST[ 'Organizer' ] = $_POST[ 'organizer' ][ 'Organizer' ];

							TribeEventsAPI::updateOrganizer( $tribe_organizer_id, $_POST );
							$this->message = __( 'Organizer updated.', 'tribe-events-community' );

							/*
							// how it should work, but updateOrganizer does not return a boolean
							if ( TribeEventsAPI::updateOrganizer($tribe_organizer_id, $_POST) ) {
							$this->message = __("Organizer updated.",'tribe-events-community');
							}else{
							$this->message = __("There was a problem saving your organizer, please try again.",'tribe-events-community');
							$this->messageType = 'error';
							}
							*/
						} else {
							$this->message     = __( 'Organizer name cannot be blank.', 'tribe-events-community' );
							$this->messageType = 'error';
						}
					} else {
						if ( isset( $_POST[ 'community-event' ] ) ) {
							$this->message     = __( 'There was a problem updating this organizer, please try again.', 'tribe-events-community' );
							$this->messageType = 'error';
						}
					}
				} else {
					return '<p>' . __( 'Organizer not found.', 'tribe-events-community' ) . '</p>';
				}

				// are we editing an organizer?

				if ( isset( $tribe_organizer_id ) ) {
					global $post;
					//$this_post = $post;
					$organizer = get_post( intval( $tribe_organizer_id ) );
				} else {
					//$event = new Object;
					if ( isset( $_POST[ 'post_title' ] ) )
						$organizer->post_title = $_POST[ 'post_title' ];
					if ( isset( $_POST[ 'post_content' ] ) )
						$organizer->post_content = $_POST[ 'post_content' ];
				}

				ob_start();
				include $this->getTemplatePath( 'views', 'organizer-form.php' );

				$output .= ob_get_clean();
			} else {
				$output .= '<p>' . __( 'Please log in to edit this organizer', 'tribe-events-community' ) . '</p>';
				$output .= wp_login_form( 'echo=false' );
				$output .= '<div class="register">';
				$output .= wp_register( '', '', false );
				$output .= '</div>';
			}

			$output .= '</div>';

			remove_filter( 'tribe-post-origin', array( $this, 'filterPostOrigin' ) );

			return $output;

		}

		/**
		 * show the current user's events
		 *
		 * @since 1.0
		 * @author nciske
		 * @param int $page pagination
		 * @return string $output the page
		 */
		public function doMyEvents( $page = null ) {
			$output = '';
			
			if ( !$this->allowUsersToEditSubmissions )
				return __( 'This feature is not currently enabled.', 'tribe-events-community' );

			$this->loadScripts = true;
			$output .= '<div id="tribe-community-events" class="list">';

			if ( is_user_logged_in() ) {
				if ( class_exists( 'TribeEventsPro' ) )
					$tribe_ecp = TribeEventsPro::instance();

				$current_user = wp_get_current_user();

				global $paged;

				if ( empty( $paged ) && !empty( $page ) )
					$paged = $page;

				$args = array(
					'posts_per_page' => $this->eventsPerPage,
					'paged' => $paged,
					'author' => $current_user->ID,
					'post_type' => TribeEvents::POSTTYPE,
					'post_status' => 'any',
					'order' => 'ASC',
					'orderby' => 'meta_value',
					'meta_key' => '_EventStartDate',
				);

				add_filter( 'post_limits', array( $this, 'limitQuery' ) );

				$events = new WP_Query( $args );

				remove_filter( 'post_limits', array( $this, 'limitQuery' ) );

				ob_start();
				include $this->getTemplatePath( 'views', 'my-events.php' );
				include $this->getTemplatePath( 'views', 'recurrence-dialog.php' );
				$output .= ob_get_clean();

				wp_reset_query();
			} else {
				$output .= '<p>' . __( 'Please log in to view your events', 'tribe-events-community' ) . '</p>';
				$output .= wp_login_form( 'echo=false' );
				$output .= '<div class="register">';
				$output .= wp_register( '', '', false );
				$output .= '</div>';
			}

			$output .= '</div>';

			return $output;

		}

		/**
		 * create an event
		 *
		 * @since 1.0
		 * @author nciske
		 * @return int $tribe_event_id the created event's ID
		 */
		public function createEvent() {

			// The following block of code is taken from the events calendar code that it uses to prepare the data of venue and organizer for saving.
			$_POST['Venue'] = stripslashes_deep( $_POST['venue'] );
			$_POST['Organizer'] = stripslashes_deep( $_POST['organizer'] );
			
			unset( $_POST['venue'] );
			unset( $_POST['organizer'] );

			if( isset($_POST['Venue']['VenueID']) && !empty($_POST['Venue']['VenueID']) && class_exists('TribeEventsPro') )
				$_POST['Venue'] = array('VenueID' => intval($_POST['Venue']['VenueID']));

			if( isset($_POST['Organizer']['OrganizerID']) && !empty($_POST['Organizer']['OrganizerID']) && class_exists('TribeEventsPro') )
				$_POST['Organizer'] = array('OrganizerID' => intval($_POST['Organizer']['OrganizerID']));
				
				
			$tribe_event_id = TribeEventsAPI::createEvent( $_POST );

			if ( isset( $_FILES['event_image']['name'] ) && !empty( $_FILES['event_image']['name'] ) ) {
				$newupload = $this->insert_attachment( 'event_image', $tribe_event_id, true );
			}

			return $tribe_event_id;

		}

		/**
		 * save an existing event
		 *
		 * @since 1.0
		 * @author nciske
		 * @return int $saved the saved event's ID
		 */
		public function saveEvent( $tribe_event_id ) {
			
			// The following block of code is taken from the events calendar code that it uses to prepare the data of venue and organizer for saving.
			$_POST['Venue'] = stripslashes_deep( $_POST['venue'] );
			$_POST['Organizer'] = stripslashes_deep( $_POST['organizer'] );

			if( isset($_POST['Venue']['VenueID']) && !empty($_POST['Venue']['VenueID']) && class_exists('TribeEventsPro') )
				$_POST['Venue'] = array('VenueID' => intval($_POST['Venue']['VenueID']));

			if( isset($_POST['Organizer']['OrganizerID']) && !empty($_POST['Organizer']['OrganizerID']) && class_exists('TribeEventsPro') )
				$_POST['Organizer'] = array('OrganizerID' => intval($_POST['Organizer']['OrganizerID']));
				
			// Assign the organizer/venue ID if one was not sent in the postdata.
			if ( $_POST['Venue']['VenueID'] == 0 && tribe_get_venue_id( $tribe_event_id ) > 0 ) {
				$_POST['Venue']['VenueID'] = tribe_get_venue_id( $tribe_event_id );
				$_POST['venue']['VenueID'] = tribe_get_venue_id( $tribe_event_id );
			}
			
			if ( $_POST['Organizer']['OrganizerID'] == 0 && tribe_get_organizer_id( $tribe_event_id ) > 0 ) {
				$_POST['Organizer']['OrganizerID'] = tribe_get_organizer_id( $tribe_event_id );
				$_POST['organizer']['OrganizerID'] = tribe_get_organizer_id( $tribe_event_id );
			}
			
			$saved = TribeEventsAPI::updateEvent( $tribe_event_id, $_POST );

			// overwrite image?
			if ( isset( $_FILES['event_image']['name'] ) && !empty( $_FILES['event_image']['name'] ) ) {
				$newupload = $this->insert_attachment( 'event_image', $tribe_event_id, true );
			}

			return $saved;
		}


		/**
		 * insert an attachment
		 *
		 * @since 1.0
		 * @author nciske
		 * @link http://voodoopress.com/including-images-as-attachments-or-featured-image-in-post-from-front-end-form/
		 * @param array $file_handler the upload
		 * @param int $post_id the post to attach the upload to
		 * @param string $setthumb to set or not to set the thumb
		 * @return $attach_id the attachment's ID
		 */
		public function insert_attachment( $file_handler, $post_id, $setthumb = 'false' ) {

			// check to make sure its a successful upload
			if ( $_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK ) __return_false();
			$uploaded_file_type = wp_check_filetype( basename( $_FILES[$file_handler]['name'] ) );
			$attach_id = false;
			
			require_once( ABSPATH . 'wp-admin' . '/includes/image.php' );
			require_once( ABSPATH . 'wp-admin' . '/includes/file.php' );
			require_once( ABSPATH . 'wp-admin' . '/includes/media.php' );
			
			$allowed_file_types = array( 'image/jpg', 'image/jpeg', 'image/gif', 'image/png' );
			if ( in_array( $uploaded_file_type['type'], $allowed_file_types ) )
				$attach_id = media_handle_upload( $file_handler, $post_id );
			else
				echo "Not a valid image.";

			if ( $attach_id != false && $setthumb == true ) 
				update_post_meta( $post_id, '_thumbnail_id', $attach_id);
			
			return $attach_id;
		}


		/**
		 * honeypot to prevent spam
		 *
		 * @since 1.0
		 * @author nciske
		 * @return void
		 */
		public function formSpamControl() {

			if ( ! is_user_logged_in() ) {
				// add honeypot for anon submissions
				$class = " ";
				if ( ( ( isset( $_POST[ 'naes' ] ) && $_POST[ 'naes' ] != 1 ) ) || ( $_POST && !isset( $_POST[ 'naes' ] ) ) )
					$class = ' error';

				echo '<p><input type="checkbox" name="naes" value="1"> <label class="naes' . $class . '">' . __( 'I am not an evil spammer', 'tribe-events-community' ) . '</label></p>';
				echo '<input type="text" style="display: none;" name="aes" value="">';
			}
		}

		/**
		 * display event details
		 *
		 * @since 1.0
		 * @author nciske
		 * @uses TribeEvents::EventsChooserBox()
		 * @return void
		 */
		public function formEventDetails( $event = null ) {
			$tec = TribeEvents::instance();

			// TEC doesn't like an empty $post object
			if ( !$event ) {
				global $post;
				if ( isset( $post->ID ) ) {
					$old_post_id = $post->ID;
				}
				$post->ID = 0;
				$post->post_type = TribeEvents::POSTTYPE;
			}

			if ( isset( $event->ID ) && $event->ID ) {
				$tec->EventsChooserBox( $event );
			} else {
				$tec->EventsChooserBox();
			}
			if ( !$event ) {
				if ( isset( $old_post_id ) ) {
					$post->ID = $old_post_id;
				}
			}
		}

		/**
		 * form event title
		 *
		 * @since 1.0
		 * @author nciske
		 * @param object $event the event to display the tile for
		 * @return void
		 */
		public function formTitle( $event = null ) {
				?>
				<input type="text" tabindex="<?php $this->tabIndex(); ?>" name="post_title" value="<?php echo ( isset( $event->post_title ) ) ? esc_html( stripslashes( $event->post_title ) ) : ''; ?>"/>
				<?php
		}

		/**
		 * form event conent
		 *
		 * @since 1.0
		 * @author nciske
		 * @param object $event the event to display the tile for
		 * @return void
		 */
		public function formContentEditor( $event = null ) {
			// if the admin wants the rich editor, and they are using WP 3.3, show the WYSIWYG, otherwise default to just a text box
			if ( $this->useVisualEditor && function_exists( 'wp_editor' ) ) {
				$settings = array(
					'wpautop' => true,
					'media_buttons' => false,
					'editor_class' => 'frontend',
					'textarea_rows' => 5,
					'tabindex' => $this->getTabIndex(),
				);

				if ( isset( $event->post_content ) ) {
					wp_editor( stripslashes( $event->post_content ), 'tcepostcontent', $settings );
				} else {
					wp_editor( '', 'tcepostcontent', $settings );
				}
			} else {
				?><textarea tabindex="<?php $this->tabIndex(); ?>" name="tcepostcontent"><?php
				if ( isset( $event->post_content ) )
					echo esc_textarea( $event->post_content );
				?></textarea><?php
			}


		}

		/**
		 * form category dropdown
		 *
		 * @since 1.0
		 * @author nciske, Paul Hughes
		 * @param object $event the event to display the tile for
		 * @return void
		 */
		public function formCategoryDropdown( $event = null ) {
			$event_cat_ids = array();
			if ( $event ) {
				$event_cats = wp_get_object_terms( $event->ID, TribeEvents::TAXONOMY );
				foreach ( $event_cats as $event_cat ) {
					$event_cat_ids[] = $event_cat->term_id;
				}
			} else {
				$event_cats = array();
			}
			$args = array(
				'hide_empty' => false,
				'orderby' => 'name',
				'order' => 'ASC',
				'exclude' => $event_cat_ids,
			);
			echo '<div id="event-categories">';
			echo '<ul>';
			// Could have used wp_terms_checklist(), but it didn't have the customizability required.
			$cats = get_terms( TribeEvents::TAXONOMY, $args );
			$cats = array_merge( $event_cats, $cats );
			$i = 0;
			foreach ( $cats as $cat ) {
				// Show the first 9 categories.
				if ( $i < 9 ) {
					echo '<li id="tribe_events_cat-' . $cat->term_id . '"><label class="selectit">';
				} else {
					echo '<li id="tribe_events_cat-' . $cat->term_id . '" class="hidden_category"><label class="selectit">';
				}
				$i++;
				if ( in_array( $cat->term_id, $event_cat_ids ) ) {
					echo '<input value="' . $cat->term_id . '" type="checkbox" name="tax_input[tribe_events_cat][]" id="in-tribe_events_cat-' . $cat->term_id . '" checked="checked">' . $cat->name . '</label></li>';
				} else {
					echo '<input value="' . $cat->term_id . '" type="checkbox" name="tax_input[tribe_events_cat][]" id="in-tribe_events_cat-' . $cat->term_id . '">' . $cat->name . '</label></li>';
				}
			}
			echo '</ul>';
			echo '</div>';
			if ( count( $cats ) > 9 ) {
				echo '<span style="display:block; text-align:center;"><a id="show_hidden_categories" href="">Show all categories (' . count( $cats ) . ')</a></span>';
			}
		}

		/**
		 * display status icon
		 *
		 * @since 1.0
		 * @author nciske
		 * @param string $status the status
		 * @return string the status image element
		 */
		function getEventStatusIcon( $status ) {
			$icon = str_replace( ' ', '-', $status ) . '.png';

			if ( $status == 'publish' ) {
				$status = "Published";
			}
			if ( file_exists( get_stylesheet_directory() . '/events/community/' . $icon ) ) {
				return '<img width="16" height="16" src="' . get_stylesheet_directory_uri() . '/events/community/' . $icon . '" alt="' . ucwords( $status ) . ' icon" title="' . ucwords( $status ) . '" class="icon">';
			}elseif ( file_exists( get_template_directory() . '/events/community/icons/' . $icon ) ) {
				return '<img width="16" height="16" src="' . get_template_directory_uri() . '/events/community/' . $icon . '" alt="' . ucwords( $status ) . ' icon" title="' . ucwords( $status ) . '" class="icon">';
			} else {
				return '<img width="16" height="16" src="' . $this->pluginUrl . '/resources/icons/' . $icon . '" alt="' . ucwords( $status ) . ' icon" title="' . ucwords( $status ) . '" class="icon">';
			}

		}

		/**
		 * filter pagination
		 *
		 * @since 1.0
		 * @author nciske
		 * @link http://www.kriesi.at/archives/how-to-build-a-wordpress-post-pagination-without-plugin
		 * @param object $query the query to paginate
		 * @param int $pages the pages
		 * @param int $range the range
		 * @return string $output the pagination links
		 */
		function pagination( $query, $pages = '', $range = 3 ) {
			$output    = '';
			$showitems = ( $range * 2 ) + 1;

			global $paged;
			if ( empty( $paged ) )
				$paged = 1;

			if ( $pages == '' ) {
				//global $wp_query;
				$pages = ceil( $query->found_posts / $this->eventsPerPage );

				//echo $pages;

				if ( !$pages ) {
					$pages = 1;
				}
			}
			
			if ( $paged > $pages ) {
				$this->message = "The requested page number was not found.";
			}		
			if ( 1 != $pages ) {
				add_filter( 'get_pagenum_link', array( $this, 'fix_pagenum_link' ) );

				$output .= "<div class='pagination'>";
				if ( $paged > 2 && $paged > $range + 1 && $showitems < $pages )
					$output .= "<a href='" . get_pagenum_link( 1 ) . "'>&laquo;</a>";
				if ( $paged > 1 && $showitems < $pages )
					$output .= "<a href='" . get_pagenum_link( $paged - 1 ) . "'>&lsaquo;</a>";

				for ( $i = 1; $i <= $pages; $i++ ) {
					if ( 1 != $pages && ( !( $i >= $paged + $range + 1 || $i <= $paged - $range - 1 ) || $pages <= $showitems ) ) {
						$output .= ( $paged == $i ) ? "<span class='current'>" . $i . "</span>" : "<a href='" . get_pagenum_link( $i ) . "' class='inactive' >" . $i . "</a>";
					}
				}

				if ( $paged < $pages && $showitems < $pages )
					$output .= "<a href='" . get_pagenum_link( $paged + 1 ) . "'>&rsaquo;</a>";
				if ( $paged < $pages - 1 && $paged + $range - 1 < $pages && $showitems < $pages )
					$output .= "<a href='" . get_pagenum_link( $pages ) . "'>&raquo;</a>";
				$output .= "</div>\n";
			}

			return $output;

		}

		/**
		 * get the template file with an output buffer
		 *
		 * @since 1.0
		 * @author nciske
		 * @param string $template_path the path
		 * @param string $template_file the file
		 * @return string the file's output
		 */
		public function get_template( $template_path, $template_file ) {
			ob_start();
			include $this->getTemplatePath( $template_path );
			return ob_get_clean();
		}

		/**
		 * get a file's path
		 *
		 * @since 1.0
		 * @author nciske
		 * @param string $template_path the path
		 * @param string $template_file the file
		 * @return string the file's path
		 */
		public static function getTemplatePath( $template_path, $template_file ) {
			if ( file_exists( get_stylesheet_directory() . '/events/community/' . $template_file ) ) {
				return get_stylesheet_directory() . '/events/community/' . $template_file;
			} elseif ( file_exists( get_template_directory() . '/events/community/' . $template_file ) ) {
				return get_template_directory() . '/events/community/' . $template_file;
			} else {
				return TribeCommunityEvents::getPluginPath() . $template_path . '/' . $template_file;
			}

		}

		/**
		 * filter the limit query
		 *
		 * @since 1.0
		 * @author nciske
		 * @return string $lq the modified query
		 */
		public function limitQuery() {
			global $paged;
			if ( $paged - 1 <= 0 ) {
				$page = 0;
			} else {
				$page = $paged - 1;
			}

			$lq = 'LIMIT ' . ( ( $this->eventsPerPage * $page ) ) . ',' . $this->eventsPerPage;
			return $lq;
		}

		/**
		 * output a message to the user
		 *
		 * @since 1.0
		 * @author nciske
		 * @param string $type the message typ
		 * @param bool $echo wheter to display or return the message
		 * @return string $output the message
		 */
		public function outputMessage( $type = null, $echo = true ) {
			if ( !$type && !$this->messageType ) {
				$type = 'updated';
			} elseif ( !$type && $this->messageType ) {
				$type = $this->messageType;
			}

			$errors = null;

			if ( isset( $this->message ) && $this->message )
				$errors = array(
					 array(
					 	'type' => $type,
						'message' => $this->message,
					)
				);

			$errors = apply_filters( 'tribe_community_events_form_errors', $errors );

			$output = '';

			if ( is_array( $errors ) ) {
				foreach ( $errors as $error ) {
					$output .= '<div id="message" class="' . $error[ 'type' ] . '"><p>' . $error[ 'message' ] . '</p></div>';
				}

				unset( $this->message );
			}

			if ( $echo ) {
				echo $output;
			} else {
				return $output;
			}

		}

		/**
		 * filter pagination links
		 *
		 * @since 1.0
		 * @author nciske
		 * @param string $result the link
		 * @return string $result the filtered link
		 */
		public function fix_pagenum_link( $result ) {

			// pretty permalinks - fix page one to have args so we don't redirect to todays's page
			if ( '' != get_option( 'permalink_structure' ) && !strpos( $result, '/page/' ) ) {
				$result = $this->getUrl( 'list', null, 1 );
			}

			// ugly links - fix page one to have args so we don't redirect to todays's page
			if ( '' == get_option( 'permalink_structure' ) && !strpos( $result, 'paged=' ) ) {
				$result = $this->getUrl( 'list', null, 1 );
			}

			return $result;

		}

		/**
		 * load recurrence data for ECP
		 *
		 * @since 1.0
		 * @author nciske
		 * @param string $postId the event
		 * @return void
		 */
		public function loadRecurrenceData( $postId ) {
			$output = '';

			$context = $this->getContext();

			$tribe_event_id = $context['id'];

			if ( $tribe_event_id ) {
				// convert array to variables that can be used in the view
				extract( TribeEventsRecurrenceMeta::getRecurrenceMeta( $tribe_event_id ) );
			} else {
				// create variables that can be used in the view
				$recType                  = 'None';
				$recEndType               = '';
				$recEnd                   = '';
				$recEndCount              = '';
				$recCustomType            = '';
				$recCustomInterval        = '';
				$recCustomTypeText        = '';
				$recOccurrenceCountText   = '';
				$recCustomWeekDay         = array(); //array
				$recCustomMonthNumber     = '';
				$recCustomMonthDay        = array();
				$recCustomYearFilter      = '';
				$recCustomYearMonthNumber = '';
				$recCustomYearMonthDay    = '';
				$recCustomYearMonth       = array(); //array

				if ( isset( $_POST[ 'recurrence' ][ 'type' ] ) )
					$recType = $_POST[ 'recurrence' ][ 'type' ];
				if ( isset( $_POST[ 'recurrence' ][ 'end-type' ] ) )
					$recEndType = $_POST[ 'recurrence' ][ 'end-type' ];
				if ( isset( $_POST[ 'recurrence' ][ 'end' ] ) )
					$recEnd = $_POST[ 'recurrence' ][ 'end' ];
				if ( isset( $_POST[ 'recurrence' ][ 'end-count' ] ) )
					$recEndCount = $_POST[ 'recurrence' ][ 'end-count' ];
				if ( isset( $_POST[ 'recurrence' ][ 'custom-type' ] ) )
					$recCustomType = $_POST[ 'recurrence' ][ 'custom-type' ];
				if ( isset( $_POST[ 'recurrence' ][ 'custom-interval' ] ) )
					$recCustomInterval = $_POST[ 'recurrence' ][ 'custom-interval' ];
				if ( isset( $_POST[ 'recurrence' ][ 'custom-type-text' ] ) )
					$recCustomTypeText = $_POST[ 'recurrence' ][ 'custom-type-text' ];
				if ( isset( $_POST[ 'recurrence' ][ 'occurrence-count-text' ] ) )
					$recOccurrenceCountText = $_POST[ 'recurrence' ][ 'occurrence-count-text' ];
				if ( isset( $_POST[ 'recurrence' ][ 'custom-week-day' ] ) )
					$recCustomWeekDay = $_POST[ 'recurrence' ][ 'custom-week-day' ]; //array
				if ( isset( $_POST[ 'recurrence' ][ 'custom-month-number' ] ) )
					$recCustomMonthNumber = $_POST[ 'recurrence' ][ 'custom-month-number' ];
				if ( isset( $_POST[ 'recurrence' ][ 'custom-month-day' ] ) )
					$recCustomMonthDay = $_POST[ 'recurrence' ][ 'custom-month-day' ];
				if ( isset( $_POST[ 'recurrence' ][ 'custom-year-filter' ] ) )
					$recCustomYearFilter = $_POST[ 'recurrence' ][ 'custom-year-filter' ];
				if ( isset( $_POST[ 'recurrence' ][ 'custom-year-month-number' ] ) )
					$recCustomYearMonthNumber = $_POST[ 'recurrence' ][ 'custom-year-month-number' ];
				if ( isset( $_POST[ 'recurrence' ][ 'custom-year-month-day' ] ) )
					$recCustomYearMonthDay = $_POST[ 'recurrence' ][ 'custom-year-month-day' ];
				if ( isset( $_POST[ 'recurrence' ][ 'custom-year-month' ] ) )
					$recCustomYearMonth = $_POST[ 'recurrence' ][ 'custom-year-month' ]; //array
			}

			$premium = TribeEventsPro::instance();

			$tce = self::instance();

			include $this->getTemplatePath( 'views', 'event-recurrence.php' );

		}

		/**
		 * determine if the specified user can edit the specified post type
		 *
		 * @since 1.0
		 * @author nciske
		 * @param string $id the user's ID
		 * @param string $post_type the post type
		 * @return bool
		 */
		public function userCanEdit( $id, $post_type ) {

			// only supports Tribe Post Types
			if ( $post_type != TribeEvents::POSTTYPE && $post_type != TribeEvents::VENUE_POST_TYPE && $post_type != TribeEvents::ORGANIZER_POST_TYPE ) {
				return false;
				}

			// admin override
			if ( current_user_can( 'administrator' ) ) {
				return true;
			}

			//short circuit if editing is off
			if ( !$this->allowUsersToEditSubmissions )
				return false;

			//pluralize post type if needed
			if ( substr( $post_type, -1 ) != 's' )
				 $post_type = $post_type . 's';
			//admin/high level user override
			if ( current_user_can( 'edit_published_' . $post_type ) && current_user_can( 'edit_others_' . $post_type ) )
				return true;

			$post = get_post( $id, 'OBJECT' );

			if ( is_object( $post ) && get_current_user_id() == $post->post_author ) {
				if ( ( $post->post_status == 'publish' && current_user_can( 'edit_published_' . $post_type ) ) || $post->post_status != 'publish' ) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}


		}

		/**
		 * add a setting tab
		 *
		 * @since 1.0
		 * @author nciske
		 * @return void
		 */
		public function doSettingsTab() {
			require_once $this->pluginPath . 'admin-views/community-options-template.php';
			new TribeSettingsTab( 'community', __( 'Community', 'tribe-events-community' ), $communityTab );
		}
		
		/**
		 * If the anonymous submit setting is changed, flush the rewrite rules.
		 *
		 * @since 1.0.1
		 * @author PaulHughes01
		 * @param string $field the name of the field being saved
		 * @param string $value the new value of the field
		 * @return void
		 */
		public function flushRewriteOnAnonymous( $field, $value ) {
			if ( $field == 'allowAnonymousSubmissions' && $value != $this->allowAnonymousSubmissions ) {
				TribeEvents::flushRewriteRules();
			}
		}

		/**
		 * filter the venue dropdown id
		 *
		 * @since 1.0
		 * @author nciske
		 * @param int $venue_id the venue id
		 * @return int $venue_id the filtered venue id
		 */
		public function tribe_display_event_venue_dropdown_id( $venue_id ) {
			$tce_venue = $this->getOption( 'defaultCommunityVenueID' );

			if ( !$venue_id )
			$venue_id = $tce_venue;

			return $venue_id;
		}

		/**
		 * filter the organizer dropdown id
		 *
		 * @since 1.0
		 * @author nciske
		 * @param int $organizer_id the organizer id
		 * @return int $organizer_id the filtered organizer id
		 */
		public function tribe_display_event_organizer_dropdown_id( $organizer_id ) {
			$tce_organizer = $this->getOption( 'defaultCommunityOrganizerID' );

			if ( !$organizer_id )
				$organizer_id = $tce_organizer;

			return $organizer_id;
		}

		/**
		 * filter the event meta template path
		 *
		 * @since 1.0
		 * @author nciske
		 * @param string $path the template path
		 * @return string the fitlered path
		 */
		public function tribe_community_events_event_meta_template( $path ) {
			return TribeCommunityEvents::getTemplatePath( 'views', 'event-meta.php' );
		}

		/**
		 * filter the event meta box template path
		 *
		 * @since 1.0
		 * @author nciske
		 * @param string $path the template path
		 * @return string the fitlered path
		 */
		public function tribe_community_events_meta_box_template( $path ) {
			return TribeCommunityEvents::getTemplatePath( 'views', 'events-meta-box.php' );
		}

		/**
		 * filter the venue meta box template path
		 *
		 * @since 1.0
		 * @author nciske
		 * @param string $path the template path
		 * @return string the fitlered path
		 */
		public function tribe_community_events_venue_meta_box_template( $path ) {
			return TribeCommunityEvents::getTemplatePath( 'views', 'venue-meta-box.php' );
		}

		/**
		 * filter the organizer meta box template path
		 *
		 * @since 1.0
		 * @author nciske
		 * @param string $path the template path
		 * @return string the fitlered path
		 */
		public function tribe_community_events_organizer_meta_box_template( $path ) {
			return TribeCommunityEvents::getTemplatePath( 'views', 'organizer-meta-box.php' );
		}


		/**
		 * add a community events origin to the audit system
		 *
		 * @since 1.0
		 * @author nciske
		 * @return string the community events slug
		 */
		public function filterPostOrigin() {
			return 'community-events';
		}

		/**
		 * display the tab index & increment it
		 *
		 * @since 1.0
		 * @author nciske
		 * @return void
		 */
		public function tabIndex() {
			echo $this->tabIndex;
			$this->tabIndex++;
		}

		/**
		 * return the tab index & increment it
		 *
		 * @since 1.0
		 * @author nciske
		 * @return int $tab_index the tab index
		 */
		public function getTabIndex() {
			$tab_index = $this->tabIndex;
			$this->tabIndex++;
			return $tab_index;
		}


		/**
		 * Get all options for the plugin
		 *
		 * @since 1.0
		 * @author nciske
		 * @return array of options
		 */
		public static function getOptions( $force = false ) {
			if ( !isset( self::$options ) || $force ) {
				$options       = get_option( TribeCommunityEvents::OPTIONNAME, array() );
				self::$options = apply_filters( 'tribe_community_events_get_options', $options );
			}
			return self::$options;
		}

		/**
		 * Get value for a specific option
		 *
		 * @since 1.0
		 * @author nciske
		 * @param string $optionName name of option
		 * @param string $default default value
		 * @return mixed results of option query
		 */
		public function getOption( $optionName, $default = '', $force = false ) {

			if ( !$optionName )
				return;

			if ( !isset( self::$options ) || $force ) {
				self::getOptions( $force );
			}

			if ( isset( self::$options[ $optionName ] ) ) {
				$option = self::$options[ $optionName ];
			} else {
				$option = $default;
			}

			return apply_filters( 'tribe_get_single_option', $option, $default );
		}


		/**
		 * Get the plugin's path
		 *
		 * @since 1.0
		 * @author nciske
		 * @return string the path
		 */
		public static function getPluginPath() {
			return self::instance()->pluginPath;
		}

		/**
		 * get the current user's role
		 *
		 * @since 1.0
		 * @author nciske
		 * @return string the role
		 */
		public function getCurrentUserRole() {

			global $current_user;

			if ( isset( $current_user->roles ) ) {
				$user_roles = $current_user->roles;
				$user_role = array_shift( $user_roles );
				return $user_role;
			} else {
				return false;
			}

		}

		/**
		 * block roles from the admin
		 *
		 * @since 1.0
		 * @author nciske
		 * @return string the path
		 */
		public function blockRolesFromAdmin() {

			if ( is_array( $this->blockRolesList ) ) {
				foreach ( $this->blockRolesList as $role ) {
					if ( $this->getCurrentUserRole() == $role ) {
						//turn off admin bar
						add_filter( 'show_admin_bar', '__return_false' );

						//redirect if trying to access admin
						if ( is_admin() ) {
							wp_redirect( trailingslashit( $this->blockRolesRedirect ) );
							exit;
						}
					}
				}
			}
		}
		
		/**
		 * Add the communiy events toolbar items.
		 *
		 * @since 1.0.1
		 * @author PaulHughes01
		 * @return void
		 */
		public function addCommunityToolbarItems() {
			global $wp_admin_bar;
			
			$wp_admin_bar->add_group( array(
				'id' => 'tribe-community-events-group',
				'parent' => 'tribe-events-add-ons-group'
			) );
			
			$wp_admin_bar->add_menu( array(
				'id' => 'tribe-community-events-submit',
				'title' => __( 'Community: Submit Event', 'tribe-events-calendar' ),
				'href' => $this->getUrl( 'add' ),
				'parent' => 'tribe-community-events-group'
			) );
			
			if ( $this->userCanEdit( null, TribeEvents::POSTTYPE ) ) {
				$wp_admin_bar->add_menu( array(
					'id' => 'tribe-community-events-my-events',
					'title' => __( 'Community: My Events', 'tribe-events-calendar' ),
					'href' => $this->getUrl( 'list' ),
					'parent' => 'tribe-community-events-group'
				) );
			}
			
			if ( current_user_can( 'manage_options' ) ) {			
				$wp_admin_bar->add_menu( array(
					'id' => 'tribe-community-events-settings-sub',
					'title' => __( 'Community Events', 'tribe-events-calendar' ),
					'href' => trailingslashit( get_admin_url() ) . 'edit.php?post_type=' . TribeEvents::POSTTYPE .'&page=tribe-events-calendar&tab=community',
					'parent' => 'tribe-events-settings'
				) );
			}
		}
		
		/**
		 * Return additional action for the plugin on the plugins page.
		 *
		 * @since 2.0.8
		 *
		 * @return array
		 */
		public function addLinksToPluginActions( $actions ) {
			if( class_exists( 'TribeEvents' ) ) {
				$actions['settings'] = '<a href="' . add_query_arg( array( 'post_type' => TribeEvents::POSTTYPE, 'page' => 'tribe-events-calendar', 'tab' => 'community' ), admin_url( 'edit.php' ) ) .'">' . __('Settings', 'tribe-events-community') . '</a>';
			}
			return $actions;
		}

		/**
		 * load the plugin's textdomain
		 *
		 * @since 1.0
		 * @author nciske
		 * @return void
		 */
		public function loadTextDomain() {
			load_plugin_textdomain( 'tribe-events-community', false, $this->pluginDir . 'lang/' );
		}


		/**
		 * init the plugin
		 *
		 * @since 1.0
		 * @author nciske
		 * @return void
		 */
		public function init() {
			self::$instance = self::instance();
			self::loadTextDomain();
			self::maybeFlushRewriteRules();
		}

		/**
		 * singleton instance method
		 *
		 * @since 1.0
		 * @author nciske
		 * @return object $instance the instance
		 */
		public static function instance() {
			if ( !isset( self::$instance ) ) {
				$className      = __CLASS__;
				self::$instance = new $className;
			}
			return self::$instance;
		}
		
		/**
		 * Sets the setting variable that says the rewrite rules should be flushed upon plugin load.
		 *
		 * @since 1.0.1
		 * @author PaulHughes01
		 * @return void
		 */
		public function activateFlushRewrite() {
			$options = self::getOptions();
			$options['maybeFlushRewrite'] = true;
			update_option( self::OPTIONNAME, $options );
		}
		
		/**
		 * Add Community Events to the list of add-ons to check required version.
		 *
		 * @author PaulHughes01
		 * @since 1.0.1
		 * @return array $plugins the existing plugins
		 * @return array the pluggins
		 */
		public function init_addon( $plugins ) {
			$plugins['TribeCE'] = array( 'plugin_name' => 'The Events Calendar: Community Events', 'required_version' => TribeCommunityEvents::REQUIRED_TEC_VERSION, 'current_version' => TribeCommunityEvents::VERSION, 'plugin_dir_file' => basename( dirname( dirname( __FILE__ ) ) ) . '/tribe-community-events.php' );
			return $plugins;
		}
		
		/**
		 * Checks if it should flush rewrite rules (after plugin is loaded).
		 *
		 * @since 1.0.1
		 * @author PaulHughes01
		 * @return void
		 */
		 public function maybeFlushRewriteRules() {
		 	if ( $this->maybeFlushRewrite == true ) {
		 		TribeEvents::flushRewriteRules();
		 		$options = self::getOptions();
				$options['maybeFlushRewrite'] = false;
				update_option( self::OPTIONNAME, $options );
			}
		 }
		
	}
}