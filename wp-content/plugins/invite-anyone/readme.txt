=== Plugin Name ===
Contributors: boonebgorges, cuny-academic-commons
Donate link: http://teleogistic.net/donate
Tags: buddypress, invitations, group, invite, friends, members
Requires at least: WP 3.2, BuddyPress 1.2.9
Tested up to: WP 3.2.1, BuddyPress 1.5
Stable tag: 1.0.7

Makes BuddyPress's invitation features more powerful.

== Description ==

Invite Anyone has two components:

1) The ability to invite members to the site by email. The plugin creates a tab on each member's Profile page called "Send Invites", which contains a form where users can invite outsiders to join the site. There is a field for a custom message. Also, inviters can optionally select any number of their groups, and when the invitee accepts the invitation he or she automatically receive invitations to join those groups.

The email invitation part of the plugin is customizable by the BP administrator, via Dashboard > BuddyPress > Invite Anyone.

2) By default, BuddyPress only allows group admins to invite their friends to groups. In some communities, you might want members to be able to invite non-friends to groups as well. This plugin allows you to do so, by populating the invitation checklist with the entire membership of the site, rather than just a friend list.

Because member lists can get very long and hard to navigate, this plugin adds a autosuggest search box to the Send Invites screen - the same one that appears on the Compose Message screen - which allows inviters to navigate directly to the members they want to invite.

Invite Anyone features optional integration with CloudSponge http://cloudsponge.com, a premium address book service, that allows your users to invite their friends to the site in a way that's easy and fun. Enable it at Dashboard > BuddyPress > Invite Anyone.

== Installation ==

* Upload the directory '/invite-anyone/' to your WP plugins directory and activate from the Dashboard of the main blog. Some users have reported problems when activating the plugin sitewide, so consider activating it on the BP blog only.
* Configure the plugin at Dashboard > BuddyPress > Invite Anyone, where you can customize the default invitation message, determine which members are allowed to invite by email, and more. If you are running WordPress Multisite version 3.1 or greater, BuddyPress Dashboard panels are found in the Network Admin area.
* If you are upgrading from a version of Invite Anyone older than 0.8, your data will be migrated from a custom database table (usually wp_bp_invite_anyone) to WordPress custom post types. After upgrading, if you are satisfied that the automatic migration has gone successfully, you can safely archive and remove the wp_bp_invite_anyone table from your database.

== Translation credits ==

* Dutch: Jesper Popma
* French: Guillaume Coulon
* German: Lars Berning
* Greek: Lena Stergatou
* Italian: Luca Camellini
* Norwegian: Stig Ulfsby
* Russian: Jettochkin
* Spanish: Mauricio Camayo, Gregor Gimmy

== Changelog ==

= 1.0.7 =
* Another attempt at fixing problem that some users are having when activating plugin

= 1.0.6 =
* Fixes bug that showed invitation message on registration screen when no invitation was found
* Fixes bug that allowed users to bypass registration lock on some setups
* Fixes some PHP notices

= 1.0.5 =
* Adds filters to some settings fields
* Removes repeated sent_email_invite action
* Adds updated Spanish translation

= 1.0.4 =
* Fixes errant autocomplete for group invitations
* Prevents 404s for shadow image in autocomplet. Props defunctl
* Fixes issue with Remove Invite link for items added with AJAX. Props defunctl

= 1.0.3 =
* Removes unneeded code block. Props defunctl

= 1.0.2 =
* Fixes syntax error that caused "invalid header" errors when activated on some setups

= 1.0.1 =
* Fixes problem that prevented settings from being saved properly on 1.2.x
* Fixes Settings link on Plugins page

= 1.0 =
* Compatibility with BuddyPress 1.5
* Rewritten autocomplete script for group invitations
* Adds Spanish translation

= 0.9.3 =
* Fixed some PHP warnings on Manage Invitations and Stats panels
* Added a 'no invitations' message to Manage Invitations when none have been sent yet

= 0.9.2 =
* Fixed bug that caused settings from being properly saved in some cases

= 0.9.1 =
* Updated .pot file for translators

= 0.9 =
* Revamped admin screens, including admin view of all sent invites and stats about invitations
* Improved support for sorting by accepted date and by email address
* Pagination added to Sent Invites screen
* Fixed bug that caused Send Invites button to appear incorrectly on group create screen in some cases
* Fixed bug that caused group create form not to submit in some browsers

= 0.8.9 =
* Fixed bug that made Cloudsponge scripts load even when CS integration was turned off
* Fixed bug that prevented Cloudsponge authorization to happen because of a problem in script loading order
* Cleaned up markup in the admin panel

= 0.8.8 =
* Added an icon to the custom post type. Props Bowe for whipping it up
* Fixed bug that caused sent invites to be recorded as sent at GMT rather than properly offset for time zone
* Refactored the widget to use a single email box, like the regular invites page
* Put the CloudSponge link into the widget

= 0.8.7 =
* Fixed bug where an undeclared global was causing the custom post type not to be loaded on multisite installations

