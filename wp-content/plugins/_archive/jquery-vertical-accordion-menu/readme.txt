=== JQuery Accordion Menu Widget ===
Contributors: remix4
Donate link: http://www.designchemical.com/blog/index.php/wordpress-plugins/wordpress-plugin-jquery-vertical-accordion-menu-widget/#form-donate
Tags: jquery, dropdown, menu, vertical accordion, animated, css, navigation, widget
Requires at least: 3.0
Tested up to: 3.13
Stable tag: 3.1.2

Creates vertical accordion menus from any Wordpress custom menu using jQuery. Add menus using either widgets or shortcodes.

== Description ==

Creates vertical accordion menus from any Wordpress custom menu using jQuery. Add menus using either widgets or shortcodes. Features include - handles multiple levels, saved state using cookies, add count of number of links and option of selecting "click" or "hover" events for triggering the menu.

The plugin has several parameters that can be configured to help cutomise the vertical accordion menu. These can either be set via the widget control panel or by passing parameters in a shortcode:

= Widget Options for Menu =

The plugin has several parameters that can be configured to help cutomise the vertical accordion menu:

* Click/Hover - Selects the event type that will trigger the menu to open/close
* Auto-close open menus - If checked this will allow only one menu item to be expanded at any time. Clicking on a new menu item will automatically close the previous one.
* Save menu state (uses cookies) - Selecting this will allow the menu to remember its open/close state when browsing to a new page.
* Auto Expand Based on Current Page/Item - If checked, this option will automatically expand sub-menus based on the current page/post based on the inherent Wordpress custom menu css classes - e.g. select this option if you would like the menu to automatically expand when the user clicks a link other than the accordion menu.
* Disable parent links - If selected, any menu items that have child elements will have their links disabled and will only open/close their relevant sub-menus. Do not select this if you want the user to still be able to browse to that item's page.
* Close menu (hover only) - If checked the menu will automatically fully close after 1 second when the mouse moves off the menu - only available if event type is "hover"
* Show Count - If checked the menu will automatically add a count showing the number of links under each parent menu item
* Class Menu - Set the CSS class of the Wordpress menu. If blank the default class "menu" will be used
* Class Disable - Input the CSS class for parent menu items that should be disabled - i.e. the child sub-menu remains open
* Hover delay - This setting adds a delay to the hover event to help prevent the menu opening/closing accidentally. A higher number means the cursor must stop moving for longer before the menu action will trigger
* Animation Speed - The speed at which the menu will open/close
* Skin - Several sample skins are available to give examples of css that can be used to style your accordion menu

Note: care should be taken when selecting the hover event as this may impact useability - adding a hover delay and reducing the animation speed may help reduce problems with useability

= Using Shortcodes =

The minimum requirement to use a shortcode is to include the name of the menu that you want to use for the accordion - the name must match one of the menus created in the Wordpress menu admin page. To add a menu using shortcodes use the following code:

[dcwp-jquery-accordion menu="Test Menu"]

Optional shortcode parameters for customising the menu (refer to widget settings above for information):

event - click/hover (default = click)
auto_close - true/false (default = false)
save - true/false (default = false)
expand - true/false (default = false)
disable - true/false (default = false)
close - true/false (default = false)
count - true/false (default = false)
menu_class - optional (default = menu)
disable_class - optional (no default)
hover - 600
animation - slow/normal/fast (default = slow)
skin - black/blue/clean/demo/graphite/grey (default = No Theme)

For more information please check out the plugin home page:

[__Plugin Home Page__](http://www.designchemical.com/blog/index.php/wordpress-plugins/wordpress-plugin-jquery-vertical-accordion-menu-widget/)
[__See Demo__](http://www.designchemical.com/lab/demo-wordpress-vertical-accordion-menu-plugin/)

== Installation ==

1. Upload the plugin through `Plugins > Add New > Upload` interface or upload `jquery-vertical-accordion-menu` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. In the widgets section, select the jQuery accordion menu widget and add to one of your widget areas
4. Select one of the WP menus, set the required settings and save your widget

== Frequently Asked Questions ==

= The menu appears on the page but does not work. Why? =

First - make sure that your custom menu has at least 2 levels. If it has only one level the open/close effect is not active.

One main reason for the menu failing to load properly is caused by the necessary code missing from the page footer. The plugin adds the required jQuery code to your template footer. Make sure that your template files contain the wp_footer() function.

Another likely cause is due to other non-functioning plugins, which may have errors and cause the plugin javascript to not load. Remove any unwanted plugins and try again. Checking with Firebug will show where these error are occuring.

[__Also check out our jquery accordion menu faq page__](http://www.designchemical.com/blog/index.php/frequently-asked-questions/jquery-vertical-accordion-menu/)


== Screenshots ==

1. Widget in edit mode
2. Sample vertical accordion menus

== Changelog ==

= 3.1.2 =
* Updated: accordion shortcode

= 3.1.1 =
* Updated: wp_enqueue_script hook

= 3.1 =
* Fixed: Auto-close feature with multiple menus

= 3.0 =
* Added: Ability to add menus using shortcodes

= 2.6 =
* Added: Ability to disable parent menu items using a CSS class
* Added: Ability to set menu CSS Class - default "menu"
* Update: Revision to auto-expand system
* Update: jquery.dcjqaccordion.2.8.js

= 2.5.4 =
* Fix: Bug with save state option

= 2.5.3 =
* Added: jQuery accordion plugin includes check for cookie function

= 2.5.2 =
* Update: jquery.dcjqaccordion.2.7.js - fix bug with count option

= 2.5.1 =
* Fix: Bug when using "No Theme" option

= 2.5 =
* Added: Auto-expand now independent of save state option

= 2.4 =
* Added: Ability to auto-expand menu based on the Wordpress current page/menu item classes

= 2.3 =
* Edit: Plugin now works with Cufon text

= 2.2 =
* Added: Option to show count of number of child links
* Edit: Plugin updated to use jquery plugin version 2.3
* Edit: Cookie name set based on widget ID

= 2.1 =
* Edit: Security for dynamic skins

= 2.0 =
* Added : Ability to select either hover or click to activate menu
* Added : Hover delay setting for hover event
* Added : Close menu option for hover event

= 1.1 =
* Fixed : Duplicate ID with themes adding ID to widget container
* Fixed : Set cookie path

= 1.0 = 
* First release