<?php
/*
Plugin Name: BuddyPress Group Tags
Plugin URI: http://wordpress.org/extend/plugins/buddypress-group-tags/
Description: This plugin allow Groups to be organized by tags via a tag cloud above the group directory or in a widget
Version: 7.0.1
Revision Date: September 26, 2011
License: GNU General Public License 2.0 (GPL) http://www.gnu.org/licenses/gpl.html
Author: Deryk Wenaus, edited by Eric Lewis
Author URI: http://www.bluemandala.com
*/

/* 
latest version: (to add to readme)
made admin page compatible with network admin
added category
added very cool activity based on tag
*/


function bp_gtags_setup_globals() {
	global $bp;
	$bp->gtags->id = 'gtags';
	$bp->gtags->slug = 'tag';
}
add_action( 'bp_setup_globals', 'bp_gtags_setup_globals' );


// in order for tags to show as /groups/tag/mytag I am using this function. however it is not optimal because it's not really a sub menu item
function bp_gtags_setup_nav() {
	global $bp;
	bp_core_new_subnav_item( array( 'name' => '&nbsp;', 'slug' => $bp->gtags->slug, 'parent_slug' => BP_GROUPS_SLUG, 'parent_url' => $bp->root_domain .'/'. BP_GROUPS_SLUG . '/', 'screen_function' => 'gtags_display_hook', 'position' => -1 ) );
}
add_action( 'bp_setup_nav', 'bp_gtags_setup_nav', 1000 );

// a hack to remove the group tags menu item before the admin bar is displayed. it works because the admin bar is called last
function gtags_remove_tags_from_admin_bar() {
	global $bp;
	unset ( $bp->bp_options_nav['groups']['98564'] );
}
add_action( 'bp_adminbar_menus', 'gtags_remove_tags_from_admin_bar', 3 );


function gtags_display_hook() {
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'groups/index' ) );
}


// load the javascript for the group tag clicks to work
function gtags_enqueue() {
	wp_enqueue_script('gtags=group-tags', WP_PLUGIN_URL.'/buddypress-group-tags/group-tags.js', array('jquery') );
	load_plugin_textdomain( 'gtags', false, dirname( plugin_basename( __FILE__ ) ).'/lang'  );
}
add_action('init', 'gtags_enqueue');

//add css
function gtags_header() {
	echo '<link rel="stylesheet" type="text/css" href="'.WP_PLUGIN_URL.'/buddypress-group-tags/group-tags.css" media="screen" />'."\n";
}
add_action('wp_head', 'gtags_header');


// this catches the ajax call, and loads the groups template
function gtags_ajax() {
	locate_template( array( "groups/groups-loop.php" ), true );
}
add_action( 'wp_ajax_gtags', 'gtags_ajax' );



// hook into group listing function, output the groups that match a given tag 
function gtags_show_groups_for_tag( $groups ) {
	global $bp, $groups_template, $gtags_done;

/*	echo '<pre>ajax_querystring: '; print_r( $bp->ajax_querystring ); echo '</pre>';
	echo '<pre>current_action: '; print_r( $bp->current_action ); echo '</pre>';
	echo '<pre>POST: '; print_r( $_POST ); echo '</pre>'; */
	
	if ( $_POST['action'] == 'groups_filter' || $_POST['groups_search_submit'] == 'Search' || $gtags_done )
		return $groups;
			
	if ( $_POST['tag'] )
		$tag = urldecode( $_POST['tag'] ); // this is what ajax sends if we are in group directory
	else if ( $bp->current_action == 'tag' )
		$tag = urldecode( $bp->action_variables[0] ); // this is for the widget from all other places
			
	if ( $tag ) {
		echo '<div id="gtags-results">'.__('Results for tag', 'gtags').': <b>' . stripslashes( $tag ) . '</b></div>';
		$gtags_groups = gtags_get_groups_by_tag( null, null, false, false, $tag );
		$groups_template->groups = $gtags_groups[groups];
		// turn off pagination 
		$groups_template->group_count = $gtags_groups[total];
		$groups_template->total_group_count = $gtags_groups[total];
		$groups_template->pag_num = $gtags_groups[total];
		$groups_template->pag_page = 1;
		$groups_template->pag_links = '';
		$groups = $gtags_groups;
	}
	
	//echo '<pre>'; print_r( $bp->current_action ); echo '</pre>';  
	//echo '<pre>'; print_r( $bp->action_variables[0] ); echo '</pre>';
	
	$gtags_done = true; // only run it once, so that the widgets function shows normal groups, not tags.
	return $groups;
}
add_filter( 'bp_has_groups', 'gtags_show_groups_for_tag', 5, 2 );



