var wpv_filter_controls_rows;

function wpv_filter_controls_edit() {
    
    // record the controls rows so we can undo changes on cancel.
    wpv_filter_controls_rows = Array();
    jQuery('#view_filter_controls_table tbody').each( function(index) {
        wpv_filter_controls_rows.push(jQuery(this).html());
    });

    jQuery('#wpv_filter_controls_admin_edit').show();
    jQuery('#wpv_filter_controls_admin_summary').hide();

    wpv_set_input_type_select_size_same();
}

function wpv_filter_controls_edit_ok() {

    var c = jQuery('textarea#wpv_filter_meta_html_content').val();
    
    if (!check_if_previous_filter_has_changed(c)) {
		jQuery('#wpv_filter_control_meta_html_content_error').show();
		jQuery('#wpv_filter_meta_html_content_error').hide();
		
	} else {
		on_generate_wpv_filter();
		show_view_changed_message();
	}	

	jQuery('#wpv_filter_controls_admin_edit').hide();
	jQuery('#wpv_filter_controls_admin_summary').show();
		
}

function wpv_filter_controls_edit_cancel() {

    jQuery('#view_filter_controls_table tbody tr').each( function(index) {
        jQuery(this).remove();
    });
    
    for (var i = 0; i < wpv_filter_controls_rows.length; i++) {
        jQuery('#view_filter_controls_table tbody').append(wpv_filter_controls_rows[i]);
    }
    
    jQuery('#wpv_filter_controls_admin_edit').hide();
    jQuery('#wpv_filter_controls_admin_summary').show();

}

jQuery(document).ready(function($){

    var fixHelper = function(e, ui) {
        ui.children().each(function() {
            $(this).width($(this).width());
        });
        return ui;
    };
    
    // Sort and Drag
    jQuery('#view_filter_controls_table tbody').sortable({
        helper: fixHelper,
        revert: true,
        handle: 'img.move',
        containment: '#view_filter_controls_table',
        forceHelperSize: false,
        forcePlaceholderSize: false,
        tolerance: 'intersect',
        items: 'tr',
        update: function(event, ui){

        }
    });

    wpv_initialize_input_type_select_change();
    wpv_initialize_input_edit_click();
});

function wpv_filter_controls_code() {
    
    var controls = '';
    
    jQuery('#view_filter_controls_table tbody tr').each( function(index) {
        if (jQuery(this).find('input[name="_wpv_settings\\[filter_controls_enable\\]\\[\\]"]').attr('checked') == 'checked') {

            var mode = jQuery(this).find('input[name="_wpv_settings\\[filter_controls_mode\\]\\[\\]"]').val();
            var label = jQuery(this).find('input[name="_wpv_settings\\[filter_controls_label\\]\\[\\]"]').val();
            
            controls += '<p>';
            if (mode == 'submit') {

                controls += '[wpv-filter-submit name="' + label + '"]';
    
            } else {
                
    
                controls += label;
    
                controls += wpv_insert_filter_control(this);
                
            }
            controls += '</p>\n';
        }
    });
                                                                
	controls = '[wpv-filter-controls]' + controls + '[/wpv-filter-controls]\n';
    
    return controls;
}

