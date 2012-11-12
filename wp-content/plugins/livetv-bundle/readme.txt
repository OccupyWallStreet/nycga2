=== liveTV Bundle ===
  
Contributors: KwarK
Donate link: http://kwark.allwebtuts.net/
Tags: gamer, livestreams, livestream, plugin, wordpress, own3d, twitch, justin, streaming, live stream, liveTV
Tested up to: 3.4.2
Stable tag: 1.3.1.1


LiveTV Bundle for WordPress. Live stream plugin for wordpress compatible with own3d.tv, twitch.tv, justin.tv


== Description ==

This plugin was developed for small gamers team / gamers team usage. Not for mass sharing.


Tools to coding/changing the css

* An Api extends folder.
* An Api extends menu.
* A complete FAQ http://wordpress.org/extend/plugins/livetv-bundle/faq/
* A "how to use"  - online documentation http://livetv.allwebtuts.net/
* All explicative tips for each option in your dashboard (the "?" on mouse over).
* A php file exemple for Api in folder `/extends/`
* A commented css if you don't use php/Api (just save your css for futur updates)


Description

* Create automatically a page with a loop of livestream thumbnails from your team members
* Create new roles or use wordpress default roles (all default roles except subscribers)
* View current game (for twitch only)
* View xplit/livestream message
* View live!since date and hour
* Display or not livestreams offline (on, off, off only on widget)
* Generate thumbnails list for each types (own3d, justin, twitch) and cnage the order
* Large view and normal view
* Cache system
* Sidebar widget
* Generate automatically one IRC quakenet under each livestream
* Change quakenet for each original chat (twitch, justin chat)
* Colorization system for text (all html color) and 3 general themes
* Optional: work also with different manual shortcodes for own3d/twitch/justin in articles or pages
* Optional: work also with different manual shortcodes for ustream/livestream in articles or pages
* Optional: work also with different manual shortcodes for original chat/quakenet IRC lonely
* Compatible with all recent major browser IE9, Safari, Chrome, Firefox, Opera
* en_US, en_EN, fr_FR included and original .pot, .mo, .po included and ready to translate
* Some pagination options added from 1.2.5
* Some limitation options added from 1.2.5


online documentation & live demo 

* http://livetv.allwebtuts.net/


For css bug or adaptative Work view FAQ 

* http://wordpress.org/extend/plugins/livetv-bundle/faq/


Why I seperated twitch/justin ? 

* During this development, people in Gamers circle says on some channels in test, justin could have its dedicated Api soon because Justin has more ressource servers. If it is the fact, the code is ready.


Compatibility with frontend profil from connexion plugin


* Currently option "themed-profil" from theme-my-login not supported. If you desire to use the plugin with theme-my-login, themed-profil option must be disable from tab options of theme-my-login. If you have a adaptative solution, I need that.


== Installation ==

* Upload 'livetv-bundle' to the '/wp-content/plugins/' directory
* To activated some parts, its very simple: You may use the part liveTV 2 ? Activate all sub-parts is required ( 0 + 1 ) + 2. View your dashboard plugins section and read each description for more information
* Define attributs and selector on pages options if the default registered options not be suitable for your theme.



== Screenshots ==

1. administration
2. frontend

== Frequently Asked Questions ==

= Api extends =


Now a dedicated folder exists to make your own extension or for loads your personal css/JQuery file

View exemple in /extends/extend.php and its `/**comments**/`. 

The folder and its php files are automaticaly listed and parsed by the plugin. 

Just create your own personal php file (view exemple in `/extends/` for more basics informations and make your first hook).

you may enqueue your script or your style and maybe before dequeue original css/script

dequeue the original style: http://codex.wordpress.org/Function_Reference/wp_dequeue_style

dequeue some original script: http://codex.wordpress.org/Function_Reference/wp_dequeue_script


= Fix css "large view" =


A message for developer for the 'Large view'. 

Currently, the best solution I have found to cheat on one maximum of themes and display a 'Large view' it is a simple css cheating.

This cheating is loaded on request 'large view' and stop the display of the sidebar and enlarge the `<div id='content'>`.

This cheating is in a light css file `/css/page-livetreams-hook.css`

If your class or ID of `<div main content>` and `<ul widgets list>` from your current theme have a exotic class for this div/ul, maybe your large view have a css bug.

Replace the content of this css file with the good class/ID from your theme for this div/ul

e.g.

`
@charset utf-8;

.themename-sidebar{ /* change .themename-sidebar by the good ID/Class of your sidebar container */
    display:none!important;
    overflow:hidden!important
}

.themename-content { /* change .themename-content by the good ID/Class of your content container */
    width:100%!important;
    margin: 0 auto;
    padding: 0;
}

#full-view-switcher {
    width:100%!important;
}
`

