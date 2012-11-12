define( 
		[
		 "jquery",
		 "libs/colorpicker"
		 ],
		 function( $ ) {

$( '#tag-color' ).click( function() {
	var fs_offset = $( '#tag-color' ).offset();
	var top = fs_offset.top + $( '#tag-color' ).height() ;
	var left = fs_offset.left + 1;
	var ul_el = $( '<ul></ul>');
	var li_els = $(
		'<li style="color: #60a;" class="color-1"></li>' + // 1
		'<li style="color: #807;" class="color-2"></li>' + // 2
		'<li style="color: #920;" class="color-3"></li>' + // 3
		'<li style="color: #a60;" class="color-4"></li>' + // 4
		'<li style="color: #990;" class="color-5"></li>' + // 5
		'<li style="color: #080;" class="color-6"></li>' + // 6
		'<li style="color: #077;" class="color-7"></li>' + // 7
		'<li style="color: #00a;" class="color-8"></li>' + // 8
		'<li style="color: #000;" class="color-9"></li>' + // 9
		'<li style="color: #444;" class="color-10"></li>' + // 10
		'<li style="color: #85e;" class="color-11"></li>' + // 11
		'<li style="color: #d5d;" class="color-12"></li>' + // 12
		'<li style="color: #d43;" class="color-13"></li>' + // 13
		'<li style="color: #d90;" class="color-14"></li>' + // 14
		'<li style="color: #bb0;" class="color-15"></li>' + // 15
		'<li style="color: #2b0;" class="color-16"></li>' + // 16
		'<li style="color: #0ba;" class="color-17"></li>' + // 17
		'<li style="color: #26d;" class="color-18"></li>' + // 18
		'<li style="color: #777;" class="color-19"></li>' + // 19
		'<li style="color: #aaa;" class="color-20"></li>' + // 20
		'<li style="color: #aab;" class="color-21"></li>'   // 21
	);
	var more_color = $( '<li class="select-more-colors">More colors</li>' );
	$( more_color ).ColorPicker({
		onSubmit: function( hsb, hex, rgb, el ) {
			$( '#tag-color-background' ).css( 'background-color', '#' + hex );
			$( '#tag-color-value' ).val( '#' + hex );
			$(el).ColorPickerHide();
			ul_el.remove();
		},
		onBeforeShow: function () {
			ul_el.hide();
			$( document ).unbind( 'mousedown', hide_color_selector );
			var color = $( '#tag-color-value' ).val();
			color = color.length > 0 ? color : '#ffffff';
			$( this ).ColorPickerSetColor( color );
		}
	});
	// Add click event for each font style
	li_els.click( function() { 
		if( rgb2hex( $(this).css( 'color' ) ) != "#aaaabb" ){
			$( '#tag-color-background' ).css( 'background-color', $(this).css( 'color' ) );
			$( '#tag-color-value' ).val( rgb2hex( $(this).css( 'color' ) )  );
			ul_el.remove();
		}
		else{
			$( '#tag-color-background' ).css( 'background-color', "" );
			$( '#tag-color-value' ).val( "" );
			ul_el.remove();
		}
	});

	// append li elements to the ul holder
	ul_el.append( li_els ).append( more_color );

	// append ul holder to the body
	ul_el
	.appendTo( 'body' )
	.css( {
		position: 'absolute',
		top: top + 'px',
		left: left + 'px',
		width: '105px',
		height: '70px',
		'z-index': 1,
		background: '#fff',
		border: '1px solid #ccc'
	})
	.addClass( 'colorpicker-list' );
	$( document ).bind( 'mousedown', {ls: ul_el}, hide_color_selector );
});

// remove category color click 
$( "#tag-color-value-remove" ).click(function(){
	$( "#tag-color-background" ).css( "background-color","" );
	$( "#tag-color-value" ).val("")
});

var rgb2hex = function( rgb ) {
	rgb = rgb.match( /^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/ );
	return "#" + hex( rgb[1] ) + hex( rgb[2] ) + hex( rgb[3] );
};

var hex = function( x ) {
	return ( "0" + parseInt( x ).toString( 16 ) ).slice( -2 );
};

var hide_color_selector = function( ev ) {
	if( ! is_child_of( ev.data.ls.get( 0 ), ev.target, ev.data.ls.get( 0 ) ) ) {
		$( ev.data.ls.get(0) ).remove();
		$( document ).unbind( 'mousedown', hide_color_selector );
	}
};
var is_child_of = function( parentEl, el, container ) {
	if( parentEl == el )
		return true;

	if( parentEl.contains )
		return parentEl.contains( el );

	if( parentEl.compareDocumentPosition )
		return !!(parentEl.compareDocumentPosition(el) & 16);

	var prEl = el.parentNode;
	while( prEl && prEl != container ) {
		if( prEl == parentEl )
			return true;
		prEl = prEl.parentNode;
	}
	return false;
};
} );
