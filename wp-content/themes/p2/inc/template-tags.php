<?php
/**
 * Template tags.
 *
 * @package P2
 * @since unknown
 */

function p2_body_class( $classes ) {
	if ( is_tax( P2_MENTIONS_TAXONOMY ) )
		$classes[] = 'mentions';
	if ( p2_is_iphone() )
		$classes[] = 'iphone';
	return $classes;
}
add_filter( 'body_class', 'p2_body_class' );

function p2_user_can_post() {
	global $user_ID;

	if ( current_user_can( 'publish_posts' ) || ( get_option( 'p2_allow_users_publish' ) && $user_ID ) )
		return true;

	return false;
}

function p2_show_comment_form() {
	global $post, $form_visible;

	$show = ( !isset( $form_visible ) || !$form_visible ) && 'open' == $post->comment_status;

	if ( $show )
		$form_visible = true;

	return $show;
}

function p2_is_ajax_request() {
	global $post_request_ajax;

	return ( $post_request_ajax ) ? $post_request_ajax : false;
}

function p2_media_upload_form() {
	require( ABSPATH . '/wp-admin/includes/template.php' );
	media_upload_form();
?>
<?php
}

function p2_user_display_name() {
	echo p2_get_user_display_name();
}

function p2_get_user_display_name() {
	$current_user = wp_get_current_user();

	return apply_filters( 'p2_get_user_display_name', isset( $current_user->first_name ) && $current_user->first_name ? $current_user->first_name : $current_user->display_name );
}

function p2_discussion_links() {
	echo p2_get_discussion_links();
}

function p2_get_discussion_links() {
	$comments = get_comments( array( 'post_id' => get_the_ID() ) );

	$unique_commentors = array();
	foreach ( $comments as $comment ) {
		if ( '1' == $comment->comment_approved )
			$unique_commentors[$comment->comment_author_email] = get_avatar( $comment, 16 ) . ' ' . get_comment_author_link( $comment->comment_ID );
	}

	$unique_commentors = array_values( $unique_commentors );
	$total_unique_commentors = count( $unique_commentors );

	$content = '';

	if ( 1 == $total_unique_commentors ) {
		$content = sprintf( __( '%1$s is discussing.', 'p2' ), $unique_commentors[0] );
	} else if ( 2 == $total_unique_commentors ) {
		$content = sprintf( __( '%1$s and %2$s are discussing.', 'p2' ),
			$unique_commentors[0],
			$unique_commentors[1]
		);
	} else if ( 3 == $total_unique_commentors ) {
		$content = sprintf( __( '%1$s, %2$s, and %3$s are discussing.', 'p2' ),
			$unique_commentors[0],
			$unique_commentors[1],
			$unique_commentors[2]
		);
	} else if ( 3 < $total_unique_commentors ) {
		$others = $total_unique_commentors - 3;
		$content .= sprintf( _n( '%1$s, %2$s, %3$s, and %4$d other are discussing.', '%1$s, %2$s, %3$s, and %4$d others are discussing.', $others, 'p2' ),
			$unique_commentors[0],
			$unique_commentors[1],
			$unique_commentors[2],
			$others
		);
	}

	return $content;
}

function p2_quote_content() {
	echo p2_get_quote_content();
}
	function p2_get_quote_content() {
		return apply_filters( 'p2_get_quote_content', get_the_content( __( '(More ...)' , 'p2' ) ) );
	}
	add_filter( 'p2_get_quote_content', 'p2_quote_filter_kses', 1 );
	add_filter( 'p2_get_quote_content', 'wptexturize' );
	add_filter( 'p2_get_quote_content', 'convert_smilies' );
	add_filter( 'p2_get_quote_content', 'convert_chars' );
	add_filter( 'p2_get_quote_content', 'prepend_attachment' );
	add_filter( 'p2_get_quote_content', 'make_clickable' );

	function p2_quote_filter_kses( $content ) {
		global $allowedtags;

		$quote_allowedtags = $allowedtags;
		$quote_allowedtags['cite'] = array();
		$quote_allowedtags['p'] = array();

		return wp_kses( $content, $quote_allowedtags );
	}

