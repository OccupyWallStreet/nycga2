<?php
function wt_get_ID_by_page_name($page_name)
{
	global $wpdb;
	$page_name_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = '".$page_name."'");
	return $page_name_id;
}

function _g($str)
{
return __($str, 'option-page');
}

$themename = "BuddyPress scholar";
$themeversion = "1.0";
$shortname = "ne";
$shortprefix = "_buddyscholar_";
/* get pages so can set them */
$ne_pages_obj = get_pages();
$ne_pages = array();
foreach ($ne_pages_obj as $ne_cat) {
	$ne_pages[$ne_cat->ID] = $ne_cat->post_name;
}
$pages_tmp = array_unshift($ne_pages, "Select a page:");
/* end of get pages */
/* get categories so can set them */
$ne_categories_obj = get_categories('hide_empty=0');
$ne_categories = array();
foreach ($ne_categories_obj as $ne_cat) {
	$ne_categories[$ne_cat->cat_ID] = $ne_cat->category_nicename;
}
$categories_tmp = array_unshift($ne_categories, "Select a category:");
/* end of get categories */

/* start of theme options */
$options = array (

array("name" => __("Do you want to use the slideshow on the front?", "bp_scholar"),
	"id" => $shortname . $shortprefix . "slideshowon",	     	
	"box"=> "1",
	"inblock" => "homepage",
    "type" => "select",
	"std" => "Select yes or no",
	"options" => array("yes", "no")),
			
array("name" => __("Select your slideshow speed in milliseconds? *(default is 3000 ms)", "bp_scholar"),
	"id" => $shortname . $shortprefix . "slideshow_speed",	     	
	"box"=> "1",
	"inblock" => "homepage",
    "type" => "select",
	"std" => "Select yes or no",
	"options" => array("1000", "3000", "5000", "7000", "9000")),

array("name" => __("Select number of posts to show in slideshow", "bp_scholar"),					
	"id" => $shortname . $shortprefix . "slideshow_number",	     	
	"box"=> "1",
	"inblock" => "homepage",
   	"type" => "select",
	"std" => "Select yes or no",
	"options" => array("2", "4", "6", "8", "10", "Unlimited")),

array( 	"name" => __("Select a category for your slideshow", "bp_scholar"),
	"id" => $shortname . $shortprefix . "feature_cat",
	"box"=> "1",
	"inblock" => "homepage",
	"type" => "select",
	"std" => "Select a category:",
	"options" => $ne_categories),		

array("name" => __("Select display size of featured image (if above size it will center and show a portion)", "bp_scholar"),
	"id" => $shortname . $shortprefix . "feature_image_size",	     	
	"box"=> "1",
	"inblock" => "homepage",
	"type" => "select",
	"std" => "Pick a size",
	"options" => array("medium", "large")),

array( 	"name" => __("Select a category for your latest news section", "bp_scholar"),
	"id" => $shortname . $shortprefix . "news_cat",
	"box"=> "1",	
	"inblock" => "homepage",
	"type" => "select",
	"std" => "Select a category:",
	"options" => $ne_categories),

array("name" => __("Select display of latest news images (if above size it will center and show a portion)", "bp_scholar"),
	"id" => $shortname . $shortprefix . "news_image_size",	     	
	"box"=> "1",
	"inblock" => "homepage",
	"type" => "select",
	"std" => "Pick a size",
	"options" => array("thumbnail", "medium", "large")),

array("name" => __("Select number of latest news posts to show", "bp_scholar"),					
	"id" => $shortname . $shortprefix . "news_number",	     	
	"box"=> "1",
	"inblock" => "homepage",
	 "type" => "select",
	 "std" => "Select",
	 "options" => array("1", "2", "3", "4", "5", "6")),

array( 	"name" => __("Select a category for your spotlight section", "bp_scholar"),
	"id" => $shortname . $shortprefix . "spotlight_cat",
	"box"=> "1",	
	"inblock" => "homepage",
	"type" => "select",
	"std" => "Select a category:",
	"options" => $ne_categories),	

array("name" => __("Select display of spotlight images (if above size it will center and show a portion)", "bp_scholar"),
	"id" => $shortname . $shortprefix . "spotlight_image_size",	     	
	"box"=> "1",
	"inblock" => "homepage",
	"type" => "select",
	"std" => "Pick a size",
	"options" => array("thumbnail", "medium", "large")),

array("name" => __("Select number of spotlight posts to show", "bp_scholar"),					
	"id" => $shortname . $shortprefix . "spotlight_number",	     	
	"box"=> "1",
	"inblock" => "homepage",
	"type" => "select",
	"std" => "Select",
	"options" => array("1", "2", "3", "4", "5", "6")),

array(
	"name" => __("Enter your title for welcome message", "bp_scholar"),
	"id" => $shortname . $shortprefix . "welcome_title",
	"box"=> "1",
	"inblock" => "homepage",
	"type" => "textarea",
	"std" => "",
),

array(
	"name" => __("Enter your welcome message text", "bp_scholar"),
	"id" => $shortname . $shortprefix . "welcome_message",
	"box"=> "1",
	"inblock" => "homepage",
	"type" => "textarea",
	"std" => "",
),

array("name" => __("Do you want to have a follow us social media sidebar section?", "bp_scholar"),
	"id" => $shortname . $shortprefix . "followuson",	     	
	"box"=> "1",
	"inblock" => "homepage",
    "type" => "select",
	"std" => "Select yes or no",
	"options" => array("yes", "no")),

array(
	"name" => __("Insert your twitter url", "bp_scholar"),
	"id" => $shortname . $shortprefix . "twitter_url",
	"box"=> "1",
	"inblock" => "homepage",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("Insert your facebook url", "bp_scholar"),
	"id" => $shortname . $shortprefix . "facebook_url",
	"box"=> "1",
	"inblock" => "homepage",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("Insert your flickr url", "bp_scholar"),
	"id" => $shortname . $shortprefix . "flickr_url",
	"box"=> "1",
	"inblock" => "homepage",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("Insert your youtube url", "bp_scholar"),
	"id" => $shortname . $shortprefix . "youtube_url",
	"box"=> "1",
	"inblock" => "homepage",
	"type" => "text",
	"std" => "",
),

array("name" => __("Do you to use a custom large image logo rather than domain name text?<br /><em>*Enter your url in the next section if saying yes</em>", "bp_scholar"),
	"id" => $shortname . $shortprefix . "header_image",	     	
	"box"=> "2",
	"inblock" => "header",
	"type" => "select",
	"std" => "Select",
	"options" => array("yes", "no")),

array(
	"name" => __("Insert your logo full url here<br /><em>*you can upload your logo in <a href='media-new.php'>media panel</a> and copy paste the url here</em>", "bp_scholar"),
	"id" => $shortname . $shortprefix . "header_logo",
	"box"=> "2",
	"inblock" => "header",
	"type" => "text",
	"std" => "",
),

array("name" => __("Do you to use a custom square image logo and your domain name text?<br /><em>*Enter your url in the next section if saying yes</em>", "bp_scholar"),
	"id" => $shortname . $shortprefix . "header_image_square",	     	
	"box"=> "2",
	"inblock" => "header",
	"type" => "select",
	"std" => "Select",
	"options" => array("yes", "no")),

array(
	"name" => __("Insert your square logo full url here<br /><em>*you can upload your logo in <a href='media-new.php'>media panel</a> and copy paste the url here</em>", "bp_scholar"),
	"id" => $shortname . $shortprefix . "header_logo_square",
	"box"=> "2",
	"inblock" => "header",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("Enter a site title", "bp_scholar"),
	"id" => $shortname . $shortprefix . "header_title",
	"box"=> "2",
	"inblock" => "header",
	"type" => "textarea",
	"std" => "Your site",
),

array("name" => __("Do you want to include a short site description in your header?", "bp_scholar"),
	"id" => $shortname . $shortprefix . "header_description_on",	     	
	"box"=> "2",
	"inblock" => "header",
	"type" => "select",
	"std" => "Select",
	"options" => array("yes", "no")),

array("name" => __("Enter a short site description", "bp_scholar"),
  	"id" => $shortname . $shortprefix . "header_description",
	"box"=> "2",
	"inblock" => "header",
	"type" => "textarea",
	"std" => "Find out more"),

array("name" => __("Do you want to have a site message?", "bp_scholar"),
	"id" => $shortname . $shortprefix . "site_message_on",	     	
	"box"=> "2",
	"inblock" => "header",
	"type" => "select",
	"std" => "Select",
	"options" => array("yes", "no")),

array("name" => __("Enter a site message", "bp_scholar"),
 	"id" => $shortname . $shortprefix . "site_message",
	"box"=> "2",
	"inblock" => "header",
	"type" => "textarea",
	"std" => "Site message"),

array(
	"name" => __("Insert your advert full url here<br /><em>*you can upload your logo in <a href='media-new.php'>media panel</a> and copy paste the url here</em> (size is 468 x 60px)", "bp_scholar"),
	"id" => $shortname . $shortprefix . "header_advert",
	"box"=> "2",
	"inblock" => "header",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("Give your advert a title", "bp_scholar"),
	"id" => $shortname . $shortprefix . "header_advert_title",
	"box"=> "2",
	"inblock" => "header",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("If you want your advert to link somewhere please give the full url here", "bp_scholar"),
	"id" => $shortname . $shortprefix . "header_advert_link",
	"box"=> "2",
	"inblock" => "header",
	"type" => "text",
	"std" => "",
),

);

