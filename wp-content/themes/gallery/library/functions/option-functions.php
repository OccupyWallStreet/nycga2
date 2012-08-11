<?php
function _g($str)
{
return __($str, 'option-page');
}

$themename = "Gallery";
$themeversion = "1.0";
$shortname = "dev";
$shortprefix = "_gallery_";
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
	array("name" => __("Show custom header", 'gallery'),
		"description" => __("You can show or hide the custom header, the default is no", 'gallery'),
		"id" => $shortname . $shortprefix . "customheader_on",	     	
		"inblock" => "home",
	    "type" => "select",
		"std" => "Show",
		"options" => array("no", "yes")),
		
	array("name" => __("Do you want to have show site navigation?", 'gallery'),
		"description" => __("By default navigation shows click no to turn it off", 'gallery'),
		"id" => $shortname . $shortprefix . "navigation",	     	
		"inblock" => "home",
		"type" => "select",
		"std" => "Select",
		"options" => array("yes", "no")),
		
		array("name" => __("Do you want to have the slideshow on?", 'gallery'),
			"description" => __("By default the slideshow is on.", 'gallery'),
			"id" => $shortname . $shortprefix . "slideshow",	     	
			"inblock" => "home",
			"type" => "select",
			"std" => "Select",
			"options" => array("yes", "no")),
		
			array("name" => __("Do you want to have a full width slideshow or a partial width one with a list of Exhibitions?", 'gallery'),
				"description" => __("Set full or partial as option.  Full with images are 960 x 450px and partial ones are 750 x 350px", 'gallery'),
				"id" => $shortname . $shortprefix . "slideshowsize",	     	
				"inblock" => "home",
				"type" => "select",
				"std" => "Select",
				"options" => array("partial", "full")),
					
			array("name" => __("Do you want to show the exhibition menu if you have a full slideshow?", 'gallery'),
				"description" => __(
					"You can set this up under your menu and add Exhibitions.", 'gallery'),
				"id" => $shortname . $shortprefix . "catnavigation",	     	
				"inblock" => "home",
				"type" => "select",
				"std" => "Select",
				"options" => array("no", "yes")),
		
						array("name" => __("Do you want social buttons on your home page?", 'gallery'),
							"description" => __(
								"You can set this up under the content tab *default no", 'gallery'),
							"id" => $shortname . $shortprefix . "socialbuttons",	     	
							"inblock" => "home",
							"type" => "select",
							"std" => "Select",
							"options" => array("no", "yes")),
							
							array("name" => __("Do you want content on your home page?", 'gallery'),
								"description" => __(
									"You can set this up under the content tab (header, sub header and description) *default no", 'gallery'),
								"id" => $shortname . $shortprefix . "homecontent",	     	
								"inblock" => "home",
								"type" => "select",
								"std" => "Select",
								"options" => array("no", "yes")),
								
												array("name" => __("Do you want to show the Exhibition menu in your footer?", 'gallery'),
													"description" => __(
														"Show a gallery page menu navigation in your footer *default no", 'gallery'),
													"id" => $shortname . $shortprefix . "gallerymenu",	     	
													"inblock" => "home",
													"type" => "select",
													"std" => "Select",
													"options" => array("no", "yes")),
						
										array("name" => __("Do you want a widget footer?", 'gallery'),
											"description" => __(
												"4 rows will show of areas you can add widgets to if you want this", 'gallery'),
											"id" => $shortname . $shortprefix . "widgets",	     	
											"inblock" => "home",
											"type" => "select",
											"std" => "Select",
											"options" => array("no", "yes")),
	
	array(
		"name" => __("Header text", 'gallery'),
		"id" => $shortname . $shortprefix . "header",
		"inblock" => "content",
		"type" => "textarea",
		"std" => "",
	),
	array(
		"name" => __("Description", 'gallery'),
		"id" => $shortname . $shortprefix . "description",
		"inblock" => "content",
		"type" => "textarea",
		"std" => "",
	),
	array(
		"name" => __("Sub Header", 'gallery'),
		"id" => $shortname . $shortprefix . "subheader",
		"inblock" => "content",
		"type" => "textarea",
		"std" => "",
	),
	array(
		"name" => __("Footer links", 'gallery'),
		"id" => $shortname . $shortprefix . "footerlinks",
		"inblock" => "content",
		"type" => "textarea",
		"std" => "",
	),
	array(
		"name" => __("Flickr link", 'gallery'),
		"id" => $shortname . $shortprefix . "flickr",
		"inblock" => "social",
		"type" => "textarea",
		"std" => "",
	),
	array(
		"name" => __("Delicious link", 'gallery'),
		"id" => $shortname . $shortprefix . "delicious",
		"inblock" => "social",
		"type" => "textarea",
		"std" => "",
	),
	array(
		"name" => __("RSS link", 'gallery'),
		"id" => $shortname . $shortprefix . "rss",
		"inblock" => "social",
		"type" => "textarea",
		"std" => "",
	),
	array(
		"name" => __("Facebook link", 'gallery'),
		"id" => $shortname . $shortprefix . "facebook",
		"inblock" => "social",
		"type" => "textarea",
		"std" => "",
	),
	array(
		"name" => __("Vimeo link", 'gallery'),
		"id" => $shortname . $shortprefix . "vimeo",
		"inblock" => "social",
		"type" => "textarea",
		"std" => "",
	),
	array(
		"name" => __("Twitter link", 'gallery'),
		"id" => $shortname . $shortprefix . "twitter",
		"inblock" => "social",
		"type" => "textarea",
		"std" => "",
	),
	array(
		"name" => __("Site name", 'gallery'),
		"id" => $shortname . $shortprefix . "site_title",
		"inblock" => "branding",
		"type" => "text",
		"std" => "",
	),
	array("name" => __("Do you to have a logo or use just use the site name?", 'gallery'),
		"description" => __("Entering yes means the logo will be used - yes by default", 'gallery'),
		"id" => $shortname . $shortprefix . "logo",	     	
		"inblock" => "branding",
		"type" => "select",
		"std" => "Select",
		"options" => array("yes", "no")),
);

