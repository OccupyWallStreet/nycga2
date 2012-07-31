<form id="search-form" method="get" action="<?php echo site_url(); ?>">
<input name="s" id="search-terms" type="text" value="<?php _e('Search here',TEMPLATE_DOMAIN); ?>" onfocus="if (this.value == '<?php _e( 'Search Here', TEMPLATE_DOMAIN ) ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e( 'Search Here', TEMPLATE_DOMAIN ) ?>';}" />
<input type="submit" name="search-submit" id="search-submit" value="<?php echo esc_attr(__('Search', TEMPLATE_DOMAIN)); ?>" />
</form>


