<?php
/*
Plugin Name: Peter's Login Redirect
Plugin URI: http://www.theblog.ca/wplogin-redirect
Description: Redirect users to different locations after logging in. Define a set of rules for specific users, user with specific roles, users with specific capabilities, and a blanket rule for all other users. This is all managed in Settings > Login/logout redirects.
Author: Peter
Version: 2.5.3
Change Log:
2012-06-15  2.5.3: Bug fix: Fallback redirect rule wouldn't update properly if logout URL was blank on MySQL installs with strict mode enabled (thanks kvandekrol!)
2012-02-06  2.5.2: Bug fix: Fallback redirect rule updates were broken for non-English installs.
2012-01-17  2.5.1: Bug fix: Redirect after registration back-end code was missed in 2.5.0, and thus that feature wasn't actually working.
2012-01-15  2.5.0: Added redirect after registration option. Also made plugin settings editable in the WordPress admin panel.
2012-01-05  2.4.0: Added support for URL variable "postid-23". Also added documentation on how to set up redirect on first login.
2011-11-06  2.3.0: Added support for URL variable "siteurl" and "homeurl". Also added filter to support custom replacement variables in the URL. See readme.txt for documentation.
2011-09-21  2.2.0: Support basic custom logout redirect URL for all users only. Future versions will have the same framework for logout redirects as for login redirects.
2011-08-13  2.1.1: Minor code cleanup. Note: users now need "manage_links" permissions to edit redirect settings by default.
2011-06-06  2.1.0: Added hooks to facilitate adding your own extensions to the plugin. See readme.txt for documentation.
2011-03-03  2.0.0: Added option to allow a redirect_to POST or GET variable to take precedence over this plugin's rules.
2010-12-15  1.9.3: Made plugin translatable (Thanks Anja!)
2010-08-20  1.9.2: Bug fix in code syntax.
2010-08-03  1.9.1: Bug fix for putting the username in the redirect URL.
2010-08-02  1.9.0: Added support for a separate redirect controller URL for compatibility with Gigya and similar plugins that bypass the regular WordPress login redirect mechanism. See the $rul_use_redirect_controller setting within this plugin.
2010-05-13  1.8.1: Added proper encoding of username in the redirect URL if the username has spaces.
2010-03-18  1.8.0: Added the ability to specify a username in the redirect URL for more dynamic URL generation.
2010-03-04  1.7.3: Minor tweak on settings page for better compatibility with different WordPress URL setups.
2010-01-11  1.7.2: Plugin now removes its database tables when it is uninstalled, instead of when it is deactivated. This prevents the redirect rules from being deleted when upgrading WordPress automatically.
2009-10-07  1.7.1: Minor database compatibility tweak. (Thanks KCP!) 
2009-05-31  1.7.0: Added option $rul_local_only (in the plugin file itself) to bypass the WordPress default limitation of only redirecting to local URLs.
2009-02-06  1.6.1: Minor database table tweak for better compatibility with different setups. (Thanks David!)
2008-11-26  1.6.0: Added a function rul_register that acts the same as the wp_register function you see in templates, except that it will return the custom defined admin address
2008-09-17  1.5.1: Fixed compatibility for sites with a different table prefix setting in wp-config.php. (Thanks Eric!) 
Author URI: http://www.theblog.ca
*/

/*
--------------
As of version 2.5.0 of this plugin and higher, all redirect settings are configured in 'Settings" > "Login/logout redirects" in the WordPress admin panel
--------------
*/

// Enable translations
add_action( 'init', 'rul_textdomain' );
function rul_textdomain()
{
	load_plugin_textdomain( 'peterloginrd', PLUGINDIR . '/' . dirname( plugin_basename(__FILE__) ), dirname( plugin_basename(__FILE__) ) );
}

global $wpdb;
global $rul_db_addresses;
global $rul_version;
// Name of the database table that will hold group information and moderator rules
$rul_db_addresses = $wpdb->prefix . 'login_redirects';
$rul_version = '2.5.3';

// A global variable that we will add to on the fly when $rul_local_only is set to equal 1
$rul_allowed_hosts = array();

// Some helper functions, all "public static" in PHP5 land
class rulRedirectFunctionCollection
{
    /*
        Grabs settings from the database as of version 2.5.0 of this plugin.
        Defaults are defined here, but the settings values should be edited in the WordPress admin panel.
        If no setting is asked for, then it returns an array of all settings; otherwise it returns a specific setting
    */
    function get_settings( $setting=false )
    {
        $rul_settings = array();

        // Setting this to 1 will make it so that you can redirect (login and logout) to any valid http or https URL, even outside of your current domain
        // Setting this to 2 will make it so that you can redirect (login and logout) to any URL you want (include crazy ones like data:), essentially bypassing the WordPress functions wp_sanitize_redirect() and wp_validate_redirect()
        // Setting this to 3 will make it so that you can only redirect (login and logout) to a local URL (one on the same domain). If you make use of the siteurl or homeurl custom variables, do not set this to 3
        $rul_settings['rul_local_only'] = 1;

        // Allow a POST or GET "redirect_to" variable to take precedence over settings within the plugin
        $rul_settings['rul_allow_post_redirect_override'] = false;

        // Allow a POST or GET logout "redirect_to" variable to take precedence over settings within the plugin
        $rul_settings['rul_allow_post_redirect_override_logout'] = false;

        // Set this to true if you're using a plugin such as Gigya that bypasses the regular WordPress redirect process (and only allow one fixed redirect URL)
        // Then, set that plugin to redirect to http://www.yoursite.com/wp-content/plugins/peters-login-redirect/wplogin_redirect_control.php
        // For more troubleshooting with this setting, make sure the paths are set correctly in wplogin_redirect_control.php
        $rul_settings['rul_use_redirect_controller'] = false;

        // To edit the redirect settings in the WordPress admin panel, users need this capability
        // Typically editors and up have "manage_links" capabilities
        // See http://codex.wordpress.org/Roles_and_Capabilities for more information about out of the box capabilities
        $rul_settings['rul_required_capability'] = 'manage_links';
        
        $rul_settings_from_options_table = rulRedirectFunctionCollection::get_settings_from_options_table();
        
        // Merge the default settings with the settings form the database
        // Limit the settings in case there are ones from the database that are old
        foreach( $rul_settings as $setting_name => $setting_value )
        {
            if( isset( $rul_settings_from_options_table[$setting_name] ) )
            {
                $rul_settings[$setting_name] = $rul_settings_from_options_table[$setting_name];
            }
        }
        if( !$setting )
        {
            return $rul_settings;
        }
        elseif( $setting && isset( $rul_settings[$setting] ) )
        {
            return $rul_settings[$setting];
        }
        else
        {
            return false;
        }
    }
    function get_settings_from_options_table()
    {
        return get_option( 'rul_settings', array() );
    }

    /*
        This extra function is necessary to support the use case where someone was previously logged in
        Thanks to http://wordpress.org/support/topic/97314 for this function
    */
    function redirect_current_user_can($capability, $current_user)
    {
        global $wpdb;

        $roles = get_option($wpdb->prefix . 'user_roles');
        $user_roles = $current_user->{$wpdb->prefix . 'capabilities'};
        $user_roles = array_keys($user_roles, true);
        $role = $user_roles[0];
        $capabilities = $roles[$role]['capabilities'];

        if ( in_array( $capability, array_keys( $capabilities, true) ) ) {
            // check array keys of capabilities for match against requested capability
            return true;
        }
        return false;
    }
    
