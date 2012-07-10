jQuery(document).ready(function($) {
	var t_test = $('input:radio[name=socialize_twitterWidget]:checked').val();
	$(".socialize-twitter-select").hide();
	$("#socialize-twitter-"+t_test).show();

	var f_test = $('input:radio[name=socialize_fbWidget]:checked').val();
	$(".socialize-facebook-select").hide();
	$("#socialize-facebook-"+f_test).show();
	
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
});