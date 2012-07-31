<?php

// Bump this when changes are made to bust cache
$version = '20120730';

/* Register styles */
function my_styles_method()  
{ 
	wp_enqueue_style( 'type', get_stylesheet_directory_uri() . '/_inc/css/type.css' ); // Inside a child theme
	wp_enqueue_style( 'events', get_stylesheet_directory_uri() . '/_inc/css/events.css' ); // Inside a child theme

  // enqueing
  wp_enqueue_style( 'type' );
  wp_enqueue_style( 'events' );
}
add_action('wp_enqueue_scripts', 'my_styles_method', 1);


// Bump this when changes are made to bust cache
$version = '20120730';


// Send user email address to CiviCRM so they can be added to lists
function send_email_to_civi($user_id, $old_user_data) {
    if ( defined( 'CIVICRM_EMAIL_POST_URL' ) && defined( 'CIVICRM_EMAIL_GROUP_ID' ) ) {
        $info = get_userdata( $user_id );
        $email = $info->user_email;
        $data = array('postURL' => '',
                      'cancelURL' => '',
                      'add_to_group' => CIVICRM_EMAIL_GROUP_ID,
                      'email-Primary' => $email,
                      '_qf_default' => '',
                      '_qf_Edit_next' => '');

        make_http_post_request( CIVICRM_EMAIL_POST_URL, $data );
    }
}
add_action( 'user_register', 'send_email_to_civi' );
//add_action( 'profile_update', 'send_email_to_civi' ); // this doesn't seem to have any effect - further investigation is needed

/* Add Cool Buttons to Activity Stream Items */
function my_bp_activity_entry_meta() {
 
    if ( bp_get_activity_object_name() == 'blogs' && bp_get_activity_type() == 'new_blog_post' ) {?>
        <a class="view-post" href="<?php bp_activity_thread_permalink() ?>">View Blog Post</a>
    <?php }
 
    if ( bp_get_activity_object_name() == 'blogs' && bp_get_activity_type() == 'new_blog_comment' ) {?>
        <a class="view-post" href="<?php bp_activity_thread_permalink() ?>">View Blog Comment</a>
    <?php }
 
    if ( bp_get_activity_object_name() == 'activity' && bp_get_activity_type() == 'activity_update' ) {?>
        <a class="view-post" href="<?php bp_activity_thread_permalink() ?>">View Activity Status</a>
    <?php }
 
        if ( bp_get_activity_object_name() == 'groups' && bp_get_activity_type() == 'new_forum_topic' ) {?>
        <a class="view-thread" href="<?php bp_activity_thread_permalink() ?>">View Forum Thread</a>
    <?php }
 
        if ( bp_get_activity_object_name() == 'groups' && bp_get_activity_type() == 'new_forum_post' ) {?>
        <a class="view-post" href="<?php bp_activity_thread_permalink() ?>">View Forum Reply</a>
    <?php }
 
}
add_action('bp_activity_entry_meta', 'my_bp_activity_entry_meta');



// Register announcement widget
register_sidebar(
	array(
		'name' => 'Announcement',
		'id' => 'announcement',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widgettitle">',
		'after_title' => '</h3>'
	)
);

//Remove paragraph tags from excerpt
remove_filter('the_excerpt', 'wpautop');

//Enable short codes in widgets
add_filter('widget_text', 'do_shortcode');

//Change default group sort order to alpha
function sort_alpha_by_default( $qs ) {
	global $bp;
	if (!$qs && ( $bp->current_component == BP_GROUPS_SLUG || $bp->current_component == BP_MEMBERS_SLUG ) )
		$qs = 'type=alphabetical&action=alphabetical';
	return $qs;
}
add_filter( 'bp_dtheme_ajax_querystring', 'sort_alpha_by_default' );


?>