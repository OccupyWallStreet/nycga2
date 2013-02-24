<?php
function _g($str)
{
return __($str, 'option-page');
}

$themename = "Network Theme";
$themeversion = "1";
$shortname = "dev";
$shortprefix = "_network_";
/* get pages so can set them */
$dev_pages_obj = get_pages();
$dev_pages = array();
foreach ($dev_pages_obj as $dev_cat) {
	$dev_pages[$dev_cat->ID] = $dev_cat->post_name;
}
$pages_tmp = array_unshift($dev_pages, "Select a page:");
/* end of get pages */
/* get categories so can set them */
$dev_categories_obj = get_categories('hide_empty=0');
$dev_categories = array();
foreach ($dev_categories_obj as $dev_cat) {
	$dev_categories[$dev_cat->cat_ID] = $dev_cat->category_nicename;
}
$categories_tmp = array_unshift($dev_categories, "Select a category:");
/* end of get categories */

/* start of theme options */

if (is_multisite()) {

	$list_of_blogs = get_list_of_blogs();
	
	if (sizeof($list_of_blogs) == 0) {

		$list_of_blogs = array();
	
	}

} else {

	$list_of_blogs = array();

}

$options = array (

	array("name" => __("Show blog posts or show network posts?", 'network'),
		"description" => __("If no network posts exist, the default will be to show blog posts", 'network'),
		"id" => $shortname . $shortprefix . "homepage_show_type_posts",	     	
		"inblock" => "home",
		"type" => "select",
		"std" => "Select",
		"options" => array("Network Posts", "Blog Posts")),
		
	array("name" => __("Exclude these blogs from the featured display:", 'network'),
		"description" => __("You can ignore this if you are not showing network posts. If you are excluding all network blogs, switch to blog post instead.", 'network'),
		"id" => $shortname . $shortprefix . "homepage_ignore_blogs",	     	
		"inblock" => "home",
		"type" => "select-multiple",
		"std" => "Select",
		"options" => $list_of_blogs),

	array(
		"name" => __("What Page number would you like to start from?", 'network'),
		"description" => __("For example, a 2 will have the featured area start on page or section two.", 'network'),
		"id" => $shortname . $shortprefix . "homepage_featured_start_page",
		"inblock" => "home",
		"type" => "text",
		"std" => "",
	),
	array(
		"name" => __("Number Of Blog Posts Per Page", 'network'),
		"id" => $shortname . $shortprefix . "homepage_items_per_page",
		"inblock" => "home",
		"type" => "text",
		"std" => "",
	),	
	array(
		"name" => __("How Many TOTAL Blog Posts?", 'network'),
		"id" => $shortname . $shortprefix . "homepage_how_many",
		"inblock" => "home",
		"type" => "text",
		"std" => "",
	),	
	array(
		"name" => __("Label Of First Button", 'network'),
		"description" => __("By default it's first", 'network'),
		"id" => $shortname . $shortprefix . "homepage_label_first",
		"inblock" => "home",
		"type" => "text",
		"std" => "",
	),	
	array(
		"name" => __("Label Of Last Button", 'network'),
		"description" => __("By default it's last", 'network'),
		"id" => $shortname . $shortprefix . "homepage_label_last",
		"inblock" => "home",
		"type" => "text",
		"std" => "",
	),
	array(
		"name" => __("Label Of Prev Button", 'network'),
		"description" => __("By default it's prev", 'network'),
		"id" => $shortname . $shortprefix . "homepage_label_prev",
		"inblock" => "home",
		"type" => "text",
		"std" => "",
	),
	array(
		"name" => __("Label Of Next Button", 'network'),
		"description" => __("By default it's next", 'network'),
		"id" => $shortname . $shortprefix . "homepage_label_next",
		"inblock" => "home",
		"type" => "text",
		"std" => "",
	),
	array("name" => __("Show Thumbnails In Blog Posts?", 'network'),
		"id" => $shortname . $shortprefix . "homepage_show_thumbnails",	     	
		"inblock" => "home",
		"type" => "select",
		"std" => "Select",
		"options" => array("yes", "no")),
		
	array("name" => __("Default Thumbnail Image URL", 'network'),
		"description" => __("This will be shown if a thumbnail can't be pulled from a blog and should be exact size (227px wide and 108 px in height)", 'network'),
		"id" => $shortname . $shortprefix . "homepage_default_thumbnail_url",	     	
		"inblock" => "home",
		"type" => "select",
		"type" => "text",
		"std" => "",	
	),	
	
	array("name" => __("Display social icons on BuddyPress profile pages?", 'network'),
		"id" => $shortname . $shortprefix . "allow_social_icons",	     	
		"inblock" => "buddypress",
		"type" => "select",
		"std" => "Select",
		"options" => array("yes", "no")),
		
	array("name" => __("Display badge boxes on BuddyPress profile pages?", 'network'),
		"description" => __("These boxes display the number of blogs, friends, and groups a user owns or is a member of.", 'network'),
		"id" => $shortname . $shortprefix . "allow_badge_boxes",	     	
		"inblock" => "buddypress",
		"type" => "select",
		"std" => "Select",
		"options" => array("yes", "no")),
		
	array("name" => __("Display latest blog post on BuddyPress profile pages?", 'network'),
		"id" => $shortname . $shortprefix . "allow_latest_blog_post",	     	
		"inblock" => "buddypress",
		"type" => "select",
		"std" => "Select",
		"options" => array("yes", "no")),		
		
	array(
		"name" => __("Site name", 'network'),
		"id" => $shortname . $shortprefix . "site_title",
		"inblock" => "branding",
		"type" => "text",
		"std" => "",
	),
	array(
		"name" => __("Site Slogan", 'network'),
		"id" => $shortname . $shortprefix . "site_slogan",
		"inblock" => "branding",
		"type" => "text",
		"std" => "",
	),
	array("name" => __("Do you to use a custom large image logo rather than domain name text?", 'network'),
		"description" => __("Enter your url in the next section if saying yes", 'network'),
		"id" => $shortname . $shortprefix . "header_image",	     	
		"inblock" => "branding",
		"type" => "select",
		"std" => "Select",
		"options" => array("yes", "no")),

	array(
		"name" => __("Insert your logo full url here", 'network'),
		"description" => __("You can upload your logo in <a href='media-new.php'>media panel</a> and copy paste the url here", 'network'),
		"id" => $shortname . $shortprefix . "header_logo",
		"inblock" => "branding",
		"type" => "text",
		"std" => "",
	),

	array("name" => __("Do you to use a custom square image logo and your domain name text?", 'network'),
		"description" => __("Enter your url in the next section if saying yes", 'network'),
		"id" => $shortname . $shortprefix . "header_image_square",	     	
		"inblock" => "branding",
		"type" => "select",
		"std" => "Select",
		"options" => array("yes", "no")),

	array(
		"name" => __("Insert your square logo full url here", 'network'),
		"description" => __("You can upload your logo in <a href='media-new.php'>media panel</a> and copy paste the url here", 'network'),
		"id" => $shortname . $shortprefix . "header_logo_square",
		"inblock" => "branding",
		"type" => "text",
		"std" => "",
	),

array(
	"name" => __("Sign up feature text", 'network'),
	"id" => $shortname . $shortprefix . "signupfeat_text",
	"inblock" => "branding",
	"type" => "textarea",
	"std" => "",
),

array(
	"name" => __("Sign up button text", 'network'),
	"id" => $shortname . $shortprefix . "signupfeat_buttontext",
	"inblock" => "branding",
	"type" => "text",
	"std" => "",
),


array(
	"name" => __("Sign up custom link (enter a custom link if don't want default ones)", 'network'),
	"id" => $shortname . $shortprefix . "signupfeat_buttontextcustom",
	"inblock" => "branding",
	"type" => "text",
	"std" => "",
),
);

