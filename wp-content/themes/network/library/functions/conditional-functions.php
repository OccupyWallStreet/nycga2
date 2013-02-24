<?php
///////////////////////////////////////////////////////////////////////////////
// global check if bp_exist so it wont't break in single wp/wpmu or new wp 3.0
//////////////////////////////////////////////////////////////////////////////

if ( function_exists( 'bp_exists' ) ) {
  //check if multisite
  global $blog_id;
	global $current_blog;
  if ( is_multisite() ) {
    //check if multiblog
    if ( defined( 'BP_ENABLE_MULTIBLOG' ) && BP_ENABLE_MULTIBLOG ) {
      $bp_existed = 'true';
    } else if ( defined( 'BP_ROOT_BLOG' ) && BP_ROOT_BLOG == $current_blog->blog_id ) {
      $bp_existed = 'true';
    }
	else if ( defined( 'BP_ROOT_BLOG' ) && ($blog_id != 1) ) {
      $bp_existed = 'false';
    }
  } else {
    $bp_existed = 'true';
  }
}
else{
	$bp_existed = 'false';
}
///////////////////////////////////////////////////////////////////////////////
// global check if wp 3.0 in single or multi network
//////////////////////////////////////////////////////////////////////////////

if( function_exists('get_current_site') ) {
$multi_site_on = 'true'; //wpmu exist
} else {
$multi_site_on = 'false'; //wpmu not exist
}

//////////////////////////////////////////////////////////////////
// check if activity or blog in frontpage for bp 1.2
/////////////////////////////////////////////////////////////////

$bp_front_setting = get_option( 'page_on_front' );
if ( 'activity' == $bp_front_setting )  {
$bp_front_is_activity = 'true';
} else {
$bp_front_is_activity = 'false';
}


?>