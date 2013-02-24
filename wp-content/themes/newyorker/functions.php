<?php
if ( !defined('ABSPATH')) exit;
load_theme_textdomain('Detox', get_template_directory().'/languages');
$locale = get_locale();
$locale_file = get_template_directory().'/languages/$locale.php';
if (is_readable( $locale_file))
require_once( $locale_file);
function Detox_category_rel_removal ($output) {
    $output = str_replace(' rel="category tag"', '', $output);
    return $output;
}
add_filter('wp_list_categories', 'Detox_category_rel_removal');
add_filter('the_category', 'Detox_category_rel_removal');  
	
$args = array(
  'default-color'          => '',
	'default-image'          => '',
	'wp-head-callback'       => '_custom_background_cb',
	'admin-head-callback'    => '',
	'default-color' => 'ffffff',
);
add_theme_support( 'custom-background', $args );

$args = array(
	'width'         => 980,
	'height'        => 60,
  'default-text-color'     => '',
	'header-text'            => true,
	'uploads'                => true,
);
add_theme_support( 'custom-header', $args );
			
if ( ! isset( $content_width ) ) $content_width = 980;
add_filter( 'show_admin_bar', '__return_false' );
add_theme_support( 'automatic-feed-links' );
remove_action('wp_head', 'wp_generator');
add_editor_style('custom-editor-style.css');

if ( is_admin() && isset($_GET['activated'] ) && $pagenow == 'themes.php' ) {
    update_option( 'posts_per_page', 12 );
    update_option( 'paging_mode', 'default' );
}
function gpp_excerpt($text) { return str_replace('[...]', '..', $text); } add_filter('the_excerpt', 'gpp_excerpt');

add_action( 'after_setup_theme', 'regMyMenus' );
function regMyMenus() {
// This theme uses wp_nav_menu() in four locations.
register_nav_menus( array(
'topnav' => __( 'Main Menu', 'Detox' ),
'footernav' => __( 'Footer Menu', 'Detox' ),
) );
}

function topnav_fallback() {
?>


<ul id="top-nav">
<li class="<?php if ( is_home() or is_single() or is_paged() or is_search() or (function_exists('is_tag') and is_tag()) ) { ?>current_page_item<?php } else { ?>page_item<?php } ?>">
<a href="<?php home_url(); ?>/"><?php _e( 'Home', 'Detox' ) ?></a></li>
<?php wp_list_pages('title_li=&depth=4&sort_column=menu_order'); ?>
<li><a href="#"><?php _e( 'Topics', 'Detox') ?></a>
<ul class='children'>
<?php wp_list_categories('orderby=id&show_count=0&sort_column=name&title_li=&depth=3'); ?>
</ul>
</li>
</ul>
<?php
}

function footernav_fallback() {
?>

<ul id="fn">
<li><a title="<?php _e( 'Home is where the heart is', 'Detox') ?>" href="<?php home_url(); ?>/"><?php _e( 'Home', 'Detox') ?></a></li>
<?php wp_list_pages('title_li=&depth=-1&number=8'); ?>
</ul>
<?php
}

function custom_colors() {
   echo '<style type="text/css">#wphead{background:#ccc !important;border-bottom:5px solid #900;color:#900;text-shadow:#111 0 1px 1px;}#message{display:none !important;}#footer{background:#ccc !important;border-top:5px solid #900;color:#900;}#user_info p,#user_info p a,#wphead a,#footer a{color:#900 !important;}</style>';
}
add_action('admin_head', 'custom_colors');

function remove_footer_admin () {
    echo "Thank you for creating with <a href='http://3oneseven.com'>milo</a>.";
} 
add_filter('admin_footer_text', 'remove_footer_admin'); 
	
