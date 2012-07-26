<?php
add_filter('wpcf_fields_type_image_value_get', 'wpcf_fields_image_value_filter');
add_filter('wpcf_fields_type_image_value_save', 'wpcf_fields_image_value_filter');

/**
 * Register data (called automatically).
 * @return type 
 */
function wpcf_fields_image() {
    return array(
        'id' => 'wpcf-image',
        'title' => __('Image', 'wpcf'),
        'description' => __('Image', 'wpcf'),
        'validate' => array('required'),
        'meta_box_js' => array(
            'wpcf-jquery-fields-file' => array(
                'inline' => 'wpcf_fields_file_meta_box_js_inline',
            ),
            'wpcf-jquery-fields-image' => array(
                'inline' => 'wpcf_fields_image_meta_box_js_inline',
            ),
        ),
        'inherited_field_type' => 'file',
    );
}

/**
 * Renders inline JS.
 */
function wpcf_fields_image_meta_box_js_inline() {
    global $post;

    ?>
    <script type="text/javascript">
        //<![CDATA[
        jQuery(document).ready(function(){
            wpcf_formfield = false;
            jQuery('.wpcf-fields-image-upload-link').live('click', function() {
                wpcf_formfield = '#'+jQuery(this).attr('id')+'-holder';
                tb_show('<?php
    echo esc_js(__('Upload image', 'wpcf'));

    ?>', 'media-upload.php?post_id=<?php echo $post->ID; ?>&type=image&wpcf-fields-media-insert=1&TB_iframe=true');
                return false;
            }); 
        });
        //]]>
    </script>
    <?php
}

/**
 * Editor callback form.
 */
function wpcf_fields_image_editor_callback() {
    wp_enqueue_style('wpcf-fields-image',
            WPCF_EMBEDDED_RES_RELPATH . '/css/basic.css', array(), WPCF_VERSION);
    wp_enqueue_script('jquery');

    // Get field
    $field = wpcf_admin_fields_get_field($_GET['field_id']);
    if (empty($field)) {
        _e('Wrong field specified', 'wpcf');
        die();
    }

    // Get post_ID
    $post_ID = false;
    if (isset($_POST['post_id'])) {
        $post_ID = intval($_POST['post_id']);
    } else {
        $http_referer = explode('?', $_SERVER['HTTP_REFERER']);
        if (isset($http_referer[1])) {
            parse_str($http_referer[1], $http_referer);
            if (isset($http_referer['post'])) {
                $post_ID = $http_referer['post'];
            }
        }
    }

    // Get attachment
    $image = false;
    $attachment_id = false;
    if ($post_ID) {
        $image = get_post_meta($post_ID,
                wpcf_types_get_meta_prefix($field) . $field['slug'], true);
        if (!empty($image)) {
            // Get attachment by guid
            global $wpdb;
            $attachment_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM {$wpdb->posts}
    WHERE post_type = 'attachment' AND guid=%s",
                            $image));
        }
    }

    // Get post type
    $post_type = '';
    if ($post_ID) {
        $post_type = get_post_type($post_ID);
    } else {
        $http_referer = explode('?', $_SERVER['HTTP_REFERER']);
        parse_str($http_referer[1], $http_referer);
        if (isset($http_referer['post_type'])) {
            $post_type = $http_referer['post_type'];
        }
    }

    $image_data = wpcf_fields_image_get_data($image);

    if (!in_array($post_type, array('view', 'view-template'))) {
        // We must ignore errors here and treat image as outsider
        if (!empty($image_data['error'])) {
            $image_data['is_outsider'] = 1;
            $image_data['is_attachment'] = 0;
        }
    } else {
        if (!empty($image_data['error'])) {
            $image_data['is_outsider'] = 0;
            $image_data['is_attachment'] = 0;
        }
    }

    $last_settings = wpcf_admin_fields_get_field_last_settings($_GET['field_id']);

    $form = array();
    $form['#form']['callback'] = 'wpcf_fields_image_editor_submit';
    if ($attachment_id) {
        $form['preview'] = array(
            '#type' => 'markup',
            '#markup' => '<div style="position:absolute; margin-left:300px;">'
            . wp_get_attachment_image($attachment_id, 'thumbnail') . '</div>',
        );
    }
    $alt = '';
    $title = '';
    if ($attachment_id) {
        $alt = trim(strip_tags(get_post_meta($attachment_id,
                                '_wp_attachment_image_alt', true)));
        $attachment_post = get_post($attachment_id);
        if (!empty($attachment_post)) {
            $title = trim(strip_tags($attachment_post->post_title));
        } else if (!empty($alt)) {
            $title = $alt;
        }
        if (empty($alt)) {
            $alt = $title;
        }
    }
    $form['title'] = array(
        '#type' => 'textfield',
        '#title' => __('Image title', 'wpcf'),
        '#description' => __('Title text for the image, e.g. &#8220;The Mona Lisa&#8221;',
                'wpcf'),
        '#name' => 'title',
        '#value' => isset($last_settings['title']) ? $last_settings['title'] : $title,
    );
    $form['alt'] = array(
        '#type' => 'textfield',
        '#title' => __('Alternate Text', 'wpcf'),
        '#description' => __('Alt text for the image, e.g. &#8220;The Mona Lisa&#8221;',
                'wpcf'),
        '#name' => 'alt',
        '#value' => isset($last_settings['alt']) ? $last_settings['alt'] : $alt,
    );
    $form['alignment'] = array(
        '#type' => 'radios',
        '#title' => __('Alignment', 'wpcf'),
        '#name' => 'alignment',
        '#default_value' => isset($last_settings['alignment']) ? $last_settings['alignment'] : 'none',
        '#options' => array(
            __('None', 'wpcf') => 'none',
            __('Left', 'wpcf') => 'left',
            __('Center', 'wpcf') => 'center',
            __('Right', 'wpcf') => 'right',
        ),
    );
    $form['class'] = array(
        '#type' => 'textfield',
        '#title' => __('Class', 'wpcf'),
        '#name' => 'class',
        '#value' => isset($last_settings['class']) ? $last_settings['class'] : '',
    );
    $form['style'] = array(
        '#type' => 'textfield',
        '#title' => __('Style', 'wpcf'),
        '#name' => 'style',
        '#value' => isset($last_settings['style']) ? $last_settings['style'] : '',
    );

    if (!in_array($post_type, array('view', 'view-template'))) {
        $attributes_outsider = $image_data['is_outsider'] ? array('disabled' => 'disabled') : array();
        $attributes_attachment = !$image_data['is_attachment'] ? array('disabled' => 'disabled') : array();
    } else {
        $attributes_outsider = array();
        $attributes_attachment = array();
    }

    if (!in_array($post_type, array('view', 'view-template')) && $image_data['is_outsider']) {
        $form['notice'] = array(
            '#type' => 'markup',
            '#markup' => '<div class="message error" style="margin:0 0 20px 0;"><p>'
            . __('Types can only resize images that you upload to this site and not images from other domains.',
                    'wpcf')
            . '</p></div>',
        );
    } else if ($image_data['is_outsider']) {
        $form['notice'] = array(
            '#type' => 'markup',
            '#markup' => '<div class="message error" style="margin:0 0 20px 0;"><p>'
            . __('Types will be able to resize images that are uploaded to the post. If you specify URLs of images on other sites, Types will not resize them.',
                    'wpcf')
            . '</p></div>',
        );
    }
    if ($image_data['is_attachment']) {
        $default_value = isset($last_settings['image-size']) ? $last_settings['image-size'] : 'thumbnail';
    } else if (!$image_data['is_outsider']) {
        $default_value = 'wpcf-custom';
    } else {
        $default_value = 'thumbnail';
    }
    $form['size'] = array(
        '#type' => 'radios',
        '#title' => __('Pre-defined sizes', 'wpcf'),
        '#name' => 'image-size',
        '#default_value' => $default_value,
        '#options' => array(
            'thumbnail' => array('#title' => __('Thumbnail', 'wpcf'), '#value' => 'thumbnail', '#attributes' => $attributes_attachment),
            'medium' => array('#title' => __('Medium', 'wpcf'), '#value' => 'medium', '#attributes' => $attributes_attachment),
            'large' => array('#title' => __('Large', 'wpcf'), '#value' => 'large', '#attributes' => $attributes_attachment),
            'full' => array('#title' => __('Full Size', 'wpcf'), '#value' => 'full', '#attributes' => $attributes_attachment),
            'wpcf-custom' => array('#title' => __('Custom size', 'wpcf'), '#value' => 'wpcf-custom', '#attributes' => $attributes_outsider),
        ),
    );
    $form['toggle-open'] = array(
        '#type' => 'markup',
        '#markup' => '<div id="wpcf-toggle" style="display:none;">',
    );
    $form['width'] = array(
        '#type' => 'textfield',
        '#title' => __('Width', 'wpcf'),
        '#description' => __('Specify custom width', 'wpcf'),
        '#name' => 'width',
        '#value' => isset($last_settings['width']) ? $last_settings['width'] : '',
        '#suffix' => '&nbsp;px',
        '#attributes' => $attributes_outsider,
    );
    $form['height'] = array(
        '#type' => 'textfield',
        '#title' => __('Height', 'wpcf'),
        '#description' => __('Specify custom height', 'wpcf'),
        '#name' => 'height',
        '#value' => isset($last_settings['height']) ? $last_settings['height'] : '',
        '#suffix' => '&nbsp;px',
        '#attributes' => $attributes_outsider,
    );
    $form['proportional'] = array(
        '#type' => 'checkbox',
        '#title' => __('Keep proportional', 'wpcf'),
        '#name' => 'proportional',
        '#default_value' => 1,
        '#attributes' => $attributes_outsider,
    );
    $form['toggle-close'] = array(
        '#type' => 'markup',
        '#markup' => '</div>',
        '#attributes' => $attributes_outsider,
    );
    if ($post_ID) {
        $form['post_id'] = array(
            '#type' => 'hidden',
            '#name' => 'post_id',
            '#value' => $post_ID,
        );
    }
    $form['submit'] = array(
        '#type' => 'submit',
        '#name' => 'submit',
        '#value' => __('Insert shortcode', 'wpcf'),
        '#attributes' => array('class' => 'button-primary'),
    );
    $f = wpcf_form('wpcf-form', $form);
    wpcf_admin_ajax_head('Insert email', 'wpcf');
    echo '<form method="post" action="">';
    echo $f->renderForm();
    echo '</form>';

    ?>
    <script type="text/javascript">
        //<![CDATA[
        jQuery(document).ready(function(){
            jQuery('input:radio[name="image-size"]').change(function(){
                if (jQuery(this).val() == 'wpcf-custom') {
                    jQuery('#wpcf-toggle').slideDown();
                } else {
                    jQuery('#wpcf-toggle').slideUp();
                }
            });
            if (jQuery('input:radio[name="image-size"]:checked').val() == 'wpcf-custom') {
                jQuery('#wpcf-toggle').show();
            }
        });
        //]]>
    </script>
    <?php
    wpcf_admin_ajax_footer();
}

