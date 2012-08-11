jQuery(document).ready(function($){
    wvp_initialize_tax_relationship_select();
    update_taxonomy_term_check();
    
    jQuery('input[name="_wpv_settings\\[taxonomy_type\\]\\[\\]"]').click(function () {
        update_taxonomy_term_check();
    });
});

function wvp_tax_relationship_change(selector) {
    var relationship = jQuery(selector).val();
    
    if (relationship == "FROM PAGE" ||
            relationship == "FROM ATTRIBUTE" ||
            relationship == "FROM URL" ||
            relationship == "FROM PARENT VIEW") {
        jQuery(selector).next().hide(); // hide category list
    } else {
        jQuery(selector).next().show(); // show category list
    }

    var parameter_div = jQuery(selector).next().next();
    if (relationship == "FROM ATTRIBUTE" ||
            relationship == "FROM URL") {
        parameter_div.show(); // Show parameters
        if (relationship == "FROM ATTRIBUTE") {
            parameter_div.children('.attribute').show();
            parameter_div.children('.url').hide();
        } else {
            parameter_div.children('.attribute').hide();
            parameter_div.children('.url').show();
        }
    } else {
        parameter_div.hide(); // Hide parameters
    }

    if (relationship == "FROM URL") {
        wpv_filter_url_hint(selector);
    }

}

function wvp_initialize_tax_relationship_select() {
    jQuery('.wpv_taxonomy_relationship').change(function() {
        wvp_tax_relationship_change(this);
    });

    jQuery('.wpv_taxonomy_relationship').each(function(index) {
        // trigger the change event to setup the help.
        wvp_tax_relationship_change(this);
    });
    
}


var taxonomy_rows = Array();

function wpv_show_filter_taxonomy_edit() {
    
    // record the taxonomy rows so we can undo changes on cancel.
    taxonomy_rows = Array();
    jQuery('.wpv_taxonomy_edit_row').each( function(index) {
        taxonomy_rows[jQuery(this).attr('id')] = jQuery(this).html();
    });
    
    jQuery('input[name="Add another filter term"]').hide();
    jQuery('.wpv_taxonomy_show_row').hide();
    jQuery('.wpv_taxonomy_edit_row').show();
    
    wpv_initialize_filter_select('popup_add_category_field');    
    
}

var wpv_filter_taxonomy_edit_is_ok;

function wpv_validate_taxonomy_data(in_popup) {
    wpv_filter_taxonomy_edit_is_ok = true;
    
    jQuery('.wpv_taxonomy_relationship:visible').each(function(index) {

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

        var relationship = jQuery(this).val();
        if (relationship == "FROM ATTRIBUTE" ||
                relationship == "FROM URL") {
            var param = jQuery(this).parent().find('.wpv_taxonomy_param').val();
            if (param == '') {
                jQuery(this).parent().find('.wpv_taxonomy_param_missing').show();
                wpv_filter_taxonomy_edit_is_ok = false;
            } else {
                jQuery(this).parent().find('.wpv_taxonomy_param_missing').hide();
            }
        }    
        
    });
    
    return wpv_filter_taxonomy_edit_is_ok;    
}

function wpv_show_filter_taxonomy_edit_ok() {
    // Make sure there's something set for the URL/VIEW parameter


    if (wpv_validate_taxonomy_data(false)) {
        wpv_add_edit_taxonomy('', '', 'edit');
        jQuery('.wpv_add_filters_button').show();
        
        wpv_add_filter_controls_for_url_params();
        
    } else {
        jQuery('#popup_add_category_field').parent().find('.wpv_taxonomy_param_missing_ok').show();
    }
}

function wpv_show_filter_taxonomy_edit_cancel() {
    // undo any changes by restoring the taxonomy rows
    for(var index in taxonomy_rows) {
        jQuery('#' + index).html(taxonomy_rows[index]);
        jQuery('#' + index).attr('class', 'wpv_taxonomy_edit_row');
    }
    
    jQuery('.wpv_add_filters_button').show()
    jQuery('.wpv_taxonomy_show_row').show();
    jQuery('.wpv_taxonomy_edit_row').hide();
    
    wpv_update_category_selectors();
}

