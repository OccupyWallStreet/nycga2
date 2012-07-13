<?php
/*
Plugin Name: Bowe Codes
Plugin URI: http://imath.owni.fr/2011/05/15/bowe-codes/
Description: adds BuddyPress specific shortcodes to display members/groups/blogs/forums
Version: 1.2
Requires at least: 3.0
Tested up to: 3.4
License: GNU/GPL 2
Author: imath
Author URI: http://imath.owni.fr/
Network: true
*/

//constants
define ( 'BOWE_CODES_PLUGIN_NAME', 'bowe-codes' );
define ( 'BOWE_CODES_PLUGIN_URL', WP_PLUGIN_URL . '/' . BOWE_CODES_PLUGIN_NAME );
define ( 'BOWE_CODES_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . BOWE_CODES_PLUGIN_NAME );
define ( 'BOWE_CODES_VERSION', '1.2' );


/**
* bowe_codes_is_django_multisite
* check if WordPress is 3.1 and multisite
*/
function bowe_codes_is_django_multisite(){
	if(get_option('db_version')>=17056 && is_multisite()) return true;
	else return false;
}

/**
* bowe_codes_add_admin_menu
* Add a submenu to buddypress to manage bpmh widgets.
* 
*/
function bowe_codes_add_admin_menu() {
	global $bp;

	if ( !$bp->loggedin_user->is_site_admin )
		return false;

	require ( dirname( __FILE__ ) . '/includes/bowe-codes-admin.php' );
	
	$admin_page = bowe_codes_16_new_admin();
	
	add_submenu_page( $admin_page, __( 'BCodes Options', 'bowe-codes' ), __( 'BCodes Options', 'bowe-codes' ), 'manage_options', 'bcodes-admin', 'bowe_codes_options' );
}

add_action( bowe_codes_is_django_multisite() ? 'network_admin_menu' : 'admin_menu', 'bowe_codes_add_admin_menu', 14 );

function bowe_codes_load_default_css(){
	if(function_exists('get_blog_option')) $bc_option = get_blog_option('1', 'bc_default_css');
	else $bc_option = get_option('bc_default_css');
	if($bc_option!="yes"){
		wp_enqueue_style('bowe-codes-css', BOWE_CODES_PLUGIN_URL.'/css/default.css');
		wp_enqueue_script('bowe-codes-js', BOWE_CODES_PLUGIN_URL.'/js/bowe-codes-fix.js', array('jquery'));
	}
}

add_action('get_header','bowe_codes_load_default_css');

function bowe_codes_load_admin_css(){
	if($_GET['page']=="bcodes-admin") wp_enqueue_style('bowe-codes-css', BOWE_CODES_PLUGIN_URL.'/css/admin.css');
}

add_action('admin_print_styles','bowe_codes_load_admin_css');

/**
* bowe_codes_html_group
* renders html for a given group
*/
function bowe_codes_html_group($id, $name, $slug, $size="50", $avatar=false, $desc=false){
	
	$group = groups_get_group( 'group_id='.$id );
	$group_home = bp_get_group_permalink( $group );
	
	$group_html ='';
	//avatar
	if($avatar) $group_html .= '<li><div class="bc_avatar"><a href="'.$group_home.'">'.bp_core_fetch_avatar('item_id='.$id.'&object=group&type=full&avatar_dir=group-avatars&width='.$size.'&height='.$size) . '</a></div>';
	else $group_html .='<li>';
	
	$group_html .= '<div class="group-infos">';
	$group_html .= '<h4><a href="'.$group_home.'">'.$name.'</a></h4>';
	
	if($desc){
		$group_html .= '<p><span class="group-desc">'.$desc.'</span></p>';
	}
	$group_html .= '</div></li>';
	
	
	return $group_html;
}

/**
* bowe_codes_html_member
* renders html for a given member
*/
function bowe_codes_html_member($user_id, $name, $avatar=false, $size="50", $fields=''){
	$user_home = bp_core_get_user_domain( $user_id );
	$member_html = "";
	
	//avatar
	if($avatar) $member_html = '<li><div class="bc_avatar"><a href="'.$user_home.'">'.bp_core_fetch_avatar('item_id=' . $user_id . '&type=full&width='.$size.'&height='.$size ) . '</a></div>';
	else $member_html .= "<li>";
	
	$member_html .= '<div class="user-infos">';
	$member_html .= '<h4><a href="'.$user_home.'">'.get_user_meta($user_id, 'nickname', true).'</a></h4>';
	
	//xprofile_fields
	if($fields!=''){
		$parse_fields = explode(',',$fields);
		foreach($parse_fields as $user_xprofile){
			$member_html .= '<p><span class="xprofile_thead">'.$user_xprofile.'</span><span class="xprofile_content">'.xprofile_get_field_data( $user_xprofile, $user_id ).'</p>';
		}
	}
	$member_html .= '</div></li>';
	
	return $member_html;
}

/**
* beginning of member shortcode functions
*/

//as we don't use ids but user_login, retrieves the id
function bowe_codes_get_member_by_login($login){
	global $wpdb;
	$user_id = $wpdb->get_var("SELECT ID FROM {$wpdb->base_prefix}users WHERE user_login='$login'");
	return $user_id;
}

add_shortcode('bc_member','bowe_codes_handle_member_shortcode');

function bowe_codes_handle_member_shortcode($atts){
	extract(shortcode_atts(array('avatar' => true, 'size' => 50, 'name' => '', 'class' => 'my_member', 'fields' => ''), $atts));
	return bowe_codes_member_tag('avatar='.$avatar.'&size='.$size.'&name='.$name.'&class='.$class.'&fields='.$fields);
}

function bowe_codes_member_tag($args=''){
	global $wpdb, $bp;
	$defaults = array(
		'avatar' => true,
		'size' => 50,
		'name' => '',
		'class' => 'my_member',
		'fields'=> ''
	);
 
	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );
	
	$user_id = bowe_codes_get_member_by_login($name);
	
	if(!$user_id) return false;
	
	$html_member_box = '<div class="'.$class.'">';
	$html_member_box .= '<ul class="'.$class.'-ul">'.bowe_codes_html_member($user_id, $name, $avatar, $size, $fields).'</ul>';
	$html_member_box .= '</div>';

	return $html_member_box;
}
/* enf of member shorcode functions */

