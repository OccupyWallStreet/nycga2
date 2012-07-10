<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Choose your WP UI Style</title>

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js"></script>
<script type="text/javascript" src="../wp-content/plugins/wp-ui/js/wp-ui.js"></script>
<!-- <link rel="stylesheet" href="../wp-content/plugins/wp-ui/wp-ui.css" media="screen">-->
<link rel="stylesheet" href="../wp-content/plugins/wp-ui/css/jquery-ui-wp-fix.css" media="screen">
<link type="text/css" rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/themes/ui-lightness/jquery.ui.base.css" />
<script type="text/javascript"  src="../wp-content/plugins/wp-ui/js/themeswitcher.js">
</script>
<!-- <script type="text/javascript" src="http://jqueryui.com/js/jquery.js"></script> -->
<script type="text/javascript">
jQuery().ready(function($) {
	jQuery('.wp-tabs').wptabs();
	
	jQuery( 'div.wp-accordion' ).wpaccord({
		h3Class			: 	'h3.wp-tab-title',
		linkAjaxClass	: 	'a.wp-tab-load',
		accordEvent : 'click',
		easing : 'bounceslide'	
	});
	jQuery( '.wp-spoiler' ).wpspoiler({
		fade	 : true,
		slide	 : true,
	});
	
	jQuery( '.wp-dialog' ).wpDialog();
	

	
  	jQuery('#chosentab').themeswitcher({
		loadTheme : 'Start',
		height : 500,
		width : 180,
		buttonHeight : 18,
		initialText : 'Select a theme',
		onSelect : function() {

			selTheme = jQuery( '.jquery-ui-themeswitcher-trigger > .jquery-ui-themeswitcher-title' ).text(); 
			nowTheme = selTheme.replace( /(Theme:\s)*/im, '' ).toLowerCase().replace(/\s/, '-');
			jQuery( '#selected_skin' ).val( nowTheme ); 
			// jQuery( '#chosentab' )
			// 	.hide('drop', {direction: 'up'}, 600);

		},
		onThemeLoad : function() {
			// jQuery( '#chosentab' )
			// 	.show('drop', {direction: 'up'}, 600);			
		}
		
	});



	jQuery( '.jquery-ui-themeswitcher-trigger' ).appendTo( 'h2.styler-title span' );
	
	// ctWidth = jQuery( '#chosentab' ).innerWidth();
	// console.log( ctWidth ); 
	// compoWidth = ( ctWidth > 750 ) ?  ( (ctWidth - 80) / 2 ) : ctWidth;


	// ctW = Math.round( cTW );
	var compoWidth = Math.round(( jQuery( '#chosentab' ).width() -80 ) / 2);
	var compoPad = ( ( jQuery( window ).width() - jQuery( '#chosentab' ).width() ) / 2 ) - 10; 

	jQuery( '#chosentab' ).css({ padding : '0 ' + compoPad + 'px' })
	
	jQuery( '.holster' ).width( compoWidth - (parseInt(jQuery( '.holster' ).css('paddingLeft')) + parseInt(jQuery( '.holster' ).css( 'paddingRight' )) ));
	
	jQuery( '.hilite-combo' ).css('text-decoration', 'underline').hover(function() {
		jQuery( '.jquery-ui-themeswitcher-trigger' ).effect( "pulsate", { times : '3' } , 300);
	}, function() {
		
	});


	
	
});
</script>

<style type="text/css">

body {
	font : 12px/1.5 'Helvetica Neue', 'Arial', 'verdana', sans-serif;
	padding: 0px !important;
	margin : 0px !important;
}

h2.styler-title {
	background : #F4F2F4;
	background : -moz-linear-gradient( top , rgba( 170, 170, 170, 0.4 ), rgba( 255, 255, 255, 0.7) );
	background : -webkit-gradient( left top, left bottom , rgba( 170, 170, 170, 0.4 ), rgba( 255, 255, 255, 0.7) );
	background : -webkit-linear-gradient( top , rgba( 170, 170, 170, 0.4 ), rgba( 255, 255, 255, 0.7) );
	background : -o-linear-gradient( top , rgba( 170, 170, 170, 0.4 ), rgba( 255, 255, 255, 0.7) );
	text-align : center;
	margin : 0px;
	-moz-box-shadow    : 0 1px 0 #FFF inset, 0 -1px 0 #FFF inset, 0 2px 7px rgba( 0, 0, 0, 0.4);
	-webkit-box-shadow : 0 1px 0 #FFF inset, 0 -1px 0 #FFF inset, 0 2px 7px rgba( 0, 0, 0, 0.4);
	-o-box-shadow      : 0 1px 0 #FFF inset, 0 -1px 0 #FFF inset, 0 2px 7px rgba( 0, 0, 0, 0.4);
	box-shadow         : 0 1px 0 #FFF inset, 0 -1px 0 #FFF inset, 0 2px 7px rgba( 0, 0, 0, 0.4);
	color : #222;
	text-shadow : 0 1px 0 #FFF;
}

