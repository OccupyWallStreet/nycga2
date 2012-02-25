<?php
/*
Plugin Name: Q2W3 Post Order
Plugin URI: http://www.q2w3.ru/q2w3-post-order-wordpress-plugin/
Description: With Q2W3 Post Order you can can change natural order of posts. Supported custom taxonomies and custom post type archive pages. Requires WP 3.1 or higher. 
Author: Max Bond, AndreSC
Version: 1.2.2
Author URI: http://www.q2w3.ru/
*/

//Hooks

if (is_admin()) {

	register_activation_hook( __FILE__, array( 'q2w3_post_order', 'install' ) );
		
	add_filter( 'plugin_action_links_'.plugin_basename(__FILE__), array( 'q2w3_post_order', 'control_links' ) );
	
	add_action( 'admin_menu', array( 'q2w3_post_order', 'admin_menu_entry' ) );

	add_action( 'delete_post', array( 'q2w3_post_order', 'delete_post_sync' ) );
	
	add_filter( 'set-screen-option', array( 'q2w3_post_order', 'screen_options_save' ), 10, 3);
	
	q2w3_post_order::load_language(); // load translation

} else {
	
	add_filter( 'posts_request', array( 'q2w3_post_order', 'sort' ) );
	
	add_filter( 'post_class', array( 'q2w3_post_order', 'post_class' ) );
	
} 



if ( class_exists('q2w3_post_order', false) ) return; // if class allready loaded return control to the main script

/**
 * Main plugin class. All functions are static.
 * 
 */
class q2w3_post_order {
	
	const ID = 'q2w3-post-order'; // plugin ID, also used as text domain
	
	const NAME = 'Q2W3 Post Order'; // plugin name
	
	const WP_VER = '3.1.0'; // Required WP version
	
	
	const POST_TABLE_OPTION = 'q2w3_post_table_ver'; // option name to store table version
	
	const META_TABLE_OPTION = 'q2w3_post_meta_table_ver'; // option name to store table version
	
	
	const POST_TABLE = 'q2w3_post_order';
		
	const POST_TABLE_VER = '1';
		
	const META_TABLE = 'q2w3_post_order_meta';
	
	const META_TABLE_VER = '1';
	
	
	public static $page_id;
	
	public static $default_post_types = array('post');
			
	public static $restricted_post_types = array('page', 'attachment', 'revision', 'nav_menu_item');
	
	public static $default_taxonomies = array('category', 'post_tag');
	
	public static $restricted_taxonomies = array('link_category', 'nav_menu', 'post_format');
	
	
	
	/**
	 * Check WP Version
	 * 
	 * @return true or wp_die()
	 * 
	 */
	public static function wp_version_check() {
	
		global $wp_version;
		
		if (version_compare($wp_version, self::WP_VER, '<')) {
    
			deactivate_plugins(plugin_basename(__FILE__));
    
			wp_die(__('Installed WordPress version', self::ID) . ' - '. $wp_version .' ' . __('is incompatible with this plugin. Please update to version', self::ID) .' '. self::WP_VER .' '.__('or higher!',self::ID));
		
		} else {
		
			return true;
		
		}
	
	}
	
	/**
	 * Returns posts table name
	 * 
	 * @return string
	 * 
	 */
	public static function posts_table() {
		
		static $table = false;
		
		if (!$table) {

			global $wpdb;
					
			$table = $wpdb->prefix.self::POST_TABLE;
		
		}
		
		return $table;
		
	}
	
	/**
	 * Returns meta table name
	 * 
	 * @return string
	 * 
	 */
	public static function meta_table() {
		
		static $table = false;
		
		if (!$table) {

			global $wpdb;
			
			$table = $wpdb->prefix.self::META_TABLE;
		
		}
		
		return $table;
		
	}
	
	/**
	 * Returns form action URL 
	 * 
	 * @return string
	 * 
	 */
	public static function action_url() {
		
		static $url = false;
		
		if (!$url) {
		
			$url = plugins_url().'/q2w3-post-order/post.php';
		
		}

		return $url;
			
	}
	
	/**
	 * Plugin install function. Creates and updates db tables.
	 * 
	 */
	public static function install() {
		
		global $wpdb;
		
		self::wp_version_check(); 
		
		$cur_table_ver = get_option(self::POST_TABLE_OPTION);
		
		$cur_meta_table_ver = get_option(self::META_TABLE_OPTION);
		
		$sql = "CREATE TABLE " . self::posts_table() . " (
			id bigint( 20 ) NOT NULL AUTO_INCREMENT ,
			post_id bigint( 20 ) NOT NULL ,
			term_id int( 10 ) NOT NULL ,
			post_rank int NOT NULL ,
			taxonomy varchar( 32 ) COLLATE utf8_general_ci NULL,
			post_type varchar( 20 ) COLLATE utf8_general_ci NULL,
			PRIMARY KEY id ( id ),
			INDEX post_id ( post_id )
		);";
	
