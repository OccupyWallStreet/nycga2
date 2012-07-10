jQuery(document).ready(function( $ ) {

	if ( wpUIOpts.enablePagination == 'on' )
		jQuery( 'div.wpui-pages-holder' ).wpuiPager();
	
	if ( wpUIOpts.enableTabs == 'on')
		jQuery('div.wp-tabs').wptabs();
	
	if ( wpUIOpts.enableSpoilers == 'on' )
		jQuery('.wp-spoiler').wpspoiler();
	
	if ( wpUIOpts.enableAccordion == 'on')
		jQuery('.wp-accordion').wpaccord();
	
	if ( wpUIOpts.enableDialogs == 'on' )
		jQuery('.wp-dialog').wpDialog();
		
		

		
});

jQuery.fn.tabsThemeSwitcher = function(classArr) {
	
	return this.each(function() {
		var $this = jQuery(this);

		$this.prepend('<div class="selector_tab_style">Switch Theme : <select id="tabs_theme_select" /></div>');
	
	for( i=0; i< classArr.length; i++) {
		jQuery('#tabs_theme_select', this).append('<option value="' + classArr[i] + '">' + classArr[i] + '</option');
		
	} // END for loop.
	

	if ( jQuery.cookie && jQuery.cookie('tab_demo_style') != null ) {
		currentVal = jQuery.cookie('tab_demo_style');
		$this.find('select#tabs_theme_select option').each(function() {
			if ( currentVal == jQuery(this).attr("value") ) {
			 	jQuery(this).attr( 'selected', 'selected' );
			}
		});
	} else {
		currentVal = classArr[0];
	} // END cookie value check.

	
	$this.children('.wp-tabs').attr('class', 'wp-tabs wpui-styles').addClass(currentVal, 500);
	$this.children('.wp-accordion').attr('class', 'wp-accordion wpui-styles').addClass(currentVal, 500);
	$this.children('.wp-spoiler').attr('class', 'wp-spoiler wpui-styles').addClass(currentVal, 500);

	
	jQuery('#tabs_theme_select').change(function(e) {
		newVal = jQuery(this).val();
		
		$this.children('.wp-tabs, .wp-accordion, .wp-spoiler').switchClass(currentVal, newVal, 1500);
		
		currentVal = newVal;
		
		if ( jQuery.cookie ) jQuery.cookie('tab_demo_style', newVal, { expires : 2 });
	}); // END on select box change.
	

	}); // END each function.	
	
};

var tb_remove = function() {
 	jQuery("#TB_imageOff").unbind("click");
	jQuery("#TB_closeWindowButton").unbind("click");
	jQuery("#TB_window")
		.fadeOut("fast",function(){
				jQuery('#TB_window,#TB_overlay,#TB_HideSelect')
					.unload("#TB_ajaxContent")
					.unbind()
					.remove();
		});
	jQuery("#TB_load").remove();
	if (typeof document.body.style.maxHeight == "undefined") {//if IE 6
		jQuery("body","html").css({height: "auto", width: "auto"});
		jQuery("html").css("overflow","");
	}
	jQuery(document).unbind('.thickbox');
	return false;
}; // END function tb_remove()

