<?php
define('SM_CLICK_TO_STICK_URL', plugins_url('/', __FILE__));

add_action( 'post_submitbox_misc_actions', 'sm_sticky_meta' );

function sm_sticky_meta() { 
	global $post;
	echo '<div id="smSticky" class="misc-pub-section ">Make Sticky: '.get_sm_sticky_link($post->ID).'</div>';
}


add_filter('manage_posts_columns', 'sm_add_sticky_column');
function sm_add_sticky_column ($columns) {
    $columns['sticky'] = 'Sticky';
    return $columns;
}

add_action('manage_posts_custom_column',  'sm_sticky_column_content');
function sm_sticky_column_content($name) {
    global $post;
    if($name=='sticky')
		echo get_sm_sticky_link($post->ID);
}

function get_sm_sticky_link($thePostID = '') {
	global $post;
	if($thePostID == '')
		$thePostID = $post->ID;
	$stickyClass = '';
	$stickyTitle = 'Make Sticky';
	if(is_sticky($thePostID)) {
		$stickyClass = 'isSticky';
		$stickyTitle = 'Remove Sticky';
	}
	$stickyLink = '<a href="id='.$thePostID.'&code='.wp_create_nonce('sm-sticky-nonce').'" id="smClickToStick'.$thePostID.'" class="smClickToStick '.$stickyClass.'" title="'.$stickyTitle.'"></a>';
	return $stickyLink;
}


add_action('wp_ajax_sm_sticky', 'sm_sticky_callback');

function sm_sticky_callback() {
	 if ( !wp_verify_nonce( $_POST['code'], 'sm-sticky-nonce' ) ) {
     	// failed nonce validation
		echo 'failed nonce: '.$_POST['anthem_nonce'];die();
	 }
	 
  	$stickyPosts = get_option('sticky_posts');
	
	if(!is_array($stickyPosts))
		$stickyPosts = array();
	
	if (in_array($_POST['id'], $stickyPosts)) {
		$removeKey = array_search($_POST['id'], $stickyPosts);
		unset($stickyPosts[$removeKey]);
		$stickyResult = 'removed';
	}
	else {
		array_unshift($stickyPosts, $_POST['id']);
		//$stickyPost[] = $_POST['id'];
		$stickyResult = 'added';
	}
		
	if(update_option('sticky_posts', $stickyPosts))
		echo $stickyResult;
	else 
		echo 'An error occured';
	
	die(); // this is required to return a proper result
}


//add admin stylesheet
function sm_click_to_stick_styles() {
	wp_enqueue_style('sm_click_to_stick_styles', SM_CLICK_TO_STICK_URL.'css/sm-click-to-stick.css', array(), '1.0.0', 'all');
}
add_action('admin_print_styles', 'sm_click_to_stick_styles');

// add admin javascript
function sm_click_to_stick_scripts() {
	wp_enqueue_script( 'sm_click_to_stick_scripts', SM_CLICK_TO_STICK_URL.'js/sm-click-to-stick.js', array('jquery') );
}
add_action( 'admin_init', 'sm_click_to_stick_scripts' );