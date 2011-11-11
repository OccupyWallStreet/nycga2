var $j = jQuery.noConflict();

$j(function(){

/*##################  IMAGE TABS  ################################ */
	
	$j("#imageTabs").tabs("#featuredPostPanes .post", {});
	$j(".sidebarTabs").tabs(".sidebarPanes .widget", { event:'mouseover' });
	
/*################## HOVER "SLIDE AWAY" EFFECT  ################################ */	
	$j('.mediaPanes .teaser').hover(function(){
		var $jgoleft = $j(this).find('.hover_link');
		$jgoleft.stop().animate({left:-$jgoleft.outerWidth()},{queue:false,duration:500});
	}, function(){
		var $jgoleft = $j(this).find('.hover_link');
		$jgoleft.stop().animate({left:'0'},{queue:false,duration:500});
	});
	
/*##################  TOOLTIPS  ################################ */	
	$j("#main_col .metaInfo").tooltip({ 
		position: ['bottom', 'right'], 
		offset: [-20, -20],
		
		onShow: function() { 
			this.getTrigger().addClass('metaInfo-active'); 
		},
		onHide: function() { 
			this.getTrigger().removeClass('metaInfo-active'); 
		}
	});
	
/*##################  OVERLAY  ################################ */
	$j("#socialtabs a[rel]").overlay({expose: '#121212'});
	
/*##################  TRACKBACKS ################################ */	
	
	$j("ol.trackback").hide();
	$j("a.show_trackbacks").click(function(){
		$j("ol.trackback").slideToggle('fast');
		return false;
	});
	
}); 