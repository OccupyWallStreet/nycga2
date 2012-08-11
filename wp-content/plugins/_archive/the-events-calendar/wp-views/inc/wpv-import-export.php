<?php

function wpv_admin_menu_import_export() {

    
    ?>    
    <div class="wrap">

        <div id="icon-views" class="icon32"><br /></div>
        <h2><?php _e('Views Import / Export', 'wpv-views') ?></h2>

        <br />
        <form name="View_export" action="<?php echo admin_url('edit.php'); ?>" method="post">
            <h2><?php _e('Export Views and View Templates', 'wpv-views'); ?></h2>
            <p><?php _e('Download all Views and View Templates', 'wpv-views'); ?></p>
            
            <p><strong><?php _e('When importing to theme:', 'wpv-views'); ?></strong></p>
            <ul style="margin-left:10px">
                <li>
                    <input id="radio-1" type="radio" value="ask" name="import-mode" checked="checked" />
                    <label for="radio-1"><?php _e('ask user for approval', 'wpv-views'); ?></label>
                </li>
                <li>
                    <input id="radio-2" type="radio" value="auto" name="import-mode" />
                    <label for="radio-2"><?php _e('import automatically', 'wpv-views'); ?></label>
                </li>
            </ul>
            <p><strong><?php _e('Affiliate details for theme designers:', 'wpv-views'); ?></strong></p>
            <table style="margin-left:10px">
                <tr>
                    <td><?php _e('Affiliate ID:', 'wpv-views'); ?></td><td><input type="text" name="aid" id="aid" style="width:200px;" /></td>
                </tr>
                <tr>
                    <td><?php _e('Affiliate Key:', 'wpv-views'); ?></td><td><input type="text" name="akey" id="akey" style="width:200px;" /></td>
                </tr>
            </table>
            <p style="margin-left:10px">
            <?php _e('You only need to enter affiliate settings if you are a theme designer and want to receive affiliate commission.', 'wpv-views'); ?>
            <br />
            <?php echo sprintf(__('Log into your account at <a href="%s">%s</a> and go to <a href="%s">%s</a> for details.', 'wpv-views'), 
                                    'http://wp-types.com',
                                    'http://wp-types.com',
                                    'http://wp-types.com/shop/account/?acct=affiliate',
                                    'http://wp-types.com/shop/account/?acct=affiliate'); ?>
            </p>
            
            <br /> 
            <input id="wpv-export" class="button-primary" type="submit" value="<?php _e('Export', 'wpv-views'); ?>" name="export" />
            
            <?php wp_nonce_field('wpv-export-nonce', 'wpv-export-nonce'); ?>

        </form>
        
        <hr />
        
        <?php wpv_admin_import_form(''); ?>
        
    </div>
    
    <?php
    
}

/**
 * Exports data to XML.
 */
