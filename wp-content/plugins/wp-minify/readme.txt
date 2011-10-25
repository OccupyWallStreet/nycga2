=== WP Minify ===
Tags: minify, js, css, html, javascript, cascading style sheets, optimize, performance, speed, http request, phpspeedy
Contributors: madeinthayaland
Donate link: http://omninoggin.com/donate/
Requires at least: 2.7
Tested up to: 3.2.1
Stable Tag: 1.1.8

This plugin uses the Minify engine to combine and compress JS and CSS files
to improve page load time.

== Description ==
This plugin integrates the [Minify engine](http://code.google.com/p/minify/)
into your WordPress blog.  Once enabled, this plugin will combine and compress
JS and CSS files to improve page load time.

= How Does it Work? =

WP Minify grabs JS/CSS files in your generated WordPress page and passes that
list to the Minify engine. The Minify engine then returns a consolidated,
minified, and compressed script or style for WP Minify to reference in the
WordPress header.

= Features =

* Easily integrate Minify into your WordPress blog.
* Minifies JavaScript, CSS, and HTML.
* Debug tools to help you debug your issues.
* Ability to include extra JS and CSS files for Minifying.
* Ability to exclude certain JS and CSS files for Minifying.
* Minified JS and CSS files can be placed wherever you want.
* Support for to minifying external files via caching.
* Ability to pass extra arguments to Minify engine.
* Expire headers for minified JS and CSS files.
* Detection and elimination of duplicate sources.
* Plugin hooks!

== Changelog ==

= 1.1.8 =
* Removed unnecessary error_log line.

= 1.1.7 =
* Set User Agent as "WP-Minify" when curling things.
* Cleaned up some old code.
* Added last modified time to pretty URLs.
* Updating it_IT translation (thanks Gianni Diiurno!).
* Added uk translation (thanks Jurko Chervony!).

= 1.1.6 =
* Extra precautions when printing last modified time variable (?m= vs. &amp;m=)

= 1.1.5 =
* Fixed documentation.

= 1.1.4 =
* Made pretty URL off by default since it doesn't work for everybody.

= 1.1.3 =
* Added it_IT translation (thanks Gianni Diurno!)

= 1.1.2 =
* Re-did options page and documentation to make things a bit more clear.

= 1.1.1 =
* Fixed incomplete /min/config.php file.

= 1.1 =
* Fixed "Class 'WPMinifyCommon' not found" errors that some are seeing.
* Added pretty URL for minified files so now you can stick it on a CDN.
* Organized options page.

= 1.0.4 =
* Fixed conflict with W3 Total Cache.

= 1.0.3 =
* Fixed "Invalid argument supplied for foreach()" error that some are having.

= 1.0.2 =
* Adding options for excluding certain URIs from WP Minify parsing.

= 1.0.1 =
* Reverting bbpress fix since it causs Minify to not even run.

= 1.0.0 =
* Added option to minify HTML.
* Added ability to NOT minify when "wp-minify-off=1" is in URL.
* Added option to report errors through FirePHP.
* Added "wp_minify_js_url" filter
* Added "wp_minify_css_url" filter
* Added "wp_minify_content" filter
* Enhanced "JavaScript files to include" feature.
* Enhanced "CSS files to include" feature.
* Fixed persistent SSL issue (Thanks belg4mit!)
* Fixed bbpress issue (Thanks foobar!)

= 0.9.3 =
* Fixed removing trailing slash in base path.

= 0.9.2 =
* Fixed "delimiter" error.
* Fixed admin sidebar.

= 0.9.1 =
* Automatically set far future expiration headers in Minify config (30 days).
* Automatically detect subdirectory from siteurl for passing as base path
  for Minify engine.

= 0.9.0 =
* Added SSL option to force http -> https on secured pages.
* Updated common.php and some other maintenance.

= 0.8.1 =
* Fixed WPMU subdirectory bug.

= 0.8.0 =
* Added ability to de-dupe sources
* Added ability to manually specify location of minified JS and CSS files.
* Fixed XHTML compliance bugs
* Cleaned empty lines in HTML output

= 0.7.4 =
* Fixed detecting if script is local or not.

= 0.7.3 =
* Fixed corner case on expire headers implementation.

= 0.7.2 =
* Add expire headers to combined JS and CSS files (Thanks Jan Seidl!).

= 0.7.1 =
* Fixed extra arguments for Minify engine.

= 0.7.0 =
* Added advanced options:
  - Minification on external files
  - Place JavaScript in footer
  - Extra arguments for Minify engine
* Removed wp_path option (Thanks Jan Seidl!)
* Fixed Output Buffer conflicts with other plugins that use output buffering
  such as All-in-One SEO and Anarchy Media Player.

= 0.6.5 =
* Fixed URL building (bug introduced by last release).
* Brought back WordPress path settings as some people with .htaccess issues
  may still need this.

= 0.6.4 =
* Fixed CSS regex to catch "media=''" case. (Thanks forum user bobmarkl33!)
* Modified minified JavaScript injection to the end of <head> (Thanks forum
  user bobmarl33!)
* Fixed WP Minify working with blogs installed in subdirectory of webroot.
  (Thanks forum user Luke!)
* Removed WordPress path settings as this is no longer needed per Luke's fix.

= 0.6.3 =
* Fixed JavaScript minification failure for large number of files.

= 0.6.2 =
* Fixed admin array_keys() bug
* Updated .pot file.

= 0.6.1 =
* Added .pot file.

= 0.6 =
* Upgraded to Minify engine 2.1.3.
* Added automatic Minify engine cache configuration.
* Fixed HTML5 <header> conflict.
* Fixed bug from blog installed in subdirectory.
* Fixed localization.

= 0.5 =
* Added option to disable JS or CSS minification.
* Fixed a few bugs.
* Admin facelift

= 0.4.1 =
* Fixed non-replaced </link> tag for valid XHTML usage.

= 0.4 =
* Automatically exclude CSS conditionals.
* Automatically exclude CSS media != all, or screen.
* Automatically exclude https URLs.
* Added sanity checking for buffer start & stop.
* Moved buffer start to init with priority 99999.
* Fixed "strpos()" warnings when settings have empty lines.

= 0.3.1 =
* Fixed "URL file-access disabled" errors.
* Fixed "implode()" warnings.

= 0.3 =
* WP 2.8 Compatibility

= 0.2.1 =
* Fixed another CSS exclusion bug (src_match -> href_match).
* Fixed JS WP Minify bug passing double forward slashes when not needed.
* Added media="screen" for minified CSS reference.

= 0.2 =
* Changed the way CSS and JS files are picked up.  No more wp_enqueue_*
     requirements!
* Fixed exclusion bug where specified files are not excluded from
     minification.
* Removed OMNINOGGIN dashboard widget.

= 0.1.1 =
* Fixed array_slice() warning in the admin dashboard.
* Fixed version check to not break page when $wp_version is empty.

= 0.1 =
* Initial release

= Credits =
This plugin utilizes the [Minify engine](http://code.google.com/p/minify/)
coded by [Steve Clay](http://mrclay.org/) and [Ryan Grove](http://wonko.com)
to perform all JS & CSS Minifying.

== Installation ==

1. Upload the plugin to your plugins folder: 'wp-content/plugins/'
2. Make sure 'wp-content/plugins/wp-minify/cache' is writeable by the
   web server. (try 'chmod 777 wp-content/plugins/wp-minify/cache')
3. Activate the 'WP Minify' plugin from the Plugins admin panel.

== Frequently Asked Questions ==

= Where is the documentation? =
If you are having problems with this plugin, please first take a look at the
various links under the "Documentation" section of the
[plugin page](http://omninoggin.com/wordpress-plugins/wp-minify-wordpress-plugin/).

= I still can't get it to work after reading the documentation! =
Please take a look at documentation available on the
[plugin page](http://omninoggin.com/wordpress-plugins/wp-minify-wordpress-plugin/).
to see if any of them can help you.  If not, feel free to post your issues
on the appropriate [plugin support forum](http://omninoggin.com/forum).
I will try my best to help you resolve any issues that you are having.

== License ==
All contents under the wp-minify/min/ directory is licensed under
[New BSD License](http://www.opensource.org/licenses/bsd-license.php) (which is
[GPL](http://www.gnu.org/copyleft/gpl.html) compatible).  All other
contents within this package is licensed under GPLv3.

== Screenshots ==

1. Options
2. Before WP Minify (11 JS requests @ 111KB)
3. After WP Minify (1 JS request @ 30KB)
