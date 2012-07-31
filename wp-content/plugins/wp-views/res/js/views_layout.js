var current_index_wpv = -1;
var current_timer_wpv = -1;
var original_color_wpv = Array();
var highlight_color_wpv = '#aaf8aa';

function on_delete_wpv(index) {
    index = Number(index);

    // Move this row to the bottom
    var temp_index = index + 1;
    while (jQuery('#wpv_field_row_' + temp_index).length != 0) {
        on_down_wpv(temp_index - 1, true);
        temp_index++;
    }
    
    // temporarily remove the views template selector.
    jQuery('#views_template_body').hide();
    jQuery('#views_template_body').appendTo('#view_layout_fields');
    
    // remove the last row.
    jQuery('#wpv_field_row_' + (temp_index - 1)).remove();

    on_generate_wpv_layout(false);
    show_body_view_template_controls();
    show_taxonomy_view_controls();
	show_view_changed_message();
    
};

function on_down_wpv(index, no_highlight) {
    index = Number(index);
    current_index_wpv = index;
    
    if (!no_highlight) {
        original_color_wpv[index] = jQuery('#wpv_field_row_' + index).css('background-color');
        original_color_wpv[index + 1] = jQuery('#wpv_field_row_' + (index + 1)).css('background-color');
        jQuery('#wpv_field_row_' + index).css('background-color', highlight_color_wpv);
        jQuery('#wpv_field_row_' + (index + 1)).css('background-color', highlight_color_wpv);
    }
    

    jQuery('#wpv_field_row_' + index).insertAfter(jQuery('#wpv_field_row_' + (index + 1)));

    jQuery('#wpv_field_row_' + (index + 1)).attr('id', '#wpv_field_row_temp');
    jQuery('#wpv_field_row_' + index).attr('id', '#wpv_field_row_' + (index + 1));
    jQuery('#wpv_field_row_temp').attr('id', '#wpv_field_row_' + index);
    
    wpv_update_layout_rows();

    if (!no_highlight) {
        // Start a timer to turn of the highlight.
        current_timer_wpv = window.setInterval(function() {
            
            jQuery('#wpv_field_row_' + current_index_wpv).css('background-color', original_color_wpv[current_index_wpv]);
            jQuery('#wpv_field_row_' + (current_index_wpv + 1)).css('background-color', original_color_wpv[current_index_wpv + 1]);
            
            window.clearInterval(current_timer_wpv);
        }, 150);
    }
    
    on_generate_wpv_layout(false);
};

function wpv_update_layout_rows() {
    var row = 0;
    jQuery('#view_layout_fields_table tbody tr').each(function(){
        jQuery(this).attr('id', 'wpv_field_row_'+row);
        jQuery(this).children('td:first-child').children('img').attr('onclick', 'on_delete_wpv('+row+')');
        jQuery(this).children('td:nth-child(2)').children('input').attr('id', 'wpv_field_prefix_'+row).attr('name', '_wpv_layout_settings[fields][prefix_'+row+']');
        
		jQuery(this).children('td:nth-child(3)').children('span').attr('id', 'wpv_field_name_'+row);
        jQuery(this).children('td:nth-child(3)').children('input:nth-child(2)').attr('id', 'wpv_field_name_hidden_'+row).attr('name', '_wpv_layout_settings[fields][name_'+row+']');
        if (jQuery(this).children('td:nth-child(3)').children('div').length !== 0) {
            jQuery(this).children('td:nth-child(3)').children('div').attr('id', 'views_template_body_'+row);
            jQuery(this).children('td:nth-child(3)').children('div').children('select').attr('id', 'views_template_'+row).attr('name', 'views_template_'+row);
        }
        jQuery(this).children('td:nth-child(3)').children('input:nth-child(3)').attr('id', 'wpv_types_field_name_hidden_'+row).attr('name', '_wpv_layout_settings[fields][types_field_name_'+row+']');
        jQuery(this).children('td:nth-child(3)').children('input:nth-child(4)').attr('id', 'wpv_types_field_data_hidden_'+row).attr('name', '_wpv_layout_settings[fields][types_field_data_'+row+']');
        
		jQuery(this).children('td:nth-child(4)').children('input').attr('id', 'wpv_field_row_title_'+row).attr('name', '_wpv_layout_settings[fields][row_title_'+row+']');
        jQuery(this).children('td:nth-child(5)').children('input').attr('id', 'wpv_field_suffix_'+row).attr('name', '_wpv_layout_settings[fields][suffix_'+row+']');
        row++;
    });
    
}

