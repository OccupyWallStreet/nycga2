<?php
/*
Plugin Name: Multisite Plugin Manager
Plugin URI: http://wordpress.org/extend/plugins/multisite-plugin-manager/
Description: The essential plugin for every multisite install! Manage plugin access permissions across your entire multisite network.
Version: 3.1.1
Author: Aaron Edwards
Author URI: http://uglyrobot.com
Network: true

Copyright 2009-2012 UglyRobot Web Development (http://uglyrobot.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

class PluginManager {

	function __construct() {
		//declare hooks
		add_action( 'network_admin_menu', array( &$this, 'add_menu' ) );
		add_action( 'wpmu_new_blog', array( &$this, 'new_blog' ) ); //auto activation hook
		add_filter( 'all_plugins', array( &$this, 'remove_plugins' ) );
		add_filter( 'plugin_action_links', array( &$this, 'action_links' ), 10, 4 );
		add_filter( 'active_plugins', array( &$this, 'check_activated' ) );
		add_action( 'admin_notices', array( &$this, 'supporter_message' ) );
		add_action( 'plugins_loaded', array( &$this, 'localization' ) );

		//individual blog options
		add_action( 'wpmueditblogaction', array( &$this, 'blog_options_form' ) );
		add_action( 'wpmu_update_blog_options', array( &$this, 'blog_options_form_process' ) );

		add_filter( 'plugin_row_meta' , array( &$this, 'remove_plugin_meta' ), 10, 2 );
		add_action( 'admin_init', array( &$this, 'remove_plugin_update_row' ) );
	}

	function PluginManager() {
		$this->__construct();
	}

	function localization() {
		load_plugin_textdomain('pm', false, '/multisite-plugin-manager/languages/');
	}

	function add_menu() {
		add_submenu_page( 'plugins.php', __('Plugin Management', 'pm'), __('Plugin Management', 'pm'), 'manage_network_options', 'plugin-management', array( &$this, 'admin_page' ) );
	}

	function admin_page() {

		if (!is_site_admin())
			die('Nice Try!');

		$this->process_form();
		?>
		<div class='wrap'>
		<div class="icon32" id="icon-plugins"><br></div>
		<h2><?php _e('Manage Plugins', 'pm'); ?></h2>

		<?php if ($_REQUEST['saved']) { ?>
		<div id="message" class="updated fade"><p><?php _e('Settings Saved', 'pm'); ?></p></div>
		<?php }

		?>
		<div class="donate-message" style="border:1px gray solid;margin:10px;padding:10px;">
			<table>
			 <tr>
		     <td><?php echo "You are probably making money with this plugin. "; ?>Why not send me a small donation in honor of the time I put into this? Thanks!</td>
		     <td>
	        <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="hosted_button_id" value="W66QWST9B9KRN">
					<input type="image" src="https://www.paypalobjects.com/WEBSCR-640-20110306-1/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
					<img alt="" border="0" src="https://www.paypalobjects.com/WEBSCR-640-20110306-1/en_US/i/scr/pixel.gif" width="1" height="1">
					</form>
		     </td>
		   </tr>
		  </table>
		</div>
		<h3><?php _e('Help', 'pm'); ?></h3>
		<p><strong><?php _e('Auto Activation', 'pm'); ?></strong><br/>
		<?php _e('When auto activation is on for a plugin, newly created blogs will have that plugin activated automatically. This does not affect existing blogs.', 'pm'); ?></p>
		<p><strong><?php _e('User Control', 'pm'); ?></strong><br/>
		<?php if ( function_exists('is_pro_site') ) { ?>
		<?php _e('Choose if all users, pro sites only, or no one will be able to activate/deactivate the plugin through the <cite>Plugins</cite> menu. When you turn it off, users that have the plugin activated are grandfathered in, and will continue to have access until they deactivate it.', 'pm'); ?>
		<?php } else { ?>
		<?php _e('When user control is enabled for a plugin, all users will be able to activate/deactivate the plugin through the <cite>Plugins</cite> menu. When you turn it off, users that have the plugin activated are grandfathered in, and will continue to have access until they deactivate it.', 'pm'); ?>
		<?php } ?>
		</p>
		<p><strong><?php _e('Mass Activation/Deactivation', 'pm'); ?></strong><br/>
		<?php _e('Mass activate and Mass deactivate buttons activate/deactivates the specified plugin for all blogs. This is different than the "Network Activate" option on the network plugins page, as users can later disable it and this only affects existing blogs. It also ignores the User Control option.', 'pm'); ?></p>
		<form action="plugins.php?page=plugin-management&saved=1" method="post">
		<table class="widefat">
		  <thead>
			<tr>
				<th><?php _e('Name', 'pm'); ?></th>
				<th><?php _e('Version', 'pm'); ?></th>
				<th><?php _e('Author', 'pm'); ?></th>
				<th title="<?php _e('Users may activate/deactivate', 'pm'); ?>"><?php _e('User Control', 'pm'); ?></th>
				<th><?php _e('Mass Activate', 'pm'); ?></th>
				<th><?php _e('Mass Deactivate', 'pm'); ?></th>
			</tr>
			</thead>
		<?php

		$plugins = get_plugins();
		$auto_activate = (array)get_site_option('pm_auto_activate_list');
		$user_control = (array)get_site_option('pm_user_control_list');
		$supporter_control = (array)get_site_option('pm_supporter_control_list');
		foreach ( $plugins as $file => $p ) {

		  //skip network plugins or network activated plugins
		  if ( is_network_only_plugin( $file ) || is_plugin_active_for_network( $file ) )
		    continue;
			?>
			<tr>
				<td><?php echo $p['Name']?></td>
				<td><?php echo $p['Version']?></td>
				<td><?php echo $p['Author']?></td>
				<td>
				<?php
				  echo '<select name="control['.$file.']" />'."\n";
					$u_checked = in_array($file, $user_control);
					$s_checked = in_array($file, $supporter_control);
		      $auto_checked = in_array($file, $auto_activate);

					if ($u_checked) {
						$n_opt = '';
						$s_opt = '';
						$a_opt = ' selected="yes"';
						$auto_opt = '';
					} else if ($s_checked) {
						$n_opt = '';
						$s_opt = ' selected="yes"';
						$a_opt = '';
						$auto_opt = '';
					} else if ($auto_checked) {
						$n_opt = '';
						$s_opt = '';
						$a_opt = '';
						$auto_opt = ' selected="yes"';
					}else {
						$n_opt = ' selected="yes"';
						$s_opt = '';
						$a_opt = '';
						$auto_opt = '';
		 			}

		 			$opts = '<option value="none"'.$n_opt.'>' . __('None', 'pm') . '</option>'."\n";
					if ( function_exists('is_pro_site'))
						$opts .= '<option value="supporters"'.$s_opt.'>' . __('Pro Sites', 'pm') . '</option>'."\n";
					$opts .= '<option value="all"'.$a_opt.'>' . __('All Users', 'pm') . '</option>'."\n";
					$opts .= '<option value="auto"'.$auto_opt.'>' . __('Auto-Activate (All Users)', 'pm') . '</option>'."\n";

					echo $opts.'</select>';
				?>
				</td>
				<td><?php echo "<a href='plugins.php?page=plugin-management&mass_activate=$file'>" . __('Activate All', 'pm') . "</a>" ?></td>
				<td><?php echo "<a href='plugins.php?page=plugin-management&mass_deactivate=$file'>" . __('Deactivate All', 'pm') . "</a>" ?></td>
			</tr>
		<?php
		}
		?>
		</table>
		<p class="submit">
		  <input name="Submit" value="<?php _e('Update Options', 'pm') ?>" type="submit">
		</p>
		</form>
		</div>
		<?php
	} //end admin_page()

	//removes the meta information for normal admins
	function remove_plugin_meta($plugin_meta, $plugin_file) {
	  if ( is_super_admin() ) {
			return $plugin_meta;
		} else {
    	remove_all_actions("after_plugin_row_$plugin_file");
		  return array();
		}
	}

  function remove_plugin_update_row() {
	  if ( !is_super_admin() ) {
    	remove_all_actions('after_plugin_row');
		}
	}

	function process_form() {

		if (isset($_GET['mass_activate'])) {
			$plugin = $_GET['mass_activate'];
			$this->mass_activate($plugin);
		}
		if (isset($_GET['mass_deactivate'])) {
			$plugin = $_GET['mass_deactivate'];
			$this->mass_deactivate($plugin);
		}

		if (isset($_POST['control'])) {
		  //create blank arrays
	    $supporter_control = array();
	    $user_control = array();
	    $auto_activate = array();
	  	foreach ($_POST['control'] as $plugin => $value) {
	  	  if ($value == 'none') {
	  		  //do nothing
	      }	else if ($value == 'supporters') {
	        $supporter_control[] = $plugin;
	      }	else if ($value == 'all') {
	        $user_control[] = $plugin;
	      }	else if ($value == 'auto') {
	        $auto_activate[] = $plugin;
	      }
	  	}
	    update_site_option('pm_supporter_control_list', array_unique($supporter_control));
	  	update_site_option('pm_user_control_list', array_unique($user_control));
	  	update_site_option('pm_auto_activate_list', array_unique($auto_activate));

	  	//can't save blank value via update_site_option
	    if (!$supporter_control)
	      update_site_option('pm_supporter_control_list', 'EMPTY');
	    if (!$user_control)
	      update_site_option('pm_user_control_list', 'EMPTY');
	    if (!$auto_activate)
	      update_site_option('pm_auto_activate_list', 'EMPTY');
	  }
	}

	//options added to wpmu-blogs.php edit page. Overrides sitewide control settings for an individual blog.
	function blog_options_form($blog_id) {

	  $plugins = get_plugins();
	  $override_plugins = (array)get_blog_option($blog_id, 'pm_plugin_override_list');
	  ?>
	  </table>
	  <h3><?php _e('Plugin Override Options', 'pm') ?></h3>
	  <p style="padding:5px 10px 0 10px;margin:0;">
	  <?php _e('Checked plugins here will be accessible to this site, overriding the sitewide <a href="plugins.php?page=plugin-management">Plugin Management</a> settings. Uncheck to return to sitewide settings.', 'pm') ?>
	  </p>
	  <table class="widefat" style="margin:10px;width:95%;">
	  <thead>
		<tr>
			<th title="<?php _e('Blog users may activate/deactivate', 'pm') ?>"><?php _e('User Control', 'pm') ?></th>
	    <th><?php _e('Name', 'pm'); ?></th>
			<th><?php _e('Version', 'pm'); ?></th>
			<th><?php _e('Author', 'pm'); ?></th>
		</tr>
		</thead>
	  <?php
	  foreach ( $plugins as $file => $p ) {

	  	//skip network plugins or network activated plugins
		  if ( is_network_only_plugin( $file ) || is_plugin_active_for_network( $file ) )
		    continue;
			?>
			<tr>
				<td>
				<?php
			  $checked = (in_array($file, $override_plugins)) ? 'checked="checked"' : '';
			  echo '<label><input name="plugins['.$file.']" type="checkbox" value="1" '.$checked.'/> ' . __('Enable', 'mp') . '</label>';
				?>
				</td>
		 		<td><?php echo $p['Name']?></td>
		 		<td><?php echo $p['Version']?></td>
		 		<td><?php echo $p['Author']?></td>
			</tr>
			<?php
	  }
	  echo '</table>';
	}

	//process options from wpmu-blogs.php edit page. Overrides sitewide control settings for an individual blog.
	function blog_options_form_process() {
	  $override_plugins = array();
	  if (is_array($_POST['plugins'])) {
	    foreach ((array)$_POST['plugins'] as $plugin => $value) {
	      $override_plugins[] = $plugin;
	    }
	    update_option( "pm_plugin_override_list", $override_plugins );
	  } else {
	    update_option( "pm_plugin_override_list", array() );
	  }
	}

	//activate on new blog
	function new_blog($blog_id) {
	  require_once( ABSPATH.'wp-admin/includes/plugin.php' );

		$auto_activate = (array)get_site_option('pm_auto_activate_list');
		if (count($auto_activate)) {
	  	switch_to_blog($blog_id);
	    activate_plugins($auto_activate, '', false); //silently activate any plugins
	    restore_current_blog();
		}
	}

	function mass_activate($plugin) {
		global $wpdb;
    set_time_limit(120);

		$blogs = $wpdb->get_col("SELECT blog_id FROM {$wpdb->blogs} WHERE site_id = {$wpdb->siteid} AND spam = 0");
		if ($blogs)	{
		  foreach($blogs as $blog_id)	{
	   		switch_to_blog($blog_id);
		    activate_plugin($plugin); //silently activate the plugin
		    restore_current_blog();
			}
			?><div id="message" class="updated fade"><p><span style="color:#FF3300;"><?php echo $plugin; ?></span><?php _e(' has been MASS ACTIVATED.', 'pm'); ?></p></div><?php
  	} else {
      ?><div class="error"><p><?php _e('Failed to mass activate: error selecting blogs', 'pm'); ?></p></div><?php
		}
	}

	function mass_deactivate($plugin) {
  	global $wpdb;
    set_time_limit(120);

		$blogs = $wpdb->get_col("SELECT blog_id FROM {$wpdb->blogs} WHERE site_id = {$wpdb->siteid} AND spam = 0");
		if ($blogs)	{
    	foreach ($blogs as $blog_id)	{
	   		switch_to_blog($blog_id);
		    deactivate_plugins($plugin, true); //silently deactivate the plugin
		    restore_current_blog();
			}
			?><div id="message" class="updated fade"><p><span style="color:#FF3300;"><?php echo $plugin; ?></span><?php _e(' has been MASS DEACTIVATED.', 'pm'); ?></p></div><?php
		} else {
      ?><div class="error"><p><?php _e('Failed to mass deactivate: error selecting blogs', 'pm'); ?></p></div><?php
		}
	}

	//remove plugins with no user control
	function remove_plugins($all_plugins) {

		if (is_super_admin()) //don't filter siteadmin
	    return $all_plugins;

	  $auto_activate = (array)get_site_option('pm_auto_activate_list');
	  $user_control = (array)get_site_option('pm_user_control_list');
	  $supporter_control = (array)get_site_option('pm_supporter_control_list');
	  $override_plugins = (array)get_option('pm_plugin_override_list');

	  foreach ( (array)$all_plugins as $plugin_file => $plugin_data) {
	    if (in_array($plugin_file, $user_control) || in_array($plugin_file, $auto_activate) || in_array($plugin_file, $supporter_control) || in_array($plugin_file, $override_plugins)) {
	      //do nothing - leave it in
	    } else {
	      unset($all_plugins[$plugin_file]); //remove plugin
	    }
	  }
	  return $all_plugins;
	}

	//plugin activate links
	function action_links($action_links, $plugin_file, $plugin_data, $context) {
		global $psts, $blog_id;
		
	  if (is_super_admin()) //don't filter siteadmin
	    return $action_links;

	  $auto_activate = (array)get_site_option('pm_auto_activate_list');
	  $user_control = (array)get_site_option('pm_user_control_list');
	  $supporter_control = (array)get_site_option('pm_supporter_control_list');
	  $override_plugins = (array)get_option('pm_plugin_override_list');

	  if ($context != 'active') {
	    if (in_array($plugin_file, $user_control) || in_array($plugin_file, $auto_activate) || in_array($plugin_file, $override_plugins)) {
	      return $action_links;
	    } else if (in_array($plugin_file, $supporter_control)) {
	      if ( function_exists('is_pro_site') ) {
	        if (is_pro_site()) {
	          return $action_links;
	        } else {
	          add_action( "after_plugin_row_$plugin_file", array( &$this, 'remove_checks' ) ); //add action to disable row's checkbox
	          return array('<a style="color:red;" href="'.$psts->checkout_url($blog_id).'">Pro Sites Only</a>');
	        }
	      }
	    }
	  }
	  return $action_links;
	}

	//show supporter message if plugin exists
	function supporter_message() {
		global $pagenow;

	  if (is_super_admin()) //don't filter siteadmin
	    return; //

	  if ( function_exists('is_pro_site') && $pagenow == 'plugins.php') {
	    if ( !is_pro_site() ) {
	   		supporter_feature_notice();
			} else {
	      echo '<div class="error" style="background-color:#F9F9F9;border:0;font-weight:bold;"><p>As a '.get_site_option('site_name')." Pro Site, you now have access to all our premium plugins!</p></div>";
	    }
		}

		return;
	}

	//use jquery to remove associated checkboxes to prevent mass activation (usability, not security)
	function remove_checks($plugin_file) {
	  echo '<script type="text/javascript">jQuery("input:checkbox[value=\''.attribute_escape($plugin_file).'\']).remove();</script>';
	}

	/*
	Removes activated plugins that should not have been activated (multi). Single activations
	are additionaly protected by a nonce field. Dirty hack in case someone uses firebug or
	something to hack the post and simulate a bulk activation. I'd rather prevent
	them from being activated in the first place, but there are no hooks for that! The
	display will show the activated status, but really they are not. Only hacking attempts
	will see this though! */
	function check_activated($active_plugins) {

	  if (is_super_admin()) //don't filter siteadmin
	    return $active_plugins;

	  //only perform check right after activation hack attempt
	  if ($_POST['action'] != 'activate-selected' && $_POST['action2'] != 'activate-selected')
	    return $active_plugins;

	  $auto_activate = (array)get_site_option('pm_auto_activate_list');
	  $user_control = (array)get_site_option('pm_user_control_list');
	  $supporter_control = (array)get_site_option('pm_supporter_control_list');
	  $override_plugins = (array)get_option('pm_plugin_override_list');

	  foreach ( (array)$active_plugins as $plugin_file => $plugin_data) {
	    if (in_array($plugin_file, $user_control) || in_array($plugin_file, $auto_activate) || in_array($plugin_file, $supporter_control) || in_array($plugin_file, $override_plugins)) {
	      //do nothing - leave it in
	    } else {
	      deactivate_plugins($plugin_file, true); //silently remove any plugins
	      unset($active_plugins[$plugin_file]);
	    }
	  }

	  if ( function_exists('is_pro_site') ) {
	    if (count($supporter_control) && !is_pro_site()) {
	      deactivate_plugins($supporter_control, true); //silently remove any plugins
	      foreach ($supporter_control as $plugin_file)
	        unset($active_plugins[$plugin_file]);
	    }
	  }

	  return $active_plugins;
	}
}

$pm = new PluginManager();
?>