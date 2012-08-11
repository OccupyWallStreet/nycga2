<div id="slider-wrapper">
<div id="slider" class="nivoSlider">

<?php
global $wpdb, $bp, $bp_existed;
if ($bp_existed=='true') {
$bp_album_count=get_option('tn_blogsmu_featured_bp_album_count');
if ( ! is_numeric($bp_album_count) ) $bp_album_count = 0;
$uploads = wp_upload_dir();
$bp_album_feat_img = $wpdb->get_results("SELECT * FROM " . $wpdb->base_prefix . "bp_album WHERE privacy = '0' ORDER BY RAND() ASC LIMIT " . $bp_album_count);

if($bp_album_feat_img) {
foreach($bp_album_feat_img as $myalbum ) {

$filename = substr( $myalbum->pic_mid_url, strrpos($myalbum->pic_mid_url, '/') + 1 );
$owner_id = $myalbum->owner_id;
$result = bp_get_root_domain() . '/' . BP_MEMBERS_SLUG . '/'. bp_get_username_by_id($id=$owner_id) . '/' . $bp->album->slug .'/'. $bp->album->single_slug . '/' . $myalbum->id;
?>

<a href="<?php echo $result; ?>">
<?php if ( bp_core_is_multisite() ) { ?>
<img title="<?php echo $myalbum->description; ?>" src="<?php echo $uploads['baseurl'] . '/'. $bp->album->slug . '/' . $myalbum->owner_id . '/'. $filename; ?>" />
<?php } else { ?>
<img title="<?php echo $myalbum->description; ?>" src="<?php echo site_url() . $myalbum->pic_mid_url; ?>" />
<?php } ?>
</a>
<?php } }
}
?>



</div>
</div>