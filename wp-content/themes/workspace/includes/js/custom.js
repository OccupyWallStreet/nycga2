//Begin!
jQuery(document).ready(function(){

/*= Loading Image
--------------------------------------------------------------------- */
    jQuery.fn.LoadingSingleImg = function() {
            return this.each(function() {
                var that = this;
                var src = jQuery(this).attr("src");
                $width = jQuery(this).width();
                $height = jQuery(this).height();
                var img = new Image();
                img.src = src;
                var loading = jQuery("<div class='loading-img-div' style='position:relative;display: block;height:"+$height+"px;width:"+$width+"px;'><img alt='loading...' class='loading-img' title='loading...' src='"+jQuery('#home-url').attr('name')+"/images/preload.gif'/></div>");
                jQuery(this).after(loading);
                jQuery(this).hide();
                jQuery(img).load(function() {
                    jQuery(that).attr("src", src);
                    jQuery(that).fadeIn(100).queue(function(){
                        loading.remove();
                        jQuery(this).dequeue();
                    });
                });
            });
        };
    if(jQuery.browser.msie&&(jQuery.browser.version!='0.9')){
        jQuery("img").lazyload({
            effect : "fadeIn"
        });
    }else{
        jQuery("#home-recent-works img").LoadingSingleImg();
        jQuery("#home-recent-posts img").LoadingSingleImg();
        jQuery("li.item>a>img").LoadingSingleImg();
        jQuery('.page-template-template-blog-php .entry-thumb').LoadingSingleImg();
    }

/*= Menu
---------------------------------------------------------------------*/
    function menu_function(){
	    jQuery('ul.nav').superfish();
        jQuery('ul.topnav').superfish(); 
    }
    menu_function();

/*= Slider
---------------------------------------------------------------------*/
    //init the slider ,hide all ,show first
    function slider_init(){
        if(jQuery.browser.msie&&(jQuery.browser.version!='9.0')){
            jQuery('#slider-box').show();
            jQuery('div.slides').eq(0).show();
        }else{
            jQuery('#slider-box').slideDown('slow').queue(function(){
                jQuery('div.slides').eq(0).fadeIn();
                jQuery(this).dequeue();
            });
        }
    }
    slider_init();
    //click next buttom function
    function slider_next_function(){
        $num = parseInt(jQuery('div.slides').size());
        $current_slider = 0;
        $next_slider = 0;
        $height = 0;
        jQuery('#slider-next').click(function(){
            jQuery('div.slides').each(function(index){
                if(jQuery(this).is(':visible')){
                    $current_slider = index;
                }
            });
            if($current_slider==($num-1)){
                $next_slider = 0;
            }else{
                $next_slider = $current_slider + 1;
            }

            $height = jQuery('div.slides').eq($next_slider).height();

            jQuery('#slider-box').animate({
                height:$height+'px'
            },600);

            jQuery('div.slides').eq($current_slider).hide().queue(function(){
                jQuery('div.slides').eq($next_slider).stop(true,true).fadeIn();
                jQuery(this).dequeue();
            });
            
        });
    }
    slider_next_function();

    //click prev button function
    function slider_prev_function(){
        $num = parseInt(jQuery('div.slides').size());
        $current_slider = 0;
        $next_slider = 0;
        $height = 0;
        jQuery('#slider-prev').click(function(){

            jQuery('div.slides').each(function(index){
                if(jQuery(this).is(':visible')){
                    $current_slider = index;
                }
            });
            if($current_slider==0){
                $next_slider = $num-1;
            }else{
                $next_slider = $current_slider - 1;
            }
            
            $height = jQuery('div.slides').eq($next_slider).height();

            jQuery('#slider-box').animate({
                height:$height+'px'
            },600);

            jQuery('div.slides').eq($current_slider).hide().queue(function(){
                jQuery('div.slides').eq($next_slider).stop(true,true).fadeIn();
                jQuery(this).dequeue();
            });


        });
    }
    slider_prev_function();

/*= PrettyPhoto Lightbox 
---------------------------------------------------------------------*/

	function tj_prettyPhoto() {
		jQuery("a[rel^='prettyPhoto']").prettyPhoto({
            show_title: false,
            social_tools: false
        });
	}
	tj_prettyPhoto();

/*= Overlay Animation
---------------------------------------------------------------------*/

    function tj_overlay(){
        if(!($.browser.msie && ($.browser.version!='9.0'))){
            jQuery('li.portfolio img,li.post img,li.item img').parent('a').hover(
                function(){
                    jQuery(this).find('.overlay').stop(true,true).fadeIn();
                },function(){
                    jQuery(this).find('.overlay').stop(true,true).fadeOut();
            });
        }
    }

	tj_overlay();

/*= Menu Flag
---------------------------------------------------------------------*/

    function menu_flag(){
        jQuery('#header-nav>li').each(function(){
            $this = jQuery(this);
            $this.has('ul').children('a').append('<b></b>').queue(function(){
                jQuery('#header-nav>li').hover(function(){
                    jQuery(this).find('b').addClass('menu-flag-hover');
                },function(){
                    jQuery(this).find('b').removeClass('menu-flag-hover');
                })
            });
        })

    }
    menu_flag();


    function control_quicksand(){

        jQuery('#filter').children('li').each(function(){
            $text = jQuery(this).find('a').text();
            $class = jQuery(this).attr('class');
            $class = $class.replace('cat-item','');
            jQuery(this).find('a').attr('href','');
            jQuery(this).find('a').attr('class',$class);
            jQuery(this).attr('class','');
        });
        
        jQuery('#filter').append('<li class="active" ><a class="all">All</a></li>');

        var $filterType = jQuery('#filter li.active a').attr('class');

        var $holder = jQuery('ul.ourHolder');

        var $data = $holder.clone();

        jQuery('#filter>li a').click(function(e) {
            
            jQuery('#filter li').removeClass('active');
            var $filterType = jQuery(this).attr('class');

            jQuery(this).parent().addClass('active');

            
            if ($filterType == 'all') {

                var $filteredData = $data.children('li');

            }else {

                var $filteredData = $data.find('li[data-type*=' + $filterType + ']');

            }

            $holder.quicksand($filteredData,{
                duration: 500,
                easing: 'easeInOutQuad'
            }, function() {
                tj_prettyPhoto();
	            tj_overlay();
                        
            });
           
            return false;

        });

    }
    control_quicksand();

/*= Correct Css
---------------------------------------------------------------------*/
    function correct_css(){
        jQuery('embed').each(function(){
            jQuery(this).attr('wmode','opaque');
        });
    }
    correct_css();

/*= Slides Gallery
---------------------------------------------------------------------*/

    function slides_gallery(){
        $url = jQuery('#home-url').attr('name');
        jQuery('#gallery').slides({
                preload: true,
                preloadImage: $url+'/images/preload.gif',
                generatePagination: false,
                autoHeight: true,
                next: 'gallery-flag-next',
                prev: 'gallery-flag-prev'

			});
    }
    slides_gallery();

/*= Correct css
---------------------------------------------------------------------*/
    function correct_css(){
        jQuery('embed').each(function(){
            jQuery(this).attr('wmode','opaque');
        });
    }
    correct_css();

/*= Show Calendar Name
---------------------------------------------------------------------*/

    function show_calendar_name(){
        //jQuery('.widget_calendar').children('h3').text('Calendar');
    }
    show_calendar_name();

/*= Remove Entry Img
---------------------------------------------------------------------*/

    function remove_entry_img(){
        //jQuery('div.entry p img,div.slides-post-content-img img').remove();
    }
    remove_entry_img();


//End ready!
})
