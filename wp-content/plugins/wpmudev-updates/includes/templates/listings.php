<hr class="section-head-divider" />
<div class="wrap grid_container">
	<h1 class="section-header"><i class="icon-th-list"></i><?php echo $page_title; ?></h1>
	<div class="listing-form-elements">
		<table cellpadding="0" cellspacing="0" border="0">
			<tbody>
				<tr>
					<td width="25%">
						<label><?php _e('Sort:', 'wpmudev'); ?> 
						<select id="sort_projects">
							<option value="released"><?php _e('Release date', 'wpmudev'); ?></option>
							<option value="updated"><?php _e('Recently updated', 'wpmudev'); ?></option>
							<option value="popularity"><?php _e('Popularity', 'wpmudev'); ?></option>
							<option value="downloads"><?php _e('Downloads', 'wpmudev'); ?></option>
							<option value="alphabetical"><?php _e('Alphabetically', 'wpmudev'); ?></option>
						</select>
						</label>
					</td>
					<td width="25%">
						<label><?php _e('Filter by Tag:', 'wpmudev'); ?> 
						<select id="filter_tags">
							<option value=""><?php _e('-- All Tags --', 'wpmudev');?></option>
						<?php foreach ($tags as $key => $tag) { ?>
							<option value="<?php echo $key; ?>"><?php echo $tag['name']; ?> (<?php echo number_format_i18n($tag['count']); ?>)</option>
						<?php } ?>
						</select>
						</label>
					</td>
					<td width="25%">
						<label><?php _e('Instant Search:', 'wpmudev'); ?>
						<input type="text" id="filter_projects" placeholder="<?php _e('Search', 'wpmudev'); ?>" /><a href="#" id="clear_search" title="<?php _e('Clear Search', 'wpmudev'); ?>" class="search-btn"><i class="icon-remove-sign icon-large"></i></a>
						</label>
					</td>
					<td width="5%">
						<label><?php _e('Results:', 'wpmudev'); ?></label> <h1 id="results-count"></h1>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<?php
if ( $this->get_apikey() && ($data['membership'] == 'full' || is_numeric($data['membership'])) && isset($data['downloads']) && $data['downloads'] != 'enabled' ) {
	?><div class="registered_error"><p><?php _e('You have reached your maximum enabled sites for one-click installations. You may <a href="http://premium.wpmudev.org/wp-admin/profile.php?page=wdpun">change which sites are enabled or upgrade to a higher membership level here &raquo;</a>', 'wpmudev'); ?></p></div><?php
}
?>

<?php if (!$this->get_apikey()) { ?>
	<div class="registered_error"><p><?php printf(__('Please, <a href="%s">set your API key</a> to enable one-click downloads and installation.', 'wpmudev'), $this->dashboard_url); ?></p></div>
<?php } ?>

<div style="display:none" id="_installed-placeholder"><span href="#" class="button"><i class="icon-ok icon-large"></i><?php echo (is_multisite() || $page_type == 'theme' || defined('WPMUDEV_NO_AUTOACTIVATE')) ? __('INSTALLED', 'wpmudev') : __('INSTALLED & ACTIVATED', 'wpmudev'); ?></span></div>
<div style="display:none" id="_install_error-placeholder">
	<span href="#" class="button error"> 
		<span class="tooltip">
			<section>Error Details</section>
			<i class='icon-question-sign'></i>
		</span>
		<i class="icon-warning-sign icon-large"></i><?php _e('ERROR', 'wpmudev'); ?>
	</span> 
</div>

<?php if (!$this->_install_message_is_hidden()) { ?>
	<div style="display:none" id="_install_setup-wrapper">
		<a href="#" class="_install_setup-close"><i class='icon-remove'></i> <?php _e('close', 'wpmudev'); ?></a>
		<div>
		<p class="intro">
			<?php _e("Hang on a minute... It looks like your WordPress site isn't configured to allow one-click installations of plugins and themes.", 'wpmudev'); ?>		
		</p>
		<p>
			<?php _e('You may still install this plugin using the manual process (by you entering your FTP credentials in the next step), or you can easily set up your site to do it automatically from now on.', 'wpmudev'); ?>
		</p>
		<br class="clear" />
		</div>
		<div>
			<span class="target"><a href="#" class="button install_plugin"><i class="icon-download-alt icon-large"></i><?php _e('MANUAL INSTALL', 'wpmudev'); ?></a></span> 
			<a href="#" class="button install_instructions"><i class="icon-question-sign icon-large"></i><?php _e('Setup one-click installation', 'wpmudev'); ?></a>
		</div>
		<label><input type="checkbox" id="_install_hide_msg" name="install_hide_msg" /> <?php _e('hide this message in future', 'wpmudev'); ?></label>
	</div>

	<div style="display:none" id="_install_setup-auto_install-wrapper">
		<a href="#" class="_install_setup-close"><i class='icon-remove'></i> <?php _e('close', 'wpmudev'); ?></a>
		<p><?php _e('You can set up one-click installations by adding these lines to your <code>wp-config.php</code> file and customizing:', 'wpmudev'); ?></p>
		<code>define('FTP_USER', 'username');</code><br />
		<code>define('FTP_PASS', 'password');</code><br />
		<code>define('FTP_HOST', 'ftp.example.org');</code>
		<br /><br />
		<a href="http://codex.wordpress.org/Editing_wp-config.php#WordPress_Upgrade_Constants" target="_blank" class="_install_setup-info"><i class='icon-info-sign'></i> <?php _e('More information', 'wpmudev'); ?></a>
	</div>
<?php } ?>

