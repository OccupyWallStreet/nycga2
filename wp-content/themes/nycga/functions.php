<?php

function bbg_change_home_tab_name() {
  global $bp;

  if ( bp_is_group() ) {
    $bp->bp_options_nav[bp_get_current_group_slug()]['home']['name'] = 'Activity';
  }
}
add_action( 'groups_setup_nav', 'bbg_change_home_tab_name' );


function my_bp_search_form_type_select() {
	global $bp;

	$options = array();

	if ( bp_is_active( 'groups' ) )
		$options['groups']  = __( 'Groups',  'buddypress' );
		
//	$options['events'] = __( 'Events', 'buddypress' );

	if ( bp_is_active( 'xprofile' ) )
		$options['members'] = __( 'Members', 'buddypress' );

	if ( bp_is_active( 'forums' ) && bp_forums_is_installed_correctly() && bp_forums_has_directory() )
		$options['forums']  = __( 'Forums',  'buddypress' );

	$options['posts'] = __( 'Posts', 'buddypress' );

	// Eventually this won't be needed and a page will be built to integrate all search results.
	$selection_box  = '<label for="search-which" class="accessibly-hidden">' . __( 'Search these:', 'buddypress' ) . '</label>';
	$selection_box .= '<select name="search-which" id="search-which" style="width: auto">';

	$options = apply_filters( 'bp_search_form_type_select_options', $options );
	foreach( (array)$options as $option_value => $option_title ) {
		$selection_box .= sprintf( '<option id="%s" value="%s">%s</option>', $option_value . "-dropdown-option", $option_value, $option_title );

	}

	$selection_box .= '</select>';
	return $selection_box;

}
add_filter('bp_search_form_type_select','my_bp_search_form_type_select');


function add_script() {
   if (!is_admin()) {
       // comment out the next two lines to load the local copy of jQuery
       	// wp_deregister_script('jquery');
       	wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js', false, '1.5.2');
		wp_enqueue_script('jquery');
		wp_enqueue_script('toggler', get_bloginfo('url') . '/wp-content/js/hide-form/toggler.js');
		}
	}

add_action('init', 'add_script');

add_action('wp_footer', 'add_search_form_script');

function add_search_form_script() {
	?>
	<script>
	// $(document).ready(function() {
	// 	$('#other').click(function() {
	// 	  $('#target').click();
	// 	});
	// }
	// );
	</script>
	<?php
}




register_sidebar(
	array(
		'name' => 'Hero-login',
		'id' => 'blurb_login',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widgettitle">',
		'after_title' => '</h3>'
	)
);

register_sidebar(
	array(
		'name' => 'Hero-no-login',
		'id' => 'blurb',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widgettitle">',
		'after_title' => '</h3>'
	)
);

register_sidebar(
	array(
		'name' => 'Bulletin-Main',
		'id' => 'sidebar-2',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widgettitle">',
		'after_title' => '</h3>'
	)
);


register_sidebar(
	array(
		'name' => 'Bulletin-side1',
		'id' => 'sidebar-3',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widgettitle">',
		'after_title' => '</h3>'
	)
);

register_sidebar(
	array(
		'name' => 'Bulletin-side2',
		'id' => 'sidebar-4',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widgettitle">',
		'after_title' => '</h3>'
	)
);


// register_sidebar(
// 	array(
// 		'name' => 'Widgeted Page',
// 		'id' => 'centerwidget-page',
// 		'before_widget' => '<div id="%1$s" class="widget %2$s">',
// 		'after_widget' => '</div>',
// 		'before_title' => '<h3 class="widgettitle">',
// 		'after_title' => '</h3>'
// 	)
// );



	


function change_activity_plus_root_folder() {	
	echo "<script>
	var _bpfbRootUrl = '" . get_stylesheet_directory_uri().  "';
	</script>";
}

add_action('wp_head','change_activity_plus_root_folder');


// add_action('init', 'redirect_to_parent_event_if_on_child');

// function redirect_to_parent_event_if_on_child() {
// 	if(! strpos($_SERVER['REQUEST_URI'], 'my-events/edit'))
// 		return;
// 	$event_id= substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], 'event_id')+9);

// 	global $wpdb;
// 	$recurrence_id = $wpdb->get_results("SELECT recurrence_id, recurrence FROM wp_em_events WHERE event_id='{$event_id}'");

