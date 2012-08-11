
var custom_field_rows = Array();

function wpv_show_filter_custom_field_edit() {
    
    // record the custom field rows so we can undo changes on cancel.
    custom_field_rows = Array();
    jQuery('.wpv_custom_field_edit_row').each( function(index) {
        custom_field_rows[jQuery(this).attr('id')] = jQuery(this).html();
    });
    
    jQuery('input[name="Add another filter term"]').hide();
    
    jQuery('.wpv_custom_field_show_row').hide();
    jQuery('.wpv_custom_field_edit_row').show();
    
    wpv_initialize_filter_select('popup_add_custom_field');    
}

function wpv_validate_custom_field_data(in_popup) {
    // Make sure we have values in the fields.
    
    wpv_filter_edit_is_ok = true;
    
    jQuery('.wpv_custom_filter_value_text:visible').each(function(index) {
        
        if (in_popup) {
            // make sure we only check ones in the popup.
            var found = false;
            var parents = jQuery(this).parents();
            for (var i=0; i < parents.length; i++) {
                var id = jQuery(parents[i]).attr('id');
                if (id == 'popup_add_custom_field_controls' || id == 'popup_add_filter_controls') {
                    found = true;
                    break;
                }
            }
            
            if (!found) {
                return;
            }
        }
        if (jQuery(this).is(":visible")) {
        
            var relationship = jQuery(this).parent().find('.wpv_custom_field_compare_mode').val();
            if (relationship != 'constant') {
                var param = jQuery(this).val();
                if (param == '') {
                    jQuery(this).parent().find('.wpv_custom_field_param_missing').show();
                    wpv_filter_edit_is_ok = false;
                } else {
                    jQuery(this).parent().find('.wpv_custom_field_param_missing').hide();
                }
            }
        }
        
    });
    
    // check for valid dates.
    
    jQuery('.wpv-custom-field-date').each(function(index) {

        if (jQuery(this).is(":visible")) {
       
            var month = jQuery(this).children(':first');
            var mm = month.val();
            var jj = month.next().val();
            var aa = month.next().next().val();
            var newD = new Date( aa, mm - 1, jj);
    
            if ( newD.getFullYear() != aa || (1 + newD.getMonth()) != mm || newD.getDate() != jj) {
                jQuery(this).parent().find('.wpv_custom_field_invalid_date').show();
                wpv_filter_edit_is_ok = false;
            } else {
                jQuery(this).parent().find('.wpv_custom_field_invalid_date').hide();
            }
            
        }
    });
    
    return wpv_filter_edit_is_ok;    
}
function wpv_show_filter_custom_field_edit_ok() {

    if (wpv_validate_custom_field_data(false)) {
    
        _wpv_resolve_custom_field_value();
        
        wpv_add_edit_custom_field('', '', 'edit');
        jQuery('.wpv_add_filters_button').show();
        
        show_view_changed_message();
        
        wpv_add_filter_controls_for_url_params();
        
    } else {
        jQuery('#popup_add_custom_field').parent().find('.wpv_custom_field_param_missing_ok').show();
    }
}

function wpv_show_filter_custom_field_edit_cancel() {
    // undo any changes by restoring the custom field rows
    for(var index in custom_field_rows) {
        jQuery('#' + index).html(custom_field_rows[index]);
        jQuery('#' + index).attr('class', 'wpv_custom_field_edit_row');
    }
    
    jQuery('.wpv_add_filters_button').show();
    jQuery('.wpv_custom_field_show_row').show();
    jQuery('.wpv_custom_field_edit_row').hide();
}

