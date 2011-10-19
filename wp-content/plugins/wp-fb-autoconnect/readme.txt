=== Plugin Name ===
Contributors: Justin_K
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=T88Y2AZ53836U
Tags: facebook connect, login with facebook, facebook autoconnect, facebook, connect, widget, login, logon, wordpress, buddypress
Requires at least: 2.5
Tested up to: 3.1.3
Stable tag: 2.1.0

A LoginLogout widget with Facebook Connect button, offering hassle-free login for your readers. Clean and extensible.  Supports BuddyPress.


== Description ==

The simple concept behind WP-FB AutoConnect is to offer an easy-to-use widget that lets readers login to your blog with either their Facebook account or local Wordpress credentials. Although many "Facebook Connect" plugins do exist, most of them are either overly complex and difficult to customize, or fail to provide a seamless experience for new  visitors. I wrote this plugin to provide what the others didn't:

* Full support for both Wordpress and Buddypress.
* No user interaction is required - the login process is transparent to new and returning users alike.
* Existing users who connect with FB retain the same local user accounts as before.
* New visitors will be given new user accounts, which can be retained even if you remove the plugin.
* Facebook profile pictures can be used as avatars, even on pre-existing comments.
* User registration announcements can be pushed to Facebook walls.
* No contact with the Facebook API after the login completes - so no slow pageloads.
* Won't bloat your database with duplicate user accounts, extra fields, or unnecessary complications.
* Custom logging options can notify you whenever someone connects with Facebook.
* A powerful set of hooks and filters allow developers to easily tailor the login process to their personal needs: redirect to a custom page, fill xProfile data with information from Facebook, setup permissions based on social connections, and more.
* Fully HTML/CSS valid.


== Installation ==

To allow your users to login with their Facebook accounts, you must first setup an Application for your site:

