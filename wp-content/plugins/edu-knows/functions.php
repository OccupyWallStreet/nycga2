<?php
define('TEMPLATE_DOMAIN', 'edu-knows');
define('EDITOR_BG_ENABLE', 'no'); //should be yes or no...
define('USE_NEW_COMMENT_FORM','no');
////////////////////////////////////////////////////////////////////////////////
// load text domain
////////////////////////////////////////////////////////////////////////////////
load_theme_textdomain( TEMPLATE_DOMAIN, TEMPLATEPATH . '/languages/' );

////////////////////////////////////////////////////////////////////////////////
// browser detect
///////////////////////////////////////////////////////////////////
add_filter('body_class','browser_body_class');
function browser_body_class($classes) {
global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;
	if($is_lynx) $classes[] = 'lynx';
	elseif($is_gecko) $classes[] = 'gecko';
	elseif($is_opera) $classes[] = 'opera';
	elseif($is_NS4) $classes[] = 'ns4';
	elseif($is_safari) $classes[] = 'safari';
	elseif($is_chrome) $classes[] = 'chrome';
	elseif($is_IE) $classes[] = 'ie';
	else $classes[] = 'unknown';
	if($is_iphone) $classes[] = 'iphone';
	return $classes;
}

function wp_add_css_ie_tweak() {
global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;
if($is_IE) { ?>
<?php print "<style type='text/css' media='screen'>"; ?>
.picture-activity-thumb {
  width: 100px;
  height: 100px;
  display: block;
}
img.feat-thumb { height/**/:/**/ auto; }
<?php print "</style>"; ?>
<?php }
}
add_action('wp_head','wp_add_css_ie_tweak');
///////////////////////////////////////////////////////////////////////////
// Update Notifications Notice
///////////////////////////////////////////////////////////////////////////
if ( !function_exists( 'wdp_un_check' ) ) {
  add_action( 'admin_notices', 'wdp_un_check', 5 );
  add_action( 'network_admin_notices', 'wdp_un_check', 5 );
  function wdp_un_check() {
    if ( !class_exists( 'WPMUDEV_Update_Notifications' ) && current_user_can( 'edit_users' ) )
      echo '<div class="error fade"><p>' . __('Please install the latest version of <a href="http://premium.wpmudev.org/project/update-notifications/" title="Download Now &raquo;">our free Update Notifications plugin</a> which helps you stay up-to-date with the most stable, secure versions of WPMU DEV themes and plugins. <a href="http://premium.wpmudev.org/wpmu-dev/update-notifications-plugin-information/">More information &raquo;</a>', 'wpmudev') . '</a></p></div>';
  }
}


////////////////////////////////////////////////////////////////////////////////
// Get Featured Post Image
////////////////////////////////////////////////////////////////////////////////
function wp_custom_post_thumbnail($the_post_id='', $with_wrap='', $wrap_w='', $wrap_h='', $title='', $fetch_size='',$fetch_w='', $fetch_h='',$alt_class='') {
// do global first
global $wpdb, $post, $posts;
$detect_post_id = $the_post_id;
if($with_wrap == 'yes') {
$before_wrap = "<div style='width: $wrap_w; height: $wrap_h; overflow: hidden;'>";
$after_wrap = "</div>";
}
?>

<?php if(get_the_post_thumbnail() != "") : ?>

<?php
$image_id = get_post_thumbnail_id();
if($fetch_size == 'original') {
$image_url = wp_get_attachment_image_src($image_id,'large');
} else {
$image_url = wp_get_attachment_image_src($image_id,array($fetch_w,$fetch_h));
}
$image_url = $image_url[0];
?>
<?php echo $before_wrap; ?>
<img width="<?php echo $fetch_w; ?>" height="auto" class="feat-post-thumbnail <?php echo $alt_class; ?>" title="<?php the_title(); ?>" alt="" src="<?php echo $image_url; ?>">
<?php echo $after_wrap; ?>


<?php else: ?>

<?php
$images = get_children(array(
'post_parent' => $the_post_id,
'post_type' => 'attachment',
'numberposts' => 1,
'post_mime_type' => 'image')); ?>
<?php if ($images) : ?>
<?php foreach($images as $image) :
if($fetch_size == 'original') {
$attachment= wp_get_attachment_image_src($image->ID,'large');
} else {
$attachment= wp_get_attachment_image_src($image->ID, array($fetch_w,$fetch_h));
} ?>
<?php echo $before_wrap; ?>
<img width="<?php echo $fetch_w; ?>" height="auto" class="feat-post-attachment <?php echo $alt_class; ?>" title="<?php the_title(); ?>" alt="" src="<?php echo $attachment[0]; ?>">
<?php echo $after_wrap; ?>
<?php endforeach; ?>


<?php elseif( !$images ): ?>

<?php
$get_post_attachment = $wpdb->get_var("SELECT guid FROM " . $wpdb->prefix . "posts WHERE post_parent = '" . $detect_post_id . "' AND post_type = 'attachment' ORDER BY menu_order ASC LIMIT 1");
// If images exist for this page

if($get_post_attachment) {  ?>
<img width="<?php echo $fetch_w; ?>" height="auto" class="feat-post-wp <?php echo $alt_class; ?>" title="<?php the_title(); ?>" alt="" src="<?php echo $get_post_attachment; ?>">

<?php } else { ?>

<?php
$first_img = '';
ob_start();
ob_end_clean();
$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
$first_img = $matches[1][0]; ?>

<?php if($first_img) { ?>
<?php echo $before_wrap; ?>
<img width="<?php echo $fetch_w; ?>" height="auto" class="feat-post-regex <?php echo $alt_class; ?>" title="<?php the_title(); ?>" alt="" src="<?php echo $first_img; ?>">
<?php echo $after_wrap; ?>
<?php } ?>

<?php } ?>

<?php endif; ?>

<?php endif; ?>

<?php }


