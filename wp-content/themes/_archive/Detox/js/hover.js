$(document).ready(function(){
	$(".glidecontent").hover(function() {
		$(this).children(".glidemeta").animate({opacity: "show"}, "slow");
	}, function() {
		$(this).children(".glidemeta").animate({opacity: "hide"}, "fast");
	});
});

$(document).ready(function() {
	$('#tabzine > ul').tabs({ fx: { height: 'toggle', opacity: 'toggle' } });
	
});