function on_up_wpv(index) {
    index = Number(index);
    on_down_wpv(index - 1, false);
};
function on_add_field_wpv(menu, name, text) {
	
	if(menu.indexOf('Types-!-Complete-!-') != 0) {
		text = editor_decode64(text);
	}
	
    var view_template = '';
    var view = '';
    var title = name;
	var types_field_name = '';
	var types_field_data = '';

    if (menu == 'View template') {
        view_template = text;
        menu = '';
        name = 'Body';
        title = name;
    } else if (menu == wpv_taxonomy_view_text || menu == wpv_post_view_text) {
        view = text;
        name = wpv_taxonomy_view_text;
        menu = '';
        title = wpv_taxonomy_view_text;
    } 
    // for taxonomies, add the necessary shortcode parts 
    else if(menu.indexOf(wpv_add_taxonomy_text +'-!-') == 0) {
    	name = 'wpv-taxonomy type="' + name + '" separator=", " format="link" show="name"'; 
    }
	else if(menu.indexOf('Types-!-Complete-!-') == 0) {
        title = 'Types - ' + name;
		types_field_name = name;
		types_field_data = text;
		text = name;
		name = 'types-field';
    }
    else if(menu.indexOf('Types-!-') == 0) {
        title = 'Types - ' + name;
        name = title;
    }
    else if (menu != '') {
        name = menu.split('-!')[0] + ' - ' + name;
        title = name;
    }
    
 
    // find the last row.
    var temp_index = 0;
    while (jQuery('#wpv_field_row_' + temp_index).length != 0) {
        temp_index++;
    }
    
    // add a down arrow to the last row.
    var down_image = wpv_url + '/res/img/arrow-down.png';
    jQuery('#wpv_field_row_' + (temp_index - 1) + ' .arrow-none').attr('src', down_image);
    jQuery('#wpv_field_row_' + (temp_index - 1) + ' .arrow-none').attr('onclick', 'on_down_wpv(' + (temp_index - 1) + ')');
    jQuery('#wpv_field_row_' + (temp_index - 1) + ' .arrow-none').attr('style', 'cursor: pointer;');
    jQuery('#wpv_field_row_' + (temp_index - 1) + ' .arrow-none').attr('class', 'arrow-down');

    // add a new row.
    var td = '<td width="20px"><img src="' + wpv_url + '/res/img/delete.png" onclick="on_delete_wpv(\'' + temp_index + '\')" style="cursor: pointer"></td>';
    td += '<td width="120px"><input id="wpv_field_prefix_' + temp_index + '" type="text" value="" name="_wpv_layout_settings[fields][prefix_' + temp_index + ']" width="100%"></td>';
    td += '<td width="76px">';
	
	td += '<span id="wpv_field_name_' + temp_index + '">' + title + '</span>';
	td += '<input id="wpv_field_name_hidden_' + temp_index + '" type="hidden" value="' + name + '" name="_wpv_layout_settings[fields][name_' + temp_index + ']" >';
	td += '<input id="wpv_types_field_name_hidden_' + temp_index + '" type="hidden" value="' + types_field_name + '" name="_wpv_layout_settings[fields][types_field_name_' + temp_index + ']" >';
	td += '<input id="wpv_types_field_data_hidden_' + temp_index + '" type="hidden" value="' + types_field_data + '" name="_wpv_layout_settings[fields][types_field_data_' + temp_index + ']" >';
	
	td += '</td>';
    td += '<td class="row-title hidden" width="120px"><input id="wpv_field_row_title_' + temp_index + '" type="text" name="_wpv_layout_settings[fields][row_title_' + temp_index + ']" value="' + text + '"></td>';
    td += '<td width="120px"><input id="wpv_field_suffix_' + temp_index + '" type="text" value="" name="_wpv_layout_settings[fields][suffix_' + temp_index + ']" width="100%"></td>';
    td += '<td width="16px"><img src="' + wpv_url + '/res/img/move.png" class="move" style="cursor: move;" /></td>';

    var query_type = jQuery('input[name="_wpv_settings\\[query_type\\]\\[\\]"]:checked').val();
    var row_class = '';
    if (query_type == 'posts') {
        row_class = 'class="wpv-post-type-field"';
    } else if (query_type == 'taxonomy') {
        row_class = 'class="wpv-taxonomy-field"';
    }
    
    jQuery('#view_layout_fields_table').append('<tr id="wpv_field_row_' + temp_index + '" ' + row_class + '>' + td + '</tr>');
    
    // Show/hide row title
    view_layout_fields_table_add_row_title_field();

    tb_remove();
    
    show_body_view_template_controls();
    
    if (view_template != '') {
        // We're adding a view template. Select in the drop down.
        select_view_template_in_dropdown(temp_index, view_template);
    }


    show_taxonomy_view_controls();
    
    if (view != '') {
        // We're adding a taxonomy view. Select in the drop down.
        select_taxonomy_view_in_dropdown(temp_index, view);
    }

    on_generate_wpv_layout(false);
	
	show_view_changed_message();

};

