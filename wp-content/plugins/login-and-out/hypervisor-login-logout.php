<?php
/*
Plugin Name: Login-Logout
Version: 2.6.1
Author: Roger Howorth
Author URI: http://www.thehypervisor.com
Plugin URI: http://www.thehypervisor.com/login-logout-changelog
Description: Adds a user friendly widget to make login/logout easy. Compatible WP 2.7+. Available in English, German, French, Italian, Spanish, Catalan, Dutch, Norwegian, Polish and Persian.
License: http://www.gnu.org/licenses/gpl.html
Text Domain: hypervisor-login-logout
*/
/*
Installation
1. Copy the file login-and-out.php to your WordPress plugins directory.
2. Login to WordPress as Administrator, go to Plugins and Activate it.
3. Add the Login-Logout widget to your Widget-enabled Sidebar
   instead of the default "Meta" Widget

Credit: Thanks to Patrick Khoo http://www.deepwave.net/ for model code. I worked with his Hide dashboard code, removed unwanted sections and updated for Wordpress 2.7+.

Copyright (c) 2009 Roger Howorth

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

*/

function rh_hype_lilo_widget($args) {
        extract($args);
        $code = array();
        global $user_identity , $user_email;
        $options = get_option('rh_hidedash_options');
        $before_html = stripslashes($options['before_html']);
        $after_html = stripslashes($options['after_html']);
        echo $before_html;
	echo __(stripslashes($options['title']),'hypervisor-login-logout');
        echo '<!--IT news from http://www.thehypervisor.com-->';
	if ( !wp_specialchars($options['sidebar_width']) ) $options['sidebar_width'] = "200";
	if ( $options['center_widget'] ) echo '<div style="width:'. wp_specialchars($options['sidebar_width']) . 'px; margin:0 auto;">';
	$all_links = get_option ( 'rh_hidedash_links_options' );
	if ( !empty($all_links)) {
		foreach ( $all_links as $link ) {
		$extra_links = $extra_links . '<a href="'. current($link) .'">'. key($link).'</a> ';
		}
	}
	if (is_user_logged_in()) {
		// User Already Logged In
		get_currentuserinfo();  // Usually someone already did this, right?
		if ( $options['display_email'] == '1' && !$options['hide_option_label'] ) $code[] = sprintf(__('Welcome, <u><b>%s</b></u> (%s)<br />Options: &nbsp;','hypervisor-login-logout'),$user_identity,$user_email);
		else
		if ( $options['display_email'] == '1' && $options['hide_option_label'] ) $code[] = sprintf(__('Welcome, <u><b>%s</b></u> (%s)<br />','hypervisor-login-logout'),$user_identity,$user_email);
		else
		if ( $options['hide_option_label'] ) $code[] = sprintf(__('Welcome, <u><b>%s</b></u><br />','hypervisor-login-logout'),$user_identity);
		else $code[] = sprintf(__('Welcome, <u><b>%s</b></u><br />Options: &nbsp;','hypervisor-login-logout'),$user_identity);
		// Default Strings
		$link_string_site = "<a href=\"".get_bloginfo('wpurl')."/wp-admin/index.php\" title=\"".__('Site Admin','hypervisor-login-logout')."\">".__('Site Admin','hypervisor-login-logout')."</a>&nbsp;&nbsp;|&nbsp;&nbsp;";
		$link_string_logout = '<a href="'. wp_logout_url($_SERVER['REQUEST_URI']) .'" title="'.__('Log out','hypervisor-login-logout').'">'.__('Log out','hypervisor-login-logout').'</a>';
		$link_string_edit = "<a href=\"".get_bloginfo('wpurl')."/wp-admin/edit.php\" title=\"".__('Edit Posts','hypervisor-login-logout')."\">".__('Edit Posts','hypervisor-login-logout')."</a>&nbsp;&nbsp;|&nbsp;&nbsp;";
		$link_string_profile = "<a href=\"".get_bloginfo('wpurl')."/wp-admin/profile.php\" title=\"".__('My Profile','hypervisor-login-logout')."\">".__('My Profile','hypervisor-login-logout')."</a>&nbsp;&nbsp;|&nbsp;&nbsp;";

		// Administrator?
		if (current_user_can('level_10')) {
			$code[] = $link_string_site;
                        $code[] = $link_string_logout;
			if ( $extra_links ) $code[] = '<br />Links: '.$extra_links;
			if ( $options['center_widget'] ) $code[] = '</div>';
			$code[] = $after_html;
		} else
		// level_2?
		if (current_user_can('level_2')) {
			if ($options['allow_authed']) {
				// Allow level_2 user to see Dashboard - treat like Administrator
				$code[] = $link_string_site;
				$code[] = $link_string_logout;
        			if ( $extra_links ) $code[] = '<br />Links: '.$extra_links;
				if ( $options['center_widget'] ) $code[] = '</div>';
				$code[] = $after_html;
			}
			// Hide Dashboard for level_2 user
			$code[] = $link_string_edit;
			$code[] = $link_string_logout;
			if ( $extra_links ) $code[] = '<br />Links: '.$extra_links;
			if ( $options['center_widget'] ) $code[] = '</div>';
			$code[] = $after_html;
		} else 
		// Less than level_2 user - Hide Dashboard from this User
		{
                $code[] = $link_string_profile;
		$code[] = $link_string_logout;
		if ( $extra_links ) $code[] = '<br />Links: '.$extra_links;
		if ( $options['center_widget'] ) $code[] = '</div>';
		$code[] = $after_html;
                }
	}
else {
	// User _NOT_ Logged In
	if ( $options['hide_register'] != 1 ) $code[] = "<a href=\"".get_bloginfo('wpurl')."/wp-login.php?action=register&amp;redirect_to=".$_SERVER['REQUEST_URI']."\" title=\"".__('Register','hypervisor-login-logout')."\">".__('Register','hypervisor-login-logout')."</a>&nbsp;&nbsp;|&nbsp;&nbsp;";
	$code[] = "<a href=\"".get_bloginfo('wpurl')."/wp-login.php?action=login&amp;redirect_to=".$_SERVER['REQUEST_URI']."\" title=\"".__('Login','hypervisor-login-logout')."\">".__('Login','hypervisor-login-logout')."</a>";
	$code[] = $after_html;
}
   foreach ( $code as $snip ) {
      _e($snip);
   }
   echo "<!--Hypervisor Login Logout end-->";
   return $code;
}

