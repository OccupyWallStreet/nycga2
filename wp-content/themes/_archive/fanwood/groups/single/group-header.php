<?php

/**
 * BuddyPress - Single Group Header Content Part
 *
 * @package BuddyPress
 * @subpackage Theme
 */
 
?>

<?php do_action( 'template_notices' ); ?>

<?php do_action( 'bp_before_group_header' ); ?>

<div class="loop-meta">

	<div id="item-header-avatar">
		<a href="<?php bp_group_permalink(); ?>" title="<?php bp_group_name(); ?>"><?php bp_group_avatar(); ?></a>
	</div><!-- #item-header-avatar -->

	<div id="item-header-content">
		<h1 class="loop-title"><a href="<?php bp_group_permalink(); ?>" title="<?php bp_group_name(); ?>"><?php bp_group_name(); ?></a></h1>
		
		<span class="highlight"><?php bp_group_type(); ?></span> <span class="activity"><?php printf( __( 'active %s', 'buddypress' ), bp_get_group_last_active() ); ?></span>

		<?php do_action( 'bp_before_group_header_meta' ); ?>

		<div id="item-meta">
		
			<?php bp_group_description(); ?>

			<div id="item-buttons">
				<?php do_action( 'bp_group_header_actions' ); ?>
			</div><!-- #item-buttons -->

			<?php do_action( 'bp_group_header_meta' ); ?>

		</div><!-- #item-meta -->

	</div><!-- #item-header-content -->
	
</div><!-- .loop-meta -->

<div id="item-actions">

	<?php if ( bp_group_is_visible() ) : ?>
		
		<div class="group-staff">

			<h3 class="group-staff-title">
				<?php _e( 'Group Admins', 'buddypress' ); ?>
			</h3>

			<?php bp_group_list_admins(); ?>

			<?php do_action( 'bp_after_group_menu_admins' ); ?>
				
		</div><!-- .group-staff -->

		<?php if ( bp_group_has_moderators() ) : ?>
					
			<div class="group-staff">

				<?php do_action( 'bp_before_group_menu_mods' ); ?>

				<h3 class="group-staff-title">
					<?php _e( 'Group Mods' , 'buddypress' ) ?>
				</h3>

				<?php bp_group_list_mods(); ?>

				<?php do_action( 'bp_after_group_menu_mods' ); ?>
					
			</div><!-- .group-staff -->

		<?php endif; ?>

	<?php endif; ?>

</div><!-- #item-actions -->

	<?php
		do_action( 'bp_after_group_header' );
	?>