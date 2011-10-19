<?php
/**
 * Template tags for BuddyPress Custom Posts
 */

if ( !function_exists( 'bpcp_get_post_type_object' ) ) {
	function bpcp_get_post_type_object() {
		global $bp;
		return get_post_type_object( $bp->{$bp->active_components[$bp->current_component]}->id );
	}
}

if ( !function_exists( 'bpcp_get_create_post_title' ) ) {
	function bpcp_get_create_post_title() {
		global $bp;
		$post_type = get_post_type_object( $bp->{$bp->active_components[$bp->current_component]}->id );
		return $post_type->labels->add_new_item;
	}
}

if ( !function_exists( 'bpcp_create_post_title' ) ) {
	function bpcp_create_post_title() {
		echo bpcp_get_create_post_title();
	}
}

if ( !function_exists( 'bpcp_directory_search_form' ) ) {
	function bpcp_directory_search_form() {
		return;
		global $bp;
	?>
		<form action = '<?php echo $_SERVER['REQUEST_URI']; ?>search/' method = 'POST' id = 'search-<?php echo $bp->current_component; ?>-form'>
			<label><input type = 'text' name = 's' id = '<?php echo $bp->current_component; ?>_search' /></label>
			<input type = 'submit' id = '<?php echo $bp->current_component; ?>_search_submit' value = '<?php _e( 'Search' ); ?>' />
		</form>
	<?php
	}
} 

if ( !function_exists( 'bpcp_get_total_count' ) ) {
	function bpcp_get_total_count( $status = 'publish' ) {
		global $bp;

		$count = wp_count_posts( $bp->{$bp->active_components[$bp->current_component]}->id, 'readable' );
		return $count->{$status};
	}
}


if ( !function_exists( 'bpcp_get_user_count' ) ) {
	function bpcp_get_user_count( $userid, $type, $perm = '' ) {
		global $wpdb, $bp;

		$user = wp_get_current_user();

		$cache_key = $type . "-" . $userid;

		$query = "SELECT post_status, COUNT( * ) AS num_posts FROM {$wpdb->posts} WHERE post_type = %s AND post_author = %s";
		if ( 'readable' == $perm && is_user_logged_in() ) {
			$post_type_object = get_post_type_object($type);
			if ( !current_user_can( $post_type_object->cap->read_private_posts ) ) {
				$cache_key .= '_' . $perm . '_' . $user->ID;
				$query .= " AND (post_status != 'private' OR ( post_author = '$user->ID' AND post_status = 'private' ))";
			}
		}
		$query .= ' GROUP BY post_status';

		$count = wp_cache_get($cache_key, 'counts');
		if ( false !== $count )
			return $count;

		$count = $wpdb->get_results( $wpdb->prepare( $query, $type, $userid ), ARRAY_A );

		$stats = array();
		foreach ( get_post_stati() as $state )
			$stats[$state] = 0;

		foreach ( (array) $count as $row )
			$stats[$row['post_status']] = $row['num_posts'];

		$stats = (object) $stats;
		wp_cache_set($cache_key, $stats, 'counts');

		return $stats;
	}
}

function bpcp_locate_template( $templates, $load = false, $require_once = true ) {
	global $bp;

	global $wp_post_types;

	if ( !is_array( $templates ) ) return;

	$located = locate_template( $templates, false );

	if ( '' == $located ) {
		if ( isset( $bp->current_component ) ) {
			$id = $bp->active_components[ $bp->current_component ];
			$type = get_post_type_object( $id );

			if ( $type )
				foreach ( $templates as $template )
					if ( file_exists( $type->theme_dir . '/' . $template ) ) {
						$located = $type->theme_dir . '/' . $template;
						break;
					}
		}

		if ( '' == $located ) 
			foreach( $templates as $template )
				if( file_exists( BPCP_THEMES_DIR . '/' . $template ) ) {
					$located = BPCP_THEMES_DIR . '/' . $template;
					break;
				}
	}

	if ( '' != $located && $load )
		load_template( $located, $require_once );

	return $located;
}

function bpcp_type_avatar( $args = Array() ) {
	$val = bpcp_get_type_avatar( $args );
	echo $val;
}

