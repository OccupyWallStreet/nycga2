=== Section Widget ===
Contributors: oltdev, godfreykfc, enej, ubcdev, ctlt-dev, ctlt-dev
Tags: text, html, shortcode, tabs, tabbed, widget, conditional, section, per page, post type, taxonomies
Requires at least: 3.2
Tested up to: 3.3.1
Stable tag: trunk

Display arbitrary information only on selected sections of your site. Also allows you to easily organize them into tabs in your sidebar.

== Description ==

**Grand Prize winner of WordPress Plugin Competition 2009**

Ever wanted to display a widget only on the front page? Subpages of certain pages? Posts with a certain tag? We've got you covered. With an extremely easy to use interface, you can create your section-specific widget in no time - without going through the frustration of writing PHP code (ala [Widget Logic][1]).

In addition to plain text and HTML, we have added **shortcodes** support into the mix. This means you can easily turn your crazy widget ideas into reality. Need a RSS widget for the posts in the "Movies" category? Yep, [there is a shortcode for that][2]. Show your AdSense ads only on the front page? [There is a shortcode for that too][3] - not to mention [tag clouds][4], [Paypal][5], [Amazon][6]... you name it. Or throw more CMS hotness into your sidebar with our [conditional custom fields][7] shortcodes. Checkout the [screenshots][8] for more inspirations.

But before you hit the download button, we have saved a surprise for you. In order to help you fit all those insane ideas into your already crowded sidebar, we have decided to add **tabs** to the equation. With an intuitive drag-and-drop interface, creating your own tabbed widget is completely effortless - even for your grandparents. (See the [screenshots][8] for details.) To bring this to the next level, we have bundled *25 (!) switchable themes*  (powered by the [jQuery UI][9] project) with the plugin - and we even included a live preview in the settings page! And of course, you can always roll your own theme to suit the design of your site. (A lite version with the 2 basic themes is also available [here][10].)

With all those awesome features, you should definitely download it and try it out. If you still cannot find a need for this, you're probably using WordPress the wrong way ;) Let us know what you think, drop us a line at the [forums][11] - we would love to hear about your creative ways of using this plugin!


= Built-in Conditionals =

In this version, you can freely mix and match these predefined rules which gives you control of where the widget should be displayed:

*   Everywhere on your site *(new)*
*   The front page
*   The posts page *(new)*
*   All posts
*   All author pages
*   All or selected pages and subpages
*   Pages or posts with comments enabled
*   Pages or posts belongs to selected categories
*   Pages or posts with selected tags
*   All *(new)* or selected category archive pages
*   All *(new)* or selected tags archive pages
*   All date-based archive pages
*   Search results page *(new)*
*   "404" not found page *(new)*
* 	Basic Support for Taxonomies
*   Basic Support for Post Types


**Please note: JavaScript is required for the widget interface to display correctly.**

**This plugin will only run on Wordpress 3.3+.**

 [1]: http://wordpress.org/extend/plugins/widget-logic/
 [2]: http://wordpress.org/extend/plugins/rss-shortcode/
 [3]: http://wordpress.org/extend/plugins/smart-ads/
 [4]: http://wordpress.org/extend/plugins/template-tag-shortcodes/
 [5]: http://wordpress.org/extend/plugins/paypal-shortcodes/
 [6]: http://wordpress.org/extend/plugins/amazon-widgets-shortcodes/
 [7]: http://wordpress.org/extend/plugins/conditional-custom-fields-shortcode/
 [8]: http://wordpress.org/extend/plugins/section-widget/screenshots/
 [9]: http://jqueryui.com/
 [10]: http://wordpress.org/extend/plugins/section-widget/download/
 [11]: http://wordpress.org/tags/section-widget?forum_id=10#postform


== Installation ==