function buddyscholar_admin_panel() {
	echo "<div id=\"admin-options\">";
    global $themename, $shortname, $options;

    if ( isset($_REQUEST['saved'] )) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings saved.</strong></p></div>';
    if ( isset($_REQUEST['reset'] )) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings reset.</strong></p></div>';
?>

<h4><?php echo "$themename"; ?> <?php _e('Theme Options', 'bp-scholar'); ?></h4>

<form action="" method="post">

<?php if( $value['box'] = '1' ) {  ?>
	
<div class="get-option">
<h2><?php _e('Front page settings', 'bp-scholar') ?></h2>

<?php foreach ($options as $value) { ?>
	
<!-- if text box -->
<?php if (($value['inblock'] == "homepage") && ($value['type'] == "text")) { ?>
<div class="option-save">
<div class="description"><?php echo $value['name']; ?></div>
<div class="option-box">
	<p><input name="<?php echo $value['id']; ?>" class="myfield" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" /></p>
</div>
</div>

<!-- if text area -->
<?php } elseif (($value['inblock'] == "homepage") && ($value['type'] == "textarea")) { ?>

<?php
$valuex = $value['id'];
$valuey = stripslashes($valuex);
$value_code = get_option($valuey);
?>
<div class="option-save">
<div class="description"><?php echo $value['name']; ?></div>
<div class="option-box"><p><textarea name="<?php echo $valuey; ?>" class="mytext" cols="40%" rows="8" /><?php if ( get_option($valuey) != "") { echo stripslashes($value_code); } else { echo $value['std']; } ?></textarea></p>
</div>
</div>

<!-- if colorpicker -->
<?php } elseif (($value['inblock'] == "homepage") && ($value['type'] == "colorpicker") ) {?>
<div class="option-save">
<div class="description"><?php echo $value['name']; ?></div>
	<?php $i = ""; $i == $i++ ; ?>
<div class="option-box">
	<p><input class="color {required:false,hash:true}" name="<?php echo $value['id']; ?>" id="colorpickerField<?php echo $i; ?>" type="text" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" />
</p>
</div>
</div>

<!-- if select -->
<?php } elseif (($value['inblock'] == "homepage") && ($value['type'] == "select") ) {  ?>
<div class="option-save">
<div class="description"><?php echo $value['name']; ?></div>
<div class="option-box">
	<p><select name="<?php echo $value['id']; ?>" class="myselect" id="<?php echo $value['id']; ?>">
	<?php foreach ($value['options'] as $option) { ?>
	<option<?php if ( get_option( $value['id'] ) == $option) { echo ' selected="selected"'; } elseif ($option == $value['std']) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option>
<?php } ?>
</select>
</p>
</div>
</div>

<?php } ?>

<?php } ?>

</div>

<?php } ?>
<?php if( $value['box'] = '2' ) {  ?>
	
<div class="get-option">
<h2><?php _e('Header settings', 'bp-scholar') ?></h2>

<?php foreach ($options as $value) { ?>
	
<!-- if text box -->
<?php if (($value['inblock'] == "header") && ($value['type'] == "text")) { ?>
<div class="option-save">
<div class="description"><?php echo $value['name']; ?></div>
<div class="option-box">
	<p><input name="<?php echo $value['id']; ?>" class="myfield" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" /></p>
</div>
</div>

<!-- if text area -->
<?php } elseif (($value['inblock'] == "header") && ($value['type'] == "textarea")) { ?>

<?php
$valuex = $value['id'];
$valuey = stripslashes($valuex);
$value_code = get_option($valuey);
?>
<div class="option-save">
<div class="description"><?php echo $value['name']; ?></div>
<div class="option-box"><p><textarea name="<?php echo $valuey; ?>" class="mytext" cols="40%" rows="8" /><?php if ( get_option($valuey) != "") { echo stripslashes($value_code); } else { echo $value['std']; } ?></textarea></p>
</div>
</div>

<!-- if colorpicker -->
<?php } elseif (($value['inblock'] == "header") && ($value['type'] == "colorpicker") ) {?>
<div class="option-save">
<div class="description"><?php echo $value['name']; ?></div>
	<?php $i = ""; $i == $i++ ; ?>
<div class="option-box">
	<p><input class="color {required:false,hash:true}" name="<?php echo $value['id']; ?>" id="colorpickerField<?php echo $i; ?>" type="text" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" />
</p>
</div>
</div>

<!-- if select -->
<?php } elseif (($value['inblock'] == "header") && ($value['type'] == "select") ) {  ?>
<div class="option-save">
<div class="description"><?php echo $value['name']; ?></div>
<div class="option-box">
	<p><select name="<?php echo $value['id']; ?>" class="myselect" id="<?php echo $value['id']; ?>">
	<?php foreach ($value['options'] as $option) { ?>
	<option<?php if ( get_option( $value['id'] ) == $option) { echo ' selected="selected"'; } elseif ($option == $value['std']) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option>
<?php } ?>
</select>
</p>
</div>
</div>

<?php } ?>

<?php } ?>

</div>

<?php } ?>


<p id="top-margin" class="save-p">
<input name="save" type="submit" class="sbutton" value="<?php echo esc_attr(__('Save Options', 'bp-scholar')); ?>" />
<input type="hidden" name="theme_action" value="save" />
</p>
</form>

<form method="post">
<p class="save-p">
<input name="reset" type="submit" class="sbutton" value="<?php echo esc_attr(__('Reset Options', 'bp-scholar')); ?>" />
<input type="hidden" name="theme_action" value="reset" />
</p>

</form>
</div>

<?php
}
function buddyscholar_admin_register() {
	global $themename, $shortname, $options;
			$action = isset($_REQUEST['theme_action']);
	if ( isset($_GET['page']) == 'functions.php' ) {
		if ( 'save' == $action ) {
			foreach ($options as $value) {	
				update_option( $value['id'], isset($_REQUEST[ $value['id'] ] )); }
			foreach ($options as $value) {
				if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); 
				} 
				else { delete_option( $value['id'] ); } 
			}
			header("Location: themes.php?page=functions.php&saved=true");
			die;
			} 
			else if( 'reset' == $action ) {
				foreach ($options as $value) {
					delete_option( $value['id'] ); 
				}
			header("Location: themes.php?page=functions.php&reset=true");
			die;
			}
		}
		add_theme_page(_g ($themename . __(' Theme Options', 'bp-scholar')),  _g (__('Theme Options', 'bp-scholar')),  'edit_theme_options', 'functions.php', 'buddyscholar_admin_panel');
}
/* End of theme options */

