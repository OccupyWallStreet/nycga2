<?php
/*
Plugin Name: WP Featured Content Slider
Plugin URI: http://www.iwebix.de/featured-content-slider-wordpress-plugin/
Description: This Plugin is used to show your featured Posts/Pages with thumbnails in a nice slider.
Version: 2.1
Author: Dennis Nissle, IWEBIX
Author URI: http://www.iwebix.de/
*/


/* options page */

$options_page = get_option('siteurl') . '/wp-admin/admin.php?page=upthemes/wp-featured-content-slider/options.php';

function slider_options_page() {
	//add_options_page('Featured Content Slider Options', 'Featured Content Slider', 10, 'wp-featured-content-slider/options.php');
}

//add_action('admin_menu', 'slider_options_page');

function add_feat_scripts() {
    if ( !is_admin() ) {
	wp_register_script('jquery.cycle', get_bloginfo('url') . '/wp-content/plugins/wp-featured-content-slider/scripts/jquery.cycle.all.2.72.js', array('jquery'), '1.3' );
	wp_enqueue_script('jquery.cycle');
    }
}

add_action('wp_enqueue_scripts', 'add_feat_scripts');


function cut_text_feat($text, $chars, $points = "...") {
	$length = strlen($text);
	if($length <= $chars) {
		return $text;
	} else {
		return substr($text, 0, $chars)." ".$points;
	}
}

add_action("admin_init", "feat_init");
add_action('save_post', 'save_feat');

function feat_init(){
    add_meta_box("feat_slider", "Featured Content Slider Options", "feat_meta", "post", "normal", "high");
    add_meta_box("feat_slider", "Featured Content Slider Options", "feat_meta", "page", "normal", "high");
}

function feat_meta(){
    global $post;
    $custom = get_post_custom($post->ID);
    $feat_slider = $custom["feat_slider"][0];
?>
	<div class="inside">
		<table class="form-table">
			<tr>
				<th><label for="feat_slider">Feature in Featured Content Slider?</label></th>
				<td><input type="checkbox" name="feat_slider" value="1" <?php if($feat_slider == 1) { echo "checked='checked'";} ?></td>
			</tr>
		</table>
	</div>
<?php
}

function save_feat(){
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
    return $post_id;
    global $post;
    if($post->post_type == "post" || $post->post_type == "page") {
	update_post_meta($post->ID, "feat_slider", $_POST["feat_slider"]);
    }
}

function insert_feat($atts, $content = null) {
    include (ABSPATH . '/wp-content/plugins/wp-featured-content-slider/content-slider.php');
}
add_shortcode("featslider", "insert_feat");

$img_width = get_option('img_width');

if(empty($img_width)) {
	$img_width = 320;
}

$img_height = get_option('img_height');

if(empty($img_height)) {
	$img_height = 200;
}

if (function_exists('add_image_size')) { 
	add_image_size( 'feat_slider', $img_width, $img_height, true ); 
}

function get_wp_generated_thumb($position) {
	$thumb = get_the_post_thumbnail($post_id, $position);
	$thumb = explode("\"", $thumb);
	return $thumb[5];
}

//Check for Post Thumbnail Support

add_theme_support( 'post-thumbnails' );

?>