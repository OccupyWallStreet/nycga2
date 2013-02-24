<?php
/*
Plugin Name: Site Categories
Plugin URI: 
Description: 
Author: Paul Menard (Incsub)
Version: 1.0.7.4
Author URI: http://premium.wpmudev.org/
WDP ID: 679160
Text Domain: site-categories
Domain Path: languages

Copyright 2012 Incsub (http://incsub.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
///////////////////////////////////////////////////////////////////////////

if (!defined('SITE_CATEGORIES_I18N_DOMAIN'))
	define('SITE_CATEGORIES_I18N_DOMAIN', 'site-categories');

if (!defined('SITE_CATEGORIES_TAXONOMY'))
	define('SITE_CATEGORIES_TAXONOMY', 'bcat');

require_once( dirname(__FILE__) . '/lib/widgets/class_site_categories_widget_categories.php');
require_once( dirname(__FILE__) . '/lib/widgets/class_site_categories_widget_category_sites.php');
require_once( dirname(__FILE__) . '/lib/widgets/class_site_categories_widget_cloud.php');

require_once( dirname(__FILE__) . '/lib/display_templates/display_list_category_sites.php');
require_once( dirname(__FILE__) . '/lib/display_templates/display_list_categories.php');
require_once( dirname(__FILE__) . '/lib/display_templates/display_grid_categories.php');
require_once( dirname(__FILE__) . '/lib/display_templates/display_accordion_categories.php');

include_once( dirname(__FILE__) . '/lib/dash-notices/wpmudev-dash-notification.php');


class SiteCategories {
		
	private $_pagehooks = array();	// A list of our various nav items. Used when hooking into the page load actions.
	private $_messages 	= array();	// Message set during the form processing steps for add, edit, udate, delete, restore actions
	private $_settings	= array();	// These are global dynamic settings NOT stores as part of the config options
	
	private $_admin_header_error;	// Set during processing will contain processing errors to display back to the user
	private $bcat_signup_meta = array();	// Used to store the signup meta information related to Site Categories during the processing. 

	/**
	 * The old-style PHP Class constructor. Used when an instance of this class 
 	 * is needed. If used (PHP4) this function calls the PHP5 version of the constructor.
	 *
	 * @since 1.0.0
	 * @param none
	 * @return self
	 */
    function SiteCategories() {
        __construct();
    }


	/**
	 * The PHP5 Class constructor. Used when an instance of this class is needed.
	 * Sets up the initial object environment and hooks into the various WordPress 
	 * actions and filters.
	 *
	 * @since 1.0.0
	 * @uses $this->_settings array of our settings
	 * @uses $this->_messages array of admin header message texts.
	 * @param none
	 * @return self
	 */
	function __construct() {
		
		$this->_settings['VERSION'] 				= '1.0.7.4';
		$this->_settings['MENU_URL'] 				= 'options-general.php?page=site_categories';
		$this->_settings['PLUGIN_URL']				= WP_CONTENT_URL . "/plugins/". basename( dirname(__FILE__) );
		$this->_settings['PLUGIN_BASE_DIR']			= dirname(__FILE__);
		$this->_settings['admin_menu_label']		= __( "Site Categories", SITE_CATEGORIES_I18N_DOMAIN ); 
		
		$this->_settings['options_key']				= "wpmudev-site-categories"; 
		
		$this->_admin_header_error 					= "";		
		
		add_action('admin_notices', array(&$this, 'admin_notices_proc') );

		/* Setup the tetdomain for i18n language handling see http://codex.wordpress.org/Function_Reference/load_plugin_textdomain */
        load_plugin_textdomain( SITE_CATEGORIES_I18N_DOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		/* Standard activation hook for all WordPress plugins see http://codex.wordpress.org/Function_Reference/register_activation_hook */
        register_activation_hook( __FILE__, 	array( &$this, 'plugin_activation_proc' ) );

		add_action( 'init', 						array(&$this, 'register_taxonomy_proc') );		
		add_action( 'init', 						array(&$this, 'enqueue_scripts_proc'));
		add_action( 'admin_menu', 					array(&$this, 'admin_menu_proc') );	
		add_action( 'widgets_init', 				array(&$this, 'widgets_init_proc') );

		// Add/Modify the column for the Taxonomy terms list page 
		add_filter( "manage_edit-bcat_columns", 	array(&$this, 'bcat_taxonomy_column_headers') );	
		add_filter( 'manage_bcat_custom_column', 	array(&$this, 'bcat_taxonomy_column'), 10, 3 );

		add_filter( 'wpmu_blogs_columns', 			array(&$this, 'bcat_sites_column_headers') );	
		add_action( 'manage_sites_custom_column',	array(&$this, 'bcat_sites_column_row'), 10, 2 );


		// Add/Edit Taxonomy term form fields. 
		add_action( 'bcat_edit_form_fields', 		array(&$this, 'bcat_taxonomy_term_edit'), 99, 2 );		
		add_action( "edit_bcat", 					array(&$this, 'bcat_taxonomy_term_save'), 99, 2 );
		
		// Adds our Site Categories to the Site signup form. 
		add_action( 'signup_blogform', 				array($this, 'bcat_signup_blogform') );
		add_action( 'wpmu_new_blog', 				array($this, 'wpmu_new_blog_proc'), 99, 6 );
		add_filter( 'wpmu_validate_blog_signup', 	array($this, 'bcat_wpmu_validate_blog_signup'));
		add_filter( 'add_signup_meta', 				array($this, 'bcat_add_signup_meta'));

		// Output for the Title and Content of the Site Category listing page
		add_filter( 'the_title', 					array($this, 'process_categories_title'), 99, 2 );
		add_filter( 'the_content', 					array($this, 'process_categories_body'), 99 );
				
		// Rewrite rules logic
		add_filter( 'rewrite_rules_array', 			array($this, 'insert_rewrite_rules') );
		add_filter( 'query_vars', 					array($this, 'insert_query_vars') );
		
		add_action( 'delete_blog', 					array($this, 'blog_change_status_count') );
		add_action( 'make_spam_blog', 				array($this, 'blog_change_status_count') );
		add_action( 'make_ham_blog', 				array($this, 'blog_change_status_count') );
		add_action( 'mature_blog', 					array($this, 'blog_change_status_count') );
		add_action( 'unmature_blog', 				array($this, 'blog_change_status_count') );		
		add_action( 'archive_blog', 				array($this, 'blog_change_status_count') );
		add_action( 'unarchive_blog', 				array($this, 'blog_change_status_count') );		
		add_action( 'activate_blog', 				array($this, 'blog_change_status_count') );
		add_action( 'deactivate_blog', 				array($this, 'blog_change_status_count') );		
	}	
	

	/**
	 * Setup scripts and stylsheets
	 *
	 * @since 1.0.0
	 * @param none
	 * @return none
	 */
	function enqueue_scripts_proc()
	{
		if (is_admin()) {

			wp_register_style( 'site-categories-admin-styles', plugins_url('css/site-categories-admin-styles.css', __FILE__) );
			wp_enqueue_style( 'site-categories-admin-styles' );

			if ((is_multisite()) && (is_main_site()) && (is_super_admin())) {
				if ((isset($_GET['action'])) && ($_GET['action'] == "edit")
				 && (isset($_GET['taxonomy'])) && ($_GET['taxonomy'] == "bcat")
				 && (isset($_GET['tag_ID']))) {
			
					add_thickbox();

					wp_register_script('site-categories-admin', WP_PLUGIN_URL .'/'. basename(dirname(__FILE__)) .'/js/jquery.site-categories-admin.js', 
						array('jquery'), $this->_settings['VERSION']  );
					wp_enqueue_script('site-categories-admin');
					
					
				} else if ((isset($_GET['page'])) && ($_GET['page'] == "bcat_settings")) {
					add_thickbox();

					wp_register_script('site-categories-admin', WP_PLUGIN_URL .'/'. basename(dirname(__FILE__)) .'/js/jquery.site-categories-admin.js', 
						array('jquery'), $this->_settings['VERSION']  );
					wp_enqueue_script('site-categories-admin');
				}
			}
		} else {
			$this->load_config();
			if (isset($this->opts['categories']['show_style']) &&  ($this->opts['categories']['show_style'] == "accordion")) {
				wp_enqueue_script('jquery');
				wp_enqueue_script('jquery-ui-accordion');

				wp_register_script('site-categories', WP_PLUGIN_URL .'/'. basename(dirname(__FILE__)) .'/js/jquery.site-categories.js', 
					array('jquery', 'jquery-ui-accordion'), $this->_settings['VERSION']  );
				wp_enqueue_script('site-categories');
			}
			
			wp_register_style( 'site-categories-styles', plugins_url('css/site-categories-styles.css', __FILE__) );
			wp_enqueue_style( 'site-categories-styles' );
			
		}
	}

	/**
	 * Initialize our widgets
	 *
	 * @since 1.0.0
	 * @param none
	 * @return none
	 */	
	function widgets_init_proc() {
		register_widget('Bcat_WidgetCategories');
		register_widget('Bcat_WidgetCategorySites');		
		register_widget('Bcat_WidgetCloud');		
	}
		
	function bcat_taxonomy_column_headers($columns) {
		if (isset($columns['posts'])) {
			unset($columns['posts']);
		}
		
		$columns_tmp = array();
		if (isset($columns['cb'])) {
			$columns_tmp['cb'] = $columns['cb'];
			unset($columns['cb']);
		}

		$columns_tmp['icon'] = __('Icon', SITE_CATEGORIES_I18N_DOMAIN);
		foreach($columns as $col_key => $col_label) {
			$columns_tmp[$col_key] = $col_label;
		}
		$columns_tmp['sites'] = __('Sites', SITE_CATEGORIES_I18N_DOMAIN); 

		return $columns_tmp;
	}

	/**
	 * On the Primary site under the Site Categories section will be a Taxonomy admin panel. This function adds a column
	 * to the standard WordPress taxonomy table. 
	 *
	 * @since 1.0.0
	 * @param none
	 * @return none
	 */
	function bcat_taxonomy_column($nothing, $column_name, $term_id) {
		switch($column_name) {
			
			case 'sites':
				$bcat_term = get_term($term_id, SITE_CATEGORIES_TAXONOMY);
				if ( !is_wp_error($bcat_term)) {
					
					if ($bcat_term->count == 0) {
						echo $bcat_term->count;
					} else {
						if ((isset($this->opts['landing_page_slug'])) && (strlen($this->opts['landing_page_slug']))) {
							if ((isset($this->opts['landing_page_rewrite'])) && ($this->opts['landing_page_rewrite'] == true)) {
								$bcat_url = trailingslashit($this->opts['landing_page_slug']) . $bcat_term->slug;
							} else {
								$bcat_url = $this->opts['landing_page_slug'] .'&amp;category_name=' . $bcat_term->slug;
							}

							if (strlen($bcat_url)) {
								?><a target="_blank" href="<?php echo $bcat_url; ?>"><?php echo $bcat_term->count; ?></a><?php
							} else {
								echo $bcat_term->count;
							}						

						} else {
							echo $bcat_term->count;
						}
					}
				}
				break;

			case 'icon':
				$bcat_image_src = '';

				$this->load_config();
				if (isset($this->opts['icons_category'][$term_id])) {
					$bcat_image_id = $this->opts['icons_category'][$term_id];
					if ($bcat_image_id)
					{
						$image_src 	= wp_get_attachment_image_src($bcat_image_id, 'thumbnail', true);
						if ($image_src) {
							$bcat_image_src = $image_src[0];
						}
					}
				}
				
				if (!strlen($bcat_image_src)) {
					$bcat_image_src = $this->get_default_category_icon_url();
				}

				?><img src="<?php echo $bcat_image_src; ?>" alt="" width="50" /><?php

				break;
			
			default:
				break;
		}
	}
	
	function bcat_sites_column_headers($columns) {
		if (!isset($columns['site-categories']))
			$columns['site-categories'] = __('Site Categories', SITE_CATEGORIES_I18N_DOMAIN); 
		return $columns;
	}
	function bcat_sites_column_row($column_name, $blog_id) {
		switch($column_name) {
			case 'site-categories':
				$terms = wp_get_object_terms( $blog_id, SITE_CATEGORIES_TAXONOMY);
				if ((!$terms) || (!is_array($terms))) 
					$terms = array();
					
				$this->load_config();
				//echo "this->opts<pre>"; print_r($this->opts); echo "</pre>";
				$column_output = '';	
				foreach($terms as $bcat_term) {
					
					if ((isset($this->opts['landing_page_slug'])) && (strlen($this->opts['landing_page_slug']))) {
						if ((isset($this->opts['landing_page_rewrite'])) && ($this->opts['landing_page_rewrite'] == true)) {
							$bcat_url = trailingslashit($this->opts['landing_page_slug']) . $bcat_term->slug;
						} else {
							$bcat_url = $this->opts['landing_page_slug'] .'&amp;category_name=' . $bcat_term->slug;
						}

						if (strlen($bcat_url)) {
							if (strlen($column_output)) $column_output .= ", ";
							$column_output .= '<a target="_blank" href="'. $bcat_url .'">'. $bcat_term->name .'</a>';
						} else {
							if (strlen($column_output)) $column_output .= ", ";
							$column_output .= $bcat_term->count;
						}						

					} else {
						if (strlen($column_output)) $column_output .= ", ";
						$column_output .= $bcat_term->name;
					}
				}
				if (strlen($column_output))
					echo $column_output;
				
				
				break;
			
			default:
				break;
		}
	}
	
	/**
	 * Gets the URL for the default icon shipped with the plugin
	 *
	 * @since 1.0.0
	 * @param none
	 * @return none
	 */
	
	function get_default_category_icon_url() {
		return WP_PLUGIN_URL .'/'. basename(dirname(__FILE__)) .'/img/default.jpg';
	}

	/**
	 * Gets the path for the default icon shipped with the plugin 
	 *
	 * @since 1.0.0
	 * @param none
	 * @return none
	 */

	function get_default_category_icon_path() {
		return WP_PLUGIN_DIR .'/'. basename(dirname(__FILE__)) .'/img/default.jpg';
	}
	

	/**
	 * Reads the taxonomy and returns the URL to the taxonomy term icon.
	 *
	 * @since 1.0.0
	 * @param none
	 * @return none
	 */
	function get_category_term_icon_src($term_id, $size) {	

		if ((isset($this->opts['icons_category'][$term_id])) && (intval($this->opts['icons_category'][$term_id]))) {
			$icon_image_id = $this->opts['icons_category'][$term_id];
			$icon_image_src = wp_get_attachment_image_src($icon_image_id, array($size, $size));
			if ($icon_image_src) {
				return $icon_image_src[0];
			}

		} else if ((isset($this->opts['categories']['default_icon_id'])) && (intval($this->opts['categories']['default_icon_id']))) {
			$default_icon_id = $this->opts['categories']['default_icon_id'];
			$icon_image_src = wp_get_attachment_image_src($default_icon_id, array($size, $size), true);
			if (( !is_wp_error($icon_image_src)) && ($icon_image_src !== false)) {
				if (($icon_image_src) && (isset($icon_image_src[0])) && (strlen($icon_image_src[0]))) {
					return $icon_image_src[0];
				} 
			} 
			
		} else {
			$icon_image_path = $this->get_default_category_icon_path();
			$icon_image_src = image_make_intermediate_size($icon_image_path, $size, $size, true);
			if (( !is_wp_error($icon_image_src)) && ($icon_image_src !== false)) {
				if (($icon_image_src) && (isset($icon_image_src['file']))) {
					return dirname($this->get_default_category_icon_url()) ."/". $icon_image_src['file'];
				} 
			} 
		}

		if ((isset($icon_image_path)) && (strlen($icon_image_path)))
			return dirname($this->get_default_category_icon_url()) ."/". basename($icon_image_path);
	}
	
	
	/**
	 * Called when the Site Categories term is edited
	 *
	 * @since 1.0.0
	 * @param none
	 * @return none
	 */
	function bcat_taxonomy_term_edit($tag, $taxonomy) {

		// Should not happen. But just in case.
		if ($tag->taxonomy != "bcat")	return;
		
		$this->load_config();

		if (isset($this->opts['icons_category'][$tag->term_id])) {
			$bcat_image_id = $this->opts['icons_category'][$tag->term_id];
		} else {
			$bcat_image_id = 0;
		}
		?>
		<tr>
			<th scope="row" valign="top"><label for="bcat_category_type"><?php _ex('Category Type', 'Category Type', SITE_CATEGORIES_I18N_DOMAIN); ?></label></th>
<?php /* ?>			
			<td>
				<ul>
					<li><input type="radio" name="bcat_category_type" id="bcat_category_type_regular" value="" /> <label 
						for="bcat_category_type_regular"><?php _e('Regular', SITE_CATEGORIES_I18N_DOMAIN); ?></label></li>
					<li><input type="radio" name="bcat_category_type" id="bcat_category_type_network_admin" value="" /> <label 
						for="bcat_category_type_network_admin"><?php _e('Network Admin Assigned', SITE_CATEGORIES_I18N_DOMAIN); ?></label></li>
			</td>
<?php */ ?>
		</tr>	

		<tr>
			<th scope="row" valign="top"><label for="upload_image"><?php _ex('Image', 'Category Image', SITE_CATEGORIES_I18N_DOMAIN); ?></label></th>
			<td>
				<p class="description"><?php _e('The image used for the category icon will be displayed square.', SITE_CATEGORIES_I18N_DOMAIN) ?></p>
				<input type="hidden" id="bcat_image_id" value="<?php echo $bcat_image_id; ?>" name="bcat_image_id" />
				<input id="bcat_image_upload" class="button-secondary" type="button" value="<?php _e('Select Image', SITE_CATEGORIES_I18N_DOMAIN); ?>" <?php
					if ($bcat_image_id) { echo ' style="display: none;" '; }; ?> />
				<input id="bcat_image_remove" class="button-secondary" type="button" value="<?php _e('Remove Image', SITE_CATEGORIES_I18N_DOMAIN); ?>" <?php
					if (!$bcat_image_id) { echo ' style="display: none;" '; }; ?> />
				<br />
				<?php
					$bcat_image_default_src = $this->get_default_category_icon_url();
					if ($bcat_image_id)
					{
						$image_src 	= wp_get_attachment_image_src($bcat_image_id, array(100, 100));
						if (!$image_src) {
							$image_src[0] = "#";							
						}
					} else {
						$image_src[0] = $bcat_image_default_src;
					}
					?>
					<img id="bcat_image_src" src="<?php echo $image_src[0]; ?>" alt="" style="margin-top: 10px; max-width: 300px; max-height: 300px" 
						rel="<?php echo $bcat_image_default_src; ?>"/>
					<?php
				?></p>
			</td>
		</tr>
		<?php
	}
	
	/**
	 * Called when the Site Categories taxonomy term is saved. 
	 *
	 * @since 1.0.0
	 * @param none
	 * @return none
	 */
	function bcat_taxonomy_term_save($term_id, $tt_id) {

		if (isset($_POST['bcat_image_id'])) {

			$bcat_image_id = intval($_POST['bcat_image_id']);

			$this->load_config();

			if (!isset($this->opts['icons_category']))
				$this->opts['icons_category'] = array();

			$this->opts['icons_category'][$term_id] = $bcat_image_id;
			
			$this->save_config();
		}
		
		//echo "term_id=[". $term_id ."]<br />";
		//echo "tt_id=[". $tt_id ."]<br />";
		$this->bcat_taxonomy_terms_count(array($tt_id), get_taxonomy(SITE_CATEGORIES_TAXONOMY));
	}
	
	/**
	 * Reads the Site Taxonomy and returns sites associated with a term
	 *
	 * @since 1.0.0
	 * @param none
	 * @return none
	 */	
	function get_taxonomy_sites($term_id, $include_child = false) {
		
		global $wpdb;

		if ($include_child == true) {

			$args = array();
			$args['taxonomy']	= SITE_CATEGORIES_TAXONOMY;
			$args['child_of']	= $term_id;
			
			$categories = get_categories($args);
			if ($categories) {
				$terms = array();
				foreach($categories as $cat) {
					$terms[] = $cat->term_id;
				}
				$terms = array_unique($terms);
			} else {
				$terms = array($term_id);
			}
		} else {
			$terms = array($term_id);
		}

		$term_sites = get_objects_in_term( $terms, SITE_CATEGORIES_TAXONOMY);
		
		if ($term_sites) {
			$sites = array();
			foreach($term_sites as $site_id) {
				$blog = get_blog_details($site_id);
				if (($blog) && ($blog->public == 1) && ($blog->archived == 0) && ($blog->spam == 0) && ($blog->deleted == 0) && ($blog->mature == 0)) {
					$sites[$site_id] = $blog;
				}
			}
			return $sites;
		} else {
			return array();
		}
	}
	
		
	/**
	 * Called when when our plugin is activated. Sets up the initial settings 
	 * and creates the initial Snapshot instance. 
	 *
	 * @since 1.0.0
	 * @uses none
	 * @see $this->__construct() when the action is setup to reference this function
	 *
	 * @param none
	 * @return none
	 */
	function plugin_activation_proc() {
		
	}

	/**
	 * Loads the config data from the primary site options
	 *
	 * @since 1.0.0
	 * @param none
	 * @return none
	 */	
	function load_config() {
		global $wpdb, $blog_id, $current_site; 
		

		$defaults = array(
			'landing_page_id'			=>	0,
			'landing_page_slug'			=>	'',

			'sites'										=>	array(
				'per_page'								=>	5,
				'icon_show' 							=> 	1,
				'icon_size'								=>	32,
				'orderby' 								=> 	'name',
				'order'									=>	'ASC',
				'show_style'							=>	'ul',
				'show_description'						=>	0,
				'default_category' 						=> 	0,
				'category_limit'						=>	10,
				'category_excludes'						=>	'',
				'signup_category_parent_selectable'		=> 	1,
				'signup_show'							=>	1,
				'signup_category_required'				=>	1,
				'signup_category_label'					=>	__('Site Categories', SITE_CATEGORIES_TAXONOMY),
				'signup_description_required'			=>	1,
				'signup_description_label'				=>	__('Site Description', SITE_CATEGORIES_TAXONOMY)
			),
			
			'categories'					=>	array(
				'per_page'					=>	5,
				'hide_empty'				=>	0,
				'show_description'			=>	0,
				'show_description_children'	=>	0,
				'show_counts'				=>	0,
				'show_counts_children'		=>	0,
				'icon_show'					=>	0,
				'icon_show_children'		=>	0,
				'icon_size'					=>	32,
				'icon_size_children'		=>	32,
				'show_style'				=>	'ul',
				'grid_cols'					=>	3,
				'grid_rows'					=>	3,
				'orderby'					=>	'name',
				'order'						=>	'ASC',
			)
		);


		//$this->_settings['options_key']				= "site-categories-". $this->_settings['VERSION']; 

		$this->opts = get_blog_option( $current_site->blog_id, $this->_settings['options_key'], false);
		if (!$this->opts) {
			
			$legacy_versions = array('1.0.4', '1.0.3', '1.0.2', '1.0.1', '1.0.0');
			
			foreach($legacy_versions as $legacy_version) {
				$options_key = "site-categories-". $legacy_version;
				$this->opts = get_blog_option( $wpdb->blogid, $options_key );

				if (!empty($this->opts)) {
					$this->opts['version'] = $legacy_version;
					break;
				}
			}
			
			if (empty($this->opts)) {
				$this->opts = $defaults;
			}
			
			// Now that we have loaded the legacy or default options save it. 
			$this->save_config();
				
		} else {
			
			if (!isset($this->opts['sites']))
				$this->opts['sites'] = $defaults['sites'];
			else
				$this->opts['sites'] = wp_parse_args( (array) $this->opts['sites'], $defaults['sites'] );

			if (!isset($this->opts['categories']))
				$this->opts['categories'] = $defaults['categories'];
			else
				$this->opts['categories'] = wp_parse_args( (array) $this->opts['categories'], $defaults['categories'] );
				
			$this->opts = wp_parse_args( (array) $this->opts, $defaults ); 			
			
			//echo "opts<pre>"; print_r($this->opts); echo "</pre>";
		}
	}
	
	/**
	 * Save our config information to the primary site options
	 *
	 * @since 1.0.0
	 * @param none
	 * @return none
	 */	
	function save_config() {
		global $current_site;
		
		$this->opts['version'] = $this->_settings['VERSION'];
		
		update_blog_option( $current_site->blog_id, $this->_settings['options_key'], $this->opts);		
	}
	
	/**
	 * Setup the rewrite rules for our Taxonomy terms. 
	 *
	 * @since 1.0.0
	 * @param none
	 * @return none
	 */
	
	function insert_rewrite_rules ($old) {
		
		$this->load_config();
		if ((isset($this->opts['landing_page_slug'])) && (strlen($this->opts['landing_page_slug']))) {
		
			$site_url = get_site_url();
			$landing_page_slug = str_replace(trailingslashit($site_url), '', $this->opts['landing_page_slug']);
			if ($landing_page_slug) {
				$landing_page_slug = untrailingslashit($landing_page_slug);
		
				$new = array(
					'(' . $landing_page_slug . ')/([^/]*)/?$' => 'index.php?pagename=$matches[1]&category_name=$matches[2]',
					'(' . $landing_page_slug . ')/([^/]*)/(\d+)/?$' => 'index.php?pagename=$matches[1]&category_name=$matches[2]&start_at=$matches[3]',
					);
			
				return $new + $old;
			}
		} 	
		return $old;
	}

	/**
	 * 
	 *
	 * @since 1.0.0
	 * @param none
	 * @return none
	 */
	function insert_query_vars ($vars) {
		$vars[] = 'category_name';
		$vars[] = 'start_at';

    	return $vars;
	}


	/**
	 * For the main site Settings. 
	 *
	 * @since 1.0.0
	 * @param none
	 * @return none
	 */
	function process_actions_main_site() {

		global $wp_rewrite;

		if (isset($_POST['bcat'])) {

			$TRIGGER_UPDATE_REWRITE = false;

			if (isset($_POST['bcat']['categories']))
				$this->opts['categories'] = $_POST['bcat']['categories'];
			
			if (isset($_POST['bcat']['sites'])) {
				$this->opts['sites'] = $_POST['bcat']['sites'];

				if ((isset($this->opts['sites']['category_excludes'])) 
				 && (!empty($this->opts['sites']['category_excludes']))) {
					$cat_excludes = explode(',', $this->opts['sites']['category_excludes']);
					if (($cat_excludes) && (count($cat_excludes))) {
						foreach($cat_excludes as $_idx => $_val) {
							$cat_excludes[$_idx] = trim($_val);
							if (empty($cat_excludes[$_idx]))
								unset($cat_excludes[$_idx]);
						}
						$cat_excludes = array_values($cat_excludes);
					}
					$this->opts['sites']['category_excludes'] = $cat_excludes;
				} else {
					$this->opts['sites']['category_excludes'] = array();
				}
			}
			

			if ((isset($_POST['bcat']['landing_page_id'])) && (intval($_POST['bcat']['landing_page_id']))) {

				$this->opts['landing_page_id'] = $_POST['bcat']['landing_page_id'];
				$this->opts['landing_page_slug'] = get_permalink(intval($this->opts['landing_page_id']));
				
				if ( isset($wp_rewrite) && $wp_rewrite->using_permalinks() )
					$this->opts['landing_page_rewrite'] = true;						
				else
					$this->opts['landing_page_rewrite'] = false;					
				
			} else {
				$this->opts['landing_page_id'] = 0;
				$this->opts['landing_page_slug'] = '';
			}
						
			$this->save_config();
			$wp_rewrite->flush_rules();			
			
			$location = add_query_arg('message', 'success-settings');
			if ($location) {
				wp_redirect($location);
				die();
			}					
		}
	}
	
	/**
	 * 
	 *
	 * @since 1.0.0
	 * @param none
	 * @return none
	 */
	function process_actions_site() {

		global $wpdb, $current_site, $current_blog;
		
		$CONFIG_CHANGED = false;
		if (isset($_POST['bcat_site_categories'])) {
			
			switch_to_blog( $current_site->blog_id );

			$bcat_site_categories = array();
			if (count($_POST['bcat_site_categories'])) {

				$site_all_categories = array();
				$_cats = wp_get_object_terms($current_blog->blog_id, SITE_CATEGORIES_TAXONOMY);
				if (($_cats) && (is_array($_cats)) && (count($_cats))) {
					foreach($_cats as $_cat) {
						$site_all_categories[$_cat->term_taxonomy_id] = $_cat;
					}
				}

				foreach($_POST['bcat_site_categories'] as $bcat_id) {

					// Double check the selected site categories in case the admin didn't select all items. 
					$bcat_id = intval($bcat_id);
					if ($bcat_id > 0) {
					
						$bcat_term = get_term($bcat_id, SITE_CATEGORIES_TAXONOMY);
						if ( !is_wp_error($bcat_term)) {
							$bcat_site_categories[] = $bcat_term->slug;
							$site_all_categories[$bcat_term->term_taxonomy_id] = $bcat_term;
						}
					}
				}
			}
			$bcat_set = wp_set_object_terms($current_blog->blog_id, $bcat_site_categories, SITE_CATEGORIES_TAXONOMY);

			if (count($site_all_categories)) {
				$this->bcat_taxonomy_terms_count(array_keys($site_all_categories), get_taxonomy(SITE_CATEGORIES_TAXONOMY));
			}

			restore_current_blog();
			$CONFIG_CHANGED = true;
		}

		if (isset($_POST['bcat_site_description'])) {
			$bcat_site_description = esc_attr(stripslashes($_POST['bcat_site_description']));
			update_option('bact_site_description', $bcat_site_description);
			$CONFIG_CHANGED = true;
		}
		
		if ($CONFIG_CHANGED == true) {
			$location = add_query_arg('message', 'success-settings');
			if ($location) {
				wp_redirect($location);
				die();
			}
		}
	}
	
	/**
	 * Display our message on the Snapshot page(s) header for actions taken 
	 *
	 * @since 1.0.0
	 * @uses $this->_messages Set in form processing functions
	 *
	 * @param none
	 * @return none
	 */
	function admin_notices_proc() {
		
		// IF set during the processing logic setsp for add, edit, restore
		if ( (isset($_REQUEST['message'])) && (isset($this->_messages[$_REQUEST['message']])) ) {
			?><div id='user-report-warning' class='updated fade'><p><?php echo $this->_messages[$_REQUEST['message']]; ?></p></div><?php
		}
		
		// IF we set an error display in red box
		if (strlen($this->_admin_header_error))
		{
			?><div id='user-report-error' class='error'><p><?php echo $this->_admin_header_error; ?></p></div><?php
		}
	}
	
	
	/**
	 * Setup our Taxonomy
	 *
	 * @since 1.0.0
	 *
	 * @param none
	 * @return none
	 */
	function register_taxonomy_proc() {
	
		if (is_multisite()) {
			// Add new taxonomy, make it hierarchical (like categories)
			$labels = array(
				'name' 					=> 	_x( 'Site Categories', 'taxonomy general name', SITE_CATEGORIES_I18N_DOMAIN ),
				'singular_name' 		=> 	_x( 'Site Category', 'taxonomy singular name', SITE_CATEGORIES_I18N_DOMAIN ),
				'search_items' 			=>  __( 'Search Site Categories', SITE_CATEGORIES_I18N_DOMAIN ),
				'all_items' 			=> 	__( 'All Site Categories', SITE_CATEGORIES_I18N_DOMAIN ),
				'parent_item' 			=> 	__( 'Parent Site Category', SITE_CATEGORIES_I18N_DOMAIN ),
				'parent_item_colon' 	=> 	__( 'Parent Site Category:', SITE_CATEGORIES_I18N_DOMAIN ),
				'edit_item' 			=> 	__( 'Edit Site Category', SITE_CATEGORIES_I18N_DOMAIN ), 
				'update_item' 			=> 	__( 'Update Site Category', SITE_CATEGORIES_I18N_DOMAIN ),
				'add_new_item' 			=> 	__( 'Add New Site Category', SITE_CATEGORIES_I18N_DOMAIN ),
				'new_item_name' 		=> 	__( 'New Site Category Name', SITE_CATEGORIES_I18N_DOMAIN ),
				'menu_name' 			=> 	__( 'Site Category', SITE_CATEGORIES_I18N_DOMAIN ),
			); 	


			if (is_super_admin()) {
				$show_ui 	= true;
				$query_var	= true;
				$rewrite	= array( 'slug' => SITE_CATEGORIES_TAXONOMY );
			}
			else {
				$show_ui 	= false;
				$query_var	= false;
				$rewrite	= '';
			}
				
			register_taxonomy(SITE_CATEGORIES_TAXONOMY, null, array(
				'hierarchical' 				=> 	true,
				'update_count_callback'		=>	array($this, 'bcat_taxonomy_terms_count'),
				'labels' 					=> 	$labels,
				'show_ui' 					=> 	$show_ui,
				'query_var' 				=> 	$query_var,
				'rewrite' 					=> 	$rewrite
			));
		}
	}
	
	/**
	 * 
	 *
	 * @since 1.0.0
	 *
	 * @param none
	 * @return none
	 */
	function bcat_taxonomy_terms_count($tt_ids, $taxonomy) {
		global $wpdb, $current_site, $current_blog; 
		
		if ($taxonomy->name != SITE_CATEGORIES_TAXONOMY) return;
		
		switch_to_blog( $current_site->blog_id );

		foreach($tt_ids as $tt_id) {
			//$sql_str = $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->term_relationships WHERE term_taxonomy_id = %d", $tt_id );
			$sql_str = $wpdb->prepare( "SELECT COUNT( $wpdb->blogs.blog_id ) as count FROM $wpdb->term_relationships LEFT JOIN $wpdb->blogs ON $wpdb->term_relationships.object_id = $wpdb->blogs.blog_id WHERE $wpdb->term_relationships.term_taxonomy_id =%d AND $wpdb->blogs.blog_id IS NOT NULL AND $wpdb->blogs.public = 1 AND $wpdb->blogs.archived = '0' AND $wpdb->blogs.mature = 0 AND $wpdb->blogs.spam = 0 AND $wpdb->blogs.deleted = 0", $tt_id );
			
			
			//echo "sql_str=[". $sql_str ."]<br />";
			$count = $wpdb->get_var( $sql_str );
			//echo "count=[". $count ."]<br />";
			//die();

			$wpdb->update( $wpdb->term_taxonomy, array('count' => $count ), array( 'term_taxonomy_id' => $tt_id ) );
		}
		restore_current_blog();		
	}
	
	/**
	 * Handled the delete blog actions (archive, delete, Deactivate, Spam). Remove the blog site categories. 
	 *
	 * @since 1.0.7.2
	 *
	 * @param none
	 * @return none
	 */
	function blog_change_status_count($blog_id) {
		global $wpdb, $current_site;

		if (!$blog_id) return;
		if (!(isset($_GET['action']))) return;
		
		$blog_state_action = esc_attr($_GET['action']);

		switch_to_blog( $current_site->blog_id );

		switch($blog_state_action) {
			case 'deleteblog':
				wp_delete_object_term_relationships($blog_id, SITE_CATEGORIES_TAXONOMY);
				break;
			
			case 'spamblog':
			case 'mature_blog':
			case 'archiveblog':
			case 'deactivateblog':
				$terms = wp_get_object_terms( $blog_id, SITE_CATEGORIES_TAXONOMY);
				if ( (!is_wp_error($terms)) && ($terms) && (is_array($terms)) && (count($terms))) {
					foreach($terms as $term) {

						$term_sites = $this->get_taxonomy_sites($term->term_id);
						if ((!$term_sites) || (!is_array($term_sites)))
							$term_sites = array();

						if (isset($term_sites[$blog_id]))
							unset($term_sites[$blog_id]);
								
						$terms_count = count($term_sites);
						$wpdb->update( $wpdb->term_taxonomy, array('count' => $terms_count ), array( 'term_taxonomy_id' => $term->term_taxonomy_id ) );
					}
				}
				break;
				
			case 'unspamblog':
			case 'unarchiveblog':
			case 'activateblog':
				$terms = wp_get_object_terms( $blog_id, SITE_CATEGORIES_TAXONOMY);
				if ( (!is_wp_error($terms)) && ($terms) && (is_array($terms)) && (count($terms))) {
					foreach($terms as $term) {

						$term_sites = $this->get_taxonomy_sites($term->term_id);
						if ((!$term_sites) || (!is_array($term_sites)))
							$term_sites = array();

						if (isset($term_sites[$blog_id]))
							unset($term_sites[$blog_id]);

						$terms_count = count($term_sites);

						$blog = get_blog_details($blog_id);
						if (($blog) && ($blog->public == 1) && ($blog->archived == 0) && ($blog->spam == 0) && ($blog->deleted == 0) && ($blog->mature == 0)) {
							$terms_count += 1;
						}
						$wpdb->update( $wpdb->term_taxonomy, array('count' => $terms_count ), array( 'term_taxonomy_id' => $term->term_taxonomy_id ) );
					}
				}
				break;
		}

		restore_current_blog();
	}
	
	
	/**
	 * Add the new Menu to the Tools section in the WordPress main nav
	 *
	 * @since 1.0.0
	 * @uses $this->_pagehooks 
	 * @see $this->__construct where this function is referenced
	 *
	 * @param none
	 * @return none
	 */
	function admin_menu_proc() {

		if ((is_multisite()) && (is_main_site()) && (is_super_admin())) {

			$page_hook = add_menu_page( _x("Site Categories", 'page label', SITE_CATEGORIES_I18N_DOMAIN), 
							_x("Site Categories", 'menu label', SITE_CATEGORIES_I18N_DOMAIN),
							'manage_options',
							'bcat_settings',
							array(&$this, 'settings_panel_main_site')
			);

			$this->_pagehooks['site-categories-settings-main-site'] = add_submenu_page( 
						'bcat_settings', 
						_x('Settings','page label', SITE_CATEGORIES_I18N_DOMAIN), 
						_x('Settings', 'menu label', SITE_CATEGORIES_I18N_DOMAIN), 
						'manage_options',
						'bcat_settings', 
						array(&$this, 'settings_panel_main_site')
			);

			$this->_pagehooks['site_categories-terms'] = add_submenu_page( 
						'bcat_settings', 
						_x('Site Categories','page label', SITE_CATEGORIES_I18N_DOMAIN), 
						_x('Site Categories', 'menu label', SITE_CATEGORIES_I18N_DOMAIN), 
						'manage_options',
						'edit-tags.php?taxonomy=bcat'
			);

			// Hook into the WordPress load page action for our new nav items. This is better then checking page query_str values.
			add_action('load-'. $this->_pagehooks['site-categories-settings-main-site'], 		array(&$this, 'on_load_page_main_site'));
			
		} 
			
		$this->_pagehooks['site-categories-settings-site'] = add_options_page(
			_x("Site Categories", 'page label', SITE_CATEGORIES_I18N_DOMAIN), 
			_x("Site Categories", 'menu label', SITE_CATEGORIES_I18N_DOMAIN),
			'manage_options', 
			'bcat_settings_site', 
			array(&$this, 'settings_panel_site')
		);
		
		add_action('load-'. $this->_pagehooks['site-categories-settings-site'], 		array(&$this, 'on_load_page_site'));
	}

	/**
	 * 
	 *
	 * @since 1.0.0
	 *
	 * @param none
	 * @return none
	 */
	function on_load_page_main_site() {
		
		if ( ! current_user_can( 'manage_options' ) )
			wp_die( __( 'Cheatin&#8217; uh?' ) );

		$this->_messages['success-settings'] 			= __( "Settings have been update.", SITE_CATEGORIES_I18N_DOMAIN );

		$this->load_config();
		$this->process_actions_main_site();
		$this->admin_plugin_help();

		wp_enqueue_script('common');
		wp_enqueue_script('wp-lists');
		wp_enqueue_script('postbox');
		
		wp_register_script('site-categories-admin', WP_PLUGIN_URL .'/'. basename(dirname(__FILE__)) .'/js/jquery.site-categories-admin.js', 
			array('jquery'), $this->_settings['VERSION']  );
		wp_enqueue_script('site-categories-admin');
		
		// Now add our metaboxes
		add_meta_box('site-categories-settings-main-admin-display_options-panel', 
			__('Landing Page Selection', SITE_CATEGORIES_I18N_DOMAIN), 
			array(&$this, 'settings_main_admin_display_options_panel'), 
			$this->_pagehooks['site-categories-settings-main-site'], 
			'normal', 'core');

		add_meta_box('site-categories-settings-main-admin-display_selection-options-panel', 
			__('Site Categories Selection Options', SITE_CATEGORIES_I18N_DOMAIN), 
			array(&$this, 'settings_main_admin_display_selection_options_panel'), 
			$this->_pagehooks['site-categories-settings-main-site'], 
			'normal', 'core');

		add_meta_box('site-categories-settings-main-categories-display-options-panel', 
			__('Landing Page Categories Display Options', SITE_CATEGORIES_I18N_DOMAIN), 
			array(&$this, 'settings_main_categories_display_options_panel'), 
			$this->_pagehooks['site-categories-settings-main-site'], 
			'normal', 'core');

		add_meta_box('site-categories-settings-main-sites-display-options-panel', 
			__('Landing Page Sites Display Options', SITE_CATEGORIES_I18N_DOMAIN), 
			array(&$this, 'settings_main_sites_display_options_panel'), 
			$this->_pagehooks['site-categories-settings-main-site'], 
			'normal', 'core');

		add_meta_box('site-categories-settings-main-sites-signup-form-options-panel', 
			__('New Site Signup Form Options', SITE_CATEGORIES_I18N_DOMAIN), 
			array(&$this, 'settings_main_sites_signup_form_options_panel'), 
			$this->_pagehooks['site-categories-settings-main-site'], 
			'normal', 'core');

	}


	/**
	 * 
	 *
	 * @since 1.0.0
	 *
	 * @param none
	 * @return none
	 */
	function on_load_page_site() {
		
		if ( ! current_user_can( 'manage_options' ) )
			wp_die( __( 'Cheatin&#8217; uh?' ) );

		$this->_messages['success-settings'] 			= __( "Settings have been update.", SITE_CATEGORIES_I18N_DOMAIN );

		$this->load_config();
		$this->process_actions_site();
		$this->site_plugin_help();
		
		wp_enqueue_script('common');
		wp_enqueue_script('wp-lists');
		wp_enqueue_script('postbox');
				
		// Now add our metaboxes
		add_meta_box('site-categories-settings-site-categories-panel', 
			__('Select the Categories for this site', SITE_CATEGORIES_I18N_DOMAIN), 
			array(&$this, 'settings_site_select_categories_panel'), 
			$this->_pagehooks['site-categories-settings-site'], 
			'normal', 'core');

		add_meta_box('site-categories-settings-site-description-panel', 
			__('Site Description', SITE_CATEGORIES_I18N_DOMAIN), 
			array(&$this, 'settings_site_description_panel'), 
			$this->_pagehooks['site-categories-settings-site'], 
			'normal', 'core');

	}


	/**
	 * 
	 *
	 * @since 1.0.0
	 *
	 * @param none
	 * @return none
	 */
	function admin_plugin_help() {
		global $wp_version;
				
		$screen = get_current_screen();
		//echo "screen<pre>"; print_r($screen); echo "</pre>";
		
		$screen_help_text = array();
		
		/**
		Left navigation list
		*/
		$screen_help_text['site-categories-help-overview'] = '<p>' . __( 'This Settings panel controls various display options for the Landing page. This landing page is hosted only on the primary site and from the options on this page you can control the layout of the site categories items.', SITE_CATEGORIES_I18N_DOMAIN ) . '</p>';

		$screen_help_text['site-categories-help-overview'] .= "<ul>";

		$screen_help_text['site-categories-help-overview'] .= '<li><strong>'. __('Landing Page Selection', SITE_CATEGORIES_I18N_DOMAIN) .'</strong> - '. __('This Selection lets you set the landing page to be used when displaying the Site Categories.', SITE_CATEGORIES_I18N_DOMAIN) .'</li>';

		$screen_help_text['site-categories-help-overview'] .= '<li><strong>'. __('Site Categories Selection Options', SITE_CATEGORIES_I18N_DOMAIN) .'</strong> - '. __('This Selection lets control how the Site Categories will be seen and selected by other site admin users.', SITE_CATEGORIES_I18N_DOMAIN) .'</li>';

		$screen_help_text['site-categories-help-overview'] .= '<li><strong>'. __('Landing Page Categories Display Options', SITE_CATEGORIES_I18N_DOMAIN) .'</strong> - '. __('This Selection controls the output of the Site Categories on the landing page. Here you can control the style, icons, number of categories per page, etc.', SITE_CATEGORIES_I18N_DOMAIN) .'</li>';

		$screen_help_text['site-categories-help-overview'] .= '<li><strong>'. __('Landing Page Sites Display Options', SITE_CATEGORIES_I18N_DOMAIN) .'</strong> - '. __('This Selection controls the output of the Sites on the landing page. Here you can control the style, icons, number of sites per page, etc.', SITE_CATEGORIES_I18N_DOMAIN) .'</li>';
		
		$screen_help_text['site-categories-help-overview'] .= '<li><strong>'. __('New Site Signup Form Options', SITE_CATEGORIES_I18N_DOMAIN) .'</strong> - '. __('This Selection controls how the Site Categories options are display on the New Site Signup Form.', SITE_CATEGORIES_I18N_DOMAIN) .'</li>';
		
		$screen_help_text['site-categories-help-overview'] .= "</ul>";


		/**
		Landing Page Selection
		*/
		$screen_help_text['site-categories-help-settings-landing'] = '<p>'. __('The Landing Page Selection lets you set the landing page to be used when displaying the Site Categories.', SITE_CATEGORIES_I18N_DOMAIN). '</p>';
		$screen_help_text['site-categories-help-settings-landing'] .= '<ul>';
			
		$screen_help_text['site-categories-help-settings-landing'] .= '<li><strong>'. __('Select Landing Page', SITE_CATEGORIES_I18N_DOMAIN). '</strong> - '. __('Select the page to function as the site categories landing page. The landing page will be inserted automatically at the bottom of the page content.', SITE_CATEGORIES_I18N_DOMAIN). '</li>';
		$screen_help_text['site-categories-help-settings-landing'] .= '</ul>';
	
	
		/**
		Site Categories Selection Options
		*/	
		$screen_help_text['site-categories-help-settings-selection'] = '<p>'. __('This Selection lets control how the Site Categories will be seen and selected by other site admin users.', SITE_CATEGORIES_I18N_DOMAIN).'</p>';
		$screen_help_text['site-categories-help-settings-selection'] .= '<ul>';
		
		$screen_help_text['site-categories-help-settings-selection'] .= '<li><strong>'. __('Number of Categories per site', SITE_CATEGORIES_I18N_DOMAIN). '</strong> - '. __(' This option controls the number of dropdown selectors the site admin will see when creating a new site or under the Site Categories Settings option within an existing site.', SITE_CATEGORIES_I18N_DOMAIN). '</li>';

		$screen_help_text['site-categories-help-settings-selection'] .= '<li><strong>'. __('Number of categories per site', SITE_CATEGORIES_I18N_DOMAIN). '</strong> - '. __('This controls the number of categories a site can set. Within the site settings panel the admin will see a number of dropdowns for the Site Categories. The admin can set one or more of these to the available site categories.', SITE_CATEGORIES_I18N_DOMAIN). '</li>';
		
		$screen_help_text['site-categories-help-settings-selection'] .= '<li><strong>'. __('Pro Sites', SITE_CATEGORIES_I18N_DOMAIN). '</strong> - '. sprintf(__('If you have the %1$sPro Sites%2$s plugins installed you can assign a different number of Site Categories for each level', SITE_CATEGORIES_I18N_DOMAIN), '<a href="http://premium.wpmudev.org/project/pro-sites/" target="_blank">', '</a>'). '</li>';

			$screen_help_text['site-categories-help-settings-selection'] .= '<li><strong>'. __('Category parents selectable', SITE_CATEGORIES_I18N_DOMAIN). '</strong> - '. __('With this option to can force the selection of only child categories from the dropdown selectors. This is handy when displaying your Site Categories using the Grid layout.', SITE_CATEGORIES_I18N_DOMAIN) . '</li>';
			
		$screen_help_text['site-categories-help-settings-selection'] .= '</ul>';
		
		
		/**
		Landing Page Categories Display Options 
		*/		
		$screen_help_text['site-categories-help-settings-landing-categories'] = '<p>'. __('This Selection controls the output of the Site Categories on the landing page. Here you can control the style, icons, number of categories per page, etc.', SITE_CATEGORIES_I18N_DOMAIN) .'</p>';
		$screen_help_text['site-categories-help-settings-landing-categories'] .= '<ul>';
		$screen_help_text['site-categories-help-settings-landing-categories'] .= '<li><strong>'. __('Display Style', SITE_CATEGORIES_I18N_DOMAIN) .'</strong> - '. __('The Display style is how the Site Categories are presented on the page. From the dropdown you can select a simple list or try more advanced display options like Grid or Accordion.', SITE_CATEGORIES_I18N_DOMAIN) .'</li>';

		$screen_help_text['site-categories-help-settings-landing-categories'] .= '<li><strong>'. __('Categories per page', SITE_CATEGORIES_I18N_DOMAIN) .'</strong> - '. __('This is the number of categories to show on a given page. If you have hundreds of site categories you probably would not want these all to show on a single page. That would be too much information for the user to digest. So you can set the number of categories to something manageable like 20, 50 or 100.', SITE_CATEGORIES_I18N_DOMAIN) .'</li>';

		$screen_help_text['site-categories-help-settings-landing-categories'] .= '<li><strong>'. __('Order by', SITE_CATEGORIES_I18N_DOMAIN) .'</strong> - '. __('By default the displayed site categories will be ordered by Name. Using this option you can adjust the order to your liking.', SITE_CATEGORIES_I18N_DOMAIN) .'</li>';
		
		$screen_help_text['site-categories-help-settings-landing-categories'] .= '<li><strong>'. __('Hide empty Categories', SITE_CATEGORIES_I18N_DOMAIN) .'</strong> - '. __('If a site categories does not have any sites assigned to it you might want to hide it from the listing.', SITE_CATEGORIES_I18N_DOMAIN) .'</li>';

		$screen_help_text['site-categories-help-settings-landing-categories'] .= '<li><strong>'. __('Show Counts', SITE_CATEGORIES_I18N_DOMAIN) .'</strong> - '. __('Similar to the hide empty this option lets you show the user just how many sites are associated with each site category.', SITE_CATEGORIES_I18N_DOMAIN) .'</li>';
		
		$screen_help_text['site-categories-help-settings-landing-categories'] .= '<li><strong>'. __('Show Category Description', SITE_CATEGORIES_I18N_DOMAIN) .'</strong> - '. __('When you create the site categories you can provide a detailed description. This description can be shown as part of the display output. ', SITE_CATEGORIES_I18N_DOMAIN) .'</li>';
		
		$screen_help_text['site-categories-help-settings-landing-categories'] .= '<li><strong>'. __('Show Icons', SITE_CATEGORIES_I18N_DOMAIN) .'</strong> - '. __('As part of the site categories setup you can upload or select an image to represent the site category. Using this option will show those icons as part of the display output.', SITE_CATEGORIES_I18N_DOMAIN) .'</li>';
		
		$screen_help_text['site-categories-help-settings-landing-categories'] .= '<li><strong>'. __('Icon size', SITE_CATEGORIES_I18N_DOMAIN) .'</strong> - '. __('If you chose to display site category icons you can control the size of these icons using this option.', SITE_CATEGORIES_I18N_DOMAIN) .'</li>';

		$screen_help_text['site-categories-help-settings-landing-categories'] .= '</ul>';


		/**
		Landing Page Sites Display Options 
		*/			
		$screen_help_text['site-categories-help-settings-landing-sites'] = '<p>'. __('This Selection controls the output of the Sites on the landing page. Here you can control the style, icons, number of sites per page, etc.', SITE_CATEGORIES_I18N_DOMAIN) .'</p>';
		$screen_help_text['site-categories-help-settings-landing-sites'] .= '<ul>';
		$screen_help_text['site-categories-help-settings-landing-sites'] .= '<li><strong>'. __('Display Style', SITE_CATEGORIES_I18N_DOMAIN) .'</strong> - '. __('The Display style is how the Sites are presented on the page.', SITE_CATEGORIES_I18N_DOMAIN) .'</li>';

		$screen_help_text['site-categories-help-settings-landing-sites'] .= '<li><strong>'. __('Sites per page', SITE_CATEGORIES_I18N_DOMAIN) .'</strong> - '. __('This is the number of site to show on a given page. If you have hundreds of sites in a single category you probably would not want these all to show on a single page. That would be too much information for the user to digest. So you can set the number of sites to something manageable like 20, 50 or 100.', SITE_CATEGORIES_I18N_DOMAIN) .'</li>';

		$screen_help_text['site-categories-help-settings-landing-sites'] .= '<li><strong>'. __('Show Site Description', SITE_CATEGORIES_I18N_DOMAIN) .'</strong> - '. __('On the Site Admin Settings page the admin can enter a description for the site. This is similar to the Site Category description. If provided by the site is will be displayed as part of the page output.', SITE_CATEGORIES_I18N_DOMAIN) .'</li>';

		$screen_help_text['site-categories-help-settings-landing-sites'] .= '<li><strong>'. __('Show Icons', SITE_CATEGORIES_I18N_DOMAIN) .'</strong> - '. sprintf(__('If the %1$sAvatars%2$s plugins is installed you can show the Site icon as part of the display output.', SITE_CATEGORIES_I18N_DOMAIN), 
			'<a href="http://premium.wpmudev.org/project/avatars/" target="_blank">', '</a>'). '</li>';
		
		$screen_help_text['site-categories-help-settings-landing-sites'] .= '<li><strong>'. __('Icon size', SITE_CATEGORIES_I18N_DOMAIN) .'</strong> - '. __('If you chose to display site icons you can control the size of these icons using this option.', SITE_CATEGORIES_I18N_DOMAIN) .'</li>';
		
		$screen_help_text['site-categories-help-settings-landing-sites'] .= '</ul>';


		/**
		New Site Signup Form Options
		*/
		$screen_help_text['site-categories-help-signup-form'] = '<p>'. __('This Selection controls how the Site Categories options are display on the New Site Signup Form.', SITE_CATEGORIES_I18N_DOMAIN) .'</p>';
		$screen_help_text['site-categories-help-signup-form'] .= '<ul>';
		$screen_help_text['site-categories-help-signup-form'] .= '<li><strong>'. __('Show Site Categories section', SITE_CATEGORIES_I18N_DOMAIN) .'</strong> - '. __('With this option you can control the display of the Site Categories dropdowns and description on the New Site Signup Form.', SITE_CATEGORIES_I18N_DOMAIN) .'</li>';
		$screen_help_text['site-categories-help-signup-form'] .= '<li><strong>'. __('Site Categories Selection Required', SITE_CATEGORIES_I18N_DOMAIN) .'</strong> - '. __('Allows you to force the new site admin to select Site Categories options. If set the admin is required to set ', SITE_CATEGORIES_I18N_DOMAIN) .'</li>';
		$screen_help_text['site-categories-help-signup-form'] .= '<li><strong>'. __('Label for Site Categories Dropdowns', SITE_CATEGORIES_I18N_DOMAIN) .'</strong> - '. __('This option lets you use an alternate form label for the Site Categories dropdown selector. Maybe something more descriptive to the user.', SITE_CATEGORIES_I18N_DOMAIN) .'</li>';
		$screen_help_text['site-categories-help-signup-form'] .= '<li><strong>'. __('Description is Required', SITE_CATEGORIES_I18N_DOMAIN) .'</strong> - '. __('This option control if the Site Description textarea field is required on the form.', SITE_CATEGORIES_I18N_DOMAIN) .'</li>';
		$screen_help_text['site-categories-help-signup-form'] .= '<li><strong>'. __('Label for Site Categories Description', SITE_CATEGORIES_I18N_DOMAIN) .'</strong> - '. __('This option lets you use an alternate form label for the Site Description field.', SITE_CATEGORIES_I18N_DOMAIN) .'</li>';
		$screen_help_text['site-categories-help-signup-form'] .= '</ul>';


		if ( version_compare( $wp_version, '3.3.0', '>' ) ) {
			
			if ((isset($_REQUEST['page'])) && ($_REQUEST['page'] == "bcat_settings")) {
		
				$screen->add_help_tab( array(
					'id'		=> 'site-categories-help-overview',
					'title'		=> __('Settings Overview', SITE_CATEGORIES_I18N_DOMAIN ),
					'content'	=>  $screen_help_text['site-categories-help-overview']
			    	) 
				);

				$screen->add_help_tab( array(
					'id'		=> 'site-categories-help-settings-landing',
					'title'		=> __('Landing Page Selection', SITE_CATEGORIES_I18N_DOMAIN ),
					'content'	=>  $screen_help_text['site-categories-help-settings-landing']
			    	) 
				);

				$screen->add_help_tab( array(
					'id'		=> 'site-categories-help-settings-selection',
					'title'		=> __('Site Categories Selection Options', SITE_CATEGORIES_I18N_DOMAIN ),
					'content'	=>  $screen_help_text['site-categories-help-settings-selection']
			    	) 
				);
				
				$screen->add_help_tab( array(
					'id'		=> 'site-categories-help-settings-landing-categories',
					'title'		=> __('Categories Display Options', SITE_CATEGORIES_I18N_DOMAIN ),
					'content'	=>  $screen_help_text['site-categories-help-settings-landing-categories']
			    	) 
				);

				$screen->add_help_tab( array(
					'id'		=> 'site-categories-help-settings-landing-sites',
					'title'		=> __('Sites Display Options', SITE_CATEGORIES_I18N_DOMAIN ),
					'content'	=>  $screen_help_text['site-categories-help-settings-landing-sites']
			    	) 
				);

				$screen->add_help_tab( array(
					'id'		=> 'site-categories-help-signup-form',
					'title'		=> __('New Site Signup Form Options', SITE_CATEGORIES_I18N_DOMAIN ),
					'content'	=>  $screen_help_text['site-categories-help-signup-form']
			    	) 
				);
				
			}			
		} 
	}


	/**
	 * 
	 *
	 * @since 1.0.0
	 *
	 * @param none
	 * @return none
	 */
	function site_plugin_help() {
		global $wp_version;

		$screen = get_current_screen();
		//echo "screen<pre>"; print_r($screen); echo "</pre>";

		$screen_help_text = array();

		$screen_help_text['site-categories-page-settings'] = '<p>' . __( 'This page lets you associate this site with various Site Categories. The Site Categories are global to this Multisite network of sites and stores within the primary site.', SITE_CATEGORIES_I18N_DOMAIN). '</p>';
		$screen_help_text['site-categories-page-settings'] .= '<ul>';
		$screen_help_text['site-categories-page-settings'] .= '<li><strong>'. __('Select the Categories for this site', SITE_CATEGORIES_I18N_DOMAIN) .'</strong> - '. __('Depending on the number of allowed categories by the super admin you will see a number of dropdowns where you can select the site category this site is to be associated.', SITE_CATEGORIES_I18N_DOMAIN) .'</li>';
		$screen_help_text['site-categories-page-settings'] .= '<li><strong>'. __('Site Description', SITE_CATEGORIES_I18N_DOMAIN) .'</strong> - '. __('Also on this page you can enter an optional Site Description. The site description is used on the Site Categories landing page of the primary site.', SITE_CATEGORIES_I18N_DOMAIN ) . '</li>';


		if ( version_compare( $wp_version, '3.3.0', '>' ) ) {

			if ((isset($_REQUEST['page'])) && ($_REQUEST['page'] == "bcat_settings_site")) {

				$screen->add_help_tab( array(
					'id'		=> 'site-categories-page-settings',
					'title'		=> __('Settings Overview', SITE_CATEGORIES_I18N_DOMAIN ),
					'content'	=>  $screen_help_text['site-categories-page-settings']
			    	) 
				);
			}			
		} 
	}
	
	/**
	 * Metabox showing form for Settings.
	 *
	 * @since 1.0.0
	 *
	 * @param none
	 * @return none
	 */		
	function settings_panel_main_site() {

		?>
		<div id="site-categories-panel" class="wrap site-categories-wrap">
			<?php screen_icon(); ?>
			<h2><?php _ex("Site Categories Settings", "Site Categories New Page Title", SITE_CATEGORIES_I18N_DOMAIN); ?></h2>

			<div id="poststuff" class="metabox-holder">
				<div id="post-body" class="">
					<div id="post-body-content" class="site-categories-metabox-holder-main">
						<form id="bcat_settings_form" action="<?php echo admin_url('admin.php?page=bcat_settings'); ?>" method="post">
							<?php do_meta_boxes($this->_pagehooks['site-categories-settings-main-site'], 'normal', ''); ?>
							<input class="button-primary" type="submit" value="<?php _e('Save Settings', SITE_CATEGORIES_I18N_DOMAIN); ?>" />
						</form>
					</div>
				</div>
			</div>	
		</div>
		<script type="text/javascript">
			//<![CDATA[
			jQuery(document).ready( function($) {
				// close postboxes that should be closed
				$('.if-js-closed').removeClass('if-js-closed').addClass('closed');

				// postboxes setup
				postboxes.add_postbox_toggles('<?php echo $this->_pagehooks['site-categories-settings-main-site']; ?>');
			});
			//]]>
		</script>
		<?php
	}

	/**
	 * 
	 *
	 * @since 1.0.0
	 *
	 * @param none
	 * @return none
	 */
	function settings_panel_site() {

		?>
		<div id="site-categories-panel" class="wrap site-categories-wrap">
			<?php screen_icon(); ?>
			<h2><?php _ex("Site Categories", "Site Categories New Page Title", SITE_CATEGORIES_I18N_DOMAIN); ?></h2>

			<div id="poststuff" class="metabox-holder">
				<div id="post-body" class="">
					<div id="post-body-content" class="site-categories-metabox-holder-main">
						<p><?php _e('From the options below you can select the Site Categories which best describe your site. Also provide a Description which may be displayed on the Site Categories landing page.', SITE_CATEGORIES_I18N_DOMAIN); ?><?php
							if (isset($this->opts['landing_page_slug'])) {
								?> <a href="<?php echo $this->opts['landing_page_slug']; ?>" target="_blank"><?php 
									_e('View the Site Categories landing page.', SITE_CATEGORIES_I18N_DOMAIN); ?></a>>
								<?php
							}
						?></p>

						<form id="bcat_settings_form" action="<?php echo admin_url('options-general.php?page=bcat_settings_site'); ?>" method="post">
							<?php do_meta_boxes($this->_pagehooks['site-categories-settings-site'], 'normal', ''); ?>
							<input class="button-primary" type="submit" value="<?php _e('Save Settings', SITE_CATEGORIES_I18N_DOMAIN); ?>" />
						</form>
					</div>
				</div>
			</div>	
		</div>
		<script type="text/javascript">
			//<![CDATA[
			jQuery(document).ready( function($) {
				// close postboxes that should be closed
				$('.if-js-closed').removeClass('if-js-closed').addClass('closed');

				// postboxes setup
				postboxes.add_postbox_toggles('<?php echo $this->_pagehooks['site-categories-settings-site']; ?>');
			});
			//]]>
		</script>
		<?php
	}
	
	/**
	 * 
	 *
	 * @since 1.0.0
	 *
	 * @param none
	 * @return none
	 */
	function settings_main_categories_display_options_panel() {

		if (($this->opts['categories']['show_style'] != "accordion") && ($this->opts['categories']['show_style'] != "grid")) { 
			$display_grid_accordion_options = "display: none;";
		} else {
			$display_grid_accordion_options = "";
		}

		?>
		<table class="form-table">
		<tr class="form-field" >
			<th scope="row">
				<label for="site-categories-show-style"><?php _e('Display Style', SITE_CATEGORIES_I18N_DOMAIN); ?></label>
			</th>
			<td>
				<select id="site-categories-show-style" name="bcat[categories][show_style]">
					<option value="ul" <?php if ($this->opts['categories']['show_style'] == "ul") { 
						echo 'selected="selected" '; } ?>><?php _e('Unordered List (ul)', SITE_CATEGORIES_I18N_DOMAIN); ?></option>
					<option value="ol" <?php if ($this->opts['categories']['show_style'] == "ol") { 
						echo 'selected="selected" '; } ?>><?php _e('Ordered List (ol)', SITE_CATEGORIES_I18N_DOMAIN); ?></option>
					<option value="accordion" <?php if ($this->opts['categories']['show_style'] == "accordion") { 
						echo 'selected="selected" '; } ?>><?php _e('Accordion', SITE_CATEGORIES_I18N_DOMAIN); ?></option>
					<option value="grid" <?php if ($this->opts['categories']['show_style'] == "grid") { 
						echo 'selected="selected" '; } ?>><?php _e('Grid', SITE_CATEGORIES_I18N_DOMAIN); ?></option>
				</select>
			</td>
		</tr>

		<tr class="form-field site-categories-non-grid-options" <?php if ($this->opts['categories']['show_style'] == "grid") { echo ' style="display: none" '; } ?>>
			<th scope="row">
				<label for="site-categories-per-page"><?php _e('Categories per page', SITE_CATEGORIES_I18N_DOMAIN); ?></label>
			</th>
			<td>
				<input type="text" id="site-categories-per-page" name="bcat[categories][per_page]" 
					value="<?php echo $this->opts['categories']['per_page']; ?>" />
			</td>
		</tr>

		<tr class="form-field site-categories-grid-options" <?php if ($this->opts['categories']['show_style'] != "grid") { echo ' style="display: none" '; } ?>>
			<th scope="row">
				<label for="site-categories-per-page"><?php _e('Categories per page', SITE_CATEGORIES_I18N_DOMAIN); ?></label>
			</th>
			<td>
				<p><?php _e('Grid Options', SITE_CATEGORIES_I18N_DOMAIN); ?></p>
				<input type="text" class='' size="5" style="width: 50px" id="site-categories-show-style-grid-cols" name="bcat[categories][grid_cols]" 
					value="<?php echo intval($this->opts['categories']['grid_cols']); ?>" /> <label for="site-categories-show-style-grid-cols"><?php _e('Number of Columns', SITE_CATEGORIES_I18N_DOMAIN); ?></label><br />
				<input type="text" class='' size="5" style="width: 50px"  id="site-categories-show-style-grid-rows" name="bcat[categories][grid_rows]" 
						value="<?php echo intval($this->opts['categories']['grid_rows']); ?>" /> <label for="site-categories-show-style-grid-rows"><?php _e('Number of Rows', SITE_CATEGORIES_I18N_DOMAIN); ?></label><br />
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="site-categories-orderby"><?php _e('Order by', SITE_CATEGORIES_I18N_DOMAIN); ?></label>
			</th>
			<td>
				<p><?php _e('This order by option controls how the listed Site Categories will be ordered on the listing page.', 
					SITE_CATEGORIES_I18N_DOMAIN); ?></p>

				<select id="site-categories-orderby" name="bcat[categories][orderby]">
					<option value="name" <?php if ($this->opts['categories']['orderby'] == "name") { 
						echo 'selected="selected" '; } ?>><?php _e('Name', SITE_CATEGORIES_I18N_DOMAIN); ?></option>
					<option value="id" <?php if ($this->opts['categories']['orderby'] == "id") { 
						echo 'selected="selected" '; } ?>><?php _e('ID', SITE_CATEGORIES_I18N_DOMAIN); ?></option>
					<option value="none" <?php if ($this->opts['categories']['orderby'] == "none") { 
						echo 'selected="selected" '; } ?>><?php _e('None', SITE_CATEGORIES_I18N_DOMAIN); ?></option>
				</select>

				<select id="site-categories-order" name="bcat[categories][order]">
					<option value="ASC" <?php if ($this->opts['categories']['order'] == "ASC") { 
						echo 'selected="selected" '; } ?>><?php _e('ASC', SITE_CATEGORIES_I18N_DOMAIN); ?></option>
					<option value="DESC" <?php if ($this->opts['categories']['order'] == "DESC") { 
						echo 'selected="selected" '; } ?>><?php _e('DESC', SITE_CATEGORIES_I18N_DOMAIN); ?></option>
				</select>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="site-categories-hide-empty"><?php _e('Hide Empty Categories', SITE_CATEGORIES_I18N_DOMAIN); ?></label>
			</th>
			<td>
				<input type="radio" name="bcat[categories][hide_empty]" id="category-hide-empty-yes" value="1" 
				<?php if ($this->opts['categories']['hide_empty'] == "1") { echo ' checked="checked" '; }?> /> <label for="category-hide-empty-yes"><?php _e('Yes', SITE_CATEGORIES_I18N_DOMAIN) ?></label><br /><input type="radio" name="bcat[categories][hide_empty]" id="category-hide-empty-no" value="0" 
				<?php if ($this->opts['categories']['hide_empty'] == "0") { echo ' checked="checked" '; }?>/> <label for="category-hide-empty-no"><?php _e('No', SITE_CATEGORIES_I18N_DOMAIN); ?></label>
				
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="site-categories-show-counts"><?php _e('Show counts', SITE_CATEGORIES_I18N_DOMAIN); ?></label>
			</th>
			<td>
				<div style="float: left; width: 100px;">

					<p class="site-categories-accordion-options site-categories-grid-options" style="<?php echo $display_grid_accordion_options; ?>"><?php _e('Parents',  SITE_CATEGORIES_I18N_DOMAIN); ?></p>
				
					<input type="radio" name="bcat[categories][show_counts]" id="category-show-counts-yes" value="1" 
					<?php if ($this->opts['categories']['show_counts'] == "1") { echo ' checked="checked" '; }?> /> <label 
					for="category-show-counts-yes"><?php _e('Yes', SITE_CATEGORIES_I18N_DOMAIN) ?></label><br />
					
					<input type="radio" name="bcat[categories][show_counts]" id="category-show-counts-no" value="0" 
					<?php if ($this->opts['categories']['show_counts'] == "0") { echo ' checked="checked" '; }?>/> <label 
					for="category-show-counts-no"><?php _e('No', SITE_CATEGORIES_I18N_DOMAIN); ?></label>
				</div>
				
				<div style="float: left; width: 100px; margin-left: 10px; <?php echo $display_grid_accordion_options; ?>" 
						class="site-categories-accordion-options site-categories-grid-options">
				
					<p><?php _e('Children',  SITE_CATEGORIES_I18N_DOMAIN); ?></p>
				
					<input type="radio" name="bcat[categories][show_counts_children]" id="category-show-counts-children-yes" value="1" 
					<?php if ($this->opts['categories']['show_counts_children'] == "1") { echo ' checked="checked" '; }?> /> 
					<label for="category-show-counts-children-yes"><?php _e('Yes', SITE_CATEGORIES_I18N_DOMAIN) ?></label><br />
					
					<input type="radio" name="bcat[categories][show_counts_children]" id="category-show-counts-children-no" value="0" 
					<?php if ($this->opts['categories']['show_counts_children'] == "0") { echo ' checked="checked" '; }?>/> <label 
					for="category-show-counts-children-no"><?php _e('No', SITE_CATEGORIES_I18N_DOMAIN); ?></label>

				</div>
				
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="site-categories-show-description"><?php _e('Show Category Description', SITE_CATEGORIES_I18N_DOMAIN); ?></label>
			</th>
			<td>
				<div style="float: left; width: 100px;">

					<p class="site-categories-accordion-options site-categories-grid-options" style="<?php echo $display_grid_accordion_options; ?>"><?php _e('Parents',  SITE_CATEGORIES_I18N_DOMAIN); ?></p>

					<input type="radio" name="bcat[categories][show_description]" id="category-show-description-yes" value="1" 
					<?php if ($this->opts['categories']['show_description'] == "1") { echo ' checked="checked" '; }?> /> <label 
					for="category-show-description-yes"><?php _e('Yes', SITE_CATEGORIES_I18N_DOMAIN) ?></label><br />
				
					<input type="radio" name="bcat[categories][show_description]" id="category-show-description-no" value="0" <?php 
					if ($this->opts['categories']['show_description'] == "0") { echo ' checked="checked" '; }?>/> <label 
					for="category-show-description-no"><?php _e('No', SITE_CATEGORIES_I18N_DOMAIN); ?></label>

				</div>
				
				<div style="float: left; width: 100px; margin-left: 10px; <?php echo $display_grid_accordion_options; ?>" 
					class="site-categories-accordion-options site-categories-grid-options">

					<p><?php _e('Children',  SITE_CATEGORIES_I18N_DOMAIN); ?></p>
					<input type="radio" name="bcat[categories][show_description_children]" id="category-show-description-children-yes" value="1" 
					<?php if ($this->opts['categories']['show_description_children'] == "1") { echo ' checked="checked" '; }?> /> <label 
					for="category-show-description-children-yes"><?php _e('Yes', SITE_CATEGORIES_I18N_DOMAIN) ?></label><br />
				
					<input type="radio" name="bcat[categories][show_description_children]" id="category-show-description-children-no" value="0" <?php 
					if ($this->opts['categories']['show_description_children'] == "0") { echo ' checked="checked" '; }?>/> <label 
					for="category-show-description-children-no"><?php _e('No', SITE_CATEGORIES_I18N_DOMAIN); ?></label>

				</div>

			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="site-categories-icons"><?php _e('Show icons', SITE_CATEGORIES_I18N_DOMAIN); ?></label>
			</th>
			<td>
				<div style="float: left; width: 100px;">

					<p class="site-categories-accordion-options site-categories-grid-options" style="<?php echo $display_grid_accordion_options; ?>"><?php _e('Parents',  SITE_CATEGORIES_I18N_DOMAIN); ?></p>

					<input type="radio" name="bcat[categories][icon_show]" id="category-icons-show-yes" value="1" 
					<?php if ($this->opts['categories']['icon_show'] == "1") { echo ' checked="checked" '; }?> /> <label 
					for="category-icons-show-yes"><?php _e('Yes', SITE_CATEGORIES_I18N_DOMAIN) ?></label><br />
					
					<input type="radio" name="bcat[categories][icon_show]" id="category-icons-show-no" value="0" 
					<?php if ($this->opts['categories']['icon_show'] == "0") { echo ' checked="checked" '; }?>/> <label 
					for="category-icons-show-no"><?php _e('No', SITE_CATEGORIES_I18N_DOMAIN); ?></label>

				</div>
				<div style="float: left; width: 100px; margin-left: 10px; <?php echo $display_grid_accordion_options; ?>" 
					class="site-categories-accordion-options site-categories-grid-options">

					<p><?php _e('Children',  SITE_CATEGORIES_I18N_DOMAIN); ?></p>
				
					<input type="radio" name="bcat[categories][icon_show_children]" id="category-icons-show-children-yes" value="1" 
					<?php if ($this->opts['categories']['icon_show_children'] == "1") { echo ' checked="checked" '; }?> /> <label 
					for="category-icons-show-children-yes"><?php _e('Yes', SITE_CATEGORIES_I18N_DOMAIN) ?></label><br />
					
					<input type="radio" name="bcat[categories][icon_show_children]" id="category-icons-show-children-no" value="0" 
					<?php if ($this->opts['categories']['icon_show_children'] == "0") { echo ' checked="checked" '; }?>/> <label 
					for="category-icons-show-children-no"><?php _e('No', SITE_CATEGORIES_I18N_DOMAIN); ?></label>

				</div>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="site-categories-icons"><?php _e('Icon size', SITE_CATEGORIES_I18N_DOMAIN); ?></label>
			</th>
			<td>
				<div style="float: left; width: 100px;">

					<p class="site-categories-accordion-options site-categories-grid-options" style="<?php echo $display_grid_accordion_options; ?>"><?php _e('Parents',  SITE_CATEGORIES_I18N_DOMAIN); ?></p>
					<input type="text" class='' size="5" name="bcat[categories][icon_size]" 
						value="<?php echo intval($this->opts['categories']['icon_size']); ?>" />px  <?php _e('square', SITE_CATEGORIES_I18N_DOMAIN); ?>
					<p class="description"><?php _e('default is 32px', SITE_CATEGORIES_I18N_DOMAIN); ?></p>

				</div>
				<div style="float: left; width: 100px; margin-left: 10px; <?php echo $display_grid_accordion_options; ?>" 
					class="site-categories-accordion-options site-categories-grid-options">

					<p ><?php _e('Children',  SITE_CATEGORIES_I18N_DOMAIN); ?></p>

					<input type="text" class='' size="5" name="bcat[categories][icon_size_children]" 
						value="<?php echo intval($this->opts['categories']['icon_size_children']); ?>" />px  <?php _e('square', SITE_CATEGORIES_I18N_DOMAIN); ?>
					<p class="description"><?php _e('default is 32px', SITE_CATEGORIES_I18N_DOMAIN); ?></p>

				</div>
			</td>
		</tr>		

		<?php
			if ((isset($this->opts['categories']['default_icon_id'])) && (intval($this->opts['categories']['default_icon_id']))) {
				$bcat_image_id = intval($this->opts['categories']['default_icon_id']);
			} else {
				$bcat_image_id = 0;
			}
		?>
		<tr>
			<th scope="row" valign="top"><label for="upload_image"><?php _ex('Default Category Image', 'Category Image', SITE_CATEGORIES_I18N_DOMAIN); ?></label></th>
			<td>
				<p class="description"><?php _e('Upload or select an image to used as the default category icons. Ensure it is at least as large as the icon size specified above. A square version of this image will be auto generated.', SITE_CATEGORIES_I18N_DOMAIN) ?></p>
				<input type="hidden" id="bcat_image_id" value="<?php echo $bcat_image_id; ?>" name="bcat[categories][default_icon_id]" />
				<input id="bcat_image_upload" class="button-secondary" type="button" value="<?php _e('Select Image', SITE_CATEGORIES_I18N_DOMAIN); ?>" <?php
					if ($bcat_image_id) { echo ' style="display: none;" '; }; ?> />
				<input id="bcat_image_remove" class="button-secondary" type="button" value="<?php _e('Remove Image', SITE_CATEGORIES_I18N_DOMAIN); ?>" <?php
					if (!$bcat_image_id) { echo ' style="display: none;" '; }; ?> />
				<br />
				<?php
					if ((isset($this->opts['categories']['default_icon_id'])) && (intval($this->opts['categories']['default_icon_id']))) {
						
						$image_src 	= wp_get_attachment_image_src(intval($this->opts['categories']['default_icon_id']), array(100, 100));
						if (!$image_src) {
							$image_src[0] = "#";
						}
					} else {
						$bcat_image_default_src = $this->get_default_category_icon_url();
						$image_src[0] = $bcat_image_default_src;
					}
					?>
					<img id="bcat_image_src" src="<?php echo $image_src[0]; ?>" alt="" style="margin-top: 10px; max-width: 300px; max-height: 300px" 
						rel="<?php echo $bcat_image_default_src; ?>"/>
					<?php
				?></p>
			</td>
		</tr>
		
		</table>
		<?php
	}
	
	/**
	 * 
	 *
	 * @since 1.0.0
	 *
	 * @param none
	 * @return none
	 */
	function settings_main_sites_display_options_panel() {
		?>
		<table class="form-table">

		<tr class="form-field" >
			<th scope="row">
				<label for="site-categories-site-show-style"><?php _e('Display Style', SITE_CATEGORIES_I18N_DOMAIN); ?></label>
			</th>
			<td>
				<select id="site-categories-site-show-style" name="bcat[sites][show_style]">
					<option value="ul" <?php if ($this->opts['sites']['show_style'] == "ul") { 
						echo 'selected="selected" '; } ?>><?php _e('Unordered List (ul)', SITE_CATEGORIES_I18N_DOMAIN); ?></option>
					<option value="ol" <?php if ($this->opts['sites']['show_style'] == "ol") { 
						echo 'selected="selected" '; } ?>><?php _e('Ordered List (ol)', SITE_CATEGORIES_I18N_DOMAIN); ?></option>
				</select>
			</td>
		</tr>
		<tr class="form-field" >
			<th scope="row">
				<label for="site-categories-sites-per-page"><?php _e('Sites per page', SITE_CATEGORIES_I18N_DOMAIN); ?></label>
			</th>
			<td>
				<input type="text" id="site-categories-sites-per-page" name="bcat[sites][per_page]" 
				value="<?php echo $this->opts['sites']['per_page']; ?>" />
			</td>
		</tr>

<?php /* ?>
		<tr>
			<th scope="row">
				<label for="site-categories-site-orderby"><?php _e('Order By', SITE_CATEGORIES_I18N_DOMAIN); ?></label>
			</th>
			<td>
				<p><?php _e('This order by option controls how the listed Site Categories will be ordered on the listing page.', 
					SITE_CATEGORIES_I18N_DOMAIN); ?></p>
				<select id="site-categories-site-orderby" name="bcat[sites][orderby]">
					<option value="name" <?php if ($this->opts['sites']['orderby'] == "name") { 
						echo 'selected="selected" '; } ?>><?php _e('Name', SITE_CATEGORIES_I18N_DOMAIN); ?></option>
					<option value="id" <?php if ($this->opts['sites']['orderby'] == "id") { 
						echo 'selected="selected" '; } ?>><?php _e('ID', SITE_CATEGORIES_I18N_DOMAIN); ?></option>
					<option value="none" <?php if ($this->opts['sites']['orderby'] == "none") { 
						echo 'selected="selected" '; } ?>><?php _e('None', SITE_CATEGORIES_I18N_DOMAIN); ?></option>
				</select>
			</td>
		</tr>
<?php */ ?>

		<tr>
			<th scope="row">
				<label for="site-categories-site-show-description"><?php _e('Show Site Description', SITE_CATEGORIES_I18N_DOMAIN); ?></label>
			</th>
			<td>
				<input type="radio" name="bcat[sites][show_description]" id="category-site-show-description-yes" value="1" 
				<?php if ($this->opts['sites']['show_description'] == "1") { echo ' checked="checked" '; }?> /> <label 
				for="category-site-show-description-yes"><?php _e('Yes', SITE_CATEGORIES_I18N_DOMAIN) ?></label><br />
				
				<input type="radio" name="bcat[sites][show_description]" id="category-site-show-description-no" value="0" 
				<?php if ($this->opts['sites']['show_description'] == "0") { echo ' checked="checked" '; }?>/> <label 
				for="category-site-show-description-no"><?php _e('No', SITE_CATEGORIES_I18N_DOMAIN); ?></label>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="site-categories-show-sites-icons"><?php _e('Show icons', SITE_CATEGORIES_I18N_DOMAIN); ?></label>
			</th>
			<td>
				<?php
					if (function_exists('get_blog_avatar')) {
						?>
						<input type="radio" name="bcat[sites][icon_show]" id="site-categories-show-sites-icons-show-yes" value="1" 
						<?php if ($this->opts['sites']['icon_show'] == "1") { echo ' checked="checked" '; } ?>/> <label 
							for="site-categories-show-sites-icons-show-yes"><?php _e('Yes', SITE_CATEGORIES_I18N_DOMAIN) ?></label><br />
						
						<input type="radio" name="bcat[sites][icon_show]" id="site-categories-show-sites-icons-show-no" value="0" 
						<?php if ($this->opts['sites']['icon_show'] == "0") { echo ' checked="checked" '; } ?> /> <label 
							for="site-categories-show-sites-icons-show-no"><?php _e('No', SITE_CATEGORIES_I18N_DOMAIN); ?></label>						
						<?php
					} else {
						?><p><?php echo sprintf(__('Install the %1$sAvatars%2$s plugin to show Site icons.', SITE_CATEGORIES_I18N_DOMAIN),  
							'<a href="http://premium.wpmudev.org/project/avatars/" target="_blank">', 
							'</a>'); ?></p><?php
					}
				?>

			</td>
		</tr>
		<?php if (function_exists('get_blog_avatar')) { ?>
		<tr>
			<th scope="row">
				<label for="site-categories-site-icon-size"><?php _e('Icon size', SITE_CATEGORIES_I18N_DOMAIN); ?></label>
			</th>
			<td>
				<input type="text" class='' size="5" id="site-categories-site-icon-size" name="bcat[sites][icon_size]" 
					value="<?php echo intval($this->opts['sites']['icon_size']); ?>" />px  <?php _e('square', SITE_CATEGORIES_I18N_DOMAIN); ?>
				<p class="description"><?php _e('default is 32px', SITE_CATEGORIES_I18N_DOMAIN); ?></p>
			</td>
		</tr>		
		<?php } ?>
		</table>
		<?php
	}
	
	/**
	 * 
	 *
	 * @since 1.0.1
	 *
	 * @param none
	 * @return none
	 */
	function settings_main_sites_signup_form_options_panel() {

		//echo "opts<pre>"; print_r($this->opts); echo "</pre>";

		?>
		<p><?php _e('These options let you control the Site Categories information displayed on the front-end New Site form.', SITE_CATEGORIES_I18N_DOMAIN); ?></p>
		<table class="form-table">

		<tr>
			<th scope="row">
				<label for="site-categories-signup-show"><?php _e('Show Site Categories section', SITE_CATEGORIES_I18N_DOMAIN); ?></label>
			</th>
			<td>
				<input type="radio" name="bcat[sites][signup_show]" id="site-categories-signup-show-yes" value="1" 
				<?php if ($this->opts['sites']['signup_show'] == "1") { echo ' checked="checked" '; } ?> /> <label 
				for="site-categories-signup-show-yes"><?php _e('Yes', SITE_CATEGORIES_I18N_DOMAIN) ?></label><br />
				
				<input type="radio" name="bcat[sites][signup_show]" id="site-categories-signup-show-no" value="0" 
				<?php if ($this->opts['sites']['signup_show'] == "0") { echo ' checked="checked" '; } ?>/> <label 
				for="site-categories-signup-show-no"><?php _e('No', SITE_CATEGORIES_I18N_DOMAIN); ?></label>
				
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="site-categories-signup-category-required"><?php _e('Site Categories Selection Required', SITE_CATEGORIES_I18N_DOMAIN); ?></label>
			</th>
			<td>
				<input type="radio" name="bcat[sites][signup_category_required]" id="site-categories-signup-category-required-yes" value="1" 
				<?php if ($this->opts['sites']['signup_category_required'] == "1") { echo ' checked="checked" '; } ?> /> <label 
				for="site-categories-signup-category-required-yes"><?php _e('Yes', SITE_CATEGORIES_I18N_DOMAIN) ?></label><br />
				
				<input type="radio" name="bcat[sites][signup_category_required]" id="site-categories-signup-category-required-no" value="0" 
				<?php if ($this->opts['sites']['signup_category_required'] == "0") { echo ' checked="checked" '; } ?>/> <label 
				for="site-categories-signup-category-required-no"><?php _e('No', SITE_CATEGORIES_I18N_DOMAIN); ?></label>
				
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="site-categories-signup-category-label"><?php _e('Label for Site Categories Dropdowns', SITE_CATEGORIES_I18N_DOMAIN); ?></label>
			</th>
			<td>
				<input type="text" class='widefat' id="site-categories-signup-category-label" name="bcat[sites][signup_category_label]" 
					value="<?php echo stripslashes($this->opts['sites']['signup_category_label']); ?>" />
					<p class="description"><?php _e("The label is shown above the number of category dropdowns", SITE_CATEGORIES_I18N_DOMAIN); ?></p>					
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="site-categories-signup-description-required"><?php _e('Description is Required', SITE_CATEGORIES_I18N_DOMAIN); ?></label>
			</th>
			<td>
				<input type="radio" name="bcat[sites][signup_description_required]" id="site-categories-signup-description-required-yes" value="1" 
				<?php if ($this->opts['sites']['signup_description_required'] == "1") { echo ' checked="checked" '; }?> /> <label
				 for="site-categories-signup-description-required-yes"><?php _e('Yes', SITE_CATEGORIES_I18N_DOMAIN) ?></label><br />
				
				<input type="radio" name="bcat[sites][signup_description_required]" id="site-categories-signup-description-required-no" value="0" 
				<?php if ($this->opts['sites']['signup_description_required'] == "0") { echo ' checked="checked" '; }?>/> <label
				 for="site-categories-signup-description-required-no"><?php _e('No', SITE_CATEGORIES_I18N_DOMAIN); ?></label>
				
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="site-categories-signup-description-label"><?php _e('Label for Site Categories Description', SITE_CATEGORIES_I18N_DOMAIN); ?></label>
			</th>
			<td>
				<input type="text" class='widefat' id="site-categories-signup-description-label" name="bcat[sites][signup_description_label]" 
					value="<?php echo stripslashes($this->opts['sites']['signup_description_label']); ?>" />
					<p class="description"><?php _e("The label is shown above the site description", SITE_CATEGORIES_I18N_DOMAIN); ?></p>					
			</td>
		</tr>

		</table>
		<?php
	}
	
	/**
	 * 
	 *
	 * @since 1.0.0
	 *
	 * @param none
	 * @return none
	 */
	function settings_main_admin_display_options_panel() {
		?>
		<table class="form-table">
		<tr class="form-field" >
			<th scope="row">
				<label for="site-categories-landing-page"><?php _e('Select Landing Page', SITE_CATEGORIES_I18N_DOMAIN); ?></label>
			</th>
			<td>
				<?php
					if (isset($this->opts['landing_page_id'])) {
						$landing_page_id = intval($this->opts['landing_page_id']);
					} else {
						$landing_page_id = 0;
					}
				?>
				<input type="hidden" name="bcat[landing_page_id_org]" id="landing_page_id_org" value="<?php echo $landing_page_id ?>" />
				<?php	

					wp_dropdown_pages( array( 
							'name' 				=> 'bcat[landing_page_id]', 
							'id'				=> 'site-categories-landing-page',
							'echo' 				=> 1, 
							'show_option_none' 	=> __( '&mdash; Select &mdash;' ), 
							'option_none_value' => '0', 
							'selected' 			=>  $landing_page_id
						)
					);

					if ($this->opts['landing_page_id']) {
						?><p class="description"><?php _e('The Site Categories listing will be appended to the selected page:', 
							SITE_CATEGORIES_I18N_DOMAIN); ?> <a href="<?php echo get_permalink($this->opts['landing_page_id']); ?>" 
								target="blank"><?php _e('View Listing', SITE_CATEGORIES_I18N_DOMAIN); ?></a></p><?php 
					}
				?>
			</td>
		</tr>
		</table>
		<?php		
	}
	
	/**
	 * 
	 *
	 * @since 1.0.2
	 *
	 * @param none
	 * @return none
	 */
	function settings_main_admin_display_selection_options_panel() {
		?>
		<table class="form-table">
		<tr>
			<th scope="row">
				<label for="site-categories-sites-category-limit"><?php _e('Number of categories per site', SITE_CATEGORIES_I18N_DOMAIN); ?></label>
			</th>
			<td>
				<p class="description"><?php _e('This option lets you limit the number of Site Categories available to the site. This option adds a number of dropdown form elements on the Settings General page where the admin can set the categories for their site.', SITE_CATEGORIES_I18N_DOMAIN); ?></p>
				<input type="text" class='widefat' id="site-categories-sites-category-limit" name="bcat[sites][category_limit]" 
					value="<?php echo intval($this->opts['sites']['category_limit']); ?>" />
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="site-categories-sites-category-limit-prosites"><?php _e('Pro Sites', SITE_CATEGORIES_I18N_DOMAIN); ?></label>
			</th>
			<td>		
				<?php
					if (function_exists('is_pro_user')) {
				
						// If not a Pro User (whatever that is). Then we do not show the Pro Site section
						if (is_pro_user(get_current_user_id())) {

							$levels = (array)get_site_option('psts_levels');
							if ($levels) {
								?>
								<p class="description"><?php _e('You can offer your Pro Sites level more site categories selections',
								 SITE_CATEGORIES_I18N_DOMAIN); ?></p>
								<ul style="float: left; width: 100%;">
								<?php
									$level_value = '';
									foreach($levels as $level_idx => $level) {
										if (isset($this->opts['sites']['prosites_category_limit'][$level_idx])) {
											$level_value = intval($this->opts['sites']['prosites_category_limit'][$level_idx]);
										} 
								
										if ($level_value == 0)
											$level_value = '';
								
										?><li><input type="text" id="bcat-sites-prosites-category-limit-<?php echo $level_idx; ?>" width="40%" 
											value="<?php echo $level_value; ?>" 
											name="bcat[sites][prosites_category_limit][<?php echo $level_idx; ?>]" /> <label for="bcat-sites-prosites-category-limit-<?php echo $level_idx; ?>"><?php echo $level['name'] ?></label></li><?php

									}
								?>
								</ul>
								<?php
							}
						}
					} else {
						?><p class=""><?php echo sprintf(__('If you install the %1$sPro Sites%2$s plugin, you can offer your Pro Sites levels more categories selections.', SITE_CATEGORIES_I18N_DOMAIN),
						'<a href="http://premium.wpmudev.org/project/pro-sites/" target="_blank">', '</a>'); ?></p><?php
					}
				?>
			</td>
		</tr>

		<?php
			if ((isset($this->opts['sites']['category_excludes'])) 
			 && (!empty($this->opts['sites']['category_excludes'])) 
			 && (count($this->opts['sites']['category_excludes']))) {
				$cat_excludes = implode(', ', $this->opts['sites']['category_excludes']);
			} else {
				$cat_excludes = '';
			}
		?>
		<tr>
			<th scope="row">
				<label for="site-categories-sites-category-exclude"><?php _e('Excluded Categories', SITE_CATEGORIES_I18N_DOMAIN); ?></label>
			</th>		
			<td>
				<p class="description"><?php _e('Enter a comma separated list of category IDs. These categories will be excluded dropdown selection on the new site signup page as well as the blog Settings > Site Categories page.', SITE_CATEGORIES_I18N_DOMAIN); ?></p>
				<input type="text" class='widefat' id="site-categories-sites-category-excludes" name="bcat[sites][category_excludes]" 
					value="<?php echo $cat_excludes; ?>" />
			</td>
		</tr>
		
<?php /* ?>
		<tr>
			<th scope="row">
				<label for="site-categories-signup-category-minimum"><?php _e('Minimum Selected Site Categories', SITE_CATEGORIES_I18N_DOMAIN); ?></label>
			</th>
			<td>
				<input type="text" class='widefat' id="site-categories-signup-category-minimum" name="bcat[sites][signup_category_minimum]" 
					value="<?php echo intval($this->opts['sites']['signup_category_minimum']); ?>" />
					<p class="description"><?php _e("The minimum number of site categories to be set. This value should be between 1 and the value of the 'Number of categories per site' item", SITE_CATEGORIES_I18N_DOMAIN); ?></p>

			</td>
		</tr>
<?php */ ?>
		<tr>
			<th scope="row">
				<label for="site-categories-signup-category-parent-selectable-yes"><?php _e('Category parents selectable', SITE_CATEGORIES_I18N_DOMAIN); ?></label>
			</th>
			<td>
				<p><?php _e('When displaying Site Categories on the Landing page using the <strong>Grid</strong> or <strong>Accordion</strong> styles. It is advisable to set this option to <strong>no</strong>. This option controls the dropdown categories options on the New Site form as well as the Settings page within wp-admin.'); ?></p>
				<input type="radio" name="bcat[sites][signup_category_parent_selectable]" id="site-categories-signup-category-parent-selectable-yes" value="1" 
				<?php if ($this->opts['sites']['signup_category_parent_selectable'] == "1") { echo ' checked="checked" '; }?> /> <label
				 for="site-categories-signup-category-parent-selectable-yes"><?php _e('Yes', SITE_CATEGORIES_I18N_DOMAIN) ?></label><br />
				
				<input type="radio" name="bcat[sites][signup_category_parent_selectable]" id="site-categories-signup-category-parent-selectable-no" value="0" 
				<?php if ($this->opts['sites']['signup_category_parent_selectable'] == "0") { echo ' checked="checked" '; }?>/> <label
				 for="site-categories-signup-category-parent-selectable-no"><?php _e('No', SITE_CATEGORIES_I18N_DOMAIN); ?></label>
				
			</td>
		</tr>
		
		</table>
		<?php		
	}

	
	/**
	 * 
	 *
	 * @since 1.0.0
	 *
	 * @param none
	 * @return none
	 */
	function settings_site_select_categories_panel() {
		
		global $wpdb, $psts, $current_site, $current_blog;
		
		if (function_exists('is_pro_user')) {
			$site_level = $psts->get_level($wpdb->blogid);
			$levels = (array)get_site_option('psts_levels');			
			
			if (($levels) && (isset($levels[$site_level]))
			 && (isset($this->opts['sites']['prosites_category_limit'][$site_level]))) {
				$blog_category_limit = intval($this->opts['sites']['prosites_category_limit'][$site_level]);
				?><p><?php _e("Pro Sites Level:", SITE_CATEGORIES_I18N_DOMAIN); ?> <?php echo $levels[$site_level]['name']; ?></p><?php
			} else {
				if (isset($this->opts['sites']['category_limit']))
					$blog_category_limit = intval($this->opts['sites']['category_limit']);
				else
					$blog_category_limit = 1;				
			}

		} else {
			if (isset($this->opts['sites']['category_limit']))
				$blog_category_limit = intval($this->opts['sites']['category_limit']);
			else
				$blog_category_limit = 1;
		}

		if (($blog_category_limit > 100)	|| ($blog_category_limit < 1))
			$blog_category_limit = 1;

		//$current_site = $wpdb->blogid;
		//echo "current_site<pre>"; print_r($current_site); echo "</pre>";
		//echo "current_blog<pre>"; print_r($current_blog); echo "</pre>";
		
		//echo "wpdb->prefix=[". $wpdb->prefix ."]<br />";
		
		switch_to_blog( $current_site->blog_id );
		
		$site_categories = wp_get_object_terms($current_blog->blog_id, SITE_CATEGORIES_TAXONOMY);
		$cat_excludes = '';
		
		if ((is_multisite()) && (!is_super_admin())) {
			if ((isset($this->opts['sites']['category_excludes'])) 
			 && (!empty($this->opts['sites']['category_excludes']))
			 && (count($this->opts['sites']['category_excludes']))) {
				$cat_excludes = implode(', ', $this->opts['sites']['category_excludes']);
			} else {
				$this->opts['sites']['category_excludes'] = array();
			}
		}
		
		$cat_counter = 0;
		?><ol><?php
		while(true) {
			
			if (isset($site_categories[$cat_counter])) {
				$cat_selected = $site_categories[$cat_counter]->term_id;
			} else {
				$cat_selected = -1;
			}
									
			?><li><?php 
				$cat_ecluded = false;
				if ((is_multisite()) && (!is_super_admin())) {
					if (array_search($cat_selected, $this->opts['sites']['category_excludes']) !== false) {
						echo $site_categories[$cat_counter]->name ." - Managed by Super Admin";
						$cat_ecluded = true;
						?><input type="hidden" name="bcat_site_categories[<?php echo $cat_counter; ?>]" 
							value="<?php echo $site_categories[$cat_counter]->term_id; ?>" /><?php
					}
				} 
				if ($cat_ecluded == false) {
					$bcat_args = array(
						'taxonomy'			=> 	SITE_CATEGORIES_TAXONOMY,
						'hierarchical'		=>	true,
						'hide_empty'		=>	false,
						'exclude'			=>	$cat_excludes,
						'show_option_none'	=>	__('None Selected', SITE_CATEGORIES_I18N_DOMAIN), 
						'name'				=>	'bcat_site_categories['. $cat_counter .']',
						'class'				=>	'bcat_category',
						'selected'			=>	$cat_selected,
						'orderby'			=>	'name',
						'order'				=>	'ASC'
					);
			
					if ($this->opts['sites']['signup_category_parent_selectable'] == 1)
						wp_dropdown_categories( $bcat_args );
					else
						$this->wp_dropdown_categories( $bcat_args );
				}
			?></li><?php
		
			$cat_counter += 1;
			if ($cat_counter >= $blog_category_limit) 
				break;
		}			
		?></ol><?php
		
		restore_current_blog();
	}
	
	/**
	 * 
	 *
	 * @since 1.0.0
	 *
	 * @param none
	 * @return none
	 */
	function settings_site_description_panel() {
		
		$bact_site_description = get_option('bact_site_description');

		?>
		<label for="bcat_site_description"><?php _e('Enter a Site Description to be used on the Landing page.', SITE_CATEGORIES_I18N_DOMAIN); ?></label><br />
		<textarea name="bcat_site_description" style="width:100%;" cols="30" rows="10" id="bcat_site_description"><?php 
			echo stripslashes($bact_site_description); ?></textarea>
		<?php
	}
	
	function dummy_body($content) {
		$content .= "This is a dummy line of text.";
		return $content;
	}
	
	/**
	 * 
	 *
	 * @since 1.0.0
	 *
	 * @param none
	 * @return none
	 */
	function process_categories_body ($content) {

		global $post;

		if (is_admin()) return $content;
		if (!in_the_loop()) return $content;
		
		$this->load_config();
		//echo "opts<pre>"; print_r($this->opts); echo "</pre>";

		$data = array();
		
		// We get the bcat options. This 'should' contain the variable 'landing_page_id' is the admin properly set things up
		if ((!isset($this->opts['landing_page_id'])) || (!intval($this->opts['landing_page_id'])))
			$opts['landing_page_id'] = 0; 
		
		if ($post->ID != intval($this->opts['landing_page_id'])) return $content;
		
		// Remove our own filter. Since we are here we should not need it. Plus in case other process call the content filters. 
		//remove_filter('the_content', array($this, 'process_categories_body'), 99);
		
		$category = get_query_var('category_name');		
		if ($category) {

			$data['term'] = get_term_by('slug', $category, SITE_CATEGORIES_TAXONOMY);
			if (( is_wp_error( $data['term'] ) ) || (!$data['term'])) {

				// Here is some fuzzy logic. The query_var 'category_name' is the first item off the page slug as in /page-slug/category-name/page-number/
				// So we need to check if it is a real intval (3, 6, 12, etc.) then we assume we don't have a category and we are viewing the top-level page
				// list of blog categories. IF we do have a valid category-name then the next query_var is the page-number

				$category_int = intval($category);
				if (($category == $category_int) && ($category_int != 0)) {
					$data['current_page'] = $category_int;
					$category = '';
				} else {

					$data['current_page']  = get_query_var('page');
					if (!$data['current_page']) {
						$data['current_page'] = get_query_var('start_at');
					}
				}
			} else {
				$data['current_page'] = get_query_var('page');

				if (!$data['current_page']) {
					$data['current_page'] = get_query_var('start_at');
				}
			}
		}

		if ((!isset($data['current_page'])) || ($data['current_page'] == 0))
			$data['current_page'] = 1;
		
		if ($category) {

			$args = $this->opts['sites'];

			$data['category']	= $category;

			$sites = $this->get_taxonomy_sites($data['term']->term_id);
			if (count($sites) < $args['per_page']) {
				$data['sites'] = $sites;

			} else {

				$data['offset'] 		= intval($args['per_page']) * (intval($data['current_page'])-1); 
				$data['sites'] 			= array_slice($sites, $data['offset'], $args['per_page'], true);
				$data['total_pages'] 	= ceil(count($sites)/intval($args['per_page']));
												
				if (intval($data['current_page']) > 1) {

					$data['prev'] = array();
					$data['prev']['page_number'] = intval($data['current_page']) - 1;

					if ((isset($this->opts['landing_page_rewrite'])) && ($this->opts['landing_page_rewrite'] == true)) {

						$data['prev']['link_url'] = trailingslashit($this->opts['landing_page_slug']) . $data['term']->slug 
							. '/' . $data['prev']['page_number'];

					} else {

						$data['prev']['link_url'] = $this->opts['landing_page_slug'] . '&amp;category_name='. $data['term']->slug 
							.'&amp;start_at=' . $data['prev']['page_number'];			

					}

					$data['prev']['link_label'] = __('Previous page', SITE_CATEGORIES_I18N_DOMAIN);
				}
				
				if ($data['current_page'] < $data['total_pages']) {

					$data['next'] = array();
					
					$data['next']['page_number'] = $data['current_page'] + 1;
					
					if ((isset($this->opts['landing_page_rewrite'])) && ($this->opts['landing_page_rewrite'] == true)) {

						$data['next']['link_url'] = trailingslashit($this->opts['landing_page_slug']) . $data['term']->slug 
							. '/' . $data['next']['page_number'];

					} else {

						$data['next']['link_url'] = $this->opts['landing_page_slug'] .'&amp;category_name='. $data['term']->slug 
							.'&amp;start_at=' . $data['next']['page_number'];			

					}

					$data['next']['link_label'] = __('Next page', SITE_CATEGORIES_I18N_DOMAIN);
				}
			}
			
			if (!function_exists('get_blog_avatar')) {
				$args['icon_show'] = false;
			} else {
				$default_icon_src = $this->get_default_category_icon_url();
			}

			if (count($data['sites'])) {

				foreach($data['sites'] as $idx => $site) {

					$data['sites'][$idx]->bact_site_description = get_blog_option($site->blog_id, 'bact_site_description');

					if ((isset($args['icon_show'])) && ($args['icon_show'] == true)) {
						$icon_image_src = get_blog_avatar($site->blog_id, $args['icon_size']);
						if ((!$icon_image_src) || (!strlen($icon_image_src))) {
							$data['sites'][$idx]->icon_image_src = $default_icon_src;
						} else {
							$data['sites'][$idx]->icon_image_src = $icon_image_src;
						}
					}
				}
			}
			
			$categories_string = apply_filters('site_categories_landing_list_sites_display', $content, $data, $args);
			return $categories_string;
				
		} else {

			$args = $this->opts['categories'];
			
			$get_terms_args = array();
			$get_terms_args['hide_empty']	=	$args['hide_empty'];
			$get_terms_args['orderby']		=	$args['orderby'];
			$get_terms_args['order']		=	$args['order'];
			$get_terms_args['pad_counts'] 	= 	true;
			
			$get_terms_args['hierarchical']	=	false;
			
			if ($args['show_style'] == "grid") {
				$get_terms_args['pad_counts'] 		= 1;
				$get_terms_args['parent'] 			= 0;
				$get_terms_args['hierarchical']		= 0;

				// For the grid we replace the 'per_page' value with the number of rows * cols
				if (!isset($args['grid_cols'])) 
					$args['grid_cols'] = 2;

				if (!isset($args['grid_rows'])) 
					$args['grid_rows'] = 3;
				
				$args['per_page'] = intval($args['grid_rows']) * intval($args['grid_cols']);
			} else if ($args['show_style'] == "accordion") {
				$get_terms_args['pad_counts'] = 1;
				$get_terms_args['parent'] = 0;
				$get_terms_args['hierarchical']	= 0;
			}
			
			//echo "args<pre>"; print_r($args); echo "</pre>";
			//echo "get_terms_args<pre>"; print_r($get_terms_args); echo "</pre>";
			
			$categories = get_terms( SITE_CATEGORIES_TAXONOMY, $get_terms_args );
			//echo "categories<pre>"; print_r($categories); echo "</pre>";
			
			if (($categories) && (count($categories))) {

				if (count($categories) < $args['per_page']) {

					$data['categories'] = $categories;

				} else {

					$data['offset'] 		= intval($args['per_page']) * (intval($data['current_page'])-1); 
					$data['categories'] 	= array_slice($categories, $data['offset'], $args['per_page'], true);

					$data['total_pages'] 	= ceil(count($categories)/intval($args['per_page']));

					if (intval($data['current_page']) > 1) {

						$data['prev'] = array();
						$data['prev']['page_number'] = intval($data['current_page']) - 1;
						
						if ((isset($this->opts['landing_page_rewrite'])) && ($this->opts['landing_page_rewrite'] == true)) {
							$data['prev']['link_url'] = trailingslashit($this->opts['landing_page_slug']) . $data['prev']['page_number'];
						} else {
							$data['prev']['link_url'] = $this->opts['landing_page_slug'] .'&amp;start_at=' . $data['prev']['page_number'];
						}
						
						$data['prev']['link_label'] = __('Previous page', SITE_CATEGORIES_I18N_DOMAIN);						
					}
					
					if ($data['current_page'] < $data['total_pages']) {

						$data['next'] = array();

						$data['next']['page_number'] = $data['current_page'] + 1;

						if ((isset($this->opts['landing_page_rewrite'])) && ($this->opts['landing_page_rewrite'] == true)) {
							$data['next']['link_url'] = trailingslashit($this->opts['landing_page_slug']) . $data['next']['page_number'];
						} else {
							$data['next']['link_url'] = $this->opts['landing_page_slug'] .'&amp;start_at=' . $data['next']['page_number'];
						}
						$data['next']['link_label'] = __('Next page', SITE_CATEGORIES_I18N_DOMAIN);
					}
				}
				
				if (count($data['categories'])) {

					foreach($data['categories'] as $idx => $data_category) {

						if ((isset($args['icon_show'])) && ($args['icon_show'] == true)) {
							$data['categories'][$idx]->icon_image_src = $this->get_category_term_icon_src($data_category->term_id, $args['icon_size']);
						}
						
						if ((isset($this->opts['landing_page_rewrite'])) && ($this->opts['landing_page_rewrite'] == true)) {
							$data['categories'][$idx]->bcat_url = trailingslashit($this->opts['landing_page_slug']) . $data_category->slug;
						} else {
							$data['categories'][$idx]->bcat_url = $this->opts['landing_page_slug'] .'&amp;category_name=' . $data_category->slug;
						}
						
						if (($args['show_style'] == "grid") || ($args['show_style'] == "accordion")) {
							$get_terms_args = array();
							$get_terms_args['hide_empty']	=	$args['hide_empty'];
							$get_terms_args['orderby']		=	$args['orderby'];
							$get_terms_args['order']		=	$args['order'];

							$get_terms_args['parent'] = $data_category->term_id;
							$get_terms_args['hierarchical']	=	0;

							//echo "child get_terms_args<pre>"; print_r($get_terms_args); echo "</pre>";

							$child_categories = get_terms( SITE_CATEGORIES_TAXONOMY, $get_terms_args );
							if (($child_categories) && (count($child_categories))) {

								// We tally the count of the children to make sure the parent count shows correctly. 
								$children_count = 0;
								foreach($child_categories as $child_category) {
									$children_count += $child_category->count;
									
									if ((isset($this->opts['landing_page_rewrite'])) && ($this->opts['landing_page_rewrite'] == true)) {
										$child_category->bcat_url = trailingslashit($this->opts['landing_page_slug']) . $child_category->slug;
									} else {
										$child_category->bcat_url = $this->opts['landing_page_slug'] .'&amp;category_name=' . $child_category->slug;
									}									
									
									if ((isset($args['icon_show_children'])) && ($args['icon_show_children'] == true)) {
										$child_category->icon_image_src = $this->get_category_term_icon_src($child_category->term_id, $args['icon_size_children']);
									}
									
								}
								if ($args['show_style'] == "accordion")
									$data['categories'][$idx]->count = $children_count;
								
								$data['categories'][$idx]->children = $child_categories;
							}
						}
					}
				}

				if (($args['show_style'] == "ul") || ($args['show_style'] == "ol")) {
					$categories_string = apply_filters('site_categories_landing_list_display', $content, $data, $args);
				} else if ($args['show_style'] == "grid") {
					$categories_string = apply_filters('site_categories_landing_grid_display', $content, $data, $args);
				} else if ($args['show_style'] == "accordion") {
					$categories_string = apply_filters('site_categories_landing_accordion_display', $content, $data, $args);
				}
				return $categories_string;
			}
		}
		
		return $content;
	}
	
	/**
	 * 
	 *
	 * @since 1.0.0
	 *
	 * @param none
	 * @return none
	 */
	function process_categories_title ($content, $post_id=0) {

		global $post;

		if (is_admin()) return $content;
		if (!in_the_loop()) return $content;

		$this->load_config();

		// We get the bcat options. This 'should' contain the variable 'landing_page_id' is the admin properly set things up
		if ((!isset($this->opts['landing_page_id'])) || (!intval($this->opts['landing_page_id'])))
			$opts['landing_page_id'] = 0; 
		
		if ($post->ID != intval($this->opts['landing_page_id'])) return $content;

		$category = get_query_var('category_name');
		$category_int = intval($category);

		// Here is some fuzzy logic. The query_var 'category_name' is the first item off the page slug as in /page-slug/category-name/page-number/
		// So we need to check if it is a real intval (3, 6, 12, etc.) then we assume we don't have a category and we are viewing the top-level page
		// list of blog categories. IF we do have a valid category-name then the next query_var is the page-number
		if (($category == $category_int) && ($category_int != 0)) {
			$category = '';
		}

		if (!$category) return $content;

		$bcat_term = get_term_by("slug", $category, SITE_CATEGORIES_TAXONOMY);
		if ( is_wp_error($bcat_term)) return $content;

		$title_str = '';

		if ((isset($this->opts['categories']['icon_show'])) && ($this->opts['categories']['icon_show'] == true)) {
			
			$icon_image_src = $this->get_category_term_icon_src($bcat_term->term_id, $this->opts['categories']['icon_size']);
			if ($icon_image_src) {
				$title_str .= '<img class="site-category-icon" style="float: left; padding-right:10px" alt="'. $bcat_term->name .'" src="'. $icon_image_src .'" 
					width="'. $this->opts['categories']['icon_size'] .'" height="'. $this->opts['categories']['icon_size'] .'" />';
			}
		} 
		$title_str .= '<span class="site-category-title">' .__('Category', SITE_CATEGORIES_I18N_DOMAIN) ." ". $bcat_term->name .'</span>';

		return $title_str;
	}
		
	/**
	 * 
	 *
	 * @since 1.0.0
	 *
	 * @param none
	 * @return none
	 */
	function bcat_signup_blogform($errors) {
		global $wpdb;

		$this->load_config();

		if ((!isset($this->opts['sites']['signup_show'])) || ($this->opts['sites']['signup_show'] != 1))
			return;
		
		//echo "opts<pre>"; print_r($this->opts); echo "</pre>";
		//echo "errors<pre>"; print_r($errors); echo "</pre>";
		//echo "_POST<pre>"; print_r($_POST); echo "</pre>";

		if (!isset($this->opts['sites']['signup_category_label']))	
			$this->opts['sites']['signup_category_label'] = __('Site Category:', SITE_CATEGORIES_I18N_DOMAIN);

		if (isset($this->opts['sites']['signup_category_required'])) {
			if (!isset($this->opts['sites']['signup_category_minimum']))
				$this->opts['sites']['signup_category_minimum'] = 1;
		} else {
			$this->opts['sites']['signup_category_minimum'] = 0;
		}

		if (!isset($this->opts['sites']['signup_description_label']))	
			$this->opts['sites']['signup_description_label'] = __('Site Description:', SITE_CATEGORIES_I18N_DOMAIN);

		if (isset($this->opts['sites']['category_limit']))
			$blog_category_limit = intval($this->opts['sites']['category_limit']);
		else
			$blog_category_limit = 1;

		if (($blog_category_limit > 100)	|| ($blog_category_limit < 1))
			$blog_category_limit = 1;

		?>
		<div id="bcat_site_categories_section">
		<label for=""><?php echo stripslashes($this->opts['sites']['signup_category_label']) ?></label><?php

		if ( $errmsg = $errors->get_error_message('bcat_site_categories') ) { ?>
			<p class="error"><?php echo $errmsg ?></p>
		<?php }
		
		//$site_categories_description = apply_filters('add_site_page_site_categories_description', '');
		//if (!empty($site_categories_description)) {
		//	echo $site_categories_description;
		//}
		
		$cat_counter = 1;
		?><ol><?php
		while(true) {

			$cat_excludes = '';
			if ((is_multisite()) && (!is_super_admin())) {
				if ((isset($this->opts['sites']['category_excludes'])) && (count($this->opts['sites']['category_excludes']))) {
					$cat_excludes = implode(', ', $this->opts['sites']['category_excludes']);
				} 
			}

			$bcat_args = array(
				'taxonomy'			=> 	SITE_CATEGORIES_TAXONOMY,
				'hierarchical'		=>	true,
				'hide_empty'		=>	false,
				'exclude'			=>	$cat_excludes,
				'show_option_none'	=>	__('None Selected', SITE_CATEGORIES_I18N_DOMAIN), 
				'name'				=>	'bcat_site_categories['. $cat_counter .']',
				'class'				=>	'bcat_category',
			);
			if (isset($_POST['bcat_site_categories'][$cat_counter])) {
				$bcat_args['selected'] = intval($_POST['bcat_site_categories'][$cat_counter]);
			}
			?><li><?php 
				if ($this->opts['sites']['signup_category_parent_selectable'] == 1)
					wp_dropdown_categories( $bcat_args ); 
				else
					$this->wp_dropdown_categories( $bcat_args ); 
				?> <?php
				if ((isset($this->opts['sites']['signup_category_required'])) && ($this->opts['sites']['signup_category_required'] == 1)) { 
					if ($cat_counter <= $this->opts['sites']['signup_category_minimum']) {
						?><span class="site-categories-required"><?php _e('(* required)', SITE_CATEGORIES_I18N_DOMAIN); ?></span><?php
					}
				}
			?></li><?php

			$cat_counter += 1;
			if ($cat_counter > $blog_category_limit) 
				break;
		}			
		?></ol></div><?php
		
		?>
		<div id="bcat_site_description_section">
			<label for="bcat_site_description"><?php echo stripslashes($this->opts['sites']['signup_description_label']) ?> <?php 
				if ((isset($this->opts['sites']['signup_description_required'])) && ($this->opts['sites']['signup_description_required'] == 1)) { 
					?><span class="site-categories-required"><?php _e('(* required)', SITE_CATEGORIES_I18N_DOMAIN); ?></span><?php 
				} ?></label>
			<?php
				//$site_description = apply_filters('new_site_page_site_categories_site_description', '');
				//if (!empty($site_description)) {
				//	echo $site_description;
				//}
			?>
			<?php
				if ( $errmsg = $errors->get_error_message('bcat_site_description') ) { ?>
					<p class="error"><?php echo $errmsg ?></p>
				<?php }
		
			?>
			<textarea name="bcat_site_description" style="width:100%;" cols="30" rows="10" id="bcat_site_description"><?php
				if (isset($_POST['bcat_site_description'])) {
					echo $_POST['bcat_site_description'];
				}
			?></textarea><br />
		</div>
		<?php
	}

	
	/**
	 * 
	 *
	 * @since 1.0.2
	 *
	 * @param none
	 * @return none
	 */
	function wpmu_new_blog_proc($blog_id, $user_id, $domain, $path, $site_id, $meta ) {
		global $current_site;

		if ((isset($blog_id)) && ($blog_id)) {

			if (isset($meta['bcat_signup_meta']['bcat_site_categories'])) {

				$bcat_set = wp_set_object_terms($blog_id, $meta['bcat_signup_meta']['bcat_site_categories'], SITE_CATEGORIES_TAXONOMY);
			}

			if (isset($meta['bcat_signup_meta']['bcat_site_description'])) {
				update_blog_option($blog_id, 'bact_site_description', $meta['bcat_signup_meta']['bcat_site_description']);
			}
		}
	}
	
	/**
	 * Validates the new blog signup form on the front-end of the website. If the fields are not valid we set the WP_Error object and return the errors
	 * to be displayed on the form. If valid then we store the form var into a class array which will be used in the 'wpmu_new_blog_proc' function.
	 *
	 * @since 1.0.2
	 *
	 * @param none
	 * @return none
	 */
	function bcat_wpmu_validate_blog_signup($result) {
		
		$this->load_config();

		if ((!isset($this->opts['sites']['signup_show'])) || ($this->opts['sites']['signup_show'] != 1))
			return $result;
		
		$errors = new WP_Error();

		if ((isset($this->opts['sites']['signup_category_required'])) && ($this->opts['sites']['signup_category_required'] == 1)) {

			if (!isset($this->opts['sites']['signup_category_minimum']))
				$this->opts['sites']['signup_category_minimum'] = 1;

			$bcat_site_categories = array();
			if (isset($_POST['bcat_site_categories'])) {
				foreach($_POST['bcat_site_categories'] as $bcat_cat) {
					if (intval($bcat_cat) > 0) {
						$bcat_site_categories[] = intval($bcat_cat);
					}
				}
			} 
			
			if (count($bcat_site_categories)) {
				$bcat_site_categories = array_unique($bcat_site_categories);
			}
			
			if (count($bcat_site_categories) < $this->opts['sites']['signup_category_minimum']) {
					$format = __('You must select at least %d unique categories', SITE_CATEGORIES_I18N_DOMAIN);
				$result['errors']->add( 'bcat_site_categories', sprintf($format, $this->opts['sites']['signup_category_minimum'] ));
			} else {
				$this->bcat_signup_meta['bcat_site_categories'] = $bcat_site_categories;
				
			}
		} 

		if ((isset($this->opts['sites']['signup_description_required'])) && ($this->opts['sites']['signup_description_required'] == 1)) {
			$bcat_site_description = '';
			if (isset($_POST['bcat_site_description'])) {
				$bcat_site_description = esc_attr($_POST['bcat_site_description']);
			}
			
			if (!strlen($bcat_site_description)) {
				$result['errors']->add( 'bcat_site_description', __('Please provide a site description', SITE_CATEGORIES_I18N_DOMAIN) );
			} else {
				$this->bcat_signup_meta['bcat_site_description'] = $bcat_site_description;
			}
		}

		return $result;
	}

	/**
	 * Once the new blog form submits we capture and validate the form information via the function 'bcat_wpmu_validate_blog_signup'. Via
	 * that function we store the bcat related fields into a class array. Via this 'bcat_add_signup_meta' function we store the class array
	 * as part of the signup meta information. 
	 *
	 * This is needed because there are two scenarios for signup. One is an anonymous user creates a new site. During this processing the form information
	 * needs to be stored until the new user is confirmed. So this does not take place all at once. The second scenario is when an authenticated user 
	 * creates a new blog. In this case the form processing is all at once. The meta will still be stored and processed but is only needed for a short 
	 * period of time
	 *
	 * @since 1.0.4
	 *
	 * @param none
	 * @return none
	 */

	function bcat_add_signup_meta($meta) {
		if (isset($this->bcat_signup_meta)) {
			$meta['bcat_signup_meta'] = $this->bcat_signup_meta;
		}
		return $meta;
	}
	
	function wp_dropdown_categories( $args = '' ) {
		$defaults = array(
			'show_option_all' => '', 'show_option_none' => '',
			'orderby' => 'id', 'order' => 'ASC',
			'show_last_update' => 0, 'show_count' => 0,
			'hide_empty' => 1, 'child_of' => 0,
			'exclude' => '', 'echo' => 1,
			'selected' => 0, 'hierarchical' => 0,
			'name' => 'cat', 'id' => '',
			'class' => 'postform', 'depth' => 0,
			'tab_index' => 0, 'taxonomy' => 'category',
			'hide_if_empty' => false
		);

		$defaults['selected'] = ( is_category() ) ? get_query_var( 'cat' ) : 0;

		// Back compat.
		if ( isset( $args['type'] ) && 'link' == $args['type'] ) {
			_deprecated_argument( __FUNCTION__, '3.0', '' );
			$args['taxonomy'] = 'link_category';
		}

		$r = wp_parse_args( $args, $defaults );

		if ( !isset( $r['pad_counts'] ) && $r['show_count'] && $r['hierarchical'] ) {
			$r['pad_counts'] = true;
		}

		$r['include_last_update_time'] = $r['show_last_update'];
		extract( $r );

		$tab_index_attribute = '';
		if ( (int) $tab_index > 0 )
			$tab_index_attribute = " tabindex=\"$tab_index\"";

		$categories = get_terms( $taxonomy, $r );
		$name = esc_attr( $name );
		$class = esc_attr( $class );
		$id = $id ? esc_attr( $id ) : $name;

		if ( ! $r['hide_if_empty'] || ! empty($categories) )
			$output = "<select name='$name' id='$id' class='$class' $tab_index_attribute>\n";
		else
			$output = '';

		if ( empty($categories) && ! $r['hide_if_empty'] && !empty($show_option_none) ) {
			$show_option_none = apply_filters( 'list_cats', $show_option_none );
			$output .= "\t<option value='-1' selected='selected'>$show_option_none</option>\n";
		}

		if ( ! empty( $categories ) ) {

			if ( $show_option_all ) {
				$show_option_all = apply_filters( 'list_cats', $show_option_all );
				$selected = ( '0' === strval($r['selected']) ) ? " selected='selected'" : '';
				$output .= "\t<option value='0'$selected>$show_option_all</option>\n";
			}

			if ( $show_option_none ) {
				$show_option_none = apply_filters( 'list_cats', $show_option_none );
				$selected = ( '-1' === strval($r['selected']) ) ? " selected='selected'" : '';
				$output .= "\t<option value='-1'$selected>$show_option_none</option>\n";
			}

			if ( $hierarchical )
				$depth = $r['depth'];  // Walk the full depth.
			else
				$depth = -1; // Flat.

			$output .= $this->walk_category_dropdown_tree( $categories, $depth, $r );
		}
		if ( ! $r['hide_if_empty'] || ! empty($categories) )
			$output .= "</select>\n";


		$output = apply_filters( 'wp_dropdown_cats', $output );

		if ( $echo )
			echo $output;

		return $output;
	}
	
	function walk_category_dropdown_tree() {
		$args = func_get_args();
		// the user's options are the third parameter
		if ( empty($args[2]['walker']) || !is_a($args[2]['walker'], 'Walker') )
			$walker = new BCat_Walker_CategoryDropdown;
		else
			$walker = $args[2]['walker'];

		return call_user_func_array(array( &$walker, 'walk' ), $args );
	}
	
	
}

