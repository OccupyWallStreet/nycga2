<?php
/*
Plugin Name: jQuery UI Widgets
Plugin URI: http://www.presscoders.com/jquery-ui-widgets
Description: Simple, clean, and flexible way to add jQuery UI widgets to your site pages.
Version: 0.1
Author: David Gwyer
Author URI: http://www.presscoders.com
License: GPLv2
*/

/*  Copyright 2009 David Gwyer (email : d.v.gwyer@presscoders.com)

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
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// @todo
// - See if I can get the jQuery cookie Plugin working to optionally remember the settings of tabs, accordions between page views.
// - Add option to not enqueue any styles at all, so the current theme can specify all jQuery UI styles without any conflicts.
// - Disable custom init code text areas in Plugin settings if the widget checkboxes are not selected.
// - Have a custom CSS reset feature in-case the custom styles get messed up. Or show custom default styles used so they can be copied/pasted.
// - Add in effects for more flexibility.
// - When WordPress 3.5 is out use the default jQuery UI admin styles in core and remove the ones shipped with the Plugin.
// - Link to jQuery demos for each custom initialization tab.

// Note: jquiw_ prefix is derived from [jq]uery [ui] [d]emo
define( "JQUIW_PLUGIN_URL", WP_PLUGIN_URL.'/'.plugin_basename(dirname(__FILE__)) );

/* Plugin hooks. */
add_action( 'wp_head', 'jquiw_initialize_scripts' );
add_action( 'wp_enqueue_scripts', 'jquiw_enqueue_scripts' );
register_activation_hook( __FILE__, 'jquiw_add_defaults' );
register_uninstall_hook( __FILE__, 'jquiw_delete_plugin_options' );
add_action( 'admin_init', 'jquiw_init' );
add_action( 'admin_menu', 'jquiw_add_options_page' );
add_filter( 'plugin_action_links', 'jquiw_plugin_action_links', 10, 2 );
add_action( 'activated_plugin', 'jquiw_save_error' );

/* Define default option settings. */
function jquiw_add_defaults() {

	$tmp = get_option('jquiw_options');
	if( ( (isset($tmp['chk_default_options_db']) && $tmp['chk_default_options_db']=='1')) || (!is_array($tmp)) ) {
		delete_option('jquiw_options');
		$arr = array( "txt_custom_theme_path" => "", "txtar_override_css" => ".ui-widget {\r\nfont-family: inherit;\r\nfont-size: inherit;\r\n}\r\n\r\n.ui-tabs .ui-tabs-panel {\r\npadding: 0 1.4em;\r\n}\r\n\r\n.ui-state-highlight p, .ui-state-error p {\r\nline-height: 1em;\r\n}\r\n\r\n.ui-accordion .ui-accordion-content {\r\npadding: 0 1.4em;\r\n}\r\n\r\n.ui-widget-content p, .ui-widget p {\r\nmargin: 1em 0;\r\n}\r\n\r\n.ui-helper-reset {\r\nline-height: inherit;\r\n}\r\n\r\n.ui-dialog-content {\r\nline-height: 1.3em;\r\n}\r\n\r\n/* Twenty Ten specific fix. */\r\n#content .ui-accordion h3 {\r\nmargin: 0;\r\n}\r\n", "drp_jquery_theme" => "base", "chk_inc_accordion" => "1", "chk_inc_dialog" => "1", "chk_inc_tabs" => "1", "chk_inc_datepicker" => "", "chk_inc_button" => "", "chk_inc_slider" => "", "txtar_tabs_initcode" => "", "txtar_accordion_initcode" => "", "txtar_dialog_initcode" => "", "txtar_button_initcode" => "", "txtar_datepicker_initcode" => "", "txtar_slider_initcode" => "", "txt_custom_theme" => "", "chk_default_options_db" => "" );
		update_option('jquiw_options', $arr);
	}
}

