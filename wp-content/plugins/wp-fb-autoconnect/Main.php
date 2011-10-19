<?php
/* Plugin Name: WP-FB-AutoConnect
 * Description: A LoginLogout widget with Facebook Connect button, offering hassle-free login for your readers. Clean and extensible. Supports BuddyPress.
 * Author: Justin Klein
 * Version: 2.1.0
 * Author URI: http://www.justin-klein.com/
 * Plugin URI: http://www.justin-klein.com/projects/wp-fb-autoconnect
 */


/*
 * Copyright 2010-2011 Justin Klein (email: justin@justin-klein.com)
 * 
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 2 of the License, or (at your option)
 * any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, write to the Free Software Foundation, Inc., 51
 * Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * -------------------------------------------
 *
 * --------Adding to WP-FB-AutoConnect--------
 * If you're interested in creating a derived plugin, please contact me (Justin Klein) first;
 * although duplicating and supplementing the existing code is one approach, doing so 
 * also means that your duplicate will not benefit from any future updates or bug fixes.
 * Instead, with a bit of coordination I'm sure we could arrange for your addon to tie into the
 * existing hooks & filters (which I can supplement if needed), allowing users to benefit 
 * both from your improvements as well as any future fixes I may provide.
 * 
 * If you do choose to create and release a derived plugin anyway, please just be sure not to represent it
 * as a fully original work, nor attempt to confuse it with the original WP-FB-AutoConnect by means of 
 * its name or otherwise.  You should give credit to the original plugin in a plainly visible location,
 * such as the admin panel and readme file.  Also, please do not remove my links to Paypal or the 
 * Premium Addon as these are the only means through which I fund the considerable time I've devoted to 
 * creating and maintaining this otherwise free plugin.  If you'd like to add your own link that's of course
 * fine, but just make sure it's no more prominent than the original.
 * 
 * Put simply, be fair.  I've put hundreds of hours into this work, and I *have* experienced someone else 
 * trying to claim credit for it.  Please don't be that person.  I'm happy if you feel it's worthy of 
 * additional development, but would appreciate it if you'd work with me and not against me.  Expanding upon 
 * my work in the spirit of free software is welcome.  Stealing my credit and donations is not.
 * 
 * Thanks! :)
 *
 */


require_once("__inc_opts.php");
@include_once(realpath(dirname(__FILE__))."/../WP-FB-AutoConnect-Premium.php");
if( !defined('JFB_PREMIUM') ) @include_once("Premium.php");
require_once("AdminPage.php");
require_once("Widget.php");


/**********************************************************************/
/*******************************GENERAL********************************/
/**********************************************************************/

/*
 * Output a Facebook Connect Button.  Note that the button will not function until you've called 
 * jfb_output_facebook_init().  I use document.write() because the button isn't XHTML valid.
 * NOTE: The button tag itself maybe overwritten by the Premium addon (wpfb_output_button filter)
 */
function jfb_output_facebook_btn()
{
    global $jfb_name, $jfb_version, $jfb_js_callbackfunc, $opt_jfb_valid;
    global $opt_jfb_ask_perms, $opt_jfb_ask_stream, $opt_jfbp_requirerealmail;
    echo "<!-- $jfb_name Button v$jfb_version -->\n";
    if( !get_option($opt_jfb_valid) )
    {
        echo "<!--WARNING: Invalid or Unset Facebook API Key-->";
        return;
    }
    ?>
    <span class="fbLoginButton">
    <script type="text/javascript">//<!--
    <?php 
    $btnTag = "document.write('<fb:login-button v=\"2\" size=\"small\" onlogin=\"$jfb_js_callbackfunc();\">Login with Facebook</fb:login-button>');";  

    //Let the premium addon overwrite the size/text
    $btnTag = apply_filters('wpfb_output_button', $btnTag );
        
    //Tell the button about the extended permissions it'll prompt for
    $email_perms = get_option($opt_jfb_ask_perms) || get_option($opt_jfbp_requirerealmail);
    $stream_perms = get_option($opt_jfb_ask_stream);
    if( $email_perms && $stream_perms )    $attr = 'scope="'.apply_filters('wpfb_extended_permissions','email,publish_stream').'"';
    else if( $email_perms )                $attr = 'scope="'.apply_filters('wpfb_extended_permissions','email').'"';
    else if( $stream_perms )               $attr = 'scope="'.apply_filters('wpfb_extended_permissions','publish_stream').'"';
    else                                   $attr = '';
    $btnTag = str_replace( "login-button ", "login-button " . $attr . " ", $btnTag);
        
    //Output!
    echo $btnTag;
    ?>
    //--></script>
    </span>
    
    <?php
    do_action('wpfb_after_button');
}


