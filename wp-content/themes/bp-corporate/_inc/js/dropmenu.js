// JavaScript Document
jQuery.noConflict();
var $dm = jQuery;
$dm(document).ready(function() {
$dm("#pnav ul").css({display: "none"}); // Opera Fix
$dm("#pnav li").hover(function(){
		$dm(this).find('ul:first').css({visibility: "visible",display: "none"}).show(400);
		},function(){
		$dm(this).find('ul:first').css({visibility: "hidden"});
		});
});

// JavaScript Document
$dm(document).ready(function() {
$dm("#nav ul").css({display: "none"}); // Opera Fix
$dm("#nav li").hover(function(){
		$dm(this).find('ul:first').css({visibility: "visible",display: "none"}).show(400);
		},function(){
		$dm(this).find('ul:first').css({visibility: "hidden"});
		});
});