<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */

// No direct access is allowed
if( ! defined( 'ABSPATH' ) ) exit;

class Buddyvents_Forums_Extension extends Buddyvents_Extension
{
	/**
	 * PHP5 Constructor
	 * 
	 * @package	Forums
	 * @since 	2.1
	 * 
	 * @uses	bpe_get_option()
	 */
	public function __construct()
	{
 		$this->name 		= __( 'Forum', 'events' );
		$this->display_name = sprintf( __( '%s: Forum', 'events' ), bpe_get_event_name( bpe_get_displayed_event() ) );
		$this->slug 		= bpe_get_option( 'forum_slug' );
		$this->forum_id 	= bpe_get_eventmeta( bpe_get_displayed_event( 'id' ), 'forum_id' );
		$this->visibility	= $this->get_visibility();
		
		$this->create_step_position = apply_filters( 'bpe_forum_create_step_position', 26 );
		$this->enable_create_step 	= true;
		$this->enable_edit_item 	= true;
		$this->enable_nav_item 		= ( empty( $this->forum_id ) ) ? false : true;
	}
	
	/**
	 * Get the visibility of the forum
 	 * 
	 * @package	Forums
	 * @since 	2.1
	 */
	private function get_visibility()
	{
		$status = bbp_get_forum_visibility( $this->forum_id );
		
		switch( $status )
		{
			case 'hidden':
				return 3;
				break;

			case 'private':
				return 2;
				break;
			
			default:
			case 'publish':
				return 1;
				break;
		}
	}

	/**
	 * Display create screen
	 * 
	 * @package	Forums
	 * @since 	2.1
	 */
	public function create_screen()
	{
		if( ! bpe_is_event_creation_step( $this->slug ) )
			return false;

		// Forum data
		$forum = bbp_get_forum( $this->forum_id );
	
		?>
		<label for="enable_forum">
			<input type="checkbox" id="enable_forum" name="enable_forum"<?php if( ! empty( $this->forum_id ) ) echo ' checked="checked"'; ?> value="true" />
			<?php _e( 'Check to enable a forum for your event.', 'events' ) ?>
		</label>
		
		<div id="forum-wrapper">
			<hr />
	
			<label for="forum_name"><?php _e( 'Forum Name (Maximum Length: 80) *', 'events' ) ?>:</label>
			<input type="text" id="forum_name" name="forum_name" value="<?php echo esc_attr( $forum->post_title ) ?>" />
			
			<label for="forum_description"><?php _e( 'Forum Description', 'events' ) ?>:</label>
			<?php bpe_editor( $forum->post_content, 'forum_description' ) ?>
			
			<label for="forum_visibility"><?php _e( 'Visibility', 'events' ) ?>:</label>
			<select id="forum_visibility" name="forum_visibility">
				<option<?php if( $forum->post_status == 'publish' ) echo ' selected="selected"' ?> value="publish"><?php _e( 'Public', 'events' ) ?></option>
				<option<?php if( $forum->post_status == 'private' ) echo ' selected="selected"' ?> value="private"><?php _e( 'Private', 'events' ) ?></option>
				<option<?php if( $forum->post_status == 'hidden' ) echo ' selected="selected"' ?> value="hidden"><?php _e( 'Hidden', 'events' ) ?></option>
			</select>	
		</div>
		
		<script type="text/javascript">
			jQuery(document).ready(function() {
				if( jQuery('#enable_forum').is(':checked') ){
					jQuery('#forum-wrapper').show();
				} else {
					jQuery('#forum-wrapper').hide();
				}
				jQuery('#enable_forum').click(function() {
					if( jQuery(this).is(':checked') ){
						jQuery('#forum-wrapper').show();
					} else {
						jQuery('#forum-wrapper').hide();
					}
				});
			});
		</script>
		<?php
		wp_nonce_field( 'bpe_add_event_'. $this->slug );
	}

	/**
	 * Process create screen
	 * 
	 * @package	Forums
	 * @since 	2.1
	 */
	public function create_screen_save()
	{
		check_admin_referer( 'bpe_add_event_'. $this->slug );
		
		if( isset( $_POST['enable_forum'] ) ) :
			if( empty( $_POST['forum_name'] ) ) :
				bpe_add_message( __( 'You need to fill in a forum name.', 'events' ), 'error' );
				bp_core_redirect( bp_get_root_domain() . $_POST['_wp_http_referer'] );
			endif;
			
			$forum_id = bbp_insert_forum( array(
				'post_parent'    => bpe_get_option( 'main_event_forum' ),
				'post_status'    => $_POST['forum_visibility'],
				'post_author'    => bp_loggedin_user_id(),
				'post_content'   => $_POST['forum_description'],
				'post_title'     => $_POST['forum_name']
			) );
			
			bpe_update_eventmeta( bpe_get_displayed_event( 'id' ),'forum_id', $forum_id );
		else :
			$forum_id = bpe_get_eventmeta( bpe_get_displayed_event( 'id' ),'forum_id' );
			
			// if a user goes back to disable the forum, we remove it completely
			if( $forum_id ) :
				wp_delete_post( $forum_id, true );
				bpe_delete_eventmeta( bpe_get_displayed_event( 'id' ),'forum_id' );
			endif;
		endif;
	}