function network_admin_panel() {
	global $themename, $shortname, $options, $options2, $options3, $bp_existed, $multi_site_on;
	if ( isset($_REQUEST['saved1']) && $_REQUEST['saved1'] ) echo '<div id="message" class="updated fade"><p><strong>'. $themename . __(' settings saved.', 'network') . '</strong></p></div>';
	if ( isset($_REQUEST['reset1']) && $_REQUEST['reset1'] ) echo '<div id="message" class="updated fade"><p><strong>'. $themename . __(' settings reset.', 'network') . '</strong></p></div>';
	?>
	<div id="options-panel">
	<form action="" method="post">

	  <div id="sbtabs">
	  <div class="tabmc">
	  <ul class="ui-tabs-nav" id="tabm">
	  <li class="first ui-tabs-selected"><a href="#home"><?php _e("Front page settings",'network'); ?></a></li>
		  <?php if($bp_existed == 'true') { ?><li class=""><a href="#buddypress"><?php _e("BuddyPress",'network'); ?></a></li><?php } ?>
	  <li class=""><a href="#branding"><?php _e("Branding",'network'); ?></a></li>
	  </ul>
	  </div>

	<div class="tabc">


	<ul style="" class="ui-tabs-panel" id="home">
	<li>

	<h2><?php _e("Front page settings - here you can exclude blogs and many advanced settings.", 'network') ?></h2>


	<?php $value_var = 'home'; foreach ($options as $value) { ?>

	<?php if (($value['inblock'] == $value_var) && ($value['type'] == "text")) { // setting ?>

	<div class="tab-option">
			<div class="description"><?php if (isset($value['name'])) { echo $value['name']; } ?><br /><span><?php if (isset($value['description'])) { echo $value['description']; } ?></span></div>	<div class="input-option"><p><input name="<?php echo $value['id']; ?>" class="myfield" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo stripslashes(get_option( $value['id']) ); } else { echo stripslashes($value['std']); } ?>" /></p></div>
	</div>

	<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "textarea")) { // setting ?>


	<div class="tab-option">
	<?php
	$valuex = $value['id'];
	$valuey = stripslashes($valuex);
	$video_code = get_option($valuey);
	?>
			<div class="description"><?php if (isset($value['name'])) { echo $value['name']; } ?><br /><span><?php if (isset($value['description'])) { echo $value['description']; } ?></span></div>	<div class="input-option"><p><textarea name="<?php echo $valuey; ?>" class="mytext" cols="40%" rows="8" /><?php if ( get_option($valuey) != "") { echo stripslashes($video_code); } else { echo $value['std']; } ?>
	</textarea></p></div>
	</div>


	<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "colorpicker") ) { // setting ?>

	<?php $i == $i++ ; ?>

	<div class="tab-option">
			<div class="description"><?php if (isset($value['name'])) { echo $value['name']; } ?><br /><span><?php if (isset($value['description'])) { echo $value['description']; } ?></span></div>	<div class="input-option"><p><input class="color {required:false,hash:true}" name="<?php echo $value['id']; ?>" id="colorpickerField<?php echo $i; ?>" type="text" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" /></p></div>
	</div>

	<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "select") ) { // setting ?>

	<div class="tab-option">
			<div class="description"><?php if (isset($value['name'])) { echo $value['name']; } ?><br /><span><?php if (isset($value['description'])) { echo $value['description']; } ?></span></div>	<div class="input-option"><p><select name="<?php echo $value['id']; ?>" class="myselect" id="<?php echo $value['id']; ?>">
	<?php foreach ($value['options'] as $option) { ?>
	<option<?php if ( get_option( $value['id'] ) == $option) { echo ' selected="selected"'; } elseif ($option == $value['std']) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option>
	<?php } ?>
	</select>
	</p>
	</div>
	</div>

	<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "select-blogs") ) { // setting ?>

	<div class="tab-option">
			<div class="description"><?php if (isset($value['name'])) { echo $value['name']; } ?><br /><span><?php if (isset($value['description'])) { echo $value['description']; } ?></span></div>	<div class="input-option"><p><select name="<?php echo $value['id']; ?>" class="myselect" id="<?php echo $value['id']; ?>">
	
	<?php
	
//		print_r ($value['options']); exit;
	
	?>
	<?php foreach ($value['options'] as $option) { ?>
	<option<?php if ( get_option( $value['id'] ) == $option['blogid']) { echo ' selected="selected"'; } elseif ($thevalue == $value['std']) { echo ' selected="selected"'; } ?> value="<?php echo $option['blogid']; ?>"><?php echo $option['blogname']; ?></option>
	<?php } ?>
	</select>
	</p>
	</div>
	</div>

	<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "select-multiple") ) { // setting ?>

	
	<?php
	
		$selectArray = $value['options'];
		$tempValue = get_option( $value['id'] );

		if (!is_array($tempValue)) {
			$savedArray = unserialize ( get_option( $value['id'] ) );
		} else {
			$savedArray = $tempValue;
		}
		//print_r ($selectArray); exit;	
//		print_r ("---".$savedArray); exit;
	?>
	
	<div class="tab-option">
			<div class="description"><?php if (isset($value['name'])) { echo $value['name']; } ?><br /><span><?php if (isset($value['description'])) { echo $value['description']; } ?></span></div>	<div class="input-option"><p><select multiple="multiple" size="20" style="height: 100px;" name="<?php echo $value['id']; ?>[]" class="myselect" id="<?php echo $value['id']; ?>">

	<?php foreach ($selectArray as $element) { ?>

	<?php if (isset($element['blogid']) && $element['blogid'] != '') {?>

	<option<?php 
	
		if ( sizeof($savedArray) > 0 && $savedArray != '') {
		
			if (in_array($element['blogid'], $savedArray)) { 
			
				echo ' selected="selected"'; 
				
			} 
			
		}?> value="<?php echo $element['blogid']; ?>"><?php echo $element['blogname']; ?></option>

	<?php } ?>
	<?php } ?>
	</select>
	</p>
	</div>
	</div>

	<?php } ?>
	
	<?php } ?>
	</li></ul>

	


	<?php if($bp_existed == 'true') { ?>
	<ul style="" class="list7 ui-tabs-panel ui-tabs-hide" id="buddypress">

	<li>

	<h2><?php _e("BuddyPress Settings - change the BuddyPress specific theme enhancements", 'network') ?></h2>

	<?php $value_var = 'buddypress'; foreach ($options as $value) { ?>

	<?php if (($value['inblock'] == $value_var) && ($value['type'] == "text")) { // setting ?>

	<div class="tab-option">
			<div class="description"><?php if (isset($value['name'])) { echo $value['name']; } ?><br /><span><?php if (isset($value['description'])) { echo $value['description']; } ?></span></div>	<div class="input-option"><p><input name="<?php echo $value['id']; ?>" class="myfield" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo stripslashes(get_option( $value['id']) ); } else { echo stripslashes($value['std']); } ?>" /></p></div>
	</div>

	<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "textarea")) { // setting ?>


	<div class="tab-option">
	<?php
	$valuex = $value['id'];
	$valuey = stripslashes($valuex);
	$video_code = get_option($valuey);
	?>
			<div class="description"><?php if (isset($value['name'])) { echo $value['name']; } ?><br /><span><?php if (isset($value['description'])) { echo $value['description']; } ?></span></div>	<div class="input-option"><p><textarea name="<?php echo $valuey; ?>" class="mytext" cols="40%" rows="8" /><?php if ( get_option($valuey) != "") { echo stripslashes($video_code); } else { echo $value['std']; } ?>
	</textarea></p></div>
	</div>


	<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "colorpicker") ) { // setting ?>

	<?php $i == $i++ ; ?>

	<div class="tab-option">
			<div class="description"><?php if (isset($value['name'])) { echo $value['name']; } ?><br /><span><?php if (isset($value['description'])) { echo $value['description']; } ?></span></div>	<div class="input-option"><p><input class="color {required:false,hash:true}" name="<?php echo $value['id']; ?>" id="colorpickerField<?php echo $i; ?>" type="text" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" /></p></div>
	</div>

	<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "select") ) { // setting ?>

	<div class="tab-option">
			<div class="description"><?php if (isset($value['name'])) { echo $value['name']; } ?><br /><span><?php if (isset($value['description'])) { echo $value['description']; } ?></span></div>	<div class="input-option"><p><select name="<?php echo $value['id']; ?>" class="myselect" id="<?php echo $value['id']; ?>">
	<?php foreach ($value['options'] as $option) { ?>
	<option<?php if ( get_option( $value['id'] ) == $option) { echo ' selected="selected"'; } elseif ($option == $value['std']) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option>
	<?php } ?>
	</select>
	</p>
	</div>
	</div>

	<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "select-alt") ) { // setting ?>

	<div class="tab-option">
			<div class="description"><?php if (isset($value['name'])) { echo $value['name']; } ?><br /><span><?php if (isset($value['description'])) { echo $value['description']; } ?></span></div>	<div class="input-option"><p><select name="<?php echo $value['id']; ?>" class="myselect" id="<?php echo $value['id']; ?>">
	<?php foreach ($value['options'] as $thekey => $thevalue) { ?>
	<option<?php if ( get_option( $value['id'] ) == $thevalue) { echo ' selected="selected"'; } elseif ($thevalue == $value['std']) { echo ' selected="selected"'; } ?>><?php echo $key; ?></option>
	<?php } ?>
	</select>
	</p>
	</div>
	</div>

	<?php } ?>
	<?php } ?>
	</li></ul>
	<?php } ?>


	<ul style="" class="list9 ui-tabs-panel ui-tabs-hide" id="branding">

	<li>

	<h2><?php _e("Branding - Set a logo and make your site match your identity", 'network') ?></h2>

	<?php $value_var = 'branding'; foreach ($options as $value) { ?>

	<?php if (($value['inblock'] == $value_var) && ($value['type'] == "text")) { // setting ?>

	<div class="tab-option">
			<div class="description"><?php if (isset($value['name'])) { echo $value['name']; } ?><br /><span><?php if (isset($value['description'])) { echo $value['description']; } ?></span></div>	<div class="input-option"><p><input name="<?php echo $value['id']; ?>" class="myfield" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo stripslashes(get_option( $value['id']) ); } else { echo stripslashes($value['std']); } ?>" /></p></div>
	</div>

	<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "textarea")) { // setting ?>


	<div class="tab-option">
	<?php
	$valuex = $value['id'];
	$valuey = stripslashes($valuex);
	$video_code = get_option($valuey);
	?>
			<div class="description"><?php if (isset($value['name'])) { echo $value['name']; } ?><br /><span><?php if (isset($value['description'])) { echo $value['description']; } ?></span></div>	<div class="input-option"><p><textarea name="<?php echo $valuey; ?>" class="mytext" cols="40%" rows="8" /><?php if ( get_option($valuey) != "") { echo stripslashes($video_code); } else { echo $value['std']; } ?>
	</textarea></p></div>
	</div>


	<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "colorpicker") ) { // setting ?>

	<?php $i == $i++ ; ?>

	<div class="tab-option">
			<div class="description"><?php if (isset($value['name'])) { echo $value['name']; } ?><br /><span><?php if (isset($value['description'])) { echo $value['description']; } ?></span></div>	<div class="input-option"><p><input class="color {required:false,hash:true}" name="<?php echo $value['id']; ?>" id="colorpickerField<?php echo $i; ?>" type="text" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" /></p></div>
	</div>

	<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "select") ) { // setting ?>

	<div class="tab-option">
			<div class="description"><?php if (isset($value['name'])) { echo $value['name']; } ?><br /><span><?php if (isset($value['description'])) { echo $value['description']; } ?></span></div>	<div class="input-option"><p><select name="<?php echo $value['id']; ?>" class="myselect" id="<?php echo $value['id']; ?>">
	<?php foreach ($value['options'] as $option) { ?>
	<option<?php if ( get_option( $value['id'] ) == $option) { echo ' selected="selected"'; } elseif ($option == $value['std']) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option>
	<?php } ?>
	</select>
	</p>
	</div>
	</div>

	<?php } ?>
	<?php } ?>
	</li></ul>
	</div>
	</div>



	<div id="submitsection">
		
		<div class="submit">
		<h2><?php _e("Click this to save your theme options", 'network') ?></h2>
	<input name="save1" type="submit" class="sbutton" value="<?php echo esc_attr(__('Save All Options','network')); ?>" />
	<input type="hidden" name="theme_action1" value="save1" />
	</div>
	</div>
	</div>
	</form>



	<form method="post">
	<div id="resetsection">
	<div class="submit">
		<h2><?php _e("Clicking this will reset all theme options - use with caution", 'network') ?></h2>
	<input name="reset1" type="submit" class="sbutton" value="<?php echo esc_attr(__('Reset All Options','network')); ?>" />
	<input type="hidden" name="theme_action1" value="reset1" />
	</div>
	</div>
	</form>


	</div>

	<?php
	}
	
