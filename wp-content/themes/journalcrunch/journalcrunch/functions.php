<?php

/*******************************
 MENUS SUPPORT
********************************/
if ( function_exists( 'wp_nav_menu' ) ){
	if (function_exists('add_theme_support')) {
		add_theme_support('nav-menus');
		add_action( 'init', 'register_my_menus' );
		function register_my_menus() {
			register_nav_menus(
				array(
					'main-menu' => __( 'Top Menu' )
				)
			);
		}
	}
}

/* CallBack functions for menus in case of earlier than 3.0 Wordpress version or if no menu is set yet*/

function primarymenu(){ ?>
			<div id="topMenu" class="ddsmoothmenu">
				<ul><li><div> Go to Admin > Appearance > Menus to set up the menu. You need to run WP 3.0+ for custom menus to work.</div></li></ul>
			</div>
<?php }

/*******************************
 THUMBNAIL SUPPORT
********************************/

add_theme_support( 'post-thumbnails' );
set_post_thumbnail_size( 255, 90, false );
add_image_size('featured-post-thumbnail',430,280,true);
add_image_size('slider-thumbnail',940,370,true);

/* Get the thumb original image full url | Important also for MultiSite installs!*/

function get_image_path ($post_id = null) {
	if ($post_id == null) {
		global $post;
		$post_id = $post->ID;
	}
	$theImageSrc = wp_get_attachment_url( get_post_thumbnail_id($post_id) );
	global $blog_id;
	if (isset($blog_id) && $blog_id > 0) {
		$imageParts = explode('/files/', $theImageSrc);
		if (isset($imageParts[1])) {
			$theImageSrc = '/blogs.dir/' . $blog_id . '/files/' . $imageParts[1];
		}
	}
	return $theImageSrc;
}

/* Get the thumb original image full url */
/*function get_thumb_urlfull ($postID) {
$image_id = get_post_thumbnail_id($post);  
$image_url = wp_get_attachment_image_src($image_id,'large');  
$image_url = $image_url[0]; 
return $image_url;
}*/

/*******************************
 EXCERPT LENGTH ADJUST
********************************/

function wpe_excerptlength_featured($length) {
    return 40;
}
function wpe_excerptlength_index($length) {
    return 20;
}

function wpe_excerpt($length_callback='', $more_callback='') {
    global $post;
    if(function_exists($length_callback)){
        add_filter('excerpt_length', $length_callback);
    }
    if(function_exists($more_callback)){
        add_filter('excerpt_more', $more_callback);
    }
    $output = get_the_excerpt();
    $output = apply_filters('wptexturize', $output);
    $output = apply_filters('convert_chars', $output);
    $output = '<p>'.$output.'</p>';
    echo $output;
}


/*******************************
 WIDGETS AREAS
********************************/

function journalcrunch_widgets_init() {
register_sidebar(array(
	'name' => 'sidebar',
	'before_widget' => '<div class="rightBox"><div class="rightBoxInner">	',
	'after_widget' => '</div></div>',
	'before_title' => '<h2>',
	'after_title' => '</h2>',
));

register_sidebar(array(
	'name' => 'footer',
	'before_widget' => '<div class="boxFooter">',
	'after_widget' => '</div>',
	'before_title' => '<h2 class="footerTitle">',
	'after_title' => '</h2>',
));

}

add_action( 'widgets_init', 'journalcrunch_widgets_init' );

/*******************************
 LATEST TWEETS WIDGET
********************************/


/**
 * Add function to widgets_init that'll load the widget */
 
add_action( 'widgets_init', 'latest_tweet_widget' );

