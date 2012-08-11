
jQuery(document).ready(function(){
    jQuery('.wpcf-access-remove-user').click(function(){
        jQuery(this).parent().fadeOut(function(){
            jQuery(this).remove();
        });
    });
    jQuery('.wpcf-access-edit-type').click(function(){
        jQuery(this).hide().parent().find('.wpcf-access-mode').slideToggle();
    });
    jQuery('.wpcf-access-edit-type-done').click(function(){
        jQuery(this).parents('.wpcf-access-mode').slideToggle().parent().find('.wpcf-access-edit-type').show();
    });
    jQuery('.wpcf-access-switch-mode').change(function(){
        if (jQuery(this).val() == 'custom') {
            jQuery(this).parent().parent().find('.wpcf-access-mode-custom').show();
            jQuery(this).parent().parent().find('.wpcf-access-mode-predefined').hide();
            jQuery(this).parent().parent().find('.wpcf-access-mode-not_managed').hide();
        } else if (jQuery(this).val() == 'predefined') {
            jQuery(this).parent().parent().find('.wpcf-access-mode-custom').hide();
            jQuery(this).parent().parent().find('.wpcf-access-mode-predefined').show();
            jQuery(this).parent().parent().find('.wpcf-access-mode-not_managed').hide();
        } else {
            jQuery(this).parent().parent().find('.wpcf-access-mode-custom').hide();
            jQuery(this).parent().parent().find('.wpcf-access-mode-predefined').hide();
            jQuery(this).parent().parent().find('.wpcf-access-mode-not_managed').show();
        }
    });
    jQuery('select[name^="wpcf_access_bulk_set"]').change(function(){
        var value = jQuery(this).val();
        if (value != '0') {
            jQuery(this).parent().find('select').each(function(){
                jQuery(this).val(value);
            });
        }
    });
    jQuery('.wpcf-access-change-level').live('click', function(){
        jQuery(this).hide().parent().find('.wpcf-access-custom-roles-select-wrapper').slideDown();
    });
    jQuery('.wpcf-access-change-level-cancel').live('click', function(){
        jQuery(this).parent().slideUp().parent().find('.wpcf-access-change-level').show();
    });
    jQuery('.wpcf-access-change-level-apply').live('click', function(){
        wpcfAccessApplyLevels(jQuery(this));
    });
});

function wpcfAccessReset(object) {
    jQuery.ajax({
        url: object.attr('href')+'&button_id='+object.attr('id'),
        type: 'get',
        dataType: 'json',
        //            data: ,
        cache: false,
        beforeSend: function() {},
        success: function(data) {
            if (data != null) {
                if (typeof data.output != 'undefined' && typeof data.button_id != 'undefined') {
                    var parent = jQuery('#'+data.button_id).parent();
                    jQuery.each(data.output, function(index, value) { 
                        parent.find('select[name*="['+index+']"]').val(value);
                    });
                }
            }
        }
    });
    return false;
}

function wpcfAccessApplyLevels(object) {
    jQuery.ajax({
        url: ajaxurl,
        type: 'post',
        dataType: 'json',
        data: object.parent().find('.wpcf-access-custom-roles-select').serialize()+'&_wpnonce='+wpcf_nonce_ajax_callback+'&action=wpcf_access_ajax_set_level',
        cache: false,
        beforeSend: function() {
            jQuery('#wpcf-access-custom-roles-table-wrapper').html('').addClass('wpcf-ajax-loading');
        },
        success: function(data) {
            if (data != null) {
                if (typeof data.output != 'undefined') {
                    jQuery('#wpcf-access-custom-roles-wrapper').replaceWith(data.output);
                }
            }
        }
    });
    return false;
}