<?php if (!defined('W3TC')) die(); ?>
<?php include W3TC_INC_DIR . '/options/common/header.php'; ?>

<script type="text/javascript">/*<![CDATA[*/
var minify_templates = {};
<?php foreach ($templates as $theme_key => $theme_templates): ?>
minify_templates['<?php echo addslashes($theme_key); ?>'] = {};
<?php foreach ($theme_templates as $theme_template_key => $theme_template_name): ?>
minify_templates['<?php echo addslashes($theme_key); ?>']['<?php echo addslashes($theme_template_key); ?>'] = '<?php echo addslashes($theme_template_name); ?>';
<?php endforeach; ?>
<?php endforeach; ?>
/*]]>*/</script>

<form action="admin.php?page=<?php echo $this->_page; ?>" method="post">
    <p>
        Minify via
        <strong><?php echo w3_get_engine_name($this->_config->get_string('minify.engine')); ?></strong>
        is currently <span class="w3tc-<?php if ($minify_enabled): ?>enabled">enabled<?php else: ?>disabled">disabled<?php endif; ?></span>.
    </p>
    <p>
		To rebuild the minify cache use the
        <?php echo $this->nonce_field('w3tc'); ?>
        <input type="submit" name="w3tc_flush_minify" value="empty cache"<?php if (! $minify_enabled): ?> disabled="disabled"<?php endif; ?> class="button" />
		operation.
        <?php if (!$auto): ?>
		Get minify hints using the
        <input type="button" class="button button-minify-recommendations {nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" value="help" />
		wizard.
        <?php endif; ?>
    </p>
</form>

