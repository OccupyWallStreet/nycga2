=== Network Latest Posts ===
Contributors: L'Elite
Donate link: http://laelite.info
Tags: recent posts, shortcode, widget, network, latest posts
Requires at least: 3.0
Tested up to: 3.5
Stable tag: 3.5.4

This plugin allows you to pull all the recent posts from the blogs in your WordPress network and display them in your main site (or internal sites)

== Description ==

This plugin pull the recent posts from all the blogs in your network and displays them in your main site (or any internal site) using shortcodes or widgets.
For further details please visit: http://en.8elite.com/network-latest-posts [English] http://es.8elite.com/network-latest-posts [Espanol] http://fr.8elite.com/network-latest-posts [Francais].

This plugin works with Wordpress 3 Network (multisites) Looking for single install versions? http://single-latest-posts.laelitenetwork.com

== Installation ==

1. Upload `network-latest-posts folder` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. If you want to use the Widget, you can add the Network Latest Posts widget under 'Appearance->Widgets'
4. If you want to use the Shortcode, go to a page or post then click the NLPosts icon (green button in the TinyMCE editor) or use [nlposts] (that's it, seriously!)

== Options ==

= CSS Classes =

* Unordered List:
* 'wrapper_o' => &lt;ul class='nlposts-wrapper nlposts-ulist $wrapper_list_css'&gt;
* 'wtitle_o' =&gt; &lt;h2 class='nlposts-ulist-wtitle'&gt;
* 'item_o' =&gt; &lt;li class='nlposts-ulist-litem'&gt;
* 'content_o' =&gt; &lt;div class='nlposts-container nlposts-ulist-container $nlp_instance'&gt;
* 'meta_o' =&gt; &lt;span class='nlposts-ulist-meta'&gt;
* 'thumbnail_o' =&gt; &lt;ul class='nlposts-ulist-thumbnail thumbnails'&gt;
* 'thumbnail_io' =&gt; &lt;li class='nlposts-ulist-thumbnail-litem span3'&gt;&lt;div class='thumbnail'&gt;
* 'pagination_o' =&gt; &lt;div class='nlposts-ulist-pagination pagination'&gt;
* 'title_o' =&gt; &lt;h3 class='nlposts-ulist-title'&gt;
* 'excerpt_o' =&gt; &lt;ul class='nlposts-ulist-excerpt'&gt;&lt;li&gt;
* Ordered List:
* 'wrapper_o' =&gt; &lt;ol class='nlposts-wrapper nlposts-olist $wrapper_list_css'&gt;
* 'wtitle_o' =&gt; &lt;h2 class='nlposts-olist-wtitle'&gt;
* 'item_o' =&gt; &lt;li class='nlposts-olist-litem'&gt;
* 'content_o' =&gt; &lt;div class='nlposts-container nlposts-olist-container $nlp_instance'&gt;
* 'meta_o' =&gt; &lt;span class='nlposts-olist-meta'&gt;
* 'thumbnail_o' =&gt; &lt;ul class='nlposts-olist-thumbnail thumbnails'&gt;
* 'thumbnail_io' =&gt; &lt;li class='nlposts-olist-thumbnail-litem span3'&gt;
* 'pagination_o' =&gt; &lt;div class='nlposts-olist-pagination pagination'&gt;
* 'title_o' =&gt; &lt;h3 class='nlposts-olist-title'&gt;
* 'excerpt_o' =&gt; &lt;ul class='nlposts-olist-excerpt'&gt;&lt;li&gt;
* Block:
* 'wrapper_o' =&gt; &lt;div class='nlposts-wrapper nlposts-block $wrapper_block_css'&gt;
* 'wtitle_o' =&gt; &lt;h2 class='nlposts-block-wtitle'&gt;
* 'item_o' =&gt; &lt;div class='nlposts-block-item'&gt;
* 'content_o' =&gt; &lt;div class='nlposts-container nlposts-block-container $nlp_instance'&gt;
* 'meta_o' =&gt; &lt;span class='nlposts-block-meta'&gt;
* 'thumbnail_o' =&gt; &lt;ul class='nlposts-block-thumbnail thumbnails'&gt;
* 'thumbnail_io' =&gt; &lt;li class='nlposts-block-thumbnail-litem span3'&gt;
* 'pagination_o' =&gt; &lt;div class='nlposts-block-pagination pagination'&gt;
* 'title_o' =&gt; &lt;h3 class='nlposts-block-title'&gt;
* 'excerpt_o' =&gt; &lt;div class='nlposts-block-excerpt'&gt;&lt;p&gt;

$nlp_instance is replaced by .nlp-instance-X where X is a number o the name of the instance passed via shortcode. $wrapper_list_css and $wrapper_block_css
are replaced by the default values or those passed using the widget form or the shortcode.

= Shortcode Options =

This is an just an example with the default values which means I could have used `[nlposts]` instead, but this will show you how the parameters
are passed. For more examples please visit the Network Latest Post website.

`[nlposts title=NULL
          number_posts=10
          time_frame=0
          title_only=TRUE
          display_type=ulist
          blog_id=NULL
          ignore_blog=NULL
          thumbnail=FALSE
          thumbnail_wh=80x80
          thumbnail_class=NULL
          thumbnail_filler=placeholder
          thumbnail_custom=FALSE
          thumbnail_field=NULL
          thumbnail_url=NULL
          custom_post_type=post
          category=NULL
          tag=NULL
          paginate=FALSE
          posts_per_page=NULL
          display_content=FALSE
          excerpt_length=NULL
          auto_excerpt=FALSE
          excerpt_trail=text
          full_meta=FALSE
          sort_by_date=FALSE
          sort_by_blog=FALSE
          sorting_order=NULL
          sorting_limit=NULL
          post_status=publish
          css_style=NULL
          wrapper_list_css='nav nav-tabs nav-stacked'
          wrapper_block_css=content
          instance=NULL
          random=FALSE
          post_ignore=NULL
]`

* @title              : Widget/Shortcode main title (section title)
* @number_posts       : Number of posts BY blog to retrieve. Ex: 10 means, retrieve 10 posts for each blog found in the network
* @time_frame         : Period of time to retrieve the posts from in days. Ex: 5 means, find all articles posted in the last 5 days
* @title_only         : Display post titles only, if false then excerpts will be shown
* @display_type       : How to display the articles, as an: unordered list (ulist), ordered list (olist) or block elements
* @blog_id            : None, one or many blog IDs to be queried. Ex: 1,2 means, retrieve posts for blogs 1 and 2 only
* @ignore_blog        : It takes the same values as blog_id but in this case this blogs will be ignored. Ex: 1,2 means, display all but 1 and 2
* @thumbnail          : If true then thumbnails will be shown, if active and not found then a placeholder will be used instead
* @thumbnail_wh       : Thumbnails size, width and height in pixels, while using the shortcode or a function this parameter must be passed like: '80x80'
* @thumbnail_class    : Thumbnail class, set a custom class (alignleft, alignright, center, etc)
* @thumbnail_filler   : Placeholder to use if the post's thumbnail couldn't be found, options: placeholder, kittens, puppies (what?.. I can be funny sometimes)
* @thumbnail_custom   : Pull thumbnails from custom fields (true or false), thumbnail parameter must be true
* @thumbnail_field    : Custom field which contains the custom thumbnail URL
* @thumbnail_url      : Custom thumbnail filler URL
* @custom_post_type   : Specify a custom post type: post, page or something-you-invented
* @category           : Category or categories you want to display. Ex: cats,dogs means, retrieve posts containing the categories cats or dogs
* @tag                : Same as categoy WordPress treats both taxonomies the same way; by the way, you can pass one or many (separated by commas)
* @paginate           : Display results by pages, if used then the parameter posts_per_page must be specified, otherwise pagination won't be displayed
* @posts_per_page     : Set the number of posts to display by page (paginate must be activated)
* @display_content    : Display post content instead of excerpt (false by default)
* @excerpt_length     : Set the excerpt's length in case you think it's too long for your needs Ex: 40 means, 40 words (55 by default)
* @auto_excerpt       : If true then it will generate an excerpt from the post content, it's useful for those who forget to use the Excerpt field in the post edition page
* @excerpt_trail      : Set the type of trail you want to append to the excerpts: text, image. The text will be _more_, the image is inside the plugin's img directory and it's called excerpt_trail.png
* @full_meta          : Display the date and the author of the post, for the date/time each blog time format will be used
* @sort_by_date       : Sorting capabilities, this will take all posts found (regardless their blogs) and sort them in order of recency, putting newest first
* @sort_by_blog       : Sort by blog ID
* @sorting_order      : Specify the sorting order: 'newer' means from newest to oldest posts, 'older' means from oldest to newest. asc/desc are used when blog ID is true
* @sorting_limit      : Limit the number of posts to display. Ex: 5 means display 5 posts from all those found (even if 20 were found, only 5 will be displayed)
* @post_status        : Specify the status of the posts you want to display: publish, new, pending, draft, auto-draft, future, private, inherit, trash
* @css_style          : Use a custom CSS style instead of the one included by default, useful if you want to customize the front-end display: filename (without extension), this file must be located where your active theme CSS style is located, this parameter should be used only once by page (it will affect all shorcodes/widgets included in that page)
* @wrapper_list_css   : Custom CSS classes for the list wrapper
* @wrapper_block_css  : Custom CSS classes for the block wrapper
* @instance           : This parameter is intended to differenciate each instance of the widget/shortcode/function you use, it's required in order for the asynchronous pagination links to work
* @random             : Pull random articles
* @post_ignore        : Post ID(s) to ignore (default null) comma separated values ex: 1 or 1,2,3 > ignore posts ID 1 or 1,2,3 (post ID 1 = Hello World)

== Changelog ==

= 3.5.4 =
* Support for date localization (specially in german) using i18n, thanks to Claas Augner for the patch.

= 3.5.3 =
* Fixing line returns when using display_content parameter. Mixing nl2br and do_shortcode to do the job.

= 3.5.2 =
* Replacing nl2br by do_shortcode to fix an incompatibility issue with Vipers shortcodes.

= 3.5.1 =
* Added catch to avoid warnings when NLPosts can't find posts matching your parameters. Now it will display a message letting you know, no posts matching your criteria were found.

= 3.5 =
* Added parameter display_content which allows you to display posts content instead of excerpts, minor bug fixes

= 3.4.6 =
* Added parameter thumbnail_url which you can use to specify a custom thumbnail filler, this parameter must be a URL address

= 3.4.5 =
* Adding missing strings to Norwegian translation, thanks to kkalvaa

= 3.4.4 =
* Replacing language loadtext order to avoid some strings being ignored by translation files, thanks to kkalvaa for spotting this and contributing with the Norwegian Bokmål translation!

= 3.4.3 =
* Fixed bug while using sort_by_date and sort_by_blog, sorting capabilities were not working properly. Thanks to Julien Dizdar for reporting this bug.

= 3.4.2 =
* Fixing typo in $thumbnail_custom variable
* Added German language provided by Claas Augner

= 3.4.1 =
* Added CSS class 'nlposts-siteid-x' to each element so they can be styled depending on which blog they were pulled from

= 3.4 =
* NEW Feature: Ignore posts by ID. Now you can ignore certain posts by their IDs ex: post_ignore=1 ignores all "Hello World" posts

= 3.3.2 =
* Added post title to alt and title tags for thumbnails

= 3.3.1 =
* Fixing Display Blogs and Ignore Blogs lists in the Shortcode form

= 3.3 =
* NEW Feature: order by blog ID. Now you can sort by blog ID using sort_by_blog=true and sorting_order=asc or sorting_order=desc

= 3.2.1 =
* Bug fixed. Excerpts were being taken from content only and not from excerpts fields

= 3.2 =
* NEW Feature added Custom Thumbnail `thumbnail_custom`, `thumbnail_field` which allows you to specify custom fields for thumbnails

= 3.1.7 =
* Fixing a bug when placed before comments forms.

= 3.1.6 =
* Due to an incompatibility issue between the Visual Composer plugin and the WordPress hook strip_shortcodes, NLposts is using regex now.

= 3.1.5 =
* Replacing `wp_enqueue_scripts` by `admin_enqueue_scripts` to solve styling issues in the TinyMCE button

= 3.1.4 =
* Fixed notice for `wp_register_style` when debug has been turned on

= 3.1.3 =
* NEW Feature added `random` allows you to pull random posts from database

= 3.1.2 =
* Register ids changed to better identify NLPosts

= 3.1.1 =
* Patch for fixing `wp_register_sidebar_widget` and `wp_register_widget_control` thanks to cyberdemon8

= 3.1 =
* It's now possible to specify multiple custom types (comma separated)
* Two deprecated functions `register_sidebar_widget` and `register_widget_control` were updated to add the new prefix `wp_` used since WordPress version 2.8

= 3.0.9 =
* Custom post type variable fixed, it was using post_type instead of custom_post_type thanks to ricardoweb for spotting this

= 3.0.8 =
* Adding translation domain to the full meta strings.

= 3.0.7 =
* Fixed excerpt functions, the excerpt_length parameter wasn't pulling the right number of words, if not specified 55 words will be used by default (WordPress defaults)

= 3.0.6 =
* Fixed Shortcode's JavaScript function when used through the TinyMCE editor, there was a problem when using multiple categories or tags. It also inserted the thumbnail_w & thumbnail_h which aren't needed.

= 3.0.5 =
* Added wrapper_list_css & wrapper_block_css, these parameters permit to customize the CSS classes for the wrapper tag
* Fixed minor bug in the Shortcode TinyMCE form which inserted the Submit button to the list of parameters

= 3.0.4 =
* Adding Blog name to the meta info when using Widgets
* Added shortcode form CSS styles

= 3.0.3 =
* Adding Blog name to the meta info

= 3.0.2 =
* Fixing call to the widget class from the shortcode form, the TinyMCE shortcode button should be working now

= 3.0.1 =
* Bug "Problem with 3.0, unexpected T_FUNCTION" Fixed, add_action on line 1092 modified to provide compatibility with PHP versions &lt; 5.3

= 3.0 =
* Network Latest Posts was totally rewritten, it no longer uses Angelo's code. WordPress hooks took its place. All the nasty hackery and workarounds
  are gone.
* Support for RTL installations added.
* Sorting capabilities added, it's now possible to display the latest posts first regardless the blogs they were found.
* Name changed for some parameters to match their functionality.
* Some parameters no longer exist (display_root, wrapo, wrapc) they are no longer useful
* Thumbnail size, class and replacement added
* Display type added, 3 styles by default, it makes it easier for people with limited CSS knowledge to tweak the visual appearance.
* Fixed some bugs in the auto_excerpt function
* CSS style allows you to use your own css file to adapt the output to your active theme (when used it will unload the default styles)
* Instance is used to include multiple instances in the same page as a widget or as a shortcode, fixing the pagination bug which didn't work when used multiple times.
* Widget now includes multi-instance support extending the WP_Widget class, you can added as many times as you want to all your widgetized zones.
* Shortcode button added to the TinyMCE editor, now you just need to fill the form and it will insert the shortcode with the parameters into the post/page content.
* Renamed some functions to avoid incompatibility with other plugins using default function names.
* Main folders and sub-folder installations supported.

= 2.0.4 =
* NEW feature added `auto_excerpt` will generate an excerpt from the post's content
* NEW feature added `full_meta` will display the author's display name, the date and the time when the post was published

= 2.0.3 =
* Excerpt Length proposed by Tim (trailsherpa.com)
* It's possible now to display the posts published in the main blog (network root) using the display_root parameter

= 2.0.2 =
* Bug fix: When using only one category only one article from each blog was displayed. Now it displays the number specified with the `number`
parameter as expected - Thanks to Marcalbertson for spotting this

= 2.0.1 =
* Added missing spaces before "published in" string: Lines 347, 358 & 399 - Spotted by Josh Maxwell

= 2.0 =
* NEW feature added `cat` which allows you to filter by one or more categories - Proposed by Jenny Beaumont
* NEW feature added `tag` which allows you to filter by one or more tags - Proposed by Jenny Beaumont
* NEW feature added `paginate` which allows you to paginate the results using the number parameter as the number of results to display by page
* NEW CSS file added
* NEW img folder added

= 1.2 =
* Fixed the repeated `<ul></ul>` tags for the widget list
* NEW feature added `cpt` which allows you to display a specific post's type (post, page, etc) - Proposed by John Hawkins (9seeds.com)
* NEW feature added `ignore_blog` which allows you to ignore one or various blogs' ids - Proposed by John Hawkins (9seeds.com)
* Added the Domain name with the IDs to the list of blog ids in the Widget
* Some other minor bugs fixed

= 1.1 =
* Fixed the missing `<ul></ul>` tags for the widget list
* NEW feature added `blogid` which allows you to display the latest posts for a specific blog
* NEW feature added `thumbnail` to display the thumbnail of each post
* The widget includes now a list where you can select the blog's id for which you want to display the latest posts

= 1.0 =
* Added Widget option to display excerpt
* Markup improved to make CSS Styling easier
* Added Uninstall hook
* Added Shortcode functionality
* Plugin based in Multisite Recent Posts Widget

== Screenshots ==
1. NLPosts Shortcode in Edit Page
2. NLPosts Insert Shortcode Form
3. NLPosts Shortcode Output
4. Results by Page
5. NLPosts Multi-instance Widget
6. NLPosts Widget: Some Options
7. NLPosts Sidebar Widget Area
8. NLPosts Footer Widget Area
9. NLPosts in RTL Installation
10. NLPosts Shortcode & Widget in RTL

== Frequently Asked Questions ==

= Why did you do this plugin? =
Because I have 3 blogs and I needed a way to display the latest posts from them in the main blog of my Network.

= If I want you to add a new feature, will you do it? =
I like new ideas, but please keep it real and be patient, I try to work as fast as I can but I have also other things to do :).

= What do I need in order to make this plugin work for me? =
Technically nothing, but the pagination feature uses jQuery to load the content without reloading the page. It's prettier that way but it's up
to you (pagination is not Javascript dependant, no jQuery = no fancy loading effects that's all). jQuery is included by default in WordPress, so you don't need to do anything or add anything.

= I can't see the thumbnails =
Your theme have to support thumbnails, just add this to the function.php inside your theme folder:
`add_theme_support('post-thumbnails');`

= OMG this plugin is awesome! I want to buy you a coke and send you a message, where can I do it? =
Please visit my website http://laelite.info if you want to support my work please consider making a donation, even $1 can help me pay my web server. If you have no money, then you can write something nice
about me and my work, then send it to opensource[at]laelite.info

= Is there a plugin like this for single WordPress installations? =
Yes, I've released a version for single installations, check it out http://single-latest-posts.laelitenetwork.com, you can also download the lite version for FREE