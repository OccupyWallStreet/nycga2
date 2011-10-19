<?php
/*
Plugin Name: BuddyPress Rate Forum Posts
Plugin URI: http://wordpress.org/extend/plugins/buddypress-rate-forum-posts/
Description: This plugin allows rating of BuddyPress forum posts and user karma. 
Version: 1.6.6
Revision Date: October 11, 2011
Requires at least: WP 2.9.1, BuddyPress 1.2.4
Tested up to: WP 3.2.1, BuddyPress 1.5
License: GNU General Public License 2.0 (GPL) http://www.gnu.org/licenses/gpl.html
Author: Deryk Wenaus
Author URI: http://www.bluemandala.com
*/



//
// PLUGIN SETUP - perhaps we should only do this in certain places
//

// this happens when you first activate the plugin via the back end.
function rfp_activate() {
	if ( get_option( 'rfp_version' ) != '2.1' ) { // no idea why I chose this number
		update_option( 'rfp_help_text', 'Rate this post' );
		update_option( 'rfp_help_text_closed', 'Rating' );
		update_option( 'rfp_version', '2.1' );
		update_option( 'rfp_superboost', 12 );
		update_option( 'rfp_boost', 6 );
		update_option( 'rfp_diminish', -3 );
		update_option( 'rfp_hide', -6 );
		update_option( 'rfp_karma_levels', maybe_serialize( array( 7, 19, 51, 138 ) ) ); // natural log (e) for levels :)
		update_option( 'rfp_karma_label', 'Post Rating:' );
	}
}
register_activation_hook( __FILE__, 'rfp_activate' );


// insert the javascript
function rfp_init() {
	global $bp;

	//if ( bp_is_group() && bp_is_action_variable('topic') ) { // the bp 1.5 way is not compat with the old way
	if ( $bp->current_component == BP_GROUPS_SLUG && $bp->action_variables[0] == 'topic') {
		wp_enqueue_script('rfp_rating_forum_posts', WP_PLUGIN_URL.'/buddypress-rate-forum-posts/js/rating.js', array('jquery') );
	}
}
add_action('bp_init', 'rfp_init');


//set up some globals, add css, and add a blogUrl variable
function rft_header() {
	echo '<link rel="stylesheet" type="text/css" href="'.WP_PLUGIN_URL.'/buddypress-rate-forum-posts/css/rating.css" media="screen" />'."\n";	
}
add_action('wp_head', 'rft_header');
add_action('admin_head', 'rft_header');




//
// POST RATING
//


// add the rating code into the topic page
function rfp_filter_rating_link( $post_text ) {
	global $bp, $topic_template, $forum_template;
	
	// if the topic is closed, return just the rating number
	if ( 0 == (int)$forum_template->topic->topic_open && !is_super_admin() )
		return $post_text . '<div class="rfp-rate"><b>'.get_option( 'rfp_help_text_closed' ).'</b><span class="counter">' . rfp_get_post_rating_signed( $topic_template->post->post_id ) . '</span></div>';
	
	// only logged in users can rate - but you don't need to be a member of the group (too restrictive)
	if ( $bp->loggedin_user->id )
		$post_text .= rfp_get_rating_links(); 
		
	return $post_text;
}
add_filter( 'bp_get_the_topic_post_content', 'rfp_filter_rating_link', 3 ); 


// spits out html for making ratings - should test for group membership (don't use cookies)
function rfp_get_rating_links() {
	global $bp, $topic_template, $rfp;
	
	$pos_text = '&nbsp;'; // these could be LIKE or DISLIKE, then turn off thumbs in css
	$neg_text = '&nbsp;'; 
	
	$post_id = $topic_template->post->post_id;
	$rating = rfp_get_post_rating_signed( $post_id );
	$rater = $bp->loggedin_user->id;
	
    $rate_link  = '<div id="rfp-rate-'.$post_id.'" class="rfp-rate">';
    $rate_link .= '<b>'.get_option( 'rfp_help_text' ).'</b>';     // 'Rate this post' text here
    $rate_link .= '<i></i>';     //status message will go here
    $rate_link .= '<span class="counter">' . $rating . '</span>';
    $rate_link .= '<a onclick="rfp_rate_js(' . $post_id . ',\'pos\','. $rater . ');" class="pos">'.$pos_text.'</a>';
    // $rate_link .= ' | ';  // use this for word links
    $rate_link .= '<a onclick="rfp_rate_js('.$post_id . ',\'neg\','. $rater . ');" class="neg">'.$neg_text.'</a>';
    $rate_link .= '</div>';
   
    return $rate_link;
}


