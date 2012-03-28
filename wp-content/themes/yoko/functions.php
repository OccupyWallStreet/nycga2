<?php
/**
 * @package WordPress
 * @subpackage Yoko
 */

/**
 * Make theme available for translation
 * Translations can be filed in the /languages/ directory
 */
load_theme_textdomain( 'yoko', TEMPLATEPATH . '/languages' );

$locale = get_locale();
$locale_file = TEMPLATEPATH . "/languages/$locale.php";
	if ( is_readable( $locale_file ) )
		require_once( $locale_file );

/**
 * Set the content width based on the theme's design and stylesheet.
 */	
if ( ! isset( $content_width ) )
	$content_width = 620;

/**
 * Tell WordPress to run yoko() when the 'after_setup_theme' hook is run.
 */
add_action( 'after_setup_theme', 'yoko' );

if ( ! function_exists( 'yoko' ) ):

/**
 * Create Yoko Theme Options Page
 */
require_once ( get_template_directory() . '/includes/theme-options.php' );

/**
 * Sets up theme defaults and registers support for WordPress features. 
 */
function yoko() {

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();

	// This theme uses post thumbnails
	add_theme_support( 'post-thumbnails' );

	// Add default posts and comments RSS feed links to head
	add_theme_support( 'automatic-feed-links' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Navigation', 'yoko' ),
	) );
	
	// Add support for Post Formats
	add_theme_support( 'post-formats', array( 'aside', 'gallery', 'link', 'video', 'image', 'quote' ) );

	// This theme allows users to set a custom background
	add_custom_background();

	// Your changeable header business starts here
	define( 'HEADER_TEXTCOLOR', '' );
	// No CSS, just IMG call. The %s is a placeholder for the theme template directory URI.
	define( 'HEADER_IMAGE', '%s/images/headers/ginko.jpg' );

	// The height and width of your custom header. You can hook into the theme's own filters to change these values.
	// Add a filter to yoko_header_image_width and yoko_header_image_height to change these values.
	define( 'HEADER_IMAGE_WIDTH', apply_filters( 'yoko_header_image_width', 1102 ) );
	define( 'HEADER_IMAGE_HEIGHT', apply_filters( 'yoko_header_image_height', 350 ) );

	// We'll be using post thumbnails for custom header images on posts and pages.
	// We want them to be 940 pixels wide by 350 pixels tall.
	// Larger images will be auto-cropped to fit, smaller ones will be ignored. See header.php.
	set_post_thumbnail_size( HEADER_IMAGE_WIDTH, HEADER_IMAGE_HEIGHT, true );

	// Don't support text inside the header image.
	define( 'NO_HEADER_TEXT', true );

	// Add a way for the custom header to be styled in the admin panel that controls
	// custom headers. See yoko_admin_header_style(), below.
	add_custom_image_header( '', 'yoko_admin_header_style' );

	// ... and thus ends the changeable header business.

	// Default custom headers packaged with the theme. %s is a placeholder for the theme template directory URI.
	register_default_headers( array(
			'ginko' => array(
			'url' => '%s/images/headers/ginko.jpg',
			'thumbnail_url' => '%s/images/headers/ginko-thumbnail.jpg',
			/* translators: header image description */
			'description' => __( 'Ginko', 'yoko' )
			),
			'flowers' => array(
			'url' => '%s/images/headers/flowers.jpg',
			'thumbnail_url' => '%s/images/headers/flowers-thumbnail.jpg',
			/* translators: header image description */
			'description' => __( 'Flowers', 'yoko' )
			),
			'plant' => array(
			'url' => '%s/images/headers/plant.jpg',
			'thumbnail_url' => '%s/images/headers/plant-thumbnail.jpg',
			/* translators: header image description */
			'description' => __( 'Plant', 'yoko' )
			),
			'sailing' => array(
			'url' => '%s/images/headers/sailing.jpg',
			'thumbnail_url' => '%s/images/headers/sailing-thumbnail.jpg',
			/* translators: header image description */
			'description' => __( 'Sailing', 'yoko' )
			),
			'cape' => array(
			'url' => '%s/images/headers/cape.jpg',
			'thumbnail_url' => '%s/images/headers/cape-thumbnail.jpg',
			/* translators: header image description */
			'description' => __( 'Cape', 'yoko' )
			),
			'seagull' => array(
			'url' => '%s/images/headers/seagull.jpg',
			'thumbnail_url' => '%s/images/headers/seagull-thumbnail.jpg',
			/* translators: header image description */
			'description' => __( 'Seagull', 'yoko' )
			)
	) );
}
endif;