I look at the core of wordpress for a php function for the futur and I will added this function in a newest version for unset really the sidebar without css cheating for 'Large view'. Or enventually, a add_class function.


= To change the style of the widget with your own style =

I have added some class/ID for the span of informations @ the right side of each thumbnail (when channel is offline, and also when channel is online).

The class start with `w-*`

You may create a simple css load for the widget only when the page isn't your_livestream_page. A simple wordpress function is 

`
if(!is_page('your-livestream-page-ID')){
  wp_enqueue_style('your-personal-style-for-the-livestream-widget');
}
`

If you are a JQuery developer/passionate, to manipulate this class/ID/css, you may enqueue your script in the same way and maybe before dequeue original css/script 

dequeue the original style: http://codex.wordpress.org/Function_Reference/wp_dequeue_style

dequeue some original script: http://codex.wordpress.org/Function_Reference/wp_dequeue_script

`
if(!is_page('your-livestream-page-ID')){
  wp_enqueue_script('your-personal-script-for-the-livestream-widget');
}
`

You may personnalize also the widget only for the home page with is_home function

`
if(is_home()){
  wp_enqueue_style('another-personal-style-for-the-livestream-widget');
}
`

The same way for your JQuery script

`
if(is_home()){
  wp_enqueue_script('another-personal-script-for-the-livestream-widget');
}
`

More information here 

is page: http://codex.wordpress.org/Function_Reference/is_page

enqueue script: http://codex.wordpress.org/Function_Reference/wp_enqueue_script (view also wp_register_script)

enqueue style: http://codex.wordpress.org/Function_Reference/wp_enqueue_style (view also wp_register_style)

is home: http://codex.wordpress.org/Function_Reference/is_home


* When offline class/ID exemple

`
<span class="minithumb-twitch offline">
	<span class="minithumb-twitch-splash"><img src="http://localhost/wp-content/plugins/livetv-bundle/images/offline.png" alt="mask" class="bubble" oldtitle="twitch channel offline"></span>
	<span class="minithumb-twitch-thumb"><a href="http://localhost/livestream?liveview=live_4_twitch_2&amp;mode=normal"><img src="http://localhost/wp-content/plugins/livetv-bundle/images/thumblist-mask-offline.png" class="bubble" alt="twitch thumbnail" oldtitle="Channel par test venant de twitch" aria-describedby="ui-tooltip-33"></a></span>
	<span class="minithumb-twitch-info" style="color:#A8998B">
		<span class="w-off-viewers">Viewers: offline</span>
		<span class="w-off-user">user: test</span><span id="w-off-channel">channel: ironsquid</span>
		<span class="w-off-view">View: <a href="http://localhost/livestream?liveview=live_4_twitch_2&amp;mode=normal" class="bubble button" oldtitle="Changer pour la vue normal">Normal</a> <a href="http://localhost/livestream?liveview=live_4_twitch_2&amp;mode=full" class="bubble button" oldtitle="Changer pour la vue large">Large</a></span>
		<span class="w-off-live">Live: Offline</span>
	</span>
</span>
`

* When online class/ID exemple

`
<span class="minithumb-twitch">
	<span class="minithumb-twitch-splash"><img src="http://localhost/wp-content/plugins/livetv-bundle/images/offline.png" alt="mask" class="bubble" oldtitle="twitch channel offline"></span>
	<span class="minithumb-twitch-thumb"><a href="http://localhost/livestream?liveview=live_4_twitch_2&amp;mode=normal"><img src="http://localhost/wp-content/plugins/livetv-bundle/images/thumblist-mask-offline.png" class="bubble" alt="twitch thumbnail" oldtitle="Channel par test venant de twitch" aria-describedby="ui-tooltip-33"></a></span>
	<span class="minithumb-twitch-info" style="color:#A8998B">
		<span class="w-viewers">Viewers: 12345</span>
		<span class="w-user">user: test</span><span id="w-off-channel">channel: ironsquid</span>
		<span class="w-view">View: <a href="http://localhost/livestream?liveview=live_4_twitch_2&amp;mode=normal" class="bubble button" oldtitle="Changer pour la vue normal">Normal</a> <a href="http://localhost/livestream?liveview=live_4_twitch_2&amp;mode=full" class="bubble button" oldtitle="Changer pour la vue large">Large</a></span>
		<span class="w-live">Live! date and hours</span>
	</span>
</span>
`


= Tip dialog effect bug for some themes = 


