<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */
 
// No direct access is allowed
if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Show the category map controls
 *
 * @package Core
 * @since 	1.1
 */
function bpe_events_map_controls( $group_id = false, $user_id = false )
{
	global $bpe, $shabuCounter;
	
	if( ! $shabuCounter )
		$shabuCounter = 1;
	
	$categories = bpe_get_event_categories();

	$lat  = ( ! bpe_get_option( 'map_location', 'lat' 	) ) ? 5 		: bpe_get_option( 'map_location', 'lat' );
	$lng  = ( ! bpe_get_option( 'map_location', 'lng' 	) ) ? 30 		: bpe_get_option( 'map_location', 'lng' );
	$zoom = ( ! bpe_get_option( 'map_zoom_level' 		) ) ? 2 		: bpe_get_option( 'map_zoom_level' );
	$type = ( ! bpe_get_option( 'map_type' 				) ) ? 'HYBRID' 	: bpe_get_option( 'map_type' );
	
	// get only events in the future
	$events = bpe_get_events( apply_filters( 'bpe_events_map_controls_args', array( 
		'user_id' 	=> $user_id, 
		'group_id' 	=> $group_id,
		'map' 		=> true,
		'per_page' 	=> 9999,
		'meta'		=> 'active',
		'meta_key'	=> 'status',
		'operator'	=> '='
	), $user_id, $group_id ) );

	?>
    <form id="event-map-cats" action="#">
        <input type="checkbox" id="check-all-markers" onclick="check_all<?php echo $shabuCounter ?>(this)" checked="checked"> <?php _e( 'Select All', 'events' ) ?>
		<?php foreach( $categories as $key => $val ) : ?>
            <input type="checkbox" class="marker-cats" onclick="boxclick<?php echo $shabuCounter ?>(this,'<?php echo $val->id ?><?php echo $shabuCounter ?>')" checked="checked"> <?php echo $val->name ?>
        <?php endforeach; ?>
    </form>

	<script type="text/javascript">
	var infowindow<?php echo $shabuCounter ?>; 
	var locations<?php echo $shabuCounter ?> = [<?php bpe_event_coordinates( $events ) ?>];
	var markers<?php echo $shabuCounter ?> = [];
	var map<?php echo $shabuCounter ?>;
	var markerCluster<?php echo $shabuCounter ?>;

	<?php foreach( $categories as $key => $val ) { ?>
	var cat<?php echo $val->id ?><?php echo $shabuCounter ?> = [];
	<?php } ?>
	
	function eventMapInitialize<?php echo $shabuCounter ?>() {
		var req = false;
		var mapOptions<?php echo $shabuCounter ?> = {
			zoom: <?php echo $zoom ?>,
			center: new google.maps.LatLng(<?php echo $lat ?>, <?php echo $lng ?>),
			mapTypeId: google.maps.MapTypeId.<?php echo $type ?>
		}
		map<?php echo $shabuCounter ?> = new google.maps.Map(document.getElementById("events-overview-map"), mapOptions<?php echo $shabuCounter ?>);
		infowindow<?php echo $shabuCounter ?> = new InfoBox(); 
		
		for (var i = 0; i < locations<?php echo $shabuCounter ?>.length; i++) {
			var loc<?php echo $shabuCounter ?> = locations<?php echo $shabuCounter ?>[i];
			var myLatLng<?php echo $shabuCounter ?> = new google.maps.LatLng(loc<?php echo $shabuCounter ?>[1], loc<?php echo $shabuCounter ?>[2]);

			var marker<?php echo $shabuCounter ?> = new google.maps.Marker({
				position: myLatLng<?php echo $shabuCounter ?>,
				map: map<?php echo $shabuCounter ?>,
				icon: new google.maps.MarkerImage(loc<?php echo $shabuCounter ?>[4], null, null, new google.maps.Point(0, <?php echo ( bpe_get_option( 'use_event_images' ) === true ) ? 50 : 40; ?>)),
				title: loc<?php echo $shabuCounter ?>[0]
			});

			google.maps.event.addListener(marker<?php echo $shabuCounter ?>, 'click', (function(m,t,i) {
				return function() {
					infowindow<?php echo $shabuCounter ?>.setTitle(t);
					infowindow<?php echo $shabuCounter ?>.setContent('<div class="infobox-inner"><img class="ajax-map" src="<?php echo EVENT_URLPATH ?>css/images/ajax-map.gif" width="66" height="66" alt="" /></div>');
					infowindow<?php echo $shabuCounter ?>.open(map<?php echo $shabuCounter ?>,m);
					if( req )
						req.abort();
					req = jQuery.post( ajaxurl, {
						action: 'bpe_get_google_map_content',
						cookie: encodeURIComponent(document.cookie),
						id: i
					},
					function(response) {
						response = jQuery.parseJSON(response);
						infowindow<?php echo $shabuCounter ?>.setUrl(response.url);
						infowindow<?php echo $shabuCounter ?>.setContent('<div class="infobox-inner">'+ response.content +'</div>');
						req = null;
					});
				};
			})(marker<?php echo $shabuCounter ?>,loc<?php echo $shabuCounter ?>[0],loc<?php echo $shabuCounter ?>[5]));

			markers<?php echo $shabuCounter ?>.push(marker<?php echo $shabuCounter ?>);

			<?php foreach( $categories as $key => $val ) { ?>
			if( loc<?php echo $shabuCounter ?>[3] == '<?php echo $val->id ?>' ) { cat<?php echo $val->id ?><?php echo $shabuCounter ?>.push(marker<?php echo $shabuCounter ?>); }
			<?php } ?>
		}
		i = null;
		
		markerCluster<?php echo $shabuCounter ?> = new MarkerClusterer(map<?php echo $shabuCounter ?>, markers<?php echo $shabuCounter ?>, { maxZoom: 12, gridSize: 50 });
	}

	function boxclick<?php echo $shabuCounter ?>(box,category) {
		if(box.checked) {
			markerShowCat<?php echo $shabuCounter ?>(category);
		} else {
			markerHideCat<?php echo $shabuCounter ?>(category);
		}
	}

	function check_all<?php echo $shabuCounter ?>(box) {
		if(box.checked) {
			markerShowAll<?php echo $shabuCounter ?>();
		} else {
			markerHideAll<?php echo $shabuCounter ?>();
		}
	}
	
	function markerShowAll<?php echo $shabuCounter ?>() {
		for( var i = 0; i < markers<?php echo $shabuCounter ?>.length; i++) {
			var m<?php echo $shabuCounter ?> = markers<?php echo $shabuCounter ?>[i];
			m<?php echo $shabuCounter ?>.setVisible(true);
		}
		jQuery('#event-map-cats input').each( function(index) {
			jQuery(this).attr( 'checked', 'checked' );
		});
		markerCluster<?php echo $shabuCounter ?>.addMarkers(markers<?php echo $shabuCounter ?>,false);
	}

	function markerHideAll<?php echo $shabuCounter ?>() {
		for( var i = 0; i < markers<?php echo $shabuCounter ?>.length; i++) {
			var m<?php echo $shabuCounter ?> = markers<?php echo $shabuCounter ?>[i];
			m<?php echo $shabuCounter ?>.setVisible(false);
			markerCluster<?php echo $shabuCounter ?>.removeMarker(m<?php echo $shabuCounter ?>);
		}
		markerCluster<?php echo $shabuCounter ?>.redraw();
		jQuery('#event-map-cats input').each( function(index) {
			jQuery(this).attr( 'checked', '' );
		});
	}

	function markerShowCat<?php echo $shabuCounter ?>(category) {
		<?php foreach( $categories as $key => $val ) { ?>
		if( category == '<?php echo $val->id ?><?php echo $shabuCounter ?>' ) { var cat<?php echo $shabuCounter ?> = cat<?php echo $val->id ?><?php echo $shabuCounter ?>; }
		<?php } ?>
		for( var i = 0; i < cat<?php echo $shabuCounter ?>.length; i++) {
			var mark<?php echo $shabuCounter ?> = cat<?php echo $shabuCounter ?>[i];
			mark<?php echo $shabuCounter ?>.setVisible(true);
		}
		markerCluster<?php echo $shabuCounter ?>.addMarkers(cat<?php echo $shabuCounter ?>,false);
		
		var c = jQuery("input.marker-cats:checked").length;
		var t = jQuery("input.marker-cats").length;
		
		if( c == t ) {
			jQuery('#check-all-markers').attr( 'checked', 'checked' );
		}
	}

	function markerHideCat<?php echo $shabuCounter ?>(category) {
		<?php foreach( $categories as $key => $val ) { ?>
		if( category == '<?php echo $val->id ?><?php echo $shabuCounter ?>' ) { var cat<?php echo $shabuCounter ?> = cat<?php echo $val->id ?><?php echo $shabuCounter ?>; }
		<?php } ?>

		for( var i = 0; i < cat<?php echo $shabuCounter ?>.length; i++) {
			var mark<?php echo $shabuCounter ?> = cat<?php echo $shabuCounter ?>[i];
			mark<?php echo $shabuCounter ?>.setVisible(false);
			markerCluster<?php echo $shabuCounter ?>.removeMarker(mark<?php echo $shabuCounter ?>);
		}
		jQuery('#check-all-markers').attr( 'checked', '' );
		markerCluster<?php echo $shabuCounter ?>.redraw();
	}

	jQuery(document).ready( function() {
		eventMapInitialize<?php echo $shabuCounter ?>();
	});
	</script>
    <?php
	
	$shabuCounter++;
}

