<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */

get_header() ?>

	<div id="content">
		<div class="padder">
        
        <?php do_action( 'bpe_before_index' ) ?>
     
        <?php if( bpe_is_events_create() ) : ?>
        	<?php bpe_load_template( 'events/top-level/create' ); ?>

        <?php elseif( bpe_is_events_active() ) : ?>
        	<?php bpe_load_template( 'events/top-level/active' ); ?>
 
        <?php elseif( bpe_is_events_archive() ) : ?>
        	<?php bpe_load_template( 'events/top-level/archive' ); ?>

        <?php elseif( bpe_is_event_search_results() ) : ?>
        	<?php bpe_load_template( 'events/top-level/results' ); ?>

		<?php elseif( bpe_is_sale_success() ) : ?>
            <?php bpe_load_template( 'events/top-level/success' ); ?>
    
        <?php elseif( bpe_is_sale_cancel() ) : ?>
            <?php bpe_load_template( 'events/top-level/cancel' ); ?>

        <?php elseif( bpe_is_event_search() ) : ?>
        	<?php bpe_load_template( 'events/top-level/search' ); ?>

		 <?php elseif( bpe_is_event_category() ) : ?>
            <?php bpe_load_template( 'events/top-level/category' ); ?>

		 <?php elseif( bpe_is_event_timezone() ) : ?>
            <?php bpe_load_template( 'events/top-level/timezone' ); ?>

		 <?php elseif( bpe_is_event_venue() ) : ?>
            <?php bpe_load_template( 'events/top-level/venue' ); ?>

		 <?php elseif( bpe_is_event_day_archive() ) : ?>
            <?php bpe_load_template( 'events/top-level/day' ); ?>

		 <?php elseif( bpe_is_event_month_archive() ) : ?>
            <?php bpe_load_template( 'events/top-level/month' ); ?>
           
        <?php elseif( bpe_is_events_map() ) : ?>
        	<?php bpe_load_template( 'events/top-level/map' ); ?>

        <?php elseif( bpe_is_events_calendar() ) : ?>
        	<?php bpe_load_template( 'events/top-level/calendar' ); ?>

        <?php elseif( bpe_is_events_directory_loop() ) : ?>
        	<?php bpe_load_template( 'events/top-level/'. bpe_get_option( 'default_tab' ) ); ?>
            
        <?php endif; ?>
        
        <?php do_action( 'bpe_after_index' ) ?>

   		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer() ?>