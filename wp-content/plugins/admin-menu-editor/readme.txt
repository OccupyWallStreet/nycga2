=== Admin Menu Editor ===
Contributors: whiteshadow
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=A6P9S6CE3SRSW
Tags: admin, dashboard, menu, security, wpmu
Requires at least: 3.0
Tested up to: 3.4.1
Stable tag: 1.1.9

Lets you edit the WordPress admin menu. You can re-order, hide or rename menus, add custom menus and more. 

== Description ==
Admin Menu Editor lets you manually edit the Dashboard menu. You can reorder the menus, show/hide specific items, change access rights, and more.

**Features**

* Edit menu title, access rights, URL, icon and so on. 
* Sort menu items via drag & drop.
* Hide/show any menu or menu item. A hidden menu is invisible to all users, including administrators.
* Move a menu item to a different submenu. 
* Create custom menus that point to any part of the Dashboard or an external URL.

The [Pro version](http://w-shadow.com/AdminMenuEditor/) lets you set per-role [menu permissions](http://w-shadow.com/admin-menu-editor-pro/documentation/permissions/), hide a menu from everyone except a specific user, export your admin menu, drag items between menu levels, make menus open in a new window and more.

**Notes**

* If you delete any of the default menus they will reappear after saving. This is by design. To get rid of a menu for good, either hide it or set it's access rights to a higher level.
* In the free version, you can't lower a menu's required access rights, but you can change them to be more restrictive.
* In case of emergency, you can reset the menu configuration back to the default by going to http://example.com/wp-admin/?reset\_admin\_menu=1

== Installation ==

**Normal installation**

1. Download the admin-menu-editor.zip file to your computer.
1. Unzip the file.
1. Upload the `admin-menu-editor` directory to your `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.

That's it. You can access the the menu editor by going to *Settings -> Menu Editor*. The plugin will automatically load your current menu configuration the first time you run it.

**WP MultiSite installation**

If you have WordPress set up in multisite ("Network") mode, you can also install Admin Menu Editor as a global plugin. This will enable you to edit the Dashboard menu for all sites and users at once.

1. Download the admin-menu-editor.zip file to your computer.
1. Unzip the file.
1. Create a new directory named `mu-plugins` in your site's `wp-content` directory (unless it already exists).
1. Upload the `admin-menu-editor` directory to `/wp-content/mu-plugins/`.
1. Move `admin-menu-editor-mu.php` from `admin-menu-editor/includes` to `/wp-content/mu-plugins/`.

Plugins installed in the `mu-plugins` directory are treated as "always on", so you don't need to explicitly activate the menu editor. Just go to *Settings -> Menu Editor* and start customizing your admin menu :)

*Notes* 
* Instead of installing Admin Menu Editor in `mu-plugins`, you can also install it normally and then activate it globally via "Network Activate". However, this will make the plugin visible to normal users when it is inactive (e.g. during upgrades).
* When Admin Menu Editor is installed in `mu-plugins` or activated via "Network Activate", only the "super admin" user can access the menu editor page. Other users will see the customized Dashboard menu, but be unable to edit it.
* It is currently not possible to install Admin Menu Editor as both a normal and global plugin on the same site.

== Screenshots ==

1. Plugin interface
2. A sample menu created by the plugin
3. Re-ordering menu items via drag and drop

== Changelog ==

= 1.1.8 =
* Fix author URL (was 404).
* Tested on WP 3.4.1
* Update plugin description. Some notes were no longer accurate for the current version.

= 1.1.7 = 
* Tested on WP 3.4
* Fixed a rare "failed to decode input" error.
* Fixed menus not being expanded/collapsed properly when the current menu item has been moved to a different sub-menu.
* Added a shortlist of Pro version benefits to the editor page (can be hidden).

= 1.1.6.1 =
* Tested on WP 3.3.2
* Added a user survey.

= 1.1.6 =
* Tested on WP 3.3.1.
* Fixed a couple 404's in the readme and the plugin itself.

= 1.1.5 =
* Fixed an error where there would be no custom menu to show.
* Removed the "Feedback" button due to lack of use. You can still provide feedback via blog comments or email, of course.

= 1.1.4 =
* Fixed the updater's cron hook not being removed when the plugin is deactivated.
* Fixed updates not showing up in some situations.
* Fixed the "Feedback" button not responding to mouse clicks in some browsers.
* Fixed "Feedback" button style to be consistent with other WP screen meta buttons.
* Enforce the custom menu order by using the 'menu_order' filter. Fixes Jetpack menu not staying put.
* You can now copy/paste as many menu separators as you like without worrying about some of them mysteriously disappearing on save.
* Fixed a long-standing copying related bug where copied menus would all still refer to the same JS object instance.
* Added ALT attributes to the toolbar icon images.
* Removed the "Custom" checkbox. In retrospect, all it did was confuse people.
* Made it impossible to edit separator properties.
* Removed the deprecated "level_X" capabilities from the "Required capability" dropdown. You can still type them in manually if you want.

= 1.1.3 = 
* Tests for WordPress 3.2 compatibility.

= 1.1.2 = 
* Fixed a "failed to decode input" error that could show up when saving the menu.

= 1.1.1 = 
* WordPress 3.1.3 compatibility. Should also be compatible with the upcoming 3.2.
* Fixed spurious slashes sometimes showing up in menus.
* Fixed a fatal error concerning "Services_JSON".

= 1.1 = 
* WordPress 3.1 compatibility.
* Added the ability to drag & drop a menu item to a different menu.
* Added a drop-down list of Dashboard pages to the "File" box.
* When the menu editor is opened, the first top-level menu is now automatically selected and it's submenu displayed. Hopefully, this will make the UI slightly easier to understand for first-time users.
* All corners rounded on the "expand" link when not expanded.
* By popular request, the "Menu Editor" menu entry can be hidden again.

= 1.0.1 =
* Added "Super Admin" to the "Required capability" dropdown.
* Prevent users from accidentally making the menu editor inaccessible.
* WordPress 3.0.1 compatibility made official.

= 1.0 =
* Added a "Feedback" link.
* Added a dropdown list of all roles and capabilities to the menu editor.
* Added toolbar buttons for sorting menu items alphabetically.
* New "Add separator" button.
* New separator graphics.
* Minimum requirements upped to WP 3.0.
* Compatibility with WP 3.0 MultiSite mode.
* Plugin pages moved to different menus no longer stop working.
* Fixed moved pages not having a window title.
* Hide advanced menu fields by default (can be turned off in Screen Options).
* Changed a lot of UI text to be a bit more intuitive.
* In emergencies, administrators can now reset the custom menu by going to http://example.com/wp-admin/?reset\_admin\_menu=1
* Fixed the "Donate" link in readme.txt
* Unbundle the JSON.php JSON parser/encoder and use the built-in class-json.php instead.
* Use the native JSON decoding routine if it's available.
* Replaced the cryptic "Cannot redeclare whatever" activation error message with a more useful one.

= 0.2 =
* Provisional WPMU support.
* Missing and unused menu items now get different icons in the menu editor.
* Fixed some visual glitches.
* Items that are not present in the default menu will only be included in the generated menu if their "Custom" flag is set. Makes perfect sense, eh? The takeaway is that you should tick the "Custom" checkbox for the menus you have created manually if you want them to show up.
* You no longer need to manually reload the page to see the changes you made to the menu. Just clicking "Save Changes" is enough.
* Added tooltips to the small flag icons that indicate that a particular menu item is hidden, user-created or otherwise special.
* Updated the readme.txt

= 0.1.6 =
* Fixed a conflict with All In One SEO Pack 1.6.10. It was caused by that plugin adding invisible sub-menus to a non-existent top-level menu.

= 0.1.5 =
* First release on wordpress.org
* Moved all images into a separate directory.
* Added a readme.txt

== Upgrade Notice ==

= 1.1.9 =
Optional upgrade. Just adds a couple of screenshots for the WordPress.org plugin description.