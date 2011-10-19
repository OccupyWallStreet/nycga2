<?php

/*
 * Tell WP about the Admin page
 */
add_action('admin_menu', 'jfb_add_admin_page', 99);
function jfb_add_admin_page()
{ 
    global $jfb_name;
    add_options_page("$jfb_name Options", 'WP-FB AutoConn', 'administrator', "wp-fb-autoconnect", 'jfb_admin_page');
}


/**
  * Link to Settings on Plugins page 
  */
add_filter('plugin_action_links', 'jfb_add_plugin_links', 10, 2);
function jfb_add_plugin_links($links, $file)
{
    if( dirname(plugin_basename( __FILE__ )) == dirname($file) )
        $links[] = '<a href="options-general.php?page=' . "wp-fb-autoconnect" .'">' . __('Settings','sitemap') . '</a>';
    return $links;
}

/**
 * Styles
 */
add_action('admin_head', 'jfb_admin_styles');
function jfb_admin_styles()
{
    echo '<style type="text/css">.wp-fb-autoconnect-admin dfn{border-bottom:1px dotted #0000FF; cursor:help; font-style:italic; font-size:80%;}</style>';
}


/**
 * Version 2.1.0 moved to a new version of the Facebook API that required changes on both the free and premium plugins;
 * warn Premium users who have upgraded their free plugin, but not their addon.
 */
