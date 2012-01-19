=== More Fields ===
Contributors: henrikmelin, kalstrom
Donate link: http://henrikmelin.se/plugins
Tags: custom fields, admin, metadata, cms, custom fields, extra content, more plugins
Requires at least: 3.2.1
Tested up to: 3.2.1
Stable tag: 2.1

Adds any number of extra fields in any number of additional boxes on the Write/Edit page in the Admin.

== Description ==

More Fields is a WordPress plugin that adds boxes to the Write/Edit page. These boxes contains input fields, so that additional (more) fields can be added to a post. For example, if you write about books, you can add a box where you can enter title and author, etc. The boxes can be placed either to the right or to the left on the Write/Edit page.

**Upgrading from More Fields 1.x?** Please follow the upgrade instructions (installation tab). Also note that the functionality to create Post Types has been moved to a stand alone plugin: [More Types](http://wordpress.org/extend/plugins/more-types/).

With More Fields you can:

* Add any number of boxes with any number of fields to the Write/Edit page.
* Add text, Web Forms 2.0/HTML5 fields, text area, wysiwyg, check boxes, radio buttons and select lists as your input fields.
* Add More Fields boxes to any defined post type (works perfectly with [More Types](http://wordpress.org/extend/plugins/more-types/))
* Create archives/feeds based on Custom field values with custom slugs
* Create boxes programmatically (from `functions.php`, from exported files or from within other plugins)

More Fields is part of a suite of plugins created to enhance the functionality of a vanilla WordPress installation. With [More Types](http://wordpress.org/extend/plugins/more-types/) you can create additional post types besides Pages and Posts, with [More Taxonomies](http://wordpress.org/extend/plugins/more-taxonomies/) you can create additional taxonomies besides Categories and Tags. More Fields works without the other plugins but interacts with them nicely.

This plugins was born out of the development work done for [Dagensskiva](http://dagensskiva.com/) and [Dagensbok](http://dagensbok.com/).

== Installation ==

To install More Fields:

1. Upload the 'more-fields' folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Administer in Settings -> More Fields

= Upgrading from More Fields 1.x? =

If you're upgrading from More Fields 1.x you need to make a couple of steps to continue working with your already defined Post Types and Fields.

1. Upgrade to More Fields 1.5.3 (this prepares the More Fields fields and Post types for WP 3.0)
2. Update to WordPress 3.0 still using More Fields 1.5.3
3. Upgrade to More Fields 2.0
4. Install More Types 1.0

More details: [Upgrading More Fields](http://labs.dagensskiva.com/2010/07/22/upgrading-more-fields/)

== Changelog ==

= 2.1 =
* Compatibility with WP 3.2
* Sputnik v8 - WP_DEBUG fixes
* New field type: Media library select with preview

= 2.0.5 = 
* Resolved issues with quotes in field and box names
* Sputnik v6 - brought back json implementation, instead of serialize() when storing arrays in forms.

= 2.0.4 =
* GUI fix for checkboxes that slipped out of the admin box
* Fix for radio buttons.
* Fixed error with unescaped single quote marks.
* Checkbox implementation changed. Checked checkbox has value 1 (true), unchecked checkbox has no value.
* Fix for file list labels
* Sputnik v5

= 2.0.3 =
* Regained file list functionality with proper admin-ajax
* Bug fix for template functions
* Problem with listings based on Custom Fields fixed
* Fixed More Fields breaking some plugins

= 2.0.2 =
* Checking that wp_query has get method in rewrite

= 2.0.1 =
* Problem with WYSIWIG fields and content not showing up
* implode error due to son implementation change, now using serialise instead
* WYSIWIG overflow when window is small.

= 2.0 =
* Bug fixes
* HTML/CSS for fields on the Write/Edit-page
* Instructions for HTML5 fields, for browsers that don't support the input types
* Fields now checks for filters (for creation of more advanced custom fields)
* Capability to override WordPress defaults
* Sputnik v3

= 2.0rc2 =
* Bug fixes

= 2.0rc1 =
* Boxes can now be attributed to a specific post type in More Fields fore better interoperability with More Types.
* Separation of different versions of fields (More Fields created with functions.php/other plugins, exported More Fields fields and More Fields created in More Fields.
* HTML5 input types (color, range, time, number, week, month, date)
* Bug fixes

= 2.0b1 =
* Compatibility with WordPress 3.0
* Post type functionality moved to the More Types plugin
* New template function, more_fields: `more_fields(field,before,after,content filter)`
* Box export functionality
* Compatibility with More Taxonomies, More Types, More Roles
* Total code revamp
* Enhanced rights management for boxes

= 1.5 =
* All boxes, fields and post types are converted to be compatible with More Fields 2.0, More Types 1.0 and WordPress 3.0

= 1.4 =
* Compatibility with WordPress 2.9
* Javascript fixes for the WordPress admin

= 1.3 =
* Compatibility with WordPress 2.8

= 1.2 =
* Compatibility with WordPress 2.7

= 1.0 =
* Post types

= 0.5 =
* First release

