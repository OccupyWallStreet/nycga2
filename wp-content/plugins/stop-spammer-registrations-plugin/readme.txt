=== Stop Spammer Registrations Plugin ===
Tags: spam, comment, registration, login, spammers,MU, StopForumSpam, Honeypot, BotScout,DNSBL, Spamhaus.org, Ubiquity Servers, HTTP_ACCEPT, disposable email
Donate link: http://www.blogseye.com/buy-the-book/
Requires at least: 3.0
Tested up to: 3.5
Contributors: Keith Graham
Stable tag: 3.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The Stop Spammer Registrations Plugin checks comments and logins 15 different ways to block spammers.

== Description ==
Eliminates 99% of spam registrations and comments. Checks all attempts to leave spam against StopForumSpam.com, Project Honeypot, BotScout, DNSBL lists such as Spamhaus.org, known spammer hosts such as Ubiquity Servers, disposable email addresses, very long email address and names, and HTTP_ACCEPT header. Checks for robots that hit your site too fast, and puts a fake comment and login screen where only spammers will find them. In all the plugin uses 15 different strategies to block spammers. 

The Stop Spammer Registrations Plugin now checks for spammer IPs much earlier in the comment and registration process. When it detects a spammer IP, the plugin stops WordPress from completing any further operations and an access denied message is presented to the spammer. You control the access denied message, or you can redirect the spammer to another page or website.

How the plugin works: 

This plugin checks against StopForumSpam.com, Project Honeypot and BotScout to to prevent spammers from registering or making comments. The Stop Spammer Registrations plugin works by checking the IP address, email and user id of anyone who tries to register, login, or leave a comment. This effectively blocks spammers who try to register on blogs or leave spam. It checks a users credentials against up to three databases: Stop Forum Spam, Project Honeypot, and BotScout. Optionally checks against Akismet for Logins and Registrations. 

Optionally the plugin will also check for disposable email addresses, check for the lack of a HTTP_ACCEPT header, and check against several DNSBL lists such as Spamhaus.org. It also checks against spammer hosts like Ubiquity-Nobis, XSServer, Balticom, Everhost, FDC, Exetel, Virpus and other servers, which are a major source of Spam Comments. 

Rejects very long email addresses and very long author names since spammers can't resist putting there message everywhere. It also rejects form POST data where there is no HTTP_REFERER header, because spammers often forget to include the referring site information in their software.

The plugin will install a "Red Herring" comment form that will be invisible to normal users. Spammers will find this form and try to do their dirty deed using it. This results in the IP address being added to the deny list. This feature is turned off by default because the form might screw up your theme. Turn the option on and check your theme. If the form (a one pixel box) changes your theme presentation then turn the feature off. I highly recommend that you try this option. It stops a ton of spam. 

The plugin can check how long it takes a spammer to read the comment submit form and then post the comment. If this takes less than 5 seconds, then the commenter is a spammer. A human cannot fill out email, comment, and then submit the comment in less than 5 seconds.

Limitations: 

StopForumSpam.com limits checks to 10,000 per day for each IP so the plugin may stop validating on very busy sites. I have not seen this happen, yet. The plugin will not stop spam that has not been reported to the various databases. You will always get some comments from spammers who are not yet reported. You can help others and yourself by reporting spam. If you do not report spam, the spammer will keep hitting you. This plugin works best with Akismet. Akismet works well, but clutters the database with spam comments that need to be deleted regularly, and Akismet does not work with spammer registrations. Since Akismet does not check registrations and logins, the plugin will use the Akismet database to check these events, too. 

API Keys: 

API Keys are NOT required for the plugin to work. Stop Forum Spam does not require a key so this plugin will work immediately without a key. The API key for Stop Forum Spam is only used for reporting spam. In order to use the Project HoneyPot or BotScout spam databases you will need to register at those sites and get a free API key. 

History: 

