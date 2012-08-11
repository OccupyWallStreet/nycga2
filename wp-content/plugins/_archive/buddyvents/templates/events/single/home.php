<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */

if( bpe_has_events() ) :  while ( bpe_events() ) : bpe_the_event(); ?>

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
	
        <li id="event-<?php bpe_event_id() ?>" class="vevent <?php bpe_event_status_class() ?>">
        
			<?php bpe_load_template( 'events/includes/single-nav' ); ?> 
            
            <div class="item">
                <div class="item-title"><span class="summary"><?php bpe_event_name() ?></span></div>
				
				<?php bpe_load_template( 'events/includes/event-header' ); ?>                
                
                <div class="item-desc description"><?php bpe_event_description() ?></div>
                
            </div>
            
            <div class="action">
                <span class="activity"><?php bpe_event_attendees() ?></span>
        		<span class="event-admin organizer"><?php _e( 'Creator:', 'events' ) ?><br /><?php bpe_event_user_avatar() ?></span>
            </div>
            
			<?php if( bpe_has_event_location() && ! bpe_get_displayed_event_meta( 'no_coords' ) ) : ?>
			<script type="text/javascript">
            function eventmap_initialize() {
                var dcoords = new google.maps.LatLng(<?php bpe_event_latitude() ?>, <?php bpe_event_longitude() ?>);
                var dmapOptions = {
                    zoom: 14,
                    center: dcoords,
                    navigationControl: true,
                    mapTypeControl: false,
                    scaleControl: false,
                    mapTypeId: google.maps.MapTypeId.<?php echo bpe_get_option( 'map_type' ) ?>
                };
                var dmap = new google.maps.Map(document.getElementById("eventmap"), dmapOptions);
            
                var marker = new google.maps.Marker({
                    position: dcoords
                });
              
                marker.setMap(dmap);
            }
            
            jQuery(document).ready( function() {
                jQuery( '.item .item-desc' ).after( '<div class="eventmap-wrapper"><div id="eventmap"></div></div>' );
                eventmap_initialize();
            });
            </script>
            <?php endif; ?>
            <?php do_action( 'bpe_end_single_event_action', bpe_get_event_id(), bpe_get_event_user_id() ) ?>
        </li>
	</ul>

<?php endwhile; ?>
<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'No events were found.', 'events' ) ?></p>
	</div>

<?php endif; ?>