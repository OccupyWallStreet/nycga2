<?php 
/*********************************
	WP-Piwik::Stats:SEO
**********************************/
	$aryConf['data'] = $GLOBALS['wp_piwik']->callPiwikAPI(
		'SEO.getRank',
		$aryConf['params']['period'],
		$aryConf['params']['date'],
		$aryConf['params']['limit'],
		false,
		false,
		'csv'
	);
	
	if (substr($aryConf['data'], 0, 6) == 'Error:') {
		$strMessage = str_replace('Error:', '', $aryConf['data']);
		echo '<strong>'.__('Piwik error', 'wp-piwik').':</strong> '.htmlentities($strMessage, ENT_QUOTES, 'utf-8');
	} else {
		
	$aryConf['title'] = __('SEO', 'wp-piwik');
	$aryLines = explode("\n", $aryConf['data']);
	foreach ($aryLines as $strLine)
		$aryData[] = explode(',', $strLine);
	unset($aryData[0]);
/***************************************************************************/ ?>
<div class="table">
	<table class="widefat">
		<tbody>
			<?php foreach ($aryData as $aryVal) { ?>
			<tr><td><?php echo $aryVal[0]; ?></td><td><?php echo $aryVal[1]; ?></td></tr>
			<?php } ?>
		</tbody>
	</table>
</div>
<?php /************************************************************************/
	}