function gallery_admin_panel() {
	global $themename, $shortname, $options, $options2, $options3, $bp_existed, $multi_site_on;
	if ( isset($_REQUEST['saved1']) ) echo '<div id="message" class="updated fade"><p><strong>'. $themename . __(' settings saved.', 'gallery') . '</strong></p></div>';
	if ( isset($_REQUEST['reset1']) ) echo '<div id="message" class="updated fade"><p><strong>'. $themename . __(' settings reset.', 'gallery') . '</strong></p></div>';
	?>
	<div id="options-panel">
	<form action="" method="post">
	  <div id="sbtabs">
	  <div class="tabmc">
	  <ul class="ui-tabs-nav" id="tabm">
	  <li class="first ui-tabs-selected"><a href="#home"><?php _e("Home Builder",'gallery'); ?></a></li>
	  <li class=""><a href="#content"><?php _e("Content",'gallery'); ?></a></li>
	  <li class=""><a href="#social"><?php _e("Social",'gallery'); ?></a></li>
	  <li class=""><a href="#branding"><?php _e("Branding",'gallery'); ?></a></li>	  </ul>
	  </div>

	<div class="tabc">

		<ul style="" class="ui-tabs-panel" id="home">
		<li>

		<h2><?php _e("Build your home page - menus set up under Appearance", 'gallery') ?></h2>


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
	<ul style="" class="ui-tabs-panel" id="content">
	<li>

	<h2><?php _e("Content", 'gallery') ?></h2>


	<?php $value_var = 'content'; foreach ($options as $value) { ?>

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
	<ul style="" class="list9 ui-tabs-panel ui-tabs-hide" id="social">

	<li>

	<h2><?php _e("Social", 'gallery') ?></h2>

	<?php $value_var = 'social'; foreach ($options as $value) { ?>

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

	<ul style="" class="list9 ui-tabs-panel ui-tabs-hide" id="branding">

	<li>

	<h2><?php _e("Branding Settings", 'gallery') ?></h2>

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
		<h2><?php _e("Click this to save your theme options", 'gallery') ?></h2>
	<input name="save1" type="submit" class="sbutton" value="<?php echo esc_attr(__('Save All Options','gallery')); ?>" />
	<input type="hidden" name="theme_action1" value="save1" />
	</div>
	</div>
	</div>
	</form>



	<form method="post">
	<div id="resetsection">
	<div class="submit">
		<h2><?php _e("Clicking this will reset all theme options - use with caution", 'gallery') ?></h2>
	<input name="reset1" type="submit" class="sbutton" value="<?php echo esc_attr(__('Reset All Options','gallery')); ?>" />
	<input type="hidden" name="theme_action1" value="reset1" />
	</div>
	</div>
	</form>


	</div>
	<?php
	}
	
$options3 = array (

array(
	"name" => __("Choose your body font", 'gallery'),
	"id" => $shortname . $shortprefix . "body_font",
	"type" => "select",
	"inblock" => "text",
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
									"Yanone Kaffeesatz, arial, serif"
	            )
),

array(
	"name" => __("Choose your header font", 'gallery'),
	"description" => __("We include google font directory fonts you can <a href='http://code.google.com/webfonts'>view here</a> ", 'gallery'),
	"id" => $shortname . $shortprefix . "header_font",
	"type" => "select",
	"inblock" => "text",
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
				"Yanone Kaffeesatz, arial, serif"
	            )
),

array(
	"name" => __("Body font size", 'gallery'),
		"description" => __("Larger than 12px you may need to adjust line height and other CSS settings in theme with some fonts - all sizes in px", 'gallery'),
	"id" => $shortname . $shortprefix . "body_size",	
	"inblock" => "text",
	"type" => "select",
	"std" => "12",
	"options" => array("12", "14", "16", "18", "20")),

array(
	"name" => __("Body line height", 'gallery'),
		"description" => __("Default is 12px font size and 22 px line height", 'gallery'),
	"id" => $shortname . $shortprefix . "body_lineheight",	
	"inblock" => "text",
	"type" => "select",
	"std" => "22",
	"options" => array("22", "24", "30", "32", "36")),
	
array(
	"name" => __("Header 1 font size", 'gallery'),
		"description" => __("All sizes in px", 'gallery'),
	"id" => $shortname . $shortprefix . "h1_size",	
	"inblock" => "text",
	"type" => "select",
	"std" => "90",
	"options" => array("100", "90", "80", "70", "60", "50")),

array(
	"name" => __("Header 2 font size", 'gallery'),
	"description" => __("ll sizes in px", 'gallery'),
	"id" => $shortname . $shortprefix . "h2_size",	
	"inblock" => "text",
	"type" => "select",
	"std" => "18",
	"options" => array("18", "20", "24", "30", "40", "50")),

array(
	"name" => __("Header 3 font size", 'gallery'),
	"description" => __("All sizes in px", 'gallery'),
	"id" => $shortname . $shortprefix . "h3_size",	
	"inblock" => "text",
	"type" => "select",
	"std" => "14",
	"options" => array("14", "18", "20", "24", "30", "40")),

array(
	"name" => __("Header 4 font size", 'gallery'),
	"description" => __("All sizes in px", 'gallery'),
	"id" => $shortname . $shortprefix . "h4_size",	
	"inblock" => "text",
	"type" => "select",
	"std" => "12",
	"options" => array("12", "14", "18", "20", "24", "30")),

array(
	"name" => __("Navigation font size", 'gallery'),
	"description" => __("All sizes in px", 'gallery'),
	"id" => $shortname . $shortprefix . "nav_size",	
	"inblock" => "navigation",
	"type" => "select",
	"std" => "18",
	"options" => array("18", "14", "12", "20", "22", "24", "30", "40")),

array(
	"name" => __("Navigation link padding", 'gallery'),
	"description" => __("All sizes in px", 'gallery'),
	"id" => $shortname . $shortprefix . "nav_padding",	
	"inblock" => "navigation",
	"type" => "select",
	"std" => "10",
	"options" => array("10", "8", "6", "4", "2", "1")),

array(
	"name" => __("Navigation side font size", 'gallery'),
	"description" => __("All sizes in px", 'gallery'),
	"id" => $shortname . $shortprefix . "nav_side_size",	
	"inblock" => "navigation",
	"type" => "select",
	"std" => "18",
	"options" => array("18", "14", "12", "20", "22", "24", "30", "40")),

array(
	"name" => __("Navigation side link padding", 'gallery'),
	"description" => __("All sizes in px", 'gallery'),
	"id" => $shortname . $shortprefix . "nav_side_padding",	
	"inblock" => "navigation",
	"type" => "select",
	"std" => "10",
	"options" => array("10", "8", "6", "4", "2", "1")),

array(
	"name" => __("Text and box shadows on or off?", 'gallery'),
	"id" => $shortname . $shortprefix . "shadows",	
	"inblock" => "layout",
	"type" => "select",
	"std" => "on",
	"options" => array("on", "off")),

array(
	"name" => __("Choose your text shadow color", 'gallery'),
	"id" => $shortname . $shortprefix . "textshadow_color",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your box shadow color", 'gallery'),
	"id" => $shortname . $shortprefix . "boxshadow_color",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your grid background color", 'gallery'),
	"id" => $shortname . $shortprefix . "grid_background_color",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your grid border color", 'gallery'),
	"id" => $shortname . $shortprefix . "grid_border_color",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your slideshow and comment form background color", 'gallery'),
	"id" => $shortname . $shortprefix . "slider_background_color",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your slideshow and comment form border color", 'gallery'),
	"id" => $shortname . $shortprefix . "slider_border_color",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your post bottom border color", 'gallery'),
	"id" => $shortname . $shortprefix . "post_border",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your avatar background color", 'gallery'),
	"id" => $shortname . $shortprefix . "avatar_background_color",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your avatar border color", 'gallery'),
	"id" => $shortname . $shortprefix . "avatar_border_color",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your comment border color", 'gallery'),
	"id" => $shortname . $shortprefix . "comment_border_color",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your font color", 'gallery'),
	"id" => $shortname . $shortprefix . "font_color",
	"inblock" => "text",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your site name color", 'gallery'),
	"id" => $shortname . $shortprefix . "branding_color",
	"inblock" => "text",
	"std" => "",
	"type" => "colorpicker"),
	
array(
	"name" => __("Choose your h1 color", 'gallery'),
	"id" => $shortname . $shortprefix . "h1_color",
	"inblock" => "text",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your h2 color", 'gallery'),
	"id" => $shortname . $shortprefix . "h2_color",
	"inblock" => "text",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your h3 color", 'gallery'),
	"id" => $shortname . $shortprefix . "h3_color",
	"inblock" => "text",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your h4 color", 'gallery'),
	"id" => $shortname . $shortprefix . "h4_color",
	"inblock" => "text",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your h5 color", 'gallery'),
	"id" => $shortname . $shortprefix . "h5_color",
	"inblock" => "text",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your h6 color", 'gallery'),
	"id" => $shortname . $shortprefix . "h6_color",
	"inblock" => "text",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your link color", 'gallery'),
	"id" => $shortname . $shortprefix . "link_color",
	"inblock" => "text",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your link hover color", 'gallery'),
	"id" => $shortname . $shortprefix . "link_hover_color",
	"inblock" => "text",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your link visited color", 'gallery'),
	"id" => $shortname . $shortprefix . "link_visited_color",
	"inblock" => "text",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your button background color", 'gallery'),
	"id" => $shortname . $shortprefix . "button_background_color",
	"inblock" => "forms",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your button text color", 'gallery'),
	"id" => $shortname . $shortprefix . "button_text_color",
	"inblock" => "forms",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your button border color", 'gallery'),
	"id" => $shortname . $shortprefix . "button_border_color",
	"inblock" => "forms",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your button hover background color", 'gallery'),
	"id" => $shortname . $shortprefix . "button_hover_background_color",
	"inblock" => "forms",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your button hover text color", 'gallery'),
	"id" => $shortname . $shortprefix . "button_hover_text_color",
	"inblock" => "forms",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your button hover border color", 'gallery'),
	"id" => $shortname . $shortprefix . "button_hover_border_color",
	"inblock" => "forms",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your form input text color", 'gallery'),
	"id" => $shortname . $shortprefix . "form_color",
	"inblock" => "forms",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your form input background color", 'gallery'),
	"id" => $shortname . $shortprefix . "form_background_color",
	"inblock" => "forms",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your form input border color", 'gallery'),
	"id" => $shortname . $shortprefix . "form_border_color",
	"inblock" => "forms",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your navigation background color", 'gallery'),
	"id" => $shortname . $shortprefix . "nav_background_color",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your navigation text color", 'gallery'),
	"id" => $shortname . $shortprefix . "nav_text_color",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your navigation border color", 'gallery'),
	"id" => $shortname . $shortprefix . "nav_border_color",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your navigation hover background color", 'gallery'),
	"id" => $shortname . $shortprefix . "nav_hover_background_color",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your navigation hover text color", 'gallery'),
	"id" => $shortname . $shortprefix . "nav_hover_text_color",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your navigation hover border color", 'gallery'),
	"id" => $shortname . $shortprefix . "nav_hover_border_color",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),
	
array(
	"name" => __("Choose your side navigation background color", 'gallery'),
	"id" => $shortname . $shortprefix . "nav_side_background_color",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your side navigation text color", 'gallery'),
	"id" => $shortname . $shortprefix . "nav_side_text_color",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your side navigation border color", 'gallery'),
	"id" => $shortname . $shortprefix . "nav_side_border_color",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your side navigation hover background color", 'gallery'),
	"id" => $shortname . $shortprefix . "nav_side_hover_background_color",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your side navigation hover text color", 'gallery'),
	"id" => $shortname . $shortprefix . "nav_side_hover_text_color",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your side navigation hover border color", 'gallery'),
	"id" => $shortname . $shortprefix . "nav_side_hover_border_color",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your alt color", 'gallery'),
	"id" => $shortname . $shortprefix . "alt",
	"inblock" => "buddypress",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your nav color", 'gallery'),
	"id" => $shortname . $shortprefix . "nav",
	"inblock" => "buddypress",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your nav selected color", 'gallery'),
	"id" => $shortname . $shortprefix . "nav_select",
	"inblock" => "buddypress",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your loading button background color", 'gallery'),
	"id" => $shortname . $shortprefix . "loading_background",
	"inblock" => "buddypress",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your loading button font color", 'gallery'),
	"id" => $shortname . $shortprefix . "loading_color",
	"inblock" => "buddypress",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your loading button background color", 'gallery'),
	"id" => $shortname . $shortprefix . "loading_background",
	"inblock" => "buddypress",
	"std" => "",
	"type" => "colorpicker"),


);

