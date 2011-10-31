<?php if (!defined('W3TC')) die(); ?>
<?php include W3TC_INC_DIR . '/popup/common/header.php'; ?>

<script type="text/javascript">/*<![CDATA[*/
jQuery(function() {
    W3tc_Popup_Cdn_Export_Library.nonce = '<?php echo wp_create_nonce('w3tc'); ?>';
	W3tc_Popup_Cdn_Export_Library.init();
});
/*]]>*/</script>

<p>This tool will upload files of the selected type to content delivery network provider.</p>
<table cellspacing="5">
	<tr>
		<td>Total media library attachments:</td>
		<td id="cdn_export_library_total"><?php echo $total; ?></td>
	</tr>
	<tr>
		<td>Processed:</td>
		<td id="cdn_export_library_processed">0</td>
	</tr>
	<tr>
		<td>Status:</td>
		<td id="cdn_export_library_status">-</td>
	</tr>
	<tr>
		<td>Time elapsed:</td>
		<td id="cdn_export_library_elapsed">-</td>
	</tr>
	<tr>
		<td>Last response:</td>
		<td id="cdn_export_library_last_response">-</td>
	</tr>
</table>

<p>
	<input id="cdn_export_library_start" class="button-primary" type="button" value="Start"<?php if (! $total): ?> disabled="disabled"<?php endif; ?> />
</p>

<div id="cdn_export_library_progress" class="progress">
	<div class="progress-value">0%</div>
	<div class="progress-bar"></div>
	<div class="clear"></div>
</div>

<div id="cdn_export_library_log" class="log"></div>

<?php include W3TC_INC_DIR . '/popup/common/footer.php'; ?>