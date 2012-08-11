<?php
/*///////////////////////////////////////

Social Media Component

///////////////////////////////////////*/
?>


<?php
global $bp, $wpdb;
$displayed_user_id = $bp->displayed_user->id;
$the_profile_link = $bp->displayed_user->domain . $bp->settings->slug . '/' .'social-media' . '/';
?>


<?php
if( isset($_POST["save-setting"]) ) {

//blog setting
$blog_feed_save = $_POST['blog_feed'];
$blog_feed_count = $_POST['blog_feed_count'];
$blog_feed_show_content = $_POST['blog_feed_show_content'];

//social media setting
$facebook_save = wp_filter_post_kses($_POST['facebook_save']);
$twitter_save = wp_filter_post_kses($_POST['twitter_save']);
$linked_save = wp_filter_post_kses($_POST['linked_save']);
$myspace_save = wp_filter_post_kses($_POST['myspace_save']);
$orkut_save = wp_filter_post_kses($_POST['orkut_save']);
$stumble_save = wp_filter_post_kses($_POST['stumble_save']);
$digg_save = wp_filter_post_kses($_POST['digg_save']);
$youtube_save = wp_filter_post_kses($_POST['youtube_save']);
$delicious_save = wp_filter_post_kses($_POST['delicious_save']);

//blog save
update_user_meta_values($my_id=$displayed_user_id, $metakey='blog_feed', $metavalue=$blog_feed_save);
update_user_meta_values($my_id=$displayed_user_id, $metakey='blog_feed_count', $metavalue=$blog_feed_count);
update_user_meta_values($my_id=$displayed_user_id, $metakey='blog_feed_show_content', $metavalue=$blog_feed_show_content);

//social media save
update_user_meta_values($my_id=$displayed_user_id, $metakey='facebook_save', $metavalue=$facebook_save);
update_user_meta_values($my_id=$displayed_user_id, $metakey='twitter_save', $metavalue=$twitter_save);
update_user_meta_values($my_id=$displayed_user_id, $metakey='linked_save', $metavalue=$linked_save);
update_user_meta_values($my_id=$displayed_user_id, $metakey='myspace_save', $metavalue=$myspace_save);

update_user_meta_values($my_id=$displayed_user_id, $metakey='stumble_save', $metavalue=$stumble_save);

update_user_meta_values($my_id=$displayed_user_id, $metakey='digg_save', $metavalue=$digg_save);
update_user_meta_values($my_id=$displayed_user_id, $metakey='youtube_save', $metavalue=$youtube_save);
update_user_meta_values($my_id=$displayed_user_id, $metakey='delicious_save', $metavalue=$delicious_save);

bp_core_add_message( __( 'Settings updated successfully!', TEMPLATE_DOMAIN ) );
}

if( isset($_POST["delete-setting"]) ) {
//blog reset
delete_user_meta($my_id=$displayed_user_id, $metakey='blog_feed', $metavalue=$blog_feed_save);
delete_user_meta($my_id=$displayed_user_id, $metakey='blog_feed_count', $metavalue=$blog_feed_count);
delete_user_meta($my_id=$displayed_user_id, $metakey='blog_feed_show_content', $metavalue=$blog_feed_show_content);

//social media reset
delete_user_meta($my_id=$displayed_user_id, $metakey='facebook_save', $metavalue=$facebook_save);
delete_user_meta($my_id=$displayed_user_id, $metakey='twitter_save', $metavalue=$twitter_save);
delete_user_meta($my_id=$displayed_user_id, $metakey='linked_save', $metavalue=$linked_save);
delete_user_meta($my_id=$displayed_user_id, $metakey='myspace_save', $metavalue=$myspace_save);

delete_user_meta($my_id=$displayed_user_id, $metakey='stumble_save', $metavalue=$stumble_save);

delete_user_meta($my_id=$displayed_user_id, $metakey='digg_save', $metavalue=$digg_save);
delete_user_meta($my_id=$displayed_user_id, $metakey='youtube_save', $metavalue=$youtube_save);
delete_user_meta($my_id=$displayed_user_id, $metakey='delicious_save', $metavalue=$delicious_save);

bp_core_add_message( __( 'Settings reset successfully!', TEMPLATE_DOMAIN ) );
}