function on_add_field_wpv_types_callback(types_call, field_title) {
	// We're calling a types function.
	// We need to set a re-direct so that the result of the Types popup
	// returns here instead of being inserted into the editor.
	if (wpcfFieldsEditorCallback_set_redirect) {
		wpcfFieldsEditorCallback_set_redirect('window.parent.on_add_field_wpv_types_callback_action', {'title' : field_title});
	}
	eval(types_call);
}

function on_add_field_wpv_types_callback_action(shortcode, params) {
	// The result of a Types popup.
	on_add_field_wpv('Types-!-Complete-!-', params['title'], shortcode);
}

jQuery(document).ready(function($){
    
    show_body_view_template_controls();
    show_taxonomy_view_controls();
    
    var c = jQuery('textarea#wpv_layout_meta_html_content').val();
    
    if (c == '') {

        on_generate_wpv_layout(true);
    }
    
});

function on_generate_wpv_layout(force) {
    
    var query_type = jQuery('input[name="_wpv_settings\\[query_type\\]\\[\\]"]:checked').val();
    
    show_or_hide_layout_table();
    
    jQuery('#wpv_generating_layout').show();
    
    // go through each row and get the fields
    
    var header_name = '';
    var fields = Array();
    var temp_index = 0;
    var add_index = 0;
    var add_type = 0;
    while (jQuery('#wpv_field_row_' + temp_index).length != 0) {
        field_type = jQuery('#wpv_field_name_' + temp_index).html();
        if (field_type.indexOf(wpv_field_text) == 0) {
            add_type = 'custom';
            // a custom field
            
            header_name = 'post-field-' + field_type.substring(wpv_field_text.length);
            field_type = '[wpv-post-field name="' + field_type.substring(wpv_field_text.length) + '"]';
        }
		else if (jQuery('#wpv_field_name_hidden_' + temp_index).val() == 'types-field') {
            add_type = 'custom';
			header_name = 'types-field-' + field_type.substring('Types - '.length);
			field_type = jQuery('#wpv_types_field_data_hidden_' + temp_index).val();
		}
        else if (field_type.indexOf('Types - ') == 0) {
            add_type = 'custom';
            // a custom field
            
			header_name = 'types-field-' + field_type.substring('Types - '.length);
			field_type = '[types field="' + field_type.substring('Types - '.length) + '"][/types]';
        } 
        // taxonomy search, some prefix preset
        else if(field_type.indexOf('wpvtax-') > 0) {
        	add_type = 'posts';
        	field_type = field_type.substring(field_type.indexOf('wpvtax-') + 7);
        	// handle taxonomies
			field_type = '[wpv-post-taxonomy type="' + field_type + '" separator=", " format="link" show="name"]';
        	
        } else {
            // a post field
            
            for(var i = 0; i < wpv_shortcodes.length; i++) {
                if (wpv_shortcodes[i][0] == field_type) {
                    
                    if (wpv_shortcodes[i][1].indexOf('wpv-taxonomy') == 0) {
                        add_type = 'taxonomy';
                    } else {
                        add_type = 'posts';
                    }

                    if (wpv_shortcodes[i][1] == 'wpv-post-body') {
                        var selected = jQuery('select[name="views_template_' + temp_index + '"]').val();
                        selected = jQuery('#views_template_' + temp_index + ' option[value="' + selected + '"]').text();
                        field_type = '[wpv-post-body view_template="' + selected + '"]';
                    } else if (wpv_shortcodes[i][1] == 'wpv-view') {
                        add_type = 'taxonomy';
                        var selected = jQuery('select[name="taxonomy_view_' + temp_index + '"]').val();
                        selected = jQuery('#taxonomy_view_' + temp_index + ' option[value="' + selected + '"]').text();
                        // remove the " - Post View" or " - Taxonomy View" from the selection.
                        selected = selected.replace(' - ' + wpv_taxonomy_view_text, '');
                        selected = selected.replace(' - ' + wpv_post_view_text, '');
                        field_type = '[wpv-view name="' + selected + '"]';
                    } else {
                        field_type = '[' + wpv_shortcodes[i][1] + ']';
                        field_type = field_type.replace(/_/g, '-');
                    }
                    header_name = wpv_shortcodes[i][1].substring(4);
                    header_name = header_name.replace(/_/g, '-');
                    break;
                } 
            } 
        }
        
        if ((query_type == 'posts' && (add_type == 'custom' || add_type == 'posts')) || (query_type == 'taxonomy' && add_type == 'taxonomy')) {
            var open_wrapper = (query_type == 'taxonomy') ? '[' : '';
            var close_wrapper = (query_type == 'taxonomy') ? ']' : '';
            
        	fields[add_index] = Array(jQuery('#wpv_field_prefix_' + temp_index).val(),
                                       field_type,
                                       jQuery('#wpv_field_suffix_' + temp_index).val(),
                                       jQuery('#wpv_field_name_' + temp_index).text(),
                                       header_name,
                                       jQuery('#wpv_field_row_title_' + temp_index).val());
            add_index++;
        }        
        temp_index++;
    }
    
    var style_selected = jQuery('select[name="_wpv_layout_settings[style]"]').val();
    switch (style_selected) {
        case "table":
            var cols_selected = jQuery('select[name="_wpv_layout_settings[table_cols]"]').val();
            data = wpv_render_table_layout(fields, cols_selected);
            break;
        
        case "table_of_fields":
            data = wpv_render_table_of_fields_layout(fields);
            break;
        
        case "ordered_list":
            data = wpv_render_ordered_list_layout(fields);
            break;
        
        case "un_ordered_list":
            data = wpv_render_un_ordered_list_layout(fields);
            break;
        
        default:
            // unformatted
            data = wpv_render_unformatted_layout(fields);
            break;
    }
    
    var no_results_text = '';
    if (query_type == 'posts') {
        no_results_text = no_post_results_text;
    } else if (query_type == 'taxonomy') {
        no_results_text = no_taxonomy_results_text;
    }
    
    data = "\[wpv-layout-start\]\n<!-- wpv-loop-start -->\n" + data + "\n<!-- wpv-loop-end -->\n" + no_results_text + "\n\[wpv-layout-end\]\n";

    c = jQuery('textarea#wpv_layout_meta_html_content').val();
    
    if (force || check_previous_layout_has_changed(c)) {
    
        c = add_wpv_layout_data_to_content(c, data);
        jQuery('textarea#wpv_layout_meta_html_content').val(c);
    }

    // save the generated value so we can compare later.
    jQuery('textarea#wpv_generated_layout_meta_html_content').val(data);
    jQuery('#wpv_generating_layout').hide();
}

