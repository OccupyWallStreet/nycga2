=== Login With Ajax ===
Contributors: netweblogic
Donate link: http://netweblogic.com/wordpress/plugins/login-with-ajax/
Tags: Login, Ajax, Redirect, BuddyPress, MU, WPMU, sidebar, admin, widget
Requires at least: 2.8
Tested up to: 3.3.2
Stable tag: 3.0.4.1

Add smooth ajax login and registration effects to your blog and choose where users get redirected upon login/logout. Supports SSL, MU, and BuddyPress.

== Description ==

Login With Ajax is for sites that need user logins or registrations and would like to avoid the normal wordpress login pages, this plugin adds the capability of placing a login widget in the sidebar with smooth AJAX login effects.

Some of the features:

* AJAX Login without refreshing your screen.
* AJAX Registration without refreshing your screen.
* AJAX Registration Password retrieval without refreshing your screen.
* Compatible with Wordpress, Wordpress MU and BuddyPress (BuddyPress supports logins only, no registrations yet).
* Will work with forced SSL logins.
* Customizable, upgrade-safe widgets.
* Redirect users to custom URLs on Login and Logout
* Redirect users with different roles to custom URLs
* shortcode and template tags available
* Fallback mechanism, will still work on javascript-disabled browsers
* Widget specific option to show link to profile page
* Now translatable (currently only Spanish is available, please contact me to contribute)