function wp_pagenavi($before = '', $after = '', $prelabel = '', $nxtlabel = '', $pages_to_show = 5, $always_show = false) {
	global $request, $posts_per_page, $wpdb, $paged;
	if(empty($prelabel)) {
		$prelabel  = '<strong>&laquo;</strong>';
	}
	if(empty($nxtlabel)) {
		$nxtlabel = '<strong>&raquo;</strong>';
	}
	$half_pages_to_show = round($pages_to_show/2);
	if (!is_single()) {
		if(!is_category()) {
			preg_match('#FROM\s(.*)\sORDER BY#siU', $request, $matches);		
		} else {
			preg_match('#FROM\s(.*)\sGROUP BY#siU', $request, $matches);		
		}
		$fromwhere = $matches[1];
		$numposts = $wpdb->get_var("SELECT COUNT(DISTINCT ID) FROM $fromwhere");
		$max_page = ceil($numposts /$posts_per_page);
		if(empty($paged)) {
			$paged = 1;
		}
		if($max_page > 1 || $always_show) {
			echo "$before <div class='Nav'><span>Pages ($max_page): </span>";
			if ($paged >= ($pages_to_show-1)) {
				echo '<a href="'.get_pagenum_link().'">&laquo; First</a> ... ';
			}
			previous_posts_link($prelabel);
			for($i = $paged - $half_pages_to_show; $i  <= $paged + $half_pages_to_show; $i++) {
				if ($i >= 1 && $i <= $max_page) {
					if($i == $paged) {
						echo "<strong class='on'>$i</strong>";
					} else {
						echo ' <a href="'.get_pagenum_link($i).'">'.$i.'</a> ';
					}
				}
			}
			next_posts_link($nxtlabel, $max_page);
			if (($paged+$half_pages_to_show) < ($max_page)) {
				echo ' ... <a href="'.get_pagenum_link($max_page).'">Last &raquo;</a>';
			}
			echo "</div> $after";
		}
	}
}

add_theme_support( 'post-thumbnails' );
add_theme_support( 'automatic-feed-links' );
add_image_size( 'cover', 173, 243 );
add_image_size( 'slider', 500, 450 );
add_image_size( 'teaser', 40, 40 );
add_image_size( 'browse', 155, 155 );
add_image_size( 'big', 1000, 700 );
add_image_size( 'sth', 600, 600 );  

function new_excerpt_length($length) {
	return 22;
}
add_filter('excerpt_length', 'new_excerpt_length');
	
register_sidebars( 1, 
	array( 
		 'name' => __('Frontpage Box 1', 'Detox'),
		'id' => 'left-column',
		'description' => __('The left column frontpage widget area.', 'Detox'),
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
	) 
);
register_sidebars( 1,
	array( 
		 'name' => __('Frontpage Box 2', 'Detox'),
		'id' => 'center-column',
		'description' => __('The center column frontpage widget area.', 'Detox'),
    'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="sl"></div>',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
	) 
);
register_sidebars( 1,
	array( 
		 'name' => __('Frontpage Box 3', 'Detox'),
		'id' => 'right-column',
		'description' => __('The right column frontpage widget area.', 'Detox'),
    'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="sl"></div>',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
	) 
);
register_sidebars( 1, 
	array( 
		 'name' => __('Footer Box 1', 'Detox'),
		'id' => 'footer-column1',
		'description' => __('The 1st column footer widget area.', 'Detox'),
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
	) 
);
register_sidebars( 1,
	array( 
		 'name' => __('Footer Box 2', 'Detox'),
		'id' => 'footer-column2',
		'description' => __('The 2nd column footer widget area.', 'Detox'),
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
	) 
);
register_sidebars( 1,
	array( 
		 'name' => __('Footer Box 3', 'Detox'),
		'id' => 'footer-column3',
		'description' => __('The 3rd column footer widget area.', 'Detox'),
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
	) 
);
register_sidebars( 1,
	array( 
		 'name' => __('Footer Box 4', 'Detox'),
		'id' => 'footer-column4',
		'description' => __('The 4th column footer widget area.', 'Detox'),
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
	) 
);
register_sidebars( 1,
	array( 
		 'name' => __('Widget bar 1', 'Detox'),
		'id' => 'sidebar1',
		'description' => __('The sidebar widget area.', 'Detox'),
    'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="sl"></div>',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
	) 
);
register_sidebars( 1,
	array( 
		 'name' => __('Widget bar 2', 'Detox'),
		'id' => 'sidebar2',
		'description' => __('The sidebar widget area.', 'Detox'),
    'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="sl"></div>',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
	) 
);
register_sidebars( 1,
	array( 
		 'name' => __('Widget bar 3', 'Detox'),
		'id' => 'sidebar3',
		'description' => __('The sidebar widget area.', 'Detox'),
    'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="sl"></div>',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
	) 
);
register_sidebars( 1,
	array( 
		 'name' => __('Widget bar 4', 'Detox'),
		'id' => 'sidebar4',
		'description' => __('The sidebar widget area.', 'Detox'),
    'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="sl"></div>',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
	) 
);
function unregister_default_wp_widgets() { 
	unregister_widget('WP_Widget_Meta');
	unregister_widget('WP_Widget_Search'); 
} 
add_action('widgets_init', 'unregister_default_wp_widgets', 1);

