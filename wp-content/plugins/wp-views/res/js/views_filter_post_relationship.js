jQuery(document).ready(function($){
    wpv_register_add_filter_callback('wpv_post_relationship_add_filter');
    
    jQuery('#wpv_post_relationship_post_type_add').change(wpv_on_post_post_relationship_change_add);
    jQuery('#wpv_post_relationship_post_type').change(wpv_on_post_post_relationship_change);
    
    wpv_update_post_relationship_info();
    
    jQuery('input[name="_wpv_settings\\[post_type\\]\\[\\]"]').click(function () {
        wpv_update_post_relationship_info();
    });
    
});

/*
 * function: wpv_post_relationship_add_filter
 *
 * Add the post relationship settings to the ajax data when we add a filter
 *
 */

function wpv_post_relationship_add_filter(data) {
    
    // get post_relationship if set
    var post_relationship_mode = '';
    var post_relationship_id = '';
    if (jQuery('input[name=post_relationship_mode\\[\\]]').length) {
        post_relationship_mode = jQuery('input[name=post_relationship_mode\\[\\]]:checked').val();
        post_relationship_id = jQuery('select[name=wpv_post_relationship_id_add]').val();
    }
    
    if (post_relationship_mode != '') {
        data['post_relationship_mode'] = post_relationship_mode;
        data['post_relationship_id'] = post_relationship_id;
    }
    
    return data; 
}

var previous_post_relationship_mode;
var previous_post_relationship_id;
var previous_post_relationship_post_type;

/* Show the edit screen */

function wpv_show_filter_post_relationship_edit(post_relationship_div_pre, post_relationship_mode, post_relationship_id) {

    previous_post_relationship_mode = jQuery('input[name=_wpv_settings\\[' + post_relationship_mode + '\\]\\[\\]]:checked');
    previous_post_relationship_id = jQuery('select[name=_wpv_settings\\[' + post_relationship_id + '\\]]').val();
    previous_post_relationship_post_type = jQuery('#wpv_post_relationship_post_type').val();
    
    jQuery('#' + post_relationship_div_pre + '-edit').parent().parent().css('background-color', jQuery('#' + post_relationship_div_pre + '-edit').css('background-color'));

    jQuery('#' + post_relationship_div_pre + '-edit').show();
    jQuery('#' + post_relationship_div_pre + '-show').hide();
    
    jQuery(document).ready(function($){
        jQuery('#wpv_post_relationship_post_type').change(wpv_on_post_post_relationship_change);
        
    });
    
}

/* Save the edit results and get the summary */
                                               
function wpv_show_filter_post_relationship_edit_ok(post_relationship_div_pre, post_relationship_mode, post_relationship_id, post_relationship_type) {

    // find the filter row in the table.
    var tr = jQuery('#' + post_relationship_div_pre + '-show').parent().parent();
    var row = tr.attr('id').substr(15);
    
    var data = {
        action : 'wpv_get_table_row_ui',
        type_data : post_relationship_type,
        row : row,
        post_relationship_mode : jQuery('input[name=_wpv_settings\\[' + post_relationship_mode + '\\]\\[\\]]:checked').val(),
        post_relationship_type : jQuery('#wpv_post_relationship_post_type').val(),
        post_relationship_id : jQuery('select[name=_wpv_settings\\[' + post_relationship_id + '\\]]').val(),
        wpv_nonce : jQuery('#wpv_get_table_row_ui_nonce').attr('value')
    };
    
    var td = '';
    jQuery.post(ajaxurl, data, function(response) {
        td = response;
        jQuery('#wpv_filter_row_' + row).html(td);
        jQuery('#' + post_relationship_div_pre + '-edit').parent().parent().css('background-color', '');
        jQuery('#' + post_relationship_div_pre + '-edit').hide();
        jQuery('#' + post_relationship_div_pre + '-show').show();
        on_generate_wpv_filter();
        wpv_update_post_relationship_info();
    });

	show_view_changed_message();

}

/* Cancel the edit operation and set the values back to the way the were
*/