$the_blog_feed_save = get_user_meta_values($my_id=$displayed_user_id='',$metakey='blog_feed');
$the_blog_feed_count = get_user_meta_values($my_id=$displayed_user_id='',$metakey='blog_feed_count');
$the_blog_feed_show_content = get_user_meta_values($my_id=$displayed_user_id='',$metakey='blog_feed_show_content');

$the_facebook_save = get_user_meta_values($my_id=$displayed_user_id, $metakey='facebook_save');
$the_twitter_save = get_user_meta_values($my_id=$displayed_user_id, $metakey='twitter_save');
$the_linked_save = get_user_meta_values($my_id=$displayed_user_id, $metakey='linked_save');
$the_myspace_save = get_user_meta_values($my_id=$displayed_user_id, $metakey='myspace_save');

$the_stumble_save = get_user_meta_values($my_id=$displayed_user_id, $metakey='stumble_save');
$the_digg_save = get_user_meta_values($my_id=$displayed_user_id, $metakey='digg_save');
$the_youtube_save = get_user_meta_values($my_id=$displayed_user_id, $metakey='youtube_save');
$the_delicious_save = get_user_meta_values($my_id=$displayed_user_id, $metakey='delicious_save');

?>


<form class="standard-form base" action="" method="post">

<?php do_action( 'template_notices' ) // (error/success feedback) ?>

<input type="hidden" name="action" value="post" />

<div style="margin-bottom: 50px;">
<h4><?php _e('Blog RSS Setting', TEMPLATE_DOMAIN); ?></h4>
<p class="small"><?php _e("the blog feed will be showed in your profile page",TEMPLATE_DOMAIN); ?></p>

<div class="editfield">
<label><?php _e('Insert Your Blog Feed URL Here', TEMPLATE_DOMAIN); ?></label>
<input<?php if($the_blog_feed_save != "") { ?> class="save"<?php } ?> id="blog_feed" name="blog_feed" type="text" value="<?php echo $the_blog_feed_save; ?>" />
<p class="description"><?php _e("example: http://site.com/feed ot http://feedburner.com/sitename",TEMPLATE_DOMAIN); ?></p>
</div>


<div class="editfield">
<label><?php _e('How Many Blog Feed Post You Want To Fetch', TEMPLATE_DOMAIN); ?></label>
<input<?php if($the_blog_feed_count != "") { ?> class="save"<?php } ?> id="blog_feed_count" name="blog_feed_count" type="text" value="<?php echo $the_blog_feed_count; ?>" />
<p class="description"><?php _e("valid value around 1 to 25",TEMPLATE_DOMAIN); ?></p>
</div>


<div class="editfield">
<label><?php _e('Do You Want To Show Content On Your Feed',TEMPLATE_DOMAIN); ?></label>
<select style="width: 70%;" name="blog_feed_show_content" id="blog_feed_show_content">
<option<?php if($the_blog_feed_show_content == "yes" && $the_blog_feed_show_content == "") { ?> <?php echo ' selected="selected"'; ?><?php } ?>>yes</option>
<option<?php if($the_blog_feed_show_content == "no") { ?> <?php echo ' selected="selected"'; ?><?php } ?>>no</option>
</select>
<p class="description"><?php _e("*if no, only title will be showed ",TEMPLATE_DOMAIN); ?></p>
</div>
</div>


<div style="margin-bottom: 50px;">
<h4><?php _e('Social Media Setting', TEMPLATE_DOMAIN); ?></h4>
<div class="editfield">
<label><?php _e('Insert Social Media Profile URL Here *must have http://',TEMPLATE_DOMAIN); ?></label>

