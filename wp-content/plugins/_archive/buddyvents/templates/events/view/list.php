<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */

do_action( 'bpe_before_list_view' ) ?>

<li id="event-<?php bpe_event_id() ?>" class="vevent <?php bpe_event_status_class() ?>">

    <div id="event-actions">
    	<?php if( bpe_are_logos_enabled() ) : ?>           
        <div class="item-avatar">
            <a href="<?php bpe_event_link() ?>"><?php bpe_event_image() ?></a>
        </div>
        <?php endif; ?>

        <?php bpe_reset_counter() ?>
        <?php bpe_attendance_button() ?>
        <?php bpe_event_leftover_spots() ?>
        <?php bpe_event_attending_status() ?>
    </div>
    
    <div class="item<?php bpe_item_class() ?>">
        <div class="item-title"><a class="url" href="<?php bpe_event_link() ?>"><span class="summary"><?php bpe_event_name() ?></span></a></div>
        
        <?php bpe_load_template( 'events/includes/event-header' ); ?>                
        
        <div class="item-desc description"><?php bpe_event_description_excerpt() ?><?php bpe_display_distance_from_user() ?></div>

    	<?php do_action( 'bpe_after_description_list_view' ) ?>
    </div>
    
    <?php do_action( 'bpe_inside_list_view' ) ?>
    
    <div class="action">
        <span class="activity"><?php bpe_event_attendees() ?></span>
        <span class="event-admin organizer"><?php _e( 'Creator:', 'events' ) ?><br /><?php bpe_event_user_avatar() ?></span>
    </div>
</li>

<?php do_action( 'bpe_after_list_view' ) ?>