///////////////////////////////////////////////////////////////////////////
// Custom footer code
///////////////////////////////////////////////////////////////////////////
function wp_network_footer() {
global $blog_id, $current_site, $current_blog;
if( is_multisite() ) {
$current_site = get_current_site();
$current_network_site = get_current_site_name(get_current_site());

if ( function_exists( 'bp_exists' ) ) {
$current_network_domain = bp_get_root_domain();
} else {
if(function_exists('network_home_url')) {
$current_network_domain = network_home_url();
} else {
$current_network_domain = 'http://' . $current_site->domain . $current_site->path;
}
}

if( BLOG_ID_CURRENT_SITE != $current_blog->blog_id && BP_ROOT_BLOG != $current_blog->blog_id ) { ?>
<?php _e('Hosted by', TEMPLATE_DOMAIN); ?> <a target="_blank" title="<?php echo $current_network_site->site_name; ?>" href="<?php echo $current_network_domain; ?>"><?php echo $current_network_site->site_name; ?></a>
<?php } ?>

<?php
}
}

////////////////////////////////////////////////////////////////////////////////
// new code for wp 3.0+
////////////////////////////////////////////////////////////////////////////////
if ( function_exists( 'add_theme_support' ) ) { // Added in 2.9
add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 200, 150, true ); // Normal post thumbnails
	add_image_size( 'single-post-thumbnail', 400, 9999 ); // Permalink thumbnail size

    // Add default posts and comments RSS feed links to head
	add_theme_support( 'automatic-feed-links' );

    if(EDITOR_BG_ENABLE == 'yes') {
    // This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();
    // This theme allows users to set a custom background
	add_custom_background();
    }

    add_theme_support( 'menus' ); // new nav menus for wp 3.0
    if ( ! isset( $content_width ) ) $content_width = 900;
    }

if ( function_exists( 'register_nav_menus' ) ) {
    // This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
    'main-nav' => __( 'Main Navigation',TEMPLATE_DOMAIN )
	) );


///////////////////////////////////////////////////////////////////////////////
// custom walker nav for mobile navigation
///////////////////////////////////////////////////////////////////////////////
class description_custom_walker extends Walker_Nav_Menu {
function start_el(&$output, $item, $depth, $args) {
           global $wp_query;
           $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
           $class_names = $value = '';
           $classes = empty( $item->classes ) ? array() : (array) $item->classes;
           $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
           $class_names = ' class="'. esc_attr( $class_names ) . '"';
           $output .= $indent . '';
           $prepend = '';
           $append = '';
//$description  = ! empty( $item->description ) ? '<span>'.esc_attr( $item->description ).'</span>' : '';

           if($depth != 0) { $description = $append = $prepend = ""; }
           $item_output = $args->before;
           $item_output .= "<option value='" . $item->url . "'>" . $item->title . "</option>";
           $item_output .= $args->after;

           $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
           }
}