// Return an array of the groups for a specific tag (plus number of groups)
// this is a modified copy of many similar functions found in bp-groups-classes.php
function gtags_get_groups_by_tag( $limit = null, $page = null, $user_id = false, $search_terms = false, $group_tag = null ) {
	global $wpdb, $bp;
	
	if ( $limit && $page ) {
		$pag_sql = $wpdb->prepare( " LIMIT %d, %d", intval( ( $page - 1 ) * $limit), intval( $limit ) );
	}

	if ( !is_user_logged_in() || ( !is_super_admin() && ( $user_id != $bp->loggedin_user->id ) ) )
		$hidden_sql = "AND g.status != 'hidden'";

	if ( $search_terms ) {
		$search_terms = like_escape( $wpdb->escape( $search_terms ) );
		$search_sql = " AND ( g.name LIKE '%%{$search_terms}%%' OR g.description LIKE '%%{$search_terms}%%' )";
	}

	if ( $group_tag ) {
		$group_tag = like_escape( $wpdb->escape( $group_tag ) );
		$group_tag = stripslashes($group_tag);
		$tag_sql = " AND ( gm3.meta_value LIKE '%%{$group_tag}%%' )";
	}
	
	$paged_groups = $wpdb->get_results( "SELECT g.*, gm1.meta_value as total_member_count, gm2.meta_value as last_activity, gm3.meta_value as gtags_group_tags FROM {$bp->groups->table_name_groupmeta} gm1, {$bp->groups->table_name_groupmeta} gm2, {$bp->groups->table_name_groupmeta} gm3, {$bp->groups->table_name} g WHERE g.id = gm1.group_id AND g.id = gm2.group_id AND g.id = gm3.group_id AND gm2.meta_key = 'last_activity' AND gm1.meta_key = 'total_member_count' AND gm3.meta_key = 'gtags_group_tags'  {$hidden_sql} {$search_sql} {$tag_sql} ORDER BY CONVERT(gm1.meta_value, SIGNED) DESC {$pag_sql}" );

	// this is commented out because it doesn't really work due to the over-inclusive issue.
	//$total_groups = $wpdb->get_var( "SELECT COUNT(DISTINCT g.id) FROM {$bp->groups->table_name_groupmeta} gm1, {$bp->groups->table_name_groupmeta} gm2, {$bp->groups->table_name_groupmeta} gm3, {$bp->groups->table_name} g WHERE g.id = gm1.group_id AND g.id = gm2.group_id AND g.id = gm3.group_id AND gm2.meta_key = 'last_activity' AND gm1.meta_key = 'total_member_count' AND gm3.meta_key = 'gtags_group_tags' {$hidden_sql} {$search_sql} {$tag_sql}" ); 

	// loop through results and return only exact matches in comma separated list.
	$paged_groups2 = array();
	
	foreach ( (array)$paged_groups as $group ) {
		$items = explode( ",", $group->gtags_group_tags );
		$match = false;
		foreach( $items as $item ) {
			if ( trim( strtolower( $item ) ) == strtolower( $group_tag ) ) $match = true; 
		}
		if ( $match == true ) 
			$paged_groups2[] = $group;
	}
	
	$total_groups = count($paged_groups2);  // in place of the commented out code above

	foreach ( (array)$paged_groups2 as $group ) $group_ids[] = $group->id;
	$group_ids = $wpdb->escape( join( ',', (array)$group_ids ) );
	$paged_groups2 = BP_Groups_Group::get_group_extras( &$paged_groups2, $group_ids, 'popular' );

	return array( 'groups' => $paged_groups2, 'total' => $total_groups );
}