/*
 * As an alternative to jfb_output_facebook_btn, this will setup an event to automatically popup the
 * Facebook Connect dialog as soon as the page finishes loading (as if they clicked the button manually) 
 */
function jfb_output_facebook_instapopup( $callbackName=0 )
{
    global $jfb_js_callbackfunc;
    if( !$callbackName ) $callbackName = $jfb_js_callbackfunc;
    
    add_action('wpfb_add_to_asyncinit', 'jfb_invoke_instapopup');
    ?>
    <script type="text/javascript">//<!--
    function showInstaPopup()
    {
    	FB.login(function(response)
		{
    		  if (response.authResponse)
    			<?php echo $callbackName?>();
    		  else
				alert("Sorry, you must be logged in to access this content.");
    	}); 
    }
    //--></script>
    <?php
}
function jfb_invoke_instapopup()
{
    echo "showInstaPopup();";
}



/*
 * Output the JS to init the Facebook API, which will also setup a <fb:login-button> if present.
 * Output this in the footer, so it always comes after the buttons.
 */
add_action('wp_footer', 'jfb_output_facebook_init');
function jfb_output_facebook_init()
{
    global $jfb_name, $jfb_version, $opt_jfb_app_id, $opt_jfb_api_key, $opt_jfb_valid;
    if( !get_option($opt_jfb_valid) ) return;
    
    $channelURL = plugins_url(dirname(plugin_basename(__FILE__))) . "/facebook-platform/channel.html";
    echo "\n<!-- $jfb_name Init v$jfb_version (NEW API) -->\n";
    ?>
    <div id="fb-root"></div>
    <script type="text/javascript">//<!--
      window.fbAsyncInit = function()
      {
        FB.init({
            appId: '<?php echo get_option($opt_jfb_app_id); ?>', status: true, cookie: true, xfbml: true, oauth:true, channelUrl: '<?php echo $channelURL; ?>' 
        });
        <?php do_action('wpfb_add_to_asyncinit'); ?>            
      };

      (function() {
        var e = document.createElement('script');
        e.src = document.location.protocol + '//connect.facebook.net/<?php echo apply_filters('wpfb_output_facebook_locale', 'en_US'); ?>/all.js';
        e.async = true;
        document.getElementById('fb-root').appendChild(e);
      }());
    //--></script>
    <?php
}



/*
 * Output the JS callback function that'll handle FB logins.
 * NOTE: The Premium addon may alter its behavior via the hooks below.
 */
add_action('wp_footer', 'jfb_output_facebook_callback');
function jfb_output_facebook_callback($redirectTo=0, $callbackName=0)
{
     //Make sure the plugin is setup properly before doing anything
     global $jfb_name, $jfb_version;
     global $opt_jfb_ask_perms, $opt_jfb_valid, $jfb_nonce_name;
     global $jfb_js_callbackfunc, $opt_jfb_ask_stream, $jfb_callback_list;
     if( !get_option($opt_jfb_valid) ) return;
     
     //Get out our params
     if( !$redirectTo )  $redirectTo = htmlspecialchars($_SERVER['REQUEST_URI']);
     if( !$callbackName )$callbackName = $jfb_js_callbackfunc;
     echo "\n<!-- $jfb_name Callback v$jfb_version -->\n";
     
     //Make sure we haven't already output a callback with this name
     if( in_array($callbackName, $jfb_callback_list) )
     {
         echo "\n<!--jfb_output_facebook_callback has already generated a callback named $callbackName!  Skipping.-->\n";
         return;
     }
     else
        array_push($jfb_callback_list, $callbackName);
     
     //Output an html form that we'll submit via JS once the FB login is complete; it redirects us to the PHP script that logs us into WP.  
?>
  
  <form id="wp-fb-ac-fm" name="<?php echo $callbackName ?>_form" method="post" action="<?php echo plugins_url(dirname(plugin_basename(__FILE__))) . "/_process_login.php"?>" >
      <input type="hidden" name="redirectTo" value="<?php echo $redirectTo?>" />
<?php 
      //An action to allow the user to inject additional data in the form, to be transferred to the login script
      do_action('wpfb_add_to_form');
?>
      <?php wp_nonce_field ($jfb_nonce_name) ?>   
    </form>
<?php

    //Output the JS callback function, which Facebook will automatically call once it's been logged in.
    ?><script type="text/javascript">//<!--
    function <?php echo $callbackName ?>()
    {
<?php 
		//An action to allow the user to inject additional javascript to get executed before the login takes place
		do_action('wpfb_add_to_js', $callbackName);

        //First, make sure the user logged into Facebook (didn't click "cancel" in the login prompt)
        echo    "    //Make sure the user logged in\n".
            	"    FB.getLoginStatus(function(response)\n".
                "    {\n".
                "      if (!response.authResponse)\n".
                "      {\n".
                apply_filters('wpfb_login_rejected', '').
                "      return;\n".
                "      }\n\n";
                
        //Submit the login and close the FB.getLoginStatus call
        echo apply_filters('wpfb_submit_loginfrm', "      document." . $callbackName . "_form.submit();\n" );
        echo "    });\n";
        ?>
    }
    //--></script>
    <?php
}



