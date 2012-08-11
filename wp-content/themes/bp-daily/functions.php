<?php
define('BP_DAILY_VERSION', '4.1.7.1');
if ( ! function_exists( 'bpdaily_setup' ) ) :
function bpdaily_setup() {
	global $options, $options2, $options3, $bp_existed, $multi_site_on;
	
	load_theme_textdomain('bp-daily', get_template_directory() . '/languages/');
	$locale = get_locale();
	$locale_file = get_template_directory() . "/languages/$locale.php";
	if ( is_readable( $locale_file ) )
		require_once( $locale_file );
	
	global $content_width;
	if ( ! isset( $content_width ) ) {
		$content_width = 685;
	}
	
	add_action( 'wp_enqueue_scripts', 'bpdaily_load_scripts' );
	add_action( 'widgets_init', 'bpdaily_widgets_init' );
	add_action( 'wp_enqueue_scripts', 'bpdaily_enqueue_styles' );
	add_action( 'wp_head', 'bpdaily_themeoptions_output' );
	
	require( dirname( __FILE__ ) . '/library/functions/conditional-functions.php' );
	if($bp_existed == 'true') {
		require( dirname( __FILE__ ) . '/library/functions/bp-functions.php' );
		add_filter( 'comment_form_defaults', 'wpmudev_comment_form', 10 );
	}
	require( dirname( __FILE__ ) . '/library/functions/custom-header.php' );
	require( dirname( __FILE__ ) . '/library/functions/custom-functions.php' );
	require( dirname( __FILE__ ) . '/library/functions/option-functions.php' );
	require( dirname( __FILE__ ) . '/library/functions/loop-functions.php' );
		
	add_theme_support('automatic-feed-links');
} 
endif;
add_action( 'after_setup_theme', 'bpdaily_setup');

if ( ! function_exists( 'bpdaily_enqueue_styles' ) ) :
function bpdaily_enqueue_styles(){
	global $options, $options2, $options3, $bp_existed, $multi_site_on;
	$version = '4';
	
	if ($bp_existed){	
		wp_enqueue_style( 'bpdaily-buddypress', get_template_directory_uri() . '/_inc/css/bpdaily-buddypress.css', array(), $version );
	}	
	
	
	wp_enqueue_style( 'bpdaily', get_template_directory_uri() . '/_inc/css/bpdaily.css', array(), $version );
				
		// Right to left CSS
		if ( is_rtl() )
			wp_enqueue_style( 'bpdaily-rtl',  get_template_directory_uri() . '/rtl.css', array( 'bpdaily' ), $version );
}
endif;

if ( ! function_exists( 'bpdaily_themeoptions_output' ) ) :
function bpdaily_themeoptions_output(){
include (get_template_directory() . '/library/options/options.php');
	$get_current_scheme = get_option('dev_buddydaily_custom_style');
	if(($get_current_scheme == '') || ($get_current_scheme == 'default.css')) { 
		print "<style type='text/css' media='screen'>"; 
		include (get_template_directory() . '/library/options/theme-options.php');
		print "</style>";
	}
}
endif;

if ( ! function_exists( 'bpdaily_load_scripts' ) ) :
function bpdaily_load_scripts() {
	$version = '4';
	if ( !is_admin() ) { 
		wp_enqueue_script("jquery");
		wp_enqueue_script( 'hoverIntent', get_template_directory_uri() . '/library/scripts/hoverIntent.js', array( 'jquery' ), $version );
		wp_enqueue_script( 'superfish', get_template_directory_uri() . '/library/scripts/superfish.js', array( 'jquery' ), $version );
		wp_enqueue_script( 'supersubs', get_template_directory_uri() . '/library/scripts/supersubs.js', array( 'jquery' ), $version );
		if ( is_singular() && get_option( 'thread_comments' ) && comments_open() )
			wp_enqueue_script( 'comment-reply' );
		
		$get_current_scheme = get_option('dev_buddydaily_custom_style');
		if (!(($get_current_scheme == '') || ($get_current_scheme == 'default.css'))) { 
			wp_enqueue_style('bp-daily', get_template_directory_uri() . "/library/styles/{$get_current_scheme}", null, BP_DAILY_VERSION);
		}
		$child_css_file = get_stylesheet_directory() . "/_inc/css/child.css";
		if (file_exists ($child_css_file) ) {
			$css_version = filemtime($child_css_file);
			wp_enqueue_style('bp-daily-child', get_template_directory_uri() . "/_inc/css/child.css", null, $css_version);
		}
	}
}
endif;