/**
* beginning of group shortcode
*/

//as we don't use group ids but group slugs, retrieves the id and others infos
function bowe_codes_get_group_by_slug($slug){
	global $wpdb;
	$group_data = $wpdb->get_row("SELECT id, description, name FROM {$wpdb->base_prefix}bp_groups WHERE slug='$slug' AND status!='hidden'");
	return $group_data;
}

add_shortcode('bc_group','bowe_codes_handle_group_shortcode');

function bowe_codes_handle_group_shortcode($atts){
	extract(shortcode_atts(array('avatar' => true, 'size' => 50, 'slug' => '', 'class' => 'my_group', 'desc' => true), $atts));
	return bowe_codes_group_tag('avatar='.$avatar.'&size='.$size.'&slug='.$slug.'&class='.$class.'&desc='.$desc);
}

function bowe_codes_group_tag($args=''){
	global $wpdb, $bp;
	$defaults = array(
		'avatar' => true,
		'size' => 50,
		'slug' => '',
		'class' => 'my_group',
		'desc'=> true
	);
 
	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );
	
	$group_data = bowe_codes_get_group_by_slug($slug);
	
	if(!$group_data){
		return false;
	} 
	
	$html_group_box = '<div class="'.$class.'">';
	if($desc) $html_group_box .= '<ul class="'.$class.'-ul">'.bowe_codes_html_group($group_data->id, $group_data->name, $slug, $size, $avatar, $group_data->description).'</ul>';
	else $html_group_box .=	'<ul class="'.$class.'-ul">'.bowe_codes_html_group($group_data->id, $group_data->name, $slug, $size, $avatar).'</ul>';
	$html_group_box .='</div>';
	
	return $html_group_box;
}
/* enf of group shorcode functions */

/**
* beginning of groups shortcode
*/

function bowe_codes_get_groups_by_slug($slug_list){
	global $wpdb;
	$group_data = $wpdb->get_results("SELECT id, name, slug FROM {$wpdb->base_prefix}bp_groups WHERE slug IN($slug_list) AND status!='hidden'");
	return $group_data;
}

add_shortcode('bc_groups','bowe_codes_handle_groups_shortcode');

function bowe_codes_handle_groups_shortcode($atts, $content){
	extract(shortcode_atts(array('amount'=> 10, 'avatar' => true, 'size' => 50, 'type' => 'popular', 'featured' => '', 'class' => 'my_groups'), $atts));
	return bowe_codes_groups_tag('amount='.$amount.'&avatar='.$avatar.'&size='.$size.'&type='.$type.'&featured='.$featured.'&class='.$class, $content);
}


