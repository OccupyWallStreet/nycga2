<?php
/**
 * A unique identifier is defined to store the options in the database and reference them from the theme.
 * By default it uses the theme name, in lowercase and without spaces, but this can be changed if needed.
 * If the identifier changes, it'll appear as if the options have been reset.
 * 
 */

function optionsframework_option_name() {

	// This gets the theme name from the stylesheet (lowercase and without spaces)
	$the_theme = wp_get_theme();
	$themename = $the_theme->Name;
	$themename = preg_replace("/\W/", "", strtolower($themename) );
	
	$optionsframework_settings = get_option('optionsframework');
	$optionsframework_settings['id'] = $themename;
	update_option('optionsframework', $optionsframework_settings);
	
}

/**
 * Defines an array of options that will be used to generate the settings page and be saved in the database.
 * When creating the "id" fields, make sure to use all lowercase and no spaces.
 *  
 */

function optionsframework_options() {
	
	// fixed or scroll position
	$fixed_scroll = array("fixed" => "Fixed","scroll" => "Scroll");
	
	// Multicheck Array
	$multicheck_array = array("one" => "French Toast", "two" => "Pancake", "three" => "Omelette", "four" => "Crepe", "five" => "Waffle");
	
	// Multicheck Defaults
	$multicheck_defaults = array("one" => "1","five" => "1");
	
	// Background Defaults
	
	$background_defaults = array('color' => '', 'image' => '', 'repeat' => 'repeat','position' => 'top center','attachment'=>'scroll');
	
	
	// Pull all the categories into an array
	$options_categories = array();  
	$options_categories_obj = get_categories();
	foreach ($options_categories_obj as $category) {
    	$options_categories[$category->cat_ID] = $category->cat_name;
	}
	
	// Pull all the pages into an array
	$options_pages = array();  
	$options_pages_obj = get_pages('sort_column=post_parent,menu_order');
	$options_pages[''] = 'Select a page:';
	foreach ($options_pages_obj as $page) {
    	$options_pages[$page->ID] = $page->post_title;
	}
		
	// If using image radio buttons, define a directory path
	$imagepath =  get_bloginfo('stylesheet_directory') . '/images/';
		
	$options = array();
		
	$options[] = array( "name" => "Typography",
						"type" => "heading");
						
	$options[] = array( "name" => "Headings",
						"desc" => "Used in H1, H2, H3, H4, H5 & H6 tags.",
						"id" => "heading_typography",
						"std" => array('face' => '"Open Sans",sans-serif','style' => 'normal','color' => '#222222'),
						"type" => "wpbs_typography");
						
	$options[] = array( "name" => "Main Body Text",
						"desc" => "Used in P tags.",
						"id" => "main_body_typography",
						"std" => array('face' => 'Helvetica','style' => 'normal','color' => '#222222'),
						"type" => "wpbs_typography");
						
	$options[] = array( "name" => "Link Color",
						"desc" => "Default used if no color is selected.",
						"id" => "link_color",
						"std" => "#2BA6CB",
						"type" => "color");
					
	$options[] = array( "name" => "Link:hover Color",
						"desc" => "Default used if no color is selected.",
						"id" => "link_hover_color",
						"std" => "#2795B6",
						"type" => "color");
						
	$options[] = array( "name" => "Link:active Color",
						"desc" => "Default used if no color is selected.",
						"id" => "link_active_color",
						"std" => "#2BA6CB",
						"type" => "color");
						
	$options[] = array( "name" => "Top Nav",
						"type" => "heading");
						
	$options[] = array( "name" => "Top nav background color",
						"desc" => "Default used if no color is selected.",
						"id" => "top_nav_bg_color",
						"std" => "#4D4D4D",
						"type" => "color");
						
	$options[] = array( "name" => "Top nav item color",
						"desc" => "Link color.",
						"id" => "top_nav_link_color",
						"std" => "#E6E6E6",
						"type" => "color");
						
	$options[] = array( "name" => "Top nav item hover color",
						"desc" => "Link hover color.",
						"id" => "top_nav_link_hover_color",
						"std" => "#E6E6E6",
						"type" => "color");
						
	$options[] = array( "name" => "Other Settings",
						"type" => "heading");
						
	$options[] = array( "name" => "'Comments are closed' message on pages",
						"desc" => "Suppress 'Comments are closed' message",
						"id" => "suppress_comments_message",
						"std" => "1",
						"type" => "checkbox");

	$options[] = array( "name" => "Recent posts homepage slider",
						"desc" => "Show Orbit slider of recent posts on homepage template?",
						"id" => "orbit_slider",
						"std" => "1",
						"type" => "checkbox");
									
	return $options;
}