The Stop Spammer Registrations plugin keeps a count of the spammers that it has blocked and displays this on the WordPress dashboard. It also displays the last hits on email or IP and it also shows a history of the times it has made a check, showing rejections, passing emails and errors. When there is data to display there will also be a button to clear out the data. You can control the size of the list and clear the history.
If a user tries to log in and passes all checks for spammers an icon appears next to the IP address. Only users you know should be allowed to login, so by clicking the icon, you can add the IP to your black list. 

Cache: 

The Stop Spammer Registrations plugin keeps track of a number of spammer emails and IP addresses in a cache to avoid pinging databases more often than necessary. The results are saved and displayed. You can control the length of the cache list and clear it at any time. The plugin caches IP addresses that do not fail, assuming that they may be valid users. In order to prevent re-checking these IP addresses, the plugin stores the last two IP addresses that passed all tests.

Reporting Spam: 

On the comments moderation page, the plugin adds extra options to check comments against the various databases and to report to the Stop Forum Spam database. You will need a Stop Forum Spam API key in order to report spam/ 

Network MU Installation Option: 

If you are running a networked WPMU system of blogs, you can optionally control this plugin from the control panel of the main blog. By checking the "Networked ON" radio button, the individual blogs will not see the options page. The API keys will only have to entered in one place and the history will only appear in one place, making the plugin easier to use for administrating many blogs. The comments, however, still must be maintained from each blog. The Network buttons only appear if you have a Networked installation.

Requirements: 

The plugin uses the WP_Http class to query the spam databases. Normally, if WordPress is working, then this class can access the databases. If, however, the system administrator has turned off the ability to open a URL, then the plugin will not work. Sometimes placing a php.ini file in the blog’s root directory with the line "allow_url_fopen=On" will solve this. 
There is a button that allows you check access to the StopForumSpam database from the plugin Options page. This will tell you if the host allows opening of remote URL addresses.


 
== Installation ==
1. Download the plugin.
2. Upload the plugin to your wp-content/plugins directory.
3. Activate the plugin.
4. Under the settings, add the appropriate API keys (optional). Update the white list. Set any of the optional items and limits.

== Changelog ==

= 1.0 =
* initial release 

= 1.2 =
 * renumber releases due to typo
 
= 1.3 =
 * Check the IP address whenever email is checked.
 
= 1.4 =
 * Checks the user name. Cache failed attempts with option to clear cache. Cleans up after itself when uninstalled. 

= 1.5 =
* fixed a bug where the the admin user was cached in error.

= 1.6 =
* Improved caching to help stop false rejections.
 
= 1.7 =
* Included signup form, that I forgot to add before. Cached data is automatically expired after 24 hours.
 
= 1.8 =
* fixed the cache cleanup (again). Changed the name in the titles and menus of the plugin to reflect that it does more than stop registrations.

= 1.9 =
* Added link to report spam to StopForumSpam.com database.

= 1.10 =
* Improved the access to StopForumSpam.com database. Fixed white space at end of plugin.
 
= 1.11 =
* Stored the StopForumSpam API Key. Fixed a possible security hole on the settings page.
 
= 1.12 =
* Fixed typo error.
 
= 1.13 =
* Changed Evidence field to spam URL or content

= 1.14 =
* Changes suggested by Paul at StopForumSpam. Fix bug in zero history data. There has been much interest in the plugin so there has been lots of feedback. I am sorry for all the updates, but they are all good stuff.

= 1.15 =
* Options added. 1) Reject if Accept header not found. Spammers use some kind of lazy approach that does not send the HTTP_ACCEPT header. All real browsers have this header. 2) Check on BL Blacklist. If for some reason the IP and email pass on the StopForumSpam db you can have a second check on Project Honeypot. 3) Added a white list in case there are IPs or emails that have problems. 4) Stopped checking for Usernames because of too many false positives. 4) Made checking for emails optional. Most spammers use bogus or random emails anyway. 5) Ability to recheck comments against the HoneyPot db from the comments admin form.

