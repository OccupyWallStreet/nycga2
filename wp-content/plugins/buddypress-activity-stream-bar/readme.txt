=== BuddyPress Activity Stream Bar ===
Contributors: xberserker
Donate Link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=Z7MUNFVW7R8XN
Tags: buddypress, activity stream, activity, bar, stream, live, rotate, groups, update, reply, forum, topic, replies, friends
Requires at least: 2.9.2 & Buddypress 1.2.5.2 or higher
Tested up to: 3.0.4, BuddyPress 1.2.7
Stable tag: 1.3.2

Adds a static bar at the bottom of every page of your website. Which displays the latest 20 BuddyPress Activities and rotates threw them.

== Description ==

Adds a static bar at the bottom of every page of your website. Which displays the latest 20 BuddyPress Activities and rotates threw them.

This way all your members/visitors can see at a glance all the latest activity on your website!

There are also buttons so you can go forward and back in the activity stream and a close/open button in the bottom right.

== Installation ==

1. Activate the "BuddyPress Activity Stream Bar" Plugin
2. See FAQ if you want to change how often the activity rotates.

== Frequently Asked Questions ==

= I want to change how often the activity changes =

Go to this file `/plugins/buddypress-activity-stream-bar/bp_activity_bar.js` and search for `bprotatetime = 7000;` change 7000 to whatever you'd like. 1000 = 1 Second.

= I want to change how many activities show up =

Go to this file `/plugins/buddypress-activity-stream-bar/bp_activity_bar.php` and search for this line `<?php if ( bp_has_activities('max=20') ) : ?>` You can change it to any number between 1-20.

= I only want my friends activity to show up =

Go to this file `/plugins/buddypress-activity-stream-bar/bp_activity_bar.php` and search for this line `<?php if ( bp_has_activities('max=20') ) : ?>` Replace it with this `<?php if ( bp_has_activities('max=20','scope=friends') ) : ?>`

= I want this displayed at the top of the page instead =

Only do this if you don't have the BuddyPress Admin Bar enabled. Go to this css file `/plugins/buddypress-activity-stream-bar/bp_activity_bar.css` Find the lines that start with the following `#footeractivity #innerbpclose #innerbpopen` and change this `bottom:0px;` to this `top:0px;`

== Screenshots ==

1. BuddyPress Activity Stream Bar in action! Complete with forward and back buttons.

== Upgrade Notice ==

* None at this time.

== Changelog ==

**Version 1.3.2** *(March 7th, 2011)*

* Bug Fix: Will load correctly if wordpress is installed in a sub-folder.

**Version 1.3.1** *(Jan 9th, 2011)*

* Bug Fix: Will now show the bar only after it's fully loaded.

**Version 1.3** *(Jan 7th, 2011)*

* Added buttons so you can go forward and back in the activity stream.
* Added a button to the bottom right to close/open the activity stream.

**Version 1.2** *(Jan 4th, 2011)*

* Fixed error with linking to the css/js files.

**Version 1.1** *(Jan 4th, 2011)*

* First release