<?php
//
//  class-ai1ec-event.php
//  all-in-one-event-calendar
//
//  Created by The Seed Studio on 2011-07-13.
//

/**
 * Ai1ec_Event class
 *
 * @package Models
 * @author time.ly
 **/
class Ai1ec_Event {
	/**
	 * post class variable
	 *
	 * @var object
	 **/
	var $post;

	/**
	 * post_id class variable
	 *
	 * @var int
	 **/
	var $post_id;

	/**
	 * instance_id class variable
	 *
	 * Uniquely identifies the recurrence instance of this event object. This
	 * may be null.
	 *
	 * @var int|null
	 **/
	var $instance_id;

	/**
	 * start class variable
	 *
	 * @var int
	 **/
	var $start;

	/**
	 * end class variable
	 *
	 * @var int
	 **/
	var $end;

	/**
	 * start_truncated class variable
	 *
	 * Whether this copy of the event was broken up for rendering and the start
	 * time is not its "real" start time.
	 *
	 * @var bool
	 **/
	var $start_truncated;

	/**
	 * end_truncated class variable
	 *
	 * Whether this copy of the event was broken up for rendering and the end
	 * time is not its "real" end time.
	 *
	 * @var bool
	 **/
	var $end_truncated;

	/**
	 * allday class variable
	 *
	 * @var int
	 **/
	var $allday;

	/**
	 * recurrence_rules class variable
	 *
	 * @var string
	 **/
	var $recurrence_rules;

	/**
	 * exception_rules class variable
	 *
	 * @var string
	 **/
	var $exception_rules;

	/**
	 * recurrence_dates class variable
	 *
	 * @var string
	 **/
	var $recurrence_dates;

	/**
	 * exception_dates class variable
	 *
	 * @var string
	 **/
	var $exception_dates;

	/**
	 * venue class variable
	 *
	 * @var string
	 **/
	var $venue;

	/**
	 * country class variable
	 *
	 * @var string
	 **/
	var $country;

	/**
	 * address class variable
	 *
	 * @var string
	 **/
	var $address;

	/**
	 * city class variable
	 *
	 * @var string
	 **/
	var $city;

	/**
	 * province class variable
	 *
	 * @var string
	 **/
	var $province;

	/**
	 * postal_code class variable
	 *
	 * @var int
	 **/
	var $postal_code;

	/**
	 * show_map class variable
	 *
	 * @var int
	 **/
	var $show_map;

	/**
	 * longitude class variable
	 *
	 * @var int
	 **/
	var $show_coordinates;

	/**
	 * longitude class variable
	 *
	 * @var float
	 **/
	var $longitude;

	/**
	 * latitude class variable
	 *
	 * @var float
	 **/
	var $latitude;

	/**
	 * facebook_eid class variable
	 *
	 * @var bigint
	 **/
	var $facebook_eid;

	/**
	 * facebook_user class variable
	 *
	 * @var bigint
	 **/
	var $facebook_user;

	/**
	 * facebook_status class variable
	 *
	 * @var char
	 **/
	var $facebook_status;

	/**
	 * contact_name class variable
	 *
	 * @var string
	 **/
	var $contact_name;

	/**
	 * contact_phone class variable
	 *
	 * @var string
	 **/
	var $contact_phone;

	/**
	 * contact_email class variable
	 *
	 * @var string
	 **/
	var $contact_email;

	/**
	 * cost class variable
	 *
	 * @var string
	 **/
	var $cost;

	// ====================================
	// = iCalendar feed (.ics) properties =
	// ====================================
	/**
	 * ical_feed_url class variable
	 *
	 * @var string
	 **/
	var $ical_feed_url;

	/**
	 * ical_source_url class variable
	 *
	 * @var string
	 **/
	var $ical_source_url;

	/**
	 * ical_organizer class variable
	 *
	 * @var string
	 **/
	var $ical_organizer;

	/**
	 * ical_contact class variable
	 *
	 * @var string
	 **/
	var $ical_contact;

	/**
	 * ical_uid class variable
	 *
	 * @var string | int
	 **/
	var $ical_uid;

	// ============
	// = Taxonomy =
	// ============
	/**
	 * tags class variable
	 *
	 * Associated event tag IDs, joined by commas.
	 *
	 * @var string
	 **/
	var $tags;

	/**
	 * categories class variable
	 *
	 * Associated event category IDs, joined by commas.
	 *
	 * @var string
	 **/
	var $categories;

