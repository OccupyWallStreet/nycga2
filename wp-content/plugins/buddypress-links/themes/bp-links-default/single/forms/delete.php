<?php do_action( 'bp_before_link_delete_form' ) ?>

<form action="<?php bp_link_admin_form_action() ?>" name="link-delete-form" id="link-delete-form" class="standard-form" method="post" enctype="multipart/form-data">

	<h2><?php _e( 'Delete Link', 'buddypress-links' ); ?></h2>

	<?php do_action( 'bp_before_link_delete_form_content' ); ?>

	<div id="message" class="info">
		<p><?php _e( 'WARNING: Deleting this link will completely remove ALL content associated with it. There is no way back, please be careful with this option.', 'buddypress-links' ); ?></p>
	</div>

	<input type="checkbox" name="delete-link-understand" id="delete-link-understand" value="1" onclick="if(this.checked) { document.getElementById('delete-link-button').disabled = ''; } else { document.getElementById('delete-link-button').disabled = 'disabled'; }" /> <?php _e( 'I understand the consequences of deleting this link.', 'buddypress-links' ); ?>

	<?php do_action( 'bp_before_link_delete_form_button' ); ?>
	<p><input type="submit" disabled="disabled" value="<?php _e( 'Delete Link', 'buddypress-links' ) ?> &raquo;" id="delete-link-button" name="delete-link-button" /></p>
	<?php do_action( 'bp_after_link_delete_form_button' ); ?>

	<input type="hidden" name="link-id" id="link-id" value="<?php bp_link_id() ?>" />

	<?php wp_nonce_field( 'bp_links_delete_link' ) ?>

	<?php /* This is important, don't forget it */ ?>
	<input type="hidden" name="link-id" id="link-id" value="<?php bp_link_id() ?>" />

	<?php do_action( 'bp_after_link_delete_form_content' ); ?>
	
</form>

<?php do_action( 'bp_after_link_delete_form' ) ?>