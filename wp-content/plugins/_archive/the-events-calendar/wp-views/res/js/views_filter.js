jQuery(document).ready(function($){
    var c = jQuery('textarea#wpv_filter_meta_html_content').val();
    
    if (c == '') {

        var data = wpv_get_filter_code();
    
        c = add_wpv_filter_data_to_content(c, data);
        jQuery('textarea#wpv_filter_meta_html_content').val(c);
        jQuery('textarea#wpv_generated_filter_meta_html_content').val(c);
    }
    
    // remove the "Save Draft" and "Preview" buttons.
    jQuery('#minor-publishing-actions').hide();
    jQuery('#misc-publishing-actions').hide();

	jQuery('#publishing-action input[name=publish]').val(wpv_save_button_text);
    
    jQuery('input[name=wpv_duplicate_view]').click(wpv_duplicate_view_click);
    wpv_duplicate_view_click();
	
	jQuery('#wpv-customize-link').insertAfter('#wpv_layout');
	jQuery('#wpv-customize-link').show();
	
	wpv_update_views_step_help();
	setInterval(wpv_update_views_step_help, 1000);

	jQuery('#major-publishing-actions').append(jQuery('#wpv-save-changes'));
	
    jQuery('#title').keyup(function(event) {
       	show_view_changed_message();
    });
	
    jQuery('#content').keyup(function(event) {
       	show_view_changed_message();
    });
	
    jQuery('input[name=_wpv_settings\\[view-query-mode\\]]').change(function(event) {
       	show_view_changed_message();
    });
	
	
    
});

function wpv_add_initial_filter_shortcode() {
    c = jQuery('textarea#content').val();
    if (c == '') {
        c += '[wpv-filter-meta-html]\n[wpv-layout-meta-html]\n';
        jQuery('textarea#content').val(c);
    }    
}

function on_generate_wpv_filter(force) {
    
    jQuery('#wpv_generating_filter').show();
    
    var data = wpv_get_filter_code();

    var c = jQuery('textarea#wpv_filter_meta_html_content').val();
    
    if (force || check_if_previous_filter_has_changed(c)) {
    
        c = add_wpv_filter_data_to_content(c, data);
        jQuery('textarea#wpv_filter_meta_html_content').val(c);
    }
    
    // save the generated value so we can compare later.
    jQuery('textarea#wpv_generated_filter_meta_html_content').val(data);
    
    jQuery('#wpv_generating_filter').hide();
}

function add_wpv_filter_data_to_content(c, data) {
    if (c.search(/\[wpv-filter-start.*?\][\s\S]*\[wpv-filter-end]/g) == -1) {
        // not there so we need to add to the start.
        c = data + c;
    } else {
        c = c.replace(/\[wpv-filter-start.*?\][\s\S]*\[wpv-filter-end]/g, data);
    }
    
    return c;
}
    


function wpv_get_filter_code() {
    
    var controls = '';

    controls += wpv_filter_controls_code();
    
    var no_user_controls = controls.length == 0;

	
    controls += wpv_get_pagination_code();
    
    var out = '';

    var no_controls = controls.length == 0;
    
    controls = '\n' + controls;
    
    if (no_controls) {
        // hide the form if there are know other controls.
        out += '[wpv-filter-start hide="true"]';
    } else {
        out += '[wpv-filter-start hide="false"]';
    }
        
    out += controls;
        
    out += '[wpv-filter-end]';
    
    return out;
    
}

function check_if_previous_filter_has_changed(body) {
    // find the filter info
	
    var match = /\[wpv-filter-start.*?\]([\s\S]*)\[wpv-filter-end\]/.exec(body);
    
    var original = jQuery('textarea#wpv_generated_filter_meta_html_content').val();

    var match_original = /\[wpv-filter-start.*?\]([\s\S]*)\[wpv-filter-end\]/.exec(original);
        
    if (match && match_original) {
        if (match_original[1] != match[1]) {
            // something has changed
            jQuery('#wpv_filter_meta_html_content_error').show();
			jQuery('#wpv_filter_control_meta_html_content_error').hide();
            wpv_view_filter_meta_html();
            return false;
        }
    }

    jQuery('#wpv_filter_meta_html_content_error').hide();
	jQuery('#wpv_filter_control_meta_html_content_error').hide();
    return true;
}

