=== TinyMCE Excerpt ===
Contributors: simonwheatley
Donate link: http://www.simonwheatley.co.uk/wordpress/
Tags: tinymce, rich text editor, excerpt
Requires at least: 2.2.3
Tested up to: 3.0rc
Stable tag: 1.33

Enables rich text editing on the excerpt field.

== Description ==

This is a simple plugin that enables rich text editing on the excerpt field.

If you have any plugins installed which alter TinyMCE, e.g. to add additional 
buttons, they will also be applied to the excerpt editor.

Any issues: [contact me](http://www.simonwheatley.co.uk/contact-me/).

== Change Log ==

= v1.32 2010/04/30 =

* Bug fixes from Eric Zhang (thanks!)
* Should now be compatible with Google's (Living Stories kit for WordPress)[http://code.google.com/p/living-stories/wiki/WordpressInstallation]

= v1.31 2010/05/07 =

* Fix: Fixed some issues whereby some JS was being called on pages other than the post edit page. Refactored some code, no additional functionality.

= v1.3 2008/04/08 =

* Fix: Now works with WordPress 2.5. Thanks to [J Bradford Dillon](http://www.jbradforddillon.com/web-development/tinymce-excerpt-for-25/) 
for reporting the issue & providing some code. Retains backwards compatibility for previous versions of WordPress.

= v1.2 2007/11/23 =

* Fix: Thanks to [Jascha Ephraim](http://www.jaschaephraim.com/) for spotting 
and providing code to fix an issue where the excerpt content wasn't 
auto-paragraphised. It is now.

==Known issues / bugs==

Please [report any issues](http://www.simonwheatley.co.uk/contact-me/) that you find.

* In WordPress versions prior to WordPress 2.5, when you click the "advanced toolbar" button, the advanced toolbar shows up on the 
Content, rather than the excerpt. Reported by [Jorge Villalobos](http://hypenotic.com/)
* You can’t send images to the excerpt editor from the edit page file browser; 
you have to send them to the main editor, then copy and paste.
* To show the excerpt you have to use a template which uses 
[the_excerpt](http://codex.wordpress.org/Template_Tags/the_excerpt), rather 
than [the_content](http://codex.wordpress.org/Template_Tags/the_content).

== Requests ==

I'm simply noting requests here, I've not necessarily looked into how possible any of these are or how much effort they might require.

* It would be great if you could switch between TinyMCE and Quicktags as the content does. Requested by [Jorge Villalobos](http://hypenotic.com/)

== Installation ==

1. Upload `tinymce_excerpt.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Edit a post and enjoy the new rich styles in your excerpts

== Screenshots ==

1. WordPress 2.5: Showing the TinyMCE Editor on the optional excerpt field, ready for editing.
2. Pre WordPress 2.5: Showing the TinyMCE Editor on the optional excerpt field, ready for editing.
