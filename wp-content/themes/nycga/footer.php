		</div> <!-- #container -->

		<?php do_action( 'bp_after_container' ) ?>
		<?php do_action( 'bp_before_footer' ) ?>

		<div id="footer">
			<?php if ( is_active_sidebar( 'first-footer-widget-area' ) || is_active_sidebar( 'second-footer-widget-area' ) || is_active_sidebar( 'third-footer-widget-area' ) || is_active_sidebar( 'fourth-footer-widget-area' ) ) : ?>
				<div id="footer-widgets">
					<?php get_sidebar( 'footer' ) ?>
				</div>
			<?php endif; ?>

			<div id="site-generator" role="contentinfo">
				<?php do_action( 'bp_dtheme_credits' ) ?>
				<p>
					<?php printf( __( 'Created in <a href="%1$s">WordPress</a> and <a href="%2$s">BuddyPress</a> by <a href="%3$s">#OccupyWallStreet Tech Ops</a>.', 'buddypress' ), 'http://wordpress.org', 'http://buddypress.org', 'http://nycga.net/groups/internet') ?>
					<?php printf("You can't copyright a movement") ?>
				</p>
			</div>

			<?php do_action( 'bp_footer' ) ?>
		</div><!-- #footer -->

		<?php do_action( 'bp_after_footer' ) ?>

		<?php wp_footer(); ?>

	</body>

</html>