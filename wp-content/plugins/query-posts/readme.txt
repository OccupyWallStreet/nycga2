=== Query Posts ===
Contributors: greenshady
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=3687060
Tags: widget, pages, posts, sidebar, page
Requires at least: 3.0
Tested up to: 3.0.1
Stable tag: 0.3.2

A WordPress widget that gives you unlimited control over showing posts and pages.

== Description ==

The *Query Posts* widget was written to allow users that don't know their way around PHP to easily show posts in any way they'd like.  It's like having a cool WordPress developer as a friend ready to do your bidding.  Seriously.

The widget has over 40 options to choose from.  You can list posts by category, tag, custom taxonomies, author, date, time, name, or anything you can imagine.  You can choose to show the full content, excerpts, or even a simple list.  You can order the posts in all sorts of ways.  Oh, and you can even show pages.

This is the widget that keeps users out of the code and gives them the ability to display items on their site how they want.

== Installation ==

1. Upload `query-posts` to the `/wp-content/plugins/` directory.
1. Activate the plugin through the *Plugins* menu in WordPress.
1. Go to *Appearance > Widgets* and place the *Query Posts* widget where you want.

More detailed instructions are included in the plugin's `readme.html` file.

== Frequently Asked Questions ==

= Why was this plugin created? =

End users sometimes want to do complicated things but don't nave the technical *know-how* of seasoned developers.  This creates a massive barrier between users that can have *really* cool sites and those that have to use what their theme developer gave them.

This plugin is about removing that barrier.

= How do I set it up? =

There's not a lot of work you have to do.  Just add the widget to your theme's widget area(s).  If anything, you'll probably be overwhelmed by the myriad of options.  I've included a handy guide on what each option means with supplemental reading material.  Just check out the `readme.html` included with the plugin download.

== Changelog ==

**Version 0.3.2**

* Clean up of debug notices.
* Only show thumbnail settings if theme supports post thumbnails or the `get_the_image()` function is present.
* Apply widget title filters to the widget title.
* Better escaping of widget settings form elements.

**Version 0.3.1**

* Fix post types so individual IDs work.
* Fix `enable_widget_title` for some instances where it didn't work.
* Fix `post_class` issue for the instances where it didn't work.
* Make sure correct image size is used.
* Only use `post_mime_type` when it's set.
* Use `tag` instead of `tag_slug__in` for the post tag taxonomy.

**Version 0.3**

* Completely redesigned the entire widget to just work better.
* Important!  Users will likely have to reset any instances of the Query Posts widget in use.

**Version 0.2.1**

* Fixed the checkbox issue where it didn't save on widget settings update.

**Version 0.2**

* Recoded the widget from the ground up to use WP 2.8's new widget class.  Users will likely need to reset each widget instance because of this.
* Fixes bug in WP 2.8 where more than one *Query Posts* widget wouldn't display.
* Dropped support for versions of WP below 2.8.
* New setting: `post_type`.
* New setting: `post_status`.
* Added ability to show posts in ordered list as well as the normal unordered list.
* Hover tooltips to explain what each setting means.
* Added support for custom taxonomies created by users and other plugins for posts.

**Version 0.1**

* This is version 0.1.  Everything's new!

== Screenshots ==

1. View of the *Query Posts* widget settings.