$options3 = array (

array(
	"name" => __("Choose your body font", 'network'),
	"id" => $shortname . $shortprefix . "body_font",
	"type" => "select",
	"inblock" => "fonts",
	"std" => "Arial, sans-serif",
				"options" => array(
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
									"Yanone Kaffeesatz, arial, serif"
	            )
),

array(
	"name" => __("Choose your header font", 'network'),
	"description" => __("We include google font directory fonts you can <a href='http://code.google.com/webfonts'>view here</a> ", 'network'),
	"id" => $shortname . $shortprefix . "header_font",
	"type" => "select",
	"inblock" => "fonts",
	"std" => "Arial, sans-serif",
				"options" => array(
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
											"Yanone Kaffeesatz, arial, serif"
	            )
),

array(
	"name" => __("Choose your feature box background colour", 'network'),
	"id" => $shortname . $shortprefix . "feature_box_colour",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your feature box hover background colour", 'network'),
	"id" => $shortname . $shortprefix . "feature_box_hover_colour",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your header background colour", 'network'),
	"id" => $shortname . $shortprefix . "header_background_colour",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your content box background colour", 'network'),
	"id" => $shortname . $shortprefix . "content_background_colour",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your body font colour", 'network'),
	"id" => $shortname . $shortprefix . "font_colour",
	"inblock" => "text",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your link colour", 'network'),
	"id" => $shortname . $shortprefix . "link_colour",
	"inblock" => "text",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your link hover colour", 'network'),
	"id" => $shortname . $shortprefix . "link_hover_colour",
	"inblock" => "text",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your link visited colour", 'network'),
	"id" => $shortname . $shortprefix . "link_visited_colour",
	"inblock" => "text",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your header colour", 'network'),
	"id" => $shortname . $shortprefix . "header_colour",
	"inblock" => "text",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your feature text title colour", 'network'),
	"id" => $shortname . $shortprefix . "feature_text_colour",
	"inblock" => "text",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your feature text blog title colour", 'network'),
	"id" => $shortname . $shortprefix . "feature_blog_title_colour",
	"inblock" => "text",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your site header text colour", 'network'),
	"id" => $shortname . $shortprefix . "site_header_colour",
	"inblock" => "text",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your navigation bar background colour", 'network'),
	"id" => $shortname . $shortprefix . "navigation_bar_background",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),
	
array(
	"name" => __("Choose your navigation text colour", 'network'),
	"id" => $shortname . $shortprefix . "nav_text_colour",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your navigation background colour", 'network'),
	"id" => $shortname . $shortprefix . "nav_background_colour",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your navigation text shadow colour", 'network'),
	"id" => $shortname . $shortprefix . "nav_shadow_colour",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your navigation hover text colour", 'network'),
	"id" => $shortname . $shortprefix . "nav_hover_text_colour",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your navigation hover background colour", 'network'),
	"id" => $shortname . $shortprefix . "nav_hover_background_colour",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

);


function network_custom_style_admin_panel() {

		global $themename, $options, $options2, $options3, $bp_existed, $multi_site_on;
		
		$i = 0;

		if ( isset($_REQUEST['saved3']) ) echo '<div id="message" class="updated fade"><p><strong>'. $themename . __(' settings saved.', 'network') . '</strong></p></div>';
		if ( isset($_REQUEST['reset3']) ) echo '<div id="message" class="updated fade"><p><strong>'. $themename . __(' settings reset.', 'network') . '</strong></p></div>';
		?>

		<div id="options-panel">
		<form action="" method="post">

		  <div id="sbtabs">
		  <div class="tabmc">
		  <ul class="ui-tabs-nav" id="tabm">
		  <li class="first ui-tabs-selected"><a href="#fonts"><?php _e("Fonts",'network'); ?></a></li>
		
		  <li class=""><a href="#layout"><?php _e("Layout Colours",'network'); ?></a></li>
		
		  <li class=""><a href="#text"><?php _e("Text Colours",'network'); ?></a></li>
		
		  <li class=""><a href="#navigation"><?php _e("Navigation Colours",'network'); ?></a></li>
		  </ul>
		</div>


		<div class="tabc">


		<ul style="" class="ui-tabs-panel" id="fonts">
		<li>
			<h2><?php _e("Fonts", 'network') ?></h2>

			<?php $value_var = 'fonts'; foreach ($options3 as $value) { ?>

					<?php if (($value['inblock'] == $value_var) && ($value['type'] == "text")) { // setting ?>

					<div class="tab-option">
					<div class="description"><?php echo $value['name']; ?><br /><span>	<?php 
							if (isset($value['description'])){
						echo $value['description']; }
						?></span></div>
					<div class="input-option"><p><input name="<?php echo $value['id']; ?>" class="myfield" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo stripslashes(get_option( $value['id']) ); } else { echo stripslashes($value['std']); } ?>" /></p></div>
					</div>

					<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "textarea")) { // setting ?>


					<div class="tab-option">
					<?php
					$valuex = $value['id'];
					$valuey = stripslashes($valuex);
					$video_code = get_option($valuey);
					?>
					<div class="description"><?php echo $value['name']; ?><br /><span><?php 
						if (isset($value['description'])){
					echo $value['description']; }
					?></span></div>
					<div class="input-option"><p><textarea name="<?php echo $valuey; ?>" class="mytext" cols="40%" rows="8" /><?php if ( get_option($valuey) != "") { echo stripslashes($video_code); } else { echo $value['std']; } ?>
					</textarea></p></div>
					</div>


					<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "colorpicker") ) { // setting ?>

					<?php $i = ""; $i == $i++ ; ?>

					<div class="tab-option">
					<div class="description"><?php echo $value['name']; ?><br /><span>	<?php 
							if (isset($value['description'])){
						echo $value['description']; }
						?></span></div>
					<div class="input-option"><p><input class="color {required:false,hash:true}" name="<?php echo $value['id']; ?>" id="colorpickerField<?php echo $i; ?>" type="text" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" /></p></div>
					</div>

					<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "select") ) { // setting ?>

					<div class="tab-option">
					<div class="description"><?php echo $value['name']; ?><br /><span>	<?php 
							if (isset($value['description'])){
						echo $value['description']; }
						?></span></div>
					<div class="input-option"><p><select name="<?php echo $value['id']; ?>" class="myselect" id="<?php echo $value['id']; ?>">
					<?php foreach ($value['options'] as $option) { ?>
					<option<?php if ( get_option( $value['id'] ) == $option) { echo ' selected="selected"'; } elseif ($option == $value['std']) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option>
					<?php } ?>
					</select>
					</p>
					</div>
					</div>

					<?php } ?>
			<?php } ?>
		</li>
		</ul>
			<ul style="" class="ui-tabs-panel" id="layout">
			<li>
				<h2><?php _e("Layout Colours", 'network') ?></h2>

				<?php $value_var = 'layout'; foreach ($options3 as $value) { ?>

						<?php if (($value['inblock'] == $value_var) && ($value['type'] == "text")) { // setting ?>

						<div class="tab-option">
						<div class="description"><?php echo $value['name']; ?><br /><span>	<?php 
								if (isset($value['description'])){
							echo $value['description']; }
							?></span></div>
						<div class="input-option"><p><input name="<?php echo $value['id']; ?>" class="myfield" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo stripslashes(get_option( $value['id']) ); } else { echo stripslashes($value['std']); } ?>" /></p></div>
						</div>

						<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "textarea")) { // setting ?>


						<div class="tab-option">
						<?php
						$valuex = $value['id'];
						$valuey = stripslashes($valuex);
						$video_code = get_option($valuey);
						?>
						<div class="description"><?php echo $value['name']; ?><br /><span><?php 
							if (isset($value['description'])){
						echo $value['description']; }
						?></span></div>
						<div class="input-option"><p><textarea name="<?php echo $valuey; ?>" class="mytext" cols="40%" rows="8" /><?php if ( get_option($valuey) != "") { echo stripslashes($video_code); } else { echo $value['std']; } ?>
						</textarea></p></div>
						</div>


						<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "colorpicker") ) { // setting ?>

						<?php $i = ""; $i == $i++ ; ?>

						<div class="tab-option">
						<div class="description"><?php echo $value['name']; ?><br /><span>	<?php 
								if (isset($value['description'])){
							echo $value['description']; }
							?></span></div>
						<div class="input-option"><p><input class="color {required:false,hash:true}" name="<?php echo $value['id']; ?>" id="colorpickerField<?php echo $i; ?>" type="text" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" /></p></div>
						</div>

						<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "select") ) { // setting ?>

						<div class="tab-option">
						<div class="description"><?php echo $value['name']; ?><br /><span>	<?php 
								if (isset($value['description'])){
							echo $value['description']; }
							?></span></div>
						<div class="input-option"><p><select name="<?php echo $value['id']; ?>" class="myselect" id="<?php echo $value['id']; ?>">
						<?php foreach ($value['options'] as $option) { ?>
						<option<?php if ( get_option( $value['id'] ) == $option) { echo ' selected="selected"'; } elseif ($option == $value['std']) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option>
						<?php } ?>
						</select>
						</p>
						</div>
						</div>

						<?php } ?>
				<?php } ?>
			</li>
			</ul>
				<ul style="" class="ui-tabs-panel" id="text">
				<li>
					<h2><?php _e("Text colours", 'network') ?></h2>

					<?php $value_var = 'text'; foreach ($options3 as $value) { ?>

							<?php if (($value['inblock'] == $value_var) && ($value['type'] == "text")) { // setting ?>

							<div class="tab-option">
							<div class="description"><?php echo $value['name']; ?><br /><span>	<?php 
									if (isset($value['description'])){
								echo $value['description']; }
								?></span></div>
							<div class="input-option"><p><input name="<?php echo $value['id']; ?>" class="myfield" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo stripslashes(get_option( $value['id']) ); } else { echo stripslashes($value['std']); } ?>" /></p></div>
							</div>

							<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "textarea")) { // setting ?>


							<div class="tab-option">
							<?php
							$valuex = $value['id'];
							$valuey = stripslashes($valuex);
							$video_code = get_option($valuey);
							?>
							<div class="description"><?php echo $value['name']; ?><br /><span><?php 
								if (isset($value['description'])){
							echo $value['description']; }
							?></span></div>
							<div class="input-option"><p><textarea name="<?php echo $valuey; ?>" class="mytext" cols="40%" rows="8" /><?php if ( get_option($valuey) != "") { echo stripslashes($video_code); } else { echo $value['std']; } ?>
							</textarea></p></div>
							</div>


							<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "colorpicker") ) { // setting ?>

							<?php $i = ""; $i == $i++ ; ?>

							<div class="tab-option">
							<div class="description"><?php echo $value['name']; ?><br /><span>	<?php 
									if (isset($value['description'])){
								echo $value['description']; }
								?></span></div>
							<div class="input-option"><p><input class="color {required:false,hash:true}" name="<?php echo $value['id']; ?>" id="colorpickerField<?php echo $i; ?>" type="text" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" /></p></div>
							</div>

							<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "select") ) { // setting ?>

							<div class="tab-option">
							<div class="description"><?php echo $value['name']; ?><br /><span>	<?php 
									if (isset($value['description'])){
								echo $value['description']; }
								?></span></div>
							<div class="input-option"><p><select name="<?php echo $value['id']; ?>" class="myselect" id="<?php echo $value['id']; ?>">
							<?php foreach ($value['options'] as $option) { ?>
							<option<?php if ( get_option( $value['id'] ) == $option) { echo ' selected="selected"'; } elseif ($option == $value['std']) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option>
							<?php } ?>
							</select>
							</p>
							</div>
							</div>

							<?php } ?>
					<?php } ?>
				</li>
				</ul>
				
				<ul style="" class="ui-tabs-panel" id="navigation">
				<li>
					<h2><?php _e("Navigation Colours", 'network') ?></h2>

					<?php $value_var = 'navigation'; foreach ($options3 as $value) { ?>

							<?php if (($value['inblock'] == $value_var) && ($value['type'] == "text")) { // setting ?>

							<div class="tab-option">
							<div class="description"><?php echo $value['name']; ?><br /><span>	<?php 
									if (isset($value['description'])){
								echo $value['description']; }
								?></span></div>
							<div class="input-option"><p><input name="<?php echo $value['id']; ?>" class="myfield" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo stripslashes(get_option( $value['id']) ); } else { echo stripslashes($value['std']); } ?>" /></p></div>
							</div>

							<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "textarea")) { // setting ?>


							<div class="tab-option">
							<?php
							$valuex = $value['id'];
							$valuey = stripslashes($valuex);
							$video_code = get_option($valuey);
							?>
							<div class="description"><?php echo $value['name']; ?><br /><span><?php 
								if (isset($value['description'])){
							echo $value['description']; }
							?></span></div>
							<div class="input-option"><p><textarea name="<?php echo $valuey; ?>" class="mytext" cols="40%" rows="8" /><?php if ( get_option($valuey) != "") { echo stripslashes($video_code); } else { echo $value['std']; } ?>
							</textarea></p></div>
							</div>


							<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "colorpicker") ) { // setting ?>

							<?php $i = ""; $i == $i++ ; ?>

							<div class="tab-option">
							<div class="description"><?php echo $value['name']; ?><br /><span>	<?php 
									if (isset($value['description'])){
								echo $value['description']; }
								?></span></div>
							<div class="input-option"><p><input class="color {required:false,hash:true}" name="<?php echo $value['id']; ?>" id="colorpickerField<?php echo $i; ?>" type="text" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" /></p></div>
							</div>

							<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "select") ) { // setting ?>

							<div class="tab-option">
							<div class="description"><?php echo $value['name']; ?><br /><span>	<?php 
									if (isset($value['description'])){
								echo $value['description']; }
								?></span></div>
							<div class="input-option"><p><select name="<?php echo $value['id']; ?>" class="myselect" id="<?php echo $value['id']; ?>">
							<?php foreach ($value['options'] as $option) { ?>
							<option<?php if ( get_option( $value['id'] ) == $option) { echo ' selected="selected"'; } elseif ($option == $value['std']) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option>
							<?php } ?>
							</select>
							</p>
							</div>
							</div>

							<?php } ?>
					<?php } ?>
				</li>
				</ul>
	</div>
	</div>



	<div id="submitsection">
		
		<div class="submit">
		<h2><?php _e("Click this to save your theme options", 'network') ?></h2>
	<input name="save3" type="submit" class="sbutton" value="<?php echo esc_attr(__('Save All Options','network')); ?>" />
	<input type="hidden" name="theme_action3" value="save3" />
	</div>
	</div>
	</div>
	</form>



	<form method="post">
	<div id="resetsection">
	<div class="submit">
		<h2><?php _e("Clicking this will reset all theme options - use with caution", 'network') ?></h2>
	<input name="reset3" type="submit" class="sbutton" value="<?php echo esc_attr(__('Reset All Options','network')); ?>" />
	<input type="hidden" name="theme_action3" value="reset3" />
	</div>
	</div>
	</form>


	</div>

