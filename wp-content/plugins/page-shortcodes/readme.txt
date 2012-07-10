=== Page Shortcodes Plugin ===
Contributors: dflydev
Donate link: http://dflydev.com/d2code/wordpress/page-shortcodes-plugin/
Tags: page, shortcode, shortcodes
Requires at least: 2.8.6
Tested up to: 3.0.1
Stable tag: 0.1

Embed page lists and other page information in pages and posts.

== Description ==

Follow here: [Page Shortcodes](http://dflydev.com/d2code/wordpress/page-shortcodes-plugin/)

Embed page lists and other page information in pages and posts by way of
several shortcodes.

`<h3>[page_title name=my-page-slug]</h3>`
`[page_list name=my-page-slug]`

= Shortcodes =

The available shortcodes are:

1. [page_list]
1. [page_title]
1. [page_content]
1. [page_meta]
1. [page_permalink]

= Common attributes =

For all shortcodes, the "name" or "id" attributes must be specified.

 * "name" is the slug name for the pathed page. If your page is /foo/bar/baz,
   then specify name as 'foo/bar/baz'.
 * "id" is the ID for the page.

= [page_list] attributes =
1. "template" attribute
 * The name of the template to use for this page list.

= [page_meta] attributes =
1. "template" attribute
 * The name of the template to use for this page meta.
1. "meta" attribute
 * The name of the meta field to include.

== Installation ==

1. Download the plugin zip file
1. Unzip contents of plugin zip file
1. Upload the page-shortcodes directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Can I customize how the page list looks? =

Yes. Create a `ps-page-list.php` template in your theme.

= Can I create a custom template for page lists? =

Yes. Give a `template` attribute to your page list shortcode to specify which
page list template to use.

`[page_list name=my-page-slug template=my-custom-page-list]`

= Can I customize how the page meta is displayed? =

Yes. Create a `psp-page-meta.php` template in your theme.

= Can I create a custom template for page metas? =

Yes. Give a `template` attribute to your page meta shortcode to specify
which page meta template to use.

`[page_meta name=my-page-slug meta=some.meta.key template=my-custom-page-meta]`


== Screenshots ==

1. Editing a page to include content by way of the page shortcodes plugin.
2. Rendering of the previously shown page snippet.

== Changelog ==

= 0.1 =
* First release.

== Upgrade Notice ==

= 0.1 =
First release.