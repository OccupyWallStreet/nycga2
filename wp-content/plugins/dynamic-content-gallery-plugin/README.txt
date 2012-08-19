=== Dynamic Content Gallery ===

Version: 3.3.5
Author: Ade Walker
Author page: http://www.studiograsshopper.ch
Plugin page: http://www.studiograsshopper.ch/dynamic-content-gallery/
Contributors: studiograsshopper
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=10131319
Tags: gallery,images,posts,rotator,content-slider
Requires at least: 3.0
Tested up to: 3.2.1
Stable tag: 3.3.5

Creates a dynamic gallery of images for latest or featured content posts, categories, pages and Custom Post Type posts.


== Description==

This plugin creates a dynamic gallery of images for latest and/or featured content using either the JonDesign SmoothGallery script for mootools, or a custom jQuery script.  The plugin dynamically creates the gallery from your latest and/or featured content by either automatically pulling in the first Image Attachment from relevant Posts/Pages, or by specifying image URLs in a DCG Metabox in the Write screen for the relevant Posts/Pages. Additionally, default images can be displayed in the event that Posts/Pages don't have an Image Attachment or manually specified image. A Dashboard Settings page gives access to a comprehensive range of options for populating the gallery and configuring its look and behaviour. The DCG can be added to your theme as a Widget, or by using a template tag. 

For best results, make sure that your theme supports Post Thumbnails, introduced in WP 2.9.

Compatible with network-enabled (multisite) WordPress 3.0, though available plugin options are slightly reduced.

**Key Features**
----------------

* Auto Image Management option - automatically pulls in first Image Attachment from relevant Posts/Pages
* Auto Carousel thumbnails, using WP's Post Thumbnail feature.
* SmoothGallery javascript updated to use latest version of mootools (v1.2.4).
* New custom jQuery script - now much closer in look and feel to the mootools version
* A choice of 4 different methods for populating the gallery -  Multi Option, One Category, ID Method or Custom Post Type.
* Up to 15 gallery images (One Category/Custom Post Type methods), 9 gallery images (Multi Option), or unlimited for ID Method (with custom page Sort Order).
* Provides for a system of default images which will be displayed in the event an image has not been defined.
* Displays the Post/Page title and a user-definable description in the Slide Pane.
* Images can be linked to external URLs.
* User settings for image file management, CSS and javascript options.
* Built-in configuration validation checks and error message reporting. 
* Valid xhtml output.
* WP Multisite compatible (with some differences in the Settings available to the user).

