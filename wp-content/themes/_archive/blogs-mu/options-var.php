<?php
include( TEMPLATEPATH . '/_inc/functions/conditional-functions.php' );

global $options, $options4, $options5, $options6, $options7, $options8, $options9, $options10, $options11, $options12, $bp_existed, $bp_front_is_activity, $multi_site_on;

foreach ($options as $value) {
if (get_option( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_option( $value['id'] ); } }


foreach ($options4 as $value) {
if (get_option( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_option( $value['id'] ); } }

foreach ($options5 as $value) {
if (get_option( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_option( $value['id'] ); } }

foreach ($options6 as $value) {
if (get_option( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_option( $value['id'] ); } }

foreach ($options7 as $value) {
if (get_option( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_option( $value['id'] ); } }

foreach ($options8 as $value) {
if (get_option( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_option( $value['id'] ); } }

foreach ($options9 as $value) {
if (get_option( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_option( $value['id'] ); } }

foreach ($options10 as $value) {
if (get_option( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_option( $value['id'] ); } }

foreach ($options11 as $value) {
if (get_option( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_option( $value['id'] ); } }

foreach ($options12 as $value) {
if (get_option( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_option( $value['id'] ); } }


//////////////////////////////////////////////
//////check platform/////////////////////////
//////////////////////////////////////////////
$uploads = wp_upload_dir();
$upload_files_path = get_option('upload_path');

global $blog_id;
$tpl_url = get_site_url();
$ptp = get_template();
$uploads_folder = "thumb";

$upload_path = $uploads['basedir'] . '/' . $uploads_folder . "/";
$upload_path_check = $uploads['basedir'] . '/' . $uploads_folder;

$ttpl = get_template_directory_uri();
$ttpl_url = get_site_url();


$upload_url_trim = str_replace( WP_CONTENT_DIR, "", $uploads['basedir'] );
//echo $upload_url_trim;
if (substr($upload_url_trim, -1) == '/') {
$upload_url_trim = rtrim($upload_url_trim, '/');
}

$ttpl_path = WP_CONTENT_URL . $upload_url_trim  . '/' . $uploads_folder;;
$upload_url = WP_CONTENT_URL . $upload_url_trim  . '/' . $uploads_folder;

?>