function wpv_add_edit_custom_field(div_id, type, mode) {
    String.prototype.startsWith = function (str){
        return this.indexOf(str) == 0;
    };
    String.prototype.endsWith = function (str){
        return this.slice(-str.length) == str;
    };


    if (mode == 'add' && !wpv_validate_custom_field_data(true)) {
        return false;
    }
    
    // get existing custom field data
    var custom_fields_name = Array();
    var custom_fields_compare = Array();
    var custom_fields_type = Array();
    var custom_fields_value = Array();
    jQuery('select').each( function(index) {
        if (mode == 'add' || jQuery(this).is(":visible")) {
            
            _wpv_resolve_custom_field_value();
            
            var name = jQuery(this).attr('name');
            if (name && name.startsWith('_wpv_settings[custom-field-') && name.endsWith('_compare]')) {
                custom_fields_name.push(name.slice(27, -9));
                name = name.slice(0, -8);
                name = name.replace('[', '\\[');
                custom_fields_compare.push(jQuery('select[name="' + name + 'compare\\]"]').val());
                custom_fields_type.push(jQuery('select[name="' + name + 'type\\]"]').val());
                custom_fields_value.push(jQuery('input[name="' + name + 'value\\]"]').val());
            }
        }        
    });

    if (type != '') {
        // get the new custom field data
        
        var type_temp = type.replace('[', '\\[');
        type_temp = type_temp.replace(']', '\\]');
        if (jQuery('#TB_ajaxContent select[name=' + type_temp + '_compare]').length) {
            custom_fields_name.push(type_temp.slice(13));
            custom_fields_compare.push(jQuery('#TB_ajaxContent select[name=' + type_temp + '_compare]').val());
            custom_fields_type.push(jQuery('#TB_ajaxContent select[name=' + type_temp + '_type]').val());
            custom_fields_value.push(jQuery('#TB_ajaxContent input[name=' + type_temp + '_value]').val());
        }
    }
    
    var temp_index = -1;
    jQuery('tr.wpv_filter_row').each( function(index) {
        var this_row = jQuery(this).attr('id');
        this_row = parseInt(this_row.substr(15));
        if (this_row > temp_index) {
            temp_index = this_row;
        }
    });
    
    
    // add the custom field relationship
    var custom_fields_relationship = 'OR';
    if(jQuery('select[name="_wpv_settings\\[custom_fields_relationship\\]"]').length) {
        custom_fields_relationship = jQuery('select[name="_wpv_settings\\[custom_fields_relationship\\]"]').val();
    }
    
    var data = {
        action : 'wpv_add_custom_field',
        custom_fields_name : custom_fields_name,
        custom_field_rows : custom_field_rows,
        custom_fields_compare : custom_fields_compare,
        custom_fields_type : custom_fields_type,
        custom_fields_value : custom_fields_value,
        custom_fields_relationship : custom_fields_relationship,
        row : temp_index + 1,
        wpv_nonce : jQuery('#wpv_add_custom_field_nonce').attr('value')
    };
    
    jQuery.ajaxSetup({async:false});
    jQuery.post(ajaxurl, data, function(response) {

        tb_remove();
        
        jQuery('.wpv_custom_field_edit_row').each( function(index) {
            jQuery(this).remove(); 
        });
        jQuery('.wpv_custom_field_show_row').each( function(index) {
            jQuery(this).remove(); 
        });
        
        
        if (div_id == 'popup_add_custom_field') {
            jQuery('#' + div_id).remove();
            jQuery('#' + div_id + '_controls').remove();
        }
        
        jQuery('#wpv_filter_table').append(response);
        
        if (mode == 'add') {
            // re-open the edit mode.
            wpv_show_filter_custom_field_edit();
        }
        
        wpv_update_custom_fields_in_select('popup_add_filter_select');
        wpv_initialize_filter_select('popup_add_filter');    
        wpv_update_custom_fields_in_select('popup_add_custom_field_select');
        wpv_initialize_filter_select('popup_add_custom_field');
        wpv_initialize_compare_mode_change();
        wpv_initialize_compare_change();
        wpv_initialize_add_another_value_click();
        wpv_initialize_remove_value_click();
        wpv_initialize_show_date();
    });
    
    return true;
}

