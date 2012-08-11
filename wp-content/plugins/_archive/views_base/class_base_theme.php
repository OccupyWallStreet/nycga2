<?php
/**
 * The theme class.
 *
 * @package views_base
 */
class class_base_theme
{
// default sidebars
	var $sidebar_array = array(
		'second_sidebar'		=>	'Second Sidebar',
		'header_sidebar'		=>	'Header Sidebar',
		'first_sidebar'			=>	'First Sidebar',
		'center_header_sidebar'	=>	'Center Header Sidebar',
		'center_foot_sidebar'	=>	'Center Foot Sidebar',
		'foot_sidebar_1'		=>	'Foot Sidebar 1',
		'foot_sidebar_2'		=>	'Foot Sidebar 2',
		'foot_sidebar_3'		=>	'Foot Sidebar 3',
	),
// Theme Options Name in database
	$options_name = '',
// Theme Options array in theme options
	$options_arr = array(),
// Theme options page name
	$option_page = 'theme_options',
// Font Size Options in the Theme options page
	$site_title_font_size_options = array(
		'14px' => 'font-size:14px; font-size:1.4rem;',
		'22px' => 'font-size:22px; font-size:2.2rem;',
		'32px' => 'font-size:32px; font-size:3.2rem;',
		'48px' => 'font-size:48px; font-size:4.8rem;',
		'60px' => 'font-size:60px; font-size:6.0rem;',
		'72px' => 'font-size:72px; font-size:7.2rem;',
		'92px' => 'font-size:92px; font-size:9.2rem;',
	),
	$site_description_font_size_options = array(
		'12px' => 'font-size:12px; font-size: 1.2rem;',
		'14px' => 'font-size:14px; font-size: 1.4rem;',
		'18px' => 'font-size:18px; font-size: 1.8rem;',
		'22px' => 'font-size:22px; font-size: 2.2rem;',
		'28px' => 'font-size:28px; font-size: 2.8rem;',
		'32px' => 'font-size:32px; font-size: 3.2rem;',
		'40px' => 'font-size:40px; font-size: 4.0rem;',
	),
// Font Family Options in the Theme options page
	$font_family_options = array(
		'Georgia'			=> 'Georgia, serif',
		'Times New Roman'	=> '"Times New Roman", Times, serif',
		'Arial'				=> 'Arial, Helvetica, sans-serif',
		'Helvetica Neue'	=> '"Helvetica Neue",Helvetica,Arial,sans-serif',
		'Lucia Grande'		=> '"Lucida Sans Unicode", "Lucida Grande", sans-serif',
		'Tahoma'			=> 'Tahoma, Geneva, sans-serif',
		'Verdana'			=> 'Verdana, Geneva, sans-serif',
		'Impact'			=> 'Impact, Charcoal, sans-serif',
	),
// Sidebar need to be counted in middle_switch
	$middle_switch_arr = array(
		'first_sidebar', 
		'second_sidebar'
	);
	
/**
 * sidebar_action_bar in wp-admin widget
 */
	function __construct()
	{
	// Theme Options array in theme options
		$this->options_arr = array
		(
			// $section_id	=> array('title' => $section_title, 'callback' => $callback, 'page' => $page, 'fields' = array()),
			'logo_favicon'	=>	array(
				'title' => __('Custom Logo, Favicon and Background', 'views_base'), 
				'callback' => 'layout_section_logo_favicon', 
				'page' => $this->option_page,
				'fields' => array(
				// $option_id	=>	array('title' => $option_title, 'callback' => $callback, 'args'	=>	array(),'sanitation' => $sanitation),
					'custom_logo'	=>	array(
						'title' => __('Custom Logo', 'views_base'), 
						'callback' => 'layout_file', 
						'args' => array(
						//arg_name => arg_value,
							'default_value'	=>	'',
						),
						'sanitation' => 'esc_url',
					),
					'custom_favicon'	=>	array(
						'title' => __('Custom Favicon', 'views_base'), 
						'callback' => 'layout_file', 
						'args' => array(
							'default_value'	=>	get_template_directory_uri() . '/images/favicon.ico',
						),
						'sanitation' => 'esc_url',
					),
					'header_background'	=>	array(
						'title' => __('Header Background', 'views_base'), 
						'callback' => 'layout_file', 
						'args' => array(
							'default_value'	=>	'',
						),
						'sanitation' => 'esc_url',
					),
					'main_background'	=>	array(
						'title' => __('Main Background', 'views_base'), 
						'callback' => 'layout_file', 
						'args' => array(
							'default_value'	=>	'',
						),
						'sanitation' => 'esc_url',
					),
					'footer_background'	=>	array(
						'title' => __('Footer Background', 'views_base'), 
						'callback' => 'layout_file', 
						'args' => array(
							'default_value'	=>	'',
						),
						'sanitation' => 'esc_url',
					),
					'site_description_position'	=>	array(
						'title' => __('Site Description Position', 'views_base'), 
						'callback' => 'layout_select', 
						'args' => array(
							'default_value'	=>	0,
							'options'	=>	array(
								0 => __('Next to site title', 'views_base'),
								1 => __('Below site title', 'views_base'),
							),
						),
						'sanitation' => 'intval',
					),
				),
			),
			'font_options'	=>	array(
				'title' => __('Font Options', 'views_base'), 
				'callback' => 'layout_section_font_options', 
				'page' => $this->option_page,
				'fields' => array(
					'site_title_font_family'	=>	array(
						'title' => __('Site Title Font Family', 'views_base'), 
						'callback' => 'layout_font_select', 
						'args' => array(
							'default_value'	=>	'Times New Roman',
							'options'	=>	$this->font_family_options,
						),
						'sanitation' => 'esc_attr',
					),
					'site_title_font_size'	=>	array(
						'title' => __('Site Title Font Size', 'views_base'), 
						'callback' => 'layout_font_select', 
						'args' => array(
							'default_value'	=>	'32px',
							'options'	=>	$this->site_title_font_size_options,
						),
						'sanitation' => 'esc_attr',
					),
					'site_title_style'	=>	array(
						'title' => __('Site Title Style', 'views_base'), 
						'callback' => 'layout_select', 
						'args' => array(
							'default_value'	=>	'bold',
							'options'	=>	array(
								'normal' => __('normal', 'views_base'),
								'italic' => __('italic', 'views_base'),
								'oblique' => __('oblique', 'views_base'),
							),
						),
						'sanitation' => 'esc_attr',
					),
					'site_description_font_family'	=>	array(
						'title' => __('Site Description Font Family', 'views_base'), 
						'callback' => 'layout_font_select', 
						'args' => array(
							'default_value'	=>	'Helvetica Neue',
							'options'	=>	$this->font_family_options,
						),
						'sanitation' => 'esc_attr',
					),
					'site_description_font_size'	=>	array(
						'title' => __('Site Description Font Size', 'views_base'), 
						'callback' => 'layout_font_select', 
						'args' => array(
							'default_value'	=>	'14px',
							'options'	=>	$this->site_description_font_size_options,
						),
						'sanitation' => 'esc_attr',
					),
					'site_description_style'	=>	array(
						'title' => __('Site Description Style', 'views_base'), 
						'callback' => 'layout_select', 
						'args' => array(
							'default_value'	=>	'italic',
							'options'	=>	array(
								'normal' => __('normal', 'views_base'),
								'italic' => __('italic', 'views_base'),
								'oblique' => __('oblique', 'views_base'),
							),
						),
						'sanitation' => 'esc_attr',
					),
					'site_title_margin_top'	=>	array(
						'title' => __('Site Title Margin Top', 'views_base'), 
						'callback' => 'layout_text', 
						'args' => array(
							'default_value'	=>	'0',
						),
						'sanitation' => 'floatval',
					),
					'site_title_margin_right'	=>	array(
						'title' => __('Site Title Margin Right', 'views_base'), 
						'callback' => 'layout_text', 
						'args' => array(
							'default_value'	=>	'0.5',
						),
						'sanitation' => 'floatval',
					),
					'site_title_margin_bottom'	=>	array(
						'title' => __('Site Title Margin Bottom', 'views_base'), 
						'callback' => 'layout_text', 
						'args' => array(
							'default_value'	=>	'0',
						),
						'sanitation' => 'floatval',
					),
					'site_title_margin_left'	=>	array(
						'title' => __('Site Title Margin Left', 'views_base'), 
						'callback' => 'layout_text', 
						'args' => array(
							'default_value'	=>	'0',
						),
						'sanitation' => 'floatval',
					),
					'site_description_margin_top'	=>	array(
						'title' => __('Site Description Margin Top', 'views_base'), 
						'callback' => 'layout_text', 
						'args' => array(
							'default_value'	=>	'0',
						),
						'sanitation' => 'floatval',
					),
					'site_description_margin_right'	=>	array(
						'title' => __('Site Description Margin Right', 'views_base'), 
						'callback' => 'layout_text', 
						'args' => array(
							'default_value'	=>	'0',
						),
						'sanitation' => 'floatval',
					),
					'site_description_margin_bottom'	=>	array(
						'title' => __('Site Description Margin Bottom', 'views_base'), 
						'callback' => 'layout_text', 
						'args' => array(
							'default_value'	=>	'0',
						),
						'sanitation' => 'floatval',
					),
					'site_description_margin_left'	=>	array(
						'title' => __('Site Description Margin Left', 'views_base'), 
						'callback' => 'layout_text', 
						'args' => array(
							'default_value'	=>	'0',
						),
						'sanitation' => 'floatval',
					),
					'site_title_padding_top'	=>	array(
						'title' => __('Site Title Padding Top', 'views_base'), 
						'callback' => 'layout_text', 
						'args' => array(
							'default_value'	=>	'0',
						),
						'sanitation' => 'floatval',
					),
					'site_title_padding_right'	=>	array(
						'title' => __('Site Title Padding Right', 'views_base'), 
						'callback' => 'layout_text', 
						'args' => array(
							'default_value'	=>	'0',
						),
						'sanitation' => 'floatval',
					),
					'site_title_padding_bottom'	=>	array(
						'title' => __('Site Title Padding Bottom', 'views_base'), 
						'callback' => 'layout_text', 
						'args' => array(
							'default_value'	=>	'0',
						),
						'sanitation' => 'floatval',
					),
					'site_title_padding_left'	=>	array(
						'title' => __('Site Title Padding Left', 'views_base'), 
						'callback' => 'layout_text', 
						'args' => array(
							'default_value'	=>	'0',
						),
						'sanitation' => 'floatval',
					),
					'site_description_padding_top'	=>	array(
						'title' => __('Site Description Padding Top', 'views_base'), 
						'callback' => 'layout_text', 
						'args' => array(
							'default_value'	=>	'0.5',
						),
						'sanitation' => 'floatval',
					),
					'site_description_padding_right'	=>	array(
						'title' => __('Site Description Padding Right', 'views_base'), 
						'callback' => 'layout_text', 
						'args' => array(
							'default_value'	=>	'0',
						),
						'sanitation' => 'floatval',
					),
					'site_description_padding_bottom'	=>	array(
						'title' => __('Site Description Padding Bottom', 'views_base'), 
						'callback' => 'layout_text', 
						'args' => array(
							'default_value'	=>	'0',
						),
						'sanitation' => 'floatval',
					),
					'site_description_padding_left'	=>	array(
						'title' => __('Site Description Padding Left', 'views_base'), 
						'callback' => 'layout_text', 
						'args' => array(
							'default_value'	=>	'0',
						),
						'sanitation' => 'floatval',
					),
				),
			),
			'social_networks'	=>	array(
				'title' => __('Custom Social Networks', 'views_base'), 
				'callback' => 'layout_section_social_networks', 
				'page' => $this->option_page,
				'fields' => array(
					'social_icons'	=>	array(
						'title' => __('Social Icons', 'views_base'), 
						'callback' => 'layout_radio', 
						'args' => array(
							'default_value'	=>	'none',
							'options'	=>	array(
								'none' => __('None', 'views_base'),
								'default' => __('Default', 'views_base'),
								'baidu_share' => __('Baidu Share', 'views_base'),
							),
						),
						'sanitation' => 'esc_attr',
					),
					'social_icons_facebook'	=>	array(
						'title' => __('Facebook', 'views_base'), 
						'callback' => 'layout_text', 
						'args' => array(
							'prepend'	=>	'<span abbr="enable_facebook"></span>',
							'default_value'	=>	'http://facebook.com/icanlocalize',
							'size'	=> 50,
						),
						'sanitation' => 'esc_url',
					),
					'social_icons_twitter'	=>	array(
						'title' => __('Twitter', 'views_base'), 
						'callback' => 'layout_text', 
						'args' => array(
							'prepend'	=>	'<span abbr="enable_twitter"></span>',
							'default_value'	=>	'http://twitter.com/icanlocalize',
							'size'	=> 50,
						),
						'sanitation' => 'esc_url',
					),
					'social_icons_linkedin'	=>	array(
						'title' => __('Linkedin', 'views_base'), 
						'callback' => 'layout_text', 
						'args' => array(
							'prepend'	=>	'<span abbr="enable_linkedin"></span>',
							'default_value'	=>	'http://linkedin.com/icanlocalize',
							'size'	=> 50,
						),
						'sanitation' => 'esc_url',
					),
					'social_icons_google_plus'	=>	array(
						'title' => __('Google+', 'views_base'), 
						'callback' => 'layout_text', 
						'args' => array(
							'prepend'	=>	'<span abbr="enable_google_plus"></span>',
							'default_value'	=>	'http://plus.google.com/icanlocalize',
							'size'	=> 50,
						),
						'sanitation' => 'esc_url',
					),
					'social_icons_flickr'	=>	array(
						'title' => __('Flickr', 'views_base'), 
						'callback' => 'layout_text', 
						'args' => array(
							'prepend'	=>	'<span abbr="enable_flickr"></span>',
							'default_value'	=>	'http://flickr.com/icanlocalize',
							'size'	=> 50,
						),
						'sanitation' => 'esc_url',
					),
					'enable_facebook'	=>	array(
						'title' => __('Enable Facebook', 'views_base'), 
						'callback' => 'layout_checkbox', 
						'args' => array(
							'default_value'	=>	0,
						),
						'sanitation' => 'intval',
					),
					'enable_twitter'	=>	array(
						'title' => __('Enable Twitter', 'views_base'), 
						'callback' => 'layout_checkbox', 
						'args' => array(
							'default_value'	=>	0,
						),
						'sanitation' => 'intval',
					),
					'enable_linkedin'	=>	array(
						'title' => __('Enable Linkedin', 'views_base'), 
						'callback' => 'layout_checkbox', 
						'args' => array(
							'default_value'	=>	0,
						),
						'sanitation' => 'intval',
					),
					'enable_google_plus'	=>	array(
						'title' => __('Enable Google+', 'views_base'), 
						'callback' => 'layout_checkbox', 
						'args' => array(
							'default_value'	=>	0,
						),
						'sanitation' => 'intval',
					),
					'enable_flickr'	=>	array(
						'title' => __('Enable Flickr', 'views_base'), 
						'callback' => 'layout_checkbox', 
						'args' => array(
							'default_value'	=>	0,
						),
						'sanitation' => 'intval',
					),
					'baidu_share_code'	=>	array(
						'title' => __('Baidu Share Code', 'views_base'), 
						'callback' => 'layout_textarea', 
						'args' => array(
							'default_value'	=> 	sprintf('
<!-- Baidu Button BEGIN -->
    <div id="bdshare" class="bdshare_t bds_tools get-codes-bdshare">
        <span class="bds_more">%1$s</span>
        <a class="bds_qzone"></a>
        <a class="bds_tsina"></a>
        <a class="bds_tqq"></a>
        <a class="bds_renren"></a>
		<a class="shareCount"></a>
    </div>
<script type="text/javascript" id="bdshare_js" data="type=tools" ></script>
<script type="text/javascript" id="bdshell_js"></script>
<script type="text/javascript">
	document.getElementById("bdshell_js").src = "http://bdimg.share.baidu.com/static/js/shell_v2.js?cdnversion=" + new Date().getHours();
</script>
<!-- Baidu Button END -->', __('Share To', 'views_base')),
						),
						'sanitation' => 'esc_html',
					),
				)
			),
			'custom_css'	=>	array(
				'title' => __('Custom CSS', 'views_base'), 
				'callback' => 'layout_section_custom_css', 
				'page' => $this->option_page, 
				'fields' => array(
					'enable_custom_css'	=>	array(
						'title' => __('Eanble Custom CSS', 'views_base'), 
						'callback'	=>	'layout_checkbox', 
						'args' => array(
							'default_value'	=>	0,
							'text'	=>	__('Enable/Disable', 'views_base')
						),
						'sanitation' => 'intval',
					),
					'custom_css'	=>	array(
						'title' => __('Custom CSS', 'views_base'), 
						'callback'	=>	'layout_textarea', 
						'args' => array(
							'default_value'	=>	'',
						),
						'sanitation' => 'esc_html',
					),
				)
			),
			'custom_footer'	=>	array(
				'title' => __('Custom Footer', 'views_base'), 
				'callback'	=>	'layout_section_custom_footer', 
				'page' => $this->option_page, 
				'fields' => array(
					'custom_footer'	=>	array(
						'title' => __('Custom Footer', 'views_base'), 
						'callback'	=>	'layout_textarea', 
						'args' => array(
							'default_value'	=>	__('Powered by <a href="http://wp-types.com/documentation/views-inside/views-base-theme/">Views Base</a> &copy; 2012', 'views_base'),
						),
						'sanitation' => 'esc_html',
					),
					'custom_js'	=>	array(
						'title' => __('Custom Analytics Code', 'views_base'), 
						'callback'	=>	'layout_textarea', 
						'args' => array(
							'default_value'	=>	'',
						),
						'sanitation' => 'esc_html',
					),
				)
			),
		);
		add_action( 'after_setup_theme', array(&$this, 'after_setup_theme') );
		add_action( 'widgets_init', array(&$this, 'widgets_init') );
		add_action( 'admin_menu', array(&$this, 'admin_menu') );
		add_action( 'admin_init', array(&$this, 'admin_init') );
		add_action( 'wp_head', array(&$this, 'add_custom_css') );
		add_action( 'wp_head', array(&$this, 'mobilemenu_js') );
		add_action( 'wp_enqueue_scripts', array(&$this, 'views_base_scripts') );
		add_filter( 'wp_page_menu_args', array(&$this, 'wp_page_menu_args') );
		add_filter( 'body_class', array(&$this, 'body_class') );
		add_action( 'get_sidebar', array(&$this, 'get_sidebar') );
		add_action( 'views_base_footer', array(&$this, 'add_custom_footer') );
		add_action( 'views_base_after_footer', array(&$this, 'add_custom_js') );
		add_action( 'views_base_options', array(&$this, 'theme_options_form') );
		add_action( 'wp_head', array(&$this, 'custom_header_styles') );
		add_filter( 'middle_switch', array(&$this, 'middle_switch'), 10, 2 );
	}
			
/**
 * after setup theme
 */
	function after_setup_theme() 
	{
		// Load languages
		load_theme_textdomain( 'views_base', get_template_directory() . '/languages' );
		
		// Add default posts and comments RSS feed links to <head>.
		add_theme_support( 'automatic-feed-links' );
		
		// Add support for a variety of post formats
		add_theme_support( 'post-formats', array( 'aside', 'image', 'link', 'quote', 'gallery', 'video', 'audio', 'chat' ) );
		
		// Add support for custom background.
		//add_custom_background();
		//work until 3.4.
		add_theme_support( 'custom-background');
		
		//Add callback for custom TinyMCE editor stylesheets.
		add_editor_style();
		
		// Add support for a custom header image.
		$header_args = array(
			'flex-width'	=> true,
			'width'	=> 1200,
			'flex-width'	=> true,
			'height'	=> 300,
			'header-text'	=> false,
			'default-image' => get_template_directory_uri() . '/images/header/default_header.jpg',
		);
		// work until 3.4
		add_theme_support( 'custom-header', $header_args );
		
		// The default header image size
/* 		define( 'HEADER_IMAGE_WIDTH', '1200' );
		define( 'HEADER_IMAGE_HEIGHT', '300' );
		define( 'HEADER_IMAGE', '%s/images/header/default_header.jpg'); // %s is the template dir uri
		add_custom_image_header( 
			array(&$this, 'header_style'),
			array(&$this, 'admin_header_style'),
			array(&$this, 'admin_header_image')
		); */
		
		// This theme uses wp_nav_menu() in some location.
		register_nav_menus( 
			array(
				'primary' => __( 'Primary Menu', 'views_base' ),
				'footer' => __( 'Footer Menu', 'views_base' ),
				)
		);
		
		// The default header text color
		define( 'HEADER_TEXTCOLOR', '444' );
		
		// Add custom image size for featured image use, displayed on "standard" posts.
		add_theme_support( 'post-thumbnails' );
		set_post_thumbnail_size( 624, 9999 ); // Unlimited height, soft crop
	}
	
/**
 * add a sidebar in wp-admin widget
 */
	function add_sidebar($sidebar_id = '') {
		if($sidebar_id == '' || !isset($this->sidebar_array[$sidebar_id])) return;
		register_sidebar( array(
			'name' => __( $this->sidebar_array[$sidebar_id], 'views_base' ),
			'id' => $sidebar_id,
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget' => "</section>",
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		) );
	}
		
/**
 * load all sidebars in wp-admin widget
 */
	function widgets_init()
	{
		if(sizeof($this->sidebar_array)<1)return; 
		foreach($this->sidebar_array as $k => $v)
		{
			$this->add_sidebar($k);
		}
	}
		
/**
 * admin_menu
 */
	function admin_menu()
	{
		$page = add_theme_page( __('Manage Theme Options', 'views_base'), __('Theme Options', 'views_base'), 'edit_theme_options', $this->option_page, array(&$this, 'theme_options'));
        add_action('admin_print_styles-' . $page, array(&$this, 'admin_styles'));
	}
			
/**
 * Print theme options form
 */
	function theme_options_form()
	{
		?>
		<h2><?php _e('Theme Options', 'views_base')?></h2>
		<p><a href="http://wp-types.com/documentation/views-inside/views-base-theme/" target="_blank">Theme documentation &raquo;</a></p>
		<form method="post" action="<?php echo admin_url('options.php');?>" id="theme_options_form">
			<?php 
			//do_settings_sections( $page);
			do_settings_sections( 'theme_options' ); 
			//hidden fields
			settings_fields( 'theme_options' );
			?>
			<p class="submit"><input type="submit" name="submit" class="button-primary" value="<?php _e('Save Changes', 'views_base');?>"  /></p>
		</form>
		<?php
	}
			
/**
 * Add Custom CSS to wp-head
 */
	function add_custom_css()
	{
		$enable = get_theme_mod('enable_custom_css');
		$css = get_theme_mod('custom_css');
		if ($enable == 1 && !empty($css)) 
		{
			echo "\n<style type=\"text/css\">\n" . $css . "\n</style>\n";
		}
	}
			
/**
 * Add Custom footer to action: views_base_footer
 */
	function add_custom_footer()
	{
		$str = get_theme_mod('custom_footer');
		if (!empty($str)) 
		{
		// reverse to plain html
		?>
		<div class="custom_footer"><?php echo htmlspecialchars_decode($str);?></div>
		<?php
		}
	}
	
/**
 * Add Custom js code in footer to action: views_base_footer
 */
	function add_custom_js()
	{
		$js = get_theme_mod('custom_js');
		if (!empty($js)) 
		{
		// reverse to plain html
			echo htmlspecialchars_decode($js);
		}
	}
		
/**
 * theme options page layout
 */
	function theme_options()
	{
		?>
		<div class="wrap">
		<div class="icon32" id="icon-options-general"><br></div>
		<?php do_action('views_base_options');?>
		</div>
		<?php
	}
	
/**
 * Enqueue scripts for front-end.
 */
	function views_base_scripts() 
	{
		//For reference: wp_enqueue_script( $handle,$src,$deps,$ver,$in_footer ); 
		wp_enqueue_script( 'mobilemenu', get_template_directory_uri() . '/javascripts/jquery.mobilemenu.js', array( 'jquery' ));
		wp_enqueue_script( 'modernizr', get_template_directory_uri() . '/javascripts/modernizr.js', array("jquery"));
	}
	
/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 *
 */
	function wp_page_menu_args( $args ) {
		$args['show_home'] = true;
		return $args;
	}
	
/**
 * Extends the default WordPress body class to denote a full-width layout.
 *
 * Used in full-width page template.
 *
 */
	function body_class( $classes ) {
		if ( is_page_template( 'full-width' ) )
			$classes[] = 'full-width';
		return $classes;
	}

/**
 * Sanitize options input.
 * @param array $input need be Sanitized.
 *
 */
	function options_sanitize($input = array())
	{
		$sanitize_arr = $this->sanitize_arr();
		$res = array();
		foreach($input as $k => $v)
		{
			if(isset($sanitize_arr[$k]))
			{
			// default esc_html sanitize
				$tmp = esc_html($v);
			// use wordpress default sanitize method
				if(function_exists ($sanitize_arr[$k]))
				{
					$tmp = $sanitize_arr[$k]($v);
				}
			// use sanitize methods in this class
				else if(method_exists (&$this, $sanitize_arr[$k]))
				{
					$tmp = &$this->$sanitize_arr[$k]($v);
				}
				set_theme_mod($k, $tmp);
				continue;
			}
			$res[$k] = $v;
		}
		return $res;
	}
	
/**
 * Get all options name which need be Sanitized.
 *
 */
	function sanitize_arr()
	{
		$res = array();
		$options_arr = $this->options_arr;
		foreach($this->options_arr as $ks => $vs)
		{
			if(isset($vs['fields']))
			{
				foreach($vs['fields'] as $kf => $vf)
				{
					if(isset($vf['sanitation']))
					{
						$res[$kf] = $vf['sanitation'];
					}
				}
			}
		}
		return $res;
	}

/**
 * admin init.
 *
 */
	function admin_init() 
	{
		$this->options_name = "theme_options_" . get_option( 'stylesheet' );
		// For reference: register_setting( $option_group, $option_name, $sanitize_callback );
		register_setting( $this->option_page, $this->options_name, array(&$this, 'options_sanitize') ); 
		
		if(sizeof($this->options_arr)>0)
		{
			foreach($this->options_arr as $ks => $vs)
			{
				// For reference: add_settings_section( $id, $title, $callback, $page );
					add_settings_section( $ks, $vs['title'], array(&$this, $vs['callback']), $vs['page'] );
				if(isset($vs['fields'])&&sizeof($vs['fields'])>0)
				{
					foreach($vs['fields'] as $kf => $vf)
					{
						$args = $vf['args'];
						$args['label_for'] = $kf;
						// For reference: add_settings_field( $id, $title, $callback, $page, $section, $args );
						add_settings_field( 
							$kf, 
							$vf['title'], 
							array(&$this, $vf['callback']), 
							$vs['page'],
							$ks,
							$args
						);
					}
				}
			}
		}
	}
	
/**
 *
 * This is the function that custom css layout section. 
 *
 */
	function layout_section_custom_css() 
	{
		?>
		<p class='intro'>
			<?php _e('Prints CSS styles in the header area in the header area', 'views_base');?>
		</p>
		<?php
	}
	
/**
 *
 * This is the function print logo and favicon layout section. 
 *
 */
	function layout_section_logo_favicon()
	{
		?>
		<p class='intro'>
			<?php _e('The header can include your logo and text. You can choose colors and text position.', 'views_base');?>
		</p>
		<?php
	}
	
/**
 *
 * This is the function print social networks layout section. 
 *
 */
	function layout_section_social_networks()
	{
		?>
		<p class='intro'>
			<?php _e('Prints your social networks in the header area', 'views_base');?>
		</p>
		<?php
	}
	
/**
 *
 * This is the function that font options layout section. 
 *
 */
	function layout_section_font_options() 
	{
		?>
<div id='div_custom_header' style="display:none;">
<style type="text/css">.custom_table_th{width:200px;}</style>
<table width="80%" border="0" cellpadding="5">
  <tr>
    <th class="custom_table_th" scope="col">&nbsp; </th>
    <th scope="col" align="left"><?php _e('Font Family', 'views_base')?></th>
    <th scope="col" align="left"><?php _e('Font Size', 'views_base')?></th>
    <th scope="col" align="left"><?php _e('Style', 'views_base')?></th>
  </tr>
  <tr>
    <td><div align="right"><?php _e('Site Title', 'views_base')?></div></td>
    <td abbr="site_title_font_family">&nbsp;</td>
    <td abbr="site_title_font_size">&nbsp;</td>
    <td abbr="site_title_style">&nbsp;</td>
  </tr>
  <tr>
    <td><div align="right"><?php _e('Site Description', 'views_base')?></div></td>
    <td abbr="site_description_font_family">&nbsp;</td>
    <td abbr="site_description_font_size">&nbsp;</td>
    <td abbr="site_description_style">&nbsp;</td>
  </tr>
</table>
<h3><?php _e('Spacing', 'views_base')?></h3>
<table width="80%" border="0" cellpadding="5">
  <tr>
    <th class="custom_table_th" scope="col"><?php _e('Margin', 'views_base')?> (<?php _e('em', 'views_base')?>)</th>
    <th scope="col"><?php _e('Top', 'views_base')?></th>
    <th scope="col"><?php _e('Right', 'views_base')?></th>
    <th scope="col"><?php _e('Buttom', 'views_base')?></th>
    <th scope="col"><?php _e('Left', 'views_base')?></th>
  </tr>
  <tr>
    <td><div align="right"><?php _e('Site Title', 'views_base')?></div></td>
    <td abbr="site_title_margin_top">&nbsp;</td>
    <td abbr="site_title_margin_right">&nbsp;</td>
    <td abbr="site_title_margin_bottom">&nbsp;</td>
    <td abbr="site_title_margin_left">&nbsp;</td>
  </tr>
  <tr>
    <td><div align="right"><?php _e('Site Description', 'views_base')?></div></td>
    <td abbr="site_description_margin_top">&nbsp;</td>
    <td abbr="site_description_margin_right">&nbsp;</td>
    <td abbr="site_description_margin_bottom">&nbsp;</td>
    <td abbr="site_description_margin_left">&nbsp;</td>
  </tr>
  <tr>
    <td><div align="center"><strong><?php _e('Padding', 'views_base')?> (<?php _e('em', 'views_base')?>)</strong></div></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><div align="right"><?php _e('Site Title', 'views_base')?></div></td>
    <td abbr="site_title_padding_top">&nbsp;</td>
    <td abbr="site_title_padding_right">&nbsp;</td>
    <td abbr="site_title_padding_bottom">&nbsp;</td>
    <td abbr="site_title_padding_left">&nbsp;</td>
  </tr>
  <tr>
    <td><div align="right"><?php _e('Site Description', 'views_base')?></div></td>
    <td abbr="site_description_padding_top">&nbsp;</td>
    <td abbr="site_description_padding_right">&nbsp;</td>
    <td abbr="site_description_padding_bottom">&nbsp;</td>
    <td abbr="site_description_padding_left">&nbsp;</td>
  </tr>
</table>
</div>
		<?php
	}
	
/**
 *
 * This is the function that custom footer layout section. 
 *
 */
	function layout_section_custom_footer() 
	{
		?>
		<p class='intro'>
			<?php _e('Prints credits and copyright Plain HTML in the footer area.', 'views_base');?>
		</p>
		<?php
	}
	
/**
 *
 * This is the function that adds our Default Layout checkbox. 
 * @param array $args Optional. Override the defaults.
 */
	function layout_checkbox($args = array()) 
	{
		if(!isset($args['label_for'])||$args['label_for']=='') return;
		$label_for = $args['label_for'];
		
		$default_value = '';
		if(isset($args['default_value'])) $default_value = $args['default_value'];
		$enable = intval(get_theme_mod($args['label_for'], $default_value));
		$option_name = $this->options_name . '[' . $label_for . ']';
		$text = '';
		if(isset($args['text'])) $text = $args['text'];
		?>
		<div id="<?php echo $args['label_for']?>">
		<input name="<?php echo $option_name;?>" type="checkbox" value="1" <?php checked( 1, $enable, true );?> /> <?php echo $text;?>
		</div>
		<?php
	}
	
/**
 *
 * This is the function that adds our Default Layout textarea. 
 * @param array $args Optional. Override the defaults.
 */
	function layout_textarea($args=array()) 
	{
		if(!isset($args['label_for'])||$args['label_for']=='') return;
		$label_for = $args['label_for'];
		
		$default_value = '';
		if(isset($args['default_value'])) $default_value = $args['default_value'];
		$option_name = $this->options_name . '[' . $label_for . ']';
		
		$value = get_theme_mod($args['label_for'], $default_value);
		?>
		<div id="<?php echo $args['label_for']?>">
		<textarea name="<?php echo $option_name;?>" rows="10" cols="50" class="large-text code"><?php echo $value;?></textarea>
		</div>
		<?php
	}
	
/**
 *
 * This is the function that adds our Default Layout for file upload. 
 * @param array $args Optional. Override the defaults.
 */
	function layout_file($args=array()) 
	{
		if(!isset($args['label_for'])||$args['label_for']=='') return;
		$label_for = $args['label_for'];
		
		$default_value = '';
		if(isset($args['default_value'])) $default_value = $args['default_value'];
		$option_name = $this->options_name . '[' . $label_for . ']';
		
		$value = get_theme_mod($args['label_for'], $default_value);
		?>
		<div id="<?php echo $args['label_for']?>">
		<input type="text" name="<?php echo $option_name?>" value="<?php echo $value?>">
		<a title="Upload image" class="thickbox button-secondary" href="<?php echo admin_url('media-upload.php');?>?type=image&post_id=0&TB_iframe=1"><?php _e('Upload', 'views_base');?></a>
		</div>
		<?php
	}
	
/**
 *
 * This is the function that adds our Default Layout for select upload. 
 * @param array $args Optional. Override the defaults.
 */
	function layout_select($args=array()) 
	{
		if(!isset($args['label_for'])||$args['label_for']=='') return;
		$label_for = $args['label_for'];
		
		$default_value = '';
		if(isset($args['default_value'])) $default_value = $args['default_value'];
		
		$options = array();
		if(isset($args['options'])&&sizeof($args['options'])>0) $options = $args['options'];
		
		$option_name = $this->options_name . '[' . $label_for . ']';
		
		$value = get_theme_mod($args['label_for'], $default_value);
		?>
		<div id="<?php echo $args['label_for']?>">
		<select name="<?php echo $option_name?>">
		<?php 
		foreach($options as $k => $v)
		{
			?>
			<option value="<?php echo $k;?>" <?php selected( $k, $value, 1 ); ?>><?php echo $v;?></option>
			<?php
		}?>
		</select>
		</div>
		<?php
	}
		
/**
 *
 * This is the function that adds our Default Layout for font select menu. 
 * @param array $args Optional. Override the defaults.
 */
	function layout_font_select($args=array()) 
	{
		if(!isset($args['label_for'])||$args['label_for']=='') return;
		$label_for = $args['label_for'];
		
		$default_value = '';
		if(isset($args['default_value'])) $default_value = $args['default_value'];
		
		$options = array();
		if(isset($args['options'])&&sizeof($args['options'])>0) $options = $args['options'];
		
		$option_name = $this->options_name . '[' . $label_for . ']';
		
		$value = get_theme_mod($args['label_for'], $default_value);
		?>
		<div id="<?php echo $args['label_for']?>">
		<select name="<?php echo $option_name?>">
		<?php 
		foreach($options as $k => $v)
		{
			?>
			<option value="<?php echo esc_attr($k);?>" <?php selected( $k, $value, 1 ); ?>><?php echo $k;?></option>
			<?php
		}?>
		</select>
		</div>
		<?php
	}
	
/**
 *
 * This is the function that adds our Default Layout for radio upload. 
 * @param array $args Optional. Override the defaults.
 */
	function layout_radio($args=array()) 
	{
		if(!isset($args['label_for'])||$args['label_for']=='') return;
		$label_for = $args['label_for'];
		
		$default_value = '';
		if(isset($args['default_value'])) $default_value = $args['default_value'];
		
		$options = array();
		if(isset($args['options'])&&sizeof($args['options'])>0) $options = $args['options'];
		
		$option_name = $this->options_name . '[' . $label_for . ']';
		
		$value = get_theme_mod($args['label_for'], $default_value);
		?>
		<div id="<?php echo $args['label_for']?>">
		<?php 
		foreach($options as $k => $v)
		{
			?>
			<input name="<?php echo $option_name?>" type="radio" value="<?php echo $k;?>" <?php checked( $k, $value, 1 ); ?> /> <?php echo $v;?>
			<?php
		}?>
		</div>
		<?php
	}
	
/**
 *
 * This is the function that adds our Default Layout text. 
 * @param array $args Optional. Override the defaults.
 */
	function layout_text($args=array()) 
	{
		if(!isset($args['label_for'])||$args['label_for']=='') return;
		$label_for = $args['label_for'];
		
		$default_value = '';
		if(isset($args['default_value'])) $default_value = $args['default_value'];
		$option_name = $this->options_name . '[' . $label_for . ']';
		
		$value = get_theme_mod($args['label_for'], $default_value);
		
		$size = '';
		if(isset($args['size'])) $size = 'size="' . $args['size'] . '"';
		
		$prepend = '';
		if(isset($args['prepend'])) $prepend = $args['prepend'];
		?>
		<div id="<?php echo $args['label_for']?>">
		<?php echo $prepend;?><input type="text" name="<?php echo $option_name;?>" <?php echo $size;?> value="<?php echo $value;?>" />
		</div>
		<?php
	}
	
/**
* Load sidebar template.
*
* Includes the sidebar template for a theme or if a name is specified then a
* specialised sidebar will be included.
* Get sitebar action. 
* @param string $name The name of the specialised sidebar.
*/
	function get_sidebar($name = null) 
	{
		$templates = array();
		if ( isset($name) )
			$templates[] = "sidebar-{$name}.php";
		//default sidebar file
		$templates[] = 'sidebar.php';
		
		// locate sidebar template file
		$_template_file  = locate_template($templates);
		
		if('' != $_template_file )
		{
			global $posts, $post, $wp_did_header, $wp_did_template_redirect, $wp_query, $wp_rewrite, $wpdb, $wp_version, $wp, $id, $comment, $user_ID;
			if ( is_array( $wp_query->query_vars ) )
			{
				extract( $wp_query->query_vars, EXTR_SKIP );
			}
			require( $_template_file );
		}
		// replace default wordpress function get_sidebar
		return;
	}
	
/**
 * Styles the header image and text displayed on the blog
 *
 * get_header_textcolor() options: HEADER_TEXTCOLOR is default, hide text (returns 'blank'), or any hex value
 */
	function header_style() {
		// If no custom options for text are set, let's bail
		if ( HEADER_TEXTCOLOR == get_header_textcolor() )
			return;
		// If we get this far, we have custom styles.
		?>
		<style type="text/css">
		<?php
			// Has the text been hidden?
			if ( 'blank' == get_header_textcolor() ) :
		?>
			.site-title,
			.site-description {
				position: absolute !important;
				clip: rect(1px 1px 1px 1px); /* IE6, IE7 */
				clip: rect(1px, 1px, 1px, 1px);
			}
		<?php
			// If the user has set a custom color for the text, use that.
			else :
		?>
			.site-title a,
			.site-description {
				color: #<?php echo get_header_textcolor(); ?> !important;
			}
		<?php endif; ?>
		</style>
		<?php
	}

/**
 * Styles the header image displayed on the Appearance > Header admin panel.
 *
 * Referenced via add_custom_image_header().
 */
	function admin_header_style() 
	{
		?>
		<style type="text/css">
		.appearance_page_custom-header #headimg {
			border: none;
		}
		#headimg h1,
		#headimg h2 {
			line-height: 1.6;
			margin: 0;
			padding: 0;
		}
		#headimg h1 {
			font-size: 30px;
		}
		#headimg h1 a {
			text-decoration: none;
		}
		#headimg h2 {
			font: normal 14px/1.8 "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", sans-serif;
			margin-bottom: 24px;
		}
		#headimg img {
		}
		</style>
		<?php
	}

