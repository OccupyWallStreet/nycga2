
jQuery(document).ready(function(){
    window.wpcf_pr_edited = false;
    jQuery('#wpcf-post-relationship table').find(':input').live('click', function(){
        window.wpcf_pr_edited = true;
        jQuery(this).parent().addClass('wpcf-pr-edited');
    });
    jQuery('.wpcf-pr-has-apply').click(function(){
        jQuery(this).parent().slideUp().parent().parent().find('.wpcf-pr-edit').fadeIn();
        var txt = new Array();
        jQuery(this).parent().find('input:checked').each(function(){
            txt.push(jQuery(this).next().html());
        });
        if (txt.length < 1) {
            var wpcf_pr_has_update = wpcf_pr_has_empty_txt;
        } else {
            var txt_update = txt.join(', ');
            var wpcf_pr_has_update = wpcf_pr_has_txt.replace("%s", txt_update);
        }
        jQuery(this).parent().parent().parent().find('.wpcf-pr-has-summary').html(wpcf_pr_has_update);
    });
    jQuery('.wpcf-pr-belongs-apply').click(function(){
        jQuery(this).parent().slideUp().parent().parent().find('.wpcf-pr-edit').fadeIn();
        var txt = new Array();
        jQuery(this).parent().find('input:checked').each(function(){
            txt.push(jQuery(this).next().html());
        });
        if (txt.length < 1) {
            var wpcf_pr_belongs_update = wpcf_pr_belongs_empty_txt;
        } else {
            var txt_update = txt.join(', ');
            var wpcf_pr_belongs_update = wpcf_pr_belongs_txt.replace("%s", txt_update);
        }
        jQuery(this).parent().parent().parent().find('.wpcf-pr-belongs-summary').html(wpcf_pr_belongs_update);
    });
    jQuery('.wpcf-pr-has-cancel').click(function(){
        jQuery(this).parent().find('.checkbox').removeAttr('checked');
        for (var checkbox in window.wpcf_pr_has_snapshot) {
            jQuery('#'+window.wpcf_pr_has_snapshot[checkbox]).attr('checked', 'checked');
        }
        jQuery(this).parent().slideUp().parent().parent().find('.wpcf-pr-edit').fadeIn();
    });
    jQuery('.wpcf-pr-belongs-cancel').click(function(){
        jQuery(this).parent().find('.checkbox').removeAttr('checked');
        for (var checkbox in window.wpcf_pr_belongs_snapshot) {
            jQuery('#'+window.wpcf_pr_belongs_snapshot[checkbox]).attr('checked', 'checked');
        }
        jQuery(this).parent().slideUp().parent().parent().find('.wpcf-pr-edit').fadeIn();
    });
    jQuery('.wpcf-pr-edit').click(function(){
        window.wpcf_pr_has_snapshot = new Array();
        window.wpcf_pr_belongs_snapshot = new Array();
        var this_id = jQuery(this).attr('id');
        if (this_id == 'wpcf-pr-has-edit') {
            jQuery(this).next().find('.checkbox:checked').each(function(){
                window.wpcf_pr_has_snapshot.push(jQuery(this).attr('id'));
            });
        } else {
            jQuery(this).next().find('input:checked').each(function(){
                window.wpcf_pr_belongs_snapshot.push(jQuery(this).attr('id'));
            });
        }
        jQuery(this).fadeOut().next().slideDown();
    });
    jQuery('.wpcf-pr-ajax-link').live('click', function(){
        var object = jQuery(this);
        jQuery.ajax({
            url: jQuery(this).attr('href'),
            type: 'get',
            dataType: 'json',
            cache: false,
            beforeSend: function() {
                object.after('<div style="margin-top:20px;"></div>').next().addClass('wpcf-ajax-loading-small');
            },
            success: function(data) {
                if (data != null) {
                    if (typeof data.output != 'undefined') {
                        object.parent().find('tbody').prepend(data.output);
                    }
                }
                object.next().fadeOut(function(){
                    jQuery(this).remove();
                });
            }
        });
        return false;
    });
    jQuery('.wpcf-pr-delete-ajax').live('click', function(){
        var answer = confirm(wpcf_pr_del_warning);
        if (answer == false) {
            return false;
        }
        var object = jQuery(this);
        jQuery.ajax({
            url: jQuery(this).attr('href'),
            type: 'get',
            dataType: 'json',
            cache: false,
            beforeSend: function() {
                object.after('<div style="margin-top:20px;"></div>').next().addClass('wpcf-ajax-loading-small');
            },
            success: function(data) {
                if (data != null) {
                    if (typeof data.output != 'undefined') {
                        object.parent().parent().fadeOut(function(){
                            jQuery(this).remove();
                        });
                    }
                }
                object.next().fadeOut(function(){
                    jQuery(this).remove();
                });
            }
        });
        return false;
    });
    jQuery('.wpcf-pr-update-belongs').live('click', function(){
        var object = jQuery(this);
        jQuery.ajax({
            url: jQuery(this).attr('href'),
            type: 'post',
            dataType: 'json',
            data: jQuery(this).attr('href')+'&'+object.prev().serialize(),
            cache: false,
            beforeSend: function() {
                object.after('<div style="margin-top:20px;"></div>').next().addClass('wpcf-ajax-loading-small');
            },
            success: function(data) {
                object.next().fadeOut(2000, function(){
                    jQuery(this).remove();
                });
            }
        });
        return false;
    });
    jQuery('.wpcf-pr-pagination-link').live('click', function(){
        if (wpcfPrIsEdited()) {
            var answer = confirm(wpcf_pr_pagination_warning);
            if (answer == false) {
                return false;
            } else {
                window.wpcf_pr_edited = false;
            }
        }
        var object = jQuery(this);
        jQuery.ajax({
            url: jQuery(this).attr('href'),
            type: 'get',
            dataType: 'json',
            cache: false,
            beforeSend: function() {
                object.after('<div style="margin-top:20px;"></div>').next().addClass('wpcf-ajax-loading-small');
            },
            success: function(data) {
                if (data != null) {
                    if (typeof data.output != 'undefined') {
                        object.parent().html(data.output);
                    }
                }
                object.next().fadeOut(function(){
                    jQuery(this).remove();
                });
            }
        });
        return false;
    });
    jQuery('.wpcf-pr-pagination-select').live('change', function(){
        if (wpcfPrIsEdited()) {
            var answer = confirm(wpcf_pr_pagination_warning);
            if (answer == false) {
                return false;
            } else {
                window.wpcf_pr_edited = false;
            }
        }
        var object = jQuery(this);
        jQuery.ajax({
            url: jQuery(this).val(),
            type: 'get',
            dataType: 'json',
            cache: false,
            beforeSend: function() {
                object.after('<div style="margin-top:20px;"></div>').next().addClass('wpcf-ajax-loading-small');
            },
            success: function(data) {
                if (data != null) {
                    if (typeof data.output != 'undefined') {
                        object.parent().html(data.output);
                    }
                }
                object.next().fadeOut(function(){
                    jQuery(this).remove();
                });
            }
        });
        return false;
    });
    jQuery('.wpcf-sortable a').live('click', function(){
        if (wpcfPrIsEdited()) {
            var answer = confirm(wpcf_pr_pagination_warning);
            if (answer == false) {
                return false;
            } else {
                window.wpcf_pr_edited = false;
            }
        }
        var object = jQuery(this);
        jQuery.ajax({
            url: jQuery(this).attr('href'),
            type: 'get',
            dataType: 'json',
            cache: false,
            beforeSend: function() {
                object.after('<div style="margin-top:20px;"></div>').next().addClass('wpcf-ajax-loading-small');
            },
            success: function(data) {
                if (data != null) {
                    if (typeof data.output != 'undefined') {
                        object.parents('.wpcf-pr-pagination-update').html(data.output);
                    }
                }
                object.next().fadeOut(function(){
                    jQuery(this).remove();
                });
            }
        });
        return false;
    });
    jQuery('.wpcf-pr-save-ajax').live('click', function(){
        var object = jQuery(this);
        var valid = true;
        var form = object.parent().parent().find(':input');
        form.each(function(){
            if (jQuery('#post').validate().element(jQuery(this)) == false) {
                valid = false;
            }
        });
        if (valid == false) {
            return false;
        }
        object.parent().parent().find('.wpcf-pr-edited').removeClass('wpcf-pr-edited');
        var height = object.parent().parent().height();
        var rand = Math.round(Math.random()*10000);
        window.wpcf_pr_edited = false;
        jQuery.ajax({
            url: jQuery(this).attr('href'),
            type: 'post',
            dataType: 'json',
            data: jQuery(this).attr('href')+'&'+form.serialize(),
            cache: false,
            beforeSend: function() {
                object.parent().parent().after('<tr id="wpcf-pr-update-'+rand+'"><td style="height: '+height+'px;"><div style="margin-top:20px;" class="wpcf-ajax-loading-small"></div></td></tr>').remove();
            },
            success: function(data) {
                if (data != null) {
                    if (typeof data.output != 'undefined') {
                        jQuery('#wpcf-pr-update-'+rand+'').after(data.output).remove();
                    }
                }
            }
        });
        return false;
    });
    jQuery('.wpcf-pr-save-all-link').live('click', function(){
        var object = jQuery(this);
        if (object.attr('disabled') == 'disabled') {
            return false;
        }
        object.attr('disabled', 'disabled');
        var table = object.parent().find('table');
        var form = table.find(':input');
        var valid = true;
        form.each(function(){
            if (jQuery('#post').validate().element(jQuery(this)) == false) {
                valid = false;
            }
        });
        if (valid == false) {
            object.removeAttr('disabled');
            return false;
        }
        var rand = Math.round(Math.random()*10000);
        var height = table.find('tbody').height();
        window.wpcf_pr_edited = false;
        table.find('.wpcf-pr-edited').removeClass('wpcf-pr-edited');
        jQuery.ajax({
            url: jQuery(this).attr('href'),
            type: 'post',
            dataType: 'json',
            data: jQuery(this).attr('href')+'&'+object.parent().find(':input').serialize(),
            cache: false,
            beforeSend: function() {
                table.find('tbody').empty().prepend('<tr id="wpcf-pr-update-'+rand+'"><td style="height: '+height+'px;"><div style="margin-top:20px;" class="wpcf-ajax-loading-small"></div></td></tr>');
            },
            success: function(data) {
                if (data != null) {
                    if (typeof data.output != 'undefined') {
                        table.find('tbody').empty().prepend(data.output);
                        object.removeAttr('disabled');
                    }
                }
            }
        });
        return false;
    });
    
    // We need to hide the _wpcf_belongs_xxxx_id field for WPML.
    
    jQuery('#icl_mcs_details table tbody tr').each(function() {
        var name = jQuery(this).find('td').html();
        if (name.search(/^_wpcf_belongs_.*?_id/) != -1) {
            jQuery(this).hide();
        }
        
    });
});


function wpcfPrIsEdited() {
    if (jQuery('.wpcf-pr-edited').length < 1) {
        return false;
    }
    return true;
}

function wpcfPrUpdateIDs(ids) {
    var x;
    for (x in ids) {
        jQuery('#wpcf-post-relationship table td').find(':input[name^="wpcf_post_relationship['+x+']"]').each(function(){
            jQuery(this).attr('name', jQuery(this).attr('name').replace("["+x+"]", "["+ids[x]+"]"));
        });
    }
}