<?php
////////////////////custom social link bookmark//////////////////////////////

function custom_social_badge($args) {

extract($args);

$settings = get_option("widget_custom_social_badge");

$sb_name = $settings['name'];
//check if xustom name exited///
if($sb_name == '') {
$sb_name = __('Connect With Us At', TEMPLATE_DOMAIN);
} else {
$sb_name = $sb_name;
}

$facebook_link = $settings['facebook_link'];

$twitter_link = $settings['twitter_link'];

$youtube_link = $settings['youtube_link'];

$flickr_link = $settings['flickr_link'];

$lastfm_link = $settings['lastfm_link'];

$myspace_link = $settings['myspace_link'];
?>

<?php

echo $before_widget;

echo $before_title . $sb_name . $after_title;

echo "<ul class='button'>";   ?>

<?php if( $facebook_link != "" ) { ?>
<li class="facebook"><a href="<?php echo $facebook_link; ?>">Facebook</a></li>
<?php } ?>

<?php if( $facebook_link != "" ) { ?>
<li class="twitter"><a href="<?php echo $twitter_link; ?>">Twitter</a></li>
<?php } ?>

<?php if( $youtube_link != "" ) { ?>
<li class="youtube"><a href="<?php echo $youtube_link; ?>">Youtube</a></li>
<?php } ?>

<?php if( $flickr_link != "" ) { ?>
<li class="flickr"><a href="<?php echo $flickr_link; ?>">Flickr</a></li>
<?php } ?>

<?php if( $lastfm_link != "" ) { ?>
<li class="lastfm"><a href="<?php echo $lastfm_link; ?>">LastFM</a></li>
<?php } ?>

<?php if( $myspace_link != "" ) { ?>
<li class="myspace"><a href="<?php echo $myspace_link; ?>">MySpace</a></li>
<?php } ?>


<?php echo "</ul>";

echo $after_widget;

?>

<?php }

function custom_social_badge_options() {

$settings = get_option("widget_custom_social_badge");

// check if anything's been sent
if (isset($_POST['update_custom_social_badge'])) {

$settings['name'] = strip_tags(stripslashes($_POST['custom_social_badge_name']));

$settings['facebook_link'] = strip_tags(stripslashes($_POST['facebook_link']));
$settings['twitter_link'] = strip_tags(stripslashes($_POST['twitter_link']));
$settings['youtube_link'] = strip_tags(stripslashes($_POST['youtube_link']));
$settings['flickr_link'] = strip_tags(stripslashes($_POST['flickr_link']));
$settings['lastfm_link'] = strip_tags(stripslashes($_POST['lastfm_link']));
$settings['myspace_link'] = strip_tags(stripslashes($_POST['myspace_link']));

update_option("widget_custom_social_badge", $settings);
}

echo '<p><small>*leave empty if not used</small></p>';

echo '<p>
<label>Facebook Link:
<input id="facebook_link" name="facebook_link" type="text" class="widefat" value="'.$settings['facebook_link'].'" /></label>
</p>';

echo '<p>
<label>Twitter Link:
<input id="twitter_link" name="twitter_link" type="text" class="widefat" value="'.$settings['twitter_link'].'" /></label>
</p>';

echo '<p>
<label>Youtube Link:
<input id="youtube_link" name="youtube_link" type="text" class="widefat" value="'.$settings['youtube_link'].'" /></label>
</p>';

echo '<p>
<label>Flickr Link:
<input id="flickr_link" name="flickr_link" type="text" class="widefat" value="'.$settings['flickr_link'].'" /></label>
</p>';

echo '<p>
<label>LastFM Link:
<input id="lastfm_link" name="lastfm_link" type="text" class="widefat" value="'.$settings['lastfm_link'].'" /></label>
</p>';

echo '<p>
<label>MySpace Link:
<input id="myspace_link" name="myspace_link" type="text" class="widefat" value="'.$settings['myspace_link'].'" /></label>
</p>';

echo '<input type="hidden" id="update_custom_social_badge" name="update_custom_social_badge" value="1" />';

}

wp_register_sidebar_widget('custom_social_badge', TEMPLATE_DOMAIN . ' | Social Badge','custom_social_badge',array('description' => __('Displays your social network badge', TEMPLATE_DOMAIN)));
wp_register_widget_control('custom_social_badge', TEMPLATE_DOMAIN . ' | Social Badge','custom_social_badge_options', 200, 200);

?>