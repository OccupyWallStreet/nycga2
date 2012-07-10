<?php

/**
 * 
 *
 *  Options Array
 *
 *
 *  @package PageLines Options
 *  @subpackage Options
 *  @since 2.0.b3
 *
 */

class PageLinesOptionsArray {

	/**
	 * Construct
	 */
	function __construct() {
		

		$this->options['website_setup'] 		= $this->website_setup();
		$this->options['layout_editor'] 		= $this->layout_editor();
		$this->options['color_control'] 		= $this->color_control();
		$this->options['typography'] 			= $this->typography();
		$this->options['header_and_footer'] 	= $this->header_footer();
		$this->options['blog_and_posts'] 		= $this->blog_posts();
		$this->last_options['advanced'] 		= $this->advanced();
		$this->last_options['custom_code'] 		= $this->custom_code();
	}


	/**
	*
	* @TODO document
	*
	*/
	function website_setup(){
		$a = array(
			'icon'			=> PL_ADMIN_ICONS.'/compass.png',
			'account_signup'	=> array(
				'default'		=> '',
				'type' 			=> 'account_signup',
				'inputlabel' 	=> __( 'Account Login', 'pagelines' ),
				'title'			=> __( 'PageLines Account Sign In', 'pagelines' ),						
				'shortexp' 		=> __( 'Login or Register for a Free PageLines Account', 'pagelines' ),
				'exp' 			=> __( 'Logging in with your PageLines account will allow you to purchase items from the PageLines Store, and more.', 'pagelines' )
			),
			'pagelines_custom_logo' => array(
				'default' 		=> PL_IMAGES.'/logo.png',
				'type' 			=> 'image_upload',
				'imagepreview' 	=> '270',
				'inputlabel' 	=> __( 'Upload custom logo', 'pagelines' ),
				'title'			=> __( 'Custom Header Image', 'pagelines' ),						
				'shortexp' 		=> __( 'Input full URL to your custom header or logo image', 'pagelines' ),
				'exp' 			=> __( 'Optional way to replace <strong>heading</strong> and <strong>description</strong> text for your website ' . 
						    		'with an image.', 'pagelines' )
			),
			'pagelines_favicon'		=> array(
				'version' 		=> 'pro',
				'default' 		=> 	PL_ADMIN_IMAGES . "/favicon-pagelines.ico",
				'inputlabel'	=> 'Upload Favicon (16px by 16px)',
				'type' 			=> 	'image_upload',
				'imagepreview' 	=> 	'16',
				'title' 		=> 	__( 'Favicon Image', 'pagelines' ),						
				'shortexp' 		=> 	__( 'Input full URL to favicon image (<strong>favicon.ico</strong> image file)', 'pagelines' ),
				'exp' 			=> 	__( 'Enter the full URL location of your custom <strong>favicon</strong> which is visible in browser favorites and tabs.<br/> <strong>Must be .png or .ico file - 16px by 16px</strong>.', 'pagelines' )
			),		
			'twittername' => array(
				'default' 		=> '',
				'type' 			=> 'text',
				'inputlabel' 	=> __( 'Your Twitter Username', 'pagelines' ),
				'title' 		=> __( 'Twitter Integration', 'pagelines' ),
				'shortexp'	 	=> __( 'Places your Twitter feed in your site', 'pagelines' ),
				'exp' 			=> __( 'This places your Twitter feed on the site. Leave blank if you want to hide or not use.', 'pagelines' )
			),
			'site-hashtag' => array(
				'default' 		=> '',
				'type' 			=> 'text',
				'inputlabel' 	=> __( 'Your Website Hashtag', 'pagelines' ),
				'title' 		=> __( 'Website Hashtag', 'pagelines' ),
				'shortexp'	 	=> __( 'This hashtag will be used in social media (e.g. Twitter) and elsewhere to create feeds.', 'pagelines' ),
				'exp' 			=> __( 'Having a hashtag can be useful in creating a common thread or feed in your social media efforts.', 'pagelines' )
			),
			
			'pl_login_image'	=> array(
				'version' 		=> 'pro',
				'default' 		=> PL_ADMIN_IMAGES . "/login-pl.png",
				'type' 			=> 	'image_upload',
				'inputlabel'	=> 'Upload Icon (80px Height)',
				'imagepreview' 	=> 	'60',
				'title' 		=> __( 'Login Page Image', 'pagelines' ),						
				'shortexp' 		=> __( "The image to use on your site's login page", 'pagelines' ),
				'exp'			=> __( 'This image will be used on the login page to your admin. Use an image that is approximately <strong>80px</strong> in height.', 'pagelines' )
			),
			'pagelines_touchicon'	=> array(
				'version' 		=> 'pro',
				'default' 		=> '',
				'inputlabel'	=> 'Upload Icon (57px by 57px)',
				'type' 			=> 	'image_upload',
				'imagepreview' 	=> 	'60',
				'title' 		=> __( 'Mobile Touch Image', 'pagelines' ),						
				'shortexp' 		=> __( 'Input full URL to Apple touch image (.jpg, .gif, .png)', 'pagelines' ),
				'exp'			=> __( 'Enter the full URL location of your Apple Touch Icon which is visible when your users set your site as a <strong>webclip</strong> in Apple Iphone and Touch Products. It is an image approximately 57px by 57px in either .jpg, .gif or .png format.', 'pagelines' )
			),
			'pl_watermark'	=> array(
				'version' 		=> 'pro',
				'type' 			=> 	'multi_option',
				'selectvalues'	=> array(
					'watermark_image'	=> array('type' =>'image_upload', 'inputlabel' => 'Watermark Image', 'default' => PL_IMAGES.'/pagelines.png'), 
					'watermark_link'	=> array('type' => 'text', 'inputlabel' => 'Watermark Link (Blank for None)', 'default' => 'http://www.pagelines.com'),
					'watermark_alt'		=> array('type' => 'text', 'inputlabel' => 'Watermark Link alt text', 'default' => 'Build a website with PageLines' ),
					'watermark_hide'	=> array('type' => 'check', 'inputlabel' => "Hide Watermark")
				),
				'title' 		=> __( 'Website Watermark', 'pagelines' ),						
				'shortexp' 		=> __( 'Configure your website watermark (in footer)', 'pagelines' ),
				'exp'			=> ''
			),
			'pagetitles' => array(
					'default'	=> true,
					'type'		=> 'check',
					'inputlabel'=> __( 'Automatically show Page titles?', 'pagelines' ),
					'title'		=> __( 'Page Titles', 'pagelines' ),						
					'shortexp'	=> __( 'Show the title of pages above the page content', 'pagelines' ),
					'exp'		=> __( 'This option will automatically place page titles on all pages.', 'pagelines' )
			),
			'sidebar_no_default' => array(
					'default'	=> '',
					'type'		=> 'check',
					'inputlabel'	=> __( 'Hide Sidebars When Empty (no widgets)', 'pagelines' ),
					'title'		=> __( 'Remove Default Sidebars When Empty', 'pagelines' ),
					'shortexp'	=> __( 'Hide default sidebars when sidebars have no widgets in them', 'pagelines' ),
					'exp'		=> __( 'This allows you to remove sidebars completely when they have no widgets in them.', 'pagelines' )
			),
			'sidebar_wrap_widgets' => array(
					'default' 	=> 'top',
					'version'	=> 'pro',
					'type' 		=> 'select',
					'selectvalues'	=> array(
						'top'		=> array('name' => __( 'On Top of Sidebar', 'pagelines') ),
						'bottom'	=> array('name' => __( 'On Bottom of Sidebar', 'pagelines') )
					),
					'inputlabel' 	=> __( 'Sidebar Wrap Widgets Position', 'pagelines' ),
					'title' 	=> __( 'Sidebar Wrap Widgets', 'pagelines' ),
					'shortexp' 	=> __( 'Choose whether to show the sidebar wrap widgets on the top or bottom of the sidebar', 'pagelines' ),
					'exp' 		=> __( 'You can select whether to show the widgets that you place in the sidebar wrap template in either the top or the bottom of the sidebar.', 'pagelines' )
			),
		
		);
		
		if ( get_option( 'pagelines_email_sent') ) 
			unset($a['email_capture']);
			
		if ( pagelines_check_credentials() ) 
			unset($a['account_signup']);
		
		return apply_filters('pagelines_options_website_setup', $a);
	}
	
