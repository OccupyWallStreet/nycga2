=== BP My Home ===
Contributors: imath
Donate link: http://imath.owni.fr/
Tags: BuddyPress, widget, home, dashboard, members
Requires at least: 3.2
Tested up to: 3.2.1
Stable tag: 1.2.2

BP My Home makes it possible to add moveable and collapsible widgets to BuddyPress Member's home.

== Description ==

BP My Home requires <a href="http://buddypress.org/">BuddyPress</a> and adds to it the ability to add moveable and collapsible custom widgets to Member's home (siteurl/members/name_of_user).
Logged in users can see their Home and choose in their settings area the widgets they want to use. They can also set their Home to be the homepage of the website.

5 widgets comes with this plugin (My Feeds, My Notepad, My Comments, My Bookmarks and @Georglob's Latest Posts), but i also added an example widget if some of you want to build their own ;)

The Administrator of the website / network of blogs can configure the available widgets from the BuddyPress submenu BPMH Manager in the WordPress backend. He can also upload new widgets in a zip format. Finally, he can enable the bookmarking of posts or pages from his website or network of blogs in the bookmark widget and can give the ability for a user to add a feed directly in his My Feed Widget.

In order to add the feed feature, the bpmh rss tag have to be added to the template files (for instance : single.php, category.php, index.php, search.php, archive.php ...) of the active theme, to do so simply add the following code : &lt;?php if(function_exists('the_bpmh_rss_button')) the_bpmh_rss_button();?&gt;.

This plugin is available in French and English.

I simply love the WordPress Dashboard, so i found interesting to share something approaching with members on the frontend. 

== Installation ==

You can download and install BP My Home using the built in WordPress plugin installer. If you download BP My Home manually, make sure it is uploaded to "/wp-content/plugins/bp-my-home/".

Activate BP My Home in the "Plugins" admin panel using the "Network Activate" (or "Activate" if you are not running a network) link.

== Frequently Asked Questions ==

= Is BP My Home BuddyPress 1.5 ready ? =

Yes !

= Is it possible to add a WordPress sidebar widget ? =

It is as explained <a href="http://buddypress.org/community/groups/bp-my-home/forum/topic/where-is-the-link-to-myhome-in-front-end/#post-81398">here</a>, but it is not the goal of this plugin.

= What is the purpose of the My Example widget ? =

It explains how to build your own widgets.

= How can i modify the 'my-home' template page ? =

In your wp-content/themes/active_theme/ , if you add a folder my-home-tpl containing a copy of my-home.php file, then you can modify it and your template will automatically display instead of the one in wp-content/plugins/bp-my-home/includes/templates/my-home-tpl

= How can i replace the rss link in BuddyPress activity/groups pages with the BP My Home RSS subscribing tooltip ? =

In your wp-content/themes/active_theme/ , add the activity folder and the groups/single folders. Copy and paste BuddyPress bp-default/activity/index.php in your theme's activity folder and do the same with bp-default/groups/single/activity.php in your theme's groups folder.

Then edit the files this way :

1. for activity/index.php on line 60, replace <i>&lt;a href="&lt;?php bp_sitewide_activity_feed_link() ?&gt;" title="RSS Feed"&gt;&lt;?php _e( 'RSS', 'buddypress' ) ?&gt;&lt;/a&gt;</i> by <b>&lt;?php if(function_exists('the_bpmh_rss_button')) the_bpmh_rss_button("Activity", bp_get_sitewide_activity_feed_link());?&gt;</b>

2. for groups/single/activity.php on line 3, replace <i>&lt;a href="&lt;?php bp_group_activity_feed_link() ?&gt;" title="RSS Feed"&gt;&lt;?php _e( 'RSS', 'buddypress' ) ?&gt;&lt;/a&gt;</i> by <b>&lt;?php if(function_exists('the_bpmh_rss_button')) the_bpmh_rss_button("Activity", bp_get_group_activity_feed_link());?&gt;</b>

= If you have more questions =

You can find more answers <a href="http://imath.owni.fr/tag/bp-my-home/">here</a>

== Screenshots ==

1. BPMH Manager.
2. the My Home page of a member.
3. the My Settings page of a member.
4. Collapsible and moveable, just like in WordPress Dashboard !
5. Add to My Bookmarks and Add to My Feeds examples.
6. My comments and My Notepad widgets

== Changelog ==

= 1.2.2 =
* BP My Home will run on BuddyPress 1.2.9+ and <b>1.5</b> !
* BP My Home is now the default component of the 'siteurl/members/user' area
* fixing the rss widget bug when using the select box to change feed

= 1.2.1 =
* fixing the bug when viewing other member area (BP My Home is no more the default component of 'siteurl/members/user' area)

= 1.2 =
* new widget to display the latest posts of the blogs
* BP My Home is now the default component of the 'siteurl/members/user' area
* It's now possible to use this tag &lt;?php if(function_exists('the_bpmh_bkmks_tag')) the_bpmh_bkmks_tag() ;?&gt; to display the "Add to my bookmarks" link on page or post.
* Widget developers can add translations to widgets using the hook add_action( 'load_widget_language_files', 'your_custom_get_locale_function' ); (check bpmh_example for more infos)
* I also added 4 hooks so you can easily add content above or under the user widgets or above or under the user settings :
	* add_action ('bp_my_home_before_widgets', 'your_function_to_add_content_above_widgets'); 
	* add_action ('bp_my_home_after_widgets', 'your_function_to_add_content_under_widgets'); 
	* add_action ('bp_my_home_before_widgets_setting', 'your_function_to_add_content_above_settings'); 
	* add_action ('bp_my_home_after_widgets_setting', 'your_function_to_add_content_under_settings');

= 1.1.1 =
* fixes php warning message bug
* fixes add to my rss widget bug
* fixes notepad widget special char bug
* new widget added : My Comments

= 1.1 =
* From the BPMH Manager submenu (in the WordPress backend - BuddyPress menu), you can activate the option to display a subscribe tooltip for your rss feed to allow members to add them in their widget My Feeds
* From the BPMH Manager submenu, you can activate the option to automatically add a link in the blog(s) posts or pages in order to let your members add them to their widget My Bookmarks
* it is now possible to directly upload a zip archive of a widget from the BPMH Manager submenu
* new widget added : My Notepad

= 1.0 =
* Plugin birth..

== Upgrade Notice ==

= 1.2.2 =
Before upgrading, you can back up the widgets folder up (wp-content/uploads/bpmh-widgets). After upgrade, you will have to upgrade the widgtes from the BPMH Manager

= 1.2.1 =
if you upgrade from 1.1.1 or lower versions :
Very Important ! Before upgrading, make sure to back up the wp-content/plugins/bp-my-home/widgets folder if you uploaded your own widgets there. After the upgrade, you will be able to put the widgets you built into the wp-content/uploads/bpmh-widgets directory.

= 1.2 =
Very Important ! Before upgrading, make sure to back up the wp-content/plugins/bp-my-home/widgets folder if you uploaded your own widgets there. After the upgrade, you will be able to put the widgets you built into the wp-content/uploads/bpmh-widgets directory.

= 1.1.1 =
Very Important ! Before upgrading, make sure to back up the wp-content/plugins/bp-my-home/widgets folder if you uploaded your own widgets there.

= 1.1 =
Very Important ! Before upgrading, make sure to back up the wp-content/plugins/bp-my-home/widgets folder if you uploaded your own widgets there.

= 1.0 =
no upgrades, just a first install..