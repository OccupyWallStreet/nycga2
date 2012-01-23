<?php if (!defined('W3TC')) die(); ?>
<tr>
    <th><label for="minify_htmltidy_options_wrap">Wrap after:</label></th>
    <td><input id="minify_htmltidy_options_wrap" class="html_enabled" type="text" name="minify.htmltidy.options.wrap" value="<?php echo htmlspecialchars($this->_config->get_integer('minify.htmltidy.options.wrap')); ?>" size="8" style="text-align: right;" /> symbols (set to 0 to disable)</td>
</tr>
