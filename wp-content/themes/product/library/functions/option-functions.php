<?php
function _g($str)
{
return __($str, 'option-page');
}

$themename = "product";
$themeversion = "1";
$shortname = "dev";
$shortprefix = "_product_";
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
	array("name" => __("Show slideshow block?", 'product'),
		"description" => __("You can show or hide the slideshow block, the default is off", 'product'),
		"id" => $shortname . $shortprefix . "slideshow",	     	
		"inblock" => "slideone",
	    "type" => "select",
		"std" => "Show",
		"options" => array("yes", "no")),
	
	array("name" => __("How many slides do you want?", 'product'),
		"id" => $shortname . $shortprefix . "slide_number",	     	
		"inblock" => "slideone",
	    "type" => "select",
		"std" => "Select your slideshow contents type",
		"options" => array("1", "2", "3", "4")),
		
	array("name" => __("Pick your slide one contents", 'product'),
		"id" => $shortname . $shortprefix . "slideone_type",	     	
		"inblock" => "slideone",
	    "type" => "select",
		"std" => "Select your slideshow contents type",
		"options" => array("video and text", "image and text", "image", "text", "video")),


		array(
			"name" => __("Enter the slide one height", 'product'),
				"description" => __("300px is the default - all other slides will auto size", 'product'),
			"id" => $shortname . $shortprefix . "slideone_height",
	"inblock" => "slideone",
			"type" => "text",
			"std" => "",
		),

	array(
		"name" => __("Enter the slide one title", 'product'),
		"id" => $shortname . $shortprefix . "slideone_title",
	"inblock" => "slideone",
		"type" => "text",
		"std" => "",
	),


array(
	"name" => __("Enter an image url if using an image", 'product'),
	"description" => __("Large images are 900px wide maximum, small any size but float right.  You can upload your image in the <a href='media-new.php'>media panel</a> and copy paste the url here", 'product'),
	"id" => $shortname . $shortprefix . "slideone_image",
	"inblock" => "slideone",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("Enter a link for your image to lead to", 'product'),
	"id" => $shortname . $shortprefix . "slideone_image_link",
	"inblock" => "slideone",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("Enter an alt title for your image", 'product'),
	"id" => $shortname . $shortprefix . "slideone_image_title",
	"inblock" => "slideone",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("Enter your embed code if using video", 'product'),
	"id" => $shortname . $shortprefix . "slideone_small_video",
	"inblock" => "slideone",
	"type" => "textarea",
	"std" => "",
),

array(
	"name" => __("Enter header if using text", 'product'),
	"id" => $shortname . $shortprefix . "slideone_header",
	"inblock" => "slideone",
	"type" => "textarea",
	"std" => "",
),

array(
	"name" => __("Enter description if using text", 'product'),
	"id" => $shortname . $shortprefix . "slideone_description",
	"inblock" => "slideone",
	"type" => "textarea",
	"std" => "",
),

array(
	"name" => __("Enter a web link if you want to include one", 'product'),
	"id" => $shortname . $shortprefix . "slideone_link",
	"inblock" => "slideone",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("Give your link a title", 'product'),
	"id" => $shortname . $shortprefix . "slideone_link_title",
	"inblock" => "slideone",
	"type" => "text",
	"std" => "",
),

array("name" => __("Pick your slide two contents", 'product'),
	"id" => $shortname . $shortprefix . "slidetwo_type",	     	
	"inblock" => "slidetwo",
    "type" => "select",
	"std" => "Select your slideshow contents type",
	"options" => array("video and text", "image and text", "image", "text")),


	array(
		"name" => __("Enter the slide title", 'product'),
		"id" => $shortname . $shortprefix . "slidetwo_title",
	"inblock" => "slidetwo",
		"type" => "text",
		"std" => "",
	),

array(
	"name" => __("Enter an image url if using an image", 'product'),
	"description" => __("Large images are 900px wide maximum, small any size but float right.  You can upload your image in the <a href='media-new.php'>media panel</a> and copy paste the url here", 'product'),
	"id" => $shortname . $shortprefix . "slidetwo_image",
	"inblock" => "slidetwo",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("Enter a link for your image to lead to", 'product'),
	"id" => $shortname . $shortprefix . "slidetwo_image_link",
	"inblock" => "slidetwo",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("Enter an alt title for your image", 'product'),
	"id" => $shortname . $shortprefix . "slidetwo_image_title",
	"inblock" => "slidetwo",
	"type" => "text",
	"std" => "",
),


array(
"name" => __("Enter embed code if using video", 'product'),
"id" => $shortname . $shortprefix . "slidetwo_small_video",
	"inblock" => "slidetwo",
"type" => "textarea",
"std" => "",
),

array(
"name" => __("Enter header if using text", 'product'),
"id" => $shortname . $shortprefix . "slidetwo_header",
	"inblock" => "slidetwo",
"type" => "textarea",
"std" => "",
),

array(
"name" => __("Enter description if using text", 'product'),
"id" => $shortname . $shortprefix . "slidetwo_description",
	"inblock" => "slidetwo",
"type" => "textarea",
"std" => "",
),

array(
"name" => __("Enter website link if want to use one", 'product'),
"id" => $shortname . $shortprefix . "slidetwo_link",
	"inblock" => "slidetwo",
"type" => "text",
"std" => "",
),

