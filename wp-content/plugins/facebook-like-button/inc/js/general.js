// JavaScript Document

$(document).ready(function(){
	
	if($("#uni_op").is(":checked")){
			
			    $("#uni_id").attr("disabled", false);

	}else{
				
			    $("#uni_id").attr("disabled", true);
			    $("#uni_id").css("color", "#3d3d3d");
				
		   }
	
	$("#uni_op").click(function(){
		
		if($("#uni_op").is(":checked")){
			
			 $("#uni_id").attr("disabled", false);
			 $("#uni_id").css("color", "black");

		}else{
				
			    $("#uni_id").attr("disabled", true);
			    $("#uni_id").css("color", "#3d3d3d");
				
		   }

		
		});	
	
	
	});