function bpcp_get_type_avatar( $args = Array() ) {
	global $bp, $post;

	$defaults = Array(
		'width' => 50,
		'height' => 50,
		'type' => 'thumbnail',
		'itemid' => $post->ID,
		'object' => $bp->current_component
	);

	extract( wp_parse_args( $args, $defaults ) );

	if ( $type == 'full' ) { $type = Array( 150, 150 ); $height = 150; $width = 150; }

	$targs = Array( 'width' => $width, 'height' => $height, 'class' => 'avatar' );

	if ( '' != ( $html = get_the_post_thumbnail( $itemid, $type, $targs ) ) ) {
		return $html;
	} else {
		// Set gravatar size
		if ( $width )
			$grav_size = $width;
		else if ( 'full' == $type )
			$grav_size = BP_AVATAR_FULL_WIDTH;
		else if ( 'post-thumbnail' == $type )
			$grav_size = BP_AVATAR_THUMB_WIDTH;

		// Set gravatar type
		if ( empty( $bp->grav_default->{$object} ) )
			$default_grav = 'wavatar';
		else if ( 'mystery' == $bp->grav_default->{$object} )
			$default_grav = apply_filters( 'bp_core_mysteryman_src', BP_AVATAR_DEFAULT, $grav_size );
		else
			$default_grav = $bp->grav_default->{$object};

		$email = "{$itemid}-{$object}@{$bp->root_domain}";

		// Set host based on if using ssl
		if ( is_ssl() )
			$host = 'https://secure.gravatar.com/avatar/';
		else
			$host = 'http://www.gravatar.com/avatar/';

		// Filter gravatar vars
		$gravatar	= apply_filters( 'bp_gravatar_url', $host ) . md5( strtolower( $email ) ) . '?d=' . $default_grav . '&amp;s=' . $grav_size;

		// Return gravatar wrapped in <img />
		$class = 'avatar';
		$post = get_post( $itemid );
		$alt = $post->post_title;
		return  '<img src="' . $gravatar . '" alt="' . $alt . '" class="' . $class . '" />';
	}
}

function bpcp_get_last_active( $typeid = 0) {
	global $post;
	if ( empty( $typeid ) || $typeid == 0 )
		$typeid = $post->ID;
	
	if ( '' != ( $last_active = get_post_meta( $typeid, '_bpcp_last_activity', true ) ) )
		return bp_core_time_since( $last_active );

	return false;
}

function bpcp_last_active( $typeid  = 0 ) {
	if ( $last_active = bpcp_get_last_active( $typeid ) ) {
		printf( __( "Active %s ago." ), $last_active );
	} else echo __( "Not active yet." );
}

function bpcp_get_the_author( $postid = 0) {
	global $post;
	if ( 0 == $postid ) $postid = $post->ID;

	$post = get_post( $postid );
	$auth = get_userdata( $post->post_author );

	return "<a href = '" . bp_core_get_user_domain( $auth->ID ) . "'>" . bp_core_fetch_avatar( Array( 'item_id' => $auth->ID, 'email' => $auth->user_email ) ) .  "</a>";
}

function bpcp_the_author( $postid = 0 ) {
	echo bpcp_get_the_author( $postid );
}

function bpcp_is_visible( $postid = 0 ) {
	global $post;

	if ( 0 == $postid ) $postid = $post->ID;
	else return;

	return current_user_can( 'read_post', $postid );
}

function bpcp_can_edit( $postid = 0 ) {
	global $post;

	if ( 0 == $postid ) $postid = $post->ID;
	else return;

	return current_user_can( 'edit_post', $postid );
}

function bpcp_is_home() {
	global $bp;
	$type = get_post_type_object( $bp->active_components[ $bp->current_component ] );

	if ( !$type ) return false;

	if ( $type->slugs->single_home == $bp->current_action ) 
		return true;
	else
		return false;
}

function bpcp_is_edit() {
	global $bp;
	$type = get_post_type_object( $bp->active_components[ $bp->current_component ] );

	if ( !$type ) return false;

	if ( $type->slugs->single_edit == $bp->current_action ) 
		return true;
	else
		return false;
}

function bpcp_get_user_post_ids( $args ) {
	global $bp, $wpdb;

	$userid = bp_loggedin_user_id();
	$type = $bp->active_components[ $bp->current_component ];

	extract( wp_parse_args( $args, compact( $userid, $type ) ) );


	if ( ! $post_ids = wp_cache_get("$userid-$type", 'bpcp_user_post_ids') ) {
		$post_ids = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE post_type = '$type' AND post_author = '$userid' AND post_status = 'publish'");
		wp_cache_add("$userid-$type", $post_ids, 'posts');
	}

	return $post_ids;
}

function bpcp_get_activity_feed_link() {
	return get_permalink() . 'feed/';
}

function bpcp_activity_feed_link() {
	echo bpcp_get_activity_feed_link();
}

function bpcp_is_forum() {
	global $bp;
	if ( $bp->current_action == 'forum' ) return true;
}

function bpcp_is_forum_topic_edit() {
	global $bp;

	if ( isset( $bp->action_variables[2] ) && $bp->action_variables[2] == 'edit' ) return true;

	return false;
}

function bpcp_is_forum_topic() {
	global $bp;
	
	if ( isset( $bp->action_variables[0] ) && $bp->action_variables[0] == 'topic' ) return true;

	return false;
}

function bpcp_is_edit_topic() {
	global $bp;

	if ( isset( $bp->action_variables[2] ) && $bp->action_variables[2] == 'edit' && empty( $bp->action_variables[3] ) ) return true;

	return false;
}

function bpcp_is_activity() {
	global $bp;
	$type = get_post_type_object( $bp->active_components[ $bp->current_component ] );

	if ( $bp->current_action == $type->slugs->single_activity ) 
		return true;
	
	return false;
}
