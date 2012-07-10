<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Choose your WPTabs options</title>

<!-- <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script> -->
<script type="text/javascript" src="../wp-content/plugins/wp-ui/js/jquery.min.js"></script>
<script type="text/javascript" src="../wp-content/plugins/wp-ui/js/jquery-ui.min.js"></script>

<script type="text/javascript" src="../wp-content/plugins/wp-ui/js/wp-ui.js"></script>
<link rel="stylesheet" href="../wp-content/plugins/wp-ui/wp-ui.css" media="screen">
<link rel="stylesheet" href="../wp-content/plugins/wp-ui/css/wpui-all.css" media="screen">
<script type="text/javascript">
jQuery(document).ready(function($) {
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


	var i = 0;
	classList = new Array;
	classList[i++] = 'wpui-light';
	classList[i++] = 'wpui-blue';
	classList[i++] = 'wpui-red';
	classList[i++] = 'wpui-green';
	classList[i++] = 'wpui-dark';
	classList[i++] = 'wpui-achu';
	classList[i++] = 'wpui-quark';
	classList[i++] = 'wpui-cyaat9';
	classList[i++] = 'wpui-redmond';
	classList[i++] = 'wpui-sevin';
	classList[i++] = 'wpui-alma';
	classList[i++] = 'wpui-macish';
	classList[i++] = 'wpui-android';
	classList[i++] = 'wpui-safle';
	
	jQuery('#chosentab').tabsThemeSwitcher( classList );
	
	var cTW = Math.round(( jQuery( '#chosentab' ).innerWidth() -80 ) / 2);
	jQuery( '.holster' ).outerWidth( cTW );
	
	jQuery( '.hilite-combo' ).css('text-decoration', 'underline').hover(function() {
		jQuery( '.selector_tab_style' ).effect( "pulsate", { times : '3' } , 300);
	}, function() {
		
	});
	
	
	
});
</script>
<style type="text/css">
body {
	background: #C9D0DE;
	font: 12px 'Arial', sans-serif;
	margin: 0;
	padding: 0;
	line-height : 1.5;
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
.styler-title {
	background:#E2E2E2;
	background: rgba( 250, 250, 250, 0.5);
	background: -moz-linear-gradient( bottom, rgba( 250, 250, 250, 0.8 ), rgba( 175, 175, 175, 0.9 ) );
	background: -webkit-gradient( linear, left bottom, left top, rgba( 250, 250, 250, 0.8 ), rgba( 175, 175, 175, 0.9 ) );
	background: -webkit-linear-gradient( bottom, rgba( 250, 250, 250, 0.8 ), rgba( 175, 175, 175, 0.9 ) );
	background: -o-linear-gradient( bottom, rgba( 250, 250, 250, 0.8 ), rgba( 175, 175, 175, 0.9 ) );
	
	margin-top: 0;
	padding: 10px;
	text-align:center;
	text-shadow: 0 1px 0 #FFF;
	color: #28303D;
	font-weight : normal;
	-moz-box-shadow    : -1px -1px 0 #FFF inset, 0 2px 5px rgba( 0, 0, 0, 0.5);
	-webkit-box-shadow :  -1px -1px 0 #FFF inset, 0 2px 5px rgba( 0, 0, 0, 0.5);
	-o-box-shadow      :  -1px -1px 0 #FFF inset, 0 2px 5px rgba( 0, 0, 0, 0.5);
	box-shadow         :  -1px -1px 0 #FFF inset, 0 2px 5px rgba( 0, 0, 0, 0.5);
	position : fixed;
	width : 100%;
	top : 0px;
	z-index:  10001;
}

div.tab-top-nav a,
div.tab-bottom-nav a {
	padding: 4px 10px;
}
#chosentab {
	width: 90%;
	min-height: 400px;
	margin: 0 auto;
}
.ui-effects-transfer {
	border: #687D9E 1px dotted;
}
#chosentab > .wp-tabs {
	text-align: left;
}

#choosetabs {
	float:right;
	width: 100%;
}

#choosetabs div.skin_name {
	min-height: 16px !important;
	margin: 2px !important;
}

#choosetabs .stacklist {
    list-style: none outside none;
    margin: 0 auto;
    position: relative;
    width: 550px;
}

#choosetabs .stacks {
	position: relative;
	width: 130px;
	height: 100px;
	float: left;
}

