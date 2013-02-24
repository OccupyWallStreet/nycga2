<?php 
/*********************************
	WP-Piwik::Stats:Overview
**********************************/
	$aryTmp = array(
		'bounce_count' => 0,
		'max_actions' => 0,
		'nb_actions' => 0,
		'nb_uniq_visitors' => 0,
		'nb_visits' => 0,
		'nb_visits_converted' => 0,
		'sum_visit_length' => 0,
		'bounce_rate' => 0,
		'nb_actions_per_visit' => 0,
		'avg_time_on_site' => 0
	);
	$aryConf['data'] = $this->callPiwikAPI(
		'VisitsSummary.get',
		$aryConf['params']['period'],
		$aryConf['params']['date'],
		$aryConf['params']['limit']
	);
	$aryConf['title'] = __('Overview', 'wp-piwik');

	if (isset($aryConf['data']['result']) && $aryConf['data']['result'] ='error')
		echo '<strong>'.__('Piwik error', 'wp-piwik').':</strong> '.htmlentities($aryConf['data']['message'], ENT_QUOTES, 'utf-8');
	else {
		if ($aryConf['params']['date'] == 'last30') {
			$intValCnt = 0;
			if (is_array($aryConf['data']))
				foreach ($aryConf['data'] as $aryDay) 
					foreach ($aryDay as $strKey => $strValue) {
						$intValCnt++;
						if (!in_array($strKey, array('max_actions','bounce_rate','nb_actions_per_visit','avg_time_on_site')))
							$aryTmp[$strKey] += $strValue;
						elseif ($aryTmp[$strKey] < $strValue)
							$aryTmp[$strKey] = $strValue;
					}
			$aryConf['data'] = $aryTmp;
			if ($intValCnt > 1 && $aryConf['data']['nb_visits'] >0) $aryConf['data']['bounce_rate'] = round($aryConf['data']['bounce_count']/$aryConf['data']['nb_visits']*100).'%';
		}
		if (empty($aryConf['data'])) $aryConf['data'] = $aryTmp;
/***************************************************************************/ ?>
<div class="table">
	<table class="widefat">
		<tbody>
<?php /************************************************************************/
		$strTime = 
			floor($aryConf['data']['sum_visit_length']/3600).'h '.
			floor(($aryConf['data']['sum_visit_length'] % 3600)/60).'m '.
			floor(($aryConf['data']['sum_visit_length'] % 3600) % 60).'s';
		$strAvgTime = 
			floor($aryConf['data']['avg_time_on_site']/3600).'h '.
			floor(($aryConf['data']['avg_time_on_site'] % 3600)/60).'m '.
			floor(($aryConf['data']['avg_time_on_site'] % 3600) % 60).'s';
		echo '<tr><td>'.__('Visitors', 'wp-piwik').':</td><td>'.$aryConf['data']['nb_visits'].'</td></tr>';
		echo '<tr><td>'.__('Unique visitors', 'wp-piwik').':</td><td>'.$aryConf['data']['nb_uniq_visitors'].'</td></tr>';
		echo '<tr><td>'.__('Page views', 'wp-piwik').':</td><td>'.$aryConf['data']['nb_actions'].' (&#216; '.$aryConf['data']['nb_actions_per_visit'].')</td></tr>';
		echo '<tr><td>'.__('Max. page views in one visit', 'wp-piwik').':</td><td>'.$aryConf['data']['max_actions'].'</td></tr>';
		echo '<tr><td>'.__('Total time spent', 'wp-piwik').':</td><td>'.$strTime.'</td></tr>';
		echo '<tr><td>'.__('Time/visit', 'wp-piwik').':</td><td>'.$strAvgTime.'</td></tr>';
		echo '<tr><td>'.__('Bounce count', 'wp-piwik').':</td><td>'.$aryConf['data']['bounce_count'].' ('.$aryConf['data']['bounce_rate'].')</td></tr>';
		if (self::$aryGlobalSettings['piwik_shortcut']) 
			echo '<tr><td>'.__('Shortcut', 'wp-piwik').':</td><td><a href="'.self::$aryGlobalSettings['piwik_url'].'">Piwik</a>'.(isset($aryConf['inline']) && $aryConf['inline']?' - <a href="?page=wp-piwik_stats">WP-Piwik</a>':'').'</td></tr>';
/***************************************************************************/ ?>
		</tbody>
	</table>
</div>
<?php /************************************************************************/
	}