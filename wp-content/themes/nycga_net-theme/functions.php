<?php

// Bump this when changes are made to bust cache
$version = '20120732';

/* Register styles */
function my_styles_method()  
{ 
	wp_enqueue_style( 'nycga', get_stylesheet_directory_uri() . '/_inc/css/nycga_core.css' ); // Inside a child theme
	wp_enqueue_style( 'type', get_stylesheet_directory_uri() . '/_inc/css/type.css' ); // Inside a child theme
	wp_enqueue_style( 'font', 'http://fonts.googleapis.com/css?family=Oswald' ); // Inside a child theme
	wp_enqueue_style( 'events', get_stylesheet_directory_uri() . '/_inc/css/events.css' ); // Inside a child theme
	wp_enqueue_style( 'chosenform', get_stylesheet_directory_uri() . '/_inc/js/chosen.css' ); // Inside a child theme

  // enqueing
  wp_enqueue_style( 'nycga' );
  wp_enqueue_style( 'type' );
  wp_enqueue_style( 'events' );
  wp_enqueue_style( 'chosenform' );
}
add_action('wp_enqueue_scripts', 'my_styles_method', 1);

// Register scripts
function my_scripts_method() {  
    wp_register_script( 'jquery-cycle', get_stylesheet_directory_uri() . '/_inc/js/jquery.cycle.all.2.72.js' ); // Inside a child theme
    wp_enqueue_script('jquery-cycle');
}  
add_action('init', 'my_scripts_method');  

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

// Register homepage top left-column widget
register_sidebar(
	array(
		'name' => __('top-left-column', TEMPLATE_DOMAIN),
      'id'            => 'top-left-column',
			'description'   => 'Top Left Column Widget',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
      'after_widget'  => '</div>',
      'before_title'  => '<h2 class="widgettitle">',
      'after_title'   => '</h2>'
	)
);
add_action( 'init', 'top-left-column' );

// Register homepage top right-column widget
register_sidebar(
	array(
		'name' => __('top-right-column', TEMPLATE_DOMAIN),
      'id'            => 'top-right-column',
			'description'   => 'Top Right Column Widget',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
      'after_widget'  => '</div>',
      'before_title'  => '<h2 class="widgettitle">',
      'after_title'   => '</h2>'
	)
);
add_action( 'init', 'top-right-column' );



// Register announcement widget
/*
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

*/
add_action( 'init', 'register_announcement' );

function register_announcement() {

    $labels = array( 
        'name' => _x( 'Announcements', 'announcement' ),
        'singular_name' => _x( 'Announcement', 'announcement' ),
        'add_new' => _x( 'Add New', 'announcement' ),
        'add_new_item' => _x( 'Add New Announcement', 'announcement' ),
        'edit_item' => _x( 'Edit Announcement', 'announcement' ),
        'new_item' => _x( 'New Announcement', 'announcement' ),
        'view_item' => _x( 'View Announcement', 'announcement' ),
        'search_items' => _x( 'Search Announcements', 'announcement' ),
        'not_found' => _x( 'No announcements found', 'announcement' ),
        'not_found_in_trash' => _x( 'No announcements found in Trash', 'announcement' ),
        'parent_item_colon' => _x( 'Parent Announcement:', 'announcement' ),
        'menu_name' => _x( 'Announcements', 'announcement' ),
    );

    $args = array( 
        'labels' => $labels,
        'hierarchical' => true,
        'supports' => array( 'title', 'editor' ),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 20,
        //'menu_icon' => '/wp-admin/images/saw-menu-icon.png',
        'show_in_nav_menus' => true,
        'publicly_queryable' => false,
        'exclude_from_search' => true,
        'has_archive' => false,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => false,
        'capability_type' => 'post'
    );

    register_post_type( 'announcement', $args );
}

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

//Add group list custom field

/* Define the custom box */

add_action( 'add_meta_boxes', 'myplugin_add_custom_box' );

