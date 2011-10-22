<?php
/**
 * General Admin for Capability Manager.
 * Provides admin pages to create and manage roles and capabilities.
 *
 * @version		$Rev: 198515 $
 * @author		Jordi Canals
 * @copyright   Copyright (C) 2009, 2010 Jordi Canals
 * @license		GNU General Public License version 2
 * @link		http://alkivia.org
 * @package		Alkivia
 * @subpackage	CapsMan
 *

	Copyright 2009, 2010 Jordi Canals <devel@jcanals.cat>

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	version 2 as published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

$roles = $this->roles;
$default = $this->current;

?>
<div class="wrap">
	<div id="icon-capsman-admin" class="icon32"></div>
	<h2><?php _e('Roles and Capabilities', $this->ID) ?></h2>

	<form method="post" action="admin.php?page=<?php echo $this->ID ?>">
	<?php wp_nonce_field('capsman-general-manager'); ?>
	<fieldset>
	<table id="akmin">
	<tr>
		<td class="content">
		<dl>
			<dt><?php printf(__('Capabilities for %s', $this->ID), $roles[$default]); ?></dt>
			<dd>
				<table width='100%' class="form-table">
				<tr>
				<?php
				$i = 0; $first_row = true;
				$current = get_role($default);
				$rcaps = $current->capabilities;
				foreach ( $this->capabilities as $key => $cap ) :

					// Levels are not shown.
					if ( preg_match( '/^level_(10|[0-9])$/i', $key ) ) {
						continue;
					}

					if ( $i == $this->getOption('form-rows') ) {
						echo '</tr><tr>';
						$i = 0; $first_row = false;
					}
					$style = ( isset($rcaps[$key]) && $rcaps[$key] ) ? 'color:green;font-weight:bold;' : 'color:red;';

					$disable = '';
					if ( 'manage_capabilities' == $key ) {
						if ( ! current_user_can('administrator') ) {
							continue;
						} elseif ( 'administrator' == $default ) {
							$disable = 'disabled="disabled"';
						}
					}
				?>
					<td style="<?php echo $style; ?>"><label for="caps[<?php echo $key; ?>]"><input id=caps[<?php echo $key; ?>] type="checkbox" name="caps[<?php echo $key; ?>]" value="1" <?php checked(1, $rcaps[$key]); echo $disable;?> />
					<?php echo $cap;
					if ( ! empty($disable) ) {
						echo '<input type="hidden" name="caps[manage_capabilities]" value="1" />';
					}
					?></label></td>
				<?php
					$i++;
				endforeach;

				if ( $i == $this->getOption('form-rows') ) {
					echo '</tr><tr>';
					$i = 0;
				}

				$level = ak_caps2level($rcaps);
				?>
				<td><?php _e('Level:', $this->ID) ;?><select name="level">
				<?php for ( $l = $this->max_level; $l >= 0; $l-- ) {?>
						<option value="<?php echo $l; ?>" style="text-align:right;"<?php selected($level, $l); ?>>&nbsp;<?php echo $l; ?>&nbsp;</option>
					<?php }
					++$i;

					if ( ! $first_row ) {
						// Now close a wellformed table
						for ( $i; $i < $this->settings['form-rows']; $i++ ){
							echo '<td>&nbsp;</td>';
						}
					}
					?>
				</select>

				</tr>
				</table>
			</dd>
		</dl>

		<p class="submit">
			<input type="hidden" name="action" value="update" />
			<input type="hidden" name="current" value="<?php echo $default; ?>" />
			<input type="submit" name="Save" value="<?php _e('Save Changes', $this->ID) ?>" class="button-primary" /> &nbsp;
			<?php if ( current_user_can('administrator') && 'administrator' != $default ) : ?>
					<a class="ak-delete" title="<?php echo attribute_escape(__('Delete this role', $this->ID)) ?>" href="<?php echo wp_nonce_url("admin.php?page={$this->ID}&amp;action=delete&amp;role={$default}", 'delete-role_' . $default); ?>" onclick="if ( confirm('<?php echo js_escape(sprintf(__("You are about to delete the %s role.\n 'Cancel' to stop, 'OK' to delete.", $this->ID), $roles[$default])); ?>') ) { return true;}return false;"><?php _e('Delete Role', $this->ID)?></a>
			<?php endif; ?>
		</p>

		<?php ak_admin_footer($this->ID, 2009); ?>

		</td>
		<td class="sidebar">
			<?php ak_admin_authoring($this->ID); ?>

			<dl>
				<dt><?php _e('Select New Role', $this->ID); ?></dt>
				<dd style="text-align:center;">
					<p><select name="role">
					<?php
					foreach ( $roles as $role => $name ) {
						echo '<option value="' . $role .'"'; selected($default, $role); echo '> ' . $name . ' &nbsp;</option>';
					}
					?>
					</select><br /><input type="submit" name="Change" value="<?php _e('Change', $this->ID) ?>" class="button" /></p>
				</dd>
			</dl>

			<dl>
				<dt><?php _e('Create New Role', $this->ID); ?></dt>
				<dd style="text-align:center;">
					<p><input type="text" name="create-name"" class="regular-text" /><br />
					<input type="submit" name="Create" value="<?php _e('Create', $this->ID) ?>" class="button" /></p>
				</dd>
			</dl>

			<dl>
				<dt><?php _e('Copy this role to', $this->ID); ?></dt>
				<dd style="text-align:center;">
					<p><input type="text" name="copy-name" class="regular-text" /><br />
					<input type="submit" name="Copy" value="<?php _e('Copy', $this->ID) ?>" class="button" /></p>
				</dd>
			</dl>

			<dl>
				<dt><?php _e('Add Capability', $this->ID); ?></dt>
				<dd style="text-align:center;">
					<p><input type="text" name="capability-name" class="regular-text" /><br />
					<input type="submit" name="AddCap" value="<?php _e('Add to role', $this->ID) ?>" class="button" /></p>
				</dd>
			</dl>

		</td>
	</tr>
	</table>
	</fieldset>
	</form>
</div>
