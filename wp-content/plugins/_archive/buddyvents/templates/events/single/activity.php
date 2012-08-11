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
<div class="activity">
	<?php if ( bpe_event_has_activity() ) : ?>
        
        <h3><?php _e( 'Event Activity', 'events' ) ?></h3> 
    
        <ul id="activity-list" class="activity-list item-list">
        
        <?php while ( bp_activities() ) : bp_the_activity(); ?>
    
			<?php locate_template( array( 'activity/entry.php' ), true, false ); ?>
                
        <?php endwhile; ?>
        
        </ul>
    
    <?php else : ?>
        
        <div id="message" class="info">
		<?php if( is_user_logged_in() ) : ?>
            <p><?php _e( 'Be the first to comment on this event.', 'events' ) ?></p>
        <?php else : ?>
            <p><?php _e( 'Log in to comment on this event.', 'events' ) ?></p>
        <?php endif; ?>
        </div>
    
    <?php endif; ?>
    
    <?php if( is_user_logged_in() ) : ?>
    
        <h3 class="add-event-comm"><?php _e( 'Add your comment', 'events' ) ?></h3>
        
        <form id="event-activity-comment-form" name="event-activity-comment-form" class="standard-form" action="" method="post">
            
            <textarea id="comment_text" name="comment_text"></textarea>
        
            <div class="submit">
                <input type="submit" value="<?php _e( 'Send Comment', 'events' ) ?>" id="send_event_comment" name="send_event_comment" />
            </div>

            <?php wp_nonce_field( 'bpe_event_comment' ); ?>
        </form>
        
    <?php endif; ?>
</div>