/* Add initialization scripts to the header. */
function jquiw_initialize_scripts() {

	$options = get_option('jquiw_options');

	$default_tabs_init = "\r\n$( \".tabs\" ).tabs();\r\n";
	$default_accordion_init = "\r\n$( \".accordion\" ).accordion();\r\n";
	$default_dialog_init = "\r\n$( \".dialog\" ).dialog();\r\n";
	$default_datepicker_init = "\r\n$( \".datepicker\" ).datepicker();\r\n";
	$default_button_init = "\r\n$( \"a.button, button\" ).button();\r\n";
	$default_slider_init = "\r\n$( \".slider\" ).slider();\r\n";

	echo "<script type=\"text/javascript\">\r\njQuery(document).ready(function($) {\r\n";

	if( isset( $options['chk_inc_tabs'] ) ) {
		echo "// Tabs";
		if( isset( $options['txtar_tabs_initcode'] ) && !empty($options['txtar_tabs_initcode']) )
			echo "\r\n".$options['txtar_tabs_initcode']."\r\n";
		else
			echo $default_tabs_init;
	}
	if( isset( $options['chk_inc_accordion'] ) ) {
		echo "// Accordion";
		if( isset( $options['txtar_accordion_initcode'] ) && !empty($options['txtar_accordion_initcode']) )
			echo "\r\n".$options['txtar_accordion_initcode']."\r\n";
		else
			echo $default_accordion_init;
	}
	if( isset( $options['chk_inc_dialog'] ) ) {
		echo "// Dialog";
		if( isset( $options['txtar_dialog_initcode'] ) && !empty($options['txtar_dialog_initcode']) )
			echo "\r\n".$options['txtar_dialog_initcode']."\r\n";
		else
			echo $default_dialog_init;
	}
	if( isset( $options['chk_inc_button'] ) ) {
		echo "// Button";
		if( isset( $options['txtar_button_initcode'] ) && !empty($options['txtar_button_initcode']) )
			echo "\r\n".$options['txtar_button_initcode']."\r\n";
		else
			echo $default_button_init;
	}
	if( isset( $options['chk_inc_datepicker'] ) ) {
		echo "// Datepicker";
		if( isset( $options['txtar_datepicker_initcode'] ) && !empty($options['txtar_datepicker_initcode']) )
			echo "\r\n".$options['txtar_datepicker_initcode']."\r\n";
		else
			echo $default_datepicker_init;
	}
	if( isset( $options['chk_inc_slider'] ) ) {
		echo "// Slider";
		if( isset( $options['txtar_slider_initcode'] ) && !empty($options['txtar_slider_initcode']) )
			echo "\r\n".$options['txtar_slider_initcode']."\r\n";
		else
			echo $default_slider_init;
	}

	echo "});\r\n</script>\r\n";

	$tmp = get_option('jquiw_options');

	if( isset($tmp['txtar_override_css']) && !empty( $tmp['txtar_override_css'] ) ) {
		echo "<style type=\"text/css\">\r\n";
		echo $tmp['txtar_override_css'];
		echo "\r\n</style>\r\n";
	}
}

/* Register the blog scripts and Plugin settings api. */
function jquiw_init(){

	/* Make sure we always have a theme selected. */
	$tmp = get_option('jquiw_options');
	if( !isset($tmp['drp_jquery_theme']) ) {
		$tmp["drp_jquery_theme"] = 'base';
		update_option('jquiw_options', $tmp);
	}
	register_setting( 'jquiw_plugin_options', 'jquiw_options' );
}

/* Enqueue scripts on front facing pages only. */
function jquiw_enqueue_scripts() {

	$options = get_option('jquiw_options');
	$jquery_theme = empty( $options['drp_jquery_theme'] ) ? 'base' : $options['drp_jquery_theme'];
	$jquery_css_base = 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/themes/'.$jquery_theme.'/jquery-ui.css';

	/* Register jQuery scripts if selected in Plugin options. */
	if( isset( $options['chk_inc_tabs'] ) ) wp_enqueue_script( 'jquery-ui-tabs' );
	if( isset( $options['chk_inc_accordion'] ) ) wp_enqueue_script( 'jquery-ui-accordion' );
	if( isset( $options['chk_inc_dialog'] ) ) wp_enqueue_script( 'jquery-ui-dialog' );
	if( isset( $options['chk_inc_button'] ) ) wp_enqueue_script( 'jquery-ui-button' );
	if( isset( $options['chk_inc_datepicker'] ) ) wp_enqueue_script( 'jquery-ui-datepicker' );
	if( isset( $options['chk_inc_slider'] ) ) wp_enqueue_script( 'jquery-ui-slider' );

	/* Register style sheet. */
	if( empty($options['txt_custom_theme']) ) {
		wp_enqueue_style ( 'jquery-ui-standard-css', $jquery_css_base );
	}
	else {
		/* Enqueue custom theme rolled styles. */
		$upload_dir = wp_upload_dir();
		$relative_path = trim($options['txt_custom_theme'], "/");
		$full_path_url = trailingslashit($upload_dir['baseurl']).$relative_path;
		$full_path_dir = trailingslashit($upload_dir['basedir']).$relative_path;
		if( file_exists($full_path_dir) ) {
			wp_enqueue_style ( 'jquery-ui-custom-css', $full_path_url );
		}
		else {
			wp_enqueue_style ( 'jquery-ui-standard-css', $jquery_css_base );
		}
	}
}