array(
	"name" => __("Enter link title if using a link", 'product'),
	"id" => $shortname . $shortprefix . "slidetwo_link_title",
	"inblock" => "slidetwo",
	"type" => "text",
	"std" => "",
),

array("name" => __("Pick your slide three contents", 'product'),
	"id" => $shortname . $shortprefix . "slidethree_type",	     	
	"inblock" => "slidethree",
    "type" => "select",
	"std" => "Select your slideshow contents type",
	"options" => array("video and text", "image and text", "image", "text")),

	array(
		"name" => __("Enter the slide title", 'product'),
		"id" => $shortname . $shortprefix . "slidethree_title",
	"inblock" => "slidethree",
		"type" => "text",
		"std" => "",
	),

array(
	"name" => __("Enter the an image url if using an image", 'product'),
	"description" => __("Large images are 900px wide maximum, small any size but float right.  You can upload your image in the <a href='media-new.php'>media panel</a> and copy paste the url here", 'product'),
	"id" => $shortname . $shortprefix . "slidethree_image",
	"inblock" => "slidethree",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("Enter a link for your image to lead to", 'product'),
	"id" => $shortname . $shortprefix . "slidethree_image_link",
	"inblock" => "slidethree",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("Enter an alt title for your image", 'product'),
	"id" => $shortname . $shortprefix . "slidethree_image_title",
	"inblock" => "slidethree",
	"type" => "text",
	"std" => "",
),


array(
"name" => __("Enter embed code if using video", 'product'),
"id" => $shortname . $shortprefix . "slidethree_small_video",
	"inblock" => "slidethree",
"type" => "textarea",
"std" => "",
),

array(
"name" => __("Enter header title if using text", 'product'),
"id" => $shortname . $shortprefix . "slidethree_header",
	"inblock" => "slidethree",
"type" => "textarea",
"std" => "",
),

array(
"name" => __("Enter description if using text", 'product'),
"id" => $shortname . $shortprefix . "slidethree_description",
	"inblock" => "slidethree",
"type" => "textarea",
"std" => "",
),

array(
"name" => __("Enter website link if want to have one", 'product'),
"id" => $shortname . $shortprefix . "slidethree_link",
	"inblock" => "slidethree",
"type" => "text",
"std" => "",
),

array(
	"name" => __("Enter link title if having a link", 'product'),
	"id" => $shortname . $shortprefix . "slidethree_link_title",
	"inblock" => "slidethree",
	"type" => "text",
	"std" => "",
),

array("name" => __("Pick your slide four contents", 'product'),
	"id" => $shortname . $shortprefix . "slidefour_type",	     	
	"inblock" => "slidefour",
    "type" => "select",
	"std" => "Select your slideshow contents type",
	"options" => array("video and text", "image and text", "image", "text")),

	array(
		"name" => __("Enter the slide title", 'product'),
		"id" => $shortname . $shortprefix . "slidefour_title",
	"inblock" => "slidefour",
		"type" => "text",
		"std" => "",
	),

array(
	"name" => __("Enter the image url if using an image", 'product'),
	"description" => __("Large images are 900px wide maximum, small any size but float right.  You can upload your image in the <a href='media-new.php'>media panel</a> and copy paste the url here", 'product'),
	"id" => $shortname . $shortprefix . "slidefour_image",
	"inblock" => "slidefour",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("Enter a link for your image to lead to", 'product'),
	"id" => $shortname . $shortprefix . "slidefour_image_link",
	"inblock" => "slidefour",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("Enter an alt title for your image", 'product'),
	"id" => $shortname . $shortprefix . "slidefour_image_title",
	"inblock" => "slidefour",
	"type" => "text",
	"std" => "",
),


array(
"name" => __("Enter embed code if using video", 'product'),
"id" => $shortname . $shortprefix . "slidefour_small_video",
	"inblock" => "slidefour",
"type" => "textarea",
"std" => "",
),

array(
"name" => __("Enter header text if using text", 'product'),
"id" => $shortname . $shortprefix . "slidefour_header",
	"inblock" => "slidefour",
"type" => "textarea",
"std" => "",
),

array(
"name" => __("Enter description if using text", 'product'),
"id" => $shortname . $shortprefix . "slidefour_description",
	"inblock" => "slidefour",
"type" => "textarea",
"std" => "",
),

array(
"name" => __("Enter website link if want to include one", 'product'),
"id" => $shortname . $shortprefix . "slidefour_link",
	"inblock" => "slidefour",
"type" => "text",
"std" => "",
),
array(
	"name" => __("Enter link title if having a link", 'product'),
	"id" => $shortname . $shortprefix . "slidefour_link_title",
	"inblock" => "slidefour",
	"type" => "text",
	"std" => "",
),

array("name" => __("Show content block? (default is off)", 'product'),
	"id" => $shortname . $shortprefix . "contentshow",	     	
	"inblock" => "contentone",
    "type" => "select",
	"std" => "Turn on and off the content block",
	"options" => array("yes", "no")),
	
array(
	"name" => __("First block header", 'product'),
	"id" => $shortname . $shortprefix . "blockone_header",
	"inblock" => "contentone",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("First block description", 'product'),
	"id" => $shortname . $shortprefix . "blockone_description",
	"inblock" => "contentone",
	"type" => "textarea",
	"std" => "",
),

