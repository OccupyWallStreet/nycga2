$(document).ready(function(){
	var original_val=$('#wp_pn_style_selection').attr('value');
		var val1=$('#wp_pn_style_select_box').attr('value');
		$('#wp_pn_style_IMG_preview').removeClass();
		$('#wp_pn_style_IMG_preview').addClass(val1);			
	if(original_val=='custom')
	{
		$('#wp_pn_custom_template_style').hide();
		$('#wp_pn_style_custom_style_box').show();

	}
	else
	{
			$('#wp_pn_style_custom_style_box').hide();
			$('#wp_pn_custom_template_style').show();
	}
	$('#wp_pn_style_selection').change(function(){
	var val1=$('#wp_pn_style_selection').attr('value');
			if(val1=='custom')
			{
				$('#wp_pn_style_custom_style_box').show();
				$('#wp_pn_custom_template_style').hide();
			}
			else
			{
				$('#wp_pn_style_custom_style_box').hide();
				$('#wp_pn_custom_template_style').show();
			}
	
	});
	
	$('#wp_pn_style_select_box').change(function(){
		var val1=$('#wp_pn_style_select_box').attr('value');
		$('#wp_pn_style_IMG_preview').removeClass();
		$('#wp_pn_style_IMG_preview').addClass(val1);			
	});
	$('.wp_pn_color_picker').ColorPicker({
		onSubmit: function(hsb, hex, rgb, el) {
			$(el).val(hex);
			$(el).ColorPickerHide();
		},
		onBeforeShow: function () {
			$(this).ColorPickerSetColor(this.value);
		}
	})
	.bind('keyup', function(){
		$(this).ColorPickerSetColor(this.value);
	});
});