/*
 * DC jQuery Slick Tabs - jQuery Slick Tabs
 * Copyright (c) 2011 Design Chemical
 * 	http://www.designchemical.com
 *
 * Dual licensed under the MIT and GPL licenses:
 * 	http://www.opensource.org/licenses/mit-license.php
 * 	http://www.gnu.org/licenses/gpl.html
 *
 */

(function($){

	//define the new for the plugin ans how to call it
	$.fn.dcSlickTabs = function(options) {

		//set default options
		var defaults = {
			method: 'slide',
			classWrapper: 'dc-social',
			classContent: 'dc-social-content',
			idWrapper: 'dc-social-'+$(this).index(),
			slideWrap: 'slide-wrap',
			classTabContent: 'tab-content',
			location: 'left',
			align: 'top',
			offset: 50,
			speed: 'slow',
			autoClose: true,
			width: 300,
			height: 300,
			direction: 'horizontal',
			start: 0,
			slider: 'dcsmt',
			slides: 'tab-content',
			tabs: 'social-tabs',
			classOpen: 'dcsmt-open',
			classClose: 'dcsmt-close',
			classToggle: 'dcsmt-toggle',
			onLoad : function() {},
            beforeOpen : function() {},
			beforeClose: function() {}
		};

		//call in the default otions
		var options = $.extend(defaults, options);
		var $dcSlickObj = this;
		//act upon the element that is passed into the design
		return $dcSlickObj.each(function(options){

			// declare variables
			var clWrap = defaults.classWrapper;
			var idWrapper = defaults.idWrapper;
			var speed = defaults.speed;
			var offset = defaults.offset+'px';
			var width = defaults.width;
			var height = defaults.height;
			var direction = defaults.direction;
			var linkOpen = $('.'+defaults.classOpen, this);
			var linkClose = $('.'+defaults.classClose, this);
			var linkToggle = $('.'+defaults.classToggle, this);
			
			$(this).addClass(defaults.classContent).wrap('<div id="'+idWrapper+'" class="'+clWrap+'" />');
			
			var $slider = $('#'+idWrapper);
			var $tab = $('.'+defaults.tabs,$slider);
			
			var widthPx = width+'px';
			var heightPx = height+'px';
			var bodyHeight = $(window).height();
			
			if(defaults.method == 'slide'){
				slickSetup($slider);
				sliderSetup();
				
				if(defaults.autoClose == true){
					$('body').mouseup(function(e){
						if($slider.hasClass('active')){
							if(!$(e.target).parents('#'+defaults.idWrapper+'.'+defaults.classWrapper).length){
								slickClose();
							}
						}
					});
				}
				
				$('li a',$tab).click(function(e){
					var i = parseInt($(this).attr('rel'));
					if(!$slider.hasClass('active')){
						slickOpen();
					}
					slickTabs(i);
					e.preventDefault();
				});
				
			$(linkOpen).click(function(e){
				slickOpen();
				e.preventDefault();
			});
			
			$(linkClose).click(function(e){
				if($slider.hasClass('active')){
					slickClose();
				}
				e.preventDefault();
			});
			
			$(linkToggle).click(function(e){
				if($slider.hasClass('active')){
					slickClose();
				} else {
					slickOpen();
				}
				e.preventDefault();
			});
				
			} else {
				staticSetup($slider);
				sliderSetup();
				$('li a',$tab).click(function(e){
					var i = parseInt($(this).attr('rel'));
					slickTabs(i);
					e.preventDefault();
				});
			}
			
			slickTabs(defaults.start);
			
			function slickTabs(i){
				$('li',$tab).removeClass('active');
				$('li:eq('+i+')',$tab).addClass('active');
				tabSlide(i);
			}
	
			function slickOpen(){
			
				$('.'+clWrap).css({zIndex: 10000});
				$slider.css({zIndex: 10001});
				var init = {marginBottom: "-=5px"};
				var params = {marginBottom: 0};
				switch (defaults.location) {
					case 'top': 
					init = {marginTop: "-=5px"};
					params = {marginTop: 0};
					break;
					case 'left':
					init = {marginLeft: "-=5px"};
					params = {marginLeft: 0};					
					break;
					case 'right': 
					init = {marginRight: "-=5px"};
					params = {marginRight: 0};
					break;
				}
				$slider.animate(init, "fast").animate(params, speed).addClass('active');
				
				// onOpen callback;
				defaults.beforeOpen.call(this);
			}
			
			function slickClose(){
			
			$slider.css({zIndex: 10000});
			if($slider.hasClass('active')){
				var params = {"marginBottom": "-"+heightPx};
				switch (defaults.location) {
					case 'top': 
					params = {"marginTop": "-"+heightPx};
					break;
					case 'left':
					params = {"marginLeft": "-"+widthPx};					
					break;
					case 'right': 
					params = {"marginRight": "-"+widthPx};
					break;
				}
				$slider.removeClass('active').animate(params, speed);
			}
			// onClose callback;
			defaults.beforeClose.call(this);
			}
			
			function tabSlide(pos){
				// Set animation based on direction
				var params = direction == 'vertical' ? {'marginTop' : height*(-pos)} : {'marginLeft' : width*(-pos)} ;
				$('#'+defaults.slideWrap).stop().animate(params);
			}
			
			function sliderSetup(){
				var slideContainer = $('.'+defaults.slider);
				var slides = $('.'+defaults.slides);
				var numSlides = slides.length;
				slideContainer.css({height: height+'px', width: width+'px'});
				slides.css({height: height+'px', width: width+'px'});
				// Set CSS of slide-wrap based on direction
				wrapCss = direction == 'vertical' ? {height: height * numSlides} : {width: width * numSlides} ;
				
				// Wrap the slides & set the wrap width
				slides.wrapAll('<div id="'+defaults.slideWrap+'"></div>').css({'float' : 'left','width' : width});
				$('#'+defaults.slideWrap).css(wrapCss);
			}
			
			function slickSetup(obj){
				$tab.css({position: 'absolute'});
				var $container = $('.'+defaults.classContent,obj);
				// Get slider border
				var bdrTop = $slider.css('border-top-width');
				var bdrRight = $slider.css('border-right-width');
				var bdrBottom = $slider.css('border-bottom-width');
				var bdrLeft = $slider.css('border-left-width');
				// Get tab dimension
				var tabWidth = $tab.outerWidth();
				var tabWidthPx = tabWidth+'px';
				var tabHeight = $tab.outerHeight();
				var tabHeightPx = tabHeight+'px';

				$(obj).addClass(defaults.location).addClass('align-'+defaults.align).css({position: 'fixed', zIndex: 10000});

				$container.css({height: heightPx, width: widthPx, position: 'relative'});
				
				switch(defaults.location){
					case 'left':
					objcss = {marginLeft: '-'+widthPx, top: offset, left: 0};
					tabWidth = $('li',$tab).outerWidth();
					tabWidthPx = tabWidth+'px';
					tabcss = {top: 0, right: 0, marginRight: '-'+tabWidthPx};
					break;
					case 'right':
					objcss = {marginRight: '-'+widthPx, top: offset, right: 0};
					tabWidth = $('li',$tab).outerWidth();
					tabWidthPx = tabWidth+'px';
					tabcss = {top: 0, right: 0, marginLeft: '-'+tabWidthPx};
					break;
					case 'top':
					objcss = {marginTop: '-'+heightPx, top: 0};
					tabHeight = $('li',$tab).outerHeight();
					tabHeightPx = tabHeight+'px';
					tabcss = {bottom: 0, marginBottom: '-'+tabHeightPx};
					if(defaults.align == 'left'){
						$(obj).css({left: offset});
						$tab.css({left: 0});
					} else {
						$(obj).css({right: offset});
						$tab.css({right: 0});
					}
					break;
					case 'bottom':
					objcss = {marginBottom: '-'+heightPx, bottom: 0};
					tabHeight = $('li',$tab).outerHeight();
					tabHeightPx = tabHeight+'px';
					tabcss = {top: 0, marginTop: '-'+tabHeightPx};
					
					if(defaults.align == 'left'){
						$(obj).css({left: offset});
						$tab.css({left: 0});
					} else {
						$(obj).css({right: offset});
						$tab.css({right: 0});
					}
					break;
				}
				
				$(obj).css(objcss).addClass('sliding');;
				$tab.css(tabcss).css({height: tabHeightPx, width: tabWidthPx});
			}
			
			function staticSetup(obj){
				$(obj).addClass('static');
				tabHeight = $('li',$tab).outerHeight();
				$tab.css({height: tabHeight+'px'});
			}

		});
	};
})(jQuery);