if( defined('JFB_PREMIUM') && version_compare(JFB_PREMIUM_VER, 24) == -1 ) add_action('admin_notices', 'jfb_warn_premium_version');
function jfb_warn_premium_version()
{ 
    ?><div class="error"><p><strong>Warning:</strong> This version of WP-FB-AutoConnect requires Premium addon version 24 or better (you're currently using version <?php echo JFB_PREMIUM_VER; ?>).  Please login to your account on <a target="store" href="http://store.justin-klein.com/index.php?route=account/download">store.justin-klein.com</a> to obtain the latest version.  I apologize for the inconvenience, but it was unavoidable due to a sudden change in Facebook's security policies.</p></div><?php
}


/*
 * Output the Admin page
 */
function jfb_admin_page()
{
    global $jfb_name, $jfb_version;
    global $opt_jfb_app_id, $opt_jfb_api_key, $opt_jfb_api_sec, $opt_jfb_email_to, $opt_jfb_email_logs, $opt_jfb_delay_redir, $jfb_homepage;
    global $opt_jfb_ask_perms, $opt_jfb_mod_done, $opt_jfb_ask_stream, $opt_jfb_stream_content;
    global $opt_jfb_bp_avatars, $opt_jfb_wp_avatars, $opt_jfb_valid, $opt_jfb_fulllogerr, $opt_jfb_disablenonce, $opt_jfb_show_credit;
    global $opt_jfb_username_style, $opt_jfb_hidesponsor;
    ?>
    <div class="wrap wp-fb-autoconnect-admin">
     <h2><?php echo $jfb_name; ?> Options</h2>
    <?php
    
    //Show applicable warnings
    if( class_exists('Facebook') )
    {
        ?><div class="error"><p><strong>Warning:</strong> Another plugin has included the Facebook API throughout all of Wordpress.  I suggest you contact that plugin's author and ask them to include it only in pages where it's actually needed.<br /><br />Things may work fine as-is, but *if* the API version included by the other plugin is older than the one required by WP-FB AutoConnect, it's possible that the login process could fail.</p></div><?php
    }
    if(version_compare('5', PHP_VERSION, ">"))
    {
        ?><div class="error"><p>Sorry, but as of v1.3.0, WP-FB AutoConnect requires PHP5.</p></div><?php
        die();
    }
    if( function_exists('is_multisite') && is_multisite() && !jfb_premium() )
    {
        ?><div class="error"><p><strong>Warning:</strong> Wordpress MultiSite is only fully supported by the premium version of this plugin; please see <a href="<?php echo $jfb_homepage ?>#premium"><b>here</b></a> for details.</p></div><?php
    }
    do_action('wpfb_admin_messages');
      
    //Update options
    if( isset($_POST['fb_opts_updated']) )
    {
        //When saving the Facebook options, make sure the key and secret are valid.
        if( !class_exists('Facebook') ) require_once('facebook-platform/validate_php5.php');
        $fbValid = jfb_validate_key($_POST[$opt_jfb_api_key], $_POST[$opt_jfb_api_sec]);
        if( $fbValid && method_exists($fbValid->api_client, 'admin_getAppProperties') )
        {
            $appInfo = $fbValid->api_client->admin_getAppProperties(array('app_id', 'application_name'));
            if( is_array($appInfo) )
            {
                $appID = sprintf("%.0f", $appInfo['app_id']);
                $message = '"' . $appInfo['application_name'] . '" (ID ' . $appID . ')'; 
            }
            else if( $appInfo->app_id )
            {   //Why does this happen? Presumably because another plugin includes a different version of the API that uses objects instead of arrays
                $appID = sprintf("%.0f", $appInfo->app_id);
                $message = '"' . $appInfo->application_name . '" (ID ' . $appID . ')';
            }
            else
            {
                $message = "Key " . $_POST[$opt_jfb_api_key];
                if( defined('WPFBAUTOCONNECT_API'))
                    jfb_auth($jfb_name, $jfb_version, 3, "Unknown instead of array (getAppProperties returns: " . print_r($appInfo, true) . ")" );
                $appID = 0;
                ?><div class="error"><p><strong>Warning:</strong> Facebook failed to retrieve your Application's properties!  The plugin is very unlikely to work until it's fixed.<br /><br />I've thus far not been able to determine the exact cause of this extremely rare problem, but my best guess is that you've made a mistake somewhere in your configuration.  If you see this warning and figure out how to fix it, please let me know <b><a href="<?php echo $jfb_homepage ?>">here</a></b> so I can clarify my setup instructions.</p></div><?php
            }
            update_option( $opt_jfb_valid, 1 );
            if( get_option($opt_jfb_api_key) != $_POST[$opt_jfb_api_key] )
               jfb_auth($jfb_name, $jfb_version, 2, "SET: " . $message );
            ?><div class="updated"><p><strong>Successfully connected with <?php echo $message ?></strong></p></div><?php
        }
        else
        {
            update_option( $opt_jfb_valid, 0 );
            $message = "ERROR: Facebook could not validate your session key and secret!  Are you sure you've entered them correctly?";
            ?><div class="updated"><p><?php echo $message ?></p></div><?php
        }
        //We can save these either way, because if "valid" isn't set, a button won't be shown.
        update_option( $opt_jfb_app_id, $appID);
        update_option( $opt_jfb_api_key, $_POST[$opt_jfb_api_key] );
        update_option( $opt_jfb_api_sec, $_POST[$opt_jfb_api_sec] );
    }
    if( isset($_POST['main_opts_updated']) )
    {
        update_option( $opt_jfb_ask_perms, $_POST[$opt_jfb_ask_perms] );
        update_option( $opt_jfb_ask_stream, $_POST[$opt_jfb_ask_stream] );
        update_option( $opt_jfb_wp_avatars, $_POST[$opt_jfb_wp_avatars] );
        update_option( $opt_jfb_stream_content, $_POST[$opt_jfb_stream_content] );        
        update_option( $opt_jfb_show_credit, $_POST[$opt_jfb_show_credit] );
        update_option( $opt_jfb_email_to, $_POST[$opt_jfb_email_to] );
        update_option( $opt_jfb_email_logs, $_POST[$opt_jfb_email_logs] );
        update_option( $opt_jfb_delay_redir, $_POST[$opt_jfb_delay_redir] );
        update_option( $opt_jfb_fulllogerr, $_POST[$opt_jfb_fulllogerr] );
        update_option( $opt_jfb_disablenonce, $_POST[$opt_jfb_disablenonce] );
        update_option( $opt_jfb_username_style, $_POST[$opt_jfb_username_style] ); 
        ?><div class="updated"><p><strong>Options saved.</strong></p></div><?php         
    }
    if( isset($_POST['prem_opts_updated']) && function_exists('jfb_update_premium_opts'))
    {
        jfb_update_premium_opts();
    }
    if( isset($_POST['mod_rewrite_update']) )
    {
        add_action('generate_rewrite_rules', 'jfb_add_rewrites');
        add_filter('mod_rewrite_rules', 'jfb_fix_rewrites');
        global $wp_rewrite;
        $wp_rewrite->flush_rules();
        update_option( $opt_jfb_mod_done, true );
        ?><div class="updated"><p><strong><?php _e('HTACCESS Updated.', 'mt_trans_domain' ); ?></strong></p></div><?php          
    }
    if( isset($_POST['remove_all_settings']) )
    {
        delete_option($opt_jfb_api_key);
        delete_option($opt_jfb_api_sec);
        delete_option($opt_jfb_email_to);
        delete_option($opt_jfb_email_logs);
        delete_option($opt_jfb_delay_redir);
        delete_option($opt_jfb_ask_perms);
        delete_option($opt_jfb_ask_stream);
        delete_option($opt_jfb_stream_content);
        delete_option($opt_jfb_mod_done);
        delete_option($opt_jfb_valid);
        delete_option($opt_jfb_bp_avatars);
        delete_option($opt_jfb_wp_avatars);
        delete_option($opt_jfb_fulllogerr);
        delete_option($opt_jfb_disablenonce);
        delete_option($opt_jfb_show_credit);
        delete_option($opt_jfb_username_style);
        delete_option($opt_jfb_hidesponsor);
        if( function_exists('jfb_delete_premium_opts') ) jfb_delete_premium_opts();
        ?><div class="updated"><p><strong><?php _e('All plugin settings have been cleared.' ); ?></strong></p></div><?php
    }
    ?>
    
    <?php 
     if( isset($_REQUEST[$opt_jfb_hidesponsor]) )
          update_option($opt_jfb_hidesponsor, $_REQUEST[$opt_jfb_hidesponsor]);
     if(!get_option($opt_jfb_hidesponsor) && !defined('JFB_PREMIUM')): ?>
      	<script type="text/javascript">
        var psHost = (("https:" == document.location.protocol) ? "https://" : "http://");
        document.write(unescape("%3Cscript src='" + psHost + "pluginsponsors.com/direct/spsn/display.php?client=wp-fb-autoconnect&spot=' type='text/javascript'%3E%3C/script%3E"));
        </script>
        <div style="float: right; font-size: 75%; margin-top:-0.75em;"><a href="http://pluginsponsors.com/privacy.html">Privacy policy</a> |
        <a href="?page=wp-fb-autoconnect&<?php echo $opt_jfb_hidesponsor ?>=1">Hide these messages</a></div><br clear="all" />
        <hr />
     <?php endif; ?>
      
    To allow your users to login with their Facebook accounts, you must first setup a Facebook Application for your website:<br /><br />
    <ol>
      <li>Visit <a href="http://www.facebook.com/developers/createapp.php" target="_lnk">www.facebook.com/developers/createapp.php</a></li>
      <li>Type in a name (i.e. the name of your website).  This is the name your users will see on the Facebook login popup.</li>
      <li>Click the "Web Site" tab and fill in your "Site URL" (with a trailing slash).  Note: http://example.com/ and http://www.example.com/ are <i>not</i> the same.</li>
      <li>Click "Save Changes."</li>
      <li>Copy the API Key and Secret to the boxes below.</li>
      <li>Click "Save" below.</li>
    </ol>
    <br />That's it!  Now you can add this plugin's <a href="<?php echo admin_url('widgets.php')?>">sidebar widget</a>, or if you're using BuddyPress, a Facebook button will be automatically added to its built-in login panel.<br /><br />
    For more complete documentation and help, visit the <a href="<?php echo $jfb_homepage?>">plugin homepage</a>.<br />
     
    <br />
    <hr />
    
    <h3>Facebook Connect</h3>
    <form name="formFacebook" method="post" action="">
        <input type="text" size="40" name="<?php echo $opt_jfb_api_key?>" value="<?php echo get_option($opt_jfb_api_key) ?>" /> API Key<br />
        <input type="text" size="40" name="<?php echo $opt_jfb_api_sec?>" value="<?php echo get_option($opt_jfb_api_sec) ?>" /> API Secret
        <input type="hidden" name="fb_opts_updated" value="1" />
        <div class="submit"><input type="submit" name="Submit" value="Connect" /></div>
    </form>
    <hr />
    
    <h3>Basic Options</h3>
    <?php 
    //Only show the options if the Facebook connection is valid!
    if(get_option($opt_jfb_valid)):
    ?>
    
    <form name="formMainOptions" method="post" action="">
    
        <b>Autoregistered Usernames:</b><br />
        <input type="radio" name="<?php echo $opt_jfb_username_style; ?>" value="0" <?php echo (get_option($opt_jfb_username_style)==0?"checked='checked'":"")?> >Based on Facebook ID (i.e. FB_123456)<br />
        <input type="radio" name="<?php echo $opt_jfb_username_style; ?>" value="1" <?php echo (get_option($opt_jfb_username_style)==1?"checked='checked'":"")?> >Based on real name with prefix (i.e. FB_John_Smith)<br />
        <input type="radio" name="<?php echo $opt_jfb_username_style; ?>" value="2" <?php echo (get_option($opt_jfb_username_style)==2?"checked='checked'":"")?> >Based on real name without prefix (i.e. John.Smith) <i><b>(Recommended for BuddyPress)</b></i><br /><br />
    
        <b>E-Mail:</b><br />
        <input type="checkbox" name="<?php echo $opt_jfb_ask_perms?>" value="1" <?php echo get_option($opt_jfb_ask_perms)?'checked="checked"':''?> /> Request permission to get the connecting user's email address<br />

        <br /><b>Announcement:</b><br />
		<?php add_option($opt_jfb_stream_content, "has connected to " . get_option('blogname') . " with WP-FB AutoConnect."); ?>
		<input type="checkbox" name="<?php echo $opt_jfb_ask_stream?>" value="1" <?php echo get_option($opt_jfb_ask_stream)?'checked="checked"':''?> /> Request permission to post the following announcement on users' Facebook walls when they connect for the first time:<br />
		<input type="text" size="100" name="<?php echo $opt_jfb_stream_content?>" value="<?php echo get_option($opt_jfb_stream_content) ?>" /><br />

		<br /><b>Avatars:</b><br />
        <input type="checkbox" name="<?php echo $opt_jfb_wp_avatars?>" value="1" <?php echo get_option($opt_jfb_wp_avatars)?'checked="checked"':''?> /> Use Facebook profile pictures as avatars<br />

        <br /><b>Credit:</b><br />
        <input type="checkbox" name="<?php echo $opt_jfb_show_credit?>" value="1" <?php echo get_option($opt_jfb_show_credit)?'checked="checked"':''?> /> Display a "Powered By" link in the blog footer (would be appreciated! :))<br />

		<br /><b>Debug:</b><br />
		<?php add_option($opt_jfb_email_to, get_bloginfo('admin_email')); ?>
		<input type="checkbox" name="<?php echo $opt_jfb_email_logs?>" value="1" <?php echo get_option($opt_jfb_email_logs)?'checked="checked"':''?> /> Send all event logs to <input type="text" size="40" name="<?php echo $opt_jfb_email_to?>" value="<?php echo get_option($opt_jfb_email_to) ?>" /><br />
		<input type="checkbox" name="<?php echo $opt_jfb_disablenonce?>" value="1" <?php echo get_option($opt_jfb_disablenonce)?'checked="checked"':''?> /> Disable nonce security check (Not recommended)<br />
        <input type="checkbox" name="<?php echo $opt_jfb_delay_redir?>" value="1" <?php echo get_option($opt_jfb_delay_redir)?'checked="checked"':''?> /> Delay redirect after login (<i><u>Not for production sites!</u></i>)<br />
        <input type="checkbox" name="<?php echo $opt_jfb_fulllogerr?>" value="1" <?php echo get_option($opt_jfb_fulllogerr)?'checked="checked"':''?> /> Show full log on error (<i><u>Not for production sites!</u></i>)<br />
        <input type="hidden" name="main_opts_updated" value="1" />
        <div class="submit"><input type="submit" name="Submit" value="Save" /></div>
    </form>
    <hr />

	<?php 
	if( function_exists('jfb_output_premium_panel')) 
	    jfb_output_premium_panel(); 
	else
	    jfb_output_premium_panel_tease();
    ?>    
    
    <!--
    FOR ADVANCED USERS: See _autologin.php for what this does.
    <h3>Mod Rewrite Rules</h3>
    <?php
    if (get_option($opt_jfb_mod_done))
        echo "It looks like your htaccess has already been updated.  If you're having trouble with autologin links, make sure the file is writable and click the Update button again.";
    else
        echo "In order to use this plugin's autologin shortcut links (i.e. www.example.com/autologin/5), your .htaccess file needs to be updated.  Click the button below to update it now.<br />Note that this is an advanced feature and won't be needed by most users; see the plugin's homepage for documentation."
    ?>
    <form name="formRewriteOptions" method="post" action="">
        <input type="hidden" name="mod_rewrite_update" value="1" />
        <div class="submit"><input type="submit" name="Submit" value="Update Rules" /></div>
    </form><hr />
    -->
    
    <?php 
    else:
        echo "Please enter a Facebook API Key and Secret above. Once these have been successfully stored & validated, the main plugin options will become available.<br /><br /><hr />";
    endif;
    ?>
    
    <h3>Delete All Plugin Options</h3>
    <form name="formDebugOptions" method="post" action="">
        <input type="hidden" name="remove_all_settings" value="1" />
        <div class="submit"><input type="submit" name="Submit" value="Delete" /></div>
    </form>
      
   </div><?php
}


