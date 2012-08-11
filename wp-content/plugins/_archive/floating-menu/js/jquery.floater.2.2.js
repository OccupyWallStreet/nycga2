/*
 * DC jQuery Floater - jQuery Floater
 * Copyright (c) 2011 Design Chemical
 *
 * Dual licensed under the MIT and GPL licenses:
 * 	http://www.opensource.org/licenses/mit-license.php
 * 	http://www.gnu.org/licenses/gpl.html
 *
 */

(function($){

	//define the new for the plugin ans how to call it
	$.fn.dcFloater = function(options) {

		//set default options
		var defaults = {
			classWrapper: 'dc-floater',
			classContent: 'dc-floater-content',
			width: 200,
			idWrapper: 'dc-floater-'+$(this).index(),
			location: 'top', // top, bottom
			align: 'left', // left, right
			offsetLocation: 10,
			offsetAlign: 10,
			center: false,
			centerPx: 0,
			speedFloat: 1500,
			speedContent: 600,
			tabText: 'Click',
			event: 'click',
			classTab: 'tab',
			classOpen: 'dc-open',
			classClose: 'dc-close',
			classToggle: 'dc-toggle',
			autoClose: true,
			loadOpen: false,
			tabClose: true,
			disableFloat: false,
			easing: 'easeOutQuint',
			onLoad : function() {},
            onOpen : function() {},
			onClose: function() {},
			onFloat: function() {}
		};

		//call in the default otions
		var options = $.extend(defaults, options);
		
		//act upon the element that is passed into the design    
		return this.each(function(options){

			var floatHtml = $(this).html();
			var floatTab = '<div class="'+defaults.classTab+'"><span>'+defaults.tabText+'</span></div>';
			$(this).hide();
			var idWrapper = defaults.idWrapper;
			if(defaults.location == 'bottom'){
				var objHtml = '<div id="'+idWrapper+'" class="'+defaults.classWrapper+' '+defaults.location+' '+defaults.align+'"><div class="'+defaults.classContent+'">'+floatHtml+'</div>'+floatTab+'</div>';
			} else {
				var objHtml = '<div id="'+idWrapper+'" class="'+defaults.classWrapper+' '+defaults.align+'">'+floatTab+'<div class="'+defaults.classContent+'">'+floatHtml+'</div></div>';
			}
			$('body').append(objHtml);
			
			//cache vars
			var $floater = $('#'+idWrapper);
			var $tab = $('.'+defaults.classTab,$floater);
			var $content = $('.'+defaults.classContent,$floater);
			var linkOpen = $('.'+defaults.classOpen);
			var linkClose = $('.'+defaults.classClose);
			var linkToggle = $('.'+defaults.classToggle);
			
			var csspos = defaults.disableFloat == false ? 'absolute' : 'fixed' ;
			$floater.css({width: defaults.width+'px', position: csspos, zIndex: 10000});
			
			var h_c = $content.outerHeight(true);
			var h_f = $floater.outerHeight();
			var h_t = $tab.outerHeight();
			
			if(defaults.tabClose == true){
				$content.hide();
			}
			
			floaterSetup($floater);
		
			var start = $('#'+idWrapper).position().top;

			if(defaults.disableFloat == false){
				floatObj();
				$(window).scroll(function (){
					floatObj(1);
				});
			}
			
			if(defaults.loadOpen == true){
				floatOpen();
			}
			
			if(defaults.tabClose == true){
			// If using hover event
			if(defaults.event == 'hover'){
				var config = {sensitivity: 2, interval: 100, over: floatOpen, timeout: 400, out: floatClose};
				$floater.hoverIntent(config);
			}
			
			// If using click event
			if(defaults.event == 'click'){
				
				$tab.click(function(e){
					if($floater.hasClass('active')){
						floatClose();
					} else {
						floatOpen();
					}
					e.preventDefault();
				});
				
			}
			
			$(linkOpen).click(function(e){
				if($floater.not('active')){
					floatOpen();
				}
				e.preventDefault();
			});
				
			$(linkClose).click(function(e){
				if($floater.hasClass('active')){
					floatClose();
				}
				e.preventDefault();
			});
				
			$(linkToggle).click(function(e){
				if($floater.hasClass('active')){
					floatClose();
				} else {
					floatOpen();
				}
				e.preventDefault();
			});
			
			// Auto-close
			if(defaults.autoClose == true){
	
				$('body').mouseup(function(e){
					if($floater.hasClass('active')){
						if(!$(e.target).parents('#'+defaults.idWrapper+'.'+defaults.classWrapper).length){
							floatClose();
						}
					}
				});
			}
			} else {
				// Add active class if tabClose false
				$floater.addClass('active');
			}
			
			function floatOpen(){
			
				$('.'+defaults.classWrapper).css({zIndex: 10000});
				$floater.css({zIndex: 10001});
				var h_fpx = h_c+'px';
				
				if(defaults.location == 'bottom'){
					
					$content.animate({marginTop: '-'+h_fpx}, defaults.speed).slideDown(defaults.speedContent);
				} else {
					$content.slideDown(defaults.speedContent);
				
				}
				$floater.addClass('active');
				
				// onOpen callback;
				defaults.onOpen.call(this);
			}
			
			function floatClose(){
				$content.slideUp(defaults.speedContent, function(){
					$floater.removeClass('active');
				});
				// onClose callback;
				defaults.onClose.call(this);
			}
			
			function floatObj(i){
			
				var scroll = $(document).scrollTop();
				var moveTo = start + scroll;
				var h_b = $('body').height();
				var h_f = $floater.height();
				var h_c = $content.height();
				if (h_b < h_f + h_c){
					$floater.css("top",start);
				} else {
					$floater.stop().animate({top: moveTo}, defaults.speedFloat, defaults.easing);
				}
				if(i==1){
					// onFloat callback;
					defaults.onFloat.call(this);
				}
			}
			
			// Set up positioning
			function floaterSetup(obj){
			
				var location = defaults.location;
				var align = defaults.align;
				var offsetL = defaults.offsetLocation;
				var offsetA = defaults.offsetAlign;
				
				if(location == 'top'){
					$(obj).css({top: offsetL});
				} else {
					$(obj).css({bottom: offsetL});
				}
				
				if(defaults.center == true){
					offsetA = '50%';
				}
				if(align == 'left'){
					$(obj).css({left: offsetA});
					if(defaults.center == true){
						$(obj).css({marginLeft: -defaults.centerPx+'px'});
					}
				} else {
					$(obj).css({right: offsetA});
					if(defaults.center == true){
						$(obj).css({marginRight: -defaults.centerPx+'px'});
					}
				}
				
				// onLoad callback;
				defaults.onLoad.call(this);
			}
			
		});
	};
})(jQuery);