if ( ! function_exists( 'bpdaily_widgets_init' ) ) :
function bpdaily_widgets_init() {
	global $options, $options2, $options3, $bp_existed, $multi_site_on;
	register_sidebar(
		array(
			'name'          => __( 'Home Sidebar', 'bp-daily' ),
			'id'            => 'home-sidebar',
			'description'   => 'Home Sidebar',
			'before_widget' => '<div class="light-container"><div id="%1$s" class="widget %2$s">',
	        'after_widget' => '</div></div>',
	        'before_title' => '<h3 class="widgettitle">',
	        'after_title' => '</h3>'
		)
	);

register_sidebar(
	array(
		'name'          => __( 'Default Sidebar', 'bp-daily' ),
		'id'            => 'default-sidebar',
		'description'   => 'Default Sidebar',
		'before_widget' => '<div class="light-container"><div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div></div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
	)
);

register_sidebar(
	array(
		'name'          => __( 'Blog Sidebar', 'bp-daily' ),
		'id'            => 'blog-sidebar',
		'description'   => 'Blog Sidebar',
		'before_widget' => '<div class="light-container"><div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div></div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
	)
);

register_sidebar(
	array(
		'name'          => __( 'Page Sidebar', 'bp-daily' ),
		'id'            => 'page-sidebar',
		'description'   => 'Page Sidebar',
		'before_widget' => '<div class="light-container"><div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div></div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
	)
);

register_sidebar(
	array(
		'name'          => __( 'Footerone Sidebar', 'bp-daily' ),
		'id'            => 'footerone-sidebar',
		'description'   => 'Footertwo Sidebar',
		'before_widget' => '<div class="light-container"><div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div></div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
	)
);

register_sidebar(
	array(
		'name'          => __( 'Footertwo Sidebar', 'bp-daily' ),
		'id'            => 'footertwo-sidebar',
		'description'   => 'Footertwo Sidebar',
		'before_widget' => '<div class="light-container"><div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div></div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
	)
);

register_sidebar(
	array(
		'name'          => __( 'Footerthree Sidebar', 'bp-daily' ),
		'id'            => 'footerthree-sidebar',
		'description'   => 'Footerthree Sidebar',
		'before_widget' => '<div class="light-container"><div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div></div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
	)
);

register_sidebar(
	array(
		'name'          => __( 'Footerfour Sidebar', 'bp-daily' ),
		'id'            => 'footerfour-sidebar',
		'description'   => 'Footerfour Sidebar',
		'before_widget' => '<div class="light-container"><div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div></div>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
	)
);

if($bp_existed == 'true') {
	register_sidebar(
		array(
			'name'          => __( 'Members Sidebar', 'bp-daily' ),
			'id'            => 'members-sidebar',
			'description'   => 'Members Sidebar',
			'before_widget' => '<div class="light-container"><div id="%1$s" class="widget %2$s">',
	        'after_widget' => '</div></div>',
	        'before_title' => '<h3 class="widgettitle">',
	        'after_title' => '</h3>'
		)
	);
	
}

}
endif;

if ( !function_exists( 'wpmudev_comment_form' ) ) :
function wpmudev_comment_form( $default_labels ) {
	global $themename, $shortname, $options, $options2, $options3, $bp_existed, $multi_site_on;
	
	if($bp_existed == 'true') :
	global $user_identity;

	$commenter = wp_get_current_commenter();
	$req = get_option( 'require_name_email' );
	$aria_req = ( $req ? " aria-required='true'" : '' );
	$fields =  array(
		'author' => '<p class="comment-form-author">' . '<label for="author">' . __( 'Name', 'bp-daily' ) . ( $req ? '<span class="required"> *</span>' : '' ) . '</label> ' .
		            '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></p>',
		'email'  => '<p class="comment-form-email"><label for="email">' . __( 'Email', 'bp-daily' ) . ( $req ? '<span class="required"> *</span>' : '' ) . '</label> ' .
		            '<input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /></p>',
		'url'    => '<p class="comment-form-url"><label for="url">' . __( 'Website', 'bp-daily' ) . '</label>' .
		            '<input id="url" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></p>',
	);

	$new_labels = array(
		'comment_field'  => '<p class="form-textarea"><textarea name="comment" id="comment" cols="60" rows="10" aria-required="true"></textarea></p>',
		'fields'         => apply_filters( 'comment_form_default_fields', $fields ),
		'logged_in_as'   => '',
		'must_log_in'    => '<p class="alert">' . sprintf( __( 'You must be <a href="%1$s">logged in</a> to post a comment.', 'bp-daily' ), wp_login_url( get_permalink() ) )	. '</p>',
		'title_reply'    => __( 'Leave a reply', 'bp-daily' )
	);

	return apply_filters( 'wpmudev_comment_form', array_merge( $default_labels, $new_labels ) );	
	endif;
}
endif;

