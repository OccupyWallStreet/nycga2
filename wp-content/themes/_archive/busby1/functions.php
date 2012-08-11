<?php

load_theme_textdomain( 'busby', TEMPLATEPATH . '/languages' );

$locale = get_locale();
$locale_file = TEMPLATEPATH . "/languages/$locale.php";
if ( is_readable( $locale_file ) )
	require_once( $locale_file );

/**
 * Loads the options panel
 */
require_once('admin/admin.php'); 


/**
 * This theme uses wp_nav_menu() in one location.
 */
register_nav_menus( array(
	'primary' => __( 'Primary Menu', 'busby' ),
) );

/**
 * Add default posts and comments RSS feed links to head
 */
add_theme_support( 'automatic-feed-links' );

/**
 * Add support for the Aside and Gallery Post Formats
 */
add_theme_support( 'post-formats', array( 'aside', 'gallery' ) );

/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 */
function busby_page_menu_args($args) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'busby_page_menu_args' );

/**
 * Register widgetized area and update sidebar with default widgets
 */
function busby_widgets_init() {
	register_sidebar( array (
		'name' => __( 'Sidebar 1', 'busby' ),
		'id' => 'sidebar-1',
		'before_widget' => '',
		'after_widget' => "</div>",
		'before_title' => '<h3 class="widgettitle">',
		'after_title' => '</h3><div class="widget">',
	) );

	register_sidebar( array (
		'name' => __( 'Footer', 'busby' ),
		'id' => 'footer-1',
		'before_widget' => '<div class="footercol">',
		'after_widget' => "</div></div>",
		'before_title' => '<h3 class="footertitle">',
		'after_title' => '</h3><div class="widget">',
	) );	
}
add_action( 'init', 'busby_widgets_init' );


/**
 * Thanks to Kriesi for Pagination code - http://www.kriesi.at/archives/how-to-build-a-wordpress-post-pagination-without-plugin
 */

function kriesi_pagination($pages = '', $range = 2)
{  
     $showitems = ($range * 2)+1;  

     global $paged;
     if(empty($paged)) $paged = 1;

     if($pages == '')
     {
         global $wp_query;
         $pages = $wp_query->max_num_pages;
         if(!$pages)
         {
             $pages = 1;
         }
     }   

     if(1 != $pages)
     {
         echo "<div class='pagination'>";
         if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<a href='".get_pagenum_link(1)."'>&laquo;</a>";
         if($paged > 1 && $showitems < $pages) echo "<a href='".get_pagenum_link($paged - 1)."'>&lsaquo;</a>";

         for ($i=1; $i <= $pages; $i++)
         {
             if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
             {
                 echo ($paged == $i)? "<span class='current'>".$i."</span>":"<a href='".get_pagenum_link($i)."' class='inactive' >".$i."</a>";
             }
         }

         if ($paged < $pages && $showitems < $pages) echo "<a href='".get_pagenum_link($paged + 1)."'>&rsaquo;</a>";  
         if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "<a href='".get_pagenum_link($pages)."'>&raquo;</a>";
         echo "</div>\n";
     }
}

/**
 * override default logo (if is set in upthemes) *
 */

function custom_logo() {
	global $up_options;
	// if there is text defined (instead of logo), display it
	if(!empty($up_options->logo_text)) {
		echo '<h1 id="logo_text">'.$up_options->logo_text.'</h1>';
		return;
	}
	// display custom logo (if defined) or default logo
	else {
		$logo_img = $up_options->logo;
		if(empty($logo_img)) { 
			$logo_img = get_bloginfo('template_url').'/img/logo.png';
		}
		echo '<img src="'.$logo_img.'" height="69" width="299" class="logo" alt="logo"/>';					
	}
}

/**
 * override theme css (if is set - dark theme/light theme etc)
 *
 */
function custom_theme_css() {
	global $up_options;
	
	if(!empty($up_options->theme)) {
		echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('template_url').'/css/'.$up_options->theme.'.css"/>';
	}
}

