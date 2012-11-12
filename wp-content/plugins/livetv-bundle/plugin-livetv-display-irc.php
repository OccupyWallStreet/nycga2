<?php 
/*
Plugin Name: liveTV Team - 3 - IRC chat
Plugin URI: http://kwark.allwebtuts.net
Description: liveTV Team - Display chat IRC - require 0 activated - Create quakenet irc channel automatically under each livestream (for this usage 0 + 1 + 2 must be activated). Create quakenet irc in some posts or pages with shortcode (for this usage 0 must be activated).
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

$GLOBALS['livetv_plugin_irc_activate'] = '1';

add_shortcode( 'liveTVChat' , 'livetv_do_irc' );

function livetv_do_irc( $atts, $content = null )
{
	//First admin general options
	$width = get_option('livetv_width');
	$height = get_option('livetv_height');
	$visibility = get_option('livetv_visibility');
	$message = get_option('livetv_message');
	$registration = get_option('livetv_registration');
	
		//Second, manual shortcode options with admin default value if nothing is defined by the author of the current shortcode
    	extract( shortcode_atts( array(
		'width' => $width,
		'height' => $height,
		'visibility' => $visibility,
		'type' => '',
		'channel' => '',
		'message' => $message,
		'registration' => $registration
      	), $atts ) );
		
		
		switch($type):
			
			//Quakenet
			case 'quakenet':
				$livetv_good_irc = '<iframe style="border:none;" src="http://webchat.quakenet.org/?channels='.$channel.'&uio=d4" width="'.$width.'" height="'.$height.'"></iframe>';
			break;
			
			//own3d
			case 'own3d':
				$livetv_good_irc = '<iframe height="'.$height.'" width="'.$width.'" scrolling="no" frameborder="0" src="http://www.own3d.tv/chatembed/'.$channel.'"></iframe>';
			break;
			
			//Twitch
			case 'twitch':
				$livetv_good_irc = '<iframe frameborder="0" scrolling="no" id="chat_embed" src="http://twitch.tv/chat/embed?channel='.$channel.'&popout_chat=true" height="'.$height.'" width="'.$width.'"></iframe>';
			break;
			//Justin
			case 'justin':
				$livetv_good_irc = '<iframe frameborder="0" scrolling="no" id="chat_embed" src="http://twitch.tv/chat/embed?channel='.$channel.'&popout_chat=true" height="'.$height.'" width="'.$width.'"></iframe>';
			break;
			
			//Quakenet by default
			default:
				$livetv_good_irc = '<iframe style="border:none;" src="http://webchat.quakenet.org/?channels='.$channel.'&uio=d4" width="'.$width.'" height="'.$height.'"></iframe>';
			break;
			
		endswitch;
		
	if ($visibility == "members only" && is_user_logged_in() || $visibility == "public")
	{
		return '<div class="livetv-irc-wrap" style="height:'.$height.'px !important;width:'.$width.'px !important;"><div class="livetv-irc-content" style="width:'.$width.'px;height:'.$height.'px !important;">'.$livetv_good_irc.'</div></div>';
	}
	else
	{
		return '<div class="livetv-info"><a href="'.esc_url($registration, array('http', 'https')).'">'.esc_html($message).'</a></div>';
	}
}
?>