<p><img src="<?php echo get_template_directory_uri(); ?>/_inc/images/social/facebook.png" width="16" height="16" alt="facebook" /> <?php _e("Facebook Profile",TEMPLATE_DOMAIN); ?>
<input<?php if($the_facebook_save != "") { ?> class="save"<?php } ?> style="width: 50%; margin-left:10px;" id="facebook_save" name="facebook_save" type="text" value="<?php echo $the_facebook_save; ?>" />
</p>

<p><img src="<?php echo get_template_directory_uri(); ?>/_inc/images/social/twitter.png" width="16" height="16" alt="twitter" /> <?php _e("Twitter Profile",TEMPLATE_DOMAIN); ?>
<input<?php if($the_twitter_save != "") { ?> class="save"<?php } ?> style="width: 50%; margin-left:10px;" id="twitter_save" name="twitter_save" type="text" value="<?php echo $the_twitter_save; ?>" />
</p>

<p><img src="<?php echo get_template_directory_uri(); ?>/_inc/images/social/linked.png" width="16" height="16" alt="linkedin" /> <?php _e("Linkedin Profile",TEMPLATE_DOMAIN); ?>
<input<?php if($the_linked_save != "") { ?> class="save"<?php } ?> style="width: 50%; margin-left:10px;" id="linked_save" name="linked_save" type="text" value="<?php echo $the_linked_save; ?>" />
</p>

<p><img src="<?php echo get_template_directory_uri(); ?>/_inc/images/social/myspace.png" width="16" height="16" alt="myspace" /> <?php _e("MySpace Profile",TEMPLATE_DOMAIN); ?>
<input<?php if($the_myspace_save != "") { ?> class="save"<?php } ?> style="width: 50%; margin-left:10px;" id="myspace_save" name="myspace_save" type="text" value="<?php echo $the_myspace_save; ?>" />
</p>

<p><img src="<?php echo get_template_directory_uri(); ?>/_inc/images/social/stumbleupon.png" width="16" height="16" alt="stumbleupon" /> <?php _e("StumbleUpon Profile",TEMPLATE_DOMAIN); ?>
<input<?php if($the_stumble_save != "") { ?> class="save"<?php } ?> style="width: 50%; margin-left:10px;" id="stumble_save" name="stumble_save" type="text" value="<?php echo $the_stumble_save; ?>" />
</p>


<p><img src="<?php echo get_template_directory_uri(); ?>/_inc/images/social/digg.png" width="16" height="16" alt="digg" /> <?php _e("Digg Profile",TEMPLATE_DOMAIN); ?>
<input<?php if($the_digg_save != "") { ?> class="save"<?php } ?> style="width: 50%; margin-left:10px;" id="digg_save" name="digg_save" type="text" value="<?php echo $the_digg_save; ?>" />
</p>

<p><img src="<?php echo get_template_directory_uri(); ?>/_inc/images/social/youtube.png" width="16" height="16" alt="youtube" /> <?php _e("YouTube Profile",TEMPLATE_DOMAIN); ?>
<input<?php if($the_youtube_save != "") { ?> class="save"<?php } ?> style="width: 50%; margin-left:10px;" id="youtube_save" name="youtube_save" type="text" value="<?php echo $the_youtube_save; ?>" />
</p>

<p><img src="<?php echo get_template_directory_uri(); ?>/_inc/images/social/delicious.png" width="16" height="16" alt="facebook" /> <?php _e("Delicious Profile",TEMPLATE_DOMAIN); ?>
<input<?php if($the_delicious_save != "") { ?> class="save"<?php } ?> style="width: 50%; margin-left:10px;" id="delicious_save" name="delicious_save" type="text" value="<?php echo $the_delicious_save; ?>" />
</p>
<p class="description"><?php _e("the social profile link will be showed in your profile page",TEMPLATE_DOMAIN); ?></p>
</div>
</div>

<p><input type="submit" name="save-setting" id="save-changes" value="Save Setting" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="delete-setting" id="delete-changes" value="Reset" /></p>

</form>




