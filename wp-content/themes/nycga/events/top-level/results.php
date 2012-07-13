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
	<?php printf( __( 'Search for <i>%s</i>', 'events' ), bpe_get_search_query() ) ?>
</h3>

<div id="event-dir-search" class="dir-search no-ajax">
    <?php bpe_directory_events_search_form() ?>
</div>

<?php do_action( 'template_notices' ) ?>

<?php do_action( 'bpe_before_search_results_events_content' ) ?>

<?php bpe_load_template( 'events/includes/navigation' ); ?>

<?php do_action( 'bpe_before_search_results_events_list' ) ?>

<div id="events-dir-list" class="events dir-list">
    
<?php do_action( 'bpe_before_search_results_events_loop' ); ?>

<?php if( bpe_has_events() ) : ?>

	<div id="pag-top" class="pagination no-ajax pag-search-results">

		<div class="pag-count" id="events-count">
			<?php bpe_events_pagination_count() ?>
		</div>

		<div class="pagination-links" id="events-pag">
			<?php bpe_events_pagination_links() ?>
		</div>

	</div>

    <?php do_action( 'bpe_search_results_before_loop' ) ?>

	<ul id="events-list" class="item-list<?php if( bpe_grid_style() ) : ?> item-list-grid<?php endif; ?>">
	
	<?php while ( bpe_events() ) : bpe_the_event(); ?>

		<?php if( bpe_grid_style() ) : ?>
        
            <?php bpe_load_template( 'events/view/grid' ); ?>                
        
        <?php else : ?>
    
            <?php bpe_load_template( 'events/view/list' ); ?>                
       
        <?php endif; ?>

	<?php endwhile; ?>
    
    <?php do_action( 'bpe_search_results_inside_loop' ) ?>
	
	</ul>

    <?php do_action( 'bpe_search_results_after_loop' ) ?>

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
    
<?php do_action( 'bpe_after_search_results_events_loop' ) ?>

</div><!-- #events-dir-list -->

<?php do_action( 'bpe_search_results_events_content' ) ?>