If you have any problems with the plugins, please visit our [http://netweblogic.com/forums/](support forums) for further information and provide some feedback first, we may be able to help. It's considered rude to just give low ratings and nothing reason for doing so.

If you find this plugin useful and would like to say thanks, a link, digg, or some other form of recognition to the plugin page on our blog would be appreciated.

= Translated Languages Available =

Here's a list of currently translated languages. Translations that have been submitted are greatly appreciated and hopefully make this plugin a better one. If you'd like to contribute, please have a look at the POT file in the langs folder and send us your translations.

* Finnish - Jaakko Kangosjärvi
* Russian - [http://dropbydrop.org.ua/](Виталий Капля)
* French - [http://wall.clan-zone.dk](Geoffroy Deleury)
* German - Linus Metzler
* Chinese - [http://fashion-bop.com](Simon Lau)
* Italian - Marco aka teethgrinder
* Romanian - Gabriel Berzescu
* Danish - Christian B.
* Dutch - Sjors Spoorendonk
* Brazilian - Humberto S. Ribeiro, Diogo Goncalves, Fabiano Arruda
* Turkish - Mesut Soylu
* Polish - Ryszard Rysz
* Lithuanian - [http://www.kulinare.lt/](Gera Dieta)
* Albanian - [http://blogu.programeshqip.org/](Besnik Bleta)
* Spanish - Myself and [http://e-rgonomy.com](Danilo Casati)
* Hungarian - Lorinc Borda
* Japanese - [http://riuiski.com](Ryuei Sasaki)

== Installation ==

1. Upload this plugin to the `/wp-content/plugins/` directory and unzip it, or simply upload the zip file within your wordpress installation.

2. Activate the plugin through the 'Plugins' menu in WordPress

3. If you want login/logout redirections, go to Settings > Login With Ajax in the admin area and fill out the form.

4. Add the login with ajax widget to your sidebar, or use login_with_ajax() in your template.

5. Happy logging in!

== Notes ==

= Shortcodes & Template Tags =

You can use the shortcode [login-with-ajax] or [lwa] and template tag login_with_ajax() with these options :

* is_widget='true'|'false' - By default it's set to false, if true it uses the $before_widget/$after_widget variables.
* profile_link='true'|'false' - By default it's set to false, if true, a profile link to wp-admin appears.
* register='true'|'false' - By default it's set to false, if true, a rgistration link appears, providing you have registration enabled.

= SSL Logins =

To force SSL, see [http://codex.wordpress.org/Administration_Over_SSL]("this page"). The plugin will automatically detect the wordpress settings.

= Customizing the Widget =
You can customize the html widgets in an upgrade-safe manner. Firstly, you need to understand how Login With Ajax loads templates:

* When looking for files/templates there is an order of precedence - active child theme (if applicable), active parent themes, and finally the plugin folder
* Login With Ajax loads only one CSS and JS file. The plugin checks the locations above and loads the one it finds first.
** login-with-ajax.js and login-with-ajax.css must be located in either wp-content/themes/yourtheme/plugins/login-with-ajax/ or wp-content/plugins/login-with-ajax/widget/
** This was done to minimize the number of resources loaded, but means that if you have more than one template, you should add any extra CSS and JS to those single files.
* Login With Ajax then checks for template folders, if two folders match names (e.g. you move default template to your theme) the order of precedence explained above applies.
** These theme folders are located within wp-content/themes/yourtheme/plugins/login-with-ajax/ or wp-content/plugins/login-with-ajax/widget/
* There is a zip file inside the widget folder with a few extra theme folders, they are not fully maintained each release, but should work.
* When a user is logged out, the widget_out.php will be shown, otherwise widget_in.php. These are located in the template folder.

For example, if you wanted to change some text on the default theme, you could simply copy wp-content/plugins/login-with-ajax/widget/default to wp-content/themes/yourtheme/plugins/login-with-ajax/default and then just edit the files as needed.

If you need to change the JS or CSS, copy the javascript file over to wp-content/themes/yourtheme/plugins/login-with-ajax/ (not within the template file) and edit accordingly.

The Javascript ajax magic relies on the id names within the template files, if you want to modify the templates, make sure you keep these id names.

= Registration Incompatibilities =

*Note that registrations will not work on buddypress due to the customizations they make on the registration process, we will try to come up with a solution for this asap*

== Screenshots ==

1. Add a  fully customizable login widget to your sidebars.

2. Smoothen the process via ajax login, avoid screen refreshes on failures.

3. If your login is unsuccessful, user gets notified without loading a new page!

4. Customizable login/logout redirection settings.

5. Choose what your users see once logged in.

== Frequently Asked Questions ==

= The registration link doesn't show! What's wrong? =
Before you start troubleshooting, make sure your blog has registrations enabled via the admin area (Settings > General) and that your widget has the registration link box checked.

= AJAX Registrations don't work! What's wrong? =
Firstly, you should make sure that you can register via the normal wp-admin login, if something goes wrong there the problem is not login with ajax. Please note that currently there is no AJAX registration with BuddyPress due to it rewriting the login area (this will be resolved soon).

= How can I customize the login widget? =
See the notes section about customizing a widget.

= How do I use SSL with this plugin? =
Yes, see the notes section.

= Do you have a shortcode or template tag? =
Yes, see the notes section.

For further questions and answers (or to submit one yourself) go to our [http://netweblogic.com/forums/](support forums).


== Changelog ==

= 2.1 =
* Added translation POT files.
* Spanish translation (quick/poor attempt on my part, just to get things going)
* Fixed result bug on [http://netweblogic.com/forums/topic/undefined-error-on-logging-in-with-wp-29]
* Fixed bug on [http://wordpress.org/support/topic/355406]

= 2.1.1 =
* Added Finnish, Russian and French Translations
* Made JS success message translatable
* Fixed encoding issue (e.g. # fails in passwords) in the JS

= 2.1.2 =
* Added German Translations
* Fixed JS url encoding issue

= 2.1.3 =
* Added Italian Translations
* Added space in widget after "Hi" when logged in.
* CSS compatability with themes improvement.

= 2.1.4 =
* Added Chinese Translations
* CSS compatability with themes improvement.

= 2.1.5 =
* Changed logged in widget to fix avatar display issue for both BuddyPress and WP. (Using ID instead of email for get_avatar and changed depreciated BP function).
* Added Danish Translation

= 2.2 =
* Added Polish, Turkish and Brazilian Translation
* Fixed buddypress avatar not showing when logged in
* Removed capitalization of username in logged in widget
* Fixed all other known bugs
* Added placeholders for redirects (e.g. %USERNAME% for username when logged in)
* Added seamless login, screen doesn't refresh upon successful login.

= 2.21 =
* Redirect bug fix
* Hopefully fixed encoding issue

= 3.0b =
* Various bug fixes
* Improved JavaScript code
* Ajax Registration Option

= 3.0b3 =
* %LASTURL% now works for logins as well
* Profile link plays nice with buddypress
* Added fix to stop wp_new_user_notification conflicts
* Empty logins now have an error message too.

= 3.0 =
* Option to choose from various widget templates.

= 3.0.1 =
* Fixed unexpected #LoginWithAjax_Footer showing up at bottom
* Fixed link problems for sub-directory blogs (using bloginfo('wpurl') now)
* Added Albanian
* Replace Spanish with revised version

= 3.0.2 =
* got rid of (hopefully all) php warnings

= 3.0.3 =
* scrollbar issue in default widget
* added hungarian

= 3.0.4 =
* updated russian translation
* added japanese
* updated iranian
* added registration attribute to template tags/shortcode

= 3.0.4.1 =
* fixed xss vulnerability for re-enlistment on wordpress repo, more on the way