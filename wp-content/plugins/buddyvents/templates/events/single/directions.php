<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */

if( bpe_has_events() ) :  while ( bpe_events() ) : bpe_the_event();

	$coords = new MAPO_Coords( null, bp_loggedin_user_id() );
	?>

	<?php if( bpe_has_single_nav() ) : ?>
	
		<div class="single-nav">
			<div class="previous-event">
				<?php bpe_previous_event_link() ?>
			</div>
	
			<div class="next-event">
				<?php bpe_next_event_link() ?>
			</div>
		</div>
	
	<?php endif; ?>	

	<ul id="events-list" class="item-list single-event">
	
		<li id="event-<?php bpe_event_id() ?>" class="<?php bpe_event_status_class() ?>">

			<?php bpe_load_template( 'events/includes/single-nav' ); ?> 
			
			<div class="item">
				<div class="item-title"><?php printf( __( 'Directions to %s', 'events' ), bpe_get_event_name() ) ?></div>
				
				<?php bpe_load_template( 'events/includes/event-header' ); ?>                
                
                <?php if( bpe_has_event_location() ) : ?>
				
                <form class="standard-form">
                    <label for="dir-mode"><?php _e( 'Mode of transport', 'events' ) ?></label>
                    <select id="dir-mode">
                        <option value="DRIVING"><?php _e( 'Driving', 'events' ) ?></option>
                        <option value="WALKING"><?php _e( 'Walking', 'events' ) ?></option>
                        <option value="BICYCLING"><?php _e( 'Bicycling', 'events' ) ?></option>
                    </select>
                    
                    <label for="dir-start"><?php _e( 'Start', 'events' ) ?></label>
                    <input type="text" id="dir-start" name="start" value="" ><br />
                    <small><?php _e( 'Leave empty to use your own location. If nothing shows up, then there is no route available for your location.', 'events' ) ?></small>
                    
                    <div class="dir-submit">
                    	<a class="button" id="get-dir" href="#"><?php _e( 'Get directions', 'events' ) ?></a>
                    </div>
                </form>
                			  
                <div id="directions-map"></div>
                <div id="directions-panel"></div>

				<script type="text/javascript">
                var directionDisplay;
                var directionsService = new google.maps.DirectionsService();
                var map;
                
                function initialize() {
                    directionsDisplay = new google.maps.DirectionsRenderer();
                    var dirOptions = {
                        zoom:7,
                        mapTypeId: google.maps.MapTypeId.ROADMAP,
                        center: new google.maps.LatLng(<?php bpe_event_latitude() ?>, <?php bpe_event_longitude() ?>)
                    }
                    map = new google.maps.Map(document.getElementById("directions-map"), dirOptions);
                    directionsDisplay.setMap(map);
                    directionsDisplay.setPanel(document.getElementById("directions-panel"));
                }
                  
                function calcRoute() {
                    var selectedMode = document.getElementById("dir-mode").value;
                    var start = document.getElementById("dir-start").value;
                    
                    if( start === "" ) {
                        start = new google.maps.LatLng(<?php bpe_check_value( $coords->lat, 5 ) ?>, <?php bpe_check_value(  $coords->lng, 30 ) ?>);
                    }
                    
                    var request = {
                        origin:start, 
                        destination: new google.maps.LatLng(<?php bpe_event_latitude() ?>, <?php bpe_event_longitude() ?>),
                        travelMode: google.maps.DirectionsTravelMode[selectedMode],
                        unitSystem: google.maps.DirectionsUnitSystem.<?php echo ( bpe_get_option( 'system' ) == 'km' ) ? 'METRIC' : 'IMPERIAL'; ?>
                    };
                    directionsService.route(request, function(result, status) {
                        if (status == google.maps.DirectionsStatus.OK) {
                            directionsDisplay.setDirections(result);
                        }
                    });
                }
                
                jQuery(document).ready(function() {
                    initialize();
					calcRoute();
                    
                    jQuery('#get-dir').click( function(){
                        calcRoute();
                        return false;
                    });
                });
                </script>
                
                <?php else : ?>
                
                    <div id="message" class="info">
                        <p><?php _e( 'This event does not have a location.', 'events' ) ?></p>
                    </div>
                 
                <?php endif; ?>
		   </div>
			
			<div class="action">
				<span class="activity"><?php bpe_event_attendees() ?></span>
        		<span class="event-admin"><?php _e( 'Creator:', 'events' ) ?><br /><?php bpe_event_user_avatar() ?></span>
			</div>
		</li>
	</ul>

<?php endwhile; ?>
<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'No events were found.', 'events' ) ?></p>
	</div>

<?php endif; ?>