function gallery_custom_style_admin_panel() {

		global $themename, $options, $options2, $options3, $bp_existed, $multi_site_on;

		if ( isset($_REQUEST['saved3']) ) echo '<div id="message" class="updated fade"><p><strong>'. $themename . __(' settings saved.', 'gallery') . '</strong></p></div>';
		if ( isset($_REQUEST['reset3']) ) echo '<div id="message" class="updated fade"><p><strong>'. $themename . __(' settings reset.', 'gallery') . '</strong></p></div>';
		?>

		<div id="options-panel">
		<form action="" method="post">

		  <div id="sbtabs">
		  <div class="tabmc">
		  <ul class="ui-tabs-nav" id="tabm">
		  <li class="first ui-tabs-selected"><a href="#layout"><?php _e("Layout",'gallery'); ?></a></li>
		
		  <li class=""><a href="#text"><?php _e("Text",'gallery'); ?></a></li>
		
		  <li class=""><a href="#forms"><?php _e("Form Colors",'gallery'); ?></a></li>
		
		  <li class=""><a href="#navigation"><?php _e("Navigation",'gallery'); ?></a></li>
		  <?php if($bp_existed == 'true') { ?><li class=""><a href="#buddypress"><?php _e("BuddyPress",'gallery'); ?></a></li><?php } ?>
		  </ul>
		</div>


		<div class="tabc">


		<ul style="" class="ui-tabs-panel" id="layout">
		<li>
			<h2><?php _e("Layout Colors - background and custom header are set under Appearance", 'gallery') ?></h2>

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
				<h2><?php _e("Text", 'gallery') ?></h2>

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
				<ul style="" class="ui-tabs-panel" id="forms">
				<li>
					<h2><?php _e("Form colors", 'gallery') ?></h2>

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
				
				<ul style="" class="ui-tabs-panel" id="navigation">
				<li>
					<h2><?php _e("Navigation Colors", 'gallery') ?></h2>

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
				
					<?php if($bp_existed == 'true') { ?>
						<ul style="" class="ui-tabs-panel" id="buddypress">
						<li>
							<h2><?php _e("BuddyPress components", 'gallery') ?></h2>

							<?php $value_var = 'buddypress'; foreach ($options3 as $value) { ?>

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
		<h2><?php _e("Click this to save your theme options", 'gallery') ?></h2>
	<input name="save3" type="submit" class="sbutton" value="<?php echo esc_attr(__('Save All Options','gallery')); ?>" />
	<input type="hidden" name="theme_action3" value="save3" />
	</div>
	</div>
	</div>
	</form>



	<form method="post">
	<div id="resetsection">
	<div class="submit">
		<h2><?php _e("Clicking this will reset all theme options - use with caution", 'gallery') ?></h2>
	<input name="reset3" type="submit" class="sbutton" value="<?php echo esc_attr(__('Reset All Options','gallery')); ?>" />
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

	array(  "name" => __("Choose Your Gallery Preset Style:", 'gallery'),
		  	"id" => $shortname. $shortprefix . "custom_style",
			"std" => "lightroom.css",
			"type" => "radio",
			"options" => $alt_stylesheets)
	);

function gallery_ready_style_admin_panel() {
	echo "<div id=\"admin-options\">";
	
	global $themename, $shortname, $options2;
	
	if ( isset($_REQUEST['saved2']) ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings saved.</strong></p></div>';
	if ( isset($_REQUEST['reset2'] )) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings reset.</strong></p></div>';
?>

<h4><?php echo "$themename"; ?> <?php _e('Choose your preset style', 'gallery'); ?></h4>
<form action="" method="post">
<div class="get-listings">
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
	<img src="<?php bloginfo('template_directory'); ?>/library/styles/images/screens/<?php echo $screenshot_img . '.png'; ?>" alt="<?php echo $screenshot_img; ?>" />
</div>
<input type="radio" name="<?php echo $value['id']; ?>" value="<?php echo $option2; ?>" <?php echo $checked; ?> /><?php echo $option2; ?>
</li>

<?php } 
} ?>

</ul>
</div>
</div>
<div id="submitsection">
	
	<div class="submit">
	<h2><?php _e("Click this to save your theme options", 'gallery') ?></h2>
<input name="save2" type="submit" class="sbutton" value="<?php echo esc_attr(__('Save All Options','gallery')); ?>" />
<input type="hidden" name="theme_action2" value="save2" />
</div>
</div>
</form>

<form method="post">
<div id="resetsection">
<div class="submit">
	<h2><?php _e("Clicking this will reset all theme options - use with caution", 'gallery') ?></h2>
<input name="reset2" type="submit" class="sbutton" value="<?php echo esc_attr(__('Reset All Options','gallery')); ?>" />
<input type="hidden" name="theme_action2" value="reset2" />
</div>
</div>
</form>


</div>


<?php }

function gallery_admin_register() {
	global $themename, $shortname, $options;
		$action1 = isset($_REQUEST['theme_action1']);
	if ( isset($_GET['page']) == 'functions.php' ) {
	if ( 'save1' == $action1 ) {
	foreach ($options as $value) {
	update_option( $value['id'], isset($_REQUEST[ $value['id'] ] )); }
	foreach ($options as $value) {
	if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } else { delete_option( $value['id'] ); } }
	header("Location: admin.php?page=gallery-theme&page=functions.php&saved1=true");
	die;
	} else if( 'reset1' == $action1 ) {
	foreach ($options as $value) {
	delete_option( $value['id'] ); }
	header("Location: admin.php?page=gallery-theme&page=functions.php&reset1=true");
	die;
	}
	}
}


function gallery_ready_style_admin_register() {
	global $themename, $shortname, $options, $options2, $options3;
		$action2 = isset($_REQUEST['theme_action2']);
	if (isset($_GET['page']) == 'gallery-themes.php' ) {
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
			header("Location: admin.php?page=gallery-theme&page=gallery-themes.php&saved2=true");
			die;
		} 
		else if( 'reset2' == $action2 ) {
			foreach ($options2 as $value) {
				delete_option( $value['id'] ); 
			}
			header("Location: admin.php?page=gallery-theme&page=gallery-themes.php&reset2=true");
			die;
		}
	}
}

function gallery_custom_style_admin_register() {
	global $themename, $shortname, $options, $options2, $options3;
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
				header("Location: admin.php?page=gallery-theme&page=styling-functions.php&saved3=true");
				die;
				} 
				else if( 'reset3' == $action3 ) {
					foreach ($options3 as $value) {
						delete_option( $value['id'] ); 
					}
				header("Location: admin.php?page=gallery-theme&page=styling-functions.php&reset3=true");
				die;
				}
			}		
			
}

function gallery_admin_head() { ?>
	<?php if ( (isset($_GET['page']) && $_GET['page'] == 'styling-functions.php' ) || ( isset($_GET['page']) && $_GET['page'] == 'functions.php' ) || ( isset($_GET['page']) && $_GET['page'] == 'home-functions.php' )) {?>
		
		<link href="<?php bloginfo('template_directory'); ?>/library/options/options-css.css" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/library/scripts/jscolor.js"></script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/library/scripts/jquery-ui-personalized-1.6rc2.min.js"></script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/library/scripts/jquery.cookie.min.js"></script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/library/scripts/jquery.imgareaselect-0.9.3.min.js"></script>
		
		<link href="<?php bloginfo('template_directory'); ?>/library/scripts/imgareaselect-default.css" rel="stylesheet" type="text/css" />
	<?php 	wp_enqueue_script("jquery"); ?>
	
		<script type="text/javascript">
			   jQuery.noConflict();
		
		jQuery(document).ready(function(){
		jQuery('ul#tabm').tabs({event: "click"});
		});
		</script>

	<?php } ?>
	
	<?php if (isset($_GET['page']) == 'gallery-themes.php'){?>
			<link href="<?php bloginfo('template_directory'); ?>/library/options/custom-admin.css" rel="stylesheet" type="text/css" />
	<?php } ?>
	
	<?php if (isset($_GET['page']) == 'gallery-theme'){?>
			<link href="<?php bloginfo('template_directory'); ?>/library/options/custom-faq.css" rel="stylesheet" type="text/css" />
	<?php } ?>
	
	<?php if (isset($_GET['page']) == 'home-functions.php'){?>
			<link href="<?php bloginfo('template_directory'); ?>/library/options/custom-homepage.css" rel="stylesheet" type="text/css" />
	<?php } ?>
	
	<?php if (isset($_GET['page']) == 'step-functions.php'){?>
			<link href="<?php bloginfo('template_directory'); ?>/library/options/custom-steps.css" rel="stylesheet" type="text/css" />
				<?php 	wp_enqueue_script("jquery"); ?>
			<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/library/scripts/accordion-menu/javascript.js"></script>
	<?php } ?>
<?php }

function gallery_pages() {
	add_menu_page(__('Gallery Theme','gallery'), __('Gallery Theme','gallery'), 'manage_options',  'gallery-theme', 'gallery_landing_page', WP_CONTENT_URL. '/themes' . '/gallery/library/options/faqimages/portfolio-icon.png', 120);
	add_submenu_page('gallery-theme', __("Getting started", 'gallery'), __("Getting Started", 'gallery'), 'manage_options', 'gallery-theme', 'gallery_landing_page');
	add_submenu_page('gallery-theme', __("Home Page", 'gallery'), __("Home Page", 'gallery'), 'manage_options', 'home-functions.php', 'gallery_home_page');
	add_submenu_page('gallery-theme', __("Exhibitions", 'gallery'), __("Exhibitions", 'gallery'), 'manage_options', 'edit.php?post_type=exhibition');
	add_submenu_page('gallery-theme', __("Advanced Options", 'gallery'), __("Advanced Options", 'gallery'), 'edit_theme_options', 'functions.php', 'gallery_admin_panel'); 
	add_submenu_page('gallery-theme', __("Preset Styles", 'gallery'), __("Preset Styles", 'gallery'), 'edit_theme_options', 'gallery-themes.php', 'gallery_ready_style_admin_panel');
	add_submenu_page('gallery-theme', __("Customization", 'gallery'), __("Customization", 'gallery'), 'edit_theme_options', 'styling-functions.php', 'gallery_custom_style_admin_panel');
	add_submenu_page('gallery-theme', __("Help & Support", 'gallery'), __("Help & Support", 'gallery'), 'manage_options', 'step-functions.php', 'gallery_step_page');	
}