    /*
        A generic function to return the value mapped to a particular variable
    */
    function rul_get_variable( $variable, $user )
    {
        $variable_value = apply_filters( 'rul_replace_variable', false, $variable, $user );
        if( !$variable_value )
        {
            // Return the permalink of the post ID
            if( 0 === strpos( $variable, 'postid-' ) )
            {
                $post_id = str_replace( 'postid-', '', $variable );
                $permalink = get_permalink( $post_id );
                if( $permalink )
                {
                    $variable_value = $permalink;
                }
            }
            else
            {
                switch( $variable )
                {
                    // Returns the current user's username (only use this if you know they're logged in)
                    case 'username':
                        $variable_value = rawurlencode( $user->user_login );
                        break;
                    // Returns the URL of the WordPress files; see http://codex.wordpress.org/Function_Reference/network_site_url
                    case 'siteurl':
                        $variable_value = network_site_url();
                        break;
                    // Returns the URL of the site, possibly different from where the WordPress files are; see http://codex.wordpress.org/Function_Reference/network_home_url
                    case 'homeurl':
                        $variable_value = network_home_url();
                        break;
                    default:
                        $variable_value = '';
                        break;
                }
            }
        }
        return $variable_value;
    }
    
    /*
        Replaces the syntax [variable]variable_name[/variable] with whatever has been mapped to the variable_name in the rul_get_variable function
    */
    function rul_replace_variable( $string, $user )
    {
        preg_match_all( "/\[variable\](.*?)\[\/variable\]/is", $string, $out );

        foreach( $out[0] as $instance => $full_match )
        {
            $replaced_variable = rulRedirectFunctionCollection::rul_get_variable( $out[1][ $instance ], $user );
            $string = str_replace( $full_match, $replaced_variable, $string );
        }

        return $string;
    }
    /*
        Allow users to be redirected to external URLs as specified by redirect rules
    */
    function rul_trigger_allowed_host( $url )
    {
        global $rul_allowed_hosts;
        $url_parsed = parse_url( $url );
        if( isset( $url_parsed[ 'host' ] ) )
        {
            $rul_allowed_hosts[] = $url_parsed[ 'host' ];
            add_filter( 'allowed_redirect_hosts', array( 'rulRedirectFunctionCollection', 'rul_add_allowed_host' ), 10, 1 );
        }
    }
    function rul_add_allowed_host( $hosts )
    {
        global $rul_allowed_hosts;
        return array_merge( $hosts, $rul_allowed_hosts );
    }
}

// Functions specific to logout redirecting
class rulLogoutFunctionCollection
{
    function logout_redirect()
    {   
        $rul_local_only = rulRedirectFunctionCollection::get_settings( 'rul_local_only' );
        $rul_allow_post_redirect_override_logout = rulRedirectFunctionCollection::get_settings( 'rul_allow_post_redirect_override_logout' );

        $requested_redirect_to = !empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : false;
        if( is_user_logged_in() && ( !$requested_redirect_to || !$rul_allow_post_redirect_override_logout ) )
        {
            $current_user = wp_get_current_user();
            $rul_url = rulLogoutFunctionCollection::get_redirect_url( $current_user, $requested_redirect_to );

            if( $rul_url )
            {
                if( 1 == $rul_local_only )
                {
                    rulRedirectFunctionCollection::rul_trigger_allowed_host( $rul_url );
                    wp_safe_redirect( $rul_url );
                    die();
                }
                elseif( 2 == $rul_local_only )
                {
                    wp_redirect( $rul_url );
                    die();
                }
                else
                {
                    wp_safe_redirect( $rul_url );
                    die();
                }
            }
        }
        return false;
    }
    // Get the logout redirect URL according to defined rules
    // Functionality for user-, role-, and capability-specific redirect rules is available
    // Note that only the "all other users" redirect URL is currently implemented in the UI
    function get_redirect_url( $user, $requested_redirect_to )
    {
        global $wpdb, $rul_db_addresses;
        
        $redirect_to = false;
        
        // Check for an extended custom redirect rule
        $rul_custom_redirect = apply_filters( 'rul_before_user_logout', false, $requested_redirect_to, $user );

        if( $rul_custom_redirect )
        {
            $redirect_to = rulRedirectFunctionCollection::rul_replace_variable( $rul_custom_redirect, $requested_redirect_to, $user );
            return $redirect_to;
        }

        // Check for a redirect rule for this user
        $rul_user = $wpdb->get_var('SELECT rul_url_logout FROM ' . $rul_db_addresses . 
            ' WHERE rul_type = \'user\' AND rul_value = \'' . $user->user_login . '\' LIMIT 1');
        
        if ( $rul_user )
        {
            $redirect_to = rulRedirectFunctionCollection::rul_replace_variable( $rul_user, $user );
            return $redirect_to;
        }

        // Check for an extended custom redirect rule
        $rul_custom_redirect = apply_filters( 'rul_before_role_logout', false, $requested_redirect_to, $user );
        if( $rul_custom_redirect )
        {
            $redirect_to = rulRedirectFunctionCollection::rul_replace_variable( $rul_custom_redirect, $user );
            return $redirect_to;
        }

        // Check for a redirect rule that matches this user's role
        $rul_roles = $wpdb->get_results('SELECT rul_value, rul_url_logout FROM ' . $rul_db_addresses . 
            ' WHERE rul_type = \'role\'', OBJECT);
            
        if( $rul_roles )
        {
            foreach( $rul_roles as $rul_role )
            {
                if( '' != $rul_role->rul_url_logout && isset( $user->{$wpdb->prefix . 'capabilities'}[$rul_role->rul_value] ) )
                {
                    $redirect_to = rulRedirectFunctionCollection::rul_replace_variable( $rul_role->rul_url_logout, $user );
                    return $redirect_to;
                }
            }
        }

        // Check for an extended custom redirect rule
        $rul_custom_redirect = apply_filters( 'rul_before_capability_logout', false, $requested_redirect_to, $user );
        if( $rul_custom_redirect )
        {
            $redirect_to = rulRedirectFunctionCollection::rul_replace_variable( $rul_custom_redirect, $user );
            return $redirect_to;
        }

        // Check for a redirect rule that matches this user's capability
        $rul_levels = $wpdb->get_results( 'SELECT rul_value, rul_url_logout FROM ' . $rul_db_addresses . 
            ' WHERE rul_type = \'level\' ORDER BY rul_order, rul_value', OBJECT );
            
        if( $rul_levels )
        {
            foreach( $rul_levels as $rul_level )
            {
                if( '' != $rul_level->rul_url_logout && rulRedirectFunctionCollection::redirect_current_user_can( $rul_level->rul_value, $user ) )
                {
                    $redirect_to = rulRedirectFunctionCollection::rul_replace_variable( $rul_level->rul_url_logout, $user );
                    return $redirect_to;
                }
            }
        }

        // Check for an extended custom redirect rule
        $rul_custom_redirect = apply_filters( 'rul_before_fallback_logout', false, $requested_redirect_to, $user );
        if( $rul_custom_redirect )
        {
            $redirect_to = rulRedirectFunctionCollection::rul_replace_variable( $rul_custom_redirect, $user );
            return $redirect_to;
        }
        
        // If none of the above matched, look for a rule to apply to all users
        $rul_all = $wpdb->get_var('SELECT rul_url_logout FROM ' . $rul_db_addresses . 
            ' WHERE rul_type = \'all\' LIMIT 1');

        if( $rul_all )
        {
            $redirect_to = rulRedirectFunctionCollection::rul_replace_variable( $rul_all, $user );
            return $redirect_to;
        }
        
        // No rules matched or existed, so just send them to the WordPress admin panel as usual
        return $redirect_to;
    }
}

