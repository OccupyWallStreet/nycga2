<?php get_header() ?>
<div id="main">
	<div class="content-header">
		<?php bp_blogs_blog_tabs() ?>
	</div>

		<?php do_action( 'template_notices' ) // (error/success feedback) ?>
		
		<h2><?php _e("Recent Posts"); ?></h2>

		<?php do_action( 'bp_before_recent_posts_content' ) ?>		

		<?php if ( bp_has_posts() ) : ?>
			
			<?php while ( bp_posts() ) : bp_the_post(); ?>
			
				<div class="bpentry">

					<h2><a href="<?php bp_post_permalink() ?>" rel="bookmark" title="<?php printf ( __( 'Permanent Link to %s' ), bp_post_title( false ) ); ?>"><?php bp_post_title(); ?></a></h2>
					
					<p class="date"><?php printf( __( '%1$s <em>in %2$s by %3$s</em>' ), bp_post_date(__('F jS, Y'), false ), bp_post_category( ', ', '', null, false ), bp_post_author( false ) ); ?></p>
					
					<p class="postmetadata"><?php bp_post_tags( '<span class="tags">', ', ', '</span>' ); ?>  <span class="comments"><?php bp_post_comments( __('No Comments'), __('1 Comment'), __('% Comments') ); ?></span></p>
					
					<?php do_action( 'bp_recent_posts_item' ) ?>		

					<div class="clearfix"></div><hr class="clear" />

					
				</div>
			
			<?php endwhile; ?>

			<?php do_action( 'bp_recent_posts_content' ) ?>		
				
		<?php else: ?>

			<div id="message" class="info">
				<p><?php bp_word_or_name( __( "You haven't made any posts yet." ), __( "%s hasn't made any posts yet." ) ) ?></p>
			</div>

		<?php endif;?>

		<?php do_action( 'bp_after_recent_posts_content' ) ?>		

	</div>

</div>
</div>
<div class="ground"></div>
<?php include(TEMPLATEPATH."/inc/ss_footer.php");?>
<?php include(TEMPLATEPATH."/inc/footer.php");?>