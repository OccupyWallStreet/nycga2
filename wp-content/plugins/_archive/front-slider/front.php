<?php
/*
Plugin Name: Front Slider
Plugin URI: http://www.iwebix.de/front-slider-wordpress-plugin/
Description: Front Slider adds a fancy Javascript Slideshow to your Blog/Homepage.
Version: 2.5
Author: Dennis Nissle, IWEBIX
Author URI: http://www.iwebix.de/
*/

$front_sl_options_page = get_option('siteurl') . '/wp-admin/admin.php?page=front-slider/options.php';

function front_sl_options_page() {
	add_options_page('Front Slider Options', 'Front Slider', 10, 'front-slider/options.php');
}

add_action('admin_menu', 'front_sl_options_page');

function front_sl_add_scripts() {
    if ( !is_admin() ) {
	wp_register_script('jquery.slider', WP_PLUGIN_URL . '/front-slider/scripts/slider.js', array('jquery'), '1.3' );
	wp_enqueue_script('jquery.slider');
    }
}

add_action('wp_enqueue_scripts', 'front_sl_add_scripts');


function front_sl_cut_text($text, $chars, $points = "...") {
	$length = strlen($text);
	if($length <= $chars) {
		return $text;
	} else {
		return substr($text, 0, $chars)." ".$points;
	}
}

add_action("admin_init", "front_sl_init");
add_action('save_post', 'front_sl_save');

function front_sl_init(){
    add_meta_box("front_slider", "Front Slider Options", "front_sl_meta", "post", "normal", "high");
    add_meta_box("front_slider", "Front Slider Options", "front_sl_meta", "page", "normal", "high");
}

function front_sl_meta(){
    global $post;
    $front_sl_custom = get_post_custom($post->ID);
    $front_sl_slider = $front_sl_custom["front_sl_slider"][0];
?>
	<div class="inside">
		<table class="form-table">
			<tr>
				<th><label for="front_sl_slider">Feature in Front Slider?</label></th>
				<td><input type="checkbox" name="front_sl_slider" value="1" <?php if($front_sl_slider == 1) { echo "checked='checked'";} ?></td>
			</tr>
		</table>
	</div>
<?php
}

function front_sl_save(){
	
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
	return $post_id;
	global $post;
	
	if($post->post_type == "post" || $post->post_type == "page") {
		
		if(isset($_POST["front_sl_slider"])) {
			
			update_post_meta($post->ID, "front_sl_slider", $_POST["front_sl_slider"]);
		
		} else {
			
			delete_post_meta($post->ID, "front_sl_slider", '');
			
		}
		
	}
    
}

function front_sl_insert($atts, $content = null) {
	
    include (ABSPATH . '/wp-content/plugins/front-slider/front-slider.php');
    
}

add_shortcode("frontslider", "front_sl_insert");

$front_sl_img_width = get_option('front_sl_img_width');

if(empty($front_sl_img_width)) {
	$front_sl_img_width = 250;
}

$front_sl_img_height = get_option('front_sl_img_height');

if(empty($front_sl_img_height)) {
	$front_sl_img_height = 150;
}

if (function_exists('add_image_size')) { 
	add_image_size('front_size', $front_sl_img_width, $front_sl_img_height, true );
	add_image_size('front_sl_thumb', 100, 75, true ); 
}

function front_sl_get_thumb($position) {
	$thumb = get_the_post_thumbnail($post_id, $position);
	$thumb = explode("\"", $thumb);
	return $thumb[5];
}

//Check for Post Thumbnail Support

add_theme_support( 'post-thumbnails' );

function front_sl_get_dynamic_class() {
        
        $class = explode("http://", get_bloginfo("url"));
	$class = explode(".", $class[1]);
        $class = $class[0];
        return $class . "_front";

}

?>