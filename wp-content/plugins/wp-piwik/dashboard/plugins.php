<?php
/*********************************
	WP-Piwik::Stats:Plugins
**********************************/

	$aryConf['data'] = $this->callPiwikAPI(
			'UserSettings.getPlugin', 
			$aryConf['params']['period'], 
			$aryConf['params']['date'],
			$aryConf['params']['limit']
	);
	$aryConf['title'] = __('Plugins', 'wp-piwik');
	
	$aryOverview = $this->callPiwikAPI(
                'VisitsSummary.get',
                $aryConf['params']['period'],
                $aryConf['params']['date'],
                $aryConf['params']['limit']
        );

	$intTotalVisits = (isset($aryOverview['nb_visits'])?$aryOverview['nb_visits']:0);

	unset($aryOverview);
	
	if (isset($aryConf['data']['result']) && $aryConf['data']['result'] = 'error')
		echo '<strong>'.__('Piwik error', 'wp-piwik').':</strong> '.htmlentities($aryConf['data']['message'], ENT_QUOTES, 'utf-8');
	else {
/***************************************************************************/ ?>
<div class="table">
	<table class="widefat wp-piwik-table">
		<thead>
			<tr>
				<th><?php _e('Plugins', 'wp-piwik'); ?></th>
				<th class="n"><?php _e('Visits', 'wp-piwik'); ?></th>
				<th class="n"><?php _e('Percent', 'wp-piwik'); ?></th>
			</tr>
		</thead>
		<tbody>
<?php /************************************************************************/
	if (is_array($aryConf['data'])) foreach ($aryConf['data'] as $aryValues)
		echo '<tr><td>'.
				$aryValues['label'].
			'</td><td class="n">'.
				$aryValues['nb_visits'].
			'</td><td class="n">'.
				($intTotalVisits != 0?
					number_format(($aryValues['nb_visits']/$intTotalVisits*100),2):
					'0.00%'
				).
			'%</td></tr>';
	else echo '<tr><td colspan="3">'.__('No data available.', 'wp-piwik').'</td></tr>';
	unset($aryTmp);
/***************************************************************************/ ?>
		</tbody>
	</table>
</div>
<?php /************************************************************************/
	}