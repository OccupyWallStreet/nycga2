=== WP UI - Tabs, Accordions, Sliders ===
Contributors: kavingray
Donate link: http://kav.in/donation
Plugin URI: http://kav.in/wp-ui-for-wordpress
Tags: posts, tabs, accordion, sliders, spoilers, collapsibles, posts, feeds, rss, rss 2.0, jquery, jquery ui, dialogs, custom themes, themeroller, CSS3, pagination, related-posts
Requires at least: 3.1
Tested up to: 3.3.1
Stable tag: 0.8.2

Easily add Tabs, Accordion, dialogs and collapsibles to your posts. With 14 fresh & Unique CSS3 styles and 24 multiple jQuery UI custom themes.

== Description ==

WordPress is a great platform suitable for almost every case of websites out there, ranging from a personal blog to a complex CMS. This plugin started out as a snippet when i needed a way to shorten my posts and make them look more presentable. WP UI plugin for WordPress, right from its first release, is all about user experience and presentation. It takes care of all the not-needed background stuff and makes it ultimately easy to implement wp ui widgets - Tabs, accordions, spoilers, dialogs.  Real power of this plugin lies in the handy functions and shortcodes that deal with posts and feeds.

>[Documentation](http://kav.in/projects/)  |  [Support](http://kav.in/forum/)

= Base =

WP UI for WordPress is powered by jQuery **U**ser **I**nterface library - jQuery UI. It acts as a bridge between jQuery UI and WordPress, simplifies and manages the code structure for easy usage. 

= Styles =

WP UI comes with 15 stunning CSS3 styles alongside the ability to use all the [jQuery UI themes](http://jqueryui.com/themeroller/). Moreover you can use more than one jQuery UI theme in the page and with [jQuery UI custom themes](http://kav.in/wp-ui-using-jquery-ui-custom-themes). Want your own CSS3 theme? No problem, just upload the stylesheet, scan, select it. It's that easy.

= Ease of usage =

There is a dedicated menus and dialogs within the WordPress editor that allows both entering content manually or inserting posts - Easy. Options page comes with contextual help and is intuitive.

= Shortcodes & Functions =

Most common functionality in WP UI is achieved through shortcodes, that comes with wide variety of arguments. For example - <code>[wptabs style="wpui-blue"]...</code> style argument accepts around 24 values, not including any custom themes you might want to try. Want only a mini loop or display related/popular/recent/random posts? Well, the shortcodes and functions are at your disposal.

= Posts and Feeds =

Including posts and feeds have never been more easier. With a single post shortcode - <code>[wptabposts]</code>, get and display posts as neatly arranged tabs or accordions, Automatically. This shortcode's counterpart that deals with feeds - <code>wpuifeeds</code>. And you can use the post argument ( <code>[wpspoiler post="3028"]</code> ) universally with most shortcodes to get a single post. 

= Documentation =

WP UI comes with rich documentation bundled in the options page and right within the editor. While they work great for a quick reference on shortcodes or arguments, there is a [dedicated documentation](http://kav.in/projects/) site, built and updated every day.

= Support =

There is an active support [forum](http://kav.in/forum) available for getting quick help and support. Moreover, an user-editable wiki style documentation is coming right away. Stay tuned, friends.

= Translations =

Missing your language here? Contribute by translating your favorite plugin!

* [All Translations](http://kav.in/resources/translations/wp-ui/) 
* [Serbian](http://kav.in/resources/translations/wp-ui/serbian) by Zoran Aksic.
* [Send your translations](http://kav.in/contact/)

= Further =

* [Follow @kavingray](http://twitter.com/kavingray)
* [Support](http://kav.in/forum)
* [Documentation/Usage/Demo](http://kav.in/projects/blog/tag/wp-ui/)
* [Plugin page](http://kav.in/wp-ui-for-wordpress)
* [Like us on Facebook](http://www.facebook.com/#!/pages/Capability/136970409692187)
* [Help improve multi-language support](http://kav.in/forum/discussion/81/need-help-improving-multi-language-support)

== Installation ==

1. Upload the `wp-ui` folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Add shortcodes to the post editor and enjoy!

== Frequently Asked Questions ==

= Where can i find a quick start guide? =
Read the [Quick start guide](http://kav.in/projects/blog/wp-ui-quick-start/) to learn about editor interface.

= Where can i find recent & up-to-date FAQ? =

Can be found [here](http://kav.in/projects/blog/wp-ui-faq/).

= How do i use multiple jQuery UI themes? =
It is possible from WP UI 0.5.5 to use multiple jQuery UI themes on the same page. Check out this [guide](http://kav.in/wp-ui-using-jquery-ui-custom-themes/).

= Where can i demo the custom CSS3 styles? =

Demo with the *style switcher* can be found [here](http://kav.in/projects/plugins/wp-ui).

= What if the user does not have Javascript enabled? =

All the code will **degrade gracefully** with javascript disabled. Please try disabling the Javascript in your browser to get the idea.

= Can i use all the available jQuery UI themes? =

Yes. Just enter the name of jQuery UI theme as value to the shortcode [wptabs] style argument. From Version 0.5.5, it is now possible to use **multiple UI themes**, with this [guide](http://kav.in/wp-ui-using-jquery-ui-custom-themes/).

= Why are some of my shortcodes appear on the rendered page? =

Make sure each one of your shortcodes is in the **separate line** but please do avoid empty lines. eg. 

    [wptabs]
      [wptabtitle]Tab 1[/wptabtitle]
      [wptabcontent]Contents of the first tab goes here. Any thing, blah blah.[/wptabcontent]
      [wptabtitle]Tab 2[/wptabtitle]
      [wptabcontent]Content inside the second tab is here.[/wptabcontent]
    [/wptabs]

= Why do i see closing shortcodes in the rendered page? =

Shortcodes should be entered into the wordpress post editor's HTML mode. WP Visual mode editor can insert additional paragraph `<p>` tags before and after the shortcodes, thereby rendering those invalid.

= My Accordions seems to be broken, with para tags in between? =

View the shortcodes in HTML editor, and remove the blank spaces/linebreaks between lines. Sometimes it might help to put the entire shortcode in a single line, but leaving a couple of spaces between each shortcode. 

= How do i insert or choose posts easily? =

Editor menu allows you to insert content manually and choose the posts. You can choose from a list of posts.  

= How do i differentiate between a styling and javascript conflict? =

Not working at all? Something other broken? It's mostly a javascript conflict. But when there is a CSS conflict - things work, but just not looking as expected.

= Why does the tabs/accordions behave strangely sometimes? =

This can be related to lot of white space between the shortcodes, which are converted by wordpress into empty space enclosed within `<p>` tags. Remove the unwanted space between the shortcodes.

= Where can i get help on this plugin? =

Documentation is available right within the wordpress admin. It is present in the 

* Post editor - look for the menu button in Visual mode and "?" icon in the HTML mode. This opens a document with common usage of the plugin shortcodes and their arguments.
* Contextual Help - Available on the options page on the top right corner, below the username. Click each tab and information related to that tab will appear.

Help 

* [Detailed Documentation & Demos](http://kav.in/projects/).
* [Support forums ](http://kav.in/forum)

= I have some exciting idea/suggestion about this plugin! =

I would love to hear about it. Please drop me a mail [here](http://kav.in/contact).

= How can i support this plugin? =

* Give this plugin a nice 5 star rating on the [plugin page](http://wordpress.org/extend/plugins/wp-ui/).
* Tell others that it works, with the compatibility box on the [plugin page](http://wordpress.org/extend/plugins/wp-ui/).
* Support us by a like on [Facebook](http://www.facebook.com/pages/Capability/136970409692187) 
* Tweet about the plugin or follow us on twitter [@kavingray](http://twitter.com/kavingray).
* We'd really appreciate a review or a blog post.

== Screenshots ==

1. Preview of the CSS3 styles.
2. Buttons on both editor aspects.
3. Near complete inbuilt documentation.
4. Both jQuery UI themes and WP UI CSS3 styles preview. Picture shows a full CSS3 style - wpui-sevin.

== Changelog ==

= 0.8.2 =
* Support for jQuery UI buttons.
* Rewritten Dialogs handling, Dialogs are now accessible through Image maps and links. 
* Bleeding edge channel, for cool yet experimental features enabled through admin. 
* Improved support for feeds with long URLs and foreign characters.
* New Image shortcode, Easily link any media gallery image.
* WP UI styles cleaned and updated.
* Improved documentation and Quick start guide.
* Serbian translation by Zoran Aksic, downloadable from plugin page.
* Numerous performance improvements and bug fixes.

= 0.8.1 =
* BugFixes related to previous version.
* On Demand script loading using jQuery.
* Feeds access/excerpt handling.
* Custom themes uploading error handling and directory browsing improved.
* Options page modal windows are now handled by colorbox, replacing thickbox. 
* Related posts widget updated.
* Fix regarding post meta.
* Options handling changed and might require a manual save for upgrading users.


= 0.8 =
* Lots of changes and rewrites from the last release.
* Wordpress 3.3 compatible - Try the tour and help.
* Completely overhauled editor interface.
* Revamped tinyMCE and QTags buttons - ready for 3.3 . 
* Load scripts on demand or use conditional logic.
* Caching images and scripts to improve functionality. Uses **<code>wp-content/uploads/wp-ui/cache/</code>** for cache files and images. 
* Display Related/Popular/Recent/Random Posts with the new posts widget.
* WordPress widgets, manual content or posts widget.
* Improved/revamped options page with new options added.
* Interactive tour, that briefs the new features on editor interface.
* Use images or icons in tabs ( with [wptabtitle] shortcode )
* New theme "wpui-gene" - minimalistic.
* New on spoilers - Use <code>[wpspoiler background="minimal green"...</code>. Also try blue or red instead of green.
* Re-Save the Options, please. Deactivate the plugin first if you are uploading manually. :)


= 0.7.5 =
* Get and display RSS feeds inside the wp ui widgets. 
* Rewritten documentation available right from within editor.
* Vertical style on tabs now more sleek and compact.
* Add unlimited post and feed templates.
* Select custom/exclusive styles as defaults.
* Scan styles path issue has been corrected and now works 100%.
* TinyMCE/quicktags buttons corrected.

= 0.7.4 =
* Compatibility and bug fixes. 
* Style fixes for IE < 8.
* Help on editor page, Style chooser on options page should now work perfectly.
* Please remember to save the options. 


= 0.7.3 =
* Tons of compatibility fixes. jQuery version requirement relaxed to 1.4.2.
* Another Tabs design, no-background tabs. Use background="false" with wptabs shortcode.
* Style Fixes - uniform feel. Use the custom css panel to enter your font size rule.
* New shortcode wpui_loop outputs custom loop anywhere and is not limited to WP UI.
* Pagination for retrieved posts. Basic pagination for now, to be used with the wpui_loop shortcode.
* Twitter and e-commerce widgets blank page fix. 

= 0.7.2 =
* Detailed Preview of jQuery UI themes and CSS3 styles.
* Dialog positions, style conflicts fixed.
* Bug fixes, including Line breaks fix, and improved security.
* Auto fix the missing options that are essential.
* Optional scroll follow navigation for tabs
* Numerous style conflicts are fixed across multiple wordpress themes.

= 0.7 =
* Display post/posts and pages within Tabs/accordion/dialogs/sliders.
* Mousewheel support and vertical styling for Tabs.
* Dialogs completely styled and ready for action. 
* Template feature for the posts.
* Sliders/dialogs rewritten.
* Various bugfixes.

= 0.5.6 =
* Fix: array_key_exists error when there are no custom themes listed.

= 0.5.5 =
* jQuery UI custom themes, manageable through options page.
* Tabs/accordion events choice - Mouseover/Click(default)
* UI Dialog, some basic support.
* Complete Linking and history.
* Tabs/accordion custom styles were modified to cooperate with the jQuery UI themes.
* Accordion/Tabs - contact form 7 related bug totally fixed - ( Missing submit button )
* Additional fix for preventing thickbox from breaking jQuery UI functionality (originally unrelated to WP UI).

= 0.5.2 =
* Accordion easing effects added.
* Many other options were added to the options page.
* Tab name special characters fix.

= 0.5.1 =
* Fixed "Unable to attach Media - Images to the post with the plugin activated" problem. 
* Fixed the options page contextual help and other documentation.
* License copy added.

= 0.5 =
* The First public release.
* Custom CSS3 styles.
* Uses jQuery 1.6.1 and jQuery UI 1.8.12.
* Added more features to tabs - Nested, AJAX loading etc.
* Plugin now supports Tabs, Accordion, Sliders, Collapsibles.

= 0.1 =
* Plugin scripts rewritten with reusability in mind. IE support, from IE6.


== Upgrade Notice ==

= 0.8.2 =
Save the options & clear the browser & page( if any ) cache. Rest should be Automagic.

= 0.8.1 =
Please save the options manually. Mostly bugfixes relating to styles, dialogs, editor menus.  

= 0.8 =
3.3 compatible. Performance related changes, lots of conflicts fixed. New editor menus and dialogs( > 3.1 only). Split-load and cache options added. Save the options.

= 0.7.5 =
Get/parse/display RSS Feeds, Rewritten documentation, select custom styles as default, multiple post templates. Fixes - TinyMCE, scan styles, linking. Please Save The Options.

= 0.7.4 =
Bug and compatibility fix update. Help/style chooser pages fixed. IE 6/7 spoiler fix. Re-Save the options.

= 0.7.3 =
Pagination, new anywhere loop shortcode, Tons of compatibility fixes, style fix etc. Please save the options.

= 0.7.2 =
Detailed preview of jQuery UI themes & CSS3 styles. Dialog positions, linebreak fix, Buttons for page editor, Improved security. Auto Fix the missing options.

= 0.7 =
Now, load posts into tabs/acc/dialogs/sliders. Mousewheel scrolling thro tabs. Dialogs styles, Vertical tabs. Please resave the options.

= 0.5.6 =
Fixed : array_key_exists error fixed. Please resave the options.

= 0.5.5 =
Multiple jQuery UI custom themes, Linking and history for the tabs, tabs/accordion events. Fixed: Contact form 7 related bug/wpui custom themes - jQuery UI themes compatibility. Choose a jQuery UI theme as default for update.

= 0.5.2 =
A lot of Accordion effects/options added. You can now use special characters in Tab titles. Please update and re save the options.

= 0.5.1 =
**Fixed** the problem, where unable to attach images to a post with the plugin enabled. Important fix.

= 0.5 =
This is the first stable version to be released.


== Demos ==

= Demos =
* [complete demo](http://kav.in/projects/blog/wp-ui-tabs-accordion-sliders-demo/)
* [styles demo ](http://kav.in/projects/blog/wp-ui-css3-styles-demo/)
* [ Including the posts ](http://kav.in/projects/blog/wp-ui-display-posts-wordpress/)

Please **rate** the plugin if you find it useful.


= Credits =
Following scripts have been included with this plugin.

Includes
* jQuery cookie plugin by Klaus Hartl.
* Hashchange event plugin by Ben Alman.
* Mousewheel event plugin by Brandon Aaron.
* Image resizer inspired from work by Jarrod Oberto.

Thanks to respective authors for their great work.