var post_types_selected = Array();
var query_type_selected = Array();
function wpv_show_type_edit() {

    // record checked items just in case the operation is cancelled.    
    post_types_selected = jQuery('input[name="_wpv_settings\\[post_type\\]\\[\\]"]:checked');
    query_type_selected = jQuery('input[name="_wpv_settings\\[query_type\\]\\[\\]"]:checked');
    
    wpv_filter_show_edit_mode('type');
}

function wpv_show_type_edit_ok() {
    data = jQuery('#wpv-filter-type-edit :input').serialize();
    data += '&action=wpv_get_type_filter_summary';
    
    jQuery.post(ajaxurl, data, function(response) {
        jQuery('#wpv-filter-type-show').html(response);
        wpv_filter_hide_edit_mode('type')
        
    });
	
	show_view_changed_message();
    
}

var post_type_controls = Array('#wpv-post-type-checkboxes',
                               '#wpv-post-order-by',
                               '#wpv-post-limit',
                               '#wpv-post-types-settings',
                               '.wpv_post_type_filter_row',
                               '.wpv_add_filters_button',
                               '.wpv-post-type-field',
                               '.wpv_add_fields_button');
var taxonomy_controls = Array('#wpv-taxonomy-radios',
                              '#wpv-taxonomy-order-by',
                              '#wpv-taxonomy-limit',
                              '#wpv-taxonomy-settings',
                              '.wpv_taxonomy_filter_row',
                              '.wpv-taxonomy-field',
                              '.wpv_add_filters_taxonomy_button',
                              '.wpv_add_taxonomy_fields_button');


/*
 * Filter activated when the "Posts (This View returns posts)" is clicked 
 */
function wpv_select_post_type_filter() {
    for (var i= 0; i < post_type_controls.length; i++) {
		jQuery(post_type_controls[i]).each (function (index) {
			if (!jQuery(this).hasClass('wpv_edit_row')) {
				jQuery(this).show();
			}
		});
    }

    for (var i= 0; i < taxonomy_controls.length; i++) {
        jQuery(taxonomy_controls[i]).hide();
    }
    
    jQuery('.editor_addon_dropdown .wrapper .group').each(function() {
    		jQuery(this).show();
    });
    
    jQuery('#wpv-layout-help-posts').show();
    jQuery('#wpv-layout-help-taxonomy').hide();
    
    jQuery('#wpv-layout-v-icon-posts').show();
    jQuery('#wpv-layout-v-icon-taxonomy').hide();

    // Generate the layout 
    on_generate_wpv_layout(true);
}

/*
 * Filter activated when "Taxonomy (This View returns taxonomies)" is selected
 */
function wpv_select_taxonomy_type_filter() {
    for (var i= 0; i < post_type_controls.length; i++) {
        jQuery(post_type_controls[i]).hide();
    }

    for (var i= 0; i < taxonomy_controls.length; i++) {
        jQuery(taxonomy_controls[i]).show();
    }
    
    // show all items that have class .taxonomy applied, i.e. "Taxonomy"
    jQuery('.editor_addon_dropdown .wrapper .group').each(function() {
    	if(!jQuery(this).hasClass('taxonomy')) {
    		jQuery(this).hide();
    	} else {
    		jQuery(this).show();
    	}
    });
    
    jQuery('#wpv-layout-help-posts').hide();
    jQuery('#wpv-layout-help-taxonomy').show();

    jQuery('#wpv-layout-v-icon-posts').hide();
    jQuery('#wpv-layout-v-icon-taxonomy').show();

    // Generate the layout 
    on_generate_wpv_layout(true);
}