1. Visit [www.facebook.com/developers/createapp.php](http://www.facebook.com/developers/createapp.php)
2. Type in a name (i.e. the name of your blog). This is what Facebook will show on the login popup.
3. Click the "Web Site" tab and fill in your "Site URL" (with a trailing slash).  Note: http://example.com/ and http://www.example.com/ are *not* - be sure this matches Settings -&gt; General -&gt; Wordpress Address.
4. Click "Save Changes," and note the API Key and Application Secret (you'll need them in a minute).

Then you can install the plugin:

1. Download the latest version from [here](http://wordpress.org/extend/plugins/wp-fb-autoconnect/), unzip it, and upload the extracted files to your plugins directory.
2. Login to your Wordpress admin panel and activate the plugin.
3. Navigate to Settings -> WP-FB AutoConn.
4. Enter your Application's API Key and Secret (obtained above), and click "Save."
5. If you're using BuddyPress, a Facebook button will automatically be added to its built-in login panel.  If not, navigate to Appearance -&gt; Widgets and add the WP-FB AutoConnect widget to your sidebar. 

That's it - users should now be able to use the widget to login to your blog with their Facebook accounts.

For more information on exactly how this plugin's login process works and how it can be customized, see the [homepage](http://www.justin-klein.com/projects/wp-fb-autoconnect).


== Frequently Asked Questions ==

[FAQ](http://www.justin-klein.com/projects/wp-fb-autoconnect#faq)


== Screenshots ==

[Screenshots](http://www.justin-klein.com/projects/wp-fb-autoconnect#demo)


== Changelog ==
= 2.1.0 (2011-10-01) =
* Update to use OAuth 2.0.  NOTE: Premium users must also update their addons; see the support page for how.

= 2.0.9 (2011-07-20) =
* Don't apply sanitize_title to nicenames

= 2.0.8 (2011-07-10) =
* Fix for completely non-alphanumeric usernames

= 2.0.7 (2011-07-06) =
* Filtered avatars use the $alt provided by WP core (or theme)

= 2.0.6 (2011-07-01) =
* Fixed bug with avatar caching on BuddyPress

= 2.0.5 (2011-06-14) =
* Increase version campatiblity number
* Remove legacy API code from _process_login.php
* Remove legacy code for registering email hashes (Facebook has eliminated that functionality from their API for some reason...)
* Update instapopup to work with the new API
* Fixed avatars in Settings -> Discussion when avatar fetching is enabled
* Apply sanitize_title to the nicename (thanks Espen Espelund)
* Add sponsorship message
* General cleanups to _process_login.php

= 2.0.4 (2011-06-09) =
* Add a channelUrl to FB.init to resolve [known bugs](https://developers.facebook.com/docs/reference/javascript/FB.init/) with the FB api

= 2.0.3 (2011-06-09) =
* _process_login will now look for "rememberme" POST variable
* Added a new "wpfb_rememberme" filter, so you can override the above if desired

= 2.0.2 (2011-05-26) =
* Oops: wpfb_userinfo_permissions was redundant; removed :)

= 2.0.1 (2011-05-26) =
* Handle relative paths in usermeta (for cached avatars)
* Get rid of some depreciated functions (get_usermeta -> get_user_meta)
* Add a new hook wpfb_add_to_asyncinit, so you can output your own JS after the Facebook API initializes
* Add a new filter wpfb_userinfo_permissions, so you can choose what userinfo permissions to ask for
* Check for function_exists('is_multisite') to support older versions of WP
* More descriptive error message for "Nonce check failed."
* More descriptive error message for "Failed to get the Facebook session."

= 2.0.0 (2011-05-19) =
* As Facebook has decided to shut off the old REST API on Sept 1, 2011, I've rewritten the core plugin to use the new Graph API.  All Premium users must also update their addons to be compatible with this new core plugin.
* Removed "IE9 compatability mode" as it's no longer relevant.
* Removed "Request and require email permissions" as it's no longer relevant (all permissions are requested in a single popup, so one cannot be denied while approving the others).
* Show a warning to Premium users with an outdated version of the addon. 

= 1.9.2 (2011-04-02) =
* Add an "IE compatability mode", so the old API will work on IE9.  Can be disabled via debug options, but is enabled by default.

= 1.9.1 (2011-04-01) =
* CSS id fbLoginButton is now class fbLoginButton, to fix validation issue if multiple buttons are placed on the same page
* NOTE: Premium users using the "ajax spinner" feature must also update their addon!

= 1.9.0 (2011-04-01) =
* jfb_output_facebook_callback() is now automatically called by wp_footer; If you're manually outputting your own Facebook buttons, you should now ONLY call jfb_output_facebook_btn() (explicitly calling jfb_output_facebook_callback() is no longer needed!)
* Premium comment-form buttons now not dependent on Widget buttons
* Output some html comments

= 1.8.9 (2011-04-01) =
* Rename some functions that weren't following convention
* jfb_output_facebook_callback() prevents itself from outputting duplicate forms on the same page

= 1.8.8 (2011-03-31) =
* Just some revisions to the admin panel

= 1.8.7 (2011-03-31) =
* Another Facebook API error check in _process_login.php
* New filter wpfb_inserting_user to replace wpfb_insert_user
* CSS for the admin panel
* Slightly reworded disclaimer

= 1.8.6 (2011-03-29) =
* Remove Donate section from the admin panel (a bit redundant now that I've got a premium addon)
* Two minor html fixes in the admin panel
* Minor bug fix in browser detection (used by logging)
* Hide the MOD REWRITE section from the admin panel, and update documentation in _autologin.php

= 1.8.5 (2011-03-24) =
* Add a new wpfb_extended_permissions filter

= 1.8.4 (2011-03-22) =
* Report Browser and OS in the login logs

= 1.8.3 (2011-03-22) =
* Allow the Premium addon to reside outside the plugin dir, so it doesn't get overwritten by automatic updates (also requires an update to Premium.php; on its way...)

= 1.8.2 (2011-03-19) =
* Differentiate the "Save" buttons in the admin panel, for clarity
* Include the sitename in emailed login logs (helpful for people who admin multiple sites)

= 1.8.1 (2011-03-19) =
* Exception handling for stream.publish (may fail if the user enters too long a message)

= 1.8.0 (2011-03-19) =
* Add support for the new OpenGraph API to _process_login.php

= 1.7.3 (2011-03-18) =
* Earlier check for PHP5 in _process_login.php
* Remove extraneous test from _process_login.php
* Bundle new Facebook API (not used YET...)

= 1.7.2 (2011-03-18) =
* Cleanups to the admin panel
* Marked as compatible up to 3.1

= 1.7.1 (2011-03-15) =
* Add ability to enter your own logging email address
* Get rid of old "hide facebook button" debug option

= 1.7.0 (2011-03-01) =
* Fix for Facebook users who've enabled "Secure Browsing (https)" on their accounts

= 1.6.9 (2011-02-10) =
* Replace depreciated update_usermeta() with update_user_meta()
* Check for completely non-alphanumeric Facebook names when autoregistering with "Pretty Names" enabled

= 1.6.8 (2011-02-06) =
* Fix validation issue if present with Wordbooker (duplicate attribute in html tag)
* Update tested compatibility to 3.0.4

= 1.6.7 (2011-02-05) =
* Fix bug with avatars on author page

= 1.6.6 (2011-01-28) =
* Reveal new premium options in the panel

= 1.6.5 (2011-01-28) =
* Add wpfb_output_facebook_locale action

= 1.6.4 (2011-01-28) =
* Add wpfb_login_rejected filter
* Add some resources for a new premium feature, & reveal more premium options in the admin panel

= 1.6.3 (2011-01-28) =
* Fixed a BP bug introduced in 1.6.2...sorry!
* Add action wpfb_after_button

= 1.6.2 (2011-01-27) =
* "Use Facebook profile pictures as avatars is now just one option" (they aren't separate for WP and BP)

= 1.6.1 (2011-01-27) =
* Fixed a bug with author links (they didn't work because the "nicename" had a space in it)
* Removed the "Enable BuddyPress Support" option; it's always enabled now
* NEW OPTION: You can now select the autoregistered username style (FB_12345, FB_John_Smith, or John.Smith)

= 1.6.0 (2011-01-23) =
* Reveal the Premium options in the admin panel

= 1.5.8 (2011-01-07) =
* Error handling for depreciated connect.registerUsers function

= 1.5.7 (2010-12-13) =
* Compatability fix for W3-Total-Cache

= 1.5.6 (2010-11-24) =
* Remove one unnecessary call to Facebook API
* Add wpfb_admin_messages action
* Pass the callback name to wpfb_add_to_js action

= 1.5.5 (2010-11-23) =
* Add support for a new option in the premium version
* More descriptive error message
* Add wpfb_existing_user action

= 1.5.4 (2010-11-03) =
* jfb_output_facebook_init() is output in footer, once, instead of using jQuery.  Should resolve conflict if multiple buttons are used on the same page.

= 1.5.3 (2010-11-02) =
* Slight revisions to readme
* Remove unneeded debug code

= 1.5.2 (2010-11-01) =
* Added new wpfb_prelogin action
* Added new wpfb_submit_loginfrm filter
* Added new wpfb_output_button filter
* Cleaner handling of a few admin panel options

= 1.5.1 (2010-11-01) =
* Cleaner integration with Premium addon

= 1.5.0 (2010-10-31) =
* Add full support for the Premium add-on
* Revise the features list in the Readme

= 1.4.4 (2010-10-30) =
* The wpfb_inserted_user action now supplies the full userdata of the inserted user
* Don't initialize the Facebook button until the page has finished loading (can be disabled via param to jfb_output_facebook_init())
* Setup hooks & options for lots of new premium features
* Add return URL to paypal donate button

= 1.4.3 (2010-10-29) =
* Hide the main plugin options until a valid API Key and Secret have been entered.

= 1.4.2 (2010-10-29) =
* Cleaned up admin panel code, regrouped the options, and rephrased some sections for better clarity. 
* Cleaned up BuddyPress & Avatar code a bit
* Add an optional "Powered By" link (defaults to off)
* Revisions to premium-checking code

= 1.4.1 (2010-10-29) =
* Remove unneeded debug code
* Add support for eventual premium functionality 

= 1.4.0 (2010-10-27) =
* Handle users with non-alphanumeric characters in their Facebook names.
* Use Firstname.Lastname rather than FirstnameLastname for Buddypress logins
* Revised some debug code, fixed problem with get_plugins()

= 1.3.14 (2010-10-26) =
* When debug logging is enabled, show REQUEST variables
* Added 2 new actions: wpfb_add_to_js and wpfb_add_to_form (Sponsored by [VideoUserManuals](http://9f200kliq7f39zam4ffc7wnk8b.hop.clickbank.net/))

= 1.3.13 (2010-10-26) =
* The prompts "Ask for permission to get the connecting user's email address" and "Request permission to post to the user's wall" are split into 2 separate permissions dialog, so the user may accept one but deny the other. (Sponsored by [VideoUserManuals](http://9f200kliq7f39zam4ffc7wnk8b.hop.clickbank.net/))

= 1.3.12 (2010-10-14) =
* Update the instructions (Facebook has changed some of the settings on their Create Application script).

= 1.3.11 (2010-10-14) =
* Performance optimization when searching for existing users during a login (thanks to Andy Clark)

= 1.3.10 (2010-08-31) =
* Still more checks to try and pinpoint the elusive "nonce check failed" bug

= 1.3.9 (2010-08-29) =
* More detailed log message on "nonce check failed" (to try and figure out what's causing it)

= 1.3.7 (2010-08-28) =
* Add a simple check to prevent users from accessing _process_login.php directly, PRIOR to the nonce check (so they get a different and more accurate error message)

= 1.3.5 (2010-08-24) =
* Attempt to find the user by directly looking up their email address before resorting to hashes
* Don't abort the login if Facebook refuses to register hashes (relevant on blogs with over 3,000 users)

= 1.3.4 (2010-08-23) =
* Slight rewording in the admin panel, for clarity
* Store proxied emails, if selected (Previously, the plugin was erroneously treating a "proxied facebook address" as "email address denied"; the log will now show what's really going on, and will store a proxied address, if selected).

= 1.3.3 (2010-08-23) =
* Clear previously fetched avatar if Facebook user has removed their profile picture
* Marked as compatible up to 3.0.1 (Oops! Forgot to do this earlier.)
* Nicer error reporting (thanks Andy Clark)

= 1.3.2 (2010-08-15) =
* Do not fetch Facebook profile picture if not present (revert to default WP/BP avatar)

= 1.3.1 (2010-08-14) =
* Fixed the "Object of class WP_Error could not be converted to string" bug

= 1.3.0 (2010-08-08) =
* Update Facebook API; PHP5 is now the minimum requirement
* This should (hopefully) fix the conflict with newer OpenGraph plugins (i.e. Like Button)

= 1.2.5 (2010-08-08) =
* New Feature: Use Facebook profile pictures as Wordpress avatars
* Code reorganization; BuddyPress code is now in Main.php, avatars are fetched in _process_login.php, etc.

= 1.2.4 (2010-08-07) =
* Reorganize options a bit to make a separate "Buddypress" section
* Made "Replace BuddyPress avatars with Facebook profile pictures" as optional
* Use htmlspecialchars so the widget will validate when redirect_to contains special chars

= 1.2.3 (2010-08-04) =
* Get rid of PHP short tags

= 1.2.2 (2010-07-24) =
* Added "Disable nonce check" to debug options (not recommended - see FAQS on the plugin page) 

= 1.2.1 (2010-07-14) =
* Oops! I made a commit error in 1.2.0.

= 1.2.0 (2010-07-14) =
* BuddyPress usernames generated via "First Name + Last Name" instead of "Name" (as reported [here](http://www.justin-klein.com/projects/wp-fb-autoconnect/comment-page-6#comment-12258))
* Facebook profile images are automatically displayed as BuddyPress avatars

= 1.1.9 (2010-05-28) =
* Again redo how the "Require Email" option is enforced
* Add option to publish new user registration announcement on user's walls (prompts for permission on connect)

= 1.1.8 (2010-05-17) =
* Added action wpfb_inserted_user to run *after* a user is inserted
* Fixed "Require Email" option

= 1.1.7 (2010-04-11) =
* Minor change: Use wp_generate_password() for autogenerated passwords

= 1.1.6 (2010-03-28) =
* Fixed to work on sites with over 1,000 existing users.

= 1.1.5 (2010-03-23) =
* Add an error check for a very rare bug; If the plugin is working on your site, you may skip this upgrade. 

= 1.1.4 (2010-03-23) =
* Include version number in login logs
* Slightly more descriptive error message in login logs
* Sanitize autogenerated usernames for BuddyPress
* Add "Show full log on error" option
* Add "Remove All Settings" (uninstall) option

= 1.1.3 (2010-03-22) =
* Check if other plugins have already included the Facebook API

= 1.1.2 (2010-03-21) =
* Logging: On failure, show the accumulated log up to the point of failure
* Logging: Show REQUEST variables
* Main: Add optional params to jfb_output_facebook_callback() and jfb_output_facebook_instapopup() so the default callback name can be overridden, allowing multiple login-handlers with different redirects and different email policies
* Main: auto-submitted login form's name based on the js callback name, to support multiple handlers
* Autologin: Fixed issue if both a button an autopopup were on the same page
* Include license

= 1.1.1 (2010-03-19) =
* Hopefully fix a crash on sites with more than 1,000 existing users
* Fix bug on some PHP4 configurations

= 1.1.0 (2010-03-18) =
* BuddyPress option is automatically enabled for BP installations
* Add wpfb_insert_user filter to run just before inserting an auto-created user
* Improved support for BuddyPress: use "pretty" usernames to fix profile links
* Include client IP in connection logs
* Cleanups/revisions to connection logs

= 1.0.8 (2010-03-18) =
* Add option to include Buddypress-specific filters
* Cleanup the Admin panel & update documentation

= 1.0.7 (2010-03-17) =
* Fix email hash-lookup for blogs with over 1,000 existing users

= 1.0.6 (2010-03-17) =
* Oops - Add support for PHP4 (really this time)

= 1.0.5 (2010-03-17) =
* Add support for PHP4

= 1.0.4 (2010-03-17) =
* Include the Facebook javascript in jfb_output_facebook_init() instead of wp_head
* Redirect form not generated by JS (this was leftover from an older version of the plugin...)
* Only check email hashes if there are actually existing users on the blog 
* Add wpfb_connect hook that runs BEFORE a login is allowed
* If email privilege is denied on first connect, but subsequently allowed, the user's auto-generated account will have its email updated to the correct one.
* Added uption to REQUIRE email address (not just prompt for it)
* XHTML Validation fix
* Small typo in the Widget

= 1.0.3 (2010-03-16) =
* Hopefully fix the "Call to undefined function wp_insert_user()" bug

= 1.0.2 (2010-03-16) =
* Fix API_Key validation check - should work properly now.

= 1.0.1 (2010-03-16) =
* Convert PHP short tags to long tags for server compatability

= 1.0.0 (2010-03-16) =
* First Release


== Support ==

Please direct all support requests [here](http://www.justin-klein.com/projects/wp-fb-autoconnect#feedback)