/**
* bowe_codes_groups_tag
* for bc_groups & bc_user_groups
*/
function bowe_codes_groups_tag($args='', $title=''){
	global $wpdb, $bp;
	$defaults = array(
		'amount' => 10,
		'avatar' => true,
		'size' => 50,
		'type' => 'popular',
		'featured'=> '',
		'class' => 'my_groups',
		'user_id' => 0,
		'dynamic'=> false
	);
 
	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );
	
	$html_groups_box = '<div class="'.$class.'">';
	if($title!='') $html_groups_box.='<h3>'.$title.'</h3>';
	
	if($featured!=''){
		$featured_list = explode(',', $featured);
		$featured_req = '"'.implode('","', $featured_list).'"';
		$featured_data = bowe_codes_get_groups_by_slug($featured_req);
		if(count($featured_data)>0){
			$html_groups_box .= '<div class="featured"><ul class="'.$class.'-ul">';
			foreach($featured_data as $feat_ids){
				$exclude_groups_from_loop[]=$feat_ids->id;
				$html_groups_box .= bowe_codes_html_group($feat_ids->id, $feat_ids->name, $feat_ids->slug, $size, $avatar);
			}
			$html_groups_box .='</ul></div>';
		}
		if(count($featured_data)==$amount){
			$html_groups_box .='</div>';
			return $html_groups_box;
		}
	}
	
	if($user_id!=0){
		if ( !empty( $bp->displayed_user->id ) && $dynamic )
			$user_id = $bp->displayed_user->id;
		else $user_id = $bp->loggedin_user->id;
	}
	
	if(bp_has_groups( 'type=' . $type . '&per_page=' . $amount . '&max=' . $amount .'&user_id='.$user_id )){
		$html_groups_box .='<ul class="'.$class.'-ul">';
		$i=1;
		while ( bp_groups() ){
			bp_the_group();
			if(isset($exclude_groups_from_loop) && in_array(bp_get_group_id(), $exclude_groups_from_loop)) continue;
			elseif($i<=$amount-count($exclude_groups_from_loop)){
				$html_groups_box .= '<li>';
				if($avatar) $html_groups_box .= '<div class="bc_avatar"><a href="'.bp_get_group_permalink().'">'.bp_get_group_avatar('type=full&width='.$size.'&height='.$size) . '</a></div>';
				$html_groups_box .= '<div class="group-infos">';
				$html_groups_box .= '<h4><a href="'.bp_get_group_permalink().'">'.bp_get_group_name().'</a></h4>';
				$html_groups_box .= '</div></li>';
			}
			$i+=1;
		}
	}
	$html_groups_box .='</ul></div>';
	return $html_groups_box;
}
/* enf of groups shorcode functions */


/**
* beginning of members shortcode
*/

//as we don't use ids but user_login, retrieves the id
function bowe_codes_get_members_by_login($login_list){
	global $wpdb;
	$user_data = $wpdb->get_results("SELECT ID, user_login FROM {$wpdb->base_prefix}users WHERE user_login IN($login_list)");
	return $user_data;
}

add_shortcode('bc_members','bowe_codes_handle_members_shortcode');

function bowe_codes_handle_members_shortcode($atts){
	//active or newest
	extract(shortcode_atts(array('amount'=> 10, 'avatar' => true, 'size' => 50, 'type' => 'active', 'featured' => '', 'class' => 'my_members'), $atts));
	return bowe_codes_members_tag('amount='.$amount.'&avatar='.$avatar.'&size='.$size.'&type='.$type.'&featured='.$featured.'&class='.$class);
}

/**
* bowe_codes_members_tag
* for bc_members & bc_friends
*/
function bowe_codes_members_tag($args=''){
	global $wpdb, $bp;
	$defaults = array(
		'amount' => 10,
		'avatar' => true,
		'size' => 50,
		'type' => 'active',
		'featured'=> '',
		'class' => 'my_members',
		'user_id' => 0,
		'dynamic' => false
	);
 
	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );
	
	$html_members_box = '<div class="'.$class.'">';
	
	if($featured!=''){
		$featured_list = explode(',', $featured);
		$featured_req = '"'.implode('","', $featured_list).'"';
		$featured_data = bowe_codes_get_members_by_login($featured_req);
		if(count($featured_data)>0){
			$html_members_box .= '<div class="featured"><ul class="'.$class.'-ul">';
			foreach($featured_data as $feat_ids){
				$exclude_members_from_loop[]=$feat_ids->ID;
				$html_members_box .= bowe_codes_html_member($feat_ids->ID, $feat_ids->user_login, $avatar, $size);
			}
			$html_members_box .='</ul></div>';
		}
		if(count($featured_data)==$amount){
			$html_members_box .='</div>';
			return $html_members_box;
		}
	}
	
	if($user_id!=0 && $dynamic && bowe_codes_is_user()) $user_id = $bp->displayed_user->id;
	elseif($user_id!=0) $user_id = $bp->loggedin_user->id;
	
	if(bp_has_members( 'user_id='.$user_id.'&type=' . $type . '&max=' . $amount )){
		$html_members_box .= '<ul class="'.$class.'-ul">';
		$i=1;
		while ( bp_members() ){
			bp_the_member();
			if(isset($exclude_members_from_loop) && in_array(bp_get_member_user_id(), $exclude_members_from_loop)) continue;
			elseif($i<=$amount-count($exclude_members_from_loop)){
				$html_members_box .= '<li>';
				if($avatar) $html_members_box .= '<div class="bc_avatar"><a href="'.bp_get_member_permalink().'">'.bp_get_member_avatar('type=full&width='.$size.'&height='.$size) . '</a></div>';
				$html_members_box .= '<div class="user-infos">';
				$html_members_box .= '<h4><a href="'.bp_get_member_permalink().'">'.bp_get_member_name().'</a></h4>';
				$html_members_box .= '</div></li>';
			}
			$i+=1;
		}
	}
	$html_members_box .='</ul></div>';
	return $html_members_box;
}
/* enf of members shorcode functions */