array(
	"name" => __("Add an image url here if you want to have an image showing", 'product'),
	"description" => __("280px wide maximum.  You can upload your image in the <a href='media-new.php'>media panel</a> and copy paste the url here", 'product'),
	"id" => $shortname . $shortprefix . "blockone_image",
	"inblock" => "contentone",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("Enter your image alt title here", 'product'),
	"id" => $shortname . $shortprefix . "blockone_image_title",
	"inblock" => "contentone",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("First block link", 'product'),
	"id" => $shortname . $shortprefix . "blockone_link",
	"inblock" => "contentone",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("First block link title", 'product'),
	"id" => $shortname . $shortprefix . "blockone_link_title",
	"inblock" => "contentone",
	"type" => "text",
	"std" => "",
),
array(
	"name" => __("Second block header", 'product'),
	"id" => $shortname . $shortprefix . "blocktwo_header",
	"inblock" => "contenttwo",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("Second block description", 'product'),
	"id" => $shortname . $shortprefix . "blocktwo_description",
	"inblock" => "contenttwo",
	"type" => "textarea",
	"std" => "",
),

array(
	"name" => __("Add an image url here if you want to have an image showing", 'product'),
	"description" => __("280px wide maximum.  You can upload your image in the <a href='media-new.php'>media panel</a> and copy paste the url here", 'product'),
	"id" => $shortname . $shortprefix . "blocktwo_image",
	"inblock" => "contenttwo",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("Enter your image alt title here", 'product'),
	"id" => $shortname . $shortprefix . "blocktwo_image_title",
	"inblock" => "contenttwo",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("Second block link", 'product'),
	"id" => $shortname . $shortprefix . "blocktwo_link",
	"inblock" => "contenttwo",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("Second block link title", 'product'),
	"id" => $shortname . $shortprefix . "blocktwo_link_title",
	"inblock" => "contenttwo",
	"type" => "text",
	"std" => "",
),
array(
	"name" => __("Third block header", 'product'),
	"id" => $shortname . $shortprefix . "blockthree_header",
	"inblock" => "contentthree",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("Third block description", 'product'),
	"id" => $shortname . $shortprefix . "blockthree_description",
	"inblock" => "contentthree",
	"type" => "textarea",
	"std" => "",
),

array(
	"name" => __("Add an image url here if you want to have an image showing", 'product'),
	"description" => __("280px wide maximum.  You can upload your image in the <a href='media-new.php'>media panel</a> and copy paste the url here", 'product'),
	"id" => $shortname . $shortprefix . "blockthree_image",
	"inblock" => "contentthree",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("Enter your image alt title here", 'product'),
	"id" => $shortname . $shortprefix . "blockthree_image_title",
	"inblock" => "contentthree",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("Third block link", 'product'),
	"id" => $shortname . $shortprefix . "blockthree_link",
	"inblock" => "contentthree",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("Third block link title", 'product'),
	"id" => $shortname . $shortprefix . "blockthree_link_title",
	"inblock" => "contentthree",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("Select the position of your BuddyPress navigation bar", 'product'),
	"description" => __("Default is top", 'product'),
	"id" => $shortname . $shortprefix . "nav_location",
	"inblock" => "buddypress",
   "type" => "select",
	"std" => "Select your slideshow contents type",
	"options" => array("top", "bottom")),

array(
	"name" => __("Do you want to have the sign up text section on?", 'product'),
	"id" => $shortname . $shortprefix . "signupfeat_on",
	"inblock" => "branding",
   "type" => "select",
	"options" => array("yes", "no")
),

array(
	"name" => __("Sign up text", 'product'),
	"id" => $shortname . $shortprefix . "signupfeat_text",
	"inblock" => "branding",
   "type" => "textarea",
	"options" => ""
),

	
	array(
		"name" => __("Enter some text for a button to click to sign up to your site", 'product'),
		"id" => $shortname . $shortprefix . "signupfeat_buttontext",
		"inblock" => "branding",
		"type" => "text",
		"std" => "",
	),
	
	array(
		"name" => __("Enter a custom page if you've created one for your login", 'product'),
		"id" => $shortname . $shortprefix . "signupfeat_buttontext_custom",
		"inblock" => "branding",
		"type" => "text",
		"std" => "",
	),

array(
	"name" => __("Site title", 'product'),
	"id" => $shortname . $shortprefix . "title",
	"inblock" => "branding",
   "type" => "text",
	"options" => ""
),

array(
"name" => __("Site description", 'product'),
"id" => $shortname . $shortprefix . "description",
	"inblock" => "branding",
"type" => "text",
"options" => ""
),


		array("name" => __("Do you to use a custom large image logo rather than domain name text?", 'product'),
		"description" => __("Enter your url in the next section if saying yes - you can ONLY have a custom large logo OR a square logo NOT both", 'product'),
			"id" => $shortname . $shortprefix . "header_image",	     	
	"inblock" => "branding",
			"type" => "select",
			"std" => "Select",
			"options" => array("yes", "no")),

		array(
			"name" => __("Insert your logo full url here", 'product'),
				"description" => __("You can upload your image in the <a href='media-new.php'>media panel</a> and copy paste the url here", 'product'),
			"id" => $shortname . $shortprefix . "header_logo",
	"inblock" => "branding",
			"type" => "text",
			"std" => "",
		),

		array("name" => __("Do you want to use a custom square image logo and your domain name text?", 'product'),	
		"description" => __("Enter your url in the next section if saying yes - you can ONLY have a custom large logo OR a square logo NOT both", 'product'),
			"id" => $shortname . $shortprefix . "header_image_square",	     	
	"inblock" => "branding",
			"type" => "select",
			"std" => "Select",
			"options" => array("yes", "no")),

		array(
			"name" => __("Insert your square logo full url here", 'product'),
				"description" => __("You can upload your image in the <a href='media-new.php'>media panel</a> and copy paste the url here", 'product'),
			"id" => $shortname . $shortprefix . "header_logo_square",
	"inblock" => "branding",
			"type" => "text",
			"std" => "",
		),
);