	/**
	 * feed class variable
	 *
	 * Associated event feed object
	 *
	 * @var string
	 **/
	var $feed;

	/**
	 * category_colors class variable
	 *
	 * @var string
	 **/
	private $category_colors;

	/**
	 * color_style class variable
	 *
	 * @var string
	 **/
	private $color_style;

	/**
	 * category_text_color class variable
	 *
	 * @var string
	 **/
	private $category_text_color;

	/**
	 * category_bg_color class variable
	 *
	 * @var string
	 **/
	private $category_bg_color;

	/**
	 * faded_color class variable
	 *
	 * @var string
	 **/
	private $faded_color;

	/**
	 * tags_html class variable
	 *
	 * A cache variable, used by __get().
	 *
	 * @var string
	 **/
	private $tags_html;

	/**
	 * categories_html class variable
	 *
	 * A cache variable, used by __get().
	 *
	 * @var string
	 **/
	private $categories_html;

	/**
	 * __construct function
	 *
	 * Create new event object, using provided data for initialization.
	 *
	 * @param int|array $data  Look up post with id $data, or initialize fields
	 *                         with flat associative array $data containing both
	 *                         post and event fields returned by join query
	 *
	 * @return void
	 **/
	function __construct( $data = null, $instance = false ) {
		global $wpdb;

		if( $data == null )
			return;

		// ===========
		// = Post ID =
		// ===========
		if( is_numeric( $data ) )
	 	{
			// ============================
			// = Fetch post from database =
			// ============================
			$post = get_post( $data );

			if( ! $post || $post->post_status == 'auto-draft' )
				throw new Ai1ec_Event_Not_Found( "Post with ID '$data' could not be retrieved from the database." );

			$left_join      = "";
			$select_sql     = "e.post_id, e.recurrence_rules, e.exception_rules, e.allday, " .
			                  "e.recurrence_dates, e.exception_dates, e.venue, e.country, e.address, e.city, e.province, e.postal_code, " .
			                  "e.show_map, e.contact_name, e.contact_phone, e.contact_email, e.cost, e.ical_feed_url, e.ical_source_url, " .
			                  "e.ical_organizer, e.ical_contact, e.ical_uid, e.longitude, e.latitude, e.show_coordinates, e.facebook_eid, e.facebook_status, e.facebook_user, " .
			                  "GROUP_CONCAT( ttc.term_id ) AS categories, " .
			                  "GROUP_CONCAT( ttt.term_id ) AS tags ";

			if( $instance != false && is_numeric( $instance ) ) {
				$select_sql .= ", IF( aei.start IS NOT NULL, UNIX_TIMESTAMP( aei.start ), UNIX_TIMESTAMP( e.start ) ) as start," .
				               "  IF( aei.start IS NOT NULL, UNIX_TIMESTAMP( aei.end ),   UNIX_TIMESTAMP( e.end ) )   as end ";

				$instance = (int) $instance;
				$left_join = 	"LEFT JOIN {$wpdb->prefix}ai1ec_event_instances aei ON aei.id = $instance AND e.post_id = aei.post_id ";
			} else {
				$select_sql .= ", UNIX_TIMESTAMP( e.start ) as start, UNIX_TIMESTAMP( e.end ) as end, e.allday ";
			}
			// =============================
			// = Fetch event from database =
			// =============================
			$query = $wpdb->prepare(
				"SELECT {$select_sql}" .
				"FROM {$wpdb->prefix}ai1ec_events e " .
					"LEFT JOIN $wpdb->term_relationships tr ON e.post_id = tr.object_id " .
					"LEFT JOIN $wpdb->term_taxonomy ttc ON tr.term_taxonomy_id = ttc.term_taxonomy_id AND ttc.taxonomy = 'events_categories' " .
					"LEFT JOIN $wpdb->term_taxonomy ttt ON tr.term_taxonomy_id = ttt.term_taxonomy_id AND ttt.taxonomy = 'events_tags' " .
					"{$left_join}" .
				"WHERE e.post_id = %d " .
				"GROUP BY e.post_id",
				$data );
			$event = $wpdb->get_row( $query );

			if( $event === null || $event->post_id === null )
				throw new Ai1ec_Event_Not_Found( "Event with ID '$data' could not be retrieved from the database." );

			// ===========================
			// = Assign post to property =
			// ===========================
			$this->post = $post;

			// ==========================
			// = Assign values to $this =
			// ==========================
			foreach( $this as $property => $value ) {
				if( $property != 'post' ) {
				  if( isset( $event->{$property} ) )
				    $this->{$property} = $event->{$property};
				}
			}
		}
		// ===================
		// = Post/event data =
		// ===================
		elseif( is_array( $data ) )
	 	{
			// =======================================================
			// = Assign each event field the value from the database =
			// =======================================================
			foreach( $this as $property => $value ) {
				if( $property != 'post' && array_key_exists( $property, $data ) ) {
					$this->{$property} = $data[$property];
					unset( $data[$property] );
				}
			}
			if( isset( $data['post'] ) ) {
				$this->post = (object) $data['post'];
			} else {
				// ========================================
				// = Remaining fields are the post fields =
				// ========================================
				$this->post = (object) $data;
			}
		}
		else {
			throw new Ai1ec_Invalid_Argument( "Argument to constructor must be integer, array or null, not '$data'." );
		}
 	}

