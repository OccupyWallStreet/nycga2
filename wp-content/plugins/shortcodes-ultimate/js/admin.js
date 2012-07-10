jQuery(document).ready(function($) {

	// Code editor
	var gn_custom_editor = CodeMirror.fromTextArea(document.getElementById("su-custom-css"), {});

	// Tables
	$('.su-table-shortcodes tr:even, .su-table-demos tr:even').addClass('even');

	// Tabs
	$('#su-wrapper .su-pane:first').show();
	$('#su-tabs a').click(function() {
		$('.su-message').hide();
		$('#su-tabs a').removeClass('su-current');
		$(this).addClass('su-current');
		$('#su-wrapper .su-pane').hide();
		$('#su-wrapper .su-pane').eq($(this).index()).show();
		gn_custom_editor.refresh();
	});

	// Ajaxify settings form
	$('#su-form-save-settings').ajaxForm({
		beforeSubmit: function() {
			$('#su-form-save-settings .su-spin').show();
			$('#su-form-save-settings .su-submit').attr('disabled', true);
		},
		success: function() {
			$('#su-form-save-settings .su-spin').hide();
			$('#su-form-save-settings .su-submit').attr('disabled', false);
		}
	});

	// Ajaxify custom CSS form
	$('#su-form-save-custom-css').ajaxForm({
		beforeSubmit: function() {
			$('#su-form-save-custom-css .su-spin').show();
			$('#su-form-save-custom-css .su-submit').attr('disabled', true);
		},
		success: function() {
			$('#su-form-save-custom-css .su-spin').hide();
			$('#su-form-save-custom-css .su-submit').attr('disabled', false);
		}
	});

	// Auto-open tab by link with hash
	if ( strpos( document.location.hash, '#tab-' ) !== false )
		$('#su-tabs a:eq(' + document.location.hash.replace('#tab-','') + ')').trigger('click');

});

// ########## Strpos tool ##########

function strpos( haystack, needle, offset) {
	var i = haystack.indexOf( needle, offset );
	return i >= 0 ? i : false;
}