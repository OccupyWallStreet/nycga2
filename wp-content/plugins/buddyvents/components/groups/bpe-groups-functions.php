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

/**
 * Sort and display the main navigation
 *
 * @package Groups
 * @since 1.4
 */
function bpe_group_navigation()
{
	$menu = array(
        'active' 	=> '<li'. bpe_highlight_current_tab( bp_action_variable( 0 ), bpe_get_option( 'active_slug' ), 'current' ) .'><a href="'. bp_get_group_permalink() . bpe_get_base( 'slug' ) . bpe_check_default_slug( bpe_get_option( 'active_slug' ) ) .'">'. sprintf( __( 'Active <span>%d</span>', 'events' ), bpe_get_event_count( 'active', bp_get_current_group_id(), 'group' ) ) .'</a></li>',
        'archive' 	=> '<li'. bpe_highlight_current_tab( bp_action_variable( 0 ), bpe_get_option( 'archive_slug' ), 'current' ) .'><a href="'. bp_get_group_permalink() . bpe_get_base( 'slug' ) . bpe_check_default_slug( bpe_get_option( 'archive_slug' ) ) .'">'. sprintf( __( 'Archive <span>%d</span>', 'events' ), bpe_get_event_count( 'archive', bp_get_current_group_id(), 'group' ) ) .'</a></li>',
        'calendar' 	=> '<li'. bpe_highlight_current_tab( bp_action_variable( 0 ), bpe_get_option( 'calendar_slug' ), 'current' ) .'><a href="'. bp_get_group_permalink() . bpe_get_base( 'slug' ) . bpe_check_default_slug( bpe_get_option( 'calendar_slug' ) ) .'">'. __( 'Calendar', 'events' ) .'</a></li>',
        'map' 		=> '<li'. bpe_highlight_current_tab( bp_action_variable( 0 ), bpe_get_option( 'map_slug' ), 'current' ) .'><a href="'. bp_get_group_permalink(). bpe_get_base( 'slug' ) . bpe_check_default_slug( bpe_get_option( 'map_slug' ) ) .'">'. __( 'Map', 'events' ) .'</a></li>',
        'create' 	=> '<li><a href="'. bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/'. bpe_get_option( 'create_slug' ) .'/'. bpe_get_option( 'step_slug' ) .'/'. bpe_get_option( 'general_slug' ) .'/?group='. bp_get_current_group_id() .'">'. __( 'Create', 'events' ) .'</a></li>'
	);

	if( ! in_array( bp_loggedin_user_id(), bpe_get_group_admins() ) )
		unset( $menu['create'] );

	$new_order = bpe_sort_menu( $menu );
	
	$new_menu = '';
	foreach( $new_order as $value )
		$new_menu .= $value;
	
	echo $new_menu;
}

/**
 * Get all groups where the user is an admin
 *
 * @package Groups
 * @since 	1.0
 */
function bpe_group_dropdown( $value = false, $label = true )
{
	global $bpe;
	
	if( ! $value )
		$value = bpe_get_event_group_id( bpe_get_displayed_event() );
	
	if( ! $value )
		$value = bpe_display_cookie( 'group_id', false );
	
	if( ! empty( $bpe->user_groups ) )
	{
		$group = ( isset( $_GET['group'] ) ) ? (int)$_GET['group'] : 0;
		
		if( $group > 0 )
			$value = $group;

		if( $label ) : ?>
        <label for="group_id"><?php if( bpe_get_option( 'restrict_creation' ) === true ) : ?>* <?php endif; ?><?php _e( 'Group', 'events' ) ?> &nbsp; <span class="ajax-loader"></span></label>
		<?php endif; ?>
        
        <select id="group_id" name="group_id">
        	<option value=""></option>
			<?php foreach( $bpe->user_groups as $group_id => $group_name ) { ?>
                <option<?php if( $value == $group_id ) echo ' selected="selected"'; ?> value="<?php echo $group_id ?>"><?php echo $group_name ?></option>
            <?php } ?>
        </select> <small><?php _e( 'You can attach this event to a group. Your event will have to be approved by a group admin, if you are not the admin or a moderator of the group.', 'events' ) ?></small>

		<hr />
		<?php
        do_action( 'bpe_group_dropdown_action' );
	}
}
add_action( 'bpe_general_create_after_url', 'bpe_group_dropdown' );
add_action( 'bpe_general_edit_after_url', 	'bpe_group_dropdown' );

/**
 * Get all group admin ids
 *
 * @package Groups
 * @since 	1.4
 */
function bpe_get_group_admins()
{
	global $bp;
	
	$ids = array();
	
	foreach( $bp->groups->current_group->admins as $admin )
		$ids[] = $admin->user_id;
		
	return $ids;
}
?>