function _wpv_get_filter_controls_values(selector) {
    var values = jQuery(selector).val();

    if (values != '') {
        values = jQuery.parseJSON(values);
        if (!('values' in values)) {
            values = {'values' : values};
        }
    } else {
        values = {'values' : Array()};
    }
    
    if (!('auto_fill' in values)) {
        values['auto_fill'] = "0";
    }

    if (!('auto_fill_default' in values)) {
        values['auto_fill_default'] = '';
    }
    
    return values;
    
}
function wpv_insert_filter_control(selector) {
    var mode = jQuery(selector).find('input[name="_wpv_settings\\[filter_controls_mode\\]\\[\\]"]').val();
	var control_style = jQuery(selector).find('select[name="_wpv_settings\\[filter_controls_type\\]\\[\\]"]').val();
	var field_name = jQuery(selector).find('input[name="_wpv_settings\\[filter_controls_field_name\\]\\[\\]"]').val();
    var url_param = jQuery(selector).find('input[name="_wpv_settings\\[filter_controls_param\\]\\[\\]"]').val();
    var values = _wpv_get_filter_controls_values(jQuery(selector).find('input[name="_wpv_settings\\[filter_controls_values\\]\\[\\]"]'));
    
    var control = '[wpv-control ';
    switch (mode) {
        case 'cf':
            if (control_style == 'types-auto' || field_name.indexOf('wpcf-') == 0) {
                control += 'field="' + field_name.slice(5) + '" ';
            }
            
            switch (control_style) {
                case 'types-auto':
                    
                    break;
                
                case 'textfield':
                    control += 'type="' + control_style + '" ';
                    break;
        
                case 'datepicker':
                    control += 'type="' + control_style + '" date_format="" ';
                    break;
        
                case 'checkbox':
                    control += 'type="' + control_style + '" ';
                    if (values['values'].length) {
                        control += 'title="' + values['values'][0][1] + '" ';
                    }
                    break;

                default:
                
                    control += 'type="' + control_style + '"';
                    
                    if (values['auto_fill'] == '1') {
                        control += ' auto_fill="' + field_name + '" ';
                        control += ' auto_fill_default="' + values['auto_fill_default'] + '" ';
                    } else {
                        control += ' values="';
                        
                        var first = true;
                        var data = '';
                        for (var i = 0; i < values['values'].length; i++) {
                            if (!first) {
                                data += ',';
                            }
                            data += values['values'][i][0];
                            first = false;
                        }
                        control += data;
                        
                        control += '" display_values="';
    
                        first = true;
                        data = '';
                        for (var i = 0; i < values['values'].length; i++) {
                            if (!first) {
                                data += ',';
                            }
                            data += values['values'][i][1];
                            first = false;
                        }
                        control += data;
                        
                        control += '" ';
                    }
                    break;
            }
            break;
        
        case 'tax':
        	control += 'taxonomy="' + field_name + '" ';
            control += 'type="' + control_style + '" ';
            break;
        
        case 'search':
            control += 'type="' + control_style + '" ';
            break;
        
    }
	
	control += 'url_param="' + url_param + '"]';
	
	return control;
}

function _wpv_show_filter_controls_ui() {
    
    // Always show the edit area if something has changed
    // ie. a URL_PARAM has been added.
    jQuery('#wpv_filter_controls_admin_edit').show();
    
    wpv_add_filter_controls_for_submit();
}

