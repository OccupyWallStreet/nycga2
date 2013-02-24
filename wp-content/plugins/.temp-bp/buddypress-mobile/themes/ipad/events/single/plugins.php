<?php get_header() ?>

	<div id="content">
		<div class="padder">
			<?php if ( bp_jes_has_events() ) : while ( jes_bp_events() ) : bp_jes_the_event(); ?>

			<?php do_action( 'bp_before_event_plugin_template' ) ?>

			<div id="item-header">
				<?php locate_template( array( 'events/single/event-header.php' ), true ) ?>
			</div><!-- #item-header -->

			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav">
					<ul>
						<?php bp_get_options_nav() ?>

						<?php do_action( 'bp_event_plugin_options_nav' ) ?>
					</ul>
				</div>
			</div><!-- #item-nav -->

			<div id="item-body">

				<?php do_action( 'bp_before_event_body' ) ?>

				<?php do_action( 'bp_template_content' ) ?>

				<?php do_action( 'bp_after_event_body' ) ?>
			</div><!-- #item-body -->

			<?php endwhile; endif; ?>

			<?php do_action( 'bp_after_event_plugin_template' ) ?>

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer() ?>