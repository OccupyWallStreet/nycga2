<?php
if ( !defined( 'DOING_AJAX' ) )
	define( 'DOING_AJAX', true);
@header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ));

class PulsePressAjax {
	function dispatch() {
		$action = isset( $_REQUEST['action'] )? $_REQUEST['action'] : '';
		if(FORCE_SSL_ADMIN)
			add_action( 'wp_ajax_'.$action, $action );
		
		do_action( "pulse_press_ajax", $action );
		
		if ( is_callable( array( 'PulsePressAjax', $action ) ) )
			call_user_func( array( 'PulsePressAjax', $action ) );
		else
			die( '-1' );
		exit;
		
	}
	
	function get_post() {
		check_ajax_referer( 'ajaxnonce', '_inline_edit' );
		if ( !is_user_logged_in() ) {
			die( '<p>'.__( 'Error: not logged in.', 'pulse_press' ).'</p>' );
		}
		$post_id = $_GET['post_ID'];
		$post_id = substr( $post_id, strpos( $post_id, '-' ) + 1 );
		if ( !current_user_can( 'edit_post', $post_id ) ) {
			die( '<p>'.__( 'Error: not allowed to edit post.', 'pulse_press' ).'</p>' );
		}
		$post = get_post( $post_id );

		function get_tag_name( $tag ) {
			return $tag->name;
		}
		$tags = array_map( 'get_tag_name', wp_get_post_tags( $post_id ) );

		$categories = get_the_category( $post_id );
		$category_slug = ( isset( $categories[0] ) ) ? $categories[0]->slug : '';

		echo json_encode( array(
			"title" => $post->post_title,
			"content" => $post->post_content,
			"type" => $category_slug,
			"tags" => $tags,
		) );
	}
	
	function tag_search() {
		global $wpdb;
		$term = $_GET['q'];
		if ( false !== strpos( $term, ',' ) ) {
			$term = explode( ',', $term );
			$term = $term[count( $term ) - 1];
		}
		$term = trim( $term );
		if ( strlen( $term ) < 2 )
			die(); // require 2 chars for matching
		$results = $wpdb->get_col( "SELECT t.name FROM $wpdb->term_taxonomy AS tt INNER JOIN $wpdb->terms AS t ON tt.term_id = t.term_id WHERE tt.taxonomy = 'post_tag' AND t.name LIKE ( '%". like_escape( $wpdb->escape( $term ) ) . "%' )" );
		echo join( $results, "\n" );
	}

	function logged_in_out() {
			check_ajax_referer( 'ajaxnonce', '_loggedin' );
			echo is_user_logged_in()? 'logged_in' : 'not_logged_in';
	}
	
	function get_comment() {
		check_ajax_referer( 'ajaxnonce', '_inline_edit' );
		if ( !is_user_logged_in() ) {
			die( '<p>'.__( 'Error: not logged in.', 'pulse_press' ).'</p>' );
		}
		$comment_id = $_GET['comment_ID'];
		$comment_id = substr( $comment_id, strpos( $comment_id, '-' ) + 1);
		$comment = get_comment($comment_id);
		echo $comment->comment_content;
	}

	function save_post() {
		check_ajax_referer( 'ajaxnonce', '_inline_edit' );
		if ( !is_user_logged_in() ) {
			die( '<p>'.__( 'Error: not logged in.', 'pulse_press' ).'</p>' );
		}

		$post_id = $_POST['post_ID'];
		$post_id = substr( $post_id, strpos( $post_id, '-' ) + 1 );

		if ( !current_user_can( 'edit_post', $post_id )) {
			die( '<p>'.__( 'Error: not allowed to edit post.', 'pulse_press' ).'</p>' );
		}

		$categories = get_the_category( $post_id );
		$category_slug = ( isset( $categories[0] ) ) ? $categories[0]->slug : '';
		
		$new_post_content = $_POST['content'];
	
		/* Add the quote citation to the content if it exists */
		if ( !empty( $_POST['citation'] ) && 'quote' == $category_slug ) {
			$new_post_content = '<p>' . $new_post_content . '</p><cite>' . $_POST['citation'] . '</cite>';
		}

		$new_tags = $_POST['tags'];

		$new_post_title = isset( $_POST['title'] ) ? $_POST['title'] : '';
		if ( !empty( $new_post_title ) ) {
			$post_title = $new_post_title;
		} else {
			$post_title = pulse_press_title_from_content( $new_post_content );
		}

		$post = wp_update_post( array(
			'post_title'	=> $post_title,
			'post_content'	=> $new_post_content,
			'post_modified'	=> current_time( 'mysql' ),
			'post_modified_gmt'	=> current_time( 'mysql', 1),
			'ID' => $post_id
		));

		$tags = wp_set_post_tags( $post_id, $new_tags );
		
		$post = get_post( $post );

		if ( !$post ) die( '-1' );

		if ( 'quote' == $category_slug ) {
			$content = apply_filters( 'pulse_press_get_quote_content', $post->post_content );
		} else {
			$content = apply_filters( 'the_content', $post->post_content );
		}

		echo json_encode( array(
			"title" => $post->post_title,
			"content" => $content,
			"tags" => pulse_press_get_tags_with_count( $post, '', __( '<br />Tags:' , 'pulse_press' ) . ' ', ', ', ' &nbsp;' ),
		) );
		
	}