	/**
	 * Layout Editor Interface & Options
	 *
	 * @since 2.0.0
	 */
	function layout_editor(){

		$a = array(
			'icon'			=> PL_ADMIN_ICONS.'/layout.png',
			'layout_handling' => array(
				'default'		=> 'pixels',
				'type'			=> 'graphic_selector',
				'inputlabel'	=> __( 'How should layout be handled?', 'pagelines' ),
				'showname'		=> true,
				'sprite'		=> PL_ADMIN_IMAGES.'/sprite-layout-modes.png',
				'height'		=> '88px', 
				'width'			=> '130px',
				'layout'		=> 'interface',
				'selectvalues'	=> array(
					'pixels'		=> array( 'name' => __( 'Responsive with Pixel Width', 'pagelines' ), 'offset' => '0px 0px' ), 
					'percent'		=> array( 'name' => __( 'Responsive with Percent Width', 'pagelines' ), 'offset' => '0px -88px', 'version'	=> 'pro' ), 
					'static'		=> array( 'name' => __( 'Static with Pixel Width', 'pagelines' ), 'offset' => '0px -176px' )
				),
				'title'		=> __( 'Layout Handling', 'pagelines' ),						
				'shortexp'	=> __( 'Select between responsive vs. static; pixel based or percentage based layout', 'pagelines' ),
				'exp'		=> __( "Responsive layout adjusts to the size of your user's browser window; static is fixed width. Use this option to switch between the pixel based site width and a percentage based one.", 'pagelines' )
			),
			'site_design_mode'	=> array(
				
				'default'	=> 'full_width',
				'type'		=> 'graphic_selector',
				'showname'	=> true,
				'sprite'		=> PL_ADMIN_IMAGES.'/sprite-design-modes.png',
				'height'		=> '88px', 
				'width'			=> '130px',
				'layout' 		=> 'interface',	
				'selectvalues'	=> array(
					'full_width'	=> array('name' => __( "Full-Width Sections", 'pagelines' ), 'offset' => '0px 0px'),
					'fixed_width'	=> array('name' => __( "Fixed-Width Mode", 'pagelines' ), 'version' => 'pro', 'offset' => '0px -88px', 'version'	=> 'pro')
				), 
				'inputlabel'	=> __( 'Site Design Mode', 'pagelines' ),
				'title'		=> __( 'Site Design Mode', 'pagelines' ),						
				'shortexp'	=> __( 'The basic HTML layout structure for color and background effects', 'pagelines' ),
				'exp'		=> __( 'This option controls how the basic HTML layout is built. Different layout structures change the way background colors and images behave.<ul><li><strong>Full-Width Mode</strong> Full width design mode allows you to have aspects of your site that are the full-width of your screen; while others are the width of the content area.</li><li><strong>Fixed-Width Mode</strong> Fixed width design mode creates a fixed width <strong>page</strong> that can be used as the area for your design.  You can set a background to the page; and the content will have a seperate <strong>fixed-width</strong> background area (i.e. the width of the content).</li></ul>', 'pagelines' ),
			),	
			'disable_mobile_view' => array(
				'default' 	=> false,
				'type' 		=> 'check',
				'title' 	=> __( 'Disable Mobile Optimized View', 'pagelines' ),
				'inputlabel'	=> __( 'Disable Mobile View', 'pagelines' ),				
				'shortexp' 	=> __( 'Forces mobile devices to see the full site', 'pagelines' ),
				'exp' 		=> __( 'By default PageLines accommodates mobile devices resolution and shows a mobile optimized view. Check this option to make it so users see your full site.', 'pagelines' ),
			),
			'layout_default' => array(
				'type' 			=> 'graphic_selector',
				'layout' 		=> 'interface',
				'default'		=> 'one-sidebar-right',	
				'sprite'		=> PL_ADMIN_IMAGES.'/sprite-layouts.png', 
				'height'		=> '50px', 
				'width'			=> '50px', 
				'selectvalues'	=> array(
					'fullwidth'				=> array( 'name' => __( 'Fullwidth layout', 'pagelines' ), 'offset' => '0px 0px'),
					'one-sidebar-right' 	=> array( 'name' => __( 'One sidebar on right', 'pagelines' ), 'offset' => '0px -50px'),
					'one-sidebar-left'		=> array( 'name' => __( 'One sidebar on left', 'pagelines' ), 'offset' => '0px -100px'),
					'two-sidebar-right' 	=> array( 'name' => __( 'Two sidebars on right', 'pagelines' ), 'version' => 'pro', 'offset' => '0px -150px' ),
					'two-sidebar-left' 		=> array( 'name' => __( 'Two sidebars on left', 'pagelines' ), 'version' => 'pro', 'offset' => '0px -200px' ),
					'two-sidebar-center' 	=> array( 'name' => __( 'Two sidebars, one on each side', 'pagelines' ), 'version' => 'pro', 'offset' => '0px -250px' ),
				),
				'title' 		=> __( 'Default Layout Mode', 'pagelines' ),
				'inputlabel'	=> __( 'Select Default Layout', 'pagelines' ),				
				'shortexp' 		=> __( 'Select your default layout mode, this can be changed on individual pages.<br />Once selected, you can adjust the layout in the Layout Dimension Editor', 'pagelines' ),
				'exp' 			=> __( 'The default layout for pages and posts on your site. Dimensions can be changed using the Layout Dimension Editor.', 'pagelines' ),
				'docslink'		=> 'http://www.pagelines.com/wiki/index.php?title=How_to_Use_the_Layout_Editor_Settings'
			),
			'layout' => array(
				'default'	=> 'one-sidebar-right',
				'type'		=> 'layout',
				'layout'	=> 'interface',
				'title'		=> __( 'Layout Dimension Editor', 'pagelines' ),						
				'shortexp'	=> __( 'Configure the default layout sizes for your site, which is initially selected above in the Default Layout Mode. <br/>This option allows you to adjust columns and margins for the default layout', 'pagelines' ),
			), 
			
			'resetlayout' => array(
				'default'	=> '',
				'inputlabel'	=> __("Reset Layout", 'pagelines'),
				'type' 		=> 'reset',
				'callback'	=> 'reset_layout_to_default',
				'title' 	=> __( 'Reset Layout To Default', 'pagelines' ),	
				'layout'	=> 'full',					
				'shortexp'	=> __( 'Changes layout mode and dimensions back to default', 'pagelines' ),
			)
		);
		
		return apply_filters('pagelines_options_layout_editor', $a);
		
	}
	
