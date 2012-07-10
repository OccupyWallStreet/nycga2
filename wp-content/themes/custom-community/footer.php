		<?php do_action('sidebar_right') ?>
		
		</div> <!-- #container -->		

		<?php do_action( 'bp_after_container' ) ?>
		
		<?php do_action( 'bp_before_footer' ) ?>
		
		<div id="footer">
			<!-- Footer Credits 

				If you appreciate our work, please leave the footer credits. 
				If you still want to delete or change them, you can find the concerning code in core/includes/theme-generator/theme-generator.php
			-->
			<?php do_action( 'bp_footer' ) ?>
		</div><!-- #footer -->

		<?php do_action( 'bp_after_footer' ) ?>

	</div><!-- #outerrim -->

	<?php wp_footer(); ?>

	</body>

</html>