function wpv_update_custom_fields_in_select(select_id) {
    
    // first set all the custom fields in the select to be shown.
    jQuery('#' + select_id + ' option').each(function(index) {
        if (jQuery(this).val().substr(0, 13) == 'custom-field-') {
            jQuery(this).show();
        }
    });
    
    jQuery('.wpv_custom_field_edit_row select').each(function(index) {
        if(jQuery(this).attr('name') && jQuery(this).attr('name').slice(-6) == '_type]') {
            var field_name = jQuery(this).attr('name').slice(27, -6);
            jQuery('#' + select_id + ' option[value=custom-field-' + field_name + ']').hide();
        }
    });
}

var _wpv_resolve_custom_field_value_output;

function _wpv_resolve_custom_field_value() {
    // Calculate the actual value to be saved using the
    // settings from the mode and text boxes.
    
    jQuery('.wpv_custom_field_values:visible').each(function(index) {
        var text_box = jQuery(this).find('input:first-child');
        _wpv_resolve_custom_field_value_output = '';
        jQuery(this).find('.wpv_custom_field_value_div').each(function(index) {
            var text_control = jQuery(this).find('.wpv_custom_filter_value_text')
            if (_wpv_resolve_custom_field_value_output != '') {
                _wpv_resolve_custom_field_value_output += ',';
            }
            var value = text_control.val();
            
            var mode = jQuery(this).children('.wpv_custom_field_compare_mode').val();
            switch(mode) {
                case 'url':
                    value = 'URL_PARAM(' + value + ')';
                    break;
                    
                case 'attribute':
                    value = 'VIEW_PARAM(' + value + ')';
                    break;
                
                case 'now':
                    value = 'NOW()';
                    break;
                
                case 'today':
                    value = 'TODAY()';
                    break;
                
                case 'future_day':
                    value = 'FUTURE_DAY(' + value + ')';
                    break;
                
                case 'past_day':
                    value = 'PAST_DAY(' + value + ')';
                    break;
                
                case 'this_month':
                    value = 'THIS_MONTH()';
                    break;
                
                case 'future_month':
                    value = 'FUTURE_MONTH(' + value + ')';
                    break;
                
                case 'past_month':
                    value = 'PAST_MONTH(' + value + ')';
                    break;
                
                case 'this_year':
                    value = 'THIS_YEAR()';
                    break;
                
                case 'future_year':
                    value = 'FUTURE_YEAR(' + value + ')';
                    break;
                
                case 'past_year':
                    value = 'PAST_YEAR(' + value + ')';
                    break;
                
                case 'seconds_from_now':
                    value = 'SECONDS_FROM_NOW(' + value + ')';
                    break;
                    
                case 'months_from_now':
                    value = 'MONTHS_FROM_NOW(' + value + ')';
                    break;
                    
                case 'years_from_now':
                    value = 'YEARS_FROM_NOW(' + value + ')';
                    break;
                
                case 'date':
                    var date_div = jQuery(this).find('.wpv-custom-field-date');
                    var month = jQuery(date_div).find('select');
                    
                    var mm = month.val();
                    var jj = month.next().val();
                    var aa = month.next().next().val();
                    
                    value = 'DATE(' + jj + ',' + mm + ',' + aa + ')';
                    break;
                    
            }
            
            _wpv_resolve_custom_field_value_output += value;
        })

        
        text_box.val(_wpv_resolve_custom_field_value_output);
    });
}

jQuery(document).ready(function($){
    wpv_initialize_compare_mode_change();
    wpv_initialize_compare_change();
    wpv_initialize_add_another_value_click();
    wpv_initialize_remove_value_click();
    wpv_initialize_show_date();
});



function wpv_initialize_compare_mode_change() {
    jQuery('.wpv_custom_field_compare_mode').change(function(event) {
        wpv_show_hide_date_controls(this);
    });
}

