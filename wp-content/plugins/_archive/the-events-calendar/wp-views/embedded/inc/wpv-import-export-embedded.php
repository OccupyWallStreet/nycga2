<?php

function wpv_admin_import_form($file_name) {

    ?>

    <form name="View_import" enctype="multipart/form-data" action="" method="post">
        <?php if ($file_name != ''): ?>
            <h2><?php _e('Import Views and View Templates for your Theme',
                    'wpv-views'); ?></h2>
        <?php else: ?>
            <h2><?php _e('Import Views and View Templates',
                'wpv-views'); ?></h2>
    <?php endif; ?>

        <p><strong><?php _e('Settings:',
            'wpv-views'); ?></strong></p>
        <ul style="margin-left:10px">
            <li>
                <input id="checkbox-1" type="checkbox" name="views-overwrite" />
                <label for="checkbox-1"><?php _e('Bulk overwrite if View exists',
            'wpv-views'); ?></label>
            </li>
            <li>
                <input id="checkbox-2" type="checkbox" name="views-delete" />
                <label for="checkbox-2"><?php _e('Delete any existing Views that are not in the import',
            'wpv-views'); ?></label>
            </li>
            <li>
                <input id="checkbox-3" type="checkbox" name="view-templates-overwrite" />
                <label for="checkbox-3"><?php _e('Bulk overwrite if View Template exists',
            'wpv-views'); ?></label>
            </li>
            <li>
                <input id="checkbox-4" type="checkbox" name="view-templates-delete" />
                <label for="checkbox-4"><?php _e('Delete any existing View Templates that are not in the import',
            'wpv-views'); ?></label>
            </li>
            <li>
                <input id="checkbox-5" type="checkbox" name="view-settings-overwrite" />
                <label for="checkbox-5"><?php _e('Overwrite Views settings',
            'wpv-views'); ?></label>
            </li>
        </ul>
        <label for="upload-views-file"><?php __('Select the views xml file to upload from your computer:&nbsp;',
            'wpv-views'); ?></label>

    <?php if ($file_name != ''): ?>
            <input type="hidden" id="upload-views-file" name="import-file" value="<?php echo $file_name; ?>" />
    <?php else: ?>
            <input type="file" id="upload-views-file" name="import-file" />
    <?php endif; ?>

        <input id="wpv-import" class="button-primary" type="submit" value="<?php _e('Import',
            'wpv-views'); ?>" name="import" />

        <input type="hidden" name="page" value="views-import-export" />
    <?php wp_nonce_field('wpv-import-nonce',
            'wpv-import-nonce'); ?>

    </form>

    <?php
}

$import_errors = null;
$import_messages = array();

function wpv_admin_menu_import_export_hook() {
    if (isset($_POST['export']) && $_POST['export'] == __('Export', 'wpv-views') &&
            isset($_POST['wpv-export-nonce']) &&
            wp_verify_nonce($_POST['wpv-export-nonce'], 'wpv-export-nonce')) {
        wpv_admin_export_data();
        die();
    }

    if (isset($_POST['import']) && $_POST['import'] == __('Import', 'wpv-views') &&
            isset($_POST['wpv-import-nonce']) &&
            wp_verify_nonce($_POST['wpv-import-nonce'], 'wpv-import-nonce')) {
        global $import_errors, $import_messages;
        $import_errors = wpv_admin_import_data();
        if ($import_errors) {
            add_action('admin_notices', 'wpv_import_errors');
        }
        if (sizeof($import_messages)) {
            add_action('admin_notices', 'wpv_import_messages');

            global $wpv_theme_import, $wpv_theme_import_xml;
            if (isset($wpv_theme_import) && $wpv_theme_import != '') {
                include $wpv_theme_import;

                update_option('views-embedded-import', $timestamp);
            }
        }
    }
}

function wpv_admin_import_data() {
    global $WP_Views;

    if (isset($_FILES['import-file'])) {
        $file = $_FILES['import-file'];
    } else {
        $file = null;
    }

    if ($file == null) {
        // check for import from settings.xml in theme
        if (isset($_POST['import-file'])) {
            $file = array();
            $file['name'] = $_POST['import-file'];
            $file['tmp_name'] = $_POST['import-file'];
            $file['size'] = filesize($file['tmp_name']);
        }
    }

    $data = array();
    $info = pathinfo($file['name']);
    $is_zip = $info['extension'] == 'zip' ? true : false;
    if ($is_zip) {
        $zip = zip_open(urldecode($file['tmp_name']));
        if (is_resource($zip)) {
            while (($zip_entry = zip_read($zip)) !== false) {
                if (zip_entry_name($zip_entry) == 'settings.xml') {
                    $data = @zip_entry_read($zip_entry,
                                    zip_entry_filesize($zip_entry));
                }
            }
        } else {
            return new WP_Error('could_not_open_file', __('Unable to open zip file', 'wpv-views'));
        }
    } else {
        $fh = fopen($file['tmp_name'], 'r');
        if ($fh) {
            $data = fread($fh, $file['size']);
            fclose($fh);
        }
    }
    
    if (!empty($data)) {

        if (!function_exists('simplexml_load_string')) {
            return new WP_Error('xml_missing', __('The Simple XML library is missing.',
                                    'wpv-views'));
        }
        $xml = simplexml_load_string($data);

        if (!$xml) {
            return new WP_Error('not_xml_file', sprintf(__('The XML file (%s) could not be read.',
                                            'wpv-views'), $file['name']));
        }

        $import_data = wpv_admin_import_export_simplexml2array($xml);

        // import view templates first.   
        $error = wpv_admin_import_view_templates($import_data);
        if ($error) {
            return $error;
        }

        // import views next.   
        $error = wpv_admin_import_views($import_data);
        if ($error) {
            return $error;
        }

        // import views next.   
        $error = wpv_admin_import_settings($import_data);
        if ($error) {
            return $error;
        }
    } else {
        return new WP_Error('could_not_open_file', __('Could not read the Views import file.',
                                'wpv-views'));
    }
}

