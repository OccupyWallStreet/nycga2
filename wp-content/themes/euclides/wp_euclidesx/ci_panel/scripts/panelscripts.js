jQuery(document).ready(function($) {
 
	// delay equivalent for jQuery 1.3.2
	$.fn.hold = function(time){
		var o = $(this);
		o.queue(function(){
			setTimeout(function() {
				o.dequeue();
			}, time);
		});
		return this;
	};

	 //tabs
	$('.tab').hide();
	$('.one').show();

	$('#ci_sidebar ul li a').click( function() {
		$(this).addClass('active').parents('li').siblings().find('a').removeClass('active');
		var tab = $(this).attr('rel');
		$('#ci_options div#'+tab).show().siblings().hide();
		return false;
	});
 
	//form submission 
	$("#ci_panel .success").hide();
	$("#ci_panel .resetbox").hold(2000).fadeOut(500);
	
	$('input.save').click(function() {
		var theoptions = $('#theform').serialize();
		$.ajax({
			type: "POST",
			url: "options.php",
			data: theoptions,
			beforeSend: function() { $("#ci_panel .success").html('Working...').fadeIn(500); }, 
			success: function(response){ $("#ci_panel .success").html('Settings saved!').hold(2000).fadeOut(500); }
		});
		return false;  
	});	 


	$('#bg_color').ColorPicker({
		onSubmit: function(hsb, hex, rgb, el) {
			$(el).val(hex);
			$(el).ColorPickerHide();
		},
		onBeforeShow: function () {
			$(this).ColorPickerSetColor(this.value);
		}
	}).bind('keyup', function(){
		$(this).ColorPickerSetColor(this.value);
	});

	var isEnabled = $('.toggle-button').attr('checked');
	var pane = $('.toggle-pane');
	if (isEnabled) { pane.hide(); } else { pane.show(); }
	
	$('.toggle-button').click(function(){
		var pane = $(this).parents('div.tab').children('.toggle-pane');
		if ($(this).attr('checked')==true) {
			pane.fadeOut();
		}
		else {
			pane.fadeIn();
		}
	});
	//$('.toggle-button').click();

});