<?php if (!defined('W3TC')) die(); ?>
<input type="hidden" name="minify.css.strip.comments" value="0" />
<input type="hidden" name="minify.css.strip.crlf" value="0" />
<label><input class="css_enabled" type="checkbox" name="minify.css.strip.comments" value="1"<?php checked($this->_config->get_boolean('minify.css.strip.comments'), true); ?> /> Preserved comment removal (not applied when combine only is active)</label><br />
<label><input class="css_enabled" type="checkbox" name="minify.css.strip.crlf" value="1"<?php checked($this->_config->get_boolean('minify.css.strip.crlf'), true); ?> /> Line break removal (not applied when combine only is active)</label><br />