/**
 * Get post format for current post object.
 *
 * The value should be a valid post format or one of the back compat categories.
 *
 * @since P2 1.3.4
 * @uses p2_get_the_category for back compat category check
 * @uses p2_get_supported_post_formats for accepted values
 *
 * @param object post_id Uses global post if in the loop; required for use outside the loop
 * @return string
 */
function p2_get_post_format( $post_id = null ) {
	if ( is_null( $post_id ) ) {
		global $post;
		$post_id = $post->ID;
	}

	if ( empty( $post_id ) )
		return '';

	// 1- try to get post format, first
	$post_format = get_post_format( $post_id );

	// 2- try back compat category, next
	if ( false === $post_format )
		$post_format = p2_get_the_category( $post_id );

	// Check against accepted values
	if ( empty( $post_format ) || ! in_array( $post_format, p2_get_supported_post_formats() ) )
		$post_format = 'standard';

	return $post_format;
}

function p2_get_the_category( $post_id = null ) {
	$categories = get_the_category( $post_id );
	$slug = ( isset( $categories[0] ) ) ? $categories[0]->slug : '';
	return apply_filters( 'p2_get_the_category', $slug );
}

function p2_user_prompt() {
	echo p2_get_user_prompt();
}
	function p2_get_user_prompt() {
		$prompt = get_option( 'p2_prompt_text' );

		return apply_filters( 'p2_get_user_prompt', sprintf ( __( 'Hi, %s. %s', 'p2' ), esc_html( p2_get_user_display_name() ), ( $prompt != '' ) ? stripslashes( $prompt ) : __( 'Whatcha up to?', 'p2' ) ) );
	}

function p2_page_number() {
	echo p2_get_page_number();
}
	function p2_get_page_number() {
		global $paged;
		return apply_filters( 'p2_get_page_number', $paged );
	}

function p2_media_buttons() {
	// If we're using http and the admin is forced to https, bail.
	if ( ! is_ssl() && ( force_ssl_admin() || get_user_option( 'use_ssl' ) )  ) {
		return;
	}

	include_once( ABSPATH . '/wp-admin/includes/media.php' );
	ob_start();
	do_action( 'media_buttons' );
	$buttons = ob_get_clean();

	// Replace any relative paths to media-upload.php
	$buttons = preg_replace( '/([\'"])media-upload.php/', '${1}' . admin_url( 'media-upload.php' ), $buttons );

	// Remove any images.
	$buttons = preg_replace( '/<img [^>]*src=(\"|\')(.+?)(\1)[^>]*>/i', '', $buttons );

	echo $buttons;
}

function p2_get_hide_sidebar() {
	return ( '' != get_option( 'p2_hide_sidebar' ) ) ? true : false;
}

function p2_archive_author() {
	echo p2_get_archive_author();
}

function p2_get_archive_author() {
	$author = '';
	if ( is_author() )
		$author = get_the_author_meta( 'display_name', get_queried_object_id() );

	return apply_filters( 'p2_get_archive_author', $author );
}

function p2_author_feed_link() {
	echo p2_get_author_feed_link();
}
	function p2_get_author_feed_link() {

		$author_id = get_queried_object_id();

		if ( isset( $author_id ) )
			return apply_filters( 'p2_get_author_feed_link', get_author_feed_link( $author_id ) );
	}

function p2_user_identity() {
	echo p2_get_user_identity();
}
	function p2_get_user_identity() {
		global $user_identity;
		return $user_identity;
	}

function p2_load_entry( $force_comments = true ) {
	global $withcomments;

	if ( $force_comments )
		$withcomments = true;

	get_template_part( 'entry' );
}

function p2_date_time_with_microformat( $type = 'post' ) {
	$d = 'comment' == $type ? 'get_comment_time' : 'get_post_time';
	return '<abbr title="'.$d( 'Y-m-d\TH:i:s\Z', true).'">'.sprintf( __( '%1$s <em>on</em> %2$s', 'p2' ),  $d(get_option( 'time_format' )), $d( get_option( 'date_format' ) ) ).'</abbr>';
}