function show_or_hide_layout_table() {
    if (jQuery('#view_layout_fields_table tr').length == 2) { // only the header and footer
        jQuery('#view_layout_fields_table').hide();
        jQuery('#view_layout_add_field_message_1').hide();
        jQuery('#view_layout_fields_to_include').hide();
        jQuery('#view_layout_add_field_message_2').show();
        jQuery('input[name=wpv-layout-add-field]').removeClass('button-secondary').addClass('button-primary');
    } else {
        jQuery('#view_layout_fields_table').show();
        jQuery('#view_layout_add_field_message_1').show();
        jQuery('#view_layout_fields_to_include').show();
        jQuery('#view_layout_add_field_message_2').hide();
        jQuery('input[name=wpv-layout-add-field]').removeClass('button-primary').addClass('button-secondary');
    }
}

function replace_to_html_fn(test, group1) {
    return "<wpv-loop" + group1 + ">";
}

function wpv_visual_to_html(data) {
    data = data.replace(/<strong>&lt;wpv-loop(.*?)&gt;<\/strong><br \/>/g, replace_to_html_fn);
    data = data.replace(/<strong>&lt;wpv-loop(.*?)&gt;<\/strong>/g, replace_to_html_fn);
    data = data.replace(/<br \/><strong>&lt;\/wpv-loop&gt;<\/strong>/g, '</wpv-loop>');
    return data;
}