function wpv_show_type_edit_cancel() {

    // uncheck any that may have been checked.
    jQuery('input[name="_wpv_settings\\[post_type\\]\\[\\]"]').each( function(index) {
        jQuery(this).attr('checked', false);
    });
    jQuery('input[name="_wpv_settings\\[query_type\\]\\[\\]"]').each( function(index) {
        jQuery(this).attr('checked', false);
    });
    
    // check the items that were selected.
    post_types_selected.each( function(index) {
        jQuery(this).attr('checked', true);
    });
    query_type_selected.each( function(index) {
        jQuery(this).attr('checked', true);
    });
    
    if (query_type_selected.val() == 'posts') {
        wpv_select_post_type_filter()
    }
    if (query_type_selected.val() == 'taxonomy') {
        wpv_select_taxonomy_type_filter()
    }
    
    wpv_filter_hide_edit_mode('type');
}

function wpv_filter_show_edit_mode(id) {
    jQuery('#wpv-filter-' + id + '-edit').parent().parent().css('background-color', jQuery('#wpv-filter-' + id + '-edit').css('background-color'));
    
    jQuery('#wpv-filter-' + id + '-edit').show();
    jQuery('#wpv-filter-' + id + '-show').hide();
   
}

function wpv_filter_hide_edit_mode(id) {
    jQuery('#wpv-filter-' + id + '-edit').parent().parent().css('background-color', '');
    jQuery('#wpv-filter-' + id + '-edit').hide();
    jQuery('#wpv-filter-' + id + '-show').show();
}

var post_status_selected = Array();
function wpv_show_filter_status_edit() {

    // record checked items just in case the operation is cancelled.    
    post_status_selected = jQuery('input[name="_wpv_settings\\[post_status\\]\\[\\]"]:checked');
    
    wpv_filter_show_edit_mode('status');
}

function wpv_show_filter_status_edit_ok() {
    
    // get selected post status.
    var selected = new Array;
    jQuery('input[name="_wpv_settings\\[post_status\\]\\[\\]"]:checked').each( function(index) {
        selected.push(jQuery(this).attr('value'));
    });

    // find the filter row in the table.
    var tr = jQuery('#wpv-filter-status-show').parent().parent();
    var row = tr.attr('id').substr(15);

    var data = {
        action : 'wpv_get_table_row_ui',
        type_data : 'post_status',
        row : row,
        checkboxes : selected,
        wpv_nonce : jQuery('#wpv_get_table_row_ui_nonce').attr('value')
    };

    var td = '';
    jQuery.post(ajaxurl, data, function(response) {
        td = response;
        jQuery('#wpv_filter_row_' + row).html(td);
        wpv_filter_hide_edit_mode('status');
    });
   
	show_view_changed_message();
   
}

function wpv_show_filter_status_edit_cancel() {
    // uncheck any that may have been checked.
    
    jQuery('input[name="_wpv_settings\\[post_status\\]\\[\\]"]').each( function(index) {
        jQuery(this).attr('checked', false);
    });
    post_status_selected.each( function(index) {
        jQuery(this).attr('checked', true);
    });
    
    wpv_filter_hide_edit_mode('status');
}

function wpv_duplicate_view_click() {
    
    
    if (jQuery('.wpv_duplicate_from_original').is(":checked")) {
        jQuery('#wpv_view_query_controls_over').show();
        jQuery('#wpv_view_layout_controls_over').show();
    } else {
        jQuery('#wpv_view_query_controls_over').hide();
        jQuery('#wpv_view_layout_controls_over').hide();
    }
    
}

/* 
 * function that filters items for the Vmenu based on checkboxes (specific post types) 
 */

