<?php
/**
 * Register data (called automatically).
 * 
 * @return type 
 */
function wpcf_fields_textfield() {
    return array(
        'id' => 'wpcf-texfield',
        'title' => __('Single line', 'wpcf'),
        'description' => __('Texfield', 'wpcf'),
        'validate' => array('required'),
    );
}