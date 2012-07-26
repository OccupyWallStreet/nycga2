=== Slick Social Share Buttons ===
Contributors: remix4
Donate link: http://www.designchemical.com/blog/index.php/wordpress-plugins/wordpress-plugin-slick-social-share-buttons/#form-donate
Tags: social media, facebook, linkedin, twitter, google+1, digg, delicious, reddit, buffer, social networks, bookmarks, buttons, animated, jquery, flyout, drop down, floating, sliding, pin it, pinterest, social statistics, social metrics
Requires at least: 3.0
Tested up to: 3.3.1
Stable tag: 2.4.3

Slick social share buttons adds facebook, twitter, google +1, linkedin, digg, delicious, reddit, stumbleupon, buffer and pinterest pin it social media buttons in a floating or slide out tab. Includes a social statistics page in the plugin admin panel showing summaries of all share totals

== Description ==

Add facebook, twitter, google +1, linkedin, digg, delicious, reddit, buffer, stumbleupon and pinterest pin it social media buttons to your website in either a floating or sliding panel and see summaries of all your social metrics in the admin social statistics page

= Button Panel Options =

To access the plugin settings go to Wordpress admin -> Social Buttons -> Social Buttons

The button panel can be fully customised on the plugin settings page in Wordpress admin:

* Type - Select either a "floating" button or a "Sliding" tab
* Location - Select the position of the button panel.
* Position From Center - only available for floating buttons this allows you to set the panels position based on the center of the screen as opposed to the edge of the browser. If checked enter the number of pixels that the panel should be positioned from the center of screen
* Offset - Position the panel by offsetting the location from the edge of the browser in pixels
* Direction - For sliding tabs select whether to list the buttons horizontally or vertically.
* Disable Floating Effects - check the box to remove the floating effects - floating panel will remain static in the screen
* Floating Speed - The speed for the floating animation (only applicable for the floating type)
* Animation Speed - The speed at which the panel will open/close
* Auto-Close - If checked, the panel will automatically slide closed when the user clicks anywhere in the browser window
* Load Open - If checked the sticky/floating panel will be in the open position when the page loads
* Default Skin - Uncheck if you wish to use your own CSS styles for the button tab and panel
* Tab Image URL - Enter URL if you wish to replace the current default tab image

= Set Display Pages =

* Home Page
* Posts Page (when using a static home page)
* Pages
* Posts
* Category Pages - includes detailed list of all categories - select those you wish to exclude
* Archive Pages

= Button Options =

* Twitter - Button size & Twitter ID
* Facebook - Button size, select whether to use iFrame or xfbml & input Facebook App ID and Admin ID details
* Google +1 - Button size
* LinkedIn - Button size
* StumbleUpon - Button size
* Digg - Button size
* Delicious - Button size
* Reddit - Button size
* Buffer - Button size
* Pin It - Button size & select option to either use the featured image for the pinit pic or use a modified version, which lets the user select the image from a preview

To change order of display drag & drop the button panel to the required position

= Twitter URL Shortening =

The plugin includes the option to select URL shortening for twitter from several services:

* Bit.ly
* Digg
* Su.pr
* tinyurl

If using bit.ly the API Key and account login must also be entered. For su.pr these are both optional.

= Shortcodes =

The plugin includes the feature to add text links within your site content that will open/close the slide out or floating tab.

1. [dcssb-link] - default link, which will toggle the button panel open/closed with the link text "Share".
2. [dcssb-link text="Tell Your Friends"] - toggle the button panel open/closed with the link text "Tell Your Friends".
3. [dcssb-link action="open"] - open the panel with the default link text "Share".
4. [dcssb-link action="close"] - close the panel with the default link text "Share".

