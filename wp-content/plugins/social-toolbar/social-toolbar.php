<?php
/*
Plugin Name: Social Toolbar
Plugin URI: http://socialtoolbarpro.com
Description: Wordpress plugin for adding a customizable toolbar with color selection, social network icons, recent tweet and share buttons in footer.
Version: 1.9
Author: DaddyDesign
Tags: footer, toolbar, social networking, social icons, tool bar, share, facebook like, tweet, recent tweet, facebook, twitter, settings, customize, colors,wibiya, social toolbar,google +1,google plusone,plusone,google share,pinit,pinterest,pin it button,pin it bar,pin it,pinterest button
Author URI: http://www.daddydesign.com
*/

/*  Copyright 2011  daddydesign.com  (email : daddydesign@gmail.com)

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
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA   02110-1301  USA
*/

/*
	GLOBAL VARIABLES
*/
global $wp_version;	
$plugin_name="Wordpress Social Toolbar Plugin";
$exit_msg=$plugin_name.' requires WordPress 2.9 or newer. <a href="http://codex.wordpress.org/Upgrading_WordPress">Please update!</a>';
$wpst_version='1.9';

if( !class_exists('WP_Http'))
{
include_once( ABSPATH . WPINC. '/class-http.php' );
}

/* LOAD PLUGIN LANGUAGE FILES */
load_plugin_textdomain('SOCIALTOOLBAR',false,'social-toolbar/languages');

/* Check Wordpress Version 3.0 + */
if (version_compare($wp_version,"2.9","<"))
{
	exit ($exit_msg);
}

if (!defined('WP_CONTENT_URL')) {
	$content_url=content_url();
	define('WP_CONTENT_URL', $content_url);
}
define('DD_SOCIAL_TOOLBAR_PATH',WP_CONTENT_URL.'/plugins/social-toolbar/');
define('DDST_PRO_ONLY_TEXT','<a href="#" title="Social Toolbar" class="social_basic" target="_blank">PRO Version Only</a>');


global $DDSTDefaults,$DDSTSettings,$DDST_Profiles,$DDST_SocialSettings;

$DDSTSettings=get_option('SOCIALTOOLBAROPTIONS');
$DDST_SocialSettings=get_option('SOCIALTOOLBARICONS');
$rss_url='';
$DDST_Profiles=array(
'0'=>array('name'=>'RSS','url'=>'','order'=>0,'enable'=>1),
'1'=>array('name'=>'twitter','url'=>'daddydesign','order'=>1,'enable'=>1),
'2'=>array('name'=>'facebook','url'=>'','order'=>2,'enable'=>1),
'3'=>array('name'=>'myspace','url'=>'','order'=>3,'enable'=>1),
'4'=>array('name'=>'LinkedIn','url'=>'','order'=>4,'enable'=>1),
'5'=>array('name'=>'flickr','url'=>'','order'=>5,'enable'=>1),
'6'=>array('name'=>'vimeo','url'=>'','order'=>6,'enable'=>1),
'7'=>array('name'=>'YouTube','url'=>'','order'=>7,'enable'=>1)
);

$exclude_pages=array();
$DDSTDefaults=array(
'background_color'=>'000000', //Default Background Color
'twitter_background'=>'999999', //Twitter Background Color
'border_color'=>'666666', //Border Color
'icon_type'=>'gray', //Icon Type
'font_family'=>'Arial, Helvetica, sans-serif', //Font Family
'font_size'=>'12px', //Font Size
'font_color'=>'ffffff', //Font Color
'link_color'=>'ffffff', //Link Color
'button_color'=>'white', // Button color
'bird_color'=>'white', //Link Color
'show_tweeter'=>'yes', // Show Tweeter Message
'hover_background'=>'ffffff', // Hover Image Background Color
'rss_url'=>$rss_url, //RSS URL,
'home_page'=>1, //RSS URL,
'category_archive'=>1, //RSS URL,
'blog_single_post'=>'blog_single_post',
'share_home'=>'true',
'google_plus_one'=>'false',
'twitter_timestamp'=>'false',
'max_icons'=>6,
'facebook_setting'=>'false',
'fan_page'=>'https://www.facebook.com/wordpressdesign',
'exclude'=>$exclude_pages,
'credit_logo'=>'true'
);


