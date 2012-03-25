<?php 

$themename = "VideoPlus";
$shortname = "videoplus";

$admincpMainTabs = array('general','navigation','layout','ad','seo','integration','doc');

$cats_array = get_categories('hide_empty=0');
$pages_array = get_pages('hide_empty=0');
$pages_number = count($pages_array);

$site_pages = array();
$site_cats = array();

foreach ($pages_array as $pagg) {
	$site_pages[$pagg->ID] = htmlspecialchars($pagg->post_title);
	$pages_ids[] = $pagg->ID;
}

foreach ($cats_array as $categs) {
	$site_cats[$categs->cat_ID] = $categs->cat_name;
	$cats_ids[] = $categs->cat_ID;
}


$options = array (

	array( "name" => "nav-general",
		   "type" => "contenttab-wrapstart",),

		array( "type" => "subnavtab-start",),

			array( "name" => "general-1",
				   "type" => "subnav-tab",
				   "desc" => "General Settings"),
				   
			array( "name" => "general-2",
				   "type" => "subnav-tab",
				   "desc" => "Social Profiles"),					   			   

		array( "type" => "subnavtab-end",),

		array( "name" => "general-1",
			   "type" => "subcontent-start",),

			array( "name" => "Theme Stylesheet",
				   "id" => $shortname."_theme_stylesheet",
				   "type" => "select",
				   "std" => "default.css",
				   "options" => array("default.css"),
				   "desc" => "Select your themes alternative color scheme.",),	 			   
			   
			array( "name" => "Logo",
				   "id" => $shortname."_logo",
				   "type" => "upload",
				   "std" => get_bloginfo('template_directory') . '/images/logo.png',
				   "desc" => "Upload a logo for your theme, or specify the image address of your online logo. (http://yoursite.com/logo.png)."
			),

			array( "name" => "Enable Text Logo",
                   "id" => $shortname."_text_logo_enable",
                   "type" => "checkbox",
                   "std" => "false",
                   "desc" => "Enable if you want Blog Title and Tagline to be text-based. Setup title/tagline in WP -> Settings -> General."),			

			array( "name" => "Favicon",
				   "id" => $shortname."_favicon",
				   "type" => "upload",
				   "std" => get_bloginfo('template_directory') . '/images/favicon.png',
				   "desc" => "Upload a 16px x 16px PNG/Gif image that will represent your website's favicon."
			),

			array( "name" => "Setup e-mail for contact form",
				   "id" => $shortname."_email",
				   "type" => "text",
				   "std" => "example@mail.com",
				   "desc" => "Contact Form",),				

			array( "type" => "clearfix",),
			
			array( "name" => "Custom CSS",
				   "id" => $shortname."_custom_css",
				   "type" => "textarea",
				   "std" => "body { }",
				   "desc" => "Quickly add some CSS to your theme by adding it to this block.",),
				   	   
			array( "name" => "Footer Credit",
				   "id" => $shortname."_footer_credit",
				   "type" => "textarea",
				   "std" => "<a href=\"http://www.theme-junkie.com\">WordPress Video Theme</a> designed by <a href=\"http://www.theme-junkie.com\">Theme Junkie</a>",
				   "desc" => "desc",),
				   
			array( "type" => "clearfix",),

		array( "name" => "general-1",
			   "type" => "subcontent-end",),
			   
		array( "name" => "general-2",
			   "type" => "subcontent-start",),

			array( "name" => "Enter Your Twitter Profile Link",
				   "id" => $shortname."_twitter_url",
				   "type" => "text",
				   "std" => "http://twitter.com/theme_junkie",
				   "desc" => "desc.",),
				   	 			   
			array( "name" => "Enter Your Custom RSS Link",
				   "id" => $shortname."_rss_url",
				   "type" => "text",
				   "std" => "http://feeds.feedburner.com/ThemeJunkie",
				   "desc" => "desc.",),	
	   
			array( "type" => "clearfix",),

		array( "name" => "general-2",
			   "type" => "subcontent-end",),

		array( "type" => "subnavtab-end",),			   

	array(  "name" => "nav-general",
			"type" => "contenttab-wrapend",),

//-------------------------------------------------------------------------------------//

	array( "name" => "nav-navigation",
		   "type" => "contenttab-wrapstart",),

		array( "type" => "subnavtab-start",),

			array( "name" => "navigation-1",
				   "type" => "subnav-tab",
				   "desc" => "Pages"),

			array( "name" => "navigation-2",
				   "type" => "subnav-tab",
				   "desc" => "Categories"),

		array( "type" => "subnavtab-end",),

		array( "name" => "navigation-1",
			   "type" => "subcontent-start",),

			array( "name" => "Exclude pages from the navigation bar",
				   "id" => $shortname."_menupages",
				   "type" => "checkboxes",
				   "std" => "",
				   "desc" => "Here you can choose to remove certain pages from the navigation menu. All pages marked with an X will not appear in your navigation bar. ",
				   "usefor" => "pages",
				   "options" => $pages_ids),

			array( "name" => "Show dropdown menus",
            "id" => $shortname."_enable_dropdowns",
            "type" => "checkbox",
            "std" => "on",
			"desc" => "If you would like to remove the dropdown menus from the pages navigation bar disable this feature."),

			array( "name" => "Display Home link",
            "id" => $shortname."_home_link",
            "type" => "checkbox2",
            "std" => "on",
			"desc" => "By default the theme creates a Home link that, when clicked, leads back to your blog's homepage. If, however, you are using a static homepage and have already created a page called Home to use, this will result in a duplicate link. In this case you should disable this feature to remove the link."),

			array( "type" => "clearfix",),
			
			array( "name" => "Number of dropdown tiers shown",
            "id" => $shortname."_tiers_shown_pages",
            "type" => "text",
            "std" => "4",
			"desc" => "This options allows you to control how many teirs your pages dropdown menu has. Increasing the number allows for additional menu items to be shown."),

			array( "name" => "Sort Pages Links",
                   "id" => $shortname."_sort_pages",
                   "type" => "select",
                   "std" => "post_title",
				   "desc" => "Here you can choose to sort your pages links.",
                   "options" => array("post_title", "menu_order","post_date","post_modified","ID","post_author","post_name")),

			array( "name" => "Order Pages Links by Ascending/Descending",
                   "id" => $shortname."_order_page",
                   "type" => "select",
                   "std" => "asc",
				   "desc" => "Here you can choose to reverse the order that your pages links are displayed. You can choose between ascending and descending.",
                   "options" => array("asc", "desc")),

			array( "type" => "clearfix",),


		array( "name" => "navigation-1",
			   "type" => "subcontent-end",),

		array( "name" => "navigation-2",
			   "type" => "subcontent-start",),

			array( "name" => "Exclude categories from the navigation bar",
				   "id" => $shortname."_menucats",
				   "type" => "checkboxes",
				   "std" => "",
				   "desc" => "Here you can choose to remove certain categories from the navigation menu. All categories marked with an X will not appear in your navigation bar. ",
				   "usefor" => "categories",
				   "options" => $cats_ids),

			array( "name" => "Show dropdown menus",
            "id" => $shortname."_enable_dropdowns_categories",
            "type" => "checkbox",
            "std" => "on",
			"desc" => "If you would like to remove the dropdown menus from the categories navigation bar disable this feature."),

			array( "name" => "Hide empty categories",
            "id" => $shortname."_categories_empty",
            "type" => "checkbox",
            "std" => "on",
			"desc" => "If you would like categories to be displayed in your navigationbar that don't have any posts in them then disable this option. By default empty categories are hidden"),

			array( "type" => "clearfix",),

			array( "name" => "Number of dropdown tiers shown",
            "id" => $shortname."_tiers_shown_categories",
            "type" => "text",
            "std" => "4",
			"desc" => "This options allows you to control how many teirs your pages dropdown menu has. Increasing the number allows for additional menu items to be shown."),

			array( "type" => "clearfix",),

		    array( "name" => "Sort Categories Links by Name/ID/Slug/Count/Term Group",
                   "id" => $shortname."_sort_cat",
                   "type" => "select",
                   "std" => "name",
				   "desc" => "By default pages are sorted by name. However if you would rather have them sorted by ID you can adjust this setting.",
                   "options" => array("name", "ID", "slug", "count", "term_group")),

			array( "name" => "Order Category Links by Ascending/Descending",
                   "id" => $shortname."_order_cat",
                   "type" => "select",
                   "std" => "asc",
				   "desc" => "Here you can choose to reverse the order that your categories links are displayed. You can choose between ascending and descending.",
                   "options" => array("asc", "desc")),

		array( "name" => "navigation-2",
			   "type" => "subcontent-end",),

	array( "name" => "nav-navigation",
		   "type" => "contenttab-wrapend",),

//-------------------------------------------------------------------------------------//

	array( "name" => "nav-layout",
		   "type" => "contenttab-wrapstart",),

		array( "type" => "subnavtab-start",),

			array( "name" => "layout-1",
				   "type" => "subnav-tab",
				   "desc" => "Homepage"),

			array( "name" => "layout-2",
				   "type" => "subnav-tab",
				   "desc" => "Single Post / Page"),

		array( "type" => "subnavtab-end",),

		array( "name" => "layout-1",
			   "type" => "subcontent-start",),

			array( "name" => "Style of secondary navigation bar",
				   "id" => $shortname."_secondary_nav_style",
				   "type" => "select",
				   "std" => "Fixed Width (960px)",
				   "options" => array("Fixed Width (960px)", "Full Width"),
				   "desc" => "desc.",),
				   		   
			array( "name" => "Enable featured slider",
                   "id" => $shortname."_featured_slider_enable",
                   "type" => "checkbox",
                   "std" => "on",
				   "desc" => "desc."),

			array( "name" => "Input comma-separated tags for featured slider",
                   "id" => $shortname."_featured_slider_tags",
                   "type" => "text",
                   "std" => "featured, headline",
				   "desc" => "desc."),
				   
			array( "name" => "Enable style-switch button for posts (box/list)",
                   "id" => $shortname."_entry_switch_enable",
                   "type" => "checkbox",
                   "std" => "on",
				   "desc" => "desc."),

			array( "name" => "Enable fancybox effect for video thumbnails",
                   "id" => $shortname."_fancybox_enable",
                   "type" => "checkbox",
                   "std" => "on",
				   "desc" => "desc."),				   
	
			array( "type" => "clearfix",),

		array( "name" => "layout-1",
			   "type" => "subcontent-end",),			   

		array( "name" => "layout-2",
			   "type" => "subcontent-start",),

			array( "name" => "Show author box on posts",
                   "id" => $shortname."_show_author_box",
                   "type" => "checkbox",
                   "std" => "on",
                   "desc" => "desc. "),

			array( "name" => "Show related posts & bottom ad on posts",
                   "id" => $shortname."_show_entry_bottom",
                   "type" => "checkbox",
                   "std" => "on",
                   "desc" => "desc. "),                                     
				   
			array( "name" => "Show comments on posts",
            "id" => $shortname."_show_post_comments",
            "type" => "checkbox2",
            "std" => "on",
			"desc" => "You can disable this option if you want to remove the comments and comment form from single post pages. "),

			array( "name" => "Show comments on pages",
            "id" => $shortname."_show_page_comments",
            "type" => "checkbox",
            "std" => "false",
			"desc" => "By default comments are not placed on pages, however, if you would like to allow people to comment on your pages simply enable this option. "),

			array( "type" => "clearfix",),

		array( "name" => "layout-2",
			   "type" => "subcontent-end",),

	array( "name" => "nav-layout",
		   "type" => "contenttab-wrapend",),

//-------------------------------------------------------------------------------------//


//-------------------------------------------------------------------------------------//
	array( "name" => "nav-seo",
		   "type" => "contenttab-wrapstart",),

		array( "type" => "subnavtab-start",),

			array( "name" => "seo-1",
				   "type" => "subnav-tab",
				   "desc" => "Homepage SEO",),

			array( "name" => "seo-2",
				   "type" => "subnav-tab",
				   "desc" => "Single Post Page SEO",),

			array( "name" => "seo-3",
				   "type" => "subnav-tab",
				   "desc" => "Index Page SEO",),

		array( "type" => "subnavtab-end",),

		array( "name" => "seo-1",
			   "type" => "subcontent-start",),

			array( "name" => " Enable custom title ",
				   "id" => $shortname."_seo_home_title",
				   "type" => "checkbox",
				   "std" => "false",
				   "desc" => "By default the theme uses a combination of your blog name and your blog description, as defined when you created your blog, to create your homepage titles. However if you want to create a custom title then simply enable this option and fill in the custom title field below. ",),

			array( "name" => " Enable meta description",
				   "id" => $shortname."_seo_home_description",
				   "type" => "checkbox",
				   "std" => "false",
				   "desc" => "By default the theme uses your blog description, as defined when you created your blog, to fill in the meta description field. If you would like to use a different description then enable this option and fill in the custom description field below. ",),

			array( "name" => " Enable meta keywords",
				   "id" => $shortname."_seo_home_keywords",
				   "type" => "checkbox",
				   "std" => "false",
				   "desc" => "By default the theme does not add keywords to your header. Most search engines don't use keywords to rank your site anymore, but some people define them anyway just in case. If you want to add meta keywords to your header then enable this option and fill in the custom keywords field below. ",),

			array( "name" => " Enable canonical URL's",
				   "id" => $shortname."_seo_home_canonical",
				   "type" => "checkbox",
				   "std" => "false",
				   "desc" => "Canonicalization helps to prevent the indexing of duplicate content by search engines, and as a result, may help avoid duplicate content penalties and pagerank degradation. Some pages may have different URLs all leading to the same place. For example domain.com, domain.com/index.html, and www.domain.com are all different URLs leading to your homepage. From a search engine's perspective these duplicate URLs, which also occur often due to custom permalinks, may be treaded individually instead of as a single destination. Defining a canonical URL tells the search engine which URL you would like to use officially. The theme bases its canonical URLs off your permalinks and the domain name defined in the settings tab of wp-admin.",),

			array( "type" => "clearfix",),

			array( "name" => "Homepage custom title (if enabled)",
				   "id" => $shortname."_seo_home_titletext",
				   "type" => "text",
				   "std" => "",
				   "desc" => "If you have enabled custom titles you can add your custom title here. Whatever you type here will be placed between the < title >< /title > tags in header.php",),

			array( "name" => "Homepage meta description (if enabled)",
				   "id" => $shortname."_seo_home_descriptiontext",
				   "type" => "textarea",
				   "std" => "",
				   "desc" => "If you have enabled meta descriptions you can add your custom description here.",),

			array( "name" => "Homepage meta keywords (if enabled)",
				   "id" => $shortname."_seo_home_keywordstext",
				   "type" => "text",
				   "std" => "",
				   "desc" => "If you have enabled meta keywords you can add your custom keywords here. Keywords should be separated by comas. For example: wordpress,themes,templates,elegant",),

			array( "name" => "If custom titles are disabled, choose autogeneration method",
				   "id" => $shortname."_seo_home_type",
				   "type" => "select",
				   "std" => "BlogName | Blog description",
				   "options" => array("BlogName | Blog description", "Blog description | BlogName", "BlogName only"),
				   "desc" => "If you are not using cutsom post titles you can still have control over how your titles are generated. Here you can choose which order you would like your post title and blog name to be displayed, or you can remove the blog name from the title completely.",),

			array( "name" => "Define a character to separate BlogName and Post title",
				   "id" => $shortname."_seo_home_separate",
				   "type" => "text",
				   "std" => " | ",
				   "desc" => "Here you can change which character separates your blog title and post name when using autogenerated post titles. Common values are | or -",),

		array( "name" => "seo-1",
			   "type" => "subcontent-end",),

		array( "name" => "seo-2",
			   "type" => "subcontent-start",),

			array( "name" => "Enable custom titles",
				   "id" => $shortname."_seo_single_title",
				   "type" => "checkbox",
				   "std" => "false",
				   "desc" => "By default the theme creates post titles based on the title of your post and your blog name. If you would like to make your meta title different than your actual post title you can define a custom title for each post using custom fields. This option must be enabled for custom titles to work, and you must choose a custom field name for your title below.",),

			array( "name" => "Enable custom description",
				   "id" => $shortname."_seo_single_description",
				   "type" => "checkbox2",
				   "std" => "false",
				   "desc" => "If you would like to add a meta description to your post you can do so using custom fields. This option must be enabled for descriptions to be displayed on post pages. You can add your meta description using custom fields based off the custom field name you define below.",),

			array( "type" => "clearfix",),

			array( "name" => "Enable custom keywords",
				   "id" => $shortname."_seo_single_keywords",
				   	"type" => "checkbox",
				   "std" => "false",
				   "desc" => "If you would like to add meta keywords to your post you can do so using custom fields. This option must be enabled for keywords to be displayed on post pages. You can add your meta keywords using custom fields based off the custom field name you define below.",),

			array( "name" => "Enable canonical URL's",
				   "id" => $shortname."_seo_single_canonical",
				   "type" => "checkbox2",
				   "std" => "false",
				   "desc" => "Canonicalization helps to prevent the indexing of duplicate content by search engines, and as a result, may help avoid duplicate content penalties and pagerank degradation. Some pages may have different URL's all leading to the same place. For example domain.com, domain.com/index.html, and www.domain.com are all different URLs leading to your homepage. From a search engine's perspective these duplicate URLs, which also occur often due to custom permalinks, may be treaded individually instead of as a single destination. Defining a canonical URL tells the search engine which URL you would like to use officially. The theme bases its canonical URLs off your permalinks and the domain name defined in the settings tab of wp-admin.",),

			array( "type" => "clearfix",),

			array( "name" => "Custom field Name to be used for title",
				   "id" => $shortname."_seo_single_field_title",
				   "type" => "text",
				   "std" => "seo_title",
				   "desc" => "When you define your title using custom fields you should use this value for the custom field Name. The Value of your custom field should be the custom title you would like to use.",),

			array( "name" => "Custom field Name to be used for description",
				   "id" => $shortname."_seo_single_field_description",
				   "type" => "text",
				   "std" => "seo_description",
				   "desc" => "When you define your meta description using custom fields you should use this value for the custom field Name. The Value of your custom field should be the custom description you would like to use.",),

			array( "name" => "Custom field Name to be used for keywords",
				   "id" => $shortname."_seo_single_field_keywords",
				   "type" => "text",
				   "std" => "seo_keywords",
				   "desc" => "When you define your keywords using custom fields you should use this value for the custom field Name. The Value of your custom field should be the meta keywords you would like to use, separated by comas.",),

			array( "name" => "If custom titles are disabled, choose autogeneration method",
				   "id" => $shortname."_seo_single_type",
				   "type" => "select",
				   "std" => "Post title | BlogName",
				   "options" => array("Post title | BlogName", "BlogName | Post title", "Post title only"),
				   "desc" => "If you are not using cutsom post titles you can still have control over hw your titles are generated. Here you can choose which order you would like your post title and blog name to be displayed, or you can remove the blog name from the title completely.",),

			array( "name" => "Define a character to separate BlogName and Post title",
				   "id" => $shortname."_seo_single_separate",
				   "type" => "text",
				   "std" => " | ",
				   "desc" => "Here you can change which character separates your blog title and post name when using autogenerated post titles. Common values are | or -",),

		array( "name" => "seo-2",
			   "type" => "subcontent-end",),

		array( "name" => "seo-3",
				   "type" => "subcontent-start",),

			array( "name" => " Enable canonical URL's",
				   "id" => $shortname."_seo_index_canonical",
				   "type" => "checkbox",
				   "std" => "false",
				   "desc" => "Canonicalization helps to prevent the indexing of duplicate content by search engines, and as a result, may help avoid duplicate content penalties and pagerank degradation. Some pages may have different URL's all leading to the same place. For example domain.com, domain.com/index.html, and www.domain.com are all different URLs leading to your homepage. From a search engine's perspective these duplicate URLs, which also occur often due to custom permalinks, may be treaded individually instead of as a single destination. Defining a canonical URL tells the search engine which URL you would like to use officially. The theme bases its canonical URLs off your permalinks and the domain name defined in the settings tab of wp-admin.",),

			array( "name" => "Enable meta descriptions",
				   "id" => $shortname."_seo_index_description",
				   	"type" => "checkbox2",
				   "std" => "false",
				   "desc" => "Check this box if you want to display meta descriptions on category/archive pages. The description is based off the category description you choose when creating/edit your category in wp-admin.",),

			array( "type" => "clearfix",),

			array( "name" => "Choose title autogeneration method",
				   "id" => $shortname."_seo_index_type",
				   "type" => "select",
				   "std" => "Category name | BlogName",
				   "options" => array("Category name | BlogName", "BlogName | Category name", "Category name only"),
				   "desc" => "Here you can choose how your titles on index pages are generated. You can change which order your blog name and index title are displayed, or you can remove the blog name from the title completely.",),

			array( "name" => "Define a character to separate BlogName and Post title",
				   "id" => $shortname."_seo_index_separate",
				   "type" => "text",
				   "std" => " | ",
				   "desc" => "Here you can change which character separates your blog title and index page name when using autogenerated post titles. Common values are | or -",),

			array( "type" => "clearfix",),

		array( "name" => "seo-3",
				   "type" => "subcontent-end",),

	array(  "name" => "nav-seo",
			"type" => "contenttab-wrapend",),

//-------------------------------------------------------------------------------------//

	array( "name" => "nav-integration",
		   "type" => "contenttab-wrapstart",),

		array( "type" => "subnavtab-start",),

			array( "name" => "integration-1",
				   "type" => "subnav-tab",
				   "desc" => "Code Integration"),

		array( "type" => "subnavtab-end",),

		array( "name" => "integration-1",
			   "type" => "subcontent-start",),

			array( "name" => "Enable header code",
                   "id" => $shortname."_integrate_header_enable",
                   "type" => "checkbox",
                   "std" => "false",
                   "desc" => "Disabling this option will remove the header code below from your blog. This allows you to remove the code while saving it for later use."),

			array( "name" => "Enable body code",
                   "id" => $shortname."_integrate_body_enable",
                   "type" => "checkbox2",
                   "std" => "false",
                   "desc" => "Disabling this option will remove the body code below from your blog. This allows you to remove the code while saving it for later use."),

			array( "type" => "clearfix",),

			array( "name" => "Enable single top code",
                   "id" => $shortname."_integrate_singletop_enable",
                   "type" => "checkbox",
                   "std" => "false",
                   "desc" => "Disabling this option will remove the single top code below from your blog. This allows you to remove the code while saving it for later use."),

			array( "name" => "Enable single bottom code",
                   "id" => $shortname."_integrate_singlebottom_enable",
                   "type" => "checkbox2",
                   "std" => "false",
                   "desc" => "Disabling this option will remove the single bottom code below from your blog. This allows you to remove the code while saving it for later use."),

			array( "type" => "clearfix",),

			array( "name" => "Add code to the < head > of your blog",
				   "id" => $shortname."_integration_head",
				   "type" => "textarea",
				   "std" => "",
				   "desc" => "Any code you place here will appear in the head section of every page of your blog. This is useful when you need to add javascript or css to all pages.",),

			array( "name" => "Add code to the < body > of your blog (good for google analytics)",
				   "id" => $shortname."_integration_body",
				   "type" => "textarea",
				   "std" => "",
				   "desc" => "Any code you place here will appear in body section of all pages of your blog. This is usefull if you need to input a tracking pixel for a state counter such as Google Analytics.",),

			array( "name" => "Add code to the top of your posts",
				   "id" => $shortname."_integration_single_top",
				   "type" => "textarea",
				   "std" => "",
				   "desc" => "Any code you place here will be placed at the top of all single posts. This is useful if you are looking to integrating things such as social bookmarking links.",),

			array( "name" => "Add code to the bottom of your posts, before the comments",
				   "id" => $shortname."_integration_single_bottom",
				   "type" => "textarea",
				   "std" => "",
				   "desc" => "Any code you place here will be placed at the top of all single posts. This is useful if you are looking to integrating things such as social bookmarking links.",),

		array( "name" => "integration-1",
			   "type" => "subcontent-end",),

	array( "name" => "nav-integration",
		   "type" => "contenttab-wrapend",),

//-------------------------------------------------------------------------------------//

	array( "name" => "nav-doc",
		   "type" => "contenttab-wrapstart",),

		array( "type" => "subnavtab-start",),

			array( "name" => "doc-1",
				   "type" => "subnav-tab",
				   "desc" => "Installation"),

			array( "name" => "doc-2",
				   "type" => "subnav-tab",
				   "desc" => "Troubleshooting"),

		array( "type" => "subnavtab-end",),

		array( "name" => "doc-1",
			   "type" => "subcontent-start",),

			array( "name" => "installation",
				   "type" => "doc",),

		array( "name" => "doc-1",
			   "type" => "subcontent-end",),

		array( "name" => "doc-2",
			   "type" => "subcontent-start",),

			array( "name" => "troubleshooting",
				   "type" => "doc",),

		array( "name" => "doc-2",
			   "type" => "subcontent-end",),

	array( "name" => "nav-doc",
		   "type" => "contenttab-wrapend",),

//-------------------------------------------------------------------------------------//

	array( "name" => "nav-advertisements",
		   "type" => "contenttab-wrapstart",),

		array( "type" => "subnavtab-start",),

			array( "name" => "advertisements-1",
				   "type" => "subnav-tab",
				   "desc" => "Manage Un-widgetized Advertisements"),

		array( "type" => "subnavtab-end",),

		array( "name" => "advertisements-1",
			   "type" => "subcontent-start",),

			array( "name" => "Enable entry bottom ad (300x250)",
				   "id" => $shortname."_entry_bottom_ad_enable",
				   	"type" => "checkbox",
				   "std" => "false",
				   "desc" => "desc.",),			   

			array( "type" => "clearfix",),
				   
			array( "name" => "Input entry bottom ad code",
				   "id" => $shortname."_entry_bottom_ad_code",
				   "type" => "textarea",
				   "std" => "",
				   "desc" => "desc",),	   				   

		array( "name" => "advertisements-1",
			   "type" => "subcontent-end",),

	array( "name" => "nav-support",
		   "type" => "contenttab-wrapend",),

//-------------------------------------------------------------------------------------//

); 


function custom_colors_css(){
	global $shortname; ?>
	
	<style type="text/css">
		body { color: #<?php echo(get_option($shortname.'_color_mainfont')); ?>; }
		#content-area a { color: #<?php echo(get_option($shortname.'_color_mainlink')); ?>; }
		ul.nav li a { color: #<?php echo(get_option($shortname.'_color_pagelink')); ?>; }
		ul.nav > li.current_page_item > a, ul#top-menu > li:hover > a, ul.nav > li.current-cat > a { color: #<?php echo(get_option($shortname.'_color_pagelink_active')); ?>; }
		h1, h2, h3, h4, h5, h6, h1 a, h2 a, h3 a, h4 a, h5 a, h6 a { color: #<?php echo(get_option($shortname.'_color_headings')); ?>; }
		
		#sidebar a { color:#<?php echo(get_option($shortname.'_color_sidebar_links')); ?>; }		
		div#footer { color:#<?php echo(get_option($shortname.'_footer_text')); ?> }
		#footer a, ul#bottom-menu li a { color:#<?php echo(get_option($shortname.'_color_footerlinks')); ?> }
	</style>

<?php };

?>