		</div> <!-- #container -->

		<?php do_action( 'bp_after_container' ) ?>
		<?php do_action( 'bp_before_footer' ) ?>

		<div id="footer">
	    	<p><?php printf( __( '%s is proudly powered by <a href="http://wordpress.org">WordPress</a> and <a href="http://buddypress.org">BuddyPress</a>', 'buddypress' ), bloginfo('name') ); ?></p>

			<?php do_action( 'bp_footer' ) ?>
		</div><!-- #footer -->

		<?php do_action( 'bp_after_footer' ) ?>

		<div id="notify"></div>
		
		<div id="help">
			<dl class="directions">
				<dt>c</dt><dd><?php _e('compose new post', 'p2'); ?></dd>
				<dt>j</dt><dd><?php _e('next post/next comment', 'p2'); ?></dd>
				<dt>k</dt> <dd><?php _e('previous post/previous comment', 'p2'); ?></dd>
				<dt>r</dt> <dd><?php _e('reply', 'p2'); ?></dd>
				<dt>e</dt> <dd><?php _e('edit', 'p2'); ?></dd>
				<dt>o</dt> <dd><?php _e('show/hide comments', 'p2'); ?></dd>
				<dt>t</dt> <dd><?php _e('go to top', 'p2'); ?></dd>
				<dt>l</dt> <dd><?php _e('go to login', 'p2'); ?></dd>
				<dt>h</dt> <dd><?php _e('show/hide help', 'p2'); ?></dd>
				<dt>esc</dt> <dd><?php _e('cancel', 'p2'); ?></dd>
			</dl>
		</div>
		
		<?php wp_footer(); ?>

	</body>

</html>