**Further information**
-----------------------
Comprehensive information on installing, configuring and using the plugin can be found [here](http://www.studiograsshopper.ch/dynamic-content-gallery/)

* [Configuration Guide](http://www.studiograsshopper.ch/dynamic-content-gallery/configuration-guide/)
* [Documentation](http://www.studiograsshopper.ch/dynamic-content-gallery/documentation/)
* [FAQ](http://www.studiograsshopper.ch/dynamic-content-gallery/faq/)
* [Error messages info](http://www.studiograsshopper.ch/dynamic-content-gallery/error-messages/)

All support is handled at the [Studiograsshopper Forum](http://www.studiograsshopper.ch/forum/). I do not have time to monitor the wordpress.org forums, therefore please post any questions on my site's forum.


== Installation ==


**Installing for the FIRST TIME**
--------------------------------------------

1. Download the latest version of the plugin to your computer.
2. Extract and upload the folder *dynamic-content-gallery-plugin* to your */wp-content/plugins/* directory. Please ensure that you do not rename any folder or filenames in the process.
3. Activate the plugin in your Dashboard via the "Plugins" menu item.
4. Go to the plugin's Settings page, and configure your settings.

Note for WordPress Multisite users:

* Install the plugin in your */plugins/* directory (do not install in the */mu-plugins/* directory).
* In order for this plugin to be visible to Site Admins, the plugin has to be activated for each blog by the Network Admin. Each Site Admin can then configure the plugin's Settings page in their Admin Settings.


**Upgrading from version 3.2, 3.2.1, 3.2.2, 3.2.3, 3.3, 3.3.1**
---------------------------------------------------------------
Follow the upgrade instructions [here](http://www.studiograsshopper.ch/dynamic-content-gallery/documentation/#faq_43).


**Upgrading from version 2.2, 3.0, 3.1**
----------------------------------------
Follow the upgrade instructions [here](http://www.studiograsshopper.ch/dynamic-content-gallery/documentation/#faq_43).
Version 3.2 introduced some changes to the handling of Custom Fields dfcg-desc, dfcg-image and dfcg-link, which requires existing Custom Field data to be upgraded. The first time you visit the plugin's Settings page after upgrading from version 3.1 or earlier, you will be prompted to perform this Custom Field upgrade. Follow the on-screen instructions to perform this upgrade. Further information regarding this Custom Field upgrade can be found [here](http://www.studiograsshopper.ch/wordpress-plugins/dynamic-content-gallery-v3-2-released/). 



== Configuration and set-up ==

Only basic information is shown here. Comprehensive information on installing, configuring and using the plugin can be found at http://www.studiograsshopper.ch/dynamic-content-gallery/

**Instructions for use**
------------------------

**Using the plugin** 

To display the dynamic gallery in your theme, add this code to your theme file wherever you want to display the gallery:

`<?php dynamic_content_gallery(); ?>`

**Note:** Do not use in the Loop.

Alternatively, add it as a Widget via Dashboard>Appearance>Widgets.

**Assigning Images to Posts**

Either select Auto in the Image Management options to automatically pull in Image Attachments from relevant Posts/Pages, or enter your image URL in the Write Post/Page screen DCG Metabox Image URL field. (The exact form of the image URL depends on whether your DCG Image File Management Settings are set to FULL or PARTIAL URL). 

Slide Pane text can be configured in three ways - Manual, Auto or None

**Manual:**
* Enter a Slide Pane Description in the Write Post/Page screen DCG Metabox. For example: Here's our latest news!

**Auto:**
* Select Auto option to automatically create custom Post/Page excerpts from your Post/Page content.

**None:**
* Select None if you don't want to display a Slide Pane Description. (Post/Page title will still display with this option)

*Note for WP Multisite users*: Use the Media Uploader (accessed via the Add Media button in Dashboard > Posts > Edit) to upload your images and to find the full URL to be used in the Write Post/Page screen DCG Metabox Image URL field. See the Settings page for further information on how to do this. This tip is good for WordPress too - especially if using the FULL URL option in your [Image file management](http://www.studiograsshopper.ch/dynamic-content-gallery/documentation/#faq_32) Settings. An even better tip for Multisite setups is to use the Auto Image Management option to automatically pull in post attachments.



== Frequently Asked Questions ==

**How does it work?**
---------------------
The plugin provides four ways to populate the gallery:

* Multi Option: user-definable combination of categories and Posts to display up to 9 images
* One Category: display up to 15 images from one selected category
* ID: designed for Pages, this method can also be used for mixing Pages and Posts in the gallery.
* Custom Post Type: display up to 15 images from one selected Custom Post Type

Image file management settings provide comprehensive options for how images are referenced, either by Auto, Full URL or Partial URL options.

Default images can be defined for each category (One Category and Multi Option display methods), which are used as "fall-backs" in the event that a Post does not have the necessary custom field set up, and thereby ensures that the gallery will always display images.  (Note that this functionality is not available when used in WordPress Multisite).

There are a wide range of CSS and javascript Settings for configuring the look and behaviour of the gallery.

The plugin is supplied with an updated version of the original Smoothgallery mootools script and a jQuery alternative, selectable via the plugin's Settings page.


**Download**
------------

Latest stable version is available from http://wordpress.org/extend/plugins/dynamic-content-gallery-plugin/ 


**Support**
-----------

This plugin is provided free of charge without warranty.  In the event you experience problems you should visit these resources:

* [Dynamic Content Gallery home page](http://www.studiograsshopper.ch/dynamic-content-gallery/)
* [Configuration Guide](http://www.studiograsshopper.ch/dynamic-content-gallery/configuration-guide/)
* [Documentation](http://www.studiograsshopper.ch/dynamic-content-gallery/documentation/)
* [FAQ](http://www.studiograsshopper.ch/dynamic-content-gallery/faq/)
* [Error messages info](http://www.studiograsshopper.ch/dynamic-content-gallery/error-messages/)

If, having referred to the above resources, you still need assistance, visit the support page at http://www.studiograsshopper.ch/forum/.  Support is provided in my free time but every effort will be made to respond to support queries as quickly as possible. I do not have time to monitor the wordpress.org forums, therefore please post any questions on my site's forum.

Thanks for downloading the plugin.  Enjoy!

If you have found the plugin useful, please consider a Donation to help me continue to invest the time and effort in maintaining and improving this plugin. Thank you!


**Troubleshooting**
-------------------

In the event of problems with the plugin, refer to the Resources listed above.

Use the in-built Error messages (printed to the page source as HTML comments) for information about configuration errors and guidance on how to fix them.


**Known Issues**
----------------

There are no known issues as such, but there are some behaviours which you should be aware of.  The tips mentioned below are a good place to start in the event that you experience a problem with the plugin.

1. Javascript conflicts.  By default the plugin uses SmoothGallery which is built on the Mootools javascript framework.  This framework may conflict with other plugins which use either the same javascript framework or a conflicting one.  In the event of problems with the gallery, and you are unable to resolve the conflict, try using the supplied jQuery script instead, which you can select in the plugin's Settings page.

2. Known conflicts: A list of plugins which are known to conflict with the mootools gallery script can be found at http://www.studiograsshopper.ch/forum/

3. The mootools gallery script will not run properly if it cannot find the first image in the gallery. It also requires a minimum of 2 images.

4. In order to reduce loading time it is recommended to match your image dimensions to the visible dimensions of the gallery and optimise the filesize in your image editor.

5. To benefit from the new Auto Image Management options your theme needs to support WP's Post Thumbnails feature, introduced in WP 2.9. See this [FAQ] (http://www.studiograsshopper.ch/dynamic-content-gallery/documentation/#faq_32) for how to add Post Thumbnails support to your theme.

If you find any bugs, or have suggestions for future features, please leave a message on the [Support Forum](http://www.studiograsshopper.ch/forum/).



**Acknowledgements**

The Dynamic Content Gallery plugin uses the mootools SmoothGallery script developed by Jonathan Shemoul of JonDesigns.net, and a custom jQuery script developed by developed by Maxim Palianytsia, and was forked from the original Featured Content Gallery v1.0 developed by Jason Schuller. Grateful acknowledgements to Jonathan, Maxim and Jason.

Many thanks and props to [Benjamin Mueller](http://inkblought.com/) for contributing code for the Custom Post Type integration into the DCG.


== Screenshots ==
1. Dynamic Content Gallery



== Upgrade Notice ==

= 3.3.5 =
Fixes HTML markup error in dfcg-admin-metaboxes.php (missing </em> tag in External Link block)



== Changelog ==

= 3.3.5 =
* Released	4 December 2010
* Bug fix:	Fixes HTML markup error in dfcg-admin-metaboxes.php (missing </em> tag in External Link block)

= 3.3.4 =
* Released	1 December 2010
* Feature:	Gallery background colour option added
* Feature:	on/off option for Slide Pane animation added to jQuery script (v2.6)
* Enhance:	Tidied up DCG Metabox markup and contents
* Bug fix:	jQuery script conflict with Adblock browser add-on fixed (v2.7)
* Bug fix:	jQuery script vertical image alignment with small images fixed (v2.7.5)
* Bug fix:	Fixed PHP warning re undefined $cat_selected variable in dfcg-gallery-constructors-jq-smooth.php

= 3.3.3 =
* Released	7 November 2010
* Bug fix:	Upgraded jQuery script to v2.5 to fix IE img alignment, and non-linking img when showArrows is off
* Bug fix:	Added z-index:1; to #dfcg-fullsize selector in dfcg-gallery-jquery-smooth-styles.php
* Bug fix:	Fixed slide pane padding issue in #dfcg-text selector in dfcg-gallery-jquery-smooth-styles.php
* Bug fix:	Fixed IE img link disappearing. Changed CSS in #dfcg-imglink in dfcg-gallery-jquery-smooth-styles.php

= 3.3.2 =
* Released	21 September 2010
* Feature:	Added showArrows checkbox for mootools and jQuery, navigation arrows now optional from within Settings
* Bug fix:	Fixed URL error to loading-bar-black.gif 
* Bug fix:	Fixed Slide Pane options errors / hidden fields in dfcg-admin-ui-functions.php

= 3.3.1 =
* Released	15 September 2010
* Bug fix:	Fixed options handling of new 3.3 options in dfcg-admin-core.php and dfcg-admin-ui-screen.php

= 3.3 =
* Released	14 September 2010
* Feature:	Support for Custom Post Types added
* Feature:	New Auto Image Management option - pulls in Post/Page Image Attachment
* Feature:	Carousel thumbnails now generated using WP Post Thumbnails feature
* Feature:	New jQuery script, replaces galleryview script. Plays nicer with jQuery 1.4.2 used by WP3.0
* Feature:	Gallery images and thumbnails can now be automatically populated by post image attachments
* Feature:	Mootools js updated to use Mootools 1.2.4
* Enhance:	Constructor functions cleaned up and improved
* Enhance:	Pages method now called ID Method (as both Post and Page ID's can be specified)
* Enhance:	dfcg_pages_method_gallery() renamed to dfcg_id_method_gallery()
* Enhance:	dfcg_jq_pages_method_gallery() renamed to dfcg_jq_id_method_gallery()
* Enhance:	DCG Metabox visible in both Write Posts and Write Pages, if ID Method is selected
* Enhance:	New tabbed interface for the DCG Settings Page
* Enhance:	Tooltips added to DCG Settings Page to declutter the interface
* Enhance:	Contextual help now moved to DCG Settings Page Help tab. dfcg-admin-ui-help.php deprecated.
* Enhance:	Cleaned up interface text strings, re-worded some strings to make info more understandable
* Bug fix:	Removed unnecessary noConflict() call in dfcg_jquery_scripts() function
* Bug fix:	Fixed html entities encoding for alt attribute in ID Method contructors (formerly Pages method). Props: Joe Veler.

= 3.2.3 =
* Released	11 April 2010
* Bug fix:	Fixes contextual help compatibility issue with WP3.0

= 3.2.2 =
* Released	08 February 2010
* Feature:	DCG Widget added
* Enhance:	Updated dfcg_ui_1_image_wp() info re DCG Metabox
* Enhance:	Updated dfcg_ui_multi_wp() info re DCG Metabox
* Enhance:	Updated dfcg_ui_onecat_wp() info re DCG Metabox
* Enhance:	Updated dfcg_ui_pages_wp() info re DCG Metabox
* Enhance:	Updated dfcg_ui_defdesc() info re DCG Metabox
* Enhance:	Updated dfcg_ui_columns() info re DCG Metabox
* Enhance:	Updated dfcg_ui_create_wpmu() info re DCG Metabox
* Enhance:	Updated contextual help text in dfcg_admin_help_content() re DCG Metabox
* Enhance:	Updated Error Message text in dfcg_errors() re DCG Metabox
* Bug fix:	Added conditional tags to add_action, add_filter hooks in main plugin file

= 3.2.1 =
* Released	03 February 2010
* Bug fix:	Fixed PHP warning on undefined index when _dfcg-exclude is unchecked
* Bug fix:	Fixed missing arg error in dfcg_add_metabox() (in dfcg-admin-metaboxes.php)
* Bug fix:	Fixed metabox error of adding extra http:// when using Partial URL settings (dfcg-admin-metaboxes.php)
* Bug fix:	Added sanitisation routine to dfcg_save_metabox_data() to remove leading slash when using Partial URL setting
* Bug fix: 	Increased sanitisation cat01 etc char limit to 6 chars to avoid problems with large cat IDs

= 3.2 =
* Released	31 January 2010
* Feature:	Added custom sort order option for Pages Method using _dfcg-sort custom field
* Feature:	Added "no description" option for the Slide Pane
* Feature:	Manual description now displays Auto description if _dfcg-desc, category description and default description don't exist
* Feature:	Added Metabox to Post/Page Editor screen to handle custom fields
* Feature:	Added _dfcg-exclude postmeta to allow specific exclusion of a post from multi-option or one-category output
* Feature:	Added postmeta upgrade routine to convert dfcg- custom fields to _dfcg-
* Enhance:	Added text-align left to h2 in jd.gallery.css for wider theme compatibility
* Enhance:	Updated inline docs
* Enhance:	$dfcg_load_textdomain() moved to dfcg-admin-core.php
* Enhance:	$dfcg_errorimgurl variable deprecated in favour of DFCG_ERRORIMGURL constant
* Enhance:	New function dfcg_query_list() for handling multi-option cat/off pairs, in dfcg-gallery-core.php
* Enhance:	Function dfcg_admin_notices() renamed to dfcg_admin_notice_reset()
* Enhance:	Tidied up Error Message markup and reorganised dfcg-gallery-errors.php, with new functions
* Enhance:	Renamed function dfcg_add_page() now dfcg_add_to_options_menu()
* Enhance:	jd.gallery.css modified to remove open.gif (looked rubbish in IE and not much better in FF)
* Enhance:	Moved Admin CSS to external stylesheet and added dfcg_loadjs_admin_head() function hooked to admin_print_scripts_$plugin
* Bug fix:	Fixed non-fatal wp_errors in dfcg-gallery-errors.php
* Bug fix:	Corrected path error for .mo files in load_textdomain() in plugin main file
* Bug fix:	Fixed Settings Page Donate broken link
* Bug fix:	Increased sanitisation cat-display limit to 4 characters
* Bug fix:	Increased sanitisation Carousel text limit to 50 characters
* Bug fix:	Removed unneeded call to dfcg_load_textdomain() in dfcg_add_to_options_menu()
* Bug fix:	Mootools jd.gallery.js - increased thumbIdleOpacity to 0.4 for improved carousel visuals in IE

= 3.1 =
* Released	28 December 2009
* Feature:	Added auto Description using custom $content excerpt + 7 options
* Enhance:	dfcg_baseimgurl() moved to dfcg-gallery-core.php, and added conditional check on loading jq or mootools constructors
* Enhance:	Tidied up Settings text for easier gettext translation
* Enhance:	Tidied up Settings page CSS
* Bug fix:	Fixed "Key Settings" display error when Restrict Scripts is set to Home page only ("home" was used incorrectly instead of "homepage").
* Bug fix:	Fixed whitelist option error for WPMU in dfcg-admin-ui-sanitise.php
* Bug fix:	Category default images folder can now be outside wp-content folder

= 3.0 =
* Released	7 December 2009
* Feature:	Added alternative jQuery gallery script and new associated options
* Feature: 	Added WP version check to Plugins screen. DCG now requires WP 2.8+
* Feature: 	Added contextual help to Settings Page
* Feature:	Added plugin meta links to Plugins main admin page
* Feature: 	Added external link capability using dfcg-link custom field
* Feature:	Added form validation + reminder messages to Settings page
* Feature: 	Added Error messages to help users troubleshoot setup problems
* Feature: 	Re-designed layout of Settings page, added Category selection dropdowns etc
* Feature: 	New Javascript gallery options added to Settings page
* Feature: 	Added "populate-method" Settings. User can now pick between 3: old way (called Multi Option), One category, or Pages.
* Feature: 	Added Settings for limiting loading of scripts into head. New functions to handle this.
* Feature: 	Added Full and Partial URL Settings to simplify location of images and be more suitable for "unusual" WP setups.
* Feature: 	Added Padding Settings for Slide Pane Heading and Description
* Bug fix: 	Complete re-write of code and file organisation for more efficient coding
* Bug fix: 	Changed $options variable name to $dfcg_options to avoid conflicts with other plugins.
* Bug fix:	Improved data sanitisation

= 2.2 =
* Released 5 December 2008
* Feature:	Added template tag function for theme files
* Feature:	Added "disable mootools" checkbox in Settings to avoid js framework	being loaded twice if another plugin uses mootools.
* Feature:	Changed options page CSS to better match with 2.7 look
* Bug fix:	Changed handling of WP constants - now works as intended
* Bug fix:	Removed activation_hook, not needed
* Bug fix:	Fixed loading flicker with CSS change => dynamic-gallery.php
* Bug fix:	Fixed error if selected post doesn't exist => dynamic-gallery.php
* Bug fix:	Fixed XHTML validation error with user-defined styles/CSS moved to head with new file dfcg-user-styles.php for the output of user definable CSS

= 2.1 =
* Released 7 November 2008
* Bug fix: Issue with path to scripts due to WP.org zip file naming convention.

= 2.0 beta =
* Released 5 November 2008			
* Feature: Major code rewrite and reorganisation of functions
* Feature: Added WPMU support
* Feature: Added RESET checkbox to reset options to defaults
* Feature: Added Gallery CSS options in the Settings page

= 1.0.0 =
* Public release 1 September 2008

= 0.9.1 =
* Released 26 August 2008
* Activation and reactivation hooks added to code to setup some default Options on Activation and to remove Options from the WP database on deactivation. 

= 0.9.0 =
* Beta testing release 25 August 2008