// backwards compatible (before WP 3.0)
// add_action( 'admin_init', 'myplugin_add_custom_box', 1 );

/* Do something with the data entered */
add_action( 'save_post', 'myplugin_save_postdata' );

/* Adds a box to the main column on the Post and Page edit screens */
function myplugin_add_custom_box() {
    add_meta_box( 
        'myplugin_sectionid',
        __( 'Working Group', 'myplugin_textdomain' ),
        'myplugin_inner_custom_box',
        'post' 
    );
    add_meta_box(
        'myplugin_sectionid',
        __( 'Working Group', 'myplugin_textdomain' ), 
        'myplugin_inner_custom_box',
        'page'
    );
}

/* Prints the box content */
function myplugin_inner_custom_box( $post ) {

  // Use nonce for verification
  wp_nonce_field( plugin_basename( __FILE__ ), 'myplugin_noncename' );

  // The actual fields for data entry
  if ( bp_has_groups('type=alphabetical') ) :
	  echo '<select name="event_group" class="groups">';
	  echo '<label for="event_group">';
	       _e("Working Group", 'myplugin_textdomain' );
	  echo '</label> ';
	  echo '<option value="">' . _e('Select a Group','tribe-events-calendar') . '</option>';
	  while ( bp_groups() ) : bp_the_group();
		  echo '<option value="' . bp_group_id() . '">' . bp_group_name() .'</option>';
	  endwhile;
	  echo '</select>';
  endif;
}

/* When the post is saved, saves our custom data */
function myplugin_save_postdata( $post_id ) {
  // verify if this is an auto save routine. 
  // If it is our form has not been submitted, so we dont want to do anything
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
      return;

  // verify this came from the our screen and with proper authorization,
  // because save_post can be triggered at other times

  if ( !wp_verify_nonce( $_POST['myplugin_noncename'], plugin_basename( __FILE__ ) ) )
      return;

  
  // Check permissions
  if ( 'page' == $_POST['post_type'] ) 
  {
    if ( !current_user_can( 'edit_page', $post_id ) )
        return;
  }
  else
  {
    if ( !current_user_can( 'edit_post', $post_id ) )
        return;
  }

  // OK, we're authenticated: we need to find and save the data

  $mydata = $_POST['myplugin_new_field'];

  // Do something with $mydata 
  // probably using add_post_meta(), update_post_meta(), or 
  // a custom table (see Further Reading section below)
}

//Remove Buddypress search drowpdown for selecting members etc
add_filter('bp_search_form_type_select', 'bpmag_remove_search_dropdown'  );
function bpmag_remove_search_dropdown($select_html){
    return '';
}

//Force buddypress to not process the search/redirect
remove_action( 'bp_init', 'bp_core_action_search_site', 7 );

//Handle the unified page ourself
add_action( 'init', 'bp_buddydev_search', 10 );// custom handler for the search
function bp_buddydev_search(){
global $bp;
    if ( bp_is_current_component(BP_SEARCH_SLUG) )//if thids is search page
        bp_core_load_template( apply_filters( 'bp_core_template_search_template', 'search-single' ) );//load the single searh template
}

//Tweak query string
add_action('advance-search','bpmag_show_search_results',1);//highest priority
/* we just need to filter the query and change search_term=The search text*/
function bpmag_show_search_results(){
    //filter the ajaxquerystring
     add_filter('bp_ajax_querystring','bpmag_global_search_qs',100,2);
}
 
 //modify the query string with the search term
function bpmag_global_search_qs(){
    return 'search_terms='.$_REQUEST['search-terms'];
}
 
//a utility function
function bpmag_is_advance_search(){
global $bp;
if(bp_is_current_component( BP_SEARCH_SLUG))
    return true;
return false;
}