function replace_to_visual_fn(test, group1) {
    return "<strong>&lt;wpv-loop" + group1 + "&gt;</strong><br />";
}

function wpv_html_to_visual(data) {
    data = data.replace(/<wpv-loop(.*?)>/g, replace_to_visual_fn);
    data = data.replace(/<\/wpv-loop>/g, "<br /><strong>&lt;/wpv-loop&gt;</strong>");
    return data;
}

function add_wpv_layout_data_to_content(c, data) {
    
    if (c.search(/\[wpv-layout-start\][\s\S]*\[wpv-layout-end\]/g) == -1) {
        // not there so we need to add.
        c += data;
    } else {
        c = c.replace(/\[wpv-layout-start\][\s\S]*\[wpv-layout-end\]/g, data);
    }
    
    return c;
    
}

function wpv_render_unformatted_layout(fields) {
    
    var body = "";
    for ( var i = 0; i < fields.length; i++ ) {
        body += fields[i][0];
        body += fields[i][1];
        body += fields[i][2];
    }
    
    var output = "   <wpv-loop>\n";
    output += "      " + body + "\n";
    output += "   </wpv-loop>\n";
    
    return output;
    
}

function wpv_render_table_layout(fields, cols) {
    
    var body = "";
    for ( var i = 0; i < fields.length; i++ ) {
        body += fields[i][0];
        body += fields[i][1];
        body += fields[i][2];
    }
    
    var output = "   <table width=\"100%\">\n      <wpv-loop wrap=\"" + cols + "\" pad=\"true\">\n";
    output += "         [wpv-item index=1]\n";
    output += "            <tr><td>" + body + "</td>\n";
    output += "         [wpv-item index=other]\n";
    output += "            <td>" + body + "</td>\n";
    output += "         [wpv-item index=" + cols + "]\n";
    output += "            <td>" + body + "</td></tr>\n";
    output += "         [wpv-item index=pad]\n";
    output += "            <td></td>\n";
    output += "         [wpv-item index=pad-last]\n";
    output += "            <td></td></tr>\n";
    output += "      </wpv-loop>\n   </table>\n";
    
    return output;
    
}

function wpv_render_table_of_fields_layout(fields) {
    
    var output = "   <table width=\"100%\">\n";

    if (jQuery('#_wpv_layout_include_field_names').attr('checked')) {
        output += "            <thead><tr>\n";
        for ( var i = 0; i < fields.length; i++ ) {
            
            output += "               <th>[wpv-heading name=\"" + fields[i][4] + "\"]" + fields[i][5] + "[/wpv-heading]</th>\n";
        }
        output += "            </tr></thead>\n";
    }    
    
    output += "      <tbody>\n";
    output += "      <wpv-loop>\n";
    
    output += "            <tr>\n";
    for ( var i = 0; i < fields.length; i++ ) {
        var body = fields[i][0];
        body += fields[i][1];
        body += fields[i][2];

        output += "               <td>" + body + "</td>\n";
    }
    output += "            </tr>\n";
    
    output += "      </wpv-loop>\n   </tbody>\n   </table>\n";
    
    return output;
    
}

function wpv_render_ordered_list_layout(fields) {
    
    var body = "";
    for ( var i = 0; i < fields.length; i++ ) {
        body += fields[i][0];
        body += fields[i][1];
        body += fields[i][2];
    }
    
    var output = "   <ol>\n";
    output += "      <wpv-loop>\n";
    output += "         <li>" + body + "</li>\n";
    output += "      </wpv-loop>\n";
    output += "   </ol>\n";
    
    return output;
    
}

