<?php
if ( !defined( 'ABSPATH' ) ) exit;

function etivite_bp_groups_directory_groups_actions() {
	echo '<div class="meta group-extras-meta">';
	do_action( 'etivite_action_groups_directory_actions' );
	echo '</div>';
}
add_action( 'bp_directory_groups_actions', 'etivite_bp_groups_directory_groups_actions',1 );

function etivite_bp_groups_directory_groups_item() {
	echo '<div class="group-extras-item">';
	do_action( 'etivite_action_groups_directory_groups_item' );
	echo '</div>';
}
add_action( 'bp_directory_groups_item', 'etivite_bp_groups_directory_groups_item',1 );

function etivite_bp_groups_directory_loop_forum_link() {

	if ( !bp_group_is_forum_enabled() )
		return;
		
	if ( !bp_group_is_visible() )
		return;
		
	echo '<a href="'. bp_get_group_forum_permalink() .'/">'. __( 'Forum', 'bp-groups-directory' ) .' &rarr;</a>';
}

function etivite_bp_groups_directory_loop_activity_item() {
	global $bp, $activities_template;

	if ( !bp_is_active( 'activity' ) )
		return;

	if ( !bp_group_is_visible() )
		return;

	$show_hidden = false;

	/* Group filtering */
	$object = $bp->groups->id;
	$primary_id = bp_get_group_id();

	if ( 'public' != bp_get_group_status() && ( groups_is_user_member( $bp->loggedin_user->id, $primary_id ) || $bp->loggedin_user->is_super_admin ) )
		$show_hidden = true;
		
	$data = maybe_unserialize( get_option( 'etivite_bp_groupsdirectory' ) );
	$count = $data['activity']['count'];
	if ( !$count || empty($count) )
		$count = 3;

	/* Note: any params used for filtering can be a single value, or multiple values comma separated. */
	$defaults = array(
		'display_comments' => false, // false for none, stream/threaded - show comments in the stream or threaded under items
		'sort' => 'DESC', // sort DESC or ASC
		'page' => 1, // which page to load
		'per_page' => false, // number of items per page
		'max' => $count, // max number to return
		'include' => false, // pass an activity_id or string of ID's comma separated
		'show_hidden' => $show_hidden, // Show activity items that are hidden site-wide?

		/* Filtering */
		'object' => $object, // object to filter on e.g. groups, profile, status, friends
		'primary_id' => $primary_id, // object ID to filter on e.g. a group_id or forum_id or blog_id etc.
		'action' => false, // action to filter on e.g. activity_update, new_forum_post, profile_updated
		'secondary_id' => false, // secondary object ID to filter on e.g. a post_id

		/* Searching */
		'search_terms' => false // specify terms to search on
	);

	extract( $defaults );

	$filter = array( 'user_id' => false, 'object' => $object, 'action' => $action, 'primary_id' => $primary_id, 'secondary_id' => $secondary_id );

	$activities_template = new BP_Activity_Template( $page, $per_page, $max, $include, $sort, $filter, $search_terms, $display_comments, $show_hidden );
	if ( bp_activities() ) {?>
	<div class="latest-group-activity-desc">
		<p><b><?php _e( 'Recent group activity', 'bp-restrictgroups' ) ?></b></p>
	</div>
	<div class="latest-group-activity">
	<?php while ( bp_activities() ) : bp_the_activity(); ?>
		<div class="item-desc" id="activity-<?php bp_activity_id() ?>">
			<div class="activity-avatar">
				<a href="<?php bp_activity_user_link() ?>">
					<?php bp_activity_avatar( 'type=full&width=25&height=25' ) ?>
				</a>
			</div>
			<div class="activity-content">
				<div class="activity-header">
					<?php bp_activity_feed_item_title() ?>
				</div>
			</div>
		</div>
	<?php endwhile; ?>
	</div>
	<?php
	}
}


?>
