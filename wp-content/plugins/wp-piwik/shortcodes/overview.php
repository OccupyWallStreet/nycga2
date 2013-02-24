<?php
/*********************************
	WP-Piwik::Short:Overview
**********************************/
$aryData = $this->callPiwikAPI('VisitsSummary.get',
	$this->aryAttributes['period'],
	$this->aryAttributes['date'],
	$this->aryAttributes['limit']
);

$this->strResult = '<table><tr><th>'.__('Overview', 'wp-piwik').($this->aryAttributes['title']?' '.$this->aryAttributes['title']:'').'</th></tr>';

function summize($aryData) {
	$aryTmp = array();
	foreach ($aryData as $aryValues)
		foreach($aryValues as $strKey => $intValue)
			if (isset($aryTmp[$strKey])) $aryTmp[$strKey] += $intValue;
			else $aryTmp[$strKey] = $intValue;
	$aryTmp['bounce_rate'] = ($aryTmp['nb_uniq_visitors']==0?0:round($aryTmp['bounce_count']/$aryTmp['nb_visits']*100,2)).'%';	
	
	return $aryTmp;
}

if (is_array($aryData)) {
		if (isset($aryData['result']) && $aryData['result'] == 'error')
			$this->strResult .= '<tr><td>'.__('Error', 'wp-piwik').':'.'</td><td>'.$aryData['message'].'</td></tr>';
		else {
			if (is_array(current($aryData)))
				$aryData = summize($aryData);			
			$strTime = 
				floor($aryData['sum_visit_length']/3600).'h '.
				floor(($aryData['sum_visit_length'] % 3600)/60).'m '.
				floor(($aryData['sum_visit_length'] % 3600) % 60).'s';
			$strAvgTime = 
				floor($aryData['avg_time_on_site']/3600).'h '.
				floor(($aryData['avg_time_on_site'] % 3600)/60).'m '.
				floor(($aryData['avg_time_on_site'] % 3600) % 60).'s';
			$this->strResult .= '<tr><td>'.__('Visitors', 'wp-piwik').':'.'</td><td>'.$aryData['nb_visits'].'</td></tr>';
			$this->strResult .= '<tr><td>'.__('Unique visitors', 'wp-piwik').':'.'</td><td>'.$aryData['nb_uniq_visitors'].'</td></tr>';
			$this->strResult .= '<tr><td>'.__('Page views', 'wp-piwik').':'.'</td><td>'.$aryData['nb_actions'].' (&#216; '.$aryData['nb_actions_per_visit'].')</td></tr>';
			$this->strResult .= '<tr><td>'.__('Max. page views in one visit', 'wp-piwik').':'.'</td><td>'.$aryData['max_actions'].'</td></tr>';
			$this->strResult .= '<tr><td>'.__('Total time spent', 'wp-piwik').':'.'</td><td>'.$strTime.'</td></tr>';
			$this->strResult .= '<tr><td>'.__('Time/visit', 'wp-piwik').':'.'</td><td>'.$strAvgTime.'</td></tr>';
			$this->strResult .= '<tr><td>'.__('Bounce count', 'wp-piwik').':'.'</td><td>'.$aryData['bounce_count'].' ('.$aryData['bounce_rate'].')</td></tr>';
		}
} else $this->strResult .= '<tr><td>'.__('No data available', 'wp-piwik').'</td></tr>';

$this->strResult .= '</table>';