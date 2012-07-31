	$(document).ready(function(){	
$("#slider").easySlider({
	controlsBefore:	'<p id="controls">',
	controlsAfter:	'</p>',
	auto:true, 
	continuous:true
});
$("#slider2").easySlider({
	controlsBefore:	'<p id="controls2">',
	controlsAfter:	'</p>',	
	prevId: 'prevBtn2',
	nextId: 'nextBtn2'	
});			
});	