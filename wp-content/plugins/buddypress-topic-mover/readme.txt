=== Buddypress Topic Mover ===
Contributors: dwenaus
Tags: buddypress, bp, forum, moderate, group, move, administrator, moderator, bp, ajax, forum topic, topics, forums, admin, groups, mod
Requires at least: WP 2.9.2	
Tested up to: WP 3.1
Stable tag: trunk

Allows BuddyPress group mods and admins to move a single forum topic to another group, updating the activity items as well.

== Description ==

This is an improved version of the now-outdated BuddyPress Forum Topic Mover plugin by twodeuces. Thanks!

Sometimes your users may inadvertently place a forum topic under the incorrect group in BuddyPress. This plugin helps moderators and administrators move those topics to the correct groups. The list of groups to move to is loaded dynamically via ajax to cut down on page load times for large BuddyPress installs. The plugin also plays nicely with the Anncounce Group plugin, so users can only move a topic to an announcement group if they are a moderator or admin of that announce group. 

A few notes about BuddyPress Forum Topic Mover:

*   Creates a simple drop down menu and move topic button.
*	uses ajax to generate drop down menu
*   Only shows for those users with admin or moderator privileges.
*	plays nice with non-standard bbpress installs
*	secure - uses nonce protection
* 	works great with default BP theme (or any theme that uses the bp_group_forum_topic_meta action hook)
*	group meta data is updated properly during topic move
*	activity stream items are updated with the new topic URL

This plugin is different than BP Move Topics. That plugin will let you move ALL topics from one group to another using the admin backend. My plugin lets you to move individual topics from the front end. They can work together.

== Installation ==

Installation Steps:

1. Install plugin. 
2. go to a group you are an admin or mod of. then view a topic page. Click 'Move Topic' in the admin links for that topic (near delete topic, etc.). choose a new group then click 'Move'

== Frequently Asked Questions ==

= How come after I move it, it looks like it is still in the same group? =

The topic has moved but you are still in the old group. 

== Screenshots ==

1. plugin in action showing the drop down menu of choices to move the current topic to.

== Changelog ==

= 2.5.1 =
Major change to plugin to reset the activity stream when a topic item is moved. Now it plays much better with BuddyPress Group Email Subscription

= 2.0 =
Total re-write by Deryk Wenaus (dwenaus) to work with BP 1.2.6+ and to work better all around. 

= 1.0 =
Initial Public Release by twodeuces.