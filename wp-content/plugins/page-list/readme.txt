=== Page-list ===
Contributors: webvitaly
Plugin URI: http://web-profile.com.ua/wordpress/plugins/page-list/
Tags: page, page-list, pagelist, sitemap, subpages, siblings
Author URI: http://web-profile.com.ua/wordpress/
Requires at least: 3.0
Tested up to: 3.4.1
Stable tag: 3.8

[pagelist], [subpages], [siblings] and [pagelist_ext] shortcodes

== Description ==

= shortcodes: =

* **[pagelist]** - hierarchical tree of all pages on site (useful to show sitemap of the site);
* **[subpages]** - hierarchical tree of subpages to the current page;
* **[siblings]** - hierarchical tree of sibling pages to the current page;
* **[pagelist_ext]** - list of pages with featured image and with excerpt (useful to show list of products with images);

= aditional parameters examples: =

* `[pagelist depth="2" child_of="4" exclude="6,7,8"]`
* `[pagelist_ext child_of="4" exclude="6,7,8" image_width="50" image_height="50"]`
* [all Page-list params](http://wordpress.org/extend/plugins/page-list/other_notes/)

[Page-list plugin page](http://web-profile.com.ua/wordpress/plugins/page-list/)

= useful plugins: =
* ["Iframe" - embed iframe with shortcode](http://wordpress.org/extend/plugins/iframe/)
* ["Login Logout" - default Meta widget replacement](http://wordpress.org/extend/plugins/login-logout/) - default Meta widget replacement
* ["Filenames to latin" - sanitize filenames to latin during upload](http://wordpress.org/extend/plugins/filenames-to-latin/)

== Other Notes ==

= Parameters for [pagelist], [subpages] and [siblings]: =
* **[pagelist]** - by default shows list of all pages as the hierarchical list;
* **[subpages]** - by default shows list of subpages to the current page as the hierarchical list;
* **[siblings]** - by default shows list of sibling pages to the current page as the hierarchical list;
* **depth** - means how many levels in the hierarchy of pages are to be included in the list, by default depth is unlimited (depth=0), but you can specify it like this: `[pagelist depth="3"]`; If you want to show flat list of pages (not hierarchical tree) you can use this shortcode: `[pagelist depth="-1"]`;
* **child_of** - if you want to show subpages of the specific page you can use this shortcode: `[pagelist child_of="4"]` where `4` is the ID of the specific page; If you want to show subpages of the current page you can use this shortcodes: `[subpages]` or `[pagelist child_of="current"]` or `[pagelist child_of="this"]`; If you want to show sibling pages of the current page you can use this shortcodes: `[siblings]` or `[pagelist child_of="parent"]`;
* **exclude** - if you want to exclude some pages from the list you can use this shortcode: `[pagelist exclude="6,7,8"]` where `exclude` parameter accepts comma-separated list of Page IDs; You may exclude current page with this shortcode: `[pagelist exclude="current"]`;
* **exclude_tree** - if you want to exclude the tree of pages from the list you can use this shortcode: `[pagelist exclude_tree="7,10"]` where `exclude_tree` parameter accepts comma-separated list of Page IDs (all this pages and their subpages will be excluded);
* **include** - if you want to include certain pages into the list of pages you can use this shortcode: `[pagelist include="6,7,8"]` where `include` parameter accepts comma-separated list of Page IDs;
* **title_li** - if you want to specify the title of the list of pages you can use this shortcode: `[pagelist title_li="<h2>List of pages</h2>"]`; by default there is no title (title_li="");
* **number** - if you want to specify the number of pages to be included into list of pages you can use this shortcode: `[pagelist number="10"]`; by default the number is unlimited (number="");
* **offset** - if you want to pass over (or displace) some pages you can use this shortcode: `[pagelist offset="5"]`; by default there is no offset (offset="");
* **meta_key** - if you want to include the pages that have this Custom Field Key you can use this shortcode: `[pagelist meta_key="metakey" meta_value="metaval"]`;
* **show_date** - if you want to show the date of the page you can use this shortcode: `[pagelist show_date="created"]`; you can use this values for `show_date` parameter: created, modified, updated;
* **menu_order** - if you want to specify the column by what to sort you can use this shortcode: `[pagelist sort_column="menu_order"]`; by default order columns are `menu_order` and `post_title` (sort_column="menu_order, post_title"); you can use this values for `sort_column` parameter: post_title, menu_order, post_date (sort by creation time), post_modified (sort by last modified time), ID, post_author (sort by the page author's numeric ID), post_name (sort by page slug);
* **sort_order** - if you want to change the sort order of the list of pages (either ascending or descending) you can use this shortcode: `[pagelist sort_order="desc"]`; by default sort_order is `asc` (sort_order="asc"); you can use this values for `sort_order` parameter: asc, desc;
* **link_before** - if you want to specify the text or html that precedes the link text inside the link tag you can use this shortcode: `[pagelist link_before="<span>"]`; you may specify html tags only in the `HTML` tab in your Rich-text editor;
* **link_after** - if you want to specify the text or html that follows the link text inside the link tag you can use this shortcode: `[pagelist link_after="</span>"]`; you may specify html tags only in the `HTML` tab in your Rich-text editor;
* **class** - if you want to specify the CSS class for list of pages you can use this shortcode: `[pagelist class="listclass"]`; by default the class is empty (class="");

= Parameters for [pagelist_ext]: =
* **[pagelist_ext]** - by default shows list of subpages to current page; but if there is no subpages than all pages will be shown;
* **show_image** - show or hide featured image `[pagelist_ext show_image="0"]`; "show_image" have higher priority than "show_first_image"; by default: show_image="1";
* **show_first_image** - show or hide first image from content if there is no featured image `[pagelist_ext show_first_image="1"]`; by default: show_first_image="0";
* **show_title** - show or hide title `[pagelist_ext show_title="0"]`; by default: show_title="1";
* **show_content** - show or hide content `[pagelist_ext show_content="0"]`; by default: show_content="1";
* **more_tag** - if you want to output all content before and after more tag use this shortcode: `[pagelist_ext more_tag="0"]`; this parameter does not add "more-link" to the end of content, it just cut content before more-tag; "more_tag" parameter have higher priority than "limit_content"; by default the more_tag is enabled (more_tag="1") and showing only content before more tag;
* **limit_content** - content is limited by "more-tag" if it is exist or by "limit_content" parameter `[pagelist_ext limit_content="100"]`; by default: limit_content="250";
* **image_width** - width of the image `[pagelist_ext image_width="80"]`; by default: image_width="50";
* **image_height** - height of the image `[pagelist_ext image_height="80"]`; by default: image_height="50";
* **child_of** - if you want to show subpages of the specific page you can use this shortcode: `[pagelist_ext child_of="4"]` where `4` is the ID of the specific page; by default it shows subpages to the current page;
* **parent** - if you want to show subpages of the specific page only you can use this shortcode: `[pagelist_ext parent="4"]` where `4` is the ID of the specific page and the depth will be only one level; by default parent="-1" and depth is unlimited;
* **sort_order** - if you want to change the sort order of the list of pages (either ascending or descending) you can use this shortcode: `[pagelist_ext sort_order="desc"]`; by default: sort_order="asc"; you can use this values for `sort_order` parameter: asc, desc;
* **sort_column** - if you want to specify the column by what to sort you can use this shortcode: `[pagelist_ext sort_column="menu_order"]`; by default order columns are `sort_column` and `post_title` (sort_column="menu_order, post_title"); you can use this values for `sort_column` parameter: post_title, menu_order, post_date (sort by creation time), post_modified (sort by last modified time), ID, post_author (sort by the page author's numeric ID), post_name (sort by page slug);
* **hierarchical** - display sub-pages below their parent page `[pagelist_ext hierarchical="0"]`; by default: hierarchical="1";
* **exclude** - if you want to exclude some pages from the list you can use this shortcode: `[pagelist_ext exclude="6,7,8"]` where `exclude` parameter accepts comma-separated list of Page IDs;
* **exclude_tree** - if you want to exclude the tree of pages from the list you can use this shortcode: `[pagelist_ext exclude_tree="7,10"]` where `exclude_tree` parameter accepts comma-separated list of Page IDs (all this pages and their subpages will be excluded);
* **include** - if you want to include certain pages into the list of pages you can use this shortcode: `[pagelist_ext include="6,7,8"]` where `include` parameter accepts comma-separated list of Page IDs;
* **meta_key** - if you want to include the pages that have this Custom Field Key you can use this shortcode: `[pagelist_ext meta_key="metakey" meta_value="metaval"]`;
* **authors** - only include the pages written by the given author(s) `[pagelist_ext authors="6,7,8"]`;
* **number** - if you want to specify the number of pages to be included into list of pages you can use this shortcode: `[pagelist_ext number="10"]`; by default the number is unlimited (number="");
* **offset** - if you want to pass over (or displace) some pages you can use this shortcode: `[pagelist_ext offset="5"]`; by default there is no offset (offset="");
* **post_type** - `[pagelist_ext post_type="page"]`;
* **post_status** - `[pagelist_ext post_status="publish"]`;
* **class** - if you want to specify the CSS class for list of pages you can use this shortcode: `[pagelist_ext class="listclass"]`; by default the class is empty (class="");
* **strip_tags** - if you want to output the content with tags use this shortcode: `[pagelist_ext strip_tags="0"]`; by default the strip_tags is enabled (strip_tags="1");
* **strip_shortcodes** - if you want to output the content with shortcode use this shortcode: `[pagelist_ext strip_shortcodes="0"]`; by default the strip_shortcodes is enabled (strip_shortcodes="1") and all registered shortcodes are removed;
* **show_child_count** - if you want to show child count you can use this shortcode: `[pagelist_ext show_child_count="1"]`; by default the child_count is disabled (show_child_count="0"); If show_child_count="1", but count of subpages=0, than child count is not showing;
* **child_count_template** - if you want to specify the template of child_count you can use this shortcode: `[pagelist_ext show_child_count="1" child_count_template="Subpages: %child_count%"]`; by default child_count_template="Subpages: %child_count%";
* **show_meta_key** - if you want to show meta key you can use this shortcode: `[pagelist_ext show_meta_key="your_meta_key"]`; by default the show_meta_key is empty (show_meta_key=""); If show_meta_key is enabled, but meta_value is empty, than meta_key is not showing;
* **meta_template** - if you want to specify the template of meta you can use this shortcode: `[pagelist_ext show_meta_key="your_meta_key" meta_template="Meta: %meta%"]`; by default meta_template="%meta%";


== Frequently Asked Questions ==

= How to show the list of posts? =

To show list of posts you can use [List Category Posts](http://wordpress.org/extend/plugins/list-category-posts/other_notes/) plugin.

= On what functions shortcodes are based? =

Shortcodes [pagelist], [subpages], [siblings] are based on [wp_list_pages('title_li=')](http://codex.wordpress.org/Template_Tags/wp_list_pages) function.
Shortcode [pagelist_ext] is based on [get_pages()](http://codex.wordpress.org/Function_Reference/get_pages) function.

= What is the difference between [pagelist], [subpages] and [siblings]? =

Shortcodes [pagelist], [subpages] and [siblings] accept the same parameters. The only difference is that [subpages] and [siblings] not accept  `child_of` parameter, because [subpages] shows subpages to the current page and [siblings] shows subpages to the parent page.

= How to create sitemap.xml? =
To create sitemap.xml you can use [Google XML Sitemaps](http://wordpress.org/extend/plugins/google-sitemap-generator/) plugin.

= Is there "more-link" feature in the plugin? =
No, there is no "more-link" feature in the plugin. Because "more-link":

* **not good for SEO.** Nobody will search your site with the word "more". "rel=nofollow" will not solve it too.
* **not good for usability.** There is already link on title and "more-link" is an extra no needed element on page. If user cannot understand that the title is the link, than there is a problem in css styles and not in plugin's templates.

I am trying to keep plugin's code and list of pages on the sites light and clean.
But if you still need "more-link" feature and you will add it by yourself, than you should also change the plugin version to ver.100 (for example) to avoid updating of the plugin, what could override and delete your code.

== Screenshots ==

1. [pagelist] shortcode
2. [pagelist_ext] shortcode

== Changelog ==

= 3.8 =
* fixed default [pagelist_ext] behaviour - showing all pages if there is no subpages

= 3.7 =
* executing shortcodes in [pagelist_ext  strip_shortcodes="0"] in content

= 3.6 =
* fixing bug with shortcode in sidebar - shortcode in comment start to execute

= 3.5 =
* showing all pages for [pagelist_ext child_of="0"] shortcode

= 3.4 =
* remove esc_attr() from title in [pagelist_ext] shortcode

= 3.3 =
* rename "get_first_image" function to "page_list_get_first_image" for avoiding conflicts

= 3.2 =
* fixed bug with "more_tag" and non english chars

= 3.1 =
* fixed bug with empty image in "show_first_image" parameter
* added "more_tag" higher priority than "limit_content" (thanks to BobyDimitrov)

= 3.0 =
* added "show_first_image" parameter for showing first image from content if there is no featured image

= 2.9 =
* added "more_tag" parameter and more tag support
* hiding password protected content of the pages

= 2.8 =
* added "strip_shortcodes" parameter

= 2.7 =
* make excerpt link if there is no title

= 2.6 =
* fixed [pagelist_ext] "parent" parameter

= 2.5 =
* adding spaces between lines when tags are stripped in [pagelist_ext]

= 2.4 =
* escaping attributes in title in [pagelist_ext]

= 2.3 =
* fixed [pagelist_ext] with showing excerpt of the page if it is not empty, else showing content

= 2.2 =
* fixed offset parameter

= 2.1 =
* fixed number parameter

= 2.0 =
* fixed crash bug with [pagelist_ext] if theme does not have thumbnail feature

= 1.9 =
* added show_child_count parameter
* added show_meta_key parameter

= 1.8 =
* added screenshots
* improved parameter parsing

= 1.7 =
* added strip_tags parameter

= 1.6 =
* improved [pagelist_ext] shortcode: added content to list, added toggle show and limit content parameters

= 1.5 =
* added [pagelist_ext] shortcode - list of pages with featured image

= 1.4 =
* added exclude="current" parameter

= 1.3.0 =
* added class to ul elements by default
* added "class" option (thanks to Arvind)

= 1.2.0 =
* added [subpages] and [siblings] shortcodes

= 1.0.0 =
* initial release

== Installation ==

1. Install and activate the plugin on the Plugins page
2. Add shortcodes to pages: `[pagelist]`, `[subpages]`, `[siblings]`, `[pagelist_ext]`
