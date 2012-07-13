<?php do_action( 'bp_before_link_header' ) ?>

<div id="item-actions">
	<?php if ( bp_link_is_visible() ) : ?>

		<?php do_action( 'bp_before_link_menu_owner' ) ?>
		<h3><?php _e( 'Link Owner', 'buddypress-links' ) ?></h3>
		<?php bp_link_user_avatar_thumb() ?>
		<div class="link-owner-text"><?php bp_link_userlink() ?></div>
		<?php do_action( 'bp_after_link_menu_owner' ) ?>

	<?php endif; ?>
</div>

<div class="item-avatar">
	<?php bp_link_avatar() ?>
	<?php bp_link_play_button() ?>
</div>

<?php do_action( 'bp_before_link_menu_voting' ) ?>
<?php bp_link_vote_panel() ?>
<?php bp_link_vote_panel_form() ?>
<?php do_action( 'bp_after_link_menu_voting' ) ?>

<h2><a href="<?php bp_link_url() ?>" title="<?php bp_link_name() ?>" target="_blank"><?php bp_link_name() ?></a></h2>
<span class="highlight"><?php bp_link_type() ?></span> <span class="activity"><?php printf( __( 'active %s ago', 'buddypress' ), bp_get_link_last_active() ) ?></span>

<?php do_action( 'bp_before_link_header_meta' ) ?>

<div id="item-meta">
	<?php if ( bp_get_link_has_description() ): ?>
	<span class="domain"><?php bp_link_url_domain() ?> --</span>
	<?php bp_link_description() ?>
	<?php endif; ?>

	<?php do_action( 'bp_link_header_meta' ) ?>
</div>

<?php do_action( 'bp_after_link_header' ) ?>

<?php do_action( 'template_notices' ) ?>