function latest_tweet_widget() {
	register_widget( 'Latest_Tweets' );
}
class Latest_Tweets extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function Latest_Tweets() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'example', 'description' => __('Display a list of latest tweets', 'example') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'latest-tweets-widget' );

		/* Create the widget. */
		$this->WP_Widget( 'latest-tweets-widget', __('Latest Tweets', 'example'), $widget_ops, $control_ops );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$no_of_tweets = $instance['no_of_tweets'];

		/* Before widget (defined by themes). */
		echo $before_widget;

		if ( $title )
			echo '<h2 class="twitter">'. $title . $after_title;

		if ( $no_of_tweets )?>
				<div id="twitter">
							<ul id="twitter_update_list"></ul>
					<a href="http://twitter.com/<?php echo get_option('journal_twitter_user'); ?>" class="action">Follow Us on Twitter! &raquo;</a>
				</div>
				
				<script type="text/javascript" src="http://twitter.com/statuses/user_timeline/<?php echo get_option('journal_twitter_user'); ?>.json?callback=twitterCallback3&amp;count=<?php echo $no_of_tweets ?>">
				</script>
	<?php 

		/* After widget (defined by themes). */
		echo $after_widget;
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['no_of_tweets'] = strip_tags( $new_instance['no_of_tweets'] );

		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => __('Latest Tweets', 'example'), 'no_of_tweets' => '3' );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'hybrid'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>


		<!-- No of Tweets: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'no_of_tweets' ); ?>"><?php _e('No. of Tweets:', 'example'); ?></label>
			<input id="<?php echo $this->get_field_id( 'no_of_tweets' ); ?>" name="<?php echo $this->get_field_name( 'no_of_tweets' ); ?>" value="<?php echo $instance['no_of_tweets']; ?>" style="width:100%;" />
		</p>

	<?php
	}
}
	
/*******************************
 PAGINATION
********************************
 * Retrieve or display pagination code.
 *
 * The defaults for overwriting are:
 * 'page' - Default is null (int). The current page. This function will
 *      automatically determine the value.
 * 'pages' - Default is null (int). The total number of pages. This function will
 *      automatically determine the value.
 * 'range' - Default is 3 (int). The number of page links to show before and after
 *      the current page.
 * 'gap' - Default is 3 (int). The minimum number of pages before a gap is 
 *      replaced with ellipses (...).
 * 'anchor' - Default is 1 (int). The number of links to always show at begining
 *      and end of pagination
 * 'before' - Default is '<div class="emm-paginate">' (string). The html or text 
 *      to add before the pagination links.
 * 'after' - Default is '</div>' (string). The html or text to add after the
 *      pagination links.
 * 'title' - Default is '__('Pages:')' (string). The text to display before the
 *      pagination links.
 * 'next_page' - Default is '__('&raquo;')' (string). The text to use for the 
 *      next page link.
 * 'previous_page' - Default is '__('&laquo')' (string). The text to use for the 
 *      previous page link.
 * 'echo' - Default is 1 (int). To return the code instead of echo'ing, set this
 *      to 0 (zero).
 *
 * @author Eric Martin <eric@ericmmartin.com>
 * @copyright Copyright (c) 2009, Eric Martin
 * @version 1.0
 *
 * @param array|string $args Optional. Override default arguments.
 * @return string HTML content, if not displaying.
 */
 
function emm_paginate($args = null) {
	$defaults = array(
		'page' => null, 'pages' => null, 
		'range' => 3, 'gap' => 3, 'anchor' => 1,
		'before' => '<div class="emm-paginate">', 'after' => '</div>',
		'title' => __(''),
		'nextpage' => __('&raquo;'), 'previouspage' => __('&laquo'),
		'echo' => 1
	);

	$r = wp_parse_args($args, $defaults);
	extract($r, EXTR_SKIP);

	if (!$page && !$pages) {
		global $wp_query;

		$page = get_query_var('paged');
		$page = !empty($page) ? intval($page) : 1;

		$posts_per_page = intval(get_query_var('posts_per_page'));
		$pages = intval(ceil($wp_query->found_posts / $posts_per_page));
	}
	
	$output = "";
	if ($pages > 1) {	
		$output .= "$before<span class='emm-title'>$title</span>";
		$ellipsis = "<span class='emm-gap'>...</span>";

		if ($page > 1 && !empty($previouspage)) {
			$output .= "<a href='" . get_pagenum_link($page - 1) . "' class='emm-prev'>$previouspage</a>";
		}
		
		$min_links = $range * 2 + 1;
		$block_min = min($page - $range, $pages - $min_links);
		$block_high = max($page + $range, $min_links);
		$left_gap = (($block_min - $anchor - $gap) > 0) ? true : false;
		$right_gap = (($block_high + $anchor + $gap) < $pages) ? true : false;

		if ($left_gap && !$right_gap) {
			$output .= sprintf('%s%s%s', 
				emm_paginate_loop(1, $anchor), 
				$ellipsis, 
				emm_paginate_loop($block_min, $pages, $page)
			);
		}
		else if ($left_gap && $right_gap) {
			$output .= sprintf('%s%s%s%s%s', 
				emm_paginate_loop(1, $anchor), 
				$ellipsis, 
				emm_paginate_loop($block_min, $block_high, $page), 
				$ellipsis, 
				emm_paginate_loop(($pages - $anchor + 1), $pages)
			);
		}
		else if ($right_gap && !$left_gap) {
			$output .= sprintf('%s%s%s', 
				emm_paginate_loop(1, $block_high, $page),
				$ellipsis,
				emm_paginate_loop(($pages - $anchor + 1), $pages)
			);
		}
		else {
			$output .= emm_paginate_loop(1, $pages, $page);
		}

		if ($page < $pages && !empty($nextpage)) {
			$output .= "<a href='" . get_pagenum_link($page + 1) . "' class='emm-next'>$nextpage</a>";
		}

		$output .= $after;
	}

	if ($echo) {
		echo $output;
	}

	return $output;
}