function wpv_admin_export_data() {
    global $WP_Views;
    
    require_once WPV_PATH_EMBEDDED . '/common/array2xml.php';
    $xml = new ICL_Array2XML();
    $data = array();

    // Get the views
    $views = get_posts('post_type=view&post_status=any&posts_per_page=-1');
    if (!empty($views)) {
        $data['views'] = array('__key' => 'view');
        foreach ($views as $key => $post) {
            $post = (array) $post;
            if ($post['post_name']) {
                $post_data = array();
                $copy_data = array('ID', 'post_content', 'post_title', 'post_name',
                    'post_excerpt', 'post_type', 'post_status');
                foreach ($copy_data as $copy) {
                    if (isset($post[$copy])) {
                        $post_data[$copy] = $post[$copy];
                    }
                }
                $data['views']['view-' . $post['ID']] = $post_data;
                $meta = get_post_custom($post['ID']);
                if (!empty($meta)) {
                    $data['view']['view-' . $post['ID']]['meta'] = array();
                    foreach ($meta as $meta_key => $meta_value) {
                        if ($meta_key == '_wpv_settings') {
                            $value = maybe_unserialize($meta_value[0]);
                            $value = $WP_Views->convert_ids_to_names_in_settings($value);
                            
                            $data['views']['view-' . $post['ID']]['meta'][$meta_key] = $value;
                        }
                        if ($meta_key == '_wpv_layout_settings') {
                            $value = maybe_unserialize($meta_value[0]);
                            $value = $WP_Views->convert_ids_to_names_in_layout_settings($value);
                            $data['views']['view-' . $post['ID']]['meta'][$meta_key] = $value;
                        }
                    }
                    if (empty($data['views']['view-' . $post['ID']]['meta'])) {
                        unset($data['views']['view-' . $post['ID']]['meta']);
                    }
                }
            }
        }
    }

    // Get the view templates
    $view_templates = get_posts('post_type=view-template&post_status=any&posts_per_page=-1');
    if (!empty($view_templates)) {
        $data['view-templates'] = array('__key' => 'view-template');
        foreach ($view_templates as $key => $post) {
            $post = (array) $post;
            if ($post['post_name']) {
                $post_data = array();
                $copy_data = array('ID', 'post_content', 'post_title', 'post_name',
                    'post_excerpt', 'post_type', 'post_status');
                foreach ($copy_data as $copy) {
                    if (isset($post[$copy])) {
                        $post_data[$copy] = $post[$copy];
                    }
                }
                $output_mode = get_post_meta($post['ID'], '_wpv_view_template_mode', true);
                
                $post_data['template_mode'] = $output_mode;

                $data['view-templates']['view-template-' . $post['ID']] = $post_data;
            }
        }
    }
    
    // Get settings
    $options = $WP_Views->get_options();
    if (!empty($options)) {
        foreach ($options as $option_name => $option_value) {
            if (strpos($option_name, 'view_') === 0
                    || strpos($option_name, 'views_template_') === 0) {
                $post = get_post($option_value);
                if (!empty($post)) {
                    $options[$option_name] = $post->post_name;
                }
            }
        }
        $data['settings'] = $options;
    }


    // Offer for download
    $data = $xml->array2xml($data, 'views');

    $sitename = sanitize_key(get_bloginfo('name'));
    if (!empty($sitename)) {
        $sitename .= '.';
    }
    $filename = $sitename . 'views.' . date('Y-m-d') . '.xml';
    $code = "<?php\r\n";
    $code .= '$timestamp = ' . time() . ';' . "\r\n";
    $code .= '$auto_import = ';
    $code .=  (isset($_POST['import-mode']) && $_POST['import-mode'] == 'ask') ? 0 : 1;
    $code .= ';' . "\r\n";
    if (isset($_POST['aid']) && $_POST['aid'] != '' && isset($_POST['akey']) && $_POST['aid'] != '') {
        $code .= '$affiliate_id="' . $_POST['aid'] . '";' . "\r\n";
        $code .= '$affiliate_key="' . $_POST['akey'] . '";' . "\r\n";
    }
    $code .= "\r\n?>";

    if (class_exists('ZipArchive')) { 
        $zipname = $sitename . 'views.' . date('Y-m-d') . '.zip';
        $zip = new ZipArchive();
        $file = tempnam("tmp", "zip");
        $zip->open($file, ZipArchive::OVERWRITE);
    
        $res = $zip->addFromString('settings.xml', $data);
        $zip->addFromString('settings.php', $code);
        $zip->close();
        $data = file_get_contents($file);
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=" . $zipname);
        header("Content-Type: application/zip");
        header("Content-length: " . strlen($data) . "\n\n");
        header("Content-Transfer-Encoding: binary");
        echo $data;
        unlink($file);
        die();
    } else {
        // download the xml.
        
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=" . $filename);
        header("Content-Type: application/xml");
        header("Content-length: " . strlen($data) . "\n\n");
        echo $data;
        die();
    }
}