// the ajax call back function - where the magic happens
function rfp_save_rating() {
	if ( $post_id = $_POST['post_id'] ) {
		$direction = $_POST['direction'];
		$rater = $_POST['rater'];
				
		$user_rated = rfp_get_user_rated_post( $rater, $post_id, $direction );

		if ( !$user_rated ) {
			$meta_value = rfp_update_post_rating( $post_id, $direction );

			if ( $meta_value > 0 ) echo '+' . $meta_value;
			elseif ( $meta_value < 0 ) echo '-' . $meta_value;
			else echo '0';
			
			echo '|Thank you';
			rfp_update_user_rating_history( $rater, $post_id, $direction );
			rfp_update_post_author_karma( $post_id, $direction );
			
		} else {
			echo rfp_get_post_rating_signed( $post_id ) . '|' . $user_rated;
		}
	}
}
add_action( 'wp_ajax_rfp_rate', 'rfp_save_rating' );



	

// save the user's rating history in user_meta as post_id => direction array
// this is used to make sure the user does not rate more than once
function rfp_update_user_rating_history( $rater, $post_id, $direction ) {
	if ( !$rater || !$post_id ) 
		return false;
	$rating_history = get_user_meta( $rater, 'rfp_rating_history', true );
	$rating_history[ $post_id ] = $direction;
	update_user_meta( $rater, 'rfp_rating_history', $rating_history );
}


// see if the user has already rated this post, or if it there own post
// return false if they have not rated, otherwise return a status message
function rfp_get_user_rated_post( $rater, $post_id ) {
	if ( !$rater || !$post_id ) 
		return true;
	global $wpdb, $bbdb;
	
	do_action( 'bbpress_init' );
		
	if ( is_super_admin() )
		return false; // site admins can rate as much as they like
	
	//posters can't rate themselves.
	if ( $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$bbdb->posts} WHERE poster_id = {$rater} AND post_id = {$post_id}" ) ) )
		return 'This is your post';	
	
	//see if it's already been rated
	$rating_history = get_user_meta( $rater, 'rfp_rating_history', true );
	
	if ( $rating_history[ $post_id ] )
		return 'Already rated';
	else
		return false; // all is well, they have not rated this post previously
}

// return the post rating signed neg or positve
function rfp_get_post_rating_signed( $id ) {
	$meta_value	= rfp_get_post_rating( $id );
	
	if ( $meta_value > 0 ) return '+' . $meta_value;
	elseif ( $meta_value < 0 ) return '-' . $meta_value;
	else return '';
}

// returns the post rating
function rfp_get_post_rating( $id ) {
	if ( !$id  ) 
		return false;
	global $wpdb, $bbdb;

	do_action( 'bbpress_init' );
		
	$result = $wpdb->get_var( $wpdb->prepare( "SELECT meta_value FROM {$bbdb->meta} WHERE object_type = 'bb_post' AND meta_key = 'rfp_rating' AND object_id = {$id}" ) );
	
	return $result;
}

// save the post rating in the bb database. I created a new object_type called bb_post to label the data. kind-of overkill
function rfp_update_post_rating( $id, $direction ) {
	if ( !$id || !$direction ) 
		return false;
	global $wpdb, $rfp, $bbdb;
	
	do_action( 'bbpress_init' );
	
	if ( $direction == 'pos' ) $value = 1;
	elseif ( $direction == 'neg' ) $value = -1;

	$rating = $wpdb->get_row( $wpdb->prepare( "SELECT meta_id, meta_value FROM {$bbdb->meta} WHERE object_type = 'bb_post' AND meta_key = 'rfp_rating' AND object_id = {$id}" ) );
		
	$wpdb->query( $wpdb->prepare( "REPLACE INTO {$bbdb->meta} ( meta_id, object_type, object_id, meta_key, meta_value ) VALUES (%d, %s, %d, %s, %d )", $rating->meta_id, 'bb_post', $id, 'rfp_rating', $rating->meta_value + $value ) );
	
	return $rating->meta_value + $value;
}




