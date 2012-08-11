<div class="wrap">
	<h2><?php _e('Floating Social settings', 'wdsb');?></h2>

<?php if (WP_NETWORK_ADMIN) { ?>
	<form action="settings.php" method="post">
<?php } else { ?>
	<form action="options.php" method="post">
<?php } ?>

	<?php settings_fields('wdsb'); ?>
	<?php do_settings_sections('wdsb_options_page'); ?>
	<p class="submit">
		<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
	</p>
	</form>

</div>

<script type="text/javascript">
(function ($) {
$(function () {

function toggleRelativeLeft () {
	var val = $("#wdsb-left_relative").val();
	if ("selector" == val) $("#wdsb-left_selector-root").show();
	else $("#wdsb-left_selector-root").hide();
}
function toggleRelativeTop () {
	var val = $("#wdsb-top_relative").val();
	if ("selector" == val) $("#wdsb-top_selector-root").show();
	else $("#wdsb-top_selector-root").hide();
}
function toggleAdvancedHook () {
	if ($("#front_footer-yes").is(":checked")) $("#wdsb_hook_root").show();
	else $("#wdsb_hook_root").hide();
}

$("#wdsb-left_relative").change(toggleRelativeLeft);
$("#wdsb-top_relative").change(toggleRelativeTop);
$("#front_footer-yes").change(toggleAdvancedHook);
$("#front_footer-no").change(toggleAdvancedHook);

toggleRelativeLeft();
toggleRelativeTop();
toggleAdvancedHook();

$("#wdsb-services").sortable({
	"items": "li:not(.wdsb-disabled)"
});
$('.wdsb-service-item input[name*="services"]').change(function () {
	var $me = $(this);
	var $parent = $me.parents('.wdsb-service-item');
	if ($me.is(":checked")) $parent.removeClass("wdsb-disabled");
	else if (!$me.is(":checked") && !$parent.is(".wdsb-disabled")) $parent.addClass("wdsb-disabled");
	$("#wdsb-services").sortable("destroy").sortable({
		"items": "li:not(.wdsb-disabled)"
	});
});

$(".wdsb_remove_service").click(function() {
	$(this).parents('li.wdsb-service-item').remove();
	return false;
});

/* --- Individual entries --- */

$(".wdsb_prevent_individual").click(function () {
	var $me = $(this);
	var $parent = $me.parents('li');
	var type = $parent.find('input').val();
	var $out = $parent.find('.wdsb_entries');

	$me.html('').css({
		'background': 'url(<?php echo admin_url("images/loading.gif");?>) top left no-repeat',
		"padding-left": 32,
		"padding-bottom": 5
	});

	$.post(ajaxurl, {"action": "wdsb_list_entries", "type": type}, function(data) {
		var html = '';
		$.each(data.entries, function (idx, item) {
			var checked = parseInt(item.checked) ? 'checked="checked"' : '';
			html += '<li>';
			html += '<input type="checkbox" id="wdsb-entry-' +
				item.id +
				'" name="wdsb[prevent_items][]" value="' +
				item.id +
				'" ' +
				checked + ' /> '
			;
			html += '<label for="wdsb-entry-' + item.id + '">' + item.title + '</label>';
			html += '</li>';
		});
		$out.html(html);
		$me.hide();
	});
	return false;
});

});
})(jQuery);
</script>