#choosetabs .stacks img {
	width: 100px;
	text-align:center;
	border: 6px solid #FFF;
	box-shadow         : 0 1px 2px #999;
	-moz-box-shadow    : 0 1px 2px #999;
	-webkit-box-shadow : 0 1px 2px #999;
	-o-box-shadow      : 0 1px 2px #999;
	position: absolute;
	top: 10px;
	left:10px;
	-ms-interpolation-mode : 'bicubic'
}

#choosetabs .stacks img.active {
	border: 6px solid #000;
	box-shadow         : 0 2px 4px #444;

}

#choosetabs {
	display: block;
	background:#E2E2E2;
	background: rgba( 250, 250, 250, 0.5);
	margin-top: 0;
	padding: 10px;
	text-align:center;
	text-shadow: 0 1px 0 #FFF;
	color: #28303D;
	box-shadow         : 0 2px 5px #888;
	-moz-box-shadow    : 0 2px 5px #888;
	-webkit-box-shadow : 0 2px 5px #888;
	-o-box-shadow      : 0 2px 5px #888;	
	bottom: 0;
	
}

.ui-dialog-buttonset {
	text-align: right;
	padding : 10px;
}

.selector_tab_style {
	font-size: 14px;
	font-weight : normal;
	display : inline;
	text-align : right;
	margin-left : 40px;
}

.tabs-holder,
.accordion-holder,
.spoiler-holder,
.dialog-holder {
	width: 330px;
	height : 350px;
	float : left;
	overflow : hidden;
	margin : 10px 5px;
	padding: 10px;
/*	background: red;*/
/*	border: 2px solid cyan;*/
}

.tabs-holder,
.accordion-holder {
	box-shadow : 0 1px 0 #FF70AA;
	border-bottom: 1px solid #BF678B;
}
.spoiler-holder {
	clear : left;
	margin-top: 20px;
}

.ui-tabs-panel {
	font-size : 12px;
}

.dialog-button {
	display : block;
	height: 100%;
	width : 100%;
	text-align : center;
	padding: 10px;
	
}

.dialog-opener-container {
	display : inline;
}

.dialog-opener-container a {
	text-decoration : none;
	color : inherit;
	text-shadow : inherit;
	font-size : 14px;
	padding : 7px;
}
p.dialog-opener-container {
	padding: 5px;
}
div.description {
	margin-top: 60px;
}
.description p {
	color : #222;
	text-shadow : 0 1px 0 #DDD;
}
.wpui-alma .wp-tab-content {
	font-size : 12px;
}