h2.styler-title span.themechooserholder {
	display : inline-block;
	padding: 10px;
	font-weight : normal;
}

p.submit {
	text-align: center;
	margin: 20px auto;
}

p.submit #submit {
	background: -moz-linear-gradient( top, #FFF, #CCC 40%, #BBB 41%, #DDD);
	background: -webkit-gradient(linear, left top, left bottom, from(#FFF), color-stop(0.4, #CCC), color-stop(0.41, #BBB), to(#DDD));
	background: -webkit-linear-gradient( top, #FFF, #CCC 40%, #BBB 41%, #DDD );
	background: -o-linear-gradient( top, #FFF, #CCC 40%, #BBB 41%, #DDD);
	color: #1E2634;
	text-shadow: 0 1px 0 #FFF;
	font-size: 1.1em;
	font-weight : bold;
	padding: 5px 10px;
	border: #4A5E80 1px solid;
	-moz-border-radius     : 5px;
	-webkit-border-radius  : 5px;
	-o-border-radius       : 5px;
	border-radius          : 5px;
	box-shadow         : 0 1px 3px #28303D;
	-moz-box-shadow    : 0 1px 3px #3A7D9E;
	-webkit-box-shadow : 0 1px 3px #28303D;
	-o-box-shadow      : 0 1px 3px #28303D;
}

input#selected_skin {
	height: 1px;
}

p.submit #submit:hover {
	border-color: #FFF;
	color: #000;
}

p.submit #submit:active {
	border: 2px solid #FFF;
	box-shadow         : 0 2px 3px #28303D inset;
	-moz-box-shadow    : 0 2px 3px #28303D inset;
	-webkit-box-shadow : 0 2px 3px #28303D inset;
	-o-box-shadow      : 0 2px 3px #28303D inset;
}

#chosentab {
	width : 800px;
	padding: 10px;
/*	margin : 0 auto;*/
}

.holster {
	margin : 10px;
	float : left;
	width : 365px;
}

.spoiler-holder {
	clear : left;
}

.wp-spoiler {
	font-family : 'Arial', sans-serif !important;
	
}
.section-header {
	text-transform : uppercase;
	font-weight : normal;
	text-shadow : 0 1px 1px rgba(255, 255, 255, 0.3);
}
</style>
<script type="text/javascript">
function submit_form() {
	var win = window.dialogArguments || opener || parent || top;
	parent.jQuery( '#tab_scheme' ).val( document.forms[ 0 ].selected_skin.value );
	parent.jQuery.fn.colorbox.close();
	parent.document.forms[ 0 ].submit();
	return false;
}
</script>
</head>
<body class="jqui-options-noise">
<h2 class="styler-title">Choose a jQuery UI theme<span class="themechooserholder"></span></h2>



<form onsubmit="submit_form()" style="margin-top: 30px;" action="#">
<p class="submit"><input type="submit" value="Choose this theme" id="submit"/></p>
<div id="chosentab">

<div class="description">
	<p style="text-align: center; padding: 5px;">Preview and choose your jQuery UI theme. Select with the <span class="hilite-combo">combo</span> button above and once done, click the choose button to select it. Visit <a href="http://jqueryui.com/themeroller/" target="_blank" rel="nofollow">jQuery Themegallery</a>. Theme might take a moment to load. </p>
<div><!-- end description -->	
	
	
	
<!-- ###################### -->
<!-- ######## Tabs ######## -->
<!-- ###################### -->

<div class="holster tabs-holder">
<h3 class="section-header">Tabs</h3>
<div class="wp-tabs">
	
<h3 class="wp-tab-title">First</h3>
	<div class="wp-tab-content">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam sed elit ut erat viverra dapibus. Cras at blandit sem. Nullam in augue non ipsum fermentum consequat. Nulla eu orci velit. Cras eu neque non justo malesuada pretium ut nec arcu. Curabitur viverra mollis risus vel convallis. Sed et felis dolor. Mauris semper faucibus ipsum non porta. Proin erat quam, congue a venenatis nec, volutpat nec leo. Nam vehicula lorem quis nulla tristique tempor. </div><!-- end div.wp-tab-content -->
<h3 class="wp-tab-title">Second</h3><br>
	<div class="wp-tab-content">Vestibulum rhoncus ligula est. Nam nisi velit, vestibulum eget fermentum vitae, bibendum vitae velit. Sed ac ante eget nisl elementum varius. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Maecenas ut leo eget leo volutpat placerat vitae at est. Mauris vestibulum ligula vel ante rhoncus luctus. Fusce sagittis, nisi at faucibus eleifend, sapien mauris semper arcu, eget aliquam justo enim sit amet turpis. Nulla fringilla, nunc in hendrerit volutpat, massa leo laoreet lectus, a vehicula odio ligula quis metus.</div><!-- end div.wp-tab-content -->
