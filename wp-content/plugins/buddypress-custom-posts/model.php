<?php
/**
 * model.php
 *
 * All database interactions are controlled by this file.
 *
 * @author kunalbhalla
 *
 * @since 0.1
 */

/**
 * Any and all data related functions.
 *
 * @since 0.1
 */
class bpcp_M {

	/**
	 * Eases typing.
	 *
	 * @var string $id Post type identifier
	 *
	 * @since 0.1
	 */
	var $id;

	/**
	 * A copy of settings stored in the main controller.
	 * Avoiding the global version to avoid 'timing' issues.
	 *
	 * @var Mixed Array $settings
	 *
	 * @since 0.1
	 */
	var $settings;

	/**
	 * Constructor, initializes settings and id.
	 *
	 * @see bpcp::bpcp
	 *
	 * @since 0.1
	 */
	function bpcp_M( $settings ) {
		$this->settings = $settings;
		$this->id = $settings['id'];
	}

	/**
	 * Create a new forum for a given post id.
	 *
	 * @param int $post_id Current post id
	 * @param string $post_name The name of the current post
	 * @param string $post_desc The description of the current post
	 *
	 * @since 0.1
	 */
	function new_forum( $my_post ) {
		global $bp, $post;

		if ( !$my_post ) {
			$post_id = $post->ID;
			$post_name = $post->post_name;
			$post_desc = $post->content;
		} else {
			$post_id = $my_post->ID;
			$post_name = $my_post->post_name;
			$post_desc = $my_post->content;
		}

		do_action( 'bpcp_' . $this->id . '_new_forum' );
		do_action( 'bpcp_new_forum', $this->id );


		if ( bp_forums_is_installed_correctly() ) {
			$forum_id = bp_forums_new_forum( array( 'forum_name' => $post_name, 'forum_desc' => $post_desc ) );
			add_post_meta( $post_id, '_bpcp_forum_id', $forum_id );
		}
	}

	/**
	 * Add a new forum post.
	 *
	 * @param string $post_text Content of post
	 * @param int $topic_id Topic of the post
	 * @param int $page Topic page
	 *
	 * @since 0.1
	 */
	function new_forum_post( $post_text, $topic_id, $page = false ) {
		global $bp, $post;
		$type = get_post_type_object( $this->id );

		if ( empty( $post_text ) )
			return false;

		if ( $post_id = bp_forums_insert_post( array( 'post_text' => $post_text, 'topic_id' => $topic_id ) ) ) {
			$topic = bp_forums_get_topic_details( $topic_id );

			$activity_action = sprintf( __( '%s posted on the forum topic %s in the %s %s:' ), bp_core_get_userlink( $bp->loggedin_user->id ), '<a href="' . get_permalink() . 'forum/topic/' . $topic->topic_slug .'/">' . attribute_escape( $topic->topic_title ) . '</a>', $type->labels->name , '<a href="' . get_permalink() . '">' . attribute_escape( $post->post_title ) . '</a>' );
			$activity_content = bp_create_excerpt( $post_text );
			$primary_link = get_permalink() . 'forum/topic/' . $topic->topic_slug . '/';

			if ( $page )
				$primary_link .= "?topic_page=" . $page;

			/* Record this in activity streams */
			if ( function_exists( 'bp_activity_add' ) ) 
				bp_activity_add( array(
					'action' => $activity_action,
					'content' => $activity_content,
					'component' => $this->id,
					'user_id' => bp_loggedin_user_id(),
					'primary_link' => "{$primary_link}#post-{$post_id}",
					'type' => 'new_forum_post',
					'item_id' => $post->ID,
					'secondary_item_id' => $post_id
				) );

			do_action( 'bpcp_' . $this->id . '_new_forum_post' );
			do_action( 'bpcp_new_forum_post', $this->id );

			return $post_id;
		}

		return false;
	}

