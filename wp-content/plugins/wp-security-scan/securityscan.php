<?php
/*
Plugin Name: WP Security Scan
Plugin URI: http://www.websitedefender.com/news/free-wordpress-security-scan-plugin/

Description: Perform security scan of WordPress installation.
Author: WebsiteDefender
Version: 3.0.9
Author URI: http://www.websitedefender.com/
*/
/*
 * $rev #1 07/17/2011 {c}
 * $rev #2 07/26,27/2011 {c}
 * $rev #3 08/05/2011 {c}
 * $rev #4 08/26/2011 {c}
 * $rev #5 09/12/2011 {c}
 * $rev #6 09/20/2011 {c}
 * $rev #7 09/30/2011 {c}
 * $rev #8 10/03/2011 {c}
 * $rev #9 11/15/2011 {c}
 * $rev #9 12/17/2011 {c}
 */
/*
Copyright (C) 2008-2010 Acunetix / http://www.websitedefender.com/
(info AT websitedefender DOT com)


This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
if ( ! defined('WP_CONTENT_URL')) {
    define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
}
if ( ! defined('WP_CONTENT_DIR')) {
    define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
}
if ( ! defined('WP_PLUGIN_URL')) {
    define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
}
if ( ! defined('WP_PLUGIN_DIR')) {
    define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );
}

if(!function_exists('json_encode') || !class_exists('Services_JSON')) {
    @require_once(WP_PLUGIN_DIR . "/wp-security-scan/libs/json.php");
}
require_once(WP_PLUGIN_DIR . "/wp-security-scan/libs/functions.php");

if (!defined('WSD_RECAPTCHA_API_SERVER')) {
    @require_once(WP_PLUGIN_DIR . "/wp-security-scan/libs/recaptchalib.php");
}
require_once(WP_PLUGIN_DIR . "/wp-security-scan/libs/wsd.php");

//menus
require_once(WP_PLUGIN_DIR . "/wp-security-scan/inc/admin/security.php");
require_once(WP_PLUGIN_DIR . "/wp-security-scan/inc/admin/scanner.php");
require_once(WP_PLUGIN_DIR . "/wp-security-scan/inc/admin/pwtool.php");
require_once(WP_PLUGIN_DIR . "/wp-security-scan/inc/admin/plugin_options.php");
require_once(WP_PLUGIN_DIR . "/wp-security-scan/inc/admin/db.php");
require_once(WP_PLUGIN_DIR . "/wp-security-scan/inc/admin/support.php");
require_once(WP_PLUGIN_DIR . "/wp-security-scan/inc/admin/templates/header.php");
require_once(WP_PLUGIN_DIR . "/wp-security-scan/inc/admin/templates/footer.php");


//## this is the container for header scripts
add_action('admin_head', 'mrt_hd');
// # $rev #2 {c}
add_action('admin_init', 'wps_admin_init_load_resources');

//before sending headers
add_action("init",'mrt_wpdberrors',1);

//after executing a query
add_action("parse_query",'mrt_wpdberrors',1);

//## add the sidebar menu
add_action('admin_menu', 'add_men_pg');

add_action("init", 'mrt_remove_wp_version',1);   //comment out this line to make ddsitemapgen work

//before rendering each admin init
add_action('admin_init','mrt_wpss_admin_init');

// Check to see whether or not we should display the dashboard widget
//@ $rev4
$plugin1 = 'websitedefender-wordpress-security';
$plugin2 = 'secure-wordpress';
if (! in_array($plugin1.'/'.$plugin1.'.php', apply_filters('active_plugins', get_option('active_plugins')))
        || ! in_array($plugin2.'/'.$plugin2.'.php', apply_filters('active_plugins', get_option('active_plugins'))))
{
    define('WPSS_WSD_BLOG_FEED', 'http://www.websitedefender.com/feed/');
    @require_once('libs/wpssUtil.php');
    //@@ Hook into the 'wp_dashboard_setup' action to create the dashboard widget
    add_action('wp_dashboard_setup', "wpssUtil::addDashboardWidget");
}
unset($plugin1,$plugin2);
//@===

function mrt_wpss_admin_init(){
//    wp_enqueue_style('wsd_style', WP_PLUGIN_URL . '/wp-security-scan/css/wsd.css');
    // @see: http://www.websitedefender.com/forums/wp-security-scan-plugin/wp-security-scan-and-ssl
    wp_enqueue_style('wsd_style', plugin_dir_url(__FILE__) . 'css/wsd.css');
    /* #r5# */
    $h6 = 'swp-dashboard';
    wp_register_style($h6, plugin_dir_url(__FILE__) . 'css/acx-wp-dashboard.css');
    wp_enqueue_style($h6);
}

remove_action('wp_head', 'wp_generator');
function add_men_pg()
{
    // $#0000110$
    if (!current_user_can('administrator')){return false;}

    if (function_exists('add_menu_page'))
    {
        add_menu_page('WSD security', 'WSD security', 'edit_pages', __FILE__, 'mrt_opt_mng_pg', WP_PLUGIN_URL.'/wp-security-scan/images/wsd-logo-small.png');
            add_submenu_page(__FILE__, 'Scanner', 'Scanner', 'edit_pages', 'scanner', 'mrt_sub0');
            add_submenu_page(__FILE__, 'Password Tool', 'Password Tool', 'edit_pages', 'passwordtool', 'mrt_sub1');
            add_submenu_page(__FILE__, 'Database', 'Database', 'edit_pages', 'database', 'mrt_sub3');
            add_submenu_page(__FILE__, 'Options', 'Options', 'edit_pages', 'plugin_options', 'mrt_sub4');
            add_submenu_page(__FILE__, 'Support', 'Support', 'edit_pages', 'support', 'mrt_sub2');
    }
}

