<?php

if (!defined('W3TC')) {
    die();
}

$csstidy_templates = array(
    'highest_compression' => 'Highest (no readability, smallest size)',
    'high_compression' => 'High (moderate readability, smaller size)',
    'default' => 'Standard (balance between readability and size)',
    'low_compression' => 'Low (higher readability)',
);

$optimise_shorthands_values = array(
    0 => 'Don\'t optimise',
    1 => 'Safe optimisations',
    2 => 'All optimisations'
);

$case_properties_values = array(
    0 => 'None',
    1 => 'Lowercase',
    2 => 'Uppercase'
);

$merge_selectors_values = array(
    0 => 'Do not change anything',
    1 => 'Only seperate selectors (split at ,)',
    2 => 'Merge selectors with the same properties (fast)'
);

$csstidy_template = $this->_config->get_string('minify.csstidy.options.template');
$optimise_shorthands = $this->_config->get_integer('minify.csstidy.options.optimise_shorthands');
$case_properties = $this->_config->get_integer('minify.csstidy.options.case_properties');
$merge_selectors = $this->_config->get_integer('minify.csstidy.options.merge_selectors');
?>
<tr>
    <th><label for="minify_csstidy_options_template">Compression:</label></th>
    <td>
        <select id="minify_csstidy_options_template" class="css_enabled" name="minify.csstidy.options.template">
            <?php foreach ($csstidy_templates as $csstidy_template_key => $csstidy_template_name): ?>
            <option value="<?php echo $csstidy_template_key; ?>"<?php selected($csstidy_template, $csstidy_template_key); ?>><?php echo $csstidy_template_name; ?></option>
            <?php endforeach; ?>
        </select>
    </td>
</tr>
<tr>
    <th><label for="minify_csstidy_options_optimise_shorthands">Optimise shorthands:</label></th>
    <td>
        <select id="minify_csstidy_options_optimise_shorthands" class="css_enabled" name="minify.csstidy.options.optimise_shorthands">
            <?php foreach ($optimise_shorthands_values as $optimise_shorthands_key => $optimise_shorthands_name): ?>
            <option value="<?php echo $optimise_shorthands_key; ?>"<?php selected($optimise_shorthands, $optimise_shorthands_key); ?>><?php echo $optimise_shorthands_name; ?></option>
            <?php endforeach; ?>
        </select>
    </td>
</tr>
<tr>
    <th><label for="minify_csstidy_options_case_properties">Case for properties:</label></th>
    <td>
        <select id="minify_csstidy_options_case_properties" class="css_enabled" name="minify.csstidy.options.case_properties">
            <?php foreach ($case_properties_values as $case_properties_key => $case_properties_name): ?>
            <option value="<?php echo $case_properties_key; ?>"<?php selected($case_properties, $case_properties_key); ?>><?php echo $case_properties_name; ?></option>
            <?php endforeach; ?>
        </select>
    </td>
</tr>
<tr>
    <th><label for="minify_csstidy_options_merge_selectors">Regroup selectors:</label></th>
    <td>
        <select id="minify_csstidy_options_merge_selectors" class="css_enabled" name="minify.csstidy.options.merge_selectors">
            <?php foreach ($merge_selectors_values as $merge_selectors_key => $merge_selectors_name): ?>
            <option value="<?php echo $merge_selectors_key; ?>"<?php selected($merge_selectors, $merge_selectors_key); ?>><?php echo $merge_selectors_name; ?></option>
            <?php endforeach; ?>
        </select>
    </td>
</tr>