	/**
	 * Design Control and Color Options
	 *
	 * @since 2.0.0
	 */
	function color_control(){

		$a = array(	
			'icon'			=> PL_ADMIN_ICONS.'/color.png',
			'page_colors'		=> array(
				'title' 	=> __( 'Basic Layout Colors', 'pagelines' ),						
				'shortexp' 	=> __( 'The main layout colors for your site', 'pagelines' ),
				'exp' 		=> __( 'Use these options to quickly setup the main layout colors for your site.  You can use these options to build custom sites very quickly, or to quickly prototype a design then refine through custom CSS.<br/><br/><strong>Notes:</strong> <ol><li>To make the background transparent, you can leave the options blank (delete text).</li>  <li>Further customize and refine colors through custom CSS or plugins</li></ol>', 'pagelines' ),
				'type' 		=> 'color_multi',
				'layout'	=> 'full',
				'selectvalues'	=> array(
					'bodybg'	=> array(				
						'default' 		=> '#FFFFFF',
						'css_prop'		=> 'background-color',
						'flag'			=> 'set_default',
						'cssgroup'		=> 'bodybg',
						'inputlabel' 	=> __( 'Body Background', 'pagelines' ),
						
					),
					'pagebg'		=> array(				
						'default' 		=> '',
						'cssgroup'		=>	'pagebg',
						'flag'			=> 'blank_default',
						'css_prop'		=> 'background-color',
						'inputlabel' 	=> __( 'Page Background (Optional)', 'pagelines' ),
						),
					'contentbg'	=> array(		
						'default' 		=> '',
						'cssgroup'		=>	'contentbg',
						'flag'			=> 'blank_default',
						'css_prop'		=> 'background-color',
						'id'			=> 'the_bg',
						'inputlabel' 	=> __( 'Content Background (Optional)', 'pagelines' ),
						'math'		=> array(
								array( 
									'id'		=> 'cascade', // use this for getting stored background color
									'mode' 		=> 'contrast', 
									'cssgroup' 	=> 'cascade', 
									'css_prop' 	=> 'background-color', 
									'diff' 		=> '1%', 
									'depends' 	=> pl_background_cascade()
								),
								array( 
									'id'		=> 'bg', // use this for getting stored background color
									'mode' 		=> 'contrast', 
									'cssgroup' 	=> 'border_layout', 
									'css_prop' 	=> 'border-color', 
									'diff' 		=> '8%', 
									'depends' 	=> pl_background_cascade()
								),
								array(
									'mode' 		=> 'darker', 
									'cssgroup' 	=> 'border_layout_darker', 
									'css_prop' 	=> 'border-color', 
									'depends' 	=> pl_background_cascade()
								), 
								array(
									'mode' 		=> 'lighter', 
									'cssgroup' 	=> 'border_layout_lighter', 
									'css_prop' 	=> 'border-color', 
									'depends' 	=> pl_background_cascade()
								),
								
								array( 
									'id'		=> 'box_bg',
									'mode' 		=> 'contrast', 
									'cssgroup' 	=> 'box_color_primary', 
									'css_prop' 	=> 'background-color', 
									'diff' 		=> '5%', 
									'depends' 	=> pl_background_cascade(),
									'math'		=> array(
										array( 'id' => 'text_box', 'mode' => 'contrast', 'cssgroup' => 'text_box', 'css_prop' => 'color', 'diff' => '65%', 'math' => array(
											array( 'mode' => 'shadow', 'mixwith' => pl_background_cascade(), 'cssgroup' => array('text_box') ),
										)),
										array( 'id' => 'primary_border', 'mode' => 'contrast', 'cssgroup' => 'border_primary', 'css_prop' => 'border-color', 'diff' => '8%', 'math' => array(
											array( 'mode' => 'darker', 'cssgroup' => 'border_primary_shadow', 'css_prop' => array('border-left-color', 'border-top-color'), 'diff' => '10%'),
											array( 'mode' => 'lighter', 'cssgroup' => 'border_primary_highlight', 'css_prop' => array('border-left-color', 'border-top-color'), 'diff' => '15%'),
										)),
										array( 'mode' => 'darker', 'cssgroup' => 'border_primary_darker', 'css_prop' => 'border-color', 'diff' => '10%' ),
										array( 'mode' => 'lighter', 'cssgroup' => 'border_primary_lighter', 'css_prop' => 'border-color', 'diff' => '10%' ),
										array( 'id' => 'box_bg_secondary', 'mode' => 'contrast', 'cssgroup' => 'box_color_secondary', 'css_prop' => array('background-color'), 'diff' => '3%', 'math' => array(
											array( 'id' => 'text_box_second', 'mode' => 'contrast', 'cssgroup' => 'text_box_secondary', 'css_prop' => array('color'), 'diff' => '65%'),
											array( 'mode' => 'darker', 'cssgroup' => 'border_secondary', 'css_prop' => array('border-color'), 'diff' => '5%'),
											array( 'mode' => 'darker', 'cssgroup' => 'border_secondary', 'css_prop' => array('border-left-color', 'border-top-color'), 'diff' => '15%'),
											
										)),
										array( 'id' => 'box_bg_tertiary', 'mode' => 'contrast', 'cssgroup' => 'box_color_tertiary', 'css_prop' => array('background-color'), 'diff' => '6%','math' => array(
											array( 'mode' => 'darker', 'cssgroup' => 'border_tertiary', 'css_prop' => array('border-color'), 'diff' => '10%'),
											array( 'mode' => 'darker', 'cssgroup' => 'border_tertiary', 'css_prop' => array('border-left-color', 'border-top-color'), 'diff' => '15%'),
										)), 
										
									)
									
								),
								
								array( 'mode' => 'lighter', 'cssgroup' => 'box_color_lighter', 'css_prop' => 'background-color'),
							)
						),
				),
			),
			'text_colors'		=> array(
				'title' 		=> __( 'Page Text Colors', 'pagelines' ),						
				'shortexp' 		=> __( 'Controls the color of text used throughout your site', 'pagelines' ),
				'exp' 			=> __( 'These options control the colors of the text throughout the page or content area of your site.<br/><br/>Certain text types are designed to contrast with different box elements and are meant to be used with hover effects.<br/><br/>Experiment to find exactly how colors are combined with text on your site.', 'pagelines' ),
				'type' 			=> 'color_multi',
				'layout'		=> 'full',
				'selectvalues'	=> array(
					'text_primary' => array(		
						'id'			=> 'text_primary',
						'default' 		=> '#000000',
						'flag'			=> 'set_default',
						'cssgroup'		=>	'text_primary',
						'inputlabel' 	=> __( 'Primary Text', 'pagelines' ),
						'math'		=> array(
							array( 'mode' => 'mix', 'mixwith' => pl_background_cascade(), 'cssgroup' => 'text_secondary', 'css_prop' => 'color', 'diff' => '65%'),
							array( 'mode' => 'shadow', 'mixwith' => pl_background_cascade(), 'cssgroup' => array('text_primary', 'text_secondary', 'text_tertiary') ),
						)
					),
					'headercolor'	=> array(		
						'default' 	=> '#000000',
						'cssgroup'	=> 'headercolor',
						'flag'			=> 'set_default',
						'inputlabel' 	=> __( 'Text Headers', 'pagelines' ),
						'math'		=> array(
							array( 'mode' => 'shadow', 'mixwith' => pl_background_cascade(), 'cssgroup' => 'headercolor'),
						)
					),
					
					'linkcolor' => array(
						'default'		=> '#225E9B',
						'cssgroup'		=>	'linkcolor',
						'flag'			=> 'set_default',
						'inputlabel' 	=> __( 'Primary Links', 'pagelines' ),	
						'math'			=> array(
							array( 'mode' => 'mix', 'mixwith' => pl_background_cascade(),  'cssgroup' => 'linkcolor_hover', 'css_prop' => 'color', 'diff' => '80%'),	
							array( 'mode' => 'shadow', 'mixwith' => pl_background_cascade(), 'cssgroup' => 'linkcolor'),
						)				
					),
					'footer_text' => array(
						'default'		=> '#AAAAAA',
						'cssgroup'		=>	'footer_highlight',
						'flag'			=> 'set_default',
						'inputlabel' 	=> __( 'Footer Text', 'pagelines' ),	
						'math'			=> array(
							array( 'mode' => 'mix', 'mixwith' => pl_body_bg(),  'cssgroup' => 'footer_text', 'css_prop' => 'color', 'diff' => '66%'),
							array( 'mode' => 'shadow', 'mixwith' => pl_body_bg(), 'cssgroup' => array('footer_text', 'footer_highlight') ),
						)					
					),
				),
			),
			'canvas_shadow' => array(
				'title' 		=> __( 'Enable Content Shadow (Fixed Width Mode Only)', 'pagelines' ),						
				'shortexp' 		=> __( 'Adds a shadow on the fixed width content area for a little style', 'pagelines' ),
				'exp' 			=> __( 'Check this option to enable a drop shadow on the canvas area when using fixed width mode.', 'pagelines' ),
				'type' 			=> 'check',
				'default'		=> false,
				'inputlabel'	=> 'Content Shadow', 
				'version'		=> 'pro'
			),
			'page_background_image' => array(
				'title' 	=> __( 'Site Background Image (Optional)', 'pagelines' ),						
				'shortexp' 	=> __( 'Setup a background image for the background of your site', 'pagelines' ),
				'exp' 		=> __( 'Use this option to apply a background image to your site. This option will be applied to different areas depending on the design mode you have set.<br/><br/><strong>Positioning</strong> Use percentages to position the images, 0% corresponds to the <strong>top</strong> or <strong>left</strong> side, 50% to center, etc..', 'pagelines' ),
				'type' 		=> 'background_image',
				'selectors'	=> cssgroup('page_background_image')
			),
			'supersize_bg' => array(
				'title' 		=> __( '<strong>Supersize</strong> The Background Image (Fixed Width Mode Required)', 'pagelines' ),						
				'shortexp' 		=> __( 'Uses a script to set the background for full width and responsive design', 'pagelines' ),
				'exp' 			=> __( 'Sets the background to match the width of the browser.', 'pagelines' ),
				'type' 			=> 'check',
				'default'		=> true,
				'inputlabel'	=> 'Supersize The Background Image'
			),
			


		);
		
		return apply_filters('pagelines_options_color_control', $a);
		
	}
	
