/*
 when someone types in the post field @ it triggers an autocomplete of all the users
 */
var PP_mention ={
	start_search : false, // this get changes to true
	search_text: '',
	onReady :function() {
		jQuery("#posttext").areacomplete({
        wordCount:1,
        mode: "outter",
        on: {
            query: function(text,cb){
                var words = [];
                PP_user_lenght = PP_users.length
                for( var i=0; i<PP_user_lenght; i++ ){
                	console.log(PP_users[i]);
                    if( PP_users[i][0].toLowerCase().indexOf(text.toLowerCase()) == 0 ) {
                    
                    words.push( PP_users[i][0] +" <em class='better-id'>"+PP_users[i][2]+ " "+PP_users[i][1]+"</em>" );
                    }
                }
                console.log(words);
                cb(words);                              
            },
            selected: function(text, data)
            {
            	
            	mention = text.split(" ");
            	console.log(mention);
                return mention[0];
            }
        }
    });
				
	}
};

jQuery(document).ready(PP_mention.onReady);
