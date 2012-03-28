var farbtastic;

(function($){
	var pickColor = function(a) {
		farbtastic.setColor(a);
		$('#custom_color').val(a);
		$('#custom_color-example').css('background-color', a);
	};

	$(document).ready( function() {
		farbtastic = $.farbtastic('#colorPickerDiv', pickColor);

		pickColor( $('#custom_color').val() );

		$('.pickcolor').click( function(e) {
			$('#colorPickerDiv').show();
			e.preventDefault();
		});

		$('#custom_color').keyup( function() {
			var a = $('#custom_color').val(),
				b = a;

			a = a.replace(/[^a-fA-F0-9]/, '');
			if ( '#' + a !== b )
				$('#custom_color').val(a);
			if ( a.length === 3 || a.length === 6 )
				pickColor( '#' + a );
		});

		$(document).mousedown( function() {
			$('#colorPickerDiv').hide();
		});
	});
})(jQuery);