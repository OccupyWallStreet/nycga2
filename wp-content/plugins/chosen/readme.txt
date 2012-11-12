=== Chosen for WordPress ===
Contributors: thenbrent
Tags: jquery, select, chosen, contact form
Requires at least: 3.1
Tested up to: 3.2.1
Stable tag: 0.2

Make long, unwieldy select boxes much more user-friendly.


== Description ==

This plugin implements the [Chosen jQuery Plugin](http://harvesthq.github.com/chosen/) for WordPress.

[Chosen](http://harvesthq.github.com/chosen/) makes long, unwieldy select boxes much more user-friendly. 

This plugin applies Chosen to any select box in your post and page content. The relevant Javascript & CSS files are only loaded if the post in question includes a select box or a `[contact-form]` shortcode. 

The [Grunion Contact Form](http://wordpress.org/extend/plugins/grunion-contact-form/) & [Contact Form 7](http://wordpress.org/extend/plugins/grunion-contact-form/) plugins both use a the `[contact-form]` shortcode. 

If you need to force the Chosen script & styles to load on a page, simply include `[chosen]` within the page. 


== Installation ==

1. Unzip and upload `/chosen/` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Chosen will apply to all select boxes automatically and select boxes within a `[contact-form]` shortcode


== Screenshots ==

1. Simple Select Box made sleek with Chosen.
2. Multiple Select Box with groups.
2. Multiple Select Box with groups.


== Changelog ==

= 0.2 =
Chosen no longer being applied to Admin Select boxes by default.
Improving shortcode parsing.

= 0.1 =
First version.

== Upgrade Notice ==

= 0.2 =
Upgrade to prevent admin boxes being Chosenified.
