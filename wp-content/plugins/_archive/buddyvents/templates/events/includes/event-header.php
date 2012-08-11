<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */
 
do_action( 'bpe_before_event_header' ) ?>

<div class="event-meta">
    <dl class="column">
        <?php printf( __( '<dt>Venue:</dt><dd><span class="location">%s</span></dd>', 'events' ), bpe_get_event_location_link() ) ?>
        <?php if( bpe_is_all_day_event() ) : ?>
			<?php printf( __( '<dt>Start:</dt><dd><span class="dtstart">%s</span> (all day event)</dd>', 'events' ), bpe_get_event_start_date() ) ?>
            <?php if( bpe_get_event_start_date() != bpe_get_event_end_date() ) : ?>
				<?php printf( __( '<dt>End:</dt><dd><span class="dtend">%s</span> (all day event)</dd>', 'events' ), bpe_get_event_end_date() ) ?>
            <?php endif; ?>
        <?php else : ?>
			<?php printf( __( '<dt>Start:</dt><dd><span class="dtstart">%s</span> at %s</dd>', 'events' ), bpe_get_event_start_date(), bpe_get_event_start_time() ) ?>
            <?php printf( __( '<dt>End:</dt><dd><span class="dtend">%s</span> at %s</dd>', 'events' ), bpe_get_event_end_date(), bpe_get_event_end_time() ) ?>
        <?php endif; ?>
        <?php if( bpe_has_event_timezone() ) :	printf( __( '<dt>Timezone:</dt><dd><span class="timezone">%s</span></dd>', 'events' ), bpe_get_event_timezone() );	endif; ?>
        <?php do_action( 'bpe_inside_event_header_left' ) ?>
    </dl>

    <dl class="column">
        <?php printf( __( '<dt>Category:</dt><dd><span class="category">%s</span></dd>', 'events' ), bpe_get_event_category() ) ?>
        <?php if( bpe_has_url() ) :	printf( __( '<dt>Website:</dt><dd><span class="url">%s</span></dd>', 'events' ), bpe_get_event_url() );	endif; ?>
		<?php if( bpe_attached_to_group() ) : ?>
            <?php printf( __( '<dt>Group:</dt><dd><a href="%s">%s</a></dd>', 'events' ), bpe_event_get_group_permalink(), bpe_event_get_group_name() ) ?>
            
            <?php if( bpe_is_address_enabled() ) : ?>
                <?php printf( __( '<dt>Address:</dt><dd>%s<br />%s<br />%s<br />%s</dd>', 'events' ), bpe_event_get_group_address_street(), bpe_event_get_group_address_city(), bpe_event_get_group_address_postcode(), bpe_event_get_group_address_country() ) ?>
            <?php endif; ?>
        <?php endif; ?>
        <?php do_action( 'bpe_inside_event_header_right' ) ?>
    </dl>
    
    <?php do_action( 'bpe_inside_event_header' ) ?>
</div>

<?php do_action( 'bpe_after_event_header' ) ?>