//## @since v3.0.8
//Display the "Settings" menu on plug-in page
add_filter( 'plugin_action_links_'.plugin_basename(__FILE__), 'wpss_admin_plugin_actions', -10);


function wpss_admin_head() {
	$scheme = 'http';
	if ( is_ssl() ) {
        $scheme = 'https';
    }
}
add_action( 'admin_head', 'wpss_admin_head' );


// function for WP < 2.8
function get_plugins_url($path = '', $plugin = '') {

  if ( function_exists('plugin_url') )
    return plugins_url($path, $plugin);

  if ( function_exists('is_ssl') )
    $scheme = ( is_ssl() ? 'https' : 'http' );
  else
    $scheme = 'http';
  if ( function_exists('plugins_url') )
    $url = plugins_url();
  else
    $url = WP_PLUGIN_URL;
  if ( 0 === strpos($url, 'http') ) {
    if ( function_exists('is_ssl') && is_ssl() )
      $url = str_replace( 'http://', "{$scheme}://", $url );
  }

  if ( !empty($plugin) && is_string($plugin) )
  {
    $folder = dirname(plugin_basename($plugin));
    if ('.' != $folder)
      $url .= '/' . ltrim($folder, '/');
  }

  if ( !empty($path) && is_string($path) && strpos($path, '..') === false )
    $url .= '/' . ltrim($path, '/');

  return apply_filters('plugins_url', $url, $path, $plugin);
}

function wpss_mrt_meta_box()
{
?>
    <div id="wsd-initial-scan" class="wsd-inside">
        <div class="wsd-initial-scan-section"><?php mrt_check_version();?></div>

        <div class="wsd-initial-scan-section"><?php mrt_check_table_prefix();?></div>

        <div class="wsd-initial-scan-section"><?php mrt_version_removal();?></div>

        <div class="wsd-initial-scan-section"><?php mrt_errorsoff();?></div>
<?php
    global $wpdb;

    echo '<div class="scanpass">WP ID META tag removed form WordPress core</div>';

    echo '<div class="wsd-initial-scan-section">';
        $name = $wpdb->get_var("SELECT user_login FROM $wpdb->users WHERE user_login='admin'");
        if ($name == "admin") {
                echo '<font color="red">"admin" user exists.</font>';
        }
        else { echo '<span class="scanpass">No user "admin".</span>'; }
    echo '</div>';

    echo '<div class="wsd-initial-scan-section">';
        if (file_exists('.htaccess')) {
            echo '<span class="scanpass">.htaccess file found in wp-admin/</span>';
        }
        else { echo '<span style="color:#f00;">
            The file .htaccess does not exist in the wp-admin section.
            Read more why you should have a .htaccess file in  the WP-admin area
            <a href="http://www.websitedefender.com/wordpress-security/htaccess-files-wordpress-security/"
            title="Why you should have a .htaccess file in  the WP-admin area" target="_blank">here</a>.
            </span>'; }
    echo '</div>';

?>

        <div class="mrt_wpss_note">
            <em>**WP Security Scan plugin <strong>must</strong> remain active for security features to persist**</em>
        </div>
    </div>
<?php
}


function wpss_mrt_meta_box2()
{
?>
    <ul id="wsd-information-scan-list"">
            <?php mrt_get_serverinfo(); ?>
    </ul>
    <script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__);?>js/wsdwpss_tooltip_glossary.js"></script>
<?php
}


// $rev #2: only load if they're not already.
function wps_admin_init_load_resources()
{
    // @see: http://www.websitedefender.com/forums/wp-security-scan-plugin/wp-security-scan-and-ssl
    wp_enqueue_script('acx-json', plugin_dir_url(__FILE__).'js/json.js');
    wp_enqueue_script('acx-md5', plugin_dir_url(__FILE__).'js/md5.js');
    wp_enqueue_script('wsd-scripts', plugin_dir_url(__FILE__).'js/scripts.js');
    wp_enqueue_script('wsd-wsd', plugin_dir_url(__FILE__).'js/wsd.js');
}

function mrt_hd()
{
?>
	<script type="text/javascript">
		var wordpress_site_name = "<?php echo htmlentities(get_bloginfo('url'));?>"
	</script>
	<script type="text/javascript">
	  var _wsdPassStrengthProvider = null;

	  jQuery(document).ready(function($) {
		_wsdPassStrengthProvider = new wsdPassStrengthProvider($);
		_wsdPassStrengthProvider.init();

		$('#wpss_mrt_1.postbox h3, #wpss_mrt_2.postbox h3, #wpss_mrt_3.postbox h3').click(function() {
			var parent = $(this).parent();
			if (parent) parent.toggleClass('closed');
		});
		$('#wpss_mrt_1.postbox .handlediv, #wpss_mrt_2.postbox .handlediv, #wpss_mrt_3.postbox .handlediv').click(function() {
			var parent = $(this).parent();
			if (parent) parent.toggleClass('closed');
		});
		$('#wpss_mrt_1.postbox.close-me, #wpss_mrt_2.postbox.close-me, #wpss_mrt_3.postbox.close-me').each(function() {
			$(this).addClass("closed");
		});
	  });
	</script>
<?php }
?>