/*
 * DC Flickr - jQuery Flickr
 * Copyright (c) 2011 Design Chemical
 * http://www.designchemical.com/blog/
 *
 * Dual licensed under the MIT and GPL licenses:
 * 	http://www.opensource.org/licenses/mit-license.php
 * 	http://www.gnu.org/licenses/gpl.html
 *
 */

(function($){

	$.fn.dcFlickr = function(options) {

		//set default options
		var defaults = {
			base: 'http://api.flickr.com/services/feeds/',
			api: 'photos_public.gne',
			limit: 20,
			style: 'thumb',
			q: {
				lang: 'en-us',
				format: 'json',
				jsoncallback: '?'
			}
		};

		//call the default otions
		var options = $.extend(defaults, options);
		var url = defaults.base + defaults.api + '?';
		var qfirst = true;

		for(var key in defaults.q){
			if(!qfirst)
				url += '&';
			url += key + '=' + defaults.q[key];
			qfirst = false;
		}
		
		var $dcFlickr = this;

		return $dcFlickr.each(function(options){

			var html = [];
			limit = defaults.limit;
		
			$.getJSON(url, function(data){
			
				// Cycle each flickr image
				$.each(data.items, function(i,item){
					if(i < limit){
						// var source = item.media.m.replace(/_m\.jpg$/, ".jpg");
						var source = item.media.m;
						switch(defaults.style)
						{
							case 'thumb':
							html.push('<li><a href="' + item.link + '" target="_blank">');
							html.push('<span class="overlay visit"></span>');
							html.push('<img src="' + source + '" alt="" />');
							html.push('</a></li>');
							break;
							case 'portfolio':
							html.push('<div class="box col4"><div class="image"><a href="' + item.link + '" target="_blank">');
							html.push('<span class="overlay details"></span>');
							html.push('<img src="' + source + '" alt="" />');
							html.push('</a></div></div>');
							break;
						}
					}
				});
				if(defaults.style == 'portfolio'){
					$('.items',$dcFlickr).html(html.join(''));
				} else {
					// append html to object
					$dcFlickr.html(html.join(''));
				}

			}).success(function() {
			
			});
		});
	};
})(jQuery);

