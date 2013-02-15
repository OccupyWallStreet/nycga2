var slickr_flickr_slideshow_timer;
var slickr_flickr_slideshow_timer_on = false;

jQuery.noConflict();

function  slickr_flickr_next_slide(obj) {
    var j = jQuery(obj);
    if (j.children('div').length == 1)  return ;
    options = j.data("options");    
    if (('autoplay' in options) && (options['autoplay'] == false)) return;   
    var $active = j.children('div.active');
    if ( $active.length == 0 ) $active = j.children('div:last');
    var $next =  $active.next().length ? $active.next() : j.children('div:first');

    $active.addClass('last-active');
    $next.css({opacity: 0.0})
        .addClass('active')
        .animate({opacity: 1.0}, options['transition'], function() {
            $active.removeClass('active last-active');
        });
}

function slickr_flickr_next_slides() {
   jQuery('.slickr-flickr-slideshow').each(function(index){
        slickr_flickr_next_slide(jQuery(this)) ;
   });
}

function  slickr_flickr_set_slideshow_height(slideshow,imgheight,divheight) {
    var s = jQuery(slideshow);
    s.find('div img').css("max-height",imgheight+"px");
    s.css("height", divheight+"px");
}    
function  slickr_flickr_set_slideshow_width(slideshow,width) {
    var s = jQuery(slideshow);
    s.find('div img').css("max-width",width+"px");
    s.css("width",width+"px");
} 

function  slickr_flickr_set_slideshow_click(slideshow,link,target) {
    var s = jQuery(slideshow);
    if (link=='next') 
    	s.unbind('click').click( function() {  slickr_flickr_next_slide(s) ; });
	else if (link=='toggle') 
		s.unbind('click').click( function() {  slickr_flickr_toggle_slideshows() ; });
	else 
		if (target == "_self")
			s.unbind('click').click( function() {  window.location = link.replace(/\\/g, ''); }); 
		else
			s.unbind('click').click( function() {  window.open(link.replace(/\\/g, ''),target); }); 		
}

function slickr_flickr_toggle_slideshows() {
   if (slickr_flickr_slideshow_timer_on)
       slickr_flickr_stop_slideshows();
   else
       slickr_flickr_start_slideshows();
}

function slickr_flickr_stop_slideshows() {
    clearTimeout(slickr_flickr_slideshow_timer);
    slickr_flickr_slideshow_timer_on = false;
}

function slickr_flickr_start_slideshows() {
   var mindelay = 0;
   jQuery('.slickr-flickr-slideshow').each(function(index){
        var s =jQuery(this);
   		options = s.data('options');
   		if (options) {
    		if (('link' in options) && (options['link'] != ''))  slickr_flickr_set_slideshow_click(s,options['link'],options['target']);
    		if (('width' in options) && (options['width'] != ''))  slickr_flickr_set_slideshow_width(s,options['width']);
     		if (('height' in options) && (options['height'] != ''))  {
     			imgheight = parseInt(options['height']);
     			divheight = imgheight+ (s.hasClass("nocaptions") ? 0 : 30);
     			if (s.hasClass("descriptions")) divheight += 50;
 	    		slickr_flickr_set_slideshow_height(s,imgheight,divheight);
 			}
    		if (('delay' in options) && (options['delay'] != '')) {
    			delay = options['delay'];
    		    if ((!(delay == undefined)) && ((mindelay == 0) || (delay < mindelay))) mindelay = delay;
    		} 		
		}
    });
	if (mindelay > 0) {
		slickr_flickr_stop_slideshows();
	    slickr_flickr_slideshow_timer = setInterval("slickr_flickr_next_slides()",mindelay);
	    slickr_flickr_slideshow_timer_on = true;
    }
}

function slickr_flickr_start() {    
    jQuery(".slickr-flickr-gallery").find('img').hover( 
		function(){ jQuery(this).addClass('hover');},
		function(){ jQuery(this).removeClass('hover');}); 	
		
    jQuery(".slickr-flickr-gallery").each( function (index) {	
        $options = jQuery(this).data("options");
  		if ($options && ('border' in $options) && ($options['border'] != '')) {
  			$id = jQuery(this).attr('id');
	 		jQuery('<style type="text/css">#'+$id+' img.hover{ background-color:'+$options['border']+'; }</style>').appendTo('head');
 			}
 	});
 	
    if (jQuery('.slickr-flickr-galleria').size() > 0) {
    	jQuery(".slickr-flickr-galleria").each(function(index){
    	    var $options = jQuery(this).data("options");
    	    jQuery(this).galleria($options);
    	});
    } 	
 	
    if (jQuery('a[rel="sf-lightbox"]').size() > 0) {
        jQuery(".slickr-flickr-gallery,.slickr-flickr-slideshow").each( function (index) {
 	        var $options = jQuery(this).data("options");
 			jQuery(this).find('a[rel="sf-lightbox"]').lightbox($options);
        });
    }   
    slickr_flickr_start_slideshows();
}