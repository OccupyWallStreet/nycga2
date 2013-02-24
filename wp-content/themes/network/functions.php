<?php
define("HIDE_CUSTOMIZATION_OPTION", false);
if ( ! function_exists( 'network_setup' ) ) :
function network_setup() {
	global $themename, $shortname, $shortprefix, $options, $options2, $options3, $bp_existed, $multi_site_on;
	
	load_theme_textdomain('network', get_template_directory() . '/languages/');
	$locale = get_locale();
	$locale_file = get_template_directory() . "/languages/$locale.php";
	if ( is_readable( $locale_file ) )
		require_once( $locale_file );
	
	global $content_width;
	if ( ! isset( $content_width ) ) {
		$content_width = 685;
	}
	
	add_action( 'wp_enqueue_scripts', 'network_load_scripts' );
	add_action( 'widgets_init', 'network_widgets_init' );
	add_action( 'wp_enqueue_scripts', 'network_enqueue_styles' );
	add_action( 'wp_head', 'network_themeoptions_output' );
	
	require( dirname( __FILE__ ) . '/library/functions/conditional-functions.php' );
	if($bp_existed == 'true') {
		require( dirname( __FILE__ ) . '/library/functions/bp-functions.php' );
		add_filter( 'comment_form_defaults', 'wpmudev_comment_form', 10 );
	}

	require( dirname( __FILE__ ) . '/library/functions/custom-functions.php' );
	require( dirname( __FILE__ ) . '/library/functions/option-functions.php' );
	require( dirname( __FILE__ ) . '/library/functions/customizer-functions.php' );
	require( dirname( __FILE__ ) . '/library/functions/loop-functions.php' );
		
	add_theme_support('automatic-feed-links');
} 
endif;
add_action( 'after_setup_theme', 'network_setup');

if ( ! function_exists( 'network_enqueue_styles' ) ) :
function network_enqueue_styles(){
	global $themename, $shortname, $options, $options2, $options3, $bp_existed, $multi_site_on;
	$version = '1.6';
	
	if ($bp_existed){	
wp_enqueue_style( 'network-buddypress', get_template_directory_uri() . '/_inc/css/network-buddypress.css', array(), $version );
	}	
wp_enqueue_style( 'network', get_template_directory_uri() . '/_inc/css/network.css', array(), $version );
}
endif;

if ( ! function_exists( 'network_load_scripts' ) ) :
function network_load_scripts() {
	$version = '1.6';
	if ( !is_admin() ) { 
		wp_enqueue_script("jquery");
		wp_enqueue_script( "jquery-pagination", get_template_directory_uri() . "/library/scripts/jquery.pajinate.min.js" , $version);
		wp_enqueue_script( "jquery-superfish", get_template_directory_uri() . "/library/scripts/superfish.js" , $version);
		wp_enqueue_script( "jquery-custom", get_template_directory_uri() . "/library/scripts/custom.js" , $version);
		if ( is_singular() && get_option( 'thread_comments' ) && comments_open() )
		wp_enqueue_script( 'comment-reply' );
	}
}
endif;

if ( ! function_exists( 'network_themeoptions_output' ) ) :
function network_themeoptions_output(){
include (get_template_directory() . '/library/options/options.php'); 
	$get_current_scheme = get_option('dev_network_custom_style');
	if (($get_current_scheme == "") || ($get_current_scheme == 'default.css')) { ?>
	<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/styles/green.css" type="text/css" media="all" />
	<?php
	}	else { ?>
	<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/styles/<?php echo $get_current_scheme; ?>" type="text/css" media="all" />
	<?php }
		print "<style type='text/css' media='screen'>"; 
		include (get_template_directory() . '/library/options/theme-options.php'); 
		print "</style>";
	?>
	<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/_inc/css/child.css" type="text/css" media="all" />
	<?php
}
endif;

/* Register the widget columns */
function network_widgets_init() {
	global $themename, $shortname, $options, $options2, $options3, $bp_existed, $multi_site_on;	
register_sidebar(
	array(
		'name'          => __( 'Footer', 'network' ),
		'id'            => 'footer-page',
		'description'   => 'Footer',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="widgettitle">',
        'after_title' => '</h4>'
	)
);	
register_sidebar(
	array(
		'name'          => __( 'Sidebar Blog', 'network' ),
		'id'            => 'sidebar-blog',
		'description'   => 'Blog',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="widgettitle">',
        'after_title' => '</h4>'
	)
);
if($bp_existed == 'true') {
register_sidebar(
	array(
		'name'          => __( 'BuddyPress Pages', 'network' ),
		'id'            => 'sidebar-members',
		'description'   => 'BuddyPress pages',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="widgettitle">',
        'after_title' => '</h4>'
	)
);
}
}
add_action( 'widgets_init', 'network_widgets_init' );

