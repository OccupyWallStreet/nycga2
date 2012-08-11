=== Flickr Gallery ===
Contributors: dancoulter
Donate link: http://co.deme.me/donate/
Tags: photos, flickr, gallery, shortcodes
Requires at least: 2.9
Tested up to: 3.0.1
Stable tag: 1.5.2

Quickly and easily add Flickr galleries, photos, and even custom search results into your WordPress pages and posts.

== Description ==

Using the "shortcodes" system in WordPress 2.5 and up, this plugin will allow you to quickly and easily incorporate 
your Flickr photos into your WordPress pages and posts.

Features include:

* A quick gallery of your recent photos, photosets and most popular photos.
* Easy database caching (just click a checkbox) 
* Displays the photos from one photoset
* Displays all of a user's photos with given tags
* Displays the results of a custom search
* Inserts a single photo into your content
* Embeds Flickr's flash movie player for videos
* Authenticate to display your private photos
* Lightbox script makes it easy to browse photos without leaving the page
* Plugin API to let sites configure the tabs in their gallery
* View photosets in the gallery mode without leaving the page
* Lightboxes are now generated for every gallery mode
* WordPress MU Support
* Pagination in galleries
* All images smaller than "medium" will load in the lightbox effect (if enabled) when the user clicks on them.
* Add a "Collections" tab to the default gallery
* Select which tabs the gallery displays
* Set the lightbox to display larger than "medium" photos if the user's browser window is large enough.
* Easier "Web" based authentication to the Flickr API.
* **New**: Show the photo's description inside the lightbox alongside the larger photos.

== Installation ==

1. Upload the folder containing the plugin files.
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings -> Flickr Gallery to enter your basic settings

You will need to enter a Flickr API key.  The settings page has a link to a page where you can apply for a new key.

== Usage ==

* `[flickr-gallery]` - Creates a generic gallery for the user you specified in your settings page.
* `[flickr]http://www.flickr.com/photos/dancoulter/2619594365/[/flickr]` - Inserts the given image.  You can also just put the photo ID in there (the numeric string at the end of the link).

Other options:

* `[flickr-gallery mode="photoset" photoset="72157605870230826"]` - Displays all of the photos from the given Flickr photoset.
* `[flickr-gallery mode="tag" tags="foo" tag_mode="all"]` - Shows photos from your photostream with the specified tags (comma separated). If you leave off the tag_mode option, it defaults to "any".
* `[flickr-gallery mode="recent"]` - Shows recent photos from your photostream
* `[flickr-gallery mode="interesting"]` - Shows interesting photos from your photostream
* `[flickr-gallery mode="search" tags="foo" group_id="46862018@N00"]` - The search mode supports any arbitrary sort using the options listed here: http://www.flickr.com/services/api/flickr.photos.search.html
* `[flickr size="small" float="left"]http://www.flickr.com/photos/dancoulter/2619594365/[/flickr]` - Sets the size of the inserted image to "small" (defaults to "medium") and makes the next paragraph of text wrap around to the right of the image (you can specify "right" for the float parameter as well, which defaults to "none")
* `[flickr height="300" width="400"]http://www.flickr.com/photos/dancoulter/2422361554/[/flickr]` - This one is a video.  It'll automatically embed the flash movie for you.  If you want to change its size, you can use the width and height options.

Advanced options:

* If you want to disable pagination, just include `pagination=0` in your shortcode.