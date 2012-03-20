=== Plugin Name ===
Contributors: pkthree
Donate link: http://www.theblog.ca
Tags: login, logout, redirect, admin, administration, dashboard, users, authentication
Requires at least: 2.7
Tested up to: 3.3
Stable tag: trunk

Redirect users to different locations after logging in and logging out.

== Description ==

Define a set of redirect rules for specific users, users with specific roles, users with specific capabilities, and a blanket rule for all other users (logout redirects in this plugin support only this blanket rule). Also, set a redirect URL for post-registration. This is all managed in Settings > Login/logout redirects.

You can use the syntax **[variable]username[/variable]** in your URLs so that the system will build a dynamic URL upon each login, replacing that text with the user's username. In addition to username, there is "homeurl", "siteurl", and "postid-23" and you can also add your own custom URL "variables". See Other Notes / How to Extend for documentation.

If you're using a plugin such as Gigya that bypasses the regular WordPress login redirect process (and only allows one fixed redirect URL), set that plugin to redirect to wp-content/plugins/peters-login-redirect/wplogin_redirect_control.php and set the $rul_use_redirect_controller setting to "true" in the main plugin file.

You can add your own code logic before and between any of the plugin's normal redirect checks if needed. See Other Notes / How to Extend for documentation. Some examples include: redirecting the user based on their IP address; and redirect users to a special page on first login.

This plugin also includes a function `rul_register` that acts the same as the `wp_register` function you see in templates (typically producing the Register or Site Admin links in the sidebar), except that it will return the custom defined admin address. `rul_register` takes three parameters: the "before" code (by default "&lt;li&gt;"), the "after" code (by default "&lt;/li&gt;"), and whether to echo or return the result (default is `true` and thus echo).

= Translations =

* nl\_NL translation by Anja of http://www.werkgroepen.net/wordpress/plugins/peters-login-redirect/
* sk\_SK translation by Michal Miksik of http://moonpixel.com/michal-miksik/
* ro\_RO translation by Anunturi Jibo of http://www.jibo.ro
* cs\_CZ translation by Petr Mašek
* de\_DE translation by Lara of http://www.u-center.nl
* es\_ES translation by Closemarketing of http://www.closemarketing.es

== Installation ==

Unzip wplogin\_redirect.php to your WordPress plugins folder.

Redirect rules are configured in the Settings > Login/logout redirects admin menu.

== Screenshots ==

1. Defining redirect rules per role.

== Frequently Asked Questions ==

Please visit the plugin page at http://www.theblog.ca/wplogin-redirect with any questions.

== How to Extend ==

= Custom redirect rules =

You can write your own code logic before any of this plugin's checks for user-specific, role-specific, and capability-specific redirects, as well as before the fallback redirect URL.

Available filters are:

* rul_before_user
* rul_before_role
* rul_before_capability
* rul_before_fallback

Each takes the same 4 parameters:

* $custom_redirect_to: This is set as false in case you don't have any redirect URL to set. Return this instead of false in case you have multiple filters running.
* $redirect_to: Set by WordPress, usually the admin URL.
* $requested_redirect_to: Set by WordPress, usually an override set in a GET parameter.
* $user: A PHP object representing the current user.

Your return value in your own code logic should be the URL to redirect to, or $custom_redirect_to to continue the plugin's normal checks.

An example of plugin code to redirect users on first login. See http://www.theblog.ca/wordpress-redirect-first-login for standalone functionality:

`// Send new users to a special page
function redirectOnFirstLogin( $custom_redirect_to, $redirect_to, $requested_redirect_to, $user )
{
    // URL to redirect to
    $redirect_url = 'http://yoursite.com/firstloginpage';
    // How many times to redirect the user
    $num_redirects = 1;
    // If implementing this on an existing site, this is here so that existing users don't suddenly get the "first login" treatment
    // On a new site, you might remove this setting and the associated check
    // Alternative approach: run a script to assign the "already redirected" property to all existing users
    // Alternative approach: use a date-based check so that all registered users before a certain date are ignored
    // 172800 seconds = 48 hours
    $message_period = 172800;

    /*
        Cookie-based solution: captures users who registered within the last n hours
        The reason to set it as "last n hours" is so that if a user clears their cookies or logs in with a different browser,
        they don't get this same redirect treatment long after they're already a registered user
    */
    /*

    $key_name = 'redirect_on_first_login_' . $user->ID;
    
    if( strtotime( $user->user_registered ) > ( time() - $message_period )
        && ( !isset( $_COOKIE[$key_name] ) || intval( $_COOKIE[$key_name] ) < $num_redirects )
      )
    {
        if( isset( $_COOKIE[$key_name] ) )
        {
            $num_redirects = intval( $_COOKIE[$key_name] ) + 1;
        }
        setcookie( $key_name, $num_redirects, time() + $message_period, COOKIEPATH, COOKIE_DOMAIN );
        return $redirect_url;
    }
    */
    /*
        User meta value-based solution, stored in the database
    */
    $key_name = 'redirect_on_first_login';
    // Third parameter ensures that the result is a string
    $current_redirect_value = get_user_meta( $user->ID, $key_name, true );
    if( strtotime( $user->user_registered ) > ( time() - $message_period )
        && ( '' == $current_redirect_value || intval( $current_redirect_value ) < $num_redirects )
      )
    {
        if( '' != $current_redirect_value )
        {
            $num_redirects = intval( $current_redirect_value ) + 1;
        }
        update_user_meta( $user->ID, $key_name, $num_redirects );
        return $redirect_url;
    }
    else
    {
        return $custom_redirect_to;
    }
}

add_filter( 'rul_before_user', 'redirectOnFirstLogin', 10, 4 );`

