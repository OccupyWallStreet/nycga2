<?php 

function pulse_press_install() {
	
	global $wpdb, $pulse_press_options;
	if(PULSEPRESS_DB_VERSION > pulse_press_get_option( 'db_version') ):
		$pulse_press_options = pulse_press_update_settings_to_new_settings();
		$pulse_press_db_table = PULSEPRESS_DB_TABLE;
				
		$sql = "CREATE TABLE " . $pulse_press_db_table . " (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				post_id bigint(11) DEFAULT '0' NOT NULL,
				user_id tinytext NOT NULL,
				counter bigint(11) DEFAULT '1' NOT NULL,
				date TIMESTAMP NOT NULL,
				date_gmt DATETIME  DEFAULT '0000-00-00 00:00:00' NOT NULL,
				type VARCHAR(64) NOT NULL,
				UNIQUE KEY id (id)
				);";
		
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		
		pulse_press_update_option( 'db_version', PULSEPRESS_DB_VERSION);
		
		$date = pulse_press_get_gmt_time();
		pulse_press_update_option( 'votes_updated', $date);
		
		pulse_press_update_custom_field_from_table();
		
	endif;
	// this is just for testing shoule be take out later
	
}
if( is_admin() )  // this runs when you active the theme 
	pulse_press_install();

if(isset($_GET['update_custom_field_table']))
	pulse_press_update_custom_field_from_table();

// delete table once you switch themes
add_action("switch_theme","pulse_press_delete_tables_and_options");
function pulse_press_delete_tables_and_options()
{
	
	global $wpdb;
	$options = pulse_press_options();
	foreach($options as $option):
		delete_option( 'pulse_press_'.$option );
	endforeach;
	delete_option( 'pulse_press_rewrites_flushed' );
	delete_option( 'pulse_press_db_version' );
	
	delete_option( 'pulse_press_show_titles' );
	delete_option( 'pulse_press_prompt_text' );
	delete_option( 'pulse_press_votes_updated' );
	
	delete_option( 'pulse_press_options'); // important to drop this

	$pulse_press_db_table = PULSEPRESS_DB_TABLE;
   	$wpdb->query("DROP TABLE IF EXISTS $pulse_press_db_table");
	// delete the different option
	
	$all_posts = get_posts('posts_per_page=-1&post_type=post&post_status=any');
	foreach( $all_posts as $postinfo) {
		delete_post_meta($postinfo->ID, 'updates_votes');
		delete_post_meta($postinfo->ID, 'total_votes');
	}
	

}

/** 
 * API FOR working with the new table 
 *****************************************************/
// just add and delete rows 
/**
 * Votes 
 *************************************************************/
function pulse_press_vote($post_id) {
	// store the value in custom field 
	
	$votes = pulse_press_sum_votes($post_id) + 1;
	$total = pulse_press_total_votes($post_id) + 1;
	// save the number of votes to better get popular votes 
	add_post_meta($post_id, 'updates_votes', $votes, true) or update_post_meta($post_id, 'updates_votes', $votes);
	add_post_meta($post_id, 'total_votes', $total, true) or update_post_meta($post_id, 'total_votes', $total);
	// for knowing when to update this 
	$date = pulse_press_get_gmt_time();
	pulse_press_update_option( 'votes_updated',$date);
	return pulse_press_add_user_post_meta($post_id,'vote',1);
	
}