/* 	Google+ Activity Widget v1.0
	Blog : http://www.moretechtips.net
	Project: http://code.google.com/p/googleplus-activity-widget/
	Copyright 2009 [Mike @ moretechtips.net] 
	Licensed under the Apache License, Version 2.0 
	(the "License"); you may not use this file except in compliance with the License. 
	You may obtain a copy of the License at http://www.apache.org/licenses/LICENSE-2.0 
*/
(function(f){f.fn.googlePlusActivity=function(o){o=f.extend({},f.fn.googlePlusActivity.defaults,o);return this.each(function(){var i=f(this),j=null,n=null,e=o,u=function(a){if(a.error)e.debug&&i.html('<b style="color:red">Error: '+a.error.message+"</b>");else if(a.displayName){n=f('<div class="gpaw-profile"></div>').prependTo(i);n.html((a.image?'<a href="'+a.url+'" class="avatar"><img src="'+t(a.image.url,{sz:e.avatar_max})+'" /></a>':"")+'<div class="name">'+a.displayName+'</div><a href="'+a.url+'" class="add">Add to circles</a>');r()}},t=function(a,b){var c=a+(a.indexOf("?")<0?"?":"&"),g=true;for(var m in b){g||(c+="&");c=c+m+"="+encodeURIComponent(b[m]);g=false}return c},w=function(a){if(a.error)e.debug&&i.html('<b style="color:red">Error: '+a.error.message+"</b>");else if(a.items){var b=a.items.length;if(b!=0){j=f('<ul class="gpaw-body" style="height:'+e.body_height+'px"></ul>');if(n)j.insertAfter(n);else j=j.prependTo(i);j.append('<div class="fade"></div>');for(b=b-1;b>=0;b--){var c=a.items[b],g=c.object.replies?c.object.replies.totalItems:0,m=c.object.plusoners?c.object.plusoners.totalItems:0,p=c.object.resharers?c.object.resharers.totalItems:0,d;d={src:"",imgLink:"",useLink:"",useTitle:""};var k=c.object.attachments;if(k)if(k.length){for(var l=0;l<k.length;l++){var h=k[l];if(h.image){d.src=h.image.url;d.imgLink=h.url;if(h.fullImage){d.w=h.fullImage.width||0;d.h=h.fullImage.height||0}}if(h.objectType=="article")d.useLink=h.url;if(h.displayName)d.useTitle=h.displayName}if(!d.useLink)d.useLink=d.imgLink;if(d.src.indexOf("resize_h")>=0)d.src=d.w>=d.h?d.src.replace(/resize_h=\d+/i,"resize_h="+e.image_height):d.src.replace(/resize_h=\d+/i,"resize_w="+e.image_width)}d=d;j.append("<li>"+(e.show_image&&d.src?'<span class="thumb" style="width:'+(e.image_width+2)+"px; height:"+(e.image_height+2)+'px; overflow:hidden">'+(d.useLink?'<a href="'+d.useLink+'">':"")+'<img src="'+d.src+'" />'+(d.useLink?"</a>":"")+"</span>":"")+'<span class="title">'+(d.useLink?'<a href="'+d.useLink+'">':"")+(c.title?c.title:d.useTitle)+(d.useLink?"</a>":"")+'</span><span class="meta">'+(e.show_plusones?'<span class="plusones">+'+q(m)+"</span>":"")+(e.show_shares?'<span class="shares">'+q(p)+" shares</span>":"")+(e.show_replies?'<span class="replies">'+q(g)+" comments</span>":"")+(e.show_date?'<a class="date" href="'+c.url+'">'+v(c.published)+"</a>":"")+"</span></li>")}r();e.rotate&&s()}}},q=function(a){var b=a;if(a>999999)b=Math.floor(a/1E6)+"M";else if(a>9999)b=Math.floor(a/1E3)+"K";else if(a>999)b=Math.floor(a/1E3)+","+a%1E3;return b},v=function(a){var b=a;if(/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(\.\d*)?(Z|[+-]\d{2}:\d{2})$/i.test(b)){a=b.slice(0,4);var c=["","Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"][1*b.slice(5,7)],g=b.slice(8,10),m=b.slice(11,13),p=b.slice(14,16),d=b.slice(17,19),k="GMT";if(b.indexOf("Z")==-1){var l=b.lastIndexOf(":");k+=b.slice(l-3,l)+b.slice(l+1)}a=g+" "+c+" "+a+" "+m+":"+p+":"+d+" "+k}else a="";b=new Date;b.setTime(Date.parse(a));a=["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];c=Math.floor((new Date-b)/1E3);if(c<0)c=0;return c<60?c+" seconds ago":c/60<60?Math.floor(c/60)+" minutes ago":c/60/60<24?Math.floor(c/60/60)+" hours ago":a[b.getMonth()]+" "+b.getDate()+", "+b.getFullYear()},s=function(){var a=f("li",j),b=a.size();if(!(b<=1)){var c=a.eq(0),g=a.eq(b-1);g.css({display:"none",visibility:"hidden"}).remove().insertBefore(c);g.animate({height:"show"},e.slide_time,"linear",function(){g.css({display:"none",visibility:"visible"});f(this).fadeIn(e.fade_time,x)})}},x=function(){j.animate({opacity:1},e.stay_time,"linear",s)},r=function(){f(".gpaw-info",i).show().css("display","block")};(function(){var a=i.attr("data-options");if(!a){var b=i.html().replace(/\n|\r\n/g,"");if(b)if((b=b.match(/<!--\s*(\{.+\});?\s*--\>/))&&b.length==2)a=b[1]}if(a){if(a.indexOf("{")<0)a="{"+a+"}";try{e=eval("("+a+")")}catch(c){i.html('<b style="color:red">'+c+"</b>");return}e=f.extend({},f.fn.googlePlusActivity.defaults,e)}!e.user&&e.debug&&i.html('<b style="color:red">user ID was not set!</b>');f.ajax({url:"https://www.googleapis.com/plus/v1/people/"+e.user+"/activities/public",data:{key:e.api_key,maxResults:e.n,prettyprint:false,fields:"items(id,kind,object(attachments(displayName,fullImage,id,image,objectType,url),id,objectType,plusoners,replies,resharers,url),published,title,url,verb)"},success:w,cache:true,dataType:"jsonp"});e.show_profile&&f.ajax({url:"https://www.googleapis.com/plus/v1/people/"+e.user,data:{key:e.api_key,prettyprint:false,fields:"displayName,image,tagline,url"},success:u,cache:true,dataType:"jsonp"})})()})};f.fn.googlePlusActivity.defaults={debug:0,api_key:"AIzaSyCBu5eYYIoZmeWrCPBX3UxjPDpnEFAxtYM",user:"",n:20,rotate:1,stay_time:5E3,slide_time:200,fade_time:500,show_profile:1,show_date:1,show_replies:1,show_plusones:1,show_shares:1,show_image:1,image_width:75,image_height:75,avatar_max:50,body_height:300}})(jQuery);jQuery(document).ready(function(){jQuery("div.google-plus-activity").googlePlusActivity()});

/**
 * jCarouselLite - jQuery plugin to navigate images/any content in a carousel style widget.
 * @requires jQuery v1.2 or above
 * @cat Plugins/Image Gallery
 * @author Ganeshji Marwaha/ganeshread@gmail.com
 */

(function($) {                                          // Compliant with jquery.noConflict()
$.fn.jCarouselLitedcsmt = function(o) {
    o = $.extend({
        btnPrev: null,
        btnNext: null,
        btnGo: null,
        mouseWheel: false,
        auto: null,
        speed: 200,
        easing: null,
        vertical: false,
        circular: true,
        visible: 3,
        start: 0,
        scroll: 1,
		width: 200,
		height: 300,
        beforeStart: null,
        afterEnd: null
    }, o || {});

    return this.each(function() {                           // Returns the element collection. Chainable.

		var running = false, animCss=o.vertical?"top":"left", sizeCss=o.vertical?"height":"width";
        var div = $(this), ul = $(".carousel > ul", div), tLi = $("> li", ul), tl = tLi.size(), v = o.visible;
		var w = o.width;
		var h = o.height;
		
        if(o.circular) {
            ul.prepend(tLi.slice(tl-v-1+1).clone())
              .append(tLi.slice(0,v).clone());
            o.start += v;
        }
		
        var li = $("> li", ul), itemLength = li.size(), curr = o.start;
        div.css("visibility", "visible");

        li.css({overflow: "hidden", float: o.vertical ? "none" : "left"});
        ul.css({margin: "0", padding: "0", position: "relative", "list-style-type": "none"});
        div.css({overflow: "hidden", position: "relative"}).addClass('dctsp-active');
		$(".carousel", div).css({width: w+'px'});

        var liSize = o.vertical ? height(li) : w;   // Full li size(incl margin)-Used for animation
        var ulSize = liSize * itemLength;                   // size of full ul(total length, not just for the visible items)
        var divSize = liSize * v;                           // size of entire div(total length for just the visible items)

        li.css({width: w+'px', height: h+'px'});
        ul.css(sizeCss, ulSize+"px").css({height: h+'px'}).css(animCss, -(curr*liSize));

        div.css(sizeCss, divSize+"px");                     // Width of the DIV. length of visible images
		
        if(o.btnPrev)
            $(o.btnPrev).click(function() {
                return go(curr-o.scroll);
            });

        if(o.btnNext)
            $(o.btnNext).click(function() {
                return go(curr+o.scroll);
            });

        if(o.btnGo)
            $.each(o.btnGo, function(i, val) {
                $(val).click(function() {
                    return go(o.circular ? o.visible+i : i);
                });
            });

        if(o.mouseWheel && div.mousewheel)
            div.mousewheel(function(e, d) {
                return d>0 ? go(curr-o.scroll) : go(curr+o.scroll);
            });

        if(o.auto)
            setInterval(function() {
                go(curr+o.scroll);
            }, o.auto+o.speed);

        function vis() {
            return li.slice(curr).slice(0,v);
        };

        function go(to) {
            if(!running) {

                if(o.beforeStart)
                    o.beforeStart.call(this, vis());

                if(o.circular) {            // If circular we are in first or last, then goto the other end
                    if(to<=o.start-v-1) {           // If first, then goto last
                        ul.css(animCss, -((itemLength-(v*2))*liSize)+"px");
						
                        // If "scroll" > 1, then the "to" might not be equal to the condition; it can be lesser depending on the number of elements.
                        curr = to==o.start-v-1 ? itemLength-(v*2)-1 : itemLength-(v*2)-o.scroll;
                    } else if(to>=itemLength-v+1) { // If last, then goto first
                        ul.css(animCss, -( (v) * liSize ) + "px" );
		
                        // If "scroll" > 1, then the "to" might not be equal to the condition; it can be greater depending on the number of elements.
                        curr = to==itemLength-v+1 ? v+1 : v+o.scroll;
                    } else curr = to;
				
                } else {                    // If non-circular and to points to first or last, we just return.
                    if(to<0 || to>itemLength-v) return;
                    else curr = to;
                }                           // If neither overrides it, the curr will still be "to" and we can proceed.
				
                if(curr==(itemLength-1)){
					//var p = parseInt(itemLength-2);
					var n = parseInt(2);
				} else if(curr==0){
					var p = parseInt(itemLength-3);
					//var n = parseInt(2);
				}
				
				running = true;

                ul.animate(
                    animCss == "left" ? { left: -(curr*liSize) } : { top: -(curr*liSize) } , o.speed, o.easing,
                    function() {
                        if(o.afterEnd)
                            o.afterEnd.call(this, vis());
                        running = false;
                    }
                );
				
                // Disable buttons when the carousel reaches the last/first, and enable when not
                if(!o.circular) {
                    $(o.btnPrev + "," + o.btnNext).removeClass("disabled");
                    $( (curr-o.scroll<0 && o.btnPrev)
                        ||
                       (curr+o.scroll > itemLength-v && o.btnNext)
                        ||
                       []
                     ).addClass("disabled");
                }

            }
            return false;
        };
    });
};

function css(el, prop) {
    return parseInt($.css(el[0], prop)) || 0;
};
function height(el) {
    return el[0].offsetHeight + css(el, 'marginTop') + css(el, 'marginBottom');
};

})(jQuery);
jQuery(document).ready(function($){
$('.tab-flickr a').css("opacity","1.0");	
	$('.tab-flickr a').hover(function(){									  
		$(this).stop().animate({ opacity: 0.75 }, "fast"); },	
	function () {
		$(this).stop().animate({ opacity: 1.0 }, "fast");
	});
	$('li.dcsmt-plusone a').click(function(){
	$('.gpaw-body li').removeClass('odd');
	$('.gpaw-body li:odd').addClass('odd');
	});

});