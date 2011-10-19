<?php
/*
Plugin Name: WordPress File Monitor
Plugin URI: http://mattwalters.net/projects/wordpress-file-monitor/
Description: Monitor your website for added/changed/deleted files
Author: Matt Walters
Version: 2.3.3
Author URI: http://mattwalters.net/
*/ 

/*

To-Do
- Option to only scan active theme/plugins in admin area
- Option to specify file types to scan
- Clean up pushing options into place if they didn't already exist since it's duplicated by install()

- Fix Windows path problems reported by Ozh -- If anyone wants to contribute this, let me know - http://mattwalters.net/contact/

*/

if (!class_exists('msw_WordPressMonitor')) {
	class msw_WordPressMonitor {
		// Define possible options, PHP4 compatible
		var $options = array();
		var $debugMode = false; // Leave this set to false ... trust me.
		var $activeAlert = false;
		var $activeScan = false;

		function msw_WordPressMonitor() { $this->__construct(); } // PHP4 compatibility
		
		function __construct() {
			if (function_exists('add_action')) {
				add_action("admin_menu", array(&$this,"add_admin_pages")); // Call to add menu option in admin
			}

			// Assumes your language files will be in the format: wordpress_file_monitor-locationcode.mo
			$wordpress_file_monitor_locale = get_locale();
			$wordpress_file_monitor_mofile = dirname(MSW_WPFM_FILE) . "/languages/wordpress_file_monitor-".$wordpress_file_monitor_locale.".mo";
			load_textdomain("wordpress_file_monitor", $wordpress_file_monitor_mofile);

			$this->activeAlert = get_option('wpfm_alert'); // Get alert status
			$this->options = maybe_unserialize(get_option('wpfm_options')); // Set options to users preferences

			if (!is_array($this->options) || empty($this->options)) {
				$this->options = array(
					'scan_interval' => 30,
					'from_address' => get_option('admin_email'),
					'notify_address' => get_option('admin_email'),
					'site_root' => ABSPATH,
					'exclude_paths' => '',
					'modification_detection' => 'datetime',
					'notification_format' => 'detailed',
					'display_admin_alert' => 'yes'
				);
			}

			if (is_admin()) {
				wp_enqueue_script(array('thickbox'));
				wp_enqueue_style('thickbox');
			} else {
				if ($this->options['scan_interval'] != 0) { // Only put in scan check if scanning interval is set
					wp_enqueue_style('msw_wpfm_scan', WP_PLUGIN_URL.'/'.plugin_basename(__FILE__),null,'scan');
				}
			}

			if ($_SERVER['HTTP_HOST'] == 'wptest.local') {
				$this->debugMode = true; // This is for development purposes only.  True = scan is run on EVERY page load.  Do NOT use in production environment.
			}
		}

		function install() {
			// Default settings
			$options = array(
				'scan_interval' => 30,
				'from_address' => get_option('admin_email'),
				'notify_address' => get_option('admin_email'),
				'site_root' => ABSPATH,
				'exclude_paths' => '',
				'modification_detection' => 'datetime',
				'notification_format' => 'detailed',
				'display_admin_alert' => 'yes'
			);

			$optionsTest = maybe_unserialize(get_option('wpfm_options'));
			if (!$optionsTest) { // Add option if it doesn't exist
				add_option('wpfm_options', maybe_serialize($options), null, 'no'); // Set to default settings
				$this->options = $options;
			} else { // Make sure a setting is defined for each of the settings
				foreach ($options as $option=>$value) { // Loop through options
					if ($optionsTest[$option] == "") { $optionsTest[$option] = $options[$option]; } // If no setting is defined, define it
				}
				update_option('wpfm_options', maybe_serialize($optionsTest));
				$this->options = $optionsTest;
			}
		}

		function plugin_action_links($links, $file) { // Add 'Settings' link to plugin listing page in admin
			$plugin_file = 'wordpress-file-monitor/'.basename(__FILE__);
			if ($file == $plugin_file) {
				$settings_link = '<a href="'.get_bloginfo('wpurl').'/wp-admin/options-general.php?page=WordPressFileMonitor">'.__('Settings', 'wordpress_file_monitor').'</a>';
				array_unshift($links, $settings_link);
			}
			return $links;
		}

		function msw_wpfm_array_diff($needle, $haystack) {
			// Our very own array diff since PHP4 doesn't support array_diff_key() and some webhosts will run PHP4 :(
			$diff_array = array();
			foreach ($needle as $a=>$b) {
				$found = false;
				foreach ($haystack as $c=>$d) {
					if ($a == $c) { $found = true; }
				}
				if (!$found) { $diff_array[$a] = $b; }
			}
			return $diff_array;
		}

		function add_admin_pages() { // Add menu option in admin
//			add_options_page('WordPress File Monitor Options', 'WordPress File Monitor', 'manage_options', 'msw_wpfm', array(&$this,"output_sub_admin_page_0"));
			add_submenu_page('options-general.php', "WordPress File Monitor", "WordPress File Monitor", 10, "WordPressFileMonitor", array(&$this,"output_sub_admin_page_0"));
		}

		function admin_processing() {
			// Process forms in administration area if needed
			if (isset($_POST['msw_wpfm_action'])) {
				check_admin_referer('wpfm-update-options'); // Security check
				switch ($_POST['msw_wpfm_action']) {
					case 'update_options':
						$this->update_options($_POST); // Update options based on form submission
						break;
					case 'scan_site':
						$this->scan_site();
						break;
					case 'clear_alert':
						$this->activeAlert = false;
						delete_option('wpfm_alert');
						delete_option('wpfm_alertDesc');
						break;
					default:
						break;
				}
			}

			// Handle alert display request if needed
			if (isset($_GET['display']) && $_GET['display'] == 'alertDesc') {
				$alertDesc = get_option('wpfm_alertDesc');
				?>
				<form action="<?php echo get_bloginfo('wpurl'); ?>/wp-admin/options-general.php?page=WordPressFileMonitor" method="post" accept-charset="utf-8">
					<?php if (function_exists('wp_nonce_field')) { wp_nonce_field('wpfm-update-options'); } ?>
					<input type="hidden" name="msw_wpfm_action" value="clear_alert" id="msw_wpfm_action">
					<p class="submit"><input type="submit" value="<?php _e('Remove Alert', 'wordpress_file_monitor'); ?>"></p>
				</form>
				<?php
				if (!$alertDesc) {
					// Shouldn't land in here, but just in case ...
					_e('No alert(s) to display', 'wordpress_file_monitor');
				} else {
					echo str_replace("\n", "<br/>", $alertDesc);
				}
				exit;
			}
		}

		function update_options($newOptions) {
			foreach ($this->options as $option=>$value) { // Loop through post variables and get form fields corresponding to valid settings
				if ($option == 'exclude_paths' || $option == 'site_root') { $value = trim(stripslashes($value)); }
				$options[$option] = $newOptions[$option];
			}
			if (!get_option('wpfm_options')) { add_option('wpfm_options', '', null, 'no'); } // Add option if it does not exist
			update_option('wpfm_options', maybe_serialize($options)); // Set settings to new values
			$this->options = $options;
		}

		function output_sub_admin_page_0() { // Form to configure plugin
			?>
			<div class="wrap">
				<h2>WordPress File Monitor Options</h2>

				<?php if ($this->activeAlert && $this->options['display_admin_alert'] == 'yes') { ?>
					<div style="border: 1px solid #f00; margin: 0 0 10px; padding: 5px; background: #F88571; color: #000;">
						<b><?php _e('Warning!', 'wordpress_file_monitor'); ?></b> <?php _e('WordPress File Monitor has detected a change in the files on your site.', 'wordpress_file_monitor'); ?>
						<br/><br/>
						<a class="thickbox" href="<?php echo get_bloginfo('wpurl'); ?>/wp-admin/options-general.php?page=WordPressFileMonitor&amp;display=alertDesc" title="<?php _e('View changes and clear this alert', 'wordpress_file_monitor'); ?>" style="color:#ff0;font-weight:bold;"><?php _e('View changes and clear this alert', 'wordpress_file_monitor'); ?></a>
					</div>
				<?php } ?>

				<form name="msw_wpfm_manual_scan" action="<?php echo get_bloginfo('wpurl'); ?>/wp-admin/options-general.php?page=WordPressFileMonitor" method="post">
					<?php if (function_exists('wp_nonce_field')) { wp_nonce_field('wpfm-update-options'); } ?>
					<input type="hidden" name="msw_wpfm_action" value="scan_site" id="msw_wpfm_action">
					<table class="form-table">
						<tr>
							<td><p class="submit"><input type="submit" name="scan_now" value="<?php _e('Perform Scan Now', 'wordpress_file_monitor'); ?>" id="scan_now" /></p></td>
						</tr>
					</table>
				</form>

				<form style="float: left;" name="msw_wpfm_options" action="<?php echo get_bloginfo('wpurl'); ?>/wp-admin/options-general.php?page=WordPressFileMonitor" method="post">
					<?php if (function_exists('wp_nonce_field')) { wp_nonce_field('wpfm-update-options'); } ?>
					<input type="hidden" name="msw_wpfm_action" value="update_options" id="msw_wpfm_action">
					<table class="form-table">
						<tr>
							<td valign="middle"><label for="display_admin_alert"><?php _e('Dashboard Alert', 'wordpress_file_monitor'); ?>: </label></td>
							<td valign="middle">
								<select name="display_admin_alert" id="display_admin_alert">
									<option value="yes"<?php if (@$this->options['display_admin_alert'] == 'yes') { echo ' selected'; } ?>><?php _e('Yes', 'wordpress_file_monitor'); ?></option>
									<option value="no"<?php if (@$this->options['display_admin_alert'] == 'no') { echo ' selected'; } ?>><?php _e('No', 'wordpress_file_monitor'); ?></option>
								</select>
								<?php _e('(Notification on Dashboard when there is an active alert)','wordpress_file_monitor'); ?>
							</td>
						</tr>
						<tr>
							<td valign="middle"><label for="scan_interval"><?php _e('Scan Interval', 'wordpress_file_monitor'); ?>: </label></td>
							<td valign="middle"><input type="text" name="scan_interval" value="<?php echo @$this->options['scan_interval']; ?>" id="scan_interval"> (<?php _e('in minutes', 'wordpress_file_monitor'); ?>, <?php _e('0 for Manual Scan only', 'wordpress_file_monitor'); ?>)</td>
						</tr>
						<tr>
							<td valign="middle"><label for="modification_detection"><?php _e('Detection Method', 'wordpress_file_monitor'); ?>: </label></td>
							<td valign="middle">
								<select name="modification_detection" id="modification_detection">
									<option value="datetime"<?php if (@$this->options['modification_detection'] == 'datetime') { echo ' selected'; } ?>><?php _e('Modification Date (faster, but less secure)', 'wordpress_file_monitor'); ?></option>
									<option value="md5"<?php if (@$this->options['modification_detection'] == 'md5') { echo ' selected'; } ?>><?php _e('Hash (more secure, but takes longer)', 'wordpress_file_monitor'); ?></option>
								</select>
								<?php _e('Note: Hash method can cause performance issues on large sites.','wordpress_file_monitor'); ?>
							</td>
						</tr>
						<tr>
							<td valign="middle"><label for="from_address"><?php _e('From Address', 'wordpress_file_monitor'); ?>: </label></td>
							<td valign="middle"><input type="text" name="from_address" value="<?php echo @$this->options['from_address']; ?>" id="from_address"> (<?php _e('for alerts', 'wordpress_file_monitor'); ?>)</td>
						</tr>
						<tr>
							<td valign="middle"><label for="notify_address"><?php _e('Notify Address', 'wordpress_file_monitor'); ?>: </label></td>
							<td valign="middle"><input type="text" name="notify_address" value="<?php echo @$this->options['notify_address']; ?>" id="notify_address"> (<?php _e('for alerts', 'wordpress_file_monitor'); ?>)</td>
						</tr>
						<tr>
							<td valign="middle"><label for="notification_format"><?php _e('Notification Format', 'wordpress_file_monitor'); ?>: </label></td>
							<td valign="middle">
								<select name="notification_format" id="notification_format">
									<option value="detailed"<?php if (@$this->options['notification_format'] == 'detailed') { echo ' selected'; } ?>><?php _e('Detailed', 'wordpress_file_monitor'); ?></option>
									<option value="subversion"<?php if (@$this->options['notification_format'] == 'subversion') { echo ' selected'; } ?>><?php _e('Brief', 'wordpress_file_monitor'); ?></option>
									<option value="sms_pager"<?php if (@$this->options['notification_format'] == 'sms_pager') { echo ' selected'; } ?>><?php _e('SMS / Pager', 'wordpress_file_monitor'); ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<td valign="middle"><label for="site_root"><?php _e('Site Root', 'wordpress_file_monitor'); ?>: </label></td>
							<td valign="middle"><input type="text" name="site_root" value="<?php if ($this->options['site_root'] == '') { echo trim(stripslashes(ABSPATH)); } else { echo trim(stripslashes($this->options['site_root'])); } ?>" id="site_root"> (<?php _e('Default', 'wordpress_file_monitor'); ?>: <?php echo ABSPATH; ?>)</td>
						</tr>
						<tr>
							<td valign="top"><label for="exclude_paths"><?php _e('Exclude Paths', 'wordpress_file_monitor'); ?>: </label></td>
							<td valign="top"><textarea name="exclude_paths" rows="8" cols="40"><?php echo trim(stripslashes(@$this->options['exclude_paths'])); ?></textarea></td>
						</tr>
						<tr>
							<td valign="top">&nbsp;</td>
							<td valign="top">
								<?php _e('Exclude paths are relative to the site root above. One path per line.', 'wordpress_file_monitor'); ?><br/>
								<br/>
								<?php _e('Examples', 'wordpress_file_monitor'); ?>:<br/>
								wp-content/cache<br/>
								wp-content/uploads<br/>
								<br/>
								<?php _e('If you run any kind of cacheing plugins or scripts on your site and the cache files are stored in a folder under the ', 'wordpress_file_monitor'); ?><br/>
								<?php _e('Site Root specified above, it is HIGHLY recommended you exclude the paths to your cache directories.', 'wordpress_file_monitor'); ?>
							</td>
						</tr>
					</table>
					<p class="submit"><input type="submit" name="Submit" value="<?php _e('Submit', 'wordpress_file_monitor'); ?>" id="Submit"></p>
				</form>
				<script type="text/javascript">
				var WPHC_AFF_ID = '14317';
				var WPHC_WP_VERSION = '<?php global $wp_version; echo $wp_version; ?>';
				</script>
				<script type="text/javascript"
					src="http://cloud.wphelpcenter.com/wp-admin/0002/deliver.js">
				</script>
				<style type="text/css" media="screen">
					div.metabox-holder {
						float: right;
						width: 256px;
					}
				</style>
			</div>
			<?php
		}

		function list_directory($dir) {
			$dir = substr($dir, 0, (strlen($dir) - 1));
			$excludePaths = explode("\n", $this->options['exclude_paths']);
			$file_list = '';
			$stack[] = $dir;

			while ($stack) {
				$current_dir = array_pop($stack);
				$scanPath = true;
				if ($this->options['exclude_paths'] != '') { // If exclude paths are specified, check for them
					$i = 0;
					while ($scanPath == true && $i < count($excludePaths)) { // Break out of the loop as soon as we realize it can be excluded
						$temp = $this->options['site_root'] . trim($excludePaths[$i]);
						$i++;
						if (strpos($current_dir, $temp) !== false) { $scanPath = false; } // File/Directory is in exclude path, ignore it
					}
				}
				if (($dh = opendir($current_dir)) && $scanPath == true) {
					while (($file = readdir($dh)) !== false) {
						if ($file !== '.' AND $file !== '..') {
							$current_file = "{$current_dir}/{$file}";
							if (is_file($current_file)) {
								$file_list[] = "{$current_dir}/{$file}";
							} elseif (is_dir($current_file)) {
								$stack[] = $current_file;
							}
						}
					}
				}
			}
			return $file_list;
		}

		function cron() { // Test to see if a scan needs to be performed.
			$previousScan = get_option('wpfm_previousScan'); // Get previous scan timestamp
			$scanNeeded = false;
			$scanInterval = intval($this->options['scan_interval']); // Get setting for how often scan should be performed

			if ($previousScan) { // Determine if scan interval has been exceeded
				if (((time() - $previousScan) / 60) > $scanInterval) {
					$scanNeeded = true;
				}
			} else { // Scan has never been run so create option and perform initial scan
				$scanNeeded = true;
				add_option('wpfm_previousScan', '', null, 'no');
			}
			if ($scanNeeded || $this->debugMode) { // If scan is needed, perform scan and update last scan timestamp
				update_option('wpfm_previousScan', time());
				if (!$this->activeScan) {
					$this->scan_site();
				}
			}
		}

		function scan_site() { // Perform scan
			$dirListing = $this->list_directory($this->options['site_root']); // Get recursive file/directory listing
			$excludePaths = explode("\n", $this->options['exclude_paths']);
			foreach ($dirListing as $item) { // Loop through listing and remove files within exclude paths
				$scanPath = true;
				if ($this->options['exclude_paths'] != '') {
					$i = 0;
					while ($scanPath == true && $i < count($excludePaths)) {
						$temp = $this->options['site_root'] . trim($excludePaths[$i]);
						$i++;
						if (strpos($item, $temp) !== false) { $scanPath = false; } // File is in exclude path, ignore it
					}
				}
				if ($scanPath) {
					/*
						Set up an array of files.  The array is:
						FILENAME => HASH [OR] TIMESTAMP
						
						The user has the ability to configure which scanning method they would like to use.  Based on their choice
						either an md5 hash or the files timestamp will be set as the value to later be tested against.  If they change
						methods, the next time a scan is run, it will appear as though every file has been changed due to this setup.
						
						... that's life :)
					*/
					if ($this->options['modification_detection'] == 'md5') { // Test for changes to file via md5 hash
						$currentDirListing[$item] = md5_file($item);
					} else { // Test for changes to file via file timestamp
						$currentDirListing[$item] = filemtime($item);
					}
				}
			}
			$previousDirListing = get_option('wpfm_listing'); // Get serialized array of the previous scan if it exists
			if ($previousDirListing) { // If it did exist ... continue
				$previousDirListing = maybe_unserialize($previousDirListing);

				// Check for differences
				if (function_exists('array_diff_key')) {
					// Take advantage of PHP5
					$diff['addedFiles'] = array_diff_key($currentDirListing, $previousDirListing); // If files were added, create array of those files
					$diff['removedFiles'] = array_diff_key($previousDirListing, $currentDirListing); // If files were removed, create array of those files
				} else {
					// PHP4 Support
					$diff['addedFiles'] = $this->msw_wpfm_array_diff($currentDirListing, $previousDirListing); // If files were added, create array of those files
					$diff['removedFiles'] = $this->msw_wpfm_array_diff($previousDirListing, $currentDirListing); // If files were removed, create array of those files
				}
				$diff['changedFiles'] = array_diff($currentDirListing, $previousDirListing); // Compare previous scan to this scan, create array of files changed

				foreach ($diff['addedFiles'] as $file=>$v) { // Remove list of added files from changed files to prevent duplication in the email
					unset($diff['changedFiles'][$file]);
				}
				foreach ($diff['removedFiles'] as $file=>$v) { // Remove list of deleted files from changes files to prevent duplication in the email
					unset($diff['changedFiles'][$file]);
				}
				delete_option('wpfm_listing');
				add_option('wpfm_listing', maybe_serialize($currentDirListing), '', 'no');
				if (count($diff['addedFiles']) > 0 || count($diff['removedFiles']) > 0 || count($diff['changedFiles']) > 0) {
					$this->notify($diff); // Trigger notification email
				}
			} else {
				// This is the first scan, so add the option and set its value to be a serialized array of the recursive listing
				add_option('wpfm_listing', maybe_serialize($currentDirListing), '', 'no');
			}
		}

		function notify($diff=array()) {
			// Send notifaction email
			$toEmail = $this->options['notify_address'];
			$fromEmail = $this->options['from_address'];
			$fromName = __('WordPress File Monitor', 'wordpress_file_monitor');
			$headers = "From: " . $fromName . " <" . $fromEmail . ">\r\n";
			$subject = __('WordPress File Monitor: Alert (' . get_bloginfo('url') . ')', 'wordpress_file_monitor');
			$admin_AlertBody = ''; // Used if they're using sms_pager as an email format to display alert in admin
			if ($this->options['notification_format'] != 'sms_pager') {
				$body = __('This email is to alert you of the following changes to the file system of your website at ' . get_bloginfo('url'), 'wordpress_file_monitor');
				$body .= "\n";
				$body .= __('Timestamp', 'wordpress_file_monitor') . ': ' . date("r");
				$body .= "\n\n";
			} else {
				$body .= __('File changes detected for ' . get_bloginfo('url') . ' - ', 'wordpress_file_monitor') . ': ';
			}
			
			switch ($this->options['notification_format']) {
				// Format email according to users settings
				case 'detailed':
					if (count($diff['addedFiles']) > 0) {
						$body .= __('Added:', 'wordpress_file_monitor');
						$body .= "\n";
						foreach ($diff['addedFiles'] as $file=>$timeStamp) {
							$body .= str_replace($this->options['site_root'], '', $file) . "\n";
						}
						$body .= "\n\n";
					}
					if (count($diff['removedFiles']) > 0) {
						$body .= __('Removed:', 'wordpress_file_monitor');
						$body .= "\n";
						foreach ($diff['removedFiles'] as $file=>$timeStamp) {
							$body .= str_replace($this->options['site_root'], '', $file) . "\n";
						}
						$body .= "\n\n";
					}
					if (count($diff['changedFiles']) > 0) {
						$body .= __('Changed:', 'wordpress_file_monitor');
						$body .= "\n";
						foreach ($diff['changedFiles'] as $file=>$timeStamp) {
							$body .= str_replace($this->options['site_root'], '', $file) . "\n";
						}
					}
					break;
				case 'subversion': 
					if (count($diff['addedFiles']) > 0) {
						foreach ($diff['addedFiles'] as $file=>$timeStamp) {
							$body .= "[A] " . str_replace($this->options['site_root'], '', $file) . "\n";
						}
					}
					if (count($diff['removedFiles']) > 0) {
						foreach ($diff['removedFiles'] as $file=>$timeStamp) {
							$body .= "[D] " . str_replace($this->options['site_root'], '', $file) . "\n";
						}
					}
					if (count($diff['changedFiles']) > 0) {
						foreach ($diff['changedFiles'] as $file=>$timeStamp) {
							$body .= "[M] " . str_replace($this->options['site_root'], '', $file) . "\n";
						}
					}
					break;
				case 'sms_pager':
					$body .= __('Added', 'wordpress_file_monitor') . ': ' . count($diff['addedFiles']) . " / ";
					$body .= __('Removed', 'wordpress_file_monitor') . ': ' . count($diff['removedFiles']) . " / ";
					$body .= __('Changed', 'wordpress_file_monitor') . ': ' . count($diff['changedFiles']);
					$admin_AlertBody = __('Timestamp', 'wordpress_file_monitor') . ': ' . date("r") . "\n\n";
					// Since we're really just storing the email to be displayed in the admin
					// we have to compose an alternate body that will actually show them what
					// was changed when they log in.
					if (count($diff['addedFiles']) > 0) {
						$admin_AlertBody .= __('Added:', 'wordpress_file_monitor');
						$admin_AlertBody .= "\n";
						foreach ($diff['addedFiles'] as $file=>$timeStamp) {
							$admin_AlertBody .= str_replace($this->options['site_root'], '', $file) . "\n";
						}
						$admin_AlertBody .= "\n\n";
					}
					if (count($diff['removedFiles']) > 0) {
						$admin_AlertBody .= __('Removed:', 'wordpress_file_monitor');
						$admin_AlertBody .= "\n";
						foreach ($diff['removedFiles'] as $file=>$timeStamp) {
							$admin_AlertBody .= str_replace($this->options['site_root'], '', $file) . "\n";
						}
						$admin_AlertBody .= "\n\n";
					}
					if (count($diff['changedFiles']) > 0) {
						$admin_AlertBody .= __('Changed:', 'wordpress_file_monitor');
						$admin_AlertBody .= "\n";
						foreach ($diff['changedFiles'] as $file=>$timeStamp) {
							$admin_AlertBody .= str_replace($this->options['site_root'], '', $file) . "\n";
						}
					}
					break;
				default:
					// Really ... no way we should end up here, but just in case ...
					$body = __('There is an error with your configuration of WordPress File Monitor.  You need to specify a notification format.', 'wordpress_file_monitor');
					break;
			}

			$activeAlert = get_option('wpfm_alert'); // $activeAlert is boolean based on whether there is an uncleared alert
			if (!$activeAlert) { add_option('wpfm_alert', '', null, 'no'); }
			update_option('wpfm_alert', 'true');

			$activeAlertDesc = get_option('wpfm_alertDesc'); // $allertDesc contains the text of all uncleared alerts
			if (!$activeAlertDesc) { add_option('wpfm_alertDesc', '', null, 'no'); }
			if ($admin_AlertBody  == '') {
				update_option('wpfm_alertDesc', $activeAlertDesc . "<hr/>" . $body);
			} else {
				update_option('wpfm_alertDesc', $activeAlertDesc . "<hr/>" . $admin_AlertBody);
			}

			mail($toEmail, $subject, $body, $headers); // Send email

			$this->activeAlert = true;
		}

		function adminAlert() { // Check to see if there is an active alert and print something out if so.
			if ($this->activeAlert && $this->options['display_admin_alert'] == 'yes' && get_option('wpfm_alertDesc') != '') {
				$html = '<div style="border: 1px solid #f00; margin: 10px 0 0; padding: 5px; background: #F88571; color: #000;">';
				$html .= '<b>' . __('Warning!', 'wordpress_file_monitor') . '</b> - ' . __('WordPress File Monitor has detected a change in the files on your site.', 'wordpress_file_monitor');
				$html .= '<br/><br/>';
				$html .= '<a class="thickbox" href="' . get_bloginfo('wpurl') . '/wp-admin/options-general.php?page=WordPressFileMonitor&amp;display=alertDesc" title="' . __('View changes and clear this alert', 'wordpress_file_monitor') . '" style="color:#ff0;font-weight:bold;">' . __('View changes and clear this alert', 'wordpress_file_monitor') . '</a>';
				$html .= '</div>';
				echo $html;
			}
		}
	}
}

