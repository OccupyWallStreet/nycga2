// JavaScript Document
jQuery(document).ready(function() {
jQuery("#wpnav ul").css({display: "none"}); // Opera Fix
jQuery("#wpnav li").hover(function(){
		jQuery(this).find('ul:first').css({visibility: "visible",display: "none"}).show(400);
		},function(){
		jQuery(this).find('ul:first').css({visibility: "hidden"});
		});
});

// JavaScript Document
jQuery(document).ready(function() {
jQuery(".pagenav ul").css({display: "none"}); // Opera Fix
jQuery(".pagenav li").hover(function(){
		jQuery(this).find('ul:first').css({visibility: "visible",display: "none"}).show(400);
		},function(){
		jQuery(this).find('ul:first').css({visibility: "hidden"});
		});
});