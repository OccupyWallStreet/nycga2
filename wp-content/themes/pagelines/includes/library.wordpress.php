<?php

/**
 * Gets Comments Link based on ID
 */
function pl_get_comments_link( $post_id ){
	
	$num_comments = get_comments_number($post_id);
	 if ( comments_open() ){
	 	  if($num_comments == 0){
	 	  	  $comments = __('Add Comment', 'pagelines');
	 	  }
	 	  elseif($num_comments > 1){
	 	  	  $comments = $num_comments.' '. __( 'Comments', 'pagelines' );
	 	  }
	 	  else{
	 	  	   $comments ="1 Comment";
	 	  }
	 $write_comments = '<a href="' . get_comments_link($post_id) .'">'. $comments.'</a>';
	 }
	else{$write_comments =  '';}
	
	return $write_comments;

}

/**
 * Get just the WordPress thumbnail URL - False if not there.
 */
function pl_the_thumbnail_url( $post_id, $size = false ){
	
	if( has_post_thumbnail($post_id) ){
		
		$img_data = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), $size, false);

		$a['img'] = ($img_data[0] != '') ? $img_data[0] : '';

		return $a['img'];
		
	} else
		return false;
}

/**
 * Support optional WordPress functionality 'add_theme_support'
 */
add_action('after_setup_theme', 'pl_theme_support');

/**
 *
 * @TODO document
 *
 */
function pl_theme_support(  ){	
	
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'menus' );
	add_theme_support( 'automatic-feed-links' );
	
}

/**
 *  Fix The WordPress Login Image URL
 */
add_filter('login_headerurl', 'fix_wp_login_imageurl');

/**
 *
 * @TODO document
 *
 */
function fix_wp_login_imageurl( $url ){	
	return home_url();
}

/**
 *  Fix The WordPress Login Image Title
 */
add_filter('login_headertitle', 'fix_wp_login_imagetitle');

/**
 *
 * @TODO document
 *
 */
function fix_wp_login_imagetitle( $url ){	
	return get_bloginfo('name');
}

/**
 *  Fix The WordPress Login Image Title
 */
if ( VPRO )
	add_action('login_head', 'pl_fix_login_image');

/**
 *
 * @TODO document
 *
 */
function pl_fix_login_image( ){	
	
	$image_url = (ploption('pl_login_image')) ? ploption('pl_login_image') : PL_ADMIN_IMAGES . '/login-pl.png';
	
	$css = sprintf('body #login h1 a{background: url(%s) no-repeat top center;height: 80px;background-size:auto;}', $image_url);
	
	inline_css_markup('pagelines-login-css', $css);
}

/**
 *  Fix The WordPress Favicon by Site Title
 */
add_action('admin_head', 'pl_fix_admin_favicon');

/**
 *
 * @TODO document
 *
 */
function pl_fix_admin_favicon( ){	
	
	$image_url = (ploption('pagelines_favicon')) ? ploption('pagelines_favicon') : PL_ADMIN_IMAGES . '/favicon-pagelines.png';
	
	$css = sprintf('#wphead #header-logo{background: url(%s) no-repeat scroll center center;}', $image_url);
	
	inline_css_markup('pagelines-wphead-img', $css);
}