/**
 * Editor callback form submit.
 */
function wpcf_fields_image_editor_submit() {
    $add = '';
    if (!empty($_POST['alt'])) {
        $add .= ' alt="' . strval($_POST['alt']) . '"';
    }
    if (!empty($_POST['title'])) {
        $add .= ' title="' . strval($_POST['title']) . '"';
    }
    $size = !empty($_POST['image-size']) ? $_POST['image-size'] : false;
    if ($size == 'wpcf-custom') {
        if (!empty($_POST['width'])) {
            $add .= ' width="' . intval($_POST['width']) . '"';
        }
        if (!empty($_POST['height'])) {
            $add .= ' height="' . intval($_POST['height']) . '"';
        }
        if (!empty($_POST['proportional'])) {
            $add .= ' proportional="true"';
        }
    } else if (!empty($size)) {
        $add .= ' size="' . $size . '"';
    }
    if (!empty($_POST['alignment'])) {
        $add .= ' align="' . $_POST['alignment'] . '"';
    }
    if (!empty($_POST['class'])) {
        $add .= ' class="' . $_POST['class'] . '"';
    }
    if (!empty($_POST['style'])) {
        $add .= ' style="' . $_POST['style'] . '"';
    }
    $field = wpcf_admin_fields_get_field($_GET['field_id']);
    if (!empty($field)) {
        $shortcode = wpcf_fields_get_shortcode($field, $add);
        wpcf_admin_fields_save_field_last_settings($_GET['field_id'], $_POST);
        echo editor_admin_popup_insert_shortcode_js($shortcode);
        die();
    }
}

