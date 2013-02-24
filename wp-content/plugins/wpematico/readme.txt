=== WPeMatico ===
Contributors: etruel
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=B8V39NWK3NFQU
Tags: RSS, Post, Posts, Feed, Feeds, RSS to Post, Feed to Post, admin, aggregation, atom, autoblogging, bot, content, syndication, writing
Requires at least: 3.1
Tested up to: 3.4.2
Stable tag: 1.1.1

This is for autoblogging. Drink a coffee meanwhile WPeMatico publish your posts.  Post automatically from the RSS/Atom feeds organized into campaigns.

== Description ==

WPeMatico is for autoblogging, automatically creating posts from the RSS/Atom feeds you choose, which are organized into campaigns. 
For RSS fetching it's using the Simplepie library included in Wordpress or force use of external.
Also for image processing it's using the core functions of wordpress.
Translations ready. .pot english file included for localize.  Translations files are welcome.
I take code from many many other plugins, but for the first beta versions of this plugin I read a lot of code of the old WP-o-Matic and also old versions of BackWPUp. Thanks to the developers ;)

If you like, please rate 5 stars and/or donate something. thanks :)

Some supported features (but not all of them):

* Campaigs Feeds and options are organized into campaigns.
* Comfortable interface like Worpress posts editing for every campaign.
* Multiple feeds / categories: it’s possible to add as many feeds as you want, and add them to some categories as you want.
* Integrated with the Simplepie library that come with Wordpress.  This includes RSS 0.91 and RSS 1.0 formats, the popular RSS 2.0 format, Atom...
* Feed autodiscovery, which lets you add feeds without even knowing the exact URL. (Thanks Simplepie!)
* Unix cron and WordPress cron jobs For maximum performance, you can make the RSS fetching process be called by a Unix cron job, or simply let WordPress handle it.
* Images caching are integrated with Wordpress Media Library and posts attach. upload remote images or link to source. Fully configurable.
* Auto add categories from source posts.
* First image attached to a post marked as Featured Image of the post.
* Words Rewriting. Regular expressions supported.
* Words Relinking. Define custom links for words you specify.
* Words to Category. Define custom words for assign every post to specified categories. Thanks to Juergen Mueller at [Wirtschaft](http://www.wirtschaft.com)
* Detailed Log sending to custom e-mail. Always on every executed cron or only on errors with campaign.
* Option to replace title links (Permalink) to source.
* Post templating. 
* Now you can choose what role can see the dashboard widget.
* Multilanguage ready.

* Extra PRO features
* Option for attempt to get Full Content of source site.
* Fix and correct wrong HTML on content.
* Delete last HTML tag option.
* Words count filters. Count how many words are in content for assign a category or skip the post.
* Also the content can be converted to text and cutted at wanted amount of words or letters.
* Keywords filtering. You can determine skip the post for certain words in title or content.


PHP5 is required!

Copyright 2012.
This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version. 

If you want support or more detail in spanish you can search WPeMatico here:[NetMdP](http://www.netmdp.com). 

Traducción al español de Argentina de la Licencia GNU: [http://www.spanish-translator-services.com/espanol/t/gnu/gpl-ar.html]

== Installation ==

You can either install it automatically from the WordPress admin, or do it manually:

1. Unzip "wpematico" archive and put the folder into your plugins folder (/wp-content/plugins/).
2. Activate the plugin from the Plugins menu.

== Frequently Asked Questions ==

= I have this plugin installed and activated.  What must I do now ? =

* OK, now you have in woprdpress admin a new area below posts called WPeMatico.  At settings, setup configuration items. At Campaigns you must add one, in there add one or few feeds with options you choose.  You can use a campaign for grouping feeds for a category or another custom topic that you want.

= Where can I ask a question? =

* [Search the page WPeMatico here](http://www.netmdp.com).

== Screenshots ==

1. Dashboard Widget and menu.

2. The table list of campaigns and some info of everyone.

3. Editing campaign.  Feeds list.

4. Enabling PRO features.

5. Settings page.

== Contributions ==

You can contribute with WPeMatico
Needed translation files: if you can translate from english to any language, you are welcome.
The .pot file are included on plugin and you must use poedit for tranlate.  
Your work, name and website will be mentioned here.

Also we need tutorials on text, pdf, videos.  All are welcome.  Isn't it?
You can send your files to [NetMdP](http://www.netmdp.com/wpematico/)
or to e-mail etruel@gmail.com

== Changelog ==

= 1.1.1 =
* Added New feature: Auto categories from source posts (were available).
* Improved images size.
* Updated language files. pot, es_ES. Spanish Language.
* Small fix with item date.
* Fix some files coded as ANSI instead UTF-8
* Fixed (when fetching) The RegEx stripslashes in "Word to Category" and "rewriting options".

= 1.1 =
* Added tags list to assign to posts in every campaign.
* Added Feed date feature to use the datetime of source post.
* Added {image} tag to post template for show featured image url into post content.
* Added New option for strip links from post content.
* Added also to general Settings no link external images.
* Fixed resets some fields when save campaign.
* Fixed The RegEx stripslashes in "Word to Category" and "rewriting options".
* Some fixes related with images and images urls on media library.
* Added Spanish language File.
* [PRO]
* Added Custom fields feature for fetched posts with values generated by template fields.
* Added Auto generate tags, getting tags from post content.
* Added images on enclosure media tags on feeds.
* Fix an issue on Add feed image to full-content on PRO Version.
* Default Featured image if not found image on content. Link or Upload new image from campaign.
* Added strip HTML filter also to feed content as full-content.
* [/PRO]

= 1.0.2 =
* Fixed many DEPRECATED PHP notices on log and cron interrupted: upgraded Simplepie library to 1.3. 
* New option on advanced settings to force use of Simplepie library in plugin. Simplepie provided by WP is not compatible with PHP 5.3.
* Added options for skip Wordpress Post content filters. Beta. (this is for allow embed code like flash or videos on posts)
* Fixed some other minor details.

= 1.0.1 =
* Fixed not run for more than five campaigns.
* Fixed? problem with filter function wp_mail_content_type
* Trying to do more compatible with plugins that use public custom posts urls: Added 'public' => false and 'exclude_from_search' => true to custom post type 'campaign'.
* Added new file for run external cron alone without call wp-cron.
* New website for plugin [WPeMatico](http://www.wpematico.com)

= 1.0 =

This is a really update. Lot of things that you asked for, are ready in 1.0 version.

* Now use Wordpress custom post types for campaigns.
* Now you can move and close metaboxes.
* Now you can paginate and filter campaigns list by name.
* Now we have an image background at WP repository. :)
* Improved feed list with scroll into campaign.
* Improved feed search filter.
* Better help.
* Better performance.
* Colored boxes for knowing what I'm doing.
* More options on Settings.
* New logo and images.
* Totally translatable. 
* Better use of Ajax.
* Better use of class SimplePie included into Wordpress core.
* Deactivated Wordpress Autosave only editing WPeMatico campaigns.
* Automatic check fields values before save campaign without reload page and/or lost fields content.
* Option to activate or deactivate automatic feed check before save campaign.
* Added option for test only one feed.
* Added description field for every campaign.
* New option to del hash on feeds for fetch duplicated posts. (Advanced config)
* New option to see last log of every campaign. (Advanced config)
* Now you can Disable Check Feeds before Save. (Advanced config)
* Now you can choose which roles can see the dashboard widget.
* Fixed rewrite to also rewrite html code.
* First image on content as WP feature image.
* Now support relative paths for upload images from source content.
* [PRO]
* Option to automatic create author names based on source or custom typed author.
* Option to assign author per feed, instead campaign or both options.
* Option for correct and fix wrong html code with a lot of options from htmLawed.
* [/PRO]

= 0.91.1Beta =
* [PRO]
* Added New Feature: Fetch every 1 Minute. Buyed by Juergen Mueller from [Wirtschaft](http://www.wirtschaft.com)
* [/PRO]
* Fix minor but important thing about duplicating posts.
* Fix minnor layout bugs on Settings.

= 0.90Beta =
* [PRO]
* Added New Feature: Attempt to get Full Content.
* [/PRO]
* First image attached to a post marked as Featured Image of the post.
* Added support for Wordpress Custom Post Types
* Added check Feeds before save campaign.
* Fix layout thing with schedule options.
* Updated Frequently Asked Questions.
* Updated donate link with paypal.

= 0.85Beta =
* [PRO]
* Added New Feature: Custom Title with counter.
* [/PRO]
* Added {author} tag for retrieve the name of the Author of the post. 
* Added {authorlink} tag for retrieve the original link of the Author of the post.
* Added new method for check duplicate posts also with the source permalink.
* Added option for display or not the dashboard widget.
* Fix automatic update issue bettwen standard and Pro versions.
* Fix some display issues in Keyword Filters box in PRO.
* Wordpress 3.3.1 compatibility.

= 0.84Beta =
* Wordpress 3.3 compatibility.
* small fix with php function str_replace

= 0.83Beta =
* New PRO version available at website.
* [PRO]
* 	New features: Delete last HTML tag option, Words count filters, Keywords filtering.
* 	New options for enable or not new features: Words count filters, Keywords filtering.
* 	Words count filters. Count how many words are in content for assign a category or skip the post.
* 	Also the content can be converted to text and cutted at wanted amount of words or letters.
* 	Keywords filtering. You can determine skip the post for certain words in title or content.
* [/PRO]
* Fixed images process after rewrite functions for not upload deleted images at content.
* Fixed spaces at images names.
* Fixed little duplicate thing on titles with special chars. 

= 0.82Beta =
* New option for enable or not the new feature: Words to Category.
* Words to Category. Define custom words for assign every post to specified categories.
* Fixed "No link to source images" Hide/show option on click "Enable cache img" 
* Added "checking" image near "Check all feeds" button.

= 0.81Beta =
* Wordpress 3.2.1 compatible.
* Add ‘Activate/Deactivate’ to options in campaign's table.
* Fixed when click “Add more” in Rewrite, the form appears in the Post template section.

= 0.8Beta =
* Upgrade only for Wordpress 3.1 compatibility.

= 0.7Beta =
* Wordpress 3.0.4 compatible.
* Fixed now check for duplicates on draft, private and published post.
* Added {feeddescription} tag.
* Fixed some issues in template post tags.

= 0.6Beta =
* Added Post template feature in every campaign.

= 0.5Beta =
* Fix Post title links to source option.
* .pot language file updated.
* Readme.txt updated.
* Merry Christmas 2010. Jesus lives.

= 0.4Beta =
* Fix some issues on rewriting words & links.
* Fix links in Dashboard widget.
* Fix the Allow Ping option issue.
* Change log e-mail to html format.
* New options added for enable or disable image cache in every campaign.
* New options added for not link to source image on error at image cache upload in every campaign.
* Fix Tested up field on Readme.txt

= 0.3Beta =
* Fix issue in 1st feed for checking.
* Fix bug Warning & Error messages on running campaign.
* Added Go Back button on error saving and get the old values.
* Added 2 more Screenshots on Wordpress repository.
* Readme.txt updated.

= 0.2Beta =
* Fixed version number.
* Fix wrong message when activating.
* Deleted .mo & .po files, replacing with new wordpress generated .pot

= 0.1Beta =
* initial release
* [more info in spanish, en español](http://www.netmdp.com/wpematico/)

== Upgrade Notice ==

= 1.1.1 =
* Maintenance Release. Many bugs fixed. Recommended upgrade.
