=== Unconfirmed  ===
Contributors: boonebgorges, cuny-academic-commons
Donate link: http://teleogistic.net/donate
Tags: multisite, network, activate, activation, email
Requires at least: WordPress 3.1 
Tested up to: WordPress 3.5
Stable tag: 1.2.2

Allows WordPress admins to manage unactivated users, by activating them manually, deleting their pending registrations, or resending the activation email.

== Description ==

If you run a WordPress or BuddyPress installation, you probably know that some of the biggest administrative headaches come from the activation process. Activation emails may be caught by spam filters, deleted unwillingly, or simply not understood. Yet WordPress itself has no UI for viewing and managing unactivated members.

Unconfirmed creates a Dashboard panel under the Users menu (Network Admin > Users on Multisite) that shows a list of unactivated user registrations. For each registration, you have the option of resending the original activation email, or manually activating the user.

== Installation ==

1. Install
1. Activate
1. Navigate to Network Admin > Users > Unconfirmed 

== Changelog ==

= 1.2.2 =
* Fixes pagination count for non-MS installations

= 1.2.1 =
* Better support for WP 3.5

= 1.2 =
* Adds 'Delete' buttons to remove registrations
* Adds support for non-MS WordPress + BuddyPress

= 1.1 =
* Adds bulk resend/activate options
* Adds a Resent Count column, to keep track of how many times an activation email has been resent to a given user
* Refines the success/failure messages to contain better information
* Updates Boone's Pagination and Boone's Sortable Columns

= 1.0.3 =
* Removes Boone's Sortable Columns plugin header to ensure no conflicts during WP plugin activation

= 1.0.2 =
* Adds language file
* Fixes problem with email resending feedback related to BuddyPress

= 1.0.1 =
* Adds pagination styling

= 1.0 =
* Initial release
