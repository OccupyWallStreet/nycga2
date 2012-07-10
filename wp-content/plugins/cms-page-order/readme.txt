=== CMS Page Order ===
Contributors: billerickson
Tags: page, pages, posts, order, cms, drag-and-drop, rearrange, reorder, management, manage, admin
Requires at least: 3.1
Tested up to: 3.4.1
Stable tag: 0.3.2

Change the page order with quick and easy drag and drop.

== Description ==

* Quick and easy drag and drop for rearranging of pages
* Actions: View, edit, trash and publish (drafts and pending pages)
* Set the maximum number of nesting levels
* Set the applicable post types
* Native looking
* [WPML](http://wordpress.org/extend/plugins/sitepress-multilingual-cms/) support

#### Translations

* English
* Swedish
* Italian
* French (thanks to Stéphane Le Roy)

[Documentation](https://github.com/billerickson/cms-page-order/wiki) | [Support Forum](https://github.com/billerickson/cms-page-order/issues)


== Installation ==

1. Upload the folder `cms-page-order` to your plugin directory (usually `/wp-content/plugins/`)
2. Activate the plugin through the 'Plugins' menu in WordPress

== Screenshots ==

1. Pages displayed as a tree.
2. Filter hook to set the maximum number of nesting levels.
3. Post status labels with support for custom statuses.

== Changelog ==

= 0.3.2 = 
* Added italian translation
* Added support for WP Multisite, thanks benhuson

= 0.3.1 =
* Added 'cmspo_page_label' filter for controlling the page label.
* Updated readme with more information on available filters

= 0.3 =
* Rebuilt the contextual help to work with WordPress 3.3
* Added user capability check, so code only loads if user can edit pages.

= 0.2 = 
* Added support for custom post types. Use 'cmspo_post_types' filter. See: https://gist.github.com/1380344

= 0.1.4 =
* Added French translation by Stéphane Le Roy
* Calls the_title filter for compatibility with qTranslate (patch by Stéphane Le Roy)
* Style corrections for Wordpress 3.2

= 0.1.3 =
* Updated nestedSortable from 1.3.3 to 1.3.4: Fixes a problem with elements sometimes getting kicked out of the ol element.

= 0.1.2 =
* Fixes a problem with scheduled posts not updating the date when transitioning to publish status
* Page order number now respects depth (resets to 1 at every new level)
* Removing left and right parameters in order array
* Minified nestedSortable

= 0.1.1 =
* Fixes a problem with permalinks not updating
* Updated nestedSortable from 1.3.2 to 1.3.3

= 0.1 =
* Some functions rewritten
* Updated nestedSortable from 1.3.1 to 1.3.2
* Bug fixes

= 0.1b =
* First release.
