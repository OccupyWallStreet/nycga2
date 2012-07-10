=== Plugin Name ===
Contributors: MrMaz
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=8591311
Tags: wpmu, buddypress, social, networking, links, rich media, embed, youtube, flickr, metacafe
Requires at least: PHP 5.2, WordPress 3.x, BuddyPress 1.2.6
Tested up to: PHP 5.2.x, WordPress 3.x, BuddyPress 1.2.6
Stable tag: 0.5

BuddyPress Links is a drop in link and rich media sharing component for BuddyPress 1.2.x

== Description ==

#### What is BuddyPress Links?

BuddyPress Links is a drop in link and rich media sharing component for BuddyPress 1.2.x

It supports complete integration with...

>Profiles, Directory, Activity Stream, Widgets, Notifications, Admin Bar, Admin Dashboard

Members can:

* Create and manage links from their profile
* Assign links to a category
* Control the visibility of their links (public, friends only, and hidden)
* Share other member's links on their profile
* Share any link with a group they are a member of
* Upload an image "avatar" to show with a link
* Auto embed rich media from URLs (YouTube, Flickr, and metacafe are supported)
* Automatic thumbnail picker available as of 0.2.1
* Embed a PicApp.com or Fotoglif.com image and use as the avatar
* Vote on other member's links
* Comment on other member's links
* @mentions support added in version 0.3

Administrators can:

* Manage all links (modify, delete)
* Manage link categories (create, modify, delete)
* Enable and customize widgets

Other features include:

* "Digg style" popularity algorithm
* Rich profile and directory sorting and filtering
* Most recent links news feed
* Hundreds of action and filter hooks
* Full i18n support (need translators!)

== Screenshots ==

1. This is the directory. Not seen on this image are the search, order by, and category drop down filters.
2. This is the how the links activity items appear on the main activity stream.
3. This is the create/admin form. You can see how web page thumbs were auto-detected.

== Installation ==

* BuddyPress Links 0.5.x requires WordPress 3.0 or higher with BuddyPress 1.2.6 or higher installed.
* BuddyPress Links 0.4.x requires WordPress 2.9.2 or higher with BuddyPress 1.2.x installed.
* BuddyPress Links 0.3.x requires WordPress 2.9.1 or higher with BuddyPress 1.2.x installed.
* BuddyPress Links 0.2.x requires WordPress 2.8.4 or higher with BuddyPress 1.1.x installed.

####Plugin:

1. Upload everything into the "/wp-content/plugins" directory of your installation.
1. Activate BuddyPress Links in the "Plugins" admin panel using the "Activate" link.
1. DO NOT COPY/MOVE THEME FILES TO YOUR CHILD THEME. This is no longer required as of 0.3

####Upgrading from an earlier version:

1. BACK UP ALL OF YOUR DATA.
1. The wire has been deprecated as of 0.3. ALL LINKS WIRE POSTS WILL BE LOST!
1. This version can use data created by previous versions, assuming you are porting your site to the new BP 1.2 default theme!

####Warning!

The 0.3.x and higher branches are not backwards compatible with the BuddyPress 1.1.x branch, or compatible with the 1.2.x classic theme.
The links data from the 0.2.x branch is compatible with 0.3.x and higher, except that all links wire posts will be lost.

== Upgrade Notice ==

= 0.5 =

No changes that affect data were made, however it is always a good idea to back up your data just in case!

= 0.4 =

BACK UP YOUR DATA! DO NOT attempt to install version 0.3 or higher on BP 1.1.X!  DO NOT try to use this plugin with the classic theme!

= 0.3 =

DO NOT attempt to install version 0.3 or higher on BP 1.1.X!  DO NOT try to use this plugin with the classic theme!

= 0.2 =

This version contains the first support for rich media embedding. *Please make sure that you update the "links" directory in your theme (see Installation).*

== Changelog ==

= 0.5 =

* Tested with WordPress 3.x and BuddyPress 1.2.6
* Improved compatibility when groups component is disabled
* Improved compatibility when activity component is disabled
* Added configuration constant for disabling groups integration
* Added configuration constant for using select box for categories on create form
* Added filter to bp_links_is_url_valid() to allow extended validation
* Fixed pubdate bug in feed generator
* Fixed linkmeta bug where empty values where being passed to array_map()
* Updated RU translation, props SlaFFik

