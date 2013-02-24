<?php
/*
Plugin Name: Googleplus, Facebook, Twitter Share Buttons
Plugin URI: http://www.tipsandtricks-hq.com
Description: This is a very simple and FAT FREE share button plugn that adds Google plus one, Facebook and Twitter share buttons to your blog posts. It loads the external JavaScript libraries asynchronously giving you a slight performance boost.
Version: 1.4
Author: Tips and Tricks HQ
Author URI: http://www.tipsandtricks-hq.com
*/
define('GFTS_PLUGIN_URL', plugins_url('',__FILE__));

function gfts_css_and_js_loader() 
{
	if (is_single()) //Load these in the single post view only
	{
		echo '<link rel="stylesheet" href="'.GFTS_PLUGIN_URL.'/gfts_css.css" type="text/css" media="screen" charset="utf-8" />';
		?>
		<div id="fb-root"></div>
		<script>(function(d, s) {
		  var js, fjs = d.getElementsByTagName(s)[0], load = function(url, id) {
		    if (d.getElementById(id)) {return;}
		    js = d.createElement(s); js.src = url; js.id = id;
		    fjs.parentNode.insertBefore(js, fjs);
		  };
		  load('//connect.facebook.net/en_US/all.js#xfbml=1', 'fbjssdk');
		  load('https://apis.google.com/js/plusone.js', 'gplus1js');
		  load('//platform.twitter.com/widgets.js', 'tweetjs');
		}(document, 'script'));
		</script>
		<?php
	}
}
add_action('wp_head', 'gfts_css_and_js_loader');

function gfts_apend_share_buttons_to_content($content) 
{
	if(is_single()) //Only apend these share buttons in the single post view
	{
		$content.= '
		<a name="gfts_share"></a>
		<div id="gfts_share_area">
		<ul id="gfts_share_buttons">
		<li><a class="twitter-share-button" data-count="none">Tweet</a></li>
		<li><g:plusone size="tall" annotation="none"></g:plusone></li>
		<li class="fb_like_button"><div class="fb-like" data-send="false" data-layout="button_count" data-width="55" data-show-faces="false"></div></li>	
		</ul>
		</div>';
		$content.= '<div class="gfts_clear"></div>';
	}
	return $content;
}
add_filter ('the_content', 'gfts_apend_share_buttons_to_content');