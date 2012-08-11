<?php

function wt_get_ID_by_page_name($page_name)
{
	global $wpdb;
	$page_name_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = '".$page_name."'");
	return $page_name_id;
}
////////////////////////////////////////////////////////////////////////////////
// new thumbnail code for wp 2.9+
////////////////////////////////////////////////////////////////////////////////
if ( function_exists( 'add_theme_support' ) ) { // Added in 2.9
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 150, 150, true ); // Normal post thumbnails
	add_image_size( 'single-post-thumbnail', 400, 9999 ); // Permalink thumbnail size
}
// This theme uses wp_nav_menu() in one location.
register_nav_menus( array(
	'primary' => __( 'Primary Navigation', 'business-services' ),
) );

function wpmudev_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'wpmudev_page_menu_args' );
////////////////////////////////////////////////////////////////////////////////
// WP-PageNavi
////////////////////////////////////////////////////////////////////////////////


function custom_wp_pagenavi($before = '', $after = '', $prelabel = '', $nxtlabel = '', $pages_to_show = 5, $always_show = false) {
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
			echo "$before <div class=\"wp-pagenavi\"><span class=\"pages\">Page $paged of $max_page:</span>";
			if ($paged >= ($pages_to_show-1)) {
				echo '<a href="'.get_pagenum_link().'">&laquo; First</a>';
			}
			previous_posts_link($prelabel);
			for($i = $paged - $half_pages_to_show; $i  <= $paged + $half_pages_to_show; $i++) {
				if ($i >= 1 && $i <= $max_page) {
					if($i == $paged) {
						echo "<strong class='current'>$i</strong>";
					} else {
						echo ' <a href="'.get_pagenum_link($i).'">'.$i.'</a> ';
					}
				}
			}
			next_posts_link($nxtlabel, $max_page);
			if (($paged+$half_pages_to_show) < ($max_page)) {
				echo '<a href="'.get_pagenum_link($max_page).'">Last &raquo;</a>';
			}
			echo "</div> $after";
		}
	}
}



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
// excerpt features
////////////////////////////////////////////////////////////////////////////////
function the_excerpt_featured($excerpt_length = '', $allowedtags = '', $filter_type = 'none', $use_more_link = true, $more_link_text = '', $force_more_link = true, $fakeit = 1, $fix_tags = true) {
if (preg_match('%^content($|_rss)|^excerpt($|_rss)%', $filter_type)) {
$filter_type = 'the_' . $filter_type;
}
$text = apply_filters($filter_type, get_the_excerpt_featured($excerpt_length, $allowedtags, $use_more_link, $more_link_text, $force_more_link, $fakeit));
$text = ($fix_tags) ? balanceTags($text) : $text;
echo $text;
}

