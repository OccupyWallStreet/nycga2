/* Base on
 * https://bitbucket.org/jiaaro/jquery-urlshortener/wiki/Home
 */

(function($){
        $.shortenUrl = function(longUrl, callback) {
                // Build the URL to query
                var api_call = "http://api.bit.ly/shorten?"
                        +"version="+$.shortenUrl.settings.version
                        +"&longUrl="+longUrl
                        +"&login="+$.shortenUrl.settings.login
                        +"&apiKey="+$.shortenUrl.settings.apiKey
                        +"&history="+$.shortenUrl.settings.history
                        +"&format=json&callback=?";
                
                // See if we've shortened this url already
                var cached_result = $.shortenUrl.shortenedUrls[longUrl];
                if (cached_result !== undefined) {
                        // the timeout is to eliminate race conditions arising 
                        // from the assumption that the callback will be
                        // called after an ajax call
                        window.setTimeout(function() {
                        	    
                                callback(cached_result, longUrl);
                        }, 1);
                }
                else {
                        // Utilize the bit.ly API
                        $.getJSON(api_call, function(data){
                        		if(data.results) {
                        		  	var short_url = data.results[longUrl].shortUrl;
                                	$.shortenUrl.shortenedUrls[longUrl] = short_url;
                                	callback(short_url,longUrl);
                        		} else {
                        			newNotification("Error with the URL Shortner:<br /> "+data.errorMessage);
                        		}
                              
                        });
                }
        }
        
        // set up default options
        $.shortenUrl.settings = {
                version:    '2.0.1',
                login:      '',
                apiKey:     '',
                history:    '0'
        };
        $.shortenUrl.shortenedUrls = {};
})(jQuery);

jQuery(function($) {
	$.pp_words = '';
	$.pp_count = 0;
	$.shortenUrl.settings.login = pp_shortner.user;
	$.shortenUrl.settings.apiKey = pp_shortner.api;


	// lets try to shorten the urls 
	$("#shorten-url").click(function(){
		$.pp_words = jQuery("#posttext").val().split(" ");
		
		var num_of_words =  $.pp_words.length;
		var i=0;
		
		if($.pp_words[0]){
		for (i=0;i<num_of_words;i++){
			
			if( $.pp_words[i].substring(4,0)  == "http" && 
			    $.pp_words[i].substring(13,0) != "http://bit.ly" && // ignore bitly as well 
			    $.pp_words[i].substring(11,0) != "http://j.mp" && // ignore bitly as well 
			    $.pp_words[i].substring(13,0) != "http://goo.gl" &&
			    $.pp_words[i].substring(14,0) != "http://yhoo.it") {
			   
			  
			    $.shortenUrl( $.pp_words[i], function(short_url,longUrl) {
			
			    	// how do I replace the string with the proper 
			    
					var num_of_words =  $.pp_words.length;
					var i=0;
					for (i=0;i<num_of_words;i++){
						
						if($.pp_words[i] == longUrl)
						 $.pp_words[i] = short_url;
					}
			   	jQuery("#posttext").val( $.pp_words.join(" ") ); // replace the right stuff back
			   	disable_submit();
			  })
			 
			}
		}
		}
	}); // click 
});