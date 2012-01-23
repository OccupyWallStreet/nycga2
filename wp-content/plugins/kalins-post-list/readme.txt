=== Kalin's Post List ===
Contributors: kalinbooks
Tags: post, widget, shortcode, pages, table of contents, related posts, links, automatic
Requires at least: 3.0
Tested up to: 3.1
Stable tag: trunk

Creates a widget, shortcode or PHP snippet for inserting dynamic, highly customizable lists of posts or pages such as related posts or table of contents into your post content or theme.

== Description ==

<p>
Creates a widget, shortcode or PHP snippet for inserting dynamic, highly customizable lists of posts or pages such as related posts or table of contents into your post content or theme.
</p>

<p>
Comes with 11 different presets, such as a list in a dropdown, a list within a CSS table, a footer-style list, a standard bulleted list, and a recent images list. You can also create an infinite number of your own presets with totally custom HTML so you can have different kinds of lists all over your website.
</p>

<p>
Comes with 22 "internal shortcodes" (many with parameters for even more customization) that you can use to add post-specific information to each item in your list. This include the basics like title, author, permalink, date (with custom formatting), thumbnail, as well as some fancier ones like post meta data, comments, categories, tags, and a PHP function shortcode to allow you to inject your own PHP scripts.
</p>

<p>
Options:
Choose post type (includes custom posts), and the number of posts to show. There are 16 options for "order by", and an ascending/descending selector. You can also choose a post parent (e.g. list children of current page). Then you can select the categories and/or tags from which you'd like to show, or you can base this option off the current post's categories or tags (use like a related posts plugin). You may also choose to require that every post contain every category or tag. Then, of course, the HTML of the list and the information to actually show is completely customizable. Finally you can choose whether or not to include the current post on which the list is sitting.
</p>

<p>
Example usage at: http://kalinbooks.com/post-list-wordpress-plugin/examples/
If you have a cool or unique example of this plugin in use, please post a link on this page.
</p>

<p>
Plugin by Kalin Ringkvist at http://kalinbooks.com/
</p>

<p>
Plugin URL: http://kalinbooks.com/post-list-wordpress-plugin/
Post a message if you find any bugs, issues or have a feature request and I will do my best to accommodate.
</p>

== Installation ==

1. Unzip `kalins-post-list.zip` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to the Post List settings menu to get shortcodes for the default preset configurations or create and save your own configurations.

Note: May require PHP 5.2 and Wordpress 3.1 (hasn't been tested on older versions)

== Screenshots ==

1. The settings menu where you create and adjust the preset configurations which you then refer to by name in the shortcode. Not shown: parent page combobox (shows up when you select page or custom post type)


== Changelog ==

= 0.7 =
* First version. Beta. 

= 1.0 =
*fixed post_excerpt internal shortcode
*added PHP snippet generation

= 1.0.1 =
*Bug fix. Plugin no longer destroys other admin help menus.

= 2.0 =
*added Custom post type support
*None post support (you can now use the plugin to simply insert plain HTML. Shortcodes here refer to current page)
*support for listing pages/custom posts based on parent page (including current page)
*post thumbnail/featured image shortcode
*shortcode for link to page/post PDF (requires PDF Creation Station plugin)
*format parameter for total customization of all date/time shortcodes
*length parameter to [post_excerpt] shortcode
*offset parameter for [item_number] shortcode to start count at something other than 1
*shortcodes now show nothing if no results instead of broken/empty list
*fail gracefully when incorrect preset param entered (shows error if admin, nothing if regular user)
*improved handling of HTML-conflicting characters
*strip shortcodes out before showing excerpts

= 2.0.1 =
*Emergency bug fix: plugin no longer throws error when theme does not support post thumbnails.

= 2.0.2 = 
*changed htmlentities() call to htmlspecialchars() cuz I was seeing issues with excerpts not having special characters converted properly

= 3.0 =
*added widget for easy adding of any Post List to your sidebar
*added preview to settings page so you can see what the list will look like as you're building it
*added post comments shortcode. Includes easy way for PHP coders to fully customize the display
*added post parent shortcode
*added post category(s) shortcode
*added post tags shortcode
*added post_meta shortcode for post's custom fields
*save function now fails gracefully if you forget to enter a preset name
*shortcodes placed into 'before' and 'after' textfields now convert, based on the current page
*added increment param to item_number shortcode for counting by something other than 1 
*added 'require all selected tags/categories' option, which allows you to require that every post include all the cats or tags you've selected
*added php_function shortcode to allow custom PHP injection

= 3.1 =
*removed contextual help and added a link to the same help page on my website. this help menu was causing a problem with a small number of users who had XML support issues in their PHP installation. The help page is still available in the plugin source files
*added [post_content] shortcode back into the documentation after I accidentally deleted it
*removed global $post object from comments callback. Fixes bug where an extra post was being added to the page when this shortcode was used
*Did same thing for php_function callback to fix the same issue. using php_function now requires you to pass in the necessary information through the shortcode parameter
*removed a bunch of orderby options because they had stopped working in recent WordPress core upgrades

== Upgrade Notice ==

= 0.7 =
First version. Beta.

= 1.0 =
post_excerpt shortcode should work properly now and anyone familiar with themes or PHP can now insert a simple auto-generated PHP snippet into their theme

= 1.0.1 =
Sorry about all those help menus my plugin was killing before this

= 2.0 =
Lots of new features.

= 2.0.1 =
Hotfix for a bug that killed everything if the theme did not have thumbnail support

= 2.0.2 =
Hotfix for some character conversion issues with excerpts

= 3.0 = 
New widget feature. New live preview feature. Several new shortcodes for outputting even more post info.

= 3.1 =
*removed some orderby options, though they should be some of the least used options. 
Changed [php_function] to take a $post parameter as well as its optional parameter so if you're using this feature, you will need to update your functions

== About ==

If you find this plugin useful please pay it forward to the community, or visit http://kalinbooks.com/ and check out some of my science fiction or political writings.

