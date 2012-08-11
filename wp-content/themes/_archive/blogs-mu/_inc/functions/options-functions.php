<?php
////////////////////////////////////////////////////////////////////////////////
// multiple option page
////////////////////////////////////////////////////////////////////////////////
function _g($str) { return __($str, 'option-page'); }

////////////////////////////////////////////////////////////////////////////////
// Load Theme Options
////////////////////////////////////////////////////////////////////////////////
$themename = wp_get_theme()->get('Name');

if(file_exists( WP_CONTENT_DIR . '/themes/blogs-mu/style.css' )) {
//$theme_data = get_theme_data( WP_CONTENT_DIR . '/themes/blogs-mu/style.css');
$theme_data = wp_get_theme( 'blogs-mu', WP_CONTENT_DIR . '/themes' );
} else {
//$theme_data = get_theme_data( TEMPLATEPATH . '/style.css');
$theme_data = wp_get_theme( );
}

$theme_version = $theme_data->get('Version');

$shortname = "tn";
$shortprefix = "_blogsmu_";

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
}

////////////////////////////////////////////////////////////////////////////////
// load options
////////////////////////////////////////////////////////////////////////////////
$options = array (

//navigation
array(
"name" => __("Choose your navigation font",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix  . "nav_font",
"type" => "select-preview",
"inblock" => "navigation",
"std" => "Lucida Grande, Lucida Sans, sans-serif",
			"options" => array(
           "Lucida Grande, Lucida Sans, sans-serif",
           "Arial, sans-serif",
											"Cantarell, arial, serif",
											"Cardo, arial, serif",
										    "Courier New, Courier, monospace",
											"Crimson Text, arial, serif",
											"Droid Sans, arial, serif",
											"Droid Serif, arial, serif",
								            "Garamond, Georgia, serif",
											"Georgia, arial, serif",
								            "Helvetica, Arial, sans-serif",
											"IM Fell SW Pica, arial, serif",
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

array (
"name" => __("Choose your navigation background main color",TEMPLATE_DOMAIN),
"description" => __("this will changed the navigation background main color<br /><em>* CSS3 Gradient Supported</em>", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "nav_bg_main_color",
"inblock" => "navigation",
"std" => "",
"type" => "colorpicker"),

array (
"name" => __("Choose your navigation background secondary color",TEMPLATE_DOMAIN),
"description" => __("this will changed the navigation background secondary color<br /><em>* CSS3 Gradient Supported</em>", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "nav_bg_secondary_color",
"inblock" => "navigation",
"std" => "",
"type" => "colorpicker"),

array (
"name" => __("Choose your navigation border color",TEMPLATE_DOMAIN),
"description" => __("this will changed the navigation border color", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "nav_border_color",
"inblock" => "navigation",
"std" => "",
"type" => "colorpicker"),

array (
"name" => __("Choose your navigation text link color",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "nav_text_link_color",
"inblock" => "navigation",
"std" => "",
"type" => "colorpicker"),

array (
"name" => __("Choose your navigation dropdown background color",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "nav_dropdown_bg_color",
"inblock" => "navigation",
"std" => "",
"type" => "colorpicker"),

array (
"name" => __("Choose your navigation dropdown link hover background color",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "nav_dropdown_link_hover_color",
"inblock" => "navigation",
"std" => "",
"type" => "colorpicker"),



//services
array(
"name" => __("Enable or Disable Bottom Services Block in Homepage", TEMPLATE_DOMAIN),
"description" => __("you can enable or disable the services block in homepage", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "home_service_block",
"inblock" => "services",
"type" => "select",
"std" => "enable",
"options" => array("enable","disable")),


array(
"name" => __("Insert Your Services Intro text", TEMPLATE_DOMAIN),
"description" => __("you can insert service top intro text on homepage", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "home_service_intro_text",
"inblock" => "services",
"type" => "text",
"std" => "",
),

array("name" => __("Choose your Bottom Services Block style?", TEMPLATE_DOMAIN),
"description" => __("*default: service mode <br /> <em>if on <strong>service mode</strong>, you will able to used the custom upload crop image with customize text.</em><br /><br /><em>if on <strong>post mode</strong>, it will get latest featured post based on your category choice below with auto image attachment thumbnail.</em>", TEMPLATE_DOMAIN),

"id" => $shortname . $shortprefix . "home_service_style",
"inblock" => "services",
"std" => "service-mode",
"type" => "select",
"options" => array("service-mode", "post-mode")),

array(
"name" => __("Choose your Featured Post Mode category", TEMPLATE_DOMAIN),
"description" => __("*only active if you're on post mode", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "home_service_postmode_cat",
"inblock" => "services",
"type" => "select",
"std" => "Choose a category:",
"options" => $wp_getcat),

array(
"name" => __("How many Featured Post Mode category you want to show?", TEMPLATE_DOMAIN),
"description" => __("*only active if you're on post mode", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "home_service_postmode_cat_count",
"inblock" => "services",
"type" => "select",
"std" => "",
"options" => $choose_count),


array(
"name" => __("You can insert a 728 x 90 banner ads here", TEMPLATE_DOMAIN),
"description" => __("*optional - html allowed", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "home_service_ads",
"inblock" => "services",
"type" => "textarea",
"std" => "",
),



//featured block
array(
"name" => __("Choose you prefered Featured Intro Block Style", TEMPLATE_DOMAIN),
"description" => __("you can choose the featured intro style between image intro, featured slider intro, featured video and bp album rotate<br /><br /><div class='checkbox-box'><p>*New option will be appeared when you save the options and you need to setup the new setting according to your choice again</p></div>", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "featured_blk_option",
"inblock" => "feat-options",
"type" => "select",
"has_sub_option"=> "yes",
"std" => "",
"options" => array("Image Intro", "Featured Slider Posts", "Featured Slider Categories", "Featured Video", "BP Album Rotate")),

array(
"name" => __("If you want to used an image for the intro header, insert your image full url here&nbsp;&nbsp;&nbsp;<em>*suitable size 420x260</em>", TEMPLATE_DOMAIN),
"description" => __("you can upload your image in <a href='media-new.php'>media panel</a> and copy paste the url here", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "featured_img_url",
"inblock" => "feat-options",
"type" => "text",
"std" => "",
),

array(
"name" => __("Insert the Post ID for your featured intro<br /><em>*example: 1,3,44,123,678</em>",TEMPLATE_DOMAIN),
"description" => __("you can choose the which post to rotate in featured intro block<br />*if empty, latest post will be rotate accordingly", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "featured_post_id",
"sub-options-id" => "Featured Slider Posts",
"inblock" => "feat-options",
"type" => "text",
"std" => "",
),

array(
"name" => __("Insert the Category ID for your featured intro<br /><em>*example: 1,3,44,123,678</em>",TEMPLATE_DOMAIN),
"description" => __("you can choose the which category to rotate in featured intro block", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "featured_cat_id",
"sub-options-id" => "Featured Slider Category",
"inblock" => "feat-options",
"type" => "text",
"std" => "",
),

array(
"name" => __("How many <strong>Posts</strong> on all category choosen you want to rotate?",TEMPLATE_DOMAIN),
"description" => __("Select how many posts in your categories choosen you want to show", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "featured_cat_id_count",
"sub-options-id" => "Featured Slider Category Count",
"inblock" => "feat-options",
"type" => "select",
"std" => "",
"options" => $choose_count),



array(
"name" => __("Insert your video embed code here",TEMPLATE_DOMAIN),
"description" => __("you can enter a video embed code here", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "featured_video",
"sub-options-id" => "Featured Video",
"inblock" => "feat-options",
"type" => "textarea",
"std" => "",
),


array(
"name" => __("Change your desired <strong>custom field</strong> for featured post images<br /><em>Default: thumbs</em><br /><em>more about <a href='http://codex.wordpress.org/Custom_Fields'>custom field</a> usage here</em>",TEMPLATE_DOMAIN),
"description" => __("you can insert your desired custom field key here<br />*if empty, images will be pulled from post FEATURED images or attachments", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "featured_custom_field",
"inblock" => "feat-options",
"type" => "text",
"std" => "",
),

array(
"name" => __("How many <strong>BP Album</strong> images you want to rotate?",TEMPLATE_DOMAIN),
"description" => __("this option only effected if you have <a target='_blank' href='http://code.google.com/p/buddypress-media/'>BuddyPress Album</a> installed", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "featured_bp_album_count",
"sub-options-id" => "BP Album Rotate",
"inblock" => "feat-options",
"type" => "select",
"std" => "",
"options" => $choose_count),





//homepage
array(
"name" => __("Your Network intro header text", TEMPLATE_DOMAIN),
"description" => __("you can insert header title for the intro on homepage", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "featured_intro_headline",
"inblock" => "homepage",
"type" => "text",
"std" => "Start Your Own Blogging Network and Community",
),

array (
"name" => __("Add CSS3 Shadow for the intro text",TEMPLATE_DOMAIN),
"description" => __("leave blank if do not want to use shadow", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "featured_intro_text_shadow",
"inblock" => "homepage",
"std" => "",
"type" => "colorpicker"),


array(
"name" => __("Your Network intro post text", TEMPLATE_DOMAIN),
"description" => __("you can insert post text for the intro on homepage", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "featured_intro_post",
"inblock" => "homepage",
"type" => "textarea",
"std" => "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Quisque sed felis. Aliquam sit amet felis. Mauris semper, velit semper laoreet dictum, quam diam dictum urna, nec placerat elit nisl in quam. Etiam augue pede, molestie eget, rhoncus at, convallis ut, eros",
),

array(
"name" => __("Your Network intro header button <strong>not logged-in</strong> text", TEMPLATE_DOMAIN),
"description" => __("you can insert button text for the intro on homepage for not logged in user", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "featured_intro_button_text",
"inblock" => "homepage",
"type" => "text",
"std" => "Join Our Community Now..",
),

array(
"name" => __("Your Network intro header button <strong>not logged-in</strong> link", TEMPLATE_DOMAIN),
"description" => __("you can insert button link for the intro on homepage for not logged in user", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "featured_intro_button_link",
"inblock" => "homepage",
"type" => "text",
"std" => "",
),

array(
"name" => __("Your Network intro header button <strong>logged in</strong> text", TEMPLATE_DOMAIN),
"description" => __("you can insert button text for the intro on homepage for logged in user", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "featured_intro_button_logged_text",
"inblock" => "homepage",
"type" => "text",
"std" => "",
),

array(
"name" => __("Your Network intro header button <strong>logged-in</strong> link", TEMPLATE_DOMAIN),
"description" => __("you can insert button link for the intro on homepage for logged in user", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "featured_intro_button_logged_link",
"inblock" => "homepage",
"type" => "text",
"std" => "",
),


array (
"name" => __("Choose your intro header button color",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "featured_intro_button_color",
"inblock" => "homepage",
"std" => "",
"type" => "colorpicker"),

array (
"name" => __("Choose your intro header button text link color",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "featured_intro_button_text_link_color",
"inblock" => "homepage",
"std" => "",
"type" => "colorpicker"),

array(
"name" => __("Your Network sub intro post text", TEMPLATE_DOMAIN),
"description" => __("you can insert post text for the sub intro on homepage", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "featured_sub_intro_post",
"inblock" => "homepage",
"type" => "textarea",
"std" => "One community multi socials blogging solutions for your network and communities<br><span>more info text can be input here...<a href='#'>more</a></span>",
),




// section
array(
"name" => __("Enable or Disable the Header Section ", TEMPLATE_DOMAIN),
"description" => __("you can enable or disable the 3 header section on homepage", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "section_status",
"inblock" => "section",
"type" => "select",
"std" => "enable",
"options" => array("enable","disable")),


array(
"name" => __("Do You Want to Showed Header Section Widgets for non logged-in visitor", TEMPLATE_DOMAIN),
"description" => __("*default: hide&nbsp;&nbsp;&nbsp;&nbsp;you can hide the widget in header section for non-logged in visitor", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "section_widget_status",
"inblock" => "section",
"type" => "select",
"std" => "hide",
"options" => array("hide","show")),


array(
"name" => __("Header Section 1 Title", TEMPLATE_DOMAIN),
"description" => __("you can insert header title for the section 1 on homepage", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "section_one_headline",
"inblock" => "section",
"type" => "text",
"std" => "",
),

array(
"name" => __("Header Section 1 Post Text", TEMPLATE_DOMAIN),
"description" => __("you can insert post text for the section 1 on homepage", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "section_one_post_text",
"inblock" => "section",
"type" => "textarea",
"std" => "",
),

array(
"name" => __("Header Section 2 Title", TEMPLATE_DOMAIN),
"description" => __("you can insert header title for the section 2 on homepage", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "section_two_headline",
"inblock" => "section",
"type" => "text",
"std" => "",
),

array(
"name" => __("Header Section 2 Post Text", TEMPLATE_DOMAIN),
"description" => __("you can insert post text for the section 2 on homepage", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "section_two_post_text",
"inblock" => "section",
"type" => "textarea",
"std" => "",
),

array(
"name" => __("Header Section 3 Title", TEMPLATE_DOMAIN),
"description" => __("you can insert header title for the section 3 on homepage", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "section_three_headline",
"inblock" => "section",
"type" => "text",
"std" => "",
),

array(
"name" => __("Header Section 3 Post Text", TEMPLATE_DOMAIN),
"description" => __("you can insert post text for the section 3 on homepage", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "section_three_post_text",
"inblock" => "section",
"type" => "textarea",
"std" => "",
),






//buddypress

array(
"name" => __("Enable or Disable Featured Groups in Homepage", TEMPLATE_DOMAIN),
"description" => __("you can enable or disable the featured groups in homepage", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "home_feat_groups",
"header-title" => __("Homepage Featured Groups Setting", TEMPLATE_DOMAIN),
"inblock" => "buddypress",
"type" => "select",
"std" => "enable",
"options" => array("enable","disable")),


array(
"name" => __("If 'Featured Groups' enable, it will randomize featured groups from your groups list but you can also insert 6 group id here instead of auto-random", TEMPLATE_DOMAIN),
"description" => __("you can insert no more than 6 group id here *sample: 1,34,55,66,67,77", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "home_feat_group_id",
"inblock" => "buddypress",
"type" => "text",
"std" => "",
),

array(
"name" => __("Insert Your Random Groups Header Title Here", TEMPLATE_DOMAIN),
"description" => __("you can insert the random group header title to replace the one at homepage", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "home_feat_group_header",
"inblock" => "buddypress",
"type" => "text",
"std" => "",
),



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
"name" => __("if you enable the <strong>privacy</strong> on all members profile for none logged in user, insert the full url link they will be redirect to for non logged in users<br /><em>*optional - leave empty for default<br />default are buddypress register link<br /> " . site_url() . '/' . ( isset($member_reg_slug) ? $member_reg_slug . '/' : '' ) . "</em>", TEMPLATE_DOMAIN),
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
"name" => __("if you enable for the only <strong>admins and editors</strong> to create group, insert the full url link they will be redirect to for non admins and editors users when they click <strong>create group</strong> button<br /><em>*optional - leave empty for default<br />default are buddypress root domain<br /> " . $the_privacy_root . '/' . "</em>", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "create_group_redirect",
"box"=> "1",
"inblock" => "buddypress",
"type" => "text",
"std" => "",
),


array (
"name" => __("<strong>Activity Post Block</strong> background color", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "activity_block_color",
"inblock" => "buddypress",
"header-title" => __("BuddyPress Activity Setting", TEMPLATE_DOMAIN),
"header-img-link" => '/_inc/admin/activitymeta.png',
"std" => "",
"type" => "colorpicker"),


array (
"name" => __("<strong>Activity Post Block</strong> text color", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "activity_block_text_color",
"inblock" => "buddypress",
"std" => "",
"type" => "colorpicker"),

array(
"name" => __("Do you want to enable facebook like it in Activity Stream",TEMPLATE_DOMAIN),
"description" => __("you can enable facebook like in stream but this is a bandwith hog features 'disable' by default", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "stream_facebook_like_status",
"inblock" => "buddypress",
"type" => "select",
"std" => "disable",
"options" => array("disable","enable")),



array (
"name" => __("<strong>SPAN Meta block</strong> background color", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "span_meta_color",
"inblock" => "buddypress",
"header-title" => __("BuddyPress Span Meta CSS Setting", TEMPLATE_DOMAIN),
"header-img-link" => '/_inc/admin/spanmeta.png',
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



//css
array(
"name" => __("Choose your body font",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix  . "body_font",
"type" => "select-preview",
"inblock" => "css",
"std" => "Lucida Grande, Lucida Sans, sans-serif",
			"options" => array(
           "Lucida Grande, Lucida Sans, sans-serif",
           "Arial, sans-serif",
											"Cantarell, arial, serif",
											"Cardo, arial, serif",
										    "Courier New, Courier, monospace",
											"Crimson Text, arial, serif",
											"Droid Sans, arial, serif",
											"Droid Serif, arial, serif",
								            "Garamond, Georgia, serif",
											"Georgia, arial, serif",
								            "Helvetica, Arial, sans-serif",
											"IM Fell SW Pica, arial, serif",
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
"inblock" => "css",
"std" =>    "Lucida Grande, Lucida Sans, sans-serif",
			"options" => array(
            "Lucida Grande, Lucida Sans, sans-serif",
            "Arial, sans-serif",
											"Cantarell, arial, serif",
											"Cardo, arial, serif",
										    "Courier New, Courier, monospace",
											"Crimson Text, arial, serif",
											"Droid Sans, arial, serif",
											"Droid Serif, arial, serif",
								            "Garamond, Georgia, serif",
											"Georgia, arial, serif",
								            "Helvetica, Arial, sans-serif",
											"IM Fell SW Pica, arial, serif",
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
"name" => __("Your prefered font size (in pixel)",TEMPLATE_DOMAIN),
"description" => __("no need to enter the px..just numeric like 11,12,13..etc", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "font_size",
"box"=> "1",
"inblock" => "css",
"type" => "text",
"std" => "",
),

array(
"name" => __("Your prefered font line height (in pixel)",TEMPLATE_DOMAIN),
"description" => __("no need to enter the px..just numeric like 11,12,13..etc", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "font_line_height",
"box"=> "1",
"inblock" => "css",
"type" => "text",
"std" => "",
),

array (
"name" => __("Choose your global links color",TEMPLATE_DOMAIN),
"description" => __("this will changed the global blog links color", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "blog_global_links_color",
"inblock" => "css",
"std" => "",
"type" => "colorpicker"),

array (
"name" => __("Choose your global links hover color",TEMPLATE_DOMAIN),
"description" => __("this will changed the global blog links hover color", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "blog_global_links_hover_color",
"inblock" => "css",
"std" => "",
"type" => "colorpicker"),




//header
array (
"name" => __("Choose your top header background main color",TEMPLATE_DOMAIN),
"description" => __("this will changed the top header background main color<br /><em>* CSS3 Gradient Supported</em>", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "top_header_bg_main_color",
"inblock" => "header",
"header-title" => "Top Header CSS Setting",
"std" => "",
"type" => "colorpicker"),

//header
array (
"name" => __("Choose your top header background secondary gradient color",TEMPLATE_DOMAIN),
"description" => __("this will changed the top header background secondary gradient color<br /><em>* CSS3 Gradient Supported</em>", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "top_header_bg_secondary_color",
"inblock" => "header",
"std" => "",
"type" => "colorpicker"),


array (
"name" => __("Choose your top header text color",TEMPLATE_DOMAIN),
"description" => __("this will changed the top header text color", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "top_header_text_color",
"inblock" => "header",
"std" => "",
"type" => "colorpicker"),


array (
"name" => __("Choose your top header text link color",TEMPLATE_DOMAIN),
"description" => __("this will changed the top header text link color", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "top_header_text_link_color",
"inblock" => "header",
"std" => "",
"type" => "colorpicker"),




array (
"name" => __("Choose your main header background main color",TEMPLATE_DOMAIN),
"description" => __("this will changed the main header background color<br /><em>* CSS3 Gradient Supported</em>", TEMPLATE_DOMAIN),
"header-title" => "Main Header CSS Setting",
"id" => $shortname . $shortprefix . "main_header_bg_main_color",
"inblock" => "header",
"std" => "",
"type" => "colorpicker"),


array (
"name" => __("Choose your main header background secondary gradient color",TEMPLATE_DOMAIN),
"description" => __("this will changed the main header background secondary gradient color<br /><em>* CSS3 Gradient Supported</em>", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "main_header_bg_secondary_color",
"inblock" => "header",
"std" => "",
"type" => "colorpicker"),


array (
"name" => __("Choose your main header text color",TEMPLATE_DOMAIN),
"description" => __("this will changed the main header text color", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "main_header_text_color",
"inblock" => "header",
"std" => "",
"type" => "colorpicker"),


array (
"name" => __("Choose your main header text link color",TEMPLATE_DOMAIN),
"description" => __("this will changed the main header text link color", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "main_header_text_link_color",
"inblock" => "header",
"std" => "",
"type" => "colorpicker"),



array(
"name" => __("Insert your logo full url here",TEMPLATE_DOMAIN),
"description" => __("you can upload your logo in <a target='_blank' href='media-new.php'>media panel</a> and copy paste the url here", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "header_logo",
"header-title" => "Header Graphics Setting",
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
"name" => __("Your custom image header link to",TEMPLATE_DOMAIN),
"description" => __("if leave blank, it will link to homepage. otherwise insert any link here<br />*must have http://", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "header_linkto",
"box"=> "2",
"inblock" => "header",
"type" => "text",
"std" => "",
),

array(
"name" => __("Your prefered custom image header height",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "image_height",
"box"=> "2",
"inblock" => "header",
"type" => "text",
"std" => "150",
),



//footer
array (
"name" => __("Choose your footer background main color",TEMPLATE_DOMAIN),
"description" => __("this will changed the footer background main color<br /><em>* CSS3 Gradient Supported</em>", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "footer_bg_main_color",
"inblock" => "footer",
"std" => "",
"type" => "colorpicker"),

array (
"name" => __("Choose your footer background secondary color",TEMPLATE_DOMAIN),
"description" => __("this will changed the footer background secondary color<br /><em>* CSS3 Gradient Supported</em>", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "footer_bg_secondary_color",
"inblock" => "footer",
"std" => "",
"type" => "colorpicker"),


array (
"name" => __("Choose your footer text color",TEMPLATE_DOMAIN),
"description" => __("this will changed the footer text color", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "footer_text_color",
"inblock" => "footer",
"std" => "",
"type" => "colorpicker"),

array (
"name" => __("Choose your footer header text color",TEMPLATE_DOMAIN),
"description" => __("this will changed the footer header title text color", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "footer_header_text_color",
"inblock" => "footer",
"std" => "",
"type" => "colorpicker"),


array (
"name" => __("Choose your footer text link color",TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "footer_text_link_color",
"inblock" => "footer",
"std" => "",
"type" => "colorpicker"),

array (
"name" => __("Choose your footer text link hover color",TEMPLATE_DOMAIN),
"description" => __("this will changed the footer link hover color", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "footer_text_link_hover_color",
"inblock" => "footer",
"std" => "",
"type" => "colorpicker"),



//extra
array(
"name" => __("Enable or Disable 4-Column Footer", TEMPLATE_DOMAIN),
"description" => __("you can enable or disable the 4-column footer here", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "home_footer_block",
"inblock" => "extra",
"type" => "select",
"std" => "enable",
"options" => array("enable","disable")),

array(
"name" => __("Left Sidebar or Right Sidear?", TEMPLATE_DOMAIN),
"description" => __("you can choose which sidebar position you prefered", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "sidebar_position",
"inblock" => "extra",
"type" => "select",
"std" => "left",
"options" => array("left","right")),


array(
"name" => __("Enable or Disable The Login Panel On Top", TEMPLATE_DOMAIN),
"description" => __("you can enable or disable the login panel on top here", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "home_login_block",
"inblock" => "extra",
"type" => "select",
"std" => "enable",
"options" => array("enable","disable")),


array(
"name" => __("EXTRA: Do you want to enable facebook like it in blog post",TEMPLATE_DOMAIN),
"description" => __("you can enable facebook like in blog post but this is a bandwith hog features 'disable' by default", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "post_facebook_like_status",
"inblock" => "extra",
"type" => "select",
"std" => "disable",
"options" => array("disable","enable"))

);


function blogsmu_admin_panel() {
global $themename, $theme_version, $shortname, $shortprefix, $options, $blog_id, $bp_existed, $bp_front_is_activity;

$i = 0;
if (!isset($_REQUEST['saved'])) {
    $_REQUEST['saved'] = '';
}
if (!isset($_REQUEST['reset'])) {
    $_REQUEST['reset'] = '';
}
if ( $_REQUEST['saved'] ) echo '<div id="message" class="updated fade"><p><strong>'. $themename . __(' settings saved.', TEMPLATE_DOMAIN) . '</strong></p></div>';
if ( $_REQUEST['reset'] ) echo '<div id="message" class="updated fade"><p><strong>'. $themename . __(' settings reset.', TEMPLATE_DOMAIN) . '</strong></p></div>';
?>


<div id="options-panel">
<div id="options-head"><h2><?php echo $themename; ?> <?php _e("Theme Options", TEMPLATE_DOMAIN); ?></h2>
<div class="theme-versions"><?php _e("Version", TEMPLATE_DOMAIN); ?> <?php echo $theme_version; ?></div>
</div>

<div id="sbtabs">

<div class="tabmc">
<ul class="ui-tabs-nav" id="tabm">
<?php
$value_var_global = array('header', 'homepage', 'navigation', 'section', 'services', 'feat-options', 'css', 'footer', 'buddypress', 'extra');
?>


<?php if( is_main_site() ) {  ?>
<li><a href="#tab1"><?php _e("Admin Bar Settings", TEMPLATE_DOMAIN); ?></a></li>
<?php } ?>

<li><a href="#tab2"><?php _e("Header Settings", TEMPLATE_DOMAIN); ?></a></li>
<li><a href="#tab3"><?php _e("Homepage Settings", TEMPLATE_DOMAIN); ?></a></li>
<li><a href="#tab4"><?php _e("Navigation Settings", TEMPLATE_DOMAIN); ?></a></li>
<li><a href="#tab5"><?php _e("Section Settings", TEMPLATE_DOMAIN); ?></a></li>
<li><a href="#tab6"><?php _e("Services Settings", TEMPLATE_DOMAIN); ?></a></li>
<li><a href="#tab7"><?php _e("Features Settings", TEMPLATE_DOMAIN); ?></a></li>
<li><a href="#tab8"><?php _e("CSS Settings", TEMPLATE_DOMAIN); ?></a></li>
<li><a href="#tab9"><?php _e("Footer Settings", TEMPLATE_DOMAIN); ?></a></li>
<?php if($bp_existed == 'true') { //only showed if buddypress installed ?>
<li><a href="#tab10"><?php _e("BuddyPress Settings", TEMPLATE_DOMAIN); ?></a></li>
<?php } ?>
<li><a href="#tab11"><?php _e("Extra Settings", TEMPLATE_DOMAIN); ?></a></li>
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
<div class="description"><?php _e('Choose your adminbar background color', TEMPLATE_DOMAIN); ?><br /><span></span></div>
<div class="input-option"><p><input class="color {required:false,hash:true}" name="<?php echo $bg_color; ?>" id="colorpickerField88" type="text" value="<?php
	if (get_site_option($bg_color) != "")
	{
		echo get_site_option($bg_color);
	}
 ?>" /></p></div>
</div>
                              <?php if($bp_existed == 'true'): ?>
<div id="<?php echo $bg_hover_color; ?>" class="tab-option">
<div class="description"><?php _e('Choose your adminbar background hover color', TEMPLATE_DOMAIN); ?><br /><span></span></div>
<div class="input-option"><p><input class="color {required:false,hash:true}" name="<?php echo $bg_hover_color; ?>" id="colorpickerField89" type="text" value="<?php
	if (get_site_option($bg_hover_color) != "")
	{
		echo get_site_option($bg_hover_color);
	}
 ?>" /></p></div>
</div>
    <?php endif; ?>

<?php } } else {

	$bg_color = $shortname . $shortprefix . 'adminbar_bg_color';
	$bg_hover_color = $shortname . $shortprefix . 'adminbar_hover_bg_color';
 ?>

<div id="<?php echo $bg_color; ?>" class="tab-option">
<div class="description"><?php _e('Choose your adminbar background color', TEMPLATE_DOMAIN); ?><br /><span></span></div>
<div class="input-option"><p><input class="color {required:false,hash:true}" name="<?php echo $bg_color; ?>" id="colorpickerField88" type="text" value="<?php
	if (get_option($bg_color) != "")
	{
		echo get_option($bg_color);
	}
 ?>" /></p></div>
</div>
                                 <?php if($bp_existed == 'true'): ?>
<div id="<?php echo $bg_hover_color; ?>" class="tab-option">
<div class="description"><?php _e('Choose your adminbar background hover color', TEMPLATE_DOMAIN); ?><br /><span></span></div>
<div class="input-option"><p><input class="color {required:false,hash:true}" name="<?php echo $bg_hover_color; ?>" id="colorpickerField89" type="text" value="<?php
	if (get_option($bg_hover_color) != "")
	{
		echo get_option($bg_hover_color);
	}
 ?>" /></p></div>
</div>
   <?php endif; ?>

<?php } ?>

</li>
</ul>



<?php $vc = 2; foreach ($value_var_global as $value_var) { ?>

<ul style="" class="ui-tabs-panel<?php if($vc > 0) { ?> ui-tabs-hide<?php } ?>" id="tab<?php echo $vc; ?>">
<li>
<?php foreach ($options as $value) {
	// Hide redundant option that already on customizer
	if ( HIDE_REDUNDANT_OPTIONS_CUSTOMIZER == 'yes' && blogsmu_option_in_customize($value) ) continue;
    if (!isset($value['header-title'])) {
        $value['header-title'] = '';
    }
    if (!isset($value['description'])) {
        $value['description'] = '';
    }
    if (($value['inblock'] == $value_var) && ($value['header-title'] != "")) { // if we got header title for option ?>
<h4><?php echo stripslashes($value['header-title']); ?><?php if(!empty($value['header-img-link'])) { ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="whatsthis" href="#thumb">(What's this?)<span><img src="<?php echo get_template_directory_uri(); ?><?php echo stripslashes($value['header-img-link']); ?>" /></span></a><?php } ?></h4>
<?php } ?>


<?php if (($value['inblock'] == $value_var) && ($value['type'] == "text")) { // setting ?>
<div id="<?php echo $value['id']; ?>" class="tab-option">
<div class="description"><?php echo $value['name']; ?><br /><span><?php echo $value['description']; ?></span></div>
<div class="input-option"><p><input name="<?php echo $value['id']; ?>" class="myfield" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php
	if (get_option($value['id']) != "")
	{
		echo stripslashes(get_option($value['id']));
	}
	else
	{
		echo stripslashes($value['std']);
	}
 ?>" /></p></div>
</div>

<?php } else if (($value['inblock'] == $value_var) && ($value['type'] == "ajax-file-upload")) { // setting ?>

<div id="<?php echo $value['id']; ?>" class="tab-option">
<div class="description"><?php echo $value['name']; ?><br /><span><?php echo $value['description']; ?></span></div>
<div class="input-option">
</div>
</div>

<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "checkbox") ) { // setting ?>

<?php
	if (get_option($value['id']))
	{
		$checked = "checked=\"checked\"";
	}
	else
	{
		$checked = "";
	}
 ?>
<div id="<?php echo $value['id']; ?>" class="checkbox-box">
<div class="description"><p><input type="<?php echo $value['type']; ?>" class="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="<?php echo $value['id']; ?>" <?php echo $checked; ?> />&nbsp;&nbsp;<?php echo $value['name']; ?></p></div></div>

<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "textarea")) { // setting ?>

<div id="<?php echo $value['id']; ?>" class="tab-option">
<?php
$valuex = $value['id'];
$valuey = stripslashes($valuex);
$video_code = get_option($valuey);
?>
<div class="description"><?php echo $value['name']; ?><br /><span><?php echo $value['description']; ?></span></div>
<div class="input-option"><p><textarea name="<?php echo $valuey; ?>" class="mytext" cols="40%" rows="8" /><?php
	if (get_option($valuey) != "")
	{
		echo stripslashes($video_code);
	}
	else
	{
		echo $value['std'];
	}
 ?>
</textarea></p></div>
</div>
<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "colorpicker") ) { // setting ?>
<?php $i == $i++; ?>
<div id="<?php echo $value['id']; ?>" class="tab-option">
<div class="description"><?php echo $value['name']; ?><br /><span><?php echo $value['description']; ?></span></div>
<div class="input-option"><p><input class="color {required:false,hash:true}" name="<?php echo $value['id']; ?>" id="colorpickerField<?php echo $i; ?>" type="text" value="<?php
	if (get_option($value['id']) != "")
	{
		echo get_option($value['id']);
	}
	else
	{
		echo $value['std'];
	}
 ?>" /></p></div>
</div>

<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "select-preview") ) { // setting ?>
<div class="tab-option">
<div class="description"><?php echo $value['name']; ?><br /><span><?php echo $value['description']; ?></span></div>
<div class="input-option"><p><select name="<?php echo $value['id']; ?>" class="myselect" id="<?php echo $value['id']; ?>">
<?php foreach ($value['options'] as $option) { ?>
<option style="font-family:<?php echo $option; ?>;" <?php
	if (get_option($value['id']) == $option)
	{
		echo ' selected="selected"';
	}
	elseif ($option == get_option($value['std']))
	{
		echo ' selected="selected"';
	}
 ?>><?php echo $option; ?></option>
<?php } ?>
</select>
</p>
</div>
</div>

<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "select") ) { // setting ?>
<div id="<?php echo $value['id']; ?>" class="tab-option">
<div class="description"><?php echo $value['name']; ?><br /><span><?php echo $value['description']; ?></span></div>
<div class="input-option"><p><select name="<?php echo $value['id']; ?>" class="myselect" id="<?php echo $value['id']; ?>">
<?php foreach ($value['options'] as $option) { ?>
<option<?php
	if (get_option($value['id']) == $option)
	{
		echo ' selected="selected"';
	}
	elseif ($option == $value['std'])
	{
		echo ' selected="selected"';
	}
 ?>><?php echo $option; ?></option>
<?php } ?>
</select>
</p>
</div>
</div>
<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "custom-radio") ) { // setting ?>
<div id="<?php echo $value['id']; ?>" class="tab-option">
<div class="description"><?php echo $value['name']; ?><br /><span><?php echo $value['description']; ?></span></div>
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

<?php $vc++;
	}
 ?>

<div class="submit">
<input name="save" type="submit" class="button-primary sbutton" value="<?php echo esc_attr(__('Save All Options', TEMPLATE_DOMAIN)); ?>" />
<input type="hidden" name="action" value="save" />
</div>
</form>

</div>

</div><!-- tabmc-right -->

</div><!-- sbtabs -->

<div id="reset-box">
<form method="post">
<div class="submit">
<input name="reset" type="submit" class="sbutton" onclick="return confirm('Are you sure you want to reset all saved settings?. This action cannot be restore.')" value="<?php echo esc_attr(__('Reset All Options', TEMPLATE_DOMAIN)); ?>" />
<input type="hidden" name="action" value="reset" />&nbsp;&nbsp;<?php _e("by pressing this reset button, all your saved setting for this theme will be deleted and restore to factory default.", TEMPLATE_DOMAIN); ?>
</div>
</form>
</div>

</div><!-- end option-panel -->
<?php
}

function blogsmu_admin_register() {
global $themename, $shortname, $shortprefix, $options;
if ( !isset($_GET['page']) ) {
$_GET['page'] = '';
}
if ( !isset($_REQUEST['action']) ) {
$_REQUEST['action'] = '';
}
if ( $_GET['page'] == 'options-functions.php' ) {
if ( 'save' == $_REQUEST['action'] ) {

if( is_multisite()):

$post_bg_color = isset($_POST[ 'multisite_adminbar_bg_color' ]) ? $_POST[ 'multisite_adminbar_bg_color' ] : '';
$post_hover_bg_color = isset($_POST[ 'multisite_adminbar_hover_bg_color' ]) ? $_POST[ 'multisite_adminbar_hover_bg_color' ] : '';

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

$post_bg_color = isset($_POST[ 'tn_blogsmu_adminbar_bg_color' ]) ? $_POST[ 'tn_blogsmu_adminbar_bg_color' ] : '';
$post_hover_bg_color = isset($_POST[ 'tn_blogsmu_adminbar_hover_bg_color' ]) ? $_POST[ 'tn_blogsmu_adminbar_hover_bg_color' ] : '';

update_option('tn_blogsmu_adminbar_bg_color', $post_bg_color );
update_option('tn_blogsmu_adminbar_hover_bg_color', $post_hover_bg_color );

if( isset( $_REQUEST[ 'tn_blogsmu_adminbar_bg_color' ] ) ) {
update_option('tn_blogsmu_adminbar_bg_color', $post_bg_color );
} else {
delete_option('tn_blogsmu_adminbar_bg_color' );
}

if( isset( $_REQUEST[ 'tn_blogsmu_adminbar_hover_bg_color' ] ) ) {
update_option('tn_blogsmu_adminbar_hover_bg_color', $post_hover_bg_color );
} else {
delete_option('tn_blogsmu_adminbar_hover_bg_color' );
}

endif;

/*foreach ($options as $value) {
update_option( $value['id'], $_REQUEST[ $value['id'] ] ); }*/
foreach ($options as $value) {
if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } else { delete_option( $value['id'] ); } }
header("Location: themes.php?page=options-functions.php&saved=true");
die;

} else if( 'reset' == $_REQUEST['action'] ) {

if( is_multisite()) {
delete_site_option('multisite_adminbar_bg_color');
delete_site_option('multisite_adminbar_hover_bg_color');
} else {
delete_option('tn_blogsmu_adminbar_bg_color');
delete_option('tn_blogsmu_adminbar_hover_bg_color');
}

foreach ($options as $value) {
delete_option( $value['id'] ); }
header("Location: themes.php?page=options-functions.php&reset=true");
die;
}
}
add_theme_page(_g ($themename . __(' Options' , TEMPLATE_DOMAIN)),  _g (__('Theme Options', TEMPLATE_DOMAIN)),  'edit_theme_options', 'options-functions.php', 'blogsmu_admin_panel');
}

add_action('admin_menu', 'blogsmu_admin_register');


?>