// Functions for redirecting post-registration
class rulRedirectPostRegistration
{
    function post_registration_wrapper( $requested_redirect_to )
    {
        /*
            Some limitations:
                - Not yet implemented but possible: toggle whether to allow a GET or POST override of the redirect_to variable (currently it is "yes")
                - Not yet possible: Redirect to a non-local URL, due to the fact that the WordPress hook is implemented pre-registration, not post-registration
                - Not yet possible: Username-customized page, since the WordPress hook is implemented pre-registration, not post-registration
        */

        $rul_url = rulRedirectPostRegistration::get_redirect_url( $requested_redirect_to );
        if( $rul_url )
        {
            return $rul_url;
        }
        return $requested_redirect_to;
    }
    
    // Looks up the redirect URL, if any
    function get_redirect_url( $requested_redirect_to )
    {
        global $wpdb, $rul_db_addresses;
        
        $redirect_to = false;
        
        $rul_all = $wpdb->get_var('SELECT rul_url FROM ' . $rul_db_addresses . 
            ' WHERE rul_type = \'register\' LIMIT 1');

        if( $rul_all )
        {
            $redirect_to = rulRedirectFunctionCollection::rul_replace_variable( $rul_all, false );
            return $redirect_to;
        }
        
        // No rule exists
        return $redirect_to;
    }
}

// This function wraps around the main redirect function to determine whether or not to bypass the WordPress local URL limitation
function redirect_wrapper( $redirect_to, $requested_redirect_to, $user )
{
    $rul_local_only = rulRedirectFunctionCollection::get_settings( 'rul_local_only' );
    $rul_allow_post_redirect_override = rulRedirectFunctionCollection::get_settings( 'rul_allow_post_redirect_override' );

    // If they're on the login page, don't do anything
    if( !isset( $user->user_login ) )
    {
        return $redirect_to;
    }

    if( ( admin_url() == $redirect_to && $rul_allow_post_redirect_override ) || !$rul_allow_post_redirect_override )
    {
        $rul_url = redirect_to_front_page( $redirect_to, $requested_redirect_to, $user );
        if( $rul_url )
        {
            if( 1 == $rul_local_only )
            {
                rulRedirectFunctionCollection::rul_trigger_allowed_host( $rul_url );
                return $rul_url;
            }
            elseif( 2 == $rul_local_only )
            {
                wp_redirect( $rul_url );
                die();
            }
            else
            {
                return $rul_url;
            }
        }
    }
    return $redirect_to;
}

// This function sets the URL to redirect to

function redirect_to_front_page( $redirect_to, $requested_redirect_to, $user )
{
    global $wpdb, $rul_db_addresses;

    // Check for an extended custom redirect rule
    $rul_custom_redirect = apply_filters( 'rul_before_user', false, $redirect_to, $requested_redirect_to, $user );
    if( $rul_custom_redirect )
    {
        $redirect_to = rulRedirectFunctionCollection::rul_replace_variable( $rul_custom_redirect, $user );
        return $redirect_to;
    }

    // Check for a redirect rule for this user
    $rul_user = $wpdb->get_var('SELECT rul_url FROM ' . $rul_db_addresses . 
        ' WHERE rul_type = \'user\' AND rul_value = \'' . $user->user_login . '\' LIMIT 1');
    
    if ( $rul_user )
    {
        $redirect_to = rulRedirectFunctionCollection::rul_replace_variable( $rul_user, $user );
        return $redirect_to;
    }

    // Check for an extended custom redirect rule
    $rul_custom_redirect = apply_filters( 'rul_before_role', false, $redirect_to, $requested_redirect_to, $user );
    if( $rul_custom_redirect )
    {
        $redirect_to = rulRedirectFunctionCollection::rul_replace_variable( $rul_custom_redirect, $user );
        return $redirect_to;
    }

    // Check for a redirect rule that matches this user's role
    $rul_roles = $wpdb->get_results('SELECT rul_value, rul_url FROM ' . $rul_db_addresses . 
        ' WHERE rul_type = \'role\'', OBJECT);
        
    if( $rul_roles )
    {
        foreach( $rul_roles as $rul_role )
        {
            if( '' != $rul_role->rul_url && isset( $user->{$wpdb->prefix . 'capabilities'}[$rul_role->rul_value] ) )
            {
                $redirect_to = rulRedirectFunctionCollection::rul_replace_variable( $rul_role->rul_url, $user );
                return $redirect_to;
            }
        }
    }

    // Check for an extended custom redirect rule
    $rul_custom_redirect = apply_filters( 'rul_before_capability', false, $redirect_to, $requested_redirect_to, $user );
    if( $rul_custom_redirect )
    {
        $redirect_to = rulRedirectFunctionCollection::rul_replace_variable( $rul_custom_redirect, $user );
        return $redirect_to;
    }

    // Check for a redirect rule that matches this user's capability
    $rul_levels = $wpdb->get_results('SELECT rul_value, rul_url FROM ' . $rul_db_addresses . 
        ' WHERE rul_type = \'level\' ORDER BY rul_order, rul_value', OBJECT);
        
    if( $rul_levels )
    {
        foreach( $rul_levels as $rul_level )
        {
            if( '' != $rul_level->rul_url && rulRedirectFunctionCollection::redirect_current_user_can ( $rul_level->rul_value, $user ) )
            {
                $redirect_to = rulRedirectFunctionCollection::rul_replace_variable( $rul_level->rul_url, $user );
                return $redirect_to;
            }
        }
    }

    // Check for an extended custom redirect rule
    $rul_custom_redirect = apply_filters( 'rul_before_fallback', false, $redirect_to, $requested_redirect_to, $user );
    if( $rul_custom_redirect )
    {
        $redirect_to = rulRedirectFunctionCollection::rul_replace_variable( $rul_custom_redirect, $user );
        return $redirect_to;
    }
    
    // If none of the above matched, look for a rule to apply to all users
    $rul_all = $wpdb->get_var('SELECT rul_url FROM ' . $rul_db_addresses . 
        ' WHERE rul_type = \'all\' LIMIT 1');

    if( $rul_all )
    {
        $redirect_to = rulRedirectFunctionCollection::rul_replace_variable( $rul_all, $user );
        return $redirect_to;
    }
    
    // No rules matched or existed, so just send them to the WordPress admin panel as usual
    return $redirect_to;
    
}

// Typically this function is used in templates, similarly to the wp_register function
// It returns a link to the administration panel or the one that was custom defined
// If no user is logged in, it returns the "Register" link
// You can specify tags to go around the returned link (or wrap it with no tags); by default this is a list item
// You can also specify whether to print out the link or just return it

function rul_register( $before = '<li>', $after = '</li>', $give_echo = true ) {
    global $current_user;
    
	if ( ! is_user_logged_in() ) {
		if ( get_option('users_can_register') )
			$link = $before . '<a href="' . site_url('wp-login.php?action=register', 'login') . '">' . __('Register', 'peterloginrd') . '</a>' . $after;
		else
			$link = '';
	} else {
        $link = $before . '<a href="' . redirect_to_front_page('', '', $current_user) . '">' . __('Site Admin', 'peterloginrd') . '</a>' . $after;;
	}
    
    if ($give_echo) {
        echo $link;
    }
    else {
        return $link;
    }
}

