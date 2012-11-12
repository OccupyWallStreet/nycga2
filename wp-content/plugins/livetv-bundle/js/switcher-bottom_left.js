/*
 * jQuery Extend - http://kwark.allwebtuts.net 
 * Copyright © 2012 Laurent (KwarK) Bertrand
 * All rights reserved.
*/
jQuery(document).ready(function($){	
		$('#full-view-switcher').before('<div id="livetv-nav-irc">').cycle({
			fx: 'shuffle', 
			shuffle: { 
				top:  340,
				left: -500,
			},
			
			easing: 'easeInOutBack', 
			delay: -3000,
			timeout: 0,
			next: '.livetv-nxt'
		})
	});