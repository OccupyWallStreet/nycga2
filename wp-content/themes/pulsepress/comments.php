<?php
/**
 * @package WordPress
 * @subpackage PulsePress
 */
?>

<?php if ( post_password_required() )
	return;
?>

<?php if ( get_comments_number() > 0 ) : ?>

<ul class="commentlist inlinecomments">
	<?php wp_list_comments( array( 'callback' => 'pulse_press_comments' ) ); ?>
</ul>
<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
			<div class="navigation">
				<div class="nav-previous"><?php previous_comments_link( __( '<span class="meta-nav">&larr;</span> Older Comments', 'pulse_press' ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( __( 'Newer Comments <span class="meta-nav">&rarr;</span>', 'pulse_press' ) ); ?></div>
			</div><!-- .navigation -->
<?php endif; // check for comment navigation ?>

<?php endif; ?>