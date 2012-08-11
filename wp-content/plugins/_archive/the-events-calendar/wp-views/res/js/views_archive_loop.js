/////////////////////////////////////////////////////////
// post archive loop functions
/////////////////////////////////////////////////////////

var archive_post_types_loop_selected = Array();

function wpv_archive_post_type_loop_edit() {

    // save the select box states.    
    archive_post_types_loop_selected = Array();
    jQuery('#wpv-post-type-loop-edit select').each(function(index) {
        archive_post_types_loop_selected[jQuery(this).attr('name')] = jQuery(this).val();
    });

    jQuery('#wpv-post-type-loop-edit').show();
    jQuery('#wpv-post-type-loop-summary').hide();
}

function wpv_archive_post_type_loop_save() {
    jQuery('#wpv_save_post_type_loop_spinner').show();
    
    data = jQuery('#wpv-post-type-loop-edit :input').serialize();
    data += '&action=wpv_get_archive_post_type_summary';
    
    jQuery.post(ajaxurl, data, function(response) {
    
        jQuery('#wpv-post-type-loop-summary').replaceWith(response);
        
        jQuery('#wpv_save_post_type_loop_spinner').hide();
        jQuery('#wpv-post-type-loop-edit').hide();
        jQuery('#wpv-post-type-loop-summary').show();
    });
}

function wpv_archive_post_type_loop_cancel() {

    // revert the select box states.    
    for (item in archive_post_types_loop_selected) {
        jQuery('select[name="' + item + '"]').val(archive_post_types_loop_selected[item]);
    }
    
    jQuery('#wpv-post-type-loop-edit').hide();
    jQuery('#wpv-post-type-loop-summary').show();
}

/////////////////////////////////////////////////////
// taxonomy archive loop functions
/////////////////////////////////////////////////////

var archive_taxonomy_loop_selected = Array();

function wpv_archive_taxonomy_loop_edit() {
    // save the select box states.    
    archive_taxonomy_loop_selected = Array();
    jQuery('#wpv-taxonomy-loop-edit select').each(function(index) {
        archive_taxonomy_loop_selected[jQuery(this).attr('name')] = jQuery(this).val();
    });

    jQuery('#wpv-taxonomy-loop-edit').show();
    jQuery('#wpv-taxonomy-loop-summary').hide();
}

function wpv_archive_taxonomy_loop_save() {
    jQuery('#wpv_save_taxonomy_loop_spinner').show();
    
    data = jQuery('#wpv-taxonomy-loop-edit :input').serialize();
    data += '&action=wpv_get_archive_taxonomy_summary';
    
    jQuery.post(ajaxurl, data, function(response) {
    
        jQuery('#wpv-taxonomy-loop-summary').replaceWith(response);
        
        jQuery('#wpv_save_taxonomy_loop_spinner').hide();
        jQuery('#wpv-taxonomy-loop-edit').hide();
        jQuery('#wpv-taxonomy-loop-summary').show();
    });
}

function wpv_archive_taxonomy_loop_cancel() {

    // revert the select box states.    
    for (item in archive_taxonomy_loop_selected) {
        jQuery('select[name="' + item + '"]').val(archive_taxonomy_loop_selected[item]);
    }

    jQuery('#wpv-taxonomy-loop-edit').hide();
    jQuery('#wpv-taxonomy-loop-summary').show();
}

/////////////////////////////////////////////////////
// taxonomy archive loop functions for View Templates
/////////////////////////////////////////////////////

var archive_view_template_taxonomy_loop_selected = Array();

function wpv_view_template_taxonomy_loop_edit() {
    // save the select box states.    
    archive_view_template_taxonomy_loop_selected = Array();
    jQuery('#wpv-view-template-taxonomy-edit select').each(function(index) {
        archive_view_template_taxonomy_loop_selected[jQuery(this).attr('name')] = jQuery(this).val();
    });
    jQuery('#wpv-view-template-taxonomy-summary').hide();
    jQuery('#wpv-view-template-taxonomy-edit').show();
}

