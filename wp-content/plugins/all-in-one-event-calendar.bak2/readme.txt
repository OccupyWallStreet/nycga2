=== All-in-One Event Calendar ===
Contributors: theseed, hubrik, vtowel, yani.iliev
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=9JJMUW48W2ED8
Tags: calendar, event, events, ics, ics calendar, ical-feed, ics feed, wordpress ics importer, wordpress ical importer, upcoming events, todo, notes, journal, freebusy, availability, web calendar, web events, webcal, google calendar, ical, iCalendar, all-in-one, ai1ec, google calendar sync, ical sync, events sync, holiday calendar, calendar 2011, events 2011, widget, events widget, upcoming events widget, calendar widget, agenda widget, posterboard
Requires at least: 3.2
Tested up to: 3.4.1
Stable tag: 1.8.3-premium
License: EULA.license

A calendar system with many views, upcoming events widget, color-coded categories, recurrence, and import/export of facebook events and .ics feeds.

== Description ==

Welcome to the [All-in-One Event Calendar Plugin](http://time.ly/), from [Timely](http://time.ly/). The All-in-One Event Calendar is a new way to list your events in WordPress and easily share them with the rest of the world.

Our new calendar system combines a clean visual design, solid architectural patterns and a powerful set of features to create the most advanced calendar system available for WordPress. Best of all: it’s completely free.

Download the free Premium version at [time.ly](http://time.ly/) and choose from 3 custom designed themes for your Calendar, or develop your own! Also includes Posterboard view, Facebook integration, refactored JavaScript and more. 

**New in version 1.8:**
* Posterboard view option for all themes
* Facebook Integration: Import events from friends, groups, and pages. Export to your Facebook Events
* Refactored JavaScript: Better integration with plugins and themes

= Calendar Features For Users =

This plugin has many features we hope will prove useful to users, including:

* **Recurring** events
* **Filtering** by event category or tag
* Easy **sharing** with Google Calendar, Apple iCal, MS Outlook and any other system that accepts iCalendar (.ics) feeds
* Embedded **Google Maps**
* **Color-coded** events based on category
* **Month**, **week**, **day**, **agenda**, and **posterboard** views
* **Upcoming Events** widget
* Direct links to **filtered calendar views**
* **Facebook** integration

= Features for Website and Blog Owners =

* Import other calendars automatically to display in your calendar
* Categorize and tag imported calendar feeds automatically
* Events from [The Events Calendar](http://wordpress.org/extend/plugins/the-events-calendar/) plugin can also be easily imported
* Create a Calendar administration role to allow for a dedicated calendar application

Importing and exporting iCalendar (.ics) feeds is one of the strongest features of the All-in-One Event Calendar system. Enter an event on one site and you can have it appear automatically in another website's calendar. You can even send events from a specific category or tag (or combination of categories and tags).

Why is this cool? It allows event creators to create one event and have it displayed on a few or thousands of calendars with no extra work. And it also allows calendar owners to populate their calendar from other calendar feeds without having to go through the hassle of creating new events. For example, a soccer league can send its game schedule to a community sports calendar, which, in turn, can send only featured games (from all the sports leagues it aggregates) to a community calendar, which features sports as just one category.

= Additional Features =

The All-in-One Event Calendar Plugin also has a few features that will prove useful for website and blog owners:

* Each event is SEO-optimized
* Each event links to the original calendar
* Your calendar can be embedded into a WordPress page without needing to create template files or modify the theme

= Video =

http://www.youtube.com/watch?v=XJ-KHOqBKuQ

= Helpful Links =

* [**Get help from our Help Desk »**](http://help.time.ly)

== Frequently Asked Questions ==

[**Get help from our Help Desk »**](http://help.time.ly)

= Shortcodes =

* Monthly view: **[ai1ec view="monthly"]**
* Weekly view: **[ai1ec view="weekly"]**
* Agenda view: **[ai1ec view="agenda"]**
* Posterboard view: **[ai1ec view="posterboard"]**
* Default view as per settings: **[ai1ec]**

* Filter by event category name: **[ai1ec cat_name="halloween"]**
* Filter by event category names (separate names by comma): **[ai1ec cat_name="Halloween, Thanksgiving Day"]**
* Filter by event category id: **[ai1ec cat_id="1"]**
* Filter by event category ids (separate ids by comma): **[ai1ec cat_id="1, 2"]**

* Filter by event tag name: **[ai1ec tag_name="halloween"]**
* Filter by event tag names (separate names by comma): **[ai1ec tag_name="Halloween, Thanksgiving Day"]**
* Filter by event tag id: **[ai1ec tag_id="1"]**
* Filter by event tag ids (separate ids by comma): **[ai1ec tag_id="1, 2"]**

* Filter by post id: **[ai1ec post_id="1"]**
* Filter by post ids (separate ids by comma): **[ai1ec post_id="1, 2"]**

== Changelog ==

= Version 1.8.3-premium =
* Fixed an issue with google maps
* Fixed an sql problem in duplicate controller
* Fixed an upgrade theme issue

= Version 1.8.2-premium =
* Added compatibility when the official Facebook plugin is installed

= Version 1.8.1-premium =
* Added support for WordPress v3.2 - WP_Scripts::get_data method didn't exist before WP v3.3

= Version 1.8-premium =
* "Posterboard" view option for event display
* Ability to have only certain calendar views enabled
* Refactored Javascript to reduce conflicts with themes and plugins
* Facebook Integration - Import and Export events to Facebook
* Front End UI enhancements
* Updated ical parser

= Version 1.7.1 Premium =
* AIOEC-186 AIOEC-195: Added compatibility for WordPress 3.4
* AIOEC-120: Internet Explorer - admin + frontend UI compatibility
* AIOEC-193: On single events page, the "pm" (or am) appears on the following line in Skeptical Wootheme
* AIOEC-195: Theme screenshots do not show up in 3.4

= Version 1.7 Premium =
* Restored support for WordPress 3.2.x, which fixes numerous JavaScript issues in that version of WordPress
* Updated jQuery loading to avoid theme, slider, other issues
* Removed opaque background from calendar containers to better match WP theme background
* Updated multi-day UI
* Improved UI for latitude / longitude
* un-minified css for easier editing

= Version 1.6.3 Premium =
* Added support for server running versions of php below 5.2.9

= Version 1.6.2 Premium =
* Fixed bug that was breaking adding/importing/editing events
* Enabled updates and update notifications when there is a newer version

= Version 1.6.1 Premium =
* Fixed bug that was breaking widget management screen
* Removed some warnings from month view in certain setups

= Version 1.6 Premium =
* Choose new Calendar Themes
* Duplicate Events
* Create Print View
* Add location details that allow latitude and longitude for areas poorly covered by Google Maps
* Turn on/off autocomplete for addresses
* See more intuitive views of multi-day events on weekly and monthly calendars
* Calendar administration role to allow for dedicated calendar application
* Security updates
* Bug fixes

= Version 1.5 =
* Added daily view
* Various bug fixes
* Added new translations
* Added support for featured images
* Better support for Multisite Ajax
* Added support for DURATION property in iCalendar specs
* Resolved FORCE_SSL_ADMIN issue

= Version 1.4 =
* Export ICS feeds with utf8 header
* Import/Download ICS feeds with CURL if available, otherwise keep the current method
* Better UTF8 support for imported events
* Use local version jquery tools instead of the CDN copy
* Improved system for catching errors and trying best to find a possible route to proceed without having to quit/fail
* Fixed various Notice level errors
* Fixed bug with recurrence/exception rules not properly being converted to GMT
* Added EXDATE support and EXDATE UI to allow selection of specific dates.
* Added filter by feed source on All events page
* Improved caching of stored events
* Fixed getOffset problem - notify me if it still happens for you

= Version 1.3 =
* Added shortcodes support.[#36](http://trac.the-seed.ca/ticket/36) (Howto is under Frequently Asked Questions tab)
* Added support to exclude events using [EXRULE](http://www.kanzaki.com/docs/ical/exrule.html)
* Added Czech translation
* Added Danish translation
* Updated Swedish translation

= Version 1.2.5 =
* Reviewed plugin's security. The plugin is as safe to use as is WordPress itself.
* Fixed: instance_id not corresponding with correct data [#275](http://trac.the-seed.ca/ticket/275)
* Fixed: Call-time pass-by-reference warning [#268](http://trac.the-seed.ca/ticket/268)
* Improvement: Added support for custom fields

= Version 1.2.4 =
* Improvement: Added a lower version of iCalcreator for environments with PHP versions below 5.3.0

= Version 1.2.3 =
* Improvement: Days of the week in month recurrence [#170](http://trac.the-seed.ca/ticket/170)
* Improvement: Make Month view, Week view compatible with touchscreen devices [#210](http://trac.the-seed.ca/ticket/210)
* Improvement: Improve error handling in get_timezone_offset function[#219](http://trac.the-seed.ca/ticket/219)
* Improvement: Update iCalcreator class [#256](http://trac.the-seed.ca/ticket/256)
* Fixed: Widget Limit options (category, tag, etc) multiselect fails to display properly [#192](http://trac.the-seed.ca/ticket/192)
* Fixed: Private Events Show in Calendar and Upcoming Events. [#201](http://trac.the-seed.ca/ticket/201)
* Fixed: Dates getting mixed up between Ai1EC calendars [#229](http://trac.the-seed.ca/ticket/229)
* Fixed: Error displayed when event is a draft [#239](http://trac.the-seed.ca/ticket/239)
* Fixed: PHP Notice errors from widget [#255](http://trac.the-seed.ca/ticket/255)

= Version 1.2.2 =
* Fixed: Issue with Week view having an improper width [#208](http://trac.the-seed.ca/ticket/208)

= Version 1.2.1 =
* Fixed: Exporting single event was exporting the whole calendar [#183](http://trac.the-seed.ca/ticket/183)
* Fixed: Widget date was off by one in certain cases [#151](http://trac.the-seed.ca/ticket/151)
* Fixed: Trashed events were still being displayed [#169](http://trac.the-seed.ca/ticket/169)
* Fixed: All day events were exporting with timezone specific time ranges [#30](http://trac.the-seed.ca/ticket/30)
* Fixed: End date was able to be before the start date [#172](http://trac.the-seed.ca/ticket/172)
* Fixed: 404 or bad ICS URLs now provide a warning message rather than fail silently [#204](http://trac.the-seed.ca/ticket/204)
* Fixed: Added cachebuster to google export URL to avoid Google Calendar errors [#160](http://trac.the-seed.ca/ticket/160)
* Fixed: Week view was always using AM and PM [#190](http://trac.the-seed.ca/ticket/190)
* Fixed: Repeat_box was too small for some translations [#165](http://trac.the-seed.ca/ticket/165)

= Version 1.2 =
* Added scrollable Week view [#117](http://trac.the-seed.ca/ticket/117)
* Fixed some notice-level errors

= Version 1.1.3 =
* Fixed: last date issue for recurring events "until" end date [#147](http://trac.theseednetwork.com/ticket/147)
* Fixed an issue with settings page not saving changes.
* Fixed issues when subscribing to calendars.
* Export only published events [#95](http://trac.theseednetwork.com/ticket/95)
* Added translation patch. Thank you josjo! [#150](http://trac.theseednetwork.com/ticket/150)
* Add language and region awareness in functions for Google Map. Thank you josjo! [#102](http://trac.theseednetwork.com/ticket/102)
* Small translation error in class-ai1ec-app-helper.php. Thank you josjo! [#94](http://trac.theseednetwork.com/ticket/94)
* Added Dutch, Spanish, and Swedish translations. For up to date language files, visit [ticket #78](http://trac.theseednetwork.com/ticket/78).

= Version 1.1.2 =
* Fixed: Problem in repeat UI when selecting months before October [#136](http://trac.theseednetwork.com/ticket/136)
* Fixed: Append instance_id only to events permalink [#140](http://trac.theseednetwork.com/ticket/140)
* Fixed: Events ending on date problem [#141](http://trac.theseednetwork.com/ticket/141)
* Feature: Added French translations

= Version 1.1.1 =
* Fixes a problem when plugin is enabled for first time

= Version 1.1 =
* Feature: New recurrence UI when adding events [#40](http://trac.theseednetwork.com/ticket/40)
* Feature: Translate recurrence rule to Human readable format that allows localization [#40](http://trac.theseednetwork.com/ticket/40)
* Feature: Add Filter by Categories, Tags to Widget [#44](http://trac.theseednetwork.com/ticket/44)
* Feature: Add option to keep all events expanded in the agenda view [#33](http://trac.theseednetwork.com/ticket/33)
* Feature: Make it possible to globalize the date picker. Thank you josjo! [#52](http://trac.theseednetwork.com/ticket/52)
* Fixed: On recurring events show the date time of the current event and NOT the original event [#39](http://trac.theseednetwork.com/ticket/39)
* Fixed: Events posted in Standard time from Daylight Savings Time are wrong [#42](http://trac.theseednetwork.com/ticket/42)
* Fixed: Multi-day Events listing twice [#56](http://trac.theseednetwork.com/ticket/56)
* Fixed: %e is not supported in gmstrftime on Windows [#53](http://trac.theseednetwork.com/ticket/53)
* Improved: IE9 Support [#11](http://trac.theseednetwork.com/ticket/11)
* Improved: Corrected as many as possible HTML validation errors [#9](http://trac.theseednetwork.com/ticket/9)
* Improved: Optimization changes for better performance.

= Version 1.0.9 =
* Fixed a problem with timezone dropdown list

= Version 1.0.8 =
* Added better if not full localization support [#25](http://trac.theseednetwork.com/ticket/25) [#23](http://trac.theseednetwork.com/ticket/23) [#10](http://trac.theseednetwork.com/ticket/10) - thank you josjo
* Added qTranslate support and output to post data using WordPress filters [#1](http://trac.theseednetwork.com/ticket/1)
* Added uninstall support [#7](http://trac.theseednetwork.com/ticket/7)
* Added 24h time in time pickers [#26](http://trac.theseednetwork.com/ticket/26) - thank you josjo
* Fixed an issue when event duration time is decremented in single (detailed) view [#2](http://trac.theseednetwork.com/ticket/2)
* Fixed an issue with times for ics imported events [#6](http://trac.theseednetwork.com/ticket/6)
* Better timezone control [#27](http://trac.theseednetwork.com/ticket/27)
* Fixed the category filter in agenda view [#12](http://trac.theseednetwork.com/ticket/12)
* Fixed event date being set to null when using quick edit [#16](http://trac.theseednetwork.com/ticket/16)
* Fixed a bug in time pickers [#17](http://trac.theseednetwork.com/ticket/17) - thank you josjo
* Deprecated function split() is removed [#8](http://trac.theseednetwork.com/ticket/8)

= Version 1.0.7 =
* Fixed issue with some MySQL version
* Added better localization support - thank you josjo
* Added layout/formatting improvements
* Fixed issues when re-importing ics feeds

= Version 1.0.6 =
* Fixed issue with importing of iCalendar feeds that define time zone per-property (e.g., Yahoo! Calendar feeds)
* Fixed numerous theme-related layout/formatting issues
* Fixed issue with all-day events after daylight savings time showing in duplicate
* Fixed issue where private events would not show at all in the front-end
* Fixed duplicate import issue with certain feeds that do not uniquely identify events (e.g., ESPN)
* Added option to General Settings for inputting dates in US format
* Added option to General Settings for excluding events from search results
* Added error messages for iCalendar feed validation
* Improved support for multiple locales

= Version 1.0.5 =
* Added agenda-like Upcoming Events widget
* Added tooltips to category color squares
* Fixed Firefox-specific JavaScript errors and layout bugs
* Added useful links to plugins list page
* Fixed bug where feed frequency setting wasn't being updated
* Made iCalendar subscription buttons optional

= Version 1.0.4 =
* Improved layout of buttons around map in single event view
* Set Content-Type to `text/calendar` for exported iCalendar feeds
* Added Donate button to Settings screen

= Version 1.0.3 =
* Changed plugin name from `All-in-One Events Calendar` to `All-in-One Event Calendar`
* **Important notice:** When upgrading to version `1.0.3` you must reactivate the plugin.

= Version 1.0.2 =
* Fixed the URL for settings page that is displayed in the notice

= Version 1.0.1 =
* Fixed bug where calendar appears on every page before it's been configured
* Displayed appropriate setup notice when user lacks administrator capabilities

= Version 1.0 =
* Initial release

== Installation ==

1. Upload `all-in-one-event-calendar` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the **Plugins** menu item in the WordPress Dashboard.
3. Once the plugin is activated, follow the instructions in the notice to configure it.

**Important notice:** When upgrading from version `1.0.2` or below you must reactivate the plugin.

= For advanced users: =

To place the calendar in a DOM/HTML element besides the default page content container without modifying the theme:

1. Navigate to **Settings** > **Calendar** in the WordPress Dashboard.
2. Enter a CSS or jQuery-style selector of the target element in the **Contain calendar in this DOM element** field.
3. Click **Update**.

== Screenshots ==

1. Add new event - part 1
2. Add new event - with recurrence
3. Event categories
4. Event categories with color picker
5. Front-end: Month view of calendar
6. Front-end: Month view of calendar with mouse cursor hovering over event
7. Front-end: Month view of calendar with active category filter
8. Front-end: Month view of calendar with active tag filter
9. Front-end: Week view of calendar
10. Front-end: Agenda view of calendar
11. Settings page
12. Upcoming Events widget
13. Upcoming Events widget - configuration options

== Upgrade Notice ==

= 1.6 Premium =
The All-in-One Event Calendar can only be upgraded to the Premium version from version 1.6 and above, or by downloading directly from [time.ly](http://time.ly/).

= 1.0.3 =
When upgrading to from below `1.0.3` you must reactivate the plugin.