function wpv_add_filter_controls_for_url_params() {
    
    // check the custom field filters for URL_PARAMS.
    
	var filters = wpv_get_filters_with_url_params();
	
	var custom_fields = filters['custom_fields'];

	for (var key in custom_fields) {
		
		if (custom_fields.hasOwnProperty(key)) {
	
			for (var i = 0; i < custom_fields[key].length; i++) {
				var url_param = custom_fields[key][i];
				
				if (typeof url_param == 'string') {
	
					var found = false;            
					jQuery('#view_filter_controls_table tbody tr').each( function(index) {
						if (url_param == jQuery(this).find('input[name="_wpv_settings\\[filter_controls_param\\]\\[\\]"]').val()) {
							found = true;
						}
						
					});
					
					if (!found) {
						// we need to add a filter control.
						
						// copy the first (invisible) row and add to the end.
						var new_tr = wpv_create_new_filter_control_row();
		
						// Set the input values.                
						jQuery(new_tr).find('input[name="_wpv_settings\\[filter_controls_mode\\]\\[\\]"]').val('cf');
						jQuery(new_tr).find('input[name="_wpv_settings\\[filter_controls_param\\]\\[\\]"]').val(url_param);
						jQuery(new_tr).find('input[name="_wpv_settings\\[filter_controls_field_name\\]\\[\\]"]').val(key);
						jQuery(new_tr).find('input[name="_wpv_settings\\[filter_controls_field_name\\]\\[\\]"]').next().html(key + ' (' + url_param + ')');
						jQuery(new_tr).find('.button-secondary').hide();
					
						// Fetch the types field name.
						var data = '&action=wpv_get_types_field_name&wpv_nonce='+jQuery('#wpv_get_types_field_name_nonce').attr('value');
						data += '&field=' + key;
						
						jQuery.ajaxSetup({async:false});
						jQuery.post(ajaxurl, data, function(response) {
							response = jQuery.parseJSON(response);
							var new_tr = jQuery('#view_filter_controls_table tbody').find('tr:last-child');
							jQuery(new_tr).find('input[name="_wpv_settings\\[filter_controls_label\\]\\[\\]"]').val(response.name);
							
							if (!response.found) {
								jQuery(new_tr).find('select[name="_wpv_settings\\[filter_controls_type\\]\\[\\]"]').val('textfield');
							}
		
							
						});
					 
						_wpv_show_filter_controls_ui();
						on_generate_wpv_filter();
						wpv_initialize_input_type_select_change();
						wpv_initialize_input_edit_click();
		
					}
				}				
			}
		}
    }
    
	// TAXONOMY
	
	taxonomy = filters['taxonomy'];

	for (var key in taxonomy) {

		if (taxonomy.hasOwnProperty(key)) {

			var url_param = taxonomy[key];
			
			if (typeof url_param == 'string') {
				var found = false;            
				jQuery('#view_filter_controls_table tbody tr').each( function(index) {
					if (url_param == jQuery(this).find('input[name="_wpv_settings\\[filter_controls_param\\]\\[\\]"]').val()) {
						found = true;
					}
					
				});
				
				if (!found) {
					// we need to add a filter control.
					
					// copy the first (invisible) row and add to the end.
					var new_tr = wpv_create_new_filter_control_row();
		
					// Set the input values.                
					jQuery(new_tr).find('input[name="_wpv_settings\\[filter_controls_mode\\]\\[\\]"]').val('tax');
					jQuery(new_tr).find('input[name="_wpv_settings\\[filter_controls_param\\]\\[\\]"]').val(url_param);
					jQuery(new_tr).find('input[name="_wpv_settings\\[filter_controls_field_name\\]\\[\\]"]').val(key);
					jQuery(new_tr).find('input[name="_wpv_settings\\[filter_controls_field_name\\]\\[\\]"]').next().html(key + ' (' + url_param + ')');
					jQuery(new_tr).find('.button-secondary').hide();
				
					// Fetch the types field name.
					var data = '&action=wpv_get_taxonomy_name&wpv_nonce='+jQuery('#wpv_get_types_field_name_nonce').attr('value');
					data += '&taxonomy=' + key;
					
					jQuery.ajaxSetup({async:false});
					jQuery.post(ajaxurl, data, function(response) {
						response = jQuery.parseJSON(response);
						var new_tr = jQuery('#view_filter_controls_table tbody').find('tr:last-child');
						jQuery(new_tr).find('input[name="_wpv_settings\\[filter_controls_label\\]\\[\\]"]').val(response.name);
						
						// Taxonomy only allows for select and checkboxes style.
						var select = jQuery(new_tr).find('select[name="_wpv_settings\\[filter_controls_type\\]\\[\\]"]');
						select.val('select');
						select.find('option').each(function (index) {
							if (jQuery(this).val() != 'checkboxes' && jQuery(this).val() != 'select') {
								jQuery(this).remove();
							}
							
						});
		
						
					});
					_wpv_show_filter_controls_ui();
					on_generate_wpv_filter();
					wpv_initialize_input_type_select_change();
					wpv_initialize_input_edit_click();
				}
			}
		}
    }
    
    // see if there are any filter controls that no longer have URL_PARAM filters.
    
    jQuery('#view_filter_controls_table tbody tr').each( function(index) {
        var url_param = jQuery(this).find('input[name="_wpv_settings\\[filter_controls_param\\]\\[\\]"]').val();
        if (url_param != '' && url_param != 'wpv_submit_button' && url_param != 'wpv_post_search') {
            found = false;
            
            for (var key in custom_fields) {
            
                for (var i = 0; i < custom_fields[key].length; i++) {
                    if (url_param == custom_fields[key][i]) {
                        found = true;
                        break;
                    }
                }
            }
                    
            for (var key in taxonomy) {
        
                if (url_param == taxonomy[key]) {
                    found = true;
                }
            }
            
            var message_div = jQuery(this).find('.wpv_url_param_deleted');
            if (!found) {
                jQuery(this).find('input[name="_wpv_settings\\[filter_controls_enable\\]\\[\\]"]').attr('checked', false);
                
                if (!message_div.length) {
                    var remove_button = wpv_create_control_value_remove_button();
                    jQuery(this).find('input[name="_wpv_settings\\[filter_controls_field_name\\]\\[\\]"]').parent().append('<div class="wpv_url_param_deleted"><strong>' + wpv_url_param_deleted_message + '</strong>' + remove_button + '</div>');
                }
                
                
                _wpv_show_filter_controls_ui();
                on_generate_wpv_filter();
            } else {
                
                if (message_div.length) {
                    message_div.remove();
                }
            }
        }
    });
        
    
    
    
}