/**********************************************************************/
/*******************************CREDIT*********************************/
/**********************************************************************/
global $opt_jfb_show_credit;
if( get_option($opt_jfb_show_credit) ) add_action('wp_footer', 'jfb_show_credit');
function jfb_show_credit()
{
    global $jfb_homepage;
    echo "Facebook login by <a href=\"$jfb_homepage\">WP-FB-AutoConnect</a>";
}


/**********************************************************************/
/*******************************AVATARS********************************/
/**********************************************************************/

/**
 * Legacy Support: there used to be two separate options for WP and BP; it's now just one option
 */
if( get_option($opt_jfb_bp_avatars) )
{
    delete_option($opt_jfb_bp_avatars);
    update_option($opt_jfb_wp_avatars, 1);    
}


/**
  * Optionally replace WORDPRESS avatars with FACEBOOK profile pictures
  */
if( get_option($opt_jfb_wp_avatars) ) add_filter('get_avatar', 'jfb_wp_avatar', 10, 5);
function jfb_wp_avatar($avatar, $id_or_email, $size, $default, $alt)
{
    //First, get the userid
	if (is_numeric($id_or_email))	    
	    $user_id = $id_or_email;
	else if(is_object($id_or_email) && !empty($id_or_email->user_id))
	   $user_id = $id_or_email->user_id;
	else
	   return $avatar; 

	//If we couldn't get the userID, just return default behavior (email-based gravatar, etc)
	if(!isset($user_id) || !$user_id) return $avatar;

	//Now that we have a userID, let's see if we have their facebook profile pic stored in usermeta.  If not, fallback on the default.
	$fb_img = get_user_meta($user_id, 'facebook_avatar_thumb', true);
	if( !$fb_img ) return $avatar;
	
	//If the usermeta doesn't contain an absolute path, prefix it with the path to the uploads dir
	if( strpos($fb_img, "http") === FALSE )
	{
	    $uploads_url = wp_upload_dir();
	    $uploads_url = $uploads_url['baseurl'];
	    $fb_img = trailingslashit($uploads_url) . $fb_img;
	}
	
	//And return the Facebook avatar (rather than the default WP one)
	return "<img alt='" . esc_attr($alt) . "' src='$fb_img' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
}


/*
 * Optionally replace BUDDYPRESS avatars with FACEBOOK profile pictures
 */
if( get_option($opt_jfb_wp_avatars) ) add_filter( 'bp_core_fetch_avatar', 'jfb_bp_avatar', 10, 4 );    
function jfb_bp_avatar($avatar, $params='')
{
    //First, get the userid
	global $comment;
	if (is_object($comment))	                        $user_id = $comment->user_id;
	if (is_object($params)) 	                        $user_id = $params->user_id;
	if (is_array($params) && $params['object']=='user') $user_id = $params['item_id'];
	if (!$user_id)                                      return $avatar;
	
    //Now that we have a userID, let's see if we have their facebook profile pic stored in usermeta.  If not, fallback on the default.
	if( $params['type'] == 'full' ) $fb_img = get_user_meta($user_id, 'facebook_avatar_full', true);
	if( !$fb_img )                  $fb_img = get_user_meta($user_id, 'facebook_avatar_thumb', true);
	if( !$fb_img )                  return $avatar;

	//If the usermeta doesn't contain an absolute path, prefix it with the path to the uploads dir
	if( strpos($fb_img, "http") === FALSE )
	{
	    $uploads_url = wp_upload_dir();
	    $uploads_url = $uploads_url['baseurl'];
	    $fb_img = trailingslashit($uploads_url) . $fb_img;
	}
	
    //And return the Facebook avatar (rather than the default WP one)
    return '<img alt="' . esc_attr($params['alt']) . '" src="' . $fb_img . '" class="avatar" />';
}