/**
* beginning of friends shortcode
*/
add_shortcode('bc_friends','bowe_codes_handle_friends_shortcode');

function bowe_codes_handle_friends_shortcode($atts){
	//active or newest
	extract(shortcode_atts(array('amount'=> 10, 'avatar' => true, 'size' => 50, 'type' => 'newest', 'class' => 'my_friends', 'dynamic' => false), $atts));
	return bowe_codes_members_tag('amount='.$amount.'&avatar='.$avatar.'&size='.$size.'&type='.$type.'&class='.$class.'&dynamic='.$dynamic.'&user_id=1');
}
/* enf of friends shorcode functions */

/**
* beginning of bc_user_groups shortcode
*/
add_shortcode('bc_user_groups','bowe_codes_handle_user_groups_shortcode');

function bowe_codes_handle_user_groups_shortcode($atts){
	//active or newest
	extract(shortcode_atts(array('amount'=> 10, 'avatar' => true, 'size' => 50, 'type' => 'popular', 'class' => 'user_groups', 'dynamic' => false), $atts));
	return bowe_codes_groups_tag('amount='.$amount.'&avatar='.$avatar.'&size='.$size.'&type='.$type.'&class='.$class.'&dynamic='.$dynamic.'&user_id=1');
}
/* enf of bc_user_groups shorcode functions */

/* beginning of bc_messages shortcode */
add_shortcode('bc_messages','bowe_codes_handle_messages_shortcode');

function bowe_codes_handle_messages_shortcode($atts){
	extract(shortcode_atts(array('amount'=> 5, 'subject' => true, 'avatar' => true, 'size' => 30, 'excerpt' => 10, 'class' => 'my_messages'), $atts));
	return bowe_codes_messages_tag('amount='.$amount.'&subject='.$subject.'&avatar='.$avatar.'&size='.$size.'&excerpt='.$excerpt.'&class='.$class);
}

function bowe_codes_messages_tag($args=''){
	global $wpdb, $bp, $messages_template;
	$defaults = array(
		'amount' => 5,
		'subject' => true,
		'avatar' => true,
		'size' => 30,
		'excerpt' => 10,
		'class' => 'my_messages'
	);
 
	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );
	
	if(!is_user_logged_in()) return false;
	
	$html_messages_box = '<div class="'.$class.'">';
	
	if(bp_has_message_threads( 'box=inbox&per_page='.$amount)){
		$html_messages_box .='<ul class="'.$class.'-ul">';
		while ( bp_message_threads() ){
			bp_message_thread();
			$html_messages_box .='<li>';
			$sender_home = bp_core_get_user_domain($messages_template->thread->last_sender_id);
			if($avatar) $html_messages_box .= '<div class="bc_avatar"><a href="'.$sender_home.'" title="'.__('From:','bowe-codes').' '.get_user_meta($messages_template->thread->last_sender_id, 'nickname', true).'">'.bp_core_fetch_avatar('item_id=' . $messages_template->thread->last_sender_id . '&type=full&width='.$size.'&height='.$size ) . '</a></div>';
			$html_messages_box .= '<div class="message-infos">';
			if(!$avatar && $subject) $html_messages_box .= '<span class="bc_from">'.__('From:','bowe-codes').' '.bp_get_message_thread_from().' </span>';
			if(!$avatar && !$subject) $html_messages_box .= '<span class="bc_from">'.__('From:','bowe-codes').'<a href="'.bp_get_message_thread_view_link().'" title="'.__( "View Message", "buddypress" ).'">'.get_user_meta($messages_template->thread->last_sender_id, 'nickname', true).'</a>';
			if($subject) $html_messages_box .= '<span class="bc_subject"><a href="'.bp_get_message_thread_view_link().'" title="'.__( "View Message", "buddypress" ).'">'.bp_get_message_thread_subject().'</a></span>';
			$html_messages_box .= '<p class="bc_excerpt">'.strip_tags( bp_create_excerpt( $messages_template->thread->last_message_content, $excerpt )).'</p>';
			$html_messages_box .= '</div></li>';
		}
	}
	$html_messages_box .='</ul></div>';
	return $html_messages_box;
}