	function save_comment() {
		check_ajax_referer( 'ajaxnonce', '_inline_edit' );
		if ( !is_user_logged_in() ) {
			die( '<p>'.__( 'Error: not logged in.', 'pulse_press' ).'</p>' );
		}

		$comment_id	= $_POST['comment_ID'];
		$comment_id = substr( $comment_id, strpos( $comment_id, '-' ) + 1);
		$comment = get_comment( $comment_id );

		if ( !current_user_can( 'edit_post', $comment->comment_post_ID ) ) {
			die( '<p>'.__( 'Error: not allowed to edit this comment.', 'pulse_press' ).'</p>' );
		}

		$comment_content = $_POST['comment_content'];

		$comment = wp_update_comment( array(
			'comment_content'	=> $comment_content,
			'comment_ID' => $comment_id
		));

		$comment = get_comment( $comment_id );
		echo apply_filters( 'comment_text', $comment->comment_content );
	}
	
	function new_post() {
		global $user_ID; 
		
		if ( 'POST' != $_SERVER['REQUEST_METHOD'] || empty( $_POST['action'] ) || $_POST['action'] != 'new_post' ) {
		    die( '-1' );
		}
		if ( !is_user_logged_in() ) {
			die( '<p>'.__( 'Error: not logged in.', 'pulse_press' ).'</p>' );
		}
		if ( ! ( current_user_can( 'publish_posts' ) || 
		        (pulse_press_get_option( 'allow_users_publish' ) && $user_ID )) ) {
		        	
			die( '<p>'.__( 'Error: not allowed to post.', 'pulse_press' ).'</p>' );
		}
		check_ajax_referer( 'ajaxnonce', '_ajax_post' );
		$user = wp_get_current_user();
		$user_id		= $user->ID;
		$post_content	= $_POST['posttext'];
		$tags			= trim( $_POST['tags'] );
		$title = $_POST['post_title'];

		// Strip placeholder text for tags
		if ( __( 'Tag it', 'pulse_press' ) == $tags )
			$tags = '';

		if ( empty( $title ) || __( 'Post Title', 'pulse_press' ) == $title )
			// For empty or placeholder text, create a nice title based on content
	    	$post_title = pulse_press_title_from_content( $post_content );
		else
			$post_title = $title;
			
		require_once ( ABSPATH . '/wp-admin/includes/taxonomy.php' );
		require_once ( ABSPATH . WPINC . '/category.php' );
		
		$accepted_post_cats = apply_filters( 'pulse_press_accepted_post_cats', array( 'post', 'quote', 'status', 'link' ) );
		$post_cat = ( in_array( $_POST['post_cat'], $accepted_post_cats ) ) ? $_POST['post_cat'] : 'post';
		
		if ( !category_exists( $post_cat ) )
			wp_insert_category( array( 'cat_name' => $post_cat ) );
		
		$post_cat = get_category_by_slug( $post_cat );
		
		/* Add the quote citation to the content if it exists */
		if ( !empty( $_POST['post_citation'] ) && 'quote' == $post_cat->slug ) {
			$post_content = '<p>' . $post_content . '</p><cite>' . $_POST['post_citation'] . '</cite>';
		}
		
		$post_id = wp_insert_post( array(
			'post_author'	=> $user_id,
			'post_title'	=> $post_title,
			'post_content'	=> $post_content,
			'post_type'		=> $post_type,
			'post_category' => array( $post_cat->cat_ID ),
			'tags_input'	=> $tags,
			'post_status'	=> 'publish'
		) );
		
		if( pulse_press_get_option( 'show_anonymous' ) && $_POST['anonymous'] == 1): // anonymous posting 
			add_post_meta($post_id, 'anonymous', 1, true);
		endif;
		
		
		echo $post_id ? $post_id : '0';
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
			require_once( dirname( dirname( __FILE__ ) ) . '/entry.php' );
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
		if ( 'POST' != $_SERVER['REQUEST_METHOD'] || empty( $_POST['action'] ) || $_POST['action'] != 'new_comment' )
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
			    die( '<p>'.__( 'Error: you must be logged in to post a comment.', 'pulse_press' ).'</p>' );
			}
			$comment_author       = ( isset($_POST['author']) )  ? trim(strip_tags($_POST['author'])) : null;
			$comment_author_email = ( isset($_POST['email']) )   ? trim($_POST['email']) : null;
			$comment_author_url   = ( isset($_POST['url']) )     ? trim($_POST['url']) : null;
		}

		$comment_type = '';

		if ( get_option( 'require_name_email' ) && !$user->ID )
			if ( strlen( $comment_author_email ) < 6 || '' == $comment_author ) {
				die( '<p>'.__( 'Error: please fill the required fields (name, email).', 'pulse_press' ).'</p>' );
			} elseif ( !is_email( $comment_author_email ) ) {
			    die( '<p>'.__( 'Error: please enter a valid email address.', 'pulse_press' ).'</p>' );
			}

		if ( '' == $comment_content )
		    die( '<p>'.__( 'Error: Please type a comment.', 'pulse_press' ).'</p>' );

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
		else echo __("Error: Unknown error occurred. Comment not posted.", 'pulse_press' );
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
		
		//get new comments
		if ($user_ID) {
			$comments = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->comments WHERE (comment_approved = '1' OR ( user_id = %d AND comment_approved = '0' ))  AND comment_date_gmt > %s ORDER BY comment_date_gmt DESC LIMIT $number", $user_ID, $load_time));
		} else if ( empty($comment_author) ) {
			$comments = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_approved = '1' AND comment_date_gmt > %s ORDER BY comment_date_gmt DESC LIMIT $number", $load_time));
		} else {
			$comments = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->comments WHERE (comment_approved = '1' OR ( comment_author = %s AND comment_author_email = %s AND comment_approved = '0' ) ) AND comment_date_gmt > %s ORDER BY comment_date_gmt DESC LIMIT $number", $comment_author, $comment_author_email, $load_time));
		}
		$number_of_new_comments = count($comments);

	    $prepare_comments = array();
		if ($number_of_new_comments > 0) {
			foreach ($comments as $comment) {
				// Setup comment html if post is visible
				$comment_html = '';
				if ( in_array( $comment->comment_post_ID, $visible_posts ) )
					$comment_html = pulse_press_comments($comment, array( 'max_depth' => $max_depth, 'before' => ' | ' ), $depth, false);

				// Setup widget html if widget is visible
				$comment_widget_html = '';
				if ( $lc_widget )
					$comment_widget_html = PulsePress_Recent_Comments::single_comment_html( $comment, $avatar_size );

				$prepare_comments[] = array( "id" => $comment->comment_ID, "postID" => $comment->comment_post_ID, "commentParent" =>  $comment->comment_parent,
					"html" => $comment_html, "widgetHtml" => $comment_widget_html );
			}
			$json_data = array("numberofnewcomments" => $number_of_new_comments, "comments" => $prepare_comments, "lastcommenttime" => gmdate( 'Y-m-d H:i:s' ) );

			echo json_encode( $json_data );
			
		} else { // No new comments
	        header("HTTP/1.1 304 Not Modified");
		}
	}
	
	function get_latest_votes() {
		$visible_posts =  isset($_GET['vp'])? (array)$_GET['vp'] : array();
		
		$loaded = strtotime( $_GET['load_time'] ); 
		$updated_date = pulse_press_get_option( 'votes_updated' );
		$updated = strtotime( $updated_date );
		
		/* todo: only run the update if there is any new votes */
		if($loaded < $updated):
			$changed_posts = array();
			$post_count_data = pulse_press_total_posts_votes($visible_posts);
			foreach($post_count_data as $data):
				$changed_posts[] = $data->post_id;
			endforeach;
			
			$zero_posts = array_diff($visible_posts, $changed_posts);
			
			foreach($zero_posts as $id):
				$post_zero_data[] = array( "post_id"=>$id, "count"=>0, 'total'=>0 );
			endforeach;
			
			if( is_array($post_zero_data) )
				$updated_data = array_merge($post_count_data, $post_zero_data);
			else
				$updated_data = $post_count_data;
			
			echo json_encode( array("lastVotesUpdate"=> $updated_date , "votes"=>$updated_data ) );
		else : // No new comments
	        header("HTTP/1.1 304 Not Modified");
		endif;
	}
	function compare_votes_data($a,$b){
		if($a->post_id == $b->post_id)
			return array( "post_id"=>$a->post_id, "count"=>$a->count);
		else
			return array( "post_id"=>$b->post_id, "count"=>$a->count);
			
	
	}
	
	function vote(){
	
		if(pulse_press_voting_init("bypass"))
			echo "voted";
	}	
	function votedown(){
	
		if(pulse_press_voting_init("bypass"))
			echo "voted";
	}	
	function star(){
	
		if(pulse_press_star_init("bypass"))
			echo "star";
	}
	
	
}