	/**
	 * __set function
	 *
	 * Magic set function
	 *
	 * @param string $name Property name
	 * @param mixed $value Property value
	 *
	 * @return void
	 **/
	public function __set( $name, $value ) {
		// Not currently used...
		switch( $name ) {
			default:
				$this->{$name} = $value;
				break;
		}
	}

	/**
	 * __get function
	 *
	 * Magic get function
	 * Shortcuts for common formatted versions of event data.
	 *
	 * @param string $name Property name
	 *
	 * @return mixed Property value
	 **/
	public function __get( $name ) {
		global $post, $more, $ai1ec_events_helper;

		switch( $name ) {

			case 'uid':
				return $this->post_id . '@' . bloginfo( 'url' );

			// ==================================
			// = Month view multiday properties =
			// ==================================
			case "multiday":
				return (
					$ai1ec_events_helper->get_long_date( $this->start )
					!=
					$ai1ec_events_helper->get_long_date( $this->end - 1 )
				);

			case "multiday_end_day":
				return $ai1ec_events_helper->get_multiday_end_day( $this->end - 1 );

			// ========================
			// = Get short-form dates =
			// ========================
			case 'short_start_time':
				return $ai1ec_events_helper->get_short_time( $this->start );

			case 'short_end_time':
				return $ai1ec_events_helper->get_short_time( $this->end );

			case 'short_start_date':
				return $ai1ec_events_helper->get_short_date( $this->start );

			case 'short_end_date':
				// Subtract 1 second so that all-day events' end date still
				// falls within the logical duration of days (since the end date
				// is always midnight of the following day)
				return $ai1ec_events_helper->get_short_date( $this->end - 1 );

			// =========================
			// = Get medium-form dates =
			// =========================
			case 'start_time':
				return $ai1ec_events_helper->get_medium_time( $this->start );

			case 'end_time':
				return $ai1ec_events_helper->get_medium_time( $this->end );

			// =======================
			// = Get long-form times =
			// =======================
			case 'long_start_time':
				return $ai1ec_events_helper->get_long_time( $this->start );

			case 'long_end_time':
				return $ai1ec_events_helper->get_long_time( $this->end );

			// =======================
			// = Get long-form dates =
			// =======================
			case 'long_start_date':
				return $ai1ec_events_helper->get_long_date( $this->start );

			case 'long_end_date':
				// Subtract 1 second so that all-day events' end date still
				// falls within the logical duration of days (since the end date
				// is always midnight of the following day)
				return $ai1ec_events_helper->get_long_date( $this->end - 1 );

			case 'timespan_html':
				$timespan = '';
				$long_start_date = str_replace( ' ', '&nbsp;', esc_html( $this->long_start_date ) );
				$long_end_date   = str_replace( ' ', '&nbsp;', esc_html( $this->long_end_date ) );

				if( $this->allday ) {
					$timespan .= $long_start_date;
					if( $long_end_date != $long_start_date ) {
						$timespan .= " – $long_end_date";
					}
					$timespan .= '<span class="ai1ec-allday-label">';
					$timespan .= __( ' (all-day)', AI1EC_PLUGIN_NAME );
					$timespan .= '</span>';
				}
				else {
					$long_start_time = str_replace( ' ', '&nbsp;', esc_html( $this->long_start_time ) );
					$long_end_time   = str_replace( ' ', '&nbsp;', esc_html( $this->long_end_time ) );
					$end_time        = str_replace( ' ', '&nbsp;', esc_html( $this->end_time ) );

					if( $long_end_date != $long_start_date ) {
						$timespan .= $long_start_time . ' – ' . $long_end_time;
					}
					elseif( $this->start != $this->end ) {
						$timespan .= $long_start_time . ' - ' . $end_time;
					}
					else {
						$timespan .= $long_start_time;
					}
				}
				return $timespan;

			// =====================================================
			// = Get the post's excerpt for display in popup view. =
			// =====================================================
			case 'post_excerpt':
				if( ! $this->post->post_excerpt ) {
					$content = strip_tags( strip_shortcodes( $this->post->post_content ) );
					$content = preg_replace( '/\s+/', ' ', $content );
					$words = explode( ' ', $content );
					if( count( $words ) > 25 )
						$this->post->post_excerpt = implode( ' ', array_slice( $words, 0, 25 ) ) . ' [...]';
					else
						$this->post->post_excerpt = $content;
				}
				return $this->post->post_excerpt;

			// ===============================================================
			// = Return any available location details separated by newlines =
			// ===============================================================
			case 'location':
				$location = '';
				if( $this->venue ) $location .= "$this->venue\n";
				if( $this->address ) {
					$bits = explode( ',', $this->address );
					$bits = array_map( 'trim', $bits );

					// If more than three comma-separated values, treat first value as
					// the street address, last value as the country, and everything
					// in the middle as the city, state, etc.
					if( count( $bits ) >= 3 ) {
						// Append the street address
						$street_address = array_shift( $bits ) . "\n";
						if( $street_address ) $location .= $street_address;
						// Save the country for the last line
						$country = array_pop( $bits );
						// Append the middle bit(s) (filtering out any zero-length strings)
						$bits = array_filter( $bits, 'strval' );
						if( $bits ) $location .= join( ',', $bits ) . "\n";
						if( $country ) $location .= $country . "\n";
					} else {
						// There are two or less comma-separated values, so just append
						// them each on their own line (filtering out any zero-length strings)
						$bits = array_filter( $bits, 'strval' );
						$location .= join( "\n", $bits );
					}
				}
				return $location;

			// ======================
			// = Categories as HTML =
			// ======================
			case 'categories_html':
				if( $this->categories_html === null ) {
					$categories = wp_get_post_terms( $this->post_id, 'events_categories' );
					foreach( $categories as &$category ) {
						$category =
							'<a class="ai1ec-category ai1ec-term-id-' . $category->term_id . '" ' .
							( $category->description ? 'title="' . esc_attr( $category->description ) . '" ' : '' ) .
							'href="' . get_term_link( $category ) . '">' .
							$ai1ec_events_helper->get_category_color_square( $category->term_id ) . ' ' . esc_html( $category->name ) . '</a>';
					}
					$this->categories_html = join( ' ', $categories );
				}
				return $this->categories_html;

			// ================
			// = Tags as HTML =
			// ================
			case 'tags_html':
				if( $this->tags_html === null ) {
					$tags = wp_get_post_terms( $this->post_id, 'events_tags' );
					foreach( $tags as &$tag ) {
						$tag =
							'<a class="ai1ec-tag ai1ec-term-id-' . $tag->term_id . '" ' .
							( $tag->description ? 'title="' . esc_attr( $tag->description ) . '" ' : '' ) .
							'href="' . get_term_link( $tag ) . '">' .
							esc_html( $tag->name ) . '</a>';
					}
					$this->tags_html = join( ' ', $tags );
				}
				return $this->tags_html;

			// ======================================
			// = Style attribute for event category =
			// ======================================
			case 'color_style':
			  if( $this->color_style === null ) {
			    $categories = wp_get_post_terms( $this->post_id, 'events_categories' );
			    if( $categories && ! empty( $categories ) ) {
			      $this->color_style = $ai1ec_events_helper->get_event_category_color_style( $categories[0]->term_id, $this->allday || $this->multiday );
			    }
		    }
			  return $this->color_style;

			// ======================================
			// = Style attribute for event bg color =
			// ======================================
			case 'category_bg_color':
			  if( $this->category_bg_color === null ) {
			    $categories = wp_get_post_terms( $this->post_id, 'events_categories' );
			    if( $categories && ! empty( $categories ) ) {
			      $this->category_bg_color = $ai1ec_events_helper->get_event_category_bg_color( $categories[0]->term_id, $this->allday || $this->multiday );
			    }
		    }
			  return $this->category_bg_color;

			// ======================================
			// = Style attribute for event bg color =
			// ======================================
			case 'category_text_color':
			  if( $this->category_text_color === null ) {
			    $categories = wp_get_post_terms( $this->post_id, 'events_categories' );
			    if( $categories && ! empty( $categories ) ) {
			      $this->category_text_color = $ai1ec_events_helper->get_event_category_text_color( $categories[0]->term_id, $this->allday || $this->multiday );
			    }
		    }
			  return $this->category_text_color;

      // =========================================
      // = Faded version of event category color =
      // =========================================
      case 'faded_color':
        if( $this->faded_color === null ) {
          $categories = wp_get_post_terms( $this->post_id, 'events_categories' );
          if( $categories && ! empty( $categories ) ) {
            $this->faded_color = $ai1ec_events_helper->get_event_category_faded_color( $categories[0]->term_id );
          }
        }
        return $this->faded_color;

      // ========================================
      // = RGBA version of faded category color =
      // ========================================
      case 'rgba_color':
        if( $this->rgba_color === null ) {
          $categories = wp_get_post_terms( $this->post_id, 'events_categories' );
          if( $categories && ! empty( $categories ) ) {
            $this->rgba_color = $ai1ec_events_helper->get_event_category_rgba_color( $categories[0]->term_id );
          }
        }
        return $this->rgba_color;

      // ===============================================
      // = HTML of category color boxes for this event =
      // ===============================================
      case 'category_colors':
        if( $this->category_colors === null ) {
          $categories = wp_get_post_terms( $this->post_id, 'events_categories' );
          $this->category_colors = $ai1ec_events_helper->get_event_category_colors( $categories );
		    }
			  return $this->category_colors;

			// ========================
			// = Contact info as HTML =
			// ========================
			case 'contact_html':
				$contact = '';
				if( $this->contact_name )
					$contact .= '<strong>' . esc_html( $this->contact_name ) . '</strong><br />';
				if( $this->contact_phone )
					$contact .= esc_html( $this->contact_phone ) . '<br />';
				if( $this->contact_email )
					$contact .= '<a href="mailto:' . esc_attr( $this->contact_email ) . '">' . esc_html( $this->contact_email ) . '</a>';
				return $contact;

			// ===========================
			// = Recurrence info as HTML =
			// ===========================
			case 'recurrence_html':
				if( ! $this->recurrence_rules || empty( $this->recurrence_rules ) )
					return null;

				return '<strong>' . esc_html( $ai1ec_events_helper->rrule_to_text( $this->recurrence_rules  ) ) . '</strong>';
			case 'exclude_html':
				if( ! $this->exception_dates || empty( $this->exception_dates ) )
					return null;

				return '<strong>' . esc_html( $ai1ec_events_helper->exdate_to_text( $this->exception_dates  ) ) . '</strong>';
		}
	}
	/**
	 * Returns the correct formatting to store the date on the db depending if its a timestamp or if its a datetime string like '2012-06-25'
	 *
	 * @param mixed string / numeric $date
	 */
	private function return_format_for_dates( $date ) {
		return is_numeric( $date ) ? 'FROM_UNIXTIME( %d )' : '%s';
	}

