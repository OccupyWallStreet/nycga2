<div class="item-list-tabs no-ajax" id="subnav">
	<ul>
		<?php bp_links_group_links_tabs() ?>
	</ul>
</div>

<?php
	do_action( 'bp_before_group_body' );
	do_action( 'bp_before_group_links_content' );
	do_action( 'bp_before_group_link_creation_content' );
	bp_links_locate_template( array( 'single/forms/details.php' ), true );
	do_action( 'bp_after_group_link_creation_content' );
	do_action( 'bp_after_group_links_content' );
	do_action( 'bp_after_group_body' );
?>