/* end of bc_messages shortcode */

/* beginning of bc_notifications shortcode */

/* as new notifications are only send by BuddyPress core notifications class */
function bowe_codes_get_all_for_user( $user_id ) {
	global $wpdb, $bp;

	return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$bp->core->table_name_notifications} WHERE user_id = %d", $user_id ) );
}

/* strangely date is the fastest way! */
function bowe_codes_get_sender( $date_notified ){
	global $wpdb, $bp;

	return $wpdb->get_var( $wpdb->prepare( "SELECT sender_id FROM {$wpdb->base_prefix}bp_messages_messages WHERE date_sent = %s", $date_notified ) );
}

add_shortcode('bc_notifications','bowe_codes_handle_notifications_shortcode');

function bowe_codes_handle_notifications_shortcode($atts){
	extract(shortcode_atts(array('amount'=> 5, 'avatar' => true, 'size' => 30, 'class' => 'my_notifications'), $atts));
	return bowe_codes_notifications_tag('amount='.$amount.'&avatar='.$avatar.'&size='.$size.'&class='.$class);
}

function bowe_codes_notifications_tag($args=''){
	global $wpdb, $bp;
	$defaults = array(
		'amount' => 5,
		'avatar' => true,
		'size' => 30,
		'class' => 'my_notifications'
	);
 
	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );
	
	if(!is_user_logged_in()) return false;
	
	$notifications = BP_Core_Notification::get_all_for_user( $bp->loggedin_user->id );
	$notifications_content = bp_core_get_notifications_for_user($bp->loggedin_user->id);
	
	$html_notifications_box = '<div class="'.$class.'">';

	if($notifications_content && count($notifications_content)>0){
		$html_notifications_box .='<ul class="'.$class.'-ul">';
		for($i=0;$i<count($notifications_content);$i++){
			if($i<$amount){
				$html_notifications_box .='<li>';
				if($avatar){
					if($notifications[$i]->component_name=="groups"){
						if($notifications[$i]->secondary_item_id!=0) $html_notifications_box .= '<div class="bc_avatar">'.bp_core_fetch_avatar('item_id=' . $notifications[$i]->secondary_item_id . '&object=group&type=full&avatar_dir=group-avatars&width='.$size.'&height='.$size ) . '</div>';
						else $html_notifications_box .= '<div class="bc_avatar">'.bp_core_fetch_avatar('item_id=' . $notifications[$i]->item_id . '&object=group&type=full&avatar_dir=group-avatars&width='.$size.'&height='.$size ) . '</div>';
					}
					elseif( $notifications[$i]->component_name== 'messages') $html_notifications_box .= '<div class="bc_avatar">'.bp_core_fetch_avatar('item_id=' . bowe_codes_get_sender($notifications[$i]->date_notified) . '&type=full&width='.$size.'&height='.$size ) . '</div>';
					else $html_notifications_box .= '<div class="bc_avatar">'.bp_core_fetch_avatar('item_id=' . $notifications[$i]->item_id . '&type=full&width='.$size.'&height='.$size ) . '</div>';
				}
				$html_notifications_box .= '<div class="notification-infos">';
				$html_notifications_box .= '<p class="bc_notifications">'. $notifications_content[$i] .'</p>';
				$html_notifications_box .= '</div></li>';
			}
		}
	}else{
		$html_notifications_box .='<ul class="'.$class.'-ul">';
		$html_notifications_box .= '<li><div class="notification-infos">';
		$html_notifications_box .= '<p class="bc_notifications">'. __( 'No new notifications.', 'buddypress' ) .'</p>';
		$html_notifications_box .= '</div></li>';
	}
	$html_notifications_box .='</ul></div>';
	return $html_notifications_box;
}
/* end of bc_notifications shortcode */

/**
* beginning of bc_blogs shortcode
*/

function bowe_codes_html_blog($blog_id, $avatar, $size, $desc){
	if($avatar) $blog_html .= '<div class="bc_avatar"><a href="'.get_blog_option($blog_id, 'siteurl').'">'.get_avatar(get_blog_option($blog_id, 'admin_email'), $size).'</a></div>';
	$blog_html .= '<div class="blog-infos">';
	$blog_html .= '<h4><a href="'.get_blog_option('siteurl',$blog_id).'">'.get_blog_option($blog_id, 'blogname').'</a></h4>';
	if($desc) $blog_html .= '<p>'.get_blog_option($blog_id, 'blogdescription').'</p>';
	$blog_html .= '</div>';
	
	return $blog_html;
}


