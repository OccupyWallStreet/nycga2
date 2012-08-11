/*

 * DC jQuery Drill Down Menu - jQuery drill down ipod menu
 * Copyright (c) 2011 Design Chemical
 *
 * Dual licensed under the MIT and GPL licenses:
 * 	http://www.opensource.org/licenses/mit-license.php
 * 	http://www.gnu.org/licenses/gpl.html
 *
 */

(function($){

	//define the new for the plugin ans how to call it
	$.fn.dcDrilldown = function(options) {

		//set default options
		var defaults = {
			classWrapper	: 'dd-wrapper',
			classMenu		: 'dd-menu',
			classParent		: 'dd-parent',
			classParentLink	: 'dd-parent-a',
			classActive		: 'active',
			classHeader		: 'dd-header',
			eventType		: 'click',
			hoverDelay		: 300,
			speed       	: 'slow',
			saveState		: true,
			showCount		: true,
			classCount		: 'dd-count',
			classIcon		: 'dd-icon',
			linkType		: 'backlink',
			resetText		: 'All',
			headerTag		: 'h3',
			defaultText		: 'Select Option',
			includeHdr		: true
		};

		//call in the default otions
		var options = $.extend(defaults, options);

		//act upon the element that is passed into the design
		return this.each(function(options){

			var $dcDrilldownObj = this;
			$($dcDrilldownObj).addClass(defaults.classMenu);
			var $wrapper = '<div class="'+defaults.classWrapper+'" />';
			$($dcDrilldownObj).wrap($wrapper);
			var $dcWrapper = $($dcDrilldownObj).parent();
			var objIndex = $($dcWrapper).index('.'+defaults.classWrapper);
			var idHeader = defaults.classHeader+'-'+objIndex;
			var idWrapper = defaults.classWrapper+'-'+objIndex;
			$($dcWrapper).attr('id',idWrapper);
			var $header = '<div id="'+idHeader+'" class="'+defaults.classHeader+'"></div>';

			setUpDrilldown();

			if(defaults.saveState == true){
				var cookieId = defaults.classWrapper+'-'+objIndex;
				checkCookie(cookieId, $dcDrilldownObj);
			}

			resetDrilldown($dcDrilldownObj, $dcWrapper);

			$('li a',$dcDrilldownObj).click(function(e){

				$link = this;
				$activeLi = $(this).parent('li').stop();
				$siblingsLi = $($activeLi).siblings();

				// Drilldown action
				if($('> ul',$activeLi).length){
					if($($link).hasClass(defaults.classActive)){
						$('ul a',$activeLi).removeClass(defaults.classActive);
						resetDrilldown($dcDrilldownObj, $dcWrapper);
					} else {
						actionDrillDown($activeLi, $dcWrapper, $dcDrilldownObj);
					}
				}

				// Prevent browsing to link if has child links
				if($(this).next('ul').length > 0){
					e.preventDefault();
				}
			});

			// Set up accordion
			function setUpDrilldown(){

				$arrow = '<span class="'+defaults.classIcon+'"></span>';
				$($dcDrilldownObj).before($header);

				// Get width of menu container & height of list item
				var totalWidth = $($dcDrilldownObj).outerWidth();
				totalWidth += 'px';
				var itemHeight = $('li',$dcDrilldownObj).outerHeight(true);

				// Get height of largest sub menu
				var objUl = $('ul',$dcDrilldownObj);
				var maxItems = findMaxHeight(objUl);

				// Get level of largest sub menu
				var maxUl = $(objUl+'[rel="'+maxItems+'"]');
				var getIndex = findMaxIndex(maxUl);

				// Set menu container height
				if(defaults.linkType == 'link'){
					menuHeight = itemHeight * (maxItems + getIndex);
				} else {
					menuHeight = itemHeight * maxItems;
				}
				$($dcDrilldownObj).css({height: menuHeight+'px', width: totalWidth});

				// Set sub menu width and offset
				$('li',$dcDrilldownObj).each(function(){
					$(this).css({width: totalWidth});
					$('ul',this).css({width: totalWidth, marginRight: '-'+totalWidth, marginTop: '0'});
					if($('> ul',this).length){
						$(this).addClass(defaults.classParent);
						$('> a',this).addClass(defaults.classParentLink).append($arrow);
						if(defaults.showCount == true){
							var parentLink = $('a:not(.'+defaults.classParentLink+')',this);
							var countParent = parseInt($(parentLink).length);
							getCount = countParent;
							$('> a',this).append(' <span class="'+defaults.classCount+'">('+getCount+')</span>');
						}
					}
				});

				// Add css class
				$('ul',$dcWrapper).each(function(){
					$('li:last',this).addClass('last');
				});
				$('> ul > li:last',$dcWrapper).addClass('last');
				if(defaults.linkType == 'link'){
					$(objUl).css('top',itemHeight+'px');
				}
			}

			// Breadcrumbs
			$('#'+idHeader+' a').live('click',function(e){

				if($(this).hasClass('link-back')){
					linkIndex = $('#'+idWrapper+' .'+defaults.classParentLink+'.active').length;
					linkIndex = linkIndex-2;
					$('a.'+defaults.classActive+':last', $dcDrilldownObj).removeClass(defaults.classActive);
				} else {
					// Get link index
					var linkIndex = parseInt($(this).index('#'+idHeader+' a'));
					if(linkIndex == 0){
						$('a',$dcDrilldownObj).removeClass(defaults.classActive);
					} else {
						// Select equivalent active link
						linkIndex = linkIndex-1;
						$('a.'+defaults.classActive+':gt('+linkIndex+')',$dcDrilldownObj).removeClass(defaults.classActive);
					}
				}
				resetDrilldown($dcDrilldownObj, $dcWrapper);
				e.preventDefault();
			});
		});

		function findMaxHeight(element){
			var maxValue = undefined;
			$(element).each(function(){
				var val = parseInt($('> li',this).length);
				$(this).attr('rel',val);
				if (maxValue === undefined || maxValue < val){
					maxValue = val;
				}
			});
			return maxValue;
		}

		function findMaxIndex(element){
			var maxIndex = undefined;
			$(element).each(function(){
				var val = parseInt($(this).parents('li').length);
				if (maxIndex === undefined || maxIndex < val) {
					maxIndex = val;
				}
			});
			return maxIndex;
		}

		// Retrieve cookie value and set active items
		function checkCookie(cookieId, obj){
			var cookieVal = $.cookie(cookieId);
			if(cookieVal != null){
				// create array from cookie string
				var activeArray = cookieVal.split(',');
				$.each(activeArray, function(index,value){
					var $cookieLi = $('li:eq('+value+')',obj);
					$('> a',$cookieLi).addClass(defaults.classActive);
				});
			}
		}

		// Drill Down
		function actionDrillDown(element, wrapper, obj){
			// Declare header
			var $header = $('.'+defaults.classHeader, wrapper);

			// Get new breadcrumb and header text
			var getNewBreadcrumb = $('h3',$header).html();
			var getNewHeaderText = $('> a',element).html();

			// Add new breadcrumb
			if(defaults.linkType == 'breadcrumb'){
				if(!$('ul',$header).length){
					$($header).prepend('<ul></ul>');
				}
				if(getNewBreadcrumb == defaults.defaultText){
					$('ul',$header).append('<li><a href="#" class="first">'+defaults.resetText+'</a></li>');
				} else {
					$('ul',$header).append('<li><a href="#">'+getNewBreadcrumb+'</a></li>');
				}
			}
			if(defaults.linkType == 'backlink'){
				if(!$('a',$header).length){
					$($header).prepend('<a href="#" class="link-back">'+getNewBreadcrumb+'</a>');
				} else {
					$('.link-back',$header).html(getNewBreadcrumb);
				}
			}
			if(defaults.linkType == 'link'){
				if(!$('a',$header).length){
					$($header).prepend('<ul><li><a href="#" class="first">'+defaults.resetText+'</a></li></ul>');
				}
			}
			// Update header text
			updateHeader($header, getNewHeaderText);

			// declare child link
			var activeLink = $('> a',element);

			// add active class to link
			$(activeLink).addClass(defaults.classActive);
			$('> ul li',element).show();
			$('> ul',element).animate({"margin-right": 0}, defaults.speed);

			// Find all sibling items & hide
			var $siblingsLi = $(element).siblings();
			$($siblingsLi).hide();

			// If using breadcrumbs hide this element
			if(defaults.linkType != 'link'){
				$(activeLink).hide();
			}

			// Write cookie if save state is on
			if(defaults.saveState == true){
				var cookieId = $(wrapper).attr('id');
				createCookie(cookieId, obj);
			}
		}

		// Drill Up
		function actionDrillUp(element, obj, wrapper){
			// Declare header
			var $header = $('.'+defaults.classHeader, wrapper);

			var activeLink = $('> a',element);
			var checklength = $('.'+defaults.classActive, wrapper).length;
			var activeIndex = $(activeLink).index('.'+defaults.classActive, wrapper);

			// Get width of menu for animating right
			var totalWidth = $(obj).outerWidth(true);
			$('ul',element).css('margin-right',-totalWidth+'px');

			// Show all elements
			$(activeLink).addClass(defaults.classActive);
			$('> ul li',element).show();
			$('a',element).show();

			// Get new header text from clicked link
			var getNewHeaderText = $('> a',element).html();
			$('h3',$header).html(getNewHeaderText);

			if(defaults.linkType == 'breadcrumb'){
				var breadcrumbIndex = activeIndex-1;
				$('a:gt('+activeIndex+')',$header).remove();
			}
		}

		function updateHeader(obj, html){
			if(defaults.includeHdr == true){
				if($('h3',obj).length){
					$('h3',obj).html(html);
				} else {
					$(obj).append('<'+defaults.headerTag+'>'+html+'</'+defaults.headerTag+'>');
				}
			}
		}

		// Reset accordion using active links
		function resetDrilldown(obj, wrapper){
			var $header = $('.'+defaults.classHeader, wrapper);
			$('ul',$header).remove();
			$('a',$header).remove();
			$('li',obj).show();
			$('a',obj).show();
			var totalWidth = $(obj).outerWidth(true);
			if(defaults.linkType == "link"){
				if($('a.'+defaults.classActive+':last',obj).parent('li').length){
					var lastActive = $('a.'+defaults.classActive+':last',obj).parent('li');
					$('ul',lastActive).css('margin-right',-totalWidth+'px');
				}else {
				$('ul',obj).css('margin-right',-totalWidth+'px');
				}
			} else {
				$('ul',obj).css('margin-right',-totalWidth+'px');
			}
			updateHeader($header, defaults.defaultText);

			// Write cookie if save state is on
			if(defaults.saveState == true){
				var cookieId = $(wrapper).attr('id');
				createCookie(cookieId, obj);
			}

			$('a.'+defaults.classActive,obj).each(function(i){
				var $activeLi = $(this).parent('li').stop();
				actionDrillDown($activeLi, wrapper, obj);
			});
		}

		// Write cookie
		function createCookie(cookieId, obj){
			var activeIndex = [];
			// Create array of active items index value
			$('a.'+defaults.classActive,obj).each(function(i){
				var $arrayItem = $(this).parent('li');
				var itemIndex = $('li',obj).index($arrayItem);
					activeIndex.push(itemIndex);
				});
			// Store in cookie
			$.cookie(cookieId, activeIndex, { path: '/' });
		}
	};
})(jQuery);