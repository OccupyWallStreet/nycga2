<form action="<?php echo bp_search_form_action() ?>" method="post" id="search-form">
<input type="text" id="search-terms" name="search-terms" value="<?php echo isset( $_REQUEST['s'] ) ? esc_attr( $_REQUEST['s'] ) : ''; ?>" />
<?php echo bp_search_form_type_select() ?>
<input type="submit" name="search-submit" id="search-submit" value="<?php _e( 'Search', 'buddypress' ) ?>" />
<?php wp_nonce_field( 'bp_search_form' ) ?>
</form>