if (isset($_GET['ver']) && $_GET['ver'] == 'scan') {
	$root = dirname(dirname(dirname(dirname(__FILE__))));
	if (file_exists($root.'/wp-load.php')) { require_once($root.'/wp-load.php'); } else { require_once($root.'/wp-config.php'); }
}

if (!isset($msw_wpfm) && function_exists('add_action')) { $msw_wpfm = new msw_WordPressMonitor(); } // Create object if needed

if (isset($_GET['ver']) && $_GET['ver'] == 'scan' && function_exists('add_action')) {
	$msw_wpfm->cron();
	exit;
}

if (function_exists('add_action')) {
	if (is_file(trailingslashit(WP_PLUGIN_DIR).'wordpress-file-monitor.php')) {
		define('MSW_WPFM_FILE', trailingslashit(WP_PLUGIN_DIR).'wordpress-file-monitor.php');
	} else if (is_file(trailingslashit(WP_PLUGIN_DIR).'wordpress-file-monitor/wordpress-file-monitor.php')) {
		define('MSW_WPFM_FILE', trailingslashit(WP_PLUGIN_DIR).'wordpress-file-monitor/wordpress-file-monitor.php');
	}

	add_action('activity_box_end', array(&$msw_wpfm, 'adminAlert')); // Display alert in Dashboard if needed
	add_action('init', array(&$msw_wpfm, 'admin_processing')); // Process form submission if needed
	if ($_SERVER['HTTP_HOST'] == 'wptest.local') { add_action('init', array(&$msw_wpfm, 'cron')); } // Just for testing now

	add_filter('plugin_action_links', array(&$msw_wpfm, 'plugin_action_links'), 10, 2); // Add settings link to plugin listing
	register_activation_hook(MSW_WPFM_FILE, array(&$msw_wpfm, 'install')); // Run install routine if being activated
}

?>