/**
 * Custom header image markup displayed on the Appearance > Header admin panel.
 *
 * Referenced via add_custom_image_header() .
 */
	function admin_header_image() 
	{ 
		?>
		<div id="headimg">
			<?php
			if ( 'blank' == get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ) || '' == get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ) )
				$style = ' style="display:none;"';
			else
				$style = ' style="color:#' . get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ) . ';"';
			?>
			<h1><a id="name"<?php echo $style; ?> onclick="return false;" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
			<h2 id="desc"<?php echo $style; ?>><?php bloginfo( 'description' ); ?></h2>
			<?php $header_image = get_header_image();
			if ( ! empty( $header_image ) ) : ?>
			<img src="<?php echo esc_url( $header_image ); ?>" alt="" />
			<?php endif; ?>
		</div>
		<?php 
	}
/**
 * Switch class of div "wptypes-middle", if no sidebarsor or just one
 */
	function middle_switch($default='', $middle_switch_arr=array()) {
		$res = $default;
		$sidebars = $this->middle_switch_arr;
		if(count($middle_switch_arr)>0)
		{
			$sidebars = $middle_switch_arr;
		}
		$num = 0;
		$class_array = array('','two_colomn','three_colomn');
		foreach($sidebars as $v)
		{
			if(is_active_sidebar( $v ))
			{
				$num ++;
			}
		}
		if(isset($class_array[$num]))
		{
			$res = $class_array[$num];
		}
		return $res;
	}