function commentslist($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment; ?>
	<li>
        <div id="comment-<?php comment_ID(); ?>" <?php comment_class(); ?>>
            <table>
                <tr>
                    <td>
                       <?php echo get_avatar($comment, 70, get_template_directory_uri().'/images/no-avatar.png'); ?>
                    </td>
                    <td>
                        <div class="comment-meta">
                            <?php printf(__('<p class="comment-author"><span>%s</span> says:</p>'), get_comment_author_link()) ?>
                            <?php printf(__('<p class="comment-date">%s</p>'), get_comment_date('M j, Y')) ?>
                            <?php comment_reply_link(array_merge($args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
                        </div>
                    </td>
                    <td>
                        <div class="comment-text">
                            <?php if ($comment->comment_approved == '0') : ?>
                                <p><?php _e('Your comment is awaiting moderation.', 'Detox') ?></p>
                                <br />
                            <?php endif; ?>
                            <?php comment_text() ?>
                        </div>
                    </td>
                </tr>
            </table>
         </div>
<?php
}

add_filter('get_comments_number', 'comment_count', 0);
function comment_count( $count ) {
        if ( ! is_admin() ) {
                global $id;
                $comments_by_type = &separate_comments(get_comments('status=approve&post_id=' . $id));
                return count($comments_by_type['comment']);
        } else {
                return $count;
        }
}

class Recentposts_thumbnail extends WP_Widget {
    function Recentposts_thumbnail() {
        parent::WP_Widget(false, $name = 'Recent Posts', 'Detox');
    }
    function widget($args, $instance) {
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        ?>
            <?php echo $before_widget; ?>
            <?php if ( $title ) echo $before_title . $title . $after_title;  else echo '<div class="widget-body clear">'; ?>
            <?php
                global $post;
                if (get_option('rpthumb_qty')) $rpthumb_qty = get_option('rpthumb_qty'); else $rpthumb_qty = 5;
                $q_args = array(
                    'numberposts' => $rpthumb_qty,
                );
                $rpthumb_posts = get_posts($q_args);
                foreach ( $rpthumb_posts as $post ) :
                    setup_postdata($post);
            ?>
                <a href="<?php the_permalink(); ?>" class="rpthumb clear">
                    <?php if ( has_post_thumbnail() && !get_option('rpthumb_thumb') ) {
                        the_post_thumbnail('teaser');
                        $offset = 'style="padding-left: 65px;"';
                    }
                    ?>
                    <span class="rpthumb-title" <?php echo $offset; ?>><?php the_title(); ?></span>
                    <span class="rpthumb-date" <?php echo $offset; unset($offset); ?>><?php the_time(__('M j, Y', 'Detox')) ?></span>
                </a>
            <?php endforeach; ?>
            <?php echo $after_widget; ?>
        <?php
    }
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        update_option('rpthumb_qty', $_POST['rpthumb_qty']);
        update_option('rpthumb_thumb', $_POST['rpthumb_thumb']);
        return $instance;
    }
    function form($instance) {
        $title = esc_attr($instance['title']);
        ?>
            <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'Detox') ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
            <p><label for="rpthumb_qty"><?php _e('Number of posts', 'Detox') ?>:  </label><input type="text" name="rpthumb_qty" id="rpthumb_qty" size="2" value="<?php echo get_option('rpthumb_qty'); ?>"/></p>
            <p><label for="rpthumb_thumb"><?php _e('Hide thumbnails', 'Detox') ?>:  </label><input type="checkbox" name="rpthumb_thumb" id="rpthumb_thumb" <?php echo (get_option('rpthumb_thumb'))? 'checked="checked"' : ''; ?>/></p>
        <?php
    }

}
add_action('widgets_init', create_function('', 'return register_widget("Recentposts_thumbnail");'));

