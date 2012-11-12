<?php
//
//  class-ai1ec-importer-controller.php
//  all-in-one-event-calendar
//
//  Created by The Seed Studio on 2011-07-13.
//

/**
 * Ai1ec_Importer_Controller class
 *
 * @package Controllers
 * @author time.ly
 **/
class Ai1ec_Importer_Controller {
	/**
	 * _instance class variable
	 *
	 * Class instance
	 *
	 * @var null | object
	 **/
	private static $_instance = NULL;

	/**
	 * get_instance function
	 *
	 * Return singleton instance
	 *
	 * @return object
	 **/
	static function get_instance() {
		if( self::$_instance === NULL ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Constructor
	 *
	 * Default constructor
	 **/
	private function __construct() { }

	/**
	 * register_importer function
	 *
	 * Registers the event calendar importer
	 *
	 * @return void
	 **/
	function register_importer() {
		global $wp_importers;

		if( ! isset( $wp_importers['ai1ec_the_events_calendar'] ) ) {
			$wp_importers['ai1ec_the_events_calendar'] = array(
				__( 'The Events Calendar → All-in-One Event Calendar', AI1EC_PLUGIN_NAME ),
				__( 'Imports events created using The Events Calendar plugin into the All-in-One Event Calendar', AI1EC_PLUGIN_NAME ),
				array( &$this, 'import_the_events_calendar' )
			);
		}
	}

	/**
	 * import_the_events_calendar function
	 *
	 * Import events from The Events Calendar into Ai1ec.
	 *
	 * @return void
	 **/
	function import_the_events_calendar() {
		global $ai1ec_view_helper,
					 $ai1ec_events_helper;

		$args = array(
			'post_type' 	=> 'post',
			'numberposts'	=> -1,
			'meta_key'		=> '_isEvent',
			'meta_value'	=> 'yes'
		);
		$posts = get_posts( $args );

		$imported_events = 0;
		foreach( $posts as $post )
		{
			$event = new Ai1ec_Event( null );
			$postmeta = get_post_custom( $post->ID );

			// Need this to offset dates coming from The Events Calendar
			$gm_diff = mktime( 0 ) - gmmktime( 0 );

			$event->allday 				= $postmeta['_EventAllDay'][0] == 'yes' || $postmeta['_EventAllDay'][0] == 1;
			$event->start 				= strtotime( $postmeta['_EventStartDate'][0] ) - $gm_diff;
			$event->end 					= strtotime( $postmeta['_EventEndDate'][0] ) - $gm_diff;
			// If all-day event, align start/end to start/end of day
			if( $event->allday ) {
				$event->start = $ai1ec_events_helper->gmgetdate( $event->start );
				$event->start = gmmktime( 0, 0, 0, $event->start['mon'], $event->start['mday'], $event->start['year'] );
				$event->end   = $ai1ec_events_helper->gmgetdate( $event->end );
				$event->end   = gmmktime( 0, 0, 0, $event->end['mon'], $event->end['mday'], $event->end['year'] );
			}
			// Finally, convert to GMT storage format
			$event->start         = $ai1ec_events_helper->local_to_gmt( $event->start );
			$event->end           = $ai1ec_events_helper->local_to_gmt( $event->end );
			// Bug in The Events Calendar where some all-day events start and end at the same time
			if( $event->allday && $event->end - $event->start < ( 24 * 60 * 60 ) )
				$event->end = $event->start + 24 * 60 * 60;
			$event->venue 				= $postmeta['_EventVenue'][0];
			$event->country 			= $postmeta['_EventCountry'][0];
			$event->city 					= $postmeta['_EventCity'][0];
			$event->province 			= $postmeta['_EventState'][0];
			$event->postal_code 	= $postmeta['_EventZip'][0];
			$event->address = array();
			if( $postmeta['$_EventAddress'] ) $event->address[] = $postmeta['$_EventAddress'];
			if( $event->city )                $event->address[] = $event->city;
			if( $event->province )            $event->address[] = $event->province;
			if( $event->postal_code )         $event->address[] = $event->postal_code;
			if( $event->country )             $event->address[] = $event->country;
			$event->address = join( ', ', $event->address );
			$event->show_map 			= $postmeta['_EventShowMapLink'][0] == 'true' || $postmeta['_EventShowMap'][0] == 'true';
			$event->cost 					= $postmeta['_EventCost'][0];
			$event->contact_phone = $postmeta['_EventPhone'][0];
			$event->post 					= get_object_vars( $post );
			$event->post["post_type"] = AI1EC_POST_TYPE;
			unset( $event->post["ID"] );

			// Transfer post categories => event categories, post tags => event tags
			$terms = wp_get_post_terms( $post->ID, array( 'category', 'post_tag' ) );
			$event->categories = array();
			$event->tags = array();
			foreach( $terms as $term )
			{
				switch( $term->taxonomy )
				{
					case 'category':
						// Ignore special "Events" category by The Events Calendar
						if( $term->name == 'Events' )
							break;
						// Need to find out the category ID, if it exists.
						$event_term = get_term_by( 'name', $term->name, 'events_categories' );
						// If no category exists, create it.
						if( $event_term === false )
							$event_term = (object) wp_insert_term(
								$term->name,
								'events_categories',
								array(
									'description' => $term->description,
									'slug' => $term->slug
								)
							);
						$event->categories[] = $event_term->term_id;
						break;

					case 'post_tag':
						// For some reason tag-like taxonomies are treated differently; term
						// IDs cannot be used; instead the actual term name must be appended
						$event->tags[] = $term->name;
						break;
				}
			}

			$post_id = $event->save();
			$ai1ec_events_helper->cache_event( $event, $post_id );

			$imported_events++;
		}

		$ai1ec_view_helper->display_admin( "import.php", array( 'imported_events' => $imported_events ) );
	}
}
// END class
