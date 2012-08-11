	<?php include (get_template_directory() . '/options.php'); ?>
	<div class="clear"></div>
	</div>
</div>
<?php if($bp_existed == 'true') : ?>
	<?php do_action( 'bp_after_container' ) ?>
	<?php do_action( 'bp_before_footer' ) ?>
<?php endif ?>
			<div id="footer-wrapper">
				<div id="footer">
					<div id="footer-sidebars">
							<?php get_sidebar('footer1'); ?>
							<?php get_sidebar('footer2'); ?>
							<?php get_sidebar('footer3'); ?>
							<?php get_sidebar('footer4'); ?>
								<div class="clear"></div>
	</div>

			<div id="footer-links">
			    <a href="http://premium.wpmudev.org/themes/" title="<?php _e( 'WordPress Plugins, Themes and Support', 'bp-scholar' )?>" ><?php _e( 'WPMU DEV - The WordPress Experts', 'bp-scholar' ) ?></a><a href="<?php echo home_url(); ?>"><?php _e( 'Copyright', 'bp-scholar' ) ?> &copy;<?php echo gmdate(__('Y')); ?> <?php get_bloginfo( 'name' ); ?></a><a href="#header"><?php _e('Go back to top &uarr;', 'bp-scholar'); ?></a>
					<?php if($bp_existed == 'true') : ?>
						<?php do_action( 'bp_footer' ) ?>
					<?php endif; ?>
			</div>
				<?php if($bp_existed == 'true') : ?>
					<?php do_action( 'bp_after_footer' ) ?>
				<?php endif; ?>
			<?php wp_footer(); ?>

				</div>
			</div>
		</body>
		</html>