function wpv_add_edit_taxonomy(div_id, type, mode) {
    String.prototype.startsWith = function (str){
        return this.indexOf(str) == 0;
    };
    String.prototype.endsWith = function (str){
        return this.slice(-str.length) == str;
    };

    if (mode == 'add' && !wpv_validate_taxonomy_data(true)) {
        return false;
    }

    // get existing taxonomy data
    var taxonomy_name = Array();
    var taxonomy_relationship = Array();
    var taxonomy_value = Array();
    var taxonomy_attribute_url = Array();
    var taxonomy_attribute_url_format = Array();
    
    jQuery('select').each( function(index) {
        if (mode == 'add' || jQuery(this).is(":visible")) {
            var name = jQuery(this).attr('name');
            if (name && name.startsWith('_wpv_settings[tax_') && name.endsWith('_relationship]')) {
                name = name.slice(18, -14);

                taxonomy_name.push(name);

                // Add the attribute or url filter.
                taxonomy_attribute_url.push(jQuery('input[name="_wpv_settings\\[taxonomy-' + name + '-attribute-url\\]"]').val());
                taxonomy_attribute_url_format.push(jQuery('input[name="_wpv_settings\\[taxonomy-' + name + '-attribute-url-format\\]\\[\\]"]:checked').val());

                if (name == 'category') {
                    name = 'post_category';
                } else {
                    name = 'tax_input_' + name;
                }
                taxonomy_relationship.push(jQuery(this).val());
                var current_taxonomy_value = '';
                jQuery('input[name="_wpv_settings\\[' + name + '\\]\\[\\]"]').each( function(index) {
                    if (jQuery(this).attr('checked')) {
                        if (current_taxonomy_value != '') {
                            current_taxonomy_value += ',';
                        }
                        current_taxonomy_value += jQuery(this).attr('value');
                    }
                });
                taxonomy_value.push(current_taxonomy_value);
            
            
            }
        }        
    });
    
    var uncheck = Array();
    
    if (type != '') {
        // get the new taxonomy data

        
        var type_temp = type.replace('[', '\\[');
        type_temp = type_temp.replace(']', '\\]');
        if (type_temp == 'post_category') {
            taxonomy_name.push('category');
        } else {
            taxonomy_name.push(type_temp.slice(11, -2));
        }
        var current_taxonomy_value = '';
        jQuery('#TB_ajaxContent input[name="' + type_temp + '\\[\\]"]').each( function(index) {
            if (jQuery(this).attr('checked')) {
                if (current_taxonomy_value != '') {
                    current_taxonomy_value += ',';
                }
                current_taxonomy_value += jQuery(this).attr('value');
                
                uncheck.push(this);
            }
        });
        taxonomy_value.push(current_taxonomy_value);
        taxonomy_relationship.push(jQuery('select[name="tax_' + taxonomy_name[0] + '_relationship"]').val());

        // Add the attribute or url filter.
        taxonomy_attribute_url.push(jQuery('input[name="tax_' + taxonomy_name[0] + '_attribute_url"]').val());
        taxonomy_attribute_url_format.push(jQuery('input[name="tax-' + taxonomy_name[0] + '-attribute-url-format"]:checked').val());

    }

    var temp_index = -1;
    jQuery('tr.wpv_filter_row').each( function(index) {
        var this_row = jQuery(this).attr('id');
        this_row = parseInt(this_row.substr(15));
        if (this_row > temp_index) {
            temp_index = this_row;
        }
    });
    
    
    // add the taxonomy relationship
    var taxonomys_relationship = 'OR';
    if(jQuery('select[name="_wpv_settings\\[taxonomy_relationship\\]"]').length) {
        taxonomys_relationship = jQuery('select[name="_wpv_settings\\[taxonomy_relationship\\]"]').val();
    }
    

    var data = {
        action : 'wpv_add_taxonomy',
        taxonomy_name : taxonomy_name,
        taxonomy_rows : taxonomy_rows,
        taxonomy_name : taxonomy_name,
        taxonomy_value : taxonomy_value,
        taxonomy_relationship : taxonomy_relationship,
        taxonomys_relationship : taxonomys_relationship,
        taxonomy_attribute_url : taxonomy_attribute_url,
        taxonomy_attribute_url_format : taxonomy_attribute_url_format,
        row : temp_index + 1,
        wpv_nonce : jQuery('#wpv_add_taxonomy_nonce').attr('value')
    };
    
    jQuery.ajaxSetup({async:false});
    jQuery.post(ajaxurl, data, function(response) {
        tb_remove();

        for (var i = 0; i < uncheck.length; i++) {
            // Uncheck it so that wordpress doesn't think we're saving a new taxonomy.
                
            jQuery(uncheck[i]).attr('checked', false);
        }

        jQuery('.wpv_taxonomy_edit_row').each( function(index) {
            jQuery(this).remove();
        });

        jQuery('.wpv_taxonomy_show_row').each( function(index) {
            jQuery(this).remove();
        });

        
        if (div_id == 'popup_add_category_field') {
            jQuery('#' + div_id).remove();
            jQuery('#' + div_id + '_controls').remove();
        }
        
        jQuery('#wpv_filter_table').append(response);

        if (mode == 'add') {
            // re-open the edit mode.
            wpv_show_filter_taxonomy_edit();
        }

        wpv_update_category_selectors();
        wvp_initialize_tax_relationship_select();
    });
}

