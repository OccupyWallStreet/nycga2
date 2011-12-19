jQuery(document).ready(function() {
	// add drop shadow to any element with the '.shadow' class
	var shadows = jQuery(document).find('.shadow');
	shadows.each( function( i, element ){
		nycga.ui.addDropShadow( element );
	});
	// show a tooltip for each element with the '.hoverMenu' class
	// code.drewwilson.com/entry/tiptip-jquery-plugin
	nycga.ui.addToolTips();
});