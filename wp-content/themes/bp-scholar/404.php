<?php get_header(); ?>

	<div id="content">

		<?php do_action( 'bp_before_404' ) ?>

		<div class="page 404">

					<div class="content-box-outer">
			<div class="h3-background">

			<h3><?php _e( 'Page Not Found', 'bp-scholar' ) ?></h3>
</div></div>
			<div id="message" class="info">

				<p><?php _e( 'The page you were looking for was not found.', 'bp-scholar' ) ?>

			</div>

			<?php do_action( 'bp_404' ) ?>

		</div>

		<?php do_action( 'bp_after_404' ) ?>

	</div><!-- #content -->
	<?php get_sidebar('page'); ?>
<?php get_footer(); ?>