/**
 * Display a search results page
 *
 * @package Core
 * @since 	1.6
 */
function bpe_add_search_results_map()
{
	global $wpdb, $bpe, $shabuCounter;

	$events = bpe_get_events( apply_filters( 'bpe_add_search_results_map_args', array( 
		'location' 		=> $_REQUEST['l'], 
		'radius' 		=> $_REQUEST['r'], 
		'search_terms' 	=> $_REQUEST['s'], 
		'per_page' 		=> 9999, 
		'future' 		=> false, 
		'past' 			=> false, 
		'map' 			=> true,
		'meta'			=> 'active',
		'meta_key'		=> 'status',
		'operator'		=> '='
	) ) );

	if( $events['total'] > 0 ) :
	
		if( ! $shabuCounter )
			$shabuCounter = 1;

		?>
		<div id="search-results-map<?php echo $shabuCounter ?>" class="event-map"></div>
		<script type="text/javascript">
		var locations<?php echo $shabuCounter ?> = [<?php bpe_event_coordinates( $events ) ?>];
	
		function searchInitialize<?php echo $shabuCounter ?>() {
			var searchOptions<?php echo $shabuCounter ?> = {
				zoom: 10,
				center: new google.maps.LatLng(5, 30),
				mapTypeId: google.maps.MapTypeId.<?php echo bpe_get_option( 'map_type' ) ?>
			}
			var map<?php echo $shabuCounter ?> = new google.maps.Map(document.getElementById("search-results-map<?php echo $shabuCounter ?>"), searchOptions<?php echo $shabuCounter ?>);
			setMarkers<?php echo $shabuCounter ?>(map<?php echo $shabuCounter ?>, locations<?php echo $shabuCounter ?>);
		}
	
		function setMarkers<?php echo $shabuCounter ?>(map<?php echo $shabuCounter ?>, loc<?php echo $shabuCounter ?>) {
			var req = false;
			var bounds<?php echo $shabuCounter ?> = new google.maps.LatLngBounds();
			for (var i = 0; i < loc<?php echo $shabuCounter ?>.length; i++) {
				var location<?php echo $shabuCounter ?> = loc<?php echo $shabuCounter ?>[i];
				var infowindow<?php echo $shabuCounter ?> = new InfoBox();
				var searchLatLng<?php echo $shabuCounter ?> = new google.maps.LatLng(location<?php echo $shabuCounter ?>[1], location<?php echo $shabuCounter ?>[2]);
				var marker<?php echo $shabuCounter ?> = new google.maps.Marker({
					position: searchLatLng<?php echo $shabuCounter ?>,
					map: map<?php echo $shabuCounter ?>,
					icon: new google.maps.MarkerImage(location<?php echo $shabuCounter ?>[4], null, null, new google.maps.Point(0, <?php echo ( bpe_get_option( 'use_event_images' ) === true ) ? 50 : 40; ?>)),
					title: location<?php echo $shabuCounter ?>[0]
				});
				
				google.maps.event.addListener(marker<?php echo $shabuCounter ?>, 'click', (function(m,t,i) {
					return function() {
						infowindow<?php echo $shabuCounter ?>.setTitle(t);
						infowindow<?php echo $shabuCounter ?>.setContent('<div class="infobox-inner"><img class="ajax-map" src="<?php echo EVENT_URLPATH ?>css/images/ajax-map.gif" width="66" height="66" alt="" /></div>');
						infowindow<?php echo $shabuCounter ?>.open(map<?php echo $shabuCounter ?>,m);
						if( req )
							req.abort();
						req = jQuery.post( ajaxurl, {
							action: 'bpe_get_google_map_content',
							cookie: encodeURIComponent(document.cookie),
							id: i
						},
						function(response) {
							response = jQuery.parseJSON(response);
							infowindow<?php echo $shabuCounter ?>.setUrl(response.url);
							infowindow<?php echo $shabuCounter ?>.setContent('<div class="infobox-inner">'+ response.content +'</div>');
							req = null;
						});
					};
				})(marker<?php echo $shabuCounter ?>,location<?php echo $shabuCounter ?>[0],location<?php echo $shabuCounter ?>[5]));
	
				bounds<?php echo $shabuCounter ?>.extend(searchLatLng<?php echo $shabuCounter ?>);
				map<?php echo $shabuCounter ?>.fitBounds(bounds<?php echo $shabuCounter ?>);
			}
		}
	
		jQuery(document).ready( function() {
			searchInitialize<?php echo $shabuCounter ?>();
		});
		</script>
		<?php
		$shabuCounter++;
	endif;
}
add_action( 'bpe_before_search_results_events_list', 'bpe_add_search_results_map' );

