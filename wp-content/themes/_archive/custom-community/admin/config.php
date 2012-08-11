<?php
//
// CheezCap - Cheezburger Custom Administration Panel
// (c) 2008 - 2010 Cheezburger Network (Pet Holdings, Inc.)
// LOL: http://cheezburger.com
// Source: http://code.google.com/p/cheezcap/
// Authors: Kyall Barrows, Toby McKes, Stefan Rusek, Scott Porad
// License: GNU General Public License, version 2 (GPL), http://www.gnu.org/licenses/gpl-2.0.html
//

$themename = 'Theme'; // used on the title of the custom admin page
$req_cap_to_edit = 'edit_theme_options'; // the user capability that is required to access the CheezCap settings page

function cap_get_options() {
	$pages = get_pages();
	$option = Array();
	$option[0] = "All pages";
	$i = 1;
	foreach ($pages as $pagg) {
		$option[$i] = $pagg->post_title;
		$i++;
	}
	$option_pages = $option;
	
	$args = array('echo' => '0','hide_empty' => '0');
	$categories = get_categories($args);
	$option = Array();
	$option[0] = Array (
            'name' => 'All categories',
            'slug' => 'all-categories'
        );
	$i = 1;
	foreach($categories as $category) {
		$option[$i]['name'] = $category->name;
		$option[$i]['slug'] = $category->slug;
		$i++;
	}

	$option_categories = $option;
    
	return array(
	new Group ("General", "general",
		array(
		new DropdownOption(
			"Colour scheme", 
			"Select the colour scheme of your website.", 
			"style_css", 
			array( 'white', 'grey', 'dark', 'more colour schemes in the pro version' )),
		new TextOption(
			"Website width", 
			"Just type in the number, either in px or %. Default is 1000. <br>
			Tip: If you use the full-width slider, don't make your site bigger than 1006px, or use the normal slider with preview.", 
			"website_width", 
			"",
			"",
			"start",
			"Website width"), 
		new DropdownOption(
			"Fluid or static width?", 
			"Do you want your layout fluid(%) or static(px)? <br>
			Notes: when you use the slideshow, better don't use fluid width. <br>
			And if you use the slideshow shadow, it only looks nice with a static width between 990 and about 1100px.", 
			"website_width_unit", 
			array('px', '%'),
			"",
			'end'),		
		new BooleanOption(
			"Use standard Wordpress background settings", 
			"Enable this option, if you like to use the standard wordpress settings page.", 
			"add_custom_background", 
			false,
			'start',
			'Background'),
			new ColorOption(
				"Background colour", 
				"Change your background colour", 
				"bg_body_color", 
				"",
				'',
				''),
			new FileOption(
				"Background image", 
				"Insert your own background image. Upload or insert url.", 
				"bg_body_img",
				'',
				false,
				''),
			new BooleanOption(
				"Fixed background image", 
				"Fix the position of your body background image", 
				"bg_body_img_fixed", 
				false,
				false,
				''),
			new DropdownOption(
				"Background position", 
				"Position of the background image: center, left, right", 
				"bg_body_img_pos", 
				array('center', 'left', 'right'),
				'',
				false,
				''),	
		new DropdownOption(
			"Background repeat", 
			"Repeat background image: x=horizontally, y=vertically", 
			"bg_body_img_repeat", 
			array('no repeat', 'x', 'y', 'x+y'),
			'',
			'end',
			''),
		new ColorOption(
			"Container colour", 
			"Change the background colour of the content part,<br> 
			write transparent for no color", 
			"bg_container_color", 
			"",
			"start",
			"Container"),
			new DropdownOption(
				"Show / hide the vertical lines", 
				"The vertical lines that divide your container are default, <br>
				you can disable them if you like.", 
				"bg_container_nolines", 
				array('show', 'hide'),
				"",
				false),
			new ColorOption(
				"Vertical lines colour", 
				"Change the colour of the vertical lines. <br>
				Tip: Best is to have them in the colour of the background and other lines. ", 
				"v_line_color", 
				"",
				'',
				''),
			new FileOption(
				"Container background image", 
				"Change background image for the container (currently the vertical lines <br>
				that separate the sidebars). Upload or insert url.", 
				"bg_container_img", 
				"",
				false),
			new DropdownOption(
				"Container background repeat", 
				"Repeat background image: x=horizontally, y=vertically", 
				"bg_container_img_repeat", 
				array('no repeat', 'x', 'y', 'x+y'),
				"",
				false),
		new DropdownOption(
			"Container corner radius", 
			"Do you want your container corners to be rounded?", 
			"container_corner_radius", 
			array('rounded', 'not rounded'),
			"",
			'end'),
		new DropdownOption(
			"Sidebar default position", 
			"Where do you like to have your sidebars? Define your default layout.", 
			"sidebar_position", 
			array('right', 'left and right', 'left', 'full-width')),
		new DropdownOption(
			"Title font style", 
			"Change the title font style (h1 and h2)", 
			"title_font_style", 
			array('Arial, sans-serif', 'Helvetica, Arial, sans-serif', 'Century Gothic, Avant Garde, Arial, sans-serif', 'Arial Black, Arial, sans-serif', 'Impact, Arial, sans-serif', 'Times New Roman, Times', 'Garamond, Times New Roman, Times'),
			"",
			"start",
			"Titles"),
			new TextOption(
				"Title size", 
				"Change the title font size (h1 and h2), default is 28px, just enter a number", 
				"title_size", 
				"",
				"",
				false),
			new DropdownOption(
				"Titles font weight", 
				"Do you want your titles bold or normal?", 
				"title_weight", 
				array('bold', 'normal'),
				"",
				false),
		new ColorOption(
			"Title colour", 
			"Change title colour", 
			"title_color", 
			"","end"),
		new DropdownOption(
			"Subtitle font style", 
			"Change the subtitle font style (h3-h6)", 
			"subtitle_font_style", 
			array('Arial, sans-serif', 'Helvetica, Arial, sans-serif', 'Century Gothic, Avant Garde, Arial, sans-serif', 'Arial Black, Arial, sans-serif', 'Impact, Arial, sans-serif', 'Times New Roman, Times', 'Garamond, Times New Roman, Times'),
			"",
			"start",
			"Subtitles"),
			new DropdownOption(
				"Subtitles font weight", 
				"Do you want your subtitles bold or normal?", 
				"subtitle_weight", 
				array('bold', 'normal'),
				"",
				false),
		new ColorOption(
			"Subtitle colour", 
			"Change subtitle colour", 
			"subtitle_color", 
			"","end"),
		new DropdownOption(
			"Show excerpts", 
			"Just for category and archive views: use excerpts or show full content of your posts", 
			"excerpt_on", 
			array('content', 'excerpt'),
			"",
			"start",
			"Excerpts"),
		new TextOption(
			"Excerpt length", 
			"Change the excerpt length, default is 30 words", 
			"excerpt_length", 
			"","","end"),
		new DropdownOption(
			"Font style", 
			"Change the font style", 
			"font_style", 
			array('Arial, sans-serif', 'Helvetica, Arial, sans-serif', 'Century Gothic, Avant Garde, Arial, sans-serif', 'Times New Roman, Times', 'Garamond, Times New Roman, Times'),
			"",
			"start",
			"Fonts"),
		new TextOption(
			"Font size", 
			"Change the standard font size, default is 13px, just enter a number", 
			"font_size", 
			"","",
			false),
		new ColorOption(
			"Font colour", 
			"Change font colour", 
			"font_color", 
			"",
			'end'),
		new ColorOption(
			"Link colour", 
			"Change link colour. <br>
			Notes: You just need to change the link colour to have a nice effect on all link and button colours. <br>
			The hover colour will automatically be your font colour or the default font colour. <br>
			Optional, you can also choose a hover colour, background colour, background hover colour or if and when to underline. ", 
			"link_color", 
			"",
			"start",
			"Links"),
		new ColorOption(
			"Link hover colour", 
			"Change link colour for mouse moves over.", 
			"link_color_hover", 
			"",
			false),
		new DropdownOption(
			"BuddyPress subnavigation adapting", 
			"Use link hover colour for the BuddyPress subnav also? <br> 
			By default the subnav links adapts the link colour and link hover colour. <br>
			Sometimes the link hover colour can look ugly here and you don't want the subnav to adapt. <br>
			Then you can change the colour adapting here easily. ", 
			"link_color_subnav_adapt", 
			array('just the link colour', 'link colour and hover colour'),
			"", 
			false),	
		new DropdownOption(
			"Link underline", 
			"Choose if (and when) to underline links.", 
			"link_underline", 
			array('never', 'always', 'just for mouse over', 'just when normal'),
			"", 
			false),
		new ColorOption(
			"Link background colour", 
			"Change link background colour.", 
			"link_bg_color", 
			"",
			false),
		new ColorOption(
			"Link background hover colour", 
			"Change link background colour for mouse moves over. <br>
			Watch out you have enough contrast to the (hover) link colour and also the font colour!", 
			"link_bg_color_hover", 
			"", 
			false),
		new DropdownOption(
			"Titles adapting", 
			"Do you like to use the link background colours or underline effetcs for the titles (h1-h6) also? <br> 
			By default they adapt just the link colour and link hover colour.", 
			"link_styling_title_adapt", 
			array('just the hover effect', 'link colour and hover colour', '...the underline effects too', 'the background colours too', 'adapt all link styles' ),
			"", 
			'end'),	
		new FileOption(
			"Favicon image", 
			"Insert your own favicon image. Upload or insert url.", 
			"favicon"),
		new DropdownOption(
			"Show / hide avatars", 
			"Show or hide the avatars in the post listing. <br> 
			This option is for the standard WordPress Homepage showing your latest articles. <br>
			To select a page as your homepage, go to Settings -> Reading.", 
			"default_homepage_hide_avatar", 
			array('show', 'hide'),
			"",
			"start",
			"Default homepage"),
		new DropdownOption(
			"Last 3 Posts on home", 
			"Display last 3 posts. <br> ",
			"default_homepage_last_posts", 
			array('show', 'hide'),
			"", 
			false),	
		new DropdownOption(
			"Post listing style", 
			"Select a style how to display the latest posts. <br> 
			You can also list your posts in magazine style, showing the featured images, check out our our <a href='http://themekraft.com/faq/list-your-latest-posts/'>FAQ here</a>.", 
			"default_homepage_style", 
			array('bubbles', 'default'),
			"", 
			false),	
		new DropdownOption(
			"Show / hide date, category and author", 
			"Show or hide the date, category and author in the post listing.", 
			"default_homepage_hide_date", 
			array('show', 'hide'),
			"",
			'end'),
		new FileOption(
			"Login page logo", 
			"Insert your own image for the login page. Upload or insert url.", 
			"bg_loginpage_img", 
			"",
			"start",
			"Login"),
		new TextOption(
			"Login logo height", 
			"Define the login logo height, the width should be 326px max", 
			"login_logo_height", 
			"",
			"",
			false),
		new FileOption(
			"Login page background image", 
			"Insert your own image for the login page background. Upload or insert url.", 
			"bg_loginpage_body_img", 
			"","",false),
		new ColorOption(
			"Login page background colour", 
			"Change login page background colour", 
			"bg_loginpage_body_color", 
			"",	
			false),
		new ColorOption(
			"Login page backtoblog fade colour 1", 
			"Change login page backtoblog colour fade 1", 
			"bg_loginpage_backtoblog_fade_1", 
			"",
			false),
		new ColorOption(
			"Login page backtoblog fade colour 2", 
			"Change login page backtoblog colour fade 2", 
			"bg_loginpage_backtoblog_fade_2", 
			"",
			"end"),
		)
		),
	new Group ("Header", "header",
		array(
		new BooleanOption(
			"Use standard Wordpress custom image header settings", 
			"Enable this option, if you like to use the standard wordpress settings Page.", 
			"add_custom_image_header", 
			false),		
		new DropdownOption(
			"Show header text", 
			"Show header text or not?", 
			"header_text", 
			array('off', 'on')),
		new ColorOption(
			"Header text colour", 
			"Change header font colour", 
			"header_text_color", 
			""),
		new FileOption(
			"Logo", 
			"Insert your own Logo. Upload or insert url.", 
			"logo"),
		new TextOption(
			"Header height", 
			"Your header height in px (and navigation position (y) at the same time), just enter a number. <br>
			This is not your header image height, you can specify your header image separately in the fields below. <br>
			Try 25px or 63px less than your header-image-height to fit perfectly...", 
			"header_height", 
			""),
		new DropdownOption(
			"Header width", 
			"Do you like the header in full width or as wide as your site?", 
			"header_width", 
			array('default', 'full-width')),
		new FileOption(
			"Header image", 
			"Insert your own header image. Upload or insert url. <br>
			Default width is 1000px, the height (and full width option) can be adjusted above. <br>
			For no image write 'none'.", 
			"header_img",
			'',
			'start',
			'Header image'),
			new DropdownOption(
				"Header image repeat", 
				"Repeat header image: x=horizontally, y=vertically", 
				"header_img_repeat", 
				array('no repeat', 'x', 'y', 'x+y'),
					"",
					false
					),		
			new DropdownOption(
				"Header image x-position", 
				"If header image is smaller, you can choose to align left, center or right", 
				"header_img_x", 
				array('left', 'center', 'right'),
					"",
					false),
		new TextOption(
			"Header image y-position", 
			"Distance from header image to top (in px), just enter a number", 
			"header_img_y", 
			"",
			"",
			"end"
		),
		)
		),
	new Group ("Menu", "menu",
		array(
		new BooleanOption(
			"Show the 'Home' menu item", 
			"You can disable the 'Home' menu item in the main navigation", 
			"menue_disable_home", 
			true),
		new BooleanOption(
			"Show community navigation", 
			"Enable Buddypress menu-items in the main navigation", 
			"menue_enable_community", 
			true),
		new DropdownOption(
			"Menu x-position", 
			"Align the menu left or right", 
			"menu_x", 
			array('left', 'right')),
		new DropdownOption(
			"Menu style", 
			"Choose a menu style", 
			"bg_menu_style", 
			array('tab style', 'closed style', 'simple', 'bordered' )),
		new ColorOption(
			"Menu border bottom", 
			"Would you like to underline your menu? Select a colour.", 
			"menu_underline", 
			""),
		new ColorOption(
			"Menu font colour", 
			"Change menu font colour", 
			"menue_link_color", 
			""),
		new ColorOption(
			"Menu font colour &raquo; current and mouse over", 
			"Change menu font colour from currently displayed menu item <br>
			or when mouse moves over", 
			"menue_link_color_current", 
			""),
		new ColorOption(
			"Menu background colour", 
			"Change the menu bar's general background colour", 
			"bg_menue_link_color", 
			""),
		new FileOption(
			"Menu background image", 
			"Insert your own background image for the menu bar. Upload or insert url.", 
			"bg_menu_img", 
			""),
		new DropdownOption(
			"Menu background repeat", 
			"Repeat background image: x=horizontally, y=vertically", 
			"bg_menu_img_repeat", 
			array('no repeat', 'x', 'y', 'x+y')),
		new ColorOption(
			"Menu background colour &raquo; current", 
			"Change background colour from currently displayed menu item", 
			"bg_menue_link_color_current", 
			""),
		new FileOption(
			"Menu background image &raquo; current", 
			"Background image of the currently displayed menu item. Upload or insert url.", 
			"bg_menu_img_current", 
			""),
		new DropdownOption(
			"Menu background image repeat &raquo current", 
			"Repeat background image: x=horizontally, y=vertically", 
			"bg_menu_img_current_repeat", 
			array('no repeat', 'x', 'y', 'x+y')),
		new ColorOption(
			"Menu background colour &raquo; mouse over and drop down list", 
			"Change a menu item's background colour when mouse moves over it, <br>
			and drop down background colour", 
			"bg_menue_link_color_hover", 
			""),
		new ColorOption(
			"Menu background colour &raquo; drop down list mouse over", 
			"Change background colour of hovered drop down menu item <br>
			(when the mouse moves over it)", 
			"bg_menue_link_color_dd_hover", 
			""),
		new DropdownOption(
			"Menu corner radius", 
			"Do you want your menu corners to be rounded?", 
			"menu_corner_radius", 
			array('all rounded', 'just the bottom ones', 'not rounded')),
		)
		),
	new Group ("Sidebars", "sidebars",
		array(
		new TextOption(
			"Left sidebar width", 
			"Change the left sidebar width - in pixel. Just enter a number. ", 
			"leftsidebar_width", 
			"225",
			"",
			"start",
			"Left sidebar"),
			new ColorOption(
				"Left sidebar background colour", 
				"Change background colour of the left sidebar. ", 
				"bg_leftsidebar_color", 
				"", 
				false),
			new FileOption(
				"Left sidebar background image", 
				"Your own background image for the left sidebar. Upload or insert url.", 
				"bg_leftsidebar_img", 
				"", 
				false),
		new DropdownOption(
			"Left sidebar background repeat", 
			"Repeat background image: x=horizontally, y=vertically", 
			"bg_leftsidebar_img_repeat", 
			array('no repeat', 'x', 'y', 'x+y'), 
			"",
			'end'),
		new TextOption(
			"Right sidebar width", 
			"Change the right sidebar width - in pixel. Just enter a number. ", 
			"rightsidebar_width", 
			"225",
			"",
			"start",
			"Right sidebar"),
			new ColorOption(
				"Right sidebar background colour", 
				"Change background colour of the right sidebar. ", 
				"bg_rightsidebar_color", 
				"", 
				false),
			new FileOption(
				"Right sidebar background image", 
				"Your own background image for the right sidebar. Upload or insert url.", 
				"bg_rightsidebar_img", 
				"", 
				false),
		new DropdownOption(
			"Right sidebar background repeat", 
			"Repeat background image: x=horizontally, y=vertically", 
			"bg_rightsidebar_img_repeat", 
			array('no repeat', 'x', 'y', 'x+y'), 
			"",
			'end'),		
		new DropdownOption(
			"Sidebar widget title style", 
			"Choose a style for the widget titles", 
			"bg_widgettitle_style", 
			array('angled', 'rounded', 'transparent')),
		new DropdownOption(
			"Sidebar widget title font style", 
			"Change the widget title's font style", 
			"widgettitle_font_style", 
			array('Arial, sans-serif', 'Impact, sans-serif', 'Helvetica, Arial, sans-serif', 'Century Gothic, Avant Garde, Arial, sans-serif', 'Times New Roman, Times', 'Garamond, Times New Roman, Times'),
			"",
			"start",
			"Sidebar widget title fonts"),
			new TextOption(
				"Widget title font size", 
				"Font size of your widget titles in px, just enter a number, default=13", 
				"widgettitle_font_size", 
				"",
				"", 
				false),
		new ColorOption(
			"Sidebar widget title font colour", 
			"Change font colour of the widget titles", 
			"widgettitle_font_color", 
			"", 
			'end'),
		new ColorOption(
			"Sidebar widget title background colour", 
			"Change background colour of the widget titles", 
			"bg_widgettitle_color", 
			"", 
			"start", 
			"Sidebar widget titles background",
			false),
		new FileOption(
			"Sidebar widget title background image", 
			"Your own background image for the widget title. Upload or insert url.", 
			"bg_widgettitle_img", 
			"", 
			false),
		new DropdownOption(
			"Sidebar widget title background repeat", 
			"Repeat background image: x=horizontally, y=vertically", 
			"bg_widgettitle_img_repeat", 
			array('no repeat', 'x', 'y', 'x+y'), 
			"",
			'end'),
		new DropdownOption(
			"Capitalizing in widgets", 
			"Capitalize the fonts in lists in your widgets?", 
			"capitalize_widgets_li", 
			array('no', 'yes'), 
			"",
			"start",
			"Capitalizing"),
		new DropdownOption(
			"Capitalizing the widget titles", 
			"Capitalize the titles in your widgets?", 
			"capitalize_widgettitles", 
			array('no', 'yes'),
			"",
			'end'),					
		)
		),
	new Group ("Footer", "footer",
		array(
		new DropdownOption(
			"Footer width", 
			"Do you like the footer in full width or as wide as your site?", 
			"footer_width", 
			array('default', 'full-width')),
		new TextOption(
			"Footer height", 
			"Change the footer height, in px, just enter a number <br>
			This option is not the footer widget height, you can define that one below.", 
			"footerall_height", 
			""),
		new ColorOption(
			"Footer background", 
			"Change background colour of the footer", 
			"bg_footerall_color", 
			""),
		new FileOption(
			"Footer background image", 
			"Background image for the footer background. Upload or insert url.", 
			"bg_footerall_img", 
			""),
		new DropdownOption(
			"Footer background image repeat", 
			"Repeat background image: x=horizontally, y=vertically", 
			"bg_footerall_img_repeat", 
			array('no repeat', 'x', 'y', 'x+y')),	
		new TextOption(
			"Footer widget height", 
			"Change the footer widgets height, in px, just enter a number <br>
			This option is nice to have your footer widget areas all the same height.", 
			"footer_height", 
			""),	
		new ColorOption(
			"Footer widget background", 
			"Change background colour of the footer widgets", 
			"bg_footer_color", 
			""),
		new FileOption(
			"Footer widget background image", 
			"Background image for the footer widgets background. Upload or insert url.", 
			"bg_footer_img", 
			""),
		new DropdownOption(
			"Footer widget background image repeat", 
			"Repeat background image: x=horizontally, y=vertically", 
			"bg_footer_img_repeat", 
			array('no repeat', 'x', 'y', 'x+y')),
		)
		),
	new Group ("Buddypress", "buddypress",
		array(
		new DropdownOption(
			"Login bar header", 
			"Select a login bar at the top of the header", 
			"bp_login_bar_top", 
			array('on', 'off' )),
		new BooleanOption(
			"Use Buddypress default sub-navigation", 
			"This sub-navigation is the secondary level navigation, <br>
			e.g. for profile it contains: [Public, Edit Profile, Change Avatar]<br>
			If you use the community navigation widget, you don't need this navigation. <br>
			If you want to use a horizontally sub-navigation - choose this one.", 
			"bp_default_navigation", 
			true),
		new ColorOption(
			"BuddyPress sub navigation background colour", 
			"Change the background colour of the Buddypress component sub navigation", 
			"bg_content_nav_color", 
			""),
		new BooleanOption(
			"Show search bar", 
			"enable BuddyPress search bar in header", 
			"menue_enable_search", 
			true),
		new BooleanOption(
			"Use global Buddydev search instead of bp-search", 
			"Replace the BuddyPress search (which comes with dropdown menu) with the Buddydev search. <br>
			The Buddydev search is an easy one-field global search with nice result-listing.", 
			"buddydev_search", 
			true),
		new DropdownOption(
			"Search bar x-position", 
			"If selected, you want the search bar left or right?", 
			"searchbar_x", 
			array('right', 'left')),
		new TextOption(
			"Search bar y-position", 
			"Distance from search bar to top (in px), just enter a number", 
			"searchbar_y", 
			""),
		new DropdownOption(
			"Login sidebar", 
			"Turn auto Buddypress login in the right sidebar on/off. <br>
			You can add this feature as a widget into every widgetarea you like.", 
			"login_sidebar", 
			array('on', 'off')),
		new TextOption(
			"Login sidebar text", 
			"Define the text displayed in the login sidebar when you're logged out.", 
			"bp_login_sidebar_text", 
			"")
		)
		),
	new Group ("Profile", "profile",
		array(
		new DropdownOption(
			"Show Profile header", 
			"Display profile header, can be used as widget area", 
			"bp_profile_header", 
			array('on', 'off')),
		new DropdownOption(
			"Profile Sidebars", 
			"Where do you like to have your sidebars in profiles? <br>
			default = the global settings and sidebars will be used<br>
			none = no sidebars, full width<br>
			left = left profile sidebar, this will overwrite the global settings and display the left profile sidebar<br>
			right = right profile sidebar, this will overwrite the global settings and display the right profile sidebar<br>
			left and right = This option will display the left and right profile sidebars and overwrite the global settings<br>
			Note: all sidebars can be filled with widgets. Without widgets there will be the user avatar and information like in the member header", 
			"bp_profile_sidebars", 
			array('default', 'none', 'left', 'right', 'left and right')),
		new TextOption(
			"Profile avatar size", 
			"Define the size of the profile avatar. Width and height is the same", 
			"bp_profiles_avatar_size", 
			""),
		new TextOption(
			"Profile menu order", 
			"Change the menu order in the profiles. Write the order in by slug, comma-separated. <br>
			Note: a slug is the name as it is written in the url, <br>
			means all letters in small, no symbols, ...", 
			"bp_profiles_nav_order", 
			"")
		)
		),
	new Group ("Groups", "groups",
		array(
		new DropdownOption(
			"Show Groups header", 
			"Display group header, can be used as widget area", 
			"bp_groups_header", 
			array('on', 'off')),
		new DropdownOption(
			"Groups Sidebars",
			"Where do you like to have your sidebars in groups? <br>
			default = the global settings and sidebars will be used<br>
			none = no sidebars, full width<br>
			left = left group sidebar, this will overwrite the global settings and display the left group sidebar<br>
			right = right group sidebar, this will overwrite the global settings and display the right group sidebar<br>
			left and right = this option will display the left and right group sidebars and overwrite the global settings<br>
			Note: all sidebars can be filled with widgets. Without widgets there will be the group avatar and information like in the group header",
			"bp_groups_sidebars",
			 array('default', 'none', 'left', 'right', 'left and right')),
		new TextOption(
			"Groups avatar size", 
			"Define the size of the group avatar. Width and height is the same <br>
			Just write a number, without px, default is 200.", 
			"bp_groups_avatar_size", 
			""),
		new TextOption(
			"Groups menu order", 
			"Change the menu order in the groups. Write the order in by slug, comma-separated. <br>
			Note: a slug is the name as it is written in the url, <br>
			means all letters in small, no symbols, ...", 
			"bp_groups_nav_order", 
			"")
		)
		),
	new Group ("Slideshow", "slideshow",
		array(
		new DropdownOption(
			"Enable slideshow", 
			"Enable slideshow", 
			"enable_slideshow_home", 
			array('home', 'off', 'all')),
		new DropdownCatOption(
			"Slideshow post categories", 
			"The slideshow takes images, titles and text-excerpts of the last 4 posts.<br>
			You can select the category the posts should be taken from. <br>
			For more info check out the <a href='http://themekraft.com/faq/' target='_blank'>FAQ</a>, especially <a href='http://themekraft.com/faq/slideshow/' target='_blank'>slideshow</a> and <a href='http://themekraft.com/faq/featured-image/' target='_blank'>featured image</a>.", 
			"slideshow_cat", 
			$option_categories),
		new TextOption(
			"Amount", 
			"Define the amount of posts. This option works just with the full width image slider.", 
			"slideshow_amount", 
			""),
		new TextOption(
			"Post type", 
			"Define the post type to display instead of posts. For pages write 'page', <br>
			for a custom post type the name of the cutsom post type, e.g. 'radio'", 
			"slideshow_post_type", 
			""),
		new TextOption(
			"Page IDs", 
			"Page IDs, comma separated. Just working if you use post types instead of categories", 
			"slideshow_show_page", 
			""),
		new TextOption(
			"Sliding time", 
			"Define the sliding time in ms", 
			"slideshow_time", ""),
		new TextOption(
			"Order posts by", 
			"* orderby=author<br>
			* orderby=date<br>
			* orderby=title<br>
			* orderby=modified<br>
			* orderby=menu_order used most often for Pages (Order field in the Edit Page -> Attributes box) and attachments (the integer fields in the Insert / Upload Media -> Gallery dialog), but could be used for any post type with distinct menu_order values (they all default to 0).<br>
			* orderby=parent<br>
			* orderby=ID<br>
			* orderby=rand<br>
			* orderby=meta_value Note: A meta_key=keyname must also be present in the query. Note also that the sorting will be alphabetical which is fine for strings (i.e. words), but can be unexpected for numbers (e.g. 1, 3, 34, 4, 56, 6, etc, rather than 1, 3, 4, 6, 34, 56 as you might naturally expect).<br>
			* orderby=meta_value_num - Order by numeric meta value (available with Version 2.8)<br>
			* orderby=none - No order (available with Version 2.8)<br>
			* orderby=comment_count - (available with Version 2.9) <br>", 
			"slideshow_orderby", 
			""),
		new DropdownOption(
			"Slideshow style", 
			"Select a type of slideshow.", 
			"slideshow_style", 
			array('default','full width')),
		new DropdownOption(
			"Caption", 
			"Show just the images or also titles and excerpts?", 
			"slideshow_caption", 
			array('on', 'off')),
		new DropdownOption(
			"Shadow", 
			"Select if you'd like to have a shadow under the top slideshow.<br>
			Note: just for bright background and static width between 990 and about 1100 pixels.", 
			"slideshow_shadow", 
			array('no shadow', 'shadow', 'sharper shadow')),
		)
		),
	new Group ("CSS", "overwrite",
		array(
		new TextOption(
			"Overwrite CSS", 
			"This is your place to overwrite existing CSS.<br>
			This way you are able to customize even the smallest CSS details. <br>
			If you know how to write, you can play around a bit!<br>
			<br>
			Here's an example how to change your body background picture:<br>
			<br>
			body {<br>
			background-image:url(url-to-your-picture);<br>
			}<br>
			<br>", 
			"overwrite_css", 
			"", 
			true,
			false),
		)
		),
		
		);
}?>