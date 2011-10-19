// JavaScript Document
$(document).ready(function(){
	  
	    var SDK  = '<div id="fb-root"></div>';
		SDK += '<script>';
		SDK += 'window.fbAsyncInit = function() {';
		SDK += 'FB.init({appId: 144910628856902, status: true, cookie: true, xfbml: true}); };';
		SDK += '(function() {';
		SDK += 'var e = document.createElement("script"); e.async = true;';
		SDK += 'e.src = document.location.protocol +';
		SDK += '"//connect.facebook.net/en_US/all.js";';
		SDK += 'document.getElementById("fb-root").appendChild(e); }()); <\/script>';
		
	   // $("#SDK").append(SDK);
	
	});
		  