<?php do_action( 'bp_before_links_list_item' ) ?>
<li id="<?php bp_link_list_item_id() ?>" class="<?php bp_link_list_item_class() ?>">
	<?php do_action( 'bp_before_links_list_item_content' ) ?>
	
	<?php do_action( 'bp_before_links_list_item_left' ) ?>
	<div class="link-list-left">
		<?php do_action( 'bp_before_links_list_item_left_content' ) ?>
		<div class="item-avatar">
			<?php do_action( 'bp_before_links_list_item_avatar_content' ) ?>
			<?php bp_link_play_button() ?>
			<a href="<?php bp_link_permalink() ?>"><?php bp_link_list_item_avatar() ?></a>
			<?php do_action( 'bp_after_links_list_item_avatar_content' ) ?>
		</div>
		<?php do_action( 'bp_after_links_list_item_left_content' ) ?>
	</div>
	<?php do_action( 'bp_after_links_list_item_left' ) ?>
	
	<?php do_action( 'bp_before_links_list_item_right' ) ?>
	<div class="link-list-right">
		<?php do_action( 'bp_before_links_list_item_right_content' ) ?>
		<?php do_action( 'bp_before_links_list_item_title' ) ?>
		<div class="item-title">
			<?php do_action( 'bp_before_links_list_item_title_content' ) ?>
			<?php bp_link_list_item_category_name() ?>: <a href="<?php bp_link_list_item_url() ?>"<?php bp_link_list_item_url_target() ?><?php bp_link_list_item_url_rel() ?>><?php bp_link_list_item_name() ?></a>
			<a class="link-external" href="<?php bp_link_list_item_external_url() ?>" title="<?php bp_link_list_item_external() ?>"<?php bp_link_list_item_external_url_target() ?><?php bp_link_list_item_external_url_rel() ?>></a>
			<?php do_action( 'bp_after_links_list_item_title_content' ) ?>
		</div>
		<?php do_action( 'bp_after_links_list_item_title' ) ?>
		<?php if ( bp_get_link_has_description() ): ?>
		<?php do_action( 'bp_before_links_list_item_description' ) ?>
		<div class="item-desc">
			<?php do_action( 'bp_before_links_list_item_description_content' ) ?>
			<p>
				<?php do_action( 'bp_before_links_list_item_description_domain' ) ?>
				<span class="domain"><?php bp_link_list_item_url_domain() ?> --</span>
				<?php do_action( 'bp_before_links_list_item_description_text' ) ?>
				<?php bp_link_list_item_description() ?> <a href="<?php bp_link_list_item_continue_url() ?>"<?php bp_link_list_item_continue_url_target() ?><?php bp_link_list_item_continue_url_rel() ?>><?php bp_link_list_item_continue() ?></a>
				<?php do_action( 'bp_after_links_list_item_description_text' ) ?>
			</p>
			<?php do_action( 'bp_after_links_list_item_description_content' ) ?>
		</div>
		<?php endif; ?>
		<?php do_action( 'bp_after_links_list_item_description' ) ?>
		<?php do_action( 'bp_after_links_list_item_right_content' ) ?>
	</div>
	<?php do_action( 'bp_after_links_list_item_right' ) ?>
	
	<?php do_action( 'bp_before_links_list_item_footer' ) ?>
	<div class="link-list-footer">
		<?php do_action( 'bp_before_links_list_item_footer_content' ) ?>
			<div class="link-list-footer-left">
				<?php do_action( 'bp_before_links_list_item_vote_panel' ) ?>
				<?php bp_link_vote_panel( false ) ?>
				<?php do_action( 'bp_after_links_list_item_vote_panel' ) ?>
			</div>
			<div class="link-list-footer-right">
				<?php do_action( 'bp_before_links_list_item_xtrabar' ) ?>
				<div class="xtrabar">
					<?php do_action( 'bp_before_links_list_item_xtrabar_content' ) ?>
					<?php if ( bp_links_is_activity_enabled() ): ?>
					<?php do_action( 'bp_before_links_list_item_xtrabar_comments' ) ?>
					<a href="<?php bp_link_permalink() ?>" class="comments"><?php bp_link_activity_post_count() ?>&nbsp;<?php bp_link_list_item_xtrabar_comments() ?></a>
					<?php do_action( 'bp_after_links_list_item_xtrabar_comments' ) ?>
					<?php endif; ?>
					<?php do_action( 'bp_before_links_list_item_xtrabar_userlink' ) ?>
					<div class="owner"><?php bp_link_user_avatar_mini() ?>&nbsp;<?php bp_link_userlink() ?> <?php bp_link_list_item_xtrabar_userlink_created() ?></div>
					<?php do_action( 'bp_after_links_list_item_xtrabar_userlink' ) ?>
					<?php if ( bp_get_link_share_has_profile_link() || bp_is_group() ): ?>
						<div class="link-shared">&nbsp;</div>
					<?php endif; ?>
					<?php do_action( 'bp_after_links_list_item_xtrabar_content' ) ?>
				</div>
				<?php do_action( 'bp_after_links_list_item_xtrabar' ) ?>
			</div>
		<?php do_action( 'bp_after_links_list_item_footer_content' ) ?>	
	</div>
	<?php do_action( 'bp_after_links_list_item_footer' ) ?>	

	<div class="action">
	<?php do_action( 'bp_before_links_list_item_action_content' ) ?>
	<?php bp_links_dtheme_share_button() ?>
		<div class="meta"><?php bp_link_type() ?> / <?php bp_link_vote_count_text() ?></div>
	<?php do_action( 'bp_after_links_list_item_action_content' ) ?>
	</div>

	<?php do_action( 'bp_after_links_list_item_content' ) ?>
	<div class="clear"></div>
</li>
<?php do_action( 'bp_after_links_list_item' ) ?>