<ul id="groups-list" class="item-list displaymode-grid" role="main">

	<?php while ( bp_groups() ) : bp_the_group(); ?>

		<li>
			<div class="item-avatar hidden-phone">
				<a href="<?php bp_group_permalink(); ?>"><?php bp_group_avatar( 'type=full&width=150&height=150' ); ?></a>
			</div>

			<div class="item">
				<div class="item-title"><a href="<?php bp_group_permalink(); ?>" title="<?php bp_group_name(); ?>"><?php bp_group_name(); ?></a></div>
				
				

				<?php do_action( 'bp_directory_groups_item' ); ?>

			</div>

			
			<div class="hoverblock">
				<div class="hoverblockcontainer">
					<div class="action">

						<?php do_action( 'bp_directory_groups_actions' ); ?>
					</div>
					<div class="meta">

						<?php bp_group_type(); ?> / <?php bp_group_member_count(); ?>

					</div>
					<div class="item-meta"><span class="activity"><?php printf( __( 'active %s', 'cc' ), bp_get_group_last_active() ); ?></span></div>
					<div class="item-desc"><?php bp_group_description_excerpt(); ?></div>
				</div>
			</div>
			<div class="clear"></div>
		</li>

	<?php endwhile; ?>

	</ul>