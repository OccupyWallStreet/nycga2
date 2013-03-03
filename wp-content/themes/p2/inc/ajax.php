<?php
/**
 * Handle Ajax requests.
 *
 * @package P2
 */

if ( defined('DOING_AJAX') && DOING_AJAX && isset( $_REQUEST['p2ajax'] ) ) {
	add_action( 'init', array( 'P2Ajax', 'dispatch' ) );
}

class P2Ajax {
	function dispatch() {
		$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : '';

		do_action( "p2_ajax", $action );
		if ( is_callable( array( 'P2Ajax', $action ) ) )
			call_user_func( array( 'P2Ajax', $action ) );
		else
			die( '-1' );
		exit;
	}

	function get_post() {
		check_ajax_referer( 'ajaxnonce', '_inline_edit' );
		if ( !is_user_logged_in() ) {
			die( '<p>'.__( 'Error: not logged in.', 'p2' ).'</p>' );
		}
		$post_id = $_GET['post_ID'];
		$post_id = substr( $post_id, strpos( $post_id, '-' ) + 1 );
		if ( !current_user_can( 'edit_post', $post_id ) ) {
			die( '<p>'.__( 'Error: not allowed to edit post.', 'p2' ).'</p>' );
		}

		// Don't treat the post differently based on user's visual editor setting.
		// If the user has disabled the visual editor, the post_content goes through an "extra" esc_textarea().
		add_filter( 'user_can_richedit', '__return_true' );
		$post = get_post( $post_id, OBJECT, 'edit' );

		function get_tag_name( $tag ) {
			return $tag->name;
		}
		$tags = array_map( 'get_tag_name', wp_get_post_tags( $post_id ) );

		$post_format = p2_get_post_format( $post_id );

		// handle page as post_type
		if ( 'page' == $post->post_type ) {
			$post_format = '';
			$tags = '';
		}

		add_filter( 'user_can_richedit', '__return_false' );
		$post->post_content = apply_filters( 'the_editor_content', $post->post_content );

		echo json_encode( array(
			'title' => $post->post_title,
			'content' => $post->post_content,
			'post_format' => $post_format,
			'post_type' => $post->post_type,
			'tags' => $tags,
		) );
	}

	function tag_search() {
		global $wpdb;
		$term = $_GET['term'];
		if ( false !== strpos( $term, ',' ) ) {
			$term = explode( ',', $term );
			$term = $term[count( $term ) - 1];
		}
		$term = trim( $term );
		if ( strlen( $term ) < 2 )
			die(); // require 2 chars for matching

		$tags = array();
		$results = $wpdb->get_results( "SELECT name, count FROM $wpdb->term_taxonomy AS tt INNER JOIN $wpdb->terms AS t ON tt.term_id = t.term_id WHERE tt.taxonomy = 'post_tag' AND t.name LIKE ( '%". like_escape( $wpdb->escape( $term ) ) . "%' ) ORDER BY count DESC" );

		foreach ( $results as $result ) {
			$rterm = '/' . preg_quote( $term, '/' ) . '/i';
			$label = preg_replace( $rterm, "<strong>$0</strong>", $result->name ) . " ($result->count)";

			$tags[] = array(
				'label' => $label,
				'value' => $result->name,
			);
		}

		echo json_encode( $tags );
	}

	function logged_in_out() {
		check_ajax_referer( 'ajaxnonce', '_loggedin' );
		echo is_user_logged_in() ? 'logged_in' : 'not_logged_in';
	}

	function get_comment() {
		check_ajax_referer( 'ajaxnonce', '_inline_edit' );
		if ( !is_user_logged_in() ) {
			die( '<p>'.__( 'Error: not logged in.', 'p2' ).'</p>' );
		}
		$comment_id = $_GET['comment_ID'];
		$comment_id = substr( $comment_id, strpos( $comment_id, '-' ) + 1);
		$comment = get_comment($comment_id);
		echo apply_filters( 'p2_get_comment_content', $comment->comment_content, $comment_id );
	}

