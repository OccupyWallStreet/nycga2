=== Events Manager ===
Contributors: netweblogic, nutsmuggler
Donate link: http://wp-events-plugin.com
Tags: events, event, event registration, event calendar, events calendar, event management, paypal, registration, ticket, tickets, ticketing, tickets, theme, widget, locations, maps, booking, attendance, attendee, buddypress, calendar, gigs, payment, payments, sports,
Requires at least: 3.3
Tested up to: 3.5
Stable tag: 5.3.5

Fully featured event registration management including recurring events, locations management, calendar, Google map integration, booking management

== Description ==

Events Manager is a full-featured event registration plugin for WordPress based on the principles of flexibility, reliability and powerful features!

Version 5 now makes events and locations WordPress Custom Post Types, allowing for more possibilities than ever before!

* [Demo](http://demo.wp-events-plugin.com/)
* [Documentation](http://wp-events-plugin.com/documentation/)
* [Tutorials](http://wp-events-plugin.com/tutorials/)

= Main Features =

* Easy event registration (single day with start/end times)
* Recurring and long (multi-day) event registration
* Bookings Management (including approval/rejections, export CVS, and more!)
* Multiple Tickets
* MultiSite Support
* BuddyPress Support
 * Submit Events
 * Group Events
 * Personal Events
 * Activity Stream
 * more on the way
* Guest/Member Event submissions
* Assign event locations and view events by location
* Event categories
* Easily create custom event attributes (e.g. dress code)
* Google Maps
* Advanced permissions - restrict user management of events and locations.
* Widgets for Events, Locations and Calendars
* Fine grained control of how every aspect of your events are shown on your site, easily modify templates from the settings pages and template files
* iCal Feed (single and all events)
* Add to Google Calendar buttons
* RSS Feeds
* Compatible with SEO plugins
* Plenty of template tags and shortcodes for use in your posts and pages
* Actively maintained and supported
* Lots of documentation and tutorials
* And much more!

= Go Pro =
We have also released an add-on for Events Manager which not only demonstrates the flexibility of Events Manager, but also adds some important features:

* PayPal, Authorize.net and Offline Payments
* Custom booking forms
* Coupon Codes
* Faster support via private forums

For more information or to go pro, [visit our plugin website](http://wp-events-plugin.com).

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

= Upgrading from version 4 to 5 =

Please [read these instructions](http://wp-events-plugin.com/updating-to-v5/).

== Upgrade Notice ==

For those upgrading from version 4 to 5, please [read these instructions](http://wp-events-plugin.com/updating-to-v5/).

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
= 5.3.5 =
* fixed bug in placeholder formatting

= 5.3.4 =
* fixed Multilingual settings not saving default language setting if other than english
* fixed typo in performance optimization settings
* fixed warning of undefined ID on archive pages when enqueuing scripts
* fixed special characters being converted to entities in non-html emails
* fixed typo in options for category/location event list placeholders
* corrected Slovak translation, thanks to Julius Pastierik 
* added British translation, thanks to Jeff Cole
* added some code to booking form js to prevent JS conflicts with JetPack's reCaptcha
* added base64 encoding/decoding to em_notice cookies for improved compatibility
* fixed potential php warning in EM_Tickets class
* event spaces show as blank rather than 0 on input form (aesthetic change in line with the field help text)
* added alphabetical ordering to category and countries ddms in search form and admin event categories ddm
* fixed XSS vulnerabilities - http://em.cm/xss
* fixed em_is_category_page() and added check for specific categories (like is_tax() second parameter), added em_is_tag_page() with checks for specific tags
* added #_EVENTPRICERANGEALL and fixed #_EVENTPRICERANGE showing if booking closed but unavailable tickets set to true (docs need revising)
* improved speed of event shortcode by adding global event object
* added ordering of locations by name and other location table fields in event queries such as events_list shortcode
* added some missing classes to event form 'when' section 

= 5.3.3 =
* changed taxonomy pages to use is_tax() to check whether page is a taxonomy page, rather than checking the $wp_query object
* fixed booked spaces being off if approvals are disabled and booking has status 0 from when approvals were enabled
* added #_CATEGORYSLUG
* fixed bad usage of wpdb::prepare in classes/em-object.php
* added em_actions_locations_search_cond for autocompleter
* added Bermuda and Jersey to countries list
* fixed title rewriting warning when $sep is blank
* fixed BP 'add event' link appearing to all users on event profile pages
* fixed loading of unused post meta overwriting post fields/properties
* added multilingual capability
* added WPML add-on warning
* simplified enqueue of styles and scripts, now uses wp_enqueue_scripts action
* added performance optimization options for CSS and JS files
* removed usage of PHP sessions in exchange for temporary cookies
* added possiblility to use wp thumbnails rather than timthumb (category images need resaving via uploader)
* added #_LOCATIONLONGITUDE and #_LOCATIONLATITUDE placeholders
* added em-pagination span wrapper and em-tablenav-pagination classnames to normal and table pagination sections
* fixed location searches not returning results admin-side in MS global tables with locations on main blog
* updated JS for location map to update when location name changed
* added pagination and limits to location and category event list placeholders.
* updated finnish language
* fixed minor php warning when deleting a user with no events in MultiSite
* fixed php warnings on category pages using formats, major change to how formats are overriden, now rewrites WP_Query completely
* added em_options_page_panel_admin_tools action to admin tools section of options page
* fixed #_BOOKINGPRICE and other booking price placeholders not using currency formatting option
* fixed permissions issue with groups plugin (thx itthinx)
* added Slovak translation, thanks to Branco
* added em_bookings_table_row_booking_price_ticket filter (needed for Pro ticket exports w/coupons)
* added default rows/cols attributes to booking form textarea field for valid html
* fixed conflict with caching plugins and booking forms due to cached wpnonces
* added #_CATEGORYDESCRIPTION to default EM filters of content placeholders
* updated timthumb.php to version 2.8.11

= 5.3.2.1 =
* added is_singular to $wp_query on format overriden category pages, fixes conflict with WooThemes Canvas theme

= 5.3.2 =
* fixed blog switching compatability in MS Global mode due to switch_blog improvements in 3.5 release

= 5.3.1 =
* Updated russian translation, thanks to Alexander Tsinkalov
* improved how EM hooks into the_content and similar template tag hooks to improve compatability with other plugins incorrectly using this outside the loop
* fixed rsvp cut-off time not being considered
* fixed missing $ in admin_capability variable of EM_Object::can_manage()
* added recurrences search attribute
* corrected some typos in category event form class, recurrence description
* prevented spaces in comma-delimited email lists failing email validations
* fixed bp event profile page urls breaking when event slugs contain slashes
* updated [events] and [location] to default to use globals if no ids passed on, so it can be used in location/event descriptions
* added resubmitted event confirmation messages
* Updated Spanish translation - thanks to Ponç Joan Llaneras

= 5.3 =
* corrected date_format/dbem_date_format typo on templates/templates/my-bookings.php
* fixed calendar links with extra search args if using non-permalinks
* fixed some non-translated strings
* updated pot file
* added em_booking_add_registration_result filter
* fixed add_user_to_blog on MS bookings not providing a default role
* fixed navbar issue for blogs with events page assigned to homepage
* updated Dutch translation, thanks to Arnout Eykelhoff
* partially-updated Russian translation, thanks to Evgeniy Efimenko
* fixed #_EVENTNOTES being converted before other placeholders, which potentially caused problems if using shortcodes with formats
* updated Chinese thanks to Leo Losoviz
* added #_ATTENDEESPENDINGLIST
* improved JS compatability with booking form (spinner and jumping up to errors/confirmation feedback)
* fixed reserved pending spaces not being approvable if event is full
* fixed categories and other plugin postmeta not being duplicated with event
* fixed admin emails not going out if setting of admin emails contains spaces
* fixed blog search filter not allowing comma seperated values e.g. 1,2,3
* improvements to listings, edit pages and admin linking of locations on MS Global setups, especially if locations are restricted only to main blog
* adjustment to title rewriting, fixing issues such as calendar day pages not having custom titles
* event end time js not failing validation if event dates span
* fixed locations not publishing on anonymous submission even if publish_locations is correctly enabled for assigned anon user
* added second em_booking_form_ticket_spaces action to templates/forms/bookingform/ticket-single.php fixes attendee field not showing for single/last ticket/space bookings
* reverted to previous use of global $wp_query in parse_query filters with additional fix to prevent clash with Pods framework

= 5.2.9 =
* added spaces to comma seperators of location on locations admin table
* improved user deletion hook, bookings now get deleted along with user
* replaced depreciated placeholders in default widget values
* added booking form anchor to action url
* updated POT file and Swedish
* added Japanese, kudos to Kenta Sakamoto
* fixed minimum ticket space value not allowing 0 spaces even if ticket not required
* added em_event_save_events_slug filter
* added $EM_Event to all do_action parameters on templates/placeholders/bookingform.php
* changed #_MAP to #_LOCATIONMAP on installation defaults
* wrapped bookings dashboard sections into divs with class names
* fixed formatting links/texts in settings page tips
* added em_options_booking_form_options action and save button on settings page > bookings > booking form options
* fixed use of global $wp_query when passed within parse_query action - thx Scott @ Pods framework plugin
* added wp_dropdown_pages instead of manual page select generation
* fixed useage of outdated user_firstname/user_lastname property for EM_Person::get_name()
* improved ticket minimum calculation (takes into account if ticket is only ticket in event, therefore required)
* fixed EM_Tickets and EM_Tickets_Bookings not storing ticket/ticket_bookings array key by ticket id
* locations list format header and footer now install ul by default, if blank no wrapped ul is used (which previously contained an 'a' outside an li)
* small fix to em_content_categories_args, now applied to EM_Categories::get() and EM_Categories::output() in templates/categories-list.php
* shortened gcal link some more to prevent some (rare) "404 url too long" errors
* added login redirection to same page in login link of my bookings page

= 5.2.8 =
* fixed js bug arsing from 5.2.7 js 'fix' for datepickers
* fixed categories not showing up on single category pages
* fixed php warning when quick-saving

= 5.2.7 =
* fixed min ticket space number calculation issues in booking forms
* fixed multiple admin emails for event submission by members with publish rights
* added em_bookings_ajax_complete/error jquery events/hooks
* updated/added Swedish, Chinese, and Finnish translations, kudos to Tommy Wahlund, Leonardo Losoviz and @Daedalon respectively
* fixed mailer charset problem in SMTP mails
* added dbem to some __ functions
* added ticket-price class to single ticket booking form html
* fixed datepicker breaking on themes inserting br tags into search/booking forms
* fixed html email setting not working in MultiSite
* added Guadaloupe to countries list
* added em_csv_header_output action (e.g. for Excel hack)
* changed/prevented registration email going out until after booking is completely saved
* added filter em_object_json_encode_pre
* fixed country searches on events page/search form
* added conditional placeholders is_private not_private is_recurrence not_recurrence
* fixed #_EVENTEXCERPT not having formatting filters applied
* changed the_content run at priority 9 for category pages
* fixed private location autocomplete/search issues
* fixed recurrences not being deleted when done from front-end
* fixed edit user link on booking admin area
* fixed edit location link showing to all
* fixed typo in map balloon hint on settings page
* removed default contact person setting (used in < v5.0, now uses author)
* added width/height property to thumbnail img html
* fixed deleted MS subsites not deleting events/locations from global tables
* fixed maps showing undefined fields on first load of edit event with location ddm enabled
* fixed non-registered attendees not being included in no-user mode for #_ATTENDEE and #_ATTENDEELIST
* fixed front-end location admin pagination
* reduced sql statements for county my/all locations on front-end locations admin page
* fixed select all ui problem (thx @Daedalon)
* fixed array_key_exists php warning in EM_Object::can_manage
* changed is_main_blog to is_main_site
* added grandchildren detection when generating permalink rules for events page
* added auto br to emails option in email settings

= 5.2.6 =
* changed validation order for bookings (no validation done in EM_Event::get_post())
* EM_Tickets_Bookings::$tickets_bookings now an associative array, keys are ticket id
* EM_Notices now accepts 2 level arrays for nested errors
* added em_bookings_table_export_options, em_bookings_admin_ticket_row actions 
* added em_bookings_table_get_headers filter 
* admins can now manually approve bookings regardless of whether events are fully booked and overbooking enabled
* fixed search page bugs
* removed some unecessary validations on get_post functions, assumed these are only run on validate() and save(), eventually it'll just be validate()
* fixed js issues when updating ticket options with checkboxes
* hooked into the_excerpt_rss filter to allow overriding event formats on normal rss feed
* fixed recurring event not correctly saving timestamps
* fixed minimum spaces problem on booking form, added 'required' tickets option to allow more possibilities
* fixed js incompatability
* fixed link on single booking admin page if user is a guest in no-user mode
* updated German, French, Hebrew, Dutch, added partial Chinese translation
* hid some unecessary localized JS strings depending on what features are enabled (bookings/recurrences)
* fixed negative non-existant category id searches showning no events instead of all events
* fixed pagination problem on templates/calendar-day.php
* added js triggers em_booking_error and em_booking_complete 
* fixed event price placeholders not accounting for unavailable tickets

= 5.2.5 =
* fixed long google calendar link issue
* fixed and improved duplication function
* allowed cancel link for offline pending bookings status
* fixed MS bug with featured images in global mode on different main/sub sites
* fixed duplicate booking activity posts when event belongs to a group
* fixed template templates/events-list.php for grouped events list
* fixed dots in usernames breaking booking activity feed links
* added booking comment collumn to booking admin tables
* changed some localized edit %s strings
* fixed booking links pointing to admin/front-side oppositely on ajax calls
* fixed tag and category searches showing all events if categories/tags don't exist

= 5.2.4 =
* fixed single ticket template and name of spaces being hard coded
* added em_booking_form_ticket_spaces action
* closed date and time picker inits with functions
* fixed warnings on options page
* fixed potential 0 spaces being available on tickets with a min requirement
* fixed ticket warning
* fixed some permalink issues for homepage and child pages of events pages
* fixed rare fatal error due to undefined object in admin/em-bookings.php

= 5.2.3 =
* improvement to ical all-day time format
* fixed grouped events shortcode not using formats supplied
* removed 'remove from page lists' options for category/event/location pages, see http://em.cm/rplo
* added calendar day abbriviations (useful for some languages e.g. Catalan, Welsh)
* moved events page calendar options to 'pages' tab, event list/archives panel
* fixed attributes bug using default value even when custom value entered (arose in 5.2.2)
* added search button custom text setting
* removed duplicate confirm/error notices in bookings admin area (dashboard-side)
* fixed #_LOCATIONPOSTID showing location id instead

= 5.2.2 =
* added member-only tickets
* added em_bookings_added action
* fixed broken grouped events titles
* fixed some timezone nuances in the ical, simpler and hopefully more reliable dealing with dst
* fixed pesky recurrence bug not ignoring the new booking cut-off dates intended single events, as a result all recurring event cut-off times will be reset to null to prevent prematurely closed booking forms.
* simplified some tab and panel names in the settings page, moved location map balloon settings to maps section
* booking forms will now show under event content if override with formats is set to 'no'
* fixed some event/location attribute nuances with regards to defaults overriding each other, will document
* made attributes, categories and featured image sections in public event/location editors their own template parts
* removed links to person summary page if in no-user booking mode and user isn't registered
* fixed bad action url in booking form when under subdirectory installs (although not actually used by ajax)
* fixed MS queries defaulting to global searches, including transactions
* fixed MS network blog tables not being deleted by WP along with rest of blog

= 5.2.1 =
* quick fix for fatal error on front-side event submit forms when bp is disabled

= 5.2 =
* reversed order of settings link in plugins page
* updated French, Czech, Dutch and pot language files
* fixed category page display problems on some themes (e.g. Thesis) and for some plugins using the loop on the same page
* added rss pubdate to individual events
* removed cut-off time for now from recurring events, since it copies same date over to every recurrence
* fixed bug in bookings table ajax
* updated to PHPMailer 5.2.1, added attachment capabilities and the em_mailer or em_mailer_sent actions
* fixed magnified google maps logo size
* fixed admin emails not going out on event submission under some configurations
* improvements to permalinks for home page loading
* added first/last name collumn to bookings admin table
* added conditional placeholders for location image has_loc_image and no_loc_image
* fixed cancelled/rejected mixup in bookings admin table
* single booking button will show 'Sold Out' if full
* booking cancel link will not show to rejected, cancelled, pending online payment statuses
* fixed yearly recurrences bug
* added em_get_currency_formatted filter 
* added event_category shortcode
* fixed search bug not showing long events outside start/end parameters
* fixed event form not showing submitted data on validation fail during second event submit
* added event re-submission and re-approval custom emails
* added has_category_x, no_category_x, has_tag_x, and no_tag
* my-bookings auto permalink won't happen if bookings disabled
* added israeli translation
* added choice of initial length for calendar day names
* template modification: passed on $EM_Event to em_booking_form_custom action, may prevent some bugs in certain situations
* fixed ical modified timestamp issue
* fixed scheduled events not being published
* fixed incorrect event links to cross blog events in MultiSite global mode
* added $post_ids to em_event_save_events filter
* updated booking button, made button text customizable and moved the js out of the template file
* updated forms/event/groups.php template to allow for site admins to see all bp groups
* updated ui-lightness.css jQuery UI CSS Framework file to v1.8.22
* fixed templates/templates/events-search.php to prevent showing unapproved locations in dropdowns
* fixed admin CSS and JS locations using WP_PLUGIN_URL instead of plugins_url()
* fixed global events and locations not showing up in MS subsites
* fixed dev-mod updating in MS network admin
* added a test email settings button/feature
* fixed grouping by date fomrat option in settings page not being used
* tidied up location autocompleter formatting
* fixed potential timepicker bug when themes add extra br tags between timepickers
* fixed maps not loading when updating events
* fixed ical description formatting issue escaping &
* fixed default location not showing on second event form update
* fixed booking table links going to admin area on public pages after ajax calls
* fixed ical time and time placeholder output issues
* fixed event form breaking when BP groups are deactivated
* fixed printable booking report showing non-approved bookings
* fixed widgets not allowing blank titles and disappearing widget headers
* removed weekly recurring events days checked by default
* removed some optional properties to the ical files, including X-WR-CALNAME
* fixed weekly recurrence bug skipping first event on some instances
* fixed rejected bookings counting as double bookings
* removed unapprove link showing up for rejected/cancelled events
* added bookings link to actions on the wp users admin page

= 5.1.8.5 =
* fixed bug with bookings being open/closed due to changes in 5.1.8.5

= 5.1.8.4 =
* fixed some issues in the EM blog updater and EM use of table constants
* improved BP member link generation in activity stream, uses bp_core_bet_user_domain now
* fixed cancellation link disappearing after booking cut-off date, even if event hasn't started
* fixed use of some get_price style filters and supplying pre-formatted currency numbers
* fixed pagination issues in shortcodes
* fixed booking table ajax issues
* fixed location auto-completer not working when maps are disabled
* fixed ical all-day event issues when offsets come into play
* fixed single day ical offset problems

= 5.1.8.3 =
* fixed bookings being closed if booking cut-off date/time not specificed in new events

= 5.1.8.2 =
* added booking cut-off times
* fixed events with bookings table ajax
* fixed bp group events list not showing location info
* fixed calendar day pages showing 'past' events if option is set not to

= 5.1.8.1 =
* important - Modified template files? See this http://em.cm/templates-5181
* fixed date ranges not working properly
* fixed pagination issues

= 5.1.8 =
* important - Modified template files? See this http://em.cm/datetime
* fixed jigoshop session_start conflict
* removed some group metabox php warnings
* fixed slashes added to location/event name in db table
* fixed/improved multisite capability management (see network admin settings)
* events with >1 ticket will show multi-ticket editor regardless of single ticket mode setting
* updated Brazilian language, added Catalan and fixed a few language datepicker oddities
* fixed RSS validation fails for some special characters
* fixed cancellations being possible after event boookings close 
* fixed admin-side search-by-category
* fixed manage_others_bookings not allowing access to bookings without other caps
* fixed calendar widgets taking on day link search arguments from other parts of the page
* fixed admin email problems when in auto-approve mode and using alternate status numbers e.g. 5
* added force-approve booking flag in EM_Booking::set_status
* fixed ical locations and apostrophes and single ical file time offsets
* fixed gallery shortcode for recurring events
* fixed guest event submission auto-complete
* refactored booking form JS to allow multiple forms on one page
* fixed some booking js ui animations
* fixed booking table overlay issues
* added more js vars for translation purposes
* improved placeholder replacement logic
* added em_event_submission_login filter
* simplified timepicker and datepicker JS and html strucuture for re-use
* EM_Notices behaviour changed so errors are printed and not deleted, only at start/end of script

= 5.1.7 =
* added excludeable categories (use negative numbers instead)
* clarified some of the field tips of "other pages" in options
* fixed thumbnail issue in MS (again)
* added event dates and times as sortable booking collumns
* fixed multisite duplicate post id bug in global mode
* simplified meaning of EM_Bookings::get_booked_spaces, so it's just booked spaces, not pending. get_available_spaces() should be used for reserved seats instead.
* replaced old default date formats with #_EVENTDATES and #_EVENTTIMES
* fixed some datepicker problems in single ticket mode with start/end date tickets
* removed jQuery datepicker and autocomplete libraries, now using WP's internal scripts instead
* improved the reliability of returned json data in booking form
* fixed categories not editable in front-end,
* added email not sent flag to booking object
* fixed tags not working for slug searches
* fixed dst issues in ical calendars
* added name/slug search fall back for tags search
* added datepicker custom date formatting
* fixed non registered user problem for failed JS submissions
* fixed some rsvp conditional and gcal placeholders
* added jquery-ui-css id to jquery ui css loader to promote compatability with others
* you can now add a custom functions.php file within yourtheme/plugins/events-manager/
* improved title rewriting compatibility
* added hierarchies to category dropdowns
* fixed an object reference error in em-object.php send_mail()
* added jQuery em_booking_success event to document
* fixed tickets not showing start/end dates in admin after editing
* fully booked message now shown rather than closed message
* location description won't take event description in public submission forms
* re-added get_date_format for backwards compatability with overriding templates
* fixed pagination issue in my events page on front-end
* fixed potential security xss exploit in json call links
* fixed default country overriding all country search option on search pages
* fixed pagination issue on my events page on the front-end

= 5.1.6 =
* fixed multiple admin emails not going out
* updated timthumb to v2.8.10
* updated placeholder outputting to avoid overwriting longer variations of similarly named placeholders
* fixed #_BOOKINGTICKETNAME not working
* single location and event pages will still use location-single.php and event-single.php templates
* fixed thumbnail image links on multisite to work with timthumb, thanks BinaryMoon for the tut!
* Reduced sql calls for booking object instantiation. $EM_Booking->custom doesn't exist anymore, and notes must be loaded first with $EM_Booking->get_notes().
* EM_Category->has_events() depreciated, returns false always
* More wp_rewrite tweaks to improve compatability
* fixed 24 hour formatting setting being ignored in timepicker
* fixed bad datepickers in single ticket mode
* fixed locations not being auto-approved if submitted via front-end and event is auto-approved.

= 5.1.5 =
* rewritten booking email function, simpler, less error-prone, overriedable and yet same effect
* fixed tax not showing on booking table totals
* fixed booking objects get_price filters, removed em_booking_get_prices from em-ticket-booking.php in place of em_ticket_booking_get_price
* changed filter name em_tickets_bookings_get_prices to em_tickets_bookings_get_price (bad name according to convention)
* admin email can be sent to multiple emails (comma delimited in settings)
* added booking status message filter
* added custom no events message in events widget
* fixed ical not working in non-permalinks mode ( must have /?ical=1 at end of homt url )
* removed original CSV export link in place of booking table exporter, unless users made a custom template
* BuddyPress private group or normal private event info are now not shown in site activity.
* fixed some php warnings
* fixed certain languages breaking date formats
* added #_EVENTCATEGORIESIMAGES
* added yearly recurrences
* added a cut-off date for bookings, so bookings can take place past event start dates
* fixed some issues with dev mode checks
* fixed booking button and multiple bookings at once bug
* fixed ticket spaces export bug
* fixed rss pubdate format
* improved CSS for booking tables front-end
* edit event locations dropdown shown to users if they can read events (previously only if could edit)
* updated the POT file and Swedish translations
* added #_CONTACTMETA placeholder
* cleaned up the RSS filters so HTML now is allowed in feed

= 5.1.4.3 =
* fixed bp group hidden events not going private
* fixed countries list not working for certain langauges

= 5.1.4.1 =
* fixed wp rewrite issue when assigned events page slug = events slug
* fixed minimum ticket price placheholder problems
* added dev version auto-updater
* improved performance in events with booking widget listings
* improved performance in date range searches (e.g. calendar)
* reverted to using .delegate() instead of jQuery 1.7+ .on() listener for compatibility
* slightly improved mail options logic and layout (plus php mail can send html emails now)
* fixed buddypress conflict if groups component is disabled
* fixed event spaces not overriding displayed values in booking stats pages

= 5.1.4 =
* pinpoint your location with dragable map markers!
* sortable booking table collumns and additional collumns
* seriously improved CSV exporting options with sortable booking collumns
* buddypress private group events now only shown to group members
* added extra ID collumns to event/location/category admin lists
* resend emails, change booking status, and modify booking form information as well as ticket numbers
* any aspect of a booking can now be edited front-end with or without BuddyPress
* customizable cancel booking message and confirmation
* 24 hour option for time pickers possible in admin area
* wp archive options visible now regardless of event/location page options
* further fixes to post thumbnail compatability (hopefully fixed for good!)
* added "Add New" location and recurring events to admin bar
* blank pending status corrected in my bookings pages
* fixed event categories when event is a subsite event shown on main site
* added Swedish datepicker translations
* fixed pagination issue
* updated Danish, Dutch, Swedish languange files
* small fix for duplicating plugins that keep the event id when saving dupe (WPML fix)
* added ticket name field in single ticket mode
* fixed pre 2k events showing in future events list in admin area
* changed calendar linking so it works on all/most themes without JS
* fixed bad translation of JS calendar days in Italian
* hard-coded country names for currently translated languages
* fixed some incompatibilities with Yoast Breadcrumbs
* modified date fixed in ical
* single event ical endpoint detection improved
* fixed reserved spaces miscalculation bug
* invalid taxonomy ids now stripped from searches
* improvements to wp rewrite compatability by registering events and locations in varied order
* fixed recurrence bug for single day events ending next early morning
* depreciated attributes deleted if blank and resaved
* single-event.php now overrides EM regardless of page settings
* front-end deleted events now trashed (if available) rather than deleted
* fixed errors on recurring event creation with bad ticket data
* admin localized js variables (messages) hidden to public
* php and wp mail functions called directly instead of via em/phpmailer
* depreciated js .live() calls and used jQuery 1.7's .on() function, hence min wp 3.3 version

= 5.1.3 =
* added is_past and is_future conditionals
* corrected conditional regex to allow multiple duplicate conditionals and nesting
* fixed some wp_rewrite irregularities due to slug combinations/conflicts
* fixed template format files not overriding


= 5.1.2 =
* fixed auto-delete bug where auto-draft recurring events deletes all events
* fixed recurrence pattern bug
* calendar ajax links are now SE and non JS friendly
* rss link fixed in non-permalinks mode
* added sorting options to my bookings page
* updated de_DE, cs_CZ, dk_DK
* fixed admin email not going out if booking approvals disabled

= 5.1.1 =
* fixed search JS bug, preventing searchings being made

= 5.1 =
* revised booking form template files (future-feature-proofing), simplified booking JS
* added readme files on updating templates with docs and commented further on templates
* improved booking button (allows cancellation)
* added disable email if subject is blank
* bookings can now have a overall spaces cap which override individual ticket spaces.
* improved compatibility with themes not supporting featured images
* improved the booking form ajax table to allow more customizability.
* calendars now will by default direct single event days directly to event page
* added various price related and location placeholders
* fixed post_id not being used in shortcode attributes
* fixed italian datepicker problem
* quick edit now reflects publish status
* GBP sign format corrected (encoding issue)
* fixed admin cross-post search function conflicts
* fixed some public side search issues
* fixed notes placeholders not formatting on some instances (e.g. categories)
* bp activity feed now reports cancelled bookings again
* location form now prefills region when loading prev. location
* fixed event sorting order for same-day events on archives (requires resaving the events).
* made times displayed on post tables use WP general time settings for consistency
* fixed empty tr in calendars
* fixed missing category meta box in MS global mode
* added  em_wp_localize_script filter for hooking into localization of js
* moved anonymous event submissions back into blog settings in MS mode
* added "email exists" customizable message

= 5.0.51 =
* fixed limit issue for calendars when no limit is set
* bookings feedback messages showing properly again
* manage my bookings page link functioning correctly now
* GBP pound sign fixed
* fixed location atts setting box not being checked for placeholders

= 5.0.50 =
* bookings table now within a unified ajax table
* added location attributes
* added currency formatting
* added "all day" format setting for #_EVENTTIMES
* added calendar ordering an limits (full-sized calendar)
* moved 'add new' button next to event lists outside wp-admin
* fixed some redirection issues for some themes
* small date formatting fixes for international english
* ticket bookings now deleted with overall booking
* categories now copied correctly when duplicating events
* tickets can now just be shown to logged out users
* added is_long, not_long, logged_in, not_logged_in, fully_booked and has_spaces conditionals
* fixed a preg issue with date formatting
* booking modifications possible again (ticket numbers)
* pending space counting in emails corrected

= 5.0.42 =
* changed csv booking time to 24 hr format
* fixed EM_URI error
* added logged_in not_logged_in conditional placeholders
* buddypress does not override pages defined in settings > pages > other pages
* default events install properly now
* title seperator problem fixed
* fixed add theme support for thumbnail function not firing early enough

= 5.0.41 =
* fixed fatal error on install (bug since 5.0.4)
* italian js translation added to prevent js error
* CVS title bug fix

= 5.0.4 =
* added installation throttle, to prevent double event imports
* pending events migrate as pending now
* index auto-correction for non-indexed events/locations on save
* wp_rewrite theme compat hack for some offending themes
* guest submission now showing success message
* added events_gcal shortcode for google calendar
* fixed bad BP links in edit event/location tables
* added single event ical endpoint
* added import to google event placeholder
* fixed em_content_pre problem
* group event locations label showing
* empty attributes are saved when previously filled

= 5.0.3 =
* searching from/to without one or another date works as intended
* fixed various old-named properties (and refreshed old properties after object save)
* fixed overriding front-end edit links from within admin area
* fixed #_EDITEVENTURL
* fixed location placeholder output/filter function
* fixed missing php in opening tag of search template

= 5.0.2 =
* fixed new booking id not being saved and passed to filters
* fixed booking placeholders not showing
* single category placeholders working for event formats
* search form has options section with configurable texts

= 5.0.1 =
* js correction preventing maps loading

= 5.0 =
* Events and Locations are now custom post types
* categories are now custom taxonomies
* events can have tags
* new placeholders, conditionals and search attributes
* BuddyPress module rewritten using 1.5 BP_Component api
* list pages split up, assign a page for each list
* extended page formatting options
* new time picker and improved datepicker
* various bugs fixed
* streamlined templates and consolidated varoius list templates
* event and location editors revamped, consolidated and basic CSS added for front-end forms
* Locations are now optional (if chosen)
* more capabilities added for finer permission control
* all day events possible

= 4.305 =
* fixed my-bookings.php template for pagination errors
* fixed duplicate tickets produced in buddypress editor
* removed console.log from js
* fixed owner=0 when admins create ownerless events
* fixed bp activity posting of member links in 1.5

= 4.304 =
* added pubdate to rss feed
* fixed datepickers in single ticket mode not showing saved dates
* fixed bookings view/edit link on second pages of ajax navigator
* added Aruba to countries list
* corrected login error string not being overriden
* removed bogus event settings page in BP (for now)
* attendees list now ommitting unconfirmed bookings
* booking addon pages should now override correctly
* improved buddypress activity notification (supports pro stuses and only one activity if a group event)

= 4.303 =
* fixed PHPMailer conflict when in wp_mail mode
* added html support in emails (if using smtp)
* new event owner now auto-selected
* tickets now duplicated along with event
* blank ticket price validation error fixed
* fixed 'available spaces' bug when only one remaining reserved/pending space is confirmed

= 4.302 =
* group events show proper links to event pages, not edit pages
* PHPMailer updated to v5.2.0
* can_manage bug in MS Mode fixed
* added more actions to ticket forms/tables
* events_calendar shortcode now filtering location search attributes as expected

= 4.301 =
* saving event tickets will now validate properly with meaningful errors
* my-bookings page will show/hide bookings depending on multisite settings (MS only)
* adjusted EM_Events output to use old preg from 4.212 but process custom atts beforehand

= 4.300 =
* more calendar css cleanup
* timepicker now working for public event forms
* user cancellation of bookings now an option in settings
* tax percentage option
* fixed compatability with yoast seo plugin (EM overwrites the title)
* custom attributes breaking when nested in conditional tags fixed
* BP child themes should work properly now without the plugins.php file
* ical DQUOTES problem removed
* events now receive approval confirmation by email
* optional user registration

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