	/**
	 * Typography Options
	 *
	 * @since 2.0.0
	 */
	function typography(){

		$a = array(
			'icon'			=> PL_ADMIN_ICONS.'/typography.png',
			'type_headers' => array(
					'default' 	=> array( 'font' => 'helvetica', 'weight' => 'bold' ),
					'type' 		=> 'typography',
					'layout'	=> 'full',
					'selectors'	=> cssgroup('type_headers'),
					'inputlabel' 	=> 'Select Font',
					'title' 	=> __( 'Typography - Text Headers', 'pagelines' ),
					'shortexp' 	=> __( "Select and style your site's header tags (H1, H2, H3...)", 'pagelines' ),
					'exp' 		=> __( "Set typography for your h1, h2, etc.. tags. <br/><br/><strong>*</strong> Denotes Web Safe Fonts<br/><strong>G</strong> Denotes Google Fonts<br/><br/><strong>Note:</strong> These options make use of the <a href='http://code.google.com/webfonts' target='_blank'>Google fonts API</a> to vastly increase the number of websafe fonts you can use.", 'pagelines' ),
					'pro_note'	=> __( 'The Pro version of this framework has over 50 websafe and Google fonts.', 'pagelines' )
			),

			'type_primary' => array(
					'default' 	=> array( 'font' => 'helvetica' ),
					'type'		=> 'typography',
					'layout'	=> 'full',
					'selectors'	=> cssgroup('type_primary'),
					'inputlabel'=> __( 'Select Font', 'pagelines' ),
					'title' 	=> __( 'Typography - Primary Font', 'pagelines' ),
					'shortexp' 	=> __( 'Select and style the standard type used in your site (body)', 'pagelines' ),
 					'exp' 		=> __( "Set typography for your primary site text. This is assigned to your site's body tag. <br/><br/> <strong>*</strong> Denotes Web Safe Fonts<br/><strong>G</strong> Denotes Google Fonts", 'pagelines' ),
					'pro_note'	=> __( 'The Pro version of this framework has over 50 websafe and Google fonts.', 'pagelines' )
			),


			'type_secondary' => array(
					'default' 	=> array( 'font' => 'helvetica' ),
					'type' 		=> 'typography',
					'layout'	=> 'full',
					'selectors'	=> cssgroup('type_secondary'),
					'inputlabel' 	=> __( 'Select Font', 'pagelines' ),
					'title' 	=> __( 'Typography - Secondary Font ', 'pagelines' ),
 					'shortexp' 	=> __( "Select and style your site's secondary or sub title text (Metabar, Sub Titles, etc..)", 'pagelines' ),
					'exp' 		=> __( 'This options sets the typography for secondary text used throughout your site. This includes your navigation, subtitles, widget titles, etc.. <br/><br/> <strong>*</strong> Denotes Web Safe Fonts<br/><strong>G</strong> Denotes Google Fonts', 'pagelines' ),
					'pro_note'	=> __( 'The Pro version of this framework has over 50 websafe and Google fonts.', 'pagelines' )
			),

			'type_inputs' => array(
					'version' 	=> 'pro',
					'default' 	=> array( 'font' => 'helvetica' ),
					'type' 		=> 'typography',
					'layout'	=> 'full',
					'selectors'	=> cssgroup('type_inputs'),
					'inputlabel' 	=> __( 'Select Font', 'pagelines' ),
					'title' 	=> __( 'Typography - Inputs and Textareas', 'pagelines' ),
					'shortexp' 	=> __( "Select and Style Your Site's Text Inputs and Textareas", 'pagelines' ),
					'exp' 		=> __( "This options sets the typography for general text inputs and textarea inputs. This includes default WordPress comment fields, etc.. <br/><br/> This option makes use of the <a href='http://code.google.com/webfonts'>Google fonts API</a> to vastly increase the number of websafe fonts you can use.<br/><strong>*</strong> Denotes web safe fonts<br/><strong>G</strong> Denotes Google fonts<br/><br/><strong>Note:</strong> the <strong>preview</strong> pane represents the font in your current browser and OS. If developing locally, Google fonts require an internet connection.", 'pagelines' ),
			),

			'typekit_script' => array(
					'default'	=> "",
					'type'		=> 'textarea',
					'inputlabel'	=> __( 'Typekit Header Script', 'pagelines' ),
					'title'		=> __( 'Typekit Font Replacement', 'pagelines' ),
					'shortexp'	=> __( 'Typekit is a service that allows you to use tons of new fonts on your site', 'pagelines' ),
					'exp'		=> __( "Typekit is a new service and technique that allows you to use fonts outside of the 10 or so <strong>web-safe</strong> fonts. <br/><br/>Visit <a href='http://www.typekit.com' target='_blank'>Typekit.com</a> to get the script for this option. Instructions for setting up Typekit are <a href='http://typekit.assistly.com/portal/article/6780-Adding-fonts-to-your-site' target='_blank'>here</a>.", 'pagelines')
			),
			'fontreplacement' => array(
					'version'	=> 'pro',
					'default'	=> false,
					'type'		=> 'check',
					'inputlabel'=> __( 'Use Cufon font replacement?', 'pagelines' ),
					'title'		=> __( 'Use Cufon Font Replacement', 'pagelines' ),
					'shortexp'	=> __( 'Use a special font replacement technique for certain text', 'pagelines' ),
					'exp'		=> sprintf( __( "Cufon is a special technique for allowing you to use fonts outside of the 10 or so <strong>web-safe</strong> fonts. <br/><br/>%s is equipped to use it.  Select this option to enable it. Visit the <a href='http://cufon.shoqolate.com/generate/'>Cufon site</a>.", 'pagelines' ), NICETHEMENAME )
			),
			'font_file'	=> array(
					'version'	=> 'pro',
					'default'	=> '',
					'type'		=> 'text',
					'inputlabel'	=> __( 'Cufon replacement font file URL', 'pagelines' ),
					'title'		=> __( 'Cufon: Replacement Font File URL', 'pagelines' ),
					'shortexp'	=> __( 'The font file used to replace text', 'pagelines' ),
					'exp'		=> __( "Use the <a href='http://cufon.shoqolate.com/generate/'>Cufon site</a> to generate a font file for use with this theme.  Place it in your theme folder and add the full URL to it here. The default font is Museo Sans.", 'pagelines' )
			),
			'replace_font' => array(
					'version'	=> 'pro',
					'default'	=> 'h1',
					'type'		=> 'text',
					'inputlabel'=> __( 'CSS elements for font replacement', 'pagelines' ),
					'title'		=> __( 'Cufon: CSS elements for font replacement', 'pagelines' ),
					'shortexp'	=> __( 'Add selectors of elements you would like replaced', 'pagelines' ),
					'exp'		=> __( 'Use standard CSS selectors to replace them with your Cufon font. Font replacement must be enabled.', 'pagelines' )
			),
		);
		
		return apply_filters('pagelines_options_typography', $a);
		
	}
	