function gallery_hook_admin_menu() {
	global $menu, $submenu, $_wp_submenu_nopriv;
	
	$found = false;
	foreach ($menu as $key=>$val) {
		if ($val[2] == 'edit.php?post_type=exhibition') {
			unset($menu[$key]);
			$found = true;
		}
	}
	
	if (!$found && isset($submenu['gallery-theme']) && isset($submenu['gallery-theme'][0])) {
		unset($submenu['gallery-theme']);
	}
}


function gallery_landing_page() {
    ?>
<div id="faq-panel">
	<h2><?php _e("Getting started with the Gallery theme", 'gallery'); ?></h2>
	<div class="faqsection">
		<div class="faqtitle">
		<h2><?php _e("Set up your home page:", 'gallery'); ?>
			 <a class="button" href="admin.php?page=home-functions.php">
			<?php _e("Set up home page", 'gallery'); ?>
			</a>&nbsp;&nbsp;<a href="admin.php?page=step-functions.php" target"_blank" class="button">
			<?php _e("Step by step guide", 'gallery'); ?>
			</a></h2>
		</div>
		<div class="note">
			<?php _e("Add a logo and set up your slideshow using the 'Home Page' menu.", 'gallery'); ?>
		</div>
	</div>
	
	<div class="faqsection">
		<div class="faqtitle">
		<h2><?php _e("Set up your exhibitions:", 'gallery'); ?>
			<a class="button" href="edit.php?post_type=exhibition">
			<?php _e("Add an exhibition", 'gallery'); ?>
			</a>&nbsp;&nbsp;<a class="button" href="admin.php?page=step-functions.php" target"_blank">
			<?php _e("Step by step guide", 'gallery'); ?>
			</a></h2>
		</div>
		<div class="note">
			<?php _e("Add, edit or delete exhibitions through the 'Exhibitions' menu.", 'gallery'); ?>
		</div>
	</div>
	
	<div class="faqsection">
		<div class="faqtitle">
		<h2>
			<?php _e("Set up your menus:", 'gallery'); ?>
			<a class="button" href="nav-menus.php">
			<?php _e("Click to set up menus", 'gallery'); ?>
				</a>&nbsp;&nbsp;<a class="button" href="admin.php?page=step-functions.php" target"_blank">
					<?php _e("Step by step guide", 'gallery'); ?>
					</a></h2>
		</div>
		<div class="note">
			<?php _e("Under the Appearance menu click 'Menus' and create a new menu then add each of your Exhibitions.", 'gallery'); ?>
		</div>
	</div>
	
	<div class="faqsection">
		<div class="faqtitle">
		<h2><?php _e("Choose theme style:", 'gallery'); ?> 
			<a class="button" href="admin.php?page=gallery-themes.php"><?php _e("Click to choose style", 'gallery'); ?>
				</a>&nbsp;&nbsp;
				<a class="button" href="admin.php?page=step-functions.php" target"_blank"><?php _e("Step by step guide", 'gallery'); ?>
					</a></h2>
		</div>
		<div class="note">
			<?php _e("Click the menu item 'Preset Styles' and pick a style to use.", 'gallery'); ?>
		</div>
	</div>
	
	<div class="faqsection">
		<div class="faqtitle">
		<h2><?php _e("Enjoy your theme and explore", 'gallery'); ?>
			</h2>
			<br />	<br />
		<p>
			
		<a class="button" href="admin.php?page=step-functions.php"><?php _e("Step by step guides", 'gallery'); ?>
			</a>
		<a class="button" href="admin.php?page=functions.php"><?php _e("Explore advanced options", 'gallery'); ?>
			</a>
		<a class="button" href="admin.php?page=styling-functions.php"><?php _e("Create your own style", 'gallery'); ?>
			</a>
		<br />	<br />
		</p>
		</div>
	</div>
	
	<div class="clear"></div>
</div>
<?php
}

function gallery_step_page() {
    ?>
<div id="step-panel">
		<h2><?php _e("Help &amp; Support for the Gallery theme", 'gallery'); ?></h2>
		<div class="note">
			<?php _e("In our Help &amp; Support section you will find step by step guides for this theme.  Should you at any time require further support you can visit our", 'gallery'); ?>
			 <a href="http://premium.wpmudev.org/forums/"><?php _e("Forums", 'gallery'); ?></a>.
		</div>
		<div class="accordionButton"><h2><?php _e("How do I set up my home page?", 'gallery'); ?></h2></div>
		<div class="accordionContent">
			<h4><?php _e("Step one: Click Home Page in the Gallery theme menu.", 'gallery'); ?></h4>
			<div class="stepimage_wrapper">
				<div id="homepage_one"></div>
			</div>
			<h4><?php _e("Step two: Select browse then find the image you want to use for your logo on your computer. Click 'Upload and Crop'", 'gallery'); ?></h4>
			<div class="stepimage_wrapper">
				<div id="homepage_two"></div>
			</div>
			<h4><?php _e("Step three: Use the cropping tool to crop your image - it will scale to the maximum logo size automatically.", 'gallery'); ?></h4>
			<div class="stepimage_wrapper">
				<div id="homepage_three"></div>
			</div>
			<h4><?php _e("Step four: Click 'Save logo'.", 'gallery'); ?></h4>
			<div class="stepimage_wrapper">
				<div id="homepage_four"></div>
			</div>
			<h4><?php _e("Step five: Next add some slides.", 'gallery'); ?></h4>
			<div class="stepimage_wrapper">
				<div id="homepage_five"></div>
			</div>
			<h4><?php _e("Step six: Add a title, caption and browse for your image to be used in slide. Click 'Upload and Crop'.", 'gallery'); ?></h4>
			<div class="stepimage_wrapper">
				<div id="homepage_six"></div>
			</div>
			<h4><?php _e("Step seven: Crop the slide image - there is a fixed cropping size anything smaller will just be rejected. Click 'Save Slide'", 'gallery'); ?></h4>
			<div class="stepimage_wrapper">
				<div id="homepage_seven"></div>
			</div>
			<h4><?php _e("Step eight: Once saved your image can be edited, deleted or you can add a new one.", 'gallery'); ?></h4>
			<div class="stepimage_wrapper">
				<div id="homepage_eight"></div>
			</div>
		</div>
		<div class="accordionButton"><h2><?php _e("How do I add an exhibition?", 'gallery'); ?></h2></div>
		<div class="accordionContent">
			<h4><?php _e("Step one: Click the exhibitions menu.", 'gallery'); ?></h4>
			<div class="stepimage_wrapper">
				<div id="exhibition_one"></div>
			</div>
			<h4><?php _e("Step two: Click 'Add exhibition'.", 'gallery'); ?>
				</h4>
			<div class="stepimage_wrapper">
				<div id="exhibition_two"></div>
			</div>
			
			<h4><?php _e("Step three: The exhibitions screen.", 'gallery'); ?>
				</h4>
			<div class="stepimage_wrapper">
				<div id="exhibition_three"></div>
			</div>
			
			<h4><?php _e("Step four: Add a title.", 'gallery'); ?>
				</h4>
			<div class="stepimage_wrapper">
				<div id="exhibition_four"></div>
			</div>
			
			<h4><?php _e("Step five: Click 'add new media'.", 'gallery'); ?>
				</h4>
			<div class="stepimage_wrapper">
				<div id="exhibition_five"></div>
			</div>		
			
			<h4><?php _e("Step six: Click select files to add images from your computer - for a gallery to work you have to have more than one image uploaded.  Click 'Save all changes' once you have uploaded your images.", 'gallery'); ?>
				</h4>
			<div class="stepimage_wrapper">
				<div id="exhibition_six"></div>
			</div>
			
			<h4><?php _e("Step seven: Once uploaded select Gallery.", 'gallery'); ?>
				</h4>
			<div class="stepimage_wrapper">
				<div id="exhibition_seven"></div>
			</div>
			<h4><?php _e("Step eight: Set the order of the images and number of columns for your gallery then click ‘Update gallery settings’.", 'gallery'); ?>
				</h4>
			<div class="stepimage_wrapper">
				<div id="exhibition_eight"></div>
			</div>
			<h4><?php _e("Step nine: You should now have a gallery placed.  Click update to save your exhibition.", 'gallery'); ?>
				</h4>
			<div class="stepimage_wrapper">
				<div id="exhibition_nine"></div>
			</div>
			<h4><?php _e("Step ten: At any time if you want to edit just click on the Exhibitions ‘Edit’.  Should you wish to delete or remove an Exhibition just hover over and click Trash", 'gallery'); ?>
				</h4>
			<div class="stepimage_wrapper">
				<div id="exhibition_ten"></div>
			</div>
		</div>
		<div class="accordionButton"><h2><?php _e("How do I add my exhibitions to a menu?", 'gallery'); ?></h2></div>
		<div class="accordionContent">
			<h4><?php _e("Step one: Click the Appearance menu then select 'Menus' in the submenus.", 'gallery'); ?>
				</h4>
			<div class="stepimage_wrapper">
				<div id="menu_one"></div>
			</div>
			<h4><?php _e("Step two: Add a name for your menu and click save.", 'gallery'); ?>
				</h4>
			<div class="stepimage_wrapper">
				<div id="menu_two"></div>
			</div>
			<h4><?php _e("Step three: To the left find the Exhibitions list and select which ones to add to your menu.  Click 'add to menu' to add them.", 'gallery'); ?>
				</h4>
			<div class="stepimage_wrapper">
				<div id="menu_three"></div>
			</div>
			<h4><?php _e("Step three note: If you can not find the Exhibitions list turn it on under screen options.", 'gallery'); ?>
				</h4>
			<div class="stepimage_wrapper">
				<div id="menu_threea"></div>
			</div>
			<h4><?php _e("Step four: You can edit your menu items by clicking the arrow if you want.  You can change details and even reorder them by dragging and dropping.  Once done save the menu.", 'gallery'); ?>
				</h4>
			<div class="stepimage_wrapper">
				<div id="menu_four"></div>
			</div>
			<h4><?php _e("Step five: To the left top under the Help box using the drop downs select Exhibitions and your menu.  Click save and you've set up your menu.", 'gallery'); ?>
				</h4>
			<div class="stepimage_wrapper">
				<div id="menu_five"></div>
			</div>

		</div>

			<div class="accordionButton"><h2><?php _e("How do I add create a Contact Page?", 'gallery'); ?></h2></div>
			<div class="accordionContent">
				<h4><?php _e("Step one: You should have a page automatically appear called Contact when you load the theme.", 'gallery'); ?>
					</h4>
				<div class="stepimage_wrapper">
					<div id="page_one"></div>
				</div>
						<h4><?php _e("Step two: If your page is not there please create a new page and assign the page template 'Contact'.", 'gallery'); ?>
							</h4>
						<div class="stepimage_wrapper">
							<div id="page_two"></div>
						</div>
			</div>
		
					<div class="accordionButton"><h2><?php _e("How do I create a gallery page?", 'gallery'); ?></h2></div>
					<div class="accordionContent">
								<h4><?php _e("Step one:  If you want to have another gallery page that doesn't use the Exhibitions post type you can easily using the page template.  Just create a new page and assign the page template 'Gallery'.", 'gallery'); ?>
									</h4>
								<div class="stepimage_wrapper">
									<div id="page_two"></div>
								</div>
					</div>
					
							<div class="accordionButton"><h2><?php _e("How do I create a full width page?", 'gallery'); ?></h2></div>
							<div class="accordionContent">
										<h4><?php _e("Step one: Create a new page and assign the page template 'Full width'.", 'gallery'); ?>
											</h4>
										<div class="stepimage_wrapper">
											<div id="page_two"></div>
										</div>
							</div>
							
									<div class="accordionButton"><h2><?php _e("How do I create a news page of my blog posts?", 'gallery'); ?></h2></div>
									<div class="accordionContent">
												<h4><?php _e("Step one: Create a new page and assign the page template 'Blog news'.", 'gallery'); ?>
													</h4>
												<div class="stepimage_wrapper">
													<div id="page_two"></div>
												</div>
									</div>
									
												<div class="accordionButton"><h2><?php _e("How do I set the slideshow to full width?", 'gallery'); ?></h2></div>
												<div class="accordionContent">
															<h4><?php _e("Step one: Under Advanced Options change the slideshow to full.  You will have to recreate your slides for this to work fully.", 'gallery'); ?>
																</h4>
															<div class="stepimage_wrapper">
																<div id="page_three"></div>
															</div>
												</div>
	
	<div class="clear"></div>
</div>
<?php
}

