<?php
/**
  * This file handles Facebook Connect login requests.  When a user logs in via the FB popup window,
  * the js_callbackfunc will redirect us here.  We then use information from FB to log them into WP.
  * See the bottom of this file for notes on the Facebook API
  */

//A very simple check to avoid people from accessing this script directly.
if( !isset($_POST['redirectTo']) || !isset($_POST['_wpnonce']) )
    die("Please do not access this script directly.");

//Make sure we're using PHP5
if(version_compare('5', PHP_VERSION, ">"))
    die("Error: This plugin requires PHP5 or better.");

//Include our options and the Wordpress core
require_once("__inc_wp.php");
require_once("__inc_opts.php");

//If present, include the Premium addon
@include_once(realpath(dirname(__FILE__))."/../WP-FB-AutoConnect-Premium.php");
if( !defined('JFB_PREMIUM') ) @include_once("Premium.php");

//Start logging
$browser = jfb_get_browser();
$jfb_log = "Starting login process (Client: " . $_SERVER['REMOTE_ADDR'] . ", Version: $jfb_version, Browser: " . $browser['shortname'] . " " . $browser['version'] . " for " . $browser['platform'] . ")\n";

//Run one hook before ANYTHING happens.
do_action('wpfb_prelogin');

//Check the nonce to make sure this was a valid login attempt (unless the user has disabled nonce checking)
if( !get_option($opt_jfb_disablenonce) )
{
    if( wp_verify_nonce ($_REQUEST['_wpnonce'], $jfb_nonce_name) != 1 )
    {
        //If there's already a user logged in, tell the user and give them a link back to where they were.
        $currUser = wp_get_current_user(); 
        if( $currUser->ID )
        {
            $msg = "User \"$currUser->user_login\" has already logged in via another browser session.\n";
            $jfb_log .= $msg;
            j_mail("FB Double-Login: " . $currUser->user_login . " -> " . get_bloginfo('name'));
            die($msg . "<br /><br /><a href=\"".$_POST['redirectTo']."\">Continue</a>");
        }
          
        //If the nonce failed for some other reason, report the error.
        $jfb_log .= "WP: nonce check failed (expected '" . wp_create_nonce( $jfb_nonce_name ) . "', received '" . $_REQUEST['_wpnonce'] . "')\n" .
                    "    Original Components) " . get_option($opt_jfb_generated_nonce) . "\n" .
                    "    Current Components)  " . jfb_debug_nonce_components() . "\n";
        if( function_exists('get_plugins') )
        {
            $plugins = get_plugins();
            $jfb_log .= "    Active Plugins:\n";
            foreach($plugins as $plugin) $jfb_log .= "      " . $plugin['Name'] . ' ' . $plugin['Version'] . "\n";
        }
        
        j_die("Nonce check failed, login aborted.\nThis usually due to your browser's privacy settings or a server-side caching plugin.  If you get this error on multiple browsers, please contact the site administrator.\n");
    }
    $jfb_log .= "WP: nonce check passed\n";
}
else
    $jfb_log .= "WP: nonce check DISABLED\n";

    
//Get the redirect URL
if( !isset($_POST['redirectTo']) || !$_POST['redirectTo'] )
    j_die("Error: Missing POST Data (redirect)");
$redirectTo = $_POST['redirectTo'];
$jfb_log .= "WP: Found redirect URL ($redirectTo)\n";


//Include Facebook, making sure another plugin didn't already do so
if( class_exists('Facebook') )
{
    $jfb_log .= "WP: WARNING - Another plugin has already included the Facebook API. "
             .  "If the login fails, please contact the other plugin's author and ask them not to "
             .  "include Facebook for every page throughout Wordpress.\n";
}
else
{
    require_once('facebook-platform/php-sdk-3.1.1/facebook.php');
}


