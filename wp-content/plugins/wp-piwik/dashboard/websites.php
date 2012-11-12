<?php
/*********************************
	WP-Piwik::Stats:Websites
**********************************/

	$aryConf['data'] = $this->callPiwikAPI(
		'Referers.getWebsites',
		$aryConf['params']['period'],
		$aryConf['params']['date'],
		$aryConf['params']['limit']
	);
	$aryConf['title'] = __('Websites', 'wp-piwik');

	if (isset($aryConf['data']['result']) && $aryConf['data']['result'] = 'error')
		echo '<strong>'.__('Piwik error', 'wp-piwik').':</strong> '.htmlentities($aryConf['data']['message'], ENT_QUOTES, 'utf-8');
	else {
/***************************************************************************/ ?>
<table class="widefat">
	<thead>
		<tr>
			<th><?php _e('Website', 'wp-piwik'); ?></th>
			<th><?php _e('Unique', 'wp-piwik'); ?></th>
		</tr>
	</thead>
	<tbody>
<?php /************************************************************************/
	if (is_array($aryConf['data'])) foreach ($aryConf['data'] as $aryValues)
		echo '<tr><td>'.$aryValues['label'].'</td><td>'.$aryValues['nb_uniq_visitors'].'</td></tr>';
	else echo '<tr><td colspan="2">'.__('No data available.', 'wp-piwik').'</td></tr>';
/***************************************************************************/ ?>
	</tbody>
</table>
<?php /************************************************************************/
	}