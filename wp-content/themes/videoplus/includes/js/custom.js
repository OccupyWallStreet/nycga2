/* Begin */
jQuery(document).ready(function(){
//Home Url
$home_url = jQuery('#home-url').attr('name');
    
/*= Preload
*************************************************/
    function addLoadEvent(func){
        var oldonload = window.onload;
        if(typeof window.onload != 'function'){
            window.onload = func;
        }else{
            window.onload = function(){
                oldonload();
                func();
            }
        }
    }

/*= Correct Css
*************************************************/
    function correct_css(){
        jQuery('embed').each(function(){
            jQuery(this).attr('wmode','opaque');
        });
        for(var i=0;i<3;i++){
            jQuery('div.entry-box').eq(i).css('border-top','none').css('padding-top','0');
        }
        jQuery('div.entry-box').each(function(index){
            if((index+1)%3 == 0){
                jQuery(this).css('margin-right','0');
            }
        }).queue(function(){
            jQuery('#main-article').slideDown();
            jQuery(this).dequeue();
        });
    }
    correct_css();
    
/*= jQuery Superfish menu
*************************************************/
    function init_nav(){
        jQuery('ul.nav').superfish({ 
	        delay:       100,                             // one second delay on mouse out 
	        animation:   {opacity:'show',height:'show'},  // fade-in and slide-down animation 
	        speed:       'fast'                           // faster animation speed 
    	});
    }
    init_nav();

/*= Slider Function
*************************************************/
    function slider_init(){
        jQuery("#slider").fadeIn();
    }
    addLoadEvent(slider_init);
    
    function slider(){
        jQuery("#slider").slides({
                preload: true,
                preloadImage: $home_url+'/images/loader-white.gif',
                generatePagination: false,
                paginationClass: 'slider-nav',
				pause: 2500,
				hoverPause: true,
                effect: 'slide, fade'
        });
    }
    slider();

/*= Change Box Style
*************************************************/
    function change_box_style(){
        jQuery('div.heading-switch').click(function(){
            $this = jQuery(this);
            if($this.hasClass('heading-box')){
                jQuery('div.heading-box').addClass('heading-list').removeClass('heading-box');
                jQuery.when(jQuery('div.entry-box').fadeOut().queue(function(){
                    jQuery(this).addClass('entry-list').removeClass('entry-box');
                    jQuery(this).fadeIn();
                    jQuery(this).dequeue();
                })).then(function(){
                    jQuery('div.entry-list:last').css('border-bottom','none').css('margin-bottom','none');
                })
            }else if($this.hasClass('heading-list')){
                jQuery('div.heading-list').addClass('heading-box').removeClass('heading-list');
                jQuery('div.entry-list').fadeOut().queue(function(){
                    jQuery(this).addClass('entry-box').removeClass('entry-list');
                    jQuery(this).fadeIn();
                    jQuery(this).dequeue();
                })
            }

        })
    }
    change_box_style();

/*= Menu Flag
*************************************************/
    function menu_flag(){
        jQuery('#primary-navigation>li, #secondary-navigation>li').each(function(){
            jQuery(this).has('ul').children('a').append('<b></b>');
            jQuery('#primary-navigation>li, #secondary-navigation>li').hover(function(){
                jQuery(this).find('b').addClass('menu-flag-hover');
            },function(){
                jQuery(this).find('b').removeClass('menu-flag-hover');
            })
        });
    }
    menu_flag();

/*= Header Search Form
*************************************************/
    function change_search(){
        $input_node = jQuery('#search-form input[type="text"]');
        $input_node.focus(function(){
            jQuery(this).stop(true,true).animate({
                width:'255px'
            },100);
        });
        $input_node.blur(function(){
            jQuery(this).stop(true,true).animate({
                width:'230px'
            },100);
        })
    }
    change_search();

/*= Iframe Correct
*************************************************/
    function iframe_correct(){
        jQuery("iframe").each(function(){
            var ifr_source = jQuery(this).attr('src');
            var wmode = "wmode=transparent";
            if(ifr_source.indexOf('?') != -1) {
                var getQString = ifr_source.split('?');
                var oldString = getQString[1];
                var newString = getQString[0];
                jQuery(this).attr('src',newString+'?'+wmode+'&'+oldString);
            }else{
                jQuery(this).attr('src',ifr_source+'?'+wmode);
            }
        });
    }
    iframe_correct();

/*= FancyBox
*************************************************/
    function fancybox_init(){
        jQuery(".various").fancybox({
		    maxWidth	: 573,
		    maxHeight	: 325,
		    fitToView	: false,
		    width		: '70%',
		    height		: '70%',
		    autoSize	: false,
		    closeClick	: false,
		    openEffect	: 'none',
		    closeEffect	: 'none',
            arrows: false
	    });
    }
    fancybox_init();
    
});
