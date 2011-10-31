<?php if (!defined('W3TC')) die(); ?>
<h3>Minify Help Wizard</h3>

<p>
    To get started with minify, we've identified the following external CSS and JS objects in the
    <select id="recom_theme">
    <?php foreach ($themes as $_theme_key => $_theme_name): ?>
        <option value="<?php echo htmlspecialchars($_theme_key); ?>"<?php selected($_theme_key, $theme_key); ?>><?php echo htmlspecialchars($_theme_name); ?><?php if ($_theme_key == $theme_key): ?> (active)<?php endif; ?></option>
    <?php endforeach; ?>
    </select>
    theme. Select "add" the files you wish to minify, then click "apply &amp; close" to save the settings.
</p>

<div id="recom_container">
    <h4 style="margin-top: 0;">JavaScript:</h4>

    <?php if (count($js_groups)) :?>
    <ul id="recom_js_files" class="minify-files">
        <?php $index = 0; foreach ($js_groups as $js_group => $js_files): ?>
        	<?php foreach ($js_files as $js_file): $index++; ?>
            <li>
            	<table>
            		<tr>
            			<th class="minify-files-add">Add:</th>
            			<th>&nbsp;</th>
            			<th>File URI:</th>
            			<th>Template:</th>
            			<th colspan="2">Embed Location:</th>
            		</tr>
            		<tr>
            			<td class="minify-files-add">
                			<input type="checkbox" name="recom_js_useit" value="1"<?php checked(isset($checked_js[$js_group][$js_file]), true); ?> />
            			</td>
            			<td><?php echo $index; ?>.</td>
            			<td>
    	                    <input type="text" name="recom_js_file" value="<?php echo htmlspecialchars($js_file); ?>" size="70" />
            			</td>
            			<td>
                            <select name="recom_js_template">
                            <?php foreach ($templates as $template_key => $template_name): ?>
                                <option value="<?php echo htmlspecialchars($template_key); ?>"<?php selected($template_key, $js_group); ?>><?php echo htmlspecialchars($template_name); ?></option>
                            <?php endforeach; ?>
                            </select>
            			</td>
            			<td>
            				<?php $selected = (isset($locations_js[$js_group][$js_file]) ? $locations_js[$js_group][$js_file] : ''); ?>
                            <select name="recom_js_location">
                                <optgroup label="Blocking:">
                                    <option value="include"<?php selected($selected, 'include'); ?>>Embed in &lt;head&gt;</option>
                                    <option value="include-body"<?php selected($selected, 'include-body'); ?>>Embed after &lt;body&gt;</option>
                                    <option value="include-footer"<?php selected($selected, 'include-footer'); ?>>Embed before &lt;/body&gt;</option>
                                </optgroup>
                                <optgroup label="Non-Blocking:">
                                    <option value="include-nb"<?php selected($selected, 'include-nb'); ?>>Embed in &lt;head&gt;</option>
                                    <option value="include-body-nb"<?php selected($selected, 'include-body-nb'); ?>>Embed after &lt;body&gt;</option>
                                    <option value="include-footer-nb"<?php selected($selected, 'include-footer-nb'); ?>>Embed before &lt;/body&gt;</option>
                                </optgroup>
                            </select>
            			</td>
            			<td>
			                <input class="js_file_verify button" type="button" value="Verify URI" />
            			</td>
					</tr>
				</table>
            </li>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </ul>
    <p>
    	<a href="#" id="recom_js_check">Check / Uncheck All</a>
    </p>
    <?php else:?>
    <p>No files found.</p>
    <?php endif;?>

    <h4>Cascading Style Sheets:</h4>

    <?php if (count($css_groups)) :?>
    <ul id="recom_css_files" class="minify-files">
        <?php $index = 0; foreach ($css_groups as $css_group => $css_files): ?>
    		<?php foreach ($css_files as $css_file): $index++; ?>
            <li>
            	<table>
            		<tr>
            			<th class="minify-files-add">Add:</th>
            			<th>&nbsp;</th>
            			<th>File URI:</th>
            			<th colspan="2">Template:</th>
            		</tr>
            		<tr>
            			<td class="minify-files-add">
                        	<input type="checkbox" name="recom_css_useit" value="1"<?php checked(isset($checked_css[$css_group][$css_file]), true); ?> />
                        </td>
            			<td><?php echo $index; ?>.</td>
                        <td>
                            <input type="text" name="recom_css_file" value="<?php echo htmlspecialchars($css_file); ?>" size="70" />
						</td>
						<td>
                            <select name="recom_css_template">
                            <?php foreach ($templates as $template_key => $template_name): ?>
                            <option value="<?php echo htmlspecialchars($template_key); ?>"<?php selected($template_key, $css_group); ?>><?php echo htmlspecialchars($template_name); ?></option>
                            <?php endforeach; ?>
                            </select>
						</td>
						<td>
			                <input class="css_file_verify button" type="button" value="Verify URI" />
						</td>
					</tr>
				</table>
            </li>
    	    <?php endforeach; ?>
        <?php endforeach; ?>
    </ul>
    <p>
    	<a href="#" id="recom_css_check">Check / Uncheck All</a>
    </p>
    <?php else:?>
    <p>No files found.</p>
    <?php endif;?>
</div>

<div id="recom_container_bottom">
    <p>
        <input class="recom_apply button-primary" type="button" value="Apply &amp; close" />
    </p>

    <fieldset>
        <legend>Notes</legend>

        <ul>
            <li>Typically minification of advertiser code, analytics/statistics or any other types of tracking code is not recommended.</li>
            <li>Scripts that were not already detected above may require <a href="admin.php?page=w3tc_support&amp;request_type=plugin_config">professional consultation</a> to implement.</li>
        </ul>
    </fieldset>
</div>