	/**
	 * Create a new forum topic
	 *
	 * @param string $topic_title The title
	 * @param string $topic_text
	 * @param string $topic_tags
	 * @param int $forum_id
	 *
	 * @since 0.1
	 */
	function new_forum_topic( $topic_title, $topic_text, $topic_tags, $forum_id ) {
		global $bp, $post;
		$type = get_post_type_object( $this->id );


		if ( empty( $topic_title ) || empty( $topic_text ) )
			return false;


		if ( $topic_id = bp_forums_new_topic( array( 'topic_title' => $topic_title, 'topic_text' => $topic_text, 'topic_tags' => $topic_tags, 'forum_id' => $forum_id ) ) ) {
			$topic = bp_forums_get_topic_details( $topic_id );


			$activity_action = sprintf( __( '%s started the forum topic %s in the %s %s:' ), bp_core_get_userlink( $bp->loggedin_user->id ), '<a href="' . get_permalink() . 'forum/topic/' . $topic->topic_slug .'/">' . attribute_escape( $topic->topic_title ) . '</a>', $type->labels->name, '<a href="' . get_permalink() . '">' . attribute_escape( $post->title ) . '</a>' );
			$activity_content = bp_create_excerpt( $topic_text );

			/* Record this in activity streams */
			if( function_exists( 'bp_activity_add' ) )
				bp_activity_add( array(
					'action' => $activity_action,
					'content' => $activity_content,
					'primary_link' => get_permalink() . 'forum/topic/' . $topic->topic_slug . '/',
					'type' => 'new_forum_topic',
					'item_id' => $post->ID,
					'secondary_item_id' => $topic->topic_id,
					'component' => $this->id
				) );

			do_action( 'bpcp_' . $this->id . '_new_forum_topic' );
			do_action( 'bpcp_new_forum_topic', $this->id );

			return $topic;
		}

		return false;
	}

	/**
	 * Delete a forum topic.
	 *
	 * @param int $topic_id 
	 *
	 * @since 0.1
	 */
	function delete_forum_topic( $topic_id ) {
		global $bp, $post;

		if ( bp_forums_delete_topic( array( 'topic_id' => $topic_id ) ) ) {

			/* Delete the activity stream item */
			if ( function_exists( 'bp_activity_delete' ) ) {
				bp_activity_delete( array( 'item_id' => $post->ID, 'secondary_item_id' => $topic_id, 'component' => $this->id, 'type' => 'new_forum_topic' ) );
			}

			do_action( 'bpcp_' . $this->id . '_delete_forum_topic' );
			do_action( 'bpcp_delete_forum_topic', $this->id );

			return true;
		}

		return false;
	}

	/**
	 * Delete a forum post
	 *
	 * @param int $post_id
	 * @param int $topic_id
	 *
	 * @since 0.1
	 */
	function delete_forum_post( $post_id, $topic_id ) {
		global $bp, $post;

		if ( bp_forums_delete_post( array( 'post_id' => $post_id ) ) ) {
			/* Delete the activity stream item */
			if ( function_exists( 'bp_activity_delete' ) ) {
				bp_activity_delete( array( 'item_id' => $post->ID, 'secondary_item_id' => $post_id, 'component' => $this->id, 'type' => 'new_forum_post' ) );
			}

			do_action( 'bpcp_' . $this->id . '_delete_forum_post' );
			do_action( 'bpcp_delete_forum_post', $this->id );

			return true;
		}

		return false;
	}

	/**
	 * Get count of topics.
	 *
	 * @param string $type
	 *
	 * @since 0.1
	 */
	function total_public_forum_topic_count( $type = 'newest' ) {
		global $bbdb, $wpdb, $bp;

		return $wpdb->get_var( "SELECT COUNT(t.topic_id) FROM {$bbdb->topics} AS t, {$wpdb->posts} AS p WHERE p.id = t.forum_id AND p.post_status = 'public' AND t.topic_status = '0' AND t.topic_sticky != '2' " );
	}

	/**
	 * Update a given forum topic.
	 *
	 * @param $topic_id
	 * @param $topic_title
	 * @param $topic_text
	 *
	 * @since 0.1
	 */
	function update_forum_topic( $topic_id, $topic_title, $topic_text ) {
		global $bp, $post;

		$type = get_post_type_object( $this->id );

		if ( $topic = bp_forums_update_topic( array( 'topic_title' => $topic_title, 'topic_text' => $topic_text, 'topic_id' => $topic_id ) ) ) {
			/* Update the activity stream item */
			if ( function_exists( 'bp_activity_delete_by_item_id' ) )
				bp_activity_delete_by_item_id( array( 'item_id' => $post->ID, 'secondary_item_id' => $topic_id, 'component' => $this->id, 'type' => 'new_forum_topic' ) );

			$activity_action = sprintf( __( '%s started the forum topic %s in the %s %s:', 'buddypress'), bp_core_get_userlink( $topic->topic_poster ),  '<a href="' . get_permalink() . 'forum/topic/' . $topic->topic_slug .'/">' . attribute_escape( $topic->topic_title ) . '</a>', '<a href="' . get_permalink() . '">' . attribute_escape( $post->post_title ) . '</a>' );
			$activity_content = bp_create_excerpt( $topic_text );

			/* Record this in activity streams */
			if( function_exists( 'bp_activity_add' ) )
				bp_activity_add( array(
					'action' => $activity_action,
					'content' => $activity_content, 
					'primary_link' => get_permalink() . 'forum/topic/' . $topic->topic_slug . '/',
					'type' => 'new_forum_topic',
					'item_id' => (int)$post->ID,
					'user_id' => (int)$topic->topic_poster,
					'secondary_item_id' => $topic->topic_id,
					'recorded_time' => $topic->topic_time,
					'component' => $this->id
				) );


			do_action( 'bpcp_' . $this->id . '_update_forum_topic' );
			do_action( 'bpcp_update_forum_topic', $this->id );

			return $topic;
		}

		return false;
	}

