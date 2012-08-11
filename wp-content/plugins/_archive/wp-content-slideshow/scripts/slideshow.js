// Tutorial by http://ooyes.net/

$slideshow = {
	context: false,
	tabs: false,
	timeout: 8000,
	fx: 'fade',  
	slideSpeed: 900,
	tabSpeed: 900,   
    
    init: function() {
        this.context = jQuery('#content-slideshow');
        this.tabs = jQuery('ul.slideshow-nav li', this.context);
        this.tabs.remove();
        this.startSlideshow();
    },
    
    startSlideshow: function() {
        jQuery('div.content_slideshow > ul', $slideshow.context).cycle({
            fx: $slideshow.fx,
            pager: jQuery('ul.slideshow-nav', $slideshow.context),
            pagerAnchorBuilder: $slideshow.startTabs,
            before: $slideshow.Tabactive,
	    timeout: $slideshow.timeout,
            speed: $slideshow.slideSpeed,
            fastOnEvent: $slideshow.tabSpeed,
            pauseOnPagerHover: true,
            pause: true
        });            
    },
    
    startTabs: function(i, slide) {
        return $slideshow.tabs.eq(i);
    },

    Tabactive: function(currentSlide, nextSlide) {
        var activeTab = jQuery('a[href="#' + nextSlide.id + '"]', $slideshow.context);
        if(activeTab.length) {
            $slideshow.tabs.removeClass('on');
            activeTab.parent().addClass('on');
        }            
    }            
};

jQuery(document).ready(function($) {
    $slideshow.init();
});  