function buddyscholar_admin_head() { ?>
	<link href="<?php bloginfo('template_directory'); ?>/_inc/admin/custom-admin.css" rel="stylesheet" type="text/css" />
<?php if(isset($_GET["page"]) == "styling-functions.php") { ?>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/_inc/js/jscolor.js"></script>
<?php } ?>
<?php }

/* Preset Styling section */
/* stylesheet additiond */
$alt_stylesheet_path = get_template_directory() .'/_inc/styles/';
$alt_stylesheets = array();

if ( is_dir($alt_stylesheet_path) ) {
	if ($alt_stylesheet_dir = opendir($alt_stylesheet_path) ) {
		while ( ($alt_stylesheet_file = readdir($alt_stylesheet_dir)) !== false ) {
			if(stristr($alt_stylesheet_file, ".css") !== false) {
				$alt_stylesheets[] = $alt_stylesheet_file;
			}
		}
	}
}

$category_bulk_list = array_unshift($alt_stylesheets, "default.css");
	$options2 = array (

	array(  "name" => __("Choose Your BuddyPress scholar Preset Style:", "bp_scholar"),
		  	"id" => $shortname. $shortprefix . "custom_style",
			"std" => "default.css",
			"type" => "radio",
			"options" => $alt_stylesheets)
	);

function buddyscholar_ready_style_admin_panel() {
	echo "<div id=\"admin-options\">";
	
	global $themename, $shortname, $options2;
	
	if ( isset($_REQUEST['saved2'] )) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings saved.</strong></p></div>';
	if ( isset($_REQUEST['reset2'] )) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings reset.</strong></p></div>';
?>

<h4><?php echo "$themename"; ?> <?php _e('Choose your BuddyPress scholar Preset Style', 'bp-scholar'); ?></h4>
<h2><?php _e('If you want to customise the theme options you MUST have default.css selected'); ?></h2>
<form action="" method="post">
<div class="get-listings">
<h2><?php _e("Style Select:", "bp_scholar") ?></h2>
<div class="option-save">
<ul>
<?php foreach ($options2 as $value) { ?>

<?php foreach ($value['options'] as $option2) {
$screenshot_img = substr($option2,0,-4);
$radio_setting = get_option($value['id']);
if($radio_setting != '') {	
	if (get_option($value['id']) == $option2) { 
		$checked = "checked=\"checked\""; } else { $checked = ""; 
	}
} 
else {
	if(get_option($value['id']) == $value['std'] ){ 
		$checked = "checked=\"checked\""; 
	} 
	else { 
		$checked = ""; 
	}
} ?>

<li>
<div class="theme-img">
	<img src="<?php bloginfo('template_directory'); ?>/_inc/styles/images/<?php echo $screenshot_img . '.png'; ?>" alt="<?php echo $screenshot_img; ?>" />
</div>
<input type="radio" name="<?php echo $value['id']; ?>" value="<?php echo $option2; ?>" <?php echo $checked; ?> /><?php echo $option2; ?>
</li>

<?php } 
} ?>

</ul>
</div>
</div>
	<p id="top-margin" class="save-p">
		<input name="save2" type="submit" class="sbutton" value="<?php echo esc_attr(__('Save Options', 'bp-scholar')); ?>" />
		<input type="hidden" name="theme_action2" value="save2" />
	</p>
</form>

<form method="post">
	<p class="save-p">
		<input name="reset2" type="submit" class="sbutton" value="<?php echo esc_attr(__('Reset Options', 'bp-scholar')); ?>" />
		<input type="hidden" name="theme_action2" value="reset2" />
	</p>
</form>
</div>

<?php }

