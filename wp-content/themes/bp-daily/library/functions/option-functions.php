<?php
function _g($str)
{
return __($str, 'option-page');
}

$themename = "BuddyPress daily";
$themeversion = "1.0";
$shortname = "dev";
$shortprefix = "_buddydaily_";
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

array("name" => __("Pick your featured content section type: video, tabbed category content, slideshow", 'bp-daily'),
	"id" => $shortname . $shortprefix . "featuretype",	     	
	"box"=> "1",
	"inblock" => "featured",
    "type" => "select",
	"std" => "Select featured content type",
	"options" => array("video", "tabbed", "slideshow", "none")),

array(
	"name" => __("Enter your video code", 'bp-daily'),
	"id" => $shortname . $shortprefix . "video_code",
	"box"=> "1",
	"inblock" => "featured",
	"type" => "textarea",
	"std" => "",
),

array(
	"name" => __("If using tabs enter a title for your video", 'bp-daily'),
	"id" => $shortname . $shortprefix . "video_title",
	"box"=> "1",
	"inblock" => "featured",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("Enter your video description", 'bp-daily'),
	"id" => $shortname . $shortprefix . "video_description",
	"box"=> "1",
	"inblock" => "featured",
	"type" => "textarea",
	"std" => "",
),

array("name" => __("Select your slideshow speed in milliseconds? *(default is 3000 ms)", 'bp-daily'),
	"id" => $shortname . $shortprefix . "slideshow_speed",	     	
	"box"=> "1",
	"inblock" => "featured",
    "type" => "select",
	"std" => "Select yes or no",
	"options" => array("1000", "3000", "5000", "7000", "9000")),

array("name" => __("Select number of posts to show in slideshow", 'bp-daily'),					
	"id" => $shortname . $shortprefix . "slideshow_number",	     	
	"box"=> "1",
	"inblock" => "featured",
   	"type" => "select",
	"std" => "Select yes or no",
	"options" => array("2", "4", "6", "8", "10", "Unlimited")),

array( 	"name" => __("Select a category for your slideshow", 'bp-daily'),
	"id" => $shortname . $shortprefix . "feature_cat",
	"box"=> "1",
	"inblock" => "featured",
	"type" => "select",
	"std" => "Select a category:",
	"options" => $dev_categories),		
	
array("name" => __("Select display size of featured image (if above size it will center and show a portion)", 'bp-daily'),
	"id" => $shortname . $shortprefix . "feature_image_size",	     	
	"box"=> "1",
	"inblock" => "featured",
	"type" => "select",
	"std" => "Pick a size",
	"options" => array("medium", "large")),

array(
	"name" => __("If using tabs enter a title for your first tab", 'bp-daily'),
	"id" => $shortname . $shortprefix . "tabone_title",
	"box"=> "1",
	"inblock" => "featured",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("If using tabs enter a title for your second tab", 'bp-daily'),
	"id" => $shortname . $shortprefix . "tabtwo_title",
	"box"=> "1",
	"inblock" => "featured",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("If using tabs enter a title for your third tab", 'bp-daily'),
	"id" => $shortname . $shortprefix . "tabthree_title",
	"box"=> "1",
	"inblock" => "featured",
	"type" => "text",
	"std" => "",
),

array( 	"name" => __("Select a category for your first featured tab", 'bp-daily'),
	"id" => $shortname . $shortprefix . "tabcat_one",
	"box"=> "1",
	"inblock" => "featured",
	"type" => "select",
	"std" => "Select a category:",
	"options" => $dev_categories),

array( 	"name" => __("Select a category for your second featured tab", 'bp-daily'),
	"id" => $shortname . $shortprefix . "tabcat_two",
	"box"=> "1",
	"inblock" => "featured",
	"type" => "select",
	"std" => "Select a category:",
	"options" => $dev_categories),

array( 	"name" => __("Select a category for your third featured tab", 'bp-daily'),
	"id" => $shortname . $shortprefix . "tabcat_three",
	"box"=> "1",
	"inblock" => "featured",
	"type" => "select",
	"std" => "Select a category:",
	"options" => $dev_categories),

