<?php
/*
Plugin Name: BuddyPress Mobile
Plugin URI: http://buddychimp.com
Description: A plugin and theme for BuddyPress for optimized viewing on Apple's <a href="http://www.apple.com/iphone/">iPhone</a> and <a href="http://www.apple.com/ipodtouch/">iPod touch</a>. Also works with some Android and blackberry devices..
Author: modemlooper
Version: 1.5.2.3
Author URI: http://www.buddychimp.com/members/modemlooper
*/

/*
 * Make sure BuddyPress is loaded before we do anything.
 */
if ( !function_exists( 'bp_core_install' ) ) {
	require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
	if ( is_plugin_active( 'buddypress/bp-loader.php' ) ) {
		require_once ( WP_PLUGIN_DIR . '/buddypress/bp-loader.php' );
	} else {
		add_action( 'admin_notices', 'buddypress_mobile_install_buddypress_notice' );
		return;
	}
}

define( 'BUDDYPRESS_MOBILE_VERSION', '1.5.2' );

/*
 * admin links
 */
require ( dirname( __FILE__ ) . '/admin.php' );

function buddypress_mobile_install_buddypress_notice() {
	echo '<div id="message" class="error fade bp-mobile-upgraded"><p style="line-height: 150%">';
	_e('<strong>BuddyPress Mobile</strong></a> requires the BuddyPress plugin to work. Please <a href="http://buddypress.org/download">install BuddyPress</a> first, or <a href="plugins.php">deactivate BuddyPress Mobile</a>.');
	echo '</p></div>';
}



require_once( 'include/compat.php' );

class bpMobilePlugin{
	var $iphone;
	var $ipad;

	function bpMobilePlugin(){
		$this->iphone = false;
		add_action('plugins_loaded',array(&$this,'detectiPhone'));
		add_filter('stylesheet',array(&$this,'get_stylesheet'));
		add_filter('template',array(&$this,'get_template'));
		add_filter( 'theme_root', array(&$this, 'theme_root') );
		add_filter( 'theme_root_uri', array(&$this, 'theme_root_uri') );
		add_filter( 'template', array(&$this, 'get_template') );
	}

	function detectiPhone($query){
		$container = $_SERVER['HTTP_USER_AGENT'];
		$useragents = array (
			"iPhone",      		// Apple iPhone
			"iPod",     		// Apple iPod touch
			"psp",				// psp experimental
			"Xbox",				// xbox experimental
			"incognito",    	// Other iPhone browser
			"webmate",     		// Other iPhone browser
			"Android",     		// 1.5+ Android
			"dream",     		// Pre 1.5 Android
			"CUPCAKE",      	// 1.5+ Android
			"blackberry9500",   // Storm
			"blackberry9530",   // Storm
			"blackberry9520",   // Storm v2
			"blackberry9550",   // Storm v2
			"blackberry 9700",  
			"blackberry 9800", 	//curve
			"blackberry 9850",
			"webOS",    		// Palm Pre Experimental
			"s8000",     		// Samsung Dolphin browser
			"bada",      		// Samsung Dolphin browser
			"Googlebot-Mobile"  // the Google mobile crawler
		);
		$ipadagents = array (
			"iPad"
		);
		$this->iphone = false;
		$this->ipad = false;
		foreach ( $useragents as $useragent ) {
			if (preg_match("/".$useragent."/i",$container) && $_COOKIE['bpthemeswitch'] != 'normal'){
				$this->iphone = true;

			}
		}
		foreach ( $ipadagents as $ipadagent ) {
			if (preg_match("/".$ipadagent."/i",$container) && $_COOKIE['bpthemeswitch'] != 'normal'){
				$this->ipad = true;

			}
		}

	}



	function get_stylesheet($stylesheet) {
		if($this->iphone){
			return 'default';
		}else if ($this->ipad){
			return 'ipad';
		}else{
			return $stylesheet;
		}
	}

	function get_template($template) {
		if($this->iphone){
			return 'default';
		}else if ($this->ipad){
			return 'ipad';
		}else{
			return $template;
		}
	}

	function get_template_directory( $value ) {
		$theme_root = compat_get_plugin_dir( 'buddypress-mobile' );
		if ($this->iphone || $this->ipad ) {
			return $theme_root . '/themes';
		} else {
			return $value;
		}
	}

	function theme_root( $path ) {
		$theme_root = compat_get_plugin_dir( 'buddypress-mobile' );
		if ($this->iphone  || $this->ipad) {
			return $theme_root . '/themes';
		} else {
			return $path;
		}
	}

	function theme_root_uri( $url ) {
		if ($this->iphone  || $this->ipad) {
			$dir = compat_get_plugin_url( 'buddypress-mobile' ) . "/themes";
			return $dir;
		} else {
			return $url;
		}
	}

}
$bp_mobile = new bpMobilePlugin();

function bp_mobile_addFooterSwitch($query){

	$container = $_SERVER['HTTP_USER_AGENT'];
	$useragents = array (
		"iPhone",
		"iPad",
		"iPod",
		"Android",
		"blackberry9500",
		"blackberry9530",
		"blackberry9520",
		"blackberry9550",
		"blackberry9800",
		"webOS"
	);
	false;
	foreach ( $useragents as $useragent ) {
		if (eregi($useragent,$container)){
			echo '<div id="footer-switch" style="margin:40px 0">
	    	<p><a href="" style="font-size:400%" id="theme-switch-site">view mobile site</a></p>
		</div><!-- #footer -->';

		}
	}
}
add_action('wp_footer', 'bp_mobile_addFooterSwitch');

