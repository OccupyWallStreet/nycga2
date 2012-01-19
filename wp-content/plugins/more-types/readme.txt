=== More Types ===
Contributors: henrikmelin, kalstrom
Donate link: http://labs.dagensskiva.com/plugins/
Tags: post type, custom post types, admin, cms, extra content
Requires at least: 3.1
Tested up to: 3.1
Stable tag: 1.1

Adds any number of extra Post types, besides Post and Page, for the WordPess Admin. Also allows for special editing rights for specific User roles for new and created post types.

== Description ==

More Types is a WordPress plugin that adds new post types to the WordPress admin. For instance, if you run a music site you could create a review post type (based on the post). If you run a food blog you could create a post type for recipes.

If you use More Fields in addition to More Types you could for instance add an input field where you put the ingredients and another where you input cooking time.

With More Types you can:

* Create additional post types
* Allow different WordPress User roles to have different rights to review, save, and publish a specific post type (even built in post types)
* List posts in specific menus in the WordPress admin
* Set a range of editing capabilities of the post type based on user level



More Types is part of a suite of plugins created to enhance the functionality of a vanilla WordPress installation. With More Fields you can create additional input fields for easier management of Custom fields, with More Taxonomies you can create additional taxonomies besides Categories and Tags. More Types works without the other plugins but interacts with them nicely.

This plugins was born out of the development work done for [Dagensskiva](http://dagensskiva.com/), [Dagensbok](http://dagensbok.com/) and user requests.

== Installation ==

To install More Types:

1. Upload the 'more-types' folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Administer in Settings -> More Types

=== Upgrading from More Fields 1.x? ===

If you're upgrading from More Fields 1.x you need to take a couple of steps to continue working with your already defined Post Types.

1. Upgrade to More Fields 1.5.1 (this prepares the More Fields fields and Post types for WP 3.0)
2. Update to WordPress 3.0 still using More Fields 1.5.1
3. Upgrade to More Fields 2.0
4. Install More Types 1.0

== Changelog ==

= 1.2 =
* Compatibility with WordPress 3.2
* Sputnik v8 - WP_DEBUG compatability

= 1.1 =
* WordPress 3.1 compatability
* Setting for inclusion of the post type in the admin menu
* Setting for enabling archives
* More Plugins Object Sputnik #7

= 1.0fc1 =
* Separation of different types of post types (default Post types, Post types created with functions.php/other plugins, exported More Types Post types and Post Types created in More Types.

= 1.0&beta;2 =
* Improved functionality for exporting and importing post types

= 1.0&beta;1 =
* Creation and editing of moved from post types to More Types
* Restructuring of data model (for WordPress 3.0)