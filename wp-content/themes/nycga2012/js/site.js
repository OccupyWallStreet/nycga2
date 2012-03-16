// nycga = {};

jQuery(document).ready(function() {

	// add drop shadow to any element with the '.shadow' class
	var shadows = jQuery(document).find('.shadow');
	shadows.each( function( i, element ){
		nycga.ui.addDropShadow( element );
	});

	// show a tooltip for each element with the '.hoverMenu' class
	// code.drewwilson.com/entry/tiptip-jquery-plugin
	
	var galleryElement = jQuery(document).find('#homapgeFeatures ul.gallery');
	if( galleryElement ){
		nycga.gallery = {};
		nycga.gallery.element = galleryElement;
		nycga.gallery.nav = jQuery(document).find('#homapgeFeatures ul.nav')[0];
		console.log( nycga.gallery.nav );
		jQuery( nycga.gallery.nav ).children().each( function( index ) {
			jQuery( this ).click( function( e ){
				e.preventDefault();
				var target = jQuery( jQuery( nycga.gallery.nav ).children()[ index ] );
				var scrollPos = target.offset().top - ( nycga.gallery.element.scrollTop() +  nycga.gallery.element.offset().top );
				nycga.gallery.element.animate({
				    scrollTop: scrollPos
				});
				console.log(":::::::", index, scrollPos, nycga.gallery.element.scrollTop() );
			});
		});

	}
	
	nycga.ui.addToolTips();

});

