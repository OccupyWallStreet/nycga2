<?php

require WPV_PATH . '/inc/wpv-layout-meta-html.php';

function view_layout_box($post){
    
    ?>
    <div id="wpv_view_layout_controls" style="position: relative">
        <span id="wpv_view_layout_controls_over" class="wpv_view_overlay" style="display:none">
            <p><strong><?php echo __('The view layout settings will be copied from the original', 'wpv-views'); ?></strong></p>
        </span>
    <?php
    
    $view_layout_settings = (array)get_post_meta($post->ID, '_wpv_layout_settings', true);
    
    view_layout_style($post, $view_layout_settings);
    
    view_layout_fields($post, $view_layout_settings);

    view_layout_additional_js($post, $view_layout_settings);

    wpv_layout_meta_html_admin($post, $view_layout_settings);
    
    ?>
    </div>
    <?php
    
}

function view_layout_style($post, $view_layout_settings) {
    if (!isset($view_layout_settings['include_field_names'])) {
        $view_layout_settings['include_field_names'] = true;
    }
    if (!isset($view_layout_settings['style'])) {
        $view_layout_settings['style'] = 'table';
    }
    if (!isset($view_layout_settings['table_cols'])) {
        $view_layout_settings['table_cols'] = 2;
    }
    ?>
        <fieldset>
            <p>
            <strong><?php _e('Layout style:', 'wpv-views') ?></strong>
            <select name="_wpv_layout_settings[style]">
                <?php $selected = $view_layout_settings['style']=='unformated' ? ' selected="selected"' : ''; ?>
                <option value="unformatted"<?php echo $selected ?>><?php _e('Unformatted', 'wpv-views'); ?></option>
                <?php $selected = $view_layout_settings['style']=='table' ? ' selected="selected"' : ''; ?>
                <option value="table"<?php echo $selected ?>><?php _e('Grid', 'wpv-views'); ?></option>
                <?php $selected = $view_layout_settings['style']=='table_of_fields' ? ' selected="selected"' : ''; ?>
                <option value="table_of_fields"<?php echo $selected ?>><?php _e('Table', 'wpv-views'); ?></option>
                <?php $selected = $view_layout_settings['style']=='ordered_list' ? ' selected="selected"' : ''; ?>
                <option value="ordered_list"<?php echo $selected ?>><?php _e('Ordered list', 'wpv-views'); ?></option>
                <?php $selected = $view_layout_settings['style']=='un_ordered_list' ? ' selected="selected"' : ''; ?>
                <option value="un_ordered_list"<?php echo $selected ?>><?php _e('Unordered list', 'wpv-views'); ?></option>
            </select>
            <?php echo sprintf(__('Learn about different %slayouts%s', 'wpv-views'), '<a href="http://wp-types.com/documentation/user-guides/view-layouts-101/" target="_blank">', ' &raquo;</a>');?>
            </p>
            
            <?php // TABLE LAYOUT // ?>
            
            <div id="_wpv_layout_table_style"<?php if($view_layout_settings['style']!='table'):?> style="display:none;"<?php endif; ?>>
                <p><?php _e('Layout the items using a HTML table', 'wpv-views')?></p>
                <strong><?php _e('Number of columns:', 'wpv-views')?></strong>
                <select name="_wpv_layout_settings[table_cols]">
                    <?php
                        for($i = 2; $i < 11; $i++) {
                            $selected = $view_layout_settings['table_cols']==(string)$i ? ' selected="selected"' : '';
                            echo '<option value="' . $i . '"' . $selected . '>'. $i . '</option>';
                        }
                    ?>
                </select>
            </div>

            <?php // TABLE OF FIELDS LAYOUT // ?>
            
            <div id="_wpv_layout_table_of_fields_style"<?php if($view_layout_settings['style']!='table_of_fields'):?> style="display:none;"<?php endif; ?>>
                <p><?php _e('Create a table of items with a column for each field', 'wpv-views')?></p>
                <?php $checked = $view_layout_settings['include_field_names'] ? ' checked="checked"' : '';?>
                <label><input id="_wpv_layout_include_field_names" type="checkbox" name="_wpv_layout_settings[include_field_names]"<?php echo $checked; ?>>&nbsp;<?php _e('Include field names in table headings', 'wpv-views'); ?></label>
                
            </div>

            <?php // ORDERED LIST // ?>
            
            <div id="_wpv_layout_order_list_style"<?php if($view_layout_settings['style']!='ordered_list'):?> style="display:none;"<?php endif; ?>>
                <p><?php _e('Items are added to an ordered list', 'wpv-views')?></p>
            </div>

            <?php // UNORDERED LIST // ?>
            
            <div id="_wpv_layout_un_order_list_style"<?php if($view_layout_settings['style']!='un_ordered_list'):?> style="display:none;"<?php endif; ?>>
                <p><?php _e('Items are added to an unordered list', 'wpv-views')?></p>
            </div>

            <?php // END OF LAYOUT STYLES // ?>

        </fieldset>        
    <?php
}

