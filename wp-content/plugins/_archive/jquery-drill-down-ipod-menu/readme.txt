=== JQuery Drill Down Ipod Menu Widget ===
Contributors: remix4
Donate link: http://www.designchemical.com/blog/index.php/wordpress-plugins/wordpress-plugin-jquery-drill-down-ipod-menu-widget/#form-donate
Tags: jquery, drill down, menu, vertical, animated, css, navigation, widget
Requires at least: 3.0
Tested up to: 3.1
Stable tag: 1.3.1

Creates a widget, which allows you to create a drill down ipod style menu from any Wordpress custom menu using jQuery.

== Description ==

Creates a widget, which allows you to create a drill down ipod style menu from any Wordpress custom menu using jQuery. Drill down menus are ideal for managing large, complicated menus in a small, vertical, compact and fixed-sized area. They also offer a great alternative to accordion style and drop down menus. Features include - handles multiple levels, saved state using cookies and multiple menus on the same page.

To facilitate useability the drill down menu offers the option to have a count of the number of links for each level.

The drill down menu also offers 3 different ways to navigate:

* Using a breadcrumb style menu at the top of the drill down menu
* A back button to return to previous options
* Selecting previous link headers, which are fixed at the top of the menu so the user can see the path taken

Uses the jquery cookie plugin by [Klaus Hartl](http://www.stilbuero.de) for saving the menu state between pages.

= Menu Options =

The widget has several parameters that can be configured to help cutomise the drill down ipod menu:

* Save menu state (uses cookies) - Selecting this will allow the menu to remember its open/close state when browsing to a new page.
* Show counter - adds the number of lower level links next to each heading
* Show header - If not selected the current selected menu option will be shown as a heading above the menu options
* Link Type - 3 options, which control how the user navigates the menu:

	1. Breadcrumb - previous selected menu items are displayed as breadcrumbs at the top of the menu. Clicking one of these breadcrumb links will take the user back to that level
	2. Back Link - the previous menu option is displayed as a back link above the menu
	3. Header Link - Previously selected links stay fixed above the menu
	
* Default Header Text - The text that is shown in the header when the menu first initialises
* Reset Link Text - The text that is shown for the header link that will reset the menu
* Animation Speed - speed of the sliding effect
* Skin - Several sample skins are available to give examples of css that can be used to style your own ipod drill down menu

[__See demo__](http://www.designchemical.com/lab/demo-wordpress-jquery-drill-down-ipod-menu-plugin/)

== Installation ==

1. Upload the plugin through `Plugins > Add New > Upload` interface or upload `jquery-drill-down-ipod-menu` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. In the widgets section, select the 'jQuery Drill Down iPod Menu' widget and add to one of your widget areas
4. Select one of the WP menus, set the required settings and save your widget

== Frequently Asked Questions ==

= The menu appears on the page but does not work. Why? =

First - make sure that your custom menu has at least 2 levels. If it has only one level the ipod scrolling effect is not active.

One main reason for the menu failing to load properly is caused by the necessary code missing from the page footer. The plugin adds the required jQuery code to your template footer. Make sure that your template files contain the wp_footer() function.

Another likely cause is due to other non-functioning plugins, which may have errors and cause the plugin javascript to not load. Remove any unwanted plugins and try again. Checking with Firebug will show where these error are occuring.

== Screenshots ==

1. Widget in edit mode
2. Sample drill down ipod menus

== Changelog ==

= 1.3.1 =
* Fixed: IE7 layout bug

= 1.3 =
* Fixed: Bug with save state option

= 1.2 =
* Added: jQuery drilldown plugin includes check for cookie function

= 1.1 =
* Fix: Bug when using "No Theme" option

= 1.0 = 
* First release