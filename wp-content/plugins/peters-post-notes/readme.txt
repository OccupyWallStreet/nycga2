=== Plugin Name ===
Contributors: pkthree
Donate link: http://www.theblog.ca
Tags: post, notification, admin, collaboration, workflow, posts
Requires at least: 2.8
Tested up to: 3.5
Stable tag: trunk

Add notes to the "edit post" and "edit page" sidebars. Collaborators can also share notes on the WordPress dashboard.

== Description ==

Add notes on the "edit post" and "edit page" screens' sidebars in WordPress 2.8 and up. When used with [Peter's Collaboration E-mails](http://www.theblog.ca/wordpress-collaboration-emails "From Peter's Useful Crap") 1.2 and up, the notes are sent along with the e-mails in the collaboration workflow.  There is also a general and private notes system on the dashboard.

= Features =

On its own, this plugin adds a panel to the sidebar of the add and edit post / page screens so that users can add notes for themselves or others and keep track of these notes. Whenever you save a post, you can type a note to be displayed along with the post in the edit view.

On the dashboard, there's also a summary of the most recent notes. By default this shows notes by all people on relevant posts / pages. There is also a general and private notes system.

For an illustrated explanation on how the plugin works with [Peter's Collaboration E-mails](http://www.theblog.ca/wordpress-collaboration-emails "From Peter's Useful Crap") to send e-mails with the notes, see [this page](http://www.theblog.ca/wordpress-post-notes "From Peter's Useful Crap").

= Translations =

* fr\_FR translation by Denis Rebaud
* pt\_BR translation by Murillo Ferrari
* es\_ES translation by Karin Sequen
* ja translation by Kazuhiro Terada
* pl\_PL translation by Michal Rozmiarek
* nl\_NL translation by Rene of http://wpwebshop.com
* sv\_SE translation by Karin Lindholm
* ru\_RU translation by Alexander Maltsev
* tr\_TR translation by Berkay Unal of http://www.berkayunal.com
* da\_DK translation by Lars Andersen
* de\_DE translation by Tobias Karnetzke
* lt\_LT translation by Vincent G of http://www.host1free.com
* it\_IT translation by Ludo

= Requirements =

* WordPress 2.8 or higher

== Installation ==

Unzip the peters\_post\_notes folder to your WordPress plugins folder. It should work out of the box, but you can tweak some settings in the plugin file itself if needed.

== Frequently Asked Questions ==

Please visit the plugin page at http://www.theblog.ca/wordpress-post-notes with any questions.

== Changelog ==

= 1.5.0 =
* 2013-01-24: Allow editing of plugin settings via the WordPress admin interface so that settings persist after upgrades.

= 1.4.1 =
* 2013-01-10: Support dates formatted according to locale (Thanks Alexander!)

= 1.4.0 =
* 2013-01-09: Added setting $ppn_general_notes_required_capability to control who can post general notes on the dashboard. Made plugin SSL compatible (thanks llch!). Also, minor code cleanup.

= 1.3.1 =
* 2011-08-13: Minor code cleanup to remove unnecessary error notices.

= 1.3.0 =
* 2011-07-03: Added "Latest note" column to the manage posts view.

= 1.2.0 =
* 2010-08-02: Added a couple of settings so that you can grant specific roles and/or capabilities the ability to edit and delete any note. Also added a setting to allow basic HTML in notes.

= 1.1.0 =
* 2010-04-24: Added option to move "add note" box for posts to the notes window. Added a couple of settings so that you grant only specific roles and/or capabilities the ability to view all collaboration notes. Added support for custom post types. Also fixed a couple of bugs with line breaks and pagination on general notes.

= 1.0.8 =
* 2010-04-11: Fixed bug where line breaks weren't preserved when first adding a note. (Thanks SNURK!)

= 1.0.7 =
* 2010-04-02: Added a check in the "save note" function to prevent the same note from being posted twice in a row.

= 1.0.6 =
* 2010-01-11: Plugin now removes its database tables when it is uninstalled, instead of when it is deactivated. This prevents the notes from being deleted when upgrading WordPress automatically.

= 1.0.5 =
* 2009-11-24: More efficient loading of notes if there are no relevant posts for the current user.

= 1.0.4 =
* 2009-09-20: Fixed a bug in date translations. (Thanks Denis!)

= 1.0.3 =
* 2009-09-19: Fixed a bug in the query to show other users' posts on the dashboard. (Thanks martijn!)  Also added proper code call to support translations. (Thanks dreb!)

= 1.0.2 =
* 2009-06-27: Fixed a display compatibility issue within the WordPress post form.

= 1.0.1 =
* 2009-06-23: Fixed minor issue where general notes database table wasn't being created on some installs.

= 1.0.0 =
* 2009-04-08: Added general and private notes system on the dashboard. Fixed UTF-8 encoding and line breaks in notes.

= 0.3 =
* 2009-01-17: Added "Notes" window to pages.  Also added an option (in this plugin file) for the Dashboard "Notes" window: show either all notes by everybody, notes by everybody on relevant posts / pages, and notes by other people on relevant posts / pages.

= 0.2 =
* 2008-12-28: Added ability for users to edit and delete their own notes. Uses Ajax, so JavaScript must be enabled in your browser.

= 0.1 =
* 2008-11-10: First release.