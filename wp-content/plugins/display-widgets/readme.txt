=== Display Widgets ===
Contributors: sswells
Donate link: http://strategy11.com/donate/
Tags: widget, widgets, admin, show, hide, page, sidebar, content, wpmu, wordpress, plugin, post, posts, content, filter, widget logic, widget context
Requires at least: 2.8
Tested up to: 3.5
Stable tag: 1.24

Simply hide widgets on specified pages. Adds checkboxes to each widget to either show or hide it on every site page.

== Description ==

Change your sidebar content for different pages, categories, custom taxonomies, and WPML languages. Avoid creating multiple sidebars and duplicating widgets by adding check boxes to each widget in the admin (as long as it is written in the WordPress version 2.8 format) which will either show or hide the widgets on every site page. Great for avoiding extra coding and keeping your sidebars clean. 

By default, 'Hide on Checked' is selected with no boxes checked, so all current widgets will continue to display on all pages. 

http://strategy11.com/display-widgets/

= Translations =
* Albanian ([Taulant](http://wporacle.com/ "Taulant"))
* Bahasa Malaysian (Jass at 100webhosting.com)
* Chinese ([Hanolex](http://hanolex.org "Hanolex"))
* Dutch (Alanya Hotels)
* French ([Fmarie](http://www.fmarie.net/ "Fmarie"))
* German ([Caspar Hübinger](http://glueckpress.com "Caspar Hübinger"))
* Hebrew ([Ariel](http://arielk.net "Ariel"))
* Japanese ([BNG NET](http://staff.blog.bng.net/ "BNG NET"))
* Polish (Soplica at artvision1.pl)
* Romanian (Nobelcom)
* Russian ([Serhij](http://darmoid.ru "Serhij"))
* Spanish ([Alicia García Holgado](http://grial.usal.es/pfcgrial "Alicia García Holgado"))
* Tagalog (Hanne at pointen.dk)

== Installation ==

1. Upload `display-widgets.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to the 'Widgets' menu and show the options panel for the widget you would like to hide.
4. Select either 'Show on Checked' or 'Hide on Checked' from the drop-down and check the boxes.


== Frequently Asked Questions ==

= Why aren't the options showing up on my widget? =

This is a known limitation. Widgets written in the pre-2.8 format don't work the same way, and don't have the hooks. Sorry.


== Screenshots ==

1. The extra widget options added.

== Changelog ==
= 1.24 =
* Fixed bug preventing boxes unchecking for some users

= 1.23 =
* Switched WPML language support from highest to lowest priority when determining whether to show or hide
* Reduced database size of options saved
* Changed 'login' to 'dw_login' parameter naming to remove conflicts with certain widgets
* Added French, Tagalog, and Polish translations

= 1.22 =
* Added WPML support
* Fix to allow more than 5 taxonomies
* Fix to allow more than 99 pages
* Changed 'include' to 'dw_include' parameter naming to remove conflict with Suffusion widget
* Added Albanian translation ([Taulant](http://wporacle.com/ "Taulant"))
* Added Bahasa Malaysian translation (100webhosting.com)

= 1.21 =
* Added Romanian translation (Nobelcom)
* Added Chinese translation ([Hanolex](http://hanolex.org "Hanolex"))

= 1.20 =
* Added Hebrew translation ([Ariel](http://arielk.net "Ariel"))
* Fix css typo to correctly show the pointer cursor to show/hide option under the headings

= 1.19 =
* Fixed option to insert IDs to work with posts

= 1.18 =
* Added custom taxonomy support
* Show category options even if there are no posts in them 
* Fixed expand and collapse bug in widget options

= 1.17 =
* Added Spanish translation ([Alicia García Holgado](http://grial.usal.es/pfcgrial "Alicia García Holgado"))
* Added Russian translation ([Serhij](http://darmoid.ru "Serhij"))

= 1.16 =
* Corrected naming of the Japanese translation files
* Added Dutch translation (Alanya Hotels)

= 1.15 =
* Added custom post type support
* Added German translation ([Caspar Hübinger](http://glueckpress.com "Caspar Hübinger"))

= 1.14 =
* Added Japanese translation ([BNG NET](http://staff.blog.bng.net/ "BNG NET"))

= 1.13 = 
* Added a PO file for translators

= 1.12 =
* Show only published pages, and increase the displayed page limit
* Toggle sections
* Added check boxes to hide/show for logged-in users
* Added text field to list post ids for posts not displayed

= 1.11 =
* WordPress 3.0 compatibility
* Fixed PHP notices

= 1.10 =
* Improved admin widget page efficiency and load time
* Fixed bug preventing widgets from being hidden/shown correctly on some subpages

= 1.9 =
* Add check box for front page
* Change category checkbox to apply not only to the category page, but also to posts in that category

= 1.8 =
* Added check box for search page under "Miscellaneous"

= 1.7 =
* Update for 2.9 compatibility

= 1.6 =
* Added category checkboxes

= 1.5 =
* Added "404 Page" checkbox

= 1.4 =
* Changed "Home Page" check box to "Blog Page"

= 1.3 =
* Added check box for Home page if it is the blog page
* Added check boxes for single post and archive pages
* Save hide/show option correctly for more widgets

= 1.2 =
* Save page check boxes for more widgets

= 1.1 =
* Fixed bug that prevented other widget options to be displayed

