var wpcfFormGroupsSupportPostTypeState = new Array();
var wpcfFormGroupsSupportTaxState = new Array();

jQuery(document).ready(function(){
    // Only for adding group
    jQuery('.wpcf-fields-add-ajax-link').click(function(){
        jQuery.ajax({
            url: jQuery(this).attr('href'),
            beforeSend: function() {
                jQuery('#wpcf-fields-under-title').hide();
                jQuery('#wpcf-ajax-response').addClass('wpcf-ajax-loading');
            },
            success: function(data) {
                jQuery('#wpcf-ajax-response').removeClass('wpcf-ajax-loading');
                jQuery('#wpcf-fields-sortable').append(data);
                jQuery('#wpcf-fields-sortable .ui-draggable:last').find('input:first').focus().select();
                var scrollToHeight = jQuery('#wpcf-fields-sortable .ui-draggable:last').offset();
                window.scrollTo(0, scrollToHeight.top);
            }
        });
        return false;
    });
    // Sort and Drag
    jQuery('.ui-sortable').sortable({
        revert: true,
        handle: 'img.wpcf-fields-form-move-field',
        containment: 'parent'
    });
    jQuery('.wpcf-fields-radio-sortable').sortable({
        revert: true,
        handle: 'img.wpcf-fields-form-radio-move-field',
        containment: 'parent'
    });
    jQuery('.wpcf-fields-select-sortable').sortable({
        revert: true,
        handle: 'img.wpcf-fields-form-select-move-field',
        containment: 'parent'
    });
    
    jQuery(".wpcf-form-fieldset legend").live('click', function() {
        jQuery(this).parent().children(".collapsible").slideToggle("fast", function() {
            var toggle = '';
            if (jQuery(this).is(":visible")) {
                jQuery(this).parent().children("legend").removeClass("legend-collapsed").addClass("legend-expanded");
                toggle = 'open';
            } else {
                jQuery(this).parent().children("legend").removeClass("legend-expanded").addClass("legend-collapsed");
                toggle = 'close';
            }
            // Save collapsed state
            // Get fieldset id
            var collapsed = jQuery(this).parent().attr('id');
            
            // For group form save fieldset toggle per group
            if (jQuery(this).parents('form').hasClass('wpcf-fields-form')) {
                // Get group id
                var group_id = false;
                if (jQuery('input:[name="group-id"]').length > 0) {
                    group_id = jQuery('input:[name="group-id"]').val();
                } else {
                    group_id = -1;
                }
                jQuery.ajax({
                    url: ajaxurl,
                    type: 'get',
                    data: 'action=wpcf_ajax&wpcf_action=group_form_collapsed&id='+collapsed+'&toggle='+toggle+'&group_id='+group_id+'&_wpnonce='+wpcf_nonce_toggle_group
                });
            } else {
                jQuery.ajax({
                    url: ajaxurl,
                    type: 'get',
                    data: 'action=wpcf_ajax&wpcf_action=form_fieldset_toggle&id='+collapsed+'&toggle='+toggle+'&_wpnonce'+wpcf_nonce_toggle_fieldset
                });
            }
        });
    });
    jQuery('.wpcf-forms-set-legend').live('keyup', function(){
        jQuery(this).parents('fieldset').find('.wpcf-legend-update').html(jQuery(this).val());
    });
    jQuery('.wpcf-form-groups-radio-update-title-display-value').live('keyup', function(){
        jQuery('#'+jQuery(this).attr('id')+'-display-value').prev('label').html(jQuery(this).val());
    });
    jQuery('.form-error').parents('.collapsed').slideDown();
    jQuery('.wpcf-form input').live('focus', function(){
        jQuery(this).parents('.collapsed').slideDown();
    });
    
    // Delete AJAX added element
    jQuery('.wpcf-form-fields-delete').live('click', function(){
        if (jQuery(this).attr('href') == 'javascript:void(0);') {
            jQuery(this).parent().fadeOut(function(){
                jQuery(this).remove();
            });
        }
    });
    
    // Check radio and select if same values
    jQuery('.wpcf-fields-form').submit(function(){
        var passed = true;
        var checkedArr = new Array();
        jQuery('.wpcf-compare-unique-value-wrapper').each(function(index){
            var childID = jQuery(this).attr('id');
            checkedArr[childID] = new Array();
            jQuery(this).find('.wpcf-compare-unique-value').each(function(index, value){
                var parentID = jQuery(this).parents('.wpcf-compare-unique-value-wrapper').first().attr('id');
                var currentValue = jQuery(this).val();
                if (jQuery.inArray(currentValue, checkedArr[parentID]) > -1) {
                    passed = false;
                    jQuery('#'+parentID).children('.wpcf-form-error-unique-value').remove();
                    jQuery('#'+parentID).append('<div class="wpcf-form-error-unique-value wpcf-form-error">'+wpcfFormUniqueValuesCheckText+'</div>');
                    jQuery(this).parents('fieldset').children('.fieldset-wrapper').slideDown();
                    jQuery(this).focus();
                    
                }
                checkedArr[parentID].push(currentValue);
            });
        });
        if (passed == false) {
            // Bind message fade out
            jQuery('.wpcf-compare-unique-value').live('keyup', function(){
                jQuery(this).parents('.wpcf-compare-unique-value-wrapper').find('.wpcf-form-error-unique-value').fadeOut(function(){
                    jQuery(this).remove();
                });
            });
            return false;
        }
    });
    
    /*
     * Generic AJAX call (link). Parameters can be used.
     */
    jQuery('.wpcf-ajax-link').live('click', function(){
        var callback = wpcfGetParameterByName('wpcf_ajax_callback', jQuery(this).attr('href'));
        var update = wpcfGetParameterByName('wpcf_ajax_update', jQuery(this).attr('href'));
        var updateAdd = wpcfGetParameterByName('wpcf_ajax_update_add', jQuery(this).attr('href'));
        var warning = wpcfGetParameterByName('wpcf_warning', jQuery(this).attr('href'));
        var thisObject = jQuery(this);
        if (warning != false) {
            var answer = confirm(warning);
            if (answer == false) {
                return false;
            }
        }
        jQuery.ajax({
            url: jQuery(this).attr('href'),
            type: 'get',
            dataType: 'json',
            //            data: ,
            cache: false,
            beforeSend: function() {
                if (update != false) {
                    jQuery('#'+update).html('').show().addClass('wpcf-ajax-loading-small');
                }
            },
            success: function(data) {
                if (data != null) {
                    if (typeof data.output != 'undefined') {
                        if (update != false) {
                            jQuery('#'+update).removeClass('wpcf-ajax-loading-small').html(data.output);
                        }
                        if (updateAdd != false) {
                            if (data.output.length < 1) {
                                jQuery('#'+updateAdd).fadeOut();
                            }
                            jQuery('#'+updateAdd).append(data.output);
                        }
                    }
                    if (typeof data.execute != 'undefined'
                        && (typeof data.wpcf_nonce_ajax_callback != 'undefined'
                        && data.wpcf_nonce_ajax_callback == wpcf_nonce_ajax_callback)) {
                            eval(data.execute);
                    }
                }
                if (callback != false) {
                    eval(callback+'(data, thisObject)');
                }
            }
        });
        return false;
    });

    jQuery('.wpcf-form-groups-support-post-type').each(function(){
        if (jQuery(this).is(':checked')) {
            window.wpcfFormGroupsSupportPostTypeState.push(jQuery(this).attr('id'));
        }
    });
    
    jQuery('.wpcf-form-groups-support-tax').each(function(){
        if (jQuery(this).is(':checked')) {
            window.wpcfFormGroupsSupportTaxState.push(jQuery(this).attr('id'));
        }
    });
    
    // Add scroll to user created fieldset if necessary
    if (jQuery('#wpcf-form-groups-user-fields').length > 0) {
        var wpcfFormGroupsUserCreatedFieldsHeight = Math.round(jQuery('#wpcf-form-groups-user-fields').height());
        var wpcfScreenHeight = Math.round(jQuery(window).height());
        var wpcfFormGroupsUserCreatedFieldsOffset = jQuery('#wpcf-form-groups-user-fields').offset();
        if (wpcfFormGroupsUserCreatedFieldsHeight+wpcfFormGroupsUserCreatedFieldsOffset.top > wpcfScreenHeight) {
            var wpcfFormGroupsUserCreatedFieldsHeightResize = Math.round(wpcfScreenHeight-wpcfFormGroupsUserCreatedFieldsOffset.top-40);
            jQuery('#wpcf-form-groups-user-fields').height(wpcfFormGroupsUserCreatedFieldsHeightResize);
            jQuery('#wpcf-form-groups-user-fields .fieldset-wrapper').height(wpcfFormGroupsUserCreatedFieldsHeightResize-15);
            jQuery('#wpcf-form-groups-user-fields .fieldset-wrapper').jScrollPane();
        }
        jQuery('.wpcf-form-fields-align-right').css('position', 'fixed');
    }
    
    // Types form
    jQuery('input:[name="ct[public]"]').change(function(){
        if (jQuery(this).val() == 'public') {
            jQuery('#wpcf-types-form-visiblity-toggle').slideDown();
        } else {
            jQuery('#wpcf-types-form-visiblity-toggle').slideUp();
        }
    });
    jQuery('input:[name="ct[rewrite][custom]"]').change(function(){
        if (jQuery(this).val() == 'custom') {
            jQuery('#wpcf-types-form-rewrite-toggle').slideDown();
        } else {
            jQuery('#wpcf-types-form-rewrite-toggle').slideUp();
        }
    });
    jQuery('.wpcf-tax-form input:[name="ct[rewrite][enabled]"]').change(function(){
        if (jQuery(this).is(':checked')) {
            jQuery('#wpcf-types-form-rewrite-toggle').slideDown();
        } else {
            jQuery('#wpcf-types-form-rewrite-toggle').slideUp();
        }
    });
    jQuery('input:[name="ct[show_in_menu]"]').change(function(){
        if (jQuery(this).is(':checked')) {
            jQuery('#wpcf-types-form-showinmenu-toggle').slideDown();
        } else {
            jQuery('#wpcf-types-form-showinmenu-toggle').slideUp();
        }
    });
    jQuery('input:[name="ct[query_var_enabled]"]').change(function(){
        if (jQuery(this).is(':checked')) {
            jQuery('#wpcf-types-form-queryvar-toggle').slideDown();
        } else {
            jQuery('#wpcf-types-form-queryvar-toggle').slideUp();
        }
    });
});

/**
 * Searches for parameter inside string ('arg', 'edit.php?arg=first&arg2=sec')
 */
function wpcfGetParameterByName(name, string){
    name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
    var regexS = "[\\?&]"+name+"=([^&#]*)";
    var regex = new RegExp( regexS );
    var results = regex.exec(string);
    if (results == null) {
        return false;
    } else {
        return decodeURIComponent(results[1].replace(/\+/g, " "));
    }
}

function wpcfRefresh() {
    window.location.reload();
}

/**
 * AJAX delete elements from group form callback.
 */
function wpcfFieldsFormDeleteElement(data, element) {
    element.parent().fadeOut(function(){
        element.parent().remove();
    });
}

/**
 * Set count for options
 */
function wpcfFieldsFormCountOptions(obj) {
    var count = wpcfGetParameterByName('count', obj.attr('href'));
    count++;
    obj.attr('href',  obj.attr('href').replace(/count=.*/, 'count='+count));
}