function gallery_allowed_image($file_name, $mime) {
	global $gallery_allowed_image_types;
	
	if (isset($gallery_allowed_image_types[$mime]) && preg_match('/'.$gallery_allowed_image_types[$mime].'$/i', $file_name) > 0) {
		return true;
	}
	return false;
}

function gallery_image_extension($mime) {
	global $gallery_allowed_image_types;
	
	if (isset($gallery_allowed_image_types[$mime])) {
		return $gallery_allowed_image_types[$mime];
	}
	return false;
}

function gallery_size_to_int($str) {
	if (preg_match('/^[0-9]+[KMG]{1}$/', $str) > 0) {
		$val = intval(substr($str, 0, -1));
		$size = substr($str, -1);
		
		switch ($size) {
			case 'G':
				$val *= 1024;
			case 'M':
				$val *= 1024;
			case 'K':
				$val *= 1024;
			default:
				$val *= 1;
		}
		return $val;
	}
	return intval($str);
}

function gallery_pretty_size($val) {
	$turns = 0;
	while (round($val/1024) > 0) {
		$val = $val/1024;
		$turns ++;
	}
	
	switch ($turns) {
		case 3:
			return "{$val} GB";
		case 2:
			return "{$val} MB";
		case 1:
			return "{$val} KB";
		default:
			return "{$val} B";
	}
}

function gallery_max_upload() {
	$sizes = array(gallery_size_to_int(ini_get('upload_max_filesize')), gallery_size_to_int(ini_get('memory_limit')), gallery_size_to_int(ini_get('post_max_size')));
	sort($sizes);
	
	return $sizes[0];
}

function gallery_get_image_height($image) {
	$size = getimagesize($image);
	$height = $size[1];
	return $height;
}

function gallery_get_image_width($image) {
	$size = getimagesize($image);
	$width = $size[0];
	return $width;
}

function gallery_resize_image($thumb_image_name, $image, $width, $height, $start_width, $start_height, $scale){
	list($imagewidth, $imageheight, $imageType) = getimagesize($image);
	$imageType = image_type_to_mime_type($imageType);
	$newImageWidth = ceil($width * $scale);
	$newImageHeight = ceil($height * $scale);
	$newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
	
	switch($imageType) {
		case "image/gif":
		$source=imagecreatefromgif($image);
		break;
		case "image/pjpeg":
		case "image/jpeg":
		case "image/jpg":
		$source=imagecreatefromjpeg($image);
		break;
		case "image/png":
		case "image/x-png":
		$source=imagecreatefrompng($image);
		break;
	}
	
	imagecopyresampled($newImage,$source,0,0,$start_width,$start_height,$newImageWidth,$newImageHeight,$width,$height);
	
	switch($imageType) {
		case "image/gif":
		imagegif($newImage,$thumb_image_name);
		break;
		case "image/pjpeg":
		case "image/jpeg":
		case "image/jpg":
		imagejpeg($newImage,$thumb_image_name,90);
		break;
		case "image/png":
		case "image/x-png":
		imagepng($newImage,$thumb_image_name);
		break;
	}
	
	chmod($thumb_image_name, 0777);
	return $thumb_image_name;
}