function rh_hype_lilo() {
  echo $before_widget;
  global $user_identity , $user_email;
//  $insert_php = get_option ( 'rh_insert_php' );
  $options = get_option('rh_hidedash_options');
  $before_html = stripslashes($options['before_html']);
  $after_html = stripslashes($options['after_html']);
  echo "<!--Hypervisor Login Logout start-->";
        echo '<!--IT news from http://www.thehypervisor.com-->';
	echo $before_html;
	$all_links = get_option ( 'rh_hidedash_links_options' );
	if ( !empty($all_links)) {
		foreach ( $all_links as $link ) {
		$extra_links = $extra_links . '<a href="'. current($link) .'">'. __(key($link),'hypervisor-login-logout').'</a> ';
		}
	}
	if (is_user_logged_in()) {
		// User Already Logged In
		get_currentuserinfo();  // Usually someone already did this, right?
		if ( $options['display_email'] == '1' && !$options['hide_option_label'] ) printf(__('Welcome, <u><b>%s</b></u> (%s)&nbsp;&nbsp;Options: &nbsp;','hypervisor-login-logout'),$user_identity,$user_email);
		else
		if ( $options['display_email'] == '1' && $options['hide_option_label'] ) printf(__('Welcome, <u><b>%s</b></u> (%s)&nbsp;&nbsp;','hypervisor-login-logout'),$user_identity,$user_email);
		else
		if ( $options['hide_option_label'] ) printf(__('Welcome, <u><b>%s</b></u>&nbsp;&nbsp;','hypervisor-login-logout'),$user_identity);
		else printf(__('Welcome, <u><b>%s</b></u>&nbsp;&nbsp;Options: &nbsp;','hypervisor-login-logout'),$user_identity);
		// Default Strings
		$link_string_site = "<a href=\"".get_bloginfo('wpurl')."/wp-admin/index.php\" title=\"".__('Site Admin','hypervisor-login-logout')."\">".__('Site Admin','hypervisor-login-logout')."</a>&nbsp;&nbsp;|&nbsp;&nbsp;";
		$link_string_logout = '<a href="'. wp_logout_url($_SERVER['REQUEST_URI']) .'" title="'.__('Log out','hypervisor-login-logout').'">'.__('Log out','hypervisor-login-logout').'</a>';
		$link_string_edit = "<a href=\"".get_bloginfo('wpurl')."/wp-admin/edit.php\" title=\"".__('Edit Posts','hypervisor-login-logout')."\">".__('Edit Posts','hypervisor-login-logout')."</a>&nbsp;&nbsp;|&nbsp;&nbsp;";
		$link_string_profile = "<a href=\"".get_bloginfo('wpurl')."/wp-admin/profile.php\" title=\"".__('My Profile','hypervisor-login-logout')."\">".__('My Profile','hypervisor-login-logout')."</a>&nbsp;&nbsp;|&nbsp;&nbsp;";

		// Administrator?
		if (current_user_can('level_10')) {
			echo $link_string_site;
			echo $link_string_logout;
			if ( $extra_links ) echo '&nbsp;&nbsp;Links: '.$extra_links;
			echo $after_html;
            echo $after_widget;
        echo "<!--Hypervisor Login Logout end-->";
			return;
		}
		// level_2?
		if (current_user_can('level_2')) {
			if ($options['allow_authed']) {
				// Allow level_2 user to see Dashboard - treat like Administrator
				echo $link_string_site;
				echo $link_string_logout;
        			if ( $extra_links ) echo '&nbsp;&nbsp;Links: '.$extra_links;
				echo $after_html;
                echo $after_widget;
        echo "<!--Hypervisor Login Logout end-->";
				return;
			}
			// Hide Dashboard for level_2 user
			echo $link_string_edit;
			echo $link_string_logout;
			if ( $extra_links ) echo '&nbsp;&nbsp;Links: '.$extra_links;
			echo $after_html;
            echo $after_widget;
        echo "<!--Hypervisor Login Logout end-->";
			return;
		}
		// Less than level_2 user - Hide Dashboard from this User
		echo $link_string_profile;
		echo $link_string_logout;
		if ( $extra_links ) echo '&nbsp;&nbsp;Links: '.$extra_links;
		echo $after_html;
        echo $after_widget;
        echo "<!--Hypervisor Login Logout end-->";
		return;
	}
	// User _NOT_ Logged In
	if ( $options['hide_register'] != 1 ) echo "<a href=\"".get_bloginfo('wpurl')."/wp-login.php?action=register&amp;redirect_to=".$_SERVER['REQUEST_URI']."\" title=\"".__('Register','hypervisor-login-logout')."\">".__('Register','hypervisor-login-logout')."</a>&nbsp;&nbsp;|&nbsp;&nbsp;";
	echo "<a href=\"".get_bloginfo('wpurl')."/wp-login.php?action=login&amp;redirect_to=".$_SERVER['REQUEST_URI']."\" title=\"".__('Login','hypervisor-login-logout')."\">".__('Login','hypervisor-login-logout')."</a>";
	echo $after_html;
     echo $after_widget;
        echo "<!--Hypervisor Login Logout end-->";
	return;
}

