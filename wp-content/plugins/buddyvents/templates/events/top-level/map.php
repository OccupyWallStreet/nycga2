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
<h3 class="pagetitle"><?php _e( 'Events Map', 'events' ) ?><?php if( ! bpe_is_restricted() ) : ?>&nbsp;<a class="button" href="<?php echo bp_get_root_domain() . '/' . bpe_get_base( 'root_slug' ) . '/'. bpe_get_option( 'create_slug' ) ?>"><?php _e( 'Create Event', 'events' ) ?></a><?php endif; ?></h3>

<div id="event-dir-search" class="dir-search no-ajax">
    <?php bpe_directory_events_search_form() ?>
</div>

<?php do_action( 'template_notices' ) ?>

<?php do_action( 'bpe_before_map_events_content' ) ?>

<?php bpe_load_template( 'events/includes/navigation' ); ?>

<?php do_action( 'bpe_before_map_events_list' ) ?>

<div id="events-dir-list" class="events dir-list">
    
<?php do_action( 'bpe_before_events_map' ) ?>

    <div id="events-overview-map"></div>
    <div id="eventsmap-controls">
        <?php bpe_events_map_controls() ?>
    </div>

<?php do_action( 'bpe_after_events_map' ) ?>

</div><!-- #events-dir-list -->

<?php do_action( 'bpe_map_events_content' ) ?>