function get_the_excerpt_feature($excerpt_length, $allowedtags, $use_more_link, $more_link_text, $force_more_link, $fakeit) {
global $id, $post;
$output = '';
$output = $post->post_excerpt;
if (!empty($post->post_password)) { // if there's a password
if ($_COOKIE['wp-postpass_'.COOKIEHASH] != $post->post_password) {  // and it doesn't match the cookie
$output = __('There is no excerpt because this is a protected post.', 'business-services');
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


/* post image */
function the_post_image_url($size) {
	
	global $post;
	$linkedimgurl = get_post_meta ($post->ID, 'image_url', true);

	if ( $images = get_children(array(
		'post_parent' => get_the_ID(),
		'post_type' => 'attachment',
		'numberposts' => 1,
		'post_mime_type' => 'image',))){
	
		foreach( $images as $image ) {
			$attachmenturl=wp_get_attachment_image_src($image->ID, $size);
			$attachmenturl=$attachmenturl[0];
			$attachmentimage=wp_get_attachment_image( $image->ID, $size );
			echo ''.$attachmenturl.'';
		}
		
	} 
	
	elseif ( $linkedimgurl ) {
		echo $linkedimgurl;

	} 
	
	elseif ( $linkedimgurl && $images = get_children(array(
		'post_parent' => get_the_ID(),
		'post_type' => 'attachment',
		'numberposts' => 1,
		'post_mime_type' => 'image',))){
		
		foreach( $images as $image ) {
			$attachmenturl=wp_get_attachment_image_src($image->ID, $size);
			$attachmenturl=$attachmenturl[0];
			$attachmentimage=wp_get_attachment_image( $image->ID, $size );
			echo ''.$attachmenturl.'';
		}
		
	} 
	else {
			$defaultimage = get_bloginfo ( 'stylesheet_directory' );
			$defaultimage .= "/_inc/images/no-attachment.gif";
			echo $defaultimage;
	}
}

// Post Attachment image function. Direct link to file. 
function the_post_image($size) {
	
	global $post;
	$linkedimgtag = get_post_meta ($post->ID, 'image_tag', true);

	if ( $images = get_children(array(
		'post_parent' => get_the_ID(),
		'post_type' => 'attachment',
		'numberposts' => 1,
		'post_mime_type' => 'image',))){

		foreach( $images as $image ) {
			$attachmenturl=wp_get_attachment_url($image->ID);
			$attachmentimage=wp_get_attachment_image( $image->ID, $size );
			echo ''.$attachmentimage.'';
		}
		
	} 

	elseif ( $linkedimgtag ) {
			echo $linkedimgtag;

	} 
	
	elseif ( $linkedimgtag && $images = get_children(array(
		'post_parent' => get_the_ID(),
		'post_type' => 'attachment',
		'numberposts' => 1,
		'post_mime_type' => 'image',))){
		
		foreach( $images as $image ) {
			$attachmenturl=wp_get_attachment_url($image->ID);
			$attachmentimage=wp_get_attachment_image( $image->ID, $size );
			echo ''.$attachmentimage.'';
		}
		
	} 
	
	else {
		$defaultimage = get_bloginfo ( 'stylesheet_directory' );
		$defaultimage .= "/_inc/images/no-attachment.gif";
		echo $defaultimage;
	}
}

//Setup Images for Attachment functions 
function image_setup($postid) {
	global $post;
	$post = get_post($postid);

	// get url
	if ( !preg_match('/<img ([^>]*)src=(\"|\')(.+?)(\2)([^>\/]*)\/*>/', $post->post_content, $matches) ) {
		return false;
	}

	// url setup
	$post->image_url = $matches[3];
	if ( !$post->image_url = preg_replace('/\?w\=[0-9]+/','', $post->image_url) )
		return false;

	$post->image_url = esc_url( $post->image_url, 'raw' );
	
	delete_post_meta($post->ID, 'image_url');
	delete_post_meta($post->ID, 'image_tag');
	add_post_meta($post->ID, 'image_url', $post->image_url);
	add_post_meta($post->ID, 'image_tag', '<img src="'.$post->image_url.'" />');

}

add_action('publish_post', 'image_setup');
add_action('publish_page', 'image_setup');

// Post Attachment image function for Attachment Pages. Not used in theme but could be called if want to have customisation
function the_attachment_image($size) {
	$attachmenturl=wp_get_attachment_url($image->ID);
	$attachmentimage=wp_get_attachment_image( $image->ID, $size );

	echo ''.$attachmentimage.'';
}

// Post Attachment image function for Attachment Pages. Not used in theme but could be called if want to have customisation
function link_to_attachment($size) {
	if ( $attachs = get_children(array(
		'post_parent' => get_the_ID(),
		'post_type' => 'attachment',
		'numberposts' => 1,
		'post_mime_type' => 'image',))){

		foreach( $attachs as $attach ) {
			$attachmentlink=get_attachment_link($attach->ID);
			echo '<a href="' . $attachmentlink . '">View EXIF Data</a>';
		}
	}
}

function wpbreadcrumbs(){
	if (is_page() && !is_front_page() || is_single() || is_category()) {
	        echo '<ul class="breadcrumbs">';
	        echo '<li class="front_page"><a href="'.get_bloginfo('url').'">'.get_bloginfo('name').'</a></li>';

	        if (is_page()) {
	            $ancestors = get_post_ancestors($post);

	            if ($ancestors) {
	                $ancestors = array_reverse($ancestors);

	                foreach ($ancestors as $crumb) {
	                    echo '<li><a href="'.get_permalink($crumb).'">'.get_the_title($crumb).'</a></li>';
	                }
	            }
	        }

	        if (is_single()) {
	            $category = get_the_category();
	            echo '<li><a href="'.get_category_link($category[0]->cat_ID).'">'.$category[0]->cat_name.'</a></li>';
	        }

	        if (is_category()) {
	            $category = get_the_category();
	            echo '<li>'.$category[0]->cat_name.'</li>';
	        }

	        // Current page
	        if (is_page() || is_single()) {
	            echo '<li class="current">'.get_the_title().'</li>';
	        }
	        echo '</ul>';
	    } elseif (is_front_page()) {
	        // Front page
	        echo '<ul class="breadcrumbs">';
	        echo '<li class="front_page"><a href="'.get_bloginfo('url').'">'.get_bloginfo('name').'</a></li>';
	        echo '<li class="current">'.__( 'Home', 'business-services' ).'</li>';
	        echo '</ul>';
	    }
}


function bpbreadcrumbs(){
	if (is_page() && !is_front_page() || is_single() || is_category()) {
	        echo '<ul class="breadcrumbs">';
	        echo '<li class="front_page"><a href="'.get_bloginfo('url').'">'.get_bloginfo('name').'</a></li>';

	        if (is_page()) {
	            $ancestors = get_post_ancestors($post);

	            if ($ancestors) {
	                $ancestors = array_reverse($ancestors);

	                foreach ($ancestors as $crumb) {
	                    echo '<li><a href="'.get_permalink($crumb).'">'.get_the_title($crumb).'</a></li>';
	                }
	            }
	        }

	        if (is_single()) {
	            $category = get_the_category();
	            echo '<li><a href="'.get_category_link($category[0]->cat_ID).'">'.$category[0]->cat_name.'</a></li>';
	        }

	        if (is_category()) {
	            $category = get_the_category();
	            echo '<li>'.$category[0]->cat_name.'</li>';
	        }

	        // Current page
	        if (is_page() || is_single()) {
		$title = get_the_title();
		if ($title != ""){
				   echo '<li class="current">'.get_the_title().'</li>';
	        }
		}
	    } 
		if (bp_is_page( BP_ACTIVITY_SLUG )){
			 echo '<li>'.__( 'Activity', 'business-services' ).'</li>';
		}
		if (bp_is_page( BP_MEMBERS_SLUG ) || bp_is_member()){
			 echo '<li class="current">'.__( 'Members', 'business-services' ).'</li>';
		}
			if (bp_is_page( BP_GROUPS_SLUG ) || bp_is_group()){
				 echo '<li class="current">'.__( 'Groups', 'business-services' ).'</li>';
			}
				if (bp_is_page( BP_FORUMS_SLUG )){
					 echo '<li class="current">'.__( 'Forums', 'business-services' ).'</li>';
				}
						if (bp_is_page( BP_BLOGS_SLUG )){
							 echo '<li class="current">'.__( 'Blogs', 'business-services' ).'</li>';
						}
						echo '</ul>';
				        
		if (is_front_page()) {
	        // Front page
	        echo '<ul class="breadcrumbs">';
	        echo '<li class="front_page"><a href="'.get_bloginfo('url').'">'.get_bloginfo('name').'</a></li>';
	        echo '<li class="current">'.__( 'Home', 'business-services' ).'</li>';
	        echo '</ul>';
	    }
}

function serverdate(){
		$today = date("F j, Y, g:i a"); 
		echo $today;
}
function signup_button(){
	include (get_template_directory() . '/library/options/options.php');
	$signupfeat_buttontext = get_option('dev_businessservices_signupfeat_buttontext');
	$signupfeat_buttontext_custom = get_option('dev_businessservices_signupfeat_buttontextcustom');
	
	if (($bp_existed == 'true') && ($signupfeat_buttontext_custom == "")){
	?>
		<a href="<?php echo get_option('home') ?>/register/" class="button"><?php echo $signupfeat_buttontext; ?></a>
		
		<?php		
	}
	else if ($signupfeat_buttontext_custom != ""){
		?>
			<a href="<?php echo $signupfeat_buttontext_custom; ?>" class="button"><?php echo $signupfeat_buttontext; ?></a>
		<?php
	}
	else{
		if ($multi_site_on == 'true'){
				?>
				<a href="<?php echo get_option('home') ?>/wp-signup.php" class="button"><?php echo $signupfeat_buttontext ?></a>
				<?php
		}
		else{
			?>
		  <a href="<?php echo get_option('home') ?>/wp-login.php" class="button"><?php echo $signupfeat_buttontext ?></a>
		<?php
		}
	}
	
}
?>