/**
 * View function.
 * 
 * @param type $params 
 */
function wpcf_fields_image_view($params) {
    $output = '';
    $alt = false;
    $title = false;
    $class = array();
    $style = array();

    // Get image data
    $image_data = wpcf_fields_image_get_data($params['field_value']);

    // Display error to admin only
    if (!empty($image_data['error'])) {
        if (current_user_can('administrator')) {
            return '<div style="padding:10px;background-color:Red;color:#FFFFFF;">'
                    . 'Types: ' . $image_data['error'] . '</div>';
        }
        return $params['field_value'];
    }

    // Set alt
    if (isset($params['alt'])) {
        $alt = $params['alt'];
    }

    // Set title
    if (isset($params['title'])) {
        $title = $params['title'];
    }

    // Set attachment class
    if (!empty($params['size'])) {
        $class[] = 'attachment-' . $params['size'];
    }

    // Set align class
    if (!empty($params['align']) && $params['align'] != 'none') {
        $class[] = 'align' . $params['align'];
    }

    if (!empty($params['class'])) {
        $class[] = $params['class'];
    }
    if (!empty($params['style'])) {
        $style[] = $params['style'];
    }

    // Pre-configured size (use WP function)
    if ($image_data['is_attachment'] && !empty($params['size'])) {
        if (isset($params['url']) && $params['url'] == 'true') {
            $image_url = wp_get_attachment_image_src($image_data['is_attachment'],
                    $params['size']);
            if (!empty($image_url[0])) {
                $output = $image_url[0];
            } else {
                $output = $params['field_value'];
            }
        } else {
            $output = wp_get_attachment_image($image_data['is_attachment'],
                    $params['size'], false,
                    array(
                'class' => implode(' ', $class),
                'style' => implode(' ', $style),
                'alt' => $alt,
                'title' => $title
                    )
            );
        }
    } else { // Custom size
        $width = !empty($params['width']) ? intval($params['width']) : null;
        $height = !empty($params['height']) ? intval($params['height']) : null;
        $crop = (!empty($params['proportional']) && $params['proportional'] == 'true') ? false : true;

        // Check if image is outsider
        if (!$image_data['is_outsider']) {
            $resized_image = wpcf_fields_image_resize_image(
                    $params['field_value'], $width, $height, 'relpath', false,
                    $crop
            );
            if (!$resized_image) {
                $resized_image = $params['field_value'];
            } else {
                // Add to library
                $image_abspath = wpcf_fields_image_resize_image(
                        $params['field_value'], $width, $height, 'abspath',
                        false, $crop
                );
                $add_to_library = wpcf_get_settings('add_resized_images_to_library');
                if ($add_to_library) {
                    global $wpdb;
                    $attachment_exists = $wpdb->get_var($wpdb->prepare("SELECT ID FROM {$wpdb->posts}
    WHERE post_type = 'attachment' AND guid=%s",
                                    $resized_image));
                    if (empty($attachment_exists)) {
                        // Add as attachment
                        $wp_filetype = wp_check_filetype(basename($image_abspath),
                                null);
                        $attachment = array(
                            'post_mime_type' => $wp_filetype['type'],
                            'post_title' => preg_replace('/\.[^.]+$/', '',
                                    basename($image_abspath)),
                            'post_content' => '',
                            'post_status' => 'inherit',
                            'guid' => $resized_image,
                        );
                        global $post;
                        $attach_id = wp_insert_attachment($attachment,
                                $image_abspath, $post->ID);
                        // you must first include the image.php file
                        // for the function wp_generate_attachment_metadata() to work
                        require_once(ABSPATH . "wp-admin" . '/includes/image.php');
                        $attach_data = wp_generate_attachment_metadata($attach_id,
                                $image_abspath);
                        wp_update_attachment_metadata($attach_id, $attach_data);
                    }
                }
            }
        } else {
            $resized_image = $params['field_value'];
        }
        if (isset($params['url']) && $params['url'] == 'true') {
            return $resized_image;
        }
        $output = '<img alt="';
        $output .= $alt !== false ? $alt : $resized_image;
        $output .= '" title="';
        $output .= $title !== false ? $title : $resized_image;
        $output .= '"';
        $output .=!empty($params['onload']) ? ' onload="' . $params['onload'] . '"' : '';
        $output .=!empty($class) ? ' class="' . implode(' ', $class) . '"' : '';
        $output .=!empty($style) ? ' style="' . implode(' ', $style) . '"' : '';
        $output .= ' src="' . $resized_image . '" />';
    }

    return $output;
}

