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
<h3 class="pagetitle"><?php _e( 'Events Calendar', 'events' ) ?><?php if( ! bpe_is_restricted() ) : ?>&nbsp;<a class="button" href="<?php echo bp_get_root_domain() . '/' . bpe_get_base( 'root_slug' ) . '/'. bpe_get_option( 'create_slug' ) ?>"><?php _e( 'Create Event', 'events' ) ?></a><?php endif; ?></h3>

<div id="event-dir-search" class="dir-search no-ajax">
    <?php bpe_directory_events_search_form() ?>
</div>

<?php do_action( 'template_notices' ) ?>

<?php bpe_load_template( 'events/includes/navigation' ); ?>

<?php bpe_calendar() ?>

<?php if( ! bpe_use_fullcalendar() ) : ?>
<script type="text/javascript">
jQuery(document).ready( function() {
	jQuery('form#cal-controls').submit(function() {
		var calYear = jQuery('#cal-controls #year').val();
		var calMonth = jQuery('#cal-controls #month').val();
		window.location.href = '<?php echo bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) ?>/calendar/'+ calMonth +'/'+ calYear +'/';
		return false;
	});
});
</script>
<?php endif; ?>