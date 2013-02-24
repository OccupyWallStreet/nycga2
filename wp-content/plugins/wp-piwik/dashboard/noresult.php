<?php
/*********************************
	WP-Piwik::Stats:SiteSearchNoResults
**********************************/

	$aryConf['data'] = $this->callPiwikAPI(
		'Actions.getSiteSearchNoResultKeywords',
		$aryConf['params']['period'],
		$aryConf['params']['date'],
		$aryConf['params']['limit']
	);
	$aryConf['title'] = __('Site Search without Results', 'wp-piwik');
	if (isset($aryConf['data']['result']) && $aryConf['data']['result'] = 'error')
		echo '<strong>'.__('Piwik error', 'wp-piwik').':</strong> '.htmlentities($aryConf['data']['message'], ENT_QUOTES, 'utf-8');
	else {
/***************************************************************************/ ?>
<table class="widefat">
	<thead>
		<tr><th><?php _e('Keyword', 'wp-piwik'); ?></th><th><?php _e('Requests', 'wp-piwik'); ?></th><th><?php _e('Bounced', 'wp-piwik'); ?></th></tr>
	</thead>
	<tbody>
<?php /************************************************************************/
	if (is_array($aryConf['data'])) foreach ($aryConf['data'] as $aryValues)
		echo '<tr><td>'.$aryValues['label'].'</td><td>'.$aryValues['nb_visits'].'</td><td>'.$aryValues['bounce_rate'].'</td></tr>';
	else echo '<tr><td colspan="2">'.__('No data available.', 'wp-piwik').'</td></tr>';
/***************************************************************************/ ?>
	</tbody>
</table>
<?php /************************************************************************/
	}