function bp_wp_custom_mobile_nav_menu($get_custom_location='', $get_default_menu=''){
$options = array('walker' => new description_custom_walker(), 'theme_location' => "$get_custom_location", 'menu_id' => '', 'echo' => false, 'container' => false, 'container_id' => '', 'fallback_cb' => "$get_default_menu");
$menu = wp_nav_menu($options);
$menu_list = preg_replace( array( '#^<ul[^>]*>#', '#</ul>$#' ), '', $menu );
return $menu_list;
}

///////////////////////////////////////////////////////////////////////////////
// remove open ul to fit the custom bp navigation.php
///////////////////////////////////////////////////////////////////////////////
function bp_wp_custom_nav_menu($get_custom_location='', $get_default_menu=''){
$options = array('theme_location' => "$get_custom_location", 'menu_id' => '', 'echo' => false, 'container' => false, 'container_id' => '', 'fallback_cb' => "$get_default_menu");
$menu = wp_nav_menu($options);
$menu_list = preg_replace( array( '#^<ul[^>]*>#', '#</ul>$#' ), '', $menu );
return $menu_list;
}

function revert_wp_menu_page() { //revert back to normal if in wp 3.0 and menu not set ?>
<ul id="nav">
<li<?php if(is_front_page()) { echo " class='home'"; } ?>><a href="<?php echo home_url(); ?>" title="<?php _e('Go back to home', TEMPLATE_DOMAIN); ?>"><?php _e('Home', TEMPLATE_DOMAIN); ?></a></li>
<?php wp_list_pages('title_li=&depth=0'); ?></ul>
<?php }


function revert_wp_mobile_menu_page() {
global $wpdb;
$qpage = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "posts WHERE post_type='page' AND post_status='publish' ORDER by ID");
foreach ($qpage as $ipage ) {
echo "<option value='" . get_permalink( $ipage->ID ) . "'>" . $ipage->post_title . "</option>";
}
}

function revert_wp_menu_cat() { //revert back to normal if in wp 3.0 and menu not set ?>
<?php wp_list_categories('orderby=id&show_count=0&use_desc_for_title=0&title_li='); ?>
<?php }
}  // end register_nav_menus check


function get_mobile_navigation($type='', $nav_name='') {
   $id = "{$type}-dropdown";
  $js =<<<SCRIPT
<script type="text/javascript">
 jQuery(document).ready(function($){
  $("select#{$id}").change(function(){
    window.location.href = $(this).val();
  });
 });
</script>
SCRIPT;
    echo $js;
  echo "<select name=\"{$id}\" id=\"{$id}\">";
  echo "<option>Where to?</option>"; ?>
<?php echo bp_wp_custom_mobile_nav_menu($get_custom_location=$nav_name, $get_default_menu='revert_wp_mobile_menu_page'); ?>
<?php echo "</select>"; }





////////////////////////////////////////////////////////////////////////////////
// includes
////////////////////////////////////////////////////////////////////////////////
$includes_path = TEMPLATEPATH . '/includes/';
include( TEMPLATEPATH . '/includes/widgets-functions.php' );
include( TEMPLATEPATH . '/includes/social-badge-widget.php' );

////////////////////////////////////////////////////////////////////////////////
// wp 2.7 wp_list_comment filter
////////////////////////////////////////////////////////////////////////////////

add_filter('comments_template', 'legacy_comments');
function legacy_comments($file) {
if(!function_exists('wp_list_comments')) : // WP 2.7-only check
$file = TEMPLATEPATH . '/legacy-comments.php';
endif;
return $file;
}

///////////////comment and ping setup /////////////////////////////////////////

function list_pings($comment, $args, $depth) {
$GLOBALS['comment'] = $comment; ?>
<li id="comment-<?php comment_ID(); ?>"><?php comment_author_link(); ?>
<?php }

add_filter('get_comments_number', 'comment_count', 0);

function comment_count( $count ) {
	global $id;
    $comments_by_type_var = get_comments('post_id=' . $id);
	$comments_by_type = &separate_comments($comments_by_type_var);
	return count($comments_by_type['comment']);
}
////////////////////////////////////////////////////////////////////////////////
// Short title
////////////////////////////////////////////////////////////////////////////////