// Return an array with tag objects
function gtags_make_tags( $urlencode=false, $exclude_tags='', $include_tags='' ) {
	global $bp, $wpdb;
	
	$all_group_tags = $wpdb->get_col( $wpdb->prepare(
		"SELECT meta_value FROM " . $bp->groups->table_name_groupmeta . " WHERE meta_key = 'gtags_group_tags' " ) );

	//count the occurances	
	$all_tags = array();	

	foreach( $all_group_tags as $group_tags ) {
		$items = explode( ',', $group_tags );
		
		foreach( $items as $item ) {
			$item = trim( strtolower( $item ) );
			
			if ( $item=='' ) 
				continue;
			
			if ( isset( $all_tags[ $item ] ) ) { 
				$all_tags[ $item ] += 1;
			} else { 
				$all_tags[ $item ] = 1; 
			}
			
		}
		
	}
	
	
	if ( !$exclude_tags && !$include_tags ) { // get the defaults if nothing is set by the widget
		$exclude_tags = get_option( 'gtags_exclude' );
		$include_tags = get_option( 'gtags_include' );
	}
		
	if ( $exclude_tags ) { // exclude takes precidence
		$exclude_tags = explode( ',', $exclude_tags );
		foreach( (array)$exclude_tags as $exclude ) {
			unset( $all_tags[ trim( $exclude ) ] );
		}	
	} elseif ( $include_tags ) {
		$include_tags = explode( ',', $include_tags );
		foreach( (array)$include_tags as $include ) {
			$include = trim( $include );
			if ($all_tags[ $include ])
				$include_array[ $include ] = $all_tags[ $include ];
		}
		$all_tags = $include_array;	
	}
	
		
	foreach( (array)$all_tags as $tag => $count ) {
		$tag = stripcslashes( $tag );
		$link = $bp->root_domain . '/' . BP_GROUPS_SLUG . '/tag/' . urlencode( $tag ) ;
		$tags[ $tag ] = (object)array( 'name' => $tag, 'count' => $count, 'link' => $link );
	}
	
	return (array)$tags;
}


// show the tags above the group directory (using the built in wp tags function)
function gtags_display_tags() {

	$gtags_dir_cloud = get_option('gtags_dir_cloud'); 
	
	if ( $gtags_dir_cloud=='show' || $gtags_dir_cloud=='link' || !$gtags_dir_cloud ){
		if ( $gtags_dir_cloud=='link' )
			$hide_tag_style = ' style="display:none;" ';
				
		echo '<div id="gtags-top">';
		echo '	<div id="gtags-top-cloud" class="gtags"'.$hide_tag_style.'>'. wp_generate_tag_cloud( gtags_make_tags(), gtags_cloud_args() ).'</div>';
		
		if ( $gtags_dir_cloud=='link' ) {
			// echo '	<div id="gtags-toggle-top"><a href="javascript:void(0);">'.__('Show Group Categories (tag cloud) +','gtags').'</a></div>';
			echo '	<div id="gtags-toggle-top"><a href="javascript:void(0);">'.__('Group Tag Cloud) +','gtags').'</a></div>';
		}
		
		echo '</div>';
	}
}
add_action( 'bp_before_directory_groups_content', 'gtags_display_tags' );


// return tag cloud args array. If nothing is passed get the value stored from the admin. Either way mix with the defaults
// NOTE: EDIT THESE VALUES VIA THE NEW ADMIN, NOT HERE
function gtags_cloud_args( $args = '' ) {

	if ( !$args )
		$args = get_option( 'gtags_cloud_args' );
	
	$defaults = array( 
		'smallest' => 9, 
		'largest' => 20, 
		'number' => 36, 
		'orderby' => 'count', 
		'order' => 'DESC', 
		'separator' => ' ' 
	);

	$r =  wp_parse_args( $args, $defaults );
	//echo '<pre style="background:white">'; print_r( $r ); echo '</pre>';
	return $r;
	//extract( $r ); // use this to pull out the values from the array into local variables
}



