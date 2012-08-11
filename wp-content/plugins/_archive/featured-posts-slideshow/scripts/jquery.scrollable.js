
/**
 * jquery.scrollable 0.11. Making HTML elements scroll.
 * 
 * http://flowplayer.org/tools/scrollable.html
 *
 * Copyright (c) 2008 Tero Piirainen (tero@flowplayer.org)
 *
 * Released under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 * 
 * >> Basically you can do anything you want but leave this header as is <<
 *
 * Since  : 0.01 - 03/01/2008
 * Version: 0.11 - 05/29/2008
 */
(function($) {
		
	// plugin initialization
	$.fn.extend({
		scrollable: function(arg1, arg2, arg3) { 
			
			return this.each(function() {
				if (typeof arg1 == "string") {
					var el = $.data(this, "scrollable");
					el[arg1].apply(el, [arg2, arg3]);
					
				} else { 
					new $.scrollable(this, arg1, arg2);
				}
			});
		}		
	});
		
	// constructor
	$.scrollable = function(el, opts) {   
			
		// store this instance
		$.data(el, "scrollable", this);
		
		this.init(el, opts); 
	};
	
	
	// methods
	$.extend($.scrollable.prototype, { 
			
		init: function(el, config)  {
			 
			// current instance
			var self = this;  
			
			var opts = {								
				size: 5,
				horizontal:false,				
				activeClass:'active',
				speed: 300,
				onSeek: null,
				
				// jquery selectors
				items: '.items',
				prev:'.prev',
				next:'.next',
				navi:'.navi',
				naviItem:'span'
			}; 
	
			this.opts = $.extend(opts, config); 			
	
			// root / itemRoot
			var root = this.root = $(el);			
			var itemRoot = $(opts.items, root);			
			if (!itemRoot.length) itemRoot = root;			
				
			// wrap itemRoot.children() inside container
			itemRoot.css({position:'relative', overflow:'hidden', visibility:'visible'});
			itemRoot.children().wrapAll('<div class="__scrollable" style="position:absolute"/>');
			
			this.wrap = itemRoot.children(":first");
			this.wrap.css(opts.horizontal ? "width" : "height", "200000em").after('<br clear="all"/>');			
			this.items = this.wrap.children();
			this.index = 0;

			
			// set height based on size
			if (opts.horizontal) {
				itemRoot.width(opts.size * (this.items.eq(1).offset().left - this.items.eq(0).offset().left) -2);	
			} else {
				itemRoot.height(opts.size * (this.items.eq(1).offset().top - this.items.eq(0).offset().top) -2);	
			} 
	
			// mousewheel
			if ($.isFunction($.fn.mousewheel)) { 
				root.bind("mousewheel.scrollable", function(event, delta)  { 
					self.move(-delta, 50);		
					return false;
				});
			} 
	
			// keyboard
			$(window).bind("keypress.scrollable", function(evt) {

				if ($(evt.target).parents(".__scrollable").length) {
					
					if (opts.horizontal && (evt.keyCode == 37 || evt.keyCode == 39)) {
						self.move(evt.keyCode == 37 ? -1 : 1);
						return false;
					}	
					
					if (!opts.horizontal && (evt.keyCode == 38 || evt.keyCode == 40)) {
						self.move(evt.keyCode == 38 ? -1 : 1);
						return false;
					}
				}
				
				return true;
				
			});	
			
			
			// item.click()
			this.items.each(function(index, arg) {
				$(this).bind("click.scrollable", function() {
					self.click(index);		
				});
			});

			this.activeIndex = 0;
			
			// prev
			$(opts.prev, root).click(function() { self.prev() });
			

			// next
			$(opts.next, root).click(function() { self.next() });
			

			// navi 			
			$(opts.navi, root).each(function() { 				
				var navi = $(this);
				
				var status = self.getStatus();
				
				// generate new entries
				if (navi.is(":empty")) {
					for (var i = 0; i < status.pages; i++) {		
						
						var item = $("<" + opts.naviItem + "/>").attr("page", i).click(function() {							
							var el = $(this);
							el.parent().children().removeClass(opts.activeClass);
							el.addClass(opts.activeClass);
							self.setPage(el.attr("page"));
							
						});
						
						if (i == 0) item.addClass(opts.activeClass);
						navi.append(item);					
					}
					
				// assign onClick events to existing entries
				} else {
					
					navi.children().each(function(i)  {
						var item = $(this);
						item.attr("page", i);
						if (i == 0) item.addClass(opts.activeClass);
						
						item.click(function() {
							item.parent().children().removeClass(opts.activeClass);
							item.addClass(opts.activeClass);
							self.setPage(item.attr("page"));
						});
						
					});
				}
				
			});			
			
		},
		

		click: function(index) {

			var item = this.items.eq(index);
			var klass = this.opts.activeClass;
			
			if (!item.hasClass(klass) && (index >= 0 || index < this.items.size())) { 
				
				var prev = this.items.eq(this.activeIndex).removeClass(klass);
				item.addClass(klass);   
				
				this.seekTo(index - Math.floor(this.opts.size / 2));
				this.activeIndex = index;
			}  
		},
		
		getStatus: function() {
			var len =  this.items.size();
			var s = {
				length: len, 
				index: this.index, 
				size: this.opts.size,
				pages: Math.floor(len / this.opts.size),
				page: Math.floor(this.index / this.opts.size)
			};

			return s;
		}, 

		
		// all other seeking functions depend on this generic seeking function		
		seekTo: function(index, time) {
			
			if (index < 0) index = 0;			
			index = Math.min(index, this.items.length - this.opts.size);			
			
			var item = this.items.eq(index);			
			if (item.size() == 0) return false; 			
			this.index = index;

			
			if (this.opts.horizontal) {
				var left = this.wrap.offset().left - item.offset().left;				
				this.wrap.animate({left: left}, time || this.opts.speed);
				
			} else {
				var top = this.wrap.offset().top - item.offset().top;
				this.wrap.animate({top: top}, time || this.opts.speed);							
			}

			// custom onSeek callback
			if ($.isFunction(this.opts.onSeek)) {
				this.opts.onSeek.call(this.getStatus());
			}
			
			// navi status update
			var navi = $(this.opts.navi, this.root);
			
			if (navi.length) {
				var klass = this.opts.activeClass;
				var page = Math.round(index / this.opts.size);
				navi.children().removeClass(klass).eq(page).addClass(klass);
			}
			
			
			return true; 
		},
		
			
		move: function(offset, time) {
			this.seekTo(this.index + offset, time);
		},
		
		next: function(time) {
			this.move(1, time);	
		},
		
		prev: function(time) {
			this.move(-1, time);	
		},
		
		movePage: function(offset, time) {
			this.move(this.opts.size * offset, time);		
		},
		
		setPage: function(index, time) {
			this.seekTo(this.opts.size * index, time);
		},
		
		prevPage: function(time) {
			var page = Math.floor(this.index / this.opts.size);
			this.seekTo(this.opts.size * (page-1), time);
		},  

		nextPage: function(time) {
			var page = Math.floor(this.index / this.opts.size);
			this.seekTo(this.opts.size * (page+1), time);
		}, 
		
		begin: function(time) {
			this.seekTo(0, time);	
		},
		
		end: function(time) {
			this.seekTo(this.items.size() - this.opts.size, time);	
		}

		
	});  
	
})(jQuery);