add_shortcode('bc_blogs','bowe_codes_handle_blogs_shortcode');

function bowe_codes_handle_blogs_shortcode($atts){
	global $blog_id;
	//only available for super admin!
	if(1!=$blog_id) return false;
	extract(shortcode_atts(array('amount'=> 5, 'avatar' => true, 'size' => 50, 'type' => 'active', 'featured' => '', 'class' => 'my_blogs', 'desc' => true), $atts));
	return bowe_codes_blogs_tag('amount='.$amount.'&avatar='.$avatar.'&size='.$size.'&type='.$type.'&featured='.$featured.'&class='.$class.'&desc='.$desc);
}

function bowe_codes_blogs_tag($args=''){
	global $wpdb, $bp, $blogs_template;
	$defaults = array(
		'amount' => 5,
		'avatar' => true,
		'size' => 50,
		'type' => 'active',
		'featured'=> '',
		'class' => 'my_blogs',
		'desc' => true
	);
 
	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );
	
	$html_blogs_box = '<div class="'.$class.'">';
	
	if($featured!=''){
		$featured_list = explode(',', $featured);
		if(count($featured_list)>0){
			$html_blogs_box .= '<div class="featured">';
			$html_blogs_box .='<ul class="'.$class.'-ul">';
			foreach($featured_list as $feat_ids){
				$exclude_blogs_from_loop[]=$feat_ids;
				$html_blogs_box .= '<li>'.bowe_codes_html_blog($feat_ids, $avatar, $size, $desc).'</li>';
			}
			$html_blogs_box .='</ul></div>';
		}

		if(count($featured_list)==$amount){
			return $html_blogs_box.'</div>';
		}
		
	}
	if(bp_has_blogs( 'type=' . $type . '&per_page=' . $amount . '&max=' . $amount )){
		$html_blogs_box .= '<ul class="'.$class.'-ul">';
		$i=1;
		$j=0;
		while ( bp_blogs() ){
			bp_the_blog();
			$check = $amount - count($exclude_blogs_from_loop);
			if(isset($exclude_blogs_from_loop) && in_array($blogs_template->blogs[$j]->blog_id, $exclude_blogs_from_loop)) $i-=1 ;
			elseif($i<=$amount - count($exclude_blogs_from_loop)){
				if($avatar) $html_blogs_box .= '<li><div class="bc_avatar"><a href="'.bp_get_blog_permalink().'">'.bp_get_blog_avatar('width='.$size.'&height='.$size) . '</a></div>';
				else $html_blogs_box .= '<li>';
				$html_blogs_box .= '<div class="blog-infos">';
				$html_blogs_box .= '<h4><a href="'.bp_get_blog_permalink().'">'.bp_get_blog_name().'</a></h4>';
				if($desc) $html_blogs_box .= '<p>'.bp_get_blog_description().'</p>';
				$html_blogs_box .= '</div></li>';
			}
			$i+=1;
			$j+=1;
		}
	}
	$html_blogs_box .='</ul></div>';
	return $html_blogs_box;
}
/* enf of bc_blogs shortcode functions */

/**
* beginning of bc_posts shortcode functions 
*/

function bowe_codes_sort_posts($table_posts=array(), $type='newest'){
	$html_posts="";
	if(count($table_posts)<1) return false;
	
	if($type=="random"){
		shuffle($table_posts);
	}
	foreach($table_posts as $post){
		$html_posts .= $post;
	}
	return $html_posts;
}

function bowe_codes_parse_post_title($title){
	preg_match('/<a href=\"([^\"]*)\">(.*)<\/a>/iU', $title, $matches);
	return stripslashes($matches[0]);
}

add_shortcode('bc_posts','bowe_codes_handle_blog_posts_shortcode');

function bowe_codes_handle_blog_posts_shortcode($atts){
	extract(shortcode_atts(array('amount'=> 5, 'avatar' => true, 'size' => 50, 'type' => 'newest', 'class' => 'my_blog_posts', 'excerpt' => 10), $atts));
	return bowe_codes_blog_posts_tag('amount='.$amount.'&avatar='.$avatar.'&size='.$size.'&type='.$type.'&class='.$class.'&excerpt='.$excerpt);
}

