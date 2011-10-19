// JavaScript Document

$(document).ready(function(){
	  
	  var appid = $("#appid").val();
		  var xfbml =  ($("#xfbml").is(':checked')) ? true : false;
		  var iframe= ($("#iframe").is(':checked')) ? true : false;
		  var pos   = $("#pos :selected").val();
		  var layout= $("#layout :selected").val();
		  var height= $("#height").val();
		  var width = $("#width").val();
		  var css   = $("#css").val();
		  var verb  = $("#verb").val();
		  var face  = ($("#face").is(":checked")) ? true : false;
		  var send  = ($("#send").is(":checked")) ? true : false;
		  var color = $("#color :selected").val();
		  var ht    = $("#ht :selected").val();
		  var locale= $("#fblikes_locale :selected").val();
          var font  = $("#fblikes_font :selected").val();

		  var SDK  = '<div id="fb-root"></div>';
			  SDK += '<script>';
			  SDK += 'window.fbAsyncInit = function() {';
			  SDK += 'FB.init({appId: '+appid+', status: true, cookie: true, xfbml: true}); };';
			  SDK += '(function() {';
			  SDK += 'var e = document.createElement("script"); e.async = true;';
			  SDK += 'e.src = document.location.protocol +';
          if (locale == "default") {
			  SDK += '"//connect.facebook.net/en_US/all.js";';
          } else {
			  SDK += '"//connect.facebook.net/'+ locale +'/all.js";';
          }
			  SDK += 'document.getElementById("fb-root").appendChild(e); }()); <\/script>';
		  
		  var iver = '<iframe src="http://www.facebook.com/plugins/like.php?';
			  iver+= 'href=http%3A%2F%2Fblog.ahmedgeek.com';
			  iver+= '&send='+send+'&amp;layout='+layout+'&amp;font='+escape(font)+'&amp;show_faces='+face+'';
          if (locale == "default") {
			  iver+= '&amp;width=450&amp;action='+verb+'&amp;colorscheme='+color+'"';
          } else {
			  iver+= '&amp;width=450&amp;action='+verb+'&amp;locale='+locale+'&amp;colorscheme='+color+'"';
          }
			  iver+= 'scrolling="no" frameborder="0" allowTransparency="true"';
			  iver+= 'style="border:none; overflow:hidden; width:450px; height:px"></iframe>';
			  
		  var xver = '<fb:like href="http://blog.ahmedgeek.com"';
			  xver+= 'layout="'+layout+'" show_faces="'+face+'" width="450"';
			  xver+= 'action="'+verb+'" send = "'+send+'" colorscheme="'+color+'" font="'+font+'"></fb:like>';
			  
	  
			  $("#live").html(iver);
			  
	  
	  
	  $("#form").change(function(){
		  
		  var appid = $("#appid").val();
		  var xfbml =  ($("#xfbml").is(':checked')) ? true : false;
		  var iframe= ($("#iframe").is(':checked')) ? true : false;
		  var pos   = $("#pos :selected").val();
		  var layout= $("#layout :selected").val();
		  var height= $("#height").val();
		  var width = $("#width").val();
		  var css   = $("#css").val();
		  var verb  = $("#verb").val();
		  var face  = ($("#face").is(":checked")) ? true : false;
		  var send  = ($("#send").is(":checked")) ? true : false;
		  var color = $("#color :selected").val();
		  var ht    = $("#ht :selected").val();		  
		  var locale= $("#fblikes_locale :selected").val();
          var font  = $("#fblikes_font :selected").val();

		  var SDK  = '<div id="fb-root"></div>';
			  SDK += '<script>';
			  SDK += 'window.fbAsyncInit = function() {';
			  SDK += 'FB.init({appId: '+appid+', status: true, cookie: true, xfbml: true}); };';
			  SDK += '(function() {';
			  SDK += 'var e = document.createElement("script"); e.async = true;';
			  SDK += 'e.src = document.location.protocol +';
          if (locale == "default") {
			  SDK += '"//connect.facebook.net/en_US/all.js";';
          } else {
			  SDK += '"//connect.facebook.net/'+ locale +'/all.js";';
          }
			  SDK += 'document.getElementById("fb-root").appendChild(e); }()); <\/script>';
		  
		  var iver = '<iframe src="http://www.facebook.com/plugins/like.php?';
			  iver+= 'href=http%3A%2F%2Fblog.ahmedgeek.com';
			  iver+= '&amp;layout='+layout+'&amp;font='+escape(font)+'&amp;show_faces='+face+'';
          if (locale == "default") {
			  iver+= '&send='+send+'&amp;width=450&amp;action='+verb+'&amp;colorscheme='+color+'"';
          } else {
			  iver+= '&amp;width=450&amp;action='+verb+'&amp;locale='+locale+'&amp;colorscheme='+color+'"';
          }
			  iver+= 'scrolling="no" frameborder="0" allowTransparency="true"';
			  iver+= 'style="border:none; overflow:hidden; width:450px; height:px"></iframe>';
			  
		  var xver = '<fb:like href="http://blog.ahmedgeek.com"';
			  xver+= 'layout="'+layout+'" show_faces="'+face+'" width="450"';
			  xver+= 'action="'+verb+'" send = "'+send+'" colorscheme="'+color+'" font="'+font+'"></fb:like>';
			  
	  
			  $("#live").html(iver);
			  
			  
		  
		  });
	  
	  });