//Connect to FB and make sure we've got a valid session (we should already from the cookie set by JS)  
$jfb_log .= "FB: Initiating Facebook connection via the new API...\n";
$facebook = new Facebook(array('appId'=>get_option($opt_jfb_app_id), 'secret'=>get_option($opt_jfb_api_sec), 'cookie'=>true ));
try                              { $fb_uid = $facebook->getUser(); }
catch (FacebookApiException $e)  { j_die("Error: Exception when getting the Facebook userid. Please verify your API Key and Secret."); }
if (!$fb_uid)                    { j_die("Error: Failed to get the Facebook user session. This is usually due to a temporary problem with Facebook's servers; please try again later."); } 
$jfb_log .= "FB: Connected to session (uid $fb_uid)\n";

//Get the user info from FB
try
{
    $fbuser = $facebook->api('/me');
    $fbuser['profile_url'] = $fbuser['link'];
    $pic = $facebook->api('/me', array('fields' => 'picture', 'type' => 'square'));
    $fbuser['pic_square'] = $pic['picture'];
    $pic = $facebook->api('/me', array('fields' => 'picture', 'type' => 'large'));
    $fbuser['pic_big'] = $pic['picture'];
}
catch( Exception $e ) {j_die("Error: Could not access the Facebook API client (failed on users_getInfo($fb_uid)).  Result: " . print_r($fbuserarray, true) . "; " . $e );}
if( !$fbuser )        {j_die("Error: Could not access the Facebook API client (failed on users_getInfo($fb_uid)).  Result: " . print_r($fbuserarray, true) ); }
$jfb_log .= "FB: Got user info (".$fbuser['name'].")\n";


//See if we were given permission to access the user's email
//This isn't required, and will only matter if it's a new user without an existing WP account
//(since we'll auto-register an account for them, using the contact_email we get from Facebook - if we can...)
$userRevealedEmail = false;
if( strpos($fbuser['email'], 'proxymail.facebook.com') === FALSE )
{
    $jfb_log .= "FB: Email privilege granted (" .$fbuser['email'] . ")\n";
    $userRevealedEmail = true;
}
else
    $jfb_log .= "FB: Email privilege granted, but only for an anonymous proxy address (" . $fbuser['email'] . ")\n";


//Run a hook so users can`examine this Facebook user *before* letting them login.  You might use this
//to limit logins based on friendship status - if someone isn't your friend, you could redirect them
//to an error page (and terminate this script).
do_action('wpfb_connect', array('FB_ID' => $fb_uid, 'facebook' => $facebook) );


//Examine all existing WP users to see if any of them match this Facebook user. 
//First we check their meta: whenever a user logs in with FB, this plugin tags them with usermeta
//so we can find them again easily.  This obviously will only work for returning FB Connect users.
if(!isset($wp_users)) $wp_users = get_users_of_blog();
$jfb_log .= "WP: Searching for user by meta...\n";
foreach ($wp_users as $wp_user)
{
    $meta_uid  = get_user_meta($wp_user->ID, $jfb_uid_meta_name, true);
    if( $meta_uid && $meta_uid == $fb_uid )
    {
        $user_data       = get_userdata($wp_user->ID);
        $user_login_id   = $wp_user->ID;
        $user_login_name = $user_data->user_login;
        $jfb_log .= "WP: Found existing user by meta (" . $user_login_name . ")\n";
        break;
    }
}


//Next, try to lookup their email directly (via Wordpress).  Obviously this will only work if they've revealed
//their "real" address (vs denying access, or changing it to a "proxy" in the popup)
if ( !$user_login_id && $userRevealedEmail )
{
    $jfb_log .= "WP: Searching for user by email address...\n";
    if ( $wp_user = get_user_by('email', $fbuser['email']) )
    {
        $user_login_id = $wp_user->ID;
        $user_data = get_userdata($wp_user->ID);
        $user_login_name = $user_data->user_login;
        $jfb_log .= "WP: Found existing user (" . $user_login_name . ") by email (" . $fbuser['email'] . ")\n";
    }
}