function gallery_home_page() {
	global $wpdb, $blog_id, $gallery_allowed_image_types, $gallery_allowed_image_ext;
	$thumb_photo_location = "";
	?>
	<div id="homeitem-panel">
		<h2><?php _e("First up add a logo", 'gallery'); ?></h2>
			<div class="homeitemsection">
	<?php
	$gallery_allowed_image_types = array('image/pjpeg'=>"jpg",'image/jpeg'=>"jpg",'image/jpg'=>"jpg",'image/png'=>"png",'image/x-png'=>"png",'image/gif'=>"gif");
	$gallery_allowed_image_ext = array_unique($gallery_allowed_image_types); // do not change this
	
	$thumb_width = 400;
	$thumb_height = 100;
	
	$upload_dir = wp_upload_dir();
	$upload_path = $upload_dir['path'];
	$upload_url = $upload_dir['url'];
	
	// Slide sizes
	if (get_option('dev_gallery_slideshowsize', 'partial') == 'full') {
		$slide_width = 960;
		$slide_height = 450;	
	} else {
		$slide_width = 750;
		$slide_height = 350;
	}
	
	if ( !empty($wpdb->base_prefix) ) {
		$db_prefix = $wpdb->base_prefix;
	} else {
		$db_prefix = $wpdb->prefix;
	}
	
	foreach ($gallery_allowed_image_ext as $ext) {
		$large_photo_exists = file_exists($upload_path . '/'. $blog_id . '.'. $ext);
		
		if ($large_photo_exists) {
			$large_photo_name = $blog_id . '.'. $ext;
			$thumb_photo_name = $blog_id . 's.'. $ext;
			$large_photo_location = $upload_path . '/' . $large_photo_name;
			$thumb_photo_location = $upload_path . '/' . $thumb_photo_name;
                        $large_photo_url = $upload_url . '/' . $large_photo_name;
                        $thumb_photo_url = $upload_url . '/' . $thumb_photo_name;
			break;
		}
	}
	
	if (isset($_POST['theme_action4']) && $_POST['theme_action4'] == 'logo') {
		if (isset($_POST["upload"])) {
			if (isset($_FILES['image']) && isset($_FILES['image']['error']) && $_FILES['image']['error'] > 0) {
				echo '<script type="text/javascript">window.location = "admin.php?page=home-functions.php&message=invalid-file"; </script>';
				exit();
			} else {
				//Get the file information
				$userfile_name = $_FILES['image']['name'];
				$userfile_name = str_replace(" ","-",$userfile_name);
				
				$userfile_tmp = $_FILES['image']['tmp_name'];
				$userfile_size = $_FILES['image']['size'];
				
				if (gallery_allowed_image($userfile_name, $_FILES['image']['type'])) {
					$large_image_location = $upload_path . '/'. $blog_id . '.'. gallery_image_extension($_FILES['image']['type']);
					$large_image_url = $upload_url . '/'. $blog_id . '.'. gallery_image_extension($_FILES['image']['type']);
					if(ereg('[^a-zA-Z0-9 ._.-]', $userfile_name)){
						echo '<script type="text/javascript">window.location = "admin.php?page=home-functions.php&message=invalid-file-name"; </script>';
						exit();
					} else {
						move_uploaded_file($userfile_tmp, $large_image_location);
						chmod($large_image_location, 0777);
						update_option('dev_gallery_large_logo', $large_image_url);
					}
				} else {
					echo '<script type="text/javascript">window.location = "admin.php?page=home-functions.php&message=invalid-file"; </script>';
					exit();
				}	
			}
		}
		
		foreach ($gallery_allowed_image_ext as $ext) {
			$large_photo_exists = file_exists($upload_path . '/'. $blog_id . '.'. $ext);
			
			if ($large_photo_exists) {
				$large_photo_name = $blog_id . '.'. $ext;
				$thumb_photo_name = $blog_id . 's.'. $ext;
				$large_photo_location = $upload_path . '/' . $large_photo_name;
				$thumb_photo_location = $upload_path . '/' . $thumb_photo_name;
                                $large_photo_url = $upload_url . '/' . $large_photo_name;
                                $thumb_photo_url = $upload_url . '/' . $thumb_photo_name;
				break;
			}
		}
	
		if (isset($_POST["delete"])) {
			if (is_file($large_photo_location)) {
				unlink($large_photo_location);
				update_option('dev_gallery_large_logo', '');
			}
			if (is_file($thumb_photo_location)) {
				unlink($thumb_photo_location);
				update_option('dev_gallery_cropped_logo', '');
			}
			
			$large_photo_exists = false;
			
			echo '<script type="text/javascript">window.location = "admin.php?page=home-functions.php&message=logo-deleted"; </script>';
			exit();
		}
		
		if (isset($_POST["upload_thumbnail"]) && $large_photo_exists) {
			$x1 = $_POST["x1"];
			$y1 = $_POST["y1"];
			$x2 = $_POST["x2"];
			$y2 = $_POST["y2"];
			$w = $_POST["w"];
			$h = $_POST["h"];
			$scale = $thumb_width/$w;
			
			$cropped = gallery_resize_image($thumb_photo_location, $large_photo_location,$w,$h,$x1,$y1,$scale);
			update_option('dev_gallery_cropped_logo', $thumb_photo_url);
			
			echo '<script type="text/javascript">window.location = "admin.php?page=home-functions.php&message=logo-cropped"; </script>';
			exit();
		}
	}
	
	$thumb_photo_exists = file_exists($thumb_photo_location);
	
	?>
	<h2><?php _e('Logo', 'gallery'); ?></h2>
	<?php
	if (isset($_REQUEST['message'])) {
		switch ($_REQUEST['message']) {
			case "logo-cropped":
				echo "<div class=\"updated fade\">" . __('Logo cropped', 'gallery'). "<div class=\"clear\"></div></p>";	
				break;
			case "logo-deleted":
				echo "<div class=\"updated fade\">" . __('Logo deleted', 'gallery'). "<div class=\"clear\"></div></p>";
				break;
			case "invalid-file-name":
				echo "<div class=\"error fade\">" . __('The image name contains invalid characters, rename it and try upload it again', 'gallery') . "<div class=\"clear\"></div></p>";
				break;
			case "invalid-file":
				echo "<div class=\"error fade\">" . sprintf(__('The image should be jpg, png or gif with a maximum file size of %s', 'gallery'), gallery_pretty_size(gallery_max_upload())) . "<div class=\"clear\"></div></p>";
				break;
		}
	}
	
	if( $large_photo_exists && (!$thumb_photo_exists || isset($_POST['recrop']))) {
		$current_large_image_width = gallery_get_image_width($large_photo_location);
		$current_large_image_height = gallery_get_image_height($large_photo_location);
		?>
		<h3><?php _e('Crop And Save Your Logo', 'gallery'); ?></h3>
		<div>
		<img src="<?php echo "$upload_url/$large_photo_name"; ?>" style="clear: both; margin-bottom: 10px;" id="thumbnail" alt="<?php _e('Create Thumbnail', 'gallery'); ?>" />
		<form name="thumbnail" action="admin.php?page=home-functions.php" method="post">
		<input type="hidden" name="theme_action4" value="logo" />
		<input type="hidden" name="x1" value="" id="x1" />
		<input type="hidden" name="y1" value="" id="y1" />
		<input type="hidden" name="x2" value="" id="x2" />
		<input type="hidden" name="y2" value="" id="y2" />
		<input type="hidden" name="w" value="" id="w" />
		<input type="hidden" name="h" value="" id="h" />
		<input class="button" type="submit" name="upload_thumbnail" value="<?php _e('Save Logo', 'gallery'); ?>" id="save_thumb" />
		<a class="button" href="admin.php?page=home-functions.php"><?php _e('Cancel', 'gallery'); ?></a>
		</form>
		</div>
		<script type="text/javascript">
		(function ($) {
			function preview(img, selection) {
				var scaleX = <?php echo $thumb_width;?> / selection.width;
				var scaleY = <?php echo $thumb_height;?> / selection.height;
			
				$('#thumbnail + div > img').css({
					width: Math.round(scaleX * <?php echo $current_large_image_width;?>) + 'px',
					height: Math.round(scaleY * <?php echo $current_large_image_height;?>) + 'px',
					marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px',
					marginTop: '-' + Math.round(scaleY * selection.y1) + 'px'
				});
				$('#x1').val(selection.x1);
				$('#y1').val(selection.y1);
				$('#x2').val(selection.x2);
				$('#y2').val(selection.y2);
				$('#w').val(selection.width);
				$('#h').val(selection.height);
			}
			
			$(document).ready(function () {
				$('#save_thumb').click(function() {
					var x1 = $('#x1').val();
					var y1 = $('#y1').val();
					var x2 = $('#x2').val();
					var y2 = $('#y2').val();
					var w = $('#w').val();
					var h = $('#h').val();
					if(x1=="" || y1=="" || x2=="" || y2=="" || w=="" || h==""){
						alert("<?php _e("You must make a selection first",'gallery'); ?>");
						return false;
					}else{
						return true;
					}
				});
			});
			
			function selectionStart(img, selection) {
				width: <?php echo $thumb_width; ?>;
				height: <?php echo $thumb_height; ?>;
			}
			
			$(window).load(function () {
				$('#thumbnail').imgAreaSelect({
					onSelectStart: selectionStart,
					resizable: true,
					x1: 0, y1: 0,
					x2: <?php echo min($current_large_image_width, $thumb_width); ?>,
					y2: <?php echo min($current_large_image_height, $thumb_height); ?>,
					aspectRatio: '<?php echo $thumb_width/$thumb_height; ?>:1',
					onSelectEnd: preview
				});
			});
		})(jQuery);
		</script>
	<?php } else if ($thumb_photo_exists) { ?>
		<img src="<?php echo "$upload_url/$thumb_photo_name"; ?>" style="clear: both; margin-bottom: 10px;" />
		<form name="thumbnail" action="admin.php?page=home-functions.php" method="post" enctype="multipart/form-data">
			<p><input class="button" type="submit" name="delete" value="<?php _e('Delete Logo', 'gallery'); ?>" />
			<input class="button" type="submit" name="recrop" value="<?php _e('Crop original image', 'gallery'); ?>" /></p>
			<input type="hidden" name="theme_action4" value="logo" />
		</form>
	<?php } else { ?>
		<h3><?php _e('Upload logo', 'gallery'); ?></h3>
		<form name="thumbnail" action="admin.php?page=home-functions.php" method="post" enctype="multipart/form-data">
			<input type="hidden" name="theme_action4" value="logo" />
			<input type="hidden" name="MAX_FILE_SIZE" value="<?php print gallery_max_upload(); ?>" />
			<p><input type="file" name="image" size="50" class="upz" /></p>
			<p><input class="button" type="submit" name="upload" value="<?php _e('Upload and Crop', 'gallery'); ?>" /></p>
		</form>
	<?php } ?>
	</div>
	<h2><?php _e("Next, create your slideshow", 'gallery'); ?></h2>
		<div class="homeitemsection">
				<h2><?php _e('Slides', 'gallery'); ?></h2>
	<a class="button add-new-h2" href="admin.php?page=home-functions.php&theme_action4=add-slide"><?php _e('Add Slide', 'gallery'); ?></a></h2>
	
	<?php
	if (isset($_REQUEST['message'])) {
		switch ($_REQUEST['message']) {
			case "slide-deleted":
				echo "<div class=\"updated fade\">" . __('Slide deleted', 'gallery'). "</p>";	
				break;
			case "missing-slide":
				echo "<div class=\"error fade\">" . __('We cannot find the slide', 'gallery'). "</p>";
				break;
			case "slide-saved":
				echo "<div class=\"updated fade\">" . __('Slide saved', 'gallery'). "</p>";
				break;
			case "slide-save-failed":
				echo "<div class=\"error fade\">" . __('There was a issue saving the slide', 'gallery'). "</p>";
				break;
			case "invalid-file-name":
				echo "<div class=\"error fade\">" . __('The image name contains invalid characters, rename it and try upload it again', 'gallery') . "</p>";
				break;
			case "invalid-file":
				echo "<div class=\"error fade\">" . sprintf(__('The image should be jpg, png or gif with a maximum file size of %s', 'gallery'), gallery_pretty_size(gallery_max_upload())) . "</p>";
				break;
			case "file-too-small":
				echo "<div class=\"error fade\">" . sprintf(__('The image should have a minimum size of %s', 'gallery'), "{$slide_width}x{$slide_height}") . "</div>";
				break;
		}
	}
	
	
	//Only display the javacript if an image has been uploaded
	if (isset($_REQUEST['theme_action4']) && $_REQUEST['theme_action4'] == 'crop-slide') {
		if ( $large_photo_exists && (!$thumb_photo_exists || isset($_POST['recrop']))) {
			$current_large_image_width = gallery_get_image_width($large_photo_location);
			$current_large_image_height = gallery_get_image_height($large_photo_location);
			?>
			<h3><?php _e('Crop And Save Your Logo', 'gallery'); ?></h3>
			<div>
			<img src="<?php echo "$upload_url/$large_photo_name"; ?>" style="clear: both; margin-bottom: 10px;" id="thumbnail" alt="<?php _e('Create Thumbnail', 'gallery'); ?>" />
			<form name="thumbnail" action="admin.php?page=home-functions.php" method="post">
			<input type="hidden" name="theme_action4" value="save-slide" />
			<input type="hidden" name="x1" value="" id="x1" />
			<input type="hidden" name="y1" value="" id="y1" />
			<input type="hidden" name="x2" value="" id="x2" />
			<input type="hidden" name="y2" value="" id="y2" />
			<input type="hidden" name="w" value="" id="w" />
			<input type="hidden" name="h" value="" id="h" />
			<input class="button" type="submit" name="upload_thumbnail" value="<?php _e('Save Logo', 'gallery'); ?>" id="save_thumb" />
			</form>
			</div>
			<script type="text/javascript">
			(function ($) {
				function preview(img, selection) {
					var scaleX = <?php echo $thumb_width;?> / selection.width;
					var scaleY = <?php echo $thumb_height;?> / selection.height;
				
					$('#thumbnail + div > img').css({
						width: Math.round(scaleX * <?php echo $current_large_image_width;?>) + 'px',
						height: Math.round(scaleY * <?php echo $current_large_image_height;?>) + 'px',
						marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px',
						marginTop: '-' + Math.round(scaleY * selection.y1) + 'px'
					});
					$('#x1').val(selection.x1);
					$('#y1').val(selection.y1);
					$('#x2').val(selection.x2);
					$('#y2').val(selection.y2);
					$('#w').val(selection.width);
					$('#h').val(selection.height);
				}
				
				$(document).ready(function () {
					$('#save_thumb').click(function() {
						var x1 = $('#x1').val();
						var y1 = $('#y1').val();
						var x2 = $('#x2').val();
						var y2 = $('#y2').val();
						var w = $('#w').val();
						var h = $('#h').val();
						if(x1=="" || y1=="" || x2=="" || y2=="" || w=="" || h==""){
							alert("<?php _e("You must make a selection first",'gallery'); ?>");
							return false;
						}else{
							return true;
						}
					});
				});
				
				function selectionStart(img, selection) {
					width: <?php echo $thumb_width; ?>;
					height: <?php echo $thumb_height; ?>;
				}
				
				$(window).load(function () {
					$('#thumbnail').imgAreaSelect({
						onSelectStart: selectionStart,
						resizable: true,
						x1: 0, y1: 0,
						x2: <?php echo min($current_large_image_width, $thumb_width); ?>,
						y2: <?php echo min($current_large_image_height, $thumb_height); ?>,
						aspectRatio: '<?php echo $thumb_width/$thumb_height; ?>:1',
						onSelectEnd: preview
					});
				});
			})(jQuery);
			</script>
		<?php }
	}
	
	// Slides
	
	if (isset($_POST['theme_action4']) && $_POST['theme_action4'] == 'crop-slide') {
		if (isset($_POST["upload_thumbnail"])) {
			$x1 = $_POST["x1"];
			$y1 = $_POST["y1"];
			$x2 = $_POST["x2"];
			$y2 = $_POST["y2"];
			$w = $_POST["w"];
			$h = $_POST["h"];
			$scale = 1;
			
			$image_location = $upload_path . '/' . $_POST['file_name'];
			$thumb_location = $upload_path . '/cropped-' . $_POST['file_name'];
			$image_url = $upload_url . '/' . $_POST['file_name'];
                        $thumb_url = $upload_url . '/cropped-' . $_POST['file_name'];

			$cropped = gallery_resize_image($thumb_location, $image_location,$w,$h,$x1,$y1,$scale);
			
			if ($wpdb->insert("{$db_prefix}gallery_slides",
					  array('slide_blog_ID' => $blog_id,
						'slide_title' => $_POST['slide_title'],
						'slide_caption' => $_POST['slide_caption'],
						'slide_link' => $_POST['slide_link'],
						'slide_file_name' => $thumb_url))) {
				echo '<script type="text/javascript">window.location = "admin.php?page=home-functions.php&message=slide-saved"; </script>';
				exit();
			} else {
				echo '<script type="text/javascript">window.location = "admin.php?page=home-functions.php&message=slide-save-failed"; </script>';
				exit();
			}
		}
	}
	
	if (isset($_POST['theme_action4']) && $_POST['theme_action4'] == 'add-slide') {
		if (isset($_POST["upload"])) {
			if (isset($_FILES['image']) && isset($_FILES['image']['error']) && $_FILES['image']['error'] > 0) {
				echo '<script type="text/javascript">window.location = "admin.php?page=home-functions.php&message=invalid-file"; </script>';
				exit();
			} else {
				//Get the file information
				$userfile_name = $_FILES['image']['name'];
				$userfile_name = str_replace(" ","-",$userfile_name);
				
				$userfile_tmp = $_FILES['image']['tmp_name'];
				$userfile_size = $_FILES['image']['size'];
				
				if (gallery_allowed_image($userfile_name, $_FILES['image']['type'])) {
					$image_location = $upload_path . '/'. $blog_id . '-'. $_FILES['image']['name'];
					$image_url = $upload_url . '/'. $blog_id . '-'. $_FILES['image']['name'];
					$image_name = $blog_id . '-'. $_FILES['image']['name'];
					
					if(ereg('[^a-zA-Z0-9 ._.-]', $userfile_name)){
						echo '<script type="text/javascript">window.location = "admin.php?page=home-functions.php&message=invalid-file-name"; </script>';
					} else {
						move_uploaded_file($userfile_tmp, $image_location);
						chmod($image_location, 0777);
						
						$image_width = gallery_get_image_width($image_location);
						$image_height = gallery_get_image_height($image_location);
						
						if ($image_width >= $slide_width && $image_height >= $slide_height) {
							?>
							<h3><?php _e('Crop And Save Your Slide', 'gallery'); ?></h3>
							<div>
							<img src="<?php echo "$upload_url/$image_name"; ?>" style="clear: both; margin-bottom: 10px;" id="thumbnail" alt="<?php _e('Crop', 'gallery'); ?>" />
							<form name="thumbnail" action="admin.php?page=home-functions.php" method="post">
							<input type="hidden" name="theme_action4" value="crop-slide" />
							<input type="hidden" name="x1" value="" id="x1" />
							<input type="hidden" name="y1" value="" id="y1" />
							<input type="hidden" name="x2" value="" id="x2" />
							<input type="hidden" name="y2" value="" id="y2" />
							<input type="hidden" name="w" value="" id="w" />
							<input type="hidden" name="h" value="" id="h" />
							<input type="hidden" name="file_name" value="<?php print $image_name; ?>" id="h" />
							<input type="hidden" name="slide_title" value="<?php echo $_POST['slide_title']; ?>" id="slide_title" />
							<input type="hidden" name="slide_caption" value="<?php echo $_POST['slide_caption']; ?>" id="slide_caption" />
							<input type="hidden" name="slide_link" value="<?php echo $_POST['slide_link']; ?>" id="slide_link" />
							<input class="button" type="submit" name="upload_thumbnail" value="<?php _e('Save Slide', 'gallery'); ?>" id="save_thumb" />
							</form>
							</div>
							<script type="text/javascript">
							(function ($) {
								function preview(img, selection) {
									var scaleX = <?php echo $slide_width;?> / selection.width;
									var scaleY = <?php echo $slide_height;?> / selection.height;
								
									$('#thumbnail + div > img').css({
										width: Math.round(scaleX * <?php echo $image_width;?>) + 'px',
										height: Math.round(scaleY * <?php echo $image_height;?>) + 'px',
										marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px',
										marginTop: '-' + Math.round(scaleY * selection.y1) + 'px'
									});
									$('#x1').val(selection.x1);
									$('#y1').val(selection.y1);
									$('#x2').val(selection.x2);
									$('#y2').val(selection.y2);
									$('#w').val(selection.width);
									$('#h').val(selection.height);
								}
								
								$(document).ready(function () {
									$('#save_thumb').click(function() {
										var x1 = $('#x1').val();
										var y1 = $('#y1').val();
										var x2 = $('#x2').val();
										var y2 = $('#y2').val();
										var w = $('#w').val();
										var h = $('#h').val();
										if(x1=="" || y1=="" || x2=="" || y2=="" || w=="" || h==""){
											alert("<?php _e("You must make a selection first",'gallery'); ?>");
											return false;
										}else{
											return true;
										}
									});
								});
								
								function selectionStart(img, selection) {
									width: <?php echo $slide_width; ?>;
									height: <?php echo $slide_height; ?>;
								}
								
								$(window).load(function () {
									$('#thumbnail').imgAreaSelect({
										onSelectStart: selectionStart,
										resizable: false,
										x1: 0, y1: 0,
										x2: <?php echo min($image_width, $slide_width); ?>,
										y2: <?php echo min($image_height, $slide_height); ?>,
										aspectRatio: '<?php echo $slide_width/$slide_height; ?>:1',
										onSelectEnd: preview
									});
								});
							})(jQuery);
							</script>
							<?php
						} else {
							echo '<script type="text/javascript">window.location = "admin.php?page=home-functions.php&message=file-too-small"; </script>';
						}
					}
				} else {
					echo '<script type="text/javascript">window.location = "admin.php?page=home-functions.php&message=invalid-file"; </script>';
				}	
			}
		}
	} else if (isset($_REQUEST['theme_action4']) && $_REQUEST['theme_action4'] == 'add-slide') { ?>
	<h3><?php _e('Create your slideshow', 'gallery'); ?></h3>
	<form id="create_slide" name="create_slide" action="admin.php?page=home-functions.php" method="post" enctype="multipart/form-data"
		style="border: 1px solid #DFDFDF; display: inline-block; padding: 0px 10px">
		<p><label>
			<b><?php _e('Slide title:', 'gallery'); ?></b>
			<input type="text" id="slide_title" name="slide_title" value="" size="53" />
		</label></p>
		<p><label>
			<b><?php _e('Slide caption:', 'gallery'); ?></b><br/>
			<textarea id="slide_caption" name="slide_caption" rows="3" cols="52" ></textarea>
		</label></p>
		<p><label>
			<b><?php _e('Slide link eg: http://www.yourlink.com:', 'gallery'); ?></b><br/>
			<input type="text" id="slide_link" name="slide_link" value="" size="61" />
		</label></p>
		<input type="hidden" name="theme_action4" value="add-slide" />
		<input type="hidden" name="MAX_FILE_SIZE" value="<?php print gallery_max_upload(); ?>" />
		<p><input type="file" name="image" id="image" size="50" class="upz" /></p>
		<p>
			<input class="button" type="submit" name="upload" value="<?php _e('Upload and Crop', 'gallery'); ?>" />
			<a class="button" href="admin.php?page=home-functions.php"><?php _e('Cancel', 'gallery'); ?></a>
		</p>
	</form>
	<script type="text/javascript">
		(function ($) {
			$(window).load(function () {
				$('#create_slide').submit(function () {
					if ($('#slide_title').val() == '') {
						alert("<?php _e("Please give this slide a title",'gallery'); ?>");
						return false;
					}
					if ($('#image').val() == '') {
						alert("<?php _e("Please select a file",'gallery'); ?>");
						return false;
					}
				});
			});
		})(jQuery);
	</script>
	<?php } else if (isset($_REQUEST['theme_action4']) && $_REQUEST['theme_action4'] == 'edit-slide') {
		if (isset($_POST['save'])) {
			if ($wpdb->update("{$db_prefix}gallery_slides",
					  array('slide_title' => $_POST['slide_title'],
						'slide_caption' => $_POST['slide_caption'],
						'slide_link' => $_POST['slide_link']),
					  array('slide_ID' => $_POST['id']))) {
				echo '<script type="text/javascript">window.location = "admin.php?page=home-functions.php&message=slide-saved"; </script>';
			}
		}
		$slide = $wpdb->get_row("SELECT * FROM {$db_prefix}gallery_slides WHERE slide_ID = ".intval($_REQUEST['id']).";");	
		if ($slide) {
			$image_location = $slide->slide_file_name;
	?>
	<h3><?php _e('Edit slide', 'gallery'); ?></h3>
	<form id="create_slide" name="create_slide" action="admin.php?page=home-functions.php" method="post" enctype="multipart/form-data"
		style="border: 1px solid #DFDFDF; display: inline-block; padding: 0px 10px">
		<p><label>
			<b><?php _e('Slide title:', 'gallery'); ?></b>
			<input type="text" id="slide_title" name="slide_title" value="<?php print $slide->slide_title; ?>" size="53" />
		</label></p>
		<p><label>
			<b><?php _e('Slide caption:', 'gallery'); ?></b><br/>
			<textarea id="slide_caption" name="slide_caption" rows="3" cols="52" ><?php print $slide->slide_caption; ?></textarea>
		</label></p>
		<p><label>
			<b><?php _e('Slide link:', 'gallery'); ?></b><br/>
			<input type="text" id="slide_link" name="slide_link" value="<?php print $slide->slide_link; ?>" size="61" />
		</label></p>
		<input type="hidden" name="theme_action4" value="edit-slide" />
		<input type="hidden" name="id" value="<?php print $slide->slide_ID; ?>" />
		<?php //if (file_exists($image_location)) { ?>
		<p><img src="<?php echo "{$slide->slide_file_name}"; ?>" style="clear: both; margin-bottom: 10px;" /></p>
		<?php //} ?>
		<p>
			<input class="button" type="submit" name="save" value="<?php _e('Save', 'gallery'); ?>" />
			<a class="button" href="admin.php?page=home-functions.php&theme_action4=delete-slide&id=<?php print $_REQUEST['id']; ?>"><?php _e('Delete slide', 'gallery'); ?></a>
			<a class="button" href="admin.php?page=home-functions.php"><?php _e('Cancel', 'gallery'); ?></a>
		</p>
	</form>
	
	<script type="text/javascript">
		(function ($) {
			$(window).load(function () {
				$('#create_slide').submit(function () {
					if ($('#slide_title').val() == '') {
						alert("<?php _e("Please give this slide a title",'gallery'); ?>");
						return false;
					}
				});
			});
		})(jQuery);
	</script>
	<?php
		} else {
			echo '<script type="text/javascript">window.location = "admin.php?page=home-functions.php&message=missing-slide"; </script>';
			exit();
		}
	} else if (isset($_REQUEST['theme_action4']) && $_REQUEST['theme_action4'] == 'delete-slide') {
		$slide = $wpdb->get_row("SELECT * FROM {$db_prefix}gallery_slides WHERE slide_ID = ".intval($_REQUEST['id']).";");	
		if ($slide) {
			$image_location = $slide->slide_file_name;
			
			if ($wpdb->query("DELETE FROM {$db_prefix}gallery_slides WHERE slide_ID = ".intval($_REQUEST['id']).";")
			    && file_exists($image_location)) {
				unlink($image_location);
				echo '<script type="text/javascript">window.location = "admin.php?page=home-functions.php&message=slide-deleted"; </script>';
				exit();
			}
		} else {
			echo '<script type="text/javascript">window.location = "admin.php?page=home-functions.php&message=missing-slide"; </script>';
			exit();
		}
	}
	?>
	
	<h3><?php _e('Existing slides', 'gallery'); ?></h3>
	<?php
	
	$slides = $wpdb->get_results("SELECT * FROM {$db_prefix}gallery_slides WHERE slide_blog_id = ".intval($blog_id).";");
	
	if (is_array($slides) && count($slides) > 0) {
	?>
	<table cellspacing="5" cellpadding="5">
	<?php
		foreach ($slides as $slide) {
	?>
		<tr>
			<td width="100"><?php print $slide->slide_title; ?></td>
			<td><a class="button" href="admin.php?page=home-functions.php&theme_action4=edit-slide&id=<?php print $slide->slide_ID; ?>"><?php _e('Edit', 'gallery'); ?></a></td>
		</tr>
	<?php
		}
	?>
	</table>
	<?php
	} else {
		echo __("You haven't created any slides, Click on 'Add slide' to add a new slide.", 'gallery');
	}
	?>
	</div>
	</div>
	<?php
}