<?php
}


/* Preset Styling section */
/* stylesheet addition */
$alt_stylesheet_path = get_template_directory() .'/library/styles/';
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

	array(  "name" => __("Choose Your Network Preset Style:", 'network'),
		  	"id" => $shortname. $shortprefix . "custom_style",
			"std" => "default.css",
			"type" => "radio",
			"options" => $alt_stylesheets)
	);

function network_ready_style_admin_panel() {
	echo "<div id=\"admin-options\">";
	
	global $themename, $shortname, $options2;
	
	if ( isset($_REQUEST['saved2']) ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings saved.</strong></p></div>';
	if ( isset($_REQUEST['reset2']) ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings reset.</strong></p></div>';
?>

<h4><?php echo "$themename"; ?> <?php _e('Choose your Network Preset Style', 'network'); ?></h4>
<form action="" method="post">
<div class="get-listings">
<h2><?php _e("Style Select:", 'network') ?></h2>
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
	<img src="<?php bloginfo('template_directory'); ?>/library/styles/images/<?php echo $screenshot_img . '.png'; ?>" alt="<?php echo $screenshot_img; ?>" />
</div>
<input type="radio" name="<?php echo $value['id']; ?>" value="<?php echo $option2; ?>" <?php echo $checked; ?> /><?php echo $option2; ?>
</li>

<?php } 
} ?>

</ul>
</div>
</div>
	<p id="top-margin" class="save-p">
		<input name="save2" type="submit" class="sbutton" value="<?php echo esc_attr(__('Save Options', 'network')); ?>" />
		<input type="hidden" name="theme_action2" value="save2" />
	</p>
</form>

<form method="post">
	<p class="save-p">
		<input name="reset2" type="submit" class="sbutton" value="<?php echo esc_attr(__('Reset Options', 'network')); ?>" />
		<input type="hidden" name="theme_action2" value="reset2" />
	</p>
</form>
</div>

<?php }

