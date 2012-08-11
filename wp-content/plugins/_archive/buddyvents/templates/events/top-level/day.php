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
<h3 class="pagetitle">
	<?php printf( __( 'Day: %s', 'events' ), mysql2date( bpe_get_option( 'date_format' ), bp_action_variable( 0 ), true ) ) ?><?php if( ! bpe_is_restricted() ) : ?>&nbsp;<a class="button" href="<?php echo bp_get_root_domain() . '/' . bpe_get_base( 'root_slug' ) . '/'. bpe_get_option( 'create_slug' ) ?>"><?php _e( 'Create Event', 'events' ) ?></a><?php endif; ?>
</h3>

<div id="event-dir-search" class="dir-search no-ajax">
    <?php bpe_directory_events_search_form() ?>
</div>

<?php do_action( 'template_notices' ) ?>

<?php do_action( 'bpe_before_directory_events_content' ) ?>

<?php bpe_load_template( 'events/includes/navigation' ); ?>

<?php do_action( 'bpe_before_directory_events_list' ) ?>

<div id="events-dir-list" class="events dir-list">
    
<?php do_action( 'bpe_before_events_loop' ); ?>

<?php if( bpe_has_events() ) : ?>

	<div id="pag-top" class="pagination no-ajax">

		<div class="pag-count" id="events-count">
			<?php bpe_events_pagination_count() ?>
		</div>

		<div class="pagination-links" id="events-pag">
			<?php bpe_events_pagination_links() ?>
		</div>

	</div>

    <?php do_action( 'bpe_day_before_loop' ) ?>

	<ul id="events-list" class="item-list<?php if( bpe_grid_style() ) : ?> item-list-grid<?php endif; ?>">
	
	<?php while ( bpe_events() ) : bpe_the_event();	?>

		<?php if( bpe_grid_style() ) : ?>
        
            <?php bpe_load_template( 'events/view/grid' ); ?>                
        
        <?php else : ?>
    
            <?php bpe_load_template( 'events/view/list' ); ?>                
       
        <?php endif; ?>

	<?php endwhile; ?>
    
    <?php do_action( 'bpe_day_inside_loop' ) ?>
	
	</ul>

    <?php do_action( 'bpe_day_after_loop' ) ?>

	<div id="pag-bottom" class="pagination no-ajax">

		<div class="pag-count" id="events-count">
			<?php bpe_events_pagination_count() ?>
		</div>

		<div class="pagination-links" id="events-pag">
			<?php bpe_events_pagination_links() ?>
		</div>

	</div>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'No events were found.', 'events' ) ?></p>
	</div>

<?php endif; ?>
    
<?php do_action( 'bpe_after_events_loop' ) ?>

</div><!-- #events-dir-list -->

<?php do_action( 'bpe_directory_events_content' ) ?>