I think maybe you have 2 load of q-tip (1.x version or adaptative work of this kind) loaded by your theme and eventually JQuery loaded by your theme from external static repository (Google). It is possible the problem comes from something like that: JQuery loaded 2 times or eventually q-tip css loaded 2 times or 2 different versions of q-tip.js loaded or eventually q-tip.js loaded 2 times. The plugin use the q-tip 2.0 version and the latest JQuery version included by wordpress (this plugin was developed under wordpress 3.3.2 version).

You may try to change one from theme or one from plugin. To changes the loading from the plugin, the good file is the file `plugin-livetv-display-lives.php` (to change or stop the load of JQuery or stop the original css q-tip file). but after some adaptative work is necessary. You may try also to stop the external load of JQuery from your theme (in your header.php file) and see if your theme made a bug for slider or other dependancies of JQuery from your theme.

If you decide to change the tip 'class' for dialog effect from the plugin and made some adaptative work (with the same class from your "q-tip" theme), is in the file `/page-frontend/page-livestreams.php` (just make a search on 'bubble').



== Upgrade Notice ==

* Upgrade this plugin manually to not have disturbance or error after the wp automatic upgrade.



== Changelog ==

= 1.3.1.1 =

* Change src link for thumbnails from own3d for their new link. Don't forget, the page is under task scheduled. This update/fix is visible only after the next schedule.

= 1.3.1 =

* Fixes cron schedule

= 1.3.0.2 =

* clarification for the help title dialog and some text in administration.
* New cron task to generate html is now correct (normally)
* The selected value `temporary off` for cache is no longer compatible and disappears
* Normally... the permalink for the widget builded by the cron task is now correct.
* Some minor bugs fixes

= 1.3.0.1 =

* Fixed the permalink on widget and passed this permalink argument to the scheduled task (to build correctly the cache for the widget).

= 1.3.0 =

* Now the plugin has a task scheduled to generate html cache
* The plugin serve one latest static html file to a last user before generating its cache when it's time to it.
* All values are now builded under php and returned in a long string (end of some echo values under [shortcode]...)

= 1.2.9.3 =

* General code improvement
* php shortcode part with more clean code for wordpress specification
* Cheating to debug own3d original chat (replacement option) without more code
* Adding an option to disable live streams loop on requesting a normal/large view
* Some files names changes (js file) for code improvement

= 1.2.9.2 =

* Replace "echo value" (after the buffer) by "return value" (some other plugins - compatibility problem)

= 1.2.9.1 =

* Minor bug fixes for Twitch chat height and title h3
* Adding global for IRC plugin part. If the plugin is deactivated, now the IRC zone and the Share zone under each live stream is disable.


= 1.2.9 =

* Adding basic validation with preg_match on user profil

= 1.2.8 =

* Bug fixes for Twitch/Justin Api from a latest comma generated for Api

= 1.2.7 =

* cleaning code - more comments
* Fixes widget offline
* css cleaning for front-end pagination buttons
* Add a div to englobe all html from the live streams page (to stop all css bug from themes)

= 1.2.6 =

* css fixes like one value in % (an oversight)

= 1.2.5 =

* Added option to define limitation of thumbnails by page with the new option `pagination`.
* Added option to define limitation by user to limit live stream update on its profil.
* Leave option 3 columns and added 2 new options to define thumbnails width & height
* Added pagination for frontend and profil
* Fix bug for twitch from one unnecessary escaping
* Code clarification for improve performance
* Added an alert when user requesting on one offline live stream
* Added "offline" also for own3d in urls
* Now absolutly all request is prepared
* escaping html and all urls with `esc_url` and `esc_html` from wp
* More square thumbnail in the thumbnails list to adapt more easily to more themes

= 1.2.4 =

* Clarification of options (in dashboard)
* Fix wp_name on live stream for futur moderation
* Fix bug counter where one empty live stream appears.

= 1.2.3 =

* Decrease datas requests on externals like twitch/justin Api
* Decrease request on wordpress database for thumbnails but plugin loose `user:` on thumbnail
* Fix latest post substr-utf-8-characters-missing (now truncat word in place of substr character)
* Delete unecessary doc in plugin folder
* Delete unecessary img in plugin folder

= 1.2.2 =

* Fix css for Firefox
* Fix flash object for own3d with Firefox

= 1.2.1 =

* Fix view button

= 1.2 =

* Add Api extends with a dedicated folder that is automatically parsed by the plugin.
* Now uninstallation event deletes all live streams from all users
* Supports more sharing and more live streams
* Delete option from profil was reviewed and more efficient
* Thumbnails loop was reviewed and more efficient

= 1.1 =

* Now option "change the order and display order" act also on each profil.
* Add support of original chat from own3d, twitch, justin.
* Add support of Ustream with shortcode.

= 1.0.1 =

* Fix special roles compatibility with some themes/plugins.
* Fix loop of thumbnails with option "wordpress default role".

= 1.0 =

* Original review