	/**
	 * Header and Footer Options
	 *
	 * @since 2.0.0
	 */
	function header_footer(){

		$a = array(
			'icon'			=> PL_ADMIN_ICONS.'/header.png',
			'drop_down_options' => array(
				'default' => '',
				'type' => 'check_multi',
				'selectvalues'=> array(
		
					'enable_drop_down'	=> array(
						'default'		=> false,
						'type'			=> 'check',
						'scope'			=> '',
						'inputlabel'	=> __( 'Enable Drop Down Navigation?', 'pagelines' ),
						'title'			=> __( 'Drop Down Navigation', 'pagelines' ),
						'shortexp'		=> __( 'Enable universal drop down navigation', 'pagelines' ),
						'exp'			=> __( 'Checking this option will create drop down menus for all child pages when users hover over main navigation items.', 'pagelines' )
						),
					'drop_down_shadow'	=> array(
						'default'		=> true,
						'type'			=> 'check',
						'scope'			=> '',
						'inputlabel'	=> __( 'Enable Shadow on Drop Down Menu?', 'pagelines' ),
						'title'			=> __( 'Drop Down Shadow', 'pagelines' ),
						'shortexp'		=> __( 'Enable shadow for drop down navigation', 'pagelines' ),
						'exp'			=> __( 'Checking this option will create shadows for the drop down menus', 'pagelines' )
						),
					'drop_down_arrows'	=> array(
						'default'		=> true,
						'type'			=> 'check',
						'scope'			=> '',
						'inputlabel'	=> __( 'Enable Arrows on Drop Down Menu?', 'pagelines' ),
						'title'			=> __( 'Drop Down Arrows', 'pagelines' ),
						'shortexp'		=> __( 'Enable arrows for drop down navigation', 'pagelines' ),
						'exp'			=> __( 'Checking this option will create arrows for the drop down menus', 'pagelines' )
						)),
				'inputlabel'			=> __( 'Select Which Drop Down Options To Show', 'pagelines' ),
				'title'					=> __( 'Nav Classic &amp; BrandNav Section - Drop Down Handling', 'pagelines' ),						
				'shortexp'				=> __( 'Select which to show', 'pagelines' ),
				'exp'					=> __( 'Enable drop downs and choose the options you would like to show', 'pagelines' ) 
			 
			),
			'hidesearch' => array(
					'version'			=> 'pro',
					'default'			=> false,
					'type'				=> 'check',
					'inputlabel'		=> __( 'Hide search field?', 'pagelines' ),
					'title'				=> __( 'Nav Classic - Hide Search', 'pagelines' ),						
					'shortexp'			=> __( 'Remove the search field from the nav section', 'pagelines' ),
					'exp'				=> __( 'Removes the search field from the PageLines Navigation Section.', 'pagelines' )
				), 
			'icon_position'		=> array(
					'version'	=> 'pro',
					'type'		=> 'text_multi',
					'inputsize'	=> 'tiny',
					'selectvalues'	=> array(
						'icon_pos_bottom'	=> array('inputlabel'=> __( 'Distance From Bottom (in pixels)', 'pagelines' ), 'default'=> 12),
						'icon_pos_right'	=> array('inputlabel'=> __( 'Distance From Right (in pixels)', 'pagelines' ), 'default'=> 1),
					),
					'title'		=> __( 'Branding Section - Social Icon Position', 'pagelines' ),
					'shortexp'	=> __( 'Control the location of the social icons in the branding section', 'pagelines' ),
					'exp'		=> __( 'Set the position of your header icons with these options. They will be relative to the <strong>branding</strong> section of your site.', 'pagelines' )
			),
			'rsslink' => array(
					'default'	=> true,
					'type'		=> 'check',
					'inputlabel'=> __( 'Display the Blog RSS icon and link?', 'pagelines' ),
					'title'		=> __( 'News/Blog RSS Icon', 'pagelines' ),
					'shortexp'	=> __( 'Places a news/blog RSS icon in your header', 'pagelines' ),
					'exp'		=> ''
				),
			'facebook_headers'	=> array(
				'default'	=> false,
				'type'		=> 'check',
				'inputlabel'=> __( 'Display facebook Opengraph data in page header?', 'pagelines' ),
				'title'		=> __( 'Facebook Opengraph', 'pagelines' ),
				'shortexp'	=> __( 'Places the special og: data in page <head> area.', 'pagelines' ),
				'exp'		=> ''
			),
			'icon_social' => array(
					'version'	=> 'pro',
					'type'		=> 'text_multi',
					'inputsize'	=> 'regular',
					'selectvalues'	=> array(
						'gpluslink'			=> array('inputlabel'=> __( 'Your Google+ Profile URL', 'pagelines' ), 'default'=> ''),
						'facebooklink'		=> array('inputlabel'=> __( 'Your Facebook Profile URL', 'pagelines' ), 'default'=> ''),
						'twitterlink'		=> array('inputlabel'=> __( 'Your Twitter Profile URL', 'pagelines' ), 'default'=> ''),
						'linkedinlink'		=> array('inputlabel'=> __( 'Your LinkedIn Profile URL', 'pagelines' ), 'default'=> ''),
						'youtubelink'		=> array('inputlabel'=> __( 'Your YouTube Profile URL', 'pagelines' ), 'default'=> ''),
					),
					'title'		=> __( 'Social Icons', 'pagelines' ),
					'shortexp'	=> __( 'Add social network profile icons to your header', 'pagelines' ),
					'exp'		=> __( 'Fill in the URLs of your social networking profiles. This option will create icons in the header/branding section of your site.', 'pagelines' )
			),
			'nav_use_hierarchy' => array(
					'default'	=> false,
					'type'		=> 'check',
					'inputlabel'=> __( 'Use Child Pages For Secondary Nav?', 'pagelines' ),
					'title'		=> __( 'Secondary Nav - Use Child Pages', 'pagelines' ),
					'shortexp'	=> __( 'Use this options if you want child pages in secondary nav, instead of WP menus', 'pagelines' ),
					'exp'		=> ''
				),
			'footer_num_columns' => array(
				'type' 			=> 'count_select',		
				'count_start'	=> '1',
				'count_number'	=> '6', 
				'title' 		=> __( 'FootCols Section - Column Number', 'pagelines' ),
				'shortexp' 		=> __( 'Control the number of columns per row in your footer columns section', 'pagelines' ),
				'inputlabel'	=> __( 'Select Number of Footer Columns', 'pagelines' )
			),
			'footer_logo' => array(
					'version'	=> 'pro',
					'default'	=> PL_IMAGES.'/logo-small.png',
					'type'		=> 'image_upload',
					'imagepreview'	=> '100',
					'inputlabel'	=> __( 'Add Footer logo', 'pagelines' ),
					'title'		=> __( 'FootCols Section - Logo', 'pagelines' ),
					'shortexp'	=> __( 'Show a logo in the footer', 'pagelines' ),
					'exp'		=> __( 'Add the full url of an image for use in the footer. Recommended size: 140px wide.', 'pagelines' )
			),
			'footer_more' => array(
					'default'	=> sprintf( __( "Thanks for dropping by! Feel free to join the discussion by leaving comments, and stay updated by subscribing to the <a href='%s'>RSS feed</a>.", 'pagelines' ), get_bloginfo('rss2_url') ),
					'type'		=> 'textarea',
					'inputlabel'=> __( 'More Statement In Footer', 'pagelines' ),
					'title'		=> __( 'FootCols Section - More Statement', 'pagelines' ),
					'shortexp'	=> __( 'Add a quick statement for users who want to know more...', 'pagelines' ),
					'exp'		=> __( "This statement will show in the footer columns under the word more. It is for users who may want to know more about your company or service.", 'pagelines' )
			),
			'footer_terms' => array(
					'default' 	=> '&copy; '.date('Y').' '.get_bloginfo('name'),
					'type' 		=> 'textarea',
					'inputlabel'=> __( 'Terms line in footer:', 'pagelines' ),
					'title' 	=> __( 'FootCols Section - Site Terms Statement', 'pagelines' ),
					'shortexp' 	=> __( 'A line in your footer for <strong>terms and conditions</strong> text or similar', 'pagelines' ),
					'exp' 		=> __( "It's sometimes a good idea to give your users a terms and conditions statement so they know how they should use your service or content.", 'pagelines' )
			)
		);
		
		return apply_filters('pagelines_options_header_footer', $a);
		
	}

