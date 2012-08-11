<?php
////////////////////////////////////////////////////////////////////////////////
// multiple option page
////////////////////////////////////////////////////////////////////////////////
function _g($str) { return __($str, 'option-page'); }

////////////////////////////////////////////////////////////////////////////////
// Load Theme Options
////////////////////////////////////////////////////////////////////////////////
$themename = get_current_theme();

if(file_exists( WP_CONTENT_DIR . '/themes/edu-clean/style.css' )) {
$theme_data = get_theme_data( WP_CONTENT_DIR . '/themes/edu-clean/style.css');
} else {
$theme_data = get_theme_data( TEMPLATEPATH . '/style.css');
}

$theme_version = $theme_data['Version'];

$shortname = "tn";
$shortprefix = "_edus_";

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


$options = array (

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
"name" => __("if you enable the <strong>privacy</strong> on all members profile for none logged in user, insert the full url link they will be redirect to for non logged in users<br /><em>*optional - leave empty for default<br />default are buddypress register link<br /> " . site_url() . '/' .$member_reg_slug . '/' . "</em>", TEMPLATE_DOMAIN),
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


array(
"name" => __("Do you want to enable facebook LIKE in activity stream", TEMPLATE_DOMAIN),
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



//navigation
array(
"name" => __("Choose your navigation background color", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "nav_bg_color",
"inblock" => "nav",
"std" => "",
"type" => "colorpicker"),


array(
"name" => __("Choose your navigation text color", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "nav_text_color",
"inblock" => "nav",
"std" => "",
"type" => "colorpicker"),


array(
"name" => __("Choose your navigation hover background color", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "nav_hover_bg_color",
"inblock" => "nav",
"std" => "",
"type" => "colorpicker"),

array(
"name" => __("Choose your navigation hover border color", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "nav_hover_border_color",
"inblock" => "nav",
"std" => "",
"type" => "colorpicker"),


array(
"name" => __("Choose your navigation hover text color", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "nav_hover_text_color",
"inblock" => "nav",
"std" => "",
"type" => "colorpicker"),




//home

array(
"name" => __("Do you want to used author avatar in post", TEMPLATE_DOMAIN),
"id" => $shortname. $shortprefix . "author_avatar",
"inblock" => "post",
"std" => "yes",
"type" => "select",
"options" => array("yes", "no")),

array(
"name" => __("Do you want to show category meta info in post", TEMPLATE_DOMAIN),
"id" => $shortname. $shortprefix . "post_cat",
"inblock" => "post",
"std" => "yes",
"type" => "select",
"options" => array("yes", "no")),

array(
"name" => __("Enable Facebook like in post", TEMPLATE_DOMAIN),
"id" => $shortname . $shortprefix . "facebook_like_status",
"inblock" => "post",
"type" => "select",
"std" => "disable",
"options" => array("disable", "enable")),



//header
array(
"name" => __("Insert your logo full url here<br /><em>*you can upload your logo in <a target='_blank' href='media-new.php'>media panel</a> and copy paste the url here</em>", TEMPLATE_DOMAIN),
"id" => $shortname. $shortprefix . "header_logo",
"inblock" => "header",
"type" => "text",
"std" => "",
),



array (	"name" => __("Choose your <strong>top header</strong> background color", TEMPLATE_DOMAIN),
			"id" => $shortname. $shortprefix . "top_header_bg_colour",
            "inblock" => "header",
            "std" => "",
			"type" => "colorpicker"),


array(
"name" => __("You can upload a repeat <strong>top header</strong> background image here<br /><em>*you can upload your image in <a href='media-new.php'>media panel</a> and copy paste the url here</em>", TEMPLATE_DOMAIN),
"id" => $shortname. $shortprefix . "top_header_bg_image",
"inblock" => "header",
"type" => "text",
"std" => "",
),



array (	"name" => __("Choose your <strong>top header</strong> text color", TEMPLATE_DOMAIN),
			"id" => $shortname. $shortprefix . "top_header_text_colour",
            "inblock" => "header",
            "std" => "",
			"type" => "colorpicker"),

array (	"name" => __("Choose your <strong>top header</strong> text link color", TEMPLATE_DOMAIN),
			"id" => $shortname. $shortprefix . "top_header_text_link_colour",
            "inblock" => "header",
            "std" => "",
			"type" => "colorpicker"),


array (	"name" => __("Choose your <strong>top header</strong> text link hover color", TEMPLATE_DOMAIN),
			"id" => $shortname. $shortprefix . "top_header_text_link_hover_colour",
            "inblock" => "header",
            "std" => "",
			"type" => "colorpicker"),


//intro
array(
"name" => __("Insert your header main text <em>*for non logged-in user only</em>", TEMPLATE_DOMAIN),
"id" => $shortname. $shortprefix . "header_text",
"inblock" => "intro",
"type" => "textarea",
"std" => "Your WordPress MU Header Title Will Be Here",
),

array(
"name" => __("Insert your header secondary text <em>*for logged-in user only</em>", TEMPLATE_DOMAIN),
"id" => $shortname. $shortprefix . "header_logged_text",
"inblock" => "intro",
"type" => "textarea",
"std" => "Welcome member to the site, browse and join our groups",
),

array(
"name" => __("Insert your header featured listing&nbsp;&nbsp;&nbsp;<em>HTML alllowed</em>", TEMPLATE_DOMAIN),
"id" => $shortname. $shortprefix . "header_listing",
"inblock" => "intro",
"type" => "textarea",
"std" => "<ul>
<li>Effortlessly create and manage blogs</li>
<li>Packed with useful features and customizable themes</li>
<li>Ready made for podcasting, videos, photos and more</li>
<li>Step by step support with our helpful video tutorials</li>
</ul>",
),

 array (	"name" => __("Choose your priority div box background color?<br /><em>this will effect main-header, footer, login-panel and sidebar-header color</em>", TEMPLATE_DOMAIN),
			"id" => $shortname . "_edus_pri_bg_colour",
            "inblock" => "intro",
            "std" => "",
			"type" => "colorpicker"),


  array (	"name" => __("Choose your priority div box border color?<br /><em>this will effect main-header, footer, login-panel and sidebar-header color</em>", TEMPLATE_DOMAIN),
			"id" => $shortname . "_edus_pri_bg_border_colour",
            "inblock" => "intro",
            "std" => "",
			"type" => "colorpicker"),

  array (	"name" => __("Choose your priority div box text color?<br /><em>this will effect main-header, footer, login-panel and sidebar-header color text</em>", TEMPLATE_DOMAIN),
			"id" => $shortname . "_edus_pri_text_colour",
            "inblock" => "intro",
            "std" => "",
			"type" => "colorpicker"),


//css
array(	"name" => __("Choose your body font", TEMPLATE_DOMAIN),
			"id" => $shortname . "_edus_body_font",
            "inblock" => "css",
            "type" => "select-preview",
            "std" => "Arial, sans-serif",
			"options" => array(
            "Arial, sans-serif",
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



array(	"name" => __("Choose your headline font", TEMPLATE_DOMAIN),
			"id" => $shortname . "_edus_headline_font",
            "type" => "select-preview",
            "inblock" => "css",
            "std" => "Arial, sans-serif",
			"options" => array(
            "Arial, sans-serif",
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


   array(	"name" => __("Choose your font size", TEMPLATE_DOMAIN),
			"id" => $shortname."_edus_font_size",
            "inblock" => "css",
			"type" => "select",
            "std" => "normal",
			"options" => array("normal","small", "bigger", "largest")),


     array (	"name" => __("Choose your global links color?<br /><em>this will effect global links</em>", TEMPLATE_DOMAIN),
			"id" => $shortname . "_edus_link_colour",
            "inblock" => "css",
            "std" => "",
			"type" => "colorpicker"),


// featured
array(	"name" => __("Insert your featured header title?", TEMPLATE_DOMAIN),
			"id" => $shortname."_edus_feat_header_title",
            "inblock" => "features",
            "std" => "",
			"type" => "text"),

array("name" => __("Choose the featured block style? *default: service mode <br /> <em>if on <strong>service mode</strong>, you will able to used the custom upload crop image with customize text.</em><br /><em>if on <strong>post mode</strong>, it will get latest featured post based on your category choice below with auto image attachment thumbnail.</em>", TEMPLATE_DOMAIN),
"id" => $shortname."_edus_feat_style",
"inblock" => "features",
"std" => "service mode",
"type" => "select",
"options" => array("service mode", "post mode")),

array(
"name" => __("Choose your <strong>Featured Post Mode</strong> category<br /><em>*only active if you're on post mode</em>", TEMPLATE_DOMAIN),
"id" => $shortname."_edus_feat_postmode",
"inblock" => "features",
"type" => "select",
"std" => "Choose a category:",
"options" => $wp_getcat),



//rss
  array (	"name" => __("Choose your tab background color", TEMPLATE_DOMAIN),
			"id" => $shortname . "_edus_tab_bg_colour",
            "header-title" => __("RSS Tab CSS Options", TEMPLATE_DOMAIN),
            "inblock" => "netrss",
            "std" => "",
			"type" => "colorpicker"),


  array (	"name" => __("Choose your tab border color?", TEMPLATE_DOMAIN),
			"id" => $shortname . "_edus_tab_border_colour",
            "inblock" => "netrss",
            "std" => "",
			"type" => "colorpicker"),

  array (	"name" => __("Choose your tab text color?", TEMPLATE_DOMAIN),
			"id" => $shortname . "_edus_tab_text_colour",
            "inblock" => "netrss",
            "std" => "",
			"type" => "colorpicker"),


  array (	"name" => __("Choose your tab links color?", TEMPLATE_DOMAIN),
			"id" => $shortname . "_edus_tab_link_colour",
            "inblock" => "netrss",
            "std" => "",
			"type" => "colorpicker"),





    array(	"name" => __("<span class=\"important-stuff\">Do you want to used the rss feeds network? *if this disable then below config will not activate</span>", TEMPLATE_DOMAIN),
			"id" => $shortname."_edus_rss_network_status",
            "header-title" => __("RSS Tab Feeds Options", TEMPLATE_DOMAIN),
            "inblock" => "netrss",
            "std" => "no",
			"type" => "select",
            "options" => array("no", "yes")),

    array(	"name" => __("How many word count to pull from your feeds?", TEMPLATE_DOMAIN),
			"id" => $shortname."_edus_feed_word",
            "inblock" => "netrss",
            "std" => "150",
			"type" => "text"),

/* if yes then here we go */

    array(	"name" => __("<div class='inline'></div><strong>Do you want to use the first rss network feed block?</strong>", TEMPLATE_DOMAIN),
			"id" => $shortname."_edus_feedone_status",
            "inblock" => "netrss",
            "std" => "no",
			"type" => "select",
            "options" => array("no", "yes")),

    array(	"name" => __("Insert your first network or sitename here", TEMPLATE_DOMAIN),
			"id" => $shortname."_edus_network_one",
            "inblock" => "netrss",
            "std" => "",
			"type" => "text"),

    array(	"name" => __("Insert the first site rss feeds url here", TEMPLATE_DOMAIN),
			"id" => $shortname."_edus_network_one_url",
            "inblock" => "netrss",
            "std" => "",
			"type" => "text"),


    array(	"name" => __("How many post feeds to show?", TEMPLATE_DOMAIN),
			"id" => $shortname."_edus_network_one_sum",
            "inblock" => "netrss",
       		"type" => "select",
            "std" => "",
			"options" => $choose_count),

    array(	"name" => __("<div class='inline'></div><strong>Do you want to use the second rss network feed block?</strong>", TEMPLATE_DOMAIN),
			"id" => $shortname."_edus_feedtwo_status",
            "inblock" => "netrss",
            "std" => "no",
			"type" => "select",
            "options" => array("no", "yes")),

   array(	"name" => __("Insert your second network or sitename here", TEMPLATE_DOMAIN),
			"id" => $shortname."_edus_network_two",
            "inblock" => "netrss",
            "std" => "",
			"type" => "text"),

    array(	"name" => __("Insert the second site rss feeds url here", TEMPLATE_DOMAIN),
			"id" => $shortname."_edus_network_two_url",
            "inblock" => "netrss",
            "std" => "",
			"type" => "text"),

    array(	"name" => __("How many post feeds to show?", TEMPLATE_DOMAIN),
			"id" => $shortname."_edus_network_two_sum",
            "inblock" => "netrss",
       		"type" => "select",
            "std" => "",
			"options" => $choose_count),

    array(	"name" => __("<div class='inline'></div><strong>Do you want to use the third rss network feed block?</strong>", TEMPLATE_DOMAIN),
			"id" => $shortname."_edus_feedthree_status",
            "inblock" => "netrss",
            "std" => "no",
			"type" => "select",
            "options" => array("no", "yes")),


   array(	"name" => __("Insert your third network or sitename here", TEMPLATE_DOMAIN),
			"id" => $shortname."_edus_network_three",
            "inblock" => "netrss",
            "std" => "",
			"type" => "text"),

    array(	"name" => __("Insert the third site rss feeds url here", TEMPLATE_DOMAIN),
			"id" => $shortname."_edus_network_three_url",
            "inblock" => "netrss",
            "std" => "",
			"type" => "text"),


    array(	"name" => __("How many post feeds to show?", TEMPLATE_DOMAIN),
			"id" => $shortname."_edus_network_three_sum",
            "inblock" => "netrss",
       		"type" => "select",
            "std" => "",
			"options" => $choose_count),

    array(	"name" => __("<div class='inline'></div><strong>Do you want to use the fourth rss network feed block?</strong>", TEMPLATE_DOMAIN),
			"id" => $shortname."_edus_feedfour_status",
            "inblock" => "netrss",
            "std" => "no",
			"type" => "select",
            "options" => array("no", "yes")),

    array(	"name" => __("Insert your fourth network or sitename here", TEMPLATE_DOMAIN),
			"id" => $shortname."_edus_network_four",
            "inblock" => "netrss",
            "std" => "",
			"type" => "text"),

    array(	"name" => __("Insert the fourth site rss feeds url here", TEMPLATE_DOMAIN),
			"id" => $shortname."_edus_network_four_url",
            "inblock" => "netrss",
            "std" => "",
			"type" => "text"),

    array(	"name" => __("How many post feeds to show?", TEMPLATE_DOMAIN),
			"id" => $shortname."_edus_network_four_sum",
            "inblock" => "netrss",
       		"type" => "select",
            "std" => "",
			"options" => $choose_count)

);


function mytheme_edus_admin() {
global $themename, $theme_version, $shortname, $shortprefix, $options, $blog_id, $bp_existed, $bp_front_is_activity;
if ( $_REQUEST['saved'] ) echo '<div id="message" class="updated fade"><p><strong>'. $themename . __(' settings saved.', TEMPLATE_DOMAIN) . '</strong></p></div>';
if ( $_REQUEST['reset'] ) echo '<div id="message" class="updated fade"><p><strong>'. $themename . __(' settings reset.', TEMPLATE_DOMAIN) . '</strong></p></div>';
?>


<div id="options-panel">
<div id="options-head"><h2><?php echo $themename; ?> <?php _e("Theme Options", TEMPLATE_DOMAIN); ?></h2>
<div class="theme-versions"><?php _e("Version",TEMPLATE_DOMAIN); ?> <?php echo $theme_version; ?></div>
</div>

<div id="sbtabs">

<div class="tabmc">
<ul class="ui-tabs-nav" id="tabm">
<?php
$value_var_global = array('intro','header','css','buddypress','features','nav','post','netrss');
?>

<?php if( is_main_site() ) {  ?>
<li><a href="#tab1"><?php _e("Admin Bar Settings", TEMPLATE_DOMAIN); ?></a></li>
<?php } ?>

<li><a href="#tab2"><?php _e("Intro Settings",TEMPLATE_DOMAIN); ?></a></li>
<li><a href="#tab3"><?php _e("Header Settings",TEMPLATE_DOMAIN); ?></a></li>
<li><a href="#tab4"><?php _e("CSS Settings",TEMPLATE_DOMAIN); ?></a></li>
<?php if($bp_existed == 'true') { //only showed if buddypress installed ?>
<li><a href="#tab5"><?php _e("BuddyPress Settings",TEMPLATE_DOMAIN); ?></a></li>
<?php } ?>
<li><a href="#tab6"><?php _e("Features Settings",TEMPLATE_DOMAIN); ?></a></li>
<li><a href="#tab7"><?php _e("Navigation Settings",TEMPLATE_DOMAIN); ?></a></li>
<li><a href="#tab8"><?php _e("Post Settings",TEMPLATE_DOMAIN); ?></a></li>
<li><a href="#tab9"><?php _e("RSS Tabs Settings",TEMPLATE_DOMAIN); ?></a></li>
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
<?php foreach ($options as $value) { ?>

<?php if (($value['inblock'] == $value_var) && ($value['header-title'] != "")) { // if we got header title for option ?>
<h4><?php echo stripslashes($value['header-title']); ?><?php if(!empty($value['header-img-link'])) { ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="whatsthis" href="#thumb">(What's this?)<span><img src="<?php echo get_template_directory_uri(); ?><?php echo stripslashes($value['header-img-link']); ?>" /></span></a><?php } ?></h4>
<?php } ?>

<?php if (($value['inblock'] == $value_var) && ($value['type'] == "text")) { // setting ?>
<div class="tab-option">
<div class="description"><?php echo $value['name']; ?><br /><span><?php echo $value['description']; ?></span></div>
<div class="input-option"><p><input name="<?php echo $value['id']; ?>" class="myfield" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo stripslashes(get_option( $value['id']) ); } else { echo stripslashes($value['std']); } ?>" /></p></div>
</div>

<?php } else if (($value['inblock'] == $value_var) && ($value['type'] == "ajax-file-upload")) { // setting ?>

<div class="tab-option">
<div class="description"><?php echo $value['name']; ?><br /><span><?php echo $value['description']; ?></span></div>
<div class="input-option">
</div>
</div>

<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "checkbox") ) { // setting ?>

<?php if(get_option($value['id'])) { $checked = "checked=\"checked\""; } else { $checked = ""; } ?>
<div class="checkbox-box">
<div class="description"><p><input type="<?php echo $value['type']; ?>" class="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="<?php echo $value['id']; ?>" <?php echo $checked; ?> />&nbsp;&nbsp;<?php echo $value['name']; ?></p></div></div>

<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "textarea")) { // setting ?>

<div class="tab-option">
<?php
$valuex = $value['id'];
$valuey = stripslashes($valuex);
$video_code = get_option($valuey);
?>
<div class="description"><?php echo $value['name']; ?><br /><span><?php echo $value['description']; ?></span></div>
<div class="input-option"><p><textarea name="<?php echo $valuey; ?>" class="mytext" cols="40%" rows="8" /><?php if ( get_option($valuey) != "") { echo stripslashes($video_code); } else { echo $value['std']; } ?>
</textarea></p></div>
</div>
<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "colorpicker") ) { // setting ?>
<?php $i == $i++ ; ?>
<div class="tab-option">
<div class="description"><?php echo $value['name']; ?><br /><span><?php echo $value['description']; ?></span></div>
<div class="input-option"><p><input class="color {required:false,hash:true}" name="<?php echo $value['id']; ?>" id="colorpickerField<?php echo $i; ?>" type="text" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" /></p></div>
</div>

<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "select-preview") ) { // setting ?>
<div class="tab-option">
<div class="description"><?php echo $value['name']; ?><br /><span><?php echo $value['description']; ?></span></div>
<div class="input-option"><p><select name="<?php echo $value['id']; ?>" class="myselect" id="<?php echo $value['id']; ?>">
<?php foreach ($value['options'] as $option) { ?>
<option style="font-family:<?php echo $option; ?>;" <?php if ( get_option( $value['id'] ) == $option) { echo ' selected="selected"'; } elseif ($option == get_option( $value['std']) ) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option>
<?php } ?>
</select>
</p>
</div>
</div>

<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "select") ) { // setting ?>
<div class="tab-option">
<div class="description"><?php echo $value['name']; ?><br /><span><?php echo $value['description']; ?></span></div>
<div class="input-option"><p><select name="<?php echo $value['id']; ?>" class="myselect" id="<?php echo $value['id']; ?>">
<?php foreach ($value['options'] as $option) { ?>
<option<?php if ( get_option( $value['id'] ) == $option) { echo ' selected="selected"'; } elseif ($option == $value['std']) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option>
<?php } ?>
</select>
</p>
</div>
</div>
<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "custom-radio") ) { // setting ?>
<div class="tab-option">
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



function mytheme_add_edus_admin() {
global $themename, $shortname, $options;
if ( $_GET['page'] == 'options-functions.php' ) {
if ( 'save' == $_REQUEST['action'] ) {

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


$post_bg_color = $_POST[ 'tn_edus_adminbar_bg_color' ];
$post_hover_bg_color = $_POST[ 'tn_edus_adminbar_hover_bg_color' ];

update_option('tn_edus_adminbar_bg_color', $post_bg_color );
update_option('tn_edus_adminbar_hover_bg_color', $post_hover_bg_color );

if( isset( $_REQUEST[ 'tn_edus_adminbar_bg_color' ] ) ) {
update_option('tn_edus_adminbar_bg_color', $post_bg_color );
} else {
delete_option('tn_edus_adminbar_bg_color' );
}

if( isset( $_REQUEST[ 'tn_edus_adminbar_hover_bg_color' ] ) ) {
update_option('tn_edus_adminbar_hover_bg_color', $post_hover_bg_color );
} else {
delete_option('tn_edus_adminbar_hover_bg_color' );
}


 endif;

foreach ($options as $value) {
update_option( $value['id'], $_REQUEST[ $value['id'] ] ); }
foreach ($options as $value) {
if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } else { delete_option( $value['id'] ); } }
header("Location: themes.php?page=options-functions.php&saved=true");
die;
} else if( 'reset' == $_REQUEST['action'] ) {

              if( is_multisite()) {
  delete_site_option('multisite_adminbar_bg_color');
  delete_site_option('multisite_adminbar_hover_bg_color');
  } else {
  delete_option('tn_edus_adminbar_bg_color');
  delete_option('tn_edus_adminbar_hover_bg_color');
  }

foreach ($options as $value) {
delete_option( $value['id'] ); }
header("Location: themes.php?page=options-functions.php&reset=true");
die;
}
}
add_theme_page(_g ($themename . __(' Options',TEMPLATE_DOMAIN)),  _g (__('Theme Options', TEMPLATE_DOMAIN)),  'edit_theme_options', 'options-functions.php', 'mytheme_edus_admin');
}



?>