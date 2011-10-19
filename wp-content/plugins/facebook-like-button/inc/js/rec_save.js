//Saveing Process for Recommendations Page

$(document).ready(function(){
	
	$('#submit').click(function(){
		
		var FormData = $("#form").serialize();
		
	$.ajax({
        type: "POST",
        url: "action/rec_save.php",
        data: FormData ,
        success: function(msg){
        alert(msg);
		
          }
   
    });
	
	
		
		});
	
	});