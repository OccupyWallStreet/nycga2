<form id="searchform" method="get" action="<?php bloginfo('home'); ?>/">
<input type="text" value="<?php _e( 'Enter keywords...', 'Detox') ?>" name="s" id="s" onfocus="if (this.value == '<?php _e( 'Enter keywords...', 'Detox') ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e( 'Search anything', 'Detox') ?>';}" />
</form>