function wpv_create_control_value_remove_button() {
	return '<input type="button" class="button-secondary wpv-remove-value" value="' + wpv_remove + '" onclick="jQuery(this).parent().parent().parent().remove();" />';
}

function wpv_add_filter_controls_for_search() {
    
    if (jQuery('input[name="_wpv_settings\\[search_mode\\]\\[\\]"]:checked').val() == 'visitor') {

        // Search for an existing control
        var url_param = 'wpv_post_search';
        var found = false;            
        jQuery('#view_filter_controls_table tbody tr').each( function(index) {
            if (url_param == jQuery(this).find('input[name="_wpv_settings\\[filter_controls_param\\]\\[\\]"]').val()) {
                found = true;
            }
        });
        
        if (!found) {
            // copy the first (invisible) row and add to the end.
            var new_tr = wpv_create_new_filter_control_row();
    
            // Set the input values.                
            jQuery(new_tr).find('input[name="_wpv_settings\\[filter_controls_mode\\]\\[\\]"]').val('search');
            jQuery(new_tr).find('input[name="_wpv_settings\\[filter_controls_param\\]\\[\\]"]').val(url_param);
            jQuery(new_tr).find('input[name="_wpv_settings\\[filter_controls_field_name\\]\\[\\]"]').val(wpv_search_text);
            jQuery(new_tr).find('input[name="_wpv_settings\\[filter_controls_field_name\\]\\[\\]"]').next().html(wpv_search_text + ' (' + url_param + ')');
            jQuery(new_tr).find('input[name="_wpv_settings\\[filter_controls_label\\]\\[\\]"]').val(wpv_search_text);
            jQuery(new_tr).find('.button-secondary').hide();
    
            // Search only allows for textfield style.
            var select = jQuery(new_tr).find('select[name="_wpv_settings\\[filter_controls_type\\]\\[\\]"]');
            select.val('textfield');
            select.find('option').each(function (index) {
                if (jQuery(this).val() != 'textfield') {
                    jQuery(this).remove();
                }
                
            });
            
            _wpv_show_filter_controls_ui();
            on_generate_wpv_filter();
        }
    }    
}

function wpv_create_new_filter_control_row() {
    
    var tr = jQuery('#view_filter_controls_table tbody').find('tr:first-child').html();
    jQuery('#view_filter_controls_table tbody').append('<tr>' + tr + '</tr>');

    wpv_initialize_input_type_select_change();
    
    return jQuery('#view_filter_controls_table tbody').find('tr:last-child');
}

