(function($) {

	$(document).ready(function($) {
		// bind functions
		$(window).bind('load', init_enable_subsite_content_types);
		$('.cp-main input[name="enable_subsite_content_types"]').bind('change', init_enable_subsite_content_types);

		//Make the combo box for date formats
		$('#date_format').combobox([
		'mm/dd/yy',
		'mm-dd-yy',
		'mm.dd.yy',
		'dd/mm/yy',
		'dd-mm-yy',
		'dd.mm.yy',
		'yy/mm/dd',
		'yy-mm-dd',
		'yy.mm.dd',
		'M d, y',
		'MM d, yy',
		'd M, yy',
		'd MM, yy',
		'DD, d MM, yy',
		"'day' d 'of' MM 'in the year' yy"
		]);

	});

	// initiate the value of the post_type rewrite field
	function init_enable_subsite_content_types() {
		if ( $('.cp-main input[name="enable_subsite_content_types"]:checked').val() === '1' ) {
			$('.cp-main input[name="display_network_content_types"]').attr( 'disabled', false );
		} else {
			$('.cp-main input[name="display_network_content_types"]').attr( 'disabled', true );
		}
	}

})(jQuery);

