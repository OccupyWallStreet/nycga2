<hr class="section-head-divider" />
<div class="wrap grid_container">
	<h1 class="section-header">
		<i class="icon-question-sign"></i><?php _e('Support', 'wpmudev') ?>
	</h1>
	<div class="listing-form-elements">
		<table cellpadding="0" cellspacing="0" border="0">
			<tbody>
				<tr>
					<td width="48%" align="center">&nbsp;</td>
					<td width="4.8%">&nbsp;</td>
					<td width="47%"><input type="text" id="search_projects" placeholder="<?php _e('Search support', 'wpmudev') ?>" /><a id="forum-search-go" href="#" class="search-btn"><i class="icon-search"></i></a></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<div class="support-container grid_container">
	<div class="ask-question-container">

		<div id="success_ajax" style="display:none;">
			<h1><i class="icon-ok"></i> <?php _e("Success!", 'wpmudev') ?></h1>
			<p><?php _e("Thanks for contacting Support, we'll get back to you as soon as possible.", 'wpmudev'); ?></p>
			<p><a href="#" target="_blank"><?php _e('You can view or add to your support request here &raquo;', 'wpmudev'); ?></a></p>
		</div>
		
		<form id="qa-form" method="post" enctype="multipart/form-data" action="">
		<h1><?php _e("Question? Bug? Feature request? Let's see how we can help.", 'wpmudev') ?></h1>
	
		<table id="qa-table" cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
				<td>
					<?php if ( $this->get_apikey() && ($data['membership'] == 'full' || is_numeric($data['membership'])) && isset($data['downloads']) && $data['downloads'] != 'enabled' ) { ?>
					<div class="error fade"><p><?php _e('You have reached your maximum enabled sites for direct dashboard support. You may <a href="http://premium.wpmudev.org/wp-admin/profile.php?page=wdpun">change which sites are enabled or upgrade to a higher membership level here &raquo;</a>', 'wpmudev'); ?></p></div>
					<?php } else if (!$this->allowed_user()) {
						$user_info = get_userdata( get_site_option('wdp_un_limit_to_user') );
					?>
					<div class="error fade"><p><?php printf(__('Only the admin user "%s" has access to WPMU DEV support.', 'wpmudev'), $user_info->display_name); ?></p></div>
					<?php } else if ($disabled && $this->get_apikey()) { ?>
					<div class="error fade"><p><i class="icon-warning-sign icon-large"></i> <?php _e('Only paid WPMU DEV members have access to WPMU DEV support.', 'wpmudev'); ?> <a href="https://premium.wpmudev.org/join/" target="_blank"><?php _e('Upgrade now &raquo;', 'wpmudev'); ?></a></p></div>
					<?php } ?>
					
					<div id="error_topic" style="display:none;" class="error fade"><p><i class="icon-warning-sign icon-large"></i> <?php _e('Please enter your question title.', 'wpmudev'); ?></p></div>
					<div id="error_ajax" style="display:none;" class="error fade"><p><i class="icon-warning-sign icon-large"></i> <?php _e('There was a problem posting your support question:', 'wpmudev'); ?> <span></span></p></div>
					<input type="text" id="topic" name="topic" placeholder="<?php _e("What's your question or topic? Be specific please :)", 'wpmudev') ?>"<?php echo $disabled; ?> />
				</td>
			</tr>
			<tr>
				<td>
					<div id="error_project" style="display:none;" class="error fade"><p><i class="icon-warning-sign icon-large"></i> <?php _e('Please select what you need support for.', 'wpmudev'); ?></p></div>
					<select id="q-and-a" name="project_id">
						<option value=""><?php _e('Select an Installed Product:', 'wpmudev') ?></option>
						<?php
						$projects = $this->get_local_projects();
						$data = $this->get_updates();
						$forum = isset( $_GET['forum'] ) ? (int)$_GET['forum'] : false;
						$plugins = '';
						$themes = '';
						foreach ($projects as $pid => $project) {
							if (isset($data['projects'][$pid])) {
								if ($data['projects'][$pid]['type'] == 'plugin')
									$plugins .= '<option value="'.$pid.'"'.$disabled.'>'.esc_attr($data['projects'][$pid]['name'])."</option>\n";
								else if ($data['projects'][$pid]['type'] == 'theme')
									$themes .= '<option value="'.$pid.'"'.$disabled.'>'.esc_attr($data['projects'][$pid]['name'])."</option>\n";
							}
						}
						if ($plugins) {
							echo '<optgroup forum_id="1" label="'.__('Plugins:', 'wpmudev').'">' . $plugins . '</optgroup>';
						}
						if ($themes) {
							echo '<optgroup forum_id="2" label="'.__('Themes:', 'wpmudev').'">' . $themes . '</optgroup>';
						}
						?>
						<optgroup label="<?php _e('General Topic:', 'wpmudev'); ?>">
							<option forum_id="11" value=""<?php echo $disabled; selected($forum, 11); ?>><?php _e('General', 'wpmudev'); ?></option>
							<option forum_id="10" value=""<?php echo $disabled; selected($forum, 10); ?>><?php _e('BuddyPress', 'wpmudev'); ?></option>
							<option forum_id="8" value=""<?php echo $disabled; selected($forum, 8); ?>><?php _e('Beginners WordPress Discussion', 'wpmudev'); ?></option>
							<option forum_id="7" value=""<?php echo $disabled; selected($forum, 7); ?>><?php _e('Advanced WordPress Discussion', 'wpmudev'); ?></option>
							<option forum_id="5" value=""<?php echo $disabled; selected($forum, 5); ?>><?php _e('Feature Suggestions &amp; Feedback', 'wpmudev'); ?></option>
						</optgroup>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<div id="error_content" style="display:none;" class="error fade"><p><i class="icon-warning-sign icon-large"></i> <?php _e('Please enter your support question.', 'wpmudev'); ?></p></div>
					<textarea rows="20" id="post_content" name="post_content" placeholder="<?php _e("Ok, go for it...", 'wpmudev') ?>"<?php echo $disabled; ?>></textarea>
					<p class="caution-note"><i class="icon-info-sign"></i> <?php _e("Please don't share any private information (passwords, API keys, etc.) here, support staff will ask for these via email if they are required.", 'wpmudev') ?></p>
					<label id="notify-me"><input type="checkbox" checked="checked" value="1" name="stt_checkbox"<?php echo $disabled; ?> /> <?php _e("Notify me of responses via email", 'wpmudev') ?></label>
				</td>
			</tr>
			<tr>
				<td>
					<?php if (!$this->get_apikey()) { ?>
					<a class="button" href="admin.php?page=wpmudev"><i class="icon-pencil icon-large"></i><?php _e("Enter your API Key for support", 'wpmudev') ?></a>
					<?php } else if (!$disabled) { ?>
					<a id="qa-submit" class="button"><i class="icon-play-circle icon-large"></i><?php _e("Post your question", 'wpmudev') ?></a>
					<?php } ?>
					<span id="qa-posting" class="button" style="display:none;"><img src="<?php echo $spinner; ?>" /> <?php _e("Posting question...", 'wpmudev') ?></span>
				</td>
			</tr>
		</table>
		<input type="hidden" value="1" id="forum_id" name="forum_id">
		</form>
		<img src="<?php echo $spinner; ?>" width="1" height="1" /><!-- preload -->
	</div>

	<div class="your-latest-q-and-a" >
		
		<section class="recent-activity-widget">
			<ul>
				<li class="accordion-title">
					<p><?php _e('BEFORE YOU POST:', 'wpmudev'); ?> <a href="#" class="ui-hide-link"><span><?php echo ($hide_tips) ? __('SHOW', 'wpmudev') : __('HIDE', 'wpmudev'); ?></span><span class="ui-hide-triangle<?php echo ($hide_tips) ? ' ui-show-triangle' : ''; ?>"></span></a></p>
					<ul<?php echo ($hide_tips) ? ' style="display:none;"' : ''; ?>>
						<li id="qa-tips">
							<h3><?php _e("Here are some ways you can solve your problem right now!", 'wpmudev'); ?></h3>
							<ol>
								<li><?php _e("Is your WP version up to date? All of our plugins and themes are built to work with the latest version of WordPress... upgrade.", 'wpmudev'); ?></li>
								<li><?php _e("Is the plugin/theme up to date? We're always updating our products, so <a href='admin.php?page=wpmudev-updates'>make sure</a> you are using the latest version, if not... upgrade.", 'wpmudev'); ?></li>
								<li><?php _e("Have you read the 'Usage' instructions? Every WPMU DEV product has a 'Usage' section, have you <a href='admin.php?page=wpmudev-updates&tab=installed'>had a look there</a>, it's like a mini manual!", 'wpmudev'); ?></li>
								<li><?php _e("Have you searched? Have a search using the form above, your question has probably come up before.", 'wpmudev'); ?></li>
							</ol>
							<h3><?php _e("And if you're feeling a bit more technical:", 'wpmudev'); ?></h3>
							<ol>
								<li><?php _e("Disable and re-activate the plugin - you'd be amazed how many problems this solves.", 'wpmudev'); ?></li>
								<li><?php _e("Check for a plugin conflict - try disabling other plugins and see if that fixes it... if it does, let us know and we'll help you out.", 'wpmudev'); ?></li>
								<li><?php _e("Check for a theme conflict - try another theme (like Twenty Eleven) and see if it fixes it... if it does, let us know and we'll help you out.", 'wpmudev'); ?></li>
							</ol>
						</li>
					</ul>
				</li>
			</ul>
		</section>
		
		<section class="recent-activity-widget">
			<ul>
				<li class="accordion-title">
					<p><?php _e('JOIN A LIVE CHAT:', 'wpmudev'); ?> <a href="#" class="ui-hide-link"><span><?php _e('HIDE', 'wpmudev'); ?></span><span class="ui-hide-triangle"></span></a></p>
					<ul>
						<li><i class="icon-external-link"></i> <a target="_blank" href="http://premium.wpmudev.org/live-support/"><?php _e('View scheduled support chats', 'wpmudev'); ?></a></li>
					</ul>
				</li>
			</ul>
		</section>
		
		<section class="recent-activity-widget">
			<ul>
				<li class="accordion-title">
					<p><?php _e('YOUR LATEST Q&A ACTIVITY:', 'wpmudev'); ?> <a href="#" class="ui-hide-link"><span><?php _e('HIDE', 'wpmudev'); ?></span><span class="ui-hide-triangle"></span></a></p>
					<ul>
					<?php if (isset($profile['forum']['support_threads'])) foreach ($profile['forum']['support_threads'] as $thread) { ?>
						<li>
							<?php if ($thread['status'] == 'resolved') { ?>
							<i class="icon-ok-sign icon-large resolved" title="<?php _e('Resolved', 'wpmudev'); ?>"></i>
							<?php } else { ?>
							
							<?php } ?> 
							<a href="<?php echo $thread['link'];?>" target="_blank"><?php echo $thread['title'];?></a>
						</li>
					<?php } else if (!$this->get_apikey()) { ?>
						<li><i class="icon-pencil"></i> <a href="admin.php?page=wpmudev"><?php _e('Enter your API key to show activity', 'wpmudev'); ?></a></li>
					<?php } else { ?>
						<li><?php _e('No support activity yet.', 'wpmudev'); ?></li>
					<?php } ?>
					</ul>
				</li>
			</ul>
		</section>
		
	</div>
</div>