	/**
	 * Display edit screen
	 * 
	 * @package	Forums
	 * @since 	2.1
	 */
	public function edit_screen()
	{
		if( ! bpe_is_event_edit_screen( $this->slug ) )
			return false;

		// Forum data
		$forum 	  = bbp_get_forum( $this->forum_id );
	
		?>
		<label for="enable_forum">
			<input type="checkbox" id="enable_forum" name="enable_forum"<?php if( ! empty( $this->forum_id ) ) echo ' checked="checked"'; ?> value="true" />
			<?php _e( 'Check to enable a forum for your event.', 'events' ) ?>
		</label>
		
		<?php if( ! empty( $forum_id ) ) : ?>
			<p><?php _e( '<strong>Attention</strong>: Disabling an existing forum will remove it completely, including all topics and replies. This is not reversible!', 'events' ) ?></p>
		<?php endif; ?>
		
		<div id="forum-wrapper">
			<hr />
	
			<label for="forum_name"><?php _e( 'Forum Name (Maximum Length: 80) *', 'events' ) ?>:</label>
			<input type="text" id="forum_name" name="forum_name" value="<?php echo esc_attr( $forum->post_title ) ?>" />
			
			<label for="forum_description"><?php _e( 'Forum Description', 'events' ) ?>:</label>
			<?php bpe_editor( $forum->post_content, 'forum_description' ) ?>
			
			<label for="forum_visibility"><?php _e( 'Visibility', 'events' ) ?>:</label>
			<select id="forum_visibility" name="forum_visibility">
				<option<?php if( $forum->post_status == 'publish' ) echo ' selected="selected"' ?> value="publish"><?php _e( 'Public', 'events' ) ?></option>
				<option<?php if( $forum->post_status == 'private' ) echo ' selected="selected"' ?> value="private"><?php _e( 'Private', 'events' ) ?></option>
				<option<?php if( $forum->post_status == 'hidden' ) echo ' selected="selected"' ?> value="hidden"><?php _e( 'Hidden', 'events' ) ?></option>
			</select>	
		</div>

        <div class="submit">
            <input type="submit" value="<?php _e( 'Save Changes', 'events' ) ?>" id="edit-event" name="edit-event" />
        </div>
		
		<script type="text/javascript">
			jQuery(document).ready(function() {
				if( jQuery('#enable_forum').is(':checked') ){
					jQuery('#forum-wrapper').show();
				} else {
					jQuery('#forum-wrapper').hide();
				}
				jQuery('#enable_forum').click(function() {
					if( jQuery(this).is(':checked') ){
						jQuery('#forum-wrapper').show();
					} else {
						jQuery('#forum-wrapper').hide();
					}
				});
			});
		</script>
		<?php		
		wp_nonce_field( 'bpe_edit_event_'. $this->slug );
	}

	/**
	 * Process edit screen
	 * 
	 * @package	Forums
	 * @since 	2.1
	 */
 	public function edit_screen_save()
	{
		if( ! isset( $_POST['edit-event'] ) )
			return false;

		check_admin_referer( 'bpe_edit_event_'. $this->slug );
		
		$forum_id = bpe_get_eventmeta( bpe_get_displayed_event( 'id' ),'forum_id' );
			
		if( isset( $_POST['enable_forum'] ) ) :
			if( empty( $_POST['forum_name'] ) ) :
				bpe_add_message( __( 'You need to fill in a forum name.', 'events' ), 'error' );
				bp_core_redirect( bp_get_root_domain() . $_POST['_wp_http_referer'] );
			endif;
			
			$forum_id = bbp_insert_forum( array(
				'ID'			 => ( ( ! empty( $forum_id ) ) ? $forum_id : null ),
				'post_parent'    => bpe_get_option( 'main_event_forum' ),
				'post_status'    => $_POST['forum_visibility'],
				'post_author'    => bp_loggedin_user_id(),
				'post_content'   => $_POST['forum_description'],
				'post_title'     => $_POST['forum_name']
			) );
			
			bpe_update_eventmeta( bpe_get_displayed_event( 'id' ),'forum_id', $forum_id );
		else :
			// if a user goes back to disable the forum, we remove it completely
			if( $forum_id ) :
				wp_delete_post( $forum_id, true );
				bpe_delete_eventmeta( bpe_get_displayed_event( 'id' ),'forum_id' );
			endif;
		endif;

		bpe_add_message( __( 'Forum details have been successfully updated.', 'events' ) );

		if( is_admin() )
			bp_core_redirect( admin_url( 'admin.php?page='. EVENT_FOLDER .'&paged=1&event='. bpe_get_displayed_event( 'id' ) .'&step='. $this->slug ) );
		else
			bp_core_redirect( bpe_get_event_link( bpe_get_displayed_event() ) . bpe_get_option( 'edit_slug' ) .'/'. $this->slug .'/' );
	}

