jQuery.fn.wpDialog = function( options ) {
	
	var o = jQuery.extend( {} , jQuery.fn.wpDialog.defaults, options );
		
	// var wpfill = function( el, index ) {
	// 	kel = el.replace( /wpui-(.*)-arg/mg, '$1' )
	// 			.replace(/(.*)-(.*)/, '$1 : $2');
	// 	return kel;
	// };
	
	return this.each(function() {
		var base = this;
		$base = jQuery( base );
	
		// dtitle = $base.find('h4.wp-dialog-title').text();

		dialogArgs = $base.find('h4.wp-dialog-title')
						.toggleClass('wp-dialog-title')
						.attr('class').split(' ');
		
		$base.find('h4:first').remove();
		
		kel = {};	
		
		// console.log( dialogArgs ); 
		for( i = 0; i < dialogArgs.length; i++ ) {
			dialogArgs[i] = dialogArgs[i].replace( /wpui-(.*)-arg/mg, '$1' );
			key = dialogArgs[i].replace(/([\w\d\S]*):([\w\d\S]*)/mg, '$1');
			value = dialogArgs[i].replace(/(.*):(.*)/mg, '$2').replace( /%/mg , ' ');
			if ( value == "true" ) value = true;
			if ( value == "false" ) value = false;
			kel[key] = value;			
		}

		
		dialogCloseFn = function() {
			$(this).dialog("close");
		};
		
		
		if ( kel.position == 'bottomleft' ) {
			kel.position = [ 'left' , 'bottom' ];
		} else if ( kel.position == 'bottomright' ) {
			kel.position = [ 'right', 'bottom' ];
		} else if ( kel.position == 'topleft' ) {
			kel.position = [ 'left', 'top' ];
		} else if ( kel.position == 'topright' ) {
			kel.position = [ 'right', 'top' ];
		}
		
		kel.width = parseInt( kel.width ) + "px";
		
		if ( kel.button ) {
			buttonLabel = kel.button;
			delete kel.button;
			kel.buttons = {};
			kel.buttons[ buttonLabel ] = dialogCloseFn
		}
		
		if ( kel.dialogClass && kel.dialogClass != '' ) {
			kel[ 'dialogClass' ] = kel.dialogClass.replace(/_/gm, ' ');
		}

		$base.dialog( kel );

		jQuery( '[class*=dialog-opener]' ).button({
			icons : {
				primary : 'ui-icon-newwin'
			}
		});
		
		jQuery( '[class*=dialog-opener]' ).click(function() {
			openerClass = jQuery( this ).attr( 'class' ).match(/dialog\-opener\-(\d{1,2})/);
			dNum = openerClass[ 1 ];
			jQuery( '.wp-dialog-' + dNum ).dialog( 'open' );
			return false;
		});
		
			
	}); // return this.each.

};

jQuery.fn.wpDialog.defaults = {
	title	: 'Information'
};