	function save_post() {
		check_ajax_referer( 'ajaxnonce', '_inline_edit' );
		if ( !is_user_logged_in() ) {
			die( '<p>'.__( 'Error: not logged in.', 'p2' ).'</p>' );
		}

		$post_id = $_POST['post_ID'];
		$post_id = substr( $post_id, strpos( $post_id, '-' ) + 1 );

		if ( !current_user_can( 'edit_post', $post_id )) {
			die( '<p>'.__( 'Error: not allowed to edit post.', 'p2' ).'</p>' );
		}

		$post_format = p2_get_post_format( $post_id );

		$new_post_content = $_POST['content'];

		// Add the quote citation to the content if it exists
		if ( ! empty( $_POST['citation'] ) && 'quote' == $post_format ) {
			$new_post_content = '<p>' . $new_post_content . '</p><cite>' . $_POST['citation'] . '</cite>';
		}

		$new_tags = $_POST['tags'];

		$new_post_title = isset( $_POST['title'] ) ? $_POST['title'] : '';

		if ( ! empty( $new_post_title ) )
			$post_title = $new_post_title;
		else
			$post_title = p2_title_from_content( $new_post_content );

		$post = wp_update_post( array(
			'post_title'	=> $post_title,
			'post_content'	=> $new_post_content,
			'post_modified'	=> current_time( 'mysql' ),
			'post_modified_gmt'	=> current_time( 'mysql', 1),
			'ID' => $post_id
		) );

		$tags = wp_set_post_tags( $post_id, $new_tags );

		$post = get_post( $post );
		$GLOBALS['post'] = $post;

		if ( !$post ) die( '-1' );

		if ( 'quote' == $post_format )
			$content = apply_filters( 'p2_get_quote_content', $post->post_content );
		else
			$content = apply_filters( 'the_content', $post->post_content );

		echo json_encode( array(
			'title' => $post->post_title,
			'content' => $content,
			'tags' => get_tags_with_count( $post, '', __( '<br />Tags:' , 'p2' ) . ' ', ', ', ' &nbsp;' ),
		) );
	}

	function save_comment() {
		check_ajax_referer( 'ajaxnonce', '_inline_edit' );
		if ( !is_user_logged_in() ) {
			die( '<p>'.__( 'Error: not logged in.', 'p2' ).'</p>' );
		}

		$comment_id	= $_POST['comment_ID'];
		$comment_id = substr( $comment_id, strpos( $comment_id, '-' ) + 1);
		$comment = get_comment( $comment_id );

		if ( !current_user_can( 'edit_post', $comment->comment_post_ID ) ) {
			die( '<p>'.__( 'Error: not allowed to edit this comment.', 'p2' ).'</p>' );
		}

		$comment_content = $_POST['comment_content'];

		wp_update_comment( array(
			'comment_content'	=> $comment_content,
			'comment_ID' => $comment_id
		));

		$comment = get_comment( $comment_id );
		echo apply_filters( 'comment_text', $comment->comment_content, $comment );
	}

	function new_post() {
		global $user_ID;

		if ( empty( $_POST['action'] ) || $_POST['action'] != 'new_post' ) {
		    die( '-1' );
		}
		if ( !is_user_logged_in() ) {
			die( '<p>'.__( 'Error: not logged in.', 'p2' ).'</p>' );
		}
		if ( ! ( current_user_can( 'publish_posts' ) ||
		        (get_option( 'p2_allow_users_publish' ) && $user_ID )) ) {

			die( '<p>'.__( 'Error: not allowed to post.', 'p2' ).'</p>' );
		}

		check_ajax_referer( 'ajaxnonce', '_ajax_post' );

		$user           = wp_get_current_user();
		$user_id        = $user->ID;
		$post_content   = $_POST['posttext'];
		$tags           = trim( $_POST['tags'] );
		$title          = $_POST['post_title'];
		$post_type      = isset( $_POST['post_type'] ) ? $_POST['post_type'] : 'post';

		// Strip placeholder text for tags
		if ( __( 'Tag it', 'p2' ) == $tags )
			$tags = '';

		// For empty or placeholder text, create a nice title based on content
		if ( empty( $title ) || __( 'Post Title', 'p2' ) == $title )
	    	$post_title = p2_title_from_content( $post_content );
		else
			$post_title = $title;

		$post_format = 'status';
		$accepted_post_formats = apply_filters( 'p2_accepted_post_cats', p2_get_supported_post_formats() ); // Keep 'p2_accepted_post_cats' filter for back compat (since P2 1.3.4)
		if ( in_array( $_POST['post_format'], $accepted_post_formats ) )
			$post_format = $_POST['post_format'];

		// Add the quote citation to the content if it exists
		if ( ! empty( $_POST['post_citation'] ) && 'quote' == $post_format )
			$post_content = '<p>' . $post_content . '</p><cite>' . $_POST['post_citation'] . '</cite>';

		$post_id = wp_insert_post( array(
			'post_author'   => $user_id,
			'post_title'    => $post_title,
			'post_content'  => $post_content,
			'post_type'     => 'post',
			'tags_input'    => $tags,
			'post_status'   => 'publish'
		) );

		if ( empty( $post_id ) )
			echo '0';

		set_post_format( $post_id, $post_format );
		echo $post_id;
	}