function bowe_codes_blog_posts_tag($args=''){
	global $wpdb, $bp, $activities_template;
	$defaults = array(
		'amount' => 5,
		'avatar' => true,
		'size' => 50,
		'type' => 'newest',
		'class' => 'my_blog_posts',
		'excerpt' => 10
	);
 
	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );
	
	$table_posts = array();
	
	$html_blog_posts_box = '<div class="'.$class.'">';
	
	if(bp_has_activities( 'action=new_blog_post&per_page=' . $amount . '&max=' . $amount )){
		$html_blog_posts_box .= '<ul class="'.$class.'-ul">';
		$i=0;
		
		while ( bp_activities() ){
			bp_the_activity();
			
			$html_blog_posts_box_content = "<li>";
			
			if($avatar) $html_blog_posts_box_content .= '<div class="bc_avatar"><a href="'.bp_get_activity_user_link().'">'.bp_get_activity_avatar('width='.$size.'&height='.$size) . '</a></div>';
			$html_blog_posts_box_content .= '<div class="post-infos">';
			$html_blog_posts_box_content .= '<div class="post-title">'.bowe_codes_parse_post_title($activities_template->activities[$i]->action).'</div>';
			$html_blog_posts_box_content .= '<p>'.strip_tags( bp_create_excerpt( $activities_template->activities[$i]->content, $excerpt )).'</p>';
			$html_blog_posts_box_content .= '</div></li>';
			
			$table_posts[] = $html_blog_posts_box_content;
			$i+=1;
		}
	}
	
	$html_blog_posts_box .= bowe_codes_sort_posts($table_posts, $type);
	
	$html_blog_posts_box .='</ul></div>';
	return $html_blog_posts_box;
}

add_shortcode( 'bc_restrict_gm', 'bowe_code_hide_post_content');

function bowe_code_hide_post_content( $atts, $content ) {
	global $wpdb;

	extract(shortcode_atts( array( 'group_id' => 0, 'class' => 'my_restrict_message' ), $atts) );
	
	if( !empty( $group_id) && intval( $group_id ) <= 0 ) {
		$group_id = $wpdb->get_var("SELECT id FROM {$wpdb->base_prefix}bp_groups WHERE slug='$group_id'");
	}
	
	$args = array( 'group_id' => $group_id, 'class' => $class, 'content' => $content );

	return bowe_code_hide_post_content_tag( $args );
		
}

function bowe_code_hide_post_content_tag( $args='' ) {
	global $bp;
	
	$defaults = array(
		'group_id' => false,
		'class' => 'my_restrict_message',
		'content' => ''
	);
 
	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );
	
	// if no group id content is return
	if( empty( $group_id ) )
		return $content;

	// if user not logeddin, he's asked to
	if( !is_user_logged_in() ) {
		
		$message_unlogged = '<p class="'.$class.'">' . __('You must be loggedin to access to this content', 'bowe-codes') . '</p>';
		return apply_filters( 'bowe_code_hide_post_connect_message', $message_unlogged );
	}
		

	$user_id = $bp->loggedin_user->id;

	// if the user is a group member, let's return the content	
	if ( groups_is_user_member( $user_id, $group_id ) )
		return $content;

	else {

		$group = groups_get_group( 'group_id='.$group_id );
		$group_home = bp_get_group_permalink( $group );
		$group_name = $group->name;
		$message_notgm = '<p class="'.$class.'">' . sprintf(__('You must be a member of the group %s to access this content', 'bowe-codes'), '<a href='.$group_home.'>'.$group_name.'</a>') . '</p>';

		return apply_filters( 'bowe_code_hide_post_connect_message', $message_notgm, $group_id);

		}
	
}

/* enf of bc_posts shortcode functions */

/* forum shortcodes */
add_shortcode('bc_forum','bowe_codes_handle_forum_shortcode');

function bowe_codes_handle_forum_shortcode($atts){
	global $wpdb;
	
	extract(shortcode_atts(array('group_id' => 0,'amount'=> 5, 'avatar' => true, 'size' => 50, 'type' => 'new_forum_topic', 'class' => 'my_forum', 'excerpt' => 10), $atts));
	
	
	if( !empty( $group_id) && intval( $group_id ) <= 0 ) {
		$group_id = $wpdb->get_var("SELECT id FROM {$wpdb->base_prefix}bp_groups WHERE slug='$group_id' AND status='public'");
	}
	
	return bowe_codes_forum_tag('group_id='.$group_id.'&amount='.$amount.'&avatar='.$avatar.'&size='.$size.'&type='.$type.'&class='.$class.'&excerpt='.$excerpt);
}