class View_layout_field {
    protected $type;
    protected $prefix;
    protected $suffix;
    protected $edittext;
    
    function __construct($type, $prefix = "", $suffix = "", $row_title = "", $edittext = "", $types_field_name = "", $types_field_data = ""){
        
        $this->type = $type;
        $this->prefix = $prefix;
        $this->suffix = $suffix;
        $this->row_title = $row_title;
        $this->edittext = $edittext;
        $this->types_field_name = $types_field_name;
        $this->types_field_data = $types_field_data;
    }
    
    function render_to_table($index) {
        global $wpv_shortcodes, $WPV_templates, $WP_Views;
        
        $view_template = null;
        $taxonomy_view = null;
        
        if (strpos($this->type, 'wpv-post-field - ') === 0) {
            $name = substr($this->type, strlen('wpv-post-field - '));
            $title = $name;
        } elseif ($this->type == 'types-field') {
            $name = $this->type;
            $title = 'Types - ' . $this->types_field_name;
        } elseif (strpos($this->type, 'types-field - ') === 0) {
            $name = substr($this->type, strlen('types-field - '));
            $title = $name;
        } elseif(strpos($this->type, 'wpvtax') === 0) {
        	// $name = substr($this->type, strlen('wpvtax-'));
        	$name = 'Taxonomy - '. $this->type;
            $title = $name;
        } elseif (strpos($this->type, 'wpv-post-body ') === 0) {
            $name = $wpv_shortcodes['wpv-post-body'][1];
            $parts = explode(' ', $this->type);
            if (isset($parts[1])) {
                $view_template = $parts[1];
            }
            $title = $name;
        } elseif (strpos($this->type, 'wpv-view ') === 0) {
            $name = 'Taxonomy View';
            $parts = explode(' ', $this->type);
            if (isset($parts[1])) {
                $taxonomy_view = $parts[1];
            }
            $title = $name;
        } else {
            $name = $wpv_shortcodes[$this->type][1];
            $title = $name;
        }
        
        ?>
        <td width="120px"><input id="wpv_field_prefix_<?php echo $index; ?>" type="text" value="<?php echo htmlspecialchars($this->prefix); ?>" name="_wpv_layout_settings[fields][prefix_<?php echo $index; ?>]" /></td>
        <td width="120px">
            <span id="wpv_field_name_<?php echo $index; ?>"><?php echo $title; ?></span>
            <?php
                if ($view_template) {
                    $view_template_select_box = $WPV_templates->get_view_template_select_box($index, $view_template);
                    $view_template_select_box = '<div id="views_template_body_' . $index . '" style="display:none;""><i> - ' . __('Using view template:' , 'wpv-views') . '</i>' . $view_template_select_box . '</div>';
                    echo $view_template_select_box;
                }
                if ($taxonomy_view) {
                    $taxonomy_view_select_box = $WP_Views->get_taxonomy_view_select_box($index, $taxonomy_view);
                    $taxonomy_view_select_box = '<div id="taxonomy_view_select_' . $index . '" style="display:none;"">' . $taxonomy_view_select_box . '</div>';
                    echo $taxonomy_view_select_box;
                }
            ?>
            <input id="wpv_field_name_hidden_<?php echo $index; ?>" type="hidden" value="<?php echo $name; ?>" name="_wpv_layout_settings[fields][name_<?php echo $index; ?>]">
            <input id="wpv_types_field_name_hidden_<?php echo $index; ?>" type="hidden" value="<?php echo $this->types_field_name; ?>" name="_wpv_layout_settings[fields][types_field_name_<?php echo $index; ?>]">
            <input id="wpv_types_field_data_hidden_<?php echo $index; ?>" type="hidden" value="<?php echo esc_js($this->types_field_data); ?>" name="_wpv_layout_settings[fields][types_field_data_<?php echo $index; ?>]">
        </td>
        <?php
        $row_title = $this->row_title;
        ?>
        <td class="row-title hidden" width="120px"><input type="text" id="wpv_field_row_title_<?php echo $index; ?>" value="<?php echo $row_title; ?>" name="_wpv_layout_settings[fields][row_title_<?php echo $index; ?>]" /></td>
        <td width="120px"><input id="wpv_field_suffix_<?php echo $index; ?>" type="text" value="<?php echo htmlspecialchars($this->suffix); ?>" name="_wpv_layout_settings[fields][suffix_<?php echo $index; ?>]" /></td>
        <?php
    }
    