	function get_latest_posts() {
		global $post_request_ajax;

		$load_time = $_GET['load_time'];
		$frontpage = $_GET['frontpage'];
		$num_posts = 10; // max amount of posts to load
		$number_of_new_posts = 0;
		$visible_posts = isset( $_GET['vp'] ) ? (array)$_GET['vp'] : array();

		query_posts( 'showposts=' . $num_posts . '&post_status=publish' );
		ob_start();
		while ( have_posts() ) : the_post();
		    $current_user_id = get_the_author_meta( 'ID' );

			// Avoid showing the same post if it's already on the page
			if ( in_array( get_the_ID(), $visible_posts ) )
				continue;

			// Only show posts with post dates newer than current timestamp
			if ( get_gmt_from_date( get_the_time( 'Y-m-d H:i:s' ) ) <= $load_time )
				continue;

			$number_of_new_posts++;
			$post_request_ajax = true;

			p2_load_entry( false );
	    endwhile;
	   	$posts_html = ob_get_clean();

	    if ( $number_of_new_posts != 0 ) {
			nocache_headers();
	    	echo json_encode( array(
				'numberofnewposts' => $number_of_new_posts,
				'html' => $posts_html,
				'lastposttime' => gmdate( 'Y-m-d H:i:s' )
			) );
		} else {
			header("HTTP/1.1 304 Not Modified");
	    }
	}

	function new_comment() {
		if ( empty( $_POST['action'] ) || $_POST['action'] != 'new_comment' )
		    die();

		check_ajax_referer( 'ajaxnonce', '_ajax_post' );

		$comment_content = isset( $_POST['comment'] ) ? trim( $_POST['comment'] ) : null;
		$comment_post_ID = isset( $_POST['comment_post_ID'] ) ? trim( $_POST['comment_post_ID'] ) : null;

		$user = wp_get_current_user();

		if ( is_user_logged_in() ) {
			if ( empty( $user->display_name ) )
				$user->display_name = $user->user_login;
			$comment_author       = $user->display_name;
			$comment_author_email = $user->user_email;
			$comment_author_url   = $user->user_url;
			$user_ID 			  = $user->ID;
		} else {
			if ( get_option( 'comment_registration' ) ) {
			    die( '<p>'.__( 'Error: you must be logged in to post a comment.', 'p2' ).'</p>' );
			}
			$comment_author       = ( isset($_POST['author']) )  ? trim(strip_tags($_POST['author'])) : null;
			$comment_author_email = ( isset($_POST['email']) )   ? trim($_POST['email']) : null;
			$comment_author_url   = ( isset($_POST['url']) )     ? trim($_POST['url']) : null;
		}

		$comment_type = '';

		if ( get_option( 'require_name_email' ) && !$user->ID )
			if ( strlen( $comment_author_email ) < 6 || '' == $comment_author ) {
				die( '<p>'.__( 'Error: please fill the required fields (name, email).', 'p2' ).'</p>' );
			} elseif ( !is_email( $comment_author_email ) ) {
			    die( '<p>'.__( 'Error: please enter a valid email address.', 'p2' ).'</p>' );
			}

		if ( '' == $comment_content )
		    die( '<p>'.__( 'Error: Please type a comment.', 'p2' ).'</p>' );

		$comment_parent = isset( $_POST['comment_parent'] ) ? absint( $_POST['comment_parent'] ) : 0;

		$commentdata = compact( 'comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type', 'comment_parent', 'user_ID' );

		$comment_id = wp_new_comment( $commentdata );
		$comment = get_comment( $comment_id );
		if ( !$user->ID ) {
			setcookie( 'comment_author_' . COOKIEHASH, $comment->comment_author, time() + 30000000, COOKIEPATH, COOKIE_DOMAIN);
			setcookie( 'comment_author_email_' . COOKIEHASH, $comment->comment_author_email, time() + 30000000, COOKIEPATH, COOKIE_DOMAIN);
			setcookie( 'comment_author_url_' . COOKIEHASH, esc_url($comment->comment_author_url), time() + 30000000, COOKIEPATH, COOKIE_DOMAIN);
		}
		if ($comment) echo $comment_id;
		else echo __("Error: Unknown error occurred. Comment not posted.", 'p2' );
	}

