<?php if (!defined('W3TC')) die(); ?>
<input type="hidden" name="minify.ccjs.options.formatting" value="" />
<label><input class="js_enabled" type="checkbox" name="minify.ccjs.options.formatting" value="pretty_print"<?php checked($this->_config->get_string('minify.ccjs.options.formatting'), 'pretty_print'); ?> /> Pretty print</label><br />
