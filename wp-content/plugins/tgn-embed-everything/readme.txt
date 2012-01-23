=== TGN Embed everything ===
Contributors: georgevanous
Tags: youtube,pdf,xls,ppt,doc,spreadsheet,excel,powerpoint,tiff,word,videoreadr,vimeo,metacafe,dailymotion,video,microsoft,google,docs
Tested up to: 3.1
Stable tag: 1.0.31
Requires at least: 3.0.1

Easily embed YouTube, PDF, Google docs, spreadsheets, PowerPoint, Word, VideoReadr, TIFF and more into WordPress 3! For example: [youtube WDLgEyJ3SlE]


== Description ==

(Copy all this into a new WordPress post to see it in action!)

= IFrame =

[iframe tgn.tv 604 500]

* Insert any website into yours!

= YouTube =

http:// www.youtube.com/watch?v=WDLgEyJ3SlE

* Just paste the raw YouTube URL on its own line to see the video! (Remove the first "http://" so "http://www.yout..." is on its own line - had to do this otherwise wordpress.org shows the video!)

* Use the shortcode for setting width, height and autoplay: [youtube WDLgEyJ3SlE 604 364 autoplay=1]

* Supports all YouTube options at http://code.google.com/apis/youtube/player_parameters.html

= PDF (without Flash!) =

[pdf tgnbooks.com/the-great-gatsby.pdf 604 700]

= PowerPoint (without Flash!) =

[powerpoint tgnbooks.com/best-caricatures.ppt 604 700]

= Google spreadsheet =

[spreadsheet 0AtFXq8XyejTEdGNEZm1NZUJ3V3QwXzIwZnQ1ckh0MlE 604 300]

* Show any sheet: [spreadsheet 0AtFXq8XyejTEdGNEZm1NZUJ3V3QwXzIwZnQ1ckh0MlE 604 300 sheet=2]
* NOTE: You must "Publish" the spreadsheet in Google Docs by clicking "Share > Publish as a web page" to see it in WordPress!

= Google document =

[document 1t0C9HghMO4ttIKbUYxVC2sn5nXi9hXldVS1okjWzTP4 604 300]

= Vimeo =