array("name" => __("Select display size of your feature tabs display (if above size it will centre and show a portion)", 'bp-daily'),
	"id" => $shortname . $shortprefix . "featuretabs_image_size",	     	
	"box"=> "1",
	"inblock" => "featured",
	"type" => "select",
	"std" => "Pick a size",
	"options" => array("thumbnail", "medium", "large")),
		
	array("name" => __("Pick your front content latest display format (columns or rows)", 'bp-daily'),
		"id" => $shortname . $shortprefix . "latesttype",	     	
		"box"=> "1",
		"inblock" => "content",
		"type" => "select",
		"std" => "Select content content type",
		"options" => array("column", "rows", "wall", "none")),

	array("name" => __("Select number of posts for your content wall (3 per row)", 'bp-daily'),					
		"id" => $shortname . $shortprefix . "wall_number",	     	
		"box"=> "2",
		"inblock" => "content",
		"type" => "select",
		"std" => "Select number",
		"options" => array("3", "6", "12", "15", "18", "21")),


	array("name" => __("Pick the number of column sections rows to show (1, 2, 3)", 'bp-daily'),
		"id" => $shortname . $shortprefix . "featurecat_row_num",	     	
		"box"=> "2",
		"inblock" => "content",
		"type" => "select",
		"std" => "Pick a number",
		"options" => array("1", "2", "3")),

	array( 	"name" => __("Select a category for your first content latest display if using columns", 'bp-daily'),
		"id" => $shortname . $shortprefix . "featurecat_one",
		"box"=> "2",
		"inblock" => "content",
		"type" => "select",
		"std" => "Select a category:",
		"options" => $dev_categories),

	array( 	"name" => __("Select a category for your second content latest display if using columns", 'bp-daily'),
		"id" => $shortname . $shortprefix . "featurecat_two",
		"box"=> "2",
		"inblock" => "content",
		"type" => "select",
		"std" => "Select a category:",
		"options" => $dev_categories),

	array( 	"name" => __("Select a category for your third content latest display if using columns", 'bp-daily'),
		"id" => $shortname . $shortprefix . "featurecat_three",
		"box"=> "2",
		"inblock" => "content",
		"type" => "select",
		"std" => "Select a category:",
		"options" => $dev_categories),

	array( 	"name" => __("Select a category for your fourth content latest display if using columns", 'bp-daily'),
		"id" => $shortname . $shortprefix . "featurecat_four",
		"box"=> "2",
		"inblock" => "content",
		"type" => "select",
		"std" => "Select a category:",
		"options" => $dev_categories),

	array( 	"name" => __("Select a category for your fifth content latest display if using columns", 'bp-daily'),
		"id" => $shortname . $shortprefix . "featurecat_five",
		"box"=> "2",
		"inblock" => "content",
		"type" => "select",
		"std" => "Select a category:",
		"options" => $dev_categories),

	array( 	"name" => __("Select a category for your sixth content latest display if using columns", 'bp-daily'),
		"id" => $shortname . $shortprefix . "featurecat_six",
		"box"=> "2",
		"inblock" => "content",
		"type" => "select",
		"std" => "Select a category:",
		"options" => $dev_categories),

	array( 	"name" => __("Select a category for your seventh content latest display if using columns", 'bp-daily'),
		"id" => $shortname . $shortprefix . "featurecat_seven",
		"box"=> "2",
		"inblock" => "content",
		"type" => "select",
		"std" => "Select a category:",
		"options" => $dev_categories),

	array( 	"name" => __("Select a category for your eighth content latest display if using columns", 'bp-daily'),
		"id" => $shortname . $shortprefix . "featurecat_eight",
		"box"=> "2",
		"inblock" => "content",
		"type" => "select",
		"std" => "Select a category:",
		"options" => $dev_categories),

	array( 	"name" => __("Select a category for your ninth content latest display if using columns", 'bp-daily'),
		"id" => $shortname . $shortprefix . "featurecat_nine",
		"box"=> "2",
		"inblock" => "content",
		"type" => "select",
		"std" => "Select a category:",
		"options" => $dev_categories),

	array("name" => __("Select display size of your content latest display (if above size it will centre and show a portion)", 'bp-daily'),
		"id" => $shortname . $shortprefix . "featurecat_image_size",	     	
		"box"=> "2",
		"inblock" => "content",
		"type" => "select",
		"std" => "Pick a size",
		"options" => array("thumbnail", "medium", "large")),

	array( 	"name" => __("Select a category for your content latest display if using rows", 'bp-daily'),
		"id" => $shortname . $shortprefix . "featurecat_rows",
		"box"=> "2",
		"inblock" => "content",
		"type" => "select",
		"std" => "Select a category:",
		"options" => $dev_categories),

	array("name" => __("Select how many posts to show if using content latest display rows", 'bp-daily'),
		"id" => $shortname . $shortprefix . "featurecat_rows_num",	     	
		"box"=> "2",
		"inblock" => "content",
		"type" => "select",
		"std" => "Pick a size",
		"options" => array("1", "2", "3", "4", "5", "6", "7", "8", "9", "10")),

	array(
		"name" => __("Show sign up feature box yes or no?", 'bp-daily'),
		"id" => $shortname . $shortprefix . "signupfeat_on",
		"box"=> "3",
		"inblock" => "spotlight",
		"type" => "select",
		"std" => "yes or no",
		"options" => array("yes", "no")
	),

	array(
		"name" => __("Enter some text to encourage sign ups to your site", 'bp-daily'),
		"id" => $shortname . $shortprefix . "signupfeat_text",
		"box"=> "3",
		"inblock" => "spotlight",
		"type" => "text",
		"std" => "",
	),
	
	
	array(
		"name" => __("Enter some text for a button to click to sign up to your site", 'bp-daily'),
		"id" => $shortname . $shortprefix . "signupfeat_buttontext",
		"box"=> "3",
		"inblock" => "spotlight",
		"type" => "text",
		"std" => "",
	),
	
	array(
		"name" => __("Enter a custom page if you've created one for your login", 'bp-daily'),
		"id" => $shortname . $shortprefix . "signupfeat_buttontextcustom",
		"box"=> "3",
		"inblock" => "spotlight",
		"type" => "text",
		"std" => "",
	),

		array( 	"name" => __("Select a category for your spotlight section", 'bp-daily'),
			"id" => $shortname . $shortprefix . "spotlight_category",
			"box"=> "3",
			"inblock" => "spotlight",
			"type" => "select",
			"std" => "Select a category:",
			"options" => $dev_categories),

		array("name" => __("Select how many posts to show for your spotlight category", 'bp-daily'),
			"id" => $shortname . $shortprefix . "spotlight_number",	     	
			"box"=> "3",
			"inblock" => "spotlight",
			"type" => "select",
			"std" => "Pick a size",
			"options" => array("1", "2", "3", "4", "5", "6", "7", "8", "9", "10")),

		array("name" => __("Select display size of your spotlight images (if above size it will centre and show a portion)", 'bp-daily'),
			"id" => $shortname . $shortprefix . "spotlight_image_size",	     	
			"box"=> "3",
			"inblock" => "spotlight",
			"type" => "select",
			"std" => "Pick a size",
			"options" => array("thumbnail", "medium", "large")),


		array(
			"name" => __("Insert your header advert full url here<br /><em>*you can upload your logo in <a href='media-new.php'>media panel</a> and copy paste the url here</em> (size is 468 x 60px)", 'bp-daily'),
			"id" => $shortname . $shortprefix . "header_advert",
			"box"=> "4",
			"inblock" => "advert",
			"type" => "text",
			"std" => "",
		),

		array(
			"name" => __("Give your header advert a title", 'bp-daily'),
			"id" => $shortname . $shortprefix . "header_advert_title",
			"box"=> "4",
			"inblock" => "advert",
			"type" => "text",
			"std" => "",
		),

		array(
			"name" => __("If you want your header advert to link somewhere please give the full url here", 'bp-daily'),
			"id" => $shortname . $shortprefix . "header_advert_link",
			"box"=> "4",
			"inblock" => "advert",
			"type" => "text",
			"std" => "",
		),

		array(
			"name" => __("If you want to use advert code (google, amazon / any html) insert it here rather than an image", 'bp-daily'),
			"id" => $shortname . $shortprefix . "header_advert_code",
			"box"=> "4",
			"inblock" => "advert",
			"type" => "textarea",
			"std" => "",
		),

		array(
			"name" => __("Insert your sidebar wide side advert full url here<br /><em>*you can upload your logo in <a href='media-new.php'>media panel</a> and copy paste the url here</em> (size is 300 x 250px)", 'bp-daily'),
			"id" => $shortname . $shortprefix . "wide_advert",
			"box"=> "4",
			"inblock" => "advert",
			"type" => "text",
			"std" => "",
		),

		array(
			"name" => __("Give your wide side advert a title", 'bp-daily'),
			"id" => $shortname . $shortprefix . "wide_advert_title",
			"box"=> "4",
			"inblock" => "advert",
			"type" => "text",
			"std" => "",
		),

		array(
			"name" => __("If you want your wide side advert to link somewhere please give the full url here", 'bp-daily'),
			"id" => $shortname . $shortprefix . "wide_advert_link",
			"box"=> "4",
			"inblock" => "advert",
			"type" => "text",
			"std" => "",
		),

		array(
			"name" => __("Insert your sidebar left advert full url here<br /><em>*you can upload your logo in <a href='media-new.php'>media panel</a> and copy paste the url here</em> (size is maximum 160px wide but centres if lower)", 'bp-daily'),
			"id" => $shortname . $shortprefix . "sideleft_advert",
			"box"=> "4",
			"inblock" => "advert",
			"type" => "text",
			"std" => "",
		),

		array(
			"name" => __("Give your sidebar left advert a title", 'bp-daily'),
			"id" => $shortname . $shortprefix . "sideleft_advert_title",
			"box"=> "4",
			"inblock" => "advert",
			"type" => "text",
			"std" => "",
		),

		array(
			"name" => __("If you want your sidebar left advert to link somewhere please give the full url here", 'bp-daily'),
			"id" => $shortname . $shortprefix . "sideleft_advert_link",
			"box"=> "4",
			"inblock" => "advert",
			"type" => "text",
			"std" => "",
		),

		array(
			"name" => __("If you want to use advert code (google, amazon / any html) insert it here rather than an image", 'bp-daily'),
			"id" => $shortname . $shortprefix . "sideleft_advert_code",
			"box"=> "4",
			"inblock" => "advert",
			"type" => "textarea",
			"std" => "",
		),

		array(
			"name" => __("Insert your sidebar right advert full url here<br /><em>*you can upload your logo in <a href='media-new.php'>media panel</a> and copy paste the url here</em> (size is maximum 160px wide but centres if lower)", 'bp-daily'),
			"id" => $shortname . $shortprefix . "sideright_advert",
			"box"=> "4",
			"inblock" => "advert",
			"type" => "text",
			"std" => "",
		),

		array(
			"name" => __("Give your sidebar right advert a title", 'bp-daily'),
			"id" => $shortname . $shortprefix . "sideright_advert_title",
			"box"=> "4",
			"inblock" => "advert",
			"type" => "text",
			"std" => "",
		),

		array(
			"name" => __("If you want your sidebar right advert to link somewhere please give the full url here", 'bp-daily'),
			"id" => $shortname . $shortprefix . "sideright_advert_link",
			"box"=> "4",
			"inblock" => "advert",
			"type" => "text",
			"std" => "",
		),

		array(
			"name" => __("If you want to use advert code (google, amazon / any html) insert it here rather than an image", 'bp-daily'),
			"id" => $shortname . $shortprefix . "sideright_advert_code",
			"box"=> "4",
			"inblock" => "advert",
			"type" => "textarea",
			"std" => "",
		),
		
		array("name" => __("Do you to use a custom large image logo rather than domain name text?<br /><em>*Enter your url in the next section if saying yes</em>", 'bp-daily'),
			"id" => $shortname . $shortprefix . "header_image",	     	
			"box"=> "5",
			"inblock" => "header",
			"type" => "select",
			"std" => "Select",
			"options" => array("yes", "no")),

		array(
			"name" => __("Insert your logo full url here<br /><em>*you can upload your logo in <a href='media-new.php'>media panel</a> and copy paste the url here</em>", 'bp-daily'),
			"id" => $shortname . $shortprefix . "header_logo",
			"box"=> "5",
			"inblock" => "header",
			"type" => "text",
			"std" => "",
		),

		array("name" => __("Do you to use a custom square image logo and your domain name text?<br /><em>*Enter your url in the next section if saying yes</em>", 'bp-daily'),
			"id" => $shortname . $shortprefix . "header_image_square",	     	
			"box"=> "5",
			"inblock" => "header",
			"type" => "select",
			"std" => "Select",
			"options" => array("yes", "no")),

		array(
			"name" => __("Insert your square logo full url here<br /><em>*you can upload your logo in <a href='media-new.php'>media panel</a> and copy paste the url here</em>", 'bp-daily'),
			"id" => $shortname . $shortprefix . "header_logo_square",
			"box"=> "5",
			"inblock" => "header",
			"type" => "text",
			"std" => "",
		),

		array(
			"name" => __("Enter a site title", 'bp-daily'),
			"id" => $shortname . $shortprefix . "header_title",
			"box"=> "5",
			"inblock" => "header",
			"type" => "textarea",
			"std" => "Your site",
		),

	array(
		"name" => __("Enter a site message", 'bp-daily'),
		"id" => $shortname . $shortprefix . "header_message",
		"box"=> "5",
		"inblock" => "header",
		"type" => "textarea",
		"std" => "Your site",
	),

);

