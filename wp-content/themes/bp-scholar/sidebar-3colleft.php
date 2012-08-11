<?php include (get_template_directory() . '/options.php'); ?>
<?php if($bp_existed == 'true') : ?>
<?php do_action( 'bp_before_sidebar' ) ?>
<?php endif; ?>
<div id="sidebar">
		<?php if( $bp_existed == 'true' ) { //check if bp existed ?>
	<?php bp_displayed_user_avatar( 'type=full' ) ?>
			<?php } ?>
	<div class="clear"></div>
		<?php if ( is_active_sidebar( '3colleft-sidebar' ) ) : ?>
				<?php dynamic_sidebar( '3colleft-sidebar' ); ?>
		<?php endif; ?>
			<?php if($bp_existed == 'true') : ?>
			<?php do_action( 'bp_inside_after_sidebar' ) ?>
			<?php endif; ?>
</div>
<?php if($bp_existed == 'true') : ?>
<?php do_action( 'bp_after_sidebar' ) ?>
<?php endif; ?>