function rh_hype_lilo_control () {
	$options = get_option('rh_hidedash_options');
	if ( $_POST['rhhd_submit'] ) {
		$options['sidebar_width'] = $_POST['rhhd_sb_width'];
		$options['center_widget'] = $_POST['ecenter_widget'];
		$options['title'] = $_POST['rhhd_title'];
		update_option('rh_hidedash_options', $options);
		$cur_links = array();
		$new_links = array();
		$cur_links = get_option ( 'rh_hidedash_links_options' );
		if ( !empty ($cur_links) ) { 
			$count=0;
			foreach ( $cur_links as $link ) {
				/* remove unwanted links... if a link is not ticked do not add to new_links array */
				if ( $_POST[$count] <> '1' ) { $count++; continue;}
				$new_links[] = $link;
				$count++;
			}
		}
		// if we posted a new link add it to new_link array
		if ( $_POST['nlink-text'] <> '' ) $new_links[] = array($_POST['nlink-text'] => $_POST['nlink-target']);
		if ( !empty ( $new_links) ) {
			array_unique ( $new_links) ;
			sort ( $new_links);
		}
	update_option ( 'rh_hidedash_links_options', $new_links );
	}
        $title = wp_specialchars(stripslashes($options['title']));
	if ( !wp_specialchars($options['sidebar_width']) ) $options['sidebar_width'] = "160"; 
	?>
	<p style="text-align: center">
	<input type="hidden" name="rhhd_submit" id="rhhd_submit" value="1" />
        <label for="rhhd_title"><?php _e('Title:','hypervisor-login-logout'); ?> <input type="text" id="rhhd_title" name="rhhd_title" value="<?php echo $title; ?>" /></label></p>
	<p style="text-align: center">
	<label for="ecenter_widget"><?php _e('Center widget: ','hypervisor-login-logout'); ?><input type="checkbox" <?php if ( $options["center_widget"] == '1' ) echo 'checked="yes" '?> name="ecenter_widget" id="ecenter_widget" value="1" /></label></p>
	<p style="text-align: center">
	<label for="rhhd_sb_width"><?php _e('Widget width:','hypervisor-login-logout'); ?> <input type="text" size="5" maxlength="5" id="rhhd_sb_width" name="rhhd_sb_width" value="<?php echo wp_specialchars($options['sidebar_width']) ?>" /></label></p>

        <?php
	echo "<h3>". __('Add a link to the widget','hypervisor-login-logout'). "</h3>";
	echo __('Text for a new link','hypervisor-login-logout') . ' :<p><input type="text" name="nlink-text" id="nlink-text" value="" /></p>';
	echo __('Target for a new link','hypervisor-login-logout') . ' :<p><input type="text" name="nlink-target" id="nlink-target" value="" /></p>';
	echo "<h3>" . __('Remove Links','hypervisor-login-logout') . "</h3>";
	echo __('Un-tick to delete','hypervisor-login-logout');
	$all_links = get_option ( 'rh_hidedash_links_options' );
	if ( !empty ($all_links) ) {
		echo '<table border="2" cellpadding="6"><tr>';
		$count = 0;
		$link = array();
		echo '<th></th><th>' . __('Text','hypervisor-login-logout') . '</th><th>' . __('Target','hypervisor-login-logout') . '</th><th></th></tr>';
		foreach ( $all_links as $link ) {
			echo '<tr><td><input type="checkbox" checked="checked"'; echo ' name="'. $count.'" id="link'. $count.'" value="1" /></td><td>'. __(key($link),'hypervisor-login-logout').'</td><td>'. current($link).'</td></tr>';
			$count++;
		}
		echo '</table><br />';
	}
	else _e('<p>No links in database.</p>','hypervisor-login-logout');
	?>
	<p>
	<?php _e('Please visit ','hypervisor-login-logout');
	echo '<a href="tools.php?page=login_out_menu">';
	_e('Login & Out widget settings','hypervisor-login-logout');
	echo '</a> ';
	_e('to adjust other settings.</p>','hypervisor-login-logout');
	_e('Note: You must logout and in again to see changes in widget.','hypervisor-login-logout');
	return;
}

