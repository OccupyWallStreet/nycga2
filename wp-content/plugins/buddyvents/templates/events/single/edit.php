<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */

if( bpe_has_events() ) : while ( bpe_events() ) : bpe_the_event(); ?>

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
                <div class="item-title"><?php printf( __( 'Edit: %s', 'events' ), bpe_get_event_name() ) ?></div>

                <?php if( ! bpe_is_closed_event( false, true ) ) : ?>

                <div class="item-edit-tabs">
                    <ul>
                       	<?php bpe_event_edit_tabs(); ?>
                    </ul>
                </div><!-- .item-list-tabs -->
                
                <form action="<?php bpe_event_edit_form_action() ?>" method="post" enctype="multipart/form-data" id="edit-event-form" class="standard-form">

					<?php if( bpe_is_event_edit_screen( bpe_get_option( 'general_slug' ) ) ) : ?>
                   
                    	<?php bpe_load_template( 'events/single/steps/general' ); ?>                 
    
                    <?php endif; ?>

                    <?php if( bpe_is_event_edit_screen( bpe_get_option( 'logo_slug' )  ) ) : ?>
                    
						<?php bpe_load_template( 'events/single/steps/logo' ); ?>

                	<?php endif; ?>

                    <?php if( bpe_is_event_edit_screen( bpe_get_option( 'manage_slug' ) ) && bpe_get_option( 'enable_attendees' ) === true ) : ?>
                   
						<?php bpe_load_template( 'events/single/steps/attendees' ); ?>

                	<?php endif; ?>

					<?php do_action( 'bpe_custom_edit_steps' ) ?>
                
                </form><!-- #edit-event-form -->
                
                <?php else : ?>
                
                    <div id="message" class="info">
                        <p><?php _e( 'This event is closed and cannot be edited anymore.', 'events' ) ?></p>
                    </div>
                    
                    <?php bpe_remove_recurrence_button() ?>
                
                <?php endif; ?>
           </div>
        </li>
	</ul>

<?php endwhile; ?>
<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'No events were found.', 'events' ) ?></p>
	</div>

<?php endif; ?>