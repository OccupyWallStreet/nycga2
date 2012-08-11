$(document).ready(function(){

	$(".btn-slide").mouseover(function(){
		$("#panel").slideToggle("slow");
		$(this).toggleClass("active"); return false;
	});
	
	 
});
$(document).ready(function(){

	$(".btn-slide2").click(function(){
		$("#panel2").slideToggle("slow");
		$(this).toggleClass("active"); return false;
	});
	
	 
});
$(document).ready(function(){

	$(".btn-slide3").click(function(){
		$("#panel3").slideToggle("slow");
		$(this).toggleClass("active"); return false;
	});
	
	 
});

$(document).ready(function(){

	$(".btn-slide4").click(function(){
		$("#panel4").slideToggle("slow");
		$(this).toggleClass("active"); return false;
	});
	
	 
});
$(document).ready(function(){

	$(".home-post-wrap .delete").click(function(){
	  $(this).parents(".home-post-wrap").animate({ opacity: "hide" }, "slow");
	});

});
$(document).ready(function(){

	$(".home-post-wrap .share").click(function(){
	  $(this).next(".share-div").slideToggle("slow");
		$(this).toggleClass("active"); return false;
	});

});

$(document).ready(function(){

	$(".post-wrapper .share").click(function(){
	  $(this).next("div").slideToggle("slow");
		$(this).toggleClass("active"); return false;
	});

});