=== New Blog Defaults ===
Contributors: DeannaS, kgraeme, MadtownLems
Tags: WPMU, Wordpress Mu, Wordpress Multiuser, Blog Defaults, Set Defaults 
Requires at least: 3.0
Tested up to: 3.4.2
Stable tag: trunk



Allows site administrator to set the defaults for all new blogs created on server.

== Description ==
Included files:

* cets\_blog\_defaults.php

This plugin does the following:

1. In 3.0, adds a new submenu to the site admin screen called "New Blog Defaults."
2. In 3.1, adds a new submenu  called "New Blog Defaults" to the Settings section on the Network Admin page.
1. Allows the site/network administrator to visual set defaults for each of the major blog sections:

	1. General Settings
	1. Writing Settings
	1. Reading Settings
	1. Discussion Settings
	1. Privacty Settings
	1. Permalinks
	1. Miscellaneous Settings
	1. Theme
	1. Bonus Settings 


== Installation ==

1. Place the cets\_blog\_defaults.php file in the wp-content/mu-plugins folder.
1. Go to the appropriate menu and configure new blog defaults. Blogs created after settings are saved will use new blog defaults.



== Frequently Asked Questions ==
1. Can I use this to update current blog settings?

No, it's not designed to affect current blogs. Settings will only affect new blogs.

2. Will this work on version prior to 3.0? 

You will have to pull a tagged version from the repository. Version 2.1 of this plugin works on older versions of WordPress.

== Changelog ==

2.2.2 - Minor cleanup and removed some notices

2.2 - Moved admin page to Network->Settings for 3.1.  Increased required WP Version to 3.0. (stopped support for lower version)

2.1 - Fixed deprecated parameter on add_sub_menu page

2.0 - Updates for WP3.0
		-Fixed default link deletion

1.5.2 - fixed bugs with default theme and permalinks. Set defaults on some options to match wpmu defaults.

1.5.1 - fixes bug with default categories

1.5 - Added support for 2.9.1.1, including the auto-embed URLs and embed sizes and added the xmlrpc options. Fixed a bug (hopefully) with timezones.

1.4 - Added Ability to add users to list of blogs with selected role. Added ability to close comments on the about page and hello world post.

1.3 - Upgrades for 2.8 - removed legacy code for < 2.7, added initial links and categories, added toggle for over-riding privacy settings

1.2.4 - bug fix for users signup page not correctly returning the name/link of the new blog and defaulting to the main blog.

1.2.3 - bug fix for tag and category base

1.2.2 - bug fix for permalinks on sub-domain installs

1.2.1 - updated for 2.7 release with additional features

1.0.2 - fixed bug with theme selector - now shows title of theme instead of name of template, and includes all themes available

1.0.1 - fixed bug with permalink structures 