function bowe_codes_forum_tag($args=''){
	global $wpdb, $bp, $activities_template;
	$defaults = array(
		'group_id' => 0,
		'amount' => 5,
		'avatar' => true,
		'size' => 50,
		'type' => 'new_forum_topic',
		'class' => 'my_forum',
		'excerpt' => 10
	);
	
	
 
	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );
	
	$table_forum = array();
	
	$html_forum_box = '<div class="'.$class.'">';
	
	if( !empty($group_id) )
		$query = 'action='.$type.'&object=groups&primary_id='.$group_id.'&per_page=' . $amount . '&max=' . $amount;
		
	else
		$query = 'action='.$type.'&per_page=' . $amount . '&max=' . $amount;
	
	if(bp_has_activities( $query )){
		$html_forum_box .= '<ul class="'.$class.'-ul">';
		$i=0;
		
		while ( bp_activities() ){
			bp_the_activity();
			
			$html_forum_box_content = "<li>";
			$is_reply = false;
			
			if($avatar) $html_forum_box_content .= '<div class="bc_avatar"><a href="'.bp_get_activity_user_link().'">'.bp_get_activity_avatar('width='.$size.'&height='.$size) . '</a></div>';
			$html_forum_box_content .= '<div class="forum-infos">';
			if( $type == 'new_forum_post')
				$is_reply = '<span>'. __('In reply to:', 'bowe-codes'). ' </span>';
			$html_forum_box_content .= '<div class="post-title">'. $is_reply .bowe_codes_parse_post_title($activities_template->activities[$i]->action).'</div>';
			$html_forum_box_content .= '<p>'.strip_tags( bp_create_excerpt( $activities_template->activities[$i]->content, $excerpt )).'</p>';
			$html_forum_box_content .= '</div></li>';
			
			$table_forum[] = $html_forum_box_content;
			$i+=1;
		}
	}
	
	$html_forum_box .= implode('', $table_forum);
	
	$html_forum_box .='</ul></div>';
	return $html_forum_box;
}


/* adding a thickbox to help users create their shortcode */
add_action('media_buttons', 'bowe_codes_add_media_button', 20);

function bowe_codes_add_media_button() {
	if(bowe_codes_can_child_blogs()){
		$url = BOWE_CODES_PLUGIN_URL.'/includes/bowe-codes-editor.php?tab=add&TB_iframe=true&amp;height=500&amp;width=640';
		if (is_ssl()) $url = str_replace( 'http://', 'https://',  $url );
		echo '<a href="'.$url.'" class="thickbox" title="'.__('Add BP Content','bowe-codes').'"><img src="'.BOWE_CODES_PLUGIN_URL.'/images/bowe-codes-btn.png" alt="'.__('Add BP Content','bowe-codes').'" width="74px" height="16px"></a>';
	}
}

/**
* taking care of deprecated since BP 1.5
*/
function bowe_codes_is_user(){
	if( defined( 'BP_VERSION' ) && version_compare( BP_VERSION, '1.5', '<' ) ){
		return bp_is_member();
	}
	else return bp_is_user();
}

/**
* BP 1.6beta1 new admin area
*/
function bowe_codes_16_new_admin(){
	if( defined( 'BP_VERSION' ) && version_compare( BP_VERSION, '1.6-alpha-6041', '>=' ) ){
		$page  = bp_core_do_network_admin()  ? 'settings.php' : 'options-general.php';
		return $page;
	}
	else return 'bp-general-settings';
}


/**
* checking if super admin allows child blogs to use it
*/
function bowe_codes_can_child_blogs(){
	if(!is_multisite())
		return true;
		
	global $blog_id;
	if(function_exists('get_blog_option')) $bc_option = get_blog_option('1', 'bc_enable_network');
	else $bc_option = get_option('bc_enable_network');
	if( $blog_id != 1 && $bc_option == "yes" && !is_super_admin()){
		return false;
	}
	else return true;
}

/**
* bowe_codes_load_textdomain
* translation!
* 
*/
function bowe_codes_load_textdomain() {

	// try to get locale
	$locale = apply_filters( 'bowe_codes_load_textdomain_get_locale', get_locale() );

	// if we found a locale, try to load .mo file
	if ( !empty( $locale ) ) {
		// default .mo file path
		$mofile_default = sprintf( '%s/languages/%s-%s.mo', BOWE_CODES_PLUGIN_DIR, BOWE_CODES_PLUGIN_NAME, $locale );
		// final filtered file path
		$mofile = apply_filters( 'bowe_codes_load_textdomain_mofile', $mofile_default );
		// make sure file exists, and load it
		if ( file_exists( $mofile ) ) {
			load_textdomain( BOWE_CODES_PLUGIN_NAME, $mofile );
		}
	}
}
add_action ( 'bp_init', 'bowe_codes_load_textdomain', 2 );


/**
* bowe_codes_activate
* store plugin's version
* 
*/
function bowe_codes_activate() {	
	//if first install
	if(!get_option('bowe-codes-version') || BOWE_CODES_VERSION != get_option('bowe-codes-version') ){
		update_option( 'bowe-codes-version', BOWE_CODES_VERSION );
	}
}
register_activation_hook( __FILE__, 'bowe_codes_activate' );
?>