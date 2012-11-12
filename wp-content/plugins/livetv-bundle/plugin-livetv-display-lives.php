<?php 
/*
Plugin Name: liveTV Team - 2 - Display lives
Plugin URI: http://kwark.allwebtuts.net
Description: liveTV Team - Display all livestreams in a page - Require 0 + 1 activated - Activate this part if you need to display loop of thumbnails with two modes: normal view and large view. Require 3 activated to have the IRC chat zone under each live stream.
Author: Laurent (KwarK) Bertrand
Version: 1.3.1.1
Author URI: http://kwark.allwebtuts.net
*/

/*  
	Copyright 2012  Laurent (KwarK) Bertrand  (email : kwark@allwebtuts.net)
	
	Please consider a small donation for my work. Behind each code, there is a geek who has to eat.
	Thank you for my futur bundle...pizza-cola. Bundle vs bundle, it's a good deal, no ? 
	Small pizza donation @ http://kwark.allwebtuts.net
	
	You can not remove comments such as my informations.

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

global $livetv_slide, $livetv_color, $livetv_qtip;

$livetv_qtip = get_option('livetv_qtip');
$livetv_color = get_option('livetv_color');
$livetv_slide = get_option('livetv_effect');

$plugurl = '' . WP_PLUGIN_URL . '';

//Main jquery
wp_register_script('livetv-beautiful-effect', $plugurl . '/livetv-bundle/js/frontend.js', array('jquery'));
wp_register_script('livetv-beautiful-qtip',  $plugurl . '/livetv-bundle/js/jquery.qtip-1.0.0-rc3.min.js', array('jquery'));
wp_register_script('livetv-color-picker',  $plugurl . '/livetv-bundle/js/jscolor.js');

//Slider effect
wp_register_script('livetv-effect-'.$livetv_slide.'', $plugurl . '/livetv-bundle/js/switcher-'.$livetv_slide.'.js', array('livetv-beautiful-effect'));

//Qtip bubble dialog
wp_register_script('livetv-effect-qtype', $plugurl . '/livetv-bundle/js/jquery.qtip.min.js', array('jquery'));
wp_register_script('livetv-qtip-'.$livetv_qtip.'', $plugurl . '/livetv-bundle/js/livetv-qtip-'.$livetv_qtip.'.js', array('livetv-effect-qtype'));

//Draggable
wp_register_script('livetv-effect-draggable', $plugurl . '/livetv-bundle/js/switcher-draggable.js', array('jquery-ui-core', 'jquery-ui-draggable'));

//Pagination
wp_register_script('livetv-pagination', $plugurl . '/livetv-bundle/js/jPages.min.js', array('jquery'));

//Css
wp_register_style('livetv-page', $plugurl . '/livetv-bundle/css/page-livetreams.css');
wp_register_style('livetv-hook', $plugurl . '/livetv-bundle/css/page-livetreams-hook.css');
wp_register_style('livetv-3col', $plugurl . '/livetv-bundle/css/page-livetreams-3col.css');
wp_register_style('livetv-'.$livetv_color.'', $plugurl . '/livetv-bundle/css/'.$livetv_color.'.css');
wp_register_style('livetv-widget-off', $plugurl . '/livetv-bundle/css/widget-off.css');
wp_register_style('livetv-qtype', $plugurl . '/livetv-bundle/css/jquery.qtip.min.css');

//We are in Frontend
if(!is_admin())
{
	global $livetv_slide, $livetv_color, $livetv_qtip;
	
	//Enqueue the good qtip script for bubble in frontend
	wp_enqueue_script('livetv-qtip-'.$livetv_qtip.'');
	
	//General css livestream for widget and page live stream
	wp_enqueue_style('livetv-page');
	
	//stylish the frontend with css option dark, light, transparent
	wp_enqueue_style('livetv-'.$livetv_color.'');
	
	//Load qtip style in frontend
	wp_enqueue_style('livetv-qtype');
	
	//Load slide effect in frontend
	wp_enqueue_script('livetv-effect-'.$livetv_slide.'');
	
	include( $livetv_plugin_path . 'page-frontend/page-livetreams.php' );
}

//We are in administration
if(is_admin())
{
	//Enqueue the good qtip script for bubble in admin if it's a pages from plugin
	global $livetv_qtip;
	wp_enqueue_script('livetv-qtip-'.$livetv_qtip.'');
	wp_enqueue_script('livetv-color-picker');
	
	//Load qtip style in admin only if is admin pages from plugin
	wp_enqueue_style('livetv-qtype');
	
	// Registers some default option on activation hook
	function livetv_livestream_page_add_defaut_settings() {
		
		global $wpdb, $current_user;
		
		$settings = array(
			'livetv_defaut_role_wordpress' => 'off', //...
			'livetv_activate_creation_role' => 'off',
			'livetv_h3' => 'img',
			'livetv_view_offline' => 'on',
			'livetv_effect' => 'bottom',
			'livetv_cache' => '3',
			'livetv_irc' => 'sitename_userName',
			'livetv_qtip' => 'dark',
			'livetv_types_order' => 'twitch_own3d',
			'livetv_disable_normal' => 'off',
			'livetv_span_color' => 'BDAD93',
			'livetv_irc_justin' => 'quakenet',
			'livetv_irc_twitch' => 'quakenet',
			'livetv_irc_own3d' => 'quakenet',
			'livetv_list_display' => 'on'
		);
	
		foreach ($settings as $key => $value)
		{
			update_option(''.$key.'', ''.$value.'');
		}
		
		// If the plugin has not yet been used one time
		$livetv_easy_install_status = get_option('livetv_easy_install');
		if ( $livetv_easy_install_status !== '1' )
		{
			//if administrator have changed its ID in db...
			get_currentuserinfo();
			 
			// Setup Default page with shortcode
			$post = array(
				 'post_title' => 'Livestream',
				 'post_content' => '[LivesOnline]',
				 'post_status' => 'publish',
				 'post_type' => 'page',
				 'post_author' => $current_user->ID
			);
			// Insert page
			wp_insert_post( $post , $wp_error );
				
			// Once done for the page with shortcode
			update_option( 'livetv_easy_install', '1' );
		}
	}
	register_activation_hook(__FILE__, 'livetv_livestream_page_add_defaut_settings');
	
	
	
	// Remove all settings and special roles for this plugin on uninstall hook
	function livetv_livestream_page_delete_defaut_settings()
	{
		global $wpdb, $blog_id, $current_user;
		
		wp_clear_scheduled_hook('livetv_schedule');
		
		$settings = array(
			'livetv_defaut_role_wordpress',
			'livetv_activate_creation_role',
			'livetv_h3',
			'livetv_view_offline',
			'livetv_effect',
			'livetv_cache',
			'livetv_irc',
			'livetv_qtip',
			'livetv_3col',
			'livetv_types_order',
			'livetv_disable_normal',
			'livetv_span_color',
			'livetv_irc_own3d',
			'livetv_irc_justin',
			'livetv_irc_twitch',
			'livetv_list_display'
		);
	
		foreach ($settings as $v) {
			delete_option( ''.$v.'' );
		}
		
		$editable_roles = get_roles();
		/*var_dump($editable_roles);*/
	
		foreach($editable_roles as $key => $value)
		{
		
			$temp = preg_match("#^live_#", $key);
		
			if($temp)
			{
				$lastusers = array(
					'blog_id' => $blog_id,
					'role' => ''.$key.'',
					'search' => ID
				);
					
				$blogusers = get_users(''.$lastusers.'' );
				
				foreach($blogusers as $specialroleusers => $specialuser)
				{
					$id = $specialuser->ID;
					
					// get user objet by user ID
					$bloguser = new WP_User( $id );
					
					$bloguser->remove_role( ''.$value.'' );
								
					//Filter current admin to make sure...
					if($current_user->ID != $id)
					{
						$bloguser->set_role( 'subscriber' );
					}
				}
			//Now remove this WP role...
			$roles = new WP_Roles();
			$result = $roles->remove_role(''.$key.'');
			}
		}
		
		//Delete remains
		$wpdb->query("DELETE FROM $wpdb->usermeta WHERE meta_key LIKE 'live\_%'");
		$wpdb->query("DELETE FROM $wpdb->usermeta WHERE meta_key LIKE 'count\_live\_%'");
	}
	register_uninstall_hook(__FILE__, 'livetv_livestream_page_delete_defaut_settings');
	
	//Now construct admin page
	include( $livetv_plugin_path . 'page-admin/page-admin-livetreams.php' );
}


