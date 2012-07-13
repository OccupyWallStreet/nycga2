=== Really simple Facebook Twitter share buttons ===
Contributors: whiletrue
Donate link: http://www.whiletrue.it/
Tags: facebook, twitter, facebook share, twitter share, facebook share button, twitter share button, linkedin, google +1, +1, pinterest, google buzz, buzz, digg, flattr, stumbleupon, hyves, links, post, page, mail, email, reddit, tipy, shortcode
Requires at least: 2.9+
Tested up to: 3.4.1
Stable tag: 2.5.2

Puts Facebook, Twitter, LinkedIn, Google "+1", Pinterest and other share buttons of your choice above or below your posts.

== Description ==
This plugin shows Facebook, Twitter, LinkedIn, Google "+1", Pinterest and other popular share buttons above or below your posts.
Easy customization of active buttons and position in the Settings menu.

Facebook Like and Twitter Share buttons are loaded by default. 
Other buttons, including Digg, Facebook Send, Flattr, LinkedIn, Google "+1", Pinterest, Google Buzz, Stumbleupon, Hyves, Email, Reddit, Tipy (and the deprecated Facebook Share button), can be added through the `Settings->Really simple share` menu.

Please be careful when selecting the `Show buttons in these pages` options : it can interact badly with other slide/fade/carousel/sidebar active plugins.

If you want to place the active buttons only in selected posts, use the [really_simple_share] shortcode.

If you want to hide the share buttons inside selected posts, set a "really_simple_share_disable" custom field with value "yes".

For more informations: http://www.whiletrue.it/en/projects/wordpress/22-really-simple-facebook-twitter-share-buttons-per-wordpress.html

*New* in version 2.5:

* Buffer button (basic support: no twitter user mention, no image support)
* CSS Style improvements
* Optional related Twitter usernames (comma separated) added to the follow list

*New* in version 2.0 - 2.4:

* Pinterest button (basic support: only shows if there is some media, links to the thumbnail or to the first media attachment)
* Language basic support for some buttons
* More compact and effective Settings page
* Possibility to put some text beside the buttons
* Speed improvements and possibility to put the scripts at the bottom of the page
* Button arbitrary positioning via drag&drop
* Arbitrary spacing for every button
* Wordpress link customization (default permalink and shortlink available)
* Email button label
* Facebook share button counter customization
* New option to disable buttons on excerpts

Do you like this plugin? Give a chance to our other works:

* [Most and Least Read Posts](http://www.whiletrue.it/en/projects/wordpress/29-most-and-least-read-posts-widget-per-wordpress.html "Most and Least Read Posts")
* [Random Tweet Widget](http://www.whiletrue.it/en/projects/wordpress/33-random-tweet-widget-per-wordpress.html "Random Tweet Widget")
* [Reading Time](http://www.whiletrue.it/en/projects/wordpress/17-reading-time-per-wordpress.html "Reading Time")
* [Really Simple Twitter Feed Widget](http://www.whiletrue.it/en/projects/wordpress/25-really-simple-twitter-feed-widget-per-wordpress.html "Really Simple Twitter Feed Widget")
* [Tilted Twitter Cloud Widget](http://www.whiletrue.it/en/projects/wordpress/26-tilted-twitter-cloud-widget-per-wordpress.html "Tilted Twitter Cloud Widget")

== Installation ==
Best is to install directly from WordPress. If manual installation is required, please make sure to put all of the plugin files in a folder named `really-simple-facebook-twitter-share-buttons` (not two nested folders) in the plugin directory, then activate the plugin through the `Plugins` menu in WordPress.

== Frequently Asked Questions ==

= What's the difference between Facebook Like and Share buttons? =
Facebook Like's behaviour is similar to Facebook Share: it is a counter and if you click it a story is published inside your Wall on Facebook.
We suggest you to use Facebook Like because it works better identifying title and text for the story to be published and it's the only one currently developed by Facebook.

= Why users can't choose which image to share when using Facebook Like button ? =
This is an automated Facebook behaviour: clicking Facebook Like the user can't choose each time which image to share, 
but you can set the right image inside the code using the 
<a href="http://developers.facebook.com/docs/reference/plugins/like/">Open Graph Tag</a> og:image.

= When I activate the plugin it messes up with other plugins showing post excerpts in different ways (fade, carousel, sidebar). What can I do? =
Uncheck all the "Show buttons in these pages" options in the `Settings->Really simple share` menu, except for "Single posts".
This way all the share buttons should disappear, except the one displayed beside the post in every Single post page.

= Is it possible to modify the style/css of the buttons? =
Yes, every button has its own div class (e.g. "really_simple_share_twitter") for easy customization inside the theme css files.
Plus, the div surrounding all buttons has its own class "really_simple_share". 
If you want to override default styling of the buttons, check the `disable default styles` option add your style rules inside your css theme file.

= Is it possible to show the buttons anywhere inside my theme, using a PHP function? =
Yes, you can call the really_simple_share_publish($link='', $title='') PHP function, passing it the link and the title used by the buttons.
You shouldn't leave the parameters blank, unless the code is put inside the WP loop.
For example, use this code to create buttons linking to the website home page:
echo really_simple_share_publish(get_bloginfo('url'), get_bloginfo('name')); 

= How about other social networks? =
We'll see!
 

== Screenshots ==
1. Sample content, activating the Facebook Share and Twitter buttons  
2. Options available in the Settings menu 

== Changelog ==

= 2.5.2 =
* Changed: Possibility to set a custom title in the publish function, leaving the default link (thanks Arvid Janson)
* Changed: Facebook Send button code update and style cleaning
* Fixed: Little php code cleaning

= 2.5.1 =
* Added: Optional related Twitter usernames (comma separated) added to the follow list

= 2.5.0 =
* Added: Buffer button
* Fixed: Style cleaning for the prepend_above box
* Changed: Pinterest button is shown if some image is found in the post content, even if it's not a thumbnail or an attachment

= 2.4.4 =
* Fixed: Google+ and Pinterest buttons broken in previous updates

= 2.4.3 =
* Changed: Little code cleaning
* Fixed: The option to disable buttons on excerpts now correctly disables only the plugin  
* Fixed: Facebook Like box height

= 2.4.2 =
* Fixed: Pinterest button broken in 2.4 and 2.4.1 while recognizing images in posts 

= 2.4.1 =
* Changed: Little code redundancy cleaning
* Fixed: Google+ and Pinterest issue on header javascript loading

= 2.4 =
* Added: Facebook share button counter customization
* Added: Option to disable buttons on excerpts
* Changed: CSS Style improvements (button vertical alignment, removed redundant code)
* Changed: Removed redundant spaces (sometimes breaking the button alignment) 
* Fixed: If button width is not set, use the default value
* Fixed: For Pinterest, now check the existence of the function has_post_thumbnail
* Fixed: On some templates the Google+ button was disappearing (javascript code not loaded)

= 2.3 =
* Added: Pinterest button (basic support: only shows if there is some media, links to the thumbnail or to the first media attachment)
* Added: Language basic support for some buttons
* Changed: More compact and effective Settings page
* Changed: Update on Google +1 button code

= 2.2 =
* Added: Option to put a line of text above the buttons, e.g. 'If you liked this post, say thanks sharing it:'
* Added: Option to put an inline short text just before the buttons, e.g. 'Share this!'

= 2.1 =
* Added: Option to put scripts at the bottom of the body, to increase page loading speed
* Added: Option to enable/disable adding the author of the post to the Twitter follow list
* Added: Little performance improvements

= 2.0 =
* Added: Button arbitrary positioning via drag&drop
* Added: Arbitrary spacing for every button
* Added: Twitter post author customization (thanks Vincent Oord - Springest.com)
* Added: Wordpress link customization (default permalink and shortlink available)
* Added: Email button label
* Added: Class "robots-nocontent" and "snap_nopreview" given to the element surrounding the buttons
* Added: Some code cleaning

= 1.8.4 =
* Fixed: Twitter share button title cleaning (thanks Harald)

= 1.8.3 =
* Fixed: Removed the standard "Tweet" text from the link inside the Twitter button, to avoid its occasional presence in the summaries (thanks David)

= 1.8.2 =
* Added: Tipy button
* Changed: better email icon (thanks Jml from Argentina)

= 1.8.1 =
* Added: Linkedin button counter customization

= 1.8.0 =
* Added: Separate stylesheet added, with an option to disable it

= 1.7.3 =
* Fixed: Flattr share button title cleaning (thanks Harald)

= 1.7.2 =
* Fixed: Flattr share button js api loading, tags loading and text linking 

= 1.7.1 =
* Fixed: Flattr share button warning for posts without tags

= 1.7.0 =
* Added: Flattr share button

= 1.6.3 =
* Added: Box layout available for compatible buttons

= 1.6.2 =
* Fixed: Facebook Like button url encoded (thanks Radek Maciaszek)

= 1.6.1 =
* Added: Google +1 button width and counter customization

= 1.6.0 =
* Added: Google +1 share button
* Changed: admin page restyle

= 1.5.1 =
* Added: possibility to hide the Twitter button counter 

= 1.5.0 =
* Added: possibility to use the "really_simple_share_publish" PHP function to publish the buttons inside the PHP code, for themes and other plugins
* Changed: single permalink and title loading, for better performance

= 1.4.16 =
* Fixed: css improvements

= 1.4.15 =
* Added: Facebook Like button new "Send" option (currently via FBML)
* Changed: admin css improvements

= 1.4.13 =
* Fixed: css improvements

= 1.4.12 =
* Fixed: more vertical space (for the current Facebook Like button)

= 1.4.11 =
* Changed: removed redundant <br /> element

= 1.4.10 =
* Fixed: Digg button JS removed from the <head> section

= 1.4.9 =
* Fixed: Email share button image absolute path

= 1.4.8 =
* Added: Reddit share button

= 1.4.7 =
* Added: Email share button
* Added: Possibility to position the buttons above and below the post content
* Fixed: PHP Notices

= 1.4.6 =
* Changed: [really_simple_share] shortcode works even when "really_simple_share_disable" is used (thanks to Chestel!)

= 1.4.5 =
* Added: "really_simple_share_disable" custom field, if set to "yes" hides share buttons inside post content
* Added: Facebook Like and Twitter button width customization via the options menu 

= 1.4.4 =
* Added: [really_simple_share] shortcode, shows active share buttons inside post content

= 1.4.3 =
* Added: Hyves (the leading Duch social network) button
* Fixed: Twitter button fixed-width style for WPtouch compatibility

= 1.4.2 =
* Fixed: Excerpt/Content and JavaScript loading

= 1.4.1 =
* Added: Facebook Like text customization (like/recommend)
* Fixed: Show in Search results

= 1.4.0 =
* Changed: Avoid multiple external JavaScript files loading when possible, for better performance
* Added: "Show in Search results" option
* Fixed: Twitter title button

= 1.3.1 =
* Added: Twitter additional text option, e.g. ' (via @authorofblogentry)'
* Changed: Settings display improvement

= 1.3.0 =
* Added: Digg and Stumbleupon share buttons
* Added: CSS classes for easy styling

= 1.2.3 =
* Fixed: Facebook share button

= 1.2.2 =
* Added: Facebook like button (Facebook share is still present but deprecated)
* Added: Google Buzz share button
* Fixed: Button positioning
* Changed: Save/retrieve options standardization

= 1.2.1 =
* Fixed: Button links

= 1.2.0 =
* Added: Active buttons option
* Added: Active locations (home page, single posts, pages, tags, categories, date based archives, author archives) option

= 1.1.0 =
* Added: LinkedIn share button

= 1.0.1 =
* Fixed: Uninstall

= 1.0.0 =
Initial release


== Upgrade Notice ==

= 2.4.4 =
Users having versions from 2.4 to 2.4.3 should upgrade due to a bugfix on the Google+ and Pinterest buttons 

= 1.7.3 =
Users having version from 1.6.3 to 1.7.2 should upgrade due to a bugfix on the Flattr button 

= 1.7.2 =
Users having version from 1.6.3 to 1.7.1 should upgrade due to a bugfix on the Flattr button 

= 1.7.1 =
Users having version from 1.6.3 to 1.7.0 should upgrade due to bugfixes on general loading and on the Flattr button 

= 1.4.2 =
Users having version 1.4.0 and 1.4.1 are advised to upgrade due to an Excerpt/Content and JavaScript loading bugfix

= 1.2.2 =
Facebook Share button is deprecated in favor of Facebook Like button

= 1.0.0 =
Initial release


== Upcoming features ==

* Reset default options
* Share buttons widget
* Shortcodes for single buttons
* New "report" button
* Counter for the "mail" button