// create the form to add tags
function gtags_add_tags_form() {
	global $show_group_add_form;
	if ($show_group_add_form) return;
	$show_group_add_form = true;
	?>
	<label for="group-tags"><?php _e( 'Group Tags', 'gtags' ) ?></label>
	<input type="text" name="group-tags" id="group-tags" value="<?php gtags_group_tags() ?>" /><br>
	<i><?php _e('Separate tags with commas', 'gtags'); ?></i><br>
	<?php 
	gtags_show_tags_chooser();
}
add_action( 'groups_custom_group_fields_editable', 'gtags_add_tags_form' );

// create easy tag adding links. tags added to form via javascript
function gtags_show_tags_chooser(){
	?>
	<div class="gtags_chooser tags_chooser"><?php _e('Click to add common tags', 'gtags'); ?>: <?php echo gtags_show_tags_in_add_form(); ?></div>
	<?php 
}

// show list of the most popular tags - used when adding tags
function gtags_show_tags_in_add_form() {

	if ( !get_option('gtags_popular_limit') ) 
		$popular_tag_limit = 36; 
	else 
		$popular_tag_limit = get_option('gtags_popular_limit');

	$tags = gtags_make_tags();
	uasort( $tags, create_function('$a, $b', 'return ($b->count > $a->count);') );
	
	foreach ( $tags as $tag ) {
		echo ' <span class="gtags-add">' . $tag->name .'</span>';
		++$i;
		if ( $i >= $popular_tag_limit ) break;
	}
	
}



// Save the tag values in the group meta - perhaps use serialize() and maybe_unserialize()
function gtags_save_tags( $group ) { 
	global $bp;
	if ( $_POST['group-tags'] ) {
		$grouptags = str_replace( "\'", "", $_POST['group-tags'] ); // remove single quotes cause they dont work!
		$grouptags =  apply_filters( 'gtags_save_tags', $grouptags );
		groups_update_groupmeta( $group->id, 'gtags_group_tags', $grouptags );
	} elseif ( $bp->action_variables[0] == 'edit-details' ){ // delete the group tags if empty, and we're on the edit details page
		groups_delete_groupmeta( $group->id, 'gtags_group_tags' );
	}
}
add_action( 'groups_group_after_save', 'gtags_save_tags' );





// create the form to add group categories
function gtags_group_category_form() {
	global $bp, $show_group_add_form_cats;	
	if ($show_group_add_form_cats) return; // prevents showing form twice
	$show_group_add_form_cats = true;

	// the group category	
	$group_cats = get_option('gtags_category'); 
	if (empty($group_cats)) return;
	
	$selected = groups_get_groupmeta( $bp->groups->current_group->id, 'gtags_group_cat' ); 
	?><label for="group-cat"><?php _e( 'Group Category', 'gtags' ) ?></label>
	<select name="group-cat" id="group-cat" />
		<option value=""><?php _e('--- SELECT ONE ---', 'gtags') ?></option>
	<?php foreach ( $group_cats as $tag => $desc ): ?>
		<option value="<?php echo $tag; ?>" <?php if ( $tag == $selected ) echo 'selected="selected"' ?>><?php echo $desc; ?></option>
	<?php endforeach; ?>
	</select>
	<i><?php _e('(the primary group activity)', 'gtags'); ?></i><br><?php 	
}
add_action( 'groups_custom_group_fields_editable', 'gtags_group_category_form', 8 );


