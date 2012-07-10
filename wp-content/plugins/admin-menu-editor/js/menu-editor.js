//(c) W-Shadow

/** @namespace wsEditorData */

var wsIdCounter = 0;

(function ($){
	
/*
 * Utility function for generating pseudo-random alphanumeric menu IDs.
 * Rationale: Simpler than atomically auto-incrementing or globally unique IDs.
 */
function randomMenuId(size){
	if ( typeof size == 'undefined' ){
		size = 5;
	}
	
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for( var i=0; i < size; i++ )
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
}
	
function outputWpMenu(menu){
	//Remove the current menu data
	$('#ws_menu_box').empty();
	$('#ws_submenu_box').empty();
	//Kill autocomplete boxes
	$('.ac_results').remove();
	
	//Display the new menu
	var i = 0;
	for (var filename in menu){
		outputTopMenu(menu[filename]);
		i++;
	}
	
	//Automatically select the first top-level menu
	$('#ws_menu_box .ws_menu:first').click();
}

/*
 * Create edit widgets for a top-level menu and its submenus and append them all to the DOM.
 *
 * Inputs : 
 *	menu - an object containing menu data
 *	afterNode - if specified, the new menu widget will be inserted after this node. Otherwise,
 *	            it will be added to the end of the list.
 * Outputs :
 *	Object with two fields - 'menu' and 'submenu' - containing the DOM nodes of the created widgets.
 */
function outputTopMenu(menu, afterNode){
	//Create a container for menu items, even if there are none
	var submenu = buildSubmenu(menu.items);
	
	//Create the menu widget
	var menu_obj = buildTopMenu(menu);
	menu_obj.data('submenu_id', submenu.attr('id'));
	
	//Display
	submenu.appendTo('#ws_submenu_box');
	if ( typeof afterNode != 'undefined' ){
		$(afterNode).after(menu_obj);
	} else {
		menu_obj.appendTo('#ws_menu_box');
	}
	
	return {
		'menu' : menu_obj,
		'submenu' : submenu
	};
}

/*
 * Create an edit widget for a top-level menu.
 */
function buildTopMenu(menu){
	var subclass = '';
	if ( menu.separator ) {
		subclass = subclass + ' ws_menu_separator';
	}
	
	//Create the menu HTML
	var menu_obj = $('<div></div>')
		.attr('class', "ws_container ws_menu "+subclass)
		.attr('id', 'ws-topmenu-'+(wsIdCounter++))
		.data('defaults', menu.defaults)
		.data('initial_values', getCurrentFieldValues(menu))
		.data('field_editors_created', false);
	
	//Add a header and a container for property editors (to improve performance
	//the editors themselves are created later, when the user tries to access them
	//for the first time).
	var contents = [];
	contents.push(
		'<div class="ws_item_head">',
			menu.separator ? '' : '<a class="ws_edit_link"> </a><div class="ws_flag_container"> </div>',
			'<span class="ws_item_title">',
				((menu.menu_title!=null) ? menu.menu_title : menu.defaults.menu_title),
			'&nbsp;</span>',
		'</div>',
		'<div class="ws_editbox" style="display: none;"></div>'
	);
	menu_obj.append(contents.join(''));
	
	//Apply flags based on the item's state
	if (menu.missing && !getFieldValue(menu, 'custom', false)) {
		addMenuFlag(menu_obj, 'missing');
	}
	if (menu.hidden) {
		addMenuFlag(menu_obj, 'hidden');
	}
	if (menu.unused) {
		addMenuFlag(menu_obj, 'unused');
	}
	if (getFieldValue(menu, 'custom', false)) {
		addMenuFlag(menu_obj, 'custom_item');
	}
	
	if ( !menu.separator ){
		//Allow the user to drag menu items to top-level menus
		menu_obj.droppable({
			'hoverClass' : 'ws_menu_drop_hover',
			
			'accept' : (function(thing){
				return thing.hasClass('ws_item');
			}),
			
			'drop' : (function(event, ui){
				var droppedItemData = readItemState(ui.draggable);
				var new_item = buildMenuItem(droppedItemData);
				var submenu = $('#' + menu_obj.data('submenu_id'));
				submenu.append(new_item);
				ui.draggable.remove();
			})
		});
	}
	
	return menu_obj;
}

/*
 * Create and populate a submenu container.
 */
function buildSubmenu(items){
	//Create a container for menu items, even if there are none
	var submenu = $('<div class="ws_submenu"style="display:none;"></div>');
	submenu.attr('id', 'ws-submenu-'+(wsIdCounter++));
	
	//Only show menus that have items. 
	//Skip arrays (with a length) because filled menus are encoded as custom objects.
	var entry = null;
	if (items && (typeof items != 'Array')){
		for (var item_file in items){
			entry = buildMenuItem(items[item_file]);
			if ( entry ){
				submenu.append(entry);
			}
		}
	}
	
	//Make the submenu sortable
	makeBoxSortable(submenu);
	
	return submenu;
}

/*
 * Create an edit widget for a menu entry and return it.
 */
function buildMenuItem(entry){
	if (!entry.defaults) {
		return null
	};
	
	var item = $('<div class="ws_container ws_item">')
		.data('defaults', entry.defaults)
		.data('initial_values', getCurrentFieldValues(entry))
		.data('field_editors_created', false);
	
	//Add a header and a container for property editors (to improve performance
	//the editors themselves are created later, when the user tries to access them
	//for the first time).
	var contents = [];
	contents.push(
		'<div class="ws_item_head">',
			'<a class="ws_edit_link"> </a><div class="ws_flag_container"> </div>',
			'<span class="ws_item_title">',
				((entry.menu_title!=null)?entry.menu_title:entry.defaults.menu_title),
			'&nbsp;</span>',
		'</div>',
		'<div class="ws_editbox" style="display: none;"></div>'
	);
	item.append(contents.join(''));
		
	//Apply flags based on the item's state
	if (entry.missing && !getFieldValue(entry, 'custom', false)) {
		addMenuFlag(item, 'missing');
	}
	if (entry.hidden) {
		addMenuFlag(item, 'hidden');
	}
	if (entry.unused) {
		addMenuFlag(item, 'unused');
	}
	if (getFieldValue(entry, 'custom', false)) {
		addMenuFlag(item, 'custom_item');
	}
	
	return item;
}

/*
 * List of all menu fields that have an associated editor
 */ 
var knownMenuFields = {
	'menu_title' : {
		caption : 'Menu title',
        standardCaption : true,
		advanced : false,
		type : 'text',
		defaultValue: '',
		visible: true
	},
	'access_level' : {
		caption: 'Required capability',
        standardCaption : true,
		advanced : false,
		type : 'text',
		defaultValue: 'read',
		addDropdown : true,
		visible: true
	},
	'file' : {
		caption: 'URL',
		advanced : false,
        standardCaption : true,
		type : 'text',
		defaultValue: '',
		addDropdown : 'ws_page_selector',
		visible: true
	},
	'page_title' : {
		caption: "Window title",
        standardCaption : true,
		advanced : true,
		type : 'text',
		defaultValue: '',
		visible: true
	},
	'open_in' : {
		caption: 'Open in',
        standardCaption : true,
		advanced : true,
		type : 'select',
		options : {
			'Same window or tab' : 'same_window',
			'New window' : 'new_window',
			'Frame' : 'iframe'
		},
		defaultValue: 'same_window',
		visible: false
	},
	'css_class' : {
		caption: 'CSS classes',
        standardCaption : true,
		advanced : true,
		type : 'text',
		defaultValue: '',
		visible: true
	},
	'hookname' : {
		caption: 'Hook name',
        standardCaption : true,
		advanced : true,
		type : 'text',
		defaultValue: '',
		visible: true
	},
	'icon_url' : {
		caption: 'Icon URL',
        standardCaption : true,
		advanced : true,
		type : 'text',
		defaultValue: 'div',
		visible: true
	},
    'custom' : {
        caption : 'Custom',
        standardCaption : false,
        advanced: true,
        type: 'checkbox',
        defaultValue: false,
        visible: false
    }
};

/*
 * Create editors for the visible fields of a menu entry and append them to the specified node.
 */
function buildEditboxFields(containerNode, entry){
	var fields = knownMenuFields;
    
    var basicFields = $('<div class="ws_edit_panel ws_basic"></div>').appendTo(containerNode);
    var advancedFields = $('<div class="ws_edit_panel ws_advanced"></div>').appendTo(containerNode);
    
    if ( hideAdvancedSettings ){
    	advancedFields.css('display', 'none');
    }
	
	for (var field_name in fields){
		var field = buildEditboxField(entry, field_name, fields[field_name]);
		if (field){
            if (fields[field_name].advanced){
                advancedFields.append(field);
            } else {
                basicFields.append(field);
            }
		}
	}
	
	//Add a link that shows/hides advanced fields
	containerNode.append(
		'<div class="ws_toggle_container"><a href="#" class="ws_toggle_advanced_fields"'+
		(hideAdvancedSettings?'':' style="display:none;"')+'>'+
		(hideAdvancedSettings?captionShowAdvanced:captionHideAdvanced)
		+'</a></div>'
	);
}

/*
 * Create an editor for a specified field.
 */
function buildEditboxField(entry, field_name, field_settings){
	if (typeof entry[field_name] === 'undefined') {
		return null; //skip fields this entry doesn't have
	}
	
	var default_value = (typeof entry.defaults[field_name] != 'undefined')?entry.defaults[field_name]:field_settings.defaultValue;
	var value = (entry[field_name]!=null)?entry[field_name]:default_value;
	
	//Build a form field of the appropriate type 
	var inputBox = null;
	switch(field_settings.type){
		case 'select':
			inputBox = $('<select class="ws_field_value">');
			var option = null;
			for( var optionTitle in field_settings.options ){
				option = $('<option>')
					.val(field_settings.options[optionTitle])
					.text(optionTitle);
				if ( field_settings.options[optionTitle] == value ){
					option.attr('selected', 'selected');
				}
				option.appendTo(inputBox);
			}
			break;
            
        case 'checkbox':
            inputBox = $('<label><input type="checkbox"'+(value?' checked="checked"':'')+ ' class="ws_field_value"> '+
                field_settings.caption+'</label>'
            );
            break;
            
		case 'text':
		default:
			inputBox = $('<input type="text" class="ws_field_value">').val(value);
	}
	
	
	var className = "ws_edit_field ws_edit_field-"+field_name;
	if(entry[field_name]==null){
		className += ' ws_input_default';
	}
	
	var hasDropdown = (typeof(field_settings['addDropdown']) != 'undefined') && field_settings.addDropdown;
	if ( hasDropdown ){
		className += ' ws_has_dropdown';
	}
		
	var editField = $('<div>' + (field_settings.standardCaption?(field_settings.caption+'<br>'):'') + '</div>')
		.attr('class', className)
		.append(inputBox);
		
	if ( hasDropdown ){
		//Add a dropdown button
		var dropdownId = 'ws_cap_selector';
		if ( typeof(field_settings.addDropdown) == 'string' ){
			dropdownId = field_settings.addDropdown;
		}
		editField.append(
			$('<input type="button" value="&#9660;">')
				.addClass('button ws_dropdown_button')
				.attr('tabindex', '-1')
				.data('dropdownId', dropdownId)
		);
	}
	
	editField
		.append('<img src="'+imagesUrl+'/transparent16.png" class="ws_reset_button" title="Reset to default value">&nbsp;</img>')
		.data('field_name', field_name)
		.data('default_value', default_value);
		
	if ( !field_settings.visible ){
		editField.css('display', 'none');
	}
		
	return editField;	
}

/*
 * Get the current values of all menu fields (ignores defaults). 
 * Returns an object containing each field as a separate property. 
 */
function getCurrentFieldValues(entry){
	var values = {};
	
	for (var field_name in knownMenuFields){
		if (typeof entry[field_name] === 'undefined') {
			continue; //skip fields this entry doesn't have
		}
		values[field_name] = entry[field_name];
	}
	
	values.defaults = entry.defaults;
	
	return values;
}

/*
 * Get the current value of a single menu field. 
 *
 * If the specified field is not set, this function will attempt to retrieve it 
 * from the "defaults" property of the menu object. If *that* fails, it will return
 * the value of the optional third argument defaultValue. 
 */
function getFieldValue(entry, fieldName, defaultValue){
	if ( (typeof entry[fieldName] === 'undefined') || (entry[fieldName] === null) ) {
		if ( (typeof entry['defaults'] === 'undefined') || (typeof entry['defaults'][fieldName] === 'undefined') ){
			return defaultValue;
		} else {
			return entry.defaults[fieldName];
		}
	} else {
		return entry[fieldName];
	}
}

/*
 * Make a menu container sortable
 */
function makeBoxSortable(menuBox){
	//Make the submenu sortable
	menuBox.sortable({
		items: '> .ws_container',
		cursor: 'move',
		dropOnEmpty: true,
		cancel : '.ws_editbox, .ws_edit_link'
	});
}

/***************************************************************************
                       Parsing & encoding menu inputs
 ***************************************************************************/

/* 
 * Encode the current menu structure as JSON
 *
 * Returns : 
 *	A JSON-encoded string representing the current menu tree loaded in the editor.
 */
function encodeMenuAsJSON(){
	var tree = readMenuTreeState();
	return $.toJSON(tree);
}

function readMenuTreeState(){
	var tree = {};
	var menu_position = 0;
		
	//Gather all menus and their items
	$('#ws_menu_box .ws_menu').each(function(i) {
		var menu = readMenuState(this, menu_position++);
		
		//Attach the current menu to the main struct
		var filename = (menu.file !== null)?menu.file:menu.defaults.file;
		tree[filename] = menu;
	});
	
	return tree;
}

/*
 * Extract the settings of a top-level menu from its editor widget(s).
 *
 * Inputs : 
 * 	menu_div - DOM node, typically one with the .ws_menu class.
 *  position - The current menu position (int).
 *
 * Output : 
 *  A menu object in the tree format.  	
 *
 */
function readMenuState(menu_div, position){
	menu_div = $(menu_div);
	var menu = readAllFields(menu_div);
	
	menu.defaults = menu_div.data('defaults');
	
	menu.position = position;
	menu.defaults.position = position; //the real default value will later overwrite this
	
	menu.separator = menu_div.hasClass('ws_menu_separator');
	menu.hidden = menu_div.hasClass('ws_hidden'); 

	//Gather the menu's items, if any
	menu.items = {};
	var item_position = 0;
	$('#'+menu_div.data('submenu_id')).find('.ws_item').each(function (i) {
		var item = readItemState(this, item_position++);
		menu.items[ (item.file?item.file:item.defaults.file) ] = item;
	});
	
	return menu;
}

/*
 * Extract the current menu item settings from its editor widget.
 *
 * Inputs : 
 *	item_div - DOM node containing the editor widget, usually with the .ws_item class.  
 *	position - Menu item position among its sibling menu items.   
 *
 */
function readItemState(item_div, position){
	var item_div = $(item_div);
	var item = readAllFields(item_div);
	
	item.defaults = item_div.data('defaults');
	
	//Save the position data
	if ( typeof position == 'undefined' ){
		position = 0;
	}
	item.position = position;
	item.defaults.position = position;
	
	//Check if the item is hidden
	item.hidden = item_div.hasClass('ws_hidden');
	
	return item;
}

/*
 * Extract the values of all menu/item fields present in a container node
 *
 * Inputs:
 *	container - a jQuery collection representing the node to read.  
 */
function readAllFields(container){
	if ( !container.hasClass('ws_container') ){
		container = container.parents('ws_container').first();
	}
	
	if ( !container.data('field_editors_created') ){
		return container.data('initial_values');
	}
	
	var state = {};
	
	//Iterate over all fields of the item
	container.find('.ws_edit_field').each(function() {
		var field = $(this);
		
		//Get the name of this field
		field_name = field.data('field_name');
		//Skip if unnamed
		if (!field_name) return true;
		
		//Find the field (usually an input or select element).
		input_box = field.find('.ws_field_value');
		
		//Save null if default used, custom value otherwise
		if (field.hasClass('ws_input_default')){
			state[field_name] = null;
		} else {
            if ( input_box.attr('type') == 'checkbox' ){
                state[field_name] = input_box.is(':checked');
            } else {
                state[field_name] = input_box.val();
            }
		}
	});
	
	return state;
}


/***************************************************************************
                         Flag manipulation
 ***************************************************************************/

var item_flags = {
	'custom_item' : 'This is a custom menu item',
	'unused' : 'This item was automatically (re)inserted into your custom menu because it is present in the default WordPress menu',
	'missing' : 'This item is not present in the default WordPress menu.',
	'hidden' : 'This item is hidden' 
}
 
function addMenuFlag(item, flag){
	item = $(item);
	
	var item_class = 'ws_' + flag;
	var img_class = 'ws_' + flag + '_flag';
	
	item.addClass(item_class);
	//Add the flag image
	var flag_container = item.find('.ws_flag_container');
	if ( flag_container.find('.' + img_class).length == 0 ){
		flag_container.append('<div class="ws_flag '+img_class+'" title="'+item_flags[flag]+'"></div>');
	}
}

function removeMenuFlag(item, flag){
	item = $(item);
	var item_class = 'ws_' + flag;
	var img_class = 'ws_' + flag + '_flag';
	
	item.removeClass('ws_' + flag);
	item.find('.' + img_class).remove();
}

function toggleMenuFlag(item, flag){
	if (menuHasFlag(item, flag)){
		removeMenuFlag(item, flag);
	} else {
		addMenuFlag(item, flag);
	}
}

function menuHasFlag(item, flag){
	return $(item).hasClass('ws_'+flag);
}

function clearMenuFlags(item){
	item = $(item);
	item.find('.ws_flag').remove();
	for(var flag in item_flags){
		item.removeClass('ws_'+flag);
	}	
}

//Cut & paste stuff
var menu_in_clipboard = null;
var submenu_in_clipboard = null;
var item_in_clipboard = null;
var ws_paste_count = 0;

$(document).ready(function(){
	if (window.wsMenuEditorPro) {
		knownMenuFields['open_in'].visible = true;
	}
	
	//Make the top menu box sortable (we only need to do this once)
    var mainMenuBox = $('#ws_menu_box');
    makeBoxSortable(mainMenuBox);
    
	/***************************************************************************
	                  Event handlers for editor widgets
	 ***************************************************************************/
	
	//Highlight the clicked menu item and show it's submenu
	var currentVisibleSubmenu = null;
    $('#ws_menu_editor .ws_container').live('click', (function () {
		var container = $(this);
		if ( container.hasClass('ws_active') ){
			return;
		}
		
		//Highlight the active item and un-highlight the previous one
		container.addClass('ws_active')
		container.siblings('.ws_active').removeClass('ws_active');
		if ( container.hasClass('ws_menu') ){
			//Show/hide the appropriate submenu
			if ( currentVisibleSubmenu ){
				currentVisibleSubmenu.hide();
			}
			currentVisibleSubmenu = $('#'+container.data('submenu_id')).show();
		}
    }));
    
    //Show/hide a menu's properties
    $('#ws_menu_editor .ws_edit_link').live('click', (function () {
    	var container = $(this).parents('.ws_container').first(); 
		var box = container.find('.ws_editbox');
		
		//For performance, the property editors for each menu are only created 
		//when the user tries to access access them for the first time.
		if ( !container.data('field_editors_created') ){
			buildEditboxFields(box, container.data('initial_values'));
			container.data('field_editors_created', true);
		}
		
		$(this).toggleClass('ws_edit_link_expanded');
		//show/hide the editbox
		if ($(this).hasClass('ws_edit_link_expanded')){
			box.show();
		} else {
			//Make sure changes are applied before the menu is collapsed
			box.find('input').change();
			box.hide();
		}
    }));
    
    //The "Default" button : Reset to default value when clicked
    $('#ws_menu_editor .ws_reset_button').live('click', (function () {
        //Find the field div (it holds the default value)
        var field = $(this).parent();
    	//Find the related input field
		var input = field.find('.ws_field_value');
		if ( (input.length > 0) && (field.length > 0) ) {
			//Set the value to the default
            if (input.attr('type') == 'checkbox'){
                if ( field.data('default_value') ){
                    input.attr('checked', 'checked');
                } else {
                    input.removeAttr('checked');
                }
            } else {
                input.val(field.data('default_value'));   
            }
			field.addClass('ws_input_default');
			//Trigger the change event to ensure consistency
			input.change();
		}	
	}));
	
	//When a field is edited, change it's appearance if it's contents don't match the default value.
    function fieldValueChange(){
        var input = $(this);
		var field = input.parents('.ws_edit_field').first();
        
        if ( input.attr('type') == 'checkbox' ){
            var value = input.is(':checked');
        } else {
            var value = input.val();
        }
		
		if ( field.data('default_value') != value ) {
			field.removeClass('ws_input_default');
		}
		
        var fieldName = field.data('field_name');
		if ( fieldName == 'menu_title' ){
            //If the changed field is the menu title, update the header
			field.parents('.ws_container').first().find('.ws_item_title').html(input.val()+'&nbsp;');
		} else if ( fieldName == 'custom' ){
            //Show/hide the custom flag
            var myContainer = field.parents('.ws_container').first();
            if ( value ){
    			addMenuFlag(myContainer, 'custom_item');
    		} else {
    			removeMenuFlag(myContainer, 'custom_item');
    		}
		} else if (fieldName == 'file' ){
			//A menu must always have a non-empty URL. If the user deletes the current value,
			//reset back to the default.
			if ( value == '' ){
				field.find('.ws_reset_button').click();
			}
		}
    }
	$('#ws_menu_editor .ws_field_value').live('click', fieldValueChange);
	//jQuery 1.3.x can't catch 'change' events with live(), 
	//so we handle that by using event delegation instead.
	//TODO: Update for jQuery 1.4.2 in WP 3.0
	$('#ws_menu_editor').change(function(event){
		var target = $(event.target);
		if ( target.is('.ws_field_value') ){
			fieldValueChange.call(target, event);
		}
	});
	
	//Show/hide advanced fields
	$('#ws_menu_editor .ws_toggle_advanced_fields').live('click', function(){
		var self = $(this);
		var advancedFields = self.parents('.ws_container').first().find('.ws_advanced');
		
		if ( advancedFields.is(':visible') ){
			advancedFields.hide();
			self.text(captionShowAdvanced);
		} else {
			advancedFields.show();
			self.text(captionHideAdvanced);
		}
		
		return false;
	});
	
	
	/***************************************************************************
	 Dropdown list for combobox fields
	 ***************************************************************************/

	var availableDropdowns = {
		'ws_cap_selector' : {
			list : $('#ws_cap_selector'),
			currentOwner : null,
			timeoutForgetOwner : 0
		},
		'ws_page_selector' : {
			list : $('#ws_page_selector'),
			currentOwner : null,
			timeoutForgetOwner : 0
		}
	};
	 
	//Show/hide the capability dropdown list when the button is clicked   
	$('#ws_menu_editor input.ws_dropdown_button').live('click',function(event){
		var button = $(this);
		var inputBox = button.parent().find('input.ws_field_value');
		
		var dropdown = availableDropdowns[button.data('dropdownId')];
		
		clearTimeout(dropdown.timeoutForgetOwner);
		dropdown.timeoutForgetOwner = 0;
		
		//If we already own the list, hide it and rescind ownership.
		if ( dropdown.currentOwner == this ){
			dropdown.list.hide();
			
			dropdown.currentOwner = null;
			inputBox.focus();
			
			return;
		}
		dropdown.currentOwner = this; //Got ye now!
		
		//Move the dropdown near to the button
		var inputPos = inputBox.offset();
		dropdown.list.css({
			position: 'absolute',
			left: inputPos.left,
			top: inputPos.top + inputBox.outerHeight()
		});
		
		//Pre-select the current capability (will clear selection if there's no match)
		dropdown.list.val(inputBox.val());
		
		dropdown.list.show();
		dropdown.list.focus();
	});
	
	//Also show it when the user presses the down arrow in the input field  
	$('#ws_menu_editor .ws_has_dropdown input.ws_field_value').live('keyup', function(event){
		if ( event.which == 40 ){
			$(this).parent().find('input.ws_dropdown_button').click();
		}
	});
	
	//Event handlers for the dropdowns themselves
	var dropdownNodes = $('.ws_dropdown');
	
	//Hide capability dropdown when it loses focus
	dropdownNodes.blur(function(event){
		var dropdown = availableDropdowns[$(this).attr('id')];
		
		dropdown.list.hide();
		/*
		* Hackiness : make sure the list doesn't disappear & immediately reappear 
		* when the event that caused it to lose focus was the user clicking on the
		* dropdown button.
		*/
		dropdown.timeoutForgetOwner = setTimeout(
			(function(){
				dropdown.currentOwner = null;
			}), 
			200
		);
	});
	
	dropdownNodes.keydown(function(event){
		var dropdown = availableDropdowns[$(this).attr('id')];
		
		//Also hide it when the user presses Esc
		if ( event.which == 27 ){
			var inputBox = $(dropdown.currentOwner).parent().find('input.ws_field_value');
			
			dropdown.list.hide();
			if ( dropdown.currentOwner ){
				$(dropdown.currentOwner).parent().find('input.ws_field_value').focus();
			}
			dropdown.currentOwner = null;
			
		//Select an item & hide the list when the user presses Enter or Tab
		} else if ( (event.which == 13) || (event.which == 9) ){
			dropdown.list.hide();
			
			var inputBox = $(dropdown.currentOwner).parent().find('input.ws_field_value');
			if ( dropdown.list.val() ){
				inputBox.val(dropdown.list.val());
				inputBox.change();
			}
			
			inputBox.focus();
			dropdown.currentOwner = null;
			
			event.preventDefault();
		}
	});
	
	//Eat Tab keys to prevent focus theft. Required to make the "select item on Tab" thing work. 
	dropdownNodes.keyup(function(event){
		if ( event.which == 9 ){
			event.preventDefault();
		}
	})
	
	
	//Update the input & hide the list when an option is clicked
	dropdownNodes.click(function(){
		var dropdown = availableDropdowns[$(this).attr('id')];
		
		if ( !dropdown.currentOwner || !dropdown.list.val() ){
			return;
		}
		dropdown.list.hide();
		
		var inputBox = $(dropdown.currentOwner).parent().find('input.ws_field_value');
		inputBox.val(dropdown.list.val()).change().focus();
		dropdown.currentOwner = null;
	});
	
	//Highlight an option when the user mouses over it (doesn't work in IE)
	dropdownNodes.mousemove(function(event){
		if ( !event.target ){
			return;
		}
		
		var option = $(event.target);
		if ( !option.attr('selected') && option.attr('value')){
			option.attr('selected', 'selected');
		}
	});	
	
    
    /*************************************************************************
	                           Menu toolbar buttons
	 *************************************************************************/
	//Show/Hide menu
	$('#ws_hide_menu').click(function () {
		//Get the selected menu
		var selection = $('#ws_menu_box .ws_active');
		if (!selection.length) return;
		
		//Mark the menu as hidden/visible
		//selection.toggleClass('ws_hidden');
		toggleMenuFlag(selection, 'hidden');
		
		//Also mark all of it's submenus as hidden/visible
		if ( menuHasFlag(selection,'hidden') ){
			$('#' + selection.data('submenu_id') + ' .ws_item').each(function(){
				addMenuFlag(this, 'hidden');
			});
		} else {
			$('#' + selection.data('submenu_id') + ' .ws_item').each(function(){
				removeMenuFlag(this, 'hidden');
			});
		}
	});
	
	//Delete menu
	$('#ws_delete_menu').click(function () {
		//Get the selected menu
		var selection = $('#ws_menu_box .ws_active');
		if (!selection.length) return;
		
		if (confirm('Delete this menu?')){
			//Delete the submenu first
			$('#' + selection.data('submenu_id')).remove();
			//Delete the menu
			selection.remove();
		}
	});
	
	//Copy menu
	$('#ws_copy_menu').click(function () {
		//Get the selected menu
		var selection = $('#ws_menu_box .ws_active');
		if (!selection.length) return;
		
		//Store a copy of the current menu state in clipboard
		menu_in_clipboard = readMenuState(selection);
	});
	
	//Cut menu
	$('#ws_cut_menu').click(function () {
		//Get the selected menu
		var selection = $('#ws_menu_box .ws_active');
		if (!selection.length) return;
		
		//Store a copy of the current menu state in clipboard
		menu_in_clipboard = readMenuState(selection);
		
		//Remove the original menu and submenu		
		selection.remove();
		$('#'+selection.data('submenu_id')).remove();
	});
	
	//Paste menu
	$('#ws_paste_menu').click(function () {
		//Check if anything has been copied/cut
		if (!menu_in_clipboard) return;

		var menu = $.extend(true, {}, menu_in_clipboard);

		//The user shouldn't need to worry about giving separators a unique filename.
		if (menu.separator) {
			menu.defaults.file = 'separator_'+randomMenuId();
		}

		//Get the selected menu
		var selection = $('#ws_menu_box .ws_active');
		
		if (selection.length > 0) {
			//If a menu is selected add the pasted item after it
			outputTopMenu(menu, selection);
		} else {
			//Otherwise add the pasted item at the end
			outputTopMenu(menu);
		}
	});
	
	//New menu
	$('#ws_new_menu').click(function () {
		ws_paste_count++;
		
		//The new menu starts out rather bare
		var randomId = 'custom_menu_'+randomMenuId();		
		var menu = {
			menu_title : null,
			page_title : null,
			access_level : null,
			file : null,
			css_class : null,
			icon_url : null,
			hookname : null,
			position : null,
			custom : null, 
			open_in: null,
			items : {}, //No items
			defaults : {
				menu_title : 'Custom Menu '+ws_paste_count,
				page_title : '',
				access_level : 'read',
				file : randomId,
				css_class : 'menu-top',
				icon_url : 'images/generic.png',
				hookname : randomId,
				open_in : 'same_window',
				custom: true, //Important : flag the new menu as custom, or it won't show up after saving.
				position : 0,
				separator: false
			}
		};
		
		//Insert the new menu
		result = outputTopMenu(menu);
				
		//The menus's editbox is always open
		result.menu.find('.ws_edit_link').click();
	});
	
	//New separator
	$('#ws_new_separator').click(function () {
		ws_paste_count++;
		
		//The new menu starts out rather bare
		var randomId = 'separator_'+randomMenuId();		
		var menu = {
			menu_title : null,
			page_title : null,
			access_level : null,
			file : null,
			css_class : null,
			icon_url : null,
			hookname : null,
			position : null,
			separator : true, //Flag as a separator
			custom : null, 
			open_in : null,
			items : {}, //No items
			defaults : {
				menu_title : '',
				page_title : '',
				access_level : 'read',
				file : randomId,
				css_class : 'wp-menu-separator',
				icon_url : '',
				hookname : '',
				position : 0,
				custom: false, //Separators don't need to flagged as custom to be retained. 
				open_in: 'same_window',
				separator: true
			}
		};
		
		//Insert the new menu
		result = outputTopMenu(menu);
	});
	
	/*************************************************************************
	                          Item toolbar buttons
	 *************************************************************************/
	//Show/Hide item
	$('#ws_hide_item').click(function () {
		//Get the selected item
		var selection = $('#ws_submenu_box .ws_submenu:visible .ws_active');
		if (!selection.length) return;
		
		//Mark the item as hidden/visible
		toggleMenuFlag(selection, 'hidden');
	});
	
	//Delete menu
	$('#ws_delete_item').click(function () {
		//Get the selected menu
		var selection = $('#ws_submenu_box .ws_submenu:visible .ws_active');
		if (!selection.length) return;
		
		if (confirm('Delete this menu item?')){
			//Delete the item
			selection.remove();
		}
	});
	
	//Copy item
	$('#ws_copy_item').click(function () {
		//Get the selected item
		var selection = $('#ws_submenu_box .ws_submenu:visible .ws_active');
		if (!selection.length) return;
		
		//Store a copy of item state in the clipboard
		item_in_clipboard = readItemState(selection);
	});
	
	//Cut item
	$('#ws_cut_item').click(function () {
		//Get the selected item
		var selection = $('#ws_submenu_box .ws_submenu:visible .ws_active');
		if (!selection.length) return;
		
		//Store a copy of item state in the clipboard
		item_in_clipboard = readItemState(selection);
		
		//Remove the original item		
		selection.remove();
	});
	
	//Paste item
	$('#ws_paste_item').click(function () {
		//Check if anything has been copied/cut
		if (!item_in_clipboard) return;

		//Create a new editor widget for the copied item
		var item = $.extend(true, {}, item_in_clipboard);
		var new_item = buildMenuItem(item);
		
		//Get the selected menu
		var selection = $('#ws_submenu_box .ws_submenu:visible .ws_active');
		if (selection.length > 0) {
			//If an item is selected add the pasted item after it
			selection.after(new_item); 
		} else {
			//Otherwise add the pasted item at the end
			$('#ws_submenu_box .ws_submenu:visible').append(new_item); 
		}
		
		new_item.show();
	});
	
	//New item
	$('#ws_new_item').click(function () {
		if ($('.ws_submenu:visible').length < 1) { 
			return; //Abort if no submenu visible 
		}
		
		ws_paste_count++;
		var entry = {
			menu_title : null,
			page_title : null,
			access_level : null,
			file : null,
			custom : null,
			open_in : null,
			defaults : {
				menu_title : 'Custom Item ' + ws_paste_count,
				page_title : '',
				access_level : 'read',
				file : 'custom_item_'+randomMenuId(),
				is_plugin_page : false,
				custom: true,
				open_in : 'same_window'
			}		
		};
		
		var menu = buildMenuItem(entry);
		
		//Insert the item into the box
		$('#ws_submenu_box .ws_submenu:visible').append(menu);
		
		//The items's editbox is always open
		menu.find('.ws_edit_link').click();
	});
	
	function compareMenus(a, b){
		function jsTrim(str){
			return str.replace(/^\s+|\s+$/g, "");
		}
		
		var aTitle = jsTrim( $(a).find('.ws_item_title').text() );
		var bTitle = jsTrim( $(b).find('.ws_item_title').text() );
		
		aTitle = aTitle.toLowerCase();
		bTitle = bTitle.toLowerCase();
						
		return aTitle > bTitle ? 1 : -1;
	}
	
	//Sort items in ascending order
	$('#ws_sort_ascending').click(function () {
		var submenu = $('#ws_submenu_box .ws_submenu:visible');
		if (submenu.length < 1) { 
			return; //Abort if no submenu visible 
		}
		
		submenu.find('.ws_container').sort(compareMenus);
	});
	
	//Sort items in descending order
	$('#ws_sort_descending').click(function () {
		var submenu = $('#ws_submenu_box .ws_submenu:visible');
		if (submenu.length < 1) { 
			return; //Abort if no submenu visible 
		}
		
		submenu.find('.ws_container').sort((function(a, b){
			return -compareMenus(a, b);
		}));
	});
	
	//==============================================
	//				Main buttons
	//==============================================
	
	//Save Changes - encode the current menu as JSON and save
	$('#ws_save_menu').click(function () {
		var data = encodeMenuAsJSON();
		$('#ws_data').val(data);
		$('#ws_main_form').submit();
	});
	
	//Load default menu - load the default WordPress menu
	$('#ws_load_menu').click(function () {
		if (confirm('Are you sure you want to load the default WordPress menu?')){
			outputWpMenu(defaultMenu);
		}
	});
	
	//Reset menu - re-load the custom menu. Discards any changes made by user.
	$('#ws_reset_menu').click(function () {
		if (confirm('Undo all changes made in the current editing session?')){
			outputWpMenu(customMenu);
		}
	});
	
	//Export menu - download the current menu as a file
	$('#export_dialog').dialog({ 
		autoOpen: false,
		closeText: ' ',
		modal: true,
		minHeight: 100
	});
	
	$('#ws_export_menu').click(function(){
		var button = $(this);
		button.attr('disabled', 'disabled');
		button.val('Exporting...');
		
		$('#export_complete_notice, #download_menu_button').hide();
		$('#export_progress_notice').show();
		$('#export_dialog').dialog('open');
		
		//Encode and store the menu for download
		var menu = readMenuTreeState();
		var exportData = {
			'format' : exportFormatString,
			'menu' : menu
		};
		exportData = $.toJSON(exportData);
		
		$.post(
			adminAjaxUrl,
			{
				'data' : exportData,
				'action' : 'export_custom_menu',
				'_ajax_nonce' : exportMenuNonce
			},
			function(data, textStatus){
				button.val('Export');
				button.removeAttr('disabled');
				
				if ( typeof data['error'] != 'undefined' ){
					$('#export_dialog').dialog('close');
					alert(data.error);
				}
				
				if ( (typeof data['download_url'] != 'undefined') && data.download_url ){
					//window.location = data.download_url;
					$('#download_menu_button').attr('href', data.download_url);
					$('#export_progress_notice').hide();
					$('#export_complete_notice, #download_menu_button').show();
				}
			},
			'json'
		);
	});
	
	$('#ws_cancel_export').click(function(){
		$('#export_dialog').dialog('close');
	});
	
	$('#download_menu_button').click(function(){
		$('#export_dialog').dialog('close');
	});
	
	//Import menu - upload an exported menu and show it in the editor
	$('#import_dialog').dialog({ 
		autoOpen: false,
		closeText: ' ',
		modal: true
	});
	
	$('#ws_cancel_import').click(function(){
		$('#import_dialog').dialog('close');
	});
	
	$('#ws_import_menu').click(function(){
		$('#import_progress_notice, #import_progress_notice2, #import_complete_notice').hide();
		$('#import_menu_form').resetForm();
		//The "Upload" button is disabled until the user selects a file
		$('#ws_start_import').attr('disabled', 'disabled'); 
		
		$('#import_dialog .hide-when-uploading').show();
		
		$('#import_dialog').dialog('open');
	})
	
	$('#import_file_selector').change(function(){
		if ( $(this).val() ){
			$('#ws_start_import').removeAttr('disabled');
		} else {
			$('#ws_start_import').attr('disabled', 'disabled');
		};
	});
	
	//AJAXify the upload form
	$('#import_menu_form').ajaxForm({
		dataType : 'json',
		beforeSubmit: function(formData, $form, options) { 
			
			//Check if the user has selected a file
			for(var i = 0; i < formData.length; i++){
				if ( formData[i].name == 'menu' ){
					if ( (typeof formData[i]['value'] == 'undefined') || !formData[i]['value']){
						alert('Select a file first!');
						return false;
					}
				}
			}
			
			$('#import_dialog .hide-when-uploading').hide();
			$('#import_progress_notice').show();
			
			$('#ws_start_import').attr('disabled', 'disabled');
		},
		success: function(data){
			if ( !$('#import_dialog').dialog('isOpen') ){
				//Whoops, the user closed the dialog while the upload was in progress.
				//Discard the response silently.
				return;				
			};
			
			if ( typeof data['error'] != 'undefined' ){
				alert(data.error);
				//Let the user try again
				$('#import_menu_form').resetForm();
				$('#import_dialog .hide-when-uploading').show();
			}
			$('#import_progress_notice').hide();
			
			if ( (typeof data['menu'] != 'undefined') && data.menu ){
				//Whee, we got back a (seemingly) valid menu. A veritable miracle!
				//Lets load it into the editor.
				$('#import_progress_notice2').show();
				outputWpMenu(data.menu);
				$('#import_progress_notice2').hide();
				//Display a success notice, then automatically close the window after a few moments
				$('#import_complete_notice').show();
				setTimeout((function(){
					//Close the import dialog
					$('#import_dialog').dialog('close');
				}), 500);				
			}
			
		}
	});

	//Flag closed hints as hidden by sending the appropriate AJAX request to the backend.
	$('.ws_hint_close').click(function() {
		var hint = $(this).parents('.ws_hint').first();
		hint.hide();
		wsEditorData.showHints[hint.attr('id')] = false;
		$.post(
			wsEditorData.adminAjaxUrl,
			{
				'action' : 'ws_ame_hide_hint',
				'hint' : hint.attr('id')
			}
		);
	});
	
	//Finally, show the menu
    outputWpMenu(customMenu);
  });
	
})(jQuery);

