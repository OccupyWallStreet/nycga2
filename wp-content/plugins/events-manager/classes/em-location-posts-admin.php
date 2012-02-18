<?php
class EM_Location_Posts_Admin{
	function init(){
		global $pagenow;
		if($pagenow == 'edit.php' && !empty($_REQUEST['post_type']) && $_REQUEST['post_type'] == EM_POST_TYPE_LOCATION ){ //only needed if editing post
			add_action('admin_head', array('EM_Location_Posts_Admin','admin_head'));
		}
		add_filter('manage_edit-location_columns' , array('EM_Location_Posts_Admin','columns_add'));
		add_filter('manage_posts_custom_column' , array('EM_Location_Posts_Admin','columns_output'),10,1 );
	}
	
	function admin_head(){
		//quick hacks to make event admin table make more sense for events
		?>
		<script type="text/javascript">
			jQuery(document).ready( function($){
				$('.inline-edit-date').prev().css('display','none').next().css('display','none').next().css('display','none');
			});
		</script>
		<style>
			table.fixed{ table-layout:auto !important; }
			.tablenav select[name="m"] { display:none; }
		</style>
		<?php
	}
	
	function admin_menu(){
		global $menu, $submenu;
	  	// Add a submenu to the custom top-level menu:
   		$plugin_pages = array(); 
		$plugin_pages[] = add_submenu_page('edit.php?post_type='.EM_POST_TYPE_EVENT, __('Locations', 'dbem'), __('Locations', 'dbem'), 'edit_locations', 'events-manager-locations', "edit.php?post_type=event");
		$plugin_pages = apply_filters('em_create_locationss_submenu',$plugin_pages);
	}
	
	function columns_add($columns) {
	    unset($columns['author']);
	    unset($columns['date']);
	    unset($columns['comments']);
	    return array_merge($columns, array( 
	    	'address' => __('Address','dbem'), 
	    	'town' => __('Town','dbem'),
	    	'state' => __('State','dbem'),
	    	'country' => __('Country','dbem') 
	    ));
	}
	
	function columns_output( $column ) {
		global $post;
		if( $post->post_type == EM_POST_TYPE_LOCATION ){
			$post = em_get_location($post); 
			switch ( $column ) {
				case 'address':
					echo $post->location_address;
					break;
				case 'town':
					echo $post->location_town;
					break;
				case 'state':
					echo $post->location_state;
					break;
				case 'country':
					echo $post->location_country;
					break;
			}
		}
	}
}
EM_Location_Posts_Admin::init();