[__See demo__](http://www.designchemical.com/lab/demo-wordpress-slick-social-share-buttons-plugin/)

[__More information See Plugin Project Page__](http://www.designchemical.com/blog/index.php/wordpress-plugins/wordpress-plugin-slick-social-share-buttons/)

= Social Statistics =

The social statistics and metrics page gives you a complete overview and summaries of shares on your posts, pages and categories.

To access this page go to Wordpress Admin -> Social Buttons -> Social Stats

= Social Stats Options =

* Show - select whether to show your just your home page, posts, pages or category pages. Selecting the pages option will also include the home page results.
* Filter - available for posts only this lets you filter the results by category
* Order By - sort results by either date or post/page title in either ascending or descending order
* Display - show the total shares per page either as text, text + data heatmap or as active share buttons (note this option may slow down loading)
* Per Page - adjust the number of results per page

== Installation ==

1. Upload the plugin through `Plugins > Add New > Upload` interface or upload `slick-social-share-buttons` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to the plugin configuration screen via menu options Admin -> Social Buttons -> Social Buttons
4. Select the required buttons and plugin settings
5. Click save to activate the social buttons

== Frequently Asked Questions ==

[__Also check out our slick social share buttons faq page__](http://www.designchemical.com/blog/index.php/frequently-asked-questions/slick-social-share-buttons/)

= The buttons appear on the page but the floating/slide out tab does not appear. Why? =

There are several reasons why this may occur:

1. The plugin adds the required jQuery code to your template footer. Make sure that your template files contain the wp_footer() function.

2. Other non-functioning plugins which have javascript errors may result in the plugin not being able to initialise the relevant jQuery code. Remove any unwanted plugins and try again. Checking with Firebug will show where these error are occuring.

3. If your theme files or another plugin incorrectly loads the jQuery library this may overwrite the plugin files - check your source code to see if jQuery is loaded a 2nd time after Wordpress has loaded the file.

== Screenshots ==

1. Plugin options screen
2. Example of vertical sliding social buttons panel
3. Example of horizontal sliding social buttons panel
4. Example of floating panel
5. Social Statistics admin page

== Changelog ==

= 2.4.3 = 
* Edit: Delicious HTML

= 2.4.2 = 
* Edit: Pinterest CSS

= 2.4 = 
* Added: Buffer button
* Added: New pinterest feature allowing user to select image

= 2.3.1 = 
* Fixed: Reddit stats bug

= 2.3 = 
* Added: Reddit button
* Fixed: Stumbleupon width & height

= 2.2 = 
* Added: Delicious button
* Removed: Obsolete google buzz button

= 2.1.4 = 
* Added: Option to disable facebook opengraph settings

= 2.1.3 = 
* Fixed: Bug with stats page option

= 2.1.2 = 
* Fixed: Reposition facebook ga tracking code

= 2.1.1 = 
* Added: Option to show buttons on posts page when using static home page
* Added: Conditional loading of jquery & CSS files
* Added: Data heatmap for social stats count

= 2.1 = 
* Added: Only show statistics for active buttons
* Fixed: Total posts when filtering posts by category

= 2.0 = 
* Added: Social statistics admin page

= 1.4.2 = 
* Fixed: Short URL post meta data

= 1.4.1 = 
* Update: Pin It button size drop down menu
* Update: Optimize jquery plugin files
* Update: Changed method of retrieving post ID

= 1.4 = 
* Added: Pin It share button for pinterest.com

= 1.3 = 
* Update: facebook js script
* Update: facebook opengraph type meta tags

= 1.2.9 = 
* Update: Added local GA social tracking script

= 1.2.8 = 
* Update: Increase title text

= 1.2.7 = 
* Fixed: Tweet home page URL using shortener
* Fixed: Hiding of facebook like comment box

= 1.2.6 = 
* Added: option to disable floating effects

= 1.2.5 = 
* Added: ability to position floating buttons based on page center

= 1.2.4 = 
* Added: google buzz button

= 1.2.3 = 
* Added: detailed category list and option to exclude specific categories from showing button panel

= 1.2.2 = 
* Update: update jquery.social.float.1.1.js
* Fixed: Error with

= 1.2 = 
* Added: Digg button
* Fixed: Bug with Stumbleupon & Safari

= 1.1 = 
* Added: Default image for facebook like
* Fixed: URL for Wordpress home page

= 1.0 = 
* First release

== Upgrade Notice ==
