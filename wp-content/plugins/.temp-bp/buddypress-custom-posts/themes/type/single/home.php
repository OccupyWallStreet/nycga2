<?php get_header() ?>

	<div id="content">
		<div class="padder">
			<?php the_post(); ?>

			<div id="item-header">
				<?php bpcp_locate_template( array( 'type/single/type-header.php' ), true ) ?>
			</div><!-- #item-header -->

			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav">
					<ul>
						<?php bp_get_options_nav() ?>
					</ul>
				</div>
			</div><!-- /#item-nav -->

			<div id="item-body">
				<?php
					global $bp;
					$component = $bp->active_components[ $bp->current_component ];
					$templates = Array();
					$templates = apply_filters( 'bpcp_single_home_template', $templates, $component );
					$templates = apply_filters( 'bpcp_' . $component . '_single_home_template', $templates );

					if ( $templates != Array() ) 
						bpcp_locate_template( $templates, true );
					else if ( bpcp_is_edit() && bpcp_can_edit() )
						bpcp_locate_template( array( 'type/single/edit.php' ), true );
					else if ( bpcp_is_forum() )
						bpcp_locate_template( array( 'type/single/forum.php' ), true );
					else if ( bp_is_active( 'activity' ) && bpcp_is_activity() )
						bpcp_locate_template( array( 'type/single/activity.php' ), true );
					else
						bpcp_locate_template( array( 'type/single/content.php' ), true );
				?>	
			</div><!-- #item-body -->
		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer() ?>