function the_short_title(){ echo substr_replace(the_title('','',false),'...','40'); }

////////////////////////////////////////////////////////////////////////////////
// Comment and pingback separate controls
////////////////////////////////////////////////////////////////////////////////

$bm_trackbacks = array();
$bm_comments = array();

function split_comments( $source ) {

    if ( $source ) foreach ( $source as $comment ) {

        global $bm_trackbacks;
        global $bm_comments;

        if ( $comment->comment_type == 'trackback' || $comment->comment_type == 'pingback' ) {
            $bm_trackbacks[] = $comment;
        } else {
            $bm_comments[] = $comment;
        }
    }
}

////////////////////////////////////////////////////////////////////////////////
// Most Comments
////////////////////////////////////////////////////////////////////////////////

function get_hottopics($limit = 10) {
    global $wpdb, $post;
    $mostcommenteds = $wpdb->get_results("SELECT  $wpdb->posts.ID, post_title, post_name, post_date, COUNT($wpdb->comments.comment_post_ID) AS 'comment_total' FROM $wpdb->posts LEFT JOIN $wpdb->comments ON $wpdb->posts.ID = $wpdb->comments.comment_post_ID WHERE comment_approved = '1' AND post_date_gmt < '".gmdate("Y-m-d H:i:s")."' AND post_status = 'publish' AND post_password = '' GROUP BY $wpdb->comments.comment_post_ID ORDER  BY comment_total DESC LIMIT $limit");
    foreach ($mostcommenteds as $post) {
			$post_title = htmlspecialchars(stripslashes($post->post_title));
			$comment_total = (int) $post->comment_total;
			echo "<li><a href=\"".get_permalink()."\">$post_title&nbsp;<strong>($comment_total)</strong></a></li>";
    }
}


////////////////////////////////////////////////////////////////////////////////
// function to generate random strings
////////////////////////////////////////////////////////////////////////////////
function RandomString($length=3)
{
$randstr='';
srand((double)microtime()*1000000);
$chars = array ('1','2','3','4','5','6','7','8','9','0');
for ($rand = 0; $rand <= $length; $rand++)
{
$random = rand(0, count($chars) -1);
$randstr .= $chars[$random];
}
return $randstr;
}


////////////////////////////////////////////////////////////////////////////////
// excerpt features
////////////////////////////////////////////////////////////////////////////////

function the_excerpt_feature($excerpt_length=15, $allowedtags='', $filter_type='none', $use_more_link=true, $more_link_text="[+]", $force_more_link=true, $fakeit=1, $fix_tags=true) {
	if (preg_match('%^content($|_rss)|^excerpt($|_rss)%', $filter_type)) {
		$filter_type = 'the_' . $filter_type;
	}
	$text = apply_filters($filter_type, get_the_excerpt_feature($excerpt_length, $allowedtags, $use_more_link, $more_link_text, $force_more_link, $fakeit));
	$text = ($fix_tags) ? balanceTags($text) : $text;
	echo $text;
}

function get_the_excerpt_feature($excerpt_length, $allowedtags, $use_more_link, $more_link_text, $force_more_link, $fakeit) {
	global $id, $post;
	$output = '';
	$output = $post->post_excerpt;
	if (!empty($post->post_password)) { // if there's a password
		if ($_COOKIE['wp-postpass_'.COOKIEHASH] != $post->post_password) {  // and it doesn't match the cookie
			$output = __('There is no excerpt because this is a protected post.', TEMPLATE_DOMAIN);
			return $output;
		}
	}

	// If we haven't got an excerpt, make one.
	if ((($output == '') && ($fakeit == 1)) || ($fakeit == 2)) {
		$output = $post->post_content;
		$output = strip_tags($output, $allowedtags);

        $output = preg_replace( '|\[(.+?)\](.+?\[/\\1\])?|s', '', $output );

		$blah = explode(' ', $output);
		if (count($blah) > $excerpt_length) {
			$k = $excerpt_length;
			$use_dotdotdot = 1;
		} else {
			$k = count($blah);
			$use_dotdotdot = 0;
		}
		$excerpt = '';
		for ($i=0; $i<$k; $i++) {
			$excerpt .= $blah[$i] . ' ';
		}
		// Display "more" link (use css class 'more-link' to set layout).
		if (($use_more_link && $use_dotdotdot) || $force_more_link) {
			$excerpt .= "<a href=\"". get_permalink() . "#more-$id\">$more_link_text</a>";
		} else {
			$excerpt .= ($use_dotdotdot) ? '...' : '';
		}
		 $output = $excerpt;
	} // end if no excerpt
	return $output;
}
////////////////////////////////////////////////////////////////////////////////
// end
////////////////////////////////////////////////////////////////////////////////