if ( !function_exists( 'wpmudev_comment_form' ) ) :
function wpmudev_comment_form( $default_labels ) {
	global $themename, $shortname, $options, $options2, $options3, $bp_existed, $multi_site_on;
	
	if($bp_existed == 'true') :
	global $user_identity;

	$commenter = wp_get_current_commenter();
	$req = get_option( 'require_name_email' );
	$aria_req = ( $req ? " aria-required='true'" : '' );
	$fields =  array(
		'author' => '<p class="comment-form-author">' . '<label for="author">' . __( 'Name', 'network' ) . ( $req ? '<span class="required"> *</span>' : '' ) . '</label> ' .
		            '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></p>',
		'email'  => '<p class="comment-form-email"><label for="email">' . __( 'Email', 'network' ) . ( $req ? '<span class="required"> *</span>' : '' ) . '</label> ' .
		            '<input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /></p>',
		'url'    => '<p class="comment-form-url"><label for="url">' . __( 'Website', 'network' ) . '</label>' .
		            '<input id="url" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></p>',
	);

	$new_labels = array(
		'comment_field'  => '<p class="form-textarea"><textarea name="comment" id="comment" cols="60" rows="10" aria-required="true"></textarea></p>',
		'fields'         => apply_filters( 'comment_form_default_fields', $fields ),
		'logged_in_as'   => '',
		'must_log_in'    => '<p class="alert">' . sprintf( __( 'You must be <a href="%1$s">logged in</a> to post a comment.', 'network' ), wp_login_url( get_permalink() ) )	. '</p>',
		'title_reply'    => __( 'Leave a reply', 'network' )
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
						printf( __( '<a href="%1$s" rel="nofollow">%2$s</a> said on <a href="%3$s"><span class="time-since">%4$s</span></a>', 'network' ), get_comment_author_url(), get_comment_author(), get_comment_link(), get_comment_date() );
					?>
				</p>
			</div>

			<div class="comment-entry">
				<?php if ( $comment->comment_approved == '0' ) : ?>
				 	<em class="moderate"><?php _e( 'Your comment is awaiting moderation.', 'network' ); ?></em>
				<?php endif; ?>

				<?php comment_text() ?>
			</div>

			<div class="comment-options">
					<?php if ( comments_open() ) : ?>
						<?php comment_reply_link( array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ); ?>
					<?php endif; ?>

					<?php if ( current_user_can( 'edit_comment', $comment->comment_ID ) ) : ?>
						<?php printf( '<a class="button comment-edit-link bp-secondary-action" href="%1$s" title="%2$s">%3$s</a> ', get_edit_comment_link( $comment->comment_ID ), esc_attr__( 'Edit comment', 'network' ), __( 'Edit', 'network' ) ) ?>
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
						printf( __( '<a href="%1$s" rel="nofollow">%2$s</a> said on <a href="%3$s"><span class="time-since">%4$s</span></a>', 'network' ), get_comment_author_url(), get_comment_author(), get_comment_link(), get_comment_date() );
					?>
				</p>
			</div>

			<div class="comment-entry">
				<?php if ( $comment->comment_approved == '0' ) : ?>
				 	<em class="moderate"><?php _e( 'Your comment is awaiting moderation.', 'network' ); ?></em>
				<?php endif; ?>

				<?php comment_text() ?>
			</div>

			<div class="comment-options">
					<?php if ( comments_open() ) : ?>
						<?php comment_reply_link( array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ); ?>
					<?php endif; ?>

					<?php if ( current_user_can( 'edit_comment', $comment->comment_ID ) ) : ?>
						<?php printf( '<a class="button comment-edit-link" href="%1$s" title="%2$s">%3$s</a> ', get_edit_comment_link( $comment->comment_ID ), esc_attr__( 'Edit comment', 'network' ), __( 'Edit', 'network' ) ) ?>
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