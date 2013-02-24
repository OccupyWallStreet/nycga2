<?php
class EM_Location_Post_Admin{
	function init(){
		global $pagenow;
		if($pagenow == 'post.php' || $pagenow == 'post-new.php' ){ //only needed if editing post
			add_action('admin_head', array('EM_Location_Post_Admin','admin_head'));
		}
		//Meta Boxes
		add_action('add_meta_boxes', array('EM_Location_Post_Admin','meta_boxes'));
		//Save/Edit actions
		add_action('save_post',array('EM_Location_Post_Admin','save_post'));
		add_action('before_delete_post',array('EM_Location_Post_Admin','before_delete_post'),10,1);
		add_action('trashed_post',array('EM_Location_Post_Admin','trashed_post'),10,1);
		add_action('untrash_post',array('EM_Location_Post_Admin','untrash_post'),10,1);
		add_action('untrashed_post',array('EM_Location_Post_Admin','untrashed_post'),10,1);
		//Notices
		add_action('admin_notices',array('EM_Location_Post_Admin','admin_notices'));
		add_action('post_updated_messages',array('EM_Location_Post_Admin','admin_notices_filter'),1,1);
	}
	
	function admin_head(){
		global $post, $EM_Location;
		if( !empty($post) && $post->post_type == EM_POST_TYPE_LOCATION ){
			$EM_Location = em_get_location($post);
		}
	}
	
	function admin_notices(){
		//When editing
		global $post, $EM_Notices;
		if( !empty($post) && $post->post_type == EM_POST_TYPE_LOCATION){
		}
	}
	
	function admin_notices_filter($messages){
		//When editing
		global $post, $EM_Notices;
		if( $post->post_type == EM_POST_TYPE_LOCATION ){
			if ( $EM_Notices->count_errors() > 0 ) {
				unset($_GET['message']);
			}
		}
		return $messages;
	}
	
	function save_post($post_id){
		global $wpdb, $EM_Location, $EM_Notices;
		$saving_status = !in_array(get_post_status($post_id), array('trash','auto-draft')) && !defined('DOING_AUTOSAVE');
		$is_post_type = get_post_type($post_id) == EM_POST_TYPE_LOCATION;
		if(!defined('UNTRASHING_'.$post_id) && $is_post_type && $saving_status){
			if( !empty($_REQUEST['_emnonce']) && wp_verify_nonce($_REQUEST['_emnonce'], 'edit_location')){
				$EM_Location = em_get_location($post_id, 'post_id');
				do_action('em_location_save_pre', $EM_Location);
				$get_meta = $EM_Location->get_post_meta();
				$save_meta = $EM_Location->save_meta();
				//Handle Errors by making post draft
				if( !$get_meta || !$save_meta ){
					$EM_Location->set_status(null, true);
					$EM_Notices->add_error( '<strong>'.sprintf(__('Your %s details are incorrect and cannot be published, please correct these errors first:','dbem'),__('location','dbem')).'</strong>', true); //Always seems to redirect, so we make it static
					$EM_Notices->add_error($EM_Location->get_errors(), true); //Always seems to redirect, so we make it static
					apply_filters('em_location_save', false , $EM_Location);
				}else{
					apply_filters('em_location_save', true , $EM_Location);
				}
			}else{
				//do a quick and dirty update
				$EM_Location = new EM_Location($post_id, 'post_id');
				do_action('em_location_save_pre', $EM_Location);
				//check for existence of index
				$loc_truly_exists = $wpdb->get_var('SELECT location_id FROM '.EM_LOCATIONS_TABLE." WHERE location_id={$EM_Location->location_id}") == $EM_Location->location_id;
				if(empty($EM_Location->location_id) || !$loc_truly_exists){ $EM_Location->save_meta(); }
				//continue
				$location_status = ($EM_Location->is_published()) ? 1:0;
				$wpdb->query("UPDATE ".EM_LOCATIONS_TABLE." SET location_name='{$EM_Location->location_name}', location_slug='{$EM_Location->location_slug}', location_private='{$EM_Location->location_private}',location_status={$location_status} WHERE location_id='{$EM_Location->location_id}'");
				apply_filters('em_location_save', true , $EM_Location);
			}
		}
	}

	function before_delete_post($post_id){
		if(get_post_type($post_id) == EM_POST_TYPE_LOCATION){
			$EM_Location = em_get_location($post_id,'post_id');
			$EM_Location->delete_meta();
		}
	}
	
	function trashed_post($post_id){
		if(get_post_type($post_id) == EM_POST_TYPE_LOCATION){
			global $EM_Notices;
			$EM_Location = em_get_location($post_id,'post_id');
			$EM_Location->set_status(null);
			$EM_Notices->remove_all(); //no validation/notices needed
		}
	}
	
	function untrash_post($post_id){
		if(get_post_type($post_id) == EM_POST_TYPE_LOCATION){
			//set a constant so we know this event doesn't need 'saving'
			if(!defined('UNTRASHING_'.$post_id)) define('UNTRASHING_'.$post_id, true);
		}
	}
	
	function untrashed_post($post_id){
		if(get_post_type($post_id) == EM_POST_TYPE_LOCATION){
			global $EM_Notices;
			$EM_Location = em_get_location($post_id,'post_id');
			$EM_Location->set_status(1);
			$EM_Notices->remove_all(); //no validation/notices needed
		}
	}
	
	function meta_boxes(){
		add_meta_box('em-location-where', __('Where','dbem'), array('EM_Location_Post_Admin','meta_box_where'),EM_POST_TYPE_LOCATION, 'normal','high');
		//add_meta_box('em-location-metadump', __('EM_Location Meta Dump','dbem'), array('EM_Location_Post_Admin','meta_box_metadump'),EM_POST_TYPE_LOCATION, 'normal','high');
		if( get_option('dbem_location_attributes_enabled') ){
			add_meta_box('em-location-attributes', __('Attributes','dbem'), array('EM_Location_Post_Admin','meta_box_attributes'),EM_POST_TYPE_LOCATION, 'normal','default');
		}
	}
	
	function meta_box_metadump(){
		global $post,$EM_Location;
		echo "<pre>"; print_r(get_post_custom($post->ID)); echo "</pre>";
		echo "<pre>"; print_r($EM_Location); echo "</pre>";
	}
	function meta_box_where(){
		?><input type="hidden" name="_emnonce" value="<?php echo wp_create_nonce('edit_location'); ?>" /><?php
		em_locate_template('forms/location/where.php',true);		
	}
	
	function meta_box_attributes(){
		em_locate_template('forms/location/attributes.php',true);
	}
}
add_action('admin_init',array('EM_Location_Post_Admin','init'));