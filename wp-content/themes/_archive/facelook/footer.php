		</div> <!-- #container -->

		<?php do_action( 'bp_after_container' ) ?>
		<div class="clear"></div>
		<?php do_action( 'bp_before_footer' ) ?>

		<div id="footer">
		<p>Copyright &copy;2010 lincme.co.uk

		<?php if ( is_site_admin() ) : ?>
			&nbsp;&middot;&nbsp;<a href="<?php echo site_url() ?>/wp-admin" target="_blank">WP Admin page</a>
		<?php endif; ?>
		</p>

		<?php do_action( 'bp_footer' ) ?>
		</div>
		<?php do_action( 'bp_after_footer' ) ?>
		<?php wp_footer(); ?>
	</body>
</html>