= 1.16 =
* Added RoboScout.com spam check to IP address. Added limits to checking to allow know spammers who are not recent spammers or do not have many spam reported. Added a complete list of passed and rejected login attempts. Fixed a bug introduced in 1.15. Fixed check on accept headers that prevented it from working.

= 1.17 =
* Fixed another bad bug. Added a warning if the host does not allow URL fopens. Reduced memory requirements. Cache less information.
This has some functions partially complete, but I had to release as is to fix the bugs that appear on new install. It's my own fault, because last time I did not test from a clean WP install.

= 2.0 =
* Made the plugin WPMU aware. Streamlined some of the code. Limited the cached spam sizes to reduce memory overhead. Changed the way that the plugin decides when to check an IP and email. This will help it when working with other plugins. It also checks in multiple places in case the is_email() function is not called. It allows admins to change the minimum requirements for spam, forgiving spammers who have few incidents or have not spammed for a period of time.

= 2.10 =
* Fixed the way the cache is sorted. Added DNSBL support for spamhaus, dsbl, sorbs, spamcop, ordb, and njabl. These are email spam databases and they get only a small portion of the comment spam, but some is better than none. Added a list of common disposable email sites so that users who use disposable sites can be blocked. The list is only popular sites and is not exhaustive. Real commentators probably won't use the disposable sites, but some bloggers may be nervous about blocking them, so it is optional. Divided the options into a stats and a parameters wp_option array. Something in spam, probably a foreign language character, has been breaking the options causing the blog to "forget" when the stored array is broken. Now, when the stats array breaks, the configuration items will still be available. Rewrote the MU options, although it is not tested on subdomain installations.

= 2.20 =
* Fixed several networked blog issues. Added a dummy email address so that pingbacks can be reported. Added Multisite Maintenance. Fixed a few minor bugs. Testing use of X-Forwarded-For HTTP IP address when the blog is behind a proxy. I cannot test this because I don't have access to a site behind a proxy. Please report if the X-forwarded-for header handling is broken.

= 3.0 =
* Restructured the Plugin completely, changing many of the ways it works. Changed the points and places where spam is checked. Spam is now being checked for much earlier. Added an Access denied screen. Optionally block Ubiquity Servers. Use AJAX to report Spam so that there is no need to open a new window.

= 3.1 =
* Changed access to SFS db to stop false positives

= 3.2 =
* Added automatic addition of admins to IP white list. Added ability to specify where plugin actions work. Added WP API key update for those who don't use Akismet. Added checks for long names and emails. Added HTTP_REFERER checks. Added a check so users can see if they have access to the StopForumSpam database. Added a long list of known Spam Hosting company IP addresses. 

= 3.3 =
* Changed way arrays are searched. It was possible that IP addresses were not found in lists. Added a "Red Herring" bogus comments form that stops a huge amount of spam. Repaired delete option.

= 3.4 =
* Fixed an issue with Red Herring inserting invalid data into feeds. Added a list of spam robot user agents. Added a timeout to the comment submission forms to ban spammers who take less than 5 seconds to fill out and submit a form. Changed the way the plugin loads, speeding up WordPress. Most functions do not load unless the plugin is processing a form. There is no need to check for spammers unless they are actually in the process of leaving a comment or logging in. Mail and XMLRPC checks load all the time. Akismet may get the spammer before this plugin does resulting in more spam in the Akismet spam queue, but it doesn't matter as long as the spammer is stopped. Added an optional JavaScript trap to the comment form. Users who do not have JavaScript enabled will be marked as spammers. Disable this if you have a blog for paranoids.

= 3.5 =
* Fixed typo. Although I tested for a week in 5 different sites, this bug didn't come up.

= 3.6 =
* Fixed issue with some web servers that did not set server variables such as SCRIPT_URI and REQUEST_URI. These were troubling to those with hosting software that ignored these variables. Fixed an issue on saving of parameters. Added a hook to 404 errors so that missed hits on wp-login can be considered malicious. Removed default doubleclick link that was causing problems.