class BCat_Walker_CategoryDropdown extends Walker {
	/**
	 * @see Walker::$tree_type
	 * @since 2.1.0
	 * @var string
	 */
	var $tree_type = 'category';

	/**
	 * @see Walker::$db_fields
	 * @since 2.1.0
	 * @todo Decouple this
	 * @var array
	 */
	var $db_fields = array ('parent' => 'parent', 'id' => 'term_id');

	/**
	 * @see Walker::start_el()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $category Category data object.
	 * @param int $depth Depth of category. Used for padding.
	 * @param array $args Uses 'selected', 'show_count', and 'show_last_update' keys, if they exist.
	 */
	function start_el(&$output, $category, $depth, $args) {
		$pad = str_repeat('&nbsp;', $depth * 3);

		$cat_name = apply_filters('list_cats', $category->name, $category);

		if ($depth == 0) {
			$output .= "<optgroup class=\"level-$depth\" label=\"".$cat_name."\">";
			
		} else {
			$output .= "\t<option class=\"level-$depth\" value=\"".$category->term_id."\"";
			if ( $category->term_id == $args['selected'] )
				$output .= ' selected="selected"';
			$output .= '>';
			$output .= $pad.$cat_name;
			if ( $args['show_count'] )
				$output .= '&nbsp;&nbsp;('. $category->count .')';
			if ( $args['show_last_update'] ) {
				$format = 'Y-m-d';
				$output .= '&nbsp;&nbsp;' . gmdate($format, $category->last_update_timestamp);
			}
			$output .= "</option>\n";
		}
	}

	function end_el(&$output, $page, $depth, $args) {
		if ($depth == 0) {
			$output .= '</optgroup>';
		}
	}
}

$site_categories = new SiteCategories();
