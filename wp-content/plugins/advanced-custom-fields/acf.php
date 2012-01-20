<?php
/*
Plugin Name: Advanced Custom Fields
Plugin URI: http://plugins.elliotcondon.com/advanced-custom-fields/
Description: Customise your edit pages with an assortment of field types: Wysiwyg, Repeater, text, textarea, image, file, select, checkbox post type, page link and more! Hide unwanted metaboxes and assign to any edit page!
Version: 3.0.6
Author: Elliot Condon
Author URI: http://www.elliotcondon.com/
License: GPL
Copyright: Elliot Condon
*/

//ini_set('error_reporting', E_ALL);

include('core/api.php');

$acf = new Acf();

class Acf
{ 
	var $dir;
	var $path;
	var $siteurl;
	var $wpadminurl;
	var $version;
	var $upgrade_version;
	var $fields;
	var $options_page;
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	Constructor
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function Acf()
	{
		
		// set class variables
		$this->path = dirname(__FILE__).'';
		$this->dir = plugins_url('',__FILE__);
		$this->siteurl = get_bloginfo('url');
		$this->wpadminurl = admin_url();
		$this->version = '3.0.6';
		$this->upgrade_version = '3.0.0'; // this is the latest version which requires an upgrade
		
		
		// set text domain
		//load_plugin_textdomain('acf', false, $this->path.'/lang' );
		load_plugin_textdomain('acf', false, basename(dirname(__FILE__)).'/lang' );
		
		// load options page
		$this->setup_options_page();
		
		// actions
		add_action('init', array($this, 'init'));
		add_action('admin_menu', array($this,'admin_menu'));
		add_action('admin_head', array($this,'admin_head'));
		add_filter('name_save_pre', array($this, 'save_name'));
		add_action('save_post', array($this, 'save_post'));
		add_action('wp_ajax_get_input_metabox_ids', array($this, 'get_input_metabox_ids'));
		add_action('wp_ajax_get_input_style', array($this, 'the_input_style'));
		add_action('admin_footer', array($this, 'admin_footer'));
		add_action('admin_print_scripts', array($this, 'admin_print_scripts'));
		add_action('admin_print_styles', array($this, 'admin_print_styles'));
		add_action('wp_ajax_acf_upgrade', array($this, 'upgrade_ajax'));
		
		return true;
	}
	
	
	


	/*--------------------------------------------------------------------------------------
	*
	*	setup_fields
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function setup_fields()
	{
		// vars
		$return = array();
		
		// include parent field
		include_once('core/fields/acf_field.php');
		
		// include child fields
		include_once('core/fields/acf_field.php');
		include_once('core/fields/text.php');
		include_once('core/fields/textarea.php');
		include_once('core/fields/wysiwyg.php');
		include_once('core/fields/image.php');
		include_once('core/fields/file.php');
		include_once('core/fields/select.php');
		include_once('core/fields/checkbox.php');
		include_once('core/fields/radio.php');
		include_once('core/fields/true_false.php');
		include_once('core/fields/page_link.php');
		include_once('core/fields/post_object.php');
		include_once('core/fields/relationship.php');
		include_once('core/fields/date_picker/date_picker.php');
		include_once('core/fields/color_picker.php');
		
		$return['text'] = new acf_Text($this); 
		$return['textarea'] = new acf_Textarea($this); 
		$return['wysiwyg'] = new acf_Wysiwyg($this); 
		$return['image'] = new acf_Image($this); 
		$return['file'] = new acf_File($this); 
		$return['select'] = new acf_Select($this); 
		$return['checkbox'] = new acf_Checkbox($this);
		$return['radio'] = new acf_Radio($this);
		$return['true_false'] = new acf_True_false($this);
		$return['page_link'] = new acf_Page_link($this);
		$return['post_object'] = new acf_Post_object($this);
		$return['relationship'] = new acf_Relationship($this);
		$return['date_picker'] = new acf_Date_picker($this);
		$return['color_picker'] = new acf_Color_picker($this);
		
		if($this->is_field_unlocked('repeater'))
		{
			include_once('core/fields/repeater.php');
			$return['repeater'] = new acf_Repeater($this);
		}
		
		if($this->is_field_unlocked('flexible_content'))
		{
			include_once('core/fields/flexible_content.php');
			$return['flexible_content'] = new acf_flexible_content($this);
		}
		
		// hook to load in third party fields
		$custom = apply_filters('acf_register_field',array());
		
		if(!empty($custom))
		{
			foreach($custom as $v)
			{
				//var_dump($v['url']);
				include($v['url']);
				$name = $v['class'];
				$custom_field = new $name($this);
				$return[$custom_field->name] = $custom_field;
			}
		}
		
		$this->fields = $return;
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	setup_options_page
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function setup_options_page()
	{
		include_once('core/options_page.php');
		$this->options_page = new Options_page($this);
	}
	
	/*--------------------------------------------------------------------------------------
	*
	*	admin_menu
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function admin_menu() {
	
		// add acf page to options menu
		add_menu_page(__("Custom Fields",'acf'), __("Custom Fields",'acf'), 'manage_options', 'edit.php?post_type=acf');
		add_submenu_page('edit.php?post_type=acf', __('Settings','wp3i'), __('Settings','wp3i'), 'manage_options','acf-settings',array($this,'admin_page_settings'));
		add_submenu_page('edit.php?post_type=acf', __('Upgrade','wp3i'), __('Upgrade','wp3i'), 'manage_options','acf-upgrade',array($this,'admin_page_upgrade'));
		
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	Init
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function init()
	{	
		include('core/actions/init.php');
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	admin_page_upgrade
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function admin_page_upgrade()
	{
		include('core/admin/upgrade.php');
	}
	
	/*--------------------------------------------------------------------------------------
	*
	*	admin_page_settings
	*
	*	@author Elliot Condon
	*	@since 3.0.5
	* 
	*-------------------------------------------------------------------------------------*/
	