= 0.8.6 =
* Fixed bug that made update nav appear on non-root-blogs of Multisite installations
* Fixed bug that made CPT register on non-root-blogs on Multisite, which meant that there was a confusing empty BuddyPress Invitations section

= 0.8.5 =
* Moved group invitations tab content into a separate template file so that it can be easily overridden in a theme (with a file at [theme]/groups/single/invite-anyone.php).

= 0.8.4 =
* Fixed bug that caused update nag to show after updating options in some cases

= 0.8.3	=
* Improved support for migrating large numbers of legacy invitations

= 0.8.2 =
* Adds German translation - props Lars Berning

= 0.8.1 =
* Fixes the way admin menus are hooked to ensure compatibility with WP Multisite < 3.1 and BP < 1.2.8

= 0.8 =
* Integration with CloudSponge, which allows users to pull email addresses from their address books for sending email invitations
* Sent invitation data has been converted to custom post types, which gives admins an easy way to manage invitations from the WP Dashboard

= 0.7.1 =
* Norwegian translation added - props Stig Ulfsby
* Greek translation added - props Lena Stergatou
* Fixed bug that made group creation bypass IA settings
* Fixed bug that broke the way that the BP core (friends only) tab rendered

= 0.7 =
* Big markup improvements to email invitations screens - huge props hnla
* Toggle to allow email invitation and registration when general registration is turned off

= 0.6.7 =
* Added hooks to provide support for Achievements
* Improved checking for deactivated components
* BuddyPress Followers support

= 0.6.6 = 
* Updated hooks to work with more recent versions of BuddyPress
* Increased number of results returned to user on group invite autocomplete

= 0.6.5 =
* Workaround for "headers already sent" issue on group invites
* Fixed a number of variable type problems with email invitation pages

= 0.6.4 =
* Fixed bug that kept item group invitations from being sent
* Fixed bug that prevented Send Invites profile tab from being hidden when access control was set to Administrator

= 0.6.3 =
* Fixed bug that showed non-activated users in group invitation list on some instances of single WP
* Fixed bug that limited number of displayed groups on invite by email screen
* Cleaned up the appearance of the group list on the invite by email screen
* Fixed bug that may have cause foreach problem in email invitation

= 0.6.2 =
* Fixed bug that kept group invitation member list from being populated on some non-MU setups
* Fixed bug that kept non-admins from seeing Send Invites group tab
* Fixed bug that prevented JS and CSS from loading on invitation step in group creation
* Fixed bug that caused email fields not to load properly in IE - thanks, techguy!
* Added do_action hooks for other plugins (eg Cubepoints) to access
* Added filter on acceptance URL and action hook before accept invitation screen for plugins to access

= 0.6.1 =
* Added checks to allow email invitations to work when groups component is disabled
* Fixed l18n bugs with error messages
* French translation added - thanks, Guillaume!
* Russian translation added - thanks, Jettochkin!
* Updated translations

= 0.6 =
* Plugin now includes a widget for email invitation from any page
* Sent Invites sortable by email address, date invited, date accepted
* Invites can be cleared from Sent Invites list: individually, all accepted, all invitations
* Created admin controls over who group admins/mods/members can invite to groups
* Admins can now allow customization of invitation's main message but still have control over a non-editable footer
* CSS issues fixed

= 0.5.2 =
* Added Italian translation (thanks, Luca!)
* Removed "Want to invite..." prompt from Send Invites screen during group creation
* Attempted a fix for certain in_array errors in css/js loader file

= 0.5.1 =
* Fixed bug with subject/message content when email is returned as an error
* Fixed error with email error messages when no groups were selected
* Changed width of textareas on Invite New Members tab

= 0.5 =
* Enabled Opt Out option for invitees
* Subject line is now customizable by the admin
* Admin can toggle whether users can customize subject line and message body of invitation emails
* Some localization bugs fixed
* Filtered spammers from group invitation list
* Fixed bug that may have caused problems with some MU limited email domain lists
* Email Address field is now auto-populated on Accept Invitation screen
* Created admin toggle for group invites attached to email screen
* Added hook for additional fields on Invite New Members screen (as well as a hook for processing the additional data)

= 0.4.1 =
* Fixed problem with email validation causing fatal errors on single WP
* Fixed bug that allows members to see Send Invites tab on profiles other than their own

= 0.4 =
* New feature: Invite by email from new Send Invites profile tab
* Links from group invite pages to Invite By Email page
* Removed "Send Invites" button during group creation

= 0.3.5 =
* Corrected localization function (d'oh)
* Added Dutch translation - thanks, Jesper!

= 0.3.4 =
* Added POT file and localization function

= 0.3.3 =
* Fixed bug that kept non-active users from appearing in member list

= 0.3.2 =
* Made it possible to use the plugin with friends component turned off
* Turned off Site Wide Only to remove PHP errors on some subdomain blogs

= 0.3.1 =
* Added a "successfully created" message when no invites are sent on group creation

= 0.3 =
* Compatibility with BP 1.2.1, including new bp-default theme
* Rearranged files to ensure BP is loaded before plugin is

= 0.2 =
* Compatibility with BP 1.2 trunk
* Bugfixes regarding file locations

= 0.1 =
* Initial release