	/**
	 * Blog and Post Options
	 *
	 * @since 2.0.0
	 */
	function blog_posts(){

		$a = array(
			'icon'			=> PL_ADMIN_ICONS.'/blog.png',
			'blog_layout_mode'	=> array(
					'default'		=> 'magazine',
					'default_free'	=> 'blog',
					'type'			=> 'graphic_selector',
					'showname'		=> true,
					'sprite'		=> PL_ADMIN_IMAGES.'/sprite-blog-modes.png',
					'height'		=> '90px', 
					'width'			=> '115px',
					'layout'		=> 'interface',
					'selectvalues'	=> array(
						'magazine'	=> array('name' => __( "Magazine Layout Mode", 'pagelines' ), 'version' => 'pro', 'offset' => '0px -90px', 'version' => 'pro'),
						'blog'		=> array('name' => __( "Blog Layout Mode", 'pagelines' ), 'offset' => '0px 0px')
						), 
					'inputlabel'	=> __( 'Select Post Layout Mode', 'pagelines' ),
					'title'			=> __( 'Blog Post Layout Mode', 'pagelines' ),						
					'shortexp'		=> __( 'Choose between magazine style and blog style layout', 'pagelines' ),
					'exp'			=> __( 'Choose between two magazine or blog layout mode. <br/><br/> <strong>Magazine Layout Mode</strong><br/> Magazine layout mode makes use of post <strong>clips</strong>. These are summarized excerpts shown at half the width of the main content column.<br/>  <strong>Note:</strong> There is an option for showing <strong>full-width</strong> posts on your main <strong>posts</strong> page.<br/><br/><strong>Blog Layout Mode</strong><br/> This is your classical blog layout. Posts span the entire width of the main content column.', 'pagelines' )
				), 
			'excerpt_mode_full' => array(
				'default'		=> 'left',
				'type'			=> 'graphic_selector',
				'inputlabel'	=> __( 'Select Excerpt Mode', 'pagelines' ),
				'showname'		=> true,
				'sprite'		=> PL_ADMIN_IMAGES.'/sprite-excerpt-modes.png',
				'height'		=> '50px', 
				'width'			=> '62px',
				'layout'		=> 'interface',
				'selectvalues'	=> array(
					'left'			=> array( 'name' => __( 'Left Justified', 'pagelines' ), 'offset' => '0px -50px' ), 
					'top'			=> array( 'name' => __( 'On Top', 'pagelines' ), 'offset' => '0px 0px', 'version' => 'pro' ), 
					'left-excerpt'	=> array( 'name' => __( 'Left, In Excerpt', 'pagelines' ), 'offset' => '0px -100px' ), 
					'right-excerpt'	=> array( 'name' => __( 'Right, In Excerpt', 'pagelines' ), 'offset' => '0px -150px', 'version' => 'pro' ), 
					
				),
				'title'		=> __( 'Feature Post Excerpt Mode', 'pagelines' ),						
				'shortexp'	=> __( 'Select how thumbs should be handled in full-width posts', 'pagelines' ),
				'exp'		=> __( 'Use this option to configure how thumbs will be shown in full-width posts on your blog page.', 'pagelines' )
			),
			'metabar_standard' => array(
				'default'		=> 'By [post_author_posts_link] On [post_date] &middot; [post_comments] [post_edit]',
				'type'			=> 'text',
				'inputlabel'	=> __( 'Configure Full Width Post Metabar', 'pagelines' ),
				'title'			=> __( 'Full Width Post Meta', 'pagelines' ),				
				'layout'		=> 'full',		
				'shortexp'		=> __( 'Additional information about a post such as Author, Date, etc...', 'pagelines' ),
				'exp'			=> __( 'Use shortcodes to control the dynamic information in your metabar. Example shortcodes you can use are: <ul><li><strong>[post_categories]</strong> - List of categories</li><li><strong>[post_edit]</strong> - Link for admins to edit the post</li><li><strong>[post_tags]</strong> - List of post tags</li><li><strong>[post_comments]</strong> - Link to post comments</li><li><strong>[post_author_posts_link]</strong> - Author and link to archive</li><li><strong>[post_author_link]</strong> - Link to author URL</li><li><strong>[post_author]</strong> - Post author with no link</li><li><strong>[post_time]</strong> - Time of post</li><li><strong>[post_date]</strong> - Date of post</li><li><strong>[post_type]</strong> - Type of post</li></ul>', 'pagelines' )
			),
			'excerpt_mode_clip' => array(
				'version'		=> 'pro',
				'default'		=> 'left',
				'type'			=> 'graphic_selector',
				'inputlabel'	=> __( 'Select Clip Excerpt Mode', 'pagelines' ),
				'showname'		=> true,
				'sprite'		=> PL_ADMIN_IMAGES.'/sprite-excerpt-modes.png',
				'height'		=> '50px', 
				'width'			=> '62px',
				'layout'		=> 'interface',
				'selectvalues'	=> array(
					'left'			=> array( 'name' => __( 'Left Justified', 'pagelines' ), 'offset' => '0px -50px' ), 
					'top'			=> array( 'name' => __( 'On Top', 'pagelines' ), 'offset' => '0px 0px' ), 
					'left-excerpt'	=> array( 'name' => __( 'Left, In Excerpt', 'pagelines' ), 'offset' => '0px -100px' ), 
					'right-excerpt'	=> array( 'name' => __( 'Right, In Excerpt', 'pagelines' ), 'offset' => '0px -150px' ), 
					
				),
				'title'		=> __( 'Clip Excerpt Mode', 'pagelines' ),						
				'shortexp'	=> __( 'Select how thumbs should be handled in clips', 'pagelines' ),
				'exp'		=> __( 'Use this option to configure how thumbs will be shown in clips. These are the smaller <strong>magazine</strong> style excerpts on your blog page.', 'pagelines' )
			),
			'metabar_clip' => array(
				'version'		=> 'pro',
				'default'		=> 'On [post_date] By [post_author_posts_link] [post_edit]',
				'type'			=> 'text',
				'layout'		=> 'full',
				'inputlabel'	=> __( 'Configure Clip Metabar', 'pagelines' ),
				'title'			=> __( 'Clip Metabar', 'pagelines' ),						
				'shortexp'		=> __( 'Additional information about a clip such as Author, Date, etc...', 'pagelines' ),
				'exp'			=> __( 'Use shortcodes to control the dynamic information in your metabar. Example shortcodes you can use are: <ul><li><strong>[post_categories]</strong> - List of categories</li><li><strong>[post_edit]</strong> - Link for admins to edit the post</li><li><strong>[post_tags]</strong> - List of post tags</li><li><strong>[post_comments]</strong> - Link to post comments</li><li><strong>[post_author_posts_link]</strong> - Author and link to archive</li><li><strong>[post_author_link]</strong> - Link to author URL</li><li><strong>[post_author]</strong> - Post author with no link</li><li><strong>[post_time]</strong> - Time of post</li><li><strong>[post_date]</strong> - Date of post</li></ul>', 'pagelines' )
			),
			'full_column_posts'	=> array(
					'version'		=> 'pro',
					'default'		=> 2,
					'type'			=> 'count_select',
					'count_number'	=> get_option('posts_per_page'),
					'inputlabel'	=> __( 'Number of Full Width Posts?', 'pagelines' ),
					'title'			=> __( 'Full Width Posts (Magazine Layout Mode Only)', 'pagelines' ),						
					'shortexp'		=> __( 'When using magazine layout mode, select the number of <strong>featured</strong> or full-width posts', 'pagelines' ),
					'exp'			=> __( 'Select the number of posts you would like shown at the full width of the main content column in magazine layout mode (the rest will be half-width post <strong>clips</strong>).', 'pagelines' )
				),
			'thumb_handling' => array(
					'type'		=> 'check_multi',
					'selectvalues'	=> array(
						'thumb_blog'		=> array('inputlabel'=> __( 'Posts/Blog Page', 'pagelines' ), 'default'=> true),
						'thumb_single'		=> array('inputlabel'=> __( 'Single Post Pages', 'pagelines' ), 'default'=> false),
						'thumb_search' 		=> array('inputlabel'=> __( 'Search Results', 'pagelines' ), 'default'=> false),
						'thumb_category' 	=> array('inputlabel'=> __( 'Category Lists', 'pagelines' ), 'default'=> true),
						'thumb_archive' 	=> array('inputlabel'=> __( 'Post Archives', 'pagelines' ), 'default'=> true),
						'thumb_clip' 		=> array('inputlabel'=> __( 'In Post Clips (Magazine Mode)', 'pagelines' ), 'default'=> true),
					),
					'title'		=> __( 'Post Thumbnail Placement', 'pagelines' ),
					'shortexp'	=> __( 'Where should the theme use post thumbnails?', 'pagelines' ),
					'exp'		=> __( 'Use this option to control where post <strong>featured images</strong> or thumbnails are used. Note: The post clips option only applies when magazine layout is selected.', 'pagelines' )
			),
			'excerpt_handling' => array(
					'type'		=> 'check_multi',
					'selectvalues'	=> array(
						'excerpt_blog'		=> array('inputlabel'=> __( 'Posts/Blog Page', 'pagelines' ), 'default'=> true),
						'excerpt_single'	=> array('inputlabel'=> __( 'Single Post Pages', 'pagelines' ), 'default'=> false),
						'excerpt_search'	=> array('inputlabel'=> __( 'Search Results', 'pagelines' ), 'default'=> true),
						'excerpt_category' 	=> array('inputlabel'=> __( 'Category Lists', 'pagelines' ), 'default'=> true),
						'excerpt_archive' 	=> array('inputlabel'=> __( 'Post Archives', 'pagelines' ), 'default'=> true),
					),
					'title'		=> __( 'Post Excerpt or Summary Handling', 'pagelines' ),
					'shortexp'	=> __( 'Where should the theme use post excerpts when showing full column posts?', 'pagelines' ),
					'exp'		=> __( 'This option helps you control where post excerpts are displayed.<br/><br/> <strong>About:</strong> Excerpts are small summaries of articles filled out when creating a post.', 'pagelines' )
			),
			
			'social_shares' => array(
					'type'		=> 'check_multi',
					'selectvalues'	=> array(
						'share_facebook'	=> array('inputlabel'=> __( 'Facebook', 'pagelines' ), 'default'=> true),
						'share_twitter'		=> array('inputlabel'=> __( 'Twitter', 'pagelines' ), 'default'=> true),
						'twitter_hash'		=> array('inputlabel'=> __( 'Twitter - Add #Hashtag', 'pagelines' ), 'default'=> true, 'version' => 'pro'),
						'twitter_via'		=> array('inputlabel'=> __( 'Twitter - Show Via @Handle', 'pagelines' ), 'default'=> false, 'version' => 'pro'),
						'share_stumble'		=> array('inputlabel'=> __( 'StumbleUpon', 'pagelines' ), 'default'=> false, 'version' => 'pro'),
						'share_google'		=> array('inputlabel'=> __( 'Google+', 'pagelines' ), 'default'=> true),
						'share_buffer'		=> array('inputlabel'=> __( 'Buffer', 'pagelines' ), 'default'=> false, 'version' => 'pro'),
						'share_linkedin'	=> array('inputlabel'=> __( 'LinkedIn', 'pagelines' ), 'default'=> false),
						'share_pinterest'	=> array('inputlabel'=> __( 'Pinterest', 'pagelines' ), 'default'=> true, 'version' => 'pro'),
						'share_under_meta'	=> array('inputlabel'=> __( 'Add Shares Under Metabar', 'pagelines' ), 'default'=> false, 'version' => 'pro'),
					),
					'inputlabel'=> __( 'Select Which Share Buttons To Show', 'pagelines' ),
					'title'		=> __( 'Sharebar Social Sharing Buttons', 'pagelines' ),						
					'shortexp'	=> __( 'Select which to show and configure appearance', 'pagelines' ),
					'exp'		=> __( "Select which social sharing buttons you would like to use in your Sharebar.<br/><br/> <strong>Note:</strong> that since these use iFrames and javascript (provided by the companies themselves) they may be hard to style and control.", 'pagelines' )
		    ),
			'continue_reading_text' => array(
					'version'	=> 'pro',
					'default'	=> 'Read Full Article &rarr;',
					'type'		=> 'text',
					'inputlabel'=> __( 'Continue Reading Link Text', 'pagelines' ),
					'title'		=> __( '<strong>Continue Reading</strong> Link Text (When Using Excerpts)', 'pagelines' ),						
					'shortexp'	=> __( 'The link at the end of your excerpt', 'pagelines' ),
					'exp' 		=> __( "This text will be used as the link to your full article when viewing articles on your posts page (when excerpts are turned on).", 'pagelines' )
			),
			'content_handling' => array(
					'type'		=> 'check_multi',
					'selectvalues'=> array(
						'content_blog'		=> array('inputlabel'=> __( 'Posts/Blog Page', 'pagelines' ), 'default'=> false),
						'content_search'	=> array('inputlabel'=> __( 'Search Results', 'pagelines' ), 'default'=> false),
						'content_category' 	=> array('inputlabel'=> __( 'Category Lists', 'pagelines' ), 'default'=> false),
						'content_archive' 	=> array('inputlabel'=> __( 'Post Archives', 'pagelines' ), 'default'=> false),
					),
					'title'		=> __( 'Full Post Content', 'pagelines' ),
					'shortexp'	=> __( 'In addition to single post pages and page templates, where should the theme place the full content of posts?', 'pagelines' ),
					'exp'		=> __( 'Choose where the full content of posts is displayed. Choose between all posts pages or just single post pages (i.e. posts pages can just show excerpts or titles).', 'pagelines' )
			),

			'excerpt_len' => array(
					'version'	=> 'pro',
					'default' 	=> 55,
					'type' 		=> 'text',
					'inputlabel'=> __( 'Number of words.', 'pagelines' ),
					'title' 	=> __( 'Excerpt Length', 'pagelines' ),
					'shortexp' 	=> __( 'Set the length of excerpts to something other than default', 'pagelines' ),
					'exp' 		=> __( 'Excerpts are set to 55 words by default.', 'pagelines' )
			),
			'excerpt_tags' => array(
					'version'	=> 'pro',
					'default' 	=> '<a>',
					'type' 		=> 'text',
					'inputlabel'=> __( 'Allowed Tags', 'pagelines' ),
					'title' 	=> __( 'Allow Tags in Excerpt', 'pagelines' ),
					'shortexp' 	=> __( 'Control which tags are stripped from excerpts', 'pagelines' ),
					'exp' 		=> __( 'By default WordPress strips all HTML tags from excerpts. You can use this option to allow certain tags. Simply enter the allowed tags in this field. <br/>An example of allowed tags could be: <strong>&lt;p&gt;&lt;br&gt;&lt;a&gt;</strong>. <br/><br/> <strong>Note:</strong> Enter a period <strong>.</strong> to disallow all tags.', 'pagelines' )
			)			
		);
		
		return apply_filters('pagelines_options_blog_posts', $a);
		
	}
	
