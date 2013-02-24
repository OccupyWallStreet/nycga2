<ul id="member-list" class="item-list" role="main">

	<?php while ( bp_group_members() ) : bp_group_the_member(); ?>

		<li>
            <a href="<?php bp_group_member_domain(); ?>" class="hidden-phone">

				<?php bp_group_member_avatar_thumb(); ?>

			</a>

			<h5><?php bp_group_member_link(); ?></h5>
			<span class="activity"><?php bp_group_member_joined_since(); ?></span>

			<?php do_action( 'bp_group_members_list_item' ); ?>

			<?php if ( bp_is_active( 'friends' ) ) : ?>

				<div class="action">

					<?php bp_add_friend_button( bp_get_group_member_id(), bp_get_group_member_is_friend() ); ?>

					<?php do_action( 'bp_group_members_list_item_action' ); ?>

				</div>

			<?php endif; ?>
		</li>

	<?php endwhile; ?>

</ul>