function gallery_slide_blog_install() {
	global $wpdb;
	if ( !empty($wpdb->base_prefix) ) {
		$db_prefix = $wpdb->base_prefix;
	} else {
		$db_prefix = $wpdb->prefix;
	}
	if (get_option( "gallery_installed" ) == '') {
		add_option( 'gallery_installed', 'no' );
	}

	if (get_option( "gallery_installed" ) == "yes") {
		// do nothing
		if ($wpdb->get_var("SHOW COLUMNS FROM `" . $db_prefix . "gallery_slides` LIKE 'slide_link'") != 'slide_link') {
			$wpdb->query("ALTER TABLE `" . $db_prefix . "gallery_slides` ADD `slide_link` VARCHAR( 255 ) NOT NULL ");
		}
	} else {

		$gallery_table1 = "CREATE TABLE `" . $db_prefix . "gallery_slides` (
  `slide_ID` bigint(20) unsigned NOT NULL auto_increment,
  `slide_blog_ID` bigint(20) NOT NULL,
  `slide_title` VARCHAR(255) NOT NULL,
  `slide_caption` TEXT,
  `slide_file_name` VARCHAR(255),
  `slide_link` VARCHAR(255),
  PRIMARY KEY  (`slide_ID`)
) ENGINE=MyISAM;";

		$wpdb->query( $gallery_table1 );
		update_option( "gallery_installed", "yes" );
	}
}