<h3 class="wp-tab-title">Third</h3>
	<div class="wp-tab-content">Donec non sem nibh, ut euismod urna. Morbi accumsan scelerisque est sed rutrum. In dictum tortor id ipsum tempus dictum. In laoreet tempus ante eu consectetur. Nunc auctor, orci quis aliquam rutrum, quam ligula vestibulum nunc, vestibulum laoreet enim urna in libero. Integer vitae augue at ante tristique luctus. Quisque dolor orci, aliquet a feugiat id, rhoncus non orci. Curabitur varius lectus in enim facilisis ut tincidunt nibh malesuada. Aliquam erat volutpat. Vestibulum id nibh nisl. Nam faucibus eros in quam ultricies vel accumsan neque aliquam. </div><!-- end div.wp-tab-content --> 

</div><!-- end wp-tabs -->
</div><!-- end holder -->



<!-- ###################### -->
<!-- ###### Accordion ##### -->
<!-- ###################### -->
<div class="holster accordion-holder">
<h3 class="section-header">Accordion</h3>


<div class="wp-accordion">
	
<h3 class="wp-tab-title">Panel 1</h3>
	<div class="wp-tab-content">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam sed elit ut erat viverra dapibus. Cras at blandit sem. Nullam in augue non ipsum fermentum consequat. Nulla eu orci velit. Cras eu neque non justo malesuada pretium ut nec arcu. Curabitur viverra mollis risus vel convallis. Sed et felis dolor. Mauris semper faucibus ipsum non porta. Proin erat quam, congue a venenatis nec, volutpat nec leo. Nam vehicula lorem quis nulla tristique tempor. </div><!-- end div.wp-tab-content -->
<h3 class="wp-tab-title">Panel 2</h3><br>
	<div class="wp-tab-content">Vestibulum rhoncus ligula est. Nam nisi velit, vestibulum eget fermentum vitae, bibendum vitae velit. Sed ac ante eget nisl elementum varius. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Maecenas ut leo eget leo volutpat placerat vitae at est. Mauris vestibulum ligula vel ante rhoncus luctus. Fusce sagittis, nisi at faucibus eleifend, sapien mauris semper arcu, eget aliquam justo enim sit amet turpis. Nulla fringilla, nunc in hendrerit volutpat, massa leo laoreet lectus, a vehicula odio ligula quis metus.</div><!-- end div.wp-tab-content -->
<h3 class="wp-tab-title">Panel 3</h3>
	<div class="wp-tab-content">Donec non sem nibh, ut euismod urna. Morbi accumsan scelerisque est sed rutrum. In dictum tortor id ipsum tempus dictum. In laoreet tempus ante eu consectetur. Nunc auctor, orci quis aliquam rutrum, quam ligula vestibulum nunc, vestibulum laoreet enim urna in libero. Integer vitae augue at ante tristique luctus. Quisque dolor orci, aliquet a feugiat id, rhoncus non orci. Curabitur varius lectus in enim facilisis ut tincidunt nibh malesuada. Aliquam erat volutpat. Vestibulum id nibh nisl. Nam faucibus eros in quam ultricies vel accumsan neque aliquam. </div><!-- end div.wp-tab-content --> 

</div><!-- end wp-accordion -->
</div><!-- end holder -->




<div class="holster spoiler-holder">
<h3 class="section-header">Spoilers</h3>

<div class="wp-spoiler">
<h3 class="wp-spoiler-title">Spoiler section</h3>
<div class="wp-spoiler-content">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam sed elit ut erat viverra dapibus. Cras at blandit sem. Nullam in augue non ipsum fermentum consequat. Nulla eu orci velit. Cras eu neque non justo malesuada pretium ut nec arcu. Curabitur viverra mollis risus vel convallis. Sed et felis dolor. Mauris semper faucibus ipsum non porta. Proin erat quam, congue a venenatis nec, volutpat nec leo. Nam vehicula lorem quis nulla tristique tempor.
</div><!-- end .ui-collapsible-content -->
</div><!-- end div.wp-spoiler -->
</div><!-- end holder -->



<div class="holster dialog-holder">
<h3 class="section-header">Dialog</h3>

<div class="dialog-button">
<p class="dialog-opener-container wpui-macish"><a href="#" class="dialog-opener-3">Open Dialog</a></p>
</div>
<div class="wp-dialog wp-dialog-3 dialog-number-3" title="UI dialog"><h4 class="wp-dialog-title wpui-dialogClass:dialog-number-3-arg wpui-width:300-arg wpui-height:auto-arg wpui-autoOpen:false-arg wpui-show:drop-arg wpui-hide:fade-arg wpui-modal:false-arg wpui-closeOnEscape:true-arg wpui-position:center-arg wpui-modal:true-arg wpui-zIndex:1000-arg"></h4> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras tempus, tellus at sagittis imperdiet, turpis augue rutrum lacus, ut tincidunt ligula mi vitae nibh. Praesent nisl velit, pellentesque in semper quis, pretium nec massa. </div>

</div><!-- end holder -->


</div><!-- End chosen tabs -->


<br />
<input type="hidden" id="selected_skin" name="selected_skin" type="text" />		

</form>
</body>
</html>