	/**
	 * save function
	 *
	 * Saves the current event data to the database. If $this->post_id exists,
	 * but $update is false, creates a new record in the ai1ec_events table of
	 * this event data, but does not try to create a new post. Else if $update
	 * is true, updates existing event record. If $this->post_id is empty,
	 * creates a new post AND record in the ai1ec_events table for this event.
	 *
	 * @param  bool  $update  Whether to update an existing event or create a
	 *                        new one
	 * @return int            The post_id of the new or existing event.
	 **/
	function save( $update = false )
 	{
		global $wpdb,
		       $ai1ec_events_helper;

		// ===========================
		// = Insert events meta data =
		// ===========================
		// Set facebook user and eid to 0 if they are not set, otherwise they will be set to '' since we use %s for big ints
		$facebook_eid  = isset( $this->facebook_eid ) ? $this->facebook_eid : 0;
		$facebook_user = isset( $this->facebook_user ) ? $this->facebook_user : 0;
		$columns = array(
			'post_id'          => $this->post_id,
			'start'            => $this->start,
			'end'              => $this->end,
			'allday'           => $this->allday,
			'recurrence_rules' => $this->recurrence_rules,
			'exception_rules'  => $this->exception_rules,
			'recurrence_dates' => $this->recurrence_dates,
			'exception_dates'  => $this->exception_dates,
			'venue'            => $this->venue,
			'country'          => $this->country,
			'address'          => $this->address,
			'city'             => $this->city,
			'province'         => $this->province,
			'postal_code'      => $this->postal_code,
			'show_map'         => $this->show_map,
			'contact_name'     => $this->contact_name,
			'contact_phone'    => $this->contact_phone,
			'contact_email'    => $this->contact_email,
			'cost'             => $this->cost,
			'ical_feed_url'    => $this->ical_feed_url,
			'ical_source_url'  => $this->ical_source_url,
			'ical_uid'         => $this->ical_uid,
			'show_coordinates' => $this->show_coordinates,
			'latitude'         => $this->latitude,
			'longitude'        => $this->longitude,
			'facebook_eid'     => $facebook_eid,
			'facebook_user'    => $facebook_user,
			'facebook_status'  => $this->facebook_status,
		);

		$format = array(
			'%d',
			'FROM_UNIXTIME( %d )',
			'FROM_UNIXTIME( %d )',
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%d',
			'%f',
			'%f',
			'%s',
			'%s',
			'%s',
		);

		$table_name = $wpdb->prefix . 'ai1ec_events';
		if( $this->post_id )
	 	{
			if( ! $update ) {
				// =========================
				// = Insert new event data =
				// =========================
				$wpdb->query( $wpdb->prepare(
					"INSERT INTO $table_name ( " .
					join( ', ', array_keys( $columns ) ) .
					" ) VALUES ( " .
					join( ', ', $format ) .
					" )",
					$columns ) );
			} else {
				// ==============================
				// = Update existing event data =
				// ==============================
				$where         = array( 'post_id' => $this->post_id );
				$where_escape  = array( '%d'                        );
				$wpdb->update( $table_name, $columns, $where, $format, $where_escape );
			}
		} else {
			// ===================
			// = Insert new post =
			// ===================
			$this->post_id = wp_insert_post( $this->post );
			$columns['post_id'] = $this->post_id;
			wp_set_post_terms( $this->post_id, $this->categories, 'events_categories' );
			wp_set_post_terms( $this->post_id, $this->tags, 'events_tags' );

			if( isset( $this->feed ) && isset( $this->feed->feed_id ) ) {
				$url_components = parse_url( $this->feed->feed_url );
				$feed_name = $url_components["host"];
				$term = term_exists( $feed_name, 'events_feeds' );
				if( ! $term ) {
					// term doesn't exist, create it
					$term = wp_insert_term(
						$feed_name,     // term
						'events_feeds', // taxonomy
						array(
							'description' => $this->feed->feed_url
						)
					);
				}
				// term_exists returns object, wp_insert_term returns array
				$term = (object)$term;
				if( isset( $term->term_id ) ) {
					// associate the event with the feed only if we have term id set
					$a = wp_set_object_terms( $this->post_id, (int)$term->term_id, 'events_feeds', false );
				}
			}

			// =========================
			// = Insert new event data =
			// =========================
			$wpdb->query( $wpdb->prepare(
				"INSERT INTO $table_name ( " .
				join( ', ', array_keys( $columns ) ) .
				" ) VALUES ( " .
				join( ', ', $format ) .
				" )",
				$columns ) );
		}

		return $this->post_id;
	}

	/**
	 * getProperty function
	 *
	 * Returns $property value
	 *
	 * @param string $property Property name
	 *
	 * @return mixed
	 **/
	function getProperty( $property ) {
		return $this->property;
	}

	/**
	 * isWholeDay function
	 *
	 * Determines if an event is a whole day event
	 *
	 * @return bool
	 **/
	function isWholeDay() {
		return ( bool ) $this->allday;
	}

	/**
	 * getStart function
	 *
	 * Returns the start time of the event
	 *
	 * @return int
	 **/
	function getStart() {
		return $this->start;
	}

	/**
	 * getEnd function
	 *
	 * Returns the end time of the event
	 *
	 * @return int
	 **/
	function getEnd() {
		return $this->end;
	}

	/**
	 * getFrequency function
	 *
	 * Returns the frequency of the event
	 *
	 * @return object
	 **/
	function getFrequency( $excluded = array() ) {
		return new SG_iCal_Freq( $this->recurrence_rules, $this->start, $excluded );
	}

	/**
	 * getDuration function
	 *
	 * Returns the duration of the event
	 *
	 * @return int
	 **/
	function getDuration() {
		return $this->end - $this->start;
	}
}
// END class