// show the rating of the first post in the group forum directory and site-wide forum directory
function rfp_after_topic_title() {
	global $forum_template;
	$topic_id = $forum_template->topic->topic_id;
	$post = bb_get_first_post( $topic_id, false );
	$rating = rfp_get_post_rating_signed( $post->post_id );
	echo '<td class="rfp-topic-rating">' . $rating . '</td>';
}
add_filter( 'bp_directory_forums_extra_cell', 'rfp_after_topic_title', 3 ); 


// add a title to the rating above (in the th tag)
function rfp_after_topic_title_head() {
	echo '<th id="th-rating">Rating</th>';
}
add_filter( 'bp_directory_forums_extra_cell_head', 'rfp_after_topic_title_head', 3 ); 



// alter the look of the page by instering classes depending on rating. 
function rfp_alter_post_based_on_rating( $class ) {
	global $rfp, $topic_template;
	
	if ( !$rfp ) { // just set them up once saves queries for long topics
		$rfp = new stdClass;
		$rfp->superboost = get_option( 'rfp_superboost' );
		$rfp->boost = get_option( 'rfp_boost' );
		$rfp->diminish = get_option( 'rfp_diminish' );
		$rfp->hide = get_option( 'rfp_hide' );
	}
	
	$post_rating = rfp_get_post_rating( $topic_template->post->post_id );
	
	if ( $post_rating == 0 )
		return $class;	
	elseif ( $post_rating >= $rfp->superboost && $rfp->superboost != 0 ) 
		$class .= ' rfp-superboost';
	elseif ( $post_rating >= $rfp->boost && $rfp->boost != 0 ) 
		$class .= ' rfp-boost';
	elseif ( $post_rating <= $rfp->hide  && $rfp->hide != 0 ) 
		$class .= ' rfp-hide';
	elseif ( $post_rating <= $rfp->diminish && $rfp->diminish != 0 ) 
		$class .= ' rfp-diminish';
	
	return $class;
}
add_filter( 'bp_get_the_topic_post_css_class', 'rfp_alter_post_based_on_rating', 1, 1 );









//
// POSTER KARMA
//


// update the poster's karma points
function rfp_update_post_author_karma( $post_id, $direction ) {
	if ( !$post_id || !$direction ) 
		return false;
	global $wpdb, $bbdb;
	
	do_action( 'bbpress_init' );
	$poster_id = $wpdb->get_var( $wpdb->prepare( "SELECT poster_id FROM {$bbdb->posts} WHERE post_id = {$post_id}" ) );
	$karma = get_user_meta( $poster_id, 'rfp_post_karma', 1 );
	if ( $direction == 'pos' ) $value = 1; // abstract this
	elseif ( $direction == 'neg' ) $value = -1;
	update_user_meta( $poster_id, 'rfp_post_karma', $karma + $value );
}

// get the poster's karma from the post id
function rfp_get_post_author_karma( $post_id ) {
	if ( !$post_id ) 
		return false;
	global $wpdb, $bbdb;

	do_action( 'bbpress_init' );
	$poster_id =  $wpdb->get_var( $wpdb->prepare( "SELECT poster_id FROM {$bbdb->posts} WHERE post_id = {$post_id}" ) );
	$karma = get_user_meta( $poster_id, 'rfp_post_karma', 1 );

	return $karma;
}


