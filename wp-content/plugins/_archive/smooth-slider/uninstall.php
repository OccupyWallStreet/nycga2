<?php 
//This plugin creates an entry in the options database. When the plugin will be deleted, this code will automatically delete the database entry from the options Wordpress table.
delete_option('smooth_slider_options'); 
//This plugin creates its own database tables to save the post ids for the posts and pages added to Smooth Slider. When the plugin will be deleted, the database tables will also get deleted.
global $wpdb, $table_prefix;

$slider_table = $table_prefix.SLIDER_TABLE;
$slider_meta = $table_prefix.SLIDER_META;
$slider_postmeta = $table_prefix.SLIDER_POST_META;
$sql = "DROP TABLE $slider_table;";
$wpdb->query($sql);
$sql = "DROP TABLE $slider_meta;";
$wpdb->query($sql);
$sql = "DROP TABLE $slider_postmeta;";
$wpdb->query($sql);
?>