p.dialog-opener-container {
	padding : 10px 7px !important;
	-moz-border-radius     : 4px !important;
	-webkit-border-radius  : 4px !important;
	-o-border-radius       : 4px !important;
	border-radius          : 4px !important;
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
<body class="options-noise">
<h2 class="styler-title">WP UI CSS3 styles</h2>
<div class="description">
<p style="text-align: center; padding: 5px;">Preview and choose your wpui custom styles here. Select with the <span class="hilite-combo">combo</span> button, then the below button to select it.</p>
<p style="text-align: center; padding: 5px;"><i>Caution : This is a preview. In actual usage, styles might look more awesome.</i></p>
<div><!-- end description -->
<form onsubmit="submit_form()" action="#">
<p class="submit"><input type="submit" value="Choose this skin" id="submit"/></p>

<div id="chosentab">
	
<!-- ###################### -->
<!-- ######## Tabs ######## -->
<!-- ###################### -->

<div class="holster tabs-holder">
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

<div class="wp-spoiler">
<h3 class="wp-spoiler-title">Spoiler section</h3>
<div class="wp-spoiler-content">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam sed elit ut erat viverra dapibus. Cras at blandit sem. Nullam in augue non ipsum fermentum consequat. Nulla eu orci velit. Cras eu neque non justo malesuada pretium ut nec arcu. Curabitur viverra mollis risus vel convallis. Sed et felis dolor. Mauris semper faucibus ipsum non porta. Proin erat quam, congue a venenatis nec, volutpat nec leo. Nam vehicula lorem quis nulla tristique tempor.
</div><!-- end .ui-collapsible-content -->
</div><!-- end div.wp-spoiler -->
</div><!-- end holder -->

<div class="holster dialog-holder">

<div class="dialog-button">
<!-- <p class="dialog-opener-container wpui-macish"> -->
<a class="dialog-opener wpui-macish dialog-opener-3" href="#" class="">Open Dialog</a>

</div>
<div class="wp-dialog wp-dialog-3 wpui-macish%wp-ui-styles%dialog-number-3" title="UI dialog"><h4 class="wp-dialog-title  wpui-dialogClass:wpui-macish%wp-ui-styles%dialog-number-3-arg wpui-width:300-arg wpui-height:auto-arg wpui-autoOpen:false-arg wpui-show:drop-arg wpui-hide:fade-arg wpui-modal:false-arg wpui-closeOnEscape:true-arg wpui-position:center-arg wpui-modal:true-arg wpui-zIndex:1000-arg"></h4> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras tempus, tellus at sagittis imperdiet, turpis augue rutrum lacus, ut tincidunt ligula mi vitae nibh. Praesent nisl velit, pellentesque in semper quis, pretium nec massa. </div>

</div><!-- end holder -->

</div><!-- End chosen tabs -->



<br />
<input type="hidden" id="selected_skin" name="selected_skin" type="text" />		

</form>
</body>
<script type="text/javascript">
(function($) {
jQuery.fn.tabsThemeSwitcher = function(classArr) {
	return this.each(function() {
		var $this = jQuery(this);

		jQuery( '.styler-title' ).append('<span><div class="selector_tab_style">Switch skin : <select id="tabs_theme_select" /></div></span>');
	
	for( i=0; i< classArr.length; i++) {
		jQuery('#tabs_theme_select' ).append('<option value="' + classArr[i] + '">' + classArr[i] + '</option');
	} // END for loop.
	
	if ( jQuery.cookie && jQuery.cookie('tab_demo_style') != null ) {
		currentVal = jQuery.cookie('tab_demo_style');
		jQuery('select#tabs_theme_select option').each(function() {
			if ( currentVal == jQuery(this).attr("value") ) {
			 	jQuery(this).attr( 'selected', 'selected' );
			}
		});
	
	} else {
		currentVal = classArr[0];
	} // END cookie value check.
		
	
		$this.find('.wp-tabs')
			.attr('class', 'wp-tabs')
			.addClass(currentVal, 500);
		$this.find('.wp-accordion')
			.attr('class', 'wp-accordion')
			.addClass(currentVal, 500);
		$this.find('.wp-spoiler')
			.attr('class', 'wp-spoiler')
			.addClass(currentVal, 500);
		$this.find( '.dialog-button' )
			.attr( 'class', 'dialog-button' )
			.addClass( currentVal , 500 );
			
		getDialogClass = jQuery('body').find( '.ui-dialog' ).attr( 'class').replace(/(wpui-[\w\d\-]{3,10}\s)/ig, currentVal + " " );
		
		jQuery( '.ui-dialog' ).attr('class' , getDialogClass );
				
		// console.log( getDialogClass ); 	
		// $this.find('.ui-dialog')
		// 	.attr('class', 'ui-dialog')
		// 	.addClass(currentVal, 500);
	
	// $this.find( '.wp-tabs, .wp-accordion, .wp-spoiler' ).addClass( currentVal, 500 );
	
	
	jQuery('#tabs_theme_select').change(function(e) {
		newVal = jQuery(this).val();
		
		$this.find('.wp-tabs, .wp-accordion, .wp-spoiler, div.dialog-button')
			.hide('drop', {direction: 'up'}, 600)
			.css({ '-moz-rotate' : '45deg'})
			.switchClass(currentVal, newVal, 20)
			.show('drop', {direction: 'up'}, 300)
			.css({ '-moz-rotate': '0deg'});
		
			getDialogClass = jQuery('body').find( '.ui-dialog' ).attr( 'class').replace(/(wpui-[\w\d\-]{3,10}\s)/ig, newVal + " " );

			jQuery( '.ui-dialog' ).attr('class' , getDialogClass );		
		
			
		jQuery( 'input#selected_skin' ).val(newVal);		
		currentVal = newVal;
		
		// jQuery('#choosetabs .stacklist .stacks img').each(function() {
		// 	jQuery(this).removeClass( 'active' );
		// 	if ( newVal.replace(/wpui\-/, '') == jQuery(this).attr('alt').replace(/wpui\-/, '') )
		// 		jQuery(this).addClass('active');
		// });		
		
		if ( jQuery.cookie ) jQuery.cookie('tab_demo_style', newVal, { expires : 2 });
	}); // END on select box change.


	}); // END each function.	
	
};
})(jQuery);
</script>
</html>
