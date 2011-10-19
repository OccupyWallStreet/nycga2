// JavaScript Document

$(document).ready(function(){

		// Define Vars
	    var domain = $("#domain").val();
		var width  = $("#width").val();
		var height = $("#height").val();
		var layout = $("#layout").val();
		var font   = $("#font").val();
		var border = $("#border").val();
		
		if($("#header").is(":checked"))
		{
			
			var header = true;
			
			}
			
		else
		{
			var header = false;
			}
		
		
		// Create the live block
	var live_rec  = '<iframe src="http://www.facebook.com/plugins/recommendations.php?site='+domain+'&amp;';
		live_rec += 'width='+width+'&amp;height='+height+'&amp;header='+header+'&amp;colorscheme='+layout+'';
		live_rec += '&amp;font='+font+'&amp;border_color='+border+'"';
		live_rec += 'scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:'+width+'px; height:'+height+'px;" allowTransparency="true">';
		live_rec += '</iframe>';
		
		// Check if the domain is null
		if(domain == ""){
			live_rec = "<br><span><b><font color = 'red'>Please Enter Domain Name to Get Preivew!</font></b></span>";
			}
		
		//Embed the live block
		$("#live_ref").html(live_rec);
    
	// Do the same on change
	$("#form").change(function(){
		var domain = $("#domain").val();
		var width  = $("#width").val();
		var height = $("#height").val();
		var layout = $("#layout").val();
		var font   = $("#font").val();
		var border = $("#border").val();
		
		if($("#header").is(":checked")){
			  
			  var header = true;
			
			}else{
			 
			  var header = false;
				
				}
		var full_plugin = '<iframe src="http://www.facebook.com/plugins/recommendations.php?site='+domain+'&amp;';
		    full_plugin += 'width='+width+'&amp;height='+height+'&amp;header='+header+'&amp;colorscheme='+layout+'';
			full_plugin += '&amp;font='+font+'&amp;border_color='+border+'"';
			full_plugin += 'scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:'+width+'px; height:'+height+'px;" allowTransparency="true">';
			full_plugin += '</iframe>';
		
		$("#live_ref").html(full_plugin);

		
		
		});
		
	//Hide the live block
	$("#close_live").change(function(){
		
		if($("#close_live").is(":checked")){
		
		$("#live_ref").fadeOut('slow');
		
		}else{
			$("#live_ref").fadeIn('slow');
			}
		
		
		
		});
		
	
	
	
	});
	
	


