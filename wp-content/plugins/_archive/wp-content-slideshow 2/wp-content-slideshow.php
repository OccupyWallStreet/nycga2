<?php
/*
Plugin Name: WP Content Slideshow
Plugin URI: http://www.iwebix.de/wp-content-slideshow-javascript-slideshow-plugin/
Description: This Plugin shows up to 5 Posts or Pages with a short description and a title at the right, and a image for every post/page on the left.
Version: 2.3
Author: Dennis Nissle, IWEBIX
Author URI: http://www.iwebix.de/
*/

/* options page */


$options_page = get_option('siteurl') . '/wp-admin/admin.php?page=wp-content-slideshow/options.php';
function slideshow_options_page() {
	add_options_page('WP Content Slideshow Options', 'WP Content Slideshow', 10, 'wp-content-slideshow/options.php');
}

add_action('admin_menu', 'slideshow_options_page');

function add_content_scripts() {
    if ( !is_admin() ) {
	wp_register_script('jquery.cycle', get_bloginfo('url') . '/wp-content/plugins/wp-content-slideshow/scripts/jquery.cycle.all.2.72.js', array('jquery'), '1.3' );
	wp_enqueue_script('jquery.cycle');
	wp_register_script('jquery.slideshow', get_bloginfo('url') . '/wp-content/plugins/wp-content-slideshow/scripts/slideshow.js', array('jquery'), '1.3' );
	wp_enqueue_script('jquery.slideshow');
    }
}

add_action('wp_enqueue_scripts', 'add_content_scripts');

function cut_content_feat($text, $chars, $points = "...") {
	$length = strlen($text);
	if($length <= $chars) {
		return $text;
	} else {
		return substr($text, 0, $chars)." ".$points;
	}
}

add_action("admin_init", "content_init");
add_action('save_post', 'save_content');


function content_init(){
    add_meta_box("content_slider", "WP Content Slideshow Options", "content_meta", "post", "normal", "high");
    add_meta_box("content_slider", "WP Content Slideshow Options", "content_meta", "page", "normal", "high");
}

function content_meta(){
    global $post;
    $custom = get_post_custom($post->ID);
    $content_slider = $custom["content_slider"][0];
?>
	<div class="inside">
		<table class="form-table">
			<tr>
				<th><label for="content_slider">Feature in WP Content Slideshow?</label></th>
				<td><input type="checkbox" name="content_slider" value="1" <?php if($content_slider == 1) { echo "checked='checked'";} ?></td>
			</tr>
		</table>
	</div>
<?php
}

function save_content(){
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
    return $post_id;
    global $post;
    if($post->post_type == "post" || $post->post_type == "page") {
	update_post_meta($post->ID, "content_slider", $_POST["content_slider"]);
    }
}

function insert_content($atts, $content = null) {
    include (ABSPATH . '/wp-content/plugins/wp-content-slideshow/content-slideshow.php');
}
add_shortcode("contentSlideshow", "insert_content");

$img_width = get_option('content_img_width');

if(empty($img_width)) {
	$img_width = 300;
}

$img_height = get_option('content_height');

if(empty($img_height)) {
	$img_height = 250;
}

if (function_exists('add_image_size')) { 
	add_image_size( 'content_slider', $img_width, $img_height, true ); 
}

function get_generated_thumb($position) {
	$thumb = get_the_post_thumbnail($post_id, $position);
	$thumb = explode("\"", $thumb);
	return $thumb[5];
}

//Check for Post Thumbnail Support

add_theme_support( 'post-thumbnails' );

function c_slideshow_get_dynamic_class() {
        
        $class = explode("http://", get_bloginfo("url"));
	$class = explode(".", $class[1]);
        $class = $class[0];
        return $class . "_content";

}

?>