/*
 * Append our RewriteRule to htaccess so we can use links like www.example.com/autologin/123
 * This gets invoked by the generate_rewrite_rules filter when we call $wp_rewrite->flush_rules(),
 * which is triggered by the Update Now button
 */
function jfb_add_rewrites($wp_rewrite)
{
    $autologin = explode(get_bloginfo('url'), plugins_url(dirname(plugin_basename(__FILE__))));
    $autologin = trim($autologin[1] . "/_autologin.php", "/") . '?p=$1';
    $wp_rewrite->non_wp_rules = $wp_rewrite->non_wp_rules + array('autologin[/]?([0-9]*)$' => $autologin);
}

/*
 * Wordpress is HARDCODED to specify every rewriterule as [QSA,L]; the only way to get a redirect is to string-replace it.
 */
function jfb_fix_rewrites($rules)
{
    $autologin = explode(get_bloginfo('url'), plugins_url(dirname(plugin_basename(__FILE__))));
    $autologin = trim($autologin[1] . "/_autologin.php", "/") . '?p=$1';
    $rules = str_replace($autologin . ' [QSA,L]', $autologin . ' [R,L]', $rules);
    return $rules;
}


/*
 * I use this for bug-finding; you can remove it if you want, but I'd appreciate it if you didn't.
 * I'll always notify you directly if I find & fix a bug thanks to your site (along with providing the fix) :)
 */
