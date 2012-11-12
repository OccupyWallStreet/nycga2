=== Shortcode Exec PHP ===
Contributors: Marcel Bokhorst
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=AJSBB7DGNA3MJ&lc=US&item_name=Shortcode%20Exec%20PHP%20WordPress%20Plugin&item_number=Marcel%20Bokhorst&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_LG%2egif%3aNonHosted
Tags: admin, shortcode, run, php, eval, execute, exec, code, post, posts, page, pages, comment, comments, sidebar, widget, widgets, rss, feed, feeds, AJAX, wpmu, tinymce
Requires at least: 3.1
Tested up to: 3.4.1
Stable tag: 1.44

Execute arbitrary, reusable PHP code in posts, pages, comments, widgets and RSS feeds using shortcodes in a safe and easy way

== Description ==

Using this plugin you can execute arbitrary [PHP](http://www.php.net/ "PHP") code using [shortcodes](http://codex.wordpress.org/Shortcode_API "Shortcode API") in your posts, pages, comments, widgets and RSS feeds, just like manually defined shortcodes. The shortcodes and associated PHP code are defined using the settings of this plugin. It is possible to parse and use shortcode parameters and to use shortcode content. Defined shortcodes can be deleted and disabled.

Advantages over other solutions:

1. Your texts do not have to contain PHP code
1. PHP code can be reused (by reusing the shortcode)
1. All PHP code is organized at one place
1. [Syntax highlighting](http://en.wikipedia.org/wiki/Syntax_highlighting "Syntax highlighting")
1. You can test your PHP code before using it
1. Import/export of shortcode definitions

For those concerned about security (hopefully everybody): only administrators can define shortcodes and associated PHP code (see also the [FAQ](http://wordpress.org/extend/plugins/shortcode-exec-php/faq/ "FAQ")).

Please report any issue you have with this plugin in [the forum](http://forum.faircode.eu/).

See my [other plugins](http://wordpress.org/extend/plugins/profile/m66b "Marcel Bokhorst").

== Installation ==

*Using the WordPress dashboard*

1. Login to your weblog
1. Go to Plugins
1. Select Add New
1. Search for *Shortcode Exec PHP*
1. Select Install
1. Select Install Now
1. Select Activate Plugin

*Manual*

1. Download and unzip the plugin
1. Upload the entire shortcode-exec-php/ directory to the /wp-content/plugins/ directory
1. Activate the plugin through the Plugins menu in WordPress

== Frequently Asked Questions ==

= Where can I define shortcodes? =

WordPress menu Tools > Shortcode Exec PHP

= What happens when I disable a shortcode? =

The shortcode will not be handled and will appear unprocessed.

= Who can access the settings and the PHP code? =

Users with *manage\_options* (single user) or *manage\_network* (multi user) capability (administrators).

= Who can use the defined shortcodes? =

Anyone who can create or modify posts, pages and/or widgets or can write comments.
Shortcode execution in widgets, excerpts, comments and RSS feeds is disabled by default (unless another plugin or your theme does enable it).
It is possible to restrict shortcode execution in posts and pages based on the capabilities of the post or page author (since version 1.18).

= How are PHP errors handled? =

Because the [PHP eval function](http://php.net/manual/en/function.eval.php "PHP eval function") is used, errors cannot be handled unfortunately, so test your code thoroughly.

= How many shortcodes can I define? =

Unlimited.

= I get a blank page when I use a shortcode on a large post/page =

This can happen if the [PCRE backtrack](http://php.net/manual/en/pcre.configuration.php "PCRE backtrack") value is too low. Try increasing it using the plugin settings.

= Where are the shortcode definitions stored? =

The shortcode name, options and PHP code are stored as WordPress options.

= How can I change the styling of the settings? =

1. Copy *shortcode-exec-php.css* to your upload directory to prevent it from being overwritten by an update
2. Change the style sheet to your wishes; the style sheet contains documentation

= How do I test a shortcode with parameters? =

Indirectly, by using default values.

= Should I use PHP opening and closing tags? =

No, omit both *`<?php`* and *`?>`*.

= Can I embed HTML code? =

Yes, if you enclose the HTML code with *`?>`* and *`<?`*.

= My shortcode appears with a question mark behind it =

The post or page author has insufficient privileges to execute shortcodes.

= My code doesn't work! =

Note that your code is not directly executed in the WordPress environment, but in a function.
This means for example that a *global $wpdb;* is needed to access the [database class](http://codex.wordpress.org/Function_Reference/wpdb_Class "wpdb class").

= Where can I ask questions, report bugs and request features? =

You can use the [the forum](http://forum.faircode.eu/).

== Screenshots ==

1. Shortcode exec PHP

== Changelog ==

= 1.44 =
* Bugfix: html entities (by *Joe Pruett*, thanks!)
* Added Lithuanian (lt\_LT) by [Host1Free](http://www.host1free.com/ "Host1Free")

= 1.43 =
* Bugfix: added missing file

= 1.42 =
* Added link to [Pro version](http://www.faircode.eu/scepro/ "Shortcode Exec PHP Pro")

= 1.41 =
* Bugfix: require *manage_options* capability when site activated on a multisite network
* Note: PHP4 support will be dropped from the next release

= 1.39 =
* Bugfix: require *manage_options* capability when site activated on a multisite network

= 1.38 =
* New feature: option to disable [wpautop](http://codex.wordpress.org/Function_Reference/wpautop "wpautop")
* Updated support page
* Updated Chinese (zh\_CN) translation by [Jie](http://thejie.org/ "Jie")
* Updated Dutch (nl\_NL) and Flemish (nl\_BE) translations
* Updated Norwegian (nb\_NO) translation by [Stein Ivar Johnsen](http://www.idyrøy.no/ "Stein Ivar Johnsen")
* You can download the development version [here](http://downloads.wordpress.org/plugin/shortcode-exec-php.zip)

= 1.37 =
* Removed [Sustainable Plugins Sponsorship Network](http://pluginsponsors.com/)

= 1.36 =
* Bugfix: PHP 4 compatibility

= 1.35 =
* Bugfix: no EditArea on network sites

= 1.34 =
* New feature: import/export (requires [SimpleXML](http://php.net/manual/en/book.simplexml.php "SimpleXML"))
* Updated Dutch (nl\_NL) and Flemish (nl\_BE) translations
* Updated Norwegian (nb\_NO) translation by [Stein Ivar Johnsen](http://www.idyrøy.no/ "Stein Ivar Johnsen")
* Tested with WordPress 3.3

= 1.33 =
* Improvement: removed dependency on *PLUGINDIR*
* Changed to minimum WordPress version 3.1
* Updated Norwegian (nb\_NO) translation by [Stein Ivar Johnsen](http://www.idyrøy.no/ "Stein Ivar Johnsen")

= 1.32 =
* Added *Sustainable Plugins Sponsorship Network* again
* Updated Dutch (nl\_NL) and Flemish (nl\_BE) translations

= 1.31 =
* Removed *Sustainable Plugins Sponsorship Network*

= 1.30 =
* Bugfix: solved a few warnings

= 1.29 =
* Bugfix: jQuery compatibility WordPress version 3.2

= 1.28 =
* Improvement: added title to description field
* Updated Dutch (nl\_NL) and Flemish (nl\_BE) translations

= 1.27 =
* Improvement: added description field for TinyMCE

= 1.26 =
* Bugfix: remove old shortcode definition when renaming
* Bugfix: added *stripslashes* for shortcode name
* Improvement: better example

= 1.25 =
* Bugfix: menu when network activated

= 1.24 =
* Bugfix: suppressing PHP warnings if *ini_set* disabled
* Improvement: fixed some PHP notices
* Improvement: trimming text input

= 1.23 =
* Added Norwegian (nb\_NO) translation by [Stein Ivar Johnsen](http://www.idyrøy.no/ "Stein Ivar Johnsen")

= 1.22 =
* Bugfix: compatibility with PHP 4

= 1.21 =
* Bugfix: make sure required capability for authors is set
* Fixed some PHP warnings

= 1.20 =
* Bugfix: support for WordPress 3.1, tested and working now

= 1.19 =
* Support for WordPress 3.1 network of sites (multisite)

= 1.18 =
* Enhancement: case insensitive sorting of shortcodes
* Better compatiblity with other TinyMCE buttons
* New feature: shortcuts for jumping to shortcode definitions
* New feature: required capability for authors to use shortcodes, see [FAQ](http://wordpress.org/extend/plugins/shortcode-exec-php/faq/ "FAQ")
* Bugfix: shortcode processing in RSS feeds can be turned off now
* Updated Dutch (nl\_NL) and Flemish (nl\_BE) translations
* More donate buttons ...

= 1.17 =
* Bugfix: made EditArea working on tools page
* Bugfix: display toggle editor for new shortcode when not displaying initially
* Bugfix: no scroll to top when enabling editor for new shortcode the first time
* Extra security check for TinyMCE editor shortcode button popup
* New feature: display last used shortcode attributes
* Updated Dutch (nl\_NL) and Flemish (nl\_BE) translations

= 1.16 =
* Added option to select required capability for TinyMCE button
* Added tools menu entry for non-network sites
* Updated Dutch (nl\_NL) and Flemish (nl\_BE) translations

= 1.15 =
* TinyMCE editor button by default disabled for security reasons
* Updated Dutch (nl\_NL) and Flemish (nl\_BE) translations

= 1.14 =
* New feature: TinyMCE editor insert shortcode button
* Updated Dutch (nl\_NL) and Flemish (nl\_BE) translations
* Tested with WordPress version 3.1 RC 3

= 1.13 =
* Added option to turn off code editor initially
* Updated Dutch (nl\_NL) and Flemish (nl\_BE) translations
* Fixed JavaScript error: initializing code editor on options page only
* Tested with WordPress version 3.1 beta 1

= 1.12 =
* Added separators between shortcode definitions
* Fixed index after deleting shortcodes

= 1.11 =
* Added option to make shortcodes global for multi user sites
* Updated Dutch (nl\_NL) and Flemish (nl\_BE) translations

= 1.10 =
* Support for multi user sites

= 1.9 =
* Replaced ajax gets by posts

= 1.8 =
* Using default PCRE configuration as minimum

= 1.7 =
* Buffer output by default for new shortcodes
* Minimum value of 100,000 for PCRE configurations

= 1.6.1 =
* Added Farsi (fa\_IR) translation by [Hamid](http://hamidoffice.com/ "Hamid")

= 1.6 =
* Added options to configure PCRE

= 1.5 =
* Using https transport when needed

= 1.4 =
* 'I have donated' removes donate button

= 1.3.1 =
* Added an option to disable html entitiy encoding
* Updated Dutch (nl\_NL) and Flemish (nl\_BE) translations

= 1.3 =
* Removed decoding/encoding of html entities

= 1.2.2 =
* Added option to store css in upload folder

= 1.2.1 =
* Constructor compatibility with PHP 5.3.3+

= 1.2 =
* Added option to handle echoed output
* Updated Dutch (nl\_NL) and Flemish (nl\_BE) translations

= 1.1 =
* Using character encoding of menu Settings > Reading, normally UTF-8.

= 1.0 =
* Added Dutch (nl\_NL) and Flemish (nl\_BE) translations
* No complaints so far, updating to version 1.0

= 0.6 =
* Added Revert button, which will undo unsaved/untested changes
* Better error handling

= 0.5 =
* Scrolling to top after loading EditArea´s
* Disabling EditArea´s on test, save, etc.

= 0.4.1 =
* More compatible ajax handling

= 0.4 =
* Syntax highlighting
* In-place add, update and delete of shortcodes (using AJAX)
* Shortcodes can be tested in the administration backend

= 0.3 =
* Only administrators can see options and shortcode definitions now
* Shortcodes are sorted alphabetically in the administration backend

= 0.2 =
* Added options to enable shortcodes in excerps, comments and RSS feeds
* Added options to change width and height of PHP code textarea
* Improved layout of options

= 0.1 =
* Initial version

= 0.0 =
* Development version

== Upgrade Notice ==

= 1.44 =
One bugfix, new translation

= 1.43 =
One bugfix

= 1.42
Added link to Pro version

= 1.41 =
One bugfix

= 1.39 =
One bugfix

= 1.38 =
One new feature, translation updates

= 1.37 =
Compliance

= 1.36 =
Compatibility

= 1.35 =
One bugfix

= 1.34 =
One new feature, compatibility

= 1.33 =
Compatibility

= 1.32 =
Compatibility

= 1.31 =
Compatibility

= 1.30 =
One bugfix

= 1.29 =
One bugfix

= 1.28 =
One improvement, updated translations

= 1.27 =
One improvement

= 1.26 =
Bugfixes, improvement

= 1.25 =
Bugfix

= 1.24 =
Bugfix

= 1.23 =
Translation

= 1.22 =
Compatibility

= 1.21 =
Bugfix

= 1.20 =
Bugfix

= 1.19 =
Compatibility

= 1.18 =
New features, enhancements, bugfix, compatibility

= 1.17 =
Security, bug fixes

= 1.16 =
Select required capability for TinyMCE button

= 1.15 =
Security

= 1.14 =
TinyMCE editor insert shortcode button

= 1.13 =
New option, bug fix

= 1.12 =
Usability, bug fix

= 1.11 =
New feature: global shortcodes for multi user sites

= 1.10 =
Security

= 1.9 =
Compatibility

= 1.8 =
Better minimum PCRE configuration

= 1.7 =
Buffer output by default for new shortcodes

= 1.6.1 =
Farsi translation

= 1.6 =
New feature: PCRE configuration

= 1.5 =
Compatibility

= 1.4 =
New feature: remove donate button

= 1.3.1 =
Compatibility

= 1.3 =
Compatibility

= 1.2.2 =
Compatibility

= 1.2.1 =
Compatibility

= 1.2 =
Option to handle echoed output

= 1.1 =
Character encoding

= 1.0 =
Production release

= 0.6 =
Revert button

= 0.5 =
Better EditArea handling

= 0.4.1 =
Compatibility

= 0.4 =
Easier editing, syntax highlighting, shortcode testing

= 0.3 =
Better security, shortcode sorting

= 0.2 =
Added options to enable shortcodes in excerps, comments and RSS feeds and to set the size of the PHP code box

= 0.1 =
Initial version

== Acknowledgments ==

This plugin uses:

* [EditArea](http://www.cdolivet.com/index.php?page=editArea "EditArea")
by *Christophe Dolivet* and published under the GNU Lesser General Public License

* [jQuery JavaScript Library](http://jquery.com/ "jQuery") published under both the GNU General Public License and MIT License

All licenses are [GPL-Compatible Free Software Licenses](http://www.gnu.org/licenses/license-list.html#GPLCompatibleLicenses "GPL compatible").
