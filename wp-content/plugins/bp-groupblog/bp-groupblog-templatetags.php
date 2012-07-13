<?php
function bp_groupblog_has_blog_posts() {
	global $bp, $blog_posts_template;

	$blog_posts_template = new BP_groupblog_Template( $bp->displayed_user->id );
	
	return $blog_posts_template->has_blog_posts();
}

function bp_groupblog_the_blog_post() {
	global $blog_posts_template;
	return $blog_posts_template->the_blog_post();
}

function bp_groupblog_blog_posts() {
	global $blog_posts_template;
	return $blog_posts_template->user_blog_posts();
}

function bp_groupblog_blog_post_name() {
	global $blog_posts_template;
	
	echo ''; // Example: $blog_posts_template->blog_post->name;
}

function bp_groupblog_blog_post_pagination() {
	global $blog_posts_template;
	
	echo $blog_posts_template->pag_links;
}

function bp_groupblog_show_enabled( $group_id ) {

  if  ( groups_get_groupmeta ( $group_id, 'groupblog_enable_blog' ) == '1' ) {
		echo ' checked="checked"';
  }

}

function bp_groupblog_is_blog_enabled( $group_id ) {
  
  if  ( groups_get_groupmeta ( $group_id, 'groupblog_enable_blog' ) == '1' ) {
  	return true;
  } else {
  	return false;
  }
}

function bp_groupblog_blog_exists( $group_id ) {

  if  ( !groups_get_groupmeta ( $group_id, 'groupblog_blog_id' ) == '' ) {
  	return true;
  } else {
  	return false;
  }

}

function bp_groupblog_silent_add( $group_id ) {

  if  ( !groups_get_groupmeta ( $group_id, 'groupblog_silent_add' ) == '' ) {
  	return true;
  } else {
  	return false;
  }
	
}  

/*
 * groupblog_blog_id()
 * 
 * Echos the blog id of the current group's blog unless
 * $group_id is explicitly passed in.
 * 
 */
function groupblog_blog_id( $group_id = '' ) {   
  echo get_groupblog_blog_id( $group_id );
}
	function get_groupblog_blog_id( $group_id = '' ) {
		global $bp;
		
		if (  $group_id == '' ) {
			$group_id = $bp->groups->current_group->id;
		}
			
		return groups_get_groupmeta( $group_id, 'groupblog_blog_id' );
	}  

/*
 * groupblog_group_id()
 * 
 * Echos the group id of the group associated with the blog id that is passed in.
 * 
 */
function groupblog_group_id( $blog_id ) {
	echo get_groupblog_group_id( $blog_id );	
}
	function get_groupblog_group_id( $blog_id ) {
		global $bp, $wpdb;
		
		if ( !isset( $blog_id ) )
			return;
		
		// table_name_groupmeta is not defined on first install
		if ( !isset( $bp->groups->table_name_groupmeta ) )
			return;
		
		if ( $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$bp->groups->table_name_groupmeta} WHERE meta_key = 'groupblog_blog_id' AND meta_value = %d", $blog_id ) ) ) {
			return $row->group_id;
		}	
	}

/*
 * bp_groupblog_id()
 * 
 * Echos the group id of the group associated with the blog id.
 * 
 */
function bp_groupblog_id() {
	echo bp_get_groupblog_id();
}
	function bp_get_groupblog_id() {
		global $current_blog;
		
		return apply_filters( 'bp_get_groupblog_id', get_groupblog_group_id( $current_blog->blog_id ) );
	}

/*
 * bp_groupblog_slug()
 * 
 * Echos the group slug of the group associated with the blog id.
 * 
 */	
function bp_groupblog_slug() {
	echo bp_get_groupblog_slug();
}
	function bp_get_groupblog_slug() {

		$group = groups_get_group( array( 'group_id' => bp_get_groupblog_id() ) );	
		return apply_filters( 'bp_get_groupblog_slug', $group->slug );
	}

function bp_groupblog_forum() {
 echo bp_get_groupblog_forum();
}
	function bp_get_groupblog_forum() {
		global $bp;
	
		$forum_id = groups_get_groupmeta( bp_get_groupblog_id(), 'forum_id' );	
		return apply_filters( 'bp_get_groupblog_forum', $forum_id );
	}

/*
 * bp_groupblog_admin_form_action()
 *
 */
function groupblog_current_layout() {
	
	$checks = get_site_option('bp_groupblog_blog_defaults_options');
	$template_name = $checks['page_template_layout'];
	
	return $template_name;
}

/*
 * bp_groupblog_allow_group_admin_layout()
 *
 */
function bp_groupblog_allow_group_admin_layout() {

	$opt = get_site_option( 'bp_groupblog_blog_defaults_options' );

	if ( ($opt['group_admin_layout'] == 1) && ($opt['theme'] == 'p2|p2-buddypress') )	{
		return true;
	} else {
		return false;
	}
}

/*
 * bp_groupblog_admin_form_action()
 *
 */
function groupblog_get_page_template_layout() {

	$opt = get_site_option( 'bp_groupblog_blog_defaults_options' );

	return $opt['page_template_layout'];
}

/*
 * bp_groupblog_admin_form_action()
 *
 */
function groupblog_locate_layout() {

	$opt = get_site_option( 'bp_groupblog_blog_defaults_options' );

	if ( ( $opt['group_admin_layout'] != 1 ) || !( $template_name = groups_get_groupmeta( bp_get_groupblog_id(), 'page_template_layout' ) ) )
		$template_name = $opt['page_template_layout'];
	
	locate_template( array( 'groupblog/layouts/' . $template_name . '.php' ), true );
}

/*
 * bp_groupblog_admin_form_action()
 *
 */
function bp_groupblog_admin_form_action( $page, $group = false ) {
	global $bp, $groups_template;

	if ( !$group )
		$group =& $groups_template->group;
	
	echo apply_filters( 'bp_groupblog_admin_form_action', bp_group_permalink( $group, false ) . '/admin/' . $page );
}
?>