/* Add options when plugin get activated */
/*Function to Call when Plugin get activated*/
function DDST_activate()
{
	global $DDSTDefaults,$values,$DDST_Profiles;
	$default_settings = get_option('SOCIALTOOLBAROPTIONS');
	$default_settings= wp_parse_args($default_settings, $DDSTDefaults);
	$default_social_settings = get_option('SOCIALTOOLBARICONS');
	$default_social_settings= wp_parse_args($default_social_settings, $DDST_Profiles);
	add_option('SOCIALTOOLBAROPTIONS',$default_settings);
	add_option('SOCIALTOOLBARICONS',$default_social_settings);
}

/* Function to Call when Plugin Deactivated */
function DDST_deactivate()
{
  /* Code needs to be added for deactivate action */
}

register_activation_hook( __FILE__, 'DDST_activate' );
register_deactivation_hook( __FILE__, 'DDST_deactivate' );

/* Add Wordpress Administrative menus */
/* Add Administrator Menus */
function DDST_admin_menu()
{
	$level = 'manage_options';
	add_menu_page('Social Toolbar', 'Social Toolbar', $level, __FILE__, 'social_toolbar_options',DD_SOCIAL_TOOLBAR_PATH.'images/icon.png');
	add_submenu_page(__FILE__, 'Social Profiles', 'Social Profiles', $level, 'social_toolbar_profiles','social_toolbar_profiles');
	add_submenu_page(__FILE__, 'How to Use', 'How to Use', $level, 'social_toolbar_how_to_use','social_toolbar_how_to_use');
}

add_action('admin_menu','DDST_admin_menu');	

function social_toolbar_options()
{
	include_once dirname(__FILE__).'/library/options.php';
}

function social_toolbar_profiles()
{
include_once dirname(__FILE__).'/library/social_profiles.php';
}


function social_toolbar_how_to_use()
{
include_once dirname(__FILE__).'/library/how_to_use.php';
}


/* Function to add javascript files in admin head*/

function DDST_admin_scripts()
{
	
    
	if(isset($_GET['page'])&&($_GET['page']=='social_toolbar_profiles'||$_GET['page']=='social-toolbar/social-toolbar.php' || $_GET['page']=='social_toolbar_how_to_use')){	
	wp_enqueue_style( 'social-toolbar-css',DD_SOCIAL_TOOLBAR_PATH.'css/social_toolbar_admin.css',false);	   
	wp_enqueue_style( 'colorpicker',DD_SOCIAL_TOOLBAR_PATH.'css/colorpicker.css',false);	   
	
	wp_enqueue_script('jquery');
	wp_enqueue_script('ddst-colorpicker', DD_SOCIAL_TOOLBAR_PATH."js/colorpicker.js", array('jquery'), '1.0', false);
	wp_enqueue_script('jquery-ui-custom', DD_SOCIAL_TOOLBAR_PATH."js/jquery-ui-1.7.1.custom.min.js", array('jquery'), '1.0', false);
	wp_enqueue_script('DDST_admin_scripts', DD_SOCIAL_TOOLBAR_PATH."js/ddst_admin_scripts.js", array('jquery'), '1.0', false);
	}
}
add_action('admin_init','DDST_admin_scripts');


function DDST_add_footer_scripts()
{
	if(isset($_GET['page'])&&($_GET['page']=='social_toolbar_profiles'||$_GET['page']=='social-toolbar/social-toolbar.php' || $_GET['page']=='social_toolbar_how_to_use')){	
	wp_deregister_script( 'pinterest' );
    wp_register_script( 'pinterest', 'http://assets.pinterest.com/js/pinit.js');
    wp_enqueue_script( 'pinterest' );
	}
}
add_action('admin_footer', 'DDST_add_footer_scripts');


/* ADD Dashboard Widger */
function DDST_dashboard_widget_function() {

	include_once dirname(__FILE__).'/library/dashboard_widget.php'; 
} 

// Create the function use in the action hook

function DDST_add_dashboard_widgets() {
wp_add_dashboard_widget('DDST_dashboard_widget', 'DaddyDesign.com News', 'DDST_dashboard_widget_function');	
} 

// Hook into the 'wp_dashboard_setup' action to register our other functions

add_action('wp_dashboard_setup', 'DDST_add_dashboard_widgets',1 );