    function render_table_row_attributes($view_settings) {
        
        if (strpos($this->type, 'wpv-taxonomy-') === 0 || strpos($this->type, 'wpv-view') === 0) {
            // taxonomy type.
            $output = 'class="wpv-taxonomy-field"';
            if ($view_settings['query_type'][0] != 'taxonomy') {
                $output .= ' style="display:none"';
            }
        } else {
            // post type
            $output = 'class="wpv-post-type-field"';
            if ($view_settings['query_type'][0] != 'posts') {
                $output .= ' style="display:none"';
            }
        }
        
        return $output;
        
    }
    
    function get_body_template() {
        if (strpos($this->type, 'wpv-post-body ') === 0) {
            $parts = explode(' ', $this->type);
            return $parts[1];
        } else {
            return -1;
        }
    }
    
}

$link_layout_number = 0;

function view_layout_fields_to_classes($fields) {

    $output = array();
    
    for ($i = 0; $i < sizeof($fields); $i++) {
        
        if (!isset($fields["name_{$i}"])) {
            break;
        }
        
        $output[] = new View_layout_field($fields["name_{$i}"],
                                          $fields["prefix_{$i}"],
                                          $fields["suffix_{$i}"],
                                          isset($fields["row_title_{$i}"]) ? $fields["row_title_{$i}"] : '',
        								  isset($fields["edittext_{$i}"]) ? $fields["edittext_{$i}"] : '',
                                          isset($fields["types_field_name_{$i}"]) ? $fields["types_field_name_{$i}"] : '',
                                          isset($fields["types_field_data_{$i}"]) ? $fields["types_field_data_{$i}"] : '');
        
    }
    
    return $output;
}

