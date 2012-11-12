<?php 
/*
Plugin Name: liveTV Team - 1 - General shortcode
Plugin URI: http://kwark.allwebtuts.net
Description: liveTV Team - General shortcode - require 0 activated - Activate this part to use some shortcodes manually to display some livestream in posts or pages.
Author: Laurent (KwarK) Bertrand
Version: 1.3.1.1
Author URI: http://kwark.allwebtuts.net
*/

/*  
	Copyright 2012  Laurent (KwarK) Bertrand  (email : kwark@allwebtuts.net)
	
	Please consider a small donation for my work. Behind each code, there is a geek who has to eat.
	Thank you for my futur bundle...pizza-cola. Bundle vs bundle, it's a good deal, no ? 
	Small pizza donation @ http://kwark.allwebtuts.net
	
	You can not remove these comments such as my informations.

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
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// disallow direct access to file
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
	wp_die(__('Sorry, but you cannot access this page directly.', 'livetv'));
}

// Hook Shortcode for Wordpress
add_shortcode('livestream','livetv_do_all_livestream_shortcode');


// Construct channel from Shortcode
function livetv_do_all_livestream_shortcode( $atts, $content = null )
{
	//First admin general options
	$width = get_option('livetv_width');
	$height = get_option('livetv_height');
	$visibility = get_option('livetv_visibility');
	$general_color = get_option('livetv_color');
	$autoplay = get_option('livetv_autoplay');
	$message = get_option('livetv_message');
	$registration = get_option('livetv_registration');
	if($general_color == 'dark'){$color = '#000000';}
	if($general_color == 'light'){$color = 'transparent';}
	if($general_color == 'transparent'){$color = 'transparent';}

	//Second, manual shortcode options
    extract( shortcode_atts( array(
	'width' => $width,
	'height' => $height,
	'visibility' => $visibility,
	'channel' => '',
	'type' => '',
	'autoplay' => $autoplay,
	'message' => $message,
	'registration' => $registration
	), $atts ) );

	if ($visibility == "members only" && is_user_logged_in() || $visibility == "public")
	{
		return do_livestream_only_from_type($type, $channel, $width, $height, $color, $autoplay);
	}
	else
	{
		return '<div class="livetv-info"><a href="'.esc_url($registration, array('http', 'https')).'">'.esc_html($message).'</a></div>';
	}		
}


function do_livestream_only_from_type($type, $channel, $width, $height, $color, $autoplay){
		
		switch($type):
		
			case 'own3d': //ok
				return '<iframe height="'.$height.'" width="'.$width.'" frameborder="0" src="http://www.own3d.tv/liveembed/'.$channel.'?autoPlay='.$autoplay.'"></iframe>';
			break;
			
			case 'twitch': //ok
				return '<object type="application/x-shockwave-flash" height="'.$height.'" width="'.$width.'" id="live_embed_player_flash" data="http://www.twitch.tv/widgets/live_embed_player.swf?channel='.$channel.'" bgcolor="'.$color.'"><param name="allowFullScreen" value="true" /><param name="allowScriptAccess" value="always" /><param name="allowNetworking" value="all" /><param name="movie" value="http://www.twitch.tv/widgets/live_embed_player.swf" /><param name="flashvars" value="hostname=www.twitch.tv&channel='.$channel.'&auto_play='.$autoplay.'&start_volume=25" /></object>';
			break;
			
			case 'justin': //ok
				return '<object type="application/x-shockwave-flash" height="'.$height.'" width="'.$width.'" id="live_embed_player_flash" data="http://www.justin.tv/widgets/live_embed_player.swf?channel='.$channel.' bgcolor="'.$color.'"><param name="allowFullScreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="allownetworking" value="all" /><param name="movie" value="http://www.justin.tv/widgets/live_embed_player.swf" /><param name="flashvars" value="channel='.$channel.'&auto_play='.$autoplay.'&start_volume=25" /></object>';
			break;
			
			case 'ustream': //ok
				return '<object width="'.$width.'" height="'.$height.'" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"><param name="flashvars" value="cid='.$channel.'&autoplay='.$autoplay.'"/><param name="allowfullscreen" value="true"/><param name="allowscriptaccess" value="always"/><param name="src" value="http://www.ustream.tv/flash/viewer.swf"/><param name="wmode" value="'.$color.'" /><embed flashvars="cid='.$channel.'&autoplay='.$autoplay.'" width="'.$width.'" height="'.$height.'" allowfullscreen="true" allowscriptaccess="always" src="http://www.ustream.tv/flash/viewer.swf" type="application/x-shockwave-flash"></embed></object>';
			break;
			
			case 'livestream': //ok
				return '<iframe src="http://cdn.livestream.com/embed/'.$channel.'?layout=4&autoPlay='.$autoplay.'&mute=false&allowchat=false&width='.$width.'&height='.$height.'" width="'.$width.'" height="'.$height.'" style="border:0;outline:0" frameborder="0" scrolling="no"></iframe>';
			break;
			
			default:
				return '<div id="nok" style="width:'.$width.'px;height:'.$height.'px;border:3px solid '.$color.'">Error</div>';
			break;
			
		endswitch;
}

if(is_admin())
{
	// Registers some default option on activation hook
	function livetv_shortcode_add_defaut_settings() {
		
		global $wpdb;
		$url = home_url('/wp-login.php');
			
		$settings = array(
			'livetv_width' => '660',
			'livetv_height' => '440',
			'livetv_color' => 'dark',
			'livetv_visibility' => 'members only',
			'livetv_message' => 'Please login to view our livestreams',
			'livetv_autoplay' => 'true',
			'livetv_registration' => ''.esc_url($url, array('http', 'https')).''
		);
	
		foreach ($settings as $key => $value) {
			update_option( ''.$key.'', ''.$value.'' );
		}
	}
	register_activation_hook(__FILE__, 'livetv_shortcode_add_defaut_settings');
	
	
	// Remove all settings on uninstall hook
	function livetv_shortcode_delete_defaut_settings() {
	
		global $wpdb;
		
		$settings = array(
			'livetv_width',
			'livetv_height',
			'livetv_color',
			'livetv_visibility',
			'livetv_message',
			'livetv_autoplay',
			'livetv_registration'
		);
	
		foreach ($settings as $value) {
			delete_option( ''.$value.'' );
		}	
	}
	register_uninstall_hook(__FILE__, 'livetv_shortcode_delete_defaut_settings');


include( $livetv_plugin_path . 'page-admin/page-admin-shortcode.php' );
}
?>