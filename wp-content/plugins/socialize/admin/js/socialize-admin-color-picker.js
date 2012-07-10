var farbtastic_border;
var farbtastic_background;

function socialize_pickcolor_border(color) {
	jQuery('#border-color').val(color);
        jQuery('#border-color').css('background-color', color);
        farbtastic_border.setColor(color);
}
function socialize_pickcolor_background(color) {
	jQuery('#background-color').val(color);
        jQuery('#background-color').css('background-color', color);
        farbtastic_background.setColor(color);
}
jQuery(document).ready(function($) {
        farbtastic_background = $.farbtastic("#colorPickerDiv",function(a){
            socialize_pickcolor_background(a)
        });
        farbtastic_border = $.farbtastic("#colorPickerDiv_border",function(a){
            socialize_pickcolor_border(a)
        });
	$('#pickcolor_border').click(function() {
		$('#colorPickerDiv_border').show();
                return false;
	});
        $('#pickcolor').click(function() {
		$('#colorPickerDiv').show();
                return false;
	});
	$('#defaultcolor').click(function() {
		socialize_pickcolor_border(default_color);
		$('#border-color').val(default_color)
	});
        $('#defaultcolor').click(function() {
		socialize_pickcolor_background(default_color);
		$('#background-color').val(default_color)
	});
	$('#border-color').keyup(function() {
		var _hex = $('#border-color').val();
		var hex = _hex;
		if ( hex[0] != '#' )
			hex = '#' + hex;
		hex = hex.replace(/[^#a-fA-F0-9]+/, '');
		if ( hex != _hex )
			$('#border-color').val(hex);
		if ( hex.length == 4 || hex.length == 7 )
			socialize_pickcolor_border( hex );
	});
        $('#background-color').keyup(function() {
		var _hex = $('#background-color').val();
		var hex = _hex;
		if ( hex[0] != '#' )
			hex = '#' + hex;
		hex = hex.replace(/[^#a-fA-F0-9]+/, '');
		if ( hex != _hex )
			$('#background-color').val(hex);
		if ( hex.length == 4 || hex.length == 7 )
			socialize_pickcolor_background( hex );
	});
	$(document).mousedown(function(){
		$('#colorPickerDiv').each( function() {
			var display = $(this).css('display');
			if (display == 'block')
				$(this).fadeOut(2);
		});
                $('#colorPickerDiv_border').each( function() {
			var display = $(this).css('display');
			if (display == 'block')
				$(this).fadeOut(2);
		});
	});

        socialize_pickcolor_border($('#border-color').val());
        socialize_pickcolor_background($('#background-color').val());

});