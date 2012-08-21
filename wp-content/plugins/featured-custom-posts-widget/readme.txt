=== Plugin Name ===
Contributors: linkhousemedia
Donate Link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=Q35ZW6YDWXSMQ
Tags: custom post type, featured products, featured posts, post type widget, custom taxonomy widget, widget
Requires at least: 3.0
Tested up to: 3.2
Stable tag: 1.1.0

Widget that allows custom post types and taxonomies to be displayed.  Works well with Custom Post Type UI and Taxonomy Images plugins.

== Description ==

This plugin was initially designed as a theme function but we thought it might come in handy for someone else.  It is designed to use the following plugins:

*	<a href="http://wordpress.org/extend/plugins/custom-post-type-ui/">Custom Post Type UI</a>
*	<a href="http://wordpress.org/extend/plugins/taxonomy-images/">Taxonomy Images</a>

Of course you can create your own custom post types and taxonomies but the Custom Post Type UI plugin makes very light work of it. 

The plugin creates a widget for adding featured posts. There are other plugins that do this but Featured Custom Posts allows for custom post types and taxonomies. It was created to show featured products that were created as custom post types.

*Features*

*	Select number of posts to display (default: 10)
*	Post Type
*	Custom Taxonomy
*	Custom Taxonomy Term

*New In Version 1.1*

*	Added Permalink Base for post URL (Defaults to post's GUID)
*	Added Post Title as the link text if Taxonomy Images plugin isn't present

*Example Usage*

Custom Post Type: "products"
Taxonomy: "product-tags"
Taxonomy Term: "featured"

In this example our custom post type of "products" allows us to add products in a similar way to posts.  Now with our custom taxonomy of "product-tags" we can tag our products with the tag "featured". Plug this information into the widget via the wp-admin area and you're all set!

*Future Updates*

*	Allow for multiple taxonomy terms
*	Add thumbnail size support

== Frequently Asked Questions ==

= How do I use the Permalink Base feature? =

This gives you a way to add a custom slug in front of your post_name in the URL if you're using permalinks.  If your permalink structure looks like `/%category%/%postname%/` then your permalink base might look like `/products`, for example. You can also use a single forward slash to produce something like `example.com/post-name`

== Installation ==

To install this plugin please use the "install" feature from the WordPress site.

== Changelog ==

= 1.1.0 =
* Added: Permalink Base for post URL (Defaults to post's GUID)
* Added: Post Title as the link text if Taxonomy Images plugin isn't present

= 1.0.1 =
* Added: Functionality that checks if "Taxonomy Images" plugin is active and in use
* Added: Class "featured-custom-posts" to list of custom posts

= 1.0.0 =
* Initial setup

== Screenshots ==

1. View of widget in wp-admin
