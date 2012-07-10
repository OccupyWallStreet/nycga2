<?php

/*
  Plugin Name: Socialize
  Plugin URI: http://www.jonbishop.com/downloads/wordpress-plugins/socialize/
  Description: Adds actionable social bookmarking buttons to your site
  Version: 2.1.1
  Author: Jon Bishop
  Author URI: http://www.jonbishop.com
  License: GPL2
 */

if (!defined('SOCIALIZE_URL')) {
    define('SOCIALIZE_URL', plugin_dir_url(__FILE__));
}
if (!defined('SOCIALIZE_PATH')) {
    define('SOCIALIZE_PATH', plugin_dir_path(__FILE__));
}
if (!defined('SOCIALIZE_BASENAME')) {
    define('SOCIALIZE_BASENAME', plugin_basename(__FILE__));
}
if (!defined('SOCIALIZE_ADMIN')) {
    define('SOCIALIZE_ADMIN', get_bloginfo('url') . "/wp-admin");
}

require_once(SOCIALIZE_PATH . "admin/socialize-admin.php");
require_once(SOCIALIZE_PATH . "frontend/socialize-services.php");
require_once(SOCIALIZE_PATH . "frontend/socialize-frontend.php");
require_once(SOCIALIZE_PATH . "frontend/socialize-og.php");

class socializeWP {

    private static $socialize_settings;
    public static  $socializeFooterJS;
    public static  $socializeFooterScript;
    //=============================================
    // Hooks and Filters
    //=============================================
    function init() {
        global $socializeWPadmin, $socializeWPfrontend;
        self::$socializeFooterJS = array();
        self::$socializeFooterScript = array();
        if (is_admin()) {
            $socializeWPadmin = new SocializeAdmin();
        } else {
            $socializeWPfrontend = new SocializeServices();
            $socializeWPfrontend = new SocializeFrontend();
            $socializeWPgraph = new SocializeGraph();
        }
    }

    function get_options() {
        if (!isset(self::$socialize_settings)) {
            self::$socialize_settings = get_option('socialize_settings10');
        }
        return self::$socialize_settings;
    }

    function update_options($socialize_settings) {
        update_option('socialize_settings10', $socialize_settings);
        self::$socialize_settings = $socialize_settings;
    }