/* Common Functions */
function DDST_fetch_feed($feed='http://feeds2.feedburner.com/daddydesign',$count=5)
{
			include_once(ABSPATH . WPINC . '/feed.php');
		// Get a SimplePie feed object from the specified feed source.
		$rss = fetch_feed($feed);
		if (!is_wp_error( $rss ) ) : // Checks that the object is created correctly 
	    // Figure out how many total items there are, but limit it to 5. 
		$maxitems = $rss->get_item_quantity($count); 
	    // Build an array of all the items, starting with element 0 (first element).
		$rss_items = $rss->get_items(0, $maxitems); 
		endif;
	echo '<ol class="WPSOCIALTOOLBAR_latest_news">';
    if ($maxitems == 0) echo '<li>No items.</li>';
    else
    // Loop through each feed item and display each item as a hyperlink.
    foreach ( $rss_items as $item ) : 
    echo '<li>';
    echo '<a href="'.$item->get_permalink().'" title="">'.$item->get_title().'</a></li>';
    endforeach; 
	echo '</ol>';
}

function DDST_aasorting (&$array, $key) {
    $sorter=array();
    $ret=array();
    reset($array);
    foreach ($array as $ii => $va) {
        $sorter[$ii]=$va[$key];
    }
    asort($sorter);
    foreach ($sorter as $ii => $va) {
        $ret[$ii]=$array[$ii];
    }
    $array=$ret;
	return $array;
}


function DDST_new_tweets()
{
	global $DDST_SocialSettings;
	$social_icons=get_option('SOCIALTOOLBARICONS');
	if (false === ( $fs_tweets = get_transient('DDST_socialtoolbar_1_7') )|| $social_icons[1]['url']!=get_transient('DDST_pro_tweets_user') ) {//if tweets are not in the cache
     $fs_tweets = DDST_get_tweets();//fetch them
     set_transient('DDST_socialtoolbar_1_7', $fs_tweets, 60*60);//cache them for 1 hour
	 set_transient('DDST_pro_tweets_user', $social_icons[1]['url'], 60*5);//cache them for 1 hour
	}
	if($fs_tweets!=FALSE)
	{
	echo '<div id="wps-twitter-status">';
	echo make_clickable($fs_tweets[0]->text);
	echo '</div>';
	}
}
function DDST_get_tweets()
{
	global $DDST_SocialSettings;
	$social_icons=get_option('SOCIALTOOLBARICONS'); 
	if($social_icons[1]['url']=='' || $social_icons[1]['url']==' ')
	{
		$social_icons[1]['url']='daddydesign';
	}
	$social_icons[1]['url']=trim($social_icons[1]['url']);
	$url = "http://api.twitter.com/1/statuses/user_timeline.json?screen_name=".$social_icons[1]['url']."&include_rts=true&count=1";	
    $twitter = @file_get_contents($url);
	if($twitter!=FALSE)
	{
    $fs_tweets = json_decode($twitter);
	}
	else
	{
		$fs_tweets=FALSE;
	}
    return $fs_tweets;
}

/* New Function to Test Twitter API using HTTP class */
function DDST_get_twitter_message()
{
	$social_icons=get_option('SOCIALTOOLBARICONS');
	if($social_icons[1]['url']=='' || $social_icons[1]['url']==' ')
	{
		$social_icons[1]['url']='daddydesign';
	}
	$tweet   = get_option("SOCIALTOOLBAR_lasttweet");
	$url = "http://api.twitter.com/1/statuses/user_timeline.json?screen_name=".$social_icons[1]['url']."&include_rts=true&count=1";	

			if ($tweet['lastcheck'] < ( mktime() - 60 ) || $social_icons[1]['url']!=$tweet['username'] ) {
			$request = new WP_Http;
			$result = $request->request($url);
			$content = array();
		
					if (isset($result->errors) || $result['response']['code']=='404') {
						// display error message of some sort
						echo '<div id="wps-twitter-status-no"></div>';
					} else {
						$content = $result['body'];
						$twitterdata = json_decode($result['body'], true);
							if($twitterdata!=FALSE)
								$i = 0;
								{
								while ($twitterdata[$i]['in_reply_to_user_id'] != '') {
								  $i++;
								}
								$pattern  = '/\@([a-zA-Z]+)/';
								$replace  = '<a href="http://twitter.com/'.strtolower('\1').'">@\1</a>';
								$output   = preg_replace($pattern,$replace,$twitterdata[$i]["text"]);  
								$tweet['lastcheck'] = mktime();
								$tweet['data']    = $output;
								$tweet['rawdata']  = $twitterdata;
								$tweet['followers'] = $twitterdata[0]['user']['followers_count'];
								$tweet['username'] = $social_icons[1]['url'];
								update_option('SOCIALTOOLBAR_lasttweet',$tweet);
								echo '<div id="wps-twitter-status">';
								echo make_clickable($tweet['data']);
								echo '</div>';
								}
					}
			}
			else
			{
						if(!empty($tweet['data']))
						{
						echo '<div id="wps-twitter-status">';
						echo make_clickable($tweet['data']);
						echo '</div>';
						}
						else
						{
							echo '<div id="wps-twitter-status-no"></div>';
						}
	
			}
}

