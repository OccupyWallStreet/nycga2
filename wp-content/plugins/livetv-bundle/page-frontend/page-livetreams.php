<?php
/*  
	Copyright 2012  Laurent (KwarK) Bertrand  (email : kwark@allwebtuts.net)
	
	Please consider a small donation for my work. Behind each code, there is a geek who has to eat.
	Thank you for my futur bundle...pizza-cola. Bundle vs bundle, it's a good deal, no ? 
	Small pizza donation @ http://kwark.allwebtuts.net
	
	You can not remove this comments such as my informations.

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

// Make shortcode to display livestreams list
add_shortcode( 'LivesOnline', 'livetv_add_livestreams_shorcode' );

function livetv_add_livestreams_shorcode()
{
	global $wpdb, $livetv_plugin_irc_activate, $livetv_plugin_url, $blog_id, $livetv_plugin_path;
	
	//General admin options First
	$visibility = get_option('livetv_visibility');
	$message = get_option('livetv_message');
	$general_width = get_option('livetv_width');
	$general_height = get_option('livetv_height');
	$registration = get_option('livetv_registration');
	$titleh3 = get_option('livetv_h3');
	$general_color = get_option('livetv_color');
	$livetv_plugin_url = esc_url($livetv_plugin_url, array('http', 'https'));
	
	$content_top = ""; //<content single view already outside cache>
	$content_thumbnails = "";// <thubnails goes to cache>
	$content_bottom = ""; //<ending content>
	$page_builded = ""; //<builder for returning large string content>
	
	//Cheat for permalink and default permalink
	$livetv_current_url = get_permalink();
	$current_url = preg_match('#\?#', $livetv_current_url);
	
	if($current_url)
	{
		$livetv_current_url .= '&';
	}
	else
	{
		$livetv_current_url .= '?';
	}
	
	//Start ENGLOBE container to fix css for themes
	$content_top .= '<div style="width:100%; height:auto; margin:0 auto; padding:0;">';
	$content_bottom .= '</div>';
	
	//OK lets go
	if(isset($_REQUEST['liveview']) && $_REQUEST['liveview'] != "offline" && isset($_REQUEST['mode']) && $_REQUEST['mode'] != "" && isset($_REQUEST['type']) && $_REQUEST['type'] != "")
	{
		if ($visibility == "members only" && is_user_logged_in() || $visibility == "public")
		{
			//General script livestream - single view
			wp_enqueue_script('livetv-beautiful-effect');
			wp_enqueue_script('livetv-effect-draggable');
			
			$viewmode = esc_attr($_REQUEST['mode']);
			$channelKey = esc_attr($_REQUEST['liveview']);
			$channelType = esc_attr($_REQUEST['type']);
			
			if($channelType == 'justin' && $general_color == 'light')
			{
				$switchimage = '-black';
			} //Cheat for image twitch. Bad result for white on white
		
			$result = $wpdb->get_results($wpdb->prepare("SELECT meta_key FROM $wpdb->usermeta WHERE meta_value=%s", $channelKey));
			
			$livetv_direct = explode('_', $result[0]->meta_key);
	
			$userName = get_user_by('id', $livetv_direct[1]);
			$userName = $userName->display_name;
			$channelName = $channelKey;
			
			//IRC datas
			$livetv_irc_format = get_option('livetv_irc');
			
			$sitename = preg_replace(array('# #','#%20#', '#-#'), array('','',''), strtolower(get_bloginfo('name')));
			$username = preg_replace(array('# #','#%20#', '#-#'), array('','',''), $userName);
			
			switch ($livetv_irc_format):
				
					case 'sitename':
						$livetv_irc = ''.$sitename.'';
					break;
					
					case 'sitename_channelName':
						$livetv_irc = ''.$sitename.'.'.$channelName.'';
					break;
						
					case 'sitename_userName':
						$livetv_irc = ''.$sitename.'.'.$username.'';
					break;
					
					case 'channelName':
						$livetv_irc = ''.$channelName.'';
					break;	
					
					case 'userName':
						$livetv_irc = ''.$username.'';
					break;
					
					case 'sitename_userName_channelName':
						$livetv_irc = ''.$sitename.'.'.$username.'.'.$channelName.'';
					break;
					
					case 'sitename_channelName_userName':
						$livetv_irc = ''.$sitename.'.'.$channelName.'.'.$username.'';
					break;
					
					default:
						$livetv_irc = ''.$channelName.'';
					break;
					
			endswitch;
			//End IRC datas
			
			//Start view mode
			if($viewmode == 'normal' || $viewmode == 'full')
			{
				if($viewmode == 'full')
				{
					wp_enqueue_style('livetv-hook');
					
					$modeView = 'Large';
					$classView = 'full';
					$widthView = '100%';
					$heightView = '590'; //Fetch to the reason
					$widthIRC = '100%';
					$heightIRC = '301';
				}
				
				if($viewmode == 'normal')
				{
					$modeView = 'Medium';
					$classView = 'full'; //Full because is the same with css with 100% value to fit to the container, but for futur...
					$widthView = $general_width;
					$heightView = $general_height;
					$heightIRC = '301';
					$widthIRC = $widthView - 6; //Due to border
				}


				
				//Main container video
				$content_top .= '<div id="'.$classView.'-view-content" class="'.$classView.'-view-content">';
				
					//H2
					if($titleh3 == 'txt')
					{
						$content_top .= '<h2 class="livetv">'.$modeView.' view '.$channelType.' - Channel '.$channelName.' by '.$userName.'</h2>';
					}
					if($titleh3 == 'img')
					{
						$content_top .= '<h2 class="livetv-h2"><img class="bubble" src="'.$livetv_plugin_url.'images/thumblist-title-'.$channelType.''.$switchimage.'.png" title="'.$channelType.' channel '.$modeView.' mode. Channel '.$channelName.' by '.$userName.'" /></h2>';
					}
				
					//Do current video
					$content_top .= do_shortcode('[livestream type="'.$channelType.'" channel="'.$channelName.'" width="'.$widthView.'" height="'.$heightView.'"]');
					//End do current video
				
				//End Main container video	
				$content_top .= '</div>';
				
				
				if($livetv_plugin_irc_activate)
				{
					//Container	share + IRC
					$content_top .= '<div id="'.$classView.'-view-switcher" class="'.$classView.'-view-switcher">';
					
						//First slide (IRC)
						$content_top .= '<div id="'.$classView.'-view-irc">';
						
							$content_top .= '<h4 class="livetv-nxt">';
							
							if($titleh3 == 'img')
							{
								$infoimg = __('Switch to share zone ?', 'livetv');
								$livetv_title = '<img class="bubble" src="'.$livetv_plugin_url.'images/qnet.png" title="'.$infoimg.'" />';
							}
							if($titleh3 == 'txt')
							{
								$livetv_title = 'IRC '.$userName.'';
							}
							$content_top .= ''.$livetv_title.'';
							$content_top .= '</h4>';
						
							//do current IRC chan
							$chat_type = get_option('livetv_irc_' . $channelType);
							
							if($chat_type == 'quakenet')
							{
								$content_top .= do_shortcode('[liveTVChat type="quakenet" channel="'.$livetv_irc.'" width="'.$widthIRC.'" height="'.$heightIRC.'"]');
							}
							else
							{
								//Cheating to debug own3d chat
								if($chat_type == 'own3d')
								{
									//Cheating for own3d, that's works
									$channelName =  'admin_' . $channelName;
								}
								
								$content_top .= do_shortcode('[liveTVChat type="'.$chat_type.'" channel="'.$channelName.'" width="'.$widthIRC.'" height="'.$heightIRC.'"]');
							}
						//End First slide (IRC)
						$content_top .= '</div>';
					
						
						//Second slide (share)
						$content_top .= '<div id="'.$classView.'-view-irc">';
			
							$content_top .= '<ul id="container-sub-live">';
						
								$content_top .= '<h4 class="livetv-nxt">'; //h4 because h3 is the do_shortcode
									
									if($titleh3 == 'img')
									{
										$infoimg = __('Return to chat IRC ?', 'livetv');
										$temp = '<img class="bubble" src="'.$livetv_plugin_url.'images/info.png" title="'.$infoimg.'" />';
									}
									
									if($titleh3 == 'txt')
									{
										$temp = __('Share live', 'livetv');
									}
									
									$content_top .= ''.$temp.'';
									
								$content_top .= '</h4>';
								
						
								$content_top .= '<li id="livetv-recent-posts" class="livetv-widget-container" style="text-align:center;">';
								
									$content_top .= '<h5 class="widget-title">Share '.$userName.'</h5>';
								
										$content_top .= '<ul>';
										
											//Facebook
											$content_top .= '<div class="facebook-share-button"><iframe
								src="http://www.facebook.com/plugins/like.php?href='.get_permalink($post->ID).'&layout=button_count&show_faces=false&width=85&action=like&colorscheme=light&height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:85px; height:21px;" allowTransparency="true"></iframe></div>';
											
											//Twitter
											$content_top .= '<a href="http://twitter.com/share" class="twitter-share-button" data-count="horizontal">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>';
											
											if($viewmode == 'normal')
											{
												$content_top .= '<div class="clear"></div>';
											}
											
											//Google+
											$content_top .= '<div class="gplusone"><script src="http://apis.google.com/js/plusone.js" type="text/javascript"></script><g:plusone size="medium"></g:plusone></div>';
											
											//Messenger
											$content_top .= '<a class="msn-share-button" href="#" onclick="javascript:window.open(\'http://profile.live.com/badge?url='.get_permalink($post->ID).'/\', \'Windows Live\', \'width=550, height=450, top=230, right=450, left=450\'); return false;" title="Messenger live share">Messenger</a>';
											
											
											//General social pages
											$general_join_facebook = get_option('livetv_facebook');
											$general_join_twitter = get_option('livetv_twitter');
											
											
											$content_top .= '<div class="clear">&nbsp;</div>';
											
											if($general_join_facebook)
											{
												$txtfacebook = __('Join us on Facebook', 'livetv');
												$content_top .= '<h4 class="share-social"><a href="'.esc_url($general_join_facebook, array('http', 'https')).'"><img class="bubble" src="' . $livetv_plugin_url . 'images/facebook.png" title="'.$txtfacebook.'" /></a></h4>';
											}
											
											if($general_join_twitter)
											{
												$txttwitter = __('Join us on Twitter', 'livetv');
												$content_top .= '<h4 class="share-social"><a href="'.esc_url($general_join_twitter, array('http', 'https')).'"><img class="bubble" src="' . $livetv_plugin_url . 'images/twitter.png" title="'.$txttwitter.'" /></a></h4>';
											}
										
										$content_top .= '</ul>';
								
								$content_top .= '</li>';
					
		
					
								//Last news
								$content_top .= '<li id="livetv-recent-posts" class="livetv-widget-container">';
							
									$content_top .= '<h5 class="widget-title">'; 
									$content_top .= __('Latest news', 'livetv'); 
									$content_top .= '</h5>';
							
									$content_top .= '<ul>';
										
											$args = array( 'numberposts' => '9', 'post_status' => 'publish');
											$posts = get_posts( $args );
											
											foreach ($posts as $posted) 
											{
												$content_top .= '<li class="news-single"><a href="' . get_permalink( $posted->ID ) . '" title="' . esc_attr( $posted->post_title ) . '">';
												$limit = 62;
												if($viewmode == 'normal'){$limit = 40;}
												$title = esc_attr($posted->post_title,'', false); 
												$trunk = trunc($title, $limit);
												$content_top .= $trunk;
												$content_top .= '&hellip;</a></li>';
											}
										
									$content_top .= '</ul>';
								
								$content_top .= '</li>';
							
							$content_top .= '</ul>';
						
						//End Second slider (share)
						$content_top .= '</div>';
					
					//End Container share + IRC
					$content_top .= '</div>';
				}//End if plugin_irc_activate
			}
			//End view mode
		}
		else
		{
			$content_top .= '<div class="livetv-info"><a href="'.esc_url($registration, array('http', 'https')).'">'.esc_html($message).'</a></div>';
		}
	}
	
	if(isset($_REQUEST['liveview']) && $_REQUEST['liveview'] === "offline")
	{
		$content_top .= '<div class="livetv-info"><a href="#" style="cursor:normal !important" onclick="javascript:return false;">Sorry, live stream offline</a></div>';
	}
	
	//Now construct with or without displaying loop of thumbnails
	$livetv_pagination_limit = esc_attr(get_option('livetv_pagination_limit'));
	
	if($livetv_pagination_limit)
	{
		wp_enqueue_script('livetv-pagination');

		$page_builded .= '<script type="text/javascript">
		/* when document is ready */
		jQuery(document).ready(function($){
			
			$("div.livetv-holder-own3d").jPages({
				containerID  : "livetv-container-own3d",
				perPage      : '.$livetv_pagination_limit.',
				first        : \'first\',
				last         : \'last\',
				previous	 : \'previous\',
				next	     : \'next\'
			});
			
			$("div.livetv-holder-twitch").jPages({
				containerID  : "livetv-container-twitch",
				perPage      : '.$livetv_pagination_limit.',
				first        : \'first\',
				last         : \'last\',
				previous	 : \'previous\',
				next	     : \'next\'
			});
			
			$("div.livetv-holder-justin").jPages({
				containerID  : "livetv-container-justin",
				perPage      : '.$livetv_pagination_limit.',
				first        : \'first\',
				last         : \'last\',
				previous	 : \'previous\',
				next	     : \'next\'
			});
		});
     </script>';
	}
		
	//Cache
	$cachetime = get_option('livetv_cache');
	$livetv_list_display = get_option('livetv_list_display');

	$cache = $livetv_plugin_path . 'cache/temp_'.$blog_id.'_live.html';
		
	$expire = time() - ($cachetime * 60); //valable X minutes

	$test_file = file_exists($cache);
	
	if($test_file)
	{
		$test_time = filemtime($cache);
	}
	
	if(!$test_file || $test_time < $expire)
	{
		//Cache thumbnails expire ok let's go to generate cache but deliver a last cache file to the user
		$args = array();
		$args[] = urlencode(get_permalink());
		
		$schedule_test = wp_next_scheduled('livetv_schedule', $args);
		
		//Cheating for wp-cron to not add multiple times the schedule
		if(!$schedule_test)
		{
			wp_schedule_event(time(), 'minutes_'.$cachetime.'', 'livetv_schedule', $args);
		}
		else
		{
			wp_schedule_event(time(), 'minutes_'.$cachetime.'', 'livetv_schedule', $args);
		}
		
		//If it's the first run or if html file is erased manually, make a php pause to not deliver an empty html file
		if(!$test_file)
		{
			sleep(4);
		}	
	}
	
	$page_builded .= $content_top;
		
	if($livetv_list_display == 'off')
	{
		if(!isset($_REQUEST['liveview']))
		{
			$page_builded .= file_get_contents($cache);
		}
	}
	else
	{
		$page_builded .= file_get_contents($cache);
	}
	
	$page_builded .= $content_bottom;
	
	//wp_clear_scheduled_hook('livetv_schedule', $args);
	
	return $page_builded;
}
?>