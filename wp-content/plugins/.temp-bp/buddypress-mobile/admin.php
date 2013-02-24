<?php

add_action('admin_menu', 'bp_mobile_plugin_menu');
add_action('network_admin_menu', 'bp_mobile_plugin_menu');

function bp_mobile_plugin_menu() {
	add_submenu_page( 'bp-general-settings', 'BuddyPress Mobile', 'BuddyPress Mobile', 'manage_options', 'bp-mobile', 'bp_mobile_plugin_options');

	//call register settings function
	add_action( 'admin_init', 'bp_mobile_register_settings' );
}

function bp_mobile_register_settings() {
	//register our settings
	register_setting( 'bp_mobile_plugin_options', 'add2homescreen' );
	register_setting( 'bp_mobile_plugin_options', 'style' );
	register_setting( 'bp_mobile_plugin_options', 'lazyload' );
	register_setting( 'bp_mobile_plugin_options', 'admob-on-off' );
	register_setting( 'bp_mobile_plugin_options', 'admob' );
}

function bp_mobile_plugin_options() {
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );

	}

?>

			<?php if ( !empty( $_GET['updated'] ) ) : ?>
				<div id="message" class="updated">
					<p><strong><?php _e('settings saved.', 'buddypress' ); ?></strong></p>
				</div>
			<?php endif; ?>



<div class="wrap">
<h2><?php _e('BuddyPress Mobile Settings', 'buddypress') ?></h2>

<form method="post" action="options.php">
<?php wp_nonce_field('update-options'); ?>
<?php settings_fields('bp_mobile_plugin_options'); ?>
<table class="wp-list-table widefat users" cellspacing="0">
<thead>
	<tr>
		<th><?php _e('Please choose options.', 'buddypress') ?></th>
		<th></th>
		<th></th>
	</tr>
</thead>

<tbody id="the-list" class="list:user">
	<tr>
		<th>Add to homescreen notice</th>
		<td><input type="checkbox" name="add2homescreen" value="1" <?php if (get_option('add2homescreen')==1) echo 'checked="checked"'; ?>/></td>
		<td></td>
	</tr>

	<tr>
		<th class="alternate">Style</th>
		<td class="alternate">Default  <input type="radio" id="style-default" name="style" value="default" <?php if (get_option('style')=='default') echo 'checked="checked"'; ?>/></td>
		<td class="alternate">Black  <input type="radio" id="style-black" name="style" value="black" <?php if (get_option('style')=='black') echo 'checked="checked"'; ?>/></td>
	</tr>

	<tr>
		<th>Lazy Load Images</th>
		<td><input type="checkbox" id="lazyload" name="lazyload" value="1" <?php if (get_option('lazyload')==1) echo 'checked="checked"'; ?>/></td>
		<td></td>
	</tr>
	
	<tr>
		<th>AdMob Publisher ID</th>
		<td>
		
		<input type="checkbox" id="admob-on-off" name="admob-on-off" value="1" <?php if (get_option('admob-on-off')==1) echo 'checked="checked"'; ?>/>
		<label>  ID</label>
		<input type="text" id="admob" name="admob" value="<?php echo get_option( 'admob' ); ?>"/>
		</td>
		<td><a target="_blank" href="http://www.admob.com">Get AdMob Publisher ID</a></td>
	</tr>

</tbody>

<tfoot>
	<tr>
		<th></th>
		<th></th>
		<th></th>
	</tr>
</tfoot>
</table>

<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="add2homescreen" />
<input type="hidden" name="page_options" value="lazyload" />
<input type="hidden" name="page_options" value="style" />
<input type="hidden" name="page_options" value="admob-on-off" />
<input type="hidden" name="page_options" value="admob" />

<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>

</form>
</div>

<h3>More options coming soon.</h3>

<h3>Forthcoming support for:</h3>
<p>Click to install</p>
<p><a class="button-primary" href="/wp-admin/plugin-install.php?tab=search&type=term&s=achievements+for+buddypress&plugin-search-input=Search+Plugins">Achievements for BuddyPress</a></p>
<p><a class="button-primary" href="/wp-admin/plugin-install.php?tab=search&type=term&s=BuddyPress+Album%2B&plugin-search-input=Search+Plugins">BuddyPress Album+</a></p>
<p><a class="button-primary" href="/wp-admin/plugin-install.php?tab=search&type=term&s=BuddyPress+docs&plugin-search-input=Search+Plugins">BuddyPress Docs</a></p>
<p><a class="button-primary" href="/wp-admin/plugin-install.php?tab=search&type=term&s=BuddyPress+share+it&plugin-search-input=Search+Plugins">BuddyPress Share it</a></p>
<p><a class="button-primary" href="/wp-admin/plugin-install.php?tab=search&type=term&s=BuddyPress+verified&plugin-search-input=Search+Plugins">BuddyPress Verified</a></p>

<?php
}
?>