<?php
////////////////////////////////////////////////////////////////////////////////
// multiple option page
////////////////////////////////////////////////////////////////////////////////
function _g($str) { return __($str, 'option-page'); }
////////////////////////////////////////////////////////////////////////////////
// theme option menu for Community
////////////////////////////////////////////////////////////////////////////////

if(file_exists( WP_CONTENT_DIR . '/themes/bp-social/style.css' )) {
$theme_data = wp_get_theme( 'bp-social' );
} else {
$theme_data = wp_get_theme();
}

$themename = $theme_data->name;

$theme_version = $theme_data->version;

$shortname = "tn";
$shortprefix = "_buddysocial_";
// get featured category
$wp_dropdown_rd_admin = get_categories('hide_empty=0');
$wp_getcat = array();
foreach ($wp_dropdown_rd_admin as $category_list) {
$wp_getcat[$category_list->cat_ID] = $category_list->category_nicename;
}
$category_bulk_list = array_unshift($wp_getcat, "Choose a category");
$choose_count = array("Select a number","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19","20");

if(function_exists('bp_get_root_domain')) {
$the_privacy_root = bp_get_root_domain(); } else {
$the_privacy_root = site_url();
}

if( function_exists('bp_get_root_slug')) {
    $member_reg_slug = bp_get_root_slug( 'register' );
} else {
    $member_reg_slug = false;
}

////////////////////////////////////////////////////////////////////////////////
// theme option menu for buddypress social
////////////////////////////////////////////////////////////////////////////////