function admob_footer_code(){
if (get_option('admob-on-off')==1 && get_option('admob')==true) {
?>
<div id="bp-mobile-footer-ads">
    <script type="text/javascript">
		var admob_vars = {
 		pubid: <?php 
 			$adcode = get_option('admob');
 			echo "'" . $adcode . "'";
 		?>, // publisher id
 		bgcolor: '000000', // background color (hex)
 		text: 'FFFFFF', // font-color (hex)
 		test: false  // test mode, set to false to receive live ads
};
	</script>
	<script type="text/javascript" src="http://mmv.admob.com/static/iphone/iadmob.js"></script>
</div>
<?php
}
}
add_action('bp_after_footer', 'admob_footer_code');


function bp_mobile_insert_head() {
?>
<style type="text/css">
#footer-switch {
	text-align: center;
	padding-bottom: 20px;
}
</style>

<?php
}
add_action('wp_head', 'bp_mobile_insert_head');

function bp_mobile_scripts() {
	wp_enqueue_script( "buddypress-mobile", path_join(WP_PLUGIN_URL, basename( dirname( __FILE__ ) )."/themes/default/theme.js"), array( 'jquery' ) );
	if (get_option('add2homescreen')==1) {
		wp_enqueue_script( "add2home-mobile", path_join(WP_PLUGIN_URL, basename( dirname( __FILE__ ) )."/themes/default/add2home.js") );
	}
}
add_action('wp_print_scripts', 'bp_mobile_scripts');


function bp_mobile_styles() {
	$container = $_SERVER['HTTP_USER_AGENT'];
	$useragents = array (
			"iPhone",      		// Apple iPhone
			"iPod",     		// Apple iPod touch
			"psp",				// psp experimental
			"Xbox",				// xbox experimental
			"incognito",    	// Other iPhone browser
			"webmate",     		// Other iPhone browser
			"Android",     		// 1.5+ Android
			"dream",     		// Pre 1.5 Android
			"CUPCAKE",      	// 1.5+ Android
			"blackberry9500",   // Storm
			"blackberry9530",   // Storm
			"blackberry9520",   // Storm v2
			"blackberry9550",   // Storm v2
			"blackberry 9700",  
			"blackberry 9800", 	//curve
			"blackberry 9850",
			"webOS",    		// Palm Pre Experimental
			"s8000",     		// Samsung Dolphin browser
			"bada",      		// Samsung Dolphin browser
			"Googlebot-Mobile"  // the Google mobile crawler	
			);
	
	
	
	$ipadagents = array (
			"iPad"
		);
		
		false;
	
	foreach ( $useragents as $useragent ) {
		if (eregi($useragent,$container) && $_COOKIE['bpthemeswitch'] != 'normal'){
		echo '<link rel="stylesheet" id="main-css"  href="'.WP_PLUGIN_URL.'/buddypress-mobile/themes/default/mobile.css" type="text/css">';
		if (get_option('style') == 'black'){
		wp_enqueue_style( "black", path_join(WP_PLUGIN_URL, basename( dirname( __FILE__ ) )."/themes/default/black.css") );
		}			
		}
	}
	
	
	foreach ( $ipadagents as $ipadagent ) {
			if (eregi($ipadagent,$container) && $_COOKIE['bpthemeswitch'] != 'normal'){
		echo '<link rel="stylesheet" id="main-css"  href="'.WP_PLUGIN_URL.'/buddypress-mobile/themes/ipad/mobile.css" type="text/css">';
		if (get_option('style') == 'black'){
		wp_enqueue_style( "black", path_join(WP_PLUGIN_URL, basename( dirname( __FILE__ ) )."/themes/ipad/black.css") );
		}			
		}
		}

}
add_action('wp_print_styles', 'bp_mobile_styles');


if (get_option('lazyload')==1 && $_COOKIE['bpthemeswitch'] != 'normal') {
	function jquery_lazy_load_headers() {
		$plugin_path = plugins_url('/', __FILE__);
		$lazy_url = $plugin_path . 'themes/default/jquery.lazyload.mini.js';
		wp_enqueue_script('jquerylazyload', $lazy_url);
	}

	function jquery_lazy_load_ready() {
		$placeholdergif = plugins_url('themes/default/images/grey.gif', __FILE__);
		echo <<<EOF
<script type="text/javascript">
jQuery(document).ready(function($){
  if (navigator.platform == "iPad") return;
  jQuery(".avatar").not(".cycle .avatar").lazyload({
    effect:"fadeIn",
    placeholder: "$placeholdergif"
  });
});
</script>
EOF;
	}
	add_action('wp_head', 'jquery_lazy_load_headers', 5);
	add_action('wp_head', 'jquery_lazy_load_ready', 12);
}

function register_mobile_menu() {
	register_nav_menu( 'mobile-menu', __( 'Mobile Menu' ) );
}
add_action( 'init', 'register_mobile_menu' );
?>