/**
 * Helper function for pagination which builds the page links.
 *
 * @access private
 *
 * @author Eric Martin <eric@ericmmartin.com>
 * @copyright Copyright (c) 2009, Eric Martin
 * @version 1.0
 *
 * @param int $start The first link page.
 * @param int $max The last link page.
 * @return int $page Optional, default is 0. The current page.
 */
function emm_paginate_loop($start, $max, $page = 0) {
	$output = "";
	for ($i = $start; $i <= $max; $i++) {
		$output .= ($page === intval($i)) 
			? "<span class='emm-page emm-current'>$i</span>" 
			: "<a href='" . get_pagenum_link($i) . "' class='emm-page'>$i</a>";
	}
	return $output;
}

function post_is_in_descendant_category( $cats, $_post = null )
{
	foreach ( (array) $cats as $cat ) {
		// get_term_children() accepts integer ID only
		$descendants = get_term_children( (int) $cat, 'category');
		if ( $descendants && in_category( $descendants, $_post ) )
			return true;
	}
	return false;
}

/*******************************
 CUSTOM COMMENTS
********************************/

function mytheme_comment($comment, $args, $depth) {
   $GLOBALS['comment'] = $comment; ?>
   <li <?php comment_class('clearfix'); ?> id="li-comment-<?php comment_ID() ?>">
	 <?php echo get_avatar($comment,$size='38'); ?>
     <div id="comment-<?php comment_ID(); ?>">
	  <div class="comment-meta commentmetadata clearfix">
	    <?php printf(__('<strong>%s</strong>'), get_comment_author_link()) ?><?php edit_comment_link(__('(Edit)'),'  ','') ?> <span><?php printf(__('%1$s at %2$s'), get_comment_date(),  get_comment_time()) ?>
	  </span>
	  </div>
	  
      <div class="text">
		  <?php comment_text() ?>
	  </div>
	  
	  <?php if ($comment->comment_approved == '0') : ?>
         <em><?php _e('Your comment is awaiting moderation.') ?></em>
         <br />
      <?php endif; ?>

      <div class="reply">
         <?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
      </div>
     </div>
<?php }

/*******************************
  THEME OPTIONS PAGE
********************************/

