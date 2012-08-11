<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */

if( bpe_has_events() ) :  while ( bpe_events() ) : bpe_the_event(); ?>

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
	
		<li id="event-<?php bpe_event_id() ?>" class="vevent <?php bpe_event_status_class() ?>">

			<?php bpe_load_template( 'events/includes/single-nav' ); ?> 
			
			<div class="item">
				<div class="item-title"><?php printf( __( 'Attendees: <span class="summary">%s</span>', 'events' ), bpe_get_event_name() ) ?></div>
				
				<?php bpe_load_template( 'events/includes/event-header' ); ?>
                
				<div class="item-edit-tabs">
                    <ul>
                    	<?php bpe_event_attendee_tabs() ?>
                    </ul>
                </div>
                
                <?php if( bp_has_members( 'include='. bpe_event_get_member_ids() .'&type=alphabetical' ) ) : ?>
                
                    <div class="pagination">
                
                        <div class="pag-count" id="member-dir-count">
                            <?php bp_members_pagination_count() ?>
                        </div>
                
                        <div class="pagination-links" id="member-dir-pag">
                            <?php bp_members_pagination_links() ?>
                        </div>
                
                    </div>
                
                    <?php do_action( 'bp_before_directory_members_list' ) ?>
                
                    <ul id="members-list" class="item-list">
                    <?php while ( bp_members() ) : bp_the_member(); ?>
                
                        <li>
                            <div class="item-avatar">
                                <a href="<?php bp_member_permalink() ?>"><?php bp_member_avatar() ?></a>
                            </div>
                
                            <div class="item item-member">
                                <div class="item-title">
                                    <a href="<?php bp_member_permalink() ?>"><span class="attendee"><?php bp_member_name() ?></span></a>
                                </div>
                                <div class="item-meta"><span class="activity"><?php bp_member_last_active() ?></span></div>
                
                                <?php do_action( 'bp_directory_members_item' ) ?>
                            </div>
                        </li>
                
                    <?php endwhile; ?>
                    </ul>
                
                    <?php do_action( 'bp_after_directory_members_list' ) ?>
                
                <?php else: ?>
                
                    <div id="message" class="info">
                        <p><?php _e( 'No attendees found', 'events' ) ?></p>
                    </div>
                
                <?php endif; ?>
		   </div>
			
			<div class="action">
				<span class="activity"><?php bpe_event_attendees() ?></span>
        		<span class="event-admin organizer"><?php _e( 'Creator:', 'events' ) ?><br /><?php bpe_event_user_avatar() ?></span>
			</div>
		</li>
	</ul>

<?php endwhile; ?>
<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'No events were found.', 'events' ) ?></p>
	</div>

<?php endif; ?>