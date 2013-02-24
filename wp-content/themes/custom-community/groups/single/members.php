
	<?php do_action( 'bp_before_group_members_content' ); ?>

	<div class="item-list-tabs" id="subnav" role="navigation">
		<ul>

			<?php do_action( 'bp_members_directory_member_sub_types' ); ?>
			
			<?php /*
				It is not working
			<li id="members-order-select" class="last filter">

				<label for="members-order-by"><?php _e( 'Order By:', 'cc' ); ?></label>
				<select id="members-order-by">
					<option value="active"><?php _e( 'Last Active', 'cc' ); ?></option>
					<option value="newest"><?php _e( 'Newest Registered', 'cc' ); ?></option>

					<?php if ( bp_is_active( 'xprofile' ) ) : ?>

						<option value="alphabetical"><?php _e( 'Alphabetical', 'cc' ); ?></option>

					<?php endif; ?>

					<?php do_action( 'bp_members_directory_order_options' ); ?>

				</select>
			</li>*/?>

			<li id="members-displaymode-select" class="last displaymode">

				<label for="members-displaymode"><?php _e( 'Display mode:', 'cc' ); ?></label>
				<select id="members-displaymode">
					<option value="list"><?php _e( 'List', 'cc' ); ?></option>
					<option value="grid"><?php _e( 'Grid', 'cc' ); ?></option>
				</select>
			</li>
		</ul>
	</div>

	<div id="members-dir-list" class="members dir-list">

		<?php get_template_part( "groups/single/members-loop" );?>

	</div>