<div class="listings-container grid_container">
	<div class="listings">
		<div class="listing-divider"></div>
		<div class="listing-divider2"></div>
		<ul data-page_type="<?php echo $page_type; ?>">
		<?php if (isset($data['projects']) && is_array($data['projects'])) foreach ($data['projects'] as $project) { ?>
			<?php
			if ($page_type != $project['type']) continue;
			//skip multisite only products if not compatible
			if ($project['requires'] == 'ms' && !is_multisite()) continue;
			//skip buddypress only products if not active
			if ($project['requires'] == 'bp' && !defined( 'BP_VERSION' )) continue;
			//skip lite products if full member
			if (isset($data['membership']) && $data['membership'] == 'full' && $project['paid'] == 'lite') continue;
			
			//installed?
			$installed = (isset($local_projects[$project['id']])) ? true : false;
			
			$action_class = '';
			if ('plugin' == $project['type']) {			
				$action_class = $this->_can_auto_download_project($project['type'])
					? (((is_multisite() && is_network_admin()) || defined('WPMUDEV_NO_AUTOACTIVATE')) ? 'install_plugin' : 'install_and_activate_plugin')
					: ($this->_install_message_is_hidden() ? '' : 'install_setup')
				;
			} else {
				$action_class = $this->_can_auto_download_project($project['type']) 
					? 'install_theme' 
					: ($this->_install_message_is_hidden() ? '' : 'install_setup')
				;
			}
			?>
			<li class="listing-item" title="<?php _e('More Info &raquo;', 'wpmudev'); ?>" data-project_id="<?php echo $project['id']; ?>" data-released="<?php echo $project['released']; ?>" data-updated="<?php echo $project['updated']; ?>" data-downloads="<?php echo $project['downloads']; ?>" data-popularity="<?php echo $project['popularity']; ?>">
				<div class="listing">
					<img src="<?php echo $project['thumbnail']; ?>" alt="<?php echo esc_attr($project['name']); ?>" width="100%">
					<h1><?php echo $project['name']; ?></h1>
					<p><?php echo substr($project['short_description'], 0, 120); ?>&hellip;  <a href="<?php echo $project['url']; ?>"><?php _e('Learn more', 'wpmudev'); ?></a></p>
					<span class="full-excerpt" style="display:none;"><?php echo esc_attr($project['short_description']); ?></span>
					<span class="project_tags" style="display:none">
					<?php 
						$project_tags = array();
						foreach ($tags as $tag) { 
							if (in_array($project['id'], $tag['pids'])) $project_tags[] = $tag['name'];
						}
						if ($project_tags) echo join(', ', $project_tags);
					?>
					</span>
				</div>
				<div class="install_wrap">
					<span class="target">
					<?php
					if ($installed) {
						?><span href="#" class="button"><i class="icon-ok icon-large"></i><?php _e('INSTALLED', 'wpmudev'); ?></span><?php
					} else if ($url = $this->auto_install_url($project['id'])) {
						?><a href="<?php echo $url; ?>" data-downloading="<?php esc_attr_e(__('DOWNLOADING...', 'wpmudev')); ?>" data-installing="<?php esc_attr_e(__('INSTALLING...', 'wpmudev')); ?>" class="button <?php echo $action_class; ?>"><i class="icon-download-alt icon-large"></i><?php _e('INSTALL', 'wpmudev'); ?></a><?php
					} else {
						?><a href="<?php echo esc_url($project['url']); ?>" target="_blank" class="button"><i class="icon-download icon-large"></i><?php _e('DOWNLOAD', 'wpmudev'); ?></a><?php
					}
					?>
					</span>
					<div class="listing-hr"></div>
				</div>
			</li>
		<?php } ?>
		</ul>
		<div id="no-results">
			<h1><?php _e('No Results', 'wpmudev'); ?></h1>
			<p><?php _e('Please change or <a href="#" title="Clear Filters">clear</a> your search filters above', 'wpmudev'); ?></p>
		</div>
	</div>	
</div>

<div id="listing-details-container" class="listing-details-wrapper" style="display:none;">
	<div class="listing-details-container grid_container">
		<div class="listing-details-overlay grid_container">
			<div class="overlay-details">
				<div class="screenshot-container">
					<img />
				</div>
				<div class="screenshot-description">
					<span class="image-of">1 / 4</span>
					<br />
					<p class="screenshot-description"></p>
					<div class="screenshot-nav">
						<a href="#" class="faded"><i class="icon-chevron-left icon-large"></i></a><a href="#"><i class="icon-chevron-right icon-large"></i></a>
					</div>
				</div>
				<a class="symbol" href="#"><i class="icon-remove icon-large"></i></a>
			</div>
		</div>
		<div class="listing-details-content">
			<div class="listing-copy">
				<h1 id="listing-title"></h1>
				<h3 id="listing-excerpt"></h3>
				<div id="listing-description"></div>
				<div id="loading-details"><?php _e('Loading...', 'wpmudev'); ?></div>
				<div class="desc-links">
					<span id="listing-install">
						<a class="button" href="#"><i class="icon-download-alt icon-large"></i> ACTION</a>
					</span> <?php _e('or', 'wpmudev'); ?> <a id="listing-readmore" href="#" target="_blank"><?php _e('Read more on WPMU DEV &raquo;', 'wpmudev'); ?></a>
				</div>
			</div>
			<div class="listing-screens">
				<span><a class="close-plugin-details" href="#"><?php _e('close plugin info', 'wpmudev'); ?> <i class="icon-remove icon-large"></i></a></span>
				<ul>
					<li>
						
						<div></div>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>