// Function to DETECT mobile phones

function DDST_mobileCSS() {
$useragent=$_SERVER['HTTP_USER_AGENT'];
if(preg_match('/android|avantgo|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))|| strstr($useragent,"iPad"))
	{
	echo '<style type="text/css"> #wp-social-toolbar{ display:none !important; } </style>';

	}
}

add_action('wp_footer', 'DDST_mobileCSS');


/* Include HTML Code to footer */
function DDST_html_code_insert()
{	
	global $DDST_SocialSettings,$DDSTSettings;
	$social_icons=get_option('SOCIALTOOLBARICONS'); 
	$social_options=get_option('SOCIALTOOLBAROPTIONS'); 
	$url = DDST_curPageURL();
	$home_url=get_bloginfo('url').'/';
	$display_code=0;
	$specific_pages=explode(',',$social_options['specific_pages']);
	$exclude_pages=explode(',',$social_options['exclude_pages']);
	$front_page=get_option('show_on_front');
	if($front_page=='page')
	{
	$blog_page=get_option('page_for_posts');
	$home_page=get_option('page_on_front');
	}
	else
	{
	$blog_page=get_option('page_for_posts');
	$home_page=get_option('page_on_front');
	}
	if($social_options['whole_website']=='true')
	{
		DDST_html_code_footer();
	}
	else
	{
		if($url==$home_url && isset($social_options['home_page']))
		{
			DDST_html_code_footer();
		}
		elseif(is_archive())
		{
			if(isset($social_options['category_archive']))
			{
				DDST_html_code_footer();
			}
			else
			{
			}						
		}
		elseif(is_singular() || $front_page=='page')
		{
			global $post,$posts;
			$page_id=$post->ID;
			if($page_id==$home_page || $page_id==$blog_page)
			{
				if($page_id==$home_page && isset($social_options['home_page']))
				{
					DDST_html_code_footer();
				}
				elseif($page_id==$blog_page && isset($social_options['blog_single_post']))
				{
					DDST_html_code_footer();
				}
				else
				{
				}
			}
			elseif(isset($social_options['blog_single_post']) || count($exclude_pages)>0 || count($specific_pages)>0)
			{
				if(in_array($page_id,$exclude_pages))
				{
				}
				elseif(in_array($page_id,$specific_pages))
				{
					DDST_html_code_footer();
				}
				elseif(isset($social_options['blog_single_post']))
				{
					DDST_html_code_footer();
				}
				else
				{
				}

			}
			else
			{
			}
		}
		else
		{
		}
	}
}
add_action('wp_footer', 'DDST_html_code_insert');




/* Added in wp-social-toolbar 1.8 to test Display Settings */
function DDST_display_checking()
{
	global $DDST_SocialSettings,$DDSTSettings;

	$social_icons=get_option('SOCIALTOOLBARICONS'); 
	$url = DDST_curPageURL();
	$display_code=0;
	$specific_pages=explode(',',$DDST_SocialSettings['specific_pages']);
	$exclude_pages=explode(',',$DDST_SocialSettings['exclude']);
	if($DDSTSettings['whole_website']=='true')
	{
		return true;
	}
	else
	{
				if(is_single() || is_page())
				{
					global $post,$posts;
					$page_id=$post->ID;
				}
				else
				{
					$page_id=0;
				}
		if(isset($DDSTSettings['home_page']) && $url==get_bloginfo('url').'/')
		{

			return true;
		}
		elseif(is_single() || is_page())
		{
			if(count($specific_pages)>0)
			{
				if(isset($DDSTSettings['blog_single_post']))
				{
					if(in_array($page_id,$exclude_pages))
					{
						return false;
					}
					elseif(in_array($page_id,$specific_pages))
					{
						return true;
					}
					else
					{
						return true;
					}
				}
			}
		}
		elseif(isset($DDSTSettings['category_archive']) && (is_archive() || is_tag() || is_tax() || is_author()))
		{
			return true;
		}
		elseif(isset($DDSTSettings['blog_single_post']) && (is_single() || is_page()) )
		{
			return true;
		}
		elseif(isset($DDSTSettings['blog_single_post']) && is_page())
		{
			return true;
		}
		 elseif(count($specific_pages)>0 && (is_single() || is_page()))
		{
			if(in_array($page_id,$specific_pages))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		elseif(count($exclude_pages)>0 && (is_single() || is_page()))
		{
			if(in_array($page_id,$exclude_pages))
			{
				return false;
			}
			else
			{
				return true;
			}
		}
		
		else
		{
			return false;
		}
	}
}

function DDST_html_code_footer()
{
	global $DDST_SocialSettings,$wpst_version;
	$DDST_SocialSettings=get_option('SOCIALTOOLBAROPTIONS');
	$theme_folder=$DDST_SocialSettings['icon_type'];
	$button_color=$DDST_SocialSettings['button_color'];
	$bird_color=$DDST_SocialSettings['bird_color'];
	?>
<div id="wp-social-toolbar" class="wp-social-toolbar-<?php echo $wpst_version;?>">
	<div id="wp-social-toolbar-show-box">
			<div id="wps-toolbar-show">
			<img src="<?php echo plugins_url('images/icons/'.$button_color.'/show.png',__FILE__); ?>" class="wpsc_show_button" alt="show"/>
			</div>    
    </div>
    <div class="wpcs-border">&nbsp;</div>
    <div id="wps-toolbar-content">
    	<div id="wps-toolbar-top">
        	<div id="wps-close-button">
            	<img src="<?php echo plugins_url('images/icons/'.$button_color.'/close.png',__FILE__); ?>" class="wpsc_close_button" alt="close" />
            </div>
			<?php
			if($DDST_SocialSettings['show_tweeter']=='yes')
			{
			?>
            
            	<?php
				DDST_new_tweets();
				?>
	        
			<?php
			}
			else
			{
				echo '<div id="wps-twitter-status-no"></div>';
			}
			?>
        </div>
        <div id="wps-toolbar-bottom">
        	<div id="wpsc-social-accounts">
			
				<?php
				$social_icons=get_option('SOCIALTOOLBARICONS');
				$social_settings=get_option('SOCIALTOOLBAROPTIONS');
				$social_icons=DDST_aasorting($social_icons,"order");
				?>
			<!-- START LOOP -->
			<?php 
			$count=0;
			while (list($key, $value) = each($social_icons)) 
			{	
				if($value['enable']==1)
				{
					$value['name']=strtolower($value['name']);
					if($value['name']=='twitter')
					{
						if($value['url']=='' || $value['url']==' ')
						{
							$value['url']='daddydesign';
						}
					?>
					
					<a href="http://twitter.com/<?php echo $value['url']; ?>" title="" target="_blank"><img src="<?php echo plugins_url('images/'.$theme_folder.'/'.$value['name'].'.png',__FILE__); ?>" alt="Follow on Twitter" /></a>
					<?php
					}
					elseif($value['name']=='skype')
					{
						$value['url']=trim($value['url']);
						?>
					<a href="skype:<?php echo $value['url']; ?>?add" title="" target="_blank"><img src="<?php echo plugins_url('images/'.$theme_folder.'/'.$value['name'].'.png',__FILE__); ?>" alt="Skype" /></a>
						<?php
					}
					elseif($value['name']=='gtalk')
					{
						$value['url']=trim($value['url']);
						?>
					<a href="gtalk:chat?jid=<?php echo $value['url']; ?>" title="" target="_blank"><img src="<?php echo plugins_url('images/'.$theme_folder.'/'.$value['name'].'.png',__FILE__); ?>" alt="gtalk" /></a>
						<?php
					}
					elseif($value['name']=='google+')
					{
					?>
					<a href="<?php echo $value['url']; ?>" title="Google +" target="_blank"><img src="<?php echo plugins_url('images/'.$theme_folder.'/googleplus.png',__FILE__); ?>" alt="google+" /></a>
					<?php
					}
					else
					{
					?>
					<a href="<?php echo $value['url']; ?>" title="" target="_blank"><img src="<?php echo plugins_url('images/'.$theme_folder.'/'.$value['name'].'.png',__FILE__); ?>" alt="<?php echo $value['name']; ?>" /></a>
					<?php
					}
				}
			}
			?>
			<!-- END LOOP -->
				
			
            	
				
            </div>
			<?php
			$social_icons=get_option('SOCIALTOOLBARICONS'); 
			if($DDST_SocialSettings['share_home']=='true')
			{
				$share_url=get_bloginfo('url');
			}
			else
			{
				if(is_single()||is_page())
				{
				$share_url=urlencode(get_permalink($post->ID));
				}
				elseif(is_archive())
				{
				
				$share_url=DDST_curPageURL();
				}
				else
				{
				$share_url=DDST_curPageURL();
				}
			}
			if($DDST_SocialSettings['facebook_setting']=='true')
			{
				$fb_share=$DDST_SocialSettings['fan_page'];
			}
			else
			{
				$fb_share=$share_url;
			}
		?>
		<div id="wpsc-social-counts">
			<?php if($DDST_SocialSettings['credit_logo']=='true') : ?>
			<div class="wpcs-share-icons daddydesign"><a href="http://socialtoolbarpro.com" title="social toolbar" target="_blank">
			<img src="<?php echo plugins_url('images/'.$theme_folder.'/social_toolbar.png',__FILE__); ?>"  alt="social toolbar" />
			</a>
			</div>
			<?php endif; ?>
		<?php
		$output='<div class="wpcs-share-icons wpcs-share-icons-twitter"><script src="http://platform.twitter.com/widgets.js" type="text/javascript"></script><a href="http://twitter.com/share?url='.$share_url.'&via='.$social_icons[1]['url'].'&count=horizontal" class="twitter-share-button">Tweet</a></div>';
		if($DDST_SocialSettings['google_plus_one']=='true')
		{
			if($DDST_SocialSettings['share_home']!='true')
			{
			$output.='<div class="wpcs-share-icons"><div class="g-plusone" data-size="medium"></div></div>';
			}
			else
			{
			$output.='<div class="wpcs-share-icons"><div class="g-plusone" data-size="medium" data-href="'.get_bloginfo('url').'"></div></div>';
			}
			$output.='<script type="text/javascript"> (function() { var po = document.createElement(\'script\'); po.type = \'text/javascript\'; po.async = true;  po.src = \'https://apis.google.com/js/plusone.js\'; var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(po, s); })(); </script>';
		}
		$output.='<div class="wpcs-share-icons"><iframe src="http://www.facebook.com/plugins/like.php?href='.$fb_share.'&amp;layout=button_count&amp;show_faces=true&amp;width=90&amp;action=like&amp;font=arial&amp;colorscheme=light&amp;height=25" scrolling="no" frameborder="0" style="border:none;margin-left:auto;margin-right:auto; overflow:hidden; width:90px; height:25px;" allowTransparency="true"></iframe></div></div></div></div>';
		echo $output;
			?>
</div>
	<?php
}


function DDST_curPageURL() {
 $pageURL = 'http';
 if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL; 
}


/* STYLES AND SCRIPTS STARTS*/
function DDST_print_scripts() {
	global $DDSTSettings;

		wp_enqueue_script ('jquery');
		wp_enqueue_script('wpsocialtoolbar',DD_SOCIAL_TOOLBAR_PATH.'js/scripts.js',array('jquery'));
		wp_enqueue_script('wpstcorescripts',DD_SOCIAL_TOOLBAR_PATH.'js/corescripts.js',array('jquery'));
	
}
add_action('wp_print_scripts', 'DDST_print_scripts');
add_action('wp_head', 'DDST_script_style_insert');
function DDST_script_style_insert() { 
	
global $DDSTSettings; 
if($DDSTSettings['background_color']!='')
{
$background_color=$DDSTSettings['background_color'];
}
else
{
$background_color='000000';
}
if($DDSTSettings['border_color']!='')
{
$border_color=$DDSTSettings['border_color'];
}
else
{
$border_color='999999';
}
if($DDSTSettings['twitter_background']!='')
{
$twitter_background=$DDSTSettings['twitter_background'];
}
else
{
$twitter_background='999999';
}

if($DDSTSettings['hover_background']!='')
{
$hover_background=$DDSTSettings['hover_background'];
}
else
{
$hover_background='0.7';
}
if($DDSTSettings['font_color']!='')
{
$twitter_color=$DDSTSettings['font_color'];
}
else
{
$twitter_color='ffffff';
}
if($DDSTSettings['link_color']!='')
{
$twitter_link=$DDSTSettings['link_color'];
}
else
{
$twitter_link='fffffff';
}
if($DDSTSettings['font_size']!='')
{
$font_size=$DDSTSettings['font_size'];
}
else
{
$font_size='13px';
}
if($DDSTSettings['font_family']!='')
{
$font_fam=stripslashes($DDSTSettings['font_family']);
$font_family='font-family:'.$font_fam.';';
}
else
{
$font_family='';
}
	$bird_color=$DDSTSettings['bird_color'];
?>
<link rel="stylesheet" type="text/css" href="<?php echo DD_SOCIAL_TOOLBAR_PATH;?>css/social_toolbar.css" />
<style type="text/css" media="screen">
#wps-toolbar-show{ background:#<?php echo $border_color; ?> !important; }
#wp-social-toolbar-show-box{border-bottom:5px solid #<?php echo $border_color; ?> !important;}
#wps-toolbar-content #wps-toolbar-top #wps-close-button,.wpcs-border{background:#<?php echo $border_color;?> !important;}
#wps-toolbar-content #wps-toolbar-top #wps-twitter-status{background-color:#<?php echo $twitter_background; ?> !important; <?php echo $font_family;?> color:#<?php echo $twitter_color;?>; font-size:<?php echo $font_size;?>; background-image:url('<?php echo DD_SOCIAL_TOOLBAR_PATH;?>/images/icons/<?php echo $bird_color;?>/bird.png'); background-repeat:no-repeat; }
#wpsc-social-accounts,#wps-toolbar-bottom { background:#<?php echo $background_color;?> !important; }
#wpsc-social-accounts img:hover,.daddydesign:hover { background:#<?php echo $hover_background;?>; }
#wp-social-toolbar-show-box,#wps-toolbar-content #wps-toolbar-bottom #wpsc-social-accounts img,#wps-toolbar-content #wps-toolbar-bottom,.wpcs-share-icons { border-color:#<?php echo $border_color;?> !important; }
#wps-toolbar-content #wps-toolbar-top #wps-twitter-status a { color:#<?php echo $twitter_link;?>; }
</style>
<!--[if IE]>
<link rel="stylesheet" type="text/css" href="<?php echo DD_SOCIAL_TOOLBAR_PATH;?>css/ie.css" />
<![endif]-->
<?php
}
/* STYLES AND SCRIPTS ENDS*/
function DDST_admin_footer_code()
{
	?>
	<!-- POPUP For PRO Plugin -->
	<!-- modal content -->
		<div id="basic-modal-content">
			<div id="wpsocial-toolbar-go-pro">
				<div class="wpsocial-toolbar-go-pro-left">
					<p><a href="http://socialtoolbarpro.com" title="Wp Social Toolbar Pro" target="_blank"><img src="<?php echo DD_SOCIAL_TOOLBAR_PATH;?>images/wp_social_toolbar_logo.png" alt="Wp Social Toolbar Pro" /></a></p>
					<h4><?php _e('This feature is only available in the PRO version of this plug-in.','WPSOCIALTOOLBAR'); ?></h4>
					<h2><?php _e('WHY GO PRO? ','WPSOCIALTOOLBAR'); ?><span><a href="http://socialtoolbarpro.com" title="Wp Social Toolbar Pro" target="_blank"><?php _e('CLICK HERE TO FIND OUT','WPSOCIALTOOLBAR');?></a></h2>
				</div>
				<div class="wpsocial-toolbar-go-pro-right">
						<a href="http://socialtoolbarpro.com" title="Buy Wp Social Toolbar Pro" target="_blank"><img src="<?php echo DD_SOCIAL_TOOLBAR_PATH;?>images/go_pro.png" alt="Buy Wp Social Toolbar Pro" /></a>
				</div>
			</div>
		</div>
		<!-- preload the images -->
		<div style='display:none'>
			<img src='<?php echo DD_SOCIAL_TOOLBAR_PATH; ?>/images/x.png' alt='' />
		</div>
<?php 
}
?>