1. Extract the zip file and drop the contents in the wp-content/plugins/ directory of your WordPress installation
2. Activate the Plugin from Plugins page
3. If you plan to use the tabbed section widget with our built-in themes, go to the **Section Widget** settings page (under the **Appearance** admin menu) and setup a CSS scope. Let us detect that setting for you if you are not sure what to do - simply click on the help link besides the text field and follow the instructions there. (Don't forget to save your changes!)


== Frequently Asked Questions ==

**How do I switch themes?**

Go to the **Section Widget** settings page (under the **Appearance** admin menu). Select any themes from the drop-down. Click "Preview" to show the live preview.

**I cannot see the widget!!!**

Did you forget to set up the conditions for the widget to display? Try checking "Everywhere" and start from there. If you still do not see the widget, there is probably no sidebar on that page. Try adding "?swt-scope-test" to the end of your page's URL (e.g. http://www.myblog.com/?swt-scope-test) - if you have at least one tabbed section widget activated and you do not see "Section Widget Scope Test" on your page, there is probably something wrong with your WordPress theme.

**Why does the tabbed section widget looks so weird on my site?**

Make sure you have set a correct CSS scope in the **Section Widget** settings page (under the **Appearance** admin menu). Let us detect that setting for you if you are not sure what to do - simply click on the help link besides the text field and follow the instructions there. On the other hand, if your widget looks stretched out, try checking the "Height fix" checkbox in the settings page (especially if you're using the default WordPress theme). Finally, make sure you have JavaScript enabled in your browser.

**Can I use shortcodes in the widget content?**

Yes. However, please beware some shortcodes (for example our [conditional custom fields][1] shortcodes) will only work when an individual pages or posts is being displayed, not on an archive pages. This is because they require information (e.g. custom fields) that are attached to individual pages/posts.

**Can I use PHP code in the content?**

By default, no. But if you really want to do that, [there is a shortcode for that][2]. ;)

**Can I use HTML in the content?**

Yep.

**Can I use JavaScript in the content?**

See below.

**Is the HTML code filtered?**

It depends. Unless the user who edited the widget has the [unfiltered\_html][3] capabilities (this means Administrators and Editors on the default settings), all "non-safe" HTML elements (particularly JavaScript) will be stripped.

**What does "Display title" do?**

When checked, your widget's title will be displayed before the its content just like the regular text widget. When unchecked, the title will be hidden which gives you more flexibility if you need to style the widget manually.

**How should I style the widget?**

If you have access to the theme CSS files, you can wrap your content in an id (or give it a CSS class), and then style it from there. Otherwise, you can always use inline CSS. For information on styling the tabbed widget, please refer to the [jQuery UI theming guide][4].

**The themes are taking up too much space!**

You can remove any unneeded themes but deleting the corresponding folder in the "section-widget/themes" folder. However, please **do not** delete the "Base" theme.

 [1]: http://wordpress.org/extend/plugins/conditional-custom-fields-shortcode/
 [2]: http://wordpress.org/extend/plugins/php-shortcode/
 [3]: http://codex.wordpress.org/Roles_and_Capabilities#unfiltered_html
 [4]: http://jqueryui.com/docs/Theming


== Screenshots ==
1. Easy to use widget interface

2. Selecting individual pages

3. Selecting individual categories

4. Creating tabs is fun - you can even drag-and-drop to reorder them

5. How it looks on an actual page

6. Switching themes - with live preview

7. (Ideas) Giving your confused visitors a helping hand

8. (Ideas) Save space by combining your navigations (Tag cloud and Category list powered by [Template Tag Shortcode][1], pages list by our own **Subpages Navigation** plugin - coming soon!)

9. (Ideas) Or go wild with our [Conditional Custom Fields Shortcode][2]

 [1]: http://wordpress.org/extend/plugins/template-tag-shortcodes/
 [2]: http://wordpress.org/extend/plugins/conditional-custom-fields-shortcode/


== Changelog ==

= Version 3.1 =
* Added support for post types
* Added support for taxonomies
* Removed js loading on the admin side to minimal
* The plugin should be translatable now


= Version 3.0.4 =
*   Upgraded to OLT Checklist library v1.1.4
   *   Fixed bug regarding tag archives

= Version 3.0.3 =
*   Upgraded to OLT Checklist library v1.1.3
   *   New "Posts page" option
   *   JavaScript and CSS are loaded in admin area only
   *   Less specific CSS selectors
*   Load tabbed widget JavaScript and CSS are only if a tabbed section widget has been added to the sidebar

= Version 3.0.2 =
*   Upgraded to OLT Checklist library v1.1.2 (Improved Windows support)

= Version 3.0.1 =
*   Fixed an accidental occurrence of <? ... ?> PHP shortcode
*   Minified all JS and CSS
*   Upgraded to OLT Checklist library v1.1.1

= Version 3.0 =
*   New conditionals - everywhere, all categories/tags archive pages, search result pages and the not found page
*   Added shortcode support
*   Added the tabbed widget

= Version 2.03 =
*   Added install instructions for Wordpress 2.7

= Version 2.02 =
*   Fixed minor JavaScript bug

= Version 2.0 =
*   First public release