/**
 * Resizes image using WP image_resize() function.
 *
 * Caches return data if called more than one time in one pass.
 *
 * @staticvar array $cached Caches calls in one pass
 * @param <type> $url_path Full URL path (works only with images on same domain)
 * @param <type> $width
 * @param <type> $height
 * @param <type> $refresh Set to true if you want image re-created or not cached
 * @param <type> $crop Set to true if you want apspect ratio to be preserved
 * @param string $suffix Optional (default 'wpcf_$widthxheight)
 * @param <type> $dest_path Optional (defaults to original image)
 * @param <type> $quality
 * @return array
 */
function wpcf_fields_image_resize_image($url_path, $width = 300, $height = 200,
        $return = 'relpath', $refresh = FALSE, $crop = TRUE, $suffix = '',
        $dest_path = NULL, $quality = 75) {

    if (empty($url_path)) {
        return $url_path;
    }

    // Get image data
    $image_data = wpcf_fields_image_get_data($url_path);

    if (empty($image_data['fullabspath']) || !empty($image_data['error'])) {
        return $url_path;
    }

    // Set cache
    static $cached = array();
    $cache_key = md5($url_path . $width . $height . intval($crop) . $suffix . $dest_path);

    // Check if cached in this call
    if (!$refresh && isset($cached[$cache_key][$return])) {
        return $cached[$cache_key][$return];
    }

    $width = intval($width);
    $height = intval($height);

    // Get size of new file
    $size = @getimagesize($image_data['fullabspath']);
    if (!$size) {
        return $url_path;
    }
    list($orig_w, $orig_h, $orig_type) = $size;
    $dims = image_resize_dimensions($orig_w, $orig_h, $width, $height, $crop);
    if (!$dims) {
        return $url_path;
    }
    list($dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h) = $dims;

    // Set suffix
    if (empty($suffix)) {
        $suffix = 'wpcf_' . $dst_w . 'x' . $dst_h;
    } else {
        $suffix .= '_wpcf_' . $dst_w . 'x' . $dst_h;
    }

    $image_data['extension'] = in_array(strtolower($image_data['extension']),
                    array('gif', 'png', 'jpeg')) ? $image_data['extension'] : 'jpg';

    $image_relpath = $image_data['relpath'] . '/' . $image_data['image_name'] . '-'
            . $suffix . '.' . $image_data['extension'];
    $image_abspath = $image_data['abspath'] . DIRECTORY_SEPARATOR
            . $image_data['image_name'] . '-' . $suffix . '.'
            . $image_data['extension'];

    // Check if already resized
    if (!$refresh && file_exists($image_abspath)) {
        // Cache it
        $cached[$cache_key]['relpath'] = $image_relpath;
        $cached[$cache_key]['abspath'] = $image_abspath;
        return $return == 'relpath' ? $image_relpath : $image_abspath;
    }

    // If original file don't exists
    if (!file_exists($image_data['fullabspath'])) {
        return $url_path;
    }

    // Resize image
    $resized_image = @image_resize(
                    $image_data['fullabspath'], $width, $height, $crop, $suffix,
                    $dest_path, $quality
    );

    // Check if error
    if (is_wp_error($resized_image)) {
        return $url_path;
    }

    $image_abspath = $resized_image;

    // Cache it
    $cached[$cache_key]['relpath'] = $image_relpath;
    $cached[$cache_key]['abspath'] = $image_abspath;

    return $return == 'relpath' ? $image_relpath : $image_abspath;
}