function wpv_add_filter_controls_for_submit() {
    
    if (jQuery('#view_filter_controls_table tbody tr').length > 1) {

        // Search for an existing control
        var url_param = 'wpv_submit_button';
        var found = false;            
        jQuery('#view_filter_controls_table tbody tr').each( function(index) {
            if (url_param == jQuery(this).find('input[name="_wpv_settings\\[filter_controls_param\\]\\[\\]"]').val()) {
                found = true;
            }
        });
        
        if (!found) {
            // copy the first (invisible) row and add to the end.
            var new_tr = wpv_create_new_filter_control_row();
    
            // Set the input values.                
            jQuery(new_tr).find('input[name="_wpv_settings\\[filter_controls_mode\\]\\[\\]"]').val('submit');
            jQuery(new_tr).find('input[name="_wpv_settings\\[filter_controls_param\\]\\[\\]"]').val(url_param);
            jQuery(new_tr).find('input[name="_wpv_settings\\[filter_controls_field_name\\]\\[\\]"]').val(url_param);
            jQuery(new_tr).find('input[name="_wpv_settings\\[filter_controls_field_name\\]\\[\\]"]').next().html(wpv_submit_button_text);
            jQuery(new_tr).find('input[name="_wpv_settings\\[filter_controls_label\\]\\[\\]"]').val(wpv_submit_text);
            jQuery(new_tr).find('.button-secondary').hide();
    
            // Search only allows for submit-button style.
            var select = jQuery(new_tr).find('select[name="_wpv_settings\\[filter_controls_type\\]\\[\\]"]');
            select.find('option').each(function (index) {
                if (jQuery(this).val() != 'textfield') {
                    jQuery(this).remove();
                } else {
                    jQuery(this).val('submit-button');
                    jQuery(this).html(wpv_submit_button_text);
                }
                
            });
            
            _wpv_show_filter_controls_ui();
            on_generate_wpv_filter();
        }
    }    
}


function wpv_set_input_type_select_size_same() {
    var max_width = 0;
    
    jQuery('select[name="_wpv_settings\\[filter_controls_type\\]\\[\\]"]').each (function(index) {
        var width = jQuery(this).width();
        if (width > max_width) {
            max_width = width;
        }
    });
    
    jQuery('select[name="_wpv_settings\\[filter_controls_type\\]\\[\\]"]').each (function(index) {
        jQuery(this).width(max_width + 10);
    });
    
}

function wpv_initialize_input_type_select_change() {
    jQuery('select[name="_wpv_settings\\[filter_controls_type\\]\\[\\]"]').change (function() {


	    var mode = jQuery(this).parent().parent().find('input[name="_wpv_settings\\[filter_controls_mode\\]\\[\\]"]').val();
		
		if (mode == 'tax') {
			// Don't show the Edit for input values.
			jQuery(this).parent().parent().find('.button-secondary').hide();
		} else {
			
			var type = jQuery(this).val();
			
			switch(type) {
				case 'checkbox':
				case 'checkboxes':
				case 'radios':
				case 'select':
					jQuery(this).parent().parent().find('.button-secondary').show();
					break;
	
				default:
					jQuery(this).parent().parent().find('.button-secondary').hide();
					break;
					
			}
		}
    });
    
}

