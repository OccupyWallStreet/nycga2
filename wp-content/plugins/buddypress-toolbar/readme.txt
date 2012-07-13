=== BuddyPress Toolbar ===
Contributors: daveshine, deckerweb
Donate link: http://genesisthemes.de/en/donate/
Tags: toolbar, adminbar, admin bar, buddypress, administration, resources, links, community, community manager, social, deckerweb, ddwtoolbar
Requires at least: 3.3 and BuddyPress 1.5+
Tested up to: 3.4
Stable tag: 1.3
License: GPLv2 or later
License URI: http://www.opensource.org/licenses/gpl-license.php

This plugin adds useful admin links and resources for BuddyPress 1.5+/1.6+ to the WordPress Toolbar / Admin Bar.

== Description ==

= Quick Access to BuddyPress Resources - Time Saver! =
This **small and lightweight plugin** just adds a lot BuddyPress related resources to your toolbar / admin bar. Also links to all setting/ tab pages of the plugin are added making life for community managers/ administrators a lot easier. So you might just switch from the fontend of your site to 'Activity Moderating' (BP 1.6) or the 'Main Settings' page etc.

= General Features =
* The plugin is **primarily intended towards (multisite) admins and webmasters**.
* Support for all native BuddyPress settings plus a huge list of supported plugins & themes out of the box (see below for full listing).
* Fully Multisite compatible, you can also network-enable it - full support of Multisite specific stuff!
* A massive list of resource & community links is included: support forums, code snippets, translations etc.
* A special - fully conditional - "Manage Groups" group where plugins like hook in.
* The added menu items by the plugin follow the same user cabalities as their original links - in other words: if a link to a settings page is not displayed without my plugin for a certain user role/capability it won't be when my plugin is active!
* 4 action hooks included for hooking custom menu items in -- for all main sections plus the resource group section ([see FAQ section here for more info on that](http://wordpress.org/extend/plugins/buddypress-toolbar/faq/)).
* 11 additional icon colors included :) (changeable via filters)
* 7 filters included to change wording/tooltip and icon of the main item - for more info [see FAQ section here](http://wordpress.org/extend/plugins/buddypress-toolbar/faq/)
* For custom "branding" or special needs a few sections like "Extensions" and "Resource links group" could be hidden from displaying via your active theme/child theme - for more info [see FAQ section here](http://wordpress.org/extend/plugins/buddypress-toolbar/faq/)
* Fully internationalized! Real-life tested and developed with international users in mind!  Also supports update-secure custom language file (if you need special wording...)
* Fully WPML compatible!
* Tested with WordPress versions 3.3.1, 3.3.2 and 3.4-beta releases - also in debug mode (no stuff there, ok? :)

The plugin is **fully compatible** with the latest WordPress version (requires WP 3.3+) and therefore **with BuddyPress 1.5 branch AND the upcoming 1.6 branch** (currently in alpha status). The plugin dynamically supports the different admin settings links in BOTH BuddyPress versions! -- So, just go and manage your community. **Use this time saver! :-)**

As the name suggests this plugin is **intended towards community managers/ webmasters and administrators**. The new toolbar / admin bar items will only be displayed if the current user has the WordPress role/cabability of `administrator`. (Note: This can be customized via helper functions/constants, see FAQ here.)

= Plugin Support =
*At this time my plugin out of the box supports also links to settings pages of some BuddyPress specific plugins:*

* Plugin: ["BuddyPress ScholarPress Courseware" (free, by ScholarPress Dev Crew)](http://wordpress.org/extend/plugins/buddypress-courseware/)
* Plugin: ["BP Group Organizer" (free, by David Dean)](http://wordpress.org/extend/plugins/bp-group-organizer/)
* Plugin: ["BP Group Hierarchy" (free, by David Dean)](http://wordpress.org/extend/plugins/bp-group-hierarchy/)
* Plugin: ["BuddyPress Group Extras" (free, by slaFFik)](http://wordpress.org/extend/plugins/buddypress-groups-extras/)
* Plugin: ["BuddyPress Group Default Avatar" (free, by Vernon Fowler)](http://wordpress.org/extend/plugins/buddypress-default-group-avatar/)
* Plugin: ["BuddyPress Group Email Subscription" (free, by Deryk Wenaus + Boone Gorges)](http://wordpress.org/extend/plugins/buddypress-group-email-subscription/)
* Plugin: ["BP GTM System" (free, by slaFFik + valant)](http://wordpress.org/extend/plugins/bp-gtm-system/)
* Plugin: ["CD BuddyPress Avatar Bubble" (free, by slaFFik + valant)](http://wordpress.org/extend/plugins/cd-bp-avatar-bubble/)
* Plugin: ["Buddypress User Account Type Lite" (free, by Rimon Habib)](http://wordpress.org/extend/plugins/buddypress-user-account-type-lite/)
* Plugin: "BP Profiles Statistics" (premium, by slaFFik)
* Plugin: ["BP Profile Search" (free, by Andrea Tarantini)](http://wordpress.org/extend/plugins/bp-profile-search/)
* Plugin: ["BuddyPress Portfolio" (free, by Nicolas Crocfer)](http://wordpress.org/extend/plugins/buddypress-portfolio/)
* Plugin: ["BuddyPress Login Redirect" (free, by Jatinder Pal Singh)](http://wordpress.org/extend/plugins/buddypress-login-redirect/)
* Plugin: ["BP Profile as Homepage" (free, by Jatinder Pal Singh)](http://wordpress.org/extend/plugins/bp-profile-as-homepage/)
* Plugin: ["sxss Buddypress Shared Friends" (free, by sxss)](http://wordpress.org/extend/plugins/buddypress-shared-friends/)
* Plugin: ["Breadcrumbs Everywhere" (free, by Betsy Kimak)](http://wordpress.org/extend/plugins/breadcrumbs-everywhere/)
* Plugin: ["BuddyStream" (free, by Peter Hofman)](http://wordpress.org/extend/plugins/buddystream/)
* Plugin: ["BuddyPress MyMood" (free, by Ayush)](http://wordpress.org/extend/plugins/buddypress-mymood/)
* Plugin: ["BuddyPress Achievements" (free, by Paul Gibbs)](http://wordpress.org/extend/plugins/achievements/)
* Plugin: ["BuddyPress Twitter" (free, by Charl Kruger)](http://wordpress.org/extend/plugins/buddypress-twitter/)
* Plugin: ["BP Code Snippets" (free, by imath)](http://wordpress.org/extend/plugins/bp-code-snippets/)
* Plugin: ["Invite Anyone" (free, by Boone Gorges)](http://wordpress.org/extend/plugins/invite-anyone/)
* Plugin: ["Events Manager" (free, by Marcus Sykes)](http://wordpress.org/extend/plugins/events-manager/)
* Plugin: ["WangGuard" (free, by WangGuard Team)](http://wordpress.org/extend/plugins/wangguard/)
* Plugin: ["Members" (free, by Justin Tadlock)](http://wordpress.org/extend/plugins/members/)
* *Your free or premium BuddyPress specific plugin? - [Just contact me with specific data](http://genesisthemes.de/en/contact/)*

= Theme/Framework Support =
*At this time my plugin out of the box supports also links to settings pages of some BuddyPress specific/supporting themes or frameworks:*

* Theme Framework with child themes: "Genesis Framework" via "Genesis Connect" plugin (both premium, by StudioPress)
* Theme: ["Custom Community" (free, by Themekraft)](http://wordpress.org/extend/themes/custom-community)
* Theme: ["Frisco for BuddyPress" (free, by David Carson)](http://wordpress.org/extend/themes/frisco-for-buddypress)
* Theme: ["Elbee Elgee" (free, by Doug Stewart)](http://wordpress.org/extend/themes/elbee-elgee)
* Themes: "Visual" (premium) and "Fanwood" (free) (both by DevPress)
* Theme: "Builder Framework" via "Builder BuddyPress" plugin (premium, by iThemes)
* Theme Framework with child themes: "Infinity (Anti-) Framework" via "BP Template Pack" plugin (free/beta, by PressCrew)
* Themes: "Gratitude" and "Buddies" (both premium, by Chris Paul/ZenThemes)
* Themes: "Business Services" and "BuddyPress Corporate" (both free, by WPMU DEV)
* Themes: "3colours", "Bruce", "Detox", "Ines", "Ice", "Niukita", "Soho", "Solitude", "Speed", "Spyker" - all from 3oneseven.com (all 10 free, by milo317)
* *Your free or premium BuddyPress specific theme/framework? - [Just contact me with specific data](http://genesisthemes.de/en/contact/)*

= Special Features =
* Not only supporting official BuddyPress sites ALSO third-party and user links - so just the whole BuddyPress ecosystem :)
* Link to downloadable German language packs - only displayed when German locales are active (de_DE, de_AT, de_CH, de_LU)
* Link to official German BuddyPress forum - only displayed when German locales are active (de_DE, de_AT, de_CH, de_LU)
* *NOTE:* I would be happy to add more language/locale specific resources and more useful third-party links - just contact me!

= Localization =
* English (default) - always included
* German - always included
* .pot file (`buddypress-toolbar.pot`) for translators is also always included :)
* Easy plugin translation platform with GlotPress tool: [Translate "BuddyPress Toolbar"...](http://translate.wpautobahn.com/projects/wordpress-plugins-deckerweb/buddypress-toolbar)
* *Your translation? - [Just send it in](http://genesisthemes.de/en/contact/)*

Credit where credit is due: This plugin here is inspired and based on the work of Remkus de Vries @defries and his original "WooThemes Admin Bar Addition" plugin.

[A plugin from deckerweb.de and GenesisThemes](http://genesisthemes.de/en/)

= Feedback =
* I am open for your suggestions and feedback - Thank you for using or trying out one of my plugins!
* Drop me a line [@deckerweb](http://twitter.com/#!/deckerweb) on Twitter
* Follow me on [my Facebook page](http://www.facebook.com/deckerweb.service)
* Or follow me on [+David Decker](http://deckerweb.de/gplus) on Google Plus ;-)

= Tips & More =
* Check out more great helper plugins for admins in [my WordPress Toolbar / Admin Bar plugin series...](http://wordpress.org/extend/plugins/tags/ddwtoolbar)
* [Also see my other plugins](http://genesisthemes.de/en/wp-plugins/) or see [my WordPress.org profile page](http://profiles.wordpress.org/daveshine/)
* Tip: [*GenesisFinder* - Find then create. Your Genesis Framework Search Engine.](http://genesisfinder.com/)

== Installation ==

1. Upload the entire `buddypress-toolbar` folder to the `/wp-content/plugins/` directory -- or just upload the ZIP package via 'Plugins > Add New > Upload' in your WP Admin
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Look at your toolbar / admin bar and enjoy using the new links there :)
4. Go and manage your community :)

For custom and update-secure language files please upload them to `/wp-content/languages/buddypress-toolbar/` (just create this folder) - This enables you to use fully custom translations that won't be overridden on plugin updates. Also, complete custom English wording is possible with that, just use a language file like `buddypress-toolbar-en_US.mo/.po` to achieve that (for creating one see the tools on "Other Notes").

== Frequently Asked Questions ==

= Does this plugin work with the latest WP version and also older versions? =
Indeed, it does! And it does only run with WordPress 3.3 or higher! Therefore, you can only run it with BuddyPress 1.5+ or upcoming BP 1.6 (currently in alpha status).

= How are new resources being added to the admin bar? =
Just drop me a note on [my Twitter @deckerweb](http://twitter.com/deckerweb) or via my contact page and I'll add the link if it is useful for admins/ webmasters and the BuddyPress community.

= How could my plugin/extension or theme options page be added to the admin bar links? =
This is possible of course and highly welcomed! Just drop me a note on [my Twitter @deckerweb](http://twitter.com/deckerweb) or via my contact page and we sort out the details!
Particularly, I need the admin url for the primary options page (like so `wp-admin/admin.php?page=foo`) - this is relevant for both, plugins and themes. For themes then I also need the correct name defined in the stylesheet (like so `Footheme`) and the correct folder name (like so `footheme-folder`) because this would be the template name when using with child themes. (I don't own all the premium stuff myself yet so you're more than welcomed to help me out with these things. Thank you!)

= There are still some other plugins for BuddyPress out there why aren't these included by default? =
* Simple answer: The settings of these add-ons have no settings page to link to. So linking/ adding is just not possible.
* Note: More plugin and theme support will be added from time to time. 

= Can custom menu items be hooked in via theme or other plugins? =
Yes, this is possible since version 1.2 of the plugin! There are 4 action hooks available for hooking custom menu items in -- `bptb_custom_main_items` for the main section, `bptb_custom_extension_items` for the exentensions section, `bptb_custom_theme_items` for the theme section plus `bptb_custom_group_items` for the resource group section. Here's an example code:
`
add_action( 'bptb_custom_group_items', 'bptb_custom_additional_group_item' );
/**
 * BuddyPress Toolbar: Custom Resource Group Items
 *
 * @global mixed $wp_admin_bar
 */
function bptb_custom_additional_group_item() {

	global $wp_admin_bar;

	$wp_admin_bar->add_menu( array(
		'parent' => 'ddw-buddypress-bpgroup',
		'id'     => 'your-unique-item-id',
		'title'  => __( 'Custom Menu Item Name', 'your-textdomain' ),
		'href'   => 'http://deckerweb.de/',
		'meta'   => array( 'title' => __( 'Custom Menu Item Name Tooltip', 'your-textdomain' ) )
	) );
}
`

= Can certain sections be removed? =
Yes, this is possible! You can remove the following sections: "Manage Groups" group (all items) / "Extensions" area (all items) / "Theme" (all items!) / "Resources link group" at the bottom (all items) / "German language stuff" (all items) / "Translations" (all items)

To achieve this add one, some or all of the following constants to your theme's/child theme's `functions.php` file:
`
/** BuddyPress Toolbar: Remove Manage Content Items */
define( 'BPTB_MANAGE_GROUPS_DISPLAY', FALSE );

/** BuddyPress Toolbar: Remove Extensions Items */
define( 'BPTB_EXTENSIONS_DISPLAY', FALSE );

/** BuddyPress Toolbar: Remove Theme Items */
define( 'BPTB_THEME_DISPLAY', FALSE );

/** BuddyPress Toolbar: Remove Resource Items */
define( 'BPTB_RESOURCES_DISPLAY', FALSE );

/** BuddyPress Toolbar: Remove German Language Items */
define( 'BPTB_DE_DISPLAY', FALSE );

/** BuddyPress Toolbar: Remove Translations Items */
define( 'BPTB_TRANSLATIONS_DISPLAY', FALSE );
`

= Can the the whole toolbar entry be removed, especially for certain users? =
Yes, that's also possible! This could be useful if your site has special user roles/capabilities or other settings that are beyond the default WordPress stuff etc. For example: if you want to disable the display of any "BuddyPress Toolbar" items for all user roles of "Editor" please use this code:
`
/** BuddyPress Toolbar: Remove all items for "Editor" user role */
if ( current_user_can( 'editor' ) ) {
	define( 'BPTB_DISPLAY', FALSE );
}
`

To hide only from the user with a user ID of "2":
`
/** BuddyPress Toolbar: Remove all items for user ID 2 */
if ( 2 == get_current_user_id() ) {
	define( 'BPTB_DISPLAY', FALSE );
}
`

To hide items only in frontend use this code:
`
/** BuddyPress Toolbar: Remove all items from frontend */
if ( ! is_admin() ) {
	define( 'BPTB_DISPLAY', FALSE );
}
`

In general, use this constant do hide any "BuddyPress Toolbar" items:
`
/** BuddyPress Toolbar: Remove all items */
define( 'BPTB_DISPLAY', FALSE );
`


= Available Filters to Customize More Stuff =
All filters are listed with the filter name in bold and the below additional info, helper functions (if available) as well as usage examples.

**bptb_filter_capability_all**

* Default value: `administrator` (BuddyPress admin stuff should only be done by admins, right?!)
* 4 Predefined helper functions:
 * `__bptb_role_editor` -- returns `'editor'` role -- usage:
`
add_filter( 'bptb_filter_capability_all', '__bptb_role_editor' );
`
 * `__bptb_cap_edit_theme_options` -- returns `'edit_theme_options'` capability -- usage:
`
add_filter( 'bptb_filter_capability_all', '__bptb_cap_edit_theme_options' );
`
 * `__bptb_cap_manage_options` -- returns `'manage_options'` capability -- usage:
`
add_filter( 'bptb_filter_capability_all', '__bptb_cap_manage_options' );
`
 * `__bptb_cap_install_plugins` -- returns `'install_plugins'` capability -- usage:
`
add_filter( 'bptb_filter_capability_all', '__bptb_cap_install_plugins' );
`
* Another example:
`
add_filter( 'bptb_filter_capability_all', 'custom_bptb_capability_all' );
/**
 * BuddyPress Toolbar: Change Main Capability
 */
function custom_bptb_capability_all() {
	return 'activate_plugins';
}
`
--> Changes the capability to `activate_plugins`

**bptb_filter_main_icon**

* Default value: BuddyPress logo (favicon) with some kind of orange/red touch... :)
* 11 Predefined helper functions for the 11 included colored icons, returning special colored icon values - the helper function always has this name: `__bptb_colornamehere_icon()` this results in the following filters ready for usage:
`
add_filter( 'bptb_filter_main_icon', '__bptb_blue_icon' );

add_filter( 'bptb_filter_main_icon', '__bptb_brown_icon' );

add_filter( 'bptb_filter_main_icon', '__bptb_gray_icon' );

add_filter( 'bptb_filter_main_icon', '__bptb_green_icon' );

add_filter( 'bptb_filter_main_icon', '__bptb_khaki_icon' );

add_filter( 'bptb_filter_main_icon', '__bptb_orange_icon' );

add_filter( 'bptb_filter_main_icon', '__bptb_pink_icon' );

add_filter( 'bptb_filter_main_icon', '__bptb_red_icon' );

add_filter( 'bptb_filter_main_icon', '__bptb_turquoise_icon' );

add_filter( 'bptb_filter_main_icon', '__bptb_yellow_icon' );

add_filter( 'bptb_filter_main_icon', '__bptb_yellowtwo_icon' );

add_filter( 'bptb_filter_main_icon', '__bptb_theme_images_icon' );
`
--> Where the last helper function returns the icon file (`icon-bptb.png`) found in your current theme's/child theme's `/images/` subfolder

* Example for using with current child theme:
`
add_filter( 'bptb_filter_main_icon', 'custom_bptb_main_icon' );
/**
 * BuddyPress Toolbar: Change Main Icon
 */
function custom_bptb_main_icon() {
	return get_stylesheet_directory_uri() . '/images/custom-icon.png';
}
`
--> Uses a custom image from your active child theme's `/images/` folder

--> Recommended dimensions are 16px x 16px

**bptb_filter_main_icon_display**

* Returning the CSS class for the main item icon
* Default value: `icon-buddypress` (class is: `.icon-buddypress`)
* 1 Predefined helper function:
 * `__bptb_no_icon_display()` -- usage:
`
add_filter( 'bptb_filter_main_icon_display', '__bptb_no_icon_display' );
`
--> This way you can REMOVE the icon!

 * Another example:
`
add_filter( 'bptb_filter_main_icon_display', 'custom_bptb_main_icon_display_class' );
/**
 * BuddyPress Toolbar: Change Main Icon CSS Class
 */
function custom_bptb_main_icon_display_class() {
	return 'your-custom-icon-class';
}
`
--> You then have to define CSS rules in your theme/child theme stylesheet for your own custom class `.your-custom-icon-class`

--> Recommended dimensions are 16px x 16px

**bptb_filter_main_item_title**

* Default value: "BuddyPress"
* Example code for your theme's/child theme's `functions.php` file:
`
add_filter( 'bptb_filter_main_item_title', 'custom_bptb_main_item_title' );
/**
 * BuddyPress Toolbar: Change Main Item Name
 */
function custom_bptb_main_item_title() {
	return __( 'Your custom main item title', 'your-textdomain' );
}
`

**bptb_filter_main_item_title_tooltip**

* Default value: "BuddyPress"
* Example code for your theme's/child theme's `functions.php` file:
`
add_filter( 'bptb_filter_main_item_title_tooltip', 'custom_bptb_main_item_title_tooltip' );
/**
 * BuddyPress Toolbar: Change Main Item Name's Tooltip
 */
function custom_bptb_main_item_title_tooltip() {
	return __( 'Your custom main item title tooltip', 'your-textdomain' );
}
`

**bptb_filter_buddypress_name** and **bptb_filter_buddypress_name_tooltip**

* Default value for both: "BuddyPress"
* Used for some items within toolbar links to enable proper branding
* Change things like in the other examples/principles shown above

**Final note:** If you don't like to add your customizations to your theme's/child theme's `functions.php` file you can also add them to a functionality plugin or an mu-plugin. This way you can also use this better for Multisite environments. In general you are then more independent from child theme changes etc.

All the custom & branding stuff code above can also be found as a Gist on Github: https://gist.github.com/2643807 (you can also add your questions/ feedback there :)

== Screenshots ==

1. BuddyPress Toolbar in action - primary level - default state (running with BuddyPress 1.6-bleeding and WordPress 3.3+ here)
2. BuddyPress Toolbar in action - second level - BP settings/components and frondend roots pages of active components
3. BuddyPress Toolbar in action - second level - BP user field groups (plus some specific extensions)
4. BuddyPress Toolbar in action - third level - BP users management (plus some specific extensions)
5. BuddyPress Toolbar in action - second level - optinal BP Groups management group (only if supporting plugins are active!)
6. BuddyPress Toolbar in action - third level - extensions support (here with a huge list of active plugins :) ([See larger image view](http://www.wordpress.org/extend/plugins/buddypress-toolbar/screenshot-6.png))
7. BuddyPress Toolbar in action - second level - resource links group
8. BuddyPress Toolbar in action - translations/language specific links at the bottom - for example: German locale

== Changelog ==

= 1.3 (2012-06-15) =
* *Maintenance release*
* *Extended plugin support:*
 * NEW: Added support for "BP Profile Search" (free, by Andrea Tarantini).
 * NEW: Added support for "BuddyPress Portfolio" (free, by Nicolas Crocfer).
 * NEW: Added support for "BuddyPress Login Redirect" (free, by Jatinder Pal Singh).
 * NEW: Added support for "BP Profile as Homepage" (free, by Jatinder Pal Singh).
* *Other stuff and maintenance:*
 * UPDATE: Improved behavior of constants for removing sections (or all), so that setting to "FALSE" removes stuff, and setting to "TRUE" displays stuff. (This does not affect existing behavior as explained in the FAQ but introduces ability to use the boolean "TRUE" to bring stuff back in favor of removing the code lines - great for testing purposes etc.)
 * UPDATE: Added one more check for Groups component to avoid crashes when running BuddyPress update wizard (single & Multisite installs).
 * CODE: Minor code/documentation updates & improvements.
 * UPDATE: Updated German translations and also the .pot file for all translators!

= 1.2 (2012-05-10) =
* *New features:*
 * NEW: Fully Multisite compatible! Plugin dynamically changes admin URLs whether or not you are in Multisite invironment - of course supported plugins are included in this behavior (and if these plugins support Multisite)!
 * NEW: Full capability support - wherever a BuddyPress, a plugin or theme settings page depends on user capabilities or roles BuddyPress Toolbar now fully respects and inherits that! - In other words: A user who won't see a special admin page without my plugin also won't if my plugin is active!
 * NEW: Added links to active component's frontend root pages, for example `yoursite.com/groups` -- this includes "Blogs/Sites" support for Multisite/Networks!
 * NEW: Now built-in search form for BuddyPress Codex - just type in a search directly in your toolbar menu!
 * COOL: Plugin can now be branded and customized a lot more!
 * NEW: Added 7 filters to change icon graphic, main item name, main capability and more! For these cases there are now the new built-in filters and helper functions available! [(See "FAQ" section here)](http://wordpress.org/extend/plugins/buddypress-toolbar/faq/)
 * NEW: Added 4 action hooks for hooking custom menu items in -- for all main sections plus the resource group section (see FAQ section here for more info on that).
 * NEW: Almost all sections can now be removed for special needs, capabilities etc. -- all done via `constants` in your active theme/child theme -- this way you can customize for your staff members if you need some more users with extended or restricted admin bar/toolbar access (See "FAQ" section here)
 * NEW: Added possibility for custom and update-secure language files for this plugin - just upload them to `/wp-content/languages/buddypress-toolbar/` (just create this folder) - this enables you to use complete custom wording or translations.
* *Extended plugin support:*
 * NEW: Added support for "BP Group Management" (free, by Boone Gorges)
 * NEW: Added support for "BP Profiles Statistics" (premium, by slaFFik)
 * NEW: Added support for "BuddyPress Docs" (free, by Boone Gorges)
 * NEW: Added support for "BuddyPress MyMood" (free, by Ayush)
 * NEW: Added support for "BuddyPress xProfiles ACL" Lite (free, by NetTantra)
 * NEW: Added support for "SeoPress" (free, by Sven Lehnert + Sven Wagener at ThemeKraft)
 * NEW: Added support for "CollabPress" (free, by WebDevStudios)
 * NEW: Added support for "BBG Record Blog Roles Changes" (free, by Boone Gorges + slaFFik)
 * NEW: Added support for "CD BuddyPress Avatar Bubble" (free, by slaFFik + valant)
 * NEW: Added support for "sxss Buddypress Shared Friends" (free, by sxss)
 * NEW: Added support for "Breadcrumbs Everywhere" (free, by Betsy Kimak)
 * UPDATE: Improved "WangGuard" plugin support.
 * UPDATE: Improved "Achievements" plugin support - added link to component's frondend root page.
 * UPDATE: Added proper capabilities to the settings links display to follow original settings of the supported plugins itself.
* *Extended theme support:*
 * NEW: Added theme support for "Buddies" (premium, by Chris Paul/ZenThemes)
 * NEW: Added theme support for "Visual" (premium) and "Fanwood" (free) (both by DevPress)
 * NEW: Added theme support for 10 themes from 3oneseven.com (all free, by milo317)
 * UPDATE: Added proper capabilities to the settings links display to follow original setting of the supported theme itself (only where available, or I had access to this data!).
* *Other stuff and maintenance:*
 * UPDATE: Added full compatibility with WordPress 3.4+ (tested since 3.4-beta1 up to latest trunk version).
 * CODE: Successfully tested against BuddyPress 1.5+ and 1.6-alpha branches plus WordPress 3.3 branch and new 3.4 branch. Also successfully tested in WP_DEBUG mode (no notices or warnings).
 * UPDATE: Updated readme.txt file
 * UPDATE: Updated all existing and added some new screenshots.
 * UPDATE: Updated German translations and also the .pot file for all translators!
 * UPDATE: Extended GPL License info in readme.txt as well as main plugin file.
 * NEW: Easy plugin translation platform with GlotPress tool: [Translate "BuddyPress Toolbar"...](http://translate.wpautobahn.com/projects/wordpress-plugins-deckerweb/buddypress-toolbar)

= 1.1 (2012-02-19) =
* NEW: Added a few new BP Codex sub-level links
* NEW: Added *BuddyPress News Planet* feed link to resource links - you can also access this from here: http://friendfeed.com/buddypress-news
* NEW: Added new community resource "BP-Tricks.com"
* *Extended plugin support:*
 * Added plugin support for "BuddyPress Group Extras" (free, by slaFFik)
 * Added plugin support for "BP GTM System" (free, by slaFFik, valant)
 * Added plugin support for "Buddypress User Account Type Lite" (free, by Rimon Habib)
 * Added plugin support for "BuddyStream" (free, by Peter Hofman)
 * Added plugin support for "BuddyPress Group Default Avatar" (free, by Vernon Fowler)
 * Added plugin support for "BuddyPress Achievements" (free, by Paul Gibbs)
 * Added plugin support for "BuddyPress Twitter" (free, by Charl Kruger)
 * Added plugin support for "BuddyPress Group Email Subscription" (free, by Deryk Wenaus + Boone Gorges)
 * Added plugin support for "BP Code Snippets" (free, by imath)
 * Added plugin support for "Invite Anyone" (free, by Boone Gorges)
 * Added plugin support for "Events Manager" (free, by Marcus Sykes)
* *Extended theme support:*
 * Added theme support for "Builder Framework" via "Builder BuddyPress" plugin (premium, by iThemes)
 * Added theme support for "Infinity (Anti-) Framework" via "BP Template Pack" plugin (free/beta, by PressCrew)
 * Added theme support for "Gratitude" (premium, by Chris Paul/ZenThemes)
 * Added theme support for "Business Services" and "BuddyPress Corporate" (both free, by WPMU DEV)
* CODE: No longer loading css styles or menu items for not logged-in users when plugins like "GD Press Tools" are active (which have options to show toolbar / admin bar also for visitors...)
* UPDATE: Updated German translations and also the .pot file for all translators!
* NEW: Added banner image on WordPress.org for better plugin branding :)

= 1.0 (2012-02-07) =
* Initial release

== Upgrade Notice ==

= 1.3 =
Maintenance release: Minor updates & code improvements. Extended plugin support. Further, updated .pot file for translators and German translations.

= 1.2 =
Major additions & improvements on all fronts! Full Multisite and cabability support for all links! Extended plugin & theme support. Also, updated .pot file for translators and German translations.

= 1.1 =
Major improvements - Added more resource links. Further, added support for 11 more plugins and 5 more themes/frameworks. Minor code/ documentation tweaks. Also, updated .pot file for translators and German translations.

= 1.0 =
Just released into the wild.

== Plugin Links ==
* [Translations (GlotPress)](http://translate.wpautobahn.com/projects/wordpress-plugins-deckerweb/buddypress-toolbar)
* [User support forums](http://wordpress.org/support/plugin/buddypress-toolbar)
* [Code snippets archive for customizing, GitHub Gist](https://gist.github.com/2643807)
* *Plugin tip:* [My WordPress Toolbar / Admin Bar plugin series...](http://wordpress.org/extend/plugins/tags/ddwtoolbar)

== Donate ==
Enjoy using *BuddyPress Toolbar*? Please consider [making a small donation](http://genesisthemes.de/en/donate/) to support the project's continued development.

== Translations ==

* English - default, always included
* German: Deutsch - immer dabei! [Download auch via deckerweb.de](http://deckerweb.de/material/sprachdateien/buddypress/#buddypress-toolbar)
* For custom and update-secure language files please upload them to `/wp-content/languages/buddypress-toolbar/` (just create this folder) - This enables you to use fully custom translations that won't be overridden on plugin updates. Also, complete custom English wording is possible with that, just use a language file like `buddypress-toolbar-en_US.mo/.po` to achieve that (for creating one see the following tools).

**Easy plugin translation platform with GlotPress tool:** [**Translate "BuddyPress Toolbar"...**](http://translate.wpautobahn.com/projects/wordpress-plugins-deckerweb/buddypress-toolbar)

*Note:* All my plugins are internationalized/ translateable by default. This is very important for all users worldwide. So please contribute your language to the plugin to make it even more useful. For translating I recommend the awesome ["Codestyling Localization" plugin](http://wordpress.org/extend/plugins/codestyling-localization/) and for validating the ["Poedit Editor"](http://www.poedit.net/), which works fine on Windows, Mac and Linux.

== Additional Info ==
**Idea Behind / Philosophy:** Just a little leightweight plugin for all the BuddyPress community managers out there to make their daily community admin life a bit easier. I'll try to add more plugin/theme support if it makes some sense. So stay tuned :).
