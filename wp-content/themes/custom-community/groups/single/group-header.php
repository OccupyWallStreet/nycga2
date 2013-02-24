  <?php do_action( 'bp_before_group_header' ) ?>

    <div id="item-actions">
	<?php if ( bp_group_is_visible() ) : ?>

		<h3><?php _e( 'Group Admins', 'cc' ) ?></h3>
		<?php bp_group_list_admins() ?>

		<?php do_action( 'bp_after_group_menu_admins' ) ?>

		<?php if ( bp_group_has_moderators() ) : ?>
			<?php do_action( 'bp_before_group_menu_mods' ) ?>

			<h3><?php _e( 'Group Mods' , 'cc' ) ?></h3>
			<?php bp_group_list_mods() ?>

			<?php do_action( 'bp_after_group_menu_mods' ) ?>
		<?php endif; ?>

	<?php endif; ?>
</div><!-- #item-actions -->

	<div id="item-header-avatar" class="hidden-phone">
		<a href="<?php bp_group_permalink() ?>" title="<?php bp_group_name() ?>">
			<?php global $cap;  $asize = '150';
			if($cap->bp_groups_avatar_size !=  '') 
				$asize = $cap->bp_groups_avatar_size;
	
			bp_group_avatar('type=full&width='.$asize.'&height='.$asize); ?>
		</a>
	</div><!-- #item-header-avatar -->

<div id="item-header-content">
	<h2><a href="<?php bp_group_permalink() ?>" title="<?php bp_group_name() ?>"><?php bp_group_name() ?></a></h2>
	<span class="highlight"><?php bp_group_type() ?></span> <span class="activity"><?php printf( __( 'active %s ago', 'cc' ), bp_get_group_last_active() ) ?></span>

	<?php do_action( 'bp_before_group_header_meta' ) ?>

	<div id="item-meta">
		<?php bp_group_description() ?>

		<?php bp_group_join_button() ?>

		<?php do_action( 'bp_group_header_meta' ) ?>
	</div>
</div><!-- #item-header-content -->

<?php do_action( 'bp_after_group_header' ) ?>

<?php do_action( 'template_notices' ) ?>