	function get_latest_comments() {
		global $wpdb, $comments, $comment, $max_depth, $depth, $user_login, $user_ID, $user_identity;

		$number = 10; //max amount of comments to load
		$load_time = $_GET['load_time'];
		$lc_widget = $_GET['lcwidget'];
		$visible_posts =  isset($_GET['vp'])? (array)$_GET['vp'] : array();

		if ( get_option( 'thread_comments' ) )
			$max_depth = get_option( 'thread_comments_depth' );
		else
			$max_depth = -1;

		// Since we currently cater the same HTML to all widgets,
		// the instances without avatars will have to remove the avatar in javascript
		$avatar_size = 32;

		// Check for non-logged-in users and fetch their comment author information from comment cookies
		if ( empty( $user_ID ) && empty( $comment_author ) ) {
			$commenter = wp_get_current_commenter();

			// The name of the current comment author escaped for use in attributes
			$comment_author = $commenter['comment_author']; // Escaped by sanitize_comment_cookies()

	 		// The email address of the current comment author escaped for use in attributes
			$comment_author_email = $commenter['comment_author_email'];  // Escaped by sanitize_comment_cookies()
		}

		// Get new comments
		if ( $user_ID ) {
			$comments = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->comments WHERE (comment_approved = '1' OR ( user_id = %d AND comment_approved = '0' ) ) AND comment_date_gmt > %s ORDER BY comment_date_gmt DESC LIMIT $number", $user_ID, $load_time ) );
		} elseif ( ! empty( $comment_author ) ) {
			$comments = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->comments WHERE (comment_approved = '1' OR ( comment_author = %s AND comment_author_email = %s AND comment_approved = '0' ) ) AND comment_date_gmt > %s ORDER BY comment_date_gmt DESC LIMIT $number", $comment_author, $comment_author_email, $load_time ) );
		} else {
			$comments = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->comments WHERE comment_approved = '1' AND comment_date_gmt > %s ORDER BY comment_date_gmt DESC LIMIT $number", $load_time ) );
		}
		$number_of_new_comments = count( $comments );

	    $prepare_comments = array();
		if ($number_of_new_comments > 0) {
			foreach ($comments as $comment) {
				// Setup comment html if post is visible
				$comment_html = '';
				if ( in_array( $comment->comment_post_ID, $visible_posts ) ) {
					ob_start();
					p2_comments($comment, array( 'max_depth' => $max_depth, 'before' => ' | ' ), $depth );
					$comment_html = ob_get_clean();
				}

				// Setup widget html if widget is visible
				$comment_widget_html = '';
				if ( $lc_widget )
					$comment_widget_html = P2_Recent_Comments::single_comment_html( $comment, $avatar_size );

				$prepare_comments[] = array( "id" => $comment->comment_ID, "postID" => $comment->comment_post_ID, "commentParent" =>  $comment->comment_parent,
					"html" => $comment_html, "widgetHtml" => $comment_widget_html );
			}
			$json_data = array("numberofnewcomments" => $number_of_new_comments, "comments" => $prepare_comments, "lastcommenttime" => gmdate( 'Y-m-d H:i:s' ) );

			echo json_encode( $json_data );
		} else { // No new comments
	        header("HTTP/1.1 304 Not Modified");
		}
	}
}