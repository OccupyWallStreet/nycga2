/*
 * jQuery Extend - http://kwark.allwebtuts.net 
 * Copyright © 2012 Laurent (KwarK) Bertrand
 * All rights reserved.
*/
jQuery(document).ready(function($){	
		$('#full-view-switcher').cycle({
			fx: 'shuffle', 
			shuffle: { 
				top:  -340,
				left:  0,
			},
			
			easing: 'easeInOutBack', 
			delay: -3000,
			timeout: 0,
			next: '.livetv-nxt'
		})
	});