//If we found an existing user, check if they'd previously denied access to their email but have now allowed it.
//If so, we'll want to update their WP account with their *real* email.
if( $user_login_id )
{
    //Check 1: It was previously denied, but is now allowed
    $updateEmail = false;
    if( strpos($user_data->user_email, $jfb_default_email) !== FALSE && strpos($fbuser['email'], $jfb_default_email) === FALSE )
    {
        $jfb_log .= "WP: Previously DENIED email has now been allowed; updating to (".$fbuser['email'].")\n";
        $updateEmail = true;
    }
    //Check 2: It was previously allowed, but only as an anonymous proxy.  They've now revealed their "true" email.
    if( strpos($user_data->user_email, "@proxymail.facebook.com") !== FALSE && strpos($fbuser['email'], "@proxymail.facebook.com") === FALSE )
    {
        $jfb_log .= "WP: Previously PROXIED email has now been allowed; updating to (".$fbuser['email'].")\n";
        $updateEmail = true;
    }
    if( $updateEmail )
    {
        $user_upd = array();
        $user_upd['ID']         = $user_login_id;
        $user_upd['user_email'] = $fbuser['email'];
        wp_update_user($user_upd);
    }
    
    //Run a hook when an existing user logs in
    do_action('wpfb_existing_user', array('WP_ID' => $user_login_id, 'FB_ID' => $fb_uid, 'facebook' => $facebook, 'WP_UserData' => $user_data) );
}


//If we still don't have a user_login_id, the FB user who's logging in has never been to this blog.
//We'll auto-register them a new account.  Note that if they haven't allowed email permissions, the
//account we register will have a bogus email address (but that's OK, since we still know their Facebook ID)
if( !$user_login_id )
{
    $jfb_log .= "WP: No user found. Automatically registering (FB_". $fb_uid . ")\n";
    $user_data = array();
    $user_data['user_login']    = "FB_" . $fb_uid;
    $user_data['user_pass']     = wp_generate_password();
    $user_data['user_nicename'] = sanitize_title($user_data['user_login']);
    $user_data['first_name']    = $fbuser['first_name'];
    $user_data['last_name']     = $fbuser['last_name'];
    $user_data['display_name']  = $fbuser['first_name'];
    $user_data['user_url']      = $fbuser["profile_url"];
    $user_data['user_email']    = $fbuser["email"];
    
    //Run a filter so the user can be modified to something different before registration
    //NOTE: If the user has selected "pretty names", this'll change FB_xxx to i.e. "John.Smith"
    $user_data = apply_filters('wpfb_insert_user', $user_data, $fbuser );
    $user_data = apply_filters('wpfb_inserting_user', $user_data, array('WP_ID' => $user_login_id, 'FB_ID' => $fb_uid, 'facebook' => $facebook, 'FB_UserData' => $fbuser) );
    
    //Insert a new user to our database and make sure it worked
    $user_login_id   = wp_insert_user($user_data);
    if( is_wp_error($user_login_id) )
    {
        $jfb_log .= "WP: Error creating user: " . $user_login_id->get_error_message() . "\n";
        j_die("Error: wp_insert_user failed!<br/><br/>If you get this error while running a Wordpress MultiSite installation, it means you'll need to purchase the <a href=\"$jfb_homepage#premium\">premium version</a> of this plugin to enable full MultiSite support.<br/><br/>If you're <u><i>not</i></u> using MultiSite, please report this bug to the plugin author on the support page <a href=\"$jfb_homepage#feedback\">here</a>.");        
    }
    
    //Success! Notify the site admin.
    $user_login_name = $user_data['user_login'];
    wp_new_user_notification($user_login_name);
    
    //Run an action so i.e. usermeta can be added to a user after registration
    do_action('wpfb_inserted_user', array('WP_ID' => $user_login_id, 'FB_ID' => $fb_uid, 'facebook' => $facebook, 'WP_UserData' => $user_data) );

    //If the option was selected and permission exists, publish an announcement about the user's registration to their wall
    if( get_option($opt_jfb_ask_stream) )
    {
        try
        {
            $jfb_log .= "FB: Publishing registration news to user's wall.\n";
            $facebook->api('/me/feed/', 'post', array('access_token' => $facebook->access_token, 'message' => get_option($opt_jfb_stream_content)));
        }
        catch (FacebookApiException $e)
        {
            $jfb_log .= "WARNING: Failed to publish to the user's wall (is your message too long?) (" . $e . ")\n";
        }
    }
}

