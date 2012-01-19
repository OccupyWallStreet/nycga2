<?php
/*
	MORE_PLUGINS_ADMIN_OBJECT
	SPUTNIK Release #7
	
	This plugin compontent is common for all More Plugins (see more-plugins.se for 
	more information about these plugins) and does most of all the heavy lifting 
	that is required when creating, editing and deleting data. This object does not 
	care what that data is, as long as the data adheres to some rules.
	
	SETTINGS
	========
	name - Name of the plugin
	option_key - The key that holds the plugin settings in the _options database table.
	defaults - contains the default values when creating a new data item
	fields - defines the keys that are extracted from $_POST when saving
	file - should always be __FILE__ (so that we can get the plugin directory)
	
	DATA LAYOUT
	===========
	1. 	There is only one data array contains all the data (per plugin). Within this 
		array, there are 4 types of data, each with their own key.
		- ${data}['_plugin']
			This is the data actually stored in the options table in the WP data-base
			for this plugin. New data is created here, and must exist here to be edited. 
		- ${data}['_plugin_saved']
			A feature of more plugins is to save individual features to a file, which can
			be exported to other installations. Objects read in from file are stored here.
		- ${data}['_other']
			This is data created in function.php or by other plugins. 
		- ${data}['_default']
			This is data that is native to WordPress, e.g. Post and Types for the case of
			post types.
	
	2. $_GET['navigation'] deterimines the view.
	2. $this->keys is the keys of the data in 1) that is viewed, e.g. array('_plugin', 'news') or
		array('_other', 'box', 'fields', 'title'). As a $_GET['keys'] variable it is imploded with a 
		comma as the separator, e.g. '&keys=_other,box,fields,title'
	3. $this->action determins what is about to happen
	4. $this->action_keys is used when deleting stuff, as $this->key sets the navigation.

	SAVING:
	=======
	- The fields that are to be saved are defined in the $this->fields, which contains 'var' for
		single value variables and 'array' for variabels that can contain array values,
		associative or just normal.
	- Default values for creating new stuff is defined in $this->default. 
	- In x-settings.php	keys that are deeper than 1, e.g. $data['one']['two'] are retrieved
		as 'one,two'.
	- $_POST['index'] sets the last key to save to. This needs to be set in 
		validate_submission() in each plugins x-settings-object.php.
	- $_POST['originating_keys'] defines where the request came from - used to
		see of the name was changed. 
	- For a key that contains deeper levels of data, the entire data contained within
		that key must be defined in $_POST. E.g. for More Fields, the entire 'fields' key
		must be defined in $_POST when saving the box. This is done with $this->settings_hidden(),
		which will seralize any array data.
	

	Copyright (C) 2010  Henrik Melin, Kal Strm
	
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

*/

