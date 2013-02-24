<?php
/**
 * @package Adminer
 * @author Frank B&uuml;ltge
 * 
 * Plugin Name: Adminer
 * Plugin URI:  http://bueltge.de/adminer-fuer-wordpress/1014/
 * Text Domain: adminer
 * Domain Path: /languages
 * Description: <a href="http://www.adminer.org/en/">Adminer</a> (formerly phpMinAdmin) is a full-featured MySQL management tool written in PHP. This plugin include this tool in WordPress for a fast management of your database.
 * Author:      Frank B&uuml;ltge
 * Version:     1.2.3
 * Author URI:  http://bueltge.de/
 * Donate URI:  https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=6069955
 * License:     Apache License
 * Last change: 01/08/2013
 * 
 * 
 * License:
 * ==============================================================================
 * Copyright 2009/2013 Frank Bueltge  (email : frank@bueltge.de)
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 * 
 * Requirements:
 * ==============================================================================
 * This plugin requires WordPress >= 2.7 and tested with WP 3.6 and PHP >= 5.3
 */

// avoid direct calls to this file, because now WP core and framework has been used
if ( ! function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
} elseif ( version_compare( phpversion(), '5.0.0', '<' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit( 'The plugin require PHP 5 or newer' );
}

class AdminerForWP {
	
	static private $classobj;
	
	public function __construct() {
		
		if ( ! is_admin() )
			return NULL;
		
		if ( is_multisite() && ! function_exists( 'is_plugin_active_for_network' ) )
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		
		add_action( 'init',       array( $this, 'register_styles' ) );
		add_action( 'init',       array( $this, 'on_init' ) );
		add_action( 'admin_init', array( $this, 'text_domain' ) );
	}
	
	/**
	 * Handler for the action 'init'. Instantiates this class.
	 *
	 * @since   1.2.2
	 * @access  public
	 * @return  $classobj
	 */
	public function get_object() {
		
		if ( NULL === self::$classobj )
			self::$classobj = new self;
	
		return self::$classobj;
	}
	
	/** 
	 * Call functions on init of WP
	 * 
	 * @return   void
	 */
	public function on_init() {
		
		// active for MU ?
		if ( is_multisite() && is_plugin_active_for_network( plugin_basename( __FILE__ ) ) )
			add_action( 'network_admin_menu', array( $this, 'on_network_admin_menu' ) );
		else
			add_action( 'admin_menu', array( $this, 'on_admin_menu' ) );
	}
	
	public function text_domain() {
		
		load_plugin_textdomain( 'adminer', false, dirname( plugin_basename(__FILE__) ) . '/languages' );
	}
	
	public function register_styles() {
		
		wp_register_style( 'adminer-menu', plugins_url( 'css/menu.css', __FILE__ ) );
		wp_register_style( 'adminer-settings', plugins_url( 'css/settings.css', __FILE__ ) );
		
		if ( is_multisite() && is_plugin_active_for_network( plugin_basename( __FILE__ ) ) ) {
			wp_enqueue_style( 'adminer-menu' );
			add_action( 'admin_bar_menu', array( $this, 'add_wp_admin_bar_item' ), 20 );
		}
	}
	
	public function on_load_page() {
		
		add_thickbox();
		wp_enqueue_style( 'adminer-settings' );
		add_action( 'contextual_help', array( $this, 'contextual_help' ), 10, 3 );
	}
	
	public function on_admin_menu() {
		
		if ( current_user_can( 'unfiltered_html' ) ) {
			wp_enqueue_style( 'adminer-menu' );
			
			$menutitle  = '<span class="adminer-icon">&nbsp;</span>';
			$menutitle .= __( 'Adminer', 'adminer' );
			$this->pagehook = add_management_page( 
				__( 'Adminer', 'adminer' ), 
				$menutitle, 
				'unfiltered_html', 
				plugin_basename(__FILE__), 
				array( $this, 'on_show_page' )
			);
			
			add_action( 'load-' . $this -> pagehook, array( $this, 'on_load_page' ) );
		}
	}
	
	public function on_network_admin_menu() {
		
		if ( current_user_can('unfiltered_html' ) ) {
			wp_enqueue_style( 'adminer-menu' );
			
			$menutitle  = '<span class="adminer-icon">&nbsp;</span>';
			$menutitle .= __( 'Adminer', 'adminer' );
			$this->pagehook = add_submenu_page( 
				'settings.php',
				__( 'Adminer', 'adminer' ), 
				$menutitle, 
				'unfiltered_html', 
				plugin_basename(__FILE__), 
				array( $this, 'on_show_page' )
			);
			
			add_action( 'load-' . $this -> pagehook, array( $this, 'on_load_page' ) );
		}
	}
	
	public function add_wp_admin_bar_item( $wp_admin_bar ) {
		
		if ( is_super_admin() ) {
			$wp_admin_bar -> add_menu( array(
			'parent' => 'network-admin',
			'secondary' => FALSE,
			'id'     => 'network-adminer',
			'title'  => '<span class="adminer-icon">&nbsp;</span>' . __( 'Adminer' ),
			'href'   => network_admin_url( 'settings.php?page=adminer/adminer.php' ),
			) );
		}
	}
	
	public function contextual_help( $contextual_help, $screen_id, $screen ) {
		
		if ( 'tools_page_adminer/adminer' !== $screen_id )
			return FALSE;
		
		$contextual_help  = '<p>';
		$contextual_help .= __( 'Start the Thickbox inside the Adminer-tool with the button &rsaquo;<em>Start Adminer inside</em>&lsaquo;.', 'adminer' ); 
		$contextual_help .= '<br />';
		$contextual_help .= __( 'Alternatively, you can use the button for use &rsaquo;<em>Adminer in a new Tab</em>&lsaquo;.', 'adminer' );
		$contextual_help .= '</p>' . "\n";
		$contextual_help .= '<p>' . __( '<a href="http://wordpress.org/extend/plugins/adminer/">Documentation on Plugin Directory</a>', 'adminer' );
		$contextual_help .= ' &middot; ' . __( '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=6069955">Donate</a>', 'adminer' );
		$contextual_help .=  ' &middot; ' .__( '<a href="http://bueltge.de/">Blog of Plugin author</a>', 'adminer' );
		$contextual_help .= ' &middot; ' . __( '<a href="http://www.adminer.org/">Adminer website</a></p>', 'adminer' );
		
		return $contextual_help;
	}
	
	/**
	 * Strip slashes for different var
	 * 
	 * @param    $value optional Array, String
	 * @return   void
	 */
	static function gpc_strip_slashes( $value = NULL ) {
		
		// cracy check, WP change the rules and also Adminer core
		// result; we must check wrong to the php doc
		if ( ! get_magic_quotes_gpc() ) {
			
			if ( NULL !== $value )
				$value = self::array_map_recursive( 'stripslashes_deep', $value ); 
			
			// stripslashes_deep or stripslashes
			$_REQUEST = self::array_map_recursive( 'stripslashes_deep', $_REQUEST ); 
			$_GET     = self::array_map_recursive( 'stripslashes_deep', $_GET ); 
			$_POST    = self::array_map_recursive( 'stripslashes_deep', $_POST ); 
			$_COOKIE  = self::array_map_recursive( 'stripslashes_deep', $_COOKIE );
		}
		
		return $value;
	} 
	
	/** 
	 * Deeper array_map() 
	 * 
	 * @param   string $callback Callback function to map 
	 * @param   array, string $value Array to map 
	 * @see     http://www.sitepoint.com/blogs/2005/03/02/magic-quotes-headaches/ 
	 * @return  array, string
	 */
	static function array_map_recursive( $callback, $values ) {
		
		if ( is_string( $values ) ) {
			$r = $callback( $values );
		} elseif ( is_array( $values ) ) {
			$r = array(); 
			
			foreach ( $values as $k => $v ) { 
				$r[$k] = is_scalar($v) 
					? $callback($v) 
					: self::array_map_recursive( $callback, $v ); 
			}
		}
		
		return $r; 
	}
	
	/**
	 * Return page content for start Adminer
	 * 
	 * @return   void
	 */
	public function on_show_page() {
		global $wpdb;
		
		if ( '' == DB_USER )
			$db_user = __( 'empty', 'adminer' );
		else
			$db_user = DB_USER;
			
		if ( '' == DB_PASSWORD )
			$db_password = __( 'empty', 'adminer' );
		else
			$db_password = DB_PASSWORD;
		?>
		<div class="wrap">
			<?php //screen_icon('tools' ); ?>
			<?php screen_icon( 'adminer-settings' ); ?>
			<h2><?php _e( 'Adminer for WordPress', 'adminer' ); ?></h2>
			<img class="alignright" src="<?php echo WP_PLUGIN_URL . '/' . dirname( plugin_basename(__FILE__) ); ?>/images/logo.png" alt="Adminer Logo" />
			<p><a href="http://www.adminer.org/">Adminer</a> <?php _e( '(formerly phpMinAdmin) is a full-featured MySQL management tool written in PHP.', 'adminer' ); ?><br><?php _e( 'Current used version of Plugin', 'adminer' ); echo ' ' . self :: get_plugin_data( 'Name' ) . ': ' . self :: get_plugin_data( 'Version' ); ?></p>
			<br class="clear"/>
			
			<p>
				<script type="text/javascript">
				<!--
					var viewportwidth,
					    viewportheight;
					
					if (typeof window.innerWidth != 'undefined' ) {
						viewportwidth = window.innerWidth-80,
						viewportheight = window.innerHeight-100
					} else if (typeof document.documentElement != 'undefined'
						&& typeof document.documentElement.clientWidth !=
						'undefined' && document.documentElement.clientWidth != 0)
					{
						viewportwidth = document.documentElement.clientWidth,
						viewportheight = document.documentElement.clientHeight
					} else { // older versions of IE
						viewportwidth = document.getElementsByTagName('body' )[0].clientWidth,
						viewportheight = document.getElementsByTagName('body' )[0].clientHeight
					}
					//document.write('<p class="textright">Your viewport width is '+viewportwidth+'x'+viewportheight+'</p>' );
					document.write('<a onclick="return false;" href="<?php echo WP_PLUGIN_URL . '/' . dirname( plugin_basename(__FILE__) ); ?>/inc/adminer/loader.php?username=<?php echo DB_USER . '&amp;db=' . DB_NAME; ?>&amp;?KeepThis=true&amp;TB_iframe=true&amp;height=' + viewportheight + '&amp;width=' + viewportwidth + '" class="thickbox button"><?php _e( 'Start Adminer inside', 'adminer' ); ?></a>' );
					//-->
				</script>
				<a target="_blank" href="<?php echo WP_PLUGIN_URL . '/' . dirname( plugin_basename(__FILE__) ); ?>/inc/adminer/loader.php?username=<?php echo DB_USER . '&amp;db=' . DB_NAME; ?>" class="button"><?php _e( 'Start Adminer in a new tab', 'adminer' ); ?></a>
			</p>
			<p>&nbsp;</p>
			
			<noscript>
				<iframe src="inc/adminer/loader.php?username=<?php echo DB_USER; ?>" width="100%" height="600" name="adminer">
					<p><?php _e('Your browser does not support embedded frames.', 'adminer'); ?></p>
				</iframe>
			</noscript>
			
			<div class="metabox-holder has-right-sidebar">
				
				<div class="inner-sidebar">
					<div class="postbox">
						
						<h3><span><?php _e( 'Like this plugin?', 'adminer' ); ?></span></h3>
						<div class="inside">
							
							<p><?php _e( 'Here\'s how you can give back:', 'adminer' ); ?></p>
							<ul>
								<li><a href="http://wordpress.org/extend/plugins/adminer/" title="<?php esc_attr_e( 'The Plugin on the WordPress plugin repository', 'adminer' ); ?>"><?php _e( 'Give the plugin a good rating.', 'adminer' ); ?></a></li>
								<li><a href="http://wordpress.org/support/view/plugin-reviews/adminer" title="<?php esc_attr_e( 'Write a good review on the repository', 'adminer' ); ?>"><?php _e( 'Write a review about the plugin.', 'adminer' ); ?></a></li>
								<li><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6069955" title="<?php esc_attr_e( 'Donate via PayPal', 'adminer' ); ?>"><?php _e( 'Donate a few euros.', 'adminer' ); ?></a></li>
								<li><a href="http://www.amazon.de/gp/registry/3NTOGEK181L23/ref=wl_s_3" title="<?php esc_attr_e( 'Frank B�ltge\'s Amazon Wish List', 'adminer' ); ?>"><?php _e( 'Get me something from my wish list.', 'adminer' ); ?></a></li>
								<li><a href="http://adminer.org" title="<?php _e( 'Adminer website for more informations and versions without WordPress', 'adminer' ); ?>"><?php _e( 'More about Adminer', 'adminer' ); ?></a></li>
							</ul>
							
						</div> <!-- .inside -->
					</div> <!-- .postbox -->
				</div> <!-- .inner-sidebar -->
				
				<div id="post-body">
					<div id="post-body-content">
						
						<div class="postbox">
							<h3><span><?php _e( 'Your Database Data', 'adminer' ); ?></span></h3>
							<div class="inside">
								
								<table class="widefat post fixed">
									<thead>
										<tr>
											<th><?php _e('Typ', 'adminer'); ?></th>
											<th><?php _e('Value', 'adminer'); ?></th>
										</tr>
									</thead>
									<tbody>
										<tr valign="top">
											<th scope="row"><?php _e('Server', 'adminer'); ?></th>
											<td><code><?php echo DB_HOST; ?></code></td>
										</tr>
										<tr valign="top">
											<th scope="row"><?php _e('Database', 'adminer'); ?></th>
											<td><code><?php echo DB_NAME; ?></code></td>
										</tr>
										<tr valign="top" class="alternate">
											<th scope="row"><?php _e('User', 'adminer'); ?></th>
											<td><code><?php echo $db_user; ?></code></td>
										</tr>
										<tr valign="top">
											<th scope="row"><?php _e('Password', 'adminer'); ?></th>
											<td><code><?php echo $db_password; ?></code></td>
										</tr>
									</tbody>
								</table>
								
							</div> <!-- .inside -->
						</div> <!-- .postbox -->
						
					</div> <!-- #post-body-content -->
				</div> <!-- #post-body -->
				
			</div> <!-- .metabox-holder -->
		
		</div>
		<?php
	}
	
	/**
	 * return plugin comment data
	 * 
	 * @uses   get_plugin_data
	 * @access public
	 * @since  1.1.0
	 * @param  $value string, default = 'TextDomain'
	 *         Name, PluginURI, Version, Description, Author, AuthorURI, TextDomain, DomainPath, Network, Title
	 * @return string
	 */
	private static function get_plugin_data( $value = 'TextDomain' ) {
		
		static $plugin_data = array();
		
		// fetch the data just once.
		if ( isset( $plugin_data[ $value ] ) )
			return $plugin_data[ $value ];
		
		if ( ! function_exists( 'get_plugin_data' ) )
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		
		$plugin_data  = get_plugin_data( __FILE__ );
		$plugin_value = $plugin_data[$value];
		
		return empty ( $plugin_data[ $value ] ) ? '' : $plugin_data[ $value ];
	}
	
} // end class

add_action( 'plugins_loaded', array( 'AdminerForWP', 'get_object' ) );