[vimeo http:// vimeo.com/2481023]

* Remove the space between "http://" and "vimeo.com" (Purposely added to prevent rendering the video on wordpress.org!)

= Metacafe =

[metacafe http://www.metacafe.com/watch/1203580 ]

= Dailymotion =

[dailymotion http://www.dailymotion.com/video/xg7km9 ]

= VideoReadr =

[videoreadr Uz7fOLDr2JM nmrdsxtb 604 370]

= TIFF =

[tiff tgnbooks.com/electronic-circuit.tif 604 840]

= Questions? =

Want to embed something else or have a question?

Contact the author: george@thewpwiki.com! (I respond to every email)

= Is this useful to you? =

If you like this plugin, consider contributing to its further development donating to the author!

Click
https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=J34QEF6Q43KF6


== Installation ==

To install this extension

  1. Put tgn-embed-everything.php into /wp-content/plugins/

	2. Activate the plugin

	3. Start using the shortcodes in your posts


== Frequently asked questions ==

= What is TGN? =

TGN is THEGAMENET, creating communities 10,000 users at a time! (and 1 million YouTube views a week!) See www.thegamenet.com

= What is YouTube? =

YouTube lets you broadcast yourself.

It is the world's best and free video hosting service. Upload your videos and share them with your friends or the world!

= What is VideoReadr? =

VideoReadr enhances YouTube videos with bookmarkes, transcripts and a full scripting language

See
www.videoreadr.com


== Changelog ==

= 1.0.31 =
* Fixed YouTube embed code to be valid XHTML using http://blog.martincrockett.com/general/valid-youtube-and-vimeo-code/ (thanks Petr Cibulka!)

= 1.0.30 =
* Plain YouTube URLs now must be on their own line (so you can describe the URL without it changing into the video!)

= 1.0.29 =
* Added support for plain YouTube URLs like http://www.youtube.com/watch?v=WDLgEyJ3SlE and the same wrapped in <a href="> (no need for shortcode!)

= 1.0.28 =
* YouTube videos now don't show other people's videos at the end. To show other videos, add "rel=1" to the shortcode. For example: [youtube Py5qo0lXtz8 rel=1]

= 1.0.27 =
* Added support for more video sites: Vimeo, Metacafe and Dailymotion! (See the Description for examples showing how to use them)

= 1.0.26 =
* Raised max spreadsheet rows from 100 to 10,000!

= 1.0.25 =
* Added support to show a different sheet in Google Spreadsheets (just add to the end "sheet=2" to show second sheet)

= 1.0.24 =
* PDFs now display in IE (thanks Michael Klusek for pointing out the fix in http://www.google.com/support/forum/p/Google+Docs/thread?tid=22d92671afd5b9b7)

= 1.0.23 =
* Updated the YouTube player to the latest version 3 (now, embedded videos play exactly like on youtube.com, including videoreadrs!)
* Added jQuery check that dynamically loads jQuery if it is not detected by the end of &lt;head>

= 1.0.22 =
* Added the [document] shortcode for Google documents and updated the Description with an example

= 1.0.21 =
* Updated VideoReadr to comply with HTML5 requirements, prefixing all custom attribute names with "data-"

= 1.0.20 =
* Added support for [iframe] shortcode to display any webpage in your post
* YouTube videos can now be hidden by pull-down menus and other HTML (added wmode=opaque to the object and embed elements)
* Updated VideoReadr to support decoupling YouTube from VideoReadr, but must now add YouTube ID to shortcode, like [videoreadr Uz7fOLDr2JM nmrdsxtb]

= 1.0.19 =
* Added all public YouTube API parameters at http://code.google.com/apis/youtube/player_parameters.html

= 1.0.18 =
* Discovered a bug where the auto-linkify feature broke the WordPress image [caption] feature - disabled auto-linkify to fix this

= 1.0.17 =
* Added YouTube autoplay option! Just add "autoplay=1" to the shortcode, like [youtube WDLgEyJ3SlE autoplay=1]

= 1.0.16 =
* Added PowerPoint example to Description
* Changed default YouTube video to "First look at LOTRO (Lord of the Rings Online) free-to-play" - to see it, type [youtube] in any WordPress post

= 1.0.15 =
* Re-enabled linkify, now correctly ignores shortcodes like [show http://tgnbooks.com/the-great-gatsby.pdf]
* Added more shortcode intelligence to support common paste issues like [show&nbsp;http://tgnbooks.com/the-great-gatsby.pdf]

= 1.0.14 =
* Disabled linkify until we find a solution that avoids linkifying [show http://tgnbooks.com/the-great-gatsby.pdf]

= 1.0.13 =
* Automatically linkify URL and email addresses in plain text (can be clicked)
* Changed YouTube embed to use the new YouTube iframe code (uses HTML5 player if your browser supports it) - http://apiblog.youtube.com/2010/07/new-way-to-embed-youtube-videos.html

= 1.0.12 =
* Improved documentation

= 1.0.11 =
* Added scrollbars for spreadsheets that do not fit in the viewing area

= 1.0.10 =
* Removed border around PDFs in IE

= 1.0.9 =
* Added support for TIFF images

= 1.0.8 =
* Increased default height of spreadsheets to the same as PDFs

= 1.0.7 =
* Added new [spreadsheet] shortcode to show published Google spreadsheets
* http:// is now optional, eg: [show tgnbooks.com/the-great-gatsby.pdf]

= 1.0.6 =
* Updated code comments

= 1.0.5 =
* Updated description to be more clear

= 1.0.4 =
* Replaced [view] with the more descriptive [show]
* Added VideoReadr Javascript fix to define $ if it wasn't already set to jQuery

= 1.0.3 =
* Updated tags in readme.txt to include all supported file types

= 1.0.2 =
* Updated readme.txt to include all supported file types in the description

= 1.0.1 =
* To avoid naming conflicts, prefixed all functions and globals with tgn_ee_ (TGN Embed everything)

= 1.0.0 =
* Initial release
