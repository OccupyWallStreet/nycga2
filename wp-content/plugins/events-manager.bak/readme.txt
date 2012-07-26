=== Events Manager ===  
Contributors: nutsmuggler, netweblogic    
Donate link: http://wp-events-plugin.com
Tags: events, event, event registration, event calendar, events calendar, event management, paypal, registration, ticket, tickets, ticketing, tickets, theme, widget, locations, maps, booking, attendance, attendee, buddypress, calendar, gigs, payment, payments, sports, 
Requires at least: 3.1
Tested up to: 3.2.1
Stable tag: 4.212

Fully featured event registration management including recurring events, locations management, calendar, Google map integration, booking management

== Upgrade Notice ==

For those upgrading from version 3 to 4, you must upgrade to version 3.0.98 manually and then to the latest version as usual. Please [see this screencast](http://wp-events-plugin.com/news/upgrade-issues-screencast-walkthrough/) for a walkthrough.
           
== Description ==

Events Manager is a full-featured event registration management solution for Wordpress based on the principles of flexibility, reliability and powerful features!

For documentation, tutorials and support please visit the [plugin website](http://wp-events-plugin.com/).

Main Features

* Easy event registration (single day with start/end times)
* Recurring and long (multi-day) event registration
* Bookings Management (including approval/rejections, export CVS, and more!)
* Multiple Tickets
* Multisite Support
* BuddyPress Support
 * Group Events
 * Personal Events
 * Activity Stream
 * more on the way
* Guest/Member event submissions (beta)
* SEO Permalinks
* Assign event locations and view events by location
* Event categories
* Easily create custom event attributes (e.g. dress code)
* Google Map integration straight out the box!
* Advanced permissions - restrict user management of events and locations.
* Sidebars to display your events and locations
* Calendaring widgets show your events in an AJAX calendar format
* Fine grained control of how every aspect of your events are shown on your site, easily modify templates from the settings pages and template files
* iCal Feed
* RSS Feeds
* Plenty of template tags and shortcodes for use in your posts and pages
* Actively maintained and supported
* Lots of documentation and tutorials
* Guest Event Submissions (new, beta)
* And much more!

We have just released a [pro add-on](http://wp-events-plugin.com/events-manager-pro/) which adds PayPal and priority support, as well as many more powerful upcoming features for those with serious eventing needs!

Events Manager was rewritten in 3.0 from the ground up with flexibility in mind. Through use of object oriented programming and exposing hooks and filters throughout the site, you can modify Events Manager just like you would Wordpress!

== Installation ==

Events Manager works like any standard Wordpress plugin, and requires little configuration to start managing events. If you get stuck, visit the our documentation and support forums.

Whenever installing or upgrading any plugin, or even Wordpress itself, it is always recommended you back up your database first!

= Installing =
 
1. If installing, go to Plugins > Add New in the admin area, and search for events manager.
2. Click install, once installed, activate and you're done!

Once installed, you can start adding events straight away, although you may want to visit the plugin site documentation and learn how to unleash the full power of Events Manager.

= Upgrading =

1. When upgrading, visit the plugins page in your admin area, scroll down to events manager and click upgrade.
2. Wordpress will help you upgrade automatically.

= Upgrading from 3.x to 4.x =

Please view [this page](http://wp-events-plugin.com/news/events-manager-4-0-released/) for information on upgrading events manager.
 
== Frequently Asked Questions ==

See our [FAQ](http://wp-events-plugin.com/documentation/faq/) page, which is updated more regularly.

== Screenshots ==

1. Event registration and user submitted events pending approval
2. Event ticketing and bookings forms, easily styleable.
3. Multiple tickets with constraints and prices
4. Locations with google map integration
5. Event registration page
6. Manage attendees with various booking reports

== Changelog ==

= 4.212 =
* removed JS entirely from booking form template, still included in footer (overriden templates should remove JS to avoid errors)
* fixed booking status name mixup when approvals are disabled
* added option to enable/disable user booking cancellation
* booking search attribute can use 'user' to show events booked by logged in user

= 4.211 =
* best explained here: http://wordpress.org/support/topic/tagging-new-plugin-version-issues?replies=14#post-2376253

= 4.2 =
* forced update to correct wordpress repository db update notification
* jquery CSS loaded by js if needed for the datepicker
* small css tweaks/fixes to the calendar
* fixed js captcha warning
* fixed warnings
* event_form time entry now using the js time entry script
* double booking now working as expected
* hid some event booking info/links for admin viewers in group event pages
* buddypress my bookings screen fixed

= 4.18 =
* corrected bad HTML in default category page format
* added booking form JS to the wp_footer area for more theme compatability
* added em_admin_paginate filter
* added em_bookings_{action} action for bookings page
* updated placeholder docs
* new tickets ordering setting
* ticket name and descriptions accept images and link html
* fixed bug when using whole year searches in shortcode
* edit bookings link placeholders won't show to users without permission
* booking form will not show when an event is fully booked
* fixed #_BOOKINGTICKETS placeholder showing incorrect space numbers
* cancel booking link re-appearing in my-bookings section
* improved display of group events
* fixed double seperator in title
* scope now being correctly saved in event widgets
* removed unnecesary filtering in email content, causing html entities in plaintext
* updated pot and German/Sweedish translations

= 4.171 =
* tagged 4.17 in the repo as 4.171 due to premature release

= 4.17 =
* delete category/location/event image option added
* added some escaping functions to outputs in calendar
* fixed table duplicate index problem
* added customizable booking form notices
* RSS now passes the W3 Validator
* tested and passed for BuddyPress 1.5
* fixed various warnings
* updated sweedish translation
* categories page title now working in disable title rewrite
* fixed the WP title seperator bug
* updated help placeholder list
* fixed badly named category events placeholders and added backward compatability
* locations_map shortcode now accepts location search attributes
* added option to prevent double bookings for one event
* changed rsvp search attribute into bookings and added backward compatability
* attribute dropdowns don't show a 'no value' since the first option should be default

= 4.16 =
* image thumbnails added
* phone and further email booking problems fixed
* added ical feed settings
* added category events list formatting options (previously taken from location settings)
* split up booking form template file into template parts
* single ticket events with only one space (due to limits or availability) won't show selection box
* added some widget filters for search arguments
* booking notes bug in 4.15 fixed
* calendar widget fix for eventful today links not showing
* event, location and category slugs can now be changed via the wp-config.php file

= 4.15 =
* single events can now be converted to recurring
* booking approval issue fixed
* group Event activities now included group wall
* fixed recurrence bug, where rescheduled past events aren't deleted
* dbem_phone fix
* initial password will now allow users to log in (bug in 4.14 only)
* fixed google map balloon centering and IE8 incompatability
* added the UK nations
* changed usernames shown in booking areas to full name (if available)

= 4.14 =
* Admin-editable bookings/tickets
* BP menu items do not show if a user doesn't have the relevant capabilities
* Member/Guest submit forms improved (still in beta due to pending template changes, but functionality is there)
* Updated the help pages with new placeholders
* Fixed a register-before-booking bug
* CSV event bookings export now an overridable template
* Cleaned up some ical formatting problems
* Countries list updated
* Fixed datepicker js issue in tickete
* Calendar headings have mb_ support for multi-byte characters.
* Various smaller bugfixes and warning removals

= 4.13 =
* events now allow 10 digit booking prices, if you have an event that costs more than this, call me :)
* fixed confirmation emails not firing from paid bookings
* fixed #_BOOKEDSPACES not including the confirmed booking in the total
* event details aren't copied by mistake to a location
* fixed booking notes
* added em_event_owner_dropdown_users filter
* added category selection in calendar widget 

= 4.12 =
* fixed JS problem in admin area when WPLANG is set
* fixed confirmation email bug for pro users
* added belize to countries list

= 4.11 =
* fixed conflict of default category/event widget
* added/fixed some gettext domains
* removed some php warnings
* corrected filter misspelling of em_booking_get_prices to em_booking_get_price
* added a few new filters
* fixed initial notification emails not going out to event contact on pro payments

= 4.1 =
* nothing, just trying to get WP to recognize a new update

= 4.0.9 =
* added various google/user translated languages and updated pot file
* fixed various gettext domain errors
* search form defaults and behaviour fixed
* added dates to buddypress group events template
* improved the google maps js insertion (updated with google's new recommended code)
* no pending approvals when switching from auto-approval to approval mode
* added new "within month" scope
* various other nuances fixed

= 4.0.83 =
* added option to remove booking login form
* fixed login issues when guest bookings is disabled
* registration email is optional
* added option to show ticket table even in single ticket mode
* fixed search defaulting to default country when all countries selected
* fixed ical timezone issue
* corrected some typos
* added Jamaica and Bolivia to countries list
* added guest event and member submissions with [event_form]
* fixed location search ownership issue
* added new template tags for page type detection
* fixed some ticket display issues
* added search filter in event
* updated the docs (although needs a thourough revision once more)

= 4.0.82 =
* fixed bookings missing in non-approval mode

= 4.0.81 =
* fixed events not editing due to new location js
* fixed pro notification
* fixed calendar ajax year switching issue

= 4.0.8 =
* just made settings page expanded
* added some update notifications for pro user

= 4.0.7 =
* minium WP version is now 3.1
* prevented JS loading in non-EM admin screens again
* updated jQuery ui objects to use the 1.8.x core
* removed dependency on ajaxForm javascript
* new booking ticket placeholders for emails
* images now saving in recurrence mode
* images now saving in multisite global/local modes
* LOADS of bugfixes in buddypress
* removed user list showing for normal location editors
* cleaning up the attributes e.g. apostrophes
* images kept when detaching recurrent event
* location and categories now have slug choice and get properly cleaned
* added and corrected some countries (Syria, Peru, corrected Panama code)
* added option to disable registration emails going out
* tickets now accept digits, e.g $1.50
* location form in event more intuitive when using previous locations
* location form and map degrade more gracefully with small screens now.
* removed various php warnings

= 4.0.6 =
* removed more php warnings
* fixed recurrence issue
* improved default values of country/state/region in search forms
* fixed ticketing issues with recurrences
* added workaround for IIS users with 404 issues
* fixed global maps not working in some instances
* made notice collisions when saving in sessions less likely
* fixed MS recurrence issue

= 4.0.5 =
* removed various php warnings
* added explanation for incorrect recurrences 
* fixed RSS title/desc not using html entities
* fixed event widget scope problem
* MultiSite superadmins can manage all

= 4.0.4 =
* Fixed the 404 problem
* added Peru to countries, fixed broken accented characters in country lists
* added ticket description to booking form.
* reordered the search form to make more sense

= 4.0.3 =
* Fixed the update method for good now
* fixed booking pending email discrepency
* other minor booking bugs
* ics file formatting fix
* buddypress group events working as expected again
* booking form and rsvps showing fixed
* fixes to search form

= 4.0.2 =
* updated default formats and event options on install
* fixed title meta location problem
* added town/country/state/region search attributes for locations
* added extra linking formatting for calendars (minor tweak for bug report)
* datepicker locale now matches WPLANG setting (if applicable)
* fixed recurrence and category issues
* changed version update mechanism 

= 4.0.1 =
* fixed recurrence slug and creation issue
* fixed created/modified dates which weren't always updating
* added bvi and greenland to countries list
* got rid of known warnings to date
* fixed various issues with the search form ajax and loaded values
* added extra location info to columns
* location placeholders fixed
* attribute now working properly as intended

= 4.0 =
* see http://wp-events-plugin.com/news/events-manager-4-0-released/

= 3.0.97 =
* Restoring stable version

= 3.0.96 =
* fixed js hook bug, you must now bind your function to the document's custom em_maps_locations_hook and em_maps_location_hook event triggers using jquery
* fixed tinymce bug with linking which cropped up in 3.1 due to new WP linking window.
* event_date_modified now properly updated

= 3.0.95 =
* removed some php warnings
* fixed blank widget defaults (resave current widgets to replace blanks with defaults)
* fixed calendar bug, where old events aren't being shown
* fixed calendar css for events on the current day
* unapproval is now reject if pre-approvals are turned off
* delete bookings working again
* booking emails working as expected without pre-approvals
* added js hook for maps
* fixed qtranslate conflict, delayed mo file loading for better compatability with wpml

= 3.0.94 =
* Fixed missing events, locations etc. due to permissions
* Fixed location widget bug
* fixed broken global map js

= 3.0.93 =
* Fixed bug with ownership and widgets
* Resolved 2.9 incompatibility
* Fixed rss ownership bug
* Fixed calendar bug where pre/post dates don't show events
* Fixed calendar, now showing today correctly
* Categories blank page fix
* fixed page nav conflicts with role scoper
* added shortcut to manage bookings on event list


= 3.0.92 =
* Fixed permission issue
* Fixed category not saving
* Fixed location saving issue


= 3.0.91 =
* Documentation finally up to date now!
* widget bug fixed
* added event permissions, so users can manage their own events/locations/categories
* improved event booking UI and management tools
* export CSV of bookings
* booking approvals added
* bookings can have individual notes
* calendar widget shows selected month if clicked on 
* custom attributes field, for atts that don't need to be in a template (e.g. pdf file url)
* time limit for main events list and events widget (e.g. show events that occur within x months)
* default location
* default category
* added extra validation so event start date/times can't be after end date/time
* calendar navigation will pass on all arguments for following month (e.g. category, etc)
* small map balloon fix for some rare js conflicts
* fixed location gui editor

= 3.0.9 =
* Fixed small calendar discrepancies
* added event and location single shortcodes
* shortcodes now accept html within format attribute or within the shortcode tags [like]<p>this</p>[/like]
* fixed pagination functionality (or lack thereof) in shortcodes
* improved user experience when navigating/editing events in admin area
* added #_CONTACTAVATAR placeholder - avatar for contact person
* ajax loading spinner graphic added to calendars
* internal wp_mail support added
* added "all events" link to events widget
* fixed date translations
* cleaned up the settings page documentation and added placeholder docs on help page.
* fixed "enable notification emails" option in settings
* added admin email option that would be send every event booking to admin 

= 3.0.81 =
* Fixed pagination bugs
* Global locations map won't show locations with 0-0 coords
* Fixed bug in recurrence description
* Removed most (if not all) php warnings
* Fixed booked seats calculation errors
* Removed dependence on php calendar

= 3.0.8 =
* Event lists now have pagination links for both admin and public areas!
* Fixed time zone issue with calendars, now taking time from WP settings, not server
* Added option to show long events if showing a calendar of events page.
* Multiple maps on one page will now show up.
* Modified styling of map balloons to not use #content (if you modded your theme, look at the CSS to override).
* Media uploads in GUI now working as expected
* Orderby ordering in events widget

= 3.0.7 =
* Renaming a few functions/shortcodes for consistency
* Fixing #_LOCATIONPAGEURL issue
* Fixed ordering issue again
* New template tags
* First filter

= 3.0.6 =
* Added revised German translation
* Fixed ordering issue
* Fixed old template tag attributes not being read
* Changed map balloon wrapper id to class

= 3.0.5 =
* Fixed 12pm bug
* Re-added #_LOCATIONPAGEURL (although officially it's depreciated)
* Added default order by settings in options page
* Added default event list limits in options page
* Added orderby attribute for shortcode
* scope attribute now also allows searching between dates, e.g. "2010-01-01,2010-01-31"
* Fixed booking email reporting bug

= 3.0.4 =
* Title rewriting workaround for themes where main menus are broken on events pages
* Added option to show lists on calendar days regardless of whether there is only one event on that day.
* added Spanish translation
* fixed rsvp deletion issue
* fixed potential phpmailer conflicts
* CSS issue with maps fixed
* optimized placeholders, adding new standard placeholders

= 3.0.3 =
* RSS Showing up again
* Fixed some reported fatal errors
* Added locations widget
* Adding location widget
* optimizing EM_Locations and removing redundant code across objects
* fixed locations_map shortcode attributes
* harmonized search attributes for locations and events
* rewrote recurrence code from scratch
* got rid of most php notices

= 3.0.2 =
* Recruccence bugfix

= 3.0.1 =
* Fixed spelling typos
* Fixed warnings for bad location image uploads (e.g. too big etc.)
* Fixed error for #_EXCERPT not showing

= 3.0 =
* Refactored all the underlying architecture, to make it object oriented. Now classes and templates are separate.    
* Merged the events and recurrences tables                                                   
* Tables migration from dbem to em (to provide a fallback in case the previous merge goes wrong)
* Bugfix: 127 limit increased (got rid of tinyint types)
* Bugfix: fixed all major php bugs preventing the use with Wordpress 3.0
* Bugfix: fixed all major js bugs preventing the use with Wordpress 3.0
* Restyling of the Settings page    
* Added a setting to revert to 2.2
* optimizing EM_Locations and removing redundant code across objects

For changelog of 2.x and lower, see the readme.txt file of version 2.2.2