if (is_admin()) {

    // Returns all option HTML for all usernames in the system except for those supplied to it
    function rul_returnusernames($exclude) {
        global $wpdb;

        $rul_returnusernames = '';
        
        // Build the "not in" part of the MySQL query
        $exclude_users = "'" . implode( "','", $exclude ) . "'";
        
        $rul_userresults = $wpdb->get_results('SELECT user_login FROM ' . $wpdb->users . ' WHERE user_login NOT IN (' . $exclude_users . ') ORDER BY user_login', ARRAY_N);
        
        // Built the option HTML
        if ($rul_userresults) {
            foreach ($rul_userresults as $rul_userresult) {
                $rul_returnusernames .= '<option value="' . $rul_userresult[0] . '">' . $rul_userresult[0] . '</option>';
            }
        }
            
        return $rul_returnusernames;
    }

    // Returns all roles in the system
    function rul_returnrolenames() {
        global $wp_roles;

        $rul_returnrolenames = array();
        foreach (array_keys($wp_roles->role_names) as $rul_rolename) {
            $rul_returnrolenames[$rul_rolename] = $rul_rolename;
        }
        
        return $rul_returnrolenames;   
    }
    
    // Returns option HTML for all roles in the system, except for those supplied to it
    function rul_returnroleoptions($exclude) {
    
        // Relies on a function that just returns the role names
        $rul_rolenames = rul_returnrolenames($exclude);
        
        $rul_returnroleoptions = '';

        // Build the option HTML
        if ($rul_rolenames) {
            foreach ($rul_rolenames as $rul_rolename) {
                if (!isset($exclude[$rul_rolename])) {
                    $rul_returnroleoptions .= '<option value="' . $rul_rolename . '">' . $rul_rolename . '</option>';
                }
            }
        }
        
        return $rul_returnroleoptions;
    
    }
    
    // Returns all level names in the system
    function rul_returnlevelnames() {
        global $wp_roles;
        
        $rul_returnlevelnames = array();
        
        // Builds the array of level names by combing through each of the roles and listing their levels
        foreach ($wp_roles->roles as $wp_role) {
            $rul_returnlevelnames = array_unique((array_merge($rul_returnlevelnames, array_keys($wp_role['capabilities']))));
        }
        
        // Sort the level names in alphabetical order
        sort($rul_returnlevelnames);
        
        return $rul_returnlevelnames;
        
    }
    
    // Returns option HTML for all levels in the system, except for those supplied to it
    function rul_returnleveloptions($exclude) {
        
        // Relies on a function that just returns the level names
        $rul_levelnames = rul_returnlevelnames();
        
        $rul_returnleveloptions = '';
        
        // Build the option HTML
        foreach ($rul_levelnames as $rul_levelname) {
            if (!isset($exclude[$rul_levelname])) {
                $rul_returnleveloptions .= '<option value="' . $rul_levelname . '">' . $rul_levelname . '</option>';
            }
        }
        
        return $rul_returnleveloptions;
        
    }
    
    // Processes the rule updates per user
    function rul_submit_username($usernames, $addresses) {
        global $wpdb, $rul_db_addresses;

        // Open the informational div
        $rul_process_submit = '<div id="message" class="updated fade">';
        
        // Code for closing the informational div
        $rul_process_close = '</div>';
        
        // ----------------------------------
        // Process the rule changes
        // ----------------------------------

        if($usernames && $addresses) {
            $rul_submit_success = true;
            $rul_usernames_updated = array();
            $rul_username_keys = array_keys($usernames);
            $rul_username_loop = 0;
            
            // Loop through all submitted usernames
            foreach( $usernames as $username ) {
                $i = $rul_username_keys[$rul_username_loop];

                if ( username_exists($username) ) {

                    // Check to see whether it matches the "local URL" test
                    $address = rul_safe_redirect($addresses[$i]);
                
                    if (!$address) {
                        $rul_submit_success = false;
                        $rul_process_submit .= '<p><strong>****' .__('ERROR: Non-local or invalid URL submitted for user ','peterloginrd')  . $username .  '****</strong></p>';
                    }
                    
                    
                    else {
                        // Update the existing entry or insert a new one
                        $rul_update_username = $wpdb->query('REPLACE INTO ' . $rul_db_addresses . ' SET rul_url = \'' . $address . '\', rul_type = \'user\', rul_value = \'' . $username . '\'');
                        
                        if (!$rul_update_username) {
                            $rul_submit_success = false;
                            $rul_process_submit .= '<p><strong>****' .__('ERROR: Unknown error updating user-specific URL for user ','peterloginrd') . $username . '****</strong></p>';
                        }
                    }
                    
                    // Make a note that we've updated this username
                    $rul_usernames_updated[] = $username;
                }
                elseif ($username != -1) {
                    $rul_submit_success = false;
                    $rul_process_submit .= '<p><strong>****' .__('ERROR: Non-existent username submitted ','peterloginrd') .'****</strong></p>';
                }
                
                ++$rul_username_loop;
            }
            
            // Prepare the "not in" MySQL code
            $rul_usernames_notin = "'" . implode( "','", $rul_usernames_updated ) . "'";            
            
            // Delete all username rules in the database that weren't updated (in other words, the user unchecked the box next to it)
            $wpdb->query('DELETE FROM ' . $rul_db_addresses . ' WHERE rul_type = \'user\' AND rul_value NOT IN (' . $rul_usernames_notin . ')');
            
            if ($rul_submit_success) {
                $rul_process_submit .= '<p>'.__('Successfully updated user-specific URLs','peterloginrd').'</p>';
            }
        }

        // Close the informational div
        $rul_process_submit .= $rul_process_close;
        
        // We've made it this far, so success!
        return $rul_process_submit;
    }

    // Processes the rule updates per role
    function rul_submit_role($roles, $addresses) {
        global $wpdb, $rul_db_addresses;

        // Open the informational div
        $rul_process_submit = '<div id="message" class="updated fade">';
        
        // Code for closing the informational div
        $rul_process_close = '</div>';
        
        // ----------------------------------
        // Process the rule changes
        // ----------------------------------

        if($roles && $addresses) {
            $rul_submit_success = true;
            $rul_roles_updated = array();
            $rul_role_keys = array_keys($roles);
            $rul_role_loop = 0;
            
            // Loop through all submitted roles
            foreach( $roles as $role ) {
                $i = $rul_role_keys[$rul_role_loop];
                
                // Get a list of roles in the system so that we can verify that a valid role was submitted
                $rul_existing_rolenames = rul_returnrolenames();
                if ( isset($rul_existing_rolenames[$role]) ) {

                    // Check to see whether it matches the "local URL" test
                    $address = rul_safe_redirect($addresses[$i]);
                
                    if (!$address) {
                        $rul_submit_success = false;
                        $rul_process_submit .= '<p><strong>****' .__('ERROR: Non-local or invalid URL submitted for role ','peterloginrd') . $role . '****</strong></p>';
                    }
                    
                    else {
                        // Update the existing entry or insert a new one
                        $rul_update_role = $wpdb->query('REPLACE INTO ' . $rul_db_addresses . ' SET rul_url = \'' . $address . '\', rul_type = \'role\', rul_value = \'' . $role . '\'');
                        
                        if (!$rul_update_role) {
                            $rul_submit_success = false;
                            $rul_process_submit .= '<p><strong>****' .__('ERROR: Unknown error updating role-specific URL for role ','peterloginrd') . $role . '****</strong></p>';
                        }
                    }
                    
                    // Make a note that this role name was updated
                    $rul_roles_updated[] = $role;
                }
                elseif ($role != -1) {
                    $rul_submit_success = false;
                    $rul_process_submit .= '<p><strong>****' .__('ERROR: Non-existent role submitted ','peterloginrd') .'****</strong></p>';
                }
                
                ++$rul_role_loop;
            }
            
            // Built the "not in" MySQL query
            $rul_roles_notin = "'" . implode( "','", $rul_roles_updated ) . "'";            
            
            // Delete all role rules in the database that weren't updated (in other words, the user unchecked the box next to it)
            $wpdb->query('DELETE FROM ' . $rul_db_addresses . ' WHERE rul_type = \'role\' AND rul_value NOT IN (' . $rul_roles_notin . ')');
            
            if ($rul_submit_success) {
                $rul_process_submit .= '<p>'.__('Successfully updated role-specific URLs','peterloginrd') .'</p>';
            }
        }

        // Close the informational div
        $rul_process_submit .= $rul_process_close;
        
        // We've made it this far, so success!
        return $rul_process_submit;
    }
    
    function rul_submit_level($levels, $orders, $addresses) {
        global $wpdb, $rul_db_addresses;

        // Open the informational div
        $rul_process_submit = '<div id="message" class="updated fade">';
        
        // Code for closing the informational div
        $rul_process_close = '</div>';
        
        // ----------------------------------
        // Process the rule changes
        // ----------------------------------

        if($levels && $addresses) {
            $rul_submit_success = true;
            $rul_levels_updated = array();
            $rul_level_keys = array_keys($levels);
            $rul_level_loop = 0;
            
            // Loop through all submitted levels
            foreach( $levels as $level ) {
                $i = $rul_level_keys[$rul_level_loop];
                
                // Build the array of existing level names
                $rul_existing_levelnames = array_flip(rul_returnlevelnames());
                
                // The order should only be between 0 and 99
                $order = abs(intval($orders[$i]));
                if ($order > 99) {
                    $order = 0;
                }

                if ( isset($rul_existing_levelnames[$level]) ) {

                    // Check to see whether it passes the "local URL" test
                    $address = rul_safe_redirect($addresses[$i]);
                
                    if (!$address) {
                        $rul_submit_success = false;
                        $rul_process_submit .= '<p><strong>****' .__('ERROR: Non-local or invalid URL submitted for level ','peterloginrd')  . $level . '****</strong></p>';
                    }
                    
                    else {
                        // Update the existing entry or insert a new one
                        $rul_update_level = $wpdb->query('REPLACE INTO ' . $rul_db_addresses . ' SET rul_url = \'' . $address . '\', rul_type = \'level\', rul_value = \'' . $level . '\', rul_order = ' . $order);
                        
                        if (!$rul_update_level) {
                            $rul_submit_success = false;
                            $rul_process_submit .= '<p><strong>****' .__('ERROR: Unknown error updating level-specific URL for level ','peterloginrd')  . $level . '****</strong></p>';
                        }
                    }
                    
                    // Make a note that this level was updated
                    $rul_levels_updated[] = $level;
                }
                elseif ($level != -1) {
                    $rul_submit_success = false;
                    $rul_process_submit .= '<p><strong>****' .__('ERROR: Non-existent level submitted ','peterloginrd') .'****</strong></p>';
                }
                
                ++$rul_level_loop;
            }
            
            // Build the "not in" MySQL code
            $rul_levels_notin = "'" . implode( "','", $rul_levels_updated ) . "'";
            
            // Delete all level rules in the database that weren't updated (in other words, the user unchecked the box next to it)
            $wpdb->query('DELETE FROM ' . $rul_db_addresses . ' WHERE rul_type = \'level\' AND rul_value NOT IN (' . $rul_levels_notin . ')');
            
            if ($rul_submit_success) {
                $rul_process_submit .= '<p>'.__('Successfully updated level-specific URLs','peterloginrd').'</p>';
            }
        }

        // Close the informational div
        $rul_process_submit .= $rul_process_close;
        
        // We've made it this far, so success!
        return $rul_process_submit;
    }
    
    function rul_submit_all( $update_or_delete, $address, $address_logout )
    {
        global $wpdb, $rul_db_addresses;
        
        $address = trim( $address );
        $address_logout = trim( $address_logout );

        // Open the informational div
        $rul_process_submit = '<div id="message" class="updated fade">';
        
        // Code for closing the informational div
        $rul_process_close = '</div>';
        
        // ----------------------------------
        // Process the rule changes
        // ----------------------------------
        
        // Since we never actually, remove the "all" entry, here we just make its value empty
        if( $update_or_delete == 'delete' )
        {
            $update = $wpdb->update (
                $rul_db_addresses,
                array( 'rul_url' => '', 'rul_url_logout' => '' ),
                array( 'rul_type' => 'all' )
            );
            
            if( $update === false )
            {
                $rul_process_submit .= '<p><strong>****' .__('ERROR: Unknown database problem removing URL for &#34;all other users&#34; ','peterloginrd') .'****</strong></p>';
            }
            else
            {
                $rul_process_submit .= '<p>'.__('Successfully removed URL for &#34;all other users&#34; ','peterloginrd') .'</p>';
            }
        }
        
        elseif( $update_or_delete == 'update' )
        {
            $address_safe = rul_safe_redirect( $address );
            $address_safe_logout = rul_safe_redirect( $address_logout );
            
            if( ( '' != $address && !$address_safe ) || ( '' != $address_logout && !$address_safe_logout ) )
            {
                $rul_process_submit .= '<p><strong>****' .__('ERROR: Non-local or invalid URL submitted ','peterloginrd') .'****</strong></p>';
            }
            
            else
            {
                $update = $wpdb->update(
                    $rul_db_addresses,
                    array( 'rul_url' => $address_safe, 'rul_url_logout' => $address_safe_logout ),
                    array( 'rul_type' => 'all' )
                );

                if( $update === false )
                {
                    $rul_process_submit .= '<p><strong>****' .__('ERROR: Unknown database problem updating URL for &#34;all other users&#34; ','peterloginrd') .'****</strong></p>';
                }
                else
                {
                    $rul_process_submit .= '<p>'.__('Successfully updated URL for &#34;all other users&#34;','peterloginrd') .'</p>';
                }
            }
        }

        // Close the informational div
        $rul_process_submit .= $rul_process_close;
        
        // We've made it this far, so success!
        return $rul_process_submit;
    }
    
    function rul_submit_register( $update_or_delete, $address )
    {
        global $wpdb, $rul_db_addresses;
        
        $address = trim( $address );

        // Open the informational div
        $rul_process_submit = '<div id="message" class="updated fade">';
        
        // Code for closing the informational div
        $rul_process_close = '</div>';
        
        // ----------------------------------
        // Process the rule changes
        // ----------------------------------
        
        // Since we never actually, remove the "register" entry, here we just make its value empty
        if( $update_or_delete == 'delete' )
        {
            $update = $wpdb->update (
                $rul_db_addresses,
                array( 'rul_url' => '' ),
                array( 'rul_type' => 'register' )
            );
            
            if ( $update === false )
            {
                $rul_process_submit .= '<p><strong>****' . __( 'ERROR: Unknown database problem removing URL for &#34;post-registration&#34; ','peterloginrd') .'****</strong></p>';
            }
            else {
                $rul_process_submit .= '<p>' . __( 'Successfully removed URL for &#34;post-registration&#34; ', 'peterloginrd' ) .'</p>';
            }
        }
        
        elseif( $update_or_delete == 'update' )
        {
            $address_safe = rul_safe_redirect( $address );

            if( ( '' != $address && !$address_safe ) )
            {
                $rul_process_submit .= '<p><strong>****' . __( 'ERROR: Non-local or invalid URL submitted ', 'peterloginrd' ) . '****</strong></p>';
            }
            
            else
            {
                $update = $wpdb->update(
                    $rul_db_addresses,
                    array( 'rul_url' => $address_safe ),
                    array( 'rul_type' => 'register' )
                );

                if( $update === false )
                {
                    $rul_process_submit .= '<p><strong>****' .__('ERROR: Unknown database problem updating URL for &#34;post-registration&#34; ','peterloginrd') .'****</strong></p>';
                }
                else
                {
                    $rul_process_submit .= '<p>'.__('Successfully updated URL for &#34;post-registration&#34;','peterloginrd') .'</p>';
                }
            }
        }

        // Close the informational div
        $rul_process_submit .= $rul_process_close;
        
        // We've made it this far, so success!
        return $rul_process_submit;
    }
    
    
    // Process submitted information to update plugin settings
    function rul_submit_settings()
    {
        $rul_settings = rulRedirectFunctionCollection::get_settings();
        foreach( $rul_settings as $setting_name => $setting_value )
        {
            if( isset( $_POST[$setting_name] ) )
            {
                $rul_settings[$setting_name] = $_POST[$setting_name];
            }
        }
        update_option( 'rul_settings', $rul_settings );
        $rul_process_submit = '<div id="message" class="updated fade">';
        $rul_process_submit .= '<p>' . __( 'Successfully updated plugin settings', 'peterloginrd' ) . '</p>';
        $rul_process_submit .= '</div>';
        return $rul_process_submit;
    }

    /*
    Stolen from wp_safe_redirect, which validates the URL
    */

    function rul_safe_redirect( $location )
    {
        $rul_local_only = rulRedirectFunctionCollection::get_settings( 'rul_local_only' );

        if( 2 == $rul_local_only || 1 == $rul_local_only )
        {
            return $location;
        }
        
        // Need to look at the URL the way it will end up in wp_redirect()
        $location = wp_sanitize_redirect( $location );

        // browsers will assume 'http' is your protocol, and will obey a redirect to a URL starting with '//'
        if( substr( $location, 0, 2 ) == '//' )
        {
            $location = 'http:' . $location;
        }
        
        // In php 5 parse_url may fail if the URL query part contains http://, bug #38143
        $test = ( $cut = strpos($location, '?') ) ? substr( $location, 0, $cut ) : $location;

        $lp  = parse_url( $test );
        $wpp = parse_url( get_option( 'home' ) );

        $allowed_hosts = (array) apply_filters('allowed_redirect_hosts', array($wpp['host']), isset($lp['host']) ? $lp['host'] : '');

        if ( isset( $lp['host'] ) && ( !in_array( $lp['host'], $allowed_hosts ) && $lp['host'] != strtolower( $wpp['host'] ) ) )
        {
    		return false;
        }
        else
        {
            return $location;
        }
    }
    
    // This is the Settings > Login/logout redirects menu
    function rul_optionsmenu()
    {
        global $wpdb, $rul_db_addresses;

        // Upgrade check here because it's the only place we know they will visit
        rul_upgrade();
        
        $rul_process_submit = '';
        
        // Process submitted information to update redirect rules
        if( isset( $_POST['rul_usernamesubmit'] ) )
        {
            $rul_process_submit = rul_submit_username( $_POST['rul_username'], $_POST['rul_usernameaddress'] );
        }
        elseif( isset( $_POST['rul_rolesubmit'] ) )
        {
            $rul_process_submit = rul_submit_role( $_POST['rul_role'], $_POST['rul_roleaddress'] );
        }
        elseif( isset( $_POST['rul_levelsubmit'] ) )
        {
            $rul_process_submit = rul_submit_level( $_POST['rul_level'], $_POST['rul_levelorder'], $_POST['rul_leveladdress'] );
        }
        elseif( isset( $_POST['rul_allupdatesubmit'] ) )
        {
            $rul_process_submit = rul_submit_all( 'update', $_POST['rul_all'], $_POST['rul_all_logout'] );
        }
        elseif( isset( $_POST['rul_alldeletesubmit'] ) )
        {
            $rul_process_submit = rul_submit_all( 'delete', $_POST['rul_all'], $_POST['rul_all_logout'] );
        }
        elseif( isset( $_POST['rul_registerupdatesubmit'] ) )
        {
            $rul_process_submit = rul_submit_register( 'update', $_POST['rul_register'] );
        }
        elseif( isset( $_POST['rul_registerdeletesubmit'] ) )
        {
            $rul_process_submit = rul_submit_register( 'delete', $_POST['rul_register'] );
        }
        elseif( isset( $_POST['rul_settingssubmit'] ) )
        {
            $rul_process_submit = rul_submit_settings();
        }
        
        // Settings that can be updated
        $rul_settings = rulRedirectFunctionCollection::get_settings();
        
        // -----------------------------------
        // Get the existing rules
        // -----------------------------------
        
        $rul_rules = $wpdb->get_results('SELECT rul_type, rul_value, rul_url, rul_url_logout, rul_order FROM ' . $rul_db_addresses . ' ORDER BY rul_type, rul_order, rul_value', ARRAY_N);

        $rul_usernamevalues = '';
        $rul_rolevalues = '';
        $rul_levelvalues = '';
        $rul_usernames_existing = array();
        $rul_roles_existing = array();
        $rul_levels_existing = array();
        
        if( $rul_rules )
        {
        
            $i = 0;
            $i_user = 0;
            $i_role = 0;
            $i_level = 0;
            
            while ($i < count($rul_rules)) {

                list($rul_type, $rul_value, $rul_url, $rul_url_logout, $rul_order) = $rul_rules[$i];

                // Specific users
                if( $rul_type == 'user' )
                {
                    $rul_usernamevalues .= '<tr>';
                    $rul_usernamevalues .= '<td><p><input type="checkbox" name="rul_username[' . $i_user . ']" value="' . $rul_value . '" checked="checked" /> ' . $rul_value . '</p></td>';
                    $rul_usernamevalues .= '<td><p><input type="text" size="90" maxlength="500" name="rul_usernameaddress[' . $i_user . ']" value="' . $rul_url . '" /></p></td>';
                    $rul_usernamevalues .= '</tr>';
                    
                    $rul_usernames_existing[] = $rul_value;
                    
                    ++$i_user;
                }
                
                elseif( $rul_type == 'role' )
                {
                
                    $rul_rolevalues .= '<tr>';
                    $rul_rolevalues .= '<td><p><input type="checkbox" name="rul_role[' . $i_role . ']" value="' . $rul_value . '" checked="checked" /> ' . $rul_value . '</p></td>';
                    $rul_rolevalues .= '<td><p><input type="text" size="90" maxlength="500" name="rul_roleaddress[' . $i_role . ']" value="' . $rul_url . '" /></p></td>';
                    $rul_rolevalues .= '</tr>';
                    
                    $rul_roles_existing[$rul_value] = '';
                    
                    ++$i_role;
                }
                elseif( $rul_type == 'level' )
                {
                    $rul_levelvalues .= '<tr>';
                    $rul_levelvalues .= '<td><p><input type="checkbox" name="rul_level[' . $i_level . ']" value="' . $rul_value . '" checked="checked" /> ' . $rul_value . '</p></td>';
                    $rul_levelvalues .= '<td><p><input type="text" size="2" maxlength="2" name="rul_levelorder[' . $i_level . ']" value="' . $rul_order . '" /></p></td>';
                    $rul_levelvalues .= '<td><p><input type="text" size="90" maxlength="500" name="rul_leveladdress[' . $i_level . ']" value="' . $rul_url . '" /></p></td>';
                    $rul_levelvalues .= '</tr>';

                    $rul_levels_existing[$rul_value] = '';
                    
                    ++$i_level;
                }
                elseif( $rul_type == 'all' )
                {
                    $rul_allvalue = $rul_url;
                    $rul_allvalue_logout = $rul_url_logout;
                }
                elseif( $rul_type == 'register' )
                {
                    $rul_registervalue = $rul_url;
                }
                ++$i;
            }

        }
?>
    <div class="wrap">
        <h2><?php _e('Manage redirect rules', 'peterloginrd' ); ?></h2>
        <?php print $rul_process_submit; ?>
        <p><?php _e('Define custom URLs to which different users, users with specific roles, users with specific levels, and all other users will be redirected upon login.', 'peterloginrd' ); ?></p>
        <p><?php _e('Define a custom URL to which all users will be redirected upon logout', 'peterloginrd' ); ?></p>
        <p><?php _e('Note that you can use the syntax <strong>[variable]username[/variable]</strong> in your URLs so that the system will build a dynamic URL upon each login, replacing that text with the users username.', 'peterloginrd' ); ?></p>

        <h3><?php _e('Specific users', 'peterloginrd' ); ?></h3>
        <form name="rul_usernameform" action="<?php print '?page=' . basename(__FILE__); ?>" method="post">
        <table class="widefat">
            <tr>
                <th><?php _e('Username', 'peterloginrd' ); ?></th>
                <th><?php _e('URL', 'peterloginrd' ); ?></th>
            </tr>
            <?php print $rul_usernamevalues; ?>
            
        </table>
        <p><?php _e('Add:', 'peterloginrd' ); ?> 
            <select name="rul_username[<?php print $i_user; ?>]" >
                <option value="-1"><?php _e('Select a username', 'peterloginrd' ); ?></option>
                <?php print rul_returnusernames($rul_usernames_existing); ?>
            </select>
            <br /><?php _e('URL:', 'peterloginrd' ); ?> <input type="text" size="90" maxlength="500" name="rul_usernameaddress[<?php print $i_user; ?>]" />
        </p>
        <p class="submit"><input type="submit" name="rul_usernamesubmit" value="<?php _e('Update', 'peterloginrd' ); ?>" /></p>
        </form>
            
        <h3><?php _e('Specific roles', 'peterloginrd' ); ?></h3>
        <form name="rul_roleform" action="<?php print '?page=' . basename(__FILE__); ?>" method="post">
        <table class="widefat">
            <tr>
                <th><?php _e('Role', 'peterloginrd' ); ?></th>
                <th><?php _e('URL', 'peterloginrd' ); ?></th>
            </tr>
            <?php print $rul_rolevalues; ?>
            
        </table>
        <p><?php _e('Add:', 'peterloginrd' ); ?> 
            <select name="rul_role[<?php print $i_role; ?>]" >
                <option value="-1"><?php _e('Select a role', 'peterloginrd' ); ?></option>
                <?php print rul_returnroleoptions($rul_roles_existing); ?>
            </select>
            <br /><?php _e('URL:', 'peterloginrd' ); ?>  <input type="text" size="90" maxlength="500" name="rul_roleaddress[<?php print $i_role; ?>]" />
        </p>
        <p class="submit"><input type="submit" name="rul_rolesubmit" value="<?php _e( 'Update', 'peterloginrd' ); ?>" /></p>
        </form> 
 
        <h3><?php _e('Specific levels', 'peterloginrd' ); ?></h3>
        <form name="rul_levelform" action="<?php print '?page=' . basename(__FILE__); ?>" method="post">
        <table class="widefat">
            <tr>
                <th><?php _e('Level', 'peterloginrd' ); ?></th>
                <th><?php _e('Order', 'peterloginrd' ); ?></th>
                <th><?php _e('URL', 'peterloginrd' ); ?></th>
            </tr>
            <?php print $rul_levelvalues; ?>
            
        </table>
        <p><?php _e('Add:', 'peterloginrd' ); ?> 
            <select name="rul_level[<?php print $i_level; ?>]" >
                <option value="-1"><?php _e('Select a level', 'peterloginrd' ); ?></option>
                <?php print rul_returnleveloptions($rul_levels_existing); ?>
            </select>
            <br /><?php _e('Order:', 'peterloginrd' ); ?> <input type="text" size="2" maxlength="2" name="rul_levelorder[<?php print $i_level; ?>]" />
            <br /><?php _e('URL:', 'peterloginrd' ); ?> <input type="text" size="90" maxlength="500" name="rul_leveladdress[<?php print $i_level; ?>]" />
        </p>
        <p class="submit"><input type="submit" name="rul_levelsubmit" value="<?php _e('Update', 'peterloginrd' ); ?>" /></p>
        </form> 
        
        <h3><?php _e( 'All other users', 'peterloginrd' ); ?></h3>
        <form name="rul_allform" action="<?php '?page=' . basename(__FILE__); ?>" method="post">
            <p><?php _e('URL:', 'peterloginrd' ) ?> <input type="text" size="90" maxlength="500" name="rul_all" value="<?php print $rul_allvalue; ?>" /></p>
            <p><?php _e('Logout URL:', 'peterloginrd' ) ?> <input type="text" size="90" maxlength="500" name="rul_all_logout" value="<?php print $rul_allvalue_logout; ?>" /></p>
            <p class="submit"><input type="submit" name="rul_allupdatesubmit" value="<?php _e('Update', 'peterloginrd' ); ?>" /> <input type="submit" name="rul_alldeletesubmit" value="<?php _e('Delete', 'peterloginrd' ); ?>" /></p>
        </form>
        
        <hr />
        
        <h3><?php _e( 'Post-registration', 'peterloginrd' ); ?></h3>
        <form name="rul_registerform" action="<?php '?page=' . basename(__FILE__); ?>" method="post">
            <p><?php _e( 'URL:', 'peterloginrd' ) ?> <input type="text" size="90" maxlength="500" name="rul_register" value="<?php print $rul_registervalue; ?>" /></p>
            <p class="submit"><input type="submit" name="rul_registerupdatesubmit" value="<?php _e( 'Update', 'peterloginrd' ); ?>" /> <input type="submit" name="rul_registerdeletesubmit" value="<?php _e( 'Delete', 'peterloginrd' ); ?>" /></p>
        </form>
        
        <hr />
        
        <h3><?php _e( 'Customize plugin settings', 'peterloginrd' ); ?></h3>
        <form name="rul_settingsform" action="<?php print '?page=' . basename(__FILE__); ?>" method="post">
        <table class="widefat">
        <tr>
            <td>
                <p><strong><?php _e( 'Redirect restrictions', 'peterloginrd' ); ?></strong></p>
            </td>
            <td>
                <select name="rul_local_only">
                    <option value="1"<?php if( 1 == $rul_settings['rul_local_only'] ) print ' selected="selected"'; ?>><?php _e( 'Any http or https URL', 'peterloginrd' ); ?></option>
                    <option value="2"<?php if( 2 == $rul_settings['rul_local_only'] ) print ' selected="selected"'; ?>><?php _e( 'Any URL', 'peterloginrd' ); ?></option>
                    <option value="3"<?php if( 3 == $rul_settings['rul_local_only'] ) print ' selected="selected"'; ?>><?php _e( 'Any URL on the same domain', 'peterloginrd' ); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                <p><strong><?php _e( 'Allow a POST or GET &#34;redirect_to&#34; variable to take redirect precedence', 'peterloginrd' ); ?></strong></p>
            </td>
            <td>
                <select name="rul_allow_post_redirect_override">
                    <option value="1"<?php if( $rul_settings['rul_allow_post_redirect_override'] ) print ' selected="selected"'; ?>><?php _e( 'Yes', 'peterloginrd' ); ?></option>
                    <option value="0"<?php if( !$rul_settings['rul_allow_post_redirect_override'] ) print ' selected="selected"'; ?>><?php _e( 'No', 'peterloginrd' ); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                <p><strong><?php _e( 'Allow a POST or GET &#34;redirect_to&#34; logout variable to take redirect precedence', 'peterloginrd' ); ?></strong></p>
            </td>
            <td>
                <select name="rul_allow_post_redirect_override_logout">
                    <option value="1"<?php if( $rul_settings['rul_allow_post_redirect_override_logout'] ) print ' selected="selected"'; ?>><?php _e( 'Yes', 'peterloginrd' ); ?></option>
                    <option value="0"<?php if( !$rul_settings['rul_allow_post_redirect_override_logout'] ) print ' selected="selected"'; ?>><?php _e( 'No', 'peterloginrd' ); ?></option>
                </select>
            </td>
        </tr>
        
        <tr>
            <td>
                <p><strong><?php _e( sprintf( 'Use external redirect file. Set this to &#34;Yes&#34; if you are using a plugin such as Gigya that bypasses the regular WordPress redirect process (and allows only one fixed redirect URL). Then, set the redirect URL to %s', '<br />http://www.yoursite.com/wp-content/plugins/peters-login-redirect/wplogin_redirect_control.php' ), 'peterloginrd' ); ?></strong></p>
            </td>
            <td>
                <select name="rul_use_redirect_controller">
                    <option value="1"<?php if( $rul_settings['rul_use_redirect_controller'] ) print ' selected="selected"'; ?>><?php _e( 'Yes', 'peterloginrd' ); ?></option>
                    <option value="0"<?php if( !$rul_settings['rul_use_redirect_controller'] ) print ' selected="selected"'; ?>><?php _e( 'No', 'peterloginrd' ); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                <p><strong><?php _e( 'Permission level required to edit redirect URLs', 'peterloginrd' ); ?></strong></p>
            </td>
            <td>
                <select name="rul_required_capability">
                    <?php
                        $rul_levelnames = rul_returnlevelnames();
                        // Build the option HTML
                        foreach( $rul_levelnames as $rul_levelname )
                        {
                            print '<option value="' . $rul_levelname . '"';
                            if( $rul_levelname == $rul_settings['rul_required_capability'] )
                            {
                                print ' selected="selected"';
                            }
                            print '>' . $rul_levelname . '</option>';
                        }
                    ?>
                </select>
            </td>
        </tr>
        </table>
        <p class="submit"><input name="rul_settingssubmit" type="submit" value="<?php _e( 'Update', 'peterloginrd' ); ?>" /></p>
        </form>
    </div>
<?php
    } // close rul_optionsmenu()
    
    /*
        Add and remove database tables when installing and uninstalling
    */

    // Perform upgrade functions
    // Some newer operations are duplicated from rul_install() as there's no guarantee that the user will follow a specific upgrade procedure
    function rul_upgrade()
    {
        global $wpdb, $rul_version, $rul_db_addresses;

        // Turn version into an integer for comparisons
        $current_version = intval( str_replace( '.', '', get_option( 'rul_version' ) ) );

        if( $current_version < 253 )
        {
            // Allow NULL values for non-essential fields
            $wpdb->query( "ALTER TABLE '$rul_db_addresses' CHANGE `rul_value` `rul_value` varchar(255) NULL default NULL" );
            $wpdb->query( "ALTER TABLE '$rul_db_addresses' CHANGE `rul_url` `rul_url` LONGTEXT NULL default NULL" );
            $wpdb->query( "ALTER TABLE '$rul_db_addresses' CHANGE `rul_url_logout` `rul_url_logout` LONGTEXT NULL default NULL" );
        }

        if( $current_version < 250 )
        {
            // Insert the "on-register" redirect entry
            
            $wpdb->query( "ALTER TABLE '$rul_db_addresses' CHANGE `rul_type` `rul_type` ENUM( 'user', 'role', 'level', 'all', 'register' ) NOT NULL" );
            $wpdb->insert( $rul_db_addresses,
                array( 'rul_type' => 'register' )
            );
        }

        if( $current_version < 220 )
        {
            $wpdb->query( "ALTER TABLE '$rul_db_addresses' ADD `rul_url_logout` LONGTEXT NOT NULL default '' AFTER `rul_url`" );
        }
        
        if( $current_version != intval( str_replace( '.', '', $rul_version ) ) )
        {
            // Add the version number to the database
            delete_option( 'rul_version' );
            add_option( 'rul_version', $rul_version, '', 'no' );
        }
    }
    function rul_install()
    {
        global $wpdb, $rul_db_addresses, $rul_version;
        
        // Add the table to hold group information and moderator rules
        if( $rul_db_addresses != $wpdb->get_var("SHOW TABLES LIKE '$rul_db_addresses'") )
        {
            $sql = "CREATE TABLE $rul_db_addresses (
            `rul_type` enum('user','role','level','all','register') NOT NULL,
            `rul_value` varchar(255) NULL default NULL,
            `rul_url` LONGTEXT NULL default NULL,
            `rul_url_logout` LONGTEXT NULL default NULL,
            `rul_order` int(2) NOT NULL default '0',
            UNIQUE KEY `rul_type` (`rul_type`,`rul_value`)
            )";

            $wpdb->query($sql);
            
            // Insert the "all" redirect entry
            $wpdb->insert( $rul_db_addresses,
                array( 'rul_type' => 'all' )
            );

            // Insert the "on-register" redirect entry
            $wpdb->insert( $rul_db_addresses,
                array( 'rul_type' => 'register' )
            );

            // Set the version number in the database
            add_option( 'rul_version', $rul_version, '', 'no' );
        }
        
        rul_upgrade();
    }

    function rul_uninstall()
    {
        global $wpdb, $rul_db_addresses;
        
        // Remove the table we created
        if( $rul_db_addresses == $wpdb->get_var('SHOW TABLES LIKE \'' . $rul_db_addresses . '\'') )
        {
            $sql = 'DROP TABLE ' . $rul_db_addresses;
            $wpdb->query($sql);
        }
        
        delete_option( 'rul_version' );
        delete_option( 'rul_settings' );
    }

    function rul_addoptionsmenu()
    {
        $rul_required_capability = rulRedirectFunctionCollection::get_settings( 'rul_required_capability' );
    	add_options_page( 'Login/logout redirects', 'Login/logout redirects', $rul_required_capability, 'wplogin_redirect.php', 'rul_optionsmenu' );
    }

    add_action( 'admin_menu', 'rul_addoptionsmenu', 1 );
}

register_activation_hook( __FILE__, 'rul_install' );
register_uninstall_hook( __FILE__, 'rul_uninstall' );
if( !rulRedirectFunctionCollection::get_settings( 'rul_use_redirect_controller' ) )
{
    add_filter( 'login_redirect', 'redirect_wrapper', 10, 3 );
}
add_filter( 'registration_redirect', array( 'rulRedirectPostRegistration', 'post_registration_wrapper' ), 10, 2 );
add_action( 'wp_logout', array( 'rulLogoutFunctionCollection', 'logout_redirect' ), 10 );
?>