<?php get_header(); ?>

	<div id="content">
		<div class="padder">
			<?php if ( bp_has_groups( 'type=single-group&slug=' . bp_get_groupblog_slug() ) ) : while ( bp_groups() ) : bp_the_group(); ?>

			<?php do_action( 'bp_before_404' ) ?>

			<div id="item-header">
				<?php locate_template( array( 'groups/single/group-header.php' ), true ) ?>
			</div>

			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav">
					<ul>
						<?php bp_groupblog_options_nav() ?>
						
						<?php do_action( 'bp_group_options_nav' ) ?>
					</ul>
				</div>
			</div>

			<div class="page 404">
	
				<h2 class="pagetitle"><?php _e( 'Page Not Found', 'buddypress' ) ?></h2>
	
				<div id="message" class="info">
	
					<p><?php _e( 'The page you were looking for was not found.', 'buddypress' ) ?>
	
				</div>
	
				<?php do_action( 'bp_404' ) ?>
	
			</div>
	
			<?php do_action( 'bp_after_404' ) ?>

			<?php endwhile; endif; ?>
		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer(); ?>