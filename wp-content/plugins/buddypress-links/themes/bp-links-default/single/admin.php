<div class="item-list-tabs no-ajax" id="subnav">
	<ul>
		<?php bp_link_admin_tabs(); ?>
	</ul>
</div><!-- .item-list-tabs -->

<?php
	do_action( 'bp_before_link_admin_content' );

	switch ( bp_links_admin_current_action_variable() ) {
		case 'edit-details':
			bp_links_locate_template( array( 'single/forms/details.php' ), true );
			break;
		case 'link-avatar':
			bp_links_locate_template( array( 'single/forms/avatar.php' ), true );
			break;
		case 'delete-link':
			bp_links_locate_template( array( 'single/forms/delete.php' ), true );
			break;
		default:
			die('Invalid admin action!');
	}

	do_action( 'bp_after_link_admin_content' );
?>