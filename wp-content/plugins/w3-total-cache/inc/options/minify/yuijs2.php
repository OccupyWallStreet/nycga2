<?php if (!defined('W3TC')) die(); ?>
<tr>
    <th><label for="minify_yuijs_path_java">Path to JAVA executable:</label></th>
    <td><input id="minify_yuijs_path_java" class="js_enabled" type="text" name="minify.yuijs.path.java" value="<?php echo htmlspecialchars($this->_config->get_string('minify.yuijs.path.java')); ?>" size="100" /></td>
</tr>
<tr>
    <th><label for="minify_yuijs_path_jar">Path to JAR file:</label></th>
    <td><input id="minify_yuijs_path_jar" class="js_enabled" type="text" name="minify.yuijs.path.jar" value="<?php echo htmlspecialchars($this->_config->get_string('minify.yuijs.path.jar')); ?>" size="100" /></td>
</tr>
<tr>
    <th>&nbsp;</th>
    <td>
        <input class="minifier_test button {type: 'yuijs', nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="Test YUI Compressor" />
        <span class="minifier_test_status w3tc-status w3tc-process"></span>
    </td>
</tr>
<tr>
    <th><label for="minify_yuijs_options_line-break">Line break after:</label></th>
    <td><input id="minify_yuijs_options_line-break" class="js_enabled" type="text" name="minify.yuijs.options.line-break" value="<?php echo htmlspecialchars($this->_config->get_integer('minify.yuijs.options.line-break')); ?>" size="8" style="text-align: right;" /> symbols (set to 0 to disable)</td>
</tr>