/**
 * Echo event locations in json format
 *
 * @package Core
 * @since 	1.0
 */
function bpe_event_coordinates( $events )
{
	global $bpe;

	$locations = array();
	foreach( (array)$events['events'] as $key => $event ) :
		$image = ( bpe_get_option( 'use_event_images' ) === true ) ? bpe_get_event_image( array( 'event' => $event, 'type' => 'thumb', 'width' => 50, 'height' => 50, 'html' => false ) ) : EVENT_URLPATH .'css/images/event.png';
		
		$locations[] = "['". esc_js( bpe_get_event_name( $event ) ) ."', ". bpe_get_event_latitude( $event ) .", ". bpe_get_event_longitude( $event ) .", '". bpe_get_event_category_id( $event ) ."', '". $image ."', ". bpe_get_event_id( $event ) ."]";
	endforeach;
	
	echo implode( ',', (array)$locations );
}

/**
 * Arrange coordinates in a circle
 *
 * @package Core
 * @since 	1.6
 */
function bpe_next_coords( $lat, $lng, $radius, $angle )
{
	$new_lat = $lat + ( $radius * cos( $angle * pi() / 180 ) );	
	$new_lng = $lng + ( $radius * sin( $angle * pi() / 180 ) );
	
	return array( 'lat' => $new_lat, 'lng' => $new_lng );
}

