<?php

function em_docs_init(){
	if( class_exists('EM_Event') ){
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
					'search' => array( 'default'=> __('Do a search for this string within event name, details and location address.', 'dbem') ),	
					'limit' => array( 'default'=> __('See the events lists limit option on the settings page.', 'dbem') ),					
					'orderby' => array( 'desc'=> __('Choose what fields to order your results by. You can supply a single field or multiple comma-seperated fields (e.g. "start_date,name").', 'dbem'), 'default'=> __('See the event lists ordering option on the settings page.', 'dbem'), 'args'=>'name, start_date, start_time, end_date, end_time'),
					'order' => array( 'default'=> __('See the event lists ordering option on the settings page.', 'dbem') ),
					'bookings' => array( 'default'=> __('Include only events with bookings enabled. Use \'user\' to show events a logged in user has booked.', 'dbem') ),
					'status' => array( 'default' => __('Limit search to events with a spefic status (1 is active, 0 is pending approval)', 'dbem')),
					'blog' => array( 'default' => __('Limit search to events created in a specific blog (MultiSite only)', 'dbem')),
					'group' => array( 'default' => __('Limit search to events belonging to a specific group id (BuddyPress only). Using \'my\' will show events belonging to groups the logged in user is a member of.', 'dbem')),
					'town' => array( 'desc'=> __('Search for events in this town (no partial matches, case sensitive).', 'dbem'), 'default' => __('none','dbem')),
					'state' => array( 'desc'=> __('Search for events in this state (no partial matches, case sensitive).', 'dbem'), 'default' => __('none','dbem')),
					'region' => array( 'desc'=> __('Search for events in this region (no partial matches, case sensitive).', 'dbem'), 'default' => __('none','dbem')),
					'country' => array( 'desc'=> __('Search for events in this country (no partial matches, case sensitive).', 'dbem'), 'default' => __('none','dbem')),
					'postcode' => array( 'desc'=> __('Search for events in this postcode (no partial matches, case sensitive).', 'dbem'), 'default' => __('none','dbem')),
					'format_header' => array( 'default' => __('When displaying event lists, you can supply specific HTML to replace the default header from the settings page.', 'dbem')),
					'format_footer' => array( 'default' => __('When displaying event lists, you can supply specific HTML to replace the default footer from the settings page.', 'dbem')),
				),
				'locations' => array(
					'eventful' => array( 'desc'=> __('If set to 1 will only show locations that have at least one event occurring during the scope.', 'dbem'), 'default' => 0),
					'eventless' => array( 'desc'=> __('If set to 1 will only show locations that have no events occurring during the scope.', 'dbem'), 'default' => 0),
					'orderby' => array('desc'=> __('Choose what fields to order your results by. You can supply a single field or multiple comma-seperated fields (e.g. "start_date,name").', 'dbem'), 'default'=>'name', 'args' => 'name, address, town'),
					'scope' => array( 'default' => 'all'),
					'town' => array( 'desc'=> __('Search for locations in this town (no partial matches, case sensitive).', 'dbem'), 'default' => __('none','dbem')),
					'state' => array( 'desc'=> __('Search for locations in this state (no partial matches, case sensitive).', 'dbem'), 'default' => __('none','dbem')),
					'region' => array( 'desc'=> __('Search for locations in this region (no partial matches, case sensitive).', 'dbem'), 'default' => __('none','dbem')),
					'country' => array( 'desc'=> __('Search for locations in this country (no partial matches, case sensitive).', 'dbem'), 'default' => __('none','dbem')),
					'postcode' => array( 'desc'=> __('Search for locations in this postcode (no partial matches, case sensitive).', 'dbem'), 'default' => __('none','dbem'))
				),
				'calendar' => array(
					'full' => array( 'desc'=> __('If set to 1 it will display a full calendar that shows event names.', 'dbem'), 'default' => 0),
					'long_events' => array( 'desc'=> __('If set to 1, will show events that last longer than a day.', 'dbem'), 'default' => 0),
					'order' => array( 'desc'=> __('Same as for events.', 'dbem') ),
					'scope' => array( 'default' => 'future')
				),
				//The object is commonly shared by all, so entries above overwrite entries here
				'general' => array(
					'limit' => array( 'desc'=> __('Limits the amount of values returned to this number.', 'dbem'), 'default'=>'0 (no limit)'),
					'scope' => array( 'desc'=> __('Choose the time frame of events to show. Accepted values are "future", "past", "today", "tomorrow", "month", "next-month", "1-months", "2-months", "3-months", "6-months", "12-months" or "all" events. Additionally you can supply dates (in format of YYYY-MM-DD), either single for events on a specific date or two dates seperated by a comma (e.g. 2010-12-25,2010-12-31) for events ocurring between these dates.', 'dbem'), 'default'=>'future'),
					'order' => array( 'desc'=> __('Indicates the order of the events. Choose between ASC (ascending) and DESC (descending).', 'dbem'), 'default'=>'ASC'),
					'orderby' => array( 'desc'=> __('Choose what fields to order your results by. You can supply a single field or multiple comma-seperated fields (e.g. "start_date,name"). See specific instances (e.g. events, locations, etc.) for field names.', 'dbem'), 'default'=>0),
					'format' => array( 'desc'=> __('If you are displaying some information with the shortcode or function (e.g. listing events), you can supply the html and placeholders here.', 'dbem'), 'default'=> __('The relevant default format will be taken from the settings page.', 'dbem')), 
					'event' => array( 'desc'=> __('Supply a single id or comma-seperated ids (e.g. "1,2,3") to limit the search to events with these id(s).', 'dbem'), 'default'=>0),
					'category' => array( 'desc'=> __('Supply a single id or comma-seperated ids (e.g. "1,2,3") to limit the search to events in these categories.', 'dbem'), 'default'=>0), 
					'location' => array( 'desc'=> __('Supply a single id or comma-seperated ids to limit the search to these locations (or events in these locations).', 'dbem'), 'default'=>0), 
					'offset' => array( 'desc'=> __('For example, if you have ten results, if you set this to 5, only the last 5 results will be returned. Useful for pagination.', 'dbem'), 'default'=>0), 
					'recurrence' => array( 'desc'=> __('If set to 1, will show only events that are recurring (i.e. events that are repeated over different dates).', 'dbem'), 'default'=>0),
					'recurring' => array( 'desc'=> __('If set to 1, will only show recurring event templates. Only useful if you know what you\'re doing, use recurrence if you want events that are recurrences.', 'dbem'), 'default'=>0),
					'month' => array( 'desc'=> __('If set to a month (1 to 12) only events that start or end during this month/year will be retured. Must be used in conjunction with year and does not work as intended if used with scope.', 'dbem'), 'default'=>''),
					'year' => array( 'desc'=> __('If set to a year (e.g. 2010) only events that start or end during this year/month will be returned. Does not work as intended if used with scope.', 'dbem'), 'default'=>''),
					'array' => array( 'desc'=> __('If you supply this as an argument, the returned data will be in an array, no objects (only useful wen using PHP, not shortcodes)', 'dbem'), 'default'=>0),
					'pagination' => array('desc'=> __('When using a function or shortcode that outputs items (e.g. [events_list] for events, [locations_list] for locations), if the number of items supercede the limit of items to show, setting this to 1 will show page links under the list.', 'dbem'), 'default'=>0),
					'owner' => array('desc'=> __('Limits returned results to a specific owner, identified by their usaer id (e.g. list events or locations owned by user)', 'dbem'), 'default'=>0)
				)
			),
			'placeholders' => array(
				'events' => array(
					'Event Details' => array(
						'placeholders' => array(
							'#_NAME' => array( 'desc' => __('Displays the name of the event.', 'dbem') ),
							'#_NOTES' => array( 'desc' => __('Shows the description of the event.', 'dbem') ),
							'#_EXCERPT' => array( 'desc' => __('If you added a <a href="http://en.support.wordpress.com/splitting-content/more-tag/">more tag</a> to your event description, only the content before this tag will show (currently, no read more link is added).', 'dbem') ),
							'#_EVENTID' => array( 'desc' => __('Shows the event corresponding ID number in the database table.', 'dbem') ),
							'#_EVENTIMAGE' => array( 'desc' => __('Shows the event image, if available.', 'dbem') ),
							'#_EVENTIMAGE{x,y}' => array( 'desc' => __('Shows the event image thumbnail, x and y are width and height respectively, both being numbers e.g. <code>#_EVENTIMAGE{100,100}</code>', 'dbem') ),
							'#_EVENTIMAGEURL' => array( 'desc' => __('Shows the event image url, if available.', 'dbem') ),
							'#_CATEGORIES' => array( 'desc' => __('Shows a list of category links this event belongs to.', 'dbem') ),
						)
					),			
					'Time' => array(
						'desc' => '',
						'placeholders' => array(
							'#_24HSTARTTIME' => array( 'desc' => __('Displays the start time in a 24 hours format (e.g. 16:30).', 'dbem') ),
							'#_24HENDTIME' => array( 'desc' => __('Displays the end time in a 24 hours format (e.g. 18:30).', 'dbem') ),
							'#_12HSTARTTIME' => array( 'desc' => __('Displays the start time in a 12 hours format (e.g. 4:30 PM).', 'dbem') ),
							'#_12HENDTIME' => array( 'desc' => __('Displays the end time in a 12 hours format (e.g. 6:30 PM).', 'dbem') )
						)
					),
					'Custom Date/Time Formatting' => array(
						'desc' => __('Events Manager allows extremely flexible date formatting by using <a href="http://www.php.net/manual/en/function.date.php">PHP date syntax format characters</a> along with placeholders.', 'dbem'),
						'placeholders' => array(
							'# or #@' => array( 'desc' => __('Prepend <code>#</code> or <code>#@</code> before a valid PHP date syntax format character to show start and end date/time information respectively (e.g. <code>#F</code> will show the starting month name like "January", #@h shows the end hour).', 'dbem') ),
							'#{x} or #@{x}' => array( 'desc' => __('You can also create a date format without prepending # to each character by wrapping a valid php date() format in <code>#{}</code> or <code>#@{}</code> (e.g. <code>#_{d/m/Y}</code>). If there is no end date (or is same as start date), the value is not shown. This is useful if you want to show event end dates only on events that are longer than on day, e.g. <code>#j #M #Y #@_{ \u\n\t\i\l j M Y}</code>.', 'dbem') ),
						)
					),
					'Links' => array(
						'placeholders' => array(
							'#_EVENTURL' => array( 'desc' => __('Simply prints the event URL. You can use this placeholder to build your own customised links.', 'dbem') ),
							'#_EVENTIMAGEURL' => array( 'desc' => __('Shows the event image url, if available.', 'dbem') ),
							'#_EVENTLINK' => array( 'desc' => __('Displays the event name with a link to the event page.', 'dbem') ),
							'#_EDITEVENTLINK' => array( 'desc' => __('Inserts a link to the admin  or buddypress (if activated) edit event page, only if a user is logged in and is allowed to edit the event.', 'dbem') ),
							'#_EDITEVENTURL' => array( 'desc' => __('Inserts a url to the admin or buddypress (if activated) edit event page, only if a user is logged in and is allowed to edit the event.', 'dbem') )
						)
					),
					'Custom Attributes' => array(
						'desc' => __('Events Manager allows you to create dynamic attributes to your events, which act as extra information fields for your events (e.g. "Dress Code"). For more information see <a href="http://wp-events-plugin.com/documentation/categories-and-attributes/">our online documentation</a> for more info on attributes.', 'dbem'),
						'placeholders' => array( 
							'#_ATT{key}' => array('desc'=> __('This key will appear as an option when adding attributes to your event.', 'dbem')),
							'#_ATT{key}{alternative text}' => array('desc'=> __('This key will appear as an option when adding attributes to your event. The text in the second braces will appear if the attribute is not defined or left blank for that event.', 'dbem')),
							'#_ATT{key}{option 1|option 2|option 3|etc.}' => array('desc'=> __('This key will appear as an option when adding attributes to your event. The second braces are optional and will use a select box with these values as input. If no valid value is defined, the first option is used.', 'dbem')),
						)
					),
					'Bookings' => array(
						'desc' => __('These placeholders will only show if bookings are enabled for the given event and in the events manager settings page. Spaces placeholders will default to 0', 'dbem'),
						'placeholders' => array(
							'#_BOOKINGFORM' => array( 'desc' => __('Adds a booking forms for this event.', 'dbem') ),
							'#_AVAILABLESPACES' => array( 'desc' => __('Shows available spaces for the event.', 'dbem') ),
							'#_BOOKEDSPACES' => array( 'desc' => __('Shows the amount of currently booked spaces for the event.', 'dbem') ),
							'#_PENDINGSPACES' => array( 'desc' => __('Shows the amount of pending spaces for the event.', 'dbem') ),
							'#_SPACES' => array( 'desc' => __('Shows the total spaces for the event.', 'dbem') ),
							'#_ATTENDEES' => array( 'desc' => __('Shows the list of user avatars attending events.', 'dbem') ),
							'#_BOOKINGBUTTON' => array( 'desc' => __('A single button that will appear to logged in users, if they click on it, they apply for a booking. This button will only display if there is one ticket.', 'dbem') ),
							'#_BOOKINGSURL' => array( 'desc' => __('Shows the url to the admin or buddypress (if activated) bookings management page for this event. Only shown if user is logged in and able to manage bookings.', 'dbem') ),
							'#_BOOKINGSLINK' => array( 'desc' => __('Shows a link to the admin or buddypress (if activated) bookings management page for this event. Only shown if user is logged in and able to manage bookings.', 'dbem') )							
						)
					),
					'Contact Details' => array(
						'desc' => __('The values here are taken from the chosen contact for the specific event, or the default contact in the settings page.', 'dbem'),
						'placeholders' => array(
							'#_CONTACTNAME' => array( 'desc' => __('Name of the contact person for this event (as shown in the dropdown when adding an event).', 'dbem') ),
							'#_CONTACTUSERNAME' => array( 'desc' => __('Contact person\'s username.', 'dbem') ),
							'#_CONTACTEMAIL' => array( 'desc' => __('E-mail of the contact person for this event.', 'dbem') ),
							'#_CONTACTPHONE' => array( 'desc' => __('Phone number of the contact person for this event. Can be set in the user profile page.', 'dbem') ),
							'#_CONTACTAVATAR' => array( 'desc' => __('Contact person\'s avatar.', 'dbem') ),
							'#_CONTACTPROFILELINK' => array( 'desc' => __('Contact person\'s "Profile" link. Only works with BuddyPress enabled.', 'dbem') ),
							'#_CONTACTPROFILEURL' => array( 'desc' => __('Contact person\'s profile url. Only works with BuddyPress enabled.', 'dbem') ),
							'#_CONTACTID' => array( 'desc' => __('Contact person\'s wordpress user ID.', 'dbem'))
						)
					),			
				),
				'categories' => array(
					'Category Details' => array(
						'placeholders' => array(
							'#_CATEGORYNAME' => array( 'desc' => __('Shows the category name of the event.', 'dbem') ),
							'#_CATEGORYID' => array( 'desc' => __('Shows the category ID of the event.', 'dbem') ),
							'#_CATEGORYIMAGE' => array( 'desc' => __('Shows the event image, if available.', 'dbem') ),
							'#_CATEGORYIMAGE{x,y}' => array( 'desc' => __('Shows the category image thumbnail, x and y are width and height respectively, both being numbers e.g. <code>#_CATEGORYIMAGE{100,100}</code>', 'dbem') ),
							'#_CATEGORYIMAGEURL' => array( 'desc' => __('Shows the category image url, if available.', 'dbem') ),
							'#_CATEGORYNOTES' => array( 'desc' => __('Shows the location description.', 'dbem') )
						)
					),			
					'Related Events' => array(
						'desc' => __('You can show lists of other events belonging to this category. The formatting of the list is the same as a normal events list.', 'dbem'),
						'placeholders' => array(
							'#_CATEGORYPASTEVENTS' => array( 'desc' => __('Will show a list of all past events at this category.', 'dbem') ),
							'#_CATEGORYNEXTEVENTS' => array( 'desc' => __('Will show a list of all future events at this category.', 'dbem') ),
							'#_CATEGORYALLEVENTS' => array( 'desc' => __('Will show a list of all events at this category.', 'dbem') )
						)
					)				
				),
				'locations' => array(
					'Location Details' => array(
						'desc' => '',
						'placeholders' => array(
							'#_LOCATIONID' => array( 'desc' => __('Displays the location ID number.', 'dbem') ),
							'#_LOCATIONNAME' => array( 'desc' => __('Displays the location name.', 'dbem') ),
							'#_LOCATIONADDRESS' => array( 'desc' => __('Displays the address.', 'dbem') ),
							'#_LOCATIONTOWN' => array( 'desc' => __('Displays the town.', 'dbem') ),
							'#_LOCATIONSTATE' => array( 'desc' => __('Displays the state/county.', 'dbem') ),
							'#_LOCATIONPOSTCODE' => array( 'desc' => __('Displays the postcode.', 'dbem') ),
							'#_LOCATIONREGION' => array( 'desc' => __('Displays the region.', 'dbem') ),
							'#_LOCATIONCOUNTRY' => array( 'desc' => __('Displays the country.', 'dbem') ),
							'#_LOCATIONMAP' => array( 'desc' => __('Displays a google map showing where the event is located (Will not show if maps are disabled in the settings page)', 'dbem') ),
							'#_LOCATIONNOTES' => array( 'desc' => __('Shows the location description.', 'dbem') ),
							'#_LOCATIONEXCERPT' => array( 'desc' => __('If you added a <a href="http://en.support.wordpress.com/splitting-content/more-tag/">more tag</a> to your location description, only the content before this tag will show (currently, no read more link is added).', 'dbem') ),
							'#_LOCATIONIMAGE' => array( 'desc' => __('Shows the location image.', 'dbem') ),
							'#_LOCATIONIMAGE{x,y}' => array( 'desc' => __('Shows the location image thumbnail, x and y are width and height respectively, both being numbers e.g. <code>#_LOCATIONIMAGE{100,100}</code>', 'dbem') ),
							'#_LOCATIONIMAGEURL' => array( 'desc' => __('Shows the cattegory image url, if available.', 'dbem') )
						)
					),
					'Links' => array(
						'placeholders' => array(
							'#_LOCATIONURL' => array( 'desc' => __('Simply prints the location URL. You can use this placeholder to build your own customised links.', 'dbem') ),
							'#_LOCATIONLINK' => array( 'desc' => __('Displays the location name with a link to the location page.', 'dbem') )
						)
					),			
					'Related Events' => array(
						'desc' => __('You can show lists of other events that are being held at this location. The formatting of the list is the same as a normal events list.', 'dbem'),
						'placeholders' => array(
							'#_LOCATIONPASTEVENTS' => array( 'desc' => __('Will show a list of all past events at this location.', 'dbem') ),
							'#_LOCATIONNEXTEVENTS' => array( 'desc' => __('Will show a list of all future events at this location.', 'dbem') ),
							'#_LOCATIONALLEVENTS' => array( 'desc' => __('Will show a list of all events at this location.', 'dbem') )
						)
					),
				),
				'bookings' => array(
					'Booking Person Information' => array(
						'desc' => __('When a specific booking is displayed (on screen and on email), you can use these placeholders to show specific information about the booking. For contact details of the contact of this event, see the events placeholders.', 'dbem'),
						'placeholders' => array(
							'#_BOOKINGNAME' => array( 'desc' => __('Name of person who made the booking.', 'dbem') ),
							'#_BOOKINGEMAIL' => array( 'desc' => __('Email of person who made the booking.', 'dbem') ),
							'#_BOOKINGPHONE' => array( 'desc' => __('Phone number of person who made the booking.', 'dbem') ),
							'#_BOOKINGSPACES' => array( 'desc' => __('Number of spaces the person has booked.', 'dbem') ),
							'#_BOOKINGCOMMENT' => array( 'desc' => __('Any specific comments made by the person who made the booking.', 'dbem') ),
							'#_BOOKINGTICKETNAME' => array( 'desc' => __('Name of the ticket booked. Useful in single ticket mode, if multiple tickets are booked a random ticket is used.', 'dbem') ),
							'#_BOOKINGTICKETDESCRIPTION' => array( 'desc' => __('Description of the ticket booked. Useful in single ticket mode, if multiple tickets are booked a random ticket is used.', 'dbem') ),
							'#_BOOKINGTICKETPRICE' => array( 'desc' => __('Booked ticket price with currency symbol (e.g. $ 10.00). Useful in single ticket mode, if multiple tickets are booked a random ticket is used.', 'dbem') ),
							'#_BOOKINGTICKETS' => array( 'desc' => __('A list of booked tickets. You can modify this by using template files and modifying templates/emails/bookingtickets.php', 'dbem') ),
							'#_BOOKINGFORMCUSTOM{field_id}' => array( 'desc' => sprintf(__('(<a href="%s">pro only</a>) Shows booking form custom fields. The field_id value must match that of your custom booking form field.', 'dbem'),'http://wp-events-plugin.com/upgrade/') ),
							'#_BOOKINGFORMCUSTOMREG{field_id}' => array( 'desc' => sprintf(__('(<a href="%s">pro only</a>) Shows booking form custom fields that are used for guest user registration. The field_id value must match that of your custom booking form field.', 'dbem'),'http://wp-events-plugin.com/upgrade/') )
						)
					),
					'Links' => array(
						'desc' => __('People are able to manage their bookings. Below are some placeholder which automatically provides correctly formatted urls', 'dbem'),
						'placeholders' => array(
							'#_BOOKINGLISTURL' => array( 'desc' => __('URL to page showing that users booked events.', 'dbem') )
						)
					)
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