<?php

if ( ! isset( $content_width ) )
	$content_width = 594;


# widgets
function grid_widgets_init() {
	register_sidebar( array(
		'name' => 'Sidebar',
		'id' => 'sidebar',
		'description' => 'Sidebar',
		'before_widget' => '',
		'after_widget' => '',
		'before_title' => '<h3>',
		'after_title' => '</h3>',
	) );
}

add_action( 'widgets_init', 'grid_widgets_init' );




# theme supports
add_theme_support( 'automatic-feed-links' );
add_editor_style();


add_theme_support( 'post-thumbnails' );
set_post_thumbnail_size( 150, 150 ); // default Post Thumbnail dimensions   


add_image_size( 'category-thumb', 300, 9999 ); //300 pixels wide (and unlimited height)
add_image_size( 'homepage-thumb', 410, 332, true ); //(cropped)



// Add Custom Header
define('HEADER_TEXTCOLOR', '');
define('HEADER_IMAGE_WIDTH', 1600);
define('HEADER_IMAGE_HEIGHT', 150);
define('NO_HEADER_TEXT', true );

function grid_header_style() {
    ?><style type="text/css">
        #custom_header {
			margin-top: 0;
			width: <?php echo HEADER_IMAGE_WIDTH; ?>px;
            height: <?php echo HEADER_IMAGE_HEIGHT; ?>px;
        }
    </style><?php
}
function admin_header_style() {
    ?><style type="text/css">
        #custom_header {
            width: <?php echo HEADER_IMAGE_WIDTH; ?>px;
            height: <?php echo HEADER_IMAGE_HEIGHT; ?>px;
        }
    </style><?php
}
add_custom_image_header('grid_header_style', 'admin_header_style');



# Displays post image attachment (sizes: thumbnail, medium, full)
function grid_attachment_image($postid=0, $size='thumbnail', $attributes='') {
	if ($postid<1) $postid = get_the_ID();
	if ($images = get_children(array(
		'post_parent' => $postid,
		'post_type' => 'attachment',
		'numberposts' => 1,
		'post_mime_type' => 'image',)))
		foreach($images as $image) {
			$attachment=wp_get_attachment_image_src($image->ID, $size);
			?><img src="<?php echo $attachment[0]; ?>" <?php echo $attributes; ?> /><?php
		}
}


// navigation menu
function grid_register_main_menus() {
	register_nav_menus(
		array(
			'primary-menu' => __( 'Primary Menu' )
		)
	);
};
if (function_exists('register_nav_menus')) add_action( 'init', 'grid_register_main_menus' );


# custom background
add_custom_background(); 




function grid_trim_excerpt($text) {
  return rtrim($text,'[...]');
}

add_filter('get_the_excerpt', 'grid_trim_excerpt');

add_filter('excerpt_length', 'my_excerpt_length');
function my_excerpt_length($length) {
return 15; // Or whatever you want the length to be.
}


//Tweak Comments
function grid_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case '' :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<div id="comment-<?php comment_ID(); ?>">
		<div class="comment-author vcard">
			<?php echo get_avatar( $comment, 50 ); ?>
			<?php printf( __( '%s '), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
		</div><!-- .comment-author .vcard -->
		<?php if ( $comment->comment_approved == '0' ) : ?>
			<em class="comment-awaiting-moderation">Your comment is awaiting moderation</em>
			<br />
		<?php endif; ?>

		<div class="comment-meta commentmetadata"><small><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
			<?php
				/* translators: 1: date, 2: time */
				printf( __( '%1$s at %2$s'), get_comment_date(),  get_comment_time() ); ?></a><?php edit_comment_link( __( '(Edit)'), ' ' );
			?>
		</small></div><!-- .comment-meta .commentmetadata -->

		<div class="comment-body"><?php comment_text(); ?></div>

		<div class="reply">
			<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
		</div><!-- .reply -->
	</div><!-- #comment-##  -->

	<?php
			break;
		case 'pingback'  :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p>Pingback: <?php comment_author_link(); ?><?php edit_comment_link( __( '(Edit)'), ' ' ); ?></p>
	<?php
			break;
	endswitch;
}



?>