	/**
	 * Advanced and Misc Options
	 *
	 * @since 2.0.0
	 */
	function advanced(){

		$a = array(
			'icon'			=> PL_ADMIN_ICONS.'/settings.png',

			'google_ie' => array(
					'default'	=> false,
					'type'		=> 'check',
					'inputlabel'=> __( 'Include Google IE Compatibility Script?', 'pagelines' ),
					'title'		=> __( 'Google IE Compatibility Fix', 'pagelines' ),
					'shortexp'	=> __( 'Include a Google JS script that fixes problems with IE', 'pagelines' ),
					'exp'		=> __( "More info on this can be found <a target='_blank' href='http://code.google.com/p/ie7-js/'>here</a>.", 'pagelines' )
			),
			'load_prettify_libs' => array(
					'default'	=> false,
					'type'		=> 'check',
					'inputlabel'=> __( 'Enable Code Prettify?', 'pagelines' ),
					'title'		=> __( 'Google Prettify Code', 'pagelines' ),
					'shortexp'	=> __( 'Adds pretty syntax highlighting for code.', 'pagelines' ),
					'exp'		=> __( "Add a class of 'prettyprint' to code or pre tags, or optionally use the [pl_codebox] shortcode. Wrap the codebox shortcode using [pl_raw] if Wordpress inserts line breaks.", 'pagelines' )
			),
			'partner_link' 	=> array(
					'default'	=> '',
					'type'		=> 'text',
					'inputlabel'=> __( 'Enter Partner Link', 'pagelines' ),
					'title'		=> __( 'PageLines Affiliate/Partner Link', 'pagelines' ),
					'shortexp'	=> __( 'Change a your PageLines footer link to a PageLines affiliate link', 'pagelines' ),
					'exp'		=> __( "If you are a <a target='_blank' href='http://www.pagelines.com'>PageLines Partner</a> enter your link here and the footer link will become a partner or affiliate link.", 'pagelines' )
			),

			'disable_ajax_save' => array(
					'default'	=> '',
					'type'		=> 'check',
					'inputlabel'=> __( 'Disable AJAX Saving?', 'pagelines' ),
					'title'		=> __( 'Disable AJAX Saving', 'pagelines' ),
					'shortexp'	=> __( 'Check to disable AJAX saving', 'pagelines' ),
					'exp'		=> __( "Check this option if you are having problems with AJAX saving. For example, if design control or typography options aren't working", 'pagelines' )
			),

			'special_body_class' 	=> array(
					'default'	=> '',
					'version'	=> 'pro',
					'type'		=> 'text',
					'inputlabel'=> __( 'Install Class', 'pagelines' ),
					'title'		=> __( 'Current Install Class', 'pagelines' ),
					'shortexp'	=> __( 'Add a special "body" class to this install of PageLines', 'pagelines' ),
					'exp'		=> __( "Use this option to add a class to the &gt;body&lt; element of the website. This can be useful when using the same child theme on several installations or sub domains and can be used to control CSS customizations.", 'pagelines' )
			),
			'enable_debug' => array(
					'default'	=> '',
					'version'	=> 'pro',
					'type'		=> 'check',
					'inputlabel'=> __( 'Enable debug?', 'pagelines' ),
					'title'		=> __( 'PageLines debug', 'pagelines' ),
					'shortexp'	=> __( 'Show detailed settings information', 'pagelines' ),
					'exp'		=>	sprintf( __( 'This information can be useful in the forums if you have a problem. %s', 'pagelines' ),
									sprintf( '%s', ( ploption( 'enable_debug' ) ) ? 
									sprintf( '<br /><a href="%s">Click here</a> for your debug info.', site_url( '?pldebug=1' ) ) : '' ) )
			),
			'pl_minify' => array(
					'default'	=> false,
					'type'		=> 'check',
					'inputlabel'=> __( 'Enable CSS minification?', 'pagelines' ),
					'title'		=> __( 'Minification', 'pagelines' ),
					'shortexp'	=> __( 'Strips whitespace from compiled CSS.', 'pagelines' ),
					'exp'		=> ""
			),
			'hide_pagelines_introduction' => array(
					'default'	=> '',
					'version'	=> 'pro',
					'type'		=> 'check',
					'inputlabel'=> __( 'Hide the introduction?', 'pagelines' ),
					'title'		=> __( 'Show Theme Introduction', 'pagelines' ),
					'shortexp'	=> __( 'Uncheck this option to show theme introduction', 'pagelines' ),
					'exp'		=> ""
			),
			'hide_controls_meta'	 => array(
					'default' 		=> 'publish_posts',
					'version'		=> 'pro',
					'type' 			=> 'select',
					'selectvalues'	=> array(
						'edit_users'			=> array('name' => __( 'Administrator', 'pagelines') ),
						'moderate_comments'		=> array('name' => __( 'Editor', 'pagelines') ),
						'publish_posts'			=> array('name' => __( 'Author', 'pagelines') ),
						'edit_posts'			=> array('name' => __( 'Contributor', 'pagelines') )
					),
					'inputlabel' 	=> __( 'Minimum user level for Post/Page Meta Settings', 'pagelines' ),
					'title' 		=> __( 'Post/Page Meta', 'pagelines' ),
					'shortexp' 		=> __( 'Set userlevels for the different settings pages. ', 'pagelines' ),
					'exp' 			=> __( 'Members with a user level lower than the settings here will not be able to see the settings.', 'pagelines' )
			),
			'hide_controls_cpt' 	=> array(
					'default' 		=> 'moderate_comments',
					'version'		=> 'pro',
					'type' 			=> 'select',
					'title'			=> 'Special Post Types',
					'selectvalues'	=> array(
						'edit_users'			=> array('name' => __( 'Administrator', 'pagelines') ),
						'moderate_comments'		=> array('name' => __( 'Editor', 'pagelines') ),
						'publish_posts'			=> array('name' => __( 'Author', 'pagelines') ),
						'edit_posts'			=> array('name' => __( 'Contributor', 'pagelines') )
					),
					'inputlabel' 	=> __( 'Minimum user level for Custom Post Types ( banners, features etc )', 'pagelines' ),
					'exp' 			=> __( 'Members with a user level lower than the settings here will not be able to see the settings.', 'pagelines' )

			)

		);
		
		return apply_filters('pagelines_options_advanced', $a);
		
	}
	
