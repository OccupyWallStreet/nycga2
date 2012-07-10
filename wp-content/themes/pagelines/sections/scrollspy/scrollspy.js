jQuery(window).load(function() {
	
	var spyCounter = 1, mainOffset = jQuery('.hentry').length && jQuery('.hentry').first().offset().top, scrollArea = jQuery('body');
	
	scrollArea.find('.scroll-header').each(function () {
		
		if (spyCounter === 1) {
			contentOffTop = jQuery(this).offset().top;
		}
		
		var headerID = 'spyID' + spyCounter++;
		var headerText;
		
		// Set ID of page header
		jQuery(this).attr('id', headerID);
		
		// Get text for nav link
		if (jQuery(this).attr('title')) {
			headerText = jQuery(this).attr('title');
		} else {
			headerText = '(HTML Title Attribute Needed)';
		}
	
		jQuery('.spynav .nav').append('<li><a class="spyanchor" href="#' + headerID + '">' + headerText + '</a></li>');
		
	});
	
	jQuery('.spynav .nav:empty').html('<li><a>Add HTML elements with a "scroll-header" class and "title" attribute to document. None detected.</a></li>');
	
	
	scrollArea.attr('data-spy', 'scroll');
	scrollArea.scrollspy({offset: 180 - mainOffset});

	
	jQuery(".spyanchor").click(function (event) {		
			event.preventDefault();
			var offTop = jQuery(this.hash).offset().top - 120;
			jQuery('html,body').animate({scrollTop: offTop}, 500);
		});

	var $win = jQuery(window)
			, $nav = jQuery('.spynav')
			, navbarHeight = jQuery('.navbar-full-width').length && jQuery('.navbar-full-width').outerHeight()
			, navbarOffset = jQuery('#wpadminbar').length && jQuery('#wpadminbar').outerHeight()
			, navOffset = navbarHeight + navbarOffset
			, navTop = jQuery('.spynav').length && jQuery('.spynav').offset().top - navbarOffset
			, isFixed = 0;				


	processScroll();
		    
    // hack sad times - holdover until rewrite for 2.1
    $nav.on('click', function () {
      if (!isFixed) setTimeout(function () {  $win.scrollTop($win.scrollTop() - 47) }, 10);
    });

    $win.on('scroll', processScroll);

		   
	function processScroll() {
		var i
			, scrollTop = $win.scrollTop()

		if (scrollTop >= navTop && !isFixed) {
			var contWidth = jQuery('.section-scrollspy .content-pad').width();
			jQuery('.spynav .nav').width(contWidth);
			jQuery('.spynav-space').show();
			isFixed = 1;
			$nav.css('top', navOffset).addClass('spynav-fixed')
		} else if (scrollTop <= navTop && isFixed) {
			isFixed = 0
			jQuery('.spynav .nav').width('auto');
			jQuery('.spynav-space').hide();
			$nav.removeClass('spynav-fixed')
		}
	}

});