   		$meta_sql = "CREATE TABLE " . self::meta_table() . " (
			id bigint( 20 ) NOT NULL AUTO_INCREMENT ,
			option TEXT NULL,
			taxonomy varchar( 32 ) COLLATE utf8_general_ci NULL,
			term_id int( 10 ) NOT NULL ,
			post_type varchar( 20 ) COLLATE utf8_general_ci NULL,
			PRIMARY KEY  id ( id ),
			INDEX term_id ( term_id )
		);";
		
		$tables = array( self::posts_table() => array( 'option_name' => self::POST_TABLE_OPTION, 'table_ver' => self::POST_TABLE_VER, 'cur_table_ver' => $cur_table_ver, 'sql' => $sql ),
				  		 self::meta_table() => array( 'option_name' => self::META_TABLE_OPTION, 'table_ver' => self::META_TABLE_VER, 'cur_table_ver' => $cur_meta_table_ver, 'sql' => $meta_sql ) );
		
		foreach ($tables as $table_name => $table_meta) {
			
			if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name || $table_meta['cur_table_ver'] != $table_meta['table_ver']) { // if table not exists or different table version 
      
				if(@file_exists(ABSPATH.'/wp-admin/includes/upgrade.php')) {
				
					include_once(ABSPATH.'/wp-admin/includes/upgrade.php');
		
				} else {
			
					wp_die(__('Can not find /wp-admin/includes/upgrade.php', self::ID));
		
				}
	      
				dbDelta($table_meta['sql']);
	
				if ($table_meta['cur_table_ver']) { // table version option
				
					update_option($table_meta['option_name'], $table_meta['table_ver'], '', 'no');
				
				} else {
	
					add_option($table_meta['option_name'], $table_meta['table_ver'], '', 'no');
			
				}
		
			}
				
		}
		
	}
	
	/**
	 * Modifies plugin control links
	 * 
	 * @param array $links
	 * 
	 * @return array
	 * 
	 */
	public static function control_links($links) {
	
		if (array_key_exists('deactivate',$links)) {
			
			$index = 'deactivate'; // compatibility with WP 3.0
			
		} else {
			
			$index = 0;
			
		}
		
		$links[$index] = '<a href="options-general.php?page='. self::ID .'&amp;deactivate=true">'. __('Deactivate') .'</a>'; // changes default plugin deactivation link // now it points to my custom plugin deactivation page
		
		$settings_link = '<a href="options-general.php?page='. self::ID .'">'. __('Settings') .'</a>'; // Direct link to plugin settings page
		
		array_unshift($links,$settings_link); // adds settings link before other links
		
		return $links;
	
	}
	
	/**
	 * Loads language files
	 * 
	 */
	public static function load_language($folder = 'languages') {
	
		$currentLocale = get_locale();
	
		if (!empty($currentLocale)) {
				
			$moFile = dirname(__FILE__)."/$folder/".$currentLocale.".mo";
		
			if (@file_exists($moFile) && is_readable($moFile)) load_textdomain(self::ID, $moFile);
			
		}
	
	}
	
	/**
	 * Registers admin menu entry
	 * 
	 */
	public static function admin_menu_entry() {
       
		$access_level = 'activate_plugins'; // Super Admin and Admin
				
		$options = get_option(self::ID);
		
		if (isset($options['editors_access']) && $options['editors_access']) $editors_access = true; else $editors_access = false;
		
		if ($editors_access) $access_level = 'moderate_comments'; // Super Admin, Admin and Editor

		self::$page_id = add_options_page(self::NAME, self::NAME, $access_level, self::ID, array( __CLASS__, 'settings_page' ) ); // Add a new menu under Manage 
		
		add_action( 'manage_'. self::$page_id .'_columns', array(__CLASS__, 'screen_options') );
		
		add_action( 'contextual_help_list', array( __CLASS__, 'help_section' ) );
		
		add_action( 'admin_init', array( __CLASS__, 'reg_settings' ) ); // registers settings
		
	}
	
	/**
	 * Loads user settings stored in wp_usermeta table
	 * 
	 */
	protected static function user_settings_load() {
		
		static $settings = NULL;
		
		if (!$settings)	$settings = unserialize(get_user_option(self::safe_plugin_id(self::ID))); // get user options from db
			
		return $settings;
				
	}
	
	/**
	 * Adds properties to Screen Options panel
	 * 
	 * @param array $columns
	 * 
	 * @return array
	 * 
	 */
	public static function screen_options($columns) {
		
		if (isset($_GET['deactivate'])) return false;
		
		global $wp_post_types, $wp_taxonomies;

		$settings = self::user_settings_load();
		
		$res = '</label><script type="text/javascript">jQuery("label[for=\'screen_options_hack-hide\']").hide()</script>'.PHP_EOL;
	
		$res .= '<span style="float: left; font-weight: bold">'.__('Post Types', self::ID).': </span><div style="clear: left"></div>';
		
		foreach ($wp_post_types as $post_type) {
			
			if (!in_array($post_type->name, self::$restricted_post_types)) {
				
				$disabled = '';
				
				if (in_array($post_type->name, self::$default_post_types)) {
					
					$settings['post_types'][$post_type->name] = '1';
					
					$disabled = 'disabled="disabled"';
					
				}
				
				$res .= '<span style="float: left; margin-left: 9px">';
				
				$res .= '<input type="checkbox" name="wp_screen_options[value][post_types]['. $post_type->name .']" value="1" '. checked('1', $settings['post_types'][$post_type->name], false) .' '. $disabled .'/> '.$post_type->labels->name;
				
				$res .= '</span>';
												
			}
						
		}

		$res .= '<div style="clear: left"></div>'.PHP_EOL;
		
		$res .= '<span style="float: left; font-weight: bold">'.__('Taxonomies', self::ID).': </span><div style="clear: left"></div>';
				
		foreach ($wp_taxonomies as $taxonomy) {
			
			if (!in_array($taxonomy->name, self::$restricted_taxonomies)) {
				
				$disabled = '';
				
				if (in_array($taxonomy->name, self::$default_taxonomies)) {
					
					$settings['taxonomies'][$taxonomy->name] = '1';
					
					$disabled = 'disabled="disabled"';
					
				}
				
				$res .= '<span style="float: left; margin-left: 9px">';
				
				$res .= '<input type="checkbox" name="wp_screen_options[value][taxonomies]['. $taxonomy->name .']" value="1" '. checked('1', $settings['taxonomies'][$taxonomy->name], false) .' '. $disabled .'/> '.$taxonomy->labels->name;
				
				$res .= '</span>';
				
			}
			
		}

		if (!$settings['rows_per_page']) $rpp = 20; else $rpp = $settings['rows_per_page'];
		
		$res .= '<div style="clear: left;"></div>'.PHP_EOL;
	
		$res .= '<h5>'. __('Posts Table Settings', self::ID) .'</h5><div class="screen-options">';
		
		$res .= '<div style="margin-left: 9px;">'. __('Rows per page', self::ID).': <input type="text" class="screen-per-page" name="wp_screen_options[value][rows_per_page]" maxlength="3" value="'. $rpp .'" /></div>'.PHP_EOL;
		
		$res .= '<input type="hidden" name="wp_screen_options[option]" value="'. self::safe_plugin_id(self::ID) .'" />';
		
		$res .= '<br/><input type="submit" class="button" value="'. __('Apply', self::ID) .'" />';
				
		$res .= '</div>';
		
		return array('screen_options_hack' => $res);
		
	}

	/**
	 * Saves Screen options panel parameters 
	 * 
	 */
	public static function screen_options_save($value, $option_name, $new_settings) {
		
		return serialize($new_settings);
		
	}
	
	/**
	 * Adds text Help panel 
	 * 
	 * @param array $help_content
	 * @return array
	 */
	public static function help_section($help_content) {
		
		$help = '<h5>'. __('How to remove posts from Sorted group?', self::ID) .'</h5>';
		
		$help .= '<p>'. __('Set position number 0 for selected posts and then they\'ll return to Unsorted group.', self::ID) .'</p>';
		
		$help .= '<h5>---</h5>';
		
		$help .= '<p>'. __('Problems, questions, ideas?', self::ID).' <a href="http://www.q2w3.ru/q2w3-post-order-wordpress-plugin/" target="_blank">'.__('Visit Plugin Home Page', self::ID).'</a>'.'</p>';
		
		$help_content[self::$page_id] = $help;
		
		return $help_content;
		
	}
	
	/**
	 * Outputs content of the plugin settings page
	 * 
	 */
	public static function settings_page() {
		
		// load deactivation page
		
		if (key_exists('deactivate', $_GET) && $_GET['deactivate'] == 'true') {
			
			self::deactivate_page(); 
			
			return;
			
		}

		// load options page
		
		if (key_exists('options', $_GET) && $_GET['options'] == 'true') {
			
			self::options_page(); 
			
			return;
			
		}

		// load main page
		
		global $wp_post_types, $wp_taxonomies;
		
		$post_type = '';
		
		$tax_name = '';
		
		$term_id = '';
		
		$settings = self::user_settings_load();
		
		if (isset($_GET['p_type'])) $post_type = sanitize_key($_GET['p_type']);
		
		if (isset($_GET['tax_name'])) $tax_name = sanitize_key($_GET['tax_name']); else $tax_name = 'category'; // Default taxonomy - category

		if (isset($_GET['term_id'])) $term_id = (int)$_GET['term_id'];
		
		echo '<div class="wrap"><h2>'. self::NAME .'</h2>';
		
		// Options page link
		
		echo '<ul class="subsubsub">'.PHP_EOL;
		
		echo '<li><a href="?page='. self::ID .'&amp;options=true" style="padding-left: 0">'. __('General Options', self::ID) .'</a></li>'.PHP_EOL;
		
		echo '</ul>'.PHP_EOL;
		
		echo '<div class="clear"></div>'.PHP_EOL;
		
		// Links for post_tpes
		
		echo '<ul class="subsubsub">'.PHP_EOL;
		
		echo '<li style="color: black">'. __('Post Types', self::ID) .': </li>'.PHP_EOL;
		
		foreach ($wp_post_types  as $wp_post_type ) { 
    
			if (!in_array($wp_post_type->name, self::$restricted_post_types) && (in_array($wp_post_type->name, self::$default_post_types) || isset($settings['post_types'][$wp_post_type->name]))) {

				if ($wp_post_type->name == $post_type) {
					
					$p_links[] = '<li>'. $wp_post_type->labels->name .'</li>'.PHP_EOL;
					
				} else {
				
					$p_links[] = '<li><a href="?page='. self::ID .'&amp;p_type='. $wp_post_type->name .'">'. $wp_post_type->labels->name .'</a></li>'.PHP_EOL;
				
				}
				
			}
				
		}
		
		echo implode(' | ', $p_links);
		
		echo '</ul>'.PHP_EOL;
		
		echo '<div class="clear"></div>'.PHP_EOL;
		
		// Links for taxonomies
		
		echo '<ul class="subsubsub">'.PHP_EOL;
		
		echo '<li style="color: black">'. __('Taxonomies', self::ID) .': </li>'.PHP_EOL;
		
		foreach ($wp_taxonomies  as $wp_taxonomy ) {
    
			if (!in_array($wp_taxonomy->name, self::$restricted_taxonomies) && (in_array($wp_taxonomy->name, self::$default_taxonomies) || isset($settings['taxonomies'][$wp_taxonomy->name]))) {

				if ($wp_taxonomy->name == $tax_name && !$post_type) {
					
					$tax_links[] = '<li>'. $wp_taxonomy->labels->name .'</li>'.PHP_EOL;
					
				} else {
				
					$tax_links[] = '<li><a href="?page='. self::ID .'&amp;tax_name='. $wp_taxonomy->name .'">'. $wp_taxonomy->labels->name .'</a></li>'.PHP_EOL;
				
				}
				
			}
				
		}
			
		echo implode(' | ', $tax_links);
		
		echo '</ul>'.PHP_EOL;
		
		echo '<div class="clear"></div>'.PHP_EOL;
		
		if ($tax_name && !$term_id && !$post_type) { // load terms table
		
			require_once('list-terms.php');
		
		} elseif ($post_type || $term_id) { // load posts table
			
			require_once('list-posts.php');
			
		}				
	
	}
	
	/**
	 * Outputs plugin options page
	 * 
	 */
	protected static function options_page() {
		
		$options = get_option(self::ID);
		
		if (isset($options['editors_access'])) $editors_access = $options['editors_access']; else $editors_access = '';
		
		if (isset($options['debug_mode'])) $debug_mode = $options['debug_mode']; else $debug_mode = '';
		
		echo '<div class="wrap">'.PHP_EOL;
		
		echo '<h2>'. self::NAME .' &raquo; '. __('Options', self::ID) .'</h2>'.PHP_EOL;
		
		echo '<ul class="subsubsub">'.PHP_EOL;
		
		echo '<li><a href="?page='. self::ID .'">&laquo; '. __('Back', self::ID) .'</a></li>'.PHP_EOL;
		
		echo '</ul>'.PHP_EOL;
			
		echo '<form method="post" action="options.php">'.PHP_EOL;
		
		echo settings_fields(self::ID);
		
		echo '<table class="form-table">'.PHP_EOL;
        
		echo '<tr valign="top">'.PHP_EOL;
		
		echo '<td style="width: 20px;"><input type="checkbox" name="'. self::ID .'[editors_access]" '. checked($editors_access, 'on', false) .' /></td>'.PHP_EOL;
        
		echo '<td>'. __('Allow Editors to access plugin settings', self::ID) .'</td>'.PHP_EOL;
		
		echo '</tr>'.PHP_EOL;
         
        echo '<tr valign="top">'.PHP_EOL;
        
        echo '<td><input type="checkbox" name="'. self::ID .'[debug_mode]" '. checked($debug_mode, 'on', false) .' /></td>'.PHP_EOL;
        
        echo '<td>'. __('Enable debug mode. Debug data will be shown on public pages only for logged in administrator!', self::ID) .'</td>'.PHP_EOL;
                
        echo '</tr>'.PHP_EOL;
        
    	echo '</table>'.PHP_EOL;
    	
   		echo '<p class="submit"><input type="submit" class="button-primary" value="'. __('Save Changes') .'" /></p>'.PHP_EOL;
    
   		echo ''.PHP_EOL;
		
		echo '</form>'.PHP_EOL;
		
		echo '</div><!--wrap-->'.PHP_EOL;
		
	}
	
	/**
	 * Registers settings (needed for options update)
	 * 
	 */
	public static function reg_settings() {
	
		register_setting(self::ID, self::ID);
  	
	}
	
	/**
	 * Outputs plugin deactivation page
	 * 
	 */
	protected static function deactivate_page() {
		
		$res = '<div class="wrap">'.PHP_EOL;
		
		$res .= '<h2>'. self::NAME .' &raquo; '. __('Deactivation', self::ID) .'</h2>'.PHP_EOL;
		
		$res .= '<br/><form method="post" action="'. self::action_url() .'" >'.PHP_EOL;
			
		$res .= '<input type="hidden" name="deactivate" value="deactivate"/>'.PHP_EOL;
					
		$res .= '<input type="hidden" name="wp_nonce" value="'. wp_create_nonce(self::ID.'_post') .'"/>'.PHP_EOL;
						
		$res .= '<input type="submit" value="'. __('Deactivate plugin', self::ID) .'" class="button-secondary" /><br/><br/>';
			
		$res .= '</form>'.PHP_EOL;
			
			
		$res .= '<form method="post" action="'. self::action_url() .'" id="deactivate_and_clean">'.PHP_EOL;
			
		$res .= '<input type="hidden" name="deactivate" value="deactivate_and_clean"/>'.PHP_EOL;
					
		$res .= '<input type="hidden" name="wp_nonce" value="'. wp_create_nonce(self::ID.'_post') .'"/>'.PHP_EOL;
			
		$res .= '<input type="submit" value="'. __('Deactivate plugin and delete all settings from database', self::ID) .'" class="button-secondary" />';
			
		$res .= '</form>'.PHP_EOL;
		
		$res .= '<script type="text/javascript">jQuery("#deactivate_and_clean").submit(function(){return confirm("'. __('Deactivate plugin and delete all settings from database', self::ID) .'?")})</script>'.PHP_EOL;

		$res .= '</div><!--wrap-->'.PHP_EOL;
		
		echo $res;
		
	}
	
	/**
	 * Deactivate actions
	 * 
	 */
	public static function deactivation() {
		
		global $wpdb;
		
		require_once(ABSPATH . 'wp-admin/includes/plugin.php');

		$redirect_url = get_option('siteurl').'/wp-admin/plugins.php?deactivate=true';
		
		if ($_REQUEST['deactivate'] == 'deactivate') { // simple deactivation

			deactivate_plugins(plugin_basename(__FILE__));
			
			wp_redirect($redirect_url);
							
		} elseif ($_REQUEST['deactivate'] == 'deactivate_and_clean') { // advanced deactivation (delete tables and settings)
			
			// deactivate plugin
			
			deactivate_plugins(plugin_basename(__FILE__)); 
			
			// delete tables and options from db 
			
			delete_option(self::ID);
			
			delete_option(self::POST_TABLE_OPTION);
			
			delete_option(self::META_TABLE_OPTION);
									
			$wpdb->query('DELETE FROM '. $wpdb->usermeta ." WHERE meta_key = '". self::safe_plugin_id(self::ID) ."'"); // delete all plugin entries in usermeta table
			
			$wpdb->query('DROP TABLE IF EXISTS '.self::posts_table()); // delete posts table
			
			$wpdb->query('DROP TABLE IF EXISTS '.self::meta_table()); // delete meta table
			
			wp_redirect($redirect_url);				
			
		}
									
	}
	
	/**
	 * Modifies the query used to display posts on the front-end
	 * 
	 * @param string $the_wp_query unmodified wp sql query
	 * 
	 * @return string 
	 * 
	 */
	public static function sort($the_wp_query) {
		
		global $wpdb, $wp_query;
		
		static $query_number = 0;
		
		$query_number++;
		
		$aspo = $_REQUEST['aspo']; // aspo=vanilla : don't use astickypostorderer for this listing 
		
		$q2w3_post_order = $_REQUEST['q2w3-post-order']; // the same thing as a string above
		
		if ($aspo == 'vanilla' || $wp_query->query_vars['aspo'] == 'vanilla' || $q2w3_post_order == 'disable' || $wp_query->query_vars['q2w3-post-order'] == 'disable') {

			return $the_wp_query;
						
		}

		if (is_category() || is_tag() || is_tax()) { // Taxonomy
			
			$taxonomy = $wp_query->tax_query->queries[0]['taxonomy'];
			
			$term_id = $wp_query->get_queried_object_id();
			
			//$term_slug = $wp_query->tax_query->queries[0]['terms'][0];
			
			//$term_ids = array();
			
			//$term_ids = get_term_children($term_id, $taxonomy);
			
			//$term_ids[] = $term_id;
			
			$sorted_posts = self::get_sorted_posts($term_id, $taxonomy);
			
			$sorted_ids = array();
			
			if (is_array($sorted_posts)) {
			
				foreach ($sorted_posts as $sorted_post) {
					
					$sorted_ids[] = $sorted_post['ID'];
				
				}
				
			}
			
			if (!empty($sorted_ids) && count($sorted_ids) > 1) {
			
				$inject_sql = ', '.$wpdb->posts.'.ID IN ('.implode(',', $sorted_ids).') AS ordered, (SELECT post_rank FROM '.self::posts_table()." WHERE post_id = {$wpdb->posts}.ID AND taxonomy = '$taxonomy' AND term_id = $term_id) as order_num ";	
			
				$inject_sql_order = 'ordered DESC, order_num ASC, ';

			} elseif (!empty($sorted_ids) && count($sorted_ids) == 1) {
				
				$inject_sql = ', '.$wpdb->posts.'.ID = '.$sorted_ids[0].' AS ordered, (SELECT post_rank FROM '.self::posts_table()." WHERE post_id = {$wpdb->posts}.ID AND taxonomy = '$taxonomy' AND term_id = $term_id) as order_num ";	
			
				$inject_sql_order = 'ordered DESC, order_num ASC, ';
				
			} else {
				
				$inject_sql = NULL;
				
				$inject_sql_order = NULL;
				
			}
			
			$left_join = 'LEFT JOIN '.self::posts_table().' ON ('.$wpdb->posts.'.ID = '.self::posts_table().'.post_id AND '.self::posts_table().".taxonomy = '$taxonomy' ) ";
			
		} elseif (is_home() || $wp_query->is_posts_page) { // Posts Archive page
			
			$post_type = 'post';
					
		} elseif (is_post_type_archive()) { // Post Type Archive
			
			$post_type = $wp_query->query_vars['post_type'];
			
		}
		
		if ($post_type) {
			
			$sorted_posts = self::get_sorted_posts('', '', $post_type);
			
			$sorted_ids = array();
			
			foreach ($sorted_posts as $sorted_post) {
				
				$sorted_ids[] = $sorted_post['ID'];
				
			}
			
			if (!empty($sorted_ids) && count($sorted_ids) > 1) {
			
				$inject_sql = ', '.$wpdb->posts.'.ID IN ('.implode(',', $sorted_ids).') AS ordered, (SELECT post_rank FROM '.self::posts_table()." WHERE post_id = {$wpdb->posts}.ID AND post_type = '$post_type') as order_num ";

				$inject_sql_order = 'ordered DESC, order_num ASC, ';

			} elseif (!empty($sorted_ids) && count($sorted_ids) == 1) {
				
				$inject_sql = ', '.$wpdb->posts.'.ID = '.$sorted_ids[0].' AS ordered, (SELECT post_rank FROM '.self::posts_table()." WHERE post_id = {$wpdb->posts}.ID AND post_type = '$post_type') as order_num ";	
			
				$inject_sql_order = 'ordered DESC, order_num ASC, ';
				
			} else {
				
				$inject_sql = NULL;
				
				$inject_sql_order = NULL;
				
			}
			
			$left_join = 'LEFT JOIN '.self::posts_table().' ON ('.$wpdb->posts.'.ID = '.self::posts_table().'.post_id AND '.self::posts_table().".post_type = '$post_type') ";
						
		}
		
		if ($left_join) {
		
			$c_query = $the_wp_query;
		
			$c_into = strpos($c_query, 'FROM '.$wpdb->posts);
		
			$c_query = substr_replace($c_query, $inject_sql, $c_into, 0);
		
			$c_into = strpos($c_query, 'WHERE 1=1');
			
			$c_query = substr_replace($c_query, $left_join,  $c_into, 0);
		
			$c_into = strpos($c_query, $wpdb->posts.'.post_date DESC');
		
			$c_query = substr_replace($c_query, $inject_sql_order, $c_into, 0);
		
			$result_query = $c_query;
		
		} else {
			
			$result_query = $the_wp_query;
			
		}
		
		// Debug info
		
		$options = get_option(self::ID);
		
		if ( $options['debug_mode'] && current_user_can('activate_plugins') ) {
			
			echo "<p>Query Number: $query_number</p>";
			
			echo '<p>WP Query Object:</p>';
			
			echo '<pre>';
			
			var_dump($wp_query);
			
			echo '</pre>';
			
			
			echo '<p>Original SQL Query:</p>';
			
			echo '<pre>';
			
			var_dump($the_wp_query);
			
			echo '</pre>';
			
			
			echo '<p>Result SQL Query:</p>';
			
			echo '<pre>';
			
			var_dump($result_query);
			
			echo '</pre>';
					
		}
		
		return $result_query;
		
	}
	
	/**
	 * Adds CSS classes to post html. Linked to 'post_class' hook.
	 * 
	 * @param array $classes
	 * 
	 * @return array
	 * 
	 */
	public static function post_class($classes) {
		
		global $post;
		
		if ($post->order_num) {
			
			$classes[] = 'q2w3-post-order';
			
			$classes[] = 'q2w3-post-order-'.$post->order_num;
			
		}
		
		return $classes;
		
	}

	/**
	 * User just deleted post: removing it from ordered posts as well
	 * 
	 * @param mixed $deleted_post_id Deleted Post ID
	 * 
	 */
	public static function delete_post_sync($deleted_post_id){
		
		global $wpdb;
		
		$sql = "DELETE FROM ". self::posts_table(). " WHERE post_id = $deleted_post_id";
		
		$sql = $wpdb->prepare($sql);
		
		return $wpdb->query($sql);
		
	}
	
	/**
	 * Removes term from meta table, when term is deleted
	 * 
	 * @param $term_id Deleted Term ID
	 * 
	 */
	public static function delete_term_sync($term_id) {
		
		/// Not need it yet
		
	}
	
	/**
	 * Outputs terms table rows
	 * 
	 * @param string $tax_name - Taxonomy Name - actually this is taxonomy slug
	 * @param mixed $parent_term - Parent Term ID - used for walking hierarchical taxonomies
	 * @param int $indent - Indent level - used for walking hierarchical taxonomies
	 * @param bool $hierarchical - If is true, search for child terms will be initiated
	 * 
	 */
	protected static function list_terms($tax_name, $parent_term, $indent = 0, $hierarchical = true) {
		
		static $tr_count = 1;
		
		$terms = get_terms($tax_name, array( 'orderby' => 'id', 'pad_counts' => true )); // if isset parent -> pad_counts = false
		
		if (!$terms) return true;
		
		foreach ($terms as $term) {
			
			if ($term->parent == $parent_term) {
			
				$term_id = $term->term_id;
				
				if ($tr_count%2 > 0) $alter = 'class="alternate"'; else $alter = '';
				
				$res = "<tr $alter><td style='width: 70px; height: 35px'>$term_id</td>";
				
				$pad = '';
				
				for ($i=0; $i < $indent; $i++) {
					
					$pad .= ' &#8250;&nbsp;&nbsp;';
				
				}
						
				$res .= '<td>'. $pad .'<a href="?page='. self::ID .'&amp;tax_name='. $tax_name .'&amp;term_id='.$term_id.'">'.$term->name.'</td>';
				
				$res .= '<td style="text-align: center">'.self::sorted_num($term_id, $tax_name).' / '. $term->count .'</td>';
				
				$res .=	'</tr>'.PHP_EOL;
				
				echo $res;
				
				$tr_count++;
				
				if ($hierarchical) {
					
					self::list_terms($tax_name, $term_id, $indent+1, $hierarchical);
				
				}
			
			}

		}
		
	}
	
	/**
	 * Counts sorted post for the term
	 * 
	 * @param mixed $term_id - Term ID
	 * @param string $tax_name - Taxonomy name (slug)
	 * 
	 */
	protected static function sorted_num($term_id, $tax_name) {
		
		global $wpdb;
		
		$sql = 'SELECT count(*) FROM '. self::posts_table() .' WHERE term_id = '. $term_id . " AND (taxonomy = '$tax_name' || taxonomy IS NULL)"; // || taxonomy IS NULL - for compatibility with old data
		
		$sql = $wpdb->prepare($sql);
		
		return $wpdb->get_var($sql);
		
	}
	
	/**
	 * Retrievs sorted posts for term or post type
	 * 
	 * @param mixed $term_id - Term ID
	 * @param string $tax_name - Taxonomy name (slug)
	 * @param string $post_type - Post Type name
	 * 
	 */
	public static function get_sorted_posts($term_id, $tax_name, $post_type = false) {
		
		global $wpdb;
		
		if ($term_id) {
			
			if (is_int($term_id)) $term_condition = 'AND q2w3_po.term_id = '.$term_id;
			
				elseif (is_array($term_id)) $term_condition = 'AND q2w3_po.term_id IN ('. implode(',', $term_id) .')';
		
			$sql = 'SELECT q2w3_po.*, posts.ID, posts.post_title '.
		 	'FROM '. self::posts_table() .' as q2w3_po, '. $wpdb->posts .' as posts '.
		 	"WHERE (q2w3_po.taxonomy = '$tax_name' || q2w3_po.taxonomy IS NULL) $term_condition AND q2w3_po.post_id = posts.ID ".
			"AND posts.post_status = 'publish' ".
		 	'ORDER BY q2w3_po.post_rank ASC';
	   
		} elseif ($post_type) {
			
			$sql = 'SELECT q2w3_po.id, posts.ID, posts.post_title, q2w3_po.post_rank '.
		 	'FROM '. self::posts_table() .' as q2w3_po, '. $wpdb->posts .' as posts '.
		 	"WHERE q2w3_po.post_type = '$post_type' AND q2w3_po.post_id = posts.ID ".
		 	"AND posts.post_status = 'publish' ".
		 	'ORDER BY q2w3_po.post_rank ASC';
			
		}
		
		$sql = $wpdb->prepare($sql);
		
		return $wpdb->get_results($sql, ARRAY_A);

	}
	
	/**
	 * Retrievs unsorted posts for term or post type. 
	 * 
	 * @param mixed $term_id - Term ID
	 * @param string $tax_name - Taxonomy name (slug)
	 * @param string $post_type - Post Type name
	 * @param array $exclude_ids - Array of ids of already sorted post.
	 * @param bool $count_mode - If is true, counts total number of unsorted posts (used for table paging) 
	 * 
	 * @return Array
	 * 
	 */
	protected static function get_unsorted_posts($term_id, $tax_name, $post_type = false, $exclude_ids = false, $count_mode = false) { // 
		
		global $wpdb;
		
		$settings = self::user_settings_load();
		
		$exclude = '';
		
		if ($exclude_ids) {
			
			if (is_array($exclude_ids) && count($exclude_ids) > 1) $exclude = implode(',', $exclude_ids); else $exclude = $exclude_ids[0];
			
			$exclude = "AND ID NOT IN ($exclude)";
			
		}
		
		$fields = 'ID, post_title';

		$search = NULL;
		
		$search_string = NULL;
		
		$limit = NULL;
		
		if (isset($_GET[q2w3_post_order_table_search::VAR_NAME]) && !empty($_GET[q2w3_post_order_table_search::VAR_NAME])) {
			
			$search = "AND post_title LIKE '%s'"; // using %s because direct input will fail wpdb->prepare
			
			$search_string = '%'. urldecode($_GET[q2w3_post_order_table_search::VAR_NAME]) .'%';
			
		}
		
		if ($count_mode) {
			
			$fields = 'count(*) as total';
			
		} else {
		
			if (!$settings['rows_per_page']) $rpp = 20; else $rpp = $settings['rows_per_page'];
			
			$limit_start = q2w3_post_order_table_paging::cur_page()*$rpp - $rpp;
			
			$limit = 'LIMIT '. $limit_start .','. $rpp;
			
		}
		
		if ($term_id && $tax_name) {
			
			$child_ids = get_term_children($term_id, $tax_name);
		
			$child_ids[] = $term_id;
		
			if (count($child_ids) > 1) $child_ids = implode(',', $child_ids); else $child_ids = $child_ids[0];
			
			$sql = "SELECT $fields ".
			"FROM $wpdb->posts ".
			"WHERE ID IN (SELECT tr.object_id FROM $wpdb->term_relationships as tr, $wpdb->term_taxonomy as tt WHERE tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.taxonomy = '$tax_name' AND tt.term_id IN ($child_ids)) ".
			"AND post_status = 'publish' $exclude $search ";
			
		} elseif ($post_type) {
			
			$sql = "SELECT $fields ".
			"FROM $wpdb->posts ".
			"WHERE post_type = '$post_type' AND post_status = 'publish' $exclude $search ";
			
		}
		 
		$sql .= 'ORDER BY post_date DESC '.$limit; 
	 	
		$sql = $wpdb->prepare($sql, $search_string); // second argument is to avoid glitch with % sign
		
		return $wpdb->get_results($sql, ARRAY_A);

	}
	
	/**
	 * Outputs posts table rows
	 * 
	 * @param array $posts - Array of posts from get_sorted or get_unsorted functions
	 * @param string $post_type - Post Type name
	 * @param string $tax_name - Taxonomy name (slug)
	 * 
	 */
	protected static function list_posts($posts, $post_type = '', $tax_name = '') {
				
		if (!$posts) {
			
			echo '<tr><td colspan="4">'. __('No records found', self::ID) .'</td></tr>'.PHP_EOL;
			
			return false;
			
		}
		
		$settings = self::user_settings_load();
		
		if (!$settings['rows_per_page']) $rpp = 20; else $rpp = $settings['rows_per_page'];
		
		$tr_count = q2w3_post_order_table_paging::cur_page()*$rpp - $rpp + 1;
		
		foreach ($posts as $post) {
		
			if ($tr_count%2 > 0) $alter = "class='alternate'"; else $alter = '';
			
			echo "<tr $alter>".PHP_EOL;
			
			if (isset($post['post_rank']) && $post['post_rank']) $pos = $post['post_rank']; else $pos = $tr_count;
		
			echo '<td style="text-align: center">'. $pos .'</td>'.PHP_EOL;
		
			echo '<td style="text-align: center">'. $post['ID'] .'</td>'.PHP_EOL;
		
			echo '<td><a href="post.php?post='. $post['ID'] .'&action=edit" target="_blank">'. $post['post_title'] .'</a></td>'.PHP_EOL;
		
			echo '<td style="text-align: center">';
			
			echo '<input name="posts['. $post['ID'] .'][new_pos]" type="text" size="6" maxlength="6">';
			
			if ($post_type) echo '<input type="hidden" name="posts['. $post['ID'] .'][post_type]" value="'. $post_type .'" />';
			
				elseif ($tax_name) echo '<input type="hidden" name="posts['. $post['ID'] .'][tax_name]" value="'. $tax_name .'" />';
			
			if (isset($post['id']) && $post['id']) { // We are in sorted posts table
				
				echo '<input type="hidden" name="posts['. $post['ID'] .'][id]" value="'. $post['id'] .'" />';
			
				echo '<input type="hidden" name="posts['. $post['ID'] .'][pos]" value="'. $post['post_rank'] .'" />';
				
			}
			
			echo '</td>'.PHP_EOL;
		
			echo '</tr>'.PHP_EOL;
			
			$tr_count++;
			
			$post_ids[] = $post['ID'];
					
		}
		
		return $post_ids;
		
	}
	
	/**
	 * Retrievs meta options form meta table. Not used after deprecation of Meta-Stickiness options.
	 * 
	 * @param string $tax_name - Taxonomy name (slug)
	 * @param mixed $term_id - Term ID
	 * 
	 * @return Array of meta options
	 * 
	 */
	/*public static function get_meta($tax_name = false, $term_id = false) {
		
		if (!$tax_name) return false;
				
		global $wpdb;
	 	
		$sql = 'SELECT term_id, term_rank, limit_to FROM '. self::meta_table() ." WHERE term_type = '$tax_name'";
		
		if ($term_id && is_int($term_id)) $sql .= 'AND term_id = '.$term_id;
		
		elseif ($term_id && is_array($term_id)) $sql .= 'AND term_id IN ('. implode(',', $term_id) .')';
		
		$sql = $wpdb->prepare($sql);
		
		$terms = $wpdb->get_results($sql, ARRAY_A);
		
		$result = array();
		
		foreach ($terms as $term) {
			
			$result[$term['term_id']]['val'] = $term['term_rank'];
			
			$result[$term['term_id']]['limit'] = $term['limit_to'];
		}
		
		return $result;
		
	}*/
	
	/**
	 * Change positions of sorted posts when new post added/deleted or post changes position
	 * 
	 * @param string $term_id - Term ID - if sorting for post type term_id = 0
	 * @param array $context - array('tax_name' => Taxonomy name) or array('post_type' => Post Type name)
	 * @param string $direction - Direction 'up' or 'down'
	 * @param mixed $pos_start - Start from post_rank
	 * @param mixed $pos_end - Finish on post_rank
	 * 
	 * @return Array of meta options
	 * 
	 */
	public static function change_positions($term_id, $context, $direction, $pos_start, $pos_end = false) {
		
		global $wpdb;
		
		if ($direction == 'up') $sign = '+';
		
		if ($direction == 'down') $sign = '-';
		
		$start = 'AND post_rank >= '. $pos_start;
		
		if ($pos_end && $direction == 'up') {
			
			$end = 'AND post_rank < '.$pos_end;
		
		}
		
		if ($pos_end && $direction == 'down') {
			
			$start = 'AND post_rank > '. $pos_start;
			
			$end = 'AND post_rank <= '.$pos_end;
			
		}
		
		if ($context['tax_name']) $cont = "AND (taxonomy = '". $context['tax_name'] ."' || taxonomy IS NULL)";
		
		if ($context['post_type']) $cont = "AND post_type = '". $context['post_type'] ."'";
		
		$sql = 'UPDATE '. self::posts_table() .' SET '.
		"post_rank = post_rank $sign 1 ".
		"WHERE term_id = $term_id $start $end $cont";
		
		$sql = $wpdb->prepare($sql);
		
		return $wpdb->query($sql);
		
	}
	
	/**
	 * Seachs for $old_value in $_SERVER['HTTP_REFERER'] and replaces it with $new_value
	 * 
	 * @param string $old_value
	 * @param string $new_value
	 */
	public static function change_referer($old_value, $new_value = '') {

		$_SERVER["HTTP_REFERER"] = str_replace($old_value, $new_value, $_SERVER["HTTP_REFERER"]);

	}
	
	public static function safe_plugin_id($plugin_id) {
		
		return preg_replace('/\d/', '', str_replace('-', '_', $plugin_id));
		
	}
		
} // end of q2w3_post_order class

?>