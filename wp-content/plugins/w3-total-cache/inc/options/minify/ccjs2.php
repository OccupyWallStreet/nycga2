<?php

if (!defined('W3TC')) {
    die();
}

$compilation_levels = array(
    'WHITESPACE_ONLY' => 'Whitespace only',
    'SIMPLE_OPTIMIZATIONS' => 'Simple optimizations',
    'ADVANCED_OPTIMIZATIONS' => 'Advanced optimizations'
);

$compilation_level = $this->_config->get_string('minify.ccjs.options.compilation_level');
?>
<tr>
    <th><label for="minify_ccjs_path_java">Path to JAVA executable:</label></th>
    <td><input id="minify_ccjs_path_java" class="js_enabled" type="text" name="minify.ccjs.path.java" value="<?php echo htmlspecialchars($this->_config->get_string('minify.ccjs.path.java')); ?>" size="60" /></td>
</tr>
<tr>
    <th><label for="minify_ccjs_path_jar">Path to JAR file:</label></th>
    <td><input id="minify_ccjs_path_jar" class="js_enabled" type="text" name="minify.ccjs.path.jar" value="<?php echo htmlspecialchars($this->_config->get_string('minify.ccjs.path.jar')); ?>" size="60" /></td>
</tr>
<tr>
    <th>&nbsp;</th>
    <td>
        <input class="minifier_test button {type: 'ccjs', nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="Test Closure Compiler" />
        <span class="minifier_test_status w3tc-status w3tc-process"></span>
    </td>
</tr>
<tr>
    <th><label for="minify_ccjs_options_compilation_level">Compilation level:</label></th>
    <td>
        <select id="minify_ccjs_options_compilation_level" class="js_enabled" name="minify.ccjs.options.compilation_level">
            <?php foreach ($compilation_levels as $compilation_level_key => $compilation_level_name): ?>
            <option value="<?php echo $compilation_level_key; ?>"<?php selected($compilation_level, $compilation_level_key); ?>><?php echo $compilation_level_name; ?></option>
            <?php endforeach; ?>
        </select>
    </td>
</tr>
