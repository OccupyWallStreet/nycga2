=== BuddyPress Rate Forum Posts ===
Contributors: dwenaus
Donate link: http://www.bluemandala.com/plugins/
Tags: buddypress, rate, rating, forum, forums, group, groups, karma, points, reputation, post rating, bp
Requires at least: 3.0 (BP 1.2.4)
Tested up to: 3.2.1 (BP 1.5)
Stable tag: trunk

Users can rate forum posts in BuddyPress. Good posts are highlighted and poor posts diminished. Highlighted karma points shown for each user.  

== Description ==

BuddyPress 1.5 compatible. Users can rate forum posts in BuddyPress. Good posts are highlighted and poor posts diminished. Highlighted karma points shown for each user. 

(Note: a huge revision of this plugin is about 95% done but not yet released. It allows you to rate or vote or like any buddypress or wordpress object. The plugin is looking for a patron.)

Features & Options:
* Post rating and responses via ajax.
* Posts can only be rated once: thumbs up or thumbs down. This can be replaced with 'like' and 'dislike'. Super-admins can rate a post as much as they want, however group admins can only rate once.
* Good posts are highlighted light yellow (default +10), great posts are highlighted bright yellow (default +25), poor posts become slightly opaque (default -3), and very poor posts have their content hidden and viewable via an ajax link. (default -6). 
* Karma points are given to each user based on their posting history. Karma points can be based on total accumulated ratings (good for quiet sites), average post rating (good for very busy sites) or a balance of the two (good for medium traffic sites). The default is the later.
* User Karma points are highlighted depending on number of karma points. There are 5 levels (based on natural log). Default, above 7p, above 19p, above 51p, and above 138p. The scale goes from grey to bright yellow. These values can be edited in bp-rate-forum-posts.php
* Topic ratings are shown in group and forum listings view using the rating of the initial post. 
* Only logged-in users can rate posts. 
* No new database tables are created.
* Post Karma shows on Member view
* There is an admin screen to change karma levels, post highligting and diminishing values, and karma calculation method
* No post rating when the topic is closed (except admin)

Thanks to Intense Debate for their thumb graphics and good layout.

== Installation ==

1. Upload `bp-rate-forum-posts` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Begin rating posts! (Note that only system admins can rate posts as much as they want, others can only rate once.)
1. Optionally edit plugin settings in the wordpress backend under BuddyPress->Rate Forum Posts. 

== Frequently Asked Questions ==

= How can I get feature XYZ implemented? =
Contact me at deryk@bluemandala.com for a quote. I'll usually get around to it in a day or two. 

== Screenshots ==

1. Post rating in action. Read the posts for description of each feature.
2. A glimpse of the back end admin options

== Changelog ==

= 1.6.6 =
* compatible with BuddyPress 1.5, user rating history bug fix

= 1.6.5 =
* compatible with BuddyPress 1.5, fixed rating.js loading issue

= 1.6.4 =
* compatible with BuddyPress 1.5

= 1.6.3 =
* added option to hide ratings in discussion form listings

= 1.6.2 =
* fixed bug which produced odd mysql error messages at times

= 1.6 =
* Re-wrote many parts of plugin with faster and cleaner code. Disabled rating for closed topics. Removed legacy code for pre BP 1.2.4 installs.

= 1.5.1 =
* changed default karma calculation to Total Karma point

= 1.5 =
* added option not to show negative karma, fixed bug where hidden posts had no 'show' link

= 1.4.3 =
* Compatibility with BP 1.2.4

= 1.4.2 =
* hide post rating when it's zero

= 1.4.1 =
* fixed length null error. Plugin is now BP 1.2.4 compatible

= 1.3.2 =
* fixed very minor javascript bug again

= 1.3.1 =
* fixed very minor javascript bug in forums section

= 1.3 =
* added ability to change karma calculation. fixed minor bug

= 1.2.1 =
* added ability to hide karma, fixed karma on members page, and fixed bug for separate bbpress installs

= 1.2 =
* added ability to hide karma, fixed karma on members page

= 1.1 =
* added admin options

= 1.0.3 =
* fixed conflict with admin javascript

= 1.0.2 =
* fixed folder name bug

= 1.0 =
* Initial release.