function jfb_activate()  
{
    global $jfb_name, $jfb_version, $opt_jfb_valid, $opt_jfb_api_key;
    $msg = get_option($opt_jfb_valid)?"VALID":(!get_option($opt_jfb_api_key)||get_option($opt_jfb_api_key)==''?"NOKEY":"INVALIDKEY");
    jfb_auth($jfb_name, $jfb_version, 1, "ON: " . $msg);
}
function jfb_deactivate()
{
    global $jfb_name, $jfb_version, $opt_jfb_valid, $opt_jfb_api_key;
    $msg = get_option($opt_jfb_valid)?"VALID":(!get_option($opt_jfb_api_key)||get_option($opt_jfb_api_key)==''?"NOKEY":"INVALIDKEY"); 
    jfb_auth($jfb_name, $jfb_version, 0, "OFF: " . $msg);
}
function jfb_auth($name, $version, $event, $message=0)
{
    $AuthVer = 1;
    $data = serialize(array(
          'pluginID'	=> '3584',
          'plugin'      => $name,
          'version'     => $version,
          'prem_version'=> (defined('JFB_PREMIUM')?("p" . JFB_PREMIUM . 'v' . JFB_PREMIUM_VER):""),
          'wp_version'  => $GLOBALS['wp_version'],
          'php_version' => PHP_VERSION,
          'event'       => $event,
          'message'     => $message,                  
          'SERVER'      => array(
             'SERVER_NAME'    => $_SERVER['SERVER_NAME'],
             'HTTP_HOST'      => $_SERVER['HTTP_HOST'],
             'SERVER_ADDR'    => $_SERVER['SERVER_ADDR'],
             'REMOTE_ADDR'    => $_SERVER['REMOTE_ADDR'],
             'SCRIPT_FILENAME'=> $_SERVER['SCRIPT_FILENAME'],
             'REQUEST_URI'    => $_SERVER['REQUEST_URI'])));
    $args = array( 'blocking'=>false, 'body'=>array(
                            'auth_plugin' => 1,
                            'AuthVer'     => $AuthVer,
                            'hash'        => md5($AuthVer.$data),
                            'data'        => $data));
    wp_remote_post("http://auth.justin-klein.com", $args);
}

