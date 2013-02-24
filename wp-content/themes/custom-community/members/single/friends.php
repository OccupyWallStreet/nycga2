<?php

/**
 * BuddyPress - Users Friends
 *
 * @package BuddyPress
 * @subpackage bp-default
 */

?>

<div class="item-list-tabs no-ajax" id="subnav" role="navigation">
	<ul>
		<?php if ( bp_is_my_profile() ) bp_get_options_nav(); ?>

		<?php if ( !bp_is_current_action( 'requests' ) ) : ?>
			
			<li id="members-order-select" class="last filter">

				<label for="members-all"><?php _e( 'Order By:', 'cc' ) ?></label>
				<select id="members-all">
					<option value="active"><?php _e( 'Last Active', 'cc' ) ?></option>
					<option value="newest"><?php _e( 'Newest Registered', 'cc' ) ?></option>
					<option value="alphabetical"><?php _e( 'Alphabetical', 'cc' ) ?></option>

					<?php do_action( 'bp_member_blog_order_options' ) ?>

				</select>
			</li>

		<?php endif; ?>

			<li id="members-displaymode-select" class="no-ajax displaymode">

				<label for="members-displaymode"><?php _e( 'Display mode:', 'cc' ); ?></label>
				<select id="members-displaymode">
					<option value="list"><?php _e( 'List', 'cc' ); ?></option>
					<option value="grid"><?php _e( 'Grid', 'cc' ); ?></option>
				</select>
			</li>
	</ul>
</div>

<?php

if ( bp_is_current_action( 'requests' ) ) :
	 locate_template( array( 'members/single/friends/requests.php' ), true );

else :
	do_action( 'bp_before_member_friends_content' ); ?>

	<div class="members friends">

		<?php locate_template( array( 'members/members-loop.php' ), true ); ?>

	</div><!-- .members.friends -->

	<?php do_action( 'bp_after_member_friends_content' ); ?>

<?php endif; ?>
