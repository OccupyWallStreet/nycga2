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

class Buddyvents_Group_Approval extends BP_Group_Extension
{	
	/**
	 * PHP5 Constructor
	 *
	 * @package	 Groups
	 * @since 	 2.0
	 */
	function __construct()
	{
        $this->name = __( 'Approve Events', 'events' );
        $this->slug = bpe_get_option( 'approve_slug' );
 
        $this->enable_create_step 	= false;
        $this->enable_nav_item 		= false;
        $this->enable_edit_item 	= true;
	}

	/**
	 * Contents of the edit screen
	 *
	 * @package	 Groups
	 * @since 	 2.0
	 */
    function edit_screen()
	{
        if( ! bp_is_group_admin_screen( $this->slug ) )
            return false;
			
		bpe_load_template( 'events/group/approve' );

        wp_nonce_field( 'groups_edit_save_' . $this->slug );
    }
 
	/**
	 * Save any values from the edit screen
	 *
	 * @package	 Groups
	 * @since 	 2.0
	 */
    function edit_screen_save()
	{
        global $bp, $wpdb;

		// an event gets approved
		if( bp_is_action_variable( 'approved', 1 ) && bp_action_variable( 2 ) )
		{
			check_admin_referer( 'bpe_approved_event' );

			$ev = new Buddyvents_Events( bp_action_variable( 2 ) );
			
			bpe_group_approve_event( bpe_get_event_id( $ev ) );
			bpe_add_member( null, bpe_get_event_id( $ev ), bp_loggedin_user_id(), 2, bp_core_current_time(), 'admin' );

			bpe_send_approval_status_mail( $ev, 'approved' );

			bp_core_add_message( __( 'Event has been approved.', 'events' ) );
			bp_core_redirect( bp_get_group_permalink( groups_get_current_group() ) .'admin/'. $this->slug .'/' );
		}

		// an event gets declined
		if( bp_is_action_variable( 'declined', 1 ) && bp_action_variable( 2 ) )
		{
			check_admin_referer( 'bpe_declined_event' );

			$ev = new Buddyvents_Events( bp_action_variable( 2 ) );
	
			do_action( 'bpe_delete_event_action', $ev );
			
			bpe_send_approval_status_mail( $ev, 'deleted' );
			
			$ev->delete();

			bp_core_add_message( __( 'Event has been declined.', 'events' ) );
	        bp_core_redirect( bp_get_group_permalink( groups_get_current_group() ) .'admin/'. $this->slug .'/' );
		}
			
 
        if( ! isset( $_POST['save'] ) )
            return false;
 
        check_admin_referer( 'groups_edit_save_' . $this->slug );

		$bulk = ( ! isset( $_POST['bulkapprove'] ) || $_POST['bulkapprove'] == '-1' ) ? false : $_POST['bulkapprove'];

		if( ! $bulk )
		{
			bp_core_add_message( __( 'No events were selected', 'events' ), 'error' );
			bp_core_redirect( bp_get_group_permalink( groups_get_current_group() ) .'admin/'. $this->slug .'/' );
		}
		
		$ids = $wpdb->escape( implode( ',', (array)$_POST['be'] ) );
		
		$count = 0;
		switch( $bulk )
		{
			case 'approve' :
				foreach( (array)$_POST['be'] as $event_id )
				{
					$ev = new Buddyvents_Events( (int)$event_id );
					
					bpe_group_approve_event( bpe_get_event_id( $ev ) );
					bpe_add_member( null, bpe_get_event_id( $ev ), bp_loggedin_user_id(), 2, bp_core_current_time(), 'admin' );
					bpe_send_approval_status_mail( $ev, 'approved' );
					$count++;
				}
				break;

			case 'del' :
				foreach( (array)$_POST['be'] as $event_id )
				{
					$ids[] = $event_id;
					
					$ev = new Buddyvents_Events( (int)$event_id );
					
					do_action( 'bpe_delete_event_action', $ev );
					
					bpe_send_approval_status_mail( $ev, 'deleted' );
					$count++;
				}
				
				bpe_delete_by_ids( $ids );
				break;
		}

        bp_core_add_message( sprintf( _n( '%d event was bulk-edited', '%d events were bulk-edited.', $count, 'events' ), $count ) );
        bp_core_redirect( bp_get_group_permalink( groups_get_current_group() ) .'admin/'. $this->slug .'/' );
    }
}
bp_register_group_extension( 'Buddyvents_Group_Approval' );
?>