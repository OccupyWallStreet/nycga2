
var previous_search_text = '';
var previous_search_mode;


function wpv_show_filter_search_edit(search_div_pre, search_value, search_mode) {
    previous_search_text = jQuery('input[name="_wpv_settings\\[' + search_value + '\\]"]').val();
    previous_search_mode = jQuery('input[name=_wpv_settings\\[' + search_mode + '\\]\\[\\]]:checked'); 
    
    jQuery('#' + search_div_pre + '-edit').parent().parent().css('background-color', jQuery('#' + search_div_pre + '-edit').css('background-color'));

    jQuery('#' + search_div_pre + '-edit').show();
    jQuery('#' + search_div_pre + '-show').hide();
}

function wpv_show_filter_search_edit_ok(search_div_pre, search_value, search_mode, search_type) {

    // find the filter row in the table.
    var tr = jQuery('#' + search_div_pre + '-show').parent().parent();
    var row = tr.attr('id').substr(15);
    
    var data = {
        action : 'wpv_get_table_row_ui',
        type_data : search_type,
        row : row,
        search : jQuery('input[name="_wpv_settings\\[' + search_value + '\\]"]').val(),
        mode : jQuery('input[name=_wpv_settings\\[' + search_mode + '\\]\\[\\]]:checked').val(),
        wpv_nonce : jQuery('#wpv_get_table_row_ui_nonce').attr('value')
    };
    
    var td = '';
    jQuery.post(ajaxurl, data, function(response) {
        td = response;
        jQuery('#wpv_filter_row_' + row).html(td);
        jQuery('#' + search_div_pre + '-edit').parent().parent().css('background-color', '');
        jQuery('#' + search_div_pre + '-edit').hide();
        jQuery('#' + search_div_pre + '-show').show();
        on_generate_wpv_filter();
    });

	wpv_add_filter_controls_for_search();
	
	show_view_changed_message();

}

function wpv_show_filter_search_edit_cancel(search_div_pre, search_value, search_mode) {

    jQuery('input[name="_wpv_settings\\[' + search_value + '\\]"]').val(previous_search_text);
    
    jQuery('input[name=_wpv_settings\\[' + search_mode + '\\]\\[\\]]').each( function(index) {
        jQuery(this).attr('checked', false); 
    });
    previous_search_mode.attr('checked', true);

    
    jQuery('#' + search_div_pre + '-edit').parent().parent().css('background-color', '');
    jQuery('#' + search_div_pre + '-edit').hide();
    jQuery('#' + search_div_pre + '-show').show();
}

function wpv_search_box_code() {
    var query_type = jQuery('input[name="_wpv_settings\\[query_type\\]\\[\\]"]:checked').val();
    if (query_type == 'posts') {
        if (jQuery('input[name=_wpv_settings\\[search_mode\\]\\[\\]]:checked').val() == 'visitor') {
            return '[wpml-string context="wpv-views"]Search: [wpv-filter-search-box][/wpml-string]\n';
        }
    } else if (query_type == 'taxonomy') {
        if (jQuery('input[name=_wpv_settings\\[taxonomy_search_mode\\]\\[\\]]:checked').val() == 'visitor') {
            return '[wpml-string context="wpv-views"]Search: [wpv-filter-search-box][/wpml-string]\n';
        }
    }

    return '';
}