	/**
	 * Show the various forum pages
	 * 
	 * @package	Forums
	 * @since 	2.1
	 */
	public function display()
	{
		bpe_load_template( 'events/includes/event-header' );

		echo '<div id="event-forum">';
			
			if( bpe_is_event_forum_tag() ) :
				$this->display_tag_archive();
				
			elseif( bpe_is_event_forum_reply_edit() ) :
				$this->display_reply_edit();
	
			elseif( bbp_is_topic_edit() ) :
				$this->display_topic_edit();
	
			elseif( bpe_is_event_forum_reply() ) :
				$this->display_reply();
	
			elseif( bpe_is_event_forum_topic() ) :
				$this->display_forum_topic();
	
			elseif( bpe_is_event_forum() ) :
				$this->display_forum();
	
			endif;
		
		echo '</div>';
	}

	/**
	 * Display a topic tag archive
	 * 
	 * @package	Forums
	 * @since 	2.1
	 */
	private function display_tag_archive()
	{
		echo '<h3 class="bpe-topic-title">';
			printf( __( 'Topic Tag: %s', 'events' ), bbp_get_topic_tag_name( bp_action_variable( 3 ) ) );
		echo '</h3>';

		query_posts( array( 
			'post_parent' => $this->forum_id,
			'post_type'   => bbp_get_topic_post_type(),
			'tax_query'   => array(
				array(
					'taxonomy' 	=> bbp_get_topic_tag_tax_id(),
					'terms' 	=> array( bp_action_variable( 3 ) ),
					'field' 	=> 'slug'
				)
			)
		) );
			
		bbp_get_template_part( 'bbpress/content', 'archive-topic' );
	}
	
	/**
	 * Show the edit reply form
	 * 
	 * @package	Forums
	 * @since 	2.1
	 */
	private function display_reply_edit()
	{
		$reply_id = bpe_forums_get_id( 'reply' );
			
		query_posts( array( 
			'post__in' 	=> array( $reply_id ), 
			'post_type' => bbp_get_reply_post_type()
		) );
			
		while ( have_posts() ) : the_post();
			bbp_get_template_part( 'bbpress/form', 'reply' );
		endwhile;
	}

	/**
	 * Show the edit topic form
	 * 
	 * @package	Forums
	 * @since 	2.1
	 */
	private function display_topic_edit()
	{
		$topic_id = bpe_forums_get_id();
			
		query_posts( array( 
			'post__in' 	=> array( $topic_id ), 
			'post_type' => bbp_get_topic_post_type()
		) );
			
		while ( have_posts() ) : the_post();
			if( ! empty( $_GET['action'] ) && ( 'merge' == $_GET['action'] ) ) :
				bbp_get_template_part( 'bbpress/form', 'topic-merge' );
					
			elseif( ! empty( $_GET['action'] ) && ( 'split' == $_GET['action'] ) ) :
				bbp_get_template_part( 'bbpress/form', 'topic-split' );
					
			else :
				bbp_get_template_part( 'bbpress/form', 'topic' );
			endif;
		endwhile;
	}

	/**
	 * Show a reply
	 * 
	 * @package	Forums
	 * @since 	2.1
	 */
	private function display_reply(){}
	
	/**
	 * Show a single topic
	 * 
	 * @package	Forums
	 * @since 	2.1
	 */
	private function display_forum_topic()
	{
		$topic_id = bpe_forums_get_id();

		if ( bbp_get_view_all( 'edit_others_replies' ) )
			$status = join( ',', array( 
									bbp_get_public_status_id(), 
									bbp_get_closed_status_id(), 
									bbp_get_spam_status_id(), 
									bbp_get_trash_status_id()
								)
							);
		else
			$status = join( ',', array(
									bbp_get_public_status_id(), 
									bbp_get_closed_status_id()
								)
							);

		query_posts( array( 
			'post__in' 	  => array( $topic_id ), 
			'post_type'   => bbp_get_topic_post_type(),
			'post_status' => $status
		) );
			
		while ( have_posts() ) : the_post();
			echo '<h3 class="bpe-topic-title">'. get_the_title() .'</h3>';

			bbp_get_template_part( 'bbpress/content', 'single-topic' );
		endwhile;
	}
	
	/**
	 * Display a forum
	 * 
	 * @package	Forums
	 * @since 	2.1
	 */
	private function display_forum()
	{
		echo '<h3 class="bpe-topic-title">'. bbp_get_forum_title() .'</h3>';
			
		echo wpautop( bbp_get_forum_content() );
			
		query_posts( array( 
			'post__in' 	=> array( $this->forum_id ), 
			'post_type' => bbp_get_forum_post_type() 
		) );
			
		while ( have_posts() ) : the_post();
			bbp_get_template_part( 'bbpress/content', 'single-forum' );
		endwhile;
	}
}
bpe_register_event_extension( 'Buddyvents_Forums_Extension' );
?>