= 3.7 =
* fixed several bugs in Options page. Reformatted Options page to make it easier to view.

== Frequently Asked Questions ==

= Help, I'm locked out of my Website =
Not everyone who is marked as a spammer is actually a spammer. It is quite possible that you have been marked as a spammer on one of the spammer databases. You can delete the "stop-spammer-registrations-plugin" in your wp-content/plugins folder and you will be able to get in. There is no "back door", because spammers could use it.
Reinstall the plugin and check off the box, "Automatically add admins to white list". Then save your settings. This puts your IP address into the white list. You should be able to get in. Then inspect the log to determine why you were locked out. Was your email or IP address marked as spam in one of the databases? If so, contact the website that maintains the database and ask them to remove you.

= I can't log into WordPress from my Android/iPhone app. =
Check your log files to find out exactly why the app was rejected. It usually is because the HTTP_REFERER header was not sent correctly. This is one sign of badly written spam software. It is also, unfortunately, a sign of badly written login software. Uncheck the box on the Stop Spammer settings page "Block with missing or invalid HTTP_REFERER".

= My blog started sending spammers to a Cell phone site =
In the 3.5 version I added the ability to send spammers to an alternate URL, and I included an example that you could use for spamming spammers. I made a mistake in my code that caused this feature to be stuck "ON". It was a stupid mistake on my part, and not malicious. Your site was not hacked. It was my own dumb fault. The plugin has become way to big and complicated and I miss things in testing. Rest assured that you can't spam spammers. These are robots creating the spam, and no flesh and blood spammers ever see the results of the redirect.

= I see errors in the error listing below the cache listing =
It could be that there is something in your system that is causing errors. Copy the errors and email them to me, or paste them into a comment on the WordPress plugin page.

= You plugin is stopping new registrations, but how do I clean up existing spam registrations? =
Unfortunately, WordPress does not record the IP address of User registrations. This is a design flaw in WordPress. They do record the IP of comments. I cannot run a check against logins without their IP address, so you have to remove users the old fashioned way, one at a time. 
You might try listing the emails of all registered users, and then deleting them. You can then ask all users to re-register, but that would probably annoy your legitimate users.

= I have a cool idea for a feature for Stop-Spammer-Registrations-Plugin. =
Most of the features in the plugin have come from the users of the plugin. By all means stop by my website and leave a comment. I read all of them, and if the are feasible, I try to include them.

= You should have a PayPal button =
No I shouldn't. I don't make any money off of the plugin, except that one or two plugin users out out thousands buy my book. I have no reason to believe that anyone would click a PayPal button. If I make 40&cent; per book on one or two books a month, I get the satisfaction that some might actually read my stories. The money is not important.


== Screenshots ==

1. A shot of the Spammer History settings page.

== Support ==
= Rate the Plugin =
This plugin is free and I expect nothing in return. Please rate the plugin at: http://wordpress.org/extend/plugins/stop-spammer-registrations-plugin/
= Buy my book =
If you wish to support my programming, buy my book:
 
http://www.blogseye.com/buy-the-book/: Error Message Eyes: A Programmer's Guide to the Digital Soul
= Some of my many plugins =
http://wordpress.org/extend/plugins/permalink-finder/ : Permalink Finder Plugin

http://wordpress.org/extend/plugins/open-in-new-window-plugin/ : Open in New Window Plugin

http://wordpress.org/extend/plugins/kindle-this/ : Kindle This - publish blog to user's Kindle

http://wordpress.org/extend/plugins/stop-spammer-registrations-plugin/ : Stop Spammer Registrations Plugin

http://wordpress.org/extend/plugins/no-right-click-images-plugin/ : No Right Click Images Plugin

http://wordpress.org/extend/plugins/collapse-page-and-category-plugin/ : Collapse Page and Category Plugin

http://wordpress.org/extend/plugins/custom-post-type-list-widget/ : Custom Post Type List Widget


