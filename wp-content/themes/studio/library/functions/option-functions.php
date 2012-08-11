<?php
function _g($str)
{
return __($str, 'option-page');
}

$themename = "Studio";
$themeversion = "1";
$shortname = "dev";
$shortprefix = "_studio_";
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
	array("name" => __("Show custom header", 'studio'),
		"description" => __("You can show or hide the custom header, the default is off", 'studio'),
		"id" => $shortname . $shortprefix . "customheader_on",	     	
		"inblock" => "slideone",
	    "type" => "select",
		"std" => "Show",
		"options" => array("no", "yes")),
	
		array("name" => __("Show slideshow block?", 'studio'),
			"description" => __("You can show or hide the slideshow block, the default is on", 'studio'),
			"id" => $shortname . $shortprefix . "slideshow",	     	
			"inblock" => "slideone",
		    "type" => "select",
			"std" => "Show",
			"options" => array("yes", "no")),

		array("name" => __("How many slides do you want?", 'studio'),
		"description" => __("You can pick up to 6", 'studio'),
			"id" => $shortname . $shortprefix . "slide_number",	     	
			"inblock" => "slideone",
		    "type" => "select",
			"std" => "Select your slideshow contents type",
			"options" => array("1", "2", "3", "4", "5", "6")),

			array(
				"name" => __("Enter the slide one height", 'studio'),
					"description" => __("300px is the default - all other slides will auto size", 'studio'),
				"id" => $shortname . $shortprefix . "slideone_height",
		"inblock" => "slideone",
				"type" => "text",
				"std" => "",
			),

			array(
				"name" => __("Enter the slide speed", 'studio'),
					"description" => __("3000 is the default add a number such as 5000, 100000 - if you do not enter a number it will not work.  1000 = 1 second.", 'studio'),
				"id" => $shortname . $shortprefix . "slideshow_speed",
		"inblock" => "slideone",
				"type" => "text",
				"std" => "",
			),
			
	array(
		"name" => __("Enter an image url", 'studio'),
		"description" => __("Large images are 960px wide maximum.  You can upload your image in the <a href='media-new.php'>media panel</a> and copy paste the url here", 'studio'),
		"id" => $shortname . $shortprefix . "slideone_image",
		"inblock" => "slidecontent",
		"type" => "text",
		"std" => "",
	),

	array(
		"name" => __("Enter a link for your image to lead to", 'studio'),
				"description" => __("Enter your link url (include full path including http://)", 'studio'),
		"id" => $shortname . $shortprefix . "slideone_image_link",
		"inblock" => "slidecontent",
		"type" => "text",
		"std" => "",
	),

	array(
		"name" => __("Enter an alt title for your image", 'studio'),
				"description" => __("Alt text is used for SEO", 'studio'),
		"id" => $shortname . $shortprefix . "slideone_image_title",
		"inblock" => "slidecontent",
		"type" => "text",
		"std" => "",
	),

	array(
		"name" => __("Enter an image url", 'studio'),
		"description" => __("Large images are 960px wide maximum.  You can upload your image in the <a href='media-new.php'>media panel</a> and copy paste the url here", 'studio'),	
		"id" => $shortname . $shortprefix . "slidetwo_image",
		"inblock" => "slidecontent",
		"type" => "text",
		"std" => "",
	),

	array(
		"name" => __("Enter a link for your image to lead to", 'studio'),
				"description" => __("Enter your link url (include full path including http://)", 'studio'),
		"id" => $shortname . $shortprefix . "slidetwo_image_link",
		"inblock" => "slidecontent",
		"type" => "text",
		"std" => "",
	),

	array(
		"name" => __("Enter an alt title for your image", 'studio'),
		"description" => __("Alt text is used for SEO", 'studio'),
		"id" => $shortname . $shortprefix . "slidetwo_image_title",
		"inblock" => "slidecontent",
		"type" => "text",
		"std" => "",
	),

	array(
			"name" => __("Enter an image url", 'studio'),
			"description" => __("Large images are 960px wide maximum.  You can upload your image in the <a href='media-new.php'>media panel</a> and copy paste the url here", 'studio'),
		"id" => $shortname . $shortprefix . "slidethree_image",
		"inblock" => "slidecontent",
		"type" => "text",
		"std" => "",
	),

	array(
		"name" => __("Enter a link for your image to lead to", 'studio'),
				"description" => __("Enter your link url (include full path including http://)", 'studio'),
		"id" => $shortname . $shortprefix . "slidethree_image_link",
		"inblock" => "slidecontent",
		"type" => "text",
		"std" => "",
	),

	array(
		"name" => __("Enter an alt title for your image", 'studio'),
		"description" => __("Alt text is used for SEO", 'studio'),
		"id" => $shortname . $shortprefix . "slidethree_image_title",
		"inblock" => "slidecontent",
		"type" => "text",
		"std" => "",
	),

	array(
		"name" => __("Enter an image url", 'studio'),
		"description" => __("Large images are 960px wide maximum.  You can upload your image in the <a href='media-new.php'>media panel</a> and copy paste the url here", 'studio'),
		"id" => $shortname . $shortprefix . "slidefour_image",
		"inblock" => "slidecontent",
		"type" => "text",
		"std" => "",
	),

	array(
		"name" => __("Enter a link for your image to lead to", 'studio'),
				"description" => __("Enter your link url (include full path including http://)", 'studio'),
		"id" => $shortname . $shortprefix . "slidefour_image_link",
		"inblock" => "slidecontent",
		"type" => "text",
		"std" => "",
	),

	array(
		"name" => __("Enter an alt title for your image", 'studio'),
		"description" => __("Alt text is used for SEO", 'studio'),
		"id" => $shortname . $shortprefix . "slidefour_image_title",
		"inblock" => "slidecontent",
		"type" => "text",
		"std" => "",
	),
	
	array(
		"name" => __("Enter an image url", 'studio'),
		"description" => __("Large images are 960px wide maximum.  You can upload your image in the <a href='media-new.php'>media panel</a> and copy paste the url here", 'studio'),
		"id" => $shortname . $shortprefix . "slidefive_image",
		"inblock" => "slidecontent",
		"type" => "text",
		"std" => "",
	),

	array(
		"name" => __("Enter a link for your image to lead to", 'studio'),
				"description" => __("Enter your link url (include full path including http://)", 'studio'),
		"id" => $shortname . $shortprefix . "slidefive_image_link",
		"inblock" => "slidecontent",
		"type" => "text",
		"std" => "",
	),

	array(
		"name" => __("Enter an alt title for your image", 'studio'),
		"description" => __("Alt text is used for SEO", 'studio'),
		"id" => $shortname . $shortprefix . "slidefive_image_title",
		"inblock" => "slidecontent",
		"type" => "text",
		"std" => "",
	),
	
	array(
		"name" => __("Enter an image url", 'studio'),
		"description" => __("Large images are 960px wide maximum.  You can upload your image in the <a href='media-new.php'>media panel</a> and copy paste the url here", 'studio'),
		"id" => $shortname . $shortprefix . "slidesix_image",
		"inblock" => "slidecontent",
		"type" => "text",
		"std" => "",
	),

	array(
		"name" => __("Enter a link for your image to lead to", 'studio'),
				"description" => __("Enter your link url (include full path including http://)", 'studio'),
		"id" => $shortname . $shortprefix . "slidesix_image_link",
		"inblock" => "slidecontent",
		"type" => "text",
		"std" => "",
	),

	array(
		"name" => __("Enter an alt title for your image", 'studio'),
		"description" => __("Alt text is used for SEO", 'studio'),
		"id" => $shortname . $shortprefix . "slidesix_image_title",
		"inblock" => "slidecontent",
		"type" => "text",
		"std" => "",
	),

	array("name" => __("Show feature content block? (default is off)", 'studio'),
		"id" => $shortname . $shortprefix . "feature_show",	     	
		"inblock" => "feature",
	    "type" => "select",
		"std" => "Turn on and off the feature content block",
		"options" => array("no", "yes")),
	
	array(
		"name" => __("Feature block header", 'studio'),		"description" => __("This is the header for the entire section", 'studio'),
		"id" => $shortname . $shortprefix . "feature_header",
		"inblock" => "feature",
		"type" => "text",
		"std" => "",
	),

	array(
		"name" => __("First feature block header", 'studio'),
		"id" => $shortname . $shortprefix . "feature_blockone_header",
		"inblock" => "feature",
		"type" => "text",
		"std" => "",
	),

	array(
		"name" => __("First block description", 'studio'),
		"id" => $shortname . $shortprefix . "feature_blockone_description",
		"inblock" => "feature",
		"type" => "textarea",
		"std" => "",
	),

	array(
		"name" => __("Add an image url here if you want to have an image showing", 'studio'),
			"description" => __("You can upload your image in the <a href='media-new.php'>media panel</a> and copy paste the url here", 'studio'),
		"id" => $shortname . $shortprefix . "feature_blockone_image",
		"inblock" => "feature",
		"type" => "text",
		"std" => "",
	),

	array(
		"name" => __("Enter your image alt title here", 'studio'),
		"description" => __("Alt text is used for SEO", 'studio'),
		"id" => $shortname . $shortprefix . "feature_blockone_image_title",
		"inblock" => "feature",
		"type" => "text",
		"std" => "",
	),

	array(
		"name" => __("First block link", 'studio'),		"description" => __("Enter your link url (include full path including http://)", 'studio'),
		"id" => $shortname . $shortprefix . "feature_blockone_link",
		"inblock" => "feature",
		"type" => "text",
		"std" => "",
	),

	array(
		"name" => __("First block link title", 'studio'),		"description" => __("Title is used for SEO", 'studio'),
		"id" => $shortname . $shortprefix . "feature_blockone_link_title",
		"inblock" => "feature",
		"type" => "text",
		"std" => "",
	),
	array(
		"name" => __("Second block header", 'studio'),
		"id" => $shortname . $shortprefix . "feature_blocktwo_header",
		"inblock" => "feature",
		"type" => "text",
		"std" => "",
	),

	array(
		"name" => __("Second block description", 'studio'),
		"id" => $shortname . $shortprefix . "feature_blocktwo_description",
		"inblock" => "feature",
		"type" => "textarea",
		"std" => "",
	),

	array(
		"name" => __("Add an image url here if you want to have an image showing", 'studio'),
			"description" => __("You can upload your image in the <a href='media-new.php'>media panel</a> and copy paste the url here", 'studio'),
		"id" => $shortname . $shortprefix . "feature_blocktwo_image",
		"inblock" => "feature",
		"type" => "text",
		"std" => "",
	),

	array(
		"name" => __("Enter your image alt title here", 'studio'),
		"description" => __("Alt text is used for SEO", 'studio'),
		"id" => $shortname . $shortprefix . "feature_blocktwo_image_title",
		"inblock" => "feature",
		"type" => "text",
		"std" => "",
	),

	array(
		"name" => __("Second block link", 'studio'),		"description" => __("Enter your link url (include full path including http://)", 'studio'),
		"id" => $shortname . $shortprefix . "feature_blocktwo_link",
		"inblock" => "feature",
		"type" => "text",
		"std" => "",
	),

	array(
		"name" => __("Second block link title", 'studio'),		"description" => __("Title is used for SEO", 'studio'),
		"id" => $shortname . $shortprefix . "feature_blocktwo_link_title",
		"inblock" => "feature",
		"type" => "text",
		"std" => "",
	),
	array(
		"name" => __("Third block header", 'studio'),
		"id" => $shortname . $shortprefix . "feature_blockthree_header",
		"inblock" => "feature",
		"type" => "text",
		"std" => "",
	),

	array(
		"name" => __("Third block description", 'studio'),
		"id" => $shortname . $shortprefix . "feature_blockthree_description",
		"inblock" => "feature",
		"type" => "textarea",
		"std" => "",
	),

	array(
		"name" => __("Add an image url here if you want to have an image showing", 'studio'),
			"description" => __("You can upload your image in the <a href='media-new.php'>media panel</a> and copy paste the url here", 'studio'),
		"id" => $shortname . $shortprefix . "feature_blockthree_image",
		"inblock" => "feature",
		"type" => "text",
		"std" => "",
	),

	array(
		"name" => __("Enter your image alt title here", 'studio'),
		"description" => __("Alt text is used for SEO", 'studio'),
		"id" => $shortname . $shortprefix . "feature_blockthree_image_title",
		"inblock" => "feature",
		"type" => "text",
		"std" => "",
	),

	array(
		"name" => __("Third block link", 'studio'),
				"description" => __("Enter your link url (include full path including http://)", 'studio'),
		"id" => $shortname . $shortprefix . "feature_blockthree_link",
		"inblock" => "feature",
		"type" => "text",
		"std" => "",
	),

	array(
		"name" => __("Third block link title", 'studio'),		"description" => __("Title is used for SEO", 'studio'),
		"id" => $shortname . $shortprefix . "feature_blockthree_link_title",
		"inblock" => "feature",
		"type" => "text",
		"std" => "",
	),

	array("name" => __("Show footer feature block? (default is off)", 'studio'),
		"id" => $shortname . $shortprefix . "footfeat_show",	     	
		"inblock" => "footfeat",
	    "type" => "select",
		"std" => "Turn on and off the feature content block",
		"options" => array("no", "yes")),
	
	array(
		"name" => __("Feature block header", 'studio'),
		"description" => __("This is the header for the entire section", 'studio'),
		"id" => $shortname . $shortprefix . "footfeat_header",
		"inblock" => "footfeat",
		"type" => "text",
		"std" => "",
	),

	array(
		"name" => __("First feature block header", 'studio'),
		"id" => $shortname . $shortprefix . "footfeat_blockone_header",
		"inblock" => "footfeat",
		"type" => "text",
		"std" => "",
	),

	array(
		"name" => __("First block description", 'studio'),
		"id" => $shortname . $shortprefix . "footfeat_blockone_description",
		"inblock" => "footfeat",
		"type" => "textarea",
		"std" => "",
	),

	array(
		"name" => __("Add an image url here if you want to have an image showing", 'studio'),
			"description" => __("You can upload your image in the <a href='media-new.php'>media panel</a> and copy paste the url here", 'studio'),
		"id" => $shortname . $shortprefix . "footfeat_blockone_image",
		"inblock" => "footfeat",
		"type" => "text",
		"std" => "",
	),

	array(
		"name" => __("Enter your image alt title here", 'studio'),
		"description" => __("Alt text is used for SEO", 'studio'),
		"id" => $shortname . $shortprefix . "footfeat_blockone_image_title",
		"inblock" => "footfeat",
		"type" => "text",
		"std" => "",
	),

	array(
		"name" => __("First block link", 'studio'),		"description" => __("Enter your link url (include full path including http://)", 'studio'),
		"id" => $shortname . $shortprefix . "footfeat_blockone_link",
		"inblock" => "footfeat",
		"type" => "text",
		"std" => "",
	),

	array(
		"name" => __("First block link title", 'studio'),		"description" => __("Title is used for SEO", 'studio'),
		"id" => $shortname . $shortprefix . "footfeat_blockone_link_title",
		"inblock" => "footfeat",
		"type" => "text",
		"std" => "",
	),
	array(
		"name" => __("Second block header", 'studio'),
		"id" => $shortname . $shortprefix . "footfeat_blocktwo_header",
		"inblock" => "footfeat",
		"type" => "text",
		"std" => "",
	),

	array(
		"name" => __("Second block description", 'studio'),
		"id" => $shortname . $shortprefix . "footfeat_blocktwo_description",
		"inblock" => "footfeat",
		"type" => "textarea",
		"std" => "",
	),

	array(
		"name" => __("Add an image url here if you want to have an image showing", 'studio'),
			"description" => __("You can upload your image in the <a href='media-new.php'>media panel</a> and copy paste the url here", 'studio'),
		"id" => $shortname . $shortprefix . "footfeat_blocktwo_image",
		"inblock" => "footfeat",
		"type" => "text",
		"std" => "",
	),

	array(
		"name" => __("Enter your image alt title here", 'studio'),
				"description" => __("Alt text is used for SEO", 'studio'),
		"id" => $shortname . $shortprefix . "footfeat_blocktwo_image_title",
		"inblock" => "footfeat",
		"type" => "text",
		"std" => "",
	),

	array(
		"name" => __("Second block link", 'studio'),
		"description" => __("Enter your link url (include full path including http://)", 'studio'),
		"id" => $shortname . $shortprefix . "footfeat_blocktwo_link",
		"inblock" => "footfeat",
		"type" => "text",
		"std" => "",
	),

	array(
		"name" => __("Second block link title", 'studio'),		"description" => __("Title is used for SEO", 'studio'),
		"id" => $shortname . $shortprefix . "footfeat_blocktwo_link_title",
		"inblock" => "footfeat",
		"type" => "text",
		"std" => "",
	),
	array(
		"name" => __("Third block header", 'studio'),
		"id" => $shortname . $shortprefix . "footfeat_blockthree_header",
		"inblock" => "footfeat",
		"type" => "text",
		"std" => "",
	),

	array(
		"name" => __("Third block description", 'studio'),
		"id" => $shortname . $shortprefix . "footfeat_blockthree_description",
		"inblock" => "footfeat",
		"type" => "textarea",
		"std" => "",
	),

	array(
		"name" => __("Add an image url here if you want to have an image showing", 'studio'),
			"description" => __("You can upload your image in the <a href='media-new.php'>media panel</a> and copy paste the url here", 'studio'),
		"id" => $shortname . $shortprefix . "footfeat_blockthree_image",
		"inblock" => "footfeat",
		"type" => "text",
		"std" => "",
	),

	array(
		"name" => __("Enter your image alt title here", 'studio'),
		"description" => __("Alt text is used for SEO", 'studio'),
		"id" => $shortname . $shortprefix . "footfeat_blockthree_image_title",
		"inblock" => "footfeat",
		"type" => "text",
		"std" => "",
	),

	array(
		"name" => __("Third block link", 'studio'),
			"description" => __("Enter your link url (include full path including http://)", 'studio'),
		"id" => $shortname . $shortprefix . "footfeat_blockthree_link",
		"inblock" => "footfeat",
		"type" => "text",
		"std" => "",
	),

	array(
		"name" => __("Third block link title", 'studio'),
		"description" => __("Title is used for SEO", 'studio'),
		"id" => $shortname . $shortprefix . "footfeat_blockthree_link_title",
		"inblock" => "footfeat",
		"type" => "text",
		"std" => "",
	),

	array(
		"name" => __("Would you like to show a header", 'studio'),
		"description" => __("The header will be on all pages - default is on", 'studio'),
		"id" => $shortname . $shortprefix . "header_show",
"inblock" => "header",
		    "type" => "select",
			"std" => "Show header yes or no?",
			"options" => array("yes", "no")),

	array(
		"name" => __("Enter your header title", 'studio'),
		"description" => __("Title for your call to action header", 'studio'),
		"id" => $shortname . $shortprefix . "header_title",
"inblock" => "header",
		"type" => "textarea",
		"std" => "",
	),
	
	array(
		"name" => __("Enter your link title", 'studio'),
		"description" => __("Enter your link title for your call to action link", 'studio'),
		"id" => $shortname . $shortprefix . "header_link",
"inblock" => "header",
		"type" => "text",
		"std" => "",
	),
	
	array(
		"name" => __("Enter your link url", 'studio'),
		"description" => __("Enter your link url for your call to action link (include full path including http://)", 'studio'),
		"id" => $shortname . $shortprefix . "header_url",
"inblock" => "header",
		"type" => "text",
		"std" => "",
	),
	
	array(
		"name" => __("Enter your link alt text", 'studio'),
		"description" => __("Enter your link alt text for your call to action link", 'studio'),
		"id" => $shortname . $shortprefix . "header_alt",
"inblock" => "header",
		"type" => "text",
		"std" => "",
	),
	
	array(
		"name" => __("Enter your description", 'studio'),
		"description" => __("Enter your description for your call to action header", 'studio'),
		"id" => $shortname . $shortprefix . "header_description",
		"inblock" => "header",
		"type" => "textarea",
		"std" => "",
	),
	
		array(
			"name" => __("Enter your panel header", 'studio'),
			"description" => __("Enter a community panel header", 'studio'),
			"id" => $shortname . $shortprefix . "panel_header",
	"inblock" => "buddypress",
			"type" => "textarea",
			"std" => "",
		),
	
		array(
			"name" => __("Enter your panel header two", 'studio'),
			"description" => __("Enter a community panel header two", 'studio'),
			"id" => $shortname . $shortprefix . "panel_headertwo",
	"inblock" => "buddypress",
			"type" => "textarea",
			"std" => "",
		),
	
		array(
			"name" => __("Enter your panel description", 'studio'),
			"description" => __("Enter a description or text for your users in your community panel", 'studio'),
			"id" => $shortname . $shortprefix . "panel_description",
	"inblock" => "buddypress",
			"type" => "textarea",
			"std" => "",
		),
	
	array(
		"name" => __("Site name", 'studio'),
		"id" => $shortname . $shortprefix . "site_title",
		"inblock" => "branding",
		"type" => "text",
		"std" => "",
	),
	array("name" => __("Do you to use a custom large image logo rather than domain name text?", 'studio'),
		"description" => __("Enter your url in the next section if saying yes", 'studio'),
		"id" => $shortname . $shortprefix . "header_image",	     	
		"inblock" => "branding",
		"type" => "select",
		"std" => "Select",
		"options" => array("no", "yes")),

	array(
		"name" => __("Insert your logo full url here", 'studio'),
		"description" => __("You can upload your logo in <a href='media-new.php'>media panel</a> and copy paste the url here", 'studio'),
		"id" => $shortname . $shortprefix . "header_logo",
		"inblock" => "branding",
		"type" => "text",
		"std" => "",
	),

	array("name" => __("Do you to use a custom square image logo and your domain name text?", 'studio'),
		"description" => __("Enter your url in the next section if saying yes", 'studio'),
		"id" => $shortname . $shortprefix . "header_image_square",	     	
		"inblock" => "branding",
		"type" => "select",
		"std" => "Select",
		"options" => array("no", "yes")),

	array(
		"name" => __("Insert your square logo full url here", 'studio'),
		"description" => __("You can upload your logo in <a href='media-new.php'>media panel</a> and copy paste the url here", 'studio'),
		"id" => $shortname . $shortprefix . "header_logo_square",
		"inblock" => "branding",
		"type" => "text",
		"std" => "",
	),
	