// 	if($recurrence_id[0]->recurrence != "1" ) {
// 		$rewritten_link = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '?'));
// 		$rewritten_link = site_url() . $rewritten_link .'?event_id=' . $event_id;
// 	}
		
// }


// uncomment line below to add a memory usage statistic to the footer of the page 
// add_action('wp_footer', 'nycga_check_php_mem_usage'); 
function nycga_check_php_mem_usage()
{
	function convert($size)
	{
		$unit = array('b','kb','mb','gb','tb','pb');
		return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
	}

	echo convert(memory_get_peak_usage(true)); // 123 kb
}

// remove 'My Sites' menu except for admins
add_action('init', 'nycga_remove_my_sites_menu', 100);
function nycga_remove_my_sites_menu()
{
	if ( ! is_super_admin())
	{
		global $current_user;
		get_currentuserinfo();
		if (empty($current_user->roles) || (count($current_user->roles) == 1 && in_array('subscriber', $current_user->roles)))
		{
			remove_action( 'bp_adminbar_menus', 'bp_adminbar_blogs_menu', 6 );
		}
	}
}

// remove dashboard access except for admins
add_action('admin_init', 'nycga_remove_dashboard_access', 100);
function nycga_remove_dashboard_access()
{
	if ( ! is_super_admin())
	{
		global $current_user;
		get_currentuserinfo();
		if (empty($current_user->roles) || (count($current_user->roles) == 1 && in_array('subscriber', $current_user->roles)))
		{
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
	}
}

// include events mods if events plugin is enabled
if (defined('EM_VERSION'))
{
	require_once(dirname(__FILE__) . '/functions-events.php');
}

// hide certain activities from activity feed
add_action('bp_has_activities', 'nycga_hidden_activities', 10, 2 );
function nycga_hidden_activities($a, $activities)
{
	$hidden = array('joined_group', 'new_member');
	
	foreach ($activities->activities as $key => $activity) 
	{
		// only remove the specified items if that activity type has not specifically been chosen
		if ( ! in_array($_COOKIE['bp-activity-filter'], $hidden) && in_array($activity->type, $hidden, true)) 
		{
			unset($activities->activities[$key]);
			$activities->activity_count = $activities->activity_count - 1;
			$activities->total_activity_count = $activities->total_activity_count - 1;
		}
	}
	
	// Renumber the array keys to account for missing items.
	$activities_new = array_values( $activities->activities );
	$activities->activities = $activities_new;
	
	return $activities;
}

add_action('bp_before_group_manage_members_admin', 'nycga_modify_group_membership_by_username');
function nycga_modify_group_membership_by_username() {
	echo "<div class='bp-widget'>
		<h4>Promote to admin</h4>
                Username:
		<input id='promoteusername' style='width: 150px' type='text'></input>
		<a id='promoteusercustomlink' href='";
	bp_group_member_promote_admin_link();
	echo "' class='button member-promote-to-admin custom-member-promote-to-admin' title='Promote to Admin'>Promote to Admin</a>
              </div>
		<script type='text/javascript'>
			jQuery(document).ready(function() {
				jQuery('a.custom-member-promote-to-admin').click(function() {
					var button = jQuery('#promoteusercustomlink');
					if (button.hasClass('disabled')) {
						return false;
					}
					button.addClass('disabled');
					var username = jQuery('#promoteusername').val();
					var groupname = jQuery('#promoteusercustomlink').attr('href').split('/')[4];
					jQuery.get('/wp-content/themes/nycga/forcejoingroupandreturnuserid.php',
						  {'username':username, 'groupname':groupname},
						  function(data) {
							data = jQuery.trim(data);
							if (!parseInt(data)) {
								alert(data);
								jQuery('#promoteusercustomlink').removeClass('disabled');
							} else {
								var uid = parseInt(data);
								var promotelink = jQuery('#promoteusercustomlink').attr('href').replace('?_wpnonce', uid + '?_wpnonce');
								window.location = promotelink;
							}
						  }, 'text');
					return false;
				});
			});
		</script>
	";
}


//allow hard-hiding of inactive groups
function bbg_redirect_from_inactive_group() { 
if ( bp_is_group() ) { 
if ( 'inactive' == groups_get_groupmeta( bp_get_current_group_id(), 'active_status' ) && !is_super_admin() ) { 
bp_core_redirect( "http://nycga.net/groups" ); 
} 
} 
}
add_action( 'bp_actions', 'bbg_redirect_from_inactive_group', 1 );