function pulse_press_vote_down($post_id) {
	// store the value in custom field 
	
	$votes = pulse_press_sum_votes($post_id) - 1;
	$total = pulse_press_total_votes($post_id) + 1;
	// save the number of votes to better get popular votes 
	add_post_meta($post_id, 'updates_votes', $votes, true) or update_post_meta($post_id, 'updates_votes', $votes);
	add_post_meta($post_id, 'total_votes', $total, true) or update_post_meta($post_id, 'total_votes', $total);
	// for knowing when to update this 
	$date = pulse_press_get_gmt_time();
	pulse_press_update_option( 'votes_updated',$date);
	return pulse_press_add_user_post_meta($post_id,'vote',-1);
	
}
function pulse_press_delete_vote($post_id) {
	// store the value in custom field 
	
	$votes = pulse_press_sum_votes($post_id) - 1;
	$total = pulse_press_total_votes($post_id) - 1;
	// save the number of votes to better get popular votes 
	add_post_meta($post_id, 'updates_votes', $votes, true) or update_post_meta($post_id, 'updates_votes', $votes);
	add_post_meta($post_id, 'total_votes', $total, true) or update_post_meta($post_id, 'total_votes', $total);
	
	
	// for knowing when to update this 
	$date = pulse_press_get_gmt_time();
	pulse_press_update_option( 'votes_updated',$date);
	
	return pulse_press_delete_user_post_meta($post_id,'vote');
}

function pulse_press_is_vote($post_id) {
	return pulse_press_get_user_post_meta_counter($post_id,'vote');
}

function pulse_press_sum_votes($post_id) {
	
	return pulse_press_get_sum_meta_by_post("vote",$post_id);
}

function pulse_press_total_votes($post_id) {
	
	return pulse_press_get_total_meta_by_post("vote",$post_id);
}

function pulse_press_get_sum_votes_by_user($user_id){
	return pulse_press_get_sum_meta_by_user('vote',$user_id);
}
function pulse_press_get_popular_vote() {
	return pulse_press_get_popular_posts_meta('vote');
}
function pulse_press_total_posts_votes($post_ids)
{
	// make sure you are dealing with numbers
	if(is_array($post_ids)):
		foreach($post_ids as $item):
			$post_id_array[] = intval($item);
		endforeach;
		$post_ids = implode(", ",$post_id_array);
	else:
		$post_ids = intval($post_ids);
	endif;
	
	
	return pulse_press_get_total_posts_meta($post_ids,'vote');
}
function pulse_press_get_updates_since_vote($date) {
	
	return pulse_press_get_updates_since_post_meta($date,'vote');
}

function pulse_press_get_total_votes_by_user($user_id) {
	
	return pulse_press_get_total_meta_by_user('vote',$user_id);
}
/**
 * Star 
 *************************************************************/

function pulse_press_add_star($post_id) {
	return pulse_press_add_user_post_meta($post_id,"star");
}

function pulse_press_delete_star($post_id) {
	return pulse_press_delete_user_post_meta($post_id,"star");
}

function pulse_press_is_star($post_id) {
	return pulse_press_get_user_post_meta($post_id,"star");
}
function pulse_press_get_user_starred_post_meta()
{
	return  pulse_press_get_all_user_post_meta("star");
}
/**
 * API HELPER 
 *************************************************************/
function pulse_press_get_user_post_meta($post_id,$type) {

	global $wpdb,$current_user;
	wp_get_current_user();
	
	return $wpdb->get_var($wpdb->prepare("SELECT id FROM ".PULSEPRESS_DB_TABLE." WHERE post_id = %d AND user_id = %d AND type ='%s';", $post_id,$current_user->ID,$type ));
}


function pulse_press_get_user_post_meta_counter($post_id,$type) {

	global $wpdb,$current_user;
	wp_get_current_user();
	
	return $wpdb->get_var($wpdb->prepare("SELECT counter FROM ".PULSEPRESS_DB_TABLE." WHERE post_id = %d AND user_id = %d AND type ='%s';", $post_id,$current_user->ID,$type ));
}

function pulse_press_get_user_from_meta_post_id($type,$post_id) {
	global $wpdb;
	
	return $wpdb->get_col($wpdb->prepare("SELECT user_id FROM ".PULSEPRESS_DB_TABLE." WHERE post_id = %d AND type ='%s';",$post_id,$type ));
}

/* return the sum of the counter for */
function pulse_press_get_total_meta_by_post($type,$post_id) {
	global $wpdb;
	
	return $wpdb->get_var($wpdb->prepare("SELECT COUNT(*)  FROM ".PULSEPRESS_DB_TABLE." WHERE post_id = %d AND type ='%s';",$post_id,$type ));
}

