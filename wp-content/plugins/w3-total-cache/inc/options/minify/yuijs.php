<?php if (!defined('W3TC')) die(); ?>
<input type="hidden" name="minify.yuijs.options.nomunge" value="0" />
<input type="hidden" name="minify.yuijs.options.preserve-semi" value="0" />
<input type="hidden" name="minify.yuijs.options.disable-optimizations" value="0" />
<label><input class="js_enabled" type="checkbox" name="minify.yuijs.options.nomunge" value="1"<?php checked($this->_config->get_boolean('minify.yuijs.options.nomunge'), true); ?> /> Minify only, do not obfuscate local symbols</label><br />
<label><input class="js_enabled" type="checkbox" name="minify.yuijs.options.preserve-semi" value="1"<?php checked($this->_config->get_boolean('minify.yuijs.options.preserve-semi'), true); ?> /> Preserve unnecessary semicolons</label><br />
<label><input class="js_enabled" type="checkbox" name="minify.yuijs.options.disable-optimizations" value="1"<?php checked($this->_config->get_boolean('minify.yuijs.options.disable-optimizations'), true); ?> /> Disable all the built-in micro optimizations</label><br />
