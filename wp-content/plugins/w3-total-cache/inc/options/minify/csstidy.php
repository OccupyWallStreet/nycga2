<?php

if (!defined('W3TC')) {
    die();
}

$css_levels = array(
    'CSS2.1',
    'CSS2.0',
    'CSS1.0'
);

$css_level = $this->_config->get_string('minify.csstidy.options.css_level');
?>
<input type="hidden" name="minify.csstidy.options.remove_bslash" value="0" />
<input type="hidden" name="minify.csstidy.options.compress_colors" value="0" />
<input type="hidden" name="minify.csstidy.options.compress_font-weight" value="0" />
<input type="hidden" name="minify.csstidy.options.lowercase_s" value="0" />
<input type="hidden" name="minify.csstidy.options.remove_last_;" value="0" />
<input type="hidden" name="minify.csstidy.options.sort_properties" value="0" />
<input type="hidden" name="minify.csstidy.options.sort_selectors" value="0" />
<input type="hidden" name="minify.csstidy.options.discard_invalid_properties" value="0" />
<input type="hidden" name="minify.csstidy.options.preserve_css" value="0" />
<input type="hidden" name="minify.csstidy.options.timestamp" value="0" />
<label><input class="css_enabled" type="checkbox" name="minify.csstidy.options.remove_bslash" value="1"<?php checked($this->_config->get_boolean('minify.csstidy.options.remove_bslash'), true); ?> /> Remove unnecessary backslashes</label><br />
<label><input class="css_enabled" type="checkbox" name="minify.csstidy.options.compress_colors" value="1"<?php checked($this->_config->get_boolean('minify.csstidy.options.compress_colors'), true); ?> /> Compress colors</label><br />
<label><input class="css_enabled" type="checkbox" name="minify.csstidy.options.compress_font-weight" value="1"<?php checked($this->_config->get_boolean('minify.csstidy.options.compress_font-weight'), true); ?> /> Compress font-weight</label><br />
<label><input class="css_enabled" type="checkbox" name="minify.csstidy.options.lowercase_s" value="1"<?php checked($this->_config->get_boolean('minify.csstidy.options.lowercase_s'), true); ?> /> Lowercase selectors</label><br />
<label><input class="css_enabled" type="checkbox" name="minify.csstidy.options.remove_last_;" value="1"<?php checked($this->_config->get_boolean('minify.csstidy.options.remove_last_;'), true); ?> /> Remove last ;</label><br />
<label><input class="css_enabled" type="checkbox" name="minify.csstidy.options.sort_properties" value="1"<?php checked($this->_config->get_boolean('minify.csstidy.options.sort_properties'), true); ?> /> Sort Properties</label><br />
<label><input class="css_enabled" type="checkbox" name="minify.csstidy.options.sort_selectors" value="1"<?php checked($this->_config->get_boolean('minify.csstidy.options.sort_selectors'), true); ?> /> Sort Selectors (caution)</label><br />
<label><input class="css_enabled" type="checkbox" name="minify.csstidy.options.discard_invalid_properties" value="1"<?php checked($this->_config->get_boolean('minify.csstidy.options.discard_invalid_properties'), true); ?> /> Discard invalid properties</label>
<select class="css_enabled" name="minify.csstidy.options.css_level">
    <?php foreach($css_levels as $_css_level): ?>
        <option value="<?php echo $_css_level; ?>"<?php selected($css_level, $_css_level); ?>><?php echo $_css_level; ?></option>
    <?php endforeach; ?>
</select><br />
<label><input class="css_enabled" type="checkbox" name="minify.csstidy.options.preserve_css" value="1"<?php checked($this->_config->get_boolean('minify.csstidy.options.preserve_css'), true); ?> /> Preserve CSS</label><br />
<label><input class="css_enabled" type="checkbox" name="minify.csstidy.options.timestamp" value="1"<?php checked($this->_config->get_boolean('minify.csstidy.options.timestamp'), true); ?> /> Add timestamp</label><br />
