<?php get_header() ?>

	<div id="main">
	<div class="content-header">
		<?php bp_blogs_blog_tabs() ?>
	</div>

		<?php do_action( 'template_notices' ) // (error/success feedback) ?>
			
		<h2><?php _e("Recent Comments", "buddypress"); ?></h2>

		<?php do_action( 'bp_before_recent_comments_content' ) ?>

		<?php if ( bp_has_comments() ) : ?>

			<ul>
				<?php while ( bp_comments() ) : bp_the_comment(); ?>
					
					<li id="comment-<?php bp_comment_id() ?>">
						<span class="small"><?php printf( __( 'On %1$s %2$s said:', 'buddypress' ), bp_comment_date( __( 'F jS, Y', 'buddypress' ), false ), bp_comment_author( false ) ); ?></span>
						<p><?php bp_comment_content() ?></p>
						<span class="small"><?php printf( __( 'Commented on the post <a href="%1$s">%2$s</a> on the blog <a href="%3$s">%4$s</a>.', 'buddypress' ), bp_comment_post_permalink( false ), bp_comment_post_title( false ), bp_comment_blog_permalink( false ), bp_comment_blog_name( false ) ); ?></span>

						<?php do_action( 'bp_recent_comments_item' ) ?>
					</li>
					
				<?php endwhile; ?>
			</ul>
			
			<?php do_action( 'bp_recent_comments_content' ) ?>

		<?php else: ?>

			<div id="message" class="info">
				<p><?php bp_word_or_name( __( "You haven't posted any comments yet.", 'buddypress' ), __( "%s hasn't posted any comments yet." ) ) ?></p>
			</div>

		<?php endif;?>

		<?php do_action( 'bp_after_recent_comments_content' ) ?>

	</div>

</div>
</div>
<div class="ground"></div>
<?php include(TEMPLATEPATH."/inc/ss_footer.php");?>
<?php include(TEMPLATEPATH."/inc/footer.php");?>