function wpv_initialize_input_edit_click() {
    jQuery('#view_filter_controls_table .button-secondary').click( function () {
        jQuery(this).hide();
        var td = jQuery(this).parent();
        var type = td.parent().find('select[name="_wpv_settings\\[filter_controls_type\\]\\[\\]"]').val();
        var values = _wpv_get_filter_controls_values(td.parent().find('input[name="_wpv_settings\\[filter_controls_values\\]\\[\\]"]'));

        jQuery(td).append('<div id="wpv_filter_controls_edit" style="background:' + wpv_edit_background + ';"></div>');
        
        var div = jQuery('#wpv_filter_controls_edit');
        
        if (type == 'checkbox') {
            var title = '';
            if (values['values'].length) {
                title = values[0][1]
            }
            jQuery(div).append(wpv_title + ' : ');
            jQuery(div).append('<input class="view_field_control_vals" type="text" value="' + title + '" />');

            jQuery(div).append('<hr />');
            jQuery(div).append('<input class="button-primary wpv-values-ok" type="button" value="' + wpv_ok + '" />');

            jQuery(div).find('.wpv-values-ok').click( function () {

                var title = jQuery(this).parent().find('.view_field_control_vals').val();
                var control_values = Array();
                control_values.push(Array(title,title));
                control_values = JSON.stringify({'values' : control_values});
                
                jQuery(this).parent().parent().parent().find('input[name="_wpv_settings\\[filter_controls_values\\]\\[\\]"]').val(control_values);
                jQuery(this).parent().parent().find('.button-secondary').show();
                jQuery(this).parent().remove();
                
                // enable controls
                jQuery('#wpv_filter_controls_admin_edit').find('input,select').each( function(index) {
                    jQuery(this).attr('disabled', false);
                });
            });
                
        } else {

            var checked = values['auto_fill'] == "1" ? ' checked="checked"' : '';
            jQuery(div).append('<label><input type="radio" value="1" name="wpv_auto_fill" class="wpv_auto_fill"' + checked + ' />' + wpv_auto_fill_on + '</label>');
            jQuery(div).append('<br />');
            jQuery(div).append('<div style="margin-left:20px;">' + wpv_auto_fill_default + ' <input type="text" class="wpv_auto_fill_default" value="' + values['auto_fill_default'] + '" /></div>');
            checked = !(values['auto_fill'] == "1") ? ' checked="checked"' : '';
            jQuery(div).append('<label><input type="radio" value="0" name="wpv_auto_fill" class="wpv_auto_fill"' + checked + ' />' + wpv_auto_fill_off + '</label>');
            jQuery(div).append('<br />');
            jQuery(div).append('<hr />');
            
            if (values['values'].length == 0) {
                jQuery(div).append('<p>' + wpv_no_values + '</p>');
            } else {
                div.append('<table><tr><td>' + wpv_values + '</td><td>' + wpv_display_values + '</td><td></td></tr></table>');
                var table = div.find('table');

                var remove_button = wpv_create_control_value_remove_button();
                
                for(var i = 0; i < values['values'].length; i++) {
                    var value = values['values'][i];
                    table.append('<tr><td><input class="view_field_control_vals" type="text" value="' + value[0] + '" /></td><td><input class="view_field_control_vals" type="text" value="' + value[1] + '" /></td><td>' + remove_button + '</td>');
                }
                
            }
            
            jQuery(div).append('<input class="button-secondary add-another-value" type="button" value="' + wpv_add_another_value + '" />');
            jQuery(div).find('.add-another-value').click( function () {wpv_add_another_input_value(jQuery(this)) });
            
            jQuery(div).append('<hr />');
            jQuery(div).append('<input class="button-primary wpv-values-ok" type="button" value="' + wpv_ok + '" />');

            jQuery(div).find('.wpv-values-ok').click( function () {
                var table = jQuery(this).parent().find('table');

                var values = Array();                
                if (table) {
                    values = wpv_filter_controls_get_values_from_table(table);
                }

                var auto_fill = jQuery(this).parent().find('.wpv_auto_fill:checked').val();
                var auto_fill_default = jQuery(this).parent().find('.wpv_auto_fill_default').val();
                var control_values = JSON.stringify({'values' : values,
                                                    'auto_fill' : auto_fill,
                                                    'auto_fill_default' : auto_fill_default});
            
                jQuery(this).parent().parent().parent().find('input[name="_wpv_settings\\[filter_controls_values\\]\\[\\]"]').val(control_values);
                jQuery(this).parent().parent().find('.button-secondary').show();
                jQuery(this).parent().remove();

                // enable controls
                jQuery('#wpv_filter_controls_admin_edit').find('input,select').each( function(index) {
                    jQuery(this).attr('disabled', false);
                });
                    
            });
            
            
        }

        jQuery(div).append('<input class="button-secondary wpv-values-cancel" type="button" value="' + wpv_cancel + '" />');

        jQuery(div).find('.wpv-values-cancel').click( function () {
            jQuery(this).parent().parent().find('.button-secondary').show();
            jQuery(this).parent().remove();

            // enable controls
            jQuery('#wpv_filter_controls_admin_edit').find('input,select').each( function(index) {
                jQuery(this).attr('disabled', false);
            });
                
        });
        
        // disable all the other edit controls.
        
        jQuery('#wpv_filter_controls_admin_edit').find('input,select').each( function(index) {
            if (!jQuery(this).hasClass('view_field_control_vals') &&
                    !jQuery(this).hasClass('wpv-remove-value') &&
                    !jQuery(this).hasClass('wpv-values-ok') &&
                    !jQuery(this).hasClass('wpv-values-cancel') &&
                    !jQuery(this).hasClass('wpv_auto_fill') &&
                    !jQuery(this).hasClass('wpv_auto_fill_default') &&
                    !jQuery(this).hasClass('add-another-value')) {
                
                jQuery(this).attr('disabled', true); 
            }
           
            
        });
        
        
    });
}