$more_common = 'MORE_PLUGINS_ADMIN_SPUTNIK_8';
if (!defined($more_common)) {

 	class more_plugins_admin_object_sputnik_8 {
		var $name, $slug, $settings_file, $dir, $options_url, $option_key, $data, $url, $keys;
	
		var $action, $navigation, $message, $error, $headed, $footed;
		/*
		**
		**
		*/
		function more_plugins_admin_object_sputnik_8 ($settings) {

			$this->name = $settings['name'];
			$this->slug = sanitize_title($settings['name']);
			$this->fields = $settings['fields'];
			if (isset($settings['settings_file'])) 
				$this->settings_file = $settings['settings_file'];
			else $this->settings_file = $this->slug . '-settings.php';
			$this->dir = plugin_dir_path($settings['file']); //WP_PLUGIN_DIR . '/' . $this->slug . '/';
			$this->url = plugin_dir_url($settings['file']); //get_option('siteurl') . '/wp-content/plugins/' . $this->slug . '/';
			$this->options_url = 'options-general.php?page=' . $this->slug;
			$this->settings_url = $this->options_url;
			$this->option_key = $settings['option_key'];
			$this->default = $settings['default'];
			$this->default_keys = ($a = $settings['default']) ? $a : array();

			// Create Settins Menu
			add_action('admin_menu', array(&$this, 'admin_menu'));
			add_action('admin_head', array(&$this, 'admin_head'));

			// Handle requests			
			add_action('settings_page_' . $this->slug, array(&$this, 'request_handler'));
			
			// Add JS & css on settings page
			add_action('admin_head-settings_page_' . $this->slug, array(&$this, 'settings_head'));
			// add_action('admin_print_scripts-settings_page_' . $this->slug, array(&$this, 'settings_init'));
			
			add_action('load-settings_page_' . $this->slug, array(&$this, 'settings_init'));
			// add_action('admin_init', array(&$this, 'settings_init'), 50);
			
			add_filter('plugin_row_meta', array(&$this, 'plugin_row_meta'), 10, 2);

			add_action('init', array(&$this, 'admin_init'), 11);
			
//			add_action('admin_print_scripts-' . $page, 'my_plugin_admin_styles');

			
			$this->add_actions();

			$this->add_key = '57UPhPh';

			

			// $this->data = $this->read_data();
		}
		function admin_init() {
/*
			// Read in saved files	
			$dir = WP_PLUGIN_DIR . '/' . $this->slug . '/saved/';
			if (is_dir($dir)) {
			
				// Pre PHP 5 compatablity
				if (is_callable('scandir'))
					$ls = scandir($dir);
				else {
					$ls = array();
					$dh  = opendir($dir);
					while (false !== ($filename = readdir($dh))) {
						if ($filename[0] != '.') $ls[] = $filename;
					}
				}
				
				$pts = array();
				foreach ($ls as $l) if (strpos($l, '.php')) $pts[] = $l;
				foreach ($pts as $file) require($dir . $file);
				$this->data = $this->load_objects();
			}
			*/	
		}
		function add_actions() {
			// This function was intentionally left blank
		}
		function plugin_data() {
			return get_option($this->option_key, array());
		}
		
		/*
		**
		**	Add links to the Plugins page.
		*/
		function plugin_row_meta ($links, $file) {
			if (strpos('padding' . $file, $this->slug)) {
				$links[] = '<a href="' . $this->settings_url . '">' . __('Settings','more-plugins') . '</a>';
				$links[] = '<a href="http://more-plugins.se/forum/forum/' . $this->slug . '/">' . __('Support','more-plugins') . '</a>';
				$links[] = '<a href="http://more-plugins.se/donate/">' . __('Donate','sitemap') . '</a>';
			}
			return $links;
		}
		
		/*
		**
		**
		*/
		function admin_menu () {
			add_options_page($this->name, $this->name, 'edit_pages', $this->slug, array(&$this, 'options_page'));
		}
		
		/*
		**
		**
		*/
		function admin_head () {
			add_thickbox();
			?>
			<script type="text/javascript">
			/* <![CDATA[ */
				(function() {
					var s = document.createElement('script'), t = document.getElementsByTagName('script')[0];
					s.type = 'text/javascript';
					s.async = true;
					s.src = 'http://api.flattr.com/js/0.6/load.js?mode=auto';
					t.parentNode.insertBefore(s, t);
				})();
			/* ]]> */
			</script>
			
			<?php
		
		}
		
		function is_plugin_installed() {
		
		}
		/*
		**
		**
		*/
		function options_page() {
			$this->options_page_wrapper_header();
			
			// Errors trump notifications
			if ($this->error) echo '<div class="updated fade error"><p><strong>' . $this->error . '</strong></p></div>';
			else if ($this->message) echo '<div class="updated fade"><p><strong>' . $this->message . '</strong></p></div>';

			// Load the settings file
			if (!$this->footed) 
				if ($this->settings_file)
					require($this->dir . $this->settings_file);
			$this->options_page_wrapper_footer();
		}
		
		function export_data() {
			$this->options_page_wrapper_header();
			$data = $this->get_data();
			$function = str_replace('-', '_', $this->slug);
			$filter = $function . '_saved';
			$k = $this->keys[1];
			$a = str_replace('-', '_', $k);
			$f = $function . '_saved_' . $a;
			$j = maybe_serialize($data);
			$export = "<?php \nadd_filter('$filter', '$f');\n";
			$export .= "function $f (\$d) {\$d['$k'] = maybe_unserialize('$j', true); return \$d; }\n?>";
			$filename = $a . '.php';
			$dir = $this->dir . 'saved/';

			if (false) {			
				$file = $this->dir . 'registered/' . $filename;
				 if (!$handle = fopen($file, 'a')) {
					echo "Cannot open file ($filename)";
					exit;
				}
				// Write $somecontent to our opened file.
				if (fwrite($handle, $export) === FALSE) {
					echo "Cannot write to file ($filename)";
					exit;
				}
				fclose($handle);
			} 

			$this->navigation_bar(array('Export'));
			?>	
				<p><?php printf(__('The %s plugin can read objects from a file. The default location for these files is in the %s directory. To create a file object, copy the text below (<code>CTRL/CMD + c</code>), paste it into a text file and save it as %s to the aforementioned directory. If an object exists both in the %s settings and as a file, the file will override the data stored in the database.', 'more-plugins'), $this->name, "<code>$dir</code>", "<code>$filename</code>", $this->name); ?></p>
				<p><textarea rows="15" class="large-text readonly" name="rules" id="rules" readonly="readonly"><?php echo esc_html($export); ?></textarea></p>
			<?php
			$this->options_page_wrapper_footer();
		}
		
		/*
		**
		**
		*/
		function data_subset ($args = array()) {
			$ret = array();
			foreach ($this->data as $key => $d) {
				$exclude = false;
				foreach ($args as $k => $a) 
					if ($d[$k] != $a) $exclude = true;				
				if (!$exclude) $ret[$key] = $d;				
			}
			return $ret;
		
		}
		function get_data($s = array(), $override = false) {
			if (empty($s) && !$override) $s = $this->keys;
			if (count($s) == 0) return $this->data;
			if (count($s) == 1) return $this->data[$s[0]];
			if (count($s) == 2) return $this->data[$s[0]][$s[1]];
			if (count($s) == 3) return $this->data[$s[0]][$s[1]][$s[2]];
			if (count($s) == 4) return $this->data[$s[0]][$s[1]][$s[2]][$s[3]];
			return $this->data;
		}		
		function set_data($value, $s = array(), $override = false) {
			if (empty($s) && !$override) $s = $this->keys;
			if (count($s) == 0) $this->data = $value;
			if (count($s) == 1) $this->data[$s[0]] = $value;
			if (count($s) == 2) $this->data[$s[0]][$s[1]] = $value;
			if (count($s) == 3) $this->data[$s[0]][$s[1]][$s[2]] = $value;
			if (count($s) == 4) $this->data[$s[0]][$s[1]][$s[2]][$s[3]] = $value;
			return $this->data;
		}
		function unset_data($s = array()) {
			if (empty($s)) $s = $this->keys;
			$key = array_pop($s);
			$arr = $this->get_data($s, true);
			if (array_key_exists($key, $arr)) unset($arr[$key]);
			$this->set_data($arr, $s, true);
			return $this->data;
		}
		
		/*
		**	settings_init()
		**
		**	Extract variables that define what we're trying to do.
		*/
		function settings_init() {

			// Single vars
			$fs = array('action', 'navigation');
			foreach ($fs as $f) if (array_key_exists($f, $_GET)) $this->{$f} = esc_attr($_GET[$f]);

			// Array vars
			$fs = array('keys', 'action_keys');
			foreach ($fs as $f) {
				if (!array_key_exists($f, $_GET)) continue;
				$a = esc_attr($_GET[$f]);
				$argh = $this->extract_array($a);
				$this->{$f} = $argh;
			}

			$this->after_settings_init();
			
			return true;
		}
		function after_settings_init() {
			/*
			** This function is intentionally left blank
			**
			** Overwritten by indiviudal plugin admin objects, if needed.
			*/
		}
		
		/*
		**
		**	Parse requests...
		*/
		function request_handler () {

			// Load up our data, internal and external
			$this->load_objects();
		
			// Ponce som en lugercheck!
			if (array_key_exists('_wpnonce', $_GET))
				if ($nonce = esc_attr($_GET['_wpnonce']))
					check_admin_referer($this->nonce_action());

			// Check whatever you want - validate_submission should return false if 
			// things don't stack up. 
			if (!($this->validate_sumbission())) {
				if ($this->action == 'save') {
					$keys = $this->keys;
					if (!empty($this->action_keys)) {
						$keys = $this->action_keys;
						$this->keys = $keys;
					}
					$this->set_data($this->extract_submission(), $keys);
				}
				return false;
			}
			
			if ($this->navigation == 'export') {
				return $this->export_data();
			}
			
			if ($this->action == 'move') {
			
				// At what level are we moving?
				$action_keys = $this->extract_array(esc_attr($_GET['action_keys']));
				if (empty($action_keys)) array_push($action_keys, '_plugin');
				$data = $this->get_data($action_keys);

				if (empty($data))
					return $this->error(__('Someting has gone awry. Sorry.', 'more-plugins'));
				
				// Which element is being moved?
				$row = esc_attr($_GET['row']);

				// Move a key
				$up = ('up' == esc_attr($_GET['direction'])) ? true : false;
				$data = $this->move_field($data, $row, $up);

				// Save the data
				$this->set_data($data, $action_keys);
				$this->save_data();
				
			}
			if ($this->action == 'save') {

				$arr = $this->extract_submission();
				// The $_POST['index'] needs to be set externally, this is
				// last index of the data to be saved 
				$index = $arr['index'];
				$keys  = $arr['originating_keys'];
				$old_last_key = $keys[count($keys) - 1];

				// We can only save to '_plugin'
				if ($keys[0] != '_plugin') {
					$arr['ancestor_key'] = $keys[1];
					$keys[0] = '_plugin';
				}

			
				// Is this not new stuff?
				if ($index != $this->add_key) {
					// Ok, so it's not new, but has it changed?
					if ($old_last_key != $index) {
						// The old keys are now redundant
						$this->unset_data($keys);
					}
				}
				// Set the appropiate focus
				array_pop($keys);
				array_push($keys, $index);
				unset($arr['originating_keys']);

				// Set and save and provide feedback
				if (count($keys) > 1) {
					$this->set_data($arr, $keys);
					$this->save_data();
					$this->message = __('Saved!', 'more_plugins');
				}
			}
			if ($this->action == 'delete') {
				$data = $this->unset_data($this->action_keys);
				$this->save_data();
				$this->message = __('Deleted!', 'more-plugins');
			}

			if (count($this->keys) && $this->action == 'add') {

				// Extract the last key
				$last = $this->keys[count($this->keys) - 1];

				// Are we trying to add stuff?
				if ($last == $this->add_key) {
					$this->data = $this->set_data($this->default, $this->keys);				
				}

			}
			
			$this->after_request_handler();
		}
		function after_request_handler() {
			/*
			** This function is intentionally left blank
			**
			** Overwritten by indiviudal plugin admin objects, if needed - mostly
			** used for cross more-plugins functionality
			*/
		}
		function extract_submission() {

			// Add required params
			array_push($this->fields['array'], 'originating_keys');
			array_push($this->fields['var'], 'index');
			array_push($this->fields['var'], 'ancestor_key');

			// Ekkstrakkt
			$arr = array();
			foreach($this->fields['var'] as $key => $field) {
				if (!is_array($field)) {
					$v = (array_key_exists($field, $_POST)) ? esc_attr($_POST[$field]) : '';
					$arr[$field] = (stripslashes($v));
				} else {
					foreach ($field as $f) {
						if (array_key_exists($key . ',' . $f, $_POST))
							$arr[$key][$f] = $_POST[$key . ',' . $f];
					
					}				
				}
			}
			foreach($this->fields['array'] as $level1 => $field) {
				if (!is_array($field)) {
					$vals = (array_key_exists($field, $_POST)) ? $this->extract_array($_POST[$field]) : array();
					foreach ($vals as $k => $v) {
						if (!is_array($v) && !is_object($v)) {
							$arr[$field][$k] = (stripslashes($v));
						} else $arr[$field][$k] = $this->object_to_array($v);
					}
				} else {
					foreach ($field as $level2 => $field2) {
						if (array_key_exists($level1 . ',' . $field2, $_POST)) {
							$post = $this->extract_array($_POST[$level1 . ',' . $field2]);
							if (!empty($post)) $arr[$level1][$field2] = (stripslashes($post[0]));
						}
					}
				}
			}

			return $arr;
		}

		/*
		** 	Might be storing serialized data or might be a 
		**	comma separated list
		*/
		function extract_array($a) {
			// *Might* be storing json data or *might* be a 
			// comma separated list
			
			if (is_array($a)) return $a;
			
			if ($a) {

				// $a be a json object
				$b = json_decode(stripslashes_deep($a), true);
				if (is_array($b)) return $this->slasherize($b, true);
								
				// Is this a comma separated list?
				if (strpos($a, ',')) 
					return explode(',', $a);
				
				// $a is just a single value		
				return array($a);
			}
			
			// $a is empty
			return array();
		}
		
		/*
		**
		**
		*/
		function stripslashes_deep ($string) {
			while(strpos($string, '\\')) 
				$string = stripslashes($string);
			return $string;
		}
		/*
		**
		**
		*/
		function object_to_array($data) {
			if (is_array($data) || is_object($data)) {
				$result = array(); 
				foreach($data as $key => $value) $result[$key] = $this->object_to_array($value); 
    			return $result;
  			}
			return $data;
		}
		/*
		**	Get the index name from the $_POST variable
		**	to be used in validate_submission() in individual
		**	settings classes.
		**
		*/		
		function get_index($key) {
			$val = esc_attr($_POST[$key]);
			$val = sanitize_title($val);
			$val = str_replace('-', '_', $val);
			return $val;		
		}
		/*
		**
		**
		*/
/*
		function read_data() {
			return array();
		}
*/
		/*
		**
		**
		*/
		function save_data($data = array()) {
			if (empty($data)) $data = $this->data['_plugin'];
			update_option($this->option_key, $data);
		}
		
		
		/*
		**
		**	Overwrite this function in subclass to validate
		**	the submission data.
		*/		
		function validate_sumbission () {
			// Somthing
			return true;
		}

		/*
		**
		**
		*/
		function error($error) {
			$this->error = $error;
			return false;
		}

		/*
		**
		**
		*/
		function set_navigation($navigation) {
			$_GET['navigation'] = $navigation;
			$_POST['navigation'] = $navigation;
			$this->navigation = $navigation;
			return $navigation;
		}	
		/*
		**
		**
		*/
		function options_page_wrapper_header () {
			if ($this->headed) return false;
			$url = get_option('siteurl');
			?>
				<div class="wrap">
				<div id="more-plugins" class="metabox-holder has-right-sidebar <?php echo $this->slug; ?> <?php echo $this->slug . '-' . $this->navigation; ?>">		
				
					<div id="icon-options-general" class="icon32"><br /></div>
					<h2><?php echo $this->name; ?></h2>
	
					<div class="inner-sidebar">
						<div id="side-sortables" class="meta-box-sortabless ui-sortable" style="position:relative;">
				
							<div id="<?php echo $this->slug; ?>-information" class="postbox">
								<h3 class="hndle"><span><?php _e('About this Plugin', 'more-plugins'); ?></span></h3>
								<div class="inside">
								
									<ul>
										<li><a href="http://more-plugins.se/plugins/<?php echo $this->slug; ?>/">Plugin homepage</a></li>
										<li><a href="http://more-plugins.se/forum/">Plugin support forum</a></li>
										<li><a href="http://wordpress.org/tags/<?php echo $this->slug; ?>?forum_id=10">Wordpress Forum</a></li>
										<li><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&#38;business=h.melin%40gmail.com&#38;item_name=<?php echo str_replace(' ', '%20', $this->name); ?>%20Plugin&#38;no_shipping=0&#38;no_note=1&#38;tax=0&#38;currency_code=USD&#38;bn=PP%2dDonationsBF&#38;charset=UTF%2d8&#38;lc=US">Donate with PayPal</a></li>
										<li>
											<a class="FlattrButton" style="display:none;" href="http://more-plugins.se/plugins/more-fields/"></a>
											<noscript><a href="http://flattr.com/thing/386416/More-Plugins" target="_blank">
											<img src="http://api.flattr.com/button/flattr-badge-large.png" alt="Flattr this" title="Flattr this" border="0" /></a></noscript>										</li>

									</ul>
							
								</div>
							</div>
							
							<div id="more-plugins-box" class="postbox">
								<h3 class="hndle"><span>More Plugins</span> <em><?php _e('Get More out of WordPress', 'more-plugins'); ?></em></h3>
								<div class="inside plugin-install-php">
								
									<ul class="action-links">
									
									<!-- MORE FIELDS -->
										<li class="more-fields">
											<dl>
									<?php if (is_plugin_active('more-fields/more-fields.php')) : ?>
												<dt><a href="options-general.php?page=more-fields">More Fields</a></dt>
									<?php else : ?>									
												<dt><a href="<?php echo $url; ?>/wp-admin/plugin-install.php?tab=plugin-information&#38;plugin=more-fields&#38;TB_iframe=true&#38;width=640&#38;height=679" class="thickbox" title="Install More Fields">More Fields</a></dt>
									<?php endif; ?>
												<dd><?php _e('Adds more input boxes to the Write/Edit screen for any post type.', 'more-plugins'); ?></dd>
											</dl>
										</li>
										
									<!-- MORE TYPES -->
										<li class="more-types">
											<dl>
									<?php if (is_plugin_active('more-types/more-types.php')) : ?>
												<dt><a href="options-general.php?page=more-types">More Types</a></dt>
									<?php else : ?>									
												<dt><a href="<?php echo $url; ?>/wp-admin/plugin-install.php?tab=plugin-information&#38;plugin=more-types&#38;TB_iframe=true&#38;width=640&#38;height=679" class="thickbox" title="Install More Types">More Types</a></dt>
									<?php endif; ?>
												<dd><?php _e('Adds more post types to your WordPress installation.', 'more-plugins'); ?></dd>
											</dl>
										</li>
									
									<!-- MORE TAXONOMIES -->
										<li class="more-taxonomies">
											<dl>
									<?php if (is_plugin_active('more-taxonomies/more-taxonomies.php')) : ?>
												<dt class="more-taxonomies"><a href="options-general.php?page=more-taxonomies">More Taxonomies</a></dt>
									<?php else : ?>									
												<dt class="more-taxonomies"><a href="<?php echo $url; ?>/wp-admin/plugin-install.php?tab=plugin-information&#38;plugin=more-taxonomies&#38;TB_iframe=true&#38;width=640&#38;height=679" class="thickbox" title="Install More Taxonomies">More Taxonomies</a></dt>
									<?php endif; ?>
												<dd class="more-taxonomies"><?php _e('Add new taxonomies - means by which to classify your posts and pages. ', 'more-plugins'); ?></dd>
											</dl>
										</li>

										<!--<li class="more-thumbnails"><a href="#">More Thumbnails</a></li>-->
										<!--<li class="more-roles"><a href="#">More Roles</a></li>-->
									</ul>
							
								</div>
							</div>
				
						</div>
					</div>
	
					<div id="post-body">
						<div id="post-body-content" class="has-sidebar-content">
					<?php
				$this->headed = true;
	
		}

		/*
		**
		**
		*/
		function options_page_wrapper_footer() {
			if ($this->footed) return false;
			?>
						</div> 
					</div>
				<!-- more-plugins --></div>
			<!-- /wrap --></div>
			<?php
			$this->footed = true;
		}
		
		/*
		**
		**
		*/
		function condition($condition, $message, $type = 'error') {
	
			if (!isset($this->is_ok)) $this->is_ok = true;
	
			// If there is an error already return
			if (!$this->is_ok && $type = 'error') return $this->is_ok;
	
			if ($condition == false && $type != 'silent') {
				echo '<div class="updated fade"><p>' . $message . '</p></div>';
	
				// Don't set the error flag if this is a warning.
				if ($type == 'error') $this->is_ok = false;
			}
		
			return ($condition == true);
		}
		
		/*
		**
		**
		*/
		function checkboxes($name, $title, $values, $arr) {
			?>
			<tr>
				<th scope="row" valign="top"><?php echo $title; ?></th>
				<td>
					<?php foreach ($values as $key => $title2) : 
		// 					$selected = ($arr[$name] == $key) ? ' checked="checked"'	: '';	
							$checked = (in_array($key, (array) $arr[$name])) ? " checked='checked'" : '';
		
					?>
						<label><input type="checkbox" name="<?php echo $name; ?>[]" value="<?php echo $key; ?>" <?php echo $checked; ?>> <?php echo $title2; ?></label>
					<?php endforeach; ?>
				</td>
			</tr> 	
			<?php
		}

		/*
		**
		**
		*/

		function bool_var($name, $title, $arr) {
			?>
			<tr>
				<th scope="row" valign="top"><?php echo $title; ?></th>
				<td>
					<?php
							$true = ($arr[$name]) ? " checked='checked'" : '';
							$false = ($true) ?  '' : " checked='checked'";
					?>
						<label><input type="radio" name="<?php echo $name; ?>" value="true" <?php echo $true; ?>> <?php echo $title2; ?> Yes</label>
						<label><input type="radio" name="<?php echo $name; ?>" value="false" <?php echo $false; ?>> <?php echo $title2; ?> No</label>
				</td>
			</tr> 	
			<?php
		
		}
		
		/*
		**
		**
		*/
		function move_field ($data, $nbr, $up = true) {
	
			// Are we moving out of bounds?
			if (count($data) == 1) return $data;
			if ($nbr >= count($data) - 1 && !$up) return $data;
			if ($nbr == 0 && $up) return $data;
	
			$new = array();
			$ctr = 0;
			$offset = ($up) ? 0 : 1;
			foreach ($data as $key => $arr) {
				if ($ctr == $nbr - 1 + $offset) $tmp_key = $key;
				else $new[$key] = $arr;
				if ($ctr == $nbr + $offset) $new[$tmp_key] = $data[$tmp_key];
				$ctr++;
			}
			return $new;

		}

		/*
		**
		**
		*/
		function updown_link ($nbr, $total, $args = array()) {
			$html = '';
			$link = array('row' => $nbr, 'navigation' => $this->navigation, 'action' => 'move');

			// Are we adding more stuff to our link?
			if (!empty($args)) $link = array_merge($link, $args);

			// Build the links
			if ($nbr > 0) $html .= ' | ' . $this->settings_link('&uarr;', array_merge($link, array('direction' => 'up')));
			if ($nbr < $total - 1) $html .= ' | ' . $this->settings_link('&darr;', array_merge($link, array('direction' => 'down')));
			return $html;
		}
		
		/*
		**
		**
		*/
		function settings_link ($text, $args) {
			$link = $this->options_url;
			foreach ($args as $key => $value) {
				if ($key == 'class') continue;
				if (!$value) continue;
				if (is_array($value)) $value = implode(',', $value);
				$link .= '&' . $key . '=' . urlencode($value);
			}
			$link = wp_nonce_url($link, $this->nonce_action($args));
			$argcl = (array_key_exists('class', $args)) ? $args['class'] : 0;
			$class = ($c = $argcl) ? $c : 'more-common';
			$html = "<a class='$class' href='$link'>$text</a>";
			if (!$text) return $link;
			return $html;
		}

		/*
		**
		**
		*/
		function nonce_action($args = array()) {

			if (empty($args)) $args = $_GET;

			$action = $this->slug . '-action_';
			if (array_key_exists('navigation', $args)) 
				if ($a = esc_attr($args['navigation'])) $action .= $a;			
			if (array_key_exists('action', $args)) 
				if ($a = esc_attr($args['action'])) $action .= $a;

			return $action;		
		}
		/*
		**
		**
		*/
		function table_header($titles) {
			?>
			<table class="widefat">
				<thead>
					<tr>
						<?php foreach ((array) $titles as $title) : ?>
						<th><?php echo $title; ?></th>
						<?php endforeach; ?>
					</tr>
				</thead>
				<tbody>
			<?php
		}

		/*
		**
		**
		*/
		function table_footer($titles) {
			?>
				</tbody>
				<tfoot>
					<tr>
						<?php foreach ((array) $titles as $title) : ?>
						<th><?php echo $title; ?></th>
						<?php endforeach; ?>
					</tr>
				</tfoot>
			</table>
			<?php
		}

		/*
		**
		**
		*/
		function table_row($contents, $nbr, $class = '') {
			$class .= ($nbr++ % 2) ? ' alternate ' : '' ;
			?>
			<tr class="<?php echo $class; ?>">
				<?php foreach ((array) $contents as $content) : ?>
				<td><?php echo $content; ?></td>
				<?php endforeach; ?>
			</tr>
			<?php
		}

		/*
		**
		**
		*/
		function setting_row($cols, $class = '') {
			?>
				<tr class="<?php echo $class; ?>">
					<th scope="row" valign="top"><?php echo array_shift($cols); ?></th>
					<?php foreach ($cols as $col) : ?>
						<td>
							<?php echo $col; ?>
		 				</td>
					<?php endforeach; ?>
	 			</tr>
			<?php
		}


		function get_val($name, $k = array()) {
			if (empty($k)) $k = $this->keys;
			$s = array();

			// Deal with comma separated keys 
			foreach ((array) $k as $b) {
				if (strpos($b, ',')) {
					$c = explode(',', str_replace(' ', '', $b));
					foreach($c as $d) $s[] = $d;
				}
				else $s[] = $b;
			}

			// Deal with comma separated field names			
			if (strpos($name, ',')) {
				$c = explode(',', str_replace(' ', '', $name));
				foreach($c as $d) $s[] = $d;
			} else $s[] = $name;

			// Iterate through the data
			$subdata = $this->data;
			foreach ($s as $key) {
				if (array_key_exists($key, $subdata)) $subdata = $subdata[$key];
				else $subdata = '';
			}
//__d($name);
			if (!is_array($subdata)) $subdata = stripslashes($subdata);
			return $subdata;
		

		}
		/*
		**
		**
		*/
		function settings_input($name, $s = array()) {
			$value = esc_attr($this->get_val($name, $s));
			$html = '<input class="input-text" type="text" name="' . $name . '" value="' . $value . '">';		
			return $html;
		}

		/*
		**
		**
		*/
		function settings_bool($name) {
			$vars = array(true => 'Yes', false => 'No');
			$html = $this->settings_radiobuttons($name, $vars);
			return $html;
		}

		function settings_radiobuttons($name, $vars, $comments = array()) {
			$html = '';
			$set = $this->get_val($name);
			foreach ($vars as $key => $value) {
				$checked = ($key == $set) ? ' checked="checked"' : '';
				$html .= "<label><input class='input-radio' type='radio' name='$name' value='$key' $checked /> $value</label> ";		
					if (array_key_exists($key, $comments)) if ($c = $comments[$key]) $html .= $this->format_comment($c);
			}
			return $html;
		}
		function settings_hidden($name, $var = 0) {
			if (!$var) $var = $this->get_val($name);
			$value = ($var) ? json_encode($this->slasherize($var)) : '';
			$html = "<input type='hidden' name='$name' value='$value'>";
			return $html;
		}
		function slasherize ($var, $strip = false) {		
			$ret = array();
			$word = '2ew8dhpf7f3';
			foreach ($var as $k => $v) {
				if (!$strip) $ret[$k] = (is_array($v)) ? $this->slasherize($v) :  str_replace(array('"', "'"), array($word, strrev($word)), stripslashes_deep(htmlspecialchars_decode($v)));
				else $ret[$k] = (is_array($v)) ? $this->slasherize($v, true) :  str_replace(array($word, strrev($word)), array('"', "'"), $v);
			}
			return $ret;
		}
	
		/*
		**
		**
		*/
		function get_roles() {
			global $wp_roles;	
			$user_levels = array();
			foreach($wp_roles->roles as $role) { 
				$name = str_replace('|User role', '', $role['name']);
				$value = sanitize_title($name); 
				if ($value) $user_levels[$value] = $name;
			}
			return $user_levels;
		}

		/*
		**
		**
		*/
		function checkbox_list($name, $vars, $options = array()) {
			$values = (array) $this->get_val($name);
			$html = '';

			foreach ($vars as $key => $val) {
				// Options will over-ride values
//				$okc = (array_key_exists('class', $options[$key])) ? $options[$key]['class'] : 0;
				$ok = (array_key_exists($key, $options)) ? $options[$key] : array();
				$okc = (array_key_exists('class', $ok)) ? $ok['class'] : '';
				$class = ($a = $okc) ? 'class="' . $a . '"' : '';
				$okc = (array_key_exists('disabled', $ok)) ? $ok['disabled'] : '';
				$readonly = ($okc) ? ' disabled="disabled"' : '';
				
				
				if (array_key_exists('value', $ok))
					$checked = ($options[$key]['value']) ? ' checked="checked" ' : '';
				else $checked = (in_array($key, $values)) ? ' checked="checked"' : '';
				
				$html .= "<label><input class='input-check' type='checkbox' value='$key' name='${name}[]' $class $readonly $checked /> $val</label>";
				$okc = (array_key_exists('text', $ok)) ? $ok['text'] : '';
				if ($t = $okc) $html .= '<em>' . $t . '</em>';
			}
		//	$html .= '<input type="hidden" name="' . $name . '_values" value="' . implode(',', array_keys($vars)) . '">';
			return $html;		
		}
		
		function settings_select($name, $vars) {
			$values = $this->get_val($name);
			$html = "<select class='input-select' name='$name'>";
			foreach ($vars as $key => $val) {
				$checked = ($key == $values) ? ' selected="selected"' : '';
				$html .= "<option value='$key' $checked> $val</option>";
			}
			$html .= "</select>";
			return $html;		
		}
		function settings_textarea($name) {
			$value = $this->get_val($name);
			$html = "<textarea class='input-textarea' name='$name'>$value</textarea>";
			return $html;
		
		}


		/*
		**
		**
		function add_button ($options) {
			?>
			<form method="GET" ACTION="<?php echo $this->options_url; ?>">
			<input type="hidden" name="page" value="<?php echo $this->slug; ?>">
			<input type="hidden" name="navigation" value="<?php echo $options['navigation']; ?>">
			<input type="hidden" name="action" value="<?php echo $options['action']; ?>">
			<p><input class="button-primary" type="submit" value="<?php echo $options['title']; ?>"></p>
			</form>
			<?php
		
		}
		*/
		
		/*
		**
		**
		*/
		function navigation_bar($levels) {
		?>
			<ul id="more-plugins-edit">
			<li><a href="<?php echo $this->settings_url; ?>"><?php echo $this->name; ?></a></li>
			<?php 
				for ($i=0; $i<count($levels); $i++) {
					$selected = ($i == count($levels) - 1) ? ' selected="selected"' : '';
					echo '<li ' . $selected . '">' . $levels[$i] . '</li>';
				}
			 ?>
			</ul>
		<?php
		}



		/*
		**
		**
		*/
		function settings_head () {
			?>
			<script type="text/javascript">
			//<![CDATA[
				jQuery(document).ready(function($){
					$("a.more-common-delete").click(function(){
						return confirm("<?php _e('Are you sure you want to 	delete?'); ?>");
					});
					$(".more-advanced-settings-toggle").click(function(){
						$('div.more-advanced-settings').slideToggle();
						return false;
					});
				});
			//]]>
			</script>
			<?php			
				if (!defined('MORE_PLUGINS_DEV')) 
					$css = $this->url . 'more-plugins/more-plugins.css';
				else $css = WP_PLUGIN_URL . '/more-plugins.css';
			?>
				<link rel='stylesheet' type='text/css' href='<?php echo $css; ?>' />
			<?php
		}
		function settings_form_header($args = array()) {
			$defaults = array('action' => 'save', 'keys' => $_GET['keys']);
			$args = wp_parse_args($args, $defaults);
			?>
			<?php $url = $this->settings_link(false, $args); ?>
			<form method="post" action='<?php echo $url; ?>'>
			<?php 
		}
		function format_comment($comment) {
			return '<em>' . $comment . '</em>';
		}
		function settings_save_button() {
			$keys = implode(',', (array) $this->keys);
		?>
			<input type="hidden" name='ancestor_key' value='<?php echo $this->get_val("ancestor_key"); ?>' />
			<input type="hidden" name='originating_keys' value='<?php echo $keys; ?>' />
			<input type="hidden" name='action' value='save' />
			<input type="submit" class='button' value='<?php _e('Save', 'more-plugins'); ?>' />		
			</form>

		<?php
		}
		
		function get_post_types() {
			global $wp_post_types;
			$ret = array();
			foreach ($wp_post_types as $key => $pt) {
				$name = ($t = $pt->labels->singular_name) ? $t : $pt->label;
				$ret[$key] = $name;	
			}
			return $ret;
		}
		function permalink_warning() {
			global $wp_rewrite;
			if (empty($wp_rewrite->permalink_structure)) {
				$html = '<em class="warning">';
				$html .= __('Permalinks are currently not enabled! To use this feature, enable permalinks in the <a href="options-permalink.php">Permalink Settings</a>.', 'more-plugins');			
				$html .= '</em>';
				return $html;
			}
			else return '';
		}
	} // end class

	load_plugin_textdomain( 'more-plugins', false, dirname( plugin_basename( __FILE__ ) ) );

	define($more_common, true);

} // endif defined


if (!is_callable('__d')) {
	function __d($d) {
		echo '<pre>';
		print_r($d);
		echo '</pre>';	
	}
}

?>