function Bruce_remove_recent_comments_style() {
	global $wp_widget_factory;
	remove_action( 'wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );
}
add_action( 'widgets_init', 'Bruce_remove_recent_comments_style' );

function myavatar_add_default_avatar( $url )
{
return get_stylesheet_directory_uri() .'/images/detox.jpg';
}
add_filter( 'bp_core_mysteryman_src', 'myavatar_add_default_avatar' ); 
function my_default_get_group_avatar($avatar) {
global $bp, $groups_template;
if( strpos($avatar,'group-avatars') ) {
return $avatar;
}
else {
$custom_avatar = get_stylesheet_directory_uri() .'/images/detox.jpg';
if($bp->current_action == "")
return '<img width="'.BP_AVATAR_THUMB_WIDTH.'" height="'.BP_AVATAR_THUMB_HEIGHT.'" src="'.$custom_avatar.'" class="avatar" alt="' . esc_attr( $groups_template->group->name ) . '" />';
else
return '<img width="'.BP_AVATAR_FULL_WIDTH.'" height="'.BP_AVATAR_FULL_HEIGHT.'" src="'.$custom_avatar.'" class="avatar" alt="' . esc_attr( $groups_template->group->name ) . '" />';
}
}
add_filter( 'bp_get_group_avatar', 'my_default_get_group_avatar');

function custom_login() { 
echo '<link rel="stylesheet" type="text/css" href="' . get_template_directory_uri() . '/log/log.css" />'; 
}   
add_action('login_head', 'custom_login');

function vp_get_thumb_url($text, $size){
        global $post;
        $imageurl="";
        $featuredimg = get_post_thumbnail_id($post->ID);
        $img_src = wp_get_attachment_image_src($featuredimg, $size);
        $imageurl=$img_src[0];
        if (!$imageurl) {
                $allimages =&get_children('post_type=attachment&post_mime_type=image&post_parent=' . $post->ID );
                foreach ($allimages as $img){
                        $img_src = wp_get_attachment_image_src($img->ID, $size);
                        break;
                }
                $imageurl=$img_src[0];
        }
        if (!$imageurl) {
                preg_match('/<\s*img [^\>]*src\s*=\s*[\""\']?([^\""\'>]*)/i' ,  $text, $matches);
                $imageurl=$matches[1];
        }
        if (!$imageurl){
                preg_match("/([a-zA-Z0-9\-\_]+\.|)youtube\.com\/watch(\?v\=|\/v\/)([a-zA-Z0-9\-\_]{11})([^<\s]*)/", $text, $matches2);
                $youtubeurl = $matches2[0];
                $videokey = $matches2[3];
        if (!$youtubeurl) {
                preg_match("/([a-zA-Z0-9\-\_]+\.|)youtu\.be\/([a-zA-Z0-9\-\_]{11})([^<\s]*)/", $text, $matches2);
                $youtubeurl = $matches2[0];
                $videokey = $matches2[2];
        }
        if ($youtubeurl)
                $imageurl = "http://i.ytimg.com/vi/{$videokey}/0.jpg";
        }
        if (!$imageurl) {
                $dir = get_template_directory_uri() . '/images/'; // [SET MANUALLY!!!]
                $get_cat = get_the_category();
                $cat = $get_cat[0]->
                slug;
                $imageurl = $dir . $cat . '.jpg'; // [SET MANUALLY!!!]
                $array = array( 'cat_1', 'cat_2', 'cat_3',);
                if (!in_array($cat, $array))
                        $imageurl = $dir . 'vertical.jpg'; // [SET MANUALLY!!!]
        }
        return $imageurl;
} 
?><?php
if ( function_exists( 'bp_is_active' ) ){
	get_template_part('func');
} else {
}
?>