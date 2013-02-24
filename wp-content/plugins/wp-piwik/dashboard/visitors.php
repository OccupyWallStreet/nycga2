<?php
/*********************************
	WP-Piwik::Stats:Vistors
**********************************/

	$aryConf['data']['Visitors'] = $this->callPiwikAPI(
		'VisitsSummary.getVisits', 
		$aryConf['params']['period'], 
		$aryConf['params']['date'],
		$aryConf['params']['limit']
	);
	$aryConf['data']['Unique'] = $this->callPiwikAPI(
		'VisitsSummary.getUniqueVisitors',
		$aryConf['params']['period'],
		$aryConf['params']['date'],
		$aryConf['params']['limit']
	);
	$aryConf['data']['Bounced'] = $this->callPiwikAPI(
		'VisitsSummary.getBounceCount',
		$aryConf['params']['period'],
		$aryConf['params']['date'],
		$aryConf['params']['limit']
	);
	
	if (isset($aryConf['data']['Visitors']['result']) && $aryConf['data']['Visitors']['result'] ='error')
		echo '<strong>'.__('Piwik error', 'wp-piwik').':</strong> '.htmlentities($aryConf['data']['Visitors']['message'], ENT_QUOTES, 'utf-8');
	else {
		$strValues = $strLabels = $strBounced =  $strValuesU = $strCounter = '';
		$intUSum = $intCount = 0; 
		if (is_array($aryConf['data']['Visitors']))
			foreach ($aryConf['data']['Visitors'] as $strDate => $intValue) {
				$intCount++;
				$strValues .= $intValue.',';
				$strValuesU .= $aryConf['data']['Unique'][$strDate].',';
				$strBounced .= $aryConf['data']['Bounced'][$strDate].',';
				$strLabels .= '['.$intCount.',"'.substr($strDate,-2).'"],';
				$intUSum += $aryConf['data']['Unique'][$strDate];
			}
		else {$strValues = '0,'; $strLabels = '[0,"-"],'; $strValuesU = '0,'; $strBounced = '0,'; }
		$intAvg = round($intUSum/30,0);
		$strValues = substr($strValues, 0, -1);
		$strValuesU = substr($strValuesU, 0, -1);
		$strLabels = substr($strLabels, 0, -1);
		$strBounced = substr($strBounced, 0, -1);
		$strCounter = substr($strCounter, 0, -1);

/***************************************************************************/ ?>
<div class="wp-piwik-graph-wide" title="<?php _e('The graph contains the values shown in the table below (visitors / unique / bounces). The red line show a linear trendline (unique).', 'wp-piwik'); ?>">
	<div id="wp-piwik_stats_vistors_graph" style="height:220px;<?php if (!isset($aryConf['inline']) || $aryConf['inline'] != true) { ?>width:100%<?php } ?>"></div>
</div>
<?php if (!isset($aryConf['inline']) || $aryConf['inline'] != true) { ?>
<div class="table">
	<table class="widefat wp-piwik-table">
		<thead>
			<tr>
				<th><?php _e('Date', 'wp-piwik'); ?></th>
				<th class="n"><?php _e('Visits', 'wp-piwik'); ?></th>
				<th class="n"><?php _e('Unique', 'wp-piwik'); ?></th>
				<th class="n"><?php _e('Bounced', 'wp-piwik'); ?></th>
			</tr>
		</thead>
		<tbody style="cursor:pointer;">
<?php /************************************************************************/
		if (is_array($aryConf['data']['Visitors'])) {
			$aryTmp = array_reverse($aryConf['data']['Visitors']);
			foreach ($aryTmp as $strDate => $intValue)
				echo '<tr onclick="javascript:datelink(\''.urlencode('wp-piwik_stats').'\',\''.str_replace('-', '', $strDate).'\',\''.(isset($_GET['wpmu_show_stats'])?(int) $_GET['wpmu_show_stats']:'').'\');"><td>'.$strDate.'</td><td class="n">'.
					$intValue.'</td><td class="n">'.
					$aryConf['data']['Unique'][$strDate].
					'</td><td class="n">'.
					$aryConf['data']['Bounced'][$strDate].
					'</td></tr>'."\n";
		}
		echo '<tr><td class="n" colspan="4"><strong>'.__('Unique TOTAL', 'wp-piwik').'</strong> '.__('Sum', 'wp-piwik').': '.$intUSum.' '.__('Avg', 'wp-piwik').': '.$intAvg.'</td></tr>';	
		unset($aryTmp);
/***************************************************************************/ ?>
		</tbody>
	</table>
</div>
<?php } ?>
<script type="text/javascript">
$plotVisitors = $j.jqplot('wp-piwik_stats_vistors_graph', [[<?php echo $strValues; ?>],[<?php echo $strValuesU; ?>],[<?php echo $strBounced;?>]],
{
	axes:{yaxis:{min:0, tickOptions:{formatString:'%.0f'}},xaxis:{min:1,max:30,ticks:[<?php echo $strLabels; ?>]}},
	seriesDefaults:{showMarker:false,lineWidth:1,fill:true,fillAndStroke:true,fillAlpha:0.9,trendline:{show:false,color:'#C00',lineWidth:1.5,type:'exp'}},
	series:[{color:'#90AAD9',fillColor:'#D4E2ED'},{color:'#A3BCEA',fillColor:'#E4F2FD',trendline:{show:true,label:'Unique visitor trend'}},{color:'#E9A0BA',fillColor:'#FDE4F2'}],
});
</script>
<?php 
	}