<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */
?>
<li id="event-<?php bpe_event_id() ?>">
    
    <div class="event-item">
    
    	<?php if( bpe_are_logos_enabled() ) : ?>
        <div class="widget-item-avatar">
            <a title="<?php bpe_event_attendees() ?>" href="<?php bpe_event_link() ?>"><?php bpe_event_image_thumb() ?></a>
        </div>
        <?php endif; ?>
                                
        <div class="item-title"><a title="<?php bpe_event_attendees() ?>" href="<?php bpe_event_link() ?>"><?php bpe_event_name() ?></a></div>
        
        <div class="widget-event-meta">
            <dl class="column">
                <?php printf( __( '<dt>Venue:</dt><dd>%s</dd>', 'events' ), bpe_get_event_location_link() ) ?>
                <?php if( bpe_is_all_day_event() ) : ?>
                    <?php printf( __( '<dt>Start:</dt><dd><span class="dtstart">%s</span> (all day event)</dd>', 'events' ), bpe_get_event_start_date() ) ?>
                    <?php if( bpe_get_event_start_date() != bpe_get_event_end_date() ) : ?>
                        <?php printf( __( '<dt>End:</dt><dd><span class="dtend">%s</span> (all day event)</dd>', 'events' ), bpe_get_event_end_date() ) ?>
                    <?php endif; ?>
                <?php else : ?>
                    <?php printf( __( '<dt>Start:</dt><dd><span class="dtstart">%s</span> at %s</dd>', 'events' ), bpe_get_event_start_date(), bpe_get_event_start_time() ) ?>
                    <?php printf( __( '<dt>End:</dt><dd><span class="dtend">%s</span> at %s</dd>', 'events' ), bpe_get_event_end_date(), bpe_get_event_end_time() ) ?>
                <?php endif; ?>
                <?php printf( __( '<dt>Category:</dt><dd>%s</dd>', 'events' ), bpe_get_event_category() ) ?>
            </dl>
        </div>
        
        <div class="item-desc"><?php bpe_event_description_excerpt( false, false ) ?></div>
    </div>
    <hr />
    <div class="clear"></div>
</li>