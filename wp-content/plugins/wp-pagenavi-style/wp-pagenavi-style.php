<?php
/*
Plugin Name: Wp Pagenavi Style
Version: 1.3
Description: Adds Style options for most popular wordpress pagination plugin wp-pagenavi.
Author: Neel
Plugin URI: http://www.snilesh.com/resources/wordpress/wordpress-pagenavi-style-plugin/
Text Domain: wp-pagenavi-style
Domain Path: /lang


Copyright 2011  Nilesh Shiragave  ( email : snilesh.com@gmail.com )

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
( at your option ) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

global $wp_version;	
$plugin_name="WP PageNavi Style Pluigin";
$exit_msg=$plugin_name.' requires WordPress 3.0 or newer. <a href="http://codex.wordpress.org/Upgrading_WordPress">Please update!</a>';

$wp_pagenavi_style_version='1.2';

/* LOAD PLUGIN LANGUAGE FILES */
load_plugin_textdomain('wp-pagenavi-style',false,'wp-pagenavi-style/lang');


$wp_pn_style=defined('WP_PLUGIN_URL') ? (WP_PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__))) : trailingslashit(get_bloginfo('wpurl')) . PLUGINDIR . '/' . dirname(plugin_basename(__FILE__)); 

if (version_compare($wp_version,"3.0","<="))
{
	exit ($exit_msg);
}
if (!defined('WP_CONTENT_URL')) {
	$content_url=content_url();
	define('WP_CONTENT_URL', $content_url);
}
define('WP_PAGENAVI_STYLE_PATH',WP_CONTENT_URL.'/plugins/wp-pagenavi-style/');
define('WP_PAGENAVI_STYLE_CSS_PATH',plugin_dir_path(__FILE__).'/css');

/*Function to Call when Plugin get activated*/
function wp_pn_style_activate()
{
	global $wp_pn_style_defaults;
	$wp_pn_style_defaults=array(
	'stylesheet'=>'template',
	'template'=>'css3_black',
	'align'=>'left',
	'font_size'=>'12px',
	'heading_color'=>'000000',
	'background_color'=>'000000',
	'hover_color'=>'666666',
	'link_color'=>'ffffff',
	'link_active_color'=>'ffffff',
	'border_color'=>'666666',
	'border_active_color'=>'000000'
	);

	add_option('WP_PAGENAVI_STYLE_OPTION',$wp_pn_style_defaults);
}

/* Function to Call when Plugin Deactivated */
function wp_pn_style_deactivate()
{
  /* Code needs to be added for deactivate action */
 // delete_option('WP_PAGENAVI_STYLE_OPTION');
}

register_activation_hook( __FILE__, 'wp_pn_style_activate' );
register_deactivation_hook( __FILE__, 'wp_pn_style_deactivate' );

/* Add Administrator Menus */
function wp_pn_style_admin_menu()
{
	$level = 'manage_options';
	add_menu_page('PageNavi Style', 'PageNavi Style', $level, __FILE__, 'wp_pn_style_options_page',WP_PAGENAVI_STYLE_PATH.'images/icon.png');
}

add_action('admin_menu','wp_pn_style_admin_menu');	


function wp_pn_style_options_page()
{
	include_once dirname(__FILE__).'/includes/options.php';
}

add_action( 'wp_print_styles', 'wp_pn_style_deregister_styles', 100 );
function wp_pn_style_deregister_styles() {
  $options=get_option('WP_PAGENAVI_STYLE_OPTION');
  $css_file=WP_PAGENAVI_STYLE_PATH.'css/'.$options['template'].'.css';
  wp_dequeue_style( 'wp-pagenavi' );
   if(count($options)==0)
   {
	   $css_file=WP_PAGENAVI_STYLE_PATH.'css/default.css';
   }
  if($options['stylesheet']=='template')
  {
  wp_enqueue_style( 'wp-pagenavi-style', $css_file, false, '1.0' );
  ?>
	<style type="text/css">
	.wp-pagenavi{<?php if($options['align']=='right'){echo 'float:right !important; ';} elseif($options['align']=='left'){echo 'float:left !important; ';} else {echo 'margin-left:auto !important; margin-right:auto; !important';} ?>}
	</style>
  <?php
  }
  else
  {
	  $css_file=WP_PAGENAVI_STYLE_PATH.'style/default.css';
	  wp_enqueue_style( 'wp-pagenavi-style', $css_file, false, '1.0' );
	  ?>
	  <style type="text/css">
	  .wp-pagenavi{<?php if($options['align']=='right'){echo 'float:right !important; ';} elseif($options['align']=='left'){echo 'float:left !important; ';} else { echo 'margin-left:auto !important; margin-right:auto; !important';} ?>}

	  .wp-pagenavi a,.wp-pagenavi a:link,.wp-pagenavi a:visited,.wp-pagenavi a:active,.wp-pagenavi span.extend { <?php if($options['background_color']!=""){ echo 'background:#'.$options['background_color'].' !important;'; } ?> <?php if($options['border_color']!=""){ echo 'border:1px solid #'.$options['border_color'].' !important;'; } ?> <?php if($options['link_color']!=""){ echo 'color:#'.$options['link_color'].' !important;'; } ?> }
	  .wp-pagenavi a:hover,.wp-pagenavi span.current
	  {
		  <?php if($options['hover_color']!=""){ echo 'background:#'.$options['hover_color'].' !important;'; } ?> <?php if($options['border_active_color']!=""){ echo 'border:1px solid #'.$options['border_active_color'].' !important;'; } ?> <?php if($options['link_active_color']!=""){ echo 'color:#'.$options['link_active_color'].' !important;'; } ?> 
	  }
	  .wp-pagenavi span.pages { <?php if($options['heading_color']!=""){ echo 'color:#'.$options['heading_color'].' !important;'; } ?> }
	  </style>
	  <?php
  }
}
add_action('wp_head', 'wp_pn_style_style_codes');

function wp_pn_style_style_codes()
{
	$options=get_option('WP_PAGENAVI_STYLE_OPTION');
	?>
	<style type="text/css">
	 .wp-pagenavi
	{
		font-size:<?php echo $options['font_size']; ?> !important;
	}
	</style>
	<?php
}

	/* LOAD COMMON FUNCTIONS WHICH ARE USED IN ALL MY PLUGINS */
	include_once dirname(__FILE__).'/includes/common_functions.php';
?>