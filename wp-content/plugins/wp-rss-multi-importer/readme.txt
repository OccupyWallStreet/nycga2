=== WP RSS Multi Importer ===
Contributors: allenweiss
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=M6GC7V8BARAJL
Tags: rss, feeds, aggregation, aggregator, import
Requires at least: 2.9
Tested up to: 3.4.2
Stable tag: 2.32
Imports and merges multiple RSS Feeds. 8 templates, customize, sort, paginate, limit feeds/page and by category, and include excerpts with images. 

== Description ==

If you want to put RSS feeds on your site, look no further.  WP RSS Multi Importer gives you the most flexibility with templates, sorting by date, categorizing your feeds, pagination, a widget and much more.

= See How It Works =

[youtube http://www.youtube.com/watch?v=BPvjMMxjWWQ]

Quick start video:

[youtube http://www.youtube.com/watch?v=K2ImrLW6rZY]

See the various templates included:

[youtube http://www.youtube.com/watch?v=5PwO8eCiD1w]


= Features =

* Templates - choose from 7 different templates, create your own and save it
* Pagination option - select number of posts per page
* Select number of posts per feed you want to show
* Select number of posts on a page of your web site (when not in pagination mode)
* Separate out Today from Earlier posts
* Sort by date (ascending or descending) 
* Output feed name as (Source, Sponsor, Via, or Club)
* Show an excerpt (and select the number of words to show - 50, 100, 200, 300)
* Select how you would like the links to open (in a Lightbox, a new window, or the current window)
* Set the links as no-follow or not
* Suppress images in excerpts of you want
* Resize images in excerpts (may slow down how quickly the page loads)
* Allow users to determine whether to show-hide excerpts
* Set caching time for faster page loading

These features are all set in the settings tab in the admin panel.

= Credit = 

[__Allen Weiss__](http://www.allenweiss.com/wp_plugin)

== Installation ==

1. Upload the `wp-rss-multi-importer` folder to the `/wp-content/plugins/` directory
2. Activate the WP RSS Multi Importer plugin through the 'Plugins' menu in WordPress
3. Configure the plugin by going to the `RSS Multi Importer` submenu that appears in your `Settings` admin menu.
3. Use the shortcode in your posts or pages: `[wp_rss_multi_importer]` or use the widget.
4. Limit which feeds get shown on a page by using a parameter in the shortcode, like: [wp_rss_multi_importer category="#,#"] or choose the categories in the widget.

You can also use other parameters for the DEFAULT template which can be customized in the shortcode...this includes:

* Headline font size
* Headline bold weight
* Style of the Today and Earlier tags
* If using excerpt, symbol or word you want to indicate More..
* Width of leading image in the excerpt
* Suppress date or attribution from posts


== Frequently Asked Questions ==
= How can I output the feeds in my theme? =

Use the shortcode in your posts and pages:
[wp_rss_multi_importer]
Make sure the shortcode is entered when the input is set to HTML (versus Visual)

If you want to limit the feeds to those in a given category, make sure to first
assign the feed to a category, then use this shortcode on your page or post:
[wp_rss_multi_importer category="#"]
Assign multiple categories using a comma delimited list:
[wp_rss_multi_importer category="#,#,#"]

Use the widget.  If your theme allows for widgets, you'll find the RSS Multi Importer Widget there.
Configure your feeds in the administration panel, then choose the categories, number of posts, sorting method, optional scrolling, and more in the widget admin.

If you want to put this in the code on your theme, you can do it like this:

echo do_shortcode('[wp_rss_multi_importer]'); 

== Screenshots ==

1. Adding feeds and assigning categories.

2. Adding new categories.

3. Options panel.

4. User view - with excerpts and images.

== Change Log ==

= Version 2.32 =
* Added new vertical scroll template. Added cron hourly service.  Cache shortcode parameter added.
= Version 2.31 =
* Fixed ability to open widget feeds in lightbox and allow several shortcodes on the same page.
= Version 2.30 =
* Added templates, improved admin interface, fixed several bugs.  Switched caching methods to allow for more real time RSS if desired. Updated colorbox version.
= Version 2.25 =
* Fixed bug that caused feeds to disappear from admin panel when several categories added.  No data was lost by users.
= Version 2.24 =
* Usability improvements added for new users.  Quick start video added.
= Version 2.23 =
* Added a workaround when other plugins did not restrict their javascript to their own admin pages..thus causing problems for some users.
= Version 2.22 =
* Caching made optional. Enhanced the admin section by putting option settings on a separate panel. More options added to the number of words in the excerpt.
= Version 2.21 =
* Performance improvements. Ability to change color of hyperlinked titles added with shortcode parameter.  Also, specify number of posts per feed via shortcode parameter.
= Version 2.20 =
* Pagination option added.
= Version 2.19 =
* Option added to not load colorbox.  Some themes already load colorbox and this causes a conflict.
= Version 2.18 =
* Show-hide option added to excerpts.
= Version 2.17 =
* No follow option added to all links.  Fixed bug with widget when no category selection is made.
= Version 2.16 =
* Date formats are now consistent with international formats. Added ability to optionally float images to the left.  Options to show excerpts in widget and number of post/feed added.
= Version 2.15 =
* Multiple categories can now be used in the shortcode and widget.  Widget now has small footprint option with motion.  Excerpt images can be sized to a certain width. Additional customized parameters added.
= Version 2.11 =
* Fixed bug that kept new window and same window links to not be live
= Version 2.1 =
* Added a widget option for displaying feeds, better image formatting, separate Today from Earlier posts.
= Version 2.01 =
* Fixed bug that caused some users to have problems when they haven't added any categories.
= Version 2.0 =
* Added ability to assign feeds to a category and output feeds from a given a given category.  Limit posts on a page.  Uninstall now works for multiuser sites.  Solved problem for some users where the LightBox option was conflicting with other plugins that also relied on Lightbox or Colorbox.
= Version 1.1 =
* Added ability to determine where the links should open (Lightbox, new window, current window)
= Version 1.0 =
* Fixed problem where showing text before the shortcode rendered after the shortcode
= Version 0.7 =
* Fixed problem with showing excerpts withe foreign characters
= Version 0.6 =
* Fixed bugs in Lightbox and eliminated error message
= Version 0.5 =
* Added ability to include short descriptions - excerpts (if they exist in the RSS feed)