/**
 * displays footer-related things - footer text and tracking code.
 *
 */
function up_footer() {
	global $up_options;
	
	if(!empty($up_options->footer_text)) 
		echo '<p id="footer_text">'.$up_options->footer_text.'</p>'; 
	if(!empty($up_options->footer_analytics)) 
		echo $up_options->footer_analytics; 
}


/**
 * Display social media (twitter, feedburner, facebook) icons if they're set to be visible
 *
 */
function social_media_icons() {
	global $up_options;
	
	if($up_options->twitter_icon != 'hidden') {
		echo '<li class="twitter"><a href="http://twitter.com/'.$up_options->twitter_id.'">Twitter</a></li>';
	}
	if($up_options->feedburner_icon != 'hidden') {
		echo '<li class="rss"><a href="'.$up_options->feedburner_url.'">RSS</a></li>';
	}
	if($up_options->facebook_icon != 'hidden') {
		echo '<li class="facebook"><a href="'.$up_options->facebook_url.'">Facebook</a></li>';
	}
}

/**
 * Show slider on the frontpage or display default image, if slider is disabled
 *
 */
function custom_slider() {
	global $up_options;
	
	if($up_options->slider == 'visible') {
		//include (ABSPATH . '/wp-content/themes/Busby/wp-featured-content-slider/content-slider.php');
		echo '<div id="sliderwrap">';
		include (dirname(__FILE__).'/wp-featured-content-slider/content-slider.php');		
		echo '</div>';
	}
	else {
		//echo '<img src="" height="266" width="575" alt="slider"/>';
	}
}


function get_feedburner_count() {
	include_once('AwAPI.class.php');
	global $up_options;
	
	if(!empty($up_options->feedburner_url)) {
		$fbid = end(explode('/', $up_options->feedburner_url));
			
		$aw = new AwAPI($fbid);
		
		try{	    
		    $tm = 3600*24*2;
		    $ts = time() - $tm;
		    $res = $aw -> FeedDataHistory(date("Y-m-d", $ts), date("Y-m-d") );

		    echo '<p class="subscribers">Subscribers <span class="subscribersno">'.$res[date("Y-m-d", $ts)]['circulation'].'</span></p>';
		}
		
		catch(Exception $e){}
		
	} 
	
}

/**
 * Featured slider functions
 */

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

$img_width = get_option('img_width');

if(empty($img_width)) {
	$img_width = 575;
}

$img_height = get_option('img_height');

if(empty($img_height)) {
	$img_height = 266;
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
// Presstrends
function presstrends() {

// Add your PressTrends and Theme API Keys
$api_key = 'fwaauw8aofwq21vgs1mw8b8g87q9x0rrezv4';
$auth = 'wvg752yfps7tj2j9vnch357sgut1ij6jz';

// NO NEED TO EDIT BELOW
$data = get_transient( 'presstrends_data' );
if (!$data || $data == ''){
$api_base = 'http://api.presstrends.io/index.php/api/sites/add/auth/';
$url = $api_base . $auth . '/api/' . $api_key . '/';
$data = array();
$count_posts = wp_count_posts();
$comments_count = wp_count_comments();
$theme_data = get_theme_data(get_stylesheet_directory() . '/style.css');
$plugin_count = count(get_option('active_plugins'));
$data['url'] = stripslashes(str_replace(array('http://', '/', ':' ), '', site_url()));
$data['posts'] = $count_posts->publish;
$data['comments'] = $comments_count->total_comments;
$data['theme_version'] = $theme_data['Version'];
$data['theme_name'] = str_replace( ' ', '', get_bloginfo( 'name' ));
$data['plugins'] = $plugin_count;
$data['wpversion'] = get_bloginfo('version');
foreach ( $data as $k => $v ) {
$url .= $k . '/' . $v . '/';
}
$response = wp_remote_get( $url );
set_transient('presstrends_data', $data, 60*60*24);
}}

add_action('wp_head', 'presstrends'); 