function network_admin_register() {
		global $themename, $shortname, $options;
			$action1 = isset($_REQUEST['theme_action1']);
		if ( isset($_GET['page']) == 'functions.php' ) {
		if ( 'save1' == $action1 ) {
		foreach ($options as $value) {
			if( isset( $_REQUEST[ $value['id'] ] ) ) {
				if (is_array($_REQUEST[ $value['id'] ])) {
					// if it's an array, we assume it's a select/multiple
					// convert array to serialize
					$sarray = serialize($_REQUEST[ $value['id'] ]);
					update_option( $value['id'], $sarray ); 
				}
				else{
					update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); 
				}
			} 
			else { delete_option( $value['id'] ); } 
		}
		foreach ($options as $value) {
		if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } else { delete_option( $value['id'] ); } }
		header("Location: admin.php?page=functions.php&saved1=true");
		die;
	} else if( 'reset1' == $action1 ) {
		foreach ($options as $value) {
		delete_option( $value['id'] ); }

		header("Location: admin.php?page=functions.php&reset1=true");
		die;
		}
		}

}


function network_ready_style_admin_register() {
	global $themename, $shortname, $options, $options2, $options3, $bp_existed, $multi_site_on;
		$action2 = isset($_REQUEST['theme_action2']);
	if (isset($_GET['page']) == 'network-themes.php' ) {
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
			header("Location: admin.php?page=network-themes.php&saved2=true");
			die;
		} 
		else if( 'reset2' == $action2 ) {
			foreach ($options2 as $value) {
				delete_option( $value['id'] ); 
			}
			header("Location: admin.php?page=network-themes.php&reset2=true");
			die;
		}
	}

}


