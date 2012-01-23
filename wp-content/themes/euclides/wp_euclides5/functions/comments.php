<?php

function ci_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case '' :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<div id="comment-<?php comment_ID(); ?>" class="comment-text">
			<?php echo get_avatar( $comment, 90 ); ?>
			<div class="comment-copy">
				<p class="comment-meta">
					<?php echo get_comment_author_link() . " - " . get_comment_date() . ' ' . get_comment_time(); ?>
				</p>
				<?php comment_text(); ?>

				<?php if ( $comment->comment_approved == '0' ) : ?>
					<p><em><?php _e( 'Your comment is awaiting moderation.', CI_DOMAIN ); ?></em></p>
				<?php endif; ?>

				<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
			</div>
		</div>
		<?php break; ?>
		
		<?php 	
			case 'pingback':
			case 'trackback':
		?>
			<li class="post pingback">
				<p><?php _e( 'Pingback:', CI_DOMAIN ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __('(Edit)', CI_DOMAIN), ' ' ); ?></p>
			<?php break; ?>
	<?php endswitch; ?>		
		
		
<?php
	
}

?>