<?php
/*
Plugin Name: Really simple Facebook Twitter share buttons
Plugin URI: http://www.whiletrue.it
Description: Puts Facebook, Twitter, LinkedIn and other share buttons of your choice above or below your posts.
Author: WhileTrue
Version: 1.8.4
Author URI: http://www.whiletrue.it
*/

/*
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 2, 
    as published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
*/


// ACTION AND FILTERS

add_action('init', 'really_simple_share_init');
add_action('wp_print_styles', 'really_simple_share_style');

add_filter('the_content', 'really_simple_share_content');
add_filter('the_excerpt', 'really_simple_share_excerpt');

add_filter('plugin_action_links', 'really_simple_share_add_settings_link', 10, 2 );

add_action('admin_menu', 'really_simple_share_menu');

add_shortcode( 'really_simple_share', 'really_simple_share_shortcode' );

// PUBLIC FUNCTIONS

function really_simple_share_init() {
	// DISABLED IN THE ADMIN PAGES
	if (is_admin()) {
		return;
	}

	// GET ARRAY OF STORED VALUES
	$option = really_simple_share_get_options_stored();

	if ($option['active_buttons']['facebook']==true) {
		wp_enqueue_script('really_simple_share_facebook', 'http://static.ak.fbcdn.net/connect.php/js/FB.Share');
	}
	if ($option['active_buttons']['linkedin']==true) {
		wp_enqueue_script('really_simple_share_linkedin', 'http://platform.linkedin.com/in.js');
	}
	
	if ($option['active_buttons']['buzz']==true) {
		wp_enqueue_script('really_simple_share_buzz', 'http://www.google.com/buzz/api/button.js');
	}
	if ($option['active_buttons']['google1']==true) {
		wp_enqueue_script('really_simple_share_google1', 'http://apis.google.com/js/plusone.js');
	}
	if ($option['active_buttons']['flattr']==true) {
		wp_enqueue_script('really_simple_share_flattr', 'http://api.flattr.com/js/0.6/load.js?mode=auto&#038;ver=0.6');
	}
	if ($option['active_buttons']['twitter']==true) {
		wp_enqueue_script('really_simple_share_twitter', 'http://platform.twitter.com/widgets.js');
	}
}    


function really_simple_share_style() {
	// GET ARRAY OF STORED VALUES
	$option = really_simple_share_get_options_stored();
	// CHECK IF IT'S DISABLED BY AN OPTION
	if ($option['disable_default_styles']) {
		return;
	}

    $myStyleUrl = WP_PLUGIN_URL  .'/really-simple-facebook-twitter-share-buttons/style.css';
    $myStyleFile = WP_PLUGIN_DIR .'/really-simple-facebook-twitter-share-buttons/style.css';
    if ( file_exists($myStyleFile) ) {
        wp_register_style('really_simple_share_style', $myStyleUrl);
        wp_enqueue_style ('really_simple_share_style');
    }
}


function really_simple_share_menu() {
	add_options_page('Really simple share Options', 'Really simple share', 'manage_options', 'really_simple_share_options', 'really_simple_share_options');
}


function really_simple_share_add_settings_link($links, $file) {
	static $this_plugin;
	if (!$this_plugin) $this_plugin = plugin_basename(__FILE__);
 
	if ($file == $this_plugin){
		$settings_link = '<a href="admin.php?page=really_simple_share_options">'.__("Settings").'</a>';
		array_unshift($links, $settings_link);
	}
	return $links;
} 


function really_simple_share_content ($content) {
	return really_simple_share ($content, 'the_content');
}


function really_simple_share_excerpt ($content) {
	return really_simple_share ($content, 'the_excerpt');
}


