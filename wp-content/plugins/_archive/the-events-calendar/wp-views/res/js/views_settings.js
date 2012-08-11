
var wpv_show_hidden_custom_fields_state = '';

function wpv_show_hidden_custom_fields_edit() {
    wpv_show_hidden_custom_fields_state = jQuery('#wpv_show_hidden_custom_fields_admin').html();

    jQuery('#wpv_show_hidden_custom_fields_admin').show();
    jQuery('#wpv_show_hidden_custom_fields_summary').hide();
}

function wpv_show_hidden_custom_fields_save() {
    jQuery('#wpv_show_custom_fields_spinner').show();
    
    data = jQuery('#wpv_show_hidden_custom_fields_admin :input').serialize();
    data += '&action=wpv_get_show_hidden_custom_fields';
    
    jQuery.post(ajaxurl, data, function(response) {
    
        jQuery('#wpv_show_hidden_custom_fields').html(response);
        
        jQuery('#wpv_show_custom_fields_spinner').hide();
        jQuery('#wpv_show_hidden_custom_fields_summary').show();
        jQuery('#wpv_show_hidden_custom_fields_admin').hide();
    });
    
}

function wpv_show_hidden_custom_fields_cancel() {
    jQuery('#wpv_show_hidden_custom_fields_admin').html(wpv_show_hidden_custom_fields_state);

    jQuery('#wpv_show_hidden_custom_fields_summary').show();
    jQuery('#wpv_show_hidden_custom_fields_admin').hide();
}