function buddydaily_admin_panel() {
	echo "<div id=\"admin-options\">";
    global $themename, $shortname, $options;

	if ( isset($_REQUEST['saved']) ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings saved.</strong></p></div>';
	if ( isset($_REQUEST['reset']) ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings reset.</strong></p></div>';
	?>

	<h4><?php echo "$themename"; ?> <?php _e('Theme Options', 'bp-daily'); ?></h4>

	<form action="" method="post">

	<?php if( $value['box'] = '1' ) {  ?>

	<div class="get-option">
	<h2><?php _e('Home page featured content settings', 'bp-daily') ?></h2>

	<?php foreach ($options as $value) { ?>

	<!-- if text box -->
	<?php if (($value['inblock'] == "featured") && ($value['type'] == "text")) { ?>
	<div class="option-save">
	<div class="description"><?php echo $value['name']; ?></div>
	<div class="option-box">
		<p><input name="<?php echo $value['id']; ?>" class="myfield" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" /></p>
	</div>
	</div>

	<!-- if text area -->
	<?php } elseif (($value['inblock'] == "featured") && ($value['type'] == "textarea")) { ?>

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
	<?php } elseif (($value['inblock'] == "featured") && ($value['type'] == "colorpicker") ) {?>
	<div class="option-save">
	<div class="description"><?php echo $value['name']; ?></div>
	<?php $i = ""; $i == $i++ ; ?>
	<div class="option-box">
		<p><input class="color {required:false,hash:true}" name="<?php echo $value['id']; ?>" id="colorpickerField<?php echo $i; ?>" type="text" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" />
	</p>
	</div>
	</div>

	<!-- if select -->
	<?php } elseif (($value['inblock'] == "featured") && ($value['type'] == "select") ) {  ?>
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
	<h2><?php _e('Home page content settings', 'bp-daily') ?></h2>

	<?php foreach ($options as $value) { ?>

	<!-- if text box -->
	<?php if (($value['inblock'] == "content") && ($value['type'] == "text")) { ?>
	<div class="option-save">
	<div class="description"><?php echo $value['name']; ?></div>
	<div class="option-box">
		<p><input name="<?php echo $value['id']; ?>" class="myfield" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" /></p>
	</div>
	</div>

	<!-- if text area -->
	<?php } elseif (($value['inblock'] == "content") && ($value['type'] == "textarea")) { ?>

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
	<?php } elseif (($value['inblock'] == "content") && ($value['type'] == "colorpicker") ) {?>
	<div class="option-save">
	<div class="description"><?php echo $value['name']; ?></div>
	<?php $i = ""; $i == $i++ ; ?>
	<div class="option-box">
		<p><input class="color {required:false,hash:true}" name="<?php echo $value['id']; ?>" id="colorpickerField<?php echo $i; ?>" type="text" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" />
	</p>
	</div>
	</div>

	<!-- if select -->
	<?php } elseif (($value['inblock'] == "content") && ($value['type'] == "select") ) {  ?>
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
	<h2><?php _e('Spotlight settings', 'bp-daily') ?></h2>

	<?php foreach ($options as $value) { ?>

	<!-- if text box -->
	<?php if (($value['inblock'] == "spotlight") && ($value['type'] == "text")) { ?>
	<div class="option-save">
	<div class="description"><?php echo $value['name']; ?></div>
	<div class="option-box">
		<p><input name="<?php echo $value['id']; ?>" class="myfield" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" /></p>
	</div>
	</div>

	<!-- if text area -->
	<?php } elseif (($value['inblock'] == "spotlight") && ($value['type'] == "textarea")) { ?>

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
	<?php } elseif (($value['inblock'] == "spotlight") && ($value['type'] == "colorpicker") ) {?>
	<div class="option-save">
	<div class="description"><?php echo $value['name']; ?></div>
	<?php $i = ""; $i == $i++ ; ?>
	<div class="option-box">
		<p><input class="color {required:false,hash:true}" name="<?php echo $value['id']; ?>" id="colorpickerField<?php echo $i; ?>" type="text" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" />
	</p>
	</div>
	</div>

	<!-- if select -->
	<?php } elseif (($value['inblock'] == "spotlight") && ($value['type'] == "select") ) {  ?>
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
	<h2><?php _e('Advert settings', 'bp-daily') ?></h2>

	<?php foreach ($options as $value) { ?>

	<!-- if text box -->
	<?php if (($value['inblock'] == "advert") && ($value['type'] == "text")) { ?>
	<div class="option-save">
	<div class="description"><?php echo $value['name']; ?></div>
	<div class="option-box">
		<p><input name="<?php echo $value['id']; ?>" class="myfield" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" /></p>
	</div>
	</div>

	<!-- if text area -->
	<?php } elseif (($value['inblock'] == "advert") && ($value['type'] == "textarea")) { ?>

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
	<?php } elseif (($value['inblock'] == "advert") && ($value['type'] == "colorpicker") ) {?>
	<div class="option-save">
	<div class="description"><?php echo $value['name']; ?></div>
	<?php $i = ""; $i == $i++ ; ?>
	<div class="option-box">
		<p><input class="color {required:false,hash:true}" name="<?php echo $value['id']; ?>" id="colorpickerField<?php echo $i; ?>" type="text" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" />
	</p>
	</div>
	</div>

	<!-- if select -->
	<?php } elseif (($value['inblock'] == "advert") && ($value['type'] == "select") ) {  ?>
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
	<h2><?php _e('Header area settings *not custom header', 'bp-daily') ?></h2>

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
	<input name="save" type="submit" class="sbutton" value="<?php echo esc_attr(__('Save Options', 'bp-daily')); ?>" />
	<input type="hidden" name="theme_action" value="save" />
	</p>
	</form>

	<form method="post">
	<p class="save-p">
	<input name="reset" type="submit" class="sbutton" value="<?php echo esc_attr(__('Reset Options', 'bp-daily')); ?>" />
	<input type="hidden" name="theme_action" value="reset" />
	</p>

	</form>
	</div>

<?php
}
function buddydaily_admin_register() {
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
		add_theme_page(_g ($themename . __(' Theme Options', 'bp-daily')),  _g (__('Theme Options', 'bp-daily')),  'edit_theme_options', 'functions.php', 'buddydaily_admin_panel');
}

function buddydaily_admin_head() { ?>
	<link href="<?php bloginfo('template_directory'); ?>/library/options/custom-admin.css" rel="stylesheet" type="text/css" />
<?php if(isset($_GET["page"]) == "styling-functions.php") { ?>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/library/scripts/jscolor.js"></script>
<?php } ?>
<?php }

/* Preset Styling section */
/* stylesheet additiond */
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

	array(  "name" => __("Choose Your BuddyPress daily Preset Style:", 'bp-daily'),
		  	"id" => $shortname. $shortprefix . "custom_style",
			"std" => "default.css",
			"type" => "radio",
			"options" => $alt_stylesheets)
	);

function buddydaily_ready_style_admin_panel() {
		echo "<div id=\"admin-options\">";

		global $themename, $shortname, $options2;

		if ( isset($_REQUEST['saved2'] )) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings saved.</strong></p></div>';
		if ( isset($_REQUEST['reset2'] )) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings reset.</strong></p></div>';
	?>

	<h4><?php echo "$themename"; ?> <?php _e('Choose your BP studio Preset Style', 'bp-daily'); ?></h4>
	<h2><?php _e('If you want to customise the theme options you MUST have default.css selected'); ?></h2>
	<form action="" method="post">
	<div class="get-listings">
	<h2><?php _e("Style Select:", 'bp-daily') ?></h2>
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
			<input name="save2" type="submit" class="sbutton" value="<?php echo esc_attr(__('Save Options', 'bp-daily')); ?>" />
			<input type="hidden" name="theme_action2" value="save2" />
		</p>
	</form>

	<form method="post">
		<p class="save-p">
			<input name="reset2" type="submit" class="sbutton" value="<?php echo esc_attr(__('Reset Options', 'bp-daily')); ?>" />
			<input type="hidden" name="theme_action2" value="reset2" />
		</p>
	</form>
	</div>

	<?php }

function buddydaily_ready_style_admin_register() {
	global $themename, $shortname, $options2;
		$action2 = isset($_REQUEST['theme_action2']);
	if ( isset($_GET['page']) == 'buddydaily-themes.php' ) {
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
			header("Location: themes.php?page=buddydaily-themes.php&saved2=true");
			die;
		} 
		else if( 'reset2' == $action2 ) {
			foreach ($options2 as $value) {
				delete_option( $value['id'] ); 
			}
			header("Location: themes.php?page=buddydaily-themes.php&reset2=true");
			die;
		}
	}
	add_theme_page(_g (__('BuddyPress daily Preset Style', 'bp-daily')),  _g (__('Preset Style', 'bp-daily')),  'edit_theme_options', 'buddydaily-themes.php', 'buddydaily_ready_style_admin_panel');
}

$options3 = array (

array(
	"name" => __("Choose your body font", 'bp-daily'),
	"box" => "1",
	"id" => $shortname . $shortprefix . "body_font",
	"type" => "select",
	"inblock" => "headers",
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
	"name" => __("Choose your header font", 'bp-daily'),
	"box" => "1",
	"id" => $shortname . $shortprefix . "header_font",
	"type" => "select",
	"inblock" => "headers",
	"std" => "Georgia, sans-serif",
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
	"name" => __("Choose your h1 colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "h1_colour",
	"box" => "1",
	"inblock" => "headers",
	"std" => "",
	"type" => "colorpicker"),
	
array(
	"name" => __("Choose your h2 colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "h2_colour",
	"box" => "1",
	"inblock" => "headers",
	"std" => "",
	"type" => "colorpicker"),


array(
	"name" => __("Choose your h3 colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "h3_colour",
	"box" => "1",
	"inblock" => "headers",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your h4 colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "h4_colour",
	"box" => "1",
	"inblock" => "headers",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your h5 colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "h5_colour",
	"box" => "1",
	"inblock" => "headers",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your h6 colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "h6_colour",
	"box" => "1",
	"inblock" => "headers",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your post and page title header background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "header_background_colour",
	"box" => "1",
	"inblock" => "headers",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your post and page title border colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "header_feature_border_colour",
	"box" => "1",
	"inblock" => "headers",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your post and page title colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "header_feature_colour",
	"box" => "1",
	"inblock" => "headers",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your button / submit border colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "button_border",
	"box" => "2",
	"inblock" => "forms",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your button / submit background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "button_background_colour",
	"box" => "2",
	"inblock" => "forms",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your button / submit text colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "button_colour",
	"box" => "2",
	"inblock" => "forms",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Insert your <strong>button background image</strong> full url here<br /><em>*you can upload your image in <a href='media-new.php'>media panel</a> and paste the url here.</em>", 'bp-daily'),
	"id" => $shortname . $shortprefix . "button_background_image",
	"box" => "2",
	"inblock" => "forms",
	"std" => "",
	"type" => "text"),

array(
	"name" => __("Button background image repeat", 'bp-daily'),
	"id" => $shortname . $shortprefix . "button_image_repeat",	
	"box" => "2",
	"inblock" => "forms",
	"type" => "select",
	"std" => "repeat-x",
	"options" => array("no-repeat", "repeat", "repeat-x", "repeat-y")),

array(
	"name" => __("Choose your button / submit hover border colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "button_hover_border_colour",
	"box" => "2",
	"inblock" => "forms",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your button / submit hover background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "button_hover_background_colour",
	"box" => "2",
	"inblock" => "forms",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your button / submit hover text colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "button_hover_colour",
	"box" => "2",
	"inblock" => "forms",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Insert your <strong>button hover background image</strong> full url here<br /><em>*you can upload your image in <a href='media-new.php'>media panel</a> and paste the url here.</em>", 'bp-daily'),
	"id" => $shortname . $shortprefix . "button_hover_background_image",
	"box" => "2",
	"inblock" => "forms",
	"std" => "",
	"type" => "text"),

array(
	"name" => __("Button hover background image repeat", 'bp-daily'),
	"id" => $shortname . $shortprefix . "button_hover_image_repeat",	
	"box" => "2",
	"inblock" => "forms",
	"type" => "select",
		"std" => "",
	"options" => array("no-repeat", "repeat", "repeat-x", "repeat-y")),

array(
	"name" => __("Choose your input border colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "input_border_colour",
	"box" => "2",
	"inblock" => "forms",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your input background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "input_background_colour",
	"box" => "2",
	"inblock" => "forms",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your input text colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "input_text_colour",
	"box" => "2",
	"inblock" => "forms",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your label colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "label_colour",
	"box" => "2",
	"inblock" => "forms",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your textarea background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "textarea_background_colour",
	"box" => "2",
	"inblock" => "forms",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your textarea border colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "textarea_border_colour",
	"box" => "2",
	"inblock" => "forms",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your textarea text colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "textarea_text_colour",
	"box" => "2",
	"inblock" => "forms",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("What's new form border colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "whats_border_colour",
	"box" => "2",
	"inblock" => "forms",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your link colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "link_colour",
	"box" => "3",
	"inblock" => "text",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your link hover colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "link_hover_colour",
	"box" => "3",
	"inblock" => "text",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your link visited colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "link_visited_colour",
	"box" => "3",
	"inblock" => "text",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your byline text colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "byline_colour",
	"box" => "3",
	"inblock" => "text",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your pre / code text colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "pre_colour",
	"box" => "3",
	"inblock" => "text",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your hr colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "hr_colour",
	"box" => "3",
	"inblock" => "text",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your pre / code background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "pre_background_colour",
	"box" => "3",
	"inblock" => "text",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your body text colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "body_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your body background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "body_background_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Insert your <strong>body background image</strong> full url here<br /><em>*you can upload your image in <a href='media-new.php'>media panel</a> and paste the url here.</em>", 'bp-daily'),
	"id" => $shortname . $shortprefix . "body_background_image",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "text"),

array(
	"name" => __("Body background image repeat", 'bp-daily'),
	"box" => "4",
	"id" => $shortname . $shortprefix . "body_image_repeat",
	"type" => "select",
	"inblock" => "layout",
		"std" => "",
	"options" => array(
	"no-repeat", "repeat", "repeat-x", "repeat-y"
	)
),

array(
	"name" => __("Choose your load more background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "load_background_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your load more border colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "load_border_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your load more text colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "load_text_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your alternative / first child background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "child_background_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your alternative / first child text colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "child_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your alternative / first child border colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "child_border_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your alternative / first child hover background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "child_hover_background_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your alternative / first child hover text colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "child_hover_text_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your image border colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "image_border_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your information bar text colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "information_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your activity comments text colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "activity_comments_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your activity comments background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "activity_comments_background_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your activity comments border colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "activity_comments_border_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your activity buttons background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "activity_background_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your activity buttons text colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "activity_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your activity buttons border", 'bp-daily'),
	"id" => $shortname . $shortprefix . "activity_border_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your activity buttons hover background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "activity_hover_background_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your activity buttons hover text colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "activity_hover_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your activity buttons hover border", 'bp-daily'),
	"id" => $shortname . $shortprefix . "activity_hover_border_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your invite list background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "invite_background_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your invite list border colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "invite_border_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your invite list text colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "invite_text_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your item list border colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "item_border_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your author box background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "author_box_background_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your author box image border colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "author_box_border_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your post meta/ widget error background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "widget_background_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your post meta/ widget text colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "widget_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your post meta/ widget border colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "widget_border_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your caption background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "caption_background_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your caption border colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "caption_border_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your spotlight posts background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "spotlight_background_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your spotlight posts text colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "spotlight_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your spotlight posts border colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "spotlight_border_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your widget title background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "widget_title_background_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your widget title colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "widget_title_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your site wrapper background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "wrapper_background_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your site wrapper border colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "wrapper_border_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your pagination border colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "pagination_border_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your column content block border colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "column_border_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your container background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "container_background_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your container colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "container_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your dark container background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "dark_background_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your dark container border colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "dark_border_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your dark container text colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "dark_text_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),
	
array(
	"name" => __("Choose your light container background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "light_background_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your light container border colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "light_border_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your light container text colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "light_text_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your sidebar item options background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "sidebar_item_background_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your sidebar item options text colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "sidebar_item_colour",
	"box" => "4",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your breadcrumb background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "breadcrumb_background_colour",
	"box" => "5",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your breadcrumb border top colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "breadcrumb_border_colour",
	"box" => "5",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your breadcrumb text colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "breadcrumb_colour",
	"box" => "5",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your category navigation background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "category_background_colour",
	"box" => "5",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your category navigation border top colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "category_border_colour",
	"box" => "5",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your category menu border right colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "category_menu_border_colour",
	"box" => "5",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your category menu background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "category_menu_background_colour",
	"box" => "5",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your category menu text colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "category_menu_colour",
	"box" => "5",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your category menu selected/hover background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "category_menu_selected_background_colour",
	"box" => "5",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your category menu selected/hover text colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "category_menu_selected_colour",
	"box" => "5",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your pagination navigation background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "pagination_background_colour",
	"box" => "5",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your pagination navigation border top colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "pagination_border_colour",
	"box" => "5",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your pagination menu border right colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "pagination_menu_border_colour",
	"box" => "5",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your pagination menu background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "pagination_menu_background_colour",
	"box" => "5",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your pagination menu text colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "pagination_menu_colour",
	"box" => "5",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your pagination menu selected/hover background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "pagination_menu_selected_background_colour",
	"box" => "5",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your pagination menu selected/hover text colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "pagination_menu_selected_colour",
	"box" => "5",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your highlight h2 background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "highlight_background_colour",
	"box" => "5",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your highlight h2 border colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "highlight_border_colour",
	"box" => "5",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your highlight h2 text colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "highlight_colour",
	"box" => "5",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your item list tabs background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "tabs_background_colour",
	"box" => "5",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your item list tabs text colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "tabs_text_colour",
	"box" => "5",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your item list tabs selected background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "tabs_selected_background_colour",
	"box" => "5",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your item list tabs selected text colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "tabs_selected_colour",
	"box" => "5",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your item list tabs sub navigation background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "tabs_sub_background_colour",
	"box" => "5",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your item list tabs sub navigation border colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "tabs_sub_border_colour",
	"box" => "5",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your item list tabs sub text colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "tabs_sub_colour",
	"box" => "5",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your item list unread text colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "tabs_unread_colour",
	"box" => "5",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your footer navigation background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "footer_background_colour",
	"box" => "5",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your footer navigation text colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "footer_colour",
	"box" => "5",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your footer navigation link colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "footer_link_colour",
	"box" => "5",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your table border colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "table_border_colour",
	"box" => "6",
	"inblock" => "messages",
	"std" => "",
	"type" => "colorpicker"),
// from here ended last night ////////////////////////////////////////////////////////////////////
array(
	"name" => __("Choose your alternative row background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "alt_background_colour",
	"box" => "6",
	"inblock" => "messages",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your alternative row text colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "alt_row_colour",
	"box" => "6",
	"inblock" => "messages",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your sticky background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "sticky_background_colour",
	"box" => "6",
	"inblock" => "messages",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your sticky text colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "sticky_colour",
	"box" => "6",
	"inblock" => "messages",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your unread background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "unread_background_colour",
	"box" => "6",
	"inblock" => "messages",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your unread border colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "unread_border_colour",
	"box" => "6",
	"inblock" => "messages",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your unread text colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "unread_colour",
	"box" => "6",
	"inblock" => "messages",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your pending border colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "pending_border_colour",
	"box" => "6",
	"inblock" => "messages",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your pending text colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "pending_colour",
	"box" => "6",
	"inblock" => "messages",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your pending hover border colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "pending_hover_border_colour",
	"box" => "6",
	"inblock" => "messages",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your pending hover text colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "pending_hover_colour",
	"box" => "6",
	"inblock" => "messages",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your messages background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "messages_background_colour",
	"box" => "6",
	"inblock" => "messages",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your messages border colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "messages_border_colour",
	"box" => "6",
	"inblock" => "messages",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your messages text colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "messages_colour",
	"box" => "6",
	"inblock" => "messages",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your updated background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "updated_background_colour",
	"box" => "6",
	"inblock" => "messages",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your updated border colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "updated_border_colour",
	"box" => "6",
	"inblock" => "messages",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your updated text colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "updated_text_colour",
	"box" => "6",
	"inblock" => "messages",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your message options background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "message_options_background_colour",
	"box" => "6",
	"inblock" => "messages",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your message options text colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "nessage_options_colour",
	"box" => "6",
	"inblock" => "messages",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your error background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "error_background_colour",
	"box" => "6",
	"inblock" => "messages",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your error border colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "error_border_colour",
	"box" => "6",
	"inblock" => "messages",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your error text colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "error_colour",
	"box" => "6",
	"inblock" => "messages",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your messages alternate background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "messages_alt_background_colour",
	"box" => "6",
	"inblock" => "messages",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your messages alternate text colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "messages_alt_colour",
	"box" => "6",
	"inblock" => "messages",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your topic alternative background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "topic_alt_background_colour",
	"box" => "6",
	"inblock" => "messages",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your topic alternative text colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "topic_alt_colour",
	"box" => "6",
	"inblock" => "messages",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your unread count background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "unread_background_colour",
	"box" => "6",
	"inblock" => "messages",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your unread text colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "unread_colour",
	"box" => "6",
	"inblock" => "messages",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your slideshow background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "slideshow_background_colour",
	"box" => "7",
	"inblock" => "featured",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your slideshow content background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "slideshow_content_background_colour",
	"box" => "7",
	"inblock" => "featured",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your slideshow image background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "slideshow_image_background_colour",
	"box" => "7",
	"inblock" => "featured",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your slideshow text colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "slideshow_colour",
	"box" => "7",
	"inblock" => "featured",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your content tabs first background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "tabs_first_background_colour",
	"box" => "7",
	"inblock" => "featured",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your content tabs first border colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "tabs_first_colour",
	"box" => "7",
	"inblock" => "featured",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your content tabs second background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "tabs_second_background_colour",
	"box" => "7",
	"inblock" => "featured",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your content tabs second border colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "tabs_second_colour",
	"box" => "7",
	"inblock" => "featured",
	"std" => "",
	"type" => "colorpicker"),


array(
	"name" => __("Choose your content tabs third background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "tabs_third_background_colour",
	"box" => "7",
	"inblock" => "featured",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your content tabs third border colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "tabs_third_colour",
	"box" => "7",
	"inblock" => "featured",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your content tabs navigation background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "tabs_nav_background_colour",
	"box" => "7",
	"inblock" => "featured",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your content tabs navigation border colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "tabs_nav_border_colour",
	"box" => "7",
	"inblock" => "featured",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your content tabs navigation link text colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "tabs_nav_link_colour",
	"box" => "7",
	"inblock" => "featured",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your content tabs navigation link background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "tabs_nav_link_background_colour",
	"box" => "7",
	"inblock" => "featured",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your content tabs selected background colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "tabs_selected_background_colour",
	"box" => "7",
	"inblock" => "featured",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your content tabs selected text colour", 'bp-daily'),
	"id" => $shortname . $shortprefix . "tabs_selected_colour",
	"box" => "7",
	"inblock" => "featured",
	"std" => "",
	"type" => "colorpicker"),

);

function buddydaily_custom_style_admin_panel() {
	echo "<div id=\"admin-options\">";
	
	global $themename, $shortname, $options3;
	
	if ( isset($_REQUEST['saved3']) ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings saved.</strong></p></div>';
	if ( isset($_REQUEST['reset3'] )) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings reset.</strong></p></div>';
?>

<h4><?php echo "$themename"; ?> <?php _e('Custom styling', 'bp-daily'); ?></h4>

<h2><?php _e('If you want to customise the theme options you MUST have default.css selected'); ?></h2>
<form action="" method="post">


<?php if( $value['box'] = '1' ) {  ?>
	
<div class="get-option">
<h2><?php _e('Headers styling', 'bp-daily') ?></h2>

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
	<h2><?php _e('Forms and buttons styling', 'bp-daily') ?></h2>

	<?php foreach ($options3 as $value) { ?>

		<!-- if text box -->
		<?php if (($value['inblock'] == "forms") && ($value['type'] == "text")) { ?>
		<div class="option-save">
		<div class="description"><?php echo $value['name']; ?></div>
		<div class="option-box">
			<p><input name="<?php echo $value['id']; ?>" class="myfield" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" /></p>
		</div>
		</div>

		<!-- if text area -->
		<?php } elseif (($value['inblock'] == "forms") && ($value['type'] == "textarea")) { ?>

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
		<?php } elseif (($value['inblock'] == "forms") && ($value['type'] == "colorpicker") ) {?>
		<div class="option-save">
		<div class="description"><?php echo $value['name']; ?></div>
		<?php $i = ""; $i == $i++ ; ?>
		<div class="option-box">
			<p><input class="color {required:false,hash:true}" name="<?php echo $value['id']; ?>" id="colorpickerField<?php echo $i; ?>" type="text" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" />
		</p>
		</div>
		</div>

		<!-- if select -->
		<?php } elseif (($value['inblock'] == "forms") && ($value['type'] == "select") ) {  ?>
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
		<h2><?php _e('Text and links styling', 'bp-daily') ?></h2>

		<?php foreach ($options3 as $value) { ?>

			<!-- if text box -->
			<?php if (($value['inblock'] == "text") && ($value['type'] == "text")) { ?>
			<div class="option-save">
			<div class="description"><?php echo $value['name']; ?></div>
			<div class="option-box">
				<p><input name="<?php echo $value['id']; ?>" class="myfield" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" /></p>
			</div>
			</div>

			<!-- if text area -->
			<?php } elseif (($value['inblock'] == "text") && ($value['type'] == "textarea")) { ?>

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
			<?php } elseif (($value['inblock'] == "text") && ($value['type'] == "colorpicker") ) {?>
			<div class="option-save">
			<div class="description"><?php echo $value['name']; ?></div>
			<?php $i = ""; $i == $i++ ; ?>
			<div class="option-box">
				<p><input class="color {required:false,hash:true}" name="<?php echo $value['id']; ?>" id="colorpickerField<?php echo $i; ?>" type="text" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" />
			</p>
			</div>
			</div>

			<!-- if select -->
			<?php } elseif (($value['inblock'] == "text") && ($value['type'] == "select") ) {  ?>
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
			<h2><?php _e('Layout styling', 'bp-daily') ?></h2>

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
				<h2><?php _e('Navigation styling', 'bp-daily') ?></h2>

				<?php foreach ($options3 as $value) { ?>

					<!-- if text box -->
					<?php if (($value['inblock'] == "navigation") && ($value['type'] == "text")) { ?>
					<div class="option-save">
					<div class="description"><?php echo $value['name']; ?></div>
					<div class="option-box">
						<p><input name="<?php echo $value['id']; ?>" class="myfield" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" /></p>
					</div>
					</div>

					<!-- if text area -->
					<?php } elseif (($value['inblock'] == "navigation") && ($value['type'] == "textarea")) { ?>

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
					<?php } elseif (($value['inblock'] == "navigation") && ($value['type'] == "colorpicker") ) {?>
					<div class="option-save">
					<div class="description"><?php echo $value['name']; ?></div>
					<?php $i = ""; $i == $i++ ; ?>
					<div class="option-box">
						<p><input class="color {required:false,hash:true}" name="<?php echo $value['id']; ?>" id="colorpickerField<?php echo $i; ?>" type="text" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" />
					</p>
					</div>
					</div>

					<!-- if select -->
					<?php } elseif (($value['inblock'] == "navigation") && ($value['type'] == "select") ) {  ?>
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
					<?php if( $value['box'] = '6' ) {  ?>

					<div class="get-option">
					<h2><?php _e('Messages styling', 'bp-daily') ?></h2>

					<?php foreach ($options3 as $value) { ?>

						<!-- if text box -->
						<?php if (($value['inblock'] == "messages") && ($value['type'] == "text")) { ?>
						<div class="option-save">
						<div class="description"><?php echo $value['name']; ?></div>
						<div class="option-box">
							<p><input name="<?php echo $value['id']; ?>" class="myfield" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" /></p>
						</div>
						</div>

						<!-- if text area -->
						<?php } elseif (($value['inblock'] == "messages") && ($value['type'] == "textarea")) { ?>

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
						<?php } elseif (($value['inblock'] == "messages") && ($value['type'] == "colorpicker") ) {?>
						<div class="option-save">
						<div class="description"><?php echo $value['name']; ?></div>
						<?php $i = ""; $i == $i++ ; ?>
						<div class="option-box">
							<p><input class="color {required:false,hash:true}" name="<?php echo $value['id']; ?>" id="colorpickerField<?php echo $i; ?>" type="text" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" />
						</p>
						</div>
						</div>

						<!-- if select -->
						<?php } elseif (($value['inblock'] == "messages") && ($value['type'] == "select") ) {  ?>
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
						<?php if( $value['box'] = '7' ) {  ?>

						<div class="get-option">
						<h2><?php _e('Featured content styling', 'bp-daily') ?></h2>

						<?php foreach ($options3 as $value) { ?>

							<!-- if text box -->
							<?php if (($value['inblock'] == "featured") && ($value['type'] == "text")) { ?>
							<div class="option-save">
							<div class="description"><?php echo $value['name']; ?></div>
							<div class="option-box">
								<p><input name="<?php echo $value['id']; ?>" class="myfield" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" /></p>
							</div>
							</div>

							<!-- if text area -->
							<?php } elseif (($value['inblock'] == "featured") && ($value['type'] == "textarea")) { ?>

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
							<?php } elseif (($value['inblock'] == "featured") && ($value['type'] == "colorpicker") ) {?>
							<div class="option-save">
							<div class="description"><?php echo $value['name']; ?></div>
							<?php $i = ""; $i == $i++ ; ?>
							<div class="option-box">
								<p><input class="color {required:false,hash:true}" name="<?php echo $value['id']; ?>" id="colorpickerField<?php echo $i; ?>" type="text" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" />
							</p>
							</div>
							</div>

							<!-- if select -->
							<?php } elseif (($value['inblock'] == "featured") && ($value['type'] == "select") ) {  ?>
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
	<input name="save3" type="submit" class="sbutton" value="<?php echo esc_attr(__('Save Options', 'bp-daily')); ?>" />
	<input type="hidden" name="theme_action3" value="save3" />
	</p>
	</form>

	<form method="post">
	<p class="save-p">
	<input name="reset3" type="submit" class="sbutton" value="<?php echo esc_attr(__('Reset Options', 'bp-daily')); ?>" />
	<input type="hidden" name="theme_action3" value="reset3" />
	</p>

	</form>
	</div>

	<?php
}

function buddydaily_custom_style_admin_register() {
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
	
			add_theme_page(_g ($themename . __('Custom styling', 'bp-daily')),  _g (__('Custom Styling', 'bp-daily')),  'edit_theme_options', 'styling-functions.php', 'buddydaily_custom_style_admin_panel');
	}


add_action('admin_head', 'buddydaily_admin_head');
add_action('admin_menu', 'buddydaily_admin_register');
add_action('admin_menu', 'buddydaily_ready_style_admin_register');
add_action('admin_menu', 'buddydaily_custom_style_admin_register');


?>