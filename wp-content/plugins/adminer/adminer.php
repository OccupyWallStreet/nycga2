<?php
/**
 * @package Adminer
 * @author Frank B&uuml;ltge
 */

/*
Plugin Name: Adminer
Plugin URI:  http://bueltge.de/adminer-fuer-wordpress/1014/
Text Domain: adminer
Domain Path: /languages
Description: <a href="http://www.adminer.org/en/">Adminer</a> (formerly phpMinAdmin) is a full-featured MySQL management tool written in PHP. This plugin include this tool in WordPress for a fast management of your database.
Author:      Frank B&uuml;ltge
Version:     1.2.1
Author URI:  http://bueltge.de/
Donate URI:  http://bueltge.de/wunschliste/
License:     Apache License
Last change: 06/12/2012
*/ 

/**
License:
==============================================================================
Copyright 2009/2011 Frank Bueltge  (email : frank@bueltge.de)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

Requirements:
==============================================================================
This plugin requires WordPress >= 2.7 and tested with PHP Interpreter >= 5.3
*/

//avoid direct calls to this file, because now WP core and framework has been used
if ( ! function_exists( 'add_action' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
} elseif ( version_compare( phpversion(), '5.0.0', '<' ) ) {
	$exit_msg = 'The plugin require PHP 5 or newer';
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit( $exit_msg );
}


if ( ! class_exists('AdminerForWP' ) ) {
	
	//WordPress definitions
	if ( ! defined('WP_CONTENT_URL' ) )
		define( 'WP_CONTENT_URL', get_option('siteurl' ) . '/wp-content' );
	if ( ! defined('WP_CONTENT_DIR' ) )
		define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
	if ( ! defined('WP_PLUGIN_URL' ) )
		define( 'WP_PLUGIN_URL', WP_CONTENT_URL.'/plugins' );
	if ( ! defined('WP_PLUGIN_DIR' ) )
		define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR.'/plugins' );
	if ( ! defined('PLUGINDIR' ) )
		define( 'PLUGINDIR', 'wp-content/plugins' ); // Relative to ABSPATH.  For back compat.
	if ( ! defined('WP_LANG_DIR' ) )
		define( 'WP_LANG_DIR', WP_CONTENT_DIR . '/languages' );
	
	// plugin definitions
	define( 'FB_ADM_BASENAME', plugin_basename(__FILE__) );
	define( 'FB_ADM_BASEDIR', dirname( plugin_basename(__FILE__) ) );
	define( 'FB_ADM_TEXTDOMAIN', 'adminer' );
	
	class AdminerForWP {
		
		public function __construct() {
			
			if ( ! is_admin() )
				return FALSE;
			
			self::strip_slashes();
			add_action( 'init',	   array( &$this, 'register_styles' ) );
			add_action( 'admin_init', array( &$this, 'text_domain' ) );
			if ( is_multisite() )
				add_action( 'network_admin_menu', array( &$this, 'on_network_admin_menu' ) );
			else
				add_action( 'admin_menu', array( &$this, 'on_admin_menu' ) );
		}
		
		public function text_domain() {
			
			load_plugin_textdomain( FB_ADM_TEXTDOMAIN, false, FB_ADM_BASEDIR . '/languages' );
		}
		
		public function register_styles() {
			wp_register_style( 'adminer-menu', plugins_url( 'css/menu.css', __FILE__ ) );
			wp_register_style( 'adminer-settings', plugins_url( 'css/settings.css', __FILE__ ) );
			
			if ( is_multisite() ) {
				wp_enqueue_style( 'adminer-menu' );
				add_action( 'admin_bar_menu', array( $this, 'add_wp_admin_bar_item' ), 20 );
			}
		}
		
		public function on_load_page() {
			
			add_thickbox();
			wp_enqueue_style( 'adminer-settings' );
			add_action( 'contextual_help', array(&$this, 'contextual_help' ), 10, 3 );
		}
		
		public function on_admin_menu() {
			
			if ( current_user_can('unfiltered_html' ) ) {
				wp_enqueue_style( 'adminer-menu' );
				
				$menutitle  = '<span class="adminer-icon">&nbsp;</span>';
				$menutitle .= __( 'Adminer', FB_ADM_TEXTDOMAIN );
				$this->pagehook = add_management_page( 
					__( 'Adminer', FB_ADM_TEXTDOMAIN ), 
					$menutitle, 
					'unfiltered_html', 
					FB_ADM_BASENAME, 
					array( &$this, 'on_show_page' )
				);
				
				add_action( 'load-' . $this -> pagehook, array( &$this, 'on_load_page' ) );
			}
		}
		
		public function on_network_admin_menu() {
			
			if ( current_user_can('unfiltered_html' ) ) {
				wp_enqueue_style( 'adminer-menu' );
				
				$menutitle  = '<span class="adminer-icon">&nbsp;</span>';
				$menutitle .= __( 'Adminer', FB_ADM_TEXTDOMAIN );
				$this->pagehook = add_submenu_page( 
					'settings.php',
					__( 'Adminer', FB_ADM_TEXTDOMAIN ), 
					$menutitle, 
					'unfiltered_html', 
					FB_ADM_BASENAME, 
					array( &$this, 'on_show_page' )
				);
				
				add_action( 'load-' . $this -> pagehook, array( &$this, 'on_load_page' ) );
			}
		}
		
		public function add_wp_admin_bar_item( $wp_admin_bar ) {
			
			if ( is_super_admin() ) {
				$wp_admin_bar -> add_menu( array(
				'parent'	=> 'network-admin',
				'secondary' => FALSE,
				'id'		=> 'network-adminer',
				'title'	 => __( 'Adminer', FB_ADM_TEXTDOMAIN ),
				'href'	  => network_admin_url( 'settings.php?page=adminer/adminer.php' ),
				) );
			}
		}
		
		public function contextual_help( $contextual_help, $screen_id, $screen ) {
			
			if ( 'tools_page_adminer/adminer' !== $screen_id )
				return FALSE;
			
			$contextual_help  = '<p>';
			$contextual_help .= __( 'Start the Thickbox inside the Adminer-tool with the button &rsaquo;<em>Start Adminer inside</em>&lsaquo;.', FB_ADM_TEXTDOMAIN ); 
			$contextual_help .= '<br />';
			$contextual_help .= __( 'Alternatively, you can use the button for use &rsaquo;<em>Adminer in a new Tab</em>&lsaquo;.', FB_ADM_TEXTDOMAIN );
			$contextual_help .= '</p>' . "\n";
			$contextual_help .= '<p>' . __( '<a href="http://wordpress.org/extend/plugins/adminer/">Documentation on Plugin Directory</a>', FB_ADM_TEXTDOMAIN );
			$contextual_help .=  ' &middot; ' .__( '<a href="http://bueltge.de/">Blog of Plugin author</a>', FB_ADM_TEXTDOMAIN );
			$contextual_help .= ' &middot; ' . __( '<a href="http://www.adminer.org/">Adminer website</a></p>', FB_ADM_TEXTDOMAIN );
			
			return $contextual_help;
		}
		
		public function on_show_page() {
			global $wpdb;
			
			if ( '' == DB_USER )
				$db_user = __( 'empty', FB_ADM_TEXTDOMAIN );
			else
				$db_user = DB_USER;
				
			if ( '' == DB_PASSWORD )
				$db_password = __( 'empty', FB_ADM_TEXTDOMAIN );
			else
				$db_password = DB_PASSWORD;
			?>
			<div class="wrap">
				<?php //screen_icon('tools' ); ?>
				<?php screen_icon( 'adminer-settings' ); ?>
				<h2><?php _e( 'Adminer for WordPress', FB_ADM_TEXTDOMAIN ); ?></h2>
				<img class="alignright" src="<?php echo WP_PLUGIN_URL . '/' . FB_ADM_BASEDIR; ?>/images/logo.png" alt="Adminer Logo" />
				<p><a href="http://www.adminer.org/">Adminer</a> <?php _e( '(formerly phpMinAdmin) is a full-featured MySQL management tool written in PHP. Conversely to phpMyAdmin, it consist of a single file ready to deploy to the target server.', FB_ADM_TEXTDOMAIN ); ?> <?php _e( 'Current used version of Plugin', FB_ADM_TEXTDOMAIN ); echo ' ' . self :: get_plugin_data( 'Name' ) . ': ' . self :: get_plugin_data( 'Version' ); ?></p>
				<br class="clear"/>
				
				<p>
					<script type="text/javascript">
					<!--
						var viewportwidth;
						var viewportheight;
					
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
						document.write('<a onclick="return false;" href="<?php echo WP_PLUGIN_URL . '/' . FB_ADM_BASEDIR; ?>/inc/adminer/loader.php?username=<?php echo DB_USER . '&amp;db=' . DB_NAME; ?>&amp;?KeepThis=true&amp;TB_iframe=true&amp;height='+viewportheight+'&amp;width='+viewportwidth+'" class="thickbox button"><?php _e( 'Start Adminer inside', FB_ADM_TEXTDOMAIN ); ?></a>' );
						//-->
					</script>
					<a target="_blank" href="<?php echo WP_PLUGIN_URL . '/' . FB_ADM_BASEDIR; ?>/inc/adminer/loader.php?username=<?php echo DB_USER . '&amp;db=' . DB_NAME; ?>" class="button"><?php _e( 'Start Adminer in a new tab', FB_ADM_TEXTDOMAIN ); ?></a>
				</p>
				<p>&nbsp;</p>
				
				<noscript>
					<iframe src="inc/adminer/loader.php?username=<?php echo DB_USER; ?>" width="100%" height="600" name="adminer">
						<p><?php _e('Your browser does not support embedded frames.', FB_ADM_TEXTDOMAIN); ?></p>
					</iframe>
				</noscript>
				
				<h4><?php _e('Your Datebase data', FB_ADM_TEXTDOMAIN); ?></h4>
				<table class="widefat post fixed">
					<thead>
						<tr>
							<th><?php _e('Typ', FB_ADM_TEXTDOMAIN); ?></th>
							<th><?php _e('Value', FB_ADM_TEXTDOMAIN); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr valign="top">
							<th scope="row"><?php _e('Server', FB_ADM_TEXTDOMAIN); ?></th>
							<td><?php echo DB_HOST; ?></td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Database', FB_ADM_TEXTDOMAIN); ?></th>
							<td><?php echo DB_NAME; ?></td>
						</tr>
						<tr valign="top" class="alternate">
							<th scope="row"><?php _e('User', FB_ADM_TEXTDOMAIN); ?></th>
							<td><?php echo $db_user; ?></td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Password', FB_ADM_TEXTDOMAIN); ?></th>
							<td><?php echo $db_password; ?></td>
						</tr>
					</tbody>
				</table>
			
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
		 *		 Name, PluginURI, Version, Description, Author, AuthorURI, TextDomain, DomainPath, Network, Title
		 * @return string
		 */
		private static function get_plugin_data ( $value = 'TextDomain' ) {
			
			if ( ! function_exists( 'get_plugin_data' ) )
				require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
			
			$plugin_data  = get_plugin_data ( __FILE__ );
			$plugin_value = $plugin_data[$value];
			
			return $plugin_value;
		}
		
		/**
		 * Filter globals for magic quotes
		 */
		static function strip_slashes() {
			
			if ( get_magic_quotes_gpc() ) {
				$_REQUEST = self::array_map_recursive( 'stripslashes', $_REQUEST );
				$_GET     = self::array_map_recursive( 'stripslashes', $_GET );
				$_POST    = self::array_map_recursive( 'stripslashes', $_POST );
				$_COOKIE  = self::array_map_recursive( 'stripslashes', $_COOKIE );
			}
			
			return;
		}
		
		/**
		 * Deeper array_map()
		 *
		 * @param string $callback Callback function to map
		 * @param array $array Array to map
		 * @source http://www.sitepoint.com/blogs/2005/03/02/magic-quotes-headaches/
		 * @return array
		 */
		static function array_map_recursive( $callback, $array ) {
			$r = array(); 
	
			if ( is_array($array) ) {
				
				foreach ( $array as $k => $v ) {
					$r[$k] = is_scalar($v)
						? $callback($v)
						: AdminerForWP::array_map_recursive($callback, $v);
				}
			} 
			
			return $r;
		}
		
	} // end class
	
	
	function AdminerForWP_start() {
	
		new AdminerForWP();
	}
	add_action( 'plugins_loaded', 'AdminerForWP_start' );
}
?>