function network_custom_style_admin_register() {
	global $themename, $shortname, $options, $options2, $options3, $bp_existed, $multi_site_on;
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
				header("Location: admin.php?page=styling-functions.php&saved3=true");
				die;
				} 
				else if( 'reset3' == $action3 ) {
					foreach ($options3 as $value) {
						delete_option( $value['id'] ); 
					}
				header("Location: admin.php?page=styling-functions.php&reset3=true");
				die;
				}
			}
	}

function network_admin_head() { ?>
	<link href="<?php bloginfo('template_directory'); ?>/library/options/options-css.css" rel="stylesheet" type="text/css" />
	<?php if ( (isset($_GET['page']) && $_GET['page'] == 'styling-functions.php' ) || ( isset($_GET['page']) && $_GET['page'] == 'functions.php' )) {?>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/library/scripts/jscolor.js"></script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/library/scripts/jquery-ui-personalized-1.6rc2.min.js"></script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/library/scripts/jquery.cookie.min.js"></script>
	<?php 	wp_enqueue_script("jquery"); ?>
		<script type="text/javascript">
			   jQuery.noConflict();
		
		jQuery(document).ready(function(){
		jQuery('ul#tabm').tabs({event: "click"});
		});
		</script>

	<?php } ?>
	
	<?php if ( isset($_GET['page']) == 'network-theme'){?>
			<link href="<?php bloginfo('template_directory'); ?>/library/options/custom-faq.css" rel="stylesheet" type="text/css" />
	<?php } ?>
	
	<?php if ( isset($_GET['page'])  == 'network-themes.php'){?>
			<link href="<?php bloginfo('template_directory'); ?>/library/options/custom-admin.css" rel="stylesheet" type="text/css" />
	<?php } ?>
	<?php if (isset($_GET['page']) == 'step-functions.php'){?>
			<link href="<?php bloginfo('template_directory'); ?>/library/options/custom-steps.css" rel="stylesheet" type="text/css" />
				<?php 	wp_enqueue_script("jquery"); ?>
			<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/library/scripts/accordion-menu/javascript.js"></script>
	<?php } ?>
<?php }