/**
 * Gets all necessary data for processed image.
 * 
 * @global type $wpdb
 * @param type $image
 * @return type 
 */
function wpcf_fields_image_get_data($image) {

    // Check if already cached
    static $cache = array();
    if (isset($cache[md5($image)])) {
        return $cache[md5($image)];
    }

    // Strip GET vars
    $image = strtok($image, '?');

    // Basic URL check
    if (strpos($image, 'http') != 0) {
        return array('error' => sprintf(__('Image %s not valid', 'wpcf'), $image));
    }
    // Extension check
    $extension = pathinfo($image, PATHINFO_EXTENSION);
    if (!in_array(strtolower($extension), array('jpg', 'jpeg', 'gif', 'png'))) {
        return array('error' => sprintf(__('Image %s not valid', 'wpcf'), $image));
    }

    // Defaults
    $abspath = '';
    $relpath = '';
    $is_outsider = 1;
    $is_in_upload_path = 0;
    $is_attachment = 0;
    $error = '';

    // Check if it's on domain or subdomain
    $url = get_bloginfo('url');
    $check_image_url = explode('//', $image);
    $check_image_url = explode('/', $check_image_url[1]);
    $check_dir_url = explode('//', $url);
    $check_dir_url = explode('/', $check_dir_url[1]);
    // Check in both ways
    if (@strpos($check_image_url[0], $check_dir_url[0]) !== false
            || @strpos($check_dir_url[0], $check_image_url[0]) !== false) {
        $is_outsider = 0;
    }

    // Check if it's in upload path
    $upload_dir = wp_upload_dir();
    unset($check_image_url[0]);
    if (empty($upload_dir['error'])) {
        $check_upload_dir = explode('//', trim($upload_dir['baseurl']));
        $check_upload_dir = explode('/', $check_upload_dir[1]);
        unset($check_upload_dir[0]);
        if (strpos(implode('/', $check_image_url),
                        implode('/', $check_upload_dir)) !== false) {
            $is_in_upload_path = 1;
        }
    }

    if (!$is_outsider) {
        // Check if it's attachment
        global $wpdb;
        $attachment_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM {$wpdb->posts}
    WHERE post_type = 'attachment' AND guid=%s",
                        $image));
        // Calculate abspath
        // Uploaded
        if ($is_in_upload_path) {
            $info = pathinfo($image);
            $path = parse_url($image);
            if (!is_multisite()) {
                $temp = parse_url(network_home_url());
            } else {
                $temp = parse_url(get_bloginfo('url'));
            }
            $port = isset($path['port']) ? ':' . $path['port'] : '';
            $info['dirname'] = $temp['scheme'] . '://' . $temp['host'] . $port . dirname($path['path']);
            $abspath = str_replace(
                    $upload_dir['baseurl'], $upload_dir['basedir'],
                    $info['dirname']
            );
        } else {// Manually uploaded
            unset($check_image_url[1]);
            if (!is_multisite()) {
                $abspath = dirname(ABSPATH . implode(DIRECTORY_SEPARATOR,
                                $check_image_url));
            } else {
                $network_url = network_home_url();
                $network_url = explode('//', $network_url);
                $network_url = explode('/', $network_url[1]);
                unset($network_url[0], $network_url[1], $check_image_url[1]);
                $abspath = dirname(ABSPATH . implode(DIRECTORY_SEPARATOR,
                                $network_url + $check_image_url));
            }
        }
    }

    $data = array(
        'image' => basename($image),
        'image_name' => basename($image, '.' . $extension),
        'extension' => $extension,
        'abspath' => realpath($abspath),
        'relpath' => dirname($image),
        'fullabspath' => realpath($abspath . DIRECTORY_SEPARATOR . basename($image)),
        'fullrelpath' => $image,
        'is_outsider' => $is_outsider,
        'is_in_upload_path' => $is_in_upload_path,
        'is_attachment' => !empty($attachment_id) ? $attachment_id : 0,
        'error' => $error,
    );

    // Cache it
    $cache[md5($image)] = $data;

    return $data;
}

/**
 * Strips GET vars from value.
 * 
 * @param type $value
 * @return type 
 */
function wpcf_fields_image_value_filter($value) {
    return strtok($value, '?');
}