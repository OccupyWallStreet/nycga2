<?php

class SocializeGraph {

    function SocializeGraph() {
        $socialize_settings = socializeWP::get_options();
        if ($socialize_settings['socialize_og'] == "on") {
            add_action('wp_head', array(&$this, 'opengraph_head'));
        }
    }

    function opengraph_head() {
        /*
          og:title - The title of the entity.
          og:type - The type of entity. You must select a type from the list of Open Graph types.
          og:image - The URL to an image that represents the entity. Images must be at least 50 pixels by 50 pixels. Square images work best, but you are allowed to use images up to three times as wide as they are tall.
          og:url - The canonical, permanent URL of the page representing the entity. When you use Open Graph tags, the Like button posts a link to the og:url instead of the URL in the Like button code.
          og:site_name - A human-readable name for your site, e.g., "IMDb".
          fb:admins or fb:app_id - A com
         */
        $og_properties = $this->get_properties();

        foreach ($og_properties as $property => $value) {
            if (trim($value) != "") {
                echo "<meta property=\"" . $property . "\" content=\"" . $value . "\"/>" . "\n";
            }
        }
    }

    function get_properties() {
        global $post;
        $socialize_settings = socializeWP::get_options();
        $og_properties = array();
        if (is_singular()) {
            $image = '';

            if (current_theme_supports('post-thumbnails') && has_post_thumbnail($post->ID)) {
                $thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'post-thumbnail');
                if ($thumbnail) {
                    $image = $thumbnail[0];
                }
            }

            $og_properties = array(
                'og:title' => $post->post_title,
                'og:type' => 'article',
                'og:url' => get_permalink(),
                'og:image' => $image,
                'og:site_name' => get_bloginfo('name'),
                //'og:description'       => strip_tags(get_the_excerpt()),
                'fb:app_id' => $socialize_settings['socialize_fb_appid'],
                'fb:admins' => $socialize_settings['socialize_fb_adminid'],
                'fb:page_id' => $socialize_settings['socialize_fb_pageid']
            );
        }
        return $og_properties;
    }
}