function product_admin_panel() {
	global $themename, $shortname, $options, $options2, $options3, $bp_existed, $multi_site_on;
	if ( isset($_REQUEST['saved'] )) echo '<div id="message" class="updated fade"><p><strong>'. $themename . __(' settings saved.', 'product') . '</strong></p></div>';
	if ( isset($_REQUEST['reset'] )) echo '<div id="message" class="updated fade"><p><strong>'. $themename . __(' settings reset.', 'product') . '</strong></p></div>';
	?>
	<div id="options-panel">
	<form action="" method="post">

	  <div id="sbtabs">
	  <div class="tabmc">
	  <ul class="ui-tabs-nav" id="tabm">
	 	<li class="first ui-tabs-selected"><a href="#slideone"><?php _e("Slide one",'product'); ?></a></li>
		<li class=""><a href="#slidetwo"><?php _e("Slide two",'product'); ?></a></li>	  
		  <li class=""><a href="#slidethree"><?php _e("Slide three",'product'); ?></a></li>
		  <li class=""><a href="#slidefour"><?php _e("Slide four",'product'); ?></a></li>
		  <li class=""><a href="#contentone"><?php _e("Content One",'product'); ?></a></li>
				  <li class=""><a href="#contenttwo"><?php _e("Content Two",'product'); ?></a></li>
						  <li class=""><a href="#contentthree"><?php _e("Content Three",'product'); ?></a></li>
		  <?php if($bp_existed == 'true') { ?><li class=""><a href="#buddypress"><?php _e("BuddyPress",'product'); ?></a></li><?php } ?>
		  <li class=""><a href="#branding"><?php _e("Branding",'product'); ?></a></li>
		  </ul>
		  </div>

		<div class="tabc">


		<ul style="" class="ui-tabs-panel" id="slideone">
		<li>

		<h2><?php _e("Slideshow Settings", 'product') ?></h2>


		<?php $value_var = 'slideone'; foreach ($options as $value) { ?>

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



		<ul style="" class="list3 ui-tabs-panel ui-tabs-hide" id="slidetwo">

		<li>

		<h2><?php _e("Slide Two Settings", 'product') ?></h2>

		<?php $value_var = 'slidetwo'; foreach ($options as $value) { ?>

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
			
			
		<ul style="" class="list3 ui-tabs-panel ui-tabs-hide" id="slidethree">

		<li>

		<h2><?php _e("Slide Three Settings", 'product') ?></h2>

		<?php $value_var = 'slidethree'; foreach ($options as $value) { ?>

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
		
		<ul style="" class="list3 ui-tabs-panel ui-tabs-hide" id="slidefour">

		<li>

		<h2><?php _e("Slide Four Settings", 'product') ?></h2>

		<?php $value_var = 'slidefour'; foreach ($options as $value) { ?>

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
		
		<ul style="" class="list6 ui-tabs-panel ui-tabs-hide" id="contentone">

		<li>

		<h2><?php _e("Content Settings", 'product') ?></h2>

		<?php $value_var = 'contentone'; foreach ($options as $value) { ?>

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


		
		<ul style="" class="list6 ui-tabs-panel ui-tabs-hide" id="contenttwo">

		<li>

		<h2><?php _e("Content two", 'product') ?></h2>

		<?php $value_var = 'contenttwo'; foreach ($options as $value) { ?>

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
		
		<ul style="" class="list7 ui-tabs-panel ui-tabs-hide" id="contenttwo">

		<li>

		<h2><?php _e("Content Two", 'product') ?></h2>

		<?php $value_var = 'contentwo'; foreach ($options as $value) { ?>

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

		<ul style="" class="list3 ui-tabs-panel ui-tabs-hide" id="contentthree">

		<li>

		<h2><?php _e("Content Three", 'product') ?></h2>

		<?php $value_var = 'contentthree'; foreach ($options as $value) { ?>

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

		<h2><?php _e("BuddyPress Settings", 'product') ?></h2>

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

		<h2><?php _e("Branding Settings", 'product') ?></h2>

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


		<div id="submitsection">

			<div class="submit">
			<h2><?php _e("Click this to save your theme options", 'product') ?></h2>
		<input name="save" type="submit" class="sbutton" value="<?php echo esc_attr(__('Save All Options','product')); ?>" />
		<input type="hidden" name="theme_action" value="save" />
		</div>
		</div>
		</div>
		</form>



		<form method="post">
		<div id="resetsection">
		<div class="submit">
			<h2><?php _e("Clicking this will reset all theme options - use with caution", 'product') ?></h2>
		<input name="reset" type="submit" class="sbutton" value="<?php echo esc_attr(__('Reset All Options','product')); ?>" />
		<input type="hidden" name="theme_action" value="reset" />
		</div>
		</div>
		</form>


		</div>

		<?php
		}
	
		$options3 = array (

		array(
			"name" => __("Choose your body font", 'product'),
			"id" => $shortname . $shortprefix . "body_font",
			"type" => "select",
			"inblock" => "format",
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
			"name" => __("Choose your header font", 'product'),
			"id" => $shortname . $shortprefix . "header_font",
			"type" => "select",
			"inblock" => "format",
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
			"name" => __("Choose your body font colour", 'product'),
			"id" => $shortname . $shortprefix . "font_colour",
			"inblock" => "format",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your h1 colour", 'product'),
			"id" => $shortname . $shortprefix . "h1_colour",
			"inblock" => "format",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your header h1 colour", 'product'),
			"id" => $shortname . $shortprefix . "header_h1_colour",
			"inblock" => "format",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your h2 colour", 'product'),
			"id" => $shortname . $shortprefix . "h2_colour",
			"inblock" => "format",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your header h2 colour", 'product'),
			"id" => $shortname . $shortprefix . "header_h2_colour",
			"inblock" => "format",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your h3 colour", 'product'),
			"id" => $shortname . $shortprefix . "h3_colour",
			"inblock" => "format",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your h4 colour", 'product'),
			"id" => $shortname . $shortprefix . "h4_colour",
			"inblock" => "format",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your h5 colour", 'product'),
			"id" => $shortname . $shortprefix . "h5_colour",
			"inblock" => "format",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your h6 colour", 'product'),
			"id" => $shortname . $shortprefix . "h6_colour",
			"inblock" => "format",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your link colour", 'product'),
			"id" => $shortname . $shortprefix . "link_colour",
			"inblock" => "format",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your link visited colour", 'product'),
			"id" => $shortname . $shortprefix . "link_visited_colour",
			"inblock" => "format",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your link hover colour", 'product'),
			"id" => $shortname . $shortprefix . "link_hover_colour",
			"inblock" => "format",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Insert your <strong>buddypress navigation background image</strong> full url here", 'product'),	
			"description" => __("You can upload your image in the <a href='media-new.php'>media panel</a> and copy paste the url here", 'product'),
			"id" => $shortname . $shortprefix . "bp_nav_bg_image",
			"inblock" => "bp",
			"std" => "",
			"type" => "text"),

		array(
			"name" => __("Buddypress navigation background image repeat", 'product'),
			"id" => $shortname . $shortprefix . "bp_nav_bg_repeat",	
			"inblock" => "bp",
			"type" => "select",
			"std" => "repeat-x",
			"options" => array("no-repeat", "repeat", "repeat-x", "repeat-y")),

		array(
			"name" => __("Choose your buddypress navigation background colour", 'product'),
			"id" => $shortname . $shortprefix . "bp_nav_bg",
			"inblock" => "bp",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your buddypress navigation text colour", 'product'),
			"id" => $shortname . $shortprefix . "bp_nav_text",
			"inblock" => "bp",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your buddypress navigation drop down background colour", 'product'),
			"id" => $shortname . $shortprefix . "bp_nav_drop_bg",
			"inblock" => "bp",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your buddypress navigation hover background colour", 'product'),
			"id" => $shortname . $shortprefix . "bp_nav_hover_bg",
			"inblock" => "bp",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your buddypress navigation hover text colour", 'product'),
			"id" => $shortname . $shortprefix . "bp_nav_hover_text",
			"inblock" => "bp",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Insert your <strong>page navigation background image</strong> full url here", 'product'),
				"description" => __("You can upload your image in the <a href='media-new.php'>media panel</a> and copy paste the url here", 'product'),
			"id" => $shortname . $shortprefix . "page_nav_bg_image",
			"inblock" => "navigation",
			"std" => "",
			"type" => "text"),

		array(
			"name" => __("Page navigation background image repeat", 'product'),
			"id" => $shortname . $shortprefix . "page_nav_bg_repeat",	
			"inblock" => "navigation",
			"type" => "select",
			"std" => "repeat-x",
			"options" => array("no-repeat", "repeat", "repeat-x", "repeat-y")),

		array(
			"name" => __("Choose your page navigation background colour", 'product'),
			"id" => $shortname . $shortprefix . "page_nav_bg",
			"inblock" => "navigation",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your page navigation text colour", 'product'),
			"id" => $shortname . $shortprefix . "page_nav_text",
			"inblock" => "navigation",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your page navigation drop down background colour", 'product'),
			"id" => $shortname . $shortprefix . "page_nav_drop_bg",
			"inblock" => "navigation",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your page navigation hover background colour", 'product'),
			"id" => $shortname . $shortprefix . "page_nav_hover_bg",
			"inblock" => "navigation",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your page navigation hover text colour", 'product'),
			"id" => $shortname . $shortprefix . "page_nav_hover_text",
			"inblock" => "navigation",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Insert your <strong>footer navigation background image</strong> full url here", 'product'),
				"description" => __("You can upload your image in the <a href='media-new.php'>media panel</a> and copy paste the url here", 'product'),
			"id" => $shortname . $shortprefix . "footer_nav_bg_image",
			"inblock" => "navigation",
			"std" => "",
			"type" => "text"),

		array(
			"name" => __("Footer navigation background image repeat", 'product'),
			"id" => $shortname . $shortprefix . "footer_nav_bg_repeat",	
			"inblock" => "navigation",
			"type" => "select",
			"std" => "repeat-x",
			"options" => array("no-repeat", "repeat", "repeat-x", "repeat-y")),

		array(
			"name" => __("Choose your footer navigation background colour", 'product'),
			"id" => $shortname . $shortprefix . "footer_nav_bg",
			"inblock" => "navigation",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your footer navigation text colour", 'product'),
			"id" => $shortname . $shortprefix . "footer_nav_text",
			"inblock" => "navigation",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your footer navigation hover background colour", 'product'),
			"id" => $shortname . $shortprefix . "footer_nav_hover_bg",
			"inblock" => "navigation",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your footer navigation hover text colour", 'product'),
			"id" => $shortname . $shortprefix . "footer_nav_hover_text",
			"inblock" => "navigation",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your body background colour", 'product'),
			"id" => $shortname . $shortprefix . "body_bg",
			"inblock" => "layout",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Insert your <strong>body background image</strong> full url here", 'product'),
				"description" => __("You can upload your image in the <a href='media-new.php'>media panel</a> and copy paste the url here", 'product'),
			"id" => $shortname . $shortprefix . "body_bg_image",
			"inblock" => "layout",
			"std" => "",
			"type" => "text"),

		array(
			"name" => __("Body background image repeat", 'product'),
			"id" => $shortname . $shortprefix . "body_bg_repeat",	
			"inblock" => "layout",
			"type" => "select",
			"std" => "repeat-x",
			"options" => array("no-repeat", "repeat", "repeat-x", "repeat-y")),

		array(
			"name" => __("Choose your content background colour", 'product'),
			"id" => $shortname . $shortprefix . "content_bg",
			"inblock" => "layout",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your content border", 'product'),
			"id" => $shortname . $shortprefix . "content_border",
			"inblock" => "layout",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your sidebar background colour", 'product'),
			"id" => $shortname . $shortprefix . "sidebar_bg",
			"inblock" => "layout",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your sidebar border colour", 'product'),
			"id" => $shortname . $shortprefix . "sidebar_border",
			"inblock" => "layout",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your footer background colour", 'product'),
			"id" => $shortname . $shortprefix . "footer_bg",
			"inblock" => "layout",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your footer border colour", 'product'),
			"id" => $shortname . $shortprefix . "footer_border",
			"inblock" => "layout",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your footer text colour", 'product'),
			"id" => $shortname . $shortprefix . "footer_text",
			"inblock" => "layout",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your content navigation background colour", 'product'),
			"id" => $shortname . $shortprefix . "itemlist_bg",
			"inblock" => "layout",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your content navigation text colour", 'product'),
			"id" => $shortname . $shortprefix . "itemlist_text",
			"inblock" => "layout",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your highlight background colour", 'product'),
			"id" => $shortname . $shortprefix . "highlight_bg",
			"inblock" => "layout",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your highlight border colour", 'product'),
			"id" => $shortname . $shortprefix . "highlight_border",
			"inblock" => "layout",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your highlight text colour", 'product'),
			"id" => $shortname . $shortprefix . "highlight_text",
			"inblock" => "layout",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your hover/dark background colour", 'product'),
			"id" => $shortname . $shortprefix . "element_bg",
			"inblock" => "layout",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your hover/dark border colour", 'product'),
			"id" => $shortname . $shortprefix . "element_border",
			"inblock" => "layout",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your hover/dark text colour", 'product'),
			"id" => $shortname . $shortprefix . "element_text",
			"inblock" => "layout",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your button background colour", 'product'),
			"id" => $shortname . $shortprefix . "button_bg",
			"inblock" => "forms",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Insert your <strong>button background image</strong> full url here", 'product'),
				"description" => __("You can upload your image in the <a href='media-new.php'>media panel</a> and copy paste the url here", 'product'),
			"id" => $shortname . $shortprefix . "button_bg_image",
			"inblock" => "forms",
			"std" => "",
			"type" => "text"),

		array(
			"name" => __("Choose your button text colour", 'product'),
			"id" => $shortname . $shortprefix . "button_text",
			"inblock" => "forms",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your button border colour", 'product'),
			"id" => $shortname . $shortprefix . "button_border",
			"inblock" => "forms",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your button hover background colour", 'product'),
			"id" => $shortname . $shortprefix . "button_hover_bg",
			"inblock" => "forms",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Insert your <strong>button hover background image</strong> full url here", 'product'),
				"description" => __("You can upload your image in the <a href='media-new.php'>media panel</a> and copy paste the url here", 'product'),
			"id" => $shortname . $shortprefix . "button_hover_bg_image",
			"inblock" => "forms",
			"std" => "",
			"type" => "text"),

		array(
			"name" => __("Choose your button hover border colour", 'product'),
			"id" => $shortname . $shortprefix . "button_hover_border",
			"inblock" => "forms",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your button hover text colour", 'product'),
			"id" => $shortname . $shortprefix . "button_hover_text",
			"inblock" => "forms",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your hr colour", 'product'),
			"id" => $shortname . $shortprefix . "hr",
			"inblock" => "elements",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your border colour", 'product'),
			"id" => $shortname . $shortprefix . "border",
			"inblock" => "elements",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your input border colour", 'product'),
			"id" => $shortname . $shortprefix . "input_border",
			"inblock" => "forms",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your input background colour", 'product'),
			"id" => $shortname . $shortprefix . "input_bg",
			"inblock" => "forms",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your textarea border colour", 'product'),
			"id" => $shortname . $shortprefix . "textarea_border",
			"inblock" => "forms",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your textarea background colour", 'product'),
			"id" => $shortname . $shortprefix . "textarea_bg",
			"inblock" => "forms",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your image border colour", 'product'),
			"id" => $shortname . $shortprefix . "image_bg",
			"inblock" => "elements",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your error background colour", 'product'),
			"id" => $shortname . $shortprefix . "error_bg",
			"inblock" => "elements",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your error border colour", 'product'),
			"id" => $shortname . $shortprefix . "error_border",
			"inblock" => "elements",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your error text colour", 'product'),
			"id" => $shortname . $shortprefix . "error_text",
			"inblock" => "elements",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your alt background colour", 'product'),
			"id" => $shortname . $shortprefix . "alt_bg",
			"inblock" => "bp",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your alt text colour", 'product'),
			"id" => $shortname . $shortprefix . "alt_text",
			"inblock" => "bp",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your pending text colour", 'product'),
			"id" => $shortname . $shortprefix . "pending_text",
			"inblock" => "bp",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your pending border colour", 'product'),
			"id" => $shortname . $shortprefix . "pending_border",
			"inblock" => "bp",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your message unread background colour", 'product'),
			"id" => $shortname . $shortprefix . "unread_bg",
			"inblock" => "bp",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your message unread border colour", 'product'),
			"id" => $shortname . $shortprefix . "unread_border",
			"inblock" => "bp",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your message unread text colour", 'product'),
			"id" => $shortname . $shortprefix . "unread_text",
			"inblock" => "bp",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your sticky background colour", 'product'),
			"id" => $shortname . $shortprefix . "sticky_bg",
			"inblock" => "bp",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your sticky text colour", 'product'),
			"id" => $shortname . $shortprefix . "sticky_text",
			"inblock" => "bp",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your slideshow pagination background colour", 'product'),
			"id" => $shortname . $shortprefix . "slideshow_tabs_bg",
			"inblock" => "slideshow",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your slideshow pagination border colour", 'product'),
			"id" => $shortname . $shortprefix . "slideshow_tabs_link",
			"inblock" => "slideshow",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your slideshow pagination link colour", 'product'),
			"id" => $shortname . $shortprefix . "slideshow_tabs_link",
			"inblock" => "slideshow",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your slideshow pagination link active background colour", 'product'),
			"id" => $shortname . $shortprefix . "slideshow_tabs_bg_active",
			"inblock" => "slideshow",
			"std" => "",
			"type" => "colorpicker"),

		array(
			"name" => __("Choose your slideshow pagination link active colour", 'product'),
			"id" => $shortname . $shortprefix . "slideshow_tabs_link_active",
			"inblock" => "slideshow",
			"std" => "",
			"type" => "colorpicker"),

		);

function product_custom_style_admin_panel() {

		global $themename, $options, $options2, $options3, $bp_existed, $multi_site_on;

		if ( isset($_REQUEST['saved3'] )) echo '<div id="message" class="updated fade"><p><strong>'. $themename . __(' settings saved.', 'product') . '</strong></p></div>';
		if ( isset($_REQUEST['reset3'] )) echo '<div id="message" class="updated fade"><p><strong>'. $themename . __(' settings reset.', 'product') . '</strong></p></div>';
		?>

		<div id="options-panel">
		<form action="" method="post">

		  <div id="sbtabs">
		  <div class="tabmc">
		  <ul class="ui-tabs-nav" id="tabm">
			  <li class="first ui-tabs-selected"><a href="#format"><?php _e("Text",'product'); ?></a></li>
			  <li class=""><a href="#navigation"><?php _e("Navigation",'product'); ?></a></li>
			  <li class=""><a href="#layout"><?php _e("Layout",'product'); ?></a></li>
			  <li class=""><a href="#elements"><?php _e("Elements",'product'); ?></a></li>
			  <li class=""><a href="#forms"><?php _e("Forms",'product'); ?></a></li>
			  <li class=""><a href="#slideshow"><?php _e("Slideshow",'product'); ?></a></li>
			  <?php if($bp_existed == 'true') { ?><li class=""><a href="#bp"><?php _e("BuddyPress",'product'); ?></a></li><?php } ?>
		  </ul>
		</div>


		<div class="tabc">


		<ul style="" class="ui-tabs-panel" id="format">
		<li>
			<h2><?php _e("Text styling", 'product') ?></h2>

			<?php $value_var = 'format'; foreach ($options3 as $value) { ?>

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
				<h2><?php _e("Navigation", 'product') ?></h2>

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
				<ul style="" class="ui-tabs-panel" id="layout">
				<li>
					<h2><?php _e("Layout", 'product') ?></h2>

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
				
				<ul style="" class="ui-tabs-panel" id="elements">
				<li>
					<h2><?php _e("Elements", 'product') ?></h2>

					<?php $value_var = 'elements'; foreach ($options3 as $value) { ?>

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
				
						<ul style="" class="ui-tabs-panel" id="forms">
						<li>
							<h2><?php _e("Forms", 'product') ?></h2>

							<?php $value_var = 'forms'; foreach ($options3 as $value) { ?>

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
								<ul style="" class="ui-tabs-panel" id="slideshow">
								<li>
									<h2><?php _e("Slideshow", 'product') ?></h2>

									<?php $value_var = 'slideshow'; foreach ($options3 as $value) { ?>

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
										<ul style="" class="ui-tabs-panel" id="bp">
										<li>
											<h2><?php _e("BuddyPress", 'product') ?></h2>

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
										</li>
										</ul>
											<?php } ?>
	</div>
	</div>



	<div id="submitsection">
		
		<div class="submit">
		<h2><?php _e("Click this to save your theme options", 'product') ?></h2>
	<input name="save" type="submit" class="sbutton" value="<?php echo esc_attr(__('Save All Options','product')); ?>" />
	<input type="hidden" name="theme_action3" value="save3" />
	</div>
	</div>
	</div>
	</form>



	<form method="post">
	<div id="resetsection">
	<div class="submit">
		<h2><?php _e("Clicking this will reset all theme options - use with caution", 'product') ?></h2>
	<input name="reset" type="submit" class="sbutton" value="<?php echo esc_attr(__('Reset All Options','product')); ?>" />
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

	array(  "name" => __("Choose Your BP product Preset Style:", 'product'),
		  	"id" => $shortname. $shortprefix . "custom_style",
			"std" => "default.css",
			"type" => "radio",
			"options" => $alt_stylesheets)
	);

function product_ready_style_admin_panel() {
	echo "<div id=\"admin-options\">";
	
	global $themename, $shortname, $options2;
	
	if ( isset($_REQUEST['saved2'] )) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings saved.</strong></p></div>';
	if ( isset($_REQUEST['reset2'] )) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings reset.</strong></p></div>';
?>

<h4><?php echo "$themename"; ?> <?php _e('Choose your BP product Preset Style', 'product'); ?></h4>
<h2><?php _e('If you want to customise the theme options you MUST have default.css selected', 'product'); ?></h2>
<form action="" method="post">
<div class="get-listings">
<h2><?php _e("Style Select:", 'product') ?></h2>
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
		<input name="save2" type="submit" class="sbutton" value="<?php echo esc_attr(__('Save Options', 'product')); ?>" />
		<input type="hidden" name="theme_action2" value="save2" />
	</p>
</form>

<form method="post">
	<p class="save-p">
		<input name="reset2" type="submit" class="sbutton" value="<?php echo esc_attr(__('Reset Options', 'product')); ?>" />
		<input type="hidden" name="theme_action2" value="reset2" />
	</p>
</form>
</div>

<?php }

function product_admin_register() {
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
		add_theme_page(_g ($themename . __(' Theme Options', 'product')),  _g (__('Theme Options', 'product')),  'edit_theme_options', 'functions.php', 'product_admin_panel');
}


function product_ready_style_admin_register() {
	global $themename, $shortname, $options, $options2, $options3, $bp_existed, $multi_site_on;
		$action2 = isset($_REQUEST['theme_action2']);
	if ( isset($_GET['page']) == 'product-themes.php' ) {
		if ( 'save2' == $action2) {
			foreach ($options2 as $value) {
				update_option( $value['id'], isset($_REQUEST[ $value['id'] ]) ); 
			}
			foreach ($options2 as $value) {
				if( isset( $_REQUEST[ $value['id'] ] ) ) { 
					update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); 
				} 
				else { 
					delete_option( $value['id'] ); 
				} 
			}	
			header("Location: themes.php?page=product-themes.php&saved2=true");
			die;
		} 
		else if( 'reset2' == $action2) {
			foreach ($options2 as $value) {
				delete_option( $value['id'] ); 
			}
			header("Location: themes.php?page=product-themes.php&reset2=true");
			die;
		}
	}
	add_theme_page(_g (__('BP product Preset Style', 'product')),  _g (__('Preset Style', 'product')),  'edit_theme_options', 'product-themes.php', 'product_ready_style_admin_panel');
}


function product_custom_style_admin_register() {
	global $themename, $shortname, $options, $options2, $options3, $bp_existed, $multi_site_on;
	$action3 = isset($_REQUEST['theme_action3']);
	
		if ( isset($_GET['page']) == 'styling-functions.php' ) {
			if ( 'save3' == $action3) {
				foreach ($options3 as $value) {	
					update_option( $value['id'],isset($_REQUEST[ $value['id'] ] )); }
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
			add_theme_page(_g ($themename . __('Custom styling', 'product')),  _g (__('Custom Styling', 'product')),  'edit_theme_options', 'styling-functions.php', 'product_custom_style_admin_panel');
	}

function product_admin_head() { ?>
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
	
	<?php if (isset($_GET['page']) == 'product-themes.php'){?>
			<link href="<?php bloginfo('template_directory'); ?>/library/options/custom-admin.css" rel="stylesheet" type="text/css" />
	<?php } ?>
	
<?php }

add_action('admin_head', 'product_admin_head');
add_action('admin_menu', 'product_admin_register');
add_action('admin_menu', 'product_ready_style_admin_register');
add_action('admin_menu', 'product_custom_style_admin_register');

?>