<?php if (!defined('W3TC')) die(); ?>
<input type="hidden" name="minify.js.strip.comments" value="0" />
<input type="hidden" name="minify.js.strip.crlf" value="0" />
<label><input class="js_enabled" type="checkbox" name="minify.js.strip.comments" value="1"<?php checked($this->_config->get_boolean('minify.js.strip.comments'), true); ?> /> Preserved comment removal (not applied when combine only is active)</label><br />
<label><input class="js_enabled" type="checkbox" name="minify.js.strip.crlf" value="1"<?php checked($this->_config->get_boolean('minify.js.strip.crlf'), true); ?> /> Line break removal (not safe, not applied when combine only is active)</label><br />
