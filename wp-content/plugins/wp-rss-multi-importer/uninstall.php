<?php
// If uninstall not called from WordPress exit
if( !defined( 'WP_UNINSTALL_PLUGIN' ) )
exit ();
// Delete option from options table
if (is_multisite()) {
    global $wpdb;
    $blogs = $wpdb->get_results("SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A);
    if ($blogs) {
        foreach($blogs as $blog) {
            switch_to_blog($blog['blog_id']);
            delete_option('rss_import_items');
            delete_option('rss_import_categories');
			delete_option('rss_template_item');
			delete_option('rss_import_options');
        }
        restore_current_blog();
    }
} else {
    delete_option('rss_import_items');
    delete_option('rss_import_categories');
	delete_option('rss_template_item');
	delete_option('rss_import_options');
}
//
?>