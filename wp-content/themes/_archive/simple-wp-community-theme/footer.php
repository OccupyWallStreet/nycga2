
		</div> <!-- #container -->

		<?php do_action( 'bp_after_container' ) ?>
		<?php do_action( 'bp_before_footer' ) ?>

		<div id="footer">
		<p>Kindly supported by <a href="http://sven-lehnert.de/en" target="_blank" title="Wordpress/Buddypress Theme Developer Sven Lehnert" alt="Wordpress/Buddypress Theme Developer Sven Lehnert">Wordpress Developer</a> Sven Lehnert &amp; <a href="http://konradsroka.com" target="_blank" title="Wordpress/Buddypress Theme Designer Konrad Sroka" alt="Wordpress/Buddypress Theme Designer Konrad Sroka">Wordpress Designer</a> Konrad Sroka</p>
			<p><?php printf( __( '%s is proudly powered by <a href="http://wordpress.org">WordPress</a> and <a href="http://buddypress.org">BuddyPress</a>', 'buddypress' ), bloginfo('name') ); ?></p>

			<?php do_action( 'bp_footer' ) ?>
		</div><!-- #footer -->

		<?php do_action( 'bp_after_footer' ) ?>


</div>
</div>
		<?php wp_footer(); ?>
	</body>

</html>