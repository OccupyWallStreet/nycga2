<?php

//////////////////////////
/// global add and update
//////////////////////////
function get_user_meta_values($my_id='',$metakey='') {
global $bp;
$my_id = $bp->displayed_user->id;
$get_user_meta_values = get_user_meta( $my_id, $metakey, true );
return $get_user_meta_values;
}

function update_user_meta_values($my_id= '',$metakey='', $metavalue='') {
global $bp;
$my_id = $bp->displayed_user->id;
$update_user_meta_values = update_user_meta( $my_id, $metakey, $metavalue );
return $get_user_meta_values;
}


///////////////////////////////////////////
/// profile inner slug - add component page
///////////////////////////////////////////

function bp_profile_header_setup() {
global $bp, $user_identity;
$display_user_link = $bp->displayed_user->domain;
$user_link = $display_user_link . $bp->settings->slug . '/';

bp_core_new_subnav_item(
array(
'name' => __( 'Flickr Youtube', TEMPLATE_DOMAIN ),
'slug' => 'flickr-youtube',
'parent_url' => $user_link,
'parent_slug' => $bp->settings->slug,
'screen_function' => 'bp_profile_social',
'position' => 10
)
);
}

function bp_profile_social() {
global $bp;
add_action( 'bp_template_content', 'bp_profile_social_output' );
bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function bp_profile_social_output() {
if ( file_exists( TEMPLATEPATH . '/_inc/custom-components/youtube-flickr.php'  ) ) {
load_template( TEMPLATEPATH . '/_inc/custom-components/youtube-flickr.php'  );
}
}

add_action( 'bp_setup_nav', 'bp_profile_header_setup' );


////////////////////////////////////////////////////////////////////////////
// init components builds
////////////////////////////////////////////////////////////////////////////

function output_flickr_youtube_content() {
global $bp, $wpdb;
$current_displayed_user = $bp->displayed_user->id;
$current_loggedin_user = $bp->loggedin_user->id;
$current_displayed_user_full_name = $bp->displayed_user->fullname;

$get_user_list = "SELECT user_login FROM " . $wpdb->base_prefix . "users WHERE ID= '" . $current_displayed_user . "' ORDER by ID limit 1";
$sql_get_user_list = $wpdb->get_var($get_user_list);

?>

<?php
if($current_displayed_user == $current_loggedin_user){
  $v_id = 'My';
  } else {
  $v_id = $sql_get_user_list . '&acute;s';
  }
?>


<?php
if($current_displayed_user == $current_loggedin_user){
  $u_id = 'My';
  } else {
  $u_id = $sql_get_user_list;
  }


$my_flickr_id = get_user_meta( $bp->displayed_user->id, 'user_flickr', true);
$my_video_id = get_user_meta( $bp->displayed_user->id, 'user_video', true);
$my_video_id_misc = get_user_meta( $bp->displayed_user->id, 'user_video_misc', true); ?>


<?php if( !bp_is_user_profile_edit() && !bp_is_user_change_avatar() ) { ?>
<?php if( $my_flickr_id ) { ?>
<div class="bp-widget">
<h4><?php echo $v_id; ?> <?php _e("Flickr",TEMPLATE_DOMAIN); ?><span><a href="http://www.flickr.com/photos/<?php echo $my_flickr_id; ?>"><?php _e("See All &rarr;",TEMPLATE_DOMAIN); ?></a></span></h4>
<ul id="myflickr">
<li>
<script type="text/javascript" src="http://www.flickr.com/badge_code_v2.gne?count=10&amp;display=latest&amp;size=s&amp;layout=x&amp;source=user&amp;user=<?php echo "$my_flickr_id"; ?>">
</script>
</li>
</ul>
</div>
<?php } ?>


<?php if( $my_video_id_misc == "" ) { ?>

<?php if( $my_video_id ) { ?>
<div class="bp-widget">
<h4><?php echo $v_id; ?> <?php _e('Video', TEMPLATE_DOMAIN); ?></h4>
<p>
<object data="http://www.youtube.com/v/<?php echo stripcslashes($my_video_id); ?>" type="application/x-shockwave-flash" width="480" height="295">
<param name="movie" value="http://www.youtube.com/v/<?php echo stripcslashes($my_video_id); ?>" /><param name="wmode" value="transparent" /></object>
</p>

</div>
<?php } ?>

<?php } else { ?>

<div class="bp-widget">
<h4><?php echo $v_id; ?> <?php _e('Video', TEMPLATE_DOMAIN); ?></h4>
<p>
<?php echo stripcslashes($my_video_id_misc); ?>
</p>
</div>

<?php } ?>



<?php } ?>


<?php }

add_action('bp_after_profile_content', 'output_flickr_youtube_content');




///////////////////////////////////////////
/// profile inner slug - add component page
///////////////////////////////////////////

function bp_social_media_header_setup() {
global $bp, $user_identity;
$display_user_link = $bp->displayed_user->domain;
$user_link = $display_user_link . $bp->settings->slug . '/';

bp_core_new_subnav_item(
array(
'name' => __( 'Social Media', TEMPLATE_DOMAIN ),
'slug' => 'social-media',
'parent_url' => $user_link,
'parent_slug' => $bp->settings->slug,
'screen_function' => 'bp_social_media_settings',
'position' => 10,
'item_css_id' => 'social-media'
)
);
}

function bp_social_media_settings() {
global $bp;
add_action( 'bp_template_content', 'bp_social_media_settings_output' );
bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function bp_social_media_settings_output() {
if ( file_exists( TEMPLATEPATH . '/_inc/custom-components/social-media.php'  ) ) {
load_template( TEMPLATEPATH . '/_inc/custom-components/social-media.php'  );
}
}

add_action( 'bp_setup_nav', 'bp_social_media_header_setup' );



////////////////////////////////////////////////////////////////////////////
// init components builds - ads
////////////////////////////////////////////////////////////////////////////


function output_ads_one_content() {
global $bp, $wpdb;
$current_displayed_user = $bp->displayed_user->id;
$current_loggedin_user = $bp->loggedin_user->id;
$current_displayed_user_full_name = $bp->displayed_user->fullname;
$get_user_list = "SELECT user_login FROM " . $wpdb->base_prefix . "users WHERE ID= '" . $current_displayed_user . "' ORDER by ID limit 1";
$sql_get_user_list = $wpdb->get_var($get_user_list);

?>

<?php
$my_profile_header_img = get_user_meta($bp->displayed_user->id, 'profile_header_img', true);
$my_profile_bg_img = get_user_meta($bp->displayed_user->id, 'profile_bg_img', true);
$my_profile_ads_box1 = get_user_meta($bp->displayed_user->id, 'profile_ads_box1', true);
$my_profile_ads_box2 = get_user_meta($bp->displayed_user->id, 'profile_ads_box2', true);
$my_profile_link_color = get_user_meta($bp->displayed_user->id, 'profile_link_color', true);
?>


<?php if( !bp_is_user_profile_edit() && !bp_is_user_change_avatar() ) { ?>
<?php if( $my_profile_ads_box1 != "" ) { ?>
<div class="profile-ads"><?php echo stripcslashes($my_profile_ads_box1); ?></div>
<?php } ?>

<?php } }

add_action('bp_before_profile_content', 'output_ads_one_content');



function output_ads_two_content() {
global $bp, $wpdb;
$current_displayed_user = $bp->displayed_user->id;
$current_loggedin_user = $bp->loggedin_user->id;
$current_displayed_user_full_name = $bp->displayed_user->fullname;
$get_user_list = "SELECT user_login FROM " . $wpdb->base_prefix . "users WHERE ID= '" . $current_displayed_user . "' ORDER by ID limit 1";
$sql_get_user_list = $wpdb->get_var($get_user_list);

?>

<?php
$my_profile_header_img = get_user_meta($bp->displayed_user->id, 'profile_header_img', true);
$my_profile_bg_img = get_user_meta($bp->displayed_user->id, 'profile_bg_img', true);
$my_profile_ads_boxtrue = get_user_meta($bp->displayed_user->id, 'profile_ads_boxtrue', true);
$my_profile_ads_box2 = get_user_meta($bp->displayed_user->id, 'profile_ads_box2', true);
$my_profile_link_color = get_user_meta($bp->displayed_user->id, 'profile_link_color', true);
?>


<?php if( !bp_is_user_profile_edit() && !bp_is_user_change_avatar() ) { ?>
<?php if( $my_profile_ads_box2 != "" ) { ?>
<div class="profile-ads"><?php echo stripcslashes($my_profile_ads_box2); ?></div>
<?php } ?>

<?php } }

add_action('bp_after_profile_content', 'output_ads_two_content');










////////////////////////////////////////////////////////////////////////////
// init components builds - social media
////////////////////////////////////////////////////////////////////////////

function output_social_media_content() {
global $bp, $wpdb;
$current_displayed_user = $bp->displayed_user->id;
$current_loggedin_user = $bp->loggedin_user->id;
$current_displayed_user_full_name = $bp->displayed_user->fullname;

$get_user_list = "SELECT user_login FROM " . $wpdb->base_prefix . "users WHERE ID= '" . $current_displayed_user . "' ORDER by ID limit 1";
$sql_get_user_list = $wpdb->get_var($get_user_list);

?>

<?php
if($current_displayed_user == $current_loggedin_user){
  $v_id = 'My';
  } else {
  $v_id = $sql_get_user_list . '&acute;s';
  }
?>


<?php
if($current_displayed_user == $current_loggedin_user){
  $u_id = 'My';
  } else {
  $u_id = $sql_get_user_list;
  }

$my_facebook_save = get_user_meta($bp->displayed_user->id, 'facebook_save', true);
$my_twitter_save = get_user_meta($bp->displayed_user->id, 'twitter_save', true);
$my_linked_save = get_user_meta($bp->displayed_user->id, 'linked_save', true);
$my_myspace_save = get_user_meta($bp->displayed_user->id, 'myspace_save', true);

$my_stumble_save = get_user_meta($bp->displayed_user->id, 'stumble_save', true);
$my_digg_save = get_user_meta($bp->displayed_user->id, 'digg_save', true);
$my_youtube_save = get_user_meta($bp->displayed_user->id, 'youtube_save', true);
$my_delicious_save = get_user_meta($bp->displayed_user->id, 'delicious_save', true);


?>


<?php if( !bp_is_user_profile_edit() && !bp_is_user_change_avatar() ) { ?>

<?php if($my_facebook_save == "" && $my_twitter_save == "" && $my_linked_save == "" && $my_myspace_save == "" && $my_stumble_save == "" && $my_digg_save == "" && $my_youtube_save == "" && $my_delicious_save == "") { ?>
<?php } else { ?>
<div class="bp-widget">
<h4><?php echo $v_id; ?> Social Media</h4>
<ul id="mysocialmedia">

<?php if( $my_facebook_save != "" ) { ?>
<li><a href="<?php echo stripcslashes($my_facebook_save); ?>"><img src="<?php echo get_template_directory_uri(); ?>/_inc/images/social/facebook.png" width="50" height="50" alt="facebook" /></a></li>
<?php } ?>

<?php if( $my_twitter_save != "" ) { ?>
<li><a href="<?php echo stripcslashes($my_twitter_save); ?>"><img src="<?php echo get_template_directory_uri(); ?>/_inc/images/social/twitter.png" width="50" height="50" alt="twitter" /></a></li>
<?php } ?>

<?php if( $my_linked_save != "" ) { ?>
<li><a href="<?php echo stripcslashes($my_linked_save); ?>"><img src="<?php echo get_template_directory_uri(); ?>/_inc/images/social/linked.png" width="50" height="50" alt="linked" /></a></li>
<?php } ?>

<?php if( $my_myspace_save != "" ) { ?>
<li><a href="<?php echo stripcslashes($my_myspace_save); ?>"><img src="<?php echo get_template_directory_uri(); ?>/_inc/images/social/myspace.png" width="50" height="50" alt="myspace" /></a></li>
<?php } ?>

<?php if( $my_stumble_save != "" ) { ?>
<li><a href="<?php echo stripcslashes($my_stumble_save); ?>"><img src="<?php echo get_template_directory_uri(); ?>/_inc/images/social/stumbleupon.png" width="50" height="50" alt="stumbleupon" /></a></li>
<?php } ?>

<?php if( $my_digg_save != "" ) { ?>
<li><a href="<?php echo stripcslashes($my_digg_save); ?>"><img src="<?php echo get_template_directory_uri(); ?>/_inc/images/social/digg.png" width="50" height="50" alt="digg" /></a></li>
<?php } ?>

<?php if( $my_youtube_save != "" ) { ?>
<li><a href="<?php echo stripcslashes($my_youtube_save); ?>"><img src="<?php echo get_template_directory_uri(); ?>/_inc/images/social/youtube.png" width="50" height="50" alt="youtube" /></a></li>
<?php } ?>

<?php if( $my_delicious_save != "" ) { ?>
<li><a href="<?php echo stripcslashes($my_delicious_save); ?>"><img src="<?php echo get_template_directory_uri(); ?>/_inc/images/social/delicious.png" width="50" height="50" alt="delicious" /></a></li>
<?php } ?>

</ul>
</div>

<?php } ?>

<?php } ?>


<?php }

add_action('bp_before_profile_content', 'output_social_media_content');




////////////////////////////////////////////////////////////////////////////
// init components builds - blog rss
////////////////////////////////////////////////////////////////////////////

function output_blog_rss_content() {

global $bp, $wpdb;
$current_displayed_user = $bp->displayed_user->id;
$current_loggedin_user = $bp->loggedin_user->id;
$current_displayed_user_full_name = $bp->displayed_user->fullname;

$get_user_list = "SELECT user_login FROM " . $wpdb->base_prefix . "users WHERE ID= '" . $current_displayed_user . "' ORDER by ID limit 1";
$sql_get_user_list = $wpdb->get_var($get_user_list);

?>

<?php
if($current_displayed_user == $current_loggedin_user){
  $v_id = 'My';
  } else {
  $v_id = $sql_get_user_list . '&acute;s';
  }
?>


<?php
if($current_displayed_user == $current_loggedin_user){
  $u_id = 'My';
  } else {
  $u_id = $sql_get_user_list;
  }

$my_blog_feed_save = get_user_meta($bp->displayed_user->id, 'blog_feed', true);
$my_blog_feed_count_save = get_user_meta($bp->displayed_user->id, 'blog_feed_count', true);
$my_blog_feed_show_content_save = get_user_meta($bp->displayed_user->id, 'blog_feed_show_content', true);

?>


<?php if( !bp_is_user_profile_edit() && !bp_is_user_change_avatar() ) { ?>

<?php if( $my_blog_feed_save != "" ) { ?>
<?php
if (file_exists(ABSPATH . WPINC . '/rss.php')) {
require_once (ABSPATH . WPINC . '/rss.php');
}
else if(file_exists(ABSPATH . WPINC . '/rss-functions.php')){
require_once(ABSPATH . WPINC . '/rss-functions.php');
}
?>

<div class="bp-widget">
<h4><?php echo $v_id; ?> <?php _e("Blog Feeds",TEMPLATE_DOMAIN); ?><span><a href="<?php echo stripcslashes($my_blog_feed_save); ?>"><?php _e("RSS Feed&rarr;",TEMPLATE_DOMAIN); ?></a></span></h4>
<ul id="myblogrssfeed">

<?php
$get_net_gfeed_url = $my_blog_feed_save;
$rss = @fetch_rss("$get_net_gfeed_url");
if ((isset($rss->items)) && (count($rss->items)>=1)) {
foreach(array_slice($rss->items,0,$my_blog_feed_count_save) as $item){

$feed_livelink = $item['link'];
$feed_livelink = str_replace("&", "&amp;", $item['link']);
$feed_livelink = str_replace("&amp;&amp;", "&amp;", $item['link']);

$feed_authorlink = $item['dc']['creator'];

$feed_categorylink = $item['category'];

$feed_livetitle = ucfirst($item['title']);

if (isset($item['description'])) {
$feed_descriptions = $item['description'];
$feed_descriptions = strip_tags($feed_descriptions);
$feed_descriptions = substr_replace($feed_descriptions,"...","150");
} else {
$feed_descriptions = '';
}

$msg .= "
<li class=\"feed-pull\"><h1>
<a href=\"".trim($feed_livelink)."\" rel=\"external nofollow\" title=\"".trim($feed_livetitle)."\">".trim($feed_livetitle)."</a>
</h1>";

if($my_blog_feed_show_content_save == 'yes') {
$msg .= "
<div class=\"rss-author\">posted by $feed_authorlink</div>
<div class=\"rss-content\">$feed_descriptions</div></li>\n";
}

}

echo "$msg";

} else {

_e("<div class=\"rss-content\">Currently there is no feed available</div>");

}

?>

</ul>
</div>

<?php } ?>

<?php } ?>


<?php }

add_action('bp_before_profile_content', 'output_blog_rss_content');





///////////////////////////////////////////
/// profile css slug - add component page
///////////////////////////////////////////

function bp_profile_css_header_setup() {
global $bp, $user_identity;
$display_user_link = $bp->displayed_user->domain;
$user_link = $display_user_link . $bp->settings->slug . '/';

bp_core_new_subnav_item(
array(
'name' => __( 'Profile CSS', TEMPLATE_DOMAIN ),
'slug' => 'profile-css',
'parent_url' => $user_link,
'parent_slug' => $bp->settings->slug,
'screen_function' => 'bp_profile_css_settings',
'position' => 10,
'item_css_id' => 'profile-css'
)
);
}

function bp_profile_css_settings() {
global $bp;
add_action( 'bp_template_content', 'bp_profile_css_settings_output' );
bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function bp_profile_css_settings_output() {
if ( file_exists( TEMPLATEPATH . '/_inc/custom-components/profile-css.php'  ) ) {
load_template( TEMPLATEPATH . '/_inc/custom-components/profile-css.php'  );
}
}

add_action( 'bp_setup_nav', 'bp_profile_css_header_setup' );




//////////////////output header profile/////////////////////////////////////

function output_profile_header_content() {
global $bp, $wpdb;
$current_displayed_user = $bp->displayed_user->id;
$current_loggedin_user = $bp->loggedin_user->id;
$current_displayed_user_full_name = $bp->displayed_user->fullname;
$get_user_list = "SELECT user_login FROM " . $wpdb->base_prefix . "users WHERE ID= '" . $current_displayed_user . "' ORDER by ID limit 1";
$sql_get_user_list = $wpdb->get_var($get_user_list);

?>

<?php
$my_profile_header_img = get_user_meta($bp->displayed_user->id, 'profile_header_img', true);
$my_profile_bg_img = get_user_meta($bp->displayed_user->id, 'profile_bg_img', true);
$my_profile_ads_boxtrue = get_user_meta($bp->displayed_user->id, 'profile_ads_boxtrue', true);
$my_profile_ads_box2 = get_user_meta($bp->displayed_user->id, 'profile_ads_box2', true);
$my_profile_link_color = get_user_meta($bp->displayed_user->id, 'profile_link_color', true);
?>


<?php if( !bp_is_user_profile_edit() && !bp_is_user_change_avatar() ) { ?>

<?php if( $my_profile_header_img != "" ) { ?>

<div id="profile-header-img"><img src="<?php echo stripcslashes($my_profile_header_img); ?>" alt="profile-header" /></div>

<?php } ?>


<?php } }

add_action('bp_before_member_home_content', 'output_profile_header_content');


function bp_custom_component_profile_css() {
print "<style>"; ?>
ul#mysocialmedia {margin: 0px; padding: 0px 0px 20px 0px; float: left; width: 100%; list-style: none;}
ul#mysocialmedia img {border: 3px solid #efefef;}
ul#mysocialmedia img:hover {border: 3px solid #CCCCCC;}
ul#mysocialmedia li {margin: 0px 10px 10px 0px; padding: 0px; float: left; width: auto !important; list-style: none;}
ul#myblogrssfeed {margin: 0px; padding: 0px; float: left; width: 100%; list-style: none;}
ul#myblogrssfeed li {margin: 0px 0px 18px; padding: 0px 0px 18px; float: left; width: 100%; list-style: none; border-bottom: 1px solid #ddd;}
ul#myblogrssfeed li h1 {margin: 0px; padding: 0px; float: left; width: 100%; list-style: none; font: 15px/20px Helvetica, Arial, sans-serif;}

ul#myblogrssfeed li div.rss-author {
background: transparent url(<?php echo get_template_directory_uri(); ?>/_inc/images/authors.gif) no-repeat left center;
margin: 0px; padding: 0px 0px 0px 23px; float: left; width: 90%; list-style: none; font: 11px/20px Helvetica, Arial, sans-serif;}

ul#myblogrssfeed li .rss-content {margin: 0px; padding: 8px 0px 0px; float: left; width: 100%; list-style: none; font: 12px/20px Helvetica, Arial, sans-serif;}

div.profile-ads {margin: 0px 0px 18px; padding: 12px 0px; float: left; width: 100%; clear:both; oveflow:hidden; }
div#profile-header-img img { margin: 0px 0px 18px; }
<?php print "</style>"; ?>
<?php }

add_action('wp_head', 'bp_custom_component_profile_css');



?>