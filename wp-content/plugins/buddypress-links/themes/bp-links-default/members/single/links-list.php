<?php do_action( 'bp_before_member_body' ) ?>
<?php do_action( 'bp_before_member_links_content' ) ?>

<div id="links-mylinks" class="links mylinks">
	<?php bp_links_locate_template( array( 'links-loop.php' ), true ) ?>
</div>

<?php do_action( 'bp_after_member_links_content' ) ?>
<?php do_action( 'bp_after_member_body' ) ?>
