<?php
	$profile = $this->get_profile();
?>
<hr class="section-head-divider" />
<div class="wrap grid_container">
	<h1 class="section-header">
		<i class="icon-comments"></i><?php _e('Community', 'wpmudev') ?>
	</h1>
	<div class="listing-form-elements">
		<table cellpadding="0" cellspacing="0" border="0">
			<tbody>
				<tr>
					<td width="100%"><input type="text" id="forum-search-q" placeholder="<?php _e('Search community', 'wpmudev'); ?>" /><a href="#" id="forum-search-go" class="search-btn"><i class="icon-search"></i></a></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<div class="community-container grid_container">
	<div class="community-content">
		
		<?php  if (isset($profile['reputation']['overall']) && $this->allowed_user()) { ?>
		<div class="community-reputation">
			<h1><?php _e('Your reputation:', 'wpmudev'); ?></h1>
			<?php  if (isset($profile['reputation']['overall']) && isset($profile['reputation']['unique_users'])) { ?>
				
				<!-- user rep level -->
				<?php  if (0 == $profile['reputation']['overall']) { ?>
				<span class="profile-reputation-badge"><section><?php _e('Brand new here', 'wpmudev'); ?></section></span>
				<?php } else if ($profile['reputation']['overall'] > 0 && $profile['reputation']['overall'] <= 10) { ?>
				<span class="profile-reputation-badge ul-10"><section><?php _e('Getting my WPMU DEV Wings', 'wpmudev'); ?></section></span>
				<?php } else if ($profile['reputation']['overall'] > 10 && $profile['reputation']['overall'] <= 50) { ?>
				<span class="profile-reputation-badge ul-50"><section><?php _e('Starting to get into this DEV thing', 'wpmudev'); ?></section></span>
				<?php } else if ($profile['reputation']['overall'] > 50 && $profile['reputation']['overall'] <= 200) { ?>
				<span class="profile-reputation-badge ul-200"><section><?php _e('Serious WPMU DEV-ster', 'wpmudev'); ?></section></span>
				<?php } else if ($profile['reputation']['overall'] > 200 && $profile['reputation']['overall'] <= 500) { ?>
				<span class="profile-reputation-badge ul-500"><section><?php _e('WPMU DEV Expert', 'wpmudev'); ?>"</section></span>
				<?php } else if ($profile['reputation']['overall'] > 500) { ?>
				<span class="profile-reputation-badge ul-500plus"><section><?php _e('Like some sort of WPMU DEV God', 'wpmudev'); ?></section></span>
				<?php } ?>
			
				<!-- user help support level -->
				<?php if ($profile['reputation']['unique_users'] > 0 && $profile['reputation']['unique_users'] < 5) { ?>
				<span class="profile-reputation-badge us-5"><section><?php _e("I'm helpful", 'wpmudev'); ?>"</section></span>
				<?php } else if ($profile['reputation']['unique_users'] > 5 && $profile['reputation']['unique_users'] < 10) { ?>
				<span class="profile-reputation-badge us-10"><section><?php _e('Seriously helpful member', 'wpmudev'); ?></section></span>
				<?php } else if ($profile['reputation']['unique_users'] > 10 && $profile['reputation']['unique_users'] < 20) { ?>
				<span class="profile-reputation-badge us-20"><section><?php _e('Exceptionally helpful', 'wpmudev'); ?></section></span>
				<?php } else if ($profile['reputation']['unique_users'] > 20) { ?>
				<span class="profile-reputation-badge us-20plus"><section><?php _e('Mindblowingly helpful member', 'wpmudev'); ?></section></span>
				<?php } ?>
				<!-- end -->
				
				<?php if ($profile['reputation']['overall'] > 1000 && $profile['reputation']['unique_users'] >= 10) { ?>
				<span class="profile-reputation-badge lifetime"><section><?php _e('Lifetime WPMU Dev member', 'wpmudev'); ?></section></span>
				<?php } ?>
				<br class="clear" />
				<span class="rep-points"><i><?php _e('You currently have ', 'wpmudev'); ?><span class="number-of-points"><?php echo number_format_i18n($profile['reputation']['overall']); ?></span><?php _e(' reputation points', 'wpmudev'); ?></i></span>
				<br />
				<a href="http://premium.wpmudev.org/forums/profile/<?php echo $profile['profile']['user_name']; ?>/reputation" target="_blank" class="button"><i class="icon-list-alt icon-large"></i><?php _e('VIEW REPUTATION REPORT', 'wpmudev'); ?></a>
			<?php } ?>
			</div>
		<?php } ?>
		
		<div class="community-quote">
			<i><?php _e('Participate in our community discussions and earn reputation points. <b>1000 points</b> gets you a free lifetime <b>WPMU DEV</b> membership!', 'wpmudev'); ?></i>
		</div>
	</div>
	<div class="your-recent-topics">
		<h1><?php _e('Recent topics started by you:', 'wpmudev'); ?></h1>
		<table width="100%" class="your-recent-topics" border="0" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<td width="2.7%"> </td>
					<td width="49%" align="left"><?php _e('Topic:', 'wpmudev'); ?></td>
					<td width="13%" align="center"><?php _e('Responses:', 'wpmudev'); ?></td>
					<td width="32.6%" align="center"><?php _e('Latest response by:', 'wpmudev'); ?></td>
					<td width="2.7%"> </td>
				</tr>
			</thead>
			<tbody>
				<?php if (@$profile['forum']['personal_threads']) foreach ($profile['forum']['personal_threads'] as $thread) { ?>
				<tr>
					<td></td>
					<td align="left"><a href="<?php echo $thread['link'];?>" target="_blank"><?php echo $thread['title'];?></a></td>
					<td align="center"><?php echo number_format_i18n($thread['posts']); ?></td>
					<td align="center"><?php echo $thread['user'];?><br /><span><?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $thread['timestamp']);?></span></td>
					<td></td>
				</tr>
				<?php } else if (!$this->get_apikey()) { ?>
					<tr><td></td><td align="center" colspan="3"><a href="admin.php?page=wpmudev"><?php _e("Enter your free API key to show your recent topics", 'wpmudev'); ?></a></td><td></td></tr>
				<?php } else { ?>
					<tr><td></td><td align="center" colspan="3"><?php _e("You haven't started any topics yet.", 'wpmudev'); ?></td><td></td></tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<div class="most-popular-topics">
		<h1><?php _e('Latest topics:', 'wpmudev'); ?></h1>
		<?php $count = 1; if (@$profile['forum']['recent_threads']) foreach ($profile['forum']['recent_threads'] as $forum_id => $forum) { ?>
		<ul>
			<li><h1><?php echo $forum['title']; ?></h1><a href="<?php echo $this->support_url. '&forum=' . $forum_id; ?>" class="button"><i class="icon-comment icon-large"></i><?php _e('START A DISCUSSION', 'wpmudev'); ?></a>
				<ul <?php echo (1<$count ? 'style="display:none"' : '');?> >
					<li>
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
							<thead>
								<tr>
									<td width="2.7%"> </td>
									<td width="49%" align="left"><?php _e('Topic:', 'wpmudev'); ?></td>
									<td width="13%" align="center"><?php _e('Responses:', 'wpmudev'); ?></td>
									<td width="32.6%" align="center"><?php _e('Latest response by:', 'wpmudev'); ?></td>
									<td width="2.7%"> </td>
								</tr>
							</thead>
							<tbody>
							<?php unset($forum['title']); unset($forum['link']); foreach ($forum as $thread) { ?>
								<tr>
									<td></td>
									<td align="left"><a href="<?php echo $thread['link'];?>" target="_blank"><?php echo $thread['title'];?></a></td>
									<td align="center"><?php echo number_format_i18n($thread['posts']); ?></td>
									<td align="center"><?php echo $thread['user'];?><br /><span><?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $thread['timestamp']);?></span></td>
									<td></td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
					</li>
				</ul>
			</li>
		</ul>
			<?php $count++; ?>
		<?php } ?>
	</div>
</div>