function wpv_filter_vmenu_items() {
	jQuery('.editor_addon_dropdown .wrapper .group').each(function() {
    		jQuery(this).hide();
    });
	
	// check the number of checkboxes for Posts listing
	var selected_posttype_checkboxes_count = jQuery('#wpv-post-type-checkboxes ul li input').filter(':checked').length;
	
	// if zero, do standard listing
	if(selected_posttype_checkboxes_count == 0) {
		wpv_select_post_type_filter();
	} 
	else {
		jQuery('#wpv-post-type-checkboxes ul li input:checked').each(function() {
			var value = jQuery(this).val();
	
			 jQuery('.editor_addon_dropdown .wrapper .group').each(function() {
		    	if(jQuery(this).hasClass(value)) {
		    		jQuery(this).show();
		    	}
		    });
		});
	}
}

function wpv_show_post_body() {
    jQuery('#postdivrich').show();
	jQuery('#wpv-customize-link').hide();
    jQuery('#wpv-learn-about-views-editing').show();
	
    jQuery('html, body').animate({
         scrollTop: jQuery("#postdivrich").offset().top
     }, 500);
	
}

function wpv_hide_post_body() {
    jQuery('#postdivrich').hide();
	jQuery('#wpv-customize-link').show();
    jQuery('#wpv-learn-about-views-editing').hide();
	
}

function wpv_update_views_step_help() {
	// update the steps helper display in the View editor.
	
    var post_types = jQuery('input[name="_wpv_settings\\[post_type\\]\\[\\]"]:checked');
    var query_type = jQuery('input[name="_wpv_settings\\[query_type\\]\\[\\]"]:checked').val();
    var view_query_mode = jQuery('input[name=_wpv_settings\\[view-query-mode\\]]:checked').val();
	
	var help_1_complete = false;
	var help_2_complete = false;
	var help_3_complete = false;
	if (jQuery('input[name="post_title"]').val() != '') {
		help_1_complete = true;
	} else {
		jQuery('#wpv-step-help-1').removeClass().addClass('wpv-incomplete-step')
	}
	
	if (!jQuery('#wpv-filter-type-edit').is(":visible")) {
		if (post_types.length || query_type == 'taxonomy' || view_query_mode == 'archive') {
			help_2_complete = true;
		} else {
			jQuery('#wpv-step-help-2').removeClass().addClass('wpv-incomplete-step')
		}
	}
	
	if (jQuery('#wpv_field_row_0').length != 0) {
		help_3_complete = true;
	} else {
		jQuery('#wpv-step-help-3').removeClass().addClass('wpv-incomplete-step')
	}

	if ((jQuery('#wpv-step-help-4').attr('class') == 'wpv-complete-step' ||
			jQuery('#wpv-step-help-4').attr('class') == 'wpv-complete-step-all') &&
			help_1_complete && help_2_complete && help_3_complete) {

		jQuery('#wpv-step-help-1').removeClass().addClass('wpv-complete-step-all')
		jQuery('#wpv-step-help-2').removeClass().addClass('wpv-complete-step-all')
		jQuery('#wpv-step-help-3').removeClass().addClass('wpv-complete-step-all')
		jQuery('#wpv-step-help-4').removeClass().addClass('wpv-complete-step-all')
		
	} else {
		if (jQuery('#wpv-step-help-4').attr('class') == 'wpv-complete-step-all') {
			jQuery('#wpv-step-help-4').removeClass().addClass('wpv-complete-step')
		}

		if (help_1_complete) {
			jQuery('#wpv-step-help-1').removeClass().addClass('wpv-complete-step')
		}
	
		if (help_2_complete) {
			jQuery('#wpv-step-help-2').removeClass().addClass('wpv-complete-step')
		}
	
		if (help_3_complete) {
			jQuery('#wpv-step-help-3').removeClass().addClass('wpv-complete-step')
		}
	}	
}

function show_view_changed_message() {
	jQuery('#wpv-save-changes').show();
}
