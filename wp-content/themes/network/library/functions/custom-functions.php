<?php

function wt_get_ID_by_page_name($page_name)
{
	global $wpdb;
	$page_name_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = '".$page_name."'");
	return $page_name_id;
}
////////////////////////////////////////////////////////////////////////////////
// new thumbnail code for wp 2.9+
////////////////////////////////////////////////////////////////////////////////
if ( function_exists( 'add_theme_support' ) ) { // Added in 2.9
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 150, 150, true ); // Normal post thumbnails
	add_image_size( 'single-post-thumbnail', 400, 9999 ); // Permalink thumbnail size
}
// This theme uses wp_nav_menu() in one location.
register_nav_menus( array(
	'primary' => __( 'Primary Navigation', 'network' ),
) );

function wpmudev_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'wpmudev_page_menu_args' );

// This theme allows users to set a custom background
add_theme_support( 'custom-background' );



	define( 'HEADER_TEXTCOLOR', '' );
	define( 'HEADER_IMAGE', '' );	

	define( 'HEADER_IMAGE_WIDTH', apply_filters( 'network_header_image_width', 980 ) );
	define( 'HEADER_IMAGE_HEIGHT', apply_filters( 'network_header_image_height', 100 ) );


	define( 'NO_HEADER_TEXT', true );

	add_theme_support( 'custom-header', array( 'admin-head-callback' => 'network_admin_header_style' ) );

if ( ! function_exists( 'network_admin_header_style' ) ) :

function network_admin_header_style() {
?>
<style type="text/css">
/* Shows the same border as on front end */
#headimg {
	border-bottom: 1px solid #000;
	border-top: 4px solid #000;
}
/* If NO_HEADER_TEXT is false, you would style the text with these selectors:
	#headimg #name { }
	#headimg #desc { }
*/
</style>
<?php
}
endif;
function signup_button(){
	include (get_template_directory() . '/library/options/options.php');
	$signupfeat_buttontext = get_option('dev_network_signupfeat_buttontext');
	$signupfeat_buttontext_custom = get_option('dev_network_signupfeat_buttontextcustom');
	
	if ($signupfeat_buttontext == ""){
		$signupfeat_buttontext = "Join now";
	}
	
	if (($bp_existed == 'true') && ($signupfeat_buttontext_custom == "")){
	?>
		<a href="<?php echo get_option('home') ?>/register/" class="button"><?php echo stripslashes($signupfeat_buttontext); ?></a>
		
		<?php		
	}
	else if ($signupfeat_buttontext_custom != ""){
		?>
			<a href="<?php echo $signupfeat_buttontext_custom; ?>" class="button"><?php echo stripslashes($signupfeat_buttontext); ?></a>
		<?php
	}
	else{
		if ($multi_site_on == 'true'){
				?>
				<a href="<?php echo get_option('home') ?>/wp-login.php?action=register" class="button"><?php echo stripslashes($signupfeat_buttontext); ?></a>
				<?php
		}
		else{
			?>
		  <a href="<?php echo get_option('home') ?>/wp-login.php" class="button"><?php echo stripslashes($signupfeat_buttontext); ?></a>
		<?php
		}
	}
	
}

