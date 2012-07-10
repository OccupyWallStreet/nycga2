jQuery(document).ready(function($) {

	// Initialize the sortable
	$('.cmspo-sortable').nestedSortable({
		tabSize: 16,
		maxLevels: cmspo_maxLevels,
		cursor: 'move',
		forcePlaceholderSize: true,
		handle: 'div',
		helper:	'clone',
		items: 'li',
		opacity: .6,
		placeholder: 'placeholder',
		revert: 80,
		tolerance: 'pointer',
		toleranceElement: '> div',
		update: function(event, ui) {
			$(ui.item).find('div').addClass('loading');
			saveTree(ui);
		}
	});

	// Save changes
	function saveTree(ui) {
		if ( typeof ui != 'undefined' && typeof ui !== 'undefined' ) {
			var order = $('.cmspo-sortable').nestedSortable('toArray');
			for ( var key in order ) {
				delete order[key]['left'];
				delete order[key]['right'];
			}
		}
		
		var state = new Array();
		$.each( $('#cmspo-pages li ol:visible'), function() {
			var li = $(this).parent('li');
			if ( li.children('ol').children('li').length ) {
				state.push( get_id(li.attr('id')) );
			}
		});
		
		var url = ajaxurl + '?action=save_tree&_ajax_nonce=' + _cmspo_ajax_nonce;
		$.post(url, { order: order, open: state }, function(data) {
				if ( typeof ui !== 'undefined' )
					$(ui.item).find('div').removeClass('loading');
				prependToggle();
			}
		);
	}
	
	// Remove labels
	$('.cmspo-state a').click(function() {
		var label = $(this).parent();	
		$(label).parent().addClass('loading');
		
		$.ajax({
			url: ajaxurl + $(this).attr('href'), 
			type: 'POST',
			success: function(data, e) {
				$(label).fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100).delay(180).animate({width:'toggle'}, 100);
				$(label).parent().removeClass('loading');
			},
			error: function() {
				$(label).parent().removeClass('loading');
				$(label).animate({left: '-9px'}, 90)
								.animate({left: '9px'}, 90)
								.animate({left: '0'}, 80);
				}
		});

		return false;
	});

	// Prepend toggle button
	function prependToggle(init) {
		var submenus = null;
		$.each($('#cmspo-pages li'), function() {
			var children = $(this).children('ol').children('li');
			var div = $(this).children('div');
			
			if ( children.length > 0 ) {
				submenus += 1;
				if ( div.children('.cmspo-toggle').length == 0 ) {
					div.prepend('<span class="cmspo-toggle"><span></span></span>');

					var li = div.parent('li');
					if ( !li.hasClass('cmspo-closed') )
						li.addClass('cmspo-open');
				
					div.children('.cmspo-toggle').click(function() {
						var li = $(this).parent('div').parent('li');
						li.children('ol').slideToggle(80, function() { li.toggleClass('cmspo-open cmspo-closed'); saveTree(); });
					});	
				}
			}
			else if ( children.length == 0 && div.children('.cmspo-toggle').length > 0 )
				div.children('.cmspo-toggle').remove();

			var count = div.children('.cmspo-count');
			if ( count.text() != children.length )
				count.text(' (' + children.length + ')');
		});
		if ( submenus > 1 ) {
			if ( $('.cmspo-collapse').length == 0 ) {
				$('.cmspo-actions').prepend('<div class="cmspo-depth"></div>');
				var div = $('.cmspo-depth');
				div.hide().append('<a href="#" class="cmspo-collapse">'+cmspo.Collapse_all+'</a>', ' | <a href="#" class="cmspo-expand">'+cmspo.Expand_all+'</a>');
				
				init ? div.show() : div.slideDown();
				
				$('.cmspo-depth a').click(function(event) {
					if ( $(this).hasClass('cmspo-expand') ) 
						ol = $('#cmspo-pages ol:hidden');
					else if ( $(this).hasClass('cmspo-collapse') )
						ol = $('#cmspo-pages ol:visible');

					var i = ol.length;
					ol.slideToggle(100, function() {
						$(this).parent('li').toggleClass('cmspo-open cmspo-closed');
						i-=1;
						i == 0 ? saveTree() : '';
					});
					return false;
				});

			} else {
				$('.cmspo-depth').slideDown(400);
			}
		}
		else if ( submenus < 1  && $('.cmspo-collapse:visible').length != 0 ) {
			var div = $('.cmspo-depth');
			div.slideUp(400);
		}
	}
	prependToggle(1);

	function get_id(str) {
		return str.substr(5);
	}

});