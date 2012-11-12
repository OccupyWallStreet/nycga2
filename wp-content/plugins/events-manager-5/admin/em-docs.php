<?php

function em_docs_init($force_init = false){
	global $pagenow;
	if( ($pagenow == 'edit.php' && !empty($_GET['page']) && $_GET['page']=='events-manager-help' && class_exists('EM_Event')) || $force_init){
		add_action('wp_head', 'emd_head');
		//Generate the docs
		global $EM_Documentation;
		$EM_Event = new EM_Event();
		$event_fields = $EM_Event->get_fields(true);
		$EM_Location = new EM_Location();
		$location_fields = $EM_Location->get_fields(true);
		$EM_Documentation = array(
			'arguments' => array(
				'events' => array(
					'blog' => array( 'desc' => sprintf('Limit search to %s created in a specific blog id (MultiSite only)','events')),
					'bookings' => array( 'desc'=> 'Include only events with bookings enabled. Use \'user\' to show events a logged in user has booked.'.'1 = yes, 0 = no'),
					'category' => array( 'desc'=> sprintf('Supply a single id or comma-seperated ids (e.g. "1,2,3") to limit the search to events in these %s. You can also use negative numbers to exclude specific categories (e.g. -1,-2,-3).','categories'), 'default'=>0),
					'tag' => array( 'desc'=> sprintf('Supply a single id or comma-seperated ids (e.g. "music,theatre,sport") to limit the search to events in these %s.','tags'), 'default'=>0),
					'event' => array( 'desc'=> sprintf('Supply a single id or comma-seperated ids (e.g. "1,2,3") to limit the search to %s with the %s.','events', 'event_id(s)'), 'default'=>0),
					'group' => array( 'desc' => 'Limit search to events belonging to a specific group id (BuddyPress only). Using \'my\' will show events belonging to groups the logged in user is a member of.'),
					'post_id' => array( 'desc' => sprintf('Supply a single id or comma-seperated ids (e.g. "1,2,3") to limit the search to %s with the %s.','events', 'post_id(s)')),
					'private' => array( 'desc' => sprintf('Display private %s within your list?','events'), 'args' => '1 = yes, 0 = no', 'default' => 'If user can view private events, 1, otherwise 0.'),
					'private_only' => array( 'desc' =>sprintf('Display only private %s ?','events'), 'args' => '1 = yes, 0 = no', 'default' => '0'),
					'recurrence' => array( 'desc'=> 'If set to the event id of the recurring event, this will show only events this event recurrences.', 'default'=>0),
					'recurring' => array( 'desc'=> 'If set to 1, will only show recurring event templates. Only useful if you know what you\'re doing, use recurrence if you want events that are recurrences.', 'default'=>0),
					'search' => array( 'desc'=> 'Do a search for this string within event name, details and location address.' ),
					'status' => array( 'desc' => sprintf('Limit search to %s with a spefic status (1 is active, 0 is pending approval)','events'), 'default'=>1),
					'scope' => array( 'desc'=> 'Choose the time frame of events to show. Additionally you can supply dates (in format of YYYY-MM-DD), either single for events on a specific date or two dates seperated by a comma (e.g. 2010-12-25,2010-12-31) for events ocurring between these dates.', 'default'=>'future', 'args'=>array("future", "past", "today", "tomorrow", "month", "next-month", "1-months", "2-months", "3-months", "6-months", "12-months","all")),
					'year' => array( 'desc'=> 'If set to a year (e.g. 2010) only events that start or end during this year/month will be returned. Does not work as intended if used with scope.', 'default'=>''),
				),
				'locations' => array(
					'blog' => array( 'desc' => sprintf('Limit search to %s created in a specific blog id (MultiSite only)','locations')),
					'eventful' => array( 'desc'=> 'If set to 1 will only show locations that have at least one event occurring during the scope.', 'default' => 0),
					'eventless' => array( 'desc'=> 'If set to 1 will only show locations that have no events occurring during the scope.', 'default' => 0),
					'location' => array( 'desc'=> sprintf('Supply a single id or comma-seperated ids (e.g. "1,2,3") to limit the search to %s with the %s.','locations', 'location_id(s)'), 'default'=>0), 
					'post_id' => array( 'desc' => sprintf('Supply a single id or comma-seperated ids (e.g. "1,2,3") to limit the search to %s with the %s.','locations', 'post_id(s)')),
					'private' => array( 'desc' => sprintf('Display private %s within your list?','locations'), 'args' => '1 = yes, 0 = no', 'default' => 'If user can view private locations, 1, otherwise 0.'),
					'private_only' => array( 'desc' =>sprintf('Display only private %s ?','locations'), 'args' => '1 = yes, 0 = no', 'default' => '0'),
					'scope' => array( 'default' => 'all'),
					'status' => array( 'desc' => sprintf('Limit search to %s with a spefic status (1 is active, 0 is pending approval)','locations'), 'default'=>1),
					'country' => array( 'desc'=> sprintf('Search for %s in this %s (no partial matches, case sensitive).','locations','Country'), 'default' => 'none', 'args'=>'Use two-character country codes as defined in <a href="http://countrycode.org/">countrycode.org</a>, e.g. US,GB,ES'),
					'region' => array( 'desc'=> sprintf('Search for %s in this %s (no partial matches, case sensitive).','locations','Region'), 'default' => 'none'),
					'state' => array( 'desc'=> sprintf('Search for %s in this %s (no partial matches, case sensitive).','locations','State'), 'default' => 'none'),
					'town' => array( 'desc'=> sprintf('Search for %s in this %s (no partial matches, case sensitive).','locations','Town'), 'default' => 'none'),
					'postcode' => array( 'desc'=> sprintf('Search for %s in this %s (no partial matches, case sensitive).','locations','Postcode'), 'default' => 'none')
				),
				'categories' => array(
					'' => array( 'desc' => 'See the <a href="http://codex.wordpress.org/Function_Reference/get_terms">WordPress get_terms() Codex</a> for a list of possible search attributes/arguments.'),
				),
				'tags' => array(
					'' => array( 'desc' => 'See the <a href="http://codex.wordpress.org/Function_Reference/get_terms">WordPress get_terms() Codex</a> for a list of possible search attributes/arguments.'),
				),
				'calendar' => array(
					'full' => array( 'desc'=> 'If set to 1 it will display a full calendar that shows event names.', 'default' => 0),
					'long_events' => array( 'desc'=> 'If set to 1, will show events that last longer than a day.', 'default' => 0),
				),
				//The object is commonly shared by all, so entries above overwrite entries here
				'general' => array(
					'array' => array( 'desc'=> 'If you supply this as an argument, the returned data will be in an array, not an object (only useful wen using PHP, not shortcodes)', 'default'=>0),
					'format_header' => array( 'desc'=> sprintf('If you are displaying lists (e.g. listing events), you can supply the %s html and placeholders here.','header'), 'default'=> 'The relevant default format will be taken from the settings page.'),
					'format' => array( 'desc'=> 'If you are displaying some information with the shortcode or function (e.g. listing events), you can supply the html and placeholders here.', 'default'=> 'The relevant default format will be taken from the settings page.'),
					'format_footer' => array( 'desc'=> sprintf('If you are displaying lists (e.g. listing events), you can supply the %s html and placeholders here.','footer'), 'default'=> 'The relevant default format will be taken from the settings page.'), 
					'limit' => array( 'desc'=> 'Limits the amount of values returned to this number.', 'default'=>'0 (no limit)'),
					'offset' => array( 'desc'=> 'For example, if you have ten results, if you set this to 5, only the last 5 results will be returned. Useful for pagination.', 'default'=>0), 
					'order' => array( 'desc'=> 'Indicates the alphabeitcal/numerical order of the lists. Choose between ASC (ascending) and DESC (descending).', 'default'=>'ASC'),
					'orderby' => array( 'desc'=> 'Choose what fields to order your results by. You can supply a single field or multiple comma-seperated fields (e.g. "event_start_date,event_name").', 'default'=>0, 'args'=>'Database table fields, e.g. <code>event_name</code> or <code>location_name</code>'),
					'owner' => array('desc'=> 'Limits returned results to a specific owner, identified by their user id (e.g. list events or locations owned by user)', 'default'=>0),
					'pagination' => array('desc'=> 'When using a function or shortcode that outputs items (e.g. [events_list] for events, [locations_list] for locations), if the number of items supercede the limit of items to show, setting this to 1 will show page links under the list.', 'default'=>0),
				)
			),
			'placeholders' => array(
				'events' => array(
					'Event Details' => array(
						'placeholders' => array(
							'#_EVENTID' => array( 'desc' => 'Shows the event ID number in the wp_em_events table.' ),
							'#_EVENTPOSTID' => array( 'desc' => 'Shows the event corresponding Post ID in the wp_posts table.' ),
							'#_EVENTNAME' => array( 'desc' => 'Displays the name of the event.' ),
							'#_EVENTNOTES' => array( 'desc' => 'Shows the description of the event.' ),
							'#_EVENTEXCERPT' => array( 'desc' => 'If you added a <a href="http://en.support.wordpress.com/splitting-content/more-tag/">more tag</a> to your event description, only the content before this tag will show (currently, no read more link is added).' ),
							'#_EVENTIMAGE' => array( 'desc' => 'Shows the event image, if available.' ),
							'#_EVENTIMAGE{x,y}' => array( 'desc' => 'Shows the event image thumbnail, x and y are width and height respectively, both being numbers e.g. <code>#_EVENTIMAGE{100,100}</code>' ),
							'#_EVENTCATEGORIES' => array( 'desc' => 'Shows a list of category links this event belongs to.' ),
							'#_EVENTCATEGORIESIMAGES'  => array( 'desc' => 'Shows a list of category images this event belongs to. Categories without an image will be ignored.' ),
							'#_EVENTTAGS' => array( 'desc' => 'Shows a list of tag links this event belongs to.' ),
						)
					),
					'Date and Times' => array(
						'desc' => '',
						'placeholders' => array(
							'#_24HSTARTTIME' => array( 'desc' => 'Displays the start time in a 24 hours format (e.g. 16:30).' ),
							'#_24HENDTIME' => array( 'desc' => 'Displays the end time in a 24 hours format (e.g. 18:30).' ),
							'#_12HSTARTTIME' => array( 'desc' => 'Displays the start time in a 12 hours format (e.g. 4:30 PM).' ),
							'#_12HENDTIME' => array( 'desc' => 'Displays the end time in a 12 hours format (e.g. 6:30 PM).' ),
							'#_EVENTTIMES' => array( 'desc' => 'Displays either a single time, time-span, or "All Day" depending on your event times. Format is taken from your Events Manager settings page.' ),
							'#_EVENTDATES' => array( 'desc' => 'Displays either a single date or a date range depending on your event dates. Format is taken from your Events Manager settings page.' ),
						)
					),
					'Custom Date/Time Formatting' => array(
						'desc' => 'Events Manager allows extremely flexible date formatting by using <a href="http://www.php.net/manual/en/function.date.php">PHP date syntax format characters</a> along with placeholders.',
						'placeholders' => array(
							'# or #@' => array( 'desc' => 'Prepend <code>#</code> or <code>#@</code> before a valid PHP date syntax format character to show start and end date/time information respectively (e.g. <code>#F</code> will show the starting month name like "January", #@h shows the end hour).' ),
							'#{x} or #@{x}' => array( 'desc' => 'You can also create a date format without prepending # to each character by wrapping a valid php date() format in <code>#{}</code> or <code>#@{}</code> (e.g. <code>#_{d/m/Y}</code>). If there is no end date (or is same as start date), the value is not shown. This is useful if you want to show event end dates only on events that are longer than on day, e.g. <code>#j #M #Y #@_{ \u\n\t\i\l j M Y}</code>.' ),
						)
					),
					'Links/URLs' => array(
						'placeholders' => array(
							'#_EVENTIMAGEURL' => array( 'desc' => 'Shows the event image url, if available.' ),
							'#_EVENTURL' => array( 'desc' => 'Simply prints the event URL. You can use this placeholder to build your own customised links.' ),
							'#_EVENTLINK' => array( 'desc' => 'Displays the event name with a link to the event page.' ),
							'#_EDITEVENTLINK' => array( 'desc' => 'Inserts a link to the admin  or buddypress (if activated) edit event page, only if a user is logged in and is allowed to edit the event.' ),
							'#_EDITEVENTURL' => array( 'desc' => 'Inserts a url to the admin or buddypress (if activated) edit event page, only if a user is logged in and is allowed to edit the event.' )
						)
					),
					'Custom Attributes' => array(
						'desc' => 'Events Manager allows you to create dynamic attributes to your events, which act as extra information fields for your events (e.g. "Dress Code"). For more information see <a href="http://wp-events-plugin.com/documentation/categories-and-attributes/">our online documentation</a> for more info on attributes.',
						'placeholders' => array( 
							'#_ATT{key}' => array('desc'=> 'This key will appear as an option when adding attributes to your event.'),
							'#_ATT{key}{alternative text}' => array('desc'=> 'This key will appear as an option when adding attributes to your event. The text in the second braces will appear if the attribute is not defined or left blank for that event.'),
							'#_ATT{key}{option 1|option 2|option 3|etc.}' => array('desc'=> 'This key will appear as an option when adding attributes to your event. The second braces are optional and will use a select box with these values as input. If no valid value is defined, the first option is used.'),
						)
					),
					'Bookings' => array(
						'desc' => 'These placeholders will only show if bookings are enabled for the given event and in the events manager settings page. Spaces placeholders will default to 0',
						'placeholders' => array(
							'#_BOOKINGFORM' => array( 'desc' => 'Adds a booking forms for this event.' ),
							'#_BOOKINGBUTTON' => array( 'desc' => 'A single button that will appear to logged in users, if they click on it, they apply for a booking. This button will only display if there is one ticket.' ),
							'#_AVAILABLESPACES' => array( 'desc' => 'Shows available spaces for the event.' ),
							'#_BOOKEDSPACES' => array( 'desc' => 'Shows the amount of currently booked spaces for the event.' ),
							'#_PENDINGSPACES' => array( 'desc' => 'Shows the amount of pending spaces for the event.' ),
							'#_SPACES' => array( 'desc' => 'Shows the total spaces for the event.' ),
							'#_ATTENDEES' => array( 'desc' => 'Shows the list of user avatars attending the event.' ),
							'#_ATTENDEESLIST' => array( 'desc' => 'Shows the list of people attending the event.' ),
							'#_BOOKINGSURL' => array( 'desc' => 'Shows the url to the admin, front-end or buddypress (if activated) bookings management page for this event. Only shown if user is logged in and able to manage bookings.' ),
							'#_BOOKINGSLINK' => array( 'desc' => 'Shows a link to the admin, front-end or buddypress (if activated) bookings management page for this event. Only shown if user is logged in and able to manage bookings.' ),
							'#_EVENTPRICERANGE' => array( 'desc' => 'Shows a "maximum - minimum" price range, or a single price if there is no range. Price is formatted according to currency formatting in your settings page.' ),
							'#_EVENTPRICEMIN' => array( 'desc' => 'Shows the lowest ticket price for this event.' ),
							'#_EVENTPRICEMAX' => array( 'desc' => 'Shows the highest ticket price for this event.' ),								
						)
					),
					'Contact Details' => array(
						'desc' => 'The values here are taken from the chosen contact for the specific event, or the default contact in the settings page.',
						'placeholders' => array(
							'#_CONTACTNAME' => array( 'desc' => 'Name of the contact person for this event (as shown in the dropdown when adding an event).' ),
							'#_CONTACTUSERNAME' => array( 'desc' => 'Contact person\'s username.' ),
							'#_CONTACTEMAIL' => array( 'desc' => 'E-mail of the contact person for this event.' ),
							'#_CONTACTPHONE' => array( 'desc' => 'Phone number of the contact person for this event. Can be set in the user profile page.' ),
							'#_CONTACTAVATAR' => array( 'desc' => 'Contact person\'s avatar.' ),
							'#_CONTACTPROFILELINK' => array( 'desc' => 'Contact person\'s "Profile" link. Only works with BuddyPress enabled.' ),
							'#_CONTACTPROFILEURL' => array( 'desc' => 'Contact person\'s profile url. Only works with BuddyPress enabled.' ),
							'#_CONTACTID' => array( 'desc' => 'Contact person\'s WordPress user ID.'),
							'#_CONTACTMETA' => array( 'desc' => 'Display any user meta of a WordPress account by including the meta key, e.g. #_CONTACTMETA{dbem_phone}'),
						)
					),
					'iCal/Calendar' => array(
						'placeholders' => array(
							'#_EVENTICALURL' => array( 'desc' => 'Displays the URL of the event ical feed (ics file format).' ),
							'#_EVENTICALLINK' => array( 'desc' => 'Displays an html link to the event ical feed (ics file format).' ),
							'#_EVENTGCALURL' => array( 'desc' => 'Displays URL which would take the user to Google Calendar and pre-fill their add new event form.' ),
							'#_EVENTGCALLINK' => array( 'desc' => 'Displays a button which would take the user to Google Calendar and pre-fill their add new event form.' )
						)
					),
				),
				'categories' => array(
					'Category Details' => array(
						'placeholders' => array(
							'#_CATEGORYNAME' => array( 'desc' => 'Shows the category name of the event.' ),
							'#_CATEGORYID' => array( 'desc' => 'Shows the category ID of the event.' ),
							'#_CATEGORYIMAGE' => array( 'desc' => 'Shows the event image, if available.' ),
							'#_CATEGORYIMAGE{x,y}' => array( 'desc' => 'Shows the category image thumbnail, x and y are width and height respectively, both being numbers e.g. <code>#_CATEGORYIMAGE{100,100}</code>' ),
							'#_CATEGORYIMAGEURL' => array( 'desc' => 'Shows the category image url, if available.' ),
							'#_CATEGORYNOTES' => array( 'desc' => 'Shows the location description.' )
						)
					),			
					'Related Events' => array(
						'desc' => 'You can show lists of other events belonging to this category. The formatting of the list is the same as a normal events list.',
						'placeholders' => array(
							'#_CATEGORYPASTEVENTS' => array( 'desc' => 'Will show a list of all past events at this category.' ),
							'#_CATEGORYNEXTEVENTS' => array( 'desc' => 'Will show a list of all future events at this category.' ),
							'#_CATEGORYALLEVENTS' => array( 'desc' => 'Will show a list of all events at this category.' )
						)
					)				
				),
				'locations' => array(
					'Location Details' => array(
						'desc' => '',
						'placeholders' => array(
							'#_LOCATIONID' => array( 'desc' => 'Shows the event ID number in the wp_em_locations table.' ),
							'#_LOCATIONPOSTID' => array( 'desc' => 'Shows the location corresponding Post ID in the wp_posts table.' ),
							'#_LOCATIONNAME' => array( 'desc' => 'Displays the location name.' ),
							'#_LOCATIONADDRESS' => array( 'desc' => 'Displays the address.' ),
							'#_LOCATIONTOWN' => array( 'desc' => 'Displays the town.' ),
							'#_LOCATIONSTATE' => array( 'desc' => 'Displays the state/county.' ),
							'#_LOCATIONPOSTCODE' => array( 'desc' => 'Displays the postcode.' ),
							'#_LOCATIONREGION' => array( 'desc' => 'Displays the region.' ),
							'#_LOCATIONCOUNTRY' => array( 'desc' => 'Displays the country.' ),
							'#_LOCATIONMAP' => array( 'desc' => 'Displays a google map showing where the event is located (Will not show if maps are disabled in the settings page)' ),
							'#_LOCATIONNOTES' => array( 'desc' => 'Shows the location description.' ),
							'#_LOCATIONEXCERPT' => array( 'desc' => 'If you added a <a href="http://en.support.wordpress.com/splitting-content/more-tag/">more tag</a> to your location description, only the content before this tag will show (currently, no read more link is added).' ),
							'#_LOCATIONIMAGE' => array( 'desc' => 'Shows the location image.' ),
							'#_LOCATIONIMAGE{x,y}' => array( 'desc' => 'Shows the location image thumbnail, x and y are width and height respectively, both being numbers e.g. <code>#_LOCATIONIMAGE{100,100}</code>' ),
							'#_LOCATIONIMAGEURL' => array( 'desc' => 'Shows the location image url, if available.' ),
							'#_LOCATIONFULLLINE' => array( 'desc' => 'Shows a comma-seperated line of location information, ommitting blanks (format of address, town, state, postcode, region' ),
							'#_LOCATIONFULLBR' => array( 'desc' => 'Shows a line-break (br tag) seperated location information, ommitting blanks (format of address, town, state, postcode, region' ),
						)
					),
					'Custom Attributes' => array(
						'desc' => 'Events Manager allows you to create dynamic attributes to your locations, which act as extra information fields for your locations (e.g. "Dress Code"). For more information see <a href="http://wp-events-plugin.com/documentation/categories-and-attributes/">our online documentation</a> for more info on attributes.',
						'placeholders' => array( 
							'#_LATT{key}' => array('desc'=> 'This key will appear as an option when adding attributes to your location.'),
							'#_LATT{key}{alternative text}' => array('desc'=> 'This key will appear as an option when adding attributes to your location. The text in the second braces will appear if the attribute is not defined or left blank for that location.'),
							'#_LATT{key}{option 1|option 2|option 3|etc.}' => array('desc'=> 'This key will appear as an option when adding attributes to your location. The second braces are optional and will use a select box with these values as input. If no valid value is defined, the first option is used.'),
						)
					),
					'Links' => array(
						'placeholders' => array(
							'#_LOCATIONURL' => array( 'desc' => 'Simply prints the location URL. You can use this placeholder to build your own customised links.' ),
							'#_LOCATIONLINK' => array( 'desc' => 'Displays the location name with a link to the location page.' ),
							'#_EDITLOCATIONLINK' => array( 'desc' => 'Inserts a link to the admin  or buddypress (if activated) edit location page, only if a user is logged in and is allowed to edit the location.' ),
							'#_EDITLOCATIONURL' => array( 'desc' => 'Inserts a url to the admin or buddypress (if activated) edit location page, only if a user is logged in and is allowed to edit the location.' )
						)
					),			
					'Related Events' => array(
						'desc' => 'You can show lists of other events that are being held at this location. The formatting of the list is the same as a normal events list.',
						'placeholders' => array(
							'#_LOCATIONPASTEVENTS' => array( 'desc' => 'Will show a list of all past events at this location.' ),
							'#_LOCATIONNEXTEVENTS' => array( 'desc' => 'Will show a list of all future events at this location.' ),
							'#_LOCATIONALLEVENTS' => array( 'desc' => 'Will show a list of all events at this location.' ),
							'#_LOCATIONNEXTEVENT' => array( 'desc' => 'Will show a link to the next event at this location, or the no events message.' ),
						)
					),
				),
				'bookings' => array(
					'Booking Person Information' => array(
						'desc' => 'When a specific booking is displayed (on screen and on email), you can use these placeholders to show specific information about the booking. Event and Location placeholders are also available in these cases.',
						'placeholders' => array(
							'#_BOOKINGID' => array( 'desc' => 'The unique ID of this booking, useful if you are making your own customizations to this plugin.' ),
							'#_BOOKINGNAME' => array( 'desc' => 'Name of person who made the booking.' ),
							'#_BOOKINGEMAIL' => array( 'desc' => 'Email of person who made the booking.' ),
							'#_BOOKINGPHONE' => array( 'desc' => 'Phone number of person who made the booking.' ),
							'#_BOOKINGSPACES' => array( 'desc' => 'Number of spaces the person has booked.' ),
							'#_BOOKINGCOMMENT' => array( 'desc' => 'Any specific comments made by the person who made the booking.' ),
							'#_BOOKINGTICKETNAME' => array( 'desc' => 'Name of the ticket booked. Useful in single ticket mode, if multiple tickets are booked a random ticket is used.' ),
							'#_BOOKINGTICKETDESCRIPTION' => array( 'desc' => 'Description of the ticket booked. Useful in single ticket mode, if multiple tickets are booked a random ticket is used.' ),
							'#_BOOKINGTICKETPRICE' => array( 'desc' => 'Booked ticket price with currency symbol (e.g. $ 10.00). Useful in single ticket mode, if multiple tickets are booked a random ticket is used.' ),
							'#_BOOKINGTICKETS' => array( 'desc' => 'A list of booked tickets. You can modify this by using template files and modifying templates/emails/bookingtickets.php' ),
							'#_BOOKINGFORMCUSTOM{field_id}' => array( 'desc' => sprintf('(<a href="%s">pro only</a>) Shows booking form custom fields. The field_id value must match that of your custom booking form field.','http://wp-events-plugin.com/upgrade/') ),
							'#_BOOKINGFORMCUSTOMREG{field_id}' => array( 'desc' => sprintf('(<a href="%s">pro only</a>) Shows booking form custom fields that are used for guest user registration. The field_id value must match that of your custom booking form field.','http://wp-events-plugin.com/upgrade/') ),
							'#_BOOKINGFORMCUSTOMFIELDS' => array( 'desc' => sprintf('(<a href="%s">pro only</a>) Generates a list of booking form custom fields that are used in the booking.','http://wp-events-plugin.com/upgrade/') )
						)
					),
					'Pricing Information' => array(
						'desc' => '',
						'placeholders' => array(
							'#_BOOKINGPRICE' => array( 'desc' => 'Displays booking total price (tax inclusion depends on your booking settings).' ),
							'#_BOOKINGPRICETAX' => array( 'desc' => 'Displays booking total tax.' ),
							'#_BOOKINGPRICEWITHOUTTAX' => array( 'desc' => 'Displays booking total without tax.' ),
							'#_BOOKINGPRICEWITHTAX' => array( 'desc' => 'Displays booking total with tax.' ),
						)
					),
					'Ticket Information' => array(
						'desc' => '',
						'placeholders' => array(
							'#_BOOKINGTICKETS' => array( 'desc' => 'Shows a breakdown of tickets and pricing, defined in the <code>emails/bookingtickets.php</code> template. (See <a href="http://wp-events-plugin.com/documentation/using-template-files/">Using Template Files</a> for more information)' ),
							'#_BOOKINGTICKETDESCRIPTION' => array( 'desc' => 'Shows the description of the first ticket booked (useful in single ticket mode/events).' ),
							'#_BOOKINGTICKETPRICE' => array( 'desc' => 'Shows the price of the first ticket booked, tax inclusion depending on your booking settings (useful in single ticket mode/events).' ),
							'#_BOOKINGTICKETTAX' => array( 'desc' => 'Shows the tax of the first ticket booked (useful in single ticket mode/events).' ),
							'#_BOOKINGTICKETPRICEWITHTAX' => array( 'desc' => 'Shows the price including tax of the first ticket booked (useful in single ticket mode/events).' ),
							'#_BOOKINGTICKETPRICEWITHOUTTAX' => array( 'desc' => 'Shows the price excluding tax of the first ticket booked (useful in single ticket mode/events).' ),
						)
					),
					'Links' => array(
						'desc' => 'People are able to manage their bookings. Below are some placeholder which automatically provides correctly formatted urls',
						'placeholders' => array(
							'#_BOOKINGLISTURL' => array( 'desc' => 'URL to page showing that users booked events.' )
						)
					),
					'Gateway-Specific Information' => array(
						'desc' => '',
						'placeholders' => array(
							'#_BOOKINGTXNID' => array( 'desc' => '<em>Online Payments Only</em> - Prints the transaction ID of this booking if available.' )
						)
					),
				),
			),
			//TODO add capabilites explanations
			'capabilities' => array()
		);
	}
}
add_action('init', 'em_docs_init');

function em_docs_placeholders($atts){
	ob_start();
	?>
	<div class="em-docs">
		<?php
		global $EM_Documentation;
		$type = $atts['type'];
		$data = $EM_Documentation['placeholders'][$type];
		foreach($data as $sectionTitle => $details) : ?>
			<div>
				<h3><?php echo $sectionTitle; ?></h3>
				<?php if( !empty($details['desc']) ): ?>
				<p><?php echo $details['desc']; ?></p>
				<?php endif; ?>
				<dl>
					<?php foreach($details['placeholders'] as $placeholder => $desc ): ?>
					<dt><b><?php echo $placeholder; ?></b></dt>
					<dd><?php echo $desc['desc']; ?></dd>
					<?php endforeach; ?>
				</dl>
			</div>
		<?php endforeach; ?>
	</div>
	<?php
	return ob_get_clean();
}
?>