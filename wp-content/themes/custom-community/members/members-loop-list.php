<ul id="members-list" class="item-list" role="main">

<?php while ( bp_members() ) : bp_the_member(); ?>

	<li>
		<div class="item-avatar hidden-phone">
			<a href="<?php bp_member_permalink(); ?>"><?php bp_member_avatar(); ?></a>
		</div>

		<div class="item">
			<div class="item-title">
				<a href="<?php bp_member_permalink(); ?>"><?php bp_member_name(); ?></a>

				<?php if ( bp_get_member_latest_update() ) : ?>

					<span class="update"> <?php bp_member_latest_update(); ?></span>

				<?php endif; ?>

			</div>

			<div class="item-meta"><span class="activity"><?php bp_member_last_active(); ?></span></div>

			<?php do_action( 'bp_directory_members_item' ); ?>

			<?php
			 /***
			  * If you want to show specific profile fields here you can,
			  * but it'll add an extra query for each member in the loop
			  * (only one regardless of the number of fields you show):
			  *
			  * bp_member_profile_data( 'field=the field name' );
			  */
			?>
		</div>

		<div class="action">

			<?php do_action( 'bp_directory_members_actions' ); ?>

		</div>

		<div class="clear"></div>
	</li>

<?php endwhile; ?>

</ul>