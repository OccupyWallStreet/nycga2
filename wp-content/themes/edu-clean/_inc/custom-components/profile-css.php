<?php
/*///////////////////////////////////////

Profile CSS Component

///////////////////////////////////////*/
?>
<?php
global $bp, $wpdb;
$displayed_user_id = $bp->displayed_user->id;
$the_profile_link = $bp->displayed_user->domain . $bp->settings->slug . '/' .'profile-css' . '/';
?>


<?php
if( isset($_POST["save-setting"]) ) {

//profile css setting
$profile_header_img = wp_filter_post_kses(  $_POST['profile_header_img'] );
$profile_ads_box1 =  wp_filter_post_kses( $_POST['profile_ads_box1'] );
$profile_ads_box2 =  wp_filter_post_kses( $_POST['profile_ads_box2'] );

//profile css save
update_user_meta_values($my_id=$displayed_user_id, $metakey='profile_header_img', $metavalue=$profile_header_img);
update_user_meta_values($my_id=$displayed_user_id, $metakey='profile_ads_box1', $metavalue=$profile_ads_box1);
update_user_meta_values($my_id=$displayed_user_id, $metakey='profile_ads_box2', $metavalue=$profile_ads_box2);

if( clean_script_process($_POST['profile_header_img']) || clean_script_process($_POST['profile_ads_box1']) || clean_script_process($_POST['profile_ads_box2']) ) {
bp_core_add_message( __( 'We detected an script insertion in your last saved, the effected input will not be save.', TEMPLATE_DOMAIN ), 'error' );
} else {
bp_core_add_message( __( 'Profile CSS updated successfully!', TEMPLATE_DOMAIN ) );
}

}

if( isset($_POST["delete-setting"]) ) {
//blog reset

delete_user_meta($my_id=$displayed_user_id, $metakey='profile_header_img', $metavalue=$profile_header_img);
delete_user_meta($my_id=$displayed_user_id, $metakey='profile_ads_box1', $metavalue=$profile_ads_box1);
delete_user_meta($my_id=$displayed_user_id, $metakey='profile_ads_box2', $metavalue=$profile_ads_box2);

bp_core_add_message( __( 'Profile CSS reset successfully!', TEMPLATE_DOMAIN ) );
}

$the_profile_header_img = get_user_meta_values($my_id=$displayed_user_id='',$metakey='profile_header_img');
$the_profile_ads_box1 = get_user_meta_values($my_id=$displayed_user_id='',$metakey='profile_ads_box1');
$the_profile_ads_box2 = get_user_meta_values($my_id=$displayed_user_id, $metakey='profile_ads_box2');



?>


<form class="standard-form base" action="" method="post">
<h4><?php _e('Customize Your Profile CSS', TEMPLATE_DOMAIN); ?></h4>
<p class="small"><?php _e("you can add header image and add banner image to your profile page",TEMPLATE_DOMAIN); ?></p>

<?php do_action( 'template_notices' ) // (error/success feedback) ?>
<input type="hidden" name="action" value="post" />


<div class="editfield">
<label><?php _e('Insert Your Profile Header Image Full Url Here',TEMPLATE_DOMAIN); ?></label>
<input<?php if($the_profile_header_img != "") { ?> class="save"<?php } ?> id="profile_header_img" name="profile_header_img" type="text" value="<?php echo $the_profile_header_img; ?>" />
<p class="description"><?php _e("insert an images full url link here with width 650px to personalize your profile page",TEMPLATE_DOMAIN); ?></p>
</div>



<div class="editfield">
<label><?php _e('Additional Ads Slot 1 For User *HTML Allowed',TEMPLATE_DOMAIN); ?></label>
<textarea<?php if($the_profile_ads_box1 != "") { ?> class="save"<?php } ?> id="profile_ads_box1" name="profile_ads_box1" value="" />
<?php echo stripslashes($the_profile_ads_box1); ?>
</textarea>
<p class="description"><?php _e("you can insert your own html ads code here and it will showed in profile page",TEMPLATE_DOMAIN); ?></p>
</div>


<div class="editfield">
<label><?php _e('<strong>Additional Ads Slot 2 For User</strong> *HTML Allowed',TEMPLATE_DOMAIN); ?></label>
<textarea<?php if($the_profile_ads_box2 != "") { ?> class="save"<?php } ?> id="profile_ads_box2" name="profile_ads_box2" value="" />
<?php echo stripslashes($the_profile_ads_box2); ?>
</textarea>
<p class="description"><?php _e("you can insert your own html ads code here and it will showed in profile page",TEMPLATE_DOMAIN); ?></p>
</div>

<p><input type="submit" name="save-setting" id="save-changes" value="Save Setting" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="delete-setting" id="delete-changes" value="Reset" /></p>

</form>




