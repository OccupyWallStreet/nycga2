<?php
/*
Plugin Name: Tweet Blender
Plugin URI: http://www.tweetblender.com
Description: Provides several Twitter widgets: show your own tweets, show tweets relevant to post's tags, show tweets for Twitter lists, show tweets for hashtags, show tweets for keyword searches, show favorite tweets. Multiple widgets on the same page are supported. Can combine sources and blend all of them into a single stream.
Version: 3.3.15
Author: Kirill Novitchenko
Author URI: http://kirill-novitchenko.com
*/

/*  Copyright 2009-2011  Kirill Novitchenko  (email : knovitchenko@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// load localization file
load_plugin_textdomain('tweetblender', false, dirname(plugin_basename(__FILE__)) . "/lang/");

// if on PHP5, include oAuth library and config
if(!version_compare(PHP_VERSION, '5.0.0', '<'))
{
    class_exists('TwitterOAuth') || include_once dirname(__FILE__).'/lib/twitteroauth/twitteroauth.php';
	include_once dirname(__FILE__).'/lib/twitteroauth/config.php';
}

// include TweetBlender library
include_once(dirname(__FILE__).'/lib/lib.php');

// include Widgets
include_once(dirname(__FILE__).'/widget.php');
include_once(dirname(__FILE__).'/widget-tags.php');
include_once(dirname(__FILE__).'/widget-favorites.php');

// include admin tools
if (is_admin()) {
	include_once(dirname(__FILE__).'/admin-page.php');
}

// DB initialization
register_activation_hook(__FILE__,'tb_plugin_init');
function tb_plugin_init() {
	// install or upgrade database
	tb_db_install();
		
	// set defaults
	$tb_o = get_option("tweet-blender");
	if (!isset($tb_o['widget_show_photos'])) {
		$tb_o['widget_show_photos'] = true;
	}
	if (!isset($tb_o['widget_show_source'])) {
		$tb_o['widget_show_source'] = true;
	}
	if (!isset($tb_o['widget_show_header'])) {
		$tb_o['widget_show_header'] = true;
	}
	if (!isset($tb_o['general_link_screen_names'])) {
		$tb_o['general_link_screen_names'] = true;
	}
	if (!isset($tb_o['general_link_hash_tags'])) {
		$tb_o['general_link_hash_tags'] = true;
	}
	if (!isset($tb_o['general_link_urls'])) {
		$tb_o['general_link_urls'] = true;
	}
	if (!isset($tb_o['widget_check_sources'])) {
		$tb_o['widget_check_sources'] = true;
	}
	if (!isset($tb_o['widget_show_user'])) {
		$tb_o['widget_show_user'] = true;
	}
	update_option('tweet-blender',$tb_o);
}
function tb_db_install() {
	global $wpdb;
	
	$table_name = $wpdb->prefix . "tweetblender";
	$tb_o = get_option("tweet-blender");

	$tb_db_version = "5";	
	$installed_ver = $tb_o["db_version"];

	// if table is not already there - create it
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		$sql = "CREATE TABLE " . $table_name . " (
			  div_id VARCHAR(200) NOT NULL PRIMARY KEY,
			  source VARCHAR(100) NOT NULL,
			  tweet_text VARCHAR(255),
			  tweet_json TEXT NOT NULL,
			  created_at TIMESTAMP
		);";
		
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		
		$tb_o['db_version'] = $tb_db_version;
		update_option('tweet-blender',$tb_o);
	}
	// if table is there but has old structure
	elseif ($installed_ver != $tb_db_version) {

		$sql = "CREATE TABLE " . $table_name . " (
			  div_id VARCHAR(200) NOT NULL PRIMARY KEY,
			  source VARCHAR(255) NOT NULL,
			  tweet_text VARCHAR(255),
			  tweet_json TEXT NOT NULL,
			  created_at TIMESTAMP
		);";
	
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);

		$tb_o['db_version'] = $tb_db_version;
		update_option('tweet-blender',$tb_o);
	}
}
// remove old DB cache entries
function tb_db_cache_clear($where_sql = ''){
	global $wpdb;
	$table_name = $wpdb->prefix . "tweetblender";

	// delete tweets that are older than predefined period
	$wpdb->query("DELETE FROM $table_name $where_sql");
}


// generate config
add_action('wp_head', 'tb_add_header_config', 1);
function tb_add_header_config() {

	$tb_o = get_option('tweet-blender');
	
	$settings = array();
	if(is_array($tb_o)) {
		
		// remove options not used by widget/archive
		unset($tb_o['archive_page_id']);
		unset($tb_o['advanced_reroute_type']);
		unset($tb_o['db_version']);
		
		// urlencode bad words
		if (isset($tb_o['filter_bad_strings'])) {
			$tb_o['filter_bad_strings'] = base64_encode($tb_o['filter_bad_strings']);
		}
				
		foreach($tb_o as $opt => $val) {
			// skip oAuth
			if (strpos($opt,"oauth_") === 0) {
				continue;
			}
			
			if ($val == 'on') {
				$settings[] = "'$opt':true";
			}
			elseif ($val == '') {
				$settings[] = "'$opt':false";
			}
			else {
				$settings[] = "'$opt':'$val'";
			}
		}
	}
	
	// add default view more link URL
	if (isset($tb_o['archive_is_disabled']) && (!$tb_o['archive_is_disabled'] && ($archive_post_id = tb_get_archive_post_id()) != null)) {
		$settings[] = "'default_view_more_url':'" . get_permalink($archive_post_id) . "'";
	}

	$js = "\nvar TB_pluginPath = '" . plugins_url('/tweet-blender') . "';\n";
	if (sizeof($settings) > 0) {
		$js .= "var TB_config = {\n" . join(",\n",$settings) . "\n}";
	}
	echo tb_wrap_javascript($js);
}

// register stylesheet
add_action('wp_head', 'tb_add_header_css', 100);
function tb_add_header_css() {
	echo '<link type="text/css" media="screen" rel="stylesheet" href="' . plugins_url('tweet-blender/css/tweets.css') . '" />' . "\n";
}

// add javascript with dependency on jQuery to public pages only
add_action("template_redirect","tb_load_js");
function tb_load_js() {

	global $js_labels;
	
	$dependencies = array('jquery');	
	$tb_o = get_option('tweet-blender');
	// load PHPDate only if have a custom date
	if (isset($tb_o['general_timestamp_format']) && ($tb_o['general_timestamp_format'] != '')) {
		wp_enqueue_script('phpdate', '/' . PLUGINDIR . '/tweet-blender/js/jquery.phpdate.js', array('jquery'), false, true);
		$dependencies[] = 'phpdate';
	}
	// load JSON plugin only if caching is enabled
	if (isset($tb_o['advanced_disable_cache']) && ($tb_o['advanced_disable_cache'] != 'on')) {
		wp_enqueue_script('tojson', '/' . PLUGINDIR . '/tweet-blender/js/jquery.json-2.2.min.js', array('jquery'), false, true);
		$dependencies[] = 'tojson';
	}
	
	// load jsonp plugin with good error hanlding
	wp_enqueue_script('jsonp', '/' . PLUGINDIR . '/tweet-blender/js/jquery.jsonp-2.1.4.min.js', array('jquery'), false, true);
	
	// load lib
	wp_enqueue_script('tb-lib', '/' . PLUGINDIR . '/tweet-blender/js/lib.js',array('jquery'), false, true);
    wp_localize_script('tb-lib', 'TB_labels', $js_labels);
	$dependencies[] = 'tb-lib';
	
	// load main JS code
	wp_enqueue_script('tb-main', '/' . PLUGINDIR . '/tweet-blender/js/main.js', $dependencies, false, true);
}

// hookup filter to add tweet list to the content of archive page
add_filter('the_content', 'tb_add_archive_page_content');
function tb_add_archive_page_content($content = '') {
	global $post;
	
	// do nothing if archive page is disabled
	$tb_o = get_option('tweet-blender');
	if (isset($tb_o['archive_is_disabled']) && $tb_o['archive_is_disabled']) {
		return $content;	
	}
	else {
		// work with pages only, ignore blog posts
		if ($post->post_type != 'page') {
			return $content;
		}
		
		// if looking at archive page, apend list of tweets to content
		if ($post->ID == tb_get_archive_post_id()) {
			$archive_html = '<div id="tweetblender-archive">';
			$archive_html .= tb_get_cached_tweets_html('archive',null);
			$archive_html .= '</div>';

			// JavaScript code for mouseovers
			$archive_html .= tb_wrap_javascript("
				TB_mode = 'archive';
				jQuery.each(jQuery('#tweetblender-archive').children('div'),function(i,obj){ TB_wireMouseOver(obj.id); });
			");
		
			return $content . $archive_html;
		}
		// else, do nothing
		else {
			return $content;
		}
	}
}

// template tag for general widget
function tweet_blender_widget($options) {

	echo '<div id="'. $options['unique_div_id'] . '" class="widget widget_tweetblender">';
	// if required parameters not provided output HTML comment with usage instructions
	if (!isset($options['unique_div_id']) || !isset($options['sources'])) {
		echo "The 'unique_div_id' and 'sources' are required parameters when using tweet_blender_widget() template tag for Tweet Blender plugin. The code should look as follows:
		
		<pre>tweet_blender_widget(array(
	'unique_div_id' => 'tweetblender-t1',
	'sources' => '@tweetblender,#tweetblender,twitter',
	'refresh_rate'=> 60,
	'tweets_num' => 5,
	'view_more_url' => 'http://twitter.com/tweetblender',
	'view_more_text' => 'follow us!'
));</pre>";	
	}
	// else create widget HTML
	else {
		$tb = new TweetBlender();
		$tb->id = $options['unique_div_id'];
		$tb->widget(array(),array(
			'widget_sources' => $options['sources'],
			'widget_refresh_rate' => $options['refresh_rate'],
			'widget_tweets_num' => $options['tweets_num'],
			'widget_view_more_url' => $options['view_more_url'],
			'widget_view_more_text' => $options['view_more_text']
		));
	}
	echo '</div>';		
}

// template tag for general tags widget
function tweet_blender_widget_for_tags($options) {

	echo '<div id="'. $options['unique_div_id'] . '" class="widget widget_tweetblender">';
	// if required parameters not provided output HTML comment with usage instructions
	if (!isset($options['unique_div_id'])) {
		echo "The 'unique_div_id' is a required parameter when using tweet_blender_widget_for_tags() template tag for Tweet Blender plugin. The code should look as follows:
		
		<pre>tweet_blender_widget_for_tags(array(
	'unique_div_id' => 'tweetblender-t1',
	'refresh_rate'=> 60,
	'tweets_num' => 5
));</pre>";	
	}
	// else create widget HTML
	else {
		$tb = new TweetBlenderForTags();
		$tb->id = $options['unique_div_id'];
		$tb->widget(array(),array(
			'widget_refresh_rate' => $options['refresh_rate'],
			'widget_tweets_num' => $options['tweets_num'],
		));
	}
	echo '</div>';
}

function tb_create_markup($mode = 'widget',$instance,$widget_id,$tb_o) {
	$html = '';
	if (isset($tb_o['widget_show_header']) && $tb_o['widget_show_header']) {
		$html .= tb_create_markup_header($widget_id);
	}
	if (isset($tb_o['general_seo_tweets_googleoff']) && $tb_o['general_seo_tweets_googleoff']) {
		$html .= '<!--googleoff: index--><div class="tb_tweetlist">' . tb_get_cached_tweets_html($mode,$instance,$widget_id) . '</div><!--googleon: index-->';
	}
	else {
		$html .= '<div class="tb_tweetlist">' . tb_get_cached_tweets_html($mode,$instance) . '</div>';
	}
	return $html;
}

function tb_create_markup_header($widget_id) {
	$html = '<div class="tb_header">';
	$html .= '<img class="tb_twitterlogo" src="' . plugins_url('tweet-blender/img/twitter-logo.png') . '" alt="Twitter Logo" />';
	$html .= '<div class="tb_tools" style="background-image:url(' . plugins_url('tweet-blender/img/bg_sm.png') . ')">';
	$html .= '<a class="tb_infolink" href="http://kirill-novitchenko.com" title="Tweet Blender by Kirill Novitchenko" style="background-image:url(' . plugins_url('tweet-blender/img/info-kino.png') . ')"> </a>';
	$html .= '<a class="tb_refreshlink" href="javascript:TB_blend(\'' . $widget_id . '\');" title="Refresh Tweets"><img src="' . plugins_url('tweet-blender/img/ajax-refresh-icon.gif') . '" alt="Refresh" /></a></div></div>';
	return $html;	
}

// for backward compatibility
function tb_archive($sources = '') {
	echo '';
	return;
}

// for backward compatibility
function tb_widget($args = array()) {
	echo '';
	return;
}

?>
