=== WordPress File Monitor ===
Contributors: mattwalters
Donate link: http://mattwalters.net/projects/
Tags: security, files, monitor, plugin
Requires at least: 3.0
Tested up to: 3.0
Stable tag: 2.3.3

Monitor files under your WordPress installation for changes.  When a change occurs, be notified via email.

== Description ==

Monitors your WordPress installation for added/deleted/changed files.  When a change is detected an email alert can be sent to a specified address.

*Features*

- Monitors file system for added/deleted/changed files
- Sends email when a change is detected
- Multiple email formats for alerts
- Administration area alert to notify you of changes in case email is not received
- Ability to monitor files for changes based on file hash or timestamp
- Ability to exclude directories from scan (for instance if you use a cacheing system that stores its files within the monitored zone)
- Site URL included in notification email in case plugin is in use on multiple sites

Sorry for the delayed release for working with WordPress 3.0.  *NOTE* I haven't tested the latest version with multi-site yet, only single site.

== Installation ==

* Upload to a directory named "wordpress-file-monitor" in your wp-content/plugins/ directory.
* Visit Settings page under Settings -> WordPress File Monitor in your WordPress Administration Area
* Configure plugin options
* Optionally change the path to the Site Root.  If you install WordPress inside a subdirectory for instance, you could set this to the directory above that to monitor files outside of the WordPress installation.

== Changelog ==

= 2.3.3 Changes =

- Updated to work with single-site WordPress 3.0.  Haven't tested Multi-site yet.
- Manually removing the plugins database options should cause the plugin to load default values instead of an error.
- Removed errors and warnings that were caused by calling the scan script directly without the correct GET variables.

= 2.3.2 Changes =

- Bug fix for something that may have prevented users first installing the plugin from being able to save their options.

= 2.3.1 Changes =

- Tested with WordPress v2.9

= 2.3 Changes =

- Bug on PHP4 systems fixed.  File scan should operate properly in a PHP4 environment now, BUT TELL YOUR HOST TO UPGRADE PHP!  I feel like I'm enabling you here :P
- Added Site URL to notification emails for those of you running the plugin on more then one site (subject and body)
- Tried to improve the explanation regarding how to properly construct an exclude path
- Minor email tweaks
- add_action() error message hopefully fixed


= 2.2 =

- Added manual scan to admin area (push a button on the settings page for the plugin to perform a scan immediately)
- Added the ability to turn off automated scanning by setting scan interval to 0
- Increase default scan interval to 30 minutes
- Changed it so some of the settings will not auto load as they can be rather large/intensive.  Instead they are loaded as needed.
- Scan is now done via an embedded object instead of prior to the page being served.
- Changed up how exclude paths are handled.  Hopefully this will help performance for some folks that have large sites.

= 2.1 =

* There's a bug fix in this release that takes care of a problem where the monitor might fail to scan.  Thanks to Ozh for discovering this and other suggestions on the plugin.
* Also thanks to Ozh, there should be some further efficiency gains in this release.

Coming soon: further efficiency tweaks to help speed up the scan and I'm also planning to change up how the scan fires to help prevent it from slowing down loading of your site when a scan is performed.  Lots of good things should be in the next release.

= 2.0 =

- *NOTE*: WPMU users, upgrade at your own risk.  I know some of you are using the plugin on WPMU, but I am not actively testing on that platform.  Some of these changes (such as admin alerts) might act weird for your users.
- Added: Administration Area Alert - You can turn on an option on the settings page which will cause an alert box to display on your dashboard when you login if changes have been detected.  It provides a link for you to view the alerts that have been sent and clear the alert.  Alerts are stored sequentially until you clear them.
- Added: SMS/Pager Email Format - This will send a shorter message to the specified email address that simple summarizes the number of Added/Deleted/Changed files.  You can then login to your Administration Area to see what files were changed using the new alert system.
- Added: There's a .pot file in the languages directory if anyone is interested in translating this plugin.  Just send me the language files you generate and I'll add them to the release along with giving you credit for the translation.
- Changes: Pretty major code overhaul.  I'm sure this doesn't matter to the average user, but there are lots of changes to make the plugin more self contained and hopefully more efficient in general.  These efficiencies do not affect using the hash method of detection.  That is still slower then just watching the files timestamps.

= 1.1.1 =

Optional Upgrade: No huge fixes here or feature enhancements.

**Bug Fixes**

- On large sites, it was possible the scan might run more then once at a time, causing performance issues.  This is hopefully taken care of now.  Although apparently some of you have higher traffic sites then mine, so let me know ;)
- You shouldn't lose your settings when you upgrade this time.  If someone does, let me know but pretty sure from my testing that I took care of it.  Sorry for the inconvenience.

= 1.1 =

Added another option allowing you to choose between different email formats.  The default option is detailed, which is the original format.  The second option is 'Subversion' which is a slightly shorter email.  Instead of separating the email into sections, you will be sent a list of the (A)dded, (M)odified, or (D)eleted files with just a simple A/M/D preceding them.

= 1.0.2 =

This is not a required upgrade.  There is a fix in here for PHP 4 compatibility.

= 1.0.1 =

There was a small bug that could have prevented hash checks from working in the 1.0 release.  This should hopefully take care of that.

= 1.0 =

Added the ability to choose between checking file timestamps (faster/less secure) and hashes (slower/more secure).

= 0.99.1 =

Fixed a bug in exclusion paths.  Recommended upgrade.