<form id="minify_form" action="admin.php?page=<?php echo $this->_page; ?>" method="post">
    <div class="metabox-holder">
		<?php echo $this->postbox_header('General'); ?>
        <table class="form-table">
        	<tr>
        		<th colspan="2">
        			<input type="hidden" name="minify.rewrite" value="0" />
        			<label><input type="checkbox" name="minify.rewrite" value="1"<?php checked($this->_config->get_boolean('minify.rewrite'), true); ?><?php if (!w3_can_check_rules()): ?> disabled="disabled"<?php endif; ?> /> Rewrite <acronym title="Uniform Resource Locator">URL</acronym> structure</label><br />
    				<span class="description">If disabled, <acronym title="Cascading Style Sheet">CSS</acronym> and <acronym title="JavaScript">JS</acronym> embeddings will use GET variables instead of "fancy" links.</span>
        		</th>
        	</tr>
            <tr>
                <th colspan="2">
                    <input type="hidden" name="minify.reject.logged" value="0" />
                    <label><input type="checkbox" name="minify.reject.logged" value="1"<?php checked($this->_config->get_boolean('minify.reject.logged'), true); ?> /> Disable minify for logged in users</label><br />
                    <span class="description">Authenticated users will not recieve minified pages if this option is enabled.</span>
                </th>
            </tr>
        	<tr>
        		<th>
        			<label for="minify_error_notification">Minify error notification:</label>
        		</th>
        		<td>
        			<select id="minify_error_notification" name="minify.error.notification">
        				<?php $value = $this->_config->get_string('minify.error.notification'); ?>
        				<option value=""<?php selected($value, ''); ?>>Disabled</option>
        				<option value="admin"<?php selected($value, 'admin'); ?>>Admin Notification</option>
        				<option value="email"<?php selected($value, 'email'); ?>>Email Notification</option>
        				<option value="admin,email"<?php selected($value, 'admin,email'); ?>>Both Admin &amp; Email Notification</option>
        			</select>
        			<br /><span class="description">Notify when minify cache creation errors occur.</span>
        		</td>
        	</tr>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
        	<input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="Save all settings" />
        </p>
    	<?php echo $this->postbox_footer(); ?>

		<?php echo $this->postbox_header('<acronym title="Hypertext Markup Language">HTML</acronym> &amp; <acronym title="eXtensible Markup Language">XML</acronym>'); ?>
        <table class="form-table">
        	<tr>
        		<th><acronym title="Hypertext Markup Language">HTML</acronym> minify settings:</th>
        		<td>
        			<input type="hidden" name="minify.html.enable" value="0" />
                    <input type="hidden" name="minify.html.inline.css" value="0" />
                    <input type="hidden" name="minify.html.inline.js" value="0" />
        			<input type="hidden" name="minify.html.reject.feed" value="0" />
        			<label><input id="html_enabled" type="checkbox" name="minify.html.enable" value="1"<?php checked($this->_config->get_boolean('minify.html.enable'), true); ?> /> Enable</label><br />
                    <label><input class="html_enabled" type="checkbox" name="minify.html.inline.css" value="1"<?php checked($this->_config->get_boolean('minify.html.inline.css'), true); ?> /> Inline <acronym title="Cascading Style Sheet">CSS</acronym> minification</label><br />
                    <label><input class="html_enabled" type="checkbox" name="minify.html.inline.js" value="1"<?php checked($this->_config->get_boolean('minify.html.inline.js'), true); ?> /> Inline <acronym title="JavaScript">JS</acronym> minification</label><br />
        			<label><input class="html_enabled" type="checkbox" name="minify.html.reject.feed" value="1"<?php checked($this->_config->get_boolean('minify.html.reject.feed'), true); ?> /> Don't minify feeds</label><br />
                    <?php
                        $html_engine_file = '';

                        switch ($html_engine) {
                            case 'html':
                            case 'htmltidy':
                                $html_engine_file = W3TC_INC_DIR . '/options/minify/' . $html_engine . '.php';
                                break;
                        }

                        if (file_exists($html_engine_file)) {
                            include $html_engine_file;
                        }
                    ?>
        		</td>
        	</tr>
            <tr>
                <th><label for="minify_html_comments_ignore">Ignored comment stems:</label></th>
                <td>
                    <textarea id="minify_html_comments_ignore" name="minify.html.comments.ignore" cols="40" rows="5"><?php echo htmlspecialchars(implode("\r\n", $this->_config->get_array('minify.html.comments.ignore'))); ?></textarea><br />
                    <span class="description">Do not remove comments that contain these terms.</span>
                </td>
            </tr>
            <?php
                $html_engine_file2 = '';

                switch ($html_engine_file2) {
                    case 'html':
                    case 'htmltidy':
                        $html_engine_file = W3TC_INC_DIR . '/options/minify/' . $html_engine . '2.php';
                        break;
                }

                if (file_exists($html_engine_file2)) {
                    include $html_engine_file2;
                }
            ?>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
        	<input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="Save all settings" />
        </p>
    	<?php echo $this->postbox_footer(); ?>

		<?php echo $this->postbox_header('<acronym title="JavaScript">JS</acronym>'); ?>
        <table class="form-table">
            <tr>
                <th><acronym title="JavaScript">JS</acronym> minify settings:</th>
                <td>
                    <input type="hidden" name="minify.js.enable" value="0" />
                    <input type="hidden" name="minify.js.combine.header" value="0" />
                    <input type="hidden" name="minify.js.combine.body" value="0" />
                    <input type="hidden" name="minify.js.combine.footer" value="0" />
                    <label><input id="js_enabled" type="checkbox" name="minify.js.enable" value="1"<?php checked($this->_config->get_boolean('minify.js.enable'), true); ?> /> Enable</label><br />
                    <label><input class="js_enabled" type="checkbox" name="minify.js.combine.header" value="1"<?php checked($this->_config->get_boolean('minify.js.combine.header'), true); ?> /> Combine only after &lt;head&gt;</label><br />
                    <label><input class="js_enabled" type="checkbox" name="minify.js.combine.body" value="1"<?php checked($this->_config->get_boolean('minify.js.combine.body'), true); ?> /> Combine only after &lt;body&gt;</label><br />
                    <label><input class="js_enabled" type="checkbox" name="minify.js.combine.footer" value="1"<?php checked($this->_config->get_boolean('minify.js.combine.footer'), true); ?> /> Combine only before &lt;/body&gt;</label><br />
                    <?php
                        $js_engine_file = '';

                        switch ($js_engine) {
                            case 'js':
                            case 'yuijs':
                            case 'ccjs':
                                $js_engine_file = W3TC_INC_DIR . '/options/minify/' . $js_engine . '.php';
                                break;
                        }

                        if (file_exists($js_engine_file)) {
                            include $js_engine_file;
                        }
                    ?>
                </td>
            </tr>
            <?php
                $js_engine_file2 = '';

                switch ($js_engine) {
                    case 'js':
                    case 'yuijs':
                    case 'ccjs':
                        $js_engine_file2 = W3TC_INC_DIR . '/options/minify/' . $js_engine . '2.php';
                        break;
                }

                if (file_exists($js_engine_file2)) {
                    include $js_engine_file2;
                }
            ?>
            <?php if (!$auto): ?>
        	<tr>
        		<th><acronym title="JavaScript">JS</acronym> file management:</th>
        		<td>
        			<p>
        				<label>
        					Theme:
        					<select id="js_themes" class="js_enabled" name="js_theme">
        						<?php foreach ($themes as $theme_key => $theme_name): ?>
        						<option value="<?php echo htmlspecialchars($theme_key); ?>"<?php selected($theme_key, $js_theme); ?>><?php echo htmlspecialchars($theme_name); ?><?php if ($theme_key == $js_theme): ?> (active)<?php endif; ?></option>
        						<?php endforeach; ?>
        					</select>
        				</label>
            			<br /><span class="description">Files are minified by template. First select the theme to manage, then add scripts used in all templates to the "All Templates" group. Use the menu above to manage scripts unique to a specific template. If necessary drag &amp; drop to resolve dependency issues (due to incorrect order).</span>
        			</p>
        			<ul id="js_files" class="minify-files">
                    <?php foreach ($js_groups as $js_theme => $js_templates): if (isset($templates[$js_theme])): ?>
                	    <?php $index = 0; foreach ($js_templates as $js_template => $js_locations): ?>
                	        <?php foreach ((array) $js_locations as $js_location => $js_config): ?>
            		            <?php if (! empty($js_config['files'])): foreach ((array) $js_config['files'] as $js_file): $index++; ?>
            					<li>
            						<table>
            							<tr>
            								<th>&nbsp;</th>
            								<th>File URI:</th>
            								<th>Template:</th>
            								<th colspan="3">Embed Location:</th>
            							</tr>
            							<tr>
            								<td><?php echo $index; ?>.</td>
            								<td>
                    							<input class="js_enabled" type="text" name="js_files[<?php echo htmlspecialchars($js_theme); ?>][<?php echo htmlspecialchars($js_template); ?>][<?php echo htmlspecialchars($js_location); ?>][]" value="<?php echo htmlspecialchars($js_file); ?>" size="70" />
                    						</td>
                    						<td>
                                    			<select class="js_file_template js_enabled">
            	                        			<?php foreach ($templates[$js_theme] as $theme_template_key => $theme_template_name): ?>
                                    				<option value="<?php echo htmlspecialchars($theme_template_key); ?>"<?php selected($theme_template_key, $js_template); ?>><?php echo htmlspecialchars($theme_template_name); ?></option>
                	                    			<?php endforeach; ?>
                                    			</select>
                    						</td>
                    						<td>
                        						<select class="js_file_location js_enabled">
                        							<optgroup label="Blocking:">
                        								<option value="include"<?php if ($js_location == 'include'): ?> selected="selected"<?php endif; ?>>Embed in &lt;head&gt;</option>
                        								<option value="include-body"<?php if ($js_location == 'include-body'): ?> selected="selected"<?php endif; ?>>Embed after &lt;body&gt;</option>
                        								<option value="include-footer"<?php if ($js_location == 'include-footer'): ?> selected="selected"<?php endif; ?>>Embed before &lt;/body&gt;</option>
                    								</optgroup>
                    								<optgroup label="Non-Blocking:">
                    									<option value="include-nb"<?php if ($js_location == 'include-nb'): ?> selected="selected"<?php endif; ?>>Embed in &lt;head&gt;</option>
                    									<option value="include-body-nb"<?php if ($js_location == 'include-body-nb'): ?> selected="selected"<?php endif; ?>>Embed after &lt;body&gt;</option>
                    									<option value="include-footer-nb"<?php if ($js_location == 'include-footer-nb'): ?> selected="selected"<?php endif; ?>>Embed before &lt;/body&gt;</option>
                    								</optgroup>
                    							</select>
                    						</td>
                    						<td>
                    							<input class="js_file_delete js_enabled button" type="button" value="Delete" />
                    							<input class="js_file_verify js_enabled button" type="button" value="Verify URI" />
                    						</td>
										</tr>
        							</table>
            					</li>
		    	        		<?php endforeach; endif; ?>
        	    	    	<?php endforeach; ?>
                        <?php endforeach; ?>
                    <?php endif; endforeach; ?>
        			</ul>
                    <div id="js_files_empty" class="w3tc-empty" style="display: none;">No <acronym title="JavaScript">JS</acronym> files added</div>
                    <input id="js_file_add" class="js_enabled button" type="button" value="Add a script" />
        		</td>
        	</tr>
            <?php endif; ?>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
        	<input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="Save all settings" />
        </p>
    	<?php echo $this->postbox_footer(); ?>

		<?php echo $this->postbox_header('<acronym title="Cascading Style Sheet">CSS</acronym>'); ?>
        <table class="form-table">
        	<tr>
        		<th><acronym title="Cascading Style Sheet">CSS</acronym> minify settings:</th>
        		<td>
        			<input type="hidden" name="minify.css.enable" value="0" />
        			<input type="hidden" name="minify.css.combine" value="0" />
        			<label><input id="css_enabled" type="checkbox" name="minify.css.enable" value="1"<?php checked($this->_config->get_boolean('minify.css.enable'), true); ?> /> Enable</label><br />
        			<label><input class="css_enabled" type="checkbox" name="minify.css.combine" value="1"<?php checked($this->_config->get_boolean('minify.css.combine'), true); ?> /> Combine only</label><br />
                    <?php
                        $css_engine_file = '';

                        switch ($css_engine) {
                            case 'css':
                            case 'yuicss':
                            case 'csstidy':
                                $css_engine_file = W3TC_INC_DIR . '/options/minify/' . $css_engine . '.php';
                                break;
                        }

                        if (file_exists($css_engine_file)) {
                            include $css_engine_file;
                        }
                    ?>
        		</td>
        	</tr>
            <tr>
                <th><label for="minify_css_import">@import handling:</label></th>
                <td>
                    <select id="minify_css_import" class="css_enabled" name="minify.css.imports">
                        <?php foreach ($css_imports_values as $css_imports_key => $css_imports_value): ?>
                        <option value="<?php echo $css_imports_key; ?>"<?php selected($css_imports, $css_imports_key); ?>><?php echo $css_imports_value; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <?php
                $css_engine_file2 = '';

                switch ($css_engine) {
                    case 'css':
                    case 'yuicss':
                    case 'csstidy':
                        $css_engine_file2 = W3TC_INC_DIR . '/options/minify/' . $css_engine . '2.php';
                        break;
                }

                if (file_exists($css_engine_file2)) {
                    include $css_engine_file2;
                }
            ?>
            <?php if (!$auto): ?>
        	<tr>
        		<th><acronym title="Cascading Style Sheet">CSS</acronym> file management:</th>
        		<td>
        			<p>
        				<label>
        					Theme:
        					<select id="css_themes" class="css_enabled" name="css_theme">
        						<?php foreach ($themes as $theme_key => $theme_name): ?>
        						<option value="<?php echo htmlspecialchars($theme_key); ?>"<?php selected($theme_key, $css_theme); ?>><?php echo htmlspecialchars($theme_name); ?><?php if ($theme_key == $css_theme): ?> (active)<?php endif; ?></option>
        						<?php endforeach; ?>
        					</select>
        				</label>
    					<br /><span class="description">Files are minified by template. First select the theme to manage, then add style sheets used in all templates to the "All Templates" group. Use the menu above to manage style sheets unique to a specific template. If necessary drag &amp; drop to resolve dependency issues (due to incorrect order).</span>
        			</p>
        			<ul id="css_files" class="minify-files">
                    <?php foreach ($css_groups as $css_theme => $css_templates): if (isset($templates[$css_theme])): ?>
                        <?php $index = 0; foreach ($css_templates as $css_template => $css_locations): ?>
                        	<?php foreach ((array) $css_locations as $css_location => $css_config): ?>
                        		<?php if (! empty($css_config['files'])): foreach ((array) $css_config['files'] as $css_file): $index++; ?>
            					<li>
            						<table>
										<tr>
											<th>&nbsp;</th>
											<th>File URI:</th>
											<th colspan="2">Template:</th>
										</tr>
            							<tr>
            								<td><?php echo $index; ?>.</td>
	            							<td>
                    							<input class="css_enabled" type="text" name="css_files[<?php echo htmlspecialchars($css_theme); ?>][<?php echo htmlspecialchars($css_template); ?>][<?php echo htmlspecialchars($css_location); ?>][]" value="<?php echo htmlspecialchars($css_file); ?>" size="70" /><br />
	            							</td>
	            							<td>
                                    			<select class="css_file_template css_enabled">
                                    			<?php foreach ($templates[$css_theme] as $theme_template_key => $theme_template_name): ?>
                                    				<option value="<?php echo htmlspecialchars($theme_template_key); ?>"<?php selected($theme_template_key, $css_template); ?>><?php echo htmlspecialchars($theme_template_name); ?></option>
                                    			<?php endforeach; ?>
                                    			</select>
                                    		</td>
	            							<td>
                    							<input class="css_file_delete css_enabled button" type="button" value="Delete" />
                    							<input class="css_file_verify css_enabled button" type="button" value="Verify URI" />
                    						</td>
										</tr>
            						</table>
            					</li>
                        		<?php endforeach; endif; ?>
                        	<?php endforeach; ?>
                    	<?php endforeach; ?>
                    <?php endif; endforeach; ?>
        			</ul>
                    <div id="css_files_empty" class="w3tc-empty" style="display: none;">No <acronym title="Cascading Style Sheet">CSS</acronym> files added</div>
        			<input id="css_file_add" class="css_enabled button" type="button" value="Add a style sheet" />
        		</td>
        	</tr>
            <?php endif; ?>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
        	<input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="Save all settings" />
        </p>
        <?php echo $this->postbox_footer(); ?>

		<?php echo $this->postbox_header('Advanced'); ?>
        <table class="form-table">
    	<?php if ($this->_config->get_string('minify.engine') == 'memcached'): ?>
        	<tr>
        		<th><label for="memcached_servers">Memcached hostname:port / <acronym title="Internet Protocol">IP</acronym>:port:</label></th>
        		<td>
        			<input id="memcached_servers" type="text" name="minify.memcached.servers" value="<?php echo htmlspecialchars(implode(',', $this->_config->get_array('minify.memcached.servers'))); ?>" size="100" />
        			<input id="memcached_test" class="button {nonce: '<?php echo wp_create_nonce('w3tc'); ?>'}" type="button" value="Test" />
        			<span id="memcached_test_status" class="w3tc-status w3tc-process"></span>
        			<br /><span class="description">Multiple servers may be used and seperated by a comma; e.g. 192.168.1.100:11211, domain.com:22122</span>
        		</td>
        	</tr>
        	<?php endif; ?>
            <tr>
        		<th><label for="minify_lifetime">Update external files every:</label></th>
        		<td><input id="minify_lifetime" type="text" name="minify.lifetime" value="<?php echo $this->_config->get_integer('minify.lifetime'); ?>" size="8" /> seconds<br />
        		<span class="description">Specify the interval between download and update of external files in the minify cache. Hint: 6 hours is 21600 seconds. 12 hours is 43200 seconds. 24 hours is 86400 seconds.</span></td>
        	</tr>
        	<tr>
        		<th><label for="minify_file_gc">Garbage collection interval:</label></th>
        		<td><input id="minify_file_gc" type="text" name="minify.file.gc" value="<?php echo $this->_config->get_integer('minify.file.gc'); ?>" size="8"<?php if ($this->_config->get_string('minify.engine') != 'file'): ?> disabled="disabled"<?php endif; ?> /> seconds
        			<br /><span class="description">If caching to disk, specify how frequently expired cache data is removed. For busy sites, a lower value is best.</span>
    			</td>
        	</tr>
            <tr>
                <th><label for="minify_reject_uri">Never minify the following pages:</label></th>
                <td>
                    <textarea id="minify_reject_uri" name="minify.reject.uri" cols="40" rows="5"><?php echo htmlspecialchars(implode("\r\n", $this->_config->get_array('minify.reject.uri'))); ?></textarea><br />
                    <span class="description">Always ignore the specified pages / directories.</span>
                </td>
            </tr>
        	<tr>
        		<th><label for="minify_reject_ua">Rejected user agents:</label></th>
        		<td>
        			<textarea id="minify_reject_ua" name="minify.reject.ua" cols="40" rows="5"><?php echo htmlspecialchars(implode("\r\n", $this->_config->get_array('minify.reject.ua'))); ?></textarea><br />
        			<span class="description">Specify user agents that will never receive minified content.</span>
        		</td>
        	</tr>
        </table>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
        	<input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="Save all settings" />
        </p>
    	<?php echo $this->postbox_footer(); ?>

		<?php echo $this->postbox_header('Note(s):'); ?>
        <table class="form-table">
        	<tr>
        		<th colspan="2">
					<ul>
						<li>Enable <acronym title="Hypertext Transfer Protocol">HTTP</acronym> compression in the "Cascading Style Sheets &amp; JavaScript" section on <a href="admin.php?page=w3tc_browsercache">Browser Cache</a> Settings tab.</li>
						<li>The <acronym title="Time to Live">TTL</acronym> of page cache files is set via the "Expires header lifetime" field in the "Cascading Style Sheets &amp; JavaScript" section on <a href="admin.php?page=w3tc_browsercache">Browser Cache</a> Settings tab.</li>
					</ul>
        		</th>
        	</tr>
        </table>
    	<?php echo $this->postbox_footer(); ?>
    </div>
</form>

<?php include W3TC_INC_DIR . '/options/common/footer.php'; ?>