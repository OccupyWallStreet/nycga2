<tr>
	<td><a href="http://peepbo.de/board/viewforum.php?f=3"><?php _e('WP-Piwik support board','wp-piwik'); ?></a> (<?php _e('no registration required, English &amp; German','wp-piwik'); ?>)</td>	
</tr>
<tr>
	<td><a href="http://wordpress.org/tags/wp-piwik?forum_id=10"><?php _e('WordPress.org forum about WP-Piwik','wp-piwik'); ?></a> (<?php _e('WordPress.org registration required, English','wp-piwik'); ?>)</td>
</tr>
<tr>
	<td><?php _e('Please don\'t forget to vote the compatibility at the','wp-piwik'); ?> <a href="http://wordpress.org/extend/plugins/wp-piwik/">WordPress.org Plugin Directory</a>.</td>
</tr>
<tr>
	<td>
		<h3><?php _e('Debugging', 'wp-piwik'); ?></h3>
		<p><?php _e('Either allow_url_fopen has to be enabled <em>or</em> cURL has to be available:', 'wp-piwik'); ?></p>
		<ol>
			<li><?php 
				_e('cURL is','wp-piwik');
				echo ' <strong>'.(function_exists('curl_init')?'':__('not','wp-piwik')).' ';
				_e('available','wp-piwik');
			?></strong>.</li>
			<li><?php 
				_e('allow_url_fopen is','wp-piwik');
				echo ' <strong>'.(ini_get('allow_url_fopen')?'':__('not','wp-piwik')).' ';
				_e('enabled','wp-piwik');
			?></strong>.</li>
		</ol>
<?php if (!(empty(self::$aryGlobalSettings['piwik_token']) || empty(self::$aryGlobalSettings['piwik_url']))) { ?>
<?php 
	if (isset($_GET['mode'])) {
		switch ($_GET['mode']) {
			case 'testscript': 
				echo '<p><strong>'.__('Test script result','wp-piwik').'</strong></p>';
				self::loadTestscript();
			break;
			case 'reset':
				echo '<p class="wp-piwik-eyecatcher"><strong class="wp-piwik-error">'.__('Please confirm your reset request','wp-piwik').':</strong> <a href="?page=wp-piwik/wp-piwik.php&tab=support&mode=resetconfirmed">'.__('YES, please reset <strong>all</strong> WP-Piwik settings <strong>except</strong> auth token and Piwi URL.', 'wp-piwik').'</a></p>';
			break;
			case 'resetconfirmed':
				// Increase time limit before resetting
				set_time_limit(0);
				self::resetSettings();
				echo '<p class="wp-piwik-eyecatcher"><strong>'.__('WP-Piwik reset done','wp-piwik').'</strong></p>';
			default:
		} 
	}
?>
		<p><strong><?php _e('Get more debug information', 'wp-piwik'); ?>:</strong></p>
		<ol>
			<li><a href="?page=wp-piwik/wp-piwik.php&tab=support&mode=testscript"><?php _e('Run test script','wp-piwik'); ?></a></li>
			<li><a href="?page=wp-piwik/wp-piwik.php&tab=sitebrowser"><?php _e('Get site configuration details','wp-piwik'); ?></a></li>
			<li><a href="?page=wp-piwik/wp-piwik.php&tab=support&mode=reset"><?php _e('Reset WP-Piwik settings except auth token and Piwik URL','wp-piwik'); ?></a> (<?php _e('This will not affect Piwik itself. Resetting large networks may take some minutes.', 'wp-piwik'); ?>)</li>
		</ol>
<?php } else echo '<p>'.__('You have to enter your auth token and the Piwik URL before you can access more debug functions.', 'wp-piwik').'</p>'; ?>
	</td>
</tr>
<tr><td><h3><?php _e('Latest support threads on WordPress.org', 'wp-piwik'); ?></h3>
<?php 
	$arySupportThreads = self::readRSSFeed('http://wordpress.org/support/rss/plugin/wp-piwik');
	if (!empty($arySupportThreads)) {
		echo '<ol>';
		foreach ($arySupportThreads as $arySupportThread) echo '<li><a href="'.$arySupportThread['url'].'">'.$arySupportThread['title'].'</a></li>';
		echo '</ol>';
	}
?></td></tr>