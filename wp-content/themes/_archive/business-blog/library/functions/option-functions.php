<?php
function _g($str)
{
return __($str, 'option-page');
}

$themename = "Business Blog";
$themeversion = "1.2";
$shortname = "dev";
$shortprefix = "_businessblog_";
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

$options = array (
	array(
		"name" => __("Site name", 'business-blog'),
		"id" => $shortname . $shortprefix . "site_title",
		"inblock" => "branding",
		"type" => "text",
		"std" => "",
	),
	array("name" => __("Do you to use a custom large image logo rather than domain name text?", 'business-blog'),
		"description" => __("Enter your url in the next section if saying yes", 'business-blog'),
		"id" => $shortname . $shortprefix . "header_image",	     	
		"inblock" => "branding",
		"type" => "select",
		"std" => "Select",
		"options" => array("yes", "no")),

	array(
		"name" => __("Insert your logo full url here", 'business-blog'),
		"description" => __("You can upload your logo in <a href='media-new.php'>media panel</a> and copy paste the url here", 'business-blog'),
		"id" => $shortname . $shortprefix . "header_logo",
		"inblock" => "branding",
		"type" => "text",
		"std" => "",
	),

	array("name" => __("Do you to use a custom square image logo and your domain name text?", 'business-blog'),
		"description" => __("Enter your url in the next section if saying yes", 'business-blog'),
		"id" => $shortname . $shortprefix . "header_image_square",	     	
		"inblock" => "branding",
		"type" => "select",
		"std" => "Select",
		"options" => array("yes", "no")),

	array(
		"name" => __("Insert your square logo full url here", 'business-blog'),
		"description" => __("You can upload your logo in <a href='media-new.php'>media panel</a> and copy paste the url here", 'business-blog'),
		"id" => $shortname . $shortprefix . "header_logo_square",
		"inblock" => "branding",
		"type" => "text",
		"std" => "",
	),
	array(
		"name" => __("RSS title", 'business-blog'),
		"id" => $shortname . $shortprefix . "rss_title",
		"inblock" => "home",
		"type" => "text",
		"std" => "",
	),
	
	array(
		"name" => __("News title", 'business-blog'),
		"id" => $shortname . $shortprefix . "news_title",
		"inblock" => "home",
		"type" => "text",
		"std" => "",
	),
	


array( 	"name" => __("Select a category for your front page", 'business-blog'),
	"id" => $shortname . $shortprefix . "feature_cat",
	"inblock" => "home",
	"type" => "select",
	"std" => "Select a category:",
	"options" => $dev_categories),

array(
		"name" => __("Side advert one", 'business-blog'),
		"description" => __("You can upload your image in the <a href='media-new.php'>media panel</a> and copy paste the url here - 285 wide by 60 height", 'business-blog'),
		"id" => $shortname . $shortprefix . "adone",
"inblock" => "adverts",
		"type" => "text",
		"std" => "",
	),
array(
	"name" => __("Side advert one link", 'business-blog'),
	"id" => $shortname . $shortprefix . "adone_link",
	"inblock" => "adverts",
	"type" => "text",
	"std" => "",
),
array(
	"name" => __("Side advert one title", 'business-blog'),
	"id" => $shortname . $shortprefix . "adone_title",
	"inblock" => "adverts",
	"type" => "text",
	"std" => "",
),

array(
		"name" => __("Side advert two", 'business-blog'),
			"description" => __("You can upload your image in the <a href='media-new.php'>media panel</a> and copy paste the url here - 285 wide by 60 height", 'business-blog'),
		"id" => $shortname . $shortprefix . "adtwo",
"inblock" => "adverts",
		"type" => "text",
		"std" => "",
	),
array(
	"name" => __("Side advert two link", 'business-blog'),
	"id" => $shortname . $shortprefix . "adtwo_link",
	"inblock" => "adverts",
	"type" => "text",
	"std" => "",
),
array(
	"name" => __("Side advert two title", 'business-blog'),
	"id" => $shortname . $shortprefix . "adtwo_title",
	"inblock" => "adverts",
	"type" => "text",
	"std" => "",
),

array(
		"name" => __("Side advert three", 'business-blog'),
			"description" => __("You can upload your image in the <a href='media-new.php'>media panel</a> and copy paste the url here - 285 wide by 60 height", 'business-blog'),
		"id" => $shortname . $shortprefix . "adthree",
"inblock" => "adverts",
		"type" => "text",
		"std" => "",
	),
array(
	"name" => __("Side advert three link", 'business-blog'),
	"id" => $shortname . $shortprefix . "adthree_link",
	"inblock" => "adverts",
	"type" => "text",
	"std" => "",
),
array(
	"name" => __("Side advert three title", 'business-blog'),
	"id" => $shortname . $shortprefix . "adthree_title",
	"inblock" => "adverts",
	"type" => "text",
	"std" => "",
),

array(
		"name" => __("Side advert four", 'business-blog'),
			"description" => __("You can upload your image in the <a href='media-new.php'>media panel</a> and copy paste the url here - 285 wide by 60 height", 'business-blog'),
		"id" => $shortname . $shortprefix . "adfour",
"inblock" => "adverts",
		"type" => "text",
		"std" => "",
	),
array(
	"name" => __("Side advert four link", 'business-blog'),
	"id" => $shortname . $shortprefix . "adfour_link",
	"inblock" => "adverts",
	"type" => "text",
	"std" => "",
),
array(
	"name" => __("Side advert four title", 'business-blog'),
	"id" => $shortname . $shortprefix . "adfour_title",
	"inblock" => "adverts",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("Useful links title", 'business-blog'),
	"id" => $shortname . $shortprefix . "links_title",
	"inblock" => "links",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("Useful link one", 'business-blog'),
	"id" => $shortname . $shortprefix . "linkone",
	"inblock" => "links",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("Useful link one title", 'business-blog'),
	"id" => $shortname . $shortprefix . "linkone_title",
	"inblock" => "links",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("Useful link one", 'business-blog'),
	"id" => $shortname . $shortprefix . "linkone_text",
	"inblock" => "links",
	"type" => "textarea",
	"std" => "",
),

array(
	"name" => __("Useful link two", 'business-blog'),
	"id" => $shortname . $shortprefix . "linktwo",
	"inblock" => "links",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("Useful link two title", 'business-blog'),
	"id" => $shortname . $shortprefix . "linktwo_title",
	"inblock" => "links",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("Useful link two", 'business-blog'),
	"id" => $shortname . $shortprefix . "linktwo_text",
	"inblock" => "links",
	"type" => "textarea",
	"std" => "",
),

array(
	"name" => __("Useful link three", 'business-blog'),
	"id" => $shortname . $shortprefix . "linkthree",
	"inblock" => "links",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("Useful link three title", 'business-blog'),
	"id" => $shortname . $shortprefix . "linkthree_title",
	"inblock" => "links",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("Useful link three", 'business-blog'),
	"id" => $shortname . $shortprefix . "linkthree_text",
	"inblock" => "links",
	"type" => "textarea",
	"std" => "",
),

array(
	"name" => __("Useful link four", 'business-blog'),
	"id" => $shortname . $shortprefix . "linkfour",
	"inblock" => "links",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("Useful link four title", 'business-blog'),
	"id" => $shortname . $shortprefix . "linkfour_title",
	"inblock" => "links",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("Useful link four", 'business-blog'),
	"id" => $shortname . $shortprefix . "linkfour_text",
	"inblock" => "links",
	"type" => "textarea",
	"std" => "",
),

array(
	"name" => __("Useful link five", 'business-blog'),
	"id" => $shortname . $shortprefix . "linkfive",
	"inblock" => "links",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("Useful link five title", 'business-blog'),
	"id" => $shortname . $shortprefix . "linkfive_title",
	"inblock" => "links",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("Useful link five", 'business-blog'),
	"id" => $shortname . $shortprefix . "linkfive_text",
	"inblock" => "links",
	"type" => "textarea",
	"std" => "",
),

array(
	"name" => __("Text for BuddyPress section", 'business-blog'),
	"id" => $shortname . $shortprefix . "bp_text",
	"inblock" => "buddypress",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("Show sign up box?", 'business-blog'),
	"id" => $shortname . $shortprefix . "signupfeat_on",
	"inblock" => "branding",
	"type" => "select",
	"std" => "Select",
	"options" => array("yes", "no")
),

array(
	"name" => __("Sign up feature text", 'business-blog'),
	"id" => $shortname . $shortprefix . "signupfeat_text",
	"inblock" => "branding",
	"type" => "textarea",
	"std" => "",
),

array(
	"name" => __("Sign up button text", 'business-blog'),
	"id" => $shortname . $shortprefix . "signupfeat_buttontext",
	"inblock" => "branding",
	"type" => "text",
	"std" => "",
),


array(
	"name" => __("Sign up custom link (enter a custom link if don't want default ones)", 'business-blog'),
	"id" => $shortname . $shortprefix . "signupfeat_buttontextcustom",
	"inblock" => "branding",
	"type" => "text",
	"std" => "",
),

);