$options = array (

//blog layout settings
array(
"name" => __("Choose your <strong>homepage layout</strong> settings",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "blog_home_layout_style",
"inblock" => "layout",
"type" => "select",
"std" => "3-column",
"options" => array("3-column","2-column")),

array(
"name" => __("Choose your <strong>blog layout</strong> settings",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "blog_index_layout_style",
"inblock" => "layout",
"type" => "select",
"std" => "3-column",
"options" => array("3-column","2-column")),

// featured post and gallery
array(
"name" => __("Enable or disable the <strong>homepage featured block</strong><br /><em>*default: disable</em><br /><em>if enable, the featured block in home will be showed and below setting will be active when saved</em>",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "blog_featured_style_status",
"inblock" => "gallery",
"type" => "select",
"std" => "disable",
"options" => array("disable", "enable")),

array(
"name" => __("Choose your <strong>homepage featured block style</strong><br /><em>there are 2 style to choose from article and gallery</em>",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "blog_featured_style",
"inblock" => "gallery",
"type" => "select",
"std" => "gallery",
"options" => array("gallery","article")),


array(
"name" => __("Insert the post id for your featured post<br />*example: 1,3,44,123,678<br /><em>leave blank if you want to use the below category id based features</em>",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "featured_post_id",
"inblock" => "gallery",
"type" => "text",
"std" => "",
),

array(
"name" => __("Insert the category id for your featured post<br />*example: 1,3,44,123,678<br /><em>leave blank if you want to use the top post id based features</em>",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "featured_cat_id",
"inblock" => "gallery",
"type" => "text",
"std" => "",
),

array(
"name" => __("How many post from <strong>choosen category id</strong> you want to show?<br /><em>*only effective if you choose category featured</em>",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "featured_count",
"inblock" => "gallery",
"type" => "select",
"std" => "",
"options" => $choose_count),


array(
"name" => __("Change your desired <strong>custom field</strong> for featured post images<br /><em>Default: thumbs</em><br /><em>more about <a href='http://codex.wordpress.org/Custom_Fields'>custom field</a> usage here</em>",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "featured_custom_field",
"inblock" => "gallery",
"type" => "text",
"std" => "",
),

array (
"name" => __("Choose your featured block <strong>background color</strong><br /><em>*this will change the featured block background color for gallery block style</em>",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "featured_bg_color",
"inblock" => "gallery",
"std" => "",
"type" => "colorpicker"),

array (
"name" => __("Choose your featured block <strong>slider background color</strong><br /><em>*this will changed the up and down slider content background</em>",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "featured_slider_bg_color",
"inblock" => "gallery",
"std" => "",
"type" => "colorpicker"),

array (
"name" => __("Choose your featured block <strong>text color</strong><br /><em>*this will changed the featured gallery block text color</em>",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "featured_text_color",
"inblock" => "gallery",
"std" => "",
"type" => "colorpicker"),


//css
array(
"name" => __("Choose your body font",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix  . "body_font",
"type" => "select-preview",
"box"=> "1",
"inblock" => "css",
"std" => "Lucida Grande, Lucida Sans, sans-serif",
			"options" => array(
            "Lucida Grande, Lucida Sans, sans-serif",
											"Cantarell, arial, serif",
											"Cardo, arial, serif",
										    "Courier New, Courier, monospace",
											"Crimson Text, arial, serif",
											"Droid Sans, arial, serif",
											"Droid Serif, arial, serif",
								            "Garamond, Georgia, serif",
											"Georgia, arial, serif",
								            "Helvetica, Arial, sans-serif",
											"IM Fell DW Pica, arial, serif",
											"Josefin Sans Std Light, arial, serif",
											"Lobster, arial, serif",
											"Lucida Sans Unicode, Lucinda Grande, sans-serif",
											"Molengo, arial, serif",
											"Neuton, arial, serif",
											"Nobile, arial, serif",
											"OFL Sorts Mill Goudy TT, arial, serif",
											"Old Standard TT, arial, serif",
											"Reenie Beanie, arial, serif",
											"Tahoma, sans-serif",
											"Tangerine, arial, serif",
								            "Trebuchet MS, sans-serif",
								            "Verdana, sans-serif",
											"Vollkorn, arial, serif",
											"Yanone Kaffeesatz, arial, serif",
                                            "Just Another Hand, arial, serif",
                                            "Terminal Dosis Light, arial, serif",
                                            "Ubuntu, arial, serif"
            )
            ),



array(
"name" => __("Choose your headline font",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "headline_font",
"type" => "select-preview",
"box"=> "1",
"inblock" => "css",
"std" => "Lucida Grande, Lucida Sans, sans-serif",
			"options" => array(
            "Lucida Grande, Lucida Sans, sans-serif",
											"Cantarell, arial, serif",
											"Cardo, arial, serif",
										    "Courier New, Courier, monospace",
											"Crimson Text, arial, serif",
											"Droid Sans, arial, serif",
											"Droid Serif, arial, serif",
								            "Garamond, Georgia, serif",
											"Georgia, arial, serif",
								            "Helvetica, Arial, sans-serif",
											"IM Fell DW Pica, arial, serif",
											"Josefin Sans Std Light, arial, serif",
											"Lobster, arial, serif",
											"Lucida Sans Unicode, Lucinda Grande, sans-serif",
											"Molengo, arial, serif",
											"Neuton, arial, serif",
											"Nobile, arial, serif",
											"OFL Sorts Mill Goudy TT, arial, serif",
											"Old Standard TT, arial, serif",
											"Reenie Beanie, arial, serif",
											"Tahoma, sans-serif",
											"Tangerine, arial, serif",
								            "Trebuchet MS, sans-serif",
								            "Verdana, sans-serif",
											"Vollkorn, arial, serif",
											"Yanone Kaffeesatz, arial, serif",
                                            "Just Another Hand, arial, serif",
                                            "Terminal Dosis Light, arial, serif",
                                            "Ubuntu, arial, serif"
            )
            ),

array(
"name" => __("Your prefered font size (in pixel) <em>no need to enter the px..just numeric like 11,12,13..etc</em>",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "font_size",
"box"=> "1",
"inblock" => "css",
"type" => "text",
"std" => "",
),

array(
"name" => __("Your prefered font line height (in pixel) <em>no need to enter the px..just numeric like 11,12,13..etc</em>",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "font_line_height",
"box"=> "1",
"inblock" => "css",
"type" => "text",
"std" => "",
),

array (
"name" => __("Choose your blog <strong>global links color</strong><br /><em>*this will changed the global blog links color</em>",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "blog_global_links_color",
"inblock" => "css",
"std" => "",
"type" => "colorpicker"),

array (
"name" => __("Choose your blog <strong>global links hover color</strong><br /><em>*this will changed the global blog links hover color</em>",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "blog_global_links_hover_color",
"inblock" => "css",
"std" => "",
"type" => "colorpicker"),



//top-header
array (
"name" => __("Choose your header <strong>background color</strong><br /><em>*this will changed the header and status background color</em>",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "blog_header_bg_color",
"inblock" => "top-header",
"std" => "",
"type" => "colorpicker"),

array (
"name" => __("Choose your header <strong>background gradient secondary color</strong><br /><em>*this will add gradient to background color</em><br /><em>leave blank if you want to use main color only</em>",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "blog_header_bg_sec_color",
"inblock" => "top-header",
"std" => "",
"type" => "colorpicker"),


array (
"name" => __("Choose your header <strong>text color</strong><br /><em>*this will changed the header normal text color</em>",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "blog_header_text_color",
"inblock" => "top-header",
"std" => "",
"type" => "colorpicker"),


array (
"name" => __("Choose your header <strong>text link color</strong><br /><em>*this will changed the header text link color</em>",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "blog_header_text_link_color",
"inblock" => "top-header",
"std" => "",
"type" => "colorpicker"),

array (
"name" => __("Choose your header <strong>text link hover color</strong><br /><em>*this will changed the header text link hover color</em>",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "blog_header_text_link_hover_color",
"inblock" => "top-header",
"std" => "",
"type" => "colorpicker"),




//intro
array(
"name" => __("Edit your welcome and sign up message here&nbsp;&nbsp;&nbsp;<em>*html allowed</em><br /><em>welcome message only showed to non member or non logged in user</em>",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "message_text",
"box"=> "2",
"inblock" => "intro",
"type" => "textarea",
"std" => "<strong>Welcome to Buddypress Social, we connect you with your friends, family and co-worker</strong>
<p>Start uploading picture, videos and write about your activity to share it with friends and family today. <a href='http://yoursite.com/register'>Sign-up here &raquo;</a></p>",
),

array (
"name" => __("Choose your blog <strong>intro header background main color</strong><br /><em>*this will changed the intro header background color</em>",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "blog_intro_header_color",
"inblock" => "intro",
"std" => "",
"type" => "colorpicker"),

array (
"name" => __("Choose your blog <strong>intro header background gradient secondary color</strong><br /><em>*this will add gradient to background color</em><br /><em>leave blank if you want to use main color only</em>",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "blog_intro_header_sec_color",
"inblock" => "intro",
"std" => "",
"type" => "colorpicker"),


array (
"name" => __("Choose your blog <strong>intro header text color</strong><br /><em>*this will changed the intro header text color</em>",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "blog_intro_text_color",
"inblock" => "intro",
"std" => "",
"type" => "colorpicker"),

array (
"name" => __("Choose your blog <strong>intro header link color</strong><br /><em>*this will changed the intro header link color</em>",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "blog_intro_header_link_color",
"inblock" => "intro",
"std" => "",
"type" => "colorpicker"),

array (
"name" => __("Choose your blog <strong>intro header hover link color</strong><br /><em>*this will changed the intro header link hover color</em>",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "blog_intro_header_link_hover_color",
"inblock" => "intro",
"std" => "",
"type" => "colorpicker"),


array(
"name" => __("If you want to use a video for the intro header, insert your youtube video id here&nbsp;&nbsp;&nbsp;<em>*html allowed</em><br /><em>http://www.youtube.com/watch?v=<strong>video_id_is_here</strong></em>",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "blog_intro_header_video",
"box"=> "2",
"inblock" => "intro",
"type" => "text",
"std" => "",
),


array(
"name" => __("If you want to use other video like vimeo, google video or any (not youtube) for the intro header, insert your video embed code here&nbsp;&nbsp;&nbsp;<em>*html allowed</em>",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "blog_intro_header_video_alt",
"box"=> "2",
"inblock" => "intro",
"type" => "textarea",
"std" => "",
),

array(
"name" => __("If you want to use a picture or image for the intro header, insert your image full url here<br /><em>*suitable size 480x260</em><br /><em>*you can upload your image in <a taget='_blank' href='media-new.php'>media panel</a> and copy paste the url here</em>",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "blog_intro_header_image",
"box"=> "2",
"inblock" => "intro",
"type" => "text",
"std" => "",
),



//navigations
array (
"name" => __("Choose your blog <strong>dropdown navigation background color</strong><br /><em>*this will changed the dropdown and subpage background color</em>",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "blog_subnav_color",
"inblock" => "nav",
"std" => "",
"type" => "colorpicker"),

array (
"name" => __("Choose your blog <strong>dropdown navigation background hover color</strong><br /><em>*this will changed the dropdown and subpage background hover color</em>",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "blog_subnav_hover_color",
"inblock" => "nav",
"std" => "",
"type" => "colorpicker"),


array (
"name" => __("Choose your blog <strong>dropdown navigation link color</strong><br /><em>*this will changed the dropdown and subpage link color</em>",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "blog_subnav_link_color",
"inblock" => "nav",
"std" => "",
"type" => "colorpicker"),


array (
"name" => __("Choose your blog <strong>dropdown navigation background hover link color</strong><br /><em>*this will changed the dropdown and subpage background hover link color</em>",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "blog_subnav_link_hover_color",
"inblock" => "nav",
"std" => "",
"type" => "colorpicker"),


// blog post
array(
"name" => __("Choose your blog post style<br /><em>*Choose your post style for archives and category</em>", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "blog_post_style",
"inblock" => "post",
"type" => "select",
"std" => "full post",
"options" => array("full post","excerpt post","featured thumbnail with excerpt post")),

array(
"name" => __("Enable or disable post meta<br /><em>*optional - you can remove post category, post tag, post date and post author</em>", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "post_meta_status",
"inblock" => "post",
"type" => "select",
"std" => "enable",
"options" => array("enable","disable")),

array(
"name" => __("Enable Facebook like in single post", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "facebook_like_status",
"inblock" => "post",
"type" => "select",
"std" => "disable",
"options" => array("disable", "enable")),



//button style
array (
"name" => __("Choose global button <strong>background color</strong><br /><em>*this will changed the submit and button background color</em>",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "button_bg_color",
"inblock" => "button",
"std" => "",
"type" => "colorpicker"),

array (
"name" => __("Choose global button <strong>border color</strong><br /><em>*this will changed the submit and button border color</em>",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "button_border_color",
"inblock" => "button",
"std" => "",
"type" => "colorpicker"),

array (
"name" => __("Choose global button <strong>text color</strong><br /><em>*this will changed the submit and button text color</em>",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "button_text_color",
"inblock" => "button",
"std" => "",
"type" => "colorpicker"),



//buddypress

array(
"name" => __("Choose your <strong>member/component page layout</strong> settings<br /><em>*you can select which layout style you prefered</em>",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "member_page_layout_style",
"header-title" => __("BuddyPress Profile and Component Layout", TEMPLATE_DOMAIN),
"inblock" => "buddypress",
"type" => "select",
"std" => "3-column",
"options" => array("3-column","2-column","1-column")),



//bp privacy
array(
"name" => __("Do you want to enable <strong>privacy</strong> on all members profile for not logged in user<br /><em>* only logged in user can view members profile and members directory. 'disable' by default</em>", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "privacy_status",
"box"=> "1",
"header-title" => __("Global Privacy Setting", TEMPLATE_DOMAIN),
"inblock" => "buddypress",
"type" => "select",
"std" => "disable",
"options" => array("disable","enable")),

array(
"name" => __("if you enable the <strong>privacy</strong> on all members profile for none logged in user, insert the full url link they will be redirect to for non logged in users<br /><em>*optional - leave empty for default<br />default are buddypress register link<br /> " . site_url() . '/' . ( isset($member_reg_slug) ? $member_reg_slug : '' ) . '/' . "</em>", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "privacy_redirect",
"box"=> "1",
"inblock" => "buddypress",
"type" => "text",
"std" => "",
),


array(
"name" => __("Do you want to enable <strong>friend only privacy</strong> for user profile<br /><em>* only friend can view friend profile. network/super admin were exclude from this condition. 'disable' by default</em>", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "friend_privacy_status",
"header-title" => __("Users Privacy Setting", TEMPLATE_DOMAIN),
"box"=> "1",
"bp-only" => $bp_existed,
"inblock" => "buddypress",
"type" => "select",
"std" => "disable",
"options" => array("disable","enable")),

array(
"name" => __("if you enable the <strong>friend privacy</strong> for user profile, insert the full url link they will be redirect when viewing a none friend user<br /><em>*optional - leave empty for default<br />default are buddypress homepage link<br /> " . site_url() . '/' . "</em>", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "friend_privacy_redirect",
"box"=> "1",
"inblock" => "buddypress",
"type" => "text",
"std" => "",
),


array(
"name" => __("Do you want to allowed only <strong>admin and moderators</strong> to create group? <em>* if yes, normal users cannot create group and can only join groups created by admin and moderators. 'no' by default</em>", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "create_group_status",
"header-title" => __("Groups Privacy Setting", TEMPLATE_DOMAIN),
"box"=> "1",
"inblock" => "buddypress",
"type" => "select",
"std" => "no",
"options" => array("no","yes")),

array(
"name" => __("if you enable for the only <strong>admins and editors</strong> to create group, insert the full url link they will be redirect to for non admins and editors users when they click <strong>create group</strong> button<br /><em>*optional - leave empty for default<br />default are buddypress root domain<br /> " . ( isset($the_privacy_root) ? $the_privacy_root : '' ) . '/' . "</em>", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "create_group_redirect",
"box"=> "1",
"inblock" => "buddypress",
"type" => "text",
"std" => "",
),

array(
"name" => __("Do you want to enable facebook LIKE in activity stream", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "stream_facebook_like_status",
"inblock" => "buddypress",
"type" => "select",
"std" => "disable",
"options" => array("disable","enable")),

// buddypress meta
array (
"name" => __("<strong>SPAN Meta block</strong> background color", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "span_meta_color",
"header-title" => __("BuddyPress Span Meta CSS Setting", TEMPLATE_DOMAIN),
"header-img-link" => '/_inc/admin/spanmeta.png',
"inblock" => "buddypress",
"std" => "",
"type" => "colorpicker"),


array (
"name" => __("<strong>SPAN Meta block</strong> border color", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "span_meta_border_color",
"inblock" => "buddypress",
"std" => "",
"type" => "colorpicker"),


array (
"name" => __("<strong>SPAN Meta block</strong> text color", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "span_meta_text_color",
"inblock" => "buddypress",
"std" => "",
"type" => "colorpicker"),


array (
"name" => __("<strong>SPAN Meta block</strong> background hover color", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "span_meta_hover_color",
"inblock" => "buddypress",
"std" => "",
"type" => "colorpicker"),

array (
"name" => __("<strong>SPAN Meta block</strong> hover border color", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "span_meta_border_hover_color",
"inblock" => "buddypress",
"std" => "",
"type" => "colorpicker"),


array (
"name" => __("<strong>SPAN Meta block</strong> hover text color", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "span_meta_text_hover_color",
"inblock" => "buddypress",
"std" => "",
"type" => "colorpicker"),


// header
array(
"name" => __("Insert your logo full url here<br /><em>*you can upload your logo in <a href='media-new.php'>media panel</a> and copy paste the url here</em>",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "header_logo",
"box"=> "2",
"inblock" => "header",
"type" => "text",
"std" => "",
),


array(
"name" => __("Do you want to enable custom image header?", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "header_on",
"box"=> "2",
"inblock" => "header",
"type" => "select",
"std" => "disable",
"options" => array("disable","enable")),


array(
"name" => __("Your prefered custom image header height",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "image_height",
"box"=> "2",
"inblock" => "header",
"type" => "text",
"std" => "150",
),

array(
"name" => __("You can input advertisment or script code here&nbsp;&nbsp;&nbsp;<em>*html allowed</em><br/>*Optional Usage if you do not want to use custom image header<br /><br /><em>due to security reason only main blog (blog id 1) had this options</em>",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "ads_code",
"box"=> "2",
"inblock" => "header",
"type" => "textarea",
"std" => "",
)


);


function buddysocial_admin() {
global $themename, $theme_version, $shortname, $shortprefix, $options, $blog_id, $bp_existed, $bp_front_is_activity;
if ( isset($_REQUEST['saved']) && $_REQUEST['saved'] ) echo '<div id="message" class="updated fade"><p><strong>'. $themename . __(' settings saved.', TEMPLATE_DOMAIN) . '</strong></p></div>';
if ( isset($_REQUEST['reset']) && $_REQUEST['reset'] ) echo '<div id="message" class="updated fade"><p><strong>'. $themename . __(' settings reset.', TEMPLATE_DOMAIN) . '</strong></p></div>';
?>


<div id="options-panel">
<div id="options-head"><h2><?php echo $themename; ?> <?php _e("Theme Options", TEMPLATE_DOMAIN); ?></h2>
<div class="theme-versions"><?php _e("Version", TEMPLATE_DOMAIN); ?> <?php echo $theme_version; ?></div>
</div>

<div id="sbtabs">

<div class="tabmc">
<ul class="ui-tabs-nav" id="tabm">
<?php
$value_var_global = array('layout','gallery','css','top-header','intro','header','nav','post','buddypress','button');
?>

<?php if( is_main_site() ) {  ?>
<li><a href="#tab1"><?php _e("Admin Bar Settings", TEMPLATE_DOMAIN); ?></a></li>
<?php } ?>

<li><a href="#tab2"><?php _e("Layout Settings",TEMPLATE_DOMAIN); ?></a></li>
<li><a href="#tab3"><?php _e("Gallery Settings",TEMPLATE_DOMAIN); ?></a></li>
<li><a href="#tab4"><?php _e("CSS Settings",TEMPLATE_DOMAIN); ?></a></li>
<li><a href="#tab5"><?php _e("Top Header Settings",TEMPLATE_DOMAIN); ?></a></li>
<li><a href="#tab6"><?php _e("Intro Settings",TEMPLATE_DOMAIN); ?></a></li>
<li><a href="#tab7"><?php _e("Header Settings",TEMPLATE_DOMAIN); ?></a></li>
<li><a href="#tab8"><?php _e("Navigation Settings",TEMPLATE_DOMAIN); ?></a></li>
<li><a href="#tab9"><?php _e("Post Settings",TEMPLATE_DOMAIN); ?></a></li>
<?php if($bp_existed == 'true') { //only showed if buddypress installed ?>
<li><a href="#tab10"><?php _e("BuddyPress Settings",TEMPLATE_DOMAIN); ?></a></li>
<?php } ?>
<li><a href="#tab11"><?php _e("Button Settings",TEMPLATE_DOMAIN); ?></a></li>
</ul>
</div>


<div class="tabmc-right">

<div class="tabc">

<form action="" method="post">


<ul style="" class="ui-tabs-panel" id="tab1">
<li>
<?php
if( is_multisite() ) {
  if( is_main_site() ) {

  $bg_color = 'multisite_adminbar_bg_color';
  $bg_hover_color = 'multisite_adminbar_hover_bg_color'; ?>

<div id="<?php echo $bg_color; ?>" class="tab-option">
<div class="description"><?php _e( 'Choose your adminbar background color', TEMPLATE_DOMAIN ); ?><br /><span></span></div>
<div class="input-option"><p><input class="color {required:false,hash:true}" name="<?php echo $bg_color; ?>" id="colorpickerField88" type="text" value="<?php if ( get_site_option( $bg_color ) != "" ) { echo get_site_option( $bg_color ); } ?>" /></p></div>
</div>
                                  <?php if($bp_existed == 'true'): ?>
<div id="<?php echo $bg_hover_color; ?>" class="tab-option">
<div class="description"><?php _e( 'Choose your adminbar background hover color', TEMPLATE_DOMAIN ); ?><br /><span></span></div>
<div class="input-option"><p><input class="color {required:false,hash:true}" name="<?php echo $bg_hover_color; ?>" id="colorpickerField89" type="text" value="<?php if ( get_site_option( $bg_hover_color ) != ""  ) { echo get_site_option( $bg_hover_color ); } ?>" /></p></div>
</div>
<?php endif; ?>

<?php } } else {

  $bg_color = $shortname . $shortprefix . 'adminbar_bg_color';
  $bg_hover_color = $shortname . $shortprefix . 'adminbar_hover_bg_color'; ?>

<div id="<?php echo $bg_color; ?>" class="tab-option">
<div class="description"><?php _e( 'Choose your adminbar background color', TEMPLATE_DOMAIN ); ?><br /><span></span></div>
<div class="input-option"><p><input class="color {required:false,hash:true}" name="<?php echo $bg_color; ?>" id="colorpickerField88" type="text" value="<?php if ( get_option( $bg_color ) != "" ) { echo get_option( $bg_color ); } ?>" /></p></div>
</div>

<?php if($bp_existed == 'true'): ?>
<div id="<?php echo $bg_hover_color; ?>" class="tab-option">
<div class="description"><?php _e( 'Choose your adminbar background hover color', TEMPLATE_DOMAIN ); ?><br /><span></span></div>
<div class="input-option"><p><input class="color {required:false,hash:true}" name="<?php echo $bg_hover_color; ?>" id="colorpickerField89" type="text" value="<?php if ( get_option( $bg_hover_color ) != ""  ) { echo get_option( $bg_hover_color ); } ?>" /></p></div>
</div>
   <?php endif; ?>

<?php } ?>

</li>
</ul>


<?php $vc = 2; foreach ($value_var_global as $value_var) { ?>
<ul style="" class="ui-tabs-panel<?php if($vc > 0) { ?> ui-tabs-hide<?php } ?>" id="tab<?php echo $vc; ?>">
<li>
<?php $i = 0;
foreach ($options as $value) { ?>


<?php if (isset($value['inblock']) && ($value['inblock'] == $value_var) && isset($value['header-title']) && ($value['header-title'] != "")) { // if we got header title for option ?>
<h4><?php echo stripslashes($value['header-title']); ?><?php if(!empty($value['header-img-link'])) { ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="whatsthis" href="#thumb">(What's this?)<span><img src="<?php echo get_template_directory_uri(); ?><?php echo stripslashes($value['header-img-link']); ?>" /></span></a><?php } ?></h4>
<?php } ?>


<?php if (isset($value['inblock']) && ($value['inblock'] == $value_var) && isset($value['type']) && ($value['type'] == "text")) { // setting ?>
<div class="tab-option">
<div class="description"><?php echo isset($value['name'])?$value['name']:''; ?><br /><span><?php echo isset($value['description'])?$value['description']:''; ?></span></div>
<div class="input-option"><p><input name="<?php echo $value['id']; ?>" class="myfield" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo stripslashes(get_option( $value['id']) ); } else { echo stripslashes($value['std']); } ?>" /></p></div>
</div>

<?php } else if (isset($value['inblock']) && ($value['inblock'] == $value_var) && isset($value['header-title']) && ($value['type'] == "ajax-file-upload")) { // setting ?>

<div class="tab-option">
<div class="description"><?php echo isset($value['name'])?$value['name']:''; ?><br /><span><?php echo isset($value['description'])?$value['description']:''; ?></span></div>
<div class="input-option">
</div>
</div>

<?php } elseif (isset($value['inblock']) && ($value['inblock'] == $value_var) && isset($value['type']) && ($value['type'] == "checkbox") ) { // setting ?>

<?php if(get_option($value['id'])) { $checked = "checked=\"checked\""; } else { $checked = ""; } ?>
<div class="checkbox-box">
<div class="description"><p><input type="<?php echo $value['type']; ?>" class="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="<?php echo $value['id']; ?>" <?php echo $checked; ?> />&nbsp;&nbsp;<?php echo isset($value['name'])?$value['name']:''; ?></p></div></div>

<?php } elseif (isset($value['inblock']) && ($value['inblock'] == $value_var) && isset($value['type']) && ($value['type'] == "textarea")) { // setting ?>

<div class="tab-option">
<?php
$valuex = $value['id'];
$valuey = stripslashes($valuex);
$video_code = get_option($valuey);
?>
<div class="description"><?php echo isset($value['name'])?$value['name']:''; ?><br /><span><?php echo isset($value['description'])?$value['description']:''; ?></span></div>
<div class="input-option"><p><textarea name="<?php echo $valuey; ?>" class="mytext" cols="40%" rows="8" /><?php if ( get_option($valuey) != "") { echo stripslashes($video_code); } else { echo $value['std']; } ?>
</textarea></p></div>
</div>
<?php } elseif (isset($value['inblock']) && ($value['inblock'] == $value_var) && isset($value['type']) && ($value['type'] == "colorpicker") ) { // setting ?>
<?php $i == $i++ ; ?>
<div class="tab-option">
<div class="description"><?php echo isset($value['name'])?$value['name']:''; ?><br /><span><?php echo isset($value['description'])?$value['description']:''; ?></span></div>
<div class="input-option"><p><input class="color {required:false,hash:true}" name="<?php echo $value['id']; ?>" id="colorpickerField<?php echo $i; ?>" type="text" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" /></p></div>
</div>

<?php } elseif (isset($value['inblock']) && ($value['inblock'] == $value_var) && isset($value['type']) && ($value['type'] == "select-preview") ) { // setting ?>
<div class="tab-option">
<div class="description"><?php echo isset($value['name'])?$value['name']:''; ?><br /><span><?php echo isset($value['description'])?$value['description']:''; ?></span></div>
<div class="input-option"><p><select name="<?php echo $value['id']; ?>" class="myselect" id="<?php echo $value['id']; ?>">
<?php foreach ($value['options'] as $option) { ?>
<option style="font-family:<?php echo $option; ?>;" <?php if ( get_option( $value['id'] ) == $option) { echo ' selected="selected"'; } elseif ($option == get_option( $value['std']) ) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option>
<?php } ?>
</select>
</p>
</div>
</div>

<?php } elseif (isset($value['inblock']) && ($value['inblock'] == $value_var) && isset($value['type']) && ($value['type'] == "select") ) { // setting ?>
<div class="tab-option">
<div class="description"><?php echo isset($value['name'])?$value['name']:''; ?><br /><span><?php echo isset($value['description'])?$value['description']:''; ?></span></div>
<div class="input-option"><p><select name="<?php echo $value['id']; ?>" class="myselect" id="<?php echo $value['id']; ?>">
<?php foreach ($value['options'] as $option) { ?>
<option<?php if ( get_option( $value['id'] ) == $option) { echo ' selected="selected"'; } elseif ($option == $value['std']) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option>
<?php } ?>
</select>
</p>
</div>
</div>
<?php } elseif (isset($value['inblock']) && ($value['inblock'] == $value_var) && isset($value['type']) && ($value['type'] == "custom-radio") ) { // setting ?>
<div class="tab-option">
<div class="description"><?php echo isset($value['name'])?$value['name']:''; ?><br /><span><?php echo isset($value['description'])?$value['description']:''; ?></span></div>
<div class="input-option"><ul id="preset-ul">
<?php foreach ($value['options'] as $option) {
$screenshot_img = substr($option,0,-4);
$radio_setting = get_option($value['id']);
if($radio_setting != '') {
if (get_option($value['id']) == $option) { $checked = "checked=\"checked\""; } else { $checked = ""; }
} else {
if(get_option($value['id']) == $value['std'] ){ $checked = "checked=\"checked\""; } else { $checked = ""; }
} ?>

<li>
<div class="theme-img"><img src="<?php echo get_template_directory_uri(); ?>/_inc/preset-styles/images/<?php echo $screenshot_img . '.png'; ?>" alt="<?php echo $screenshot_img; ?>" /></div>
<input type="radio" name="<?php echo $value['id']; ?>" value="<?php echo $option; ?>" <?php echo $checked; ?> /><?php echo $option; ?>
</li>

<?php } ?>
</ul>
</div>
</div>
<?php } ?>
<?php } ?>
</li></ul>
<?php $vc++; } ?>

<div class="submit">
<input name="save" type="submit" class="button-primary sbutton" value="<?php echo esc_attr(__('Save All Options',TEMPLATE_DOMAIN)); ?>" />
<input type="hidden" name="action" value="save" />
</div>
</form>

</div>

</div><!-- tabmc-right -->

</div><!-- sbtabs -->

<div id="reset-box">
<form method="post">
<div class="submit">
<input name="reset" type="submit" class="sbutton" onclick="return confirm('Are you sure you want to reset all saved settings?. This action cannot be restore.')" value="<?php echo esc_attr(__('Reset All Options',TEMPLATE_DOMAIN)); ?>" />
<input type="hidden" name="action" value="reset" />&nbsp;&nbsp;<?php _e("by pressing this reset button, all your saved setting for this theme will be deleted and restore to factory default.",TEMPLATE_DOMAIN); ?>
</div>
</form>
</div>

</div><!-- end option-panel -->
<?php
}


function buddysocial_admin_register() {
global $themename, $shortname, $shortprefix, $options;
if ( isset($_GET["page"]) && isset($_GET["page"]) && $_GET['page'] == 'options-functions.php' ) {
if ( isset($_REQUEST['action']) && 'save' == $_REQUEST['action'] ) {

if( is_multisite()):

$post_bg_color = $_POST[ 'multisite_adminbar_bg_color' ];
$post_hover_bg_color = $_POST[ 'multisite_adminbar_hover_bg_color' ];

update_site_option('multisite_adminbar_bg_color', $post_bg_color );
update_site_option('multisite_adminbar_hover_bg_color', $post_hover_bg_color );

if( isset( $_REQUEST[ 'multisite_adminbar_bg_color' ] ) ) {
update_site_option('multisite_adminbar_bg_color', $post_bg_color );
} else {
delete_site_option('multisite_adminbar_bg_color' );
}

if( isset( $_REQUEST[ 'multisite_adminbar_hover_bg_color' ] ) ) {
update_site_option('multisite_adminbar_hover_bg_color', $post_hover_bg_color );
} else {
delete_site_option('multisite_adminbar_hover_bg_color' );
}

  else:


$post_bg_color = $_POST[ 'tn_buddysocial_adminbar_bg_color' ];
$post_hover_bg_color = $_POST[ 'tn_buddysocial_adminbar_hover_bg_color' ];

update_option('tn_buddysocial_adminbar_bg_color', $post_bg_color );
update_option('tn_buddysocial_adminbar_hover_bg_color', $post_hover_bg_color );

if( isset( $_REQUEST[ 'tn_buddysocial_adminbar_bg_color' ] ) ) {
update_option('tn_buddysocial_adminbar_bg_color', $post_bg_color );
} else {
delete_option('tn_buddysocial_adminbar_bg_color' );
}

if( isset( $_REQUEST[ 'tn_buddysocial_adminbar_hover_bg_color' ] ) ) {
update_option('tn_buddysocial_adminbar_hover_bg_color', $post_hover_bg_color );
} else {
delete_option('tn_buddysocial_adminbar_hover_bg_color' );
}

  endif;

foreach ($options as $value) {
update_option( $value['id'], $_REQUEST[ $value['id'] ] ); }
foreach ($options as $value) {
if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } else { delete_option( $value['id'] ); } }
header("Location: themes.php?page=options-functions.php&saved=true");
die;
} else if( isset($_REQUEST['action']) && 'reset' == $_REQUEST['action'] ) {
foreach ($options as $value) {

         if( is_multisite()) {
  delete_site_option('multisite_adminbar_bg_color');
  delete_site_option('multisite_adminbar_hover_bg_color');
  } else {
  delete_option('tn_buddysocial_adminbar_bg_color');
  delete_option('tn_buddysocial_adminbar_hover_bg_color');
  }


delete_option( $value['id'] ); }
header("Location: themes.php?page=options-functions.php&reset=true");
die;
}
}
add_theme_page(_g ($themename . __(' Options', TEMPLATE_DOMAIN)),  _g (__('Theme Options', TEMPLATE_DOMAIN)),  'edit_theme_options', 'options-functions.php', 'buddysocial_admin');
}
add_action('admin_menu', 'buddysocial_admin_register');



////////////////////////////////////////////////////////////////////////////////
// add theme cms pages
////////////////////////////////////////////////////////////////////////////////
function dev_remove_all_scripts() {
wp_deregister_script('jquery');
wp_deregister_script('jquery-ui-tabs');
}

function buddysocial_head() { ?>
<link href="<?php echo get_template_directory_uri(); ?>/_inc/admin/options-css.css" rel="stylesheet" type="text/css" />

<?php if(isset($_GET["page"]) && $_GET["page"] == "options-functions.php") { ?>

<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/_inc/js/jscolor.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/_inc/js/jquery-ui-personalized-1.6rc2.min.js">
</script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/_inc/js/jquery.cookie.min.js"></script>

<script type="text/javascript">
jQuery.noConflict();
var $jd = jQuery;
$jd(document).ready(function(){
$jd('ul#tabm').tabs({event: "click"});
});
</script>

<link href='http://fonts.googleapis.com/css?family=Cantarell' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=Cardo' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=Crimson+Text' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=Droid+Sans' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=Droid+Serif' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=IM+Fell+DW+Pica' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=Josefin+Sans+Std+Light' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=Lobster' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=Molengo' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=Neuton' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=Nobile' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=OFL+Sorts+Mill+Goudy+TT' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=Reenie+Beanie' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=Tangerine' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=Old+Standard+TT' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=Volkorn' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=Yanone+Kaffeesatz' rel='stylesheet' type='text/css'/>
<link href='http://fonts.googleapis.com/css?family=Just+Another+Hand' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Terminal+Dosis+Light' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Ubuntu:light,regular,bold' rel='stylesheet' type='text/css'>
<?php
// add_action('admin_head', 'dev_remove_all_scripts', 100);
} ?>

<?php }


add_action('admin_head', 'buddysocial_head');


////////////////////////////////////////////////////////////////////////////////
// CUSTOM IMAGE HEADER  - IF ON WILL BE SHOWN ELSE WILL HIDE
////////////////////////////////////////////////////////////////////////////////

$header_enable = get_option('tn_buddysocial_header_on');
if($header_enable == 'enable') {

$custom_height = get_option('tn_buddysocial_image_height');
if($custom_height==''){$custom_height='150'; } else { $custom_height = get_option('tn_buddysocial_image_height'); }


define('HEADER_TEXTCOLOR', '');
define('HEADER_IMAGE', ''); // %s is theme dir uri
define('HEADER_IMAGE_WIDTH', 960); //width is fixed
define('HEADER_IMAGE_HEIGHT', $custom_height);
define('NO_HEADER_TEXT', true );


function buddysocial_admin_header_style() { ?>
<style type="text/css">
#headimg {
	background: url(<?php header_image() ?>) no-repeat;
}
#headimg {
	height: <?php echo HEADER_IMAGE_HEIGHT; ?>px;
	width: <?php echo HEADER_IMAGE_WIDTH; ?>px;
}

#headimg h1, #headimg #desc {
	display: none;
}
</style>
<?php }
$defaults = array(
	'default-image'          => '',
	'random-default'         => false,
	'width'                  => 0,
	'height'                 => 0,
	'flex-height'            => false,
	'flex-width'             => false,
	'default-text-color'     => '',
	'header-text'            => true,
	'uploads'                => true,
	'wp-head-callback'       => '',
	'admin-head-callback'    => 'buddysocial_admin_header_style',
	'admin-preview-callback' => '',
);
add_theme_support( 'custom-header', $defaults );
}
//end check for header image


?>