/**
 * Get event timezone
 *
 * @package Core
 * @since 	1.7
 */
function bpe_get_timezone( $lat = false, $lng = false )
{
	if( defined( 'WP_LOCAL_DEV' ) && WP_LOCAL_DEV )
		return false;

	if( empty( $lat ) || empty( $lng ) )
		return false;
		
	if( ! bpe_get_option( 'geonames_username' ) )
		return false;
	
	// get the timezone data
	$json = wp_remote_get( 'http://api.geonames.org/timezoneJSON?lat='. urlencode( $lat ) .'&lng='. urlencode( $lng ) .'&username='. urlencode( bpe_get_option( 'geonames_username' ) ) );

	$data = json_decode( wp_remote_retrieve_body( $json ) );
	
	return bpe_format_utc_offset( $data->rawOffset, $data->timezoneId );
}

/**
 * Format utc offset
 *
 * @package Core
 * @since 	1.7
 */
function bpe_format_utc_offset( $offset, $timezone )
{
	if( empty( $timezone ) )
		return false;
		
	if( empty( $offset ) )
		return 'UTC '. $timezone;
	
	$offset = explode( '.', $offset );
	
	$hour = ( ( $prefix = substr( $offset[0], 0, 1 ) ) == '-' ) ? substr( $offset[0], 1 ) : $offset[0];
	$prefix = ( $prefix == '-' ) ? $prefix : '+';
	
	if( strlen( $hour ) == 1 )
		$hour = '0'. $hour;
		
	$hour = $prefix . $hour;
	
	if( isset( $offset[1] ) ) :	
		$minute = 60 * ( $offset[1] / 100 );
		
		if( strlen( $minute ) == 1 ) :
			$minute = $minute .'0';
		endif;
	else :
		$minute = '00';
	endif;
	
	$tz = $hour .':'. $minute .' '. $timezone;
	
	return ( ! empty( $tz ) ) ? 'UTC '. $tz : false;
}
?>