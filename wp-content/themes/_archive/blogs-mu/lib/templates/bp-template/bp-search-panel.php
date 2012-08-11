<form action="<?php echo bp_search_form_action() ?>" method="post" id="search-form">
<input type="text" id="search-terms" name="search-terms" value="" />
<?php echo bp_search_form_type_select() ?>
<input type="submit" name="search-submit" id="search-submit" value="<?php _e( 'Search', TEMPLATE_DOMAIN ) ?>" />
<?php wp_nonce_field( 'bp_search_form' ) ?>
</form><!-- #search-form -->
<?php do_action( 'bp_search_login_bar' ) ?>