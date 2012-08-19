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
					'main-menu' => __( 'Main Menu' )
				)
			);
		}
	}
}

/* CallBack functions for menus in case of earlier than 3.0 Wordpress version or if no menu is set yet*/

function primarymenu(){ ?>
			<div id="mainMenu" class="ddsmoothmenu">
				<ul>
					<?php wp_list_pages('title_li='); ?>
					<?php wp_list_categories('hide_empty=1&exclude=1&title_li='); ?>
				</ul>
			</div>
<?php }

/*******************************
 THUMBNAIL SUPPORT
********************************/

add_theme_support( 'post-thumbnails' );
set_post_thumbnail_size( 300, 200, true );

/* Get the thumb original image full url */

function get_thumb_urlfull ($postID) {
$image_id = get_post_thumbnail_id($post);  
$image_url = wp_get_attachment_image_src($image_id,'large');  
$image_url = $image_url[0]; 
return $image_url;
}

/*******************************
 EXCERPT LENGTH ADJUST
********************************/

function home_excerpt_length($length) {
	return 30;
}
add_filter('excerpt_length', 'home_excerpt_length');


/*******************************
 WIDGETS AREAS
********************************/

if ( function_exists('register_sidebar') )
register_sidebar(array(
	'name' => 'sidebar',
	'before_widget' => '<div class="rightBox">',
	'after_widget' => '</div>',
	'before_title' => '<h2>',
	'after_title' => '</h2>',
));

register_sidebar(array(
	'name' => 'footer',
	'before_widget' => '<div class="boxFooter">',
	'after_widget' => '</div>',
	'before_title' => '<h2>',
	'after_title' => '</h2>',
));
	
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
		'title' => __('Pages:'),
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
   	<div class="gravatar">
	 <?php echo get_avatar($comment,$size='50',$default='http://www.gravatar.com/avatar/61a58ec1c1fba116f8424035089b7c71?s=32&d=&r=G' ); ?>
	 <div class="gravatar_mask"></div>
	</div>
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

