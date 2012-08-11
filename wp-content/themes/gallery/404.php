<?php get_header() ?>
<?php if($bp_existed == 'true') : ?>
	<?php do_action( 'bp_before_404' ) ?>
<?php endif; ?>
<div id="site-container">
<div id="content"><!-- start #content -->
	<div class="padder">
		<div class="page 404">
			<?php locate_template( array( '/library/components/messages.php' ), true ); ?>
		</div>
		<?php if($bp_existed == 'true') : ?>
			<?php do_action( 'bp_404' ) ?>
		<?php endif; ?>
	</div>
</div><!-- end #content -->
	<?php get_sidebar('blog'); ?>
	<div class="clear"></div>
</div>
<?php if($bp_existed == 'true') : ?>
	<?php do_action( 'bp_after_404' ) ?>
<?php endif; ?>
<?php get_footer() ?>
