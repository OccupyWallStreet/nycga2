=== Revision History ===
Contributors: jkeyes
Donate link: http://keyes.ie/wordpress/donate/
Tags: revision
Requires at least: 2.8.6
Tested up to: 2.9
Stable tag: 0.9.1

Revision History displays the revision history of a post or page, with links 
to the content of each revision.

== Description ==

Revision History displays the revision history of a post or page, with links 
to the content of each revision.

The revision history is appended to the `content` of the post or page.

The following are configuration settings which can be changed in the Revision
History submenu of 'Settings'.

1. Display on pages.
2. Display on posts.
3. Show autosave revisions.
4. Show the revision timestamp in the post title.

By default the revision history is not shown on any posts or pages.

For control over where you place the revision history use the `rh_the_revision` function.
It takes optional parameters, before and after.  For example:

`<?php rh_the_revision('<h4>', '</h4>'); ?>`

== Installation ==

1. Upload `version-history.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the directory of the stable readme.txt, so in this case, `/tags/4.3/screenshot-1.png` (or jpg, jpeg, gif)
2. This is the second screen shot

== Changelog ==

= 0.9.1 =
  * Layout uses same markup as standard settings pages.
  * Now use the blog date and time format as specified in General Settings.
  * Allow a CSS class to be set for the revision string in the post title.
  * Added `rh_the_revision` function.

= 0.9 =
  * First version.