/*********************************************************************************/
/**********************Premium Teaser - show the premium options******************/
/*********************************************************************************/

/*
 * This is an exact copy of jfb_output_premium_panel() from the premium addon; it of course just doesn't include implementation...
 */
function jfb_output_premium_panel_tease()
{
    global $jfb_homepage;
    global $opt_jfbp_notifyusers, $opt_jfbp_notifyusers_subject, $opt_jfbp_notifyusers_content, $opt_jfbp_commentfrmlogin, $opt_jfbp_wploginfrmlogin, $opt_jfbp_registrationfrmlogin, $opt_jfbp_cache_avatars, $opt_jfbp_cache_avatar_dir;
    global $opt_jfbp_buttonsize, $opt_jfbp_buttontext, $opt_jfbp_requirerealmail;
    global $opt_jfbp_redirect_new, $opt_jfbp_redirect_new_custom, $opt_jfbp_redirect_existing, $opt_jfbp_redirect_existing_custom, $opt_jfbp_redirect_logout, $opt_jfbp_redirect_logout_custom;
    global $opt_jfbp_restrict_reg, $opt_jfbp_restrict_reg_url, $opt_jfbp_restrict_reg_uid, $opt_jfbp_restrict_reg_pid, $opt_jfbp_restrict_reg_gid;
    global $opt_jfbp_show_spinner, $jfb_data_url;
    global $opt_jfbp_wordbooker_integrate, $opt_jfbp_signupfrmlogin, $opt_jfbp_localize_facebook;
    function disableatt() { echo (defined('JFB_PREMIUM')?"":"disabled='disabled'"); }
    ?>
    <h3>Premium Options <?php echo (defined('JFB_PREMIUM_VER')?"<small>(Version " . JFB_PREMIUM_VER . ")</small>":""); ?></h3>
    
    <?php 
    if( !defined('JFB_PREMIUM') )
        echo "<div class=\"error\"><i><b>The following options are available to Premium users only.</b><br />For information about the WP-FB-AutoConnect Premium Add-On, including purchasing instructions, please visit the plugin homepage <b><a href=\"$jfb_homepage#premium\">here</a></b></i>.</div>";
    ?>
    
    <form name="formPremOptions" method="post" action="">
    
        <b>MultiSite Support:</b><br/>
		<input disabled='disabled' type="checkbox" name="musupport" value="1" <?php echo ((defined('JFB_PREMIUM')&&function_exists('is_multisite')&&is_multisite())?"checked='checked'":"")?> >
		Automatically enabled when a MultiSite install is detected
		<dfn title="The free plugin is not aware of users registered on other sites in your WPMU installation, which can result in problems i.e. if someone tries to register on more than one site.  The Premium version will actively detect and handle existing users across all your sites.">(Mouseover for more info)</dfn><br /><br />
		
		<b>Double Logins:</b><br />
        <input disabled='disabled' type="checkbox" name="doublelogin" value="1" <?php echo (defined('JFB_PREMIUM')?"checked='checked'":"")?> /> Automatically handle double logins 
        <dfn title="If a visitor opens two browser windows, logs into one, then logs into the other, the security nonce check will fail.  This is because in the second window, the current user no longer matches the user for which the nonce was generated.  The free version of the plugin reports this to the visitor, giving them a link to their desired redirect page.  The premium version will transparently handle such double-logins: to visitors, it'll look like the page has just been refreshed and they're now logged in.  For more information on nonces, please visit http://codex.wordpress.org/WordPress_Nonces.">(Mouseover for more info)</dfn><br /><br />
		
		<!-- Facebook's OAuth 2.0 migration BROKE my ability to localize the XFBML-generated dialog.  I've reported a bug, and will do my best to fix it as soon as possible.
		 <b>Facebook Localization:</b><br />
		<?php add_option($opt_jfbp_localize_facebook, 1); ?>
		<input <?php disableatt() ?> type="checkbox" name="<?php echo $opt_jfbp_localize_facebook?>" value="1" <?php echo get_option($opt_jfbp_localize_facebook)?"checked='checked'":""?> >
		Translate Facebook prompts to the same locale as your Wordpress blog (Detected locale: <i><?php echo ( (defined('WPLANG')&&WPLANG!="") ? WPLANG : "en_US" ); ?></i>)
		<dfn title="The Wordpress locale is specified in wp-config.php, where valid language codes are of the form 'en_US', 'ja_JP', 'es_LA', etc.  Please see http://codex.wordpress.org/Installing_WordPress_in_Your_Language for more information on localizing Wordpress, and http://developers.facebook.com/docs/internationalization/ for a list of locales supported by Facebook.">(Mouseover for more info)</dfn><br /><br />
		 -->
						
   		<b>E-Mail Permissions:</b><br />
        <input <?php disableatt() ?> type="checkbox" name="<?php echo $opt_jfbp_requirerealmail?>" value="1" <?php echo get_option($opt_jfbp_requirerealmail)?'checked="checked"':''?> /> Enforce access to user's real (unproxied) email
        <dfn title="The basic option to request user emails will prompt your visitors, but they can still hide their true addresses by using a Facebook proxy (click 'change' in the permissions dialog, and select 'xxx@proxymail.facebook.com').  This option performs a secondary check to enforce that they allow access to their REAL e-mail.  Note that the check requires several extra queries to Facebook's servers, so it could result in a slightly longer delay before the login initiates.">(Mouseover for more info)</dfn><br /><br />

        <b>Avatar Caching:</b><br />         
        <input <?php disableatt() ?> type="checkbox" name="<?php echo $opt_jfbp_cache_avatars?>" value="1" <?php echo get_option($opt_jfbp_cache_avatars)?'checked="checked"':''?> />
        Cache Facebook avatars to: <span style="background-color:#FFFFFF; color:#aaaaaa; padding:2px 0;">
        <?php 
        add_option($opt_jfbp_cache_avatar_dir, 'facebook-avatars');
        $ud = wp_upload_dir();
        echo "<i>" . $ud['basedir'] . "/</i>";         
        ?>
        </span>
        <input <?php disableatt() ?> type="text" size="20" name="<?php echo $opt_jfbp_cache_avatar_dir; ?>" value="<?php echo get_option($opt_jfbp_cache_avatar_dir); ?>" />
        <dfn title="This will make a local copy of Facebook avatars, so they'll always load reliably, even if Facebook's servers go offline or if a user deletes their photo from Facebook. They will be fetched and updated whenever a user logs in.  NOTE: Changing the cache directory will not move existing avatars or update existing users; it only applies to subsequent logins.  It's therefore recommended that you choose a cache directory once, then leave it be.">(Mouseover for more info)</dfn><br /><br />        

        <b>Wordbooker Avatar Integration:</b><br />
        <input <?php disableatt() ?> type="checkbox" name="<?php echo $opt_jfbp_wordbooker_integrate?>" value="1" <?php echo get_option($opt_jfbp_wordbooker_integrate)?'checked="checked"':''?> /> Use Facebook avatars for <a href="http://wordpress.org/extend/plugins/wordbooker/">Wordbooker</a>-imported comments
        <dfn title="The Wordbooker plugin allows you to push blog posts to your Facebook wall, and also to import comments on these posts back to your blog.  This option will display real Facebook avatars for imported comments, provided the commentor logs into your site at least once.">(Mouseover for more info)</dfn><br /><br />

        <b>Widget Appearance:</b><br />
        Please use the <a href="<?php echo admin_url('widgets.php') ?>" target="widgets">WP-FB AutoConnect <b><i>Premium</i></b> Widget</a> if you'd like to:<br />
        &bull; Customize the Widget's text <dfn title="You can customize the text of: User, Pass, Login, Remember, Forgot, Logout, Edit Profile, Welcome.">(Mouseover for more info)</dfn><br />
        &bull; Hide the User/Pass fields (leaving Facebook as the only way to login)<br />
        &bull; Show the user's avatar (when logged in)<br />
		&bull; Show a "Remember" tickbox<br />      
        &bull; Allow the user to simultaneously logout of your site <i>and</i> Facebook<br /><br />
        
        <b>Button Appearance:</b><br />
        <?php add_option($opt_jfbp_buttontext, "Login with Facebook"); ?>
        <?php add_option($opt_jfbp_buttonsize, "2"); ?>
        Text: <input <?php disableatt() ?> type="text" size="30" name="<?php echo $opt_jfbp_buttontext; ?>" value="<?php echo get_option($opt_jfbp_buttontext); ?>" /> <dfn title="This setting applies to ALL of your Facebook buttons (in the widget, wp-login.php, comment forms, etc).">(Mouseover for more info)</dfn><br />
        Style: 
        <input <?php disableatt() ?> type="radio" name="<?php echo $opt_jfbp_buttonsize; ?>" value="2" <?php echo (get_option($opt_jfbp_buttonsize)==2?"checked='checked'":"")?>>Small
        <input <?php disableatt() ?> type="radio" name="<?php echo $opt_jfbp_buttonsize; ?>" value="3" <?php echo (get_option($opt_jfbp_buttonsize)==3?"checked='checked'":"")?>>Medium
        <input <?php disableatt() ?> type="radio" name="<?php echo $opt_jfbp_buttonsize; ?>" value="4" <?php echo (get_option($opt_jfbp_buttonsize)==4?"checked='checked'":"")?>>Large
        <input <?php disableatt() ?> type="radio" name="<?php echo $opt_jfbp_buttonsize; ?>" value="5" <?php echo (get_option($opt_jfbp_buttonsize)==5?"checked='checked'":"")?>>X-Large<br /><br />
        
        <b>Additional Buttons:</b><br />
        <input <?php disableatt() ?> type="checkbox" name="<?php echo $opt_jfbp_commentfrmlogin?>" value="1" <?php echo get_option($opt_jfbp_commentfrmlogin)?'checked="checked"':''?> /> Add a Facebook Login button below the comment form<br />
        <input <?php disableatt() ?> type="checkbox" name="<?php echo $opt_jfbp_wploginfrmlogin?>" value="1" <?php echo get_option($opt_jfbp_wploginfrmlogin)?'checked="checked"':''?> /> Add a Facebook Login button to the standard Login page (wp-login.php)<br />
        <input <?php disableatt() ?> type="checkbox" name="<?php echo $opt_jfbp_registrationfrmlogin?>" value="1" <?php echo get_option($opt_jfbp_registrationfrmlogin)?'checked="checked"':''?> /> Add a Facebook Login button to the Registration page (wp-login.php)<br />
        <input <?php disableatt() ?> type="checkbox" name="<?php echo $opt_jfbp_signupfrmlogin?>" value="1" <?php echo get_option($opt_jfbp_signupfrmlogin)?'checked="checked"':''?> /> Add a Facebook Login button to the Signup page (wp-signup.php) (WPMU Only)<br /><br />
            
        <b>AJAX Spinner:</b><br />
        <input <?php disableatt() ?> type="radio" name="<?php echo $opt_jfbp_show_spinner; ?>" value="0" <?php echo (get_option($opt_jfbp_show_spinner)==0?"checked='checked'":"")?> >Don't show an AJAX spinner<br />
        <input <?php disableatt() ?> type="radio" name="<?php echo $opt_jfbp_show_spinner; ?>" value="1" <?php echo (get_option($opt_jfbp_show_spinner)==1?"checked='checked'":"")?> >Show a white AJAX spinner to indicate the login process has started (<img src=" <?php echo $jfb_data_url ?>/spinner/spinner_white.gif" alt="spinner" />)<br />
        <input <?php disableatt() ?> type="radio" name="<?php echo $opt_jfbp_show_spinner; ?>" value="2" <?php echo (get_option($opt_jfbp_show_spinner)==2?"checked='checked'":"")?> >Show a black AJAX spinner to indicate the login process has started (<img src=" <?php echo $jfb_data_url ?>/spinner/spinner_black.gif" alt="spinner" />)<br /><br />
                
        <b>AutoRegistration Restrictions:</b><br />
        <?php add_option($opt_jfbp_restrict_reg_url, '/') ?>
        <input <?php disableatt() ?> type="radio" name="<?php echo $opt_jfbp_restrict_reg; ?>" value="0" <?php echo (get_option($opt_jfbp_restrict_reg)==0?"checked='checked'":"")?>>Open: Anyone can login (Default)<br />
        <input <?php disableatt() ?> type="radio" name="<?php echo $opt_jfbp_restrict_reg; ?>" value="1" <?php echo (get_option($opt_jfbp_restrict_reg)==1?"checked='checked'":"")?>>Closed: Only login existing blog users<br />
        <input <?php disableatt() ?> type="radio" name="<?php echo $opt_jfbp_restrict_reg; ?>" value="2" <?php echo (get_option($opt_jfbp_restrict_reg)==2?"checked='checked'":"")?>>Invitational: Only login users who've been invited via the <a href="http://wordpress.org/extend/plugins/wordpress-mu-secure-invites/">Secure Invites</a> plugin <dfn title="For invites to work, the connecting user's Facebook email must be accessible, and it must match the email to which the invitation was sent.">(Mouseover for more info)</dfn><br />
		<input <?php disableatt() ?> type="radio" name="<?php echo $opt_jfbp_restrict_reg; ?>" value="3" <?php echo (get_option($opt_jfbp_restrict_reg)==3?"checked='checked'":"")?>>Friendship: Only login users who are friends with uid <input <?php disableatt() ?> type="text" size="15" name="<?php echo $opt_jfbp_restrict_reg_uid?>" value="<?php echo get_option($opt_jfbp_restrict_reg_uid) ?>" /> on Facebook <dfn title="To find your Facebook uid, login and view your Profile Pictures album.  The URL will be something like 'http://www.facebook.com/media/set/?set=a.123.456.789'.  In this example, your uid would be 789 (the numbers after the last decimal point).">(Mouseover for more info)</dfn><br />
		<input <?php disableatt() ?> type="radio" name="<?php echo $opt_jfbp_restrict_reg; ?>" value="4" <?php echo (get_option($opt_jfbp_restrict_reg)==4?"checked='checked'":"")?>>Membership: Only login users who are members of group id <input <?php disableatt() ?> type="text" size="15" name="<?php echo $opt_jfbp_restrict_reg_gid?>" value="<?php echo get_option($opt_jfbp_restrict_reg_gid); ?>" /> on Facebook <dfn title="To find a groups's id, view its URL.  It will be something like 'http://www.facebook.com/group.php?gid=12345678'.  In this example, the group id would be 12345678.">(Mouseover for more info)</dfn><br />
		<input <?php disableatt() ?> type="radio" name="<?php echo $opt_jfbp_restrict_reg; ?>" value="5" <?php echo (get_option($opt_jfbp_restrict_reg)==5?"checked='checked'":"")?>>Fanpage: Only login users who are fans of page id <input <?php disableatt() ?> type="text" size="15" name="<?php echo $opt_jfbp_restrict_reg_pid?>" value="<?php echo get_option($opt_jfbp_restrict_reg_pid); ?>" /> on Facebook <dfn title="To find a page's id, view one of its photo albums.  The URL will be something like 'http://www.facebook.com/media/set/?set=a.123.456.789'.  In this example, the id would be 789 (the numbers after the last decimal point).">(Mouseover for more info)</dfn><br />
        Redirect URL for denied logins: <input <?php disableatt() ?> type="text" size="30" name="<?php echo $opt_jfbp_restrict_reg_url?>" value="<?php echo get_option($opt_jfbp_restrict_reg_url) ?>" /><br /><br />
                
        <b>Custom Redirects:</b><br />
        <?php add_option($opt_jfbp_redirect_new, "1"); ?>
        <?php add_option($opt_jfbp_redirect_existing, "1"); ?>
        <?php add_option($opt_jfbp_redirect_logout, "1"); ?>
        When a new user is autoregistered on your site, redirect them to:<br />
        <input <?php disableatt() ?> type="radio" name="<?php echo $opt_jfbp_redirect_new; ?>" value="1" <?php echo (get_option($opt_jfbp_redirect_new)==1?"checked='checked'":"")?> >Default (refresh current page)<br />
        <input <?php disableatt() ?> type="radio" name="<?php echo $opt_jfbp_redirect_new; ?>" value="2" <?php echo (get_option($opt_jfbp_redirect_new)==2?"checked='checked'":"")?> >Custom URL:
        <input <?php disableatt() ?> type="text" size="47" name="<?php echo $opt_jfbp_redirect_new_custom?>" value="<?php echo get_option($opt_jfbp_redirect_new_custom) ?>" /> <small>(Supports %username% variables)</small><br /><br />
        When an existing user returns to your site, redirect them to:<br />
        <input <?php disableatt() ?> type="radio" name="<?php echo $opt_jfbp_redirect_existing; ?>" value="1" <?php echo (get_option($opt_jfbp_redirect_existing)==1?"checked='checked'":"")?> >Default (refresh current page)<br />
        <input <?php disableatt() ?> type="radio" name="<?php echo $opt_jfbp_redirect_existing; ?>" value="2" <?php echo (get_option($opt_jfbp_redirect_existing)==2?"checked='checked'":"")?> >Custom URL:
        <input <?php disableatt() ?> type="text" size="47" name="<?php echo $opt_jfbp_redirect_existing_custom?>" value="<?php echo get_option($opt_jfbp_redirect_existing_custom) ?>" /> <small>(Supports %username% variables)</small><br /><br />
        When a user logs out of your site, redirect them to:<br />
        <input <?php disableatt() ?> type="radio" name="<?php echo $opt_jfbp_redirect_logout; ?>" value="1" <?php echo (get_option($opt_jfbp_redirect_logout)==1?"checked='checked'":"")?> >Default (refresh current page)<br />
        <input <?php disableatt() ?> type="radio" name="<?php echo $opt_jfbp_redirect_logout; ?>" value="2" <?php echo (get_option($opt_jfbp_redirect_logout)==2?"checked='checked'":"")?> >Custom URL:
        <input <?php disableatt() ?> type="text" size="47" name="<?php echo $opt_jfbp_redirect_logout_custom?>" value="<?php echo get_option($opt_jfbp_redirect_logout_custom) ?>" /><br /><br />

        <b>Welcome Message:</b><br />
        <?php add_option($opt_jfbp_notifyusers_content, "Thank you for logging into " . get_option('blogname') . " with Facebook.\nIf you would like to login manually, you may do so with the following credentials.\n\nUsername: %username%\nPassword: %password%"); ?>
        <?php add_option($opt_jfbp_notifyusers_subject, "Welcome to " . get_option('blogname')); ?>
        <input <?php disableatt() ?> type="checkbox" name="<?php echo $opt_jfbp_notifyusers?>" value="1" <?php echo get_option($opt_jfbp_notifyusers)?'checked="checked"':''?> /> Send a custom welcome e-mail to users who register via Facebook <small>(*If we know their address)</small><br />
        <input <?php disableatt() ?> type="text" size="102" name="<?php echo $opt_jfbp_notifyusers_subject?>" value="<?php echo get_option($opt_jfbp_notifyusers_subject) ?>" /><br />
        <textarea <?php disableatt() ?> cols="85" rows="5" name="<?php echo $opt_jfbp_notifyusers_content?>"><?php echo get_option($opt_jfbp_notifyusers_content) ?></textarea><br />
                                        
        <input type="hidden" name="prem_opts_updated" value="1" />
        <div class="submit"><input <?php disableatt() ?> type="submit" name="Submit" value="Save Premium" /></div>
    </form>
    <hr />
    <?php    
}


?>