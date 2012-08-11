<?php
function setupgallery_pages(){
global $user_ID;
$new_post = array(
'post_title' => 'Contact',
'post_status' => 'publish',
'post_author' => $user_ID,
'post_type' => 'page'
);

$post_id = wp_insert_post($new_post);

add_post_meta($post_id, '_wp_page_template', 'template-contact.php');

$new_post2 = array(
'post_title' => 'Example exhibition',
'post_status' => 'publish',
'post_author' => $user_ID,
'post_type' => 'exhibition'
);

$post_id2 = wp_insert_post($new_post2);
}

global $pagenow;
if ( is_admin() && 'themes.php' == $pagenow && isset( $_GET['activated'] ) ) {
	$page = get_page_by_title('Contact', 'ARRAY_A');
	if( ! $page )
	{
		setupgallery_pages();
	}
}
?>