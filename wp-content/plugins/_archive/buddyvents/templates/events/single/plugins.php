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

            <h3 class="pagetitle">
                <?php _e( 'Events Directory', 'events' ) ?><?php if( ! bpe_is_restricted() ) : ?>&nbsp;<a class="button" href="<?php echo bp_get_root_domain() . '/' . bpe_get_base( 'root_slug' ) . '/'. bpe_get_option( 'create_slug' ) ?>"><?php _e( 'Create Event', 'events' ) ?></a><?php endif; ?>
            </h3>
            
            <div id="event-dir-search" class="dir-search no-ajax">
                <?php bpe_directory_events_search_form() ?>
            </div>

            <?php do_action( 'template_notices' ) ?>
            
            <?php do_action( 'bpe_before_directory_events_content' ) ?>
            
            <?php bpe_load_template( 'events/includes/navigation' ); ?>
            
            <?php do_action( 'bpe_before_directory_events_list' ) ?>
            
            <div id="events-dir-list" class="events dir-list">
    
        	<?php do_action( 'bpe_before_event_plugin_template' ) ?>
        
			<?php if( bpe_has_events() ) : while ( bpe_events() ) :	bpe_the_event(); ?>
                
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
                            <div class="item-title"><?php do_action( 'bpe_template_title' ) ?></div>

                            <div id="item-body">
                
                                <?php do_action( 'bpe_before_event_body' ) ?>
                
                                <?php do_action( 'bpe_template_content' ) ?>
                
                                <?php do_action( 'bpe_after_event_body' ) ?>
                            </div><!-- #item-body -->
            
                        </div>
                    </li>
                </ul>

				<?php endwhile;
			endif; ?>

			<?php do_action( 'bpe_after_event_plugin_template' ) ?>

			</div><!-- #events-dir-list -->

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer() ?>