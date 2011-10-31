<?php if (!defined('W3TC')) die(); ?>
<?php include W3TC_INC_DIR . '/options/common/header.php'; ?>

<script type="text/javascript">/*<![CDATA[*/
var mobile_themes = {};
<?php foreach ($themes as $theme_key => $theme_name): ?>
mobile_themes['<?php echo addslashes($theme_key); ?>'] = '<?php echo addslashes($theme_name); ?>';
<?php endforeach; ?>
/*]]>*/</script>

<p>
    User agent group support is always <span class="w3tc-enabled">enabled</span>.
</p>

<form id="mobile_form" action="admin.php?page=<?php echo $this->_page; ?>" method="post">
    <div class="metabox-holder">
		<?php echo $this->postbox_header('Manage User Agent Groups'); ?>
		<p>
			<input id="mobile_add" type="button" class="button" value="Create a group" /> of user agents by specifying names in the user agents field. Assign a set of user agents to use a specific theme, redirect them to another domain or if an existing mobile plugin is active, create user agent groups to ensure that a unique cache is created for each user agent group. Drag and drop groups into order (if needed) to determine their priority (top -&gt; down).
		</p>

		<ul id="mobile_groups">
			<?php $index = 0; foreach ($groups as $group => $group_config): $index++; ?>
			<li id="mobile_group_<?php echo htmlspecialchars($group); ?>">
                <table class="form-table">
                	<tr>
                		<th>
                			Group name:
                		</th>
                		<td>
                			<span class="mobile_group_number"><?php echo $index; ?>.</span> <span class="mobile_group"><?php echo htmlspecialchars($group); ?></span> <input type="button" class="button mobile_delete" value="Delete group" />
                		</td>
                	</tr>
                	<tr>
                		<th>
                			<label for="mobile_groups_<?php echo htmlspecialchars($group); ?>_enabled">Enabled:</label>
                		</th>
                		<td>
                			<input type="hidden" name="mobile_groups[<?php echo htmlspecialchars($group); ?>][enabled]" value="0" />
                			<input id="mobile_groups_<?php echo htmlspecialchars($group); ?>_enabled" type="checkbox" name="mobile_groups[<?php echo htmlspecialchars($group); ?>][enabled]" value="1"<?php checked($group_config['enabled'], true); ?> />
                		</td>
                	</tr>
                	<tr>
                		<th>
                			<label for="mobile_groups_<?php echo htmlspecialchars($group); ?>_theme">Theme:</label>
                		</th>
                		<td>
                			<select id="mobile_groups_<?php echo htmlspecialchars($group); ?>_theme" name="mobile_groups[<?php echo htmlspecialchars($group); ?>][theme]">
                				<option value="">-- Pass-through --</option>
	                			<?php foreach ($themes as $theme_key => $theme_name): ?>
                				<option value="<?php echo htmlspecialchars($theme_key); ?>"<?php selected($theme_key, $group_config['theme']); ?>><?php echo htmlspecialchars($theme_name); ?></option>
    	            			<?php endforeach; ?>
                			</select>
                			<br /><span class="description">Assign this group of user agents to a specific theme. Selecting "Pass-through" allows any plugin(s) (e.g. mobile plugins) to properly handle requests for these user agents. If the "redirect users to" field is not empty, this setting is ignored.</span>
                		</td>
                	</tr>
                	<tr>
                		<th>
                			<label for="mobile_groups_<?php echo htmlspecialchars($group); ?>_redirect">Redirect users to:</label>
                		</th>
                		<td>
                			<input id="mobile_groups_<?php echo htmlspecialchars($group); ?>_redirect" type="text" name="mobile_groups[<?php echo htmlspecialchars($group); ?>][redirect]" value="<?php echo htmlspecialchars($group_config['redirect']); ?>" size="60" />
                			<br /><span class="description">A 302 redirect is used to send this group of users to another hostname (domain); recommended if a 3rd party service provides a mobile version of your site.</span>
                		</td>
                	</tr>
                	<tr>
                		<th>
                			<label for="mobile_groups_<?php echo htmlspecialchars($group); ?>_agents">User agents:</label>
                		</th>
                		<td>
                			<textarea id="mobile_groups_<?php echo htmlspecialchars($group); ?>_agents" name="mobile_groups[<?php echo htmlspecialchars($group); ?>][agents]" rows="10" cols="50"><?php echo htmlspecialchars(implode("\r\n", (array) $group_config['agents'])); ?></textarea>
                			<br /><span class="description">Specify the user agents for this group. Remember to escape special characters like spaces, dots or dashes with a backslash. Regular expressions are also supported.</span>
                		</td>
                	</tr>
                </table>
            </li>
		    <?php endforeach; ?>
	    </ul>
	    <div id="mobile_groups_empty" style="display: none;">No groups added. All user agents recieve the same page and minify cache results.</div>

        <p class="submit">
            <?php echo $this->nonce_field('w3tc'); ?>
        	<input type="submit" name="w3tc_save_options" class="w3tc-button-save button-primary" value="Save all settings" />
        </p>
    	<?php echo $this->postbox_footer(); ?>
    </div>
</form>

<?php include W3TC_INC_DIR . '/options/common/footer.php'; ?>
