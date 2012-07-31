<?php

add_action( 'after_setup_theme', 'regMyMenus' );
function regMyMenus() {
// This theme uses wp_nav_menu() in four locations.
register_nav_menus( array(
'top-nav' => __( 'Main Menu' ),
) );
}

function topnav_fallback() {
?>

<ul class="nav fix">
<li class="<?php if ( is_home() or is_single() or is_paged() or is_search() or (function_exists('is_tag') and is_tag()) ) { ?>current_page_item<?php } else { ?>page_item<?php } ?>"></li>
<?php wp_list_pages('depth=1&sort_column=menu_order&title_li='); ?>
</ul>
<?php
}

function admin_favicon() {
	echo '<link rel="Shortcut Icon" type="image/x-icon" href="'.get_bloginfo('stylesheet_directory').'/favicon.ico" />';
}
add_action('admin_head', 'admin_favicon');

add_theme_support( 'post-thumbnails' );
add_theme_support( 'automatic-feed-links' );
add_theme_support( 'post-formats', array( 'aside', 'audio', 'image', 'video' ) );
add_image_size( 'cover', 173, 243 );
add_image_size( 'slider', 500, 350 );
add_image_size( 'teaser', 40, 40 );
add_image_size( 'browse', 155, 155 );
add_image_size( 'fthumb', 70, 70 ); 

remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'rsd_link'); 
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'start_post_rel_link');
remove_action('wp_head', 'index_rel_link');
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');
remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );

function custom_colors() {
   echo '<style type="text/css">#wphead{background:#ccc !important;border-bottom:5px solid #900;color:#333 !important;text-shadow:#fff 0 1px 1px;}#message{display:none !important;}#footer{background:#ccc !important;border-top:5px solid #900;color:#900;}#user_info p,#user_info p a,#wphead a{color:#900 !important;}</style>';
}
add_action('admin_head', 'custom_colors');

function remove_footer_admin () {
    echo "Thank you for creating with <a href='http://3oneseven.com/'>milo</a>.";
} 
add_filter('admin_footer_text', 'remove_footer_admin'); 

add_action('admin_head', 'my_custom_logo');
function my_custom_logo() {
   echo '
      <style type="text/css">
         #header-logo { background-image: url('.get_bloginfo('template_directory').'/favicon.ico) !important; }
      </style>
   ';
}

function custom_login() { 
echo '<link rel="stylesheet" type="text/css" href="' . get_template_directory_uri() . '/log/log.css" />'; 
}   
add_action('login_head', 'custom_login');
function fb_login_headerurl() {
	$url = bloginfo('url');
	echo $url;
}
add_filter( 'login_headerurl', 'fb_login_headerurl' );
function fb_login_headertitle() {
	$name = get_option('blogname');
	echo $name;
}
add_filter( 'login_headertitle', 'fb_login_headertitle' );

function custom_excerpt_length($length) {
	return 15;
}
add_filter('excerpt_length', 'custom_excerpt_length');

