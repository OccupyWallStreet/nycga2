<?php
/*
Plugin Name: My Custom CSS
Plugin URI: http://www.darkwolf.it/
Description: With this plugin you can put custom css code without edit your theme and/or your plugins (really useful in case of any theme/plugin update).
It contain also a syntax color and tab support for write a good css code.
You can see in action (source code) here: http://vegamami.altervista.org/ :)
Author: Salvatore Noschese - DarkWolf
Version: 0.4
Author URI: http://www.darkwolf.it/
*/

// Prevent Direct Access with homepage redirect */
if (!defined('DB_NAME')) {
	header('Location: ../../../');
}

function add_my_custom_css() {
	$mycustomcss = get_option('my_custom_css');
	if (!empty($mycustomcss)) {
		echo "\n<!-- My Custom CSS Start -->\n<style type=\"text/css\">\n/* Plugin Author: Salvatore Noschese\nDarkWolf: http://www.darkwolf.it/ */\n\n".$mycustomcss."\n</style>\n<!-- My Custom CSS End -->\n";
	}
}

function mccss_admin() {
	$icon_url = WP_PLUGIN_URL."/".str_replace(basename( __FILE__),"",plugin_basename(__FILE__))."css-icon.png";
	$plugin_page = add_menu_page(__('My Custom CSS Panel','mccss'),__('My Custom CSS','mccss'), 'manage_options', 'my_custom_css', 'mccss_options', $icon_url, 61);
	add_action( 'admin_init', 'register_settings_mccss' ); 	
	add_action( 'admin_head-'. $plugin_page, 'mccss_syntax' );
}

function mccss_syntax() { ?>
<!-- Syntax Support Start -->
<link type="text/css" rel="stylesheet" href="<?php echo WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)); ?>syntax/codemirror.css"></link>
<link type="text/css" rel="stylesheet" href="<?php echo WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)); ?>syntax/default.css"></link>
<script language="javascript" src="<?php echo WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)); ?>syntax/codemirror.js"></script>
<script language="javascript" src="<?php echo WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)); ?>syntax/css.js"></script>
<!-- Syntax Support End -->
<?php }

// register settings
function register_settings_mccss(){
	register_setting('mccss_settings','my_custom_css');
}
function mccss_options() {
?>
<div class="wrap">
	<h2><?php _e('My Custom CSS Options','mccss')?></h2>
	<form method="post" action="options.php">
	<?php settings_fields( 'mccss_settings' ); ?>
	<p><?php _e('Custom CSS Code:','mccss'); ?></p>
	<textarea name="my_custom_css" id="my_custom_css" dir="ltr" cols="100" rows="10" class="css"><?php echo get_option('my_custom_css');?></textarea>
	<script language="javascript">var editor = CodeMirror.fromTextArea(document.getElementById("my_custom_css"), { lineNumbers: true });</script>
	<p class="submit">
    	<input type="submit" class="button-primary" value="<?php _e('Save') ?>" />
   	</p>
	</form>
</div>
<?php 
}

add_action('admin_menu', 'mccss_admin');
add_action('wp_head', 'add_my_custom_css');
load_plugin_textdomain('mccss', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );

?>