function wpv_render_un_ordered_list_layout(fields) {
    
    var body = "";
    for ( var i = 0; i < fields.length; i++ ) {
        body += fields[i][0];
        body += fields[i][1];
        body += fields[i][2];
    }
    
    var output = "   <ul>\n";
    output += "      <wpv-loop>\n";
    output += "         <li>" + body + "</li>\n";
    output += "      </wpv-loop>\n";
    output += "   </ul>\n";
    
    return output;
    
}

function check_previous_layout_has_changed(body) {
    var match = /\[wpv-layout-start\]([\s\S]*)\[wpv-layout-end\]/.exec(body);
    
    var original = jQuery('textarea#wpv_generated_layout_meta_html_content').val();
    var match_original = /\[wpv-layout-start\]([\s\S]*)\[wpv-layout-end\]/.exec(original);
    
    if (match && match_original) {
        // compare to what was generated last time
        if (match_original[1] != match[1]) {
            // something has changed
            jQuery('#wpv_layout_meta_html_content_error').show();
            wpv_view_layout_meta_html();
            return false;
        }
    }

    return true;
}

jQuery(document).ready(function($){
    jQuery('select[name="_wpv_layout_settings[style]"]').change(function() {
        var style_selected = jQuery('select[name="_wpv_layout_settings[style]"]').val();
        
        switch (style_selected) {
            case "table":
                jQuery('#_wpv_layout_table_style').show();
                jQuery('#_wpv_layout_table_of_fields_style').hide();
                jQuery('#_wpv_layout_order_list_style').hide();
                jQuery('#_wpv_layout_un_order_list_style').hide();
                break;
            
            case "table_of_fields":
                jQuery('#_wpv_layout_table_style').hide();
                jQuery('#_wpv_layout_table_of_fields_style').show();
                jQuery('#_wpv_layout_order_list_style').hide();
                jQuery('#_wpv_layout_un_order_list_style').hide();
                break;
            
            case "ordered_list":
                jQuery('#_wpv_layout_table_style').hide();
                jQuery('#_wpv_layout_table_of_fields_style').hide();
                jQuery('#_wpv_layout_order_list_style').show();
                jQuery('#_wpv_layout_un_order_list_style').hide();
                break;
            
            case "un_ordered_list":
                jQuery('#_wpv_layout_table_style').hide();
                jQuery('#_wpv_layout_table_of_fields_style').hide();
                jQuery('#_wpv_layout_order_list_style').hide();
                jQuery('#_wpv_layout_un_order_list_style').show();
                break;
            
            default:
                // unformatted
                jQuery('#_wpv_layout_table_style').hide();
                jQuery('#_wpv_layout_table_of_fields_style').hide();
                jQuery('#_wpv_layout_order_list_style').hide();
                jQuery('#_wpv_layout_un_order_list_style').hide();
                break;
        }
        // Show/Hide Row Title
        view_layout_fields_table_add_row_title_field();
        on_generate_wpv_layout(false);
		
		show_view_changed_message();

        
    });
    jQuery('select[name="_wpv_layout_settings[table_cols]"]').change(function() {
        on_generate_wpv_layout(false);
		show_view_changed_message();

    });
    jQuery('.views_template_select').change(function() {
        on_generate_wpv_layout(false);
		show_view_changed_message();
	});
    
    jQuery('.taxonomy_view_select').change(function() {
        on_generate_wpv_layout(false);
		show_view_changed_message();
    });
    
    var fixHelper = function(e, ui) {
        ui.children().each(function() {
            $(this).width($(this).width());
        });
        return ui;
    };
    
    // Sort and Drag
    jQuery('#view_layout_fields_table tbody').sortable({
        helper: fixHelper,
        revert: true,
        handle: 'img.move',
        containment: '#view_layout_fields_table',
        forceHelperSize: false,
        forcePlaceholderSize: false,
        tolerance: 'intersect',
        items: 'tr',
        update: function(event, ui){
            wpv_update_layout_rows();
            on_generate_wpv_layout(false);
            show_body_view_template_controls();
            show_taxonomy_view_controls();
			show_view_changed_message();
			
        }
    });
    // Table Row Title
    jQuery('#_wpv_layout_include_field_names').click(function(){
        view_layout_fields_table_add_row_title_field();
        on_generate_wpv_layout(false);
    });
    // Set on init
    if (jQuery('select[name="_wpv_layout_settings[style]"]').val() == 'table_of_fields') {
        view_layout_fields_table_add_row_title_field();
    }
    // Update on typing
    jQuery('#view_layout_fields_table input').blur(function(){on_generate_wpv_layout(false);});
});


