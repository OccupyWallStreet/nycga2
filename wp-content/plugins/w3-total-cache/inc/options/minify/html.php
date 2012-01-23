<?php if (!defined('W3TC')) die(); ?>
<input type="hidden" name="minify.html.strip.crlf" value="0" />
<label><input class="html_enabled" type="checkbox" name="minify.html.strip.crlf" value="1"<?php checked($this->_config->get_boolean('minify.html.strip.crlf'), true); ?> /> Line break removal</label><br />
