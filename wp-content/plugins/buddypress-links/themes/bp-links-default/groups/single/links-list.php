<div class="item-list-tabs no-ajax" id="subnav">
	<ul>
		<?php bp_links_group_links_tabs() ?>
		<?php bp_links_dtheme_link_order_options_list() ?>
		<?php bp_links_dtheme_link_category_filter_options_list() ?>
	</ul>
</div>

<?php do_action( 'bp_before_group_body' ) ?>
<?php do_action( 'bp_before_group_links_content' ) ?>

<div id="links-mylinks" class="links mylinks">
	<?php bp_links_locate_template( array( 'links-loop.php' ), true ) ?>
</div>

<?php do_action( 'bp_after_group_links_content' ) ?>
<?php do_action( 'bp_after_group_body' ) ?>
