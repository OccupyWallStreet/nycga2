<?php 

/*--------------------------------------------------------------------------
*
*	Acf_options_page
*
*	@author Elliot Condon
*	@since 2.0.4
* 
*-------------------------------------------------------------------------*/
 
 
class Options_page 
{

	var $parent;
	var $dir;
	var $data;
	
	/*--------------------------------------------------------------------------------------
	*
	*	Acf_options_page
	*
	*	@author Elliot Condon
	*	@since 2.0.4
	* 
	*-------------------------------------------------------------------------------------*/
	
	function __construct($parent)
	{
		// vars
		$this->parent = $parent;
		$this->dir = $parent->dir;
		
		// data for passing variables
		$this->data = array();
		
		// actions
		add_action('admin_menu', array($this,'admin_menu'));
		
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	admin_menu
	*
	*	@author Elliot Condon
	*	@since 2.0.4
	* 
	*-------------------------------------------------------------------------------------*/
	
	function admin_menu() 
	{
		// validate
		if(!$this->parent->is_field_unlocked('options_page'))
		{
			return true;
		}
		
		$parent_slug = 'acf-options';
		$parent_title = __('Options','acf');
		
		// set parent slug
		$custom = apply_filters('acf_register_options_page',array());
		if(!empty($custom))
		{	
			$parent_slug = $custom[0]['slug'];
			$parent_title = $custom[0]['title'];
		}
		
		
		// Parent
		$parent_page = add_menu_page($parent_title, __('Options','acf'), 'edit_posts', $parent_slug, array($this, 'html'));	
		
		// some fields require js + css
		add_action('admin_print_scripts-'.$parent_page, array($this, 'admin_print_scripts'));
		add_action('admin_print_styles-'.$parent_page, array($this, 'admin_print_styles'));
		
		// Add admin head
		add_action('admin_head-'.$parent_page, array($this,'admin_head'));
		add_action('admin_footer-'.$parent_page, array($this,'admin_footer'));
		
		if(!empty($custom))
		{
			foreach($custom as $c)
			{
				$child_page = add_submenu_page($parent_slug, $c['title'], $c['title'], 'edit_posts', $c['slug'], array($this, 'html'));
				
				// some fields require js + css
				add_action('admin_print_scripts-'.$child_page, array($this, 'admin_print_scripts'));
				add_action('admin_print_styles-'.$child_page, array($this, 'admin_print_styles'));
				
				// Add admin head
				add_action('admin_head-'.$child_page, array($this,'admin_head'));
				add_action('admin_footer-'.$child_page, array($this,'admin_footer'));
			}
		}

	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	admin_init
	*
	*	@author Elliot Condon
	*	@since 2.0.4
	* 
	*-------------------------------------------------------------------------------------*/
	
	function admin_init()
	{	

		
	}
	
	
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	admin_head
	*
	*	@author Elliot Condon
	*	@since 2.0.4
	* 
	*-------------------------------------------------------------------------------------*/
	
	function admin_head()
	{	
		// save
		if(isset($_POST['update_options']))
		{
			// post_id = 0 for options page
			$post_id = 999999999;
							
			// strip slashes
			$_POST = array_map('stripslashes_deep', $_POST);
			
			// save fields
			$fields = isset($_POST['fields']) ? $_POST['fields'] : false;
			
			if($fields)
			{
				foreach($fields as $key => $value)
				{
					// get field
					$field = $this->parent->get_acf_field($key);
					
					$this->parent->update_value($post_id, $field, $value);
				}
			}
			
			$this->data['admin_message'] = 'Options Updated';
			
		}
		
		$include = $this->parent->get_input_metabox_ids(array('post_id' => 999999999), false);
		if(empty($include))
		{
			$this->data['no_fields'] = true;
			return false;	
		}
		
		// create tyn mce instance for wysiwyg
		//add_action('admin_head', 'wp_tiny_mce');
		
		// add css + javascript
		echo '<link rel="stylesheet" type="text/css" href="'.$this->parent->dir.'/css/global.css" />';
		echo '<link rel="stylesheet" type="text/css" href="'.$this->parent->dir.'/css/input.css" />';
		//echo '<script type="text/javascript" src="'.$this->parent->dir.'/js/input.js" ></script>';
		
		// fields admin_head
		foreach($this->parent->fields as $field)
		{
			$this->parent->fields[$field->name]->admin_head();
		}
		
		// get acf's
		$acfs = get_pages(array(
			'numberposts' 	=> 	-1,
			'post_type'		=>	'acf',
			'sort_column' => 'menu_order',
			'order' => 'ASC',
			'include'	=>	$include
		));
		if($acfs)
		{
			foreach($acfs as $acf)
			{
				
				// load
				$options = $this->parent->get_acf_options($acf->ID);
				$fields = $this->parent->get_acf_fields($acf->ID);

				// add meta box
				add_meta_box(
					'acf_' . $acf->ID, 
					$acf->post_title, 
					array($this->parent, 'meta_box_input'), 
					'acf_options_page', 
					$options['position'], 
					'high', 
					array( 'fields' => $fields )
				);
			}

		}
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	admin_footer
	*
	*	@author Elliot Condon
	*	@since 2.0.4
	* 
	*-------------------------------------------------------------------------------------*/
	function admin_footer()
	{
		//wp_preload_dialogs( array( 'plugins' => 'safari,inlinepopups,spellchecker,paste,wordpress,media,fullscreen,wpeditimage,wpgallery,tabfocus' ) );
	}
	
	
	/*---------------------------------------------------------------------------------------------
	 * admin_print_scripts / admin_print_styles
	 *
	 * @author Elliot Condon
	 * @since 2.0.4
	 * 
	 ---------------------------------------------------------------------------------------------*/
	function admin_print_scripts() {

  		foreach($this->parent->fields as $field)
		{
			$this->parent->fields[$field->name]->admin_print_scripts();
		}

	}
	
	function admin_print_styles() {
		
		foreach($this->parent->fields as $field)
		{
			$this->parent->fields[$field->name]->admin_print_styles();
		}
		
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	options_page
	*
	*	@author Elliot Condon
	*	@since 2.0.4
	* 
	*-------------------------------------------------------------------------------------*/
	function html()
	{
		?>
		<div class="wrap no_move">
		
			<div class="icon32" id="icon-options-general"><br></div>
			<h2><?php echo get_admin_page_title(); ?></h2>
			
			<?php if(isset($this->data['admin_message'])): ?>
			<div id="message" class="updated"><p><?php echo $this->data['admin_message']; ?></p></div>
			<?php endif; ?>
			
			<?php if(isset($this->data['no_fields'])): ?>
			<div id="message" class="updated"><p>No Custom Field Group found for the options page. <a href="<?php echo admin_url(); ?>post-new.php?post_type=acf">Create a Custom Field Group</a></p></div>
			<?php else: ?>
			
			<form id="post" method="post" name="post">
			<div class="metabox-holder has-right-sidebar" id="poststuff">
				
				<!-- Sidebar -->
				<div class="inner-sidebar" id="side-info-column">
					
					<!-- Update -->
					<div class="postbox">
						<h3 class="hndle"><span><?php _e("Save",'acf'); ?></span></h3>
						<div class="inside">
							<input type="hidden" name="HTTP_REFERER" value="<?php echo $_SERVER['HTTP_REFERER'] ?>" />
							<input type="submit" class="button-primary" value="Save Options" name="update_options" />
						</div>
					</div>
					
				</div>
					
				<!-- Main -->
				<div id="post-body">
				<div id="post-body-content">
					<?php $meta_boxes = do_meta_boxes('acf_options_page', 'normal', null); ?>
					<script type="text/javascript">
					(function($){
					
						$('#poststuff .postbox[id*="acf_"]').addClass('acf_postbox');

					})(jQuery);
					</script>
				</div>
				</div>
			
			</div>
			</form>
			
			<?php endif; ?>
		
		</div>
		
		<?php
				
	}
			
}

?>