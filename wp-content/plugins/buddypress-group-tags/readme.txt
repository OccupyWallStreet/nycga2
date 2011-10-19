=== BuddyPress Group Tags ===
Contributors: dwenaus
Donate link: http://www.bluemandala.com/plugins/
Tags: buddypress, group tags, group tag cloud, group, groups, tag, tags, bp, tag cloud, category, categorize, organize, activity, group activity
Requires at least: 2.9 (BP 1.2)
Tested up to: 3.2.1 (BP 1.5)
Stable tag: trunk

Categorize BuddyPress groups using tags. Show a clickable tag cloud above the group listings or as a widget.

== Description ==

This plugin allows you to assign tags to BuddyPress groups in order to categorize them. You can then show a clickable tag cloud above the group 
listings or as a widget. BuddyPress 1.5 friendly. 

New to version 2.0. You can show group activity stream based on tag. For example, if you have ten groups tagged 'sports' you can show a combined activity feed for just those groups. There is a widget and a shortcode to show this group tag activity. It's powerful stuff for larger sites. using the gtags_group_activity() function you can even find the activity of groups that share two tags, or you can show activity for two tags added together. 

There is an admin where you can turn off and on different aspects of the group tags as well as customize the tag cloud.

Translations:
Swedish: Malin Jonsson - Oddalice.se
please email translations to deryk@bluemandala.com so I can include them in the next update. 

== Installation ==

1. Upload `bp-group-tags` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. To start adding tags go to the admin section of any group. You'll see the Group Tags field at the bottom. 
1. install the Group Tags widget if you like. 
1. optionally edit settings Group Tags admin page in the wordpress back end.
1. Once you have activity in your groups, you can then use the Group Tags Activity widget or shortcode to show activity from all the groups of a specific tag.

== Frequently Asked Questions ==

= How can I get feature XYZ implemented? =
Send a paypal donation to deryk@bluemandala.com and I'll happily do it. If I don't do it, I'll refund the donation. I'm an honest guy. Small donations also ensure continued plugin development. 

== Screenshots ==

1. The group tag cloud
1. Tag adding in group admin
1. Admin interface

== Changelog ==

= 2.0.1 = 
* compatible with BuddyPress 1.5, added powerful group activity for tag widget/shortcode, new way to categorize tags using drop down menu. minor fix for depreciated function.

= 2.0 = 
* compatible with BuddyPress 1.5, added powerful group activity for tag widget/shortcode, new way to categorize tags using drop down menu.

= 1.4.6 = 
* added swedish translationg thanks to Malin Jonsson - Oddalice.se

= 1.4.5 = 
* added nice admin to control tag cloud appearance. allow include and exclude of tags. widgets now have multiple instances and include/exclude tags. NOTE: you'll need to re-add your group tags widget due to the new format.

= 1.4.1 = 
* added nice admin to control tag cloud appearance. fixed a bunch of other stuff. fixed tiny bug

= 1.4 = 
* added nice admin to control tag cloud appearance. fixed a bunch of other stuff

= 1.3.3 =
* default view is to not show group tags in directory, instead show an expandable link. Good for when there are many tags. 

= 1.3.1 =
* fixed the bug introduced in 1.2.2 where tags were not saved on group creation (ugh!)

= 1.3 =
* fixed the bug introduced in 1.2.2 where tags were not saved on group creation

= 1.2.3 =
* cleaned up depreciated tags

= 1.2.2 =
* fixed issue where you could not delete the last group tag

= 1.2.1 =
* fixed issue with tags with spaces in tags widget

= 1.2 =
* Internationalized plugin

= 1.1.2 =
* added group widget, added tag links for each group, bookmarkable tags urls, fixed the folder structure

= 1.0 =
* made everything ajax compatible, you can now click to add tags the group admin

= 0.7 =
* added listing of popular tags below tag adding/editing field

= 0.6.1 =
* Initial release after internal testing

= 0.6 =
* Initial release after internal testing

= 0.5 =
* Initial release.