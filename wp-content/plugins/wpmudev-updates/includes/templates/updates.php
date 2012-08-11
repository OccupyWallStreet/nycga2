<?php
//handle forced update
if ( isset($_GET['action']) && $_GET['action'] == 'update' ) {

	$result = $this->refresh_updates();
	if ( is_array($result) ) {
		?><div class="updated fade"><p><?php _e('Update data successfully refreshed from WPMU DEV.', 'wpmudev'); ?></p></div><?php
	} else {
		?><div class="error fade"><p><?php _e('There was a problem refreshing data from WPMU DEV.', 'wpmudev'); ?></p></div><?php
	}

} else {
	$this->refresh_local_projects();
}

$data = $this->get_updates(); //load up the data

$allow_auto = true;
if ( $this->get_apikey() && $this->allowed_user() && ($data['membership'] == 'full' || is_numeric($data['membership'])) && isset($data['downloads']) && $data['downloads'] != 'enabled' ) {
	?><div class="error fade"><p><?php _e('You have reached your maximum enabled sites for automatic updates. You may <a href="http://premium.wpmudev.org/wp-admin/profile.php?page=wdpun">change which sites are enabled or upgrade to a higher membership level here &raquo;</a>', 'wpmudev'); ?></p></div><?php
	$allow_auto = false;
}

if (!$this->allowed_user()) {
	$allow_auto = false;
}
?>
<div class="wrap">
<h3 class="nav-tab-wrapper">
<?php
$tab = ( !empty($_GET['tab']) ) ? $_GET['tab'] : 'available';

$tabs = array(
	'available'    => __('Available Updates', 'wpmudev'),
	'installed'    => __('Installed', 'wpmudev')
);
$tabhtml = array();

// If someone wants to remove or add a tab
$tabs = apply_filters( 'wpmudev_updates_tabs', $tabs );

foreach ( $tabs as $stub => $title ) {
	$class = ( $stub == $tab ) ? ' nav-tab-active' : '';
	$tabhtml[] = '	<a href="' . $this->updates_url . '&tab=' . $stub . '" class="nav-tab'.$class.'">'.$title.'</a>';
}

echo implode( "\n", $tabhtml );
?>
</h3>
<div class="clear"></div>	

<div class="grid_container">

