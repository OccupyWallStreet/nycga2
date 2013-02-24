=== Plugin Name ===
Contributors: Rocky1983
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=paypal%40amegrant%2ehu&lc=HU&item_name=Diamond%20Multisite%20WordPress%20Widget&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_SM%2egif%3aNonHosted
Tags: recent post, network, multisite, widget, recent comments, MU, WPMU, sidebar, broadcast, copy post, share post, network post, rss, rss feed, recent post rss, network rss, bloglist, sub-blog list, blog list
Requires at least: 3.0
Tested up to: 3.1
Stable tag: 1.8

Bloglist, recent posts and comments from the whole network.

== Description ==
Bloglist and recent posts and comments from the whole network<br />

Features:<br />
- You can choose the entries count<br />
- You can format the entries easily with short codes and html tags <br />
- You can set custom datetime format<br />
- You can use it on sub-blogs<br />
- Blacklist/Whitelist<br />
- Avatar support<br />
- Posts/Pages shortcode support (read more at the admin page)<br />
- RSS Recent Post feed<br />
- Cache<br />
<br />
Broadcast Post On The Network<br />
- In the publish box, you can copy your post to the network's sub-blogs<br />
<br />
Translations<br />
- Hungarian<br />
- Japanese (<a href="http://staff.blog.bng.net/" target="_blank">Chestnut</a>)<br />
<br />
if you have any question write me an e-mail to daniel.bozo@amegrant.hu
<br />
if you want to work with me <a href="http://www.odesk.com/referrals/track/rocky1983?redir=http%3A%2F%2Fwww.odesk.com%2Fusers%2F%7E%7E62d462b7f34dbdb4" target="_blank">Hire me on oDesk!</a>

== Changelog ==
= 1.8 =
- Whitelist shortcodes now works fine
- You can add 'min_post_count' parameter to the posts shortcode
- The recent posts and comments does not list default 'Hello World!' posts and default comments
- You can use order="4" to achieve the new sort by comment count functionallity (You can also set comment_age = "7" to count the comments in the last 7 days)
- You can use {avatar} in the format of the posts shortcode
- You can also use {blog_link} and {blog_url} in the recent posts
- 'Widget title' field bugfix on Settings page
- You can limit the number of posts in bloglist (post_limit in shortcodes)
- Bugfix: User was unable to set cache life to 0 on settings page
- Changed cache storage from options to files
- Changed substr() function calls to mb_subsrt() - multibite-safe substring
- Add #comment-commentnumber to the end of the links in Recent Comments widget and shortcode
- Recent comments now only displays comments, no trackbacks and pingbacks

= 1.7.7 =
- 'By Post Count' bugfix

= 1.7.6 =
- Format string HTML bugfix
- 'By Post Count' order option to bloglist

= 1.7.5 =
- Blogs' Whitelist

= 1.7.1 =
- Bloglist default format fix

= 1.7 =
- Cache

= 1.6.1 =
- Bloglist Exclude blogs bugfix

= 1.6 =
- Bloglist functionality

= 1.5.5 =
- Japanese translation
- Feed template correction

= 1.5.4 =
- HTML errors on the admin page fixed

= 1.5.3 =

- Feed fix
- Read more shordcode support and fix

= 1.5.2 =

- CSS commit problem

= 1.5.1 =

- RSS feed bugfix

= 1.5 =

- RSS feed support
- Admin UI
- Read more link bugfix

= 1.4 =

- Posts/Pages shortcode support
- Refactor the render mechanism

= 1.3.1 =

- {post-title}, {post-title_txt} schortcodes added to diamond recent comments widget

= 1.3 =

-  Broadcast post

= 1.2.3 =

- Avatar size bugfix

= 1.2.2 =

- Custom datetime format

= 1.2.1 =

- Avatar size disappears after save bugfix
- 'Read more' link shorcode documentation added

= 1.2 =

- Avatar support
- 'Read more' link added
- Excerpt support

= 1.1 =

- Now you can use it on sub-blogs