function buddyscholar_ready_style_admin_register() {
	global $themename, $shortname, $options2;
			$action2 = isset($_REQUEST['theme_action2']);
	if ( isset($_GET['page']) == 'buddyscholar-themes.php' ) {
		if ( 'save2' == $action2 ) {
			foreach ($options2 as $value) {
				update_option( $value['id'], isset($_REQUEST[ $value['id'] ] )); 
			}
			foreach ($options2 as $value) {
				if( isset( $_REQUEST[ $value['id'] ] ) ) { 
					update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); 
				} 
				else { 
					delete_option( $value['id'] ); 
				} 
			}	
			header("Location: themes.php?page=buddyscholar-themes.php&saved2=true");
			die;
		} 
		else if( 'reset2' == $action2 ) {
			foreach ($options2 as $value) {
				delete_option( $value['id'] ); 
			}
			header("Location: themes.php?page=buddyscholar-themes.php&reset2=true");
			die;
		}
	}
	add_theme_page(_g (__('BuddyPress scholar Preset Style', 'bp-scholar')),  _g (__('Preset Style', 'bp-scholar')),  'edit_theme_options', 'buddyscholar-themes.php', 'buddyscholar_ready_style_admin_panel');
}

/* end of preset styling section */

/* custom styling section */
$options3 = array (

array(
	"name" => __("Choose your body font", "bp_scholar"),
	"box" => "1",
	"id" => $shortname . $shortprefix . "body_font",
	"type" => "select",
	"inblock" => "headers",
	"std" => "Tahoma, Helvetica, Sans-serif",
				"options" => array(
	            "Lucida Grande, Lucida Sans, sans-serif",
	            "Arial, sans-serif",
	            "Verdana, sans-serif",
	            "Trebuchet MS, sans-serif",
	            "Fertigo, serif",
	            "Georgia, serif",
	            "Cambria, Georgia, serif",
	            "Tahoma, sans-serif",
	            "Helvetica, Arial, sans-serif",
	            "Corpid, Corpid Bold, sans-serif",
	            "Century Gothic, Century, sans-serif",
	            "Palatino Linotype, Times New Roman, serif",
	            "Garamond, Georgia, serif",
	            "Caslon Book BE, Caslon, Arial Narrow",
	            "Arial Rounded Bold, Arial",
	            "Arial Narrow, Arial",
	            "Myriad Pro, Calibri, sans-serif",
	            "Candara, Calibri, Lucida Grande",
	            "Univers LT 55, Univers LT Std 55, Univers, sans-serif",
	            "Ronda, Ronda Light, Century Gothic",
	            "Century, Times New Roman, serif",
	            "Courier New, Courier, monospace",
	            "Walbaum LT Roman, Walbaum, Times New Roman",
	            "Dax, Dax-Regular, Dax-Bold, Trebuchet MS",
	            "VAG Round, Arial Rounded Bold, sans-serif",
	            "Humana Sans ITC, Humana Sans Md ITC, Lucida Grande",
	            "Qlassik Medium, Qlassik Bold, Lucida Grande",
	            "TradeGothic LT, Lucida Sans, Lucida Grande",
	            "Cocon, Cocon-Light, sans-serif",
	            "Frutiger, Frutiger LT Std 55 Roman, tahoma",
	            "Futura LT Book, Century Gothic, sans-serif",
	            "Steinem, Cocon, Cambria",
	            "Delicious, Trebuchet MS, sans-serif",
	            "Helvetica 65 Medium, Helvetica Neue, Helvetica Bold, sans-serif",
	            "Helvetica Neue, Helvetica, Helvetica-Normal, sans-serif",
	            "Helvetica Rounded, Arial Rounded Bold, VAGRounded BT, sans-serif",
	            "Decker, sans-serif",
	            "Mrs Eaves OT, Georgia, Cambria, serif",
	            "Anivers, Lucida Sans, Lucida Grande",
	            "Geneva, sans-serif",
	            "Trajan, Trajan Pro, serif",
	            "FagoCo, Calibri, Lucida Grande",
	            "Meta, Meta Bold , Meta Medium, sans-serif",
	            "Chocolate, Segoe UI, Seips",
	            "Ronda, Ronda Light, Century Gothic",
	            "DIN, DINPro-Regular, DINPro-Medium, sans-serif",
	            "Gotham, Georgia, serif"
	            )
),		

array(
	"name" => __("Choose your headline font", "bp_scholar"),
	"box" => "1",
	"id" => $shortname . $shortprefix . "headline_font",
	"type" => "select",
	"inblock" => "headers",
	"std" => "Tahoma, Helvetica, Sans-serif",
				"options" => array(
	            "Lucida Grande, Lucida Sans, sans-serif",
	            "Arial, sans-serif",
	            "Verdana, sans-serif",
	            "Trebuchet MS, sans-serif",
	            "Fertigo, serif",
	            "Georgia, serif",
	            "Cambria, Georgia, serif",
	            "Tahoma, sans-serif",
	            "Helvetica, Arial, sans-serif",
	            "Corpid, Corpid Bold, sans-serif",
	            "Century Gothic, Century, sans-serif",
	            "Palatino Linotype, Times New Roman, serif",
	            "Garamond, Georgia, serif",
	            "Caslon Book BE, Caslon, Arial Narrow",
	            "Arial Rounded Bold, Arial",
	            "Arial Narrow, Arial",
	            "Myriad Pro, Calibri, sans-serif",
	            "Candara, Calibri, Lucida Grande",
	            "Univers LT 55, Univers LT Std 55, Univers, sans-serif",
	            "Ronda, Ronda Light, Century Gothic",
	            "Century, Times New Roman, serif",
	            "Courier New, Courier, monospace",
	            "Walbaum LT Roman, Walbaum, Times New Roman",
	            "Dax, Dax-Regular, Dax-Bold, Trebuchet MS",
	            "VAG Round, Arial Rounded Bold, sans-serif",
	            "Humana Sans ITC, Humana Sans Md ITC, Lucida Grande",
	            "Qlassik Medium, Qlassik Bold, Lucida Grande",
	            "TradeGothic LT, Lucida Sans, Lucida Grande",
	            "Cocon, Cocon-Light, sans-serif",
	            "Frutiger, Frutiger LT Std 55 Roman, tahoma",
	            "Futura LT Book, Century Gothic, sans-serif",
	            "Steinem, Cocon, Cambria",
	            "Delicious, Trebuchet MS, sans-serif",
	            "Helvetica 65 Medium, Helvetica Neue, Helvetica Bold, sans-serif",
	            "Helvetica Neue, Helvetica, Helvetica-Normal, sans-serif",
	            "Helvetica Rounded, Arial Rounded Bold, VAGRounded BT, sans-serif",
	            "Decker, sans-serif",
	            "Mrs Eaves OT, Georgia, Cambria, serif",
	            "Anivers, Lucida Sans, Lucida Grande",
	            "Geneva, sans-serif",
	            "Trajan, Trajan Pro, serif",
	            "FagoCo, Calibri, Lucida Grande",
	            "Meta, Meta Bold , Meta Medium, sans-serif",
	            "Chocolate, Segoe UI, Seips",
	            "Ronda, Ronda Light, Century Gothic",
	            "DIN, DINPro-Regular, DINPro-Medium, sans-serif",
	            "Gotham, Georgia, serif"
	            )
),		

array(
	"name" => __("Choose your h1 colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "h1_colour",
	"box" => "1",
	"inblock" => "headers",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your h1 header colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "h1_header_colour",
	"box" => "1",
	"inblock" => "headers",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your h2 colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "h2_colour",
	"box" => "1",
	"inblock" => "headers",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your h3 colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "h3_colour",
	"box" => "1",
	"inblock" => "headers",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your h3 title colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "h3_title_colour",
	"box" => "1",
	"inblock" => "headers",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your h3 title background colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "h3_title_background_colour",
	"box" => "1",
	"inblock" => "headers",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your h3 title bottom border colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "h3_title_border_colour",
	"box" => "1",
	"inblock" => "headers",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your h4 colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "h4_colour",
	"box" => "1",
	"inblock" => "headers",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your h4 title colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "h4_title_colour",
	"box" => "1",
	"inblock" => "headers",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your h4 title background", "bp_scholar"),
	"id" => $shortname . $shortprefix . "h4_title_background_colour",
	"box" => "1",
	"inblock" => "headers",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Insert your <strong>h4 title header background image</strong> full url here<br /><em>*you can upload your image in <a href='media-new.php'>media panel</a> and paste the url here.</em>", "bp_scholar"),
	"id" => $shortname . $shortprefix . "h4_title_background_image",
	"box" => "1",
	"inblock" => "headers",
	"std" => "",
	"type" => "text"),

array(
	"name" => __("H4 title background image repeat", "bp_scholar"),
	"id" => $shortname . $shortprefix . "h4_title_image_repeat",	
	"box" => "1",
	"inblock" => "headers",
	"type" => "select",
	"std" => "repeat-x",
	"options" => array("no-repeat", "repeat", "repeat-x", "repeat-y")),

array(
	"name" => __("Choose your text colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "text_colour",
	"box" => "1",
	"inblock" => "headers",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your blockquote colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "blockquote_colour",
	"box" => "1",
	"inblock" => "headers",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your blockquote border colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "blockquote_border_colour",
	"box" => "1",
	"inblock" => "headers",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your list text colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "list_colour",
	"box" => "1",
	"inblock" => "headers",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your link colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "link_colour",
	"box" => "2",
	"inblock" => "links",
	"std" => "",
	"type" => "colorpicker"),
	
array(
	"name" => __("Choose your link hover colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "link_hover_colour",
	"box" => "2",
	"inblock" => "links",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your link visited colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "link_visited_colour",
	"box" => "2",
	"inblock" => "links",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your navigation text link colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "navigation_link_colour",
	"box" => "3",
	"inblock" => "navlinks",
	"std" => "",
	"type" => "colorpicker"),
	
array(
	"name" => __("Choose your navigation link current and hover colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "navigation_hover_colour",
	"box" => "3",
	"inblock" => "navlinks",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your navigation background current and hover colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "navigation_background_hover_colour",
	"box" => "3",
	"inblock" => "navlinks",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your navigation drop down background colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "navigation_background_drop_colour",
	"box" => "3",
	"inblock" => "navlinks",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your user navigation link colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "user_link_colour",
	"box" => "3",
	"inblock" => "navlinks",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your user navigation link current and hover colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "user_link_hover_colour",
	"box" => "3",
	"inblock" => "navlinks",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Insert your <strong>header background image</strong> full url here<br /><em>*you can upload your image in <a href='media-new.php'>media panel</a> and paste the url here.</em>", "bp_scholar"),
	"id" => $shortname . $shortprefix . "header_background_image",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "text"),

array(
	"name" => __("Header background images repeat", "bp_scholar"),
	"id" => $shortname . $shortprefix . "header_image_repeat",	
	"box" => "4",
	"inblock" => "layout",
	"type" => "select",
	"std" => "repeat-x",
	"options" => array("no-repeat", "repeat", "repeat-x", "repeat-y")),

array(
	"name" => __("Choose your header background colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "header_background_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your header bottom/top border colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "header_border_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),
	
array(
	"name" => __("Insert your <strong>navigation background image</strong> full url here<br /><em>*you can upload your image in <a href='media-new.php'>media panel</a> and paste the url here.</em>", "bp_scholar"),
	"id" => $shortname . $shortprefix . "navigation_background_image",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "text"),

array(
	"name" => __("Navigation background images repeat", "bp_scholar"),
	"id" => $shortname . $shortprefix . "navigation_image_repeat",	
	"box" => "4",
	"inblock" => "layout",
	"type" => "select",
	"std" => "repeat-x",
	"options" => array("no-repeat", "repeat", "repeat-x", "repeat-y")),

array(
	"name" => __("Choose your navigation background colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "navigation_background_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your navigation top/bottom border colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "navigation_border_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your content section background colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "content_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your content section top/bottom border colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "content_border_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your footer background colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "footer_background_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your footer top border colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "footer_border_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your footer links (copyright etc) border colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "footer_links_border_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your information box background colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "information_background_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your information box border colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "information_border_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your boxed areas background colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "box_background_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your boxed areas border colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "box_border_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your login / welcome background colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "login_background_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your login / welcome border colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "login_border_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your login / welcome text colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "login_text_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your widget text colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "widget_text_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your widget background colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "widget_background_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your widget border colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "widget_border_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your widget list text colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "widget_list_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your slideshow background colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "slideshow_background_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your slideshow text colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "slideshow_text_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your slideshow text background colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "slideshow_text_background_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your alt background colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "alt_background_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your alt border colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "alt_border_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your alt border colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "alt_border_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your table header background colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "table_colour",
	"box" => "5",
	"inblock" => "form",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your table border colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "table_border_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your table sticky colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "table_sticky_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your table sticky border colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "table_sticky_border_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your table unread colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "table_unread_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your table unread border colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "table_unread_border_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your unread count colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "unread_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your unread text colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "unread_text_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your messages box border colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "messages_border_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your button text colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "button_text",
	"box" => "5",
	"inblock" => "form",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your button colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "button_colour",
	"box" => "5",
	"inblock" => "form",
	"std" => "",
	"type" => "colorpicker"),
	
array(
	"name" => __("Choose your button border colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "button_border",
	"box" => "5",
	"inblock" => "form",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your button hover text colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "button_hover_text",
	"box" => "5",
	"inblock" => "form",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your button hover background colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "button_hover_background",
	"box" => "5",
	"inblock" => "form",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your submit button background colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "submit_background_colour",
	"box" => "5",
	"inblock" => "form",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your submit button border colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "submit_border_colour",
	"box" => "5",
	"inblock" => "form",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your submit button text colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "submit_text_colour",
	"box" => "5",
	"inblock" => "form",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your form input background colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "form_background_colour",
	"box" => "5",
	"inblock" => "form",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your form input border colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "form_border_colour",
	"box" => "5",
	"inblock" => "form",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your form input text colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "form_text",
	"box" => "5",
	"inblock" => "form",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your label text colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "label_text_colour",
	"box" => "5",
	"inblock" => "form",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your comments list border colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "comment_list_border_colour",
	"box" => "5",
	"inblock" => "form",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your comments meta text colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "comment_meta_colour",
	"box" => "5",
	"inblock" => "form",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your comments meta text colour", "bp_scholar"),
	"id" => $shortname . $shortprefix . "comment_meta_colour",
	"box" => "5",
	"inblock" => "form",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your comments odd background", "bp_scholar"),
	"id" => $shortname . $shortprefix . "comment_odd_colour",
	"box" => "5",
	"inblock" => "form",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your comments odd border", "bp_scholar"),
	"id" => $shortname . $shortprefix . "comment_odd_border_colour",
	"box" => "5",
	"inblock" => "form",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your comments even background", "bp_scholar"),
	"id" => $shortname . $shortprefix . "comment_even_colour",
	"box" => "5",
	"inblock" => "form",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your comments even border", "bp_scholar"),
	"id" => $shortname . $shortprefix . "comment_even_border_colour",
	"box" => "5",
	"inblock" => "form",
	"std" => "",
	"type" => "colorpicker"),

);

function buddyscholar_custom_style_admin_panel() {
	echo "<div id=\"admin-options\">";
	
	global $themename, $shortname, $options3;
	
	if ( isset($_REQUEST['saved3'] )) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings saved.</strong></p></div>';
	if ( isset($_REQUEST['reset3'] )) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings reset.</strong></p></div>';
?>

<h4><?php echo "$themename"; ?> <?php _e('Custom styling', 'bp-scholar'); ?></h4>

<h2><?php _e('If you want to customise the theme options you MUST have default.css selected'); ?></h2>
<form action="" method="post">


<?php if( $value['box'] = '1' ) {  ?>
	
<div class="get-option">
<h2><?php _e('Text styling', 'bp-scholar') ?></h2>

<?php foreach ($options3 as $value) { ?>

	<!-- if text box -->
	<?php if (($value['inblock'] == "headers") && ($value['type'] == "text")) { ?>
	<div class="option-save">
	<div class="description"><?php echo $value['name']; ?></div>
	<div class="option-box">
		<p><input name="<?php echo $value['id']; ?>" class="myfield" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" /></p>
	</div>
	</div>

	<!-- if text area -->
	<?php } elseif (($value['inblock'] == "headers") && ($value['type'] == "textarea")) { ?>

	<?php
	$valuex = $value['id'];
	$valuey = stripslashes($valuex);
	$value_code = get_option($valuey);
	?>
	<div class="option-save">
	<div class="description"><?php echo $value['name']; ?></div>
	<div class="option-box"><p><textarea name="<?php echo $valuey; ?>" class="mytext" cols="40%" rows="8" /><?php if ( get_option($valuey) != "") { echo stripslashes($value_code); } else { echo $value['std']; } ?></textarea></p>
	</div>
	</div>

	<!-- if colorpicker -->
	<?php } elseif (($value['inblock'] == "headers") && ($value['type'] == "colorpicker") ) {?>
	<div class="option-save">
	<div class="description"><?php echo $value['name']; ?></div>
		<?php $i = ""; $i == $i++ ; ?>
	<div class="option-box">
		<p><input class="color {required:false,hash:true}" name="<?php echo $value['id']; ?>" id="colorpickerField<?php echo $i; ?>" type="text" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" />
	</p>
	</div>
	</div>

	<!-- if select -->
	<?php } elseif (($value['inblock'] == "headers") && ($value['type'] == "select") ) {  ?>
	<div class="option-save">
	<div class="description"><?php echo $value['name']; ?></div>
	<div class="option-box">
		<p><select name="<?php echo $value['id']; ?>" class="myselect" id="<?php echo $value['id']; ?>">
		<?php foreach ($value['options'] as $option) { ?>
		<option<?php if ( get_option( $value['id'] ) == $option) { echo ' selected="selected"'; } elseif ($option == $value['std']) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option>
	<?php } ?>
	</select>
	</p>
	</div>
	</div>
	<?php } ?>

	<?php } ?>

	</div>

<?php } ?>
	
<?php if( $value['box'] = '2' ) {  ?>
	
<div class="get-option">
<h2><?php _e('Link styling', 'bp-scholar') ?></h2>

<?php foreach ($options3 as $value) { ?>

	<!-- if text box -->
	<?php if (($value['inblock'] == "links") && ($value['type'] == "text")) { ?>
	<div class="option-save">
	<div class="description"><?php echo $value['name']; ?></div>
	<div class="option-box">
		<p><input name="<?php echo $value['id']; ?>" class="myfield" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" /></p>
	</div>
	</div>

	<!-- if text area -->
	<?php } elseif (($value['inblock'] == "links") && ($value['type'] == "textarea")) { ?>

	<?php
	$valuex = $value['id'];
	$valuey = stripslashes($valuex);
	$value_code = get_option($valuey);
	?>
	<div class="option-save">
	<div class="description"><?php echo $value['name']; ?></div>
	<div class="option-box"><p><textarea name="<?php echo $valuey; ?>" class="mytext" cols="40%" rows="8" /><?php if ( get_option($valuey) != "") { echo stripslashes($value_code); } else { echo $value['std']; } ?></textarea></p>
	</div>
	</div>

	<!-- if colorpicker -->
	<?php } elseif (($value['inblock'] == "links") && ($value['type'] == "colorpicker") ) {?>
	<div class="option-save">
	<div class="description"><?php echo $value['name']; ?></div>
		<?php $i = ""; $i == $i++ ; ?>
	<div class="option-box">
		<p><input class="color {required:false,hash:true}" name="<?php echo $value['id']; ?>" id="colorpickerField<?php echo $i; ?>" type="text" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" />
	</p>
	</div>
	</div>

	<!-- if select -->
	<?php } elseif (($value['inblock'] == "links") && ($value['type'] == "select") ) {  ?>
	<div class="option-save">
	<div class="description"><?php echo $value['name']; ?></div>
	<div class="option-box">
		<p><select name="<?php echo $value['id']; ?>" class="myselect" id="<?php echo $value['id']; ?>">
		<?php foreach ($value['options'] as $option) { ?>
		<option<?php if ( get_option( $value['id'] ) == $option) { echo ' selected="selected"'; } elseif ($option == $value['std']) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option>
	<?php } ?>
	</select>
	</p>
	</div>
	</div>
	<?php } ?>

	<?php } ?>

	</div>

<?php } ?>

<?php if( $value['box'] = '3' ) {  ?>
	
<div class="get-option">
<h2><?php _e('Navigation styling', 'bp-scholar') ?></h2>

<?php foreach ($options3 as $value) { ?>

	<!-- if text box -->
	<?php if (($value['inblock'] == "navlinks") && ($value['type'] == "text")) { ?>
	<div class="option-save">
	<div class="description"><?php echo $value['name']; ?></div>
	<div class="option-box">
		<p><input name="<?php echo $value['id']; ?>" class="myfield" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" /></p>
	</div>
	</div>

	<!-- if text area -->
	<?php } elseif (($value['inblock'] == "navlinks") && ($value['type'] == "textarea")) { ?>

	<?php
	$valuex = $value['id'];
	$valuey = stripslashes($valuex);
	$value_code = get_option($valuey);
	?>
	<div class="option-save">
	<div class="description"><?php echo $value['name']; ?></div>
	<div class="option-box"><p><textarea name="<?php echo $valuey; ?>" class="mytext" cols="40%" rows="8" /><?php if ( get_option($valuey) != "") { echo stripslashes($value_code); } else { echo $value['std']; } ?></textarea></p>
	</div>
	</div>

	<!-- if colorpicker -->
	<?php } elseif (($value['inblock'] == "navlinks") && ($value['type'] == "colorpicker") ) {?>
	<div class="option-save">
	<div class="description"><?php echo $value['name']; ?></div>
		<?php $i = ""; $i == $i++ ; ?>
	<div class="option-box">
		<p><input class="color {required:false,hash:true}" name="<?php echo $value['id']; ?>" id="colorpickerField<?php echo $i; ?>" type="text" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" />
	</p>
	</div>
	</div>

	<!-- if select -->
	<?php } elseif (($value['inblock'] == "navlinks") && ($value['type'] == "select") ) {  ?>
	<div class="option-save">
	<div class="description"><?php echo $value['name']; ?></div>
	<div class="option-box">
		<p><select name="<?php echo $value['id']; ?>" class="myselect" id="<?php echo $value['id']; ?>">
		<?php foreach ($value['options'] as $option) { ?>
		<option<?php if ( get_option( $value['id'] ) == $option) { echo ' selected="selected"'; } elseif ($option == $value['std']) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option>
	<?php } ?>
	</select>
	</p>
	</div>
	</div>
	<?php } ?>

	<?php } ?>

	</div>

<?php } ?>

<?php if( $value['box'] = '4' ) {  ?>
	
<div class="get-option">
<h2><?php _e('Layout styling', 'bp-scholar') ?></h2>

<?php foreach ($options3 as $value) { ?>

	<!-- if text box -->
	<?php if (($value['inblock'] == "layout") && ($value['type'] == "text")) { ?>
	<div class="option-save">
	<div class="description"><?php echo $value['name']; ?></div>
	<div class="option-box">
		<p><input name="<?php echo $value['id']; ?>" class="myfield" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" /></p>
	</div>
	</div>

	<!-- if text area -->
	<?php } elseif (($value['inblock'] == "layout") && ($value['type'] == "textarea")) { ?>

	<?php
	$valuex = $value['id'];
	$valuey = stripslashes($valuex);
	$value_code = get_option($valuey);
	?>
	<div class="option-save">
	<div class="description"><?php echo $value['name']; ?></div>
	<div class="option-box"><p><textarea name="<?php echo $valuey; ?>" class="mytext" cols="40%" rows="8" /><?php if ( get_option($valuey) != "") { echo stripslashes($value_code); } else { echo $value['std']; } ?></textarea></p>
	</div>
	</div>

	<!-- if colorpicker -->
	<?php } elseif (($value['inblock'] == "layout") && ($value['type'] == "colorpicker") ) {?>
	<div class="option-save">
	<div class="description"><?php echo $value['name']; ?></div>
		<?php $i = ""; $i == $i++ ; ?>
	<div class="option-box">
		<p><input class="color {required:false,hash:true}" name="<?php echo $value['id']; ?>" id="colorpickerField<?php echo $i; ?>" type="text" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" />
	</p>
	</div>
	</div>

	<!-- if select -->
	<?php } elseif (($value['inblock'] == "layout") && ($value['type'] == "select") ) {  ?>
	<div class="option-save">
	<div class="description"><?php echo $value['name']; ?></div>
	<div class="option-box">
		<p><select name="<?php echo $value['id']; ?>" class="myselect" id="<?php echo $value['id']; ?>">
		<?php foreach ($value['options'] as $option) { ?>
		<option<?php if ( get_option( $value['id'] ) == $option) { echo ' selected="selected"'; } elseif ($option == $value['std']) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option>
	<?php } ?>
	</select>
	</p>
	</div>
	</div>
	<?php } ?>

	<?php } ?>

	</div>

<?php } ?>

<?php if( $value['box'] = '5' ) {  ?>
	
<div class="get-option">
<h2><?php _e('Form styling', 'bp-scholar') ?></h2>

<?php foreach ($options3 as $value) { ?>

	<!-- if text box -->
	<?php if (($value['inblock'] == "form") && ($value['type'] == "text")) { ?>
	<div class="option-save">
	<div class="description"><?php echo $value['name']; ?></div>
	<div class="option-box">
		<p><input name="<?php echo $value['id']; ?>" class="myfield" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" /></p>
	</div>
	</div>

	<!-- if text area -->
	<?php } elseif (($value['inblock'] == "form") && ($value['type'] == "textarea")) { ?>

	<?php
	$valuex = $value['id'];
	$valuey = stripslashes($valuex);
	$value_code = get_option($valuey);
	?>
	<div class="option-save">
	<div class="description"><?php echo $value['name']; ?></div>
	<div class="option-box"><p><textarea name="<?php echo $valuey; ?>" class="mytext" cols="40%" rows="8" /><?php if ( get_option($valuey) != "") { echo stripslashes($value_code); } else { echo $value['std']; } ?></textarea></p>
	</div>
	</div>

	<!-- if colorpicker -->
	<?php } elseif (($value['inblock'] == "form") && ($value['type'] == "colorpicker") ) {?>
	<div class="option-save">
	<div class="description"><?php echo $value['name']; ?></div>
		<?php $i = ""; $i == $i++ ; ?>
	<div class="option-box">
		<p><input class="color {required:false,hash:true}" name="<?php echo $value['id']; ?>" id="colorpickerField<?php echo $i; ?>" type="text" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" />
	</p>
	</div>
	</div>

	<!-- if select -->
	<?php } elseif (($value['inblock'] == "form") && ($value['type'] == "select") ) {  ?>
	<div class="option-save">
	<div class="description"><?php echo $value['name']; ?></div>
	<div class="option-box">
		<p><select name="<?php echo $value['id']; ?>" class="myselect" id="<?php echo $value['id']; ?>">
		<?php foreach ($value['options'] as $option) { ?>
		<option<?php if ( get_option( $value['id'] ) == $option) { echo ' selected="selected"'; } elseif ($option == $value['std']) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option>
	<?php } ?>
	</select>
	</p>
	</div>
	</div>
	<?php } ?>

	<?php } ?>

	</div>

<?php } ?>

	<p id="top-margin" class="save-p">
	<input name="save3" type="submit" class="sbutton" value="<?php echo esc_attr(__('Save Options', 'bp-scholar')); ?>" />
	<input type="hidden" name="theme_action3" value="save3" />
	</p>
	</form>

	<form method="post">
	<p class="save-p">
	<input name="reset3" type="submit" class="sbutton" value="<?php echo esc_attr(__('Reset Options', 'bp-scholar')); ?>" />
	<input type="hidden" name="theme_action3" value="reset3" />
	</p>

	</form>
	</div>

	<?php
}
function buddyscholar_custom_style_admin_register() {
		global $themename, $shortname, $options3;
				$action3 = isset($_REQUEST['theme_action3']);
		if ( isset($_GET['page']) == 'styling-functions.php' ) {
			if ( 'save3' == $action3 ) {
				foreach ($options3 as $value) {	
					update_option( $value['id'], isset($_REQUEST[ $value['id'] ] )); }
				foreach ($options3 as $value) {
					if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); 
					} 
					else { delete_option( $value['id'] ); } 
				}
				header("Location: themes.php?page=styling-functions.php&saved3=true");
				die;
				} 
				else if( 'reset3' == $action3 ) {
					foreach ($options3 as $value) {
						delete_option( $value['id'] ); 
					}
				header("Location: themes.php?page=styling-functions.php&reset3=true");
				die;
				}
			}
			add_theme_page(_g ($themename . __('Custom styling', 'bp-scholar')),  _g (__('Custom Styling', 'bp-scholar')),  'edit_theme_options', 'styling-functions.php', 'buddyscholar_custom_style_admin_panel');
	}


add_action('admin_head', 'buddyscholar_admin_head');
add_action('admin_menu', 'buddyscholar_admin_register');
add_action('admin_menu', 'buddyscholar_ready_style_admin_register');
add_action('admin_menu', 'buddyscholar_custom_style_admin_register');

?>