function businessblog_admin_panel() {
	global $themename, $shortname, $options, $options2, $options3, $bp_existed, $multi_site_on;
	if ( isset($_REQUEST['saved'] )) echo '<div id="message" class="updated fade"><p><strong>'. $themename . __(' settings saved.', 'business-blog') . '</strong></p></div>';
	if ( isset($_REQUEST['reset'] )) echo '<div id="message" class="updated fade"><p><strong>'. $themename . __(' settings reset.', 'business-blog') . '</strong></p></div>';
	?>
	<div id="options-panel">
	<form action="" method="post">

	  <div id="sbtabs">
	  <div class="tabmc">
	  <ul class="ui-tabs-nav" id="tabm">
	  <li class="first ui-tabs-selected"><a href="#home"><?php _e("Home",'business-blog'); ?></a></li>
	<li class=""><a href="#adverts"><?php _e("Adverts",'business-blog'); ?></a></li>
	<li class=""><a href="#links"><?php _e("Links",'business-blog'); ?></a></li>
		  <?php if($bp_existed == 'true') { ?><li class=""><a href="#buddypress"><?php _e("BuddyPress",'business-blog'); ?></a></li><?php } ?>
	  <li class=""><a href="#branding"><?php _e("Branding",'business-blog'); ?></a></li>
	  </ul>
	  </div>

	<div class="tabc">


	<ul style="" class="ui-tabs-panel" id="home">
	<li>

	<h2><?php _e("Home page settings", 'business-blog') ?></h2>


	<?php $value_var = 'home'; foreach ($options as $value) { ?>

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
	</li></ul>

	

	<ul style="" class="list3 ui-tabs-panel ui-tabs-hide" id="adverts">

	<li>

	<h2><?php _e("Advert Settings", 'business-blog') ?></h2>

	<?php $value_var = 'adverts'; foreach ($options as $value) { ?>

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
	</li></ul>
	
	<ul style="" class="list3 ui-tabs-panel ui-tabs-hide" id="links">

	<li>

	<h2><?php _e("Link Settings", 'business-blog') ?></h2>

	<?php $value_var = 'links'; foreach ($options as $value) { ?>

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
	</li></ul>



	<?php if($bp_existed == 'true') { ?>
	<ul style="" class="list7 ui-tabs-panel ui-tabs-hide" id="buddypress">

	<li>

	<h2><?php _e("BuddyPress Settings", 'business-blog') ?></h2>

	<?php $value_var = 'buddypress'; foreach ($options as $value) { ?>

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
	</li></ul>
	<?php } ?>


	<ul style="" class="list9 ui-tabs-panel ui-tabs-hide" id="branding">

	<li>

	<h2><?php _e("Branding Settings", 'business-blog') ?></h2>

	<?php $value_var = 'branding'; foreach ($options as $value) { ?>

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
	</li></ul>


	</div>
	</div>



	<div class="submit">
	<input name="save" type="submit" class="sbutton" value="<?php echo esc_attr(__('Save All Options','business-blog')); ?>" />
	<input type="hidden" name="theme_action" value="save" />
	</div>

	</form>



	<form method="post">
	<div class="submit">
	<input name="reset" type="submit" class="sbutton" value="<?php echo esc_attr(__('Reset All Options','business-blog')); ?>" />
	<input type="hidden" name="theme_action" value="reset" />
	</div>

	</form>


	</div>

	<?php
	}
	
$options3 = array (

array(
	"name" => __("Choose your body font", 'business-blog'),
	"id" => $shortname . $shortprefix . "body_font",
	"type" => "select",
	"inblock" => "design",
	"std" => "Arial, sans-serif",
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
	"name" => __("Choose your header font", 'business-blog'),
	"id" => $shortname . $shortprefix . "header_font",
	"type" => "select",
	"inblock" => "design",
	"std" => "Arial, sans-serif",
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
	"name" => __("Choose your body font colour", 'business-blog'),
	"id" => $shortname . $shortprefix . "font_colour",
	"inblock" => "design",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your link colour", 'business-blog'),
	"id" => $shortname . $shortprefix . "link_colour",
	"inblock" => "design",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your link hover colour", 'business-blog'),
	"id" => $shortname . $shortprefix . "link_hover_colour",
	"inblock" => "design",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your link visited colour", 'business-blog'),
	"id" => $shortname . $shortprefix . "link_visited_colour",
	"inblock" => "design",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your button background colour", 'business-blog'),
	"id" => $shortname . $shortprefix . "button_background_colour",
	"inblock" => "design",
	"std" => "",
	"type" => "colorpicker"),
	
array(
	"name" => __("Choose your button text colour", 'business-blog'),
	"id" => $shortname . $shortprefix . "button_text_colour",
	"inblock" => "design",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your button background hover colour", 'business-blog'),
	"id" => $shortname . $shortprefix . "button_background_hover_colour",
	"inblock" => "design",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your button hover text colour", 'business-blog'),
	"id" => $shortname . $shortprefix . "button_hover_text_colour",
	"inblock" => "design",
	"std" => "",
	"type" => "colorpicker"),


);

function businessblog_custom_style_admin_panel() {

		global $themename, $options, $options2, $options3, $bp_existed, $multi_site_on;

		if ( isset($_REQUEST['saved3'] )) echo '<div id="message" class="updated fade"><p><strong>'. $themename . __(' settings saved.', 'business-blog') . '</strong></p></div>';
		if ( isset($_REQUEST['reset3'] )) echo '<div id="message" class="updated fade"><p><strong>'. $themename . __(' settings reset.', 'business-blog') . '</strong></p></div>';
		?>

		<div id="options-panel">
		<form action="" method="post">

		  <div id="sbtabs">
		  <div class="tabmc">
		  <ul class="ui-tabs-nav" id="tabm">
		  <li class="first ui-tabs-selected"><a href="#design"><?php _e("Design",'business-blog'); ?></a></li>
		  <?php if($bp_existed == 'true') { ?><li class=""><a href="#bp"><?php _e("BuddyPress",'business-blog'); ?></a></li><?php } ?>
		  </ul>
		</div>


		<div class="tabc">


		<ul style="" class="ui-tabs-panel" id="design">
		<li>
			<h2><?php _e("Design styling", 'business-blog') ?></h2>

			<?php $value_var = 'design'; foreach ($options3 as $value) { ?>

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


		<?php if($bp_existed == 'true') { ?>
		<ul style="" class="list7 ui-tabs-panel ui-tabs-hide" id="bp">

		<li>

		<h2><?php _e("BuddyPress Styling", 'business-blog') ?></h2>

		<?php $value_var = 'bp'; foreach ($options3 as $value) { ?>

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
		</li></ul>
		<?php } ?>

	</div>
	</div>



	<div class="submit">
	<input name="save3" type="submit" class="sbutton" value="<?php echo esc_attr(__('Save All Options','business-blog')); ?>" />
	<input type="hidden" name="theme_action3" value="save3" />
	</div>

	</form>



	<form method="post">
	<div class="submit">
	<input name="reset3" type="submit" class="sbutton" value="<?php echo esc_attr(__('Reset All Options','business-blog')); ?>" />
	<input type="hidden" name="theme_action3" value="reset3" />
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

	array(  "name" => __("Choose Your Business Blog Preset Style:", 'business-blog'),
		  	"id" => $shortname. $shortprefix . "custom_style",
			"std" => "default.css",
			"type" => "radio",
			"options" => $alt_stylesheets)
	);

function businessblog_ready_style_admin_panel() {
	echo "<div id=\"admin-options\">";
	
	global $themename, $shortname, $options2;
	
	if ( isset($_REQUEST['saved2'] )) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings saved.</strong></p></div>';
	if ( isset($_REQUEST['reset2'] )) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings reset.</strong></p></div>';
?>

<h4><?php echo "$themename"; ?> <?php _e('Choose your Business Blog Preset Style', 'business-blog'); ?></h4>
<h2><?php _e('If you want to customise the theme options you MUST have default.css selected'); ?></h2>
<form action="" method="post">
<div class="get-listings">
<h2><?php _e("Style Select:", 'business-blog') ?></h2>
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
		<input name="save2" type="submit" class="sbutton" value="<?php echo esc_attr(__('Save Options', 'business-blog')); ?>" />
		<input type="hidden" name="theme_action2" value="save2" />
	</p>
</form>

<form method="post">
	<p class="save-p">
		<input name="reset2" type="submit" class="sbutton" value="<?php echo esc_attr(__('Reset Options', 'business-blog')); ?>" />
		<input type="hidden" name="theme_action2" value="reset2" />
	</p>
</form>
</div>

<?php }

function businessblog_admin_register() {
	global $themename, $shortname, $options;
		$action = isset($_REQUEST['theme_action']);
	if ( isset($_GET['page']) == 'functions.php' ) {
	if ( 'save' == $action ) {
	foreach ($options as $value) {
	update_option( $value['id'], isset($_REQUEST[ $value['id'] ] )); }
	foreach ($options as $value) {
	if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } else { delete_option( $value['id'] ); } }
	header("Location: themes.php?page=functions.php&saved=true");
	die;
	} else if( 'reset' == $action ) {
	foreach ($options as $value) {
	delete_option( $value['id'] ); }
	header("Location: themes.php?page=functions.php&reset=true");
	die;
	}
	}
		add_theme_page(_g ($themename . __(' Theme Options', 'business-blog')),  _g (__('Theme Options', 'business-blog')),  'edit_theme_options', 'functions.php', 'businessblog_admin_panel');
}


function businessblog_ready_style_admin_register() {
	global $themename, $shortname, $options, $options2, $options3, $bp_existed, $multi_site_on;
		$action2 = isset($_REQUEST['theme_action2']);
	if ( isset($_GET['page']) == 'business-blog-themes.php' ) {
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
			header("Location: themes.php?page=business-blog-themes.php&saved2=true");
			die;
		} 
		else if( 'reset2' == $action2 ) {
			foreach ($options2 as $value) {
				delete_option( $value['id'] ); 
			}
			header("Location: themes.php?page=business-blog-themes.php&reset2=true");
			die;
		}
	}
	add_theme_page(_g (__('Business Blog Preset Style', 'business-blog')),  _g (__('Preset Style', 'business-blog')),  'edit_theme_options', 'business-blog-themes.php', 'businessblog_ready_style_admin_panel');
}


function businessblog_custom_style_admin_register() {
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
			add_theme_page(_g ($themename . __('Custom styling', 'business-blog')),  _g (__('Custom Styling', 'business-blog')),  'edit_theme_options', 'styling-functions.php', 'businessblog_custom_style_admin_panel');
	}

function businessblog_admin_head() { ?>
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
	
	<?php if (isset($_GET['page']) == 'business-blog-themes.php'){?>
			<link href="<?php bloginfo('template_directory'); ?>/library/options/custom-admin.css" rel="stylesheet" type="text/css" />
	<?php } ?>
	
<?php }

add_action('admin_head', 'businessblog_admin_head');
add_action('admin_menu', 'businessblog_admin_register');
add_action('admin_menu', 'businessblog_ready_style_admin_register');
add_action('admin_menu', 'businessblog_custom_style_admin_register');

?>