	/**
	 * Update a given forum post
	 *
	 * @param int $post_id
	 * @param string $post_text
	 * @param int $topic_id
	 * @param int $page
	 *
	 * @since 0.1
	 */
	function update_forum_post( $post_id, $post_text, $topic_id, $page = false ) {
		global $bp, $post;
		$type = get_post_type_object( $this->id );

		$bbpost = bp_forums_get_post( $post_id );

		if ( $post_id = bp_forums_insert_post( array( 'post_id' => $post_id, 'post_text' => $post_text, 'post_time' => $bbpost->post_time, 'topic_id' => $topic_id, 'poster_id' => $bbpost->poster_id ) ) ) {
			$topic = bp_forums_get_topic_details( $topic_id );

			$activity_action = sprintf( __( '%s posted on the forum topic %s in the %s %s:' ), bp_core_get_userlink( $bbpost->poster_id ), '<a href="' . get_permalink() . 'forum/topic/' . $topic->topic_slug .'">' . attribute_escape( $topic->topic_title ) . '</a>', '<a href="' . get_permalink() . '">' . attribute_escape( $post->title ) . '</a>' );
			$activity_content = bp_create_excerpt( $post_text );
			$primary_link = get_permalink() . 'forum/topic/' . $topic->topic_slug . '/';

			if ( $page )
				$primary_link .= "?topic_page=" . $page;

			/* Fetch an existing entry and update if one exists. */
			if ( function_exists( 'bp_activity_get_activity_id' ) )
				$id = bp_activity_get_activity_id( array( 'user_id' => $bbpost->poster_id, 'component' => $this->id, 'type' => 'new_forum_post', 'item_id' => $post->ID, 'secondary_item_id' => $post_id ) );

			/* Update the entry in activity streams */
			bp_activity_add( array(
				'id' => $id,
				'action' => $activity_action,
				'content' => $activity_content,
				'primary_link' => $primary_link . "#post-" . $post_id,
				'type' => 'new_forum_post',
				'item_id' => (int)$post->ID,
				'user_id' => (int)$bbpost->poster_id,
				'secondary_item_id' => $post_id,
				'recorded_time' => $bbpost->post_time,
				'component' => $this->id
			) );

			do_action( 'bpcp_' . $this->id . '_update_forum_post' );
			do_action( 'bpcp_update_forum_post', $this->id );

			return $post_id;
		}

		return false;
	}

	/**
	 * Save the post's data based on $_POST contents.
	 *
	 * Heavily simplified version of the labyrinthine
	 * wp-admin/edit, post, post-new files.
	 *
	 * @since 0.1
	 */
	function save_post_data() {
		global $post;

		//The post administration API.
		$admin_path  = ABSPATH . '/wp-admin';
		require_once( $admin_path . '/includes/post.php' );

		$_POST[ 'post_content' ] = $_POST[ 'post-content' ]; //Hackiness.

		if ( $posted = ( wp_insert_post( Array(
			'post_status' => 'publish',
			'post_type' => $this->id,
			'post_author' => $_POST[ 'user_ID' ],
			'post_content' => $_POST[ 'post-content' ],
			'post_title' => $_POST[ 'post_title' ],
			'ID' => $_POST[ 'post_ID' ]
		) ) ) != 0 ) {
			bp_core_add_message( __( 'Updated succesfully.' ) );
			$postid = $_POST[ 'post_ID' ];
			$post = get_post( $postid );
		}
		else
			bp_core_add_message( __( 'Could not update.' ) );

	}
}
