=== Template Tag Shortcodes ===
Contributors: greenshady
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=3687060
Tags: post, posts, page, shortcode
Requires at least: 2.5
Tested up to: 2.8
Stable tag: 0.1.1

A plugin that turns many of the WP template tags into shortcodes (40+ shortcodes).

== Description ==

*Template Tag Shortcodes* is a plugin that turns many of the WordPress <a href="http://codex.wordpress.org/Template_Tags" title="WordPress template tags">template tags</a> into easy-to-use shortcodes.

This plugin was created so end users could make use of the template tags within their posts and pages.  Currently, this plugin creates 40+ shortcodes for use.  Not all template tags are available as shortcodes, but you are more than welcome to request others be added.

See the FAQ or the included `readme.html` for a list of all available shortcodes.

== Installation ==

1. Unzip the `template-tag-shortcodes.zip` folder.
1. Upload the `template-tag-shortcodes` folder to your `/wp-content/plugins directory`.
1. In your WordPress dashboard, head over to the *Plugins* section.
1. Activate *Template Tag Shortcodes*.

More detailed instructions are included in the plugin's `readme.html` file.

== Frequently Asked Questions ==

= Why was this plugin created? =

To allow users the ability to easily use WordPress template tags from within the *Write Post/Page* screen.

= What shortcodes are available? =

* `[wp_list_authors]`
* `[the_author]`
* `[the_author_description]`
* `[the_author_login]`
* `[the_author_firstname]`
* `[the_author_lastname]`
* `[the_author_nickname]`
* `[the_author_ID]`
* `[the_author_url]`
* `[the_author_email]`
* `[the_author_link]`
* `[the_author_aim]`
* `[the_author_yim]`
* `[the_author_posts]`
* `[the_author_posts_link]`
* `[the_modified_author]`
* `[wp_list_categories]`
* `[wp_dropdown_categories]`
* `[the_category]`
* `[get_category_link]`
* `[the_date]`
* `[the_time]`
* `[the_modified_date]`
* `[the_modified_time]`
* `[wp_tag_cloud]`
* `[the_tags]`
* `[get_tag_link]`
* `[wp_list_bookmarks]`
* `[the_title]`
* `[the_title_attribute]`
* `[the_ID]`
* `[the_permalink]`
* `[get_permalink]`
* `[wp_list_pages]`
* `[wp_dropdown_pages]`
* `[wp_get_archives]`
* `[bloginfo]`
* `[allowed_tags]`
* `[wp_logout_url]`
* `[wp_login_url]`
* `[comments_link]`
* `[category_description]`
* `[tag_description]`
* `[term_description]`
* `[the_terms]`
* `[the_author_meta]`

== What are the parameters for each shortcode? ==

There are several parameters for many shortcodes and none for others.  Each shortcode represents its equivalent template tag.  A list of the available parameters for each shortcode is included in the plugin's `readme.html` file.

== Screenshots ==

There are no screenshots at this time.

== Changelog ==

**Version 0.1.1**

* `[the_author_email]` uses `antispambot()` to protect emails addresses.
* Added the `[comments_link]` shortcode.
* Added the `[category_description]` shortcode.
* Added the `[tag_description]` shortcode.
* Added the `[term_description]` shortcode.
* Added the `[the_terms]` shortcode.
* Added the `[the_author_meta]` shortcode.

**Version 0.1**

* Plugin launch.