function wpv_show_hide_date_controls(item) {
    // Show the text control depending on the compare function.
    var mode = jQuery(item).val();
    switch(mode) {
        case 'constant':
        case 'url':
        case 'attribute':
        case 'future_day':
        case 'past_day':
        case 'future_month':
        case 'past_month':
        case 'future_year':
        case 'past_year':
        case 'seconds_from_now':
        case 'months_from_now':
        case 'years_from_now':
            jQuery(item).parent().find('.wpv_custom_filter_value_text').show();
            jQuery(item).parent().find('.wpv-custom-field-date').hide();
            break;
        
        case 'date':
            jQuery(item).parent().find('.wpv_custom_filter_value_text').hide();
            jQuery(item).parent().find('.wpv-custom-field-date').show();
            break;

        default:
            jQuery(item).parent().find('.wpv_custom_filter_value_text').hide();
            jQuery(item).parent().find('.wpv-custom-field-date').hide();
            break;
            
    }
    
    if (mode == 'url') {
        wpv_filter_url_hint(item);
    }
}

function wpv_initialize_show_date() {
    jQuery('.wpv_custom_field_compare_mode').each(function(index) {
    
        wpv_show_hide_date_controls(this);
    });
}


var wpv_initialize_compare_change_count;
var wpv_initialize_compare_change_mode;

function wpv_initialize_compare_change() {
    jQuery('.wpv_custom_field_compare_select').change(function(event) {
        wpv_initialize_compare_change_mode = jQuery(this).val();
        
        switch(wpv_initialize_compare_change_mode) {
            case 'BETWEEN':
            case 'NOT BETWEEN':
                wpv_initialize_compare_change_count = 2;
                jQuery(this).parent().find('.wpv_custom_field_add_value').hide();
                
                divs = jQuery(this).parent().find('.wpv_custom_field_value_div');
                if (divs.length < 2) {
                    // add another one.

                    var clone = jQuery(divs[0]).clone();
                    
                    clone.find('.wpv_custom_filter_value_text').val('');
                    
                    clone.insertAfter(divs[0]);
                    
                    wpv_initialize_compare_mode_change();
                    wpv_initialize_remove_value_click();

                }
                break;
            
            case 'IN':
            case 'NOT IN':
                wpv_initialize_compare_change_count = 100000; // A big number
                jQuery(this).parent().find('.wpv_custom_field_add_value').show();
                break;
            
            default:
                wpv_initialize_compare_change_count = 1;
                jQuery(this).parent().find('.wpv_custom_field_add_value').hide();
                break;
        }
        
        jQuery(this).parent().find('.wpv_custom_field_value_div').each(function(index) {
            
            if (index > 0 && (wpv_initialize_compare_change_mode == 'IN' || wpv_initialize_compare_change_mode == 'NOT IN')) {
                jQuery(this).find('.wpv_custom_field_remove_value').show();
            } else {
                jQuery(this).find('.wpv_custom_field_remove_value').hide();
            }
            
            if (wpv_initialize_compare_change_count > 0) {
                jQuery(this).show();
            } else {
                jQuery(this).hide();
            }
            wpv_initialize_compare_change_count--;
        });
        
        
    });
}

function wpv_initialize_add_another_value_click() {
    
    jQuery('.wpv_custom_field_add_value').click(function () {
        
        var clone = jQuery(this).prev().clone();
        
        clone.find('.wpv_custom_filter_value_text').val('');
        clone.find('.wpv_custom_field_remove_value').show();
        
        clone.insertBefore(jQuery(this));

        wpv_initialize_compare_mode_change();
        wpv_initialize_remove_value_click();

        wpv_show_hide_date_controls(jQuery(this).prev().find('.wpv_custom_field_compare_mode'));
    });
}

function wpv_initialize_remove_value_click() {

    jQuery('.wpv_custom_field_remove_value').click(function () {
        jQuery(this).parent().remove();
    });
    
}