function wpv_add_another_input_value(button) {
    var div = button.parent();
    
    if(button.prev().html() == wpv_no_values) {
        button.prev().remove();
        jQuery('<table><tr><td>' + wpv_values + '</td><td>' + wpv_display_values + '</td><td></td></tr></table>').insertBefore(button);
    }
    
    var table = div.find('table');
	var remove_button = wpv_create_control_value_remove_button();

    table.append('<tr><td><input class="view_field_control_vals" type="text" /></td><td><input class="view_field_control_vals" type="text" /></td><td>' + remove_button + '</td>');
    
}

function wpv_filter_controls_get_values_from_table(table) {
    var values = Array();
    
    jQuery(table).find('tr').each( function(index) {
        var value = Array();
        jQuery(this).find('input:text').each( function(index_2) {
            value.push(jQuery(this).val());
        });
        
        if (value.length) {
            values.push(value);
        }
    });
    
    return values;
    
}

function wpv_filter_control_update_manual() {
	// don't do anything, just close the warning box.
    jQuery('#wpv_filter_meta_html_content_error').hide();
    jQuery('#wpv_filter_control_meta_html_content_error').hide();
}

function wpv_filter_control_update_elements() {
	// update just the [wpv-control]

    var body = jQuery('textarea#wpv_filter_meta_html_content').val();
	
    jQuery('#view_filter_controls_table tbody tr').each( function(index) {
        if (jQuery(this).find('input[name="_wpv_settings\\[filter_controls_enable\\]\\[\\]"]').attr('checked') == 'checked') {

            var mode = jQuery(this).find('input[name="_wpv_settings\\[filter_controls_mode\\]\\[\\]"]').val();
 
			var field_name = jQuery(this).find('input[name="_wpv_settings\\[filter_controls_field_name\\]\\[\\]"]').val();
			var url_param = jQuery(this).find('input[name="_wpv_settings\\[filter_controls_param\\]\\[\\]"]').val();
            var label = jQuery(this).find('input[name="_wpv_settings\\[filter_controls_label\\]\\[\\]"]').val();
            
			var control = '';
            if (mode == 'submit') {

                control += '[wpv-filter-submit name="' + label + '"]';

				var regex = new RegExp('\\[wpv-filter-submit.*?name=".*?".*?\\]');
				var match = regex.exec(body);
				if (match) {
					body = body.replace(match, control);
				}
    
            } else {
    
                control += wpv_insert_filter_control(this);

				if (field_name.indexOf('wpcf-') == 0) {
					field_name = field_name.slice(5);
				}
				var regex = new RegExp('\\[wpv-control.*?field="' + field_name.replace('-', '\\-') + '".*?url_param="' + url_param.replace('-', '\\-') + '".*?\\]');
				var match = regex.exec(body);
				if (match) {
					body = body.replace(match, control);
				}
                
            }

        }
    });
	
    jQuery('textarea#wpv_filter_meta_html_content').val(body);
	
    jQuery('#wpv_filter_meta_html_content_error').hide();
    jQuery('#wpv_filter_control_meta_html_content_error').hide();
	
}

function wpv_filter_control_apply_changes() {
	var mode = jQuery('input[name="wpv_filter_control_update"]:checked').val();
	switch(mode) {
		case 'update':
			wpv_filter_control_update_elements();
			break;
		
		case 'manual':
			wpv_filter_control_update_manual();
			break;
		
		default:
			wpv_filter_meta_html_generate_new();
			break;
	}
}