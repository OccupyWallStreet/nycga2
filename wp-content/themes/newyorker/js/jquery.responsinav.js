/*
*
* ResponsiNav Plugin
* @author - Kent Safranski - http://www.fluidbyte.net
*
*/

(function( $ ){
    $.fn.responsinav = function(o){
    
        var o = jQuery.extend({ breakpoint : 480 },o);

        // 0 = Full, 1 = Mobile
        rn_mode = undefined; sub_nav_bind=false /* init */
        
        // Bind to load and resize
        $(window).bind('load resize', function(){
            // Run mode by width
            if($(window).width()<=o.breakpoint){
                if(rn_mode==0 || rn_mode==undefined){ nav.reset(); nav.mobile(); } 
            }else{
                if(rn_mode==1 || rn_mode==undefined){ nav.reset(); nav.full(); } 
            }
        });
        
        nav = {
        
            reset : function(){ 
                $('nav>ul li').unbind('mouseenter mouseleave click');
                $('nav .sub_nav').unbind('click').remove();
            },
    
            full : function(){
                // Set mode
                rn_mode = 0;
                // Ensure nav is visible, hide sub
                $('nav>ul').show(); $('nav ul ul').hide();
                // Behavior
                $("nav>ul li").hover(function() {
                var timeout = $(this).data("timeout");
                 if(timeout) clearTimeout(timeout);
                   $(this).children("ul").slideDown(300).css({ 'z-index':curz++ });
                 }, function() {
                   $(this).data("timeout", setTimeout($.proxy(function() {
                   $(this).find("ul").slideUp(300);
                 }, this), 300));
               });
            },
            
            mobile : function(){            
                
                // Set mode
                rn_mode = 1;
                // Start nav hidden
                $('nav>ul').hide();
                // Create mobile handle
                if($('nav>a.mobile_handle').length==0){ $('<a class="mobile_handle">Navigation</a>').appendTo('nav'); }
                
                // Mobile handle toggle
                $('nav>a.mobile_handle').unbind('click');
                $('nav>a.mobile_handle').click(function(){ $('nav>ul').slideToggle(300); });
                
                // Arrows
                if($('.sub_nav').length==0){
                    $('nav ul li').each(function(){
                        if($(this).children('ul').length>0){ $('<a class="sub_nav"><div class="arrow_down"></div></a>').appendTo(this); }
                    });
                }
                
                // Sub-Nav
                if(sub_nav_bind==false){              
                    $('nav>ul').delegate('.sub_nav', 'click', function(e) {               
                        $(this).siblings('ul').slideToggle(300);               
                        if ($(this).children('div').hasClass('arrow_down')){
                            $(this).children('div').attr('class', 'arrow_up');
                        }else{
                            $(this).children('div').attr('class', 'arrow_down');
                        }
                    });
                    sub_nav_bind = true;
                } 
            }      
        };       
    };
})( jQuery );