//Show group search
function bpmag_show_groups_search(){
    ?>
<div class="groups-search-result search-result">
    <h2 class="content-title"><?php _e('Group Search','bpmag');?></h2>
    <?php locate_template( array('groups/groups-loop.php' ), true ) ;  ?>
 
        <a href="<?php echo bp_get_root_domain().'/'.  bp_get_groups_slug().'/?s='.$_REQUEST['search-terms']?>" ><?php _e("View All matched Groups","bpmag");?></a>
</div>
    <?php
 //endif;
  }
 
//Hook Groups results to search page
 if(bp_is_active( 'groups' ))
    add_action('advance-search','bpmag_show_groups_search',15);

//Show Member Search
function bpmag_show_member_search(){
    ?>
   <div class="members-search-result search-result">
   <h2 class="content-title"><?php _e('Members Results',"bpmag");?></h2>
  <?php locate_template( array( 'members/members-loop.php' ), true ) ;  ?>
  <?php global $members_template;
    if($members_template->total_member_count>1):?>
   <a href="<?php echo bp_get_root_domain().'/'.  bp_get_members_slug().'/?s='.$_REQUEST['search-terms']?>" ><?php _e(sprintf('View all %d matched Members',$members_template->total_member_count),"bpmag");?></a>
    <?php    endif; ?>
    </div>
<?php
 }
//Hook Member results to search page
add_action('advance-search','bpmag_show_member_search',10); //the priority defines where in page this result will show up(the order of member search in other searches)

//Show multisite blog posts
function bpmag_show_blogs_search(){
 
    ?>
  <div class="blogs-search-result search-result">
  <h2 class="content-title"><?php _e('Blogs Search',"bpmag");?></h2>
  <?php locate_template( array( 'blogs/blogs-loop.php' ), true ) ;  ?>
  <a href="<?php echo bp_get_root_domain().'/'. bp_get_blogs_slug().'/?s='.$_REQUEST['search-terms']?>" ><?php _e("View All matched Blogs","bpmag");?></a>
 </div>
  <?php
  }
 
//Hook Blogs results to search page if blogs comonent is active
 if(bp_is_active( 'blogs' ))
    add_action('advance-search','bpmag_show_blogs_search',30);

//Show activity
function bpmag_show_activity_search(){
    ?>
<div class="activity-search-result search-result">
    <h2 class="content-title"><?php _e('Activity Updates','bpmag');?></h2>
    <?php locate_template( array('activity/activity-loop.php' ), true ) ;  ?>
 
        <a href="<?php echo bp_get_root_domain().'/'.  bp_get_activity_slug().'/?s='.$_REQUEST['search-terms']?>" ><?php _e("View all matched updates","bpmag");?></a>
</div>
    <?php
 //endif;
  }
 
//Hook Activity results to search page
 if(bp_is_active( 'activity' ))
    add_action('advance-search','bpmag_show_activity_search',20);    

//Show forums
function bpmag_show_bbpress_topic_search(){
     $_REQUEST['ts']=$_REQUEST['search-terms'];//put it for bbpress topic search
    ?>
  <div class="bbp-topic-search-result search-result">
  <h2 class="content-title"><?php _e('Global Topic Search',"bpmag");?></h2>
  <?php bbp_get_template_part('bbpress/content','archive-topic') ;  ?>
  <?php
  global $bbp;
    $page = bbp_get_page_by_path( $bbp->root_slug );
 
  ?>
  <a href="<?php echo get_permalink($page).'?ts='.$_REQUEST['search-terms']?>" ><?php _e("View All matched topics","bpmag");?></a>
 </div>
  <?php
  }
 
//Hook Blogs results to search page if blogs comonent is active
 if(function_exists( 'bbp_has_topics' ))
    add_action('advance-search','bpmag_show_bbpress_topic_search',40);

//Chosen