//==============================================
//				Screen options
//==============================================

jQuery(function($){
	var screenOptions = $('#ws-ame-screen-meta-contents');
	var checkbox = screenOptions.find('#ws-hide-advanced-settings');
	
	if ( hideAdvancedSettings ){
		checkbox.attr('checked', 'checked');
	} else {
		checkbox.removeAttr('checked');
	}
	
	//Update editor state when settings change
	checkbox.click(function(){
		hideAdvancedSettings = $(this).attr('checked'); //Using '$(this)' instead of 'checkbox' due to jQuery bugs
		if ( hideAdvancedSettings ){
			$('#ws_menu_editor div.ws_advanced').hide();
			$('#ws_menu_editor a.ws_toggle_advanced_fields').text(captionShowAdvanced).show();
		} else {
			$('#ws_menu_editor div.ws_advanced').show();
			$('#ws_menu_editor a.ws_toggle_advanced_fields').text(captionHideAdvanced).hide();
		}
		
		$.post(
			adminAjaxUrl,
			{
				'action' : 'ws_ame_save_screen_options',
				'hide_advanced_settings' : hideAdvancedSettings?1:0,
				'_ajax_nonce' : hideAdvancedSettingsNonce
			}
		);
	});
	
	//Move our options into the screen meta panel
	$('#adv-settings').empty().append(screenOptions.show());
});