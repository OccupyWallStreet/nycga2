=== Plugin Name ===
Contributors: fredericktownes
Tags: user experience, cache, caching, page cache, css cache, js cache, db cache, disk cache, disk caching, database cache, http compression, gzip, deflate, minify, cdn, content delivery network, media library, performance, speed, multiple hosts, css, merge, combine, unobtrusive javascript, compress, optimize, optimizer, javascript, js, cascading style sheet, plugin, yslow, yui, google, google rank, google page speed, mod_pagespeed, s3, cloudfront, aws, amazon web services, cloud files, rackspace, cotendo, max cdn, limelight, cloudflare, microsoft, microsoft azure, iis, nginx, litespeed, apache, varnish, xcache, apc, eacclerator, wincache, mysql, w3 total cache, batcache, wp cache, wp super cache, buddypress
Requires at least: 2.8
Tested up to: 3.2.1
Stable tag: 0.9.2.4

Improve site performance and user experience via caching: browser, page, object, database, minify and content delivery network support.

== Description ==

The **most complete** WordPress performance framework.

Recommended by web hosts like: MediaTemple, Host Gator, Page.ly and WP Engine and countless more.

Trusted by countless sites like: stevesouders.com, mattcutts.com, mashable.com, smashingmagazine.com, makeuseof.com, yoast.com, kiss925.com, pearsonified.com, lockergnome.com, johnchow.com, ilovetypography.com, webdesignerdepot.com, css-tricks.com and tens of thousands of others.

W3 Total Cache improves the user experience of your site by improving your server performance, caching every aspect of your site, reducing the download times and providing transparent content delivery network (CDN) integration.

An inside look:

http://www.youtube.com/watch?v=rkmrQP8S5KY

Benefits:

