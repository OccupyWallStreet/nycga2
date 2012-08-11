<?php
/*///////////////////////////////////////

Youtube-Flickr Component

///////////////////////////////////////*/
?>


<?php
global $bp, $wpdb;
$displayed_user_id = $bp->displayed_user->id;
$the_profile_link = $bp->displayed_user->domain . $bp->settings->slug . '/' .'flickr-youtube' . '/';

$allowed_html = array(
    'p' => array(),
    'a' => array('href' => array(),'title' => array()),
    'br' => array(),
    'em' => array(),
    'strong' => array(),
    'object' => array(
    'height' => array (),
    'width' => array ()),
    'iframe' => array(
    'height' => array(),
    'width' => array(),
    'allowFullScreen' => array(),
    'src' => array(),
    'frameborder' => array ()),
    'param' => array (
    'name' => array (),
    'value' => array ()),
    'object' => array(
    'width' => array (),
    'height' => array (),
    'movie' => array (),
    'allowFullScreen' => array (),
    'embed src' => array(),
    'application/x-shockwave-flash' => array() ) );
?>


<?php
if( isset($_POST["save-setting"]) ) {

$flickr_save = wp_filter_post_kses($_POST['flickr_save']);
$video_save = wp_filter_post_kses( $_POST['video_save'] );
$video_save_misc = wp_kses($_POST['video_save_misc'], $allowed_html);

update_user_meta_values($my_id=$displayed_user_id, $metakey='user_flickr', $metavalue=$flickr_save);
update_user_meta_values($my_id=$displayed_user_id, $metakey='user_video', $metavalue=$video_save);
update_user_meta_values($my_id=$displayed_user_id, $metakey='user_video_misc', $metavalue=$video_save_misc);


if( clean_script_process($_POST['flickr_save']) || clean_script_process($_POST['video_save']) || clean_script_process($_POST['video_save_misc']) ) {
bp_core_add_message( __( 'We detected an script insertion in your last saved, the effected input will not be save.', TEMPLATE_DOMAIN ), 'error' );
} else {
bp_core_add_message( __( 'flickr-youtube settings updated successfully!', TEMPLATE_DOMAIN ) );
}




}

if( isset($_POST["delete-setting"]) ) {
delete_user_meta($my_id=$displayed_user_id, $metakey='user_flickr', $metavalue=$flickr_save);
delete_user_meta($my_id=$displayed_user_id, $metakey='user_video', $metavalue=$video_save);
delete_user_meta($my_id=$displayed_user_id, $metakey='user_video_misc', $metavalue=$video_save_misc);

bp_core_add_message( __( 'flickr-youtube settings reset successfully!', TEMPLATE_DOMAIN ) );
}

$the_profile_flickr = get_user_meta_values($my_id=$displayed_user_id='',$metakey='user_flickr');
$the_profile_video = get_user_meta_values($my_id=$displayed_user_id='',$metakey='user_video');
$the_profile_video_misc = get_user_meta_values($my_id=$displayed_user_id='',$metakey='user_video_misc');
?>




<form class="standard-form base" action="" method="post">
<h4><?php _e('Flickr and Youtube Setting',TEMPLATE_DOMAIN); ?></h4>
<p class="small"><?php _e("the flickr and video will be showed in your profile page",TEMPLATE_DOMAIN); ?></p>

<?php do_action( 'template_notices' ) // (error/success feedback) ?>

<input type="hidden" name="action" value="post" />

<div class="editfield">
<label><?php _e('Insert Your flickr ID Here',TEMPLATE_DOMAIN); ?></label>
<input<?php if($the_profile_flickr != "") { ?> class="save"<?php } ?> id="flickr_save" name="flickr_save" type="text" value="<?php echo $the_profile_flickr; ?>" />
<p class="description">example: <strong>37219473@N03 or 44419533@N54</strong><br /><a target="_blank" href='http://idgettr.com/'><?php _e("Get Valid flickr ID",TEMPLATE_DOMAIN); ?></a></p>
</div>


<div class="editfield">
<label><?php _e('Insert Your Youtube Video Id here',TEMPLATE_DOMAIN); ?></label>
<input<?php if($the_profile_video != "") { ?> class="save"<?php } ?> id="video_save" name="video_save" type="text" value="<?php echo $the_profile_video; ?>" />
<p class="description">
<em>example: http://www.youtube.com/watch?v=<strong><?php _e("this is your video id",TEMPLATE_DOMAIN); ?></strong></em><br />
*only youtube id code allowed, did not support flash or swf</p>
</div>


<div class="editfield">
<label><?php _e('Insert Your Other Video embed code here',TEMPLATE_DOMAIN); ?></label>
<textarea<?php if($the_profile_video_misc != "") { ?> class="save"<?php } ?> id="video_save_misc" name="video_save_misc" value="" />
<?php echo stripslashes($the_profile_video_misc); ?>
</textarea>
<p class="description"><?php _e("*if you have youtube id saved above, this option will take priority so you have to clear this option to used youtube video. You can input video code like google video, vimeo or any embed code here",TEMPLATE_DOMAIN); ?></p>
</div>


<p><input type="submit" name="save-setting" id="save-changes" value="Save Setting" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="delete-setting" id="delete-changes" value="Reset" /></p>

</form>