function network_landing_page() {
    ?>
<div id="faq-panel">
	<h2><?php _e("Getting started with the Network theme", 'network'); ?></h2>
	<div class="faqsection">
		<div class="faqtitle">
		<h2><?php _e("Learn how to set up the front page:", 'network'); ?></h2>
			<a class="button" href="admin.php?page=step-functions.php"><?php _e("Step by step front page setup", 'network'); ?></a>
		</div>
		<div class="note">
			<?php _e("When you load up Network it already works but you can dig deeper and create your front page just the way you want.", 'network'); ?>
		</div>
	</div>
	
	<div class="faqsection">
		<div class="faqtitle">
		<h2><?php _e("Choose your style:", 'network'); ?></h2>
			<a class="button" href="admin.php?page=network-themes.php"><?php _e("Network styles", 'network'); ?></a>
		</div>
		<div class="note">
			<?php _e("There are 3 styles to choose from or you can create your own.", 'network'); ?>
		</div>
	</div>
	
	<div class="faqsection">
		<div class="faqtitle">
		<h2><?php _e("Enjoy your theme and explore:", 'network'); ?>
			</h2>
			<br />	<br />
		<p>
			
		<a class="button" href="admin.php?page=step-functions.php"><?php _e("Step by step guides", 'network'); ?>
			</a>
		<a class="button" href="admin.php?page=functions.php"><?php _e("Explore advanced options", 'network'); ?>
			</a>
		<a class="button" href="admin.php?page=styling-functions.php"><?php _e("Create your own style", 'network'); ?>
			</a>
		</p>
		<div class="note">
			<?php _e("Network is a theme packed with options giving you lots of ways to create your own site.", 'network'); ?>
		</div>
		</div>
	</div>
	
	<div class="clear"></div>
</div>
<?php
}