// Long function name because gallery_columns conflicts with
// http://wordpress.org/extend/plugins/gallery-columns/
function gallery_theme_wp_gallery_columns($content) {
	global $post;
	
	if (get_post_type($post) != 'exhibition') {
		return $content;
	}
	
	$columns = 5;
	
	if ( !empty($exclude) ) {
		$exclude = is_array($exclude) ? $exclude : preg_split('/[\s]*[,][\s]*/', $exclude);
		
		if ( in_array($post->ID, $exclude) ) 
			return $content;
	}
		
	$pattern = array(
		'#(\[gallery(.*?)columns="([0-9])"(.*?)\])#ie',
		'#(\[gallery\])#ie',
		'#(\[gallery(.*?)\])#ie'
	);
	$replace = 'stripslashes(strstr("\1", "columns=\"$columns\"") ? "\1" : "[gallery \2 \4 columns=\"$columns\"]")';
	
	return preg_replace($pattern, $replace, $content);		
}

function gallery_theme_wp_gallery_link($content) {
	global $post;
	
	if (get_post_type($post) != 'exhibition') {
		return $content;
	}
	
	$link = "file";
	
	if ( !empty($exclude) ) {
		$exclude = is_array($exclude) ? $exclude : preg_split('/[\s]*[,][\s]*/', $exclude);
		
		if ( in_array($post->ID, $exclude) ) 
			return $content;
	}
		
	$pattern = array(
		'#(\[gallery(.*?)link="([a-z])"(.*?)\])#ie',
		'#(\[gallery\])#ie',
		'#(\[gallery(.*?)\])#ie'
	);
	$replace = 'stripslashes(strstr("\1", "link=\"$link\"") ? "\1" : "[gallery \2 \4 link=\"$link\"]")';
	
	return preg_replace($pattern, $replace, $content);		
}

add_filter('the_content', 'gallery_theme_wp_gallery_columns');
add_filter('the_content', 'gallery_theme_wp_gallery_link');
add_action('init', 'gallery_slide_blog_install');
add_filter('admin_menu', 'gallery_hook_admin_menu');
add_action('admin_menu', 'gallery_pages');
add_action('admin_head', 'gallery_admin_head');
add_action('admin_menu', 'gallery_admin_register');
add_action('admin_menu', 'gallery_ready_style_admin_register');
add_action('admin_menu', 'gallery_custom_style_admin_register');
