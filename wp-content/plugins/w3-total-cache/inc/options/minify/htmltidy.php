<?php if (!defined('W3TC')) die(); ?>
<input type="hidden" name="minify.htmltidy.options.clean" value="0" />
<input type="hidden" name="minify.htmltidy.options.hide-comments" value="0" />
<label><input class="html_enabled" type="checkbox" name="minify.htmltidy.options.clean" value="1"<?php checked($this->_config->get_boolean('minify.htmltidy.options.clean'), true); ?> /> Clean</label><br />
<label><input class="html_enabled" type="checkbox" name="minify.htmltidy.options.hide-comments" value="1"<?php checked($this->_config->get_boolean('minify.htmltidy.options.hide-comments'), true); ?> /> Hide comments</label><br />
