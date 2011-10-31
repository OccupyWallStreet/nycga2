<?php if (!defined('W3TC')) die(); ?>
<?php include W3TC_INC_DIR . '/popup/common/header.php'; ?>

<script type="text/javascript">/*<![CDATA[*/
jQuery(function() {
    W3tc_Popup_Cdn_Import_Library.nonce = '<?php echo wp_create_nonce('w3tc'); ?>';
    W3tc_Popup_Cdn_Import_Library.cdn_host = '<?php echo $cdn_host; ?>';
	W3tc_Popup_Cdn_Import_Library.init();
});
/*]]>*/</script>

<p>This tool will copy post or page attachments into the Media Library allowing WordPress to work as intended.</p>
<table cellspacing="5">
	<tr>
		<td>Total posts:</td>
		<td id="cdn_import_library_total"><?php echo $total; ?></td>
	</tr>
	<tr>
		<td>Processed:</td>
		<td id="cdn_import_library_processed">0</td>
	</tr>
	<tr>
		<td>Status:</td>
		<td id="cdn_import_library_status">-</td>
	</tr>
	<tr>
		<td>Time elapsed:</td>
		<td id="cdn_import_library_elapsed">-</td>
	</tr>
	<tr>
		<td>Last response:</td>
		<td id="cdn_import_library_last_response">-</td>
	</tr>
	<tr>
		<td colspan="2">
			<label><input id="cdn_import_library_redirect_permanent" type="checkbox" checked="checked" /> Create a list of permanent (301) redirects for use in your site's .htaccess file</label>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<label><input id="cdn_import_library_redirect_cdn" type="checkbox" /> Create a list of redirects to <acronym title="Content Delivery Network">CDN</acronym> (hostname specified in hostname field #1.)</label>
		</td>
	</tr>
</table>

<p>
	<input id="cdn_import_library_start" class="button-primary" type="button" value="Start"<?php if (! $total): ?> disabled="disabled"<?php endif; ?> />
</p>

<div id="cdn_import_library_progress" class="progress">
	<div class="progress-value">0%</div>
	<div class="progress-bar"></div>
	<div class="clear"></div>
</div>

<div id="cdn_import_library_log" class="log"></div>

<p>
	Add the following directives to your .htaccess file or if there are several hundred they should be added directly to your configuration file:
</p>

<p>
	<textarea rows="10" cols="90" id="cdn_import_library_rules" class="rules"></textarea>
</p>

<?php include W3TC_INC_DIR . '/popup/common/footer.php'; ?>