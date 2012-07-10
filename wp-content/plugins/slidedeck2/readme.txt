=== SlideDeck 2 Lite ===
Contributors: dtelepathy, kynatro, jamie3d, dtrenkner, oriontimbers, nielsfogt, bkenyon, dtlabs
Donate link: http://www.slidedeck.com/
Tags: dynamic, image gallery, iPad, jquery, media, photo, pictures, plugin, posts, Search Engine Optimized, seo, skinnable, slide, slide show, slider, slideshow, theme, touch support, video, widget, Flickr, Instagram, 500px, RSS, Pinterest, Google+, Twitter, YouTube, Vimeo, Dailymotion, Picasa, Dribbble
Requires at least: 3.3
Tested up to: 3.4.1
Stable tag: trunk
License: GPLv3

Create SlideDecks on your WordPress blogging platform. Manage SlideDeck content and insert them into templates and posts.

== Description ==

= Content Slider by SlideDeck 2 =
= Easily create content sliders for your WordPress site without code. Use images & text, plus YouTube, Flickr, Pinterest & more =
SlideDeck 2 for WordPress is a slider plugin that lets you easily create content sliders out of almost any content. Connect to a variety of Content Sources like YouTube, Flickr, Twitter, WordPress posts and Pinterest to create gorgeous, dynamic sliders in a few clicks - no coding is required.

**Requirements:** WordPress 3.3+, PHP5 and higher 

**Important Links:**

* [More Details](http://www.slidedeck.com/)
* [Knowledge Base](https://dtelepathy.zendesk.com/categories/20031167-slidedeck-2)

== Installation ==

1. Upload the `slidedeck2-lite` folder and all its contents to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Create a new SlideDeck from the “SlideDeck » Manage” menu in the control panel sidebar
4. Insert a SlideDeck in your post or page by clicking on the "Embed a SlideDeck" button above the rich text editor in the post/page view.

You can also place a SlideDeck in your template or theme via the PHP command “do_shortcode()”. Just pass the ID of the SlideDeck you want to render. For example:
`<?php echo do_shortcode( "[SlideDeck2 id=1644]" ); ?>`

Where 1644 is the SlideDeck's ID. You can also see this code snippet when you save a new SlideDeck for the first time.

== Screenshots ==

1. The SlideDeck manage view.
2. The choose source modal. This is where you can select a source to create your dynamic SlideDeck.
3. The editing interface showing the O-Town lens and digital-telepathy's Pinterest items.
4. The editing interface showing the current deck and the top of the lens selection area. 
5. One of the many settings page aviable to you. You can tweak dozens of settings for each SlideDeck.
6. The playback tab controls the slide transition, the animation easing, the start slide and more.

== Frequently Asked Questions ==

= Can I show multiple SlideDecks on one page? =
Of course! The only limitation is the number of slides per SlideDeck, and what kind of content you're sourcing for each slide. The greater the amount of content you're trying to show all at once, the slower your pages will render for your visitors, so avoid trying to show 10 SlideDecks, each with 10 YouTube videos, as it'll make your page pretty sluggish!


= What's with this sharing overlay? Can I disable that? =
Yes!
If you don't want your visitors to easily share your SlideDeck with their social networks, you can disable the sharing overlay in the deck settings. Just set "Show Overlay" to "Never."


= Can I Choose Which WordPress Pages to Show in a SlideDeck? (Featured Posts/Pages) =
If you want to use the WordPress Posts Content Source to add pages to your SlideDeck, but want to include/exclude certain pages then you'll have to add custom taxonomies to your pages. By default WordPress Posts have tags and categories, but Pages do not. The easiest way to mark certain pages on your WordPress site for inclusion in a SlideDeck is to give them categories or tags. A plugin like GD Custom Posts And Taxonomies Tools allows you to add custom taxonomies to your posts and pages. For instance, you could add a category called "Home Page SlideDeck" to your pages and then those pages can be pulled into your SlideDeck.


= What’s the difference between SlideDeck 1 and SlideDeck 2 for WordPress? =
Where to begin! The most important improvement is that SlideDeck 2 is designed to work smoothly with your existing content across a variety of Content Sources on the web, like Flickr,  your blog posts and YouTube. We think this is a way better approach instead of requiring you to create and format content specifically for your slider - as was necessary for SlideDeck in the past. This not only makes it far quicker and easier to create new sliders, but also allows you to breathe new life into content you’ve already invested time into creating. That's just the tip of the iceberg, though. You can get a much better idea as to what’s new with SlideDeck 2 by checking out the Features and Examples pages.


== Changelog ==
= 2.1.20120705 =
* Added min-width value to Flickr source flyout to fix rendering display in Webkit browsers (Safari/Chrome)
* Appended to the sort logic. If the deck only has one source, we don't sort by date. If it has more than one source, we sort by date.
* Added an additional sort option to the WordPress Posts content source. You can now sort by menu_order
* Fixed an issue with the YouTube content source where videos in a playlist did not have the correct date ordering under some circumstances
* Added Pinterest icon to the sources image on the manage page
* Refined lens selection view
* Added a "jQuery Hook" (custom event) for the re-filling of the deck options area and fixed a display issue with the options area

= 2.1.20120702 =
* Adjusted the YouTube API call so no related videos are shown at the end of playback
* Fixed the `slidedeck_lens_selection_before_lenses` and `slidedeck_lens_selection_after_lenses` hooks so they no longer throw notices

= 2.1.20120628 =
* Creation of a Lite version of the SlideDeck 2 Plugin
* Fixed some logic bugs with uploading lenses
* Improved deletion logic for user-uploaded lenses to work properly with server configurations where the owner of the lens folder is the FTP user and not the web server user
* Moved strip_shortcodes() command to take place after an image has been searched for instead of at initial setup of slide nodes for dynamic post content source
* Fixed a bug that was returning a bad URL for first image in content
* Improved the "first image in gallery" logic to properly retrieve the first listed image in a gallery that has not yet been intentionally sorted by the user
* Fixed a regression bug with post thumbnail support in the posts dynamic content source that was preventing SlideDecks from working properly with themes that don't have thumbnail support
* Fixed an issue where jQuery Masonry was not being enqueued

== Upgrade Notice ==
= 2.1.20120628 =
Lite Plugin is available