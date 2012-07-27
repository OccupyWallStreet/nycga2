<?php
/**
 * Plugin Update Engine Class
 */

// Don't load directly
if ( !defined('ABSPATH') ) { die('-1'); }

if ( !class_exists('PluginUpdateEngineChecker') ) {
	/**
	 * A custom plugin update checker.
	 *
	 * @original author (c) Janis Elsts
	 * @heavily modified by Darren Ethier
	 * @slighty modified by Nick Ciske
	 * @slighty modified by Joachim Kudish
	 * @license GPL2 or greater.
	 * @version 1.2
	 * @access public
	 */
	class PluginUpdateEngineChecker {

		public $metadataUrl = ''; //The URL of the plugin's metadata file.
		public $pluginFile = '';  //Plugin filename relative to the plugins directory.
		public $pluginName = ''; //variable used to hold the pluginName as set by the constructor.
		public $slug = '';        //Plugin slug. (with .php extension)
		public $checkPeriod = 12; //How often to check for updates (in hours).
		public $optionName = '';  //Where to store the update info.
		public $json_error = ''; //for storing any json_error data that get's returned so we can display an admin notice.
		public $api_secret_key = ''; //used to hold the user API.  If not set then nothing will work!
		public $install_key = '';  //used to hold the install_key if set (included here for addons that will extend PUE to use install key checks)
		public $download_query = array(); //used to hold the query variables for download checks;
		public $lang_domain = ''; //used to hold the localization domain for translations .
		public $dismiss_upgrade; //for setting the dismiss upgrade option (per plugin).
		public $pue_install_key; //we'll customize this later so each plugin can have it's own install key!
		public $puePluginPath;

		/**
		 * Class constructor.
		 *
		 * @param string $metadataUrl The URL of the plugin's metadata file.
		 * @param string $pluginFile Fully qualified path to the main plugin file.
		 * @param string $slug The plugin's 'slug'.
		 * @param array $options:  Will contain any options that need to be set in the class initialization for construct.  These are the keys:
		 * 	@key integer $checkPeriod How often to check for updates (in hours). Defaults to checking every 12 hours. Set to 0 to disable automatic update checks.
		 * 	@key string $optionName Where to store book-keeping info about update checks. Defaults to 'external_updates-$slug'.
		 *  @key string $apikey used to authorize download updates from developer server
		 *	@key string $lang_domain If the plugin file pue-client.php is included with is localized you can put the domain reference string here so any strings in this file get included in the localization.
		 * @return void
		 */
		function __construct( $metadataUrl, $slug = '', $options = array(), $pluginFile = '' ){

			$this->puePluginPath = trailingslashit( dirname( dirname(__FILE__) ) );

			if (defined('PUE_UPDATE_URL')) {
				$this->metadataUrl = trailingslashit(PUE_UPDATE_URL);
			} else {
				$this->metadataUrl = trailingslashit($metadataUrl);
			}

			$this->slug = $slug;
			$tr_slug = str_replace('-','_',$this->slug);

			if( !empty($pluginFile) ){
				$this->pluginFile = $pluginFile;
			}else{
				$this->pluginFile = $slug.'/'.$slug.'.php';
			}

			$this->dismiss_upgrade = 'pu_dismissed_upgrade_'.$tr_slug;

			//get name from plugin file itself
			if ( ! function_exists( 'get_plugins' ) )
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			$plugin_details = explode('/',$pluginFile);
			$plugin_folder = get_plugins( '/'.$plugin_details[0] );
			$this->pluginName = $plugin_folder[ $plugin_details[1] ]['Name'];
			//get name from plugin file itself

			$this->pue_install_key = 'pue_install_key_'.$tr_slug;

			$defaults = array(
				'optionName' => 'external_updates-' . $this->slug,
				'apikey' => '',
				'installkey' => false,
				'lang_domain' => '',
				'checkPeriod' => 12
			);

			$options = wp_parse_args( $options, $defaults );
			extract( $options, EXTR_SKIP );

			$this->optionName = $optionName;
			$this->checkPeriod = (int) $checkPeriod;
			$this->api_secret_key = $apikey;
			if (isset($installkey) && $installkey) {
				$this->install_key = trim($installkey);
			} else {
				$this->install_key = trim(get_option( $this->pue_install_key ));
			}
			$this->lang_domain = $lang_domain;

			$this->set_api();
			$this->installHooks();
		}

		/**
		 * gets the api from the options table if present
		 **/
		function set_api($new_api = '') {

			//download query flag
			$this->download_query['pu_get_download'] = 1;

			//include current version
			if ($version = $this->getInstalledVersion()) {
				$this->download_query['pue_active_version'] = $version;
			}

			//the following is for install key inclusion (will apply later with PUE addons.)
			if ( isset( $this->install_key ) ) {
				$this->download_query['pu_install_key'] = $this->install_key;
			}

			// pass api key if appropriate
			if ( !empty($new_api) ) {
				$this->api_secret_key = $new_api;
			}
			if ( !empty($this->api_secret_key) ) {
				$this->download_query['pu_plugin_api'] = $this->api_secret_key;
			}
		}

		/**
		 * Install the hooks required to run periodic update checks and inject update info
		 * into WP data structures.
		 * Also other hooks related to the automatic updates (such as checking agains API and what not (@from Darren)
		 * @return void
		 */
		function installHooks(){
			//Override requests for plugin information
			add_filter('plugins_api', array(&$this, 'injectInfo'), 10, 3);

			// Check for updates when the WP updates are checked and inject our update if needed.
			add_filter('pre_set_site_transient_update_plugins', array(&$this, 'checkForUpdates'));

			add_action('tribe_license_fields', array(&$this, 'doLicenseKeyFields'));
			add_action('tribe_settings_after_content_tab_licenses', array(&$this, 'doLicenseKeyJavascript'));
			add_action('tribe_settings_success_message', array(&$this, 'doLicenseKeySuccessMessage'), 10, 2);

			// key validation
			add_action( 'wp_ajax_pue-validate-key_'.$this->slug, array($this, 'ajax_validate_key' ) );

			//dashboard message "dismiss upgrade" link
			add_action( "wp_ajax_".$this->dismiss_upgrade, array(&$this, 'dashboard_dismiss_upgrade'));
		}

		/**
		 * Inserts license key fields on license key page
		 *
		 * @author jkudish
		 * @since 2.0.5
		 * @return void
		 */
		public function doLicenseKeyFields($fields) {
			$fields[$this->pue_install_key.'-heading'] = array(
				'type' => 'heading',
				'label' => $this->pluginName,
			);
			$fields[$this->pue_install_key] = array(
				'type' => 'license_key',
				'size' => 'large',
				'validation_type' => 'license_key',
				'label' => sprintf( __('License Key for %s'), $this->pluginName),
				'tooltip' => __('A valid license key is required for support and updates', 'plugin-update-engine'),
				'parent_option' => false,
			);
			return $fields;
		}

		/**
		 * Inserts the javascript that makes the ajax checking
		 * work on the license key page
		 *
		 * @author jkudish
		 * @since 2.0.5
		 * @return void
		 */
		public function doLicenseKeyJavascript() {
			?>
		<script>
			jQuery(document).ready(function($) {
				$('#tribe-field-<?php echo $this->pue_install_key ?>').change(function() { <?php echo $this->pue_install_key ?>_validateKey(); });
					<?php echo $this->pue_install_key ?>_validateKey();
			});
			function <?php echo $this->pue_install_key ?>_validateKey() {
				var this_id = '#tribe-field-<?php echo $this->pue_install_key ?>';
				if ( jQuery(this_id+' input').val() != '') {
					jQuery(this_id+' .invalid-key').hide();
					jQuery(this_id+' .valid-key').hide();
					jQuery(this_id+' .ajax-loading-license').show();
					//strip whitespace from key
					var <?php echo $this->pue_install_key ?>_license_key = jQuery(this_id+' input').val().replace(/^\s+|\s+$/g, "");
					jQuery(this_id+' input').val( <?php echo $this->pue_install_key ?>_license_key );

					var data = { action: 'pue-validate-key_<?php echo $this->slug; ?>', key: <?php echo $this->pue_install_key ?>_license_key };
					jQuery.post(ajaxurl, data, function(response) {
						var data = jQuery.parseJSON(response);
						jQuery(this_id+' .ajax-loading-license').hide();
						if(data.status == '1') {
							jQuery(this_id+' .valid-key').show();
							jQuery(this_id+' .valid-key').text(data.message);
							jQuery(this_id+' .invalid-key').hide();
						} else {
							jQuery(this_id+' .invalid-key').show();
							jQuery(this_id+' .invalid-key').text(data.message);
							jQuery(this_id+' .valid-key').hide();
						}
					});
				}
			}
		</script>
		<?php
		}

		/**
		 * Filter the success message on license key page
		 *
		 * @author jkudish
		 * @since 2.0.5
		 * @return void
		 */
		public function doLicenseKeySuccessMessage($message, $tab) {

			if ($tab != 'licenses')
				return $message;

			return '<div id="message" class="updated"><p><strong>' . __('License key(s) updated.', 'plugin-update-engine') . '</strong></p></div>';

		}

		public function ajax_validate_key() {
			$response = array();
			$response['status'] = 0;
			if (isset($_POST['key'])) {

				$queryArgs = array(
					'pu_install_key' => trim($_POST['key']),
					'pu_checking_for_updates' => '1'
				);

				//include version info
				$queryArgs['pue_active_version'] = $this->getInstalledVersion();

				global $wp_version;
				$queryArgs['wp_version'] = $wp_version;

				//include domain and multisite stats
				$queryArgs['domain'] = $_SERVER['SERVER_NAME'];

				if( is_multisite() ){
					$queryArgs['multisite'] = 1;
					$queryArgs['network_activated'] = is_plugin_active_for_network( $this->pluginFile );
					global $wpdb;
					$queryArgs['active_sites'] = $wpdb->get_var( "SELECT count(blog_id) FROM $wpdb->blogs WHERE public = '1' AND archived = '0' AND spam = '0' AND deleted = '0'" );
				}else{
					$queryArgs['multisite'] = 0;
					$queryArgs['network_activated'] = 0;
					$queryArgs['active_sites'] = 1;
				}

				$pluginInfo = $this->requestInfo( $queryArgs );

				if (empty($pluginInfo)) {
					$response['message'] = __('Sorry, key validation server is not available.','plugin-update-engine');
				} elseif (isset($pluginInfo->api_expired) && $pluginInfo->api_expired == 1) {
					$response['message'] = __('Sorry, this key is expired.','plugin-update-engine');
				} elseif (isset($pluginInfo->api_upgrade) && $pluginInfo->api_upgrade == 1) {
					$response['message'] = __('Sorry, this key is out of installs.','plugin-update-engine');
				} elseif (isset($pluginInfo->api_invalid) && $pluginInfo->api_invalid == 1) {
					$response['message'] = __('Sorry, this key is not valid.','plugin-update-engine');
				} else {
					$response['status'] = 1;
					$response['message'] = sprintf(__('Valid Key! Expires on %s','plugin-update-engine'),$pluginInfo->expiration);
					$response['expiration'] = $pluginInfo->expiration;
				}
			} else {
				$response['message'] = __('Hmmm... something\'s wrong with this validator. Please contact <a href="http://tri.be/support">support.</a>','plugin-update-engine');
			}
			echo json_encode($response);
			exit;
		}

		function display_json_error() {
			$pluginInfo = $this->json_error;
			$update_dismissed = get_option($this->dismiss_upgrade);

			$is_dismissed = !empty($update_dismissed) && in_array($pluginInfo->version, $update_dismissed) ? true : false;

			if ($is_dismissed)
				return;

			//only display messages if there is a new version of the plugin.
			if ( version_compare($pluginInfo->version, $this->getInstalledVersion(), '>') ) {
				if ( $pluginInfo->api_invalid && current_user_can('administrator') ){
					$msg = str_replace('%plugin_name%', '<b>'.$this->pluginName.'</b>', $pluginInfo->api_invalid_message);
					$msg = str_replace('%plugin_slug%', $this->slug, $msg);
					$msg = str_replace('%update_url%', $this->metadataUrl, $msg);
					$msg = str_replace('%version%', $pluginInfo->version, $msg);
				}

				if( isset($msg) ){
					//Dismiss code idea below is obtained from the Gravity Forms Plugin by rocketgenius.com
					?>
				<div class="updated" style="padding:5px; position:relative;" id="pu_dashboard_message"><?php echo $msg ?>
					<a href="javascript:void(0);" onclick="PUDismissUpgrade();" style='float:right;'><?php _e("[X]") ?></a>
				</div>
				<script type="text/javascript">
					function PUDismissUpgrade(){
						jQuery("#pu_dashboard_message").slideUp();
						jQuery.post(ajaxurl, {action:"<?php echo $this->dismiss_upgrade; ?>", version:"<?php echo $pluginInfo->version; ?>", cookie: encodeURIComponent(document.cookie)});
					}
				</script>
				<?php
				}
			}
		}

		/**
		 * Retrieve plugin info from the configured API endpoint.
		 *
		 * @uses wp_remote_get()
		 *
		 * @param array $queryArgs Additional query arguments to append to the request. Optional.
		 * @return $pluginInfo
		 */
		function requestInfo($queryArgs = array()){

			//Query args to append to the URL. Plugins can add their own by using a filter callback (see addQueryArgFilter()).
			$queryArgs['installed_version'] = $this->getInstalledVersion();
			$queryArgs['pu_request_plugin'] = $this->slug;

			if ( empty($queryArgs['pu_plugin_api']) && !empty($this->api_secret_key) ) {
				$queryArgs['pu_plugin_api'] = $this->api_secret_key;
			}

			if ( empty($queryArgs['pu_install_key']) && !empty($this->install_key) ) {
				$queryArgs['pu_install_key'] = $this->install_key;
			}

			//include version info
			$queryArgs['pue_active_version'] = $this->getInstalledVersion();

			global $wp_version;
			$queryArgs['wp_version'] = $wp_version;

			//include domain and multisite stats
			$queryArgs['domain'] = $_SERVER['SERVER_NAME'];

			if( is_multisite() ){
				$queryArgs['multisite'] = 1;
				$queryArgs['network_activated'] = is_plugin_active_for_network( $this->pluginFile );
				global $wpdb;
				$queryArgs['active_sites'] = $wpdb->get_var( "SELECT count(blog_id) FROM $wpdb->blogs WHERE public = '1' AND archived = '0' AND spam = '0' AND deleted = '0'" );

			}else{
				$queryArgs['multisite'] = 0;
				$queryArgs['network_activated'] = 0;
				$queryArgs['active_sites'] = 1;
			}

			$queryArgs = apply_filters('puc_request_info_query_args-'.$this->slug, $queryArgs);

			//Various options for the wp_remote_get() call. Plugins can filter these, too.
			$options = array(
				'timeout' => 10, //seconds
				'headers' => array(
					'Accept' => 'application/json'
				),
			);
			$options = apply_filters('puc_request_info_options-'.$this->slug, array());

			$url = $this->metadataUrl;
			if ( !empty($queryArgs) ){
				$url = add_query_arg($queryArgs, $url);
			}

			//echo $url; //DEBUG
			
			// Cache the API call so it only needs to be made once per plugin per page load.
			static $plugin_info_cache;
			$key = crc32(implode('',$queryArgs));
			if (isset($plugin_info_cache[$key])) {
				return $plugin_info_cache[$key];
			}

			$result = wp_remote_get(
				$url,
				$options
			);

			//echo $result['body']; //DEBUG

			//Try to parse the response
			$pluginInfo = null;
			if ( !is_wp_error($result) && isset($result['response']['code']) && ($result['response']['code'] == 200) && !empty($result['body']) ){
				$pluginInfo = PU_PluginInfo::fromJson($result['body']);
			}
			$pluginInfo = apply_filters('puc_request_info_result-'.$this->slug, $pluginInfo, $result);

			$plugin_info_cache[$key] = $pluginInfo;

			return $pluginInfo;
		}

		/**
		 * Retrieve the latest update (if any) from the configured API endpoint.
		 *
		 * @uses PluginUpdateEngineChecker::requestInfo()
		 *
		 * @return PluginUpdateUtility An instance of PluginUpdateUtility, or NULL when no updates are available.
		 */
		function requestUpdate(){
			//For the sake of simplicity, this function just calls requestInfo()
			//and transforms the result accordingly.
			$pluginInfo = $this->requestInfo(array('pu_checking_for_updates' => '1'));
			if ( $pluginInfo == null ){
				return null;
			}
			//admin display for if the update check reveals that there is a new version but the API key isn't valid.
			if ( isset($pluginInfo->api_invalid) )  { //we have json_error returned let's display a message
				$this->json_error = $pluginInfo;
				add_action('admin_notices', array(&$this, 'display_json_error'));
				return null;
			}

			if ( isset($pluginInfo->new_install_key) ) {
				update_option($this->pue_install_key, $pluginInfo->new_install_key);
			}

			//need to correct the download url so it contains the custom user data (i.e. api and any other paramaters)

			if ( !empty($this->download_query) )  {
				$pluginInfo->download_url = add_query_arg($this->download_query, $pluginInfo->download_url);
			}

			return PluginUpdateUtility::fromPluginInfo($pluginInfo);
		}

		function in_plugin_update_message($plugin_data) {
			$plugininfo = $this->json_error;
			//only display messages if there is a new version of the plugin.
			if ( is_object($plugininfo) && version_compare($plugininfo->version, $this->getInstalledVersion(), '>') ) {
				if ( $plugininfo->api_invalid ) {
					$msg = str_replace('%plugin_name%', '<b>'.$this->pluginName.'</b>', $plugininfo->api_inline_invalid_message);
					$msg = str_replace('%plugin_slug%', $this->slug, $msg);
					$msg = str_replace('%update_url%', $this->metadataUrl, $msg);
					$msg = str_replace('%version%', $plugininfo->version, $msg);
					$msg = str_replace('%changelog%', '<a class="thickbox" title="'.$this->pluginName.'" href="plugin-install.php?tab=plugin-information&plugin='.$this->slug.'&TB_iframe=true&width=640&height=808">what\'s new</a>', $msg);
					echo '</tr><tr class="plugin-update-tr"><td colspan="3" class="plugin-update"><div class="update-message">' . $msg . '</div></td>';
				}
			}
		}

		function display_changelog() {
			//contents of changelog display page when api-key is invalid or missing.  It will ONLY show the changelog (hook into existing thickbox?)

		}

		function dashboard_dismiss_upgrade() {
			$os_ary = get_option($this->dismiss_upgrade);
			if (!is_array($os_ary))
				$os_ary = array();

			$os_ary[] = $_POST['version'];
			update_option($this->dismiss_upgrade, $os_ary);
		}

		/**
		 * Get the currently installed version of the plugin.
		 *
		 * @return string Version number.
		 */
		function getInstalledVersion(){
			if ( function_exists('get_plugins') ) {
				$allPlugins = get_plugins();
				if ( array_key_exists($this->pluginFile, $allPlugins) && array_key_exists('Version', $allPlugins[$this->pluginFile]) ){
					return $allPlugins[$this->pluginFile]['Version'];
				}
			}
		}

		/**
		 * Check for plugin updates.
		 * The results are stored in the DB option specified in $optionName.
		 *
		 * @return void
		 */
		function checkForUpdates( $updates = array() ){
			$state = get_option($this->optionName);
			if ( empty($state) ){
				$state = new StdClass;
				$state->lastCheck = 0;
				$state->checkedVersion = '';
				$state->update = null;
			}

			$state->lastCheck = time();
			$state->checkedVersion = $this->getInstalledVersion();
			update_option($this->optionName, $state); //Save before checking in case something goes wrong

			$state->update = $this->requestUpdate();
			
			// If a null update was returned, skip the end of the function.
			if ( $state->update == null ) {
				return $updates;
			}
			
			//Is there an update to insert?
			if ( version_compare($state->update->version, $this->getInstalledVersion(), '>') ){
				$updates->response[$this->pluginFile] = $state->update->toWpFormat();
			}

			update_option($this->optionName, $state);
			add_action('after_plugin_row_'.$this->pluginFile, array(&$this, 'in_plugin_update_message'));

			return $updates;
		}

		/**
		 * Intercept plugins_api() calls that request information about our plugin and
		 * use the configured API endpoint to satisfy them.
		 *
		 * @see plugins_api()
		 *
		 * @param mixed $result
		 * @param string $action
		 * @param array|object $args
		 * @return mixed
		 */
		function injectInfo($result, $action = null, $args = null){
			$relevant = ($action == 'plugin_information') && isset($args->slug) && ($args->slug == $this->slug);
			if ( !$relevant ){
				return $result;
			}

			$pluginInfo = $this->requestInfo(array('pu_checking_for_updates' => '1'));
			if ($pluginInfo){
				return $pluginInfo->toWpFormat();
			}

			return $result;
		}

		/**
		 * Register a callback for filtering query arguments.
		 *
		 * The callback function should take one argument - an associative array of query arguments.
		 * It should return a modified array of query arguments.
		 *
		 * @uses add_filter() This method is a convenience wrapper for add_filter().
		 *
		 * @param callback $callback
		 * @return void
		 */
		function addQueryArgFilter($callback){
			add_filter('puc_request_info_query_args-'.$this->slug, $callback);
		}

		/**
		 * Register a callback for filtering arguments passed to wp_remote_get().
		 *
		 * The callback function should take one argument - an associative array of arguments -
		 * and return a modified array or arguments. See the WP documentation on wp_remote_get()
		 * for details on what arguments are available and how they work.
		 *
		 * @uses add_filter() This method is a convenience wrapper for add_filter().
		 *
		 * @param callback $callback
		 * @return void
		 */
		function addHttpRequestArgFilter($callback){
			add_filter('puc_request_info_options-'.$this->slug, $callback);
		}

		/**
		 * Register a callback for filtering the plugin info retrieved from the external API.
		 *
		 * The callback function should take two arguments. If the plugin info was retrieved
		 * successfully, the first argument passed will be an instance of  PU_PluginInfo. Otherwise,
		 * it will be NULL. The second argument will be the corresponding return value of
		 * wp_remote_get (see WP docs for details).
		 *
		 * The callback function should return a new or modified instance of PU_PluginInfo or NULL.
		 *
		 * @uses add_filter() This method is a convenience wrapper for add_filter().
		 *
		 * @param callback $callback
		 * @return void
		 */
		function addResultFilter($callback){
			add_filter('puc_request_info_result-'.$this->slug, $callback, 10, 2);
		}
	}
}
?>