function rh_plugin_init() {
	$plugin_dir = dirname(plugin_basename(__FILE__));
	load_plugin_textdomain( 'hypervisor-login-logout', PLUGINDIR . '/' . $plugin_dir , $plugin_dir );
	register_sidebar_widget('Hypervisor '. __('Login/Logout','hypervisor-login-logout'), 'rh_hype_lilo_widget');
	register_widget_control('Hypervisor '. __('Login/Logout','hypervisor-login-logout'), 'rh_hype_lilo_control');
	return;
}

add_action("plugins_loaded", "rh_plugin_init");
add_action("admin_menu", "rh_plugin_init");

// Hook for adding admin menus
add_action('admin_menu', 'login_and_out_menu');

// action function for above hook
function login_and_out_menu() {
	add_management_page('Login & Out', 'Login & Out', 8, 'login_out_menu', 'login_out_menu');
}

// login_out_menu() displays the page content for the Login & Out admin submenu
function login_out_menu() {
	if ( isset ($_POST['update_loginout']) )  { 
		if ( !wp_verify_nonce ( $_POST['loginout-verify-key'], 'loginout') ) die(__('Failed security check. Reload page and retry','hypervisor-login-logout'));
        if ( $_POST['insert_php'] == 'php' ) update_option ( 'rh_insert_php', '1' ); else update_option ( 'rh_insert_php', '0' );
		$options["display_email"] = $_POST['edisplay_email'];
		$options["hide_register"] = $_POST['ehide_register'];
		$options["hide_option_label"] = $_POST['ehide_option_label'];
		$options["before_html"] = $_POST['ebefore_html'];
		$options["after_html"] = $_POST['eafter_html'];
	        update_option ( 'rh_hidedash_options', $options );

	?><div id="message" class="updated fade"><p><strong><?php _e('Login and Out options updated.','hypervisor-login-logout'); ?></strong></p></div><?php
	} // end if isset
	?>
	<form name="form1" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	<div class="form-field">
	<?php
	echo "<h2>" . __('Login and Out Configuration','hypervisor-login-logout') . "</h2>";
	echo '<input type="hidden" name="loginout-verify-key" id="loginout-verify-key" value="' . wp_create_nonce('loginout') . '" />';
	$options = get_option('rh_hidedash_options');
	$insert_php = get_option ( 'rh_insert_php' );
        $before_html = stripslashes($options['before_html']);
        $after_html = stripslashes($options['after_html']);
	echo "<h3>". __('How to display Login Logout','hypervisor-login-logout'). "</h3>";
	echo "<p>". __('Display the plugin by placing the widget in a sidebar or by inserting "&lt&#63php rh_hype_lilo();&#63&gt" into your template file(s).','hypervisor-login-logout'). "</p>";
        ?>
        <table>
	<tr><td><label for="edisplay_email"><?php _e('Display email address: ','hypervisor-login-logout'); ?></td><td><input type="checkbox" <?php if ( $options["display_email"] == '1' ) echo 'checked="yes" '?> name="edisplay_email" id="edisplay_email" value="1" /></label></td></tr>
	<tr><td><label for="ehide_option_label"><?php _e('Hide option label: ','hypervisor-login-logout'); ?></td><td><input type="checkbox" <?php if ( $options["hide_option_label"] == '1' ) echo 'checked ' ?> name="ehide_option_label" id="ehide_option_label" value="1" /></label></td></tr>
	<tr><td><label for="ehide_register"><?php _e('Hide Register link: ','hypervisor-login-logout'); ?></td><td><input type="checkbox" <?php if ( $options["hide_register"] == '1' ) echo 'checked ' ?> name="ehide_register" id="ehide_register" value="1" /></label>
</table>
	<h3><?php _e('Customise Appearance','hypervisor-login-logout');?></h3>
        <table><tr><td>
	<label for="before_html"><?php _e('Insert before Login Logout HTML: (e.g. any valid HTML)','hypervisor-login-logout'); ?> <input type="text" id="ebefore_html" name="ebefore_html" value="<?php echo wp_specialchars($before_html) ?>" /></label></td></tr>
	<tr><td><label for="after_html"><?php _e('Insert after Login Logout HTML: (e.g. any valid HTML)','hypervisor-login-logout'); ?> <input type="text" id="eafter_html" name="eafter_html" value="<?php echo wp_specialchars($after_html) ?>" /></label></td></tr>
        </table>
	<p class="submit">
	<input type="submit" name="update_loginout" value="<?php _e('Submit!','hypervisor-login-logout'); ?>" />
	</p><br />
	<h3><?php _e('Like this plugin?','hypervisor-login-logout'); ?></h3>
	<?php _e('Please visit our website ','hypervisor-login-logout') ?><a href="http://www.thehypervisor.com">The Hypervisor</a>
	</div>
	</form>
<?php _e('Or consider making a donation','hypervisor-login-logout') ?>.<br />
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick" />
<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
" />
<input type="image" src="https://www.paypal.com/en_US/GB/i/btn/btn_donateCC_LG.gif" name="submit" alt="PayPal - <?php _e('The safer, easier way to pay online','hypervisor-login-logout'); ?>." />
<img alt="" border="0" src="https://www.paypal.com/en_GB/i/scr/pixel.gif" width="1" height="1" />
</form>
<?php
}

?>
