/**
 * WordPress Administration Navigation Menu
 * Interface JS functions
 * 
 * Modified for BP Group Organizer
 */

var wpNavMenu;

(function($) {

	var api = wpNavMenu = {

		options : {
			menuItemDepthPerLevel : 30, // Do not use directly. Use depthToPx and pxToDepth instead.
			globalMaxDepth : 11
		},

		menuList : undefined,	// Set in init.
		targetList : undefined, // Set in init.
		menusChanged : false,
		isRTL: !! ( 'undefined' != typeof isRtl && isRtl ),
		negateIfRTL: ( 'undefined' != typeof isRtl && isRtl ) ? -1 : 1,

		// Functions that run on init.
		init : function() {
			api.menuList = $('#menu-to-edit');
			api.targetList = api.menuList;

			this.jQueryExtensions();

			this.attachMenuEditListeners();

			this.attachUnsavedChangesListener();

			if( api.menuList.length ) // If no menu, we're in the + tab.
				this.initSortables();

			this.initToggles();
		},

		jQueryExtensions : function() {
			// jQuery extensions
			$.fn.extend({
				menuItemDepth : function() {
					var margin = api.isRTL ? this.eq(0).css('margin-right') : this.eq(0).css('margin-left');
					return api.pxToDepth( margin && -1 != margin.indexOf('px') ? margin.slice(0, -2) : 0 );
				},
				updateDepthClass : function(current, prev) {
					return this.each(function(){
						var t = $(this);
						prev = prev || t.menuItemDepth();
						$(this).removeClass('menu-item-depth-'+ prev )
							.addClass('menu-item-depth-'+ current );
					});
				},
				shiftDepthClass : function(change) {
					return this.each(function(){
						var t = $(this),
							depth = t.menuItemDepth();
						$(this).removeClass('menu-item-depth-'+ depth )
							.addClass('menu-item-depth-'+ (depth + change) );
					});
				},
				childMenuItems : function() {
					var result = $();
					this.each(function(){
						var t = $(this), depth = t.menuItemDepth(), next = t.next();
						while( next.length && next.menuItemDepth() > depth ) {
							result = result.add( next );
							next = next.next();
						}
					});
					return result;
				},
				updateParentMenuItemDBId : function() {
					return this.each(function(){
						var item = $(this),
							input = item.find('.menu-item-data-parent-id'),
							depth = item.menuItemDepth(),
							parent = item.prev();

						if( depth == 0 ) { // Item is on the top level, has no parent
							input.val(0);
						} else { // Find the parent item, and retrieve its object id.
							while( ! parent[0] || ! parent[0].className || -1 == parent[0].className.indexOf('menu-item') || ( parent.menuItemDepth() != depth - 1 ) )
								parent = parent.prev();
							input.val( parent.find('.menu-item-data-db-id').val() );
						}
					});
				},
				hideAdvancedMenuItemFields : function() {
					return this.each(function(){
						var that = $(this);
						$('.hide-column-tog').not(':checked').each(function(){
							that.find('.field-' + $(this).val() ).addClass('hidden-field');
						});
					});
				},
				/**
				 * Adds selected menu items to the menu.
				 *
				 * @param jQuery metabox The metabox jQuery object.
				 */
				addSelectedToMenu : function(processMethod) {
					if ( 0 == $('#menu-to-edit').length ) {
						return false;
					}

					return this.each(function() {
						var t = $(this), menuItems = {},
							checkboxes = t.find('.tabs-panel-active .categorychecklist li input:checked'),
							re = new RegExp('menu-item\\[(\[^\\]\]*)');

						processMethod = processMethod || api.addMenuItemToBottom;

						// If no items are checked, bail.
						if ( !checkboxes.length )
							return false;

						// Show the ajax spinner
						t.find('img.waiting').show();

						// Retrieve menu item data
						$(checkboxes).each(function(){
							var t = $(this),
								listItemDBIDMatch = re.exec( t.attr('name') ),
								listItemDBID = 'undefined' == typeof listItemDBIDMatch[1] ? 0 : parseInt(listItemDBIDMatch[1], 10);
							if ( this.className && -1 != this.className.indexOf('add-to-top') )
								processMethod = api.addMenuItemToTop;
							menuItems[listItemDBID] = t.closest('li').getItemData( 'add-menu-item', listItemDBID );
						});

						// Add the items
						api.addItemToMenu(menuItems, processMethod, function(){
							// Deselect the items and hide the ajax spinner
							checkboxes.removeAttr('checked');
							t.find('img.waiting').hide();
						});
					});
				},
				getItemData : function( itemType, id ) {
					itemType = itemType || 'menu-item';

					var itemData = {}, i,
					fields = [
						'menu-item-db-id',
						'menu-item-object-id',
						'menu-item-object',
						'menu-item-parent-id',
						'menu-item-position',
						'menu-item-type',
						'menu-item-title',
						'menu-item-url',
						'menu-item-description',
						'menu-item-attr-title',
						'menu-item-target',
						'menu-item-classes',
						'menu-item-xfn'
					];

					if( !id && itemType == 'menu-item' ) {
						id = this.find('.menu-item-data-db-id').val();
					}

					if( !id ) return itemData;

					this.find('input').each(function() {
						var field;
						i = fields.length;
						while ( i-- ) {
							if( itemType == 'menu-item' )
								field = fields[i] + '[' + id + ']';
							else if( itemType == 'add-menu-item' )
								field = 'menu-item[' + id + '][' + fields[i] + ']';

							if (
								this.name &&
								field == this.name
							) {
								itemData[fields[i]] = this.value;
							}
						}
					});

					return itemData;
				},
				setItemData : function( itemData, itemType, id ) { // Can take a type, such as 'menu-item', or an id.
					itemType = itemType || 'menu-item';

					if( !id && itemType == 'menu-item' ) {
						id = $('.menu-item-data-db-id', this).val();
					}

					if( !id ) return this;

					this.find('input').each(function() {
						var t = $(this), field;
						$.each( itemData, function( attr, val ) {
							if( itemType == 'menu-item' )
								field = attr + '[' + id + ']';
							else if( itemType == 'add-menu-item' )
								field = 'menu-item[' + id + '][' + attr + ']';

							if ( field == t.attr('name') ) {
								t.val( val );
							}
						});
					});
					return this;
				}
			});
		},

		initToggles : function() {
			// init postboxes
			postboxes.add_postbox_toggles('nav-menus');

			// adjust columns functions for menus UI
			columns.useCheckboxesForHidden();
			columns.checked = function(field) {
				$('.field-' + field).removeClass('hidden-field');
			}
			columns.unchecked = function(field) {
				$('.field-' + field).addClass('hidden-field');
			}
			// hide fields
			api.menuList.hideAdvancedMenuItemFields();
		},

		initSortables : function() {
			var currentDepth = 0, originalDepth, minDepth, maxDepth,
				prev, next, prevBottom, nextThreshold, helperHeight, transport,
				menuEdge = api.menuList.offset().left,
				body = $('body'), maxChildDepth,
				menuMaxDepth = initialMenuMaxDepth();

			// Use the right edge if RTL.
			menuEdge += api.isRTL ? api.menuList.width() : 0;

			api.menuList.sortable({
				handle: '.menu-item-handle',
				placeholder: 'sortable-placeholder',
				start: function(e, ui) {
					var height, width, parent, children, tempHolder;

					// handle placement for rtl orientation
					if ( api.isRTL )
						ui.item[0].style.right = 'auto';

					transport = ui.item.children('.menu-item-transport');

					// Set depths. currentDepth must be set before children are located.
					originalDepth = ui.item.menuItemDepth();
					updateCurrentDepth(ui, originalDepth);

					// Attach child elements to parent
					// Skip the placeholder
					parent = ( ui.item.next()[0] == ui.placeholder[0] ) ? ui.item.next() : ui.item;
					children = parent.childMenuItems();
					transport.append( children );

					// Update the height of the placeholder to match the moving item.
					height = transport.outerHeight();
					// If there are children, account for distance between top of children and parent
					height += ( height > 0 ) ? (ui.placeholder.css('margin-top').slice(0, -2) * 1) : 0;
					height += ui.helper.outerHeight();
					helperHeight = height;
					height -= 2; // Subtract 2 for borders
					ui.placeholder.height(height);

					// Update the width of the placeholder to match the moving item.
					maxChildDepth = originalDepth;
					children.each(function(){
						var depth = $(this).menuItemDepth();
						maxChildDepth = (depth > maxChildDepth) ? depth : maxChildDepth;
					});
					width = ui.helper.find('.menu-item-handle').outerWidth(); // Get original width
					width += api.depthToPx(maxChildDepth - originalDepth); // Account for children
					width -= 2; // Subtract 2 for borders
					ui.placeholder.width(width);

					// Update the list of menu items.
					tempHolder = ui.placeholder.next();
					tempHolder.css( 'margin-top', helperHeight + 'px' ); // Set the margin to absorb the placeholder
					ui.placeholder.detach(); // detach or jQuery UI will think the placeholder is a menu item
					$(this).sortable( "refresh" ); // The children aren't sortable. We should let jQ UI know.
					ui.item.after( ui.placeholder ); // reattach the placeholder.
					tempHolder.css('margin-top', 0); // reset the margin

					// Now that the element is complete, we can update...
					updateSharedVars(ui);
				},
				stop: function(e, ui) {
					var children, depthChange = currentDepth - originalDepth;

					// Return child elements to the list
					children = transport.children().insertAfter(ui.item);

					// Update depth classes
					if( depthChange != 0 ) {
						ui.item.updateDepthClass( currentDepth );
						children.shiftDepthClass( depthChange );
						updateMenuMaxDepth( depthChange );
					}
					// Register a change
					api.registerChange();
					// Update the item data.
					ui.item.updateParentMenuItemDBId();

					// address sortable's incorrectly-calculated top in opera
					ui.item[0].style.top = 0;

					// handle drop placement for rtl orientation
					if ( api.isRTL ) {
						ui.item[0].style.left = 'auto';
						ui.item[0].style.right = 0;
					}

				},
				change: function(e, ui) {
					// Make sure the placeholder is inside the menu.
					// Otherwise fix it, or we're in trouble.
					if( ! ui.placeholder.parent().hasClass('menu') )
						(prev.length) ? prev.after( ui.placeholder ) : api.menuList.prepend( ui.placeholder );

					updateSharedVars(ui);
				},
				sort: function(e, ui) {
					var offset = ui.helper.offset(),
						edge = api.isRTL ? offset.left + ui.helper.width() : offset.left,
						depth = api.negateIfRTL * api.pxToDepth( edge - menuEdge );
					// Check and correct if depth is not within range.
					// Also, if the dragged element is dragged upwards over
					// an item, shift the placeholder to a child position.
					if ( depth > maxDepth || offset.top < prevBottom ) depth = maxDepth;
					else if ( depth < minDepth ) depth = minDepth;

					if( depth != currentDepth )
						updateCurrentDepth(ui, depth);

					// If we overlap the next element, manually shift downwards
					if( nextThreshold && offset.top + helperHeight > nextThreshold ) {
						next.after( ui.placeholder );
						updateSharedVars( ui );
						$(this).sortable( "refreshPositions" );
					}
				}
			});

			function updateSharedVars(ui) {
				var depth;

				prev = ui.placeholder.prev();
				next = ui.placeholder.next();

				// Make sure we don't select the moving item.
				if( prev[0] == ui.item[0] ) prev = prev.prev();
				if( next[0] == ui.item[0] ) next = next.next();

				prevBottom = (prev.length) ? prev.offset().top + prev.height() : 0;
				nextThreshold = (next.length) ? next.offset().top + next.height() / 3 : 0;
				minDepth = (next.length) ? next.menuItemDepth() : 0;

				if( prev.length )
					maxDepth = ( (depth = prev.menuItemDepth() + 1) > api.options.globalMaxDepth ) ? api.options.globalMaxDepth : depth;
				else
					maxDepth = 0;
			}

			function updateCurrentDepth(ui, depth) {
				ui.placeholder.updateDepthClass( depth, currentDepth );
				currentDepth = depth;
			}

			function initialMenuMaxDepth() {
				if( ! body[0].className ) return 0;
				var match = body[0].className.match(/menu-max-depth-(\d+)/);
				return match && match[1] ? parseInt(match[1]) : 0;
			}

			function updateMenuMaxDepth( depthChange ) {
				var depth, newDepth = menuMaxDepth;
				if ( depthChange === 0 ) {
					return;
				} else if ( depthChange > 0 ) {
					depth = maxChildDepth + depthChange;
					if( depth > menuMaxDepth )
						newDepth = depth;
				} else if ( depthChange < 0 && maxChildDepth == menuMaxDepth ) {
					while( ! $('.menu-item-depth-' + newDepth, api.menuList).length && newDepth > 0 )
						newDepth--;
				}
				// Update the depth class.
				body.removeClass( 'menu-max-depth-' + menuMaxDepth ).addClass( 'menu-max-depth-' + newDepth );
				menuMaxDepth = newDepth;
			}
		},

		attachMenuEditListeners : function() {
			var that = this;
			$('#update-nav-menu').bind('click', function(e) {
				if ( e.target && e.target.className ) {
					if ( -1 != e.target.className.indexOf('item-edit') ) {
						return that.eventOnClickEditLink(e.target);
					} else if ( -1 != e.target.className.indexOf('menu-save') ) {
						return that.eventOnClickMenuSave(e.target);
					} else if ( -1 != e.target.className.indexOf('item-delete') ) {
						return that.eventOnClickMenuItemDelete(e.target);
					} else if ( -1 != e.target.className.indexOf('item-cancel') ) {
						return that.eventOnClickCancelLink(e.target);
					}
				}
			});
		},

		addItemToMenu : function(menuItem, processMethod, callback) {
			var menu = $('#menu').val(),
				nonce = $('#menu-settings-column-nonce').val();

			processMethod = processMethod || function(){};
			callback = callback || function(){};

			params = {
				'action': 'add-menu-item',
				'menu': menu,
				'menu-settings-column-nonce': nonce,
				'menu-item': menuItem
			};

			$.post( ajaxurl, params, function(menuMarkup) {
				var ins = $('#menu-instructions');
				processMethod(menuMarkup, params);
				if( ! ins.hasClass('menu-instructions-inactive') && ins.siblings().length )
					ins.addClass('menu-instructions-inactive');
				callback();
			});
		},

		/**
		 * Process the add menu item request response into menu list item.
		 *
		 * @param string menuMarkup The text server response of menu item markup.
		 * @param object req The request arguments.
		 */
		addMenuItemToBottom : function( menuMarkup, req ) {
			$(menuMarkup).hideAdvancedMenuItemFields().appendTo( api.targetList );
		},

		addMenuItemToTop : function( menuMarkup, req ) {
			$(menuMarkup).hideAdvancedMenuItemFields().prependTo( api.targetList );
		},

		attachUnsavedChangesListener : function() {
			$('#menu-management input, #menu-management select, #menu-management, #menu-management textarea').change(function(){
				api.registerChange();
			});

			if ( 0 != $('#menu-to-edit').length ) {
				window.onbeforeunload = function(){
					if ( api.menusChanged )
						return OrganizerL10n.saveAlert;
				};
			} else {
				// Make the post boxes read-only, as they can't be used yet
				$('#menu-settings-column').find('input,select').prop('disabled', true).end().find('a').attr('href', '#').unbind('click');
			}
		},

		registerChange : function() {
			api.menusChanged = true;
		},

		eventOnClickEditLink : function(clickedEl) {
			var settings, item,
			matchedSection = /#(.*)$/.exec(clickedEl.href);
			if ( matchedSection && matchedSection[1] ) {
				settings = $('#'+matchedSection[1]);
				item = settings.parent();
				if( 0 != item.length ) {
					if( item.hasClass('menu-item-edit-inactive') ) {
						if( ! settings.data('menu-item-data') ) {
							settings.data( 'menu-item-data', settings.getItemData() );
						}
						settings.slideDown('fast');
						item.removeClass('menu-item-edit-inactive')
							.addClass('menu-item-edit-active');
					} else {
						settings.slideUp('fast');
						item.removeClass('menu-item-edit-active')
							.addClass('menu-item-edit-inactive');
					}
					return false;
				}
			}
		},

		eventOnClickCancelLink : function(clickedEl) {
			var settings = $(clickedEl).closest('.menu-item-settings');
			settings.setItemData( settings.data('menu-item-data') );
			return false;
		},

		eventOnClickMenuSave : function(clickedEl) {
			window.onbeforeunload = null;
			return true;
		},

		eventOnClickMenuItemDelete : function(clickedEl) {
			if ( confirm( OrganizerL10n.warnDeleteGroup ) ) {
				var itemID = parseInt(clickedEl.id.replace('delete-', ''), 10);
				
				var href = $('#delete-' + itemID).attr('href');
				
				api.removeMenuItem( $('#menu-item-' + itemID) );
				api.registerChange();

				params = {
					'action':	'bp_organizer_delete-group',
					'group_id':	itemID,
					'_ajax_nonce':	href.substr(href.indexOf('_wpnonce=')+9)
				};
				
				$.post( ajaxurl , params, function(data) {
					console.log(data);
					if(data == 'success') {
						$('<div id="message" class="updated fade"><p>' + OrganizerL10n.groupDeleted  + '</p></div>').insertAfter('h2');
					} else {
						$('<div id="message" class="error fade"><p>' + OrganizerL10n.groupDeleteFailed  + '</p></div>').insertAfter('h2');
					}
					api.menusChanged = false;
				});

			}
			return false;
		},

		/**
		 * Process the quick search response into a search result
		 *
		 * @param string resp The server response to the query.
		 * @param object req The request arguments.
		 * @param jQuery panel The tabs panel we're searching in.
		 */
		processQuickSearchQueryResponse : function(resp, req, panel) {
			var matched, newID,
			takenIDs = {},
			form = document.getElementById('nav-menu-meta'),
			pattern = new RegExp('menu-item\\[(\[^\\]\]*)', 'g'),
			$items = $('<div>').html(resp).find('li'),
			$item;

			if( ! $items.length ) {
				$('.categorychecklist', panel).html( '<li><p>' + OrganizerL10n.noResultsFound + '</p></li>' );
				$('img.waiting', panel).hide();
				return;
			}

			$items.each(function(){
				$item = $(this);

				// make a unique DB ID number
				matched = pattern.exec($item.html());

				if ( matched && matched[1] ) {
					newID = matched[1];
					while( form.elements['menu-item[' + newID + '][menu-item-type]'] || takenIDs[ newID ] ) {
						newID--;
					}

					takenIDs[newID] = true;
					if ( newID != matched[1] ) {
						$item.html( $item.html().replace(new RegExp(
							'menu-item\\[' + matched[1] + '\\]', 'g'),
							'menu-item[' + newID + ']'
						) );
					}
				}
			});

			$('.categorychecklist', panel).html( $items );
			$('img.waiting', panel).hide();
		},

		removeMenuItem : function(el) {
			var children = el.childMenuItems();

			el.addClass('deleting').animate({
					opacity : 0,
					height: 0
				}, 350, function() {
					var ins = $('#menu-instructions');
					el.remove();
					children.shiftDepthClass(-1).updateParentMenuItemDBId();
					if( ! ins.siblings().length )
						ins.removeClass('menu-instructions-inactive');
				});
		},

		depthToPx : function(depth) {
			return depth * api.options.menuItemDepthPerLevel;
		},

		pxToDepth : function(px) {
			return Math.floor(px / api.options.menuItemDepthPerLevel);
		}

	};

	$(document).ready(function(){ wpNavMenu.init(); });

})(jQuery);
