<?php
//WangGuard Wizard
function wangguard_stats() {
	global $wpdb,$wangguard_nonce, $wangguard_api_key,$wangguard_api_host , $wangguard_rest_path;
	
	if ( !current_user_can('level_10') )
		die(__('Cheatin&#8217; uh?', 'wangguard'));
	

	$lang = substr(WPLANG, 0,2);
	?>

<div class="wrap" id="wangguard-stats-cont">
	<div class="wangguard-confico"><img src="<?php echo WP_PLUGIN_URL ?>/wangguard/img/stats.png" alt="<?php echo htmlentities(__('WangGuard Stats', 'wangguard')) ?>" /></div>
	<div class="icon32" id="icon-wangguard"><br></div>
	<h2><?php _e('WangGuard Stats', 'wangguard'); ?></h2>
	
	<script type="text/javascript">
		jQuery(document).ready(function () {
			
			var WGURL = "http://<?php echo $wangguard_api_host . $wangguard_rest_path?>";
			var WGstatsLast7URL = WGURL + "get-stat.php?wg="+ encodeURIComponent('<in><apikey><?php echo $wangguard_api_key?></apikey><last7>1</last7><lang><?php echo $lang?></lang></in>');
			var WGstatsLast30URL = WGURL + "get-stat.php?wg="+ encodeURIComponent('<in><apikey><?php echo $wangguard_api_key?></apikey><last30>1</last30><lang><?php echo $lang?></lang></in>');
			var WGstatsLast6URL = WGURL + "get-stat.php?wg="+ encodeURIComponent('<in><apikey><?php echo $wangguard_api_key?></apikey><last6>1</last6><lang><?php echo $lang?></lang></in>');

			jQuery.ajax({
				dataType: "jsonp",
				url: WGstatsLast30URL,
				jsonpCallback: "callback",
				success: function (data) {
						jQuery("#wangguard-stats-last30-container").wijbarchart(data);

						jQuery.ajax({
							dataType: "jsonp",
							url: WGstatsLast6URL,
							jsonpCallback: "callback",
							success: function (data) {
									jQuery("#wangguard-stats-last6-container").wijbarchart(data);
								}
						});
					}
			});

		});
	</script>

	<div id="wangguard-stats-container">
		<h2><?php _e( 'Last 30 days' , 'wangguard' )?></h2>
		<div id="wangguard-stats-last30-container" class="ui-widget ui-widget-content ui-corner-all"></div>

		<h2><?php _e( 'Last 6 months' , 'wangguard' )?></h2>
		<div id="wangguard-stats-last6-container" class="ui-widget ui-widget-content ui-corner-all"></div>
	</div>		
	

</div>
<?php
}
?>