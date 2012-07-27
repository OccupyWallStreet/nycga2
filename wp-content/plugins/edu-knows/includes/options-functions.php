<?php
////////////////////////////////////////////////////////////////////////////////
// multiple option page
////////////////////////////////////////////////////////////////////////////////
function _g($str) { return __($str, 'option-page'); }
////////////////////////////////////////////////////////////////////////////////
// theme option menu for Community
////////////////////////////////////////////////////////////////////////////////

$themename = get_current_theme();
$theme_data = get_theme_data( WP_CONTENT_DIR . '/themes/edu-knows/style.css');
$theme_version = $theme_data['Version'];

$shortname = "tn";
$shortprefix = "_edufaq_";


// get featured category
$wp_dropdown_rd_admin = get_categories('hide_empty=0');
$wp_getcat = array();
foreach ($wp_dropdown_rd_admin as $category_list) {
$wp_getcat[$category_list->cat_ID] = $category_list->category_nicename;
}
$category_bulk_list = array_unshift($wp_getcat, "Choose a category");
$choose_count = array("Select a number","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19","20");


////////////////////////////////////////////////////////////////////
///// start theme setting
///////////////////////////////////////////////////////////////////
$options = array (

     //site
     array ( "name" => __("Enter your logo full url here: <br /><em>*you can upload your logo (max size 300px width and 100px height) in <a target='_blank' href='media-new.php'>media panel</a> and copy paste the url here</em>", TEMPLATE_DOMAIN),
			"id" => $shortname."_edufaq_header_logo",
            "inblock" => "site",
            "std" => "",
			"type" => "text"),

     array ("name" => __("Enter your desired site title<em> *optional</em>", TEMPLATE_DOMAIN),
			"id" => $shortname."_edufaq_site_custom_name",
            "inblock" => "site",
            "std" => "",
			"type" => "text"),

     array ("name" => __("Enter your desired site description<em> *optional</em>", TEMPLATE_DOMAIN),
			"id" => $shortname."_edufaq_site_custom_description",
            "inblock" => "site",
            "std" => "",
			"type" => "text"),

     array(	"name" => __("Choose your desired background style:", TEMPLATE_DOMAIN),
			"id" => $shortname."_edufaq_body_bg",
            "inblock" => "site",
			"type" => "select",
            "std" => "Default Blue",
			"options" => array("Default Blue", "Red", "Yellow", "Grey", "Light Brown", "Light Green", "Dark Green", "White", "White Shade")),

    //css
    array(	"name" => __("Choose your global body font?", TEMPLATE_DOMAIN),
			"id" => $shortname."_edufaq_body_font",
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

	array(	"name" => __("Choose your global headline font", TEMPLATE_DOMAIN),
			"id" => $shortname."_edufaq_headline_font",
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

    array(	"name" => __("Choose your font size here?", TEMPLATE_DOMAIN),
			"id" => $shortname."_edufaq_font_size",
            "inblock" => "css",
			"type" => "select",
            "std" => "normal",
			"options" => array("normal","small", "bigger", "largest")),






    array (	"name" => __("Choose your prefered global links colour:", TEMPLATE_DOMAIN),
			"id" => $shortname."_edufaq_content_link_colour",
            "inblock" => "css",
            "std" => "",
			"type" => "colorpicker"),


    //sbox
    array (	"name" => __("Choose your prefered searchbox colour:", TEMPLATE_DOMAIN),
			"id" => $shortname."_edufaq_searchbox_colour",
            "inblock" => "sbox",
            "std" => "",
			"type" => "colorpicker"),


    array (	"name" => __("Choose your prefered searchbox border colour:", TEMPLATE_DOMAIN),
			"id" => $shortname."_edufaq_searchbox_border_colour",
            "inblock" => "sbox",
            "std" => "",
			"type" => "colorpicker"),





    //main
    array(	"name" => __("Top Faq Main Header Text <em>*html allowed</em>", TEMPLATE_DOMAIN),
			"id" => $shortname."_edufaq_top_faq_header",
            "inblock" => "main",
            "std" => "",
			"type" => "text"),

    array(	"name" => __("Top Faq Main Text <em>*html allowed</em>", TEMPLATE_DOMAIN),
			"id" => $shortname."_edufaq_top_faq",
            "inblock" => "main",
            "std" => "",
			"type" => "textarea"),



   //extra
    array (	"name" => __("If you want to use FeedBurner Feeds, insert your FeedBurner url here<br /><em>sample: http://feed2.feedburner.com/name_id</em>", TEMPLATE_DOMAIN),
			"id" => $shortname."_edufaq_feedburner_url",
            "inblock" => "extra",
            "std" => "",
			"type" => "text"),


    array (	"name" => __("If you want to use Twitter Follow, insert your Twitter url here<br /><em>sample: http://twitter.com/name_id</em>", TEMPLATE_DOMAIN),
			"id" => $shortname."_edufaq_twitter_url",
            "inblock" => "extra",
            "std" => "",
			"type" => "text"),


    array(	"name" => __("Enable Post Mini Socials?", TEMPLATE_DOMAIN),
			"id" => $shortname."_edufaq_mini_social",
            "inblock" => "extra",
			"type" => "select",
            "std" => "yes",
			"options" => array("yes","no")),


   array(	"name" => __("Enable Author Post Gravatar?", TEMPLATE_DOMAIN),
			"id" => $shortname."_edufaq_post_gravatar",
            "inblock" => "extra",
			"type" => "select",
            "std" => "yes",
			"options" => array("yes","no"))

);


function edufaq_admin_panel() {
global $themename, $theme_version, $shortname, $options, $blog_id, $bp_existed, $bp_front_is_activity;
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
$value_var_global = array('site','main','css', 'sbox', 'extra');
?>
<li><a href="#tab1"><?php _e("Site Settings", TEMPLATE_DOMAIN); ?></a></li>
<li><a href="#tab2"><?php _e("Main Settings",TEMPLATE_DOMAIN); ?></a></li>
<li><a href="#tab3"><?php _e("CSS Settings",TEMPLATE_DOMAIN); ?></a></li>
<li><a href="#tab4"><?php _e("Search Box Settings",TEMPLATE_DOMAIN); ?></a></li>
<li><a href="#tab5"><?php _e("Extra Settings",TEMPLATE_DOMAIN); ?></a></li>
</ul>
</div>


<div class="tabmc-right">

<div class="tabc">

<form action="" method="post">

<?php $vc = 1; foreach ($value_var_global as $value_var) { ?>
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
<input name="reset" type="submit" class="sbutton" value="<?php echo esc_attr(__('Reset All Options',TEMPLATE_DOMAIN)); ?>" />
<input type="hidden" name="action" value="reset" />&nbsp;&nbsp;<?php _e("by pressing this reset button, all your saved setting for this theme will be deleted and restore to factory default.",TEMPLATE_DOMAIN); ?>
</div>
</form>
</div>

</div><!-- end option-panel -->
<?php
}


function edufaq_admin_register() {
global $themename, $shortname, $options;
if ( $_GET['page'] == 'options-functions.php' ) {
if ( 'save' == $_REQUEST['action'] ) {
foreach ($options as $value) {
update_option( $value['id'], $_REQUEST[ $value['id'] ] ); }
foreach ($options as $value) {
if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } else { delete_option( $value['id'] ); } }
header("Location: themes.php?page=options-functions.php&saved=true");
die;
} else if( 'reset' == $_REQUEST['action'] ) {
foreach ($options as $value) {
delete_option( $value['id'] ); }
header("Location: themes.php?page=options-functions.php&reset=true");
die;
}
}
add_theme_page(_g ($themename . __(' Theme Options', TEMPLATE_DOMAIN)),  _g (__('Theme Options', TEMPLATE_DOMAIN)),  'edit_theme_options', 'options-functions.php', 'edufaq_admin_panel');
}
add_action('admin_menu', 'edufaq_admin_register');

?>