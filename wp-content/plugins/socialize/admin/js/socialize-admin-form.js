
jQuery(document).ready(function($) {
	var t_test = $('input:radio[name=socialize_twitterWidget]:checked').val();
	$(".socialize-twitter-select").hide();
	$("#socialize-twitter-"+t_test).show();

	var f_test = $('input:radio[name=socialize_fbWidget]:checked').val();
	$(".socialize-facebook-select").hide();
	$("#socialize-facebook-"+f_test).show();
		
	var d_test = $("input[name=socialize_button_display]:checked").val();
	if(d_test == 'in')
		$("input[name=socialize_out_margin]").attr("disabled", "disabled");
	else
		$("input[name=socialize_out_margin]").removeAttr("disabled");

	$("input[name=socialize_twitterWidget]").change(function() {
		var t_test = $(this).val();
		$(".socialize-twitter-select").hide();
		$("#socialize-twitter-"+t_test).show();
	}); 
	$("input[name=socialize_fbWidget]").change(function() {
		var f_test = $(this).val();
		$(".socialize-facebook-select").hide();
		$("#socialize-facebook-"+f_test).show();
	});
	$("input[name=socialize_button_display]").change(function() {
		var d_test = $(this).val();
		if(d_test == 'in')
             $("input[name=socialize_out_margin]").attr("disabled", "disabled");
          else
             $("input[name=socialize_out_margin]").removeAttr("disabled");
	});
});