function view_layout_fields($post, $view_layout_settings) {
    
    global $WP_Views;
    $view_settings = $WP_Views->get_view_settings($post->ID);
    
    if (isset($view_layout_settings['fields'])) {
        $view_layout_settings['fields'] = view_layout_fields_to_classes($view_layout_settings['fields']);
    } else {
        $view_layout_settings['fields'] = array();
    }

    view_layout_javascript();
    
    global $WPV_templates;
    $template_selected = 0;
    foreach ($view_layout_settings['fields'] as $field) {
        $posible_template = $field->get_body_template();
        if ($posible_template >= 0) {
            $template_selected = $posible_template;
            break;
        }
    }
    
    // Add a select control so that we can chose the view template for the body.
    $view_template_select_box = $WPV_templates->get_view_template_select_box('', '');
    $view_template_select_box = '<div id="views_template_body" style="display:none;""><i> - ' . __('Using View template:' , 'wpv-views') . '</i>' . $view_template_select_box . '</div>';


    // Add a select control so that we can chose the Taxonomy View.
    $taxonomy_view_select_box = $WP_Views->get_taxonomy_view_select_box('', '');
    $taxonomy_view_select_box = '<div id="taxonomy_view_select" style="display:none;"">' . $taxonomy_view_select_box . '</div>';

    ?>
    
    <div id="view_layout_fields" class="view_layout_fields">
        
        <p id="view_layout_fields_to_include"><strong><?php echo __('Fields to include:', 'wpv-views'); ?></strong></p>
        
        <?php echo $view_template_select_box; ?>
        <?php echo $taxonomy_view_select_box; ?>
        
        <p id="view_layout_add_field_message_1"><?php echo __("Click on <strong>Add field</strong> to insert additional fields. Drag them to reorder, or delete fields that you don't need.", 'wpv-views'); ?></p>
        <p id="view_layout_add_field_message_2" style="display:none"><?php echo __("Click on <strong>Add field</strong> to insert fields to this View.", 'wpv-views'); ?></p>
        
        <table id="view_layout_fields_table" class="widefat fixed">
            <thead>
                <tr>
                    <th width="20px"></th><th width="120px"><?php echo __('Prefix', 'wpv-views'); ?></th><th width="220px"><?php echo __('Field', 'wpv-views'); ?></th><th class="row-title hidden" width="120px"><?php echo __('Row Title', 'wpv-views'); ?></th><th width="120px"><?php echo __('Suffix', 'wpv-views'); ?></th><th width="16px"></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th></th><th></th><th></th><th class="row-title hidden"></th><th></th><th></th>
                </tr>
            </tfoot>
            
            <tbody>
                <?php
                $count = sizeof($view_layout_settings['fields']);
                foreach($view_layout_settings['fields'] as $index => $field) {
                    ?>
                    <tr id="wpv_field_row_<?php echo $index; ?>" <?php echo $field->render_table_row_attributes($view_settings); ?>>
                    
                        <td width="20px"><img src="<?php echo WPV_URL . '/res/img/delete.png'; ?>" onclick="on_delete_wpv(<?php echo $index; ?>)" style="cursor: pointer"></td><?php $field->render_to_table($index); ?><td width="16px"><img src="<?php echo WPV_URL; ?>/res/img/move.png" class="move" style="cursor: move;" /></td>
                    
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        
        </table>
        <br />
    </div>
    
    <?php
        $show = $view_settings['query_type'][0] == 'posts';
    ?>
    <input class="button-secondary wpv_add_fields_button" type="button" value="<?php echo __('Add field', 'wpv-views'); ?>" name="wpv-layout-add-field" <?php if($show) {echo '';} else {echo ' style="display:none"';} ?>>
    <div id="add_field_popup" style="display:none; overflow: auto;">

        <?php
        global $link_layout_number;
        $link_layout_number = 0;
        $WP_Views->editor_addon->add_form_button('', '#wpv_layout_meta_html_content', false);
        //add_short_codes_to_js(array('post', 'body-view-templates'), null, 'short_code_menu_callback');
        ?>

    </div>  

	<?php // echo $WP_Views->editor_addon->add_form_button('', '#wpv_layout_meta_html_content', false); ?>
    <?php // Add a popup for taxonomy fields ?>
    
    <div id="add_taxonomy_field_popup" style="display:none">

        <table id="wpv_taxonomy_field_popup_table" width="100%">
        <tr>
        <?php
        global $link_layout_number;
        $link_layout_number = 0;
        add_short_codes_to_js(array('taxonomy', 'taxonomy-view', 'post-view'), null, 'short_code_taxonomy_menu_callback');
        ?>
        </tr>
        </table>

    </div>  

    <script type="text/javascript">
        var wpv_shortcodes = new Array();
        <?php
            $current_index = add_short_codes_to_js(array('post', 'taxonomy', 'taxonomy-view', 'post-view'), null, 'short_code_variable_callback');
        ?>
        wpv_shortcodes[<?php echo $current_index; ?>] = new Array('Taxonomy View', 'wpv-view');
        var wpv_view_template_text = "<?php echo __('View template', 'wpv-views'); ?>";
        var wpv_taxonomy_view_text = "<?php echo __('Taxonomy View', 'wpv-views'); ?>";
        var wpv_post_view_text = "<?php echo __('Post View', 'wpv-views'); ?>";
        var wpv_add_field_text = "<?php echo __('Field', 'wpv-views'); ?>";
        var wpv_add_taxonomy_text = "<?php echo __('Taxonomy', 'wpv-views'); ?>";
    </script>
    <?php
        $show = $view_settings['query_type'][0] == 'taxonomy';
    ?>
    <input alt="#TB_inline?inlineId=add_taxonomy_field_popup" class="thickbox button-secondary wpv_add_taxonomy_fields_button" type="button" value="<?php echo __('Add field', 'wpv-views'); ?>" name="Add a taxonomy field" <?php if($show) {echo '';} else {echo ' style="display:none"';} ?>>
    
    <?php
        $show = $view_settings['query_type'][0] == 'posts' ? '' : 'style="display:none"';
    ?>
    <span id="wpv-layout-help-posts" <?php echo $show;?>><i><?php echo sprintf(__('Want to add complex fields? Learn about %susing View Templates to customize fields%s.', 'wpv-views'),
                                                                               '<a href="http://wp-types.com/user-guides/using-a-view-template-in-a-view-layout/" target="_blank">',
                                                                               ' &raquo;</a>'); ?></i></span>
    <?php
        $show = $view_settings['query_type'][0] == 'taxonomy' ? '' : 'style="display:none"';
    ?>
    <span id="wpv-layout-help-taxonomy" <?php echo $show;?>><i><?php echo sprintf(__('Want to display posts that belong to this taxonomy? Learn about %sinserting child Views to Taxonomy Views%s.', 'wpv-views'),
                                                                                  '<a href="http://wp-types.com/user-guides/using-a-child-view-in-a-taxonomy-view-layout/" target="_blank">',
                                                                                  ' &raquo;</a>'); ?></i></span>

    <?php
        // Warn if Types is less than 1.0.2
        // We need at least 1.0.2 for the Types popups to work when adding fields.
        if (defined('WPCF_VERSION') && version_compare(WPCF_VERSION, '1.0.2', '<')) {
            echo '<br /><p style="color:red;"><strong>';
            _e('* Views requires Types 1.0.2 or greater for best results when adding fields.', 'wpv-views');
            echo '</strong></p>';
        }
    ?>

    
    <?php
}

function view_layout_javascript() {
    global $pagenow;
    ?>
    
    <script type="text/javascript">
    
        var wpv_url = '<?php echo WPV_URL; ?>';
        var wpv_field_text = '<?php _e('Field', 'wpv-views'); ?> - ';
        var wpv_confirm_layout_change = '<?php _e("Are you sure you want to change the layout?", 'wpv-views'); echo "\\n\\n"; _e("It appears that you made modifications to the layout.", 'wpv-views'); ?>';
        var no_post_results_text = "[wpv-no-posts-found][wpml-string context=\"wpv-views\"]<strong>No posts found</strong>[/wpml-string][/wpv-no-posts-found]";
        var no_taxonomy_results_text = "[wpv-no-taxonomy-found][wpml-string context=\"wpv-views\"]<strong>No taxonomy found</strong>[/wpml-string][/wpv-no-taxonomy-found]";
        
    </script>
    
    <?php
}

function view_layout_additional_js($post, $view_layout_settings) {
    $js = isset($view_layout_settings['additional_js']) ? strval($view_layout_settings['additional_js']) : '';
    ?>
    <br /><br />
    <fieldset><legend><?php _e('Additional Javascript files to be loaded with this View (comma separated): ', 'wpv-views'); ?></legend>
    <input type="text" name="_wpv_layout_settings[additional_js]" style="width:100%;" value="<?php echo $js; ?>" />
    </fieldset>
    <?php
}
    

function save_view_layout_settings($post_id) {
    if(isset($_POST['_wpv_layout_settings'])){
        if (!isset($_POST['_wpv_layout_settings']['fields'])) {
            $_POST['_wpv_layout_settings']['fields'] = array();
        }
        $fields = $_POST['_wpv_layout_settings']['fields'];
        
        foreach ($fields as $index => $value) {
            if (strpos($index, 'name_') === 0) {
                if (strpos($value, __('Field', 'wpv-views') . ' - ') === 0) {
                    $fields[$index] = 'wpv-post-field' . ' - ' . $value;
                } else if (strpos($value, 'types-field') === 0) {
                    // do nothing.
                } else if (strpos($value, 'Types - ') === 0) {
                    $fields[$index] = 'types-field' . ' - ' . $value;
                } else if (strpos($value, 'Taxonomy - ') === 0) {
                	$fields[$index] = substr($value, 11);
                } else {
                    $fields[$index] = wpv_get_shortcode($value);
                    if ($fields[$index] == 'wpv-post-body') {
                        $row = substr($index, 5);
                        if (isset($_POST['views_template_' . $row]) && $_POST['views_template_' . $row] != 0) {
                            $fields[$index] .= ' ' . $_POST['views_template_' . $row];
                        }
                    }

                    // Check for a taxonomy view (A view for laying out the child terms)
                    if ($fields[$index] == 'wpv-view') {
                        $row = substr($index, 5);
                        if (isset($_POST['taxonomy_view_' . $row]) && $_POST['taxonomy_view_' . $row] != 0) {
                            $fields[$index] .= ' ' . $_POST['taxonomy_view_' . $row];
                        }
                    }
                }
            } else if (strpos($index, 'suffix_') == 0 || strpos($index, 'prefix_') == 0) {
                $fields[$index] = htmlspecialchars_decode($value);
            }
        }
        $_POST['_wpv_layout_settings']['fields'] = $fields;
        
        if (!isset($_POST['_wpv_layout_settings']['include_field_names'])) {
            // set it to 0 if it's not in the $_POST data
            $_POST['_wpv_layout_settings']['include_field_names'] = 0;
        }
        
        update_post_meta($post_id, '_wpv_layout_settings', $_POST['_wpv_layout_settings']);
    }
}

function short_code_menu_callback($index, $cf_key, $function_name, $menu, $shortcode) {
    global $link_layout_number, $wpdb;
    static $fields_started = false;
    static $templates_started = false;

    if ($menu == __('View template', 'wpv-views') && !$templates_started) {
        $templates_started = true;
        echo '</tr><td>&nbsp;</td><tr>' ;
        echo '</tr><tr><td><strong>' . __('View templates', 'wpv-views') . '</strong></td>' ;
        echo '</tr><tr>' ;
        $link_layout_number = 0;
    }
    
    if ($menu != '' && $menu != __('View template', 'wpv-views') && !$fields_started) {
        $fields_started = true;
        echo '</tr><td>&nbsp;</td><tr>' ;
        echo '</tr><tr><td><strong>' . __('Fields', 'wpv-views') . '</strong></td>' ;
        echo '</tr><tr>' ;
        $link_layout_number = 0;
    }
    
    if (!($link_layout_number % 2)) {
        if ($link_layout_number != 0) {
            echo '</tr><tr>' ;
        }
        
    }
 
    if ($menu == __('View template', 'wpv-views')) {

        // get the View template title.
        $field_name = $wpdb->get_var($wpdb->prepare("SELECT post_title FROM {$wpdb->posts} WHERE post_type='view-template' AND post_name=%s", $cf_key));
        if (!$field_name) {
            $field_name = $cf_key;
        }
        
    } else {
        $field_name = $cf_key;
        if (function_exists('wpcf_types_get_meta_prefix')) {
            // we have types.
            // Get the field name for display
            $types_prefix = wpcf_types_get_meta_prefix();
            if (strpos($cf_key, $types_prefix) === 0) {
                $field_info = wpcf_fields_get_field_by_slug(substr($cf_key, strlen($types_prefix)));
                
                if (isset($field_info['name'])) {
                    $field_name = $field_info['name'];
                }
            }
        }
    }
    
    echo '<td><a style="cursor: pointer" onclick="on_add_field_wpv(\''. $menu . '\', \'' . esc_js($cf_key) . '\', \'' . base64_encode($field_name) . '\')">';
    echo $field_name;
    echo '</a></td>';
    
    $link_layout_number++;
}

function short_code_taxonomy_menu_callback($index, $cf_key, $function_name, $menu, $shortcode) {
    global $link_layout_number;
    
    static $taxonomy_view_started = false;
    static $post_view_started = false;
    static $suffix = '';
    
    if (!$taxonomy_view_started && $menu == __('Taxonomy View', 'wpv-views')) {
        echo '</tr><tr><td></td>';
        echo '</tr><tr><td></td></tr><tr>';
        echo '<td colspan="2"><strong>' . $menu . '</strong> ' . __(' - Use to layout child taxonomy terms', 'wpv-views') . '</td>';
        echo '</tr><tr>';
        $link_layout_number = 0;
        $taxonomy_view_started = true;
        $suffix = ' - ' . __('Taxonomy View', 'wpv-views');
    }

    if (!$post_view_started && $menu == __('Post View', 'wpv-views')) {
        echo '</tr><tr><td></td>';
        echo '</tr><tr><td></td></tr><tr>';
        echo '<td colspan="2"><strong>' . $menu . '</strong> ' . __(' - Use to layout posts for the current taxonomy term', 'wpv-views') . '</td>';
        echo '</tr><tr>';
        $link_layout_number = 0;
        $post_view_started = true;
        $suffix = ' - ' . __('Post View', 'wpv-views');
    }
    
    if (!($link_layout_number % 2)) {
        if ($link_layout_number != 0) {
            echo '</tr><tr>' ;
        }
        
    }
    echo '<td><a style="cursor: pointer" onclick="on_add_field_wpv(\''. $menu . '\', \'' . esc_js($cf_key) . '\', \'' . base64_encode($cf_key . $suffix) . '\')">';
    echo $cf_key;
    echo '</a></td>';
    
    $link_layout_number++;
}

function short_code_variable_callback($index, $cf_key, $function_name, $menu, $shortcode) {
    ?>
        wpv_shortcodes[<?php echo $index?>] = new Array('<?php echo esc_js($cf_key);?>', '<?php echo esc_js($shortcode); ?>');
    <?php
}


function add_views_layout_js() {
	wp_enqueue_script( 'views-layout-script' , WPV_URL . '/res/js/views_layout.js', array('jquery', 'jquery-ui-sortable', 'jquery-ui-draggable'), WPV_VERSION);
	wp_enqueue_script( 'views-layout-meta-html-script' , WPV_URL . '/res/js/views_layout_meta_html.js', array('jquery'), WPV_VERSION);
}