if ( ! function_exists( 'yoko_admin_header_style' ) ) :

/**
 * Styles the header image displayed on the Appearance > Header admin panel.
 * Referenced via add_custom_image_header() in yoko_setup().
 */
function yoko_admin_header_style() {
?>
<style type="text/css">
/* Shows the same border as on front end */
#heading {
	border-bottom: 1px solid #000;
	border-top: 4px solid #000;
}
/* If NO_HEADER_TEXT is false, you would style the text with these selectors:
	#headimg #name { }
	#headimg #desc { }
*/
</style>
<?php
}
endif;

/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 */
function yoko_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'yoko_page_menu_args' );

/**
 * Sets the post excerpt length to 40 characters.
 */
function yoko_excerpt_length( $length ) {
	return 40;
}
add_filter( 'excerpt_length', 'yoko_excerpt_length' );

/**
 * Returns a "Continue Reading" link for excerpts
 */
function yoko_continue_reading_link() {
	return ' <a href="'. get_permalink() . '">' . __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'yoko' ) . '</a>';
}

/**
 * Replaces "[...]" (appended to automatically generated excerpts) with an ellipsis and yoko_continue_reading_link().
 *
 * To override this in a child theme, remove the filter and add your own
 * function tied to the excerpt_more filter hook.
 */
function yoko_auto_excerpt_more( $more ) {
	return ' &hellip;' . yoko_continue_reading_link();
}
add_filter( 'excerpt_more', 'yoko_auto_excerpt_more' );

/**
 * Adds a pretty "Continue Reading" link to custom post excerpts.
 *
 * To override this link in a child theme, remove the filter and add your own
 * function tied to the get_the_excerpt filter hook.
 */
function yoko_custom_excerpt_more( $output ) {
	if ( has_excerpt() && ! is_attachment() ) {
		$output .= yoko_continue_reading_link();
	}
	return $output;
}
add_filter( 'get_the_excerpt', 'yoko_custom_excerpt_more' );

/**
 * Remove inline styles printed when the gallery shortcode is used.
 */
function yoko_remove_gallery_css( $css ) {
	return preg_replace( "#<style type='text/css'>(.*?)</style>#s", '', $css );
}
add_filter( 'gallery_style', 'yoko_remove_gallery_css' );

if ( ! function_exists( 'yoko_comment' ) ) :

/**
 * Template for comments and pingbacks. 
 */
function yoko_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case '' :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<div id="comment-<?php comment_ID(); ?>">
		<div class="comment-gravatar"><?php echo get_avatar( $comment, 65 ); ?></div>
		
		<div class="comment-body">
		<div class="comment-meta commentmetadata"> 
		<?php printf( __( '%s', 'yoko' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?><br/>
		<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
			<?php
				/* translators: 1: date, 2: time */
				printf( __( '%1$s at %2$s', 'yoko' ), get_comment_date(),  get_comment_time() ); ?></a><?php edit_comment_link( __( 'Edit &rarr;', 'yoko' ), ' ' );
			?>		
		</div><!-- .comment-meta .commentmetadata -->

		<?php comment_text(); ?>
		
		<?php if ( $comment->comment_approved == '0' ) : ?>
		<p class="moderation"><?php _e( 'Your comment is awaiting moderation.', 'yoko' ); ?></p>
		<?php endif; ?>

		<div class="reply">
			<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
		</div><!-- .reply -->
		
		</div>
		<!--comment Body-->
		
	</div><!-- #comment-##  -->

	<?php
			break;
		case 'pingback'  :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'yoko' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __('(Edit)', 'yoko'), ' ' ); ?></p>
	<?php
			break;
	endswitch;
}
endif;