function pulse_press_get_sum_meta_by_post($type,$post_id) {
	global $wpdb;
	
	return $wpdb->get_var($wpdb->prepare("SELECT SUM(counter)  FROM ".PULSEPRESS_DB_TABLE." WHERE post_id = %d AND type ='%s';",$post_id,$type ));
}




/* returns all the post_id that a user voted on or stared */
function pulse_press_get_all_user_post_meta($type) {

	global $wpdb,$current_user;
	wp_get_current_user();
	
	return $wpdb->get_col($wpdb->prepare("SELECT post_id FROM ".PULSEPRESS_DB_TABLE." WHERE user_id = %d AND type ='%s';",$current_user->ID,$type ));
}

/* returns back the SUM of the votes a user made */ 
function pulse_press_get_sum_meta_by_user($type,$user_id) {
	
	global $wpdb;

	return $wpdb->get_var($wpdb->prepare("SELECT SUM(counter) FROM ".PULSEPRESS_DB_TABLE." WHERE user_id = %s AND  type ='%s';", $user_id,$type ));

}
/* returns back the total number of votes a user made */
function pulse_press_get_total_meta_by_user($type,$user_id) {
	global $wpdb;
	
	return $wpdb->get_var($wpdb->prepare("SELECT COUNT(*)  FROM ".PULSEPRESS_DB_TABLE." WHERE user_id = %d AND type ='%s';",$user_id,$type ));
}



function pulse_press_add_user_post_meta($post_id,$type,$count=1) {

	global $wpdb,$current_user;
	wp_get_current_user();
	$date = pulse_press_get_gmt_time();
	$data = array( 
			'post_id' 	=> $post_id, 
			'type' 		=> $type,
			'counter'	=> $count,
			'user_id'	=> $current_user->ID,
			'date_gmt'  => $date,
			 );
	$GLOBALS[ 'wp_log' ][ 'pulsepress' ][] = 'added user post meta';
	$result = $wpdb->insert( PULSEPRESS_DB_TABLE,$data , array( '%d', '%s', '%d', '%s') );
	
	return $result;
	
	

}

function pulse_press_delete_user_post_meta($post_id,$type){
	global $wpdb,$current_user;
	wp_get_current_user();
	
	return $wpdb->query( $wpdb->prepare( "DELETE FROM ".PULSEPRESS_DB_TABLE." WHERE post_id = %d AND user_id = %d AND type ='%s'", $post_id,$current_user->ID,$type) );
	
}
function pulse_press_get_popular_posts_meta($type) {

	global $wpdb,$current_user;
	wp_get_current_user();
	
	return $wpdb->get_results($wpdb->prepare("SELECT post_id, COUNT(post_id) as count FROM ".PULSEPRESS_DB_TABLE." WHERE type ='%s' GROUP BY post_id ORDER BY count DESC;", $type ));
}
/* This gives you back all the items in the table together */
function pulse_press_get_sum_posts_meta($post_ids,$type) {
	global $wpdb;
	return $wpdb->get_results($wpdb->prepare("SELECT post_id, COUNT(*) as count FROM ".PULSEPRESS_DB_TABLE." WHERE post_id IN (".$post_ids.") AND type ='%s' GROUP BY post_id;", $type));
	
}
/* this returns a list of summed up items */
function pulse_press_get_total_posts_meta($post_ids,$type) {
	global $wpdb;
	return $wpdb->get_results($wpdb->prepare("SELECT post_id, SUM(counter) as count, COUNT(*) as total FROM ".PULSEPRESS_DB_TABLE." WHERE post_id IN (".$post_ids.") AND type ='%s' GROUP BY post_id;", $type));
}
function pulse_press_get_updates_since_post_meta($date,$type) {
	global $wpdb;
	return $wpdb->get_var($wpdb->prepare("SELECT date_gmt FROM ".PULSEPRESS_DB_TABLE." WHERE date_gmt > %s  AND type ='%s' ORDER BY date_gmt;",$date, $type));

}