function view_layout_fields_table_add_row_title_field() {
    var style = jQuery('select[name="_wpv_layout_settings[style]"]').val();
    if (style == 'table_of_fields' && jQuery('#_wpv_layout_include_field_names').is(':checked')) {
         jQuery('#view_layout_fields_table .row-title').show();
//    }
//        jQuery('#view_layout_fields_table thead tr').children('th:nth-child(3)').after('<th class="row-title" width="120px">Row title</th>');
//        jQuery('#view_layout_fields_table tfoot tr').children('th:nth-child(3)').after('<th class="row-title"></th>');
//        var row = 0;
//        jQuery('#view_layout_fields_table tbody tr').each(function(){
//            jQuery(this).children('td:nth-child(3)').after('<td class="row-title" width="120px"><input type="text" name="_wpv_layout_settings[fields][row_title_'+row+']" value="" id="wpv_field_row_title_'+row+'"></td>');
//            row++;
//        });
    } else {
        jQuery('#view_layout_fields_table .row-title').hide();
    }
}

function show_body_view_template_controls() {
    var body = jQuery('input[value="Body"]');
    body.each(function() {
        if (jQuery(this).attr('id').slice(0, 22) == 'wpv_field_name_hidden_') {
            row = jQuery(this).attr('id').slice(22);
            if (jQuery('#views_template_body_' + row).length == 0) {
                var copy = jQuery('#views_template_body').clone(true);
                copy.attr('id', 'views_template_body_' + row);
                copy.children().each(function() {
                    if (jQuery(this).attr('id') == 'views_template') {
                        jQuery(this).attr('id', 'views_template_' + row);
                        jQuery(this).attr('name', 'views_template_' + row);
                    }
                });
            }
            jQuery('#wpv_field_name_' + jQuery(this).attr('id').slice(22)).after(copy);
            jQuery('#views_template_body_' + row).css('display', 'inline');
        } else {
            jQuery('#views_template_body').hide();
        }
        
    });
}

function select_view_template_in_dropdown(row, view_template) {
    jQuery('#views_template_body_' + row + ' > #views_template_' + row + ' > option').each(function() {
        if (jQuery(this).html() == view_template) {
            jQuery(this).parent().val(jQuery(this).val());
        }
    });
}

function show_taxonomy_view_controls() {
    var taxonomy_view = jQuery('input[value="Taxonomy View"]');
    taxonomy_view.each(function() {
        if (jQuery(this).attr('id').slice(0, 22) == 'wpv_field_name_hidden_') {
            row = jQuery(this).attr('id').slice(22);
            if (jQuery('#taxonomy_view_' + row).length == 0) {
                var copy = jQuery('#taxonomy_view_select').clone(true);
                copy.attr('id', 'taxonomy_view_select_' + row);
                copy.children().each(function() {
                    if (jQuery(this).attr('id') == 'taxonomy_view') {
                        jQuery(this).attr('id', 'taxonomy_view_' + row);
                        jQuery(this).attr('name', 'taxonomy_view_' + row);
                    }
                });
            }
            jQuery('#wpv_field_name_' + jQuery(this).attr('id').slice(22)).after(copy);
            jQuery('#taxonomy_view_select_' + row).css('display', 'inline');
        } else {
            jQuery('#taxonomy_view').hide();
        }
        
    });
}

function select_taxonomy_view_in_dropdown(row, view) {
    jQuery('#taxonomy_view_select_' + row + ' > #taxonomy_view_' + row + ' > option').each(function() {
        if (jQuery(this).html() == view) {
            jQuery(this).parent().val(jQuery(this).val());
        }
    });
}

