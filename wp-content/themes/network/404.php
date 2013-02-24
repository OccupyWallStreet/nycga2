<?php get_header(); ?>
<div id="container-background">
	<div id="content"><!-- start #content -->
		<div class="padder">
		<?php if($bp_existed == 'true') : ?>
			<?php do_action( 'bp_before_404' ) ?>
		<?php endif; ?>
		<div class="page 404">
			<div id="message" class="info">
					<?php locate_template( array( '/library/components/messages.php' ), true ); ?>
			</div>
			<?php if($bp_existed == 'true') : ?>
				<?php do_action( 'bp_404' ) ?>
		<?php endif; ?>
		</div>
		<?php if($bp_existed == 'true') : ?>
			<?php do_action( 'bp_after_404' ) ?>
		<?php endif; ?>
		</div>
	</div><!-- end #content -->
	<div class="clear">
	</div>
<?php get_footer(); ?>