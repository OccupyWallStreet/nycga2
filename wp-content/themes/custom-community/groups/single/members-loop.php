<?php if ( bp_group_has_members( 'exclude_admins_mods=0' ) ) : ?>
	<div id="pag-top" class="pagination no-ajax">

		<div class="pag-count" id="member-count-top">

			<?php bp_members_pagination_count(); ?>

		</div>

		<div class="pagination-links" id="member-pag-top">

			<?php bp_members_pagination_links(); ?>

		</div>

	</div>

	<?php do_action( 'bp_before_group_members_list' ); ?>

	<?php if(cc_is_displaymode_grid('members')):?>
		<?php get_template_part( "groups/single/members-loop", 'grid'); ?>
	<?php else :?>
		<?php get_template_part( "groups/single/members-loop", 'list'); ?>
	<?php endif; ?>

	<?php do_action( 'bp_after_group_members_list' ); ?>

	<div id="pag-bottom" class="pagination">

		<div class="pag-count" id="member-count-bottom">

			<?php bp_members_pagination_count(); ?>

		</div>

		<div class="pagination-links" id="member-pag-bottom">

			<?php bp_members_pagination_links(); ?>

		</div>

	</div>

	<?php do_action( 'bp_after_group_members_content' ); ?>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'This group has no members.', 'cc' ); ?></p>
	</div>

<?php endif; ?>