add_action('admin_menu', 'boldy_theme_page');
function boldy_theme_page ()
{
	if ( count($_POST) > 0 && isset($_POST['boldy_settings']) )
	{
		$options = array ('logo_img', 'logo_alt','contact_email','contact_text','cufon','linkedin_link','twitter_user','latest_tweet','facebook_link','keywords','description','analytics','copyright','home_box1','home_box1_link','home_box2','home_box2_link','home_box3','home_box3_link','blurb_enable','blurb_text','blurb_link','blurb_page', 'footer_actions','actions_hide','portfolio','blog','slider');
		
		foreach ( $options as $opt )
		{
			delete_option ( 'boldy_'.$opt, $_POST[$opt] );
			add_option ( 'boldy_'.$opt, $_POST[$opt] );	
		}			
		 
	}
	add_menu_page(__('Boldy Options'), __('Boldy Options'), 'edit_themes', basename(__FILE__), 'boldy_settings');
	add_submenu_page(__('Boldy Options'), __('Boldy Options'), 'edit_themes', basename(__FILE__), 'boldy_settings');
}
function boldy_settings()
{?>
<div class="wrap">
	<h2>Boldy Options Panel</h2>
	
<form method="post" action="">

	<fieldset style="border:1px solid #ddd; padding-bottom:20px; margin-top:20px;">
	<legend style="margin-left:5px; padding:0 5px;color:#2481C6; text-transform:uppercase;"><strong>General Settings</strong></legend>
	<table class="form-table">
		<!-- General settings -->
		
		<tr valign="top">
			<th scope="row"><label for="logo_img">Change logo (full path to logo image)</label></th>
			<td>
				<input name="logo_img" type="text" id="logo_img" value="<?php echo get_option('boldy_logo_img'); ?>" class="regular-text" /><br />
				<em>current logo:</em> <br /> <img src="<?php echo get_option('boldy_logo_img'); ?>" alt="<?php echo get_option('boldy_logo_alt'); ?>" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="logo_alt">Logo ALT Text</label></th>
			<td>
				<input name="logo_alt" type="text" id="logo_alt" value="<?php echo get_option('boldy_logo_alt'); ?>" class="regular-text" />
			</td>
		</tr>
        
		 <tr valign="top">
			<th scope="row"><label for="cufon">Cufon Font Replacement</label></th>
			<td>
				<select name="cufon" id="cufon">
					<option value="yes" <?php if(get_option('boldy_cufon') == 'yes'){?>selected="selected"<?php }?>>Yes</option>		
					<option value="no" <?php if(get_option('boldy_cufon') == 'no'){?>selected="selected"<?php }?>>No</option>
				</select>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="b">Choose Blog Category</label></th>
			<td>
				<?php wp_dropdown_categories("name=blog&hide_empty=0&show_option_none=".__('- Select -')."&selected=" .get_option('boldy_blog')); ?>
			</td>
		</tr>
		 <tr valign="top">
			<th scope="row"><label for="portfolio">Choose Portfolio Category</label></th>
			<td>
				<?php wp_dropdown_categories("name=portfolio&hide_empty=0&show_option_none=".__('- Select -')."&selected=" .get_option('boldy_portfolio')); ?>
			</td>
		</tr>
	</table>
	</fieldset>
	
	<p class="submit">
		<input type="submit" name="Submit" class="button-primary" value="Save Changes" />
		<input type="hidden" name="boldy_settings" value="save" style="display:none;" />
		</p>
	
	<fieldset style="border:1px solid #ddd; padding-bottom:20px; margin-top:20px;">
	<legend style="margin-left:5px; padding:0 5px; color:#2481C6;text-transform:uppercase;"><strong>Social Links</strong></legend>
		<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="twitter_user">Twitter Username</label></th>
			<td>
				<input name="twitter_user" type="text" id="twitter_user" value="<?php echo get_option('boldy_twitter_user'); ?>" class="regular-text" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="latest_tweet">Display Latest Tweet</label></th>
			<td>
				<select name="latest_tweet" id="latest_tweet">		
					<option value="yes" <?php if(get_option('boldy_latest_tweet') == 'yes'){?>selected="selected"<?php }?>>Yes</option>
                    <option value="no" <?php if(get_option('boldy_latest_tweet') == 'no'){?>selected="selected"<?php }?>>No</option>
				</select>
			</td>
		</tr>
        <tr valign="top">
			<th scope="row"><label for="facebook_link">Facebook link</label></th>
			<td>
				<input name="facebook_link" type="text" id="facebook_link" value="<?php echo get_option('boldy_facebook_link'); ?>" class="regular-text" />
			</td>
		</tr>
        <tr valign="top">
			<th scope="row"><label for="flickr_link">LinkedIn link</label></th>
			<td>
				<input name="linkedin_link" type="text" id="linkedin_link" value="<?php echo get_option('boldy_linkedin_link'); ?>" class="regular-text" />
			</td>
		</tr>
        </table>
        </fieldset>
		<p class="submit">
		<input type="submit" name="Submit" class="button-primary" value="Save Changes" />
		<input type="hidden" name="boldy_settings" value="save" style="display:none;" />
		</p>
		
		<fieldset style="border:1px solid #ddd; padding-bottom:20px; margin-top:20px;">
	<legend style="margin-left:5px; padding:0 5px;color:#2481C6; text-transform:uppercase;"><strong>Homepage Settings</strong></legend>
	<table class="form-table">
		<!-- Homepage Boxes 1 -->
		<tr>
			<th colspan="2"><strong>Homepage Slider </strong></th>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="slider">Homepage Slider Images Page</label></th>
			<td>
				<?php wp_dropdown_pages("name=slider&show_option_none=".__('- Select -')."&selected=" .get_option('boldy_slider')); ?>
			</td>
		</tr>
		<tr>
			<th colspan="2"><strong>Homepage Boxes </strong></th>
		</tr>
		<tr>
			<th colspan="2"> They should be ALL selected ! Other way the row wont appear at all.</th>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="home_box1">Home Box1 Page</label></th>
			<td>
				<?php wp_dropdown_pages("name=home_box1&show_option_none=".__('- Select -')."&selected=" .get_option('boldy_home_box1')); ?>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="home_box1_link">Home Box1 "read more" link</label></th>
			<td>
				<input name="home_box1_link" type="text" id="home_box1_link" value="<?php echo get_option('boldy_home_box1_link'); ?>" class="regular-text" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="home_box2">Homepage Box2 Page</label></th>
			<td>
				<?php wp_dropdown_pages("name=home_box2&show_option_none=".__('- Select -')."&selected=" .get_option('boldy_home_box2')); ?>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="home_box2_link">Home Box2 "read more" link</label></th>
			<td>
				<input name="home_box2_link" type="text" id="home_box2_link" value="<?php echo get_option('boldy_home_box2_link'); ?>" class="regular-text" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="home_box3">Home Box3 Page</label></th>
			<td>
				<?php wp_dropdown_pages("name=home_box3&show_option_none=".__('- Select -')."&selected=" .get_option('boldy_home_box3')); ?>
			</td>
		</tr>	
		<tr valign="top">
			<th scope="row"><label for="home_box3_link">Home Box3 "read more" link</label></th>
			<td>
				<input name="home_box3_link" type="text" id="home_box3_link" value="<?php echo get_option('boldy_home_box3_link'); ?>" class="regular-text" />
			</td>
		</tr>
		<tr>
			<th colspan="2"><strong>Homepage Blurb (request quote section) </strong></th>
		</tr>
		 <tr>
			<th><label for="blurb_enable">Display Homepage Blurb</label></th>
			<td>
				<select name="blurb_enable" id="blurb_enable"> 
					<option value="yes" <?php if(get_option('boldy_blurb_enable') == 'yes'){?>selected="selected"<?php }?>>Yes</option>		
					<option value="no" <?php if(get_option('boldy_blurb_enable') == 'no'){?>selected="selected"<?php }?>>No</option>
				</select><br />
                <em>If "Yes" is selected and text field is empty, the blurb wont appear</em>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="blurb_text">Blurb Text</label></th>
			<td>
				<textarea name="blurb_text" id="blurb_text" rows="3" cols="70" style="font-size:11px;"><?php echo stripslashes(get_option('boldy_blurb_text')); ?></textarea>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="blurb_link">Request Quote Link</label></th>
			<td>
				<input name="blurb_link" type="text" id="blurb_link" value="<?php echo get_option('boldy_blurb_link'); ?>" class="regular-text" />
				<br />
				<em>You can either enter a link manually or select a page to point at.</em>
			</td>
		</tr>
		
		<tr valign="top">
			<th scope="row"><label for="blurb_page">Request Quote Page</label></th>
			<td>
				<?php wp_dropdown_pages("name=blurb_page&show_option_none=".__('- Select -')."&selected=" .get_option('boldy_blurb_page')); ?>
				<br />
				<em>You can either enter a link manually or select a page to point at.</em>
			</td>
		</tr>
	</table>
	</fieldset>
	<p class="submit">
		<input type="submit" name="Submit" class="button-primary" value="Save Changes" />
		<input type="hidden" name="boldy_settings" value="save" style="display:none;" />
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
				<textarea name="contact_text" id="contact_text" rows="7" cols="70" style="font-size:11px;"><?php echo stripslashes(get_option('boldy_contact_text')); ?></textarea>
			</td>
		</tr>
        <tr valign="top">
			<th scope="row"><label for="contact_email">Email Address for Contact Form</label></th>
			<td>
				<input name="contact_email" type="text" id="contact_email" value="<?php echo get_option('boldy_contact_email'); ?>" class="regular-text" />
			</td>
		</tr>
        </table>
     </fieldset>
	 <p class="submit">
		<input type="submit" name="Submit" class="button-primary" value="Save Changes" />
		<input type="hidden" name="boldy_settings" value="save" style="display:none;" />
	</p>
	
	<fieldset style="border:1px solid #ddd; padding-bottom:20px; margin-top:20px;">
	<legend style="margin-left:5px; padding:0 5px; color:#2481C6;text-transform:uppercase;"><strong>Footer</strong></legend>
		<table class="form-table">
		<tr>
			<th colspan="2"><strong>Footer Twitter &amp; Quick Contact </strong></th>
		</tr>
		<tr>
			<th><label for="footer_actions">Display Footer Twitter &amp; Quick Contact Section</label></th>
			<td>
				<select name="footer_actions" id="footer_actions"> 
					<option value="yes" <?php if(get_option('boldy_footer_actions') == 'yes'){?>selected="selected"<?php }?>>Yes</option>		
					<option value="no" <?php if(get_option('boldy_footer_actions') == 'no'){?>selected="selected"<?php }?>>No</option>
				</select>
			</td>
		</tr>
		<tr>
			<th><label for="actions_hide">Default Section Visibility</label></th>
			<td>
				<select name="actions_hide" id="actions_hide"> 
					<option value="visible" <?php if(get_option('boldy_actions_hide') == 'visible'){?>selected="selected"<?php }?>>Visible</option>		
					<option value="hidden" <?php if(get_option('boldy_actions_hide') == 'hidden'){?>selected="selected"<?php }?>>Hidden</option>
				</select>
			</td>
		</tr>
		<tr>
			<th colspan="2"><strong>Copyright Info</strong></th>
		</tr>
        <tr>
			<th><label for="copyright">Copyright Text</label></th>
			<td>
				<textarea name="copyright" id="copyright" rows="4" cols="70" style="font-size:11px;"><?php echo stripslashes(get_option('boldy_copyright')); ?></textarea><br />
				<em>You can use HTML for links etc.</em>
			</td>
		</tr>
		
		
	</table>
	</fieldset>
	<p class="submit">
		<input type="submit" name="Submit" class="button-primary" value="Save Changes" />
		<input type="hidden" name="boldy_settings" value="save" style="display:none;" />
	</p>
        
      <fieldset style="border:1px solid #ddd; padding-bottom:20px; margin-top:20px;">
	<legend style="margin-left:5px; padding:0 5px; color:#2481C6;text-transform:uppercase;"><strong>SEO</strong></legend>
		<table class="form-table">
        <tr>
			<th><label for="keywords">Meta Keywords</label></th>
			<td>
				<textarea name="keywords" id="keywords" rows="7" cols="70" style="font-size:11px;"><?php echo get_option('boldy_keywords'); ?></textarea><br />
                <em>Keywords comma separated</em>
			</td>
		</tr>
        <tr>
			<th><label for="description">Meta Description</label></th>
			<td>
				<textarea name="description" id="description" rows="7" cols="70" style="font-size:11px;"><?php echo get_option('boldy_description'); ?></textarea>
			</td>
		</tr>
		<tr>
			<th><label for="ads">Google Analytics code:</label></th>
			<td>
				<textarea name="analytics" id="analytics" rows="7" cols="70" style="font-size:11px;"><?php echo stripslashes(get_option('boldy_analytics')); ?></textarea>
			</td>
		</tr>
		
	</table>
	</fieldset>
	<p class="submit">
		<input type="submit" name="Submit" class="button-primary" value="Save Changes" />
		<input type="hidden" name="boldy_settings" value="save" style="display:none;" />
	</p>
</form>
</div>
<?php }
/*******************************
  CONTACT FORM 
********************************/

 function hexstr($hexstr) {
  $hexstr = str_replace(' ', '', $hexstr);
  $hexstr = str_replace('\x', '', $hexstr);
  $retstr = pack('H*', $hexstr);
  return $retstr;
}

function strhex($string) {
  $hexstr = unpack('H*', $string);
  return array_shift($hexstr);
}
?>