/**
 * WordPress Chosen Taxonomy Metabox
 * Author: Helen Hou-Sandi
 *
 * Use Chosen for a replacement taxonomy metabox in WordPress
 * Useful for taxonomies that aren't changed much on the fly and are
 * non-hierarchical in nature, as Chosen is for flat selection only.
 * You can always use the taxonomy admin screen to add/edit taxonomy terms.
 * Categories need slightly different treatment from the rest in order to
 * leverage the correct form field name for saving without interference.
 *
 * Example screenshot: http://cl.ly/2T2D232x172G353i2V2C
 *
 * Chosen: http://harvesthq.github.com/chosen/
 */

add_action( 'add_meta_boxes', 'hhs_add_meta_boxes' );
function hhs_add_meta_boxes() {
	remove_meta_box( 'tagsdiv-post_tag', 'post', 'side' );
	remove_meta_box( 'categorydiv', 'post', 'side' );
	remove_meta_box( 'customtaxdiv', 'post', 'side' );

	add_meta_box( 'chosen-tax', 'Choose Terms', 'hhs_chosen_tax_meta_box_display', 'post', 'side', 'default' );
}

function hhs_chosen_tax_meta_box_display() {
	global $post;
	?>
	<script type="text/javascript">
	jQuery(document).ready(function($){
		$( '.chzn-select' ).chosen();
	});
	</script>
	<?php
	// which taxonomies should be used - can be multiple
	$taxes = array(
		'post_tag' => 'Tags',
		'category' => 'Categories',
		'customtax' => 'eab_events_category',
	);
	
	foreach ( $taxes as $tax => $label ) {
		/**
		 * You may want to check if the current user can assign terms to
		 * the taxonomy in question before going any further.
		 * It will not save their assignments anyway, but it's a bit of a
		 * funny user experience.
		 */

		// add more args if you want (e.g. orderby)
		$terms = get_terms( $tax, array( 'hide_empty' => 0 ) );
		$current_terms = wp_get_post_terms ( $post->ID, $tax, array('fields' => 'ids') );

		if ( 'category' == $tax )
			$name = 'post_category';
		else
			$name = "tax_input[$tax]";
		?>
		<p><label for="<?php echo $tax; ?>"><?php echo $label; ?></label>:</p>
		<p><select name="<?php echo $name; ?>[]" class="chzn-select widefat" data-placeholder="Select" multiple="multiple">
		<?php foreach ( $terms as $term ) { ?>
			<option value="<?php echo $term->term_id; ?>"<?php selected( in_array( $term->term_id, $current_terms ) ); ?>><?php echo $term->name; ?></option>
		<?php } ?>
		</select>
		</p>
		<?php
	}
}

// Chosen JS and CSS enqueue - assumes you are doing this in a theme
// with the JS, CSS, and sprite files in themefolder/js/chosen/
// You'd want to use plugins_url() instead if using this in a plugin
add_action( 'admin_enqueue_scripts', 'hhs_add_admin_scripts', 10, 1 );
function hhs_add_admin_scripts( $hook ) {
	global $post;

	if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
		if ( 'post' === $post->post_type ) {     
			wp_enqueue_script(  'chosen', get_stylesheet_directory_uri().'/_inc/js/chosen.jquery.min.js', array( 'jquery' ), '1.0' );
			wp_enqueue_style( 'chosen', get_stylesheet_directory_uri().'/_inc_js/chosen.css' );
		}
	}
}

// Improved excerpt function
function improved_trim_excerpt($text) {
    global $post;
    if ( '' == $text ) {
        $text = get_the_content('');
        $text = apply_filters('the_content', $text);
        $text = str_replace('\]\]\>', ']]&gt;', $text);
        $text = preg_replace('@<script[^>]*?>.*?</script>@si', '', $text);
        $text = strip_tags($text, '<p>');
        $text = strip_tags($text, '<a>');
        $excerpt_length = 80;
        $words = explode(' ', $text, $excerpt_length + 1);
        if (count($words)> $excerpt_length) {
                array_pop($words);
                array_push($words, '[…]');
                $text = implode(' ', $words);
        }
    }
    return $text;
}