// Save the cat values as a tag (happens after gtags_save_tags())
function gtags_save_cats( $group ) { 	
	if ( $group_cat = $_POST['group-cat'] ) {
		$gtags = explode( ',', gtags_get_group_tags( $group ) );
		array_walk( $gtags, create_function( '&$a', '$a = trim($a);' ) ); // trim spaces
		$group_cat_prev = groups_get_groupmeta( $group->id, 'gtags_group_cat' );

		if ( $group_cat != $group_cat_prev ) {  // we have a new value this time
			groups_update_groupmeta( $group->id, 'gtags_group_cat', $group_cat ); //save the group category in group meta
			$gtags = array_diff( $gtags, (array)$group_cat_prev ); // remove the prev val with array_diff cause unset sucks
		}

		$gtags[] = $group_cat; // add it
		$gtags = array_unique( $gtags ); // remove duplications
		groups_update_groupmeta( $group->id, 'gtags_group_tags', implode( ', ', $gtags ) );
	}	
}
add_action( 'groups_group_after_save', 'gtags_save_cats', 20 );




// Get or return the tag values
function gtags_group_tags() {
	echo gtags_get_group_tags();
}
	function gtags_get_group_tags( $group = false ) {
		global $groups_template;
		if ( !$group )
			$group =& $groups_template->group;
		$group_tags = groups_get_groupmeta( $group->id, 'gtags_group_tags' );
		$group_tags = stripcslashes( $group_tags );
		return apply_filters( 'gtags_get_group_tags', $group_tags );
	}


// show tags in group directory listing
function gtags_show_tags_in_directory() {
	global $gtags_show_in_dir_list;
	
	// cache the option value because we're doing this 20+ times
	if ( !$gtags_show_in_dir_list ) {
		if ( !$gtags_show_in_dir_list = get_option('gtags_show_in_dir_list') )
			$gtags_show_in_dir_list = 'false'; // the default
	}

	if ( $gtags_show_in_dir_list == 'true' ) { 
		if ( gtags_get_group_tags() ) {
			?><div class="gtags-item-desc gtags-item-tags"><?php _e('Tags', 'gtags'); ?>: <?php echo gtags_make_tags_for_group() ?></div><?php
		}
	}
}
add_action( 'bp_directory_groups_item', 'gtags_show_tags_in_directory' );


// show tags in group header
function gtags_show_tags_in_header( $description ) {
	$gtags_single_header = get_option('gtags_single_header');
	
	if ( $gtags_single_header == 'true' || !$gtags_single_header )  { // default is to show
		if ( gtags_get_group_tags() ) {
			$description .= '<div class="gtags-header">'. __('Tags', 'gtags').': '. gtags_make_tags_for_group().'</div>';
		}
	}
	
	return $description;
}
add_filter( 'bp_get_group_description', 'gtags_show_tags_in_header' );


// show tags for an individual group with links
function gtags_make_tags_for_group() {
	global $bp, $wpdb;	
	
	$group_tags = gtags_get_group_tags();
	
	$items = explode( ",", $group_tags );
	foreach( $items as $item ) {
		$item = trim( strtolower( $item ) );
		if ($item=='') continue;
		$link = $bp->root_domain . '/' . BP_GROUPS_SLUG . '/tag/' . urlencode( $item );
		$output .= ' <a href="'.$link.'">'.$item.'</a> ';
	}
	return $output;	
}



// good 2.8 widget resource http://justintadlock.com/archives/2009/05/26/the-complete-guide-to-creating-widgets-in-wordpress-28

