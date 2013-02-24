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
$req_cap_to_edit = 'read'; // the user capability that is required to access the CheezCap settings page
function cc_get_user_roles(){
    global $wp_roles;
    $return_roles = array();
    if(!empty($wp_roles)){
        $roles = $wp_roles->roles;
        foreach ($roles as $role){
            $max_cap = array_shift(array_keys($role['capabilities']));
            $return_roles[$max_cap] = $role['name'];
        }
    }
    return $return_roles;

}
function cap_get_options() {
	$pages     = get_pages();
	$option    = Array();
	$option[0] = __("All pages",'cc');
	$i         = 1;
	foreach ($pages as $pagg) {
		$option[$i] = $pagg->post_title;
		$i++;
	}
	$option_pages = $option;

	$args       = array('echo' => '0','hide_empty' => '0');
	$categories = get_categories($args);
	$option     = Array();
	$i = 0;
	foreach($categories as $category) {
		$option[$i]['name'] = $category->name;
		$option[$i]['id'] = $category->term_id;
		$i++;
	}

	$option_categories = $option;
    $magazine_styles = array(
                            __('img-mouse-over', 'cc'),
                            __('img-left-content-right', 'cc'),
                            __('img-right-content-left', 'cc'),
                            __('img-over-content', 'cc'),
                            __('img-under-content', 'cc')
                        );
	$options = array(
		new Group (__("General",'cc'), "general",
			array(
			new DropdownOption(
				__("Colour scheme",'cc'),
				__("Select the colour scheme of your website.",'cc'),
				"style_css",
				apply_filters('cc_get_color_scheme', array( __('white','cc'),  __('grey','cc'), __('dark','cc')))),
			new TextOption(
				__("Website width",'cc'),
				__("Just type in the number, either in px or %. Default is 1000. <br>
				Tip: If you use the full-width slider, don't make your site bigger than 1006px, or use the normal slider with preview.",'cc'),
				"website_width",
				"1000",
				"",
				"start",
				__("Website width",'cc')),
			new DropdownOption(
				__("Fluid or static width?",'cc'),
				__("Do you want your layout fluid(%) or static(px)? <br>
				Notes: when you use the slideshow, better don't use fluid width. <br>
				And if you use the slideshow shadow, it only looks nice with a static width between 990 and about 1100px.",'cc'),
				"website_width_unit",
				array('px', '%'),
				"",
				'end'),
			new BooleanOption(
				__("Use standard Wordpress background settings",'cc'),
				__("Enable this option, if you like to use the standard wordpress settings page.",'cc'),
				"add_custom_background",
				false,
				'start',
				__('Background','cc')),
				new ColorOption(
					__("Background colour",'cc'),
					__("Change your background colour",'cc'),
					"bg_body_color",
					"",
					'',
					''),
				new FileOption(
					__("Background image",'cc'),
					__("Insert your own background image. Upload or insert url.",'cc'),
					"bg_body_img",
					'',
					false,
					''),
				new BooleanOption(
					__("Fixed background image",'cc'),
					__("Fix the position of your body background image",'cc'),
					"bg_body_img_fixed",
					false,
					false,
					''),
				new DropdownOption(
					__("Background position",'cc'),
					__("Position of the background image: center, left, right",'cc'),
					"bg_body_img_pos",
					array(__('center','cc'), __('left','cc'), __('right','cc')),
					'',
					false,
					''),
			new DropdownOption(
				__("Background repeat",'cc'),
				__("Repeat background image: x=horizontally, y=vertically",'cc'),
				"bg_body_img_repeat",
				array(__('no repeat','cc'), 'x', 'y', 'x+y'),
				'',
				'end',
				''),
	        new BooleanOption(
	                __('Responsive', CC_TRANSLATE_DOMAIN),
	                __('Enable/disable responsive mode. <br><br>
	                <b>Important Note:</b> When responsive mode is on, your <i>website width</i> and <i>sidebar width settings</i> will be inactive <br>
	                as the responsive design takes automatically care of it.', CC_TRANSLATE_DOMAIN),
	                'cc_responsive_enable'),
			new ColorOption(
				__("Container colour",'cc'),
				__("Change the background colour of the content part,<br>
				write transparent for no color",'cc'),
				"bg_container_color",
				"",
				"start",
				__("Container",'cc')),
				new DropdownOption(
					__("Show / hide the vertical lines",'cc'),
					__("The vertical lines that divide your container are default, <br>
					you can disable them if you like.",'cc'),
					"bg_container_nolines",
					array(__('show','cc'), __('hide','cc')),
					"",
					false),
				new ColorOption(
					__("Vertical lines colour",'cc'),
					__("Change the colour of the vertical lines. <br>
					Tip: Best is to have them in the colour of the background and other lines. ",'cc'),
					"v_line_color",
					"",
					'',
					''),
				new FileOption(
					__("Container background image",'cc'),
					__("Change background image for the container (currently the vertical lines <br>
					that separate the sidebars). Upload or insert url.",'cc'),
					"bg_container_img",
					"",
					false),
				new DropdownOption(
					__("Container background repeat",'cc'),
					__("Repeat background image: x=horizontally, y=vertically",'cc'),
					"bg_container_img_repeat",
					array(__('no repeat','cc'), 'x', 'y', 'x+y'),
					"",
					false),
			new DropdownOption(
				__("Container corner radius",'cc'),
				__("Do you want your container corners to be rounded?",'cc'),
				"container_corner_radius",
				array(__('rounded','cc'), __('not rounded','cc')),
				__("rounded",'cc'),
				'end'),
			new DropdownOption(
				__("Sidebar default position",'cc'),
				__("Where do you like to have your sidebars? Define your default layout.",'cc'),
				"sidebar_position",
				array(__('right','cc'), __('left and right','cc'), __('left','cc'), __('full-width','cc')),
				__('right','cc')),
			new DropdownOption(
				__("Title font style",'cc'),
				__("Change the title font style (h1 and h2)",'cc'),
				"title_font_style",
				array('Arial, sans-serif', 'Helvetica, Arial, sans-serif', 'Century Gothic, Avant Garde, Arial, sans-serif', 'Arial Black, Arial, sans-serif', 'Impact, Arial, sans-serif', 'Times New Roman, Times', 'Garamond, Times New Roman, Times'),
				"Arial, sans-serif",
				"start",
				__("Titles",'cc')),
				new TextOption(
					__("Title size",'cc'),
					__("Change the title font size (h1 and h2), default is 28px, just enter a number",'cc'),
					"title_size",
					"",
					"",
					false),
				new DropdownOption(
					__("Titles font weight",'cc'),
					__("Do you want your titles bold or normal?",'cc'),
					"title_weight",
					array(__('bold','cc'), __('normal','cc')),
					__("bold",'cc'),
					false),
			new ColorOption(
				__("Title colour",'cc'),
				__("Change title colour",'cc'),
				"title_color",
				"","end"),
			new DropdownOption(
				__("Subtitle font style",'cc'),
				__("Change the subtitle font style (h3-h6)",'cc'),
				"subtitle_font_style",
				array('Arial, sans-serif', 'Helvetica, Arial, sans-serif', 'Century Gothic, Avant Garde, Arial, sans-serif', 'Arial Black, Arial, sans-serif', 'Impact, Arial, sans-serif', 'Times New Roman, Times', 'Garamond, Times New Roman, Times'),
				"Arial, sans-serif",
				"start",
				__("Subtitles",'cc')),
				new DropdownOption(
					__("Subtitles font weight",'cc'),
					__("Do you want your subtitles bold or normal?",'cc'),
					"subtitle_weight",
					array(__('bold','cc'), __('normal','cc')),
					__("bold",'cc'),
					false),
			new ColorOption(
				__("Subtitle colour",'cc'),
				__("Change subtitle colour",'cc'),
				"subtitle_color",
				"","end"),
			new DropdownOption(
				__("Show excerpts",'cc'),
				__("Just for category and archive views: use excerpts or show full content of your posts",'cc'),
				"excerpt_on",
				array(__('content','cc'), __('excerpt','cc')),
				__("content",'cc'),
				"start",
				__("Excerpts",'cc')),
			new TextOption(
				__("Excerpt length",'cc'),
				__("Change the excerpt length, default is 30 words",'cc'),
				"excerpt_length",
				"","","end"),
			new DropdownOption(
				__("Font style",'cc'),
				__("Change the font style",'cc'),
				"font_style",
				array('Arial, sans-serif', 'Helvetica, Arial, sans-serif', 'Century Gothic, Avant Garde, Arial, sans-serif', 'Times New Roman, Times', 'Garamond, Times New Roman, Times'),
				"Arial, sans-serif",
				"start",
				__("Fonts",'cc')),
			new TextOption(
				__("Font size",'cc'),
				__("Change the standard font size, default is 13px, just enter a number",'cc'),
				"font_size",
				"","",
				false),
			new ColorOption(
				__("Font colour",'cc'),
				__("Change font colour",'cc'),
				"font_color",
				"",
				'end'),
			new ColorOption(
				__("Link colour",'cc'),
				__("Change link colour. <br>
				Notes: You just need to change the link colour to have a nice effect on all link and button colours. <br>
				The hover colour will automatically be your font colour or the default font colour. <br>
				Optional, you can also choose a hover colour, background colour, background hover colour or if and when to underline. ",'cc'),
				"link_color",
				"",
				"start",
				__("Links",'cc')),
			new ColorOption(
				__("Link hover colour",'cc'),
				__("Change link colour for mouse moves over.",'cc'),
				"link_color_hover",
				"",
				false),
			new DropdownOption(
				__("BuddyPress subnavigation adapting",'cc'),
				__("Use link hover colour for the BuddyPress subnav also? <br>
				By default the subnav links adapts the link colour and link hover colour. <br>
				Sometimes the link hover colour can look ugly here and you don't want the subnav to adapt. <br>
				Then you can change the colour adapting here easily. ",'cc'),
				"link_color_subnav_adapt",
				array(__('just the link colour','cc'), __('link colour and hover colour','cc')),
				__("link colour and hover colour",'cc'),
				false),
			new DropdownOption(
				__("Link underline",'cc'),
				__("Choose if (and when) to underline links.",'cc'),
				"link_underline",
				array(__('never','cc'), __('always','cc'), __('just for mouse over','cc'), __('just when normal','cc')),
				__("just for mouse over",'cc'),
				false),
			new ColorOption(
				__("Link background colour",'cc'),
				__("Change link background colour.",'cc'),
				"link_bg_color",
				"",
				false),
			new ColorOption(
				__("Link background hover colour",'cc'),
				__("Change link background colour for mouse moves over. <br>
				Watch out you have enough contrast to the (hover) link colour and also the font colour!",'cc'),
				"link_bg_color_hover",
				"",
				false),
			new DropdownOption(
				__("Titles adapting",'cc'),
				__("Do you like to use the link background colours or underline effetcs for the titles (h1-h6) also? <br>
				By default they adapt just the link colour and link hover colour.",'cc'),
				"link_styling_title_adapt",
				array(__('just the hover effect','cc'), __('link colour and hover colour','cc'), __('...the underline effects too','cc'), __('the background colours too','cc'), __('adapt all link styles','cc') ),
				"",
				'end'),
			new FileOption(
				__("Favicon image",'cc'),
				__("Insert your own favicon image. Upload or insert url.",'cc'),
				"favicon"),
			// Default homepage
			new DropdownOption(
				'<span class="blog-item-home">' . __("Show / hide avatars",'cc') . '</span>',
				'<span class="blog-item-home">' . __("Show or hide the avatars in the post listing. <br>
				This option is for the standard WordPress Homepage showing your latest articles. <br>
				To select a page as your homepage, go to Settings -> Reading.",'cc'). '</span>',
				"default_homepage_hide_avatar",
				array(__('show','cc'), __('hide','cc')),
				__("show",'cc'),
				"start",
				__("Default homepage",'cc')),
	        new DropdownOption(
				__("Posts listing style on home page",'cc'),
				__("Display style for home page list page.",'cc'),
				"posts_lists_style_home",
				array(__('blog','cc'), __('magazine','cc')),
				__("blog",'cc'),
				""),
	        new DropdownOption(
	            __('List Post Template (for magazine style) home page list page.','cc'), // Ole K H - added translation.
				__("Choose a layout for the magazine style.",'cc'),
				"magazine_style_home",
				$magazine_styles,
				'',
				""
	            ),
			new DropdownOption(
				'<span class="blog-item-home">' .__("Last 3 Posts on home",'cc') . '</span>',
				'<span class="blog-item-home">' . __("Display last 3 posts. <br> ",'cc') . '</span>',
				"default_homepage_last_posts",
				array(__('show','cc'), __('hide','cc')),
				__("show",'cc'),
				false),
			new DropdownOption(
				'<span class="blog-item-home">' .__("Post listing style",'cc'). '</span>',
				'<span class="blog-item-home">' .__("Select a style how to display the latest posts. <br>
				You can also list your posts in magazine style, showing the featured images, check out the option 'Posts listing style on home page' above here.",'cc'). '</span>',
				"default_homepage_style",
				array(__('bubbles','cc'), __('default','cc')),
				__("bubbles",'cc'),
				false),
			new DropdownOption(
				'<span class="blog-item-home">' .__("Show / hide date, category and author",'cc'). '</span>',
				'<span class="blog-item-home">' .__("Show or hide the date, category and author in the post listing.",'cc'). '</span>',
				"default_homepage_hide_date",
				array(__('show','cc'), __('hide','cc')),
				__("show",'cc'),
				'end'),
			// Posts lists (categories, tags)
			new DropdownOption(
				__("Posts lists on archive pages",'cc'),
				__("Display style for categories, tags and other possible taxonomy types.",'cc'),
				"posts_lists_style_taxonomy",
				array(__('blog','cc'), __('magazine','cc')),
				__("blog",'cc'),
				"start"),
            new DropdownOption(
                __('Archives page template', 'cc'),
                __('Select Archives page template', 'cc'),
                'archive_template',
                array(__('right', 'cc'), __('left','cc'), __('left and right','cc')),
                '',
                ''),
			new DropdownOption(
				'',
				__("List Post Template (for magazine style) for categories, tags and other possible taxonomy types.",'cc'),
				"magazine_style_taxonomy",
				$magazine_styles,
				'',
				""),
			// Posts lists (dates)
			new DropdownOption(
				'',
				__("Display style for dates archives (by year, month, date).",'cc'),
				"posts_lists_style_dates",
				array(__('blog','cc'), __('magazine','cc')),
				__("blog",'cc'),
				""),
			new DropdownOption(
				'',
				__("List Post Template (for magazine style) for dates archives (by year, month, date).",'cc'),
				"magazine_style_dates",
				$magazine_styles,
				'',
				""),
			// Posts lists (author archives)
			new DropdownOption(
				'',
				__("Display style for author archives.",'cc'),
				"posts_lists_style_author",
				array(__('blog','cc'), __('magazine','cc')),
				__("blog",'cc'),
				""),
			new DropdownOption(
				'',
				__("List Post Template (for magazine style) for author archives.",'cc'),
				"magazine_style_author",
				$magazine_styles,
				'',
				""),
			// Posts lists (extra options)
			new DropdownOption(
				'<span class="blog-items">' . __("Show / hide avatars in blog display",'cc') . '</span>',
				'<span class="blog-items">'.  __("Show or hide the avatars in the post listing. <br>
				This option is for categories, tags and archives pages, showing your articles.",'cc'),
				"posts_lists_hide_avatar" . '</span>',
				array(__('show','cc'), __('hide','cc')),
				__("show",'cc'),
				"",
				__("Posts archive pages (categories, tags, dates)",'cc')),
			new DropdownOption(
				'<span class="blog-items">' .__("Post listings layout for blog style",'cc'). '</span>',
				'<span class="blog-items">' .__("Select a layout how to display the latest posts. <br>
				You can also list your posts in magazine style, showing the featured images, check out the option 'Display style for author archives' above here..",'cc'),
				"posts_lists_style". '</span>',
				array(__('bubbles','cc'), __('default','cc')),
				__("bubbles",'cc'),
				false),
			new DropdownOption(
				'<span class="blog-items">' .__("Show / hide date, category and author in blog display",'cc'). '</span>',
				'<span class="blog-items">' .__("Show or hide the date, category and author in the post listing.",'cc'). '</span>',
				"posts_lists_hide_date",
				array(__('show','cc'), __('hide','cc')),
				__("show",'cc'),
				'end'),
			// Login
			new FileOption(
				__("Login page logo",'cc'),
				__("Insert your own image for the login page. Upload or insert url.",'cc'),
				"bg_loginpage_img",
				"",
				"start",
				__("Login",'cc')),
			new TextOption(
				__("Login logo height",'cc'),
				__("Define the login logo height, the width should be 326px max",'cc'),
				"login_logo_height",
				"",
				"",
				false),
			new ColorOption(
				__("Login page background colour",'cc'),
				__("Change login page background colour",'cc'),
				"bg_loginpage_body_color",
				"",
				false),
			new ColorOption(
				__("Login page backtoblog fade colour 1",'cc'),
				__("Change login page backtoblog colour fade 1",'cc'),
				"bg_loginpage_backtoblog_fade_1",
				"",
				false),
			new ColorOption(
				__("Login page backtoblog fade colour 2",'cc'),
				__("Change login page backtoblog colour fade 2",'cc'),
				"bg_loginpage_backtoblog_fade_2",
				"",
				"end"),
			new TextOption(
				__("Add scripts to head",'cc'),
				__("...for google fonts, analytics, etc. <br>
				Here you can add stuff right before the end of the head tag.",'cc'),
				"add_to_head",
				"",
				true,
				true),
			new TextOption(
				__("Add scripts to footer",'cc'),
				__("...for analytics, ads, etc. <br>
				Here you can add stuff right before the end of the footer tag.",'cc'),
				"add_to_footer",
				"",
				true,
				true),
			)),
		new Group (__("Header",'cc'), "header",
			array(
			new BooleanOption(
				__("Use standard Wordpress custom image header settings",'cc'),
				__("Enable this option, if you like to use the standard wordpress settings Page.",'cc'),
				"add_custom_image_header",
				false),
			new DropdownOption(
				__("Show header text",'cc'),
				__("Show header text or not?",'cc'),
				"header_text",
				array(__('on','cc'), __('off','cc')),
				__('on','cc')),
			new ColorOption(
				__("Header text colour",'cc'),
				__("Change header font colour",'cc'),
				"header_text_color",
				""),
			new FileOption(
				__("Logo",'cc'),
				__("Insert your own Logo. Upload or insert url.",'cc'),
				"logo"),
			new TextOption(
				__("Header height",'cc'),
				__("Your header height in px (and navigation position (y) at the same time), just enter a number. <br>
				This is not your header image height, you can specify your header image separately in the fields below. <br>
				Try 25px or 63px less than your header-image-height to fit perfectly...",'cc'),
				"header_height",
				"200"),
			new DropdownOption(
				__("Header width",'cc'),
				__("Do you like the header in full width or as wide as your site?",'cc'),
				"header_width",
				array(__('default','cc'), __('full-width','cc')),
				__('default','cc')),
			new FileOption(
				__("Header image",'cc'),
				__("Insert your own header image. Upload or insert url. <br>
				Default width is 1000px, the height (and full width option) can be adjusted above. <br>
				For no image write 'none'.",'cc'),
				"header_img",
				'',
				'start',
				__('Header image','cc')),
				new DropdownOption(
					__("Header image repeat",'cc'),
					__("Repeat header image: x=horizontally, y=vertically",'cc'),
					"header_img_repeat",
					array(__('no repeat','cc'), 'x', 'y', 'x+y'),
						__("no repeat",'cc'),
						false
						),
				new DropdownOption(
					__("Header image x-position",'cc'),
					__("If header image is smaller, you can choose to align left, center or right",'cc'),
					"header_img_x",
					array(__('left','cc'), __('center','cc'), __('right','cc')),
					__("left",'cc'),
					false),
			new TextOption(
				__("Header image y-position",'cc'),
				__("Distance from header image to top (in px), just enter a number",'cc'),
				"header_img_y",
				"",
				"",
				"end"
			),
			)
			),
		new Group (__("Menu",'cc'), "menu",
			array(
			new BooleanOption(
				__("Show the 'Home' menu item",'cc'),
				__("You can disable the 'Home' menu item in the main navigation",'cc'),
				"menue_disable_home",
				true),
			new BooleanOption(
				__("Show community navigation",'cc'),
				__("Enable Buddypress menu-items in the main navigation",'cc'),
				"menue_enable_community",
				true),
			new DropdownOption(
				__("Menu x-position",'cc'),
				__("Align the menu left or right",'cc'),
				"menu_x",
				array(__('left','cc'), __('right','cc')),
				__('left','cc')),
			new DropdownOption(
				__("Menu style",'cc'),
				__("Choose a menu style",'cc'),
				"bg_menu_style",
				array(__('tab style','cc'), __('closed style','cc'), __('simple','cc'), __('bordered','cc') ),
				__('tab style','cc')),
			new ColorOption(
				__("Menu border bottom",'cc'),
				__("Would you like to underline your menu? Select a colour.",'cc'),
				"menu_underline",
				""),
			new ColorOption(
				__("Menu font colour",'cc'),
				__("Change menu font colour",'cc'),
				"menue_link_color",
				""),
			new ColorOption(
				__("Menu font colour &raquo; current and mouse over",'cc'),
				__("Change menu font colour from currently displayed menu item <br>
				or when mouse moves over",'cc'),
				"menue_link_color_current",
				""),
			new ColorOption(
				__("Menu background colour",'cc'),
				__("Change the menu bar's general background colour",'cc'),
				"bg_menue_link_color",
				""),
			new FileOption(
				__("Menu background image",'cc'),
				__("Insert your own background image for the menu bar. Upload or insert url.",'cc'),
				"bg_menu_img",
				""),
			new DropdownOption(
				__("Menu background repeat",'cc'),
				__("Repeat background image: x=horizontally, y=vertically",'cc'),
				"bg_menu_img_repeat",
				array(__('no repeat','cc'), 'x', 'y', 'x+y'),
				__('no repeat','cc')),
			new ColorOption(
				__("Menu background colour &raquo; current",'cc'),
				__("Change background colour from currently displayed menu item",'cc'),
				"bg_menue_link_color_current",
				""),
			new FileOption(
				__("Menu background image &raquo; current",'cc'),
				__("Background image of the currently displayed menu item. Upload or insert url.",'cc'),
				"bg_menu_img_current",
				""),
			new DropdownOption(
				__("Menu background image repeat &raquo; current",'cc'),
				__("Repeat background image: x=horizontally, y=vertically",'cc'),
				"bg_menu_img_current_repeat",
				array(__('no repeat','cc'), 'x', 'y', 'x+y'),
				__('no repeat','cc')),
			new ColorOption(
				__("Menu background colour &raquo; mouse over and drop down list",'cc'),
				__("Change a menu item's background colour when mouse moves over it, <br>
				and drop down background colour",'cc'),
				__("bg_menue_link_color_hover",'cc'),
				""),
			new ColorOption(
				__("Menu background colour &raquo; drop down list mouse over",'cc'),
				__("Change background colour of hovered drop down menu item <br>
				(when the mouse moves over it)",'cc'),
				"bg_menue_link_color_dd_hover",
				""),
			new DropdownOption(
				__("Menu corner radius",'cc'),
				__("Do you want your menu corners to be rounded?",'cc'),
				"menu_corner_radius",
				array(__('all rounded','cc'), __('just the bottom ones','cc'), __('not rounded','cc')),
				__('all rounded','cc')),
			)
			),
		new Group (__("Sidebars",'cc'), "sidebars",
			array(
			new TextOption(
				__("Left sidebar width",'cc'),
				__("Change the left sidebar width - in pixel. Just enter a number. ",'cc'),
				"leftsidebar_width",
				"225",
				"",
				"start",
				__("Left sidebar",'cc')),
				new ColorOption(
					__("Left sidebar background colour",'cc'),
					__("Change background colour of the left sidebar. ",'cc'),
					"bg_leftsidebar_color",
					"",
					false),
				new FileOption(
					__("Left sidebar background image",'cc'),
					__("Your own background image for the left sidebar. Upload or insert url.",'cc'),
					"bg_leftsidebar_img",
					"",
					false),
			new DropdownOption(
				__("Left sidebar background repeat",'cc'),
				__("Repeat background image: x=horizontally, y=vertically",'cc'),
				"bg_leftsidebar_img_repeat",
				array(__('no repeat','cc'), 'x', 'y', 'x+y'),
				__("no repeat",'cc'),
				false),
			new DropdownOption(
				__("Left sidebar default navigation menu",'cc'),
				__("Display default navigation menu",'cc'),
				"bg_leftsidebar_default_nav",
				array(__('yes','cc'), __('no','cc')),
				__("yes",'cc'),
				'end'),
			new TextOption(
				__("Right sidebar width",'cc'),
				__("Change the right sidebar width - in pixel. Just enter a number. ",'cc'),
				"rightsidebar_width",
				"225",
				"",
				"start",
				__("Right sidebar",'cc')),
				new ColorOption(
					__("Right sidebar background colour",'cc'),
					__("Change background colour of the right sidebar. ",'cc'),
					"bg_rightsidebar_color",
					"",
					false),
				new FileOption(
					__("Right sidebar background image",'cc'),
					__("Your own background image for the right sidebar. Upload or insert url.",'cc'),
					"bg_rightsidebar_img",
					"",
					false),
			new DropdownOption(
				__("Right sidebar background repeat",'cc'),
				__("Repeat background image: x=horizontally, y=vertically",'cc'),
				"bg_rightsidebar_img_repeat",
				array(__('no repeat','cc'), 'x', 'y', 'x+y'),
				__("no repeat",'cc'),
				'end'),
			new DropdownOption(
				__("Sidebar widget title style",'cc'),
				__("Choose a style for the widget titles",'cc'),
				"bg_widgettitle_style",
				array(__('angled','cc'), __('rounded','cc'), __('transparent','cc')),
				__('angled','cc')),
			new DropdownOption(
				__("Sidebar widget title font style",'cc'),
				__("Change the widget title's font style",'cc'),
				"widgettitle_font_style",
				array('Arial, sans-serif', 'Impact, sans-serif', 'Helvetica, Arial, sans-serif', 'Century Gothic, Avant Garde, Arial, sans-serif', 'Times New Roman, Times', 'Garamond, Times New Roman, Times'),
				"Arial, sans-serif",
				"start",
				__("Sidebar widget title fonts",'cc')),
				new TextOption(
					__("Widget title font size",'cc'),
					__("Font size of your widget titles in px, just enter a number, default=13",'cc'),
					"widgettitle_font_size",
					"",
					"",
					false),
			new ColorOption(
				__("Sidebar widget title font colour",'cc'),
				__("Change font colour of the widget titles",'cc'),
				__("widgettitle_font_color",'cc'),
				"",
				'end'),
			new ColorOption(
				__("Sidebar widget title background colour",'cc'),
				__("Change background colour of the widget titles",'cc'),
				"bg_widgettitle_color",
				"",
				"start",
				__("Sidebar widget titles background",'cc'),
				false),
			new FileOption(
				__("Sidebar widget title background image",'cc'),
				__("Your own background image for the widget title. Upload or insert url.",'cc'),
				"bg_widgettitle_img",
				"",
				false),
			new DropdownOption(
				__("Sidebar widget title background repeat",'cc'),
				__("Repeat background image: x=horizontally, y=vertically",'cc'),
				"bg_widgettitle_img_repeat",
				array(__('no repeat','cc'), 'x', 'y', 'x+y'),
				__("no repeat",'cc'),
				'end'),
			new DropdownOption(
				__("Capitalizing in widgets",'cc'),
				__("Capitalize the fonts in lists in your widgets?",'cc'),
				"capitalize_widgets_li",
				array(__('no','cc'), __('yes','cc')),
				__("no",'cc'),
				"start",
				__("Capitalizing",'cc')),
			new DropdownOption(
				__("Capitalizing the widget titles",'cc'),
				__("Capitalize the titles in your widgets?",'cc'),
				"capitalize_widgettitles",
				array(__('no','cc'), __('yes','cc')),
				__("no",'cc'),
				'end'),
			)
			),
		new Group (__("Footer",'cc'), "footer",
			array(
			new DropdownOption(
				__("Footer width",'cc'),
				__("Do you like the footer in full width or as wide as your site?",'cc'),
				"footer_width",
				array(__('default','cc'), __('full-width','cc'))),
			new TextOption(
				__("Footer height",'cc'),
				__("Change the footer height, in px, just enter a number <br>
				This option is not the footer widget height, you can define that one below.",'cc'),
				"footerall_height",
				""),
			new ColorOption(
				__("Footer background",'cc'),
				__("Change background colour of the footer",'cc'),
				"bg_footerall_color",
				""),
			new FileOption(
				__("Footer background image",'cc'),
				__("Background image for the footer background. Upload or insert url.",'cc'),
				"bg_footerall_img",
				""),
			new DropdownOption(
				__("Footer background image repeat",'cc'),
				__("Repeat background image: x=horizontally, y=vertically",'cc'),
				"bg_footerall_img_repeat",
				array(__('no repeat','cc'), 'x', 'y', 'x+y'),
				__('no repeat','cc')),
			new TextOption(
				__("Footer widget height",'cc'),
				__("Change the footer widgets height, in px, just enter a number <br>
				This option is nice to have your footer widget areas all the same height.",'cc'),
				"footer_height",
				""),
			new ColorOption(
				__("Footer widget background",'cc'),
				__("Change background colour of the footer widgets",'cc'),
				"bg_footer_color",
				""),
			new FileOption(
				__("Footer widget background image",'cc'),
				__("Background image for the footer widgets background. Upload or insert url.",'cc'),
				"bg_footer_img",
				""),
			new DropdownOption(
				__("Footer widget background image repeat",'cc'),
				__("Repeat background image: x=horizontally, y=vertically",'cc'),
				"bg_footer_img_repeat",
				array(__('no repeat','cc'), 'x', 'y', 'x+y'),
				__('no repeat','cc')),
			)),
		new Group (__("BuddyPress",'cc'), "buddypress",
			array(
			new DropdownOption(
				__("Login bar header",'cc'),
				__("Select a login bar at the top of the header",'cc'),
				"bp_login_bar_top",
				array(__('on','cc'), __('off','cc') ),
				__('on','cc')),
			new BooleanOption(
				__("Use BuddyPress default sub-navigation",'cc'),
				__("This sub-navigation is the secondary level navigation, <br>
				e.g. for profile it contains: [Public, Edit Profile, Change Avatar]<br>
				If you use the community navigation widget, you don't need this navigation. <br>
				If you want to use a horizontally sub-navigation - choose this one.",'cc'),
				"bp_default_navigation",
				true),
			new ColorOption(
				__("BuddyPress sub navigation background colour",'cc'),
				__("Change the background colour of the Buddypress component sub navigation",'cc'),
				"bg_content_nav_color",
				""),
			new BooleanOption(
				__("Show search bar",'cc'),
				__("Enable BuddyPress search bar in header",'cc'),
				"menue_enable_search",
				true),
			new BooleanOption(
				__("Use global Buddydev search instead of bp-search",'cc'),
				__("Replace the BuddyPress search (which comes with dropdown menu) with the Buddydev search. <br>
				The Buddydev search is an easy one-field global search with nice result-listing.",'cc'),
				"buddydev_search",
				true),
			new DropdownOption(
				__("Search bar horizontal position",'cc'),
				__("If selected, you want the search bar left or right?",'cc'),
				"searchbar_x",
				array(__('right','cc'), __('left','cc')),
				__('right','cc')),
			new TextOption(
				__("Search bar vertical position",'cc'),
				__("Distance from search bar to top (in px), just enter a number",'cc'),
				"searchbar_y",
				""),
			new DropdownOption(
				__("Login sidebar",'cc'),
				__("Turn auto BuddyPress login in the right sidebar on/off. <br>
				You can add this feature as a widget into every widgetarea you like.",'cc'),
				"login_sidebar",
				array(__('on','cc'), __('off','cc')),
				__('on','cc')),
			new TextOption(
				__("Login sidebar text",'cc'),
				__("Define the text displayed in the login sidebar when you're logged out.",'cc'),
				"bp_login_sidebar_text",
				"")
			)
			),
		new Group (__("Profile",'cc'), "profile",
			array(
			new DropdownOption(
				__("Show Profile header",'cc'),
				__("Display profile header, can be used as widget area",'cc'),
				"bp_profile_header",
				array(__('on','cc'), __('off','cc')),
				__('on','cc')),
			new DropdownOption(
				__("Profile Sidebars",'cc'),
				__("Where do you like to have your sidebars in profiles? <br>
				default = the global settings and sidebars will be used<br>
				none = no sidebars, full width<br>
				left = left profile sidebar, this will overwrite the global settings and display the left profile sidebar<br>
				right = right profile sidebar, this will overwrite the global settings and display the right profile sidebar<br>
				left and right = This option will display the left and right profile sidebars and overwrite the global settings<br>
				Note: all sidebars can be filled with widgets. Without widgets there will be the user avatar and information like in the member header",'cc'),
				"bp_profile_sidebars",
				array(__('default','cc'), __('none','cc'), __('left','cc'), __('right','cc'), __('left and right','cc')),
				__('default','cc')),
			new TextOption(
				__("Profile avatar size",'cc'),
				__("Define the size of the profile avatar. Width and height is the same",'cc'),
				"bp_profiles_avatar_size",
				""),
			new TextOption(
				__("Profile menu order",'cc'),
				__("Change the menu order in the profiles. Write the order in by slug, comma-separated. <br>
				Note: a slug is the name as it is written in the url, <br>
				means all letters in small, no symbols, ...",'cc'),
				"bp_profiles_nav_order",
				"")
			)
			),
		new Group (__("Groups",'cc'), "groups",
			array(
			new DropdownOption(
				__("Show Groups header",'cc'),
				__("Display group header, can be used as widget area",'cc'),
				"bp_groups_header",
				array(__('on','cc'), __('off','cc')),
				__('on','cc')),
			new DropdownOption(
				__("Groups Sidebars",'cc'),
				__("Where do you like to have your sidebars in groups? <br>
				default = the global settings and sidebars will be used<br>
				none = no sidebars, full width<br>
				left = left group sidebar, this will overwrite the global settings and display the left group sidebar<br>
				right = right group sidebar, this will overwrite the global settings and display the right group sidebar<br>
				left and right = this option will display the left and right group sidebars and overwrite the global settings<br>
				Note: all sidebars can be filled with widgets. Without widgets there will be the group avatar and information like in the group header",'cc'),
				"bp_groups_sidebars",
				 array(__('default','cc'), __('none','cc'), __('left','cc'), __('right','cc'), __('left and right','cc')),
				 __('default','cc')),
			new TextOption(
				__("Groups avatar size",'cc'),
				__("Define the size of the group avatar. Width and height is the same <br>
				Just write a number, without px, default is 200.",'cc'),
				"bp_groups_avatar_size",
				""),
			new TextOption(
				__("Groups menu order",'cc'),
				__("Change the menu order in the groups. Write the order in by slug, comma-separated. <br>
				Note: a slug is the name as it is written in the url, <br>
				means all letters in small, no symbols, ...",'cc'),
				"bp_groups_nav_order",
				"")
			)
			),
		new Group (__("Slideshow",'cc'), "slideshow",
			array(
			new DropdownOption(
				__("Enable slideshow",'cc'),
				__("Enable slideshow",'cc'),
				"enable_slideshow_home",
				array(__('home','cc'), __('off','cc'), __('all','cc')),
				__('home','cc')),
			new FileOption(
				__("Set your own default slideshow image",'cc'),
				__("Insert your own default slideshow image. Upload or insert URL (with http://).",'cc'),
				"slideshow_img",
				"",
				"start",
				__('Default slideshow images','cc')),
			new FileOption(
				__("Set your own default slideshow small image",'cc'),
				__("Insert your own default small slideshow image. Upload or insert url (with http://).",'cc'),
				"slideshow_small_img",
				"",
				"end", FALSE),
			new CheckboxGroupOptions(
				__("Slideshow post categories",'cc'),
				__("The slideshow takes images, titles and text-excerpts of the last 4 posts.<br>
				You can select the category the posts should be taken from. <br>
				For more info check out the Knowledge Base topic about the <a href='http://support.themekraft.com/entries/21647926-slideshow' target='_blank'>Slideshow</a> and <a href='http://support.themekraft.com/entries/21621621-featured-image' target='_blank'>featured image</a>.",'cc'),
				"slideshow_cat",
				$option_categories),
			new TextOption(
				__("Amount",'cc'),
				__("Define the amount of posts. This option works just with the full width image slider.",'cc'),
				"slideshow_amount",
				""),
			new TextOption(
				__("Post type",'cc'),
				__("Define the post type to display instead of posts. For pages write 'page', <br>
				for a custom post type the name of the cutsom post type, e.g. 'radio'", 'cc'),
				"slideshow_post_type",
				""),
			new TextOption(
				__("Page IDs",'cc'),
				__("Page IDs, comma separated. Just working if you use post types instead of categories", 'cc'),
				"slideshow_show_page",
				""),
			new TextOption(
				__("Sliding time",'cc'),
				__("Define the sliding time in ms",'cc'),
				"slideshow_time", ""),
			new TextOption(
				__("Order posts by",'cc'),
				__("* orderby=author<br>
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
				* orderby=comment_count - (available with Version 2.9) <br>",'cc'),
				"slideshow_orderby",
				""),
			new DropdownOption(
				__("Slideshow style",'cc'),
				__("Select a type of slideshow.",'cc'),
				"slideshow_style",
				array(__('default','cc'),__('full width','cc'))),
			new DropdownOption(
				__("Caption",'cc'),
				__("Show just the images or also titles and excerpts?",'cc'),
				"slideshow_caption",
				array(__('on','cc'), __('off','cc'))),
			new DropdownOption(
				__("Shadow",'cc'),
				__("Select if you'd like to have a shadow under the top slideshow.<br>
				Note: just for bright background and static width between 990 and about 1100 pixels.",'cc'),
				"slideshow_shadow",
				array(__('sharper shadow','cc'), __('shadow','cc'), __('no shadow','cc')),
				__('sharper shadow','cc')),
	        new DropdownOption(
	            __('Allow direct post access', 'cc'),
	            __('When small thumbnails are displayed to the right of a slideshow currently.
	                When clicking on them you will be redirected to page of the post of this thumbnail.','cc'), // Ole K Hornnes, added 'cc'.
	            'slideshow_direct_links',
	            array(__('no', 'cc'), __('yes', 'cc')),
	            __('no', 'cc')
	        )
		  )),
		new Group (__("CSS",'cc'), "overwrite",
			array(
			new TextOption(
				__("Overwrite CSS",'cc'),
				__("This is your place to overwrite existing CSS.<br>
				This way you are able to customize even the smallest CSS details. <br>
				If you know how to write, you can play around a bit!<br>
				<br>
				Here's an example how to change your body background picture:<br>
				<br>
				body {<br>
				background-image:url(url-to-your-picture);<br>
				}<br>
				<br>",'cc'),
				"overwrite_css",
				"",
				true,
				false),
			)
			),
		new Group (__('Roles', CC_TRANSLATE_DOMAIN),
            'roles_and_capabilities',
            array(
                new DropdownOption(
                    __('Min role for General settings',CC_TRANSLATE_DOMAIN ),
                    __('This option set min role for General settings', CC_TRANSLATE_DOMAIN),
                    'general_min_role',
                    cc_get_user_roles()
                ),
                new DropdownOption(
                    __('Min role for Header settings',CC_TRANSLATE_DOMAIN ),
                    __('This option set min role for Header settings', CC_TRANSLATE_DOMAIN),
                    'header_min_role',
                    cc_get_user_roles()
                ),
                new DropdownOption(
                    __('Min role for Menu settings',CC_TRANSLATE_DOMAIN ),
                    __('This option set min role for Menu settings', CC_TRANSLATE_DOMAIN),
                    'menu_min_role',
                    cc_get_user_roles()
                ),
                new DropdownOption(
                    __('Min role for Sidebar settings',CC_TRANSLATE_DOMAIN ),
                    __('This option set min role for Sidebar settings', CC_TRANSLATE_DOMAIN),
                    'sidebars_min_role',
                    cc_get_user_roles()
                ),
                new DropdownOption(
                    __('Min role for Footer settings',CC_TRANSLATE_DOMAIN ),
                    __('This option set min role for Footer settings', CC_TRANSLATE_DOMAIN),
                    'footer_min_role',
                    cc_get_user_roles()
                ),
                new DropdownOption(
                    __('Min role for BuddyPress settings',CC_TRANSLATE_DOMAIN ),
                    __('This option set min role for BuddyPress settings', CC_TRANSLATE_DOMAIN),
                    'buddypress_min_role',
                    cc_get_user_roles()
                ),
                new DropdownOption(
                    __('Min role for Profile settings',CC_TRANSLATE_DOMAIN ),
                    __('This option set min role for Profile settings', CC_TRANSLATE_DOMAIN),
                    'profile_min_role',
                    cc_get_user_roles()
                ),
                new DropdownOption(
                    __('Min role for Group settings',CC_TRANSLATE_DOMAIN ),
                    __('This option set min role for Group settings', CC_TRANSLATE_DOMAIN),
                    'groups_min_role',
                    cc_get_user_roles()
                ),
                new DropdownOption(
                    __('Min role for Slideshow settings',CC_TRANSLATE_DOMAIN ),
                    __('This option set min role for Slideshow settings', CC_TRANSLATE_DOMAIN),
                    'slideshow_min_role',
                    cc_get_user_roles()
                ),
                new DropdownOption(
                    __('Min role for CSS settings', CC_TRANSLATE_DOMAIN ),
                    __('This option set min role for CSS Overwrite settings', CC_TRANSLATE_DOMAIN),
                    'overwrite_min_role',
                    cc_get_user_roles()
                ),
                new DropdownOption(
                    __('Min role for Roles and Capabilities settings',CC_TRANSLATE_DOMAIN ),
                    __('This option set min role for Roles and Capabilities settings', CC_TRANSLATE_DOMAIN),
                    'roles_and_capabilities_min_role',
                    cc_get_user_roles()
                ),

            )),
    );

	return apply_filters('cc_admin_options', $options);
}
?>