<?php
switch( $tab ) {
	//---------------------------------------------------//
	case "available":
		?>
		<h2><?php _e('WPMU DEV Updates Available', 'wpmudev') ?></h2>
		<?php
		$last_run = get_site_option('wdp_un_last_run');
		$projects = array();
		if ( is_array( $data ) ) {
			$remote_projects = isset($data['projects']) ? $data['projects'] : array();
			$local_projects = $this->get_local_projects();
			if ( is_array( $local_projects ) ) {
				foreach ( $local_projects as $local_id => $local_project ) {
					//skip if not in remote results
					if (!isset($remote_projects[$local_id]))
						continue;
					
					$type = $remote_projects[$local_id]['type'];
					
					$projects[$type][$local_id]['thumbnail'] = $remote_projects[$local_id]['thumbnail'];
					$projects[$type][$local_id]['name'] = $remote_projects[$local_id]['name'];
					$projects[$type][$local_id]['description'] = $remote_projects[$local_id]['short_description'];
					$projects[$type][$local_id]['url'] = $remote_projects[$local_id]['url'];
					$projects[$type][$local_id]['instructions_url'] = $remote_projects[$local_id]['instructions_url'];
					$projects[$type][$local_id]['support_url'] = $remote_projects[$local_id]['support_url'];
					$projects[$type][$local_id]['changelog'] = $remote_projects[$local_id]['changelog'];
					$projects[$type][$local_id]['autoupdate'] = (($local_project['type'] == 'plugin' || $local_project['type'] == 'theme') && $this->get_apikey() && $allow_auto) ? $remote_projects[$local_id]['autoupdate'] : 0;

					//handle wp autoupgrades
					if ($projects[$type][$local_id]['autoupdate'] == '2') {
						if ($local_project['type'] == 'plugin') {
							$update_plugins = get_site_transient('update_plugins');
							if (isset($update_plugins->response[$local_project['filename']]->new_version))
								$projects[$type][$local_id]['remote_version'] = $update_plugins->response[$local_project['filename']]->new_version;
							else
								$projects[$type][$local_id]['remote_version'] = $local_project['version'];
						} else if ($local_project['type'] == 'theme') {
							$update_themes = get_site_transient('update_themes');
							if (isset($update_themes->response[$local_project['filename']]['new_version']))
								$projects[$type][$local_id]['remote_version'] = $update_themes->response[$local_project['filename']]['new_version'];
							else
								$projects[$type][$local_id]['remote_version'] = $local_project['version'];
						} else {
							$projects[$type][$local_id]['remote_version'] = $remote_projects[$local_id]['version'];
						}
					} else {
						$projects[$type][$local_id]['remote_version'] = $remote_projects[$local_id]['version'];
					}

					$projects[$type][$local_id]['local_version'] = $local_project['version'];
					$projects[$type][$local_id]['filename'] = $local_project['filename'];
					$projects[$type][$local_id]['type'] = $local_project['type'];
					
					if ( !version_compare($projects[$type][$local_id]['remote_version'], $local_project['version'], '>') ) {
						unset($projects[$type][$local_id]);
						continue;
					}
				}
			}
		}
		?>
		<p><?php _e('Here you can find information about any available updates for your installed WPMU DEV themes and plugins. Note that it is important to keep your themes and plugins updated for security, performance, and to maintain compatibility with the latest versions of WordPress. Most plugins and themes are able to be auto-updated depending on where they are installed.', 'wpmudev') ?></p>
		
		<form class="upgrade" name="upgrade-plugins" action="update-core.php?action=do-plugin-upgrade" method="post">
		
		
		<?php
		$form_fields = array();
		$rows = '';
		if (is_array($projects['plugin']) && count($projects['plugin']) > 0) {
			$class = (isset($class) && 'alternate' == $class) ? '' : 'alternate';
			foreach ($projects['plugin'] as $project_id => $project) {
				$local_version = $project['local_version'];
				$remote_version = $project['remote_version'];

				if ( $project['autoupdate'] && $project['type'] == 'plugin' ) {
					$upgrade_button_code = "<a href='" . wp_nonce_url( $this->self_admin_url('update.php?action=upgrade-plugin&plugin=') . $project['filename'], 'upgrade-plugin_' . $project['filename']) . "' class='button-secondary'><i class='icon-upload-alt'></i> ".__('Auto Update', 'wpmudev').'</a>';
					$form_fields[] = '<input type="hidden" value="'.$project['filename'].'" name="checked[]">';
				} else if ( $project['autoupdate'] && $project['type'] == 'theme' ) {
					$upgrade_button_code = "<a href='" . wp_nonce_url( $this->self_admin_url('update.php?action=upgrade-theme&theme=') . $project['filename'], 'upgrade-theme_' . $project['filename']) . "' class='button-secondary'><i class='icon-upload-alt'></i> ".__('Auto Update', 'wpmudev').'</a><input type="hidden" value="'.$project['filename'].'" name="checked[]">';
					$form_fields[] = '<input type="hidden" value="'.$project['filename'].'" name="checked[]">';
				} else {
					$upgrade_button_code = "<a href='" . $project['url'] . "' class='button-secondary' target='_blank'><i class='icon-download-alt'></i> ".__('Download Update', 'wpmudev')."</a>";
				}

				$upgrade_button = (version_compare($remote_version, $local_version, '>')) ? $upgrade_button_code : '';

				$screenshot = $project['thumbnail'];

				//=========================================================//
				$rows .= "<tr class='wdv-update " . $class . "'>";
				$rows .= "<td style='vertical-align:middle'><img src='$screenshot' width='100' height='60' style='float:left; padding: 5px' /></a><strong><a href='{$this->server_url}?action=description&id={$project_id}&TB_iframe=true&width=640&height=800' class='thickbox' title='" . sprintf( __('%s Details', 'wpmudev'), $project['name'] ) . "'>{$project['name']}</a></strong><br />{$project['description']}</td>";
				$rows .= "<td style='vertical-align:middle;width:200px;'><a href='{$this->server_url}?action=help&id={$project_id}&TB_iframe=true&width=640&height=800' class='thickbox' title='" . sprintf( __('%s Installation & Use Instructions', 'wpmudev'), $project['name'] ) . "'><i class='icon-info-sign'></i> " . __('Installation & Use Instructions', 'wpmudev') . "</a><br /><a target='_blank' href='{$project['support_url']}'><i class='icon-question-sign'></i> " . __('Get Support', 'wpmudev') . "</a></td>";
				$rows .= "<td style='vertical-align:middle'><strong>" . $local_version . "</strong></td>";
				$rows .= "<td style='vertical-align:middle'><strong><a href='{$this->server_url}?action=details&id={$project_id}&TB_iframe=true&width=640&height=800' class='thickbox' title='" . sprintf( __('View version %s details', 'wpmudev'), $remote_version ) . "'>{$remote_version}</a></strong></td>";
				$rows .= "<td style='vertical-align:middle'>" . $upgrade_button . "</td>";
				$rows .= "</tr>";
				$rows .= "<tr class='wdv-changelog'><td colspan='5'>";
				$rows .= "<div class='wdv-view-link'><a href='#'>" . __('View Changes', 'wpmudev') . " <i class='icon-chevron-down'></i></a></div>";
				$rows .= "<div class='wdv-changelog-drop'>" . $project['changelog'];
				$rows .= "<div class='wdv-close-link'><a href='#'>" . __('Close', 'wpmudev') . " <i class='icon-chevron-up'></a></div>";
				$rows .= "</div></td></tr>";
				$class = ('alternate' == $class) ? '' : 'alternate';
				//=========================================================//
			}
		} else {
			$rows .= '<tr><td colspan="5">' . __('No WPMU DEV plugin updates required', 'wpmudev') . '</td></tr>';
		}
		
		echo '<h3>' . __('WPMU DEV Plugin Updates', 'wpmudev');
		if (count($form_fields) >= 2) {
			echo implode("\n", $form_fields);
			wp_nonce_field('upgrade-core');
			echo "<a href='#' class='button-secondary upgrade-all'><i class='icon-upload-alt'></i> ".__('Update All Plugins', 'wpmudev')."</a>";
		}
		echo '</h3>';
		
		echo "
			<table cellpadding='3' cellspacing='3' width='100%' class='widefat'>
			<thead><tr>
			<th scope='col'>".__('Name', 'wpmudev')."</th>
			<th scope='col'>".__('Links', 'wpmudev')."</th>
			<th scope='col'>".__('Installed Version', 'wpmudev')."</th>
			<th scope='col'>".__('Latest Version', 'wpmudev')."</th>
			<th scope='col'>".__('Actions', 'wpmudev')."</th>
			</tr></thead>
			<tbody id='the-list'>
			";
			
			echo $rows;
		?>
		</tbody></table>
		</form>
		
		<form class="upgrade" name="upgrade-themes" action="update-core.php?action=do-theme-upgrade" method="post">
		<?php
		$form_fields = array();
		$rows = '';
		if (is_array($projects['theme']) && count($projects['theme']) > 0) {
			$class = (isset($class) && 'alternate' == $class) ? '' : 'alternate';
			foreach ($projects['theme'] as $project_id => $project) {
				$local_version = $project['local_version'];
				$remote_version = $project['remote_version'];

				if ( $project['autoupdate'] && $project['type'] == 'plugin' ) {
					$upgrade_button_code = "<a href='" . wp_nonce_url( $this->self_admin_url('update.php?action=upgrade-plugin&plugin=') . $project['filename'], 'upgrade-plugin_' . $project['filename']) . "' class='button-secondary'><i class='icon-upload-alt'></i> ".__('Auto Update', 'wpmudev').'</a>';
					$form_fields[] = '<input type="hidden" value="'.$project['filename'].'" name="checked[]">';
				} else if ( $project['autoupdate'] && $project['type'] == 'theme' ) {
					$upgrade_button_code = "<a href='" . wp_nonce_url( $this->self_admin_url('update.php?action=upgrade-theme&theme=') . $project['filename'], 'upgrade-theme_' . $project['filename']) . "' class='button-secondary'><i class='icon-upload-alt'></i> ".__('Auto Update', 'wpmudev').'</a><input type="hidden" value="'.$project['filename'].'" name="checked[]">';
					$form_fields[] = '<input type="hidden" value="'.$project['filename'].'" name="checked[]">';
				} else {
					$upgrade_button_code = "<a href='" . $project['url'] . "' class='button-secondary' target='_blank'><i class='icon-download-alt'></i> ".__('Download Update', 'wpmudev')."</a>";
				}

				$upgrade_button = (version_compare($remote_version, $local_version, '>')) ? $upgrade_button_code : '';

				$screenshot = $project['thumbnail'];

				//=========================================================//
				$rows .= "<tr class='wdv-update " . $class . "'>";
				$rows .= "<td style='vertical-align:middle'><img src='$screenshot' width='100' height='60' style='float:left; padding: 5px' /></a><strong><a href='{$this->server_url}?action=description&id={$project_id}&TB_iframe=true&width=640&height=800' class='thickbox' title='" . sprintf( __('%s Details', 'wpmudev'), $project['name'] ) . "'>{$project['name']}</a></strong><br />{$project['description']}</td>";
				$rows .= "<td style='vertical-align:middle;width:200px;'><a href='{$this->server_url}?action=help&id={$project_id}&TB_iframe=true&width=640&height=800' class='thickbox' title='" . sprintf( __('%s Installation & Use Instructions', 'wpmudev'), $project['name'] ) . "'><i class='icon-info-sign'></i> " . __('Installation & Use Instructions', 'wpmudev') . "</a><br /><a target='_blank' href='{$project['support_url']}'><i class='icon-question-sign'></i> " . __('Get Support', 'wpmudev') . "</a></td>";
				$rows .= "<td style='vertical-align:middle'><strong>" . $local_version . "</strong></td>";
				$rows .= "<td style='vertical-align:middle'><strong><a href='{$this->server_url}?action=details&id={$project_id}&TB_iframe=true&width=640&height=800' class='thickbox' title='" . sprintf( __('View version %s details', 'wpmudev'), $remote_version ) . "'>{$remote_version}</a></strong></td>";
				$rows .= "<td style='vertical-align:middle'>" . $upgrade_button . "</td>";
				$rows .= "</tr>";
				$rows .= "<tr class='wdv-changelog'><td colspan='5'>";
				$rows .= "<div class='wdv-view-link'><a href='#'>" . __('View Changes', 'wpmudev') . " <i class='icon-chevron-down'></i></a></div>";
				$rows .= "<div class='wdv-changelog-drop'>" . $project['changelog'];
				$rows .= "<div class='wdv-close-link'><a href='#'>" . __('Close', 'wpmudev') . " <i class='icon-chevron-up'></a></div>";
				$rows .= "</div></td></tr>";
				$class = ('alternate' == $class) ? '' : 'alternate';
				//=========================================================//
			}
		} else {
			$rows .= '<tr><td colspan="5">' . __('No WPMU DEV theme updates required', 'wpmudev') . '</td></tr>';
		}
		
		echo '<h3>' . __('WPMU DEV Theme Updates', 'wpmudev');
		if (count($form_fields) >= 2) {
			echo implode("\n", $form_fields);
			wp_nonce_field('upgrade-core');
			echo "<a href='#' class='button-secondary upgrade-all'><i class='icon-upload-alt'></i> ".__('Update All Themes', 'wpmudev')."</a>";
		}
		echo '</h3>';
		
		echo "
			<table cellpadding='3' cellspacing='3' width='100%' class='widefat'>
			<thead><tr>
			<th scope='col'>".__('Name', 'wpmudev')."</th>
			<th scope='col'>".__('Links', 'wpmudev')."</th>
			<th scope='col'>".__('Installed Version', 'wpmudev')."</th>
			<th scope='col'>".__('Latest Version', 'wpmudev')."</th>
			<th scope='col'>".__('Actions', 'wpmudev')."</th>
			</tr></thead>
			<tbody id='the-list'>
			";
			
			echo $rows;
		?>
		</tbody></table>
		</form>
		
		<p><?php _e('Please note that all data is updated every 12 hours.', 'wpmudev') ?> <?php _e('Last updated:', 'wpmudev'); ?> <?php echo get_date_from_gmt(date('Y-m-d H:i:s', $last_run), get_option('date_format') . ' ' . get_option('time_format')); ?> - <a id="refresh-link" href="<?php echo $this->updates_url; ?>&action=update"><i class='icon-refresh'></i> <?php _e('Update Now', 'wpmudev'); ?></a></p>
		<?php
		break;			
	
	case "installed":
		?>				
		<h2><?php _e('WPMU DEV Installed', 'wpmudev') ?></h2>
		<?php
		$projects = array();
		if ( is_array( $data ) ) {
			$remote_projects = isset($data['projects']) ? $data['projects'] : array();
			$local_projects = $this->get_local_projects();
			if ( is_array( $local_projects ) ) {
				foreach ( $local_projects as $local_id => $local_project ) {
					//skip if not in remote results
					if (!isset($remote_projects[$local_id]))
						continue;
					
					$type = $remote_projects[$local_id]['type'];
					
					$projects[$type][$local_id]['thumbnail'] = $remote_projects[$local_id]['thumbnail'];
					$projects[$type][$local_id]['name'] = $remote_projects[$local_id]['name'];
					$projects[$type][$local_id]['description'] = $remote_projects[$local_id]['short_description'];
					$projects[$type][$local_id]['url'] = $remote_projects[$local_id]['url'];
					$projects[$type][$local_id]['instructions_url'] = $remote_projects[$local_id]['instructions_url'];
					$projects[$type][$local_id]['support_url'] = $remote_projects[$local_id]['support_url'];
					$projects[$type][$local_id]['autoupdate'] = (($local_project['type'] == 'plugin' || $local_project['type'] == 'theme') && $this->get_apikey() && $allow_auto) ? $remote_projects[$local_id]['autoupdate'] : 0;

					//handle wp autoupgrades
					if ($projects[$type][$local_id]['autoupdate'] == '2') {
						if ($local_project['type'] == 'plugin') {
							$update_plugins = get_site_transient('update_plugins');
							if (isset($update_plugins->response[$local_project['filename']]->new_version))
								$projects[$type][$local_id]['remote_version'] = $update_plugins->response[$local_project['filename']]->new_version;
							else
								$projects[$type][$local_id]['remote_version'] = $local_project['version'];
						} else if ($local_project['type'] == 'theme') {
							$update_themes = get_site_transient('update_themes');
							if (isset($update_themes->response[$local_project['filename']]['new_version']))
								$projects[$type][$local_id]['remote_version'] = $update_themes->response[$local_project['filename']]['new_version'];
							else
								$projects[$type][$local_id]['remote_version'] = $local_project['version'];
						} else {
							$projects[$type][$local_id]['remote_version'] = $remote_projects[$local_id]['version'];
						}
					} else {
						$projects[$type][$local_id]['remote_version'] = $remote_projects[$local_id]['version'];
					}

					$projects[$type][$local_id]['local_version'] = $local_project['version'];
					$projects[$type][$local_id]['filename'] = $local_project['filename'];
					$projects[$type][$local_id]['type'] = $local_project['type'];
				}
			}
		}
		?>
		<p><?php _e('Here you can find a list of the WPMU DEV plugins and themes installed on this server, along with quick links to documentation and support for each.', 'wpmudev') ?></p>
		
		<h3><?php _e('Installed WPMU DEV Plugins', 'wpmudev') ?></h3>
		<?php
		echo "
			<table cellpadding='3' cellspacing='3' width='100%' class='widefat'>
			<thead><tr>
			<th scope='col'>".__('Name', 'wpmudev')."</th>
			<th scope='col'>".__('Links', 'wpmudev')."</th>
			<th scope='col'>".__('Installed Version', 'wpmudev')."</th>
			<th scope='col'>".__('Latest Version', 'wpmudev')."</th>
			<th scope='col'>".__('Actions', 'wpmudev')."</th>
			</tr></thead>
			<tbody id='the-list'>
			";
		
		if (is_array($projects['plugin']) && count($projects['plugin']) > 0) {
			$class = (isset($class) && 'alternate' == $class) ? '' : 'alternate';
			foreach ($projects['plugin'] as $project_id => $project) {
				$local_version = $project['local_version'];
				$remote_version = $project['remote_version'];

				$check = (version_compare($remote_version, $local_version, '>')) ? "style='background-color:#EFF7FF;'" : '';

				if ( $project['autoupdate'] && $project['type'] == 'plugin' )
					$upgrade_button_code = "<a href='" . wp_nonce_url( $this->self_admin_url('update.php?action=upgrade-plugin&plugin=') . $project['filename'], 'upgrade-plugin_' . $project['filename']) . "' class='button-secondary'><i class='icon-upload-alt'></i> ".__('Auto Update', 'wpmudev')."</a>";
				else if ( $project['autoupdate'] && $project['type'] == 'theme' )
					$upgrade_button_code = "<a href='" . wp_nonce_url( $this->self_admin_url('update.php?action=upgrade-theme&theme=') . $project['filename'], 'upgrade-theme_' . $project['filename']) . "' class='button-secondary'><i class='icon-upload-alt'></i> ".__('Auto Update', 'wpmudev')."</a>";
				else
					$upgrade_button_code = "<a href='" . $project['url'] . "' class='button-secondary' target='_blank'><i class='icon-download-alt'></i> ".__('Download Update', 'wpmudev')."</a>";

				$upgrade_button = (version_compare($remote_version, $local_version, '>')) ? $upgrade_button_code : '';

				$screenshot = $project['thumbnail'];

				//=========================================================//
				echo "<tr class='" . $class . "' " . $check . " >";
				echo "<td style='vertical-align:middle'><img src='$screenshot' width='70' height='45' style='float:left; padding: 5px' /></a><strong><a href='{$this->server_url}?action=description&id={$project_id}&TB_iframe=true&width=640&height=800' class='thickbox' title='" . sprintf( __('%s Details', 'wpmudev'), $project['name'] ) . "'>{$project['name']}</a></strong><br />{$project['description']}</td>";
				echo "<td style='vertical-align:middle;width:200px;'><a href='{$this->server_url}?action=help&id={$project_id}&TB_iframe=true&width=640&height=800' class='thickbox' title='" . sprintf( __('%s Installation & Use Instructions', 'wpmudev'), $project['name'] ) . "'><i class='icon-info-sign'></i> " . __('Installation & Use Instructions', 'wpmudev') . "</a><br /><a target='_blank' href='{$project['support_url']}'><i class='icon-question-sign'></i> " . __('Get Support', 'wpmudev') . "</a></td>";
				echo "<td style='vertical-align:middle'><strong>" . $local_version . "</strong></td>";
				echo "<td style='vertical-align:middle'><strong><a href='{$this->server_url}?action=details&id={$project_id}&TB_iframe=true&width=640&height=800' class='thickbox' title='" . sprintf( __('View version %s details', 'wpmudev'), $remote_version ) . "'>{$remote_version}</a></strong></td>";
				echo "<td style='vertical-align:middle'>" . $upgrade_button . "</td>";
				echo "</tr>";
				$class = ('alternate' == $class) ? '' : 'alternate';
				//=========================================================//
			}
		} else {
			?><tr><td colspan="5"><?php _e('No installed WPMU DEV plugins', 'wpmudev') ?></td></tr><?php	
		}
		?>
		</tbody></table>
			
		<h3><?php _e('Installed WPMU DEV Themes', 'wpmudev') ?></h3>
		<?php
		echo "
			<table cellpadding='3' cellspacing='3' width='100%' class='widefat'>
			<thead><tr>
			<th scope='col'>".__('Name', 'wpmudev')."</th>
			<th scope='col'>".__('Links', 'wpmudev')."</th>
			<th scope='col'>".__('Installed Version', 'wpmudev')."</th>
			<th scope='col'>".__('Latest Version', 'wpmudev')."</th>
			<th scope='col'>".__('Actions', 'wpmudev')."</th>
			</tr></thead>
			<tbody id='the-list'>
			";
		
		if (is_array($projects['theme']) && count($projects['theme']) > 0) {
			$class = (isset($class) && 'alternate' == $class) ? '' : 'alternate';
			foreach ($projects['theme'] as $project_id => $project) {
				$local_version = $project['local_version'];
				$remote_version = $project['remote_version'];

				$check = (version_compare($remote_version, $local_version, '>')) ? "style='background-color:#EFF7FF;'" : '';

				if ( $project['autoupdate'] && $project['type'] == 'plugin' )
					$upgrade_button_code = "<a href='" . wp_nonce_url( $this->self_admin_url('update.php?action=upgrade-plugin&plugin=') . $project['filename'], 'upgrade-plugin_' . $project['filename']) . "' class='button-secondary'><i class='icon-upload-alt'></i> ".__('Auto Update', 'wpmudev')."</a>";
				else if ( $project['autoupdate'] && $project['type'] == 'theme' )
					$upgrade_button_code = "<a href='" . wp_nonce_url( $this->self_admin_url('update.php?action=upgrade-theme&theme=') . $project['filename'], 'upgrade-theme_' . $project['filename']) . "' class='button-secondary'><i class='icon-upload-alt'></i> ".__('Auto Update', 'wpmudev')."</a>";
				else
					$upgrade_button_code = "<a href='" . $project['url'] . "' class='button-secondary' target='_blank'><i class='icon-download-alt'></i> ".__('Download Update', 'wpmudev')."&raquo;</a>";

				$upgrade_button = (version_compare($remote_version, $local_version, '>')) ? $upgrade_button_code : '';

				$screenshot = $project['thumbnail'];

				//=========================================================//
				echo "<tr class='" . $class . "' " . $check . " >";
				echo "<td style='vertical-align:middle'><img src='$screenshot' width='70' height='45' style='float:left; padding: 5px' /></a><strong><a href='{$this->server_url}?action=description&id={$project_id}&TB_iframe=true&width=640&height=800' class='thickbox' title='" . sprintf( __('%s Details', 'wpmudev'), $project['name'] ) . "'>{$project['name']}</a></strong><br />{$project['description']}</td>";
				echo "<td style='vertical-align:middle;width:200px;'><a href='{$this->server_url}?action=help&id={$project_id}&TB_iframe=true&width=640&height=800' class='thickbox' title='" . sprintf( __('%s Installation & Use Instructions', 'wpmudev'), $project['name'] ) . "'><i class='icon-info-sign'></i> " . __('Installation & Use Instructions', 'wpmudev') . "</a><br /><a target='_blank' href='{$project['support_url']}'><i class='icon-question-sign'></i> " . __('Get Support', 'wpmudev') . "</a></td>";
				echo "<td style='vertical-align:middle'><strong>" . $local_version . "</strong></td>";
				echo "<td style='vertical-align:middle'><strong><a href='{$this->server_url}?action=details&id={$project_id}&TB_iframe=true&width=640&height=800' class='thickbox' title='" . sprintf( __('View version %s details', 'wpmudev'), $remote_version ) . "'>{$remote_version}</a></strong></td>";
				echo "<td style='vertical-align:middle'>" . $upgrade_button . "</td>";
				echo "</tr>";
				$class = ('alternate' == $class) ? '' : 'alternate';
				//=========================================================//
			}
		} else {
			?><tr><td colspan="5"><?php _e('No installed WPMU DEV themes', 'wpmudev') ?></td></tr><?php	
		}
		?>
		</tbody></table>
		
		<p><small>* <?php _e('Installed plugins and themes above only refer to those provided to', 'wpmudev') ?> <a href="http://premium.wpmudev.org/"><?php _e('WPMU DEV members'); ?></a> <?php _e('by Incsub - other plugins and themes are not included here.', 'wpmudev'); ?></small></p>
		<?php
		break;
}
?>
</div>
</div>