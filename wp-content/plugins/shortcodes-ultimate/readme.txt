=== Plugin Name ===
Contributors: gn_themes
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=MMA2VA7JDXWDY
Tags: shortcode, shortcodes, short code, shortcodes, tab, tabs, button, buttons, jquery, box, boxes, toggle, spoiler, column, columns, services, service, pullquote, list, lists, frame, images, image, links, fancy, fancy link, fancy links, fancy buttons, jquery tabs, accordeon, slider, nivo, nivo slider, plugin, admin, photoshop, gallery, bloginfo, list pages, sub pages, navigation, siblings pages, children pages, permalink, permalinks, feed, document, member, members, documents, jcarousel, rss
Requires at least: 3.0
Tested up to: 4.0
Stable tag: 3.9.5

Provides support for multiple useful shortcodes


== Description ==

With this plugin you can easily create buttons, boxes, different sliders and much, much more. Turn your free theme to premium in just a few clicks. Using Shortcodes Ultimate you can quickly and easily retrieve many premium themes features and display it on your site. See screenshots for more information.

= !! Important note !! =

Be careful when updating to 4.0.0 version in next time. It will be massive update, that affect many code.

= Features =
* [Premium Addons](http://shortcodes-ultimate.com/) (coming soon)
* 30+ amazing shortcodes
* Handy shortcodes generator
* Custom CSS editor with syntax highlight
* Frequently updates
* Special widget
* International

= New in this version =
* Small fixes
* Generator select improved with [Chosen](http://harvesthq.github.com/chosen/)
* Farbtastic color picker

= Demo video =
[youtube http://www.youtube.com/watch?v=Q0jDDIjOKsM]

= More videos =
* [Shortcodes Ultimate Tutorial](http://www.youtube.com/watch?v=IjmaXz-b55I)
* [How to use nivo_slider, jcarousel and custom_gallery](http://www.youtube.com/watch?v=1QK4cceZrks)
* [How to use special widget](http://www.youtube.com/watch?v=YU3Zu6C5ZfA)
* [Creating jcarousel from category posts](http://www.youtube.com/watch?v=jgDsj_adPqM)

= Got a bug or suggestion? =
* [Support forum](http://wordpress.org/support/plugin/shortcodes-ultimate)
* [Plugin page](http://shortcodes-ultimate.com/)
* [Twitter](http://twitter.com/gn_themes)

= Translations =
* Fr - [Aurélien DENIS](http://wpchannel.com/)
* Sp - [Esteban Truelsegaard](http://www.netmdp.com/)
* De - [Matthias Wittmann](http://net-graphix.de/)
* Ru - [Vladimir Anokhin](http://gndev.info/)
* By - [Alexander Ovsov](http://webhostinggeeks.com/science/)
* Sk - [Viliam Brozman](http://www.brozman.sk/blog/)
* Lt - [Vincent G](http://www.host1free.com/)
* He - [Ariel Klikstein](http://www.arielk.net/)

Have a translation? [Contact me (for translators ONLY)](mailto:ano.vladimir@gmail.com)


== Installation ==

Unzip plugin files and upload them under your '/wp-content/plugins/' directory.

Resulted names will be:
  './wp-content/plugins/shortcodes-ultimate/*'

Activate plugin at "Plugins" administration page.


== Upgrade Notice ==

Upgrade normally via your Wordpress admin -> Plugins panel.


== Screenshots ==

1. Insert shortcode in 3 easy steps.
2. Heading, spoiler, tabs, quote, button.
3. Box, note, divider (top), list.
4. List styles.


== Frequently Asked Questions ==

= What mean compatibility mode? =
This mode adds a prefix to all plugin shortcodes

* [button] => [gn_button]
* [tabs] => [gn_tabs]
* [tab] => [gn_tab]
* etc.

= Is there WYSIWYG button? =
Search it near Upload/Insert buttons. See [screenshots](http://wordpress.org/extend/plugins/shortcodes-ultimate/screenshots/) or [screecast](http://www.youtube.com/watch?v=Q0jDDIjOKsM)

= How to use: nivo_slider, jcarousel, custom_gallery =
With these shortcodes you can create different galleries from attached to post images, or from category posts.

Way 1: gallery from post attachments

* Create new post
* Upload images
* Use next shortcode on pages, posts or even widgets

`[nivo_slider source="post=XX" link="image"]`

XX - ID of the post with uploaded images

Way 2: gallery from category

* Create some posts in some category
* Set the post thumbnails
* Use next shortcode on pages, posts or even widgets

`[nivo_slider source="cat=XX" link="post"]`

XX - ID of the category with new posts

And here is the [demo video 1](http://www.youtube.com/watch?v=1QK4cceZrks) and [demo video 2](http://www.youtube.com/watch?v=jgDsj_adPqM)

Also, you can use [jcarousel] and [custom_gallery] according these principles.


== Changelog ==

= 3.9 =
* More screencasts
* Special widget for shortcodes
* Small fixes
* Hebrew translation
* [Awesome tutorial by Digital Cascade TV](http://www.youtube.com/watch?v=IjmaXz-b55I)
* Partners section on settings page
* Generator select improved with [Chosen](http://harvesthq.github.com/chosen/)
* Farbtastic color picker

= 3.8 (security release) =
* 2 new translations (Sk, Lt)
* Donate button in control panel
* Updated timthumb.php (version 2.8.10)
* Added 2 useful screencasts

= 3.7 =
* Complete support for nested shortcodes. Check the FAQ page.
* New shortcode [label]
* New style for buttons [button style="5"]
* Fixed images ordering for [custom_gallery], [jcarousel] and [nivo_slider]

= 3.6 =
* Descriptions for [custom_gallery]
* Custom options for jwPlayer
* Fixed size option for sliders and gallery

= 3.5 =
* New shortcode [accordion] for muliple spoilers
* Improved spoiler shortcode (check settings page)
* Multiple tabs bugfix
* Authors can also use shortcode generator
* Nested shortcodes: spoiler, column, tabs, box, note

= 3.4 =
* Belarusian translation
* New shortcode [dropcap]

= 3.3 =
* Changed: [nivo_slider] and [jcarousel] (see docs in console)
* New shortcode: [custom_gallery]
* New parameter: [members login="0|1"]
* New shortcode: guests
* German translation

= 3.0 =
* Button for WYSIWIG editor (search it near Upload/Insert buttons)
* New shortcode: private (private notes for editors)
* Patched and secure timthumb.php

= 2.7 =
* French translation
* Fixed for work with new jQuery 1.6 in WP 3.2

= 2.5 =
* Theme integration

= 2.4 =
* New shortcode: jcarousel

= 2.3 =
* New admin page: Demo

= 2.2 =
* New shortcode: document
* New shortcode: members
* New shortcode: feed
* New attr: link="caption" for [nivo_slider]
* New attr: p for [subpages]
* New tabs style (style=3)

= 2.1 =
* New option: disable any script
* New option: disable any stylesheet
* New attribute for column shortcode - style
* New attribute for spoiler shortcode - style

= 2.0 =
* New shortcode: menu
* New shortcode: subpages
* New shortcode: siblings
* Some admin fixes
* New button attribute - class
* New button attribute - target
* Different tabs styles (1 old + 1 new)

= 1.9 =
* New shortcode: permalink
* New shortcode: bloginfo

= 1.8 =
* Some small additions
* Ajax admin page
* No-js compatibility
* Multiple tabs support

= 1.7 =
* Improved settings page design
* Added shortcode nivo_slider
* Added shortcode photoshop

= 1.6 =
* New admin panel
* Custom CSS editor with syntax hughlight
* Small fixes
* Added donation forms

= 1.5 =
* Added option "Compatibility mode"
* Added new button styles
* Added new list styles
* Added new shortcode media
* Added new shortcode table

= 1.4 =
* Added shortcode "Fancy link"

= 1.3 =
* Some fixes

= 1.2 =
* Localization support

= 1.1 =
* Added options page
* Fixed options saving

= 1.0 =
* Initial release