register_sidebars( 1,
	array( 
		'name' => 'Left Sidebar',
		'id' => 'leftbar',
		'description' => __('The left sidebar widget area for ads etc.'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
	) 
);
register_sidebars( 1,
	array( 
		'name' => 'Right Sidebar',
		'id' => 'rbar',
		'description' => __('The right sidebar widget area for ads etc.'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
	) 
);
register_sidebars( 1,
	array( 
		'name' => 'BuddyPress Bar',
		'id' => 'buddybar',
		'description' => __('The buddypress sidebar widget area for ads etc.'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
	) 
);
register_sidebars( 1,
	array( 
		'name' => 'Sidebar',
		'id' => 'sbar',
		'description' => __('The single post sidebar for ads etc.'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
	) 
);
register_sidebars( 1,
	array( 
		'name' => 'Frontbar',
		'id' => 'frontbar',
		'description' => __('The frontpage bar for ads etc.'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
	) 
);
register_sidebars( 1, 
	array( 
		'name' => 'Frontpage Box 1',
		'id' => 'middle-column1',
		'description' => __('The middle widget area, 1st box.'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
	) 
);
register_sidebars( 1,
	array( 
		'name' => 'Frontpage Box 2',
		'id' => 'middle-column2',
		'description' => __('The middle widget area, 2nd box.'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
	) 
);
register_sidebars( 1,
	array( 
		'name' => 'Frontpage Box 3',
		'id' => 'middle-column3',
		'description' => __('The middle widget area, 3rd box.'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
	) 
);
register_sidebars( 1, 
	array( 
		'name' => 'Footer Box 1',
		'id' => 'footer-column1',
		'description' => __('The right sidebar widget area.'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
	) 
);
register_sidebars( 1,
	array( 
		'name' => 'Footer Box 2',
		'id' => 'footer-column2',
		'description' => __('The footer widget area, 1st box.'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
	) 
);
register_sidebars( 1,
	array( 
		'name' => 'Footer Box 3',
		'id' => 'footer-column3',
		'description' => __('The footer widget area, 2nd box.'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
	) 
);
register_sidebars( 1,
	array( 
		'name' => 'Footer Box 4',
		'id' => 'footer-column4',
		'description' => __('The footer widget area, 3rd box.'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
	) 
);

function commentslist($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment; ?>
	<li>
        <div id="comment-<?php comment_ID(); ?>" <?php comment_class(); ?>>
            <table>
                <tr>
                    <td>
                        <?php echo get_avatar($comment, 70, get_bloginfo('template_url').'/images/01.gif'); ?>
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
                                <p><?php _e('Your comment is awaiting moderation.') ?></p>
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

function getTinyUrl($url) {
    $tinyurl = file_get_contents("http://tinyurl.com/api-create.php?url=".$url);
    return $tinyurl;
}

class Recentposts_thumbnail extends WP_Widget {
    function Recentposts_thumbnail() {
        parent::WP_Widget(false, $name = 'Detox Recent Posts');
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
                    <span class="rpthumb-date" <?php echo $offset; unset($offset); ?>><?php the_time(__('M j, Y')) ?></span>
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
            <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
            <p><label for="rpthumb_qty">Number of posts:  </label><input type="text" name="rpthumb_qty" id="rpthumb_qty" size="2" value="<?php echo get_option('rpthumb_qty'); ?>"/></p>
            <p><label for="rpthumb_thumb">Hide thumbnails:  </label><input type="checkbox" name="rpthumb_thumb" id="rpthumb_thumb" <?php echo (get_option('rpthumb_thumb'))? 'checked="checked"' : ''; ?>/></p>
        <?php
    }

}
add_action('widgets_init', create_function('', 'return register_widget("Recentposts_thumbnail");'));
	
if ( !function_exists('fb_addgravatar') ) {
	function fb_addgravatar( $avatar_defaults ) {
		$myavatar = get_bloginfo('stylesheet_directory') . '/images/01.gif';
		$avatar_defaults[$myavatar] = 'people';
$myavatar2 = get_bloginfo('stylesheet_directory') . '/images/01.gif';
$avatar_defaults[$myavatar2] = 'Bruce';
return $avatar_defaults;
}
add_filter( 'avatar_defaults', 'fb_addgravatar' );
}

function Bruce_remove_recent_comments_style() {
	global $wp_widget_factory;
	remove_action( 'wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );
}
add_action( 'widgets_init', 'Bruce_remove_recent_comments_style' );

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

function get_flickrRSS() {
  	for($i = 0 ; $i < func_num_args(); $i++) {
    	$args[] = func_get_arg($i);
    	}
  	if (!isset($args[0])) $num_items = get_option('flickrRSS_display_numitems'); else $num_items = $args[0];
  	if (!isset($args[1])) $type = get_option('flickrRSS_display_type'); else $type = $args[1];
  	if (!isset($args[2])) $tags = trim(get_option('flickrRSS_tags')); else $tags = trim($args[2]);
  	if (!isset($args[3])) $imagesize = get_option('flickrRSS_display_imagesize'); else $imagesize = $args[3];
  	if (!isset($args[4])) $before_image = stripslashes(get_option('flickrRSS_before')); else $before_image = $args[4];
  	if (!isset($args[5])) $after_image = stripslashes(get_option('flickrRSS_after')); else $after_image = $args[5];
  	if (!isset($args[6])) $id_number = stripslashes(get_option('flickrRSS_flickrid')); else $id_number = $args[6];
  	if (!isset($args[7])) $set_id = stripslashes(get_option('flickrRSS_set')); else $set_id = $args[7];
	# use image cache & set location
	$useImageCache = get_option('flickrRSS_use_image_cache');
	$cachePath = get_option('flickrRSS_image_cache_uri');
	$fullPath = get_option('flickrRSS_image_cache_dest'); 
	if (!function_exists('MagpieRSS')) { // Check if another plugin is using RSS, may not work
		include_once (ABSPATH . WPINC . '/rss.php');
		error_reporting(E_ERROR);
	}
	if ($type == "user") { $rss_url = 'http://api.flickr.com/services/feeds/photos_public.gne?id=' . $id_number . '&tags=' . $tags . '&format=rss_200'; }
	elseif ($type == "favorite") { $rss_url = 'http://api.flickr.com/services/feeds/photos_faves.gne?id=' . $id_number . '&format=rss_200'; }
	elseif ($type == "set") { $rss_url = 'http://api.flickr.com/services/feeds/photoset.gne?set=' . $set_id . '&nsid=' . $id_number . '&format=rss_200'; }
	elseif ($type == "group") { $rss_url = 'http://api.flickr.com/services/feeds/groups_pool.gne?id=' . $id_number . '&format=rss_200'; }
	elseif ($type == "community" || $type == "public") { $rss_url = 'http://api.flickr.com/services/feeds/photos_public.gne?tags=' . $tags . '&format=rss_200'; }
	else { print "flickrRSS probably needs to be setup"; }
	# get rss file
	$rss = @ fetch_rss($rss_url);
	if ($rss) {
    	$imgurl = "";
    	# specifies number of pictures
		$items = array_slice($rss->items, 0, $num_items);
	    # builds html from array
    	foreach ( $items as $item ) {
       	 if(preg_match('<img src="([^"]*)" [^/]*/>', $item['description'],$imgUrlMatches)) {
            	$imgurl = $imgUrlMatches[1];
            #change image size         
           	if ($imagesize == "square") {
             	$imgurl = str_replace("m.jpg", "s.jpg", $imgurl);
           	} elseif ($imagesize == "thumbnail") {
             $imgurl = str_replace("m.jpg", "t.jpg", $imgurl);
           	} elseif ($imagesize == "medium") {
             $imgurl = str_replace("_m.jpg", ".jpg", $imgurl);
           	}
           #check if there is an image title (for html validation purposes)
           if($item['title'] !== "") $title = htmlspecialchars(stripslashes($item['title']));
           else $title = "Flickr photo";          
           $url = $item['link'];
	       preg_match('<http://farm[0-9]{0,3}\.static.flickr\.com/\d+?\/([^.]*)\.jpg>', $imgurl, $flickrSlugMatches);
	       $flickrSlug = $flickrSlugMatches[1];
	       # cache images 
	       if ($useImageCache) {   
               # check if file already exists in cache
               # if not, grab a copy of it
               if (!file_exists("$fullPath$flickrSlug.jpg")) {   
                 if ( function_exists('curl_init') ) { // check for CURL, if not use fopen
                    $curl = curl_init();
                    $localimage = fopen("$fullPath$flickrSlug.jpg", "wb");
                    curl_setopt($curl, CURLOPT_URL, $imgurl);
                    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 1);
                    curl_setopt($curl, CURLOPT_FILE, $localimage);
                    curl_exec($curl);
                    curl_close($curl);
                   } else {
                 	$filedata = "";
                    $remoteimage = fopen($imgurl, 'rb');
                  	if ($remoteimage) {
                    	 while(!feof($remoteimage)) {
                         	$filedata.= fread($remoteimage,1024*8);
                       	 }
                  	}
                	fclose($remoteimage);
                	$localimage = fopen("$fullPath$flickrSlug.jpg", 'wb');
                	fwrite($localimage,$filedata);
                	fclose($localimage);
                 } // end CURL check
                } // end file check
                # use cached image
                print $before_image . "<a href=\"$url\" title=\"$title\"><img src=\"$cachePath$flickrSlug.jpg\" alt=\"$title\" /></a>" . $after_image;
            } else {
                # grab image direct from flickr
                print $before_image . "<a href=\"$url\" title=\"$title\"><img src=\"$imgurl\" alt=\"$title\" /></a>" . $after_image;      
            } // end use imageCache
       } // end pregmatch
     } // end foreach
  } // end if($rss)
} # end get_flickrRSS() function
function widget_flickrRSS_init() {
	function widget_flickrRSS($args) {
		extract($args);
		$options = get_option('widget_flickrRSS');
		$title = $options['title'];
		$before_images = $options['before_images'];
		$after_images = $options['after_images'];
		echo $before_widget . $before_title . $title . $after_title . $before_images;
		get_flickrRSS();
		echo $after_images . $after_widget;
	}
	function widget_flickrRSS_control() {
		$options = get_option('widget_flickrRSS');
		if ( $_POST['flickrRSS-submit'] ) {
			$options['title'] = strip_tags(stripslashes($_POST['flickrRSS-title']));
			$options['before_images'] = $_POST['flickrRSS-beforeimages'];
			$options['after_images'] = $_POST['flickrRSS-afterimages'];
			update_option('widget_flickrRSS', $options);
		}
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		$before_images = htmlspecialchars($options['before_images'], ENT_QUOTES);
		$after_images = htmlspecialchars($options['after_images'], ENT_QUOTES);
		echo '<p style="text-align:right;"><label for="flickrRSS-title">Title: <input style="width: 180px;" id="gsearch-title" name="flickrRSS-title" type="text" value="'.$title.'" /></label></p>';
		echo '<p style="text-align:right;"><label for="flickrRSS-beforeimages">Before all images: <input style="width: 180px;" id="flickrRSS-beforeimages" name="flickrRSS-beforeimages" type="text" value="'.$before_images.'" /></label></p>';
		echo '<p style="text-align:right;"><label for="flickrRSS-afterimages">After all images: <input style="width: 180px;" id="flickrRSS-afterimages" name="flickrRSS-afterimages" type="text" value="'.$after_images.'" /></label></p>';
		echo '<input type="hidden" id="flickrRSS-submit" name="flickrRSS-submit" value="1" />';
	}		
	wp_register_sidebar_widget('flickrRSS', 'widget_flickrRSS');
	wp_register_widget_control('flickrRSS', 'widget_flickrRSS_control', 300, 100);
}
function flickrRSS_subpanel() {
     if (isset($_POST['save_flickrRSS_settings'])) {
       $option_flickrid = $_POST['flickr_id'];
       $option_tags = $_POST['tags'];
       $option_set = $_POST['set'];
       $option_display_type = $_POST['display_type'];
       $option_display_numitems = $_POST['display_numitems'];
       $option_display_imagesize = $_POST['display_imagesize'];
       $option_before = $_POST['before_image'];
       $option_after = $_POST['after_image'];
       $option_useimagecache = $_POST['use_image_cache'];
       $option_imagecacheuri = $_POST['image_cache_uri'];
       $option_imagecachedest = $_POST['image_cache_dest'];
       update_option('flickrRSS_flickrid', $option_flickrid);
       update_option('flickrRSS_tags', $option_tags);
       update_option('flickrRSS_set', $option_set);
       update_option('flickrRSS_display_type', $option_display_type);
       update_option('flickrRSS_display_numitems', $option_display_numitems);
       update_option('flickrRSS_display_imagesize', $option_display_imagesize);
       update_option('flickrRSS_before', $option_before);
       update_option('flickrRSS_after', $option_after);
       update_option('flickrRSS_use_image_cache', $option_useimagecache);
       update_option('flickrRSS_image_cache_uri', $option_imagecacheuri);
       update_option('flickrRSS_image_cache_dest', $option_imagecachedest);
       ?> <div class="updated"><p>flickrRSS settings saved</p></div> <?php
     }
	?>
	<div class="wrap">
		<h2>flickrRSS Settings</h2>
		<form method="post">
		<table class="form-table">
		 <tr valign="top">
		  <th scope="row">ID Number</th>
	      <td><input name="flickr_id" type="text" id="flickr_id" value="<?php echo get_option('flickrRSS_flickrid'); ?>" size="20" />
        		Use the <a href="http://idgettr.com">idGettr</a> to find your user or group id.</p></td>
         </tr>
         <tr valign="top">
          <th scope="row">Display</th>
          <td>
        	<select name="display_type" id="display_type">
        	  <option <?php if(get_option('flickrRSS_display_type') == 'user') { echo 'selected'; } ?> value="user">user</option>
        	  <option <?php if(get_option('flickrRSS_display_type') == 'set') { echo 'selected'; } ?> value="set">set</option>
        	  <option <?php if(get_option('flickrRSS_display_type') == 'favorite') { echo 'selected'; } ?> value="favorite">favorite</option>
		      <option <?php if(get_option('flickrRSS_display_type') == 'group') { echo 'selected'; } ?> value="group">group</option>
		      <option <?php if(get_option('flickrRSS_display_type') == 'community') { echo 'selected'; } ?> value="community">community</option>
		    </select>
		 	items using 
        	<select name="display_numitems" id="display_numitems">
		      <option <?php if(get_option('flickrRSS_display_numitems') == '1') { echo 'selected'; } ?> value="1">1</option>
		      <option <?php if(get_option('flickrRSS_display_numitems') == '2') { echo 'selected'; } ?> value="2">2</option>
		      <option <?php if(get_option('flickrRSS_display_numitems') == '3') { echo 'selected'; } ?> value="3">3</option>
		      <option <?php if(get_option('flickrRSS_display_numitems') == '4') { echo 'selected'; } ?> value="4">4</option>
		      <option <?php if(get_option('flickrRSS_display_numitems') == '5') { echo 'selected'; } ?> value="5">5</option>
		      <option <?php if(get_option('flickrRSS_display_numitems') == '6') { echo 'selected'; } ?> value="6">6</option>
		      <option <?php if(get_option('flickrRSS_display_numitems') == '7') { echo 'selected'; } ?> value="7">7</option>
		      <option <?php if(get_option('flickrRSS_display_numitems') == '8') { echo 'selected'; } ?> value="8">8</option>
		      <option <?php if(get_option('flickrRSS_display_numitems') == '9') { echo 'selected'; } ?> value="9">9</option>
		      <option <?php if(get_option('flickrRSS_display_numitems') == '10') { echo 'selected'; } ?> value="10">10</option>
		      <option <?php if(get_option('flickrRSS_display_numitems') == '11') { echo 'selected'; } ?> value="11">11</option>
		      <option <?php if(get_option('flickrRSS_display_numitems') == '12') { echo 'selected'; } ?> value="12">12</option>
		      <option <?php if(get_option('flickrRSS_display_numitems') == '13') { echo 'selected'; } ?> value="13">13</option>
		      <option <?php if(get_option('flickrRSS_display_numitems') == '14') { echo 'selected'; } ?> value="14">14</option>
		      <option <?php if(get_option('flickrRSS_display_numitems') == '15') { echo 'selected'; } ?> value="15">15</option>
		      <option <?php if(get_option('flickrRSS_display_numitems') == '16') { echo 'selected'; } ?> value="16">16</option>
		      <option <?php if(get_option('flickrRSS_display_numitems') == '17') { echo 'selected'; } ?> value="17">17</option>
		      <option <?php if(get_option('flickrRSS_display_numitems') == '18') { echo 'selected'; } ?> value="18">18</option>
		      <option <?php if(get_option('flickrRSS_display_numitems') == '19') { echo 'selected'; } ?> value="19">19</option>
		      <option <?php if(get_option('flickrRSS_display_numitems') == '20') { echo 'selected'; } ?> value="20">20</option>
		      </select>
            <select name="display_imagesize" id="display_imagesize">
		      <option <?php if(get_option('flickrRSS_display_imagesize') == 'square') { echo 'selected'; } ?> value="square">square</option>
		      <option <?php if(get_option('flickrRSS_display_imagesize') == 'thumbnail') { echo 'selected'; } ?> value="thumbnail">thumbnail</option>
		      <option <?php if(get_option('flickrRSS_display_imagesize') == 'small') { echo 'selected'; } ?> value="small">small</option>
		      <option <?php if(get_option('flickrRSS_display_imagesize') == 'medium') { echo 'selected'; } ?> value="medium">medium</option>
		    </select>
            images</p>
           </td> 
         </tr>
         <tr valign="top">
		  <th scope="row">Set</th>
          <td><input name="set" type="text" id="set" value="<?php echo get_option('flickrRSS_set'); ?>" size="40" /> Use number from the set url</p>
         </tr>
         <tr valign="top">
		  <th scope="row">Tags</th>
          <td><input name="tags" type="text" id="tags" value="<?php echo get_option('flickrRSS_tags'); ?>" size="40" /> Comma separated, no spaces</p>
         </tr>
         <tr valign="top">
          <th scope="row">HTML Wrapper</th>
          <td><label for="before_image">Before Image:</label> <input name="before_image" type="text" id="before_image" value="<?php echo htmlspecialchars(stripslashes(get_option('flickrRSS_before'))); ?>" size="10" />
        	  <label for="after_image">After Image:</label> <input name="after_image" type="text" id="after_image" value="<?php echo htmlspecialchars(stripslashes(get_option('flickrRSS_after'))); ?>" size="10" />
          </td>
         </tr>
         </table>      
        <h3>Cache Settings</h3>
		<p>This allows you to store the images on your server and reduce the load on Flickr. Make sure the plugin works without the cache enabled first. If you're still having
		trouble, try visiting the <a href="http://eightface.com/forum/viewforum.php?id=3">forum</a>.</p>
		<table class="form-table">
         <tr valign="top">
          <th scope="row">URL</th>
          <td><input name="image_cache_uri" type="text" id="image_cache_uri" value="<?php echo get_option('flickrRSS_image_cache_uri'); ?>" size="50" />
          <em>http://yoursite.com/cache/</em></td>
         </tr>
         <tr valign="top">
          <th scope="row">Full Path</th>
          <td><input name="image_cache_dest" type="text" id="image_cache_dest" value="<?php echo get_option('flickrRSS_image_cache_dest'); ?>" size="50" /> 
          <em>/home/path/to/wp-content/flickrrss/cache/</em></td>
         </tr>
		 <tr valign="top">
		  <th scope="row" colspan="2" class="th-full">
		  <input name="use_image_cache" type="checkbox" id="use_image_cache" value="true" <?php if(get_option('flickrRSS_use_image_cache') == 'true') { echo 'checked="checked"'; } ?> />  
		  <label for="use_image_cache">Enable the image cache</label></th>
		 </tr>
        </table>
        <div class="submit">
           <input type="submit" name="save_flickrRSS_settings" value="<?php _e('Save Settings', 'save_flickrRSS_settings') ?>" />
        </div>
        </form>
    </div>
<?php } // end flickrRSS_subpanel()
function flickrRSS_admin_menu() {
if (function_exists('add_options_page')) {
        add_options_page('flickrRSS Settings', 'flickrRSS', 8, basename(__FILE__), 'flickrRSS_subpanel');
        }
}
add_action('admin_menu', 'flickrRSS_admin_menu'); 
add_action('plugins_loaded', 'widget_flickrRSS_init');

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

function unregister_default_wp_widgets() { 
	unregister_widget('WP_Widget_Meta');
	unregister_widget('WP_Widget_Search'); 
	unregister_widget('WP_Widget_Recent_Posts'); 
	unregister_widget('WP_Widget_Tag_Cloud');
} 
add_action('widgets_init', 'unregister_default_wp_widgets', 1);
function my_new_contactmethods( $contactmethods ) {
$contactmethods['twitter'] = 'Twitter';
$contactmethods['facebook'] = 'Facebook';
return $contactmethods;
}
add_filter('user_contactmethods','my_new_contactmethods',10,1);
function myavatar_add_default_avatar( $url )
{
return get_stylesheet_directory_uri() .'/images/01.gif';
}
add_filter( 'bp_core_mysteryman_src', 'myavatar_add_default_avatar' ); 
function my_default_get_group_avatar($avatar) {
global $bp, $groups_template;
if( strpos($avatar,'group-avatars') ) {
return $avatar;
}
else {
$custom_avatar = get_stylesheet_directory_uri() .'/images/01.gif';
if($bp->current_action == "")
return '<img width="'.BP_AVATAR_THUMB_WIDTH.'" height="'.BP_AVATAR_THUMB_HEIGHT.'" src="'.$custom_avatar.'" class="avatar" alt="' . attribute_escape( $groups_template->group->name ) . '" />';
else
return '<img width="'.BP_AVATAR_FULL_WIDTH.'" height="'.BP_AVATAR_FULL_HEIGHT.'" src="'.$custom_avatar.'" class="avatar" alt="' . attribute_escape( $groups_template->group->name ) . '" />';
}
}
add_filter( 'bp_get_group_avatar', 'my_default_get_group_avatar');

define('MAGPIE_CACHE_AGE', 120);
define('MAGPIE_INPUT_ENCODING', 'UTF-8');
$twitter_options['widget_fields']['title'] = array('label'=>'Title:', 'type'=>'text', 'default'=>'');
$twitter_options['widget_fields']['username'] = array('label'=>'Username:', 'type'=>'text', 'default'=>'');
$twitter_options['widget_fields']['num'] = array('label'=>'Number of links:', 'type'=>'text', 'default'=>'5');
$twitter_options['widget_fields']['update'] = array('label'=>'Show timestamps:', 'type'=>'checkbox', 'default'=>true);
$twitter_options['widget_fields']['linked'] = array('label'=>'Linked:', 'type'=>'text', 'default'=>'#');
$twitter_options['widget_fields']['hyperlinks'] = array('label'=>'Discover Hyperlinks:', 'type'=>'checkbox', 'default'=>true);
$twitter_options['widget_fields']['twitter_users'] = array('label'=>'Discover @replies:', 'type'=>'checkbox', 'default'=>true);
$twitter_options['widget_fields']['encode_utf8'] = array('label'=>'UTF8 Encode:', 'type'=>'checkbox', 'default'=>false);
$twitter_options['prefix'] = 'twitter';
function twitter_messages($username = '', $num = 1, $list = false, $update = true, $linked  = '#', $hyperlinks = true, $twitter_users = true, $encode_utf8 = false) {
	global $twitter_options;
	include_once(ABSPATH . WPINC . '/rss.php');	
	$messages = fetch_rss('http://twitter.com/statuses/user_timeline/'.$username.'.rss');
	if ($list) echo '<ul class="twitter">';	
	if ($username == '') {
		if ($list) echo '<li>';
		echo 'RSS not configured';
		if ($list) echo '</li>';
	} else {
			if ( empty($messages->items) ) {
				if ($list) echo '<li>';
				echo 'No public Twitter messages.';
				if ($list) echo '</li>';
			} else {
				foreach ( $messages->items as $message ) {
					$msg = " ".substr(strstr($message['description'],': '), 2, strlen($message['description']))." ";
					if($encode_utf8) $msg = utf8_encode($msg);
					$link = $message['link'];			
					if ($list) echo '<li class="twitter-item">'; elseif ($num != 1) echo '<p class="twitter-message">';				
					if ($linked != '' || $linked != false) {
            if($linked == 'all')  { 
              $msg = '<a href="'.$link.'" class="twitter-link">'.$msg.'</a>';  // Puts a link to the status of each tweet 
            } else {
              $msg = $msg . '<a href="'.$link.'" class="twitter-link">'.$linked.'</a>'; // Puts a link to the status of each tweet             
            }
          } 
          if ($hyperlinks) { $msg = hyperlinks($msg); }
          if ($twitter_users)  { $msg = twitter_users($msg); }
          echo $msg;
        if($update) {				
          $time = strtotime($message['pubdate']);
          if ( ( abs( time() - $time) ) < 86400 )
            $h_time = sprintf( __('%s ago'), human_time_diff( $time ) );
          else
            $h_time = date(__('Y/m/d'), $time);
          echo sprintf( __('%s', 'twitter-for-wordpress'),' <span class="twitter-timestamp"><abbr title="' . date(__('Y/m/d H:i:s'), $time) . '">' . $h_time . '</abbr></span>' );
         }          
					if ($list) echo '</li>'; elseif ($num != 1) echo '</p>';
					$i++;
					if ( $i >= $num ) break;
				}
			}
		}
		if ($list) echo '</ul>';
	}
// Link discover stuff
function hyperlinks($text) {
    // match protocol://address/path/file.extension?some=variable&another=asf%
    $text = preg_replace("/\s([a-zA-Z]+:\/\/[a-z][a-z0-9\_\.\-]*[a-z]{2,6}[a-zA-Z0-9\/\*\-\?\&\%]*)([\s|\.|\,])/i"," <a href=\"$1\" class=\"twitter-link\">$1</a>$2", $text);
    // match www.something.domain/path/file.extension?some=variable&another=asf%
    $text = preg_replace("/\s(www\.[a-z][a-z0-9\_\.\-]*[a-z]{2,6}[a-zA-Z0-9\/\*\-\?\&\%]*)([\s|\.|\,])/i"," <a href=\"http://$1\" class=\"twitter-link\">$1</a>$2", $text);      
    // match name@address
    $text = preg_replace("/\s([a-zA-Z][a-zA-Z0-9\_\.\-]*[a-zA-Z]*\@[a-zA-Z][a-zA-Z0-9\_\.\-]*[a-zA-Z]{2,6})([\s|\.|\,])/i"," <a href=\"mailto://$1\" class=\"twitter-link\">$1</a>$2", $text);    
    return $text;
}
function twitter_users($text) {
       $text = preg_replace('/([\.|\,|\:|\?|\?|\>|\{|\(]?)@{1}(\w*)([\.|\,|\:|\!|\?|\>|\}|\)]?)\s/i', "$1<a href=\"http://twitter.com/$2\" class=\"twitter-user\">@$2</a>$3 ", $text);
       return $text;
}     
// Twitter widget stuff
function widget_twitter_init() {
	if ( !function_exists('register_sidebar_widget') )
		return;
	$check_options = get_option('widget_twitter');
  if ($check_options['number']=='') {
    $check_options['number'] = 1;
    update_option('widget_twitter', $check_options);
  }
	function widget_twitter($args, $number = 1) {
		global $twitter_options;
		// $args is an array of strings that help widgets to conform to
		// the active theme: before_widget, before_title, after_widget,
		// and after_title are the array keys. Default tags: li and h2.
		extract($args);
		// Each widget can store its own options. We keep strings here.
		include_once(ABSPATH . WPINC . '/rss.php');
		$options = get_option('widget_twitter');
		// fill options with default values if value is not set
		$item = $options[$number];
		foreach($twitter_options['widget_fields'] as $key => $field) {
			if (! isset($item[$key])) {
				$item[$key] = $field['default'];
			}
		}
		$messages = fetch_rss('http://twitter.com/statuses/user_timeline/'.$username.'.rss');
		// These lines generate our output.
		echo $before_widget . $before_title . $item['title'] . $after_title;
		twitter_messages($item['username'], $item['num'], true, $item['update'], $item['linked'], $item['hyperlinks'], $item['twitter_users'], $item['encode_utf8']);
		echo $after_widget;		
	}
	// This is the function that outputs the form to let the users edit
	// the widget's title. It's an optional feature that users cry for.
	function widget_twitter_control($number) {
		global $twitter_options;
		// Get our options and see if we're handling a form submission.
		$options = get_option('widget_twitter');
		if ( isset($_POST['twitter-submit']) ) {
			foreach($twitter_options['widget_fields'] as $key => $field) {
				$options[$number][$key] = $field['default'];
				$field_name = sprintf('%s_%s_%s', $twitter_options['prefix'], $key, $number);
				if ($field['type'] == 'text') {
					$options[$number][$key] = strip_tags(stripslashes($_POST[$field_name]));
				} elseif ($field['type'] == 'checkbox') {
					$options[$number][$key] = isset($_POST[$field_name]);
				}
			}
			update_option('widget_twitter', $options);
		}
		foreach($twitter_options['widget_fields'] as $key => $field) {
			$field_name = sprintf('%s_%s_%s', $twitter_options['prefix'], $key, $number);
			$field_checked = '';
			if ($field['type'] == 'text') {
				$field_value = htmlspecialchars($options[$number][$key], ENT_QUOTES);
			} elseif ($field['type'] == 'checkbox') {
				$field_value = 1;
				if (! empty($options[$number][$key])) {
					$field_checked = 'checked="checked"';
				}
			}
			printf('<p style="text-align:right;" class="twitter_field"><label for="%s">%s <input id="%s" name="%s" type="%s" value="%s" class="%s" %s /></label></p>',
				$field_name, __($field['label']), $field_name, $field_name, $field['type'], $field_value, $field['type'], $field_checked);
		}
		echo '<input type="hidden" id="twitter-submit" name="twitter-submit" value="1" />';
	}
	function widget_twitter_setup() {
		$options = $newoptions = get_option('widget_twitter');
		if ( isset($_POST['twitter-number-submit']) ) {
			$number = (int) $_POST['twitter-number'];
			$newoptions['number'] = $number;
		}
		if ( $options != $newoptions ) {
			update_option('widget_twitter', $newoptions);
			widget_twitter_register();
		}
	}
	function widget_twitter_page() {
		$options = $newoptions = get_option('widget_twitter');
	?>
		<div class="wrap">
			<form method="POST">
				<h2><?php _e('Twitter Widgets'); ?></h2>
				<p style="line-height: 30px;"><?php _e('How many Twitter widgets would you like?'); ?>
				<select id="twitter-number" name="twitter-number" value="<?php echo $options['number']; ?>">
	<?php for ( $i = 1; $i < 10; ++$i ) echo "<option value='$i' ".($options['number']==$i ? "selected='selected'" : '').">$i</option>"; ?>
				</select>
				<span class="submit"><input type="submit" name="twitter-number-submit" id="twitter-number-submit" value="<?php echo attribute_escape(__('Save')); ?>" /></span></p>
			</form>
		</div>
	<?php
	}
	function widget_twitter_register() {
		$options = get_option('widget_twitter');
		$dims = array('width' => 300, 'height' => 300);
		$class = array('classname' => 'widget_twitter');
		for ($i = 1; $i <= 9; $i++) {
			$name = sprintf(__('Twitter #%d'), $i);
			$id = "twitter-$i"; // Never never never translate an id
			wp_register_sidebar_widget($id, $name, $i <= $options['number'] ? 'widget_twitter' : /* unregister */ '', $class, $i);
			wp_register_widget_control($id, $name, $i <= $options['number'] ? 'widget_twitter_control' : /* unregister */ '', $dims, $i);
		}
		add_action('sidebar_admin_setup', 'widget_twitter_setup');
		add_action('sidebar_admin_page', 'widget_twitter_page');
	}
	widget_twitter_register();
}
add_action('widgets_init', 'widget_twitter_init');

if ( !function_exists( 'bp_is_active' ) )
	return;
	
if ( !function_exists( 'bp_dtheme_setup' ) ) :
function bp_dtheme_setup() {
	global $bp;
	// Load the AJAX functions for the theme
	require( TEMPLATEPATH . '/_inc/ajax.php' );
}
add_action( 'after_setup_theme', 'bp_dtheme_setup' );
endif;

if ( !function_exists( 'bp_dtheme_enqueue_scripts' ) ) :
function bp_dtheme_enqueue_scripts() {	
	// Bump this when changes are made to bust cache
	$version = '20110729';	
	// Enqueue the global JS - Ajax will not work without it
	wp_enqueue_script( 'dtheme-ajax-js', get_template_directory_uri() . '/_inc/global.js', array( 'jquery' ), $version );
	// Add words that we need to use in JS to the end of the page so they can be translated and still used.
	$params = array(
		'my_favs'           => __( 'My Favorites', 'buddypress' ),
		'accepted'          => __( 'Accepted', 'buddypress' ),
		'rejected'          => __( 'Rejected', 'buddypress' ),
		'show_all_comments' => __( 'Show all comments for this thread', 'buddypress' ),
		'show_all'          => __( 'Show all', 'buddypress' ),
		'comments'          => __( 'comments', 'buddypress' ),
		'close'             => __( 'Close', 'buddypress' ),
		'view'              => __( 'View', 'buddypress' )
	);
	wp_localize_script( 'dtheme-ajax-js', 'BP_DTheme', $params );
}
add_action( 'wp_enqueue_scripts', 'bp_dtheme_enqueue_scripts' );
endif;

if ( !function_exists( 'bp_dtheme_widgets_init' ) ) :
function bp_dtheme_widgets_init() {
}
add_action( 'widgets_init', 'bp_dtheme_widgets_init' );
endif;

if ( !function_exists( 'bp_dtheme_activity_secondary_avatars' ) ) :
function bp_dtheme_activity_secondary_avatars( $action, $activity ) {
	switch ( $activity->component ) {
		case 'groups' :
		case 'friends' :
			// Only insert avatar if one exists
			if ( $secondary_avatar = bp_get_activity_secondary_avatar() ) {
				$reverse_content = strrev( $action );
				$position        = strpos( $reverse_content, 'a<' );
				$action          = substr_replace( $action, $secondary_avatar, -$position - 2, 0 );
			}
			break;
	}
	return $action;
}
add_filter( 'bp_get_activity_action_pre_meta', 'bp_dtheme_activity_secondary_avatars', 10, 2 );
endif;

if ( !function_exists( 'bp_dtheme_page_menu_args' ) ) :
function bp_dtheme_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'bp_dtheme_page_menu_args' );
endif;

if ( !function_exists( 'bp_dtheme_sidebar_login_redirect_to' ) ) :
function bp_dtheme_sidebar_login_redirect_to() {
	$redirect_to = apply_filters( 'bp_no_access_redirect', !empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '' );
?>
	<input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>" />
<?php
}
add_action( 'bp_sidebar_login_form', 'bp_dtheme_sidebar_login_redirect_to' );
endif;
?>