function really_simple_share ($content, $filter, $link='', $title='') {
	static $last_execution = '';

	// IF the_excerpt IS EXECUTED AFTER the_content MUST DISCARD ANY CHANGE MADE BY the_content
	if ($filter=='the_excerpt' and $last_execution=='the_content') {
		// WE TEMPORARILY REMOVE CONTENT FILTERING, THEN CALL THE_EXCERPT
		remove_filter('the_content', 'really_simple_share_content');
		$last_execution = 'the_excerpt';
		return the_excerpt();
	}
	if ($filter=='the_excerpt' and $last_execution=='the_excerpt') {
		// WE RESTORE THE PREVOIUSLY REMOVED CONTENT FILTERING, FOR FURTHER EXECUTIONS (POSSIBLY NOT INVOLVING 
		add_filter('the_content', 'really_simple_share_content');
	}

	// IF THE "DISABLE" CUSTOM FIELD IS FOUND, BLOCK EXECUTION
	// unless the shortcode was used in which case assume the disable
	// should be overridden, allowing us to disable general settings for a page
	// but insert buttons in a particular content area
	$custom_field_disable = get_post_custom_values('really_simple_share_disable');
	if ($custom_field_disable[0]=='yes' and $filter!='shortcode') {
		return $content;
	}
	
	//GET ARRAY OF STORED VALUES
	$option = really_simple_share_get_options_stored();

	if ($filter!='shortcode') {
		if (is_single()) {
			if (!$option['show_in']['posts']) { return $content; }
		} else if (is_singular()) {
			if (!$option['show_in']['pages']) {
				return $content;
			}
		} else if (is_home()) {
			if (!$option['show_in']['home_page']) {	return $content; }
		} else if (is_tag()) {
			if (!$option['show_in']['tags']) { return $content; }
		} else if (is_category()) {
			if (!$option['show_in']['categories']) { return $content; }
		} else if (is_date()) {
			if (!$option['show_in']['dates']) { return $content; }
		} else if (is_author()) {
			//IF DISABLED INSIDE PAGES
			if (!$option['show_in']['authors']) { return $content; }
		} else if (is_search()) {
			if (!$option['show_in']['search']) { return $content; }
		} else {
			// IF NONE OF PREVIOUS, IS DISABLED
			return $content;
		}
	}
	$first_shown = false; // NO PADDING FOR THE FIRST BUTTON
	
	// IF LINK AND TITLE ARE NOT SET, USE DEFAULT GET_PERMALINK AND GET_THE_TITLE FUNCTIONS
	if ($link=='' and $title=='') {
		$link = get_permalink();
		$title = get_the_title();
	}
	
	$height = ($option['layout']=='button') ? 33 : 66;

	$out = '<div style="height:'.$height.'px;" class="really_simple_share">';
	if ($option['active_buttons']['facebook']==true) {
		
		$option_layout = ($option['layout']=='button') ? 'button_count' : 'box_count';
		// REMOVE HTTP:// FROM STRING
		$facebook_link = (substr($link,0,7)=='http://') ? substr($link,7) : $link;
		$out .= '<div class="really_simple_share_facebook"> 
				<a name="fb_share" type="'.$option_layout.'" href="http://www.facebook.com/sharer.php" share_url="'.$facebook_link.'">Share</a> 
			</div>';
	}
	if ($option['active_buttons']['facebook_like']==true) {
		$option_layout = ($option['layout']=='button') ? 'button_count' : 'box_count';
		$option_height = ($option['layout']=='button') ? 27 : 60;
		// OPTION facebook_like_text FILTERING
		$option_facebook_like_text = ($option['facebook_like_text']=='recommend') ? 'recommend' : 'like';
		$out .= '<div style="width:'.$option['facebook_like_width'].'px;" class="really_simple_share_facebook_like"> 
				<iframe src="http://www.facebook.com/plugins/like.php?href='.urlencode($link).'&amp;layout='.$option_layout.'&amp;show_faces=false&amp;width='.$option['facebook_like_width'].'&amp;action='.$option_facebook_like_text.'&amp;colorscheme=light&amp;send=false&amp;height='.$option_height.'" 
					scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:'.$option['facebook_like_width'].'px; height:'.$option_height.'px;" allowTransparency="true"></iframe>
			</div>';
		// FACEBOOK LIKE SEND BUTTON CURRENTLY IN FBML MODE - WILL BE MERGED IN THE LIKE BUTTON WHEN FACEBOOK RELEASES IT	
		if ($option['facebook_like_send']) {
			static $facebook_like_send_script_inserted = false;
			if (!$facebook_like_send_script_inserted) {
				$out .= '<script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>';
				$facebook_like_send_script_inserted = true;
			}
			$out .= '<div style="float:left; width:50px; padding-left:10px;" class="really_simple_share_facebook_like_send">
				<fb:send href="'.$link.'" font=""></fb:send>
				</div>';
		}	
	}
	if ($option['active_buttons']['linkedin']==true) {
		$option_layout = ($option['layout']=='button') ? 'data-counter="right"' : 'data-counter="top"';
		$option_layout = ($option['linkedin_count']) ? $option_layout : '';
		$out .= '<div class="really_simple_share_linkedin"> 
				<script type="IN/Share" '.$option_layout.' data-url="'.$link.'"></script>
			</div>';
	}
	if ($option['active_buttons']['buzz']==true) {
		$option_layout = ($option['layout']=='button') ? 'small-count' : 'normal-count';
		$out .= '<div class="really_simple_share_buzz"> 
				<a title="Post to Google Buzz" class="google-buzz-button" href="http://www.google.com/buzz/post" data-button-style="'.$option_layout.'" 
					data-url="'.$link.'"></a>
			</div>';
	}
	if ($option['active_buttons']['digg']==true) {
		$option_layout = ($option['layout']=='button') ? 'DiggCompact' : 'DiggMedium';
		// THE DIGG JS FILE DOES NOT ALWAYS WORK INSIDE THE <HEAD> SECTION, WE KEEP IT HERE
		$out .= '<div class="really_simple_share_digg"> 
				<script type="text/javascript" src="http://widgets.digg.com/buttons.js"></script>
				<a class="DiggThisButton '.$option_layout.'" href="http://digg.com/submit?url='.$link.'&amp;title='.htmlentities($title).'"></a>	
			</div>';
	}
	if ($option['active_buttons']['stumbleupon']==true) {
		$option_layout = ($option['layout']=='button') ? '1' : '5';
		$out .= '<div class="really_simple_share_stumbleupon"> 
				<script type="text/javascript" src="http://www.stumbleupon.com/hostedbadge.php?s='.$option_layout.'&amp;r='.$link.'"></script>
			</div>';
	}	
	if ($option['active_buttons']['hyves']==true) {
		$out .= '<div class="really_simple_share_hyves"> 
				<iframe src="http://www.hyves.nl/respect/button?url='.$link.'" 
					style="border: medium none; overflow:hidden; width:150px; height:21px;" scrolling="no" 
					frameborder="0" allowTransparency="true" ></iframe>
			</div>';
	}		
	if ($option['active_buttons']['reddit']==true) {
		$option_layout = ($option['layout']=='button') ? '1' : '3';
		$out .= '<div class="really_simple_share_hyves"> 
				<script type="text/javascript" src="http://www.reddit.com/static/button/button'.$option_layout.'.js?newwindow=1&amp;url='.$link.'"></script>
			</div>';
	}	
	if ($option['active_buttons']['email']==true) {
		$out .= '<div class="really_simple_share_email"> 
				<a href="mailto:?subject='.$title.'&amp;body='.$title.' - '.$link.'"><img src="'.WP_PLUGIN_URL.'/really-simple-facebook-twitter-share-buttons/email.png" alt="Email" title="Email" /></a> 
			</div>';
	}
	if ($option['active_buttons']['google1']==true) {
		$option_layout = ($option['layout']=='button') ? 'medium' : 'tall';
		$data_count = ($option['google1_count']) ? '' : 'count="false"';
		$out .= '<div style="width:'.$option['google1_width'].'px;" class="really_simple_share_google1"> 
				<g:plusone size="'.$option_layout.'" href="'.$link.'" '.$data_count.'></g:plusone>
			</div>';
	}
	if ($option['active_buttons']['flattr']==true) {
		$language = 'en_GB';
		$option_layout = ($option['layout']=='button') ? 'button:compact' : '';
		$out .= '<div class="really_simple_share_flattr"> 
				<a class="FlattrButton" style="display:none;" href="'.$link.'" title="'.strip_tags($title).'" rev="flattr;uid:'.$option['flattr_uid'].';language:'.$language.';category:text;tags:'.strip_tags(get_the_tag_list('', ',', '')).';'.$option_layout.';">'.$title.'</a>
			</div>';
	}
	if ($option['active_buttons']['tipy']==true) {
		$option_layout = ($option['layout']=='button') ? 'tipy_button_compact' : 'tipy_button';
		$option_image  = ($option['layout']=='button') ? 'button_compact' : 'button';
		$out .= '<div class="really_simple_share_tipy">
				<script type="text/javascript">
					(function() {
					var s = document.createElement("script"), s1 = document.getElementsByTagName("script")[0];
					s.type = "text/javascript";
					s.async = true;
					s.src = "http://www.tipy.com/button.js";
					s1.parentNode.insertBefore(s, s1);
					})();
				</script> 
				<a href="http://www.tipy.com/s/'.$option['tipy_uid'].'" class="'.$option_layout.'"><img src="http://www.tipy.com/'.$option_image.'.gif" border="0"></a>
			</div>';
	}
	if ($option['active_buttons']['twitter']==true) {
		$option_layout = ($option['layout']=='button') ? 'horizontal' : 'vertical';
		$data_count = ($option['twitter_count']) ? $option_layout : 'none';
		$out .= '<div style="width:'.$option['twitter_width'].'px;" class="really_simple_share_twitter"> 
				<a href="http://twitter.com/share" class="twitter-share-button" data-count="'.$data_count.'" 
					data-text="'.strip_tags($title).stripslashes($option['twitter_text']).'" data-url="'.$link.'"></a> 
			</div>';
	}
	
	$out .= '</div>
		<div style="clear:both;"></div>';

	// REMEMBER LAST FILTER EXECUTION TO HANDLE the_excerpt VS the_content	
	$last_execution = $filter;
	
	if ($filter=='shortcode') {
		return $out;
	}

	if ($option['position']=='both') {
		return $out.$content.$out;
	} else if ($option['position']=='below') {
		return $content.$out;
	} else {
		return $out.$content;
	}
}

