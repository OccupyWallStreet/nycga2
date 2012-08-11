=== Plugin Name ===
Contributors: IWEBIX, Dennis Nissle
Donate link: http://www.iwebix.de/
Tags: slideshow, content slideshow, slideshow, wp slideshow, featured content slideshow, gallery, content gallery, wordpress gallery
Requires at least: 3.0
Tested up to: 3.1.2
Stable tag: trunk

WP Content Slideshow is the perfect Slideshow for Wordpress. It displays up to 5 Posts or Pages with Tile, Description and Image for every Post.

== Description ==

* Added Post/Page Order/Sort Support! *

WP Content Slideshow shows up to 5 Posts or Pages in a very nice and powerful Javascript Slideshow. On the left side of the Slideshow it displays a Image for every Post. On the right side, there are all the Titles (and a small description under the Title) of the Posts/Pages. The Slideshow highlights the active Post and repeats automatically after getting to the 5th Post.
You have a powerful Administration Area to adjust the whole Layout of the Slideshow. Inserting new Posts/Pages now is as easy as can be. Just choose "Feature in WP-Content Slideshow" when editing a post or a page.

* Titles and small description
* Navigation possibility (when hitting a Title the Image appears)
* Image for every different Post (No need for timthumb anymore!)
* Supporting Pages now!
* Order/Sort Post/Pages by date, title, random
* Powerful Administration for Posts and Styles of the Slideshow
* Javascript Effects between different Posts

Check out the <a href="http://preview.wp-themix.org/plugins/wp-content-slideshow/" target="_blank">DEMO</a> at <a href="http://www.iwebix.de/" target="_blank" title="webdesign">webdesign</a>

== Installation ==

1. Upload /wp-content-slideshow/ to your Plugin directory (wp-content/plugins/)
2. Go to the 'Plugins' Page of your Administration Area and activate the Plugin.
3. Place `<?php include (ABSPATH . '/wp-content/plugins/wp-content-slideshow/content-slideshow.php'); ?>` in your template or use [contentSlideshow] as placeholder on a page/post to show the Slidehsow.
4. Edit a post or a page and choose "Feature in WP-Content Slideshow, right beneath your Editor
5. Set an image as "Featured Image" (Wordpress Post-Thumbnail Support)
6. Edit Layout & Settings in WP-Admin (Settings - WP-Content Slideshow)

== Frequently Asked Questions ==

= Where can I insert the Content Slideshow? =

You can Insert the Content Slideshow almost everywhere you want (it looks best under Navigation Bar)!

= Where can I edit the Stylesheet? =

You don't have to edit the Stylesheet you can make changes directly in your Administration Panel (Settings --> WP Content Slideshow)

= What is perfect Resolution for the Images?

You don't have to mind the resolutions. Pictures will be cropped automatically so that they fit the Slideshow!

= Thumbnails are not being generated?

We are not using timthumb anymore - Thumbnails are being automatically generated with Wordpress, when you are uploading an image. If you are already having pictures set you have to regenerate thumbnails using the "regenerate thumbnails plugin", otherwise old thumbnails won't display.

== Screenshots ==

1. This is how WP-Content Slideshow might look in your theme.
2. This is what your Administration Section for the Content Slideshow will look like.

== Changelog ==

= 1.0 =
* WP Content Slideshow is available for download

= 1.1 =
* Small Javascript Bugfixes

= 2.0 =
* Added Support for Pages
* WP 3.0 Ready using Post-Thumbnails
* Added meta box to easily insert certain posts/pages
* Added Thumbnail generation
* Fixed styles and options

= 2.1 =
* Not using timthumb anymore to avoid thumbnail problems
* Using Wordpress built-in thumbnail function

= 2.2 = 
* Added Option to Sort/Order Post/Pages
* Added Background Active Color Option

= 2.3 = 
* Minor Bug Fixes
