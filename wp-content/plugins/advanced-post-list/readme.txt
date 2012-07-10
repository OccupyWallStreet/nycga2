=== Advanced Post List ===
Contributors: jokerbr313
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=2E6Z4VQ6NF4CQ&lc=US&item_name=Wordpress%20%2d%20Advanced%20Post%20List&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Tags: Advanced, Post List, Categories, Category, Children, Children Pages, Content, Custom, Custom Post Type, Custom Post Types, Custom Taxonomy, Custom Taxonomies, Draft, Draft Posts, Excerpt, Filter, Future, Future Posts, Links, List, Links, News, Page, Pages, Parent, Parent Pages, Popular Posts, Post, Posts, Private, Private Posts, Related, Related Posts, Recent, Recent Posts, Shortcode, Shortcodes, Simple, Tag, Tags, Thumbnail, Widget, Widgets
Requires at least: 2.0.2
Tested up to: 3.4
Stable tag: 0.3.b3

Create custom post lists with easy to use advanced settings. Highly customizable for designing unique post-list designs.

== Description ==
[forum home]: http://advanced-post-list.wikiforum.net/
		"Community support forum"
[forum issues]: http://advanced-post-list.wikiforum.net/f1-issues-troubleshooting
		"Find/Start to fix your problem"
[forum presetDesigns]: http://advanced-post-list.wikiforum.net/f5-preset-designs
		"Find/Submit a preset design"
[code google apl]: http://code.google.com/p/wordpress-advanced-post-list/
		"Official bug reports, downloads, and projects."
[wordpress kalins post list]: http://wordpress.org/extend/plugins/kalins-post-list/
		"Kalin's plugin page"


Highly customizable plugin for designing a large variety of post lists. Allowing 
the Webmaster to create any design for displaying Recent Posts, Popular Posts, 
Related Posts, Future Posts, etc. Including Custom Post Type & Taxonomy support 
and allows the Webmaster to Back-Up the plugin database. 
		
[Community][forum home] - Plugin Home site

[Issues/Bug Report][forum issues] - Report Issues with the plugin.

[Preset Designs][forum presetDesigns] - Browse or submit a preset design (requires registration).

This is an alternate/upgraded version of [Kalins Post List][wordpress kalins post list] 
that was unfortunately declared abandoned. Most of the credit for creating an 
extraordinary plugin like this goes to **Kalin**. As a plugin developer, I also 
admire the uses of this plugin. So, I couldn't see a nice plugin like this get 
left in the dark. 

This plugin is still in the first stages of its target design. Many of the old 
bugs have been fixed, and new features added. Version 1.0.0 will feature many of 
the functionalities that Kalin and others have mentioned, and will have a completely 
new layout to accommodate for the extra tools that will be added. Right now the 
plugin is in the pre-release versions (0.X.X).

**0.2.0 Release** - contains an export function that also supports individual 
preset downloads. This is for two main reasons. **1)** Help with debugging 
purposes when trying to replicate the style and some of the content. 
**2)** Community purposes. It was a simple piece of code to add (maybe 5 lines, 
if that) for preset downloads after all the main export functions, and I also 
believe having a community will help with the evolution of the plug in by 
spotting the limitations and the advances needed for the plugin. 

**WORKING 0.3.Beta Version - CAUTION when upgrading, back-up your plugin data 
in case a bug occurs**  - supports Custom Post Types & Taxonomies which provides 
a highly customizable post filter and can display pages from various Post Types. 
The new version is also setup to allow multiple Parent Pages to be used, new 
dialog feature for displaying errors and info, and is setup to filter by Post 
Status (any, publish, pending, draft, auto-draft, future, private, inherit, and 
trash). The scripting was changed to make use of wp_enqueue_(script/style) which 
also led to JQuery UI to be added and allows other future supported scripts to 
be added. 

**Pre-Release Projects for Version 1.0.0**

* **(Completed)** Import/export (including importing data from Kalins Post List)
* **(Completed)** Custom Post Type & Taxonomies Support. Available in the 0.3 release.
* Additional sort methods for 'Orderby' combo box


== Installation ==
1. Upload zip to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to Settings->Advanced Post List to access the settings.
 

== Frequently Asked Questions ==

= How do I display the post list that I created? =
You need to locate and copy the shortcode which is in the Advance Post List - Settings page on the saved preset table. Then create a page/post and paste the shortcode on your page/post (e.g. [post_list name='some-preset-name])' 


== Screenshots ==
1. Advanced Post List admin page.
2. Displays an example of what the created preset will look like.
3. Saved Post List table. Now you can download individual presets.
4. Easy to use shortcodes to add to your page/post.
5. Preset in action.
6. Export or import your plugin database data.

== Changelog ==

= 0.3.b3 =
* Fixed some 'scrict' errors that were being tossed.
* Fixed the Activation, Deactivation, and Delete/Uninstall action hooks.

= 0.3.b2 =
* Fixed issue with script interference.
* Fixed installing/restoring default preset settings.

= 0.3.b1 =
* Added Custom Post Type and Taxonomy support.
* Added JQuery UI features.
* Added "Post Status" setting for presets.
* Changed "Post Parent" to carry multiple parent pages instead of just one.
* Changed script and style handling to use wp_enqueue.
* Changed from get_posts() to APLQuery (WP_Query) class.
* Changed APLPresetObj class.
* Added APLQuery class to shadow WP_Query.
* Added a plugin database update for the change preset settings.
* Changed import file to accommodate for new preset settings.


= 0.2.0 =
* Designed a 'General Settings' section for core settings.
* Changed 'Upon plugin deactivation clean up all database entries' to a yes/no ratio.
* Added Import/Export feature.
* Added a preset download feature.
* Added admin.css to separate styles from admin.php file.
* Designed a new default css style button.
* Fixed database version checking.
* Fixed PHP hardcode string that was displayed to the admin.
* Fixed 'Exclude Current'.
* Fixed Before, Content, and After TextArea to expand correctly

= 0.1.1 =
* Fixed including required files.

= 0.1.0 =
* Basic clean up and reorganizing.
* Added phpdocumentation to created files.

= 0.1.b1 =
* 'Post Parent' combo box was corrected to now display only pages
* 'Orderby' combo box was corrected to pass values that WordPress allows with WP Query
* Edited some of the front end designs to make it current with the plugin
* Post data is now being correctly pulled within APL_run
* A clean up was done on 0.1.a1 

= 0.1.a1 =
* -Very first working version-
* Fixed 'Require all categories'.
* Upgraded core functions.

== Upgrade Notice ==

= 0.3.b3 =
Beta Version. It is recommended you back up data prior to upgrading. This version 
introduces Custom Post Type & Taxonomy support, and a few added preset settings.
Fixed some 'strict' errors that were being tossed that could cause an issue.

= 0.3.b2 =
Beta Version. It is recommended you back up data prior to upgrading. This version 
introduces Custom Post Type & Taxonomy support, and a few added preset settings. 
Fixed a problem with script handling that was interfering with built-in 
scripting.

= 0.3.b1 =
Beta Version. Please back up your plugin data prior to upgrading. This version 
introduces custom post type and taxonomy support. Along with a few added settings.

= 0.3.a1 =
Alpha Version. Please back up your plugin data prior to upgrading. This version 
introduces custom post type and taxonomy support. Along with a few added settings.

= 0.2.0 =
Upgrade adds a new export/import feature to back up your data, and fixes the PHP 
hardcode, exclude current, and TextArea element. See change log for more details.

= 0.1.1 =
The require() functions in advanced-post-list.php didn't have a dynamic value set.

= 0.1.0 =
First stable version.