function font_show(){
	$fonttype = get_option('dev_network_header_font');
	$bodytype = get_option('dev_network_body_font');
	if (($fonttype == "")&&($bodytype == "")){
	?>
<link href='http://fonts.googleapis.com/css?family=Nobile' rel='stylesheet' type='text/css'/>
<style type="text/css" media="screen">
      h1, h2, h3, h4, h5, h6, #site-logo{
font-family: 'Nobile', arial, serif!important;
	}
	body{
		font-family: Helvetica, Arial, Sans-serif!important;
	}
    </style>
	<?php
	}
	else if (($fonttype == "Cantarell, arial, serif") || ($bodytype == "Cantarell, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Cantarell' rel='stylesheet' type='text/css'/>
<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #site-logo{
	font-family: <?php echo $fonttype; ?>!important;
		}
		body{
			font-family: <?php echo $bodytype; ?>!important;
		}
	    </style>
	<?php
	}
	else if (($fonttype == "Cardo, arial, serif") || ($bodytype == "Cardo, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Cardo' rel='stylesheet' type='text/css'/>
<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #site-logo{
	font-family: <?php echo $fonttype; ?>!important;
		}
		body{
			font-family: <?php echo $bodytype; ?>!important;
		}
	    </style>
	<?php
	}
	else if (($fonttype == "Crimson Text, arial, serif") || ($bodytype == "Crimson Text, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Crimson+Text' rel='stylesheet' type='text/css'/>
<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #site-logo{
	font-family: <?php echo $fonttype; ?>!important;
		}
		body{
			font-family: <?php echo $bodytype; ?>!important;
		}
	    </style>
	<?php
	}
	else if (($fonttype == "Droid Sans, arial, serif") || ($bodytype == "Droid Sans, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Droid+Sans' rel='stylesheet' type='text/css'/>
	<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #site-logo{
	font-family: <?php echo $fonttype; ?>!important;
		}
		body{
			font-family: <?php echo $bodytype; ?>!important;
		}
	    </style>
	<?php
	}
	else if (($fonttype == "Droid Serif, arial, serif") || ($bodytype == "Droid Serif, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Droid+Serif' rel='stylesheet' type='text/css'/>
	<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #site-logo{
	font-family: <?php echo $fonttype; ?>!important;
		}
		body{
			font-family: <?php echo $bodytype; ?>!important;
		}
	    </style>
	<?php
	}
	else if (($fonttype == "IM Fell DW Pica, arial, serif") || ($bodytype == "IM Fell DW Pica, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=IM+Fell+DW+Pica' rel='stylesheet' type='text/css'/>
<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #site-logo{
	font-family: <?php echo $fonttype; ?>!important;
		}
		body{
			font-family: <?php echo $bodytype; ?>!important;
		}
	    </style>
	<?php
	}
	else if (($fonttype == "Josefin Sans Std Light, arial, serif") || ($bodytype == "Josefin Sans Std Light, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Josefin+Sans+Std+Light' rel='stylesheet' type='text/css'/>
<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #site-logo{
	font-family: <?php echo $fonttype; ?>!important;
		}
		body{
			font-family: <?php echo $bodytype; ?>!important;
		}
	    </style>
	<?php
	}
	else if (($fonttype == "Lobster, arial, serif") || ($bodytype == "Lobster, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Lobster' rel='stylesheet' type='text/css'/>
<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #site-logo{
	font-family: <?php echo $fonttype; ?>!important;
		}
		body{
			font-family: <?php echo $bodytype; ?>!important;
		}
	    </style>
	<?php
	}
	else if (($fonttype == "Molengo, arial, serif") || ($bodytype == "Molengo, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Molengo' rel='stylesheet' type='text/css'/>
<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #site-logo{
	font-family: <?php echo $fonttype; ?>!important;
		}
		body{
			font-family: <?php echo $bodytype; ?>!important;
		}
	    </style>
	<?php
	}
	else if (($fonttype == "Neuton, arial, serif") || ($bodytype == "Neuton, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Neuton' rel='stylesheet' type='text/css'/>
<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #site-logo{
	font-family: <?php echo $fonttype; ?>!important;
		}
		body{
			font-family: <?php echo $bodytype; ?>!important;
		}
	    </style>
	<?php
	}
	else if (($fonttype == "Nobile, arial, serif") || ($bodytype == "Nobile, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Nobile' rel='stylesheet' type='text/css'/>
	<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #site-logo{
	font-family: <?php echo $fonttype; ?>!important;
		}
		body{
			font-family: <?php echo $bodytype; ?>!important;
		}
	    </style>
	<?php
	}
	else if (($fonttype == "OFL Sorts Mill Goudy TT, arial, serif") || ($bodytype == "OFL Sorts Mill Goudy TT, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=OFL+Sorts+Mill+Goudy+TT' rel='stylesheet' type='text/css'/>
<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #site-logo{
	font-family: <?php echo $fonttype; ?>!important;
		}
		body{
			font-family: <?php echo $bodytype; ?>!important;
		}
	    </style>
	<?php
	}
	else if (($fonttype == "Reenie Beanie, arial, serif") || ($bodytype == "Reenie Beanie, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Reenie+Beanie' rel='stylesheet' type='text/css'/>
<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #site-logo{
	font-family: <?php echo $fonttype; ?>!important;
		}
		body{
			font-family: <?php echo $bodytype; ?>!important;
		}
	    </style>
	<?php
	}	
	else if (($fonttype == "Tangerine, arial, serif") || ($bodytype == "Tangerine, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Tangerine' rel='stylesheet' type='text/css'/>
<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #site-logo{
	font-family: <?php echo $fonttype; ?>!important;
		}
		body{
			font-family: <?php echo $bodytype; ?>!important;
		}
	    </style>
	<?php
	}
	else if (($fonttype == "Old Standard TT, arial, serif") || ($bodytype == "Old Standard TT, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Old+Standard+TT' rel='stylesheet' type='text/css'/>
	<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #site-logo{
	font-family: <?php echo $fonttype; ?>!important;
		}
		body{
			font-family: <?php echo $bodytype; ?>!important;
		}
	    </style>
	<?php
	}
	else if (($fonttype == "Volkorn, arial, serif") || ($bodytype == "Volkorn, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Volkorn' rel='stylesheet' type='text/css'/>
	<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #site-logo{
	font-family: <?php echo $fonttype; ?>!important;
		}
		body{
			font-family: <?php echo $bodytype; ?>!important;
		}
	    </style>
	<?php
	}
	else if (($fonttype == "Yanone Kaffessatz, arial, serif") || ($bodytype == "Yanone Kaffessatz, arial, serif")){
	?>
	<link href='http://fonts.googleapis.com/css?family=Yanone+Kaffeesatz' rel='stylesheet' type='text/css'/>
<style type="text/css" media="screen">
	      h1, h2, h3, h4, h5, h6, #site-logo{
	font-family: <?php echo $fonttype; ?>!important;
		}
		body{
			font-family: <?php echo $bodytype; ?>!important;
		}
	    </style>
	<?php
	}
}

