=== Content Scheduler ===
Contributors: freakingid 
Plugin Name: Content Scheduler
Plugin URI: http://structurewebdev.com/wordpress-plugins/content-scheduler/
Tags: expire posts, expire, expiring, scheduling, sticky
Author URI: http://profiles.wordpress.org/users/freakingid/
Author: Paul Kaiser (freakingid)
Requires at least: 2.9
Tested up to: 3.4.1
Stable tag: 0.9.9

Schedule content to automatically expire and change at a certain time, and notify people of expiration.

== Description ==

Content Scheduler lets you control when content automatically expires, what to do with that content when it expires, and optionally how to provide notification to site contributors when the content expired.

= Expiration Options =

You control what happens upon expiration, including:

* Change status to Pending, Draft, or Private
* Unstick Posts
* Change Categories
* Add and remove Tags
* Move to the Trash

= Notification Options =

Content Scheduler can also notify you:

* When expiration occurs
* A specific number of days before expiration occurs

This reminder helps you keep content fresh, providing a reminder that content is out of date and needs updated or replaced. Content Scheduler lets you use notification tools without making any changes to content upon expiration, if you'd like.

= More Information =

Documentation included with the plugin in PDF format.
Also see the developer's site, [Structure Web Development:](http://structurewebdev.com/)

== Installation ==

Extract the zip file and just drop the contents in the wp-content/plugins/ directory of your WordPress installation and then activate the Plugin from Plugins page.

== Frequently Asked Questions ==

= Where did you get the datepicker used in this plugin? =

That's the ["Any+Time" date / time picker:](http://www.ama3.com/anytime/) 

= Why isn't the datepicker showing up for me? =

1. Make sure you have enabled the datepicker in the plugin's settings.

1. You may have another plugin installed that uses JavaScript in the backend that conflicts with the Any+Time datepicker. Try disabling other plugins, one at a time, and checking for the datepicker working.

= Does Content Scheduler work with Network / Multisite installations? =

Yes. As of Version 0.9.7, Content Scheduler should work on WordPress Network Sites.

= My content doesn't seem to be expiring. What should I do? =

1. Check the plugin setting's "expiration period" and make sure you have waited at least that long before checking your content for expiration.

1. Make sure you have actually visited a page on your website after the post's expected expiration date. WordPress only fires off scheduled tasks when people actually visit the site.

1. Check your WordPress installation Timezone, and use one of the timezone strings. That is, when set to "UTC -6," our testing team found WordPress was going to wait several hours before beginning to check schedules. However, setting timezone to "America/Chicago" (the same timezone) fixed the problem. We're still checking on the reason for this.

1. Try simply deactivating the plugin and reactivating it, then testing again.

1. Other plugins that schedule events might handle scheduling incorrectly, thereby removing Content Scheduler's expiration period. Again, deactivating and reactivating Content Scheduler should re-instate the scheduling.

== Screenshots ==

1. The Content Scheduler options screen, where you determine what happens when the expiration date is reached.
2. Content Scheduler can optionally display the expiraton date and status in a column where your Posts and Pages are listed.
3. Scheduling content expiration uses a small, unobtrusive box on your Post and Pages edit screens.

== Changelog ==

= 0.9.9 =
* Changed (de)activation functions to use the $network_wide flag to detect network (de)activation of plugin.

= 0.9.8 =
* FIX: for WordPress 3.3, properly checking for user level allowed to see Content Scheduler fields, values, and shortcodes.
* FIX: tag manipulation. NOTE: Tags must be comma-seperated.
* NEW: DEFAULT date and time handling (Set expiration to 'default' and then default hours / days / weeks will be added to Publish time to get your expiration time. NEEDS documented!)

= 0.9.7 =
* Users can set how often Content Scheduler checks content for expiration. (Helps not overload server if a lot of Posts exist.)
* Notification to Authors now works properly.
* Enhanced tagging ability to add OR remove multiple tags upon expiration. (e.g., +thistag -othertag)
* Added template tag to be used in the loop for showing a Post's expiration date.
* Users can set a default amount of time to add to content for expiration. (e.g., all content could expire 5 days after published date.)
* Fixed use of deprecated "user_level" for minimum user level that can see Content Scheduler controls.
* Fixed bug in shortcode that displays a post's expiration date.
* Brushed up WordPress Multisite / Network support.
* Added new debug info to Settings screen (if user has WP_DEBUG = true in wp-config.php)
* Changed name of Content Scheduler variables from cs-enable-schedule to _cs-enable-schedule and cs-expire-date to _cs-expire-date (underscore hides fields from standard Custom Fields dialog on Edit screens.)

= 0.9.6 =
* Implemented a fix for users with PHP version earlier than 5.3.

= 0.9.5 =
* Added i18n support
* Changed plugin option values that were english strings into numerical values

= 0.9.4 =
* Ensured WordPress-configured timezone is honored upon plugin activation so expiration periods are not inadvertently delayed.

= 0.9.3 =
* Added ability to select minimum user level that can see Content Scheduler fields and shortcodes.

= 0.9.2 =
* Added support for Custom Post Types.
* Added ability to add Tag(s) to expired items.
* Added shortcode to display exipiration time in content.

= 0.9.1 =
* Added the "Expiration period" option on the settings screen. This allows users to tell WordPress how often Content Scheduler expiration times should be checked.

= 0.9 =
* First public release.

== Upgrade Notice ==
