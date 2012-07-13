=== Gravity Forms Popup Widget ===
Contributors: sirshurf
Donate link: http://alex.frenkel-online.com/donate/
Tags: gravity forms, gravityforms, jqueryUI, dialog, popup, widget
Requires at least: 3.2
Tested up to: 3.3.1
Stable tag: 0.6

A widget to add Gravity Form in dialog popup, has an option to add a delay, a position, and an introduction page.

== Description ==

A widget to add Gravity Form in dialog popup, has an option to add a delay, a position, and an introduction page.

From version 0.3 can be opted in/out to work ont he homepage.
From version 0.5 you can use a button to open the popup.
From version 0.6, added an option to show the popup only ones in X views (random generated) - Requested by .

= Under the hood: =

* Built on Gravity Form 1.5.2, and uses it for working.
* jQuery UI added from Wordpress itself
* Uses jQueryUI CSS

*Enjoy using Gravity Form Popup Widget? Please consider [making a small donation](http://alex.frenkel-online.com/donate/) to support the software’s continued development.*

== Installation ==

1. Upload the `gravity-form-popup-widget` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions == 

Install the plugin, and add it as a widget.

= How to set delay? = 

The delay is set from the widget managment. The delay is in mileseconds.

= What are the possable values to position the window? =

From the JQuery UI manual:

Possible values:
1) a single string representing position within viewport: 'center', 'left', 'right', 'top', 'bottom'.
2) an array containing an x,y coordinate pair in pixel offset from left, top corner of viewport (e.g. [350,100])
3) an array containing x,y position string values (e.g. ['right','top'] for top right corner)

== Changelog ==

= 0.6 =
* Added an option to be able to "select" ones in how many views the popup will appear. Uses rand() in order to get current user number.

= 0.5 =
* Added an option to start the dialog from button

= 0.4 =
* A Misstype...

= 0.3 =
* Added an option to optin/optout working on homepage (Default is off)
* Moved all text to a self domain: gravity-forms-popup-widget

= 0.2 =
* Updated to work with Wordpress 3.3
* Added an option to position the window.

= 0.1 =
* Initial Alpha Release