	function admin_page_settings()
	{
		include('core/admin/page_settings.php');
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	ajax_upgrade
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function upgrade_ajax()
	{	
		include('core/admin/upgrade_ajax.php');
	}
	
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	admin_print_scripts / admin_print_styles
	*
	*	@author Elliot Condon
	*	@since 3.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function admin_print_scripts() {
		
		// thickbox
		if($GLOBALS['pagenow'] == 'edit.php' && isset($GLOBALS['post_type']) && $GLOBALS['post_type'] == 'acf')
		{
			wp_enqueue_script( 'jquery' );
    		wp_enqueue_script( 'thickbox' );
		}
		
		if(in_array($GLOBALS['pagenow'], array('post.php', 'post-new.php')))
		{
			if($GLOBALS['post_type'] == 'acf')
			{
				// hmmm
			}
			else
			{
				// fields admin_head
				foreach($this->fields as $field)
				{
					$this->fields[$field->name]->admin_print_scripts();
				}
			}
		}
		
	}
	
	function admin_print_styles() {
		
		// thickbox
		if($GLOBALS['pagenow'] == 'edit.php' && isset($GLOBALS['post_type']) && $GLOBALS['post_type'] == 'acf')
		{
			wp_enqueue_style( 'thickbox' );
		}
		
		if(in_array($GLOBALS['pagenow'], array('post.php', 'post-new.php')))
		{
			if($GLOBALS['post_type'] == 'acf')
			{
				// hmmm	
			}
			else
			{
				// fields admin_head
				foreach($this->fields as $field)
				{
					$this->fields[$field->name]->admin_print_styles();
				}
			}
		}
		
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	admin_head
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function admin_head()
	{
		// vars
		global $post;
		
		// hide upgrade page from nav
		echo '<style type="text/css"> 
			#toplevel_page_edit-post_type-acf a[href="edit.php?post_type=acf&page=acf-upgrade"]{ display:none; }
			#toplevel_page_edit-post_type-acf .wp-menu-image { background: url("../wp-admin/images/menu.png") no-repeat scroll 0 -33px transparent; }
			#toplevel_page_edit-post_type-acf .wp-menu-image img { display:none; }
		</style>';
		
		
		// only add to edit pages
		if(in_array($GLOBALS['pagenow'], array('post.php', 'post-new.php')))
		{
			// edit field
			if($GLOBALS['post_type'] == 'acf')
			{
				echo '<script type="text/javascript" src="'.$this->dir.'/js/fields.js" ></script>';
				echo '<link rel="stylesheet" type="text/css" href="'.$this->dir.'/css/global.css" />';
				echo '<link rel="stylesheet" type="text/css" href="'.$this->dir.'/css/fields.css" />';
				
				add_meta_box('acf_fields', 'Fields', array($this, 'meta_box_fields'), 'acf', 'normal', 'high');
				add_meta_box('acf_location', 'Location </span><span class="description">- Add Fields to Edit Screens', array($this, 'meta_box_location'), 'acf', 'normal', 'high');
				add_meta_box('acf_options', 'Options</span><span class="description">- Customise the edit page', array($this, 'meta_box_options'), 'acf', 'normal', 'high');
			
			}
			else
			{
		
				// find post type and add wysiwyg support
				$post_type = get_post_type($post);
		
				// add css + javascript
				echo '<link rel="stylesheet" type="text/css" href="'.$this->dir.'/css/global.css" />';
				echo '<link rel="stylesheet" type="text/css" href="'.$this->dir.'/css/input.css" />';
				echo '<script type="text/javascript" src="'.$this->dir.'/js/input.js" ></script>';
				echo '<style type="text/css">.acf_postbox, .postbox[id*="acf_"] { display: none; }</style>';

				// get style for page
				$metabox_ids = $this->get_input_metabox_ids(array('post_id' => $post->ID), false);
				$style = isset($metabox_ids[0]) ? $this->get_input_style($metabox_ids[0]) : '';
				echo '<style type="text/css" id="acf_style" >' .$style . '</style>';
				
				// fields admin_head
				foreach($this->fields as $field)
				{
					$this->fields[$field->name]->admin_head();
				}
				
				// get acf's
				$acfs = get_pages(array(
					'numberposts' 	=> 	-1,
					'post_type'		=>	'acf',
					'sort_column' => 'menu_order',
					'order' => 'ASC',
				));
				if($acfs)
				{
					foreach($acfs as $acf)
					{
						// hide / show
						$show = in_array($acf->ID, $metabox_ids) ? "true" : "false";
						
						// load
						$options = $this->get_acf_options($acf->ID);
						$fields = $this->get_acf_fields($acf->ID);
						
						// add meta box
						add_meta_box(
							'acf_' . $acf->ID, 
							$acf->post_title, 
							array($this, 'meta_box_input'), 
							$post_type, 
							$options['position'], 
							'default', 
							array( 'fields' => $fields, 'options' => $options, 'show' => $show )
						);
					}
		
				}
						
				
		
			}
		}
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	admin_footer
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function admin_footer()
	{
		// acf edit list
		if($GLOBALS['pagenow'] == 'edit.php' && isset($GLOBALS['post_type']) && $GLOBALS['post_type'] == 'acf')
		{
			include('core/admin/page_acf.php');
		}
		
		// input meta boxes
		if(in_array($GLOBALS['pagenow'], array('post.php', 'post-new.php')) && $GLOBALS['post_type'] != 'acf')
		{
			//wp_preload_dialogs( array( 'plugins' => 'safari,inlinepopups,spellchecker,paste,wordpress,media,fullscreen,wpeditimage,wpgallery,tabfocus' ) );
			?>
			<script type="text/javascript">
			(function($){
				
				// add classes
				$('#poststuff .postbox[id*="acf_"]').addClass('acf_postbox');
				$('#adv-settings label[for*="acf_"]').addClass('acf_hide_label');
				
				// hide acf stuff
				$('#poststuff .acf_postbox').hide();
				$('#adv-settings .acf_hide_label').hide();
				
				// loop through acf metaboxes
				$('#poststuff .postbox.acf_postbox').each(function(){
					
					// vars
					var options = $(this).find('.inside > .options');
					var show = options.attr('data-show');
					var layout = options.attr('data-layout');
					var id = $(this).attr('id').replace('acf_', '');
					
					// layout
					$(this).addClass('acf_postbox').addClass(layout);
					
					// show / hide
					if(show == 'true')
					{
						$(this).show();
						$('#adv-settings .acf_hide_label[for="acf_' + id + '-hide"]').show();
					}
					
				});

			})(jQuery);
			</script>
			<?php
		}
		
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	meta_box_fields
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function meta_box_fields()
	{
		include('core/admin/meta_box_fields.php');
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	meta_box_location
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function meta_box_location()
	{
		include('core/admin/meta_box_location.php');
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	meta_box_options
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function meta_box_options()
	{
		include('core/admin/meta_box_options.php');
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	meta_box_input
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function meta_box_input($post, $args)
	{
		include('core/admin/meta_box_input.php');
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	get_acf_fields
	*	- returns an array of fields for a acf object
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/

	function get_acf_fields($post_id)
	{
		// vars
		$return = array();
		$keys = get_post_custom_keys($post_id);
		
		if($keys)
		{
			foreach($keys as $key)
			{
				if(strpos($key, 'field_') !== false)
				{
					$field = $this->get_acf_field($key, $post_id);
	
			 		$return[$field['order_no']] = $field;
				}
			}
		 	
		 	ksort($return);
	 	}
	 	// return fields
		return $return;
		
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	get_acf_field
	*	- returns a field
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/

	function get_acf_field($field_name, $post_id = false)
	{
		$post_id = $post_id ? $post_id : $this->get_post_meta_post_id($field_name);
		
		$field = get_post_meta($post_id, $field_name, true);
 		
 		return $field;
		
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	get_post_meta_post_id
	*	- returns the post_id for a meta_key
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/

	function get_post_meta_post_id($field_name)
	{
		global $wpdb;
		$post_id = $wpdb->get_var( $wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = %s", $field_name) );
		
		if($post_id) return (int)$post_id;
		 
		return false;
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	create_field
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function create_field($field)
	{
		if(!isset($this->fields[$field['type']]) || !is_object($this->fields[$field['type']]))
		{
			_e('Error: Field Type does not exist!','acf');
			return false;
		}
		
		// defaults
		if(!isset($field['class'])) $field['class'] = $field['type'];
		
		$this->fields[$field['type']]->create_field($field);
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	get_acf_location
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function get_acf_location($post_id)
	{
		// vars
		$return = array(
	 		'rules'		=>	array(),
	 		'allorany'	=>	get_post_meta($post_id, 'allorany', true) ? get_post_meta($post_id, 'allorany', true) : 'all', 
	 	);
		
		// get all fields
	 	$rules = get_post_meta($post_id, 'rule', false);
	 	
	 	if($rules)
	 	{
		 	foreach($rules as $rule)
		 	{
		 		$return['rules'][$rule['order_no']] = $rule;
		 	}
	 	}
	 	
	 	ksort($return['rules']);
	 	
	 	// return fields
		return $return;
	 	
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	get_acf_options
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function get_acf_options($post_id)
	{
		// defaults
	 	$options = array(
	 		'position'		=>	get_post_meta($post_id, 'position', true) ? get_post_meta($post_id, 'position', true) : 'normal',
	 		'layout'		=>	get_post_meta($post_id, 'layout', true) ? get_post_meta($post_id, 'layout', true) : 'default',
	 		'show_on_page'	=>	get_post_meta($post_id, 'show_on_page', true) ? get_post_meta($post_id, 'show_on_page', true) : array(),
	 	);
	 	
	 	// If this is a new acf, there will be no custom keys!
	 	if(!get_post_custom_keys($post_id))
	 	{
	 		$options['show_on_page'] = array('the_content', 'discussion', 'custom_fields', 'comments', 'slug', 'author');
	 	}
	 	
	 	// return
	 	return $options;
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	save_post
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function save_post($post_id)
	{	
		
		// do not save if this is an auto save routine
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;

		// only save once! WordPress save's twice for some strange reason.
		global $flag;
		if ($flag != 0) return $post_id;
		$flag = 1;
		
		// set post ID if is a revision
		if(wp_is_post_revision($post_id)) 
		{
			$post_id = wp_is_post_revision($post_id);
		}
		
		// include save files
		if(isset($_POST['save_fields']) &&  $_POST['save_fields'] == 'true') include('core/actions/save_fields.php');
		if(isset($_POST['save_input']) &&  $_POST['save_input'] == 'true') include('core/actions/save_input.php');
		
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	save_name
	*	- this function intercepts the acf post obejct and adds an "acf_" to the start of 
	*	it's name to stop conflicts between acf's and page's urls
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function save_name($name)
	{
        if (isset($_POST['post_type']) && $_POST['post_type'] == 'acf') 
        {
			$name = 'acf_' . sanitize_title_with_dashes($_POST['post_title']);
        }
        return $name;
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	get_value
	*
	*	@author Elliot Condon
	*	@since 3.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function get_value($post_id, $field)
	{
		if(!isset($this->fields[$field['type']]) || !is_object($this->fields[$field['type']]))
		{
			return '';
		}
		
		return $this->fields[$field['type']]->get_value($post_id, $field);
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	get_value_for_api
	*
	*	@author Elliot Condon
	*	@since 3.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function get_value_for_api($post_id, $field)
	{
		if(!isset($this->fields[$field['type']]) || !is_object($this->fields[$field['type']]))
		{
			return '';
		}
		
		return $this->fields[$field['type']]->get_value_for_api($post_id, $field);
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	update_value
	*
	*	@author Elliot Condon
	*	@since 3.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function update_value($post_id, $field, $value)
	{
		$this->fields[$field['type']]->update_value($post_id, $field, $value);
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	update_field
	*
	*	@author Elliot Condon
	*	@since 3.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function update_field($post_id, $field)
	{
		// format the field (select, repeater, etc)
		$field = $this->pre_save_field($field);
		
		// save it!
		update_post_meta($post_id, $field['key'], $field);
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	pre_save_field
	*
	*	@author Elliot Condon
	*	@since 3.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function pre_save_field($field)
	{
		// format the field (select, repeater, etc)
		return $this->fields[$field['type']]->pre_save_field($field);
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	format_value_for_input
	*
	*	@author Elliot Condon
	*	@since 3.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	//function format_value_for_input($value, $field)
	//{
	//	return $this->fields[$field['type']]->format_value_for_input($value, $field);
	//}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	format_value_for_api
	*
	*	@author Elliot Condon
	*	@since 3.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function format_value_for_api($value, $field)
	{
		if(!isset($this))
		{
			// called form api!
			
		}
		else
		{
			// called from object
		}
		return $this->fields[$field['type']]->format_value_for_api($value, $field);
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	create_format_data
	*
	*	@author Elliot Condon
	*	@since 3.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function create_format_data($field)
	{
		return $this->fields[$field['type']]->create_format_data($field);
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	get_input_metabox_ids
	*	- called by function.fields to hide / show metaboxes
	*	
	*	@author Elliot Condon
	*	@since 2.0.5
	* 
	*-------------------------------------------------------------------------------------*/
	
	function get_input_metabox_ids($overrides = array(), $json = true)
	{
		// overrides
		if(isset($_POST))
		{
			if(isset($_POST['post_id']) && $_POST['post_id'] != 'false') $overrides['post_id'] = $_POST['post_id'];
			if(isset($_POST['page_template']) && $_POST['page_template'] != 'false') $overrides['page_template'] = $_POST['page_template'];
			if(isset($_POST['page_parent']) && $_POST['page_parent'] != 'false') $overrides['page_parent'] = $_POST['page_parent'];
			if(isset($_POST['page_type']) && $_POST['page_type'] != 'false') $overrides['page_type'] = $_POST['page_type'];
			if(isset($_POST['page']) && $_POST['page'] != 'false') $overrides['page'] = $_POST['page'];
			if(isset($_POST['post']) && $_POST['post'] != 'false') $overrides['post'] = $_POST['post'];
			if(isset($_POST['post_category']) && $_POST['post_category'] != 'false') $overrides['post_category'] = $_POST['post_category'];
			if(isset($_POST['post_format']) && $_POST['post_format'] != 'false') $overrides['post_format'] = $_POST['post_format'];
			if(isset($_POST['taxonomy']) && $_POST['taxonomy'] != 'false') $overrides['taxonomy'] = $_POST['taxonomy'];
		}
		
		// create post object to match against
		$post = isset($overrides['post_id']) ? get_post($_POST['post_id']) : false;
		
		// find all acf objects
		$acfs = get_pages(array(
			'numberposts' 	=> 	-1,
			'post_type'		=>	'acf',
			'sort_column' 	=>	'menu_order',
		));
		
		// blank array to hold acfs
		$return = array();
		
		if($acfs)
		{
		
			foreach($acfs as $acf)
			{
				$add_box = false;
				$location = $this->get_acf_location($acf->ID);

				if($location['allorany'] == 'all')
				{
					// ALL
					$add_box = true;
					
					if($location['rules'])
					{
						foreach($location['rules'] as $rule)
						{
							
							// if any rules dont return true, dont add this acf
							if(!$this->match_location_rule($post, $rule, $overrides))
							{
								$add_box = false;
							}
						}
					}
					
				}
				elseif($location['allorany'] == 'any')
				{
					// ANY
					
					$add_box = false;
					
					if($location['rules'])
					{
						foreach($location['rules'] as $rule)
						{
							// if any rules return true, add this acf
							if($this->match_location_rule($post, $rule, $overrides))
							{
								$add_box = true;
							}
						}
					}
				}
							
				if($add_box == true)
				{
					$return[] = $acf->ID;
				}
				
			}
		}
		
		if($json)
		{
			echo json_encode($return);
			die;
		}
		else
		{
			return $return;
		}
		
		
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	get_input_style
	*	- called by function.fields to hide / show other metaboxes
	*	
	*	@author Elliot Condon
	*	@since 2.0.5
	* 
	*-------------------------------------------------------------------------------------*/
	
	function get_input_style($acf_id = false)
	{
		// get field group options
		$options = $this->get_acf_options($acf_id);
		$html = "";
		
		// html 
		if(!in_array('the_content',$options['show_on_page']))
		{
			$html .= '#postdivrich {display: none;} ';
		}
		if(!in_array('custom_fields',$options['show_on_page']))
		{
			$html .= '#postcustom, #screen-meta label[for=postcustom-hide] { display: none; } ';
		}
		if(!in_array('discussion',$options['show_on_page']))
		{
			$html .= '#commentstatusdiv, #screen-meta label[for=commentstatusdiv-hide] {display: none;} ';
		}
		if(!in_array('comments',$options['show_on_page']))
		{
			$html .= '#commentsdiv, #screen-meta label[for=commentsdiv-hide] {display: none;} ';
		}
		if(!in_array('slug',$options['show_on_page']))
		{
			$html .= '#slugdiv, #screen-meta label[for=slugdiv-hide] {display: none;} ';
		}
		if(!in_array('author',$options['show_on_page']))
		{
			$html .= '#authordiv, #screen-meta label[for=authordiv-hide] {display: none;} ';
		}
		
		return $html;
		
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	the_input_style
	*	- called by function.fields to hide / show other metaboxes
	*	
	*	@author Elliot Condon
	*	@since 2.0.5
	* 
	*-------------------------------------------------------------------------------------*/
	
	function the_input_style()
	{
		// overrides
		if(isset($_POST['acf_id']))
		{
			echo $this->get_input_style($_POST['acf_id']);
		}
		
		die;
		
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	match_location_rule
	*
	*	@author Elliot Condon
	*	@since 2.0.0
	* 
	*-------------------------------------------------------------------------------------*/

	function match_location_rule($post, $rule, $overrides = array())
	{

		if(!$post)
		{
			// post is false! that's okay if the rule is for user_type or options_page
			if($rule['param'] != 'user_type' && $rule['param'] != 'options_page')
			{
				return false;
			}
		}
		
		
		
		
		switch ($rule['param']) {
		
			// POST TYPE
		    case "post_type":
		    
		    	$post_type = isset($overrides['post_type']) ? $overrides['post_type'] : get_post_type($post);
		        
		        if($rule['operator'] == "==")
		        {
		        	if($post_type == $rule['value'])
		        	{
		        		return true; 
		        	}
		        	
		        	return false;
		        }
		        elseif($rule['operator'] == "!=")
		        {
		        	if($post_type != $rule['value'])
		        	{
		        		return true; 
		        	}
		        	
		        	return false;
		        }
		        
		        break;
		        
		    // PAGE
		    case "page":
		        
		        $page = isset($overrides['page']) ? $overrides['page'] : $post->ID;
		        
		        if($rule['operator'] == "==")
		        {
		        	if($page == $rule['value'])
		        	{
		        		return true; 
		        	}
		        	
		        	return false;
		        }
		        elseif($rule['operator'] == "!=")
		        {
		        	if($page != $rule['value'])
		        	{
		        		return true; 
		        	}
		        	
		        	return false;
		        }
		        
		        break;
		        
			// PAGE
		    case "page_type":
		        
		        $page_type = isset($overrides['page_type']) ? $overrides['page_type'] : $post->post_parent;
		        
		        if($rule['operator'] == "==")
		        {
		        	if($rule['value'] == "parent" && $page_type == "0")
		        	{
		        		return true; 
		        	}
		        	
		        	if($rule['value'] == "child" && $page_type != "0")
		        	{
		        		return true; 
		        	}
		        	
		        	return false;
		        }
		        elseif($rule['operator'] == "!=")
		        {
		        	if($rule['value'] == "parent" && $page_type != "0")
		        	{
		        		return true; 
		        	}
		        	
		        	if($rule['value'] == "child" && $page_type == "0")
		        	{
		        		return true; 
		        	}
		        	
		        	return false;
		        }
		        
		        break;
		        
		    // PAGE PARENT
		    case "page_parent":
		        
		        $page_parent = isset($overrides['page_parent']) ? $overrides['page_parent'] : $post->post_parent;
		        
		        if($rule['operator'] == "==")
		        {
		        	if($page_parent == $rule['value'])
		        	{
		        		return true; 
		        	}
		        	
		        	return false;
		        	
		        }
		        elseif($rule['operator'] == "!=")
		        {
		        	if($page_parent != $rule['value'])
		        	{
		        		return true; 
		        	}
		        	
		        	return false;
		        }
		        
		        break;
		    
		    // PAGE
		    case "page_template":
		        
		        $page_template = isset($overrides['page_template']) ? $overrides['page_template'] : get_post_meta($post->ID,'_wp_page_template',true);
		        
		        if($rule['operator'] == "==")
		        {
		        	if($page_template == $rule['value'])
		        	{
		        		return true; 
		        	}
		        	
		        	if($rule['value'] == "default" && !$page_template)
		        	{
		        		return true;
		        	}
		        	
		        	return false;
		        }
		        elseif($rule['operator'] == "!=")
		        {
		        	if($page_template != $rule['value'])
		        	{
		        		return true; 
		        	}
		        	
		        	return false;
		        }
		        
		        break;
		       
		    // POST
		    case "post":
		        
		        $post_id = isset($overrides['post']) ? $overrides['post'] : $post->ID;
		        
		        if($rule['operator'] == "==")
		        {
		        	if($post_id == $rule['value'])
		        	{
		        		return true; 
		        	}
		        	
		        	return false;
		        }
		        elseif($rule['operator'] == "!=")
		        {
		        	if($post_id != $rule['value'])
		        	{
		        		return true; 
		        	}
		        	
		        	return false;
		        }
		        
		        break;
		        
		    // POST CATEGORY
		    case "post_category":
		        
		        $cats = array();
		        
		        if(isset($overrides['post_category']))
		        {
		        	$cats = $overrides['post_category'];
		        }
		        else
		        {
		        	$all_cats = get_the_category($post->ID);
		        	foreach($all_cats as $cat)
					{
						$cats[] = $cat->term_id;
					}
		        }
		        if($rule['operator'] == "==")
		        {
		        	if($cats)
					{
						if(in_array($rule['value'], $cats))
						{
							return true; 
						}
					}
		        	
		        	return false;
		        }
		        elseif($rule['operator'] == "!=")
		        {
		        	if($cats)
					{
						if(!in_array($rule['value'], $cats))
						{
							return true; 
						}
					}
		        	
		        	return false;
		        }
		        
		        break;
			
			// PAGE PARENT
			/*
		    case "post_format":
		        
		        $post_format = isset($overrides['post_format']) ? $overrides['post_format'] : get_post_format(); 
		        
		        if($rule['operator'] == "==")
		        {
		        	if($post_format == $rule['value'])
		        	{
		        		return true; 
		        	}
		        	
		        	return false;
		        	
		        }
		        elseif($post_format == "!=")
		        {
		        	if($post->post_parent != $rule['value'])
		        	{
		        		return true; 
		        	}
		        	
		        	return false;
		        }
		        
		        break;
			*/
			
			// USER TYPE
		    case "user_type":
		        		
		        if($rule['operator'] == "==")
		        {
		        	if(current_user_can($rule['value']))
		        	{
		        		return true;
		        	}
		        	
		        	return false;
		        }
		        elseif($rule['operator'] == "!=")
		        {
		        	if(!current_user_can($rule['value']))
		        	{
		        		return true;
		        	}
		        	
		        	return false;
		        }
		        
		        break;
		    
		    // Options Page
		    case "options_page":
		
		        if($rule['operator'] == "==")
		        {
		        	if(get_admin_page_title() == $rule['value'])
		        	{
		        		return true;
		        	}
		        	
		        	return false;
		        }
		        elseif($rule['operator'] == "!=")
		        {
		        	if(get_admin_page_title() != $rule['value'])
		        	{
		        		return true;
		        	}
		        	
		        	return false;
		        }
		        
		        break;
		    
		    
		    // Post Format
		    case "post_format":
		        
		       
		        $post_format = isset($overrides['post_format']) ? has_post_format($overrides['post_format'],$post->ID) : has_post_format($rule['value'],$post->ID); 
		        
		        if($rule['operator'] == "==")
		        {
		        	if($post_format)
		        	{
		        		return true;
		        	}
		        	
		        	return false;
		        }
		        elseif($rule['operator'] == "!=")
		        {
		        	if(!$post_format)
		        	{
		        		return true;
		        	}
		        	
		        	return false;
		        }
		        
		        break;
		    
		    // Taxonomy
		    case "taxonomy":
		        
		        $terms = array();

		        if(isset($overrides['taxonomy']))
		        {
		        	$terms = $overrides['taxonomy'];
		        }
		        else
		        {
		        	$taxonomies = get_object_taxonomies($post->post_type);
		        	if($taxonomies)
		        	{
			        	foreach($taxonomies as $tax)
						{
							$all_terms = get_the_terms($post->ID, $tax);
							if($all_terms)
							{
								foreach($all_terms as $all_term)
								{
									$terms[] = $all_term->term_id;
								}
							}
						}
					}
		        }
		        
		        if($rule['operator'] == "==")
		        {
		        	if($terms)
					{
						if(in_array($rule['value'], $terms))
						{
							return true; 
						}
					}
		        	
		        	return false;
		        }
		       elseif($rule['operator'] == "!=")
		        {
		        	if($terms)
					{
						if(!in_array($rule['value'], $terms))
						{
							return true; 
						}
					}
		        	
		        	return false;
		        }
		        
		        
		        break;
		
		}
		
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	is_field_unlocked
	*
	*	@author Elliot Condon
	*	@since 3.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function is_field_unlocked($field_name)
	{
		switch ($field_name) {
		    case 'repeater':
		    	if(md5($this->get_license_key($field_name)) == "bbefed143f1ec106ff3a11437bd73432"){ return true; }else{ return false; }
		        break;
		    case 'options_page':
		        if(md5($this->get_license_key($field_name)) == "1fc8b993548891dc2b9a63ac057935d8"){ return true; }else{ return false; }
		        break;
		    case 'flexible_content':
		    	if(md5($this->get_license_key($field_name)) == "d067e06c2b4b32b1c1f5b6f00e0d61d6"){ return true; }else{ return false; }
		    	break;
	    }
	}
	
	/*--------------------------------------------------------------------------------------
	*
	*	is_field_unlocked
	*
	*	@author Elliot Condon
	*	@since 3.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function get_license_key($field_name)
	{
		return get_option('acf_' . $field_name . '_ac');
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	admin_message
	*
	*	@author Elliot Condon
	*	@since 2.0.5
	* 
	*-------------------------------------------------------------------------------------*/
	
	function admin_message($message = "", $type = 'updated')
	{
		$GLOBALS['acf_mesage'] = $message;
		$GLOBALS['acf_mesage_type'] = $type;
		
		function my_admin_notice()
		{
		    echo '<div class="' . $GLOBALS['acf_mesage_type'] . '" id="message">'.$GLOBALS['acf_mesage'].'</div>';
		}
		add_action('admin_notices', 'my_admin_notice');
	}
	
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	get_taxonomies_for_select
	*
	*---------------------------------------------------------------------------------------
	*
	*	returns a multidimentional array of taxonomies grouped by the post type / taxonomy
	*
	*	@author Elliot Condon
	*	@since 3.0.2
	* 
	*-------------------------------------------------------------------------------------*/
	
	function get_taxonomies_for_select()
	{
		$post_types = get_post_types();
		$choices = array();
		
		if($post_types)
		{
			foreach($post_types as $post_type)
			{
				$post_type_object = get_post_type_object($post_type);
				$taxonomies = get_object_taxonomies($post_type);
				if($taxonomies)
				{
					foreach($taxonomies as $taxonomy)
					{
						if(!is_taxonomy_hierarchical($taxonomy)) continue;
						$terms = get_terms($taxonomy, array('hide_empty' => false));
						if($terms)
						{
							foreach($terms as $term)
							{
								$choices[$post_type_object->label . ': ' . $taxonomy][$term->term_id] = $term->name; 
							}
						}
					}
				}
			}
		}
		
		return $choices;
	}
	
	
	function in_taxonomy($post, $ids)
	{
		$terms = array();
		
        $taxonomies = get_object_taxonomies($post->post_type);
    	if($taxonomies)
    	{
        	foreach($taxonomies as $tax)
			{
				$all_terms = get_the_terms($post->ID, $tax);
				if($all_terms)
				{
					foreach($all_terms as $all_term)
					{
						$terms[] = $all_term->term_id;
					}
				}
			}
		}
        
        if($terms)
		{
			if(is_array($ids))
			{
				foreach($ids as $id)
				{
					if(in_array($id, $terms))
					{
						return true; 
					}
				}
			}
			else
			{
				if(in_array($ids, $terms))
				{
					return true; 
				}
			}
		}
        	
        return false;
        	
	}
	
}
?>