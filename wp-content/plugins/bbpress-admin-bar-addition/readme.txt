=== bbPress Admin Bar Addition ===
Contributors: daveshine, deckerweb
Donate link: http://genesisthemes.de/en/donate/
Tags: toolbar, tool bar, adminbar, admin bar, bbpress, bbpress 2.0, administration, resources, links, forum, forums, forum moderator, deckerweb, ddwtoolbar
Requires at least: 3.1
Tested up to: 3.4
Stable tag: 1.6
License: GPLv2 or later
License URI: http://www.opensource.org/licenses/gpl-license.php

This plugin adds useful admin links and resources for the bbPress 2.x Forum Plugin to the WordPress Toolbar / Admin Bar.

== Description ==

= Quick Access to bbPress Forums Resources - Time Saver! =
This **small and lightweight plugin** just adds a lot bbPress 2.x related resources to your toolbar / admin bar. Also links to all setting/ tab pages of the plugin are added making life for forum administrators/ moderators a lot easier. So you might just switch from the fontend of your site to your 'Forums', 'Topics' or 'Main Settings' page or even plugin extensions etc.

= General Features =
* The plugin is **primarily intended towards forum admins and webmasters**.
* Support for all native bbPress 2.x plugin settings plus a huge list of supported plugins & themes out of the box (see below for full listing).
* A massive list of resource & community links is included: support forums, videos/tutorials, code snippets, translations etc.
* A special - fully conditional - "Manage Groups" group where plugins like hook in.
* The added menu items added via my plugin follow the same user cabalities as their original links - in other words: if a link to a settings page is not displayed without my plugin for a certain user role/capability it won't be when my plugin is active!
* 5 action hooks included for hooking custom menu items in -- for all main sections plus the resource group section ([see FAQ section here for more info on that](http://wordpress.org/extend/plugins/bbpress-admin-bar-addition/faq/)).
* 10 additional icon colors included :) (changeable via filters)
* 7 filters included to change wording/tooltip and icon of the main item - for more info [see FAQ section here](http://wordpress.org/extend/plugins/bbpress-admin-bar-addition/faq/)
* For custom "branding" or special needs a few sections like "Extensions" and "Resource links group" could be hidden from displaying via your active theme/child theme - for more info [see FAQ section here](http://wordpress.org/extend/plugins/bbpress-admin-bar-addition/faq/)
* Fully internationalized! Real-life tested and developed with international users in mind!  Also supports update-secure custom language file (if you need special wording...)
* Fully Multisite compatible, you can also network-enable it - full support of Multisite specific stuff!
* Fully WPML compatible!
* Tested with WordPress versions 3.2-branch, 3.3.1, 3.3.2 and 3.4-beta releases - also in debug mode (no stuff there, ok? :)

= Special Features =
* Not only supporting official bbPress 2.x sites, ALSO third-party and user links - so just the whole bbPress 2.x ecosystem :)
* Link to downloadable German language packs - only displayed when German locales are active (de_DE, de_AT, de_CH, de_LU)
* Link to official German bbPress forum - only displayed when German locales are active (de_DE, de_AT, de_CH, de_LU)
* *NOTE:* I would be happy to add more language/locale specific resources and more useful third-party links - just contact me!

As the name suggests this plugin is **intended towards forum admins/ webmasters and moderators**. The new admin bar entries will only be displayed if the current user has the bbPress/ WordPress capability of `moderate`. (Note: I am open for suggestions here if this should maybe changed to a more suitable capability.)

= Plugin Support =
*At this time my plugin out of the box supports also links to settings pages of some bbPress 2.x specific plugins:*

* Plugin: ["GD bbPress Attachments" (free, by Dev4Press)](http://wordpress.org/extend/plugins/gd-bbpress-attachments/)
* Plugin: ["GD bbPress Tools" (free, by Dev4Press)](http://wordpress.org/extend/plugins/gd-bbpress-tools/)
* Plugin: ["GD bbPress Widgets" (free, by Dev4Press)](http://wordpress.org/extend/plugins/gd-bbpress-widgets/)
* Plugin: "GD bbPress Toolbox Pro" (premium, by Dev4Press)
* Plugin: ["bbPress Post Toolbar" (free, by Jason Schwarzenberger)](http://wordpress.org/extend/plugins/bbpress-post-toolbar/)
* Plugin: ["bbPress Antispam" (free, by Daniel Huesken)](http://wordpress.org/extend/plugins/bbpress-antispam/)
* Plugin: ["bbPress reCaptcha" (free, by Pippin Williamson)](http://wordpress.org/extend/plugins/bbpress-recaptcha/)
* Plugin: ["bbPress Moderation" (free, by Ian Haycox)](http://wordpress.org/extend/plugins/bbpressmoderation/)
* Plugin: ["bbPress2 BBCode" (free, by Anton Channing + bOingball + Viper007Bond)](http://wordpress.org/extend/plugins/bbpress-bbcode/)
* Plugin: ["bbPress2 Shortcode Whitelist" (free, by Anton Channing)](http://wordpress.org/extend/plugins/bbpress2-shortcode-whitelist/)
* Plugin: ["bbPress WP Tweaks" (free, by veppa)](http://wordpress.org/extend/plugins/bbpress-wp-tweaks/)
* Plugin: ["Custom Post Type Privacy" (free, by Kev Price)](http://wordpress.org/extend/plugins/custom-post-type-privacy/)
* Plugin: ["bbConverter" (free, by anointed + AWJunkies)](http://wordpress.org/extend/plugins/bbconverter/)
* Plugin: ["WangGuard" (free, by WangGuard Team)](http://wordpress.org/extend/plugins/wangguard/)
* Plugin: ["Members" (free, by Justin Tadlock)](http://wordpress.org/extend/plugins/members/)
* Plugin: ["WP SyntaxHighlighter" (free, by redcocker)](http://wordpress.org/extend/plugins/wp-syntaxhighlighter/)
* Plugin: ["s2Member" (free version, by WebSharks, Inc.)](http://wordpress.org/extend/plugins/s2member/)
* *Your free or premium bbPress specific plugin? - [Just contact me with specific data](http://genesisthemes.de/en/contact/)*

= Theme/Framework Support =
*At this time my plugin out of the box supports also links to settings pages of some bbPress specific/supporting themes or frameworks:*

* Theme Framework with child themes: "Genesis Framework" (premium, by StudioPress) via "bbPress Genesis Extend" plugin (free, by Jared Atchison)
* Theme: "Skeleton" (free, by Simple Themes)
* Theme: ["Elbee Elgee" (free, by Doug Stewart)](http://wordpress.org/extend/themes/elbee-elgee)
* Themes: "Fanwood" (free, by DevPress)
* Theme Framework with child themes: "Infinity (Anti-) Framework" via "BP Template Pack" plugin (free/beta, by PressCrew)
* Themes: "Gratitude" and "Buddies" (both premium, by Chris Paul/ZenThemes)
* Theme: "WP Sharp" (premium, by PrimaThemes at ThemeForest)
* *Your free or premium bbPress specific theme/framework? - [Just contact me with specific data](http://genesisthemes.de/en/contact/)*

= Localization =
* English (default) - always included
* German - always included
* .pot file (`bbpaba.pot`) for translators is also always included :)
* Easy plugin translation platform with GlotPress tool: [Translate "bbPress Admin Bar Addition"...](http://translate.wpautobahn.com/projects/wordpress-plugins-deckerweb/bbpress-admin-bar-addition)
* *Your translation? - [Just send it in](http://genesisthemes.de/en/contact/)*

Credit where credit is due: This plugin here is inspired and based on the work of Remkus de Vries @defries and his awesome "WooThemes Admin Bar Addition" plugin.

[A plugin from deckerweb.de and GenesisThemes](http://genesisthemes.de/en/)

= Feedback =
* I am open for your suggestions and feedback - Thank you for using or trying out one of my plugins!
* Drop me a line [@deckerweb](http://twitter.com/#!/deckerweb) on Twitter
* Follow me on [my Facebook page](http://www.facebook.com/deckerweb.service)
* Or follow me on [+David Decker](http://deckerweb.de/gplus) on Google Plus ;-)

= More =
* [Also see my other plugins](http://genesisthemes.de/en/wp-plugins/) or see [my WordPress.org profile page](http://profiles.wordpress.org/daveshine/)
* Tip: [*GenesisFinder* - Find then create. Your Genesis Framework Search Engine.](http://genesisfinder.com/)

== Installation ==

1. Upload the entire `bbpress-admin-bar-addition` folder to the `/wp-content/plugins/` directory -- or just upload the ZIP package via 'Plugins > Add New > Upload' in your WP Admin
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Look at your toolbar / admin bar and enjoy using the new links there :)
4. Go and manage your forum :)

**Multisite install:** Yes, it's fully compatible but have a look in the [FAQ section here](http://wordpress.org/extend/plugins/bbpress-admin-bar-addition/faq/) for more info :)

**Own translation/wording:** For custom and update-secure language files please upload them to `/wp-content/languages/bbpress-admin-bar-addition/` (just create this folder) - This enables you to use fully custom translations that won't be overridden on plugin updates. Also, complete custom English wording is possible with that, just use a language file like `bbpaba-en_US.mo/.po` to achieve that (for creating one see the tools on "Other Notes").

== Frequently Asked Questions ==

= Does this plugin work with latest WP version and also older versions? =
Yes, this plugin works really fine with WordPress 3.3+!
It also works great with WP 3.2 branch - and also should with WP 3.1 branch - but we only tested extensively with WP 3.3+ and 3.2 branch. So you always should run the latest WordPress version for a lot of reasons.

= How are new resources being added to the admin bar? =
Just drop me a note on [my Twitter @deckerweb](http://twitter.com/deckerweb) or via my contact page and I'll add the link if it is useful for admins/ webmasters and the bbPress community.

= How could my plugin/extension or theme options page be added to the admin bar links? =
This is possible of course and highly welcomed! Just drop me a note on [my Twitter @deckerweb](http://twitter.com/deckerweb) or via my contact page and we sort out the details!
Particularly, I need the admin url for the primary options page (like so `wp-admin/admin.php?page=foo`) - this is relevant for both, plugins and themes. For themes then I also need the correct name defined in the stylesheet (like so `Footheme`) and the correct folder name (like so `footheme-folder`) because this would be the template name when using with child themes. (I don't own all the premium stuff myself yet so you're more than welcomed to help me out with these things. Thank you!)

= There are still some other plugins for bbPress 2.x out there why aren't these included by default? =
Simple answer: The settings of these add-ons are added directly to the bbPress main settings page and have no anchor to link to. So linking/ adding is just not possible.

= Is this plugin Multisite compatible? =
Yes, it is! :) Works really fine in Multisite invironment - here I just recommend to activate on a per site basis so to load things only where and when needed.

= In Multisite, could I "network enable" this plugin? =
Yes, you could. -- However, it doesn't make much sense. The plugin is intented for a per site use as the admin links refer to the special settings, plugin-support and theme-support for that certain site/blog. So if you have a Multisite install with 5 sites but only 3 would run "bbPress 2.x" the the other 2 sites will only see support links in the Toolbar / Admin Bar... I guess, you got it? :)

Though intended for a per site use it could make some sense in such an edge case: if all of the sites in Multisite use bbPress 2.x and have lots of bbPress-specific plugins in common and use the same theme/framework. This might be the case if you use Multisite for multilingual projects, especially via that awesome plugin: http://wordpress.org/extend/plugins/multilingual-press/

= Can custom menu items be hooked in via theme or other plugins? =
Yes, this is possible since version 1.5 of the plugin! There are 5 action hooks available for hooking custom menu items in -- `bbpaba_custom_main_items` for the main section, `bbpaba_custom_forum_items` for the Forums sub-level section (frontend links), `bbpaba_custom_extension_items` for the exentensions section, `bbpaba_custom_theme_items` for the theme section plus `bbpaba_custom_group_items` for the resource group section. Here's an example code:
`
add_action( 'bbpaba_custom_group_items', 'bbpaba_custom_additional_group_item' );
/**
 * bbPress Admin Bar Addition: Custom Resource Group Items
 *
 * @global mixed $wp_admin_bar
 */
function bbpaba_custom_additional_group_item() {

	global $wp_admin_bar;

	$wp_admin_bar->add_menu( array(
		'parent' => 'ddw-bbpress-bbpgroup',
		'id'     => 'your-unique-item-id',
		'title'  => __( 'Custom Menu Item Name', 'your-textdomain' ),
		'href'   => 'http://deckerweb.de/',
		'meta'   => array( 'title' => __( 'Custom Menu Item Name Tooltip', 'your-textdomain' ) )
	) );
}
`

= Can certain sections be removed? =
Yes, this is possible! You can remove the following sections: "Extensions" area (all items) / "Theme" (all items!) / "Resources link group" at the bottom (all items) / "German language stuff" (all items)

To achieve this add one, some or all of the following constants to your theme's/child theme's `functions.php` file:
`
/** bbPress Admin Bar Addition: Remove Extensions Items */
define( 'BBPABA_EXTENSIONS_DISPLAY', FALSE );

/** bbPress Admin Bar Addition: Remove Theme Items */
define( 'BBPABA_THEME_DISPLAY', FALSE );

/** bbPress Admin Bar Addition: Remove Resource Items */
define( 'BBPABA_RESOURCES_DISPLAY', FALSE );

/** bbPress Admin Bar Addition: Remove German Language Items */
define( 'BBPABA_DE_DISPLAY', FALSE );
`

= Can the the whole toolbar entry be removed, especially for certain users? =
Yes, that's also possible! This could be useful if your site has special user roles/capabilities or other settings that are beyond the default WordPress stuff etc. For example: if you want to disable the display of any "bbPress Admin Bar Addition" items for all user roles of "Editor" please use this code:
`
/** bbPress Admin Bar Addition: Remove all items for "Editor" user role */
if ( current_user_can( 'editor' ) ) {
	define( 'BBPABA_DISPLAY', FALSE );
}
`

To hide only from the user with a user ID of "2":
`
/** bbPress Admin Bar Addition: Remove all items for user ID 2 */
if ( 2 == get_current_user_id() ) {
	define( 'BBPABA_DISPLAY', FALSE );
}
`

To hide items only in frontend use this code:
`
/** bbPress Admin Bar Addition: Remove all items from frontend */
if ( ! is_admin() ) {
	define( 'BBPABA_DISPLAY', FALSE );
}
`

In general, use this constant do hide any "bbPress Admin Bar Addition" items:
`
/** bbPress Admin Bar Addition: Remove all items */
define( 'BBPABA_DISPLAY', FALSE );
`

= Can I remove the original Toolbar items for "GD bbPress Tools" or "GB bbPress Toolbox (Pro)"? =
Yes, this is also possible! Since v1.5+ of my plugin support for *GD bbPress Tools" and "GD bbPress Toolbox (Pro)" (both by Milan Petrovic of Dev4Press)* is included so if you only want his stuff to appear within "bbPress Admin Bar Addition" just add this constant to your active theme's/child theme's `functions.php file` or functionality plugin:
`
/** bbPress Admin Bar Addition: Remove original GD bbPress Tools items */
define( 'BBPABA_REMOVE_GDBBPRESSTOOLS_TOOLBAR', true );
`


= Available Filters to Customize More Stuff =
All filters are listed with the filter name in bold and the below additional info, helper functions (if available) as well as usage examples.

**bbpaba_filter_capability_all**

* Default value: `moderate` (bbPress admin stuff should only be done by at least "Moderators", right?!)
* 7 Predefined helper functions:
 * `__bbpaba_admin_only` -- returns `'administrator'` role -- usage:
`
add_filter( 'bbpaba_filter_capability_all', '__bbpaba_admin_only' );
`
 * `__bbpaba_role_editor` -- returns `'editor'` role -- usage:
`
add_filter( 'bbpaba_filter_capability_all', '__bbpaba_role_editor' );
`
 * `__bbpaba_role_bbp_moderator` -- returns `'bbp_moderator'` role -- usage:
`
add_filter( 'bbpaba_filter_capability_all', '__bbpaba_role_bbp_moderator' );
`
 * `__bbpaba_cap_moderate` -- returns `'moderate'` capability -- usage:
`
add_filter( 'bbpaba_filter_capability_all', '__bbpaba_cap_moderate' );
`
 * `__bbpaba_cap_manage_options` -- returns `'manage_options'` capability -- usage:
`
add_filter( 'bbpaba_filter_capability_all', '__bbpaba_cap_manage_options' );
`
 * `__bbpaba_cap_install_plugins` -- returns `'install_plugins'` capability -- usage:
`
add_filter( 'bbpaba_filter_capability_all', '__bbpaba_cap_install_plugins' );
`
 * `__bbpaba_cap_edit_theme_options` -- returns `'edit_theme_options'` capability -- usage:
`
add_filter( 'bbpaba_filter_capability_all', '__bbpaba_cap_edit_theme_options' );
`
* Another example:
`
add_filter( 'bbpaba_filter_capability_all', 'custom_bbpaba_capability_all' );
/**
 * bbPress Admin Bar Addition: Change Main Capability
 */
function custom_bbpaba_capability_all() {
	return 'activate_plugins';
}
`
--> Changes the capability to `activate_plugins`

**bbpaba_filter_main_icon**

* Default value: bbPress bee logo :)
* 10 Predefined helper functions for the 10 included colored icons, returning special colored icon values - the helper function always has this name: `__bbpaba_colornamehere_icon()` this results in the following filters ready for usage:
`
add_filter( 'bbpaba_filter_main_icon', '__bbpaba_blue_icon' );

add_filter( 'bbpaba_filter_main_icon', '__bbpaba_brown_icon' );

add_filter( 'bbpaba_filter_main_icon', '__bbpaba_gray_icon' );

add_filter( 'bbpaba_filter_main_icon', '__bbpaba_green_icon' );

add_filter( 'bbpaba_filter_main_icon', '__bbpaba_khaki_icon' );

add_filter( 'bbpaba_filter_main_icon', '__bbpaba_orange_icon' );

add_filter( 'bbpaba_filter_main_icon', '__bbpaba_pink_icon' );

add_filter( 'bbpaba_filter_main_icon', '__bbpaba_red_icon' );

add_filter( 'bbpaba_filter_main_icon', '__bbpaba_turquoise_icon' );

add_filter( 'bbpaba_filter_main_icon', '__bbpaba_alternate_icon' );

add_filter( 'bbpaba_filter_main_icon', '__bbpaba_theme_images_icon' );
`
--> Where the 2nd to last "alternate" icon returns the same icon as in the left-hand "Forums" menu!

--> Where the last helper function returns the icon file (`icon-bbpaba.png`) found in your current theme's/child theme's `/images/` subfolder

* Example for using with current child theme:
`
add_filter( 'bbpaba_filter_main_icon', 'custom_bbpaba_main_icon' );
/**
 * bbPress Admin Bar Addition: Change Main Icon
 */
function custom_bbpaba_main_icon() {
	return get_stylesheet_directory_uri() . '/images/custom-icon.png';
}
`
--> Uses a custom image from your active child theme's `/images/` folder

--> Recommended dimensions are 16px x 16px

**bbpaba_filter_main_icon_display**

* Returning the CSS class for the main item icon
* Default value: `icon-bbpress` (class is: `.icon-bbpress`)
* 1 Predefined helper function:
 * `__bbpaba_no_icon_display()` -- usage:
`
add_filter( 'bbpaba_filter_main_icon_display', '__bbpaba_no_icon_display' );
`
--> This way you can REMOVE the icon!

 * Another example:
`
add_filter( 'bbpaba_filter_main_icon_display', 'custom_bbpaba_main_icon_display_class' );
/**
 * bbPress Admin Bar Addition: Change Main Icon CSS Class
 */
function custom_bbpaba_main_icon_display_class() {
	return 'your-custom-icon-class';
}
`
--> You then have to define CSS rules in your theme/child theme stylesheet for your own custom class `.your-custom-icon-class`

--> Recommended dimensions are 16px x 16px

**bbpaba_filter_main_item_title**

* Default value: "bbPress"
* Example code for your theme's/child theme's `functions.php` file:
`
add_filter( 'bbpaba_filter_main_item_title', 'custom_bbpaba_main_item_title' );
/**
 * bbPress Admin Bar Addition: Change Main Item Name
 */
function custom_bbpaba_main_item_title() {
	return __( 'Your custom main item title', 'your-textdomain' );
}
`

**bbpaba_filter_main_item_title_tooltip**

* Default value: "bbPress Forums"
* Example code for your theme's/child theme's `functions.php` file:
`
add_filter( 'bbpaba_filter_main_item_title_tooltip', 'custom_bbpaba_main_item_title_tooltip' );
/**
 * bbPress Admin Bar Addition: Change Main Item Name's Tooltip
 */
function custom_bbpaba_main_item_title_tooltip() {
	return __( 'Your custom main item title tooltip', 'your-textdomain' );
}
`

**bbpaba_filter_bbpress_name** and **bbpaba_filter_bbpress_name_tooltip**

* Default value for both: "bbPress"
* Used for some items within toolbar links to enable proper branding
* Change things like in the other examples/principles shown above

**Final note:** If you don't like to add your customizations to your theme's/child theme's `functions.php` file you can also add them to a functionality plugin or an mu-plugin. This way you can also use this better for Multisite environments. In general you are then more independent from child theme changes etc.

All the custom & branding stuff code above can also be found as a Gist on Github: https://gist.github.com/2721186 (you can also add your questions/ feedback there :)

== Screenshots ==

1. bbPress Admin Bar Addition in action - primary level - default state (running with bbPress 2.1-bleeding and WordPress 3.3+ here)
2. bbPress Admin Bar Addition in action - second level - main settings
3. bbPress Admin Bar Addition in action - third level - forums - view frontend forums plus edit/add forums
4. bbPress Admin Bar Addition in action - second level - users (also with some extensions hooking in)
5. bbPress Admin Bar Addition in action - third level - extensions support
6. bbPress Admin Bar Addition in action - second level - resources: documentation stuff
7. bbPress Admin Bar Addition in action - second level - resources: bbPress HQ stuff
8. bbPress Admin Bar Addition in action - language specific links at the bottom - for example: German locale

== Changelog ==

= 1.6 (2012-06-14) =
* BUGFIX: Had to remove the links to all existing Forums (frontend views) because it was causing major errors with other custom post types. (Maybe it will be re-added later...).
* NEW: Added plugin support for "GD bbPress Toolbox Pro" (premium, by Dev4Press)
* UPDATE: Updated German translations and also the .pot file for all translators!

= 1.5 (2012-05-18) =
* Woot, major updates on all fronts! :)
* *New features:*
 * COOL: Plugin can now be branded and customized a lot more!
 * NEW: Added 5 action hooks for hooking custom menu items in - see [FAQ section here](http://wordpress.org/extend/plugins/bbpress-admin-bar-addition/faq/) for more info on that.
 * NEW: Added 7 filters to change icon graphic, main item name, main capability and more! For these cases there are now the new built-in filters and helper functions available! [(See "FAQ" section here)](http://wordpress.org/extend/plugins/bbpress-admin-bar-addition/faq/)
 * NEW: Almost all sections can now be removed for special needs, capabilities etc. -- all done via `constants` in your active theme/child theme -- this way you can customize for your staff members if you need some more users with extended or restricted admin bar/toolbar access (See "FAQ" section here)
* *Extended theme/framework support:*
 * NEW: Added theme support section to display links to settings pages of some bbPress 2.x specific themes.
 * NEW: Added support for: Genesis Framework plus Child Themes / Skeleton / Elbee Elgee / Fanwood / Infinity (Anti-) Framework plus Child Themes / Gratitude / Buddies / WP Sharp
* *Extended plugin support:*
 * NEW: Added plugin support for "GD bbPress Tools" (free, by Dev4Press)
 * NEW: Added plugin support for "GD bbPress Widgets" (free, by Dev4Press)
 * NEW: Added plugin support for "s2Member" (free version, by WebSharks, Inc.)
 * UPDATE: Updated and improved plugin support for "GD bbPress Attachments"
 * FIX: Fixed settings link for "bbPress Moderation" plugin.
* *Other stuff and maintenance:*
 * NEW: Added new settings links "Update Forum" and "Converter" in favor of upcoming bbPress 2.1 :)
 * NEW: Added new documentation video resource link.
 * NEW: Added *bbPress Plugin News Planet* feed link to resource links (you can also access this from here: http://friendfeed.com/bbpress-news)
 * UPDATE: Revised some wording/strings and optimized language for better display.
 * CODE: No longer loading css styles or menu items for not logged-in users when plugins like "GD Press Tools" are active (which have options to show toolbar / admin bar also for visitors...)
 * NEW: Added possibility for custom and update-secure language files for this plugin - just upload them to `/wp-content/languages/bbpaba/` (just create this folder) - this enables you to use fully custom wording or translations
 * UPDATE: Moved all admin-only functions/code from main file to extra admin file which only loads within 'wp-admin', this way it's all  performance-improved! :)
 * UPDATE: Updated all existing screenshots and added some new ones.
 * NEW: Added info for Multisite installs to "Installation" and "FAQ" section of readme.txt file
 * UPDATE: Updated readme.txt file with new and improved documentation, added more plugin links.
 * UPDATE: Updated German translations and also the .pot file for all translators!
 * UPDATE: Extended GPL License info in readme.txt as well as main plugin file.
 * NEW: Easy plugin translation platform with GlotPress tool: [Translate "bbPress Admin Bar Addition"...](http://translate.wpautobahn.com/projects/wordpress-plugins-deckerweb/bbpress-admin-bar-addition)

= 1.4 (2012-02-07) =
* Maintenance release: For WordPress 3.3+ changed display of resource links group: now at the bottom, below settings links and in WP 3.3 group style :)
* Maintenance release: Enhanced backwards compatibility with WP 3.2/3.1 branches
* Maintenance release: General CSS and code optimizations up to WP 3.3+
* NEW: Added a new "Users" section below "Replies" because users seem to play an important role in a forum, right? :) (Note, only displayed if currend user as the appropiate role/cap)
* UPDATE: Updated German community support forum link
* NEW: Added plugin support for "bbPress WP Tweaks" (free, by veppa)
* NEW: Added plugin support for "Custom Post Type Privacy" (free, by Kev Price)
* NEW: Added plugin support for "WangGuard" (an anti-splog plugin - free, by WangGuard Team)
* NEW: Added plugin support for "Members" (free, by Justin Tadlock)
* CODE: Splitted code into more files for better maintenance - plugin support now has its own file
* CODE: Improvement - hooked loading call for textdomain into init hook for fullfilling standard
* CODE: More minor code/ code documenation tweaks and improvements
* UPDATE: Updated existing and also added a few new screenshots
* UPDATE: Updated German translations and also the .pot file for all translators!

= 1.3 (2012-01-09) =
* Added plugin support for "bbPress Antispam" (free, by Daniel Huesken)
* Added plugin support for "bbPress Moderation" (free, by Ian Haycox)
* Added plugin support for "WP SyntaxHighlighter" (free, by redcocker)
* Minor code/ code documenation tweaks
* Updated readme.txt file - added new "Toolbar" wording introduced with WordPress 3.3 (formerly known as the Admin Bar)
* Updated German translations and also the .pot file for all translators!
* Added banner image on WordPress.org for better plugin branding :)

= 1.2 (2011-12-04) =
* Added plugin support for "bbConverter" (free, by anointed + AWJunkies)
* Added new external resource - "Hooks, Filters and Components for bbPress 2.0" at etivite.com
* Added new external resource - "Getting Started with bbPress" by Smashing Magazine
* Fixed display of first-level icon on mouse-hover with WordPress 3.3 - props to [Dominik Schilling](http://wpgrafie.de/) [@ocean90](http://twitter.com/#!/ocean90) for great help with the CSS!
* Updated the screenshots with fixed first-level icon
* Updated and improved readme.txt file
* Updated German translations and also the .pot file for all translators!
* Now I'd call this some fully optimized release - enjoy :-)

= 1.1 (2011-12-03) =
* Added link to topic tag "bbpress-plugin" in the official bbPress Forum
* Corrected a wrong link (free WP.org forum)
* Minor code tweaks
* Fixed some ugly typos (Mmh, happens sometimes...)
* Updated German translations and also the .pot file for all translators!

= 1.0 (2011-12-03) =
* Initial release

== Upgrade Notice ==

= 1.6 =
Maintenance release: Bugfix causing CPT conflicts. Extended plugin support. Also, updated .pot file for translators and German translations.

= 1.5 =
Major additions & improvements: Full cabability support for all links! Extended plugin & theme support. Added customization abilities. Code & documentation improvements. Also, updated .pot file for translators and German translations.

= 1.4 =
Major changes and improvements - Improved styling and support for WP 3.1 - 3.3+. Added new "Users" section plus plugin support for 4 more third-party plugins. Further code/ documentation tweaks and updated .pot file for translators together with German translations.

= 1.3 =
Added plugin support for 3 more third-party plugins. A few minor code/ documentation tweaks, updated readme.txt file and also updated .pot file for translators together with German translations.

= 1.2 =
Added plugin support for bbConverter as well as 2 new resources. Fixed first-level icon display in WP 3.3. Updated readme.txt file, screenshots and also .pot file for translators together with German translations.

= 1.1 =
Added link to topic tag "bbpress-plugin" in official forum. Corrected a wrong link and added minor code tweaks, also fixed some ugly typos. Updated .pot file for translators and German translations.

= 1.0 =
Just released into the wild.

== Plugin Links ==
* [Translations (GlotPress)](http://translate.wpautobahn.com/projects/wordpress-plugins-deckerweb/bbpress-admin-bar-addition)
* [User support forums](http://wordpress.org/support/plugin/bbpress-admin-bar-addition)
* [Code snippets archive for customizing, GitHub Gist](https://gist.github.com/2721186)
* *Plugin tip:* [My bbPress Search Widget plugin](http://wordpress.org/extend/plugins/bbpress-search-widget/) -- search functionality for bbPress 2.x, independent from regular WordPress search :)

== Donate ==
Enjoy using *bbPress Admin Bar Addition*? Please consider [making a small donation](http://genesisthemes.de/en/donate/) to support the project's continued development.

== Translations ==

* English - default, always included
* German: Deutsch - immer dabei! [Download auch via deckerweb.de](http://deckerweb.de/material/sprachdateien/bbpress-forum/#bbpress-admin-bar-addition)
* For custom and update-secure language files please upload them to `/wp-content/languages/bbpress-admin-bar-addition/` (just create this folder) - This enables you to use fully custom translations that won't be overridden on plugin updates. Also, complete custom English wording is possible with that, just use a language file like `bbpaba-en_US.mo/.po` to achieve that (for creating one see the following tools).

**Easy plugin translation platform with GlotPress tool: [Translate "bbPress Admin Bar Addition"...](http://translate.wpautobahn.com/projects/wordpress-plugins-deckerweb/bbpress-admin-bar-addition)**

*Note:* All my plugins are internationalized/ translateable by default. This is very important for all users worldwide. So please contribute your language to the plugin to make it even more useful. For translating I recommend the awesome ["Codestyling Localization" plugin](http://wordpress.org/extend/plugins/codestyling-localization/) and for validating the ["Poedit Editor"](http://www.poedit.net/), which works fine on Windows, Mac and Linux.

== Additional Info ==
**Idea Behind / Philosophy:** Just a little leightweight plugin for all the bbPress Forum managers out there to make their daily forum admin life a bit easier. I'll try to add more plugin/theme support if it makes some sense. So stay tuned :).

== Credits ==
* Thanx to [Dominik Schilling](http://wpgrafie.de/) [@ocean90](http://twitter.com/#!/ocean90) for great help with the CSS for the first level icon in WordPress 3.3!