add_action('admin_menu', 'journal_theme_page');
function journal_theme_page ()
{
	if ( count($_POST) > 0 && isset($_POST['journal_settings']) )
	{
		$options = array ('logo_img', 'logo_alt','contact_email','contact_text','cufon','twitter_user','latest_tweet','facebook_link','keywords','description','analytics','copyright','slider','slider_effect','slider_slices','slider_animation_speed','slider_pause_time','slider_caption_opacity','featured_posts','home_posts', 'box_model');
		
		foreach ( $options as $opt )
		{
			delete_option ( 'journal_'.$opt, $_POST[$opt] );
			add_option ( 'journal_'.$opt, $_POST[$opt] );	
		}			
		 
	}
	add_menu_page(__('JournalCrunch Options'), __('JournalCrunch Options'), 'edit_themes', basename(__FILE__), 'journal_settings');
	add_submenu_page(__('JournalCrunch Options'), __('JournalCrunch Options'), 'edit_themes', basename(__FILE__), 'journal_settings');
}
function journal_settings()
{?>
<div class="wrap">
	<h2>JournalCrunch Options Panel</h2>
	
<form method="post" action="">

	<fieldset style="border:1px solid #ddd; padding-bottom:20px; margin-top:20px;">
	<legend style="margin-left:5px; padding:0 5px;color:#2481C6; text-transform:uppercase;"><strong>General Settings</strong></legend>
	<table class="form-table">
		<!-- General settings -->
		
		<tr valign="top">
			<th scope="row"><label for="logo_img">Change logo (full path to logo image)</label></th>
			<td>
				<input name="logo_img" type="text" id="logo_img" value="<?php echo get_option('journal_logo_img'); ?>" class="regular-text" /><br />
				<em>current logo:</em> <br /> <img src="<?php echo get_option('journal_logo_img'); ?>" alt="<?php echo get_option('journal_logo_alt'); ?>" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="logo_alt">Logo ALT Text</label></th>
			<td>
				<input name="logo_alt" type="text" id="logo_alt" value="<?php echo get_option('journal_logo_alt'); ?>" class="regular-text" />
			</td>
		</tr>
        
		 <tr valign="top">
			<th scope="row"><label for="cufon">Cufon Font Replacement</label></th>
			<td>
				<select name="cufon" id="cufon">
					<option value="yes" <?php if(get_option('journal_cufon') == 'yes'){?>selected="selected"<?php }?>>Yes</option>		
					<option value="no" <?php if(get_option('journal_cufon') == 'no'){?>selected="selected"<?php }?>>No</option>
				</select>
			</td>
		</tr>
		
		<tr valign="top">
			<th scope="row"><label for="box_model">Way to display posts on category pages</label></th>
			<td>
				<select name="box_model" id="box_model">
					<option value="box" <?php if(get_option('journal_box_model') == 'box'){?>selected="selected"<?php }?>>Box</option>		
					<option value="normal" <?php if(get_option('journal_box_model') == 'normal'){?>selected="selected"<?php }?>>Normal</option>
				</select>
			</td>
		</tr>
	</table>
	</fieldset>
	
	<p class="submit">
		<input type="submit" name="Submit" class="button-primary" value="Save Changes" />
		<input type="hidden" name="journal_settings" value="save" style="display:none;" />
		</p>
	
	<fieldset style="border:1px solid #ddd; padding-bottom:20px; margin-top:20px;">
	<legend style="margin-left:5px; padding:0 5px; color:#2481C6;text-transform:uppercase;"><strong>Social Links</strong></legend>
		<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="twitter_user">Twitter Username</label></th>
			<td>
				<input name="twitter_user" type="text" id="twitter_user" value="<?php echo get_option('journal_twitter_user'); ?>" class="regular-text" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="latest_tweet">Display Header MouseOver Tip Latest Tweet </label></th>
			<td>
				<select name="latest_tweet" id="latest_tweet">		
					<option value="yes" <?php if(get_option('journal_latest_tweet') == 'yes'){?>selected="selected"<?php }?>>Yes</option>
                    <option value="no" <?php if(get_option('journal_latest_tweet') == 'no'){?>selected="selected"<?php }?>>No</option>
				</select>
			</td>
		</tr>
        <tr valign="top">
			<th scope="row"><label for="facebook_link">Facebook link</label></th>
			<td>
				<input name="facebook_link" type="text" id="facebook_link" value="<?php echo get_option('journal_facebook_link'); ?>" class="regular-text" />
			</td>
		</tr>
        </table>
        </fieldset>
		<p class="submit">
		<input type="submit" name="Submit" class="button-primary" value="Save Changes" />
		<input type="hidden" name="journal_settings" value="save" style="display:none;" />
		</p>
		
		<fieldset style="border:1px solid #ddd; padding-bottom:20px; margin-top:20px;">
	<legend style="margin-left:5px; padding:0 5px;color:#2481C6; text-transform:uppercase;"><strong>Homepage Settings</strong></legend>
	<table class="form-table">
		<!-- Homepage Boxes 1 -->
		<tr>
			<th colspan="2"><strong>Homepage Slider </strong></th>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="slider">Display Slider?</label></th>
			<td>
				<select name="slider" id="slider">		
					<option value="no" <?php if(get_option('journal_slider') == 'no'){?>selected="selected"<?php }?>>No</option>
                    <option value="yes" <?php if(get_option('journal_slider') == 'yes'){?>selected="selected"<?php }?>>Yes</option>
				</select><br/>
				<em>If you choose to display the slider, the featured posts wont be available anymore.</em>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="slider_effect">Slider Effect</label></th>
			<td>
				<select name="slider_effect" id="slider_effect">	
				sliceDown, sliceDownLeft, sliceUp, sliceUpLeft, sliceUpDown, sliceUpDownLeft, fold, fade, random	
					<option value="random" <?php if(get_option('journal_slider_effect') == 'random'){?>selected="selected"<?php }?>>random</option>
					<option value="fade" <?php if(get_option('journal_slider_effect') == 'fade'){?>selected="selected"<?php }?>>fade</option>
					<option value="fold" <?php if(get_option('journal_slider_effect') == 'fold'){?>selected="selected"<?php }?>>fold</option>
					<option value="sliceDown" <?php if(get_option('journal_slider_effect') == 'sliceDown'){?>selected="selected"<?php }?>>sliceDown</option>
					<option value="sliceDownLeft" <?php if(get_option('journal_slider_effect') == 'sliceDownLeft'){?>selected="selected"<?php }?>>sliceDownLeft</option>
					<option value="sliceUp" <?php if(get_option('journal_slider_effect') == 'sliceUp'){?>selected="selected"<?php }?>>sliceUp</option>
					<option value="sliceUpLeft" <?php if(get_option('journal_slider_effect') == 'sliceUpLeft'){?>selected="selected"<?php }?>>sliceUpLeft</option>
					<option value="sliceUpDown" <?php if(get_option('journal_slider_effect') == 'sliceUpDown'){?>selected="selected"<?php }?>>sliceUpDown</option>
					<option value="sliceUpDownLeft" <?php if(get_option('journal_slider_effect') == 'sliceUpDownLeft'){?>selected="selected"<?php }?>>sliceUpDownLeft</option>				
                   
				</select><br/>
				<em>Default is "random".</em>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="slider_slices">Slices</label></th>
			<td>
				<input name="slider_slices" type="text" id="slider_slices" value="<?php echo get_option('journal_slider_slices'); ?>" class="regular-text" /><br/>
				<em>Default is 15.</em>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="slider_animation_speed">Animation Speed</label></th>
			<td>
				<input name="slider_animation_speed" type="text" id="slider_animation_speed" value="<?php echo get_option('journal_slider_animation_speed'); ?>" class="regular-text" /><br/>
				<em>Default is 500.</em>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="slider_pause_time">Pause Time</label></th>
			<td>
				<input name="slider_pause_time" type="text" id="slider_pause_time" value="<?php echo get_option('journal_slider_pause_time'); ?>" class="regular-text" /><br/>
				<em>Default is 3000.</em>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="slider_caption_opacity">Caption Opacity</label></th>
			<td>
				<input name="slider_caption_opacity" type="text" id="slider_caption_opacity" value="<?php echo get_option('journal_slider_caption_opacity'); ?>" class="regular-text" /><br/>
				<em>Default is 0.8. This value should be between 0 and 1. </em>
			</td>
		</tr>
		<tr>
			<th colspan="2"><strong>Homepage Featured Posts </strong></th>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="featured_posts">Number of featured posts to be displayed</label></th>
			<td><em>To have posts featured you just need to add tag "featured" to the post.</em><br />
				<input name="featured_posts" type="text" id="featured_posts" value="<?php echo get_option('journal_featured_posts'); ?>" class="regular-text" />
				<br />
                <em>Default is 2. Use EVEN number for proper page display.</em>
			</td>
		</tr>
		<tr>
			<th colspan="2"><strong>Homepage Posts </strong></th>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="home_posts">Number of posts to be displayed</label></th>
			<td>
				<em>On the homepage you can display specific posts, tagged with "homepost". If there are no such posts, latest post will be displayed.</em><br/>
				<input name="home_posts" type="text" id="home_posts" value="<?php echo get_option('journal_home_posts'); ?>" class="regular-text" />
				<br />
                <em>Default is 6.</em>
			</td>
		</tr>
		
	</table>
	</fieldset>
	<p class="submit">
		<input type="submit" name="Submit" class="button-primary" value="Save Changes" />
		<input type="hidden" name="journal_settings" value="save" style="display:none;" />
		</p>
	
    <fieldset style="border:1px solid #ddd; padding-bottom:20px; margin-top:20px;">
	<legend style="margin-left:5px; padding:0 5px; color:#2481C6;text-transform:uppercase;"><strong>Contact Page Settings</strong></legend>
		<table class="form-table">	
        <tr>
        	<td colspan="2"></td>
        </tr>
         <tr valign="top">
			<th scope="row"><label for="contact_text">Contact Page Text</label></th>
			<td>
				<textarea name="contact_text" id="contact_text" rows="7" cols="70" style="font-size:11px;"><?php echo stripslashes(get_option('journal_contact_text')); ?></textarea>
			</td>
		</tr>
        <tr valign="top">
			<th scope="row"><label for="contact_email">Email Address for Contact Form</label></th>
			<td>
				<input name="contact_email" type="text" id="contact_email" value="<?php echo get_option('journal_contact_email'); ?>" class="regular-text" />
			</td>
		</tr>
        </table>
     </fieldset>
	 <p class="submit">
		<input type="submit" name="Submit" class="button-primary" value="Save Changes" />
		<input type="hidden" name="journal_settings" value="save" style="display:none;" />
	</p>
	
	<fieldset style="border:1px solid #ddd; padding-bottom:20px; margin-top:20px;">
	<legend style="margin-left:5px; padding:0 5px; color:#2481C6;text-transform:uppercase;"><strong>Footer</strong></legend>
		<table class="form-table">
		
		<tr>
			<th colspan="2"><strong>Copyright Info</strong></th>
		</tr>
        <tr>
			<th><label for="copyright">Copyright Text</label></th>
			<td>
				<textarea name="copyright" id="copyright" rows="4" cols="70" style="font-size:11px;"><?php echo stripslashes(get_option('journal_copyright')); ?></textarea><br />
				<em>You can use HTML for links etc.</em>
			</td>
		</tr>
		
		
	</table>
	</fieldset>
	<p class="submit">
		<input type="submit" name="Submit" class="button-primary" value="Save Changes" />
		<input type="hidden" name="journal_settings" value="save" style="display:none;" />
	</p>
        
      <fieldset style="border:1px solid #ddd; padding-bottom:20px; margin-top:20px;">
	<legend style="margin-left:5px; padding:0 5px; color:#2481C6;text-transform:uppercase;"><strong>SEO</strong></legend>
		<table class="form-table">
        <tr>
			<th><label for="keywords">Meta Keywords</label></th>
			<td>
				<textarea name="keywords" id="keywords" rows="7" cols="70" style="font-size:11px;"><?php echo get_option('journal_keywords'); ?></textarea><br />
                <em>Keywords comma separated</em>
			</td>
		</tr>
        <tr>
			<th><label for="description">Meta Description</label></th>
			<td>
				<textarea name="description" id="description" rows="7" cols="70" style="font-size:11px;"><?php echo get_option('journal_description'); ?></textarea>
			</td>
		</tr>
		<tr>
			<th><label for="ads">Google Analytics code:</label></th>
			<td>
				<textarea name="analytics" id="analytics" rows="7" cols="70" style="font-size:11px;"><?php echo stripslashes(get_option('journal_analytics')); ?></textarea>
			</td>
		</tr>
		
	</table>
	</fieldset>
	<p class="submit">
		<input type="submit" name="Submit" class="button-primary" value="Save Changes" />
		<input type="hidden" name="journal_settings" value="save" style="display:none;" />
	</p>
</form>
</div>
<?php }

