=== Convert Post Types ===
Contributors: sillybean
Tags: post types, conversion
Donate Link: http://sillybean.net/code/wordpress/convert-post-types/
Requires at least: 3.0
Tested up to: 3.1
Stable tag: 1.1

A bulk conversion utility for post types.

== Description ==

This is a utility for converting lots of posts or pages to a custom post type (or vice versa). You can limit the conversion to posts in a single category or children of specific page. You can also assign new taxonomy terms, which will be added to the posts' existing terms.

This plugin is useful for converting many posts at once. If you'd rather do one at a time, use <a href="http://wordpress.org/extend/plugins/post-type-switcher/">Post Type Switcher</a> instead.

== Installation ==

1. Install a database backup plugin, like <a href="http://www.ilfilosofo.com/blog/wp-db-backup">WP DB Backup</a>. Make a backup of your database, and make sure you know how to restore your site using the backup. I'm not kidding. Do this first!
1. Upload this plugin directory to `/wp-content/plugins/` 
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to Tools &rarr; Convert Post Types to convert your posts.

== Screenshots ==

1. The options screen

== Changelog ==

= 1.1 =
* Removed private post types (like nav menu items) from the dropdown menus to prevent accidents. Only public post types are available for switching.
= 1.0 =
* First release (June 30, 2010)