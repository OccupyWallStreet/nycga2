=== bbPress ===
Contributors: matt, johnjamesjacoby
Tags: forums, discussion, support, theme, akismet, multisite
Requires at least: 3.4
Tested up to: 3.5
Stable tag: 2.2.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

bbPress is forum software, made the WordPress way

== Description ==

Have you ever been frustrated with forum or bulletin board software that was slow, bloated and always got your server hacked? bbPress is focused on ease of integration, ease of use, web standards, and speed.

We're keeping things as small and light as possible while still allowing for great add-on features through WordPress's extensive plugin system. What does all that mean? bbPress is lean, mean, and ready to take on any job you throw at it.

== Installation ==

1. Place the 'bbpress' folder in your '/wp-content/plugins/' directory.
2. Activate bbPress.
3. Visit 'Settings > Forums' and adjust your configuration.
4. Create some forums.
5. View your site.
6. Adjust the CSS of your theme as needed, to make everything pretty.

== Changelog ==

= 2.2.4 =
* Prepare converter queries
* Improve validation and sanitization of form values

= 2.2.3 =
* Improve compatibility with some themes
* Fix integration with BuddyPress Group Forums
* Fix BuddyPress Activity Stream integration

= 2.2.2 =
* RTL and i18n fixes
* Improved user profile theme compatibility
* Fixed incorrect link in credits page
* Fixed admin area JS issues related to topic suggest
* Fixed template part reference in extras user edit template

= 2.2.1 =
* Fix role mapping for non-WordPress roles
* Fix issue with private forums being blocked
* Allow moderators to see hidden forums

= 2.2 =
* Improved user roles and capabilities
* Improved theme compatibility
* Improved BuddyPress Group Forums integration
* Improved forums convertion tool
* Improved forums tools and settings
* Improved multisite support
* Added What's New and Credits pages
* WordPress 3.5 and BuddyPress 1.7 ready

= 2.1.2 =
* Fixed admin-side help verbiage
* Fixed reply height CSS
* Fixed password converter
* Fixed child post trash and delete functions

= 2.1.1 =
* Fixed Invision, phpBB, and vBulletin importers
* Fixed private/hidden forum bugs
* Fixed topic split meta values
* Fixed theme compatibility logic error
* Fixed role mask issues for shared user installs
* Fixed missing function cruft
* Fixed missing filter on displayed user fields

= 2.1 =
* WordPress 3.4 compatibility
* Deprecate $bbp global, use bbpress() singleton
* Private forums now visible to registered users
* Updated forum converter
* Topic and reply edits now ran through Akismet
* Fixed Akismet edit bug
* Fixed Widgets nooping globals
* Fixed translation load order
* Fixed user-edit bugs
* Fixed settings screen regressions
* Improved post cache invalidation
* Improved admin-side nonce checks
* Improved admin settings API
* Improved bbPress 1.1 converter
* Improved BuddyPress integration
* Improved Theme-Compatibility
* Improved template coverage
* Improved query performance
* Improved breadcrumb behavior
* Improved multisite integration
* Improved code clarity
* Improved RTL styling
* Added 2x menu icons for HiDPI displays
* Added fancy editor support
* Added fallback theme picker
* Added tools for importing, resetting, and removing

= 2.0 =
* Released on September 21, 2011

= 2.0-rc-5 =
* Fixed Genesis incompatibilities
* Fixed BuddyPress activity stream issues
* Fixed Subscription email sending issues
* Fixed Theme Compat display issues for some themes
* Improved Theme Compat class
* More future proofing internal API's

= 2.0-rc-4 =
* BuddyPress @mention integration
* Improved Akismet user agent handling
* Added blacklist_keys support
* Fixed spam/deleted user handling
* Updated green admin color scheme for WordPress 3.2
* Added actions to topic/reply forms
* Improved support for future ajaxification

= 2.0-rc-3 =
* Fixed activation/deactivation
* Added Forum Participant role for multisite use

= 2.0-rc-2 =
* BuddyPress activity action integration
* Multisite integration
* Fixed a bushel of bugs
* Fixed tag pagination again
* Fixed ajax priority loading

= 2.0-rc-1 =
* Fixed tag pagination
* Broke tag pagination
* Squashed a bunch of bugs

= 2.0-beta-3b =
* Fix regression in forum index theme compatibility template
* Audit usage of get strings for moderator level and above users

= 2.0-beta-3 =
* Akismet integration
* Fixes replies within wp-admin
* Fixes reply notification links
* Fixes inconsistent breadcrumb behavior
* Fixes theme compatibility issues
* Fixes archive and page conflicts
* Improvements to unpretty permalink support
* Improvements to importer
* Improvements to multisite support
* Normalize theme, shortcodes, and template parts
* Add humans.txt
* Add empty index.php files to prevent snooping
* Add max length to topic titles (default 80 chars)

= 2.0-beta-2 =
* GlotPress integration
* Fixes Forum archive bug
* Fixes and improvements to importer
* Adds home link support to breadcrumb
* Improvements to Theme Compatibility
* Numerous template and CSS improvements
* RTL support
* Improved multisite support
* Add filters for future anti-spam support
* Add missing breadcrumbs to various template files
* Topic/reply trash fixes

= 2.0-beta-1 =
* In development