/**
 * Switch to mobile menu for devices
 */
	function mobilemenu_js()
	{
		?>
		<script>
			jQuery(document).ready(function(){
					jQuery('.mainmenu').mobileMenu();
			});
		</script>
	<?php 
	}
	
/**
 * print js and css in admin side
 */
	function admin_styles ()
	{
		//wp_enqueue_script( $handle,$src,$deps,$ver,$in_footer );
		wp_enqueue_script( 'views_base_admin', get_template_directory_uri() .'/javascripts/admin.js', array( 'jquery', 'thickbox', 'media-upload' ) ); 
		wp_enqueue_style('thickbox');
	}
	
/**
 * print custom header style
 */
	function custom_header_styles ()
	{
		?>
		<link rel="shortcut icon" href="<?php echo get_theme_mod('custom_favicon', $this->get_default_value('custom_favicon'));?>" type="image/x-icon" />
		<style type="text/css">
			<?php if(get_theme_mod('header_background')!=''){?>
			#header-container{
				background-image:url(<?php echo get_theme_mod('header_background', $this->get_default_value('header_background'));?>);
			}
			<?php }
			if(get_theme_mod('main_background')!=''){?>
			#main-container{
				background-image:url(<?php echo get_theme_mod('main_background', $this->get_default_value('main_background'));?>);
			}
			<?php }
			if(get_theme_mod('footer_background')!=''){?>
			#footer-container{
				background-image:url(<?php echo get_theme_mod('footer_background', $this->get_default_value('footer_background'));?>);
			}
			<?php }?>
			.site-header hgroup h1{
				<?php echo $this->get_current_value('site_title_font_size');?>
				
				font-family:<?php echo $this->get_current_value('site_title_font_family');?>;
				font-style:<?php echo get_theme_mod('site_title_style', $this->get_default_value('site_title_style'));?>;
				margin: <?php echo get_theme_mod('site_title_margin_top', $this->get_default_value('site_title_margin_top'));?>em  <?php echo get_theme_mod('site_title_margin_right', $this->get_default_value('site_title_margin_right'));?>em  <?php echo get_theme_mod('site_title_margin_bottom', $this->get_default_value('site_title_margin_bottom'));?>em  <?php echo get_theme_mod('site_title_margin_left', $this->get_default_value('site_title_margin_left'));?>em;
				padding: <?php echo get_theme_mod('site_title_padding_top', $this->get_default_value('site_title_padding_top'));?>em  <?php echo get_theme_mod('site_title_padding_right', $this->get_default_value('site_title_padding_right'));?>em  <?php echo get_theme_mod('site_title_padding_bottom', $this->get_default_value('site_title_padding_bottom'));?>em  <?php echo get_theme_mod('site_title_padding_left', $this->get_default_value('site_title_padding_left'));?>em;
			}
			.site-header hgroup h2{
				<?php echo $this->get_current_value('site_description_font_size');?>
				
				font-family:<?php echo $this->get_current_value('site_description_font_family');?>;
				font-style:<?php echo get_theme_mod('site_description_style', $this->get_default_value('site_description_style'))?>;
				margin: <?php echo get_theme_mod('site_description_margin_top', $this->get_default_value('site_description_margin_top'));?>em  <?php echo get_theme_mod('site_description_margin_right', $this->get_default_value('site_description_margin_right'));?>em  <?php echo get_theme_mod('site_description_margin_bottom', $this->get_default_value('site_description_margin_bottom'));?>em  <?php echo get_theme_mod('site_description_margin_left', $this->get_default_value('site_description_margin_left'));?>em;
				padding: <?php echo get_theme_mod('site_description_padding_top', $this->get_default_value('site_description_padding_top'));?>em  <?php echo get_theme_mod('site_description_padding_right', $this->get_default_value('site_description_padding_right'));?>em  <?php echo get_theme_mod('site_description_padding_bottom', $this->get_default_value('site_description_padding_bottom'));?>em  <?php echo get_theme_mod('site_description_padding_left', $this->get_default_value('site_description_padding_left'));?>em;
			}
			.site-header hgroup h3{
				<?php echo $this->get_current_value('site_description_font_size');?>
				
				font-family:<?php echo $this->get_current_value('site_description_font_family');?>;
				font-style:<?php echo get_theme_mod('site_description_style', $this->get_default_value('site_description_style'))?>;
				margin: <?php echo get_theme_mod('site_description_margin_top', $this->get_default_value('site_description_margin_top'));?>em  <?php echo get_theme_mod('site_description_margin_right', $this->get_default_value('site_description_margin_right'));?>em  <?php echo get_theme_mod('site_description_margin_bottom', $this->get_default_value('site_description_margin_bottom'));?>em  <?php echo get_theme_mod('site_description_margin_left', $this->get_default_value('site_description_margin_left'));?>em;
				padding: <?php echo get_theme_mod('site_description_padding_top', $this->get_default_value('site_description_padding_top'));?>em  <?php echo get_theme_mod('site_description_padding_right', $this->get_default_value('site_description_padding_right'));?>em  <?php echo get_theme_mod('site_description_padding_bottom', $this->get_default_value('site_description_padding_bottom'));?>em  <?php echo get_theme_mod('site_description_padding_left', $this->get_default_value('site_description_padding_left'));?>em;
			}
		</style>
		<?php
	}
	
