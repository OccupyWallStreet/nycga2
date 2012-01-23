<?php
/*
Plugin Name: Really simple Facebook Twitter share buttons
Plugin URI: http://www.whiletrue.it
Description: Puts Facebook, Twitter, LinkedIn and other share buttons of your choice above or below your posts.
Author: WhileTrue
Version: 2.2
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
		wp_enqueue_script('jquery-ui-sortable');
		return;
	}

	// GET ARRAY OF STORED VALUES
	$option = really_simple_share_get_options_stored();

	if ($option['active_buttons']['facebook']) {
		wp_enqueue_script('really_simple_share_facebook', 'http://static.ak.fbcdn.net/connect.php/js/FB.Share', array(), false, $option['scripts_at_bottom']);
	}
	if ($option['active_buttons']['linkedin']) {
		wp_enqueue_script('really_simple_share_linkedin', 'http://platform.linkedin.com/in.js', array(), false, $option['scripts_at_bottom']);
	}
	
	if ($option['active_buttons']['buzz']) {
		wp_enqueue_script('really_simple_share_buzz', 'http://www.google.com/buzz/api/button.js', array(), false, $option['scripts_at_bottom']);
	}
	if ($option['active_buttons']['google1']) {
		wp_enqueue_script('really_simple_share_google1', 'http://apis.google.com/js/plusone.js', array(), false, $option['scripts_at_bottom']);
	}
	if ($option['active_buttons']['flattr']) {
		wp_enqueue_script('really_simple_share_flattr', 'http://api.flattr.com/js/0.6/load.js?mode=auto&#038;ver=0.6', array(), false, $option['scripts_at_bottom']);
	}
	if ($option['active_buttons']['twitter']) {
		wp_enqueue_script('really_simple_share_twitter', 'http://platform.twitter.com/widgets.js', array(), false, $option['scripts_at_bottom']);
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


function really_simple_share ($content, $filter, $link='', $title='', $author='') {
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
	
	// IF LINK AND TITLE ARE NOT SET, USE DEFAULT FUNCTIONS
	if ($link=='' and $title=='') {
		$link = ($option['use_shortlink']) ? wp_get_shortlink() : get_permalink();
		$title = get_the_title();
		$author = get_the_author_meta('nickname');
	}
	

	// PREPEND ABOVE TEXT
	if ($option['prepend_above']!='') {
		$out .= '<div style="height:33px;" class="really_simple_share_prepend_above robots-nocontent snap_nopreview">'.stripslashes($option['prepend_above']).'</div>';
	}

	$height = ($option['layout']=='button') ? 33 : 66;
	$out .= '<div style="height:'.$height.'px;" class="really_simple_share robots-nocontent snap_nopreview">';

	// PREPEND INLINE TEXT
	if ($option['prepend_inline']!='') {
		$out .= '<div class="really_simple_share_prepend_inline">'.stripslashes($option['prepend_inline']).'</div>';
	}

	foreach (explode(',',$option['sort']) as $name) {
		if (!$option['active_buttons'][$name]) {
			continue;
		}
		
		// OPEN THE BUTTON DIV
		$out .= '<div class="really_simple_share_'.$name.'" style="width:'.$option['width_buttons'][$name].'px;">';
		
		if ($name == 'facebook') {
		
			$option_layout = ($option['layout']=='button') ? 'button_count' : 'box_count';
			// REMOVE HTTP:// FROM STRING
			$facebook_link = (substr($link,0,7)=='http://') ? substr($link,7) : $link;
			$out .= '
					<a name="fb_share" type="'.$option_layout.'" href="http://www.facebook.com/sharer.php" share_url="'.$facebook_link.'">Share</a> 
				';
		}
		else if ($name == 'facebook_like') {
			$option_layout = ($option['layout']=='button') ? 'button_count' : 'box_count';
			$option_height = ($option['layout']=='button') ? 27 : 60;
			// OPTION facebook_like_text FILTERING
			$option_facebook_like_text = ($option['facebook_like_text']=='recommend') ? 'recommend' : 'like';
			$out .= '
				<iframe src="http://www.facebook.com/plugins/like.php?href='.urlencode($link).'&amp;layout='.$option_layout.'&amp;show_faces=false&amp;width='.$option['facebook_like_width'].'&amp;action='.$option_facebook_like_text.'&amp;colorscheme=light&amp;send=false&amp;height='.$option_height.'" 
						scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:'.$option['facebook_like_width'].'px; height:'.$option_height.'px;" allowTransparency="true"></iframe>
				';
			// FACEBOOK LIKE SEND BUTTON CURRENTLY IN FBML MODE - WILL BE MERGED IN THE LIKE BUTTON WHEN FACEBOOK RELEASES IT	
			if ($option['facebook_like_send']) {
				static $facebook_like_send_script_inserted = false;
				if (!$facebook_like_send_script_inserted) {
					$out .= '<script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>';
					$facebook_like_send_script_inserted = true;
				}
				$out .= '</div>
					<div style="float:left; width:50px; padding-left:10px;" class="really_simple_share_facebook_like_send">
					<fb:send href="'.$link.'" font=""></fb:send>
					';
			}	
		}
		else if ($name == 'linkedin') {
			$option_layout = ($option['layout']=='button') ? 'data-counter="right"' : 'data-counter="top"';
			$option_layout = ($option['linkedin_count']) ? $option_layout : '';
			$out .= '
					<script type="IN/Share" '.$option_layout.' data-url="'.$link.'"></script>
				';
		}
		else if ($name == 'buzz') {
			$option_layout = ($option['layout']=='button') ? 'small-count' : 'normal-count';
			$out .= '
					<a title="Post to Google Buzz" class="google-buzz-button" href="http://www.google.com/buzz/post" data-button-style="'.$option_layout.'" 
						data-url="'.$link.'"></a>
				';
		}
		else if ($name == 'digg') {
			$option_layout = ($option['layout']=='button') ? 'DiggCompact' : 'DiggMedium';
			// THE DIGG JS FILE DOES NOT ALWAYS WORK INSIDE THE <HEAD> SECTION, WE KEEP IT HERE
			$out .= '
					<script type="text/javascript" src="http://widgets.digg.com/buttons.js"></script>
					<a class="DiggThisButton '.$option_layout.'" href="http://digg.com/submit?url='.$link.'&amp;title='.htmlentities($title).'"></a>	
				';
		}
		else if ($name == 'stumbleupon') {
			$option_layout = ($option['layout']=='button') ? '1' : '5';
			$out .= '
					<script type="text/javascript" src="http://www.stumbleupon.com/hostedbadge.php?s='.$option_layout.'&amp;r='.$link.'"></script>
				';
		}	
		else if ($name == 'hyves') {
			$out .= ' 
					<iframe src="http://www.hyves.nl/respect/button?url='.$link.'" 
						style="border: medium none; overflow:hidden; width:150px; height:21px;" scrolling="no" 
						frameborder="0" allowTransparency="true" ></iframe>
				';
		}		
		else if ($name == 'reddit') {
			$option_layout = ($option['layout']=='button') ? '1' : '3';
			$out .= '
					<script type="text/javascript" src="http://www.reddit.com/static/button/button'.$option_layout.'.js?newwindow=1&amp;url='.$link.'"></script>
				';
		}	
		else if ($name == 'email') {
			$out .= '
					<a href="mailto:?subject='.$title.'&amp;body='.$title.' - '.$link.'"><img src="'.WP_PLUGIN_URL.'/really-simple-facebook-twitter-share-buttons/email.png" alt="Email" title="Email" /> '.stripslashes($option['email_label']).'</a> 
				';
		}
		else if ($name == 'google1') {
			$option_layout = ($option['layout']=='button') ? 'medium' : 'tall';
			$data_count = ($option['google1_count']) ? '' : 'count="false"';
			$out .= '
					<g:plusone size="'.$option_layout.'" href="'.$link.'" '.$data_count.'></g:plusone>
				';
		}
		else if ($name == 'flattr') {
			$language = 'en_GB';
			$option_layout = ($option['layout']=='button') ? 'button:compact' : '';
			$out .= '
					<a class="FlattrButton" style="display:none;" href="'.$link.'" title="'.strip_tags($title).'" rev="flattr;uid:'.$option['flattr_uid'].';language:'.$language.';category:text;tags:'.strip_tags(get_the_tag_list('', ',', '')).';'.$option_layout.';">'.$title.'</a>
				';
		}
		else if ($name == 'tipy') {
			$option_layout = ($option['layout']=='button') ? 'tipy_button_compact' : 'tipy_button';
			$option_image  = ($option['layout']=='button') ? 'button_compact' : 'button';
			$out .= '
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
				';
		}
		else if ($name == 'twitter') {
			$option_layout = ($option['layout']=='button') ? 'horizontal' : 'vertical';
			$data_count = ($option['twitter_count']) ? $option_layout : 'none';
			$twitter_author = ($option['twitter_author']) ? ' data-related="'.stripslashes($author).':The author of this post" ' : '';
			$out .= '
					<a href="http://twitter.com/share" class="twitter-share-button" data-count="'.$data_count.'" 
						data-text="'.strip_tags($title).stripslashes($option['twitter_text']).'" data-url="'.$link.'" 
						data-via="'.stripslashes($option['twitter_via']).'" '.$twitter_author.'></a> 
				';
		}
		
		// CLOSE THE BUTTON DIV
		$out .= '</div>';
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
			$option['width_buttons'][$item]  = esc_html($_POST['really_simple_share_width_'.$item]);
		}
		foreach (array_keys($show_in) as $item) {
			$option['show_in'][$item] = (isset($_POST['really_simple_share_show_'.$item]) and $_POST['really_simple_share_show_'.$item]=='on') ? true : false;
		}
		$option['sort'] = esc_html($_POST['really_simple_share_sort']);
		$option['position'] = esc_html($_POST['really_simple_share_position']);
		$option['layout'] = esc_html($_POST['really_simple_share_layout']);
		$option['prepend_above']  = esc_html($_POST['really_simple_share_prepend_above']);
		$option['prepend_inline'] = esc_html($_POST['really_simple_share_prepend_inline']);
		$option['disable_default_styles'] = (isset($_POST['really_simple_share_disable_default_styles']) and $_POST['really_simple_share_disable_default_styles']=='on') ? true : false;
		$option['use_shortlink'] = (isset($_POST['really_simple_share_use_shortlink']) and $_POST['really_simple_share_use_shortlink']=='on') ? true : false;
		$option['scripts_at_bottom'] = (isset($_POST['really_simple_share_scripts_at_bottom']) and $_POST['really_simple_share_scripts_at_bottom']=='on') ? true : false;
		$option['facebook_like_text'] = ($_POST['really_simple_share_facebook_like_text']=='recommend') ? 'recommend' : 'like';
		$option['facebook_like_send'] = (isset($_POST['really_simple_share_facebook_like_send']) and $_POST['really_simple_share_facebook_like_send']=='on') ? true : false;
		$option['email_label'] = esc_html($_POST['really_simple_share_email_label']);
		$option['flattr_uid'] = esc_html($_POST['really_simple_share_flattr_uid']);
		$option['google1_count'] = (isset($_POST['really_simple_share_google1_count']) and $_POST['really_simple_share_google1_count']=='on') ? true : false;
		$option['linkedin_count'] = (isset($_POST['really_simple_share_linkedin_count']) and $_POST['really_simple_share_linkedin_count']=='on') ? true : false;
		$option['tipy_uid'] = esc_html($_POST['really_simple_share_tipy_uid']);
		$option['twitter_count'] = (isset($_POST['really_simple_share_twitter_count']) and $_POST['really_simple_share_twitter_count']=='on') ? true : false;
		$option['twitter_text'] = esc_html($_POST['really_simple_share_twitter_text']);
		$option['twitter_author'] = (isset($_POST['really_simple_share_twitter_author']) and $_POST['really_simple_share_twitter_author']=='on') ? true : false;
		$option['twitter_via'] = esc_html($_POST['really_simple_share_twitter_via']);
		
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
	$use_shortlink = ($option['use_shortlink']) ? 'checked="checked"' : '';
	$scripts_at_bottom = ($option['scripts_at_bottom']) ? 'checked="checked"' : '';
	$facebook_like_show_send_button = ($option['facebook_like_send']) ? 'checked="checked"' : '';
	$google1_count = ($option['google1_count']) ? 'checked="checked"' : '';
	$linkedin_count = ($option['linkedin_count']) ? 'checked="checked"' : '';
	$twitter_count = ($option['twitter_count']) ? 'checked="checked"' : '';
	$twitter_author = ($option['twitter_author']) ? 'checked="checked"' : '';
	
	// SETTINGS FORM

	$out .= '
	<style>
		#really_simple_share_form h3 { cursor: default; }
		#really_simple_share_form td { vertical-align:top; padding-bottom:15px; }
		#sortable { list-style-type: none; margin: 0; padding: 0; width:300px; }
		#sortable li { margin: 3px; padding: 0.5em 0.8em 0.5em 1.5em; height: 22px; cursor:pointer; border:1px solid gray;}
	</style>
	<script>
	jQuery(function() {
		var really_simple_sort = jQuery( "#sortable" ).sortable({ axis: "y",
			update:function(e,ui) {
				var order = really_simple_sort.sortable("toArray").join();
				jQuery("#really_simple_share_sort").val(order);
			}
		});
	});
	</script>

	
	<div class="wrap">
	<h2>'.__( 'Really simple Facebook and Twitter share buttons', 'menu-test' ).'</h2>
	<div id="poststuff" style="padding-top:10px; position:relative;">

	<div style="float:left; width:74%; padding-right:1%;">

		<form id="really_simple_share_form" name="form1" method="post" action="">

		<div class="postbox">
		<h3>'.__("General options", 'menu-test' ).'</h3>
		<div class="inside">
			<table>
			<tr><td style="width:130px;">'.__("Share buttons", 'menu-test' ).':<br /><br />
				<span class="description">'.__("Check to activate, Drag&Drop to sort, Adjust width in pixels", 'menu-test' ).'</span>
			</td>
			<td>';
		
			$out .= '<ul id="sortable">';
			
			foreach (explode(',',$option['sort']) as $name) {
				$checked = ($option['active_buttons'][$name]) ? 'checked="checked"' : '';
				$out .= '<li class="ui-state-default" id="'.$name.'">
						<input type="checkbox" name="really_simple_share_active_'.$name.'" '.$checked.' /> '	
						. __($active_buttons[$name], 'menu-test' ).'
						<input type="text" name="really_simple_share_width_'.$name.'" value="'.stripslashes($option['width_buttons'][$name]).'" size="5" style="float:right;" />	
					</li>';
			}

			$out .= '</ul>
				<input type="hidden" id="really_simple_share_sort" name="really_simple_share_sort" value="'.stripslashes($option['sort']).'" />
				';


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
			<tr><td>'.__("Prepend text on the above line", 'menu-test' ).':</td>
			<td><input type="text" name="really_simple_share_prepend_above" value="'.stripslashes($option['prepend_above']).'" size="50" /><br />
				<span class="description">'.__("Optional text shown above the buttons, e.g. 'If you liked this post, say thanks by sharing it:'", 'menu-test' ).'</span>
			</td></tr>
			<tr><td>'.__("Prepend text inline", 'menu-test' ).':</td>
			<td><input type="text" name="really_simple_share_prepend_inline" value="'.stripslashes($option['prepend_inline']).'" size="25" /><br />
				<span class="description">'.__("Optional text shown inline before the buttons, e.g. 'Share this:'", 'menu-test' ).'</span>
			</td></tr>
			<tr><td>'.__("Disable default styles", 'menu-test' ).':</td>
			<td><input type="checkbox" name="really_simple_share_disable_default_styles" '.$disable_default_styles.' />
			</td></tr>
			<tr><td>'.__("Load scripts at the bottom of the body", 'menu-test' ).':</td>
			<td><input type="checkbox" name="really_simple_share_scripts_at_bottom" '.$scripts_at_bottom.' />
				<span class="description">'.__("Checking it should increase the page loading speed. Warning: this requires the theme to have the wp_footer() hook in the appropriate place; if unsure, leave it unchecked", 'menu-test' ).'</span>
			</td></tr>
			<tr><td>'.__("Use Wordpress shortlink instead of permalink", 'menu-test' ).':</td>
			<td><input type="checkbox" name="really_simple_share_use_shortlink" '.$use_shortlink.' />
				<span class="description">'.__("Warning: changing the link format may reset the button counters; if unsure, leave it unchecked", 'menu-test' ).'</span>
			</td></tr>
			</table>
		</div>
		</div>'
		.really_simple_share_box_content('Facebook Like button options', 
			array(
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
		.really_simple_share_box_content('Email button options', 
			array('Email label'=>'
					<input type="text" name="really_simple_share_email_label" value="'.stripslashes($option['email_label']).'" size="25" /><br />
					<span class="description">'.__("This optional text is added next to the email button, e.g. 'forward to a friend'", 'menu-test' ).'</span>
				'
			)
		)
		.really_simple_share_box_content('Flattr button options', 
			array('Flattr UID'=>'
					<input type="text" name="really_simple_share_flattr_uid" value="'.stripslashes($option['flattr_uid']).'" size="10" /><br />
					<span class="description">'.__("This field is mandatory if you want to use the Flattr button", 'menu-test' ).'</span>
				'
			)
		)
		.really_simple_share_box_content('Google +1 button options', 
			array(
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
					<input type="text" name="really_simple_share_tipy_uid" value="'.stripslashes($option['tipy_uid']).'" size="10" /><br />
					<span class="description">'.__("This numeric field is mandatory if you want to use the Tipy button", 'menu-test' ).'</span>
				'
			)
		)
		.really_simple_share_box_content('Twitter button options', 
			array(
				'Additional text'=>'
					<input type="text" name="really_simple_share_twitter_text" value="'.stripslashes($option['twitter_text']).'" size="25" /><br />
					<span class="description">'.__("Optional text added at the end of every tweet, e.g. ' (via @authorofblogentry)'.
					If you use it, insert an initial space or puntuation mark", 'menu-test' ).'</span>
				',
				'Add author to follow list'=>'
					<input type="checkbox" name="really_simple_share_twitter_author" '.$twitter_author.' />
					<span class="description">'.__("If checked, the nickname of the author of the post is always added to the follow list.", 'menu-test' ).'</span>
				',
				'Via this user'=>'
					<input type="text" name="really_simple_share_twitter_via" value="'.stripslashes($option['twitter_via']).'" size="25" /><br />
					<span class="description">'.__("Optional user to follow after tweeting", 'menu-test' ).'</span>
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
	 
	if(!is_array($option)) {
		$option = array();
	} else if (!isset($option['sort'])) {
		// Versions below 2.0 compatibility
		$option['width_buttons']['facebook_like'] = $option['facebook_like_width'];
		$option['width_buttons']['google1'] = $option['google1_width'];
		$option['width_buttons']['twitter'] = $option['twitter_width'];
	}	
	
	// MERGE DEFAULT AND STORED OPTIONS
	$option = array_merge(really_simple_share_get_options_default(), $option);
	
	return $option;
}

function really_simple_share_get_options_default ($position='above') {
	$option = array();
	$option['active_buttons'] = array('facebook'=>false, 'twitter'=>true, 'linkedin'=>false, 'buzz'=>false, 
		'digg'=>false, 'stumbleupon'=>false, 'facebook_like'=>true, 'hyves'=>false, 'email'=>false, 
		'reddit'=>false, 'google1'=>false, 'flattr'=>false, 'tipy'=>false);
	$option['width_buttons'] = array('facebook'=>'100', 'twitter'=>'110', 'linkedin'=>'100', 'buzz'=>'100', 
		'digg'=>'100', 'stumbleupon'=>'100', 'facebook_like'=>'100', 'hyves'=>'100', 'email'=>'100', 
		'reddit'=>'100', 'google1'=>'90', 'flattr'=>'120', 'tipy'=>'100');
	$option['sort'] = implode(',',array('facebook_like', 'google1', 'linkedin', 'buzz', 'digg', 'stumbleupon', 'hyves', 'email', 
		'reddit', 'flattr', 'tipy', 'facebook', 'twitter'));
	$option['position'] = $position;
	$option['show_in'] = array('posts'=>true, 'pages'=>true, 'home_page'=>true, 'tags'=>true, 'categories'=>true, 'dates'=>true, 'authors'=>true, 'search'=>true);
	$option['layout'] = 'button';
	$option['prepend_above']  = '';
	$option['prepend_inline'] = '';
	$option['disable_default_styles'] = false;
	$option['use_shortlink'] = false;
	$option['scripts_at_bottom'] = false;

	$option['facebook_like_text'] = 'like';
	$option['facebook_like_send'] = false;
	$option['flattr_uid'] = '';
	$option['google1_count'] = true;
	$option['email_label'] = '';
	$option['linkedin_count'] = true;
	$option['tipy_uid'] = '';
	$option['twitter_count'] = true;
	$option['twitter_text'] = '';
	$option['twitter_author'] = false;
	$option['twitter_via'] = '';
	return $option;
}
