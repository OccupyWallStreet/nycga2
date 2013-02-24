<?php
/*
 Plugin Name: WPeMatico
 Plugin URI: http://www.wpematico.com
 Description: Enables administrators to create posts automatically from RSS/Atom feeds with multiples filters.  If you like it, please rate it 5 stars.
 Version: 1.1.1
 Author: etruel <esteban@netmdp.com>
 Author URI: http://www.netmdp.com
 */
# @charset utf-8
if ( ! function_exists( 'add_filter' ) )
	exit;

if ( !class_exists( 'WPeMatico' ) ) {
	add_action( 'init', array( 'WPeMatico', 'init' ) );

	if (is_admin()) {
	if(file_exists('app/nonstatic.php'))
		include_once('app/nonstatic.php');
		include_once('app/campaigns_list.php');
		include_once('app/campaigns_edit.php');
	}
	include_once('app/functions.php');


	register_activation_hook( plugin_basename( __FILE__ ), array( 'WPeMatico', 'activate' ) );
	register_deactivation_hook( plugin_basename( __FILE__ ), array( 'WPeMatico', 'deactivate' ) );
	register_uninstall_hook( plugin_basename( __FILE__ ), array( 'WPeMatico', 'uninstall' ) );

	class WPeMatico extends WPeMatico_functions {

		/**
		 * Textdomain
		 *
		 * @access public
		 * @const string
		 */
		const TEXTDOMAIN = 'WPeMatico';

		/**
		 * Version
		 *
		 * @access public
		 * @const string
		 */
		const VERSION = '1.1.1';
		const RELEASE = '1';

		/**
		 * Option Key
		 *
		 * @access public
		 * @const string
		 */
		const OPTION_KEY = 'WPeMatico_Options';

		/**
		 * $basen
		 *
		 * Plugin basename
		 *
		 * @access public
		 * @static
		 * @var string
		 */
		public static $basen;

		/**
		 * $uri
		 *
		 * absolute uri to the plugin with trailing slash
		 *
		 * @access public
		 * @static
		 * @var string
		 */
		public static $uri = '';

		/**
		 * $dir
		 *
		 * filesystem path to the plugin with trailing slash
		 *
		 * @access public
		 * @static
		 * @var string
		 */
		public static $dir = '';

		/**
		 * $default_options
		 *
		 * Some settings to use by default
		 *
		 * @access protected
		 * @static
		 * @var array
		 */
		protected static $default_options = array(
			'mailmethod' => 'mail',
			'mailsndemail' => '',
			'mailsndname' =>'',
			'mailsendmail' => '',
			'mailsecure' =>'',
			'mailhost' => '',
			'mailuser' => '',
			'mailpass' => '',
			'enableseelog' => false,
			'disabledashboard' =>false,
			'roles_widget' => array( "administrator" => "administrator" ),
			'enablerewrite' =>false,
			'disablewpcron' =>false,
			'imgattach' =>false,
			'imgcache' => false,
			'gralnolinkimg' => false,
			'featuredimg' => true,
			'nonstatic' => false,
			'disablecheckfeeds' => false,
			'enabledelhash' => false,
			'enableword2cats' => false,
			'disable_credits' => false,
			'force_mysimplepie' => false,
			'woutfilter' => false,
		);

		/**
		 *
		 * $options
		 *
		 * @access protected
		 * @var array
		 */
		public static $options = array();

		/**
		 * init
		 *
		 * @access public
		 * @static
		 * @return void
		 */
		public function init() {
			self :: $uri = plugin_dir_url( __FILE__ );
			self :: $dir = plugin_dir_path( __FILE__ );
			self :: $basen = plugin_basename(__FILE__);
			
			new self( TRUE );
		}
		
		/**
		 * constructor
		 *
		 * @access public
		 * @param bool $hook_in
		 * @return void
		 */
		public function __construct( $hook_in = FALSE ) {
			//Admin message
			//add_action('admin_notices', array( &$this, 'wpematico_admin_notice' ) ); 
			if ( ! $this->wpematico_env_checks() )
				return;
			$this->load_options();
			if($this->options['nonstatic'] && !class_exists( 'NoNStatic' )){ 
				$this->options['nonstatic'] = false; $this->update_options();
			}
			$this->load_textdomain();
			add_action( 'admin_notices', array( &$this, 'old_version_notice' ) );
			$this->Create_campaigns_page(); 
			if ( $hook_in ) {
				add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
				add_action( 'the_permalink', array( &$this, 'wpematico_permalink' ) );

				//Additional links on the plugin page
				add_filter(	'plugin_row_meta',	array(	__CLASS__, 'init_row_meta'),10,2);
				add_filter(	'plugin_action_links_' . self :: $basen, array( __CLASS__,'init_action_links'));
				
				//add Dashboard widget
				if (!$this->options['disabledashboard']){
					global $current_user;      
					get_currentuserinfo();	
					$user_object = new WP_User($current_user->ID);
					$roles = $user_object->roles;
					$display = false;
					if (!is_array($this->options['roles_widget'])) $this->options['roles_widget']= array( "administrator" => "administrator" );
					foreach( $roles as $cur_role ) {
						if ( array_search($cur_role, $this->options['roles_widget']) ) {
							$display = true;
						}
					}	
					if ( $current_user->ID && ( $display == true ) ) {	
						add_action('wp_dashboard_setup', array( &$this, 'wpematico_add_dashboard'));
					}
				}
			}
			//Disable WP_Cron
			if ($this->options['disablewpcron']) {
				define('DISABLE_WP_CRON',true);
			} //else{
			//add cron intervals
			add_filter('cron_schedules', 'wpematico_intervals');
			//Actions for Cron job
			add_action('wpematico_cron', 'wpematico_cron');
			//test if cron active
			if (!(wp_next_scheduled('wpematico_cron')))
				wp_schedule_event(0, 'wpematico_int', 'wpematico_cron');
			//}
		}

		//add dashboard widget
		function wpematico_add_dashboard() {
			wp_add_dashboard_widget( 'wpematico_widget', self :: TEXTDOMAIN , array( &$this, 'wpematico_dashboard_widget') );
		}

		 //Dashboard widget
		function wpematico_dashboard_widget() {
			$campaigns= $this->get_campaigns();
			echo '<div style="background: -moz-linear-gradient(center bottom , #FCF6BC 0px, #E1DC9C 98%, #FFFEA8 0px); border: 1px solid #DDDDDD; height: 20px; margin: -10px -10px 2px; padding: 5px 10px 0px;">';
			echo '<strong>'.__('Last Processed Campaigns:', self :: TEXTDOMAIN ).'</strong></div>';
			@$campaigns2 = $this->filter_by_value($campaigns, 'lastrun', '');  
			$this->array_sort($campaigns2,'lastrun');
	//				echo "<pre>".print_r($campaigns, true)."</pre>";
			if (is_array($campaigns2)) {
				$count=0;
				//http://localhost/wordpress/wp-admin/post.php?post=9&action=edit
				foreach ($campaigns2 as $_id => $campaign_data) {
					echo '<a href="'.wp_nonce_url('post.php?post='.$campaign_data['campaign_id'].'&action=edit', 'edit').'" title="'.__('Edit Campaign', self :: TEXTDOMAIN ).'">';
						if ($campaign_data['lastrun']) {
							echo " <i><strong>".$campaign_data['campaign_title']."</i></strong>, ";
							echo  date_i18n(get_option('date_format'),$campaign_data['lastrun']).'-'. date_i18n(get_option('time_format'),$campaign_data['lastrun']).', <i>'; 
							if ($campaign_data['lastpostscount']>0)
								echo ' <span style="color:green;">'. sprintf(__('Processed Posts: %1s', self :: TEXTDOMAIN ),$campaign_data['lastpostscount']).'</span>, ';
							else
								echo ' <span style="color:red;">'. sprintf(__('Processed Posts: %1s', self :: TEXTDOMAIN ), '0').'</span>, ';
								
							if ($campaign_data['lastruntime']<10)
								echo ' <span style="color:green;">'. sprintf(__('Fetch done in %1s sec.', self :: TEXTDOMAIN ),$campaign_data['lastruntime']) .'</span>';
							else
								echo ' <span style="color:red;">'. sprintf(__('Fetch done in %1s sec.', self :: TEXTDOMAIN ),$campaign_data['lastruntime']) .'</span>';
						} 
					echo '</i></a><br />';
					$count++;
					if ($count>=5)
						break;
				}		
			}
			unset($campaigns2);
			echo '<br />';
			echo '<div style="background: -moz-linear-gradient(center bottom , #FCF6BC 0px, #E1DC9C 98%, #FFFEA8 0px); border: 1px solid #DDDDDD; height: 20px; margin: -10px -10px 2px; padding:5px 10px 0px;">';
			echo '<strong>'.__('Next Scheduled Campaigns:', self :: TEXTDOMAIN ).'</strong>';
			echo '</div>';
			echo '<ul style="list-style: circle inside none; margin-top: 2px; margin-left: 9px;">';
			foreach ($campaigns as $campaign_id => $campaign_data) {
				if ($campaign_data['activated']) {
					echo '<li><a href="'.wp_nonce_url('post.php?post='.$campaign_data['campaign_id'].'&action=edit', 'edit').'" title="'.__('Edit Campaign', self :: TEXTDOMAIN ).'">';
					echo '<strong>'.$campaign_data['campaign_title'].'</strong>, ';
					if ($campaign_data['starttime']>0 and empty($campaign_data['stoptime'])) {
						$runtime=current_time('timestamp')-$campaign_data['starttime'];
						echo __('Running since:', self :: TEXTDOMAIN ).' '.$runtime.' '.__('sec.', self :: TEXTDOMAIN );
					} elseif ($campaign_data['activated']) {
						echo date(get_option('date_format'),$campaign_data['cronnextrun']).' '.date(get_option('time_format'),$campaign_data['cronnextrun']);
					}
					echo '</a></li>';
				}
			}
			$campaigns=$this->filter_by_value($campaigns, 'activated', '');
			if (empty($campaigns)) 
				echo '<i>'.__('None', self :: TEXTDOMAIN ).'</i><br />';
			echo '</ul>';

		}
		
		/**
		* Actions-Links del Plugin
		*
		* @param   array   $data  Original Links
		* @return  array   $data  modified Links
		*/
		public static function init_action_links($data)	{
			if ( !current_user_can('manage_options') ) {
				return $data;
			}
			return array_merge(
				$data,
				array(
					'<a href="edit.php?post_type=wpematico&page=settings" title="' . __('Load WPeMatico Settings Page', self :: TEXTDOMAIN ) . '">' . __('Settings', self :: TEXTDOMAIN ) . '</a>'
				)
			);
		}


		/**
		* Meta-Links del Plugin
		*
		* @param   array   $data  Original Links
		* @param   string  $page  plugin actual
		* @return  array   $data  modified Links
		*/

		public static function init_row_meta($data, $page)	{
			if ( $page != self::$basen ) {
				return $data;
			}
			return array_merge(
				$data,
				array(
				'<a href="http://wordpress.org/extend/plugins/wpematico/faq/" target="_blank">' . __('FAQ') . '</a>',
				'<a href="http://www.wpematico.com/" target="_blank">' . __('Support') . '</a>',
				'<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=B8V39NWK3NFQU" target="_blank">' . __('Donate', self :: TEXTDOMAIN ) . '</a>'
				)
			);
		}		
		
		/**
		 * admin menu custom post type
		 *
		 * @access public
		 * @return void
		 */
		 public function Create_campaigns_page() {
		  $labels = array(
			'name' => __('Campaigns', 'WpeMatico'),
			'singular_name' => __('Campaign', 'WpeMatico'),
			'add_new' => __('Add New', 'WpeMatico'),
			'add_new_item' => __('Add New Campaign'),
			'edit_item' => __('Edit Campaign'),
			'new_item' => __('New Campaign'),
			'all_items' => __('All Campaigns'),
			'view_item' => __('View Campaign'),
			'search_items' => __('Search Campaign'),
			'not_found' =>  __('No campaign found'),
			'not_found_in_trash' => __('No Campaign found in Trash'), 
			'parent_item_colon' => '',
			'menu_name' => 'WpeMatico');
		  $args = array(
			'labels' => $labels,
			//'public' => true,
			'public' => false,
			'exclude_from_search' => true,
			'publicly_queryable' => false,
			'show_ui' => true, 
			'show_in_menu' => true, 
			'query_var' => true,
			'rewrite' => true,
			'capability_type' => 'post',
			'has_archive' => true, 
			'hierarchical' => false,
			'menu_position' => 7,
			'menu_icon' => self :: $uri.'/images/wpe_ico.png',
			'register_meta_box_cb' => array( 'WPeMatico_Campaign_edit', 'create_meta_boxes'),
			'map_meta_cap' => true,
			'supports' => array( 'title', 'excerpt' ) ); 
		  register_post_type('wpematico',$args);
		}  //
		
		/**
		 * admin menu
		 *
		 * @access public
		 * @return void
		 */
		public function admin_menu() {
			$jobs=get_option('wpematico_jobs');
			if ( is_array($jobs) ) :
				add_submenu_page(
					'edit.php?post_type=wpematico',
					__( 'Import old version', self :: TEXTDOMAIN ),
					__( 'Import old version', self :: TEXTDOMAIN ),
					'manage_options',
					'tools',
					array( &$this, 'add_admin_tools_page' )
				);
			endif;
			add_submenu_page(
				'edit.php?post_type=wpematico',
				__( 'Settings', self :: TEXTDOMAIN ),
				__( 'Settings', self :: TEXTDOMAIN ),
				'manage_options',
				'settings',
				array( &$this, 'add_admin_submenu_page' )
			);
		}

		/**	 * Show a warning if any of the requirements is not met.	 */
		function old_version_notice() {
			$jobs=get_option('wpematico_jobs');
			if ( is_array($jobs) ) :
				?><div id="message" class="error fade">
					<p> <?php _e( 'WPeMatico: Found campaigns from previous versions.', self :: TEXTDOMAIN );?>
					<?php _e( "If you want import or delete them go to:", self :: TEXTDOMAIN ); ?> <a href="<?php echo admin_url( 'edit.php?post_type=wpematico&page=tools'); ?>">Import old campaigns</a>.</p>
				</div>
				<?php 
			endif;
		}
		
		/**
		 * an admin submenu page
		 *
		 * @access public
		 * @return void
		 */
		public function add_admin_tools_page () {
			include_once( self :: $dir . "app/tools_page.php");
		}
		
		/**
		 * an admin submenu page
		 *
		 * @access public
		 * @return void
		 */
		public function add_admin_submenu_page () {
			if ( 'POST' === $_SERVER[ 'REQUEST_METHOD' ] ) {
				if ( get_magic_quotes_gpc() ) {
					$_POST = array_map( 'stripslashes_deep', $_POST );
				}
				# evaluation goes here
				check_admin_referer('wpematico-settings');
				$cfg = $this->options;
				$cfg['mailsndemail']	= sanitize_email($_POST['mailsndemail']);
				$cfg['mailsndname']		= $_POST['mailsndname'];
				$cfg['mailmethod']		= $_POST['mailmethod'];
				$cfg['mailsendmail']	= untrailingslashit(str_replace('//','/',str_replace('\\','/',stripslashes($_POST['mailsendmail']))));
				$cfg['mailsecure']		= $_POST['mailsecure'];
				$cfg['mailhost']		= $_POST['mailhost'];
				$cfg['mailuser']		= $_POST['mailuser'];
				$cfg['mailpass']		= base64_encode($_POST['mailpass']);
				@$cfg['disable_credits']= $_POST['disable_credits']==1 ? true : false;
				@$cfg['disablecheckfeeds']= $_POST['disablecheckfeeds']==1 ? true : false;
				@$cfg['enabledelhash']	= $_POST['enabledelhash']==1 ? true : false;
				@$cfg['enableseelog']	= $_POST['enableseelog']==1 ? true : false;
				@$cfg['disabledashboard']= $_POST['disabledashboard']==1 ? true : false;
				@$cfg['enablerewrite']	= $_POST['enablerewrite']==1 ? true : false;
				@$cfg['enableword2cats']= $_POST['enableword2cats']==1 ? true : false;
				@$cfg['disablewpcron']	= $_POST['disablewpcron']==1 ? true : false;
				@$cfg['imgcache']		= $_POST['imgcache']==1 ? true : false;
				@$cfg['gralnolinkimg']	= $_POST['gralnolinkimg']==1 ? true : false;
				@$cfg['featuredimg']	= $_POST['featuredimg']==1 ? true : false;
				@$cfg['imgattach']		= $_POST['imgattach']==1 ? true : false;
				@$cfg['force_mysimplepie']	= $_POST['force_mysimplepie']==1 ? true : false;
				@$cfg['woutfilter']		= $_POST['woutfilter']==1 ? true : false;
				// Roles 
				global $wp_roles, $current_user;    
				get_currentuserinfo();
				$role_conf = array();
				foreach ( $_POST['role_name'] as $role_id => $role_val ) {
					$role_conf["$role_val"]= $role_val;
				}
				$cfg['roles_widget'] = $role_conf; 
				
				$this->options = $cfg;
				# saving
				if ( $this->update_options() ) {
					?><div class="updated"><p> <?php _e( 'Settings saved.', self :: TEXTDOMAIN );?></p></div><?php
				}else{
				/*	?><div class="error"><p> <?php _e( 'Settings NOT saved.', self :: TEXTDOMAIN );?></p></div><?php  */
				}
			}
//			add_action('admin_head', array( &$this,'wpesettings_admin_head'));
			include_once( self :: $dir . "app/settings_page.php");
		}
		
		function wpesettings_admin_head() {
			?>
			<?php
		}
		
		
		/**
		 * load_textdomain
		 *
		 * @access protected
		 * @return void
		 */
		protected function load_textdomain() {
			# load plugin textdomain
			load_plugin_textdomain( self :: TEXTDOMAIN, FALSE, basename( plugin_basename( __FILE__ ) ) . '/lang' );
			# load tinyMCE localisation file
			#add_filter( 'mce_external_languages', array( &$this, 'mce_localisation' ) );
		}

		/**
		 * mce_localisation
		 *
		 * @access public
		 * @param array $mce_external_languages
		 * @return array
		 */
		public function mce_localisation( $mce_external_languages ) {

			if ( file_exists( self :: $dir . 'lang/mce_langs.php' ) )
				$mce_external_languages[ 'inpsydeOembedVideoShortcode' ] = self :: $dir . 'lang/mce-langs.php';
			return $mce_external_languages;
		}
		
		/**
		 * load_options
		 *
		 * @access protected
		 * @return void
		 */
		public function load_options() {
			if ( ! get_option( self :: OPTION_KEY ) ) {
				if ( empty( self :: $default_options ) )
					return;
				$this->options = self :: $default_options;	
				add_option( self :: OPTION_KEY, $this->options , '', 'yes' );
			}
			else {
				$this->options = get_option( self :: OPTION_KEY );
			}
			//$this->options = $this->check_options($this->options);
		}

		public function check_options($cfg) {
			if(!isset($cfg['mailmethod'])) $cfg['mailmethod'] = 'mail';
			if(!isset($cfg['mailsndemail'])) $cfg['mailsndemail'] = '';
			if(!isset($cfg['mailsndname'])) $cfg['mailsndname'] ='';
			if(!isset($cfg['mailsendmail'])) $cfg['mailsendmail'] = '';
			if(!isset($cfg['mailsecure'])) $cfg['mailsecure'] ='';
			if(!isset($cfg['mailhost'])) $cfg['mailhost'] = '';
			if(!isset($cfg['mailuser'])) $cfg['mailuser'] = '';
			if(!isset($cfg['mailpass'])) $cfg['mailpass'] = '';
			if(!isset($cfg['enableseelog']) || !is_bool($cfg['enableseelog'])) $cfg['enableseelog'] = false;
			if(!isset($cfg['disabledashboard']) || !is_bool($cfg['disabledashboard'])) $cfg['disabledashboard'] =false;
			if(!isset($cfg['roles_widget']) || !is_array($cfg['roles_widget'])) $cfg['roles_widget'] = array( "administrator" => "administrator" );
			if(!isset($cfg['enablerewrite']) || !is_bool($cfg['enablerewrite'])) $cfg['enablerewrite'] =false;
			if(!isset($cfg['disablewpcron']) || !is_bool($cfg['disablewpcron'])) $cfg['disablewpcron'] =false;
			if(!isset($cfg['imgattach']) || !is_bool($cfg['imgattach'])) $cfg['imgattach'] =false;
			if(!isset($cfg['imgcache']) || !is_bool($cfg['imgcache'])) $cfg['imgcache'] = false;
			if(!isset($cfg['gralnolinkimg']) || !is_bool($cfg['gralnolinkimg'])) $cfg['gralnolinkimg'] = false;
			if(!isset($cfg['featuredimg']) || !is_bool($cfg['featuredimg'])) $cfg['featuredimg'] = true;
			if(!isset($cfg['nonstatic']) || !is_bool($cfg['nonstatic'])) $cfg['nonstatic'] = false;
			if(!isset($cfg['disablecheckfeeds']) || !is_bool($cfg['disablecheckfeeds'])) $cfg['disablecheckfeeds'] = false;
			if(!isset($cfg['enabledelhash']) || !is_bool($cfg['enabledelhash'])) $cfg['enabledelhash'] = false;
			if(!isset($cfg['enableword2cats']) || !is_bool($cfg['enableword2cats'])) $cfg['enableword2cats'] = false;
			if(!isset($cfg['disable_credits']) || !is_bool($cfg['disable_credits'])) $cfg['disable_credits'] = false;
			if(!isset($cfg['force_mysimplepie']) || !is_bool($cfg['force_mysimplepie'])) $cfg['force_mysimplepie'] = false;
			if(!isset($cfg['woutfilter']) || !is_bool($cfg['woutfilter'])) $cfg['woutfilter'] = false;
			return $cfg;
		}
		
		/**
		 * update_options
		 *
		 * @access protected
		 * @return bool True, if option was changed
		 */
		public function update_options() {
			return update_option( self :: OPTION_KEY, $this->options );
		}

		/**
		 * activation
		 *
		 * @access public
		 * @static
		 * @return void
		 */
		public static function activate() {
		    self :: Create_campaigns_page(); 
			// ATTENTION: This is *only* done during plugin activation hook // You should *NEVER EVER* do this on every page load!!
			flush_rewrite_rules();			
	
			//remove old cron jobs
			$args = array( 'post_type' => 'wpematico', 'orderby' => 'ID', 'order' => 'ASC' );
			$campaigns = get_posts( $args );
			foreach( $campaigns as $post ) {
				$campaign = self :: get_campaign( $post->ID );	
				$activated = $campaign['activated'];
				if ($time=wp_next_scheduled('wpematico_cron',array('campaign_id'=>$post->ID )))
					wp_unschedule_event($time,'wpematico_cron',array('campaign_id'=>$post->ID ));
				
			}

			wp_clear_scheduled_hook('wpematico_cron');
			//make schedule
			wp_schedule_event(0, 'wpematico_int', 'wpematico_cron'); 
		}

		/**
		 * deactivation
		 *
		 * @access public
		 * @static
		 * @return void
		 */
		public static function deactivate() {
			//remove old cron jobs
			$args = array( 'post_type' => 'wpematico', 'orderby' => 'ID', 'order' => 'ASC' );
			$campaigns = get_posts( $args );
			foreach( $campaigns as $post ) {
				$campaign = self :: get_campaign( $post->ID );	
				$activated = $campaign['activated'];
				if ($time=wp_next_scheduled('wpematico_cron',array('campaign_id'=>$post->ID)))
					wp_unschedule_event($time,'wpematico_cron',array('campaign_id'=>$post->ID));
				
			}

			wp_clear_scheduled_hook('wpematico_cron');
			// NO borro opciones ni campañas
		}

		/**
		 * uninstallation
		 *
		 * @access public
		 * @static
		 * @global $wpdb, $blog_id
		 * @return void
		 */
		public static function uninstall() {
			global $wpdb, $blog_id;
			if ( is_network_admin() ) {
				if ( isset ( $wpdb->blogs ) ) {
					$blogs = $wpdb->get_results(
						$wpdb->prepare(
							'SELECT blog_id ' .
							'FROM ' . $wpdb->blogs . ' ' .
							"WHERE blog_id <> '%s'",
							$blog_id
						)
					);
					foreach ( $blogs as $blog ) {
						delete_blog_option( $blog->blog_id, self :: OPTION_KEY );
					}
				}
			}
			delete_option( self :: OPTION_KEY );
			// Tambien borrar campañas ?
			//self :: delete_campaigns();
		}
	}
}

