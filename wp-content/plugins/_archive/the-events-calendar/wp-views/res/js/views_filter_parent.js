var previous_parent_mode;
var previous_parent_id;
var previous_post_type;

function wpv_show_filter_parent_edit(parent_div_pre, parent_mode, parent_id) {

    previous_parent_mode = jQuery('input[name=_wpv_settings\\[' + parent_mode + '\\]\\[\\]]:checked');
    previous_parent_id = jQuery('select[name=_wpv_settings\\[' + parent_id + '\\]]').val();
    previous_post_type = jQuery('#wpv_parent_post_type').val();
    
    jQuery('#' + parent_div_pre + '-edit').parent().parent().css('background-color', jQuery('#' + parent_div_pre + '-edit').css('background-color'));

    jQuery('#' + parent_div_pre + '-edit').show();
    jQuery('#' + parent_div_pre + '-show').hide();
    
    jQuery(document).ready(function($){
        jQuery('#wpv_parent_post_type').change(wpv_on_post_parent_change);
        
    });
    
}

                                               
function wpv_on_post_parent_change() {
     // Update the parents for the selected type.
     var data = {
         action : 'wpv_get_posts_select',
         post_type : jQuery('#wpv_parent_post_type').val(),
         wpv_nonce : jQuery('#wpv_get_posts_select_nonce').attr('value')
     };
     
     jQuery('#wpv_update_parent').show();
     jQuery.post(ajaxurl, data, function(response) {
         jQuery('select[name=_wpv_settings\\[parent_id\\]]').remove();
         jQuery('#wpv_parent_post_type').after(response);
         jQuery('#wpv_update_parent').hide();
     });
}

function wpv_on_post_parent_change_add() {
     // Update the parents for the selected type.
     // This is for in the popup.
     var data = {
         action : 'wpv_get_posts_select',
         post_type : jQuery('#wpv_parent_post_type_add').val(),
         wpv_nonce : jQuery('#wpv_get_posts_select_nonce').attr('value')
     };
     
     jQuery('#wpv_update_parent').show();
     jQuery.post(ajaxurl, data, function(response) {
         jQuery('select[name=wpv_parent_id_add]').remove();
         response = response.replace('_wpv_settings[parent_id]', 'wpv_parent_id_add');
         jQuery('#wpv_parent_post_type_add').after(response);
         jQuery('#wpv_update_parent').hide();
     });
}


function wpv_show_filter_parent_edit_ok(parent_div_pre, parent_mode, parent_id, parent_type) {

    // find the filter row in the table.
    var tr = jQuery('#' + parent_div_pre + '-show').parent().parent();
    var row = tr.attr('id').substr(15);
    
    var data = {
        action : 'wpv_get_table_row_ui',
        type_data : parent_type,
        row : row,
        parent_mode : jQuery('input[name=_wpv_settings\\[' + parent_mode + '\\]\\[\\]]:checked').val(),
        parent_id : jQuery('select[name=_wpv_settings\\[' + parent_id + '\\]]').val(),
        wpv_nonce : jQuery('#wpv_get_table_row_ui_nonce').attr('value')
    };
    
    if (parent_mode == 'taxonomy_parent_mode') {
        data['taxonomy'] = jQuery('input[name=_wpv_settings\\[taxonomy_type\\]\\[\\]]:checked').val();
    }
    
    var td = '';
    jQuery.post(ajaxurl, data, function(response) {
        td = response;
        jQuery('#wpv_filter_row_' + row).html(td);
        jQuery('#' + parent_div_pre + '-edit').parent().parent().css('background-color', '');
        jQuery('#' + parent_div_pre + '-edit').hide();
        jQuery('#' + parent_div_pre + '-show').show();
        on_generate_wpv_filter();
    });
    
	show_view_changed_message();
    

}

function wpv_show_filter_parent_edit_cancel() {

    jQuery('input[name=_wpv_settings\\[parent_mode\\]\\[\\]]').each( function(index) {
        jQuery(this).attr('checked', false); 
    });
    previous_parent_mode.attr('checked', true);
    
    if (jQuery('#wpv_parent_post_type').val() != previous_post_type) {
        var data = {
            action : 'wpv_get_posts_select',
            post_type : previous_post_type,
            wpv_nonce : jQuery('#wpv_get_posts_select_nonce').attr('value')
        };
        
        jQuery.ajaxSetup({async:false});
        jQuery.post(ajaxurl, data, function(response) {
            jQuery('select[name=_wpv_settings\\[parent_id\\]]').remove();
            jQuery('#wpv_parent_post_type').after(response);
            jQuery('#wpv_parent_post_type').val(previous_post_type);
            jQuery('select[name=_wpv_settings\\[parent_id\\]]').val(previous_parent_id);
        });
    } else {
        jQuery('select[name=_wpv_settings\\[parent_id\\]]').val(previous_parent_id);
    }

    jQuery('#wpv-filter-parent-edit').parent().parent().css('background-color', '');
    jQuery('#wpv-filter-parent-edit').hide();
    jQuery('#wpv-filter-parent-show').show();
}

function wpv_show_filter_taxonomy_parent_edit_cancel() {

    jQuery('input[name=_wpv_settings\\[taxonomy_parent_mode\\]\\[\\]]').each( function(index) {
        jQuery(this).attr('checked', false); 
    });
    previous_parent_mode.attr('checked', true);
    
    jQuery('select[name=_wpv_settings\\[taxonomy_parent_id\\]]').val(previous_parent_id);

    jQuery('#wpv-filter-taxonomy-parent-edit').parent().parent().css('background-color', '');
    jQuery('#wpv-filter-taxonomy-parent-edit').hide();
    jQuery('#wpv-filter-taxonomy-parent-show').show();
}


jQuery(document).ready(function($){
    jQuery('#wpv_parent_post_type_add').change(wpv_on_post_parent_change_add);

    jQuery('#popup_add_filter_taxonomy_select').change(update_taxonomy_parents_select);
    
});
    
// Update the items in the taxonomy parents select box
// if the taxonomy type has changed.

function update_taxonomy_parents_select() {
    
    var taxonomy = jQuery('input[name="_wpv_settings\\[taxonomy_type\\]\\[\\]"]:checked').val();
    var current = jQuery('#wpv-current-taxonomy-parent').val();
    
    if (taxonomy != current) {
        // we need to update the parent select to list the required taxonomy

        var data = {
            action : 'wpv_get_taxonomy_parents_select',
            taxonomy : taxonomy,
            wpv_nonce : jQuery('#wpv_get_taxonomy_select_nonce').attr('value')
        };
        
        jQuery('#wpv_update_taxonomy_parent').show();
        jQuery.post(ajaxurl, data, function(response) {
            jQuery('select[name=wpv_taxonomy_parent_id]').remove();
            jQuery('#wpv-current-taxonomy-parent').after(response);
            jQuery('#wpv_update_taxonomy_parent').hide();
            jQuery('#wpv-current-taxonomy-parent').val(taxonomy);
        });
    }
}    