function wpv_admin_import_view_templates($import_data) {

    global $wpdb, $import_messages;

    $imported_view_templates = array();
    $overwrite_count = 0;
    $new_count = 0;

    if (isset($import_data['view-templates']['view-template'])) {
        $view_templates = $import_data['view-templates']['view-template'];

        // check for a single view template
        if (!isset($view_templates[0])) {
            $view_templates = array($view_templates);
        }

        foreach ($view_templates as $view_template) {

			$output_mode = '';
			if (isset($view_template['template_mode'])) {
				$output_mode = $view_template['template_mode'];
				unset($view_template['template_mode']);
			}
			
            $post_to_update = $wpdb->get_var($wpdb->prepare(
                            "SELECT ID FROM $wpdb->posts
                                WHERE post_name = %s AND post_type = %s",
                            $view_template['post_name'], 'view-template'));

            $id = 0;
			if ($post_to_update) {
                $imported_view_templates[] = $post_to_update;

                // only update if we have overwrite enabled.
                if (isset($_POST['view-templates-overwrite']) && $_POST['view-templates-overwrite'] == 'on') {
                    $overwrite_count++;
                    $view_template['ID'] = $post_to_update;
                    $id = wp_update_post($view_template);
                    if (!$id) {
                        return new WP_Error('could_not_update_post', sprintf(__('Failed to update view template - %s.',
                                                        'wpv-views'),
                                                $view_template['post_name']));
                    }
                }
            } else {
                // it's a new view template
                $new_count++;
                unset($view_template['ID']);
                $id = wp_insert_post($view_template, true);
                if (is_object($id)) {
                    // it's an WP_Error object.
                    return $id;
                }
                $imported_view_templates[] = $id;
            }
			
			if ($id && $output_mode != '') {
				
                update_post_meta($id, '_wpv_view_template_mode', $output_mode);
				
			}
        }
    }

    $deleted_count = 0;
    if (isset($_POST['view-templates-delete']) && $_POST['view-templates-delete'] == 'on') {
        $view_templates_to_delete = get_posts('post_type=view-template&post_status=any&posts_per_page=-1');
        if (!empty($view_templates_to_delete)) {
            foreach ($view_templates_to_delete as $view_template_to_delete) {
                if (!in_array($view_template_to_delete->ID,
                                $imported_view_templates)) {
                    wp_delete_post($view_template_to_delete->ID, true);
                    $deleted_count++;
                }
            }
        }
    }

    $import_messages[] = sprintf(__('%d View Templates found in the file. %d have been created and %d have been over written.',
                    'wpv-views'), sizeof($imported_view_templates), $new_count,
            $overwrite_count);

    if ($deleted_count) {
        $import_messages[] = sprintf(__('%d existing View Templates were deleted.',
                        'wpv-views'), $deleted_count);
    }

    return false; // no errors
}