/* Add menu page. */
function jquiw_add_options_page() {
	$page = add_options_page('jQuery UI Widgets Options Page', 'jQuery UI Widgets', 'manage_options', __FILE__, 'jquiw_render_form');

	add_action( 'load-'.$page, "jquiw_plugin_admin_scripts" );
}

/* Enqueue plugin options page scripts. */
function jquiw_plugin_admin_scripts() {

	/* Use built-in WordPress jQuery UI libraries. */
	wp_enqueue_script( 'jquery-ui-tabs' );

	/* Register and enqueue jQuery plugin script. */
	wp_register_script( 'jquiw-plugin-script', plugins_url( 'jquery-ui-widgets.js' , __FILE__ ), array( 'jquery-ui-core' ) );
	wp_enqueue_script( 'jquiw-plugin-script' );

	if ( 'classic' == get_user_option( 'admin_color') )
		wp_enqueue_style ( 'jquery-ui-css', plugin_dir_url( __FILE__ ).'jquery-ui-classic.css' );
	else
		wp_enqueue_style ( 'jquery-ui-css', plugin_dir_url( __FILE__ ).'jquery-ui-fresh.css' );
}

function jquiw_save_error(){
    update_option('pc_plugin_error', ob_get_contents());
}

/* Delete options table entries ONLY when Plugin deactivated AND deleted. */
function jquiw_delete_plugin_options() {
	delete_option('jquiw_options');
}