function edu_knows_widgets() {
register_sidebar(array(
    'name'=> __('Left Content', TEMPLATE_DOMAIN),
    'id'=> __('left-content', TEMPLATE_DOMAIN),
    'description'=> __('Homepage Left Content Widget', TEMPLATE_DOMAIN),
	'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget'  => '</div>',
    'before_title'  => '<h3 class="widgettitle">',
    'after_title'   => '</h3>'
));
register_sidebar(array(
    'name'=>__('Right Content', TEMPLATE_DOMAIN),
    'id'=>__('right-content', TEMPLATE_DOMAIN),
    'description'=>__('Homepage Right Content Widget', TEMPLATE_DOMAIN),
	'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget'  => '</div>',
    'before_title'  => '<h3 class="widgettitle">',
    'after_title'   => '</h3>'
));
}
add_action('widgets_init','edu_knows_widgets');


///////////////////////////////////////////////////////////////
// admin header function
////////////////////////////////////////////////////////////////
function edufaq_admin_head() { ?>
<link href="<?php echo get_template_directory_uri(); ?>/admin/options-css.css" rel="stylesheet" type="text/css" />
<?php if($_GET["page"] == "options-functions.php") { ?>
<?php wp_enqueue_script( 'jquery' ); ?>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/jscolor.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/jquery-ui-personalized-1.6rc2.min.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/jquery.cookie.min.js"></script>
<script type="text/javascript">
jQuery.noConflict();
var $jd = jQuery;
$jd(document).ready(function(){
$jd('ul#tabm').tabs({event: "click"});
});
</script>
<link href='http://fonts.googleapis.com/css?family=Cantarell' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=Cardo' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=Crimson+Text' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=Droid+Sans' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=Droid+Serif' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=IM+Fell+DW+Pica' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=Josefin+Sans+Std+Light' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=Lobster' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=Molengo' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=Neuton' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=Nobile' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=OFL+Sorts+Mill+Goudy+TT' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=Reenie+Beanie' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=Tangerine' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=Old+Standard+TT' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=Volkorn' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=Yanone+Kaffeesatz' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=Just+Another+Hand' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Terminal+Dosis+Light' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Ubuntu:light,regular,bold' rel='stylesheet' type='text/css'>
<?php } ?>
<?php }
add_action('admin_head', 'edufaq_admin_head');

////////////////////////////////////////////////////////////////////////////////
// add theme custom crop
////////////////////////////////////////////////////////////////////////////////
include( TEMPLATEPATH . '/includes/options-functions.php' );

