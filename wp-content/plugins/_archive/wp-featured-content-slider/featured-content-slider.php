<?php
/*
Plugin Name: WP Featured Content Slider
Plugin URI: http://www.iwebix.de/featured-content-slider-wordpress-plugin/
Description: This Plugin is used to show your featured Posts/Pages with thumbnails in a nice slider.
Version: 2.6
Author: Dennis Nissle, IWEBIX
Author URI: http://www.iwebix.de/
*/

$c_slider_options_page = get_option('siteurl') . '/wp-admin/admin.php?page=wp-featured-content-slider/options.php';

function c_slider_options_page() {
        
	add_options_page('Featured Content Slider Options', 'Featured Content Slider', 10, 'wp-featured-content-slider/options.php');

}

add_action('admin_menu', 'c_slider_options_page');

function c_slider_add_scripts() {
    
        if ( !is_admin() ) {
    
                wp_register_script('jquery.cycle', get_bloginfo('url') . '/wp-content/plugins/wp-featured-content-slider/scripts/jquery.cycle.all.2.72.js', array('jquery'), '1.3' );
                wp_enqueue_script('jquery.cycle');
        
        }

}

add_action('wp_enqueue_scripts', 'c_slider_add_scripts');


function c_slider_cut_text($text, $chars, $points = "...") {
	
	$content = $text;
	
	$content = preg_replace('/\[.+\]/','', $content);
	$content = apply_filters('the_content', $content); 
	$content = str_replace(']]>', ']]&gt;', $content);
	$content = strip_tags($content);
		
	$length = strlen($content);
	
	if($length <= $chars) {
		
		return $content;
	
	} else {
		
		return mb_substr($content, 0, $chars)." ".$points;
		
	}
}

add_action("admin_init", "c_slider_init");
add_action('save_post', 'c_slider_save');

function c_slider_init() {
        
        add_meta_box("feat_slider", "Featured Content Slider Options", "c_slider_meta", "post", "normal", "high");
        add_meta_box("feat_slider", "Featured Content Slider Options", "c_slider_meta", "page", "normal", "high");

}

function c_slider_meta() {
        
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

function c_slider_save() {

        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
        return $post_id;
        global $post;
        
        if($post->post_type == "post" || $post->post_type == "page") {
                
                update_post_meta($post->ID, "feat_slider", $_POST["feat_slider"]);
        
        }
        
}

function c_slider_insert($atts, $content = null) {
        
        include (ABSPATH . '/wp-content/plugins/wp-featured-content-slider/content-slider.php');

}

add_shortcode("featslider", "c_slider_insert");

$c_slider_img_width = get_option('img_width');

if(empty($c_slider_img_width)) {
	
        $c_slider_img_width = 320;
        
}

$c_slider_img_height = get_option('img_height');

if(empty($c_slider_img_height)) {
	
        $c_slider_img_height = 200;

}

if (function_exists('add_image_size')) {
        
	add_image_size( 'feat_slider', $c_slider_img_width, $c_slider_img_height, true );
        
}

function c_slider_get_thumb($position) {
        
	$thumb = get_the_post_thumbnail($post_id, $position);
	$thumb = explode("\"", $thumb);
	return $thumb[5];
        
}

//Check for Post Thumbnail Support

add_theme_support( 'post-thumbnails' );

function c_slider_get_dynamic_class() {
        
        $class = explode("http://", get_bloginfo("url"));
	$class = explode(".", $class[1]);
        $class = $class[0];
        return $class . "_slider";

}

?>