function really_simple_share_options () {

	$option_name = 'really_simple_share';

	//must check that the user has the required capability 
	if (!current_user_can('manage_options')) {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}

	$active_buttons = array(
		'facebook_like'=>'Facebook like',
		'facebook'=>'(old) Facebook share',
		'twitter'=>'Twitter',
		'linkedin'=>'Linkedin',
		'google1'=>'Google "+1"',
		'buzz'=>'Google Buzz',
		'digg'=>'Digg',
		'stumbleupon'=>'Stumbleupon',
		'hyves'=>'Hyves (Duch social net)',
		'reddit'=>'Reddit',
		'flattr'=>'Flattr',
		'email'=>'Email',
		'tipy'=>'Tipy'
	);	

	$show_in = array(
		'posts'=>'Single posts',
		'pages'=>'Pages',
		'home_page'=>'Home page',
		'tags'=>'Tags',
		'categories'=>'Categories',
		'dates'=>'Date based archives',
		'authors'=>'Author archives',
		'search'=>'Search results',
	);
	
	$out = '';
	
	// See if the user has posted us some information
	if( isset($_POST['really_simple_share_position'])) {
		$option = array();

		foreach (array_keys($active_buttons) as $item) {
			$option['active_buttons'][$item] = (isset($_POST['really_simple_share_active_'.$item]) and $_POST['really_simple_share_active_'.$item]=='on') ? true : false;
		}
		foreach (array_keys($show_in) as $item) {
			$option['show_in'][$item] = (isset($_POST['really_simple_share_show_'.$item]) and $_POST['really_simple_share_show_'.$item]=='on') ? true : false;
		}
		$option['position'] = esc_html($_POST['really_simple_share_position']);
		$option['layout'] = esc_html($_POST['really_simple_share_layout']);
		$option['disable_default_styles'] = (isset($_POST['really_simple_share_disable_default_styles']) and $_POST['really_simple_share_disable_default_styles']=='on') ? true : false;
		$option['facebook_like_width'] = esc_html($_POST['really_simple_share_facebook_like_width']);
		$option['facebook_like_text'] = ($_POST['really_simple_share_facebook_like_text']=='recommend') ? 'recommend' : 'like';
		$option['facebook_like_send'] = (isset($_POST['really_simple_share_facebook_like_send']) and $_POST['really_simple_share_facebook_like_send']=='on') ? true : false;
		$option['flattr_uid'] = esc_html($_POST['really_simple_share_flattr_uid']);
		$option['google1_count'] = (isset($_POST['really_simple_share_google1_count']) and $_POST['really_simple_share_google1_count']=='on') ? true : false;
		$option['google1_width'] = esc_html($_POST['really_simple_share_google1_width']);
		$option['linkedin_count'] = (isset($_POST['really_simple_share_linkedin_count']) and $_POST['really_simple_share_linkedin_count']=='on') ? true : false;
		$option['tipy_uid'] = esc_html($_POST['really_simple_share_tipy_uid']);
		$option['twitter_count'] = (isset($_POST['really_simple_share_twitter_count']) and $_POST['really_simple_share_twitter_count']=='on') ? true : false;
		$option['twitter_width'] = esc_html($_POST['really_simple_share_twitter_width']);
		$option['twitter_text'] = esc_html($_POST['really_simple_share_twitter_text']);
		
		update_option($option_name, $option);
		// Put a settings updated message on the screen
		$out .= '<div class="updated"><p><strong>'.__('Settings saved.', 'menu-test' ).'</strong></p></div>';
	}
	
	//GET ARRAY OF STORED VALUES
	$option = really_simple_share_get_options_stored();
	
	$sel_above = ($option['position']=='above') ? 'selected="selected"' : '';
	$sel_below = ($option['position']=='below') ? 'selected="selected"' : '';
	$sel_both  = ($option['position']=='both' ) ? 'selected="selected"' : '';

	$sel_button = ($option['layout']=='button') ? 'selected="selected"' : '';
	$sel_box = ($option['layout']=='box') ? 'selected="selected"' : '';

	$sel_like      = ($option['facebook_like_text']=='like'     ) ? 'selected="selected"' : '';
	$sel_recommend = ($option['facebook_like_text']=='recommend') ? 'selected="selected"' : '';
	
	$disable_default_styles = ($option['disable_default_styles']) ? 'checked="checked"' : '';
	$facebook_like_show_send_button = ($option['facebook_like_send']) ? 'checked="checked"' : '';
	$google1_count = ($option['google1_count']) ? 'checked="checked"' : '';
	$linkedin_count = ($option['linkedin_count']) ? 'checked="checked"' : '';
	$twitter_count = ($option['twitter_count']) ? 'checked="checked"' : '';

	// SETTINGS FORM

	$out .= '
	<style>
	#really_simple_share_form h3 { cursor: default; }
	#really_simple_share_form td { vertical-align:top; padding-bottom:15px; }
	</style>
	
	<div class="wrap">
	<h2>'.__( 'Really simple Facebook and Twitter share buttons', 'menu-test' ).'</h2>
	<div id="poststuff" style="padding-top:10px; position:relative;">

	<div style="float:left; width:74%; padding-right:1%;">

		<form id="really_simple_share_form" name="form1" method="post" action="">

		<div class="postbox">
		<h3>'.__("General options", 'menu-test' ).'</h3>
		<div class="inside">
			<table>
			<tr><td style="width:130px;">'.__("Active share buttons", 'menu-test' ).':</td>
			<td>';
		
			foreach ($active_buttons as $name => $text) {
				$checked = ($option['active_buttons'][$name]) ? 'checked="checked"' : '';
				$out .= '<div style="width:250px; float:left;">
						<input type="checkbox" name="really_simple_share_active_'.$name.'" '.$checked.' /> '
						. __($text, 'menu-test' ).' &nbsp;&nbsp;</div>';
			}

			$out .= '</td></tr>
			<tr><td>'.__("Show buttons in these pages", 'menu-test' ).':</td>
			<td>';

			foreach ($show_in as $name => $text) {
				$checked = ($option['show_in'][$name]) ? 'checked="checked"' : '';
				$out .= '<div style="width:250px; float:left;">
						<input type="checkbox" name="really_simple_share_show_'.$name.'" '.$checked.' /> '
						. __($text, 'menu-test' ).' &nbsp;&nbsp;</div>';
			}

			$out .= '</td></tr>
			<tr><td>'.__("Position", 'menu-test' ).':</td>
			<td><select name="really_simple_share_position">
				<option value="above" '.$sel_above.' > '.__('only above the post', 'menu-test' ).'</option>
				<option value="below" '.$sel_below.' > '.__('only below the post', 'menu-test' ).'</option>
				<option value="both"  '.$sel_both.'  > '.__('above and below the post', 'menu-test' ).'</option>
				</select>
			</td></tr>
			<tr><td>'.__("Layout", 'menu-test' ).':</td>
			<td><select name="really_simple_share_layout">
				<option value="button" '.$sel_button.' > '.__('button', 'menu-test' ).'</option>
				<option value="box" '.$sel_box.' > '.__('box', 'menu-test' ).'</option>
				</select>
			</td></tr>
			<tr><td>'.__("Disable default styles", 'menu-test' ).':</td>
			<td><input type="checkbox" name="really_simple_share_disable_default_styles" '.$disable_default_styles.' />
			</td></tr>
			</table>
		</div>
		</div>'
		.really_simple_share_box_content('Facebook Like button options', 
			array(
				'Button width'=>'
					<input type="text" name="really_simple_share_facebook_like_width" value="'.stripslashes($option['facebook_like_width']).'" size="10"> px<br />
					<span class="description">'.__("default: 100", 'menu-test' ).'</span>
				',
				'Button text'=>'
					<select name="really_simple_share_facebook_like_text">
						<option value="like" '.$sel_like.' > '.__('like', 'menu-test' ).'</option>
						<option value="recommend" '.$sel_recommend.' > '.__('recommend', 'menu-test' ).'</option>
					</select>
				',
				'Show Send button'=>'
					<input type="checkbox" name="really_simple_share_facebook_like_send" '.$facebook_like_show_send_button.' />
				'
			)
		)
		.really_simple_share_box_content('Flattr button options', 
			array('Flattr UID'=>'
					<input type="text" name="really_simple_share_flattr_uid" value="'.stripslashes($option['flattr_uid']).'" size="10"><br />
					<span class="description">'.__("this field is mandatory if you want to use the Flattr button", 'menu-test' ).'</span>
				'
			)
		)
		.really_simple_share_box_content('Google +1 button options', 
			array(
				'Button width'=>'
					<input type="text" name="really_simple_share_google1_width" value="'.stripslashes($option['google1_width']).'" size="10"> px<br />
					<span class="description">'.__("default: 90", 'menu-test' ).'</span>
				',
				'Show counter'=>'
					<input type="checkbox" name="really_simple_share_google1_count" '.$google1_count.' />
				'
			)
		)
		.really_simple_share_box_content('Linkedin button options', 
			array(
				'Show counter'=>'
					<input type="checkbox" name="really_simple_share_linkedin_count" '.$linkedin_count.' />
				'
			)
		)
		.really_simple_share_box_content('Tipy button options', 
			array('Tipy Website ID'=>'
					<input type="text" name="really_simple_share_tipy_uid" value="'.stripslashes($option['tipy_uid']).'" size="10"><br />
					<span class="description">'.__("this numeric field is mandatory if you want to use the Tipy button", 'menu-test' ).'</span>
				'
			)
		)
		.really_simple_share_box_content('Twitter button options', 
			array(
				'Button width'=>'
					<input type="text" name="really_simple_share_twitter_width" value="'.stripslashes($option['twitter_width']).'" size="10"> px<br />
					<span class="description">'.__("default: 110", 'menu-test' ).'</span>				
				',
				'Additional text'=>'
					<input type="text" name="really_simple_share_twitter_text" value="'.stripslashes($option['twitter_text']).'" size="25"><br />
					<span class="description">'.__("optional text added at the end of every tweet, e.g. ' (via @authorofblogentry)'.
					If you use it, insert an initial space or puntuation mark.", 'menu-test' ).'</span>
				',
				'Show counter'=>'<input type="checkbox" name="really_simple_share_twitter_count" '.$twitter_count.' />'
			)
		)
		.'<p class="submit">
			<input type="submit" name="Submit" class="button-primary" value="'.esc_attr('Save Changes').'" />
		</p>

		</form>

	</div>
	
	<div style="float:right; width:25%;">'
		.really_simple_share_box_content('PremiumPress Shopping Cart', '
			<a target="_blank" href="https://secure.avangate.com/order/product.php?PRODS=2929632&amp;QTY=1&amp;AFFILIATE=26764&amp;AFFSRC=really_simple_share_plugin">
				<img border="0" src="http://shopperpress.com/inc/images/banners/180x150.png" style="display: block; margin-left: auto; margin-right: auto;">
			</a>
		')
		.really_simple_share_box_content('Additional info', '
			<b>Selective use</b><br />
			If you want to place the active buttons only in selected posts, put the [really_simple_share] shortcode inside the post text.<br /><br />
			<b>Selective hide</b><br />
			If you want to hide the share buttons inside selected posts, set the "really_simple_share_disable" custom field with value "yes".
		')
		.really_simple_share_box_content('Really simple, isn\'t it?', '
			Most of the actual plugin features were requested by users and developed for the sake of doing it.<br /><br />
			If you want to be sure this passion lasts centuries, please consider donating some cents!<br /><br />
			<div style="text-align: center;">
			<form method="post" action="https://www.paypal.com/cgi-bin/webscr">
			<input value="_s-xclick" name="cmd" type="hidden">
			<input value="-----BEGIN PKCS7-----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-----END PKCS7----- " name="encrypted" type="hidden"> 
			<input alt="PayPal - The safer, easier way to pay online!" name="submit" border="0" src="https://www.paypalobjects.com/WEBSCR-640-20110306-1/en_US/i/btn/btn_donateCC_LG.gif" type="image"> 
			<img height="1" width="1" src="https://www.paypalobjects.com/WEBSCR-640-20110306-1/it_IT/i/scr/pixel.gif" border="0"> 
			</form>
			</div>
		')
	.'</div>

	</div>
	</div>
	';
	echo $out; 
}


// SHORTCODE FOR ALL ACTIVE BUTTONS
function really_simple_share_shortcode ($atts) {
	return really_simple_share ('', 'shortcode');
}


//FUNCTION AVAILABLE FOR EXTERNAL INCLUDING INSIDE THEMES AND OTHER PLUGINS
function really_simple_share_publish ($link='', $title='') {
	return really_simple_share ('', 'shortcode', $link, $title);
}



// PRIVATE FUNCTIONS

function really_simple_share_box_content ($title, $content) {
	if (is_array($content)) {
		$content_string = '<table>';
		foreach ($content as $name=>$value) {
			$content_string .= '<tr>
				<td style="width:130px;">'.__($name, 'menu-test' ).':</td>	
				<td>'.$value.'</td>
				</tr>';
		}
		$content_string .= '</table>';
	} else {
		$content_string = $content;
	}

	$out = '
		<div class="postbox">
			<h3>'.__($title, 'menu-test' ).'</h3>
			<div class="inside">'.$content_string.'</div>
		</div>
		';
	return $out;
}

function really_simple_share_get_options_stored () {
	//GET ARRAY OF STORED VALUES
	$option = get_option('really_simple_share');
	 
	if ($option===false) {
		//OPTION NOT IN DATABASE, SO WE INSERT DEFAULT VALUES
		$option = really_simple_share_get_options_default();
		add_option('really_simple_share', $option);
	} else if ($option=='above' or $option=='below') {
		// Versions below 1.2.0 compatibility
		$option = really_simple_share_get_options_default($option);
	} else if(!is_array($option)) {
		// Versions below 1.2.2 compatibility
		$option = json_decode($option, true);
	}
	
	// Versions below 1.4.1 compatibility
	if (!isset($option['facebook_like_text'])) {
		$option['facebook_like_text'] = 'like';
	}

	// Versions below 1.4.5 compatibility
	if (!isset($option['facebook_like_width'])) {
		$option['facebook_like_width'] = '100';
	}
	if (!isset($option['twitter_width'])) {
		$option['twitter_width'] = '110';
	}

	// Versions below 1.5.1 compatibility
	if (!isset($option['twitter_count'])) {
		$option['twitter_count'] = true;
	}

	// Versions below 1.6.1 compatibility
	if (!isset($option['google1_count'])) {
		$option['google1_count'] = true;
	}	
	if (!isset($option['google1_width'])) {
		$option['google1_width'] = '90';
	}
	
	// Versions below 1.6.3 compatibility
	if (!isset($option['layout'])) {
		$option['layout'] = 'button';
	}	
	
	// Versions below 1.8.0 compatibility
	if (!isset($option['disable_default_styles'])) {
		$option['disable_default_styles'] = false;
	}	

	// Versions below 1.8.1 compatibility
	if (!isset($option['linkedin_count'])) {
		$option['linkedin_count'] = true;
	}	
	return $option;
}

function really_simple_share_get_options_default ($position='above') {
	$option = array();
	$option['active_buttons'] = array('facebook'=>false, 'twitter'=>true, 'linkedin'=>false, 'buzz'=>false, 
		'digg'=>false, 'stumbleupon'=>false, 'facebook_like'=>true, 'hyves'=>false, 'email'=>false, 
		'reddit'=>false, 'google1'=>false, 'flattr'=>false, 'tipy'=>false);
	$option['position'] = $position;
	$option['show_in'] = array('posts'=>true, 'pages'=>true, 'home_page'=>true, 'tags'=>true, 'categories'=>true, 'dates'=>true, 'authors'=>true, 'search'=>true);
	$option['layout'] = 'button';
	$option['disable_default_styles'] = false;
	$option['facebook_like_text'] = 'like';
	$option['facebook_like_send'] = false;
	$option['facebook_like_width'] = '100';
	$option['flattr_uid'] = '';
	$option['google1_count'] = true;
	$option['google1_width'] = '90';
	$option['linkedin_count'] = true;
	$option['tipy_uid'] = '';
	$option['twitter_count'] = true;
	$option['twitter_text'] = '';
	$option['twitter_width'] = '110';
	return $option;
}