    // Define default option settings
    function add_defaults_socialize() {
        $tmp = get_option('socialize_settings10');
        if (!is_array($tmp)) {
            $tmp = array(
                "socialize_installed" => "on",
                "socialize_version" => "25",
                "socialize_alert_bg" => "#FFEAA8",
                "socialize_alert_border_size" => "2px",
                "socialize_alert_border_style" => "solid",
                "socialize_alert_border_color" => "#ddd",
                "socialize_text" => 'If you enjoyed this post, please consider <a href="#comments">leaving a comment</a> or <a href="' . get_bloginfo("rss2_url") . '" title="Syndicate this site using RSS">subscribing to the <abbr title="Really Simple Syndication">RSS</abbr> feed</a> to have future articles delivered to your feed reader.',
                "socialize_display_front" => "on",
                "socialize_display_archives" => "on",
                "socialize_display_search" => "",
                "socialize_display_posts" => "on",
                "socialize_display_pages" => "on",
                "socialize_display_feed" => "",
                "socialize_alert_box" => "on",
                "socialize_alert_box_pages" => "on",
                "socialize_twitterWidget" => "official",
                "socialize_fbWidget" => "official-like",
                "socialize_float" => "left",
                "socialize_twitter_source" => "socializeWP",
                "sharemeta" => "1,2,17,18",
                "socialize_bitly_name" => "",
                "socialize_bitly_key" => "",
                "socialize_topsy_theme" => "light-blue",
                "socialize_topsy_size" => "big",
                "socialize_twitter_count" => "vertical",
                "socialize_twitter_related" => "",
                "socialize_tweetmeme_style" => "normal",
                "socialize_tweetcount_via" => "false",
                "socialize_tweetcount_links" => "true",
                "socialize_tweetcount_size" => "large",
                "socialize_tweetcount_background" => "80b62a",
                "socialize_tweetcount_border" => "CCCCCC",
                "fb_layout" => "box_count",
                "fb_showfaces" => "false",
                "fb_verb" => "like",
                "fb_font" => "arial",
                "fb_color" => "light",
                "fb_width" => "50",
                "reddit_type" => "2",
                "reddit_bgcolor" => "",
                "reddit_bordercolor" => "",
                "su_type" => "5",
                "buzz_style" => "normal-count",
                "plusone_style" => "tall",
                "digg_size" => "DiggMedium",
                "yahoo_badgetype" => "square",
                "linkedin_counter" => "top",
                "socialize_position" => "vertical",
                "socialize_css" => "",
                "socialize_action_template" => "<div class=\"socialize-buttons\">%%buttons%%</div><div class=\"socialize-text\">%%content%%</div>",
                "socialize_fb_appid" => "",
                "socialize_fb_adminid" => "",
                "socialize_display_custom" => array(),
                "socialize_og" => "on",
                "socialize_fb_pageid" => "on",
                "pinterest_counter" => "vertical",
                "buffer_counter" => "vertical",
                "fb_sendbutton" => "false"
            );
            update_option('socialize_settings10', $tmp);
        }
        // 2.1 update
        if (empty($tmp['fb_sendbutton'])) {
            $tmp['pinterest_counter'] = 'vertical';
            $tmp['buffer_counter'] = 'vertical';
            $tmp['fb_sendbutton'] = 'false';
            update_option('socialize_settings10', $tmp);
        }
        // 2.0.3 update
        if (empty($tmp['plusone_style'])) {
            $tmp['plusone_style'] = 'tall';
            $tmp['socialize_version'] = '23';
            update_option('socialize_settings10', $tmp);
        }
        // 1.3 update
        if (empty($tmp['socialize_alert_box_pages'])) {
            $tmp['socialize_alert_box_pages'] = 'on';
            $tmp['socialize_version'] = '13';
            update_option('socialize_settings10', $tmp);
        }
        // 2.0 update
        if (empty($tmp['socialize_action_template']) || trim($tmp['socialize_action_template']) == "") {
            $tmp['fb_layout'] = 'box_count';
            $tmp['fb_showfaces'] = 'false';
            $tmp['fb_verb'] = 'like';
            $tmp['fb_font'] = 'arial';
            $tmp['fb_color'] = 'light';
            $tmp['socialize_bitly_name'] = '';
            $tmp['socialize_bitly_key'] = '';
            $tmp['socialize_topsy_theme'] = 'light-blue';
            $tmp['socialize_topsy_size'] = 'big';
            $tmp['socialize_twitter_count'] = 'vertical';
            $tmp['socialize_twitter_related'] = '';
            $tmp['socialize_tweetmeme_style'] = 'normal';
            $tmp['socialize_tweetcount_via'] = 'false';
            $tmp['socialize_tweetcount_links'] = 'true';
            $tmp['socialize_tweetcount_size'] = 'large';
            $tmp['socialize_tweetcount_background'] = '80b62a';
            $tmp['socialize_tweetcount_border'] = 'CCCCCC';
            $tmp['fb_width'] = '50';
            $tmp['reddit_type'] = '2';
            $tmp['reddit_bgcolor'] = '';
            $tmp['reddit_bordercolor'] = '';
            $tmp['su_type'] = '5';
            $tmp['buzz_style'] = 'normal-count';
            $tmp['digg_size'] = 'DiggMedium';
            $tmp['yahoo_badgetype'] = 'square';
            $tmp['linkedin_counter'] = 'top';
            $tmp['socialize_position'] = 'vertical';
            $tmp['socialize_css'] = '';
            $tmp['socialize_action_template'] = '<div class="socialize-buttons">%%buttons%%</div><div class="socialize-text">%%content%%</div>';
            $tmp['socialize_fb_appid'] = "";
            $tmp['socialize_fb_adminid'] = "";
            $tmp['socialize_display_custom'] = array();
            $tmp['socialize_alert_border_color'] = '#ddd';
            $tmp['socialize_alert_border_size'] = '2px';
            $tmp['socialize_alert_border_style'] = 'solid';
            $tmp['socialize_og'] = 'on';
            $tmp['socialize_fb_pageid'] = '';
            $tmp['socialize_version'] = '21';
            update_option('socialize_settings10', $tmp);
        }
        // Change Facebook Share to Facebook Like
        if ($tmp['socialize_fbWidget'] == 'official') {
            $tmp['socialize_fbWidget'] = 'official-like';
            update_option('socialize_settings10', $tmp);
        }
    }

}
$socializeWP = new socializeWP();
$socializeWP->init();
// RegisterDefault settings
register_activation_hook(__FILE__, array($socializeWP, 'add_defaults_socialize'));
?>