/**
 * Get theme option default_value
 * @param string $name The name of the specialised theme option.
 */
	function get_default_value($name='')
	{
		$res = false;
		foreach($this->options_arr as $ks => $vs)
		{
			if(isset($vs['fields'])&&sizeof($vs['fields'])>0)
			{
				foreach($vs['fields'] as $kf => $vf)
				{
					if($kf == $name)
					{
						if(isset($vf['args']['default_value']))
						{
							$res = $vf['args']['default_value'];
							return $res;
						}
					}
				}
			}
		}
		return $res;
	}
	
/**
 * Get theme option current value
 * @param string $name The name of the specialised theme option.
 */
	function get_current_value($name='')
	{
		$res = false;
		$key = $this->get_default_value($name);
		if(get_theme_mod($name, $key))
		{
			$key = get_theme_mod($name, $key);
		}
		foreach($this->options_arr as $ks => $vs)
		{
			if(isset($vs['fields'])&&sizeof($vs['fields'])>0)
			{
				foreach($vs['fields'] as $kf => $vf)
				{
					if($kf == $name)
					{
						if(isset($vf['args']['options'])&&isset($vf['args']['options'][$key]))
						{
							$res = $vf['args']['options'][$key];
							return $res;
						}
					}
				}
			}
		}
		return $res;
	}
}
?>
