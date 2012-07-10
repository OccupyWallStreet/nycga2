<?php
/*
Plugin Name: Page Shortcodes
Plugin URI: http://wordpress.org/extend/plugins/page-shortcodes/
Description: Shortcode tools to embed pages and page lists.
Author: Dragonfly Development
Author URI: http://dflydev.com/
Version: 0.1
License: New BSD License - http://www.opensource.org/licenses/bsd-license.php
*/

if ( ! class_exists('PageShortcodesPlugin') ) {
    require_once('lib/PageShortcodesPlugin.php');
    PageShortcodesPlugin::SINGLETON(__FILE__);
    function page_shortcodes_plugin() {
        return PageShortcodesPlugin::SINGLETON(__FILE__);
    }
}

?>