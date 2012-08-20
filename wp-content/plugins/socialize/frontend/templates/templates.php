<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class socialize_inline_class {

    function init() {
        $socialize_settings = socializeWP::get_options();
        // check to see if turned on,
        // check to see if single page, post or custom post type
        if(is_singular() && !is_feed()){
            if($socialize_settings['socialize_button_display'] == 'out'){
                add_filter('socialize-inline_class', array(__CLASS__, 'replace_class'));
                add_filter('socialize-after-inline_content', array(__CLASS__, 'email_button'));
                add_filter('socialize-after-inline_content', array(__CLASS__, 'print_button'));
                add_filter('wp_enqueue_scripts', array(__CLASS__, 'scripts'));
                add_action('wp_head', array(__CLASS__, 'style'));
            }
        }
    }

    function scripts() {
        wp_enqueue_script('socialize-floating', SOCIALIZE_URL . 'frontend/js/floating.js', array('jquery'));
    }
    
    function style(){
        $socialize_settings = socializeWP::get_options();
        echo '<style type="text/css" media="screen">';
        echo '.socialize-floating { margin-left: '.$socialize_settings['socialize_out_margin'].'px; }';
        echo '</style>';
    }
    
    function replace_class($classes) {
        $classes = array('socialize-floating', 'socialize-floating-bg');
        return $classes;
    }

    function email_button($content) {
        $content .= '<div class="socialize-in-button socialize-in-button-vertical">';
        $content .= '<a href="mailto:?subject=' . urlencode(get_the_title()) . '&body=' . urlencode(get_permalink()) . '" class="socialize-email-button">Email</a>';
        $content .= '</div>';
        return $content;
    }
    
    function print_button($content) {
        $content .= '<div class="socialize-in-button socialize-in-button-vertical">';
        $content .= '<a href="javascript:window.print()" class="socialize-print-button">Print</a>';
        $content .= '</div>';
        return $content;
    }

}
add_filter('wp', array('socialize_inline_class', 'init'));
//socialize_inline_class::init();
?>
