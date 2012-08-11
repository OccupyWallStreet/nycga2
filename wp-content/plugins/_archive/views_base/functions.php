<?php
/**
 * Views Base Theme functions and definitions
 *
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
 *
 * @package views_base
 */

/**
 * include base theme class
 */
if(!class_exists('class_base_theme')) 
{
	require_once('class_base_theme.php');
}

/**
 *  new class_base_theme.
 */
if(!isset($class_base_theme))
{
	$class_base_theme = new class_base_theme();
}


/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 584;

if ( ! function_exists( 'views_base_content_nav' ) ) 
{
/**
 * Display navigation to next/previous pages when applicable
 *
 */
	function views_base_content_nav( $nav_id ) 
	{
		global $wp_query;

		if ( $wp_query->max_num_pages > 1 ) 
		{?>
			<nav id="<?php echo $nav_id; ?>" role="navigation">
				<h3 class="assistive-text"><?php _e( 'Post navigation', 'views_base' ); ?></h3>
				<div class="nav-previous alignleft"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'views_base' ) ); ?></div>
				<div class="nav-next alignright"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'views_base' ) ); ?></div>
			</nav><!-- #nav-above -->
		<?php 
		}
	}
}

if ( ! function_exists( 'views_base_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own views_base_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 */
function views_base_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
		// Display trackbacks differently than normal comments.
	?>
	<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
		<p><?php _e( 'Pingback:', 'views_base' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( '(Edit)', 'views_base' ), '<span class="edit-link">', '</span>' ); ?></p>
	<?php
			break;
		default :
		// Proceed with normal comments.
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment">
			<header class="comment-meta comment-author vcard">
				<?php
					echo get_avatar( $comment, 44 );

					printf( '<cite class="fn">%s</cite>', get_comment_author_link() );
					printf( '<a href="%1$s"><time pubdate datetime="%2$s">%3$s</time></a>',
						esc_url( get_comment_link( $comment->comment_ID ) ),
						get_comment_time( 'c' ),
						/* translators: 1: date, 2: time */
						sprintf( __( '%1$s at %2$s', 'views_base' ), get_comment_date(), get_comment_time() )
					);
				?>
				<?php edit_comment_link( __( 'Edit', 'views_base' ), '<span class="edit-link">', '</span>' ); ?>
			</header>

			<?php if ( '0' == $comment->comment_approved ) : ?>
				<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'views_base' ); ?></p>
			<?php endif; ?>

			<section class="comment post-content">
				<?php comment_text(); ?>
			</section>

			<div class="reply">
				<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply <span>&darr;</span>', 'views_base' ), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
			</div><!-- .reply -->
		</article><!-- #comment-## -->
	<?php
		break;
	endswitch; // end comment_type check
}
endif;

if ( ! function_exists( 'views_base_posted_on' ) ) :
/**
 * Prints HTML with information for the current post author and published date/time.
 *
 * Create your own views_base_posted_on() to override in a child theme.
 *
 * @uses views_base_posted_by()
 */
function views_base_posted_on( $return = false ) {
	$out = sprintf( __( '<a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s" pubdate>%4$s</time></a>%5$s', 'views_base' ),
		esc_url( get_permalink() ),
		esc_attr( get_the_time() ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		views_base_posted_by( true )
	);
	if ( $return )
		return $out;
	echo $out;
}
endif;


if ( ! function_exists( 'views_base_posted_by' ) ) :
/**
 * Prints HTML with meta information for the current post author.
 *
 * Create your own views_base_posted_by() to override in a child theme.
 *
 */
function views_base_posted_by( $return = false ) {
	//Show author info for blog posts only
	$out = null;
	if(get_post_type() != 'post')return $out;
	$out = sprintf( __( '<span class="by-author"> <span class="sep"> by </span> <span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span></span>', 'views_base' ),
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		esc_attr( sprintf( __( 'View all posts by %s', 'views_base' ), get_the_author() ) ),
		get_the_author()
	);
	if ( $return )
		return $out;

	echo $out;
}
endif;

if ( ! function_exists( 'views_base_entry_meta' ) ) :
/**
 * Prints HTML with meta information for current post: categories, tags, permalink, author, and date.
 *
 * Create your own views_base_entry_meta() to override in a child theme.
 *
 * @uses views_base_posted_on()
 */
function views_base_entry_meta() {
	/* translators: used between list items, there is a space after the comma */
	$categories_list = get_the_category_list( __( ', ', 'views_base' ) );

	/* translators: used between list items, there is a space after the comma */
	$tag_list = get_the_tag_list( '', __( ', ', 'views_base' ) );

	if ( '' != $tag_list ) {
		$utility_text = __( 'This entry was posted in %1$s and tagged %2$s on %3$s.', 'views_base' );
	} elseif ( '' != $categories_list ) {
		$utility_text = __( 'This entry was posted in %1$s on %3$s.', 'views_base' );
	} else {
		$utility_text = __( 'This entry was posted on %3$s.', 'views_base' );
	}

	printf(
		$utility_text,
		$categories_list,
		$tag_list,
		views_base_posted_on( true )
	);
}
endif;