/*******************************
  SHORTCODES
********************************/

function theme_formatter($content) {
	$new_content = '';
	$pattern_full = '{(\[raw\].*?\[/raw\])}is';
	$pattern_contents = '{\[raw\](.*?)\[/raw\]}is';
	$pieces = preg_split($pattern_full, $content, -1, PREG_SPLIT_DELIM_CAPTURE);
	
	foreach ($pieces as $piece) {
		if (preg_match($pattern_contents, $piece, $matches)) {
			$new_content .= $matches[1];
		} else {
			$new_content .= wptexturize(wpautop($piece));
		}
	}

	return $new_content;
}
remove_filter('the_content',	'wpautop');
remove_filter('the_content',	'wptexturize');

add_filter('the_content', 'theme_formatter', 99);

// DROPCAPS
function theme_shortcode_dropcaps($atts, $content = null, $code) {
	extract(shortcode_atts(array(
		'class' => '',
	), $atts));

	if($color){
		$color = ' '.$color;
	}
	return '<span class="' . $code.$class . '">' . do_shortcode($content) . '</span>';
}
add_shortcode('dropcap1', 'theme_shortcode_dropcaps');
add_shortcode('dropcap2', 'theme_shortcode_dropcaps');
add_shortcode('dropcap3', 'theme_shortcode_dropcaps');