/**
 * Register widgetized area and update sidebar with default widgets
 */
function yoko_widgets_init() {
	register_sidebar( array (
		'name' => __( 'Sidebar 1', 'yoko' ),
		'id' => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array (
		'name' => __( 'Sidebar 2', 'yoko' ),
		'id' => 'sidebar-2',
		'description' => __( 'An second sidebar area', 'yoko' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );	
}
add_action( 'init', 'yoko_widgets_init' );

/**
 * Removes the default styles that are packaged with the Recent Comments widget.
 */
function yoko_remove_recent_comments_style() {
	global $wp_widget_factory;
	remove_action( 'wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );
}
add_action( 'widgets_init', 'yoko_remove_recent_comments_style' );


/**
 * Calls SmoothScroll in Footer
 */
function yoko_smoothscroll_init() {
    if ( !is_admin() ) {
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'smoothscroll', get_template_directory_uri() . '/js/smoothscroll.js', array( 'jquery'), '1.0' ); 
    }
}
// works also for WP < version 3.0
global $wp_version;
if ( version_compare($wp_version, "3.0alpha", "<") ) {
    add_action( 'init', 'yoko_smoothscroll_init' );
} else {
    add_action( 'after_setup_theme', 'yoko_smoothscroll_init' );
}

/**
 * Search form custom styling
 */
function yoko_search_form( $form ) {

    $form = '<form role="search" method="get" class="searchform" action="'.get_bloginfo('url').'" >
    <div><label class="screen-reader-text" for="s">' . __('') . '</label>
    <input type="text" class="search-input" value="' . get_search_query() . '" name="s" id="s" />
    <input type="submit" class="searchsubmit" value="'. esc_attr__('Search', 'yoko') .'" />
    </div>
    </form>';

    return $form;
}
add_filter( 'get_search_form', 'yoko_search_form' );

/**
 * Remove the default CSS style from the WP image gallery
 */
add_filter('gallery_style', create_function('$a', 'return "
<div class=\'gallery\'>";'));


/** 
 * Yoko Shortcodes
 */
 
// Enable shortcodes in widget areas
add_filter( 'widget_text', 'do_shortcode' );


// Columns Shortcodes
// Don't forget to add _last behind the shortcode if it is the last column.

// Two Columns
function yoko_shortcode_two_columns_one( $atts, $content = null ) {
   return '<div class="two-columns-one">' . $content . '</div>';
}
add_shortcode( 'two_columns_one', 'yoko_shortcode_two_columns_one' );

function yoko_shortcode_two_columns_one_last( $atts, $content = null ) {
   return '<div class="two-columns-one last">' . $content . '</div>';
}
add_shortcode( 'two_columns_one_last', 'yoko_shortcode_two_columns_one_last' );

// Three Columns
function yoko_shortcode_three_columns_one($atts, $content = null) {
   return '<div class="three-columns-one">' . $content . '</div>';
}
add_shortcode( 'three_columns_one', 'yoko_shortcode_three_columns_one' );

function yoko_shortcode_three_columns_one_last($atts, $content = null) {
   return '<div class="three-columns-one last">' . $content . '</div>';
}
add_shortcode( 'three_columns_one_last', 'yoko_shortcode_three_columns_one_last' );

function yoko_shortcode_three_columns_two($atts, $content = null) {
   return '<div class="three-columns-two">' . $content . '</div>';
}
add_shortcode( 'three_columns_two', 'yoko_shortcode_three_columns' );

function yoko_shortcode_three_columns_two_last($atts, $content = null) {
   return '<div class="three-columns-two last">' . $content . '</div>';
}
add_shortcode( 'three_columns_two_last', 'yoko_shortcode_three_columns_two_last' );

// Four Columns
function yoko_shortcode_four_columns_one($atts, $content = null) {
   return '<div class="four-columns-one">' . $content . '</div>';
}
add_shortcode( 'four_columns_one', 'yoko_shortcode_four_columns_one' );

function yoko_shortcode_four_columns_one_last($atts, $content = null) {
   return '<div class="four-columns-one last">' . $content . '</div>';
}
add_shortcode( 'four_columns_one_last', 'yoko_shortcode_four_columns_one_last' );

function yoko_shortcode_four_columns_two($atts, $content = null) {
   return '<div class="four-columns-two">' . $content . '</div>';
}
add_shortcode( 'four_columns_two', 'yoko_shortcode_four_columns_two' );

function yoko_shortcode_four_columns_two_last($atts, $content = null) {
   return '<div class="four-columns-two last">' . $content . '</div>';
}
add_shortcode( 'four_columns_two_last', 'yoko_shortcode_four_columns_two_last' );

function yoko_shortcode_four_columns_three($atts, $content = null) {
   return '<div class="four-columns-three">' . $content . '</div>';
}
add_shortcode( 'four_columns_three', 'yoko_shortcode_four_columns_three' );

function yoko_shortcode_four_columns_three_last($atts, $content = null) {
   return '<div class="four-columns-three last">' . $content . '</div>';
}
add_shortcode( 'four_columns_three_last', 'yoko_shortcode_four_columns_three_last' );

// Divide Text Shortcode
function yoko_shortcode_divider($atts, $content = null) {
   return '<div class="divider"></div>';
}
add_shortcode( 'divider', 'yoko_shortcode_divider' );

//Text Highlight and Info Boxes Shortcodes
function yoko_shortcode_highlight($atts, $content = null) {
   return '<span class="highlight">' . $content . '</span>';
}
add_shortcode( 'highlight', 'yoko_shortcode_highlight' );

function yoko_shortcode_yellow_box($atts, $content = null) {
   return '<div class="yellow-box">' . $content . '</div>';
}
add_shortcode( 'yellow_box', 'yoko_shortcode_yellow_box' );

function yoko_shortcode_red_box($atts, $content = null) {
   return '<div class="red-box">' . $content . '</div>';
}
add_shortcode( 'red_box', 'yoko_shortcode_red_box' );

function yoko_shortcode_green_box($atts, $content = null) {
   return '<div class="green-box">' . $content . '</div>';
}
add_shortcode( 'green_box', 'yoko_shortcode_green_box' );

/** 
 * Custom Social Links Widget
 */
class Yoko_SocialLinks_Widget extends WP_Widget {
	function Yoko_SocialLinks_Widget() {
		$widget_ops = array(
		'classname' => 'widget_social_links', 
		'description' => __('Link to your social profiles like twitter, facebook or flickr.', 'yoko'));
		$this->WP_Widget('social_links', 'Yoko Social Links', $widget_ops);
	}

	function widget($args, $instance) {
		extract($args, EXTR_SKIP);
		echo $before_widget;
		$title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
		
		$rss_title = empty($instance['rss_title']) ? ' ' : apply_filters('widget_rss_title', $instance['rss_title']);	
		$rss_url = empty($instance['rss_url']) ? ' ' : apply_filters('widget_rss_url', $instance['rss_url']);
		$twitter_title = empty($instance['twitter_title']) ? ' ' : apply_filters('widget_twitter_title', $instance['twitter_title']);	
		$twitter_url = empty($instance['twitter_url']) ? ' ' : apply_filters('widget_twitter_url', $instance['twitter_url']);		
		$fb_title = empty($instance['fb_title']) ? ' ' : apply_filters('widget_fb_title', $instance['fb_title']);
		$fb_url = empty($instance['fb_url']) ? ' ' : apply_filters('widget_fb_url', $instance['fb_url']);
$googleplus_title = empty($instance['googleplus_title']) ? ' ' : apply_filters('widget_googleplus_title', $instance['googleplus_title']);
		$googleplus_url = empty($instance['googleplus_url']) ? ' ' : apply_filters('widget_googleplus_url', $instance['googleplus_url']);		
		$flickr_title = empty($instance['flickr_title']) ? ' ' : apply_filters('widget_flickr_title', $instance['flickr_title']);
		$flickr_url = empty($instance['flickr_url']) ? ' ' : apply_filters('widget_flickr_url', $instance['flickr_url']);
		$vimeo_title = empty($instance['vimeo_title']) ? ' ' : apply_filters('widget_vimeo_title', $instance['vimeo_title']);
		$vimeo_url = empty($instance['vimeo_url']) ? ' ' : apply_filters('widget_vimeo_url', $instance['vimeo_url']);
		$linkedin_title = empty($instance['linkedin_title']) ? ' ' : apply_filters('widget_linkedin_title', $instance['linkedin_title']);
		$linkedin_url = empty($instance['linkedin_url']) ? ' ' : apply_filters('widget_linkedin_url', $instance['linkedin_url']);
		$delicious_title = empty($instance['delicious_title']) ? ' ' : apply_filters('widget_delicious_title', $instance['delicious_title']);
		$delicious_url = empty($instance['delicious_url']) ? ' ' : apply_filters('widget_delicious_url', $instance['delicious_url']);
		
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; };
		echo '<ul>';
	if($rss_title == ' ') { echo ''; } else {  echo  '<li class="widget_sociallinks"><a href="'. $rss_url .'" class="rss" target="_blank">'. $rss_title .'</a></li>'; }
		if($twitter_title == ' ') { echo ''; } else {  echo  '<li class="widget_sociallinks"><a href="'. $twitter_url .'" class="twitter" target="_blank">'. $twitter_title .'</a></li>'; }
		if($fb_title == ' ') { echo ''; } else {  echo  '<li class="widget_sociallinks"><a href="'. $fb_url .'" class="facebook" target="_blank">'. $fb_title .'</a></li>'; }
		if($googleplus_title == ' ') { echo ''; } else {  echo  '<li class="widget_sociallinks"><a href="'. $googleplus_url .'" class="googleplus" target="_blank">'. $googleplus_title .'</a></li>'; }
		if($flickr_title == ' ') { echo ''; } else {  echo  '<li class="widget_sociallinks"><a href="'. $flickr_url .'" class="flickr" target="_blank">'. $flickr_title .'</a></li>'; }
		if($vimeo_title == ' ') { echo ''; } else {  echo  '  <li class="widget_sociallinks"><a href="'. $vimeo_url .'" class="vimeo" target="_blank">'. $vimeo_title .'</a></li>'; }
		if($linkedin_title == ' ') { echo ''; } else {  echo  '<li class="widget_sociallinks"><a href="'. $linkedin_url .'" class="linkedin" target="_blank">'. $linkedin_title .'</a></li>'; }
		if($delicious_title == ' ') { echo ''; } else {  echo  '<li class="widget_sociallinks"><a href="'. $delicious_url .'" class="delicious" target="_blank">'. $delicious_title .'</a></li>'; }
		echo '</ul>';
		echo $after_widget;
		
	}
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		
		$instance['rss_title'] = strip_tags($new_instance['rss_title']);
		$instance['rss_url'] = strip_tags($new_instance['rss_url']);
		$instance['twitter_title'] = strip_tags($new_instance['twitter_title']);
		$instance['twitter_url'] = strip_tags($new_instance['twitter_url']);
		$instance['fb_title'] = strip_tags($new_instance['fb_title']);
		$instance['fb_url'] = strip_tags($new_instance['fb_url']);
		$instance['googleplus_title'] = strip_tags($new_instance['googleplus_title']);
		$instance['googleplus_url'] = strip_tags($new_instance['googleplus_url']);
		$instance['flickr_title'] = strip_tags($new_instance['flickr_title']);
		$instance['flickr_url'] = strip_tags($new_instance['flickr_url']);		
		$instance['vimeo_title'] = strip_tags($new_instance['vimeo_title']);
		$instance['vimeo_url'] = strip_tags($new_instance['vimeo_url']);
		$instance['linkedin_title'] = strip_tags($new_instance['linkedin_title']);
		$instance['linkedin_url'] = strip_tags($new_instance['linkedin_url']);
		$instance['delicious_title'] = strip_tags($new_instance['delicious_title']);
		$instance['delicious_url'] = strip_tags($new_instance['delicious_url']);
		return $instance;
	}
	function form($instance) {
		$instance = wp_parse_args(
		(array) $instance, array( 
			'title' => '',
			'rss_title' => '',
			'rss_url' => '',
			'twitter_title' => '',
			'twitter_url' => '',
			'fb_title' => '',
			'fb_url' => '',
			'googleplus_title' => '',
			'googleplus_url' => '',
			'flickr_title' => '',
			'flickr_url' => '',
			'vimeo_title' => '',
			'vimeo_url' => '',
			'linkedin_title' => '',
			'linkedin_url' => '',
			'delicious_title' => '',
			'delicious_url' => ''
		) );
		$title = strip_tags($instance['title']);
		$rss_title = strip_tags($instance['rss_title']);
		$rss_url = strip_tags($instance['rss_url']);
		$twitter_title = strip_tags($instance['twitter_title']);
		$twitter_url = strip_tags($instance['twitter_url']);
		$fb_title = strip_tags($instance['fb_title']);
		$fb_url = strip_tags($instance['fb_url']);
		$googleplus_title = strip_tags($instance['googleplus_title']);
		$googleplus_url = strip_tags($instance['googleplus_url']);
		$flickr_title = strip_tags($instance['flickr_title']);
		$flickr_url = strip_tags($instance['flickr_url']);
		$vimeo_title = strip_tags($instance['vimeo_title']);
		$vimeo_url = strip_tags($instance['vimeo_url']);
		$linkedin_title = strip_tags($instance['linkedin_title']);
		$linkedin_url = strip_tags($instance['linkedin_url']);
		$delicious_title = strip_tags($instance['delicious_title']);
		$delicious_url = strip_tags($instance['delicious_url']);
?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', 'yoko' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label></p>
			
			<p><label for="<?php echo $this->get_field_id('rss_title'); ?>"><?php _e( 'RSS Text:', 'yoko' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('rss_title'); ?>" name="<?php echo $this->get_field_name('rss_title'); ?>" type="text" value="<?php echo esc_attr($rss_title); ?>" /></label></p>	
			
			<p><label for="<?php echo $this->get_field_id('rss_url'); ?>"><?php _e( 'RSS  URL:', 'yoko' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('rss_url'); ?>" name="<?php echo $this->get_field_name('rss_url'); ?>" type="text" value="<?php echo esc_attr($rss_url); ?>" /></label></p>	
			
			<p><label for="<?php echo $this->get_field_id('twitter_title'); ?>"><?php _e( 'Twitter Text:', 'yoko' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('twitter_title'); ?>" name="<?php echo $this->get_field_name('twitter_title'); ?>" type="text" value="<?php echo esc_attr($twitter_title); ?>" /></label></p>	
			<p><label for="<?php echo $this->get_field_id('twitter_url'); ?>"><?php _e( 'Twitter  URL:', 'yoko' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('twitter_url'); ?>" name="<?php echo $this->get_field_name('twitter_url'); ?>" type="text" value="<?php echo esc_attr($twitter_url); ?>" /></label></p>
			<p><label for="<?php echo $this->get_field_id('fb_title'); ?>"><?php _e( 'Facebook Text:', 'yoko' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('fb_title'); ?>" name="<?php echo $this->get_field_name('fb_title'); ?>" type="text" value="<?php echo esc_attr($fb_title); ?>" /></label></p>
			<p><label for="<?php echo $this->get_field_id('fb_url'); ?>"><?php _e( 'Facebook URL:', 'yoko' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('fb_url'); ?>" name="<?php echo $this->get_field_name('fb_url'); ?>" type="text" value="<?php echo esc_attr($fb_url); ?>" /></label></p>
			<p><label for="<?php echo $this->get_field_id('googleplus_title'); ?>"><?php _e( 'Google+ Text:', 'yoko' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('googleplus_title'); ?>" name="<?php echo $this->get_field_name('googleplus_title'); ?>" type="text" value="<?php echo esc_attr($googleplus_title); ?>" /></label></p>
			<p><label for="<?php echo $this->get_field_id('googleplus_url'); ?>"><?php _e( 'Google+ URL:', 'yoko' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('googleplus_url'); ?>" name="<?php echo $this->get_field_name('googleplus_url'); ?>" type="text" value="<?php echo esc_attr($googleplus_url); ?>" /></label></p>
			<p><label for="<?php echo $this->get_field_id('flickr_title'); ?>"><?php _e( 'Flickr Text:', 'yoko' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('flickr_title'); ?>" name="<?php echo $this->get_field_name('flickr_title'); ?>" type="text" value="<?php echo esc_attr($flickr_title); ?>" /></label></p>
			<p><label for="<?php echo $this->get_field_id('flickr_url'); ?>"><?php _e( 'Flickr URL:', 'yoko' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('flickr_url'); ?>" name="<?php echo $this->get_field_name('flickr_url'); ?>" type="text" value="<?php echo esc_attr($flickr_url); ?>" /></label></p>
			<p><label for="<?php echo $this->get_field_id('vimeo_title'); ?>"><?php _e( 'Vimeo Text:', 'yoko' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('vimeo_title'); ?>" name="<?php echo $this->get_field_name('vimeo_title'); ?>" type="text" value="<?php echo esc_attr($vimeo_title); ?>" /></label></p>	
			<p><label for="<?php echo $this->get_field_id('vimeo_url'); ?>"><?php _e( 'Vimeo URL:', 'yoko' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('vimeo_url'); ?>" name="<?php echo $this->get_field_name('vimeo_url'); ?>" type="text" value="<?php echo esc_attr($vimeo_url); ?>" /></label></p>		
			<p><label for="<?php echo $this->get_field_id('linkedin_title'); ?>"><?php _e( 'LinkedIn Text:', 'yoko' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('linkedin_title'); ?>" name="<?php echo $this->get_field_name('linkedin_title'); ?>" type="text" value="<?php echo esc_attr($linkedin_title); ?>" /></label></p>		
			<p><label for="<?php echo $this->get_field_id('linkedin_url'); ?>"><?php _e( 'LinkedIn URL:', 'yoko' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('linkedin_url'); ?>" name="<?php echo $this->get_field_name('linkedin_url'); ?>" type="text" value="<?php echo esc_attr($linkedin_url); ?>" /></label></p>	
			<p><label for="<?php echo $this->get_field_id('delicious_title'); ?>"><?php _e( 'Delicious Text:', 'yoko' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('delicious_title'); ?>" name="<?php echo $this->get_field_name('delicious_title'); ?>" type="text" value="<?php echo esc_attr($delicious_title); ?>" /></label></p>
			<p><label for="<?php echo $this->get_field_id('delicious_url'); ?>"><?php _e( 'Delicious URL:', 'yoko' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('delicious_url'); ?>" name="<?php echo $this->get_field_name('delicious_url'); ?>" type="text" value="<?php echo esc_attr($delicious_url); ?>" /></label></p>

<?php
	}
}
// register Yoko SocialLinks Widget
add_action('widgets_init', create_function('', 'return register_widget("Yoko_SocialLinks_Widget");'));


