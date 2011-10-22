=== Tweet Blender ===
Contributors: kirilln
Tags: sidebar, twitter, tweets, multiple authors, favorites, tweet, tags, lists, hashtags, archive, widget, admin, AJAX, jquery, keywords, BuddyPress, blender
Requires at least: 2.8.0
Tested up to: 3.2.1
Stable tag: 3.3.15
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=5907095

Provides several Twitter widgets: show your own tweets, show tweets relevant to post's tags, show tweets for Twitter lists, show tweets for hashtags, show tweets for keyword searches. Multiple widgets on the same page are supported. Can combine sources and blend all of them into a single stream.

== Description ==

Better than Twitter's own widgets - Tweet Blender is tag-aware and has support for multiple authors, lists, hashtags, and keywords all blended together. The plugin can show tweets from just one user or a list of users (as all other Twitter plugins do); however, it can also show tweets for a topic which you can define via Twitter hashtag or keyword. But there is more! It can also show tweets for multiple authors AND multiple lists AND multiple keywords AND multiple hashtags all blended together into a single stream.

Version 3 added a new widget that *automatically shows tweets relevant to your blog post* by taking post's tags and using them as keywords for Twitter search.

Submit ideas, ask questions, or report problems on the [GetSatisfaction.com community site for Tweet Blender widget](http://getsatisfaction.com/tweet_blender "Get Satisfaction!")

Follow [Tweet Blender on Twitter](http://twitter.com/tweetblender "@tweetblender")  to keep up to date on bug fixes and releases and join the conversation using [#tweetblender](http://search.twitter.com/search?q=%23tweetblender "#tweetblender") hashtag

= Features =
* NEW: fully localizable! Russian translation available as of 3.3.6, more languages coming soon (please get in touch if you can help)
* NEW: ability to apply filtering to lists e.g. @tweetblender/testlist|plugin will pull all tweets from that list that contain word "plugin"
* NEW: ability to get Cache Manager addon ($) that allows to backup/restore cache and delete individual tweets
* If you use custom field "tb_tags" for a post its value overrides actual tags and gets used by Tweet Blender for Tags widget (requested by Thomas P via GetSatisfaction)
* Support for non-English hashtags and filter words
* A filter that allows to filter out tweets that are from different screen names but have exactly same content
* A filter that allows to show only replies and hide original tweets
* Ability to define custom text for "view more" link
* Support for multi-keyword phrase searches (e.g. <b>Tweet Blender</b>)
* Ability to define name that shows up instead of twitter handle using a colon (e.g. @joesmith:Joe Smith would show "Joe Smith" for each tweet instead of "@joesmith") 
* Throttling that allows to limit number of tweets to show for each user account. You can now have X tweets for each user in the list no matter how frequently each of them tweets. 
* Favorites widget that shows favorite tweets or one or multiple users
* Improved cache management controls
* Ability to qualify searches using a pipe symbol. For example @user|#fun will get all tweets from @user that contain #fun in them
* Ability to filter out tweets that come from ceratin users or contain certain undersired keywords or hashtags
* Ability to turn ON/OFF display of mentions
* Quick, simple and secure way to insert widget into your posts and pages
* Multi-widget support - include any number of widgets in the sidebar or on the page with different sources in each.
* Database-driven efficient caching
* Widget that automatically shows relevant tweets by using post's tags
* Show tweets for one or more Twitter lists (e.g. [@tweetblender/testlist](http://twitter.com/tweetblender/testlist "example Twitter list"))
* Allows to blend tweets from private accounts if you authorize access to your Twitter account that follows these private users (requires oAuth so PHP5 only).
* Status tab in the admin panel that shows API limits remaining and cache information
* Shows tweets from one or more Twitter users (e.g. [@tweetblender](http://twitter.com/tweetblender "@tweetblender"))
* Shows tweets for one or more topic defined by keywords (e.g. ['wordpress'](http://search.twitter.com/search?q=wordpress "wordpress Twitter search"))
* Shows tweets for one or more topic defined by Twitter hashtag (e.g. [#wordpress](http://search.twitter.com/search?q=%23wordpress "#wordpress Twitter hashtag search"))
* Shows tweets for multiple users, multiple topics, and multiple hashtags blended together into single stream
* Allows to turn display of user's photo ON/OFF. Photos can be OFF in the sidebar to conserve screen space and ON on the archive page.
* Allows to replace @screennames in tweets with links to user accounts (open in new window)
* Allows to replace #hastags in tweets with links to Twitter search results for that hashtag (open in new window)
* Allows to replace URLs in tweets with links to those URLs (open in new window)
* Automatically creates a page with archive (a longer, expanded list of tweets). Can be disabled if you want to create archive manually or don't need an archive.
* Allows to specify number of tweets to show in each widget
* Allows to specify number of tweets to show on the archive page
* Provides template tag `<?php tweet_blender_widget(); ?>` to include one or more widgets on any page
* Provides "refresh" icon that allows users to manually refresh tweet list
* Allows to specify refresh period which turns ON automatic refresh of the tweet list. Can be set for each individual widget instance
* Checks screennames, lists, keywords, and hashtags for validity prior to saving
* Displays "reply" and "follow" links for each tweet that appear when user places mouse over the tweet area. Can be turned ON/OFF
* Allows to create individual twitter streams for different authors by using tweet_blender_widget() tag
* Provides advanced distributed caching mechanism to store Twitter data and work around Twitter API's connection limit
* Allows to reroute all Twitter API requests via blog's web server to take advantage of white-listed server (with oAuth)
* Allows to filter tweets by language (for hashtags and keyword sources only)

= Translations =
Russian - @knovitchenko
Dutch - @afoka  (for help in Dutch use http://www.werkgroepen.net/wordpress/plugins/tweet-blender/)

Please send translations for your language. POT file is in the /lang folder

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the entire tweet-blender directory to the /wp-content/plugins/ directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Use admin Settings > Tweet Blender to specify configuration options

= Adding Tweet Blender to Sidebar =

If you'd like to show one or more widgets in your sidebar follow these simple steps.

1. Go to WP admin > Appearance > Widgets
2. Drag and drop widget of your choice into the appropriate place on your sidebar. You can add multiple widgets and have each of them blend different sources.
3. Update configuration options and Save them

Note: The Tweet Blender For Tags widget will show up in your sidebar only on a dedicated post page and only if that post has tags. For index and custom pages (and for posts without tags) nothing will be shown.

= Adding Tweet Blender to Posts and Pages =

You can insert the widget directly into the body of your post or page by following these simple steps:

1. Click on New Post in WP Admin to start working on your new post
2. In editor, click on the HTML tab in the upper right corner so you are looking at the HTML source of your post
3. Enter the following HTML where you want the widget to appear and update configuration options

`&lt;form id="tweetblender1" class="tb-widget-configuration" action="#">&lt;div>
&lt;input type="hidden" name="sources" value="@tweetblender,#tweetblender,@tweetblender/testlist,tweetblender" />
&lt;input type="hidden" name="refreshRate" value="60" />
&lt;input type="hidden" name="tweetsNum" value="4" />
&lt;input type="hidden" name="viewMoreUrl" value="http://twitter.com/tweetblender" />
&lt;input type="hidden" name="viewMoreText" value="Follow Us!" />
</div&gt;</form&gt;`

4. When you are done editing, save the page/post.

Notes: make sure the id of the form is unique on that page. Only sources is a required setting.

= Adding Tweet Blender to Templates =

For greater flexibility, there is a PHP template tag that allows you to add tweet blender to a custom page template or a template in your theme. Edit the PHP page template where you want to include the widget and put the following tag in:

`tweet_blender_widget(array(
    'unique_div_id' => 'tweetblender-t1',
    'sources' => '@knovitchenko,@tweetblender/testlist,#tweetblender,twitter',
    'refresh_rate' => 60,
    'tweets_num' => 5,
    'view_more_url' => 'http://twitter.com/tweetblender',
    'view_more_text' => 'follow us!'
));`

= Options =
* unique_id_id = Required. The ID of your widget. It has to be unique on each page where widget(s) is on. Widgets added through Appearance > Widgets automatically have unique IDs because WordPress creates them for you using 'tweetblender-X' format where X is a number.  It is recommended that you use a similar format for this ID e.g. 'tweetblender-t1' or 'tweetblender-tag1'
* sources = Required. List of sources to blend separated by comma. Don't forget the single quotes around this parameter.
* refresh_rate = Optional. How often to refresh the list. In seconds i.e. 60 = 1 minute. Set to 0 to disable automatic refresh.
* tweets_num = Optional. How many tweets to show in the widget.
* view_more_url = Optional. The url where "view more" link points to. Don't forget the single quotes around this parameter.

For the related tweets widget that uses post's tags you would use the following:

`tweet_blender_widget_for_tags(array(
    'unique_div_id' => 'tweetblender-tt1',
    'refresh_rate' => 60,
    'tweets_num' => 5
));`

= Options =
* unique_div_id = Required. The ID of your widget. As mentioned above, it has to be unique on each page where widget(s) is on.
* refresh_rate = Optional. How often to refresh the list. In seconds i.e. 60 = 1 minute. Set to 0 to disable automatic refresh.
* tweets_num = Optional. How many tweets to show in the widget

== Screenshots ==

1. Sidebar showing 2 widgets. One mixes tweets for "twitter" keyword. The other blends @knovitchenko tweets with tweets for @tweetblender/testlist Twitter list. Any number of widgets can be included. You can turn off display of pictures to conserve screen space.
2. Tweets archive page. Created automatically and tweets are inserted into content automatically; however, you can edit title, tags, and text of the page using regular admin features
3. Admin: widgets page that shows Tweet Blender For Tags and Tweet Blender dropped into Sidebar North area and widget control options exposed.
4. Admin: general settings for the plugin.
5. Admin: general widget settings for the plugin
6. Admin: archive page settings
7. Admin: filtering options
8. Admin: advanced settings
9. Admin: status information
10. Admin: Cache Manager addon (note: paid module)

== Getting Help ==

Best place to get help is on the [Tweet Blender support community page](http://getsatisfaction.com/tweet_blender "Get Satisfaction!")

My goal is to make sure that Tweet Blender works on *your* site.

If you experience a problem please don't simply disable and delete the plugin; instead, do let me know about your issue! You'd be helping me make TweetBlender better, you'd be helping other users who could be experiencing the same issue, and you'd get a kick-ass plugin working for you as a result.

When reporting an issue please state the following things in your initial message:
1. The URL of the page where the issue can be seen
2. The version of TweetBlender you are using
3. The version of WordPress you are using
4. Your browser type and version
5. Your OS type and version
If you don't tell me these things right away I usually have to write back and ask for them and that delays the fix.

Here are the places I monitor regularly:

* Twitter: hashtag #tweetblender, mentions of @tweetblender, keywords "tweetblender" and "tweet blender" - This is the best way if your request is fairly urgent as I'm online 16 hours a day
* Facebook: [Tweet Blender fan page](http://www.facebook.com/pages/Tweet-Blender/96201618006 "Facebook Fan Page") discussion board
* WordPress Support Forums: [tweet-blender tag](http://wordpress.org/tags/tweet-blender?forum_id=10 "WP forum")

Additional resources:

* New Homepage: (coming soon) [http://tweet-blender.com](http://tweet-blender.com "TweetBlender home page")
* Email: tweetblender AT gmail DOT com

*Note #1: I might not get back to you immediately.* This software is written and supported by an individual, not a company or a group. I have a demanding full time job and family with two kids. All of my free time is spent on fun projects like this one.

*Note #2: Please don't flame me for bugs.* Twitter is notoriously unstable and has some bugs in the API. On top of it, I use jQuery library that has some bugs in it as well. On top of it you might have other plugins installed that have bugs or introduce conflicts. Finally, 90% of the code works within browsers which have all sorts of different bugs of their own. Before calling TweetBlender "crap" give it a benefit of a doubt - it might not be its problem. I'm really striving to make it the best it could be

== Changelog ==

= 3.3.15 =
* Bug fix: made "view more" link appear again (thanks to Somino for reporting via GetSatisfaction)
* Improvement: both PHP tag and form embed now support view more text config option - see Installation tab (thanks to jenkins2541 for requesting via GetSatisfaction)
* Improvement: faster page loads because JavaScript now loads in the footer (thanks to Alexander P for suggesting via GetSatisfaction)
* Improvement: main.js is now minifiable (thanks to Martin E for requesting the fix via GetSatisfaction)

= 3.3.14 =
* Bug fix: duplicate widgets are shown in sidebar

= 3.3.13 =
* Bug fix: form-based embeds did not work in posts and pages
* Improvement: filtered bad words are now encoded so they don't appear in page HTML source in plain text
* Tested with WordPress 3.2.1

= 3.3.12 =
* Bug fix: error during activation "Fatal error: Cannot redeclare class Services_JSON in /.../wp-content/plugins/tweet-blender/lib/JSON.php on line 116" (thanks to Peter O for reporting via WP Support forum)
* Bug fix: checkbox state was not shown as checked after settings were saved in admin (thanks to @JDub82 for reporting via Twitter)

= 3.3.11 =
* Replaced non-GPL jQuery.lightbox plugin with jQuery.fancybox plugin to deal with new WP policy enforcement

= 3.3.10 =
* Tested with WordPress 3.2

= 3.3.9 =
* Improvement: special workaround for an issue where english and localized labels did not load on some hosts.

= 3.3.8 =
* Bug fix: javascript error "js_labels is not defined" (thanks to ianam for reporting via WP Support Forums)

= 3.3.7 =
* Bug fix: problems in IE due to console debug output (thanks to Ken M for reporting via GetSatisfaction)
* Added Dutch translation (thanks to @afoka for translating the plugin)

= 3.3.6 =
* New feature: ability to filter a list using pipe symbol e.g. @user/list|#hash or @user/list|keyword 
* Bug fix: plugin allowed to have an empty string or space under filtered keywords which filtered out all tweets and produced "No tweets found message"
* Bug fix: plugin allowed to select both "hide mentions" and "hide NOT mentions" under Filters tab which produced "No tweets found message"
* Improvement: all labels are now translatable. Russian translations available as of this version. Dutch coming next
* Improvement: plugin now works even when extra markup is added to the form by other misbehaving plugins (thanks to doug S for reporting via GetSatisfaction)
* Tested with WordPress 3.1.1 and 3.1.2

= 3.3.5 =
* Bug fix: Cache Manager not working if your blog is not in the root of your domain (thanks to Brad for reporting via GetSatisfaction)
* Improvement: Filters are now only used internally and not supplied to Twitter API queries to work around limitations and special character issues (thanks to Phil B for reporting via email)

= 3.3.4 =
* Bug fix: Cache Manager installer not working after purchase is complete (thanks to Stef N for reporting via Get Satisfaction)
* Bug fix: "Warning: require_once(.../wp-includes/class-json.php): failed to open stream: No such file or directory" (thanks to Angilee S for reporting via Facebook)
* Bug fix: If your filters included "xxx" tweets from @somexxxuser still showed up (thanks to Mark S for reporting via GetSatisfactin)  

= 3.3.3 =
* Bug fix: visual editor and other AJAX features broken in WP3.1 on PHP4 when plugin is active due to JSON library conflict (thanks to Nezi, Gabor R, Geoffrey F, and @ILoveStyle_be for reporting)
* Bug fix: Cache panel layout broken if blog is not at root url and Cache Manager addon is not installed

= 3.3.2 =
* Bug fix: blending private/pass-protected accounts did not find any tweets
* Bug fix: error "Fatal error: Cannot redeclare class OAuthSignatureMethod_HMAC_SHA1" when other OAuth-based plugins installed (thanks to Tomislav V for reporting via Facebook)
* Bug fix: same tweets in cache and from Twitter were treated as different and appeared twice (thanks to mark for reporting via GetSatisfaction)
* Bug fix: WP Admin > Settings > Tweet Blender was not working in IE7 on Windows
* Tested with IE7 and Opera11 on Windows XP
* Tested with WordPress 3.1

= 3.3.1 =
* Bug fix: hashtag links in the infobox that appears after click on Twitter logo were broken
* Bug fix: tweets not refreshed due to "jQuery.jsonp is not a function" error (thanks to Ryan M for reporting via GetSatisfaction)
* Bug fix: Cache Manager installation failed with "Failed opening required 'uploader/pclzip.lib.php'" error (thanks to Lucas S for reporting via GetSatisfaction)
* Bug fix: lists validation when saving widget settings - "json error: can't get json" (thanks to @aaronhudspeth for reporting via Twitter)

= 3.3.0 =
* New feature: ability to get Cache Manager addon ($) that allows to backup/restore cache and delete individual tweets
* New feature: if you use custom field "tb_tags" for a post its value overrides actual tags and gets used by Tweet Blender for Tags widget (requested by Thomas P via GetSatisfaction)
* Bug fix: multiple escapes for filter phrases in quotes broke searches (thanks to @drtanz for reporting via WP Forums)
* Bug fix: problem retrieving cached tweets for hashtags when API limit is reached
* Bug fix: filters now support words in any lanugage, not just in English (thanks to Dmitry Sh for reporting via Facebook)
* Bug fix: Twitter default number of search results (15) was returned even when widget was configured to show more (thanks to Brent S for reporting via GetSatisfaction)
* Bug fix: non-English hashtags support (thanks to Esben R for reporting via GetSatisfaction)
* Bug fix: jQuery conflict with CadabraPress theme (thanks to Dave G for reporting via GetSatisfaction)
* Bug fix: multiple tweets created at the same second by the same user had only the first one show up in the stream
* Improvement: switched to new Twitter API endpoint for user timelines
* Improvement: "Loading tweets" message now hides if Twitter had an error and loading is done
* Improvement: Made admin section use the latest jquery shipping with WP and stopped bundling local copy with plugin
* Tested with WordPress 3.0.3, 3.0.4, 3.0.5

= 3.2.4 =
* Bug fix: not able to use "Show author's username for each tweet" checkbox under Archive tab in admin
* Tested with WordPress 3.0.2

= 3.2.3 =
* Bug fix: not able to turn off search API use under Advanced tab in admin
* Bug fix: cache not being saved properly (reported by Daniel M via GetSatisfaction)

= 3.2.2 =
* New feature: filter that allows to filter out tweets that are from different screen names but have exactly same content
* New feature: filter that allows to show only replies and hide original tweets (requested by @mobloggers via Twitter)
* New feature: ability to define custom label for "view more" link for Favorites widgets
* Bug fix: follow and reply mouseover links not showing on cached tweets (thanks to dale.rogers for reporting via GetSatisfaction)
* Bug fix: JavaScript error on clean installation with caching disabled
* Bug fix: custom "view more" url not used when default archive page is present
* Bug fix: additional changes to prevent duplicate tweets

= 3.2.1 =
* New feature: ability to define custom label for "view more" link in each Tweet Blender widget
* Bug fix: tweets not being refreshed (thanks to MijniPad for reporting via GetSatisfaction site)
* Improvement: additional code clean up (thanks to @Frumph for pointing out issues)

= 3.2.0 =
* New feature: ability to use multi-keyword phrases as search queries such as <b>Tweet Blender</b>
* Bug fix: Some tweets were apearing twice in the widget due to integer conversion accuracy bug
* Bug fix: Limit of number of tweets was not honored and old tweets didn't fall off the list
* Bug fix: Tweet Blender for Tags was failing if there were no tags (bug fix contributed by Leif E)
* Improvement: more efficient cache storage
* Improvement: better architecture for tweet handling and indexing in prepration for Twitter's migration off numeric tweet IDs
* Improvement: numerous security and realiability fixes (contributed by @Frumph)

= 3.1.18 =
* Bug fix: if more than 9 bad words defined in the filters the tweets are not pulled in (reported by mike via GetSatisfactionx)

= 3.1.17 =
* Bug fix: Some tweets apearing twice in the widget or not falling off the widget after limit is reached (additional fixes for cached tweets)
* Bug fix: throttle settings not saving (reported by @algebrawl via Twitter)

= 3.1.16 =
* Bug fix: Some tweets apearing twice in the widget or not falling off the widget after limit is reached (additional fixes for cached tweets)

= 3.1.15 =
* Bug fix: "Any language" in filters pulling English-only tweets (reported by Geert V via GetSatisfaction)
* Bug fix: Some tweets apearing twice in the widget (reported by Lefty F via GetSatisfaction)

= 3.1.14 =
* Bug fix: message "Check boxes above to clear cached tweets from the database" showed up even if there were no cached tweets.
* Added ability to define name that shows up instead of twitter handle using a colon. Using <b>@joesmith:Joe Smith</b> as source would show "Joe Smith" for each tweet instead of "@joesmith"

= 3.1.13 =
* Added new time period options for throttling feature (requested by jfgagne via GetSatisfaction site)

= 3.1.11,3.1.12 =
* New Feature: throttling settings under the Filters tab
* Tested with WordPress 3.0.1

= 3.1.10 =
* Bug fix: incorrect links when screennames are inside parenthesis in a tweet (reported by debattierklubwien via GetSatisfaction)
* Bug fix: added debugging info to fix caching problem when subdomains point to main domain site (reported by Nunzia R via GetSatisfaction)
* Improved security of admin tools by adding nonces

= 3.1.9 =
* New feature: Favorites widget that's just like Twitter's own but supports multiple accounts (requested by Varun C via GetSatisfaction)
* New feature: cache table has a separate column with only tweet text - simplifies manual table management (requested by Victoria A via Facebook)

= 3.1.8 =
* New feature: for better SEO there is now ability to include googleoff/googleon tags around dates and tweets (requested by jmuribe via GetSatisfaction site)
* New feature: ability to control how long the cached tweets are kept in the database
* New feature: ability to delete cached tweets for each source or for all sources together
* Improved automated cache clean up handling - now happens when Settings page is loaded/saved in WP Admin

= 3.1.7 =
* Bug fix: Cannot use object of type WP_Error as array in .../plugins/tweet-blender/ws.php on line 112
* Bug fix: Cache not being refreshed properly (thanks to djustus for reporting via WP support forum)
* Bug fix: 2nd attempt to fix "Invalid argument supplied for foreach() in ...plugins/tweet-blender/lib/lib.php on line 374" (reported by Texiwill via WP Support forums)
* Added comment output when Tweet Blender for Tags is not shown to assist debugging (for pedrgon on GetSatisfaction)
* Made plugin available for BuddyPress

= 3.1.6 =
* Bug fix: Tweet number limit not obeyed in Google Chrome (reported by Kristian.39 via GetSatisfaction site)
* Bug fix: Error "Invalid argument supplied for foreach() in ...plugins/tweet-blender/lib/lib.php on line 374" (reported by Texiwill via WP Support forums)
* New feature: ability to switch back to user_timeline API if Twitter search does not return tweets for your accounts
* Improvement: widget no longer contacts tweet-blender.com to check opt out status

= 3.1.5 =
* Bug fix: 'Fatal error: Cannot instantiate non-existent class: twitteroauth in .../plugins/tweet-blender/widget.php on line 111' (reported by Will C via Facebook)
* Bug fix: 'Fatal error: Cannot use object of type WP_Error as array in .../plugins/tweet-blender/lib/lib.php on line 337' (reported by gavster via GetSatisfaction and Jed V via Facebook)
* Improvement: no default sources pre-filled for widgets so there is no confusion and widget doesn't pull tweets from strangers

= 3.1.4 =
* Bug fix: JavaScript error when max tweets set to 1 for a widget (reported by Neil via GetSatisfaction site)
* Bug fix: Due to OAuth library conflict, when activating the plugin browser shows blank screen and all admin tools disappear (reported by Markos I via email)
* Bug fix: Error "jQuery("#" + f.id).next().attr("id") is undefined" that appeared if form tag was placed before an element that had no id attribute (reported by koenhendricks via GetSatisfaction site)
* Bug fix: Error in WP 3.0 can't find WP_HTTP 
* Bug fix: target="_blank" didn't validate for Strict DTD. Replaced with JS equivalent (requested by David via GetSatisfaction site)

= 3.1.3 =
* Bug fix: with many screen names as sources the widget still pulled in mentions even when setting was set to filter mentions out (reported by artbykelly via GetSatisfaction site)

= 3.1.2 =
* New feature: checkbox under Filters that allows to tun display of tweets with mentions ON/OFF
* Bug fix: widget not detecting private accounts and showing no tweets as result (reported by nerdsden via GetSatisfaction site)
* Bug fix: "Hide tweets that are in reply to other tweets" was not working since v3.1.0. Fixed now

= 3.1.1 =
* New feature: checkbox under Widgets tab allows to turn username display in the widget ON/OFF (requested by daramjit via GetSatisfaction site)
* Bug fix: problems with spaces and blank lines in sources (Reported by @yvesdetalleur via WP Support and Twitter) 
* Bug fix: SQL error in blogs that use non-standard prefixes for DB tables (Reported by Karl-Heinz K via blog/email)
* Bug fix: plugin pulled mentions in addition to tweets from users themselves (Reported by artbykelly via GetSatisfaction and Carsten F via Facebook)
* Bug fix: form embeds showed “Twitter sources to blend are not defined” message due to markup change (Reported by @prabhasp via Twitter and Michael R via blog)

= 3.1.0 =
* New feature: in addition to "Manual" refresh setting there is now a "Only once (on load)" option to help keep cache fresh (Requested by Xeross via GetSatisfaction site) 
* New feature: advanced source selection using pipe "|" symbol e.g. @knovitchenko|#tweetblender would show tweets from me that use the hashtag
* New feature: blacklist users, keywords and hashtags so tweets that contain them are filtered out from the blend
* New feature: toggle widget header ON/OFF
* New feature: JavaScript hook TB_customFormat(tweetHTML) for advanced tweet HTML formatting (requested by Mikkel W B via Facebook)
* New feature: archive page is not created automatically by default. You need to check a box under Admin > Settings > TweetBlender > Archive (requested by SM Wordpress via GetSatisfaction site)
* New feature: checkbox under Widgets tab in admin that turns source verification ON/OFF to work around firewall/proxy issues (reported by Rose L via Twitter) 
* Improvement: quicker timeout on calls to tweet-blender.com (requested by Johnny P via WordPress support)
* Improvement: title that has only spaces is treated as empty (requested by Mick via WordPress support)
* Improvement: widget uses timezone from WordPress config when making custom formatted time stamps (requested by Keith F via GetSatisfaction site)
* Improvement: ensured markup passes W3C validation (suggested by @buildaroo via Twitter)
* Bug fix: callback.php 404 error after trying to authorize private account via oAuth (thanks to @MsTT for reporting via Twitter)

= 3.0.8 =
* Bug fix: widget not showing up on some pages when archive page is disabled (thanks to Markos I for reporting via email)
* Bug fix: javascript incompatibility with some themes due to Array object extension conflict (thanks to Viktor C for reporting via GetSatisfaction site)

= 3.0.7 =
* Tested with WordPress 2.9.2
* Bug fix: "view more" always pointed to archive page when using simple embed method (thanks to Ralf S for reporting via blog)

= 3.0.6 =
* Bug fix: finally found the bug that wipes out settings on every upgrade. Now upgrades should be much more seamless.

= 3.0.5 =
* New feature: simple HTML short code that allows to include Tweet Blender into a post or page. Exec-PHP plugin is no longer needed! See *Installation* tab for more info.

= 3.0.4 =
* Bug fix: fixed 'Warning: Invalid argument supplied for foreach() in .../plugins/tweet-blender/widget-tags.php on line 35' message in Tweet Blender For Tags (thanks to Jean-Francois G. for reporting via email)
* Bug fix: widget and archive page showing duplicate cached tweets if a tweet matched several sources/keywoards (thanks to John C. for reporting via Facebook)
* Bug fix: tweet_blender_widget() tag didn't work when inserted directly into post/page body with Exec-PHP plugin (thanks to Jean-Francois G. for testing and reporting via email)

= 3.0.3 =
* Bug fix: setting were wiped out on every upgrade. Moved uninstall.php to tb_uninstall.php - looks like a bug in WP
* Bug fix: hastags, links, and scrennames were not linked when cached tweets were showing even if admin setting was configured to link them
* Bug fix: cache not being saved thus producing blank archive page and warning message under Admin > Tweet Blender > Status
* Bug fix: too much white space between header and tweets in some themes (thanks to @robcolbert for reporting via Twitter)

= 3.0.2 =
* Bug fix: tweets not showing in themes that don't have multi-widget support and thus don't assign unique IDs to containers (thanks to @triplecrankset and @Diambi for reporting via Twitter)
* Bug fix: "Loading tweets..." message not hidden even after tweets are loaded when multiple widgets with keyword sources are on the same page (thanks to Adam P for reporting via email)

= 3.0.1 =
* For backward compatiblity, added back tb_widget() and tb_archive() template tags that output empty string.
* Bug fix: fixed 'Warning: include_once(...../plugins/tweet-blender/lib/twitteroauthconfig.php) [function.include-once]: failed to open stream: No such file or directory in ..../plugins/tweet-blender/lib/twitteroauth/callback.php on line 13' (thanks to Jean-Francois G. for reporting via email)
* Bug fix: fixed 'Warning: extract() [function.extract]: First argument should be an array in .../blog/wp-content/plugins/tweet-blender/widget.php on line 13' (thanks to Fabien T. for reporting via Facebok)
* Bug fix: animation wasn't showing on the refresh icon (thanks to Jurjis K. and @sobaka_ebaka for reporting via Facebook and Twitter)

= 3.0.0 =
* New feature: Multi-widget support. Place any number of widgets into sidebar or page and have them blend different sources.
* New feature: DB-based super-efficient cache. Only new tweets are sent to server thus conserving bandwidth.
* New feature: automatic daily clean-up of cache database
* New feature: ability to re-route tweets via server using whitelisted account (oAuth) and not just IP
* New feature: major SEO improvement - widgets loads on the page with cached tweets already in it. Now page looks good to search engine bots and people without JavaScript
* New feature: a new widgets that takes tags of your post and uses them as keywords to show relevant tweets in the sidebar
* Enhancement: additional options for number of tweets on the archive page. Now goes up to 500
* Enhancement: additional options for number of tweets in the widget. Now goes up to 100
* Enhancement: additional options for refresh periods. Now can be as frequent as every 5 seconds
* Enhancement: performance improvements for both bandwidth and CPU use. If you had issues with your ISP they should be resolved now.
* Enhancement: checks for PHP5 and disables oAuth on PHP4 hosts
* Enhancement: uninstall script that removes database table and options when plugin is removed thus making clean exit
* Enhancement: re-organized code and directory structure
* Removed URLexpand.com use - not ready for prime time
* Removed refresh on the archive page

= 2.4.7 =
* Tested with WordPress 2.9.1
* Bug fix: On GoDaddy servers only, 404 status returned by cache requests but caching was still OK (thanks to @prgully for reporting via Twitter)
* Bug fix: "view more" link is misaligned vertically in some themes (thanks to @lorenzguitar for reporting via Twitterl)
* New feature: admin "Status" tab's label becomes red if any warnings are present on that tab. This way it's more visual and you don't have to check it explicitly.

= 2.4.6 =
* Bug fix: message "Initializing..." is shown but nothing happens (thanks to Daniel F, Frank R, bodylovewellness for reporting via email, Facebook, and WP support forums)
* Bug fix: first tweet in the list is shifted to the right on some sites (thanks to Dbdevils for reporting via email)
* New feature: under "Status" tab, shows number of API requests for each refresh. A hint is shown if using Twitter lists would add efficiency.

= 2.4.5 =
* Bug fix: connections to Twitter were still made even if page didn't have a widget on it (thanks to @MJLaRue for reporting via Twitter)

= 2.4.4 =
* Tested with WordPress 2.9
* Bug fix: "headers already sent" error in admin when plugin is activated (thanks to Jörgen C. for reporting via Twitter)
* Bug fix: plugin not starting due to other plugins overloading window.onload event (thanks to @DanielFarrellNZ for reporting via Twitter)

= 2.4.3 =
* Bug fix: "jQuery.toJSON is not a function" error that was shown if you disabled caching (thanks to jasicom & x3r0ss for reporting via WP Support)
* Bug fix: "d.user is undefined" error that was shown if you had a private source but no oAuth credentials to follow it
* Switched from `<ol> with <li>` to `<div>` in HTML markup, ensured XHTML1.0 Strict compliance (thanks to Bryan B for suggesting via Blog)
* Made tweets load sooner by switching to window.load event vs. document.load event (thanks to John C. for suggesting via Facebook)

= 2.4.2 =
* Bug fix: for some themes the recent update broke settings pages due to conflicts with other libraries. Should be fixed now. (thanks to Tia & muse95758 for reporting via Blog)
* Bug fix: Status tab stated that oAuth login was not needed even if private sources were present in the list

= 2.4.1 =
* Rewrote twitter oAuth library to ensure PHP4 compatibility
* Added rel="nofollow" to all links in tweets (requested by Barbara H. via Facebook)
* Bug fix: urls that looked liked shortened urls were only partially linked (thanks to Steve K. for reporting via Facebook)
* Bug fix: tabs in admin were not shown properly in older versions of WordPress

= 2.4.0 =
* Tested with WordPress 2.8.6
* New feature: support for Twitter lists. Add lists as one of the sources (e.g. @tweetblender/testlist) and blend just like you do with individual screen names, hashtags and keywords. Blending multiple lists is possible.
* New feature: ability to blend tweets for private accounts using oAuth. Tweets are shown only to users logged in to your blog. A checkbox to reset oAuth tokens was added under the Advanced tab.
* New feature: when you check the box to disable the archive page all other settings for archive become hidden so there is less confusion
* New feature: new Status tab in the admin was added to show info on oAuth tokens, API limits, and cache status
* Bug fix: cache update requests from http://www.yoursite.com and http://yoursite.com were treated as x-domain and were refused. Not anymore.
* Removed checkbox to validate sources when saving. Sources are always checked from now on i.e. every time you save the settings
* Removed hotlink to CSS on jqueryui.com and bundled CSS with plugin. This fixes the issue for some users that were not seeing tabs in admin.

= 2.3.0 =
* New feature: option to preview/unshorten tiny urls in tweets. Full url can be shown only when you mouseover a link or it can replace the tiny url right in the text. Uses [URLexpand.com](http://urlexpand.com "http://urlexpand.com"). This is in beta. (thanks to Rick S. for suggesting via Facebook)
* Bug fix: no tweets showing when using custom date/time format and have other jQuery-based plugins on the site
* Bug fix: javascript error if your sources start with a number e.g. `5thround` (thanks to Tim N. for reporting via email)
* Bug fix: message `Warning: Invalid argument supplied for foreach() in wp-content/plugins/tweet-blender/tweet-blender.php on line 60` appears if you try to view the widget on the site before saving settings in the admin (thanks to Tamar W. for reporting via Facebook)
* Bug fix: first tweet in the list is shifted to the right due to CSS conflicts (thanks to The Lucky Ladybug for reporting via email)
* Bug fix: cache failure since cached data had extra slashes in it (thanks to Zoli E. for reporting via Facebook)
* Improved logic for loading of javascript libraries. If you disable caching, toJSON plugin is not loaded (savings of 4Kb). If you don't use custom date format, PHPDate plugin is not loaded (savings of 4Kb)

= 2.2.3 =
* Tested with WordPress 2.8.5
* Added "Get Help!" tab to WP plugin info page
* Workaround: getting stuck on "Loading..." message due to javascript error `$.toJSON is undefined` caused by comment-validator plugin (thanks to The Lucky Ladybug for reporting via wordpress forums)
* New feature: better organized tabbed admin panel and updated admin screenshots
* New feature: attempt to create cache directory automatically to ensure it is writable
* New feature: ability to filter out tweets that are replies to other tweets (thanks to John C. for contributing)

= 2.2.2 =
* Bug fix: not showing tweets for hashtags if all of them are longer than 140 character Twitter search query limit (thanks to Kensai for reporting via Facebook)
* Bug fix: not using `before_title` and `after_title` (thanks to x3r0ss and iamtakashiirie for reporting via blog)
* New feature: ability to supply custom title to the widget section of the sidebar. Every other widget asks for title, now TweetBlender does too
* Bug fix: outputting `before_widget` and `after_widget` to the sidebar of the archive page
* Bug fix: Message: `‘TB_config.filter_lang.length’ is null or not an object` (thanks to Greg for reporting via blog)
* Bug fix: erratic behavior with cache feature - cache not being saved
* Bug fix: CSS issue with tweets shifted to the bottom of in a narrow sidebar (thanks to wjwestfall for reporting via blog)

= 2.2.1 =
* Bug fix: reply and follow links were showing up even if they were turned OFF in settings (thanks to x3r0ss for reporting via blog)
* Bug fix: not able to save screenname sources in some cases while hashtags/keywords worked OK (thanks to Chad B. for reporting via Facebook)
* Bug fix: getting stuck at "Initializing..." message due to javascript error "TB_pluginPath is not defined" caused by config options being inserted into page after the main plugin code loads instead of before. (thanks to David S P for reporting via Facebook)
* Bug fix: getting stuck at "Loading..." message due to javascript error "a[0] is undefined" caused by plugin trying to cache Twitter responses with error messages and no data. (thanks to Walther for reporting via blog)
* Bug fix: "c.user is undefined" javascript error that came up when connection limits were reached

= 2.2.0 =
* New feature: caching of Twitter data. Enabled by default but can be turned ON/OFF
* New feature: ability to reroute all Twitter API requests via blog's web server. To be used for white-listed servers only
* New feature: ability to turn ON/OFF the message about connection limits
* New feature: ability to filter tweets by language (for hashtags and keyword sources only)
* Bug fix: multiple clicks on Twitter logo showed multiple info messages
* Bug fix: Warning: array_key_exists() [function.array-key-exists]: The second argument should be either an array or an object in "/xxx/xxx/wp-content/plugins/tweet-blender/tweet-blender.php on line 271"
* Bug fix: message that no tweets were found did not disappear after tweets were finally found.

= 2.1.1 =
* Fixed error 'Cannot instantiate non-existent class: services_json in /home/xxxx/public_html/wp-content/plugins/tweet-blender/admin-page.php on line 37'
* Fixed bug where '@' are continually added to sources
* Fixed bug that showed one less tweet in the widget (and JavaScript error 'c.user is undefined') if you had only screen names as sources

= 2.1.0 =
* Improved performance. It's now about 60% faster!
* Added ability to specify regular keywords in addition to screen names and hashtags. More things to blend!
* Added automatic refresh feature - admin can choose refresh rate in the settings panel or in the widget configuration menu
* Added ability to specify custom URL for archive page
* Fixed bug in archive page tag that used widget's number of tweets setting instead of archive's number of tweets setting
* Fixed bug in "View All" link not pointing to automatically-created archive page. Renamed link to "view more"
* Added display of "reply" and "follow" links when user places mouse over tweet
* Added new feature that validates sources prior to saving them so that misspelled/protected screen names are not accepted (and annoying login pop-up wouldn't appear for users as a result)
* Greatly improved efficiency and speed by grouping hashtags together into single API calls
* Removed caching functionality (store/get) until next feature release as it creates too much traffic and needs careful planning/thinking through
* Removed extra AJAX request for configuration
* Fixed bug with bullets appearing for each tweet in some themes
* Added ability to over-ride sources when using tb_archive template tag - now each profile can show tweets for one user and an index page can show a regular blend from all users.
* Added better handling of connection limit - shows when it will be reset in verbal time e.g. "next reset in 12 minutes"
* Added ability to disable the archive page (note: you'll need to manually delete existing one to remove it from navigation)

= 2.0.5 =
* Created work around for widget not starting if other plugins have JavaScript errors (e.g. Flickr Manager plugin)
* Added new template tag tb_archive() that allows to manually create an archive tweet list on any page
* Fixed issue with tweet source not appearing after "from" if tweet source has un-encoded HTML in it (e.g. TweetDeck link)
* Adjusted CSS so that tweets are not shifted to the right if your theme overrides padding for list items
* Fixed "NaN years ago" error in timestamp that appeared in Internet Explorer 6, 7 and 8
* Fixed problem with archive page not being created or linked to
* Replaced jquery.timeago library with own code and reduced page load by 3Kb
* Tested and ensured compatibility with WordPress 2.8.1

= 2.0.4 =
* Fixed "Warning: Missing argument 1 for tb_widget()" error when trying to include plugin using template code instead of widget tool in the admin

= 2.0.3 =
* Fixed "Cannot retrieve Tweet Blender configuration options" error thrown on some servers

= 2.0.2 =
* Added message that's shown if sources are valid but have no tweets
* Fixed bug in links for hashtags in sources screen (shown if you click on Twitter logo)
* Added this Changelog to plugin page on WordPress
* Added feature to disable refresh button if configuration has not been loaded

= 2.0.1 =
* Fixed CSS so no border is shown around refresh icon - it appeared on some sites before
* Updated installation instructions to clarify where sources are defined

= 2.0.0 =
* Complete overhaul of the widget shifting blending functionality from PHP to JavaScript
* Added web services for configuration and cache management
* Added refresh icon for manual refresh
* Simplified CSS
* Improved loading time and decoupled widget from the rest of the page so it doesn't hold up loading
* Fixed checking for connection limit and fixed "Tweets temporary unavailable" error
* Switched to different configuration management technique
* Added Tweet Blender logo to admin page