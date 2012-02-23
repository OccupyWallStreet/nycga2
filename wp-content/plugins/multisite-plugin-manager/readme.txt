=== Multisite Plugin Manager ===
Contributors: uglyrobot
Tags: multisite, wpmu, plugins
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=W66QWST9B9KRN
Requires at least: 3.1
Tested up to: 3.3.1
Stable tag: trunk

The essential plugin for every multisite install! Manage plugin access permissions across your entire multisite network.

== Description ==
Plugin management for Wordpress Multisite that supports the native plugins page and the WPMU DEV Pro Sites plugin! Used on thousands of multisite installs across the web.
Previously known as **WPMU Plugin Manager**, it uses a backend options page to adjust plugin permissions for all the sites in your network.

* Select what plugins sites have access to
* Choose plugins to Auto-Activate for all new blogs
* Mass activate/deactivate a plugin on all sites in your network (Very Handy!)
* Assign special plugin access permissions for specific sites in your network
* And as Super Admin, you can override all these to activate specific plugins on the sites you choose!
* Removes the plugin meta row links (Version, Author, Plugin) and any update messages for blog admins

Also, if you use the excellent <a href="http://premium.wpmudev.org/project/pro-sites">Pro Sites plugin from WPMU DEV</a> you will be able to charge for access to certain plugins!

A free plugin by Aaron Edwards of <a href="http://uglyrobot.com/">UglyRobot Web Development</a>.

== Installation ==
= To Install: =

1.  Download the plugin file
1.  Unzip the file into a folder on your hard drive
1.  Upload the `/plugin-manager/` folder to the `/wp-content/plugins/` folder on your site
1.  Visit *Network Admin -> Plugins* and *Network Activate* it there.

= To Configure Network Wide Options =
1. Visit *Network Admin -> Plugins -> Plugin Management*
1. Select what kind of access each plugin should have. You can choose:
	* No access (default)
	* All Users
	* All Users (Auto-Activate) - activates the plugin for all new blogs
1. You may also mass activate/deactivate a plugin on all sites in your network (Very Handy!)

= To Override Plugin Access Per Site =
1. Visit the *Network Admin -> Sites* list
1. Click the "*Edit*" link for the site you wish to modify
1. Look at the bottom of the "*Settings*" tab screen for the per blog options

== Frequently Asked Questions ==

= Can I use this plugin for non-multisite WP installs? =
No, this plugin is only compatible (and useful) with Multisite installs.

= Do I need the Pro Sites plugin installed? =
Not at all, but if you install the <a href="http://premium.wpmudev.org/project/pro-sites">Pro Sites plugin from WPMU DEV</a> the options to charge for access to certain plugins will appear in the dropdowns.

== Screenshots ==

1. The plugin management admin page
2. Overriding allowed plugins per site

== Changelog ==

= 3.1.1 =
* Readme updates
* Pro Sites support

= 3.1 =
* Fix auto-activate for new blogs

= 3.0 =
* Complete rewrite for WP 3.1