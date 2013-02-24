<?php

/**
 * BuddyPress - Users Groups
 *
 * @package BuddyPress
 * @subpackage bp-default
 */

?>

<div class="item-list-tabs no-ajax" id="subnav" role="navigation">
	<ul>
		<?php if ( bp_is_my_profile() ) bp_get_options_nav(); ?>

		<?php if ( !bp_is_current_action( 'invites' ) ) : ?>

			<li id="groups-order-select" class="last filter">

				<label for="groups-sort-by"><?php _e( 'Order By:', 'cc' ); ?></label>
				<select id="groups-sort-by">
					<option value="active"><?php _e( 'Last Active', 'cc' ); ?></option>
					<option value="popular"><?php _e( 'Most Members', 'cc' ); ?></option>
					<option value="newest"><?php _e( 'Newly Created', 'cc' ); ?></option>
					<option value="alphabetical"><?php _e( 'Alphabetical', 'cc' ); ?></option>

					<?php do_action( 'bp_member_group_order_options' ) ?>

				</select>
			</li>

		<?php endif; ?>
		<li id="groups-displaymode-select" class="no-ajax displaymode">

			<label for="groups-displaymode"><?php _e( 'Display mode:', 'cc' ); ?></label>
			<select id="groups-displaymode">
				<option value="list"><?php _e( 'List', 'cc' ); ?></option>
				<option value="grid"><?php _e( 'Grid', 'cc' ); ?></option>

				<?php do_action( 'bp_groups_directory_displaymode_options' ); ?>

			</select>
		</li>

	</ul>
</div><!-- .item-list-tabs -->

<?php

if ( bp_is_current_action( 'invites' ) ) :
	locate_template( array( 'members/single/groups/invites.php' ), true );

else :
	do_action( 'bp_before_member_groups_content' ); ?>

	<div class="groups mygroups">

		<?php locate_template( array( 'groups/groups-loop.php' ), true ); ?>

	</div>

	<?php do_action( 'bp_after_member_groups_content' ); ?>

<?php endif; ?>
