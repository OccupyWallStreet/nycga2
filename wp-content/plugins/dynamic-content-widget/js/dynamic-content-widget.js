
function checkField(id, hiddenid) {
	setTimeout(function() {
		var field = jQuery("#" + id);
		var hiddenField = jQuery("#" + hiddenid);
		var resultField = jQuery("#" + id + "-result");
		
		var q = field.val();
		
		resultField.html('<img src="../wp-admin/images/loading.gif" alt="Checking" />');
		
		jQuery.getJSON(  
	        "../wp-content/plugins/dynamic-content-widget/ajax/findcontentid.php",  
	        {q: field.val()},  
	        function(json) {
	        	var result = '<img src="../wp-admin/images/no.png" alt="Unknown error" />';
	        	if (json.status == 'NOT_FOUND') {
	        		field.addClass("error");
	            	result = '<img src="../wp-admin/images/no.png" alt="No content found" />';
	        	} else {
	            	var innerResult = json.id + ": " + json.title; 
	
	            	hiddenField.val(json.id);
	        		
	        		if (json.status == 'TOO_MANY_FOUND') {
		        		field.addClass("warning");
	        			result = '<img src="../wp-admin/images/yes.png" alt="Warning: more than one item found" />';
	        		} else if (json.status == 'OK') {
	        			field.removeClass("error");
	        			field.removeClass("warning");
	        			result = '<img src="../wp-admin/images/yes.png" alt="Content found" />'
	        		}
	        	}
	            resultField.html(result);  
	        }  
	    );  
	}, 500);
}

jQuery.ajaxSetup ({  
    cache: false  
});  