function network_step_page() {
    ?>
<div id="step-panel">
		<h2><?php _e("Help &amp; Support for the Network theme", 'network'); ?></h2>
		<div class="note">
			<?php _e("In our Help &amp; Support section you will find step by step guides for this theme.  Should you at any time require further support you can visit our", 'network'); ?>
			 <a href="http://premium.wpmudev.org/forums/"><?php _e("Forums", 'network'); ?></a>.
		</div>
		<div class="accordionButton"><h2><?php _e("How do I set up my front page?", 'network'); ?></h2></div>
		<div class="accordionContent">
			<h4><?php _e("Step one: Your site should automatically pick up your blog posts but you can further set up by click Advanced Options", 'network'); ?></h4>
			<div class="stepimage_wrapper">
				<div id="homepage_one"></div>
			</div>
			<h4><?php _e("Step two: Click the first tab to make sure you are on Front Page Settings", 'network'); ?></h4>
			<div class="stepimage_wrapper">
				<div id="homepage_two"></div>
			</div>
			<h4><?php _e("Step three: Using the drop down select blog posts or network posts (network only works if using multisite).", 'network'); ?></h4>
			<div class="stepimage_wrapper">
				<div id="homepage_three"></div>
			</div>
			<h4><?php _e("Step four: If you are using multisite you can exclude blogs using the 'exclude blogs box'.", 'network'); ?></h4>
			<div class="stepimage_wrapper">
				<div id="homepage_four"></div>
			</div>
			<h4><?php _e("Step five: Next we have a range of display options.  Select the number to start from as a page, number of blogs posts per page and total number of blog posts even.", 'network'); ?></h4>
			<div class="stepimage_wrapper">
				<div id="homepage_five"></div>
			</div>
			<h4><?php _e("Step six: You can also set the label of the buttons.", 'network'); ?></h4>
			<div class="stepimage_wrapper">
				<div id="homepage_six"></div>
			</div>
			<h4><?php _e("Step seven: Network comes with the built in ability to show thumbnails automatically.  You can turn this on and off", 'network'); ?></h4>
			<div class="stepimage_wrapper">
				<div id="homepage_seven"></div>
			</div>
			<h4><?php _e("Step eight: Should a post not have an image you can have your own custom image to show - make sure it's the exact size though of 227px wide and 108px in height", 'network'); ?></h4>
			<div class="stepimage_wrapper">
				<div id="homepage_eight"></div>
			</div>
				<h4><?php _e("Step nine: Make sure you save all options once you've set up your front page.", 'network'); ?></h4>
				<div class="stepimage_wrapper">
					<div id="homepage_nine"></div>
				</div>
					<h4><?php _e("Step ten: If at any point you want to reset the options just hit reset", 'network'); ?></h4>
					<div class="stepimage_wrapper">
						<div id="homepage_ten"></div>
					</div>
					
		</div>
			<div class="accordionButton"><h2><?php _e("What BuddyPress specific options are there?", 'network'); ?></h2></div>
			<div class="accordionContent">
						<h4><?php _e("If you have BuddyPress installed you can set up 3 options along with add profile fields.  You can show social icons, display badges and show latest blog posts.", 'network'); ?>
							</h4>
								<div class="stepimage_wrapper">
									<div id="buddypress_one"></div>
								</div>
			</div>
			<div class="accordionButton"><h2><?php _e("How do I set up the social icon fields if using BuddyPress?", 'network'); ?></h2></div>
			<div class="accordionContent">
						<h4><?php _e("Step one: Built into Network there are some profile fields styling.  In order to use these you need to create those profile fields.  Click Profile Field Setup under the BuddyPress menu.", 'network'); ?>
							</h4>
						<div class="stepimage_wrapper">
							<div id="profile_one"></div>
						</div>
							<h4><?php _e("Step two: Click Add New Field to add just one field or add new field group to add a group.  For social icons we're going to add to the existing base group.", 'network'); ?>
								</h4>
							<div class="stepimage_wrapper">
								<div id="profile_two"></div>
							</div>
										<h4><?php _e("Step three: Click Add New Field then enter the field details for this case make sure the title is linkedin for linkedin.  A full list of profile fields is as available in these FAQs.", 'network'); ?>
											</h4>
										<div class="stepimage_wrapper">
											<div id="profile_three"></div>
										</div>
										
											<h4><?php _e("Step four: Save the profile field.", 'network'); ?>
												</h4>
											<div class="stepimage_wrapper">
												<div id="profile_four"></div>
											</div>
											
															<h4><?php _e("Step five: On the profile settings you can now add a new field for your linkedin url.", 'network'); ?>
																</h4>
															<div class="stepimage_wrapper">
																<div id="profile_five"></div>
															</div>
															
																			<h4><?php _e("Step six: The social icons will show on the user profile.  *You must have show icons ON under Advanced Options > BuddyPress for this to work.", 'network'); ?>
																				</h4>
																			<div class="stepimage_wrapper">
																				<div id="profile_six"></div>
																			</div>
			</div>
				<div class="accordionButton"><h2><?php _e("What are all the profile fields I can set up?", 'network'); ?></h2></div>
				<div class="accordionContent">
							<h4><?php _e("Name of field: linkedin<br />
							Name of field: facebook<br />
							Name of field: twitter<br />
							Name of field: foursquare<br />
							Name of field: youtube<br />
							Name of field: bio<br />
							* You will only see these if you set them up as profile fields on your BuddyPress install.  You can edit how they show and format in sidebar-profile.php", 'network'); ?>
								</h4>
				</div>
					<div class="accordionButton"><h2><?php _e("How do I add a background or change the background color?", 'network'); ?></h2></div>
					<div class="accordionContent">
								<h4><?php _e("Network uses the built in WordPress background feature.  Click Appearance > Background to set this up.", 'network'); ?>
									</h4>
										<div class="stepimage_wrapper">
											<div id="background_one"></div>
										</div>
					</div>
					
						<div class="accordionButton"><h2><?php _e("How do I add a a custom header?", 'network'); ?></h2></div>
						<div class="accordionContent">
									<h4><?php _e("Network has a built in custom header feature you can use to add an advert, show a banner.. the choice is yours.  Click Appearance > Header to set this up.", 'network'); ?>
										</h4>
											<div class="stepimage_wrapper">
												<div id="header_one"></div>
											</div>
						</div>
						
							<div class="accordionButton"><h2><?php _e("How do add a menu?", 'network'); ?></h2></div>
							<div class="accordionContent">
										<h4><?php _e("Network uses the built in WordPress menus.  Click Appearance > Menus to set these up.", 'network'); ?>
											</h4>
												<div class="stepimage_wrapper">
													<div id="menus_one"></div>
												</div>
							</div>
					
							<div class="accordionButton"><h2><?php _e("How do I create a full width page?", 'network'); ?></h2></div>
							<div class="accordionContent">
										<h4><?php _e("Step one: Create a new page and assign the page template 'Full width'.", 'network'); ?>
											</h4>
										<div class="stepimage_wrapper">
											<div id="page_one"></div>
										</div>
							</div>
					
						
							
									<div class="accordionButton"><h2><?php _e("How do I create a news page of my blog posts?", 'network'); ?></h2></div>
									<div class="accordionContent">
												<h4><?php _e("Step one: Create a new page and assign the page template 'Blog news'.", 'network'); ?>
													</h4>
												<div class="stepimage_wrapper">
													<div id="page_one"></div>
												</div>
									</div>
									
	
	<div class="clear"></div>
</div>
<?php
}

function network_pages() {
	add_menu_page(__('Network Theme','network'), __('Network Theme','network'), 'manage_options',  'network-theme', 'network_landing_page', get_bloginfo('template_directory').'/library/styles/images/network-icon.png', 120);
	add_submenu_page('network-theme', __("Getting started", 'network'), __("Getting Started", 'network'), 'manage_options', 'network-theme', 'network_landing_page');
	add_submenu_page('network-theme', __("Advanced Options", 'network'), __("Advanced Options", 'network'), 'edit_theme_options', 'functions.php', 'network_admin_panel'); 
	add_submenu_page('network-theme', __("Preset Styles", 'network'), __("Preset Styles", 'network'), 'edit_theme_options', 'network-themes.php', 'network_ready_style_admin_panel');
	if ( ! defined('HIDE_CUSTOMIZATION_OPTION') || HIDE_CUSTOMIZATION_OPTION == false )
		add_submenu_page('network-theme', __("Customization", 'network'), __("Customization", 'network'), 'edit_theme_options', 'styling-functions.php', 'network_custom_style_admin_panel');
	add_submenu_page('network-theme', __("Help & Support", 'network'), __("Help & Support", 'network'), 'manage_options', 'step-functions.php', 'network_step_page');
}

add_action('admin_head', 'network_admin_head');
add_action('admin_menu', 'network_admin_register');
add_action('admin_menu', 'network_ready_style_admin_register');
if ( ! defined('HIDE_CUSTOMIZATION_OPTION') || HIDE_CUSTOMIZATION_OPTION == false )
	add_action('admin_menu', 'network_custom_style_admin_register');
add_action('admin_menu', 'network_pages');
?>