	<?php include (get_template_directory() . '/library/options/options.php'); ?>
<?php if($bp_existed == 'true') : ?>
<?php do_action( 'bp_before_sidebar' ) ?>
<?php endif; ?>
<!-- start blog sidebar -->
<div id="sidebar"><!-- start #sidebar -->
	<div class="padder">
		<?php if ( !is_user_logged_in() ) : ?>
		<?php locate_template( array( '/library/components/signup-box.php' ), true );?>
		<?php endif; ?>
		<?php if($bp_existed == 'true') : ?>
		<?php locate_template( array( '/library/components/buddypress/buddypress-panel.php' ), true ); ?>
		<?php endif; ?>
		<?php if ( is_active_sidebar( 'sidebar-blog' ) ) : ?>
				<?php dynamic_sidebar( 'sidebar-blog' ); ?>
		<?php endif; ?>
			<?php if($bp_existed == 'true') : ?>
			<?php do_action( 'bp_inside_after_sidebar' ) ?>
			<?php endif; ?>
	</div>
</div><!-- end #sidebar -->
<!-- end blog sidebar -->
<?php if($bp_existed == 'true') : ?>
<?php do_action( 'bp_after_sidebar' ) ?>
<?php endif; ?>