	/**
	 * Custom Coding Options
	 *
	 * @since 2.0.0
	 */
	function custom_code(){

		$a = array(
			'icon'			=> PL_ADMIN_ICONS.'/code.png',
			'customcss' => array(
					'default' 	=> 'body{}',
					'type' 		=> 'code',
					'height'	=> '300px',
					'layout' 	=> 'full',
					'inputlabel'=> __( 'CSS Rules', 'pagelines' ),
					'title' 	=> __( 'Custom CSS / LESS', 'pagelines' ),
					'shortexp' 	=> __( 'Insert custom CSS or LESS styling here. It will be stored in the DB and not overwritten. <br/>Note: The professional way to customize your site is using a child theme, or customization plugin', 'pagelines' ),
					'exp' 		=> $this->css_examples(),
					'vidtitle'	=> __( 'View Customization Documentation', 'pagelines' )
				),
			'headerscripts' => array(
					'version'	=> 'pro',
					'default'	=> '',
					'type'		=> 'code',
					'layout'	=> 'full',
					'inputlabel'=> __( 'Headerscripts Code', 'pagelines' ),
					'title'		=> __( 'Header Scripts', 'pagelines' ),
					'shortexp'	=> __( 'Scripts inserted directly before the end of the HTML &lt;head&gt; tag', 'pagelines' ),
					'exp'		=> ''
				),
			'footerscripts' => array(
					'default'	=> '',						
					'type'		=> 'code',
					'layout'	=> 'full',
					'inputlabel'=> __( 'Footerscripts Code or Analytics', 'pagelines' ),
					'title'		=> __( 'Footer Scripts &amp; Analytics', 'pagelines' ),
					'shortexp'	=> __( 'Any footer scripts including Google Analytics', 'pagelines' ),
					'exp'		=> ''
				), 
			'asynch_analytics' => array(
					'version'	=> 'pro',
					'default'	=> '',
					'type'		=> 'code',
					'layout'	=> 'full',
					'inputlabel'=> __( 'Asynchronous Analytics', 'pagelines' ),
					'title'		=> __( 'Asynchronous Analytics', 'pagelines' ),
					'shortexp'	=> __( 'Placeholder for Google asynchronous analytics. Goes just before <strong>&lt;/html&gt;</strong> tag', 'pagelines' ),
					'exp'		=> ''
			),
		);
		
		return apply_filters('pagelines_options_custom_code', $a);
	}
	
	
	function css_examples() {
		
		$example_body = ".post-excerpt{<br/>&nbsp;&nbsp;&nbsp;background-color: lighten(@dark-base, 50);<br/>&nbsp;&nbsp;&nbsp;.border-radius( 10px );<br/>&nbsp;&nbsp;&nbsp;padding: 5px;<br />}";
		
		$example = sprintf( "<div class='theexample'><strong>Example:</strong><br/>%s</div>", $example_body );
		
		$docs = __("Enter CSS Rules to change the style of your site.<br/><br/> A lot can be accomplished by simply changing the default styles of the <strong>body</strong> tag such as <strong>line-height</strong>, <strong>font-size</strong>, or <strong>color</strong> (as in text color).", 'pagelines' );
		
		return $example . $docs;
		
		
	}
	
	
	function account_signup(){
		
		ob_start(); 
		?>
		whoop
		<?php 
		return ob_get_clean();
		
	}
	


	
	/**
	 * Custom Options (Deprecated)
	 *
	 * @since 2.0.0
	 */
	function custom_options(){

		$a = array(	);
		
		return apply_filters('pagelines_custom_options', $a);
		
	}
	
	
}

/**
 *
 *  Returns Options Array
 *
 */
function get_option_array( $load_unavailable = true ){
	
	global $disabled_settings; 
	
	$default = new PageLinesOptionsArray();
	 
	$optionarray =  array_merge( $default->options, $default->last_options);
	if( isset($disabled_settings) && !empty($disabled_settings) ){
		foreach($disabled_settings as $key => $s){
			
			if( isset( $s['section'] ) && false != $s['option_id'] ) {
				if( isset($optionarray[$s['panel']][ $s['option_id'] ]) && ( !$load_unavailable || $s['keep'] == false ) )
					unset($optionarray[$s['panel']][ $s['option_id'] ]);
			} else {
				if( isset($optionarray[ $s['panel'] ]) && ( !$load_unavailable || $s['keep'] == false ) ) 
					unset($optionarray[ $s['panel'] ]);
			}	
		}
	}
	return apply_filters('pagelines_options_array', $optionarray);	
}
