=== Floating Menu ===
Contributors: remix4
Donate link: http://www.designchemical.com/blog/index.php/wordpress-plugins/wordpress-plugin-jquery-floating-menu/#form-donate
Tags: jquery, flyout, drop down, floating, sliding, menu, vertical, animated, navigation, widget
Requires at least: 3.0
Tested up to: 3.10
Stable tag: 1.4

Floating Menu creates a sticky, floating menu widget from any Wordpress custom menu using jQuery.

== Description ==

Creates a widget, which adds a floating, sticky, drop down menu from any standard Wordpress custom menu using jQuery. Can handle multiple floating menus on each page and the location of each menu tab can be easily set from the widget control panel. Menu can also be activated either by 'hover' or 'click'.

= Menu Options =

The widget has several parameters that can be configured to help cutomise the floating menu:

* Event - Open/Close the menu using either 'hover' or 'click'.
* Width - Set the width of the menu
* Tab Text - Enter the text that you would like to use for the menu tab.
* Location & Aligment - Position can be set using a combination of location (Top or Bottom) and aligment (left or right). For each one you can also add the offset (in pixels) from the edge of the browser window. The slide out animation depends on the menu location:
	** Top Left or Top Right - menu slides down
	** Bottom Left or Bottom Right - menu slides up
	
* Set Alignment from Center - check this box to position the menu based on the center of the browser window as opposed to the sides - this ensures a fixed position even when the browser resolution changes
* Floating Speed - The speed for the menu floating animation
* Animation Speed - The speed at which the menu will open/close
* Auto-Close Menu - If checked, the menu will automatically slide closed when the user clicks anywhere in the browser
* Keep Open - If checked the tab content will remain open
* Disable Float - Check this box to disable the floating animation
* Skin - 4 different sample skins are currently available for styling the floating menu. Since there are no essential styles required to create the floating menu, these can easily be used to create your own custom menu theme.

= Shortcodes =

The plugin includes the feature to add text links within your site content that will open/close the floating tab.

1. [dcfl-link] - default link, which will toggle the menu open/closed with the link text "Click Here".
2. [dcfl-link text="Floating Menu"] - toggle the menu open/closed with the link text "Floating Menu".
3. [dcfl-link action=open] - open the menu with the default link text.
4. [dcfl-link action=close] - close the menu with the default link text.

[__See demo__](http://www.designchemical.com/lab/demo-wordpress-jquery-floating-menu-plugin/)

[__More information__](http://www.designchemical.com/blog/index.php/wordpress-plugins/wordpress-plugin-jquery-floating-menu/)

== Installation ==

1. Upload the plugin through `Plugins > Add New > Upload` interface or upload `floating-menu` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. In the widgets section, select the Floating Menu widget and add to one of your widget areas
4. Select one of the WP menus, set the required settings and save your widget

== Frequently Asked Questions ==

= The menu appears on the page but does not work. Why? =

One main reason for this is that the plugin adds the required jQuery code to your template footer. Make sure that your template files contain the wp_footer() function.

Another likely cause is due to other non-functioning plugins, which may have errors and cause the plugin javascript to not load. Remove any unwanted plugins and try again. Checking with Firebug will show where these error are occuring.

[__Also check out our floating menu faq page__](http://www.designchemical.com/blog/index.php/frequently-asked-questions/floating-menu/)

== Screenshots ==

1. Floating Menu widget in edit mode

== Changelog ==

= 1.3 = 
* Added: option to position menu from center of browser window

= 1.3 = 
* Added: disable floating animation option

= 1.2.1 = 
* Fixed: validation error

= 1.2 = 
* Added: shortcodes to create external links to open/close menu
* Added: 4 new skins
* Added: width: auto to menu tab CSS

= 1.1 = 
* Added: Ability to keep floating tab content open

= 1.0 = 
* First release

== Upgrade Notice ==