// create a nice new widget class
class GTags_Widget extends WP_Widget {
	function GTags_Widget() {
		$widget_ops = array( 'classname' => 'gtags', 'description' => 'Show a tag cloud for Group Tags' );
		$control_ops = array( 'id_base' => 'gtags-widget' );
		$this->WP_Widget( 'gtags-widget', 'Group Tags', $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters('widget_title', $instance['title'] );
		echo $before_widget;

		if ( $title )
			echo $before_title . $title . $after_title;

		echo '<div class="gtags gtags-widget">';
		echo wp_generate_tag_cloud( gtags_make_tags( null, $instance['exclude'], $instance['include'] ), gtags_cloud_args() );
		echo '</div>';
		echo $after_widget;	
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['exclude'] = $new_instance['exclude'];
		$instance['include'] = $new_instance['include'];
		return $instance;
	}

	function form( $instance ) {
		$defaults = array( 'title' => __('Group Tags', 'gtags') );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		<p><label>Title:<input name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" /></label>
		<p><label><b>Exclude</b> these group tags:<textarea name="<?php echo $this->get_field_name( 'exclude' ); ?>" style="width:100%;"><?php echo $instance['exclude']; ?></textarea></label>
		<p><b>OR</b></p>
		<p><label> <b>Include</b> these group tags:<textarea name="<?php echo $this->get_field_name( 'include' ); ?>" style="width:100%;"><?php echo $instance['include']; ?></textarea></label>
		<?php
	}

}

function gtags_load_widgets() {
	register_widget('GTags_Widget');
	register_widget('GTags_Activity_Widget');
}
add_action( 'widgets_init', 'gtags_load_widgets' );





//
// GROUP TAG ACTIVITY
//


/* 
example of usage: 

// generate group ids from a tag
$group_ids = gtags_group_activity( array( 'tag' => 'my tag name' ) );

// output activity for those group ids
gtags_activity_for_item( array( 'group_ids' => $group_ids ) );

*/


function gtags_get_group_ids( $tag ) {
	if ( !$tag ) return;
	
	$groups_tag = gtags_get_groups_by_tag( '', '', '', '', $tag ); 
	foreach ( $groups_tag['groups'] as $group )
		$group_ids_tag[] = $group->id;	
		
	return $group_ids_tag;
}


// get combined group ids for a more than one tag
function gtags_group_activity( $args ) {
	$defaults = array(
		'tag' => '', // the primary tag name
		'tag2' => '', // the secondary tag name to narrow our search
		'type' => 'intersect' // if two tags are passed, show the intersection or show the combination
	);
	$args = wp_parse_args( $args, $defaults );	
	extract( $args, EXTR_SKIP );
	
	$group_ids_tag = gtags_get_group_ids( $tag );
	$group_ids_tag2 = gtags_get_group_ids( $tag2 );
	
	if ( $tag && $tag2 && $type = 'intersect' )
		$group_ids = array_intersect( (array)$group_ids_tag, (array)$group_ids_tag2 );
	else
		$group_ids = array_merge( (array)$group_ids_tag, (array)$group_ids_tag2 );	
	
	return $group_ids;
}


// add transient caching (with user_id)


// generate list of activities for group_ids and scope passed.
function gtags_activity_for_item( $args ) {
	global $wpdb, $bp;
	
	ob_start();
	
	$defaults = array(
		'group_ids' => '', // an array of group ids. usually build from the gtags_group_activity() function
 		'scope' => '', // use 'my' to only list activity from the user's own groups
		'show' => 4, // number of items to show
		'show_more' => 8, // number of items to show hidden with a more link (better done with ajax?)
		'truncate' => 190 // max characters before truncate the activity feed
	);
	$args = wp_parse_args( $args, $defaults );	
	extract( $args, EXTR_SKIP );

	if ( $scope == 'my' && is_user_logged_in() ) {
		$my_groups = BP_Groups_Member::get_group_ids( $bp->loggedin_user->id );
		$group_ids = array_intersect( $group_ids, $my_groups['groups'] );
		$show_hidden = true;
	}
	
	if ( empty( $group_ids ) ){
		echo '<div class="recent recent-none-found">'.__('Sorry no groups were found.', 'gtags').'</div>';
		return;
	}
	
	?><div class="recent"><?php
	
	// generate source group links	
	foreach ( $group_ids as $group_id ) {
		$group = groups_get_group( array( 'group_id' => $group_id ) );
		$avatar = bp_core_fetch_avatar( 'object=group&type=thumb&width=50&height=50&item_id=' . $group->id );
		$group_output .= $sep . '<a href="' . bp_get_group_permalink( $group ) . '" class="recent-group-avatar">' . $avatar . '&nbsp;' . $group->name .'</a>'; $sep = ', ';
		//compile group data to be used in loop below
		$the_groups[$group->id] = array( 'permalink' => bp_get_group_permalink( $group ), 'name' => bp_get_group_name( $group ), 'avatar' => $avatar );
	} 
	?>
	<div class="gtags-recent-groups">
		<?php _e('Recent Activity From', 'gtags'); ?> <a href="#" class="gtags-more-groups"><?php _e('these groups +', 'gtags'); ?></a>
		<div class="gtags-recent-groups-list"><?php echo $group_output; ?></div>
	</div>
	<?php
				
	// fetch a whole bunch of activity so we can sort them by date below, otherwise they are sorted by group
	$filter = array(
	  	'user_id' => false, // user_id to filter on
	 	'object' => 'groups', // object to filter on e.g. groups, profile, status, friends
	 	'action' => false, // action to filter on e.g. activity_update, profile_updated
	 	'primary_id' => implode( ',', (array)$group_ids ) // object ID to filter on e.g. a group_id or forum_id or blog_id etc.
	);
	$activity = bp_activity_get( array( 'max' => 1000, 'per_page' => 1000, 'filter' => $filter, 'show_hidden' => $show_hidden ) );

	//$type_skip = apply_filters( 'gtags_type_skip', array( 'joined_group' ) ); // array of activity types to skip

	// generate a cleaned array of content
	foreach ( $activity['activities'] as $item ) {
		if ( in_array( $item->type, (array)$type_skip ) )
			continue;
					
		$action = preg_replace( '/:$/', '', $item->action ); // remove trailing colon in activity
		$action = apply_filters( 'gtags_action', $action );	
			
		$content = strip_tags( stripslashes( $item->content ) );
		
		if ( $truncate && strlen( $content ) > $truncate )
			$content = substr( $content, 0, $truncate ) . '... ';
		
		if ( $content )
			$content .= ' &nbsp;<a href="'. $item->primary_link .'">view</a>';

		$activity_list[ $item->date_recorded ] = 
			array( 
				'action' => $action, 
				'group_id' => $item->item_id, 
				'content' => $content, 
				'primary_link' => $item->primary_link, 
				'user_id' => $item->user_id 
			);
	}
	
	if ( empty( $activity_list ) ) {
		echo __("Sorry, there was no activity found.", 'gtags');
		echo "</div>"; //close the div
		return;
	}
	
	// sort them by date (regardless of group)
	ksort( $activity_list );
	$activity_list = array_reverse( $activity_list );

	// output pretty html for recent activity for groups
	foreach ( (array)$activity_list as $date => $item ) : $i++;
		$group_id = $item['group_id'];
		$action = $item['action'];

		// show only a certain amount, after that make a 'show more' link and show the rest in a hidden div 
		if ( $i == $show +1 && $show_more ) :
			?><a href="#" class="gtags-more-activity"><?php _e('show more +', 'gtags'); ?></a>
			<div class="gtags-more-content"><?php 
			$more_link = true;
		endif;
		
		if ( $i > $show + $show_more +1 ) break;

		// for repeating group content, remove group link and shrink group avatar
		if ( $prev_group_id == $group_id ) {
			$action = preg_replace( '/ in the group(.*)$/i', '', $action ); 
			$dup_class = ' duplicate-group';
		} else {
			$dup_class = '';
		}
		$prev_group_id = $group_id;
							
		// group avatar
		echo '<a href="'. $the_groups[$group_id]['permalink'] . '" title="' . $the_groups[$group_id]['name'] . '" class="gtags-item-group-avatar' . $dup_class . '">' . $the_groups[$group_id]['avatar'] . '</a>';		
		// the actual content		
		?><div class="gtags-item-recent group">
			<div class="gtags-item-avatar">
				<a href="<?php echo bp_core_get_user_domain( $item['user_id'] ) ?>">
					<?php echo bp_core_fetch_avatar( 'object=user&type=full&width=50&height=50&item_id=' . $item['user_id'] ) ?>
				</a>
			</div>
			<div class="gtags-item-action">
				<?php echo $action; ?> 
				<span class="gtags-time-ago"><?php echo bp_core_time_since( $date ) ?> <?php _e('ago', 'gtags'); ?></span>
			</div>
			<div class="gtags-item-content">
				<?php echo convert_smilies( $item['content'] ); ?>
			</div>
		</div><?php
		
	endforeach; 
	
	if ( $more_link ) {
		echo '<div class="gtags-recent-groups"> '.__('Continue reading in:', 'gtags').' '. $group_output .'</div>'; 
		echo '</div>'; // close the more div
	} 
	
	?></div><?php // end recent
	
	return ob_get_clean();
}


// shorten the action title
function gtags_action_clean( $action ) {
	$action = str_replace( ' started the discussion topic', ' started', $action );
	$action = str_replace( ' posted on the discussion topic', ' posted on', $action );	
	return $action;
}
add_filter( 'gtags_action', 'gtags_action_clean' );



// create a nice new widget class
class GTags_Activity_Widget extends WP_Widget {
	function GTags_Activity_Widget() {
		$widget_ops = array( 'classname' => 'gtags_activity', 'description' => 'Show a activity for all groups matching a tag.' );
		$control_ops = array( 'id_base' => 'gtags-activity-widget' );
		$this->WP_Widget( 'gtags-activity-widget', 'Group Tags Activity', $widget_ops, $control_ops );
	}
	
