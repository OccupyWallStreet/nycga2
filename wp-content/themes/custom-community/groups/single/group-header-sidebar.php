<?php global $cap;?>
<?php do_action( 'bp_before_group_header' ) ?>
<?php if ( bp_has_groups() ) : while ( bp_groups() ) : bp_the_group(); ?>

	<div id="item-header-avatar" class="hidden-phone">
		<a href="<?php bp_group_permalink() ?>" title="<?php bp_group_name() ?>">
			<?php $asize = '150';
			if($cap->bp_groups_avatar_size !=  '') 
				$asize = $cap->bp_groups_avatar_size;

			bp_group_avatar('type=full&width='.$asize.'&height='.$asize); ?>
		</a>
	</div><!-- #item-header-avatar -->

	<h3 style="" class="widgettitle"><a href="<?php bp_group_permalink() ?>" title="<?php bp_group_name() ?>"><?php bp_group_name() ?></a></h3>
	
	<span class="highlight"><?php bp_group_type() ?></span> <span class="activity"><?php printf( __( 'active %s ago', 'cc' ), bp_get_group_last_active() ) ?></span>
	<?php do_action( 'bp_before_group_header_meta' ) ?>

	
	<div id="item-meta">
		<?php bp_group_join_button() ?>
	</div>
	<div class="widget">
	</div>
	<div class="clear"></div>
	<div id="item-meta">
	 <h3 class="widgettitle"><?php _e('Description', 'cc'); ?></h3>
		<?php bp_group_description() ?>
	</div>
	<?php do_action( 'bp_group_header_meta' ) ?>

	<div style="height:105px" id="item-list">
	<?php if ( bp_group_is_visible() ) : ?>
		<h3 class="widgettitle"><?php _e('Group Admins', 'cc'); ?></h3>
		<?php bp_group_list_admins() ?>
		<?php do_action( 'bp_after_group_menu_admins' ) ?>
	</div>
	<div id="item-list">

		<?php if ( bp_group_has_moderators() ) : ?>
			<?php do_action( 'bp_before_group_menu_mods' ) ?>
			<h3 style="" class="widgettitle"><?php _e('Group Moderators', 'cc'); ?></h3>
			<?php bp_group_list_mods() ?>
			<?php do_action( 'bp_after_group_menu_mods' ) ?>
		<?php endif; ?>
	<?php endif; ?>
	</div><!-- #item-actions -->
<?php endwhile; endif; ?>
<?php do_action( 'bp_after_group_header' ) ?>
<?php do_action( 'template_notices' ) ?>