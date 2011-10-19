//Rot13 original code from: http://scott.yang.id.au/file/js/rot13.js
//adapted by Hikari at http://Hikari.ws

HkMuobJS = {
    rotMap: null,

    rotInit: function() {
        if (HkMuobJS.rotMap != null)
            return;
              
        var map = new Array();
        var s   = "abcdefghijklmnopqrstuvwxyz";

        for (i=0; i<s.length; i++)
            map[s.charAt(i)] = s.charAt((i+13)%26);
        for (i=0; i<s.length; i++)
            map[s.charAt(i).toUpperCase()] = s.charAt((i+13)%26).toUpperCase();

        HkMuobJS.rotMap = map;
    },
	
    rotDecote: function(a) {
        HkMuobJS.rotInit();

        var s = "";
        for (i=0; i < a.length; i++) {
            var b = a.charAt(i);
            s += ((b>='A' && b<='Z') || (b>='a' && b<='z') ? HkMuobJS.rotMap[b] : b);
        }
        return s;
    },

//Cc8b originally written by Debugged Interactive Designs www.debuggeddesigns.com
//adapted by Hikari at http://Hikari.ws
	cc8bDecode : function (cipherText,key) {
	
 //alert("cipherText: "+cipherText+"\nkey: "+key);
	
		var plainText = new String("");
		var temp;
	
		//key = parseInt(key,10);
	
		for(var i=0;i <= cipherText.length - 2;i=i+2)
		{
			temp = cipherText.substr(i,2);
			temp = parseInt(temp,16);
			temp += 0xFF; 
			temp -= key;
			temp = temp % 0xFF;
			plainText += String.fromCharCode(temp);
		}
		return plainText;
	},

    write: function(id,key,addr,attributes,content,decodeContent) {
	
 //alert("before decode\n\nid: "+id+"\nkey: "+key+ "\naddr: "+addr+ "\ncontent: "+content+ "\nattributes: "+attributes);
	
		var decoded_addr='';
		
		// if key is 0, it means chosen encoding was ROT13, it is not 0, used encoding was cc8b
	
		// let's start decoding href address
		if(key==0){
			decoded_addr = HkMuobJS.rotDecote(addr);
			decoded_attr = HkMuobJS.rotDecote(attributes);
		}else{
			decoded_addr = HkMuobJS.cc8bDecode(addr,key);
			decoded_attr = HkMuobJS.cc8bDecode(attributes,key);
		}
		

		
		//decodeContent means that content is also encoded, so if it's true let's decode content
		if(decodeContent){
			if(key==0)
				content = HkMuobJS.rotDecote(content);
			else
				content = HkMuobJS.cc8bDecode(content,key);
		}
		
 //alert("after decode\n\nid: "+id+"\nkey: "+key+ "\ndecoded_addr: "+decoded_addr+ "\ncontent: "+content+ "\nattributes: "+decoded_attr);
 
		// everything set, let's create the anchor link, giving it the decoded address, adding all additional attributes, eventual title, and of course its content
		var final_link = '<a href="'+decoded_addr+'" '+decoded_attr+'>'+content+'</a>';



 //alert("id: "+id+"\naddr: "+addr+"\ncontent: "+content+"\nattributes: "+attributes+"\ndecodeContent: "+decodeContent+"\ncontentIsTitle: "+contentIsTitle+"\ndecoded_addr: "+decoded_addr+"\ntitle: "+title+"\nfinal_link: "+final_link);

 //alert("final_link:\n\n"+final_link);



		// ok our link is ready, now let's  print it
		

		// if id was set, we must find the element with this id and replace its content with our link
		if(id){
			element = document.getElementById(id);
			
 //alert("before element.innerHTML:\n\n"+element.innerHTML);

			// if element was not found, do nothing
			if(element)
				element.innerHTML=final_link;
		
		// if id was not set, just write it where our code was called
		}else{
			document.write(final_link);
		}

 //alert("after element.innerHTML:\n\n"+element.innerHTML);
		
		//document.write(final_link);
    }
}