////////////////////////////////////////////////////////////////////////////////
// get google web font
////////////////////////////////////////////////////////////////////////////////
function font_show(){
$bodytype = get_option('tn_edufaq_body_font');
$headtype = get_option('tn_edufaq_headline_font');

if ($bodytype == ""){ ?>
<?php } else if ($bodytype == "Cantarell, arial, serif" ){ ?>
<link href='http://fonts.googleapis.com/css?family=Cantarell' rel='stylesheet' type='text/css'/>
<?php } else if ($bodytype == "Cardo, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Cardo' rel='stylesheet' type='text/css'/>
<?php } else if ($bodytype == "Crimson Text, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Crimson+Text' rel='stylesheet' type='text/css'/>
<?php } else if ($bodytype == "Droid Sans, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Droid+Sans' rel='stylesheet' type='text/css'/>
<?php } else if ($bodytype == "Droid Serif, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Droid+Serif' rel='stylesheet' type='text/css'/>
<?php } else if ($bodytype == "IM Fell DW Pica, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=IM+Fell+DW+Pica' rel='stylesheet' type='text/css'/>
<?php } else if ($bodytype == "Josefin Sans Std Light, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Josefin+Sans+Std+Light' rel='stylesheet' type='text/css'/>
<?php } else if ($bodytype == "Lobster, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Lobster' rel='stylesheet' type='text/css'/>
<?php } else if ($bodytype == "Molengo, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Molengo' rel='stylesheet' type='text/css'/>
<?php } else if ($bodytype == "Neuton, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Neuton' rel='stylesheet' type='text/css'/>
<?php } else if ($bodytype == "Nobile, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Nobile' rel='stylesheet' type='text/css'/>
<?php } else if ($bodytype == "OFL Sorts Mill Goudy TT, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=OFL+Sorts+Mill+Goudy+TT' rel='stylesheet' type='text/css'/>
<?php } else if ($bodytype == "Reenie Beanie, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Reenie+Beanie' rel='stylesheet' type='text/css'/>
<?php } else if ($bodytype == "Tangerine, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Tangerine' rel='stylesheet' type='text/css'/>
<?php } else if ($bodytype == "Old Standard TT, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Old+Standard+TT' rel='stylesheet' type='text/css'/>
<?php } else if ($bodytype == "Volkorn, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Volkorn' rel='stylesheet' type='text/css'/>
<?php } else if ($bodytype == "Yanone Kaffessatz, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Yanone+Kaffeesatz' rel='stylesheet' type='text/css'/>
<?php } else if ($bodytype == "Just Another Hand, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Just+Another+Hand' rel='stylesheet' type='text/css'>
<?php } else if ($bodytype == "Terminal Dosis Light, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Terminal+Dosis+Light' rel='stylesheet' type='text/css'>
<?php } else if ($bodytype == "Ubuntu, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Ubuntu:light,regular,bold' rel='stylesheet' type='text/css'>
<?php }

if ($headtype == ""){ ?>
<?php } else if ($headtype == "Cantarell, arial, serif" ){ ?>
<link href='http://fonts.googleapis.com/css?family=Cantarell' rel='stylesheet' type='text/css'/>
<?php } else if ($headtype == "Cardo, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Cardo' rel='stylesheet' type='text/css'/>
<?php } else if ($headtype == "Crimson Text, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Crimson+Text' rel='stylesheet' type='text/css'/>
<?php } else if ($headtype == "Droid Sans, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Droid+Sans' rel='stylesheet' type='text/css'/>
<?php } else if ($headtype == "Droid Serif, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Droid+Serif' rel='stylesheet' type='text/css'/>
<?php } else if ($headtype == "IM Fell DW Pica, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=IM+Fell+DW+Pica' rel='stylesheet' type='text/css'/>
<?php } else if ($headtype == "Josefin Sans Std Light, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Josefin+Sans+Std+Light' rel='stylesheet' type='text/css'/>
<?php } else if ($headtype == "Lobster, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Lobster' rel='stylesheet' type='text/css'/>
<?php } else if ($headtype == "Molengo, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Molengo' rel='stylesheet' type='text/css'/>
<?php } else if ($headtype == "Neuton, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Neuton' rel='stylesheet' type='text/css'/>
<?php } else if ($headtype == "Nobile, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Nobile' rel='stylesheet' type='text/css'/>
<?php } else if ($headtype == "OFL Sorts Mill Goudy TT, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=OFL+Sorts+Mill+Goudy+TT' rel='stylesheet' type='text/css'/>
<?php } else if ($headtype == "Reenie Beanie, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Reenie+Beanie' rel='stylesheet' type='text/css'/>
<?php } else if ($headtype == "Tangerine, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Tangerine' rel='stylesheet' type='text/css'/>
<?php } else if ($headtype == "Old Standard TT, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Old+Standard+TT' rel='stylesheet' type='text/css'/>
<?php } else if ($headtype == "Volkorn, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Volkorn' rel='stylesheet' type='text/css'/>
<?php } else if ($headtype == "Yanone Kaffeesatz, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Yanone+Kaffeesatz' rel='stylesheet' type='text/css'>
<?php } else if ($headtype == "Just Another Hand, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Just+Another+Hand' rel='stylesheet' type='text/css'>
<?php } else if ($headtype == "Terminal Dosis Light, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Terminal+Dosis+Light' rel='stylesheet' type='text/css'>
<?php } else if ($headtype == "Ubuntu, arial, serif"){ ?>
<link href='http://fonts.googleapis.com/css?family=Ubuntu:light,regular,bold' rel='stylesheet' type='text/css'>
<?php }

}

include( TEMPLATEPATH . '/includes/twenty-eleven-add-header-image.php' );
?>