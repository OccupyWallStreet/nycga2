<?php
//handle forced update
if ( isset($_GET['action']) && $_GET['action'] == 'update' ) {

	$result = $this->process();
	if ( is_array($result) ) {
		?><div class="updated fade"><p><?php _e('Update data successfully refreshed from WPMU DEV.', 'wpmudev'); ?></p></div><?php
	} else {
		?><div class="error fade"><p><?php _e('There was a problem refreshing data from WPMU DEV.', 'wpmudev'); ?></p></div><?php
	}

} else {
	$this->refresh_local_projects();
}

if ( isset($_POST['wpmudev_apikey']) ) {
	update_site_option('wpmudev_apikey', $_POST['wpmudev_apikey']);
	$result = $this->process();
	if ( is_array($result) && !$result['membership'] ) {
		update_site_option('wpmudev_apikey', '');
		?><div class="error fade"><p><?php _e('Your API Key was invalid. Please try again.', 'wpmudev'); ?></p></div><?php
	}

	if ( $result['membership'] == 'full' ) { //free member
		update_site_option('wdp_un_hide_upgrades', $_POST['hide_upgrades']);
		update_site_option('wdp_un_hide_notices', $_POST['hide_notices']);
		update_site_option('wdp_un_hide_releases', $_POST['hide_releases']);
	} else if ( is_numeric( $result['membership'] ) ) { //single
		update_site_option('wdp_un_hide_upgrades', $_POST['hide_upgrades']);
	}

	?><div class="updated fade"><p><?php _e('Settings Saved!', 'wpmudev'); ?></p></div><?php
}
?>


<hr class="section-head-divider" />
<div class="wrap grid_container">
	<h1 class="section-header header-long">
		<span class="symbol">Z</span>
		<?php _e('Updates, upgrades &amp; new releases ', 'wpmudev') ?>
	</h1>
</div>
<div class="updates-container grid_container">
		<ul>
			<li><span class="symbol scoping">6</span> - <?php _e('scoping', 'wpmudev'); ?></li>
			<li><span class="symbol development">6</span> - <?php _e('development', 'wpmudev'); ?></li>
			<li><span class="symbol testing">6</span> - <?php _e('testing', 'wpmudev'); ?></li>
		</ul>
		<table cellpadding="0" cellspacing="0" border="0">
			<thead>
				<tr>
				<td width="9.6%" align="center"><?php _e('Type', 'wpmudev'); ?></td>
				<td width="27.6%" align="left"><?php _e('Project name', 'wpmudev'); ?></td>
				<td width="10.57%" align="left"><?php _e('Version', 'wpmudev'); ?></td>
				<td width="36.85%" align="left"><?php _e('Status', 'wpmudev'); ?></td>
				<td width="15.37%" align="left"><?php _e('Who', 'wpmudev'); ?></td>
				</tr>
			</thead>
			<tbody>
				<tr><td colspan="5" height="10px"></td></tr>
				<tr>
					<td align="center"><span class="symbol">~</span></td>
					<td align="left">Theme Creation framework</td>
					<td align="left">1.0.4</td>
					<td align="left"><img src="../wp-content/plugins/wpmudev-updates/includes/images/graph.gif" /></td>
					<td align="left">
						<ul>
							<li><img src="../wp-content/plugins/wpmudev-updates/includes/images/tiny_g.gif" /></li>
						</ul>
					</td>
				</tr>
				<tr>
					<td align="center"><span class="symbol">~</span></td>
					<td align="left">Theme Creation framework</td>
					<td align="left">1.0.4</td>
					<td align="left"><img src="../wp-content/plugins/wpmudev-updates/includes/images/graph.gif" /></td>
					<td align="left">
						<ul>
							<li><img src="../wp-content/plugins/wpmudev-updates/includes/images/tiny_g.gif" /></li>
							<li><img src="../wp-content/plugins/wpmudev-updates/includes/images/tiny_g.gif" /></li>
							<li><img src="../wp-content/plugins/wpmudev-updates/includes/images/tiny_g.gif" /></li>
						</ul>
					</td>
				</tr>
				<tr>
					<td align="center"><span class="symbol">~</span></td>
					<td align="left">Theme Creation framework</td>
					<td align="left">1.0.4</td>
					<td align="left"><img src="../wp-content/plugins/wpmudev-updates/includes/images/graph.gif" /></td>
					<td align="left">
						<ul>
							<li><img src="../wp-content/plugins/wpmudev-updates/includes/images/tiny_g.gif" /></li>
							<li><img src="../wp-content/plugins/wpmudev-updates/includes/images/tiny_g.gif" /></li>
							<li><img src="../wp-content/plugins/wpmudev-updates/includes/images/tiny_g.gif" /></li>
							<li><img src="../wp-content/plugins/wpmudev-updates/includes/images/tiny_g.gif" /></li>
						</ul>
					</td>
				</tr>
				<tr>
					<td align="center"><span class="symbol">~</span></td>
					<td align="left">Theme Creation framework</td>
					<td align="left">1.0.4</td>
					<td align="left"><img src="../wp-content/plugins/wpmudev-updates/includes/images/graph.gif" /></td>
					<td align="left">
						<ul>
							<li><img src="../wp-content/plugins/wpmudev-updates/includes/images/tiny_g.gif" /></li>
							<li><img src="../wp-content/plugins/wpmudev-updates/includes/images/tiny_g.gif" /></li>
						</ul>
					</td>
				</tr>
				<tr>
					<td align="center"><span class="symbol">~</span></td>
					<td align="left">Theme Creation framework</td>
					<td align="left">1.0.4</td>
					<td align="left"><img src="../wp-content/plugins/wpmudev-updates/includes/images/graph.gif" /></td>
					<td align="left">
						<ul>
							<li><img src="../wp-content/plugins/wpmudev-updates/includes/images/tiny_g.gif" /></li>
							<li><img src="../wp-content/plugins/wpmudev-updates/includes/images/tiny_g.gif" /></li>
							<li><img src="../wp-content/plugins/wpmudev-updates/includes/images/tiny_g.gif" /></li>
						</ul>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
