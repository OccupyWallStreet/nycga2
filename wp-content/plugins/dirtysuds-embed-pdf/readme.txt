=== Embed PDF ===
Contributors: dirtysuds, pathawks
Donate link: http://www.pathawks.com/p/wordpress-plugins.html
Tags: WordPress,embed,google,media,plugin,plugins,Post,posts,simple,pdf,google,Google Docs
Requires at least: 2.9
Tested up to: 3.1.2
Stable tag: 1.03

Adds pseudo oembed support for PDF documents

== Description ==

Will embed a PDF file using Google Docs Viewer
Simply include the URL for a PDF document on it's own line, or wrapped in the embed tag like `[embed]http://example.com/file.pdf[/embed]` and the plugin will embed the PDF into the page using the Google Docs Viewer embed code.
The url must end with `.pdf`

Supported attributes in the embed tag are `class` `id` `title` `height` and `width`

== Installation ==

1. Upload `dirtysuds-embed-pdf` to the `/wp-content/plugins/` directory
2. Activate **DirtySuds - Embed PDF** through the 'Plugins' menu in WordPress
3. That's it. Now when you embed a PDF using the Wordpress `[embed]` shortcode, the plugin will embed the document using the Google Docs viewer


== Frequently Asked Questions ==

= How can I change the size of the PDF viewer? =

If you want to change the default size of media embedded in a post, that can be done in the admin interface under *Settings ⇒ Media Settings ⇒ Embeds*

If you'd just like to change the size of one instance, you can use the standard attributes of the WordPress embed shortcode.

   `[embed width="400" height="600"]http://example.com/document.pdf[/embed]`


= I have an idea for a great way to improve this plugin =

Great! I'd love to hear from you.

plugins@dirtysuds.com


== Changelog ==

= 1.04 =
* Changed URL for Google Docs Viewer to reflect change that Google made

= 1.03 =
* Automatically enable auto-embeds on plugin activation

= 1.02 =
* Added support for `gdoc` shortcode for compatibility with older plugins

= 1.01 =
* Added support for `class` and `id` attributes (Thanks, _Robert_)

= 1.00 =
* First version
* Works

== Upgrade Notice ==

= 1.04 =
* Fixed problem with Google Docs viewer.  Upgrade immediately.
