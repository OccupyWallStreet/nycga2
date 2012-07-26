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

/**
 * Extension API to add to edit/create panels
 *
 * @package	 Core
 * @since 	 1.7
 */
class Buddyvents_Extension
{
	protected $name 				= false;
	protected $display_name 		= false;
	protected $slug 				= false;
	protected $visibility 			= 1;	
	protected $create_step_position = 81;
	protected $enable_create_step 	= true;
	protected $enable_nav_item 		= true;
	protected $enable_edit_item 	= true;
	protected $template_file 		= 'events/single/plugins';

	/**
	 * Handles the display of the content, if $enable_nav_item is set to true
	 * 
	 * Can be overriden in a child class
	 *
	 * @package	Core
	 * @since 	1.7
	 * @access	public
	 */
	public function display(){}

	/**
	 * Handles the display of the edit screen, if $enable_edit_item is set to true
	 * 
	 * Can be overriden in a child class
	 *
	 * @package	Core
	 * @since 	1.7
	 * @access	public
	 */
	public function edit_screen(){}

	/**
	 * Handles the saving of data of the edit screen
	 * 
	 * Can be overriden in a child class
	 *
	 * @package	Core
	 * @since 	1.7
	 * @access	public
	 */
	public function edit_screen_save(){}

	/**
	 * Handles the display of the creation screen, if $enable_create_step is set to true
	 * 
	 * Can be overriden in a child class
	 *
	 * @package	Core
	 * @since 	1.7
	 * @access	public
	 */
	public function create_screen(){}

	/**
	 * Handles the saving of data of the create screen
	 * 
	 * Can be overriden in a child class
	 *
	 * @package	Core
	 * @since 	1.7
	 * @access	public
	 */
	public function create_screen_save(){}

	/**
	 * Registers the component and handles display of creat/edit screens
	 * 
	 * This function should never be overridden. It needs to be set to public,
	 * though, because methods attached to hooks have to be public
	 *
	 * @package	Core
	 * @since 	1.7
	 * @access	public
	 */
	public function _register()
	{
		global $bp, $bpe;

		if( $this->enable_create_step )
		{
			$bpe->config->creation_steps[$this->slug] = array( 'name' => $this->name, 'slug' => $this->slug, 'position' => $this->create_step_position );

			add_action( 'bpe_custom_create_steps', array( &$this, 'create_screen' ) );
			add_action( 'bpe_create_event_step_save_' . $this->slug, array( &$this, 'create_screen_save' ) );
		}

		if( $this->enable_edit_item && $bp->is_item_admin || is_super_admin() )
		{
			add_action( 'bpe_event_edit_tabs', create_function( '$current, $url', 'echo "<li". ( ( \''. esc_attr( $this->slug ) .'\' == $current ) ? \' class="current"\' : \'\' ) ."><a href=\"". $url ."'. esc_attr( $this->slug ) .'\">' . esc_attr( $this->name ) . '</a></li>";' ), 10, 2 );

			if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_action_variable( bpe_get_option( 'edit_slug' ), 1 ) && bp_is_action_variable( $this->slug, 2 ) || isset( $_GET['page'] ) && $_GET['page'] == EVENT_FOLDER && isset( $_GET['step'] ) && $_GET['step'] == $this->slug )
			{
				if( is_admin() )
					add_action( 'admin_init', array( &$this, 'edit_screen_save' ) );
				else
					add_action( 'wp', array( &$this, 'edit_screen_save' ), 0 );
					
				add_action( 'bpe_custom_edit_steps', array( &$this, 'edit_screen' ) );
			}
		}

		if( $this->_check_visibility() )
		{			
			if( bp_is_current_component( bpe_get_base( 'slug' ) ) && $bp->is_single_item ) :
				$selected = '';
				add_action( 'bpe_single_nav_inside_menu', create_function( '$event, $current', 'echo "<a class=\"button". ( ( \''. esc_attr( $this->slug ) .'\' == $current ) ? \' selected\' : \'\' ) ."\" href=\"". bpe_get_event_link( $event ) ."'. esc_attr( $this->slug ) .'\">' . esc_attr( $this->name ) . '</a>";' ), 10, 2 );
			endif;
			
			if( bp_is_current_component( bpe_get_base( 'slug' ) ) && $bp->is_single_item && bp_is_action_variable( $this->slug, 1 ) )
			{
				$name = ( empty( $this->display_name ) ) ? $this->name : $this->display_name;
				
				add_action( 'bp_template_content_header', create_function( '', 'echo "' . esc_attr( $this->name ) . '";' ) );
				add_action( 'bpe_template_title', create_function( '', 'echo "' . esc_attr( $name ) . '";' ) );
				add_action( 'wp', array( &$this, '_display_hook' ), 2 );
			}
		}
	}

	/**
	 * Checks visibility of the extension
	 *
	 * @package	Core
	 * @since 	2.1
	 * @access	private
	 */
	private function _check_visibility()
	{
		if( ! $this->enable_nav_item )
			return false;
		
		// 1 = show to anybody
		// 2 = show to loggedin members only
		// 3 = show to event members only
		switch( $this->visibility )
		{
			case 1:
				return true;
				break;

			case 2:
				return ( is_user_logged_in() ) ? true : false;
				break;

			case 3:
				return ( bpe_is_member( bpe_get_displayed_event() ) ) ? true : false;
				break;
		}
	}

	/**
	 * Handles the display of the content
	 *
	 * This function should never be overridden. It needs to be set to public,
	 * though, because methods attached to hooks have to be public
	 *
	 * 	 * @package	Core
	 * @since 	1.7
	 * @access	public
	 */
	public function _display_hook()
	{
		add_action( 'bpe_template_content', array( &$this, 'display' ) );
		bp_core_load_template( apply_filters( 'bpe_core_template_plugin', $this->template_file ) );
	}
}

/**
 * Register an events extension
 *
 * @package	 Core
 * @since 	 1.7
 */
function bpe_register_event_extension( $class )
{
	if( ! class_exists( $class ) )
		return false;

	add_action( 'init', create_function( '', '$extension = new ' . $class . '; add_action( "init", array( &$extension, "_register" ), 20 );' ), 11 );
}
?>