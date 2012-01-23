<?php if (!defined('W3TC')) die(); ?>
<?php include W3TC_INC_DIR . '/popup/common/header.php'; ?>

<script type="text/javascript">/*<![CDATA[*/
jQuery(function() {
	W3tc_Popup_Cdn_Rename_Domain.nonce = '<?php echo wp_create_nonce('w3tc'); ?>';
	W3tc_Popup_Cdn_Rename_Domain.init();
});
/*]]>*/</script>

<p>This tool allows you to modify the URL of Media Library attachments. Use it if the "WordPress address (<acronym title="Uniform Resource Indicator">URL</acronym>)" value has been changed in the past.</p>
<table cellspacing="5">
	<tr>
		<td>Total posts:</td>
		<td id="cdn_rename_domain_total"><?php echo $total; ?></td>
	</tr>
	<tr>
		<td>Processed:</td>
		<td id="cdn_rename_domain_processed">0</td>
	</tr>
	<tr>
		<td>Status:</td>
		<td id="cdn_rename_domain_status">-</td>
	</tr>
	<tr>
		<td>Time elapsed:</td>
		<td id="cdn_rename_domain_elapsed">-</td>
	</tr>
	<tr>
		<td>Last response:</td>
		<td id="cdn_rename_domain_last_response">-</td>
	</tr>
	<tr>
		<td>Domains to rename:</td>
		<td>
			<textarea cols="40" rows="3" id="cdn_rename_domain_names"></textarea><br />
			e.g.: domain.com
		</td>
	</tr>
</table>

<p>
	<input id="cdn_rename_domain_start" class="button-primary" type="button" value="Start"<?php if (! $total): ?> disabled="disabled"<?php endif; ?> />
</p>

<div id="cdn_rename_domain_progress" class="progress">
	<div class="progress-value">0%</div>
	<div class="progress-bar"></div>
	<div class="clear"></div>
</div>

<div id="cdn_rename_domain_log" class="log"></div>

<?php include W3TC_INC_DIR . '/popup/common/footer.php'; ?>