function wpv_update_category_selectors() {

    wpv_update_categories_in_select('popup_add_filter_select');
    wpv_initialize_filter_select('popup_add_filter');
    wpv_update_categories_in_select('popup_add_category_field_select');
    wpv_initialize_filter_select('popup_add_category_field');
}

function wpv_update_categories_in_select(select_id) {
    
    // first set all the category in the select to be shown.
    var show_count = 0;
    jQuery('#' + select_id + ' option').each(function(index) {
        if (jQuery(this).val() == 'post_category' || jQuery(this).val().substr(0, 10) == 'tax_input[') {
            jQuery(this).show();
            show_count++;
        }
    });
    
    jQuery('.wpv_taxonomy_edit_row select').each(function(index) {
        var name = jQuery(this).attr('name');
        if(typeof name !== 'undefined' && name !== false && name != '_wpv_settings[taxonomy_relationship]' && name.slice(-14) == '_relationship]') {
            var tax_name = name.slice(18, -14);
            if (tax_name == 'category') {
                jQuery('#' + select_id + ' option[value=post_category]').hide();
            } else {
                jQuery('#' + select_id + ' option[value="tax_input\\[' + tax_name + '\\]"]').hide();
            }
            show_count--;
        }
    
    });

    if (select_id == 'popup_add_category_field_select') {
        if (show_count > 0) {    
            jQuery('input[name="Add another category"]').removeAttr("disabled");
        } else {
            jQuery('input[name="Add another category"]').attr("disabled", "disabled");
        }
    }
    
}

// Update the items in the taxonomy term checkboxes
// if the taxonomy type has changed.

function update_taxonomy_term_check() {
    
    var taxonomy = jQuery('input[name="_wpv_settings\\[taxonomy_type\\]\\[\\]"]:checked').val();
    var current = jQuery('#wpv-current-taxonomy-term').val();
    
    if (taxonomy != current) {
        // we need to update the parent select to list the required taxonomy

        var data = {
            action : 'wpv_get_taxonomy_term_check',
            taxonomy : taxonomy,
            wpv_nonce : jQuery('#wpv_get_taxonomy_term_check_nonce').attr('value')
        };
        
        jQuery('#wpv_update_taxonomy_term').show();
        jQuery.post(ajaxurl, data, function(response) {
            
            jQuery('.taxonomy-term-div').html(response);
            jQuery('#wpv_update_taxonomy_term').hide();
        });
    }
}

var taxonomy_terms_selected = Array();

function wpv_show_taxonomy_term_edit() {

    // record checked items just in case the operation is cancelled.    
    taxonomy_terms_selected = jQuery('input[name="_wpv_settings\\[taxonomy_terms\\]\\[\\]"]:checked');

    // Show the edit div for selecting terms
    jQuery('#wpv-filter-taxonomy-term-show').hide();
    jQuery('#wpv-filter-taxonomy-term-edit').show();
}

function wpv_show_taxonomy_term_edit_ok() {

    var taxonomy = jQuery('input[name="_wpv_settings\\[taxonomy_type\\]\\[\\]"]:checked').val();
 
    data = jQuery('#wpv-filter-taxonomy-term-edit :input').serialize();
    data += '&action=wpv_get_taxonomy_term_summary';
    data += '&taxonomy_type=' + taxonomy;
    
    jQuery.post(ajaxurl, data, function(response) {
        
        jQuery('#wpv-filter-taxonomy-term-show').html(response);
        jQuery('#wpv-filter-taxonomy-term-show').show();
        jQuery('#wpv-filter-taxonomy-term-edit').hide();
        
    });

	show_view_changed_message();

}

function wpv_show_taxonomy_term_edit_cancel() {
    // undo any changes.
    jQuery('input[name="_wpv_settings\\[taxonomy_terms\\]\\[\\]"]').each( function(index) {
        jQuery(this).attr('checked', false);
    });
    
    // check the items that were selected.
    taxonomy_terms_selected.each( function(index) {
        jQuery(this).attr('checked', true);
    });
    jQuery('#wpv-filter-taxonomy-term-show').show();
    jQuery('#wpv-filter-taxonomy-term-edit').hide();
}