//Tag the user with our meta so we can recognize them next time, without resorting to email hashes
update_user_meta($user_login_id, $jfb_uid_meta_name, $fb_uid);
$jfb_log .= "WP: Updated usermeta ($jfb_uid_meta_name)\n";

//Also store the user's facebook avatar(s), in case the user wants to use them later
if( $fbuser['pic_square'] )
{
    update_user_meta($user_login_id, 'facebook_avatar_thumb', $fbuser['pic_square']);
    update_user_meta($user_login_id, 'facebook_avatar_full', $fbuser['pic_big']);
    $jfb_log .= "WP: Updated avatars (" . $fbuser['pic_square'] . ")\n";
}
else
{
    update_user_meta($user_login_id, 'facebook_avatar_thumb', '');
    update_user_meta($user_login_id, 'facebook_avatar_full', '');
    $jfb_log .= "FB: User does not have a profile picture; clearing cached avatar (if present).\n";
}

//Log them in
$rememberme = apply_filters('wpfb_rememberme', isset($_POST['rememberme'])&&$_POST['rememberme']);
wp_set_auth_cookie( $user_login_id, $rememberme );

//Run a custom action.  You can use this to modify a logging-in user however you like,
//i.e. add them to a "Recent FB Visitors" log, assign a role if they're friends with you on Facebook, etc.
do_action('wpfb_login', array('WP_ID' => $user_login_id, 'FB_ID' => $fb_uid, 'facebook' => $facebook) );
do_action('wp_login', $user_login_name);


//Email logs if requested
$jfb_log .= "Login complete (rememberme=" . ($rememberme?"yes":"no") . ")\n";
$jfb_log .= "   WP User : $user_login_name (" . admin_url("user-edit.php?user_id=$user_login_id") . ")\n";
$jfb_log .= "   FB User : " . $fbuser['name'] . " (" . $fbuser["profile_url"] . ")\n";
$jfb_log .= "   Redirect: " . $redirectTo . "\n";
j_mail("FB Login: " . $user_login_name . " -> " . get_bloginfo('name'));


//Redirect the user back to where they were
$delay_redirect = get_option($opt_jfb_delay_redir);
if( !isset($delay_redirect) || !$delay_redirect )
{
    header("Location: " . $redirectTo);
    exit;
}
?>
<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>Logging In...</title>
    </head>
    <body>
        <?php $jfb_log .= "\n---REQUEST:---\n" . print_r($_REQUEST, true); ?> 
        <?php echo "<pre>".$jfb_log."</pre>" ?>
        <?php echo '<a href="'.$redirectTo.'">Continue</a>'?>
    </body>
</html>
<?php


/*
NOTES:
->Tutorial on how to use the NEW API (which I used when upgrading): http://thinkdiff.net/facebook/php-sdk-graph-api-base-facebook-connect-tutorial/
->Basic FB Connect Tutorial: http://wiki.developers.facebook.com/index.php/Facebook_Connect_Tutorial1
->Facebook Javascript API: http://developers.facebook.com/docs/?u=facebook.jslib.FB
->How authentication works: http://wiki.developers.facebook.com/index.php/How_Connect_Authentication_Works
->Note: The FB API is available in JS and PHP; a session that's been started in either of these languages
        can be used in the other: http://wiki.developers.facebook.com/index.php/Using_Facebook_Connect_with_Server-Side_Libraries
        Once you login with Javascript, it creates a session cookie.  Then if you create a new Facebook object in PHP with the same
        API key, it'll automatically activate the session found in the cookie set by JS.
->Note: It's easiest to connect in Javascript (via a popup) then transfer to PHP (as I've done here), but you can also login directly with PHP
        by creating a new Facebook instance, generating a token with auth_token, ask the user to click the login URL, then get the session key
        by using getSession() with this token (as done in Facebook Photo Fetcher).  See: http://forum.developers.facebook.com/viewtopic.php?pid=148426
->Note: An api_key and api_secret are NOT the same as a session_key and session_secret; the api_key identifies the APPLICATION (i.e. this webpage),
        and the SESSION represents an active user connected to this website (about whom we can pull profile info).
*/
?>