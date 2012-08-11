	<?php include (get_template_directory() . '/library/options/options.php'); ?>
			<div class="clear"></div>
		</div><!--end container -->
					<div class="clear"></div>
				<?php 
					$gallerymenu = get_option('dev_gallery_gallerymenu');
					if ($gallerymenu == "yes"){
						locate_template( array( '/library/components/navigation-gallery.php' ), true ); 
					}
				?>
			<?php 
				$widgets = get_option('dev_gallery_widgets');
				if ($widgets == "yes"){
					locate_template( array( '/library/components/footer-widgets.php' ), true ); 
				}
			?>
			<div class="clear"></div>
	</div><!-- end #site-wrapper or #site-wrapper-home -->
<?php if($bp_existed == 'true') : ?>
	<?php do_action( 'bp_after_container' ) ?>
	<?php do_action( 'bp_before_footer' ) ?>
<?php endif; ?>
	<div id="footer-wrapper">
		<div id="footer">
			<?php locate_template( array( '/library/components/option-footerlinks.php' ), true ); ?>
	<?php if($bp_existed == 'true') : ?>
		<?php do_action( 'bp_footer' ) ?>
	<?php endif; ?>
						</div>
											</div>
			<?php if($bp_existed == 'true') : ?>
				<?php do_action( 'bp_after_footer' ) ?>
			<?php endif; ?>
				<?php wp_footer(); ?>
					<!-- start google code-->
					<?php $googlecode = get_option('dev_gallery_google');
					echo stripslashes($googlecode);
					?>
					<!-- end google code -->
	</body>
</html>