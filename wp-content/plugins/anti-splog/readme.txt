=== Anti-Splog ===
Contributors: WPMUDEV, uglyrobot
Tags: splog, splogs, spam, multisite, buddypress, signup, captcha, wpmu
Requires at least: 3.3
Tested up to: 3.5.1
Stable tag: 2.1

The ultimate plugin and service to stop and kill splogs in WordPress Multisite and BuddyPress, from WPMU DEV.

== Description ==

This is the plugin every Wordpress Multisite or Buddypress admin has been waiting for! Announcing Anti-Splog, the ultimate plugin and service to stop and kill splogs in WordPress Multisite
[youtube http://www.youtube.com/watch?v=4BR1ai2w-B0&hd=1]

This plugin goes way beyond any existing splog plugin for Multisite because at its core is the new Anti-Splog API service hosted at WPMU DEV Premium. This means that Anti-Splog not only prevents/limits bots, it also identifies human and existing spammers automatically and with great efficiency.

= Four Layers of Protection =
Anti-Splog works with four layers of protection to provide the ultimate in splog killing power. Up front we have 5 methods to choose from that can limit or stop those evil automated bots that are flooding your Multisite install with splogs. These can be fairly effective, but with our experience on sites like Edublogs, a large number of splogs are manually created. While every other splog prevention plugin stops here and leaves your site unprotected, ours goes 200% further.

If the splogger makes it past the initial roadblocks the plugin sends all their signup information to our API server, and we decide if it's suspicious enough to mark the blog as spam right off the bat. The beauty of our Anti-Splog API service is that we crowdsource data of tens of thousands of splogs from Edublogs and other Anti-Splog users. If anyone else has run into that splogger or spam post you don't have to worry about it on your site.

Sometimes our Anti-Splog service may not have enough information to mark a blog as spam right at signup. But not to worry, that's when Anti-Splog pulls out its most potent (and amazing) weapon: Post monitoring. The second a splogger writes a spam post, no matter how cleverly disguised, our API service analyzes it and boom, that splog is shutdown. It's hard to describe the utter satisfaction we get watching splogs destroyed every few minutes live before our eyes at Edublogs. Evil sploggers have finally met their match with Anti-Splog!

And if all that wasn't enough, we include an incredibly well thought out moderation queue for blogs. When our API service returns a suspicious score that's not high enough to auto spam, they get sent to the “Suspected Blogs” queue instead. From there you can monitor suspicious blogs until you know whether they are spam or not. When you are sure just mark them as spam or ignore them to remove them from the queue. Our API service will learn from every action you take, becoming more and more accurate.

= Anti-Splog Features =
1. **Signup Prevention** - these measures are mainly to stop bots. User friendly error messages are shown to users if any of these prevent signup. They are all optional, require no API key and include:

	* *Limiting the number of signups per IP per 24 hours* - this can slow down human spammers too if the site clientele supports it.
	* *Changing the signup page location every 24 hours* - this is one of the most effective yet still user-friendly methods to stop bots dead.
	* *Human tests* - answering random user defined questions, picking the cat pics, or reCAPTCHA. (see screenshots)
	* *Pattern Matching* - *NEW!* checking site domains, titles, or usernames against your defined set of regular expressions.

2. **The API** - when signup is complete (email activated) and a blog is first created, or when a user publishes a new post it will send all kinds of blog and signup info to our premium server where we will rate it based on our secret ever-tweaking logic. Our API will return a splog Certainty number (0%-100%). If that number is greater than the sensitivity preference you set in the settings (80% default) then the blog gets spammed. Since the blog was actually created, it will still show up in the super admin area (as spammed) so you can unspam later if there was a mistake (and our API will learn from that). Note that this service requires an API key.
3. **The Moderation Queue** - for existing blogs or blogs that get past other filters, the queue provides an ongoing way to monitor blogs and spam or flag them as valid (ignore) them more easily as they are updated with new posts. Also if a user tries to visit a blog that has been spammed, it will now show a user-friendly message and form to contact the admin for review if they think it was valid. The email contains links to be able to easily unspam or bring up the last posts. The entire queue is AJAX based so you can moderate blogs with incredible speed, not having to wait for the page to reload on every action. Click an action link (like spam) and it flashes and instantly disappears!

	* *Suspected Blogs* - this list pulls in any blogs that the plugin thinks may be splogs. It pulls in blogs that have a greater that 0% certainty as previously returned by our API, and those that contain at least 1 keyword in recent posts from the keyword list you define. The list attempts to bring the most suspected blogs to the top, ordered by # of keyword matches, then % splog certainty (as returned by the API), then finally by last updated. The list has a bunch of improvements for moderation, including last user name, last user IP, links to search for or Spam any user and their blogs or blogs tied to an IP (incredibly powerful, be careful with that one!), ability to Ignore (dismiss) valid blogs from the queue, and view a list of recent posts and instant previews of their content or the entire blog without leaving the page (the most time saving feature of all!)
	*	*Recent Splogs* - this is simply a list of all blogs that have been spammed on the site ever, in order of the time they were spammed. The idea here is that if you make a mistake you can come back here to undo. Also if a user complains that a valid blog was spammed, a review link will be sent to your email so you can quickly pull it up here and see previews of the latest posts or entire blog to confirm (normally you wouldn't be able to see blog content at all for spammed blogs).
	* *Ignored Blogs* - If a valid blog shows up in the suspect list, simply mark it as ignored to get it out of there. It will then show in the ignored list just in case you need to undo.

Time to clean up your WordPress Multisite network and make some sploggers really mad!

== Installation ==
= To Install: =

1.  Download the Anti-Splog plugin file
1.  Unzip the file into a folder on your hard drive
1.  Upload the `/anti-splog/` folder to the `/wp-content/plugins/` folder on your site
1.  Visit Network Admin -> Plugins and Network Activate it there.
1.  Upload `blog-suspended.php` into the `/wp-content/` folder on your site

= To Set Up And Configure Anti-Splog =
* You can find <a href='http://premium.wpmudev.org/project/anti-splog/installation/'>in-depth setup and usage instructions with screenshots here &raquo;</a>

== Frequently Asked Questions ==

= Can I use this plugin for non-multisite WP installs? =
No, this plugin is only compatible (and useful) with Multisite installs.

= Do I need to be a paid WPMU DEV member? =
A current WPMU DEV membership is needed to get an API key to access our powerful splog-checking service. **It is not required however to use the many additional signup protections this plugin provides**.

Here are some of the signup protection features you don't need an API key for:

* Limiting the number of signups per IP
* Changing the signup page location
* Random user defined questions
* ASSIRA - picking the cat pictures
* reCAPTCHA
* Moderation Queues

= Is this BuddyPress compatible? =
Anti-Splog is fully BuddyPress compatible with the exception of auto-renaming wp-signup.php. Note that it does not yet protect against spam users or their entries in status updates, forums, activity streams, etc.

= How do I get support? =
We provide comprehensive and guaranteed support on the <a href='http://premium.wpmudev.org/forums/tags/anti-splog'>WPMU DEV forums</a> and <a href='http://premium.wpmudev.org/live-support/'>live chat</a> only.

== Screenshots ==

1. Anti-splog tank of doom!
2. Limiting the number of signups per IP
3. Changing the signup page location every 24 hours
4. Random user defined questions
5. ASSIRA - picking the cat pictures
6. reCAPTCHA protection
7. Recent splogs queue, notice all the instant actions!
8. Suspected Blogs queue
9. Instant spamming of an entire IP address
10. Instant post previews
11. Instant full blog previews
12. Splog review form
13. Anti-splog process Flowchart
14. Site/Splog creation statistics

== Changelog ==

= 2.1 =
* Add in local IP blacklist blocking
* Fix a bug in post checking, tags not added to api call properly

= 2.0.1 =
* Fix some PHP notices
* Fix blog deletion links on moderation lists

= 2.0 =
* Refreshed menu structure and admin screens
* New Pattern Matching functionality to block bots by signup patterns
* New Are You A Human PlayThru game captcha
* New blog and slog creation stats screen with pretty graphs
* Add tags to API call for more accurate classification
* Add splogging command capability to admin toolbar
* Fixed various bugs/notices

= 1.1.1 =
* Fix UN notice

= 1.1 =
* Preparations for free release
* Usability/text updates to admin pages
* Ability to use your own custom wp-signup template by installing in `wp-content/custom-wpsignup.php`
* Another bug fix in splog review form, make sure to overwrite the `wp-content/blog-suspended.php` file.
* Depreciated function argument fixes

= 1.0.7 =
* Bug fix in splog review form, make sure to overwrite the `wp-content/blog-suspended.php` file.

= 1.0.6.1 =
* Tiny bug fix for WP 3.1

= 1.0.6 =
* WP 3.1 compatibility - This version is NOT backwards compatible!
* Changed install location for autoupdate capability
* Fixed bugs with deleting blogs on Anti-Splog pages
* Added default .po file for translation

= 1.0.5 =
* Fixed email spam link
* Small bug fixes

= 1.0.4 =
* WP 3.0 Multi-Site compatibility. This version is NOT backwards compatible.

= 1.0.3 =
* Bug fix on dynamic signup page when registration is set to users only

= 1.0.2 =
* Fixed major bug preventing recaptcha, asirra, admin questions from working with BP
* Added support for spamming/unspamming users of blog when it is spammed/unspammed
* Highlights in red the spam status of users in Anti-Splog queues

= 1.0.1 =
* Fixed bug conflicting with Supporter free trials

= 1.0.0 =
* Initial Release.

152976-1362278610