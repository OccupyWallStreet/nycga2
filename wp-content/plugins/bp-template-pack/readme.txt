=== Plugin Name ===
Contributors: apeatling, boonebgorges, r-a-y
Tags: buddypress, themes, compatibility, convert, integration
Requires at least: WordPress 3.0 / BuddyPress 1.2
Tested up to: WordPress 3.3.1 / BuddyPress 1.5.4
Stable tag: 1.2.1

== Description ==

Add support for BuddyPress to your existing WordPress theme. This plugin will guide you through the process
step by step. Once you are finished, your existing WordPress theme will be able to manage and display all BuddyPress
pages and content. The process is completely reversible and does not modify any of your existing template files.

== Installation ==

Download and install the plugin using the built in WordPress plugin installer.
If you download the plugin manually, upload the plugin to "/wp-content/plugins/bp-template-pack/".

Activate the plugin in the "Plugins" admin panel using the "Activate" link.

Head to the "Appearance > BP Compatibility" menu and follow the step-by-step instructions.

== Changelog ==

= 1.2.1 =
* Improves performance with parent/child themes
* Improves performance with WP 3.3+

= 1.2 =
* Adds BP 1.5 compatibility
* Restructures how scripts and CSS are enqueued
* Removed bundled template files

= 1.1.4 =
* Updates to BP 1.2.9 template files

= 1.1.3 =
* Fixes add_theme_page() param that might cause permissions errors on some setups

= 1.1.2 =
* Adds hooks for BP action buttons

= 1.1.1 =
* Replaces deprecated is_site_admin() with is_super_admin()

= 1.1 =
* May 27 2011
* Updates templates to latest 1.2.8
* Ensures that JavaScript strings are defined
* First attempts to pull templates from BuddyPress itself
* Fixes float bug on item-lists
* Uses bp_include loader file to load main function
* Removes calls to deprecated bp_core_is_multisite() template
* Adds admin nag when the setup process has not been completed

= 1.0.2 =
* Feb 22 2010
* fixed shorthand php tag.

= 1.0.1 =
* Feb 20 2010 
* fixed possible issue with group home template.

= 1.0 =
* Feb 18 2010
* initial release.