function wpv_view_template_taxonomy_loop_save() {
    jQuery('#wpv_save_view_template_taxonomy_loop_spinner').show();
    
    data = jQuery('#wpv-view-template-taxonomy-edit :input').serialize();
    data += '&action=wpv_get_archive_view_template_taxonomy_summary';
    
    jQuery.post(ajaxurl, data, function(response) {
    
        jQuery('#wpv-view-template-taxonomy-summary').replaceWith(response);
        
        jQuery('#wpv_save_view_template_taxonomy_loop_spinner').hide();
        jQuery('#wpv-view-template-taxonomy-summary').show();
        jQuery('#wpv-view-template-taxonomy-edit').hide();
    });
}

function wpv_view_template_taxonomy_loop_cancel() {
    // revert the select box states.    
    for (item in archive_view_template_taxonomy_loop_selected) {
        jQuery('select[name="' + item + '"]').val(archive_view_template_taxonomy_loop_selected[item]);
    }

    jQuery('#wpv-view-template-taxonomy-summary').show();
    jQuery('#wpv-view-template-taxonomy-edit').hide();
}

//////////////////////////////////////////////////////
// post type archive loop functions for View Templates
//////////////////////////////////////////////////////

var archive_view_template_post_type_loop_selected = Array();

function wpv_view_template_post_type_loop_edit() {
    // save the select box states.    
    archive_view_template_post_type_loop_selected = Array();
    jQuery('#wpv-view-template-post-type-edit select').each(function(index) {
        archive_view_template_post_type_loop_selected[jQuery(this).attr('name')] = jQuery(this).val();
    });
    jQuery('#wpv-view-template-post-type-summary').hide();
    jQuery('#wpv-view-template-post-type-edit').show();
}

function wpv_view_template_post_type_loop_save() {
    jQuery('#wpv_save_view_template_post_type_loop_spinner').show();
    
    data = jQuery('#wpv-view-template-post-type-edit :input').serialize();
    data += '&action=wpv_get_archive_view_template_post_type_edit';
    
    jQuery.post(ajaxurl, data, function(edit_response) {
    
        data = jQuery('#wpv-view-template-post-type-edit :input').serialize();
        data += '&action=wpv_get_archive_view_template_post_type_summary';
        jQuery.post(ajaxurl, data, function(summary_response) {
            
            jQuery('#wpv-view-template-post-type-summary').replaceWith(summary_response);
     
            jQuery('#wpv-view-template-post-type-edit').replaceWith(edit_response);
            
            jQuery('#wpv_save_view_template_post_type_loop_spinner').hide();
            jQuery('#wpv-view-template-post-type-summary').show();
            jQuery('#wpv-view-template-post-type-edit').hide();
            
            wpv_view_template_post_type_show_admin_if_update_required();
        });
    });
}

function wpv_view_template_post_type_show_admin_if_update_required() {
    jQuery('#wpv-view-template-post-type-edit .wpv-update-now').each(function (index) {
        if (jQuery(this).hasClass('button-primary')) {
            jQuery('#wpv-view-template-post-type-summary').hide();
            jQuery('#wpv-view-template-post-type-edit').show();
        }
    });
}

function wpv_view_template_post_type_loop_cancel() {
    // revert the select box states.    
    for (item in archive_view_template_post_type_loop_selected) {
        jQuery('select[name="' + item + '"]').val(archive_view_template_post_type_loop_selected[item]);
    }

    jQuery('#wpv-view-template-post-type-summary').show();
    jQuery('#wpv-view-template-post-type-edit').hide();
}

//////////////////////////////////////////////////////
// archive view mode for the View editor
//////////////////////////////////////////////////////


function wpv_archive_view_edit() {
    jQuery('#wpv-archive-view-mode-summary').hide();
    jQuery('#wpv-archive-view-mode-edit').show();
}

function wpv_archive_view_ok() {
    jQuery('#wpv_archive_view_loop_spinner').show();
    
    data = jQuery('#wpv-archive-view-mode-edit :input').serialize();
    data += '&action=wpv_get_archive_view_edit_summary';
    
    jQuery.post(ajaxurl, data, function(response) {
    
        jQuery('#wpv-archive-view-mode-summary').replaceWith(response);
        
        jQuery('#wpv_archive_view_loop_spinner').hide();
        jQuery('#wpv-archive-view-mode-summary').show();
        jQuery('#wpv-archive-view-mode-edit').hide();
        
        show_view_changed_message();
    });
}

function wpv_archive_view_cancel() {
    jQuery('#wpv-archive-view-mode-summary').show();
    jQuery('#wpv-archive-view-mode-edit').hide();
}