array(
	"name" => __("Show sign up box?", 'studio'),
	"id" => $shortname . $shortprefix . "signupfeat_on",
	"inblock" => "branding",
	"type" => "select",
	"std" => "Select",
	"options" => array("no", "yes")
),

array(
	"name" => __("Sign up feature text", 'studio'),
	"id" => $shortname . $shortprefix . "signupfeat_text",
	"inblock" => "branding",
	"type" => "textarea",
	"std" => "",
),

array(
	"name" => __("Sign up button text", 'studio'),
	"id" => $shortname . $shortprefix . "signupfeat_buttontext",
	"inblock" => "branding",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("Sign up custom link (enter a custom link if don't want default ones)", 'studio'),
	"id" => $shortname . $shortprefix . "signupfeat_buttontextcustom",
	"inblock" => "branding",
	"type" => "text",
	"std" => "",
),

array(
	"name" => __("Enter footer links if want to add", 'studio'),
	"description" => __("Make sure using full html for the links ie; a href= and so on", 'studio'),
	"id" => $shortname . $shortprefix . "footer_links",
	"inblock" => "branding",
	"type" => "textarea",
	"std" => "",
),

);

function studio_admin_panel() {
	global $themename, $shortname, $options, $options2, $options3, $bp_existed, $multi_site_on;
	if ( isset($_REQUEST['saved'] )) echo '<div id="message" class="updated fade"><p><strong>'. $themename . __(' settings saved.', 'studio') . '</strong></p></div>';
	if ( isset($_REQUEST['reset'] )) echo '<div id="message" class="updated fade"><p><strong>'. $themename . __(' settings reset.', 'studio') . '</strong></p></div>';
	?>
	<div id="options-panel">
	<form action="" method="post">

	  <div id="sbtabs">
	  <div class="tabmc">
	  <ul class="ui-tabs-nav" id="tabm">
	 	<li class="first ui-tabs-selected"><a href="#slideone"><?php _e("Scripts",'studio'); ?></a></li>
		<li class=""><a href="#slidecontent"><?php _e("Slides",'studio'); ?></a></li>	  
		  <li class=""><a href="#feature"><?php _e("Feature",'studio'); ?></a></li>
		  <li class=""><a href="#footfeat"><?php _e("Footer Feature",'studio'); ?></a></li>
		  <li class=""><a href="#header"><?php _e("Header",'studio'); ?></a></li>
		  <?php if($bp_existed == 'true') { ?><li class=""><a href="#buddypress"><?php _e("BuddyPress",'studio'); ?></a></li><?php } ?>
		  <li class=""><a href="#branding"><?php _e("Branding",'studio'); ?></a></li>
		  </ul>
		  </div>

		<div class="tabc">


		<ul style="" class="ui-tabs-panel" id="slideone">
		<li>

		<h2><?php _e("Slideshow Settings", 'studio') ?></h2>


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



		<ul style="" class="list3 ui-tabs-panel ui-tabs-hide" id="slidecontent">

		<li>

		<h2><?php _e("Slide Content Settings", 'studio') ?></h2>

		<?php $value_var = 'slidecontent'; foreach ($options as $value) { ?>

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
			
		
		<ul style="" class="list6 ui-tabs-panel ui-tabs-hide" id="feature">

		<li>

		<h2><?php _e("Feature Blocks", 'studio') ?></h2>

		<?php $value_var = 'feature'; foreach ($options as $value) { ?>

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

		
		<ul style="" class="list6 ui-tabs-panel ui-tabs-hide" id="footfeat">

		<li>

		<h2><?php _e("Footer Feature", 'studio') ?></h2>

		<?php $value_var = 'footfeat'; foreach ($options as $value) { ?>

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
		
		<ul style="" class="list7 ui-tabs-panel ui-tabs-hide" id="header">

		<li>

		<h2><?php _e("Header", 'studio') ?></h2>

		<?php $value_var = 'header'; foreach ($options as $value) { ?>

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

		<h2><?php _e("BuddyPress Settings", 'studio') ?></h2>

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

		<h2><?php _e("Branding Settings", 'studio') ?></h2>

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
			<h2><?php _e("Click this to save your theme options", 'studio') ?></h2>
		<input name="save" type="submit" class="sbutton" value="<?php echo esc_attr(__('Save All Options','studio')); ?>" />
		<input type="hidden" name="theme_action" value="save" />
		</div>
		</div>
		</div>
		</form>



		<form method="post">
		<div id="resetsection">
		<div class="submit">
			<h2><?php _e("Clicking this will reset all theme options - use with caution", 'studio') ?></h2>
		<input name="reset" type="submit" class="sbutton" value="<?php echo esc_attr(__('Reset All Options','studio')); ?>" />
		<input type="hidden" name="theme_action" value="reset" />
		</div>
		</div>
		</form>


		</div>

		<?php
		}
	
$options3 = array (

array(
	"name" => __("Choose your body font", 'studio'),
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
	"name" => __("Choose your header font", 'studio'),
	"description" => __("We include google font directory fonts you can <a href='http://code.google.com/webfonts'>view here</a> ", 'studio'),
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
	"name" => __("Choose your feature wrapper background colour", 'studio'),
	"id" => $shortname . $shortprefix . "feature_colour",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your header background colour", 'studio'),
	"id" => $shortname . $shortprefix . "header_background_colour",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your body font colour", 'studio'),
	"id" => $shortname . $shortprefix . "font_colour",
	"inblock" => "text",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your link colour", 'studio'),
	"id" => $shortname . $shortprefix . "link_colour",
	"inblock" => "text",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your link hover colour", 'studio'),
	"id" => $shortname . $shortprefix . "link_hover_colour",
	"inblock" => "text",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your link visited colour", 'studio'),
	"id" => $shortname . $shortprefix . "link_visited_colour",
	"inblock" => "text",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your header colour", 'studio'),
	"id" => $shortname . $shortprefix . "header_colour",
	"inblock" => "text",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your header text shadow colour", 'studio'),
	"id" => $shortname . $shortprefix . "header_shadow_colour",
	"inblock" => "text",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your feature text colour", 'studio'),
	"id" => $shortname . $shortprefix . "feature_text_colour",
	"inblock" => "text",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your feature text shadow colour", 'studio'),
	"id" => $shortname . $shortprefix . "feature_text_shadow_colour",
	"inblock" => "text",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your site header text colour", 'studio'),
	"id" => $shortname . $shortprefix . "site_header_colour",
	"inblock" => "text",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your navigation text colour", 'studio'),
	"id" => $shortname . $shortprefix . "nav_text_colour",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your navigation background colour", 'studio'),
	"id" => $shortname . $shortprefix . "nav_background_colour",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your navigation text shadow colour", 'studio'),
	"id" => $shortname . $shortprefix . "nav_shadow_colour",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your navigation hover text colour", 'studio'),
	"id" => $shortname . $shortprefix . "nav_hover_text_colour",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your navigation hover background colour", 'studio'),
	"id" => $shortname . $shortprefix . "nav_hover_background_colour",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your navigation border colour", 'studio'),
	"id" => $shortname . $shortprefix . "nav_border_colour",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),
);


function studio_custom_style_admin_panel() {

		global $themename, $options, $options2, $options3, $bp_existed, $multi_site_on;

		if ( isset($_REQUEST['saved3'] )) echo '<div id="message" class="updated fade"><p><strong>'. $themename . __(' settings saved.', 'studio') . '</strong></p></div>';
		if ( isset($_REQUEST['reset3'] )) echo '<div id="message" class="updated fade"><p><strong>'. $themename . __(' settings reset.', 'studio') . '</strong></p></div>';
		?>

		<div id="options-panel">
		<form action="" method="post">

		  <div id="sbtabs">
		  <div class="tabmc">
		  <ul class="ui-tabs-nav" id="tabm">
		  <li class="first ui-tabs-selected"><a href="#fonts"><?php _e("Fonts",'studio'); ?></a></li>
		
		  <li class=""><a href="#layout"><?php _e("Layout Colours",'studio'); ?></a></li>
		
		  <li class=""><a href="#text"><?php _e("Text Colours",'studio'); ?></a></li>
		
		  <li class=""><a href="#navigation"><?php _e("Navigation Colours",'studio'); ?></a></li>
		  </ul>
		</div>


		<div class="tabc">


		<ul style="" class="ui-tabs-panel" id="fonts">
		<li>
			<h2><?php _e("Fonts", 'studio') ?></h2>

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
				<h2><?php _e("Layout Colours", 'studio') ?></h2>

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
					<h2><?php _e("Text colours", 'studio') ?></h2>

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
					<h2><?php _e("Navigation Colours", 'studio') ?></h2>

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
		<h2><?php _e("Click this to save your theme options", 'studio') ?></h2>
	<input name="save" type="submit" class="sbutton" value="<?php echo esc_attr(__('Save All Options','studio')); ?>" />
	<input type="hidden" name="theme_action3" value="save3" />
	</div>
	</div>
	</div>
	</form>



	<form method="post">
	<div id="resetsection">
	<div class="submit">
		<h2><?php _e("Clicking this will reset all theme options - use with caution", 'studio') ?></h2>
	<input name="reset" type="submit" class="sbutton" value="<?php echo esc_attr(__('Reset All Options','studio')); ?>" />
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

	array(  "name" => __("Choose Your BP studio Preset Style:", 'studio'),
		  	"id" => $shortname. $shortprefix . "custom_style",
			"std" => "default.css",
			"type" => "radio",
			"options" => $alt_stylesheets)
	);

function studio_ready_style_admin_panel() {
	echo "<div id=\"admin-options\">";
	
	global $themename, $shortname, $options2;
	
	if ( isset($_REQUEST['saved2'] )) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings saved.</strong></p></div>';
	if ( isset($_REQUEST['reset2'] )) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings reset.</strong></p></div>';
?>

<h4><?php echo "$themename"; ?> <?php _e('Choose your BP studio Preset Style', 'studio'); ?></h4>
<h2><?php _e('If you want to customise the theme options you MUST have default.css selected'); ?></h2>
<form action="" method="post">
<div class="get-listings">
<h2><?php _e("Style Select:", 'studio') ?></h2>
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
		<input name="save2" type="submit" class="sbutton" value="<?php echo esc_attr(__('Save Options', 'studio')); ?>" />
		<input type="hidden" name="theme_action2" value="save2" />
	</p>
</form>

<form method="post">
	<p class="save-p">
		<input name="reset2" type="submit" class="sbutton" value="<?php echo esc_attr(__('Reset Options', 'studio')); ?>" />
		<input type="hidden" name="theme_action2" value="reset2" />
	</p>
</form>
</div>

<?php }

function studio_admin_register() {
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
		add_theme_page(_g ($themename . __(' Theme Options', 'studio')),  _g (__('Theme Options', 'studio')),  'edit_theme_options', 'functions.php', 'studio_admin_panel');
}


function studio_ready_style_admin_register() {
	global $themename, $shortname, $options, $options2, $options3, $bp_existed, $multi_site_on;
		$action2 = isset($_REQUEST['theme_action2']);
	if ( isset($_GET['page']) == 'studio-themes.php' ) {
		if ( 'save2' == $action2) {
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
			header("Location: themes.php?page=studio-themes.php&saved2=true");
			die;
		} 
		else if( 'reset2' == $action2 ) {
			foreach ($options2 as $value) {
				delete_option( $value['id'] ); 
			}
			header("Location: themes.php?page=studio-themes.php&reset2=true");
			die;
		}
	}
	add_theme_page(_g (__('BP studio Preset Style', 'studio')),  _g (__('Preset Style', 'studio')),  'edit_theme_options', 'studio-themes.php', 'studio_ready_style_admin_panel');
}


function studio_custom_style_admin_register() {
	global $themename, $shortname, $options, $options2, $options3, $bp_existed, $multi_site_on;
	$action3 = isset($_REQUEST['theme_action3']);
	
		if ( isset($_GET['page']) == 'styling-functions.php' ) {
			if ( 'save3' == $action3) {
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
			add_theme_page(_g ($themename . __('Custom styling', 'studio')),  _g (__('Custom Styling', 'studio')),  'edit_theme_options', 'styling-functions.php', 'studio_custom_style_admin_panel');
	}

function studio_admin_head() { ?>
	<link href="<?php bloginfo('template_directory'); ?>/library/options/options-css.css" rel="stylesheet" type="text/css" />

	<?php if ( (isset($_GET['page']) && $_GET['page'] == 'styling-functions.php' ) || ( isset($_GET['page']) && $_GET['page'] == 'functions.php' )) {?>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/library/scripts/jscolor.js"></script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/library/scripts/jquery-ui-personalized-1.6rc2.min.js"></script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/library/scripts/jquery.cookie.min.js"></script>
	<?php wp_enqueue_script("jquery"); ?>
		<script type="text/javascript">
			   jQuery.noConflict();
		
		jQuery(document).ready(function(){
		jQuery('ul#tabm').tabs({event: "click"});
		});
		</script>

	<?php } ?>
	
	<?php if (isset($_GET['page']) == 'studio-themes.php'){?>
			<link href="<?php bloginfo('template_directory'); ?>/library/options/custom-admin.css" rel="stylesheet" type="text/css" />
	<?php } ?>
	
<?php }

add_action('admin_head', 'studio_admin_head');
add_action('admin_menu', 'studio_admin_register');
add_action('admin_menu', 'studio_ready_style_admin_register');
add_action('admin_menu', 'studio_custom_style_admin_register');

?>