//A new cron interval for thumbnails
add_filter('cron_schedules', 'livetv_scheduled_interval');
function livetv_scheduled_interval($schedules)
{
	$cache = get_option('livetv_cache');
    $schedules['minutes_'.$cache.''] = array('interval' => ($cache * 60), 'display' => vsprintf(__('LiveTV cron cache %s minutes', 'livetv'), $cache));
    return $schedules;
}

//Hook for scheduled task
add_action('livetv_schedule', 'livetv_schedule_thumbnails', 1, 1);

//Only for single sheduled task by cache expiration from the cache option duration.
function livetv_schedule_thumbnails($permalink)
{
	global $wpdb, $livetv_plugin_url, $blog_id, $livetv_plugin_path;
	
	//General admin options First
	$titleh3 = get_option('livetv_h3');
	$general_color = get_option('livetv_color');
	$livetv_view_offline = get_option('livetv_view_offline');
	$limit_to_display = get_option('livetv_pagination_limit');
	
	//Cheat for permalink
	$livetv_current_url = urldecode($permalink);
	
	$current_url = preg_match('#\?#', $livetv_current_url);
	
	if($current_url)
	{
		$livetv_current_url .= '&';
	}
	else
	{
		$livetv_current_url .= '?';
	}
	
	//Cheat for css widget_off with the html cache
	if($livetv_view_offline == 'widget_off')
	{
		$live_offline_class = 'hide-on-page';
	}
	
	$livetv_width_thumbnails = get_option('livetv_width_thumbnails');
	$livetv_height_thumbnails = get_option('livetv_height_thumbnails');
	
	if(!$livetv_width_thumbnails){$livetv_width_thumbnails = '170';}
	if(!$livetv_height_thumbnails){$livetv_height_thumbnails = '100';}
	
	$types = explode('_', get_option('livetv_types_order'));
	$button = get_option('livetv_disable_normal');
	$livetv_span_color = get_option('livetv_span_color');
	$livetv_span_color = '#'.$livetv_span_color;
	$cache = $livetv_plugin_path . 'cache/temp_'.$blog_id.'_live.html';
	
	ob_start();
		
	foreach($types as $keyType => $valueType)
	{
		
		switch($valueType):
					
			
			case 'own3d': //Own3d thumbnails loop
			
					$content = "";
					
					//Start general container own3d
					$content .= '<div id="minithumb-own3d-content">';
						
					if($titleh3 == 'txt')
					{
						$content .= '<h3>'.__('Own3d channels', 'livetv').'</h3>';
					}
					
					//H3
					if($titleh3 == 'img')
					{
						$content .= '<h3><img class="bubble" src="'.$livetv_plugin_url.'images/thumblist-title-own3d.png" title="'.__('own3d channels', 'livetv').'" /></h3>';
					}
					//End H3
					
					$count = 0;
					
					$meta_key = 'live_%_own3d_%';
					$owned = $wpdb->get_results($wpdb->prepare("SELECT user_id, meta_key, meta_value FROM $wpdb->usermeta WHERE meta_key LIKE %s", $meta_key));
					
					$total_limit = count($owned);
					
					if($total_limit > $limit_to_display)
					{				
						//Pagination buttons
						$content .= '<div class="livetv-holder-own3d"></div>';
						//End pagination buttons
					}
					
					//Start pagination container
					$content .= '<ul id="livetv-container-own3d">';
	
					foreach($owned as $key => $value)
					{
						$userID = $value->user_id;
						$userInfo = get_userdata( $userID );
						$userName = $userInfo->display_name;
						$channelName = $value->meta_value;
						$channelKey = $channelName;
	
						$live_now = false;
						$own3d = 'http://api.own3d.tv/liveCheck.php?live_id=' . $channelName .'';
						$xml = simplexml_load_file($own3d);
						$is_live = $xml->xpath('/own3dReply/liveEvent/isLive');
						$live_viewers = $xml->xpath('/own3dReply/liveEvent/liveViewers');
						$live_duration = $xml->xpath('/own3dReply/liveEvent/liveDuration');
						$live_stamp = $live_duration[0];
						$live_now = $is_live[0];
						$live_until = (time() - $live_stamp);
										
										
						if($live_now == 'true')
						{ 
							$count++;
							
							//Start square
							$content .= '<li style="width:'.$livetv_width_thumbnails.'px">';
							$content .= '<span class="minithumb-own3d" style="opacity:1">';
							
								//splash own3d image when online
								$content .= '<span class="minithumb-own3d-splash"><img class="bubble" src="'.$livetv_plugin_url.'images/mini-own3d.png" alt="mask" title="'.__('Online since', 'livetv'). ' ' . date('d-m-Y H:i', $live_until) . '" /></span>';
								//End splash
							
								//thumbnail from own3d
								$content .= '<span class="minithumb-own3d-thumb"><a href="'. esc_url('' . $livetv_current_url . 'liveview='.$channelKey.'&mode=normal&type=own3d', array('http', 'https')).'"><img style="width:'.$livetv_width_thumbnails.'px; height:'.$livetv_height_thumbnails.'px; height:'.$livetv_height_thumbnails.'px" class="bubble" src="http://owned.vo.llnwd.net/e2/live/live_tn_'.$channelName.'_.jpg" title="'. __('Channel by', 'livetv').' '.$userName.' '.__('from own3d', 'livetv').'" alt="own3d thumbnail" /></a></span>';
								//End thumbnail
							
								//Start info
								$content .= '<span class="minithumb-own3d-info" style="color:'.$livetv_span_color.'">';
									
									//Viewers
									$content .= '<span class="w-viewers">'. __('Viewers:', 'livetv').' '. $live_viewers[0] . '</span>';
									//End viewers
									
									//Channel
									$content .= '<span class="w-channel">'. __('channel:', 'livetv'). ' ' . $channelName . '</span>';
									//End channel
									
									//Button
									$content .= '<span class="w-view">';
								
										//Button normal
										if($button == 'off')
										{
											$content .= '<a class="bubble button" href="'.esc_url('' . $livetv_current_url . 'liveview='.$channelKey.'&mode=normal&type=own3d', array('http', 'https')).'" title="'.__('Swtich to normal view', 'livetv').'">'.__('Normal', 'livetv').'</a>';
										}
										//End button normal
										
										//Button large
										$content .=' <a class="bubble button" href="'.esc_url('' . $livetv_current_url . 'liveview='.$channelKey.'&mode=full&type=own3d', array('http', 'https')).'" title="'. __('Swtich to Large view', 'livetv').'">'.__('Large', 'livetv').'</a></span><span class="w-live">'.__('Live!', 'livetv').' ' . date('d-m-Y H:i', $live_until) . '';
										//End button large
									
									//End button	
									$content .= '</span>';
							
								//End infos	
								$content .= '</span>';
						
						//End square
						$content .= '</span>';
						$content .= '</li>';
						}
									
						if($livetv_view_offline == 'on' && $live_now == 'false' || $livetv_view_offline == 'widget_off' && $live_now == 'false')
						{
							$channelKey = 'offline';
							
							//Start square
							$content .= '<li class="offline" style="width:'.$livetv_width_thumbnails.'px">';
							$content .= '<span class="minithumb-own3d offline">';
							
								//splash own3d image when online
								$content .= '<span class="minithumb-own3d-splash"><img class="bubble" alt="mask" src="'.$livetv_plugin_url.'images/offline.png" title="'.__('own3d channel offline', 'livetv'). '" /></span>';
								//End splash
							
								//thumbnail from own3d
								$content .= '<span class="minithumb-own3d-thumb"><a href="'. esc_url('' . $livetv_current_url . 'liveview='.$channelKey.'&mode=normal&type=own3d', array('http', 'https')).'"><img style="width:'.$livetv_width_thumbnails.'px; height:'.$livetv_height_thumbnails.'px; height:'.$livetv_height_thumbnails.'px" class="bubble" src="http://owned.vo.llnwd.net/e2/live/live_tn_'.$channelName.'_.jpg" title="'. __('Channel by', 'livetv').' '.$userName.' '.__('from own3d', 'livetv').'" alt="own3d thumbnail" /></a></span>';
								//End thumbnail
							
								//Start info
								$content .= '<span class="minithumb-own3d-info" style="color:'.$livetv_span_color.'">';
									
									//Viewers
									$content .= '<span class="w-off-viewers">'. __('Viewers: offline', 'livetv').'</span>';
									//End viewers
									
									//Channel
									$content .= '<span class="w-off-channel">'. __('channel:', 'livetv'). ' ' . $userName . '</span>';
									//End channel
									
									//Button
									$content .= '<span class="w-off-view">';
								
										//Button normal
										if($button == 'off')
										{
											$content .= '<a class="bubble button" href="'.esc_url('' . $livetv_current_url . 'liveview='.$channelKey.'&mode=normal&type=own3d', array('http', 'https')).'" title="'.__('Swtich to normal view', 'livetv').'">'.__('Normal', 'livetv').'</a>';
										}
										//End button normal
										
										//Button large
										$content .=' <a class="bubble button" href="'.esc_url('' . $livetv_current_url . 'liveview='.$channelKey.'&mode=full&type=own3d', array('http', 'https')).'" title="'. __('Swtich to Large view', 'livetv').'">'.__('Large', 'livetv').'</a></span><span class="w-off-live">'.__('Live: Offline', 'livetv').'';
										//End button large
									
									//End button	
									$content .= '</span>';
							
								//End infos	
								$content .= '</span>';
						
							//End square
							$content .= '</span>';
							$content .= '</li>';
						}
					}
					
					//End pagination container
					$content .= '</ul>';
					
					//No live streaml online alert
					if($count == 0)
					{
						if($livetv_view_offline == 'off' || $livetv_view_offline == 'widget_off')
						{
							$content .= '<p class="'.$live_offline_class.'">'.__('Currently no live stream online', 'livetv').'</p>';
						}
						
					}
					//End alert
					
					//End general container own3d
					$content .= '</div>';
					
					echo $content;
											
			break;
			
			
			case 'twitch': //Twitch thumbnails loop
			
					$content = "";
					
					//Start general container own3d
					$content .= '<div id="minithumb-twitch-content">';
						
					if($titleh3 == 'txt')
					{
						$content .= '<h3>'.__('Twitch channels', 'livetv').'</h3>';
					}
					
					//H3
					if($titleh3 == 'img')
					{
						$content .= '<h3><img class="bubble" src="'.$livetv_plugin_url.'images/thumblist-title-twitch.png" title="'.__('twitch channels', 'livetv').'" /></h3>';
					}
					//End H3
					
					$count = 0;
					$counter = 0;
					
					$meta_key = 'live_%_twitch_%';
					$twitch = $wpdb->get_results($wpdb->prepare("SELECT user_id, meta_key, meta_value FROM $wpdb->usermeta WHERE meta_key LIKE %s", $meta_key));
					
					$total_limit = count($twitch);
					
					if($total_limit > $limit_to_display)
					{				
						//Pagination buttons
						$content .= '<div class="livetv-holder-twitch"></div>';
						//End pagination buttons
					}
					
					//Start pagination container
					$content .= '<ul id="livetv-container-twitch">';
					

					$userNames = array();
					$base_url = "";
					$base_url .= 'http://api.justin.tv/api/stream/list.json?channel=';
					
					foreach($twitch as $k => $v)
					{
						$counter++;
						$userID = $v->user_id;
						$userInfo = get_userdata( $userID );
						$userNames[] = $userInfo->display_name;
	
						$base_url .= $v->meta_value;
						$base_url .= ',';
					}
					$base_url = preg_replace('#,$#', '', $base_url);
					$json_file = file_get_contents($base_url, 0, null, null);
					$json_array = json_decode($json_file, true);
					/*var_dump($json_file);*/
					
					for($i = 0; $i < $counter; $i++)
					{
						$live_now = 'false';
						
						$userName = esc_html($userNames[$i]);
														
						if(!empty($json_array[$i]['name']))
						{
							$live_now = 'true';
						}
						
						$live_name = esc_html(preg_replace('#live_user_#', '', $json_array[$i]['name']));
						
						if(!$live_name)
						{
							$live_name = esc_html('offline');
						}
						
						$live_title = esc_html($json_array[$i]['channel']['title']);
						$live_status = esc_html($json_array[$i]['channel']['status']);
						$live_game = esc_html($json_array[$i]['meta_game']);
						$live_thumb = esc_html($json_array[$i]['channel']['screen_cap_url_medium']);
						$live_count = esc_html($json_array[$i]['channel_count']);
						$live_until = strtotime(esc_html($json_array[$i]['up_time']));
							
						if($live_now == 'true')
						{
							$count++;
							
							//Start square
							$content .= '<li style="width:'.$livetv_width_thumbnails.'px">';
							$content .= '<span class="minithumb-twitch" style="opacity:1">';
							
								//splash twitch image when online
								$content .= '<span class="minithumb-twitch-splash"><img class="bubble" src="'.$livetv_plugin_url.'images/mini-twitch.png" alt="mask" title="'.__('Online since', 'livetv'). ' ' . date('d-m-Y H:i', $live_until) . '';
								
								if($live_game)
								{
									$content .= ' '. __('on game', 'livetv').' '.$live_game.'';
								} 
								$content .= '" /></span>';
								//End splash
							
								//thumbnail from twitch
								$content .= '<span class="minithumb-twitch-thumb"><a href="'. esc_url('' . $livetv_current_url . 'liveview='.$live_name.'&mode=normal&type=twitch', array('http', 'https')).'"><img style="width:'.$livetv_width_thumbnails.'px; height:'.$livetv_height_thumbnails.'px; height:'.$livetv_height_thumbnails.'px" class="bubble" src="'.$live_thumb.'" title="' . $live_game . ' | ' . $live_title . ' | ' . $live_status . '" alt="twitch thumbnail" /></a></span>';
								//End thumbnail
							
								//Start info
								$content .= '<span class="minithumb-twitch-info" style="color:'.$livetv_span_color.'">';
									
									//Viewers
									$content .= '<span class="w-viewers">'. __('Viewers:', 'livetv').' '. $live_count . '</span>';
									//End viewers
									
									//Channel
									$content .= '<span class="w-channel">'. __('channel:', 'livetv'). ' ' . $live_name . '</span>';
									//End channel
									
									//Button
									$content .= '<span class="w-view">';
								
										//Button normal
										if($button == 'off')
										{
											$content .= '<a class="bubble button" href="'.esc_url('' . $livetv_current_url . 'liveview='.$live_name.'&mode=normal&type=twitch', array('http', 'https')).'" title="'.__('Swtich to normal view', 'livetv').'">'.__('Normal', 'livetv').'</a>';
										}
										//End button normal
										
										//Button large
										$content .=' <a class="bubble button" href="'.esc_url('' . $livetv_current_url . 'liveview='.$live_name.'&mode=full&type=twitch', array('http', 'https')).'" title="'. __('Swtich to Large view', 'livetv').'">'.__('Large', 'livetv').'</a></span><span class="w-live">'.__('Live!', 'livetv').' ' . date('d-m-Y H:i', $live_until) . '';
										//End button large
									
									//End button	
									$content .= '</span>';
							
								//End infos	
								$content .= '</span>';
						
						//End square
						$content .= '</span>';
						$content .= '</li>';
						}
						
						if($livetv_view_offline == 'on' && $live_now == 'false' || $livetv_view_offline == 'widget_off' && $live_now == 'false')
						{
							//Start square
							$content .= '<li class="offline" style="width:'.$livetv_width_thumbnails.'px">';
							$content .= '<span class="minithumb-twitch offline">';
							
								//splash twitch image when online
								$content .= '<span class="minithumb-twitch-splash"><img class="bubble" alt="mask" src="'.$livetv_plugin_url.'images/offline.png" title="'.__('twitch channel offline', 'livetv'). '" /></span>';
								//End splash
							
								//thumbnail from twitch
								$content .= '<span class="minithumb-twitch-thumb"><a href="'. esc_url('' . $livetv_current_url . 'liveview='.$live_name.'&mode=normal&type=twitch', array('http', 'https')).'"><img style="width:'.$livetv_width_thumbnails.'px; height:'.$livetv_height_thumbnails.'px; height:'.$livetv_height_thumbnails.'px" class="bubble" src="'.$livetv_plugin_url.'images/thumblist-mask-offline.png" title="'. __('Channel by', 'livetv').' '.$userName.' '.__('from twitch', 'livetv').'" alt="twitch thumbnail" /></a></span>';
								//End thumbnail
							
								//Start info
								$content .= '<span class="minithumb-twitch-info" style="color:'.$livetv_span_color.'">';
									
									//Viewers
									$content .= '<span class="w-off-viewers">'. __('Viewers: offline', 'livetv').'</span>';
									//End viewers
									
									//Channel
									$content .= '<span class="w-off-channel">'. __('channel:', 'livetv'). ' ' . $userName . '</span>';
									//End channel
									
									//Button
									$content .= '<span class="w-off-view">';
								
										//Button normal
										if($button == 'off')
										{
											$content .= '<a class="bubble button" href="'.esc_url('' . $livetv_current_url . 'liveview='.$live_name.'&mode=normal&type=twitch', array('http', 'https')).'" title="'.__('Swtich to normal view', 'livetv').'">'.__('Normal', 'livetv').'</a>';
										}
										//End button normal
										
										//Button large
										$content .=' <a class="bubble button" href="'.esc_url('' . $livetv_current_url . 'liveview='.$live_name.'&mode=full&type=twitch', array('http', 'https')).'" title="'. __('Swtich to Large view', 'livetv').'">'.__('Large', 'livetv').'</a></span><span class="w-off-live">'.__('Live: Offline', 'livetv').'';
										//End button large
									
									//End button	
									$content .= '</span>';
							
								//End infos	
								$content .= '</span>';
						
							//End square
							$content .= '</span>';
							$content .= '</li>';
						}
					}
					
					//End pagination container
					$content .= '</ul>';
					
					//No live stream online alert
					if($count == '0')
					{
						if($livetv_view_offline == 'off' || $livetv_view_offline == 'widget_off')
						{
							$content .= '<p class="'.$live_offline_class.'">'.__('Currently no live stream online', 'livetv').'</p>';
						}
					}
					//End alert
					
					//End general container own3d
					$content .= '</div>';
					
					echo $content;
											
			break;
									
			
							
			case 'justin': //Justin thumbnails loop
			
					$content = "";
					
					//Start general container own3d
					$content .= '<div id="minithumb-justin-content">';
						
					if($titleh3 == 'txt')
					{
						$content .= '<h3>'.__('Justin channels', 'livetv').'</h3>';
					}
					
					//H3
					if($titleh3 == 'img')
					{
						if($general_color = 'light')
						{
							$justinlight = '-black'; //Poor white on white icon
						}
						$content .= '<h3><img class="bubble" src="'.$livetv_plugin_url.'images/thumblist-title-justin'.$justinlight.'.png" title="'.__('justin channels', 'livetv').'" /></h3>';
					}
					//End H3
					
					$count = 0;
					$counter = 0;
					
					$meta_key = 'live_%_justin_%';
					$justin = $wpdb->get_results($wpdb->prepare("SELECT user_id, meta_key, meta_value FROM $wpdb->usermeta WHERE meta_key LIKE %s", $meta_key));
					
					$total_limit = count($justin);
					
					if($total_limit > $limit_to_display)
					{				
						//Pagination buttons
						$content .= '<div class="livetv-holder-justin"></div>';
						//End pagination buttons
					}
					
					//Start pagination container
					$content .= '<ul id="livetv-container-justin">';
					
					
					$userNames = array();
					$base_url = "";
					$base_url .= 'http://api.justin.tv/api/stream/list.json?channel=';
					
					foreach($justin as $ke => $va)
					{
						$counter++;
						$userID = $va->user_id;
						$userInfo = get_userdata( $userID );
						$userNames[] = $userInfo->display_name;
	
						$base_url .= $va->meta_value;
						$base_url .= ',';
					}
					$base_url = preg_replace('#,$#', '', $base_url);
					$json_file = file_get_contents($base_url, 0, null, null);
					$json_array = json_decode($json_file, true);
					/*var_dump($json_file);*/
					
					
					for($i = 0; $i < $counter; $i++)
					{
						$live_now = 'false';
						
						$userName = esc_html($userNames[$i]);
														
						if(!empty($json_array[$i]['name']))
						{
							$live_now = 'true';
						}
						
						$live_name = esc_html(preg_replace('#live_user_#', '', $json_array[$i]['name']));
						
						if(!$live_name)
						{
							$live_name = esc_html('offline');
						}
						
						$live_title = esc_html($json_array[$i]['channel']['title']);
						$live_status = esc_html($json_array[$i]['channel']['status']);
						$live_game = esc_html($json_array[$i]['meta_game']);
						$live_thumb = esc_html($json_array[$i]['channel']['screen_cap_url_medium']);
						$live_count = esc_html($json_array[$i]['channel_count']);
						$live_until = strtotime(esc_html($json_array[$i]['up_time']));
							
						if($live_now == 'true')
						{
							$count++;
							
							//Start square
							$content .= '<li style="width:'.$livetv_width_thumbnails.'px">';
							$content .= '<span class="minithumb-justin" style="opacity:1">';
							
								//splash justin image when online
								$content .= '<span class="minithumb-justin-splash"><img class="bubble" src="'.$livetv_plugin_url.'images/mini-justin.png" alt="mask" title="'.__('Online since', 'livetv'). ' ' . date('d-m-Y H:i', $live_until) . '';
								
								if($live_game)
								{
									$content .= ' '. __('on game', 'livetv').' '.$live_game.'';
								} 
								$content .= '" /></span>';
								//End splash
							
								//thumbnail from justin
								$content .= '<span class="minithumb-justin-thumb"><a href="'. esc_url('' . $livetv_current_url . 'liveview='.$live_name.'&mode=normal&type=justin', array('http', 'https')).'"><img style="width:'.$livetv_width_thumbnails.'px; height:'.$livetv_height_thumbnails.'px; height:'.$livetv_height_thumbnails.'px" class="bubble" src="'.$live_thumb.'" title="' . $live_game . ' | ' . $live_title . ' | ' . $live_status . '" alt="justin thumbnail" /></a></span>';
								//End thumbnail
							
								//Start info
								$content .= '<span class="minithumb-justin-info" style="color:'.$livetv_span_color.'">';
									
									//Viewers
									$content .= '<span class="w-viewers">'. __('Viewers:', 'livetv').' '. $live_count . '</span>';
									//End viewers
									
									//Channel
									$content .= '<span class="w-channel">'. __('channel:', 'livetv'). ' ' . $live_name . '</span>';
									//End channel
									
									//Button
									$content .= '<span class="w-view">';
								
										//Button normal
										if($button == 'off')
										{
											$content .= '<a class="bubble button" href="'.esc_url('' . $livetv_current_url . 'liveview='.$live_name.'&mode=normal&type=justin', array('http', 'https')).'" title="'.__('Swtich to normal view', 'livetv').'">'.__('Normal', 'livetv').'</a>';
										}
										//End button normal
										
										//Button large
										$content .=' <a class="bubble button" href="'.esc_url('' . $livetv_current_url . 'liveview='.$live_name.'&mode=full&type=justin', array('http', 'https')).'" title="'. __('Swtich to Large view', 'livetv').'">'.__('Large', 'livetv').'</a></span><span class="w-live">'.__('Live!', 'livetv').' ' . date('d-m-Y H:i', $live_until) . '';
										//End button large
									
									//End button	
									$content .= '</span>';
							
								//End infos	
								$content .= '</span>';
						
						//End square
						$content .= '</span>';
						$content .= '</li>';
						}
						
						if($livetv_view_offline == 'on' && $live_now == 'false' || $livetv_view_offline == 'widget_off' && $live_now == 'false')
						{
							//Start square
							$content .= '<li class="offline" style="width:'.$livetv_width_thumbnails.'px">';
							$content .= '<span class="minithumb-justin offline">';
							
								//splash justin image when online
								$content .= '<span class="minithumb-justin-splash"><img class="bubble" alt="mask" src="'.$livetv_plugin_url.'images/offline.png" title="'.__('justin channel offline', 'livetv'). '" /></span>';
								//End splash
							
								//thumbnail from justin
								$content .= '<span class="minithumb-justin-thumb"><a href="'. esc_url('' . $livetv_current_url . 'liveview='.$live_name.'&mode=normal&type=justin', array('http', 'https')).'"><img style="width:'.$livetv_width_thumbnails.'px; height:'.$livetv_height_thumbnails.'px; height:'.$livetv_height_thumbnails.'px" class="bubble" src="'.$livetv_plugin_url.'images/thumblist-mask-offline.png" title="'. __('Channel by', 'livetv').' '.$userName.' '.__('from justin', 'livetv').'" alt="justin thumbnail" /></a></span>';
								//End thumbnail
							
								//Start info
								$content .= '<span class="minithumb-justin-info" style="color:'.$livetv_span_color.'">';
									
									//Viewers
									$content .= '<span class="w-off-viewers">'. __('Viewers: offline', 'livetv').'</span>';
									//End viewers
									
									//Channel
									$content .= '<span class="w-off-channel">'. __('channel:', 'livetv'). ' ' . $userName . '</span>';
									//End channel
									
									//Button
									$content .= '<span class="w-off-view">';
								
										//Button normal
										if($button == 'off')
										{
											$content .= '<a class="bubble button" href="'.esc_url('' . $livetv_current_url . 'liveview='.$live_name.'&mode=normal&type=justin', array('http', 'https')).'" title="'.__('Swtich to normal view', 'livetv').'">'.__('Normal', 'livetv').'</a>';
										}
										//End button normal
										
										//Button large
										$content .=' <a class="bubble button" href="'.esc_url('' . $livetv_current_url . 'liveview='.$live_name.'&mode=full&type=justin', array('http', 'https')).'" title="'. __('Swtich to Large view', 'livetv').'">'.__('Large', 'livetv').'</a></span><span class="w-off-live">'.__('Live: Offline', 'livetv').'';
										//End button large
									
									//End button	
									$content .= '</span>';
							
								//End infos	
								$content .= '</span>';
						
							//End square
							$content .= '</span>';
							$content .= '</li>';
						}
					}
					//End pagination container
					$content .= '</ul>';
					
					//No live streaml online alert
					if($count == '0')
					{
						if($livetv_view_offline == 'off' || $livetv_view_offline == 'widget_off')
						{
							$content .= '<p class="'.$live_offline_class.'">'.__('Currently no live stream online', 'livetv').'</p>';
						}
					}
					//End alert
					
					//End general container own3d
					$content .= '</div>';
					
					echo $content;
											
			break;
											
								
			default:
								
				$live_now = false;
				
			break;
										
		endswitch;
	
	}
	
	//Clean buffer and put the html thumbnails file
	$content_thumbnails = ob_get_contents();
	
	ob_end_clean();
	
	file_put_contents($cache, $content_thumbnails);
}
?>