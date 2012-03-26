=== WPeMatico ===
Contributors: etruel
Donate link: http://www.netmdp.com/wpematico/
Tags: RSS, Post, Posts, Feed, Feeds, RSS to Post, Feed to Post, admin, aggregation, atom, autoblogging, bot, content, syndication, writing
Requires at least: 3.0
Tested up to: 3.3
Stable tag: 0.85Beta

WPeMatico is for autoblogging, automatically creating posts from the RSS/Atom feeds you choose, which are organized into campaigns. 

== Description ==

WPeMatico is for autoblogging, automatically creating posts from the RSS/Atom feeds you choose, which are organized into campaigns. 
For RSS fetching it's using the simplepie library included in Wordpress.
Also for image processing it's using the core functions of wordpress.
Translations ready. .pot english file included for localize.  Translations files are welcome.
I take code from many many other plugins, but for this plugin I read a lot of code of the old WP-o-Matic and also old versions of BackWPUp. Thanks to the developers;)
If you like, please rate 5 stars. thanks :)

Supported features:

* Campaigs Feeds and options are organized into campaigns.
* Comfortable interface like Worpress posts editing for every campaign.
* Multiple feeds / categories: it’s possible to add as many feeds as you want, and add them to some categories as you want.
* Integrated with the Simplepie library that come with Wordpress.  This includes RSS 0.91 and RSS 1.0 formats, the popular RSS 2.0 format, Atom...
* Feed autodiscovery, which lets you add feeds without even knowing the exact URL. (Thanks Simplepie!)
* Unix cron and WordPress cron jobs For maximum performance, you can make the RSS fetching process be called by a Unix cron job, or simply let WordPress handle it.
* Images caching are integrated with Wordpress Media Library and posts attach. upload remote images or link to source. Fully configurable.
* Words Rewriting. Regular expressions supported.
* Words Relinking. Define custom links for words you specify.
* Words to Category. Define custom words for assign every post to specified categories. Thanks to Juergen Mueller at [Wirtschaft](http://www.wirtschaft.com)
* Detailed Log sending to custom e-mail. Always on every executed cron or only on errors with campaign.
* Option to replace title links (Permalink) to source.
* Post templating. 
* Multilanguage ready.
* Tested in Wordpress MULTISITE 

* Extra PRO features
* Delete last HTML tag option.
* Words count filters. Count how many words are in content for assign a category or skip the post.
* Also the content can be converted to text and cutted at wanted amount of words or letters.
* Keywords filtering. You can determine skip the post for certain words in title or content.

Upcoming features:

* Campaigns import/export.
* Some requested easy cool features...

PHP5 is required!

Copyright 2010.
This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version. 

If you want support or more detail in spanish you can search WPeMatico here:[NetMdP](http://www.netmdp.com). 

Traducción al español de Argentina de la Licencia GNU: [http://www.spanish-translator-services.com/espanol/t/gnu/gpl-ar.html]

== Installation ==

You can either install it automatically from the WordPress admin, or do it manually:

1. Unzip "wpematico" archive and put the folder into your plugins folder (/wp-content/plugins/).
2. Activate the plugin from the Plugins menu.

== Frequently Asked Questions ==

= Where can I ask a question? =

* [Search the page WPeMatico here](http://www.netmdp.com).

== Screenshots ==

1. The table list of campaigns and some info of everyone.

2. The detailed log after executing "Run Now" in campaign.

3. Checking feeds on campaign editing.

4. Assigning a category to a post because there is a word in content.

5. Filtering with keywords or Regular Expressions at title or content.

6. Cutting last html predefined tag, and counting words or letters to skip, cut or asign to a category.

== Changelog ==

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

= 0.85 Beta =
* WP 3.3.1 Ready.