function wpv_show_filter_post_relationship_edit_cancel() {

    jQuery('input[name=_wpv_settings\\[post_relationship_mode\\]\\[\\]]').each( function(index) {
        jQuery(this).attr('checked', false); 
    });
    previous_post_relationship_mode.attr('checked', true);
    
    if (jQuery('#wpv_post_relationship_post_type').val() != previous_post_relationship_post_type) {
        var data = {
            action : 'wpv_get_posts_select',
            post_type : previous_post_type,
            wpv_nonce : jQuery('#wpv_get_posts_select_nonce').attr('value')
        };
        
        jQuery.ajaxSetup({async:false});
        jQuery.post(ajaxurl, data, function(response) {
            // change the id because it's set the the parent_id
            response = response.replace(/_wpv_settings\[parent_id\]/g, '_wpv_settings[post_relationship_id]');

            jQuery('select[name=_wpv_settings\\[post_relationship_id\\]]').remove();
            jQuery('#wpv_post_relationship_post_type').after(response);
            jQuery('#wpv_post_relationship_post_type').val(previous_post_relationship_post_type);
            jQuery('select[name=_wpv_settings\\[post_relationship_id\\]]').val(previous_post_relationship_id);
        });
    } else {
        jQuery('select[name=_wpv_settings\\[post_relationship_id\\]]').val(previous_post_relationship_id);
    }

    jQuery('#wpv-filter-post_relationship-edit').parent().parent().css('background-color', '');
    jQuery('#wpv-filter-post_relationship-edit').hide();
    jQuery('#wpv-filter-post_relationship-show').show();
}


/*
 * Fill the post list with the posts for the post type
 * This is on the Add Filter popup
 */

function wpv_on_post_post_relationship_change_add() {
    // Update the parents for the selected type.
    var data = {
        action : 'wpv_get_posts_select',
        post_type : jQuery('#wpv_post_relationship_post_type_add').val(),
        wpv_nonce : jQuery('#wpv_get_posts_select_nonce').attr('value')
    };
    
    jQuery('#wpv_update_post_relationship').show();
    jQuery.post(ajaxurl, data, function(response) {

        // change the id because it's set the the parent_id
        response = response.replace(/_wpv_settings\[parent_id\]/g, 'wpv_post_relationship_id_add');
        
        jQuery('#wpv_post_relationship_id_add').remove();
        jQuery('#wpv_post_relationship_post_type_add').after(response);
        jQuery('#wpv_update_post_relationship').hide();
    });
}

/*
 * Fill the post list with the posts for the post type
 */

function wpv_on_post_post_relationship_change() {
    // Update the parents for the selected type.
    var data = {
        action : 'wpv_get_posts_select',
        post_type : jQuery('#wpv_post_relationship_post_type').val(),
        wpv_nonce : jQuery('#wpv_get_posts_select_nonce').attr('value')
    };
    
    jQuery('#wpv_update_post_relationship').show();
    jQuery.post(ajaxurl, data, function(response) {

        // change the id because it's set the the parent_id
        response = response.replace(/_wpv_settings\[parent_id\]/g, '_wpv_settings[post_relationship_id]');
        
        jQuery('select[name=_wpv_settings\\[post_relationship_id\\]]').remove();
        jQuery('#wpv_post_relationship_post_type').after(response);
        jQuery('#wpv_update_post_relationship').hide();
    });
}

/**
 * Fills in the div in the post relationship filter with the related post types
 * to the post types selected in the post type filter
 *
 */
 
function wpv_update_post_relationship_info() {
    
    var selected = new Array;
    jQuery('input[name="_wpv_settings\\[post_type\\]\\[\\]"]:checked').each( function(index) {
        selected.push(jQuery(this).attr('value'));
    });

    var data = {
        action : 'wpv_get_post_relationship_info',
        post_types : selected,
        wpv_nonce : jQuery('#wpv_get_posts_select_nonce').attr('value')
    };

    jQuery.post(ajaxurl, data, function(response) {
        jQuery('#wpv-post-relationship-info').html(response);
    });
}