if ( !function_exists( 'wpmudev_blog_comments' ) ) :
function wpmudev_blog_comments( $comment, $args, $depth ) {
global $themename, $shortname, $options, $options2, $options3, $bp_existed, $multi_site_on;

if($bp_existed == 'true') {
	$GLOBALS['comment'] = $comment;

	if ( 'pingback' == $comment->comment_type )
		return false;

	if ( 1 == $depth )
		$avatar_size = 50;
	else
		$avatar_size = 25;
	?>

	<li <?php comment_class() ?> id="comment-<?php comment_ID() ?>">
		<div class="comment-avatar-box">
			<div class="avb">
				<a href="<?php echo get_comment_author_url() ?>" rel="nofollow">
					<?php if ( $comment->user_id ) : ?>
						<?php echo bp_core_fetch_avatar( array( 'item_id' => $comment->user_id, 'width' => $avatar_size, 'height' => $avatar_size, 'email' => $comment->comment_author_email ) ) ?>
					<?php else : ?>
						<?php echo get_avatar( $comment, $avatar_size ) ?>
					<?php endif; ?>
				</a>
			</div>
		</div>

		<div class="comment-content">
			<div class="comment-meta">
				<p>
					<?php
						printf( __( '<a href="%1$s" rel="nofollow">%2$s</a> said on <a href="%3$s"><span class="time-since">%4$s</span></a>', 'bp-daily' ), get_comment_author_url(), get_comment_author(), get_comment_link(), get_comment_date() );
					?>
				</p>
			</div>

			<div class="comment-entry">
				<?php if ( $comment->comment_approved == '0' ) : ?>
				 	<em class="moderate"><?php _e( 'Your comment is awaiting moderation.', 'bp-daily' ); ?></em>
				<?php endif; ?>

				<?php comment_text() ?>
			</div>

			<div class="comment-options">
					<?php if ( comments_open() ) : ?>
						<?php comment_reply_link( array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ); ?>
					<?php endif; ?>

					<?php if ( current_user_can( 'edit_comment', $comment->comment_ID ) ) : ?>
						<?php printf( '<a class="button comment-edit-link bp-secondary-action" href="%1$s" title="%2$s">%3$s</a> ', get_edit_comment_link( $comment->comment_ID ), esc_attr__( 'Edit comment', 'bp-daily' ), __( 'Edit', 'bp-daily' ) ) ?>
					<?php endif; ?>

			</div>

		</div>
<?php } else {

	$GLOBALS['comment'] = $comment;

	if ( 'pingback' == $comment->comment_type )
		return false;

	if ( 1 == $depth )
		$avatar_size = 50;
	else
		$avatar_size = 25;
	?>
	<li <?php comment_class() ?> id="comment-<?php comment_ID() ?>">
		<div class="comment-avatar-box">
			<div class="avb">
				<a href="<?php echo get_comment_author_url() ?>" rel="nofollow">
					<?php if ( $comment->user_id ) : ?>
							<?php echo get_avatar( $comment, 40 ); ?>
					<?php else : ?>
						<?php echo get_avatar( $comment, $avatar_size ) ?>
					<?php endif; ?>
				</a>
			</div>
		</div>

		<div class="comment-content">
			<div class="comment-meta">
				<p>
					<?php
						printf( __( '<a href="%1$s" rel="nofollow">%2$s</a> said on <a href="%3$s"><span class="time-since">%4$s</span></a>', 'bp-daily' ), get_comment_author_url(), get_comment_author(), get_comment_link(), get_comment_date() );
					?>
				</p>
			</div>

			<div class="comment-entry">
				<?php if ( $comment->comment_approved == '0' ) : ?>
				 	<em class="moderate"><?php _e( 'Your comment is awaiting moderation.', 'bp-daily' ); ?></em>
				<?php endif; ?>

				<?php comment_text() ?>
			</div>

			<div class="comment-options">
					<?php if ( comments_open() ) : ?>
						<?php comment_reply_link( array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ); ?>
					<?php endif; ?>

					<?php if ( current_user_can( 'edit_comment', $comment->comment_ID ) ) : ?>
						<?php printf( '<a class="button comment-edit-link" href="%1$s" title="%2$s">%3$s</a> ', get_edit_comment_link( $comment->comment_ID ), esc_attr__( 'Edit comment', 'bp-daily' ), __( 'Edit', 'bp-daily' ) ) ?>
					<?php endif; ?>

			</div>

		</div>
<?php
	}
}
endif;

///////////////////////////////////////////////////////////////////////////
/* -------------------- Update Notifications Notice -------------------- */
if ( !function_exists( 'wdp_un_check' ) ) {
  add_action( 'admin_notices', 'wdp_un_check', 5 );
  add_action( 'network_admin_notices', 'wdp_un_check', 5 );
  function wdp_un_check() {
    if ( !class_exists( 'WPMUDEV_Update_Notifications' ) && current_user_can( 'edit_users' ) )
      echo '<div class="error fade"><p>' . __('Please install the latest version of <a href="http://premium.wpmudev.org/project/update-notifications/" title="Download Now &raquo;">our free Update Notifications plugin</a> which helps you stay up-to-date with the most stable, secure versions of WPMU DEV themes and plugins. <a href="http://premium.wpmudev.org/wpmu-dev/update-notifications-plugin-information/">More information &raquo;</a>', 'wpmudev') . '</a></p></div>';
  }
}
/* --------------------------------------------------------------------- */
?>