// BLOCKQUOTES

function theme_shortcode_blockquote($atts, $content = null, $code) {
	extract(shortcode_atts(array(
		'align' => false,
		'cite' => false,
	), $atts));
	
	return '<blockquote' . ($align ? ' class="align' . $align . '"' : '') . '>' . do_shortcode($content) . ($cite ? '<p><cite>- ' . $cite . '</cite></p>' : '') . '</blockquote>';
}
add_shortcode('blockquote', 'theme_shortcode_blockquote');

// TEXT HIGHLIGHTS

function theme_shortcode_highlight($atts, $content = null, $code) {
	extract(shortcode_atts(array(
		'color' => false,
	), $atts));

	return '<span class="highlight'.(($color)?''.$color:'').'">'.do_shortcode($content).'</span>';
}
add_shortcode('highlight', 'theme_shortcode_highlight');

//MULTIPLE CONTENT COLUMS

function onehalf($atts, $content = null) {
	return '
<div class="onehalf">'.$content.'</div>
';
}
function onehalf_last($atts, $content = null) {
	return '
<div class="onehalf_last">'.$content.'</div>';
}

function onethird($atts, $content = null) {
	return '
<div class="onethird">'.$content.'</div>
';
}
function onethird_last($atts, $content = null) {
	return '
<div class="onethird_last">'.$content.'</div>';
}