function wpv_admin_import_views($import_data) {

    global $wpdb, $import_messages, $WP_Views;

    $imported_views = array();
    $overwrite_count = 0;
    $new_count = 0;

    if (isset($import_data['views']['view'])) {
        $views = $import_data['views']['view'];

        // check for a single view
        if (!isset($views[0])) {
            $views = array($views);
        }


        foreach ($views as $view) {

            $meta = $view['meta'];
            unset($view['meta']);

            if (isset($meta['_wpv_settings'])) {
                $meta['_wpv_settings'] = $WP_Views->convert_names_to_ids_in_settings($meta['_wpv_settings']);
            }
            if (isset($meta['_wpv_layout_settings'])) {
                $meta['_wpv_layout_settings'] = $WP_Views->convert_names_to_ids_in_layout_settings($meta['_wpv_layout_settings']);
            }

            $post_to_update = $wpdb->get_var($wpdb->prepare(
                            "SELECT ID FROM $wpdb->posts
                                WHERE post_name = %s AND post_type = %s",
                            $view['post_name'], 'view'));

            if ($post_to_update) {
                $imported_views[] = $post_to_update;

                // only update if we have overwrite enabled.
                if (isset($_POST['views-overwrite']) && $_POST['views-overwrite'] == 'on') {
                    $overwrite_count++;
                    $view['ID'] = $post_to_update;
                    $id = wp_update_post($view);
                    if (!$id) {
                        return new WP_Error('could_not_update_post', sprintf(__('Failed to update view - %s.',
                                                        'wpv-views'),
                                                $view['post_name']));
                    }
                    if (isset($meta['_wpv_settings'])) {
                        update_post_meta($id, '_wpv_settings',
                                $meta['_wpv_settings']);
                    }
                    if (isset($meta['_wpv_layout_settings'])) {
                        update_post_meta($id, '_wpv_layout_settings',
                                $meta['_wpv_layout_settings']);
                    }
                }
            } else {
                // it's a new view template
                $new_count++;
                unset($view['ID']);
                $id = wp_insert_post($view, true);
                if (is_object($id)) {
                    // it's an WP_Error object.
                    return $id;
                }
                $imported_views[] = $id;

                if (isset($meta['_wpv_settings'])) {
                    update_post_meta($id, '_wpv_settings',
                            $meta['_wpv_settings']);
                }
                if (isset($meta['_wpv_layout_settings'])) {
                    update_post_meta($id, '_wpv_layout_settings',
                            $meta['_wpv_layout_settings']);
                }
            }
        }
    }

    $deleted_count = 0;
    if (isset($_POST['views-delete']) && $_POST['views-delete'] == 'on') {
        $views_to_delete = get_posts('post_type=view&post_status=any&posts_per_page=-1');
        if (!empty($views_to_delete)) {
            foreach ($views_to_delete as $view_to_delete) {
                if (!in_array($view_to_delete->ID, $imported_views)) {
                    wp_delete_post($view_to_delete->ID, true);
                    $deleted_count++;
                }
            }
        }
    }

    $import_messages[] = sprintf(__('%d Views found in the file. %d have been created and %d have been over written.',
                    'wpv-views'), sizeof($imported_views), $new_count,
            $overwrite_count);

    if ($deleted_count) {
        $import_messages[] = sprintf(__('%d existing Views were deleted.',
                        'wpv-views'), $deleted_count);
    }

    return false; // no errors
}

function wpv_admin_import_settings($data) {
    global $WP_Views, $import_messages, $wpdb;
    if (isset($_POST['view-settings-overwrite'])) {
        $options = $WP_Views->get_options();
        // Reset options
        foreach ($options as $option_name => $option_value) {
            if (is_numeric($option_value)) {
                $options[$option_name] = 0;
            } else {
                $options[$option_name] = '';
            }
        }
        // Set exported options
        if (!empty($data['settings'])) {
            foreach ($data['settings'] as $option_name => $option_value) {
                if (strpos($option_name, 'view_') === 0
                        || strpos($option_name, 'views_template_') === 0) {
                    $post_type = strpos($option_name, 'view_') === 0 ? 'view' : 'view-template';
                    
                    if ($option_value) {
						$post_id = $wpdb->get_var($wpdb->prepare(
                                    "SELECT ID FROM $wpdb->posts
                                        WHERE post_name = %s AND post_type = %s",
                                    $option_value, $post_type));
					} else {
						$post_id = 0;
					}
                    
                    if ($post_id) {
                        $options[$option_name] = $post_id;
                    } else {
                        $options[$option_name] = 0;
						if ($option_value) {
							$import_messages[] = sprintf(__('%s could not be found', 'wpv-views'), $post_type . ' ' . $option_value);
						}
                    }
                } else {
                    $options[$option_name] = $option_value;
                }
            }
        }
        $WP_Views->save_options($options);
        $import_messages[] = __('Settings updated', 'wpv-views');
    }
    return false; // no errors
}

function wpv_import_errors() {
    global $import_errors;

    ?>
    <div class="message error"><p><?php echo $import_errors->get_error_message() ?></p></div>
    <?php
}

function wpv_import_messages() {
    global $import_messages;

    foreach ($import_messages as $message) {

        ?>
        <div class="message updated"><p><?php echo $message ?></p></div>
        <?php
    }
}

/**
 * Loops over elements and convert to array or empty string.
 * 
 * @param type $element
 * @return string 
 */
function wpv_admin_import_export_simplexml2array($element) {
    $element = is_string($element) ? trim($element) : $element;
    if (!empty($element) && is_object($element)) {
        $element = (array) $element;
    }
    if (empty($element)) {
        $element = '';
    } else if (is_array($element)) {
        foreach ($element as $k => $v) {
            $v = is_string($v) ? trim($v) : $v;
            if (empty($v)) {
                $element[$k] = '';
                continue;
            }
            $add = wpv_admin_import_export_simplexml2array($v);
            if (!empty($add)) {
                $element[$k] = $add;
            } else {
                $element[$k] = '';
            }
        }
    }

    if (empty($element)) {
        $element = '';
    }

    return $element;
}
