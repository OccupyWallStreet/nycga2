=== Admin Post Navigation ===
Contributors: coffee2code
Donate link: http://coffee2code.com/donate
Tags: admin, navigation, post, next, previous, edit, post types, coffee2code
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 3.0
Tested up to: 3.5
Stable tag: 1.7.2
Version: 1.7.2

Adds links to navigate to the next and previous posts when editing a post in the WordPress admin.


== Description ==

Adds links to navigate to the next and previous posts when editing a post in the WordPress admin.

This plugin adds "&larr; Previous" and "Next &rarr;" links to the "Edit Post" admin page if a previous and next post are present, respectively.  The link titles (visible when hovering over the links) reveal the title of the previous/next post.  The links link to the "Edit Post" admin page for the previous/next posts so that you may edit them.

By default, a previous/next post is determined by the next lower/higher valid post based on relative sequential post ID and which the user can edit.  Other post criteria such as post type (draft, pending, etc), publish date, post author, category, etc, are not taken into consideration when determining the previous or next post. How posts are navigated, and post types and post statuses to restrict navigation can be customized via filters (see Filters section).

NOTE: Be sure to save the post currently being edited before navigating away to the previous/next post.

Links: [Plugin Homepage](http://coffee2code.com/wp-plugins/admin-post-navigation/) | [Plugin Directory Page](http://wordpress.org/extend/plugins/admin-post-navigation/) | [Author Homepage](http://coffee2code.com)


== Installation ==

1. Unzip `admin-post-navigation.zip` inside the `/wp-content/plugins/` directory for your site (or install via the built-in WordPress plugin installer)
1. Activate the plugin through the 'Plugins' admin menu in WordPress
1. See documentation for available customizations, if so inclined


== Screenshots ==

1. A screenshot of the previous/next links adjacent to the 'Edit Post' admin page header when Javascript is enabled.
2. A screenshot of the previous/next links in their own 'Edit Post' admin page sidebar panel when Javascript is disabled for the admin user.


== Frequently Asked Questions ==

= How do I change it so the previous/next links find the adjacent post according to post_date? =

See the Filters section for the `c2c_admin_post_navigation_orderby` filter, which has just such an example.


== Filters ==

The plugin is further customizable via four filters. Typically, these customizations would be put into your active theme's functions.php file, or used by another plugin.

= c2c_admin_post_navigation_orderby (filter) =

The 'c2c_admin_post_navigation_orderby' filter allows you to change the post field used in the ORDER BY clause for the SQL to find the previous/next post.  By default this is 'ID' for non-hierarchical post types (such as posts) and 'post_title' for hierarchical post types (such as pages).  If you wish to change this, hook this filter.  This is not typical usage for most users.

Arguments:

* $field (string) The current ORDER BY field

Example:

`add_filter( 'c2c_admin_post_navigation_orderby', 'order_apn_by_post_date' );
function order_apn_by_post_date( $field ) {
	return 'post_date';
}`

= c2c_admin_post_navigation_post_statuses (filter) =

The 'c2c_admin_post_navigation_post_statuses' filter allows you to modify the list of post_statuses used as part of the search for the prev/next post.  By default this array includes 'draft', 'future', 'pending', 'private', and 'publish'.  If you wish to change this, hook this filter.  This is not typical usage for most users.

Arguments:

* $post_statuses (array) The array of valid post_statuses

Example:

`
add_filter( 'c2c_admin_post_navigation_post_statuses', 'change_apn_post_status' );
function change_apn_post_status( $post_statuses ) {
	$post_statuses[] = 'trash'; // Adding a post status
	if ( isset( $post_statuses['future'] ) ) unset( $post_statuses['future'] ); // Removing a post status
	return $post_statuses;
}
`

= c2c_admin_post_navigation_post_types (filter) =

The 'c2c_admin_post_navigation_post_types' filter allows you to modify the list of post_types used as part of the search for the prev/next post.  By default this array includes all available post types.  If you wish to change this, hook this filter.

Arguments:

* $post_types (array) The array of valid post_types

Examples:

`
// Modify Admin Post Navigation to only allow navigating strictly for posts.
add_filter( 'c2c_admin_post_navigation_post_types', 'change_apn_post_types' );
function change_apn_post_types( $post_types ) {
	return array( 'post' );
}
`

`
// Modify Admin Post Navigation to disallow navigation for the 'recipe' post type
add_filter( 'c2c_admin_post_navigation_post_types', 'remove_recipe_apn_post_types' );
function remove_recipe_apn_post_types( $post_types ) {
	if ( isset( $post_types['recipe'] ) )
		unset( $post_types['recipe'] ); // Removing a post type
	return $post_types;
}
`

= c2c_admin_post_navigation_display (filter) =

The 'c2c_admin_post_navigation_display' filter allows you to customize the output links for the post navigation.

Arguments:

* $text (string) The current output for the prev/next navigation link

Example:

`
add_filter( 'c2c_admin_post_navigation_display', 'override_apn_display' );
function override_apn_display( $text ) {
	// Simplistic example. You could preferably make the text bold using CSS.
	return '<strong>' . $text . '</strong>';
}
`


== Changelog ==

= 1.7.2 =
* Add check to prevent execution of code if file is directly accessed
* Note compatibility through WP 3.5+
* Update copyright date (2013)
* Move screenshots into repo's assets directory

= 1.7.1 =
* Use string instead of variable to specify translation textdomain
* Re-license as GPLv2 or later (from X11)
* Add 'License' and 'License URI' header tags to readme.txt and plugin file
* Add banner image for plugin page
* Remove ending PHP close tag
* Minor documentation tweaks
* Note compatibility through WP 3.4+

= 1.7 =
* Add support for localization
* Use post type label instead of post type name, when possible, in link title attribute
* Use larr/rarr characters to denote direction of navigation instead of larquo/rarquo
* Enhanced styling of navigation links
* Hook 'admin_enqueue_scripts' action instead of 'admin_head' to output CSS
* Hook 'load-post.php' to add actions for the post.php page rather than using $pagenow
* Add version() to return plugin version
* Add register_post_page_hooks()
* Remove admin_init() and hook 'do_meta_boxes' in register_post_page_hooks() instead
* Update screenshots for WP 3.3
* Note compatibility through WP 3.3+
* Drop compatibility with versions of WP older than 3.0
* Update screenshots for WP 3.3
* Tweak plugin description
* Add link to plugin directory page to readme.txt
* Minor code reformatting
* Minor readme.txt reformatting
* Update copyright date (2012)

= 1.6.1 =
* Use ucfirst() instead of strtoupper() to capitalize post type name for metabox title
* Note compatibility through WP 3.2+
* Minor code formatting changes (spacing)
* Add FAQ section to readme.txt
* Fix plugin homepage and author links in description in readme.txt

= 1.6 =
* Add support for navigation in other post types
    * Add filter 'c2c_admin_post_navigation_post_types' for customizing valid post_types for search
    * Enable navigation for all post types by default
    * Allow per-post_type sort order for navigation by adding $post_type argument when applying filters for 'c2c_admin_post_navigation_orderby'
    * Pass additional arguments ($post_type and $post) to functions hooking 'c2c_admin_post_navigation_post_statuses'
* Ensure post navigation only appears on posts of the appropriate post_status
* For hierarchical post types, order by 'post_title', otherwise order by 'ID' (filterable)
* Move application of filters from admin_init() into new do_meta_box(), which is hooking 'do_meta_box' action, so they only fire when actually being used
* Output JavaScript via 'admin_print_footer_scripts' action rather than 'admin_footer'
* Rename class from 'AdminPostNavigation' to 'c2c_AdminPostNavigation'
* Switch from object instantiation to direct class invocation
* Explicitly declare all functions public static and class variables private static
* Documentation tweaks
* Note compatibility through WP 3.1+
* Update copyright date (2011)

= 1.5 =
* Change post search ORDER BY from 'post_date' to 'ID'
* Add filter 'c2c_admin_post_navigation_orderby' for customizing search ORDER BY field
* Add filter 'c2c_admin_post_navigation_post_statuses' for customizing valid post_statuses for search
* Deprecate (but still support) 'admin_post_nav' filter
* Add filter 'c2c_admin_post_navigation_display' filter as replacement to 'admin_post_nav' filter to allow modifying output
* Retrieve post title via get_the_title() rather than directly from object
* Also strip tags from the title prior to use in tag attribute
* Don't navigate to auto-saves
* Check for is_admin() before defining class rather than during constructor
* esc_sql() on SQL strings that have potentially been filtered
* Use esc_attr() instead of attribute_escape()
* Store plugin instance in global variable, $c2c_admin_post_navigation, to allow for external manipulation
* Fix localization of the two strings
* Instantiate object within primary class_exists() check
* Note compatibility with WP 3.0+
* Drop compatibility with version of WP older than 2.8
* Minor code reformatting (spacing)
* Remove docs from top of plugin file (all that and more are in readme.txt)
* Remove trailing whitespace in header docs
* Add Upgrade Notice and Filters sections to readme.txt
* Add package info to top of plugin file

= 1.1.1 =
* Add PHPDoc documentation
* Note compatibility with WP 2.9+
* Update copyright date
* Update readme.txt (including adding Changelog)

= 1.1 =
* Add offset and limit arguments to query()
* Only get ID and post_title fields in query, not *
* Change the previous/next post query to ensure it only gets posts the user can edit
* Note compatibility with WP 2.8+

= 1.0 =
* Initial release


== Upgrade Notice ==

= 1.7.2 =
Trivial update: noted compatibility through WP 3.5+

= 1.7.1 =
Trivial update: noted compatibility through WP 3.4+; explicitly stated license

= 1.7 =
Recommended update: enhanced styling of navigation links; added support for localization; noted compatibility through WP 3.3+; and more

= 1.6.1 =
Trivial update: noted compatibility through WP 3.2+

= 1.6 =
Feature update: added support for non-'post' post types; fixed so navigation only appears for posts of appropriate post status; implementation changes; renamed class; updated copyright date; other minor code changes.

= 1.5 =
Recommended update. Highlights: find prev/next post by ID rather than post_date, fix navigation logic, added numerous filters to allow for customizations, miscellaneous improvements, dropped pre-WP 2.8 compatibility, added verified WP 3.0 compatibility.