	function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters('widget_title', $instance['title'] );
		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;
		echo '<div class="gtags gtags-activity-widget">';
		$group_ids = gtags_group_activity( array( 'tag' => $instance['tag']/* , 'tag2' => $instance['tag2'], 'type' => $instance['type'] */ ) );
		echo gtags_activity_for_item( array( 'group_ids' => $group_ids, 'show' => $instance['show'], 'show_more' => 0, /* , 'scope' => $scope, 'truncate' => $truncate */ ) );
		echo '</div>';
		echo $after_widget;	
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['tag'] = $new_instance['tag'];
		$instance['show'] = $new_instance['show'];
		//$instance['tag2'] = $new_instance['tag2'];
		//$instance['type'] = $new_instance['type'];
		return $instance;
	}

	function form( $instance ) {
		$defaults = array( 'title' => __('Group Tag Activity', 'gtags'), 'show' => 3 );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		<p><label>Title:<input name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" /></label>
		<p><label><b>Group Tag</b> (required):<input type="text" name="<?php echo $this->get_field_name('tag'); ?>" style="width:100%;" value="<?php echo $instance['tag']; ?>" /></label>
<!-- 		<p><label>Second Group Tag:<input type="text" name="<?php echo $this->get_field_name('tag2'); ?>" style="width:100%;" value="<?php echo $instance['tag2']; ?>" /></label> -->
		<p><label>Items to show:<input type="text" name="<?php echo $this->get_field_name('show'); ?>" style="width:30%;" value="<?php echo $instance['show']; ?>" /></label>
		<?php
	}

}

// activity shortcode
function gtags_activity_shortcode($atts) {
     extract(shortcode_atts(array(
	      'tag' => '',
	      'show' => '6',
	      'show_more' => '12',
     ), $atts));
	$group_ids = gtags_group_activity( array( 'tag' => $tag ) );
	return gtags_activity_for_item( array( 'group_ids' => $group_ids, 'show' => $show, 'show_more' => $show_more ) );
}
add_shortcode('group_tag_activity', 'gtags_activity_shortcode');





// setting for the admin page
function gtags_add_admin_menu() {
	global $bp;
	if ( !is_super_admin() )
		return false;
	require ( dirname( __FILE__ ) . '/admin.php' );
	add_submenu_page( 'bp-general-settings', 'Group Tags', 'Group Tags', 'manage_options', 'gtags_admin', 'gtags_admin' );
}
//add_action( 'admin_menu', 'gtags_add_admin_menu', 20 );
add_action( is_multisite() ? 'network_admin_menu' : 'admin_menu', 'gtags_add_admin_menu', 20 );

?>