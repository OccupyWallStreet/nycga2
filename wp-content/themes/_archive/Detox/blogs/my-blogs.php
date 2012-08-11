<?php get_header() ?>

	<div id="main">
	<div class="content-header">
		<?php bp_blogs_blog_tabs() ?>
	</div>

		<?php do_action( 'template_notices' ) // (error/success feedback) ?>
		
		<h2><?php bp_word_or_name( __( "My Blogs" ), __( "%s's Blogs" ) ) ?></h2>

		<?php do_action( 'bp_before_my_blogs_content' ) ?>

		<?php if ( bp_has_blogs() ) : ?>
			
				<?php while ( bp_blogs() ) : bp_the_blog(); ?>
					
					<div class="bpentry">
			
						<h2><a href="<?php bp_blog_permalink() ?>"><?php bp_blog_title() ?></a></h2>
						<p><?php bp_blog_description() ?></p>

						<?php do_action( 'bp_my_blogs_item' ) ?>
					</div>
					
				<?php endwhile; ?>
		
			<?php do_action( 'bp_my_blogs_content' ) ?>
			
		<?php else: ?>

			<div id="message" class="info">
				<p><?php bp_word_or_name( __( "You haven't created any blogs yet." ), __( "%s hasn't created any public blogs yet." ) ) ?> <?php bp_create_blog_link() ?> </p>
			</div>

		<?php endif;?>

		<?php do_action( 'bp_after_my_blogs_content' ) ?>

	</div>

</div>
</div>
<div class="ground"></div>
<?php include(TEMPLATEPATH."/inc/ss_footer.php");?>
<?php include(TEMPLATEPATH."/inc/footer.php");?>