An example of plugin code to redirect to a specific URL for only a specific IP range as the first redirect check:

`function redirectByIP( $custom_redirect_to, $redirect_to, $requested_redirect_to, $user )
{
    $ip_check = '192.168.0';
    if( 0 === strpos( $_SERVER['REMOTE_ADDR'], $ip_check ) )
    {
        return '/secret_area';
    }
    else
    {
        return $custom_redirect_to;
    }
}

add_filter( 'rul_before_user', 'redirectByIP', 10, 4 );`

Note that the same extensibility is available for logout redirects with this filter:

* rul_before_fallback_logout

It takes 3 parameters:

* $custom_redirect_to: This is set as false in case you don't have any redirect URL to set. Return this instead of false in case you have multiple filters running.
* $requested_redirect_to: A redirect parameter set via POST or GET.
* $user: A PHP object representing the current user.

= Custom variable parameters =

There is an available filter "rul_replace_variable" for adding your own custom variable names. For example, to replace **[variable]month[/variable]** in the redirect URL with the numeric representation of the current month (with leading zeros):

`function customRULVariableMonth( $custom_redirect_to, $variable, $user )
{
    if( 'month' == $variable )
    {
        return date( 'm' );
    }
    else
    {
        return $custom_redirect_to;
    }
}

add_filter( 'rul_replace_variable', 'customRULVariableMonth', 10, 3 );`

Be sure to rawurlencode the returned variable if necessary.

== Changelog ==

= 2.5.2 =
* 2012-02-06: Bug fix: Fallback redirect rule updates were broken for non-English installs.

= 2.5.1 =
* 2012-01-17: Bug fix: Redirect after registration back-end code was missed in 2.5.0, and thus that feature wasn't actually working.

= 2.5.0 =
* 2012-01-15: Added redirect after registration option. Also made plugin settings editable in the WordPress admin panel.

= 2.4.0 =
* 2012-01-05: Added support for URL variable "postid-23". Also added documentation on how to set up redirect on first login.

= 2.3.0 =
* 2011-11-06: Added support for URL variable "siteurl" and "homeurl". Also added filter to support custom replacement variables in the URL. See Other Notes / How to Extend for documentation.

= 2.2.0 =
* 2011-09-21: Support basic custom logout redirect URL for all users only. Future versions will have the same framework for logout redirects as for login redirects.

= 2.1.1 =
* 2011-08-13: Minor code cleanup. Note: users now need "manage_links" permissions to edit redirect settings by default.

= 2.1.0 =
* 2011-06-06: Added hooks to facilitate adding your own extensions to the plugin. See Other Notes / How to Extend for documentation.

= 2.0.0 =
* 2011-03-03: Added option to allow a redirect_to POST or GET variable to take precedence over this plugin's rules.

= 1.9.3 =
* 2010-12-15: Made plugin translatable. (Thanks Anja!)

= 1.9.2 =
* 2010-08-20: Bug fix in code syntax.

= 1.9.1 =
* 2010-08-03: Bug fix for putting the username in the redirect URL.

= 1.9.0 =
* 2010-08-02: Added support for a separate redirect controller URL for compatibility with Gigya and similar plugins that bypass the regular WordPress login redirect mechanism. See the $rul_use_redirect_controller setting within this plugin.

= 1.8.1 =
* 2010-05-13: Added proper encoding of username in the redirect URL if the username has spaces.

= 1.8.0 =
* 2010-03-18: Added the ability to specify a username in the redirect URL for more dynamic URL generation.

= 1.7.3 =
* 2010-03-04: Minor tweak on settings page for better compatibility with different WordPress URL setups.

= 1.7.2 =
* 2010-01-11: Plugin now removes its database tables when it is uninstalled, instead of when it is deactivated. This prevents the redirect rules from being deleted when upgrading WordPress automatically.

= 1.7.1 =
* 2009-10-07: Minor database compatibility tweak. (Thanks KCP!) 

= 1.7.0 =
* 2009-05-31: Added option $rul_local_only (in the plugin file itself) to bypass the WordPress default limitation of only redirecting to local URLs.

= 1.6.1 =
* 2009-02-06: Minor database table tweak for better compatibility with different setups. (Thanks David!)

= 1.6.0 =
* 2008-11-26: Added a function rul_register that acts the same as the wp_register function you see in templates, except that it will return the custom defined admin address

= 1.5.1 =
* 2008-09-17: Fixed compatibility for sites with a different table prefix setting in wp-config.php. (Thanks Eric!) 