<?php
/*
Plugin Name: WPMU-DEV Error Message Removal
Description: See that error message above your admin content asking you to install an update plugin from WPMU-DEV?  Truth is, you don't need it, and since you can't get rid of the error message, you're stuck with it.  Until now.  Install this plugin to remove the annoying red error message up at the top, and you'll never have to worry about seeing it again!
Version: .11
Author: Mitch Canter
Author URI: http://www.studionashvegas.com
License: GPL2
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=GEMDJD
Tags: wp, wpmu-dev, wpmu, anti-spam, spam
Requires at least: 2.3
Tested up to: 3.2
Stable tag: trunk

See that annoying WPMU-DEV error message above your admin content asking you to install a plugin?  Truth is, you don't need it, and since you can't get rid of the error message, you're stuck with it.  Install this plugin to remove the annoying red error message up at the top, and you'll never have to worry about seeing it again!
*/

// Remove the annoying actions
function remove_annoying_actions() {
    remove_action('admin_notices','wdp_un_check',5);
    remove_action('network_admin_notices','wdp_un_check',5);
}

// Call 'remove_annoying_actions' during WP initialization
add_action('init','remove_annoying_actions');
//That's It!