/* Draw the menu page itself. */
function jquiw_render_form() {
	?>
	<div class="wrap">
		<div class="icon32" id="icon-options-general"><br></div>
		<h2>jQuery UI Widgets Options</h2>

		<form method="post" action="options.php">
			<?php settings_fields('jquiw_plugin_options'); ?>
			<?php $options = get_option('jquiw_options'); ?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">Select Theme</th>
					<td>
						<select name='jquiw_options[drp_jquery_theme]'>
							<option value='base' <?php selected('star', $options['drp_jquery_theme']); ?>>Base</option>
							<option value='black-tie' <?php selected('black-tie', $options['drp_jquery_theme']); ?>>Black Tie</option>
							<option value='blitzer' <?php selected('blitzer', $options['drp_jquery_theme']); ?>>Blitzer</option>
							<option value='cupertino' <?php selected('cupertino', $options['drp_jquery_theme']); ?>>Cupertino</option>
							<option value='dark-hive' <?php selected('dark-hive', $options['drp_jquery_theme']); ?>>Dark Hive</option>
							<option value='dot-luv' <?php selected('dot-luv', $options['drp_jquery_theme']); ?>>Dot Luv</option>
							<option value='eggplant' <?php selected('eggplant', $options['drp_jquery_theme']); ?>>Eggplant</option>
							<option value='excite-bike' <?php selected('excite-bike', $options['drp_jquery_theme']); ?>>Excite Bike</option>
							<option value='flick' <?php selected('flick', $options['drp_jquery_theme']); ?>>Flick</option>
							<option value='hot-sneaks' <?php selected('hot-sneaks', $options['drp_jquery_theme']); ?>>Hot Sneaks</option>
							<option value='humanity' <?php selected('humanity', $options['drp_jquery_theme']); ?>>Humanity</option>
							<option value='le-frog' <?php selected('le-frog', $options['drp_jquery_theme']); ?>>Le Frog</option>
							<option value='mint-choc' <?php selected('mint-choc', $options['drp_jquery_theme']); ?>>Mint Choc</option>
							<option value='overcast' <?php selected('overcast', $options['drp_jquery_theme']); ?>>Overcast</option>
							<option value='pepper-grinder' <?php selected('pepper-grinder', $options['drp_jquery_theme']); ?>>Pepper Grinder</option>
							<option value='redmond' <?php selected('redmond', $options['drp_jquery_theme']); ?>>Redmond</option>
							<option value='smoothness' <?php selected('smoothness', $options['drp_jquery_theme']); ?>>Smoothness</option>
							<option value='south-street' <?php selected('south-street', $options['drp_jquery_theme']); ?>>South Street</option>
							<option value='start' <?php selected('start', $options['drp_jquery_theme']); ?>>Start</option>
							<option value='sunny' <?php selected('sunny', $options['drp_jquery_theme']); ?>>Sunny</option>
							<option value='swanky-purse' <?php selected('swanky-purse', $options['drp_jquery_theme']); ?>>Swanky Purse</option>
							<option value='trontastic' <?php selected('trontastic', $options['drp_jquery_theme']); ?>>Trontastic</option>
							<option value='ui-darkness' <?php selected('ui-darkness', $options['drp_jquery_theme']); ?>>UI Darkness</option>
							<option value='ui-lightness' <?php selected('ui-lightness', $options['drp_jquery_theme']); ?>>UI Lightness</option>
							<option value='vader' <?php selected('vader', $options['drp_jquery_theme']); ?>>Vader</option>
						</select><br />
						<span class="description">Choose a standard jQuery UI theme to render widgets. Preview these themes on the jQuery UI <a href="http://jqueryui.com/themeroller/#themeGallery" target="_blank">ThemeRoller page</a>.</span><br /><br />
						<input class="widefat" type="text" size="57" name="jquiw_options[txt_custom_theme]" value="<?php echo $options['txt_custom_theme']; ?>" /><br />
						<?php $upload_dir = wp_upload_dir(); ?>

						<span class="description">Create a <a href="http://jqueryui.com/themeroller/" target="_blank">custom theme</a> to override the standard theme. Upload to <code style="font-style:normal;"><?php echo $upload_dir['baseurl']; ?>/</code> and enter the path/name of the custom stylesheet above, relative to this folder. See the Plugin <a href="http://wordpress.org/extend/plugins/jquery-ui-widgets/faq/" target="_blank">FAQ</a> for detailed instructions on using custom themes.</span><br />
						<?php
							/* Enqueue custom theme rolled styles. */
							$upload_dir = wp_upload_dir();
							$relative_path = trim($options['txt_custom_theme'], "/");
							$full_path_url = trailingslashit($upload_dir['baseurl']).$relative_path;
							$full_path_dir = trailingslashit($upload_dir['basedir']).$relative_path;
							if( !file_exists($full_path_dir) )
								echo "<div class=\"error inline\">Cannot find the file: <code style=\"background-color: #ffebe8;\">".$full_path_url."</code>. Reverting to the standard theme. Please enter a valid custom stylesheet path.</div>";
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Add Widgets</th>
					<td>
						<label><input name="jquiw_options[chk_inc_tabs]" type="checkbox" value="1" <?php if (isset($options['chk_inc_tabs'])) { checked('1', $options['chk_inc_tabs']); } ?> /> Tabs</label><br />
						<label><input name="jquiw_options[chk_inc_accordion]" type="checkbox" value="1" <?php if (isset($options['chk_inc_accordion'])) { checked('1', $options['chk_inc_accordion']); } ?> /> Accordion</label><br />
						<label><input name="jquiw_options[chk_inc_dialog]" type="checkbox" value="1" <?php if (isset($options['chk_inc_dialog'])) { checked('1', $options['chk_inc_dialog']); } ?> /> Dialog</label><br />
						<label><input name="jquiw_options[chk_inc_button]" type="checkbox" value="1" <?php if (isset($options['chk_inc_button'])) { checked('1', $options['chk_inc_button']); } ?> /> Button</label><br />
						<label><input name="jquiw_options[chk_inc_datepicker]" type="checkbox" value="1" <?php if (isset($options['chk_inc_datepicker'])) { checked('1', $options['chk_inc_datepicker']); } ?> /> Datepicker</label><br />
						<label><input name="jquiw_options[chk_inc_slider]" type="checkbox" value="1" <?php if (isset($options['chk_inc_slider'])) { checked('1', $options['chk_inc_slider']); } ?> /> Slider</label><br />
						<span class="description">Select the jQuery UI widget(s) that will be added to the site header.</span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Customize Initialization</th>
					<td>
						<div class="jquiw-tabs" style="margin-bottom:4px;">
							<ul>
								<li><a href="#tabs-1">Tabs</a></li>
								<li><a href="#tabs-2">Accordion</a></li>
								<li><a href="#tabs-3">Dialog</a></li>
								<li><a href="#tabs-4">Button</a></li>
								<li><a href="#tabs-5">Datepicker</a></li>
								<li><a href="#tabs-6">Slider</a></li>
							</ul>
							<div id="tabs-1">
								<textarea class="widefat" style="font-family: Lucida Console;" name="jquiw_options[txtar_tabs_initcode]" rows="5" type='textarea'><?php echo $options['txtar_tabs_initcode']; ?></textarea><br />
								<p style="margin:5px 0 0 0;">Tabs init code defaults to <code>$( ".tabs" ).tabs();</code> if text box above empty.</p>
							</div>
							<div id="tabs-2">
								<textarea class="widefat" style="font-family: Lucida Console;" name="jquiw_options[txtar_accordion_initcode]" rows="5" type='textarea'><?php echo $options['txtar_accordion_initcode']; ?></textarea><br />
								<p style="margin:5px 0 0 0;">Accordion init code defaults to <code>$( ".accordion" ).accordion();</code> if text box above empty.</p>
							</div>
							<div id="tabs-3">
								<textarea class="widefat" style="font-family: Lucida Console;" name="jquiw_options[txtar_dialog_initcode]" rows="5" type='textarea'><?php echo $options['txtar_dialog_initcode']; ?></textarea><br />
								<p style="margin:5px 0 0 0;">Dialog init code defaults to <code>$( ".dialog" ).dialog();</code> if text box above empty.</p>
							</div>
							<div id="tabs-4">
								<textarea class="widefat" style="font-family: Lucida Console;" name="jquiw_options[txtar_button_initcode]" rows="5" type='textarea'><?php echo $options['txtar_button_initcode']; ?></textarea><br />
								<p style="margin:5px 0 0 0;">Button init code defaults to <code>$( "a.button, button" ).button();</code> if text box above empty.</p>
							</div>
							<div id="tabs-5">
								<textarea class="widefat" style="font-family: Lucida Console;" name="jquiw_options[txtar_datepicker_initcode]" rows="5" type='textarea'><?php echo $options['txtar_datepicker_initcode']; ?></textarea><br />
								<p style="margin:5px 0 0 0;">Datepicker init code defaults to <code>$( ".datepicker" ).datepicker();</code> if text box above empty.</p>
							</div>
							<div id="tabs-6">
								<textarea class="widefat" style="font-family: Lucida Console;" name="jquiw_options[txtar_slider_initcode]" rows="5" type='textarea'><?php echo $options['txtar_slider_initcode']; ?></textarea><br />
								<p style="margin:5px 0 0 0;">Slider init code defaults to <code>$( ".slider" ).slider();</code> if text box above empty.</p>
							</div>
						</div>
						<span class="description">Default code is automatically added for each active widget, but this is replaced by custom code if specified above.</span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Override Theme CSS</th>
					<td>
						<textarea class="widefat" style="font-family: Lucida Console;" name="jquiw_options[txtar_override_css]" rows="5" type='textarea'><?php echo $options['txtar_override_css']; ?></textarea><br /><span class="description">Edit the custom CSS rules above to override the current jQuery UI theme (whether a standard or custom theme). This is a great place to tweak the theme styles.</span>
					</td>
				</tr>
				<tr><td colspan="2"><div style="margin-top:0;"></div></td></tr>
				<tr valign="top" style="border-top:#dddddd 1px solid;">
					<th scope="row">Database Options</th>
					<td>
						<label><input name="jquiw_options[chk_default_options_db]" type="checkbox" value="1" <?php if (isset($options['chk_default_options_db'])) { checked('1', $options['chk_default_options_db']); } ?> /> Restore defaults upon Plugin deactivation/reactivation</label>
						<br /><span class="description">Only check this if you want to reset Plugin settings upon reactivation.</span>
					</td>
				</tr>
			</table>
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>

		<div style="margin-top:15px;">
			<p style="margin-bottom:10px;">If you find this Plugin useful <b><em>please</em></b> consider making a <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=MTVAN3NBV3HCA" target="_blank">donation</a> to support continued development.</p>
		</div>

		<div style="clear:both;">
			<p>
				<a href="http://www.facebook.com/PressCoders" title="Our Facebook page" target="_blank"><img src="<?php echo plugins_url(); ?>/jquery-ui-widgets/images/facebook.png" /></a><a href="http://www.twitter.com/dgwyer" title="Follow on Twitter" target="_blank"><img src="<?php echo plugins_url(); ?>/jquery-ui-widgets/images/twitter.png" /></a>&nbsp;<input class="button" style="vertical-align:12px;" type="button" value="Visit Our Site" onClick="window.open('http://www.presscoders.com')">&nbsp;<input class="button" style="vertical-align:12px;" type="button" value="Free Responsive Theme!" onClick="window.open('http://www.presscoders.com/designfolio')">
			</p>
		</div>

	</div>
	<?php	
}

/* Display a Settings link on the main Plugins page. */
function jquiw_plugin_action_links( $links, $file ) {

	if ( $file == plugin_basename( __FILE__ ) ) {
		$posk_links = '<a href="'.get_admin_url().'options-general.php?page=jquery-ui-widgets/jquery-ui-widgets.php">'.__('Settings').'</a>';
		/* Make the 'Settings' link appear first. */
		array_unshift( $links, $posk_links );
	}

	return $links;
}

?>