= 0.4.1 =

* Fixed comment count bug
* Fixed nasty bug that caused filtering not to work for specific translations
* Fixed some translatable string issues, props SlaFFik
* Updated RU translation, props SlaFFik

= 0.4 =

* Initial group integration support added
* Added profile and group sharing features
* Create link directly from user profile and group pages
* Moved link list update/error messages to inside the current link's li block
* Added external link icon next to main link URL on the link list
* All link list targets and rels are no longer set by default and must be explicitly set with a filter
* All link list content is now separately filterable for finer control over URLs and content
* Load members profile links using plugins template instead of members home action
* Link description can be configured as optional with a constant
* Usability fixes to the link create/admin form (props Mike Pratt)
* Changing the component slug is now officially supported
* Heavy duty javascript refactoring

= 0.3.2 =

* Fixed broken paging
* Fixed bug with status check in some queries
* My Links now correctly only shows the displayed user's links
* My Links activity now correctly only shows the displayed user's links activity

= 0.3.1 =

* Fixed nasty SQL query bug, big props to windhamdavid
* Fixed broken category filtering that affected recently active links for single user
* Updated French translations, props Chouf1
* Added German translation, props Michael Berra
* Added Swedish translation, props Ezbizniz

= 0.3 =

* Baseline BuddyPress 1.2 support, REQUIRES BP 1.2 or higher
* Removed classic theme support (may re-support in the future if there is a huge demand)
* Wire support has been dropped and replaced with the activity stream
* Deep and seamless activity stream integration, complete with RSS feeds
* @mentions support, complete with e-mail notifications
* Lightbox for viewing photos and videos without leaving the site
* Moved template files to plugin dir to ease future upgrading
* Added support for template overriding from child theme
* Moved link loop item HTML from hard coded PHP to a template (links-loop-item.php)
* Added the much requested filters for link REL and TARGET
* Completely hooked into default theme AJAX (no duplicate functionality)
* Removed redundant "Home" link from link list
* Major overhaul of how we hook into the dashboard
* Replaced full blown widget with a basic widget based on groups
* Replaced custom elapsed time function with bp_core_time_since for continuity
* Added filters for changing navigation tab names.
* Fixed many old bugs

= 0.2.1 =

* Added support for auto embedding standard web pages
* Added automatic thumb picker for rich web pages
* Fixed layout bug that was affecting all webkit browsers
* Some other minor bug fixes

= 0.2 =

* Added support for auto-embedding of rich media (API documentation coming soon!)
* Reduced create/admin form to one page
* Wider selection of thumb sizes for the links widget
* Many CSS improvements and fixes
* Lots of general refactoring
* Some minor bug fixes

= 0.1 =

* First beta versions
* Many, many i18n fixes
* A few bug fixes

== Frequently Asked Questions ==

= What is the license? =

Released under the GNU GENERAL PUBLIC LICENSE 3.0 (http://www.gnu.org/licenses/gpl.txt)

All original code is Copyright (c) 2009 Marshall Sorenson. All rights reserved.

= How do I customize the default templates? =

To override only certain templates from the bp-links-default theme directory,
create a directory named "bp-links-default" in your child theme,
and replace the template using the EXACT same path AND filename.

To create a totally custom theme in order to completely bypass any core links
themes you will need to define a custom theme name.

For example, if your active WordPress theme is 'bluesky', and you wanted
to define your links theme as 'links-custom', you would put your files in:

/path/to/wp-content/themes/bluesky/links-custom

And in wp-config.php you would place this define statement:

define( 'BP_LINKS_CUSTOM_THEME', 'links-custom' )

To find out which template files are required to exist, do a recursive search for 'bp_links_load_template'

= Where can I get support? =

The support forum for the 0.4 branch can be found here: http://buddypress.org/forums/topic/buddypress-links-04x-releases-and-support

= Where can I find documentation? =

Coming soon

= Where can I report a bug? =

Look for MrMaz in #buddypress-dev

Or on buddypress.org http://buddypress.org/community/members/MrMaz/

Or on his website http://marshallsorenson.com/

Please search the forums first!!!
