<?php
/*********************************
	WP-Piwik::Stats:BrowserDetails
**********************************/

	$aryConf['data'] = $this->callPiwikAPI(
			'UserSettings.getBrowserVersion', 
			$aryConf['params']['period'], 
			$aryConf['params']['date'],
			$aryConf['params']['limit']
	);
	$aryConf['title'] = __('Browser Details', 'wp-piwik');
	$strValues = '';
	$intCount = 0; $intMore = 0; $intSum = 0;
	if (isset($aryConf['data']['result']) && $aryConf['data']['result'] ='error')
		echo '<strong>'.__('Piwik error', 'wp-piwik').':</strong> '.htmlentities($aryConf['data']['message'], ENT_QUOTES, 'utf-8');
	else {
		if (is_array($aryConf['data']))
			foreach ($aryConf['data'] as $key => $aryValues) {
				$intCount++;
				if ($intCount <= 9) $strValues .= '["'.$aryValues['label'].'",'.$aryValues['nb_uniq_visitors'].'],';
				else $intMore += $aryValues['nb_uniq_visitors'];
				$intSum += $aryValues['nb_uniq_visitors'];
			}
		if ($intMore) $strValues .= '["'.__('Others', 'wp-piwik').'",'.$intMore.'],';
		$strValues = substr($strValues, 0, -1);
		if ($intSum) {
/***************************************************************************/ ?>
<div class="wp-piwik-graph-wide">
	<div id="wp-piwik_stats_browserdetails_graph" style="height:310px;width:100%"></div>
</div>
<?php /************************************************************************/
		}
/***************************************************************************/ ?>	
<div class="table">
	<table class="widefat wp-piwik-table">
		<thead>
			<tr>
				<th><?php _e('Browser', 'wp-piwik'); ?></th>
				<th class="n"><?php _e('Unique', 'wp-piwik'); ?></th>
				<th class="n"><?php _e('Percent', 'wp-piwik'); ?></th>
			</tr>
		</thead>
		<tbody>
<?php /************************************************************************/
		if ($intSum)
			foreach ($aryConf['data'] as $aryValues)
				echo '<tr><td>'.
					$aryValues['label'].
					'</td><td class="n">'.
					$aryValues['nb_uniq_visitors'].
					'</td><td class="n">'.
					number_format($aryValues['nb_uniq_visitors']/$intSum*100, 2).
				'%</td></tr>';
		else echo '<tr><td colspan="3">'.__('No data available.', 'wp-piwik').'</td></tr>';
		unset($aryTmp);
/***************************************************************************/ ?>
		</tbody>
	</table>
</div>
<script type="text/javascript">
$plotBrowsers = $j.jqplot('wp-piwik_stats_browserdetails_graph', [[<?php echo $strValues; ?>]], {
    seriesDefaults:{renderer:$j.jqplot.PieRenderer, rendererOptions:{sliceMargin:8}},
    legend:{show:true}
});
</script>
<?php
	}