* At least 10x improvement in overall site performance (Grade A in [YSlow](http://developer.yahoo.com/yslow/) or significant [Google Page Speed](http://code.google.com/speed/page-speed/) improvements) **when fully configured**
* Improved conversion rates and "[site performance](http://googlewebmastercentral.blogspot.com/2009/12/your-sites-performance-in-webmaster.html)" which [affect your site's rank](http://googlewebmastercentral.blogspot.com/2010/04/using-site-speed-in-web-search-ranking.html) on Google.com
* "Instant" subsequent page views: browser caching
* Optimized progressive render: pages start rendering quickly
* Reduced page load time: increased visitor time on site; visitors view more pages
* Improved web server performance; sustain high traffic periods
* Up to 80% bandwidth savings via minify and HTTP compression of HTML, CSS, JavaScript and feeds

Features:

* Compatible with shared hosting, virtual private / dedicated servers and dedicated servers / clusters
* Transparent content delivery network (CDN) integration with Media Library, theme files and WordPress itself
* Mobile support: respective caching of pages by referrer or groups of user agents including theme switching for groups of referrers or user agents
* Caching of (minified and compressed) pages and posts in memory or on disk or on CDN (mirror only)
* Caching of (minified and compressed) CSS and JavaScript in memory, on disk or on CDN
* Caching of feeds (site, categories, tags, comments, search results) in memory or on disk or on CDN (mirror only)
* Caching of search results pages (i.e. URIs with query string variables) in memory or on disk
* Caching of database objects in memory or on disk
* Caching of objects in memory or on disk
* Minification of posts and pages and feeds
* Minification of inline, embedded or 3rd party JavaScript (with automated updates)
* Minification of inline, embedded or 3rd party CSS (with automated updates)
* Browser caching using cache-control, future expire headers and entity tags (ETag) with "cache-busting"
* JavaScript grouping by template (home page, post page etc) with embed location control
* Non-blocking JavaScript embedding
* Import post attachments directly into the Media Library (and CDN)

Improve the user experience for your readers without having to change WordPress, your theme, your plugins or how you produce your content.

== Frequently Asked Questions ==

= Why does speed matter? =

Speed is among the most significant success factors web sites face. In fact, your site's speed directly affects your income (revenue) &mdash; it's a fact. Some high traffic sites conducted research and uncovered the following:

* Google.com: **+500 ms** (speed decrease) -> **-20% traffic loss** [[1](http://home.blarg.net/~glinden/StanfordDataMining.2006-11-29.ppt)]
* Yahoo.com: **+400 ms** (speed decrease) -> **-5-9% full-page traffic loss** (visitor left before the page finished loading) [[2](http://www.slideshare.net/stoyan/yslow-20-presentation)]
* Amazon.com: **+100 ms** (speed decrease) -> **-1% sales loss** [[1](http://home.blarg.net/~glinden/StanfordDataMining.2006-11-29.ppt)]

A thousandth of a second is not a long time, yet the impact is quite significant. Even if you're not a large company (or just hope to become one), a loss is still a loss. However, there is a solution to this problem, take advantage.

Search engines like Google, measure and factor in the speed of web sites in their ranking algorithm. When they recommend a site they want to make sure users find what they're looking for quickly. So in effect you and Google should have the same objective.

= Why is W3 Total Cache better than other cache plugins? =

**It's a complete framework.** Many of the popular cache plugins available do a great job at achieving some performance aims. Our plugin remedies numerous performance reducing aspects of any web site going far beyond merely reducing CPU usage (load) and bandwidth consumption for HTML pages alone. Equally important, the plugin requires no theme modifications, modifications to your .htaccess (mod_rewrite rules) or programming compromises to get started. Most importantly, it's the only plugin designed to optimize all practical hosting environments small or large. The options are many and setup is easy.

= I've never heard of any of this stuff; my site is fine, no one complains about the speed. Why should I install this? =

Rarely do readers take the time to complain. They typically just stop browsing earlier than you'd prefer and may not return altogether. This is the only plugin specifically designed to make sure that all aspects of your site are as fast as possible. Google is placing more emphasis on the [speed of a site as a factor in rankings](http://searchengineland.com/site-speed-googles-next-ranking-factor-29793); this plugin helps with that too.

It's in every web site owner's best interest is to make sure that the performance of your site is not hindering its success.

= Which web servers do you support? =

We are aware of no incompatibilities with [apache](http://httpd.apache.org/) 1.3+, [nginx](http://wiki.nginx.org/) 0.7+, , [IIS](http://www.iis.net/) 5+ or [litespeed](http://litespeedtech.com/products/webserver/overview/) 4.0.2+. If there's a web server you feel we should be actively testing (e.g. [lighttpd](http://www.lighttpd.net/)), we're [interested in hearing](mailto:wordpressexperts@w3-edge.com).

= Which WordPress versions are supported? =

To use all features in the suite, a minimum of version WordPress 2.8 with PHP 5 is required. Earlier versions will benefit from our Media Library Importer to get them back on the upgrade path and into a CDN of their choosing.

= Who do you recommend as a CDN (Content Delivery Network) provider? =

That depends on how you use your site and where most of your readers read your site (regionally). Here's a short list:

* [MaxCDN](http://bit.ly/pXZ4t1)
* [EdgeCast](http://bit.ly/bIjSWC)
* [Amazon Cloudfront](http://bit.ly/ao1sGt)
* [Rackspace Cloud Files](http://bit.ly/9hpX8T)
* [VPS NET (Level 3)](http://bit.ly/d5hfFt)
* [Cotendo](http://bit.ly/bnVs0a)
* [Limelight Networks](http://bit.ly/aCW04j)
* [Akamai](http://bit.ly/a5GBLV)

= What about comments? Does the plugin slow down the rate at which comments appear? =

On the contrary, as with any other action a user can perform on a site, faster performance will encourage more of it. The cache is so quickly rebuilt in memory that it's no trouble to show visitors the most current version of a post that's experiencing Digg, Slashdot, Drudge Report, Yahoo Buzz or Twitter effect.

= Will the plugin interfere with other plugins or widgets? =

No, on the contrary if you use the minify settings you will improve their performance by several times.

= Does this plugin work with WordPress in network mode? =

Indeed it does.

= Does this plugin work with BuddyPress (bbPress)? =

Yes.

= Will this plugin speed up WP Admin? =

Yes, indirectly - if you have a lot of bloggers working with you, you will find that it feels like you have a server dedicated only to WP Admin once this plugin is enabled; the result, increased productivity.

= Which web servers do you support? =

We are aware of no incompatibilities with [apache](http://httpd.apache.org/) 1.3+, [IIS](http://www.iis.net/) 5+ or [litespeed](http://litespeedtech.com/products/webserver/overview/) 4.0.2+. If there's a web server you feel we should be actively testing (e.g. [lighttpd](http://www.lighttpd.net/)), we're [interested in hearing](http://www.w3-edge.com/contact/).

= Is this plugin server cluster and load balancer friendly? =

Yes, built from the ground up with scale and current hosting paradigms in mind.

= What is the purpose of the "Media Library Import" tool and how do I use it? =

The media library import tool is for old or "messy" WordPress installations that have attachments (images etc in posts or pages) scattered about the web server or "hot linked" to 3rd party sites instead of properly using the media library.

The tool will scan your posts and pages for the cases above and copy them to your media library, update your posts to use the link addresses and produce a .htaccess file containing the list of of permanent redirects, so search engines can find the files in their new location.

You should backup your database before performing this operation.

= How do I find the JS and CSS to optimize (minify) them with this plugin? =

Use the "Help" button available on the Minify settings tab. Once open, the tool will look for and populate the CSS and JS files used in each template of the site for the active theme. To then add a file to the minify settings, click the checkbox next to that file. The embed location of JS files can also be specified to improve page render performance. Minify settings for all installed themes can be managed from the tool as well by selecting the theme from the drop down menu. Once done configuring minify settings, click the apply and close button, then save settings in the Minify settings tab.

= I don't understand what a CDN has to do with caching, that's completely different, no? =

Technically no, a CDN is a high performance cache that stores static assets (your theme files, media library etc) in various locations throughout the world in order to provide low latency access to them by readers in those regions.

= What if I don't want to work with a CDN right now, is there any other use for this feature? =

Yes! You can take advantage of the [pipelining](http://www.mozilla.org/projects/netlib/http/pipelining-faq.html) support in some browsers by creating a sub-domain for the static content for your site. So you could select the "Origin Push / Self-hosted" method of the General Settings tab. Create static.domain.com on your server (and update your DNS zone) and then specify the FTP details for it in the plugin configuration panel and you're done. If you disable the scripting options on your server you'll find that your server will actually respond slightly faster from that sub-domain because it's just sending files and not processing them.

= How do I use an Origin Pull (Mirror) CDN? =
Login to your CDN providers control panel or account management area. Following any set up steps they provide, create a new "pull zone" or "bucket" for your site's domain name. If there's a set up wizard or any troubleshooting tips your provider offers, be sure to review them. In the CDN tab of the plugin, enter the hostname your CDN provider provided in the "replace site's hostname with" field. You should always do a quick check by opening a test file from the CDN hostname, e.g. http://cdn.domain.com/favicon.ico. Troubleshoot with your CDN provider until this test is successful.

Now go to the General tab and click the checkbox and save the settings to enable CDN functionality and empty the cache for the changes to take effect.

= How do I configure Amazon Simple Storage Service (Amazon S3) or Amazon CloudFront as my CDN? =

First [create an S3 account](http://aws.amazon.com/); it may take several hours for your account credentials to be functional. Next, you need to obtain your "Access key ID" and "Secret key" from the "Access Credentials" section of the "[Security Credentials](http://aws-portal.amazon.com/gp/aws/developer/account/index.html?action=access-key)" page of "My Account." Make sure the status is "active." Next, make sure that "Amazon Simple Storage Service (Amazon S3)" is the selected "CDN type" on the "General Settings" tab, then save the changes. Now on the "Content Delivery Network Settings" tab enter your "Access key," "Secret key" and enter a name (avoid special characters and spaces) for your bucket in the "Create a bucket" field by clicking the button of the same name. If using an existing bucket simply specify the bucket name in the "Bucket" field. Click the "Test S3 Upload" button and make sure that the test is successful, if not check your settings and try again. Save your settings.

Unless you wish to use CloudFront, you're almost done, skip to the next paragraph if you're using CloudFront. Go to the "General Settings" tab and click the "Enable" checkbox and save the settings to enable CDN functionality. Empty the cache for the changes to take effect. If preview mode is active you will need to "deploy" your changes for them to take effect.

To use CloudFront, perform all of the steps above, except select the "Amazon CloudFront" "CDN type" in the "Content Delivery Network" section of the "General Settings" tab. When creating a new bucket, the distribution ID will automatically be populated. Otherwise, proceed to the [AWS Management Console](https://console.aws.amazon.com/cloudfront/) and create a new distribution: select the S3 Bucket you created earlier as the "Origin," enter a [CNAME](http://docs.amazonwebservices.com/AmazonCloudFront/latest/DeveloperGuide/index.html?CNAMEs.html) if you wish to add one or more to your DNS Zone. Make sure that "Distribution Status" is enabled and "State" is deployed. Now on "Content Delivery Network" tab of the plugin, copy the subdomain found in the AWS Management Console and enter the CNAME used for the distribution in the "CNAME" field.

You may optionally, specify up to 10 hostnames to use rather than the default hostname, doing so will improve the render performance of your site's pages. Additional hostnames should also be specified in the settings for the distribution you're using in the AWS Management Console.

Now go to the General tab and click the "Enable" checkbox and save the settings to enable CDN functionality and empty the cache for the changes to take effect. If preview mode is active you will need to "deploy" your changes for them to take effect.

= How do I configure Rackspace Cloud Files as my CDN? =

First [create an account](http://www.rackspacecloud.com/cloud_hosting_products/files). Next, in the "Content Delivery Network" section of the "General Settings" tab, select Rackspace Cloud Files as the "CDN Type." Now, in the "Configuration" section of the "Content Delivery Network" tab, enter the "Username" and "API key" associated with your account (found in the API Access section of the [rackspace cloud control panel](https://manage.rackspacecloud.com/APIAccess.do)) in the respective fields. Next enter a name for the container to use (avoid special characters and spaces). If the operation is successful, the container's ID will automatically appear in the "Replace site's hostname with" field. You may optionally, specify the container name and container ID of an [existing container](https://manage.rackspacecloud.com/CloudFiles.do) if you wish. Click the "Test Cloud Files Upload" button and make sure that the test is successful, if not check your settings and try again. Save your settings. You're now ready to export your media library, theme and any other files to the CDN.

You may optionally, specify up to 10 hostnames to use rather than the default hostname, doing so will improve the render performance of your site's pages.

Now go to the General tab and click the "Enable" checkbox and save the settings to enable CDN functionality and empty the cache for the changes to take effect.  If preview mode is active you will need to "deploy" your changes for them to take effect.

= My YSlow score is low because it doesn't recognize my CDN, what can I do? =

Rule 2 says to use a content delivery network (CDN). The score for this rule is computed by checking the hostname of each component against the list of known CDNs. Unfortunately, the list of "known CDNs" are the ones used by Yahoo!. Most likely these are not relevant to your web site, except for potentially yui.yahooapis.com. If you want an accurate score for your web site, you can add your CDN hostnames to YSlow using Firefox's preferences. Here are the steps to follow:

* Go to about:config in Firefox. You'll see the current list of preferences.
* Right-click in the window and choose New and String to create a new string preference.
* Enter extensions.yslow.cdnHostnames for the preference name.
* For the string value, enter the hostname of your CDN, for example, mycdn.com. Do not use quotes. If you have multiple CDN hostnames, separate them with commas.

If you specify CDN hostnames in your preferences, they'll be shown under the details for Rule 2 in the Performance view.

= What is the purpose of the "modify attachment URLs" button? =

If the domain name of your site has changed, this tool is useful in updating your posts and pages to use the current addresses. For example, if your site used to be www.domain.com, and you decided to change it to domain.com, the result would either be many "broken" images or many unncessary redirects (which slow down the visitor's browsing experience). You can use this tool to correct this and similar cases. Correcting the URLs of your images also allows the plugin to do a better job of determining which images are actually hosted with the CDN.

As always, it never hurts to back up your database first.

= Is this plugin comptatible with TDO Mini Forms? =

Captcha and recaptcha will work fine, however you will need to prevent any pages with forms from being cached. Add the page's URI to the "Never cache the following pages" box on the Page Cache Settings tab.

= Is this plugin comptatible with GD Star Rating? =

Yes. Follow these steps:

1. Enable dynamic loading of ratings by checking GD Star Rating -> Settings -> Features "Cache support option"
1. If Database cache enabled in W3 Total Cache add `wp_gdsr` to "Ignored query stems" field in the Database Cache settings tab, otherwise ratings will not updated after voting
1. Empty all caches

= I see garbage characters instead of the normal web site, what's going on here? =

If a theme or it's files use the call `php_flush()` or function `flush()` that will interfere with the plugins normal operation; making the plugin send cached files before essential operations have finished. The `flush()` call is no longer necessary and should be removed.

= How do I cache only the home page? =

Add `/.+` to page cache "Never cache the following pages" option on the page cache settings tab.

= I'm getting blank pages or 500 error codes when trying to upgrade on WordPress in network mode =

First, make sure the plugin is not active (disabled) network-wide. Then make sure it's deactivated network-wide. Now you should be able to successful upgrade without breaking your site.

= This is too good to be true, how can I test the results? =
You will be able to see it instantly on each page load, but for tangible metrics, consider the following tools:

* [Mozilla Firefox](http://www.mozilla.com/firefox/) + [Firebug](http://getfirebug.com/) + [Yahoo! YSlow](http://developer.yahoo.com/yslow/)
* [Mozilla Firefox](http://www.mozilla.com/firefox/) + [Firebug](http://getfirebug.com/) + [Google Page Speed](http://code.google.com/speed/page-speed/)
* [Mozilla Firefox](http://www.mozilla.com/firefox/) + [Firebug](http://getfirebug.com/) + [Hammerhead](http://stevesouders.com/hammerhead/)
* [Google Chrome](http://www.google.com/chrome) + [Google Speed Tracer](http://code.google.com/webtoolkit/speedtracer/)
* [Pingdom](http://tools.pingdom.com/)
* [WebPagetest](http://www.webpagetest.org/test)
* [Gomez Instant Test Pro](http://www.gomez.com/instant-test-pro/)
* [Resource Expert Droid](http://redbot.org/)
* [Web Caching Tests](http://www.procata.com/cachetest/)
* [Port80 Compression Check](http://www.port80software.com/tools/compresscheck.asp)
* [A simple online web page compression / deflate / gzip test tool](http://www.gidnetwork.com/tools/gzip-test.php)

= I don't have time to deal with this, but I know I need it. Will you help me? =

Yes! Please [reach out to us](http://www.w3-edge.com/contact/) and we'll get you acclimated so you can "set it and forget it."

Install the plugin to read the full FAQ.

== Installation ==

1. Deactivate and delete any other caching plugin you may be using. Make sure wp-content/ and wp-content/uploads/ (temporarily) has 777 permissions before proceeding, e.g.: `# chmod 777 /var/www/vhosts/domain.com/httpdocs/wp-content/` using your web hosting control panel or your SSH account.
1. Login as an administrator to your WordPress Admin account. Using the "Add New" menu option under the "Plugins" section of the navigation, you can either search for: w3 total cache or if you've downloaded the plugin already, click the "Upload" link, find the .zip file you download and then click "Install Now". Or you can unzip and FTP upload the plugin to your plugins directory (wp-content/plugins/). In either case, when done wp-content/plugins/w3-total-cache/ should exist.
1. Locate and activate the plugin on the "Plugins" page. Page caching will **automatically be running** in basic mode. Set the permissions of wp-content and wp-content/uploads back to 755, e.g.: `# chmod 755 /var/www/vhosts/domain.com/httpdocs/wp-content/`.
1. Now click the "Settings" link to proceed to the "General" tab and select your caching methods for page, database and minify. In most cases, "disk enhanced" mode for page cache, "disk" mode for minify and "disk" mode for database caching are "good" settings.
1. *Recommended:* On the "Minify Settings" tab, all of the recommended settings are preset. Use the help button to simplify discovery of your CSS and JS files and groups. Pay close attention to the method and location of your JS group embeddings. See the plugin's FAQ for more information on usage.
1. *Recommended:* On the "Browser Cache" tab, HTTP compression is enabled by default. Make sure to enable other options to suit your goals.
1. *Recommended:* If you already have a content delivery network (CDN) provider, proceed to the "Content Delivery Network" tab and populate the fields and set your preferences. If you do not use the Media Library, you will need to import your images etc into the default locations. Use the Media Library Import Tool on the "Content Delivery Network" tab to perform this task. If you do not have a CDN provider, you can still improve your site's performance using the "Self-hosted" method. On your own server, create a subdomain and matching DNS Zone record; e.g. static.domain.com and configure FTP options on the "Content Delivery Network" tab accordingly. Be sure to FTP upload the appropriate files, using the available upload buttons.
1. *Recommended:* On the "Browser Cache" tab, HTTP compression is enabled by default. Make sure to enable other options to suit your goals.
1. *Optional:* On the "Database Cache" tab, the recommended settings are preset. If using a shared hosting account use the "disk" method with caution, the response time of the disk may not be fast enough, so this option is disabled by default. Try object caching instead for shared hosting.
1. *Optional:* On the "Object Cache" tab, all of the recommended settings are preset. If using a shared hosting account use the "disk" method with caution, the response time of the disk may not be fast enough, so this option is disabled by default. Test this option with and without database cache to ensure that it provides a performance increase.
1. *Optional:* On the "User Agent Groups" tab, specify any user agents, like mobile phones if a mobile theme is used. 

== What users have to say: ==

* Read [testimonials](http://bit.ly/6Wbvpt) from W3TC users.

== Press: Mentions, Tutorials &amp; Reviews ==

**August 2011:**

* [Matt Mullenweg: State of the Word 2011 (4:49)](http://wordpress.tv/2011/08/14/matt-mullenweg-state-of-the-word-2011/), Matt Mullenweg
* [W3 Total Cache Setup with CloudFlare and CDN : Complete Tutorial Guide](http://thecustomizewindows.com/2011/08/w3-total-cache-setup-with-cloudflare-and-cdn-complete-tutorial-guide/), Abhishek Ghosh

**July 2011:**

* [Speeding Up Your Blog - Part II: WordPress & Cloudflare Integration](http://www.thewebhostinghero.com/tutorials/wordpress-cloudflare.html)
* [How Your Website Loses 7% of Potential Conversions](http://www.clickz.com/clickz/column/2097323/website-loses-potential-conversions), Bryan Eisenberg
* [How to Integrate Google Page Speed with W3 Total Cache](http://geekscalling.com/google/how-to-integrate-google-page-speed-with-w3-total-cache), Anish
* [22 WordPress Plugins for Content Marketers](http://www.business2community.com/content-marketing/22-wordpress-plugins-for-content-marketers-040787), Brody Dorland

**June 2011:**

* [WordPress Optimization Results: Varnish/Nginx/APC + W3 Total Cache + Amazon S3 + CloudFlare](http://danielmiessler.com/blog/wordpress-optimization-results-varnishnginxapc-w3-total-cache-amazon-s3-cloudflare), Daniel Miessler
* [Case Study: WordPress, MaxCDN, CloudFlare and W3 Total Cache Integration](http://www.thewebhostinghero.com/articles/case-study-wp-maxcdn-cloudflare.html), Ritesh Sanap

**May 2011:**

* [Optimizing WordPress with Nginx, Varnish, APC, W3 Total Cache, and Amazon S3 (With Benchmarks)](http://danielmiessler.com/blog/optimizing-wordpress-with-nginx-varnish-w3-total-cache-amazon-s3-and-memcached), Daniel Miessler
* [Poll: Best Caching Plugin for WordPress?](http://digwp.com/2011/05/best-caching-plugin-wordpress/), Jeff Starr
* [Page Speed Online has a shiny new API](http://googlecode.blogspot.com/2011/05/page-speed-online-has-shiny-new-api.html), Andrew Oates and Richard Rabbat
* [Use W3 Total Cache to Speed Up Your WordPress Site](http://www.ostraining.com/blog/wordpress/w3-total-cache/), Steve Burge

**April 2011:**

* [Setting Up and Optimizing W3 Total Cache](http://tentblogger.com/w3-total-cache/), John Saddington
* [How To Configure The Various W3TC Plugin Settings For Your WordPress Blog](http://www.makeuseof.com/tag/configure-w3tc-plugin-wordpress/), James Bruce
* [Speeding Up Your WordPress Website: 11 Ways to Improve Your Load Time](http://wpmu.org/speeding-up-your-wordpress-website-11-ways-to-improve-your-load-time/), Siobhan Ambrose
* [Recipe for Baked WordPress](http://carpeaqua.com/2011/04/05/recipe-for-baked-wordpress/), Justin Williams
* [WordPress + W3 Total Cache + CDN story](http://translate.google.com/translate?hl=en&sl=auto&tl=en&u=http%3A%2F%2Fblog.gaspanik.com%2Factivate-cdn-option-on-w3totalcache), Mori Masako
* [SETTING UP W3 TOTAL CACHE PART 2](http://www.geekforhim.com/setting-up-w3-total-cache-part-2/), Matthew Snider
* [SETTING UP W3 TOTAL CACHE PART 1](http://www.geekforhim.com/setting-up-w3-total-cache-part-1/), Matthew Snider

**March 2011:**

* [WPML with W3TC for Fast and Efficient Multilingual Websites](http://wpml.org/2011/03/wpml-with-w3tc/), Amir

**February 2011:**

* [Optimizing WordPress with Nginx, Varnish, W3 Total Cache, Amazon S3, and Memcached (With Benchmarks)](http://danielmiessler.com/blog/optimizing-wordpress-with-nginx-varnish-w3-total-cache-amazon-s3-and-memcached), Daniel Miessler
* [My WordPress site loads in 2 seconds... does yours?](http://labsecrets.com/blog/2011/02/14/my-wordpress-site-loads-in-two-seconds-does-yours/)

**January 2011:**

* [11 Important Steps to Optimize WordPress and Increase Performance](http://www.bernskiold.com/2011/01/10/11-important-steps-to-optimize-wordpress-and-increase-performance/), Erik Bernskiold
* [Speed up WordPress with the W3 Total Cache Plugin](http://wplift.com/speed-up-wordpress-with-the-w3-total-cache-plugin), Oliver Dale
* [How to Make Your Blog Load Faster than ProBlogger](http://www.problogger.net/archives/2011/01/04/how-to-make-your-blog-load-faster-than-problogger/), Pro Blogger
* [WP Honors Winner, Free Plugin Category](http://wpcandy.com/reports/the-2010-wphonors-award-winners), WPCandy.com

**December 2010:**

* [Best blog plugins](http://www.blog.web6.org/best-blog-plugins/)
* [How To Make Your WordPress Blog Load Faster](http://www.johnchow.com/how-to-make-your-wordpress-blog-load-faster/), John Chow
* [Unleash the Power of WordPress Using Plugin Combos](http://freelancefolder.com/unleash-the-power-of-wordpress-using-plugin-combos/), Paul de Wouters
* [Rackspace Cloud Files for WordPress](http://sporkmarketing.com/blog/1095/rackspace-cloud-files-wordpress/), Jason Lancaster

**November 2010:**

* [Make your blog super fast with W3 Total Cache plugin](http://laspas.gr/2010/11/26/make-blog-super-fast-w3-total-cache-plugin/), Stratos Laspas
* [10 WordPress Plugins I'm Thankful For (And Cannot Live Without)](http://wpmu.org/10-wordpress-plugins-im-thankful-for-and-cannot-live-without/), Sarah Gooding
* [Subjective Results of Installing W3 Total Cache Plugin](http://www.codyhatch.com/administriva/subjective-results-of-installing-w3-total-cache-plugin/), Cody Hatch
* [13 Plugins Your WordPress Site Might Need](http://www.jonbishop.com/2010/11/13-plugins-your-wordpress-site-might-need/), Jon Bishop
* [Best WordPress Plugins that Marketers Use](http://www.nicoleonthenet.com/6390/best-wordpress-plugins-marketers-use/), Nicole Dean
* [WordPress Fat-Loss Diet to Speed Up & Ease Load](http://superbeachbody.com/12528/wordpress-fat-loss-diet-to-speed-up-ease-load/), Erhald Bergman
* [10 WordPress Plugins I'm Thankful For (And Cannot Live Without)](http://wpmu.org/10-wordpress-plugins-im-thankful-for-and-cannot-live-without/), Sarah Gooding
* [Subjective Results of Installing W3 Total Cache Plugin](http://www.codyhatch.com/administriva/subjective-results-of-installing-w3-total-cache-plugin/), Cody Hatch
* [W3 Total Cache Plugin](http://www.xenritech.com/w3-total-cache-plugin.html/)

**October 2010:**

* [20 Wordpress Plugins for Successful Internet Marketers](http://www.incomediary.com/20-wordpress-plugins-for-successful-internet-marketers/), Michael Dunlop
* [Failure Under Load](http://blog.hybridindie.com/2010/10/20/failure-load/), John Brien
* [W3 Total Cache and site response time (as measured by Pingdom)](http://www.pauldavidolson.com/blog/2010/w3-total-cache-and-site-response-time-as-measured-by-pingdom/), Paul David Olson
* [Overhauling WordPress Performance](http://brianegan.com/overhauling-wordpress-performance/), Brian Egan
* [How to Make WordPress Run Faster](http://www.stevecoursen.com/498/how-to-make-wordpress-run-faster/), Stephen Coursen
* [Give Your Wordpress Blog Lightning Fast Speeds With W3 Total Cache](http://www.makeuseof.com/tag/give-wordpress-blog-lightning-fast-speeds-w3-total-cache/), Steven Campbell
* [W3 Total Cache and site response time (as measured by Pingdom)](http://www.pauldavidolson.com/blog/2010/w3-total-cache-and-site-response-time-as-measured-by-pingdom/), Paul David Olson
* [11 Ways to Make Your WordPress Site Faster and Leaner](http://wpmu.org/11-ways-to-make-your-wordpress-site-faster-and-leaner/), Sarah Gooding
* [The Top 10 of Your Top 5 Plugins](http://weblogtoolscollection.com/archives/2010/10/04/the-top-10-of-your-top-5-plugins/), James Huff
* [Integrating memcached to wordpress](http://www.ruchirablog.com/intergrating-memcached-to-wordpress/), Ruchira Sahan
* [Make WordPress Faster (on the Rackspace Cloud)](http://www.mattytemple.com/2010/10/make-wordpress-faster-on-the-rackspace-cloud/), Matt Temple

**September 2010:**

* [Review: W3 Total Cache [WordPress Plugin]](http://sokkz.com/2010/09/29/review-w3-total-cache-wordpress-plugin/)
* [Plugins to Power-Up Your WordPress Installation](http://www.afiffattouh.com/web-design/plugins-to-power-up-your-wordpress-installation), Afif Fattouh
* [Reduce Page Loading Time by 300% With W3 Total Cache](http://c3mdigital.com/2010/09/reduce-page-loading-time-w3-total-cache/), Chris Olbekson
* [Performance Unleashed: How To Optimize Websites For Speed](http://diythemes.com/thesis/improve-website-pagespeed/), Willie Jackson
* [5 Best WordPress Plugins To Improve The Loading Speed Of a Blog](http://www.gadgetcage.com/2010/09/5-best-wordpress-plugins-to-improve-the-loading-speed-of-a-blog.html/)
* [WordPress Fat-Loss Diet to Speed Up & Ease Load](http://www.daljinskapodrska.com/wordpress-fat-loss-diet-to-speed-up-ease-load/)

**August 2010:**

* [WordPress Speed and Optimization Guide](http://thesocialmediaguide.com.au/2010/08/30/wordpress-speed-and-optimization-guide/), Matthew Tommasi
* [How to configure WordPress Blogs Search Engine Friendly](http://solvater.com/2010/09/how-to-configure-wordpress-blog-search-engine-friendly-complete-beginners-guide-for-wordpress-seo/), Arafath Hashmi
* [How to Install and Setup W3 Total Cache for Beginners](http://www.wpbeginner.com/plugins/how-to-install-and-setup-w3-total-cache-for-beginners/)
* [20 Most Useful WordPress Plugins](http://zemalf.posterous.com/20-most-useful-wordpress-plugins), Antti Kokkonen
* [Speed up, compress and optimise WordPress using W3 Total Cache](http://thisishelpful.com/speed-compress-optimise-wordpress-w3-total-cache.html)
* [W3 Total Cache - Further optimization of the blog](http://www.bhoffmeier.de/2010/08/21/w3-total-cache-weitere-optimierung-des-blogs/), Bernd Hoffmeier
* [W3 Total Cache Fixes Bugs, Adds Features with Update](http://www.whoishostingthis.com/blog/2010/08/04/first-draft-w3-total-cache-fixes-bugs-adds-features-with-update/), Jonathan
* [The Quickest Way To Make Your Blog Load Faster](http://www.peterleehc.com/blog/work-from-home/the-quickest-way-to-make-your-blog-load-faster), Peter Lee

**July 2010:**

* [Getting W3 Total Cache and a mobile plugin to work in WordPress](http://blog.trasatti.it/2010/07/getting-w3-total-cache-and-a-mobile-plugin-to-work-in-wordpress.html), Andrea Trasatti
* [Improve Your WordPress Performance With W3 Total Cache](http://maketecheasier.com/improve-wordpress-performance-with-w3-total-cache/2010/07/21), Damien Oh
* [Four Simple Steps For Big Gains In Page Speed](http://www.dailyblogtips.com/four-simple-steps-for-big-gains-in-page-speed/), Greg Hayes
* [How to use Content Delivery Network on Shared Hosting for WordPress](http://solvater.com/2010/07/content-delivery-network-shared-hosting-wordpress-configuration-with-w3-total-cache-in-wordpress/), Arafath Hashmi
* [How to Use Google Webmaster Tools to Diagnose and Improve WordPress Page Speed](http://wpmu.org/how-to-use-google-webmaster-tools-to-diagnose-and-improve-wordpress-page-speed/), Sarah Gooding
* [Caching Wordpress - Preparing Your Blog For The Mainstream](http://bradblogging.com/how-to/caching-wordpress-preparing-your-blog-for-the-mainstream/), Brad Ney
* [11 Ways to Speed Up WordPress](http://mashable.com/2010/07/19/speed-up-wordpress/), Cyrus Patten
* [How To Decrease Page Loading Time Of Your WordPress Blog By 75%](http://bloggingwithsuccess.net/decrease-loading-times), Ishan Sharma
* [Top 10 Wordpress Plugins which I use on DailyBlogging](http://www.dailyblogging.org/wordpress/top-10-wordpress-plugins-which-i-use-on-dailyblogging/), Mani Viswanathan
* [Install and Configure W3 Total Cache in 7 Easy Steps](http://zemalf.com/1443/w3-total-cache/), Antti Kokkonen
* [How to Reduce the Loading Time of Your Blog](http://www.admixweb.com/2010/07/09/how-to-reduce-the-loading-time-of-your-blog/), Rishabh Agarwal
* [5 Wordpress Plugins You Need To Know About](http://thenextweb.com/apps/2010/07/06/5-wordpress-plugins-you-need-to-know-about/), James Hicks

**June 2010:**

* [12 Ways to Improve Wordpress Page Load Time](http://myblog2day.com/12-ways-to-improve-wordpress-page-load-time.php), Lee Ka Hoong
* [Significantly Speed Up Your WordPress Blog in 9 Easy Steps](http://www.bloggingpro.com/archives/2010/06/21/significantly-speed-up-your-wordpress-blog-in-9-easy-steps/), Robyn-Dale Samuda
* [Speed 'Em Up: Wordpress &amp; W3 Total Cache](http://translate.google.com/translate?js=y&prev=_t&hl=en&ie=UTF-8&layout=1&eotf=1&u=http://www.andilicious.com/blog/1473/20100610/wordpress-beschleunigen-grundlagen-w3-total-cache-page-speed&sl=auto&tl=en),  Andi Licious

**May 2010:**

* [Make Your Blog 10x Faster With W3 Total Cache Plug-in](http://www.strictlyonlinebiz.com/blog/speed-up-wordpress-with-w3-total-cache/1231/), Udegbunam Chukwudi
* [xCache v1.3.0 Now Available](http://webcache.googleusercontent.com/search?q=cache%3Ahttp%3A%2F%2Fresellr.net%2Fxcache-now-available%2F&rls=com.microsoft:en-us&ie=UTF-8&oe=UTF-8&startIndex=&startPage=1&rlz=1I7GGIE_en&redir_esc=&ei=NO49TNaAFIH60wS2zuXLDg), Michael
* [Maximize WordPress and BuddyPress Performance With W3 Total Cache](http://wpmu.org/maximize-wordpress-and-buddypress-performance-with-w3-total-cache/), Sarah Gooding
* [Is Your Wordpress Blog Slow to Load?](http://homenotion.com/blog/blogs/is-your-wordpress-blog-slow-to-load/), Elizabeth McGee

**April 2010:**

* [WordPress Optimization: How I Reduced Page Load Time by 75%](http://www.kadavy.net/blog/posts/wordpress-optimization-dreamhost-rackspace/), David Kadavy
* [Top 10 Wordpress Plugins Your Blog Should Have (Video)](http://www.blogsuccessjournal.com/blog-tips-and-advice/wordpress-tips-advice/top-10-wordpress-plugins-your-blog-should-have-video/), Dan &amp; Jennifer
* [Super or Total? Money Talks But Cache Rules](http://website-in-a-weekend.net/website-maintenance/super-total-money-talks-cache-rules/), Dave Thackeray
* [W3 Total Cache, the most comprehensive cache plugin in WordPress](http://blogandweb.com/wordpress/w3-total-cache-plugin-cache-wordpress/), Francisco Oliveros
* [10 OF THE BEST WORDPRESS PLUGINS IN 2010](http://www.sitesketch101.com/best-wordpress-plugins), Nicholas Cardot

**March 2010:**

* [Howto: Speed up WordPress sites by using Amazon Cloudfront](http://www.jitscale.com/howto-speed-up-wordpress-sites-by-using-amazon-cloudfront/), Niek Waarbroek
* [Wordpress Cache Plugin Benchmarks](http://cd34.com/blog/scalability/wordpress-cache-plugin-benchmarks/), Chris Davies
* [Wordpress + W3 Total Cache + MaxCDN How-To](http://rackerhacker.com/2010/02/13/wordpress-w3-total-cache-maxcdn/), Major Hayden

**February 2010:**

* [Blog Building: How To Dramatically Speed Up Your WordPress Site with W3 Total Cache](http://nimopress.com/pressed/blog-building-how-to-dramatically-speed-up-your-wordpress-site-with-w3-total-cache/), Nicholas Ong
* [Wordpress + W3 Total Cache + MaxCDN How-To](http://rackerhacker.com/2010/02/13/wordpress-w3-total-cache-maxcdn/), Major Hayden
* [Utilizing W3 Total Cache](http://www.reviewkin.com/utilizing-w3-total-cache/), Anangga Pratama
* [Shared Hosting vs. Cloud Hosting](http://gregrickaby.com/2010/02/shared-hosting-vs-cloud-hosting.html), Greg Rickaby
* [My Thoughts on Premium Plugins](http://weblogtoolscollection.com/archives/2010/02/04/my-thoughts-on-premium-plugins/), Ronald Huereca
* [W3 Total Cache Plugin for Wordpress Eats WP Super Cache's Lunch!](http://human3rror.com/w3-total-cache-plugin-for-wordpress-eats-wp-super-caches-lunch/), John Saddington

**January 2010:**

* [WordPress Cacheing with W3 Total Cache](http://blog.whoishostingthis.com/2010/01/19/wordpress-cacheing-w3-total-cache/), Jonathan
* [Configuring W3 Total Cache for WordPress](http://translate.google.com/translate?js=y&prev=_t&hl=en&ie=UTF-8&layout=1&eotf=1&u=http://da.clausheinrich.com/w3-total-cache-wordpress/&sl=auto&tl=en)
* [Wordpress load test part 2 - amendment](http://loadimpact.com/blog/wordpress-load-test-part-2-amendment), Erik Torsner
* [Wordpress - Accelerate your site with W3 Total Cache](http://translate.google.com/translate?hl=en&sl=auto&tl=en&u=http://www.egonomik.com/2010/01/wordpress-w3-total-cache-ile-sitenizi-hizlandirin-sunucunuzu-rahatlatin/), Caner Phenix

**December 2009:**

* [WordPress Plugin &mdash; Best of 4 Caching Plugins](http://nimopress.com/pressed/wordpress-plugin-best-of-4-caching-plugins/), Nicholas Ong
* [Speed Up Your Blog With W3 Total Cache &amp; Amazon](http://www.freedomtarget.com/w3-total-cache-with-amazon-s3-and-cloudfront), Kevin McKillop
* [W3 Total Cache with Amazon S3 and CloudFront](http://kovshenin.com/archives/w3-total-cache-with-amazon-s3-and-cloudfront/), Konstantin Kovshenin

**November 2009:**

* [How to Boost Ad Revenue: Speed is Your Secret Weapon](http://blog.buysellads.com/2009/11/how-to-boost-ad-revenue-speed-is-your-secret-weapon/), Todd Garland

**October 2009:**

* [Plugin: WordPress Caching with CDN Integration](http://www.blogperfume.com/plugin-wordpress-caching-with-cdn-integration/)
* [8 Powerful Wordpress Plugins You Probably Don't Use But Should](http://www.smashingapps.com/2009/10/19/8-powerful-wordpress-plugins-you-probably-dont-use-but-should.html), AN Jay
* [Beyond Super Cache: W3 Total Cache](http://www.webmaster-source.com/2009/10/15/beyond-super-cache-w3-total-cache/), Matt Harzewski

**September 2009:**

* [Why Noupe.com is Loading So Much Faster?](http://209.85.129.132/search?q=cache:PgY8haU_0I4J:www.noupe.com/spotlight/why-noupe-com-is-loading-pretty-fast.html+http://www.noupe.com/spotlight/why-noupe-com-is-loading-pretty-fast.html&cd=1&hl=en&ct=clnk&gl=it), Noura Yehia

**August 2009:**

* [W3 Total Cache Plugin](http://dougal.gunters.org/blog/2009/08/26/w3-total-cache-plugin), Dougal Campbell

**July 2009:**

* [W3 Total Cache](http://weblogtoolscollection.com/pluginblog/2009/07/27/w3-total-cache/)

== Who do I thank for all of this? ==

It's quite difficult to recall all of the innovators that have shared their thoughts, code and experiences in the blogosphere over the years, but here are some names to get you started:

* [Steve Souders](http://stevesouders.com/)
* [Steve Clay](http://mrclay.org/)
* [Ryan Grove](http://wonko.com/)
* [Nicholas Zakas](http://www.nczonline.net/blog/2009/06/23/loading-javascript-without-blocking/)
* [Ryan Dean](http://rtdean.livejournal.com/)
* [Andrei Zmievski](http://gravitonic.com/)
* George Schlossnagle
* Daniel Cowgill
* [Rasmus Lerdorf](http://toys.lerdorf.com/)
* [Gopal Vijayaraghavan](http://t3.dotgnu.info/)
* [Bart Vanbraban](http://eaccelerator.net/)
* [mOo](http://xcache.lighttpd.net/)

Please reach out to all of these people and support their projects if you're so inclined.

== Changelog ==

= 0.9.2.4 =
* Added support for Microsoft SQL Server
* Added API support for MediaTemple ProCDN (EdgeCast)
* Added set_time_limit to self test
* Fixed LiteSpeed web server support
* Fixed native hostname 301 redirect
* Fixed redundant object origin push export
* Fixed WSOD (white screen of death) caused by minify in some hosting configurations
* Fixed text encoding in feeds
* Fixed incorrect mime-type in feeds (which caused feedburner anomalies)
* Fixed array to string notices
* Fixed expires header support for AWS
* Fixed minification of font-family
* Fixed object cache write issue in WP Admin
* Improved (reduced) memory utilization by up to 70%
* Improved disk enhanced page caching performance
* Improved object caching performance
* Improved activation reliability
* Improved reliability of minify auto mode
* Improved security (added nonces, no directory indexing, prevent direct file access)
* Improved compatibility with network based file systems

= 0.9.2.3 =
* Added additional CloudFlare IP range
* Fixed html tidy encoding
* Fixed NetDNA / MaxCDN purging
* Improved handling of markers in .htaccess files - easier upgrades
* Improved cache busting logic
* Improved numerous notifications and user interface behaviors
* Improved AWS S3 and Cloudfront reliability
* Improved reliability of minify auto mode

= 0.9.2.2 =
* Fixed minify directives, e.g.: "File param is missing," causing minify caching to fail
* Fixed document root detection for IIS server
* Fixed HTTP compression when using CloudFlare
* Fixed HTML validation with JavaScript embed tags
* Fixed fancy permalinks, sites with or without trailing slashes can now cache pages using disk enhanced
* Fixed appending WP_CACHE define into wp-config.php for some users
* Fixed path to JSON.php
* Fixed listing of buckets error with AWS S3
* Improved compatibility with WordPress SEO by Yoast, 404 error exception list sitemap value changed to: sitemap(_index|[0-9]+)?\.xml(\.gz)?

= 0.9.2.1 =
* Fixed existing installation upgrades: set minify to manual mode by default
* Fixed unsuccessful transfer queue button
* Fixed background in lightbox
* Fixed handling of local http requests being blocked on some hosts
* Disabled CDN for minify files when auto mode is selected and the CDN method is origin push

= 0.9.2 =
* Added support for nginx web server
* Added support for CloudFlare
* Added origin pull support for Amazon Cloudfront
* Added Microsoft Azure Storage support for CDN
* Added WinCache opcode cache support
* Added additional minifier engines for HTML, CSS and JS including: HTMLtidy, CSStidy, Closure Compiler, YUI Compressor
* Added Google Page Speed integration
* Added support for @import processing
* Added controls for page cache purging policy
* Added auto mode for minify (not compatible with CDN)
* Added support for set cookie domain setting
* Added reliability improvements for Amazon Web Services
* Added referrer group management for uniquely caching these cases
* Added Amazon S3 bucket location selection control
* Added support CNAMEs confguration support for Amazon Cloudfront
* Added purge tool
* Added support of custom wp-config.php location
* Added cache busting support
* Improved object caching performance when no plugins are active
* Improved non-blocking JS embedding implementation
* Improved reliability of CDN export operations
* Improved implementation of headers for all cache engines
* Improved minify help (recommendations) tool
* Improved handling of .htaccess directive changes
* Improved support of IIS web server
* Improved varnish support
* Fixed bugs with API changes with Rackspace Cloudfiles
* Fixed bugs with origin push content delivery network methods
* Fixed HTML encoding
* Fixed emptying cache for various cache keys
* Fixed rejected CDN file support
* Fixed HTTPS mode in WP Admin
* Fixed relative document root for disk enhanced page cache
* Fixed trailing slash for disk enhanced page cache
* Fixed minify template group settings being lost upon upgrade
* Fixed division by zero error
* Fixed object cache clones
* Moved browser cache rules to site root instead of document root

= 0.9.1.3 =
* Improved error messages with AWS S3 CDN
* Added SSL support for CDN
* Added control for CDN queue upload interval
* Added option for 404 file exceptions list in browser cache
* Added exception for NextGen Gallery flash image rotator to CDN settings
* Fixed external file imports
* Fixed document root detection for CDN
* Fixed minify file search
* Fixed bugs with AWS CloudFront distribution creation and saving
* Fixed Rackspace Cloud Files API

= 0.9.1.2 =
* Improved media library import compatibility
* Improved various notifications
* Changed expires implementation to last accessed instead of last modified
* Resolved Apache 1.3 compatibility issue
* Fixed issues with document root detection on some servers
* Fixed an issue with minification of script tags with HTML comments inside
* Fixed minify gzip compression
* Fixed cache-control headers
* Fixed empty fatal error notification on network activation
* Fixed minify when https is active
* Fixed fatal error upon activation when uploads path does not exist

= 0.9.1.1 =
* Added an additional notification to help users identify incomplete installations upon activation
* Reverted previous Cloud Files workaround
* Fixed preview mode buttons
* Fixed duplicate entries appearing when using minify help tool more than once
* Fixed browser cache rules generation for media files

= 0.9.1 =
* Improved Rackspace Cloud Files implementation
* Improved frequently asked questions implementation to support incomplete PHP distributions
* Fixed 500 Internal Server Error when upgrading with Disk enhanced mode enabled
* Fixed notification issues with preview mode
* Fixed an issue with fatal errors with minify and memcache(d) caching engine

= 0.9 =
* Added preview feature so all cache settings can be reviewed prior to deployment
* Added minify configuration wizard (help button on minify tab)
* Added "never cache the following pages" to database and object cache
* Added minify option to JavaScript embed after &lt;body&gt;
* Added minify error notifications
* Added drag and drop dependency resolution for minify CSS / JavaScript groups
* Added object caching
* Added option to automatically page cache prime (preload) with or without XML sitemap
* Added support for multiple CNAMEs to CDN
* Added support for minifcation of any respective theme installed a single site
* Added support for page caching of multiple themes for various user agent groups
* Added support for theme switching / redirection based on groups of user agents
* Added compatibility with WP Super Cache fragment caching method (disk basic mode only)
* Added HTTP compression and headers for AWS S3
* Added ignored comment stems field, with Google AdSense default value to HTML minify
* Added support for varnish purging
* Added Rackspace Cloud Files support
* Added native NetDNA / MaxCDN integration
* Added option to cache 404 pages
* Added changed files auto-upload to CDN
* Added option to handle 404 errors for static objects directly with the web server
* Added support for gravity forms to database cache default settings
* Added changed file auto-upload to CDN
* Database connection errors now return internal server (500) error response and are thus not cached
* Incomplete plugin installation / removal no longer generates public errors
* Unterminated string errors no longer display publicly
* Support tab improvements
* Install tab improvements
* Resolved conflicts with disk enhanced .htaccess directives insertion
* Improved compatibility with all mobile plugins
* Improved AWS reliability
* Improved browser caching support and management
* Improved directory management for disk caching methods for increased performance
* Improved handling of missing minify files
* Improved Media Library import
* Improved Multi Site support
* Improved SSL interoperability

= 0.8.5.2 =
* Added support for Bad Behavior plugin
* Added support for eAccelerator 0.9.5.3 and XCache opcode caches
* Added support for rewriting href attribute of anchors for images that use the CDN
* Added deflate http compression support to minify
* Added setting of file modification time after FTP upload
* Added check of file modification time and file size before FTP upload
* Added check of file hash before uploading to Amazon S3
* Added option to force replacement of uploaded attachments on CDN
* Added option to toggle feed minification
* Added combine only option for CSS minification to overcome invalid CSS files
* Added configuration file import / export
* Database caching now disabled by default to improve compatibility with some shared hosting environments
* Default page caching method now disk enhanced
* Improved HTTP Compression inter-operability
* Improved compatibility with Multi Site Manager
* Improved apache compatibility
* Improved .htaccess directives
* Improved header management for posts / pages
* Improved notifications
* Improved minify handling of external files
* Improved minify memory limit to avoid blank pages when document size is large
* Improved minify reliability by reducing comment removal options
* Improved Media Library Import versatility
* Improved reliability of plugin activation in WordPress MU
* Improved security handling for some operations
* Improved reliability of handling file names containing spaces in CDN functionality
* Improved non-blocking embedding reliability
* Improved memcached detection by supporting only PECL memcache
* Fixed disk enhanced method of page cache in WordPress MU
* Fixed false match of page cache files in disk enhanced method of page cache
* Fixed cron anomalies caused by caching of wp_options table
* Fixed missing trailing slash issue when using disk enhanced page caching
* Fixed auto-embed bug CSS and JS when already manually embedded

= 0.8.5.1 =
* Added option to CDN Settings to skip specified directories
* Added option to allow for full control of HTTP compression options for page cache (some WordPress installations have issues with deflate)
* Added sql_calc_found_rows to default auto reject SQL list
* Added more notification cases identified and configured
* Added new mobile user agents for Japanese market
* Page cache performance improvements for disk enhanced mode
* Improved FAQ and option descriptions
* Improved apache directives for minify headers
* Improved handling of redirects
* Improved name space to avoid issues with other plugins
* Improved handling of incomplete installations, caching runs with default options if custom settings file does not exist
* Fixed anomalies with memcached-client.php in some environments
* Fixed another interface bug with management of minify files
* Fixed minor bug with table column length for some MySQL versions
* Fixed minify bug with CRLF
* Fixed minify bug with handling of zlib compression enabled
* Fixed handling of pages with CDN Media Library import

= 0.8.5 =
* Added "enhanced" disk caching mode for page cache, a 160% performance improvement over basic mode
* Added disk caching as an option for Database Cache
* Added CDN support for Amazon S3 and CloudFront
* Added mobile user agent rejection and redirect fields to page cache for handling mobile user agents
* Added Submit Bug Report tab
* Added support for detection of custom templates for minify groups
* Added separate controls expiration time field for minify and page cache settings
* Added PHP4 Support Notification to handle fatal errors on activation
* Improved database caching by 45%
* Improved handling of cache-control HTML headers
* Improved handing of 3rd Party CSS file minification
* Improved media library import reliability
* Improved handling of `DOCUMENT_ROOT` on some servers
* Improved garbage collection routine
* Improved handling of `<pre>` and `<textarea>` minification
* Improved handling of regular expressions in custom file list in CDN settings
* Improved handling of media library attachments in RSS feeds
* Improved handing of subdomains for CDN settings
* Improved various notifications and error messages
* Improved optional .htaccess directives (located in /ini/_htaccess)
* Fixed JS minifcation saving group settings
* Fixed false positives for duplicate CSS or JS in minify settings
* Fixed bug causing settings to be lost on upgrade
* Fixed attachment URI when CDN mode enabled
* Fixed small bug with FTP upload when CDN Method is Mirror (Origin Pull)
* Fixed the URI for wlwmanfiest.xml when CDN enabled 
* Fixed handling of HTTPS objects according to options
* Fixed emptying disk cache under various obscure permutations
* Fixed handling of obscure open_basedir restrictions
* Fixed various bugs with emptying cache under various obscure permutations
* Fixed installations deeper than document root

= 0.8 =
* Added disk as method for page caching
* Added support for mirror (origin pull) content delivery networks
* Added options to specify minify group policies per template
* Added options for toggling inline CSS and JS minification to improve minify reliability
* Added option to update Media Library attachment hostnames (when migrating domains etc)
* Added "Empty Cache" buttons to respective tabs
* Added additional file download fallback methods for minify
* Improved cookie handling
* Improved header handling
* Improved reliability of Media Library import
* "Don't cache pages for logged in users" is now the default page cache setting
* Fixed minify bug with RSS feeds
* Fixed minify bug with rewriting of url() URI in CSS
* Addressed more page cache invalidity cases
* Addressed rare occurrence of PHP fatal errors when saving post or comments
* Addressed CSS bug on wp-login.php
* Addressed rare MySQL error when uploading attachments to Media Library
* Modified plugin file/directory structure
* Confirmed compatibility with varnish and squid

= 0.7.5.2 =
* Added warning dialog to minify tab about removal of query strings locally hosted object URIs
* Added empty (memcached) cache button to each tab
* Improved reliability of memcache flush
* Minified files now (optionally) upload automatically according to update interval (expiry time)
* Changed directory of minify working files to wp-content/w3tc-cache/
* Fixed parsing memcached server strings
* Fixed minify sometimes not creating files as it should
* Addressed WordPress network activation/deactivation issues
* Provided memcache.ini directives updated to improve network throughput

= 0.7.5.1 =
* Added memcached test button for convenience
* Added option to concatenate any script to header or footer with non-blocking options for scripts that cannot be minified (e.g. obfuscated scripts)
* Added options to concatenate JS files only in header or footer (for use with obfuscated scripts)
* Improved notification handling
* Improved compatibility with suPHP
* Improved reliability of Media Library Export
* Fixed database cache that caused comment counts to become out of date
* Fixed minor issue with URI with CDN functionality enabled
* Removed unnecessary minify options
* Minification error dialogs now disabled when JS or CSS minify settings disabled
* Normalized line endings with /n as per minify author's direction
* Resolved bug in the minify library preventing proper permission notification messages

= 0.7.5 =
* Added handling for magic_quotes set to on
* Fixed issue with auto-download/upgrade and additional error checking
* Fixed bug preventing minify working properly if either CSS or JS minification was disabled
* Improved handling of inline comments and JavaScript in HTML documents
* Improved handing of @import CSS embedding
* Addressed privilege control issue
* Resolved warnings thrown in various versions of WordPress
* Memcached engine logic modified to better support clustering and multiple memcached instances
* Eliminated false negatives in a number of gzip / deflate compression analysis tools
* Total plugin file size reduced

= 0.7 =
* Added minify support for URIs starting with /
* WordPress network mode support bug fixes
* Minor CDN uploader fixes
* Minor error message improvements

= 0.6 =
* Added "Debug Mode" listing all settings and queries with statistics
* Improved error message notifications
* Improved cache stability for large objects
* FAQ and installation instructions corrections/adjustments
* Support for multiple wordpress installations added
* Resolved bug in minification of feeds

= 0.5 =
* Initial release