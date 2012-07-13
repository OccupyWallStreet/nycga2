<?php

/* Apply WordPress defined filters */
add_filter( 'bp_get_link_url', 'stripslashes' );
add_filter( 'bp_get_link_name', 'stripslashes' );
add_filter( 'bp_get_link_description', 'stripslashes' );
add_filter( 'bp_get_link_description_excerpt', 'stripslashes' );
add_filter( 'bp_links_add_meta_description_single_item', 'stripslashes' );

add_filter( 'bp_get_link_name', 'wptexturize' );
add_filter( 'bp_get_link_description', 'wptexturize' );
add_filter( 'bp_get_link_description_excerpt', 'wptexturize' );
add_filter( 'bp_links_add_meta_description_single_item', 'wptexturize' );

add_filter( 'bp_get_link_name', 'convert_chars' );
add_filter( 'bp_get_link_description', 'convert_chars' );
add_filter( 'bp_get_link_description_excerpt', 'convert_chars' );
add_filter( 'bp_links_add_meta_description_single_item', 'convert_chars' );

// not sure how this works, but we don't want any html tags anyways
//add_filter( 'bp_get_link_name', 'wp_filter_kses', 1 );
//add_filter( 'bp_get_link_description', 'wp_filter_kses', 1 );
//add_filter( 'bp_get_link_description_excerpt', 'wp_filter_kses', 1 );
//add_filter( 'bp_links_link_name_before_save', 'wp_filter_kses', 1 );
//add_filter( 'bp_links_link_description_before_save', 'wp_filter_kses', 1 );
//add_filter( 'bp_links_add_meta_description_single_item', 'wp_filter_kses', 1 );

// just escape any html to be safe
// FYI if you remove this filters the feed is going to get hosed
add_filter( 'bp_get_link_url', 'esc_url' );
add_filter( 'bp_get_link_name', 'esc_html' );
add_filter( 'bp_get_link_description', 'esc_html' );
add_filter( 'bp_get_link_description_excerpt', 'esc_html' );
add_filter( 'bp_links_add_meta_description_single_item', 'esc_html' );

// forms - strip
add_filter( 'bp_get_link_details_form_category_id', 'stripslashes' );
add_filter( 'bp_get_link_details_form_url', 'stripslashes' );
add_filter( 'bp_get_link_details_form_name', 'stripslashes' );
add_filter( 'bp_get_link_details_form_description', 'stripslashes' );
add_filter( 'bp_get_link_avatar_form_embed_html', 'stripslashes' );

// forms - escape
add_filter( 'bp_get_link_details_form_category_id', 'esc_attr' );
add_filter( 'bp_get_link_details_form_url', 'esc_attr' );
add_filter( 'bp_get_link_details_form_name', 'esc_attr' );
add_filter( 'bp_get_link_details_form_description', 'esc_html' );
add_filter( 'bp_get_link_avatar_form_embed_html', 'esc_html' );

add_filter( 'bp_get_link_description', 'convert_smilies' );
add_filter( 'bp_get_link_description_excerpt', 'convert_smilies' );
?>