/* helper functions */
function pulse_press_get_gmt_time(){

	$default_timezone = date_default_timezone_get();
		date_default_timezone_set('GMT');
	$date = date("Y-m-d H:i:s");
	date_default_timezone_set($default_timezone);
	return $date;
}


// get the earliest post 
function get_earliest_post_date()
{
	global $wpdb;
	
	return $wpdb->get_var("SELECT post_date FROM $wpdb->posts WHERE `post_type` = 'post' AND `post_status` ='publish' ORDER BY `post_date` ASC LIMIT 0 , 1");
	
	
}
// get the last post 
function pulse_press_get_last_post_date()
{
	global $wpdb;
	return $wpdb->get_var("SELECT post_date FROM $wpdb->posts WHERE post_type = 'post' AND post_status ='publish' ORDER BY post_date DESC LIMIT 0 , 1");
	
}

/* Options */
/**
 * pulse_press_update_settings_to_new_settings function.
 *  useful for backwards compatibility
 * @access public
 * @return void
 */
function pulse_press_update_settings_to_new_settings() {
	global $pulse_press_options;
	
	
	if(pulse_press_get_option( 'db_version')): // for backwards compatibility
	$options_name = array(
			'allow_users_publish',
			'show_categories',
			'hide_threads',
			'show_reply',
			'show_voting',
			'voting_type',
			'show_unpopular',
			'show_most_voted_on',
			'show_vote_breakdown',
			'show_anonymous',
			'show_fav',
			'show_tagging',
			'allow_fileupload',
			'show_twitter',
			'bitly_user',
			'bitly_api',
			'hide_sidebar',
			'prompt_text',
			'vote_text',
			'vote_up_text',
			'vote_down_text',
			'popular_text',
			'unpopular_text',
			'most_voted_on_text',
			'star_text',
			'remove_frontend_post',
			'rewrites_flushed',
			'db_version',
			'show_titles',
			'prompt_text',
			'votes_updated',
			'limit_comments',
		);
	// if( !is_array( $pulse_press_options) ):
	foreach($options_name as $option):
		$pulse_press_options[$option] = get_option("pulse_press_".$option);
		delete_option("pulse_press_".$option); // delete the options as well... next step
	endforeach;
	else:
		$pulse_press_options = array(
			'allow_users_publish'  	=> 0,
			'show_categories'  		=> 0,
			'hide_threads'  		=> 0,
			'show_reply' 			=> 0,
			'show_voting' 			=> 0,
			'voting_type'  			=> 'one',
			'show_unpopular'  		=> 0,
			'show_most_voted_on'  	=> 0,
			'show_vote_breakdown'  	=> 0,
			'show_anonymous'  		=> 0,
			'show_fav'  			=> 0,
			'show_tagging'  		=> 0,
			'allow_fileupload'  	=> 0,
			'show_twitter' 			=> 0,
			'bitly_user' 			=> '',
			'bitly_api' 			=> '',
			'hide_sidebar' 			=> 0,
			'prompt_text' 			=> '',
			'vote_text'	 			=> '',
			'vote_up_text' 			=> '',
			'vote_down_text' 		=> '',
			'popular_text'  		=> '',
			'unpopular_text' 		=> '',
			'limit_comments' 		=> 0,
			'most_voted_on_text'	=> '',
			'star_text' 			=> '',
			'remove_frontend_post' 	=> '',
			'rewrites_flushed'		=> false,
			'db_version'			=> 6,
			'prompt_text' 			=> '',
			'votes_updated' 		=> ''
		);

	endif;
	update_option('pulse_press_options', $pulse_press_options );
	
	// endif;
	return $pulse_press_options;

}

function pulse_press_update_option( $name, $value )
{
	global $pulse_press_options;
	
	$pulse_press_options[$name] = $value;
	
	return update_option( 'pulse_press_options' , $pulse_press_options );
	
}

function pulse_press_get_option( $name )
{
	global $pulse_press_options;
	if( is_array($pulse_press_options) )
		return $pulse_press_options[$name];
	else{
		$pulse_press_options = get_option( 'pulse_press_options' );
		return $pulse_press_options[$name];
	}
}