add_shortcode('onehalf', 'onehalf');
add_shortcode('onehalf_last', 'onehalf_last');
add_shortcode('onethird', 'onethird');
add_shortcode('onethird_last', 'onethird_last');


/*******************************
   SLIDESHOW CUSTOM POST TYPES
********************************/
register_post_type( 'slideshow',
    array(
      'labels' => array(
        'name' => __( 'Slider Items' ), //this name will be used when will will call the investments in our theme
        'singular_name' => __( 'Slider Item' ),
		'add_new' => _x('Add New', 'slideshow'),
		'add_new_item' => __('Add New Slider Item'), //custom name to show up instead of Add New Post. Same for the following
		'edit_item' => __('Edit Slider Item'),
		'new_item' => __('New Slider Item'),
		'view_item' => __('View Slider Item'),
      ),
      'public' => true,
	  'show_ui' => true,
	  'exclude_from_search' => true,
	  'hierarchical' => false, //it means we cannot have parent and sub pages
	  'capability_type' => 'post', //will act like a normal post
	  'rewrite' => false, //this is used for rewriting the permalinks
	  'query_var' => false,
	  'supports' => array( 'title',	'thumbnail'), //the editing regions that will support
	  'menu_position' => 100
    )
  );
  
 /*******************************
   SLIDESHOW CUSTOM META
********************************/
 
add_action('admin_init','slideshow_meta_init');
 
function slideshow_meta_init()
{

	// add a meta box for each of the wordpress page types: posts and pages
	add_meta_box('slideshow_all_meta', 'Silder Item Settings', 'slideshow_meta_setup', 'slideshow', 'normal', 'high');
 
	// add a callback function to save any data a user enters in
	add_action('save_post','slideshow_meta_save');
}
 
