=== Custom Post Widget ===
Contributors: vanderwijk
Author URI: http://www.vanderwijk.com/
Donate link: http://www.vanderwijk.com/wordpress/support/
Tags: custom-post, widget, sidebar, content block, content, block, custom, post, shortcode
Requires at least: 2.9.2
Tested up to: 3.4.1
Stable tag: 1.9.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin enables you to display the content of a custom post type called Content Block in a sidebar widget.

== Description ==

The Custom Post Widget allows you to display the contents of a specific custom post in a widget.

Even though you could use the text widget that comes with the default WordPress install, this plugin has some clear benefits:

* If you are using the standard WordPress text widgets to display content on various areas of your template, this content can only be edited by users with administrator access. If you would like editors to modify the widget content, you can use this plugin to provide them access to the custom posts that provide the content for the widget areas.
* The Custom Post Widget plugin enables users to use the WYSIWYG editor for editing the content and adding images.

This plugin creates a 'content_block' custom post type. You can choose to either display the title on the page or use it to describe the contents and widget position of the content block. Note that these content blocks can only be displayed in the context of the page. I have added 'public' => false to the custom post type which means that it is not accessible outside the page context.

To add content to a widget, drag it to the required position in the sidebar and select the title of the custom post in the widget configuration.

This plugin is ready for localization and Dutch, French, German, Polish and Russian language files are included.

You can find more information about this plugin and a screencast video which shows the plugin in action on the [plugin homepage](http://www.vanderwijk.com/wordpress/wordpress-custom-post-widget/).

== Screenshots ==

1. After activating the plugin a new post type called 'Content Blocks' is added. You can also see the little icon above the WYSIWYG editor that allows you to insert the content block using the shortcode.
2. The widget has a select box to choose the content block. Click on the 'Edit Content Block' link to edit the selected Content Block custom post.

== Installation ==

1. First you will have to upload the plugin to the `/wp-content/plugins/` folder.
2. Then activate the plugin in the plugin panel.
You will see that a new custom post type has been added called Content Block.
3. Type some content for the widget. You can choose to either use the title to describe the of the content on the page, or to display it. Check 'Show Post Title' to display the title on the page.
4. Go to 'Appearance' > 'Widgets' and drag the Content Block widget to the required position in the sidebar.
5. Select a Content Block from the drop-down list.
6. Check the 'Show Post Title' checkbox if you would like to display the title of your Content Block
7. If you are experiencing issues with content being added automatically to your posts (Social media sharing buttons for instance), check the 'Do not apply content filters' checkbox. Use this with caution!
8. Click save.

== Frequently Asked Questions ==

= Why can't I use the default text-widget? =

Of course you can always use the default text widget, but if you prefer to use the WYSIWYG editor or if you have multiple editors and you don't want to give them administrator rights, it is recommended to use this plugin.

= How can I show the content bock on a specific page? =

It is recommended to install the Widget Logic plugin, this will give you complete flexibility on widget placement.

= How can I display the featured image in the widget? =

This plugin has built-in support for the featured image functionality on the edit screen. But to display the image you will have to add the following code to your functions.php:

`function InsertFeaturedImage($content) {
    global $post;
    $original_content = $content;
    if (current_theme_supports('post-thumbnails')) {
        if ('content_block' == get_post_type()) {
            $content = the_post_thumbnail('medium');
            $content .= $original_content;
        }
    }
    return $content;
}
add_filter('the_content', 'InsertFeaturedImage');`

= My social sharing plugin adds buttons to all the Custom Post Widget areas =

If your social media sharing plugin adds buttons to the widget areas you could check the 'Do not apply content filters' checkbox. Note that when this is done, WordPress will also stop adding paragraph tags to your text, so use this setting with caution. It is much better to ask the developer of the social media sharing buttons plugin to correctly use the content filters (see http://pippinsplugins.com/playing-nice-with-the-content-filter/).


== Changelog ==

= 1.9.5 =
Added the option to disable apply_filters on the content to prevent issues with misbehaving plugins. I would have rather not added this, but it appears many plugin developers do not know how to properly use filters (see http://pippinsplugins.com/playing-nice-with-the-content-filter/).

= 1.9.4 =
Corrected a minor bug regarding translation strings.

= 1.9.3 =
Minor bugfix and added the French translation which was created by Alexandre Simard (http://brocheafoin.biz/).

= 1.9.2 =
Now includes Polish language files as created by Kuba Skublicki.

= 1.9 =
The content blocks can now be translated using the WPML plugin, thanks to Jonathan Liuti (http://unboxed-logic.com/).
Thanks to Vitaliy Kaplya (http://www.dasayt.com/) a Russian translation has been added to the plugin.

= 1.8.6 =
Minor bugfix for edit link in widget.

= 1.8.5 =
This release is to fix an issue with the WordPress plugin repository.

= 1.8.4 =
Added edit content block link to the widget editor and changed the 'view content block' message to include a 'manage widgets' link. The 'Draft' and 'Preview' buttons are now hidden via CSS, hopefully this will soon be default WordPress behaviour (see related ticket: http://core.trac.wordpress.org/ticket/18956).
Thanks to Julian Gardner-Hobbs (http://www.hobwebs.com/) for requesting this functionality.

= 1.8.3 -> rolled-back because of some reported issues with social media icons being added to the widget areas =
The widget now emulates the $post loop. This means you can now make use of WordPress functionality such as inserting a [gallery]. Thanks to Jari Pennanen for providing the code.

= 1.8.2 =
Updated German translation and various bugfixes.

= 1.8 =
Added a button above to content editor to make it easier to add the shortcode (no need for looking up the id).

= 1.7 =
This release fixes all the debug error messages Yoast discovered when [reviewing this plugin](http://yoast.com/wp-plugin-review/custom-post-widget/). As requested by Tony Allsopp the option of using the shortcode [content_block id= ] to pull in the content of a content block in a page or post has been added.

= 1.6 =
The Custom Post Widget plugin is now using the more efficient get_post instead of query_posts to display the content block on the page. A code example for this change has been graciously provided by Paul de Wouters.

= 1.5 =
Thanks to Caspar Huebinger the plugin now has its own icon and as requested by Stephen James the author field has been added to the Content Block edit screen.

= 1.4 =
The plugin has been translated into Dutch and German. Hat tip: Caspar H&uuml;binger - glueckpress.com

= 1.3 =
Now the title of the content block is displayed in the admin interface to make it easy to manage the widgets.

= 1.2.1 =
The widget title now uses $before_title and $after_title to generate the appropriate tags to display it on the page. Hat tip: Etienne Proust.

= 1.2 =
Added a checkbox in the widget to make it possible to show the custom post title in the widget area

= 1.1.1 =
Added showposts=-1 to the post query to display more than 10 custom posts in the widget configuration select box.

= 1.1 =
Fixed screenshots for plugin directory

= 1.0 =
First release


== Upgrade Notice ==

= 1.8 =
I would appreciate some feedback on the newly introduced shortcode functionality. Is this useful or not? Any issues found? Thanks!