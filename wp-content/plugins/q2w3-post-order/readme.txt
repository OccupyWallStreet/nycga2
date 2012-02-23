=== Q2W3 Post Order ===
Contributors: Max Bond, AndreSC 
Tags: q2w3, astickypostorderer, post order, order, posts, category, tag, custom taxonomy, custom post type archive, english 
Requires at least: 3.1
Tested up to: 3.3.1
Stable tag: 1.2.2

Lets you manipulate the order in which posts are displayed. Requires WP 3.1 or higher!

== Description ==

This plugin is a descendant of a well known [AStickyPostOrderER](http://wordpress.org/extend/plugins/astickypostorderer/). 
Because it was not updated for a long time I decided to make an upgrade.

Note! Original AStickyPostOrderER must be deactivated before Q2W3 Post Order installation!

The main changes are:

* Since version 1.1.0 added ability to `stylize ordered posts` (see FAQ for details)
* Plugin was completely rewritten	
* Now you can change order of posts for `custom taxonomy` and `custom post type archive` pages
* Removed Meta-Stickiness options - the plugin became lighter, faster and easier to use
* Added support for internationalization
* Advanced uninstall
* Plugin settings page was moved from Tools to Settings section

Supported languages: 

* English

== Installation ==

Deactivate AStickyPostOrderER plugin if you have it installed.

Then follow standard WordPress plugin installation procedure.

== Screenshots ==

1. List of taxonomy terms
2. List of posts
3. Screen Options panel

== Frequently Asked Questions ==

= How to enable custom taxonomies and custom post types? =

Open plugin setting page. Look in upper right corner of the screen, there is a Screen Options dropdown panel. 
There you can enable/disable custom taxonomies and post types. 

= How to stylize ordered posts? =

For each ordered post two css classes are set: `q2w3-post-order` and `q2w3-post-order-{n}`, where {n} is post position number. 
Use `q2w3-post-order` css class to set general style for ordered posts. 
Use `q2w3-post-order-{n}` to set unique style for specific post position. 
Note! You have to use `<?php post_class(); ?>` template tag in your theme. 

= How to disable plugin for feeds, pages and custom queries? =

You can add a parameter `q2w3-post-order=disable` to the url. 
For example `example.com/feed/?q2w3-post-order=disable` - your main feed post order will not be modified.

If you use custom queries: `query_posts('cat=13&showposts=10&q2w3-post-order=disable');`.
 
Array style: `query_posts(array('cat'=>13,'showposts'=> 10,'q2w3-post-order'=>'disable'));`.

== Other Notes ==


Q2W3 Plugins:

* [Code Insert Manager](http://wordpress.org/extend/plugins/q2w3-inc-manager/)
* [Q2W3 Thickbox](http://wordpress.org/extend/plugins/q2w3-thickbox/)
* [Q2W3 Yandex Speller](http://wordpress.org/extend/plugins/q2w3-yandex-speller/)
* [Q2W3 Screen Options Hack Demo](http://wordpress.org/extend/plugins/q2w3-screen-options-hack-demo/)

== Changelog ==

= 1.2.2 =
* Fixed php warnings nad notices
* Post order number now can contain up to 6 digits

= 1.2.1 =
* Fixed bug with 404 page on sites with non standard db_prefix
* Checked compatibility with WordPress 3.3 RC3

= 1.2.0 =
* Added debug mode.
* Added option which allows Editors to access plugin settings page.

= 1.1.0 =
* Added ability to stylize ordered posts.
* Fixed small bug with post ordering in hierarhical taxonomies.

= 1.0.0 =
* First public release.