function slideshow_meta_setup()
{
	global $post;
 
	// using an underscore, prevents the meta variable
	// from showing up in the custom fields section
	$meta = get_post_meta($post->ID,'_slideshow_meta',TRUE);
 
	echo '<div class="my_meta_control">
 
	<p style="margin:6px 0 8px;">Set the caption text and the link of the slider item. The image should be set as <strong>Featured Image</strong> of the item. For proper display use images  940px X 370px.</p>
	<br/>
	
	<label>Slider Item Caption Text</label>
 
	<p style="margin:6px 0 8px;">
		<textarea name="_slideshow_meta[caption]" rows="3" cols="40">';?><?php if(!empty($meta['caption'])) echo $meta['caption']; ?><?php echo '</textarea>
	</p>
 
	<label>Linking to (optional) <small>e.g. http://www.site5.com</small></label>
 
	<p style="margin:6px 0 8px;">
		<textarea name="_slideshow_meta[linkto]" rows="2" cols="40">';?><?php if(!empty($meta['linkto'])) echo $meta['linkto']; ?><?php echo '</textarea>
	</p>
 
	
 
</div>';
 
	// create a custom nonce for submit verification later
	echo '<input type="hidden" name="slideshow_meta_noncename" value="' . wp_create_nonce(__FILE__) . '" />';
}
 
function slideshow_meta_save($post_id) 
{
	// authentication checks
 
	// make sure data came from our meta box
	if (!wp_verify_nonce($_POST['slideshow_meta_noncename'],__FILE__)) return $post_id;
 
	// check user permissions
	if ($_POST['post_type'] == 'slideshow') 
	{
		if (!current_user_can('edit_page', $post_id)) return $post_id;
	}
	else 
	{
		if (!current_user_can('edit_post', $post_id)) return $post_id;
	}
 
	// authentication passed, save data
 
	// var types
	// single: _my_meta[var]
	// array: _my_meta[var][]
	// grouped array: _my_meta[var_group][0][var_1], _my_meta[var_group][0][var_2]
 
	$current_data = get_post_meta($post_id, '_slideshow_meta', TRUE);	
 
	$new_data = $_POST['_slideshow_meta'];
 
	slideshow_meta_clean($new_data);
 
	if ($current_data) 
	{
		if (is_null($new_data)) delete_post_meta($post_id,'_slideshow_meta');
		else update_post_meta($post_id,'_slideshow_meta',$new_data);
	}
	elseif (!is_null($new_data))
	{
		add_post_meta($post_id,'_slideshow_meta',$new_data,TRUE);
	}
 
	return $post_id;
}
 
function slideshow_meta_clean(&$arr)
{
	if (is_array($arr))
	{
		foreach ($arr as $i => $v)
		{
			if (is_array($arr[$i])) 
			{
				slideshow_meta_clean($arr[$i]);
 
				if (!count($arr[$i])) 
				{
					unset($arr[$i]);
				}
			}
			else 
			{
				if (trim($arr[$i]) == '') 
				{
					unset($arr[$i]);
				}
			}
		}
 
		if (!count($arr)) 
		{
			$arr = NULL;
		}
	}
}
 
 function edit_slideshow_columns($slideshow_columns) {
	$columns = array(
		"cb" => "<input type=\"checkbox\" />",
		"title" => _x('Slider Item Title', 'column name' ),
		"caption" => __('Caption Text' ),
		"link" => __('Link'),
		"thumbnail" => __('Thumbnail')
	);

	return $columns;
}
add_filter('manage_edit-slideshow_columns', 'edit_slideshow_columns');

function manage_slideshow_columns($column) {
	global $post;
	$slideshow_meta = get_post_meta($post->ID,'_slideshow_meta',TRUE);
	if ($post->post_type == "slideshow") {
		switch($column){
			case 'thumbnail':
				echo the_post_thumbnail('thumbnail');
				break;
			case 'caption':
				echo $slideshow_meta['caption'];
				break;
			case 'link':
				echo '<a href="'.$slideshow_meta['linkto'].'">'.$slideshow_meta['linkto'].'</a>';
				break;
		}
	}
}
add_action('manage_posts_custom_column', 'manage_slideshow_columns', 10, 2);
 
?>