function get_list_of_blogs() {
	global $wpdb;
	global $table_prefix;
	
	$blogs = $wpdb->get_results("SELECT * FROM $wpdb->blogs WHERE archived = '0' AND mature = '0' AND spam = '0' AND deleted = '0' ORDER BY blog_id DESC");
	
	$bloglist = array();
	$bloglist[] = array('' => '');
	
	if (sizeof($blogs) > 1) {

		foreach ($blogs as $blog) {
 			$blogname = ( is_subdomain_install() ) ? str_replace( '.'.$current_site->domain, '', $blog->domain ) : $blog->path;
			$bloglist[] = array('blogname' => $blogname, 'blogid' => $blog->blog_id);
		}
		
		return $bloglist;
				
	} else {
	
		return null;
	
	}

}

function multisite_count_recent_posts($how_many=10000, $how_long=0, $homepage_show_thumbnails = "yes", $titleOnly=true, $begin_wrap="\n<li>", $end_wrap="</li>") {
	global $wpdb;
	global $table_prefix;
	$counter = 0;
	
	if ($how_long > 0) {
		$blogs = $wpdb->get_var("SELECT COUNT(blog_id) FROM $wpdb->blogs WHERE
			public = '1' AND archived = '0' AND mature = '0' AND spam = '0' AND deleted = '0'
			AND last_updated >= DATE_SUB(CURRENT_DATE(), INTERVAL $how_long DAY)
			ORDER BY last_updated DESC LIMIT $how_many");
	} else {
		$blogs = $wpdb->get_var("SELECT COUNT(blog_id) FROM $wpdb->blogs WHERE
			public = '1' AND archived = '0' AND mature = '0' AND spam = '0' AND deleted = '0'
			ORDER BY last_updated DESC LIMIT $how_many");
	}
	

	if ($blogs) {
		return true;
	} else {
		return false;
	}
	
}

function get_recent_posts($how_many=10000, $how_long=0, $homepage_show_thumbnails = "yes", $titleOnly=true, $begin_wrap="\n<li>", $end_wrap="</li>") {
	global $wpdb;
	global $table_prefix;
	global $blog_id;
	$blog = $blog_id;
	$counter = 0;

	if ($blog != 1){
		$blogOptionsTable = $wpdb->options;
		$blogPostsTable = $wpdb->posts;
		$blogPostsMetaTable = $wpdb->postmeta;
	}
	else{
		$blogOptionsTable = $wpdb->options;
	   	$blogPostsTable = $wpdb->posts;
	}

	if ($how_long > 0) {
		$thisposts = $wpdb->get_results("SELECT ID, post_title
			FROM $blogPostsTable WHERE post_status = 'publish'
			AND ID > 0
			AND post_type = 'post'
			AND post_date >= DATE_SUB(CURRENT_DATE(), INTERVAL $how_long DAY)
			ORDER BY id DESC LIMIT $how_many");
	} else {
		$thisposts = $wpdb->get_results("SELECT ID, post_title
			FROM $blogPostsTable WHERE post_status = 'publish'
			AND ID > 0
			AND post_type = 'post'
			ORDER BY id DESC LIMIT $how_many");

	}
	
	$default_thumbnail_url = get_option('dev_network_homepage_default_thumbnail_url');

	if (sizeof($thisposts) > 0) { 
		
		$options = $wpdb->get_results("SELECT option_value FROM
			$blogOptionsTable WHERE option_name IN ('siteurl','blogname') 
			ORDER BY option_name DESC");
	
		foreach ($thisposts as $thispost) {
			$thispermalink = get_permalink($thispost->ID);
					if ($homepage_show_thumbnails == "yes") { 

					?>
					<li class="withthumb">

					<?php

					} else {

					?>
					<li>
					<?php
					}
			
			if ($homepage_show_thumbnails != "no") { 
				if(is_multisite()){
				switch_to_blog($blog);
			}
					$thumbnail = get_image_path($thispost->ID, $blog);
						if(is_multisite()){
						restore_current_blog();
					}
							
						
			
			
			?>
		<?php
			if ( is_multisite() ) {
				
				if ($thumbnail != '') { ?>
						<img src="<?php bloginfo('template_directory'); ?>/library/functions/timthumb.php?src=<?php echo $thumbnail; ?>&amp;h=108&amp;w=227&amp;zc=1&amp;multisite=false&amp;blogdirid=<?php echo $blog; ?>" alt="" style="width:227px; height: 108px;" />
					<?php } else if ($default_thumbnail_url != '') { ?>
						<div class="thumb"><a href="<?php $thispermalink; ?>"><img src="<?php echo $default_thumbnail_url; ?>" alt="" /></a></div>
					<?php } 	else { ?>
								<div class="thumb"><a href="<?php echo $thispermalink; ?>"><img src="<?php bloginfo('template_directory'); ?>/_inc/images/placeholders/article.jpg" alt="" /></a></div>
							<?php }
						}
			else{ 
				if ($thumbnail != '') { ?>
							<img src="<?php bloginfo('template_directory'); ?>/library/functions/timthumb.php?src=<?php echo $thumbnail; ?>&amp;h=108&amp;w=227&amp;zc=1&amp;multisite=false&amp;blogdirid=<?php echo $blog; ?>" alt="" style="width:227px; height: 108px;" />
					<?php } else if ($default_thumbnail_url != '') { ?>
						<div class="thumb"><a href="<?php $thispermalink; ?>"><img src="<?php echo $default_thumbnail_url; ?>" alt="" /></a></div>
					<?php } else { ?>
							<div class="thumb"><a href="<?php echo $thispermalink; ?>"><img src="<?php bloginfo('template_directory'); ?>/_inc/images/placeholders/article.jpg" alt="" /></a></div>
						<?php }
			}
			
			?>
			
						
					<?php }

					?>
			
						<h2><a href="<?php echo $thispermalink; ?>"><?php echo limit_words($thispost->post_title, 5);?></a></h2>
			<?php
				if (isset($options) && sizeof($options) > 0 && $options[0]->option_value != '') {
			?>
			
		
			<h3><a href="<?php echo $options[0]->option_value; ?>"><?php echo $options[1]->option_value; ?></a></h3>
			<?php
			}
			
			echo '
				  </li>';
		
		}
		if($counter >= $how_many) {
			break; 
		}
	}
	
}

function network_badge_boxes() {
	$badge_boxes = get_option('dev_network_allow_badge_boxes');
	
	if ($badge_boxes != "no") { ?>
	<div class="profile-badges">
		<?php if (function_exists('bp_total_blog_count_for_user')) { ?>
		<div class="articles-badge"><div class="info"><span><?php bp_total_blog_count_for_user(); ?></span><?php _e( 'blogs', 'network' ) ?></div><div class="badge-icon"></div></div>
		<?php } ?>
		<?php if (function_exists('friends_get_total_friend_count')) { ?>
		<div class="friends-badge"><div class="info"><span><?php echo friends_get_total_friend_count(); ?></span><?php _e( 'friends', 'network' ) ?></div><div class="badge-icon"></div></div>
		<?php } ?>
		<?php if (function_exists('bp_group_total_for_member')) { ?>
		<div class="groups-badge"><div class="info"><span><?php bp_group_total_for_member(); ?></span><?php _e( 'groups', 'network' ) ?></div><div class="badge-icon"></div></div>
		<?php } ?>
	</div>
<?php
}
?>
	<div class="clear"></div>
<?php
}


function multisite_recent_posts($how_many=10, $how_long=0, $homepage_show_thumbnails = "yes", $titleOnly=true, $begin_wrap="\n<li>", $end_wrap="</li>") {
	global $wpdb;
	global $table_prefix;
	$counter = 0;
	
	$default_thumbnail_url = get_option('dev_network_homepage_default_thumbnail_url');
	
	if ($how_long > 0) {
		$blogs = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs WHERE
			public != '2' AND archived = '0' AND mature = '0' AND spam = '0' AND deleted = '0'
			AND last_updated >= DATE_SUB(CURRENT_DATE(), INTERVAL $how_long DAY)
			ORDER BY last_updated DESC LIMIT $how_many");
	} else {
		$blogs = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs WHERE
			public != '2' AND archived = '0' AND mature = '0' AND spam = '0' AND deleted = '0'
			ORDER BY last_updated DESC LIMIT $how_many");
	}
	
	if ($blogs) {
		foreach ($blogs as $blog) {
		
			$thumbnail = '';
			
			switch_to_blog($blog);
			$blogOptionsTable = $wpdb->options;
			$blogPostsTable = $wpdb->posts;
			$blogPostsMetaTable = $wpdb->postmeta;
			restore_current_blog();
		    	
			$options = $wpdb->get_results("SELECT option_value FROM
				$blogOptionsTable WHERE option_name IN ('siteurl','blogname') 
				ORDER BY option_name DESC");
			if ($how_long > 0) {
				$thispost = $wpdb->get_results("SELECT ID, post_title
					FROM $blogPostsTable WHERE post_status = 'publish'
					AND ID > 0
					AND post_type = 'post'
					AND post_date >= DATE_SUB(CURRENT_DATE(), INTERVAL $how_long DAY)
					ORDER BY id DESC LIMIT 0,1");
			} else {
				$thispost = $wpdb->get_results("SELECT ID, post_title
					FROM $blogPostsTable WHERE post_status = 'publish'
					AND ID > 0
					AND post_type = 'post'
					ORDER BY id DESC LIMIT 0,1");

			}

			$homepage_ignore_blogs = array ();
			$homepage_ignore_blogs_temp = get_option('dev_network_homepage_ignore_blogs');
			if (is_array($homepage_ignore_blogs_temp) && sizeof($homepage_ignore_blogs_temp) > 0) { 
		
				$homepage_ignore_blogs = $homepage_ignore_blogs_temp;
			} else {
				$homepage_ignore_blogs = unserialize($homepage_ignore_blogs_temp);
			}

			if($thispost && (is_array($homepage_ignore_blogs) && sizeof($homepage_ignore_blogs) > 0 && !in_array($blog, $homepage_ignore_blogs) || $homepage_ignore_blogs == ''  )   ) {

				$thispermalink = get_blog_permalink($blog, $thispost[0]->ID);

					if ($homepage_show_thumbnails == "yes") { 

					?>
					<li class="withthumb">
						<?php

					} else {

					echo '<li>';

					}
			
				if ($homepage_show_thumbnails != "no") { 
				
						switch_to_blog($blog);
						$thumbnail = get_image_path($thispost[0]->ID, $blog);
						restore_current_blog();			

				?>
							
					<?php if ($thumbnail != '') { ?>
					<img src="<?php bloginfo('template_directory'); ?>/library/functions/timthumb.php?src=<?php echo $thumbnail; ?>&amp;h=108&amp;w=227&amp;zc=1&amp;multisite=true&amp;blogdirid=<?php echo $blog; ?>" alt="" style="width:227px; height: 108px;" />
					<?php } else if ($default_thumbnail_url != '') { ?>
						<div class="thumb"><a href="<?php $thispermalink; ?>"><img src="<?php echo $default_thumbnail_url; ?>" alt="" /></a></div>
					<?php } 	else { ?>
							<div class="thumb"><a href="<?php echo $thispermalink; ?>"><img src="<?php bloginfo('template_directory'); ?>/_inc/images/placeholders/article.jpg" alt="" /></a></div>
						<?php } ?>
			<?php }
			
		?>
						<h2><a href="<?php echo $thispermalink; ?>"><?php echo limit_words($thispost[0]->post_title, 5); ?></a></h2>
						<h3><a href="<?php echo $options[0]->option_value; ?>"><?php echo $options[1]->option_value; ?></a></h3>
				  </li>
				<?php
				  
			$counter++;
				
			}
			if($counter >= $how_many) {
				break; 
			}
		}
		return $counter;
	} else {
		return false;
	}
		
}

function get_user_latest_blog_post($blog_id) {
	global $wpdb;
    if ($blog_id == 1){
		$blogOptionsTable = $wpdb->base_prefix."options";
	    $blogPostsTable = $wpdb->base_prefix."posts";
	}
	else{
		$blogOptionsTable = $wpdb->base_prefix.$blog_id."_options";
	    $blogPostsTable = $wpdb->base_prefix.$blog_id."_posts";
	}
	$counter = 0;
	$thispost = $wpdb->get_results("SELECT ID, post_title, post_date, guid, post_excerpt
		FROM $blogPostsTable WHERE post_status = 'publish'
		AND ID > 1
		AND post_type = 'post'
		ORDER BY id DESC LIMIT 0,1");
	return $thispost;
}

function networktheme_core_get_last_activity( $last_activity_date ) {
	$last_active = sprintf( bp_core_time_since( $last_activity_date ) );

	return apply_filters( 'bp_core_get_last_activity', $last_active );
}

function limit_words($string, $word_limit)
{
    $words = explode(" ",$string);
    $extract = implode(" ",array_splice($words,0,$word_limit));
    if (strlen($extract) < strlen($string)) { $extract .= "..."; }
    return $extract;
}
 
function get_image_path($post_id = null, $blog_id = null) {
	if ($post_id == null) {
		global $post;
		$post_id = $post->ID;
	}
	$thumbid = get_post_thumbnail_id($post_id);
	if (isset($thumbid) && $thumbid > 0) { 
		$theImageSrc = wp_get_attachment_url($thumbid);
		if (isset($blog_id) && $blog_id > 0) {
			$imageParts = explode('/files/', $theImageSrc);
			if (isset($imageParts[1])) {
				$theImageSrc = '/blogs.dir/' . $blog_id . '/files/' . $imageParts[1];
			}
		}
		return $theImageSrc;
	}
}

?>