// add little karma numbers next to the users name in forum topics
function rfp_filter_poster_karma( $poster_name_link ) {
	global $topic_template, $wpdb;
	
	if ( get_option( 'rfp_karma_hide' ) )
		return $poster_name_link;
	$karma = rfp_get_post_author_karma( $topic_template->post->post_id );
	$relative_karma = rfp_calculate_relative_karma( $karma, $topic_template->post->poster_id );
	$poster_name_link .= rfp_poster_karma( $relative_karma );
	
	return $poster_name_link;
}
add_filter( 'bp_get_the_topic_post_poster_name', 'rfp_filter_poster_karma', 3 );


// show the user's karma in their member page
function rfp_show_poster_karma() {
	global $bp;

	$karma = get_user_meta( $bp->displayed_user->id, 'rfp_post_karma', 1 );
	$relative_karma = rfp_calculate_relative_karma( $karma, $bp->displayed_user->id );

	if ( get_option( 'rfp_karma_hide' ) || $relative_karma == 0 || get_option( 'rfp_karma_never_minus' ) && $karma < 0 )
		return;

	echo '<div class="rfp-member-profile-karma">' . get_option( 'rfp_karma_label' ) . rfp_poster_karma( $relative_karma ) . '</div>';
}
add_action( 'bp_before_member_header_meta', 'rfp_show_poster_karma' );


// calculate relative karma based on number of posts
function rfp_calculate_relative_karma( $karma, $poster_id ) {
	global $bp, $topic_template, $wpdb, $bbdb;
		
	if (!isset($bbdb))
		do_action( 'bbpress_init' );

	if ( !$karma ) 
		return 0;
		
	$karma_calc = get_option( 'rfp_karma_calc' );
	
	if ( !$karma_calc || $karma_calc == 'total' )
		return intval( $karma );  // total karma - for quiet sites
			
	$count_posts = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT( poster_id ) FROM {$bbdb->posts} WHERE poster_id = {$poster_id}" ) ); // this calculation includes deleted posts... not sure if it shouldn't
	
	if ( $karma_calc == 'average' ) {
		return intval( $karma / ( $count_posts + 0.5 ) );  // average karma - for busy sites
	} else if ( $karma_calc == 'mixed' ) {
		return intval( $karma / ( $count_posts / 25 + 1 ) - 0.5 ); // mix average & total - for medium traffic sites  // the division number (25) acts like a reference, say someone posted 1000 posts and each post got one point they would end up with 24.4 points. increase it to increase average points.
	} else if ( $karma_calc == 'mixed2' ) {
		return intval( $karma / ( $count_posts / 50 + 1 ) - 0.5 ); // higher value version of above - default
	}

}


// returns karma html with altered color depending on karma 'level'
function rfp_poster_karma( $karma ) {
	global $rfp_karma;
	
	if (!isset( $rfp_karma ))
		$rfp_karma = maybe_unserialize( get_option( 'rfp_karma_levels' ) );

	// if karma is zero, or if karma should not be less than zero, return nada
	if ( $karma == 0 || get_option( 'rfp_karma_never_minus' ) && $karma < 0 )
		return;
		
	if ( $karma >= $rfp_karma[3] && $rfp_karma[3] != 0 ) $k = ' rfp-k4';
	elseif ( $karma >= $rfp_karma[2] && $rfp_karma[2] != 0 ) $k = ' rfp-k3';
	elseif ( $karma >= $rfp_karma[1] && $rfp_karma[1] != 0 ) $k = ' rfp-k2';
	elseif ( $karma >= $rfp_karma[0] && $rfp_karma[0] != 0 ) $k = ' rfp-k1';
	
	return "<span class='rfp-karma{$k}'>" . $karma . "p</span>";
}






// setting for the admin page
function rfp_add_admin_menu() {
	global $bp;
	if ( !is_super_admin() )
		return false;
	require ( dirname( __FILE__ ) . '/admin.php' );
	add_submenu_page( 'bp-general-settings', 'Rate Forum Posts', 'Rate Forum Posts', 'manage_options', 'rfp_admin', 'rfp_admin' );
}
add_action( is_multisite() ? 'network_admin_menu' : 'admin_menu', 'rfp_add_admin_menu', 20 );


/*
Thanks to Intense Debate for their thumb graphics and good layout. 
*/


?>