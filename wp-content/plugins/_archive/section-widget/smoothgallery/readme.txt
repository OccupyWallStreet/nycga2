=== SmoothGallery ===
Contributors: chschenk
Donate link: http://www.christianschenk.org/donation/
Tags: jondesign, smoothgallery, gallery, pictures, images
Requires at least: 2.0
Tested up to: 2.8
Stable tag: 1.15.1

Embed JonDesign's SmoothGallery into your posts and pages.

== Description ==

This plugin embeds JonDesign's [SmoothGallery](http://smoothgallery.jondesign.net/) into your posts and pages.

It's this simple:

* upload some pictures to a post/page
* use the shortcode "smoothgallery"
* add a custom field named "smoothgallery" with some [options](http://www.christianschenk.org/projects/wordpress-smoothgallery-plugin/#option)
* watch your gallery ;-)

There're a lot more possibilities with this plugin. Please have a more
detailed look at it and don't hesitate to leave a
[comment](http://www.christianschenk.org/projects/wordpress-smoothgallery-plugin/#respond)
if you'd like to suggest a feature, need help with the plugin or just
want to say how cool this is ;)

== Installation ==

1. Unzip the plugin into your wp-content/plugins directory and activate it
2. [Integrate](http://www.christianschenk.org/projects/wordpress-smoothgallery-plugin/#howto) it into a post or your theme.
3. You don't want to read all the instructions? No problem: [watch](http://www.christianschenk.org/projects/wordpress-smoothgallery-plugin/videos-for-this-plugin/) these videos.

== Frequently Asked Questions ==

= The visual editor replaces div-tags with p-tags =

If you're using the visual editor the div-tags may be replaced with
p-tags. To keep the markup intact you should disable the visual editor
if you'd like to edit a post/page that contains a SmoothGallery.

To do this, go to Users -> Your profile and uncheck "Use the visual
editor when writing". Once you've saved and published the post/page you
can activate the visual editor again - but make sure to disable it again
if you want to change a post that contains a SmoothGallery. 

= The SmoothGallery doesn't show up in Internet Explorer =

SmoothGallery doesn't seem to work with Internet Explorer if you use [Prototype](http://www.prototypejs.org/) on your site.

Read about a solution [here](http://www.christianschenk.org/blog/integrate-smoothgallery-into-wordpress/).
Basically you'll have to make sure that you don't use Prototype and
SmoothGallery on the same site or at least embed the SmoothGallery
inside an iFrame.

= What about integrating it into my theme? =

Read about this [here](http://www.christianschenk.org/projects/wordpress-smoothgallery-plugin/integration-into-your-theme/).

== Screenshots ==

1. Have a look at my gallery [here](http://www.christianschenk.org/projects/wordpress-smoothgallery-plugin/#example).

== Changelog ==

1.15.1

* Compatibility with WordPress 2.8. If you don't use 2.8 yet you don't have to update the plugin.
* Fixed bug with thumbnails that weren't generated if the cache directory wasn't writeable
* It will be easier and less error prone to change parameters that need quoting in the JavaScript now; like "textShowCarousel" etc.
* Compressed fleche1.png, fleche2.png and open.png

1.15

* It's possible to order the images now (thanks to [Jelte Liebrand](http://liebrand.co.uk/) )
* You can add custom links to the images via the media edit screen. Those are used as links in the gallery.
* Content of the iFrame should be valid XHTML 1.0 Strict now.
* Background color of iFrame can be customized (use "iframebgcolor" in shortcode)
* Automatic scaling of images can be enabled, i.e. carousel finaly works.
* i18n: en, de
* Widget can now use images from a directory
* Recent images box feature has a widget now

1.14

* Show your images from Flickr or Picasa
* You can use images from a custom directory now, i.e. you don't have to upload the images with WordPress but just to your webserver.
* Randomize the order of the images on every pageload with just one switch (use 'randomize=1' along with the shortcode).
* Some bugfixes

1.13.1

* Bugfix for images with alternative size, i.e. thumbnails.

1.13

* It's now possible to change the transitions between images.

1.12.1

* Small bugfix (forgot configuration for ReMooz and images)

1.12

* Added version 2.1beta1 and ReMooz; activate it in the config.php.

1.11.3

* Bugfix (compatibility with WordPress 2.7)

1.11.2

* Bugfix (finding the thumbnails might not work on some occasions)

1.11.1

* Bugfix (allow_call_time_pass_reference)

1.11

* Added a widget that you can use in your sidebar. You can customize them individually and add as many of them as you like.
* Enhanced available attributes indide an iFrame (shortcode)

1.10.1

* Bugfix (utils.php wasn't included at the right spot)

1.10

* Support for SmoothGallery inside an iFrame, i.e. you can have many galleries on a single page. Furthermore you can use scripts like Prototype.js or jQuery that are known to be incompatible with SmoothGallery on your page because they won't interfere with SmoothGallery any more
* Enhanced support for thumbnails generated with the Media Library

1.9

* Introduced a Shortcode ("smoothgallery") that automatically generates the markup for the gallery
* Recent-images-box now uses a Shortcode too
* You can use every option offered by SmoothGallery in the custom field now ([comment](http://www.christianschenk.org/projects/wordpress-smoothgallery-plugin/#comment-1704))
* With help of the Media Library we don't have to generate the thumbnails ([comment](http://www.christianschenk.org/projects/wordpress-smoothgallery-plugin/#comment-1961))
* Overall code cleanup
* Successfully tested with WP 2.6.2 ([comment](http://www.christianschenk.org/projects/wordpress-smoothgallery-plugin/integration-into-your-theme/#comment-2274))

1.8.1

* It's possible to specify the color of the border for the gallery.

1.8

* The custom configuration resides in a spearate file ("config.php") now to ease updating.
* A new feature called "recent images box" picks up the images that are attached to the most recent posts and generates a SmoothGallery for you. Read about this [here](http://www.christianschenk.org/projects/wordpress-smoothgallery-plugin/recent-images-box/).

1.7

* If you attach images to a post/page the markup will be generated for you. Have a look at the "SmoothGallery" box under "Advanced Options" on the edit screen. Check out this [video](http://www.christianschenk.org/projects/wordpress-smoothgallery-plugin/videos-for-this-plugin/#markup).  

== Licence ==

This plugin is released under the GPL.
