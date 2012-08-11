<div class="cbox">
<?php
$fetch_group_mode = get_option('tn_blogsmu_home_feat_group_id');
$fetch_group_mode_header = get_option('tn_blogsmu_home_feat_group_header');
if($fetch_group_mode_header == '') {
?>
<h3><?php _e("Checkout our growing community network, why not join one of them?", TEMPLATE_DOMAIN); ?></h3>
<?php } else { ?>
<h3><?php echo stripcslashes($fetch_group_mode_header); ?></h3>
<?php } ?>

<?php
if($fetch_group_mode == '') {
fetch_random_groups($limit='6', $size='138', $type='full', $block_id='random-groups');
} else {
fetch_specific_groups($limit='6', $size='138', $type='full', $block_id='random-groups');
}
?>

</div>