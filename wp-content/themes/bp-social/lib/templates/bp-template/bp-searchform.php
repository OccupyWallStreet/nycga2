<div class="widget widget_text">
<h2 class="widgettitle"><?php _e('Search', TEMPLATE_DOMAIN); ?></h2>
<div class="textwidget">

<form action="<?php echo bp_search_form_action() ?>" method="post" id="search-form">
<input type="text" id="search-terms" name="search-terms" onfocus="if (this.value == '<?php _e( 'Start Searching...', TEMPLATE_DOMAIN ) ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e( 'Start Searching...', TEMPLATE_DOMAIN ) ?>';}" />
<?php echo bp_search_form_type_select() ?>
&nbsp;<input type="submit" name="search-submit" id="search-submit" value="<?php _e( 'Search', TEMPLATE_DOMAIN) ?>" />
<?php wp_nonce_field( 'bp_search_form' ) ?>
</form>


</div>
</div>