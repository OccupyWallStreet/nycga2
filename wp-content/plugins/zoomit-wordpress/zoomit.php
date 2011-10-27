<?php
/*
Plugin Name: Zoom.it
Plugin URI: http://zoom.it
Description: Zoom.it let's you easily share high-resolution images on your blog.
Version: 0.4.2
Author: Daniel Gasienica
Author URI: http://gasi.ch
License: Apache License, Version 2.0
*/

// Imports
require('zoomit-php-sdk/Zoomit.class.php');

// Shortcode, filter & action hooks
add_shortcode('zoomit', 'zoomit_shortcode_handler');
add_filter('image_send_to_editor', 'zoomit_image_send_to_editor', 10 /* priority */, 8 /* arguments */);
add_filter('attachment_fields_to_edit', 'zoomit_attachment_fields_to_edit', 11 /* priority */, 2 /* arguments */);
add_filter('wp_generate_attachment_metadata', 'zoomit_wp_generate_attachment_metadata', 10, 2);

function zoomit_wp_generate_attachment_metadata($metadata, $attachment_id) {
    $post = get_post($attachment_id);
    $url = $post->guid;
    $zoomit = new Zoomit();
    $content = $zoomit->getContentInfoByURL($url);
    $zoomit_data = array('_zoomit_id' => $content['id']);
    return array_merge($metadata, $zoomit_data);
}

function zoomit_image_send_to_editor($html, $id, $caption, $title, $align, $url, $size, $alt) {
    list($img_src, $width, $height) = image_downsize($id, $size);
    $hwstring = image_hwstring($width, $height);
    $metadata = wp_get_attachment_metadata($id);
    $zoomit_id = $metadata['_zoomit_id'];
    return "[zoomit id=\"$zoomit_id\" width=\"auto\" height=\"{$height}px\"]";
}

function zoomit_shortcode_handler($atts) {
    // Default attributes
    shortcode_atts(array('id' => '8',
                         'width' => 'auto',
                         'height' => '400px'), $atts);
    return "<script src=\"http://zoom.it/{$atts['id']}.js?width={$atts['width']}&height={$atts['height']}\"></script>";
}

function zoomit_attachment_fields_to_edit($form_fields, $post) {
    $metadata = wp_get_attachment_metadata($id);
    $zoomit_id = $metadata['_zoomit_id'];
    if (!empty($zoomit_id)) {
        $form_fields['zoomit_id'] = array(
            'label'      => __('Zoom.it ID'),
            'input'      => 'html',
            'html'       => "<input type='text' class='text' readonly='readonly' value='" . esc_attr($zoomit_id) . "' /><br />",
            'value'      => $zoomit_id,
            'helps'      => __('Zoom.it ID of the uploaded image after conversion')
        );
    }

    unset($form_fields['post_title']);
    unset($form_fields['image_alt']);
    unset($form_fields['post_excerpt']);
    unset($form_fields['post_content']);
    unset($form_fields['url']);
    unset($form_fields['image-size']);
    unset($form_fields['align']);

    return $form_fields;
}

?>