/**********************************************************************/
/******************************USERNAMES*******************************/
/**********************************************************************/
    
/*
 * Optionally modify the FB_xxxxxx to something "prettier", based on the user's real name on Facebook
 */
global $opt_jfb_username_style;
if( get_option($opt_jfb_username_style) == 1 || get_option($opt_jfb_username_style) == 2 ) add_filter( 'wpfb_insert_user', 'jfb_pretty_username', 10, 2 );
function jfb_pretty_username( $wp_userdata, $fb_userdata )
{
    global $jfb_log, $opt_jfb_username_style;
    $jfb_log .= "WP: Converting username to \"pretty\" username...\n";
    
    //Create a username from the user's Facebook name
    if( get_option($opt_jfb_username_style) == 1 )
        $name = "FB_" . str_replace( ' ', '', $fb_userdata['first_name'] . "_" . $fb_userdata['last_name'] );
    else
        $name = str_replace( ' ', '', $fb_userdata['first_name'] . "." . $fb_userdata['last_name'] );
    
    //Strip all non-alphanumeric characters, and make sure we've got something left.  If not, we'll just leave the FB_xxxxx username as is.
    $name = sanitize_user($name, true);
    if( strlen($name) <= 1 || $name == "FB__" )
    {
        $jfb_log .= "WP: Warning - Completely non-alphanumeric Facebook name cannot be used; leaving as default.\n";
        return $wp_userdata;
    }
    
    //Make sure the name is unique: if we've already got a user with this name, append a number to it.
    $counter = 1;
    if ( username_exists( $name ) )
    {
        do
        {
            $username = $name;
            $counter++;
            $username = $username . $counter;
        } while ( username_exists( $username ) );
    }
    else
    {
        $username = $name;
    }
        
    //Done!
    $wp_userdata['user_login']   = $username;
    $wp_userdata['user_nicename']= $username;
    $jfb_log .= "WP: Name successfully converted to $username.\n";
    return $wp_userdata;
}



/**********************************************************************/
/*******************BUDDYPRESS (previously in BuddyPress.php)**********/
/**********************************************************************/

/*
 * Default the username style to "Pretty Usernames" if BP is detected.
 */
add_action( 'bp_init', 'jfb_turn_on_prettynames' );
function jfb_turn_on_prettynames()
{
    global $opt_jfb_username_style;
    add_option($opt_jfb_username_style, 2);
}


/*
 * Add a Facebook Login button to the Buddypress sidebar login widget
 * NOTE: If you use this, you mustn't also use the built-in widget - just one or the other!
 */
add_action( 'bp_after_sidebar_login_form', 'jfb_bp_add_fb_login_button' );
function jfb_bp_add_fb_login_button()
{
  if ( !is_user_logged_in() )
  {
      echo "<p></p>";
      jfb_output_facebook_btn();
  }
}

    
/**********************************************************************/
/****************************IE compatibility**************************/
/**********************************************************************/


/**
  * Include the FB class in the <html> tag (only when not already logged in)
  * So stupid IE will render the button correctly
  */
add_filter('language_attributes', 'jfb_output_fb_namespace');
function jfb_output_fb_namespace()
{
    global $current_user;
    if( isset($current_user) && $current_user->ID != 0 ) return;
    if( has_filter( "language_attributes", "wordbooker_schema" ) ) return;
    echo 'xmlns:fb="http://www.facebook.com/2008/fbml"';
}


/**********************************************************************/
/***************************Error Reporting****************************/
/**********************************************************************/

register_activation_hook(__FILE__, 'jfb_activate');
register_deactivation_hook(__FILE__, 'jfb_deactivate');

?>